<?php

namespace Database\Seeders;

use App\Models\Dapodik_User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan; // Import ini
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan Seeder Referensi
        // Pastikan file ReferenceSeeder.php sudah benar isinya
        $this->call([
            ReferenceSeeder::class,
        ]);

        // 2. TRIGGER FILAMENT SHIELD (PENTING!)
        // Ini akan men-scan semua Resource (Siswa, Sekolah, dll) 
        // dan membuatkan permission-nya di database secara otomatis.
       $this->command->info('Membangun Permission Shield (Sabar, ini memakan waktu)...');

        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'app', // Sesuai pilihan "app" (index 0)
            '--option' => 'policies_and_permissions', // Sesuai pilihan "Policies & Permissions"
            '--no-interaction' => true, // Menghindari prompt pertanyaan
        ]);

        // 3. Buat Role Super Admin jika belum ada
        // (Biasanya sudah dibuatkan oleh command shield:generate di atas)
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // 4. Buat User Admin Utama
        $admin = Dapodik_User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Utama',
                'password' => Hash::make('password123'),
                'peran_id_str' => 'Super Admin',
            ]
        );

        // 5. Tempelkan Role ke User
        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole($role);
        }

        $this->command->info('Database berhasil di-reset!');
        $this->command->info('Username: admin | Password: password123');
    }
}