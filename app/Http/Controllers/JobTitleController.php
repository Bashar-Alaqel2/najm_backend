<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobTitle;

class JobTitleController extends Controller
{
    public function index()
    {
        // نُحضر جميع المسميات الوظيفية مع اسم القسم التابعة له
        $jobTitles = JobTitle::with('department:id,name_ar')->get();
        return response()->json(['success' => true, 'data' => $jobTitles]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $jobTitle = JobTitle::create($validated);
        $jobTitle->load('department:id,name_ar');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المسمى الوظيفي بنجاح',
            'data' => $jobTitle
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $jobTitle = JobTitle::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'department_id' => 'sometimes|required|exists:departments,id',
        ]);

        $jobTitle->update($validated);
        $jobTitle->load('department:id,name_ar');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المسمى الوظيفي بنجاح',
            'data' => $jobTitle
        ]);
    }

    public function destroy($id)
    {
        $jobTitle = JobTitle::findOrFail($id);
        $jobTitle->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المسمى الوظيفي بنجاح'
        ]);
    }
}
