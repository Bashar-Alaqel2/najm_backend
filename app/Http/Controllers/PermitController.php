<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;

class PermitController extends Controller
{
    public function index()
    {
        // جلب جميع التصاريح مع بيانات الموظف
        $permits = Permit::with('employee')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $permits]);
    }

    public function store(Request $request)
    {
        // طلب تصريح جديد
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'expected_exit_time' => 'required|date',
            'expected_return_time' => 'required|date|after:expected_exit_time',
            'reason' => 'nullable|string',
        ]);

        $permit = Permit::create($validated);
        return response()->json(['success' => true, 'message' => 'تم رفع طلب التصريح بنجاح', 'data' => $permit]);
    }

    public function approve($id)
    {
        $permit = Permit::findOrFail($id);
        $permit->update(['status' => 'approved']);
        return response()->json(['success' => true, 'message' => 'تم الموافقة على التصريح']);
    }

    public function reject($id)
    {
        $permit = Permit::findOrFail($id);
        $permit->update(['status' => 'rejected']);
        return response()->json(['success' => true, 'message' => 'تم رفض التصريح']);
    }

    public function recordExit($id)
    {
        $permit = Permit::findOrFail($id);
        $permit->update([
            'actual_exit_time' => now(),
            'status' => 'exited'
        ]);
        return response()->json(['success' => true, 'message' => 'تم تسجيل خروج الموظف']);
    }

    public function recordReturn($id)
    {
        $permit = Permit::findOrFail($id);
        // يمكنك هنا استدعاء الـ RPC الخاصة بـ Supabase أو حساب التأخير برمجياً في Laravel
        $permit->update([
            'actual_return_time' => now(),
            'status' => 'returned'
        ]);
        return response()->json(['success' => true, 'message' => 'تم تسجيل عودة الموظف']);
    }
}
