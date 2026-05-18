<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    protected $fillable = ['nama', 'whatsapp', 'email', 'provinsi', 'kabupaten', 'foto_identitas'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
