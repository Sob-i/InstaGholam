<?php

namespace App\Models\chat;

use Illuminate\Database\Eloquent\Model;

class messageReadsModel extends Model
{
    protected $table = 'message_reads';

    protected $fillable = ['message_id', 'user_id', 'read_at'];
}
