<?php

namespace Database\Seeders;

use App\Models\Dapodik_User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan perintah storage:link secara otomatis
        $this->command->info('Mengecek tautan storage (storage:link)...');
        
        // Kita cek dulu apakah folder storage sudah ada di public
        if (!File::exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');
                $this->command->info('Tautan storage berhasil dibuat.');
            } catch (\Exception $e) {
                $this->command->warn('Gagal membuat storage:link otomatis. Pastikan Anda menjalankan terminal sebagai Administrator.');
            }
        } else {
            $this->command->info('Tautan storage sudah ada, melewati langkah ini.');
        }

        // 2. Jalankan Seeder Referensi (Termasuk RoleSeeder)
        $this->command->info('Mengisi data referensi (Agama, Pekerjaan, Pendidikan, Role)...');
        $this->call([
            ReferenceSeeder::class, // Pastikan Seeder ini sudah berisi RoleSeeder
        ]);

        // 3. TRIGGER FILAMENT SHIELD
        $this->command->info('Membangun Permission Shield...');
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'app', 
            '--option' => 'policies_and_permissions',
            '--no-interaction' => true,
        ]);

        // 4. Pastikan Role Super Admin ada
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // 5. Buat User Admin Utama
        $this->command->info('Membuat akun Administrator...');
        $admin = Dapodik_User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Utama',
                'password' => Hash::make('password123'),
                'peran_id_str' => 'SUPER_ADMIN_SYSTEM',
                'sekolah_id' => null,
            ]
        );

        // 6. Tempelkan Role Super Admin
        $admin->syncRoles(['super_admin']);

        $this->command->info('-----------------------------------------');
        $this->command->info('SINKRONISASI SELESAI!');
        $this->command->info('Username : admin');
        $this->command->info('Password : password123');
        $this->command->info('-----------------------------------------');
    }
}