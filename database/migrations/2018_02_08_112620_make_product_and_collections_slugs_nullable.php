<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeProductAndCollectionsSlugsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('products', function (Blueprint $table) {
            $table->integer('quantity')->nullable();
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
            $table->dropColumn('quantity');
        });

        // Schema::table('collections', function (Blueprint $table) {
        //     $table->string('slug')->nullable($value = false)->change();
        // });
    }
}
