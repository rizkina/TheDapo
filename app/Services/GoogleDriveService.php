<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class GoogleDriveService
{
    // ... method applyConfig yang sudah ada ...

    /**
     * Fungsi untuk mengetes koneksi secara mandiri menggunakan data mentah
     */
    public static function testConnectivity(string $json, string $folderId): array
    {
        try {
            $jsonArray = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false, 'message' => 'Format JSON tidak valid!'];
            }

            // Kita buat disk temporary (on-the-fly) untuk ngetes
            $config = [
                'driver' => 'google',
                'serviceAccountJson' => $jsonArray,
                'folderId' => $folderId,
            ];

            // Build temporary disk
            $disk = Storage::build($config);

            // Coba ambil daftar file (hanya untuk ngetes koneksi)
            $disk->files(); 

            return [
                'success' => true, 
                'message' => 'Koneksi Berhasil! Aplikasi dapat mengakses folder Google Drive Anda.'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false, 
                'message' => 'Koneksi Gagal: ' . $e->getMessage()
            ];
        }
    }
}