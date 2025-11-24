<?php
//require('config.rdm.php');
//MODIFICARE IN .inc.rdm.php per effettuare i test
//include_once('../include/dbengine.inc.php');

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
	
	public $client_annullaErogato;

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
//DIE(INVIO_EROGATO_ENDPOINT);
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
        
		
		$this -> client_annullaErogato = new SoapClient(ANNULLA_EROGATO_WSDL,
		array(
			'login' => LOGIN,
			'password' => PASSWORD,
			'location' => ANNULLA_EROGATO_ENDPOINT,
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
	public function visualizzaErogato($nre,$cfAssistito, $idImp, $giaincarica) {
                
		//$this->logger->info('visualizzaErogato: Invoked Method');
		//$this->logger->debug('visualizzaErogato, nre:'.$nre);
		//$this->logger->debug('visualizzaErogato, cf:'.$cfAssistito );

		if (isset($cfAssistito)) {
			$cfAssistito_cifrato = $this -> cifraSanitel($cfAssistito);
		}
	

      //  echo"<pre>";
		
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
                $json = json_encode($ret, true);
                echo(ltrim($json));
              // echo ($this -> parseVisualizzaResponse($idImp, $ret, $giaincarica));
               
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
	
	public function annullaErogato($nre,$cfAssistito, $idImp) 
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
			'codAnnullamento' => OP_ANNULLA_EROGATO
		);

		$ret = $this -> client_annullaErogato ->annullaErogato($VisualizzaErogatoRichiesta);
		$ret_json=json_encode($ret);
		echo($ret_json);
		//print_r($ret);
		//$this -> parseRilasciaResponse($idImp, $ret);		
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
		//print_r($ret);	
		$this -> parseVisualizzaDatiResponse($idImp, $ret);
	}
	*/
    public function parseVisualizzaResponse($idImp, $ret, $giaincarica)
    {
	
		$codEsitoVisualizzazione = $ret->codEsitoVisualizzazione;
		//echo '<pre>';
		//print_r($ret);
		
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
			$classepriorita = $ret->classePriorita;
			//$tipoVisita = $ret->tipoVisita; //FACOLTATIVO DA DOCUMENTAZIONE
			//$provAssistito = $ret->provAssistito;
			$ticket = $ret->ticket;
			//$aslAssistito = $ret->aslAssistito; //FACOLTATIVO DA DOCUMENTAZIONE
			$quotaFissa = $ret->quotaFissa;
			$franchigia = $ret->franchigia;
			$galDirChiamAltro = $ret->galDirChiamAltro;
			$list = $ret->ElencoDettagliPrescrVisualErogato;
			if($codEsenzione == null)
				$nonEsenzione = 1;
			else 
				$nonEsenzione = 2;
						$ElencoDettagliPrescrVisualErogato = array();
						
			foreach($list as $key => $value)
			{
				$DettaglioPrescrizioneVisualErogato = array();
				$details = $list -> $key;
				//print_r($details);
				foreach($details as $key => $value)
				{
				
					$DettaglioPrescrizioneVisualErogato[$key] = $value;
				}
				array_push($ElencoDettagliPrescrVisualErogato, $DettaglioPrescrizioneVisualErogato);
            }
			
			
			
			$codAutenticazioneMedico = $ret->codAutenticazioneMedico;
			$codAutenticazioneErogatore = $ret->codAutenticazioneErogatore;
			//Ottengo dagli array di ogni prescrizione le informazioni della singola prescrizione e le inserisco in array appositi
			$codiciCatalogo = array();
			$quantitaCatalogo = array();
			$tipoaccesso = array();
			
			//print_r($ElencoDettagliPrescrVisualErogato);
			
			foreach($ElencoDettagliPrescrVisualErogato as $key => $value)
			{
			
			if (isset($value['statoPresc']))
			{
				if(isset($value['codCatalogoPrescr']))
					array_push($codiciCatalogo, $value['codCatalogoPrescr']);
				if(isset($value['quantita']))
					array_push($quantitaCatalogo,$value['quantita']);
				if(isset($value['tipoAccesso']))
					array_push($tipoaccesso,$value['tipoAccesso']);
			}
			elseif(count($value==2))
			{
				if(isset($value[0]->codCatalogoPrescr))
					array_push($codiciCatalogo, $value[0]->codCatalogoPrescr);
				if(isset($value[0]->quantita))
					array_push($quantitaCatalogo,$value[0]->quantita);
				if(isset($value[0]->tipoAccesso))
					array_push($tipoaccesso,$value[0]->tipoAccesso);
					
				if(isset($value[1]->codCatalogoPrescr))
					array_push($codiciCatalogo, $value[1]->codCatalogoPrescr);
				if(isset($value[1]->quantita))
					array_push($quantitaCatalogo,$value[1]->quantita);
				if(isset($value[1]->tipoAccesso))
					array_push($tipoaccesso,$value[1]->tipoAccesso);
				
			}
			
			
			}
			
		//print_r($codiciCatalogo);
		//	die("qui");
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
					$dataCompilazione, $aslAssistito, $codiciCatalogo, $quantitaCatalogo, $codEsenzione, $dispReg,$classepriorita,$tipoaccesso);			
			}
			//Altrimenti aggiorno il record con le informazioni ricavate
			else
			{
				$this -> mostraPresaInCaricaOk($giaincarica, $idImp, $nre, $cf_medico, $codRegione, $codASLAo, 
					$codSpecializzazione, $cognome_medico, $nome_medico, $anagraficaAss, $indirizzo,
					$tipoPrescrizione, $nonEsenzione, $codDiagnosi, $statoProcesso, $descrizioneDiagnosi, 
					$dataCompilazione, $aslAssistito, $codiciCatalogo, $quantitaCatalogo, $codEsenzione, $dispReg,$classepriorita,$tipoaccesso);
				$this -> updateDB($idImp, $nre, $cf_medico, $codRegione, $codASLAo, 
					$codSpecializzazione, $cognome_medico, $nome_medico, $anagraficaAss, $indirizzo,
					$tipoPrescrizione, $nonEsenzione, $codDiagnosi, $descrizioneDiagnosi, 
					$dataCompilazione, $tipoVisita, $provAssistito, $aslAssistito, $statoProcesso, $ticket, $quotaFissa,
					$franchigia, $galDirChiamAltro, $codAutenticazioneMedico, $codAutenticazioneErogatore, $codEsitoVisualizzazione, $codiceAss, $codiciCatalogo, $quantitaCatalogo, $codEsenzione,$dispReg,$classepriorita,$tipoaccesso);
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
			
			if(count($ElencoErroriRicette) > 0){
				foreach($ElencoErroriRicette as $error){
					echo $error['codEsito'].' - '.$error['esito']."\n";
				}
				$this -> deleteFromDB($idImp);
			}
			/*
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
				$this -> deleteFromDB($idImp);
			}*/
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
				$dataCompilazione, $aslAssistito, $codiciCatalogo, $quantitaCatalogo, $codEsenzione, $dispReg,$classepriorita,$tipoaccesso)
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
					echo "\nClasse priorità: $classepriorita";
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
						echo "Tipo Accesso: $tipoaccesso[$key]\n";						
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
			case RICETTA_ANNULLATA_DAL_MEDICO:
				echo "<div style=\"color:#FF3300; font-size: 120%; font-style: italic;\">RICETTA ANNULLATA DAL MEDICO</div>";
				echo "\n<div style=\"color:#FF3300; font-size: 100%; font-style: italic;\">Il medico ha annullato la ricetta.Impossibile procedere.</div>";
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
                $franchigia, $galDirChiamAltro, $codAutenticazioneMedico, $codAutenticazioneErogatore, $codEsitoVisualizzazione, $codiceAss, $codiciCatalogo, $quantitaCatalogo, $codEsenzione, $dispReg,$classepriorita,$tipoaccesso)
        {
            // Create connection
			$conn = db_connect();
			$sqTipoAccesso="";
			if (count($tipoaccesso)>0)
			$sqTipoAccesso=',tipoaccesso=$tipoaccesso[0]';
			
			
			
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
		//	print_r($codiciCatalogo);
		
			$updateImp = "UPDATE impegnative SET statordm = 5, DataDimissione = NULL, note_dimissione = NULL WHERE idImpegnativa = '$idImp'";
			
			if(count($codiciCatalogo) == 1 && count($quantitaCatalogo) == 1)
			{				
				$updateRDM = "UPDATE rdm SET Nre='$nre', CfMedico='$cf_medico', CodRegione='$codRegione', CodAsl='$codASLAo', CodSpec='$codSpecializzazione', CognomeMed=\"$cognome_medico\","
                    . " NomeMed=\"$nome_medico\", AnaAss=\"$anagraficaAss\", IndirizzoAss=\"$indirizzo\", TipoPresc='$tipoPrescrizione', NonEsenzione='$nonEsenzione', CodDiagnosi='$codDiagnosi', "
                    . " DescrDiagnosi=\"$descrizioneDiagnosi\", DataCompilazione='$dataCompilazione', TipoVisita='$tipoVisita', ProvinciaAss='$provAssistito', AslAss='$aslAssistito', StatoProcesso='$statoProcesso',"
                    . " Ticket='$ticket', QuotaFissa='$quotaFissa', Franchigia='$franchigia', GalDirChiamAltro='$galDirChiamAltro', CodAutMedico='$codAutenticazioneMedico', CodAutErog='$codAutenticazioneErogatore', CodiceCatalogo1 = '$codiciCatalogo[0]', Quantita1 = $quantitaCatalogo[0], CodEsenzione = '$codEsenzione', CodEsitVisual='$codEsitoVisualizzazione', CodiceAss='$codiceAss', DispReg='$dispReg',classepriorita='$classepriorita' $sqTipoAccesso WHERE IdImpegnativa = '$idImp' AND nre = '$nre'";
				echo '\n<pre>';
				print_r($updateRDM);
				echo '</pre>';
				mssql_query($updateRDM, $conn);
			}
			else if(count($codiciCatalogo) == 2 && count($quantitaCatalogo) == 2)
			{
				$updateRDM = "UPDATE rdm SET Nre='$nre', CfMedico='$cf_medico', CodRegione='$codRegione', CodAsl='$codASLAo', CodSpec='$codSpecializzazione', CognomeMed=\"$cognome_medico\","
                    . " NomeMed=\"$nome_medico\", AnaAss=\"$anagraficaAss\", IndirizzoAss=\"$indirizzo\", TipoPresc='$tipoPrescrizione', NonEsenzione='$nonEsenzione', CodDiagnosi='$codDiagnosi', "
                    . " DescrDiagnosi=\"$descrizioneDiagnosi\", DataCompilazione='$dataCompilazione', TipoVisita='$tipoVisita', ProvinciaAss='$provAssistito', AslAss='$aslAssistito', StatoProcesso='$statoProcesso',"
                    . " Ticket='$ticket', QuotaFissa='$quotaFissa', Franchigia='$franchigia', GalDirChiamAltro='$galDirChiamAltro', CodAutMedico='$codAutenticazioneMedico', CodAutErog='$codAutenticazioneErogatore',  CodiceCatalogo1 = '$codiciCatalogo[0]', Quantita1 = $quantitaCatalogo[0],  CodiceCatalogo2 = '$codiciCatalogo[1]', Quantita2 = $quantitaCatalogo[1], CodEsenzione = '$codEsenzione', CodEsitVisual='$codEsitoVisualizzazione', CodiceAss='$codiceAss', DispReg='$dispReg',classepriorita='$classepriorita' $sqTipoAccesso WHERE IdImpegnativa = '$idImp' AND nre = '$nre'";
				//echo '\n<pre>';
				//print($updateRDM);
				//echo '</pre>';
				mssql_query($updateRDM, $conn);			
			}
			else 
			{
				$updateRDM = "UPDATE rdm SET Nre='$nre', CfMedico='$cf_medico', CodRegione='$codRegione', CodAsl='$codASLAo', CodSpec='$codSpecializzazione', CognomeMed=\"$cognome_medico\","
                    . " NomeMed=\"$nome_medico\", AnaAss=\"$anagraficaAss\", IndirizzoAss=\"$indirizzo\", TipoPresc='$tipoPrescrizione', NonEsenzione='$nonEsenzione', CodDiagnosi='$codDiagnosi', "
                    . " DescrDiagnosi=\"$descrizioneDiagnosi\", DataCompilazione='$dataCompilazione', TipoVisita='$tipoVisita', ProvinciaAss='$provAssistito', AslAss='$aslAssistito', StatoProcesso='$statoProcesso',"
                    . " Ticket='$ticket', QuotaFissa='$quotaFissa', Franchigia='$franchigia', GalDirChiamAltro='$galDirChiamAltro', CodAutMedico='$codAutenticazioneMedico', CodAutErog='$codAutenticazioneErogatore', CodEsenzione = '$codEsenzione', CodEsitVisual='$codEsitoVisualizzazione', CodiceAss='$codiceAss', DispReg='$dispReg',classepriorita='$classepriorita' $sqTipoAccesso WHERE IdImpegnativa = '$idImp' AND nre = '$nre'";
				//echo '\n<pre>';
				//print($updateRDM);
				//echo '</pre>';
				mssql_query($updateRDM, $conn);
						
			}
			print($updateRDM);
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
	public function invioErogato($nre,$cfAssistito,$idImp,$richiesta) 
	{              
		
		//die("$richiesta");
		
		$ret = $this -> client_invioErogato -> invioErogato($richiesta);
		$ret_json=json_encode($ret);
		echo($ret_json);
		//$this -> parseVisualizzaDatiResponse($idImp, $ret);
		
		
		
		
}

	public function get_prestazioni($idImp, $dal, $al)
	{
		$dettagli_prestazioni = array();
			
		$conn = db_connect();
		
		$from = date_format($dal, 'Y-m-d H:i:s');
		$to = date_format($al, 'Y-m-d H:i:s');
		
		/*$dal = $from->format('Y-m-d H:i:s');
		$al = $to->format('Y-m-d H:i:s');*/
		
		$select = "SELECT dbo.RicetteCodici.IdRicetta, dbo.RicetteCodici.pacchetto, SUM(dbo.RicettePresenze.QuantitaErogate) 
		AS qta, dbo.tariffe.codice, dbo.tariffe.dal, dbo.tariffe.al, dbo.tariffe.tariffa, SUM(dbo.tariffe.tariffa * dbo.RicettePresenze.QuantitaErogate) AS tot
		FROM		dbo.tariffe RIGHT OUTER JOIN
                    dbo.tipologia ON dbo.tariffe.idtipologia = dbo.tipologia.idtipologia RIGHT OUTER JOIN
                    dbo.RicetteCodici INNER JOIN
                    dbo.RicettePresenze ON dbo.RicetteCodici.IdRicettaCodice = dbo.RicettePresenze.IdRicettaCodice ON 
                    dbo.tipologia.idtipologia = dbo.RicetteCodici.CodicePrestazione
		WHERE     (dbo.tariffe.al >= CONVERT(DATETIME, $from, 102)) AND (dbo.tariffe.dal <= CONVERT(DATETIME, $to, 102))
		GROUP BY dbo.RicetteCodici.IdRicetta, dbo.RicetteCodici.pacchetto, dbo.tariffe.dal, dbo.tariffe.al, dbo.tariffe.codice, dbo.tariffe.tariffa
		HAVING      (dbo.RicetteCodici.IdRicetta = $idImp)";
		
		print_r($select);
		
		$select = "SELECT idRicetta, codicePrestazione FROM RicetteCodici WHERE IdRicetta = '$idImp'";
		$rs = mssql_query($select,$conn);
		
		while($row = mssql_fetch_assoc($rs))
		{
			$cod = $row['codicePrestazione'];
			$idRicetta = $row['idRicetta'];
			
			$selectCod = "SELECT tariffa, codice FROM tariffe WHERE idtipologia = '$cod' AND dal <= '$dal' AND al >= '$al'";
						
			$rse = mssql_query($selectCod,$conn);
			
			while($row =  mssql_fetch_assoc($rse))
			{
				$codice_prest_spec = $row['codice'];
				$prezzo = $row['tariffa'];
			}
			
			$selectQuantita = "SELECT  FROM tariffe WHERE idtipologia = '$cod' AND dal <= '$dal' AND al >= '$al'";
			
			
			array_push($dettagli_prestazioni, array(
							'codice_prest_spec' => "$codice_prest_spec", //TODO AGGIUNGERE DETTAGLI
							'descr_presc_spec' => '', //TODO AGGIUNGERE DETTAGLI
							'codice_branca_spec' => COD_BRANCA, //Codice della branca specialistica della prestazione, come da nomenclatore regionale
							'prezzo' => "$prezzo",//Tariffa della prestazione specialistica applicata al cittadino al lordo dello sconto
							'quantitaErogata' => '',
							'prezzo_rimborso' => '0' // Prezzo rimborso al laboratorio Elemento obbligatorio (se assente indicare 0). Solo per prestazioni specialistiche
			));
			
		}
		
		return $dettagli_prestazioni;
	}	
}

