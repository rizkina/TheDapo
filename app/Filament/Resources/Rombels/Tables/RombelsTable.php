<?php

namespace App\Filament\Resources\Rombels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RombelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('sekolah_id'),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('tingkat_pendidikan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tingkat_pendidikan_id_str')
                    ->searchable(),
                TextColumn::make('semester_id')
                    ->searchable(),
                TextColumn::make('jenis_rombel')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jenis_rombel_str')
                    ->searchable(),
                TextColumn::make('kurikulum_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kurikulum_id_str')
                    ->searchable(),
                TextColumn::make('id_ruang')
                    ->searchable(),
                TextColumn::make('id_ruang_str')
                    ->searchable(),
                TextColumn::make('moving_class')
                    ->searchable(),
                TextColumn::make('ptk_id'),
                TextColumn::make('ptk_id_str')
                    ->searchable(),
                TextColumn::make('jurusan_id')
                    ->searchable(),
                TextColumn::make('jurusan_id_str')
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
