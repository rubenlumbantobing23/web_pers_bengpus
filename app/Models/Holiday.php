<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'description',
        'type',
        'year',
    ];

    protected $casts = [
        'date' => 'date',
        'year' => 'integer',
    ];
}
