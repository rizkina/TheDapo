<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelajaran extends Model
{
    use HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', // Kita akan isi dengan 'pembelajaran_id' dari JSON
        'rombel_id',
        'mata_pelajaran_id',
        'mata_pelajaran_id_str',
        'ptk_terdaftar_id',
        'ptk_id',
        'nama_mata_pelajaran',
        'induk_pembelajaran_id',
        'jam_mengajar_per_minggu',
        'status_di_kurikulum',
        'status_di_kurikulum_str',
    ];

    protected function casts(): array
    {
        return [
            'mata_pelajaran_id' => 'integer',
            'jam_mengajar_per_minggu' => 'integer',
            'status_di_kurikulum' => 'integer',
        ];
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function ptk(): BelongsTo
    {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }
}