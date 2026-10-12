<?php
mysqli_report(MYSQLI_REPORT_OFF);
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME') ?: 'poppleton';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) { http_response_code(500); exit('Database connection failed. Check local configuration.'); }
$conn->set_charset('utf8mb4');
