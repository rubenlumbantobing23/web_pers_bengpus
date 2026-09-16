<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'request_number',
        'start_date',
        'end_date',
        'working_days_count',
        'reason',
        'tujuan',
        'pengikut',
        'kendaraan',
        'kodim_koramil',
        'emergency_contact',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'approved_days',
        'approved_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'working_days_count' => 'integer',
        'approved_days' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(LeaveRequestDocument::class);
    }
}
