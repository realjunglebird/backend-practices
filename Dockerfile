FROM php:8.4-apache

RUN docker-php-ext-install mysqli

# Копирование файлов сайта в корень веб-сервера
COPY index.php /var/www/html/index.php
COPY style.css /var/www/html/style.css
