<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'penyewa_id',
        'fasilitas_id',
        'tgl_mulai',
        'tgl_selesai',
        'package_type',
        'selected_packages',
        'total_harga',
        'status',
        'rejection_reason',
        'expired_at',
        'checkin_at'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'checkin_at' => 'datetime',
    ];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class);
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }
}
