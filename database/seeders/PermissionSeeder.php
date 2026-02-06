<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan Cache Spatie (Wajib)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Data Permissions (Berdasarkan CSV Anda)
        $permissions = [
            ['id' => 1, 'name' => 'ViewAny:DapodikConf', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'View:DapodikConf', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'Create:DapodikConf', 'guard_name' => 'web'],
            ['id' => 4, 'name' => 'Update:DapodikConf', 'guard_name' => 'web'],
            ['id' => 5, 'name' => 'Delete:DapodikConf', 'guard_name' => 'web'],
            ['id' => 6, 'name' => 'Restore:DapodikConf', 'guard_name' => 'web'],
            ['id' => 7, 'name' => 'ForceDelete:DapodikConf', 'guard_name' => 'web'],
            ['id' => 8, 'name' => 'ForceDeleteAny:DapodikConf', 'guard_name' => 'web'],
            ['id' => 9, 'name' => 'RestoreAny:DapodikConf', 'guard_name' => 'web'],
            ['id' => 10, 'name' => 'Replicate:DapodikConf', 'guard_name' => 'web'],
            ['id' => 11, 'name' => 'Reorder:DapodikConf', 'guard_name' => 'web'],
            ['id' => 12, 'name' => 'ViewAny:DapodikUser', 'guard_name' => 'web'],
            ['id' => 13, 'name' => 'View:DapodikUser', 'guard_name' => 'web'],
            ['id' => 14, 'name' => 'Create:DapodikUser', 'guard_name' => 'web'],
            ['id' => 15, 'name' => 'Update:DapodikUser', 'guard_name' => 'web'],
            ['id' => 16, 'name' => 'Delete:DapodikUser', 'guard_name' => 'web'],
            ['id' => 17, 'name' => 'Restore:DapodikUser', 'guard_name' => 'web'],
            ['id' => 18, 'name' => 'ForceDelete:DapodikUser', 'guard_name' => 'web'],
            ['id' => 19, 'name' => 'ForceDeleteAny:DapodikUser', 'guard_name' => 'web'],
            ['id' => 20, 'name' => 'RestoreAny:DapodikUser', 'guard_name' => 'web'],
            ['id' => 21, 'name' => 'Replicate:DapodikUser', 'guard_name' => 'web'],
            ['id' => 22, 'name' => 'Reorder:DapodikUser', 'guard_name' => 'web'],
            ['id' => 23, 'name' => 'ViewAny:FileCategory', 'guard_name' => 'web'],
            ['id' => 24, 'name' => 'View:FileCategory', 'guard_name' => 'web'],
            ['id' => 25, 'name' => 'Create:FileCategory', 'guard_name' => 'web'],
            ['id' => 26, 'name' => 'Update:FileCategory', 'guard_name' => 'web'],
            ['id' => 27, 'name' => 'Delete:FileCategory', 'guard_name' => 'web'],
            ['id' => 28, 'name' => 'Restore:FileCategory', 'guard_name' => 'web'],
            ['id' => 29, 'name' => 'ForceDelete:FileCategory', 'guard_name' => 'web'],
            ['id' => 30, 'name' => 'ForceDeleteAny:FileCategory', 'guard_name' => 'web'],
            ['id' => 31, 'name' => 'RestoreAny:FileCategory', 'guard_name' => 'web'],
            ['id' => 32, 'name' => 'Replicate:FileCategory', 'guard_name' => 'web'],
            ['id' => 33, 'name' => 'Reorder:FileCategory', 'guard_name' => 'web'],
            ['id' => 34, 'name' => 'ViewAny:File', 'guard_name' => 'web'],
            ['id' => 35, 'name' => 'View:File', 'guard_name' => 'web'],
            ['id' => 36, 'name' => 'Create:File', 'guard_name' => 'web'],
            ['id' => 37, 'name' => 'Update:File', 'guard_name' => 'web'],
            ['id' => 38, 'name' => 'Delete:File', 'guard_name' => 'web'],
            ['id' => 39, 'name' => 'Restore:File', 'guard_name' => 'web'],
            ['id' => 40, 'name' => 'ForceDelete:File', 'guard_name' => 'web'],
            ['id' => 41, 'name' => 'ForceDeleteAny:File', 'guard_name' => 'web'],
            ['id' => 42, 'name' => 'RestoreAny:File', 'guard_name' => 'web'],
            ['id' => 43, 'name' => 'Replicate:File', 'guard_name' => 'web'],
            ['id' => 44, 'name' => 'Reorder:File', 'guard_name' => 'web'],
            ['id' => 45, 'name' => 'ViewAny:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 46, 'name' => 'View:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 47, 'name' => 'Create:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 48, 'name' => 'Update:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 49, 'name' => 'Delete:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 50, 'name' => 'Restore:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 51, 'name' => 'ForceDelete:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 52, 'name' => 'ForceDeleteAny:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 53, 'name' => 'RestoreAny:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 54, 'name' => 'Replicate:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 55, 'name' => 'Reorder:GoogleDriveConf', 'guard_name' => 'web'],
            ['id' => 56, 'name' => 'ViewAny:Ptk', 'guard_name' => 'web'],
            ['id' => 57, 'name' => 'View:Ptk', 'guard_name' => 'web'],
            ['id' => 58, 'name' => 'Create:Ptk', 'guard_name' => 'web'],
            ['id' => 59, 'name' => 'Update:Ptk', 'guard_name' => 'web'],
            ['id' => 60, 'name' => 'Delete:Ptk', 'guard_name' => 'web'],
            ['id' => 61, 'name' => 'Restore:Ptk', 'guard_name' => 'web'],
            ['id' => 62, 'name' => 'ForceDelete:Ptk', 'guard_name' => 'web'],
            ['id' => 63, 'name' => 'ForceDeleteAny:Ptk', 'guard_name' => 'web'],
            ['id' => 64, 'name' => 'RestoreAny:Ptk', 'guard_name' => 'web'],
            ['id' => 65, 'name' => 'Replicate:Ptk', 'guard_name' => 'web'],
            ['id' => 66, 'name' => 'Reorder:Ptk', 'guard_name' => 'web'],
            ['id' => 67, 'name' => 'ViewAny:Rombel', 'guard_name' => 'web'],
            ['id' => 68, 'name' => 'View:Rombel', 'guard_name' => 'web'],
            ['id' => 69, 'name' => 'Create:Rombel', 'guard_name' => 'web'],
            ['id' => 70, 'name' => 'Update:Rombel', 'guard_name' => 'web'],
            ['id' => 71, 'name' => 'Delete:Rombel', 'guard_name' => 'web'],
            ['id' => 72, 'name' => 'Restore:Rombel', 'guard_name' => 'web'],
            ['id' => 73, 'name' => 'ForceDelete:Rombel', 'guard_name' => 'web'],
            ['id' => 74, 'name' => 'ForceDeleteAny:Rombel', 'guard_name' => 'web'],
            ['id' => 75, 'name' => 'RestoreAny:Rombel', 'guard_name' => 'web'],
            ['id' => 76, 'name' => 'Replicate:Rombel', 'guard_name' => 'web'],
            ['id' => 77, 'name' => 'Reorder:Rombel', 'guard_name' => 'web'],
            ['id' => 78, 'name' => 'ViewAny:Sekolah', 'guard_name' => 'web'],
            ['id' => 79, 'name' => 'View:Sekolah', 'guard_name' => 'web'],
            ['id' => 80, 'name' => 'Create:Sekolah', 'guard_name' => 'web'],
            ['id' => 81, 'name' => 'Update:Sekolah', 'guard_name' => 'web'],
            ['id' => 82, 'name' => 'Delete:Sekolah', 'guard_name' => 'web'],
            ['id' => 83, 'name' => 'Restore:Sekolah', 'guard_name' => 'web'],
            ['id' => 84, 'name' => 'ForceDelete:Sekolah', 'guard_name' => 'web'],
            ['id' => 85, 'name' => 'ForceDeleteAny:Sekolah', 'guard_name' => 'web'],
            ['id' => 86, 'name' => 'RestoreAny:Sekolah', 'guard_name' => 'web'],
            ['id' => 87, 'name' => 'Replicate:Sekolah', 'guard_name' => 'web'],
            ['id' => 88, 'name' => 'Reorder:Sekolah', 'guard_name' => 'web'],
            ['id' => 89, 'name' => 'ViewAny:Siswa', 'guard_name' => 'web'],
            ['id' => 90, 'name' => 'View:Siswa', 'guard_name' => 'web'],
            ['id' => 91, 'name' => 'Create:Siswa', 'guard_name' => 'web'],
            ['id' => 92, 'name' => 'Update:Siswa', 'guard_name' => 'web'],
            ['id' => 93, 'name' => 'Delete:Siswa', 'guard_name' => 'web'],
            ['id' => 94, 'name' => 'Restore:Siswa', 'guard_name' => 'web'],
            ['id' => 95, 'name' => 'ForceDelete:Siswa', 'guard_name' => 'web'],
            ['id' => 96, 'name' => 'ForceDeleteAny:Siswa', 'guard_name' => 'web'],
            ['id' => 97, 'name' => 'RestoreAny:Siswa', 'guard_name' => 'web'],
            ['id' => 98, 'name' => 'Replicate:Siswa', 'guard_name' => 'web'],
            ['id' => 99, 'name' => 'Reorder:Siswa', 'guard_name' => 'web'],
            ['id' => 100, 'name' => 'ViewAny:Role', 'guard_name' => 'web'],
            ['id' => 101, 'name' => 'View:Role', 'guard_name' => 'web'],
            ['id' => 102, 'name' => 'Create:Role', 'guard_name' => 'web'],
            ['id' => 103, 'name' => 'Update:Role', 'guard_name' => 'web'],
            ['id' => 104, 'name' => 'Delete:Role', 'guard_name' => 'web'],
            ['id' => 105, 'name' => 'Restore:Role', 'guard_name' => 'web'],
            ['id' => 106, 'name' => 'ForceDelete:Role', 'guard_name' => 'web'],
            ['id' => 107, 'name' => 'ForceDeleteAny:Role', 'guard_name' => 'web'],
            ['id' => 108, 'name' => 'RestoreAny:Role', 'guard_name' => 'web'],
            ['id' => 109, 'name' => 'Replicate:Role', 'guard_name' => 'web'],
            ['id' => 110, 'name' => 'Reorder:Role', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['id' => $p['id']], $p);
        }

        // 3. Mapping Role Has Permissions (Berdasarkan CSV Anda)
        // Saya asumsikan ID Role sesuai dengan diskusi sebelumnya:
        // 1: super_admin, 2: admin, 3: guru, 4: tenaga kependidikan, 5: siswa, 6: kepsek

        $rolePermissions = [
            [1, 1], [2, 1], [3, 1], [4, 1], [5, 1], [6, 1], [7, 1], [8, 1], [9, 1], [10, 1], [11, 1],
            [12, 1], [12, 2], [13, 1], [13, 2], [14, 1], [14, 2], [15, 1], [15, 2], [16, 1], [16, 2],
            [17, 1], [17, 2], [18, 1], [19, 1], [20, 1], [20, 2], [21, 1], [22, 1], [22, 2], [23, 1],
            [23, 2], [24, 1], [24, 2], [25, 1], [25, 2], [26, 1], [26, 2], [27, 1], [27, 2], [28, 1],
            [28, 2], [29, 1], [30, 1], [31, 1], [31, 2], [32, 1], [32, 2], [33, 1], [33, 2], [34, 1],
            [34, 2], [34, 3], [34, 4], [34, 5], [34, 6], [35, 1], [35, 2], [35, 3], [35, 4], [35, 5],
            [35, 6], [36, 1], [36, 2], [36, 3], [36, 4], [36, 5], [36, 6], [37, 1], [37, 2], [37, 3],
            [37, 4], [37, 5], [37, 6], [38, 1], [38, 2], [38, 3], [38, 4], [38, 5], [38, 6], [39, 1],
            [39, 2], [39, 3], [39, 4], [39, 5], [39, 6], [40, 1], [41, 1], [42, 1], [42, 2], [42, 4],
            [42, 5], [42, 6], [43, 1], [43, 2], [43, 5], [44, 1], [44, 2], [44, 3], [44, 5], [44, 6],
            [45, 1], [45, 2], [46, 1], [46, 2], [47, 1], [47, 2], [48, 1], [48, 2], [49, 1], [49, 2],
            [50, 1], [50, 2], [51, 1], [52, 1], [53, 1], [53, 2], [54, 1], [55, 1], [55, 2], [56, 1],
            [56, 2], [56, 3], [56, 4], [56, 6], [57, 1], [57, 2], [57, 3], [57, 4], [57, 6], [58, 1],
            [58, 2], [59, 1], [59, 2], [60, 1], [61, 1], [62, 1], [63, 1], [64, 1], [65, 1], [66, 1],
            [67, 1], [67, 2], [67, 3], [67, 4], [67, 5], [67, 6], [68, 1], [68, 2], [68, 3], [68, 4],
            [68, 5], [68, 6], [69, 1], [69, 2], [70, 1], [70, 2], [71, 1], [72, 1], [73, 1], [74, 1],
            [75, 1], [76, 1], [77, 1], [78, 1], [78, 2], [78, 3], [78, 4], [78, 5], [78, 6], [79, 1],
            [79, 2], [79, 3], [79, 4], [79, 5], [79, 6], [80, 1], [80, 2], [81, 1], [81, 2], [82, 1],
            [83, 1], [84, 1], [85, 1], [86, 1], [87, 1], [88, 1], [89, 1], [89, 2], [89, 3], [89, 4],
            [89, 5], [89, 6], [90, 1], [90, 2], [90, 3], [90, 4], [90, 5], [90, 6], [91, 1], [91, 2],
            [92, 1], [92, 2], [92, 5], [93, 1], [94, 1], [94, 2], [95, 1], [96, 1], [97, 1], [98, 1],
            [99, 1], [100, 1], [101, 1], [102, 1], [103, 1], [104, 1], [105, 1], [106, 1], [107, 1],
            [108, 1], [109, 1], [110, 1]
        ];

        foreach ($rolePermissions as $map) {
            DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $map[0],
                'role_id' => $map[1]
            ]);
        }

        $this->command->info('Semua Permission dan Mapping Role berhasil disinkronkan!');
    }
}