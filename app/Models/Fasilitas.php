<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama',
        'tipe',
        'deskripsi',
        'detail',
        'harga',
        'harga_bulanan',
        'paket_harian',
        'max_dewasa',
        'max_anak',
        'max_durasi_harian',
        'jam_operasional',
        'image',
        'gallery',
        'labels',
        'harga_thumbnail',
    ];

    protected $casts = [
        'paket_harian' => 'array',
        'gallery' => 'array',
        'labels' => 'array',
    ];

    public function histories()
    {
        return $this->hasMany(HargaSewaHistory::class, 'fasilitas_id');
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class, 'fasilitas_id');
    }
}
