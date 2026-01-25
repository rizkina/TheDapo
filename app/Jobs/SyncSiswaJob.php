<?php

namespace App\Jobs;

use App\Models\Siswa;
use App\Models\DapodikConf;
use App\Services\DapodikService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSiswaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function handle(DapodikService $service): void
    {
        Log::info("Memulai Sinkronisasi Siswa...");

        try {
            $res = $service->fetchData('getPesertaDidik');

            if ($res && isset($res['rows'])) {
                $rows = $res['rows'];
                $chunks = array_chunk($rows, 100);

                foreach ($chunks as $chunk) {
                    $upsertData = [];

                    foreach ($chunk as $row) {
                        $upsertData[] = [
                            'id'                        => $row['peserta_didik_id'],
                            'sekolah_id'                => $service->getConfig()->sekolah_id ?? null,
                            'registrasi_id'             => $row['registrasi_id'],
                            'jenis_pendaftaran_id'      => $this->sanitizeInt($row['jenis_pendaftaran_id']),
                            'jenis_pendaftaran_id_str'  => $row['jenis_pendaftaran_id_str'],
                            'nipd'                      => $row['nipd'],
                            'tanggal_masuk_sekolah'     => $row['tanggal_masuk_sekolah'],
                            'sekolah_asal'              => $row['sekolah_asal'],
                            'nama'                      => $row['nama'],
                            'nisn'                      => $row['nisn'],
                            'jenis_kelamin'             => $row['jenis_kelamin'],
                            'nik'                       => $row['nik'],
                            'tempat_lahir'              => $row['tempat_lahir'],
                            'tanggal_lahir'             => $row['tanggal_lahir'],
                            
                            // SANITASI FOREIGN KEYS (Penting!)
                            'agama_id'                  => $this->sanitizeInt($row['agama_id']),
                            'pekerjaan_ayah_id'         => $this->sanitizeInt($row['pekerjaan_ayah_id']),
                            'pekerjaan_ibu_id'          => $this->sanitizeInt($row['pekerjaan_ibu_id']),
                            'pekerjaan_wali_id'         => $this->sanitizeInt($row['pekerjaan_wali_id']),
                            
                            'agama_id_str'              => $row['agama_id_str'],
                            'nomor_telepon_rumah'       => $row['nomor_telepon_rumah'],
                            'nomor_telepon_seluler'     => $row['nomor_telepon_seluler'],
                            'nama_ayah'                 => $row['nama_ayah'],
                            'pekerjaan_ayah_id_str'     => $row['pekerjaan_ayah_id_str'],
                            'nama_ibu'                  => $row['nama_ibu'],
                            'pekerjaan_ibu_id_str'      => $row['pekerjaan_ibu_id_str'],
                            'nama_wali'                 => $row['nama_wali'],
                            'pekerjaan_wali_id_str'     => $row['pekerjaan_wali_id_str'],
                            
                            'anak_keberapa'             => (int) ($row['anak_keberapa'] ?? 0),
                            'tinggi_badan'              => (int) ($row['tinggi_badan'] ?? 0),
                            'berat_badan'               => (int) ($row['berat_badan'] ?? 0),
                            'email'                     => $row['email'],
                            'semester_id'               => $row['semester_id'],
                            'anggota_rombel_id'         => $row['anggota_rombel_id'],
                            'rombongan_belajar_id'      => $row['rombongan_belajar_id'],
                            'tingkat_pendidikan_id'     => (int) $row['tingkat_pendidikan_id'],
                            'nama_rombel'               => $row['nama_rombel'],
                            'kurikulum_id'              => $this->sanitizeInt($row['kurikulum_id']),
                            'kurikulum_id_str'          => $row['kurikulum_id_str'],
                            'kebutuhan_khusus'          => $row['kebutuhan_khusus'],

                            'nik_ayah' => $row['nik_ayah'] ?? null,
                            'tahun_lahir_ayah' => $row['tahun_lahir_ayah'] ?? null,
                            'pendidikan_ayah_id' => $row['pendidikan_ayah_id'] ?? null,
                            'pendidikan_ayah_id_str' => $row['pendidikan_ayah_id_str'] ?? null,
                            'penghasilan_ayah_id' => $row['penghasilan_ayah_id'] ?? null,
                            'penghasilan_ayah_id_str' => $row['penghasilan_ayah_id_str'] ?? null,

                            'nik_ibu' => $row['nik_ibu'] ?? null,
                            'tahun_lahir_ibu' => $row['tahun_lahir_ibu'] ?? null,
                            'pendidikan_ibu_id' => $row['pendidikan_ibu_id'] ?? null,
                            'pendidikan_ibu_id_str' => $row['pendidikan_ibu_id_str'] ?? null,
                            'penghasilan_ibu_id' => $row['penghasilan_ibu_id'] ?? null,
                            'penghasilan_ibu_id_str' => $row['penghasilan_ibu_id_str'] ?? null,

                            'nik_wali' => $row['nik_wali'] ?? null,
                            'tahun_lahir_wali' => $row['tahun_lahir_wali'] ?? null,
                            'pendidikan_wali_id' => $row['pendidikan_wali_id'] ?? null,
                            'pendidikan_wali_id_str' => $row['pendidikan_wali_id_str'] ?? null,
                            'penghasilan_wali_id' => $row['penghasilan_wali_id'] ?? null,
                            'penghasilan_wali_id_str' => $row['penghasilan_wali_id_str'] ?? null,
                            
                            'updated_at'                => now(),
                            'created_at'                => now(),
                        ];
                    }

                    Siswa::upsert($upsertData, ['id'], [
                        'nama', 'nisn', 'nik', 'nama_rombel', 'updated_at', 
                        'tinggi_badan', 'berat_badan', 'pekerjaan_wali_id',
                        'nik_ayah', 'tahun_lahir_ayah', 
                        'pendidikan_ayah_id', 'pendidikan_ayah_id_str',
                        'penghasilan_ayah_id', 'penghasilan_ayah_id_str',
                        'nik_ibu', 'tahun_lahir_ibu',
                        'pendidikan_ibu_id', 'pendidikan_ibu_id_str',
                        'penghasilan_ibu_id', 'penghasilan_ibu_id_str',
                        'nik_wali', 'tahun_lahir_wali',
                        'pendidikan_wali_id', 'pendidikan_wali_id_str',
                        'penghasilan_wali_id', 'penghasilan_wali_id_str',
                    ]);
                }

                DapodikConf::where('is_active', true)->update(['last_sync_at' => now()]);
                Log::info("Sinkronisasi Selesai. " . count($rows) . " siswa diproses.");
            }
        } catch (\Exception $e) {
            Log::error("Gagal Sync Siswa: " . $e->getMessage());
        }
    }

    /**
     * Helper untuk mengubah nilai 0 atau kosong menjadi NULL
     * agar tidak melanggar Foreign Key di PostgreSQL
     */
    private function sanitizeInt($value)
    {
        $intVal = (int) $value;
        return ($intVal <= 0) ? null : $intVal;
    }
}