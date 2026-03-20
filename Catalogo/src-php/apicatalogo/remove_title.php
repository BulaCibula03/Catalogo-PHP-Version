<?php
require_once 'config.php';
require_once 'auth.php';
header('Content-Type: application/json');

$user = requireUser();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$titleId = $_GET['title_id'] ?? '';
if (!$titleId) {
    http_response_code(400);
    exit(json_encode(['error' => 'title_id required']));
}

$del = $pdo->prepare('DELETE FROM user_titles WHERE user_id = ? AND title_id = ?');
$del->execute([$user['id'], $titleId]);

echo json_encode(['status' => 'removed']);
?>
