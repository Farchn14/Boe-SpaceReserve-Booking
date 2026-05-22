<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Form Reservasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        .step-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        .status-ready       { background-color: #d1fae5; color: #065f46; cursor: pointer; }
        .status-ready:hover { background-color: #a7f3d0; }
        .status-pending     { background-color: #fef9c3; color: #854d0e; cursor: not-allowed; }
        .status-booked      { background-color: #dbeafe; color: #1e40af; cursor: not-allowed; }
        .status-blocked     { background-color: #1e293b; color: #f1f5f9; cursor: not-allowed; }
        .status-maintenance { background-color: #fee2e2; color: #991b1b; cursor: not-allowed; }
        .status-past        { background-color: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
        .status-closed      { background-color: #e2e8f0; color: #94a3b8; opacity: 0.5; cursor: not-allowed; }
        .status-in-range    { background-color: #bfdbfe; color: #1e40af; }
        .status-conflict    { background-color: #fca5a5; color: #7f1d1d; cursor: not-allowed; }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }
        .animate-shake { animation: shake 0.4s ease-in-out; }

        .custom-select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.2rem;
            padding-right: 2.5rem;
        }

        .room-slot {
            transition: all 0.3s ease;
            position: relative;
        }
        .room-slot .person-dot {
            width: 20px; height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
            transition: all 0.3s ease;
        }
        .person-dot.filled  { background: #2563eb; color: #fff; }
        .person-dot.empty   { background: #e2e8f0; color: #94a3b8; border: 2px dashed #cbd5e1; }
        .person-dot.child   { background: #f59e0b; color: #fff; }

        @keyframes popIn {
            0%   { transform: scale(0.7); opacity: 0; }
            70%  { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); }
        }
        .pop-in { animation: popIn 0.35s ease forwards; }

        .field-error { border-color: #f87171 !important; background-color: rgba(254,242,242,0.4) !important; }
        .field-ok    { border-color: #34d399 !important; background-color: rgba(236,253,245,0.3) !important; }
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

        <!-- STEP 1 -->
        <div x-show="step === 1" x-transition class="step-transition">

            <div x-show="currentFacility" class="mb-8 px-6 py-4 rounded-[1.5rem] flex items-center gap-4"
                 style="background:linear-gradient(135deg,#1e3a5f,#0f172a)">
                <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            x-show="currentFacility?.tipe==='aula'"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            x-show="currentFacility?.tipe==='asrama'"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-[0.25em] mb-0.5">Fasilitas yang Dipilih</p>
                    <p class="text-white font-black text-base truncate" x-text="currentFacility?.nama"></p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[10px] font-bold text-blue-300 uppercase" x-text="currentFacility?.tipe?.toUpperCase()"></span>
                        <span class="text-blue-600">·</span>
                        <span class="text-[10px] text-slate-400">Kapasitas: <span class="text-blue-300 font-bold" x-text="currentFacility?.max_dewasa || '–'"></span> orang</span>
                    </div>
                </div>
                <span class="bg-emerald-500/20 text-emerald-400 text-[9px] font-black px-3 py-1.5 rounded-full uppercase border border-emerald-500/30 flex-shrink-0">Tersedia</span>
            </div>

            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 1/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Pilih Tipe Sewa</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Tentukan durasi pemesanan Anda di BOE Malang.</p>
            </div>

            <div class="space-y-3">
                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 ml-1 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Jenis Paket Sewa <span class="text-red-500">*</span>
                </label>
                <select x-model="packageType"
                        class="custom-select w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl outline-none font-bold text-sm text-gray-700 transition-all duration-200 focus:border-blue-500 focus:bg-white"
                        :class="packageType ? 'border-blue-400 bg-blue-50/20' : (step1Submitted && !packageType ? 'field-error' : '')">
                    <option value="" disabled selected>-- Pilih Jenis Sewa --</option>
                    <option value="harian">Harian — Per Hari</option>
                    <option value="mingguan">Mingguan — Per Minggu (7 Hari)</option>
                    <option value="bulanan">Bulanan — Per Bulan</option>
                    <option value="tahunan">Tahunan — Per Tahun</option>
                </select>

                <div x-show="step1Submitted && !packageType" x-transition class="flex items-center gap-1.5 ml-1 mt-1">
                    <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="text-[10px] text-red-500 font-semibold">Jenis sewa wajib dipilih</span>
                </div>

                <template x-if="packageType">
                    <div x-transition class="p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-blue-700 mb-0.5" x-text="packageLabels[packageType]?.title"></p>
                                <p class="text-[11px] text-blue-600 font-medium" x-text="packageLabels[packageType]?.desc"></p>
                                <p class="text-[10px] text-blue-500 font-semibold mt-1" x-text="tarifLabel"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-10 flex justify-between gap-4">
                <button @click="confirmCancel()" class="flex-1 py-4 px-6 text-gray-400 hover:text-red-500 font-bold uppercase tracking-widest text-xs transition-colors">Batal</button>
                <button @click="submitStep1()"
                    class="flex-[2] py-4 px-6 font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg transition-all"
                    :class="packageType ? 'bg-blue-600 text-white shadow-blue-200 hover:bg-blue-700' : 'bg-gray-100 text-gray-400'">
                    Lanjut →
                </button>
            </div>
        </div>

        <!-- STEP 2 -->
        <div x-show="step === 2" x-transition class="step-transition">
            <div class="text-center mb-8">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 2/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Konfigurasi Paket</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">
                    <span x-text="currentFacility?.nama"></span> · <span x-text="packageLabels[packageType]?.title"></span>
                </p>
            </div>

            <div class="mb-5 p-4 rounded-[1.5rem] flex items-center gap-4" style="background:linear-gradient(135deg,#1e293b,#0f172a)">
                <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            x-show="currentFacility?.tipe==='asrama'"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            x-show="currentFacility?.tipe==='aula'"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Menyewa</p>
                    <p class="text-white font-black text-sm truncate" x-text="currentFacility?.nama"></p>
                </div>
                <span class="bg-blue-500/20 text-blue-300 text-[9px] font-black px-3 py-1.5 rounded-full uppercase" x-text="currentFacility?.tipe"></span>
            </div>

            <div class="space-y-5">

                <!-- DURASI -->
                <div class="p-5 bg-gray-50 rounded-3xl border-2 transition-all"
                     :class="step2Errors.duration ? 'border-red-300 bg-red-50/30' : 'border-gray-100'">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-black text-gray-800 uppercase tracking-tighter text-sm">Durasi Sewa</h4>
                            <p class="text-[10px] font-bold uppercase tracking-widest mt-0.5"
                               :class="step2Errors.duration ? 'text-red-400' : 'text-gray-400'"
                               x-text="packageType==='harian'?'Satuan: Hari':packageType==='mingguan'?'Satuan: Minggu':packageType==='bulanan'?'Satuan: Bulan':'Satuan: Tahun'"></p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="decDuration()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all"
                                :class="duration<=1?'opacity-40 cursor-not-allowed':''">−</button>
                            <span class="text-2xl font-black w-8 text-center"
                                  :class="step2Errors.duration?'text-red-600':'text-gray-800'"
                                  x-text="duration"></span>
                            <button @click="incDuration()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all">+</button>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <p class="text-[10px] text-gray-400 font-semibold">
                            Total: <span class="text-gray-700 font-black" x-text="totalDays + ' hari'"></span>
                        </p>
                    </div>
                    <p x-show="step2Errors.duration" x-transition class="text-[10px] text-red-500 font-semibold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Durasi minimal 1
                    </p>
                </div>

                <!-- KAMAR (asrama saja) -->
                <div x-show="currentFacility?.tipe === 'asrama'"
                     class="p-5 rounded-3xl border-2 transition-all"
                     :class="step2Errors.rooms ? 'border-red-300 bg-red-50/30' : 'border-blue-200 bg-blue-50'">

                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <h4 class="font-black text-blue-800 uppercase tracking-tighter text-sm">Jumlah Kamar</h4>
                            </div>
                            <p class="text-[10px] font-bold text-blue-600">
                                1 Kamar = Maks <strong>2 Dewasa</strong> + Maks <strong>2 Anak</strong> &nbsp;·&nbsp;
                                Kapasitas: <strong x-text="rooms + ' kamar × 2 = ' + (rooms*2) + ' orang'"></strong>
                            </p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <button @click="decRooms()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-red-500 hover:text-white transition-all"
                                :class="rooms<=1?'opacity-40 cursor-not-allowed':''">−</button>
                            <span class="text-2xl font-black text-blue-700 w-8 text-center" x-text="rooms"></span>
                            <button @click="incRooms()"
                                class="w-11 h-11 text-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl transition-all"
                                :class="rooms >= maxRoomsFromFacility ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'">+</button>
                        </div>
                    </div>

                    <div class="grid gap-3" :style="'grid-template-columns: repeat(' + Math.min(rooms,3) + ', 1fr)'">
                        <template x-for="r in rooms" :key="r">
                            <div class="room-slot bg-white border-2 border-blue-200 rounded-2xl p-3 pop-in">
                                <p class="text-[9px] font-black text-blue-500 uppercase tracking-wider mb-2">Kamar <span x-text="r"></span></p>
                                <div class="flex flex-col gap-1.5">
                                    <!-- Row dewasa -->
                                    <div class="flex items-center gap-1.5">
                                        <template x-for="slot in [1,2]" :key="'a'+slot">
                                            <div class="person-dot"
                                                :class="occupantSlot(r, slot) === 'adult' ? 'filled' : 'empty'">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        </template>
                                        <span class="text-[8px] text-blue-400 font-bold">Dewasa</span>
                                    </div>
                                    <!-- Row anak -->
                                    <div class="flex items-center gap-1.5">
                                        <template x-for="slot in [3,4]" :key="'c'+slot">
                                            <div class="person-dot"
                                                :class="occupantSlot(r, slot) === 'child' ? 'child' : 'empty'">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        </template>
                                        <span class="text-[8px] text-amber-400 font-bold">Anak</span>
                                    </div>
                                    <p class="text-[8px] text-slate-400 font-semibold mt-1.5" x-text="roomLabel(r)"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center gap-3 mt-3">
                        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-blue-600"></div><span class="text-[9px] text-blue-600 font-bold">Dewasa</span></div>
                        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-amber-400"></div><span class="text-[9px] text-amber-600 font-bold">Anak</span></div>
                        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-slate-200 border-2 border-dashed border-slate-300"></div><span class="text-[9px] text-slate-400 font-bold">Kosong</span></div>
                    </div>

                    <!-- FIX: warning kapasitas kamar — gunakan totalBillableGuests untuk slot dewasa -->
                    <div x-show="(adults + billableChildren) > rooms * 2 || freeChildren > rooms * 2" x-transition
                         class="mt-3 p-3 bg-amber-50 border border-amber-300 rounded-2xl flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="text-[11px] text-amber-700 font-black">Kamar tidak cukup!</p>
                            <p class="text-[10px] text-amber-600 font-medium">
                                Maks per kamar: <strong>2 dewasa</strong> dan <strong>2 anak</strong>.
                                Butuh minimal <strong x-text="roomsNeeded"></strong> kamar untuk
                                <span x-text="adults + ' dewasa & ' + children + ' anak'"></span>.
                                Tambah <strong x-text="Math.max(0, roomsNeeded - rooms)"></strong> kamar lagi.
                            </p>
                        </div>
                    </div>

                    <!-- Warning: anak ≥12 menggunakan slot dewasa -->
                    <div x-show="billableChildren > 0 && adultSlotOverflow"
                        x-transition
                        class="mt-3 p-3 bg-red-50 border border-red-300 rounded-2xl flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="text-[11px] text-red-700 font-black">Slot dewasa penuh karena anak ≥12 tahun!</p>
                            <p class="text-[10px] text-red-600 font-medium mt-0.5">
                                Total slot dewasa terpakai:
                                <strong x-text="adults + ' dewasa + ' + billableChildren + ' anak≥12 = ' + (adults + billableChildren) + ' orang'"></strong>
                                → butuh min. <strong x-text="Math.ceil((adults + billableChildren) / 2) + ' kamar'"></strong>.
                                Tambah <strong x-text="adultSlotShortfall + ' kamar lagi'"></strong>.
                            </p>
                        </div>
                    </div>

                    <p x-show="step2Errors.rooms" x-transition class="text-[10px] text-red-500 font-semibold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span x-text="step2Errors.roomsMsg"></span>
                    </p>
                </div>

                <!-- DEWASA -->
                <div class="p-5 bg-gray-50 rounded-3xl border-2 transition-all"
                     :class="step2Errors.adults ? 'border-red-300 bg-red-50/30' : 'border-gray-100'">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-black text-gray-800 uppercase tracking-tighter text-sm"
                                x-text="currentFacility?.tipe==='aula' ? 'Total Peserta / Kapasitas' : 'Jumlah Tamu Dewasa'"></h4>
                            <p class="text-[10px] font-bold mt-0.5"
                               :class="step2Errors.adults ? 'text-red-400' : 'text-gray-400'">
                                <span x-show="currentFacility?.tipe==='asrama'"
                                      x-text="'Maks dewasa: ' + maxAdultsAllowed + ' (dari ' + (rooms*2) + ' slot kamar)'"></span>
                                <span x-show="currentFacility?.tipe==='aula'"
                                      x-text="'Maks. ' + (currentFacility?.max_dewasa||'–') + ' orang'"></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="decAdults()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all"
                                :class="adults<=1?'opacity-40 cursor-not-allowed':''">−</button>
                            <span class="text-2xl font-black w-8 text-center"
                                  :class="step2Errors.adults?'text-red-600':'text-gray-800'"
                                  x-text="adults"></span>
                            <button @click="incAdults()"
                                class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-xl transition-all"
                                :class="adults >= maxAdultsAllowed
                                    ? 'bg-amber-100 text-amber-500 hover:bg-amber-200'
                                    : 'bg-white shadow-sm text-blue-600 hover:bg-blue-600 hover:text-white'">
                                <span x-show="adults < maxAdultsAllowed">+</span>
                                <svg x-show="adults >= maxAdultsAllowed"
                                    class="w-5 h-5 text-amber-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div x-show="showRoomHint && currentFacility?.tipe==='asrama'" x-transition
                         class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-2xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[11px] text-blue-700 font-semibold">
                            Slot penuh — tambah kamar untuk menambah tamu dewasa.
                            Saat ini: <span x-text="adults + ' dewasa + ' + children + ' anak = ' + totalOccupants + ' tamu'"></span>.
                        </p>
                    </div>

                    <!-- FIX: hint untuk aula jika capai max -->
                    <div x-show="showRoomHint && currentFacility?.tipe==='aula'" x-transition
                         class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[11px] text-amber-700 font-semibold">
                            Kapasitas aula penuh. Maks. <span x-text="currentFacility?.max_dewasa"></span> peserta.
                        </p>
                    </div>

                    <p x-show="step2Errors.adults" x-transition class="text-[10px] text-red-500 font-semibold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span x-text="step2Errors.adultsMsg"></span>
                    </p>
                </div>

                <!-- ANAK (asrama saja) -->
                <div x-show="currentFacility?.tipe === 'asrama'"
                     class="p-5 bg-gray-50 rounded-3xl border-2 transition-all"
                     :class="step2Errors.childAges ? 'border-red-300 bg-red-50/30' : 'border-gray-100'">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-black text-gray-800 uppercase tracking-tighter text-sm">Jumlah Anak</h4>
                            <p class="text-[10px] font-bold mt-0.5"
                               :class="step2Errors.childAges ? 'text-red-400' : 'text-gray-400'"
                               x-text="'Maks anak: ' + maxChildrenAllowed + ' (slot anak per kamar)'"></p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="decChildren()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all"
                                :class="children<=0?'opacity-40 cursor-not-allowed':''">−</button>
                            <span class="text-2xl font-black text-gray-800 w-8 text-center" x-text="children"></span>
                            <button @click="incChildren()"
                                class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-xl transition-all"
                                :class="children >= maxChildrenAllowed
                                    ? 'bg-amber-100 text-amber-500 hover:bg-amber-200'
                                    : 'bg-white shadow-sm text-blue-600 hover:bg-blue-600 hover:text-white'">
                                <span x-show="children < maxChildrenAllowed">+</span>
                                <svg x-show="children >= maxChildrenAllowed"
                                    class="w-5 h-5 text-amber-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div x-show="showChildHint" x-transition
                         class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[11px] text-amber-700 font-semibold">
                            Slot penuh — tambah kamar untuk menambah anak.
                            Saat ini: <span x-text="adults + ' dewasa + ' + children + ' anak = ' + totalOccupants + ' tamu'"></span>.
                        </p>
                    </div>
                </div>

                <!-- Input umur anak -->
                <div x-show="currentFacility?.tipe==='asrama' && children > 0" x-transition
                    class="p-5 bg-blue-50/30 rounded-3xl border border-blue-100">
                    <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Umur Anak (Tahun)</h4>
                    <p class="text-[10px] text-blue-400 font-medium mb-4">
                        Anak &lt; 12 tahun = <span class="font-black text-emerald-600">Gratis</span> ·
                        Anak ≥ 12 tahun = <span class="font-black text-amber-600">Tarif Dewasa</span>
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        <template x-for="(age, idx) in childAges" :key="idx">
                            <div class="text-center">
                                <label class="text-[9px] text-blue-500 font-bold uppercase block mb-1">
                                    Anak <span x-text="idx+1"></span>
                                </label>
                                <input type="number" x-model.number="childAges[idx]" min="0" max="17" placeholder="0"
                                    class="w-full p-2.5 bg-white border-2 rounded-xl text-center font-bold text-sm outline-none focus:border-blue-400 transition-all"
                                    :class="step2Submitted && (childAges[idx] === '' || childAges[idx] === null || childAges[idx] === undefined)
                                        ? 'field-error'
                                        : (childAges[idx] !== '' && childAges[idx] !== null && childAges[idx] !== undefined)
                                            ? (parseInt(childAges[idx]) >= 12 ? 'border-amber-400 bg-amber-50/30' : 'border-emerald-400 bg-emerald-50/30')
                                            : 'border-gray-200'">
                                <div x-show="childAges[idx] !== '' && childAges[idx] !== null && childAges[idx] !== undefined"
                                    x-transition class="mt-1.5 text-center">
                                    <template x-if="parseInt(childAges[idx]) >= 12">
                                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-[8px] font-black px-2 py-0.5 rounded-full border border-amber-200">
                                            Tarif Dewasa
                                        </span>
                                    </template>
                                    <template x-if="childAges[idx] !== '' && childAges[idx] !== null && childAges[idx] !== undefined && parseInt(childAges[idx]) < 12">
                                        <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-[8px] font-black px-2 py-0.5 rounded-full border border-emerald-200">
                                            Gratis
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <p x-show="step2Errors.childAges" x-transition
                    class="text-[10px] text-red-500 font-semibold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Mohon isi umur semua anak
                    </p>

                    <template x-if="children > 0 && childAges.filter(a => a !== '' && a !== null && a !== undefined).length === children">
                        <div x-transition class="mt-4 pt-4 border-t border-blue-200">
                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-3">Ringkasan Tagihan Tamu</p>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="bg-white rounded-2xl p-3 text-center border border-gray-100">
                                    <p class="text-[8px] text-gray-400 font-bold uppercase mb-1">Dewasa</p>
                                    <p class="text-base font-black text-blue-700" x-text="adults"></p>
                                    <p class="text-[8px] text-blue-500 font-semibold">Berbayar</p>
                                </div>
                                <div class="bg-white rounded-2xl p-3 text-center border border-amber-100">
                                    <p class="text-[8px] text-amber-500 font-bold uppercase mb-1">Anak ≥12</p>
                                    <p class="text-base font-black text-amber-600" x-text="billableChildren"></p>
                                    <p class="text-[8px] text-amber-500 font-semibold">Tarif Dewasa</p>
                                </div>
                                <div class="bg-white rounded-2xl p-3 text-center border border-emerald-100">
                                    <p class="text-[8px] text-emerald-500 font-bold uppercase mb-1">Anak &lt;12</p>
                                    <p class="text-base font-black text-emerald-600" x-text="freeChildren"></p>
                                    <p class="text-[8px] text-emerald-500 font-semibold">Gratis</p>
                                </div>
                            </div>
                            <div x-show="billableChildren > 0" x-transition
                                class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-[11px] text-amber-700 font-bold">
                                        <span x-text="billableChildren"></span> anak ditagih sebagai dewasa
                                    </p>
                                </div>
                                <span class="bg-amber-200 text-amber-800 text-[10px] font-black px-2.5 py-1 rounded-full"
                                    x-text="totalBillableGuests + ' tamu berbayar'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- FIX: warning anak≥12 di slot dewasa — hanya tampil untuk asrama -->
                <div x-show="currentFacility?.tipe==='asrama' && billableChildren > 0" x-transition
                    class="mt-3 p-3 bg-amber-50 border border-amber-300 rounded-2xl flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-[11px] text-amber-700 font-black">
                            <span x-text="billableChildren"></span> anak ≥12 tahun terhitung sebagai dewasa
                        </p>
                        <p class="text-[10px] text-amber-600 font-medium mt-0.5">
                            Mereka menempati <strong>slot dewasa</strong>. Pastikan jumlah kamar mencukupi
                            total <span x-text="totalBillableGuests"></span> tamu berbayar
                            (<span x-text="adults + ' dewasa + ' + billableChildren + ' anak≥12'"></span>).
                        </p>
                    </div>
                </div>

            </div>

            <!-- Ringkasan mini -->
            <div class="mt-5 p-4 bg-slate-900 rounded-2xl">
                <div class="grid gap-3 text-center"
                    :style="currentFacility?.tipe==='asrama' ? 'grid-template-columns: repeat(3, 1fr)' : 'grid-template-columns: repeat(2, 1fr)'">
                    <div>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Durasi</p>
                        <p class="text-white font-black text-sm"
                        x-text="duration + ' ' + (packageType==='harian'?'Hari':packageType==='mingguan'?'Minggu':packageType==='bulanan'?'Bln':'Thn')"></p>
                    </div>
                    <div x-show="currentFacility?.tipe==='asrama'">
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Kamar</p>
                        <p class="font-black text-sm"
                        :class="((adults + billableChildren) > rooms*2 || freeChildren > rooms*2) ? 'text-amber-400' : 'text-white'"
                        x-text="rooms + ' Kamar'"></p>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Tamu Berbayar</p>
                        <p class="font-black text-sm text-white" x-text="totalBillableGuests + ' Orang'"></p>
                        <p x-show="billableChildren > 0"
                        class="text-[8px] text-amber-400 font-semibold mt-0.5"
                        x-text="adults + ' dewasa + ' + billableChildren + ' anak(≥12)'"></p>
                        <p x-show="freeChildren > 0"
                        class="text-[8px] text-emerald-400 font-semibold"
                        x-text="freeChildren + ' anak gratis (<12)'"></p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-between gap-4">
                <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                <button @click="submitStep2()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200">Lanjut ke Kalender →</button>
            </div>
        </div>

        <!-- STEP 3 -->
        <div x-show="step === 3" x-transition class="step-transition">
            <div class="text-center mb-8">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 3/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Pilih Tanggal</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Semua hari dalam durasi sewa harus tersedia</p>
            </div>

            <div class="flex flex-wrap gap-2 mb-5 justify-center">
                <span class="bg-slate-100 text-slate-700 text-[10px] font-black px-3 py-1.5 rounded-full uppercase" x-text="currentFacility?.nama"></span>
                <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-3 py-1.5 rounded-full uppercase" x-text="packageLabels[packageType]?.title"></span>
                <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-3 py-1.5 rounded-full uppercase" x-text="totalDays + ' Hari'"></span>
                <span x-show="currentFacility?.tipe==='asrama'" class="bg-purple-100 text-purple-700 text-[10px] font-black px-3 py-1.5 rounded-full uppercase"
                      x-text="rooms + ' Kamar · ' + totalOccupants + ' Org'"></span>
            </div>

            <div x-show="step3Submitted && !selectedDate" x-transition
                 class="mb-4 p-3 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-[11px] text-red-600 font-bold">Pilih tanggal check-in terlebih dahulu</p>
            </div>

            <div class="bg-white rounded-[2.5rem] overflow-hidden border-2 border-black/10 shadow-xl relative">
                <div x-show="isLoadingCalendar" class="absolute inset-0 z-50 bg-white/60 backdrop-blur-sm flex flex-col items-center justify-center gap-4">
                    <div class="w-10 h-10 border-4 border-slate-100 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Sinkronisasi Jadwal...</p>
                </div>

                <div class="p-6 md:p-8 flex items-center justify-between bg-white border-b border-gray-100">
                    <div>
                        <h3 class="text-xl md:text-2xl font-black uppercase tracking-tighter text-gray-900" x-text="monthName"></h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]" x-text="currentYear"></p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="prevMonth()" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="nextMonth()" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 md:px-8 py-3 flex flex-wrap gap-x-4 gap-y-2 bg-gray-50/50 border-b border-gray-100">
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-emerald-300"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Ready</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-yellow-300"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Pending</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-blue-300"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Booked</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-red-300"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Tidak Tersedia</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-blue-400"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Rentang Dipilih</span></div>
                </div>

                <div class="grid grid-cols-7 gap-px bg-gray-100">
                    <template x-for="d in ['MIN','SEN','SEL','RAB','KAM','JUM','SAB']">
                        <div class="bg-gray-50 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest" x-text="d"></div>
                    </template>
                    <template x-for="(item, idx) in daysInMonth" :key="idx">
                        <div class="h-16 sm:h-20 md:h-24 relative group flex flex-col items-center justify-center transition-all"
                             :class="getDayCellClass(item)"
                             @click="selectDate(item.date)">
                            <div x-show="item.day"
                                 class="relative z-10 text-sm md:text-base font-black transition-all duration-300"
                                 :class="{ 'ring-4 ring-black/20 rounded-full w-8 h-8 md:w-10 md:h-10 flex items-center justify-center bg-gray-900 text-white shadow-lg scale-110': selectedDate && item.date && item.date.getTime()===selectedDate.getTime() }"
                                 x-text="item.day"></div>
                            <div x-show="item.day && !['ready','closed','past','in-range'].includes(getDateStatus(item.date))"
                                 class="absolute bottom-1.5 w-1.5 h-1.5 rounded-full"
                                 :class="{ 'bg-yellow-500': getDateStatus(item.date)==='pending', 'bg-blue-600': getDateStatus(item.date)==='booked', 'bg-red-600': getDateStatus(item.date)==='maintenance'||getDateStatus(item.date)==='blocked' }">
                            </div>
                            <template x-if="item.day && !['ready','closed','past'].includes(getDateStatus(item.date))">
                                <div class="absolute bottom-1 left-0 right-0 text-center opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                    <span class="bg-black/80 text-white text-[7px] font-black uppercase px-2 py-0.5 rounded shadow-lg whitespace-nowrap" x-text="getDayInfo(item.date)"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="selectedDate" x-transition class="mt-4 p-4 rounded-2xl"
                 :class="hasConflictInRange ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200'">
                <div class="flex items-start gap-3">
                    <svg x-show="!hasConflictInRange" class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg x-show="hasConflictInRange" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs font-black" :class="hasConflictInRange ? 'text-red-700' : 'text-emerald-700'"
                           x-text="hasConflictInRange ? 'Ada hari dalam range yang tidak tersedia!' : 'Semua hari tersedia ✓'"></p>
                        <p class="text-[11px] font-semibold mt-0.5" :class="hasConflictInRange ? 'text-red-600' : 'text-emerald-600'"
                           x-text="'Check-in: ' + formatDisplay(selectedDate) + '  →  Check-out: ' + formatDisplay(endDate)"></p>
                        <p x-show="hasConflictInRange" class="text-[11px] text-red-500 font-semibold mt-1">
                            Pilih tanggal mulai lain yang semua harinya kosong.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-between gap-4">
                <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                <button @click="submitStep3()"
                    class="flex-[2] py-4 px-6 font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg transition-all"
                    :class="selectedDate && !hasConflictInRange ? 'bg-blue-600 text-white shadow-blue-200' : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                    Konfirmasi Data Diri →
                </button>
            </div>
        </div>

        <!-- STEP 4 -->
        <div x-show="step === 4" x-transition class="step-transition">

            <div class="text-center mb-8">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah Akhir</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Konfirmasi Data</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Detail Pemohon</p>
            </div>

            <div class="space-y-5">
                <div :class="{'animate-shake': step4Shake.name}">
                    <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                        :class="step4Errors.name?'text-red-500':step4Success.name?'text-emerald-500':'text-gray-400'">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" x-model="step4Name" @input="s4ValidateField('name')" placeholder="Masukkan nama lengkap Anda"
                            class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all pr-10"
                            :class="step4Errors.name?'field-error':step4Success.name?'field-ok':'border-gray-200 focus:border-blue-500 focus:bg-white'">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Errors.name">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Success.name" x-transition>
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <!-- FIX: Pesan error lebih deskriptif -->
                    <p x-show="step4Errors.name" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Nama minimal 3 karakter, hanya huruf dan spasi</p>
                    <p x-show="step4Success.name" x-transition class="text-[10px] text-emerald-600 font-semibold mt-1.5 ml-1">Nama valid ✓</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative" style="z-index:50;">
                    <!-- Provinsi -->
                    <div :class="{'animate-shake': step4Shake.provinsi}" style="z-index:40; position:relative;">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                            :class="step4Errors.provinsi?'text-red-500':step4Success.provinsi?'text-emerald-500':'text-gray-400'">
                            Provinsi Asal <span class="text-red-500">*</span>
                        </label>
                        <div @click.stop="step4OpenProvinsi=!step4OpenProvinsi;step4OpenKabupaten=false" @click.away="step4OpenProvinsi=false"
                            class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl flex justify-between items-center cursor-pointer font-medium text-sm transition-all"
                            :class="step4Errors.provinsi?'field-error':step4Success.provinsi?'field-ok':'border-gray-200 hover:border-gray-300'">
                            <span :class="step4ProvinsiName?'text-gray-800':'text-gray-400'" x-text="step4ProvinsiName||'Pilih Provinsi...'"></span>
                            <svg class="w-4 h-4 transition-transform" :class="step4OpenProvinsi?'text-blue-500 rotate-180':'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <div x-show="step4OpenProvinsi" x-transition @click.stop
                            class="absolute left-0 w-full mt-2 bg-white border border-gray-100 shadow-xl rounded-2xl overflow-hidden" style="z-index:100;">
                            <div class="p-3 border-b border-gray-50">
                                <input x-model="step4SearchProvinsi" type="text" placeholder="Cari Provinsi..."
                                    class="w-full bg-gray-50 text-xs px-4 py-2.5 rounded-xl outline-none border border-gray-200 focus:border-blue-400">
                            </div>
                            <div class="max-h-48 overflow-y-auto">
                                <div x-show="step4LoadingProvinsi" class="px-5 py-4 text-xs text-gray-400 text-center">Memuat provinsi...</div>
                                <template x-for="p in step4FilteredProvinces" :key="p.id">
                                    <div @click="s4SelectProvinsi(p)" class="px-5 py-2.5 cursor-pointer text-xs font-semibold text-gray-700 transition-colors"
                                        :class="step4Provinsi===p.id?'bg-blue-50 text-blue-600':'hover:bg-gray-50'" x-text="p.name"></div>
                                </template>
                                <div x-show="!step4LoadingProvinsi && step4FilteredProvinces.length===0" class="px-5 py-4 text-xs text-gray-400 text-center">Tidak ditemukan</div>
                            </div>
                        </div>
                        <p x-show="step4Errors.provinsi" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Pilih provinsi terlebih dahulu</p>
                    </div>

                    <!-- Kabupaten -->
                    <div :class="{'animate-shake': step4Shake.kabupaten}" style="z-index:30; position:relative;">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                            :class="step4Errors.kabupaten?'text-red-500':step4Success.kabupaten?'text-emerald-500':'text-gray-400'">
                            Kabupaten / Kota <span class="text-red-500">*</span>
                        </label>
                        <div @click.stop="if(!step4Provinsi){s4TriggerError('provinsi')}else{step4OpenKabupaten=!step4OpenKabupaten;step4OpenProvinsi=false}" @click.away="step4OpenKabupaten=false"
                            class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl flex justify-between items-center font-medium text-sm transition-all"
                            :class="!step4Provinsi?'opacity-50 cursor-not-allowed border-gray-200':step4Errors.kabupaten?'field-error cursor-pointer':step4Success.kabupaten?'field-ok cursor-pointer':'border-gray-200 hover:border-gray-300 cursor-pointer'">
                            <span :class="step4KabupatenName?'text-gray-800':'text-gray-400'" x-text="step4LoadingKabupaten?'Memuat data kota...':step4KabupatenName||'Pilih Kota/Kabupaten...'"></span>
                            <svg class="w-4 h-4 transition-transform" :class="step4OpenKabupaten?'text-blue-500 rotate-180':'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <div x-show="step4OpenKabupaten && step4Provinsi" x-transition @click.stop
                            class="absolute left-0 w-full mt-2 bg-white border border-gray-100 shadow-xl rounded-2xl overflow-hidden" style="z-index:100;">
                            <div class="p-3 border-b border-gray-50">
                                <input x-model="step4SearchKabupaten" type="text" placeholder="Cari Kota..."
                                    class="w-full bg-gray-50 text-xs px-4 py-2.5 rounded-xl outline-none border border-gray-200 focus:border-blue-400">
                            </div>
                            <div class="max-h-48 overflow-y-auto">
                                <div x-show="step4LoadingKabupaten" class="px-5 py-4 text-xs text-gray-400 text-center">Memuat data kabupaten...</div>
                                <template x-for="k in step4FilteredRegencies" :key="k.id">
                                    <div @click="s4SelectKabupaten(k)" class="px-5 py-2.5 cursor-pointer text-xs font-semibold text-gray-700 transition-colors"
                                        :class="step4Kabupaten===k.id?'bg-blue-50 text-blue-600':'hover:bg-gray-50'" x-text="k.name"></div>
                                </template>
                                <div x-show="!step4LoadingKabupaten && step4FilteredRegencies.length===0" class="px-5 py-4 text-xs text-gray-400 text-center">Tidak ditemukan</div>
                            </div>
                        </div>
                        <p x-show="step4Errors.kabupaten" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Pilih kota/kabupaten terlebih dahulu</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-0">
                    <!-- WhatsApp -->
                    <div :class="{'animate-shake': step4Shake.whatsapp}">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                            :class="step4Errors.whatsapp?'text-red-500':step4Success.whatsapp?'text-emerald-500':'text-gray-400'">
                            Nomor WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" x-model="step4Whatsapp" @input="s4ValidateField('whatsapp')" placeholder="08xxxxxxxxx" maxlength="14"
                                class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all pr-10"
                                :class="step4Errors.whatsapp?'field-error':step4Success.whatsapp?'field-ok':'border-gray-200 focus:border-blue-500 focus:bg-white'">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Errors.whatsapp">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Success.whatsapp" x-transition>
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <p x-show="step4Errors.whatsapp" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Nomor valid 9–14 digit angka</p>
                        <p x-show="step4Success.whatsapp" x-transition class="text-[10px] text-emerald-600 font-semibold mt-1.5 ml-1">Nomor valid ✓</p>
                    </div>

                    <!-- Email -->
                    <div :class="{'animate-shake': step4Shake.email}">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                            :class="step4Errors.email?'text-red-500':step4Success.email?'text-emerald-500':'text-gray-400'">
                            Email Aktif <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="email" x-model="step4Email" @input="s4ValidateField('email')" placeholder="nama@email.com"
                                class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all pr-10"
                                :class="step4Errors.email?'field-error':step4Success.email?'field-ok':'border-gray-200 focus:border-blue-500 focus:bg-white'">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Errors.email">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Success.email" x-transition>
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <p x-show="step4Errors.email" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Format email tidak sesuai</p>
                        <p x-show="step4Success.email" x-transition class="text-[10px] text-emerald-600 font-semibold mt-1.5 ml-1">Email valid ✓</p>
                    </div>
                </div>

                <!-- Foto Identitas -->
                <div :class="{'animate-shake': step4Shake.foto}"
                    class="p-5 bg-white border-2 rounded-3xl transition-all"
                    :style="step4Errors.foto?'border-color:#f87171':step4Success.foto?'border-color:#34d399':'border-color:#e5e7eb'">
                    <label class="text-[9px] font-black uppercase tracking-widest flex items-center gap-1 mb-4 transition-colors"
                        :class="step4Errors.foto?'text-red-500':step4Success.foto?'text-emerald-500':'text-gray-400'">
                        Upload Foto Identitas (KTP/SIM) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 space-y-3">
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed rounded-2xl cursor-pointer transition-all"
                                :class="step4Errors.foto?'border-red-400 bg-red-50/30':step4Success.foto?'border-emerald-400 bg-emerald-50/30':'border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-blue-400'">
                                <div class="flex flex-col items-center text-center px-4">
                                    <svg x-show="!step4Success.foto&&!step4Errors.foto" class="w-7 h-7 mb-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <svg x-show="step4Success.foto" class="w-7 h-7 mb-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <svg x-show="step4Errors.foto" class="w-7 h-7 mb-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <p class="text-xs font-semibold" :class="step4Errors.foto?'text-red-500':step4Success.foto?'text-emerald-600':'text-gray-500'"
                                    x-text="step4Success.foto ? step4FotoFileName : 'Klik untuk unggah file'"></p>
                                    <p class="text-[10px] mt-0.5" :class="step4Errors.foto?'text-red-400':step4Success.foto?'text-emerald-500':'text-gray-400'"
                                    x-text="step4Success.foto ? 'Foto siap digunakan ✓' : 'JPG, JPEG, PNG (Maks. 2MB)'"></p>
                                </div>
                                <input type="file" class="hidden" accept="image/jpeg,image/png,image/jpg" @change="s4HandleFileChange($event)" />
                            </label>
                            <p x-show="step4Errors.foto" x-transition class="text-[10px] text-red-500 font-semibold ml-1" x-text="step4FotoErrorMsg"></p>
                            <div class="p-3 bg-blue-50 rounded-xl flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <p class="text-[10px] text-blue-700 font-medium leading-relaxed">Dokumen hanya digunakan untuk validasi reservasi dan dihapus otomatis setelah masa sewa berakhir.</p>
                            </div>
                        </div>
                        <div class="w-full md:w-44 flex flex-col items-center justify-center border-2 rounded-2xl overflow-hidden relative min-h-[7rem] transition-all"
                            :class="step4Errors.foto?'border-red-200 bg-red-50/20':step4Success.foto?'border-emerald-300 bg-emerald-50/20':'border-gray-200 bg-gray-50'">
                            <template x-if="step4FotoPreview">
                                <div class="w-full h-full absolute inset-0">
                                    <img :src="step4FotoPreview" class="object-cover w-full h-full" alt="Preview Identitas">
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                        <p class="text-[9px] text-white font-black text-center tracking-widest">PREVIEW</p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!step4FotoPreview">
                                <div class="text-center p-4">
                                    <svg class="w-7 h-7 mx-auto mb-1.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-[10px] font-bold text-gray-400">Belum ada foto</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Reservasi -->
            <div class="bg-[#0f172a] rounded-[1.75rem] overflow-hidden mt-6">
                <div class="px-6 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold tracking-[.12em] uppercase text-blue-400">Ringkasan Reservasi</span>
                        <span class="text-[10px] font-semibold bg-[#1e3a5f] text-blue-300 px-3 py-1 rounded-full">Draft</span>
                    </div>
                    <div class="mb-4 p-3 bg-[#1e293b] rounded-2xl">
                        <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Fasilitas</p>
                        <p class="text-[16px] font-bold text-slate-100 truncate" x-text="currentFacility?.nama"></p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="bg-blue-500/20 text-blue-400 text-[9px] font-black px-2 py-1 rounded-lg uppercase" x-text="currentFacility?.tipe"></span>
                            <span x-show="currentFacility?.tipe==='asrama'" class="bg-purple-500/20 text-purple-400 text-[9px] font-black px-2 py-1 rounded-lg uppercase"
                                x-text="rooms + ' Kamar · maks ' + (rooms*2) + ' org'"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2.5 mb-4">
                        <div class="bg-[#1e293b] rounded-xl p-3">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Paket</p>
                            <p class="text-[13px] font-bold text-slate-100 capitalize" x-text="packageType"></p>
                        </div>
                        <div class="bg-[#1e293b] rounded-xl p-3">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Check-In</p>
                            <p class="text-[13px] font-bold text-slate-100"
                               x-text="selectedDate ? new Intl.DateTimeFormat('id-ID',{day:'numeric',month:'short'}).format(selectedDate) : '-'"></p>
                        </div>
                        <div class="bg-[#1e293b] rounded-xl p-3">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Durasi</p>
                            <p class="text-[13px] font-bold text-slate-100"
                               x-text="duration + ' ' + (packageType==='harian'?'Hari':packageType==='mingguan'?'Minggu':packageType==='bulanan'?'Bln':'Thn')"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-[#1e293b] rounded-xl px-3.5 py-2.5 mb-5">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-[11px] text-slate-400 font-medium"
                            x-text="adults + ' Dewasa' + (children>0 ? ' + '+children+' Anak ('+totalOccupants+' total)' : '')"></span>
                    </div>
                </div>
                <div class="bg-[#0a1628] px-6 py-4 flex items-center justify-between border-t border-slate-800">
                    <div>
                        <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Total Estimasi</p>
                        <p class="text-[26px] font-extrabold text-blue-400 leading-none"
                           x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice)"></p>
                        <p class="text-[10px] text-slate-600 font-medium mt-1">Sudah termasuk pajak & layanan</p>
                    </div>
                    <div class="w-11 h-11 bg-[#1e3a5f] rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Turnstile Captcha --}}
            <div class="mt-6">
                <div class="g-recaptcha"
                    data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                    data-callback="onRecaptchaSuccess"
                    data-expired-callback="onRecaptchaExpired">
                </div>
                <div x-show="captchaError" x-transition
                    class="mt-2 flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-[10px] text-red-500 font-semibold">Selesaikan verifikasi terlebih dahulu</span>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <div class="flex justify-between gap-4">
                    <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                    <button @click="doSubmit()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200 hover:bg-black transition-all">Submit Reservasi</button>
                </div>
                <button @click="confirmCancel()" class="w-full py-4 text-red-500 font-bold uppercase tracking-widest text-[10px] bg-red-50 rounded-2xl border border-red-100">Batal Booking</button>
            </div>
        </div>
    </div>

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
            facilities: config.facilities || [],
            selectedFacilityId: config.selectedFacilityId || '',
            currentMonth: new Date().getMonth(),
            currentYear:  new Date().getFullYear(),
            daysInMonth:  [],
            calendarEvents: [],
            isLoadingCalendar: false,
            showRoomHint:  false,
            showChildHint: false,

            step1Submitted: false,
            step2Submitted: false,
            step3Submitted: false,
            step2Errors: { duration: false, rooms: false, roomsMsg: '', adults: false, adultsMsg: '', childAges: false },

            // ── Step 4 state ──────────────────────────────────────────
            step4Name: '',
            step4Provinsi: '',
            step4ProvinsiName: '',
            step4Kabupaten: '',
            step4KabupatenName: '',
            step4Whatsapp: '',
            step4Email: '',
            step4FotoPreview: null,
            step4FotoFile: null,
            step4FotoFileName: '',
            step4FotoErrorMsg: '',
            step4SearchProvinsi: '',
            step4SearchKabupaten: '',
            step4Provinces: [],
            step4Regencies: [],
            step4RegenciesCache: {},
            step4LoadingProvinsi: false,
            step4LoadingKabupaten: false,
            step4OpenProvinsi: false,
            step4OpenKabupaten: false,
            // Captcha
            captchaToken: '',
            captchaError: false,
            step4Submitted: false,
            step4Errors:  { name:false, provinsi:false, kabupaten:false, whatsapp:false, email:false, foto:false },
            step4Success: { name:false, provinsi:false, kabupaten:false, whatsapp:false, email:false, foto:false },
            step4Shake:   { name:false, provinsi:false, kabupaten:false, whatsapp:false, email:false, foto:false },

            packageLabels: {
                harian:   { title: 'Harian',   desc: 'Cocok untuk kebutuhan jangka pendek, dihitung per hari.' },
                mingguan: { title: 'Mingguan', desc: 'Paket 7 hari, lebih hemat dari harian.' },
                bulanan:  { title: 'Bulanan',  desc: 'Lebih hemat untuk kebutuhan jangka panjang.' },
                tahunan:  { title: 'Tahunan',  desc: 'Harga terbaik untuk kontrak panjang.' },
            },

            async init() {
                this.updateDaysInMonth();
                this.$watch('children', val => {
                    const count = parseInt(val) || 0;
                    while (this.childAges.length < count) this.childAges.push('');
                    this.childAges = this.childAges.slice(0, count);
                });
                this.$watch('currentMonth', () => { this.updateDaysInMonth(); this.fetchCalendarData(); });
                this.$watch('currentYear',  () => { this.updateDaysInMonth(); this.fetchCalendarData(); });
                this.$watch('selectedFacilityId', () => { this.fetchCalendarData(); });
                this.$watch('step', val => {
                    if (val === 4) {
                        if (this.step4Provinces.length === 0) {
                            this.s4LoadProvinces();
                        }
                        // Re-render Turnstile widget
                        setTimeout(() => {
                            const container = document.querySelector('.cf-turnstile');
                            if (window.grecaptcha) {
                                window.grecaptcha.reset();
                            }
                        }, 300);
                    }
                });
            },

            // ── Step 4 methods ────────────────────────────────────────

            async s4LoadProvinces() {
                this.step4LoadingProvinsi = true;
                try {
                    const cached = sessionStorage.getItem('_provinces_cache');
                    if (cached) {
                        this.step4Provinces = JSON.parse(cached);
                        this.step4LoadingProvinsi = false;
                        return;
                    }
                } catch(e) {}
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), 8000);
                try {
                    const r = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json',
                        { signal: controller.signal });
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    this.step4Provinces = await r.json();
                    try {
                        sessionStorage.setItem('_provinces_cache', JSON.stringify(this.step4Provinces));
                    } catch(e) {}
                } catch(e) {
                    if (e.name === 'AbortError') {
                        Swal.fire('Koneksi Lambat', 'Gagal memuat data provinsi. Periksa koneksi internet Anda.', 'error');
                    } else {
                        console.error('Gagal memuat provinsi:', e);
                    }
                    this.step4Provinces = [];
                } finally {
                    clearTimeout(timeout);
                    this.step4LoadingProvinsi = false;
                }
            },

            async s4FetchKabupaten(id) {
                this.step4LoadingKabupaten = true;
                this.step4Regencies = [];
                this.step4Kabupaten = '';
                this.step4KabupatenName = '';
                this.step4Success.kabupaten = false;
                this.step4Errors.kabupaten = false;
                this.step4SearchKabupaten = '';

                if (this.step4RegenciesCache[id]) {
                    this.step4Regencies = this.step4RegenciesCache[id];
                    this.step4LoadingKabupaten = false;
                    return;
                }
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), 8000);
                try {
                    const r = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/' + id + '.json',
                        { signal: controller.signal });
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    this.step4Regencies = await r.json();
                    this.step4RegenciesCache[id] = this.step4Regencies;
                } catch(e) {
                    this.step4Regencies = [];
                    if (e.name === 'AbortError') {
                        Swal.fire('Timeout', 'Gagal memuat data kabupaten. Coba lagi.', 'error');
                    }
                } finally {
                    clearTimeout(timeout);
                    this.step4LoadingKabupaten = false;
                }
            },

            get step4FilteredProvinces() {
                if (!this.step4SearchProvinsi.trim()) return this.step4Provinces;
                return this.step4Provinces.filter(p =>
                    p.name.toLowerCase().includes(this.step4SearchProvinsi.toLowerCase().trim())
                );
            },

            get step4FilteredRegencies() {
                if (!this.step4SearchKabupaten.trim()) return this.step4Regencies;
                return this.step4Regencies.filter(k =>
                    k.name.toLowerCase().includes(this.step4SearchKabupaten.toLowerCase().trim())
                );
            },

            // FIX: s4TriggerError dengan auto-reset shake setelah 400ms
            s4TriggerError(f) {
                this.step4Errors[f] = true;
                this.step4Success[f] = false;
                this.step4Shake[f] = true;
                setTimeout(() => { this.step4Shake[f] = false; }, 400);
            },

            s4TriggerSuccess(f) {
                this.step4Errors[f] = false;
                this.step4Success[f] = true;
                this.step4Shake[f] = false;
            },

            // FIX: validasi nama diperluas — support huruf Indonesia & spasi, minimal 3 karakter
            s4ValidateField(f) {
                if (f === 'name') {
                    const trimmed = this.step4Name.trim();
                    if (!trimmed) { this.step4Errors.name = false; this.step4Success.name = false; return; }
                    // Support huruf latin dasar + spasi, minimal 3 karakter
                    const nameValid = /^[a-zA-Z\s]{3,}$/.test(trimmed);
                    (nameValid ? this.s4TriggerSuccess : this.s4TriggerError).call(this, 'name');
                }
                if (f === 'whatsapp') {
                    const num = this.step4Whatsapp.trim();
                    if (!num) { this.step4Errors.whatsapp = false; this.step4Success.whatsapp = false; return; }
                    (/^[0-9]{9,14}$/.test(num) ? this.s4TriggerSuccess : this.s4TriggerError).call(this, 'whatsapp');
                }
                if (f === 'email') {
                    const em = this.step4Email.trim();
                    if (!em) { this.step4Errors.email = false; this.step4Success.email = false; return; }
                    (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em) ? this.s4TriggerSuccess : this.s4TriggerError).call(this, 'email');
                }
            },

            s4SelectProvinsi(p) {
                this.step4Provinsi = p.id;
                this.step4ProvinsiName = p.name;
                this.step4OpenProvinsi = false;
                this.step4SearchProvinsi = '';
                this.s4TriggerSuccess('provinsi');
                this.s4FetchKabupaten(p.id);
            },

            s4SelectKabupaten(k) {
                this.step4Kabupaten = k.id;
                this.step4KabupatenName = k.name;
                this.step4OpenKabupaten = false;
                this.step4SearchKabupaten = '';
                this.s4TriggerSuccess('kabupaten');
            },

            s4HandleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                if (!['image/jpeg','image/png','image/jpg'].includes(file.type)) {
                    this.step4FotoErrorMsg = 'Format file harus JPG, JPEG, atau PNG!';
                    this.s4TriggerError('foto');
                    this.step4FotoPreview = null;
                    this.step4FotoFileName = '';
                    e.target.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    this.step4FotoErrorMsg = 'Ukuran file terlalu besar! Maksimal 2MB.';
                    this.s4TriggerError('foto');
                    this.step4FotoPreview = null;
                    this.step4FotoFileName = '';
                    e.target.value = '';
                    return;
                }
                this.step4FotoFile = file;
                this.step4FotoFileName = file.name.length > 24 ? file.name.substring(0, 24) + '...' : file.name;
                const reader = new FileReader();
                reader.onload = ev => {
                    this.step4FotoPreview = ev.target.result;
                    this.s4TriggerSuccess('foto');
                };
                reader.readAsDataURL(file);
            },

            s4ValidateAll() {
                let ok = true;
                const nameValid = /^[a-zA-Z\s]{3,}$/.test(this.step4Name.trim());
                if (!this.step4Name.trim() || !nameValid) {
                    this.s4TriggerError('name'); ok = false;
                }
                if (!this.step4ProvinsiName) { this.s4TriggerError('provinsi'); ok = false; }
                if (!this.step4KabupatenName) { this.s4TriggerError('kabupaten'); ok = false; }
                if (!/^[0-9]{9,14}$/.test(this.step4Whatsapp.trim())) { this.s4TriggerError('whatsapp'); ok = false; }
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.step4Email.trim())) { this.s4TriggerError('email'); ok = false; }
                if (!this.step4FotoFile) {
                    this.step4FotoErrorMsg = 'Foto identitas wajib diunggah';
                    this.s4TriggerError('foto');
                    ok = false;
                }
                return ok;
            },

            doSubmit() {
                this.step4Submitted = true;

                if (!this.s4ValidateAll()) {
                    Swal.fire({
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon perbaiki semua field yang ditandai merah.',
                        icon: 'warning',
                        confirmButtonColor: '#1265A8'
                    });
                    return;
                }

                // Cek captcha dulu
                if (!this.captchaToken) {
                    this.captchaError = true;
                    Swal.fire({
                        title: 'Verifikasi Diperlukan',
                        text: 'Harap selesaikan verifikasi captcha terlebih dahulu.',
                        icon: 'warning',
                        confirmButtonColor: '#1265A8'
                    });
                    return;
                }

                const fd = new FormData();
                fd.append('name', this.step4Name.trim());
                fd.append('whatsapp', this.step4Whatsapp.trim());
                fd.append('email', this.step4Email.trim());
                fd.append('provinsi', this.step4ProvinsiName);
                fd.append('kabupaten', this.step4KabupatenName);
                fd.append('foto_identitas', this.step4FotoFile);
                fd.append('fasilitas_id', this.selectedFacilityId);
                fd.append('package_type', this.packageType);
                fd.append('duration', this.duration);
                fd.append('adults', this.adults);
                fd.append('billable_children', this.billableChildren);
                fd.append('free_children', this.freeChildren);
                fd.append('total_billable_guests', this.totalBillableGuests);
                fd.append('children_count', this.children);
                fd.append('rooms_count', this.rooms);
                fd.append('max_per_room', 2);
                this.childAges.forEach(a => fd.append('child_age[]', a));
                fd.append('tgl_mulai', this.formatDateLocal(this.selectedDate));
                fd.append('tgl_selesai', this.formatDateLocal(this.endDate));
                // FIX: ambil CSRF token dari meta tag (lebih reliable dari Blade literal)
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                fd.append('_token', csrfToken);
                fd.append('g-recaptcha-response', this.captchaToken);

                Swal.fire({
                    title: 'Mengunggah Foto...',
                    html: '<div style="margin-top:8px">' +
                        '<div style="background:#e2e8f0;border-radius:99px;height:8px;overflow:hidden">' +
                        '<div id="swal-bar" style="height:100%;width:0%;background:#2563eb;transition:width .3s ease;border-radius:99px"></div>' +
                        '</div>' +
                        '<p id="swal-pct" style="margin-top:6px;font-size:12px;color:#64748b;font-weight:600">0%</p>' +
                        '</div>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const xhr = new XMLHttpRequest();
                const TIMEOUT_MS = 30000;
                const state = { timedOut: false, aborted: false };

                xhr.upload.onprogress = (e) => {
                    if (!e.lengthComputable) return;
                    const pct = Math.round((e.loaded / e.total) * 100);
                    const bar = document.getElementById('swal-bar');
                    const pctEl = document.getElementById('swal-pct');
                    if (bar) bar.style.width = pct + '%';
                    if (pctEl) pctEl.textContent = pct + '%';
                    if (pct >= 100) {
                        const titleEl = Swal.getTitle();
                        if (titleEl) titleEl.textContent = 'Memproses Reservasi...';
                        if (pctEl) pctEl.textContent = 'Mohon tunggu sebentar...';
                    }
                };

                const timeoutId = setTimeout(() => {
                    state.timedOut = true;
                    state.aborted = true;
                    xhr.abort();
                    Swal.fire({
                        title: 'Waktu Habis',
                        text: 'Server tidak merespons dalam 30 detik. Periksa koneksi Anda lalu coba lagi.',
                        icon: 'error',
                        confirmButtonColor: '#1265A8',
                        confirmButtonText: 'Coba Lagi'
                    });
                }, TIMEOUT_MS);

                xhr.onreadystatechange = () => {
                    if (xhr.readyState !== 4) return;
                    clearTimeout(timeoutId);
                    if (state.timedOut) return;
                    // Reset turnstile setelah gagal
                    if (window.grecaptcha) window.grecaptcha.reset();
                    this.captchaToken = '';

                    if (xhr.status >= 200 && xhr.status < 300) {
                        let d;
                        try { d = JSON.parse(xhr.responseText); }
                        catch(e) {
                            Swal.fire('Error', 'Respons server tidak valid. Hubungi administrator.', 'error');
                            return;
                        }
                        if (d.success) {
                            try { sessionStorage.setItem('booking_success', 'true'); } catch(e) {}
                            Swal.fire({
                                title: 'Reservasi Terkirim!',
                                text: 'Permintaan Anda sedang diproses.',
                                icon: 'success',
                                confirmButtonColor: '#1265A8',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => { window.location.href = '/'; });
                        } else {
                            Swal.fire('Gagal!', d.message || 'Terjadi kesalahan.', 'error');
                        }
                    } else if (xhr.status === 422) {
                        let d;
                        try { d = JSON.parse(xhr.responseText); } catch(e) { d = {}; }
                        const msgs = d.errors
                            ? Object.values(d.errors).flat().join('\n')
                            : (d.message || 'Data tidak valid.');
                        Swal.fire('Validasi Gagal', msgs, 'error');
                    } else if (xhr.status === 0) {
                        if (!state.aborted) {
                            Swal.fire('Koneksi Terputus', 'Tidak dapat terhubung ke server. Periksa internet Anda.', 'error');
                        }
                    } else {
                        Swal.fire('Error ' + xhr.status, 'Terjadi kesalahan server. Coba beberapa saat lagi.', 'error');
                    }
                };

                xhr.open('POST', '{{ route("bookings.store") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');
                fd.append('cf-turnstile-response', this.captchaToken);
                xhr.send(fd);
            },

            // ── Getters ──────────────────────────────────────────────

            get currentFacility() {
                return this.facilities.find(f => f.id == this.selectedFacilityId) || null;
            },

            get maxRoomsFromFacility() {
                const f = this.currentFacility;
                if (!f || f.tipe !== 'asrama') return 999;
                return f.jumlah_kamar || 999;
            },

            get totalOccupants() {
                return this.adults + this.children;
            },

            // FIX: roomsNeeded mempertimbangkan billableChildren yang menempati slot dewasa
            get roomsNeeded() {
                const adultSlots = this.adults + this.billableChildren; // anak≥12 pakai slot dewasa
                const childSlots = this.freeChildren;                   // anak<12 pakai slot anak
                return Math.max(
                    Math.ceil(adultSlots / 2),
                    Math.ceil(childSlots / 2)
                );
            },

            // FIX: maxAdultsAllowed untuk asrama = rooms*2 dikurangi slot yang sudah dipakai billableChildren
            // Untuk aula = max_dewasa dari fasilitas
            get maxAdultsAllowed() {
                const f = this.currentFacility;
                if (!f) return 999;
                if (f.tipe === 'asrama') {
                    // Total slot dewasa = rooms*2; sudah dipakai oleh billableChildren
                    return Math.max(0, (this.rooms * 2) - this.billableChildren);
                }
                return f.max_dewasa || 999;
            },

            get adultSlotOverflow() {
                return (this.adults + this.billableChildren) > (this.rooms * 2);
            },

            get adultSlotShortfall() {
                const totalAdultSlotNeeded = Math.ceil((this.adults + this.billableChildren) / 2);
                return Math.max(0, totalAdultSlotNeeded - this.rooms);
            },

            // FIX: maxChildrenAllowed = slot anak per kamar (independen dari slot dewasa)
            // Slot anak = rooms * 2 (setiap kamar punya 2 slot anak terpisah dari slot dewasa)
            get maxChildrenAllowed() {
                const f = this.currentFacility;
                if (!f || f.tipe !== 'asrama') return 999;
                const facilityChildLimit = f.max_anak || 999;
                return Math.min(facilityChildLimit, this.rooms * 2);
            },

            get billableChildren() {
                return this.childAges.filter(a => a !== '' && a !== null && a !== undefined && parseInt(a) >= 12).length;
            },

            get freeChildren() {
                return this.childAges.filter(a => a !== '' && a !== null && a !== undefined && parseInt(a) < 12).length;
            },

            get totalBillableGuests() {
                return this.adults + this.billableChildren;
            },

            get totalDays() {
                const d = parseInt(this.duration) || 1;
                if (this.packageType === 'mingguan') return d * 7;
                if (this.packageType === 'bulanan')  return d * 30;
                if (this.packageType === 'tahunan')  return d * 365;
                return d;
            },

            // FIX: endDate — gunakan UTC-safe date arithmetic untuk bulanan/tahunan
            // agar tidak terjadi off-by-one akibat DST atau overflow hari
            get endDate() {
                if (!this.selectedDate) return null;
                const start = new Date(this.selectedDate);
                start.setHours(0, 0, 0, 0);
                const d = parseInt(this.duration) || 1;
                let end;

                if (this.packageType === 'bulanan') {
                    // Tambah N bulan, lalu mundur 1 hari
                    end = new Date(start.getFullYear(), start.getMonth() + d, start.getDate());
                    end.setDate(end.getDate() - 1);
                } else if (this.packageType === 'tahunan') {
                    // Tambah N tahun, lalu mundur 1 hari
                    end = new Date(start.getFullYear() + d, start.getMonth(), start.getDate());
                    end.setDate(end.getDate() - 1);
                } else {
                    // harian & mingguan: +totalDays - 1
                    end = new Date(start);
                    end.setDate(start.getDate() + this.totalDays - 1);
                }
                end.setHours(0, 0, 0, 0);
                return end;
            },

            get tarifLabel() {
                const f = this.currentFacility; if (!f) return '';
                const h = parseFloat(f.harga) || 0;
                const b = parseFloat(f.harga_bulanan) || h * 30;
                const fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
                if (this.packageType === 'harian')   return 'Tarif: ' + fmt(h) + ' / hari';
                if (this.packageType === 'mingguan') return 'Tarif: ' + fmt(h * 7) + ' / minggu';
                if (this.packageType === 'bulanan')  return 'Tarif: ' + fmt(b) + ' / bulan';
                if (this.packageType === 'tahunan')  return 'Tarif: ' + fmt(b * 12) + ' / tahun';
                return '';
            },

            get totalPrice() {
                const f = this.currentFacility; if (!f) return 0;
                const h = parseFloat(f.harga) || 0;
                const b = parseFloat(f.harga_bulanan) || h * 30;
                const d = parseInt(this.duration) || 0;
                // FIX: multiplier hanya untuk asrama (per kamar), aula = flat
                const multiplier = f.tipe === 'asrama' ? this.rooms : 1;
                if (this.packageType === 'harian')   return d * h * multiplier;
                if (this.packageType === 'mingguan') return d * h * 7 * multiplier;
                if (this.packageType === 'bulanan')  return d * b * multiplier;
                if (this.packageType === 'tahunan')  return d * b * 12 * multiplier;
                return 0;
            },

            // FIX: hasConflictInRange — tidak menggunakan status 'in-range' (bukan status valid dari getDateStatus)
            // Iterasi setiap hari di range dan cek apakah statusnya bukan 'ready'
            get hasConflictInRange() {
                if (!this.selectedDate || !this.endDate) return false;
                const start = new Date(this.selectedDate); start.setHours(0,0,0,0);
                const end   = new Date(this.endDate);      end.setHours(0,0,0,0);
                let cursor  = new Date(start);
                while (cursor <= end) {
                    const status = this.getDateStatus(new Date(cursor));
                    if (status !== 'ready') return true;
                    cursor.setDate(cursor.getDate() + 1);
                }
                return false;
            },

            // ── Visual helpers ────────────────────────────────────────

            // occupantSlot: slot dewasa (1,2) dan anak (3,4) dihitung linear per kamar
            occupantSlot(roomNumber, slot) {
                if (slot <= 2) {
                    const adultIdx = (roomNumber - 1) * 2 + (slot - 1);
                    return adultIdx < this.adults ? 'adult' : 'empty';
                }
                const childIdx = (roomNumber - 1) * 2 + (slot - 3);
                return childIdx < this.children ? 'child' : 'empty';
            },

            roomLabel(roomNumber) {
                const adultStart = (roomNumber - 1) * 2;
                let a = 0;
                for (let i = adultStart; i < adultStart + 2; i++) {
                    if (i < this.adults) a++;
                }
                const childStart = (roomNumber - 1) * 2;
                let c = 0;
                for (let i = childStart; i < childStart + 2; i++) {
                    if (i < this.children) c++;
                }
                if (a === 0 && c === 0) return 'Kosong';
                const parts = [];
                if (a > 0) parts.push(a + ' Dewasa');
                if (c > 0) parts.push(c + ' Anak');
                return parts.join(' · ');
            },

            // ── Step navigations ──────────────────────────────────────

            submitStep1() {
                this.step1Submitted = true;
                if (!this.packageType) return;
                this.step++;
            },

            submitStep2() {
                this.step2Submitted = true;
                const f = this.currentFacility;
                let ok = true;

                // 1. Durasi
                if (!this.duration || parseInt(this.duration) < 1) {
                    this.step2Errors.duration = true; ok = false;
                } else {
                    this.step2Errors.duration = false;
                }

                // 2. Adults — minimal 1, tidak boleh melebihi kapasitas
                if (!this.adults || parseInt(this.adults) < 1) {
                    this.step2Errors.adults    = true;
                    this.step2Errors.adultsMsg = 'Jumlah tamu minimal 1 orang';
                    ok = false;
                } else if (f?.tipe === 'aula' && this.adults > (f.max_dewasa || 999)) {
                    this.step2Errors.adults    = true;
                    this.step2Errors.adultsMsg = 'Melebihi kapasitas aula (' + f.max_dewasa + ' orang)';
                    ok = false;
                } else if (f?.tipe === 'asrama' && this.adults > this.maxAdultsAllowed) {
                    this.step2Errors.adults    = true;
                    this.step2Errors.adultsMsg = 'Melebihi kapasitas kamar (maks ' + this.maxAdultsAllowed + ' dewasa)';
                    ok = false;
                } else {
                    this.step2Errors.adults = false;
                    this.step2Errors.adultsMsg = '';
                }

                // 3. ChildAges — wajib diisi jika ada anak
                if (this.children > 0) {
                    const anyEmpty = this.childAges.some(a => a === '' || a === null || a === undefined);
                    this.step2Errors.childAges = anyEmpty;
                    if (anyEmpty) ok = false;
                } else {
                    this.step2Errors.childAges = false;
                }

                // 4. Kamar asrama — hanya cek jika adults & childAges sudah valid
                if (f?.tipe === 'asrama') {
                    if (!this.step2Errors.adults && !this.step2Errors.childAges) {
                        const totalAdultSlot    = this.adults + this.billableChildren;
                        const totalChildSlot    = this.freeChildren;
                        const kurangKamarDewasa = totalAdultSlot > this.rooms * 2;
                        const kurangKamarAnak   = totalChildSlot > this.rooms * 2;

                        if (kurangKamarDewasa || kurangKamarAnak) {
                            this.step2Errors.rooms = true;
                            if (kurangKamarDewasa) {
                                const butuh = Math.ceil(totalAdultSlot / 2);
                                this.step2Errors.roomsMsg =
                                    'Kamar kurang! ' + this.adults + ' dewasa + ' + this.billableChildren +
                                    ' anak≥12 = ' + totalAdultSlot + ' slot dewasa → butuh min. ' + butuh +
                                    ' kamar. Tambah ' + (butuh - this.rooms) + ' lagi.';
                            } else {
                                const butuh = Math.ceil(totalChildSlot / 2);
                                this.step2Errors.roomsMsg =
                                    'Kamar kurang untuk anak <12! Butuh min. ' + butuh +
                                    ' kamar. Tambah ' + (butuh - this.rooms) + ' lagi.';
                            }
                            ok = false;
                        } else {
                            this.step2Errors.rooms    = false;
                            this.step2Errors.roomsMsg = '';
                        }
                    } else {
                        this.step2Errors.rooms    = false;
                        this.step2Errors.roomsMsg = '';
                    }
                }

                if (!ok) return;

                this.fetchCalendarData();
                this.step++;
            },

            submitStep3() {
                this.step3Submitted = true;
                if (!this.selectedDate) return;
                if (this.hasConflictInRange) return;
                this.step++;
            },

            prevStep() { if (this.step > 1) this.step--; },

            // ── Increment / Decrement ─────────────────────────────────

            incDuration() {
                const f = this.currentFacility;
                if (this.packageType === 'harian' && f?.max_durasi_harian && this.duration >= f.max_durasi_harian) {
                    Swal.fire('Peringatan', 'Maksimal durasi harian untuk fasilitas ini adalah ' + f.max_durasi_harian + ' hari.', 'warning');
                    return;
                }
                this.duration++;
            },
            decDuration() { if (this.duration > 1) this.duration--; },

            incRooms() {
                if (this.rooms >= this.maxRoomsFromFacility) {
                    Swal.fire('Kapasitas Penuh', 'Fasilitas ini tidak dapat menambah kamar lagi (maks ' + this.maxRoomsFromFacility + ' kamar).', 'warning');
                    return;
                }
                this.rooms++;
                // Bersihkan error kamar jika sekarang sudah cukup
                const totalAdultSlot = this.adults + this.billableChildren;
                if (totalAdultSlot <= this.rooms * 2 && this.freeChildren <= this.rooms * 2) {
                    this.step2Errors.rooms    = false;
                    this.step2Errors.roomsMsg = '';
                }
            },

            // FIX: decRooms — semua logika pengurangan dilakukan di dalam .then(), tidak ada this.rooms-- di luar
            decRooms() {
                if (this.rooms <= 1) return;

                const newRooms = this.rooms - 1;
                const newCap   = newRooms * 2;

                const adultsAfter   = Math.min(this.adults,   newCap);
                const childrenAfter = Math.min(this.children, newCap);

                const adultsRemoved   = this.adults   - adultsAfter;
                const childrenRemoved = this.children - childrenAfter;

                if (adultsRemoved > 0 || childrenRemoved > 0) {
                    const parts = [];
                    if (adultsRemoved   > 0) parts.push('<strong>' + adultsRemoved   + ' dewasa</strong>');
                    if (childrenRemoved > 0) parts.push('<strong>' + childrenRemoved + ' anak</strong>');

                    Swal.fire({
                        title: 'Kurangi Kamar?',
                        html: 'Menghapus kamar ' + this.rooms + ' akan otomatis mengurangi ' + parts.join(' dan ') + ' dari daftar tamu.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Kurangi',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: { popup: 'rounded-[2rem] p-8' }
                    }).then(result => {
                        if (result.isConfirmed) {
                            this.rooms    = newRooms;   // FIX: set rooms dulu
                            this.adults   = adultsAfter;
                            this.children = childrenAfter;
                            this.childAges = this.childAges.slice(0, childrenAfter);
                            this.step2Errors.rooms    = false;
                            this.step2Errors.roomsMsg = '';
                        }
                    });
                    // FIX: TIDAK ada this.rooms-- di sini — sudah dihandle di .then()
                    return;
                }

                // Tidak ada tamu yang perlu dihapus — langsung kurangi
                this.rooms--;
            },

            incAdults() {
                if (this.adults >= this.maxAdultsAllowed) {
                    this.showRoomHint = true;
                    setTimeout(() => { this.showRoomHint = false; }, 4000);
                    return;
                }
                this.adults++;
            },

            // FIX: decAdults — minimum 1, tidak auto-trim anak
            decAdults() {
                if (this.adults > 1) this.adults--;
            },

            incChildren() {
                if (this.children >= this.maxChildrenAllowed) {
                    this.showChildHint = true;
                    setTimeout(() => { this.showChildHint = false; }, 4000);
                    return;
                }
                this.children++;
            },
            decChildren() {
                if (this.children > 0) this.children--;
            },

            // ── Calendar ─────────────────────────────────────────────

            updateDaysInMonth() {
                const lastDay  = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                const startDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                this.daysInMonth = [];
                for (let i = 0; i < startDay; i++) this.daysInMonth.push({ day: null, date: null });
                for (let i = 1; i <= lastDay; i++)  this.daysInMonth.push({ day: i, date: new Date(this.currentYear, this.currentMonth, i) });
            },

            async fetchCalendarData() {
                if (!this.selectedFacilityId) return;
                this.isLoadingCalendar = true;
                try {
                    const res = await fetch('/schedule_booking/data?fasilitas_id=' + this.selectedFacilityId +
                        '&year=' + this.currentYear +
                        '&month=' + (this.currentMonth + 1) +
                        '&t=' + Date.now());
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    this.calendarEvents = await res.json();
                } catch (e) {
                    console.error('Gagal fetch kalender:', e);
                    this.calendarEvents = [];
                } finally {
                    this.isLoadingCalendar = false;
                }
            },

            getDateStatus(date) {
                if (!date) return 'closed';
                const today = new Date(); today.setHours(0,0,0,0);
                const d = new Date(date); d.setHours(0,0,0,0);
                if (d < today) return 'past';
                for (const ev of this.calendarEvents) {
                    const s = new Date(ev.tgl_mulai); s.setHours(0,0,0,0);
                    const e = new Date(ev.tgl_selesai); e.setHours(0,0,0,0);
                    if (d >= s && d <= e) {
                        if (ev.color === 'yellow') return 'pending';
                        if (ev.color === 'blue')   return 'booked';
                        if (ev.color === 'black')  return 'blocked';
                        if (ev.color === 'red')    return 'maintenance';
                    }
                }
                return 'ready';
            },

            getDayInfo(date) {
                if (!date) return '';
                const d = new Date(date); d.setHours(0,0,0,0);
                for (const ev of this.calendarEvents) {
                    const s = new Date(ev.tgl_mulai); s.setHours(0,0,0,0);
                    const e = new Date(ev.tgl_selesai); e.setHours(0,0,0,0);
                    if (d >= s && d <= e) {
                        if (ev.status === 'maintenance') return 'Perbaikan: ' + (ev.reason || 'Maintenance');
                        return ev.status.toUpperCase();
                    }
                }
                return '';
            },

            isInRange(date) {
                if (!this.selectedDate || !date || !this.endDate) return false;
                const d = new Date(date); d.setHours(0,0,0,0);
                const s = new Date(this.selectedDate); s.setHours(0,0,0,0);
                const e = new Date(this.endDate); e.setHours(0,0,0,0);
                return d >= s && d <= e;
            },

            getDayCellClass(item) {
                if (!item.day) return 'bg-white';
                const status  = this.getDateStatus(item.date);
                const inRange = this.isInRange(item.date);
                if (status === 'past')   return 'status-past';
                if (status === 'closed') return 'bg-white status-closed';
                // FIX: cek in-range SEBELUM status lain agar warna range tampil benar
                // Tapi kalau statusnya bukan ready, tampilkan sebagai conflict
                if (inRange) {
                    if (status !== 'ready') return 'status-conflict';
                    // Jika ini adalah tanggal yang tepat sama dengan selectedDate, biarkan di-handle oleh ring class
                    return 'status-in-range';
                }
                if (status === 'pending')     return 'status-pending';
                if (status === 'booked')      return 'status-booked';
                if (status === 'blocked')     return 'status-blocked';
                if (status === 'maintenance') return 'status-maintenance';
                return 'status-ready';
            },

            // FIX: selectDate — jika klik tanggal yang sama, toggle off (deselect)
            selectDate(date) {
                if (!date) return;
                const status = this.getDateStatus(date);
                if (status !== 'ready') return;
                // Toggle: klik tanggal yang sama → deselect
                if (this.selectedDate && date.getTime() === this.selectedDate.getTime()) {
                    this.selectedDate = null;
                    return;
                }
                this.selectedDate = date;
            },

            prevMonth() {
                if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; }
                else { this.currentMonth--; }
            },
            nextMonth() {
                if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; }
                else { this.currentMonth++; }
            },

            get monthName() {
                return new Intl.DateTimeFormat('id-ID', { month: 'long' }).format(new Date(this.currentYear, this.currentMonth));
            },

            formatDisplay(date) {
                if (!date) return '-';
                return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(date);
            },

            formatDateLocal(date) {
                if (!date) return '';
                const d  = new Date(date);
                const y  = d.getFullYear();
                const m  = String(d.getMonth()+1).padStart(2,'0');
                const dd = String(d.getDate()).padStart(2,'0');
                return y+'-'+m+'-'+dd;
            },

            confirmCancel() {
                Swal.fire({
                    title: 'Batal Booking?',
                    text: 'Semua progres pengisian akan hilang.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tidak'
                }).then(r => { if (r.isConfirmed) window.location.href = '/'; });
            },
        }));
    });

    function onRecaptchaSuccess(token) {
        const el = document.querySelector('[x-data]');
        if (el && el._x_dataStack) {
            el._x_dataStack[0].captchaToken = token;
            el._x_dataStack[0].captchaError = false;
        }
    }
    function onRecaptchaExpired() {
        const el = document.querySelector('[x-data]');
        if (el && el._x_dataStack) {
            el._x_dataStack[0].captchaToken = '';
            el._x_dataStack[0].captchaError = true;
        }
    }
</script>
</body>
</html>