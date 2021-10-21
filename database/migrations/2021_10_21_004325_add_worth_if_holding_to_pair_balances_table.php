<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorthIfHoldingToPairBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pair_balances', function (Blueprint $table) {
            $table->float('worth_if_holding');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pair_balances', function (Blueprint $table) {
            $table->dropColumn('worth_if_holding');
        });
    }
}
