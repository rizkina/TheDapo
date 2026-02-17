<?php

namespace App\Filament\Resources\Ptks;

use App\Filament\Resources\Ptks\Pages\CreatePtk;
use App\Filament\Resources\Ptks\Pages\EditPtk;
use App\Filament\Resources\Ptks\Pages\ListPtks;
use App\Filament\Resources\Ptks\Pages\ViewPtk;
use App\Filament\Resources\Ptks\Schemas\PtkForm;
use App\Filament\Resources\Ptks\Schemas\PtkInfolist;
use App\Filament\Resources\Ptks\Tables\PtksTable;
use App\Models\Ptk;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Components\Section;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
// use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Infolists\Components\ImageEntry;

class PtkResource extends Resource
{
    protected static ?string $model = Ptk::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Identification;

    // protected static string | UnitEnum | null $navigationGroup = 'Manajemen GTK';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Data Guru & Tendik';

    protected static ?string $pluralModelLabel = 'Guru & Tenaga Kependidikan';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        // return PtkForm::configure($schema);
        return $schema
            ->components([
                Section::make('Pas Foto')
                    ->schema([
                        FileUpload::make('foto')
                            ->label('Foto Profil')
                            ->image() // Pastikan hanya gambar
                            ->avatar() // Membuat bentuk lingkaran (khusus foto profil)
                            ->imageEditor() // Memungkinkan admin melakukan crop/resize
                            ->circleCropper()
                            ->directory('foto-ptk') // Tersimpan di storage/app/public/foto-siswa
                            ->disk('public') // Gunakan disk lokal
                            ->maxSize(1024), // Batasi 1MB agar server tetap ringan
                    ])->columnSpan(1),

                Section::make('Informasi Utama')
                    ->description('Data ini sinkron dengan Dapodik.')
                    ->schema([
                        Forms\Components\TextInput::make('nama')->disabled(),
                        Forms\Components\TextInput::make('nuptk')->label('NUPTK')->disabled(),
                        Forms\Components\TextInput::make('nip')->label('NIP')->disabled(),
                        Forms\Components\TextInput::make('nik')->label('NIK')->disabled(),
                        Forms\Components\TextInput::make('tempat_lahir')->disabled(),
                        Forms\Components\DatePicker::make('tanggal_lahir')->disabled(),
                    ])->columns(2),

                Section::make('Status Kepegawaian')
                    ->schema([
                        Forms\Components\TextInput::make('jenis_ptk_id_str')->label('Jenis PTK')->disabled(),
                        Forms\Components\TextInput::make('status_kepegawaian_id_str')->label('Status')->disabled(),
                        Forms\Components\TextInput::make('pangkat_golongan_terakhir')->label('Golongan')->disabled(),
                    ])->columns(3),

                // Menampilkan Riwayat Pendidikan (JSON) secara rapi
                Section::make('Riwayat Pendidikan Formal')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Repeater::make('riwayat_pendidikan')
                            ->label('Daftar Pendidikan')
                            ->schema([
                                Forms\Components\TextInput::make('satuan_pendidikan_formal')->label('Sekolah/Kampus'),
                                Forms\Components\TextInput::make('jenjang_pendidikan_id_str')->label('Jenjang'),
                                Forms\Components\TextInput::make('tahun_lulus')->label('Lulus'),
                                Forms\Components\TextInput::make('gelar_akademik_id_str')->label('Gelar'),
                            ])
                            ->columns(4)
                            ->disabled() // Tidak bisa diubah
                            ->addable(false) // Tidak bisa tambah manual
                            ->deletable(false) // Tidak bisa hapus
                    ])
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        // return PtkInfolist::configure($schema);
        return $schema
        ->schema([
            Section::make('Foto Profil')
                ->schema([
                    ImageEntry::make('foto')
                        ->label('')
                        ->circular()
                        ->disk('public')
                        ->height(150),
                ])->columnSpan(1),
            // SEKSI 1: IDENTITAS UTAMA
            Section::make('Identitas Pegawai')
                ->icon('heroicon-m-user')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('nama')
                                ->label('Nama Lengkap')
                                ->weight('bold')
                                ->copyable(),
                            TextEntry::make('nuptk')
                                ->label('NUPTK')
                                ->copyable()
                                ->placeholder('Tidak Ada'),
                            TextEntry::make('nip')
                                ->label('NIP')
                                ->copyable()
                                ->placeholder('Tidak Ada'),
                        ]),
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('nik')
                                ->label('NIK')
                                ->copyable(),
                            TextEntry::make('tempat_lahir')
                                ->label('Tempat Lahir'),
                            TextEntry::make('tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->date('d F Y'),
                        ]),
                ]),

            // SEKSI 2: KEPEGAWAIAN & PENDIDIKAN TERAKHIR
            Section::make('Status & Pendidikan')
                ->icon('heroicon-m-academic-cap')
                ->schema([
                    Grid::make(4)
                        ->schema([
                            TextEntry::make('ptk_induk')
                                ->label('Status Induk')
                                ->weight('bold'),
                            TextEntry::make('jenis_ptk_id_str')
                                ->label('Jenis PTK')
                                ->badge()
                                ->color('info'),
                            TextEntry::make('status_kepegawaian_id_str')
                                ->label('Status Kepegawaian')
                                ->badge()
                                ->color('success'),
                            TextEntry::make('pendidikanTerakhir.nama')
                                ->label('Pendidikan Terakhir')
                                ->weight('bold'),
                        ]),
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('bidang_studi_terakhir')
                                ->label('Bidang Studi'),
                            TextEntry::make('pangkat_golongan_terakhir')
                                ->label('Pangkat / Golongan')
                                ->placeholder('-'),
                        ]),
                ]),

            // SEKSI 3: RIWAYAT PENDIDIKAN (DARI JSONB)
            Section::make('Riwayat Pendidikan Formal')
                ->description('Data riwayat pendidikan yang tercatat di Dapodik')
                ->icon('heroicon-m-briefcase')
                ->collapsed()
                ->schema([
                    RepeatableEntry::make('riwayat_pendidikan')
                        ->label('')
                        ->schema([
                            Grid::make(4)
                                ->schema([
                                    TextEntry::make('satuan_pendidikan_formal')
                                        ->label('Nama Sekolah/Kampus'),
                                    TextEntry::make('jenjang_pendidikan_id_str')
                                        ->label('Jenjang'),
                                    TextEntry::make('tahun_lulus')
                                        ->label('Tahun Lulus'),
                                    TextEntry::make('gelar_akademik_id_str')
                                        ->label('Gelar'),
                                ]),
                        ])
                        ->columns(1)
                ]),

            // SEKSI 4: RIWAYAT KEPANGKATAN (DARI JSONB)
            Section::make('Riwayat Kepangkatan')
                ->icon('heroicon-m-arrow-trending-up')
                ->collapsed()
                ->schema([
                    RepeatableEntry::make('riwayat_kepangkatan')
                        ->label('')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('pangkat_golongan_id_str')
                                        ->label('Golongan'),
                                    TextEntry::make('nomor_sk')
                                        ->label('Nomor SK'),
                                    TextEntry::make('tanggal_sk')
                                        ->label('TMT Pangkat')
                                        ->date('d M Y'),
                                ]),
                        ])
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        // return PtksTable::configure($table);
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('')
                    ->circular() // Bentuk lingkaran
                    ->defaultImageUrl(url('/images/avatar.png')),
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nuptk')
                    ->label('NUPTK')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->placeholder('-')
                    ->toggleable()
                    ->copyable(),
                TextColumn::make('jenis_ptk_id_str')
                    ->label('Jenis GTK'),
                TextColumn::make('status_kepegawaian_id_str')
                    ->label('Status')
                    ->badge()
                    ->color('info'),
                TextColumn::make('deleted_at')
                    ->label('Tanggal Keluar')
                    ->dateTime('d M Y')
                    ->since() // Menampilkan "2 hari yang lalu"
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    // Tambahkan EditAction jika suatu saat Anda menambah field lokal (seperti No HP)
                ]),
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
            'index' => ListPtks::route('/'),
            // 'create' => CreatePtk::route('/create'),
            'view' => ViewPtk::route('/{record}'),
            'edit' => EditPtk::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\Dapodik_User $user */
        $user = Auth::user();

        $query = parent::getEloquentQuery()
            ->with([
                'pendidikanTerakhir',
                'sekolah',
                'agama',
            ])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
        

            // 2. Proteksi: Jika user belum login, jangan tampilkan apa-apa
            if (!$user) {
                return $query->whereRaw('1 = 0');
            }

            // 3. Role: Super Admin, Admin, atau Operator (Lihat SEMUA data)
            if ($user->hasAnyRole(['super_admin', 'admin', 'operator', 'kepsek'])) {
                return $query;
            }

            // 4. Role: Guru / GTK (Hanya lihat siswa di kelas binaannya / Wali Kelas)
            if ($user->hasAnyRole('guru', 'tenaga kependidikan')) {
                if (!$user->ptk_id) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->where('id', $user->ptk_id);
            }

            // 6. Default: Jika user punya role lain yang tidak terdaftar, sembunyikan semua data (Safety First)
            return $query->whereRaw('1 = 0');
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    // public static function canEdit($record): bool
    // {
    //     return true;
    // }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    protected static function getHeaderWidgets(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    // Opsional: Memberi warna pada badge
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : 'gray';
    }
}
