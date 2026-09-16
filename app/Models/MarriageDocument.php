<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarriageDocument extends Model
{
    protected $fillable = [
        'marriage_application_id', 'marriage_document_type_id', 'pihak', 'jenis_dokumen', 'nama_dokumen',
        'file_path', 'file_name', 'mime_type', 'file_size',
        'status_verifikasi', 'catatan_verifikasi', 'uploaded_at', 'verified_at', 'verified_by'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function documentType()
    {
        return $this->belongsTo(MarriageDocumentType::class, 'marriage_document_type_id');
    }

    public function application()
    {
        return $this->belongsTo(MarriageApplication::class, 'marriage_application_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
