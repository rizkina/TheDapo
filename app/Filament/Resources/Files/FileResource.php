<?php

namespace App\Filament\Resources\Files;

use App\Filament\Resources\Files\Pages\CreateFile;
use App\Filament\Resources\Files\Pages\EditFile;
use App\Filament\Resources\Files\Pages\ListFiles;
use App\Filament\Resources\Files\Pages\ViewFile;
use App\Filament\Resources\Files\Schemas\FileInfolist;
use App\Models\File;
use App\Models\FileCategory;
use App\Services\GoogleDriveService;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;


class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentCheck;
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen File';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'File Arsip';
    protected static ?string $pluralModelLabel = 'File Arsip';
    protected static ?string $modelLabel = 'File Arsip';


    protected static ?string $recordTitleAttribute = 'file_name';

    public static function form(Schema $schema): Schema
    {
        GoogleDriveService::applyConfig();

        // return FileForm::configure($schema);
         return $schema->components([
            \Filament\Schemas\Components\Section::make('Arsip Dokumen')
                ->schema([
                    Select::make('file_category_id')
                        ->label('Kategori Dokumen')
                        ->options(function() {
                            $user = Auth::user();
                            if ($user->hasAnyRole(['super_admin', 'admin'])) return FileCategory::pluck('nama', 'id');
                            return FileCategory::whereHas('roles', fn($q) => $q->whereIn('name', $user->getRoleNames()))->pluck('nama', 'id');
                        })
                        ->reactive()
                        ->required(),

                    FileUpload::make('file_path')
                        ->label('Unggah PDF')
                        ->disk('google') // Pakai driver Google Drive
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(2048) // 2 MB
                        ->helperText('Ukuran file maksimal adalah 2 MB dalam format PDF.') 
                        ->validationMessages([
                                'max' => 'Ukuran file terlalu besar. Maksimal diperbolehkan adalah 2 MB.',
                            ])
                        ->directory(function ($get) {
                            $user = Auth::user();
                            $category = FileCategory::find($get('file_category_id'));
                            $roleDir = $user->getRoleNames()->first() ?? 'Umum';
                            $catDir = $category ? $category->nama : 'Lainnya';
                            return "{$roleDir}/{$catDir}"; // Otomatis buat folder di Drive
                        })
                        ->getUploadedFileNameForStorageUsing(function ($file, $get) {
                            $category = FileCategory::find($get('file_category_id'));
                            $prefix = $category ? $category->nama : 'FILE';
                            return "{$prefix}_" . Auth::user()->username . ".pdf"; // Nama file standar
                        })
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                $set('original_name', $state->getClientOriginalName());
                                $set('mime_type', $state->getMimeType());
                                $set('size', $state->getSize());
                            }
                        }),

                    TextInput::make('file_name')
                        ->label('Keterangan Singkat')
                        ->placeholder('Misal: KTP Asli / Ijazah SMP')
                        ->required(),

                    // Simpan data otomatis di background
                    Hidden::make('user_id')->default(Auth::id()),
                    Hidden::make('original_name'),
                    Hidden::make('mime_type'),
                    Hidden::make('size'),
                ])->columns(2)
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.nama')->label('Kategori')->badge()->color('info')->sortable(),
                TextColumn::make('file_name')->label('Keterangan')->searchable()->sortable()
                    ->description(fn (File $record) => "File asli: {$record->original_name}"),

                TextColumn::make('user.nama')->label('Pemilik')
                    ->searchable()
                    ->sortable()
                    // ->description(fn (File $record) => "{$record->user->username}"),
                    ->visible(fn () => Auth::user()->hasAnyRole(['super_admin', 'admin', 'operator', 'guru', 'tenaga kependidikan', 'kepsek'])),
                TextColumn::make('user.roles.name')
                    ->label('Peran')
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->searchable()
                    ->visible(fn () => Auth::user()->hasAnyRole(['super_admin', 'admin', 'operator', 'guru', 'tenaga kependidikan', 'kepsek'])),
                TextColumn::make('size')->label('Ukuran')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB')
                    ->color('gray'),
                TextColumn::make('created_at')->label('Tgl Unggah')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->headerActions([
                // FITUR EKSPOR PDF (SEMUA)
                Action::make('export_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->action(function () {
                        $records = static::getEloquentQuery()->get();
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.files', ['files' => $records]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'daftar-arsip.pdf');
                    }),
            ])
            ->filters([
                SelectFilter::make('file_category_id')->label('Kategori')->relationship('category', 'nama'),
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('preview')
                        ->label('Lihat PDF')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->url(fn (File $record) => route('file.preview', $record))
                        ->openUrlInNewTab(),
                    Action::make('download')
                        ->label('Unduh')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->action(function (File $record) {
                            GoogleDriveService::applyConfig();
                            /** 
                             * Beritahu Intelephense bahwa ini adalah FilesystemAdapter 
                             * @var \Illuminate\Filesystem\FilesystemAdapter $disk 
                             */
                            $disk = \Illuminate\Support\Facades\Storage::disk('google');
                            if (!$disk->exists($record->file_path)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('File tidak ditemukan di Google Drive.')
                                    ->body('Mungkin file telah dihapus atau dipindahkan secara manual.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            return $disk->download($record->file_path, $record->original_name);
                        }),
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
            'index' => ListFiles::route('/'),
            'create' => CreateFile::route('/create'),
            'view' => ViewFile::route('/{record}'),
            'edit' => EditFile::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['category', 'user.roles'])->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
        $user = Auth::user();
        if ($user->hasAnyRole(['super_admin', 'admin', 'operator'])) {
            return $query;
        }
        return $query->where(function (Builder $q) use ($user) {
            $q->where('user_id', $user->id);

            if ($user->hasAnyRole(['tenaga kependidikan', 'kepsek'])) {
              $q->orWhereHas('user', function ($subQ) {
                    $subQ->whereHas('roles', function ($roleQ) {
                        $roleQ->whereIn('name', ['guru', 'siswa', 'tenaga kependidikan', 'kepsek']);
                    });
              });
        }

            if  ($user->hasRole('guru') && $user->ptk_id) {
                $q->orWhereHas('user.siswa.rombels', function ($rombelQ) use ($user) {
                    $rombelQ->where('ptk_id', $user->ptk_id);
                });
            }
        });
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
