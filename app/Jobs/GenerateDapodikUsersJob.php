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

class GenerateDapodikUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900;

    public function handle(): void
    {
        Log::info("Memulai pembuatan akun otomatis...");

        // 1. GENERATE USER UNTUK GTK
        Ptk::all()->each(function ($ptk) {
            $username = $ptk->nip ?? $ptk->nuptk;

            // Validasi: Jangan proses jika tidak ada username (NIP/NUPTK)
            if (!$username) return;

            Dapodik_User::updateOrCreate(
                ['ptk_id' => $ptk->id], // Cari berdasarkan PTK ID
                [
                    'username'     => $username,
                    'nama'         => $ptk->nama,
                    // Password default: tgl lahir tanpa tanda baca (misal: 17081945)
                    'password'     => $ptk->tanggal_lahir ? $ptk->tanggal_lahir->format('dmY') : 'password123',
                    'peran_id_str' => $ptk->jenis_ptk_id_str, // Ini akan memicu role otomatis di model
                    'sekolah_id'   => $ptk->sekolah_id,
                ]
            );
        });

        // 2. GENERATE USER UNTUK SISWA
        Siswa::chunk(100, function ($siswas) {
            foreach ($siswas as $siswa) {
                if (!$siswa->nisn) continue;

                Dapodik_User::updateOrCreate(
                    ['peserta_didik_id' => $siswa->id],
                    [
                        'username'     => $siswa->nisn,
                        'nama'         => $siswa->nama,
                        'password'     => $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('dmY') : 'siswa123',
                        'peran_id_str' => 'Siswa', // Ini akan memicu role otomatis di model
                        'sekolah_id'   => $siswa->sekolah_id,
                    ]
                );
            }
        });

        Log::info("Pembuatan akun selesai.");
    }
}