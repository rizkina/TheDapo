<?php

namespace App\Filament\Resources\Sekolahs\Pages;

use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Jobs\SyncSekolahJob;
use App\Services\DapodikService;
use App\Models\DapodikConf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSekolahs extends ListRecords
{
    protected static string $resource = SekolahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncSekolah')
                ->label('Tarik Data Sekolah')
                ->icon('heroicon-o-arrow-path')
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
                    // 1. Cek apakah ada konfigurasi yang aktif di database
                    $config = DapodikConf::where('is_active', true)->first();

                    if (!$config) {
                        Notification::make()
                            ->title('Gagal: Konfigurasi Tidak Ada')
                            ->body('Silakan buat dan aktifkan konfigurasi Dapodik terlebih dahulu di menu Settings.')
                            ->danger()
                            ->persistent()
                            ->send();
                        return; // Hentikan proses
                    }

                    // 2. Cek Koneksi secara real-time (Ping)
                    $test = $service->testConnection(
                        $config->base_url, 
                        $config->token, 
                        $config->npsn
                    );

                    if (!$test['success']) {
                        Notification::make()
                            ->title('Gagal: Koneksi Terputus')
                            ->body('Tidak dapat menjangkau server Dapodik. Pastikan aplikasi Dapodik lokal sedang terbuka dan IP diizinkan. Detail: ' . $test['message'])
                            ->danger()
                            ->persistent()
                            ->send();
                        return; // Hentikan proses
                    }

                    // 3. Jika lolos semua cek, jalankan Job
                    // Karena data sekolah hanya 1 record, gunakan dispatchSync agar instan
                    try {
                        SyncSekolahJob::dispatchSync();

                        Notification::make()
                            ->title('Berhasil!')
                            ->body('Data profil sekolah berhasil diperbarui dari Dapodik.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Terjadi Kesalahan Sistem')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}