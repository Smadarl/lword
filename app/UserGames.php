<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserGames extends Model
{
    public function scopeFromView($query)
    {
        return $query->from('user_games');
    }
}
