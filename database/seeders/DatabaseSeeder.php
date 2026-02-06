<?php

namespace Database\Seeders;

use App\Models\Dapodik_User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan Seeder Referensi (Termasuk RoleSeeder)
        $this->command->info('Mengisi data referensi (Agama, Pekerjaan, Pendidikan, Role)...');
        $this->call([
            ReferenceSeeder::class,
        ]);

        // 2. TRIGGER FILAMENT SHIELD
        // Menghasilkan permission untuk semua resource yang sudah kita buat
        $this->command->info('Membangun Permission Shield...');
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'app', 
            '--option' => 'policies_and_permissions',
            '--no-interaction' => true,
        ]);

        // 3. Pastikan Role Super Admin ada
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // 4. Buat User Admin Utama
        // CATATAN: Gunakan peran_id_str yang UNIK agar tidak tertimpa 
        // oleh logic 'match' di booted model (misal jangan pakai kata 'admin' saja)
        $this->command->info('Membuat akun Administrator...');
        $admin = Dapodik_User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Utama',
                'password' => Hash::make('password123'),
                'peran_id_str' => 'SUPER_ADMIN_SYSTEM', // Label khusus
                'sekolah_id' => null,
            ]
        );

        // 5. Tempelkan Role Super Admin
        // Kita gunakan syncRoles agar role lamanya (jika ada) dibersihkan
        $admin->syncRoles(['super_admin']);

        $this->command->info('-----------------------------------------');
        $this->command->info('SINKRONISASI SELESAI!');
        $this->command->info('Username : admin');
        $this->command->info('Password : password123');
        $this->command->info('-----------------------------------------');
        $this->call([
            PermissionSeeder::class,
        ]);
    }
}