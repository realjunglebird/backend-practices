<?php

require_once __DIR__ . '/sort_shell.php';

echo "<h2>Сортировка Шелла</h2>";

// Проверка наличия и непустоты параметра arr в GET-запросе
if (!isset($_GET['arr']) || trim($_GET['arr']) === '') {
    echo "<p style='color:red;'>Ошибка: передайте параметр ?arr=числа,через,запятую</p>";
    echo "<p>Пример: <a href='?arr=5,3,8,1,9,2,7,4,6'>?arr=5,3,8,1,9,2,7,4,6</a></p>";
    exit;
}

$raw = $_GET['arr'];            // Получение сырой введённой строки
$parts = explode(',', $raw);    // Разделение строки на фрагменты по запятой

// Проверка, что каждый элемент является числом
$valid = true;      // Флаг успешности валидации
$nums = [];         // Результирующий массив для хранения целых чисел

foreach ($parts as $p) {
    $p = trim($p);          // Удаление возможных пробельных символов по краям

    // Если фрагмент пуст или не является числом --- прерываем валидацию
    if ($p === '' || !is_numeric($p)) {
        $valid = false;
        break;
    }
    $nums[] = (int)$p;
}

// Проверка успешности валидации и минимально необходимого количества элементов
if (!$valid || count($nums) < 2) {
    echo "<p style='color:red;'>Ошибка: массив должен содержать минимум 2 целых числа, разделённых запятыми.</p>";
    exit;
}

echo "<p><b>Исходный массив:</b> [" . implode(', ', $nums) . "]</p>";

// Запуск алгоритма и сохранение результатов работы
$result = shellSort($nums);

echo "<h3>Промежуточные проходы:</h3>";
echo "<table border='1' cellpadding='6' style='border-collapse:collapse;'>";
echo "<tr><th>Промежуток</th><th>Состояние массива</th></tr>";

// Итерация по сохраненным снимкам состояний массива для построчного вывода
foreach ($result['steps'] as $i => $step) {
    echo "<tr><td style='text-align:center;'><b>" . $result['gaps'][$i] . "</b></td>";
    echo "<td>[" . implode(', ', $step) . "]</td></tr>";
}
echo "</table>";

echo "<h3>Результат:</h3>";
echo "<p style='font-size:1.2em; color:green;'>[" . implode(', ', $result['sorted']) . "]</p>";
