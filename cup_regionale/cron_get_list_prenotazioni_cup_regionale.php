<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);


if(isset($_GET['usephp']) && $_GET['usephp']=='7')
{
	// in produzione carichiamo le configurazioni dal file config.rdm.php generale
	$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));
	require_once($productionConfig);
	 
	
// --------------------------------------------
// RECUPERO I CODICI AGENDA DI QUESTO CENTRO --
// --------------------------------------------
	$stmt_get_codici_agenda = "SELECT CodiceAgenda FROM cup_regionale_agende";
	$result_get_codici_agenda = $conn->query($stmt_get_codici_agenda)->fetchAll(PDO::FETCH_ASSOC);

	if (count($result_get_codici_agenda) == 0) {
		die("<p>ATTENZIONE: non sono memorizzati dei codici agenda in Database, impossibile proseguire.</p>");
	}
	else 
	{	
		foreach ($result_get_codici_agenda as $key=>$record) 
		{
			$codici_agende[$key] = $record['CodiceAgenda'];
		}
	}
	
	// array da passare alle varie CURL
	$dataCodiciAgende = array( 
		'externalDiaryId' => $codici_agende,
		'debug'			  => false		// con true scriviamo i log sul DB delle API
	);
	$dataCodiciAgende = json_encode($dataCodiciAgende);


// ---------------------------------
// MI AUTENTICO SUI NOSTRI SERVER --
// ---------------------------------
	echo "------ INIZIO AUTENTICAZIONE<br>";
	$data = array(
		'client_id' 	=> '9ea827fadb0ae1a0',
		'client_secret' => 'e3e5d84b3b9d7de240594d89768acca46f7ef253',
		'remed' 		=> 1,
		'debug'			=> false		// con true scriviamo i log sul DB delle API
	);
	$postData = json_encode($data);

	$url = 'https://remedcup.braincomputing.com/api/cup/campania/v1/token';
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($postData))
	);
	$response_prenotazioni = curl_exec($curl);
	if(curl_errno($curl)){
		echo 'Errore cURL: ' . curl_error($curl);
	}
	curl_close($curl);	
	
	$response_prenotazioni = json_decode($response_prenotazioni);
	
	if( empty($response_prenotazioni) || empty($response_prenotazioni->access_token) ) {
		die("Utente non autorizzato, token non rilasciato.");
	}
	$access_token = $response_prenotazioni->access_token;
	


	// contatori
	$tot_prenotazioni = $tot_spostamenti = $tot_disdette = 0;

// -------------------------------------
// INIZIO L'IMPORT DELLE PRENOTAZIONI --
// -------------------------------------
	echo "------ AUTENTICATO, INIZIO IMPORT DELLE PRENOTAZIONI<br>";

	$url = 'https://remedcup.braincomputing.com/api/cup/campania/v1/list/prenotazioni';
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $dataCodiciAgende);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: Bearer ' . $access_token
	));
	$response_prenotazioni = curl_exec($curl);

	if(curl_errno($curl)){echo 'Errore cURL: ' . curl_error($curl); }
	curl_close($curl);
	
	$response_prenotazioni = json_decode($response_prenotazioni);
	
	if (empty($response_prenotazioni) || empty($response_prenotazioni->messages[0]->text)) {
		echo("<br>------------------------<br>Non ci sono nuove prenotazioni da importare.<br>------------------------<br>");
	} 
	else 
	{
		$elenco_risultati = $response_prenotazioni->messages[0]->text;

		foreach ($elenco_risultati as $content) 
		{
			$id_prenotazione 	= $content->id_prenotazione;
			$externalDiaryId 	= $content->externalDiaryId;
			$personIdentifier 	= $content->personIdentifier;
			$supplyModeId 		= $content->supplyModeId;
			
			$oldBookingCode 		= $content->oldBookingCode;
			$oldBookingMovingReason = $content->oldBookingMovingReason;
			$bookingCode 			= $content->bookingCode;
			
			$startTime 			= new DateTime($content->startTime);	
			$startTime 			= $startTime->format('Y-m-d H:i:s');

			$endTime 			= new DateTime($content->endTime);
			$endTime 			= $endTime->format('Y-m-d H:i:s');
			
			$customFieldsJson	= json_encode($content->customFields);
			$customFields		= $content->customFields;
			
			// pre-inizializzo questi campi a vuoto
			$cf_IS_IN_FIRST_AVAIL = $cf_IS_PRIVATE = $cf_IS_EXTRACUP = $cf_IS_ONLINE = "";
			
			// ciclo customFields alla ricerca dei campi di sopra da valorizzare. Eventuali altri nuovi rimarranno in 'customFieldsJson'
			foreach($customFields as $cf)
			{
				switch( $cf->fieldId )
				{
					case 'IS_IN_FIRST_AVAIL':		$cf_IS_IN_FIRST_AVAIL 	= $cf->valueNum;		break;
					case 'IS_PRIVATE':				$cf_IS_PRIVATE 			= $cf->valueNum;		break;
					case 'IS_EXTRACUP':				$cf_IS_EXTRACUP 		= $cf->valueNum;		break;
					case 'IS_ONLINE':				$cf_IS_ONLINE 			= $cf->valueNum;		break;
				}
			}

			$created_at			= new DateTime($content->created_at);
			$created_at			= $created_at->format('Y-m-d H:i:s');
			$request 			= json_encode($content->request);		// mi salvo comunque tutta la request per eventuali controlli

		
			// memorizzo la prenotazione
			$stmt_prenotazioni = "INSERT INTO cup_regionale_prenotazioni (id, 				externalDiaryId,  personIdentifier,  supplyModeId,  bookingCode, startTime, 						 endTime, 							customFields, 	   IS_IN_FIRST_AVAIL, 	  IS_PRIVATE, 	  IS_EXTRACUP, 	   IS_ONLINE, 	 created_at, 						   request) 
								  VALUES 								(:id_prenotazione, :externalDiaryId, :personIdentifier, :supplyModeId, :bookingCode, CONVERT(DATETIME, :startTime, 102), CONVERT(DATETIME, :endTime, 102), :customFieldsJson, :cf_IS_IN_FIRST_AVAIL, :cf_IS_PRIVATE, :cf_IS_EXTRACUP, :cf_IS_ONLINE, CONVERT(DATETIME, :created_at, 102), :request)";

			$stmt = $conn->prepare($stmt_prenotazioni);
			$stmt->bindParam(':id_prenotazione', $id_prenotazione);
			$stmt->bindParam(':externalDiaryId', $externalDiaryId);
			$stmt->bindParam(':personIdentifier', $personIdentifier);
			$stmt->bindParam(':supplyModeId', $supplyModeId);
			$stmt->bindParam(':bookingCode', $bookingCode);
			$stmt->bindParam(':startTime', $startTime);
			$stmt->bindParam(':endTime', $endTime);
			$stmt->bindParam(':customFieldsJson', $customFieldsJson);
			$stmt->bindParam(':cf_IS_IN_FIRST_AVAIL', $cf_IS_IN_FIRST_AVAIL);
			$stmt->bindParam(':cf_IS_PRIVATE', $cf_IS_PRIVATE);
			$stmt->bindParam(':cf_IS_EXTRACUP', $cf_IS_EXTRACUP);
			$stmt->bindParam(':cf_IS_ONLINE', $cf_IS_ONLINE);
			$stmt->bindParam(':created_at', $created_at);
			$stmt->bindParam(':request', $request);

			$result_stmt_prenotazioni = $stmt->execute();

			$tot_prenotazioni++;
			echo "$tot_prenotazioni) Prenotazione inserita con ID ".$id_prenotazione."<br>";
			
			
			
			// ciclo tutte le prestazioni associate alla prenotazione di sopra
			$prescriptionNumber = $compilationDate = "";
			
			foreach ($content->details as $row_stmt_prestazione) 
			{
				$id_prestazione		= $row_stmt_prestazione->id_prestazione;
				$id_content_request = $row_stmt_prestazione->id_prenotazione;
				$prescriptionNumber = $row_stmt_prestazione->prescriptionNumber;
				
				if(!empty($row_stmt_prestazione->compilationDate)) {
					$compilationDate 	= new DateTime($row_stmt_prestazione->compilationDate);
					$compilationDate 	= $compilationDate->format('Y-m-d H:i:s');
				} else $compilationDate= NULL;
				
				$admissionCode 		= $row_stmt_prestazione->admissionCode;
				$curCode 			= $row_stmt_prestazione->curCode;
				
				$scheduledDatetime  = new DateTime($row_stmt_prestazione->scheduledDatetime);
				$scheduledDatetime 	= $scheduledDatetime->format('Y-m-d H:i:s');
				
				$exemptionJson		= json_encode($row_stmt_prestazione->exemption);
				if(isset($row_stmt_prestazione->exemption->code))		$exemptionCode = utf8_encode($row_stmt_prestazione->exemption->code);		else 		$exemptionCode = NULL;
				if(isset($row_stmt_prestazione->exemption->name))		$exemptionName = $row_stmt_prestazione->exemption->name;		else 		$exemptionName = NULL;
				
				$doctor 			= json_encode($row_stmt_prestazione->doctor);
				$doctorFiscalCode 	= $row_stmt_prestazione->doctor->fiscalCode ?? NULL;
				$doctorFirstName 	= !empty($row_stmt_prestazione->doctor->firstName) ? utf8_encode($row_stmt_prestazione->doctor->firstName) : NULL;
				$doctorLastName 	= !empty($row_stmt_prestazione->doctor->lastName) ? utf8_encode($row_stmt_prestazione->doctor->lastName) : NULL;
				
				$orderId 			= $row_stmt_prestazione->orderId;
				$waitingTracing 	= $row_stmt_prestazione->waitingTracing;
				$best 				= $row_stmt_prestazione->best;
				
				if(!empty($row_stmt_prestazione->deleted_at)) {
					$deleted_at = new DateTime($row_stmt_prestazione->deleted_at);
					$deleted_at = $deleted_at->format('Y-m-d H:i:s');
				} else {
					$deleted_at = NULL;
				}
				
				// memorizzo la prestazione
				$stmt_prestazioni = "INSERT INTO cup_regionale_prestazioni (  id,			   id_content_request,  prescriptionNumber, compilationDate,   						   admissionCode,  curCode, scheduledDatetime, 							 exemption_code, exemption_name, doctor_fiscal_code, doctor_first_name, doctor_last_name, orderId,  waitingTracing,  best,  deleted_at ) 
									VALUES 									(:id_prestazione, :id_content_request, :prescriptionNumber, CONVERT(DATETIME, :compilationDate, 102), :admissionCode, :curCode, CONVERT(DATETIME, :scheduledDatetime, 102), :exemptionCode,	:exemptionName, :doctorFiscalCode,	:doctorFirstName,  :doctorLastName,	 :orderId, :waitingTracing, :best, :deleted_at)";

				$stmt_prestazioni = $conn->prepare($stmt_prestazioni);
				$stmt_prestazioni->bindParam(':id_prestazione', $id_prestazione);
				$stmt_prestazioni->bindParam(':id_content_request', $id_content_request);
				$stmt_prestazioni->bindParam(':prescriptionNumber', $prescriptionNumber);
				$stmt_prestazioni->bindParam(':compilationDate', $compilationDate);
				$stmt_prestazioni->bindParam(':admissionCode', $admissionCode);
				$stmt_prestazioni->bindParam(':curCode', $curCode);
				$stmt_prestazioni->bindParam(':scheduledDatetime', $scheduledDatetime);
				$stmt_prestazioni->bindParam(':exemptionCode', $exemptionCode);
				$stmt_prestazioni->bindParam(':exemptionName', $exemptionName);
				$stmt_prestazioni->bindParam(':doctorFiscalCode', $doctorFiscalCode);
				$stmt_prestazioni->bindParam(':doctorFirstName', $doctorFirstName);
				$stmt_prestazioni->bindParam(':doctorLastName', $doctorLastName);
				$stmt_prestazioni->bindParam(':orderId', $orderId);
				$stmt_prestazioni->bindParam(':waitingTracing', $waitingTracing);
				$stmt_prestazioni->bindParam(':best', $best);
				$stmt_prestazioni->bindParam(':deleted_at', $deleted_at, PDO::PARAM_NULL); // Assicura che se $deleted_at è vuoto, venga passato NULL al database
				$stmt_prestazioni->execute();

				echo "---> Prestazione inserita con ID ". $id_prestazione ."<br>";
			}
		

			// verifico se esistono altre prenotazioni per questo PrescriptionNumber 
			// (questo perchè altri record potrebbero arrivare anche dopo la presa in carico in TC)
			// se presenti, mi prendo l'operatore che ha eseguito la prima operazione di presa in carico dell'impegnativa
			$stmt_get_dati_tc = "SELECT TOP(1) tc_idimpegnativa FROM cup_regionale_prenotazioni WHERE prescriptionNumber = '$prescriptionNumber' ORDER BY id ASC";
			$result_get_dati_tc = $conn->query($stmt_get_dati_tc)->fetchAll(PDO::FETCH_ASSOC);
			
			if( count($result_get_dati_tc) == 1 && !empty($result_get_dati_tc[0]['tc_idimpegnativa']) ) 
			{
				$tc_idimpegnativa = $result_get_dati_tc[0]['tc_idimpegnativa'];
				$tc_dataacquisizione = date('Y-m-d H:i:s');
				$tc_opeacquisizione = 'script';
				$query_aggiorna_dati_tc = ", tc_idimpegnativa = $tc_idimpegnativa, 
											 tc_dataacquisizione = CONVERT(DATETIME, '$tc_dataacquisizione', 102), 
											 tc_opeacquisizione = '$tc_opeacquisizione'";
				
			} else {
				$tc_idimpegnativa = '-';
				$tc_dataacquisizione = NULL;
				$tc_opeacquisizione = '-';
				$query_aggiorna_dati_tc = "";
			}
			
			
			// aggiorno il record della relativa prenotazione inserendo il numero di ricetta e la data di compilazione della stessa.
			// lo faccio qui perchè i valori sono presenti nell'indice "details" e non negli elementi padre relativi alla prenotazione.
			if( !empty($prescriptionNumber) && !empty($compilationDate) )
			{
				$stmt_upd_dati_prenotazione = "UPDATE cup_regionale_prenotazioni 
												SET 
													prescriptionNumber = '$prescriptionNumber', 
													compilationDate = CONVERT(DATETIME, '$compilationDate', 102) 
													" . $query_aggiorna_dati_tc . "
												WHERE 
													id = $id_content_request";
				$result_stmt_upd_dati_prenotazione = $conn->query($stmt_upd_dati_prenotazione);
			}
		}
	}
	
	echo "FINE: importate $tot_prenotazioni prenotazioni.<br><br>";
// -------------------------------------
// FINE MPORT DELLE PRENOTAZIONI --
// -------------------------------------






// -------------------------------------------------------------------------
// INIZIO L'IMPORT DEGLI SPOSTAMENTI (ovvero date prestazioni modificate) --
// -------------------------------------------------------------------------
	echo "<br><br>------ INIZIO IMPORT DEGLI SPOSTAMENTI<br>";

	$url = 'https://remedcup.braincomputing.com/api/cup/campania/v1/list/spostamenti';
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $dataCodiciAgende);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: Bearer ' . $access_token,
	));
	$response_spostamenti = curl_exec($curl);

	if(curl_errno($curl)){echo 'Errore cURL: ' . curl_error($curl); }
	curl_close($curl);
	
	$response_spostamenti = json_decode($response_spostamenti);
	if (empty($response_spostamenti) || empty($response_spostamenti->messages[0]->text)) {
		echo("<br>------------------------<br>Non ci sono nuovi spostamenti da importare.<br>------------------------<br>");
	}
	else 
	{	
		$elenco_risultati = $response_spostamenti->messages[0]->text;

		foreach ($elenco_risultati as $content) 
		{
			$id_prenotazione = $content->id_prenotazione;		// solo per l'echo in basso
			
			$externalDiaryId 	= $content->externalDiaryId;
			$supplyModeId 		= $content->supplyModeId;
			
			$oldBookingCode 		= $content->oldBookingCode;
			$oldBookingMovingReason = $content->oldBookingMovingReason;
			$bookingCode 			= $content->bookingCode;
			
			$startTime 			= new DateTime($content->startTime);	
			$startTime 			= $startTime->format('Y-m-d H:i:s');

			$endTime 			= new DateTime($content->endTime);
			$endTime 			= $endTime->format('Y-m-d H:i:s');
			
			$customFieldsJson	= json_encode($content->customFields);
			$customFields		= $content->customFields;
			
			$disdettaElenco		= $content->disdettaElenco;
			
			// pre-inizializzo questi campi a vuoto
			$cf_IS_IN_FIRST_AVAIL = $cf_IS_PRIVATE = $cf_IS_EXTRACUP = $cf_IS_ONLINE = "";
			
			// ciclo customFields alla ricerca dei campi di sopra da valorizzare. Eventuali altri nuovi rimarranno in 'customFieldsJson'
			foreach($customFields as $cf)
			{
				switch( $cf->fieldId )
				{
					case 'IS_IN_FIRST_AVAIL':		$cf_IS_IN_FIRST_AVAIL 	= $cf->valueNum;		break;
					case 'IS_PRIVATE':				$cf_IS_PRIVATE 			= $cf->valueNum;		break;
					case 'IS_EXTRACUP':				$cf_IS_EXTRACUP 		= $cf->valueNum;		break;
					case 'IS_ONLINE':				$cf_IS_ONLINE 			= $cf->valueNum;		break;
				}
			}

			$updated_at			= new DateTime($content->created_at);	// la data di creazione del record di spostamento la faccio corrispondere col campo "updated_at" della prenotazione
			$updated_at			= $updated_at->format('Y-m-d H:i:s');
			$request 			= json_encode($content->request);		// mi salvo comunque tutta la request per eventuali controlli


			
			if(!empty($oldBookingCode))		// oldBookingCode non dovrebbe essere mai vuoto, essendo uno spostamento
			{ 
				// cerco la prenotazione interessata allo spostamento e ne aggiorno i bookingCode
				$stmt_get_old_prenotazione = "SELECT id FROM cup_regionale_prenotazioni WHERE bookingCode = '$oldBookingCode'";
				$result_get_old_prenotazione = $conn->query($stmt_get_old_prenotazione)->fetchAll(PDO::FETCH_ASSOC);

				if (count($result_get_old_prenotazione) > 0) 
				{
					$id_prenotazione_modificata = $result_get_old_prenotazione[0]['id'];
			
					$stmt_prenotazioni = "UPDATE cup_regionale_prenotazioni 
											SET
												externalDiaryId 		= :externalDiaryId,
												supplyModeId 			= :supplyModeId,
												oldBookingCode 			= :oldBookingCode,
												oldBookingMovingReason 	= :oldBookingMovingReason,
												bookingCode 			= :bookingCode,
												startTime 				= CONVERT(DATETIME, :startTime, 102),
												endTime 				= CONVERT(DATETIME, :endTime, 102),
												customFields 			= :customFieldsJson,
												IS_IN_FIRST_AVAIL 		= :cf_IS_IN_FIRST_AVAIL,
												IS_PRIVATE 				= :cf_IS_PRIVATE,
												IS_EXTRACUP 			= :cf_IS_EXTRACUP,
												IS_ONLINE 				= :cf_IS_ONLINE,
												updated_at 				= CONVERT(DATETIME, :updated_at, 102),
												request 				= :request
											WHERE
												id = :id_prenotazione_modificata";

					$stmt = $conn->prepare($stmt_prenotazioni);
					$stmt->bindParam(':externalDiaryId', $externalDiaryId);
					$stmt->bindParam(':supplyModeId', $supplyModeId);
					$stmt->bindParam(':oldBookingCode', $oldBookingCode);
					$stmt->bindParam(':oldBookingMovingReason', $oldBookingMovingReason);
					$stmt->bindParam(':bookingCode', $bookingCode);
					$stmt->bindParam(':startTime', $startTime);
					$stmt->bindParam(':endTime', $endTime);
					$stmt->bindParam(':customFieldsJson', $customFieldsJson);
					$stmt->bindParam(':cf_IS_IN_FIRST_AVAIL', $cf_IS_IN_FIRST_AVAIL);
					$stmt->bindParam(':cf_IS_PRIVATE', $cf_IS_PRIVATE);
					$stmt->bindParam(':cf_IS_EXTRACUP', $cf_IS_EXTRACUP);
					$stmt->bindParam(':cf_IS_ONLINE', $cf_IS_ONLINE);
					$stmt->bindParam(':updated_at', $updated_at);
					$stmt->bindParam(':request', $request);
					$stmt->bindParam(':id_prenotazione_modificata', $id_prenotazione_modificata);
					$stmt->execute();

					
					$tot_spostamenti++;
					echo "$tot_spostamenti) Prenotazione modificata con vecchio ID $id_prenotazione_modificata e nuovo ID $id_prenotazione ++++ OLD: $oldBookingCode - NEW: $bookingCode;<br>";
				
					$prescriptionNumber = $compilationDate = "";

					// ciclo le prestazioni con i dati aggiornati 
					foreach ($content->details as $row_stmt_prestazione) 
					{
						//$id_prestazione		= $row_stmt_prestazione->id_prestazione;				
						$prescriptionNumber = $row_stmt_prestazione->prescriptionNumber;
						$curCode 			= $row_stmt_prestazione->curCode;
						$scheduledDatetime  = new DateTime($row_stmt_prestazione->scheduledDatetime);
						$scheduledDatetime 	= $scheduledDatetime->format('Y-m-d H:i:s');

						echo "---> STO PER modificare la prestazione con curCode $curCode e id_content_request $id_prenotazione_modificata<br>";

						/*$stmt_ins_prestazione_modificata = "UPDATE cup_regionale_prestazioni 
																SET
																	id 					= :id_prestazione,*/
						$stmt_ins_prestazione_modificata = "UPDATE cup_regionale_prestazioni 
																SET																	
																	id_content_request 	= :id_prenotazione_modificata,
																	prescriptionNumber 	= :prescriptionNumber,
																	curCode 			= :curCode, 
																	scheduledDatetime 	= CONVERT(DATETIME, :scheduledDatetime, 102)
																WHERE 
																	id_content_request 	= :id_prenotazione_modificata2 
																AND curCode 			= :curCode2";
						
						$stmt_ins_prestazione_modificata = $conn->prepare($stmt_ins_prestazione_modificata);

						//$stmt_ins_prestazione_modificata->bindParam(':id_prestazione', $id_prestazione);
						$stmt_ins_prestazione_modificata->bindParam(':id_prenotazione_modificata', $id_prenotazione_modificata);
						$stmt_ins_prestazione_modificata->bindParam(':prescriptionNumber', $prescriptionNumber);
						$stmt_ins_prestazione_modificata->bindParam(':curCode', $curCode);
						$stmt_ins_prestazione_modificata->bindParam(':scheduledDatetime', $scheduledDatetime);
						$stmt_ins_prestazione_modificata->bindParam(':id_prenotazione_modificata2', $id_prenotazione_modificata);
						$stmt_ins_prestazione_modificata->bindParam(':curCode2', $curCode);
						$stmt_ins_prestazione_modificata->execute();
							

					}
				}
			}
		}
	}
	echo "FINE: importati $tot_spostamenti spostamenti.<br><br>";
// -------------------------------------------------------------------------
// FINE IMPORT DEGLI SPOSTAMENTI (ovvero date prestazioni modificate) --
// -------------------------------------------------------------------------




// ----------------------------------------------------------------------
// INIZIO L'IMPORT DELLE DISDETTE (ovvero date prestazioni cancellate) --
// ----------------------------------------------------------------------
	echo "<br><br>------ INIZIO IMPORT DELLE DISDETTE <br>";

	$url = 'https://remedcup.braincomputing.com/api/cup/campania/v1/list/disdette';
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $dataCodiciAgende);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: Bearer ' . $access_token
	));
	$response_disdette = curl_exec($curl);

	if(curl_errno($curl)){echo 'Errore cURL: ' . curl_error($curl); }
	curl_close($curl);
	
	$response_disdette = json_decode($response_disdette);

	if (empty($response_disdette) || empty($response_disdette->messages[0]->text)) {
		echo("<br>------------------------<br>Non ci sono nuovi spostamenti da importare.<br>------------------------<br>");
	}
	else
	{	
		$elenco_risultati = $response_disdette->messages[0]->text;

		foreach ($elenco_risultati as $content) 
		{
			// leggo direttamente l'array con gli ID delle prestazioni da cancellaree le relative date di cancellazione sul cup regionale
			$disdettaElenco	= $content->disdettaElenco;
			
			if(count($disdettaElenco) > 0)		// essendo delle disdette, questo array generato dalle nostre API non dovrebbe mai essere vuoto
			{
				// ciclo tutte le prestazioni cancellate, cercandole direttamente per ID
				foreach($disdettaElenco AS $prestazione)
				{
					$id_prestazione = $prestazione->id;
					$deleted_at 	= $prestazione->deleted_at;

					$stmt_cancella_prest = "UPDATE  cup_regionale_prestazioni 
											SET 	deleted_at = CONVERT(DATETIME, :deleted_at, 102) 
											WHERE 	id = :id_prestazione";
					$stmt_cancella_prest = $conn->prepare($stmt_cancella_prest);
					$stmt_cancella_prest->bindParam(':deleted_at', $deleted_at);
					$stmt_cancella_prest->bindParam(':id_prestazione', $id_prestazione);
					$stmt_cancella_prest->execute();
					
					$tot_disdette++;
					echo "$tot_disdette) Cancello prestazione con ID $id_prestazione<br>";
				}
				
				
				// dopo l'aggiornamento della disdetta prenotazione, verifico se tutte le prestazioni di questa prenotazione risultano disdette
				$stmt_check_cancellazione_completa = "SELECT pren.id AS id_prenotazione, prest.deleted_at FROM cup_regionale_prestazioni AS prest
														JOIN cup_regionale_prenotazioni AS pren ON pren.id = prest.id_content_request
														WHERE 
															prest.id = :id_prestazione";
				$stmt_check_cancellazione_completa = $conn->prepare($stmt_check_cancellazione_completa);
				$stmt_check_cancellazione_completa->bindParam(':id_prestazione', $id_prestazione);
				$stmt_check_cancellazione_completa->execute();
				$result_check_cancellazione_completa = $stmt_check_cancellazione_completa->fetchAll(PDO::FETCH_ASSOC);

				$prenotazione_disdetta_completamente = true;
				if(count($result_check_cancellazione_completa) > 0)
				{
					$id_prenotazione = $result_check_cancellazione_completa[0]['id_prenotazione'];
					
					foreach($result_check_cancellazione_completa AS $prestazione)
					{
						// se una sola delle prestazioni non è disdetta, setto a false e chiudo termino il ciclo
						if(empty($prestazione['deleted_at'])) {
							$prenotazione_disdetta_completamente = false;
							continue;
						}
					}
				}
				

				// se tutte le prestazioni sono state disdette, disdico anche il record della prenotazione
				if($prenotazione_disdetta_completamente)
				{
					$stmt_cancella_prenotazione = "UPDATE cup_regionale_prenotazioni 
													SET 
														tc_idimpegnativa 	= NULL,
														tc_dataacquisizione = NULL,
														tc_opeacquisizione 	= NULL,
														deleted_at 			= getdate()
													WHERE 
														id = :id_prenotazione";
					$stmt_cancella_prenotazione = $conn->prepare($stmt_cancella_prenotazione);
					$stmt_cancella_prenotazione->bindParam(':id_prenotazione', $id_prenotazione);
					$stmt_cancella_prenotazione->execute();
					
					echo "--> Cancello prenotazione con ID $id_prenotazione<br>";
				}
			}
		}
	}
	echo "FINE: importate $tot_disdette disdette.<br><br>";
// ----------------------------------------------------------------------
// FINE IMPORT DELLE DISDETTE (ovvero date prestazioni cancellate) --
// ----------------------------------------------------------------------	

}
?>
	