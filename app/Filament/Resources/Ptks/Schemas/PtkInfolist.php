<?php

namespace App\Filament\Resources\Ptks\Schemas;

use App\Models\Ptk;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PtkInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('sekolah_id')
                    ->placeholder('-'),
                TextEntry::make('ptk_terdaftar_id')
                    ->placeholder('-'),
                TextEntry::make('ptk_induk')
                    ->placeholder('-'),
                TextEntry::make('tanggal_surat_tugas')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('nama'),
                TextEntry::make('jenis_kelamin')
                    ->placeholder('-'),
                TextEntry::make('tempat_lahir')
                    ->placeholder('-'),
                TextEntry::make('tanggal_lahir')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('agama_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('agama_id_str')
                    ->placeholder('-'),
                TextEntry::make('nuptk')
                    ->placeholder('-'),
                TextEntry::make('nik')
                    ->placeholder('-'),
                TextEntry::make('jenis_ptk_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('jenis_ptk_id_str')
                    ->placeholder('-'),
                TextEntry::make('jabatan_ptk_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('jabatan_ptk_id_str')
                    ->placeholder('-'),
                TextEntry::make('status_kepegawaian_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status_kepegawaian_id_str')
                    ->placeholder('-'),
                TextEntry::make('nip')
                    ->placeholder('-'),
                TextEntry::make('pendidikan_terakhir')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('bidang_studi_terakhir')
                    ->placeholder('-'),
                TextEntry::make('pangkat_golongan_terakhir')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Ptk $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
