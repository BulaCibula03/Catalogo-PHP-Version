<?php
require_once 'config.php';
require_once 'auth.php';
header('Content-Type: application/json');

$user = requireUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}
$payload = json_decode(file_get_contents('php://input'), true);
$titleId = $payload['title_id'] ?? '';

if (!$titleId) {
    http_response_code(400);
    exit(json_encode(['error' => 'title_id required']));
}

$chk = $pdo->prepare('SELECT 1 FROM titles WHERE id = ?');
$chk->execute([$titleId]);
if (!$chk->fetch()) {
    http_response_code(404);
    exit(json_encode(['error' => 'Title not found']));
}

$ins = $pdo->prepare('INSERT IGNORE INTO user_titles (user_id,title_id) VALUES (?,?)');
$ins->execute([$user['id'], $titleId]);

echo json_encode(['status' => 'added']);
?>
