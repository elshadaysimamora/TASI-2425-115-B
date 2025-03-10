<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\Documents\DocumentChunkingService;
use Illuminate\Support\Facades\Storage;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
    
    protected function afterCreate(): void
    {
        $filePath = $this->record->file;
        $fullPath = Storage::disk('public')->path($filePath);
        $chunkingService = app(DocumentChunkingService::class);
        $chunkingService->processDocument($fullPath);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
