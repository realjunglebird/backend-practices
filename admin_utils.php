<?php

function runCommand(string $cmd): string {
    // Проверка: доступна ли функция выполнения
    if (!function_exists('shell_exec')) {
        return '<span style="color:red;">shell_exec() отключён на сервере</span>';
    }

    // Белый список разрешённых команд (защита от инъекций)
    $allowed = ['ls', 'ps', 'whoami', 'id', 'uname', 'uptime', 'df', 'free', 'hostname', 'pwd', 'date'];
    $baseCmd = explode(' ', trim($cmd))[0];

    if (!in_array($baseCmd, $allowed, true)) {
        return '<span style="color:red;">Команда "' . htmlspecialchars($baseCmd) . '" не разрешена</span>';
    }

    $output = @shell_exec(escapeshellcmd($cmd) . ' 2>&1');

    if ($output === null || $output === '') {
        return '<span style="color:orange;">Нет вывода или ошибка выполнения</span>';
    }

    return htmlspecialchars($output);
}

function getServerInfo(): array {
    $commands = [
        'Имя пользователя (whoami)' => 'whoami',
        'ID пользователя (id)'  => 'id',
        'Имя хоста (hostname)'  => 'hostname',
        'ОС (uname -a)'        => 'uname -a',
        'Время работы (uptime)' => 'uptime',
        'Текущая дата (date)'   => 'date',
        'Текущая директория (pwd)' => 'pwd',
        'Файлы (ls -la)'       => 'ls -la',
        'Процессы (ps aux)'    => 'ps aux',
        'Диски (df -h)'        => 'df -h',
    ];

    if (shell_exec('which free 2>/dev/null')) {
        $commands['Память (free -h)'] = 'free -h';
    }

    $results = [];
    foreach ($commands as $label => $cmd) {
        $results[$label] = runCommand($cmd);
    }
    return $results;
}
