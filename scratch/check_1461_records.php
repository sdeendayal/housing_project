<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = 'E:\EWS flat data\Sonipat\survey.xlsx';
if (!file_exists($path)) {
    die("File not found!\n");
}

$spreadsheet = IOFactory::load($path);
$sheet = $spreadsheet->getSheetByName('1461');
if (!$sheet) {
    die("Sheet '1461' not found!\n");
}

$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

echo "Highest Row: $highestRow, Columns: $highestColumnIndex ($highestColumn)\n";

$headers = [];
for ($col = 1; $col <= $highestColumnIndex; $col++) {
    $headers[] = trim($sheet->getCell([$col, 1])->getValue()); // Row 1 has headers!
}

print_r($headers);

// Check if Row 1 is header or title
for ($r = 1; $r <= 3; $r++) {
    echo "Row $r: " . $sheet->getCell([1, $r])->getValue() . " | " . $sheet->getCell([2, $r])->getValue() . "\n";
}

for ($r = $highestRow - 2; $r <= $highestRow; $r++) {
    echo "Row $r: " . $sheet->getCell([1, $r])->getValue() . " | " . $sheet->getCell([2, $r])->getValue() . "\n";
}
