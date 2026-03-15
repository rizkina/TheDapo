<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 1, 'nama' => 'Tidak bekerja'],
            ['kode' => 2, 'nama' => 'Nelayan'],
            ['kode' => 3, 'nama' => 'Petani'],
            ['kode' => 4, 'nama' => 'Peternak'],
            ['kode' => 5, 'nama' => 'PNS/TNI/Polri'],
            ['kode' => 6, 'nama' => 'Karyawan Swasta'],
            ['kode' => 7, 'nama' => 'Pedagang Kecil'],
            ['kode' => 8, 'nama' => 'Pedagang Besar'],
            ['kode' => 9, 'nama' => 'Wiraswasta'],
            ['kode' => 10, 'nama' => 'Wirausaha'],
            ['kode' => 11, 'nama' => 'Buruh'],
            ['kode' => 12, 'nama' => 'Pensiunan'],
            ['kode' => 13, 'nama' => 'Tenaga Kerja Indonesia'],
            ['kode' => 90, 'nama' => 'Tidak dapat diterapkan'],
            ['kode' => 98, 'nama' => 'Sudah Meninggal'],
            ['kode' => 99, 'nama' => 'Lainnya'],
            ['kode' => 14, 'nama' => 'Karyawan BUMN'],
        ];

        // Urutkan berdasarkan kode agar lebih rapi
        usort($data, function ($a, $b) {
            return $a['kode'] <=> $b['kode'];
        });

        DB::table('pekerjaans')->insert($data);
    }
}
