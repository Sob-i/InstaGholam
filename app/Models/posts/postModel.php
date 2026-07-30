<?php

namespace App\Models\posts;

use App\Models\reports\reportModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\comments\commentModel;

class postModel extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'post_files',
        'post_caption',
        'post_tags',
        'post_location',
        'post_audience',
        'post_likes',
        'post_comments',
        'comment_status',
        'like_count',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }
    public function comments()
    {
        return $this->hasMany(commentModel::class, 'post_id', 'id');
    }
    public function likes()
    {
        return $this->hasMany(postsLikeModel::class, 'post_id');
    }
    public function isLikedByUser($userId = null)
    {
        if (!$userId && auth()->check()) {
            $userId = auth()->id();
        }

        if (!$userId) return false;

        return $this->likes()->where('user_id', $userId)->exists();
    }
    public function saves()
    {
        return $this->hasMany(postsSaveModel::class , 'post_id');
    }
    public function isSavedByUser($userId = null)
    {
        if (!$userId && auth()->check()) {
            $userId = auth()->id();
        }
        if (!$userId) return false;
        return $this->saves()->where('user_id', $userId)->exists();
    }

}
