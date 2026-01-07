<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // Penting untuk Auth
use Illuminate\Notifications\Notifiable;
// use Spatie\Permission\Traits\HasRoles; // Untuk Spatie 
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Dapodik_User extends Model
{
    use HasUuids, Notifiable; //jangan untuk ditambahkan HasRoles di sini

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

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    // Relasi ke PTK (Jika user adalah Guru)
    public function ptk() {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }

    // Relasi ke Siswa (Jika user adalah Siswa)
    public function siswa() {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id');
    }

    /**
     * Logic: Cek apakah user adalah Wali Kelas
     */
    public function isWaliKelas(): bool {
        return Rombel::where('ptk_id', $this->ptk_id)->exists();
    }


}
