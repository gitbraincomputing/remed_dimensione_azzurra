<?php
/* 	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); */

	 if(isset($_REQUEST['dirfilename']) 		&&
		isset($_REQUEST['idcartella']) 			&& $_REQUEST['idcartella'] > 0 	  		&&
		isset($_REQUEST['idpaziente']) 			&& $_REQUEST['idpaziente'] > 0 	  		&&
		isset($_REQUEST['idmodulopadre']) 	 	&& $_REQUEST['idmodulopadre'] > 0 	  		&&
		isset($_REQUEST['idmoduloversione']) 	&& $_REQUEST['idmoduloversione'] > 0 		&&
		isset($_REQUEST['idinserimento']) 	 	&& $_REQUEST['idinserimento'] > 0 	  		&&
		isset($_REQUEST['uid']) 				&& $_REQUEST['uid'] > 0 	  				&&
		isset($_REQUEST['usephp']) 				&& $_REQUEST['usephp'] == '7' 		  		&&
		isset($_REQUEST['tipoallegato'])		&& !empty($_REQUEST['tipoallegato'])
		)
	{
		
		$dirfilename = str_replace('/', '\\', $_REQUEST['dirfilename']);		// dir + nome_file
		
		if (!file_exists($dirfilename)) {
			echo "Il file non esiste in \n$dirfilename.";
		}
		else 
		{
			// carico le configurazioni dal file config.rdm.php generale
	 		$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));
			require_once($productionConfig); 
			
			
			$tipoallegato 	  = $_REQUEST['tipoallegato'];
			$secondoallegato  = $_REQUEST['secondoallegato'];
			$idcartella 	  = $_REQUEST['idcartella'];
			$idpaziente 	  = $_REQUEST['idpaziente'];
			$idmodulopadre	  = $_REQUEST['idmodulopadre'];
			$idmoduloversione = $_REQUEST['idmoduloversione'];
			$idinserimento 	  = $_REQUEST['idinserimento'];
			$idoperatore 	  = $_REQUEST['uid'];
			$dest_pdf = "c:\\web\\Republic\\modelli_word\\allegato\\";
			
			ob_start();
			system('"C:\Program Files\LibreOffice\program\soffice" "-env:UserInstallation=file:///C:/web/Republic/firmadigitale/temp" --headless --convert-to pdf ' . $dirfilename . ' --outdir ' . $dest_pdf, $esito_convers);
			ob_end_clean();
			if($esito_convers == '0')  		// comando inoltrato correttamente
			{
				$arr_dirfilename= explode('\\', $dirfilename);
				$nome_file_pdf = end($arr_dirfilename);					// END prende l'ultimo indice dell'array creato dall'explode
				$nome_file_pdf = substr($nome_file_pdf, 0, -4).".pdf";	// tolgo l'estensione ".rtf" dal nome e metto ".pdf"

				$s = 0;
				$dest_pdf = str_replace('\\', '/', $dest_pdf);
				while(!file_exists($dest_pdf."/".$nome_file_pdf)) 
				{
					sleep(1);   // ad ogni check attendo 1 secondo e lo ripeto, fino a max 60 sec
					$s++;
					if($s > 60) {
						echo "Errore durante la creazione del file PDF (timeout).";
						die();
					}
				}		
				
				$query_get_iit = "SELECT id_istanza_testata FROM istanze_testata WHERE id_cartella = $idcartella AND id_modulo_versione = $idmoduloversione AND id_inserimento = $idinserimento";
				//$result_get_iit= $conn->query($query_get_iit);
				$result_get_iit = $conn->query($query_get_iit)->fetchAll();
				$id_istanza_testata_da_associare = $result_get_iit[0]['id_istanza_testata'];
				
				$query_set_allegato = "UPDATE istanze_testata SET " . $tipoallegato . " = '$nome_file_pdf', data_" . $tipoallegato . " = getdate() 
										WHERE id_istanza_testata = $id_istanza_testata_da_associare";
				$result_set_allegato = $conn->query($query_set_allegato);
				
				unlink($dirfilename);
				
				//echo $query_set_allegato;
				echo "Esecuzione riuscita.<br>Chiusura automatica in 3 secondi..
				<script>let seconds = 4;
						let close = setInterval(function() {
							seconds--;
							
							if(seconds == 0) {
								clearInterval(close);
								window.close('','_parent','');
							}
						}, 1000);</script>";
				
				
									
					
			} else {
				echo "Errore durante la creazione del file PDF (system).";
				die();
			}
		}
	}
	
?>