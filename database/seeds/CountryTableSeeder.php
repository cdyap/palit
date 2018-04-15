<?php

use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('app_settings')->insert([[
            'name' => "country",
            'value' => "Philippines",
            'value_2' => null,
            'value_3' => null
        ]]);
    }
}
