<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class FasilitasController extends Controller
{

    public function index()
    {
        // Mengambil semua data dari model Facility
        $facilities = Fasilitas::all(); 

        $today = now()->startOfDay();
        foreach ($facilities as $f) {
            $f->is_maintenance = \App\Models\JadwalBlokir::where('fasilitas_id', $f->id)
                ->where('tipe', 'maintenance')
                ->where('tgl_mulai', '<=', $today)
                ->where('tgl_selesai', '>=', $today)
                ->exists();
        }

        // Pastikan nama variabel di compact('facilities') sesuai dengan @foreach($facilities as $item)
        return view('admin.dashboard.dataFasilitas', compact('facilities'));
    }

    public function update(Request $request, $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:asrama,aula',
            'deskripsi' => 'required',
            'detail' => 'nullable',
            'harga' => 'required|numeric',
            'harga_bulanan' => 'nullable|numeric',
            'max_dewasa' => 'nullable|integer',
            'max_anak' => 'nullable|integer',
            'max_durasi_harian' => 'nullable|integer',
            'jam_operasional' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'paket_harian' => 'nullable|string',
            'labels' => 'nullable|array',
        ]);

        $oldHarga = $fasilitas->harga;
        $newHarga = $request->harga;

        if ($oldHarga != $newHarga) {
            $diff = $newHarga - $oldHarga;
            $percent = ($oldHarga != 0) ? ($diff / $oldHarga) * 100 : 100;
            $percentFormatted = ($percent > 0 ? '+' : '') . round($percent) . '%';

            \App\Models\HargaSewaHistory::create([
                'fasilitas_id' => $fasilitas->id,
                'harga_lama' => $oldHarga,
                'harga_baru' => $newHarga,
                'persen_perubahan' => $percentFormatted,
            ]);
        }

        $paket_harian = []; // UI removed, default to empty
        
        // Calculate thumbnail price range
        $prices = [$newHarga];
        if ($request->harga_bulanan) $prices[] = $request->harga_bulanan;
        
        $minPrice = min($prices);
        $maxPrice = max($prices);

        $formatPrice = function($price) {
            if ($price >= 1000000) return round($price / 1000000, 1) . 'JT';
            if ($price >= 1000) return round($price / 1000) . 'K';
            return $price;
        };

        $harga_thumbnail = (count($prices) > 1) 
            ? "Mulai " . $formatPrice($minPrice) . " - " . $formatPrice($maxPrice)
            : "Rp " . number_format($newHarga, 0, ',', '.');

        $data = [
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'detail' => $request->detail,
            'harga' => $request->harga,
            'harga_bulanan' => $request->harga_bulanan,
            'max_dewasa' => $request->max_dewasa,
            'max_anak' => $request->max_anak,
            'max_durasi_harian' => $request->max_durasi_harian,
            'jam_operasional' => $request->jam_operasional,
            'paket_harian' => $paket_harian,
            'labels' => $request->labels ?? [],
            'harga_thumbnail' => $harga_thumbnail,
        ];

        if ($request->hasFile('image')) {
            $oldPath = public_path('storage/fasilitas/' . $fasilitas->image);
            if (File::exists($oldPath)) File::delete($oldPath);

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/fasilitas'), $imageName);
            $data['image'] = $imageName;
        }

        // Handle Gallery
        $gallery = $fasilitas->gallery ?? [];
        if ($request->hasFile('gallery')) {
            // Delete old gallery if new ones are uploaded (or just replace, but user said UX 3 boxes)
            // For simplicity, we replace if index matches or just append
            // User requested 3 boxes, so we'll expect array of files
            foreach ($request->file('gallery') as $index => $file) {
                if ($file) {
                    $name = time() . '_gallery_' . $index . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('storage/fasilitas/gallery'), $name);
                    
                    // Replace if exists at index
                    $gallery[$index] = $name;
                }
            }
        }
        $data['gallery'] = array_values(array_filter($gallery));

        $fasilitas->update($data);

        // Audit Log
        AuditLog::catat(
            'Update Fasilitas',
            "Mengubah data fasilitas: {$fasilitas->nama}",
            ['target_tipe' => 'fasilitas', 'target_id' => $fasilitas->id, 'fasilitas_nama' => $fasilitas->nama]
        );

        return redirect()->route('fasilitas.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function edit($id) {
        $fasilitas = Fasilitas::findOrFail($id);
        return view('admin.dashboard.edit.editFasilitas', compact('fasilitas'));
    }

    public function destroy($id) {
        $fasilitas = Fasilitas::findOrFail($id);
        if ($fasilitas->image) {
            Storage::delete('public/fasilitas/' . $fasilitas->image);
        }
        // Also delete gallery
        if ($fasilitas->gallery) {
            foreach ($fasilitas->gallery as $img) {
                Storage::delete('public/fasilitas/gallery/' . $img);
            }
        }
        $fasilitas->delete();

        // Audit Log
        AuditLog::catat(
            'Hapus Fasilitas',
            "Menghapus fasilitas: {$fasilitas->nama}",
            ['target_tipe' => 'fasilitas', 'target_id' => $id, 'fasilitas_nama' => $fasilitas->nama]
        );

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama'              => 'required|string|max:255',
                'tipe'              => 'required|in:asrama,aula',
                'deskripsi'         => 'required',
                'labels'            => 'nullable|array',
                'jam_operasional'   => 'nullable|string',
                'max_durasi_harian' => 'nullable|integer',
                'image'             => 'required|image|mimes:jpeg,png,jpg|max:2048',

                'room_types'                    => 'required|array|min:1',
                'room_types.*.nama'             => 'required|string|max:255',
                'room_types.*.deskripsi'        => 'nullable|string',
                'room_types.*.max_dewasa'       => 'nullable|integer|min:0',
                'room_types.*.max_anak'         => 'nullable|integer|min:0',
                'room_types.*.harga_harian'     => 'required|numeric|min:0',
                'room_types.*.harga_mingguan'   => 'nullable|numeric|min:0',
                'room_types.*.harga_bulanan'    => 'nullable|numeric|min:0',
                'room_types.*.harga_tahunan'    => 'nullable|numeric|min:0',
                'room_types.*.stok'             => 'required|integer|min:1',
                'room_types.*.nomor_kamar'      => 'nullable|array',
                'room_types.*.gallery.*'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            DB::beginTransaction();

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('fasilitas', 'public');
                $imageName = basename($path);
            }

            $allPrices = [];
            foreach ($request->room_types as $rt) {
                $allPrices[] = (float) $rt['harga_harian'];
                if (!empty($rt['harga_mingguan'])) $allPrices[] = (float) $rt['harga_mingguan'];
                if (!empty($rt['harga_bulanan'])) $allPrices[] = (float) $rt['harga_bulanan'];
                if (!empty($rt['harga_tahunan'])) $allPrices[] = (float) $rt['harga_tahunan'];
            }

            $formatPrice = function ($price) {
                if ($price >= 1000000) return round($price / 1000000, 1) . 'JT';
                if ($price >= 1000) return round($price / 1000) . 'K';
                return $price;
            };

            $minPrice = min($allPrices);
            $maxPrice = max($allPrices);
            $harga_thumbnail = ($minPrice !== $maxPrice)
                ? "Mulai " . $formatPrice($minPrice) . " - " . $formatPrice($maxPrice)
                : "Rp " . number_format($minPrice, 0, ',', '.');

            $fasilitas = Fasilitas::create([
                'nama'              => $request->nama,
                'tipe'              => $request->tipe,
                'deskripsi'         => $request->deskripsi,
                'detail'            => $request->detail,
                'harga'             => $minPrice,
                'harga_bulanan'     => null,
                'max_dewasa'        => null,
                'max_anak'          => null,
                'max_durasi_harian' => $request->max_durasi_harian,
                'jam_operasional'   => $request->jam_operasional,
                'image'             => $imageName,
                'gallery'           => [],
                'paket_harian'      => [],
                'labels'            => $request->labels ?? [],
                'harga_thumbnail'   => $harga_thumbnail,
            ]);

            foreach ($request->room_types as $index => $rtData) {
                $gallery = [];
                $fileKey = "room_types.{$index}.gallery";

                if ($request->hasFile("room_types.{$index}.gallery")) {
                    foreach ($request->file("room_types.{$index}.gallery") as $gi => $file) {
                        if ($file) {
                            $path = $file->store('fasilitas/gallery', 'public');
                            $gallery[] = basename($path);
                        }
                    }
                }

                $roomType = RoomType::create([
                    'fasilitas_id'   => $fasilitas->id,
                    'nama'           => $rtData['nama'],
                    'deskripsi'      => $rtData['deskripsi'] ?? null,
                    'max_dewasa'     => $rtData['max_dewasa'] ?? 0,
                    'max_anak'       => $rtData['max_anak'] ?? 0,
                    'harga_harian'   => $rtData['harga_harian'],
                    'harga_mingguan' => $rtData['harga_mingguan'] ?? null,
                    'harga_bulanan'  => $rtData['harga_bulanan'] ?? null,
                    'harga_tahunan'  => $rtData['harga_tahunan'] ?? null,
                    'gallery'        => $gallery,
                    'stok'           => $rtData['stok'],
                ]);

                $nomorList = $rtData['nomor_kamar'] ?? [];
                $stok = (int) $rtData['stok'];

                for ($r = 0; $r < $stok; $r++) {
                    Room::create([
                        'room_type_id' => $roomType->id,
                        'nomor_kamar'  => $nomorList[$r] ?? ($rtData['nama'] . ' - ' . ($r + 1)),
                    ]);
                }
            }

            DB::commit();

            AuditLog::catat(
                'Tambah Fasilitas',
                "Menambahkan fasilitas baru: {$request->nama}",
                ['target_tipe' => 'fasilitas', 'target_id' => $fasilitas->id, 'fasilitas_nama' => $request->nama]
            );

            return response()->json(['success' => 'Data fasilitas berhasil disimpan!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Fasilitas Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updatePaketHarian(Request $request, $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        $request->validate([
            'paket_harian' => 'nullable|string',
        ]);

        $fasilitas->update([
            'paket_harian' => $request->paket_harian ? json_decode($request->paket_harian, true) : [],
        ]);

        return redirect()->back()->with('success', 'Paket harian berhasil diperbarui!');
    }

    public function storeMaintenance(Request $request, $id)
    {
        try {
            $request->validate([
                "tgl_mulai" => "required|date|after_or_equal:today",
                "tgl_selesai" => "required|date|after_or_equal:tgl_mulai",
                "tujuan" => "required|string|max:255",
            ]);

            $fasilitas = Fasilitas::findOrFail($id);
            $start = \Carbon\Carbon::parse($request->tgl_mulai)->startOfDay();
            $end = \Carbon\Carbon::parse($request->tgl_selesai)->endOfDay();

            $overlaps = \App\Models\Booking::where("fasilitas_id", $id)
                ->whereIn("status", ["pending", "confirmed", "booked"])
                ->where(function($q) use ($start, $end) {
                    $q->whereBetween("tgl_mulai", [$start, $end])
                      ->orWhereBetween("tgl_selesai", [$start, $end])
                      ->orWhere(function($q2) use ($start, $end) {
                          $q2->where("tgl_mulai", "<=", $start)
                             ->where("tgl_selesai", ">=", $end);
                      });
                })
                ->get();

            if ($overlaps->count() > 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Gagal! Terdapat " . $overlaps->count() . " reservasi aktif pada rentang tanggal tersebut."
                ], 422);
            }

            \App\Models\JadwalBlokir::create([
                "fasilitas_id" => $id,
                "tgl_mulai" => $start,
                "tgl_selesai" => $end,
                "tipe" => "maintenance",
                "tujuan" => $request->tujuan,
                "created_by" => session("nama") ?? "System Admin",
            ]);

            \App\Models\AuditLog::catat(
                "Maintenance Facility",
                "Mengaktifkan mode perbaikan untuk: {$fasilitas->nama} ({$request->tgl_mulai} s/d {$request->tgl_selesai})",
                ["target_tipe" => "fasilitas", "target_id" => $id, "reason" => $request->tujuan]
            );

            return response()->json(["success" => true, "message" => "Mode perbaikan berhasil diaktifkan!"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()], 500);
        }
    }

    public function cancelMaintenance($id)
    {
        try {
            $fasilitas = Fasilitas::findOrFail($id);
            $today = now()->startOfDay();

            // Cari record maintenance yang aktif atau akan datang
            $deletedCount = \App\Models\JadwalBlokir::where('fasilitas_id', $id)
                ->where('tipe', 'maintenance')
                ->where('tgl_selesai', '>=', $today)
                ->delete();

            if ($deletedCount > 0) {
                \App\Models\AuditLog::catat(
                    "Cancel Maintenance",
                    "Membatalkan mode perbaikan untuk: {$fasilitas->nama}. Fasilitas sekarang siap digunakan kembali.",
                    ["target_tipe" => "fasilitas", "target_id" => $id, "fasilitas_nama" => $fasilitas->nama]
                );

                return response()->json([
                    "success" => true,
                    "message" => "Mode perbaikan berhasil dibatalkan! Fasilitas kini tersedia kembali."
                ]);
            }

            return response()->json([
                "success" => false,
                "message" => "Tidak ditemukan jadwal perbaikan aktif untuk fasilitas ini."
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Terjadi kesalahan sistem: " . $e->getMessage()
            ], 500);
        }
    }
}
