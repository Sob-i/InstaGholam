<?php

namespace App\Models\chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class chatModel extends Model
{
    protected $table = 'chats';

    protected $fillable = ['type','name'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_members', 'chat_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(messageModel::class, 'chat_id');
    }
}
