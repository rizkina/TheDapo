<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 0, 'nama' => 'Tidak sekolah'],
            ['kode' => 1, 'nama' => 'PAUD'],
            ['kode' => 2, 'nama' => 'TK / sederajat'],
            ['kode' => 3, 'nama' => 'Putus SD'],
            ['kode' => 4, 'nama' => 'SD / sederajat'],
            ['kode' => 5, 'nama' => 'SMP / sederajat'],
            ['kode' => 6, 'nama' => 'SMA / sederajat'],
            ['kode' => 7, 'nama' => 'Paket A'],
            ['kode' => 8, 'nama' => 'Paket B'],
            ['kode' => 9, 'nama' => 'Paket C'],
            ['kode' => 20, 'nama' => 'D1'],
            ['kode' => 21, 'nama' => 'D2'],
            ['kode' => 22, 'nama' => 'D3'],
            ['kode' => 23, 'nama' => 'D4'],
            ['kode' => 30, 'nama' => 'S1'],
            ['kode' => 31, 'nama' => 'Profesi'],
            ['kode' => 32, 'nama' => 'Sp-1'],
            ['kode' => 35, 'nama' => 'S2'],
            ['kode' => 36, 'nama' => 'S2 Terapan'],
            ['kode' => 37, 'nama' => 'Sp-2'],
            ['kode' => 40, 'nama' => 'S3'],
            ['kode' => 41, 'nama' => 'S3 Terapan'],
            ['kode' => 90, 'nama' => 'Non formal'],
            ['kode' => 91, 'nama' => 'Informal'],
            ['kode' => 99, 'nama' => 'Lainnya'],
        ];

        DB::table('pendidikans')->insert($data);
    }
}
