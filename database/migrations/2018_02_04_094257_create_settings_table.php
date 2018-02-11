<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('name');
            $table->string('value');
            $table->string('value_2')->nullable();
            $table->string('value_3')->nullable();
        });

        Schema::table('variants', function (Blueprint $table) {
            $table->dropColumn('size');
            $table->dropColumn('color');
            $table->string('attribute_1')->nullable();
            $table->string('attribute_2')->nullable();
            $table->string('attribute_3')->nullable();
            $table->string('attribute_4')->nullable();
            $table->string('attribute_5')->nullable();
            $table->integer('company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');

        Schema::table('variants', function (Blueprint $table) {
            $table->string('size');
            $table->string('color');
            $table->dropColumn('attribute_1');
            $table->dropColumn('attribute_2');
            $table->dropColumn('attribute_3');
            $table->dropColumn('attribute_4');
            $table->dropColumn('attribute_5');
            $table->dropColumn('company_id');
        });
    }
}
