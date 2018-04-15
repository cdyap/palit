<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIsDeliveredToDeliveredQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivered_variants', function (Blueprint $table) {
            //
            $table->dropColumn('is_delivered');
            $table->integer('delivered_quantity')->default(0);
        });

        Schema::table('delivered_products', function (Blueprint $table) {
            //
            $table->dropColumn('is_delivered');
            $table->integer('delivered_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivered_variants', function (Blueprint $table) {
            //
            $table->dropColumn('delivered_quantity');
            $table->boolean('is_delivered')->default(false);
        });

        Schema::table('delivered_products', function (Blueprint $table) {
            //
            $table->dropColumn('delivered_quantity');
            $table->boolean('is_delivered')->default(false);
        });
    }
}
