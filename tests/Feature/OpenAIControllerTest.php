<?php

namespace Tests\Feature;

use App\Http\Controllers\OpenAIController;
use Orhanerday\OpenAi\OpenAi;
use ReflectionClass;
use Tests\TestCase;


class OpenAIControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_csv_files_open_and_are_not_empty()
    {


        $controller = new OpenAIController($this->mock(OpenAi::class));
        $reflection = new ReflectionClass(OpenAIController::class);

        $getEmbeddingsCsv = $reflection->getMethod('getEmbeddingsCsv');
        $getDatasetCsv = $reflection->getMethod('getDatasetCsv');
        $getEmbeddingsCsv->setAccessible(true);
        $getDatasetCsv->setAccessible(true);

        $resultEmbeddings = $getEmbeddingsCsv->invoke($controller);

        $this->assertIsArray($resultEmbeddings);
        $this->assertNotEmpty($resultEmbeddings);

        $resultDataset = $getDatasetCsv->invoke($controller);

        $this->assertIsArray($resultDataset);
        $this->assertNotEmpty($resultDataset);
    }
}
