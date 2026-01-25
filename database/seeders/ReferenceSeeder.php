<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->call([
            // UserSeeder::class,
            AgamaSeeder::class,
            PendidikanSeeder::class,
            PekerjaanSeeder::class,
            PenghasilanSeeder::class,
        ]);
    }
}
