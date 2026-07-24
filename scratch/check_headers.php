<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$filePath = __DIR__ . '/../database/seeders/data/GURGAON_Completed_24-01-2026.xlsx';

if (!file_exists($filePath)) {
    echo "File not found: $filePath\n";
    exit(1);
}

echo "Loading Excel file...\n";
$spreadsheet = IOFactory::load($filePath);

echo "Sheet names:\n";
$sheetNames = $spreadsheet->getSheetNames();
foreach ($sheetNames as $idx => $name) {
    echo " - [$idx]: $name\n";
}

$sheet = $spreadsheet->getActiveSheet();
echo "Active sheet name: " . $sheet->getTitle() . "\n";

$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();
$highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

echo "Highest Row: $highestRow, Highest Column: $highestColumn ($highestColumnIndex)\n";

$headers = [];
for ($col = 1; $col <= $highestColumnIndex; $col++) {
    $headers[] = trim($sheet->getCell([$col, 1])->getValue());
}

echo "Headers (first row):\n";
print_r($headers);
