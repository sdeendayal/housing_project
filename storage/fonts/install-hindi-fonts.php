<?php

require __DIR__ . '/../../vendor/autoload.php';

use FontLib\Font;

$fontDir = __DIR__;

foreach (['NotoSansDevanagari-Regular', 'NotoSansDevanagari-Bold'] as $name) {
    $ttf = "{$fontDir}/{$name}.ttf";
    $font = Font::load($ttf);
    $font->parse();
    $font->saveAdobeFontMetrics("{$fontDir}/{$name}.ufm");
    $font->close();
    echo "Generated {$name}.ufm\n";
}

$installed = [
    'noto sans devanagari' => [
        'normal' => 'NotoSansDevanagari-Regular',
        'bold' => 'NotoSansDevanagari-Bold',
    ],
];

file_put_contents(
    "{$fontDir}/installed-fonts.json",
    json_encode($installed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "installed-fonts.json created\n";
