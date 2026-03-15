<?php

namespace App\Http\Controllers;

use App\Models\GoogleDriveConf;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class GoogleDriveController extends Controller
{
    private function setDynamicConfig($config)
    {
        if (!$config) return;

        // DINAMIS & AMAN: Menggunakan route() agar mengikuti domain/IP hosting secara otomatis
        $redirectUrl = route('google.drive.callback');

        Config::set('services.google', [
            'client_id' => trim($config->client_id),
            'client_secret' => trim($config->client_secret),
            'redirect' => $redirectUrl,
        ]);
    }

    public function connect()
    {
        $config = GoogleDriveConf::where('is_active', true)->first();
        
        // Proteksi awal agar tidak terjadi 'malformed request' ke Google
        if (!$config || !$config->client_id || !$config->client_secret) {
            return back()->with('error', 'Client ID dan Secret wajib diisi di database!');
        }

        $this->setDynamicConfig($config);

        /** @var GoogleProvider $driver */
        $driver = Socialite::driver('google');

        return $driver
            ->scopes(['https://www.googleapis.com/auth/drive.file'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function callback()
    {
        $config = GoogleDriveConf::where('is_active', true)->first();
        
        if (!$config) return redirect('/app')->with('error', 'Konfigurasi tidak ditemukan.');

        $this->setDynamicConfig($config);

        try {
            /** @var GoogleProvider $driver */
            $driver = Socialite::driver('google');
            $googleUser = $driver->user();

            // Simpan token dengan aman
            $config->update([
                'access_token' => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
            ]);

            Log::info("Google Drive: Akun berhasil dihubungkan untuk " . $config->name);

            return redirect('/app/google-drive-confs');

        } catch (\Exception $e) {
            // Mencatat error ke log agar mudah diperbaiki tanpa menebak-nebak
            Log::error("Google Drive Auth Error: " . $e->getMessage());
            
            return redirect('/app/google-drive-confs')
                ->with('error', 'Gagal menghubungkan akun: ' . $e->getMessage());
        }
    }
}