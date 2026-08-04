<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\chat\chatModel;
use App\Models\closeFriend\closeFriendModel;
use App\Models\posts\postsSaveModel;
use App\Models\user\followsModel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\posts\postModel;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'followers',
        'following',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function posts()
    {
        return $this->hasMany(postModel::class, 'user_id', 'id');
    }
    public function savedPosts()
    {
        return $this->hasMany(postsSaveModel::class, 'user_id', 'id');
    }
    public function followers()
    {
       return $this->hasMany(followsModel::class, 'follower_id', 'id');
    }
    public function closeFriend()
    {
        return $this->hasMany(closeFriendModel::class, 'friend_id', 'id');
    }
    public function Chats()
    {
        return $this->belongsToMany(chatModel::class, 'chat_members', 'user_id', 'chat_id');
    }
}
