<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveOfficialLetter extends Model
{
    protected $fillable = [
        'leave_request_id',
        'template_used',
        'signing_option',
        'snapshot_data',
        'file_path',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }
}
