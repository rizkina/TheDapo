<?php

namespace App\Filament\Resources\Sekolahs\Pages;

use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Jobs\SyncSekolahJob;
use App\Services\DapodikService;
use App\Models\DapodikConf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListSekolahs extends ListRecords
{
    protected static string $resource = SekolahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncSekolah')
                ->label('Tarik Data Sekolah')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn () => Auth::user()->hasAnyRole('super_admin', 'admin', 'operator'))
                ->color(fn () => DapodikConf::where('is_active', true)->exists() ? 'success' : 'gray')
                ->disabled(function () {
                    return !DapodikConf::where('is_active', true)->exists();
                    })
                // Menambahkan keterangan kenapa tombol mati
                ->tooltip(function () {
                    if (!DapodikConf::where('is_active', true)->exists()) {
                        return 'Aktifkan konfigurasi Dapodik terlebih dahulu untuk menggunakan fitur ini.';
                    }
                    return null;
                    })
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Sinkronisasi')
                ->modalDescription('Sistem akan memastikan koneksi ke server Dapodik aktif sebelum menarik data.')
                ->action(function (DapodikService $service) {

                    $config = DapodikConf::where('is_active', true)->first();
                    
                    // Test koneksi dulu
                    $test = $service->testConnection($config->base_url, $config->token, $config->npsn);
                    if (!$test['success']) {
                        Notification::make()->title('Koneksi Gagal')->danger()->send();
                        return;
                    }

                    // Jalankan di background
                    SyncSekolahJob::dispatchSync(Auth::id()); 
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        // start /B menjalankan perintah di background tanpa membuka jendela CMD baru
                        // --stop-when-empty membuat worker mati otomatis jika antrean sudah bersih (hemat RAM)
                        pclose(popen("start /B php " . base_path('artisan') . " queue:work redis --stop-when-empty", "r"));
                    }

                    // Beri notifikasi ke database bahwa proses sudah dikirim ke antrean
                    Notification::make()
                        ->title('Sinkronisasi Dimulai')
                        ->body('Data sedang diproses oleh Redis. Cek lonceng notifikasi sebentar lagi.')
                        ->info()
                        ->sendToDatabase(Auth::user());
                })
        ];
    }
}