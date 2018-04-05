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
            $table->index(['game_id', 'user_id']);
            $table->index(['user_id']);
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
