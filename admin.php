<?php

require_once __DIR__ . '/admin_utils.php';

echo "<h2>Информация о сервере</h2>";
echo "<p>Версия PHP: " . phpversion() . "</p>";

$info = getServerInfo();

foreach ($info as $label => $output) {
    echo "<h3>$label</h3>";
    echo "<pre style='background:#1e1e1e; color:#0f0; padding:12px; border-radius:6px; overflow-x:auto;'>"
       . $output . "</pre>";
}
