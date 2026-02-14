<?php

namespace App\Jobs;

use App\Models\Ptk;
use App\Models\Siswa;
use App\Models\Dapodik_User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class GenerateDapodikUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900;
    protected $userId;

    public function __construct($userId = null) 
    {
        $this->userId = $userId;
    }

    public function handle(): void
    {
        Log::info("Memulai pembuatan akun otomatis...");
        
        $recipient = $this->userId ? Dapodik_User::find($this->userId) : null;
        
        // Inisialisasi penghitung
        $countPtk = 0;
        $countSiswa = 0;

        try {
            // 1. GENERATE USER UNTUK GTK
            $ptks = Ptk::all();
            foreach ($ptks as $ptk) {
                $username = $ptk->nip ?? $ptk->nuptk;

                if ($username) {
                    Dapodik_User::updateOrCreate(
                        ['ptk_id' => $ptk->id],
                        [
                            'username'     => $username,
                            'nama'         => $ptk->nama,
                            'password'     => $ptk->tanggal_lahir ? $ptk->tanggal_lahir->format('dmY') : 'password123',
                            'peran_id_str' => $ptk->jenis_ptk_id_str,
                            'sekolah_id'   => $ptk->sekolah_id,
                        ]
                    );
                    $countPtk++;
                }
            }

            // 2. GENERATE USER UNTUK SISWA (Gunakan Chunks agar hemat RAM)
            Siswa::chunk(100, function ($siswas) use (&$countSiswa) {
                foreach ($siswas as $siswa) {
                    if (!$siswa->nisn) continue;

                    Dapodik_User::updateOrCreate(
                        ['peserta_didik_id' => $siswa->id],
                        [
                            'username'     => $siswa->nisn,
                            'nama'         => $siswa->nama,
                            'password'     => $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('dmY') : 'siswa123',
                            'peran_id_str' => 'Siswa',
                            'sekolah_id'   => $siswa->sekolah_id,
                        ]
                    );
                    $countSiswa++;
                }
            });

            Log::info("Pembuatan akun selesai. GTK: $countPtk, Siswa: $countSiswa");

            // Notifikasi Berhasil
            if ($recipient) {
                Notification::make()
                    ->title('Generate Akun Berhasil')
                    ->body("Berhasil memperbarui akun untuk $countPtk GTK dan $countSiswa Siswa.")
                    ->success() 
                    ->icon('heroicon-o-check-circle')
                    ->sendToDatabase($recipient);
            }

        } catch (\Exception $e) {
            Log::error("Gagal Generate Akun: " . $e->getMessage());

            // Notifikasi Gagal
            if ($recipient) {
                Notification::make()
                    ->title('Generate Akun Gagal')
                    ->body("Galat: " . $e->getMessage())
                    ->danger() 
                    ->icon('heroicon-o-x-circle')
                    ->sendToDatabase($recipient);
            }
        }
    }
}