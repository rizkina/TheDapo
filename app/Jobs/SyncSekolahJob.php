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
                $rows = $res['rows'];

                // Cek apakah 'rows' adalah array numerik atau object langsung
                // Jika API memberikan object langsung (seperti contoh JSON Anda)
                if (isset($rows['sekolah_id'])) {
                    $row = $rows;
                } 
                // Jika API memberikan array (daftar sekolah)
                else if (isset($rows[0])) {
                    $row = $rows[0];
                } 
                else {
                    $row = null;
                }

                if ($row) {
                    // MENGHAPUS SEMUA DATA LAMA
                    \App\Models\Sekolah::query()->forceDelete();

                    // BUAT RECORD BARU
                    \App\Models\Sekolah::create([
                        'id' => $row['sekolah_id'], // UUID
                        'npsn' => $row['npsn'],
                        'nama' => $row['nama'],
                        'alamat_jalan' => $row['alamat_jalan'] ?? null,
                        'rt' => $row['rt'] ?? null,
                        'rw' => $row['rw'] ?? null,
                        'kode_wilayah' => $row['kode_wilayah'] ?? null,
                        'kode_pos' => $row['kode_pos'] ?? null,
                        'nomor_telepon' => $row['nomor_telepon'] ?? null,
                        'nomor_fax' => $row['nomor_fax'] ?? null,
                        'email' => $row['email'] ?? null,
                        'website' => $row['website'] ?? null,
                        'is_sks' => filter_var($row['is_sks'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'lintang' => $row['lintang'] ?? null,
                        'bujur' => $row['bujur'] ?? null,
                        'dusun' => $row['dusun'] ?? null,
                        'desa_kelurahan' => $row['desa_kelurahan'] ?? null,
                        'kecamatan' => $row['kecamatan'] ?? null,
                        'kabupaten_kota' => $row['kabupaten_kota'] ?? null,
                        'provinsi' => $row['provinsi'] ?? null,
                    ]);

                    \App\Models\DapodikConf::where('is_active', true)->update([
                        'last_sync_at' => now()
                    ]);
                    
                    Log::info("Sinkronisasi Sekolah Berhasil: " . $row['nama']);
                } else {
                    Log::warning("Sinkronisasi Gagal: Format 'rows' tidak dikenali.");
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal Sync Sekolah: " . $e->getMessage());
        }
    }
}