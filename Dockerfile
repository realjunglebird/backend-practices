FROM php:8.4-apache

# Установка расширения для работы с MySQL из PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Копирование файлов сайта в корень веб-сервера
COPY index.php /var/www/html/index.php
COPY style.css /var/www/html/style.css
