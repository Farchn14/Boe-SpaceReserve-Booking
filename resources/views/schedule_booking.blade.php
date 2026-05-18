<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Jadwal Pembookingan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        [x-cloak] { display: none !important; }

        /* ── Calendar Grid ── */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .cal-day {
            min-height: 60px;
            border-radius: 14px;
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
        @media (min-width: 768px) { .cal-day { min-height: 80px; font-size: 15px; } }

        .cal-day.other-month { opacity: .28; pointer-events: none; }
        .cal-day.today-marker::after {
            content: ''; position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%);
            width: 5px; height: 5px; border-radius: 50%; background: #1265A8;
        }

        /* ── Status Colors (Mirrored from Admin) ── */
        /* ── Status Colors (Unified Status) ── */
        .status-ready       { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; } /* GREEN */
        .status-pending     { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; } /* YELLOW (Pending) */
        .status-booked      { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; } /* BLUE (Booked) */
        .status-blocked     { background: #1e293b; color: #f1f5f9; border: 1px solid #0f172a; } /* BLACK */
        .status-maintenance { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; } /* RED */
        .status-past        { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; } /* GREY */

        .status-tooltip {
            position: absolute; bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%);
            background: #1e293b; color: #fff; font-size: 10px; font-weight: 600;
            padding: 4px 10px; border-radius: 8px; white-space: nowrap;
            pointer-events: none; opacity: 0; transition: opacity .2s;
            z-index: 50;
        }
        .cal-day:hover .status-tooltip { opacity: 1; }
        @media (min-width: 768px) { .cal-day:hover { transform: scale(1.04); z-index: 10; } }

        /* Custom Scrollbar for Dropdown */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen">
    
    <x-layout.navbar />

    <main class="max-w-6xl mx-auto px-4 pt-32 pb-12" 
        x-data="scheduleManager({
            facilities: {{ $facilities->toJson() }},
            initialFasilitasId: {{ $selectedFasilitasId ?? 'null' }}
        })"
        x-cloak>
        
        {{-- ═══ HEADER ═══ --}}
        <header class="mb-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-[#1265A8] rounded-full text-[10px] font-bold uppercase tracking-widest mb-3 border border-blue-100">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1265A8]"></span>
                        </span>
                        Live Schedule
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight leading-none">Cek Ketersediaan Room</h1>
                    <p class="mt-2 text-slate-500 text-sm font-medium italic">Pantau jadwal pemakaian fasilitas secara real-time.</p>
                </div>

                {{-- Facility Detail Button --}}
                <a href="/#booking" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-[#1265A8] hover:bg-slate-50 transition-all shadow-sm">
                    Kembali ke List Room
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- ═══ LEFT SIDE: CONTROL & LEGEND ═══ --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Selector Card --}}
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 block">Pilih Fasilitas</label>
                    
                    <div class="space-y-3">
                        <template x-for="f in facilities" :key="f.id">
                            <button @click="selectFacility(f.id)" 
                                class="w-full text-left px-5 py-5 rounded-[1.5rem] border-2 transition-all duration-500 group relative overflow-hidden"
                                :class="selectedId == f.id ? 'border-[#1265A8] bg-blue-50/50 shadow-lg shadow-blue-100' : 'border-slate-50 hover:border-slate-200 bg-slate-50/30'">
                                
                                <div class="flex items-center justify-between relative z-10">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase tracking-widest text-[#1265A8] mb-1 opacity-60" x-text="f.tipe"></span>
                                        <span class="text-[15px] font-black tracking-tight" 
                                            :class="selectedId == f.id ? 'text-[#1265A8]' : 'text-slate-600'"
                                            x-text="f.nama"></span>
                                    </div>
                                    
                                    <template x-if="selectedId == f.id">
                                        <div class="bg-[#1265A8] text-white p-1 rounded-full shadow-lg scale-100 transition-transform duration-500" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-0" x-transition:enter-end="scale-100">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    </template>
                                </div>

                                {{-- Active State Background Accent --}}
                                <div x-show="selectedId == f.id" x-transition.opacity class="absolute top-0 right-0 w-24 h-24 bg-blue-100/30 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Actions Check (Integrated - Hidden by default) --}}
                <div x-show="selectedId" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
                    class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-blue-900/20 relative overflow-hidden border border-white/5">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
                    
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] mb-8 text-blue-400">Keterangan Status</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="w-4 h-4 rounded-full bg-emerald-400 border border-emerald-300 shadow-[0_0_10px_rgba(52,211,153,0.3)]"></div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-emerald-50 uppercase tracking-widest">Tersedia (Ready)</span>
                                <span class="text-[9px] text-white/40 font-medium italic">Unit siap untuk digunakan/dipesan.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="w-4 h-4 rounded-full bg-yellow-400 border border-yellow-300 shadow-[0_0_10px_rgba(250,204,21,0.3)]"></div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-yellow-50 uppercase tracking-widest">Pending Konfirmasi</span>
                                <span class="text-[9px] text-white/40 font-medium italic">Dalam proses validasi oleh administrator.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="w-4 h-4 rounded-full bg-blue-400 border border-blue-300 shadow-[0_0_10px_rgba(96,165,250,0.3)]"></div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-blue-50 uppercase tracking-widest">Sudah Terbooking</span>
                                <span class="text-[9px] text-white/40 font-medium italic">Jadwal telah dikonfirmasi dan tidak tersedia.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="w-4 h-4 rounded-full bg-red-400 border border-red-300 shadow-[0_0_10px_rgba(248,113,113,0.3)]"></div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-red-50 uppercase tracking-widest">Maintenance (Repair)</span>
                                <span class="text-[9px] text-white/40 font-medium italic">Pemeliharaan rutin atau perbaikan unit.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="w-4 h-4 rounded-full bg-slate-900 border border-slate-700 shadow-[0_0_10px_rgba(0,0,0,0.3)]"></div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-slate-100 uppercase tracking-widest">Blokir / Locked</span>
                                <span class="text-[9px] text-white/40 font-medium italic">Jadwal dikunci untuk penggunaan internal.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="w-4 h-4 rounded-full bg-slate-100 border border-slate-200"></div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-slate-300 uppercase tracking-widest">Tanggal Terlewati</span>
                                <span class="text-[9px] text-white/40 font-medium italic">Hari sebelum tanggal hari ini.</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 p-5 bg-[#1265A8]/20 rounded-3xl border border-[#1265A8]/30 backdrop-blur-sm relative z-10">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-[10px] leading-relaxed text-blue-100 font-medium">
                                <span class="font-black text-white block mb-1 uppercase tracking-widest">Informasi Privasi:</span>
                                Identitas penyewa disamarkan. Silakan hubungi pusat bantuan kami jika diperlukan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ RIGHT SIDE: CALENDAR GRID (Revealed Section) ═══ --}}
            <div class="lg:col-span-8 space-y-6">
                
                <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 p-8 md:p-12 relative overflow-hidden min-h-[600px] flex flex-col">
                    {{-- Decorative Background --}}
                    <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                    
                    {{-- Placeholder State --}}
                    <div x-show="!selectedId" x-transition.opacity class="flex-1 flex flex-col items-center justify-center text-center p-10 relative z-10">
                        <div class="w-24 h-24 bg-blue-50 text-blue-200 rounded-[2rem] flex items-center justify-center mb-6">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-2">Lihat Kalender Fasilitas</h3>
                        <p class="text-sm text-slate-400 font-medium max-w-xs mx-auto">Pilih Fasilitas Terlebih Dahulu Untuk Melihat Jadwal Ketersediaan Secara Real-Time.</p>
                    </div>

                    {{-- Calendar Content (Revealed) --}}
                    <div x-show="selectedId" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="flex-1 flex flex-col">
                        {{-- Calendar Header --}}
                        <div class="flex items-center justify-between mb-12 relative z-10">
                            <div class="flex items-center gap-4">
                                <button @click="changeMonth(-1)" class="w-12 h-12 flex items-center justify-center bg-slate-50 rounded-2xl hover:bg-[#1265A8] hover:text-white transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <div class="text-center min-w-[200px]">
                                    <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tighter" x-text="monthName"></h2>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]" x-text="currentYear"></p>
                                </div>
                                <button @click="changeMonth(1)" class="w-12 h-12 flex items-center justify-center bg-slate-50 rounded-2xl hover:bg-[#1265A8] hover:text-white transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                            <button @click="goToday()" class="px-6 py-3 text-[11px] font-black bg-slate-900 text-white rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-lg tracking-widest">HARI INI</button>
                        </div>

                        {{-- Day of Week --}}
                        <div class="grid grid-cols-7 gap-1 mb-6 relative z-10">
                            @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
                            <div class="text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] py-2">{{ $day }}</div>
                            @endforeach
                        </div>

                        {{-- Grid Content --}}
                        <div class="calendar-grid relative z-10 min-h-[400px]">
                            {{-- Loading Overlay --}}
                            <div x-show="isLoading" class="absolute inset-0 z-50 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center gap-5 transition-all">
                                <div class="w-12 h-12 border-4 border-slate-100 border-t-[#1265A8] rounded-full animate-spin"></div>
                                <div class="flex flex-col items-center gap-1">
                                    <p class="text-[11px] font-black text-[#1265A8] uppercase tracking-[0.3em] animate-pulse">Sinkronisasi Data</p>
                                    <p class="text-[9px] font-medium text-slate-400 italic">Mohon tunggu sebentar...</p>
                                </div>
                            </div>

                            <template x-for="(day, index) in daysInMonth" :key="index">
                                <div class="cal-day transition-all group" 
                                    :class="[
                                        day.isOther ? 'other-month opacity-20 pointer-events-none' : '',
                                        day.statusClass ? day.statusClass : 'status-ready',
                                        day.isToday && !day.isOther ? 'today-marker shadow-inner ring-2 ring-[#1265A8]/10' : ''
                                    ]">
                                    
                                    <span class="relative z-10" x-text="day.day"></span>
                                    
                                    {{-- Tooltip --}}
                                    <template x-if="!day.isOther && day.tooltip">
                                        <div class="status-tooltip" x-text="day.tooltip"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Action Card (Revealed / Prompt Card) --}}
                <div x-show="selectedId" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
                    class="bg-gradient-to-br from-[#1265A8] to-[#276AD7] rounded-[3rem] p-10 flex flex-col lg:flex-row items-center justify-between gap-10 relative overflow-hidden group shadow-2xl shadow-blue-500/20">
                    {{-- Animated background elements --}}
                    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-80 h-80 bg-white/10 rounded-full blur-[100px] group-hover:scale-150 transition-transform duration-1000"></div>
                    <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-64 h-64 bg-blue-400/20 rounded-full blur-[80px]"></div>
                    
                    <div class="relative z-10 text-center lg:text-left max-w-xl">
                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/10 rounded-full border border-white/20 mb-6">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                            </span>
                            <span class="text-white font-black uppercase tracking-[0.2em] text-[10px]">Ready to Reservate</span>
                        </div>
                        <h4 class="text-white text-3xl md:text-4xl font-black mb-4 leading-tight tracking-tighter">Sudah Menemukan Jadwal Kosong?</h4>
                        <p class="text-blue-100 text-lg font-medium opacity-90 leading-relaxed">Lanjutkan ke pengisian form reservasi sekarang juga.</p>
                    </div>
                    
                    <div class="relative z-10 shrink-0">
                        <a :href="'{{ route('formBooking') }}?id=' + selectedId" 
                            class="px-12 py-6 bg-white text-[#1265A8] font-black rounded-[2rem] shadow-[0_20px_40px_rgba(255,255,255,0.2)] hover:bg-slate-900 hover:text-white hover:-translate-y-2 transition-all active:scale-95 text-xs uppercase tracking-[0.3em] flex items-center gap-4 group/btn">
                            Booking Room Ini
                            <svg class="w-5 h-5 group-hover/btn:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('scheduleManager', (config) => ({
                facilities: config.facilities,
                selectedId: config.initialFasilitasId,
                showCalendar: !!config.initialFasilitasId,
                isLoading: false,
                currentYear: new Date().getFullYear(),
                currentMonth: new Date().getMonth() + 1,
                daysInMonth: [],
                events: [],
                today: new Date(),

                init() {
                    this.today.setHours(0,0,0,0);
                    this.buildCalendar();
                    if (this.selectedId) {
                        this.fetchData();
                    }
                },

                selectFacility(id) {
                    this.selectedId = id;
                    this.showCalendar = true;
                    this.fetchData();
                    
                    // Smooth scroll to calendar
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.querySelector('.lg:col-span-8').offsetTop - 120,
                            behavior: 'smooth'
                        });
                    }, 100);
                },

                async fetchData() {
                    if (!this.selectedId) return;
                    this.isLoading = true;
                    try {
                        const res = await fetch(`/schedule_booking/data?fasilitas_id=${this.selectedId}&year=${this.currentYear}&month=${this.currentMonth}&t=${Date.now()}`);
                        this.events = await res.json();
                        this.buildCalendar();
                    } catch (e) {
                        console.error("Fetch Data Error:", e);
                        this.events = [];
                    } finally {
                        this.isLoading = false;
                    }
                },

                buildCalendar() {
                    const year = this.currentYear;
                    const month = this.currentMonth;
                    
                    const firstDay = new Date(year, month - 1, 1).getDay();
                    const daysInMonth = new Date(year, month, 0).getDate();
                    const prevMonthDays = new Date(year, month - 1, 0).getDate();
                    
                    const tempDays = [];

                    // Previous month days
                    for (let i = firstDay - 1; i >= 0; i--) {
                        tempDays.push({ day: prevMonthDays - i, isOther: true });
                    }

                    // Current month days
                    for (let d = 1; d <= daysInMonth; d++) {
                        const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                        const { statusClass, tooltip } = this.getDayInfo(dateStr);
                        const isToday = (new Date(year, month - 1, d).getTime() === this.today.getTime());
                        
                        tempDays.push({
                            day: d,
                            isOther: false,
                            statusClass,
                            tooltip,
                            isToday,
                            dateStr
                        });
                    }

                    // Next month days
                    const remaining = (7 - (tempDays.length % 7)) % 7;
                    for (let d = 1; d <= remaining; d++) {
                        tempDays.push({ day: d, isOther: true });
                    }

                    this.daysInMonth = tempDays;
                },

                getDayInfo(dateStr) {
                    const date = new Date(dateStr);
                    date.setHours(0,0,0,0);

                    // Grey for past dates
                    if (date < this.today) {
                        return { statusClass: 'status-past', tooltip: 'Tanggal Terlewati' };
                    }

                    for (const ev of this.events) {
                        const start = new Date(ev.tgl_mulai); start.setHours(0,0,0,0);
                        const end   = new Date(ev.tgl_selesai); end.setHours(23,59,59,999);
                        
                        if (date >= start && date <= end) {
                            if (ev.color === 'yellow') {
                                return { statusClass: 'status-pending', tooltip: 'Pending Konfirmasi' };
                            }
                            if (ev.color === 'blue') {
                                return { statusClass: 'status-booked', tooltip: 'Sudah Terbooking' };
                            }
                            if (ev.color === 'black') {
                                return { statusClass: 'status-blocked', tooltip: 'Diblokir / Locked' };
                            }
                            if (ev.color === 'red') {
                                return { statusClass: 'status-maintenance', tooltip: 'Maintenance: ' + (ev.reason || 'Perbaikan') };
                            }
                        }
                    }

                    // Default ready
                    return { statusClass: 'status-ready', tooltip: 'Tersedia' };
                },

                changeMonth(delta) {
                    this.currentMonth += delta;
                    if (this.currentMonth > 12) { this.currentMonth = 1; this.currentYear++; }
                    if (this.currentMonth < 1)  { this.currentMonth = 12; this.currentYear--; }
                    this.fetchData();
                },

                goToday() {
                    const n = new Date();
                    this.currentYear = n.getFullYear();
                    this.currentMonth = n.getMonth() + 1;
                    this.fetchData();
                },

                get monthName() {
                    const names = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    return names[this.currentMonth - 1];
                }
            }));
        });
    </script>
</body>
</html>

