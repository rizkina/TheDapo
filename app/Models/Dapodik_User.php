<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; 
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dapodik_User extends Authenticatable implements FilamentUser, HasName
{
    use HasUuids, Notifiable, HasRoles, SoftDeletes; 

    protected $table = 'dapodik_users';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', // Tambahkan id agar bisa diisi UUID manual dari Job
        'sekolah_id',
        'username',
        'nama',
        'peran_id_str',
        'alamat',
        'no_telepon',
        'no_hp',
        'ptk_id',
        'peserta_didik_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Optimasi Booted Method
     */
    protected static function booted()
    {
        static::saved(function ($user) {
            // 1. CEK: Hanya jalankan jika kolom 'peran_id_str' berubah atau user baru dibuat
            // Ini penting agar database tidak terbebani setiap kali user update data lain (misal: no hp)
            if ($user->wasRecentlyCreated || $user->isDirty('peran_id_str')) {
                
                $peran = strtolower($user->peran_id_str ?? '');

                // 2. MAPPING: Sesuai dengan Seeder yang kita buat
                $roleTarget = match (true) {
                    str_contains($peran, 'siswa') => 'siswa',
                    str_contains($peran, 'guru') => 'guru',
                    str_contains($peran, 'tenaga kependidikan') => 'tenaga kependidikan',
                    str_contains($peran, 'kepala sekolah') => 'kepsek',
                    str_contains($peran, 'admin') || str_contains($peran, 'operator') => 'admin',
                    default => null,
                };

                // 3. KEAMANAN: Jangan sinkronisasi jika user adalah super_admin
                // Agar role super_admin tidak hilang karena tertimpa role otomatis
                if ($roleTarget && !$user->hasRole('super_admin')) {
                    $user->syncRoles([$roleTarget]);
                }
            }
        });
    }

    public function getFilamentName(): string
    {
        return (string) ($this->nama ?? $this->username ?? 'User');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Di masa depan: return $this->hasAnyRole(['super_admin', 'admin', 'guru', 'tenaga kependidikan', 'siswa', 'kepsek']);
        return true; 
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    // --- RELASI ---

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function ptk(): BelongsTo
    {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }

    public function siswa(): BelongsTo 
    {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id');
    }

    public function isWaliKelas(): bool 
    {
        if (!$this->ptk_id) return false;

        // Gunakan cache atau query langsung ke model Rombel
        return \App\Models\Rombel::where('ptk_id', $this->ptk_id)->exists();
    }
}