<?php

namespace App\Filament\Resources\Files\Pages;

use App\Filament\Resources\Files\FileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\File;
use Illuminate\Support\Facades\Auth;

class ListFiles extends ListRecords
{
    protected static string $resource = FileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();
        $tabs = [];

        // 1. Tab: Milik Saya (Semua Role Punya Tab Ini)
        $tabs['milik_saya'] = Tab::make('Milik Saya')
            ->icon('heroicon-m-user')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', $user->id))
            ->badge(File::where('user_id', $user->id)->count())
            ->badgeColor('success');

        // 2. Tab Khusus untuk Guru (Wali Kelas)
        if ($user->hasRole('guru')) {
            $tabs['file_siswa'] = Tab::make('File Siswa (Wali Kelas)')
                ->icon('heroicon-m-academic-cap')
                // Logika: Ambil file yang user_id-nya bukan saya, tapi masuk dalam query scope (siswa di kelasnya)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', '!=', $user->id))
                ->badge(function() use ($user) {
                    // Menghitung jumlah file siswa di kelasnya saja
                    return File::where('user_id', '!=', $user->id)
                        ->whereHas('user.siswa.rombels', fn($q) => $q->where('ptk_id', $user->ptk_id))
                        ->count();
                })
                ->badgeColor('info');
        }

        // 3. Tab Khusus untuk Admin, Operator, Tendik, Kepsek
        if ($user->hasAnyRole(['super_admin', 'admin', 'operator', 'tenaga kependidikan', 'kepsek'])) {
            $tabs['file_orang_lain'] = Tab::make('File Orang Lain')
                ->icon('heroicon-m-users')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', '!=', $user->id))
                ->badge(File::where('user_id', '!=', $user->id)->count())
                ->badgeColor('warning');
            $tabs['semua_file'] = Tab::make('Semua File')
                ->icon('heroicon-m-globe-alt')
                // Admin/Tendik bisa melihat semuanya tanpa filter tambahan
                ->modifyQueryUsing(fn (Builder $query) => $query)
                ->badge(File::count())
                ->badgeColor('gray');
        }

        return $tabs;
    }
}
