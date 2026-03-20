<?php
require_once 'config.php';
require_once 'auth.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!isset($payload['email'], $payload['password'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Missing email or password']));
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$payload['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($payload['password'], $user['password_hash'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Invalid credentials']));
}

$token = createToken((int)$user['id']);

echo json_encode(['token' => $token]);
?>
