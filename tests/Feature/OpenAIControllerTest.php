<?php

namespace Tests\Feature;

use App\Http\Controllers\OpenAIController;
use Illuminate\Support\Facades\Session;
use Orhanerday\OpenAi\OpenAi;
use ReflectionClass;
use Tests\TestCase;

class OpenAIControllerTest extends TestCase
{
    public function test_csv_files_open_and_are_not_empty(): void
    {
        $OpenAIController = new OpenAIController($this->mock(OpenAi::class));

        $resultEmbeddings = $this->callPrivate($OpenAIController, 'getEmbeddingsCsv');

        $this->assertIsArray($resultEmbeddings);
        $this->assertNotEmpty($resultEmbeddings);

        $resultDataset = $this->callPrivate($OpenAIController, 'getDatasetCsv');

        $this->assertIsArray($resultDataset);
        $this->assertNotEmpty($resultDataset);

        $this->assertSameSize($resultDataset, $resultEmbeddings);
    }

    public function test_vector_dot_product_is_calculated(): void
    {
        $OpenAIController = new OpenAIController($this->mock(OpenAi::class));

        $vNormalized = [0.13483997, 0.26967994, 0.40451992, 0.53935989, 0.67419986];
        $res1 = $this->callPrivate($OpenAIController, 'vectorSimilarity', [$vNormalized, $vNormalized]);
        $this->assertIsFloat($res1);
        $this->assertNotEmpty($res1);
        $this->assertEquals(0.9999999953896426, $res1);

        $this->expectException(\Exception::class);
        $this->callPrivate($OpenAIController, 'vectorSimilarity', [[1, 1], [1, 1, 1]]);
    }

    public function test_similarities_are_selected(): void
    {
        $OpenAi = $this->getMockBuilder(OpenAi::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockQueryEmbedding = file_get_contents(base_path('tests') . '/Mocks/OpenAiEmbedResponse.json');
        $OpenAi->expects($this->any())
            ->method('embeddings')
            ->willReturn($mockQueryEmbedding);

        $OpenAIController = new OpenAIController($OpenAi);
        $OpenAIController->setQuery('test query embedding');

        $embedding = $this->callPrivate($OpenAIController, 'getQueryEmbedding');

        $this->assertNotNull($embedding);
        $this->assertIsArray($embedding);

        $embeddings = $this->callPrivate($OpenAIController, 'getEmbeddingsCsv');

        $this->assertTrue(count($embeddings) > 1);

        $sortedEmbeddingsKeys = $this->callPrivate(
            $OpenAIController,
            'orderDocumentSectionsByQuerySimilarity',
            [$embedding, $embeddings]
        );

        $this->assertSameSize($sortedEmbeddingsKeys, $embeddings);
        $this->assertTrue(reset($sortedEmbeddingsKeys) > end($sortedEmbeddingsKeys));
    }

    public function test_prompt_is_correctly_constructed()
    {
        $mockSimilarities = [1 => 0.9, 3 => 0.8, 5 => 0.7];

        $OpenAIController = new OpenAIController($this->mock(OpenAi::class));
        $OpenAIReflection = new ReflectionClass(OpenAIController::class);
        $maxSectionLen = $OpenAIReflection->getProperty('maxSectionLen');
        $maxSectionLen->setAccessible(true);
        $maxSectionLen->setValue($OpenAIController, 5000);

        $dataset = $this->callPrivate($OpenAIController, 'getDatasetCsv');

        $systemPrompt = $this->callPrivate($OpenAIController, 'constructSystemPrompt', [$dataset, $mockSimilarities]);

        $this->assertStringContainsString($dataset[1][2], $systemPrompt);
        $this->assertStringContainsString($dataset[3][2], $systemPrompt);
        $this->assertStringContainsString($dataset[5][2], $systemPrompt);

        $maxSectionLen->setValue($OpenAIController, 0);
        $systemPrompt = $this->callPrivate($OpenAIController, 'constructSystemPrompt', [$dataset, $mockSimilarities]);

        $this->assertStringNotContainsString($dataset[1][2], $systemPrompt);
        $this->assertStringNotContainsString($dataset[3][2], $systemPrompt);
        $this->assertStringNotContainsString($dataset[5][2], $systemPrompt);
    }

    public function test_messages_array_is_constructed()
    {
        $OpenAIController = new OpenAIController($this->mock(OpenAi::class));

        $queryTest = 'test query';
        $OpenAIController->setQuery($queryTest);

        $messagesTest = [
            ['question 1', 'answer 1'],
            ['question 2', 'answer 2']
        ];
        Session::put('chat', $messagesTest);
        $systemPrompt = 'test prompt';

        $messages = $this->callPrivate($OpenAIController, 'constructMessages', [$systemPrompt]);

        $expectedMessagesLength = count($messagesTest) * 2 + 2;
        $this->assertCount($expectedMessagesLength, $messages);
        $this->assertEquals('system', $messages[0]['role']);
        $this->assertEquals($systemPrompt, $messages[0]['content']);

        $this->assertEquals('assistant', $messages[2]['role']);
        $this->assertEquals($messagesTest[1][0], $messages[3]['content']);

        $this->assertEquals('user', $messages[$expectedMessagesLength - 1]['role']);
        $this->assertEquals($queryTest, $messages[$expectedMessagesLength - 1]['content']);
    }
}
