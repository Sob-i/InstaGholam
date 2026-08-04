<?php

namespace App\Models\chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class messageModel extends Model
{
    protected $table = 'messages';

    protected $fillable = ['chat_id' , 'sender_id' , 'receiver_id' , 'message' , 'attachments' , 'type'];

    public function chat()
    {
        return $this->belongsTo(chatModel::class, 'chat_id');
    }
    public function sender()
    {
        return $this->hasOne(User::class , 'id' , 'sender_id');
    }
    public function receiver()
    {
        return $this->hasOne(User::class , 'id' , 'receiver_id');
    }
}
