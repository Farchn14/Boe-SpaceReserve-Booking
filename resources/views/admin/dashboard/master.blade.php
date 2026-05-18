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
    </style>
</head>
<body class="flex min-h-screen">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 md:ml-64 p-6 md:p-10">
        @include('admin.dashboard.layouts.header', [
            'headerTitle' => 'Admin Dashboard',
            'headerSubtitle' => 'Selamat datang di pusat kendali operasional anda.'
        ])

        {{-- Dashboard Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            
            <div class="glass-card-modern card-hover group p-7 rounded-[2.5rem] transition-all duration-500 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-6">
                        <div class="p-3 bg-blue-50 rounded-2xl icon-float transition-transform duration-300">
                            <svg class="w-6 h-6 text-[#1265A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 text-[11px] font-extrabold tracking-wider">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            +12.5%
                        </span>
                    </div>
                    
                    <div class="space-y-1 mb-6">
                        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-[0.15em]">Data Fasilitas</h3>
                        <div class="flex items-baseline gap-2">
                            <p class="stat-value text-5xl font-black tracking-tighter text-slate-800" data-target="{{ $countFasilitas }}">0</p>
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

            <div class="glass-card-modern card-hover group p-7 rounded-[2.5rem] transition-all duration-500 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-6">
                        <div class="p-3 bg-indigo-50 rounded-2xl icon-float transition-transform duration-300">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 text-[11px] font-extrabold tracking-wider">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            Active
                        </span>
                    </div>
                    
                    <div class="space-y-1 mb-6">
                        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-[0.15em]">History Booking</h3>
                        <div class="flex items-baseline gap-2">
                            <p class="stat-value text-5xl font-black tracking-tighter text-slate-800" data-target="{{ $countBooking }}">0</p>
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
            <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-slate-100 searchable-section">
                <h4 class="text-sm font-bold text-slate-700 mb-6 flex items-center">
                    <span class="w-2 h-2 bg-[#1265A8] rounded-full mr-2"></span> Data Pengunjung (Jan - Des)
                </h4>
                <div class="chart-wrapper">
                    <div class="chart-area">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-slate-100 searchable-section">
                <h4 class="text-sm font-bold text-slate-700 mb-6 flex items-center">
                    <span class="w-2 h-2 bg-[#1265A8] rounded-full mr-2"></span> Data Fasilitas
                </h4>
                <div class="chart-wrapper flex justify-center items-center">
                    <div class="relative w-full h-[250px] flex justify-center">
                        <canvas id="doughnutChart"></canvas>
                    </div>
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
                        <a href="/admin/dashboard/management/add_new_admin" onclick="handleNavClick(event, this)" class="nav-btn-loading flex items-center justify-center w-full px-6 py-4 bg-white text-slate-600 border-2 border-slate-100 rounded-2xl font-bold text-sm hover:border-[#4292DC] hover:text-[#4292DC] transition-all">
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
        const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Tingkat Okupansi',
                    data: [10, 25, 20, 35, 30, 45, 50, 40, 60, 75, 80, 95],
                    borderColor: '#1265A8',
                    backgroundColor: '#ffffff',
                    fill: true,
                    tension: 0.4
                    
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => ` ${context.parsed.y}%`
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        beginAtZero: true,
                        border: { display: false },
                        grid: {
                            color: '#f1f5f9', 
                            drawTicks: false
                        },
                        ticks: {
                            stepSize: 20, 
                            padding: 10,
                            callback: function(value) {
                                return value + '%'; 
                            },
                            font: { 
                                family: "'Plus Jakarta Sans'", 
                                size: 12,
                                weight: '500'
                            },
                            color: '#64748b' 
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });

        // --- Doughnut Chart ---
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Asrama Tunggul Ametung', 'Asrama Ken Umang', 'Asrama Kendedes', 'Asrama Ken Arok', 'Asrama Kertajaya', 'Aula Utama'],
                datasets: [{
                    data: [30, 20, 15, 10, 15, 10], // Persentase/Jumlah data
                    backgroundColor: [
                        '#1265A8', // Biru Utama
                        '#4292DC', // Biru Muda
                        '#94a3b8', // Slate 400
                        '#cbd5e1', // Slate 300
                        '#1e293b', // Slate 800
                        '#e2e8f0'  // Slate 200
                    ],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Membuat lubang tengah lebih besar 
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Plus Jakarta Sans'", size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
    }

    // Animasi Angka (Statistik)
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-value');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const duration = 1500;
            const startTime = performance.now();

            const updateCount = (currentTime) => {
                const elapsedTime = currentTime - startTime;
                const progress = Math.min(elapsedTime / duration, 1);
                const easeOutQuad = (t) => t * (2 - t);
                
                counter.innerText = Math.floor(easeOutQuad(progress) * target);

                if (progress < 1) requestAnimationFrame(updateCount);
                else counter.innerText = target;
            };
            requestAnimationFrame(updateCount);
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