<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Orhanerday\OpenAi\OpenAi;

class OpenAIController extends Controller
{
    private int $maxSectionLen, $maxTokens, $maxQueryLength, $chatHistoryLimit;
    private string $chatEmbeddingModel, $chatCompletionModel, $embeddingCsvFile, $datasetCsvFile, $query;

    public function __construct(private readonly OpenAi $openAi)
    {
        $this->maxSectionLen = (int)env('CHAT_MAX_SECTION_LEN');
        $this->maxTokens = (int)env('CHAT_MAX_TOKENS');
        $this->maxQueryLength = (int)env('CHAT_MAX_QUERY_LENGTH');
        $this->chatHistoryLimit = (int)env('CHAT_HISTORY_LIMIT');
        $this->chatEmbeddingModel = env('CHAT_EMBEDDING_MODEL');
        $this->chatCompletionModel = env('CHAT_COMPLETION_MODEL');
        $this->embeddingCsvFile = env('CHAT_EMBEDDING_CSV_FILE');
        $this->datasetCsvFile = env('CHAT_DATASET_CSV_FILE');
    }

    public function chatAnswer(Request $request): void
    {
        $query = $request->input('prompt', 'no prompt was provided');
        if ($query === '') {
            return;
        } else {
            $this->setQuery($query);
        }

        try {
            $embedding = $this->getQueryEmbedding();
            $datasetEmbeddings = $this->getEmbeddingsCsv();
            $dataset = $this->getDatasetCsv();

            $similarities = $this->orderDocumentSectionsByQuerySimilarity($embedding, $datasetEmbeddings);
            $systemPrompt = $this->constructSystemPrompt($dataset, $similarities);
            $messages = $this->constructMessages($systemPrompt);

            $this->outputStream($messages);
//            $result = $this->outputMessage($messages);
        } catch (Exception $e) {
            echo sprintf('Error: %s', json_encode(
                ['error' => $e->getMessage()]
            ));
        }
    }

    private function vectorSimilarity(array $x, array $y): float
    {
        if (count($x) !== count($y)) {
            throw new Exception("Arrays have different sizes");
        }
        $result = 0;
        for ($i = 0; $i < count($x); $i++) {
            $result += $x[$i] * $y[$i];
        }

        return $result;
    }

    private function orderDocumentSectionsByQuerySimilarity(array $embedding, array $docs): array
    {
        $similarities = array_map(fn($doc) => $this->vectorSimilarity($embedding, $doc), $docs);
        arsort($similarities);
        return $similarities;
    }

    private function constructSystemPrompt(array $docs, array $similarities): string
    {
        $system = "You are an AI assistant of Tymur Mardas. Tymur is a web developer. You help people to know Tymur better, you act friendly and helpful.

        If people ask a question about you - answer like it was asked was about Tymur:
        - Do you like cats?
        - Yes, Tymur likes cats.
        Skip prose like 'As an AI assistant...'.

        Answer as precise as possible using context provided further.";

        $context = '
        Context:
        ';

        $chosen_sections_context = [];
        $chosen_sections_system = [];
        $chosen_sections_len = 0;

        foreach ($similarities as $i => $sim) {

            $context_text = $docs[$i][2];
            $system_text = $docs[$i][3];
            $chosen_sections_len += strlen($context_text);

            if ($chosen_sections_len > $this->maxSectionLen) {
                break;
            }

            $chosen_sections_context[] = "\n* " . $context_text;
            $chosen_sections_system[] = " " . $system_text;
        }

        $prompt = $system . implode('', $chosen_sections_system);
        $prompt .= $context . implode('', $chosen_sections_context);

        return $prompt;
    }

    private function getDatasetCsvPath(): string
    {
        return base_path() . $this->datasetCsvFile;
    }

    private function getEmbeddingsCsvPath(): string
    {
        return base_path() . $this->embeddingCsvFile;
    }

    private function getDatasetCsv(): array
    {
        if (!file_exists($this->getDatasetCsvPath())) {
            throw new Exception('Dataset file is missing');
        }
        $dataset_csv = [];
        $dataset_file = fopen($this->getDatasetCsvPath(), 'r');
        fgetcsv($dataset_file);
        while (($result = fgetcsv($dataset_file)) !== false)
            $dataset_csv[] = $result;

        return $dataset_csv;
    }

    private function getEmbeddingsCsv(): array
    {
        if (!file_exists($this->getEmbeddingsCsvPath())) {
            throw new \Exception('Embeddings file is missing');
        }
        $docs_embed = array_map(
            fn($line) => $csv[] = json_decode(str_getcsv($line)[2]),
            file($this->getEmbeddingsCsvPath()));
        array_shift($docs_embed);

        return $docs_embed;
    }

    private function constructMessages($systemPrompt): array
    {
        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt
        ];

        $i = 0;
        $chatHistory = Session::get('chat');
        if (is_array($chatHistory)) {
            foreach ($chatHistory as $message) {
                if ($i >= $this->chatHistoryLimit) {
                    break;
                }

                $messages[] = [
                    'role' => 'user',
                    'content' => $message[0],
                ];
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $message[1],
                ];
                $i++;
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $this->query,
        ];

        return $messages;
    }

    private function getQueryEmbedding(): array
    {
        $embed = $this->openAi->embeddings([
            'input' => $this->query,
            'model' => $this->chatEmbeddingModel
        ]);

        $resEmbed = json_decode($embed);

        if (!isset($resEmbed->data[0]->embedding)) {
            throw new Exception('No embedding provided');
        }
        $embedding = $resEmbed->data[0]->embedding;

        return $embedding;
    }

    private function outputStream($messages)
    {
        header('Content-type: text/event-stream');
        header('Cache-Control: no-cache');

        $temp_response = '';

        $this->openAi->setTimeout(10);
        $this->openAi->chatCompletion([
            'model' => $this->chatCompletionModel,
            'messages' => $messages,
            'temperature' => 0,
            'max_tokens' => $this->maxTokens,
            'stream' => true,
        ], function ($curl_info, $data) use (&$temp_response) {
            $temp_data = Str::replace('data: ', '', $data);
            $temp_data = json_decode($temp_data);
            if (isset($temp_data->choices[0]->delta->content)) {
                $temp_response .= $temp_data->choices[0]->delta->content;
            }
            echo $data . "<br><br>";
            echo PHP_EOL;
            ob_flush();
            flush();
            return strlen($data);
        });

        $chatHistory = Session::get('chat');
        if (count($chatHistory) >= $this->chatHistoryLimit)
            array_shift($chatHistory);

        $chatHistory[] = [$this->query, $temp_response];

        Session::put('chat', $chatHistory);
    }

    private function outputMessage($messages)
    {
        $complete = $this->openAi->chatCompletion([
            'model' => $this->chatCompletionModel,
            'messages' => $messages,
            'temperature' => 0,
            'max_tokens' => $this->maxTokens,
        ]);

        $res = json_decode($complete);
        return $res->choices[0]->message->content;
    }

    public function setQuery($query): void
    {
        $query = new HtmlString($query);
        $query = Str::limit($query, $this->maxQueryLength);

        $this->query = $query;
    }
}
