<?php

namespace App\Filament\Resources\Rombels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RombelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sekolah_id'),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('tingkat_pendidikan_id')
                    ->numeric(),
                TextInput::make('tingkat_pendidikan_id_str'),
                TextInput::make('semester_id'),
                TextInput::make('jenis_rombel')
                    ->numeric(),
                TextInput::make('jenis_rombel_str'),
                TextInput::make('kurikulum_id')
                    ->numeric(),
                TextInput::make('kurikulum_id_str'),
                TextInput::make('id_ruang'),
                TextInput::make('id_ruang_str'),
                TextInput::make('moving_class')
                    ->required()
                    ->default('Tidak'),
                TextInput::make('ptk_id'),
                TextInput::make('ptk_id_str'),
                TextInput::make('jurusan_id'),
                TextInput::make('jurusan_id_str'),
            ]);
    }
}
