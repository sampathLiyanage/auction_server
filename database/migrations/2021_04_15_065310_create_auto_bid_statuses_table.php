<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoBidStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_bid_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('item_id')->unsigned();
            $table->boolean('auto_bid_enabled')->default(false);
            $table->timestamps();
        });
        Schema::table('auto_bid_statuses', function($table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('item_id')
                ->references('id')
                ->on('items');
            $table->unique(array('user_id', 'item_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_bid_statuses');
    }
}
