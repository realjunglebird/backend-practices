<?php

require_once __DIR__ . '/drawer_logic.php';

echo "<h2>Drawer</h2>";

if (!isset($_GET['num']) || $_GET['num'] === '') {
    echo "<p style='color:red;'>Ошибка: передайте параметр ?num=число</p>";
    echo "<p>Пример: <a href='?num=2349'>?num=2349</a></p>";
    exit;
}

$num = filter_input(INPUT_GET, 'num', FILTER_VALIDATE_INT);

if ($num === false || $num === null) {
    echo "<p style='color:red;'>Ошибка: параметр num должен быть целым числом.</p>";
    exit;
}

if ($num < 0) {
    echo "<p style='color:red;'>Ошибка: число должно быть неотрицательным.</p>";
    exit;
}

$fig = decodeFigure($num);
$svg = generateSvg($fig);

echo "<p><b>Число:</b> $num (двоичное: " . decbin($num) . ")</p>";
echo "<p><b>Форма:</b> {$fig['shape']}, <b>Цвет:</b> {$fig['color']}, <b>Размер:</b> {$fig['width']}×{$fig['height']}</p>";
echo $svg;
