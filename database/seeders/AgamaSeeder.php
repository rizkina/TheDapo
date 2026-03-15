<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 1, 'nama' => 'Islam'],
            ['kode' => 2, 'nama' => 'Kristen'],
            ['kode' => 3, 'nama' => 'Katholik'],
            ['kode' => 4, 'nama' => 'Hindu'],
            ['kode' => 5, 'nama' => 'Budha'],
            ['kode' => 6, 'nama' => 'Khonghucu'],
            ['kode' => 7, 'nama' => 'Kepercayaan kpd Tuhan YME'],
            ['kode' => 99, 'nama' => 'lainnya'],
        ];

        DB::table('agamas')->insert($data);

    }
}
