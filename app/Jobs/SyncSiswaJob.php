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

    // Timeout cukup lama untuk data besar
    public $timeout = 600; 

    public function handle(DapodikService $service): void
    {
        Log::info("Memulai Sinkronisasi Siswa Massal...");

        try {
            // Tarik data dari endpoint getPesertaDidik
            $res = $service->fetchData('getPesertaDidik');

            if ($res && isset($res['rows'])) {
                $rows = $res['rows'];
                
                // Chunking: Bagi ribuan data menjadi paket 100 per insert
                $chunks = array_chunk($rows, 100);

                foreach ($chunks as $chunk) {
                    $upsertData = [];

                    foreach ($chunk as $row) {
                        $upsertData[] = [
                            'id'                     => $row['peserta_didik_id'], // UUID dari Dapodik
                            'sekolah_id'            => $row['sekolah_id'],
                            'nama'                   => $row['nama'],
                            'nisn'                   => $row['nisn'],
                            'nik'                    => $row['nik'],
                            'jenis_kelamin'          => $row['jenis_kelamin'],
                            'tempat_lahir'           => $row['tempat_lahir'],
                            'tanggal_lahir'          => $row['tanggal_lahir'],
                            'nipd'                   => $row['nipd'] ?? null,
                            'agama_id'               => $row['agama_id'],
                            'nama_ayah'              => $row['nama_ayah'] ?? null,
                            'nama_ibu'               => $row['nama_ibu_kandung'] ?? null,
                            'nama_rombel'            => $row['nama_rombel'] ?? null,
                            'tingkat_pendidikan_id' => $row['tingkat_pendidikan_id'] ?? null,
                            'updated_at'             => now(),
                            'created_at'             => now(),
                        ];
                    }

                    /**
                     * UPSERT (Sangat Cepat):
                     * Jika ID sudah ada, update kolom tertentu.
                     * Jika ID belum ada, buat record baru.
                     */
                    Siswa::upsert($upsertData, ['id'], [
                        'nama', 'nisn', 'nik', 'sekolah_id', 'nama_rombel', 'updated_at'
                    ]);
                }

                // Update info sync terakhir di config
                DapodikConf::where('is_active', true)->update(['last_sync_at' => now()]);
                
                Log::info("Sinkronisasi Berhasil: " . count($rows) . " data siswa diproses.");
            }
        } catch (\Exception $e) {
            Log::error("Gagal Sinkronisasi Siswa: " . $e->getMessage());
        }
    }
}