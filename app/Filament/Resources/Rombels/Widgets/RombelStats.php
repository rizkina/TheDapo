<?php

namespace App\Filament\Resources\Rombels\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Rombel;
use Illuminate\Support\Facades\DB;

class RombelStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
            $stats = [];

            $stats[] = Stat::make('Total Rombel', \App\Models\Rombel::count())
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])
                ->color('primary');
            
            $tingkat = Rombel::query()
                ->select('tingkat_pendidikan_id', 'tingkat_pendidikan_id_str', 'jenis_rombel_str', DB::raw('count(*) as total'))
                ->whereNotNull('tingkat_pendidikan_id')
                ->groupBy('tingkat_pendidikan_id', 'tingkat_pendidikan_id_str', 'jenis_rombel_str')
                ->orderBy('tingkat_pendidikan_id', 'asc')
                ->get();

            $availableColors = ['info', 'success'];

            foreach ($tingkat as $index => $item) {
                $color = $availableColors[$index % count($availableColors)];
                $stats[] = Stat::make("{$item->tingkat_pendidikan_id_str}", $item->total)
                    ->description('Rombel '.$item->jenis_rombel_str)
                    ->chart([7, 3, 4, 5, 6, 3, 5, 8])
                    ->color($color); 
            }
            return $stats;  
    }
}
