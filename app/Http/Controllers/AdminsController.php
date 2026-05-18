<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admins;
use App\Models\Fasilitas;
use App\Models\Booking;
use App\Models\Penyewa;
use App\Models\HargaSewaHistory;

class AdminsController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari admin berdasarkan username
        $admin = Admins::where('username', $request->username)->first();

        // Cek username & password (tanpa hash)
        if ($admin && $request->password === $admin->password) {
            
            // CEK PERSISTENT FORCE LOGOUT (BANNED/BLOCKED BY OWNER)
            if ($admin->force_logout && $admin->logout_type === 'manual') {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'login' => ['Anda telah dikeluarkan paksa dari sistem oleh owner, silakan hubungi owner.']
                    ]
                ], 401);
            }

            // Jika logout karena update, izinkan login kembali dan reset status
            if ($admin->force_logout && $admin->logout_type === 'update') {
                $admin->update(['force_logout' => false, 'logout_type' => null]);
            }

            // Simpan session
            $request->session()->put('id_log', $admin->id_log);
            $request->session()->put('nama', $admin->nama);
            $request->session()->put('role', $admin->role);
            $request->session()->put('can_edit', $admin->can_edit);

            return response()->json([
                'success' => true,
                'redirect' => route('dashboardMaster')
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => [
                'login' => ['Username atau password salah!']
            ]
        ], 422);
    }

    public function logout(Request $request)
    {
        if ($request->session()->has('id_log')) {
            $admin = Admins::find($request->session()->get('id_log'));
            if ($admin) {
                $admin->update(['force_logout' => false]);
            }
        }
        $request->session()->forget(['id_log', 'nama', 'role', 'can_edit']);
        $request->session()->flush();

        return redirect()->route('login');
    }

    public function store(Request $request)
    {
        if (session('role') !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username',
            'password' => 'required|string|min:6',
        ]);

        $can_edit = $request->has('can_edit') ? filter_var($request->can_edit, FILTER_VALIDATE_BOOLEAN) : false;

        $admin = Admins::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => $request->password,
            'role' => 'admin',
            'can_edit' => $can_edit,
            'force_logout' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin Berhasil Ditambahkan',
            'admin' => $admin,
        ]);
    }

    // Password hanya diupdate jika diisi di form
    public function update(Request $request, $id_log)
    {
        try {
            // contoh update
            $admin = Admins::where('id_log', $id_log)->first();

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $admin->update([
                // isi field kamu
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Tambahkan fungsi ini di dalam AdminsController
    public function manage($id_log)
    {
        // Mengambil data admin berdasarkan primary key id_log
        $admin = Admins::findOrFail($id_log);

        // Meneruskan variabel $admin ke view
        return view('admin.dashboard.management.manage_admin_control', compact('admin'));
    }

    public function index()
    {
        // 1. Ambil Data Statistik untuk Summary Card
        $totalFasilitas = Fasilitas::count();
        $totalBooking = Booking::count();
        // Hitung total booking yang sudah di-approve sebagai representasi rekap data penyewa
        $totalPenyewa = Booking::where('status', 'confirmed')->count();

        // 2. Ambil data untuk Chart Fasilitas
        $fasilitas = Fasilitas::withCount('booking')->get();

        // Label: Nama Fasilitas
        $labelsFasilitas = $fasilitas->pluck('nama_fasilitas')->toArray();

        // Data: Jumlah booking per fasilitas
        $dataBookingPerFasilitas = $fasilitas->pluck('booking_count')->toArray();

        // 3. Kirim semua data ke satu view dashboard
        return view('admin.dashboard.index', compact(
            'totalFasilitas',
            'totalBooking',
            'totalPenyewa',
            'labelsFasilitas',
            'dataBookingPerFasilitas'
        ));
    }

    /**
     * Menampilkan Daftar Admin (Dipisahkan dari index dashboard)
     */
    public function adminActiveControl()
    {
        $admins = Admins::all();
        return view('admin.dashboard.management.admin_active_control', compact('admins'));
    }

    public function view($id_log)
    {
        $admin = Admins::findOrFail($id_log);
        return view('admin.dashboard.management.view_admin', compact('admin'));
    }

    // Role Management Methods
    public function updatePermissions(Request $request, $id_log)
    {
        if (session('role') !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $admin = Admins::findOrFail($id_log);
        
        // Prevent editing another owner's permissions unless specifically needed, but we allow it for now.
        $can_edit = filter_var($request->can_edit, FILTER_VALIDATE_BOOLEAN);
        $admin->update(['can_edit' => $can_edit]);

        // Trigger auto-logout for the updated admin (if not self)
        if ($admin->id_log != session('id_log')) {
            $admin->update(['force_logout' => true, 'logout_type' => 'update']);
        }

        return response()->json(['success' => true, 'message' => 'Permissions updated successfully.']);
    }

    public function promoteToOwner($id_log)
    {
        if (session('role') !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $admin = Admins::findOrFail($id_log);
        $admin->update(['role' => 'owner', 'can_edit' => true]);

        // Trigger auto-logout for the updated admin (if not self)
        if ($admin->id_log != session('id_log')) {
            $admin->update(['force_logout' => true, 'logout_type' => 'update']);
        }

        return response()->json(['success' => true, 'message' => 'Admin promoted to Owner successfully.']);
    }

    public function forceLogoutAdmin($id_log)
    {
        if (session('role') !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $admin = Admins::findOrFail($id_log);
        // Kita tidak menendang owner utama dari dirinya sendiri
        if ($admin->id_log == session('id_log')) {
             return response()->json(['success' => false, 'message' => 'Cannot logout yourself from here.']);
        }
        
        $newStatus = !$admin->force_logout;
        $admin->update([
            'force_logout' => $newStatus,
            'logout_type' => $newStatus ? 'manual' : null
        ]);

        $msg = $newStatus ? 'Admin has been Force Logged Out and Blocked.' : 'Admin is now allowed to Login Back.';
        return response()->json(['success' => true, 'message' => $msg, 'force_logout' => $newStatus]);
    }

    public function destroyAdmin($id_log)
    {
        if (session('role') !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $admin = Admins::findOrFail($id_log);
        
        // Prevent deleting oneself
        if ($admin->id_log == session('id_log')) {
             return response()->json(['success' => false, 'message' => 'Cannot delete your own account.']);
        }

        $admin->delete();

        return response()->json(['success' => true, 'message' => 'Admin account deleted permanently.']);
    }

    public function updateAdminCredentials(Request $request, $id_log)
    {
        if (session('role') !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username,'.$id_log.',id_log',
            'password' => 'nullable|string|min:6',
        ]);

        $admin = Admins::findOrFail($id_log);
        $admin->nama = $request->nama;
        $admin->username = $request->username;
        
        if ($request->filled('password')) {
            $admin->password = $request->password;
        }

        // Only update can_edit if it's an admin role (owner always has full access)
        if ($admin->role === 'admin' && $request->has('can_edit')) {
            $admin->can_edit = filter_var($request->can_edit, FILTER_VALIDATE_BOOLEAN);
        }
        
        $admin->save();

        // Trigger auto-logout for the updated admin (if not self)
        if ($admin->id_log != session('id_log')) {
            $admin->update(['force_logout' => true, 'logout_type' => 'update']);
        } else {
            // Update current session if self
            session(['nama' => $admin->nama]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Akun berhasil diperbarui.',
            'admin' => $admin
        ]);
    }

}