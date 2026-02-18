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
                    Artisan::call('backup:run', ['--only-db' => true]);

                    /** @var FilesystemAdapter $disk */
                    $disk = Storage::disk('local');
                    $appName = config('app.name');
                    $files = $disk->allFiles($appName);

                    if (count($files) > 0) {
                        $latestFile = collect($files)
                            ->sortByDesc(fn ($file) => $disk->lastModified($file))
                            ->first();

                        Notification::make()->title('Backup Berhasil')->success()->send();

                        // SEKARANG TIDAK AKAN MERAH LAGI
                        return $disk->download($latestFile);
                    }

                    throw new \Exception("File backup tidak ditemukan.");
                } catch (\Exception $e) {
                    Notification::make()->title('Gagal')->body($e->getMessage())->danger()->send();
                }
            });
    }

    /**
     * Fitur 2: Reset / Truncate Database & File (SAFETY MODE)
     */
    public function resetDatabaseAction(): Action
    {
        return Action::make('resetDatabase')
            ->label('Wipe / Reset Data')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            
            // PERBAIKAN: Gunakan schema() bukan form() untuk menghilangkan warning deprecated
            ->schema([
                \Filament\Forms\Components\TextInput::make('confirm')
                    ->label('Konfirmasi Teks')
                    ->placeholder('Ketik: HAPUS SEMUA')
                    ->required()
                    ->rules(['in:HAPUS SEMUA']),
            ])
            
            ->action(function () {
                // ... logika reset database Anda tetap sama ...
                Notification::make()->title('Sistem Berhasil Dibersihkan')->danger()->send();
            });
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