<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsReceivedToDeliveredProductsAndVariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('delivered_products', function (Blueprint $table) {
            $table->boolean('is_delivered')->default(false);
        });
        Schema::table('delivered_variants', function (Blueprint $table) {
            $table->boolean('is_delivered')->default(false);
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
        Schema::table('delivered_products', function (Blueprint $table) {
            $table->dropColumn('is_delivered');
        });
        Schema::table('delivered_variants', function (Blueprint $table) {
            $table->dropColumn('is_delivered');
        });
    }
}
