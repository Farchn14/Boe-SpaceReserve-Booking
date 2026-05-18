<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Add Fasilitas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Alpine.js CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shining {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .swal2-shown { padding-right: 0 !important; }
    </style>
</head>
<body class="bg-[#F8FAFC] font-sans antialiased text-slate-800">
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 blur-[120px] rounded-full opacity-50"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-indigo-100 blur-[120px] rounded-full opacity-50"></div>
    </div>

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center" x-data="{ 
        tipe: 'asrama',
        labels: {
            asrama: ['Shower', 'AC', 'Wifi', 'Parkir', 'TV', 'Lemari'],
            aula: ['Wifi', 'Sound System', 'AC', 'Kursi', 'Meja', 'Panggung', 'Proyektor']
        },
        selectedLabels: [],
        customLabel: '',
        galleryPreviews: [null, null, null],
        addCustomLabel() {
            if (this.customLabel.trim() !== '') {
                const label = this.customLabel.trim();
                if (!this.labels[this.tipe].includes(label)) {
                    this.labels[this.tipe].push(label);
                }
                if (!this.selectedLabels.includes(label)) {
                    this.selectedLabels.push(label);
                }
                this.customLabel = '';
            }
        }
    }">
        <div class="w-full max-w-5xl bg-white/80 backdrop-blur-xl rounded-[3rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.08)] border border-white overflow-hidden transition-all duration-500 hover:shadow-blue-200/40">
            
            <div class="pt-10 pb-6 px-10 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 bg-blue-50/50 rounded-full border border-blue-100 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1d6fa5]"></span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1d6fa5]" x-text="'Management Portal | ' + tipe">Management Portal</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight uppercase">
                    Add <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1d6fa5] to-blue-400" x-text="tipe === 'asrama' ? 'Asrama' : 'Aula'">Venue</span> Data
                </h2>
                <div class="h-1 w-12 bg-gradient-to-r from-[#1d6fa5] to-blue-400 mx-auto mt-4 rounded-full"></div>

                {{-- Type Switcher --}}
                <div class="flex justify-center gap-4 mt-8">
                    <button type="button" @click="tipe = 'asrama'; selectedLabels = []" :class="tipe === 'asrama' ? 'bg-[#1d6fa5] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Asrama</button>
                    <button type="button" @click="tipe = 'aula'; selectedLabels = []" :class="tipe === 'aula' ? 'bg-[#1d6fa5] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Aula</button>
                    <input type="hidden" name="tipe" :value="tipe">
                </div>
            </div>

            <form action="/admin/fasilitas/store" method="POST" enctype="multipart/form-data" class="p-8 lg:p-12 pt-6">
                @csrf
                <input type="hidden" name="tipe" :value="tipe">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    
                    <div class="space-y-6">
                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Fasilitas</label>
                            <input type="text" name="nama" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold" required>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Singkat</label>
                            <textarea name="deskripsi" rows="3" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm resize-none font-medium leading-relaxed" required></textarea>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Detail Fasilitas</label>
                            <textarea name="detail" rows="5" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm resize-none font-medium leading-relaxed"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jam Operasional</label>
                                <input type="text" name="jam_operasional" placeholder="08.00 - 22.00" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold">
                            </div>
                            <div x-show="tipe === 'asrama'">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Max Durasi (Hari)</label>
                                <input type="number" name="max_durasi_harian" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold">
                            </div>
                            <div x-show="tipe === 'aula'">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kapasitas (Orang)</label>
                                <input type="number" name="max_dewasa" placeholder="Total" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold">
                            </div>
                        </div>

                        <div x-show="tipe === 'asrama'" class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kapasitas Dewasa (Kamar)</label>
                                <input type="number" name="max_dewasa" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kapasitas Anak (Kamar)</label>
                                <input type="number" name="max_anak" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold">
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Labels / Fitur</label>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="label in labels[tipe]" :key="label">
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="labels[]" :value="label" x-model="selectedLabels" class="hidden">
                                        <span :class="selectedLabels.includes(label) ? 'bg-[#1d6fa5] text-white border-[#1d6fa5]' : 'bg-white text-slate-400 border-slate-200'" class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all duration-300 block" x-text="label"></span>
                                    </label>
                                </template>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" x-model="customLabel" @keydown.enter.prevent="addCustomLabel()" placeholder="Tambah fitur custom..." class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold outline-none focus:border-[#1d6fa5] transition-all">
                                <button type="button" @click="addCustomLabel()" class="px-4 py-2 bg-[#1d6fa5] text-white rounded-xl hover:bg-slate-800 transition-all font-black text-sm">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Biaya Harian</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] transition-all">Rp</span>
                                    <input type="text" id="hargaDisplay" class="w-full pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold transition-all" required>
                                    <input type="hidden" name="harga" id="hargaReal">
                                </div>
                            </div>
                            <div x-show="tipe === 'asrama'">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Biaya Bulanan</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5]">Rp</span>
                                    <input type="number" name="harga_bulanan" class="w-full pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            {{-- Thumbnail Upload --}}
                            <div class="w-full">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Thumbnail Cards</label>
                                <div id="dropzone" class="relative overflow-hidden rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1d6fa5] transition-all duration-500 h-48 flex items-center justify-center group/drop cursor-pointer">
                                    <img id="preview" class="absolute inset-0 w-full h-full object-cover hidden z-10" src="">
                                    <div id="ui-content" class="relative z-20 flex flex-col items-center">
                                        <div class="p-4 bg-white/90 backdrop-blur rounded-2xl shadow-lg mb-2 transform group-hover/drop:scale-110 transition-all duration-500">
                                            <svg class="w-6 h-6 text-[#1d6fa5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <p class="text-[10px] font-black uppercase tracking-[0.1em] text-slate-500">Pilih Foto Utama</p>
                                    </div>
                                    <input type="file" id="fileInput" name="image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-30" required>
                                </div>
                            </div>
                            
                            {{-- Preview Gallery (3 slots) --}}
                            <div class="w-full">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Preview Gallery (3 Foto)</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <template x-for="i in [0, 1, 2]" :key="i">
                                        <div class="relative overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1d6fa5] transition-all duration-500 h-32 flex items-center justify-center group/gal cursor-pointer">
                                            <img :src="galleryPreviews[i]" class="absolute inset-0 w-full h-full object-cover z-10" x-show="galleryPreviews[i]">
                                            <div class="relative z-20 flex flex-col items-center" x-show="!galleryPreviews[i]">
                                                <svg class="w-5 h-5 text-slate-300 group-hover/gal:text-[#1d6fa5] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            </div>
                                            <input :id="'galleryInput' + i" :name="'gallery[' + i + ']'" type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-35" 
                                                @change="
                                                    if (window.validateGalleryFile($event.target, i)) {
                                                        const file = $event.target.files[0];
                                                        if (file) {
                                                            const reader = new FileReader();
                                                            reader.onload = (e) => galleryPreviews[i] = e.target.result;
                                                            reader.readAsDataURL(file);
                                                        }
                                                    }
                                                ">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row-reverse gap-4 pt-6 mt-4 border-t border-slate-100/50">
                            <button type="submit" id="btnSimpan"
                                class="group relative w-full sm:w-2/3 overflow-hidden rounded-2xl bg-[#1d6fa5] px-8 py-5 transition-all duration-300 hover:bg-slate-800 hover:shadow-[0_20px_40px_-12px_rgba(29,111,165,0.35)] active:scale-[0.98]">
                                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover:translate-x-full"></div>
                                <div class="relative flex items-center justify-center gap-3">
                                    <svg id="spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span id="btnText" class="text-sm font-black uppercase tracking-[0.2em] text-white">Simpan Data</span>
                                    <svg id="btnIcon" class="h-5 w-5 text-blue-400 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </div>
                            </button>

                            <div class="w-full sm:w-1/3 relative">
                                <a href="/admin/dashboard/dataFasilitas" id="btn-batal-venue"
                                    class="group w-full flex items-center justify-center gap-2 py-5 px-8 rounded-2xl border-2 border-slate-100 bg-white transition-all duration-300 hover:border-red-100 hover:bg-red-50 active:scale-[0.98] relative overflow-hidden decoration-none cursor-pointer">
                                    <div id="loader-batal-venue" class="absolute inset-0 flex items-center justify-center bg-red-50 opacity-0 invisible transition-all duration-300"><svg class="animate-spin h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>
                                    <div id="text-batal-venue" class="flex items-center gap-2 transition-all duration-300"><span class="text-xs font-black uppercase tracking-widest text-slate-400 group-hover:text-red-500">Batal</span></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="loadingOverlay" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-50/60 backdrop-blur-sm transition-all duration-500">
        <div class="flex flex-col items-center">
            <div class="relative w-16 h-16 mb-8">
                <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-[#1d6fa5] border-t-transparent rounded-full animate-spin"></div>
            </div>
            <div class="text-center">
                <h3 id="loadingStatus" class="text-lg font-medium text-slate-800 tracking-tight transition-all duration-300">Memproses data</h3>
                <div class="flex justify-center gap-1 mt-2">
                    <span class="w-1.5 h-1.5 bg-[#1d6fa5] rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                    <span class="w-1.5 h-1.5 bg-[#1d6fa5] rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                    <span class="w-1.5 h-1.5 bg-[#1d6fa5] rounded-full animate-bounce"></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnSimpan = document.getElementById('btnSimpan');
            const fileInput = document.getElementById('fileInput');
            const preview = document.getElementById('preview');
            const uiContent = document.getElementById('ui-content');
            const dropzone = document.getElementById('dropzone');

            // LIMITS (Match PHP configuration)
            const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
            const MAX_POST_SIZE = 8 * 1024 * 1024; // 8MB

            function formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function checkTotalSize() {
                let total = 0;
                // Check main image
                if (fileInput.files[0]) total += fileInput.files[0].size;
                // Check gallery images
                for (let i = 0; i < 3; i++) {
                    const galInput = document.getElementById('galleryInput' + i);
                    if (galInput && galInput.files[0]) total += galInput.files[0].size;
                }
                return total;
            }

            // 1. Preview Gambar Utama
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > MAX_FILE_SIZE) {
                        Swal.fire({
                            title: 'File Terlalu Besar',
                            text: `Ukuran file (${formatBytes(file.size)}) melebihi batas 2MB.`,
                            icon: 'warning',
                            confirmButtonColor: '#1d6fa5'
                        });
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        uiContent.classList.add('opacity-0'); 
                        dropzone.classList.remove('border-dashed');
                        dropzone.classList.add('border-solid', 'border-[#1d6fa5]');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // 2. Currency Masking
            const hargaDisplay = document.getElementById('hargaDisplay');
            const hargaReal = document.getElementById('hargaReal');

            if (hargaDisplay) {
                hargaDisplay.addEventListener('input', function(e) {
                    let value = this.value.replace(/\D/g, "");
                    hargaReal.value = value;
                    if (value === "") {
                        this.value = "";
                        return;
                    }
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                });
            }

            // logika simpan data
            btnSimpan.closest('form').addEventListener('submit', function (e) {
                e.preventDefault(); 

                // Check total size before asking
                const totalSize = checkTotalSize();
                if (totalSize > MAX_POST_SIZE) {
                    Swal.fire({
                        title: 'Total Data Terlalu Besar',
                        text: `Total ukuran gambar (${formatBytes(totalSize)}) melebihi batas server (8MB). Harap perkecil ukuran foto atau unggah satu per satu.`,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Simpan Data?',
                    text: "Pastikan semua informasi venue sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1d6fa5',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-[2.5rem]',
                        confirmButton: 'rounded-2xl px-8 py-3 font-bold',
                        cancelButton: 'rounded-2xl px-8 py-3 font-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        eksekusiSimpanData();
                    }
                });
            });

            // memproses loading dan sukses
            function eksekusiSimpanData() {
                const form = document.querySelector('form');
                const formData = new FormData(form); // Mengambil semua input & file gambar
                const overlay = document.getElementById('loadingOverlay');
                const statusText = document.getElementById('loadingStatus');
                
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');

                // KIRIM DATA KE BACKEND (Laravel)
                fetch('/admin/fasilitas/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    // Cek tipe konten respon
                    const contentType = response.headers.get("content-type");
                    let data = null;

                    if (contentType && contentType.includes("application/json")) {
                        data = await response.json();
                    }

                    if (!response.ok) {
                        // Jika ada data JSON dari backend (seperti error validasi)
                        if (data && data.message) {
                            throw new Error(data.message);
                        }
                        // Default error if not JSON or no message
                        throw new Error(`Server error: ${response.status}`);
                    }
                    
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('loadingOverlay').classList.add('hidden');
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#1265A8',
                            customClass: { popup: 'rounded-[2.5rem] p-8' }
                        }).then(() => {
                            window.location.href = "/admin/dashboard/dataFasilitas";
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingOverlay').classList.add('hidden');
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'Terjadi kesalahan sistem.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        customClass: { popup: 'rounded-[1.5rem] p-8' }
                    });
                    
                    // Reset Button State
                    const btnText = document.getElementById('btnText');
                    const btnIcon = document.getElementById('btnIcon');
                    const btnLoader = document.getElementById('spinner');
                    btnText.innerText = 'Simpan Data';
                    btnText.classList.remove('opacity-0');
                    btnIcon.classList.remove('hidden');
                    btnLoader.classList.add('hidden');
                });
            }

            // Global access for gallery inputs validation
            window.validateGalleryFile = function(input, index) {
                const file = input.files[0];
                if (file && file.size > MAX_FILE_SIZE) {
                    Swal.fire({
                        title: 'File Terlalu Besar',
                        text: `Gambar galeri ${index + 1} (${formatBytes(file.size)}) melebihi batas 2MB.`,
                        icon: 'warning',
                        confirmButtonColor: '#1d6fa5'
                    });
                    input.value = '';
                    return false;
                }
                return true;
            };
        });

        const btnBatalVenue = document.getElementById('btn-batal-venue');

        if (btnBatalVenue) {
            btnBatalVenue.addEventListener('click', function(e) {
                e.preventDefault();
                const previousPage = document.referrer || '/admin/dashboard';
                const loader = document.getElementById('loader-batal-venue');
                const textContent = document.getElementById('text-batal-venue');

                Swal.fire({
                    title: 'Batalkan Pengisian?',
                    text: "Data yang sudah diisi tidak akan tersimpan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', 
                    cancelButtonColor: '#94a3b8', 
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Kembali',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-[2rem]',
                        confirmButton: 'rounded-xl',
                        cancelButton: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        textContent.classList.add('opacity-0', 'scale-95');
                        loader.classList.remove('invisible', 'opacity-0');
                        this.classList.add('pointer-events-none');

                        setTimeout(() => {
                            window.location.href = previousPage;
                        }, 700);
                    }
                });
            });
        }
    </script>
</body>
</html>