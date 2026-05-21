<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorInvitation;

class VisitorController extends Controller
{
    public function index()
    {
        $visitors = Visitor::with('invitation')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $visitors]);
    }

    public function createInvitation(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string',
            'inviter_id' => 'required|string', // uuid من auth.users
            'expected_date' => 'required|date',
        ]);

        // Barcode UUID يتم توليده تلقائياً في قاعدة البيانات أو يمكن إنشاؤه هنا
        $invitation = VisitorInvitation::create($validated);
        return response()->json(['success' => true, 'message' => 'تم إنشاء الدعوة للزائر', 'data' => $invitation]);
    }

    public function register(Request $request)
    {
        // تسجيل دخول الزائر الفعلي
        $validated = $request->validate([
            'visitor_name' => 'required|string',
            'identifier' => 'nullable|string',
            'purpose' => 'nullable|string',
            'invitation_id' => 'nullable|exists:visitor_invitations,id',
        ]);

        $visitor = Visitor::create(array_merge($validated, ['entry_time' => now(), 'status' => 'inside']));

        if ($request->invitation_id) {
            VisitorInvitation::where('id', $request->invitation_id)->update(['is_used' => true]);
        }

        return response()->json(['success' => true, 'message' => 'تم تسجيل دخول الزائر', 'data' => $visitor]);
    }

    public function checkout($id)
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->update(['exit_time' => now(), 'status' => 'departed']);
        return response()->json(['success' => true, 'message' => 'تم تسجيل خروج الزائر']);
    }
}
