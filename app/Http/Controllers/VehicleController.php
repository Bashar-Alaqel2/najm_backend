<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleLog;

class VehicleController extends Controller
{
    public function index()
    {
        $logs = VehicleLog::orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $logs]);
    }

    public function requestEntry(Request $request)
    {
        $validated = $request->validate([
            'driver_name' => 'required|string',
            'vehicle_type' => 'required|string',
            'plate_number' => 'required|string',
            'purpose' => 'required|string',
            'guard_id' => 'required|string',
        ]);

        $log = VehicleLog::create(array_merge($validated, ['status' => 'pending_approval']));
        return response()->json(['success' => true, 'message' => 'تم رفع طلب دخول المركبة للإدارة', 'data' => $log]);
    }

    public function approveEntry($id)
    {
        $log = VehicleLog::findOrFail($id);
        $log->update(['status' => 'approved']);
        return response()->json(['success' => true, 'message' => 'تمت الموافقة على دخول المركبة']);
    }

    public function logMovement(Request $request, $id)
    {
        $log = VehicleLog::findOrFail($id);
        $action = $request->input('action'); // 'entry' or 'exit'

        if ($action === 'entry') {
            $log->update(['entry_time' => now(), 'status' => 'inside']);
        } elseif ($action === 'exit') {
            $log->update(['exit_time' => now(), 'status' => 'departed']);
        }

        return response()->json(['success' => true, 'message' => 'تم تسجيل حركة المركبة']);
    }
}
