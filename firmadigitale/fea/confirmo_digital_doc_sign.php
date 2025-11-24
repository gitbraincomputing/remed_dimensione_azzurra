<?php

    class ConfigConfirmo {
        public static $mode_stage = false;
        
        // url stage
        public static $url_api_stage  = "https://api-v2.confirmo.it/braincomputing/api/integrations/";
		public static $username_stage = "sviluppo@braincomputing.com";
		public static $password_stage = "#Check23LmBc!";
		
        // url produzione
        public static $url_api_prod  = "https://api-v2.confirmo.it/buonincontro/api/integrations/";
		public static $username_prod = "api-integration";
        public static $password_prod = "C1@80^NP9yfPXof%su6n";
		
		// url endpoint
		public static $endpoint_auth 				= "document/auth_token";					// login and get auth token
		public static $endpoint_sign_request 		= "document/start_signature_request";		// send a document to be signed by the specified recipients
		public static $endpoint_get_tablet_list 	= "document/graph_devices";					// Provides the list of devices enabled for graphometric signature
		public static $endpoint_send_pdf_doc		= "document/start_signature_request";		
		public static $endpoint_send_to_tablet  	= "document/open_on_device";				
		public static $endpoint_download_signed_doc = "document/download_document";				

		
    }

    function getOauthToken() 
	{
		
        if (ConfigConfirmo::$mode_stage) 
		{
			$url_api  = ConfigConfirmo::$url_api_stage;
			$username = ConfigConfirmo::$username_stage;
			$password = ConfigConfirmo::$password_stage;
        } else {
			$url_api  = ConfigConfirmo::$url_api_prod;
			$username = ConfigConfirmo::$username_prod;
			$password = ConfigConfirmo::$password_prod;
        }
    
        $payloadName = array(
            'username' => $username,
            'password' => $password
        );
		
        
		
		
        $ch = curl_init($url_api . ConfigConfirmo::$endpoint_auth);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true); // enable tracking
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloadName));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $response = curl_exec($ch);

/* 			echo "<br><br>HEADER<br>";
			var_dump(curl_getinfo($ch)); // request headers
			
			
			echo "<br><br>BODY<br>";
			var_dump(json_encode($payloadName)); 		
			
			curl_close($ch);
			
			echo "<br><br>RESULT:<br>";
			return json_decode($response); */
			
        if ($response === FALSE) {
            echo("41 cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
        } else {
			return json_decode($response); 
		}
    }
	
	
    function getTabletList($token) 
	{
		
		if (ConfigConfirmo::$mode_stage) 
		{
			$url_api  = ConfigConfirmo::$url_api_stage;
			$username = ConfigConfirmo::$username_stage;
			$password = ConfigConfirmo::$password_stage;
        } else {
			$url_api  = ConfigConfirmo::$url_api_prod;
			$username = ConfigConfirmo::$username_prod;
			$password = ConfigConfirmo::$password_prod;
        }
		
		$body = array(
            'Token' => $token
        );
		
		
        $ch = curl_init($url_api . ConfigConfirmo::$endpoint_get_tablet_list);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true); // enable tracking
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $response = curl_exec($ch);

/* 			echo "<br><br>HEADER<br>";
			var_dump(curl_getinfo($ch)); // request headers
			
			
			echo "<br><br>BODY<br>";
			var_dump(json_encode($payloadName)); 		
			
			curl_close($ch);
			
			echo "<br><br>RESULT:<br>";
			return json_decode($response); */
			
        if ($response === FALSE) {
            echo("getTabletList cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
        } else {
			return json_decode($response); 
		}
    }	
	
	
    function SendPdfDocument($token, $pdf_content, $pdf_path, $documentTitle, $idpaziente, $idoperatore) 
	{
		
		// carico le configurazioni dal file config.rdm.php 
		require 'assets/config.rdm.php';
		include 'vendor/autoload.php';
		
				
		if (ConfigConfirmo::$mode_stage) 
		{
			$url_api  = ConfigConfirmo::$url_api_stage;
			$username = ConfigConfirmo::$username_stage;
			$password = ConfigConfirmo::$password_stage;
        } else {
			$url_api  = ConfigConfirmo::$url_api_prod;
			$username = ConfigConfirmo::$username_prod;
			$password = ConfigConfirmo::$password_prod;
        }
	
		// carico la libreria
		$parser = new \Smalot\PdfParser\Parser();
		$PDF = $parser->parseFile($pdf_path);
		$testo_pdf = $PDF->getText();
		//echo nl2br($testo_pdf);
		$body_array_recipients = [];
		$signatureOrder = 1;
		
		// Esegui la ricerca utilizzando preg_match_all
		if (preg_match_all('/\$signature_firma(.*?)_/', $testo_pdf, $matches)) 
		{
			// $matches[0] contiene tutte le occorrenze complete
			// $matches[1] contiene le sottostringhe desiderate
			//var_dump($matches[0]);

			foreach ($matches[1] as $key => $tipo_firmatario) 
			{
				//$signatureOrder++;
				//echo "Sottostringa: '$tipo_firmatario'<br>";
				
				
				
				
// ###################
// ## OPERATORE ######
// ###################
				if( strpos($tipo_firmatario, 'operatore') !== false) 			// se firma un operatore (che sarà sempre il DS)
				{

					$queryDS = "SELECT uid, nome, codice_fiscale FROM operatori WHERE dir_sanitario = 'y'";
					$resultDS = $conn->query($queryDS)->fetchAll();

					if (count($resultDS) > 0)
					{
						// cerco se e' presente questo codice fiscale del DS nell'array
						$pos = array_search($resultDS[0]['codice_fiscale'], array_column($body_array_recipients, 'taxCode') );
						
						// se e' il primo campo firma che trovo legato al DS, 
						// ne compongo l'array delle informazioni da associare al campo firma
						if($pos === false)
						{	
							$array_recipients = array (
								"externalID" 	=> $resultDS[0]['uid'],
								"familyName" 	=> $resultDS[0]['nome'],
								"givenName" 	=> '',
								"birthDate" 	=> '1900-01-01',
								"birthState" 	=> '',
								"birthCountry" 	=> '',
								"residencePlace"=> '',
								"address" 		=> '',
								"contactPhone" 	=> '',
								"taxCode" 		=> $resultDS[0]['codice_fiscale'],
								"signerRoleName"=> "Direttore Sanitario",
								"signatureOrder" => $signatureOrder,
								"signatureType" => "GRAPH",
								"placeholders" 	=> array(
									array(
										"floating" 	=> true, 
										"field" 	=> "signature",
										"required"  => false,
										"fieldName" => $tipo_firmatario
									),
								)
							);
							array_push($body_array_recipients, $array_recipients);
						}
						else 	// se trovo già il paziente, inserisco solo un altro placeholder
						{	 	// per indicare la presenza di un'altra firma
							//echo " -> l'ho già inserito<br>";
							$array_recipients =  array (
									"floating" => true, 
									"field" => "signature",
									"required" => false,
									"fieldName" => $tipo_firmatario
								);
							array_push($body_array_recipients[$pos]['placeholders'], $array_recipients);
						}
					}
					else return("DS non trovato");
				}
	
// ###################
// ## PAZIENTE #######
// ###################

				elseif( strpos($tipo_firmatario, 'paziente') !== false 		&& 
						strpos($tipo_firmatario, 'pazientetutore') === false ) 			// se firma il paziente
				{
					// prendo i dati del paziente
					$queryPaziente = "SELECT IdUtente, Cognome, Nome, Sesso, codice_fiscale,
											 data_nascita, comune_nascita, provincia_nascita,
											 comune_residenza, indirizzo, 
											 telefono, cellulare
										FROM re_dati_utenti_stampa
										WHERE IdUtente = $idpaziente";
					$resultPaziente = $conn->query($queryPaziente)->fetchAll();
					
					if (count($resultPaziente) > 0)
					{
						// cerco se e' presente questo ID paziente nell'array
						$pos = array_search($resultPaziente[0]['IdUtente'], array_column($body_array_recipients, 'externalID') );
						
						// se e' il primo campo firma che trovo legato al paziente, 
						// ne compongo l'array delle informazioni da associare al campo firma
						if($pos === false)
						{	
							
							if(trim($resultPaziente[0]['Sesso']) =="1")		$gender = "M";
							else 											$gender = "F";
							
								if(trim($resultPaziente[0]['cellulare']) !== "")	$contactPhone = $resultPaziente[0]['cellulare'];
							elseif(trim($resultPaziente[0]['telefono']) !== "")		$contactPhone = $resultPaziente[0]['telefono'];
							else 													$contactPhone = "";
						
							$array_recipients = array (
								"externalID" 	=> $resultPaziente[0]['IdUtente'],
								"familyName" 	=> $resultPaziente[0]['Cognome'],
								"givenName" 	=> $resultPaziente[0]['Nome'],
								"birthDate" 	=> date("Y-m-d", strtotime($resultPaziente[0]['data_nascita'])),
								"gender" 		=> $gender,
								"birthPlace" 	=> $resultPaziente[0]['comune_nascita'],
								"birthState" 	=> $resultPaziente[0]['provincia_nascita'],
								"birthCountry" 	=> '',
								"taxCode" 		=> $resultPaziente[0]['codice_fiscale'],
								"residencePlace"=> $resultPaziente[0]['comune_residenza'],
								"address" 		=> $resultPaziente[0]['indirizzo'],
								"contactPhone" 	=> $contactPhone,
								"signerRoleName"=> "Paziente",
								"signatureOrder" => $signatureOrder,
								"signatureType" => "GRAPH",
								"placeholders" 	=> array(
									array(
										"floating" 	=> true, 
										"field" 	=> "signature",
										"required" => false,
										"fieldName" => $tipo_firmatario
									),
								)
							);
							array_push($body_array_recipients, $array_recipients);
						}
						else 	// se trovo già il paziente, inserisco solo un altro placeholder
						{	 	// per indicare la presenza di un'altra firma
							$array_recipients =  array (
									"floating" => true, 
									"field" => "signature",
									"required" => false,
									"fieldName" => $tipo_firmatario
								);
							array_push($body_array_recipients[$pos]['placeholders'], $array_recipients);
						}
					}
				
				} else {


				
// ####################
// ## PAZIENTE/TUTORE #
// ## TUTORE1 #########
// ## TUTORE2 #########
// ####################

// verifico quale tutore deve firmare e quale sto leggendo in DB
					if( strpos($tipo_firmatario, 'pazientetutore') !== false || 
						strpos($tipo_firmatario, 'tutore1') !== false)
						$where = " AND (tutoreprincipale = -1 OR tutore1 = -1)";
					elseif(strpos($tipo_firmatario, 'tutore2') !== false)
						$where = " AND tutore2 = -1";
					elseif(strpos($tipo_firmatario, 'delegato1') !== false)
						$where = " AND delegato1 = -1";
					elseif(strpos($tipo_firmatario, 'delegato2') !== false)
						$where = " AND delegato2 = -1";
					else $where = "";
					
							
					// verifico se esistono tutori attivi per il paziente
					$queryTutori = "SELECT TOP(1) * FROM re_utenti_tutor 
									WHERE idutente = $idpaziente $where
									ORDER BY tutoreprincipale DESC, tutore1 DESC, tutore2 DESC";
					$resultTutori = $conn->query($queryTutori)->fetchAll();
					if(count($resultTutori) > 0)	
					{
						$paz_externalID = '';
						
						foreach($resultTutori as $rowTutori)
						{
							// cerco se e' presente il codice fiscale del tutore nell'array
							$pos = array_search($rowTutori['codice_fiscale'], array_column($body_array_recipients, 'taxCode') );
								
							// se e' il primo campo firma che trovo legato al tutore che sto ciclando, 
							// ne compongo l'array delle informazioni da associare al campo firma
							if($pos === false)
							{	
						
								if(trim($rowTutori['sesso']) =="1")		$gender = "M";
								else 									$gender = "F";
							
								if(trim($rowTutori['cellulare']) !== "")		$contactPhone = $rowTutori['cellulare'];
								elseif(trim($rowTutori['telefono']) !== "")		$contactPhone = $rowTutori['telefono'];
								else 											$contactPhone = "";
								
								$array_recipients = array (
									"externalID" 	=> '',
									"familyName" 	=> $rowTutori['cognome'],
									"givenName" 	=> $rowTutori['nome'],
									"birthDate" 	=> date("Y-m-d", strtotime($rowTutori['data_nascita'])),
									"gender" 		=> $gender,
									"birthPlace" 	=> $rowTutori['nome_comune_n'],
									"birthState" 	=> $rowTutori['nome_prov_n'],
									"birthCountry" 	=> '',
									"taxCode" 		=> $rowTutori['codice_fiscale'],
									"residencePlace"=> $rowTutori['comune_r'],
									"address" 		=> $rowTutori['indirizzo'],
									"contactPhone" 	=> $contactPhone,
									"signerRoleName"=> trim($rowTutori['relazione_paziente']),
									"signatureOrder" => $signatureOrder,
									"signatureType" => "GRAPH",
									"placeholders" 	=> array(
										array(
											"floating" 	=> true, 
											"field" 	=> "signature",
											"required"  => false,
											"fieldName" => $tipo_firmatario,
											"fontFamily"=> "verdana",
											"fontSize" 	=> 10
										),
									)
								);
								array_push($body_array_recipients, $array_recipients);
							}
							else 	// se trovo già il tutore, inserisco solo un altro placeholder
							{	 	// per indicare la presenza di un'altra firma
								$array_recipients =  array (
										"floating" => true, 
										"field" => "signature",
										"required" => false,
										"fieldName" => $tipo_firmatario,
										"fontFamily" => "verdana",
										"fontSize" => 10
									);
								array_push($body_array_recipients[$pos]['placeholders'], $array_recipients);
							}
						}
					
					} else {				
						
						// prendo i dati del paziente
						$queryPaziente = "SELECT 
												IdUtente, Cognome, Nome, Sesso, codice_fiscale,
												data_nascita, comune_nascita, provincia_nascita,
												comune_residenza, indirizzo, 
												telefono, cellulare
											FROM re_dati_utenti_stampa
											WHERE IdUtente = $idpaziente";
						$resultPaziente = $conn->query($queryPaziente)->fetchAll();
						
						if (count($resultPaziente) > 0)
						{
							// cerco se e' presente questo ID paziente nell'array
							$pos = array_search($resultPaziente[0]['IdUtente'], array_column($body_array_recipients, 'externalID') );
							
							// se e' il primo campo firma che trovo legato al paziente, 
							// ne compongo l'array delle informazioni da associare al campo firma
							if($pos === false)
							{	
								
								if(trim($resultPaziente[0]['Sesso']) =="1")		$gender = "M";
								else 											$gender = "F";
								
									if(trim($resultPaziente[0]['cellulare']) !== "")	$contactPhone = $resultPaziente[0]['cellulare'];
								elseif(trim($resultPaziente[0]['telefono']) !== "")		$contactPhone = $resultPaziente[0]['telefono'];
								else 													$contactPhone = "";
							
								$array_recipients = array (
									"externalID" 	=> $resultPaziente[0]['IdUtente'],
									"familyName" 	=> $resultPaziente[0]['Cognome'],
									"givenName" 	=> $resultPaziente[0]['Nome'],
									"birthDate" 	=> date("Y-m-d", strtotime($resultPaziente[0]['data_nascita'])),
									"gender" 		=> $gender,
									"birthPlace" 	=> $resultPaziente[0]['comune_nascita'],
									"birthState" 	=> $resultPaziente[0]['provincia_nascita'],
									"birthCountry" 	=> '',
									"taxCode" 		=> $resultPaziente[0]['codice_fiscale'],
									"residencePlace"=> $resultPaziente[0]['comune_residenza'],
									"address" 		=> $resultPaziente[0]['indirizzo'],
									"contactPhone" 	=> $contactPhone,
									"signerRoleName"=> "Paziente",
									"signatureOrder" => $signatureOrder,
									"signatureType" => "GRAPH",
									"placeholders" 	=> array(
										array(
											"floating" 	=> true, 
											"field" 	=> "signature",
											"required"  => false,
											"fieldName" => $tipo_firmatario,
											"fontFamily"=> "verdana",
											"fontSize" 	=> 10
										),
									)
								);
								array_push($body_array_recipients, $array_recipients);
							}
							else 	// se trovo già il paziente, inserisco solo un altro placeholder
							{	 	// per indicare la presenza di un'altra firma
								$array_recipients =  array (
										"floating" => true, 
										"field" => "signature",
										"required" => false,
										"fieldName" => $tipo_firmatario,
										"fontFamily" => "verdana",
										"fontSize" => 10
									);
								array_push($body_array_recipients[$pos]['placeholders'], $array_recipients);
							}
						}	
					}
				}
			}
		}
		
		else return "-1";

		/* echo json_encode($body_array_recipients); 
		die(); */
		
		$body = array (
				"Token" => $token,
				"recipients" => $body_array_recipients,
				"documentBase64" => $pdf_content,
				"documentTitle" => $documentTitle,
				"notification" => array(
					"subject" => "oggetto test",
					"message" => "teso test"
				)
			);

		
		
        $ch = curl_init($url_api . ConfigConfirmo::$endpoint_send_pdf_doc);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true); // enable tracking
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $response = curl_exec($ch);

/*
 			echo "<br><br>HEADER<br>";
			var_dump(curl_getinfo($ch)); // request headers
		
			echo "<br><br>BODY<br>";
  			var_dump(json_encode($body)); 		
	
			echo "<br><br>RESULT:<br>";
			return json_decode($response);  
*/	
			
			curl_close($ch);
			
        if ($response === FALSE) {
            echo("SendPdfDocument cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
			die();
        }
		return json_decode($response); 
		
    }
	
	
    function SendPdfToTablet($token, $IdDocument, $IdTablet) 
	{
		
		if (ConfigConfirmo::$mode_stage) 
		{
			$url_api  = ConfigConfirmo::$url_api_stage;
			$username = ConfigConfirmo::$username_stage;
			$password = ConfigConfirmo::$password_stage;
        } else {
			$url_api  = ConfigConfirmo::$url_api_prod;
			$username = ConfigConfirmo::$username_prod;
			$password = ConfigConfirmo::$password_prod;
        }
		
		$body = array(		
			"Token" => $token,
			"DocumentID" => "$IdDocument",
			"IDTablet" => $IdTablet
        );
		
		
        $ch = curl_init($url_api . ConfigConfirmo::$endpoint_send_to_tablet);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true); // enable tracking
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $response = curl_exec($ch);

 			/*echo "<br><br>HEADER<br>";
			var_dump(curl_getinfo($ch)); // request headers*/
			
			
			/*echo "<br><br>BODY<br>";
			var_dump(json_encode($body)); 		*/
			
			curl_close($ch);
			
			/* echo "<br><br>RESULT:<br>";
			return json_decode($response);   */
			
        if ($response === FALSE) {
            echo("SendPdfDocument cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch)));
        } else {
			return json_decode($response); 
		}
    }	
	
	
	
    function DownloadDocument($token, $IdDocument) 
	{
		
		if (ConfigConfirmo::$mode_stage) 
		{
			$url_api  = ConfigConfirmo::$url_api_stage;
			$username = ConfigConfirmo::$username_stage;
			$password = ConfigConfirmo::$password_stage;
        } else {
			$url_api  = ConfigConfirmo::$url_api_prod;
			$username = ConfigConfirmo::$username_prod;
			$password = ConfigConfirmo::$password_prod;
        }
		
		
		$body = array(		
			"Token" => $token,
			"DocumentID" => "$IdDocument"
        );

		$curl = curl_init();

 		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url_api . ConfigConfirmo::$endpoint_download_signed_doc,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLINFO_HEADER_OUT => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 10,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => json_encode($body),
		  CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		));
		

	/*	echo "<br><br>HEADER<br>";
		var_dump(curl_getinfo($curl)); // request headers
		
		echo "<br><br>BODY<br>";
		var_dump(json_encode($body)); 		
*/
		$base64Document = curl_exec($curl);
		curl_close($curl);
/*
		echo "<br><br>RESULT:<br>";
		var_dump(json_encode($base64Document)); 
*/


		if ($base64Document == FALSE) {
			$response['esito'] = 1;
			$response['titolo'] = "Si è verificato un errore";
			$response['sottotitolo'] = "DownloadDocument cUrl error (#%d): %s<br>\n" . curl_errno($ch) . htmlspecialchars(curl_error($ch));
			echo json_encode($response);
			die();					
		}
		elseif($base64Document == NULL) 
		{
			$response['esito'] = 1;
			$response['titolo'] = "Documento non trovato.";
			$response['sottotitolo'] = "Forse la firma non è stata ancora eseguita?<br>Se invece il documento è stato firmato, potrebbe essere necessario riprovare qualche secondo.";
			echo json_encode($response);
			die();
		}
		elseif(property_exists($base64Document, 'error')) 
		{
			$response['esito'] = 1;
			$response['titolo'] = "Si è verificato un errore";
			$response['sottotitolo'] = "Se il problema dovesse persistere, vi preghiamo di aprire un ticket di assistenza. Grazie.";
			echo json_encode($response);
			die();					
		}
		else 
		{			
			header('Content-type: application/pdf');
			return $base64Document;
			
		}
    }

?>