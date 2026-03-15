<?php

namespace App\Filament\Resources\DapodikConfs;

use App\Filament\Resources\DapodikConfs\Pages\CreateDapodikConf;
use App\Filament\Resources\DapodikConfs\Pages\EditDapodikConf;
use App\Filament\Resources\DapodikConfs\Pages\ListDapodikConfs;
use App\Models\DapodikConf;
use BackedEnum;
use Filament\Actions\ActionGroup as ActionsActionGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\DapodikService;
use Filament\Notifications\Notification;
use UnitEnum;

class DapodikConfResource extends Resource
{
    protected static ?string $model = DapodikConf::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;
    
    protected static string |UnitEnum| null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Konfigurasi Dapodik';
    protected static ?string $pluralModelLabel = 'Konfigurasi Dapodik';

    protected static ?string $recordTitleAttribute = 'npsn';

    // public static function form(Schema $schema): Schema
    // {
    //     return DapodikConfForm::configure($schema);
    // }
    // public static function form(Forms\Form $form): Forms\Form
    public static function form(Schema $schema): Schema
    {
        return $schema
        ->components([
                Section::make('Web service DAPODIK')
                    ->description('Klik tombol sinyal di kolom Token untuk mengetes koneksi ke server Dapodik.')
                    ->schema([
                        TextInput::make('base_url')
                            ->label('URL Web Service')
                            ->placeholder('http://192.168.x.x:5774')
                            ->required()
                            ->url(),
                        
                        TextInput::make('npsn')
                            ->label('NPSN Sekolah')
                            ->required()
                            ->maxLength(10),

                        TextInput::make('token')
                            ->label('Token Webservice')
                            ->password()
                            ->revealable()
                            ->required(),
                            // --- TOMBOL CEK KONEKSI DIMULAI DI SINI ---
                        Actions::make([
                            Action::make('cekKoneksi')
                                ->label('Cek Koneksi Ke Dapodik') // Tulisan tombol
                                ->icon('heroicon-m-signal')
                                ->color('info')
                                ->requiresConfirmation() // Opsional: Tambah konfirmasi sebelum tes
                                ->action(function ($get, DapodikService $service) {
                                    $url = $get('base_url');
                                    $npsn = $get('npsn');
                                    $token = $get('token');

                                    if (blank($url) || blank($npsn) || blank($token)) {
                                        Notification::make()
                                            ->title('Data Belum Lengkap')
                                            ->body('Silakan isi URL, NPSN, dan Token sebelum mencoba koneksi.')
                                            ->danger()
                                            ->send();
                                        return;
                                    }

                                    $result = $service->testConnection($url, $token, $npsn);

                                    if ($result['success']) {
                                        Notification::make()
                                            ->title('Koneksi Berhasil!')
                                            ->body($result['message'])
                                            ->success()
                                            ->send();
                                    } else {
                                        Notification::make()
                                            ->title('Koneksi Gagal')
                                            ->body($result['message'])
                                            ->danger()
                                            ->persistent()
                                            ->send();
                                    }
                                }),
                            ])->columnSpanFull(),          
                            // --- TOMBOL CEK KONEKSI SELESAI ---

                        Toggle::make('is_active')
                            ->label('Aktifkan Koneksi')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
                Section::make('Web service DAPODIK')
                    ->description('Klik tombol sinyal di kolom Token untuk mengetes koneksi ke server Dapodik.')
            ->schema([
                Section::make('Web service DAPODIK') // Gunakan Card atau Section sederhana
                    ->schema([
                        TextInput::make('base_url')
                            ->label('URL Web Service')
                            ->placeholder('http://localhost:5774')
                            ->required() // Validasi agar tidak NULL
                            ->url(),
                        
                        TextInput::make('npsn')
                            ->label('NPSN Sekolah')
                            ->required()
                            ->maxLength(10),

                        TextInput::make('token')
                            ->label('Token Webservice')
                            ->password()
                            ->revealable()
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Aktifkan Koneksi')
                            ->default(true),
                    ])
                    ->columns(2), // Membagi jadi 2 kolom di dalam card
            ]);
    }

    public static function table(Table $table): Table
    {
        // return DapodikConfsTable::configure($table);
        return $table
            ->columns([
                TextColumn::make('base_url')
                    ->label('Base URL')
                    ->limit(30),
                TextColumn::make('npsn')
                    ->label('NPSN'),
                TextColumn::make('token')
                    ->label('Token Webservice')
                    ->limit(10)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_sync_at')
                    ->label('Terakhir Sinkronisasi')
                    ->dateTime('d M Y H:i:s'),
                IconColumn::make('is_active')
                    ->label('Status Keaktifan')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                ActionsActionGroup::make([
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ])->label('Aksi Massal'),
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
            'index' => ListDapodikConfs::route('/'),
            'create' => CreateDapodikConf::route('/create'),
            'edit' => EditDapodikConf::route('/{record}/edit'),
        ];
    }
    // 1. Agar data muncul di tabel List saat difilter 'Trashed'
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    // 2. Agar data dapat diakses saat mengedit record yang dihapus secara soft delete
    // Agar halaman Edit/View tidak 404 saat mengakses data yang sudah dihapus
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
