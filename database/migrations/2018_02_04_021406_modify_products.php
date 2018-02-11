<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyProducts extends Migration
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
            $table->boolean('is_shipped')->default(false);
            $table->string('currency')->nullable();
            $table->double('price')->nullable();
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
            $table->dropColumn('is_shipped');
            $table->dropColumn('currency');
            $table->dropColumn('price');
        });
    }
}
