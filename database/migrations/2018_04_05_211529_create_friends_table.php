<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('friend_id');
            $table->enum('status', ['requested', 'confirmed', 'rejected']);
            $table->unique(['user_id', 'friend_id']);
            $table->index(['friend_id']);
        });

        DB::statement("CREATE VIEW `user_friends`  AS
        (
            select `me`.`id` AS `user_id`,`friend`.`id` AS `friend_id`,`friend`.`name` AS `friend_name`
            from
            (
                (
                    `users` `me`
                        join `friends` `f` on ((`f`.`user_id` = `me`.`id`))
                )
                join `users` `friend` on ((`friend`.`id` = `f`.`friend_id`))
            )
        );");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friends');
        DB::statement("DROP VIEW user_friends");
    }
}
