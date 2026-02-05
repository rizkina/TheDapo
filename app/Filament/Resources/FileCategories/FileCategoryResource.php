<?php

namespace App\Filament\Resources\FileCategories;

use App\Filament\Resources\FileCategories\Pages\CreateFileCategory;
use App\Filament\Resources\FileCategories\Pages\EditFileCategory;
use App\Filament\Resources\FileCategories\Pages\ListFileCategories;
use App\Filament\Resources\FileCategories\Pages\ViewFileCategory;
use App\Filament\Resources\FileCategories\Schemas\FileCategoryInfolist;
use App\Models\FileCategory;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class FileCategoryResource extends Resource
{
    protected static ?string $model = FileCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::FolderOpen;
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen File';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Kategori File';
    protected static ?string $pluralModelLabel = 'Kategori File';
    protected static ?string $modelLabel = 'Kategori File';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Pengaturan Kategori & Akses')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('nama')
                        ->label('Nama Kategori')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($set, $state) => $set('slug', \Illuminate\Support\Str::slug($state))),

                    \Filament\Forms\Components\TextInput::make('slug')
                        ->label('Nama Folder di Drive')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->disabled() // Kita buat otomatis dari nama
                        ->dehydrated(), // Tetap simpan ke database meskipun disabled

                    \Filament\Forms\Components\Select::make('roles')
                        ->label('Role yang Diizinkan Mengunggah')
                        ->relationship('roles', 'name') // Menghubungkan ke tabel pivot Spatie
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2)
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FileCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return FileCategoriesTable::configure($table);
         return $table
        ->columns([
            \Filament\Tables\Columns\TextColumn::make('nama')->searchable()->sortable(),
            \Filament\Tables\Columns\TextColumn::make('slug')->color('gray'),
            \Filament\Tables\Columns\TextColumn::make('roles.name')
                ->label('Hak Akses Role')
                ->badge()
                ->color('success'),
        ])
        ->actions([
            EditAction::make(),
            DeleteAction::make(),
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
            'index' => ListFileCategories::route('/'),
            'create' => CreateFileCategory::route('/create'),
            'view' => ViewFileCategory::route('/{record}'),
            'edit' => EditFileCategory::route('/{record}/edit'),
        ];
    }
}
