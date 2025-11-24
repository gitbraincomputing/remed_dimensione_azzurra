<?php
/* 	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); */
	
 	if (isset($_POST['idcartella']) 	 && 
		isset($_POST['idpaziente']) 	 && $_POST['idpaziente'] > 0 	  	&&
		isset($_POST['idmodulopadre']) 	 && 
		isset($_POST['idmoduloversione'])&& 
		isset($_POST['idinserimento']) 	 && 
		isset($_POST['uid']) 			 && $_POST['uid'] > 0 			  	&&
		isset($_POST['idregime']) 		 && 
		isset($_POST['idimpegnativa']) 	 &&
		isset($_POST['idimpegnativa_tc'])&&
		isset($_POST['tipoallegato'])	 && 
		isset($_POST['secondoallegato']) && 
		isset($_POST['usephp']) 		 && $_POST['usephp']=='7'			&&
		isset($_POST['access_token']) 	 && !empty($_POST['access_token'])	&&
		isset($_POST['id_documento']) 	 && !empty($_POST['id_documento'])	&&
		isset($_POST['nome_documento'])  && !empty($_POST['nome_documento'])&&
		isset($_POST['source']) 	 	 && !empty($_POST['source'])	
		)
    {

		$idcartella 		= $_POST['idcartella'];
		$idpaziente 		= $_POST['idpaziente'];
		$idmodulopadre 		= $_POST['idmodulopadre'];
		$idmoduloversione 	= $_POST['idmoduloversione'];
		$idinserimento 		= $_POST['idinserimento'];
		$idoperatore 		= $_POST['uid'];
		$idregime 			= $_POST['idregime'];
		$idimpegnativa 		= $_POST['idimpegnativa'];
		$tipoallegato 		= $_POST['tipoallegato'];
		$secondoallegato 	= $_POST['secondoallegato'];
		$access_token 		= $_POST['access_token'];
		$DocumentId	  		= $_POST['id_documento'];
		$DocumentName 		= $_POST['nome_documento'];   													 // es. "471_FEA_20231214_173545_RegolamentoRD1.pdf"
		$descrizione 		= substr($_POST['nome_documento'], strrpos($_POST['nome_documento'], "_") + 1);	 // es. invece di "471_FEA_20231214_173545_RegolamentoRD1.pdf" prendo solo "RegolamentoRD1.pdf"
		$descrizione 		= substr($descrizione, 0, -4);													 // es. invece di "RegolamentoRD1.pdf" prendo solo "RegolamentoRD1"
		$source       		= $_POST['source'];
		$tbl          		= $_POST['tbl'];
		
		
		// carico le configurazioni dal file config.rdm.php 
		require_once 'assets/config.rdm.php';
		require_once 'confirmo_digital_doc_sign.php';

		$Base64DownloadedDocument = DownloadDocument($access_token, $DocumentId);
		
		
			if($source == 'remed') 	$path_pdf_firmato 	= "../../modelli_word/allegato";
		elseif($source == 'tc') 
		{
				if($tbl == 'ua')  	$path_pdf_firmato = "../../allegati_utenti";					// utenti_allegati
			elseif($tbl == 'uai')	$path_pdf_firmato = "../../allegati_utenti/amministrativi/";	// utenti_allegati_impegnative

		}
		
		$pdf_decoded = $Base64DownloadedDocument;
		$pdf = file_put_contents($path_pdf_firmato."/".$DocumentName, $pdf_decoded);


		header('Content-type: application/json');

		if($source == 'remed') 
		{
			if (empty($idimpegnativa) || $idimpegnativa == 'NULL')
				 $imp_query = "";
			else $imp_query = "AND it.id_impegnativa = $idimpegnativa ";
			
			
			$query_get_iit = "SELECT it.id_istanza_testata FROM istanze_testata AS it 
								WHERE 
									it.id_cartella = $idcartella 
								AND it.id_modulo_versione = $idmoduloversione 
								AND it.id_inserimento = $idinserimento 
									$imp_query";
									//echo $query_get_iit ."<br><br>";
			$result_get_iit = $conn->query($query_get_iit)->fetchAll();
			$id_istanza_testata_da_firmare = $result_get_iit[0]['id_istanza_testata'];

			$query = "SELECT DISTINCT uc.idregime, m.idmodulo as id_modulo_padre, m.validazione_automatica
						FROM moduli AS m
						INNER JOIN istanze_testata AS it ON it.id_modulo_versione = m.id
						INNER JOIN utenti_cartelle AS uc ON uc.id = it.id_cartella
						WHERE m.id = $idmoduloversione";
			$info_modulo = $conn->query($query)->fetchAll();
			$info_modulo = $info_modulo[0];
			
			$query = "SELECT dir_sanitario as is_ds
						FROM operatori
						WHERE uid = $idoperatore";
			$info_ope = $conn->query($query)->fetchAll()[0];
			$info_ope = $info_ope[0];
			
			$cstm_set = "";
			//eseguo la validazione automatica per tutti i moduli che la prevedono o nel caso in cui sia il ds a fare 
			//	il caricamento dell'allegato. Disabilito questa operazione per i moduli delle visite specialistiche
			if( $info_modulo['validazione_automatica'] && $info_ope['is_ds'] == 'y' &&
				($secondoallegato == 0 || ($secondoallegato == 1 && $tipoallegato == 'allegato2'))
			){
				$cstm_set = ", ope_validazione = $idoperatore, data_validazione = getdate(), validazione = 's' ";
			}
			
			$cstm_set = "";
			$query_set_allegato = "UPDATE istanze_testata 
									SET ".$tipoallegato." = '$DocumentName', 
										data_".$tipoallegato." = getdate() $cstm_set
									WHERE id_istanza_testata = $id_istanza_testata_da_firmare";
			
			if($conn->query($query_set_allegato))
				 $set_allegato = true;
			else $set_allegato = false;


			if($tipoallegato == 'allegato' && $secondoallegato == 1)	// sto firmando il primo allegato e so che il modulo ne prevede un secondo
				$update_firma_allegati = ", stato_firma_allegato2='a'";
			elseif($tipoallegato == 'allegato2')
				$update_firma_allegati = ", stato_firma_allegato2='s'";
			else 
				$update_firma_allegati = "";
			
			
			$query_set_firma = "UPDATE istanze_testata_firme 
								SET 
									stato_firma_".$tipoallegato."='s', 
									data_firma_".$tipoallegato."=getdate(),
									nome_".$tipoallegato." = '".$DocumentName."'
									".$update_firma_allegati." 
								WHERE 
									id_istanza_testata = $id_istanza_testata_da_firmare 
								AND id_operatore = $idoperatore";
			if($conn->query($query_set_firma))
				 $set_firma = true;
			else $set_firma = false;
			
			if ($set_allegato && $set_firma) {
				$response['titolo'] = "Documento scaricato e associato!";
				$response['sottotitolo'] = "Puoi chiudere questa finestra.";
				$response['esito'] = "0";
				
				/*ob_start();
				system('"C:\Program Files\Adobe\Acrobat DC\Acrobat\Acrobat.exe" ' . $path_pdf_firmato.'/'.$DocumentName);
				ob_end_clean();
				*/

			} else {
				$response['titolo'] = "Documento scaricato ma non associato.";
				$response['sottotitolo'] = "Il database non ha risposto in un tempo ragionevole e non è stato possibile associare il documento all'istanza.<br><br>Siete pregati di aprire un ticket di assistenza.<br><br>set_allegato = $set_allegato<br>set_firma = $set_firma";
				$response['esito'] = "1";
			}
			echo json_encode($response);

		}
		
		
		
		
		
		elseif($_POST['source'] == 'tc') 
		{
			$insert_allegato = false;
			$move_allegato 	 = false;


			if($tbl == 'ua')
			{
				$query_set_allegato = "INSERT INTO utenti_allegati 
											(IdUtente, descrizione, file_name,
											 tipo, type, datains, opeins)
										VALUES
											($idpaziente, '$descrizione', '$DocumentName', 
											 '1', 'application/pdf', getdate(), $idoperatore)";
				if($conn->query($query_set_allegato))
				{
					$insert_allegato = true;
					
					if( rename($path_pdf_firmato."/".$DocumentName, "C:/web/republic/allegati_utenti/".$DocumentName) )
						$move_allegato = true;
				} 
					
			}
			
			
			elseif($tbl == 'uai')
			{			
				if(!empty($_POST['idimpegnativa_tc']))
					 $idimpegnativa_tc = $_POST['idimpegnativa_tc'];
				else $idimpegnativa_tc = 'NULL';
				
				$query_set_allegato = "INSERT INTO utenti_allegati_imp
											(IdUtente, descrizione, file_name, stampa, 
											 tipo, type, datains, opeins, idimpegnativa)
										VALUES
											($idpaziente, '$descrizione', '$DocumentName', 'no', 
											 '1', 'application/pdf', getdate(), $idoperatore, $idimpegnativa_tc)";
				//die($query_set_allegato);
				if($conn->query($query_set_allegato))
				{
					$insert_allegato = true;
					if( rename($path_pdf_firmato."/".$DocumentName, "C:/web/republic/allegati_utenti/amministrativi/".$DocumentName) )
						$move_allegato = true;
				}
			}	
			
			if($insert_allegato && $move_allegato)
			{
				$response['titolo'] = "Documento scaricato e associato!";
				$response['sottotitolo'] = "Puoi chiudere questa finestra.";
				$response['esito'] = '0';
				
				/*ob_start();
				system('"C:\Program Files\Adobe\Acrobat DC\Acrobat\Acrobat.exe" ' . $path_pdf_firmato.'/'.$DocumentName);
				ob_end_clean();*/

			}
			else 
			{
				if($insert_allegato && !$move_allegato) {
					$response['titolo'] = "ERRORE<br>Record inserito, file non spostato!";
					$response['sottotitolo'] = "Vi preghiamo di aprire un ticket di assistenza, grazie.";
					$response['esito'] = '1';
				}
				elseif(!$insert_allegato && !$move_allegato) {
					$response['titolo'] = "ERRORE<br>Record non inserito, file non spostato!";
					$response['sottotitolo'] = "Vi preghiamo di aprire un ticket di assistenza, grazie.";
					$response['esito'] = '1';
				}
			}		
			
			echo json_encode($response);			
		}
		
   } else {
	   
		$response['esito'] = "1";
		$response['titolo'] = "Invio delle informazioni non riuscito.";
		$response['sottotitolo'] = "Vi preghiamo di aprire un ticket di assistenza, grazie.";
		echo json_encode($response);
   }