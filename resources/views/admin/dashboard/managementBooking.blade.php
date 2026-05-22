<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Booking Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            overflow-x: hidden;
        }
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="flex min-h-screen">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 w-full md:ml-64 p-4 md:p-10 transition-all duration-500 min-h-screen"
        x-data="{
            activeTab: 'request',
            showPreviewModal: false,
            showDetailModal: false,
            previewType: '',
            currentBooking: null,
            previewUrl: '',
            detailPayload: {}
        }"
        x-on:open-detail.window="detailPayload = $event.detail; showDetailModal = true">

        @include('admin.dashboard.layouts.header', [
            'headerTitle'    => 'Booking Management',
            'headerSubtitle' => 'Pusat kendali transaksi reservasi secara real-time.'
        ])

        {{-- ═══════════════════════════════════════════════════
             TABS NAV
        ═══════════════════════════════════════════════════ --}}
        <div class="flex space-x-2 border-b border-slate-200 mb-6 overflow-x-auto scrollbar-hide pb-1 cursor-pointer">
            <button @click="activeTab = 'request'"
                :class="activeTab==='request'
                    ? 'border-b-2 border-amber-500 text-amber-600'
                    : 'text-slate-500 hover:text-slate-700'"
                class="px-6 py-3 font-bold text-sm tracking-wide transition-colors whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Booking Request
                <span class="ml-1 bg-amber-100 text-amber-700 py-0.5 px-2 rounded-full text-[10px] font-bold">
                    {{ $pendingBookings->count() }}
                </span>
            </button>

            <button @click="activeTab = 'confirmed'"
                :class="activeTab==='confirmed'
                    ? 'border-b-2 border-emerald-500 text-emerald-600'
                    : 'text-slate-500 hover:text-slate-700'"
                class="px-6 py-3 font-bold text-sm tracking-wide transition-colors whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pending Confirmation
                <span class="ml-1 bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-[10px] font-bold">
                    {{ $confirmedBookings->count() }}
                </span>
            </button>

            <button @click="activeTab = 'booked'"
                :class="activeTab==='booked'
                    ? 'border-b-2 border-indigo-500 text-indigo-600'
                    : 'text-slate-500 hover:text-slate-700'"
                class="px-6 py-3 font-bold text-sm tracking-wide transition-colors whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Penyewa Aktif (Booked)
                <span class="ml-1 bg-indigo-100 text-indigo-700 py-0.5 px-2 rounded-full text-[10px] font-bold">
                    {{ $bookedBookings->count() }}
                </span>
            </button>
        </div>

        {{-- ═══════════════════════════════════════════════════
             TAB 1 — BOOKING REQUEST
             Kolom disesuaikan dengan data form step 1–4:
             • Pemesan (step 4: nama, WA, email, provinsi, kabupaten)
             • Fasilitas & Paket (step 1–2: tipe sewa, durasi, tanggal)
             • Tamu & Kamar (step 2: rooms, adults, child ≥12, child <12)
             • Tagihan (totalPrice dari form)
             • KTP (step 4: foto_identitas)
             • Aksi
        ═══════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'request'" x-transition.opacity.duration.300ms
            class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm overflow-hidden pb-4">

            <div class="px-6 py-4 border-b border-slate-50 flex items-center gap-3 bg-amber-50/40">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse flex-shrink-0"></span>
                <h3 class="font-bold text-slate-700 text-sm">Menunggu Verifikasi Admin</h3>
            </div>

            <div class="overflow-x-auto scrollbar-hide">
                <table class="w-full border-collapse min-w-[1200px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-24">ID</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-52">Pemesan</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-56">Fasilitas &amp; Paket</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-56">Tamu &amp; Kamar</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-36">Tagihan</th>
                            <th class="p-5 text-center text-[10px] uppercase tracking-wider text-slate-400 font-black w-20">KTP</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($pendingBookings as $booking)
                        @php
                            $details  = json_decode($booking->selected_packages, true) ?? [];
                            $duration = $details['duration']            ?? 1;
                            $rooms    = $details['rooms_count']         ?? 1;
                            $adults   = $details['adults']              ?? 1;
                            $children = $details['children_count']      ?? 0;
                            $billable = $details['billable_children']   ?? 0;
                            $free     = $details['free_children']       ?? 0;
                            $totalBillable = $details['total_billable_guests'] ?? $adults;

                            $durUnit = match($booking->package_type) {
                                'mingguan' => 'Minggu',
                                'bulanan'  => 'Bulan',
                                'tahunan'  => 'Tahun',
                                default    => 'Hari',
                            };
                            $totalHariLabel = match($booking->package_type) {
                                'mingguan' => ($duration * 7) . ' hari',
                                'bulanan'  => '~' . ($duration * 30) . ' hari',
                                'tahunan'  => '~' . ($duration * 365) . ' hari',
                                default    => $duration . ' hari',
                            };
                            $tipe = $booking->fasilitas?->tipe ?? 'aula';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-all duration-200">

                            {{-- ID + waktu masuk --}}
                            <td class="p-5">
                                <span class="text-xs text-slate-500 font-bold bg-slate-100 px-3 py-1 rounded-lg">
                                    #BOE-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <p class="text-[10px] text-amber-500 font-bold mt-2">
                                    {{ $booking->created_at?->diffForHumans() ?? '-' }}
                                </p>
                            </td>

                            {{-- PEMESAN: step 4 data --}}
                            <td class="p-5">
                                <p class="text-sm font-black text-slate-800">
                                    {{ $booking->penyewa?->nama ?? 'Data Hilang' }}
                                </p>
                                <p class="text-[11px] text-[#1265A8] font-bold mt-1">
                                    {{ $booking->penyewa?->whatsapp ?? '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $booking->penyewa?->email ?? '-' }}
                                </p>
                                @if($booking->penyewa?->kabupaten || $booking->penyewa?->provinsi)
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">
                                    {{ implode(', ', array_filter([$booking->penyewa?->kabupaten, $booking->penyewa?->provinsi])) }}
                                </p>
                                @endif
                            </td>

                            {{-- FASILITAS & PAKET: step 1–2 data --}}
                            <td class="p-5">
                                <p class="text-[12px] font-black text-slate-700">
                                    {{ $booking->fasilitas?->nama ?? 'Fasilitas Hilang' }}
                                </p>
                                <div class="flex items-center gap-2 flex-wrap mt-2">
                                    {{-- Badge tipe fasilitas --}}
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase
                                        {{ $tipe === 'asrama' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                                        {{ ucfirst($tipe) }}
                                    </span>
                                    {{-- Badge paket sewa (step 1) --}}
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase
                                        {{ match($booking->package_type) {
                                            'harian'   => 'bg-blue-100 text-blue-700',
                                            'mingguan' => 'bg-violet-100 text-violet-700',
                                            'bulanan'  => 'bg-emerald-100 text-emerald-700',
                                            'tahunan'  => 'bg-amber-100 text-amber-700',
                                            default    => 'bg-slate-100 text-slate-600',
                                        } }}">
                                        {{ ucfirst($booking->package_type) }}
                                    </span>
                                </div>
                                {{-- Durasi dari step 2 --}}
                                <p class="text-[11px] text-slate-500 font-semibold mt-1.5">
                                    {{ $duration }} {{ $durUnit }} &middot; {{ $totalHariLabel }}
                                </p>
                                {{-- Rentang tanggal dari step 3 --}}
                                <p class="text-[10px] text-slate-400 mt-1">
                                    {{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d M Y') }}
                                    &rarr;
                                    {{ \Carbon\Carbon::parse($booking->tgl_selesai)->format('d M Y') }}
                                </p>
                            </td>

                            {{-- TAMU & KAMAR: step 2 breakdown --}}
                            <td class="p-5">
                                @if($tipe === 'asrama')
                                {{-- Jumlah kamar --}}
                                <div class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full mb-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    {{ $rooms }} Kamar &middot; maks {{ $rooms * 2 }} slot dewasa
                                </div>
                                @endif

                                {{-- Dewasa --}}
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $adults }} Dewasa
                                    </span>

                                    {{-- Anak ≥12 (berbayar tarif dewasa) --}}
                                    @if($billable > 0)
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full"
                                          title="Anak ≥12 tahun — ditagih tarif dewasa, menempati slot dewasa">
                                        {{ $billable }} Anak ≥12 (tarif dewasa)
                                    </span>
                                    @endif

                                    {{-- Anak <12 (gratis) --}}
                                    @if($free > 0)
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full"
                                          title="Anak <12 tahun — gratis">
                                        {{ $free }} Anak &lt;12 (gratis)
                                    </span>
                                    @endif
                                </div>

                                {{-- Total tamu berbayar --}}
                                <p class="text-[10px] text-slate-500 font-semibold mt-2">
                                    @if($tipe === 'aula')
                                        Total peserta: <strong class="text-slate-700">{{ $adults }}</strong>
                                        @if($booking->fasilitas?->max_dewasa)
                                        / maks {{ $booking->fasilitas->max_dewasa }}
                                        @endif
                                    @else
                                        Tamu berbayar: <strong class="text-slate-700">{{ $totalBillable }}</strong>
                                        @if($free > 0)
                                        &nbsp;+&nbsp;<span class="text-emerald-600 font-bold">{{ $free }} gratis</span>
                                        @endif
                                    @endif
                                </p>
                            </td>

                            {{-- TAGIHAN --}}
                            <td class="p-5">
                                <p class="text-sm font-black text-[#1265A8]">
                                    Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                                </p>
                                {{-- Rumus tagihan agar admin bisa verifikasi --}}
                                <p class="text-[10px] text-slate-400 mt-1 leading-relaxed">
                                    @if($tipe === 'asrama')
                                        {{ $rooms }} kamar &times; {{ $duration }} {{ $durUnit }}
                                    @else
                                        {{ $duration }} {{ $durUnit }} &times; tarif aula
                                    @endif
                                </p>
                                <p class="text-[10px] text-slate-400">Sudah termasuk pajak</p>
                            </td>

                            {{-- KTP — foto_identitas dari step 4 --}}
                            <td class="p-5 text-center">
                                <button
                                    @click="showPreviewModal = true;
                                            previewType = 'KTP';
                                            currentBooking = {{ $booking->id }};
                                            previewUrl = '{{ $booking->penyewa?->foto_identitas ? Storage::url($booking->penyewa->foto_identitas) : '' }}'"
                                    class="mx-auto flex flex-col items-center justify-center w-14 h-14 bg-slate-50 border border-dashed border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 hover:text-blue-600 transition-all text-slate-400 group">
                                    <svg class="w-5 h-5 mb-1 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                    <span class="text-[8px] font-bold uppercase tracking-wider">KTP</span>
                                </button>
                            </td>

                            {{-- AKSI --}}
                            <td class="p-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button onclick="approveBooking({{ $booking->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/25 transition-all font-bold text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approve
                                    </button>
                                    <button onclick="rejectBooking({{ $booking->id }})"
                                        class="flex items-center justify-center w-8 h-8 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    <button @click="fetchDetailData({{ $booking->id }})"
                                        class="flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-800 hover:text-white transition-all"
                                        title="Detail Lengkap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-300">
                                    <svg class="w-14 h-14 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    <p class="font-medium text-slate-400">Belum ada pengajuan booking baru.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             TAB 2 — PENDING CONFIRMATION
        ═══════════════════════════════════════════════════ --}}
        <div x-cloak x-show="activeTab === 'confirmed'" x-transition.opacity.duration.300ms
            class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm overflow-hidden pb-4">

            <div class="px-6 py-4 border-b border-slate-50 flex items-center gap-3 bg-emerald-50/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse flex-shrink-0"></span>
                <h3 class="font-bold text-slate-700 text-sm">Menunggu Kedatangan / Pembayaran Final</h3>
            </div>

            <div class="overflow-x-auto scrollbar-hide">
                <table class="w-full border-collapse min-w-[1200px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-24">ID</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-52">Pemesan</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-56">Fasilitas &amp; Paket</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-52">Tamu &amp; Tagihan</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-44">Kwitansi (Countdown)</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($confirmedBookings as $booking)
                        @php
                            $details  = json_decode($booking->selected_packages, true) ?? [];
                            $duration = $details['duration']            ?? 1;
                            $rooms    = $details['rooms_count']         ?? 1;
                            $adults   = $details['adults']              ?? 1;
                            $children = $details['children_count']      ?? 0;
                            $billable = $details['billable_children']   ?? 0;
                            $free     = $details['free_children']       ?? 0;
                            $totalBillable = $details['total_billable_guests'] ?? $adults;

                            $durUnit = match($booking->package_type) {
                                'mingguan' => 'Minggu',
                                'bulanan'  => 'Bulan',
                                'tahunan'  => 'Tahun',
                                default    => 'Hari',
                            };
                            $tipe = $booking->fasilitas?->tipe ?? 'aula';

                            $isJatim   = strtoupper($booking->penyewa?->provinsi ?? '') === 'JAWA TIMUR';
                            $totalMs   = $isJatim ? 86400000 : 259200000;
                            $expiredAt = $booking->expired_at
                                ? $booking->expired_at->toIso8601String()
                                : $booking->updated_at->addHours($isJatim ? 24 : 72)->toIso8601String();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-all duration-200">

                            {{-- ID --}}
                            <td class="p-5">
                                <span class="text-xs text-slate-500 font-bold bg-slate-100 px-3 py-1 rounded-lg">
                                    #BOE-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            {{-- PEMESAN --}}
                            <td class="p-5">
                                <p class="text-sm font-black text-slate-800">
                                    {{ $booking->penyewa?->nama ?? 'Data Hilang' }}
                                </p>
                                <p class="text-[11px] text-[#1265A8] font-bold mt-1">
                                    {{ $booking->penyewa?->whatsapp ?? '-' }}
                                </p>
                                @if($booking->penyewa?->kabupaten || $booking->penyewa?->provinsi)
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ implode(', ', array_filter([$booking->penyewa?->kabupaten, $booking->penyewa?->provinsi])) }}
                                </p>
                                @endif
                            </td>

                            {{-- FASILITAS & PAKET --}}
                            <td class="p-5">
                                <p class="text-[12px] font-black text-slate-700">
                                    {{ $booking->fasilitas?->nama ?? 'Fasilitas Hilang' }}
                                </p>
                                <div class="flex items-center gap-2 flex-wrap mt-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase
                                        {{ $tipe === 'asrama' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                                        {{ ucfirst($tipe) }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase
                                        {{ match($booking->package_type) {
                                            'harian'   => 'bg-blue-100 text-blue-700',
                                            'mingguan' => 'bg-violet-100 text-violet-700',
                                            'bulanan'  => 'bg-emerald-100 text-emerald-700',
                                            'tahunan'  => 'bg-amber-100 text-amber-700',
                                            default    => 'bg-slate-100 text-slate-600',
                                        } }}">
                                        {{ ucfirst($booking->package_type) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 font-semibold mt-1.5">
                                    {{ $duration }} {{ $durUnit }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d M Y') }}
                                    &rarr;
                                    {{ \Carbon\Carbon::parse($booking->tgl_selesai)->format('d M Y') }}
                                </p>
                            </td>

                            {{-- TAMU & TAGIHAN --}}
                            <td class="p-5">
                                @if($tipe === 'asrama')
                                <div class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full mb-2">
                                    {{ $rooms }} Kamar
                                </div>
                                @endif
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $adults }} Dewasa
                                    </span>
                                    @if($billable > 0)
                                    <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $billable }} Anak ≥12
                                    </span>
                                    @endif
                                    @if($free > 0)
                                    <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $free }} Anak &lt;12 gratis
                                    </span>
                                    @endif
                                </div>
                                <p class="text-sm font-black text-[#1265A8] mt-2">
                                    Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    @if($tipe === 'asrama')
                                        {{ $rooms }} kamar × {{ $duration }} {{ $durUnit }}
                                    @else
                                        {{ $duration }} {{ $durUnit }} × tarif aula
                                    @endif
                                </p>
                            </td>

                            {{-- COUNTDOWN KWITANSI --}}
                            <td class="p-5"
                                x-data="countdown('{{ $expiredAt }}', {{ $totalMs }})">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Masa Berlaku</span>
                                        <span class="text-[10px] font-black"
                                            :class="expired ? 'text-rose-500' : 'text-emerald-600'"
                                            x-text="timeText">Loading...</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-1000 ease-linear"
                                            :class="expired
                                                ? 'bg-rose-500 w-full'
                                                : (percentage < 25 ? 'bg-amber-500' : 'bg-emerald-500')"
                                            :style="`width: ${expired ? 100 : percentage}%`"></div>
                                    </div>
                                    <p class="text-[9px] text-slate-400 italic">
                                        {{ $isJatim ? 'Jawa Timur → 24 jam' : 'Luar Jatim → 3 hari' }}
                                    </p>
                                </div>
                            </td>

                            {{-- AKSI --}}
                            <td class="p-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button onclick="confirmCheckIn({{ $booking->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 hover:shadow-lg hover:shadow-emerald-500/25 transition-all font-bold text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        Check-In &amp; Bayar
                                    </button>
                                    <button onclick="extendDeadline({{ $booking->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-500 hover:text-white transition-all font-bold text-xs ring-1 ring-blue-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Extend
                                    </button>
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^\d]/', '', $booking->penyewa?->whatsapp ?? '')) }}?text=Halo%20{{ urlencode($booking->penyewa?->nama ?? '') }},%20kami%20dari%20Admin%20BOE-Space..."
                                        target="_blank"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-emerald-50 text-emerald-700 rounded-xl hover:bg-emerald-500 hover:text-white transition-all font-bold text-xs ring-1 ring-emerald-200">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.098.824z"/>
                                        </svg>
                                        WA
                                    </a>
                                    <button onclick="cancelBooking({{ $booking->id }})"
                                        class="flex items-center justify-center w-8 h-8 bg-white text-slate-400 border border-slate-200 rounded-xl hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 transition-all shadow-sm"
                                        title="Batalkan Reservasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                    <button @click="fetchDetailData({{ $booking->id }})"
                                        class="flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-800 hover:text-white transition-all"
                                        title="Detail Lengkap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-300">
                                    <svg class="w-14 h-14 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="font-medium text-slate-400">Tidak ada data pending confirmation.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             TAB 3 — PENYEWA AKTIF (BOOKED)
        ═══════════════════════════════════════════════════ --}}
        <div x-cloak x-show="activeTab === 'booked'" x-transition.opacity.duration.300ms
            class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm overflow-hidden pb-4">

            <div class="px-6 py-4 border-b border-slate-50 flex items-center gap-3 bg-indigo-50/30">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse flex-shrink-0"></span>
                <h3 class="font-bold text-slate-700 text-sm">Memonitor Tamu Aktif (Guest Monitoring)</h3>
            </div>

            <div class="overflow-x-auto scrollbar-hide">
                <table class="w-full border-collapse min-w-[1200px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-24">ID</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-52">Tamu &amp; Fasilitas</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-56">Detail Paket &amp; Tamu</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-44">Check-In Time</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black w-44">Durasi Tersisa</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black">Kontrol Operasional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($bookedBookings as $booking)
                        @php
                            $details  = json_decode($booking->selected_packages, true) ?? [];
                            $duration = $details['duration']            ?? 1;
                            $rooms    = $details['rooms_count']         ?? 1;
                            $adults   = $details['adults']              ?? 1;
                            $children = $details['children_count']      ?? 0;
                            $billable = $details['billable_children']   ?? 0;
                            $free     = $details['free_children']       ?? 0;
                            $totalBillable = $details['total_billable_guests'] ?? $adults;

                            $durUnit = match($booking->package_type) {
                                'mingguan' => 'Minggu',
                                'bulanan'  => 'Bulan',
                                'tahunan'  => 'Tahun',
                                default    => 'Hari',
                            };
                            $tipe = $booking->fasilitas?->tipe ?? 'aula';
                            $end  = \Carbon\Carbon::parse($booking->tgl_selesai)->endOfDay();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-all duration-200">

                            {{-- ID --}}
                            <td class="p-5">
                                <span class="text-xs text-indigo-600 font-bold bg-indigo-50 px-3 py-1 rounded-lg">
                                    #BOE-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            {{-- TAMU & FASILITAS --}}
                            <td class="p-5">
                                <p class="text-sm font-black text-slate-800">
                                    {{ $booking->penyewa?->nama ?? 'Data Hilang' }}
                                </p>
                                <p class="text-[12px] font-bold text-indigo-600 mt-0.5">
                                    {{ $booking->fasilitas?->nama ?? 'Fasilitas Hilang' }}
                                </p>
                                @if($booking->penyewa?->kabupaten || $booking->penyewa?->provinsi)
                                <p class="text-[10px] text-slate-400 mt-1">
                                    {{ implode(', ', array_filter([$booking->penyewa?->kabupaten, $booking->penyewa?->provinsi])) }}
                                </p>
                                @endif
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d M') }}
                                    &rarr;
                                    {{ \Carbon\Carbon::parse($booking->tgl_selesai)->format('d M Y') }}
                                </p>
                            </td>

                            {{-- DETAIL PAKET & TAMU --}}
                            <td class="p-5">
                                <div class="flex items-center gap-2 flex-wrap mb-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase
                                        {{ $tipe === 'asrama' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                                        {{ ucfirst($tipe) }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase
                                        {{ match($booking->package_type) {
                                            'harian'   => 'bg-blue-100 text-blue-700',
                                            'mingguan' => 'bg-violet-100 text-violet-700',
                                            'bulanan'  => 'bg-emerald-100 text-emerald-700',
                                            'tahunan'  => 'bg-amber-100 text-amber-700',
                                            default    => 'bg-slate-100 text-slate-600',
                                        } }}">
                                        {{ $duration }} {{ $durUnit }}
                                    </span>
                                </div>
                                @if($tipe === 'asrama')
                                <div class="flex flex-wrap gap-1.5 mb-1.5">
                                    <span class="bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $rooms }} Kamar
                                    </span>
                                    <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $adults }} Dewasa
                                    </span>
                                    @if($billable > 0)
                                    <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $billable }} Anak ≥12
                                    </span>
                                    @endif
                                    @if($free > 0)
                                    <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $free }} Anak &lt;12
                                    </span>
                                    @endif
                                </div>
                                @else
                                <div class="mb-1.5">
                                    <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $adults }} Peserta
                                    </span>
                                </div>
                                @endif
                                <p class="text-sm font-black text-slate-700">
                                    Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                                </p>
                            </td>

                            {{-- CHECK-IN TIME --}}
                            <td class="p-5">
                                <p class="text-xs font-bold text-slate-700">
                                    {{ $booking->checkin_at ? $booking->checkin_at->format('d M Y, H:i') : '-' }} WIB
                                </p>
                                <p class="text-[10px] text-slate-400 mt-1">
                                    {{ $booking->checkin_at ? $booking->checkin_at->diffForHumans() : '-' }}
                                </p>
                            </td>

                            {{-- DURASI TERSISA --}}
                            <td class="p-5" x-data="countdown('{{ $end->toIso8601String() }}', 0)">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tersisa</span>
                                        <span class="text-[10px] font-black text-indigo-600"
                                            :class="expired ? 'text-rose-500' : 'text-indigo-600'"
                                            x-text="timeText">Loading...</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-1000 ease-linear"
                                            :class="expired ? 'bg-rose-500 w-full' : 'bg-indigo-500'"
                                            :style="`width: ${expired ? 100 : percentage}%`"></div>
                                    </div>
                                    <p class="text-[9px] text-slate-400 italic" x-show="!expired">
                                        Selesai: {{ $end->format('d M, H:i') }} WIB
                                    </p>
                                </div>
                            </td>

                            {{-- KONTROL OPERASIONAL --}}
                            <td class="p-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button onclick="confirmCheckOut({{ $booking->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-rose-500 text-white rounded-xl hover:bg-rose-600 hover:shadow-lg hover:shadow-rose-500/25 transition-all font-bold text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Check-Out
                                    </button>
                                    <button onclick="extendStay({{ $booking->id }})"
                                        class="flex items-center justify-center w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100 shadow-sm"
                                        title="Perpanjang Masa Sewa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                    <button @click="fetchDetailData({{ $booking->id }})"
                                        class="flex items-center justify-center w-8 h-8 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all border border-blue-100 shadow-sm"
                                        title="Lihat Detail Lengkap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-300">
                                    <svg class="w-14 h-14 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="font-medium text-slate-400">Tidak ada tamu yang sedang aktif saat ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             MODAL: QUICK PREVIEW KTP
        ═══════════════════════════════════════════════════ --}}
        <div x-show="showPreviewModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
            <div @click.away="showPreviewModal = false" x-show="showPreviewModal"
                x-transition.opacity.duration.300ms
                class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-lg mx-4">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Preview Foto <span x-text="previewType"></span>
                    </h3>
                    <button @click="showPreviewModal = false"
                        class="text-slate-400 hover:text-rose-500 transition-colors p-1 bg-white rounded-xl shadow-sm border border-slate-100 hover:bg-rose-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-8">
                    <div class="w-full aspect-[1.6] bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden group hover:border-blue-400 transition-colors cursor-pointer relative"
                        @click="if(previewUrl) window.open(previewUrl, '_blank')">
                        <div x-show="previewUrl" class="w-full h-full absolute inset-0">
                            <img :src="previewUrl ?? ''" alt="Preview KTP"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="text-white text-xs font-bold tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                    </svg>
                                    Buka Gambar Penuh
                                </span>
                            </div>
                        </div>
                        <div x-show="!previewUrl" class="flex flex-col items-center justify-center w-full h-full">
                            <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                            </svg>
                            <span class="text-sm font-bold text-slate-400">Dokumen belum dilampirkan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             MODAL: DETAIL LENGKAP
             Disesuaikan dengan field dari form step 1–4
        ═══════════════════════════════════════════════════ --}}
        <div x-show="showDetailModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
            <div @click.away="showDetailModal = false" x-show="showDetailModal"
                x-transition.scale.duration.300ms
                class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-2xl mx-4 flex flex-col max-h-[90vh]">

                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-700 md:text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1265A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Detail Reservasi
                        <span class="text-[#1265A8] bg-blue-50 px-2 py-0.5 rounded-lg text-sm ml-1"
                            x-text="detailPayload.id"></span>
                    </h3>
                    <button @click="showDetailModal = false"
                        class="text-slate-400 hover:text-rose-500 transition-colors p-1 bg-white rounded-xl shadow-sm border border-slate-100 hover:bg-rose-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 md:p-8 overflow-y-auto">

                    {{-- SECTION: Data Pemesan (form step 4) --}}
                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mb-4 border-b border-slate-100 pb-2">
                        Informasi Pemesan
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Nama Lengkap</span>
                            <span class="font-bold text-slate-700 text-sm" x-text="detailPayload.nama"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">WhatsApp</span>
                            <span class="font-bold text-[#1265A8] text-sm" x-text="detailPayload.whatsapp"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Email</span>
                            <span class="font-bold text-slate-700 text-xs leading-tight break-all"
                                x-text="detailPayload.email || '-'"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 col-span-2 md:col-span-3">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Asal Wilayah</span>
                            <span class="font-bold text-slate-700 text-sm"
                                x-text="(detailPayload.kabupaten || '') + ', ' + (detailPayload.provinsi || '')"></span>
                        </div>
                    </div>

                    {{-- SECTION: Fasilitas & Paket (step 1–2) --}}
                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mb-4 border-b border-slate-100 pb-2">
                        Fasilitas &amp; Paket Sewa
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 col-span-2 md:col-span-4 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <span class="block text-[10px] uppercase text-blue-400 font-bold mb-1">Fasilitas</span>
                                <span class="font-black text-[#1265A8] text-base" x-text="detailPayload.fasilitas"></span>
                            </div>
                            <span class="bg-[#1265A8] text-white px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider"
                                x-text="detailPayload.package"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Tgl Check-In</span>
                            <span class="font-bold text-slate-700 text-sm"
                                x-text="formatDate(detailPayload.tgl_mulai)"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Tgl Check-Out</span>
                            <span class="font-bold text-slate-700 text-sm"
                                x-text="formatDate(detailPayload.tgl_selesai)"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Durasi</span>
                            <span class="font-bold text-slate-700 text-sm"
                                x-text="(detailPayload.details?.duration || 1) + ' ' + (detailPayload.package === 'harian' ? 'Hari' : detailPayload.package === 'mingguan' ? 'Minggu' : detailPayload.package === 'bulanan' ? 'Bulan' : 'Tahun')"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Tipe Fasilitas</span>
                            <span class="font-bold text-slate-700 text-sm capitalize"
                                x-text="detailPayload.details?.tipe || '-'"></span>
                        </div>
                    </div>

                    {{-- SECTION: Konfigurasi Tamu & Kamar (step 2) --}}
                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mb-4 border-b border-slate-100 pb-2">
                        Konfigurasi Tamu &amp; Kamar
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-2">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center"
                            x-show="detailPayload.details?.tipe === 'asrama'">
                            <span class="block text-[10px] uppercase text-purple-500 font-bold mb-1">Kamar</span>
                            <span class="font-black text-purple-700 text-xl"
                                x-text="detailPayload.details?.rooms || '1'"></span>
                            <span class="block text-[9px] text-purple-400 mt-1"
                                x-text="'maks ' + ((detailPayload.details?.rooms || 1) * 2) + ' slot dewasa'"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                            <span class="block text-[10px] uppercase text-blue-500 font-bold mb-1">Dewasa</span>
                            <span class="font-black text-blue-700 text-xl"
                                x-text="detailPayload.details?.adults || '1'"></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center"
                            x-show="detailPayload.details?.tipe === 'asrama'">
                            <span class="block text-[10px] uppercase text-amber-500 font-bold mb-1">Anak ≥12</span>
                            <span class="font-black text-amber-600 text-xl"
                                x-text="detailPayload.details?.billable_children || '0'"></span>
                            <span class="block text-[9px] text-amber-400 mt-1">Tarif dewasa</span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center"
                            x-show="detailPayload.details?.tipe === 'asrama'">
                            <span class="block text-[10px] uppercase text-emerald-500 font-bold mb-1">Anak &lt;12</span>
                            <span class="font-black text-emerald-600 text-xl"
                                x-text="detailPayload.details?.free_children || '0'"></span>
                            <span class="block text-[9px] text-emerald-400 mt-1">Gratis</span>
                        </div>
                    </div>

                    {{-- Total berbayar --}}
                    <div class="bg-amber-50 p-5 rounded-2xl border border-amber-200 flex flex-wrap justify-between items-center gap-3 mt-4">
                        <div>
                            <span class="block text-[10px] uppercase text-amber-600 font-bold mb-1">Total Tamu Berbayar</span>
                            <span class="text-base font-black text-amber-700"
                                x-text="(detailPayload.details?.total_billable_guests || detailPayload.details?.adults || 1) + ' orang'"></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] uppercase text-amber-600 font-bold mb-1">Total Tagihan Final</span>
                            <span class="text-2xl font-black text-amber-700" x-text="detailPayload.total"></span>
                        </div>
                    </div>

                    {{-- SECTION: KTP & Log waktu --}}
                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mt-8 mb-4 border-b border-slate-100 pb-2">
                        Dokumen &amp; Log Waktu
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-2">Foto KTP / Identitas</span>
                            <div class="w-full h-44 bg-slate-200 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 relative group cursor-pointer"
                                @click="if(detailPayload.foto_identitas) window.open(detailPayload.foto_identitas, '_blank')">
                                <div x-show="detailPayload.foto_identitas" class="w-full h-full relative">
                                    <img :src="detailPayload.foto_identitas ?? ''" alt="KTP"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">Perbesar</span>
                                    </div>
                                </div>
                                <div x-show="!detailPayload.foto_identitas" class="flex flex-col items-center justify-center w-full h-full">
                                    <svg class="w-8 h-8 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                    </svg>
                                    <span class="text-xs text-slate-400 font-medium">Belum ada dokumen</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex flex-col justify-between">
                            <div>
                                <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Waktu Pengajuan</span>
                                <span class="font-bold text-slate-700 text-sm flex items-center gap-2 mb-4">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="detailPayload.created_at"></span>
                                </span>
                                <template x-if="detailPayload.checkin_at">
                                    <div class="mt-2 pt-2 border-t border-slate-100">
                                        <span class="block text-[10px] uppercase text-indigo-400 font-bold mb-1">Waktu Check-In</span>
                                        <span class="font-bold text-indigo-700 text-sm"
                                            x-text="detailPayload.checkin_at"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-4 bg-white p-3 rounded-xl border border-slate-100 text-center shadow-sm">
                                <span class="block text-[10px] uppercase text-slate-400 font-bold mb-2">Status Saat Ini</span>
                                <span class="px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider block"
                                    :class="{
                                        'bg-amber-100 text-amber-700 border border-amber-200':   detailPayload.status === 'pending',
                                        'bg-emerald-100 text-emerald-700 border border-emerald-200': detailPayload.status === 'confirmed',
                                        'bg-blue-100 text-blue-700 border border-blue-200':     detailPayload.status === 'booked',
                                        'bg-rose-100 text-rose-700 border border-rose-200':     detailPayload.status === 'rejected' || detailPayload.status === 'cancelled'
                                    }"
                                    x-text="detailPayload.status">
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- AKSI MODAL --}}
                    <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col-reverse md:flex-row justify-end gap-3 sticky bottom-0 bg-white z-10 pb-1">
                        <button @click="showDetailModal = false"
                            class="px-6 py-3 bg-slate-50 text-slate-600 font-bold rounded-xl hover:bg-slate-100 transition-colors text-sm text-center border border-slate-200">
                            Kembali
                        </button>
                        <a :href="`/admin/bookings/${detailPayload.id_raw}/receipt`" target="_blank"
                            class="px-6 py-3 bg-gradient-to-r from-[#1265A8] to-[#257bc2] text-white font-bold rounded-xl hover:shadow-lg hover:shadow-blue-500/30 transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Kuitansi
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        // ── Helpers ───────────────────────────────────────────────────────────────
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            });
        }

        function openDetailModal(id, dataPayload) {
            window.dispatchEvent(new CustomEvent('open-detail', { detail: dataPayload }));
        }

        // ── Fetch detail via AJAX ─────────────────────────────────────────────────
        function fetchDetailData(id) {
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Mengambil informasi lengkap penyewa',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/admin/bookings/${id}/detail`)
                .then(res => {
                    if (!res.ok) return res.json().then(e => { throw new Error(e.message || `Error ${res.status}`); });
                    return res.json();
                })
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        openDetailModal(id, data);
                    } else {
                        Swal.fire('Gagal!', data.message || 'Tidak dapat memuat detail reservasi.', 'error');
                    }
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire({
                        title: 'Error Sistem',
                        text: err.message || 'Terjadi kesalahan sistem saat memuat data.',
                        icon: 'error',
                        footer: '<span style="font-size:11px;color:#94a3b8">Pastikan koneksi aman & data valid</span>'
                    });
                });
        }

        // ── Countdown Alpine component ────────────────────────────────────────────
        function countdown(expireIsoStr, customTotalMs = 86400000) {
            return {
                timeText: '00j 00m 00d',
                expired: false,
                percentage: 100,
                init() {
                    const expireMs = new Date(expireIsoStr).getTime();
                    const update = () => {
                        const rem = expireMs - Date.now();
                        if (rem <= 0) {
                            this.timeText  = 'Kadaluarsa';
                            this.expired   = true;
                            this.percentage = 100;
                            return;
                        }
                        const h = Math.floor(rem / 3600000);
                        const m = Math.floor((rem % 3600000) / 60000);
                        const s = Math.floor((rem % 60000) / 1000);
                        this.timeText   = `${String(h).padStart(2,'0')}j ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}d`;
                        this.percentage = customTotalMs > 0 ? Math.min(100, (rem / customTotalMs) * 100) : 50;
                    };
                    update();
                    setInterval(update, 1000);
                }
            };
        }

        // ── Admin actions ─────────────────────────────────────────────────────────
        function approveBooking(id) {
            Swal.fire({
                title: 'Setujui Pengajuan?',
                text: 'Status akan berubah menjadi Confirmed dan kwitansi otomatis dikirimkan ke email penyewa.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Ya, Setujui & Kirim',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[1.5rem]' }
            }).then(r => { if (r.isConfirmed) processAction(`/admin/bookings/${id}/approve`); });
        }

        function confirmCheckIn(id) {
            Swal.fire({
                title: 'Konfirmasi Kedatangan?',
                text: 'Tamu akan beralih ke status Occupied (Aktif). Pastikan pembayaran telah diterima sesuai tagihan.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Ya, Check-In Tamu',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[1.5rem]' }
            }).then(r => { if (r.isConfirmed) processAction(`/admin/bookings/${id}/checkin`); });
        }

        function confirmCheckOut(id) {
            Swal.fire({
                title: 'Akhiri Masa Sewa?',
                text: 'Tamu akan Check-Out dan data akan diarsipkan secara permanen ke Riwayat Booking.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'Ya, Check-Out & Arsipkan',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[1.5rem]' }
            }).then(r => { if (r.isConfirmed) processAction(`/admin/bookings/${id}/checkout`); });
        }

        async function rejectBooking(id) {
            const { value: reason } = await Swal.fire({
                title: 'Tolak Pengajuan?',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Tuliskan alasan mengapa booking ini ditolak (cth: jadwal penuh)...',
                inputAttributes: { 'aria-label': 'Alasan penolakan' },
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Tolak & Hapus',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[1.5rem]' }
            });
            if (reason) processAction(`/admin/bookings/${id}/reject`, { reason });
        }

        function cancelBooking(id) {
            Swal.fire({
                title: 'Batalkan & Tarik Jadwal?',
                text: 'Booking akan dibatalkan sepihak. Tanggal pada kalender akan kembali kosong (Ready). Harap beri tahu penyewa via WA terlebih dahulu.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Batalkan Reservasi',
                cancelButtonText: 'Kembali',
                customClass: { popup: 'rounded-[1.5rem]' }
            }).then(r => { if (r.isConfirmed) processAction(`/admin/bookings/${id}/cancel`); });
        }

        function extendDeadline(id) {
            Swal.fire({
                title: 'Perpanjang Tenggat Kwitansi?',
                text: 'Masa aktif kwitansi ini akan diperpanjang otomatis selama 1 hari (24 jam) tambahan dari sekarang.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Ya, Perpanjang',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[1.5rem]' }
            }).then(r => { if (r.isConfirmed) processAction(`/admin/bookings/${id}/extend`); });
        }

        function extendStay(id) {
            Swal.fire({
                title: 'Perpanjang Masa Sewa',
                text: 'Masukkan jumlah hari tambahan (biaya dihitung otomatis):',
                input: 'number',
                inputAttributes: { min: 1, step: 1 },
                inputValue: 1,
                showCancelButton: true,
                confirmButtonText: 'Perpanjang Stay',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#6366f1',
                showLoaderOnConfirm: true,
                preConfirm: days => fetch(`/admin/bookings/${id}/extend-stay`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ days })
                }).then(r => r.json()).then(data => {
                    if (!data.success) throw new Error(data.message);
                    return data;
                }).catch(e => Swal.showValidationMessage(`Gagal: ${e}`)),
                allowOutsideClick: () => !Swal.isLoading()
            }).then(r => {
                if (r.isConfirmed) {
                    Swal.fire({ title: 'Berhasil!', text: r.value.message, icon: 'success' })
                        .then(() => location.reload());
                }
            });
        }

        function processAction(url, data = {}) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[1.5rem]' }
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', data.message || 'Terjadi kesalahan sistem.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error sistem', 'Cek console untuk detail', 'error');
                console.error(err);
            });
        }
    </script>
</body>
</html>