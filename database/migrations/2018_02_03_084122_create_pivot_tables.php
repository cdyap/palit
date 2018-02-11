<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('collection_product', function (Blueprint $table) {
            $table->integer('collection_id');
            $table->integer('product_id');
        });

        Schema::create('deliveredvariant_delivery', function (Blueprint $table) {
            $table->integer('delivery_id');
            $table->integer('delivered_variant_id');
        });

        Schema::create('cart_order', function (Blueprint $table) {
            $table->integer('cart_id');
            $table->integer('order_id');
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
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('deliveredvariant_delivery');
        Schema::dropIfExists('cart_order');
    }
}
