<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarriageApplication extends Model
{
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_DIAJUKAN = 'DIAJUKAN';
    const STATUS_PENGAJUAN_DISETUJUI = 'PENGAJUAN_DISETUJUI';
    const STATUS_DITOLAK = 'DITOLAK';
    const STATUS_PERLU_PERBAIKAN = 'PERLU_PERBAIKAN';
    const STATUS_DIVERIFIKASI = 'DIVERIFIKASI';
    const STATUS_DISETUJUI = 'DISETUJUI';
    const STATUS_SELESAI = 'SELESAI';

    protected $fillable = [
        'user_id', 'personel_id', 'jenis_kelamin_anggota', 'peran_anggota',
        'tanggal_pengajuan', 'tanggal_rencana_nikah', 'tempat_nikah', 'alamat_nikah',
        'kelurahan_nikah', 'kecamatan_nikah', 'kabupaten_nikah', 'provinsi_nikah',
        'status', 'catatan_admin',
        'alamat_domisili', 'kelurahan_domisili', 'kecamatan_domisili',
        'kabupaten_domisili', 'provinsi_domisili', 'kua_tujuan'
    ];

    public function isLocked(): bool
    {
        return in_array($this->status, [self::STATUS_DISETUJUI, self::STATUS_SELESAI]);
    }

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_rencana_nikah' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function personel()
    {
        return $this->belongsTo(Personel::class);
    }

    public function partner()
    {
        return $this->hasOne(MarriagePartner::class);
    }

    public function documents()
    {
        return $this->hasMany(MarriageDocument::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(MarriageStatusHistory::class);
    }

    public function letters()
    {
        return $this->hasMany(MarriageLetter::class);
    }
}
