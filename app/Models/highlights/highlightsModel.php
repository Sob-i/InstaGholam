<?php

namespace App\Models\highlights;

use App\Models\story\storyModel;
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

    public function stories()
    {
        return $this->hasMany(storyModel::class , 'id','story_id');
    }
}
