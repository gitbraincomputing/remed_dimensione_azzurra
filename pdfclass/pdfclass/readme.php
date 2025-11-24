<?php
//===================================================================================================
// this is the php file which creates the readme.pdf file, this is not seriously 
// suggested as a good way to create such a file, nor a great example of prose,
// but hopefully it will be useful
//
// adding ?d=1 to the url calling this will cause the pdf code itself to ve echoed to the 
// browser, this is quite useful for debugging purposes.
// there is no option to save directly to a file here, but this would be trivial to implement.
//
// note that this file comprisises both the demo code, and the generator of the pdf documentation
//
//===================================================================================================


// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
//error_reporting(7);
//error_reporting(E_ALL);
//set_time_limit(1800);
include_once('../include/functions.inc.php');
include_once('../include/dbengine.inc.php');
include 'class.ezpdf.php';

// define a clas extension to allow the use of a callback to get the table of contents, and to put the dots in the toc
class Creport extends Cezpdf {

var $reportContents = array();

function Creport($p,$o){
  $this->Cezpdf($p,$o);
}

function rf($info){
  // this callback records all of the table of contents entries, it also places a destination marker there
  // so that it can be linked too
  $tmp = $info['p'];
  $lvl = $tmp[0];
  $lbl = rawurldecode(substr($tmp,1));
  $num=$this->ezWhatPageNumber($this->ezGetCurrentPageNumber());
  $this->reportContents[] = array($lbl,$num,$lvl );
  $this->addDestination('toc'.(count($this->reportContents)-1),'FitH',$info['y']+$info['height']);
}

function dots($info){
  // draw a dotted line over to the right and put on a page number
  $tmp = $info['p'];
  $lvl = $tmp[0];
  $lbl = substr($tmp,1);
  $xpos = 520;

  switch($lvl){
    case '1':
      $size=16;
      $thick=1;
      break;
    case '2':
      $size=12;
      $thick=0.5;
      break;
  }

  $this->saveState();
  $this->setLineStyle($thick,'round','',array(0,10));
  $this->line($xpos,$info['y'],$info['x']+5,$info['y']);
  $this->restoreState();
  $this->addText($xpos+5,$info['y'],$size,$lbl);


}


}
// I am in NZ, so will design my page for A4 paper.. but don't get me started on that.
// (defaults to legal)
// this code has been modified to use ezpdf.

//$pdf = new Cezpdf('a4','portrait');
$pdf = new Creport('a4','landscape');

$pdf -> ezSetMargins(50,70,50,50);

// put a line top and bottom on all the pages
$all = $pdf->openObject();
$pdf->saveState();
$pdf->setStrokeColor(0,0,0,1);
$pdf->line(20,40,578,40);
$pdf->line(20,822,578,822);
//$pdf->addText(50,34,6,'http://ros.co.nz/pdf - http://www.sourceforge.net/projects/pdf-php');
$pdf->restoreState();
$pdf->closeObject();
// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
// or 'even'.
$pdf->addObject($all,'all');

$pdf->ezSetDy(-100);

//$mainFont = './fonts/Helvetica.afm';
$mainFont = './fonts/Times-Roman.afm';
$codeFont = './fonts/Courier.afm';
// select a font
$pdf->selectFont($mainFont);

$pdf->ezText("PHP Pdf Creation\n",30,array('justification'=>'centre'));
$pdf->ezText("Module-free creation of Pdf documents\nfrom within PHP\n",20,array('justification'=>'centre'));
$pdf->ezText("developed by R&OS Ltd\n<c:alink:http://www.ros.co.nz/pdf/>http://www.ros.co.nz/pdf</c:alink>",18,array('justification'=>'centre'));
$pdf->ezText("\n<c:alink:http://sourceforge.net/projects/pdf-php>http://sourceforge.net/projects/pdf-php</c:alink>\n\nversion 0.09",18,array('justification'=>'centre'));

$pdf->ezSetDy(-100);
// modified to use the local file if it can

$pdf->openHere('Fit');

function ros_logo(&$pdf,$x,$y,$height,$wl=0,$wr=0){
  $pdf->saveState();
  $h=100;
  $factor = $height/$h;
  $pdf->selectFont('./fonts/Helvetica-Bold.afm');
  $text = 'R&OS';
  $ts=100*$factor;
  $th = $pdf->getFontHeight($ts);
  $td = $pdf->getFontDecender($ts);
  $tw = $pdf->getTextWidth($ts,$text);
  $pdf->setColor(0.6,0,0);
  $z = 0.86;
  $pdf->filledRectangle($x-$wl,$y-$z*$h*$factor,$tw*1.2+$wr+$wl,$h*$factor*$z);
  $pdf->setColor(1,1,1);
  $pdf->addText($x,$y-$th-$td,$ts,$text);
  $pdf->setColor(0.6,0,0);
  $pdf->addText($x,$y-$th-$td,$ts*0.1,'http://www.ros.co.nz');
  $pdf->restoreState();
  return $height;
}

ros_logo($pdf,150,$pdf->y-100,80,150,200);
$pdf->selectFont($mainFont);
$pdf->ezNewPage();


$conn = db_connect();
$where=$_REQUEST['where'];
$query1="SELECT * from re_status_moduli $where order by cognome, nome";
echo($query1);
$rs = mssql_query($query1, $conn);
$old_impegnativa="";
$open=0;
$cognome="";
$nome="";
while($row=mssql_fetch_assoc($rs)){
	$id_impegnativa=$row['id_impegnativa'];		
	$idmodulo=$row['id_modulo_padre'];		
	$idmodulo_id=$row['idmodulo_id'];
	$nome_modulo=$row['modello'];
	$cod_cartella=$row['codice_cartella'];		
	$scadenza=$row['scadenza'];		
	$obbligatorio=$row['obbligatorio'];				
	$ultima_compilazione=formatta_data($row['ultima_compilazione']);		
	$data_osservazione=$row['data_fissa'];
	$data_ins=$row['datains'];
	$nome_medico=$row['operatore'];
	$prot_auth_asl=$row['ProtAutorizAsl'];
	$data_auth_asl=formatta_data($row['DataAutorizAsl']);
	$normativa=$row['normativa'];
	$regime=$row['regime'];
	$num_istanze=$row['num_istanze'];


	if(($cognome!=$row['Cognome'])and ($nome!=$row['Nome'])){
		if($open){
			$cols = array('a'=>'Modulo',
						  'b'=>'Prot Autoriz',
						  'c'=>'Data Autoriz',
						  'd'=>'Figura Responsabile',
						  'e'=>'Tipo',
						  'f'=>'Istanze',
						  'g'=>'Ultima Compialzione');
			$pdf->ezTable($data,$cols,'',array('xPos'=>90,
								   'xOrientation'=>'right',								   
								   'cols'=>array('a'=>array('justification'=>'right'),'b'=>array('width'=>100))
								));		
			}
		$data=array();
		$cognome=$row['Cognome'];
		$nome=$row['Nome'];
		$pdf->ezText("paziente: $cognome $nome\n",18,array('justification'=>'left'));
		$pdf->ezText("cc n. $cod_cartella - normativa: $normativa - $regime \n \n",14,array('justification'=>'left'));	
		$open=1;
	}													
	if ($obbligatorio=='o')
		$tipo="OBB/";
		else
		$tipo="FAC/";
	if(($replica==1)or($replica==3))
		$tipo.="M";
		else
		$tipo.="U";
	$data_tmp=array('a'=>$nome_modulo,
					  'b'=>$prot_auth_asl,
					  'c'=>$data_auth_asl,
					  'd'=>$nome_medico,
					  'e'=>$tipo,
					  'f'=>$num_istanze,
					  'g'=>$ultima_compilazione);
	
	array_push($data,$data_tmp);
}
mssql_free_result($rs);
$pdf->ezStream();



/*
$pdf->ezText("paziente: DI MARTINO FRANCESCO \n",18,array('justification'=>'left'));
$pdf->ezText("cc n. 10789 - normativa: ex Art. 26 L. 833/78 - Residenziale \n \n",14,array('justification'=>'left'));
$data = array(
array('a'=>1,'b'=>'gandalf','c'=>'wizard')
,array('a'=>2,'b'=>'bilbo','c'=>'hobbit','url'=>'http://www.ros.co.nz/pdf/')
,array('a'=>3,'b'=>'frodo','c'=>'hobbit')
,array('a'=>4,'b'=>'saruman','c'=>'bad dude','url'=>'http://sourceforge.net/projects/pdf-php')
,array('a'=>5,'b'=>'sauron','c'=>'really bad dude')
);

$cols = array('a'=>"number a a a a a a a a a a a a a a a\nmore",
			  'b'=>'Name',
			  'c'=>'Type');

$pdf->ezTable($data,$cols,'',array('xPos'=>90,
								   'xOrientation'=>'right',								   
								   'cols'=>array('a'=>array('justification'=>'right'),'b'=>array('width'=>100))
								));


$pdf->ezText("\n \npaziente: DI MARTINO FRANCESCO \n",18,array('justification'=>'left'));
$pdf->ezText("cc n. 10789 - normativa: ex Art. 26 L. 833/78 - Residenziale \n \n",14,array('justification'=>'left'));
$data = array(
array('a'=>1,'b'=>'gandalf','c'=>'wizard')
,array('a'=>2,'b'=>'bilbo','c'=>'hobbit','url'=>'http://www.ros.co.nz/pdf/')
,array('a'=>3,'b'=>'frodo','c'=>'hobbit')
,array('a'=>4,'b'=>'saruman','c'=>'bad dude','url'=>'http://sourceforge.net/projects/pdf-php')
,array('a'=>5,'b'=>'sauron','c'=>'really bad dude')
);

$cols = array('a'=>"number a a a a a a a a a a a a a a a\nmore",
			  'b'=>'Name',
			  'c'=>'Type');

$pdf->ezTable($data,$cols,'',array('xPos'=>90,
								   'xOrientation'=>'right',								   
								   'cols'=>array('a'=>array('justification'=>'right'),'b'=>array('width'=>100))
								));
$pdf->ezStream();
*/
?>