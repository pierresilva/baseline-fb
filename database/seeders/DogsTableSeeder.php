<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DogsTableSeeder extends Seeder

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

            DB::table('dogs')->insert([

                'user_id' => $faker->numberBetween(1,30),
                'name' => $faker->name(),
                'weight' => $faker->numberBetween(1,10),
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
