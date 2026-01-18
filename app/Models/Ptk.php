<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ptk extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'sekolah_id',
        'ptk_terdaftar_id',
        'ptk_induk',
        'tanggal_surat_tugas',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama_id',
        'agama_id_str',
        'nuptk',
        'nik',
        'jenis_ptk_id',
        'jenis_ptk_id_str',
        'jabatan_ptk_id',
        'jabatan_ptk_id_str',
        'status_kepegawaian_id',
        'status_kepegawaian_id_str',
        'nip',
        'pendidikan_terakhir',
        'bidang_studi_terakhir',
        'pangkat_golongan_terakhir',
        'riwayat_pendidikan',
        'riwayat_kepangkatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_surat_tugas' => 'date',
        'riwayat_pendidikan' => 'array',
        'riwayat_kepangkatan' => 'array',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function agama()
    {
        return $this->belongsTo(Agama::class, 'agama_id', 'kode');
    }

    public function pendidikanTerakhir()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_terakhir', 'kode');
    }
}
