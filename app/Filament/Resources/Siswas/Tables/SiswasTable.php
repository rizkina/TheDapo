<?php

namespace App\Filament\Resources\Siswas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('sekolah_id'),
                TextColumn::make('registrasi_id')
                    ->searchable(),
                TextColumn::make('jenis_pendaftaran_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jenis_pendaftaran_id_str')
                    ->searchable(),
                TextColumn::make('nipd')
                    ->searchable(),
                TextColumn::make('tanggal_masuk_sekolah')
                    ->date()
                    ->sortable(),
                TextColumn::make('sekolah_asal')
                    ->searchable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('nisn')
                    ->searchable(),
                TextColumn::make('jenis_kelamin')
                    ->searchable(),
                TextColumn::make('nik')
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
                TextColumn::make('nomor_telepon_rumah')
                    ->searchable(),
                TextColumn::make('nomor_telepon_seluler')
                    ->searchable(),
                TextColumn::make('nama_ayah')
                    ->searchable(),
                TextColumn::make('pekerjaan_ayah_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pekerjaan_ayah_id_str')
                    ->searchable(),
                TextColumn::make('nama_ibu')
                    ->searchable(),
                TextColumn::make('pekerjaan_ibu_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pekerjaan_ibu_id_str')
                    ->searchable(),
                TextColumn::make('nama_wali')
                    ->searchable(),
                TextColumn::make('pekerjaan_wali_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pekerjaan_wali_id_str')
                    ->searchable(),
                TextColumn::make('anak_keberapa')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tinggi_badan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('berat_badan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('semester_id')
                    ->searchable(),
                TextColumn::make('anggota_rombel_id'),
                TextColumn::make('rombongan_belajar_id'),
                TextColumn::make('tingkat_pendidikan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nama_rombel')
                    ->searchable(),
                TextColumn::make('kurikulum_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kurikulum_id_str')
                    ->searchable(),
                TextColumn::make('kebutuhan_khusus')
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
