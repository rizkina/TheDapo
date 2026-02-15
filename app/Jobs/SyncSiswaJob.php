<?php

namespace App\Jobs;

use App\Models\Siswa;
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

class SyncSiswaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    protected $userId;

    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    public function handle(DapodikService $service): void
    {
        Log::info("Memulai Sinkronisasi Siswa Terproteksi...");
        $recipient = $this->userId ? Dapodik_User::find($this->userId) : null;

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
                             // Identitas Utama
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
                            'agama_id'                  => $this->sanitizeInt($row['agama_id']),
                            'agama_id_str'              => $row['agama_id_str'],
                            
                            // Data Kontak & Fisik
                            'nomor_telepon_rumah'       => $row['nomor_telepon_rumah'],
                            'nomor_telepon_seluler'     => $row['nomor_telepon_seluler'],
                            'email'                     => $row['email'],
                            'anak_keberapa'             => (int) ($row['anak_keberapa'] ?? 0),
                            'tinggi_badan'              => (int) ($row['tinggi_badan'] ?? 0),
                            'berat_badan'               => (int) ($row['berat_badan'] ?? 0),
                            
                            // Data Orang Tua (Sesuai JSON)
                            'nama_ayah'                 => $row['nama_ayah'],
                            'pekerjaan_ayah_id'         => $this->sanitizeInt($row['pekerjaan_ayah_id']),
                            'pekerjaan_ayah_id_str'     => $row['pekerjaan_ayah_id_str'],
                            'nama_ibu'                  => $row['nama_ibu'],
                            'pekerjaan_ibu_id'          => $this->sanitizeInt($row['pekerjaan_ibu_id']),
                            'pekerjaan_ibu_id_str'      => $row['pekerjaan_ibu_id_str'],
                            'nama_wali'                 => $row['nama_wali'],
                            'pekerjaan_wali_id'         => $this->sanitizeInt($row['pekerjaan_wali_id']),
                            'pekerjaan_wali_id_str'     => $row['pekerjaan_wali_id_str'],
                            
                            // Data Akademik
                            'semester_id'               => $row['semester_id'],
                            'anggota_rombel_id'         => $row['anggota_rombel_id'],
                            'rombongan_belajar_id'      => $row['rombongan_belajar_id'],
                            'tingkat_pendidikan_id'     => (int) $row['tingkat_pendidikan_id'],
                            'nama_rombel'               => $row['nama_rombel'],
                            'kurikulum_id'              => $this->sanitizeInt($row['kurikulum_id']),
                            'kurikulum_id_str'          => $row['kurikulum_id_str'],
                            'kebutuhan_khusus'          => $row['kebutuhan_khusus'],
                            // Timestamps & Meta
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
                        'sekolah_id',
                        'registrasi_id',
                        'jenis_pendaftaran_id',
                        'jenis_pendaftaran_id_str',
                        'nipd',
                        'tanggal_masuk_sekolah',
                        'sekolah_asal',
                        'nama',
                        'nisn',
                        'jenis_kelamin',
                        'nik',
                        'tempat_lahir',
                        'tanggal_lahir',
                        'nama_ayah',
                        'nama_ibu',
                        'nama_wali',
                        'semester_id',
                        'anggota_rombel_id',
                        'rombongan_belajar_id',
                        'tingkat_pendidikan_id',
                        'nama_rombel',
                        'kurikulum_id',
                        'kurikulum_id_str',
                        'updated_at', 
                        'deleted_at' // Sangat penting untuk menghidupkan kembali siswa yang "pindah lalu kembali"
                    ]);
                }
                Log::info("Sinkronisasi Berhasil. Siswa bertambah/tetap, siswa tidak aktif otomatis masuk Sampah.");
                if ($recipient) {
                    Notification::make()
                        ->title('Sinkronisasi data murid Berhasil')
                        ->body('Data murid sebanyak '. count($rows) .' diperbarui.')
                        ->success() 
                        ->icon('heroicon-o-check-circle')
                        ->sendToDatabase($recipient);
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal Sync Siswa: " . $e->getMessage());
            if ($recipient) {
                Notification::make()
                    ->title('Sinkronisasi data siswa gagal.')
                    ->body("Galat : {$e->getMessage()}")
                    ->danger()
                    ->icon('heroicon-o-x-circle')
                    ->sendToDatabase($recipient);
            }
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