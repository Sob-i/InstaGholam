<?php

namespace App\Models\highlights;

use Illuminate\Database\Eloquent\Model;

class highlightsModel extends Model
{
    protected $table = 'highlights';

    protected $fillable = [
        'title',
        'cover',
        'user_id',
        'story_id'
    ];
}
