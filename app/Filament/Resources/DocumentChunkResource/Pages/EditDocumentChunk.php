<?php

namespace App\Filament\Resources\DocumentChunkResource\Pages;

use App\Filament\Resources\DocumentChunkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentChunk extends EditRecord
{
    protected static string $resource = DocumentChunkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
