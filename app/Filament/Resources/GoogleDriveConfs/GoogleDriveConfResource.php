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
                Section::make('Kredensial API Google')
                    ->description('Lengkapi data Client ID dan Secret, Simpan, lalu klik tombol Hubungkan.')
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
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('client_secret')
                            ->label('Google Client Secret')
                            ->password()
                            ->revealable()
                            ->required()
                            ->columnSpanFull(),

                        // --- TOMBOL HUBUNGKAN AKUN ---
                        Actions::make([
                            Action::make('connectGoogle')
                                ->label('Hubungkan Akun Google')
                                ->icon('heroicon-m-link')
                                ->color('primary')
                                // Tombol ini hanya muncul jika record sudah di-save (punya ID)
                                ->hidden(fn ($record) => $record === null)
                                ->url(fn ($record) => route('google.drive.connect')), 
                        ])->columnSpanFull(),

                        // --- STATUS KONEKSI ---
                        Placeholder::make('connection_status')
                            ->label('Status Akun')
                            ->content(fn ($record) => $record?->refresh_token 
                                ? '✅ Terhubung (Aplikasi memiliki akses)' 
                                : '❌ Belum Terhubung'
                            ),

                        Toggle::make('is_active')
                            ->label('Aktifkan Sebagai Penyimpanan Utama')
                            ->default(true),
                    ])->columns(2),

                // SEKSI TOKEN (READ ONLY - UNTUK DEBUG)
                Section::make('Token Keamanan')
                    ->description('Data ini terisi otomatis oleh Google.')
                    ->collapsed()
                    ->schema([
                        Textarea::make('access_token')->disabled()->rows(3),
                        Textarea::make('refresh_token')->disabled()->rows(3),
                    ])
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