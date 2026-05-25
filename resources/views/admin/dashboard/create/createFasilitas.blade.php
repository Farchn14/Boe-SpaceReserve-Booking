<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Add Fasilitas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    {{-- Background blurs --}}
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 blur-[120px] rounded-full opacity-50"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-indigo-100 blur-[120px] rounded-full opacity-50"></div>
    </div>

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-start" x-data="facilityForm()">
        <div class="w-full max-w-5xl space-y-8">

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- SECTION 1: PARENT FACILITY (GLOBAL DATA)              --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.08)] border border-white overflow-hidden transition-all duration-500 hover:shadow-blue-200/40">

                {{-- Header --}}
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
                        <button type="button" @click="tipe = 'asrama'" :class="tipe === 'asrama' ? 'bg-[#1d6fa5] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Asrama</button>
                        <button type="button" @click="tipe = 'aula'" :class="tipe === 'aula' ? 'bg-[#1d6fa5] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Aula</button>
                    </div>
                </div>

                {{-- Global Fields --}}
                <div class="p-8 lg:p-12 pt-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        {{-- Left Column --}}
                        <div class="space-y-6">
                            <div class="group">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Fasilitas</label>
                                <input type="text" x-model="nama" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold" required>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Utama</label>
                                <textarea x-model="deskripsi" rows="4" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm resize-none font-medium leading-relaxed" required></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jam Operasional</label>
                                    <input type="text" x-model="jam_operasional" placeholder="08.00 - 22.00" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Max Durasi (Hari)</label>
                                    <input type="number" x-model="max_durasi_harian" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold">
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Thumbnail + Number of Types --}}
                        <div class="space-y-6">
                            <div class="w-full">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Thumbnail Cards</label>
                                <div class="relative overflow-hidden rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1d6fa5] transition-all duration-500 h-64 flex items-center justify-center group/drop cursor-pointer" :class="thumbnailPreview ? 'border-solid border-[#1d6fa5]' : ''">
                                    <img :src="thumbnailPreview" class="absolute inset-0 w-full h-full object-cover z-10" x-show="thumbnailPreview">
                                    <div class="relative z-20 flex flex-col items-center" x-show="!thumbnailPreview">
                                        <div class="p-4 bg-white/90 backdrop-blur rounded-2xl shadow-lg mb-2 transform group-hover/drop:scale-110 transition-all duration-500">
                                            <svg class="w-6 h-6 text-[#1d6fa5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <p class="text-[10px] font-black uppercase tracking-[0.1em] text-slate-500">Pilih Foto Utama</p>
                                    </div>
                                    <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-30" @change="handleThumbnail($event)">
                                </div>
                            </div>

                            {{-- Number of Room Types --}}
                            <div class="group">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jumlah Tipe Kamar</label>
                                <div class="flex items-center gap-4">
                                    <button type="button" @click="removeRoomType()" class="w-12 h-12 flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white hover:border-red-300 hover:bg-red-50 transition-all text-xl font-black text-slate-400 hover:text-red-500">-</button>
                                    <span class="text-3xl font-black text-[#1d6fa5] min-w-[3rem] text-center" x-text="roomTypes.length"></span>
                                    <button type="button" @click="addRoomType()" class="w-12 h-12 flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white hover:border-[#1d6fa5] hover:bg-blue-50 transition-all text-xl font-black text-slate-400 hover:text-[#1d6fa5]">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- SECTION 2: ROOM TYPES SLIDER                          --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.08)] border border-white overflow-hidden transition-all duration-500 hover:shadow-blue-200/40" x-show="roomTypes.length > 0" x-transition>

                {{-- Slide Navigation --}}
                <div class="flex items-center justify-between px-10 pt-8 pb-4">
                    <button type="button" @click="prevSlide()" :disabled="currentSlide === 0" class="w-10 h-10 flex items-center justify-center rounded-xl border-2 transition-all duration-300 disabled:opacity-30 disabled:cursor-not-allowed" :class="currentSlide === 0 ? 'border-slate-200 text-slate-300' : 'border-[#1d6fa5] text-[#1d6fa5] hover:bg-[#1d6fa5] hover:text-white'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="flex items-center gap-3">
                        <template x-for="(rt, idx) in roomTypes" :key="idx">
                            <button type="button" @click="currentSlide = idx" class="w-8 h-8 rounded-lg text-xs font-black transition-all duration-300" :class="currentSlide === idx ? 'bg-[#1d6fa5] text-white shadow-lg scale-110' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" x-text="idx + 1"></button>
                        </template>
                    </div>
                    <button type="button" @click="nextSlide()" :disabled="currentSlide >= roomTypes.length - 1" class="w-10 h-10 flex items-center justify-center rounded-xl border-2 transition-all duration-300 disabled:opacity-30 disabled:cursor-not-allowed" :class="currentSlide >= roomTypes.length - 1 ? 'border-slate-200 text-slate-300' : 'border-[#1d6fa5] text-[#1d6fa5] hover:bg-[#1d6fa5] hover:text-white'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <div class="px-8 pb-2 text-center">
                    <h3 class="text-lg font-black text-slate-700 uppercase tracking-widest">
                        Tipe Kamar <span class="text-[#1d6fa5]" x-text="currentSlide + 1"></span> / <span x-text="roomTypes.length"></span>
                    </h3>
                    <div class="h-0.5 w-8 bg-gradient-to-r from-[#1d6fa5] to-blue-400 mx-auto mt-2 rounded-full"></div>
                </div>

                {{-- Slide Content --}}
                <template x-for="(rt, idx) in roomTypes" :key="idx">
                    <div x-show="currentSlide === idx" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="p-8 lg:p-12 pt-4">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                            {{-- Left Column --}}
                            <div class="space-y-6">
                                {{-- 3-Slot Photo Gallery --}}
                                <div>
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Foto Tipe Kamar (3 Foto, Max 2MB)</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <template x-for="gi in [0, 1, 2]" :key="gi">
                                            <div class="relative overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1d6fa5] transition-all duration-500 h-32 flex items-center justify-center group/gal cursor-pointer">
                                                <img :src="rt.galleryPreviews[gi]" class="absolute inset-0 w-full h-full object-cover z-10" x-show="rt.galleryPreviews[gi]">
                                                <div class="relative z-20 flex flex-col items-center" x-show="!rt.galleryPreviews[gi]">
                                                    <svg class="w-5 h-5 text-slate-300 group-hover/gal:text-[#1d6fa5] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                    <span class="text-[8px] font-bold text-slate-300 mt-1" x-text="gi === 0 ? 'THUMBNAIL' : 'FOTO ' + (gi + 1)"></span>
                                                </div>
                                                <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-30" @change="handleRoomGallery($event, idx, gi)">
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Tipe Kamar</label>
                                    <input type="text" x-model="rt.nama" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm font-semibold" placeholder="Contoh: Deluxe Room" required>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Tipe</label>
                                    <textarea x-model="rt.deskripsi" rows="3" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all duration-300 shadow-sm resize-none font-medium leading-relaxed" placeholder="Deskripsi spesifik untuk tipe kamar ini..."></textarea>
                                </div>

                                {{-- Max Rooms with +/- and manual input --}}
                                <div class="group">
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Max Rooms (Stok Unit)</label>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="rt.stok = Math.max(1, rt.stok - 1); generateRoomNumbers(idx)" class="w-12 h-12 flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white hover:border-red-300 hover:bg-red-50 transition-all text-xl font-black text-slate-400 hover:text-red-500">-</button>
                                        <input type="number" x-model.number="rt.stok" min="1" @input="generateRoomNumbers(idx)" class="flex-1 px-6 py-4 bg-white border border-slate-200 rounded-2xl text-center font-bold text-lg focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all" required>
                                        <button type="button" @click="rt.stok++; generateRoomNumbers(idx)" class="w-12 h-12 flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white hover:border-[#1d6fa5] hover:bg-blue-50 transition-all text-xl font-black text-slate-400 hover:text-[#1d6fa5]">+</button>
                                    </div>
                                </div>

                                {{-- Capacity: Adult & Child with +/- --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kapasitas Dewasa</label>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="rt.max_dewasa = Math.max(0, rt.max_dewasa - 1)" class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-slate-200 bg-white hover:border-red-300 hover:bg-red-50 transition-all text-lg font-black text-slate-400 hover:text-red-500">-</button>
                                            <input type="number" x-model.number="rt.max_dewasa" min="0" class="flex-1 px-4 py-3 bg-white border border-slate-200 rounded-xl text-center font-bold focus:ring-2 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all">
                                            <button type="button" @click="rt.max_dewasa++" class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-slate-200 bg-white hover:border-[#1d6fa5] hover:bg-blue-50 transition-all text-lg font-black text-slate-400 hover:text-[#1d6fa5]">+</button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kapasitas Anak</label>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="rt.max_anak = Math.max(0, rt.max_anak - 1)" class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-slate-200 bg-white hover:border-red-300 hover:bg-red-50 transition-all text-lg font-black text-slate-400 hover:text-red-500">-</button>
                                            <input type="number" x-model.number="rt.max_anak" min="0" class="flex-1 px-4 py-3 bg-white border border-slate-200 rounded-xl text-center font-bold focus:ring-2 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none transition-all">
                                            <button type="button" @click="rt.max_anak++" class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-slate-200 bg-white hover:border-[#1d6fa5] hover:bg-blue-50 transition-all text-lg font-black text-slate-400 hover:text-[#1d6fa5]">+</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Labels / Features (per room type) --}}
                                <div class="group">
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Labels / Fitur</label>
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <template x-for="label in labelOptions[tipe]" :key="label">
                                            <label class="cursor-pointer">
                                                <input type="checkbox" :value="label" x-model="rt.selectedLabels" class="hidden">
                                                <span :class="rt.selectedLabels.includes(label) ? 'bg-[#1d6fa5] text-white border-[#1d6fa5]' : 'bg-white text-slate-400 border-slate-200'" class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all duration-300 block" x-text="label"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="rt.customLabel" @keydown.enter.prevent="addCustomLabel(idx)" placeholder="Tambah fitur custom..." class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold outline-none focus:border-[#1d6fa5] transition-all">
                                        <button type="button" @click="addCustomLabel(idx)" class="px-4 py-2 bg-[#1d6fa5] text-white rounded-xl hover:bg-slate-800 transition-all font-black text-sm">+</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Pricing + Room Numbers --}}
                            <div class="space-y-6">
                                {{-- 4-Tier Pricing --}}
                                <div>
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Paket Harga</label>
                                    <div class="space-y-3">
                                        <template x-for="tier in ['harian', 'mingguan', 'bulanan', 'tahunan']" :key="tier">
                                            <div class="relative">
                                                <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs">Rp</span>
                                                <input type="text"
                                                    :placeholder="tier.charAt(0).toUpperCase() + tier.slice(1)"
                                                    :value="formatDot(rt['harga_' + tier])"
                                                    @input="rt['harga_' + tier] = parseDot($event.target.value); $event.target.value = formatDot(rt['harga_' + tier])"
                                                    class="w-full pl-12 pr-6 py-3.5 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold transition-all text-sm"
                                                    :required="tier === 'harian'">
                                                <span class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase tracking-widest text-slate-300" x-text="'/ ' + tier"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Dynamic Room Numbers --}}
                                <div x-show="rt.stok > 0" x-transition class="space-y-3">
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nomor Kamar</label>
                                    <div class="max-h-64 overflow-y-auto space-y-2 pr-2 scrollbar-thin">
                                        <template x-for="(room, ri) in rt.nomor_kamar" :key="ri">
                                            <div class="flex items-center gap-3" style="animation: fadeIn 0.3s ease forwards">
                                                <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-xs font-black text-[#1d6fa5]" x-text="ri + 1"></span>
                                                <input type="text" x-model="rt.nomor_kamar[ri]" class="flex-1 px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-semibold text-sm transition-all" :placeholder="rt.nama ? rt.nama + ' - ' + (ri + 1) : 'Kamar ' + (ri + 1)">
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- SECTION 3: ACTION BUTTONS                             --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div class="flex flex-col sm:flex-row-reverse gap-4">
                <button type="button" @click="confirmSubmit()" id="btnSimpan"
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

    {{-- Loading Overlay --}}
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
        const MAX_FILE_SIZE = 2 * 1024 * 1024;

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function facilityForm() {
            return {
                tipe: 'asrama',
                nama: '',
                deskripsi: '',
                jam_operasional: '',
                max_durasi_harian: '',
                thumbnailPreview: null,
                thumbnailFile: null,

                labelOptions: {
                    asrama: ['Shower', 'AC', 'Wifi', 'Parkir', 'TV', 'Lemari'],
                    aula: ['Wifi', 'Sound System', 'AC', 'Kursi', 'Meja', 'Panggung', 'Proyektor']
                },

                currentSlide: 0,
                roomTypes: [this.createRoomType()],

                init() {
                    this.roomTypes = [this.createRoomType()];
                },

                createRoomType() {
                    return {
                        nama: '',
                        deskripsi: '',
                        max_dewasa: 0,
                        max_anak: 0,
                        harga_harian: '',
                        harga_mingguan: '',
                        harga_bulanan: '',
                        harga_tahunan: '',
                        stok: 1,
                        nomor_kamar: [''],
                        galleryPreviews: [null, null, null],
                        galleryFiles: [null, null, null],
                        selectedLabels: [],
                        customLabel: '',
                    };
                },

                addRoomType() {
                    this.roomTypes.push(this.createRoomType());
                    this.currentSlide = this.roomTypes.length - 1;
                },

                removeRoomType() {
                    if (this.roomTypes.length <= 1) return;
                    this.roomTypes.pop();
                    if (this.currentSlide >= this.roomTypes.length) {
                        this.currentSlide = this.roomTypes.length - 1;
                    }
                },

                prevSlide() {
                    if (this.currentSlide > 0) this.currentSlide--;
                },

                nextSlide() {
                    if (this.currentSlide < this.roomTypes.length - 1) this.currentSlide++;
                },

                addCustomLabel(idx) {
                    const rt = this.roomTypes[idx];
                    if (rt.customLabel.trim() !== '') {
                        const label = rt.customLabel.trim();
                        if (!this.labelOptions[this.tipe].includes(label)) {
                            this.labelOptions[this.tipe].push(label);
                        }
                        if (!rt.selectedLabels.includes(label)) {
                            rt.selectedLabels.push(label);
                        }
                        rt.customLabel = '';
                    }
                },

                formatDot(value) {
                    if (value === '' || value === null || value === undefined) return '';
                    let num = String(value).replace(/\D/g, '');
                    if (num === '') return '';
                    return new Intl.NumberFormat('id-ID').format(num);
                },

                parseDot(value) {
                    return String(value).replace(/\D/g, '');
                },

                handleThumbnail(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (file.size > MAX_FILE_SIZE) {
                        Swal.fire({ title: 'File Terlalu Besar', text: `Ukuran file (${formatBytes(file.size)}) melebihi batas 2MB.`, icon: 'warning', confirmButtonColor: '#1d6fa5' });
                        event.target.value = '';
                        return;
                    }
                    this.thumbnailFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => this.thumbnailPreview = e.target.result;
                    reader.readAsDataURL(file);
                },

                handleRoomGallery(event, rtIdx, galIdx) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (file.size > MAX_FILE_SIZE) {
                        Swal.fire({ title: 'File Terlalu Besar', text: `Gambar galeri (${formatBytes(file.size)}) melebihi batas 2MB.`, icon: 'warning', confirmButtonColor: '#1d6fa5' });
                        event.target.value = '';
                        return;
                    }
                    this.roomTypes[rtIdx].galleryFiles[galIdx] = file;
                    const reader = new FileReader();
                    reader.onload = (e) => this.roomTypes[rtIdx].galleryPreviews[galIdx] = e.target.result;
                    reader.readAsDataURL(file);
                },

                generateRoomNumbers(idx) {
                    const rt = this.roomTypes[idx];
                    const stok = parseInt(rt.stok) || 0;
                    const old = rt.nomor_kamar || [];
                    const arr = [];
                    for (let i = 0; i < stok; i++) {
                        arr.push(old[i] || '');
                    }
                    rt.nomor_kamar = arr;
                },

                confirmSubmit() {
                    if (!this.nama.trim()) {
                        Swal.fire({ title: 'Nama Wajib Diisi', icon: 'warning', confirmButtonColor: '#1d6fa5' });
                        return;
                    }
                    if (!this.deskripsi.trim()) {
                        Swal.fire({ title: 'Deskripsi Wajib Diisi', icon: 'warning', confirmButtonColor: '#1d6fa5' });
                        return;
                    }
                    if (!this.thumbnailFile) {
                        Swal.fire({ title: 'Thumbnail Wajib Diisi', icon: 'warning', confirmButtonColor: '#1d6fa5' });
                        return;
                    }
                    for (let i = 0; i < this.roomTypes.length; i++) {
                        if (!this.roomTypes[i].nama.trim()) {
                            Swal.fire({ title: `Nama Tipe Kamar ${i + 1} Wajib Diisi`, icon: 'warning', confirmButtonColor: '#1d6fa5' });
                            this.currentSlide = i;
                            return;
                        }
                        if (!this.roomTypes[i].harga_harian) {
                            Swal.fire({ title: `Harga Harian Tipe ${i + 1} Wajib Diisi`, icon: 'warning', confirmButtonColor: '#1d6fa5' });
                            this.currentSlide = i;
                            return;
                        }
                    }

                    Swal.fire({
                        title: 'Simpan Data?',
                        text: `Fasilitas "${this.nama}" dengan ${this.roomTypes.length} tipe kamar akan disimpan.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#1d6fa5',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: { popup: 'rounded-[2.5rem]', confirmButton: 'rounded-2xl px-8 py-3 font-bold', cancelButton: 'rounded-2xl px-8 py-3 font-bold' }
                    }).then((result) => {
                        if (result.isConfirmed) this.submitForm();
                    });
                },

                submitForm() {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('nama', this.nama);
                    formData.append('tipe', this.tipe);
                    formData.append('deskripsi', this.deskripsi);
                    formData.append('jam_operasional', this.jam_operasional || '');
                    formData.append('max_durasi_harian', this.max_durasi_harian || '');

                    if (this.thumbnailFile) {
                        formData.append('image', this.thumbnailFile);
                    }

                    this.roomTypes.forEach((rt, idx) => {
                        formData.append(`room_types[${idx}][nama]`, rt.nama);
                        formData.append(`room_types[${idx}][deskripsi]`, rt.deskripsi || '');
                        formData.append(`room_types[${idx}][max_dewasa]`, rt.max_dewasa);
                        formData.append(`room_types[${idx}][max_anak]`, rt.max_anak);
                        formData.append(`room_types[${idx}][harga_harian]`, rt.harga_harian || 0);
                        formData.append(`room_types[${idx}][harga_mingguan]`, rt.harga_mingguan || '');
                        formData.append(`room_types[${idx}][harga_bulanan]`, rt.harga_bulanan || '');
                        formData.append(`room_types[${idx}][harga_tahunan]`, rt.harga_tahunan || '');
                        formData.append(`room_types[${idx}][stok]`, rt.stok);

                        rt.selectedLabels.forEach((l, li) => {
                            formData.append(`room_types[${idx}][labels][${li}]`, l);
                        });

                        rt.nomor_kamar.forEach((n, ri) => {
                            formData.append(`room_types[${idx}][nomor_kamar][${ri}]`, n || (rt.nama + ' - ' + (ri + 1)));
                        });

                        rt.galleryFiles.forEach((file, gi) => {
                            if (file) formData.append(`room_types[${idx}][gallery][${gi}]`, file);
                        });
                    });

                    const overlay = document.getElementById('loadingOverlay');
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');

                    fetch('/admin/fasilitas/store', {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(async response => {
                        const contentType = response.headers.get("content-type");
                        let data = null;
                        if (contentType && contentType.includes("application/json")) {
                            data = await response.json();
                        }
                        if (!response.ok) {
                            if (data && data.message) throw new Error(data.message);
                            throw new Error(`Server error: ${response.status}`);
                        }
                        return data;
                    })
                    .then(data => {
                        overlay.classList.add('hidden');
                        Swal.fire({
                            title: 'Berhasil!',
                            text: typeof data.success === 'string' ? data.success : 'Data fasilitas berhasil disimpan!',
                            icon: 'success',
                            confirmButtonColor: '#1265A8',
                            customClass: { popup: 'rounded-[2.5rem] p-8' }
                        }).then(() => {
                            window.location.href = "/admin/dashboard/dataFasilitas";
                        });
                    })
                    .catch(error => {
                        overlay.classList.add('hidden');
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'Terjadi kesalahan sistem.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444',
                            customClass: { popup: 'rounded-[1.5rem] p-8' }
                        });
                    });
                }
            };
        }

        // Cancel button handler
        document.addEventListener('DOMContentLoaded', () => {
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
                        customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            textContent.classList.add('opacity-0', 'scale-95');
                            loader.classList.remove('invisible', 'opacity-0');
                            btnBatalVenue.classList.add('pointer-events-none');
                            setTimeout(() => { window.location.href = previousPage; }, 700);
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
