<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_moves', function (Blueprint $table) {
            $table->integer('game_id');
            $table->integer('user_id');
            $table->string('guess', 40);
            $table->tinyInteger('result');
            $table->timestamps();
            $table->index(['user_id', 'game_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_moves');
    }
}
