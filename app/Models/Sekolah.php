<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sekolah extends Model
{
    use HasUuids, SoftDeletes;
    
    protected $table = 'sekolahs';

    // PENTING: Karena kita memasukkan UUID dari Dapodik secara manual
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', // UUID dari sekolah_id
        'npsn',
        'nss', // Tambahan dari JSON
        'nama',
        'bentuk_pendidikan_id_str', // Tambahan: SMK/SMA dll
        'status_sekolah_str', // Tambahan: Negeri/Swasta
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

    protected function casts(): array
    {
        return [
            'is_sks' => 'boolean',
            'lintang' => 'decimal:12', // Dapodik mengirim hingga 12 angka di belakang koma
            'bujur' => 'decimal:12',
        ];
    }
}