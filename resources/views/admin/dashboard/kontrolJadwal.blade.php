<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Kontrol Jadwal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; overflow-x: hidden; }

        /* ── Calendar Grid ── */
        #calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .cal-day {
            min-height: 52px;
            border-radius: 14px;
            cursor: pointer;
            position: relative;
            transition: transform 0.18s cubic-bezier(.4,0,.2,1), box-shadow 0.18s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            user-select: none;
        }
        @media (min-width: 768px) { .cal-day { min-height: 70px; font-size: 15px; } }

        .cal-day:hover { transform: scale(1.07); box-shadow: 0 8px 24px rgba(0,0,0,.10); z-index: 2; }
        .cal-day.other-month { opacity: .28; cursor: default; pointer-events: none; }
        .cal-day.today-marker::after {
            content: ''; position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%);
            width: 5px; height: 5px; border-radius: 50%; background: #1265A8;
        }

        /* ── Six Status Colors ── */
        .status-ready       { background: #d1fae5; color: #065f46; }
        .status-pending     { background: #fef9c3; color: #854d0e; }
        .status-booked      { background: #dbeafe; color: #1e40af; }
        .status-blocked     { background: #1e293b; color: #f1f5f9; }
        .status-maintenance { background: #fee2e2; color: #991b1b; }
        .status-past        { background: #f1f5f9; color: #94a3b8; cursor: default; }

        .status-tooltip {
            position: absolute; bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%);
            background: #1e293b; color: #fff; font-size: 11px; font-weight: 600;
            padding: 4px 10px; border-radius: 8px; white-space: nowrap;
            pointer-events: none; opacity: 0; transition: opacity .2s;
            z-index: 50;
        }
        .cal-day:hover .status-tooltip { opacity: 1; }

        /* ── Modals ── */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,.55);
            backdrop-filter: blur(5px); z-index: 100;
            display: flex; align-items: flex-end; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity .3s;
        }
        @media (min-width: 640px) {
            .modal-overlay { align-items: center; }
        }
        .modal-overlay.open { opacity: 1; pointer-events: all; }

        .modal-box {
            background: #fff; border-radius: 28px 28px 0 0; width: 100%; max-width: 480px;
            padding: 28px 24px 32px;
            transform: translateY(60px); transition: transform .35s cubic-bezier(.4,0,.2,1);
            max-height: 90vh; overflow-y: auto;
        }
        @media (min-width: 640px) {
            .modal-box { border-radius: 28px; transform: scale(.92) translateY(0); max-height: 85vh; }
        }
        .modal-overlay.open .modal-box { transform: translateY(0); }
        @media (min-width: 640px) {
            .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }
        }

        /* ── Legend dots ── */
        .legend-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }

        /* ── Audit log badge ── */
        .audit-badge {
            display: inline-flex; align-items: center; padding: 2px 10px;
            border-radius: 999px; font-size: 10px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .06em;
        }

        /* ── Input style ── */
        .form-input {
            width: 100%; border: 1.5px solid #e2e8f0; border-radius: 12px;
            padding: 10px 14px; font-size: 13px; font-family: inherit;
            transition: border-color .2s, box-shadow .2s; outline: none;
            background: #f8fafc;
        }
        .form-input:focus { border-color: #1265A8; box-shadow: 0 0 0 3px rgba(18,101,168,.12); }

        /* ── Quick Block Bubble ── */
        .quick-block-bubble {
            position: fixed; z-index: 200;
            background: #1e293b; color: #fff;
            padding: 8px 16px; border-radius: 12px;
            font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.05em;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
            cursor: pointer; pointer-events: none; opacity: 0;
            transform: translate(0, 0) scale(0.8);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .quick-block-bubble.show {
            opacity: 1; pointer-events: all;
            transform: translate(0, 0) scale(1);
        }
        .quick-block-bubble:hover {
            background: #1265A8;
            transform: translate(15px, -15px) scale(1.05);
        }
        .quick-block-bubble::after {
            content: ''; position: absolute; top: 100%; left: 0;
            border: 6px solid transparent; border-top-color: #1265A8;
            transform: translateX(4px);
        }
    </style>
</head>
<body class="flex min-h-screen">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 w-full md:ml-64 p-4 md:p-8 transition-all duration-500 min-h-screen">

        {{-- ═══ HEADER ═══ --}}
        @include('admin.dashboard.layouts.header', [
            'headerTitle' => 'Kontrol Jadwal',
            'headerSubtitle' => 'Kelola availability & jadwal blokir fasilitas secara real-time.'
        ])

        {{-- ═══ FILTER BAR ═══ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-5 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-2 flex-1">
                <svg class="w-4 h-4 text-[#1265A8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest shrink-0">Fasilitas</label>
                <select id="fasilitasSelect" onchange="onFasilitasChange(this.value)"
                    class="flex-1 pl-3 pr-8 py-2 rounded-xl border border-slate-200 appearance-none bg-slate-50 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-[#1265A8] cursor-pointer">
                    @foreach($facilities as $f)
                        <option value="{{ $f->id }}" {{ $f->id == $selectedFasilitasId ? 'selected' : '' }}>{{ $f->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Month nav --}}
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="changeMonth(-1)"
                    class="w-9 h-9 flex items-center justify-center bg-slate-50 border border-slate-200 rounded-xl hover:bg-[#1265A8] hover:text-white hover:border-[#1265A8] transition-all active:scale-90">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <span id="monthLabel" class="text-sm font-black text-slate-700 min-w-[120px] text-center"></span>
                <button onclick="changeMonth(1)"
                    class="w-9 h-9 flex items-center justify-center bg-slate-50 border border-slate-200 rounded-xl hover:bg-[#1265A8] hover:text-white hover:border-[#1265A8] transition-all active:scale-90">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <button onclick="goToday()"
                    class="px-3 py-2 text-[11px] font-black bg-blue-50 text-[#1265A8] rounded-xl border border-blue-100 hover:bg-[#1265A8] hover:text-white transition-all active:scale-90">
                    Hari Ini
                </button>
            </div>
        </div>

        {{-- ═══ LEGEND ═══ --}}
        <div class="flex flex-wrap gap-x-5 gap-y-2 mb-4 px-1">
            <div class="flex items-center gap-1.5"><div class="legend-dot bg-emerald-100 border border-emerald-300"></div><span class="text-[11px] font-semibold text-slate-500">Ready (Klik untuk blokir)</span></div>
            <div class="flex items-center gap-1.5"><div class="legend-dot bg-yellow-200 border border-yellow-300"></div><span class="text-[11px] font-semibold text-slate-500">Pending</span></div>
            <div class="flex items-center gap-1.5"><div class="legend-dot bg-blue-200 border border-blue-300"></div><span class="text-[11px] font-semibold text-slate-500">Booked</span></div>
            <div class="flex items-center gap-1.5"><div class="legend-dot bg-slate-800"></div><span class="text-[11px] font-semibold text-slate-500">Blocked</span></div>
            <div class="flex items-center gap-1.5"><div class="legend-dot bg-red-400"></div><span class="text-[11px] font-semibold text-slate-500">Maintenance</span></div>
            <div class="flex items-center gap-1.5"><div class="legend-dot bg-slate-200"></div><span class="text-[11px] font-semibold text-slate-500">Lewat</span></div>
        </div>

        {{-- ═══ CALENDAR CARD ═══ --}}
        <div class="bg-white rounded-[1.8rem] border border-slate-100 shadow-sm p-4 md:p-6 mb-6">
            {{-- Day-of-week headers --}}
            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                <div class="text-center text-[10px] md:text-xs font-black text-slate-400 uppercase tracking-widest py-1">{{ $d }}</div>
                @endforeach
            </div>
            {{-- Calendar grid filled by JS --}}
            <div id="calendar-grid" class="min-h-[300px]">
                <div class="col-span-7 flex items-center justify-center py-12">
                    <svg class="btn-spin w-8 h-8 text-[#1265A8]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
            </div>
        </div>

        {{-- ═══ QUICK STATS MINI ═══ --}}
        <div id="mini-stats" class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6"></div>

        {{-- ── Contextual Bubble ── --}}
        <div id="quickBlockBubble" class="quick-block-bubble" onclick="goToFormBlokir()">
            Quick Block
        </div>

    </main>

    {{-- ══════════════════════════════════════════════
         MODAL 1: Quick Block (Ready → Blocked/Maintenance)
    ══════════════════════════════════════════════ --}}
    <div id="modalBlock" class="modal-overlay" onclick="closeModal('modalBlock', event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Quick Block</h3>
                    <p id="blockDateLabel" class="text-xs text-slate-400 font-medium mt-0.5"></p>
                </div>
                <button onclick="closeModal('modalBlock')" class="w-9 h-9 flex items-center justify-center bg-slate-100 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Tipe toggle --}}
            <div class="flex gap-2 mb-5">
                <button id="btnTypeBlocked" onclick="setBlockType('blocked')"
                    class="flex-1 py-2.5 rounded-xl text-xs font-black border-2 transition-all border-slate-200 text-slate-500">
                    🔒 Blocked (Internal)
                </button>
                <button id="btnTypeMaint" onclick="setBlockType('Maintenance')"
                    class="flex-1 py-2.5 rounded-xl text-xs font-black border-2 transition-all border-slate-200 text-slate-500">
                    🔧 Maintenance
                </button>
            </div>

            <form id="formBlock" onsubmit="submitBlock(event)" class="space-y-3">
                <input type="hidden" id="blockFasilitasId">
                <input type="hidden" id="blockTglMulai">
                <input type="hidden" id="blockTglSelesai">
                <input type="hidden" id="blockTipe" value="blocked">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 mb-1 block">Tanggal Mulai</label>
                        <input type="date" id="blockTglMulaiInput" class="form-input" required onchange="syncBlockDates()">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 mb-1 block">Tanggal Selesai</label>
                        <input type="date" id="blockTglSelesaiInput" class="form-input" required onchange="syncBlockDates()">
                    </div>
                </div>

                <div id="blockedExtraFields" class="space-y-3">
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 mb-1 block">Nama PIC *</label>
                        <input type="text" id="blockNama" class="form-input" placeholder="Nama petugas internal">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 mb-1 block">Divisi</label>
                            <input type="text" id="blockDivisi" class="form-input" placeholder="Divisi/unit">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 mb-1 block">WhatsApp</label>
                            <input type="text" id="blockWA" class="form-input" placeholder="08xx">
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 mb-1 block">Durasi (hari)</label>
                        <input type="number" id="blockDurasi" class="form-input" placeholder="Otomatis terhitung" min="1" readonly>
                    </div>
                </div>

                <div id="maintExtraFields" class="hidden space-y-3">
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 mb-1 block">Catatan Perbaikan</label>
                        <textarea id="blockCatatan" class="form-input resize-none" rows="3" placeholder="Deskripsi kegiatan Maintenance..."></textarea>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 mb-1 block">Penanggung Jawab</label>
                        <input type="text" id="blockNamaMaint" class="form-input" placeholder="Nama PJ Maintenance">
                    </div>
                </div>

                <button type="submit" id="btnSubmitBlock"
                    class="w-full mt-2 py-3 rounded-2xl text-sm font-black bg-slate-900 text-white hover:bg-[#1265A8] transition-all active:scale-95 shadow-lg">
                    Blokir Jadwal
                </button>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL 2: Booking Detail (Pending / Booked)
    ══════════════════════════════════════════════ --}}
    <div id="modalDetail" class="modal-overlay" onclick="closeModal('modalDetail', event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Detail Penyewa</h3>
                    <p id="detailDateLabel" class="text-xs text-slate-400 font-medium mt-0.5"></p>
                </div>
                <button onclick="closeModal('modalDetail')" class="w-9 h-9 flex items-center justify-center bg-slate-100 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Status badge --}}
            <div id="detailStatusBadge" class="inline-flex mb-4 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest border"></div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-start py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ID Booking</span>
                    <span id="detailBookingId" class="font-black text-slate-800 text-right"></span>
                </div>
                <div class="flex justify-between items-start py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nama</span>
                    <span id="detailNama" class="font-bold text-slate-700 text-right"></span>
                </div>
                <div class="flex justify-between items-start py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email</span>
                    <span id="detailEmail" class="font-bold text-slate-700 text-right break-all"></span>
                </div>
                <div class="flex justify-between items-start py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">WhatsApp</span>
                    <a id="detailWaLink" href="#" target="_blank" class="font-bold text-[#1265A8] hover:underline text-right"></a>
                </div>
                <div class="flex justify-between items-start py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Fasilitas</span>
                    <span id="detailFasilitas" class="font-bold text-slate-700 text-right"></span>
                </div>
                <div class="flex justify-between items-start py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Paket</span>
                    <span id="detailPaket" class="font-bold text-slate-700"></span>
                </div>
                <div class="flex justify-between items-start py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total</span>
                    <span id="detailHarga" class="font-black text-[#1265A8]"></span>
                </div>
                <div class="flex justify-between items-start py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Dibuat</span>
                    <span id="detailCreated" class="font-semibold text-slate-500 text-right text-xs"></span>
                </div>
                <div class="flex justify-between items-start py-2.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Diupdate</span>
                    <span id="detailUpdated" class="font-semibold text-slate-500 text-right text-xs"></span>
                </div>
            </div>

            <div class="mt-5 flex gap-2">
                <a id="detailWaBtn" href="#" target="_blank"
                   class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-green-500 text-white text-xs font-black hover:bg-green-600 transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
                <a id="detailReceiptBtn" href="#"
                   class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-[#1265A8] text-white text-xs font-black hover:bg-blue-700 transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Unduh Kuitansi
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL 3: Blocked Detail (Hapus / Info)
    ══════════════════════════════════════════════ --}}
    <div id="modalBlocked" class="modal-overlay" onclick="closeModal('modalBlocked', event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-black text-slate-800" id="modalBlockedTitle">Detail Blokir</h3>
                <button onclick="closeModal('modalBlocked')" class="w-9 h-9 flex items-center justify-center bg-slate-100 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tipe</span>
                    <span id="blockedTipe" class="font-black"></span>
                </div>
                <div class="flex justify-between py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Periode</span>
                    <span id="blockedPeriode" class="font-bold text-slate-700 text-right"></span>
                </div>
                <div class="flex justify-between py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">PIC / Catatan</span>
                    <span id="blockedPIC" class="font-bold text-slate-700 text-right"></span>
                </div>
                <div class="flex justify-between py-2.5 border-b border-slate-50">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Dibuat</span>
                    <span id="blockedCreated" class="font-semibold text-slate-500 text-xs"></span>
                </div>
            </div>

            <button id="btnHapusBlokir" onclick="hapusBlokir()"
                class="w-full mt-5 py-3 rounded-2xl text-sm font-black bg-red-500 text-white hover:bg-red-600 transition-all active:scale-95 shadow-lg">
                🔓 Buka Blokir (Hapus)
            </button>
        </div>
    </div>

    <script>
    // ════════════════════════════════════════════════════
    //  STATE
    // ════════════════════════════════════════════════════
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentYear  = {{ now()->year }};
    let currentMonth = {{ now()->month }};
    let currentFasilitasId = {{ $selectedFasilitasId ?? 'null' }};
    let calendarEvents = [];
    let activeBlockId  = null;

    const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const today   = new Date();
    today.setHours(0,0,0,0);

    // ════════════════════════════════════════════════════
    //  INIT
    // ════════════════════════════════════════════════════
    document.addEventListener('DOMContentLoaded', () => {
        renderCalendar();
    });

    // Global click to hide bubble
    document.addEventListener('click', (e) => {
        const bubble = document.getElementById('quickBlockBubble');
        if (!e.target.closest('.cal-day.status-ready')) {
            hideQuickBlockBubble();
        }
    });

    function hideQuickBlockBubble() {
        const bubble = document.getElementById('quickBlockBubble');
        bubble.classList.remove('show');
        activeBubbleDate = null;
    }

    let activeBubbleDate = null;

    function onFasilitasChange(id) {
        currentFasilitasId = id;
        renderCalendar();
    }

    function changeMonth(delta) {
        currentMonth += delta;
        if (currentMonth > 12) { currentMonth = 1; currentYear++; }
        if (currentMonth < 1)  { currentMonth = 12; currentYear--; }
        renderCalendar();
    }

    function goToday() {
        currentYear  = today.getFullYear();
        currentMonth = today.getMonth() + 1;
        renderCalendar();
    }

    // ════════════════════════════════════════════════════
    //  FETCH & RENDER CALENDAR
    // ════════════════════════════════════════════════════
    async function renderCalendar() {
        document.getElementById('monthLabel').textContent = MONTHS[currentMonth-1] + ' ' + currentYear;
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = `<div class="col-span-7 flex items-center justify-center py-12">
            <svg class="btn-spin w-8 h-8 text-[#1265A8]" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg></div>`;

        if (!currentFasilitasId) return;

        try {
            const res = await fetch(`/admin/dashboard/kontrolJadwal/data?fasilitas_id=${currentFasilitasId}&year=${currentYear}&month=${currentMonth}`);
            calendarEvents = await res.json();
        } catch(e) {
            calendarEvents = [];
        }

        buildGrid();
        buildMiniStats();
    }

    function buildGrid() {
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
        const prevMonthDays = new Date(currentYear, currentMonth - 1, 0).getDate();

        // Prev month filler days
        for (let i = firstDay - 1; i >= 0; i--) {
            const d = prevMonthDays - i;
            grid.appendChild(createDayCell(d, true, null, null));
        }

        // Current month days
        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${currentYear}-${String(currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const { statusClass, tooltipText, eventData } = getDayInfo(dateStr);
            const isToday = (new Date(currentYear, currentMonth-1, d).getTime() === today.getTime());
            grid.appendChild(createDayCell(d, false, statusClass, tooltipText, eventData, dateStr, isToday));
        }

        // Next month filler
        const totalCells = firstDay + daysInMonth;
        const remaining  = (7 - (totalCells % 7)) % 7;
        for (let d = 1; d <= remaining; d++) {
            grid.appendChild(createDayCell(d, true, null, null));
        }
    }

    function getDayInfo(dateStr) {
        const date = new Date(dateStr);
        date.setHours(0,0,0,0);

        // Check past
        if (date < today) return { statusClass: 'status-past', tooltipText: 'Tanggal Lewat', eventData: null };

        // Find matching event
        for (const ev of calendarEvents) {
            const start = new Date(ev.tgl_mulai); start.setHours(0,0,0,0);
            const end   = new Date(ev.tgl_selesai); end.setHours(23,59,59,999);
            if (date >= start && date <= end) {
                if (ev.color === 'yellow')   return { statusClass: 'status-pending',     tooltipText: '🕐 Pending — ' + ev.nama,   eventData: ev };
                if (ev.color === 'blue')     return { statusClass: 'status-booked',      tooltipText: '✅ Booked — ' + ev.nama,    eventData: ev };
                if (ev.color === 'purple')   return { statusClass: 'status-booked',      tooltipText: '🏠 Booked — ' + ev.nama,    eventData: ev };
                if (ev.color === 'black')    return { statusClass: 'status-blocked',     tooltipText: '🔒 Blocked',               eventData: ev };
                if (ev.color === 'red')      return { statusClass: 'status-maintenance', tooltipText: '🔧 Maintenance',               eventData: ev };
            }
        }

        return { statusClass: 'status-ready', tooltipText: 'Klik untuk blokir', eventData: null };
    }

    function createDayCell(day, isOther, statusClass, tooltipText, eventData, dateStr, isToday) {
        const el = document.createElement('div');
        el.className = 'cal-day ' + (isOther ? 'other-month ' : '') + (statusClass || 'status-ready') + (isToday ? ' pulse-ring' : '');

        // Number
        const num = document.createElement('span');
        num.className = 'relative z-10 text-base leading-none';
        num.textContent = day;
        el.appendChild(num);

        // Dot badge for events
        if (eventData && !isOther) {
            const dot = document.createElement('span');
            dot.className = 'absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full';
            if (eventData.color === 'yellow') dot.classList.add('bg-yellow-400');
            else if (eventData.color === 'blue') dot.classList.add('bg-blue-400');
            else if (eventData.color === 'purple') dot.classList.add('bg-blue-400');
            else if (eventData.color === 'black') dot.classList.add('bg-slate-300');
            else if (eventData.color === 'red') dot.classList.add('bg-red-500');
            el.appendChild(dot);
        }

        // Maintenance tooltip (hover only — no click action)
        if (tooltipText && !isOther) {
            const tip = document.createElement('div');
            tip.className = 'status-tooltip';
            tip.textContent = tooltipText;
            el.appendChild(tip);
        }

        // Click handlers
        if (!isOther) {
            el.addEventListener('click', (e) => handleDayClick(e, statusClass, eventData, dateStr));
        }

        return el;
    }

    // ════════════════════════════════════════════════════
    //  CLICK ROUTING
    // ════════════════════════════════════════════════════
    function handleDayClick(event, statusClass, eventData, dateStr) {
        if (statusClass === 'status-past' || statusClass === 'status-maintenance') {
            hideQuickBlockBubble();
            return;
        }

        if (statusClass === 'status-ready') {
            showQuickBlockBubble(event, dateStr);
        } else {
            hideQuickBlockBubble();
            if (statusClass === 'status-pending' || statusClass === 'status-booked') {
                openDetailModal(eventData);
            } else if (statusClass === 'status-blocked') {
                openBlockedModal(eventData);
            }
        }
    }

    function showQuickBlockBubble(e, dateStr) {
        // Prevent bubbling to document listener
        if (e && e.stopPropagation) e.stopPropagation();

        const bubble = document.getElementById('quickBlockBubble');
        if (!bubble) return;

        // Use click coordinates for precise positioning
        const x = e.clientX;
        const y = e.clientY;
        
        // Offset slightly to the top-right from the click point
        bubble.style.left = `${x + 10}px`;
        bubble.style.top  = `${y - 10}px`;
        
        activeBubbleDate = dateStr;
        bubble.classList.add('show');
    }

    function goToFormBlokir() {
        if (!activeBubbleDate) return;
        const url = "{{ route('kontrolJadwal.formBlokir') }}?fasilitas_id=" + currentFasilitasId + "&date=" + activeBubbleDate;
        window.location.href = url;
    }

    // ════════════════════════════════════════════════════
    //  MODAL: QUICK BLOCK
    // ════════════════════════════════════════════════════
    function openBlockModal(dateStr) {
        document.getElementById('blockDateLabel').textContent = 'Tanggal: ' + formatDateID(dateStr);
        document.getElementById('blockTglMulai').value = dateStr;
        document.getElementById('blockTglSelesai').value = dateStr;
        document.getElementById('blockTglMulaiInput').value = dateStr;
        document.getElementById('blockTglSelesaiInput').value = dateStr;
        document.getElementById('blockTglMulaiInput').min = dateStr;
        document.getElementById('blockTglSelesaiInput').min = dateStr;
        document.getElementById('blockFasilitasId').value = currentFasilitasId;
        document.getElementById('blockNama').value = '';
        document.getElementById('blockDivisi').value = '';
        document.getElementById('blockWA').value = '';
        document.getElementById('blockCatatan').value = '';
        document.getElementById('blockNamaMaint').value = '';
        syncBlockDates();
        setBlockType('blocked');
        openModal('modalBlock');
    }

    function setBlockType(type) {
        document.getElementById('blockTipe').value = type;
        const btnB = document.getElementById('btnTypeBlocked');
        const btnM = document.getElementById('btnTypeMaint');
        const extraB = document.getElementById('blockedExtraFields');
        const extraM = document.getElementById('maintExtraFields');

        if (type === 'blocked') {
            btnB.classList.replace('border-slate-200','border-slate-900');
            btnB.classList.replace('text-slate-500','text-slate-900');
            btnM.classList.remove('border-orange-500','text-orange-600');
            btnM.classList.add('border-slate-200','text-slate-500');
            extraB.classList.remove('hidden');
            extraM.classList.add('hidden');
            document.getElementById('btnSubmitBlock').textContent = '🔒 Blokir Jadwal';
        } else {
            btnM.classList.replace('border-slate-200','border-orange-500');
            btnM.classList.replace('text-slate-500','text-orange-600');
            btnB.classList.remove('border-slate-900','text-slate-900');
            btnB.classList.add('border-slate-200','text-slate-500');
            extraM.classList.remove('hidden');
            extraB.classList.add('hidden');
            document.getElementById('btnSubmitBlock').textContent = '🔧 Tandai Maintenance';
        }
    }

    function syncBlockDates() {
        const s = document.getElementById('blockTglMulaiInput').value;
        const e = document.getElementById('blockTglSelesaiInput').value;
        document.getElementById('blockTglMulai').value = s;
        document.getElementById('blockTglSelesai').value = e;
        if (s && e) {
            const diff = Math.round((new Date(e) - new Date(s)) / 86400000) + 1;
            document.getElementById('blockDurasi').value = diff > 0 ? diff : 1;
        }
    }

    async function submitBlock(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitBlock');
        btn.disabled = true;
        btn.innerHTML = '<svg class="btn-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyimpan...';

        const tipe = document.getElementById('blockTipe').value;
        const payload = {
            fasilitas_id : document.getElementById('blockFasilitasId').value,
            tgl_mulai    : document.getElementById('blockTglMulai').value,
            tgl_selesai  : document.getElementById('blockTglSelesai').value,
            tipe         : tipe,
            durasi       : document.getElementById('blockDurasi').value,
        };

        if (tipe === 'blocked') {
            payload.nama_pic = document.getElementById('blockNama').value;
            payload.divisi   = document.getElementById('blockDivisi').value;
            payload.whatsapp = document.getElementById('blockWA').value;
        } else {
            payload.catatan  = document.getElementById('blockCatatan').value;
            payload.nama_pic = document.getElementById('blockNamaMaint').value;
        }

        try {
            const res = await fetch('/admin/jadwal/blokir', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                closeModal('modalBlock');
                Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success', timer: 1800, showConfirmButton: false, customClass: { popup: 'rounded-[1.5rem]' } })
                    .then(() => renderCalendar());
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
            }
        } catch(err) {
            Swal.fire('Error', 'Koneksi bermasalah.', 'error');
        }

        btn.disabled = false;
        btn.textContent = 'Blokir Jadwal';
    }

    // ════════════════════════════════════════════════════
    //  MODAL: BOOKING DETAIL
    // ════════════════════════════════════════════════════
    function openDetailModal(ev) {
        if (!ev) return;
        document.getElementById('detailDateLabel').textContent = formatDateID(ev.tgl_mulai) + ' — ' + formatDateID(ev.tgl_selesai);
        document.getElementById('detailBookingId').textContent = ev.booking_id;
        document.getElementById('detailNama').textContent = ev.nama;
        document.getElementById('detailEmail').textContent = ev.email;
        document.getElementById('detailFasilitas').textContent = ev.fasilitas;
        document.getElementById('detailPaket').textContent = ev.package_type.charAt(0).toUpperCase() + ev.package_type.slice(1);
        document.getElementById('detailHarga').textContent = ev.total_harga;
        document.getElementById('detailCreated').textContent = ev.created_at;
        document.getElementById('detailUpdated').textContent = ev.updated_at;

        // WA
        const waNum = ev.whatsapp.replace(/\D/g,'');
        const waUrl = 'https://wa.me/62' + waNum.replace(/^0/, '');
        document.getElementById('detailWaLink').textContent = ev.whatsapp;
        document.getElementById('detailWaLink').href = waUrl;
        document.getElementById('detailWaBtn').href = waUrl;

        // Receipt
        document.getElementById('detailReceiptBtn').href = `/admin/bookings/${ev.id}/receipt`;

        // Status badge
        const badge = document.getElementById('detailStatusBadge');
        badge.textContent = ev.status.charAt(0).toUpperCase() + ev.status.slice(1);
        badge.className = 'inline-flex mb-4 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest border ';
        if (ev.status === 'pending') badge.className += 'bg-amber-50 text-amber-600 border-amber-200 animate-pulse';
        else badge.className += 'bg-blue-50 text-[#1265A8] border-blue-200';

        openModal('modalDetail');
    }

    // ════════════════════════════════════════════════════
    //  MODAL: BLOKIR DETAIL
    // ════════════════════════════════════════════════════
    function openBlockedModal(ev) {
        if (!ev) return;
        activeBlockId = ev.id;

        document.getElementById('modalBlockedTitle').textContent = ev.status === 'blocked' ? '🔒 Detail Blokir' : '🔧 Detail Maintenance';
        const tipeEl = document.getElementById('blockedTipe');
        if (ev.status === 'blocked') {
            tipeEl.innerHTML = '<span class="px-2 py-1 rounded-lg bg-slate-800 text-slate-100 text-xs">BLOCKED</span>';
        } else {
            tipeEl.innerHTML = '<span class="px-2 py-1 rounded-lg bg-orange-100 text-orange-700 text-xs">Maintenance</span>';
        }
        document.getElementById('blockedPeriode').textContent = formatDateID(ev.tgl_mulai) + ' — ' + formatDateID(ev.tgl_selesai);
        document.getElementById('blockedPIC').textContent = ev.nama_pic !== '-' ? ev.nama_pic + (ev.divisi !== '-' ? ' (' + ev.divisi + ')' : '') : ev.catatan;
        document.getElementById('blockedCreated').textContent = ev.created_at;

        openModal('modalBlocked');
    }

    async function hapusBlokir() {
        if (!activeBlockId) return;
        const result = await Swal.fire({
            title: 'Buka Blokir?',
            text: 'Tanggal ini akan kembali tersedia untuk booking.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Buka Blokir',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[1.5rem]' }
        });
        if (!result.isConfirmed) return;

        try {
            const res = await fetch(`/admin/jadwal/blokir/${activeBlockId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await res.json();
            if (data.success) {
                closeModal('modalBlocked');
                Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success', timer: 1600, showConfirmButton: false, customClass: { popup: 'rounded-[1.5rem]' } })
                    .then(() => renderCalendar());
            }
        } catch(err) {
            Swal.fire('Error', 'Koneksi bermasalah.', 'error');
        }
    }

    // ════════════════════════════════════════════════════
    //  MINI STATS
    // ════════════════════════════════════════════════════
    function buildMiniStats() {
        let stats = { ready: 0, pending: 0, booked: 0, blocked: 0, Maintenance: 0 };
        const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${currentYear}-${String(currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const date = new Date(dateStr); date.setHours(0,0,0,0);
            if (date < today) continue;
            const { statusClass } = getDayInfo(dateStr);
            if (statusClass === 'status-ready')       stats.ready++;
            else if (statusClass === 'status-pending')   stats.pending++;
            else if (statusClass === 'status-booked')    stats.booked++;
            else if (statusClass === 'status-blocked')   stats.blocked++;
            else if (statusClass === 'status-Maintenance') stats.Maintenance++;
        }

        const items = [
            { label: 'Ready',       count: stats.ready,       bg: '#d1fae5', text: '#065f46', border: '#6ee7b7' },
            { label: 'Pending',     count: stats.pending,     bg: '#fef9c3', text: '#854d0e', border: '#fcd34d' },
            { label: 'Booked',      count: stats.booked,      bg: '#dbeafe', text: '#1e40af', border: '#93c5fd' },
            { label: 'Blocked',     count: stats.blocked,     bg: '#1e293b', text: '#f8fafc', border: '#334155' },
            { label: 'Maintenance', count: stats.Maintenance, bg: '#ffedd5', text: '#9a3412', border: '#fdba74' },
        ];

        const container = document.getElementById('mini-stats');
        container.innerHTML = items.map(i => `
            <div class="bg-white rounded-2xl border shadow-sm p-4 flex items-center gap-3" style="border-color:${i.border}30">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm" style="background:${i.bg};color:${i.text}">${i.count}</div>
                <span class="text-[11px] font-bold text-slate-500">${i.label}</span>
            </div>
        `).join('');
    }

    // ════════════════════════════════════════════════════
    //  MODAL HELPERS
    // ════════════════════════════════════════════════════
    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id, event) {
        if (event && event.target !== document.getElementById(id)) return;
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }

    // ════════════════════════════════════════════════════
    //  UTILS
    // ════════════════════════════════════════════════════
    function formatDateID(str) {
        if (!str) return '-';
        const d = new Date(str);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-container') || document.querySelector('aside');
        if (sidebar) sidebar.classList.toggle('-translate-x-full');
    }
    </script>
</body>
</html>
