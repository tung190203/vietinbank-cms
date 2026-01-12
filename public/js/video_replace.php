<?php
if (!isset($_GET['path'])) {
    http_response_code(400);
    exit('Missing path');
}

$path = $_GET['path'];

// Bảo mật: chặn truy cập ra ngoài thư mục storage
$baseDir = realpath(__DIR__ . '/../storage/videos');
$filePath = realpath($baseDir . '/' . basename($path));
if (!$filePath || strpos($filePath, $baseDir) !== 0 || !file_exists($filePath)) {
    http_response_code(404);
    exit('Video not found');
}

// Thêm CORS header
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Range");
header("Content-Type: video/mp4");

// Stream video
readfile($filePath);
exit();
?>
