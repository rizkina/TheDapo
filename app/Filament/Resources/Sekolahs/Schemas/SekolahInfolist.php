<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SekolahInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('npsn'),
                TextEntry::make('nama'),
                TextEntry::make('alamat_jalan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('rt')
                    ->placeholder('-'),
                TextEntry::make('rw')
                    ->placeholder('-'),
                TextEntry::make('kode_wilayah')
                    ->placeholder('-'),
                TextEntry::make('kode_pos')
                    ->placeholder('-'),
                TextEntry::make('nomor_telepon')
                    ->placeholder('-'),
                TextEntry::make('nomor_fax')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('website')
                    ->placeholder('-'),
                IconEntry::make('is_sks')
                    ->boolean(),
                TextEntry::make('lintang')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('bujur')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('dusun')
                    ->placeholder('-'),
                TextEntry::make('desa_kelurahan')
                    ->placeholder('-'),
                TextEntry::make('kecamatan')
                    ->placeholder('-'),
                TextEntry::make('kabupaten_kota')
                    ->placeholder('-'),
                TextEntry::make('provinsi')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
