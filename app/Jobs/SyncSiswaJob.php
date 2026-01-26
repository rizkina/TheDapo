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
        Log::info("Memulai Sinkronisasi Siswa Terproteksi...");

        try {
            $res = $service->fetchData('getPesertaDidik');

            if ($res && isset($res['rows'])) {
                $rows = $res['rows'];
                
                // 1. Ambil semua ID (UUID) siswa dari API Dapodik
                $apiSiswaIds = collect($rows)->pluck('peserta_didik_id')->toArray();

                // 2. DETEKSI SISWA KELUAR (Soft Delete)
                // Siswa yang ada di DB lokal tapi TIDAK ADA di API Dapodik akan di-softdelete
                \App\Models\Siswa::whereNotIn('id', $apiSiswaIds)->delete();

                // 3. PROSES TAMBAH & UPDATE MINIMAL
                $chunks = array_chunk($rows, 100);
                foreach ($chunks as $chunk) {
                    $upsertData = [];
                    foreach ($chunk as $row) {
                        $upsertData[] = [
                            'id' => $row['peserta_didik_id'],
                            'nama' => $row['nama'],
                            'nisn' => $row['nisn'],
                            'nik' => $row['nik'],
                            'nipd' => $row['nipd'],
                            'nama_rombel' => $row['nama_rombel'],
                            'sekolah_id' => $service->getConfig()->sekolah_id ?? null,
                            'created_at' => now(), // Digunakan jika data baru (Insert)
                            'updated_at' => now(), // Selalu diupdate
                            'deleted_at' => null,  // Pastikan jika dulu pernah keluar lalu masuk lagi, statusnya aktif kembali
                        ];
                    }

                    /**
                     * UPSERT STRATEGY:
                     * - Parameter 2: 'id' (UUID) sebagai kunci penentu.
                     * - Parameter 3: HANYA kolom yang boleh diubah otomatis oleh sistem.
                     *   Data Orang Tua TIDAK ADA di list ini, sehingga jika admin lokal sudah mengubahnya, 
                     *   nilainya tidak akan tertimpa (terhapus) oleh data kosong dari API.
                     */
                    \App\Models\Siswa::upsert($upsertData, ['id'], [
                        'nama', 
                        'nisn', 
                        'nik', 
                        'nipd', 
                        'nama_rombel', 
                        'updated_at', 
                        'deleted_at' // Sangat penting untuk menghidupkan kembali siswa yang "pindah lalu kembali"
                    ]);
                }

                Log::info("Sinkronisasi Berhasil. Siswa bertambah/tetap, siswa tidak aktif otomatis masuk Sampah.");
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