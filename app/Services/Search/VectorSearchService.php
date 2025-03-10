<?php

namespace App\Services\Search;

use App\Models\DocumentChunk;
use App\Models\Documents;
use App\Services\Embeddings\EmbeddingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class VectorSearchService
{
    private const SIMILARITY_THRESHOLD = 0.4;

    public function __construct(
        private EmbeddingService $embeddingService
    ) {}

    public function findRelevantDocuments(string $query, int $topK = 3): Collection
    {
        try {
            // Validasi dan preprocessing query
            $processedQuery = $this->preprocessQuery($query);

            // Buat embedding untuk query
            $queryEmbedding = $this->embeddingService->createEmbedding($processedQuery);

            if (empty($queryEmbedding)) {
                throw new \Exception("Gagal membuat embedding untuk query");
            }

            // Konversi embedding ke format JSON
            $embeddingJson = json_encode($queryEmbedding);

            // Cari dokumen yang relevan dengan cosine similarity
            $relevantChunks = DB::table('document_chunks')
                ->select([
                    'document_chunks.id',
                    'document_chunks.document_id',
                    'document_chunks.content',
                    'document_chunks.metadata',
                    'document_chunks.chunk_order',
                    'documents.title',
                    DB::raw('1 - (embedding <=> :embedding1) as similarity')
                ])
                ->join('documents', 'document_chunks.document_id', '=', 'documents.id')
                ->whereRaw('1 - (embedding <=> :embedding2) >= :threshold', [
                    'embedding2' => $embeddingJson,
                    'threshold' => self::SIMILARITY_THRESHOLD
                ])
                ->orderByRaw('embedding <=> :embedding3', ['embedding3' => $embeddingJson])
                ->limit($topK)
                ->addBinding($embeddingJson, 'select')  // Menambahkan binding untuk select statement
                ->get();
            return $this->postProcessResults($relevantChunks);
        } catch (\Exception $e) {
            Log::error('Document Retrieval Error', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Gagal mencari dokumen relevan: " . $e->getMessage());
        }
    }

    private function preprocessQuery(string $query): string
    {
        if (empty(trim($query))) {
            throw new \Exception("Query tidak boleh kosong");
        }
        return preg_replace('/\s+/', ' ', trim($query));
    }

    private function postProcessResults(Collection $chunks): Collection
    {
        return $chunks->map(function ($chunk) {
            $metadata = json_decode($chunk->metadata, true) ?? [];

            return [
                'id' => $chunk->id,
                'document_id' => $chunk->document_id,
                'title' => $chunk->title,
                'content' => $chunk->content,
                'similarity_score' => round($chunk->similarity * 100, 2),
                'chunk_order' => $chunk->chunk_order,
                'metadata' => $metadata,
            ];
        });
    }

    public function findRelevantDocumentsWithContext(string $query, int $topK = 3): Collection
    {
        $baseResults = $this->findRelevantDocuments($query, $topK);
        return $this->addContextToResults($baseResults);
    }

    private function addContextToResults(Collection $results): Collection
    {
        return $results->map(function ($result) {
            // Ambil chunk sebelum dan sesudah
            $surroundingChunks = DB::table('document_chunks')
                ->where('document_id', $result['document_id'])
                ->whereBetween('chunk_order', [
                    $result['chunk_order'] - 1,
                    $result['chunk_order'] + 1
                ])
                ->orderBy('chunk_order')
                ->get();

            $result['context'] = [
                'previous' => $surroundingChunks->where('chunk_order', '<', $result['chunk_order'])->first(),
                'next' => $surroundingChunks->where('chunk_order', '>', $result['chunk_order'])->first(),
            ];

            return $result;
        });
    }
}
