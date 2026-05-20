<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOE-Space Reserve | Tambah Admin Baru</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            15%       { transform: translateX(-7px); }
            30%       { transform: translateX(7px); }
            45%       { transform: translateX(-5px); }
            60%       { transform: translateX(5px); }
            75%       { transform: translateX(-3px); }
            90%       { transform: translateX(3px); }
        }
        .shake { animation: shake 0.42s cubic-bezier(.36,.07,.19,.97) both; }

        .field-error input,
        .field-error .input-wrap input {
            border-color: #F87171 !important;
            background-color: #FFF5F5 !important;
            box-shadow: 0 0 0 4px rgba(248,113,113,0.12) !important;
            color: #991B1B !important;
        }
        .field-error label { color: #EF4444 !important; }

        .field-ok input,
        .field-ok .input-wrap input {
            border-color: #4ADE80 !important;
            background-color: #F0FDF4 !important;
            box-shadow: 0 0 0 4px rgba(74,222,128,0.10) !important;
        }
        .field-ok label { color: #16A34A !important; }

        .err-msg, .ok-msg {
            display: none;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            font-weight: 700;
            margin-top: 7px;
            margin-left: 6px;
        }
        .err-msg { color: #EF4444; }
        .ok-msg  { color: #16A34A; }
        .err-msg.show, .ok-msg.show { display: flex; }

        #pw-strength-bar {
            height: 4px;
            border-radius: 99px;
            transition: width 0.3s, background-color 0.3s;
            width: 0%;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] font-sans antialiased text-slate-800">

    @php
        $from      = $from ?? request('from', '');
        $allowed   = ['dashboardMaster', 'admin.active.list'];
        $backRoute = in_array($from, $allowed) ? $from : 'admin.active.list';
        $backUrl   = route($backRoute);
    @endphp

    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 blur-[120px] rounded-full opacity-50"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-indigo-100 blur-[120px] rounded-full opacity-50"></div>
    </div>

    <div class="min-h-screen py-12 px-4 flex justify-center items-center">
        <div class="w-full max-w-4xl bg-white/80 backdrop-blur-xl rounded-[3rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.08)] border border-white overflow-hidden transition-all duration-500 hover:shadow-blue-200/40">

            <div class="pt-10 pb-6 px-10 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 bg-blue-50/50 rounded-full border border-blue-100 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1265A8]"></span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1265A8]">Secure Access</span>
                </div>
                <h2 class="text-3xl md:text-3xl font-black text-slate-900 tracking-tight uppercase">
                    Register <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1265A8] to-blue-400">New Admin</span>
                </h2>
                <div class="h-1 w-12 bg-gradient-to-r from-[#1265A8] to-blue-400 mx-auto mt-4 rounded-full"></div>
            </div>

            <form action="{{ route('admin.store') }}" method="POST" class="p-8 lg:p-12 pt-6" id="addAdminForm">
                @csrf
                {{-- Simpan back_url agar JS bisa baca --}}
                <input type="hidden" id="back-url" value="{{ $backUrl }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">

                    {{-- Nama Lengkap --}}
                    <div class="group" id="wrap-nama">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" name="nama" id="f-nama"
                            oninput="validateNama()"
                            class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold"
                            placeholder="John Doe">
                        <p class="err-msg" id="err-nama">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span id="err-nama-txt"></span>
                        </p>
                        <p class="ok-msg" id="ok-nama">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span id="ok-nama-txt"></span>
                        </p>
                    </div>

                    {{-- Username --}}
                    <div class="group" id="wrap-username">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Username</label>
                        <div class="relative input-wrap">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-400">@</span>
                            <input type="text" name="username" id="f-username"
                                oninput="validateUsername()"
                                class="w-full pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold lowercase"
                                placeholder="johndoe">
                        </div>
                        <p class="err-msg" id="err-username">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span id="err-username-txt"></span>
                        </p>
                        <p class="ok-msg" id="ok-username">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span id="ok-username-txt"></span>
                        </p>
                    </div>

                    {{-- Password --}}
                    <div class="group md:col-span-2" id="wrap-password">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="f-password"
                                oninput="validatePassword()"
                                class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold pr-14"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePw()" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" tabindex="-1">
                                <svg id="pw-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg id="pw-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <div class="mt-2 mx-1 bg-slate-100 rounded-full overflow-hidden" style="height:4px">
                            <div id="pw-strength-bar"></div>
                        </div>
                        <p class="err-msg" id="err-password">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span id="err-password-txt"></span>
                        </p>
                        <p class="ok-msg" id="ok-password">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span id="ok-password-txt"></span>
                        </p>
                    </div>

                    {{-- Can Edit Toggle --}}
                    <div class="group md:col-span-2 p-6 bg-slate-50 border border-slate-200 rounded-[2rem]">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Izin Edit (Can Edit)</h4>
                                <p class="text-[11px] text-slate-500 mt-1">Jika aktif, admin ini dapat mengunggah dan mengubah data fasilitas, harga, dll.</p>
                            </div>
                            <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                                <input type="checkbox" name="can_edit" id="toggle"
                                    class="absolute block w-6 h-6 rounded-full bg-white border-4 border-slate-300 appearance-none cursor-pointer z-10 transition-transform duration-300 ease-in-out left-0"
                                    onchange="this.classList.toggle('right-0'); this.classList.toggle('left-0'); this.classList.toggle('border-[#1265A8]'); this.nextElementSibling.classList.toggle('bg-[#1265A8]');"
                                    value="1">
                                <label for="toggle" class="block overflow-hidden h-6 rounded-full bg-slate-300 cursor-pointer transition-colors duration-300"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row-reverse gap-4 pt-6 mt-4 border-t border-slate-100/50">
                    <button type="submit" class="group relative w-full sm:w-2/3 overflow-hidden rounded-2xl bg-[#1265A8] px-8 py-5 transition-all duration-300 hover:bg-[#0d548a] hover:shadow-[0_20px_40px_-12px_rgba(18,101,168,0.35)] active:scale-[0.98]">
                        <div class="relative flex items-center justify-center gap-3">
                            <span class="text-sm font-black uppercase tracking-[0.2em] text-white">Buat Admin</span>
                        </div>
                    </button>
                    {{-- Tombol Batal — kembali ke asal halaman --}}
                    <a href="{{ $backUrl }}" class="group w-full sm:w-1/3 flex items-center justify-center gap-2 py-5 px-8 rounded-2xl border-2 border-slate-100 bg-white hover:border-slate-300 hover:bg-slate-50 active:scale-[0.98] transition-all">
                        <span class="text-xs font-black uppercase tracking-widest text-slate-500">Batal</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const BACK_URL = document.getElementById('back-url').value;

        /* ─── Helpers ─────────────────────────────────────────── */
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

        function showFieldErr(id, msg) {
            liveErr(id, msg);
            const wrap = document.getElementById('wrap-' + id);
            if (!wrap) return;
            wrap.classList.remove('shake');
            void wrap.offsetWidth;
            wrap.classList.add('shake');
            wrap.addEventListener('animationend', () => wrap.classList.remove('shake'), { once: true });
        }

        /* ─── Validators ──────────────────────────────────────── */
        function validateNama() {
            const val = document.getElementById('f-nama').value.trim();
            if (!val)                              return liveErr('nama', 'Nama lengkap wajib diisi.');
            if (/\d/.test(val))                    return liveErr('nama', 'Nama tidak boleh mengandung angka.');
            if (val.replace(/\s+/g,'').length < 2) return liveErr('nama', 'Nama minimal 2 huruf.');
            liveOk('nama', 'Nama valid.');
        }

        function validateUsername() {
            const val = document.getElementById('f-username').value.trim().toLowerCase();
            document.getElementById('f-username').value = val;
            if (!val)                        return liveErr('username', 'Username wajib diisi.');
            if (val.length < 3)              return liveErr('username', 'Username minimal 3 karakter.');
            if (val.length > 20)             return liveErr('username', 'Username maksimal 20 karakter.');
            if (/\s/.test(val))              return liveErr('username', 'Username tidak boleh mengandung spasi.');
            if (!/^[a-z0-9._]+$/.test(val)) return liveErr('username', 'Hanya huruf kecil, angka, titik, dan underscore.');
            liveOk('username', '@' + val + ' tersedia.');
        }

        function validatePassword() {
            const val = document.getElementById('f-password').value;
            const bar = document.getElementById('pw-strength-bar');

            if (!val) {
                bar.style.width = '0%';
                return liveErr('password', 'Password wajib diisi.');
            }
            if (val.length < 6) {
                bar.style.width = '25%'; bar.style.backgroundColor = '#F87171';
                return liveErr('password', 'Password minimal 6 karakter (' + val.length + '/6).');
            }

            let score = 0;
            if (val.length >= 8)           score++;
            if (/[A-Z]/.test(val))         score++;
            if (/[0-9]/.test(val))         score++;
            if (/[^A-Za-z0-9]/.test(val))  score++;

            const levels = [
                { w: '40%',  color: '#FB923C', label: 'Lemah' },
                { w: '60%',  color: '#FACC15', label: 'Cukup' },
                { w: '80%',  color: '#4ADE80', label: 'Kuat' },
                { w: '100%', color: '#22C55E', label: 'Sangat Kuat' },
            ];
            const lvl = levels[Math.min(score, 3)];
            bar.style.width = lvl.w;
            bar.style.backgroundColor = lvl.color;

            if (score < 2) return liveErr('password', 'Terlalu lemah — tambahkan huruf besar atau angka.');
            liveOk('password', 'Password ' + lvl.label + '.');
        }

        function togglePw() {
            const input  = document.getElementById('f-password');
            const eye    = document.getElementById('pw-eye');
            const eyeOff = document.getElementById('pw-eye-off');
            if (input.type === 'password') {
                input.type = 'text';
                eye.style.display = 'none';
                eyeOff.style.display = 'block';
            } else {
                input.type = 'password';
                eye.style.display = 'block';
                eyeOff.style.display = 'none';
            }
        }

        /* ─── Submit ──────────────────────────────────────────── */
        document.getElementById('addAdminForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let ok = true;

            const nama = document.getElementById('f-nama').value.trim();
            if (!nama)                                   { showFieldErr('nama', 'Nama lengkap wajib diisi.'); ok=false; }
            else if (/\d/.test(nama))                    { showFieldErr('nama', 'Nama tidak boleh mengandung angka.'); ok=false; }
            else if (nama.replace(/\s+/g,'').length < 2) { showFieldErr('nama', 'Nama minimal 2 huruf.'); ok=false; }
            else liveOk('nama', 'Nama valid.');

            const uname = document.getElementById('f-username').value.trim();
            if (!uname)                             { showFieldErr('username', 'Username wajib diisi.'); ok=false; }
            else if (uname.length < 3)              { showFieldErr('username', 'Username minimal 3 karakter.'); ok=false; }
            else if (uname.length > 20)             { showFieldErr('username', 'Username maksimal 20 karakter.'); ok=false; }
            else if (/\s/.test(uname))              { showFieldErr('username', 'Username tidak boleh mengandung spasi.'); ok=false; }
            else if (!/^[a-z0-9._]+$/.test(uname)) { showFieldErr('username', 'Hanya huruf kecil, angka, titik, dan underscore.'); ok=false; }
            else liveOk('username', '@' + uname + ' tersedia.');

            const pw = document.getElementById('f-password').value;
            if (!pw)                { showFieldErr('password', 'Password wajib diisi.'); ok=false; }
            else if (pw.length < 6) { showFieldErr('password', 'Password minimal 6 karakter.'); ok=false; }
            else {
                let score = 0;
                if (pw.length >= 8)          score++;
                if (/[A-Z]/.test(pw))        score++;
                if (/[0-9]/.test(pw))        score++;
                if (/[^A-Za-z0-9]/.test(pw)) score++;
                if (score < 2) { showFieldErr('password', 'Password terlalu lemah — tambahkan variasi karakter.'); ok=false; }
                else liveOk('password', 'Password kuat.');
            }

            if (!ok) return;

            const form = this;
            const formData = new FormData(form);
            if (!formData.has('can_edit')) formData.append('can_edit', false);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Admin baru telah ditambahkan.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[2rem]' }
                    }).then(() => {
                        // Kembali ke halaman asal (dashboard atau admin active list)
                        window.location.href = data.redirect || BACK_URL;
                    });
                } else {
                    Swal.fire('Error', data.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal menambahkan admin', 'error');
            });
        });
    </script>
</body>
</html>