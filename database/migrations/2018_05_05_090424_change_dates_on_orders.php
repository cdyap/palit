<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDatesOnOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dateTimeTz('date_confirmed')->change();
            $table->dateTimeTz('date_ordered')->change();
            $table->dateTimeTz('date_fulfilled')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dateTime('date_confirmed')->change();
            $table->dateTime('date_ordered')->change();
            $table->dateTime('date_fulfilled')->change();
        });
    }
}
