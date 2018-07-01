<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreColumnsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('company_id');
            $table->string('country_code');
            $table->string('contact_number');
            $table->string('country');
            $table->string('shipping_address_2')->nullable()->change();
            $table->dateTime('date_confirmed')->nullable()->change();
            $table->dateTime('date_ordered')->nullable()->change();
            $table->dateTime('date_fulfilled')->nullable()->change();
            $table->boolean('is_confirmed')->default(false)->change();
            $table->boolean('is_fulfilled')->default(false)->change();
            $table->boolean('is_active')->default(false)->change();
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('company_id');
            $table->dropColumn('country_code');
            $table->dropColumn('contact_number');
            $table->dropColumn('country');
            $table->string('shipping_address_2')->nullable(false)->change();
            $table->dateTime('date_confirmed')->nullable(false)->change();
            $table->dateTime('date_ordered')->nullable(false)->change();
            $table->dateTime('date_fulfilled')->nullable(false)->change();
            $table->boolean('is_confirmed')->default(NULL)->change();
            $table->boolean('is_fulfilled')->default(NULL)->change();
            $table->boolean('is_active')->default(NULL)->change();
        });
    }
}
