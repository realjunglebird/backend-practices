CREATE DATABASE IF NOT EXISTS appDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'password';
GRANT SELECT, INSERT, UPDATE, DELETE ON appDB.* TO 'user'@'%';
FLUSH PRIVILEGES;

USE appDB;

-- Принудительно указываем кодировку для этого скрипта
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Сущность 1: Пользователи
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Сущность 2: Заказы (связана с users через user_id)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавляем тестовые данные
INSERT INTO users (name, email, password) VALUES
('Иван Иванов', 'ivan@example.com', 'password123'),
('Петр Петров', 'petr@example.com', 'password456');

INSERT INTO orders (user_id, product_name, amount) VALUES
(1, 'Ноутбук', 50000.00),
(1, 'Мышь', 1500.00),
(2, 'Монитор', 20000.00);
