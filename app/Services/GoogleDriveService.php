<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\GoogleDriveConf;
use Illuminate\Support\Facades\Config;


class GoogleDriveService
{
    /**
     * Menyuntikkan konfigurasi dari Database ke Filesystem Laravel
     */
    public static function applyConfig(?GoogleDriveConf $config = null)
    {
        // Jika tidak ada record yang dipassing, ambil yang aktif di DB
        $config = $config ?? GoogleDriveConf::where('is_active', true)->first();

        if ($config && $config->refresh_token) {
            Config::set('filesystems.disks.google', [
                'driver' => 'google',
                'clientId' => $config->client_id,
                'clientSecret' => $config->client_secret,
                'refreshToken' => $config->refresh_token,
                'folderId' => $config->folder_id,
            ]);
            return true;
        }
        return false;
    }
    
    // ... method applyConfig yang sudah ada ...

    /**
     * Fungsi untuk mengetes koneksi secara mandiri menggunakan data mentah
     */
    public static function testConnectivity(): array
    {
        try {
            // Gunakan string tanpa backslash di depan untuk pengecekan
            $className = 'Masbug\Flysystem\GoogleDriveAdapter';

            if (!class_exists($className)) {
                return [
                    'success' => false, 
                    'message' => 'Sistem tidak menemukan Library Google Drive. Silakan jalankan "composer dump-autoload -o" lalu restart Laragon.'
                ];
            }

            if (!self::applyConfig()) {
                return [
                    'success' => false, 
                    'message' => 'Status Akun belum Terhubung. Klik tombol Hubungkan Akun Google dulu.'
                ];
            }

            // Tes akses ke Google Drive
            \Illuminate\Support\Facades\Storage::disk('google')->files(); 

            return [
                'success' => true,
                'message' => 'Koneksi Berhasil! Driver google aktif.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi Gagal: ' . $e->getMessage()
            ];
        }
    }

}