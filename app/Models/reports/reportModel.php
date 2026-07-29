<?php

namespace App\Models\reports;

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
}
