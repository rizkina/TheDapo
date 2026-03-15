<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SekolahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('npsn')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                Textarea::make('alamat_jalan')
                    ->columnSpanFull(),
                TextInput::make('rt'),
                TextInput::make('rw'),
                TextInput::make('kode_wilayah'),
                TextInput::make('kode_pos'),
                TextInput::make('nomor_telepon')
                    ->tel(),
                TextInput::make('nomor_fax'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('website')
                    ->url(),
                Toggle::make('is_sks')
                    ->required(),
                TextInput::make('lintang')
                    ->numeric(),
                TextInput::make('bujur')
                    ->numeric(),
                TextInput::make('dusun'),
                TextInput::make('desa_kelurahan'),
                TextInput::make('kecamatan'),
                TextInput::make('kabupaten_kota'),
                TextInput::make('provinsi'),
            ]);
    }
}
