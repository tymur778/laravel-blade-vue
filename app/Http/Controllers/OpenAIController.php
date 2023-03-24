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
    const MAX_SECTION_LEN = 1500;
    const MAX_TOKENS = 150;
    const MAX_QUERY_LENGTH = 500;
    const CHAT_HISTORY_LIMIT = 3;
    const CHAT_EMBEDDING_MODEL = 'text-embedding-ada-002';
    const CHAT_COMPLETION_MODEL = 'gpt-3.5-turbo';
    const EMBEDDING_CSV_FILE = '/csv/tim_embed.csv';
    const DATASET_CSV_FILE = '/csv/tim.csv';

    private string $query;

    public function __construct(private OpenAi $openAi)
    {
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
            $docsEmbed = $this->getEmbeddingsCsv();
            $docsDataset = $this->getDatasetCsv();

            $similarities = $this->orderDocumentSectionsByQuerySimilarity($embedding, $docsEmbed);
            $systemPrompt = $this->constructSystemPrompt($docsDataset, $similarities);
            $messages = $this->constructMessages($systemPrompt);

            $this->outputStream($messages);
//            $this->outputMessage($messages);
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

            if ($chosen_sections_len > OpenAIController::MAX_SECTION_LEN)
                break;

            $chosen_sections_context[] = "\n* " . $context_text;
            $chosen_sections_system[] = " " . $system_text;
        }

        $prompt = $system . implode('', $chosen_sections_system);
        $prompt .= $context . implode('', $chosen_sections_context);

        return $prompt;
    }

    private function getEmbeddingsCsv(): array
    {
        if (!file_exists(resource_path() . OpenAIController::EMBEDDING_CSV_FILE)) {
            throw new \Exception('Embeddings file is missing');
        }
        $docs_embed = array_map(fn($line) => $csv[] = json_decode(str_getcsv($line)[2]), file(resource_path() . OpenAIController::EMBEDDING_CSV_FILE));
        array_shift($docs_embed);

        return $docs_embed;
    }

    private function getDatasetCsv(): array
    {
        if (!file_exists(resource_path() . OpenAIController::DATASET_CSV_FILE)) {
            throw new Exception('Dataset file is missing');
        }
        $dataset_csv = [];
        $dataset_file = fopen(resource_path() . OpenAIController::DATASET_CSV_FILE, 'r');
        fgetcsv($dataset_file);
        while (($result = fgetcsv($dataset_file)) !== false)
            $dataset_csv[] = $result;

        return $dataset_csv;
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
                if ($i >= OpenAIController::CHAT_HISTORY_LIMIT) {
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

    private function getQueryEmbedding()
    {
        $embed = $this->openAi->embeddings([
            'input' => $this->query,
            'model' => OpenAIController::CHAT_EMBEDDING_MODEL
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
            'model' => OpenAIController::CHAT_COMPLETION_MODEL,
            'messages' => $messages,
            'temperature' => 0,
            'max_tokens' => OpenAIController::MAX_TOKENS,
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
        if (count($chatHistory) >= OpenAIController::CHAT_HISTORY_LIMIT)
            array_shift($chatHistory);

        $chatHistory[] = [$this->query, $temp_response];

        Session::put('chat', $chatHistory);
    }

    private function outputMessage($messages)
    {
        $complete = $this->openAi->chatCompletion([
            'model' => OpenAIController::CHAT_COMPLETION_MODEL,
            'messages' => $messages,
            'temperature' => 0,
            'max_tokens' => OpenAIController::MAX_TOKENS,
        ]);

        $res = json_decode($complete);
        return $res->choices[0]->text;
    }

    private function setQuery($query): void
    {
        $query = new HtmlString($query);
        $query = Str::limit($query, OpenAIController::MAX_QUERY_LENGTH);

        $this->query = $query;
    }
}
