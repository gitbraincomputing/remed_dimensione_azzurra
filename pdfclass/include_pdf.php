<?php

//inclusioen pagina esterna
require_once('fpdf/fpdf.php'); 
require_once('fpdi/fpdi.php'); 
	 
// initiate FPDI 
$pdf =& new FPDI(); 
	 
$pagecount = $pdf->setSourceFile('istanza.pdf'); 
$tplidx = $pdf->importPage(1, '/MediaBox'); 
	 
$pdf->addPage(); 
$pdf->useTemplate($tplidx, 100, 100, 100); 
 
$pdf->Output('newpdf.pdf', 'D'); 
?>