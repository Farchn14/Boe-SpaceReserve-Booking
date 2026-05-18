<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fasilitas;
use App\Models\Booking;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Gunakan try-catch agar dashboard tidak crash jika tabel belum dibuat (mockup)
        try {
            $countFasilitas = \Illuminate\Support\Facades\Schema::hasTable('fasilitas') ? Fasilitas::count() : 0;
            $countBooking = \Illuminate\Support\Facades\Schema::hasTable('bookings') ? Booking::count() : 0;
        } catch (\Exception $e) {
            $countFasilitas = 0;
            $countBooking = 0;
        }

        return view('admin.dashboard.master', compact('countFasilitas', 'countBooking'));
    }
}
