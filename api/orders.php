<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

/** @var mysqli $conn */
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

#region GET метод
if ($method === 'GET') {
    if ($id !== null) {
        $stmt = $conn->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Заказ с переданным ID не найден'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode($order, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = $conn->query("SELECT * FROM orders ORDER BY id");
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders, JSON_UNESCAPED_UNICODE);
    exit;
}
#endregion

#region POST метод
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        !isset($data['user_id']) ||
        !isset($data['product_name']) || trim($data['product_name']) === '' ||
        !isset($data['amount'])
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Не указаны user_id, product_name или amount'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $user_id = (int)$data['user_id'];
    $product_name = trim($data['product_name']);
    $amount = $data['amount']; // Можно привести к (int) или (float) в зависимости от типа в БД

    $stmt = $conn->prepare('INSERT INTO orders (user_id, product_name, amount) VALUES (?, ?, ?)');
    $stmt->bind_param('isi', $user_id, $product_name, $amount);
    $stmt->execute();

    $newId = $conn->insert_id;
    http_response_code(201);
    echo json_encode([
        'id' => $newId,
        'user_id' => $user_id,
        'product_name' => $product_name,
        'amount' => $amount
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
#endregion

#region PUT метод
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($id === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Укажите ID заказа'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (
        !isset($data['user_id']) ||
        !isset($data['product_name']) || trim($data['product_name']) === '' ||
        !isset($data['amount'])
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Не указаны user_id, product_name или amount'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $user_id = (int)$data['user_id'];
    $product_name = trim($data['product_name']);
    $amount = $data['amount'];

    $stmt = $conn->prepare('UPDATE orders SET user_id = ?, product_name = ?, amount = ? WHERE id = ?');
    $stmt->bind_param('isii', $user_id, $product_name, $amount, $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Заказ не найден или данные не изменились'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'id' => $id,
        'user_id' => $user_id,
        'product_name' => $product_name,
        'amount' => $amount
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
#endregion

#region DELETE метод
if ($method === 'DELETE') {
    if ($id === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Укажите ID заказа'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $conn->prepare('DELETE FROM orders WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Заказ не найден'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'message' => 'Заказ удален',
        'id' => $id
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
#endregion

// =========================
// Неподдерживаемый метод
// =========================
http_response_code(405);
echo json_encode(['error' => 'Метод не поддерживается'], JSON_UNESCAPED_UNICODE);
