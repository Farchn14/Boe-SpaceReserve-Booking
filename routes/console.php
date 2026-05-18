<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\Booking;
use App\Models\AuditLog;

Schedule::call(function () {
    $expiredBookings = Booking::where('status', 'confirmed')
        ->whereNotNull('expired_at')
        ->where('expired_at', '<', now())
        ->get();

    foreach ($expiredBookings as $booking) {
        $booking->update([
            'status' => 'cancelled',
            'rejection_reason' => 'Sistem Otomatis: Melewati batas waktu konfirmasi/pembayaran.'
        ]);
        
        $fasilitasNama = $booking->fasilitas->nama ?? '-';
        
        AuditLog::catat(
            'Auto Expire',
            "Membatalkan reservasi #{$booking->id} secara otomatis karena melewati batas kuitansi.",
            [
                'target_tipe'    => 'booking',
                'target_id'      => $booking->id,
                'fasilitas_nama' => $fasilitasNama,
            ]
        );
    }
})->everyMinute();
