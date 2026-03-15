<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Siswa extends Model
{
    use HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id', // WAJIB TAMBAHKAN INI agar UUID dari Dapodik bisa masuk
        'sekolah_id',
        'registrasi_id',
        'jenis_pendaftaran_id',
        'jenis_pendaftaran_id_str',
        'nipd',
        'tanggal_masuk_sekolah',
        'sekolah_asal',
        'nama',
        'foto',
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
        'nik_ayah',
        'tahun_lahir_ayah',
        'pendidikan_ayah_id',
        'pendidikan_ayah_id_str',
        'penghasilan_ayah_id',
        'penghasilan_ayah_id_str',
        'nik_ibu',
        'tahun_lahir_ibu',
        'pendidikan_ibu_id',
        'pendidikan_ibu_id_str',
        'penghasilan_ibu_id',
        'penghasilan_ibu_id_str',
        'nik_wali',
        'tahun_lahir_wali',
        'pendidikan_wali_id',
        'pendidikan_wali_id_str',
        'penghasilan_wali_id',
        'penghasilan_wali_id_str',
    ];

    /**
     * Konfigurasi Casting (Standar Laravel 12)
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk_sekolah' => 'date',
            'tinggi_badan' => 'integer',
            'berat_badan' => 'integer',
        ];
    }

    // --- RELASI ---

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function agama(): BelongsTo
    {
        return $this->belongsTo(Agama::class, 'agama_id', 'kode');
    }

    public function pekerjaanAyah(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_ayah_id', 'kode');
    }

    public function pekerjaanIbu(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_ibu_id', 'kode');
    }

    public function pekerjaanWali(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_wali_id', 'kode');
    }

    public function pendidikanAyah(): BelongsTo
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_ayah_id', 'kode');
    }
    public function pendidikanIbu(): BelongsTo
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_ibu_id', 'kode');
    }
    public function pendidikanWali(): BelongsTo
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_wali_id', 'kode');
    }
    public function penghasilanAyah(): BelongsTo
    {
        return $this->belongsTo(Penghasilan::class, 'penghasilan_ayah_id', 'kode');
    }
    public function penghasilanIbu(): BelongsTo
    {
        return $this->belongsTo(Penghasilan::class, 'penghasilan_ibu_id', 'kode');
    }
    public function penghasilanWali(): BelongsTo
    {
        return $this->belongsTo(Penghasilan::class, 'penghasilan_wali_id', 'kode');
    }

    /**
     * Relasi Many-to-Many ke Rombel
     * Gunakan 'anggota__rombels' jika di migration menggunakan double underscore
     */
    public function rombels(): BelongsToMany
    {
        return $this->belongsToMany(Rombel::class, 'anggota__rombels', 'peserta_didik_id', 'rombel_id')
                    ->withPivot('id', 'jenis_pendaftaran_id_str')
                    ->withTimestamps();
    }
}