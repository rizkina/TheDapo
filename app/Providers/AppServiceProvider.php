<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter as LaravelAdapter;
use League\Flysystem\Filesystem as Flysystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;

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
                
                $folderId = $config['folderId'] ?? '/';

                // 1. Definisikan Adapter dengan Namespace yang BENAR
                // Kita beri komentar @var agar IDE tahu ini mengimplementasikan interface Flysystem
                /** @var \League\Flysystem\FilesystemAdapter $adapter */
                $adapter = new GoogleDriveAdapter($service, $folderId);
                
                // 2. Buat Operator Flysystem
                $operator = new Flysystem($adapter);

                // 3. Kembalikan FilesystemAdapter milik Laravel
                return new LaravelAdapter($operator, $adapter, $config);
            });
        } catch (\Exception $e) {
            // Biarkan kosong untuk keamanan booting
        }
    }
}