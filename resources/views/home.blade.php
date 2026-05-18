<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        
        .smooth-transition {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes scroll-dot {
            0% { opacity: 1; 
                transform: translateY(0); 
            }
            100% { opacity: 0; 
                transform: translateY(15px); 
            }
        }

        .animate-scroll-dot {
            animation: scroll-dot 1.5s infinite;
        }

        /* Menghilangkan scrollbar pada AOS jika ada */
        [data-aos] {
            pointer-events: none;
        }
        
        [data-aos].aos-animate {
            pointer-events: auto;
        }
        
        /* Efek Smooth Line Clamp untuk deskripsi */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }

        @keyframes scroll-dot {
            0% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(10px); opacity: 0.5; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .animate-scroll-dot {
            animation: scroll-dot 2s infinite ease-in-out;
        }

        /* Class tambahan untuk trigger menghilang */
        .hide-indicator {
            opacity: 0 !important;
            transform: translateY(20px) !important;
            pointer-events: none;
        }
                    
        /* Animasi Pulse Halus untuk elemen latar */
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.05); }
        }

        .animate-pulse-slow {
            animation: pulse-slow 6s infinite ease-in-out;
        }
    </style>
    <title>BOE-Space Reserve</title>
</head>
<body class="bg-[#FFFFFF]">
    <x-layout.navbar />
    {{-- Hero Section --}}
    <section class="relative w-full h-screen bg-[#FFFFFF] overflow-hidden flex flex-col items-center justify-center text-center px-6">
        {{-- Background Image --}}
        <div data-aos="zoom-out" data-aos-duration="2000" class="absolute inset-0 z-0">
            <img src="/image/pictures/bgImage-boe.svg" alt="bg-section" class="w-full h-full object-cover opacity-90">
            
            {{-- Overlay Atas (Gelap) --}}
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-transparent"></div>
            
            {{-- Overlay Bawah (Fade Putih yang diperbaiki agar tidak menutupi indikator) --}}
            <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-white via-white/40 to-transparent z-[1]"></div>
        </div>
        
        {{-- Content Container --}}
        <div class="relative z-10 flex flex-col items-center">
            <h1 data-aos="fade-down" data-aos-delay="200" class="text-white drop-shadow-[0_5px_15px_rgba(0,0,0,0.5)] text-[45px] lg:text-[72px] font-extrabold leading-[1.05] uppercase tracking-tighter mb-6 font-sans">
                <span class="block">SELAMAT DATANG</span>
                <span class="block mt-1">DI <span class="text-blue-400 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-blue-200">BOE-SPACE RESERVE</span></span>
            </h1>

            <p data-aos="fade-up" data-aos-delay="400" class="text-white/95 text-[18px] lg:text-[22px] drop-shadow-lg font-medium max-w-4xl leading-relaxed mb-10">
                Balai Besar Pengembangan Penjaminan Mutu Pendidikan Vokasi<br class="hidden md:block">
                Bidang Otomotif dan Elektronika Malang
            </p>

            <div data-aos="zoom-in" data-aos-delay="600" class="flex flex-col sm:flex-row gap-5">
                <a href="#booking" id="bookingBtn" class="inline-flex items-center justify-center bg-[#276AD7] text-white px-12 py-4 rounded-2xl font-bold text-xl hover:bg-black transition-all duration-300 shadow-[0_10px_40px_rgba(39,106,215,0.6)] transform hover:-translate-y-1.5 active:scale-95 min-w-[240px]">
                    Booking Now
                </a>

                <a href="/schedule_booking" class="inline-flex items-center justify-center bg-white/10 backdrop-blur-md border border-white/30 text-white px-12 py-4 rounded-2xl font-bold text-xl hover:bg-white hover:text-black transition-all duration-300 transform hover:-translate-y-1.5 active:scale-95 min-w-[240px]">
                    Check Schedule
                </a>
            </div>
        </div>

        {{-- Modern Scroll Indicator --}}
        <div id="scroll-indicator" 
            class="absolute bottom-8 left-0 right-0 flex flex-col items-center gap-3 z-20 cursor-pointer group transition-all duration-700 ease-in-out opacity-100 translate-y-0"> 
            
            <span class="text-slate-500 text-[10px] font-bold tracking-[0.4em] uppercase group-hover:text-blue-600 transition-colors duration-300">
                Scroll
            </span>

            {{-- Mouse Icon --}}
            <div class="relative w-6 h-10 border-2 border-blue-600/30 rounded-full flex justify-center p-1 group-hover:border-blue-500 transition-colors duration-300">
                {{-- Dot Animasi --}}
                <div class="w-1 h-2 bg-blue-600 rounded-full animate-scroll-dot group-hover:bg-blue-500"></div>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section id="about" class="relative overflow-hidden py-32 px-6 lg:px-20">
        {{-- Abstract Background Elements --}}
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-100/40 rounded-full blur-[120px] -z-0"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-blue-50 rounded-full blur-[100px] opacity-60 -z-0"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            {{-- Main Content Grid --}}
            <div class="flex flex-col lg:flex-row items-center gap-20 mb-32">
                
                {{-- Image Side with Modern Layered & Line Effect --}}
                <div class="relative w-full lg:w-1/2 px-4" data-aos="fade-left" data-aos-duration="1500">
                    <div class="relative max-w-[550px] mx-auto lg:ml-auto group">
                        {{-- Garis Horizontal Atas - Menghubungkan ke luar --}}
                        <div class="absolute -top-10 right-0 w-32 h-px bg-slate-200 -z-10 group-hover:w-48 transition-all duration-500"></div>
                        {{-- Garis Vertikal Kiri - Pembingkai samping --}}
                        <div class="absolute top-0 -left-10 h-full w-px bg-slate-200 -z-10 group-hover:h-[110%] transition-all duration-500"></div>
                        {{-- Garis Aksen Pendek --}}
                        <div class="absolute top-1/2 -left-14 w-8 h-1 bg-[#1d6fa5] rounded-full -z-10 opacity-70"></div>
                        {{-- ========================================= --}}

                        {{-- Decorative Background Elements --}}
                        <div class="absolute -top-6 -left-6 w-24 h-24 bg-blue-50 rounded-full -z-10 animate-pulse-slow"></div>
                        {{-- Bentuk Modern dengan Border Tebal --}}
                        <div class="absolute -bottom-10 -right-10 w-40 h-40 border-[15px] border-slate-50 rounded-[4rem] -z-10 shadow-inner"></div>

                        {{-- Main Image Container --}}
                        <div class="relative h-[450px] md:h-[500px] overflow-hidden rounded-[3.5rem] shadow-[0_40px_80px_-20px_rgba(29,111,165,0.2)] bg-slate-100 border border-slate-50">
                            {{-- Hover Zoom Effect --}}
                            <img src="/image/pictures/imgAbout.svg" alt="Gedung BOE" 
                                class="w-full h-full object-cover transition-transform duration-[1.5s] ease-in-out group-hover:scale-125">
                            
                            {{-- Subtle Gradient Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-tr from-[#1d6fa5]/25 via-transparent to-transparent opacity-70"></div>

                            {{-- Location Badge --}}
                            <div class="absolute top-6 right-6 bg-white/70 backdrop-blur-lg border border-white/40 text-slate-800 text-[10px] font-black px-5 py-2.5 rounded-full uppercase tracking-[0.2em] shadow-sm flex items-center gap-2">
                                <span class="text-xs">📍</span> Malang, Indonesia
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Content Side --}}
                <div class="w-full lg:w-1/2" data-aos="fade-left" data-aos-duration="1000">
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-[#1d6fa5]/5 rounded-full mb-6">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#1d6fa5] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1d6fa5]"></span>
                        </span>
                        <span class="text-[#1d6fa5] font-bold uppercase tracking-widest text-[10px]">Discover Our Vision</span>
                    </div>

                    <h2 class="text-6xl lg:text-7xl font-black text-slate-900 tracking-tighter leading-[0.9] mb-8">
                        Elevating 
                        <span class="bg-gradient-to-r from-[#1d6fa5] to-blue-400 bg-clip-text text-transparent">
                            About
                        </span> 
                        Space.
                    </h2>
                    
                    <div class="space-y-8">
                        <p class="text-xl leading-relaxed text-slate-600 font-medium border-l-4 border-[#1d6fa5] pl-6">
                            BBPPMPV BOE Malang tidak hanya berperan sebagai pusat pengembangan pendidikan vokasi, 
                            tetapi juga menyediakan <span class="text-slate-900 font-bold italic decoration-[#1d6fa5] underline-offset-4 decoration-2">layanan premium booking</span> untuk kebutuhan profesional Anda.
                        </p>
                        <p class="text-lg leading-relaxed text-slate-500 font-light">
                            Layanan ini dirancang untuk mendukung ekosistem kolaborasi melalui seminar, pelatihan, dan rapat formal. Kami berkomitmen memberikan pengalaman penggunaan ruang yang <span class="font-semibold text-slate-700">representatif, efisien, dan berstandar internasional.</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bento Grid Footer: Developed By --}}
            <div class="pt-20 border-t border-slate-200" data-aos="fade-up">
                <div class="flex flex-col lg:flex-row gap-12">
                    <div class="lg:w-1/4">
                        <div class="sticky top-24">
                            <h3 class="text-slate-900 font-black uppercase tracking-tighter text-2xl mb-4">The Creative<br><span class="text-[#1d6fa5]">Minds.</span></h3>
                            <p class="text-slate-400 text-sm leading-relaxed mb-6">Sistem ini dikembangkan oleh talenta muda melalui program kolaborasi industri dan pendidikan vokasi.</p>
                            <div class="flex gap-2">
                                <div class="w-8 h-1 bg-[#1d6fa5] rounded-full"></div>
                                <div class="w-2 h-1 bg-slate-200 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-3/4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @php
                            $devs = [
                                ['name' => 'Mohammad Dirgo Marchellino', 'school' => 'SMKN 1 KRAKSAAN', 'role' => 'Backend Developer'],
                                ['name' => 'Moh. Romsi Ramadani', 'school' => 'SMKN 1 KRAKSAAN', 'role' => 'UI/UX Designer'],
                                ['name' => 'Ardan Ramadhan P.H', 'school' => 'SMKN 1 PURWOSARI', 'role' => 'Frontend Developer'],
                                ['name' => 'Syafiq Labib', 'school' => 'SMKN 1 PURWOSARI', 'role' => 'UI/UX Designer'],
                                ['name' => 'Muhammad Farchan', 'school' => 'SMKN 8 MALANG', 'role' => 'Backend Developer'],
                                ['name' => 'Feriska Agustina Fitria', 'school' => 'SMKN 8 MALANG', 'role' => 'UI/UX Designer'],
                            ];
                        @endphp

                        @foreach($devs as $dev)
                        <div class="group relative p-6 bg-white border border-slate-100 rounded-[2rem] transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] hover:border-[#1d6fa5]/20">
                            <div class="flex flex-col h-full">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-slate-50 text-[#1d6fa5] rounded-2xl flex items-center justify-center group-hover:bg-[#1d6fa5] group-hover:text-white transition-all duration-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        {{-- Teks Role Tetap Terlihat (Tanpa Hover) --}}
                                        <p class="text-xs font-bold text-[#1d6fa5] mb-1 uppercase tracking-tighter">{{ $dev['role'] }}</p>
                                        <div class="h-px bg-slate-100 w-full"></div>
                                    </div>
                                </div>
                                <h4 class="text-[14px] font-black text-slate-800 uppercase leading-tight mb-1">{{ $dev['name'] }}</h4>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $dev['school'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Booking Section --}}
    <section id="booking" class="py-24 px-6 min-h-screen relative overflow-hidden">
        {{-- Decorative Background Pattern --}}
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#1d6fa5 1px, transparent 1px); background-size: 40px 40px;"></div>

        {{-- Header Section --}}
        <div class="max-w-6xl mx-auto mb-20 text-center relative" data-aos="fade-down">
            <span class="hidden md:block text-blue-600/5 text-9xl font-black absolute -top-16 left-1/2 -translate-x-1/2 select-none tracking-[0.2em]">RESERVE</span>
            <h1 class="relative text-5xl md:text-6xl font-black text-gray-900 uppercase tracking-tighter">
                Boo<span class="text-[#1d6fa5]">king</span>
            </h1>
            <div class="h-2 w-24 bg-gradient-to-r from-[#1d6fa5] to-blue-400 mx-auto mt-4 rounded-full shadow-lg shadow-blue-100"></div>
            <p class="mt-6 text-gray-500 font-medium max-w-lg mx-auto leading-relaxed">
                Pilih fasilitas terbaik kami untuk menunjang kenyamanan kegiatan Anda di BBPPMPV BOE Malang.
            </p>
        </div>

        {{-- Facilities Grid --}}
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                
                {{-- Loop langsung dari database --}}
                @foreach($facilities as $index => $item)
                <div data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}" 
                    class="group bg-white rounded-[2.5rem] p-4 shadow-[0_15px_40px_rgba(0,0,0,0.03)] hover:shadow-[0_30px_60px_rgba(29,111,165,0.12)] transition-all duration-500 flex flex-col border border-transparent hover:border-blue-100/50">
                    
                    {{-- Card Image --}}
                    <div class="relative h-64 w-full overflow-hidden rounded-[2rem]">
                        <div class="absolute inset-0 bg-[#1d6fa5]/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10"></div>
                        
                        {{-- Gunakan asset storage untuk gambar --}}
                        <img src="{{ asset('storage/fasilitas/' . $item->image) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                        
                        {{-- Price Tag --}}
                        <div class="absolute top-5 right-5 z-20">
                            <div class="bg-white/90 backdrop-blur-md px-4 py-2 rounded-2xl shadow-xl">
                                <p class="text-[8px] font-bold text-blue-600 uppercase tracking-[0.2em] leading-none mb-1">Price Range</p>
                                {{-- Format harga otomatis --}}
                                <p class="text-sm font-black text-gray-900 leading-none">{{ $item->harga_thumbnail }}</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="px-3 py-6 flex flex-col flex-grow">
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight mb-3">
                            <span class="text-[#1d6fa5]">{{ $item->nama }}</span>
                        </h2>
                        
                        <p class="text-gray-500 text-sm leading-relaxed mb-8 line-clamp-2">
                            {{ $item->deskripsi }}
                        </p>

                        {{-- Actions --}}
                        <div class="mt-auto flex items-center gap-3">
                            {{-- Kirim data ke modal detail --}}
                            <button 
                                onclick='openDescription(
                                    {{ json_encode($item->nama) }}, 
                                    {{ json_encode($item->deskripsi) }}, 
                                    {{ json_encode(asset("storage/fasilitas/" . $item->image)) }},
                                    {{ json_encode($item->detail) }},
                                    @json($item->gallery),
                                    @json($item->labels),
                                    {{ json_encode($item->tipe) }},
                                    {{ json_encode($item->max_dewasa) }},
                                    {{ json_encode($item->max_anak) }},
                                    {{ json_encode($item->jam_operasional) }}
                                )'
                                class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-600 py-4 rounded-2xl font-bold text-xs transition-all duration-200 active:scale-95 border border-gray-100"
                            >
                                Lihat Detail
                            </button>
                            
                            <a href="{{ route('formBooking', ['id' => $item->id]) }}" 
                                class="relative flex-[1.2] bg-[#1d6fa5] hover:bg-slate-900 text-white py-4 rounded-2xl font-bold text-xs transition-all duration-300 shadow-lg flex items-center justify-center gap-2 group/btn overflow-hidden">
                                <span>Book Now</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section id="contact" class="relative bg-[#1d6fa5] py-24 px-6 lg:px-20 text-white overflow-hidden border-t border-white/5">
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[500px] h-[500px] bg-blue-400/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[400px] h-[400px] bg-black/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-20 mb-20" data-aos="fade-up" data-aos-duration="1200">
                
                <div class="md:col-span-5 flex flex-col space-y-8">
                    <div>
                        <h2 class="text-4xl font-black tracking-tighter leading-[0.9] uppercase cursor-default">
                            BBPPMPV <br> 
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-white">BOE Malang</span>
                        </h2>
                        <div class="h-1.5 w-16 bg-blue-300 mt-4 rounded-full shadow-[0_0_15px_rgba(147,197,253,0.5)]"></div>
                    </div>
                    
                    <p class="text-blue-50/80 leading-relaxed text-lg font-light max-w-md">
                        Bukan sekadar pusat keunggulan vokasi, kami menyediakan ekosistem pendukung performa terbaik bagi para peserta pelatihan dan komunitas. Kami percaya bahwa ketajaman pikiran di bidang otomotif dan elektronika harus didukung oleh kebugaran fisik yang prima.
                    </p>

                    <div class="flex gap-4 pt-4">
                        <a href="#" class="group relative w-12 h-12 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md transition-all duration-500 hover:bg-white hover:scale-110 hover:-translate-y-1 shadow-xl">
                            <ion-icon name="logo-whatsapp" class="text-2xl group-hover:text-[#1d6fa5] transition-colors"></ion-icon>
                        </a>
                        <a href="#" class="group relative w-12 h-12 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md transition-all duration-500 hover:bg-white hover:scale-110 hover:-translate-y-1 shadow-xl">
                            <ion-icon name="logo-instagram" class="text-2xl group-hover:text-[#1d6fa5] transition-colors"></ion-icon>
                        </a>
                        <a href="#" class="group relative w-12 h-12 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md transition-all duration-500 hover:bg-white hover:scale-110 hover:-translate-y-1 shadow-xl">
                            <ion-icon name="logo-facebook" class="text-2xl group-hover:text-[#1d6fa5] transition-colors"></ion-icon>
                        </a>
                    </div>
                </div>

                <div class="md:col-span-4 flex flex-col" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-xl font-extrabold mb-10 tracking-widest uppercase flex items-center gap-3">
                        Contact Info
                        <span class="flex-grow h-[1px] bg-gradient-to-r from-white/30 to-transparent"></span>
                    </h3>
                    <ul class="space-y-8">
                        <li class="flex items-start gap-5 group">
                            <div class="bg-white/10 p-3.5 rounded-2xl border border-white/10 group-hover:bg-blue-400/20 group-hover:border-blue-300/50 transition-all duration-300">
                                <ion-icon name="location-outline" class="text-2xl text-blue-200"></ion-icon>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-300 mb-1">Lokasi</span>
                                <span class="text-blue-50/90 leading-relaxed font-medium text-sm">Jl. Teluk Mandar Tromol Arjosari, Malang, Jawa Timur 65126</span>
                            </div>
                        </li>
                        <li class="flex items-center gap-5 group">
                            <div class="bg-white/10 p-3.5 rounded-2xl border border-white/10 group-hover:bg-blue-400/20 transition-all">
                                <ion-icon name="call-outline" class="text-2xl text-blue-200"></ion-icon>
                            </div>
                            <div class="flex flex-col">
                                {{-- Label Nama CP --}}
                                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-300 mb-1">
                                    CP: Bpk. Donny Lesmana — Telepon
                                </span>
                                
                                {{-- Nomor Telepon --}}
                                <div class="flex items-center gap-2 group">
                                    <span class="text-blue-50/90 font-semibold group-hover:text-white transition-colors text-sm tracking-wide">
                                        (0341) 123456
                                    </span>
                                    
                                    {{-- Aksen Garis Kecil --}}
                                    <div class="w-4 h-px bg-white/20 group-hover:w-6 group-hover:bg-blue-400 transition-all duration-300"></div>
                                </div>
                            </div>
                        </li>
                        <li class="flex items-center gap-5 group">
                            <div class="bg-white/10 p-3.5 rounded-2xl border border-white/10 group-hover:bg-blue-400/20 transition-all">
                                <ion-icon name="mail-outline" class="text-2xl text-blue-200"></ion-icon>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-300 mb-1">Email</span>
                                <span class="text-blue-50/90 font-semibold group-hover:text-white transition-colors text-sm break-all">info@bbppmpvboe-malang.ac.id</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="md:col-span-3 flex flex-col" data-aos="fade-up" data-aos-delay="400">
                    <h3 class="text-xl font-extrabold mb-10 tracking-widest uppercase flex items-center gap-3">
                        Menu
                        <span class="flex-grow h-[1px] bg-gradient-to-r from-white/30 to-transparent"></span>
                    </h3>
                    <nav>
                        <ul class="space-y-5">
                            <li>
                                <a href="#about" class="group flex items-center text-lg font-medium text-blue-100 hover:text-white transition-all duration-300">
                                    <span class="w-0 group-hover:w-6 h-[2px] bg-blue-300 mr-0 group-hover:mr-3 transition-all duration-300"></span>
                                    About
                                </a>
                            </li>
                            <li>
                                <a href="#booking" class="group flex items-center text-lg font-medium text-blue-100 hover:text-white transition-all duration-300">
                                    <span class="w-0 group-hover:w-6 h-[2px] bg-blue-300 mr-0 group-hover:mr-3 transition-all duration-300"></span>
                                    Booking
                                </a>
                            </li>
                            <li>
                                <a href="#contact" class="group flex items-center text-lg font-medium text-blue-100 hover:text-white transition-all duration-300">
                                    <span class="w-0 group-hover:w-6 h-[2px] bg-blue-300 mr-0 group-hover:mr-3 transition-all duration-300"></span>
                                    Contact
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-6" data-aos="zoom-in">
                <p class="text-blue-200/80 text-xs font-medium tracking-widest uppercase">
                    © 2026 BOE. ALL RIGHTS RESERVED.
                </p>
                
                <div class="flex items-center gap-4 bg-black/20 px-6 py-3 rounded-2xl border border-white/5 backdrop-blur-sm">
                    <span class="text-[10px] font-bold tracking-[0.2em] text-blue-300/40 uppercase">Powered By</span>
                    <div class="flex items-center gap-3 border-l border-white/10 pl-4">
                        <img src="/image/logo/tutwuri-logo.svg" alt="Logo" class="h-8 w-auto brightness-125">
                        <span class="text-xs font-semibold tracking-wider uppercase leading-tight">
                            BBPPMPV <br> BOE MALANG
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
    // Inisialisasi AOS
    AOS.init({
        offset: 120,
        delay: 0,
        duration: 800,
        easing: 'ease-out-back',
        once: false,
        mirror: false,
    });

    // State & Variabel Global
    let currentPreviewImg = "";

    // Logika Modal Deskripsi & Preview Gambar
    function openDescription(title, body, imgUrl, detail, gallery, labels, tipe, max_dewasa, max_anak, jam_operasional) {
        const modal = document.getElementById('descModal');
        const modalContent = document.getElementById('modalContent');
        
        document.getElementById('modalTitle').innerText = title || '-';
        document.getElementById('modalBody').innerText = body || '-';
        document.getElementById('modalDetail').innerText = detail || 'Tidak ada detail tambahan.';
        document.getElementById('modalTypeLabel').innerText = 'Informasi ' + (tipe ? (tipe.charAt(0).toUpperCase() + tipe.slice(1)) : 'Fasilitas');
        
        // Capacity & Hours
        document.getElementById('modalMaxDewasa').innerText = max_dewasa || '-';
        document.getElementById('modalMaxAnak').innerText = max_anak || '-';
        document.getElementById('modalHours').innerText = jam_operasional || '-';
        
        // Gallery logic
        const galleryContainer = document.getElementById('modalGallery');
        galleryContainer.innerHTML = '';
        if (gallery && Array.isArray(gallery) && gallery.length > 0) {
            gallery.forEach(img => {
                const imgElement = document.createElement('img');
                imgElement.src = '/storage/fasilitas/gallery/' + img;
                imgElement.className = 'w-full h-24 object-cover rounded-xl border border-slate-100 hover:scale-105 transition-transform cursor-pointer shadow-sm';
                imgElement.onclick = () => {
                    currentPreviewImg = imgElement.src;
                    handlePreview();
                };
                galleryContainer.appendChild(imgElement);
            });
        } else {
            galleryContainer.innerHTML = '<p class="col-span-3 text-[10px] text-slate-300 italic">No gallery photos</p>';
        }

        // Labels logic
        const labelsContainer = document.getElementById('modalLabels');
        labelsContainer.innerHTML = '';
        if (labels && Array.isArray(labels) && labels.length > 0) {
            labels.forEach(label => {
                const span = document.createElement('span');
                span.className = 'px-3 py-1.5 bg-blue-50 text-[#1d6fa5] text-[10px] font-black uppercase tracking-widest rounded-xl border border-blue-100 shadow-sm';
                span.innerText = label;
                labelsContainer.appendChild(span);
            });
        } else {
            labelsContainer.innerHTML = '<span class="text-[10px] text-slate-300 italic">No features listed</span>';
        }
        
        currentPreviewImg = imgUrl; // Default to main image
        
        modal.classList.replace('hidden', 'flex');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDescription() {
        const modal = document.getElementById('descModal');
        const modalContent = document.getElementById('modalContent');

        modalContent.classList.replace('scale-100', 'opacity-100', 'scale-95');
        modalContent.classList.add('opacity-0');

        setTimeout(() => {
            modal.classList.replace('flex', 'hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    function handlePreview() {
        const overlay = document.getElementById('imagePreviewOverlay');
        const container = document.getElementById('previewContainer');
        const fullImg = document.getElementById('previewFullImage');

        fullImg.src = currentPreviewImg;
        overlay.classList.replace('hidden', 'flex');

        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            container.classList.replace('scale-90', 'scale-100');
        }, 10);
    }

    function closePreview() {
        const overlay = document.getElementById('imagePreviewOverlay');
        const container = document.getElementById('previewContainer');

        overlay.classList.add('opacity-0');
        container.classList.replace('scale-100', 'scale-90');

        setTimeout(() => {
            overlay.classList.replace('flex', 'hidden');
        }, 500);
    }

    // Logika Animasi Loading (Universal)
    function triggerLoading(e, buttonElement) {
        e.preventDefault();
        const targetUrl = buttonElement.getAttribute('href');
        
        const btnContent = buttonElement.querySelector('#btnContent');
        const loadingContainer = buttonElement.querySelector('#loadingContainer');

        // Matikan interaksi klik
        buttonElement.classList.add('pointer-events-none');
        
        // Sembunyikan konten (teks & panah) dengan opacity
        if (btnContent) {
            btnContent.classList.add('opacity-0', 'scale-90');
        }

        // Tampilkan loader di tengah
        if (loadingContainer) {
            loadingContainer.classList.replace('hidden', 'flex');
        }

        // Redirect setelah animasi (800ms)
        setTimeout(() => {
            window.location.href = targetUrl;
        }, 800);
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', () => {
        
        // Scroll Indicator & Back to Top Logic
        const scrollIndicator = document.getElementById('scroll-indicator');
        const backToTopBtn = document.getElementById('backToTop');

        window.addEventListener('scroll', () => {
            const scrollValue = window.scrollY;

            // Update Scroll Indicator
            if (scrollIndicator) {
                let opacity = 1 - (scrollValue / 200);
                scrollIndicator.style.opacity = Math.max(0, opacity);
                scrollIndicator.style.transform = `translateY(${scrollValue * 0.5}px)`;
                scrollIndicator.style.pointerEvents = opacity <= 0 ? 'none' : 'auto';
            }

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
        });



        // Close modal on click outside
        window.onclick = (event) => {
            const modal = document.getElementById('descModal');
            if (event.target == modal) closeDescription();
        };

        // ESC Key listener
        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape") {
                closePreview();
                closeDescription();
            }
        });

        // Booking Success Notification
        if (sessionStorage.getItem('booking_success')) {
            sessionStorage.removeItem('booking_success');
            Swal.fire({
                title: 'Berhasil!',
                text: 'Pengajuan booking anda sedang di proses, mohon tunggu dan cek email yang telah di daftarkan untuk informasi lebih lanjut',
                icon: 'success',
                confirmButtonColor: '#1d6fa5',
                customClass: {
                    popup: 'rounded-[1.5rem]'
                }
            });
        }
    });
</script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div id="descModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-2xl transform transition-all scale-95 opacity-0 duration-300" id="modalContent">
            <div class="p-8 md:p-12">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span id="modalTypeLabel" class="text-[#1d6fa5] font-black uppercase tracking-[0.2em] text-[10px] bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Informasi</span>
                        <h2 id="modalTitle" class="text-4xl font-black text-slate-900 mt-2 uppercase tracking-tighter">Judul Card</h2>
                    </div>
                    <button onclick="closeDescription()" class="bg-slate-50 hover:bg-red-500 hover:text-white p-3 rounded-2xl transition-all duration-300 group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="h-1 w-12 bg-gradient-to-r from-[#1d6fa5] to-blue-400 rounded-full mb-8"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-2">Deskripsi</h4>
                        <p id="modalBody" class="text-slate-600 leading-relaxed text-sm font-medium italic"></p>
                        
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-2 mt-6">Detail Fasilitas</h4>
                        <p id="modalDetail" class="text-slate-800 leading-relaxed text-sm font-bold"></p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-3">Informasi Operasional</h4>
                        <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Cap. Dewasa</span>
                                <span id="modalMaxDewasa" class="text-xs font-black text-slate-800"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Cap. Anak</span>
                                <span id="modalMaxAnak" class="text-xs font-black text-slate-800"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Jam Ops.</span>
                                <span id="modalHours" class="text-xs font-black text-[#1d6fa5]"></span>
                            </div>
                        </div>

                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-3">Fitur & Layanan</h4>
                        <div id="modalLabels" class="flex flex-wrap gap-2 mb-6"></div>
                        
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-3">Gallery Preview</h4>
                        <div id="modalGallery" class="grid grid-cols-3 gap-2"></div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-slate-100">
                    <button onclick="handlePreview()" class="flex-1 bg-[#1d6fa5] text-white py-5 rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-[10px] hover:bg-slate-900 transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-100 group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Main Photo
                    </button>

                    <button onclick="closeDescription()" class="flex-1 bg-slate-50 text-slate-400 py-5 rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-[10px] hover:bg-slate-100 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="imagePreviewOverlay" 
        class="fixed inset-0 z-[150] hidden items-center justify-center bg-black/40 backdrop-blur-xl p-4 transition-all duration-500 ease-in-out opacity-0"
        onclick="closePreview()">
        
        <div class="relative max-w-5xl w-full transform scale-90 transition-transform duration-500 ease-out" 
            id="previewContainer"
            onclick="event.stopPropagation()"> 
            <button onclick="closePreview()" 
                class="absolute top-6 right-6 z-[160] group flex items-center justify-center">
                
                <div class="absolute inset-0 bg-white/10 backdrop-blur-md border border-white/20 rounded-full scale-100 group-hover:scale-110 group-active:scale-95 transition-all duration-300 shadow-2xl"></div>
                
                <div class="relative p-3 text-white group-hover:text-red-400 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        class="h-7 w-7 transform group-hover:rotate-90 transition-transform duration-500 ease-out" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </button>
            
            <div class="overflow-hidden rounded-[2rem] shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-white/10 bg-gray-900">
                <img id="previewFullImage" 
                    src="/image/pictures/tenis-boe.svg" 
                    alt="Full Preview" 
                    class="mx-auto max-h-[80vh] w-full object-contain">
            </div>

            <div class="mt-4 text-center">
                <p class="text-white/50 text-xs uppercase tracking-[0.3em] font-medium">Press ESC to close</p>
            </div>
        </div>
    </div>
</body>
</html>