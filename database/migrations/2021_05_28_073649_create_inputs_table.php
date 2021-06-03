<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pair_id')->nullable()->constrained('pairs');
            $table->string('symbol1');
            $table->float('amount1', 10, 5);
            $table->float('amount1_usd');
            $table->string('symbol2');
            $table->float('amount2', 10, 5);
            $table->float('amount2_usd');
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
        Schema::dropIfExists('inputs');
    }
}
