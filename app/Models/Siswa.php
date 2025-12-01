<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Siswa extends Model
{
    use HasUuids;

    protected $fillable = [
        'sekolah_id',
        'registrasi_id',
        'jenis_pendaftaran_id',
        'jenis_pendaftaran_id_str',
        'nipd',
        'tanggal_masuk_sekolah',
        'sekolah_asal',
        'nama',
        'nisn',
        'jenis_kelamin',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'agama_id',
        'agama_id_str',
        'nomor_telepon_rumah',
        'nomor_telepon_seluler',
        'nama_ayah',
        'pekerjaan_ayah_id',
        'pekerjaan_ayah_id_str',
        'nama_ibu',
        'pekerjaan_ibu_id',
        'pekerjaan_ibu_id_str',
        'nama_wali',
        'pekerjaan_wali_id',
        'pekerjaan_wali_id_str',
        'anak_keberapa',
        'tinggi_badan',
        'berat_badan',
        'email',
        'semester_id',
        'anggota_rombel_id',
        'rombongan_belajar_id',
        'tingkat_pendidikan_id',
        'nama_rombel',
        'kurikulum_id',
        'kurikulum_id_str',
        'kebutuhan_khusus',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk_sekolah' => 'date',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function agama()
    {
        return $this->belongsTo(Agama::class, 'agama_id', 'kode');
    }

    public function pekerjaanAyah()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_ayah_id', 'kode');
    }

    public function pekerjaanIbu()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_ibu_id', 'kode');
    }

    public function pekerjaanWali()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_wali_id', 'kode');
    }
}
