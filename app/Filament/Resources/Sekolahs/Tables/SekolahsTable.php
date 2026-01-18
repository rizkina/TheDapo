<?php

namespace App\Filament\Resources\Sekolahs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SekolahsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('npsn')
                    ->searchable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('rt')
                    ->searchable(),
                TextColumn::make('rw')
                    ->searchable(),
                TextColumn::make('kode_wilayah')
                    ->searchable(),
                TextColumn::make('kode_pos')
                    ->searchable(),
                TextColumn::make('nomor_telepon')
                    ->searchable(),
                TextColumn::make('nomor_fax')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('website')
                    ->searchable(),
                IconColumn::make('is_sks')
                    ->boolean(),
                TextColumn::make('lintang')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bujur')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('dusun')
                    ->searchable(),
                TextColumn::make('desa_kelurahan')
                    ->searchable(),
                TextColumn::make('kecamatan')
                    ->searchable(),
                TextColumn::make('kabupaten_kota')
                    ->searchable(),
                TextColumn::make('provinsi')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
