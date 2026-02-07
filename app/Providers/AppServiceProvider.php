<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter as LaravelAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Support\Facades\Gate;
use App\Models\Dapodik_User;
use Exception;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            Storage::extend('google', function ($app, $config) {
                $client = new GoogleClient();
                $client->setClientId($config['clientId'] ?? '');
                $client->setClientSecret($config['clientSecret'] ?? '');
                $client->refreshToken($config['refreshToken'] ?? '');

                $service = new GoogleDrive($client);
                
                // Pastikan folderId bersih dari spasi dan tidak null
                $rootFolderId = !empty($config['folderId']) ? trim($config['folderId']) : 'root';

                // 1. Buat Adapter
                // Parameter kedua adalah ID folder yang akan dianggap sebagai "/" (root) oleh aplikasi
                $adapter = new GoogleDriveAdapter($service, $rootFolderId);
                
                // 2. Buat Operator Flysystem (Standar V3)
                $operator = new Filesystem($adapter);

                // 3. Kembalikan instance LaravelAdapter
                // Penting: Sertakan $config agar metadata seperti 'url' bisa diproses Laravel
                return new LaravelAdapter($operator, $adapter, $config);
            });
        } catch (\Exception $e) {
            // Log error jika extend gagal saat booting
            \Illuminate\Support\Facades\Log::error("Gagal mendaftarkan Driver Google Drive: " . $e->getMessage());
        }

         Gate::before(function (Dapodik_User $user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}