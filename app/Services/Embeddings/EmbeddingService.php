<?php

namespace App\Services\Embeddings;

use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;


class EmbeddingService
{
    private const MODEL = 'text-embedding-3-small';

    private function l2Normalize(array $vector): array
    {
        $squareSum = array_sum(array_map(fn($x) => $x * $x, $vector));
        $norm = sqrt($squareSum);
        return array_map(fn($x) => $x / $norm, $vector);
    }


    public function createEmbedding($text)
    {
        try {
            $response = OpenAI::embeddings()->create([
                'model' => self::MODEL,
                'input' => $text,
            ]);
            $embedding = $response['data'][0]['embedding'];
            // Normalisasi embedding sebelum disimpan
            return $this->l2Normalize($embedding);
        } catch (\Exception $e) {
            Log::error('Embedding Creation Error', [
                'message' => $e->getMessage(),
                'text' => substr($text, 0, 100) . '...'
            ]);
            throw new \Exception("Failed to create embedding: {$e->getMessage()}");
        }
    }
}
