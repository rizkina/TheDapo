<?php

namespace App\Filament\Resources\Rombels\Pages;

use App\Filament\Resources\Rombels\RombelResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Notifications\Notification;
use App\Jobs\SyncRombelJob;
use App\Models\DapodikConf;
use App\Services\DapodikService;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\Rombels\Widgets\RombelStats;


class ListRombels extends ListRecords
{
    protected static string $resource = RombelResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            RombelStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        // return [
        //     CreateAction::make(),
        // ];
        return [
            Actions\Action::make('generateDataRombel')
                ->label('Generate Data Rombel')
                ->icon('heroicon-o-document-text')
                ->visible(fn () => Auth::user()->hasAnyRole('super_admin', 'admin', 'operator'))
                ->color('primary')
                ->color(fn () => DapodikConf::where('is_active', true)->exists() ? 'success' : 'gray')
                ->disabled(fn () => !DapodikConf::where('is_active', true)->exists())
                ->requiresConfirmation()
                ->modalHeading('Generate Data Rombel?')
                ->modalDescription('Sistem akan membuatkan data rombel secara menyeluruh.')
                ->action(function (DapodikService $service) {
                    // Cek Koneksi real-time
                    $config = DapodikConf::where('is_active', true)->first();
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

                    // Masukkan ke antrean Redis
                    // SyncRombelJob::dispatch();
                    SyncRombelJob::dispatch(Auth::id());

                    // Jalankan Worker otomatis (Windows trick)
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        pclose(popen("start /B php " . base_path('artisan') . " queue:work redis --stop-when-empty", "r"));
                    }

                    Notification::make()
                        ->title('Proses Dimulai')
                        ->body('Data rombel sedang dibuat di latar belakang. Silakan tunggu beberapa saat.')
                        ->info()
                        ->send();
                }),
            // Actions\CreateAction::make(),
        ];
    }

    // public function getSubheading(): ?string
    // {
    //     $lastSync = \App\Models\DapodikConf::where('is_active', true)->first()?->last_sync_at;
        
    //     return $lastSync 
    //         ? "Data terakhir diperbarui: " . $lastSync->diffForHumans() 
    //         : "Data belum pernah disinkronkan.";
    // }

    
}
