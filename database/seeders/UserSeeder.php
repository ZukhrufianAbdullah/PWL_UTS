<?php

namespace Database\Seeders;

use App\Models\MUser;
use App\Models\MLevel;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminLevel = MLevel::where('level_kode', 'ADM')->first();
        $kasirLevel = MLevel::where('level_kode', 'KSR')->first();

        MUser::create([
            'level_id' => $adminLevel->level_id,
            'username' => 'admin',
            'nama' => 'Administrator',
            'password' => '12345',
        ]);

        MUser::create([
            'level_id' => $kasirLevel->level_id,
            'username' => 'kasir1',
            'nama' => 'Kasir Satu',
            'password' => '12345',
        ]);
    }
}
