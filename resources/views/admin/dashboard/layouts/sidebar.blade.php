<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BOE-Space Reserve | Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            overflow-x: hidden; 
        }

        #sidebar { 
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        }

        #overlay { 
            display: block; 
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s;
            backdrop-filter: blur(0px);
        }

        #overlay.active { 
            opacity: 1;
            visibility: visible;
            backdrop-filter: blur(4px); 
        }

        .sidebar-active { 
            background: rgba(18, 101, 168, 0.1); 
            border-right: 4px solid #1265A8; 
            color: #1265A8; 
        }

        .btn-close-sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-close-sidebar:hover {
            background-color: #fee2e2; 
            color: #ef4444; 
            transform: rotate(90deg);
        }
    </style>
</head>
<body>
    <div id="sidebar-overlay" 
        onclick="toggleSidebar()" 
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden opacity-0 transition-opacity duration-300 md:hidden">
    </div>
    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-30 md:pointer-events-none"></div>

    <aside id="sidebar-container" class="w-64 bg-white border-r border-slate-100 flex flex-col fixed h-full z-40 transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0" id="sidebar">
        <div class="p-8 relative">
            <h1 class="text-2xl font-black text-[#1265A8] leading-tight tracking-tighter">
                BOE-Space<br><span class="text-slate-400">Reserve</span>
            </h1>

            <button onclick="toggleSidebar()" 
                class="md:hidden absolute top-6 right-2 btn-close-sidebar w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl shadow-sm border border-slate-100"
                aria-label="Close Sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-4 space-y-1">
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Main Menu</p>
            
            <a href="/admin/dashboard/master" class="{{ request()->is('admin/dashboard/master') ? 'sidebar-active' : 'text-slate-500 hover:text-[#1265A8] hover:bg-slate-50' }} flex items-center px-4 py-3 rounded-xl font-bold transition-all">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>

            <a href="/admin/dashboard/dataFasilitas" class="{{ request()->is('admin/dashboard/dataFasilitas') ? 'sidebar-active' : 'text-slate-500 hover:text-[#1265A8] hover:bg-slate-50' }} flex items-center px-4 py-3 rounded-xl transition-all font-semibold">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Data Fasilitas
            </a>


            <a href="/admin/dashboard/kontrolJadwal" class="{{ request()->is('admin/dashboard/kontrolJadwal*') ? 'sidebar-active' : 'text-slate-500 hover:text-[#1265A8] hover:bg-slate-50' }} flex items-center px-4 py-3 rounded-xl transition-all font-semibold">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Kontrol Jadwal
            </a>

            <a href="/admin/dashboard/managementBooking" class="{{ request()->is('admin/dashboard/managementBooking') ? 'sidebar-active' : 'text-slate-500 hover:text-[#1265A8] hover:bg-slate-50' }} flex items-center justify-between px-4 py-3 rounded-xl transition-all font-semibold group/item">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Booking Management
                </div>
                
                {{-- Red Badge --}}
                <span id="sidebar-notif-badge" class="hidden px-2 py-0.5 bg-[#EF4444] text-white text-[9px] font-black rounded-full shadow-sm shadow-rose-200 group-hover/item:scale-110 transition-transform">
                    0
                </span>
            </a>

            <a href="/admin/dashboard/historyBooking" class="{{ request()->is('admin/dashboard/historyBooking') ? 'sidebar-active' : 'text-slate-500 hover:text-[#1265A8] hover:bg-slate-50' }} flex items-center px-4 py-3 rounded-xl transition-all font-semibold">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                History Booking
            </a>

            @if(session('role') === 'owner')
            <a href="/admin/dashboard/auditLog" class="{{ request()->is('admin/dashboard/auditLog') ? 'sidebar-active' : 'text-slate-500 hover:text-[#1265A8] hover:bg-slate-50' }} flex items-center px-4 py-3 rounded-xl transition-all font-semibold">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Audit Log
            </a>
            @endif

        </nav>

        <div class="p-6 mt-auto border-t border-slate-50">
            <div class="flex items-center gap-3 mb-4">
                <img src="/image/logo/tutwuri-logo.svg" class="w-8 h-8 opacity-80" alt="Logo">
                <p class="text-[10px] leading-tight text-slate-400 font-medium">Powered By<br><span class="text-slate-600 font-bold uppercase">BBPPMPV BOE MALANG</span></p>
            </div>
            <p class="text-[10px] text-slate-400">&copy; 2026 BOE. All Rights Reserved.</p>
        </div>
    </aside>

    <script>
        // Sidebar Toggle Logic
        function toggleSidebar() {
            const sidebar = document.querySelector('aside') || document.getElementById('sidebar-container');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (!sidebar || !overlay) return;

            // Periksa apakah sidebar sedang tertutup
            const isClosed = sidebar.classList.contains('-translate-x-full');

            if (isClosed) {
                // MUNCULKAN SIDEBAR
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                
                // MUNCULKAN OVERLAY (SMOOTH)
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100', 'pointer-events-auto');
                
                // Kunci scroll layar utama
                document.body.style.overflow = 'hidden';
            } else {
                // SEMBUNYIKAN SIDEBAR
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                
                // SEMBUNYIKAN OVERLAY (SMOOTH)
                overlay.classList.remove('opacity-100', 'pointer-events-auto');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                
                // Aktifkan kembali scroll
                document.body.style.overflow = 'auto';
            }
        }
    </script>
    
    {{-- REAL-TIME NOTIFICATION SYSTEM --}}
    <script>
        let lastPendingCount = 0;
        let notificationPermissionGranted = false;

        document.addEventListener('DOMContentLoaded', () => {
            // Initial check
            fetchNotifications();
            
            // Request permission for push notifications
            if ("Notification" in window) {
                if (Notification.permission === "granted") {
                    notificationPermissionGranted = true;
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            notificationPermissionGranted = true;
                        }
                    });
                }
            }

            // Polling every 30 seconds
            setInterval(fetchNotifications, 30000);
        });

        async function fetchNotifications() {
            try {
                const response = await fetch('/admin/notifications/count');
                const data = await response.json();
                
                if (data.success) {
                    updateNotificationUI(data.count);
                    
                    // Trigger browser notification only if count increased
                    if (data.count > lastPendingCount) {
                        triggerBrowserNotification(data.count);
                        // Show banner if new requests arrive (even if previously dismissed)
                        sessionStorage.removeItem('notif_banner_dismissed');
                    }
                    
                    lastPendingCount = data.count;
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        }

        function updateNotificationUI(count) {
            const sidebarBadge = document.getElementById('sidebar-notif-badge');
            const headerBadge = document.getElementById('header-bell-badge');
            const banner = document.getElementById('pending-alert-banner');
            const bannerCount = document.getElementById('banner-count');

            // Update Badges
            if (count > 0) {
                if (sidebarBadge) {
                    sidebarBadge.innerText = count;
                    sidebarBadge.classList.remove('hidden');
                }
                if (headerBadge) {
                    headerBadge.innerText = count;
                    headerBadge.classList.remove('hidden', 'hidden'); // Just to be sure
                    headerBadge.style.display = 'flex';
                }
                
                // Update Banner
                if (banner && bannerCount) {
                    bannerCount.innerText = count;
                    const isDismissed = sessionStorage.getItem('notif_banner_dismissed') === 'true';
                    
                    if (!isDismissed) {
                        banner.classList.remove('hidden');
                        setTimeout(() => {
                            banner.classList.remove('opacity-0', 'translate-y-[-20px]');
                        }, 100);
                    }
                }
            } else {
                if (sidebarBadge) sidebarBadge.classList.add('hidden');
                if (headerBadge) {
                    headerBadge.classList.add('hidden');
                    headerBadge.style.display = 'none';
                }
                if (banner) {
                    banner.classList.add('opacity-0', 'translate-y-[-20px]');
                    setTimeout(() => banner.classList.add('hidden'), 500);
                }
            }
        }

        function triggerBrowserNotification(count) {
            if (notificationPermissionGranted) {
                const title = "Booking Baru!";
                const options = {
                    body: `Ada ${count} permintaan booking yang menunggu persetujuan Anda.`,
                    icon: "/image/logo/tutwuri-logo.svg",
                    badge: "/image/logo/tutwuri-logo.svg",
                    vibrate: [200, 100, 200],
                    tag: "booking-notification" // Prevents spamming with multiple notifications
                };
                
                const notification = new Notification(title, options);
                
                notification.onclick = function(event) {
                    event.preventDefault();
                    window.focus();
                    window.location.href = "/admin/dashboard/managementBooking";
                    notification.close();
                };
            }
        }
    </script>
</body>
</html>