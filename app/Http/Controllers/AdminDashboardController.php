<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fasilitas;
use App\Models\Booking;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            $countFasilitas = Schema::hasTable('fasilitas') ? Fasilitas::count() : 0;
            $countBooking   = Schema::hasTable('bookings')  ? Booking::count()   : 0;

            // Data doughnut chart
            $fasilitasChart = Schema::hasTable('fasilitas') && Schema::hasTable('bookings')
                ? Fasilitas::select('id', 'nama', 'tipe')
                    ->withCount(['bookings' => fn($q) => $q->whereNotIn('status', ['rejected', 'cancelled'])])
                    ->get()
                : collect();

            // Data line chart: jumlah booking per bulan (tahun berjalan)
            $bookingPerBulan = collect(range(1, 12))->map(function ($month) {
                return Schema::hasTable('bookings')
                    ? Booking::whereYear('tgl_mulai', now()->year)   // ← ganti ke tgl_mulai
                        ->whereMonth('tgl_mulai', $month)             // ← ganti ke tgl_mulai
                        ->whereNotIn('status', ['rejected', 'cancelled'])
                        ->count()
                    : 0;
            });

        } catch (\Exception $e) {
            $countFasilitas  = 0;
            $countBooking    = 0;
            $fasilitasChart  = collect();
            $bookingPerBulan = collect(array_fill(0, 12, 0));
        }

        return view('admin.dashboard.master', compact(
            'countFasilitas', 'countBooking', 'fasilitasChart', 'bookingPerBulan'
        ));
    }
}