<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Form Reservasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        .step-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        /* ── Status Colors (Mirrored from Admin) ── */
        .status-ready       { background-color: #d1fae5; color: #065f46; }
        .status-pending     { background-color: #fef9c3; color: #854d0e; }
        .status-booked      { background-color: #dbeafe; color: #1e40af; }
        .status-blocked     { background-color: #1e293b; color: #f1f5f9; }
        .status-maintenance { background-color: #fee2e2; color: #991b1b; }
        .status-past        { background-color: #f1f5f9; color: #94a3b8; }
        .status-closed      { background-color: #e2e8f0; color: #94a3b8; opacity: 0.5; cursor: not-allowed; }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }
        .animate-shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen font-['Poppins']">

<main class="flex flex-col items-center justify-start pt-32 pb-20 px-4" 
    x-cloak
    x-data="bookingForm({
        facilities: {{ $facilities->toJson() }},
        selectedFacilityId: '{{ $selectedId ?? '' }}'
    })">

    <div class="w-full max-w-2xl bg-white/80 backdrop-blur-xl p-8 md:p-12 rounded-[3.5rem] shadow-2xl border border-white/60 relative overflow-hidden">
        
        {{-- Progress Bar --}}
        <div class="absolute top-0 left-0 w-full h-2 bg-gray-100">
            <div class="h-full bg-blue-600 transition-all duration-700" :style="'width: ' + (step * 25) + '%'"></div>
        </div>

        {{-- Step 1: Initial Choice --}}
        <div x-show="step === 1" x-transition class="step-transition">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 1/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Pilih Tipe Pilihan</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Tentukan durasi pemesanan Anda di BOE Malang.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <button @click="packageType = 'harian'; nextStep()" 
                    class="group relative p-8 bg-white border-2 border-gray-100 rounded-[2.5rem] hover:border-blue-600 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-8 h-8 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">Booking-Harian</h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">Cocok untuk kebutuhan jangka pendek atau harian.</p>
                </button>

                <button @click="packageType = 'bulanan'; nextStep()" 
                    class="group relative p-8 bg-white border-2 border-gray-100 rounded-[2.5rem] hover:border-blue-600 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">Booking-Bulanan</h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">Lebih hemat untuk kebutuhan jangka panjang (Bulanan).</p>
                </button>
            </div>
            
            <div class="mt-12 flex justify-center">
                <button @click="confirmCancel()" class="text-gray-400 hover:text-red-500 font-bold uppercase tracking-widest text-xs transition-colors">Batal Booking</button>
            </div>
        </div>

        {{-- Step 2: Configuration --}}
        <div x-show="step === 2" x-transition class="step-transition">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 2/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Konfigurasi Paket</h2>
                <p class="text-sm text-gray-400 font-medium mt-2" x-text="'Tipe: ' + packageType.toUpperCase()"></p>
            </div>

            <div class="space-y-6">
                {{-- Duration --}}
                <div class="flex items-center justify-between p-6 bg-gray-50 rounded-3xl border border-gray-100">
                    <div>
                        <h4 class="font-black text-gray-800 uppercase tracking-tighter">Durasi Booking</h4>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest" x-text="packageType === 'harian' ? 'Satuan: Hari' : 'Satuan: Bulan'"></p>
                    </div>
                    <div class="flex items-center gap-6">
                        <button @click="dec('duration', 1)" class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all">-</button>
                        <span class="text-2xl font-black text-gray-800" x-text="duration"></span>
                        <button @click="inc('duration')" class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all">+</button>
                    </div>
                </div>

                {{-- Guests --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100">
                        <h4 class="font-black text-gray-800 uppercase tracking-tighter mb-4" x-text="currentFacility?.tipe === 'aula' ? 'Total Kapasitas' : 'Dewasa'">Dewasa</h4>
                        <div class="flex items-center justify-between">
                            <button @click="dec('adults', 1)" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm">-</button>
                            <span class="text-xl font-black text-gray-800" x-text="adults"></span>
                            <button @click="inc('adults')" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm">+</button>
                        </div>
                    </div>
                    {{-- Hide children for Aula --}}
                    <div x-show="currentFacility?.tipe === 'asrama'" class="p-6 bg-gray-50 rounded-3xl border border-gray-100">
                        <h4 class="font-black text-gray-800 uppercase tracking-tighter mb-4">Anak</h4>
                        <div class="flex items-center justify-between">
                            <button @click="dec('children', 0)" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm">-</button>
                            <span class="text-xl font-black text-gray-800" x-text="children"></span>
                            <button @click="inc('children')" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm">+</button>
                        </div>
                    </div>
                </div>

                {{-- Child Ages --}}
                <div x-show="currentFacility?.tipe === 'asrama' && children > 0" x-transition class="p-6 bg-blue-50/20 rounded-3xl border border-blue-100">
                    <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-4">Umur Anak (Tahun)</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <template x-for="(age, idx) in childAges" :key="idx">
                            <input type="number" x-model="childAges[idx]" placeholder="0" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-center font-bold text-sm outline-none focus:border-blue-400">
                        </template>
                    </div>
                </div>

                {{-- Rooms --}}
                <div x-show="currentFacility?.tipe === 'asrama'" class="p-6 bg-gray-50 rounded-3xl border border-gray-100 flex items-center justify-between">
                    <div>
                        <h4 class="font-black text-gray-800 uppercase tracking-tighter">Jumlah Kamar</h4>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest italic">* 1 Kamar Max 1 Dewasa</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <button @click="dec('rooms', 1)" class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600">-</button>
                        <span class="text-2xl font-black text-gray-800" x-text="rooms"></span>
                        <button @click="inc('rooms')" class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 transition-all"
                            :class="rooms >= adults ? 'opacity-30 cursor-not-allowed' : 'hover:bg-blue-600 hover:text-white'">+</button>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex justify-between gap-4">
                <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                <button @click="nextStep()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200">Lanjut ke Kalender</button>
            </div>
        </div>

        {{-- Step 3: Calendar Selection --}}
        <div x-show="step === 3" x-transition class="step-transition">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 3/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Pilih Tanggal</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Kalender Ketersediaan Unit</p>
            </div>

            <div class="bg-white rounded-[2.5rem] overflow-hidden border-2 border-black/10 shadow-xl relative">
                {{-- Loading Overlay --}}
                <div x-show="isLoadingCalendar" class="absolute inset-0 z-50 bg-white/60 backdrop-blur-sm flex flex-col items-center justify-center gap-4 transition-opacity">
                    <div class="w-10 h-10 border-4 border-slate-100 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Sinkronisasi Jadwal...</p>
                </div>

                {{-- Header --}}
                <div class="p-6 md:p-8 flex items-center justify-between bg-white border-b border-gray-100">
                    <div>
                        <h3 class="text-xl md:text-2xl font-black uppercase tracking-tighter text-gray-900" x-text="monthName"></h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]" x-text="currentYear"></p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="prevMonth()" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-all text-gray-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button @click="nextMonth()" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-all text-gray-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Status Legend --}}
                <div class="px-6 md:px-8 py-3 flex flex-wrap gap-x-4 gap-y-2 bg-gray-50/50 border-b border-gray-100">
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-ready"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Ready</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-pending"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Pending</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-booked"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Booked</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-blocked"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Blocked</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-maintenance"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Repair</span></div>
                </div>

                <div class="grid grid-cols-7 gap-px bg-gray-100">
                    <template x-for="d in ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB']">
                        <div class="bg-gray-50 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest" x-text="d"></div>
                    </template>
                    <template x-for="(item, idx) in daysInMonth" :key="idx">
                        <div class="h-16 sm:h-20 md:h-24 relative group transition-all flex items-center justify-center cursor-pointer"
                            :class="item.day ? 'status-' + getDateStatus(item.date) : 'bg-white'"
                            @click="selectDate(item.date)">
                            
                            {{-- Date Number --}}
                            <div x-show="item.day" class="relative z-10 text-sm md:text-base font-black transition-all duration-300"
                                :class="{
                                    'ring-4 ring-black/10 rounded-full w-8 h-8 md:w-10 md:h-10 flex items-center justify-center bg-gray-900 text-white shadow-lg scale-110': selectedDate && item.date && item.date.getTime() === selectedDate.getTime()
                                }"
                                x-text="item.day">
                            </div>

                            {{-- Range Indication Overlay --}}
                            <div x-show="item.day && isInRange(item.date)" 
                                x-transition
                                class="absolute inset-0 bg-blue-600/10 border-2 border-blue-600/30 z-0">
                            </div>

                            {{-- Tooltip info --}}
                            <template x-if="item.day && getDateStatus(item.date) !== 'ready' && getDateStatus(item.date) !== 'closed'">
                                <div class="absolute bottom-1 left-0 right-0 text-center opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                    <span class="bg-black/80 text-white text-[7px] font-black uppercase px-2 py-0.5 rounded shadow-lg whitespace-nowrap" x-text="getDayInfo(item.date)"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-12 flex justify-between gap-4">
                <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                <button x-show="selectedDate" @click="nextStep()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200">Konfirmasi Data Diri</button>
            </div>
        </div>

        {{-- Step 4: Personal Data & Confirmation --}}
        <div x-show="step === 4" x-transition class="step-transition">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah Akhir</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Konfirmasi Data</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Detail Pemohon</p>
            </div>

            <div class="space-y-6">
                <div x-data="{ 
                    name: '',
                    provinsi: '',      // Menyimpan ID Provinsi
                    provinsiName: '',  // Menyimpan Nama Provinsi
                    kabupaten: '',     // Menyimpan ID Kabupaten
                    kabupatenName: '', // Menyimpan Nama Kabupaten
                    whatsapp: '',
                    email: '',
                    fotoPreview: null,
                    
                    // State Input Pencarian
                    searchProvinsi: '',
                    searchKabupaten: '',
                    
                    // Data List dari API
                    provinces: [],
                    regencies: [],
                    loadingProvinsi: true,  
                    loadingKabupaten: false, 
                    
                    // State Buka/Tutup Dropdown (Penyelesaian masalah Scope)
                    openProvinsi: false,
                    openKabupaten: false,
                    
                    // State Tracker Error & Getar
                    errors: { name: false, provinsi: false, kabupaten: false, whatsapp: false, email: false, foto: false },
                    shake: { name: false, provinsi: false, kabupaten: false, whatsapp: false, email: false, foto: false },
                    fotoErrorMsg: '',

                    // Ambil Data Provinsi saat Halaman Pertama Kali Dimuat
                    async init() {
                        this.loadingProvinsi = true;
                        try {
                            let response = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                            this.provinces = await response.json();
                        } catch (error) {
                            console.error('Gagal mengambil data provinsi:', error);
                        } finally {
                            this.loadingProvinsi = false;
                        }
                    },

                    // Ambil Data Kabupaten Berdasarkan ID Provinsi yang Dipilih
                    async fetchKabupaten(provinsiId) {
                        this.loadingKabupaten = true;
                        this.regencies = []; 
                        this.kabupaten = '';
                        this.kabupatenName = '';
                        this.searchKabupaten = ''; // Reset keyword pencarian kota lama
                        
                        try {
                            // PERBAIKAN: Menggunakan endpoint www.emsifa.com agar konsisten dan data berhasil ditarik
                            let response = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinsiId}.json`);
                            if (!response.ok) throw new Error('Gagal memuat data kabupaten dari server');
                            this.regencies = await response.json();
                        } catch (error) {
                            console.error('Gagal mengambil data kabupaten:', error);
                            this.regencies = [];
                        } finally {
                            this.loadingKabupaten = false;
                        }
                    },

                    // Filter Otomatis untuk Provinsi (Mendukung huruf kecil/kapital saat mengetik)
                    get filteredProvinces() {
                        if (!this.searchProvinsi.trim()) return this.provinces;
                        return this.provinces.filter(p => 
                            p.name.toLowerCase().includes(this.searchProvinsi.toLowerCase().trim())
                        );
                    },

                    // Filter Otomatis untuk Kabupaten (Mendukung huruf kecil/kapital saat mengetik)
                    get filteredRegencies() {
                        if (!this.searchKabupaten.trim()) return this.regencies;
                        return this.regencies.filter(k => 
                            k.name.toLowerCase().includes(this.searchKabupaten.toLowerCase().trim())
                        );
                    },

                    triggerError(field) {
                        this.errors[field] = true;
                        this.shake[field] = true;
                        setTimeout(() => { this.shake[field] = false; }, 400);
                    },

                    validateField(field) {
                        if (field === 'name') {
                            let alphaRegex = /^[a-zA-Z\s]+$/;
                            this.errors.name = (this.name.trim().length > 0) && (this.name.trim().length < 3 || !alphaRegex.test(this.name));
                            if (this.errors.name) this.triggerError('name');
                        }
                        if (field === 'whatsapp') {
                            let numericRegex = /^[0-9]+$/;
                            this.errors.whatsapp = (this.whatsapp.length > 0 && (!numericRegex.test(this.whatsapp) || this.whatsapp.length < 9 || this.whatsapp.length > 14));
                            if (this.errors.whatsapp) this.triggerError('whatsapp');
                        }
                        if (field === 'email') {
                            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            this.errors.email = (this.email.length > 0 && !emailRegex.test(this.email));
                            if (this.errors.email) this.triggerError('email');
                        }
                    },

                    handleFileChange(e) {
                        let file = e.target.files[0];
                        if (!file) return;

                        let allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!allowedTypes.includes(file.type)) {
                            this.fotoErrorMsg = 'Format file harus JPG, JPEG, atau PNG!';
                            this.triggerError('foto');
                            this.fotoPreview = null;
                            e.target.value = '';
                            return;
                        }

                        if (file.size > 2 * 1024 * 1024) {
                            this.fotoErrorMsg = 'Ukuran file terlalu besar! Maksimal 2MB.';
                            this.triggerError('foto');
                            this.fotoPreview = null;
                            e.target.value = '';
                            return;
                        }

                        this.errors.foto = false;
                        let reader = new FileReader();
                        reader.onload = (event) => { this.fotoPreview = event.target.result; };
                        reader.readAsDataURL(file);
                    }
                }" class="space-y-4">

                    <div class="relative z-0" :class="{ 'animate-shake': shake.name }">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-4 mb-2 block transition-colors" :class="errors.name ? 'text-red-500' : 'text-gray-400'">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="name" @input="validateField('name')" placeholder="Masukan nama lengkap Anda" 
                            class="w-full px-6 py-4 bg-gray-50 border rounded-3xl outline-none font-medium text-sm transition-all"
                            :class="errors.name ? 'border-red-500 bg-red-50/30 focus:border-red-600' : 'border-gray-100 focus:border-blue-600'">
                        <span x-show="errors.name" x-transition class="text-[10px] text-red-500 font-bold ml-4 mt-1 block">Nama minimal 3 karakter & hanya boleh huruf abjad!</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative" style="z-index: 50;">
                        
                        <div class="relative" :class="{ 'animate-shake': shake.provinsi }" style="z-index: 40;">
                            <label class="text-[9px] font-black uppercase tracking-widest ml-4 mb-2 block transition-colors" :class="errors.provinsi ? 'text-red-500' : 'text-gray-400'">
                                Provinsi Asal <span class="text-red-500">*</span>
                            </label>
                            <div @click.stop="openProvinsi = !openProvinsi; openKabupaten = false" @click.away="openProvinsi = false" 
                                class="w-full px-6 py-4 bg-gray-50 border rounded-3xl outline-none flex justify-between items-center cursor-pointer font-medium text-sm transition-colors"
                                :class="errors.provinsi ? 'border-red-500 bg-red-50/30' : 'border-gray-100 focus-within:border-blue-600'">
                                <span x-text="provinsiName ? provinsiName : 'Pilih Provinsi...'" :class="provinsiName ? 'text-gray-900' : 'text-gray-400'"></span>
                                <svg class="w-4 h-4 transition-transform" :class="openProvinsi ? 'text-blue-600 rotate-180' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            
                            <div x-show="openProvinsi" x-transition.opacity @click.stop class="absolute left-0 w-full mt-2 bg-white border border-gray-100 shadow-2xl rounded-2xl overflow-hidden py-1" style="z-index: 100;">
                                <div class="p-3 border-b border-gray-50">
                                    <input x-model="searchProvinsi" type="text" placeholder="Cari Provinsi..." class="w-full bg-gray-50 text-xs px-4 py-3 rounded-xl outline-none border border-gray-100 focus:border-blue-400">
                                </div>
                                <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                    <div x-show="loadingProvinsi" class="px-5 py-3 text-xs font-bold text-gray-400 text-center">Memuat provinsi...</div>
                                    <template x-for="p in filteredProvinces" :key="p.id">
                                        <div @click="provinsi = p.id; provinsiName = p.name; errors.provinsi = false; openProvinsi = false; searchProvinsi = ''; fetchKabupaten(p.id)" 
                                            class="px-5 py-3 hover:bg-blue-50 cursor-pointer text-xs font-bold text-gray-700 transition text-left" x-text="p.name"></div>
                                    </template>
                                    <div x-show="!loadingProvinsi && filteredProvinces.length === 0" class="px-5 py-3 text-xs font-bold text-gray-400 text-center">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>

                        <div class="relative" :class="{ 'animate-shake': shake.kabupaten }" style="z-index: 30;">
                            <label class="text-[9px] font-black uppercase tracking-widest ml-4 mb-2 block transition-colors" :class="errors.kabupaten ? 'text-red-500' : 'text-gray-400'">
                                Kabupaten / Kota <span class="text-red-500">*</span>
                            </label>
                            <div @click.stop="if(!provinsi) { triggerError('provinsi') } else { openKabupaten = !openKabupaten; openProvinsi = false }" @click.away="openKabupaten = false" 
                                class="w-full px-6 py-4 bg-gray-50 border rounded-3xl outline-none flex justify-between items-center cursor-pointer font-medium text-sm transition-colors" 
                                :class="!provinsi ? 'opacity-50 cursor-not-allowed border-gray-100' : errors.kabupaten ? 'border-red-500 bg-red-50/30' : 'border-gray-100 focus-within:border-blue-600'">
                                
                                <span x-text="loadingKabupaten ? 'Memuat data kota...' : kabupatenName ? kabupatenName : 'Pilih Kota/Kabupaten...'" 
                                    :class="kabupatenName ? 'text-gray-900' : 'text-gray-400'"></span>
                                
                                <svg class="w-4 h-4 transition-transform" :class="openKabupaten ? 'text-blue-600 rotate-180' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            
                            <div x-show="openKabupaten && provinsi" x-transition.opacity @click.stop class="absolute left-0 w-full mt-2 bg-white border border-gray-100 shadow-2xl rounded-2xl overflow-hidden py-1" style="z-index: 100;">
                                <div class="p-3 border-b border-gray-50">
                                    <input x-model="searchKabupaten" type="text" placeholder="Cari Kota..." class="w-full bg-gray-50 text-xs px-4 py-3 rounded-xl outline-none border border-gray-100 focus:border-blue-400">
                                </div>
                                <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                    <div x-show="loadingKabupaten" class="px-5 py-3 text-xs font-bold text-gray-400 text-center">Memuat data kabupaten...</div>
                                    <template x-for="k in filteredRegencies" :key="k.id">
                                        <div @click="kabupaten = k.id; kabupatenName = k.name; errors.kabupaten = false; openKabupaten = false; searchKabupaten = ''" 
                                            class="px-5 py-3 hover:bg-blue-50 cursor-pointer text-xs font-bold text-gray-700 transition text-left" x-text="k.name"></div>
                                    </template>
                                    <div x-show="!loadingKabupaten && filteredRegencies.length === 0" class="px-5 py-3 text-xs font-bold text-gray-400 text-center">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-0">
                        <div class="relative" :class="{ 'animate-shake': shake.whatsapp }">
                            <label class="text-[9px] font-black uppercase tracking-widest ml-4 mb-2 block transition-colors" :class="errors.whatsapp ? 'text-red-500' : 'text-gray-400'">
                                Nomor HP / WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="whatsapp" @input="validateField('whatsapp')" placeholder="08xxxxx" 
                                class="w-full px-6 py-4 bg-gray-50 border rounded-3xl outline-none font-medium text-sm transition-all"
                                :class="errors.whatsapp ? 'border-red-500 bg-red-50/30 focus:border-red-600' : 'border-gray-100 focus:border-blue-600'">
                            <span x-show="errors.whatsapp" x-transition class="text-[10px] text-red-500 font-bold ml-4 mt-1 block">Gunakan nomor HP valid (9-14 digit angka)</span>
                        </div>

                        <div class="relative" :class="{ 'animate-shake': shake.email }">
                            <label class="text-[9px] font-black uppercase tracking-widest ml-4 mb-2 block transition-colors" :class="errors.email ? 'text-red-500' : 'text-gray-400'">
                                Email Aktif <span class="text-red-500">*</span>
                            </label>
                            <input type="email" x-model="email" @input="validateField('email')" placeholder="example@mail.com" 
                                class="w-full px-6 py-4 bg-gray-50 border rounded-3xl outline-none font-medium text-sm transition-all"
                                :class="errors.email ? 'border-red-500 bg-red-50/30 focus:border-red-600' : 'border-gray-100 focus:border-blue-600'">
                            <span x-show="errors.email" x-transition class="text-[10px] text-red-500 font-bold ml-4 mt-1 block">Format alamat email tidak sesuai</span>
                        </div>
                    </div>
                    
                    <div class="relative z-0 mt-4 p-6 bg-white border rounded-3xl shadow-sm transition-all" :class="errors.foto ? 'border-red-500 bg-red-50/10 animate-shake' : 'border-gray-100'">
                        <label class="text-[9px] font-black uppercase tracking-widest block mb-4 transition-colors" :class="errors.foto ? 'text-red-500' : 'text-gray-400'">
                            Upload Foto Identitas <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="flex-1">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all"
                                    :class="errors.foto ? 'border-red-400 hover:border-red-500' : 'border-gray-300 hover:border-blue-500'">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 transition-colors" :class="errors.foto ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="mb-1 text-xs font-semibold" :class="errors.foto ? 'text-red-500' : 'text-gray-500'">Klik untuk unggah file</p>
                                        <p class="text-[10px]" :class="errors.foto ? 'text-red-400' : 'text-gray-400'">JPG, JPEG, PNG (Maks. 2MB)</p>
                                    </div>
                                    <input type="file" class="hidden" accept="image/jpeg,image/png,image/jpg" @change="handleFileChange" />
                                </label>
                                <span x-show="errors.foto" x-text="fotoErrorMsg" x-transition class="text-[10px] text-red-500 font-bold mt-2 block"></span>

                                <div class="mt-3 p-3 bg-blue-50 rounded-xl flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-[10px] text-blue-700 font-medium leading-relaxed">
                                        Dokumen identitas Anda hanya digunakan untuk keperluan validasi reservasi dan akan dihapus secara otomatis dari sistem kami setelah masa sewa berakhir demi menjaga privasi dan keamanan data Anda.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-48 flex flex-col items-center justify-center border bg-gray-50 rounded-2xl overflow-hidden relative min-h-[8rem]" :class="errors.foto ? 'border-red-200' : 'border-gray-100'">
                                <template x-if="fotoPreview">
                                    <div class="w-full h-full">
                                        <img :src="fotoPreview" class="object-cover w-full h-full absolute inset-0" alt="Preview Identitas">
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-2">
                                            <p class="text-[9px] text-white font-bold text-center tracking-wider">PREVIEW</p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!fotoPreview">
                                    <div class="text-center p-4">
                                        <svg class="w-8 h-8 mx-auto mb-2" :class="errors.foto ? 'text-red-300' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-[10px] font-bold" :class="errors.foto ? 'text-red-400' : 'text-gray-400'">Belum ada foto</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary Card --}}
                <div class="p-8 bg-gray-900 rounded-[2.5rem] text-white">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-500 mb-6 underline underline-offset-8 decoration-2">Ringkasan Reservasi</h4>
                    <div class="grid grid-cols-2 gap-y-4">
                        <div>
                            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Fasilitas</p>
                            <p class="text-sm font-bold truncate" x-text="currentFacility?.nama"></p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Paket</p>
                            <p class="text-sm font-bold" x-text="packageType.toUpperCase()"></p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Check-In</p>
                            <p class="text-sm font-bold" x-text="selectedDate ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(selectedDate) : '-'"></p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Durasi</p>
                            <p class="text-sm font-bold" x-text="duration + (packageType === 'harian' ? ' Hari' : ' Bulan')"></p>
                        </div>
                        <div class="col-span-2 pt-6 mt-2 border-t border-white/10 flex justify-between items-end">
                            <div>
                                <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Total Estimasi</p>
                                <p class="text-2xl font-black text-blue-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice)"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Kamar & Tamu</p>
                                <p class="text-[10px] font-bold text-gray-300" x-text="rooms + ' Kamar, ' + adults + ' Dewasa'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 space-y-4">
                <div class="flex justify-between gap-4">
                    <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                    <button @click="submitBooking()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200 hover:bg-black transition-all">Submit Reservasi</button>
                </div>
                <button @click="confirmCancel()" class="w-full py-4 text-red-500 font-bold uppercase tracking-widest text-[10px] bg-red-50 rounded-2xl border border-red-100 transition-colors">Batal Booking</button>
            </div>
        </div>

    </div>

    {{-- Footer Info --}}
    <div class="mt-12 text-center">
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.4em]">© 2026 BBPPMPV BOE MALANG</p>
    </div>
</main>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bookingForm', (config) => ({
            step: 1,
            packageType: '',
            duration: 1,
            adults: 1,
            children: 0,
            childAges: [],
            rooms: 1,
            selectedDate: null,
            name: '',
            email: '',
            whatsapp: '',
            provinsi: '',
            provinsiName: '',
            kabupaten: '',
            kabupatenName: '',
            provinces: [],
            regencies: [],
            fotoIdentitas: null,
            fotoPreview: null,
            facilities: config.facilities || [],
            selectedFacilityId: config.selectedFacilityId || '',

            // Calendar state
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            daysInMonth: [],
            calendarEvents: [],
            isLoadingCalendar: false,

            init() {
                this.updateDaysInMonth();
                this.$watch('children', val => {
                    const count = parseInt(val) || 0;
                    if (count > this.childAges.length) {
                        for (let i = this.childAges.length; i < count; i++) this.childAges.push('');
                    } else {
                        this.childAges = this.childAges.slice(0, count);
                    }
                });
                this.$watch('adults', val => { 
                    if (this.currentFacility?.tipe === 'asrama' && this.rooms > val) this.rooms = val; 
                });

                // Watch for month/year changes to refetch calendar
                this.$watch('currentMonth', () => { this.updateDaysInMonth(); this.fetchCalendarData(); });
                this.$watch('currentYear', () => { this.updateDaysInMonth(); this.fetchCalendarData(); });
                this.$watch('selectedFacilityId', () => { this.fetchCalendarData(); });

                // Fetch Provinces from Emsifa API
                fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                    .then(res => res.json())
                    .then(data => this.provinces = data);
                
                // Watch province to fetch regencies
                this.$watch('provinsi', val => {
                    this.kabupaten = '';
                    this.kabupatenName = '';
                    this.regencies = [];
                    if(val) {
                        const prov = this.provinces.find(p => p.id == val);
                        this.provinsiName = prov ? prov.name : '';
                        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${val}.json`)
                            .then(res => res.json())
                            .then(data => this.regencies = data);
                    }
                });

                // Watch kabupaten to get Name
                this.$watch('kabupaten', val => {
                    if(val) {
                        const kab = this.regencies.find(k => k.id == val);
                        this.kabupatenName = kab ? kab.name : '';
                    } else {
                        this.kabupatenName = '';
                    }
                });
            },

            get currentFacility() {
                return this.facilities.find(f => f.id == this.selectedFacilityId) || null;
            },

            get totalPrice() {
                const f = this.currentFacility;
                if (!f) return 0;
                if (this.packageType === 'harian') {
                    return (parseInt(this.duration) || 0) * (parseFloat(f.harga) || 0);
                } else {
                    if (!f.harga_bulanan) return 0;
                    return (parseInt(this.duration) || 0) * (parseFloat(f.harga_bulanan) || 0);
                }
            },

            handleFileChange(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.fotoIdentitas = null;
                    this.fotoPreview = null;
                    return;
                }
                if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                    Swal.fire('Format Tidak Valid', 'Mohon unggah file format JPG, JPEG, atau PNG.', 'error');
                    event.target.value = '';
                    this.fotoIdentitas = null;
                    this.fotoPreview = null;
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Ukuran Terlalu Besar', 'Maksimal ukuran file foto identitas adalah 2MB.', 'error');
                    event.target.value = '';
                    this.fotoIdentitas = null;
                    this.fotoPreview = null;
                    return;
                }
                this.fotoIdentitas = file;
                this.fotoPreview = URL.createObjectURL(file);
            },

            nextStep() { 
                if (this.step === 2) {
                    if (this.currentFacility?.tipe === 'asrama') {
                        if (this.packageType === 'harian' && this.duration > (this.currentFacility.max_durasi_harian || 999)) {
                            Swal.fire('Peringatan', `Maksimal durasi harian untuk asrama ini adalah ${this.currentFacility.max_durasi_harian} hari.`, 'warning');
                            return;
                        }
                    }
                    this.fetchCalendarData();
                }
                if (this.step < 4) this.step++; 
            },

            prevStep() { if (this.step > 1) this.step--; },

            confirmCancel() {
                Swal.fire({
                    title: 'Batal Booking?',
                    text: 'Semua progres pengisian akan hilang.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tidak'
                }).then(result => { if (result.isConfirmed) window.location.href = '/'; });
            },

            inc(field, max = null) {
                const f = this.currentFacility;
                if (field === 'duration') {
                    if (this.packageType === 'harian') {
                        const limit = f?.max_durasi_harian || 999;
                        if (this.duration >= limit) {
                            Swal.fire({
                                title: 'Peringatan',
                                text: `Maksimal durasi harian untuk ${f?.nama || 'fasilitas ini'} adalah ${limit} hari.`,
                                icon: 'warning',
                                confirmButtonColor: '#276AD7'
                            });
                            return;
                        }
                    }
                }
                if (field === 'rooms') {
                    if (f?.tipe === 'aula') return;
                    if (this.rooms >= this.adults) {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Maksimal 1 orang 1 kamar, mohon tambah jumlah orang/dewasa untuk menambah 1 kamar lagi',
                            icon: 'warning',
                            confirmButtonColor: '#276AD7'
                        });
                        return;
                    }
                }
                if (field === 'adults') {
                    const limit = f?.max_dewasa || 999;
                    if (this.adults >= limit) {
                        Swal.fire('Peringatan', `Maksimal kapasitas dewasa adalah ${limit}`, 'warning');
                        return;
                    }
                }
                if (field === 'children') {
                    const limit = f?.max_anak || 0;
                    if (this.children >= limit) {
                        Swal.fire('Peringatan', `Maksimal kapasitas anak adalah ${limit}`, 'warning');
                        return;
                    }
                }
                if (max !== null && this[field] >= max) return;
                this[field]++;
            },

            dec(field, min = 0) { if (this[field] > min) this[field]--; },

            updateDaysInMonth() {
                const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                const startDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                this.daysInMonth = [];
                for (let i = 0; i < startDay; i++) this.daysInMonth.push({ day: null, date: null });
                for (let i = 1; i <= lastDay; i++) {
                    this.daysInMonth.push({ day: i, date: new Date(this.currentYear, this.currentMonth, i) });
                }
            },

            async fetchCalendarData() {
                if (!this.selectedFacilityId) return;
                this.isLoadingCalendar = true;
                try {
                    const res = await fetch(`/schedule_booking/data?fasilitas_id=${this.selectedFacilityId}&year=${this.currentYear}&month=${this.currentMonth + 1}&t=${Date.now()}`);
                    this.calendarEvents = await res.json();
                } catch (e) {
                    this.calendarEvents = [];
                } finally {
                    this.isLoadingCalendar = false;
                }
            },

            formatDateLocal(date) {
                if (!date) return '';
                const d = new Date(date);
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },

            getDateStatus(date) {
                if (!date) return 'closed';
                const today = new Date(); today.setHours(0,0,0,0);
                if (date < today) return 'closed';
                
                for (const ev of this.calendarEvents) {
                    const start = new Date(ev.tgl_mulai); start.setHours(0,0,0,0);
                    const end   = new Date(ev.tgl_selesai); end.setHours(23,59,59,999);
                    if (date >= start && date <= end) {
                        if (ev.color === "yellow") return "pending";
                        if (ev.color === "blue")   return "booked";
                        if (ev.color === "black")  return "blocked";
                        if (ev.color === "red")    return "maintenance";
                    }
                }
                return 'ready';
            },

            getDayInfo(date) {
                if (!date) return "";
                for (const ev of this.calendarEvents) {
                    const start = new Date(ev.tgl_mulai); start.setHours(0,0,0,0);
                    const end   = new Date(ev.tgl_selesai); end.setHours(23,59,59,999);
                    if (date >= start && date <= end) {
                        if (ev.status === "maintenance") return "Perbaikan: " + (ev.reason || "Maintenance");
                        return ev.status.toUpperCase();
                    }
                }
                return "";
            },

            isInRange(date) {
                if (!this.selectedDate || !date) return false;
                const start = new Date(this.selectedDate);
                start.setHours(0,0,0,0);
                const end = new Date(start);
                
                if (this.packageType === 'harian') {
                    end.setDate(start.getDate() + (parseInt(this.duration) - 1));
                } else {
                    end.setMonth(start.getMonth() + parseInt(this.duration));
                    end.setDate(end.getDate() - 1);
                }
                
                date.setHours(0,0,0,0);
                return date >= start && date <= end;
            },

            selectDate(date) {
                const status = this.getDateStatus(date);
                if (!date || status !== 'ready') return;
                this.selectedDate = date;
            },

            prevMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
            },

            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
            },

            get monthName() {
                return new Intl.DateTimeFormat('id-ID', { month: 'long' }).format(new Date(this.currentYear, this.currentMonth));
            },

            submitBooking() {
                if (!this.name || !this.whatsapp || !this.email || !this.provinsiName || !this.kabupatenName || !this.fotoIdentitas) {
                    Swal.fire({
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi seluruh data pemohon termasuk Asal Wilayah dan Foto Identitas Anda.',
                        icon: 'warning',
                        confirmButtonColor: '#1265A8'
                    });
                    return;
                }

                Swal.fire({ title: 'Mengirim Reservasi...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                const formData = new FormData();
                formData.append('name', this.name);
                formData.append('whatsapp', this.whatsapp);
                formData.append('email', this.email);
                formData.append('provinsi', this.provinsiName);
                formData.append('kabupaten', this.kabupatenName);
                formData.append('fasilitas_id', this.selectedFacilityId);
                formData.append('package_type', this.packageType);
                if (this.fotoIdentitas) {
                    formData.append('foto_identitas', this.fotoIdentitas);
                }
                formData.append('duration', this.duration);
                formData.append('adults', this.adults);
                formData.append('children_count', this.children);
                formData.append('rooms_count', this.rooms);
                this.childAges.forEach(age => formData.append('child_age[]', age));
                formData.append('tgl_mulai', this.formatDateLocal(this.selectedDate));
                if (this.packageType === 'bulanan') {
                    const end = new Date(this.selectedDate);
                    end.setMonth(end.getMonth() + parseInt(this.duration));
                    end.setDate(end.getDate() - 1);
                    formData.append('tgl_selesai', this.formatDateLocal(end));
                }
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('bookings.store') }}', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        sessionStorage.setItem('booking_success', 'true');
                        window.location.href = '/';
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        }));
    });
</script>

</body>
</html>
