<?php

namespace App\Jobs;

use App\Models\Sekolah;
use App\Models\DapodikConf;
use App\Models\Dapodik_User; 
use App\Services\DapodikService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class SyncSekolahJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    protected $userId;

    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    public function handle(DapodikService $service): void
    {
        Log::info("Memulai sinkronisasi data Sekolah dari Dapodik...");

        // PERBAIKAN: Ambil recipient dari model Dapodik_User
        $recipient = $this->userId ? Dapodik_User::find($this->userId) : null;

        try {
            $res = $service->fetchData('getSekolah');

            if ($res && isset($res['rows'])) {
                $dataRows = $res['rows'];
                $row = isset($dataRows['sekolah_id']) ? $dataRows : ($dataRows[0] ?? null);

                if ($row) {
                    $sekolah = Sekolah::updateOrCreate(
                        ['id' => $row['sekolah_id']], // Kunci UUID
                        [
                            'npsn'                      => $row['npsn'],
                            'nss'                       => $row['nss'] ?? null,
                            'nama'                      => $row['nama'],
                            'bentuk_pendidikan_id_str'  => $row['bentuk_pendidikan_id_str'] ?? null, // TAMBAHKAN INI
                            'status_sekolah_str'        => $row['status_sekolah_str'] ?? null,   // TAMBAHKAN INI
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

                    // Pastikan hanya 1 record
                    Sekolah::where('id', '!=', $sekolah->id)->forceDelete();

                    // Update last sync
                    DapodikConf::where('is_active', true)->update(['last_sync_at' => now()]);

                    if ($recipient) {
                        Notification::make()
                            ->title('Sinkronisasi Berhasil')
                            ->body("Data sekolah '{$sekolah->nama}' diperbarui.")
                            ->success() 
                            ->icon('heroicon-o-check-circle')
                            ->sendToDatabase($recipient);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal Sync Sekolah: " . $e->getMessage());
            if ($recipient) {
                Notification::make()
                    ->title('Sinkronisasi Gagal')
                    ->body("Galat : {$e->getMessage()}")
                    ->danger()
                    ->icon('heroicon-o-x-circle')
                    ->sendToDatabase($recipient);
            }
        }
    }
}