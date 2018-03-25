<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerMoves extends Model
{
    protected $table = 'user_moves';

    public function player()
    {
        return $this->belongsTo('App\User');
    }

    public function game()
    {
        return $this->belongsTo('App\Game');
    }
}
