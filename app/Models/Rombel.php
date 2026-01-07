<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Rombel extends Model
{
    use HasUuids;

    protected $fillable = [
        'sekolah_id',
        'nama',
        'tingkat_pendidikan_id',
        'tingkat_pendidikan_id_str',
        'semester_id',
        'jenis_rombel',
        'jenis_rombel_str',
        'kurikulum_id',
        'kurikulum_id_str',
        'id_ruang',
        'id_ruang_str',
        'moving_class',
        'ptk_id',
        'ptk_id_str',
        'jurusan_id',
        'jurusan_id_str',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function ptk()
    {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }

    // Relasi langsung ke banyak siswa melalui tabel pivot anggota_rombels
    public function siswas() {
        return $this->belongsToMany(Siswa::class, 'anggota_rombels', 'rombel_id', 'peserta_didik_id');
    }
}
