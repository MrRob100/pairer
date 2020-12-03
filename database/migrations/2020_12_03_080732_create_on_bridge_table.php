<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnBridgeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_bridge', function (Blueprint $table) {
            $table->id();
            $table->string('bridge_pair');
            $table->string('bridge_side');
            $table->string('bridge_amount');
            $table->string('bridge_price');
            $table->string('bridge_order_id');
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
        Schema::dropIfExists('hits');
    }
}
