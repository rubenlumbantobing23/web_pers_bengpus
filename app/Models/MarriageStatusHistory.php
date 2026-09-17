<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarriageStatusHistory extends Model
{
    protected $fillable = [
        'marriage_application_id', 'status', 'catatan', 'changed_by'
    ];

    public function application()
    {
        return $this->belongsTo(MarriageApplication::class, 'marriage_application_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
