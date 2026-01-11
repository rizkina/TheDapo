<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable; // Penting untuk Auth
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Untuk Spatie 
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dapodik_User extends Authenticatable implements FilamentUser
{
    use HasUuids, Notifiable, HasRoles; //jangan untuk ditambahkan HasRoles di sini

    protected $table = 'dapodik_users';

    protected $fillable = [
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
     * Method ini wajib ada agar Filament tahu kolom nama Anda adalah 'nama'
     */
    public function getFilamentName(): string
    {
        return (string) ($this->nama ?? $this->username ?? 'User');
    }

    /**
     * Tambahkan juga Accessor 'name' secara manual 
     * agar Laravel standar tidak bingung saat mencari property 'name'
     */
    public function getNameAttribute()
    {
        return $this->nama;
    }

    /**
     * Menentukan siapa yang boleh masuk ke panel Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Untuk awal, kita ijinkan semua user yang punya password untuk login.
        // Nanti setelah Spatie terpasang, kita ganti jadi $this->hasRole('admin') dll.
        return true; 
    }

    /**
     * Pastikan password otomatis di-hash jika Anda menginputnya (Laravel 11/12)
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    // Relasi ke PTK (Jika user adalah Guru)
    public function ptk(): BelongsTo
    {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }

    // Relasi ke Siswa (Jika user adalah Siswa)
    public function siswa(): BelongsTo 
    {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id');
    }

    /**
     * Logic: Cek apakah user adalah Wali Kelas
     */
    public function isWaliKelas(): bool 
    {
         if (!$this->ptk_id) return false;

        return Rombel::where('ptk_id', $this->ptk_id)->exists();
    }


}
