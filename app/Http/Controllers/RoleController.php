<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Operation;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('operations')->get();
        $totalOperations = Operation::count();

        return response()->json([
            'success' => true,
            'data' => [
                'roles' => $roles,
                'total_operations' => $totalOperations
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    public function getPermissions($id)
    {
        $role = Role::with('operations')->find($id);
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found'], 404);
        }

        $allOperations = Operation::all();
        $assignedOperationIds = $role->operations->pluck('id')->toArray();

        // Grouping logic based on some assumed code prefixes (e.g., visitors_add, employee_edit)
        $grouped = [];
        foreach ($allOperations as $op) {
            $parts = explode('_', $op->code);
            $categoryCode = count($parts) > 0 ? $parts[0] : 'general';
            
            // Map category code to Arabic names for the UI
            $categoryName = match($categoryCode) {
                'visitor' => 'الزوار',
                'employee' => 'الموظفين',
                'permit' => 'التصاريح',
                'security' => 'الأمن',
                'department' => 'الأقسام',
                'role' => 'الأدوار',
                default => 'أخرى',
            };

            if (!isset($grouped[$categoryName])) {
                $grouped[$categoryName] = [
                    'name' => $categoryName,
                    'icon' => $categoryName == 'الزوار' ? 'people_alt' : ($categoryName == 'الموظفين' ? 'work' : 'list'),
                    'isExpanded' => true,
                    'operations' => []
                ];
            }

            $grouped[$categoryName]['operations'][] = [
                'id' => $op->id,
                'name' => $op->name_ar,
                'code' => $op->code,
                'isEnabled' => in_array($op->id, $assignedOperationIds)
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $role,
                'categories' => array_values($grouped),
                'totalAvailable' => count($allOperations),
                'totalGranted' => count($assignedOperationIds)
            ]
        ]);
    }

    public function savePermissions(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found'], 404);
        }

        $validated = $request->validate([
            'operations' => 'array',
            'operations.*' => 'exists:operations,id'
        ]);

        $role->operations()->sync($validated['operations'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully'
        ]);
    }
}
