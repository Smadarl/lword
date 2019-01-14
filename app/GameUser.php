<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameUser extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'game_user';
    protected $primaryKey = ['game_id', 'user_id'];
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['game_id', 'user_id', 'word', 'result'];

    static public function findPlayerByUserGame(UserGames $userGame)
    {
        return self::where('game_id', $userGame->game_id)->where('user_id', $userGame->player_id)->get()->first();
    }

    static public function findOpponentByUserGame(UserGames $userGame)
    {
        return self::where('game_id', $userGame->game_id)->where('user_id', $userGame->opponent_id)->get()->first();
    }
}
