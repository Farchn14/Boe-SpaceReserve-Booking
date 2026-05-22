<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            overflow-x: hidden; 
        }

        .chart-wrapper {
            width: 100%;
            overflow-x: auto; 
            scrollbar-width: thin; 
        }
        
        .chart-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .chart-wrapper::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        
        .chart-area {
            min-width: 600px; 
            height: 250px;
        }

        .btn-shadow { 
            box-shadow: 0 4px 14px 0 rgba(18, 101, 168, 0.39); 
        }

        .action-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .action-card:hover {
            transform: translateY(-8px);
        }
        
        .icon-container {
            background: linear-gradient(135deg, #1265A8 0%, #4292DC 100%);
        }

        /* Efek Glassmorphism */
        .glass-card-modern {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 8px 32px 0 rgba(15, 23, 42, 0.05);
        }

        /* Efek Hover Floating & Glow */
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px -15px rgba(18, 101, 168, 0.15);
            border-color: rgba(18, 101, 168, 0.2);
        }

        /* Gradient Text */
        .text-gradient-blue {
            background: linear-gradient(135deg, #1265A8 0%, #4292DC 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Animasi Icon saat Hover */
        .group:hover .icon-float {
            transform: scale(1.1) rotate(5deg);
        }

        .counter-val {
            will-change: transform, opacity;
            display: inline-block;
        }
    </style>
</head>
<body class="flex min-h-screen">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 md:ml-64 p-6 md:p-10">
        @include('admin.dashboard.layouts.header', [
            'headerTitle' => 'Admin Dashboard',
            'headerSubtitle' => 'Selamat datang di pusat kendali operasional anda.'
        ])

        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        {{-- Dashboard Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            {{-- Card Data Fasilitas --}}
            <div class="glass-card-modern card-hover group p-7 rounded-[2.5rem] transition-all duration-500 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-6">
                        <div class="p-3 bg-blue-50 rounded-2xl icon-float transition-transform duration-300">
                            <svg class="w-6 h-6 text-[#1265A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        {{-- Badge dinamis: tampilkan jumlah fasilitas aktif --}}
                        <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 text-[11px] font-extrabold tracking-wider">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            {{ $countFasilitas }} Unit
                        </span>
                    </div>
                    <div class="space-y-1 mb-6">
                        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-[0.15em]">Data Fasilitas</h3>
                        <div class="flex items-baseline gap-2">
                            <p class="counter-val text-5xl font-black tracking-tighter text-slate-800"
                            data-target="{{ $countFasilitas }}"
                            data-prefix=""
                            data-suffix="">0</p>
                            <span class="text-slate-400 font-bold text-sm">Units</span>
                        </div>
                    </div>
                    <div class="pt-5 border-t border-slate-100/80 flex justify-between items-center">
                        <p class="text-[11px] text-slate-400 font-medium">Monitoring ketersediaan</p>
                        <a href="/admin/dashboard/dataFasilitas" class="flex items-center gap-2 text-[#1265A8] text-xs font-bold group/link">
                            Detail <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card History Booking --}}
            <div class="glass-card-modern card-hover group p-7 rounded-[2.5rem] transition-all duration-500 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-6">
                        <div class="p-3 bg-indigo-50 rounded-2xl icon-float transition-transform duration-300">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        {{-- Badge dinamis: tampilkan booking bulan ini --}}
                        @php $bookingBulanIni = $bookingPerBulan[now()->month - 1] ?? 0; @endphp
                        <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 text-[11px] font-extrabold tracking-wider">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            {{ $bookingBulanIni }} Bulan ini
                        </span>
                    </div>
                    <div class="space-y-1 mb-6">
                        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-[0.15em]">History Booking</h3>
                        <div class="flex items-baseline gap-2">
                            <p class="counter-val text-5xl font-black tracking-tighter text-slate-800"
                            data-target="{{ $countBooking }}"
                            data-prefix=""
                            data-suffix="">0</p>
                            <span class="text-slate-400 font-bold text-sm">Records</span>
                        </div>
                    </div>
                    <div class="pt-5 border-t border-slate-100/80 flex justify-between items-center">
                        <p class="text-[11px] text-slate-400 font-medium">Pusat kelola reservasi</p>
                        <a href="/admin/dashboard/historyBooking" class="flex items-center gap-2 text-[#1265A8] text-xs font-bold group/link">
                            Detail <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            {{-- Analytics: Data Pengunjung --}}
            <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-sm border border-slate-100">

                {{-- Header --}}
                <div class="flex items-start justify-between mb-6 flex-wrap gap-3">
                    <div>
                        <h4 class="text-sm font-bold text-slate-700 flex items-center">
                            <span class="w-2 h-2 bg-[#1265A8] rounded-full mr-2"></span>
                            Data Pengunjung
                        </h4>
                        <p class="text-[11px] text-slate-400 ml-4 mt-0.5">Jumlah booking aktif per bulan</p>
                    </div>
                    <span class="bg-blue-50 text-[#0C447C] text-[11px] font-semibold px-3 py-1.5 rounded-full">
                        {{ now()->year }}
                    </span>
                </div>

                {{-- Stat Cards --}}
                @php
                    $total     = $bookingPerBulan->sum();
                    $peakVal   = $bookingPerBulan->max();
                    $peakMonth = $bookingPerBulan->search($peakVal);
                    $avg       = $total > 0 ? round($total / 12) : 0;
                    $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                @endphp
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-slate-50 rounded-2xl p-4">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Total tahun ini</p>
                        <p class="text-xl font-black text-slate-800">{{ $total }}</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 mt-1 inline-block">↑ aktif</span>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-3.5">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Bulan tertinggi</p>
                        <p class="text-xl font-black text-slate-800">{{ $peakVal > 0 ? $monthNames[$peakMonth] : '—' }}</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 mt-1 inline-block">
                            {{ $peakVal > 0 ? $peakVal.' booking' : '—' }}
                        </span>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-3.5">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Rata-rata / bulan</p>
                        <p class="text-xl font-black text-slate-800">{{ $avg }}</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 mt-1 inline-block">booking</span>
                    </div>
                </div>

                {{-- Chart --}}
                <div class="chart-wrapper mt-2">
                    <div class="chart-area">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="flex items-center gap-2 mt-4 pb-1">
                    <span class="w-2 h-2 rounded-full bg-[#1265A8]"></span>
                    <span class="text-[10px] text-slate-400">Booking diterima (exclude rejected & cancelled)</span>
                </div>
            </div>

            {{-- Analytics: Data Fasilitas --}}
            <div class="p-8 md:p-10 rounded-[2.5rem] border border-white/75"
                style="background:rgba(255,255,255,0.55);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);box-shadow:0 8px 32px rgba(18,101,168,0.08);">

                {{-- Header --}}
                <div class="flex items-start justify-between mb-6 flex-wrap gap-3">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 flex items-center">
                            <span class="w-2 h-2 bg-[#1265A8] rounded-full mr-2"></span>
                            Data Fasilitas
                        </h4>
                        <p class="text-[11px] text-slate-400 ml-4 mt-0.5">Distribusi booking per fasilitas</p>
                    </div>
                    <span class="text-[11px] font-semibold px-3 py-1.5 rounded-full border"
                        style="background:rgba(18,101,168,0.08);color:#0C447C;border-color:rgba(18,101,168,0.15);">
                        {{ $fasilitasChart->count() }} fasilitas
                    </span>
                </div>

                {{-- Stat Cards --}}
                @php
                    $topFas     = $fasilitasChart->sortByDesc('bookings_count')->first();
                    $totalAktif = $fasilitasChart->sum('bookings_count');
                    $topPct     = $totalAktif > 0 ? round($topFas?->bookings_count / $totalAktif * 100) : 0;
                @endphp
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="rounded-[1.25rem] p-4 border border-white/80" style="background:rgba(255,255,255,0.6);">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Fasilitas terbanyak</p>
                        <p class="text-lg font-black text-slate-900 truncate">{{ $topFas?->nama ?? '—' }}</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full mt-1.5 inline-block"
                            style="background:rgba(18,101,168,0.1);color:#0C447C;">
                            {{ $topFas?->bookings_count ?? 0 }} booking · {{ $topPct }}%
                        </span>
                    </div>
                    <div class="rounded-[1.25rem] p-4 border border-white/80" style="background:rgba(255,255,255,0.6);">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Total booking aktif</p>
                        <p class="text-lg font-black text-slate-900">{{ $totalAktif }}</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full mt-1.5 inline-block bg-slate-100 text-slate-500">
                            semua fasilitas
                        </span>
                    </div>
                </div>

                {{-- Chart + Legend --}}
                <div class="flex items-center gap-6">
                    <div class="relative flex-shrink-0" style="width:175px;height:175px;">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                    <div class="flex-1 flex flex-col gap-2.5 min-w-0" id="fasilitasLegend"></div>
                </div>
            </div>
        </div>

        {{-- Control Panel Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Card Kelola Tempat --}}
            <div class="action-card group bg-white rounded-[2.5rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-slate-50 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="icon-container w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-extrabold text-slate-800 mb-2">Kelola Tempat</h3>
                    <p class="text-slate-400 text-sm mb-8">Atur ketersediaan, kelengkapan, dan detail operasional seluruh fasilitas Anda dalam satu panel kendali.</p>
                    
                    <div class="flex flex-col gap-3">
                        @if(session('role') === 'owner' || filter_var(session('can_edit'), FILTER_VALIDATE_BOOLEAN))
                        <a href="/admin/dashboard/create/createFasilitas" onclick="handleNavClick(event, this)" class="nav-btn-loading flex items-center justify-center w-full px-6 py-4 bg-white text-slate-600 border-2 border-slate-100 rounded-2xl font-bold text-sm hover:border-[#4292DC] hover:text-[#4292DC] transition-all">
                            <span class="btn-text flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Fasilitas Baru
                            </span>
                        </a>
                        @endif
                        
                        <a href="/admin/dashboard/dataFasilitas" onclick="handleNavClick(event, this)" class="nav-btn-loading group/btn flex items-center justify-between w-full px-6 py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm transition-all hover:bg-[#1265A8] active:scale-95 shadow-lg shadow-slate-200">
                            <span class="btn-text">Lihat Semua Fasilitas</span>
                            <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card Manajemen Admin (Owner Only) --}}
            @if(session('role') === 'owner')
            <div class="action-card group bg-white rounded-[2.5rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-slate-50 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>

                <div class="relative z-10">
                    <div class="icon-container w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-indigo-200">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-extrabold text-slate-800 mb-2">Manajemen Admin</h3>
                    <p class="text-slate-400 text-sm mb-8">Kelola hak akses dan pantau aktivitas administrator sistem.</p>
                    
                    <div class="flex flex-col gap-3">
                        <a href="/admin/dashboard/management/add_new_admin?from=dashboardMaster" onclick="handleNavClick(event, this)" class="nav-btn-loading flex items-center justify-center w-full px-6 py-4 bg-white text-slate-600 border-2 border-slate-100 rounded-2xl font-bold text-sm hover:border-[#4292DC] hover:text-[#4292DC] transition-all">
                            <span class="btn-text flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                Tambah Admin Baru
                            </span>
                        </a>
                        <a href="/admin/dashboard/management/admin_active_control" onclick="handleNavClick(event, this)" class="nav-btn-loading group/btn flex items-center justify-between w-full px-6 py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm transition-all hover:bg-[#1265A8] active:scale-95 shadow-lg shadow-slate-200">
                            <span class="btn-text">Lihat Daftar Admin Aktif</span>
                            <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    </main>

    {{-- Back to Top Button --}}
    <button id="backToTop" 
        class="fixed bottom-8 right-8 z-50 p-4 rounded-2xl bg-white/80 backdrop-blur-lg border border-slate-200 text-[#1265A8] shadow-2xl transition-all duration-500 translate-y-20 opacity-0 hover:bg-[#1265A8] hover:text-white hover:-translate-y-1 active:scale-90 group"
        aria-label="Back to Top">
        
        <div class="relative">
            <div class="absolute inset-0 bg-blue-400 blur-lg opacity-0 group-hover:opacity-40 transition-opacity"></div>
            
            <svg class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
            </svg>
        </div>
    </button>

    <script>
        // Sidebar Toggle (untuk Mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-container') || document.querySelector('aside');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
        }

        // Chart Initialization
        function initCharts() {
            const ctxLine = document.getElementById('lineChart')?.getContext('2d');
            const ctxDoughnut = document.getElementById('doughnutChart')?.getContext('2d');
            
            if (!ctxLine || !ctxDoughnut) return;

            // --- Line Chart ---
            const grad = ctxLine.createLinearGradient(0, 0, 0, 250);
            grad.addColorStop(0, 'rgba(18,101,168,0.18)');
            grad.addColorStop(1, 'rgba(18,101,168,0)');

            const months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
            const bookingData = @json($bookingPerBulan->values());

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Booking',
                        data: bookingData,
                        borderColor: '#1265A8',
                        borderWidth: 2.5,
                        backgroundColor: grad,
                        fill: true,
                        tension: 0.45,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#1265A8',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#1265A8',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#94a3b8',
                            bodyColor: '#f8fafc',
                            padding: 12,
                            cornerRadius: 12,
                            titleFont: { size: 11, weight: '500' },
                            bodyFont: { size: 13, weight: '500' },
                            callbacks: {
                                title: (items) => months[items[0].dataIndex],
                                label: (ctx) => '  ' + ctx.parsed.y + ' booking',
                            }
                        }
                    },
                    scales: {
                        y: {
                            min: 0,
                            beginAtZero: true,
                            border: { display: false, dash: [4, 4] },
                            grid: { color: '#f1f5f9', drawTicks: false },
                            ticks: {
                                padding: 10,
                                maxTicksLimit: 6,        // ← ganti stepSize dengan ini
                                precision: 0,            // ← pastikan integer, bukan desimal
                                color: '#64748b',
                                font: { size: 11, weight: '500', family: "'Plus Jakarta Sans'" },
                            }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 11, weight: '500', family: "'Plus Jakarta Sans'" },
                                padding: 6,
                            }
                        }
                    }
                }
            });

            // --- Doughnut Chart ---
            const fasilitasLabels = @json($fasilitasChart->pluck('nama'));
            const fasilitasRaw    = @json($fasilitasChart->pluck('bookings_count'));

            // Chart selalu sama rata per fasilitas (bukan per booking)
            const chartData = new Array(fasilitasLabels.length).fill(1);

            const chartColors = ['#1265A8','#4292DC','#64748b','#94a3b8','#cbd5e1','#3b82f6','#0ea5e9','#475569'];
            const totalAktif  = fasilitasRaw.reduce((a, b) => a + b, 0);

            const legendEl = document.getElementById('fasilitasLegend');
            if (legendEl) {
                fasilitasLabels.forEach((lbl, i) => {
                    // Persentase booking tetap dihitung dari data asli
                    const pct   = totalAktif > 0 ? Math.round(fasilitasRaw[i] / totalAktif * 100) : 0;
                    const color = chartColors[i % chartColors.length];
                    legendEl.innerHTML += `
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="width:9px;height:9px;border-radius:2px;background:${color};flex-shrink:0;"></span>
                        <span style="font-size:11px;font-weight:600;color:#334155;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${lbl}</span>
                        <div style="width:68px;background:rgba(0,0,0,0.07);border-radius:999px;height:5px;flex-shrink:0;">
                            <div style="width:${pct}%;height:5px;border-radius:999px;background:${color};"></div>
                        </div>
                        <span style="font-size:10px;color:#64748b;min-width:28px;text-align:right;font-weight:600;">${pct}%</span>
                    </div>`;
                });
            }

            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: fasilitasLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: chartColors.slice(0, fasilitasLabels.length),
                        borderWidth: 5,
                        borderColor: 'rgba(255,255,255,0.7)',
                        hoverOffset: 14,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.85)',
                            titleColor: '#94a3b8',
                            bodyColor: '#f8fafc',
                            padding: 12,
                            cornerRadius: 12,
                            callbacks: {
                                title: () => '',
                                label: (ctx) => {
                                    const pct = totalAktif > 0
                                        ? Math.round(fasilitasRaw[ctx.dataIndex] / totalAktif * 100)
                                        : 0;
                                    return '  ' + ctx.label + ' · ' + pct + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Animasi Angka (Statistik)
        function animateCounters() {
            const counters = document.querySelectorAll('.counter-val');

            const easeOutExpo = t => t === 1 ? 1 : 1 - Math.pow(2, -10 * t);

            counters.forEach((el, idx) => {
                const target   = +el.getAttribute('data-target');
                const prefix   = el.getAttribute('data-prefix') || '';
                const suffix   = el.getAttribute('data-suffix') || '';
                const duration = 2000;
                const delay    = idx * 200;

                // Set awal: invisible + geser ke bawah
                el.style.opacity    = '0';
                el.style.transform  = 'translateY(16px)';
                el.style.transition = 'none';

                setTimeout(() => {
                    // Fade + slide in
                    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    el.style.opacity    = '1';
                    el.style.transform  = 'translateY(0)';

                    if (target === 0) {
                        el.textContent = prefix + '0' + suffix;
                        return;
                    }

                    const startTime = performance.now();

                    const tick = (now) => {
                        const elapsed  = now - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        const eased    = easeOutExpo(progress);
                        const current  = Math.round(eased * target);

                        el.textContent = prefix + current.toLocaleString('id-ID') + suffix;

                        if (progress < 1) {
                            requestAnimationFrame(tick);
                        } else {
                            el.textContent = prefix + target.toLocaleString('id-ID') + suffix;
                        }
                    };

                    requestAnimationFrame(tick);

                }, delay);
            });
        }

        // Loading State pada Tombol Navigasi
        function handleNavClick(event, el) {
            const targetUrl = el.getAttribute('href');
            if (!targetUrl || targetUrl === '#') return;

            event.preventDefault();
            const isDarkBtn = el.classList.contains('bg-slate-900');
            const spinnerColor = isDarkBtn ? 'text-white' : 'text-blue-500';

            el.innerHTML = `
                <div class="flex items-center justify-center gap-3">
                    <svg class="animate-spin h-5 w-5 ${spinnerColor}" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="animate-pulse">MEMUAT...</span>
                </div>
            `;
            el.classList.add('pointer-events-none', 'opacity-80');
            setTimeout(() => { window.location.href = targetUrl; }, 600);
        }

        // Eksekusi saat halaman siap
        document.addEventListener('DOMContentLoaded', () => {
            initCharts();
            animateCounters();
        });

        // Back to Top Logic
        const backToTopBtn = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTopBtn.classList.remove('translate-y-20', 'opacity-0');
                backToTopBtn.classList.add('translate-y-0', 'opacity-100');
            } else {
                backToTopBtn.classList.add('translate-y-20', 'opacity-0');
                backToTopBtn.classList.remove('translate-y-0', 'opacity-100');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>