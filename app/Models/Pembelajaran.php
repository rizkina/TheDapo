<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function ptk()
    {
        return $this->belongsTo(Ptk::class, 'ptk_id');
    }
}
