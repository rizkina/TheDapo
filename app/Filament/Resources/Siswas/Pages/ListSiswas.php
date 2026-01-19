<?php

namespace App\Filament\Resources\Siswas\Pages;

use App\Filament\Resources\Siswas\SiswaResource;
use App\Jobs\SyncSiswaJob;
use App\Models\DapodikConf;
use App\Services\DapodikService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncSiswa')
                ->label('Tarik Data Siswa')
                ->icon('heroicon-o-arrow-path')
                // Warna abu-abu jika config tidak ada
                ->color(fn () => DapodikConf::where('is_active', true)->exists() ? 'success' : 'gray')
                ->disabled(fn () => !DapodikConf::where('is_active', true)->exists())
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Siswa Massal')
                ->modalDescription('Data ribuan siswa akan ditarik di background menggunakan Redis. Pastikan koneksi ke server Dapodik aktif.')
                ->action(function (DapodikService $service) {
                    $config = DapodikConf::where('is_active', true)->first();

                    // Cek Koneksi real-time sebelum antrekan job
                    $test = $service->testConnection($config->base_url, $config->token, $config->npsn);

                    if (!$test['success']) {
                        Notification::make()
                            ->title('Koneksi Gagal')
                            ->body($test['message'])
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    // Jalankan Job ke Queue Redis
                    SyncSiswaJob::dispatch();

                    // Jalankan Worker otomatis untuk Windows (Laragon)
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        pclose(popen("start /B php " . base_path('artisan') . " queue:work redis --stop-when-empty", "r"));
                    }

                    Notification::make()
                        ->title('Sinkronisasi Dimulai')
                        ->body('Data siswa sedang diproses di latar belakang. Silakan refresh halaman ini beberapa saat lagi.')
                        ->info()
                        ->send();
                }),
        ];
    }
}