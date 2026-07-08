<?php
$csvFile = 'database/seeders/data/owners/owners.csv';
$file = fopen($csvFile, 'r');
$header = fgetcsv($file);
$map = array_flip($header);
while (($row = fgetcsv($file)) !== false) {
    if (($row[$map['OwnerId']] ?? '') == '172') {
        echo "CSV Row for OwnerId 172:\n";
        print_r(array_combine($header, $row));
        break;
    }
}
fclose($file);
