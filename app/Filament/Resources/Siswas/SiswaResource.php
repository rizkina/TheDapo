<?php

namespace App\Filament\Resources\Siswas;

use App\Filament\Resources\Siswas\Pages\CreateSiswa;
use App\Filament\Resources\Siswas\Pages\EditSiswa;
use App\Filament\Resources\Siswas\Pages\ListSiswas;
use App\Filament\Resources\Siswas\Pages\ViewSiswa;
use App\Filament\Resources\Siswas\Schemas\SiswaForm;
use App\Filament\Resources\Siswas\Schemas\SiswaInfolist;
use App\Filament\Resources\Siswas\Tables\SiswasTable;
use App\Models\Siswa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Exports\SiswaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Filament\Actions\Action;


class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

     protected static ?string $pluralModelLabel = 'Siswa';

    protected static ?string $recordTitleAttribute = 'nama';

    // 
    
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEKSI 1: IDENTITAS (READ ONLY)
                Section::make('Identitas Utama (Dapodik)')
                    ->description('Data ini dikunci dan hanya bisa diperbarui melalui Sinkronisasi Dapodik.')
                    ->schema([
                        TextInput::make('nama')->disabled(),
                        TextInput::make('nisn')->disabled(),
                        TextInput::make('nipd')->label('NIS')->disabled(),
                        TextInput::make('nik')->disabled(),
                        TextInput::make('tempat_lahir')->disabled(),
                        DatePicker::make('tanggal_lahir')->disabled(),
                        TextInput::make('nama_rombel')->label('Kelas Saat Ini')->disabled(),
                    ])->columns(2),

                // SEKSI 2: DATA YANG BOLEH DIEDIT
                Section::make('Data Pelengkap & Orang Tua')
                    ->description('Data di bawah ini diperbolehkan untuk diperbarui secara lokal.')
                    ->schema([
                        // Agama (Editable)
                        Select::make('agama_id')
                            ->label('Agama')
                            ->relationship('agama', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('nomor_telepon_seluler')
                            ->label('No. Telepon Seluler')
                            ->tel(),
                        TextInput::make('nomor_telepon_rumah')
                            ->label('No. Telepon Rumah')
                            ->tel(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        TextInput::make('anak_keberapa')
                            ->label('Anak Ke-')
                            ->numeric(),
                        TextInput::make('tinggi_badan')
                            ->label('Tinggi Badan (cm)')
                            ->numeric(),
                        TextInput::make('berat_badan')
                            ->label('Berat Badan (kg)')
                            ->numeric(),
                        TextInput::make('kebutuhan_khusus')
                            ->label('Kebutuhan Khusus')
                            ->helperText('Isi jika berkebutuhan khusus, kosongkan jika tidak.'),
                    
                        
                    ])->columns(2),
                Section::make('Data Orang Tua')
                    ->description('Data tambahan yang dapat diisi sesuai kebutuhan.')
                    ->schema([
                        // Data Orang Tua (Editable)
                        TextInput::make('nama_ayah'),
                        TextInput::make('nik_ayah')
                            ->label('NIK Ayah')
                            ->mask('9999999999999999') // Memaksa input 16 digit angka (UX sangat bagus)
                            ->length(16)               // Memastikan panjang tepat 16
                            ->rules(['digits:16']),     // Validasi Laravel agar harus 16 digit angka
                        TextInput::make('tahun_lahir_ayah')
                            ->label('Tahun Lahir Ayah')
                            ->numeric()
                            ->length(4),
                        Select::make('pekerjaan_ayah_id')
                            ->relationship('pekerjaanAyah', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('pendidikan_ayah_id')
                            ->relationship('pendidikanAyah', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('penghasilan_ayah_id')
                            ->label('Penghasilan Ayah')
                            ->relationship('penghasilanAyah', 'nama')
                            ->searchable()
                            ->preload(),

                        TextInput::make('nama_ibu'),
                        TextInput::make('nik_ibu')
                            ->label('NIK Ibu')
                            ->mask('9999999999999999')
                            ->length(16)
                            ->rules(['digits:16']),
                        TextInput::make('tahun_lahir_ibu')
                            ->label('Tahun Lahir Ibu')
                            ->numeric()
                            ->length(4),
                        Select::make('pekerjaan_ibu_id')
                            ->relationship('pekerjaanIbu', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('pendidikan_ibu_id')
                            ->relationship('pendidikanIbu', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('penghasilan_ibu_id')
                            ->label('Penghasilan Ibu')
                            ->relationship('penghasilanIbu', 'nama')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                Section::make('Data Wali')
                    ->description('Diisi jika siswa memiliki wali atau tinggal dengan selain Ayah/Ibu.')
                    ->schema([
                        TextInput::make('nama_wali'),
                        TextInput::make('nik_wali')
                            ->label('NIK Wali')
                            ->mask('9999999999999999')
                            ->length(16)
                            ->rules(['digits:16']),
                        TextInput::make('tahun_lahir_wali')
                            ->label('Tahun Lahir Wali')
                            ->numeric()
                            ->length(4),
                        Select::make('pekerjaan_wali_id')
                            ->relationship('pekerjaanWali', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('pendidikan_wali_id')
                            ->relationship('pendidikanWali', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('penghasilan_wali_id')
                            ->label('Penghasilan Wali')
                            ->relationship('penghasilanWali', 'nama')
                            ->searchable()
                            ->preload(),
                    ])
                    ->collapsed()
                    ])->columns(2);
            // ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SiswaInfolist::configure($schema);
    }

    // public static function table(Table $table): Table
    // {
    //     return SiswasTable::configure($table);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->copyable()
                    ->searchable(),
                    
                TextColumn::make('nipd')
                    ->label('NIS')
                    ->searchable(),

                TextColumn::make('nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tempat_lahir')
                    ->label('Tempat Lahir'),

                TextColumn::make('tanggal_lahir')
                    ->label('Tgl Lahir')
                    ->sortable()
                    ->date('d/m/Y'),

                TextColumn::make('nama_rombel')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                // Data lain disembunyikan secara default
                TextColumn::make('nik')
                    ->label('NIK')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nama_ibu')
                    ->label('Nama Ibu')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Action::make('export excel')
                    ->label('Excel (Semua)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn () => Excel::download(new SiswaExport(Siswa::all()), 'data-siswa.xlsx')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('export selected excel')
                        ->label('Excel (Terpilih)')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->action(fn ($records) => Excel::download(new SiswaExport($records), 'siswa-pilihan.xlsx')),

                    // Ekspor PDF yang dicentang
                    BulkAction::make('exportSelectedPdf')
                        ->label('PDF (Terpilih)')
                        ->icon('heroicon-o-document-text')
                        ->color('primary')
                        ->action(function ($records) {
                            $pdf = Pdf::loadView('pdf.siswa', ['siswas' => $records])
                                ->setPaper('a4', 'landscape');

                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                'siswa-pilihan.pdf'
                            );
                        }),
                ])
            ])

            ->actions([
                ActionGroup::make([
                    ViewAction::make(), // Untuk melihat Detail
                    EditAction::make(), // Untuk mengedit data tertentu
                ])
            ]);
            
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // Opsional: Memberi warna pada badge
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : 'gray';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiswas::route('/'),
            // 'create' => CreateSiswa::route('/create'),
            'view' => ViewSiswa::route('/{record}'),
            'edit' => EditSiswa::route('/{record}/edit'),
        ];
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
}
