<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!isset($payload['full_name'], $payload['email'], $payload['password'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Missing required fields']));
}

$hash = password_hash($payload['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare('
    INSERT INTO users (full_name,email,password_hash,phone,gender,bio,profile_image)
    VALUES (:name,:email,:pwd,:phone,:gender,:bio,:img)
');
$stmt->execute([
    ':name'   => $payload['full_name'],
    ':email'  => $payload['email'],
    ':pwd'    => $hash,
    ':phone'  => $payload['phone'] ?? null,
    ':gender' => $payload['gender'] ?? null,
    ':bio'    => $payload['bio'] ?? null,
    ':img'    => $payload['profile_image'] ?? null,
]);

echo json_encode(['status' => 'registered']);
?>
