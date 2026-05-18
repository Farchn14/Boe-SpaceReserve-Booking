<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | History Booking</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .facility-row { transition: all 0.3s ease; }
        .facility-row.selected { background-color: #f1f5f9; }
    </style>
</head>
<body class="flex min-h-screen">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 w-full md:ml-64 p-4 md:p-10 transition-all duration-500 min-h-screen"
        x-data="{ showDetailModal: false, detailPayload: {} }"
        x-on:open-detail.window="detailPayload = $event.detail; showDetailModal = true">
        
        {{-- ═══ HEADER ═══ --}}
        @include('admin.dashboard.layouts.header', [
            'headerTitle' => 'Booking History',
            'headerSubtitle' => 'Record integritas seluruh transaksi yang telah selesai.'
        ])

        {{-- ═══ FILTER INTERFACE ═══ --}}
        <div class="bg-white rounded-[2rem] p-6 mb-8 border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <form action="{{ route('dashboardhistoryBooking') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                
                {{-- Global Search --}}
                <div class="md:col-span-4 relative group">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Cari Guest / ID</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Contoh: Nama Penyewa atau ID..." 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300 transition-all">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                {{-- Facility Type --}}
                <div class="md:col-span-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Fasilitas</label>
                    <select name="facility_id" onchange="this.form.submit()" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-100 appearance-none shadow-sm h-[46px]">
                        <option value="all">SEMUA FASILITAS</option>
                        @foreach($facilities as $f)
                            <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>
                                {{ strtoupper($f->nama) }}
                            </option>
                        @endforeach
                        {{-- Handle Selected Deleted Facility --}}
                        @if(request('facility_id') && request('facility_id') !== 'all' && !$facilities->contains('id', request('facility_id')))
                            <option value="{{ request('facility_id') }}" selected>
                                [DELETED FACILITY #{{ request('facility_id') }}]
                            </option>
                        @endif
                    </select>
                </div>

                {{-- Mini-Calendar Filter (Month/Year) --}}
                <div class="md:col-span-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Periode (Bulan/Tahun)</label>
                    <div class="flex gap-2">
                        <select name="month" onchange="this.form.submit()" class="flex-1 px-3 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-600 outline-none">
                            <option value="">BULAN</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                        <select name="year" onchange="this.form.submit()" class="flex-1 px-3 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-600 outline-none">
                            <option value="">TAHUN</option>
                            @foreach(range(now()->year, 2024) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 h-[46px] bg-[#1265A8] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-900/10 hover:bg-slate-900 transition-all active:scale-95">Filter</button>
                    <a href="{{ route('dashboardhistoryBooking') }}" class="h-[46px] w-[46px] flex items-center justify-center bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 hover:bg-white hover:text-[#1265A8] transition-all group">
                        <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- ═══ BATCH ACTIONS PANEL ═══ --}}
        <div id="batchPanel" class="hidden opacity-0 translate-y-4 transition-all duration-300 bg-slate-900 rounded-[1.5rem] p-4 mb-6 flex items-center justify-between shadow-xl">
            <div class="flex items-center gap-3 ml-2">
                <div class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></div>
                <span class="text-[10px] font-black text-white uppercase tracking-widest"><span id="selectedCount">0</span> Data Terpilih</span>
            </div>
            <div class="flex gap-2">
                <button onclick="confirmBatchDelete()" class="px-5 py-2.5 bg-rose-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-600 transition-all flex items-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Terpilih
                </button>
            </div>
        </div>

        {{-- ═══ TABLE RECORD ═══ --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
            <div class="p-6 md:p-8 flex items-center justify-between border-b border-slate-50">
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-200 text-[#1265A8] focus:ring-[#1265A8]">
                        <span class="text-[10px] font-black text-slate-400 group-hover:text-slate-600 transition-colors uppercase tracking-widest">Select All In Table</span>
                    </label>
                    <div class="h-4 w-px bg-slate-100"></div>
                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic tracking-tighter">Read-only Archived Records</span>
                </div>
                <button onclick="confirmDeleteAll()" class="px-4 py-2 text-rose-500 hover:bg-rose-50 rounded-xl text-[9px] font-black uppercase tracking-widest border border-rose-100 transition-all">Clean History</button>
            </div>

            <div class="overflow-x-auto scrollbar-hide">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="p-4 text-center w-12"></th>
                            <th class="p-6 text-center text-[10px] uppercase tracking-wider text-slate-400 font-black">Guest Info</th>
                            <th class="p-6 text-center text-[10px] uppercase tracking-wider text-slate-400 font-black">Facility Type</th>
                            <th class="p-6 text-center text-[10px] uppercase tracking-wider text-slate-400 font-black">Booking Period</th>
                            <th class="p-6 text-center text-[10px] uppercase tracking-wider text-slate-400 font-black">Status</th>
                            <th class="p-6 text-center text-[10px] uppercase tracking-wider text-slate-400 font-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($bookings as $booking)
                        @php
                            $isPast = \Carbon\Carbon::parse($booking->tgl_selesai)->isPast();
                            $status = $booking->status;
                            if($status == 'confirmed' && $isPast) $status = 'completed';
                        @endphp
                        <tr class="facility-row group hover:bg-slate-50/80 transition-all" data-id="{{ $booking->id }}">
                            <td class="p-4 text-center">
                                <input type="checkbox" class="record-checkbox w-4 h-4 rounded border-slate-200 text-[#1265A8]" value="{{ $booking->id }}">
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-[#1265A8] font-black text-xs">
                                        {{ substr($booking->penyewa?->nama ?? 'D', 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-800">{{ $booking->penyewa?->nama ?? 'Data Not Found' }}</span>
                                        <span class="text-[9px] text-slate-400 font-medium">#BOE-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6 text-center">
                                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase tracking-widest border border-slate-200">
                                    {{ $booking->fasilitas?->tipe ?? 'UNSET' }}
                                </span>
                                <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase">{{ $booking->fasilitas?->nama ?? 'Fasilitas Hilang / Terhapus' }}</p>
                            </td>
                            <td class="p-6 text-center">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-700">{{ \Carbon\Carbon::parse($booking->tgl_mulai)->translatedFormat('d M Y') }}</span>
                                    <span class="text-[8px] text-slate-300 font-bold uppercase tracking-tighter">— {{ \Carbon\Carbon::parse($booking->tgl_selesai)->translatedFormat('d M Y') }} —</span>
                                </div>
                            </td>
                            <td class="p-6 text-center">
                                <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border
                                    @if($status == 'completed') bg-emerald-50 text-emerald-600 border-emerald-100
                                    @elseif($status == 'rejected') bg-rose-50 text-rose-600 border-rose-100
                                    @elseif($status == 'cancelled') bg-slate-50 text-slate-400 border-slate-100
                                    @else bg-blue-50 text-blue-600 border-blue-100 @endif">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="p-6 text-center">
                                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="viewDetail({{ $booking->id }})" class="p-2 bg-slate-800 text-white rounded-lg hover:bg-black transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button onclick="deleteRecord({{ $booking->id }})" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-400">Tidak ada data riwayat ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ═══ DETAIL MODAL (Read-Only) ═══ --}}
        <div id="detailDataModal" x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
            <div @click.away="showDetailModal = false" x-show="showDetailModal" x-transition.scale.duration.300ms class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-2xl mx-4 transform transition-all flex flex-col max-h-[90vh]">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-700 md:text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1265A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Archive Detail <span x-text="detailPayload.id" class="text-[#1265A8] bg-blue-50 px-2 py-0.5 rounded-lg text-sm ml-2"></span>
                    </h3>
                    <button @click="showDetailModal = false" class="text-slate-400 hover:text-rose-500 transition-colors p-1 bg-white rounded-xl shadow-sm border border-slate-100 hover:bg-rose-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 md:p-8 overflow-y-auto">
                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mb-4 border-b border-slate-100 pb-2">Informasi Pemesan Archive</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Nama Lengkap</span>
                            <span class="font-bold text-slate-700 text-sm" x-text="detailPayload.nama"></span>
                        </div>
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">WhatsApp</span>
                            <span class="font-bold text-[#1265A8] text-sm" x-text="detailPayload.whatsapp"></span>
                        </div>
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Asal Wilayah</span>
                            <span class="font-bold text-slate-700 text-xs leading-tight" x-text="(detailPayload.kabupaten || '') + ', ' + (detailPayload.provinsi || '')"></span>
                        </div>
                    </div>

                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mb-4 border-b border-slate-100 pb-2">Detail Peminjaman Fasilitas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 md:col-span-2 flex justify-between items-center">
                            <div>
                                <span class="block text-[10px] uppercase text-blue-400 font-bold mb-1">Fasilitas Dipilih</span>
                                <span class="font-black text-[#1265A8] text-base" x-text="detailPayload.fasilitas"></span>
                            </div>
                            <span class="bg-[#1265A8] text-white px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider" x-text="detailPayload.package"></span>
                        </div>
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Tgl Mulai</span>
                            <span class="font-bold text-slate-700 text-sm" x-text="formatDate(detailPayload.tgl_mulai)"></span>
                        </div>
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Tgl Selesai</span>
                            <span class="font-bold text-slate-700 text-sm" x-text="formatDate(detailPayload.tgl_selesai)"></span>
                        </div>
                    </div>

                    <div class="bg-amber-50 p-5 rounded-2xl border border-amber-200 flex justify-between items-center mt-2 mb-8">
                        <span class="font-bold text-amber-700">Total Transaksi Final</span>
                        <span class="text-xl font-black text-amber-600" x-text="detailPayload.total"></span>
                    </div>

                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mb-4 border-b border-slate-100 pb-2">Dokumen Identitas & History</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-bold mb-2">Foto KTP / Identitas</span>
                            <div class="w-full h-48 bg-slate-200 rounded-xl overflow-hidden flex items-center justify-center border border-slate-200 relative group cursor-pointer" @click="if(detailPayload.foto_identitas) window.open(detailPayload.foto_identitas, '_blank')">
                                <template x-if="detailPayload.foto_identitas">
                                    <div class="w-full h-full relative">
                                        <img :src="detailPayload.foto_identitas" alt="Dokumen Identitas" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="text-white text-xs font-bold tracking-wider">Perbesar Identitas</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!detailPayload.foto_identitas">
                                    <span class="text-xs text-slate-400 font-medium">No Document found</span>
                                </template>
                            </div>
                        </div>
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100 flex flex-col justify-between">
                            <div>
                                <span class="block text-[10px] uppercase text-slate-400 font-bold mb-1">Archived At</span>
                                <span class="font-bold text-slate-700 text-sm italic" x-text="detailPayload.created_at || '-'"></span>
                            </div>
                            <div class="mt-auto bg-white p-3 rounded-xl border border-slate-100 text-center">
                                <span class="block text-[10px] uppercase text-slate-400 font-bold mb-2">Final Status</span>
                                <span class="px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider block"
                                    :class="{
                                        'bg-emerald-100 text-emerald-700 border border-emerald-200': detailPayload.status === 'completed',
                                        'bg-rose-100 text-rose-700 border border-rose-200': detailPayload.status === 'rejected' || detailPayload.status === 'cancelled'
                                    }"
                                    x-text="detailPayload.status">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col-reverse md:flex-row justify-end gap-3 sticky bottom-0 bg-white z-10">
                        <button @click="showDetailModal = false" class="px-6 py-3 bg-slate-50 text-slate-600 font-bold rounded-xl hover:bg-slate-100 transition-colors text-sm border border-slate-200">
                            Kembali
                        </button>
                        <a :href="`/admin/bookings/${detailPayload.id_raw}/receipt`" target="_blank" class="px-6 py-3 bg-gradient-to-r from-[#1265A8] to-[#257bc2] text-white font-bold rounded-xl hover:shadow-lg transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Kuitansi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-10 py-6 border-t border-slate-100 flex items-center justify-between">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">© 2026 BBPPMPV BOE MALANG — DASHBOARD v2.0</p>
        </div>
    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Selection Logic
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.record-checkbox');
        const batchPanel = document.getElementById('batchPanel');
        const selectedCount = document.getElementById('selectedCount');

        selectAll.addEventListener('change', (e) => {
            checkboxes.forEach(cb => {
                cb.checked = e.target.checked;
                updateRowStyle(cb);
            });
            updateBatchPanel();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                updateRowStyle(cb);
                updateBatchPanel();
                if (!cb.checked) selectAll.checked = false;
            });
        });

        function updateRowStyle(cb) {
            const row = cb.closest('.facility-row');
            if (cb.checked) row.classList.add('selected');
            else row.classList.remove('selected');
        }

        function updateBatchPanel() {
            const checkedCount = document.querySelectorAll('.record-checkbox:checked').length;
            selectedCount.textContent = checkedCount;
            if (checkedCount > 0) {
                batchPanel.classList.remove('hidden');
                setTimeout(() => {
                    batchPanel.classList.remove('opacity-0', 'translate-y-4');
                }, 10);
            } else {
                batchPanel.classList.add('opacity-0', 'translate-y-4');
                setTimeout(() => batchPanel.classList.add('hidden'), 300);
            }
        }

        // Action Handlers
        async function deleteRecord(id) {
            const result = await Swal.fire({
                title: 'Hapus Data?',
                text: "Riwayat reservasi ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[2rem]' }
            });

            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/admin/dashboard/historyBooking/\${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false, customClass: { popup: 'rounded-[2rem]' }});
                        location.reload();
                    }
                } catch(e) { Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'); }
            }
        }

        async function confirmBatchDelete() {
            const ids = Array.from(document.querySelectorAll('.record-checkbox:checked')).map(cb => cb.value);
            const result = await Swal.fire({
                title: `Hapus \${ids.length} Data?`,
                text: "Data yang dipilih akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[2rem]' }
            });

            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/admin/dashboard/historyBooking/batch`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: ids })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false, customClass: { popup: 'rounded-[2rem]' }});
                        location.reload();
                    }
                } catch(e) { Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'); }
            }
        }

        async function confirmDeleteAll() {
            const result = await Swal.fire({
                title: 'Clean History?',
                text: "Tindakan ini akan MENGHAPUS SELURUH riwayat transaksi yang tersimpan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#000',
                confirmButtonText: 'Ya, Clean All',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[2rem]' }
            });

            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/admin/dashboard/historyBooking/batch`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ all_select: 'true' })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false, customClass: { popup: 'rounded-[2rem]' }});
                        location.reload();
                    }
                } catch(e) { Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'); }
            }
        }

        function formatDate(dateStr) {
            if(!dateStr) return '-';
            const date = new Date(dateStr);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        function openDetailModal(id, dataPayload) {
            window.dispatchEvent(new CustomEvent('open-detail', { 
                detail: dataPayload 
            }));
        }

        async function viewDetail(id) {
            Swal.fire({
                title: 'Memuat Archive...',
                text: 'Mengambil informasi lengkap riwayat',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            try {
                const response = await fetch(`/admin/bookings/${id}/detail`);
                const data = await response.json();
                Swal.close();
                
                if(data.success) {
                    openDetailModal(id, data);
                } else {
                    Swal.fire('Gagal!', data.message || 'Tidak dapat memuat detail.', 'error');
                }
            } catch (err) {
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan sistem saat memuat data.', 'error');
            }
        }
    </script>
</body>
</html>