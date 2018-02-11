<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvailableColumns extends Migration
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
            $table->boolean('is_available')->default(false);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_available')->default(false);
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });
    }
}
