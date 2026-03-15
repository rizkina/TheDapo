<?php

namespace App\Filament\Resources\Ptks\Pages;

use App\Filament\Resources\Ptks\PtkResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\DapodikConf;
use App\Services\DapodikService;
use App\Jobs\SyncPtkJob;;
use Filament\Notifications\Notification;
use App\Models\Ptk;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Ptks\Widgets\PtkStats;
use Illuminate\Support\Facades\Auth;

class ListPtks extends ListRecords
{
    protected static string $resource = PtkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
            Action::make('syncPtk')
                ->label('Tarik Data PTK')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn () => Auth::user()->hasAnyRole('super_admin', 'admin', 'operator'))
                ->color(fn () => DapodikConf::where('is_active', true)->exists() ? 'success' : 'gray')
                ->disabled(fn () => !DapodikConf::where('is_active', true)->exists())
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Data GTK Massal')
                ->modalDescription('Aplikasi akan menghubungi server Dapodik. Jika koneksi berhasil, data akan diproses di latar belakang (Redis).')
                ->action(function (DapodikService $service) {
                        $config = DapodikConf::where('is_active', true)->first();

                        // 1. Safety Guard: Cek Koneksi Real-time sebelum memasukkan ke antrean
                        $test = $service->testConnection($config->base_url, $config->token, $config->npsn);

                        if (!$test['success']) {
                            Notification::make()->title('Koneksi Gagal')->danger()->send();
                            return;
                        }

                        // 2. Kirim Job ke Redis
                        SyncPtkJob::dispatch(Auth::id());

                        // 3. Auto-Worker untuk Windows/Laragon
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            pclose(popen("start /B php " . base_path('artisan') . " queue:work redis --stop-when-empty", "r"));
                        }
                        Notification::make()
                            ->title('Sinkronisasi Dimulai')
                            ->body('Data GTK sedang ditarik. Silakan refresh halaman ini dalam beberapa menit.')
                            ->info()
                            ->sendToDatabase(Auth::user());
                }),
        ];
                    
    }

    public function getTabs(): array
    {
        return [
            'aktif' => 
                Tab::make('PTK Aktif')
                    ->modifyQueryUsing(fn (Builder $query) => $query->withoutTrashed())
                    ->badge(\App\Models\Ptk::query()->count())
                    ->badgeColor('success'),

            'keluar' => 
                Tab::make('PTK Keluar / Non-Aktif')
                    ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed())
                    ->badge(\App\Models\Ptk::query()->onlyTrashed()->count())
                    ->badgeColor('danger'),

            'semua' => 
                Tab::make('Semua Data')
                    ->modifyQueryUsing(fn (Builder $query) => $query->withTrashed()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PtkStats::class,
        ];
    }
}
