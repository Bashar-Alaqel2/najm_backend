<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        $dept = Department::firstOrCreate(
            ['name_ar' => 'قسم الإدارة'],
            ['description' => 'قسم الإدارة العامة']
        );

        $emp = Employee::firstOrCreate(
            ['email' => 'admin@najm.com'],
            [
                'full_name' => 'مدير النظام',
                'department_id' => $dept->id,
                'phone' => '0500000000',
                'status' => 'active',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'admin@najm.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password123'),
                'employee_id' => $emp->id,
            ]
        );

        echo "Test User Created Successfully!\n";
        echo "Email: admin@najm.com\n";
        echo "Password: password123\n";
    }
}
