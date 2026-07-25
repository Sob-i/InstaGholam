<?php

namespace App\Models\story;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class storyModel extends Model
{
    protected $table = 'stories';

    protected $fillable = [
        'user_id',
        'media',
        'media_type',
        'expires_at',
        'audience'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
