<?php

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ERROR);

/**
 * Classe per la gestione delle chiamate al sistema di
 * interoperabilità del Fascicolo sanitario elettronico Campano
 */
final class FseCampania
{
    private $versione = 1.4;
    private $username;
    private $password;
    private $cacheWsdl = WSDL_CACHE_NONE;
    private $soapVersion = SOAP_1_1;
    private $sslMethod = 'SOAP_SSL_METHOD_SSLv3';
    private $trace = true;
    private $exception = false;
    private $location;
    private $streamContext;
    private $wsdl;
	private $pinCode;
    private $identificativoOrganizzazione;
    private $debug;

    /**
     * @param $endpoint string Nome dell'indice array presente in data/api.php
     * @param bool $debug Attiva o disattiva la risposta in modalità debug
     */
    public function __construct($endpoint, $debug = false)
    {

        //Controllo la versione di PHP sia >= 7.2
        $phpVersion = phpversion();

        if ((float)phpversion() < 7.2) {
            try {
                throw new Exception("Per utlizzare correttamente la classe si richiede PHP 7.2. Versione attiva : " . phpversion());
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            die();
        }

        $auth = require_once('data/auth.php');
        $api = require_once('data/api.php');
        $wsdlPath = require_once('data/pathWSDL.php');


        $this->username = $auth['username'];
        $this->password = $auth['password'];
        $this->location = $api[$endpoint];
        $this->wsdl = $wsdlPath[$endpoint];
		$this->pinCode = $auth['pinCode'];
        $this->identificativoOrganizzazione = $auth['identificativoOrganizzazione'];
        $this->debug = $debug;
        $this->streamContext = stream_context_create($this->streamContext());


        if ($this->debug) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
    }


    /**
     * Funzione che ritorna la struttura base delle informazioni per la chiamata soap
     * @return array[]
     */
    private function streamContext()
    {
        return array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }


    /*
     * Inserisco i parametri per la chiamata SOAP
     */
    private function getOption()
    {
        return array(
            'login' => $this->username,
            'password' => $this->password,
            'cache_wsdl' => $this->cacheWsdl,
            'soap_version' => $this->soapVersion,
            'ssl_method' => $this->sslMethod,
            'trace' => $this->trace,
            'exception' => $this->exception,
            'location' => $this->location,
            'stream_context' => $this->streamContext
        );
    }


    /**
     * Funzione per la comunicazione dei consensi del paziente<br>
     * Il servizio di Comunicazione dei Consensi permette ad un attore del processo che risulti abilitato all’utilizzo di tale funzionalità di esprimere i diversi tipi di consenso previsti dall’allegato B del decreto 4 agosto 2017.
     * l consensi vengono espressi ogni volta come se si trattasse di una prima volta, ossia per tale argomento non viene applicato il concetto di variazione di tutte o parte delle scelte effettuate in precedenza, ma devono sempre essere esplicitati tutti i consensi richiesti che sostituiscono completamente quelli precedenti.
     * @param $identificatoUtente string Id Utente
     * @param $pinCode string PinCode utente SistemaTS
     * @param $strutturaUtente string Codifica HSP.11 - HSP.11bis - STS.11 - RIA.11
     * @param $ruoloUtente string Tabella 5.4-1 di Affinity domain
     * @param $contestoOperativo string Tabella 5.4-2 di Affinity domain
     * @param $identificativoAssistitoGenitoreTutore string Codice fiscale dell'assistito cui si riferisce la richiesta o del genitore/tutore che ha richiesto l'operazione
     * @param $presaInCarica string Tabella 5.4-4 di Affinity domain N.B. Passare valore True o False come stringa
     * @param $tipoAttività string Tabella 5.4-5 di Affinity domain
     * @param $indetificatoAssistitoConsenso string Codice fiscale dell'assistito a cui si riferisce la richiesta
     * @param $identificativoGenitoreConsenso string Codice fiscale del genitore/tutore
     * @param $listaConsensi array Consenso, si ripete per le varie tipologie Ex: $listaConsenso = array('Consenso' => array(
     * 'CodiceConsenso'=> 'C1','ValoreConsenso' => 'true','Data' => '20171110155300', 'Note => ''),
     * 'CodiceConsenso'=> 'C2','ValoreConsenso' => 'true','Data' => '20171110155300', 'Note => '')));
     * @param $indetificativoInformativa string Identificativo dell'informativa
     * @return array Ritorna array con i valori di richiesta e risposta
     */
    public function comunicazioneConsensi($identificatoUtente, $pinCode, $strutturaUtente, $ruoloUtente, $contestoOperativo, $identificativoAssistitoGenitoreTutore, $presaInCarica, $tipoAttività, $indetificatoAssistitoConsenso, $identificativoGenitoreConsenso, $listaConsensi, $indetificativoInformativa)
    {
        $client = new SoapClient($this->wsdl, $this->getOption());
        try {
            $response = $client->comunicazioneConsensi(array(
                'IdentificativoUtente' => $identificatoUtente,
                'pinCode' => $pinCode,
                'IdentificativoOrganizzazione' => $this->identificativoOrganizzazione,
                'StrutturaUtente' => $strutturaUtente,
                'RuoloUtente' => $ruoloUtente,
                'ContestoOperativo' => $contestoOperativo,
                'IdentificativoAssistitoGenitoreTutore' => $identificativoAssistitoGenitoreTutore,
                'PresaInCarico' => $presaInCarica,
                'TipoAttivita' => $tipoAttività,
                'IdentificativoAssistitoConsenso' => $indetificatoAssistitoConsenso,
                'IdentificativoGenitoreConsenso' => $identificativoGenitoreConsenso,
                'ListaConsensi' => $listaConsensi,
                'IdentificativoInformativa' => $indetificativoInformativa
            ));

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'response' => $response
                );
            }
			
            return array(
                'Esito' => $response->Esito,
                'codEsito' => $response->ElencoErrori->Errore->codEsito,
                'text_response' => $response->ElencoErrori->Errore->esito,
                'tipoErrore' => $response->ElencoErrori->Errore->tipoErrore,
            );

        } catch (Exception $e) {

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'error' => $client
                );
            }

            return array(
                'Esito' => 'Error',
                'codEsito' => '500',
                'response' => 'Errore durante la chiamata SOAP.',
                'tipoErrore' => 'Bloccante',
            );
        }
    }


    /**
     * Funzione per la verifica dello stato del file caricato <br>
     * Il servizio di Esito Caricamento Documenti permette ad un attore del processo che risulti abilitato all’utilizzo di tale funzionalità di recuperare l’esito del controllo effettuato dal Sistema del FSE-INI sul documento di tipo .pdf inviato.
     * @param $identificatoUtente string Id utente
     * @param $pinCode string PinCode utente SistemaTS
     * @param $strutturaUtente string Codifica HSP.11 - HSP.11bis - STS.11 - RIA.11
     * @param $ruoloUtente string Tabella 5.4-1 di Affinity domain
     * @param $identificativiTemporaneiDocumenti string Identificativo temporaneo del set di metadati
     * @param $dataRicercaDA string Criterio di ricerca data DA, nel formato yyyymmddHH24MISS, si basa sulla data di caricamento del documento
     * @param $dataRicercaA string Criterio di ricerca data A, nel formato yyyymmddHH24MISS, si basa sulla data di caricamento del documento
     * @return array Ritorna array con i valori di richiesta e risposta
     */
    public function esitoCaricamentoDocumento($identificatoUtente, $pinCode, $strutturaUtente, $ruoloUtente, $identificativiTemporaneiDocumenti, $dataRicercaDA, $dataRicercaA)
    {
        $client = new SoapClient($this->wsdl, $this->getOption());
        try {
            $response = $client->esitoCaricamentoDocumento(array(
                'IdentificativoUtente' => $identificatoUtente,
                'pinCode' => $this->pinCode,
                'IdentificativoOrganizzazione' => $this->identificativoOrganizzazione,
                'StrutturaUtente' => $strutturaUtente,
                'RuoloUtente' => $ruoloUtente,
                'IdentificativiTemporaneiDocumenti' => $identificativiTemporaneiDocumenti,
                'DataRicercaDA' => $dataRicercaDA,
                'DataRicercaA' => $dataRicercaA
            ));

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'response' => $response
                );
            }

            return array(
                'Esito' => $response->Esito,
                'codEsito' => $response->ElencoErrori->Errore->codEsito,
                'text_response' => $response->ElencoErrori->Errore->esito,
                'tipoErrore' => $response->ElencoErrori->Errore->tipoErrore,
                'listaDocumenti' => $response->EsitoCaricamentoDocumento ?? ''
            );

        } catch (Exception $e) {

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'error' => $client
                );
            }

            return array(
                'Esito' => 'Error',
                'codEsito' => '500',
                'response' => 'Errore durante la chiamata SOAP.',
                'tipoErrore' => 'Bloccante',
            );
        }
    }


    /**
     * Funzione per il recupero dei documenti caricati <br>
     * "IdentificativoOrgDoc" / "IdentificativoRepository" / "IdentificativoDocumento"
     * I tre dati devono essere recuperati da RicercaDocumentiRicevuta e identificano il documento da recuperare e la sua ubicazione
     * @param $identificatoUtente string Id utente
     * @param $pinCode string PinCode utente SistemaTS
     * @param $strutturaUtente string Codifica HSP.11 - HSP.11bis - STS.11 - RIA.11
     * @param $ruoloUtente string Tabella 5.4-1 di Affinity domain
     * @param $contestoOperativo string Tabella 5.4-2 di Affinity domain
     * @param $identificativoAssistito string Codice fiscale dell'assistito
     * @param $presaInCarico string Tabella 5.4-4 di Affinity domain N.B. Passare valore True o False come stringa
     * @param $identificativoOrgDoc string Codifica OID della regione/PA
     * @param $identificativoRepository string Codifica OID del repository regionale
     * @param $identificativoDocumento string Codifica OID del documento
     * @return array Ritorna array con i valori di richiesta e risposta
     */
    public function recuperoDocumento($identificatoUtente, $pinCode, $strutturaUtente, $ruoloUtente, $contestoOperativo, $identificativoAssistito, $presaInCarico, $identificativoOrgDoc, $identificativoRepository, $identificativoDocumento)
    {
        $client = new SoapClient($this->wsdl, $this->getOption());
        try {
            $response = $client->recuperoDocumento(array(
                'IdentificativoUtente' => $this->username,
                'pinCode' => $this->pinCode,
                'IdentificativoOrganizzazione' => $this->identificativoOrganizzazione,
                'StrutturaUtente' => $strutturaUtente,
                'RuoloUtente' => $ruoloUtente,
                'ContestoOperativo' => $contestoOperativo,
                'IdentificativoAssistito' => $identificativoAssistito,
                'PresaInCarico' => $presaInCarico,
                'IdentificativoOrgDoc' => $identificativoOrgDoc,
                'IdentificativoRepository' => $identificativoRepository,
                'IdentificativoDocumento' => $identificativoDocumento
            ));

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'response' => $response
                );
            }

            return array(
                'Esito' => $response->Esito,
                'codEsito' => $response->ElencoErrori->Errore->codEsito,
                'text_response' => $response->ElencoErrori->Errore->esito,
                'tipoErrore' => $response->ElencoErrori->Errore->tipoErrore,
                'infoDocumento' => array(
                    'documento' => $response->Documento ?? '',
                    'tipoMime' => $response->TipoMime ?? '',
                    'identificativoOrgDoc' => $response->IdentificativoOrgDoc ?? '',
                    'identificativoRepository' => $response->IdentificativoOrgDoc ?? '',
                    'identificativoDocumento' => $response->IdentificativoDocumento ?? '',
                )
            );

        } catch (Exception $e) {

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'error' => $client
                );
            }

            return array(
                'Esito' => 'Error',
                'codEsito' => '500',
                'response' => 'Errore durante la chiamata SOAP.',
                'tipoErrore' => 'Bloccante',
            );
        }

    }


    /**
     * Funzione per il recupero dell'informativa <br>
     * Il servizio di Recupero dell’Informativa permette ad un attore del processo che risulti abilitato all’utilizzo di tale funzionalità di acquisire i documenti in formato pdf forniti dalle Regioni e dalle Province Autonome che riportano le informazioni generali sul FSE e la modulistica specifica che serve ad esprimere le diverse tipologie di consenso.<br>
     * N.B. per convertire in modo corretto i file ricevuti utilizzare base64_decode(base64_encode("base64informativa"))
     * @param $identificatoUtente string Id utente
     * @param $pinCode string PinCode utente SistemaTS
     * @param $strutturaUtente string Codifica HSP.11 - HSP.11bis - STS.11 - RIA.11
     * @param $ruoloUtente string Tabella 5.4-1 di Affinity domain
     * @param $identificativoInformativa string Identificativo dell'informativa
     * @return array Ritorna array con i valori di richiesta e risposta
     */
    public function recuperoInformativa($identificatoUtente, $pinCode, $ruoloUtente, $identificativoInformativa)
    {
        $client = new SoapClient($this->wsdl, $this->getOption());
        try {
            $response = $client->recuperoInformativa(array(
                'IdentificativoUtente' => $identificatoUtente,
                'pinCode' => $pinCode,
                'IdentificativoOrganizzazione' => $this->identificativoOrganizzazione,
                'StrutturaUtente' => '',
                'RuoloUtente' => $ruoloUtente,
                'TipoAttivita' => 'READ',
                'IdentificativoInformativa' => $identificativoInformativa
            ));

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'response' => $response
                );
            }

            return array(
                'Esito' => $response->Esito,
                'codEsito' => $response->ElencoErrori->Errore->codEsito,
                'text_response' => $response->ElencoErrori->Errore->esito,
                'tipoErrore' => $response->ElencoErrori->Errore->tipoErrore,
                'infoInformativa' => array(
                    'informativa' => $response->Informativa ?? '',
                    'tipoMimeInformativa' => $response->TipoMimeInformativa ?? '',
                    'modulistica' => $response->Modulistica ?? '',
                    'tipoMimeModulistica' => $response->TipoMimeModulistica ?? '',
                    'identificativoInformativa' => $response->IdentificativoInformativa ?? ''
                )
            );

        } catch (Exception $e) {

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'error' => $client
                );
            }

            return array(
                'Esito' => 'Error',
                'codEsito' => '500',
                'response' => 'Errore durante la chiamata SOAP.',
                'tipoErrore' => 'Bloccante',
            );
        }
    }


    /**
     * Funzione per la ricerca dei documenti caricati in pase al paziente<br>
     * Il servizio di Ricerca Documenti permette ad un attore del processo che risulti abilitato all’utilizzo di tale funzionalità di richiedere i documenti appartenenti ad un assistito relativamente ad un determinato periodo di tempo, sottoforma di lista di metadati.
     * @param $identificatoUtente string Id utente
     * @param $pinCode string PinCode utente SistemaTS
     * @param $strutturaUtente string Codifica HSP.11 - HSP.11bis - STS.11 - RIA.11
     * @param $ruoloUtente string Tabella 5.4-1 di Affinity domain
     * @param $contestoOperativo string Tabella 5.4-2 di Affinity domain
     * @param $identificativoAssistito string Codice fiscale dell'assistito
     * @param $presaInCarico string Tabella 5.4-4 di Affinity domain. Passare valore True o False come stringa
     * @param $dataRicercaDA string Criterio di ricerca data DA, nel formato yyyymmddHH24MISS, si basa su DataValidazioneDocumento
     * @param $dataRicercaA string Criterio di ricerca data A, nel formato yyyymmddHH24MISS, si basa su DataValidazioneDocumento
     * @param $identificativoDocumento string Identificativo/i del/i set di metadati, codifica UUID oppure OID del/i documento/i
     * @return array Ritorna array con i valori di richiesta e risposta
     */
    public function ricercaDocumenti($identificatoUtente, $pinCode, $strutturaUtente, $ruoloUtente, $contestoOperativo, $identificativoAssistito, $presaInCarico, $dataRicercaDA = null, $dataRicercaA = null, $identificativoDocumento = null)
    {
        $client = new SoapClient($this->wsdl, $this->getOption());
        try {
            $response = $client->ricercaDocumenti(array(
                'IdentificativoUtente' => $this->username,
                'pinCode' => $this->pinCode,
                'IdentificativoOrganizzazione' => $this->identificativoOrganizzazione,
                'StrutturaUtente' => $strutturaUtente,
                'RuoloUtente' => $ruoloUtente,
                'ContestoOperativo' => $contestoOperativo,
                'IdentificativoAssistito' => $identificativoAssistito,
                'PresaInCarico' => $presaInCarico,
                'DataRicercaDA' => $dataRicercaDA,
                'DataRicercaA' => $dataRicercaA,
                'IdentificativoDocumento' => $identificativoDocumento
            ));

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'response' => $response
                );
            }

            return array(
                'Esito' => $response->Esito,
                'codEsito' => $response->ElencoErrori->Errore->codEsito,
                'text_response' => $response->ElencoErrori->Errore->esito,
                'tipoErrore' => $response->ElencoErrori->Errore->tipoErrore,
                'metadato' => $response->Metadato ?? ''
            );

        } catch (Exception $e) {

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'error' => $client
                );
            }

            return array(
                'Esito' => 'Error',
                'codEsito' => '500',
                'text_response' => 'Errore durante la chiamata SOAP.',
                'tipoErrore' => 'Bloccante',
            );
        }
    }


    /**
     * Funzione per la visualizzazione dello stato dei consensi del paziente<br>
     * Il servizio di Interrogazione dello Stato dei Consensi permette ad un attore del processo che risulti abilitato all’utilizzo di tale funzionalità di consultare i consensi forniti da un assistito secondo quanto richiesto dalla normativa del Fascicolo Sanitario Elettronico regionale.
     * @param $identificatoUtente string Id utente
     * @param $pinCode string PinCode utente SistemaTS
     * @param $strutturaUtente string Codifica HSP.11 - HSP.11bis - STS.11 - RIA.11
     * @param $ruoloUtente string Tabella 5.4-1 di Affinity domain
     * @param $contestoOperativo string Tabella 5.4-2 di Affinity domain
     * @param $identificativoAssistitoGenitoreTutore string Codice fiscale dell'assistito cui si riferisce la richiesta o del genitore/tutore che ha richiesto l'operazione
     * @param $presaInCarico string string Tabella 5.4-4 di Affinity domain. Passare valore True o False come stringa
     * @param $identificativoAssistitoConsenso string Codice fiscale dell'assistito a cui si riferisce la richiesta
     * @return array Ritorna array con i valori di richiesta e risposta
     */
    public function statoConsensi($identificatoUtente, $pinCode, $strutturaUtente, $ruoloUtente, $contestoOperativo, $identificativoAssistitoGenitoreTutore, $presaInCarico, $identificativoAssistitoConsenso)
    {
        $client = new SoapClient($this->wsdl, $this->getOption());
        try {
            $response = $client->statoConsensi(array(
                'IdentificativoUtente' => $identificatoUtente,
                'pinCode' => $pinCode,
                'IdentificativoOrganizzazione' => $this->identificativoOrganizzazione,
                'StrutturaUtente' => $strutturaUtente,
                'RuoloUtente' => $ruoloUtente,
                'ContestoOperativo' => $contestoOperativo,
                'IdentificativoAssistitoGenitoreTutore' => $identificativoAssistitoGenitoreTutore,
                'PresaInCarico' => $presaInCarico,
                'TipoAttivita' => 'READ',
                'IdentificativoAssistitoConsenso' => $identificativoAssistitoConsenso,
            ));

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'response' => $response
                );
            }

            return array(
                'Esito' => $response->Esito,
                'codEsito' => $response->ElencoErrori->Errore->codEsito,
                'text_response' => $response->ElencoErrori->Errore->esito,
                'tipoErrore' => $response->ElencoErrori->Errore->tipoErrore,
                'identificativoAssistitoConsenso' => $response->IdentificativoAssistitoConsenso ?? '',
                'listaConsensi' => $response->ListaConsensi->Consenso ?? '',
                'identificativoInformativaConsensi' => $response->IdentificativoInformativaConsensi ?? '',
                'identificativoInformativaCorrente' => $response->IdentificativoInformativaCorrente ?? '',
            );

        } catch (Exception $e) {

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'error' => $client
                );
            }

            return array(
                'Esito' => 'Error',
                'codEsito' => '500',
                'response' => 'Errore durante la chiamata SOAP.',
                'tipoErrore' => 'Bloccante',
            );
        }
    }


    /**
     * Funzione per l'eliminazione dei metadati comunicati<br>
     * Il servizio di Cancellazione metadati permette al solo attore del processo che ha redatto un documento (c.d. autore) di cancellarne i metadati con cui è stato precedentemente indicizzato tramite il servizio di Comunicazione metadati.
     * @param $identificatoUtente string Id utente
     * @param $pinCode string PinCode utente SistemaTS
     * @param $strutturaUtente string Codifica HSP.11 - HSP.11bis - STS.11 - RIA.11
     * @param $ruoloUtente string Tabella 5.4-1 di Affinity domain
     * @param $contestoOperativo string Tabella 5.4-2 di Affinity domain
     * @param $identificativoAssistito string Codice fiscale dell'assistito
     * @param $identificativoDocumento string Identificativo/i del/i set di metadati, codifica UUID
     * @return array Ritorna array con i valori di richiesta e risposta
     */
    public function cancellazioneMetadati($identificatoUtente, $pinCode, $strutturaUtente, $ruoloUtente, $contestoOperativo, $identificativoAssistito, $identificativoDocumento)
    {
        $client = new SoapClient($this->wsdl, $this->getOption());
        try {
            $response = $client->cancellazioneMetadati(array(
                'IdentificativoUtente' => $this->username,
                'pinCode' => $this->pinCode,
                'IdentificativoOrganizzazione' => $this->identificativoOrganizzazione,
                'StrutturaUtente' => $strutturaUtente,
                'RuoloUtente' => $ruoloUtente,
                'ContestoOperativo' => $contestoOperativo,
                'IdentificativoAssistito' => $identificativoAssistito,
                'TipoAttivita' => 'DELETE',
                'IdentificativoDocumento' => $identificativoDocumento,
            ));

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'response' => $response
                );
            }

            return array(
                'Esito' => $response->Esito,
                'codEsito' => $response->ElencoErrori->Errore->codEsito,
                'text_response' => $response->ElencoErrori->Errore->esito,
                'tipoErrore' => $response->ElencoErrori->Errore->tipoErrore,
            );

        } catch (Exception $e) {

            if ($this->debug) {
                return array(
                    'request_xml' => $client->__getLastRequest(),
                    'response_xml' => $client->__getLastResponse(),
                    'error' => $client
                );
            }

            return array(
                'Esito' => 'Error',
                'codEsito' => '500',
                'response' => 'Errore durante la chiamata SOAP.',
                'tipoErrore' => 'Bloccante',
            );
        }
    }


    /**
     * Funzione per eseguire l'insert o l'update dei file
     * @param $identificatoUtente string Id utente
     * @param $pinCode string PinCode utente SistemaTS
     * @param $strutturaUtente string Codifica HSP.11 - HSP.11bis - STS.11 - RIA.11
     * @param $ruoloUtente string Tabella 5.4-1 di Affinity domain
     * @param $contestoOperativo string Tabella 5.4-2 di Affinity domain
     * @param $tipoDocumento string Tabella 2.18-1 di Affinity domai
     * @param $identificativoAssistito string Codice fiscale dell'assistito
     * @param $presaInCarico string string Tabella 5.4-4 di Affinity domain. Passare valore True o False come stringa
     * @param $tipoAttivita string Tabella 5.4-5 di Affinity domain
     * @param $tipologiaStruttura string Codice da Tabella 2.8-1
     * @param $tipoMime string Per FSE text/x-cda-r2+xml o application/pdf
     * @param $livelloConfi string Codice Tabella 2.5-1
     * @param $documentoAlto string Codice Tabella 2.3-1
     * @param $documentoMedio string Codice Tabella 2.18-1
     * @param $documentoBasso string Codice Tabella 2.6-1
     * @param $rifDocumento string Codifica UUID del documento a cui si riferisce l'aggiornamento. Campo vuoto se è un primo insert.
     * @param $dataValidazione string Data validazione del documento nel formato yyyymmddHH24MISS
     * @param $ruoloAutore string Tabella 5.4-1
     * @param $istituzioneAutore string Contiene nome dell'organizzazione + OID del sistema di codifica + ISO + codice della struttura
     * @param $cfAutore string Codice Fiscale + codice fisso
     * @param $telecAutore string email dell'autore
     * @param $assettoOrganizzazione string Codice Tabella 2.12-1
     * @param $attivitaClinica string Codice Tabella 3.2-1
     * @param $dataInizioPrest string Data inizio della prestazione, nel formato yyyymmddHH24MISS
     * @param $dataFinePrest string Data fine della prestazione, nel formato yyyymmddHH24MISS
     * @param $rappLegale string CF legale rappresentante secondo Table 4.2.3.1.7-2
     * @param $conservazioneSost string Valore CONS indica che il documento è memorizzato in archivi per la conservazione sostitutiva (di solito è campo vuoto)
     * @param $cidFile string nome cid del file che si deve inviare via MTOM(senza cid:)
     * @param $fileUpload string Percorso del file da caricare
     * @return array Ritorna array con i valori di richiesta e risposta
     */
    public function comunicazioneMetadati($identificatoUtente, $pinCode, $strutturaUtente, $ruoloUtente, $contestoOperativo, $tipoDocumento, $identificativoAssistito, $presaInCarico, $tipoAttivita, $tipologiaStruttura, $tipoMime, $livelloConfi, $documentoAlto, $documentoMedio, $documentoBasso, $rifDocumento, $dataValidazione, $ruoloAutore, $istituzioneAutore, $cfAutore, $telecAutore, $assettoOrganizzazione, $attivitaClinica, $dataInizioPrest, $dataFinePrest, $rappLegale, $conservazioneSost, $cidFile, $fileUpload)
    {
        $xml_template = file_get_contents(__DIR__ . '/template/ComunicazioneMetadati.xml');
        $array_to_replace = array(
            'identificativoUtente' => $this->username,
            'pinCode' => $this->pinCode,
            'identificazioneOrgan' => $this->identificativoOrganizzazione,
            'strutturaUtente' => $strutturaUtente,
            'ruoloUtente' => $ruoloUtente,
            'contestoOperativo' => $contestoOperativo,
            'tipoDocumento' => $tipoDocumento,
            'identificativoAssistito' => $identificativoAssistito,
            'presaInCarico' => $presaInCarico,
            'tipoAttivita' => $tipoAttivita,
            'tipologiaStruttura' => $tipologiaStruttura,
            'tipoMime' => $tipoMime,
            'livelloConfidenzialita' => $livelloConfi,
            'documentoAlto' => $documentoAlto,
            'documentoMedio' => $documentoMedio,
            'documentoBasso' => $documentoBasso,
            'riferimentoDocumento' => $rifDocumento,
            'dataValidazione' => $dataValidazione,
            'ruoloAutore' => $ruoloAutore,
            'istituzioneAutore' => $istituzioneAutore,
            'cfAutore' => $cfAutore,
            'telecAutore' => $telecAutore,
            'assettoOrganizzazione' => $assettoOrganizzazione,
            'attivitaClinica' => $attivitaClinica,
            'dataInizioPrest' => $dataInizioPrest,
            'dataFinePrest' => $dataFinePrest,
            'rappresentanteLegale' => $rappLegale,
            'conservazioneSost' => $conservazioneSost,
            'cidfile' => $cidFile,
        );

        $xml_replaced = $xml_template;
        foreach ($array_to_replace as $key => $element) {
            $xml_replaced = str_replace('##' . $key . '##', $element, $xml_replaced);
        }

        $curl = curl_init();
        $boundary = uniqid();
        $file_info = pathinfo($fileUpload);


        /*
        *------------------------------------------------------------------------------------------------------------------
        *   N.B: non modificare l'indentatura della variabile postData per evitare errori durante la chiamata MTOM
        *------------------------------------------------------------------------------------------------------------------
        */
        $postData = '------=_' . $boundary . '
Content-Type: application/xop+xml; charset=UTF-8; type="text/xml"
Content-Transfer-Encoding: 8bit
Content-ID: <rootpart@soapui.org>

' . $xml_replaced . '

------=_' . $boundary . '
Content-Type: ' . $tipoMime . '; name=' . $file_info["basename"] . '
Content-Transfer-Encoding: binary
Content-ID: <' . $cidFile . '>
Content-Disposition: attachment; name="' . $file_info["basename"] . '"; filename="' . $file_info["basename"] . '"

' . file_get_contents($fileUpload) . '
------=_' . $boundary . '--';

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->location,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: multipart/related; type="application/xop+xml"; start="<rootpart@soapui.org>"; start-info="text/xml"; boundary="----=_' . $boundary . '"',
                'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password)),
        ));

        $response = curl_exec($curl);
		if (!curl_errno($curl)) {
			$info = curl_getinfo($curl);
			var_dump($info);
		}
        curl_close($curl);

        $plainXML = $this->parseXML(trim($response));
        $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        if ($this->debug) {
            return array(
                'request_xml' => $postData,
                'response_xml' => $response,
            );
        }

		var_dump($arrayResult);

        return array(
            'Esito' => $arrayResult['soapenv_Body']['a_ComunicazioneMetadatiRicevuta']['a_Esito'],
            'codEsito' => $arrayResult['soapenv_Body']['a_ComunicazioneMetadatiRicevuta']['a_ElencoErrori']['Errore']['codEsito'],
            'text_response' => $arrayResult['soapenv_Body']['a_ComunicazioneMetadatiRicevuta']['a_ElencoErrori']['Errore']['esito'],
            'tipoErrore' => $arrayResult['soapenv_Body']['a_ComunicazioneMetadatiRicevuta']['a_ElencoErrori']['Errore']['tipoErrore'],
			'identificativoDocumento' => $arrayResult['soapenv_Body']['a_ComunicazioneMetadatiRicevuta']['a_IdentificativoDocumento']
        );

    }


    /**
     * Funzione che parsifica la risposta XML che viene data dalla funzione *comunicazioneMetadati*
     * @param $xml string valore XML da parsificare
     * @return array|mixed|string|string[]|null
     */
    private function parseXML($xml)
    {
        $obj = SimpleXML_Load_String($xml);
        if ($obj === FALSE) return $xml;

        // GET NAMESPACES, IF ANY
        $nss = $obj->getNamespaces(TRUE);
        if (empty($nss)) return $xml;

        // CHANGE ns: INTO ns_
        $nsm = array_keys($nss);
        foreach ($nsm as $key) {
            // A REGULAR EXPRESSION TO MUNG THE XML
            $rgx = '#'               // REGEX DELIMITER
                . '('               // GROUP PATTERN 1
                . '\<'              // LOCATE A LEFT WICKET
                . '/?'              // MAYBE FOLLOWED BY A SLASH
                . preg_quote($key)  // THE NAMESPACE
                . ')'               // END GROUP PATTERN
                . '('               // GROUP PATTERN 2
                . ':{1}'            // A COLON (EXACTLY ONE)
                . ')'               // END GROUP PATTERN
                . '#'               // REGEX DELIMITER
            ;
            // INSERT THE UNDERSCORE INTO THE TAG NAME
            $rep
                = '$1'          // BACKREFERENCE TO GROUP 1
                . '_'           // LITERAL UNDERSCORE IN PLACE OF GROUP 2
            ;
            // PERFORM THE REPLACEMENT
            $xml = preg_replace($rgx, $rep, $xml);
        }
        return $xml;
    }
	
	
    public function invokeValidationGateway(array $module, string $pdfPath, string $gatewayFilesBaseDir, string $certificatePassword): array
    {
        $gatewayFilesPaths = [
            'data-json'     => $gatewayFilesBaseDir . '/data.json',
            'jwt-generator' => $gatewayFilesBaseDir . '/bin/jwt-generator-0.0.8-SNAPSHOT.jar',
            'ssl-pem'       => $gatewayFilesBaseDir . '/ssl/S1#111#BRAINCOMPUTINGXX.pem',
            'ssl-p12'       => $gatewayFilesBaseDir . '/ssl/chiave_firma_fse_2.0.p12',
        ];

        // Creazione data.json con i dati di $module
        $data = [
            "sub"                         => "FNGCML93T57E131J^^^&2.16.840.1.113883.2.9.4.3.2&ISO",
            "subject_role"                => "AAS",
            "purpose_of_use"              => "TREATMENT",
            "iss"                         => "jwt-issuer",
            "subject_application_id"      => "ReMed",
            "subject_application_vendor"  => "Brain Computing S.p.a.",
            "subject_application_version" => "2.0.1",
            "locality"                    => "jwt-location",
            "subject_organization_id"     => $module['regione_struttura'],
            "subject_organization"        => $module['nome_regione'],
            "aud"                         => "https://modipa-val.fse.salute.gov.it/govway/rest/in/FSE/gateway/v1",
            "patient_consent"             => true,
            "action_id"                   => "CREATE",
            "resource_hl7_type"           => "('11502-2^^2.16.840.1.113883.6.1')",
            "jti"                         => "1234",
            "person_id"                   => "{$module['codice_fiscale_paziente']}^^^&2.16.840.1.113883.2.9.4.3.2&ISO",
            "pem_path"                    => $gatewayFilesPaths['ssl-pem'],
            "p12_path"                    => $gatewayFilesPaths['ssl-p12']
        ];
        file_put_contents($gatewayFilesPaths['data-json'], json_encode($data, JSON_PRETTY_PRINT));

        // Creazione JWT tramite il jwt-generator
        ob_start();
        $tokens = system("java -jar {$gatewayFilesPaths['jwt-generator']} -d {$gatewayFilesPaths['data-json']} -a firma -p ciupido -t 1 2>&1");
        ob_end_clean();

        echo "<pre>", var_dump($tokens), exit;

        $bearerToken = '';
        $fseToken = '';

        // Chiamata
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->location);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSLCERT, $gatewayFilesPaths['ssl-p12']);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'P12');
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $certificatePassword);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $bearerToken",
            "FSE-JWT-Signature: $fseToken",
            "Accept: application/json",
            "Content-Type: multipart/form-data",
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'file' => curl_file_create($pdfPath, 'application/pdf', basename($pdfPath)),
            'requestBody' => '{"healthDataFormat": "CDA", "mode": "ATTACHMENT","activity":"VALIDATION"}',
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        echo "<pre>", var_dump($response), exit;


        // Elaborazione risposta

        return [];

    }

}