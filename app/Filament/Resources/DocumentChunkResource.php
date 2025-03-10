<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentChunkResource\Pages;
use App\Filament\Resources\DocumentChunkResource\RelationManagers;
use App\Models\DocumentChunk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;

class DocumentChunkResource extends Resource
{
    protected static ?string $model = DocumentChunk::class;
    protected static ?string $navigationGroup = 'Documents';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_id')
                    ->label('Document ID')
                    ->required(),
                RichEditor::make('content')
                    ->label('Content')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_id')
                    ->searchable()
                    ->label('Document ID'),
                TextColumn::make('content')
                    ->searchable()
                    ->label('Content'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocumentChunks::route('/'),
            'create' => Pages\CreateDocumentChunk::route('/create'),
            'edit' => Pages\EditDocumentChunk::route('/{record}/edit'),
        ];
    }
}
