<?php
$csvFile = 'database/seeders/data/owners.csv';
if (!file_exists($csvFile)) {
    $csvFile = 'database/seeders/data/owners/owners.csv';
}
$file = fopen($csvFile, 'r');
$header = fgetcsv($file);
$map = array_flip($header);
$counts = [];
$sample = [];
while (($row = fgetcsv($file)) !== false) {
    $val = $row[$map['IsPaid']] ?? '';
    $counts[$val] = ($counts[$val] ?? 0) + 1;
    if ($val == '1' || strtolower($val) == 'true' || strtolower($val) == 'yes') {
        $sample[] = $row[$map['BlockId']];
    }
}
fclose($file);
print_r($counts);
echo "Sample BlockIds for paid: " . implode(', ', array_slice($sample, 0, 10)) . "\n";
echo "Total matching paid rows in CSV: " . count($sample) . "\n";
