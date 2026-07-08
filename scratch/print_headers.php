<?php
$f = fopen('database/seeders/data/owners/owners.csv', 'r');
print_r(fgetcsv($f));
fclose($f);
