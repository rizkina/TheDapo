<?php

namespace App\Filament\Resources\Siswas\Schemas;

use App\Models\Siswa;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiswaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('sekolah_id')
                    ->placeholder('-'),
                TextEntry::make('registrasi_id')
                    ->placeholder('-'),
                TextEntry::make('jenis_pendaftaran_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('jenis_pendaftaran_id_str')
                    ->placeholder('-'),
                TextEntry::make('nipd')
                    ->placeholder('-'),
                TextEntry::make('tanggal_masuk_sekolah')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('sekolah_asal')
                    ->placeholder('-'),
                TextEntry::make('nama'),
                TextEntry::make('nisn')
                    ->placeholder('-'),
                TextEntry::make('jenis_kelamin')
                    ->placeholder('-'),
                TextEntry::make('nik')
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
                TextEntry::make('nomor_telepon_rumah')
                    ->placeholder('-'),
                TextEntry::make('nomor_telepon_seluler')
                    ->placeholder('-'),
                TextEntry::make('nama_ayah')
                    ->placeholder('-'),
                TextEntry::make('pekerjaan_ayah_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('pekerjaan_ayah_id_str')
                    ->placeholder('-'),
                TextEntry::make('nama_ibu')
                    ->placeholder('-'),
                TextEntry::make('pekerjaan_ibu_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('pekerjaan_ibu_id_str')
                    ->placeholder('-'),
                TextEntry::make('nama_wali')
                    ->placeholder('-'),
                TextEntry::make('pekerjaan_wali_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('pekerjaan_wali_id_str')
                    ->placeholder('-'),
                TextEntry::make('anak_keberapa')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tinggi_badan')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('berat_badan')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('semester_id')
                    ->placeholder('-'),
                TextEntry::make('anggota_rombel_id')
                    ->placeholder('-'),
                TextEntry::make('rombongan_belajar_id')
                    ->placeholder('-'),
                TextEntry::make('tingkat_pendidikan_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('nama_rombel')
                    ->placeholder('-'),
                TextEntry::make('kurikulum_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('kurikulum_id_str')
                    ->placeholder('-'),
                TextEntry::make('kebutuhan_khusus')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Siswa $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
