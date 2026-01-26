<?php

namespace App\Http\Controllers;

use App\Models\GoogleDriveConf;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Illuminate\Support\Facades\Config;

class GoogleDriveController extends Controller
{
    private function setDynamicConfig($config)
    {
         Config::set('services.google', [
            'client_id' => trim($config->client_id),
            'client_secret' => trim($config->client_secret),
            'redirect' => url('/google-drive/callback'),
        ]);
    }

    public function connect()
    {
        $config = GoogleDriveConf::where('is_active', true)->first();
        
        if (!$config || !$config->client_id) {
            return "Error: Client ID belum diisi!";
        }

        $this->setDynamicConfig($config);

        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');

        return $driver
            ->scopes(['https://www.googleapis.com/auth/drive.file'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function callback()
    {
        $config = GoogleDriveConf::where('is_active', true)->first();
        $this->setDynamicConfig($config);

        $googleUser = Socialite::driver('google')->user();

        $config->update([
            'access_token' => $googleUser->token,
            'refresh_token' => $googleUser->refreshToken,
        ]);

        return redirect('/app/google-drive-confs');
    }
}