<?php
// Данные для подключения к MySQL
$host = 'db';
$username = 'user';
$password = 'password';
$database = 'appDB';

// Создаём подключение к базе данных
$conn = new mysqli($host, $username, $password, $database);

// Проверяем подключение
if ($conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'error' => 'Ошибка подключения к базе данных: ' . $conn->connect_error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Устанавливаем кодировку UTF-8 для корректной работы с кириллицей
$conn->set_charset('utf8mb4');
