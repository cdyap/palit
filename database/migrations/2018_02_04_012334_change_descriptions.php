<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDescriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('collections', function (Blueprint $table) {
            $table->text('description')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('collections', function (Blueprint $table) {
            $table->string('description')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('description')->change();
        });
    }
}
