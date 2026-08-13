<?php

namespace App\Models\comments;

use App\Models\posts\postModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class commentModel extends Model
{
        protected $table = 'comments';
        protected $fillable = [
            'user_id',
            'post_id',
            'content',
            'type',
            'reply_comment_id',
        ];

        public function user(){
            return $this->belongsTo(User::class, 'user_id','id');
        }

        public function post(){
           return $this->belongsTo(postModel::class, 'post_id','id');
        }
}
