<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\BookingApprovedMail;
use App\Mail\BookingRejectedMail;
use App\Models\AuditLog;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tgl_mulai' => 'required|date',
            'package_type' => 'required|in:harian,bulanan',
            'duration' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'rooms_count' => 'required|integer|min:1',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'foto_identitas' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fasilitas = \App\Models\Fasilitas::findOrFail($request->fasilitas_id);
        $totalPrice = 0;
        $tgl_selesai = null;
        $duration = (int)$request->duration;
        $tgl_mulai = $request->tgl_mulai;

        if ($request->package_type === 'harian') {
            $totalPrice = $duration * $fasilitas->harga;
            $start = \Carbon\Carbon::parse($tgl_mulai);
            $tgl_selesai = $start->copy()->addDays($duration - 1)->format('Y-m-d');
        } else {
            // Bulanan: use dedicated harga_bulanan column
            if (!$fasilitas->harga_bulanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas ini tidak mendukung paket bulanan.'
                ], 422);
            }
            $totalPrice = $duration * $fasilitas->harga_bulanan;
            $start = \Carbon\Carbon::parse($tgl_mulai);
            $tgl_selesai = $start->copy()->addMonths($duration)->subDay()->format('Y-m-d');
        }

        // --- VALIDASI OVERLAP ---
        $isOverlapping = \App\Models\Booking::where('fasilitas_id', $request->fasilitas_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($tgl_mulai, $tgl_selesai) {
                $q->whereBetween('tgl_mulai', [$tgl_mulai, $tgl_selesai])
                  ->orWhereBetween('tgl_selesai', [$tgl_mulai, $tgl_selesai])
                  ->orWhere(function ($q2) use ($tgl_mulai, $tgl_selesai) {
                      $q2->where('tgl_mulai', '<=', $tgl_mulai)
                         ->where('tgl_selesai', '>=', $tgl_selesai);
                  });
            })
            ->exists();

        $isBlocked = \App\Models\JadwalBlokir::where('fasilitas_id', $request->fasilitas_id)
            ->where(function ($q) use ($tgl_mulai, $tgl_selesai) {
                $q->whereBetween('tgl_mulai', [$tgl_mulai, $tgl_selesai])
                  ->orWhereBetween('tgl_selesai', [$tgl_mulai, $tgl_selesai])
                  ->orWhere(function ($q2) use ($tgl_mulai, $tgl_selesai) {
                      $q2->where('tgl_mulai', '<=', $tgl_mulai)
                         ->where('tgl_selesai', '>=', $tgl_selesai);
                  });
            })
            ->exists();

        if ($isOverlapping || $isBlocked) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, rentang tanggal yang Anda pilih sudah tidak tersedia atau telah digunakan oleh pemesan lain.'
            ], 422);
        }

        $identitasPath = null;
        if ($request->hasFile('foto_identitas')) {
            $file = $request->file('foto_identitas');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $identitasPath = $file->storeAs('identitas', $filename, 'public');
        }

        // Create renter
        $penyewa = \App\Models\Penyewa::create([
            'nama' => $request->name,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'foto_identitas' => $identitasPath,
        ]);

        $booking = \App\Models\Booking::create([
            'penyewa_id' => $penyewa->id,
            'fasilitas_id' => $request->fasilitas_id,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $tgl_selesai,
            'package_type' => $request->package_type,
            // Store technical details in JSON or separate columns if needed
            'selected_packages' => json_encode([
                'duration' => $duration,
                'adults' => $request->adults,
                'children' => $request->children_count ?? 0,
                'rooms' => $request->rooms_count,
                'child_ages' => $request->child_age ?? []
            ]),
            'total_harga' => $totalPrice,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservasi Anda telah berhasil dikirim! Silakan tunggu konfirmasi admin.'
        ]);
    }


    public function approve($id)
    {
        $booking = \App\Models\Booking::with('penyewa')->findOrFail($id);
        
        // Calculate expiration based on province (JAWA TIMUR = 1 day, others = 3 days)
        $isJatim = strtoupper($booking->penyewa->provinsi ?? '') === 'JAWA TIMUR';
        $expiredAt = $isJatim ? now()->addDays(1) : now()->addDays(3);

        $booking->update([
            'status' => 'confirmed',
            'expired_at' => $expiredAt
        ]);

        // Generate PDF attach data
        $pdf = Pdf::loadView('pdf.receipt', compact('booking'));
        $pdfOutput = $pdf->output();

        $penyewaEmail = $booking->penyewa->email ?? null;
        $penyewaNama = $booking->penyewa->nama ?? 'Unknown';
        
        // Send Email using Mail facade safely
        if ($penyewaEmail) {
            try {
                Mail::to($penyewaEmail)->send(new BookingApprovedMail($booking, $pdfOutput));
            } catch (\Exception $e) {
                \Log::error("Failed to send approval email for booking #{$id}: " . $e->getMessage());
            }
        }

        $publicReceiptUrl = route('public.receipt', $booking->id);
        $fasilitasNama = $booking->fasilitas->nama ?? '-';

        // Audit Log
        AuditLog::catat(
            'Approve Booking',
            "Menyetujui reservasi #{$id} atas nama {$penyewaNama}.",
            [
                'target_tipe'    => 'booking',
                'target_id'      => $id,
                'fasilitas_nama' => $fasilitasNama,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking #' . $id . ' telah disetujui! Email telah dikirim.',
            'name' => $penyewaNama,
            'phone' => $booking->penyewa->whatsapp ?? '-',
            'booking_id' => $id,
            'public_receipt_url' => $publicReceiptUrl
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $booking = \App\Models\Booking::with('penyewa')->findOrFail($id);
        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);

        $penyewaEmail = $booking->penyewa->email ?? null;
        $penyewaNama = $booking->penyewa->nama ?? 'Unknown';

        // Send Email using Mail facade safely
        if ($penyewaEmail) {
            try {
                Mail::to($penyewaEmail)->send(new BookingRejectedMail($booking, $request->reason));
            } catch (\Exception $e) {
                \Log::error("Failed to send rejection email for booking #{$id}: " . $e->getMessage());
            }
        }

        // Audit Log
        AuditLog::catat(
            'Reject Booking',
            "Menolak reservasi #{$id} ({$penyewaNama}) Alasan: {$request->reason}",
            [
                'target_tipe'    => 'booking',
                'target_id'      => $id,
                'fasilitas_nama' => $booking->fasilitas->nama ?? '-',
                'reason'         => $request->reason,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking #' . $id . ' telah ditolak dengan alasan: ' . $request->reason,
            'name' => $penyewaNama,
            'phone' => $booking->penyewa->whatsapp ?? '-',
            'reason' => $request->reason
        ]);
    }

    public function downloadReceipt($id)
    {
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);
        
        $pdf = Pdf::loadView('pdf.receipt', compact('booking'));
        
        return $pdf->download('Kwitansi_BOE_' . $booking->id . '.pdf');
    }

    public function publicReceipt($id)
    {
        // Public method to stream the receipt for sharing via WA link
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);

        if ($booking->status !== 'confirmed') {
            abort(403, 'Kwitansi ini belum valid untuk diunduh karena belum disetujui.');
        }

        $pdf = Pdf::loadView('pdf.receipt', compact('booking'));
        
        return $pdf->stream('Kwitansi_BOE_' . $booking->id . '.pdf');
    }

    public function show($id)
    {
        try {
            $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);

            $foto_identitas_url = null;
            if ($booking->penyewa && $booking->penyewa->foto_identitas) {
                // Menggunakan Storage::url agar path presisi sesuai filesystem_disk
                $foto_identitas_url = Storage::disk('public')->url($booking->penyewa->foto_identitas);
            }

            return response()->json([
                'success' => true,
                'id_raw' => $booking->id,
                'id' => '#BOE-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT),
                'nama' => $booking->penyewa?->nama ?? 'Data Hilang',
                'email' => $booking->penyewa?->email ?? '-',
                'whatsapp' => $booking->penyewa?->whatsapp ?? '-',
                'provinsi' => $booking->penyewa?->provinsi ?? 'Belum Diatur',
                'kabupaten' => $booking->penyewa?->kabupaten ?? 'Belum Diatur',
                'tgl_mulai' => $booking->tgl_mulai ? \Carbon\Carbon::parse($booking->tgl_mulai)->format('Y-m-d') : null,
                'tgl_selesai' => $booking->tgl_selesai ? \Carbon\Carbon::parse($booking->tgl_selesai)->format('Y-m-d') : null,
                'fasilitas' => $booking->fasilitas?->nama ?? 'Fasilitas Hilang',
                'package' => $booking->package_type,
                'status' => $booking->status,
                'total' => 'Rp ' . number_format($booking->total_harga, 0, ',', '.'),
                'details' => json_decode($booking->selected_packages, true) ?? [],
                'created_at' => $booking->created_at?->format('d M Y, H:i \W\I\B') ?? '-',
                'checkin_at' => $booking->checkin_at?->format('d M Y, H:i \W\I\B') ?? null,
                'foto_identitas' => $foto_identitas_url
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data reservasi tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function management()
    {
        $pendingBookings = \App\Models\Booking::with(['penyewa', 'fasilitas'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $confirmedBookings = \App\Models\Booking::with(['penyewa', 'fasilitas'])
            ->where('status', 'confirmed')
            ->orderBy('updated_at', 'desc')
            ->get();

        $bookedBookings = \App\Models\Booking::with(['penyewa', 'fasilitas'])
            ->where('status', 'booked')
            ->orderBy('checkin_at', 'desc')
            ->get();
            
        return view('admin.dashboard.managementBooking', compact('pendingBookings', 'confirmedBookings', 'bookedBookings'));
    }

    public function cancel($id)
    {
        $booking = \App\Models\Booking::with('penyewa')->findOrFail($id);
        
        if ($booking->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya booking yang sudah disetujui yang dapat dibatalkan.'
            ]);
        }
        
        $booking->update([
            'status' => 'cancelled',
            'rejection_reason' => 'Dibatalkan/ditarik secara manual oleh Admin'
        ]);
        
        $fasilitasNama = $booking->fasilitas->nama ?? '-';
        $penyewaNama = $booking->penyewa->nama ?? 'Unknown';

        // Audit Log
        AuditLog::catat(
            'Cancel Booking',
            "Membatalkan reservasi #{$id} atas nama {$penyewaNama}.",
            [
                'target_tipe'    => 'booking',
                'target_id'      => $id,
                'fasilitas_nama' => $fasilitasNama,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Reservasi berhasil dibatalkan sepihak. Tanggal kembali ke status Ready.'
        ]);
    }

    public function extend($id)
    {
        $booking = \App\Models\Booking::with('penyewa')->findOrFail($id);
        
        if ($booking->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya reservasi Confirmed yang bisa diperpanjang.'
            ]);
        }

        // Add 1 day to the current expired_at, or if null, from now
        $newExpiry = $booking->expired_at ? $booking->expired_at->addDays(1) : now()->addDays(1);

        $booking->update([
            'expired_at' => $newExpiry
        ]);

        AuditLog::catat(
            'Extend Deadline',
            "Memperpanjang batas waktu pembayaran reservasi #{$id} sebanyak 1 Hari.",
            [
                'target_tipe' => 'booking',
                'target_id'   => $id
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Batas kadaluarsa berhasil diperpanjang 1 Hari.'
        ]);
    }

    public function checkIn($id)
    {
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);
        
        if ($booking->status !== 'confirmed') {
            return response()->json(['success' => false, 'message' => 'Hanya booking berstatus Confirmed yang bisa Check-In.']);
        }

        $booking->update([
            'status' => 'booked',
            'checkin_at' => now()
        ]);

        AuditLog::catat(
            'Confirm Check-In',
            "Check-In berhasil untuk reservasi #{$id} atas nama {$booking->penyewa->nama}.",
            ['target_tipe' => 'booking', 'target_id' => $id, 'fasilitas_nama' => $booking->fasilitas->nama]
        );

        return response()->json(['success' => true, 'message' => 'Check-In berhasil! Status beralih ke Booked.']);
    }

    public function checkOut($id)
    {
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);
        
        if ($booking->status !== 'booked') {
            return response()->json(['success' => false, 'message' => 'Hanya booking berstatus Booked yang bisa Check-Out.']);
        }

        $booking->update(['status' => 'completed']);

        AuditLog::catat(
            'Confirm Check-Out',
            "Check-Out berhasil untuk reservasi #{$id} atas nama {$booking->penyewa->nama}.",
            ['target_tipe' => 'booking', 'target_id' => $id, 'fasilitas_nama' => $booking->fasilitas->nama]
        );

        return response()->json(['success' => true, 'message' => 'Check-Out berhasil! Data telah diarsipkan ke Riwayat.']);
    }

    public function extendStay(Request $request, $id)
    {
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);
        
        if ($booking->status !== 'booked') {
            return response()->json(['success' => false, 'message' => 'Hanya tamu aktif (Booked) yang bisa diperpanjang masa sewanya.']);
        }

        $days = (int) $request->days;
        if ($days < 1) {
            return response()->json(['success' => false, 'message' => 'Durasi perpanjangan minimal 1 hari.']);
        }

        $currentEnd = \Carbon\Carbon::parse($booking->tgl_selesai);
        $newEnd = $currentEnd->addDays($days);
        
        // Simple logic for cost update
        // Use daily rate for extensions
        $extraCost = $days * $booking->fasilitas->harga;

        $booking->update([
            'tgl_selesai' => $newEnd->format('Y-m-d'),
            'total_harga' => $booking->total_harga + $extraCost
        ]);

        AuditLog::catat(
            'Extend Stay',
            "Memperpanjang masa sewa #{$id} sebanyak {$days} hari. Total biaya diperbarui.",
            ['target_tipe' => 'booking', 'target_id' => $id]
        );

        return response()->json(['success' => true, 'message' => "Masa sewa berhasil diperpanjang {$days} hari."]);
    }
}
