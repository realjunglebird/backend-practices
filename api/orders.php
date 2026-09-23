<?php
// Устанавливаем заголовок ответа в формате JSON
header('Content-Type: application/json; charset=utf-8');

// Настройки подключения к БД
$host = 'db';
$db   = 'appDB';
$user = 'user';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Ошибка подключения к БД: " . $e->getMessage()]);
    exit;
}

// Определяем метод HTTP-запроса и читаем тело запроса (для POST/PUT)
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Получение заказа по ID или списка всех заказов
            $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $order = $stmt->fetch();
            echo $order ? json_encode($order) : json_encode(["error" => "Заказ не найден"]);
        } else {
            $stmt = $pdo->query('SELECT * FROM orders');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        // Создание нового заказа
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, product_name, amount) VALUES (?, ?, ?)');
        $stmt->execute([$input['user_id'], $input['product_name'], $input['amount']]);
        http_response_code(201);    // 201 Created
        echo json_encode(["message" => "Заказ создан", "id" => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        // Обновление данных заказа
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('UPDATE orders SET user_id = ?, product_name = ?, amount = ? WHERE id = ?');
            $stmt->execute([$input['user_id'], $input['product_name'], $input['amount'], $_GET['id']]);
            echo json_encode(["message" => "Заказ обновлен"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Не указан ID заказа"]);
        }
        break;

    case 'DELETE':
        // Удаление заказа
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('DELETE FROM orders WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            echo json_encode(["message" => "Заказ удален"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Не указан ID заказа"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Метод не поддерживается"]);
        break;
}
