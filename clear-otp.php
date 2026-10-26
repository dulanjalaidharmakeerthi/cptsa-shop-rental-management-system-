<?php
require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

use Illuminate\Support\Facades\DB;

DB::table('password_reset_requests')->where('email', 'piumikad678@gmail.com')->delete();
echo "Cleared all OTP records for piumikad678@gmail.com\n";
