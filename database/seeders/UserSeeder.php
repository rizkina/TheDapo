<?php

namespace Database\Seeders;

use App\Models\Dapodik_User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role super_admin tersedia
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // 2. Buat User Admin Pertama
        // Menggunakan updateOrCreate agar tidak terjadi error jika seeder dijalankan 2x
        $user = Dapodik_User::updateOrCreate(
            ['username' => 'admin'], // Cari berdasarkan username
            [
                'nama' => 'Super Admin',
                'password' => Hash::make('password123'), // Ganti dengan password pilihan Anda
                'peran_id_str' => 'Administrator',
            ]
        );

        // 3. Tempelkan Role ke User
        $user->assignRole($role);
        
        $this->command->info('User Admin berhasil dibuat. Username: admin | Password: password123');
    }
}