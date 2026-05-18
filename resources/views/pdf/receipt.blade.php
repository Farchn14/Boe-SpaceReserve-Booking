<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Reservasi - BOE Space Reserve</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .container { width: 100%; max-width: 800px; margin: auto; padding: 20px; }
        .header { text-align: center; border-bottom: 4px solid #1265A8; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #1265A8; margin: 0; font-size: 28px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        
        .info-section { margin-bottom: 30px; }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid th { text-align: left; padding: 12px; background: #f8f9fa; border-bottom: 1px solid #ddd; width: 30%; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #1265A8; }
        .info-grid td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; font-weight: bold; }
        
        .footer { margin-top: 50px; text-align: right; }
        .stamp { display: inline-block; padding: 10px 20px; border: 3px double #22c55e; color: #22c55e; font-weight: 800; transform: rotate(-5deg); text-transform: uppercase; margin-bottom: 20px; }
        .signature { margin-top: 10px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bukti Reservasi Resmi</h1>
            <p>BOE-Space Reserve System | Management Portal</p>
        </div>

        <div class="info-section">
            <table class="info-grid">
                <tr>
                    <th>ID Reservasi</th>
                    <td>#BR-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <th>Nama Penyewa</th>
                    <td>{{ $booking->penyewa->nama }}</td>
                </tr>
                <tr>
                    <th>WhatsApp</th>
                    <td>{{ $booking->penyewa->whatsapp }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $booking->penyewa->email }}</td>
                </tr>
                <tr>
                    <th>Fasilitas</th>
                    <td>{{ $booking->fasilitas->nama }}</td>
                </tr>
                <tr>
                    <th>Durasi Sewa</th>
                    <td>{{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($booking->tgl_selesai)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Tipe Paket</th>
                    <td>{{ ucfirst($booking->package_type) }}</td>
                </tr>
                <tr>
                    <th>Total Biaya</th>
                    <td style="font-size: 18px; color: #1265A8;">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div class="stamp">LUNAS / CONFIRMED</div>
            <p style="margin-bottom: 40px;">Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y, H:i \W\I\B') }}</p>
            <p><b>Administrasi BOE-Reserve</b></p>
            <p class="signature">Dokumen ini sah dikeluarkan melalui sistem elektronik.</p>
        </div>
    </div>
</body>
</html>
