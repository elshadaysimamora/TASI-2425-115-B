<?php

namespace App\Services\Documents;

use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\RichText;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;


class TextExactorService
{
    private Parser $pdfParser;

    public function __construct()
    {
        $this->pdfParser = new Parser();
    }
    public function extractText(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'pdf':
                return $this->extractPdfText($filePath);
            case 'txt':
                return $this->extractTxt($filePath);
            case 'docx':
                return $this->extractDocxText($filePath);
            case 'pptx':
                return $this->extractPptxText($filePath);
            default:
                throw new \Exception("File type {$extension} is not supported");
        }
    }

    // extract from TXT
    private function extractTxt(string $filePath): string
    {
        $text = "";
        $text = file_get_contents($filePath);
        return $text;
    }

    //extractPdfText
    private function extractPdfText(string $filePath): string
    {
        try {
            set_time_limit(0);
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error('PDF Text Extraction Error', [
                'message' => $e->getMessage(),
                'file_path' => $filePath,
            ]);
            throw $e;
        }
    }


    // extractDocxText
    private function extractDocxText(string $filePath): string
    {
        $text = '';
        $phpWord = WordIOFactory::load($filePath);

        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();

            foreach ($elements as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    $text .= $element->getText() . " ";
                }
            }
        }

        return trim($text);
    }

    // extractPptxText
    private function extractPptxText(string $filePath): string
    {
        //cek apakah ada error di phpPresentation, jika iya, ubah file nya menjadi pdf
        try {
            $phpPresentation = IOFactory::load($filePath);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengekstrak teks. Coba konversi file ke PDF dan ulangi prosesnya.', [
                'message' => $e->getMessage(),
                'file_path' => $filePath,
            ]);
        }
        $text = '';

        // Loop through each slide
        foreach ($phpPresentation->getAllSlides() as $slide) {
            // Loop through each shape in the slide
            foreach ($slide->getShapeCollection() as $shape) {
                if ($shape instanceof RichText) {
                    // Loop through each paragraph in the shape
                    foreach ($shape->getParagraphs() as $paragraph) {
                        // Loop through each element in the paragraph
                        foreach ($paragraph->getRichTextElements() as $element) {
                            if ($element instanceof RichText\Run) {
                                $text .= $element->getText() . "\n";
                            }
                        }
                    }
                }
            }
        }
        return $text;
    }
}
