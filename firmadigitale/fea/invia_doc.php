<?php

	 
   	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); 
	
	

 	if (isset($_POST['idcartella']) 	 && 
		isset($_POST['idpaziente']) 	 && $_POST['idpaziente'] > 0 	  &&
		isset($_POST['idmodulopadre']) 	 && 
		isset($_POST['idmoduloversione'])&& 
		isset($_POST['idinserimento']) 	 && 
		isset($_POST['uid']) 			 && $_POST['uid'] > 0 			  &&
		isset($_POST['idregime']) 		 && 
		isset($_POST['idimpegnativa']) 	 &&
		isset($_POST['tipoallegato'])	 && 
		isset($_POST['secondoallegato']) && 
		isset($_POST['usephp']) 		 && $_POST['usephp']=='7'			&&
 	    isset($_POST['access_token']) 	 && !empty($_POST['access_token'])	&&
 	    isset($_POST['device_id']) 		 && $_POST['device_id'] > 0			&&
		isset($_POST['source']) 	 	 && !empty($_POST['source'])		&&
 	   (isset($_POST['rtf']) 			 || isset($_POST['pdf']))
	   )
    {

		$idpaziente 		= $_POST['idpaziente'];
		$idcartella 		= $_POST['idcartella'];
		$idmoduloversione 	= $_POST['idmoduloversione'];
		$idinserimento 		= $_POST['idinserimento'];
		$idoperatore 		= $_POST['uid'];		
		$device_id 			= $_POST['device_id'];
		$access_token 		= $_POST['access_token'];
		$rtf 				= $_POST['rtf'];
		$pdf 				= $_POST['pdf'];
		$idimpegnativa 		= $_POST['idimpegnativa'];
		$source       		= $_POST['source'];
		
			if(!empty($rtf) &&  empty($pdf))			$file_type = 'rtf';
		elseif( empty($rtf) && !empty($pdf))			$file_type = 'pdf';
		elseif( empty($rtf) &&  empty($pdf)) {
			$response['esito'] = 1;
			$response['titolo'] = "Documento da inviare non specificato.";
			$response['sottotitolo'] = "";
			echo json_encode($response);
			die();
		}
		
		require_once 'confirmo_digital_doc_sign.php';


		$path_temp_pdf = "temp_pdf";		// definisco e creo la cartella temporanea se non esiste
		if (!is_dir($path_temp_pdf)) 
			mkdir($path_temp_pdf);
	
		if($file_type == 'rtf') 
		{
			
			if (file_exists($rtf)) 
			{
				
				
				ob_start();
				system('"C:\Program Files\LibreOffice\program\soffice" "-env:UserInstallation=file:///C:/web/Republic/firmadigitale/temp" --headless --convert-to pdf ' . $rtf . ' --outdir ' . $path_temp_pdf, $esito_convers);
				ob_end_clean();
				
				if($esito_convers == '0')  		// comando inoltrato correttamente
				{
					$arr_tmp_filename_dest = explode('/', $rtf);
					$signaturePdfFile = end($arr_tmp_filename_dest);		// END prende l'ultimo indice dell'array creato dall'explode
					$signaturePdfFile = substr($signaturePdfFile, 0, -4).".pdf";				// tolgo l'estensione ".rtf" dal nome e metto ".pdf"
					$pdf_path = $path_temp_pdf."/".$signaturePdfFile;
					
					$s = 0;
					while(!file_exists($pdf_path)) 
					{
						sleep(1);   // ad ogni check attendo 1 secondo e lo ripeto, finchè il file non esiste e vado avanti con addPDF
						$s++;
						if($s > 60) {
							$response['esito'] = 1;
							$response['titolo'] = "Errore durante la creazione del file PDF (timeout)<br>$pdf_path.";
							$response['sottotitolo'] = "Riprova aggiornando questa pagina. Se l'errore dovesse ripetersi, aprire un ticket di assistenza.";
							echo json_encode($response);
							die();
						}
					}		
				} else {
				
					$response['esito'] = 1;
					$response['titolo'] = "Errore durante la creazione del file PDF (system)<br>$pdf_path.";
					$response['sottotitolo'] = "Riprova aggiornando questa pagina. Se l'errore dovesse ripetersi, aprire un ticket di assistenza.";
					echo json_encode($response);
					die();
				}
				
				$pdf_content = base64_encode(file_get_contents($pdf_path));
			
			} else {
				
				$response['esito'] = 1;
				$response['titolo'] = "Errore, documento da firmare non trovato!";
				$response['sottotitolo'] = "";
				echo json_encode($response);
				die();
			}
			
		}
		else 
		{
		
			if (file_exists($pdf)) 
			{
				$pdf_path = $pdf;

				$arr_tmp_filename_dest = explode('\\', $pdf);
				$signaturePdfFile = str_replace("_", "-", end($arr_tmp_filename_dest));				// END prende l'ultimo indice dell'array creato dall'explode
				$signaturePdfFile = substr($signaturePdfFile, 0, -4).".pdf";						// tolgo l'estensione ".rtf" dal nome e metto ".pdf"
				
				$pos = strpos($signaturePdfFile, "_");		// verifico che esiste un "_" nel file name
				if($pos > 0) {
					$signaturePdfFile = substr($signaturePdfFile, strpos($signaturePdfFile, "_") + 1);	// il nome del file è, ad es., "471_privacy.pdf", prendo solo "privacy.pdf"
				}
				
				$pdf_content = base64_encode(file_get_contents($pdf_path));
			} else {
				$response['esito'] = 1;
				$response['titolo'] = "Errore, documento da firmare non trovato!";
				$response['sottotitolo'] = "";
				echo json_encode($response);
				die();
			}
		}
	
		$documentTitle = $idpaziente."_FEA_".date('Ymd_His')."_".$signaturePdfFile;
		


		//$pdf_content = str_replace("\\", "", $pdf_content);

		
		
		$IdPdfDocument = SendPdfDocument($access_token, $pdf_content, $pdf_path, $documentTitle, $idpaziente, $idoperatore);

		if($IdPdfDocument == "-1")
		{
			$response['esito'] = 1;
			$response['titolo'] = "Il modulo non contiene campi di firma validi.";
			$response['sottotitolo'] = "Assicurarsi che debba essere soggetto a firma grafometrica.";
			echo json_encode($response);
			die();	
		}
		else 
		{
			if(property_exists($IdPdfDocument, 'statusCode')) 
			{
				if($IdPdfDocument->statusCode == 500) 
				{
					$response['esito'] = 1;
					$response['titolo'] = "Errore invio documento (IdPdfDocument) [".$IdPdfDocument->message."]";
					$response['sottotitolo'] = "";
					echo json_encode($response);
					die();	
				}
			}
		}
			
		$IdPdfDocument = $IdPdfDocument->DocumentID;
		
		if($source == 'remed') 
		{
			// carico le configurazioni dal file config.rdm.php 
			require 'assets/config.rdm.php';
		
			if (empty($idimpegnativa) || $idimpegnativa == 'NULL')
				 $imp_query = "";
			else $imp_query = "AND id_impegnativa = $idimpegnativa ";
			
			$query_set_id_doc_confirmo = "UPDATE istanze_testata 
											SET id_doc_confirmo_fea = $IdPdfDocument
											WHERE 
												id_cartella = $idcartella 
											AND id_modulo_versione = $idmoduloversione 
											AND id_inserimento = $idinserimento 
												$imp_query";
			$conn->query($query_set_id_doc_confirmo);
		}
		
		//var_dump($IdPdfDocument);
		
		$sendStatus = SendPdfToTablet($access_token, $IdPdfDocument, $_POST['device_id']);
		//var_dump($sendStatus);
		
		if(property_exists($sendStatus, 'CodiceErrore') && $sendStatus->CodiceErrore > 0) 
		{
			$response['esito'] = 1;
			$response['titolo'] = "Errore invio documento (sendStatus) [".$sendStatus->CodiceErrore."]";
			$response['sottotitolo'] = "";
			echo json_encode($response);
			die();	
		}
		
		$response['esito'] = "0";
		$response['titolo'] = "In attesa di firma.";
		$response['sottotitolo'] = "Documento firmato? Clicca sul pulsante per continuare.";
		$response['nome_file'] = $documentTitle;
		$response['id_doc'] = $IdPdfDocument;
		echo json_encode($response);
		die();	
		
		
		

   } else {
	   
		$response['esito'] = 1;
		$response['titolo'] = "Invio delle informazioni non riuscito.";
		$response['sottotitolo'] = "Vi preghiamo di aprire un ticket di assistenza, grazie.";
		echo json_encode($response);
   }