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
                Section::make('Konfigurasi Google Drive')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('folder_id')->label('Folder ID (Opsional)'),
                        
                        Actions::make([
                            Action::make('connectGoogle')
                                ->label('Hubungkan Akun Google')
                                ->icon('heroicon-m-link')
                                ->color('primary')
                                ->url(fn () => route('google.drive.connect')), // Mengarah ke controller luar
                        ])->columnSpanFull(),
                        
                        Placeholder::make('status')
                            ->label('Status Koneksi')
                            ->content(fn ($record) => $record?->refresh_token ? '✅ Terhubung' : '❌ Belum Terhubung'),
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
                // Menggunakan ActionGroup agar UI rapi dan Intelephense tenang
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