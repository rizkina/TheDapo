<?php

namespace App\Jobs;

use App\Models\Sekolah;
use App\Models\DapodikConf;
use App\Services\DapodikService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSekolahJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Tentukan waktu timeout (misal 5 menit)
    public $timeout = 300;

    public function handle(DapodikService $service): void
    {
        Log::info("Memulai sinkronisasi data Sekolah dari Dapodik...");

         try {
            $res = $service->fetchData('getSekolah');

            if ($res && isset($res['rows'])) {
                // Kita ambil hanya row pertama karena ini data profil sekolah
                $row = $res['rows'][0] ?? null;

                if ($row) {
                    // MENGHAPUS SEMUA DATA LAMA (agar benar-benar hanya ada 1 record)
                    // Kita gunakan forceDelete jika ingin benar-benar hilang dari DB
                    \App\Models\Sekolah::query()->forceDelete();

                    // BUAT RECORD BARU
                    \App\Models\Sekolah::create([
                        'id' => $row['sekolah_id'],
                        'npsn' => $row['npsn'],
                        'nama' => $row['nama'],
                        'alamat_jalan' => $row['alamat_jalan'],
                        'rt' => $row['rt'],
                        'rw' => $row['rw'],
                        'kode_wilayah' => $row['kode_wilayah'],
                        'kode_pos' => $row['kode_pos'],
                        'nomor_telepon' => $row['nomor_telepon'],
                        'nomor_fax' => $row['nomor_fax'],
                        'email' => $row['email'],
                        'website' => $row['website'],
                        'is_sks' => (bool) ($row['is_sks'] ?? false),
                        'lintang' => $row['lintang'],
                        'bujur' => $row['bujur'],
                        'dusun' => $row['dusun'],
                        'desa_kelurahan' => $row['desa_kelurahan'],
                        'kecamatan' => $row['kecamatan'],
                        'kabupaten_kota' => $row['kabupaten_kota'],
                        'provinsi' => $row['provinsi'],
                    ]);

                    \App\Models\DapodikConf::where('is_active', true)->update([
                        'last_sync_at' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal Sync Sekolah: " . $e->getMessage());
        }
    }
}