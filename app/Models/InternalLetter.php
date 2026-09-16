<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternalLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_number',
        'letter_date',
        'category',
        'subject',
        'description',
        'file_path',
        'file_size',
        'uploader_id',
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
