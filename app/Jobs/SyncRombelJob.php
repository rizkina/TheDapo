<?php

namespace App\Jobs;

use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\Ptk;
use App\Models\Anggota_Rombel;
use App\Models\Pembelajaran;
use App\Services\DapodikService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncRombelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200; // 20 menit karena prosesnya berat

    public function handle(DapodikService $service): void
    {
        Log::info("Memulai Sinkronisasi Rombel Kompleks...");

        try {
            $res = $service->fetchData('getRombonganBelajar');

            if ($res && isset($res['rows'])) {
                foreach ($res['rows'] as $row) {
                    DB::transaction(function () use ($row, $service) {
                        // 1. Sinkronisasi Data Rombel Utama
                        $rombel = \App\Models\Rombel::updateOrCreate(
                            ['id' => $row['rombongan_belajar_id']],
                            [
                                'sekolah_id'                => $service->getConfig()->sekolah_id ?? null,
                                'nama'                      => $row['nama'],
                                'tingkat_pendidikan_id'     => $row['tingkat_pendidikan_id'],
                                'tingkat_pendidikan_id_str' => $row['tingkat_pendidikan_id_str'],
                                'semester_id'               => $row['semester_id'],
                                'kurikulum_id_str'          => $row['kurikulum_id_str'],
                                'ptk_id'                    => $row['ptk_id'], // Wali Kelas
                                'ptk_id_str'                => $row['ptk_id_str'],
                                'moving_class'              => $row['moving_class'],
                                'updated_at'                => now(),
                            ]
                        );

                        // 2. Sinkronisasi Anggota Rombel (Siswa)
                        if (isset($row['anggota_rombel'])) {
                            $apiAnggotaIds = collect($row['anggota_rombel'])->pluck('anggota_rombel_id')->toArray();
                            
                            // Hapus anggota yang sudah tidak ada di rombel ini (di Dapodik)
                            \App\Models\Anggota_Rombel::where('rombel_id', $rombel->id)
                                ->whereNotIn('id', $apiAnggotaIds)
                                ->delete();

                            foreach ($row['anggota_rombel'] as $anggota) {
                                \App\Models\Anggota_Rombel::updateOrCreate(
                                    ['id' => $anggota['anggota_rombel_id']],
                                    [
                                        'rombel_id'                => $rombel->id,
                                        'peserta_didik_id'         => $anggota['peserta_didik_id'],
                                        'jenis_pendaftaran_id_str' => $anggota['jenis_pendaftaran_id_str'],
                                    ]
                                );
                            }
                        }

                        // 3. Sinkronisasi Pembelajaran (Guru & Mapel)
                        if (isset($row['pembelajaran'])) {
                            foreach ($row['pembelajaran'] as $pemb) {
                                \App\Models\Pembelajaran::updateOrCreate(
                                    ['id' => $pemb['pembelajaran_id']], // Gunakan pembelajaran_id sebagai PK
                                    [
                                        'rombel_id'               => $rombel->id,
                                        'ptk_id'                  => $pemb['ptk_id'],
                                        'mata_pelajaran_id'       => $pemb['mata_pelajaran_id'],
                                        'mata_pelajaran_id_str'   => $pemb['mata_pelajaran_id_str'],
                                        'nama_mata_pelajaran'     => $pemb['nama_mata_pelajaran'],
                                        'jam_mengajar_per_minggu' => (int) ($pemb['jam_mengajar_per_minggu'] ?? 0),
                                        'induk_pembelajaran_id'   => $pemb['induk_pembelajaran_id'],
                                        'ptk_terdaftar_id'        => $pemb['ptk_terdaftar_id'] ?? null,
                                        'status_di_kurikulum'     => (int) ($pemb['status_di_kurikulum'] ?? 0),
                                        'status_di_kurikulum_str' => $pemb['status_di_kurikulum_str'] ?? null,
                                    ]
                                );
                            }
                        }
                    });
                }
                
                // Update timestamp sinkronisasi terakhir
                \App\Models\DapodikConf::where('is_active', true)->update(['last_sync_at' => now()]);
                Log::info("Sinkronisasi Rombel Selesai.");
            }
        } catch (\Exception $e) {
            Log::error("Gagal Sync Rombel: " . $e->getMessage());
        }
    }
}