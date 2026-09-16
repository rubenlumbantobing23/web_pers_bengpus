<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveEntitlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'total_days',
    ];

    protected $casts = [
        'year' => 'integer',
        'total_days' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
