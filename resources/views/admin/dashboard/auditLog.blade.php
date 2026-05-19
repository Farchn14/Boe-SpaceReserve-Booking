<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Audit Log</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; overflow-x: hidden; }
        .log-row { transition: all 0.2s ease; }
        .log-row:hover { background: #f0f7ff; }
        .audit-badge {
            display: inline-flex; align-items: center; padding: 3px 10px;
            border-radius: 999px; font-size: 10px; font-weight: 800;
            text-transform: uppercase; letter-spacing: .06em; white-space: nowrap;
        }
        .btn-batch-delete {
            display: none;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="flex min-h-screen">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 w-full md:ml-64 p-4 md:p-8 min-h-screen">

        {{-- HEADER --}}
        @include('admin.dashboard.layouts.header', [
            'headerTitle' => 'Audit Log',
            'headerSubtitle' => 'Manajemen rekaman aktivitas sistem.'
        ])

        <div class="flex justify-end mb-6">
            <div class="flex items-center gap-2">
                <button id="btnBatchDelete" onclick="deleteSelected()"
                    class="btn-batch-delete items-center gap-2 px-4 py-2.5 bg-red-500 text-white rounded-xl text-xs font-bold hover:bg-red-600 transition-all shadow-lg active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Terpilih (<span id="selectedCount">0</span>)
                </button>

                <a href="{{ route('kontrolJadwal.index') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-[1.8rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-5 text-center w-12">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 text-[#1265A8] border-slate-300 rounded focus:ring-[#1265A8] cursor-pointer">
                            </th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black">Waktu</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black">Admin</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black">Aksi</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black">Deskripsi</th>
                            <th class="p-5 text-left text-[10px] uppercase tracking-wider text-slate-400 font-black">Fasilitas</th>
                            <th class="p-5 text-center text-[10px] uppercase tracking-wider text-slate-400 font-black">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($logs as $log)
                        <tr class="log-row" id="log-row-{{ $log->id }}">
                            <td class="p-5 text-center">
                                <input type="checkbox" name="log_ids[]" value="{{ $log->id }}" class="log-checkbox w-4 h-4 text-[#1265A8] border-slate-300 rounded focus:ring-[#1265A8] cursor-pointer">
                            </td>
                            <td class="p-5 whitespace-nowrap">
                                <p class="text-[11px] font-black text-slate-700">{{ $log->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-400">{{ $log->created_at->format('H:i:s') }}</p>
                            </td>
                            <td class="p-5">
                                <p class="text-xs font-black text-slate-800">{{ $log->admin_nama ?? 'Sistem' }}</p>
                                <span class="audit-badge
                                    @if($log->admin_role === 'owner') bg-indigo-50 text-indigo-600
                                    @else bg-slate-100 text-slate-500 @endif">
                                    {{ $log->admin_role ?? '-' }}
                                </span>
                            </td>
                            <td class="p-5">
                                <span class="audit-badge
                                    @if(str_contains($log->aksi,'Block') || str_contains($log->aksi,'Blokir')) bg-slate-800 text-slate-100
                                    @elseif(str_contains($log->aksi,'Buka')) bg-emerald-50 text-emerald-700
                                    @elseif(str_contains($log->aksi,'Download') || str_contains($log->aksi,'Kuitansi')) bg-blue-50 text-blue-700
                                    @elseif(str_contains($log->aksi,'Maintenance')) bg-orange-50 text-orange-700
                                    @else bg-slate-50 text-slate-600 @endif">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="p-5 max-w-xs">
                                <p class="text-[11px] text-slate-600 font-medium leading-relaxed">{{ $log->deskripsi ?? '-' }}</p>
                            </td>
                            <td class="p-5">
                                <p class="text-[11px] font-bold text-[#1265A8]">{{ $log->fasilitas_nama ?? '-' }}</p>
                            </td>
                            <td class="p-5 text-center">
                                <button onclick="deleteLog({{ $log->id }})" class="p-2 text-slate-300 hover:text-red-500 transition-colors" title="Hapus Log">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400 font-medium italic">
                                Belum ada aktivitas yang tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
            <div class="p-5 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-container') || document.querySelector('aside');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
        }

        // Selection Logic
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.log-checkbox');
        const btnBatchDelete = document.getElementById('btnBatchDelete');
        const selectedCountLabel = document.getElementById('selectedCount');

        function updateBatchButton() {
            const checkedCount = document.querySelectorAll('.log-checkbox:checked').length;
            if (checkedCount > 0) {
                btnBatchDelete.style.display = 'flex';
                selectedCountLabel.innerText = checkedCount;
            } else {
                btnBatchDelete.style.display = 'none';
            }
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBatchButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBatchButton);
        });

        // Delete Logic
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function deleteLog(id) {
            const result = await Swal.fire({
                title: 'Hapus Log ini?',
                text: "Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin/dashboard/auditLog/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire('Terhapus!', data.message, 'success');
                        document.getElementById(`log-row-${id}`).remove();
                    }
                } catch (error) {
                    Swal.fire('Error', 'Gagal menghapus log.', 'error');
                }
            }
        }

        async function deleteSelected() {
            const selectedIds = Array.from(document.querySelectorAll('.log-checkbox:checked'))
                                     .map(cb => cb.value);

            const result = await Swal.fire({
                title: 'Hapus ' + selectedIds.length + ' Log?',
                text: "Tindakan ini akan menghapus log yang dipilih secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('/admin/dashboard/auditLog/batch', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    }
                } catch (error) {
                    Swal.fire('Error', 'Gagal menghapus log massal.', 'error');
                }
            }
        }
    </script>
</body>
</html>
