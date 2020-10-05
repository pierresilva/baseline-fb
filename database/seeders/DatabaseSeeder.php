<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(\UsersTableSeeder::class);
        $this->call(\CompaniesTableSeeder::class);
        $this->call(\HobbiesTableSeeder::class);
        $this->call(\DogsTableSeeder::class);
        $this->call(\HobbyUserTableSeeder::class);
    }
}
