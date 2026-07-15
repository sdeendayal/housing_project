<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = 'E:\EWS flat data\Sonipat\Master sheet for draw sonipat.xlsx';
if (!file_exists($path)) {
    die("File not found!\n");
}

echo "Loading Master sheet...\n";
$spreadsheet = IOFactory::load($path);
$sheet = $spreadsheet->getSheetByName('814 bookings');

$highestRow = $sheet->getHighestRow();

$appCounts = [];
$aadharCounts = [];

for ($r = 2; $r <= $highestRow; $r++) {
    $aadhar = trim($sheet->getCell([1, $r])->getValue()); // Col 1 is aadhar_no
    $appNo = trim($sheet->getCell([4, $r])->getValue());  // Col 4 is application_number
    
    if ($appNo !== '') {
        if (!isset($appCounts[$appNo])) {
            $appCounts[$appNo] = [];
        }
        $appCounts[$appNo][] = $r;
    }
    
    if ($aadhar !== '') {
        if (!isset($aadharCounts[$aadhar])) {
            $aadharCounts[$aadhar] = [];
        }
        $aadharCounts[$aadhar][] = $r;
    }
}

$dupApps = [];
foreach ($appCounts as $val => $rows) {
    if (count($rows) > 1) {
        $dupApps[$val] = $rows;
    }
}

$dupAadhars = [];
foreach ($aadharCounts as $val => $rows) {
    if ($val !== 'NA' && $val !== 'None' && count($rows) > 1) {
        $dupAadhars[$val] = $rows;
    }
}

echo "\nDuplicate Application Numbers in 814 bookings:\n";
print_r($dupApps);

echo "\nDuplicate Aadhar Numbers in 814 bookings (excluding NA/None):\n";
print_r($dupAadhars);
