<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarriageRequestDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'marriage_request_id',
        'document_name',
        'file_path',
        'file_size',
    ];

    public function marriageRequest()
    {
        return $this->belongsTo(MarriageRequest::class);
    }
}
