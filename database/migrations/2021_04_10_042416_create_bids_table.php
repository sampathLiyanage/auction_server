<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->increments('id');
            $table->float('amount');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('item_id')->unsigned();
            $table->boolean('is_auto_bid')->default(false);
            $table->timestamps();
        });
        Schema::table('bids', function($table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('item_id')
                ->references('id')
                ->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bids');
    }
}
