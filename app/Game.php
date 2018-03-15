<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['max_length', 'max_recurrance', 'started_by', 'turn', 'status'];

    public function players()
    {
        return $this->belongsToMany('App\User')->withPivot('word');
    }
}
