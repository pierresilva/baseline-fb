<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CompaniesTableSeeder extends Seeder

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

            DB::table('companies')->insert([

                'name' => $faker->company(),
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
