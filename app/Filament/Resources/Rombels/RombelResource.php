<?php

namespace App\Filament\Resources\Rombels;

use App\Filament\Resources\Rombels\Pages\CreateRombel;
use App\Filament\Resources\Rombels\Pages\EditRombel;
use App\Filament\Resources\Rombels\Pages\ListRombels;
use App\Filament\Resources\Rombels\Pages\ViewRombel;
use App\Filament\Resources\Rombels\Schemas\RombelForm;
use App\Filament\Resources\Rombels\Schemas\RombelInfolist;
use App\Filament\Resources\Rombels\Tables\RombelsTable;
use App\Models\Rombel;
use App\Models\Dapodik_User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Auth; 

class RombelResource extends Resource
{
    protected static ?string $model = Rombel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::TableCells;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return RombelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        // return RombelInfolist::configure($schema);
        return $schema
        ->components([
            Section::make('Daftar Siswa di Kelas Ini')
                ->collapsed()
                ->schema([
                    RepeatableEntry::make('siswas') // Relasi ke model Siswa
                        ->label('')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('nisn')->label('NISN'),
                                    TextEntry::make('nama')->label('Nama Siswa'),
                                    TextEntry::make('jenis_kelamin')->label('L/P'),
                                ]),
                        ])
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        // return RombelsTable::configure($table);
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('nama')
                ->label('Nama Rombel')
                ->searchable()
                ->sortable(),

            // Menampilkan Nama Wali Kelas (PTK)
            Tables\Columns\TextColumn::make('ptk.nama')
                ->label('Wali Kelas')
                ->searchable()
                ->placeholder('Belum Ditentukan'),

            Tables\Columns\TextColumn::make('tingkat_pendidikan_id')
                ->label('Tingkat')
                ->sortable(),

            Tables\Columns\TextColumn::make('semester_id')
                ->label('Semester'),

            // Fitur Canggih: Hitung Jumlah Siswa otomatis
            Tables\Columns\TextColumn::make('siswas_count')
                ->label('Jml Siswa')
                ->counts('siswas') // Menggunakan relasi siswas() di model Rombel
                ->badge()
                ->color('success'),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),
            Tables\Filters\SelectFilter::make('tingkat_pendidikan_id')
            ->label('Tingkat Pendidikan')
            ->options(function () {
                return Rombel::query()
                    ->whereNotNull('tingkat_pendidikan_id')
                    ->whereNotNull('tingkat_pendidikan_id_str')
                    ->select('tingkat_pendidikan_id', 'tingkat_pendidikan_id_str')
                    ->distinct() 
                    ->orderBy('tingkat_pendidikan_id', 'asc') 
                    ->pluck('tingkat_pendidikan_id_str', 'tingkat_pendidikan_id')
                    ->toArray();
            })
            ->searchable() 
            ->preload(),   
        ])
        ->actions([
            ViewAction::make(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\Dapodik_User $user */
        $user = Auth::user();

        // 1. Jika Super Admin / Admin / Operator, biarkan melihat semua
        if ($user->hasAnyRole(['super_admin', 'admin', 'operator'])) {
            return $query;
        }

        // 2. Jika GTK (Guru)
        if ($user->hasRole('guru')) {
            // Cek apakah dia wali kelas di suatu rombel
            return $query->whereHas('rombels', function ($q) use ($user) {
                $q->where('ptk_id', $user->ptk_id);
            });
        }

        // 3. Jika Siswa, hanya lihat dirinya sendiri
        if ($user->hasRole('siswa')) {
            return $query->where('id', $user->peserta_didik_id);
        }

        // Default: Tidak ada akses jika role tidak dikenali
        return $query->whereRaw('1 = 0');
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
            'index' => ListRombels::route('/'),
            // 'create' => CreateRombel::route('/create'),
            'view' => ViewRombel::route('/{record}'),
            // 'edit' => EditRombel::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
}
