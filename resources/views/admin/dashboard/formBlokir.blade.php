<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Internal Booking Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #F9FAFB; }
        [x-cloak] { display: none !important; }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            15%       { transform: translateX(-8px); }
            30%       { transform: translateX(8px); }
            45%       { transform: translateX(-6px); }
            60%       { transform: translateX(6px); }
            75%       { transform: translateX(-3px); }
            90%       { transform: translateX(3px); }
        }
        .shake { animation: shake 0.45s cubic-bezier(.36,.07,.19,.97) both; }

        .field-error input,
        .field-error textarea,
        .field-error select {
            border-color: #F87171 !important;
            background-color: #FFF5F5 !important;
            box-shadow: 0 0 0 4px rgba(248,113,113,0.12) !important;
            color: #991B1B !important;
        }
        .field-error label { color: #EF4444 !important; }

        .field-ok input,
        .field-ok textarea,
        .field-ok select {
            border-color: #4ADE80 !important;
            background-color: #F0FDF4 !important;
            box-shadow: 0 0 0 4px rgba(74,222,128,0.10) !important;
        }
        .field-ok label { color: #16A34A !important; }

        .err-msg {
            display: none;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            font-weight: 700;
            color: #EF4444;
            margin-top: 7px;
            margin-left: 18px;
        }
        .err-msg.show { display: flex; }

        .ok-msg {
            display: none;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            font-weight: 700;
            color: #16A34A;
            margin-top: 7px;
            margin-left: 18px;
        }
        .ok-msg.show { display: flex; }
    </style>
</head>
<body class="min-h-screen">

<main class="flex flex-col items-center justify-start pt-24 pb-20 px-4"
    x-data="{
        tipe: 'blocked',
        fasilitas_id: '{{ $selectedId }}',
        tgl_mulai: '{{ $selectedDate }}',
        duration: 1,
        unit: 'day',
        nama_pic: '',
        divisi: '',
        whatsapp: '',
        tujuan: '',
        facilities: {{ $facilities->toJson() }},

        get currentFacility() {
            return this.facilities.find(f => f.id == this.fasilitas_id) || null;
        },

        get checkOutDate() {
            if (!this.tgl_mulai) return '-';
            let date = new Date(this.tgl_mulai);
            let val = parseInt(this.duration) || 1;
            if (this.unit === 'month') {
                date.setMonth(date.getMonth() + val);
                date.setDate(date.getDate() - 1);
            } else {
                date.setDate(date.getDate() + val - 1);
            }
            return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }).format(date);
        },

        validateNama() {
            const val = this.nama_pic.trim();
            if (!val) return liveErr('nama', 'Nama wajib diisi.');
            if (/\d/.test(val)) return liveErr('nama', 'Nama tidak boleh mengandung angka.');
            if (val.replace(/\s+/g,'').length < 2) return liveErr('nama', 'Nama minimal 2 huruf.');
            liveOk('nama', 'Nama valid.');
        },

        validateDivisi() {
            const val = this.divisi.trim();
            if (!val) return liveErr('divisi', 'Divisi wajib diisi.');
            liveOk('divisi', 'Divisi valid.');
        },

        validateWa() {
            this.whatsapp = this.whatsapp.replace(/\D/g, '');
            const val = this.whatsapp.trim();
            if (!val) return liveErr('wa', 'Nomor WhatsApp wajib diisi.');
            if (val.length < 10) return liveErr('wa', 'Minimal 10 digit, sekarang ' + val.length + ' digit.');
            if (val.length > 15) return liveErr('wa', 'Maksimal 15 digit.');
            liveOk('wa', 'Nomor valid (' + val.length + ' digit).');
        },

        validateTujuan() {
            const val = this.tujuan.trim();
            if (!val) return liveErr('tujuan', 'Tujuan penggunaan wajib diisi.');
            if (val.length < 10) return liveErr('tujuan', 'Deskripsikan lebih detail (' + val.length + '/10 karakter).');
            liveOk('tujuan', 'Deskripsi lengkap.');
        },

        validateFasilitas() {
            if (!this.fasilitas_id) return liveErr('fasilitas', 'Pilih unit fasilitas terlebih dahulu.');
            liveOk('fasilitas', 'Fasilitas dipilih.');
        },

        validateTgl() {
            if (!this.tgl_mulai) return liveErr('tgl', 'Tanggal check-in wajib diisi.');
            liveOk('tgl', 'Tanggal valid.');
        },

        submitBlokir() {
            let ok = true;

            if (!this.fasilitas_id)                             { showFieldErr('fasilitas','Pilih unit fasilitas.'); ok=false; } else liveOk('fasilitas','Fasilitas dipilih.');
            if (!this.tgl_mulai)                                { showFieldErr('tgl','Tanggal check-in wajib diisi.'); ok=false; } else liveOk('tgl','Tanggal valid.');

            const nama = this.nama_pic.trim();
            if (!nama)                                          { showFieldErr('nama','Nama wajib diisi.'); ok=false; }
            else if (/\d/.test(nama))                           { showFieldErr('nama','Nama tidak boleh mengandung angka.'); ok=false; }
            else if (nama.replace(/\s+/g,'').length < 2)        { showFieldErr('nama','Nama minimal 2 huruf.'); ok=false; }
            else liveOk('nama','Nama valid.');

            if (!this.divisi.trim())                            { showFieldErr('divisi','Divisi wajib diisi.'); ok=false; } else liveOk('divisi','Divisi valid.');

            const wa = this.whatsapp.trim();
            if (!wa)                                            { showFieldErr('wa','Nomor WhatsApp wajib diisi.'); ok=false; }
            else if (wa.length < 10)                            { showFieldErr('wa','Minimal 10 digit.'); ok=false; }
            else if (wa.length > 15)                            { showFieldErr('wa','Maksimal 15 digit.'); ok=false; }
            else liveOk('wa','Nomor valid.');

            const tujuan = this.tujuan.trim();
            if (!tujuan)                                        { showFieldErr('tujuan','Tujuan penggunaan wajib diisi.'); ok=false; }
            else if (tujuan.length < 10)                        { showFieldErr('tujuan','Deskripsikan lebih detail.'); ok=false; }
            else liveOk('tujuan','Deskripsi lengkap.');

            if (!ok) return;

            Swal.fire({ title: 'Mengunci Jadwal...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch('{{ route('kontrolJadwal.blokir') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    fasilitas_id: this.fasilitas_id,
                    tgl_mulai: this.tgl_mulai,
                    tipe: this.tipe,
                    unit: this.unit,
                    durasi: this.duration,
                    nama_pic: this.nama_pic,
                    divisi: this.divisi,
                    whatsapp: this.whatsapp,
                    tujuan: this.tujuan
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success' }).then(() => {
                        window.location.href = data.redirect || '{{ route('kontrolJadwal.index') }}';
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Terjadi kesalahan sistem.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error!', 'Terjadi kesalahan jaringan atau server.', 'error');
            });
        }
    }">

    <div class="w-full max-w-2xl glass-card p-10 md:p-14 rounded-[4rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.08)] border border-white relative overflow-hidden">

        <div class="absolute -top-32 -right-32 w-80 h-80 bg-blue-50 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-indigo-50 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="text-center mb-12 relative">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                Internal Reserve
            </div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic leading-none mb-3">Form Booking Blocked</h2>
            <p class="text-slate-400 font-medium text-sm">Halaman khusus pemblokiran unit untuk agenda BBPPMPV BOE.</p>
        </div>

        <div class="space-y-8 relative">

            {{-- Quick Config --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-2">

                {{-- Fasilitas --}}
                <div id="wrap-fasilitas">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block font-['Poppins']">Pilih Unit Fasilitas *</label>
                    <div class="relative">
                        <select x-model="fasilitas_id"
                            @change="validateFasilitas()"
                            class="w-full px-7 py-5 bg-slate-100/50 border border-slate-200/60 rounded-[2rem] outline-none focus:bg-white focus:ring-4 focus:ring-slate-100 transition-all font-bold text-slate-700 text-sm appearance-none">
                            <option value="">-- Pilih --</option>
                            <template x-for="f in facilities" :key="f.id">
                                <option :value="f.id" :selected="f.id == fasilitas_id" x-text="f.nama.toUpperCase()"></option>
                            </template>
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <p class="err-msg" id="err-fasilitas"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span id="err-fasilitas-txt"></span></p>
                    <p class="ok-msg"  id="ok-fasilitas"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span id="ok-fasilitas-txt"></span></p>
                </div>

                {{-- Tanggal --}}
                <div id="wrap-tgl">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block font-['Poppins']">Tanggal Check-In *</label>
                    <input type="date" x-model="tgl_mulai"
                        @change="validateTgl()"
                        class="w-full px-7 py-5 bg-slate-100/50 border border-slate-200/60 rounded-[2rem] outline-none focus:bg-white focus:ring-4 focus:ring-slate-100 transition-all font-bold text-slate-700 text-sm">
                    <p class="err-msg" id="err-tgl"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span id="err-tgl-txt"></span></p>
                    <p class="ok-msg"  id="ok-tgl"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span id="ok-tgl-txt"></span></p>
                </div>
            </div>

            {{-- Duration Logic --}}
            <div class="bg-white p-2 rounded-[3.2rem] shadow-xl shadow-slate-200/40 border border-slate-100">
                <div class="p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-8 bg-slate-900 rounded-[2.8rem] text-white">
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2">Estimasi Check-Out</h4>
                        <div class="text-2xl md:text-3xl font-black tracking-tighter italic" x-text="checkOutDate"></div>
                    </div>
                    <div class="flex flex-col items-center gap-3 w-full md:w-auto">
                        <div class="flex items-center gap-4 bg-white/5 p-2 rounded-3xl border border-white/5">
                            <button @click="if(duration > 1) duration--" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center font-black text-xl text-slate-900 hover:bg-slate-200 transition-all active:scale-95">-</button>
                            <div class="text-center w-14">
                                <span class="text-2xl font-black block leading-none" x-text="duration"></span>
                            </div>
                            <button @click="duration++" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center font-black text-xl text-slate-900 hover:bg-slate-200 transition-all active:scale-95">+</button>
                        </div>
                        <div class="flex bg-white/10 p-1 rounded-2xl w-full">
                            <button @click="unit = 'day'" :class="unit === 'day' ? 'bg-white text-slate-900' : 'text-white'" class="flex-1 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all">Hari</button>
                            <button @click="unit = 'month'" :class="unit === 'month' ? 'bg-white text-slate-900' : 'text-white'" class="flex-1 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all">Bulan</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Fields --}}
            <div class="space-y-5">

                {{-- Nama --}}
                <div id="wrap-nama">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Nama Pegawai / Penyewa Internal *</label>
                    <input type="text" x-model="nama_pic"
                        @input="validateNama()"
                        placeholder="Contoh: Dr. Budi Santoso"
                        class="w-full px-8 py-5 bg-slate-100/30 border border-slate-200/50 rounded-[2rem] outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all font-bold text-slate-700 text-sm">
                    <p class="err-msg" id="err-nama"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span id="err-nama-txt"></span></p>
                    <p class="ok-msg"  id="ok-nama"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span id="ok-nama-txt"></span></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Divisi --}}
                    <div id="wrap-divisi">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Divisi / Unit Kerja *</label>
                        <input type="text" x-model="divisi"
                            @input="validateDivisi()"
                            placeholder="Contoh: Divisi IT"
                            class="w-full px-8 py-5 bg-slate-100/30 border border-slate-200/50 rounded-[2rem] outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all font-bold text-slate-700 text-sm">
                        <p class="err-msg" id="err-divisi"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span id="err-divisi-txt"></span></p>
                        <p class="ok-msg"  id="ok-divisi"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span id="ok-divisi-txt"></span></p>
                    </div>

                    {{-- WhatsApp --}}
                    <div id="wrap-wa">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Nomor WhatsApp *</label>
                        <input type="text" x-model="whatsapp"
                            @input="validateWa()"
                            placeholder="08xxxxxxxxxx"
                            maxlength="15"
                            class="w-full px-8 py-5 bg-slate-100/30 border border-slate-200/50 rounded-[2rem] outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all font-bold text-slate-700 text-sm">
                        <p class="err-msg" id="err-wa"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span id="err-wa-txt"></span></p>
                        <p class="ok-msg"  id="ok-wa"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span id="ok-wa-txt"></span></p>
                    </div>
                </div>

                {{-- Tujuan --}}
                <div id="wrap-tujuan">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Tujuan Penggunaan *</label>
                    <textarea x-model="tujuan" rows="3"
                        @input="validateTujuan()"
                        placeholder="Deskripsikan agenda atau kegiatan..."
                        class="w-full px-8 py-5 bg-slate-100/30 border border-slate-200/50 rounded-[2.5rem] outline-none focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all font-bold text-slate-700 text-sm resize-none"></textarea>
                    <p class="err-msg" id="err-tujuan"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span id="err-tujuan-txt"></span></p>
                    <p class="ok-msg"  id="ok-tujuan"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span id="ok-tujuan-txt"></span></p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 flex flex-col md:flex-row gap-4">
                <a href="{{ route('kontrolJadwal.index') }}" class="flex-1 py-6 px-8 bg-slate-100 text-slate-400 font-black rounded-[2.2rem] uppercase tracking-widest text-[11px] text-center hover:bg-slate-200 transition-all active:scale-95">Batalkan</a>
                <button @click="submitBlokir()" class="flex-[2] py-6 px-8 bg-slate-900 text-white font-black rounded-[2.2rem] uppercase tracking-widest text-[11px] shadow-2xl shadow-slate-200 hover:bg-black transition-all active:scale-95 flex items-center justify-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    Simpan & Kunci Jadwal
                </button>
            </div>
        </div>
    </div>

    <div class="mt-14 text-center">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] italic leading-relaxed">
            © 2026 BBPPMPV BOE MALANG — ADMIN SYSTEM<br>
            <span class="opacity-50 tracking-normal font-medium">Internal Secure Scheduling Pipeline</span>
        </p>
    </div>
</main>

<script>
    /* Tampilkan error + shake, sembunyikan ok */
    function showFieldErr(id, msg) {
        const wrap = document.getElementById('wrap-' + id);
        if (!wrap) return;
        wrap.classList.remove('field-ok');
        wrap.classList.add('field-error');
        const errBox = document.getElementById('err-' + id);
        const errTxt = document.getElementById('err-' + id + '-txt');
        const okBox  = document.getElementById('ok-' + id);
        if (errTxt) errTxt.textContent = msg;
        if (errBox) errBox.classList.add('show');
        if (okBox)  okBox.classList.remove('show');
        wrap.classList.remove('shake');
        void wrap.offsetWidth;
        wrap.classList.add('shake');
        wrap.addEventListener('animationend', () => wrap.classList.remove('shake'), { once: true });
    }

    /* Sama seperti showFieldErr tapi tanpa shake — untuk validasi live saat mengetik */
    function liveErr(id, msg) {
        const wrap = document.getElementById('wrap-' + id);
        if (!wrap) return;
        wrap.classList.remove('field-ok');
        wrap.classList.add('field-error');
        const errBox = document.getElementById('err-' + id);
        const errTxt = document.getElementById('err-' + id + '-txt');
        const okBox  = document.getElementById('ok-' + id);
        if (errTxt) errTxt.textContent = msg;
        if (errBox) errBox.classList.add('show');
        if (okBox)  okBox.classList.remove('show');
    }

    /* Tampilkan ok, sembunyikan error */
    function liveOk(id, msg) {
        const wrap = document.getElementById('wrap-' + id);
        if (!wrap) return;
        wrap.classList.remove('field-error', 'shake');
        wrap.classList.add('field-ok');
        const errBox = document.getElementById('err-' + id);
        const okBox  = document.getElementById('ok-' + id);
        const okTxt  = document.getElementById('ok-' + id + '-txt');
        if (errBox) errBox.classList.remove('show');
        if (okTxt)  okTxt.textContent = msg;
        if (okBox)  okBox.classList.add('show');
    }
</script>

</body>
</html>