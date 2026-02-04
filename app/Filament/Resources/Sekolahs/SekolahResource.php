<?php

namespace App\Filament\Resources\Sekolahs;

// use App\Filament\Resources\Sekolahs\Pages\CreateSekolah;
// use App\Filament\Resources\Sekolahs\Pages\EditSekolah;
use App\Filament\Resources\Sekolahs\Pages\ListSekolahs;
use App\Filament\Resources\Sekolahs\Pages\ViewSekolah;
use App\Filament\Resources\Sekolahs\Schemas\SekolahForm;
use App\Filament\Resources\Sekolahs\Schemas\SekolahInfolist;
use App\Filament\Resources\Sekolahs\Tables\SekolahsTable;
use App\Models\Sekolah;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Tables;
use Google\Service\Docs\Tab;

class SekolahResource extends Resource
{
    protected static ?string $model = Sekolah::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice;

    protected static ?string $navigationLabel = 'Sekolah';

    protected static ?string $pluralModelLabel = 'Sekolah';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SekolahForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SekolahInfolist::configure($schema);
       
    }

    // public static function table(Table $table): Table
    // {
    //     return SekolahsTable::configure($table);
    // }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Hanya tampilkan 3-4 kolom utama agar ringan
                Tables\Columns\TextColumn::make('npsn')
                    ->label('NPSN')
                    ->copyable() // Memudahkan admin menyalin NPSN
                    ->searchable(),
                Tables\Columns\TextColumn::make('nss')
                    ->label('NSS')
                    ->copyable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bentuk_pendidikan_id_str')
                    ->label('Bentuk Pendidikan')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_sekolah_str')
                    ->label('Status Sekolah')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('kecamatan')
                    ->label('Kecamatan'),

                Tables\Columns\TextColumn::make('kabupaten_kota')
                    ->label('Kota/Kabupaten'),
                Tables\Columns\TextColumn::make('provinsi')
                    ->label('Provinsi')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tetap biarkan TrashedFilter jika ingin melihat data yang pernah dihapus
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                // GANTI EditAction menjadi ViewAction
                // Tables\Actions\ViewAction::make(), 
                ViewAction::make()
                    ->label('Lihat Detail')
                    ->icon(Heroicon::Eye)
                    ->button(),
            ])
            ->bulkActions([
            //    
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSekolahs::route('/'),
            // 'create' => CreateSekolah::route('/create'),
            'view' => ViewSekolah::route('/{record}'),
            // 'edit' => EditSekolah::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

}
