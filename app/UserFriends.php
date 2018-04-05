<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFriends extends Model
{
    public function scopeFromView($query)
    {
        return $query->from('user_friends');
    }
}
