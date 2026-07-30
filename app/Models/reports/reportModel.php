<?php

namespace App\Models\reports;

use App\Models\posts\postModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class reportModel extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'reportable_id',
        'reportable_type',
        'report_subject',
        'status',
        'reviewed_by',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
    public function reportedUser(){
        return $this->belongsTo(User::class, 'reported_user_id');
    }
    public function getSubjectLabelAttribute()
    {
        return match ($this->report_subject) {
            'spam'  => 'Spam',
            'harassment'  => 'Harassment',
            'hate_speech' => 'Hate Speech',
            'violence' => 'Violence',
            'nudity' => 'Nudity',
            'false_information' => 'False Information',
            'other' => 'Other',
            default => 'unknown',
        };
    }
}
