<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $table = 'room_types';

    protected $fillable = [
        'fasilitas_id',
        'nama',
        'deskripsi',
        'max_dewasa',
        'max_anak',
        'harga_harian',
        'harga_mingguan',
        'harga_bulanan',
        'harga_tahunan',
        'gallery',
        'labels',
        'stok',
    ];

    protected $casts = [
        'gallery' => 'array',
        'labels' => 'array',
    ];

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id');
    }
}
