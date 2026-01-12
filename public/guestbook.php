<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$email   = $_POST['email'];
$phone   = $_POST['phone'];
$content = $_POST['content'];

DB::table('guestbook')->insert([
    'email'         => $email,
    'phone'         => $phone,
    'content'       => $content,
    'is_read'       => 0,
    'created_at'    => now(),
    'updated_at'    => now(),
]);

echo json_encode(["status" => "success", "message" => "Gửi thành công!"]);
