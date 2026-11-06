<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            JenisSampahSeeder::class,
            AkunSeeder::class, // admin, pengguna, pengelola_tps, kurir
        ]);

        // User::factory(10)->create();
    }
}
