<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class AnnouncementWidget extends Widget
{
    protected string $view = 'filament.widgets.announcement-widget';

    // Agar muncul di posisi paling atas dashboard
    protected static ?int $sort = 1;

    // Membuat widget memenuhi lebar layar
    protected int | string | array $columnSpan = 'full';

    /**
     * Mengambil pengumuman yang aktif, belum expired, dan sesuai role user
     */
    public function getAnnouncements(): Collection
    {
        /** @var \App\Models\Dapodik_User $user */
        $user = Auth::user();

        if (!$user) return new Collection();

        return Announcement::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            // Filter berdasarkan role user yang sedang login
            ->whereHas('roles', function ($query) use ($user) {
                $query->whereIn('name', $user->getRoleNames());
            })
            ->latest()
            ->get();
    }

    /**
     * Pemetaan warna Tailwind berdasarkan tipe pengumuman
     */
    public function getLabel($tipe): string
    {
        return match ($tipe) {
            'info' => 'Informasi',
            'success' => 'Penting',
            'warning' => 'Perlu Diperhatikan',
            'danger' => 'Sangat Penting',
            default => 'Lainnya',
        };
    }

    public function getTypeConfig($tipe): array
    {
        return match ($tipe) {
            'info' => ['color' => 'info', 'icon' => 'heroicon-m-information-circle'],
            'warning' => ['color' => 'warning', 'icon' => 'heroicon-m-exclamation-triangle'],
            'danger' => ['color' => 'danger', 'icon' => 'heroicon-m-x-circle'],
            'success' => ['color' => 'success', 'icon' => 'heroicon-m-check-circle'],
            default => ['color' => 'gray', 'icon' => 'heroicon-m-megaphone'],
        };
    }
}
