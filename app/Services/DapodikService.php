<?php

namespace App\Services;

use App\Models\DapodikConf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class DapodikService
{
    /**
     * Memastikan URL memiliki format http://ip:port/WebService
     */
    private function prepareBaseUrl($url): string
    {
        $url = rtrim($url, '/');
        
        // Jika user lupa memasukkan /WebService, kita tambahkan secara otomatis
        if (!str_contains($url, '/WebService')) {
            $url .= '/WebService';
        }
        
        return $url;
    }

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
            // Memastikan URL menjadi http://ip:port/WebService/getSekolah
            $apiUrl = $this->prepareBaseUrl($url) . '/getSekolah';

            /** @var Response $response */
            $response = Http::withToken($token)
                ->timeout(10)
                ->withoutVerifying()
                ->get($apiUrl, [
                    'npsn' => $npsn // Laravel akan mengubah ini menjadi ?npsn=xxxx
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

        // Contoh hasil: http://172.20.1.252:5774/WebService/getPesertaDidik
        $url = $this->prepareBaseUrl($config->base_url) . '/' . ltrim($endpoint, '/');

        /** @var Response $response */
        $response = Http::withToken($config->token)
            ->timeout(120) // Tarik data banyak butuh waktu lebih lama
            ->withoutVerifying()
            ->get($url, array_merge(['npsn' => $config->npsn], $params));

        if ($response->successful()) {
            return $response->json();
        }

        Log::error("Dapodik API Error [{$endpoint}]: " . $response->body());
        return null;
    }
}