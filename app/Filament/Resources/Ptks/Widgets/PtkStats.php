<?php

namespace App\Filament\Resources\Ptks\Widgets;

use App\Models\Ptk;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PtkStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total GTK', Ptk::count())
                ->description('Jumlah GTK terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Kepala Sekolah', Ptk::where('jenis_ptk_id_str', 'Kepala Sekolah')->count())
                ->description('Total Kepala Sekolah')
                ->color('info'),

            Stat::make('Guru', Ptk::where('jenis_ptk_id_str', 'Guru')->count())
                ->description('Total Guru')
                ->color('info'),
            Stat::make('Tenaga Kependidikan', Ptk::where('jenis_ptk_id_str', 'Tenaga Kependidikan')->count())
                ->description('Total Tenaga Kependidikan') 
                ->color('info'),
        ];
    }
}