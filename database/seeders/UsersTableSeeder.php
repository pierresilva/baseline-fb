<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UsersTableSeeder extends Seeder

{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $faker = Faker\Factory::create();

        for($i=0;$i<30;$i++){

            DB::table('users')->insert([

                'company_id' => $faker->numberBetween(1,30),
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => $faker->password(),
                'birth_day' => $faker->date(),
                'phone' => $faker->phoneNumber(),
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
