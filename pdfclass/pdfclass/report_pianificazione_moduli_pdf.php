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
$conn = db_connect();
$query="SELECT * FROM struttura WHERE idstruttura=1";
$rs1=mssql_query($query,$conn);
$row1=mssql_fetch_assoc($rs1);
$logo=$row1['logo'];
$intestazione=$row1['intestazione'];
$piede=$row1['piede'];
// I am in NZ, so will design my page for A4 paper.. but don't get me started on that.
// (defaults to legal)
// this code has been modified to use ezpdf.

//$pdf = new Cezpdf('a4','portrait');
$pdf = new Creport('a3','landscape');

$pdf -> ezSetMargins(50,70,50,50);
$data_att=date('d/m/Y');
// put a line top and bottom on all the pages
$all = $pdf->openObject();
$pdf->saveState();
$pdf->setStrokeColor(0,0,0,1);
$pdf->line(20,815,1150,815);
$pdf->addText(600,800,8,'Report pianificazione moduli cartelle cliniche per utente del '.$data_att.' - '.$intestazione);
$pdf->line(20,40,1150,40);
$pdf->addText(50,28,8,'Software ReMed by Bain Computing http://www.braincomputing.com');
$pdf->restoreState();
$pdf->closeObject();
// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
// or 'even'.
$pdf->addObject($all,'all');

$pdf->ezSetDy(-200);


$where_sp="";
$idnorm=$_REQUEST['idnorm'];

if(isset($_REQUEST['idnorm'])and($_REQUEST['idnorm']!=0)) {	 
	$where.=" and idregime=".$_REQUEST['idnorm'];
	$idnorm=$_REQUEST['idnorm'];
	$where_sp="@idregime=$idnorm";
}else{
	$idnorm=5;
	//$where.=" and idregime=".$idnorm;
	$where_sp="@idregime='5'";
}

$conn = db_connect();
$query_r="SELECT idregime,regime,normativa FROM regime INNER JOIN normativa ON regime.idnormativa = normativa.idnormativa WHERE (regime.stato = 1) AND (regime.cancella = 'n') $where";
$rs_op = mssql_query($query_r, $conn);
$row_r=mssql_fetch_assoc($rs_op);
$regime=$row_r['normativa']." ".$row_r['regime'];

//$mainFont = './fonts/Helvetica.afm';
$mainFont = './fonts/Times-Roman.afm';
$codeFont = './fonts/Courier.afm';
// select a font
$pdf->selectFont($mainFont);
$pdf->ezText("$intestazione\n",30,array('justification'=>'centre'));
$pdf->ezText("Report pianificazione moduli Cartelle Cliniche per utente\n",18,array('justification'=>'centre'));
$pdf->ezText("Regime di trattamento: $regime\n",15,array('justification'=>'centre'));
$pdf->ezText("dati aggiornati al $data_att ",12,array('justification'=>'centre'));

$pdf->ezSetDy(-100);
$pdf->ezText($piede,10,array('justification'=>'centre'));
$pdf->ezSetDy(-60);
// modified to use the local file if it can

$pdf->openHere('Fit');
function ros_logo(&$pdf,$x,$y,$height,$wl=0,$wr=0){
  $pdf->saveState();
  $h=100;
  $factor = $height/$h;
  $pdf->selectFont('./fonts/Helvetica-Bold.afm');
  $text = 'ReMed';
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
  //$pdf->addText($x,$y-$th-$td,$ts*0.1,'http://www.ros.co.nz');
  $pdf->restoreState();
  return $height;
}
if($logo!="") {
	$pdf->addJpegFromFile('../images/'.$logo,550,$pdf->y-(-110),80,0);
	ros_logo($pdf,150,$pdf->y-100,80,150,200);
}
$pdf->selectFont($mainFont);
$pdf->ezNewPage();
$pdf->ezStartPageNumbers(1100,28,8,'','',1);

$conn = db_connect();
$where=$_REQUEST['where'];
$query1 = "crosstable_moduli $where_sp";
$rs2 = mssql_query($query1, $conn);
mssql_next_result($rs2);
$num_rows=mssql_num_rows($rs2);
$txt_trovate="Numero pianificazioni per utente trovate: $num_rows";

$pdf->ezText("\n".$txt_trovate."\n",15,array('justification'=>'left'));	

$fields_arr=array();		
for ($i = 0; $i < mssql_num_fields($rs2); ++$i) {
	$field = mssql_fetch_field($rs2, $i);		
		array_push($fields_arr,$field->name);
}
	

$data=array();
$col=array();		
while($row=mssql_fetch_assoc($rs2)){
	$i=0;
	$data_tmp=array();
	foreach ($fields_arr as $value){		
		if($i>0)$data_tmp[$value]=pulisci_lettura($row[$value]);
		$i++;
		if($i>3) 
			$col[$value]=array('width'=>50,'justification'=>'center');
			else
			$col[$value]=array('width'=>80);			
	}
	array_push($data,$data_tmp);	
}
mssql_free_result($rs2);
$pdf->ezTable($data,$cols,'',array('fontSize'=>8,
						   'xPos'=>50,
						   'xOrientation'=>'right',
						   'cols'=>$col));
$pdf->ezText("\n\n",18,array('justification'=>'left'));	
$pdf->ezStopPageNumbers(1,1);
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