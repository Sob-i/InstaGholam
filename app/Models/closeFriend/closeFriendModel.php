<?php

namespace App\Models\closeFriend;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class closeFriendModel extends Model
{
    protected $table = 'close_friend';

    protected $fillable = ['user_id', 'friend_id'];

    public function userInfo()
    {
        return $this->hasMany(User::class, 'id', 'friend_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
