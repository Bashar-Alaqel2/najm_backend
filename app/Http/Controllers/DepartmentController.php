<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        // نُحضر جميع الأقسام مع معلومات المدير الخاص بكل قسم وعدد الموظفين
        $departments = Department::with('manager:id,full_name')->withCount('employees')->get();
        return response()->json(['success' => true, 'data' => $departments]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
        ]);

        $department = Department::create($validated);
        
        // جلب القسم مع مديره لإعادته في الاستجابة
        $department->load('manager:id,full_name');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة القسم بنجاح',
            'data' => $department
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name_ar' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
        ]);

        $department->update($validated);
        $department->load('manager:id,full_name');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات القسم بنجاح',
            'data' => $department
        ]);
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف القسم بنجاح'
        ]);
    }
}
