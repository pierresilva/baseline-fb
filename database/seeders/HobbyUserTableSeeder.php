<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class HobbyUserTableSeeder extends Seeder

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

            DB::table('hobby_user')->insert([

                'hobby_id' => $faker->numberBetween(1,30),
                'user_id' => $faker->numberBetween(1,30),
                'skill_level' => $faker->numberBetween(1,30),
                'firend_name' => $faker->name(),
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
