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
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\File;
use ZipArchive;
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
                            ->sortByDesc(fn($file) => $disk->lastModified($file))
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

    public function restoreAction(): Action
    {
        return Action::make('restore')
            ->label('Restore Database')
            ->icon('heroicon-o-arrow-path')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Pulihkan Database?')
            ->modalDescription('PERINGATAN: Seluruh data saat ini akan dihapus dan diganti dengan data dari file backup. Pastikan file backup valid.')
            ->schema([
                FileUpload::make('backup_file')
                    ->label('Pilih File Backup (.zip)')
                    ->acceptedFileTypes([
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/octet-stream',
                        'application/x-compress',
                        'application/x-compressed',
                        'multipart/x-zip',
                    ])
                    ->disk('local')
                    ->directory('temp-restores')
                    ->required()
                    ->maxSize(102400),
            ])
            ->action(function (array $data) {
                try {
                    $disk = Storage::disk('local');
                    $zipFilePath = $disk->path($data['backup_file']);
                    $extractPath = storage_path('app/temp-restores');

                    // Pastikan folder bersih
                    if (File::exists($extractPath)) {
                        File::deleteDirectory($extractPath);
                    }
                    File::makeDirectory($extractPath, 0755, true);

                    // Ekstraksi
                    $zip = new \ZipArchive;
                    if ($zip->open($zipFilePath) !== TRUE) throw new \Exception("Gagal membuka ZIP.");
                    $zip->extractTo($extractPath);
                    $zip->close();

                    // Cari SQL
                    $sqlFile = collect(File::allFiles($extractPath))->first(fn($f) => $f->getExtension() === 'sql');
                    if (!$sqlFile) throw new \Exception("File SQL tidak ditemukan.");

                    // DB Config
                    $db = config('database.connections.pgsql');
                    putenv("PGPASSWORD=" . $db['password']);

                    // Ambil binary psql secara dinamis
                    $psql = $this->getPsqlPath('psql');

                    // Eksekusi Command (Universal dengan escapeshellarg)
                    $command = sprintf(
                        '%s -h %s -p %s -U %s -d %s -f %s',
                        escapeshellarg($psql),
                        escapeshellarg($db['host']),
                        escapeshellarg($db['port']),
                        escapeshellarg($db['username']),
                        escapeshellarg($db['database']),
                        escapeshellarg($sqlFile->getRealPath())
                    );

                    [$output, $resultCode] = $this->executeBinaryCommand($command);

                    // Bersihkan
                    File::deleteDirectory($extractPath);
                    $disk->delete($data['backup_file']);

                    if ($resultCode !== 0) throw new \Exception("Database Error: " . implode(" ", $output));

                    Notification::make()->title('Database Berhasil Dipulihkan')->success()->send();
                } catch (\Exception $e) {
                    Notification::make()->title('Restore Gagal')->body($e->getMessage())->danger()->persistent()->send();
                }
            })
            ->disabled(!$this->isPsqlAvailable())
            ->tooltip(!$this->isPsqlAvailable() ? 'psql tidak ditemukan' : null);
    }

    public function isPsqlAvailable(): bool
    {
        $psqlPath = env('PSQL_PATH', 'psql');

        // Perintah cek versi (berlaku di Windows & Linux)
        $command = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            ? "\"$psqlPath\" --version"
            : "which $psqlPath";

        exec($command, $output, $resultCode);

        return $resultCode === 0;
    }

    private function getPsqlPath($binary = 'psql'): string
    {
        // 1. Cek apakah ada path manual di .env (untuk Windows/Laragon)
        $envPath = ($binary === 'psql') ? env('PSQL_PATH') : env('PG_DUMP_PATH');
        if ($envPath) return $envPath;

        // 2. Jika di Linux/Docker, cukup panggil namanya langsung
        return $binary;
    }

    private function executeBinaryCommand($command): array
    {
        // Tambahkan 2>&1 agar error dari terminal tertangkap oleh PHP
        exec($command . ' 2>&1', $output, $resultCode);
        return [$output, $resultCode];
    }

    /**
     * Logic Inti Pembersihan
     */
    protected function runReset(bool $deletePhysicalFiles)
    {
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403);
        }


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
                    if ($google->exists($folder)) {
                        $google->deleteDirectory($folder);
                    }
                }
            }

            // Truncate Tabel (PostgreSQL)
            DB::statement('SET CONSTRAINTS ALL DEFERRED');
            $childTables = ['pembelajarans', 'anggota__rombels', 'notifications'];
            foreach ($childTables as $table) {
                DB::table($table)->truncate();
            }
            $tables = ['rombels', 'siswas', 'ptks', 'sekolahs', 'files', 'announcements'];
            foreach ($tables as $table) {
                DB::table($table)->delete();
            }

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

        // Ambil info backup terakhir
        $disk = Storage::disk('local');
        $appName = config('app.name');
        $files = $disk->exists($appName) ? $disk->allFiles($appName) : [];
        $lastBackupDate = count($files) > 0
            ? date('d M Y H:i', $disk->lastModified(collect($files)->last()))
            : 'Belum ada';

        return [
            'driveStatus' => $drive,
            'dapoStatus' => $dapo,
            'isPsqlReady' => $this->isPsqlAvailable(),
            'lastBackup' => $lastBackupDate,
            'stats' => [
                'siswa' => \App\Models\Siswa::count(),
                'ptk' => \App\Models\Ptk::count(),
                'file' => \App\Models\File::count(),
                'rombel' => \App\Models\Rombel::count(),
            ]
        ];
    }
}
