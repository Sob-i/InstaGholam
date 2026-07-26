<?php

namespace App\Models\notifications;

use App\Models\posts\postModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class notificationModel extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'post_id',
        'target_user_id',
        'message',
        'type',
    ];

    public function post()
    {
        return $this->belongsTo(postModel::class, 'post_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
