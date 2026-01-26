<?php

namespace App\Filament\Resources\Siswas\Pages;

use App\Filament\Resources\Siswas\SiswaResource;
use App\Filament\Resources\Siswas\Widgets\SiswaStats; 
use App\Jobs\SyncSiswaJob;
use App\Models\DapodikConf;
use App\Services\DapodikService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder; 
use Filament\Schemas\Components\Tabs\Tab;
// use App\Filament\Resources\Siswas\Widgets\SiswaStats;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncSiswa')
                ->label('Tarik Data Siswa')
                ->icon('heroicon-o-arrow-path')
                ->color(fn () => DapodikConf::where('is_active', true)->exists() ? 'success' : 'gray')
                ->disabled(fn () => !DapodikConf::where('is_active', true)->exists())
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Siswa Massal')
                ->modalDescription('Proses ini akan menarik data siswa ke antrean Redis. Anda bisa menutup halaman ini sementara proses berjalan.')
                ->action(function (DapodikService $service) {
                    $config = DapodikConf::where('is_active', true)->first();

                    // Cek Koneksi real-time
                    $test = $service->testConnection($config->base_url, $config->token, $config->npsn);

                    if (!$test['success']) {
                        Notification::make()
                            ->title('Gagal: Koneksi Terputus')
                            ->body($test['message'])
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    // Kirim ke Queue
                    SyncSiswaJob::dispatch();

                    // Memicu Worker secara otomatis di Windows (Background)
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        // Perintah start /B menjalankan artisan di background tanpa jendela cmd baru
                        pclose(popen("start /B php " . base_path('artisan') . " queue:work redis --stop-when-empty", "r"));
                    }

                    Notification::make()
                        ->title('Antrean Dimulai')
                        ->body('Data siswa sedang diproses oleh Aplikasi. Silakan refresh halaman ini beberapa saat lagi.')
                        ->info()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'aktif' => 
                Tab::make('Siswa Aktif')
                    ->modifyQueryUsing(fn (Builder $query) => $query->withoutTrashed())
                    ->badge(\App\Models\Siswa::query()->count())
                    ->badgeColor('success'),

            'keluar' => 
                Tab::make('Siswa Keluar / Alumni')
                    ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed())
                    ->badge(\App\Models\Siswa::query()->onlyTrashed()->count())
                    ->badgeColor('danger'),

            'semua' => 
                Tab::make('Semua Data')
                    ->modifyQueryUsing(fn (Builder $query) => $query->withTrashed()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SiswaStats::class,
        ];
    }

    
}