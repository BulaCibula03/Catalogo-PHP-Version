<?php
require_once 'config.php';
require_once 'auth.php';
header('Content-Type: application/json');

$user = requireUser();

$stmt = $pdo->prepare('
    SELECT t.id, t.primary_title, t.original_title, t.type, t.start_year,
           i.url AS image_url
    FROM user_titles ut
    JOIN titles t ON t.id = ut.title_id
    LEFT JOIN title_images i ON i.title_id = t.id
    WHERE ut.user_id = ?
');
$stmt->execute([$user['id']]);
$my = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($my);
?>
