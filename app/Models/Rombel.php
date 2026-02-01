<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rombel extends Model
{
    use HasUuids, SoftDeletes;

    // Tambahkan ini agar PostgreSQL tidak mencoba menganggap ID sebagai integer
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', // WAJIB ada untuk proses Sync/Upsert
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

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function ptk(): BelongsTo
    {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }

    public function siswas(): BelongsToMany
    {
        // Pastikan nama tabel pivot anggota__rombels sesuai dengan migration (double underscore)
        return $this->belongsToMany(Siswa::class, 'anggota__rombels', 'rombel_id', 'peserta_didik_id')
            ->withPivot('id', 'anggota_rombel_id', 'jenis_pendaftaran_id_str')
            ->withTimestamps();
    }

    public function waliKelas(): BelongsTo
    {
        return $this->ptk();
    }
}