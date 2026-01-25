<?php

namespace App\Filament\Resources\Siswas\Widgets;

use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiswaStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Siswa', Siswa::count())
                ->description('Jumlah siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Siswa Laki-laki', Siswa::where('jenis_kelamin', 'L')->count())
                ->description('Total L')
                ->color('success'),

            Stat::make('Siswa Perempuan', Siswa::where('jenis_kelamin', 'P')->count())
                ->description('Total P')
                ->color('warning'),
        ];
    }
}