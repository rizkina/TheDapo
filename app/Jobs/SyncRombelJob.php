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
use Filament\Notifications\Notification;
use App\Models\Dapodik_User;

class SyncRombelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200; // 20 menit karena prosesnya berat

    protected $userId;
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle(DapodikService $service): void
    {
        Log::info("Memulai Sinkronisasi Rombel Lengkap...");
        $recipient = $this->userId ? Dapodik_User::find($this->userId) : null;

        $totalRombel = 0;

        try {
            // Ambil data dengan limit besar agar semua 84 rombel terbawa
            $res = $service->fetchData('getRombonganBelajar', ['limit' => 500]);

            if ($res && isset($res['rows'])) {
                $totalRombel = count($res['rows']);
                foreach ($res['rows'] as $row) {
                    // Gunakan try-catch per baris agar jika 1 rombel error, 83 lainnya tetap masuk
                    try {
                        DB::transaction(function () use ($row, $service) {
                            
                            // Cek apakah PTK (Wali Kelas) ada di DB kita
                            $ptkExists = \App\Models\Ptk::where('id', $row['ptk_id'])->exists();

                            // 1. Sinkronisasi Data Rombel
                            $rombel = \App\Models\Rombel::updateOrCreate(
                                ['id' => $row['rombongan_belajar_id']],
                                [
                                    'sekolah_id'                => $service->getConfig()->sekolah_id ?? null,
                                    'nama'                      => $row['nama'],
                                    'tingkat_pendidikan_id'     => $row['tingkat_pendidikan_id'],
                                    'tingkat_pendidikan_id_str' => $row['tingkat_pendidikan_id_str'],
                                    'semester_id'               => $row['semester_id'],
                                    'jenis_rombel'              => $row['jenis_rombel'],
                                    'jenis_rombel_str'          => $row['jenis_rombel_str'],
                                    'jurusan_id'                => $row['jurusan_id'],
                                    'jurusan_id_str'            => $row['jurusan_id_str'],
                                    'id_ruang'                  => $row['id_ruang'],
                                    'id_ruang_str'              => $row['id_ruang_str'],
                                    'kurikulum_id'              => $row['kurikulum_id'],
                                    'kurikulum_id_str'          => $row['kurikulum_id_str'],
                                    // Jika PTK tidak ditemukan di DB lokal, set null agar tidak error FK
                                    'ptk_id'                    => $ptkExists ? $row['ptk_id'] : null, 
                                    'ptk_id_str'                => $row['ptk_id_str'],
                                    'moving_class'              => $row['moving_class'],
                                    'updated_at'                => now(),
                                ]
                            );

                            // 2. Sinkronisasi Anggota Rombel
                            if (isset($row['anggota_rombel'])) {
                                foreach ($row['anggota_rombel'] as $anggota) {
                                    // Pastikan Siswa ada di DB kita sebelum masukkan ke rombel
                                    $pdExists = \App\Models\Siswa::where('id', $anggota['peserta_didik_id'])->exists();
                                    
                                    if ($pdExists) {
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
                            }

                            // 3. Sinkronisasi Pembelajaran
                            if (isset($row['pembelajaran'])) {
                                foreach ($row['pembelajaran'] as $pemb) {
                                    $guruMapelExists = \App\Models\Ptk::where('id', $pemb['ptk_id'])->exists();

                                    \App\Models\Pembelajaran::updateOrCreate(
                                        ['id' => $pemb['pembelajaran_id']],
                                        [
                                            'rombel_id'               => $rombel->id,
                                            'ptk_id'                  => $guruMapelExists ? $pemb['ptk_id'] : null,
                                            'mata_pelajaran_id'       => $pemb['mata_pelajaran_id'],
                                            'mata_pelajaran_id_str'   => $pemb['mata_pelajaran_id_str'],
                                            'nama_mata_pelajaran'     => $pemb['nama_mata_pelajaran'],
                                            'jam_mengajar_per_minggu' => (int) ($pemb['jam_mengajar_per_minggu'] ?? 0),
                                        ]
                                    );
                                }
                            }
                        });
                        $recipient = Dapodik_User::find($this->userId);
                
                        
                        Log::info("Sinkronisasi Selesai." . $row['nama'] . "selesai");
                        
                    } catch (\Exception $rowException) {
                        Log::error("Gagal memproses Rombel: " . $row['nama'] . ". Error: " . $rowException->getMessage());
                        continue; // Lanjut ke rombel berikutnya
                    }
                }
                Log::info("Sinkronisasi Rombel Selesai.");
                if ($recipient) {
                    Notification::make()
                        ->title('Sinkronisasi Rombel Selesai')
                        ->body("Berhasil menarik $totalRombel data Rombongan Belajar beserta Anggota dan Pembelajarannya.")
                        ->success()
                        ->icon('heroicon-o-check-circle')
                        ->sendToDatabase($recipient); // Notifikasi dikirim ke database
                }
            }
        } catch (\Exception $e) {
            Log::error("Koneksi API Gagal: " . $e->getMessage());
            if ($recipient) {
                Notification::make()
                    ->title('Sinkronisasi data rombel gagal.')
                    ->body("Galat : {$e->getMessage()}")
                    ->danger()
                    ->icon('heroicon-o-x-circle')
                    ->sendToDatabase($recipient);
            }
        }
    }
}