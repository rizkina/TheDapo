<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anggota_Rombel extends Model
{
    use HasUuids, SoftDeletes;

    // WAJIB: deklarasikan nama tabel karena ada double underscore
    protected $table = 'anggota__rombels';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', // Kita akan isi dengan 'anggota_rombel_id' dari JSON
        'rombel_id',
        'peserta_didik_id',
        'jenis_pendaftaran_id',
        'jenis_pendaftaran_id_str',
    ];

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'peserta_didik_id');
    }
}