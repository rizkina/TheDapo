<?php

namespace App\Filament\Resources\DapodikUsers;

use App\Filament\Resources\DapodikUsers\Pages\CreateDapodikUser;
use App\Filament\Resources\DapodikUsers\Pages\EditDapodikUser;
use App\Filament\Resources\DapodikUsers\Pages\ListDapodikUsers;
use App\Filament\Resources\DapodikUsers\Pages\ViewDapodikUser;
use App\Filament\Resources\DapodikUsers\Schemas\DapodikUserForm;
use App\Filament\Resources\DapodikUsers\Schemas\DapodikUserInfolist;
use App\Filament\Resources\DapodikUsers\Tables\DapodikUsersTable;
use App\Models\Dapodik_User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\DeleteAction;;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use UnitEnum;

class DapodikUserResource extends Resource
{
    protected static ?string $model = Dapodik_User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Pengguna';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Pengguna Dapodik';
    protected static ?string $pluralModelLabel = 'Pengguna Dapodik';
    protected static ?string $modelLabel = 'Pengguna Dapodik';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getGloballySearchableAttributes(): array
    {
        return ['username', 'nama'];
    }
    // return DapodikUserForm::configure($schema);
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->description('Kelola data autentikasi dan peran akses pengguna.')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username (NIP/NISN)')
                            ->required()
                            ->unique(ignoreRecord: true) // Mencegah username ganda
                            ->maxLength(255),

                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Select::make('peran_id_str')
                            ->label('Peran Utama (Dapodik)')
                            ->options([
                                'admin' => 'Admin / Operator',
                                'kepala sekolah' => 'Kepala Sekolah',
                                'guru' => 'Guru',
                                'tenaga kependidikan' => 'Tenaga Kependidikan (Staf)',
                                'siswa' => 'Siswa',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText('Sistem akan menyamakan Role akses secara otomatis berdasarkan pilihan ini.'),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->revealable()
                            // Password di-hash otomatis oleh Model Casts yang sudah kita buat
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn ($context) => $context === 'create')
                            ->minLength(8),

                        Select::make('roles')
                            ->label('Role Akses (Spatie)')
                            ->relationship('roles', 'name', function (Builder $query) {
                                // Mencegah pemberian role super_admin secara sembarangan
                                return $query->where('name', '!=', 'super_admin');
                            })
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Role ini akan terisi otomatis saat Anda menyimpan Peran Utama di atas.')
                    ])->columns(2),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DapodikUserInfolist::configure($schema);
    }

    // return DapodikUsersTable::configure($table);
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('peran_id_str')
                    ->label('Tipe User')
                    ->badge()
                    ->searchable()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('roles.name')
                    ->label('Role Aktif')
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->toggleable(),

                TextColumn::make('id')
                    ->label('UUID User')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ptk_id')
                    ->label('ID PTK')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
            //     TrashedFilter::make(),
            // ])
            ->filters([
                // 1. Filter Berdasarkan Role Spatie
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Role Akses')
                    ->multiple() // Bisa pilih lebih dari satu role sekaligus
                    ->preload(),

                // 2. Filter Berdasarkan Tipe Peran (String Dapodik)
                SelectFilter::make('peran_id_str')
                    ->label('Tipe Pengguna')
                    ->options([
                        'Guru' => 'Guru',
                        'Tenaga Kependidikan' => 'Staf',
                        'Siswa' => 'Siswa',
                        'Kepala Sekolah' => 'Kepsek',
                    ]),

                // 3. Filter Soft Deletes (Siswa/Guru yang keluar)
                TrashedFilter::make(),
                ], layout:FiltersLayout::AboveContent)

            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
            'index' => ListDapodikUsers::route('/'),
            'create' => CreateDapodikUser::route('/create'),
            'view' => ViewDapodikUser::route('/{record}'),
            'edit' => EditDapodikUser::route('/{record}/edit'),
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

    // Opsional: Memberi warna pada badge
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : 'gray';
    }

}
