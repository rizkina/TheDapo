<?php

namespace App\Services;

use App\Models\DapodikConf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response; // Tambahkan import ini

class DapodikService
{
    public function getConfig()
    {
        return DapodikConf::where('is_active', true)->first();
    }

    /**
     * Fungsi Cek Koneksi (Ping)
     */
    public function testConnection($url, $token, $npsn): array
    {
        try {
            $apiUrl = rtrim($url, '/') . '/getSekolah';

            /** @var Response $response */
            $response = Http::withToken($token)
                ->timeout(10)
                ->withoutVerifying()
                ->get($apiUrl, [
                    'npsn' => $npsn
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Koneksi Berhasil! Data Sekolah terdeteksi.',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal: ' . $response->status() . ' - ' . $response->reason()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: Tidak dapat menjangkau server. (' . $e->getMessage() . ')'
            ];
        }
    }

    /**
     * Fungsi Dasar Get Data (Generic)
     */
    public function fetchData($endpoint, $params = [])
    {
        $config = $this->getConfig();

        if (!$config) {
            throw new \Exception("Tidak ada konfigurasi Dapodik yang aktif.");
        }

        $url = rtrim($config->base_url, '/') . '/' . ltrim($endpoint, '/');

        /** @var Response $response */
        $response = Http::withToken($config->token)
            ->timeout(60)
            ->withoutVerifying()
            ->get($url, array_merge(['npsn' => $config->npsn], $params));

        if ($response->successful()) {
            return $response->json();
        }

        Log::error("Dapodik API Error [{$endpoint}]: " . $response->body());
        return null;
    }
}