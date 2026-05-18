<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class NotificationController extends Controller
{
    public function getPendingCount()
    {
        try {
            $pendingBookings = Booking::where('status', 'pending')
                ->select('id', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'count' => $pendingBookings->count(),
                'latest' => $pendingBookings->first() ? $pendingBookings->first()->id : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
