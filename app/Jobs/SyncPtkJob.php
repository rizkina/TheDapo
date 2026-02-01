<?php

namespace App\Jobs;

use App\Models\Ptk;
use App\Models\DapodikConf;
use App\Services\DapodikService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPtkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function handle(DapodikService $service): void
    {
        Log::info("Memulai Sinkronisasi GTK...");

        try {
            // fetchData akan memanggil http://localhost:5774/WebService/getGtk?npsn=...
            $res = $service->fetchData('getGtk'); 

            if ($res && isset($res['rows'])) {
                $rows = $res['rows'];
                
                // 1. Deteksi GTK yang sudah keluar (Soft Delete)
                $apiPtkIds = collect($rows)->pluck('ptk_id')->toArray();
                Ptk::whereNotIn('id', $apiPtkIds)->delete();

                // 2. Mapping Pendidikan Sesuai Seeder Anda
                $pendidikanMap = [
                    'Tidak sekolah' => 0, 'PAUD' => 1, 'TK / sederajat' => 2,
                    'SD / sederajat' => 4, 'SMP / sederajat' => 5, 'SMA / sederajat' => 6,
                    'D1' => 20, 'D2' => 21, 'D3' => 22, 'D4' => 23,
                    'S1' => 30, 'S2' => 35, 'S3' => 40,
                ];

                $chunks = array_chunk($rows, 50);

                foreach ($chunks as $chunk) {
                    $upsertData = [];

                    foreach ($chunk as $row) {
                        $upsertData[] = [
                            'id'                        => $row['ptk_id'],
                            'sekolah_id'                => $service->getConfig()->sekolah_id ?? null,
                            'ptk_terdaftar_id'          => $row['ptk_terdaftar_id'],
                            'ptk_induk'                 => $row['ptk_induk'],
                            'tanggal_surat_tugas'       => $row['tanggal_surat_tugas'],
                            'nama'                      => $row['nama'],
                            'jenis_kelamin'             => $row['jenis_kelamin'],
                            'tempat_lahir'              => $row['tempat_lahir'],
                            'tanggal_lahir'             => $row['tanggal_lahir'],
                            'agama_id'                  => $this->sanitizeInt($row['agama_id']),
                            'agama_id_str'              => $row['agama_id_str'],
                            'nuptk'                     => $row['nuptk'],
                            'nik'                       => $row['nik'],
                            'nip'                       => $row['nip'],
                            'jenis_ptk_id'              => (int) $row['jenis_ptk_id'],
                            'jenis_ptk_id_str'          => $row['jenis_ptk_id_str'],
                            'jabatan_ptk_id'            => (int) $row['jabatan_ptk_id'],
                            'jabatan_ptk_id_str'        => $row['jabatan_ptk_id_str'],
                            'status_kepegawaian_id'     => (int) $row['status_kepegawaian_id'],
                            'status_kepegawaian_id_str' => $row['status_kepegawaian_id_str'],
                            
                            // Mapping Kode Pendidikan (S1 -> 30)
                            'pendidikan_terakhir'       => $pendidikanMap[$row['pendidikan_terakhir']] ?? 99,
                            'bidang_studi_terakhir'     => $row['bidang_studi_terakhir'],
                            'pangkat_golongan_terakhir' => $row['pangkat_golongan_terakhir'],
                            
                            // BERIKAN ARRAY LANGSUNG (Laravel casts akan menangani konversi ke JSONB)
                            'riwayat_pendidikan'        => isset($row['rwy_pend_formal']) ? json_encode($row['rwy_pend_formal']) : null,
                            'riwayat_kepangkatan'       => isset($row['rwy_kepangkatan']) ? json_encode($row['rwy_kepangkatan']) : null,
                            
                            'updated_at'                => now(),
                            'created_at'                => now(),
                            'deleted_at'                => null,
                        ];
                    }

                    // PostgreSQL Upsert
                   Ptk::upsert($upsertData, ['id'], [
                        'sekolah_id',
                        'ptk_terdaftar_id',
                        'ptk_induk',
                        'tanggal_surat_tugas',
                        'nama',
                        'jenis_kelamin',
                        'tempat_lahir',
                        'tanggal_lahir',
                        'agama_id',
                        'agama_id_str',
                        'nuptk',
                        'nik',
                        'jenis_ptk_id',
                        'jenis_ptk_id_str',
                        'jabatan_ptk_id',
                        'jabatan_ptk_id_str',
                        'status_kepegawaian_id',
                        'status_kepegawaian_id_str',
                        'nip',
                        'pendidikan_terakhir',
                        'bidang_studi_terakhir',
                        'pangkat_golongan_terakhir',
                        'riwayat_pendidikan',
                        'riwayat_kepangkatan',
                        'updated_at',
                        'deleted_at' // Sangat penting: Menghidupkan kembali data jika mutasi balik
                    ]);
                }

                DapodikConf::where('is_active', true)->update(['last_sync_at' => now()]);
                Log::info("Sinkronisasi GTK Berhasil. " . count($rows) . " data diproses.");
            }
        } catch (\Exception $e) {
            Log::error("Gagal Sync GTK: " . $e->getMessage());
        }
    }

    private function sanitizeInt($value)
    {
        $intVal = (int) $value;
        return ($intVal <= 0) ? null : $intVal;
    }
}