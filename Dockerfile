FROM php:8.4-apache

# Установка расширения для работы с MySQL из PHP
RUN docker-php-ext-install mysqli

# Копирование файлов сайта в корень веб-сервера
COPY index.php /var/www/html/index.php
COPY style.css /var/www/html/style.css

COPY drawer.php /var/www/html/drawer.php
COPY drawer_logic.php /var/www/html/drawer_logic.php

COPY sort_shell.php /var/www/html/sort_shell.php
COPY sort.php /var/www/html/sort.php
