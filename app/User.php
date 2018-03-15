<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

    public function game_list()
    {
        return $this->hasMany('App\UserGames');
    }

    /*
    public function player_moves()
    {
        return $this->hasMany('App\UserMove');
    }
    */
}
