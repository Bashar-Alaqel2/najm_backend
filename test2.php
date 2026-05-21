<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$result = DB::select("SELECT pg_get_constraintdef(oid) as def FROM pg_constraint WHERE conname = 'employees_status_check'");
print_r($result);
