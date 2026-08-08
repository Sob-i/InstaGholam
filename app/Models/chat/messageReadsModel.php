<?php

namespace App\Models\chat;

use Illuminate\Database\Eloquent\Model;

class messageReadsModel extends Model
{
    protected $table = 'messages_reads';

    public $timestamps = false;

    protected $fillable = ['message_id', 'user_id', 'read_at'];
}
