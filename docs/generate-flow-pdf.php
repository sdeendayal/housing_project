<?php

require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$htmlPath = __DIR__ . '/citizen-database-flow.html';
$pdfPath = __DIR__ . '/citizen-database-flow.pdf';
$publicPath = __DIR__ . '/../public/docs/citizen-database-flow.pdf';

if (! is_file($htmlPath)) {
    fwrite(STDERR, "HTML file not found: {$htmlPath}\n");
    exit(1);
}

$html = file_get_contents($htmlPath);

$options = new Options();
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

file_put_contents($pdfPath, $dompdf->output());

if (! is_dir(dirname($publicPath))) {
    mkdir(dirname($publicPath), 0755, true);
}
copy($pdfPath, $publicPath);

echo "PDF created:\n";
echo "  {$pdfPath}\n";
echo "  {$publicPath}\n";
echo "Download: http://127.0.0.1:8000/docs/citizen-database-flow.pdf\n";
