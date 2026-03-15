<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan Cache Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Import Data Permissions dari JSON
        $permissionsPath = database_path('data/permissions.json');
        
        if (File::exists($permissionsPath)) {
            $permissions = json_decode(File::get($permissionsPath), true);
            
            foreach ($permissions as $p) {
                // Gunakan updateOrCreate agar data yang ada diperbarui, yang baru ditambah
                Permission::updateOrCreate(
                    ['id' => $p['id']], 
                    [
                        'name' => $p['name'],
                        'guard_name' => $p['guard_name'] ?? 'web',
                    ]
                );
            }
            $this->command->info('Data Permissions berhasil di-import dari JSON.');
        }

        // 3. FIX POSTGRESQL SEQUENCE (Kunci agar tidak ERROR Unique Violation)
        if (config('database.default') === 'pgsql') {
            $maxId = DB::table('permissions')->max('id') ?? 0;
            // Kita set sequence ke ID terbesar saat ini
            DB::statement("SELECT setval('permissions_id_seq', $maxId)");
            $this->command->info("Sequence PostgreSQL permissions_id_seq direset ke: $maxId");
        }

        // 4. Import Mapping Role Has Permissions dari JSON
        $pivotPath = database_path('data/role_has_permissions.json');
        
        if (File::exists($pivotPath)) {
            $rolePermissions = json_decode(File::get($pivotPath), true);
            
            foreach ($rolePermissions as $map) {
                // Kita gunakan DB table langsung untuk kecepatan (tabel pivot)
                DB::table('role_has_permissions')->updateOrInsert([
                    'permission_id' => $map['permission_id'],
                    'role_id' => $map['role_id']
                ]);
            }
            $this->command->info('Data Mapping Role-Permission berhasil di-import.');
        }
    }
}