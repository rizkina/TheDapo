<?php

namespace App\Filament\Resources\Ptks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PtkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sekolah_id'),
                TextInput::make('ptk_terdaftar_id'),
                TextInput::make('ptk_induk'),
                DatePicker::make('tanggal_surat_tugas'),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('jenis_kelamin'),
                TextInput::make('tempat_lahir'),
                DatePicker::make('tanggal_lahir'),
                TextInput::make('agama_id')
                    ->numeric(),
                TextInput::make('agama_id_str'),
                TextInput::make('nuptk'),
                TextInput::make('nik'),
                TextInput::make('jenis_ptk_id')
                    ->numeric(),
                TextInput::make('jenis_ptk_id_str'),
                TextInput::make('jabatan_ptk_id')
                    ->numeric(),
                TextInput::make('jabatan_ptk_id_str'),
                TextInput::make('status_kepegawaian_id')
                    ->numeric(),
                TextInput::make('status_kepegawaian_id_str'),
                TextInput::make('nip'),
                TextInput::make('pendidikan_terakhir')
                    ->numeric(),
                TextInput::make('bidang_studi_terakhir'),
                TextInput::make('pangkat_golongan_terakhir'),
                TextInput::make('riwayat_pendidikan'),
                TextInput::make('riwayat_kepangkatan'),
            ]);
    }
}
