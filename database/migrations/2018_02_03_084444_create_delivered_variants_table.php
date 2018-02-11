<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveredVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivered_variants', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('delivery_id');
            $table->integer('variant_id');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivered_variants');
    }
}
