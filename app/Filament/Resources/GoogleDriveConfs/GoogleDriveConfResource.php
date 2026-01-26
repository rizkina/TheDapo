<?php

namespace App\Filament\Resources\GoogleDriveConfs;

use App\Filament\Resources\GoogleDriveConfs\Pages;
use App\Models\GoogleDriveConf;
use App\Services\GoogleDriveService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
// use Filament\Forms\Components\Section;
// IMPORT KHUSUS UNTUK ACTIONS DI DALAM FORM
use Filament\Forms\Components\Actions; 
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
// IMPORT UNTUK TABEL
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Navigation\NavigationGroup;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;

class GoogleDriveConfResource extends Resource
{
    protected static ?string $model = GoogleDriveConf::class;

    // Gunakan string langsung untuk Heroicon agar tidak error di Intelephense
    //  protected static string|BackedEnum|null $navigationIcon = Heroicon::Cloud;
    // Ganti baris yang menyebabkan error (sekitar baris 42)
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';
    
    protected static string |UnitEnum| null $navigationGroup = 'Settings';
    
    protected static ?string $navigationLabel = 'Google Drive Config';
    protected static ?string $pluralModelLabel = 'Google Drive Configs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konfigurasi Google Drive')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('folder_id')
                            ->label('Folder ID')
                            ->required(),

                        Textarea::make('service_account_json')
                            ->label('Service Account JSON')
                            ->required()
                            ->rows(10)
                            ->columnSpanFull(),

                        // PEMBUNGKUS TOMBOL (MENGGUNAKAN CLASS ACTIONS YANG SUDAH DI-IMPORT)
                        Actions::make([
                            Action::make('testGoogleConnection')
                                ->label('Test Koneksi Google Drive')
                                ->icon('heroicon-m-signal')
                                ->color('warning')
                                ->action(function ($get) {
                                    $json = $get('service_account_json');
                                    $folderId = $get('folder_id');

                                    if (!$json || !$folderId) {
                                        Notification::make()
                                            ->title('Data tidak lengkap')
                                            ->danger()
                                            ->send();
                                        return;
                                    }

                                    $result = GoogleDriveService::testConnectivity($json, $folderId);

                                    if ($result['success']) {
                                        Notification::make()->title($result['message'])->success()->send();
                                    } else {
                                        Notification::make()->title('Gagal')->body($result['message'])->danger()->persistent()->send();
                                    }
                                })
                        ])->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Aktifkan Koneksi')
                            ->default(true),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('folder_id')->label('Folder ID')->limit(20),
                IconColumn::make('is_active')->label('Status')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggledHiddenByDefault(),
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