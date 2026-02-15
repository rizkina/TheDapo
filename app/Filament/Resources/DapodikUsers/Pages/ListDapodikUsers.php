<?php

namespace App\Filament\Resources\DapodikUsers\Pages;

use App\Filament\Resources\DapodikUsers\DapodikUserResource;
use App\Jobs\GenerateDapodikUsersJob;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Models\DapodikConf;
use App\Services\DapodikService;
use Illuminate\Support\Facades\Auth;

class ListDapodikUsers extends ListRecords
{
    protected static string $resource = DapodikUserResource::class;

    protected function getHeaderActions(): array
    {
        // return [
        //     CreateAction::make(),
        // ];
        return [
            Actions\Action::make('generateAccounts')
                ->label('Generate Akun Massal')
                ->icon('heroicon-o-user-plus')
                ->visible(fn () => Auth::user()->hasAnyRole('super_admin', 'admin', 'operator'))
                ->color('warning')
                ->requiresConfirmation()
                ->color(fn () => DapodikConf::where('is_active', true)->exists() ? 'success' : 'gray')
                ->disabled(fn () => !DapodikConf::where('is_active', true)->exists())
                ->modalHeading('Buat Akun untuk Semua Guru & Siswa?')
                ->modalDescription('Sistem akan membuatkan Username & Password otomatis bagi yang belum memiliki akun. Username GTK = NIP/NUPTK, Username Siswa = NISN.')
                ->action(function (DapodikService $service) {
                    // Cek Koneksi real-time
                    $config = DapodikConf::where('is_active', true)->first();
                    $test = $service->testConnection($config->base_url, $config->token, $config->npsn);
                    if (!$test['success']) {
                        Notification::make()->title('Koneksi Gagal')->danger()->send();
                        return;
                    }
                    // Masukkan ke antrean Redis
                    GenerateDapodikUsersJob::dispatch(Auth::id());

                    // Jalankan Worker otomatis (Windows trick)
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        pclose(popen("start /B php " . base_path('artisan') . " queue:work redis --stop-when-empty", "r"));
                    }

                    Notification::make()
                        ->title('Proses Dimulai')
                        ->body('Akun sedang dibuat di latar belakang. Silakan tunggu beberapa saat.')
                        ->info()
                        ->sendToDatabase(Auth::user());
                }),
            Actions\CreateAction::make(),
        ];
    }
}
