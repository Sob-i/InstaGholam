<?php

namespace App\Models\posts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Types\Model\Posts;

class postsSaveModel extends Model
{
    protected $table = 'posts_saved';

    protected $fillable = ['post_id', 'user_id'];

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function post(){
        return $this->belongsTo(postModel::class, 'post_id');
    }

}
