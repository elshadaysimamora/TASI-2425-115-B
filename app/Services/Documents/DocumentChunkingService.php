<?php

namespace App\Services\Documents;

use App\Models\DocumentChunk;
use App\Models\Document;
use Illuminate\Support\Facades\Log;
use App\Services\Documents\TextExactorService;
use App\Services\Embeddings\EmbeddingService;

class DocumentChunkingService
{
    private const MIN_CHUNK_SIZE = 100;
    private const MAX_CHUNK_SIZE = 3072;

    // Inject TextExactorService
    public function __construct(
        private TextExactorService $textExtractorService,
        private EmbeddingService $embeddingService
    ) {}

    public function processDocument($filePath)
    {
        try {
            // Buat entri dokumen
            $document = Document::create([
                'title' => pathinfo($filePath, PATHINFO_FILENAME),
                'file' => $filePath,
            ]);
            $text = $this->textExtractorService->extractText($filePath);


            // Pecah dokumen menjadi chunks
            $chunks = $this->splitDocument($text);

            // Proses setiap chunk
            foreach ($chunks as $index => $chunkText) {
                // Buat embedding
                $embedding = $this->embeddingService->createEmbedding($chunkText);

                // Simpan chunk dengan embedding
                DocumentChunk::create([
                    'document_id' => $document->id,
                    'content' => $chunkText,
                    'embedding' => json_encode($embedding),
                    'chunk_order' => $index,
                    'metadata' => json_encode([
                        'source' => $filePath,
                        'chunk_index' => $index,
                    ]),
                ]);
            }

            return $document;
        } catch (\Exception $e) {
            // Log error
            Log::error('Document Processing Error: ' . $e->getMessage());
            throw $e;
        }
    }

    //splitDocument to chunk
    public  function splitDocument($text)
    {
        //bersihkan dan standarisasi teks
        $text = $this->preprocessText($text);

        //pecah teks menjadi paragraf
        $paragraphs = $this->splitIntoParagraphs($text);

        //buat chunks berdasarkan paragraf dengan semantik splitting
        return $this->createSemanticChunks($paragraphs);
    }

    //preprocessText
    public function preprocessText($text)
    {
        //standarisasi line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        //Bersihkan white space
        $text = preg_replace('/\s+/', ' ', $text);
        //Standarisasi tanda baca
        $text = preg_replace('/\s+([.,!?])/', '$1', $text);
        //hapus karakter non ascii
        $text = preg_replace('/[^\x20-\x7E]/', '', $text);
        //hapus titik titik yang berlebihan
        $text = preg_replace('/\.{3,}/', '.', $text);

        return trim($text);
    }

    private function splitIntoParagraphs($text)
    {
        // Pecah berdasarkan baris kosong atau multiple newlines
        $paragraphs = preg_split('/\n\s*\n/', $text);
        return array_filter(array_map('trim', $paragraphs));
    }


    //createSemanticChunks
    private function createSemanticChunks($paragraphs)
    {
        $chunks = [];
        $currentChunk = '';
        $currentSize = 0;

        foreach ($paragraphs as $paragraph) {
            $paragraphLength = strlen($paragraph);

            // Jika paragraf sendiri terlalu panjang, pecah berdasarkan kalimat
            if ($paragraphLength > self::MAX_CHUNK_SIZE) {
                if ($currentChunk !== '') {
                    $chunks[] = trim($currentChunk);
                    $currentChunk = '';
                    $currentSize = 0;
                }

                $sentenceChunks = $this->splitIntoSentences($paragraph);
                foreach ($sentenceChunks as $sentenceChunk) {
                    $chunks[] = trim($sentenceChunk);
                }
                continue;
            }

            // Cek apakah menambahkan paragraf ini akan melebihi ukuran maksimal
            if ($currentSize + $paragraphLength > self::MAX_CHUNK_SIZE) {
                if ($currentChunk !== '') {
                    $chunks[] = trim($currentChunk);
                }
                $currentChunk = $paragraph;
                $currentSize = $paragraphLength;
            } else {
                $currentChunk .= ($currentChunk !== '' ? "\n\n" : '') . $paragraph;
                $currentSize += $paragraphLength;
            }
        }

        // Tambahkan chunk terakhir jika ada
        if ($currentChunk !== '') {
            $chunks[] = trim($currentChunk);
        }

        return $this->ensureMinimumSize($chunks);
    }

    //splitIntoSentences
    private function splitIntoSentences($text)
    {
        $chunks = [];
        $currentChunk = '';
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);

        foreach ($sentences as $sentence) {
            if (strlen($currentChunk) + strlen($sentence) > self::MAX_CHUNK_SIZE) {
                if ($currentChunk !== '') {
                    $chunks[] = $currentChunk;
                    $currentChunk = '';
                }
                // Jika satu kalimat terlalu panjang, pecah berdasarkan frasa
                if (strlen($sentence) > self::MAX_CHUNK_SIZE) {
                    $chunks = array_merge($chunks, $this->splitIntoPhases($sentence));
                    continue;
                }
            }
            $currentChunk .= ($currentChunk !== '' ? ' ' : '') . $sentence;
        }

        if ($currentChunk !== '') {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    private function splitIntoPhases($sentence)
    {
        $chunks = [];
        $phrases = preg_split('/[,;:]/', $sentence);
        $currentChunk = '';

        foreach ($phrases as $phrase) {
            if (strlen($currentChunk) + strlen($phrase) > self::MAX_CHUNK_SIZE) {
                if ($currentChunk !== '') {
                    $chunks[] = trim($currentChunk);
                    $currentChunk = '';
                }
            }
            $currentChunk .= ($currentChunk !== '' ? ', ' : '') . trim($phrase);
        }

        if ($currentChunk !== '') {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    private function ensureMinimumSize($chunks)
    {
        $result = [];
        $currentChunk = '';

        foreach ($chunks as $chunk) {
            if (strlen($chunk) < self::MIN_CHUNK_SIZE) {
                $currentChunk .= ($currentChunk !== '' ? "\n\n" : '') . $chunk;
                if (strlen($currentChunk) >= self::MIN_CHUNK_SIZE) {
                    $result[] = $currentChunk;
                    $currentChunk = '';
                }
            } else {
                if ($currentChunk !== '') {
                    $result[] = $currentChunk;
                    $currentChunk = '';
                }
                $result[] = $chunk;
            }
        }

        if ($currentChunk !== '') {
            $result[] = $currentChunk;
        }

        return $result;
    }



    //validate document
    public function validateDocument($file)
    {
        $allowedExtensions = ['pdf', 'txt'];
        // $maxFileSize = 10 * 1024 * 1024; // 10 MB
        $maxFileSize = 20 * 1024 * 1024; // 20 MB

        // Cek ekstensi
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception("Tipe file tidak didukung. Gunakan PDF atau TXT.");
        }

        // Cek ukuran file
        if ($file->getSize() > $maxFileSize) {
            throw new \Exception("Ukuran file terlalu besar. Maksimal 10 MB.");
        }
        return true;
    }
}
