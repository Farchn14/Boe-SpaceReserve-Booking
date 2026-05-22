<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Fasilitas;
use App\Models\AuditLog;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['penyewa', 'fasilitas'])
            ->where(function ($q) {
                $q->whereIn('status', ['rejected', 'cancelled', 'completed', 'booked'])
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'confirmed')
                         ->where('tgl_selesai', '<', now()->toDateString());
                  });
            });

        // Search Filter (ID or Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('penyewa', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Specific Facility Filter
        if ($request->filled('facility_id') && $request->facility_id !== 'all') {
            $query->where('fasilitas_id', $request->facility_id);
        }

        // Date/Month/Year Filter
        if ($request->filled('month')) {
            $query->whereMonth('tgl_mulai', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('tgl_mulai', $request->year);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();
        $facilities = Fasilitas::all();

        return view('admin.dashboard.historyBooking', compact('bookings', 'facilities'));
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $penyewaNama = $booking->penyewa->nama ?? 'Unknown';

        // Audit Log before deletion
        AuditLog::catat(
            'Delete History',
            "Menghapus riwayat reservasi #{$id} atas nama {$penyewaNama}.",
            [
                'target_tipe' => 'booking_history',
                'target_id'   => $id
            ]
        );

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat berhasil dihapus permanen.'
        ]);
    }

    public function destroyBatch(Request $request)
    {
        if ($request->all_select === 'true') {
            // Delete all finalized records
            $count = Booking::whereIn('status', ['rejected', 'cancelled', 'completed', 'booked'])
                ->orWhere(function ($q2) {
                    $q2->where('status', 'confirmed')
                       ->where('tgl_selesai', '<', now()->toDateString());
                })->count();

            Booking::whereIn('status', ['rejected', 'cancelled', 'completed', 'booked'])
                ->orWhere(function ($q2) {
                    $q2->where('status', 'confirmed')
                       ->where('tgl_selesai', '<', now()->toDateString());
                })->delete();

            AuditLog::catat('Batch Delete History', "Mencuci seluruh data riwayat ({$count} records).");

            return response()->json(['success' => true, 'message' => "Seluruh ({$count}) data riwayat telah dibersihkan."]);
        }

        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih.'], 400);
        }

        $count = count($ids);
        Booking::whereIn('id', $ids)->delete();

        AuditLog::catat('Batch Delete History', "Menghapus {$count} data riwayat terpilih.");

        return response()->json(['success' => true, 'message' => "{$count} data riwayat terpilih berhasil dihapus."]);
    }
}
