<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Models\File;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('user_id'),
                TextEntry::make('file_path'),
                TextEntry::make('file_name'),
                TextEntry::make('original_name'),
                TextEntry::make('mime_type'),
                TextEntry::make('size')
                    ->numeric(),
                TextEntry::make('disk'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (File $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
