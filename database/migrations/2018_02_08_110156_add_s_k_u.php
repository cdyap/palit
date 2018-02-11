<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSKU extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('variants', function (Blueprint $table) {
            $table->string('SKU')->nullable();
            $table->softDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('SKU')->nullable();
            $table->softDeletes();
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
        Schema::table('variants', function (Blueprint $table) {
            $table->dropColumn('SKU');
            $table->dropSoftDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('SKU');
            $table->dropSoftDeletes();
        });
    }
}
