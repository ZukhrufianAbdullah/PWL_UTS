<?php

namespace Database\Seeders;

use App\Models\MLevel;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['level_kode' => 'ADM', 'level_nama' => 'Administrator'],
            ['level_kode' => 'KSR', 'level_nama' => 'Kasir'],
            ['level_kode' => 'MGR', 'level_nama' => 'Manager'],
        ];

        foreach ($levels as $level) {
            MLevel::create($level);
        }
    }
}
