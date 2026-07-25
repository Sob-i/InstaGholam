<?php

namespace App\Models\user;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class followsModel extends Model
{
    protected $table = 'follows';

    protected $fillable = ['follower_id', 'followed_id','status'];

    public function userInfo()
    {
        return $this->belongsTo(User::class, 'followed_id');
    }
}
