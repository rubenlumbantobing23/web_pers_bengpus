<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarriageLetter extends Model
{
    protected $fillable = [
        'marriage_application_id', 'jenis_surat', 'nomor_surat', 'file_generated',
        'status', 'generated_at', 'generated_by'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(MarriageApplication::class, 'marriage_application_id');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
