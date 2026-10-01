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
        'letter_type_id',
        'direction',
        'received_date',
        'sender',
        'recipient',
        'classification',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'received_date' => 'date',
        'file_size' => 'integer',
    ];

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
