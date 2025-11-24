<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Fpdi library 
require_once('vendor/autoload.php');
use setasign\Fpdi\Fpdi; 
require_once('vendor/setasign/fpdf/fpdf.php');


		
class PDF_Rotate extends FPDI
{
	var $angle=0;

	function Rotate($angle,$x=-1,$y=-1)
	{
		if($x==-1)				$x=$this->x;
		if($y==-1)				$y=$this->y;
		if($this->angle!=0)		$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function _endpage()
	{
		if($this->angle!=0) {
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
}
$pdf_1 = new PDF_Rotate();
$pdf_2 = new PDF_Rotate();
		
		

// AME 31/01/2023 - FUNZIONE DI ANNULLAMENTO ISTANZA E ALLEGATO ANNESSI
// Questo file prende l'allegato1 e 2 (se presente) di una istanza e ne crea una copia con filigrana ANNULLATO su ogni pagina
// Il file originale (con firma digitale o senza) viene backuppato nella $dir_allegati_annullati

 if (isset($_GET['idcartella']) 	 && $_GET['idcartella'] > 0 	  &&
	isset($_GET['idpaziente']) 		 && $_GET['idpaziente'] > 0 	  &&
	isset($_GET['idmodulopadre']) 	 && $_GET['idmodulopadre'] > 0 	  &&
	isset($_GET['idmoduloversione']) && $_GET['idmoduloversione'] > 0 &&
	isset($_GET['idinserimento']) 	 && $_GET['idinserimento'] > 0 	  &&
	isset($_GET['uid']) 			 && $_GET['uid'] > 0 			  &&
	isset($_GET['action']) 			 && $_GET['action'] !== '' 		  &&
	isset($_GET['usephp']) 			 && $_GET['usephp']=='7' 		  )
{


	// carico le configurazioni dal file config.rdm.php generale
	$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));
	require_once($productionConfig);

	$idcartella 	  = $_GET['idcartella'];
	$idpaziente 	  = $_GET['idpaziente'];
	$idmodulopadre	  = $_GET['idmodulopadre'];
	$idmoduloversione = $_GET['idmoduloversione'];
	$idinserimento 	  = $_GET['idinserimento'];
	$idoperatore 	  = $_GET['uid'];
	$action	 	  	  = $_GET['action'];
	$dir_allegati 			= "C:\\Web\\republic\\modelli_word\\allegato";
	$dir_allegati_annullati = "C:\\Web\\Republic\\modelli_word\\allegati_annullati";
	
	$query_1 = "SELECT id_istanza_testata, allegato, data_allegato, allegato2, data_allegato2 from istanze_testata AS it
						WHERE it.id_cartella = $idcartella AND it.id_inserimento = $idinserimento 
						AND it.id_modulo_padre = $idmodulopadre AND it.id_modulo_versione = $idmoduloversione";

	$result_1 = $conn->query($query_1)->fetchAll();

	if (count($result_1) == 1) {
		$id_istanza_testata = $result_1[0]['id_istanza_testata'];
		$allegato 			= $result_1[0]['allegato'];
		$data_allegato		= $result_1[0]['data_allegato'];
		$allegato2			= $result_1[0]['allegato2'];
		$data_allegato2		= $result_1[0]['data_allegato2'];
		
		
		if($action == 's') 			// annulla istanza
		{
			$query="UPDATE istanze_testata 
					SET annullamento = 's', 
						ope_annullamento = $idoperatore,
						data_annullamento = getdate()
					WHERE id_istanza_testata = $id_istanza_testata";
			$result = $conn->query($query);
			
			if(!empty($allegato))	annulla_istanza($pdf_1, '1', $conn, $dir_allegati, $dir_allegati_annullati, $allegato, $id_istanza_testata, $idoperatore);
			if(!empty($allegato2))	annulla_istanza($pdf_2, '2', $conn, $dir_allegati, $dir_allegati_annullati, $allegato2, $id_istanza_testata, $idoperatore);
			chiudi();
		}
		elseif($action == 'n')	// ripristina istanza eliminando l'annullamento
		{
			$query="UPDATE istanze_testata 
					SET annullamento = 'n', 
						ope_annullamento = NULL,
						data_annullamento = NULL
					WHERE id_istanza_testata = $id_istanza_testata";
			$result = $conn->query($query);
			
			if(!empty($allegato))	ripristina_istanza($pdf_1, '1', $conn, $dir_allegati, $dir_allegati_annullati, $allegato, $id_istanza_testata);
			if(!empty($allegato2))	ripristina_istanza($pdf_2, '2', $conn, $dir_allegati, $dir_allegati_annullati, $allegato2, $id_istanza_testata);
			chiudi();
		} 
		else die("Operazione non consentita");
	}	

} else echo "no var";



	function annulla_istanza($pdf, $num_allegato, $conn, $dir_allegati, $dir_allegati_annullati, $nome_allegato, $id_istanza_testata, $idoperatore)
	{

	
		$file_originale = $dir_allegati."\\".$nome_allegato;
		$file_annullato = $dir_allegati_annullati."\\".$nome_allegato;
		
		// sposto il file originale per tenermi una copia e aggiorno l'istanza nel DB con l'allegato annullato
		if(rename($file_originale, $file_annullato)) 
		{

			// se lo spostamento dell'originale e' avvenuto con successo
			// aggiorno l'istanza nel dba_close
			// e procedo alla creazione del file con filigrana ANNULLATO
			if(file_exists($file_annullato))
			{
				$pagecount = $pdf->setSourceFile($file_annullato); 

				// aggiungo la filigrana ad ogni pagina
				for($i=1;$i<=$pagecount;$i++){ 
					$tpl = $pdf->importPage($i); 
					$size = $pdf->getTemplateSize($tpl); 
					$pdf->addPage(); 
					$pdf->useTemplate($tpl, 1, 1, $size['width'], $size['height'], TRUE); 
					$pdf->Rotate(60,20,270);
					$pdf->Image('vendor\setasign\fpdi\src\ANNULLATO.png',20,260,290,45);
					$pdf->Rotate(0);
				} 
				
				// salvo il file 
				$pdf->Output($file_originale,'F');
				
				echo "Allegato $num_allegato annullato.<br>";		
			}
			else echo("Allegato $num_allegato non trovato.<br>"); 
			 	
		}
		else 
			die("Impossibile proseguire con l'azione di annullamento.<br>Forse sono stati cambiati i permessi di lettura/scrittura alle cartelle?<br><br>");
		
	}


	function ripristina_istanza($pdf, $num_allegato, $conn, $dir_allegati, $dir_allegati_annullati, $nome_allegato, $id_istanza_testata)
	{
		$file_annullato = $dir_allegati."\\".$nome_allegato;
		$file_originale = $dir_allegati_annullati."\\".$nome_allegato;
		
		unlink($file_annullato);
		
		// ripristino il file originale
		if(rename($file_originale, $file_annullato))
		{
			echo "Allegato $num_allegato ripristinato.<br>";		
		} 
		else 
			die("Impossibile proseguire con l'azione di annullamento.<br>Forse sono stati cambiati i permessi di lettura/scrittura alle cartelle?<br><br>");
	}

	function chiudi() {
		echo "<br>Chiusura automatica in 3 secondi..
		<script>let seconds = 4;
				let close = setInterval(function() {
					seconds--;
					
					if(seconds == 0) {
						clearInterval(close);
						window.close('','_parent','');
					}
				}, 1000);</script>";
	}
?>