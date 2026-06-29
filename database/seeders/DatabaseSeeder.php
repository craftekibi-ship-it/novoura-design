<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tek kullanıcı (Novoura) — şifreyi sonra .env / panelden değiştir.
        User::updateOrCreate(
            ['email' => 'novoura@design.local'],
            [
                'name' => 'Novoura',
                'password' => Hash::make('novoura'),
            ]
        );

        $this->call([
            EstoSeeder::class,     // Esto markası + menü (153)
            BrandSeeder::class,    // diğer 6 marka (voice/renk/tip)
            SermBarrSeeder::class, // Serm & Barr katalog (9 model) + voice
            VailSeeder::class,     // Vail katalog (4 model) + voice
            MoreBrandsSeeder::class, // Pureline + Dethleffs Leal + Novoura katalog/voice + Sultan=restoran
        ]);
    }
}
