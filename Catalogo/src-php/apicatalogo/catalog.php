<?php
require_once 'config.php';
header('Content-Type: application/json');

$stmt = $pdo->query('
    SELECT t.*, i.url AS image_url, i.width, i.height
    FROM titles t
    LEFT JOIN title_images i ON i.title_id = t.id
');
$titles = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($titles as &$t) {
    $gstmt = $pdo->prepare('
        SELECT g.name
        FROM genres g
        JOIN title_genre tg ON tg.genre_id = g.id
        WHERE tg.title_id = ?
    ');
    $gstmt->execute([$t['id']]);
    $t['genres'] = $gstmt->fetchAll(PDO::FETCH_COLUMN);
}
echo json_encode($titles);
?>
