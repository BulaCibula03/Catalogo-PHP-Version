<?php
require_once 'config.php';
require_once 'auth.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // -----------  GET : dati utente corrente -----------
    case 'GET':
        $user = requireUser();
        //unset($user['password_hash']);
        echo json_encode($user);
        break;

    // -----------  PUT : aggiornamento profilo ----------
    case 'PUT':
        $user = requireUser();
        $payload = json_decode(file_get_contents('php://input'), true);
        $fields = [];
        $params = [':id' => $user['id']];
    
        if (!empty($payload['password_current'])) {
            if (!password_verify($payload['password_current'], $user['password_hash'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Password corrente errata']);
                exit;
            }
        }
    
        foreach (['full_name','email','phone','gender','bio','profile_image'] as $f) {
            if (isset($payload[$f])) {
                $fields[] = "$f = :$f";
                $params[":$f"] = $payload[$f];
            }
        }
    
        if (!empty($payload['password'])) {
            $fields[] = "password_hash = :pwd";
            $params[':pwd'] = password_hash($payload['password'], PASSWORD_DEFAULT);
        }
    
        if ($fields) {
            $sql = 'UPDATE users SET '.implode(', ', $fields).' WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }
        echo json_encode(['status' => 'updated']);
        break;

    // -----------  DELETE : elimina account -------------
    case 'DELETE':
        $user = requireUser();
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$user['id']]);
        echo json_encode(['status' => 'deleted']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
