<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'room_type_id',
        'nomor_kamar',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }
}
