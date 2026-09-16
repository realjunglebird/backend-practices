<?php

// Логика декодирования запроса и генерации SVG

function decodeFigure(int $num): array {
    $shape = $num & 0b11;                   // биты [0; 1]
    $color = ($num >> 2) & 0b111;           // биты [2; 4]
    $width = (($num >> 5) & 0b1111) * 10;   // биты [5; 8]
    $height = (($num >> 9) & 0b1111) * 10;  // биты [9; 12]

    $colorMap = [
        0 => 'red',
        1 => 'green',
        2 => 'blue',
        3 => 'yellow',
        4 => 'black',
        5 => 'white',
        6 => 'orange',
        7 => 'purple',
    ];

    return [
        'shape' => $shape,
        'color' => $colorMap[$color] ?? 'gray',
        'width' => max($width, 10),
        'height' => max($height, 10),
    ];
}

function generateSvg(array $fig): string {
    $cx = 150; $cy = 150;
    $w = $fig['width'];
    $h = $fig['height'];
    $color = htmlspecialchars($fig['color']);

    $element = '';

    switch ($fig['shape']) {
        case 0: // Круг
            $r = min($w, $h) / 2;
            $element = "
                <circle
                    cx=\"$cx\"
                    cy=\"$cy\"
                    r=\"$r\"
                    fill=\"$color\"
                    stroke=\"black\"
                    stroke-width=\"2\"
                />
            ";
            break;
        case 1: // Прямоугольник
            $x = $cx - $w/2; $y = $cy - $h/2;
            $element = "
                <rect
                    x=\"$x\"
                    y=\"$y\"
                    width=\"$w\"
                    height=\"$h\"
                    fill=\"$color\"
                    stroke=\"black\"
                    stroke-width=\"2\"
                />
            ";
            break;
        case 2: // Линия
            $x1 = $cx - $w/2; $y1 = $cy - $h/2;
            $x2 = $cx + $w/2; $y2 = $cy + $h/2;
            $element = "
                <line
                    x1=\"$x1\"
                    y1=\"$y1\"
                    x2=\"$x2\"
                    y2=\"$y2\"
                    stroke=\"$color\"
                    stroke-width=\"4\"
                />
            ";
            break;
        case 3: // Треугольник
            $points = sprintf(
                "%d,%d %d,%d %d,%d",
                $cx, $cy - $h/2,
                $cx - $w/2, $cy + $h/2,
                $cx + $w/2, $cy + $h/2
            );
            $element = "
                <polygon
                    points=\"$points\"
                    fill=\"$color\"
                    stroke=\"black\"
                    stroke-width=\"2\"
                />
            ";
            break;
    }

    return '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg" style="border:1px solid #ccc; background:#f9f9f9;">'
         . $element . '</svg>';
}
