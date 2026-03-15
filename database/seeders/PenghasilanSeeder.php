<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenghasilanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 11, 'nama' => 'Kurang dari Rp. 500,000'],
            ['kode' => 12, 'nama' => 'Rp. 500,000 - Rp. 999,999'],
            ['kode' => 13, 'nama' => 'Rp. 1,000,000 - Rp. 1,999,999'],
            ['kode' => 14, 'nama' => 'Rp. 2,000,000 - Rp. 4,999,999'],
            ['kode' => 15, 'nama' => 'Rp. 5,000,000 - Rp. 20,000,000'],
            ['kode' => 16, 'nama' => 'Lebih dari Rp. 20,000,000'],
            ['kode' => 99, 'nama' => 'Tidak Berpenghasilan'],
        ];

        DB::table('penghasilans')->insert($data);
    }
}
