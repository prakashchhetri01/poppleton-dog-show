<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only.'); }
require __DIR__ . '/../public/db_connect.php';
$username = trim(readline('Admin username: '));
$password = readline('Admin password (visible in terminal): ');
if ($username === '' || strlen($password) < 12) { exit("Username required; choose a password of at least 12 characters.\n"); }
$hash = password_hash($password, PASSWORD_DEFAULT);
$role = 'admin';
$stmt = $conn->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $username, $hash, $role);
if (!$stmt->execute()) { exit("Could not create admin; username may already exist.\n"); }
echo "Admin created.\n";
$stmt->close();
$conn->close();
