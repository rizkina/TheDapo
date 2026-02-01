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
                $dataRows = $res['rows'];

                // Mendeteksi apakah 'rows' berupa object tunggal atau array
                $row = isset($dataRows['sekolah_id']) ? $dataRows : ($dataRows[0] ?? null);

                if ($row) {
                    // 1. UPDATE atau CREATE (Menggunakan updateOrCreate lebih aman daripada Delete-Create)
                    $sekolah = \App\Models\Sekolah::updateOrCreate(
                        ['id' => $row['sekolah_id']], // Kunci pencarian
                        [
                            'npsn'                      => $row['npsn'],
                            'nss'                       => $row['nss'] ?? null,
                            'nama'                      => $row['nama'],
                            'bentuk_pendidikan_id_str'  => $row['bentuk_pendidikan_id_str'] ?? null,
                            'status_sekolah_str'        => $row['status_sekolah_str'] ?? null,
                            'alamat_jalan'              => $row['alamat_jalan'] ?? null,
                            'rt'                        => $row['rt'] ?? null,
                            'rw'                        => $row['rw'] ?? null,
                            'kode_wilayah'              => $row['kode_wilayah'] ?? null,
                            'kode_pos'                  => $row['kode_pos'] ?? null,
                            'nomor_telepon'             => $row['nomor_telepon'] ?? null,
                            'nomor_fax'                 => $row['nomor_fax'] ?? null,
                            'email'                     => $row['email'] ?? null,
                            'website'                   => $row['website'] ?? null,
                            'is_sks'                    => filter_var($row['is_sks'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'lintang'                   => $row['lintang'] ?? null,
                            'bujur'                     => $row['bujur'] ?? null,
                            'dusun'                     => $row['dusun'] ?? null,
                            'desa_kelurahan'            => $row['desa_kelurahan'] ?? null,
                            'kecamatan'                 => $row['kecamatan'] ?? null,
                            'kabupaten_kota'            => $row['kabupaten_kota'] ?? null,
                            'provinsi'                  => $row['provinsi'] ?? null,
                        ]
                    );

                    // 2. LOGIKA "HANYA 1 RECORD": 
                    // Hapus sekolah lain yang ID-nya TIDAK SAMA dengan yang baru saja ditarik
                    // Ini jauh lebih aman daripada menghapus semua di awal.
                    \App\Models\Sekolah::where('id', '!=', $sekolah->id)->forceDelete();

                    // 3. Update waktu sinkronisasi terakhir
                    \App\Models\DapodikConf::where('is_active', true)->update([
                        'last_sync_at' => now()
                    ]);
                    
                    Log::info("Sinkronisasi Sekolah Berhasil: " . $sekolah->nama);
                } else {
                    Log::warning("Sinkronisasi Gagal: Format 'rows' tidak dikenali atau kosong.");
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal Sync Sekolah: " . $e->getMessage());
        }
    }
}