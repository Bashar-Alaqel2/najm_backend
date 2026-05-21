<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Employee;
use App\Models\User;
use App\Models\Permit;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. الأقسام (Departments)
        $departments = [
            ['name_ar' => 'الإدارة العامة'],
            ['name_ar' => 'الموارد البشرية (HR)'],
            ['name_ar' => 'قسم الحراسة '],
            ['name_ar' => 'العمليات والصيانة'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name_ar' => $dept['name_ar']], $dept);
        }

        // 2. المسميات الوظيفية (Job Titles)
        $jobTitles = [
            ['name_ar' => 'مدير نظام'],
            ['name_ar' => 'مشرف أمن'],
            ['name_ar' => 'حارس أمن'],
            ['name_ar' => 'أخصائي موارد بشرية'],
            ['name_ar' => 'موظف عادي'],
        ];

        foreach ($jobTitles as $job) {
            JobTitle::firstOrCreate(['name_ar' => $job['name_ar']], $job);
        }

        // 3. الأدوار والصلاحيات (Roles)
        $roles = [
            ['name_ar' => 'المدير العام (Admin)', 'description' => 'صلاحيات كاملة على النظام'],
            ['name_ar' => 'مشرف (Supervisor)', 'description' => 'إدارة الحراس والموظفين'],
            ['name_ar' => 'حارس أمن (Guard)', 'description' => 'صلاحيات البوابات والزوار'],
            ['name_ar' => 'موظف HR', 'description' => 'إدارة بيانات الموظفين'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name_ar' => $role['name_ar']], $role);
        }

        // 4. إنشاء بعض الموظفين الوهميين (Employees & Users)
        $securityDept = Department::where('name_ar', 'قسم الحراسة ')->first();
        $hrDept = Department::where('name_ar', 'الموارد البشرية (HR)')->first();
        $adminDept = Department::where('name_ar', 'الإدارة العامة')->first();

        $adminRole = Role::where('name_ar', 'المدير العام (Admin)')->first();
        $guardRole = Role::where('name_ar', 'حارس أمن (Guard)')->first();
        $hrRole = Role::where('name_ar', 'موظف HR')->first();

        $guardTitle = JobTitle::where('name_ar', 'حارس أمن')->first();
        $hrTitle = JobTitle::where('name_ar', 'أخصائي موارد بشرية')->first();
        $adminTitle = JobTitle::where('name_ar', 'مدير نظام')->first();

        $dummyEmployees = [
            [
                'full_name' => 'عادل المسؤول',
                'email' => 'admin@najm.com',
                'phone' => '0500000001',
                'department_id' => $adminDept->id,
                'job_title_id' => $adminTitle->id,
                'role_id' => $adminRole->id,
            ],
            [
                'full_name' => 'سالم الحارس',
                'email' => 'guard@najm.com',
                'phone' => '0500000002',
                'department_id' => $securityDept->id,
                'job_title_id' => $guardTitle->id,
                'role_id' => $guardRole->id,
            ],
            [
                'full_name' => 'فاطمة الموارد',
                'email' => 'hr@najm.com',
                'phone' => '0500000003',
                'department_id' => $hrDept->id,
                'job_title_id' => $hrTitle->id,
                'role_id' => $hrRole->id,
            ],
        ];

        foreach ($dummyEmployees as $empData) {
            $role_id = $empData['role_id'];
            unset($empData['role_id']); // Remove it before passing to Employee creation
            
            $emp = Employee::firstOrCreate(
                ['email' => $empData['email']], 
                array_merge($empData, ['status' => 'نشط'])
            );

            // إنشاء حساب مستخدم
            User::firstOrCreate(
                ['email' => $empData['email']],
                [
                    'name' => $empData['full_name'],
                    'password' => Hash::make('123456'), // كلمة مرور موحدة للكل
                    'employee_id' => $emp->id,
                    'role_id' => $role_id,
                ]
            );
        }

        // 5. تصاريح خروج تجريبية
        $guardEmployee = Employee::where('email', 'guard@najm.com')->first();
        $hrEmployee = Employee::where('email', 'hr@najm.com')->first();

        Permit::firstOrCreate([
            'employee_id' => $hrEmployee->id,
            'reason' => 'اجتماع خارجي مع الموردين',
        ], [
            'expected_exit_time' => now()->addHours(1),
            'expected_return_time' => now()->addHours(3),
            'status' => 'pending'
        ]);

        Permit::firstOrCreate([
            'employee_id' => $guardEmployee->id,
            'reason' => 'ظرف عائلي طارئ',
        ], [
            'expected_exit_time' => now()->subHours(2),
            'expected_return_time' => now()->addHours(1),
            'status' => 'approved'
        ]);
        
        Permit::firstOrCreate([
            'employee_id' => $hrEmployee->id,
            'reason' => 'زيارة بنكية',
        ], [
            'expected_exit_time' => now()->subHours(5),
            'expected_return_time' => now()->subHours(3),
            'status' => 'returned',
            'actual_exit_time' => now()->subHours(5)->addMinutes(5),
            'actual_return_time' => now()->subHours(3)->addMinutes(10),
        ]);
    }
}
