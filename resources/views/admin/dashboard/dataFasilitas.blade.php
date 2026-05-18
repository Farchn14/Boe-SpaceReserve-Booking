<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            overflow-x: hidden; 
        }

        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes shimmer {
            100% { transform: translateX(250%); }
        }
    </style>
</head>
<body class="flex min-h-screen">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 md:ml-64 p-6 md:p-10">
        @include('admin.dashboard.layouts.header', [
            'headerTitle' => 'Data Fasilitas',
            'headerSubtitle' => 'Selamat datang di manajemen data fasilitas.'
        ])
        
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <section x-data="{ 
            openPreview: false, 
            previewImg: '', 
            previewTitle: '',
            maintenanceModal: false,
            maintData: { id: null, name: '', start_date: '', end_date: '', reason: '' },
            openMaintenanceModal(id, name) {
                this.maintData = { id: id, name: name, start_date: new Date().toISOString().split('T')[0], end_date: '', reason: '' };
                this.maintenanceModal = true;
            },
            submitMaintenance() {
                const url = `/admin/fasilitas/${this.maintData.id}/maintenance`;
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        tgl_mulai: this.maintData.start_date,
                        tgl_selesai: this.maintData.end_date,
                        tujuan: this.maintData.reason
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#ef4444'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        }).then(() => location.reload());
                    }
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan sistem.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    }).then(() => location.reload());
                });
            },
            handleMaintenanceToggle(id, name, isMaintenance) {
                if (!isMaintenance) {
                    this.openMaintenanceModal(id, name);
                } else {
                    this.cancelMaintenance(id, name);
                }
            },
            cancelMaintenance(id, name) {
                Swal.fire({
                    title: 'Selesai Perbaikan?',
                    text: `Apakah fasilitas ${name} sudah siap digunakan kembali?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1265A8',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tutup',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-[2rem] p-8' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = `/admin/fasilitas/${id}/cancel-maintenance`;
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#1265A8'
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan sistem.',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        });
                    }
                });
            }
        }">
            <div class="flex items-center justify-between mb-8">
                <div class="flex flex-col gap-1.5 p-2">
                    <h3 class="text-2xl font-extrabold tracking-tight text-slate-800 leading-none">
                        Daftar Fasilitas
                    </h3>
                    
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#1265A8] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1265A8]"></span>
                        </span>
                        <p class="text-[13px] font-medium text-slate-500 uppercase tracking-wider">
                            Total <span class="text-slate-900 font-bold">{{ count($facilities) }}</span> Fasilitas <span class="lowercase">tersedia</span>
                        </p>
                    </div>
                </div>

                @if(session('role') === 'owner' || filter_var(session('can_edit'), FILTER_VALIDATE_BOOLEAN))
                <a href="/admin/dashboard/create/createFasilitas" id="btnTambah" onclick="handleLoading(event, this)" class="group relative inline-flex items-center gap-2 px-8 py-3.5 bg-[#1265A8] text-white rounded-2xl font-bold text-sm transition-all duration-300 hover:bg-[#0d4d82] hover:shadow-[0_10px_20px_-10px_rgba(18,101,168,0.5)] active:scale-95 overflow-hidden">
                    <div class="absolute inset-0 w-1/2 h-full bg-white/10 skew-x-[-25deg] -translate-x-full group-hover:animate-[shimmer_0.75s_infinite]"></div>
                    
                    <div class="relative flex items-center gap-2">
                        <svg id="iconPlus" class="w-5 h-5 transition-all duration-500 group-hover:rotate-180" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>

                        <svg id="iconLoading" class="hidden w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span id="btnText">Tambah Fasilitas</span>
                    </div>
                </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($facilities as $item)
                <div class="group bg-white rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                    
                    {{-- Bagian Gambar dengan Hover Zoom & Eye Icon --}}
                    <div class="relative h-52 overflow-hidden cursor-pointer" 
                        @click="openPreview = true; previewImg = '{{ asset('storage/fasilitas/' . $item->image) }}'; previewTitle = '{{ $item->nama }}'; previewDesc = '{{ $item->deskripsi }}'">
                        
                        <img src="{{ asset('storage/fasilitas/' . $item->image) }}" 
                            alt="{{ $item['nama'] }}" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-125 {{ $item->is_maintenance ? 'grayscale brightness-75' : '' }}">
                        
                        @if($item->is_maintenance)
                        <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
                            <span class="flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white text-[10px] font-black rounded-lg shadow-lg uppercase tracking-widest border border-red-400/30 backdrop-blur-md">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                </span>
                                Mode Perbaikan
                            </span>
                        </div>
                        @endif

                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white/20 backdrop-blur-md p-3 rounded-full border border-white/50">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="mb-6">
                            <h4 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-[#1265A8] transition-colors">
                                {{ $item['nama'] }}
                            </h4>
                            <p class="text-slate-500 text-sm line-clamp-2 mb-4">
                                {{ $item->deskripsi }}
                            </p>

                            <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Maintenance Mode</span>
                                    <span class="text-[11px] font-bold {{ $item->is_maintenance ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $item->is_maintenance ? 'Sedang Perbaikan' : 'Siap Digunakan' }}
                                    </span>
                                </div>
                                
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" {{ $item->is_maintenance ? 'checked' : '' }}
                                        @click.prevent="handleMaintenanceToggle({{ $item->id }}, '{{ addslashes($item->nama) }}', {{ $item->is_maintenance ? 'true' : 'false' }})">
                                    <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-500 transition-all"></div>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-50">
                            <h4 class="text-sm uppercase tracking-[0.15em] text-[#1265A8] font-black">
                                Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}
                            </h4>

                            <div class="flex items-center gap-3">
                                @if(session('role') === 'owner' || filter_var(session('can_edit'), FILTER_VALIDATE_BOOLEAN))
                                <form id="delete-form-{{ $item->id }}" action="{{ route('fasilitas.destroy', $item->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button" 
                                    onclick="confirmDelete('{{ $item->id }}')"
                                    class="p-3 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                                
                                <a href="{{ route('fasilitas.edit', $item->id) }}" class="btn-edit inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:border-[#1265A8] hover:text-[#1265A8] transition-all font-medium text-sm">
                                    <div class="button-content flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </div>
                                    <div class="loading-spinner hidden">
                                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </a>
                                @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-xs font-semibold">View Only</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- MODAL PREVIEW (Akan muncul saat gambar diklik) --}}
            </div>



        <style>
            [x-cloak] { display: none !important; }
        </style>
        {{-- MODAL MAINTENANCE --}}
        <div x-show="maintenanceModal" x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="maintenanceModal = false"></div>
                
                <div class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="scale-90 opacity-0"
                    x-transition:enter-end="scale-100 opacity-100">
                    
                    <div class="bg-red-600 p-8 text-white relative">
                        <div class="absolute top-0 right-0 p-8 opacity-10">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45L19.55 19H4.45L12 5.45zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z"/></svg>
                        </div>
                        <h3 class="text-2xl font-black mb-1">Mode Perbaikan</h3>
                        <p class="text-red-100 text-sm font-medium">Fasilitas: <span x-text="maintData.name" class="font-bold underline text-white"></span></p>
                    </div>

                    <form id="maintForm" @submit.prevent="submitMaintenance" class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest px-1">Mulai Dari</label>
                                <input type="date" x-model="maintData.start_date" name="tgl_mulai" required
                                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest px-1">Sampai Dengan</label>
                                <input type="date" x-model="maintData.end_date" name="tgl_selesai" required
                                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest px-1">Alasan Perbaikan</label>
                            <textarea x-model="maintData.reason" name="tujuan" required rows="3" placeholder="Contoh: Renovasi lantai atau Perbaikan AC..."
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all resize-none"></textarea>
                        </div>

                        <div class="pt-4 flex items-center gap-3">
                            <button type="button" @click="maintenanceModal = false" 
                                class="flex-1 px-6 py-4 bg-slate-100 text-slate-500 rounded-2x border border-slate-200 font-bold text-sm hover:bg-slate-200 transition-all">
                                Batal
                            </button>
                            <button type="submit" 
                                class="flex-[2] px-6 py-4 bg-red-600 text-white rounded-2xl font-black text-sm hover:bg-red-700 hover:shadow-lg hover:shadow-red-600/30 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Blokir Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
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
        function handleLoading(event, element) {
            // Mencegah redirect instan
            event.preventDefault();
            const url = element.getAttribute('href');
            
            const iconPlus = element.querySelector('#iconPlus');
            const iconLoading = element.querySelector('#iconLoading');
            const btnText = element.querySelector('#btnText');

            // Ubah State Tombol
            iconPlus.classList.add('hidden');
            iconLoading.classList.remove('hidden');
            btnText.innerText = 'Memuat...';
            element.classList.add('opacity-90', 'cursor-not-allowed');
            element.style.pointerEvents = 'none'; // Mencegah klik ganda

            setTimeout(() => {
                window.location.href = url;
            }, 600); 
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Fasilitas?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[2rem] p-8'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form berdasarkan ID yang dikirim
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

        document.getElementById('btnTambah').addEventListener('click', function(e) {
            const btn = this;
            const icon = document.getElementById('iconPlus');
            const spinner = document.getElementById('spinner');
            const text = document.getElementById('btnText');

            // efek ripple
            const ripple = document.createElement('span');
            const rect = btn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.classList.add('ripple');
            
            btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);

            // efek loading
            e.preventDefault(); 
            const targetUrl = btn.getAttribute('href');

            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.innerText = 'Memuat...';
            btn.classList.add('opacity-80', 'cursor-wait');

            setTimeout(() => {
                window.location.href = targetUrl;
            }, 500); 
        });

        // Ambil semua elemen dengan class btn-edit
        const editButtons = document.querySelectorAll('.btn-edit');

        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Stop pindah halaman instan
                
                const targetUrl = this.getAttribute('href');
                const content = this.querySelector('.button-content');
                const spinner = this.querySelector('.loading-spinner');

                // Tampilkan loading
                content.classList.add('hidden');
                spinner.classList.remove('hidden');
                this.classList.add('opacity-70', 'cursor-wait');

                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 600);
            });
        });

        // Logika Back to Top
        const backToTopBtn = document.getElementById('backToTop');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                // Tampilkan tombol saat scroll lebih dari 400px
                backToTopBtn.classList.remove('translate-y-20', 'opacity-0');
                backToTopBtn.classList.add('translate-y-0', 'opacity-100');
            } else {
                // Sembunyikan tombol saat di atas
                backToTopBtn.classList.add('translate-y-20', 'opacity-0');
                backToTopBtn.classList.remove('translate-y-0', 'opacity-100');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>