<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;

class MaintenanceController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::with(['requester', 'purchaser'])->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requester_id' => 'required|exists:employees,id',
            'request_type' => 'required|in:purchase,external_repair',
            'urgency' => 'nullable|in:normal,urgent',
            'item_name' => 'required|string',
            'item_type' => 'nullable|string',
            'quantity' => 'integer|min:1',
            'installation_location' => 'nullable|string',
        ]);

        $mRequest = MaintenanceRequest::create(array_merge($validated, ['status' => 'pending_approval']));
        return response()->json(['success' => true, 'message' => 'تم إنشاء طلب الصيانة/الشراء بنجاح', 'data' => $mRequest]);
    }

    public function approve($id)
    {
        $mRequest = MaintenanceRequest::findOrFail($id);
        $mRequest->update(['status' => 'approved', 'approved_at' => now()]);
        return response()->json(['success' => true, 'message' => 'تمت الموافقة على الطلب']);
    }

    public function assignPurchaser(Request $request, $id)
    {
        $validated = $request->validate([
            'purchaser_id' => 'required|exists:employees,id'
        ]);

        $mRequest = MaintenanceRequest::findOrFail($id);
        $mRequest->update([
            'purchaser_id' => $validated['purchaser_id'],
            'status' => 'assigned',
            'assigned_at' => now()
        ]);
        return response()->json(['success' => true, 'message' => 'تم تعيين مسؤول المشتريات']);
    }

    public function executePurchase($id)
    {
        $mRequest = MaintenanceRequest::findOrFail($id);
        $mRequest->update(['status' => 'purchased', 'purchased_at' => now()]);
        return response()->json(['success' => true, 'message' => 'تم تأكيد الشراء، بانتظار التفتيش في البوابة']);
    }

    public function verifyAtGate($id)
    {
        $mRequest = MaintenanceRequest::findOrFail($id);
        $mRequest->update(['status' => 'at_gate', 'gate_checked_at' => now()]);
        return response()->json(['success' => true, 'message' => 'تم تفتيش القطع بالبوابة ودخولها']);
    }

    public function confirmReceipt($id)
    {
        $mRequest = MaintenanceRequest::findOrFail($id);
        $mRequest->update(['status' => 'received', 'received_at' => now()]);
        return response()->json(['success' => true, 'message' => 'تم استلام القطع بنجاح']);
    }
}
