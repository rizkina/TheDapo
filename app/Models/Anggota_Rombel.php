<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Anggota_Rombel extends Model
{
    use HasUuids;

    protected $fillable = [
        'rombel_id',
        'anggota_rombel_id',
        'peserta_didik_id',
        'jenis_pendaftaran_id',
        'jenis_pendaftaran_id_str',
    ];

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id');
    }
}
