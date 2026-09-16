<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequestDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_request_id',
        'document_name',
        'file_path',
        'file_size',
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }
}
