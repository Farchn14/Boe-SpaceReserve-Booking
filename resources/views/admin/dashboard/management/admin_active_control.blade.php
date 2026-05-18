<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BOE-Space Reserve | Manage Admins</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; overflow: hidden; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        
        .admin-item { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .admin-item.active { background: white; border-color: #1265A8; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); transform: translateX(8px); }
        .admin-item.active .avatar { background: #1265A8; color: white; }
        
        .control-panel { transition: all 0.4s ease; transform: translateY(0); opacity: 1; }
        .panel-hidden { transform: translateY(20px); opacity: 0; pointer-events: none; }
        
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e2e8f0; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #1265A8; }
        input:checked + .slider:before { transform: translateX(20px); }

        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease forwards; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 flex flex-col md:ml-64 bg-[#f8fafc] h-screen transition-all duration-500">
        <!-- Header -->
        @include('admin.dashboard.layouts.header', [
            'headerTitle' => 'Admin Active Control',
            'headerSubtitle' => 'Management & Role Controls'
        ])

        <div class="px-8 pb-4 flex justify-end">
            <a href="{{ route('dashboardAddNewAdmin') }}" class="px-5 py-2.5 bg-[#1265A8] text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:scale-105 transition-transform flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                New Account
            </a>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex overflow-hidden p-6 gap-6">
            
            <!-- Left Panel: Admin List -->
            <div class="w-full md:w-[380px] flex flex-col gap-4 overflow-hidden">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest">Accounts ({{ count($admins) }})</h3>
                    <div class="relative">
                        <input type="text" id="adminSearch" placeholder="Search..." class="w-32 py-1.5 px-3 text-xs bg-white border border-slate-200 rounded-full focus:w-48 transition-all outline-none focus:ring-2 focus:ring-blue-100 font-medium">
                    </div>
                </div>
                
                <div id="adminList" class="flex-1 overflow-y-auto custom-scrollbar pr-2 flex flex-col gap-3">
                    @foreach($admins as $admin)
                    <div onclick="selectAdmin({{ json_encode($admin->makeVisible(['password'])) }}, this)" 
                         class="admin-item group cursor-pointer p-4 rounded-2xl border border-transparent bg-slate-50 hover:bg-white hover:border-slate-100 flex items-center gap-4 {{ $admin->id_log == session('id_log') ? 'ring-1 ring-slate-200 bg-white/50' : '' }}"
                         data-id="{{ $admin->id_log }}"
                         data-name="{{ $admin->nama }}"
                         data-username="{{ $admin->username }}">
                        
                        <div class="avatar w-12 h-12 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center text-[#1265A8] font-bold text-lg group-hover:bg-[#1265A8] group-hover:text-white transition-all shrink-0">
                            {{ strtoupper(substr($admin->nama, 0, 1)) }}
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-slate-800 truncate">{{ $admin->nama }}</h4>
                                @if($admin->id_log == session('id_log'))
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-black uppercase rounded">Me</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] font-medium text-slate-400 truncate">@ {{ $admin->username }}</span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">{{ $admin->role }}</span>
                            </div>
                        </div>

                        @if($admin->force_logout)
                            <div class="w-2 h-2 rounded-full bg-rose-500 ring-4 ring-rose-100" title="Locked"></div>
                        @else
                            <div class="w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100 animate-pulse" title="Active"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Panel: Control Panel -->
            <div id="controlPanel" class="flex-1 glass-card rounded-[2.5rem] shadow-xl shadow-slate-200/50 flex flex-col overflow-hidden relative border border-white">
                
                <!-- Empty State (Hidden when admin selected) -->
                <div id="emptyPanel" class="absolute inset-0 flex flex-col items-center justify-center text-center p-10 z-20 bg-white/90 backdrop-blur-sm transition-opacity duration-300">
                    <div class="w-24 h-24 bg-blue-50 rounded-[2rem] flex items-center justify-center text-[#1265A8] mb-6 animate-bounce">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800">Select an Account</h2>
                    <p class="mt-2 text-slate-400 font-medium max-w-xs">Select an admin from the list on the left to manage their permissions and account settings.</p>
                </div>

                <!-- Panel Header -->
                <div class="p-8 pb-4 flex items-center justify-between">
                    <div>
                        <h2 id="panelTitle" class="text-2xl font-black text-slate-800">Admin Control</h2>
                        <p id="panelSubTitle" class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Manage Credentials & Access</p>
                    </div>
                    <div id="roleBadge" class="px-4 py-1.5 bg-amber-50 text-amber-600 border border-amber-100 rounded-full text-[11px] font-black uppercase tracking-wider shadow-sm">
                        Owner
                    </div>
                </div>

                <!-- Form Scrollable -->
                <div class="flex-1 overflow-y-auto px-8 py-4 custom-scrollbar">
                    <form id="updateForm" onsubmit="handleUpdate(event)" class="space-y-6">
                        <input type="hidden" id="editId">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name Field -->
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                <div class="relative group">
                                    <input type="text" id="editNama" required
                                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all outline-none font-semibold text-slate-700">
                                    <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-[#1265A8] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            </div>

                            <!-- Username Field -->
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Username</label>
                                <div class="relative group">
                                    <input type="text" id="editUsername" required
                                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all outline-none font-semibold text-slate-700">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 font-bold group-focus-within:text-[#1265A8]">@</span>
                                </div>
                            </div>
                        </div>

                        <!-- Spacer line -->
                        <div class="h-px bg-gradient-to-r from-transparent via-slate-100 to-transparent my-2"></div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password Lama (View) -->
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Password Lama</label>
                                <div class="relative group">
                                    <div class="flex items-center">
                                        <input type="password" id="oldPassword" readonly
                                            class="w-full px-5 py-3.5 bg-slate-100/50 border border-slate-100 rounded-2xl font-mono font-bold text-slate-500 cursor-default outline-none" value="********">
                                        <button type="button" onclick="toggleViewPassword('oldPassword', this)" class="absolute right-4 text-slate-400 hover:text-[#1265A8]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium ml-1">* Kata sandi saat ini yang terdaftar.</p>
                            </div>

                            <!-- Password Baru -->
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Password Baru</label>
                                <div class="relative group">
                                    <input type="text" id="newPassword" placeholder="Biarkan kosong jika tidak diubah"
                                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all outline-none font-semibold text-slate-700">
                                    <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-[#1265A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Special Controls Section -->
                        <div id="adminControlsArea" class="p-6 bg-blue-50/50 rounded-[2rem] border border-blue-50 space-y-5">
                            <!-- Can Edit Toggle -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#1265A8] shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-black text-slate-700">Editing Permissions</h5>
                                        <p class="text-[11px] font-medium text-slate-400">Allow this admin to modify website data.</p>
                                    </div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="canEditToggle">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <!-- Force Logout Control -->
                            <div class="flex items-center justify-between pt-4 border-t border-blue-100/50">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rose-500 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-black text-slate-700">Account Locking</h5>
                                        <p id="forceLogoutStatus" class="text-[11px] font-bold text-emerald-500 uppercase">Status: Active</p>
                                    </div>
                                </div>
                                <button type="button" id="forceLogoutBtn" onclick="toggleForceLogout()"
                                    class="px-4 py-2 bg-rose-100 text-rose-600 rounded-xl text-[10px] font-black uppercase hover:bg-rose-500 hover:text-white transition-all">
                                    Force Logout
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer Actions -->
                <div class="p-8 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" id="deleteBtn" onclick="handleDelete()"
                        class="px-5 py-3 text-rose-500 hover:bg-rose-50 rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete Account
                    </button>
                    
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="cancelEdit()" class="px-6 py-3 text-slate-400 hover:text-slate-600 text-xs font-black uppercase tracking-widest transition-all">
                            Batal
                        </button>
                        <button type="submit" form="updateForm" class="px-8 py-3 bg-[#1265A8] text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-100 hover:scale-105 active:scale-95 transition-all">
                            Update Account
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let selectedAdminData = null;
        let selectedRowElement = null;

        // Search Admin
        document.getElementById('adminSearch').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.admin-item').forEach(item => {
                const name = item.dataset.name.toLowerCase();
                const username = item.dataset.username.toLowerCase();
                if (name.includes(term) || username.includes(term)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        function selectAdmin(admin, element) {
            selectedAdminData = { ...admin };
            
            // UI Toggle
            if (selectedRowElement) selectedRowElement.classList.remove('active');
            selectedRowElement = element;
            selectedRowElement.classList.add('active');
            
            document.getElementById('emptyPanel').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('emptyPanel').style.display = 'none';
            }, 300);

            // Populate Form
            document.getElementById('editId').value = admin.id_log;
            document.getElementById('editNama').value = admin.nama;
            document.getElementById('editUsername').value = admin.username;
            document.getElementById('oldPassword').value = admin.password;
            document.getElementById('newPassword').value = '';
            document.getElementById('canEditToggle').checked = admin.can_edit == 1;
            
            // Header Info
            document.getElementById('panelTitle').innerText = admin.nama;
            document.getElementById('panelSubTitle').innerText = `@${admin.username} — ${admin.role.toUpperCase()}`;
            
            // Badge & Visibility based on Role
            const roleBadge = document.getElementById('roleBadge');
            roleBadge.innerText = admin.role;
            if (admin.role === 'owner') {
                roleBadge.className = 'px-4 py-1.5 bg-amber-50 text-amber-600 border border-amber-100 rounded-full text-[11px] font-black uppercase tracking-wider shadow-sm';
                document.getElementById('adminControlsArea').style.display = 'none';
            } else {
                roleBadge.className = 'px-4 py-1.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-full text-[11px] font-black uppercase tracking-wider shadow-sm';
                document.getElementById('adminControlsArea').style.display = 'block';
            }

            // Status Locking
            const statusText = document.getElementById('forceLogoutStatus');
            const logoutBtn = document.getElementById('forceLogoutBtn');
            if (admin.force_logout) {
                statusText.innerText = 'Status: Locked / Forced Out';
                statusText.className = 'text-[11px] font-bold text-rose-500 uppercase';
                logoutBtn.innerText = 'Allow Login';
                logoutBtn.className = 'px-4 py-2 bg-emerald-100 text-emerald-600 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-500 hover:text-white transition-all';
            } else {
                statusText.innerText = 'Status: Active';
                statusText.className = 'text-[11px] font-bold text-emerald-500 uppercase';
                logoutBtn.innerText = 'Force Logout';
                logoutBtn.className = 'px-4 py-2 bg-rose-100 text-rose-600 rounded-xl text-[10px] font-black uppercase hover:bg-rose-500 hover:text-white transition-all';
            }

            // Animasi masuk
            const form = document.querySelector('#controlPanel form');
            form.classList.remove('animate-fade-in');
            void form.offsetWidth; // Trigger reflow
            form.classList.add('animate-fade-in');
        }

        function cancelEdit() {
            if (selectedAdminData) {
                selectAdmin(selectedAdminData, selectedRowElement);
            }
        }

        function toggleViewPassword(id, btn) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>`;
            } else {
                input.type = 'password';
                btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
            }
        }

        async function handleUpdate(e) {
            e.preventDefault();
            const id = document.getElementById('editId').value;
            const data = {
                nama: document.getElementById('editNama').value,
                username: document.getElementById('editUsername').value,
                password: document.getElementById('newPassword').value,
                can_edit: document.getElementById('canEditToggle').checked
            };

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: 'Data admin akan diperbarui dan admin mungkin perlu login ulang.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1265A8',
                confirmButtonText: 'Ya, Update!',
                customClass: { popup: 'rounded-[1.5rem]' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await fetch(`/admin/update-credentials/${id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify(data)
                        });
                        const resultData = await res.json();
                        if (resultData.success) {
                            Swal.fire({ title: 'Success!', text: resultData.message, icon: 'success', timer: 1500, showConfirmButton: false });
                            window.location.reload();
                        } else {
                            const errorMsg = resultData.errors ? Object.values(resultData.errors)[0][0] : resultData.message;
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    } catch (err) {
                        Swal.fire('Error', 'Technical connection error', 'error');
                    }
                }
            });
        }

        async function toggleForceLogout() {
            const id = selectedAdminData.id_log;
            const isCurrentlyBlocked = selectedAdminData.force_logout;
            
            Swal.fire({
                title: isCurrentlyBlocked ? 'Unlock Account?' : 'Force Logout?',
                text: isCurrentlyBlocked ? 'Admin will be able to log in again.' : 'Admin will be kicked out and blocked from logging in.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isCurrentlyBlocked ? '#10b981' : '#f43f5e',
                confirmButtonText: 'Proceed',
                customClass: { popup: 'rounded-[1.5rem]' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const res = await fetch(`/admin/force-logout/${id}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Updated', data.message, 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                }
            });
        }

        async function handleDelete() {
            const id = selectedAdminData.id_log;
            const name = selectedAdminData.nama;

            Swal.fire({
                title: 'Delete Account?',
                text: `Permanent removal of ${name}. This cannot be undone!`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, Delete Permanently',
                customClass: { popup: 'rounded-[1.5rem]' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const res = await fetch(`/admin/delete/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Deleted!', 'Account has been removed.', 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                }
            });
        }

        function toggleSidebar() {
            const sidebar = document.querySelector('.md\\:ml-64');
            const aside = document.querySelector('aside');
            if (aside) {
                // Simplified toggle logic for sidebar visibility on mobile
                aside.classList.toggle('-translate-x-full');
            }
        }
    </script>
</body>
</html>