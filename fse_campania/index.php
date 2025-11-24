<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

include_once('../include/dbengine.inc.php');
require_once('include/FseCampania.php');

/*
QUESTO FILE SERVE PER EFFETTUARE L'INVIO AL FSE DI TUTTI I MODULI CON:
-- DATA DI INSERIMENTO <= (OGGI - 2 GIORNI)
-- CON ALLEGATO CARICATO E FIRMATO
-- CON VALIDAZIONE EFFETTUATA COME VALIDO

NON LANCIARE MAI MANUALMENTE, VIENE ESEGUITO TRAMITE CRON
*/


$debug = false;		// se passo il param. a true restitusce un array di debug
const STRUTTURA_UTENTE = '201123456'; //150841; //Codice di Linea Medica
const RUOLO_UTENTE = 'APR'; //'DRS';
const CONTESTO_OPERATIVO = 'TREATMENT';
const PRESA_IN_CARICA = 'true';
const TIPO = 'TREATMENT';
const TIPOLOGIA_STRUTTURA = 'Ospedale';
const TIPO_AZIONE_SERVIZIO = array(
	'CREATE' => 'CREATE', 
	'UPDATE' => 'UPDATE', 
	'READ' => 'READ', 
	'DELETE' => 'DELETE');

if(isset($_GET['action']) && (isset($_GET['usephp']) && $_GET['usephp']=='7'))
{	$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));
	require_once($productionConfig);

	switch($_GET['action']) 
	{
		case 'comunicazioneConsensi':
			$fse_campania = new FseCampania('comunicazioneConsensi', $debug);


			$risposta_fse = $fse_campania->comunicazioneConsensi(
				'PROVAX00X00X000Y',
				'LsQiYtf7FcpMYVKvf+51V6t1BSUk+E/dGOB2vmwNl0DhirZ8QzvTI2Ay04p6+t+eH+DjzkJpXrlEEZvKRz6wKVNOt7uYSQUYKBIFcbcEQJnqT7zTgtz7jV3BK+QaEphfKRsOP1Iejv+vKvJ/3te2xNMHPkNYZIAjxEQHftw9Swk=',
				'',	// struttura utente
				'APR',
				'TREATMENT',
				'XTSXST71A01H501T',
				'true',
				'UPDATE',
				'XTSXST71A01H501T',
				'',
				array('Consenso' => 
					array(
						array(
							'CodiceConsenso' => 'C1',
							'ValoreConsenso' => 'true',
							'Data' => '20171110155300', 
							'Note' => ''					
						),
						array(
							'CodiceConsenso' => 'C2',
							'ValoreConsenso' => 'true',
							'Data' => '20171110155300', 
							'Note' => ''					
						),
						array(
							'CodiceConsenso' => 'C3',
							'ValoreConsenso' => 'true',
							'Data' => '20171110155300', 
							'Note' => ''					
						),
					)
				),
				'150^0002'
			);
			
			$esito 		= $risposta_fse['Esito'];
			$cod_esito 	= $risposta_fse['codEsito'];
			$text_esito = $risposta_fse['text_response'];
			$tipo_errore = $risposta_fse['tipoErrore'];

			if($debug) {
				$request_xml 	= $risposta_fse['request_xml'];
				$response_xml 	= $risposta_fse['response_xml'];
				$response 		= $risposta_fse['response'];
			}
		
			if($cod_esito == '0000') {
				echo "$esito - $text_esito";
			} else {
				echo "$esito [$text_esito]<br>$tipo_errore";
			}

		break;
		
		
		
		case 'esitoCaricamentoDocumento':
			$dt_inizio = isset($_GET['dti']) & !empty($_GET['dti']) ? date_format(date_create($_GET['dti']),"YmdHis") : '20221001000000';
			$dt_fine = isset($_GET['dtf']) & !empty($_GET['dtf']) ? (date_format(date_create($_GET['dtf']),"Ymd") . '235959') : (date('Ymd') . '235959');
			
			$fse_campania = new FseCampania('esitoCaricamentoDocumento', $debug);
			$risposta_fse = $fse_campania->esitoCaricamentoDocumento(
				'PROVAX00X00X000Y',
				'',
				STRUTTURA_UTENTE, // '------',	// struttura utente
				RUOLO_UTENTE,
				'',
				$dt_inizio,
				$dt_fine
			);

			$esito 			 = $risposta_fse['Esito'];
			$cod_esito 		 = $risposta_fse['codEsito'];
			$text_esito 	 = $risposta_fse['text_response'];
			$tipo_errore 	 = $risposta_fse['tipoErrore'];
			$lista_documenti = $risposta_fse['listaDocumenti'];

			if($debug) {
				$request_xml 	= $risposta_fse['request_xml'];
				$response_xml 	= $risposta_fse['response_xml'];
				$response 		= $risposta_fse['response'];
			}

			$resp_client = array(
				'result' => array(
								'code' => $cod_esito,
								'message' => $text_esito
							),
				'values' => array()
			);
		
			if($cod_esito == '0000') {
				if(!isset($_GET['from_fe'])){
					echo "$esito - $text_esito<br>";
				}
				
				foreach($lista_documenti as $key=>$documento)
				{
					$identificativo_temporaneo_documento = $documento->IdentificativoTemporaneoDocumento;
					$stato_elaborazione 				 = $documento->StatoElaborazione;
					$identificativo_documento 			 = $documento->IdentificativoDocumento;
			
					if(isset($_GET['from_fe']) && $_GET['from_fe']) {
						$resp_client['values'][] = array(
							'stato_elab' => $stato_elaborazione,
							'id_temp_doc' => $identificativo_temporaneo_documento,
							'id_doc' => $identificativo_documento
						);
					} else {
						echo "$key) $identificativo_temporaneo_documento - $stato_elaborazione ($identificativo_documento)<br>";
					}
				}
				
				if(isset($_GET['from_fe']) && $_GET['from_fe']) {
					echo json_encode($resp_client);
				}
				
			} else {
				if(isset($_GET['from_fe']) && $_GET['from_fe']) {
					echo json_encode($resp_client);
				} else {
					echo "$esito [$text_esito]<br>$tipo_errore";
				}
			}
		break;
		
		
		case 'recuperoDocumento': 
			$fse_campania = new FseCampania('recuperoDocumento', $debug);		
			//var_dump($fse_campania);

			$risposta_fse = $fse_campania->recuperoDocumento(
				'PROVAX00X00X000Y',
				'LsQiYtf7FcpMYVKvf+51V6t1BSUk+E/dGOB2vmwNl0DhirZ8QzvTI2Ay04p6+t+eH+DjzkJpXrlEEZvKRz6wKVNOt7uYSQUYKBIFcbcEQJnqT7zTgtz7jV3BK+QaEphfKRsOP1Iejv+vKvJ/3te2xNMHPkNYZIAjxEQHftw9Swk=',
				'',	// struttura utente
				'APR',
				'TREATMENT',
				'TSTCPN80T31A024I',
				'true',
				'2.16.840.1.113883.2.9.2.150',
				'2.16.840.1.113883.2.9.2.150.4.5.1',
				'2.16.840.1.113883.2.9.2.150.4.4^300571');

			
			$esito 		= $risposta_fse['Esito'];
			$cod_esito 	= $risposta_fse['codEsito'];
			$text_esito = $risposta_fse['text_response'];
			$tipo_errore = $risposta_fse['tipoErrore'];
			
            $info_documento 			= $risposta_fse['infoDocumento']['documento'];
            $tipo_mime 					= $risposta_fse['infoDocumento']['tipoMime'];
            $identificativo_OrgDoc 		= $risposta_fse['infoDocumento']['identificativoOrgDoc'];
            $identificativo_repository 	= $risposta_fse['infoDocumento']['identificativoRepository'];
            $identificativo_documento 	= $risposta_fse['infoDocumento']['identificativoDocumento'];
			
			if($debug) {
                $request_xml 	= $risposta_fse['request_xml'];
                $response_xml 	= $risposta_fse['response_xml'];
                $response 		= $risposta_fse['response'];
			}
			
			if($cod_esito == '0000') 
			{
				if($tipo_mime == "application/pdf")
				{
					$pdf_decoded = base64_decode(base64_encode($info_documento));
					$pdf = fopen("$identificativo_documento.pdf",'w');
					fwrite ($pdf, $pdf_decoded);
					fclose ($pdf);
				}
				elseif($tipo_mime == "text/x-cda-r2+xml")
				{
					$xml_decoded = base64_decode($info_documento);
					$xml = fopen("$identificativo_documento.xml",'w');
					fwrite ($xml, $xml_decoded);
					fclose ($xml);
				}
				else echo "Tipo file informativa diverso da PDF e XML ($tipo_mime_informativa)<br>";
				
			} else {
				echo "$doc --> $esito [$text_esito]<br>$tipo_errore";
			}
		break;
	
	
	
		case 'recuperoInformativa': 
			$fse_campania = new FseCampania('recuperoInformativa', $debug);		
			//var_dump($fse_campania);

			$risposta_fse = $fse_campania->recuperoInformativa(
				'PROVAX00X00X000Y',
				'LsQiYtf7FcpMYVKvf+51V6t1BSUk+E/dGOB2vmwNl0DhirZ8QzvTI2Ay04p6+t+eH+DjzkJpXrlEEZvKRz6wKVNOt7uYSQUYKBIFcbcEQJnqT7zTgtz7jV3BK+QaEphfKRsOP1Iejv+vKvJ/3te2xNMHPkNYZIAjxEQHftw9Swk=',
				'APR',
				'150^0002');
			
			$esito 		= $risposta_fse['Esito'];
			$cod_esito 	= $risposta_fse['codEsito'];
			$text_esito = $risposta_fse['text_response'];
			$tipo_errore = $risposta_fse['tipoErrore'];
			
            $info_informativa 			= $risposta_fse['infoInformativa']['informativa'];	// pdf
            $tipo_mime_informativa 		= $risposta_fse['infoInformativa']['tipoMimeInformativa'];
            $modulistica				= $risposta_fse['infoInformativa']['modulistica'];	// pdf
            $tipo_mime_modulistica 		= $risposta_fse['infoInformativa']['tipoMimeModulistica'];
            $identificativo_informativa = $risposta_fse['infoInformativa']['identificativoInformativa'];
			
			if($debug) {
                $request_xml 	= $risposta_fse['request_xml'];
                $response_xml 	= $risposta_fse['response_xml'];
                $response 		= $risposta_fse['response'];
			}
			
			if($cod_esito == '0000') 
			{
				if($tipo_mime_informativa == "application/pdf")
				{				
					$pdf_decoded = base64_decode(base64_encode($info_informativa));
					$pdf = fopen("$identificativo_informativa.pdf",'w');
					fwrite ($pdf, $pdf_decoded);
					fclose ($pdf);
				}
				elseif($tipo_mime == "text/x-cda-r2+xml")
				{
					$xml_decoded = base64_decode($info_documento);
					$xml = fopen("$identificativo_documento.xml",'w');
					fwrite ($xml, $xml_decoded);
					fclose ($xml);
				}
				else echo "Tipo file informativa diverso da PDF e XML ($tipo_mime_informativa)<br>";
				
				if($tipo_mime_modulistica == "application/pdf")
				{				
					$pdf_decoded = base64_decode(base64_encode($modulistica));
					$pdf = fopen("$identificativo_informativa - modulistica.pdf",'w');
					fwrite ($pdf, $pdf_decoded);
					fclose ($pdf);
				} else {
					echo "Tipo file modulistica non PDF ($tipo_mime_modulistica)";
				}
				
			} else {
				echo "$esito [$text_esito]<br>$tipo_errore";
			}
		break;
		
		
		
		case 'ricercaDocumenti': 
			$fse_campania = new FseCampania('ricercaDocumenti', $debug);
			$risposta_fse = $fse_campania->ricercaDocumenti(
				'',
				'',
				'',
				RUOLO_UTENTE,
				CONTESTO_OPERATIVO,
				$_GET['codice_fiscale'],
				PRESA_IN_CARICA,
				$dt_inizio,
				$dt_fine,
				$_GET['id_final_fse']
			);

			$esito 		 = $risposta_fse['Esito'];
			$cod_esito 	 = $risposta_fse['codEsito'];
			$text_esito  = $risposta_fse['text_response'];
			$tipo_errore = $risposta_fse['tipoErrore'];
			$metadato	 = $risposta_fse['metadato'];

			if($debug) {
                $request_xml 	= $risposta_fse['request_xml'];
                $response_xml 	= $risposta_fse['response_xml'];
                $response 		= $risposta_fse['response'];
			}			
			
			if($cod_esito == '0000') 
			{
				if(isset($_GET['from_fe']) && $_GET['from_fe']) {					
					echo json_encode(array(
							"esito" => "$esito",
							"text_esito" => "$text_esito",
							'LID' => $metadato->LID
						));
				} else {
					$IdentificativoOrgDoc 			= $metadato->IdentificativoOrgDoc;
					$IdentificativoRepository 		= $metadato->IdentificativoRepository;
					$IdentificativoDocumento 		= $metadato->IdentificativoDocumento;
					$VersioneOggettoDocumento 		= $metadato->VersioneOggettoDocumento;
					$IdentificativoUnivocoDocumento = $metadato->IdentificativoUnivocoDocumento;
					$DataValidazioneDocumento 		= $metadato->DataValidazioneDocumento;
					$IstituzioneAutore 				= $metadato->IstituzioneAutore;
					$DataInizioPrestazione 			= $metadato->DataInizioPrestazione;
					$DataFinePrestazione 			= $metadato->DataFinePrestazione;
					$LivelloConfidenzialita 		= $metadato->LivelloConfidenzialita;
					$LID 							= $metadato->LID;				// $IdentificativoDocumento per 'cancellazioneMetadati'	
				
					echo "IdentificativoOrgDoc : $IdentificativoOrgDoc<br>
						IdentificativoRepository : $IdentificativoRepository<br>
						IdentificativoDocumento : $IdentificativoDocumento<br>
						VersioneOggettoDocumento : $VersioneOggettoDocumento<br>
						IdentificativoUnivocoDocumento : $IdentificativoUnivocoDocumento<br>
						DataValidazioneDocumento : $DataValidazioneDocumento<br>
						IstituzioneAutore : $IstituzioneAutore<br>
						DataInizioPrestazione : $DataInizioPrestazione<br>
						DataFinePrestazione : $DataFinePrestazione<br>
						LivelloConfidenzialita : $LivelloConfidenzialita";
						
					if($LivelloConfidenzialita == "N") {
						echo " (non offuscato)";
					} elseif($LivelloConfidenzialita == "V") {
						echo " (offuscato)";
					}
					echo "<br>LID: $LID<br>";
				}

			} else {
				if(isset($_GET['from_fe']) && $_GET['from_fe']) {
					echo json_encode(array(
							"esito" => "$esito",
							"text_esito" => "$tipo_errore"
						));
				} else {
					echo "$esito [$text_esito]<br>$tipo_errore";	
				}
			}
		break;
	
	
	
		case 'statoConsensi':
			$fse_campania = new FseCampania('statoConsensi', $debug);

			$risposta_fse = $fse_campania->statoConsensi(
				'PROVAX00X00X000Y',
				'LsQiYtf7FcpMYVKvf+51V6t1BSUk+E/dGOB2vmwNl0DhirZ8QzvTI2Ay04p6+t+eH+DjzkJpXrlEEZvKRz6wKVNOt7uYSQUYKBIFcbcEQJnqT7zTgtz7jV3BK+QaEphfKRsOP1Iejv+vKvJ/3te2xNMHPkNYZIAjxEQHftw9Swk=',
				'',
				'APR',
				'',
				'',
				'true',
				'XTSXST71A01H501T');
			
			$esito 		= $risposta_fse['Esito'];
			$cod_esito 	= $risposta_fse['codEsito'];
			$text_esito = $risposta_fse['text_response'];
			$tipo_errore = $risposta_fse['tipoErrore'];
			$identificativo_assistito_consenso 		= $risposta_fse['identificativoAssistitoConsenso'];
			$lista_consensi 						= $risposta_fse['listaConsensi'];
			$identificativo_informativa_consensi 	= $risposta_fse['identificativoInformativaConsensi'];
			$identificativo_informativa_corrente 	= $risposta_fse['identificativoInformativaCorrente'];
			
			if($debug) {
				$request_xml 	= $risposta_fse['request_xml'];
				$response_xml 	= $risposta_fse['response_xml'];
				$response 		= $risposta_fse['response'];
			}
		
			if($cod_esito == '0000') {
				foreach($lista_consensi as $key=>$consenso)
				{
					$codice_consenso = $consenso->CodiceConsenso;
					$valore_consenso = $consenso->ValoreConsenso;
					
						if($valore_consenso == true) $valore_consenso = 'Accettato';
					elseif($valore_consenso == false) $valore_consenso = 'Non accettato';
					
					$data_consenso = $consenso->Data;
					echo "$key) $codice_consenso - $valore_consenso ($data_consenso)<br>";
				}
			} else {
				echo "$esito [$text_esito]<br>$tipo_errore";
			}

		break;

		case 'cancellazioneMetadati':
			$fse_campania = new FseCampania('cancellazioneMetadati', $debug);		
			$risposta_fse = $fse_campania->cancellazioneMetadati(
				'', //IdentificativoUtente
				'', //pinCode
				STRUTTURA_UTENTE,
				'DRS',
				'SYSADMIN',
				$_GET['codice_fiscale'],
				$_GET['id_documento']
			);
			
			$esito 		 = $risposta_fse['Esito'];
			$cod_esito 	 = $risposta_fse['codEsito'];
			$text_esito  = $risposta_fse['text_response'];
			$tipo_errore = $risposta_fse['tipoErrore'];
		
			
			if($debug) {
                $request_xml 	= $risposta_fse['request_xml'];
                $response_xml 	= $risposta_fse['response_xml'];
                $response 		= $risposta_fse['response'];
			}

			

			if($cod_esito == '0000') 
			{
				if(isset($_GET['from_fe']) && $_GET['from_fe']) {					
					echo json_encode(array(
							"esito" => "$esito",
							"text_esito" => "$text_esito"
						));
				} else {
					echo "$esito - $text_esito<br>";
				}
			} else {
				if(isset($_GET['from_fe']) && $_GET['from_fe']) {
					echo json_encode(array(
							"esito" => "$esito",
							"text_esito" => "$tipo_errore"
						));
				} else {
					echo "$esito [$text_esito]<br>$tipo_errore";
				}				
			}
		break;
		
		case 'cancellazioneMetadatiCron':
			$moduli_da_inviare = array();
			$query = "SELECT * FROM re_moduli_da_cancellare_fse";
			$result = $conn->query($query)->fetchAll();

			$fse_campania = new FseCampania('cancellazioneMetadati', $debug);
			$id_ds = 11;
			foreach ($result as $key => $row) {
				$codice_fiscale_paziente = $row['codice_fiscale_paziente'];
				$id_paziente = $row['id_paziente'];
				$id_modulo_padre = $row['id_modulo_padre'];
				$id_modulo_versione = $row['id_modulo_versione'];
				$id_istanza_testata = $row['id_istanza_testata'];
				$offuscamento_fse = $row['offuscamento_fse'];
				$nome_modulo = $row['nome_modulo'];
				$opeins_modulo = $row['opeins_modulo'];
				$tipo_azione_servizio = TIPO_AZIONE_SERVIZIO['DELETE'];
				$id_temp_fse = $row['id_temp_fse'];
				$id_final_fse = $row['id_final_fse'];

				$risposta_fse = $fse_campania->cancellazioneMetadati(
					'', //IdentificativoUtente
					'', //pinCode
					STRUTTURA_UTENTE,
					'DRS',
					'SYSADMIN',
					$codice_fiscale_paziente,
					$id_final_fse
				);

				$esito = !empty($risposta_fse['Esito']) ? ("'" . $risposta_fse['Esito'] . "'") : 'NULL';
				$cod_esito = !empty($risposta_fse['codEsito']) ? $risposta_fse['codEsito'] : 'NULL';
				$text_esito = !empty($risposta_fse['text_response']) ? ("'" . $risposta_fse['text_response'] . "'") : 'NULL';
				$tipo_errore = !empty($risposta_fse['tipoErrore']) ? ("'" . $risposta_fse['tipoErrore'] . "'") : 'NULL';

				if ($debug) {
					$request_xml 	= $risposta_fse['request_xml'];
					$response_xml 	= $risposta_fse['response_xml'];
					$response 		= $risposta_fse['response'];
				}
				
				$esito = 'NULL';
				$cod_esito = 'NULL';
				$text_esito = 'NULL';
				$tipo_errore = 'NULL';
				
				if ($cod_esito == '0000') {
					$stato_esecuzione = 'OK';
					$cancellato = 's';
					echo "$esito - $text_esito<br>ID DOC. TEMP.: $identificativo_documento<br>";
					
					$query = "UPDATE istanze_testata
							SET inviato_fse = 'n'
							WHERE id_istanza_testata = $id_istanza_testata";
					$result = $conn->query($query);
				} else {
					$stato_esecuzione = 'KO';
					$cancellato = 'n';
					echo "$esito [$text_esito]<br>$tipo_errore<br>";
				}
				
				$query = "INSERT INTO repository_fse(id_paziente, codice_fiscale_paziente, nome_modulo, id_modulo_padre, id_modulo_versione, 
									id_istanza_testata, offuscamento, azione, id_temp_fse, id_final_fse,
									data_esecuzione, id_ope_esecuzione, opeins_modulo, stato_esecuzione, codice_esito, 
									testo_esito, tipo_errore, cancellato)
							VALUES ($id_paziente, '$codice_fiscale_paziente', '$nome_modulo', $id_modulo_padre, $id_modulo_versione, 
									$id_istanza_testata, '$offuscamento_fse', '$tipo_azione_servizio', '$id_temp_fse', '$id_final_fse',
									GETDATE(), $id_ds, $opeins_modulo, '$stato_esecuzione', '$cod_esito',
									$text_esito, $tipo_errore, '$cancellato')";
				$result = $conn->query($query);
			}
			break;
		
		case 'comunicazioneMetadati':
			$moduli_da_inviare = array();
			$query = "SELECT TOP(1) * 
						FROM re_moduli_da_inviare_fse_48h";
			$result = $conn->query($query)->fetchAll();
			
			$fse_campania = new FseCampania('comunicazioneMetadati', $debug);
			$id_ds = 11;
			
			foreach ($result as $key=>$row) 
			{
				$codice_fiscale = $row['CodiceFiscale']; // 'XTSXST71A01H501T' assegnazione da rimuovere in modo da prendere quello estratto
				$id_paziente = $row['id_paziente'];
				$id_modulo_padre = $row['id_modulo_padre'];
				$id_modulo_versione = $row['id_modulo_versione'];
				$id_istanza_testata = $row['id_istanza_testata'];
				$offuscamento_fse = $row['offuscamento_fse'];
				$nome_modulo = $row['nome_modulo'];
				$opeins_modulo = $row['opeins'];
				$tipo_azione_servizio = TIPO_AZIONE_SERVIZIO['CREATE'];
				$dataora_creazione = substr($row['data_creazione'], 0 ,10) . ' ' . $row['ora_creazione'];
				$dataora_creazione = date_format(date_create($dataora_creazione),"YmdHis");
				$dataora_creazione = substr($row['data_creazione'], 0 ,10) . ' ' . $row['ora_creazione'];
				$dataora_creazione = date_format(date_create($dataora_creazione),"YmdHis");
					
				$risposta_fse = $fse_campania->comunicazioneMetadati(
					'', //CAMPI RECUPERATI DALLA FUNZIONE comunicazioneMetadati
					'', //CAMPI RECUPERATI DALLA FUNZIONE comunicazioneMetadati
					STRUTTURA_UTENTE,
					RUOLO_UTENTE,
					CONTESTO_OPERATIVO,
					'11502-2',
					$codice_fiscale,
					PRESA_IN_CARICA,
					$tipo_azione_servizio,
					TIPOLOGIA_STRUTTURA,
					'application/pdf',
					($row['offuscamento_fse'] == 'n' ? 'N' : 'V'), // N (documento non offuscato) oppure V (documento offuscato per privacy)
					'REF',
					'11502-2',
					'PDF',
					'',
					date_format(date_create($row['data_validazione']),"YmdHis"),
					'APR',
					'150201',
					'PROVAX00X00X000Y',
					'',
					'AD_PSC100',
					'CON',
					$dataora_creazione,
					$dataora_creazione,
					'',
					'',
					'cda.pdf',
					'C:/web/Republic/fse_campania/cda.pdf'				
				);
				
				$esito = !empty($risposta_fse['Esito']) ? ("'".$risposta_fse['Esito']."'") : 'NULL';
				$cod_esito = !empty($risposta_fse['codEsito']) ? ("'".$risposta_fse['codEsito']."'") : 'NULL';
				$text_esito = !empty($risposta_fse['text_response']) ? ("'".$risposta_fse['text_response']."'") : 'NULL';
				$tipo_errore = !empty($risposta_fse['tipoErrore']) ? ("'".$risposta_fse['tipoErrore']."'") : 'NULL';
				$identificativo_documento = !empty($risposta_fse['identificativoDocumento']) ? ("'".$risposta_fse['identificativoDocumento']."'") : 'NULL';
				
				if($debug) {
					$request_xml 	= $risposta_fse['request_xml'];
					$response_xml 	= $risposta_fse['response_xml'];
					$response 		= $risposta_fse['response'];
					
					echo "Request_xml : $request_xml<br>";
					echo "Response_xml : $response_xml<br>";
					echo "Response : $response <br>";
				}
				
				if($cod_esito == '0000' || $cod_esito == '0001') {
					$stato_esecuzione = 'OK';
					echo "$esito - $text_esito<br>ID DOC. TEMP.: $identificativo_documento<br>";
				} else {
					$stato_esecuzione = 'KO';
					echo "$esito [$text_esito]<br>$tipo_errore<br>";
				}
				
				$query = "INSERT INTO repository_fse(id_paziente, codice_fiscale_paziente, nome_modulo, id_modulo_padre, id_modulo_versione, 
									id_istanza_testata, offuscamento, azione, id_temp_fse, id_final_fse,
									data_esecuzione, id_ope_esecuzione, opeins_modulo, stato_esecuzione, codice_esito, 
									testo_esito, tipo_errore)
							VALUES ($id_paziente, '$codice_fiscale', '$nome_modulo', $id_modulo_padre, $id_modulo_versione, 
									$id_istanza_testata, '$offuscamento_fse', '$tipo_azione_servizio', $identificativo_documento, NULL,
									GETDATE(), $id_ds, $opeins_modulo, '$stato_esecuzione', $cod_esito,
									$text_esito, $tipo_errore)";
				$result = $conn->query($query);
			}
			break;

        case 'invokeValidationGateway':
            require_once __DIR__ . '/include/Cda.php';
            $cda = new Cda($conn);
            $query = "SELECT TOP(1) * FROM re_moduli_da_inviare_fse_48h";
            $modulesToSend = $conn->query($query)->fetchAll();

            foreach ($modulesToSend as $moduleToSend) {
                $fse = new FseCampania($_GET['action'], $_GET['debug']);
                $response = $fse->invokeValidationGateway(
                    $cda->getModuleData($moduleToSend['id_istanza_testata']),
                    __DIR__ . '/cda.pdf',
                    __DIR__ . '/jwt-generator',
                    'ciupido'
                );
            }

            break;
	}		
} else echo "no var";