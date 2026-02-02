<?php

namespace App\Filament\Resources\Rombels;

use App\Filament\Resources\Rombels\Pages\ListRombels;
use App\Filament\Resources\Rombels\Pages\ViewRombel;
use App\Filament\Resources\Rombels\Schemas\RombelForm;
use App\Models\Rombel;
use App\Models\Pembelajaran;
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

    protected static ?string $pluralModelLabel = 'Rombel';

    public static function form(Schema $schema): Schema
    {
        return RombelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        // return RombelInfolist::configure($schema);
        return $schema
        ->components([
            Section::make('Informasi Rombongan Belajar')
                ->icon(Heroicon::OutlinedHomeModern)
                // ->collapsed()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('jenis_rombel_str')->label('Jenis Rombel'),
                            TextEntry::make('nama')->label('Nama Rombel')->weight('bold')->size('lg'),
                            TextEntry::make('ptk.nama')->label('Wali Kelas')->color('primary'),
                            TextEntry::make('tingkat_pendidikan_id_str')->label('Tingkat Pendidikan'),
                            TextEntry::make('semester_id')->label('Semester'),
                            TextEntry::make('kurikulum_id_str')->label('Kurikulum'),
                            TextEntry::make('jurusan_id_str')->label('Jurusan'),
                            TextEntry::make('siswas_count')->label('Jumlah Siswa')->suffix(' siswa')->badge(),
                        ]),
                    ]),

            Section::make('Daftar Mata Pelajaran & Pengampu')
                    ->icon(Heroicon::BookOpen)
                    ->collapsed()
                    ->description('Daftar guru yang mengajar di kelas ini.')
                    ->schema([
                        RepeatableEntry::make('pembelajarans')
                            ->label('')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('nama_mata_pelajaran')
                                            ->label('Mata Pelajaran')
                                            ->weight('bold'),
                                        TextEntry::make('ptk.nama')
                                            ->label('Guru Pengampu')
                                            ->placeholder('Belum Ditentukan'),
                                        TextEntry::make('jam_mengajar_per_minggu')
                                            ->label('Jam/Minggu')
                                            ->badge()
                                            ->suffix(' JP'),
                                        TextEntry::make('status_di_kurikulum_str')
                                            ->label('Kategori')
                                            ->placeholder('Belum Ditentukan'),
                                            // ->badge(),
                                        
                                    ])
                            ])
                        ]),

            Section::make('Daftar Siswa')
                    ->icon(Heroicon::Users)
                    ->description('Daftar Siswa dalam Kelas')
                    // ->description('Total Siswa: '.fn($record) => $record->siswas()->count())
                    ->collapsed()
                        ->schema([
                            RepeatableEntry::make('siswas') // Relasi ke model Siswa
                                ->label('')
                                ->schema([
                                    Grid::make(4)
                                        ->schema([
                                            TextEntry::make('nisn')->label('NISN')->copyable(),
                                            TextEntry::make('nipd')->label('NIS'),
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

            Tables\Columns\TextColumn::make('jenis_rombel_str')
                ->label('Jenis Rombel')
                ->searchable()
                ->sortable()
                ->badge(),

            // Menampilkan Nama Wali Kelas (PTK)
            Tables\Columns\TextColumn::make('ptk.nama')
                ->label('Wali Kelas')
                ->searchable()
                ->placeholder('Belum Ditentukan'),

            Tables\Columns\TextColumn::make('tingkat_pendidikan_id')
                ->label('Tingkat')
                ->sortable(),

            Tables\Columns\TextColumn::make('jurusan_id_str')
                ->label('Jurusan'),
                // ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('semester_id')
                ->label('Semester')
                ->toggleable(isToggledHiddenByDefault: true),

            // Fitur Canggih: Hitung Jumlah Siswa otomatis
            Tables\Columns\TextColumn::make('siswas_count')
                ->label('Jml Siswa')
                ->counts('siswas') // Menggunakan relasi siswas() di model Rombel
                ->badge()
                ->color('success'),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),

            Tables\Filters\SelectFilter::make('jenis_rombel_str')
                ->label('Jenis Rombel')
                ->options(function () {
                    return Rombel::query()
                        ->whereNotNull('jenis_rombel')
                        ->whereNotNull('jenis_rombel_str')
                        ->select('jenis_rombel', 'jenis_rombel_str')
                        ->distinct()
                        ->orderBy('jenis_rombel', 'asc')
                        ->pluck('jenis_rombel_str', 'jenis_rombel')
                        ->toArray();
                })
                ->searchable()
                ->preload(),

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
        $query = parent::getEloquentQuery()
            ->with([
                'ptk',                // Wali Kelas
                'pembelajarans.ptk',  // Guru Mapel
                'siswas'              // Daftar Siswa
            ])
            ->withCount('siswas')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        /** @var \App\Models\Dapodik_User $user */
        $user = Auth::user();
        // 2. Proteksi Dasar: Jika tidak ada user, sembunyikan semua
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // 3. Jika Super Admin / Admin / Operator, biarkan melihat semua
        if ($user->hasAnyRole(['super_admin', 'admin', 'operator'])) {
            return $query;
        }

        // 4. Jika GTK (Guru)
        if ($user->hasRole('guru')) {
            // Cek apakah dia wali kelas di suatu rombel
            return $query->where('ptk_id', $user->ptk_id);
            // return $query->whereHas('rombels', function ($q) use ($user) {
            //     $q->where('ptk_id', $user->ptk_id);
            // });
        }

        // 3. Jika Siswa, hanya lihat dirinya sendiri
        if ($user->hasRole('siswa')) {
            return $query->where('id', $user->peserta_didik_id);
            // return $query->whereHas('siswas', function ($q) use ($user) {
            //     $q->where('siswas.id', $user->peserta_didik_id);
            // });
        }

        // Default: Tidak ada akses jika role tidak dikenali
        return $query->whereRaw('1 = 0');


    }

    public function pembelajarans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pembelajaran::class, 'rombel_id');
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

     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : 'gray';
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
}
