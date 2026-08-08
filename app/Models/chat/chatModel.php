<?php

namespace App\Models\chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
    public function lastMessage()
    {
        return $this->hasOne(messageModel::class, 'chat_id')->latestOfMany();
    }
    public function getLastReadMessageCountAttribute()
    {
        $userId = Auth::id();
        $lastRead = chatMembersModel::where('user_id',$userId)->where('chat_id',$this->id)->first();
        return messageModel::where('chat_id', $this->id)->where('sender_id','!=', $userId)->where('id','>',$lastRead->last_read ?? 0)->count();
    }

}
