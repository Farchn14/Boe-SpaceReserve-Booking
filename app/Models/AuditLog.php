<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'admin_nama',
        'admin_role',
        'aksi',
        'target_tipe',
        'target_id',
        'deskripsi',
        'fasilitas_nama',
        'ip_address',
    ];

    /**
     * Helper static method for easy logging
     */
    public static function catat(string $aksi, string $deskripsi, array $extra = []): self
    {
        return self::create(array_merge([
            'admin_nama'     => session('admin_nama') ?? session('nama') ?? 'Sistem',
            'admin_role'     => session('role') ?? '-',
            'aksi'           => $aksi,
            'deskripsi'      => $deskripsi,
            'ip_address'     => request()->ip(),
        ], $extra));
    }
}
