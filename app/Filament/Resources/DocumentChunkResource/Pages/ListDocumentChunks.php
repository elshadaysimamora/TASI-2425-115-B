<?php

namespace App\Filament\Resources\DocumentChunkResource\Pages;

use App\Filament\Resources\DocumentChunkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentChunks extends ListRecords
{
    protected static string $resource = DocumentChunkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
