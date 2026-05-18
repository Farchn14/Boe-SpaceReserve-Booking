<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Fasilitas;
use App\Models\JadwalBlokir;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KontrolJadwalController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX — render the calendar page
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $facilities = Fasilitas::orderBy('nama')->get();
        $selectedFasilitasId = $request->query('fasilitas_id', $facilities->first()?->id);

        return view('admin.dashboard.kontrolJadwal', compact('facilities', 'selectedFasilitasId'));
    }

    public function showFormBlokir(Request $request)
    {
        $facilities = Fasilitas::orderBy('nama')->get();
        $selectedId = $request->query('fasilitas_id');
        $selectedDate = $request->query('date'); // Format: YYYY-MM-DD

        return view('admin.dashboard.formBlokir', compact('facilities', 'selectedId', 'selectedDate'));
    }

    // ─────────────────────────────────────────
    // API — return calendar events as JSON
    // ─────────────────────────────────────────
    public function calendarData(Request $request)
    {
        $fasilitasId = $request->query('fasilitas_id');
        $year        = (int) $request->query('year',  now()->year);
        $month       = (int) $request->query('month', now()->month);

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = Carbon::create($year, $month, 1)->endOfMonth();

        $events = [];

        // ── 1. Booking events (Pending = Kuning, Booked = Biru, Occupied = Ungu) ──
        $bookings = Booking::with(['penyewa', 'fasilitas'])
            ->where('fasilitas_id', $fasilitasId)
            ->whereIn('status', ['pending', 'confirmed', 'booked'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tgl_mulai', [$start, $end])
                  ->orWhereBetween('tgl_selesai', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('tgl_mulai', '<=', $start)
                         ->where('tgl_selesai', '>=', $end);
                  });
            })
            ->get();

        foreach ($bookings as $b) {
            $color = 'blue';
            if ($b->status === 'pending') {
                $color = 'yellow';
            } elseif ($b->status === 'confirmed' && $b->expired_at && $b->expired_at->isFuture()) {
                $color = 'yellow';
            } elseif ($b->status === 'booked') {
                $color = 'blue';
            }

            $events[] = [
                'type'         => 'booking',
                'status'       => $b->status,
                'color'        => $color,
                'tgl_mulai'    => $b->tgl_mulai,
                'tgl_selesai'  => $b->tgl_selesai,
                // detail data
                'booking_id'   => '#BOE-' . str_pad($b->id, 4, '0', STR_PAD_LEFT),
                'id'           => $b->id,
                'nama'         => $b->penyewa->nama ?? '-',
                'email'        => $b->penyewa->email ?? '-',
                'whatsapp'     => $b->penyewa->whatsapp ?? '-',
                'fasilitas'    => $b->fasilitas->nama ?? '-',
                'package_type' => $b->package_type,
                'total_harga'  => 'Rp ' . number_format($b->total_harga, 0, ',', '.'),
                'created_at'   => $b->created_at ? $b->created_at->format('d M Y, H:i') : '-',
                'updated_at'   => $b->updated_at ? $b->updated_at->format('d M Y, H:i') : '-',
            ];
        }

        // ── 2. JadwalBlokir events (Blocked = Hitam, Maintenance = Oranye) ──
        $blokirs = JadwalBlokir::where('fasilitas_id', $fasilitasId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tgl_mulai', [$start, $end])
                  ->orWhereBetween('tgl_selesai', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('tgl_mulai', '<=', $start)
                         ->where('tgl_selesai', '>=', $end);
                  });
            })
            ->get();

        foreach ($blokirs as $bl) {
            $color = $bl->tipe === 'blocked' ? 'black' : 'red';
            $events[] = [
                'type'        => 'blokir',
                'status'      => $bl->tipe,
                'color'       => $color,
                'tgl_mulai'   => $bl->tgl_mulai,
                'tgl_selesai' => $bl->tgl_selesai,
                'id'          => $bl->id,
                'nama_pic'    => $bl->nama_pic ?? '-',
                'divisi'      => $bl->divisi ?? '-',
                'whatsapp'    => $bl->whatsapp ?? '-',
                'durasi'      => $bl->durasi ?? '-',
                'catatan'     => $bl->catatan ?? '-',
                'created_at'  => $bl->created_at ? $bl->created_at->format('d M Y, H:i') : '-',
            ];
        }

        return response()->json($events);
    }

    // ─────────────────────────────────────────
    // PUBLIC API — return sanitized events for users
    // ─────────────────────────────────────────
    public function publicCalendarData(Request $request)
    {
        $fasilitasId = $request->query('fasilitas_id');
        $year        = (int) $request->query('year',  now()->year);
        $month       = (int) $request->query('month', now()->month);

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = Carbon::create($year, $month, 1)->endOfMonth();

        $events = [];

        // 1. Booking events (Sanitized)
        $bookings = Booking::where('fasilitas_id', $fasilitasId)
            ->whereIn('status', ['pending', 'confirmed', 'booked'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tgl_mulai', [$start, $end])
                  ->orWhereBetween('tgl_selesai', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('tgl_mulai', '<=', $start)
                         ->where('tgl_selesai', '>=', $end);
                  });
            })
            ->select('id', 'fasilitas_id', 'tgl_mulai', 'tgl_selesai', 'status', 'expired_at')
            ->get();

        foreach ($bookings as $b) {
            $color = 'blue';
            if ($b->status === 'pending') {
                $color = 'yellow';
            } elseif ($b->status === 'confirmed' && $b->expired_at && $b->expired_at->isFuture()) {
                $color = 'yellow';
            } elseif ($b->status === 'booked') {
                $color = 'blue';
            }

            $events[] = [
                'type'        => 'booking',
                'status'      => $b->status,
                'color'       => $color,
                'tgl_mulai'   => $b->tgl_mulai,
                'tgl_selesai' => $b->tgl_selesai,
            ];
        }

        // 2. JadwalBlokir events (Sanitized)
        $blokirs = JadwalBlokir::where('fasilitas_id', $fasilitasId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tgl_mulai', [$start, $end])
                  ->orWhereBetween('tgl_selesai', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('tgl_mulai', '<=', $start)
                         ->where('tgl_selesai', '>=', $end);
                  });
            })
            ->select('id', 'fasilitas_id', 'tgl_mulai', 'tgl_selesai', 'tipe', 'tujuan')
            ->get();

        foreach ($blokirs as $bl) {
            $color = $bl->tipe === 'blocked' ? 'black' : 'red';
            $events[] = [
                'type'        => 'blokir',
                'status'      => $bl->tipe,
                'color'       => $color,
                'tgl_mulai'   => $bl->tgl_mulai,
                'tgl_selesai' => $bl->tgl_selesai,
                'reason'      => $bl->tujuan ?? 'Maintenance rutin',
            ];
        }

        return response()->json($events);
    }

    // ─────────────────────────────────────────
    // STORE — Quick Block (Blocked / Maintenance)
    // ─────────────────────────────────────────
    public function storeBlokir(Request $request)
    {
        $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tgl_mulai'    => 'required|date',
            'tipe'         => 'required|in:blocked,maintenance',
            'unit'         => 'nullable|in:day,month',
            'durasi'       => 'required|integer|min:1',
            'nama_pic'     => 'required|string|max:255',
            'divisi'       => 'required|string|max:255',
            'whatsapp'     => 'required|string|max:30',
            'tujuan'       => 'required|string|max:1000',
            'catatan'      => 'nullable|string|max:1000',
        ]);

        $fasilitas = Fasilitas::find($request->fasilitas_id);
        $tgl_mulai = Carbon::parse($request->tgl_mulai);
        $unit      = $request->input('unit', 'day');
        $durasi    = (int) $request->durasi;
        
        // Calculate tgl_selesai based on unit
        if ($unit === 'month') {
            $tgl_selesai = $tgl_mulai->copy()->addMonths($durasi)->subDay();
        } else {
            $tgl_selesai = $tgl_mulai->copy()->addDays($durasi - 1);
        }

        $startStr = $tgl_mulai->toDateString();
        $endStr   = $tgl_selesai->toDateString();

        // ── OVERLAP CHECK ──
        // 1. Check existing bookings
        $bookingConflict = Booking::where('fasilitas_id', $request->fasilitas_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($startStr, $endStr) {
                $q->whereBetween('tgl_mulai', [$startStr, $endStr])
                  ->orWhereBetween('tgl_selesai', [$startStr, $endStr])
                  ->orWhere(function ($q2) use ($startStr, $endStr) {
                      $q2->where('tgl_mulai', '<=', $startStr)
                         ->where('tgl_selesai', '>=', $endStr);
                  });
            })->exists();

        if ($bookingConflict) {
            return response()->json(['success' => false, 'message' => 'Gagal! Ada reservasi aktif pada rentang tanggal tersebut.'], 422);
        }

        // 2. Check existing blokir/maintenance
        $blokirConflict = JadwalBlokir::where('fasilitas_id', $request->fasilitas_id)
            ->where(function ($q) use ($startStr, $endStr) {
                $q->whereBetween('tgl_mulai', [$startStr, $endStr])
                  ->orWhereBetween('tgl_selesai', [$startStr, $endStr])
                  ->orWhere(function ($q2) use ($startStr, $endStr) {
                      $q2->where('tgl_mulai', '<=', $startStr)
                         ->where('tgl_selesai', '>=', $endStr);
                  });
            })->exists();

        if ($blokirConflict) {
            return response()->json(['success' => false, 'message' => 'Gagal! Jadwal ini sudah terblokir atau dalam maintenance.'], 422);
        }

        $blokir = JadwalBlokir::create([
            'fasilitas_id' => $request->fasilitas_id,
            'tgl_mulai'    => $startStr,
            'tgl_selesai'  => $endStr,
            'tipe'         => $request->tipe,
            'nama_pic'     => $request->nama_pic,
            'divisi'       => $request->divisi,
            'whatsapp'     => $request->whatsapp,
            'durasi'       => $durasi,
            'tujuan'       => $request->tujuan,
            'catatan'      => $request->catatan,
            'created_by'   => session('nama') ?? 'System Admin',
        ]);

        // Audit Log
        AuditLog::catat(
            'Internal Block',
            "Memblokir unit {$fasilitas->nama} untuk: {$request->tujuan} (PIC: {$request->nama_pic}) mulai {$startStr} s/d {$endStr}",
            [
                'target_tipe'    => 'jadwal_blokir',
                'target_id'      => $blokir->id,
                'fasilitas_nama' => $fasilitas->nama,
                'tujuan'         => $request->tujuan,
            ]
        );

        return response()->json([
            'success' => true, 
            'message' => 'Jadwal berhasil diblokir & dikunci.',
            'redirect' => route('kontrolJadwal.index', ['fasilitas_id' => $request->fasilitas_id])
        ]);
    }

    // ─────────────────────────────────────────
    // DESTROY — Release a blokir record
    // ─────────────────────────────────────────
    public function destroyBlokir($id)
    {
        $blokir = JadwalBlokir::with('fasilitas')->findOrFail($id);
        $fasilitasNama = $blokir->fasilitas->nama ?? '-';
        $tipe = strtoupper($blokir->tipe);

        $blokir->delete();

        AuditLog::catat(
            'Buka Blokir',
            "Status {$tipe} pada {$fasilitasNama} (tgl {$blokir->tgl_mulai} s/d {$blokir->tgl_selesai}) telah dibuka.",
            [
                'target_tipe'    => 'jadwal_blokir',
                'target_id'      => $id,
                'fasilitas_nama' => $fasilitasNama,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Blokir berhasil dihapus.']);
    }

    // ─────────────────────────────────────────
    // DOWNLOAD receipt PDF for a booking
    // ─────────────────────────────────────────
    public function downloadReceipt($id)
    {
        $booking = Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.receipt', compact('booking'));

        AuditLog::catat(
            'Download Kuitansi',
            "Kuitansi Booking #{$id} ({$booking->penyewa->nama}) diunduh.",
            [
                'target_tipe'    => 'booking',
                'target_id'      => $id,
                'fasilitas_nama' => $booking->fasilitas->nama ?? '-',
            ]
        );

        return $pdf->download('Kwitansi_BOE_' . $id . '.pdf');
    }
}
