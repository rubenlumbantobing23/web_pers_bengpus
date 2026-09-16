<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarriageDocumentType extends Model
{
    use HasFactory;

    protected $table = 'marriage_document_types';

    protected $fillable = [
        'code',
        'name',
        'category',
        'owner_type',
        'source_type',
        'is_required',
        'is_active',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function documents()
    {
        return $this->hasMany(MarriageDocument::class, 'marriage_document_type_id');
    }
}
