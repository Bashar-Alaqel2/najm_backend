<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:users,employee_id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'employee_id' => $validated['employee_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['success' => true, 'message' => 'تم إنشاء حساب المستخدم بنجاح', 'token' => $token, 'user' => $user]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['success' => true, 'token' => $token, 'user' => $user->load('employee')]);
        }

        return response()->json(['success' => false, 'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function user(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->user()->load('employee')]);
    }
}
