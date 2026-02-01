<?php

namespace App\Filament\Resources\Rombels\Schemas;

use App\Models\Rombel;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RombelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('sekolah_id')
                    ->placeholder('-'),
                TextEntry::make('nama'),
                TextEntry::make('tingkat_pendidikan_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tingkat_pendidikan_id_str')
                    ->placeholder('-'),
                TextEntry::make('semester_id')
                    ->placeholder('-'),
                TextEntry::make('jenis_rombel')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('jenis_rombel_str')
                    ->placeholder('-'),
                TextEntry::make('kurikulum_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('kurikulum_id_str')
                    ->placeholder('-'),
                TextEntry::make('id_ruang')
                    ->placeholder('-'),
                TextEntry::make('id_ruang_str')
                    ->placeholder('-'),
                TextEntry::make('moving_class'),
                TextEntry::make('ptk_id')
                    ->placeholder('-'),
                TextEntry::make('ptk_id_str')
                    ->placeholder('-'),
                TextEntry::make('jurusan_id')
                    ->placeholder('-'),
                TextEntry::make('jurusan_id_str')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Rombel $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
