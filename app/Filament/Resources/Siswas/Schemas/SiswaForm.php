<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sekolah_id'),
                TextInput::make('registrasi_id'),
                TextInput::make('jenis_pendaftaran_id')
                    ->numeric(),
                TextInput::make('jenis_pendaftaran_id_str'),
                TextInput::make('nipd'),
                DatePicker::make('tanggal_masuk_sekolah'),
                TextInput::make('sekolah_asal'),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('nisn'),
                TextInput::make('jenis_kelamin'),
                TextInput::make('nik'),
                TextInput::make('tempat_lahir'),
                DatePicker::make('tanggal_lahir'),
                TextInput::make('agama_id')
                    ->numeric(),
                TextInput::make('agama_id_str'),
                TextInput::make('nomor_telepon_rumah')
                    ->tel(),
                TextInput::make('nomor_telepon_seluler')
                    ->tel(),
                TextInput::make('nama_ayah'),
                TextInput::make('pekerjaan_ayah_id')
                    ->numeric(),
                TextInput::make('pekerjaan_ayah_id_str'),
                TextInput::make('nama_ibu'),
                TextInput::make('pekerjaan_ibu_id')
                    ->numeric(),
                TextInput::make('pekerjaan_ibu_id_str'),
                TextInput::make('nama_wali'),
                TextInput::make('pekerjaan_wali_id')
                    ->numeric(),
                TextInput::make('pekerjaan_wali_id_str'),
                TextInput::make('anak_keberapa')
                    ->numeric(),
                TextInput::make('tinggi_badan')
                    ->numeric(),
                TextInput::make('berat_badan')
                    ->numeric(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('semester_id'),
                TextInput::make('anggota_rombel_id'),
                TextInput::make('rombongan_belajar_id'),
                TextInput::make('tingkat_pendidikan_id')
                    ->numeric(),
                TextInput::make('nama_rombel'),
                TextInput::make('kurikulum_id')
                    ->numeric(),
                TextInput::make('kurikulum_id_str'),
                TextInput::make('kebutuhan_khusus'),
            ]);
    }
}
