<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$signaturePdfFile = "test.pdf";
$pdf_content = base64_encode(file_get_contents($signaturePdfFile));
echo $pdf_content;

?>