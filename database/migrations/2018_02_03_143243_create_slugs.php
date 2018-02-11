<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlugs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('companies', function (Blueprint $table) {
            $table->string('slug')->unique();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->unique();
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->string('slug')->unique();
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
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
