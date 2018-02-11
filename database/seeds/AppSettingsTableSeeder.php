<?php

use Illuminate\Database\Seeder;

class AppSettingsTableSeeder extends Seeder
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
            'name' => "currency",
            'value' => "Philippine Peso",
            'value_2' => "₱",
            'value_3' => "PHP"
        ],[
            'name' => "currency",
            'value' => "US Dollar",
            'value_2' => "$",
            'value_3' => "USD"
        ]]);
    }
}
