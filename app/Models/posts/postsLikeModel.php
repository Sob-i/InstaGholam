<?php

namespace App\Models\posts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class postsLikeModel extends Model
{
    protected $table = 'posts_likes';

    protected $fillable = ['user_id', 'post_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(postModel::class);
    }
}
