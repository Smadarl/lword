<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\UserGames;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'role',
    ];

    public function games()
    {
        return $this->hasMany('App\Game')->withPivot('word');
    }

    public function game_list($status = null)
    {
        if ($status == 'active')
        {
            return UserGames::fromView()->where('player_id', $this->id)->where('status', '!=', 'finished')->get();
        }
        else if ($status == 'finished')
        {
            return UserGames::fromView()->where('player_id', $this->id)->where('status', 'finished')->get();
        }
        else
        {
            return UserGames::fromView()->where('player_id', $this->id)->get();
        }
    }

    public function game($gameId)
    {
        
    }

    public function userGame($gameId)
    {
        return UserGames::fromView()->where('player_id', $this->id)->where('game_id', $gameId)->get()->first();
    }

    /*
    public function player_moves()
    {
        return $this->hasMany('App\UserMove');
    }
    */
}
