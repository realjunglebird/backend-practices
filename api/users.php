<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

/** @var mysqli $conn */
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

#region GET метод
if ($method === 'GET') {
    if ($id !== null) {
        $stmt = $conn->prepare('SELECT id, name, email FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Пользователь с переданным ID не найден'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode($user, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = $conn->query("SELECT id, name, email FROM users ORDER BY id");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users, JSON_UNESCAPED_UNICODE);
    exit;
}
#endregion

#region POST метод
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || trim($data['name']) === '' ||
        !isset($data['email']) || trim($data['email']) === '' ||
        !isset($data['password']) || trim($data['password']) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Не указаны name, email или password'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $name = trim($data['name']);
    $email = trim($data['email']);
    $password = trim($data['password']);

    $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $email, $password);

    if (!$stmt->execute()) {
        if ($conn->errno === 1062) {
            http_response_code(409);
            echo json_encode(['error' => 'Пользователь с таким email уже существует'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        http_response_code(500);
        echo json_encode(['error' => 'Ошибка создания пользователя'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(201);
    echo json_encode(['id' => $conn->insert_id, 'name' => $name, 'email' => $email], JSON_UNESCAPED_UNICODE);
    exit;
}
#endregion

#region PUT метод
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($id === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Укажите ID пользователя'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!isset($data['name']) || trim($data['name']) === '' ||
        !isset($data['email']) || trim($data['email']) === '' ||
        !isset($data['password']) || trim($data['password']) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Не указаны name, email или password'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $name = trim($data['name']);
    $email = trim($data['email']);
    $password = trim($data['password']);

    $stmt = $conn->prepare('UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?');
    $stmt->bind_param('sssi', $name, $email, $password, $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Пользователь не найден или данные не изменились'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['id' => $id, 'name' => $name, 'email' => $email], JSON_UNESCAPED_UNICODE);
    exit;
}
#endregion

#region DELETE метод
if ($method === 'DELETE') {
    if ($id === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Укажите ID пользователя'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Пользователь не найден'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['message' => 'Пользователь и связанные данные удалены', 'id' => $id], JSON_UNESCAPED_UNICODE);
    exit;
}
#endregion

http_response_code(405);
echo json_encode(['error' => 'Метод не поддерживается'], JSON_UNESCAPED_UNICODE);
