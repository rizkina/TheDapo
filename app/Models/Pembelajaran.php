<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembelajaran extends Model
{
    use HasUuids;

    protected $fillable = [
        'rombel_id',
        'pembelajaran_id',
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

    protected $casts = [
        'mata_pelajaran_id' => 'integer',
        'jam_mengajar_per_minggu' => 'integer',
        'status_di_kurikulum' => 'integer',
    ];

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function ptk(): BelongsTo
    {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }

    /**
     * Helper: Mengecek apakah ini mata pelajaran utama (bukan tambahan/induk)
     */
    public function isMataPelajaranUtama(): bool
    {
        return is_null($this->induk_pembelajaran_id);
    }    
}
