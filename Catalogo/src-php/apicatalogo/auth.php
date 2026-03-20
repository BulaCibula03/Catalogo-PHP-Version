<?php
require_once __DIR__.'/config.php';

header('Access-Control-Allow-Origin: http://localhost:5173');   // Vite dev
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

function createToken(int $userId): string {
    global $pdo;
    $raw   = bin2hex(random_bytes(32));          // 64‑char hex
    $exp   = date('Y-m-d H:i:s', time() + TOKEN_TTL_SECONDS);
    $stmt  = $pdo->prepare('INSERT INTO api_tokens (token,user_id,expires_at) VALUES (?,?,?)');
    $stmt->execute([$raw, $userId, $exp]);
    return $raw;
}

function requireUser(): array {
    $headers = getallheaders();

    $normalized = array_change_key_case($headers, CASE_LOWER);

    if (!isset($normalized['authorization'])) {
        http_response_code(401);
        exit(json_encode(['error' => 'Missing Authorization header']));
    }

    $token = preg_replace('/^Bearer\s+/i', '', $normalized['authorization']);

    global $pdo;
    $stmt = $pdo->prepare('
        SELECT u.* FROM api_tokens t
        JOIN users u ON u.id = t.user_id
        WHERE t.token = ? AND t.expires_at > NOW()
    ');
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        exit(json_encode(['error' => 'Invalid or expired token']));
    }
    return $user;
}
?>
