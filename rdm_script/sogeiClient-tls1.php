<?php
require('../include/reserved/config.rdm.php');

//DA CAMBIARE (TOGLIERE .rdm)
include_once('../include/dbengine.inc.php');

/**
* Class which implements comunication to the Sogei Service for the purpose of using the Ricetta Dematerializzata's functionalities.
*/
class SogeiClient {

	//private $logger;



	/* Set of SoapClients objects used to manage the connections to the different endpoints.
	* Every client is dedicated to a different endpoint/operation.*/
	public $client_visualizzaErogato;

	/* Oggetto necessario per l'invio delle comunicazioni di chiusura delle ricette*/
	public $client_invioErogato;

	/* Public key used for the encryptions and decriptions of some soap fields.*/
	private $publicKey;



	/* Codice PIN cifrato.*/
	private $pinCode_cifrato;


	public function SogeiClient(){

		//$this->logger= new Katzgrau\KLogger\Logger(LOGGING_DIR);

		//$this->logger->info('SogeiClient: Invoked Constructor');

		/*
		*  Opening and retrieving of the public sanitel_cert key
		*/
		$keyFile = fopen(SANITEL_CERT, "r");
		$this->publicKey = fread($keyFile, 8192);
		fclose($keyFile);
		openssl_get_publickey($this->publicKey);

		/* Inizializing the conxtex of the soap call in order to use ssl and verify the server's certificate
		* through the use of the cafile. which is the certification authority certificate file*/
		//$context = stream_context_create(array('ssl' => array('verify_peer' => true, 'cafile' => CA_CERT_FILE)));

		// inizializing cyphered pincode
		if (PINCODE <> '') {
			//$pinCode_cifrato=$pinCode;
			$this->pinCode_cifrato = $this -> cifraSanitel(PINCODE);
		}

		/* Inizializing the soap client objects*/
		$this -> client_visualizzaErogato = new SoapClient(VISUALIZZA_EROGATO_WSDL,
		array(
			'login' => LOGIN,
			'password' => PASSWORD,
			'location' => VISUALIZZA_EROGATO_ENDPOINT,
                        'exceptions' => true,
			'authentication' => SOAP_AUTHENTICATION_BASIC,
			"stream_context" => stream_context_create(
                        array(
                                    'ssl' => array(
                                    'verify_peer'       => false,
                                    'verify_peer_name'  => false,
                                    'cafile' => CA_CERT_FILE
                                )
                            )
                        )  
		));

		$this -> client_invioErogato = new SoapClient(INVIO_EROGATO_WSDL,
		array(
			'login' => LOGIN,
			'password' => PASSWORD,
			'location' => INVIO_EROGATO_ENDPOINT,
                        'exceptions' => true,
			'authentication' => SOAP_AUTHENTICATION_BASIC,
                        'trace' => true,
			"stream_context" => stream_context_create(
                        array(
                                    'ssl' => array(
                                    'verify_peer'       => false,
                                    'verify_peer_name'  => false,
                                    'cafile' => CA_CERT_FILE
                                )
                            )
                        )  
                    ));
        }
        
        
	/**
	* Visualizzazione del contenuto della ricetta dematerializzata prescritta
	* con contestuale presa in carico in maniera esclusiva della stessa,
	*
	* @param	nre	Numero Ricetta Elettronica
	* @param	cfAssistito	Codice fiscale dell’assistito.
	* 			Se Elemento l’assistito e' provvisto di Tessera facoltativo Sanitaria l’elemento è OBBLIGATORIO per la ricerca
	*			della prescrizione. Se l’assistito è un soggetto privo di Tessera Sanitaria (ad es. straniero europeo o extraeuropeo) il
	* 			campo deve essere lasciato vuoto in quanto il soggetto non possiede un codice fiscale.
	*
	*/
	public function visualizzaErogato($nre,$cfAssistito, $idImp, $giaincarica, $debug = 0) {
                
		//$this->logger->info('visualizzaErogato: Invoked Method');
		//$this->logger->debug('visualizzaErogato, nre:'.$nre);
		//$this->logger->debug('visualizzaErogato, cf:'.$cfAssistito );

		if (isset($cfAssistito)) {
			$cfAssistito_cifrato = $this -> cifraSanitel($cfAssistito);
		}


        echo"<pre>";
		$VisualizzaErogatoRichiesta = array(
			'pinCode' => $this->pinCode_cifrato,
			'codiceRegioneErogatore' => CODICE_REGIONE_EROGATORE,
			'codiceAslErogatore' => CODICE_ASL_EROGATORE,
			'codiceSsaErogatore' => CODICE_SSA_EROGATORE ,
			'pwd' => PWD,
			'nre' => $nre,
			'cfAssistito' => $cfAssistito_cifrato,
			'tipoOperazione' => OP_PRENDI_IN_CARICO
		);

		$ret = $this -> client_visualizzaErogato ->visualizzaErogato($VisualizzaErogatoRichiesta);
		//decommentare per vedere l'errore
		
		if($debug == -1)
			print_r($ret);	
		$this -> parseVisualizzaResponse($idImp, $ret, $giaincarica, $debug);
	}
	
	
	public function rilasciaErogato($nre,$cfAssistito, $idImp) 
	{
                
		//$this->logger->info('visualizzaErogato: Invoked Method');
		//$this->logger->debug('visualizzaErogato, nre:'.$nre);
		//$this->logger->debug('visualizzaErogato, cf:'.$cfAssistito );

		if (isset($cfAssistito)) {
			$cfAssistito_cifrato = $this -> cifraSanitel($cfAssistito);
		}


        //echo"<pre>";
		$VisualizzaErogatoRichiesta = array(
			'pinCode' => $this->pinCode_cifrato,
			'codiceRegioneErogatore' => CODICE_REGIONE_EROGATORE,
			'codiceAslErogatore' => CODICE_ASL_EROGATORE,
			'codiceSsaErogatore' => CODICE_SSA_EROGATORE ,
			'pwd' => PWD,
			'nre' => $nre,
			'cfAssistito' => $cfAssistito_cifrato,
			'tipoOperazione' => OP_RILASCIA
		);

		$ret = $this -> client_visualizzaErogato ->visualizzaErogato($VisualizzaErogatoRichiesta);
		
		//print_r($ret);
		$this -> parseRilasciaResponse($idImp, $ret);		
	}
	
	/*public function visualizzaDatiAssistito($nre,$cfAssistito, $idImp) 
	{
		//$this->logger->info('visualizzaErogato: Invoked Method');
		//$this->logger->debug('visualizzaErogato, nre:'.$nre);
		//$this->logger->debug('visualizzaErogato, cf:'.$cfAssistito );

		if (isset($cfAssistito)) {
			$cfAssistito_cifrato = $this -> cifraSanitel($cfAssistito);
		}


        echo"<pre>";
		$VisualizzaErogatoRichiesta = array(
			'pinCode' => $this->pinCode_cifrato,
			'codiceRegioneErogatore' => CODICE_REGIONE_EROGATORE,
			'codiceAslErogatore' => CODICE_ASL_EROGATORE,
			'codiceSsaErogatore' => CODICE_SSA_EROGATORE ,
			'pwd' => PWD,
			'nre' => $nre,
			'cfAssistito' => $cfAssistito_cifrato,
			'tipoOperazione' => OP_VISUALIZZA
		);

		$ret = $this -> client_visualizzaErogato ->visualizzaErogato($VisualizzaErogatoRichiesta);
		print_r($ret);	
		$this -> parseVisualizzaDatiResponse($idImp, $ret);
	}
	*/
   public function parseVisualizzaResponse($idImp, $ret, $giaincarica, $debug)
    {
		$codEsitoVisualizzazione = $ret->codEsitoVisualizzazione;
		if($codEsitoVisualizzazione == VISUAL_OK)
		{
			$nre =  $ret->nre;
			$medico_daRicavare = explode(';',$ret->testata1);
			$cognome_medico_daRicavare = explode("=", $medico_daRicavare[0]);
			$cognome_medico = $cognome_medico_daRicavare[1];
			$nome_medico_daRicavare = explode("=", $medico_daRicavare[1]);
			$nome_medico = $nome_medico_daRicavare[1];
			$cf_medico = $ret->cfMedico1;
			$anagraficaAss = $ret->cognNome;
			$codRegione = $ret->codRegione;
			$codASLAo = $ret->codASLAo;
			$codiceAss = $ret ->codiceAss;
			$indirizzo = $ret->indirizzo;
			$codEsenzione = $ret->codEsenzione;
			$codDiagnosi = $ret->codDiagnosi;
			$statoProcesso = $ret->statoProcesso;
			$dispReg = $ret->dispReg;
			$codSpecializzazione = $ret->codSpecializzazione;
			$descrizioneDiagnosi = $ret->descrizioneDiagnosi; //FACOLTATIVO DA DOCUMENTAZIONE
			$tipoPrescrizione = $ret->tipoPrescrizione;
			$dataCompilazione = $ret->dataCompilazione; //FACOLTATIVO DA DOCUMENTAZIONE
			//$tipoVisita = $ret->tipoVisita; //FACOLTATIVO DA DOCUMENTAZIONE
			//$provAssistito = $ret->provAssistito;
			$ticket = $ret->ticket;
			//$aslAssistito = $ret->aslAssistito; //FACOLTATIVO DA DOCUMENTAZIONE
			$quotaFissa = $ret->quotaFissa;
			$franchigia = $ret->franchigia;
			$galDirChiamAltro = $ret->galDirChiamAltro;
			$list = $ret->ElencoDettagliPrescrVisualErogato;
			
			// Stefano
			try { 
			$conn = db_connect();
		
		    $result = mssql_query("SELECT * FROM esenzione_codici WHERE CodiceEsenzione = '$codEsenzione'", $conn);
			
			$codiciTrovati = mssql_num_rows($result);
			
			$nonEsenzione = 1;
			
			if($codiciTrovati > 0) {
				$codiceTrovatoRow = mssql_fetch_assoc($result);
				
				switch($codiceTrovatoRow['ticket'])
				{
					case 0:
					$nonEsenzione = 2;	
					break;
					default:
					
					$nonEsenzione = 1;	
				    break;
			    }
			}
			} catch(Exception $ex){
				die($ex->getMessage());
			}
			//print_r ($nonEsenzione);
			/*
			if($codEsenzione == null)
			{
				$nonEsenzione = 1;	
			} else {
				$nonEsenzione = 2;	
			}
		*/
			// end Stefano
			
			
				
	        $ElencoDettagliPrescrVisualErogato = array();
			/*foreach($list as $key => $value)
			{
				$DettaglioPrescrizioneVisualErogato = array();
				$details = $list -> $key;
				foreach($details as $key => $value)
				{
					$DettaglioPrescrizioneVisualErogato[$key] = $value;
				}
				array_push($ElencoDettagliPrescrVisualErogato, $DettaglioPrescrizioneVisualErogato);
            }*/
			foreach($list as $key => $value)
			{
				$DettaglioPrescrizioneVisualErogato = array();
				$details = $list -> $key;
				
				$nimp = 1;
				if(is_array($details)){
					$nimp = 2;
				}
				
				foreach($details as $key => $value)
				{
					$DettaglioPrescrizioneVisualErogato[$key] = $value;
				}
				
				if($nimp==2)
					$ElencoDettagliPrescrVisualErogato = $DettaglioPrescrizioneVisualErogato;
				else
					array_push($ElencoDettagliPrescrVisualErogato, $DettaglioPrescrizioneVisualErogato);
				
            }
			$codAutenticazioneMedico = $ret->codAutenticazioneMedico;
			$codAutenticazioneErogatore = $ret->codAutenticazioneErogatore;
			/*//Ottengo dagli array di ogni prescrizione le informazioni della singola prescrizione e le inserisco in array appositi
			$codiciCatalogo = array();
			$quantitaCatalogo = array();
			foreach($ElencoDettagliPrescrVisualErogato as $key => $value)
			{
				if(isset($value['codCatalogoPrescr']))
					array_push($codiciCatalogo, $value['codCatalogoPrescr']);
				if(isset($value['quantita']))
					array_push($quantitaCatalogo,$value['quantita']);
			}*/
			
			//Ottengo dagli array di ogni prescrizione le informazioni della singola prescrizione e le inserisco in array appositi
						
			$codiciCatalogo = array();
			$quantitaCatalogo = array();
			
			foreach($ElencoDettagliPrescrVisualErogato as $key => $value)
			{
				if(is_object($value)){
					if(isset($value->codCatalogoPrescr))
						array_push($codiciCatalogo, $value->codCatalogoPrescr);
					if(isset($value->quantita))
						array_push($quantitaCatalogo,$value->quantita);
				}
				
				else{
					if(isset($value['codCatalogoPrescr']))
						array_push($codiciCatalogo, $value['codCatalogoPrescr']);
					if(isset($value['quantita']))
						array_push($quantitaCatalogo,$value['quantita']);
				}
			}
			
			if(isset($dataCompilazione))
			{
				$date = new DateTime($dataCompilazione);
				$dataCompilazione = $date->format('d-m-Y H:i:s');
			}
			//Se è già in carica lo devo solo visualizzare
			if($giaincarica == 1)
			{
			
				$this -> mostraPresaInCaricaOk($giaincarica, $idImp, $nre, $cf_medico, $codRegione, $codASLAo, 
					$codSpecializzazione, $cognome_medico, $nome_medico, $anagraficaAss, $indirizzo,
					$tipoPrescrizione, $nonEsenzione, $codDiagnosi, $statoProcesso, $descrizioneDiagnosi, 
					$dataCompilazione, $aslAssistito, $codiciCatalogo, $quantitaCatalogo, $codEsenzione, $dispReg);			
			}
			//Altrimenti aggiorno il record con le informazioni ricavate
			else
			{
				$this -> mostraPresaInCaricaOk($giaincarica, $idImp, $nre, $cf_medico, $codRegione, $codASLAo, 
					$codSpecializzazione, $cognome_medico, $nome_medico, $anagraficaAss, $indirizzo,
					$tipoPrescrizione, $nonEsenzione, $codDiagnosi, $statoProcesso, $descrizioneDiagnosi, 
					$dataCompilazione, $aslAssistito, $codiciCatalogo, $quantitaCatalogo, $codEsenzione, $dispReg);
				$this -> updateDB($idImp, $nre, $cf_medico, $codRegione, $codASLAo, 
					$codSpecializzazione, $cognome_medico, $nome_medico, $anagraficaAss, $indirizzo,
					$tipoPrescrizione, $nonEsenzione, $codDiagnosi, $descrizioneDiagnosi, 
					$dataCompilazione, $tipoVisita, $provAssistito, $aslAssistito, $statoProcesso, $ticket, $quotaFissa,
					$franchigia, $galDirChiamAltro, $codAutenticazioneMedico, $codAutenticazioneErogatore, $codEsitoVisualizzazione, $codiceAss, $codiciCatalogo, $quantitaCatalogo, $codEsenzione,$dispReg, $debug);
			}
		}
		else if($codEsitoVisualizzazione == VISUAL_ERROR)
		{
			$list = $ret->ElencoErroriRicette;
			$ElencoErroriRicette = array();
			foreach($list as $key => $value)
			{
				$ErroreRicetta = array();
				$details = $list -> $key;
				foreach($details as $key => $value)
				{
					$ErroreRicetta[$key] = $value;
				}
				array_push($ElencoErroriRicette, $ErroreRicetta);
            }
			//print_r($ElencoErroriRicette);
			//echo count($ElencoErroriRicette);
			
			echo "<div style=\"color:#CC0000; font-size: 150%; font-weight: bold;\">ATTENZIONE</div>";
			echo "<div style=\"color:#CC0000; font-size: 140%;\">L'OPERAZIONE DI PRESA IN CARICA È TERMINATA\nCON I SEGUENTI ERRORI:</div>\n";
			for($c = 0; $c < count($ElencoErroriRicette); $c++)
			{
				if(count($ElencoErroriRicette) > 1)
				{
					for($i = 0; $i < count($ElencoErroriRicette[$c]); $i++)
					{
						$object = $ElencoErroriRicette[$c][$i];
						if(($object -> codEsito) == IN_EROGAZIONE_DA_ALTRO_CENTRO)
							$this -> mostraPresaInCaricaError(IN_EROGAZIONE_DA_ALTRO_CENTRO);
						else if(($object -> codEsito) == RICETTA_NON_ESISTENTE)
							$this -> mostraPresaInCaricaError(RICETTA_NON_ESISTENTE);
						
						else if(($object -> codEsito) == RICETTA_SCADUTA)
							$this -> mostraPresaInCaricaError(RICETTA_SCADUTA);
						
						else if(($object -> codEsito) == NRE_REG_CODE_ERR)
							$this -> mostraPresaInCaricaError(NRE_REG_CODE_ERR);
						
						else if(($object -> codEsito) == NRE_RAGGRUPPAMENTO_ERR)
							$this -> mostraPresaInCaricaError(NRE_RAGGRUPPAMENTO_ERR);
						
						else if(($object -> codEsito) == NRE_PROGRESSIVO_ERR)
							$this -> mostraPresaInCaricaError(NRE_PROGRESSIVO_ERR);
						
						else if(($object -> codEsito) == CF_NON_CORRISPONDENTE)
							$this -> mostraPresaInCaricaError(CF_NON_CORRISPONDENTE);					
						
						else if(($object -> codEsito) == RICETTA_ANNULLATA_DAL_MEDICO)
							$this -> mostraPresaInCaricaError(RICETTA_ANNULLATA_DAL_MEDICO);
						
						
					}
				}
				else 
				{
					$object = $ElencoErroriRicette[$c];
					if(($object['codEsito']) == IN_EROGAZIONE_DA_ALTRO_CENTRO)
						$this -> mostraPresaInCaricaError(IN_EROGAZIONE_DA_ALTRO_CENTRO);
		
					else if(($object['codEsito']) == RICETTA_NON_ESISTENTE)
						$this -> mostraPresaInCaricaError(RICETTA_NON_ESISTENTE);
					
					else if(($object['codEsito']) == RICETTA_SCADUTA)
						$this -> mostraPresaInCaricaError(RICETTA_SCADUTA);					
					
					else if(($object['codEsito']) == NRE_REG_CODE_ERR)
						$this -> mostraPresaInCaricaError(NRE_REG_CODE_ERR);

					else if(($object['codEsito']) == NRE_RAGGRUPPAMENTO_ERR)
						$this -> mostraPresaInCaricaError(NRE_RAGGRUPPAMENTO_ERR);

					else if(($object['codEsito']) == CF_NON_CORRISPONDENTE)
						$this -> mostraPresaInCaricaError(CF_NON_CORRISPONDENTE);
						
					else if(($object['codEsito']) == NRE_PROGRESSIVO_ERR)
							$this -> mostraPresaInCaricaError(NRE_PROGRESSIVO_ERR);
					
					else if(($object['codEsito']) == CF_NON_CORRISPONDENTE)
						$this -> mostraPresaInCaricaError(CF_NON_CORRISPONDENTE);
					
					else if(($object['codEsito']) == RICETTA_ANNULLATA_DAL_MEDICO)
						$this -> mostraPresaInCaricaError(RICETTA_ANNULLATA_DAL_MEDICO);
					
					
				}
				//$this -> deleteFromDB($idImp);
			}
		}
		
		
		
		
    }   
	
	public function parseRilasciaResponse($idImp, $ret)
	{
		$codEsitoVisualizzazione = $ret->codEsitoVisualizzazione;
		if($codEsitoVisualizzazione == VISUAL_OK)
		{
			$nre = $ret->nre;
			$list = $ret->ElencoErroriRicette;
			$ElencoErroriRicette = array();
			foreach($list as $key => $value)
			{
				$ErroreRicetta = array();
				$details = $list -> $key;
				foreach($details as $key => $value)
				{
					$ErroreRicetta[$key] = $value;
				}
				array_push($ElencoErroriRicette, $ErroreRicetta);
            }
			if($ElencoErroriRicette[0]['codEsito'] == ESITO_OK)
			{
				$this -> setDeletedInDB($idImp);
				$this -> mostraRilasciaOk($idImp, $nre);
			}
			else
			{
				echo "<div style=\"color:#CC0000; font-size: 120%; font-style: italic;\">ATTENZIONE\nSi è verificato un errore sconosciuto. Controllare i dati immessi, quindi riprovare</div>";
			}
		}
		else if($codEsitoVisualizzazione == VISUAL_ERROR)
		{
			$list = $ret->ElencoErroriRicette;
			$ElencoErroriRicette = array();
			foreach($list as $key => $value)
			{
				$ErroreRicetta = array();
				$details = $list -> $key;
				foreach($details as $key => $value)
				{
					$ErroreRicetta[$key] = $value;
				}
				array_push($ElencoErroriRicette, $ErroreRicetta);
            }
			if($ElencoErroriRicette[0]['codEsito'] == OPERAZIONE_NON_CONSENTITA)
			{
				$this -> mostraRilasciaError(OPERAZIONE_NON_CONSENTITA);
			}
		}
	}
	
	/*public function parseVisualizzaDatiResponse($idImp, $ret)
	{
		$codEsitoVisualizzazione = $ret->codEsitoVisualizzazione;
		if($codEsitoVisualizzazione == VISUAL_OK)
		{
			$nre = $ret->nre;
			$cognNome = $ret->cognNome;
			$indirizzo = $ret->indirizzo;
			$list = $ret->ElencoErroriRicette;
			$ElencoErroriRicette = array();
			foreach($list as $key => $value)
			{
				$ErroreRicetta = array();
				$details = $list -> $key;
				foreach($details as $key => $value)
				{
					$ErroreRicetta[$key] = $value;
				}
				array_push($ElencoErroriRicette, $ErroreRicetta);
            }
			if($ElencoErroriRicette[0]['codEsito'] == ESITO_OK)
			{
				$this -> mostraVisualizzaDatiOk($idImp, $nre, $cognNome, $indirizzo);
			}
			else
			{
				echo "<div style=\"color:#CC0000; font-size: 120%; font-style: italic;\">ATTENZIONE\nSi è verificato un errore sconosciuto. Controllare i dati immessi, quindi riprovare</div>";
			}
		}
		else if($codEsitoVisualizzazione == VISUAL_ERROR)
		{
			$list = $ret->ElencoErroriRicette;
			$ElencoErroriRicette = array();
			foreach($list as $key => $value)
			{
				$ErroreRicetta = array();
				$details = $list -> $key;
				foreach($details as $key => $value)
				{
					$ErroreRicetta[$key] = $value;
				}
				array_push($ElencoErroriRicette, $ErroreRicetta);
            }
			if($ElencoErroriRicette[0]['codEsito'] == OPERAZIONE_NON_CONSENTITA)
			{
				$this -> mostraRilasciaError(OPERAZIONE_NON_CONSENTITA);
			}
		}
	}*/
	
	public function mostraPresaInCaricaOk($giaincarica, $idImp, $nre, $cf_medico, $codRegione, $codASLAo, 
                $codSpecializzazione, $cognome_medico, $nome_medico, $anagraficaAss, $indirizzo,
                $tipoPrescrizione, $nonEsenzione, $codDiagnosi, $statoProcesso, $descrizioneDiagnosi, 
				$dataCompilazione, $aslAssistito, $codiciCatalogo, $quantitaCatalogo, $codEsenzione, $dispReg)
	{
					if($giaincarica == 1)
					{
						//echo"\n\n<div style=\"color:#FFFF00; font-size: 109%; font-style: italic;\">ATTENZIONE: La ricetta risulta già presa in carica presso questo centro.</div>";
					}
					else
					{
						echo "Ricetta dematerializzata trovata!";
						echo"\n\n<div style=\"color:#00CCFF; font-size: 109%; font-style: italic;\">RICETTA PRESA IN CARICA CON SUCCESSO</div>";	
					}
					echo "-----------------------------------------------------------------------------------";
					echo "\nCodice Regione: $codRegione\t\tCodice ASL: $codASLAo";
					echo "\nImpegnativa nr. $idImp\t\tRicetta elettronica nr. $nre".					
					"\n\nMedico: $cognome_medico $nome_medico\t\t\tCF: $cf_medico\nCodice ASL:$dispReg\t\t\tCodice specializzazione: $codSpecializzazione".
					"\n\nAssistito: $anagraficaAss\nResidenza: $indirizzo\n";

					if($codEsenzione == null)
						echo "Soggetto non esente.";
					else
						echo "Codice esenzione: $codEsenzione";
						
					echo "\n\nCodice diagnosi: $codDiagnosi\nDiagnosi: $descrizioneDiagnosi";
					echo "\n\nStato ricetta: ";
					if($statoProcesso == STATO_DA_EROGARE)
						echo "DA EROGARE";
					else if($statoProcesso == STATO_IN_EROGAZIONE)
						echo "IN EROGAZIONE";
					else if($statoProcesso == ANNULLATA_DA_PRESCRITTORE)
						echo "ANNULLATA DAL PRESCRITTORE";
					else if($statoProcesso == STATO_EROGATA)
						echo "EROGATA";
					
					echo "\n\nINFORMAZIONI PRESCRIZIONE\n";
					foreach($codiciCatalogo as $key => $value)
					{
						echo "Codice catalogo: $value\n";
						echo "Quantità: $quantitaCatalogo[$key]\n";						
					}
					
					echo "\n\nData compilazione: $dataCompilazione\n";

					echo '-----------------------------------------------------------------------------------';
	}
	
	public function mostraPresaInCaricaError($errorcode)
	{
		echo "\n<div style=\"color:#CC6633\";>-----------------------------------------------------------------------------------</div>";
		switch ($errorcode)
		{
			case IN_EROGAZIONE_DA_ALTRO_CENTRO:
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">RICETTA IN EROGAZIONE PRESSO UN ALTRO CENTRO</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">Verificarne la correttezza, quindi riprovare.</div>";
			break;
			case RICETTA_NON_ESISTENTE:
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">NUMERO RICETTA ELETTRONICA NON PRESENTE</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">Verificarne la correttezza, quindi riprovare.</div>";
			break;
			case NRE_REG_CODE_ERR:
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">NUMERO RICETTA ELETTRONICA FORMALMENTE ERRATO</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">Il codice regione - prime tre cifre - non è valido.</div>";
			break;
			case NRE_RAGGRUPPAMENTO_ERR:
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">NUMERO RICETTA ELETTRONICA FORMALMENTE ERRATO</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">Raggruppamento dei caratteri errato.</div>";
			break;
			case NRE_PROGRESSIVO_ERR: 
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">NUMERO RICETTA ELETTRONICA FORMALMENTE ERRATO</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">Il progressivo - secondo blocco di codice - non è valido.</div>";
			break;
			case CF_NON_CORRISPONDENTE:
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">CODICE FISCALE NON CORRISPONDENTE</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">Il codice fiscale inserito non appartiene al numero di ricetta inserito.</div>";
			break;			
			case RICETTA_SCADUTA:
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">RICETTA SCADUTA</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">La ricetta è scaduta e non può essere presa in carico.</div>";
			break;
			case RICETTA_ANNULLATA_DAL_MEDICO:
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">RICETTA ANNULLATA</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">La ricetta è stata annullata dal medico.</div>";
			break;
		}		
		echo "<div style=\"color:#CC6633\";>-----------------------------------------------------------------------------------</div>";
	}
	
	public function mostraRilasciaOk($idImp, $nre)
	{
		echo "\n<div;>-----------------------------------------------------------------------------------</div>";
		echo "<div style=\"font-size: 120%; font-style: italic;\">Impegnativa nr.$idImp\nRicetta elettronica nr.$nre</div>";
		echo "\n<div style=\"color:#00CCFF; font-size: 100%; font-style: italic;\">La ricetta è stata rilasciata correttamente.</div>";
		echo "<div>-----------------------------------------------------------------------------------</div>";
	}
	
	public function mostraRilasciaError($errorcode)
	{
		//echo "\n<div style=\"color:#CC6633\";>-----------------------------------------------------------------------------------</div>";
		switch ($errorcode)
		{
			case OPERAZIONE_NON_CONSENTITA:
				echo "<div style=\"color:#CC0000; font-size: 120%; font-style: italic;\">ERRORE:\nOPERAZIONE NON CONSENTITA</div>";
				echo "\n\n<div style=\"color:#CC0000; font-size: 100%; font-style: italic;\">La ricetta potrebbe essere già stata rilasciata.</div>";
				break;
		}
	}
	
	/*public function mostraVisualizzaDatiOk($idImp, $nre, $cognNome, $indirizzo)
	{
		echo "Ricetta dematerializzata trovata!";
		echo "\n-----------------------------------------------------------------------------------";
		echo "\nImpegnativa nr. $idImp\nRicetta elettronica nr. $nre".
		"\n\nAssistito: $cognNome\nResidenza: $indirizzo\n";
		echo "-----------------------------------------------------------------------------------";
	}
	
	public function exists($idImp)	
	{		
		$conn = db_connect();
		$query = "SELECT CodiceAss from rdm WHERE IdImpegnativa='$idImp' AND Rilasciata = NULL";
		$rs = mssql_query($query, $conn);
		
		while($row = mssql_fetch_assoc($rs)) {
				if($row['CodiceAss'] == null ||$row['CodiceAss'] == '')
					return 0;
				return 1;			
		}
	}*/
		
	public function setDeletedInDB($idImp)
	{
		$conn = db_connect();
		$update = "UPDATE rdm SET StatoProcesso = ".STATO_DA_EROGARE.", Rilasciata =".RILASCIATA." WHERE IdImpegnativa = '$idImp'";
		mssql_query($update, $conn);		
	}
	
	public function updateDB($idImp, $nre, $cf_medico, $codRegione, $codASLAo, 
                $codSpecializzazione, 
                $cognome_medico, $nome_medico, $anagraficaAss, $indirizzo,
                $tipoPrescrizione, $nonEsenzione, $codDiagnosi, $descrizioneDiagnosi, 
                $dataCompilazione, $tipoVisita, $provAssistito, $aslAssistito, $statoProcesso, $ticket, $quotaFissa,
                $franchigia, $galDirChiamAltro, $codAutenticazioneMedico, $codAutenticazioneErogatore, $codEsitoVisualizzazione, $codiceAss, $codiciCatalogo, $quantitaCatalogo, $codEsenzione, $dispReg, $debug)
        {
            // Create connection
			$conn = db_connect();
			
			/*$select = "SELECT * FROM dbo.stato_processi_rdm
						WHERE Utente = '$user'";
			
			
			$rs = mssql_query($select,$conn);
		
			if($row = mssql_fetch_assoc($rs))
			{	
				$update = "UPDATE stato_processi_rdm SET stato = 1 WHERE Utente = '$user'";
				mssql_query($update,$conn);
			}
			else 
			{
				$insert = "INSERT INTO dbo.stato_processi_rdm (Utente, Stato) VALUES('$user', 1)";
				mssql_query($insert,$conn);
			}*/
				
            /*$query = "INSERT INTO rdm (IdImpegnativa, Nre, CfMedico, CodRegione, CodAsl, CodSpec, CognomeMed, NomeMed, AnaAss, IndirizzoAss, TipoPresc, NonEsenzione, CodDiagnosi, DescrDiagnosi, DataComplilazione, TipoVisita, ProvinciaAss, AslAss, StatoProcesso, Ticket, QuotaFissa, Franchigia, GalDirChiamAltro, CodAutMedico, CodAutErog, CodEsitVisual, MsgEsito, CodiceCom, MsgCom)
			VALUES ('$idImp', '$nre', '$cf_medico', '$codRegione', '$codASLAo', '$codSpecializzazione', '$cognome_medico',"
                    . "'$nome_medico', '$anagraficaAss', '$indirizzo', '$tipoPrescrizione', '$nonEsenzione', '$codDiagnosi', "
                    . "\"$descrizioneDiagnosi\", '$dataCompilazione', '$tipoVisita', '$provAssistito', '$aslAssistito', '$statoProcesso',"
                    . "'$ticket', '$quotaFissa', '$franchigia', '$galDirChiamAltro', '$codAutenticazioneMedico', '$codAutenticazioneErogatore', '$codEsitoVisualizzazione', null, null, null)";
			*/
			
			$updateImp = "UPDATE impegnative SET statordm = 5, DataDimissione = NULL, note_dimissione = NULL WHERE idImpegnativa = '$idImp'";
			if(count($codiciCatalogo) == 1 && count($quantitaCatalogo) == 1)
			{				
				$updateRDM = "UPDATE rdm SET Nre='$nre', CfMedico='$cf_medico', CodRegione='$codRegione', CodAsl='$codASLAo', CodSpec='$codSpecializzazione', CognomeMed=\"$cognome_medico\","
                    . " NomeMed=\"$nome_medico\", AnaAss=\"$anagraficaAss\", IndirizzoAss=\"$indirizzo\", TipoPresc='$tipoPrescrizione', NonEsenzione='$nonEsenzione', CodDiagnosi='$codDiagnosi', "
                    . " DescrDiagnosi=\"$descrizioneDiagnosi\", DataCompilazione='$dataCompilazione', TipoVisita='$tipoVisita', ProvinciaAss='$provAssistito', AslAss='$aslAssistito', StatoProcesso='$statoProcesso',"
                    . " Ticket='$ticket', QuotaFissa='$quotaFissa', Franchigia='$franchigia', GalDirChiamAltro='$galDirChiamAltro', CodAutMedico='$codAutenticazioneMedico', CodAutErog='$codAutenticazioneErogatore', CodiceCatalogo1 = '$codiciCatalogo[0]', Quantita1 = $quantitaCatalogo[0], CodEsenzione = '$codEsenzione', CodEsitVisual='$codEsitoVisualizzazione', CodiceAss='$codiceAss', DispReg='$dispReg' WHERE IdImpegnativa = '$idImp' AND nre = '$nre'";
				
				if($debug == -1)
				{
					echo '\n<pre>';
						print_r($updateRDM);
					echo '</pre>';
				}
				mssql_query($updateRDM, $conn);
			}
			else if(count($codiciCatalogo) == 2 && count($quantitaCatalogo) == 2)
			{
				$updateRDM = "UPDATE rdm SET Nre='$nre', CfMedico='$cf_medico', CodRegione='$codRegione', CodAsl='$codASLAo', CodSpec='$codSpecializzazione', CognomeMed=\"$cognome_medico\","
                    . " NomeMed=\"$nome_medico\", AnaAss=\"$anagraficaAss\", IndirizzoAss=\"$indirizzo\", TipoPresc='$tipoPrescrizione', NonEsenzione='$nonEsenzione', CodDiagnosi='$codDiagnosi', "
                    . " DescrDiagnosi=\"$descrizioneDiagnosi\", DataCompilazione='$dataCompilazione', TipoVisita='$tipoVisita', ProvinciaAss='$provAssistito', AslAss='$aslAssistito', StatoProcesso='$statoProcesso',"
                    . " Ticket='$ticket', QuotaFissa='$quotaFissa', Franchigia='$franchigia', GalDirChiamAltro='$galDirChiamAltro', CodAutMedico='$codAutenticazioneMedico', CodAutErog='$codAutenticazioneErogatore',  CodiceCatalogo1 = '$codiciCatalogo[0]', Quantita1 = $quantitaCatalogo[0],  CodiceCatalogo2 = '$codiciCatalogo[1]', Quantita2 = $quantitaCatalogo[1], CodEsenzione = '$codEsenzione', CodEsitVisual='$codEsitoVisualizzazione', CodiceAss='$codiceAss', DispReg='$dispReg' WHERE IdImpegnativa = '$idImp' AND nre = '$nre'";
				if($debug == -1)
				{
					echo '\n<pre>';
						print_r($updateRDM);
					echo '</pre>';
				}
				mssql_query($updateRDM, $conn);			
			}
			else 
			{
				$updateRDM = "UPDATE rdm SET Nre='$nre', CfMedico='$cf_medico', CodRegione='$codRegione', CodAsl='$codASLAo', CodSpec='$codSpecializzazione', CognomeMed=\"$cognome_medico\","
                    . " NomeMed=\"$nome_medico\", AnaAss=\"$anagraficaAss\", IndirizzoAss=\"$indirizzo\", TipoPresc='$tipoPrescrizione', NonEsenzione='$nonEsenzione', CodDiagnosi='$codDiagnosi', "
                    . " DescrDiagnosi=\"$descrizioneDiagnosi\", DataCompilazione='$dataCompilazione', TipoVisita='$tipoVisita', ProvinciaAss='$provAssistito', AslAss='$aslAssistito', StatoProcesso='$statoProcesso',"
                    . " Ticket='$ticket', QuotaFissa='$quotaFissa', Franchigia='$franchigia', GalDirChiamAltro='$galDirChiamAltro', CodAutMedico='$codAutenticazioneMedico', CodAutErog='$codAutenticazioneErogatore', CodEsenzione = '$codEsenzione', CodEsitVisual='$codEsitoVisualizzazione', CodiceAss='$codiceAss', DispReg='$dispReg' WHERE IdImpegnativa = '$idImp' AND nre = '$nre'";
				if($debug == -1)
				{
					echo '\n<pre>';
						print_r($updateRDM);
					echo '</pre>';
				}
				mssql_query($updateRDM, $conn);
						
			}
			mssql_query($updateImp, $conn);
			/*	$update = "UPDATE stato_processi_rdm SET stato = 0 WHERE Utente = '$user'";
				mssql_query($update,$conn);*/
					
        }
		
	public function deleteFromDB($idImp)
	{
		// Create connection
		$conn = db_connect();
		$delete = "DELETE FROM rdm WHERE IdImpegnativa = '$idImp'";
		
		$rs = mssql_query($delete, $conn);
	}
        
        /*
	* Cifra con il certificato "SanitelCertificate" il cui path e' stato
	*  passato come parametro al costruttore della classe.
	*/
	public function cifraSanitel($text) {
		openssl_public_encrypt($text, $cryptText, $this->publicKey,OPENSSL_PKCS1_PADDING);
		return (base64_encode($cryptText));

	}
        

	/**
	* Il servizio permette alle strutture di erogazione dei servizi sanitari farmaceutici
	* o specialistici di trasmettere elettronicamente al SAC, anche tramite SAR, le
	* informazioni inerenti alla chiusura dell’erogazione delle ricette dematerializzate
	* registrate dai medici prescrittori; preventivamente le ricette devono essere state
	* visualizzate e prese in carico in maniera esclusiva dalla struttura erogatrice(STATO DI PROCESSO=5).
	*
	* @param	nre	Numero Ricetta Elettronica
	* @param	cfAssistito	Codice fiscale dell’assistito.
	* 			Se Elemento l’assistito e' provvisto di Tessera facoltativo Sanitaria l’elemento è OBBLIGATORIO per la ricerca
	*			della prescrizione. Se l’assistito è un soggetto privo di Tessera Sanitaria (ad es. straniero europeo o extraeuropeo) il
	* 			campo deve essere lasciato vuoto in quanto il soggetto non possiede un codice fiscale.
	* @param 	$dettagli_prestazioni	Array di array associativi, ad ogni elemento dell'array principale è associata
	*			una prestazione specialistica. Ogni elemento dell'array principale è un array associativo con le seguenti chiavi:
	*
	*			- codice_prest_spec	Codice della prestazione specialistica effettivamente erogata, come da nomenclatore regionale.
	*			- descr_presc_spec	Descrizione della prestazione specialistica effettivamente erogata, come da nomenclatore regionale
	*			- codice_branca_spec	Codice della branca specialistica della prestazione, come da nomenclatore regionale
	*			- prezzo	Tariffa della prestazione specialistica applicata al cittadino al lordo dello sconto
	*			- prezzo_rimborso	Prezzo rimborso al laboratorio Elemento obbligatorio (se assente indicare 0). Solo per prestazioni specialistiche
	* */
	
}

