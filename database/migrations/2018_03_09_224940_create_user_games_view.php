<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGamesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW `user_games`  AS
        (
            select `p1`.`id` AS `player_id`,`g`.`id` AS `game_id`,`g`.`max_length` AS `max_length`,
                   `g`.`max_recurrance` AS `max_recurrance`,`g`.`started_by` AS `started_by`,
                   `g`.`started_at` AS `started_at`,`g`.`turn` AS `turn`,`g`.`status` AS `status`,
                   `g`.`created_at` AS `created_at`,`g`.`updated_at` AS `updated_at`,
                   `p2`.`id` AS `opponent_id`,`p2`.`name` AS `opponent_name`, `gp1`.`letters` AS `letters`
            from
            (
                (
                    (
                        (
                            `users` `p1`
                                join `game_user` `gp1` on ((`gp1`.`user_id` = `p1`.`id`))
                        )
                        join `games` `g` on ((`g`.`id` = `gp1`.`game_id`))
                    )
                    join `game_user` `gp2` on (( (`gp2`.`game_id` = `g`.`id`) and (`gp2`.`user_id` <> `p1`.`id`) ))
                )
                join `users` `p2` on ((`p2`.`id` = `gp2`.`user_id`))
            )
        ) ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW user_games");
    }
}
