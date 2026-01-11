<?php

namespace App\Filament\Resources\DapodikConfs;

use App\Filament\Resources\DapodikConfs\Pages\CreateDapodikConf;
use App\Filament\Resources\DapodikConfs\Pages\EditDapodikConf;
use App\Filament\Resources\DapodikConfs\Pages\ListDapodikConfs;
use App\Filament\Resources\DapodikConfs\Schemas\DapodikConfForm;
use App\Filament\Resources\DapodikConfs\Tables\DapodikConfsTable;
use App\Models\DapodikConf;
use BackedEnum;
use Dom\Text;
use Filament\Actions\ActionGroup as ActionsActionGroup;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Container\Attributes\DB;
// use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PHPUnit\Metadata\Group;
use UnitEnum;

class DapodikConfResource extends Resource
{
    protected static ?string $model = DapodikConf::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;
    
    protected static string | UnitEnum | null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Konfigurasi Dapodik';

    protected static ?string $recordTitleAttribute = 'npsn';

    // public static function form(Schema $schema): Schema
    // {
    //     return DapodikConfForm::configure($schema);
    // }
    // public static function form(Forms\Form $form): Forms\Form
    public static function form(Schema $schema): Schema
    {
        return $schema
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
            ->bulkActions([
                ActionsBulkActionGroup::make([
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

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
