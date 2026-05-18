<!-- resources/views/admin/dashboard/layouts/header.blade.php -->
<header class="mb-8 md:mb-10">
    {{-- Alert Banner for Pending Requests --}}
    <div id="pending-alert-banner" class="hidden mb-6 transform transition-all duration-500 ease-in-out translate-y-[-20px] opacity-0">
        <div class="bg-gradient-to-r from-[#EF4444] to-[#f43f5e] p-4 rounded-2xl shadow-lg shadow-rose-200 flex items-center justify-between group">
            <div class="flex items-center gap-4 text-white">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center animate-pulse">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-black text-sm uppercase tracking-wider">Permintaan Booking Baru!</h4>
                    <p class="text-xs text-rose-100 font-medium">Ada <span id="banner-count" class="font-black underline">0</span> permintaan booking yang menunggu verifikasi Anda.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="/admin/dashboard/managementBooking" class="px-4 py-2 bg-white text-[#EF4444] rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-50 transition-colors shadow-sm">
                    Kelola Sekarang
                </a>
                <button onclick="dismissAlertBanner()" class="p-2 text-white/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center justify-between md:justify-start gap-4 flex-1">
            <div class="relative">
                <div class="absolute -left-4 top-0 bottom-0 w-1 bg-gradient-to-b from-[#1265A8] to-transparent rounded-full opacity-50 hidden md:block"></div>
                
                <h2 class="text-2xl md:text-3xl font-black tracking-tight text-slate-800 flex items-center gap-3">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-slate-900 via-[#1265A8] to-[#4292DC]">
                        {{ $headerTitle ?? 'Admin Dashboard' }}
                    </span>
                    
                    <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#1265A8] border border-blue-100 uppercase tracking-widest animate-pulse">
                        Live
                    </span>
                </h2>
                
                <p class="mt-1 text-slate-400 text-xs md:text-sm font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2 text-[#1265A8]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ $headerSubtitle ?? 'Selamat datang di pusat kendali operasional anda.' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 lg:gap-6" x-data="{ userMenuOpen: false }">
            {{-- 1. Search Input --}}
            <div class="hidden lg:block">
                @include('admin.dashboard.search.searchBar')
            </div>

            <div class="flex items-center gap-3 md:gap-4">
                {{-- 2. Notification Bell --}}
                <div class="relative group">
                    <a href="/admin/dashboard/managementBooking" 
                        class="p-3 bg-white rounded-xl border border-slate-100 text-slate-400 
                        transition-all duration-300 ease-out
                        hover:bg-blue-50 hover:border-blue-200 hover:text-[#1265A8] 
                        hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] 
                        active:scale-95 flex items-center justify-center">
                        
                        <svg class="w-6 h-6 transition-transform duration-300 group-hover:rotate-12" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>

                        <span id="header-bell-badge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-[#EF4444] text-white text-[10px] font-black items-center justify-center rounded-full border-2 border-white animate-bounce">
                            0
                        </span>
                    </a>
                </div>

                {{-- 3. User Profile Dropdown --}}
                <div class="relative">
                    <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false"
                        class="flex items-center gap-3 p-1 bg-white border border-slate-100 rounded-2xl transition-all duration-300 hover:border-blue-200 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] active:scale-95 group">
                        <div class="w-10 h-10 rounded-[14px] bg-gradient-to-br from-[#1265A8] to-[#4292DC] flex items-center justify-center text-white font-black text-sm shadow-md shadow-blue-200 group-hover:rotate-3 transition-transform">
                            {{ substr(session('nama') ?? 'A', 0, 1) }}
                        </div>
                        <div class="hidden sm:block text-left mr-2">
                            <p class="text-[11px] font-black text-slate-800 leading-tight">{{ session('nama') ?? 'Administrator' }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ session('role') ?? 'Admin' }}</p>
                        </div>
                        <svg class="hidden sm:block w-4 h-4 text-slate-300 transition-transform duration-300 mr-2" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="userMenuOpen" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="absolute right-0 mt-3 w-72 bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-50 overflow-hidden z-50 py-2"
                        x-cloak>
                        
                        {{-- 1. Username Display --}}
                        <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/30">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-[#1265A8] text-[10px] font-black border border-blue-100">
                                    ID
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 leading-none">Username</p>
                                    <p class="text-sm font-black text-[#1265A8] truncate leading-none">@ {{ session('username') ?? 'admin' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-2 space-y-1">
                            {{-- 2. Clickable WhatsApp Link --}}
                            <a href="https://wa.me/6281234567890?text=Halo%20Owner,%20saya%20ingin%20mengajukan%20perubahan%20data%20atau%20reset%20password%20admin%20BOE-Space." 
                                target="_blank"
                                class="flex items-start gap-4 px-4 py-4 bg-amber-50/50 hover:bg-amber-50 rounded-2xl border border-amber-100/30 transition-all group/item">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-amber-500 shadow-sm border border-amber-100/50 shrink-0 group-hover/item:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-amber-800 leading-relaxed italic group-hover/item:text-amber-900 transition-colors">
                                        Ingin ganti password? Hubungi Owner via website untuk perubahan data.
                                    </p>
                                    <span class="inline-flex items-center gap-1 text-[8px] font-black uppercase tracking-widest text-amber-400 mt-2">
                                        WhatsApp Owner 
                                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </span>
                                </div>
                            </a>

                            {{-- 3. Logout Button --}}
                            <button onclick="confirmHeaderLogout()" 
                                class="w-full flex items-center justify-between px-5 py-4 text-rose-500 hover:bg-rose-500 hover:text-white rounded-[1.5rem] transition-all duration-300 group/logout mt-1">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-400 group-hover/logout:bg-white/20 group-hover/logout:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-[0.2em]">Logout</span>
                                </div>
                                <svg class="w-4 h-4 opacity-0 -translate-x-2 group-hover/logout:opacity-100 group-hover/logout:translate-x-0 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Mobile Sidebar Toggle --}}
                <button onclick="toggleSidebar()" 
                    class="md:hidden p-3 bg-white rounded-xl border border-slate-100 text-[#1265A8] 
                    transition-all duration-300 ease-out
                    hover:bg-blue-50 hover:border-blue-200 hover:text-[#4292DC] 
                    active:scale-95 group">
                    
                    <svg class="w-6 h-6 transition-transform duration-300 group-hover:rotate-180" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    function dismissAlertBanner() {
        const banner = document.getElementById('pending-alert-banner');
        if (banner) {
            banner.classList.add('opacity-0', 'translate-y-[-20px]');
            setTimeout(() => banner.classList.add('hidden'), 500);
            sessionStorage.setItem('notif_banner_dismissed', 'true');
        }
    }

    function confirmHeaderLogout() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Keluar Sistem?',
                text: "Anda harus login kembali untuk mengakses dashboard.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', 
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-xs',
                    cancelButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-xs'
                }
            }).then((result) => { 
                if (result.isConfirmed) {
                    window.location.href = '/';
                } 
            });
        } else {
            if(confirm("Keluar dari sistem?")) {
                window.location.href = '/';
            }
        }
    }
</script>
