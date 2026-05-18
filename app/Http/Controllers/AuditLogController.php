<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display the audit logs.
     * Restricted to Owner via middleware in web.php
     */
    public function index()
    {
        $logs = AuditLog::orderBy('created_at', 'desc')->paginate(30);
        return view('admin.dashboard.auditLog', compact('logs'));
    }

    /**
     * Delete a single audit log.
     */
    public function destroy($id)
    {
        $log = AuditLog::findOrFail($id);
        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log audit berhasil dihapus.'
        ]);
    }

    /**
     * Batch delete multiple audit logs.
     */
    public function destroyBatch(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada log yang dipilih.'
            ], 400);
        }

        AuditLog::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' log audit telah dihapus.'
        ]);
    }
}
