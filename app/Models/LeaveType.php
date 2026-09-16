<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'terms_conditions',
        'required_documents_info',
        'default_days',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_days' => 'integer',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
