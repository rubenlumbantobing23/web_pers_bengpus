<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarriageRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_number',
        'spouse_name',
        'spouse_nrp_nip',
        'spouse_occupation',
        'marriage_date',
        'marriage_location',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(MarriageRequestDocument::class);
    }
}
