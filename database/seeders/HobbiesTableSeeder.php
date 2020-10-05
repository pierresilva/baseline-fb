<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class HobbiesTableSeeder extends Seeder

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

            DB::table('hobbies')->insert([

                'name' => $faker->word(),
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
