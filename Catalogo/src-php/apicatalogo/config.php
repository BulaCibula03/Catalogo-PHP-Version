<?php
header('Access-Control-Allow-Origin: http://localhost:5173');   // Vite dev
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

/* ---------- Connessione al DB ---------- */
try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=catalog;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Throwable $e) {
    error_log('DB connection error: ' . $e->getMessage());

    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Impossibile connettersi al database']);
    exit;
}

/* ---------- Costanti di utility ---------- */
define('TOKEN_TTL_SECONDS', 7 * 24 * 60 * 60); // 7 giorni
?>
