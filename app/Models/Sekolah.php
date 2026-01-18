<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sekolah extends Model
{
    use HasUuids, SoftDeletes;
    
    protected $table = 'sekolahs';

    protected $fillable = [
        'npsn',
        'nama',
        'alamat_jalan',
        'rt',
        'rw',
        'kode_wilayah',
        'kode_pos',
        'nomor_telepon',
        'nomor_fax',
        'email',
        'website',
        'is_sks',
        'lintang',
        'bujur',
        'dusun',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
    ];

    protected $casts = [
        'is_sks' => 'boolean',
        'lintang' => 'decimal:7',
        'bujur' => 'decimal:7',
    ];
}
