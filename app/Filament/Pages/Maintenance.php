<?php

namespace App\Filament\Pages;

use App\Models\Dapodik_User;
use App\Services\DapodikService;
use App\Services\GoogleDriveService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;
use BackedEnum;

class Maintenance extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::WrenchScrewdriver;
    protected static string | UnitEnum | null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Maintenance';
    protected static ?string $pluralModelLabel = 'Maintenance';
    protected static ?int $navigationSort = 100;
    protected string $view = 'filament.pages.maintenance';

    // Otorisasi: Hanya Super Admin yang bisa melihat menu ini
    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('super_admin');
    }

    /**
     * Fitur 1: Backup Database
     */
    public function backupAction(): Action
    {
        return Action::make('backup')
            ->label('Backup Database')
            ->icon('heroicon-o-cloud-arrow-down')
            ->color('info')
            ->requiresConfirmation()
            ->action(function () {
                try {
                    Artisan::call('backup:clean');
                    Artisan::call('backup:run', ['--only-db' => true]);

                    /** @var FilesystemAdapter $disk */
                    $disk = Storage::disk('local');
                    $appName = config('app.name');
                    $files = $disk->allFiles($appName);

                    if (count($files) > 0) {
                        $latestFile = collect($files)
                            ->sortByDesc(fn ($file) => $disk->lastModified($file))
                            ->first();

                        // 1. Tentukan Format Nama Baru
                        // Hasil: backup-TheDapo-20-02-2026-1430.zip
                        $newFileName = 'backup-' . str(config('app.name'))->slug() . '-' . now()->format('d-m-Y-Hi') . '.zip';

                        Notification::make()->title('Backup Berhasil')->success()->send();

                        // 2. Berikan parameter kedua pada fungsi download()
                        return $disk->download($latestFile, $newFileName);
                    }

                    throw new \Exception("File backup tidak ditemukan.");
                } catch (\Exception $e) {
                    Notification::make()->title('Gagal')->body($e->getMessage())->danger()->send();
                }
            });
    }

    // --- FITUR 2: WIPE DATABASE SAJA (Aman) ---
    public function wipeDatabaseAction(): Action
    {
        return Action::make('wipeDatabase')
            ->label('Hapus Database Saja')
            ->icon('heroicon-o-circle-stack')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Kosongkan Tabel Database?')
            ->modalDescription('Tindakan ini menghapus data di tabel (Siswa, PTK, Rombel, dll) tetapi tetap MEMPERTAHANKAN file di Google Drive dan Foto Profil.')
            ->schema([
                \Filament\Forms\Components\TextInput::make('confirm')
                    ->label('Ketik "HAPUS DATA" untuk konfirmasi')
                    ->required()
                    ->rules(['in:HAPUS DATA']),
            ])
            ->action(fn() => $this->runReset(false));
    }

    // --- FITUR 3: NUCLEAR RESET (DB + FILES) ---
    public function nuclearResetAction(): Action
    {
        return Action::make('nuclearReset')
            ->label('Hapus Database & File Fisik')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Hapus Segalanya?')
            ->modalDescription('PERINGATAN KRITIS: Tindakan ini akan menghapus data di tabel DAN menghapus seluruh file fisik di Google Drive serta Foto Profil secara permanen!')
            ->schema([
                \Filament\Forms\Components\TextInput::make('confirm')
                    ->label('Ketik "HAPUS SEMUA" untuk konfirmasi')
                    ->required()
                    ->rules(['in:HAPUS SEMUA']),
            ])
            ->action(fn() => $this->runReset(true));
    }

    /**
     * Logic Inti Pembersihan
     */
    protected function runReset(bool $deletePhysicalFiles)
    {
        if (!Auth::user()->hasRole('super_admin')) { abort(403); }

        
        DB::transaction(function () use ($deletePhysicalFiles) {
            $protectedUserIds = Dapodik_User::where('username', 'admin')
                ->orWhere('id', Auth::id())
                ->pluck('id')
                ->toArray();
            if ($deletePhysicalFiles) {
                // Hapus File Lokal (Foto)
                Storage::disk('public')->deleteDirectory('foto-ptk');
                Storage::disk('public')->deleteDirectory('foto-siswa');
                Storage::disk('public')->makeDirectory('foto-ptk');
                Storage::disk('public')->makeDirectory('foto-siswa');

                // Hapus File Google Drive
                GoogleDriveService::applyConfig();
                $google = Storage::disk('google');
                foreach (['siswa', 'guru', 'kepsek', 'tenaga kependidikan'] as $folder) {
                    if ($google->exists($folder)) { $google->deleteDirectory($folder); }
                }
            }

            // Truncate Tabel (PostgreSQL)
            DB::statement('SET CONSTRAINTS ALL DEFERRED');
             $childTables = ['pembelajarans', 'anggota__rombels', 'notifications'];
            foreach ($childTables as $table) {
                DB::table($table)->truncate();
            }
            $tables = ['rombels', 'siswas', 'ptks', 'sekolahs', 'files', 'announcements'];
            foreach ($tables as $table) { DB::table($table)->delete(); }

            // Reset User kecuali admin utama dan user yang sedang login
            Dapodik_User::query()
                ->whereNotIn('id', $protectedUserIds)
                ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin']))
                // ->where('username', '!=', 'admin') 
                // ->where('id', '!=', Auth::id())   
                ->forceDelete();
            // Reset Metadata pada Admin yang tersisa
            // Pastikan admin tidak lagi terhubung ke ptk_id atau sekolah_id yang sudah dihapus
            Dapodik_User::whereIn('id', $protectedUserIds)->update([
                'ptk_id' => null,
                'peserta_didik_id' => null,
                'sekolah_id' => null,
            ]);
        });

        Notification::make()
            ->title($deletePhysicalFiles ? 'Pembersihan Total Berhasil' : 'Database Berhasil Dikosongkan')
            ->success()
            ->send();
    }

    /**
     * Mengirim data status ke View
     */
    protected function getViewData(): array
    {
        $drive = GoogleDriveService::testConnectivity();
        $dapoConfig = \App\Models\DapodikConf::where('is_active', true)->first();
        $dapo = $dapoConfig ? (new DapodikService())->testConnection($dapoConfig->base_url, $dapoConfig->token, $dapoConfig->npsn) : ['success' => false, 'message' => 'Belum dikonfigurasi'];

        return [
            'driveStatus' => $drive,
            'dapoStatus' => $dapo,
            'stats' => [
                'siswa' => \App\Models\Siswa::count(),
                'ptk' => \App\Models\Ptk::count(),
                'file' => \App\Models\File::count(),
                'rombel' => \APP\Models\Rombel::count(),
            ]
        ];
    }
}