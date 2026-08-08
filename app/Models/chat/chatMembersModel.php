<?php

namespace App\Models\chat;

use Illuminate\Database\Eloquent\Model;

class chatMembersModel extends Model
{
    protected $table = 'chat_members';

    protected $fillable = ['chat_id','user_id'];

}
