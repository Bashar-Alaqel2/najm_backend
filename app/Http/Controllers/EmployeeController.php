<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'jobTitle', 'supervisor'])->get();
        return response()->json(['success' => true, 'data' => $employees]);
    }

    public function show($id)
    {
        $employee = Employee::with(['department', 'jobTitle', 'supervisor', 'user.role'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $employee]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
            'job_title_id' => 'required|exists:job_titles,id',
            'status' => 'nullable|string',
            'photo' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Employee
            $employee = Employee::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'department_id' => $validated['department_id'],
                'job_title_id' => $validated['job_title_id'],
                'status' => $validated['status'] ?? 'active',
                'photo' => $validated['photo'] ?? null,
            ]);

            // 2. Create User Account
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'employee_id' => $employee->id,
                'role_id' => $validated['role_id'],
            ]);

            // Optional: link user_id back to employee if needed
            $employee->user_id = $user->id;
            $employee->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully',
                'data' => $employee->load(['department', 'jobTitle', 'user.role'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create employee: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:employees,email,'.$id,
            'phone' => 'nullable|string',
            'role_id' => 'sometimes|exists:roles,id',
            'department_id' => 'sometimes|exists:departments,id',
            'job_title_id' => 'sometimes|exists:job_titles,id',
            'status' => 'nullable|string',
            'photo' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $employee->update($validated);

            if ($employee->user) {
                $userData = [];
                if (isset($validated['full_name'])) $userData['name'] = $validated['full_name'];
                if (isset($validated['email'])) $userData['email'] = $validated['email'];
                if (isset($validated['role_id'])) $userData['role_id'] = $validated['role_id'];
                
                $employee->user->update($userData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully',
                'data' => $employee->load(['department', 'jobTitle', 'user.role'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update employee'], 500);
        }
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Instead of hard delete, maybe just change status
        $employee->status = 'موقوف';
        $employee->save();

        return response()->json(['success' => true, 'message' => 'Employee suspended successfully']);
    }
}
