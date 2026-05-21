<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    App\Models\Employee::create([
        'full_name' => 'test3',
        'email' => 'test3@test.com',
        'phone' => '123',
        'department_id' => 1,
        'job_title_id' => 1,
        'status' => 'نشط'
    ]);
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
