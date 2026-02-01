<?php

namespace App\Filament\Resources\Ptks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PtksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('sekolah_id'),
                TextColumn::make('ptk_terdaftar_id')
                    ->searchable(),
                TextColumn::make('ptk_induk')
                    ->searchable(),
                TextColumn::make('tanggal_surat_tugas')
                    ->date()
                    ->sortable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('jenis_kelamin')
                    ->searchable(),
                TextColumn::make('tempat_lahir')
                    ->searchable(),
                TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                TextColumn::make('agama_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('agama_id_str')
                    ->searchable(),
                TextColumn::make('nuptk')
                    ->searchable(),
                TextColumn::make('nik')
                    ->searchable(),
                TextColumn::make('jenis_ptk_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jenis_ptk_id_str')
                    ->searchable(),
                TextColumn::make('jabatan_ptk_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jabatan_ptk_id_str')
                    ->searchable(),
                TextColumn::make('status_kepegawaian_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status_kepegawaian_id_str')
                    ->searchable(),
                TextColumn::make('nip')
                    ->searchable(),
                TextColumn::make('pendidikan_terakhir')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bidang_studi_terakhir')
                    ->searchable(),
                TextColumn::make('pangkat_golongan_terakhir')
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
