<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php';

ob_start();
include __DIR__ . '/invoiceTemplates/template3.php';
$html = ob_get_contents();
ob_end_clean();

$title = !empty($invoice['title']) ? 'Invoice' . $invoice['title'] : 'Invoice';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/vendor/mpdf/mpdf/tmp',
    'format' => 'A4',
    'orientation' => 'P',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 8,
    'margin_bottom' => 8,
    'margin_header' => 0,
    'margin_footer' => 0
]);
$mpdf->WriteHTML($html);
$mpdf->Output($title . '.pdf', 'I');
// $mpdf->Output('example.pdf', 'D');

?>