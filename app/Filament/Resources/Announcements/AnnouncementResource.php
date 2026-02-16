<?php

namespace App\Filament\Resources\Announcements;

use App\Filament\Resources\Announcements\Pages\CreateAnnouncement;
use App\Filament\Resources\Announcements\Pages\EditAnnouncement;
use App\Filament\Resources\Announcements\Pages\ListAnnouncements;
use App\Filament\Resources\Announcements\Pages\ViewAnnouncement;
use App\Filament\Resources\Announcements\Schemas\AnnouncementForm;
use App\Filament\Resources\Announcements\Schemas\AnnouncementInfolist;
use App\Filament\Resources\Announcements\Tables\AnnouncementsTable;
use App\Models\Announcement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::SpeakerWave;

    protected static ?string $navigationLabel = 'Pengumuman';

    protected static ?string $pluralModelLabel = 'Pengumuman';

    protected static ?string $recordTitleAttribute = 'judul';

    // public static function form(Schema $schema): Schema
    // {
    //     return AnnouncementForm::configure($schema);
    // }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return AnnouncementInfolist::configure($schema);
    // }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEKSI 1: ISI PENGUMUMAN
                Section::make('Isi Pengumuman')
                    ->description('Tuliskan pesan yang ingin disampaikan kepada pengguna.')
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul Pengumuman')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Jadwal Ujian Akhir Semester'),

                        Select::make('tipe')
                            ->label('Kategori / Warna Visual')
                            ->options([
                                'info' => 'Biasa (Biru)',
                                'success' => 'Umum (Hijau)',
                                'warning' => 'Penting (Kuning)',
                                'danger' => 'Sangat Penting (Merah)',
                            ])
                            ->default('info')
                            ->required()
                            ->native(false), // Tampilan dropdown lebih modern

                        RichEditor::make('konten')
                            ->label('Isi Pesan')
                            ->required()
                            ->columnSpanFull() // Memenuhi lebar section
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'redo',
                                'undo',
                            ]),
                    ]),

                // SEKSI 2: PENGATURAN TARGET & WAKTU
                Section::make('Target & Penjadwalan')
                    ->description('Tentukan siapa yang bisa melihat dan kapan pengumuman ini berakhir.')
                    ->schema([
                        Select::make('roles')
                            ->label('Tujukan Kepada Role')
                            ->relationship('roles', 'name') // Mengambil data dari Spatie Roles
                            ->multiple() // Bisa pilih lebih dari satu role
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Pilih satu atau lebih role yang berhak melihat pengumuman ini.'),

                        DateTimePicker::make('expires_at')
                            ->label('Berlaku Sampai')
                            ->placeholder('Selamanya')
                            ->helperText('Setelah tanggal ini, pengumuman otomatis hilang dari dashboard.'),

                        Toggle::make('is_active')
                            ->label('Terbitkan Pengumuman')
                            ->helperText('Jika dimatikan, pengumuman tidak akan tampil meskipun belum expired.')
                            ->default(true)
                            ->onColor('success'),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pengumuman')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('judul')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->columnSpan(2),
                                
                                TextEntry::make('tipe')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'info' => 'info',
                                        'warning' => 'warning',
                                        'danger' => 'danger',
                                        'success' => 'success',
                                        default => 'gray',
                                    }),
                            ]),

                        // Menampilkan isi pengumuman sebagai HTML agar format Rich Editor terlihat
                        TextEntry::make('konten')
                            ->label('Isi Pesan')
                            ->html() 
                            ->columnSpanFull()
                            ->prose(), // Memberikan styling typografi yang rapi
                    ]),

                Section::make('Target & Metadata')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('roles.name')
                                    ->label('Ditujukan Kepada')
                                    ->badge()
                                    ->color('primary'),
                                
                                TextEntry::make('expires_at')
                                    ->label('Berlaku Sampai')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('Selamanya'),

                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y H:i'),
                            ]),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
         return $table
            ->columns([
                TextColumn::make('judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(), // Judul panjang akan turun ke bawah, tidak memotong tabel

                TextColumn::make('tipe')
                    ->badge()
                    ->color(fn (string $state): string => $state), // Menggunakan warna sesuai isi kolom

                TextColumn::make('roles.name')
                    ->label('Target Role')
                    ->badge()
                    ->separator(','),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                TextColumn::make('expires_at')
                    ->label('Kadaluarsa')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Selamanya')
                    ->sortable(),
            ])
            ->filters([

            ])
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
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'view' => ViewAnnouncement::route('/{record}'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\Dapodik_User $user */
        $user = Auth::user();

        // Menu hanya muncul jika user adalah super_admin, admin, atau operator
        return $user->hasAnyRole(['super_admin', 'admin', 'operator']);
    }
}
