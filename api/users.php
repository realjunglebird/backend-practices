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
        // Получение пользователя по ID или списка всех пользователей
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $userData = $stmt->fetch();
            // пароль намеренно не возвращается в GET-запросах в целях безопасности
            echo $userData ? json_encode($userData) : json_encode(["error" => "Пользователь не найден"]);
        } else {
            $stmt = $pdo->query('SELECT id, name, email FROM users');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        // Создание нового пользователя
        if (!isset($input['name'], $input['email'], $input['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Необходимо указать поля: name, email, password"]);
            break;
        }

        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        try {
            $stmt->execute([$input['name'], $input['email'], $input['password']]);
            http_response_code(201); // 201 Created
            echo json_encode(["message" => "Пользователь успешно создан", "id" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(409); // 409 Conflict (например, если email уже занят)
            echo json_encode(["error" => "Ошибка создания: " . $e->getMessage()]);
        }
        break;

    case 'PUT':
        // Обновление данных пользователя
        if (isset($_GET['id'])) {
            if (!isset($input['name'], $input['email'], $input['password'])) {
                http_response_code(400);
                echo json_encode(["error" => "Необходимо указать поля: name, email, password"]);
                break;
            }
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?');
            $stmt->execute([$input['name'], $input['email'], $input['password'], $_GET['id']]);
            echo json_encode(["message" => "Данные пользователя обновлены"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Не указан ID пользователя для обновления"]);
        }
        break;

    case 'DELETE':
        // Удаление пользователя
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            // Благодаря ON DELETE CASCADE в БД, заказы этого пользователя тоже удалятся
            echo json_encode(["message" => "Пользователь и связанные данные удалены"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Не указан ID пользователя для удаления"]);
        }
        break;

    default:
        http_response_code(405); // 405 Method Not Allowed
        echo json_encode(["error" => "Метод не поддерживается"]);
        break;
}
