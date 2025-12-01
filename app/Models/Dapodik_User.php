<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Dapodik_User extends Model
{
    use HasUuids;

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

    public function ptk()
    {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id');
    }

}
