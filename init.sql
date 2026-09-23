CREATE DATABASE IF NOT EXISTS appDB;
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'password';
GRANT SELECT,UPDATE,INSERT ON appDB.* TO 'user'@'%';
FLUSH PRIVILEGES;

USE appDB;

-- Сущность 1: Пользователи
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Сущность 2: Заказы (связана с users через user_id)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Добавляем тестовые данные
INSERT INTO users (name, email, password) VALUES
('Иван Иванов', 'ivan@example.com', 'password123'),
('Петр Петров', 'petr@example.com', 'password456');

INSERT INTO orders (user_id, product_name, amount) VALUES
(1, 'Ноутбук', 50000.00),
(1, 'Мышь', 1500.00),
(2, 'Монитор', 20000.00);
