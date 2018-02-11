<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('shipping_address_1');
            $table->string('shipping_address_2');
            $table->string('city');
            $table->integer('zip_code');
            $table->boolean('is_confirmed');
            $table->boolean('is_fulfilled');
            $table->boolean('is_active');
            $table->dateTime('date_ordered');
            $table->dateTime('date_confirmed');
            $table->dateTime('date_fulfilled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
