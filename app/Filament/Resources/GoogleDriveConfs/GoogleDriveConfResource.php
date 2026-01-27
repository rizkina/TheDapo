<?php

namespace App\Filament\Resources\GoogleDriveConfs;

use App\Filament\Resources\GoogleDriveConfs\Pages;
use App\Models\GoogleDriveConf;
use App\Services\GoogleDriveService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
// IMPORT UNTUK FORM ACTIONS
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Group;
use Illuminate\Support\Facades\Storage;
use BackedEnum;
use UnitEnum;

class GoogleDriveConfResource extends Resource
{
    protected static ?string $model = GoogleDriveConf::class;

    // Gunakan string langsung, jangan pakai Enum Heroicon::... agar tidak error
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cloud';
    
    protected static string | UnitEnum | null $navigationGroup = 'Settings';
    
    protected static ?string $navigationLabel = 'Google Drive Config';

    protected static ?string $pluralModelLabel = 'Google Drive Configs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Gunakan Grid dengan pembagian kolom eksplisit untuk layar besar (lg)
                Grid::make([
                    'default' => 1,
                    'lg' => 2, // Ini memaksa 50:50 pada layar desktop
                ])
                ->schema([
                    
                    // --- KOLOM KIRI (Petunjuk & Token) ---
                    Group::make([
                        Section::make('Langkah Konfigurasi API')
                            ->description('Ikuti urutan ini untuk menghubungkan aplikasi.')
                            ->schema([
                                Placeholder::make('Petunjuk Konfigurasi')
                                    ->content(new HtmlString('
                                        <div class="space-y-3 text-sm">
                                            <ol class="list-decimal ml-4 space-y-2 text-gray-600 dark:text-gray-400">
                                                <li>Siapkan project di <b>Google Cloud Console</b>.</li>
                                                <li>Aktifkan <b>Google Drive API</b>.</li>
                                                <li>Buat kredensial <b>OAuth 2.0</b>.</li>
                                                <li>Masukkan Redirect URI:<br>
                                                    <code class="text-xs bg-gray-100 p-1 rounded">'.url('/google-drive/callback').'</code>
                                                </li>
                                                <li>Salin <b>Client ID & Secret</b> ke form samping.</li>
                                                <li><b>Simpan</b>, lalu klik <b>Hubungkan</b>.</li>
                                            </ol>
                                        </div>
                                    ')),
                            ])
                            ->collapsible(),

                        Section::make('Token Keamanan')
                            ->description('Data otomatis dari Google.')
                            ->collapsed()
                            ->schema([
                                Textarea::make('access_token')
                                    ->label('Access Token')
                                    ->disabled()
                                    ->rows(3),
                                Textarea::make('refresh_token')
                                    ->label('Refresh Token')
                                    ->disabled()
                                    ->rows(3),
                            ])
                            ->collapsible(),
                    ]), // Secara otomatis mengambil columnSpan(1)

                    // --- KOLOM KANAN (Kredensial & Aksi) ---
                    Section::make('Kredensial API Google')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Koneksi')
                                ->required()
                                ->default('Google Drive Utama'),

                            TextInput::make('folder_id')
                                ->label('Folder ID Utama')
                                ->placeholder('1By9_xxxxxxxxxxxxxxxxx')
                                ->helperText('Salin ID dari URL folder Google Drive Anda.'),

                            TextInput::make('client_id')
                                ->label('Google Client ID')
                                ->required(),

                            TextInput::make('client_secret')
                                ->label('Google Client Secret')
                                ->password()
                                ->revealable()
                                ->required(),

                            // --- TOMBOL AKSI ---
                            Actions::make([
                                // TOMBOL 1: HUBUNGKAN (Hanya muncul jika belum ada token)
                                Action::make('connectGoogle')
                                    ->label('Hubungkan Akun')
                                    ->icon('heroicon-m-link')
                                    ->color('primary')
                                    // Sembunyikan jika: record belum di-save (id null) ATAU sudah punya refresh_token
                                    ->hidden(fn ($record) => $record === null || !empty($record->refresh_token))
                                    ->url(fn () => route('google.drive.connect')),

                                // TOMBOL 2: CEK KONEKSI (Hanya muncul jika sudah ada token)
                                Action::make('checkConnection')
                                    ->label('Cek Koneksi')
                                    ->icon('heroicon-m-check-badge')
                                    ->color('success')
                                    // Muncul hanya jika sudah ada refresh_token
                                    ->visible(fn ($record) => $record !== null && !empty($record->refresh_token))
                                    ->action(function () {
                                        // 1. Bersihkan cache konfigurasi Laravel secara internal
                                        \Illuminate\Support\Facades\Artisan::call('config:clear');

                                        // 2. Coba jalankan koneksi melalui Service
                                        $result = \App\Services\GoogleDriveService::testConnectivity();

                                        if ($result['success']) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Koneksi Sukses!')
                                                ->body($result['message'])
                                                ->success()
                                                ->send();
                                        } else {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Koneksi Gagal')
                                                ->body($result['message'])
                                                ->danger()
                                                ->persistent()
                                                ->send();
                                        }
                                    }),
                                    
                                // TOMBOL 3: HUBUNGKAN ULANG (Hanya muncul jika sudah ada token)
                                Action::make('reconnectGoogle')
                                    ->label('Hubungkan Ulang')
                                    ->icon('heroicon-m-arrow-path')
                                    ->color('gray')
                                    ->visible(fn ($record) => $record !== null && !empty($record->refresh_token))
                                    ->requiresConfirmation()
                                    ->modalHeading('Hubungkan Ulang Akun?')
                                    ->modalDescription('Anda akan diarahkan kembali ke halaman login Google.')
                                    ->url(fn () => route('google.drive.connect')),
                            ])->columnSpanFull(),

                            // Status & Switch
                            Grid::make(1)->schema([
                                Placeholder::make('connection_status')
                                    ->label('Status Akun')
                                    ->content(fn ($record) => $record?->refresh_token 
                                        ? new HtmlString('<span class="text-success-600 font-bold">✅ Terhubung</span>') 
                                        : new HtmlString('<span class="text-danger-600 font-bold">❌ Belum Terhubung</span>')
                                    ),

                                Toggle::make('is_active')
                                    ->label('Aktifkan Sebagai Penyimpanan Utama')
                                    ->onColor('success')
                                    ->default(false),
                            ]),
                        ]),
                ])->columnSpanFull(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('folder_id')->label('Folder ID')->limit(20),
                IconColumn::make('is_active')->label('Status')->boolean(),
                // Indikator apakah sudah login google
                TextColumn::make('refresh_token')
                    ->label('Koneksi')
                    ->formatStateUsing(fn ($state) => $state ? 'Terhubung' : 'Terputus')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoogleDriveConfs::route('/'),
            'create' => Pages\CreateGoogleDriveConf::route('/create'),
            'view' => Pages\ViewGoogleDriveConf::route('/{record}'),
            'edit' => Pages\EditGoogleDriveConf::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}