<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
/**
 * 
 * @author Stefano Pallozzi
 *
 */
class ResponseFactory {

    /**
     * 
     * @param unknown $response
     * @param unknown $attributes
     */
    public static function make($response, $attributes) {
        $doc = new DOMDocument();
        $doc->loadXML($response);

        $result = new stdClass();

        foreach ($attributes as $field) {
            $e = $doc->getElementsByTagName($field);
			$result->{$field} = null;
			
            if ($e != null && $e->item(0) != null) {			
                $result->{$field} = $e->item(0)->nodeValue;
            } 
        }

        return $result;
    }

}

/**
 * 
 * @author Stefano Pallozzi
 *
 */
class MTOMClient {

    /**
     * 
     * @var string
     */
    protected static $certificate = '-----BEGIN CERTIFICATE-----
MIIESjCCAzKgAwIBAgIDHG6uMA0GCSqGSIb3DQEBBQUAMG0xCzAJBgNVBAYTAklU
MR4wHAYDVQQKExVBZ2VuemlhIGRlbGxlIEVudHJhdGUxGzAZBgNVBAsTElNlcnZp
emkgVGVsZW1hdGljaTEhMB8GA1UEAxMYQ0EgQWdlbnppYSBkZWxsZSBFbnRyYXRl
MB4XDTE1MDQyMzEzNDY1MloXDTE4MDQyMzEzNDY0NlowXjELMAkGA1UEBhMCSVQx
HjAcBgNVBAoTFUFnZW56aWEgZGVsbGUgRW50cmF0ZTEbMBkGA1UECxMSU2Vydml6
aSBUZWxlbWF0aWNpMRIwEAYDVQQDEwlTYW5pdGVsQ0YwgZ8wDQYJKoZIhvcNAQEB
BQADgY0AMIGJAoGBANQfl8dJ65QsUGAIRviObyQPA2AYBpxgVjTimrn+B9C9YUSz
6bbZv83ZX5dMYb368G6zsJhvZvoqVZQGofz5psc9HzXNAtZ9BqaZfFQ1JJmdenar
RSsTdPWXuJrkktAMQ10hEo1By2fG2oy1f934idprxOvcoxsO6tqSF8W9MtHvAgMB
AAGjggGEMIIBgDAOBgNVHQ8BAf8EBAMCBDAwgZkGA1UdIwSBkTCBjoAU6kQ/Hxnj
Nz6rqpSCpZ/r/Ba6f7WhcaRvMG0xCzAJBgNVBAYTAklUMR4wHAYDVQQKExVBZ2Vu
emlhIGRlbGxlIEVudHJhdGUxGzAZBgNVBAsTElNlcnZpemkgVGVsZW1hdGljaTEh
MB8GA1UEAxMYQ0EgQWdlbnppYSBkZWxsZSBFbnRyYXRlggMQYnAwgbIGA1UdHwSB
qjCBpzCBpKCBoaCBnoaBm2xkYXA6Ly9jYWRzLmVudHJhdGUuZmluYW56ZS5pdC9j
biUzZENBJTIwQWdlbnppYSUyMGRlbGxlJTIwRW50cmF0ZSxvdSUzZFNlcnZpemkl
MjBUZWxlbWF0aWNpLG8lM2RBZ2VuemlhJTIwZGVsbGUlMjBFbnRyYXRlLGMlM2Rp
dD9jZXJ0aWZpY2F0ZVJldm9jYXRpb25MaXN0MB0GA1UdDgQWBBSTjSlo+yQSiry1
7pHw8rX38FvTYDANBgkqhkiG9w0BAQUFAAOCAQEAsMpmdoET+LHOWLgm5h2VRnhJ
On7znKgidf2PYRvfWnlWVvhrd5ogpfzL0+k4UHrq1RIyyVuFgmbCEPjnnXEnP+UR
BLAv48t8Owb1NVEhXD+Ae4xmekpq/50eND/h+3iwcDGIzbH6yIPi7Z3kJIBKMGTV
d3fZq6rCtyHI/EYm9UNjmGMTRqEzeOkgRQEiY8bWEN7Edgxk+0/Ppo6G6YNllPAG
fSYwjqe5j+4wbLvB4P9fGvpsw3IblZbNa/DC40fcQCLGYaoW/eYfM44oYk69FZQj
KGDdIPaqRB89iKeY98Nj4K6Fj/CFxQYzknculhxpOkQ1uuyugsl9l3/StuN0OA==
-----END CERTIFICATE-----';
    protected $pinCripted = '';

    /**
     * 
     * @var string
     */
    protected $username = '';

    /**
     * 
     * @var string
     */
    protected $password = '';

    /**
     * 
     * @var string
     */
    protected $environment = 'sandbox';

    /**
     * 
     * @var array
     */
    protected static $endpoints = array(
        'production' => array
            (
            'InvioTelematicoSS730pMtomPort' => 'https://invioSS730p.sanita.finanze.it/InvioTelematicoSS730pMtomWeb/InvioTelematicoSS730pMtomPort',												
            'ricevutePdf' => 'https://invioSS730p.sanita.finanze.it/Ricevute730ServiceWeb/ricevutePdf',
            'DettaglioErrori730Service' => 'https://invioSS730p.sanita.finanze.it/EsitoStatoInviiWEB/DettaglioErrori730Service',
            'EsitoInvioDatiSpesa730Service' => 'https://invioSS730p.sanita.finanze.it/EsitoStatoInviiWEB/EsitoInvioDatiSpesa730Service',
            'DocumentoSpesa730pPort' => 'https://invioSS730p.sanita.finanze.it/DocumentoSpesa730pWeb/DocumentoSpesa730pPort',
            'ReportMensilePort' => 'https://invioSS730p.sanita.finanze.it/ReportMensile730Web/ReportMensilePort',
            'InterrogazionePuntuale730Port' => 'https://invioSS730p.sanita.finanze.it/InterrogazionePuntuale730Web/InterrogazionePuntuale730Port'
        ),
        'sandbox' => array
            (
            'InvioTelematicoSS730pMtomPort' => 'https://invioSS730pTest.sanita.finanze.it/InvioTelematicoSS730pMtomWeb/InvioTelematicoSS730pMtomPort',
            'ricevutePdf' => 'https://invioSS730pTest.sanita.finanze.it/Ricevute730ServiceWeb/ricevutePdf',
            'DettaglioErrori730Service' => 'https://invioSS730pTest.sanita.finanze.it/EsitoStatoInviiWEB/DettaglioErrori730Service',
            'EsitoInvioDatiSpesa730Service' => 'https://invioSS730pTest.sanita.finanze.it/EsitoStatoInviiWEB/EsitoInvioDatiSpesa730Service',
            'DocumentoSpesa730pPort' => 'https://invioSS730pTest.sanita.finanze.it/DocumentoSpesa730pWeb/DocumentoSpesa730pPort',
            'ReportMensilePort' => 'https://invioSS730pTest.sanita.finanze.it/ReportMensile730Web/ReportMensilePort',
            'InterrogazionePuntuale730Port' => 'https://invioSS730pTest.sanita.finanze.it/InterrogazionePuntuale730Web/InterrogazionePuntuale730Port'
        ),
    );

    /**
     * 
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password, $pin, $environment = 'sandbox') {
        $this->username = $username;
        $this->password = $password;
        $this->environment = $environment;
        $crypted = null;
        openssl_public_encrypt($pin, $crypted, self::$certificate);
        $this->pinCripted = base64_encode($crypted);
    }

    /**
     * 
     * @param string $request
     * @param filename $file
     * @return string xml
     */
    protected function sendRequest($endpoint, $request) {
        $boundary = uniqid();
        $credentials = $this->encodeUsernamePassword();

        /*
         * for multipart/related details see https://tools.ietf.org/html/rfc2387
         */
        $postData = "--{$boundary}\r\n"
                . "Content-Type: application/xop+xml; charset=UTF-8\r\n"
                . "Content-ID: <rootpart@soapui.org>\r\n\r\n"
                . $request . "\r\n"
                . "--{$boundary}--";

        $contentLength = strlen($postData);

        $host = explode('/', $endpoint);
        $headers = array(
            'Accept-Encoding: gzip,deflate',
            'Content-Type: multipart/related; type="application/xop+xml"; start="<rootpart@soapui.org>"; startinfo="text/xml"; boundary="' . $boundary . '"',
            'SOAPAction: ""',
            'MIME-Version: 1.0',
            'Content-Length: ' . $contentLength,
            'Host: ' . $host[2],
            'Authorization: Basic ' . $credentials,
            'Connection: Keep-Alive',
            'User-Agent: PHP-MTOM-CLIENT-STE80PA',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
		
		$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
		
		$this->CheckHttpCode($httpStatus, $response);
			
        curl_close($ch);
		return $response;
    }

	/**
	 *
	 * 
	 */
	private function CheckHttpCode($httpStatusCode, $response)
	{
		switch(intval($httpStatusCode))
		{
			case 200: return;
			default:
			$object = ResponseFactory::make($response, array('faultstring'));
			throw new Exception("Impossibile completare la transazione : " .$object->faultstring);
		}
	}
    /**
     * 
     * @param $file
     * @param string $codiceSSA
     * @param string $codiceAsl
     * @param string $codiceRegione
     * @param string $codiceFiscale
     * @return string
     */
    public function inviaFileMtomResponse($file, $codiceSSA, $codiceAsl, $codiceRegione, $codiceFiscale) {
        $response = $this->sendRequest(
                self::$endpoints[$this->environment]['InvioTelematicoSS730pMtomPort'], '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ejb="http://ejb.invioTelematicoSS730p.sanita.finanze.it/">
                <soapenv:Header/>
                <soapenv:Body>
                   <ejb:inviaFileMtom>
                      <nomeFileAllegato>' . basename($file) . '</nomeFileAllegato>
                      <pincodeInvianteCifrato>' . $this->pinCripted . '</pincodeInvianteCifrato>
                      <datiProprietario>
                         <codiceRegione>' . $codiceRegione . '</codiceRegione>
                         <codiceAsl>' . $codiceAsl . '</codiceAsl>
                         <codiceSSA>' . $codiceSSA . '</codiceSSA>
                         <cfProprietario>' . $codiceFiscale . '</cfProprietario>
                      </datiProprietario>
                      <documento>' . base64_encode(file_get_contents($file)) . '</documento>
                   </ejb:inviaFileMtom>
                </soapenv:Body>
             </soapenv:Envelope>', $file);

        return ResponseFactory::make($response, array(
                    'codiceEsito',
                    'dataAccoglienza',
                    'descrizioneEsito',
                    'dimensioneFileAllegato',
                    'nomeFileAllegato',
                    'protocollo',
                    'idErrore'
                ));
    }

    public function ricevutePdf($protocollo, $path = '') {
        $response = $this->sendRequest(
                self::$endpoints[$this->environment]['ricevutePdf'], '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ric="http://ricevutapdf.p730.sanita.sogei.it/">
   <soapenv:Header/>
   <soapenv:Body>
      <ric:RicevutaPdf>
         <DatiInputRichiesta>
            <pinCode>' . $this->pinCripted . '</pinCode>
            <protocollo>' . $protocollo . '</protocollo>
         </DatiInputRichiesta>
      </ric:RicevutaPdf>
   </soapenv:Body>
</soapenv:Envelope>'
        );

        $result = ResponseFactory::make($response, array(
                    'pdf',
                    'esitoChiamata'
        ));

        if ($result->esitoChiamata == 0) {
            file_put_contents($path . "esito_{$protocollo}.pdf", base64_decode($result->pdf));
            $result->pdf = realpath($path . "esito_{$protocollo}.pdf");
        }
        return $result;
    }
    
    public function DettaglioErrori730Service($protocollo,$path = '')
    {
        $response = $this->sendRequest(
                   self::$endpoints[$this->environment]['DettaglioErrori730Service'],
                    '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:det="http://dettaglioerrori.p730.sanita.sogei.it/">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <det:DettaglioErrori>
                             <DatiInputRichiesta>
                                <pinCode>'.$this->pinCripted.'</pinCode>
                                <protocollo>' . $protocollo.'</protocollo>
                             </DatiInputRichiesta>
                          </det:DettaglioErrori>
                       </soapenv:Body>
                    </soapenv:Envelope>'
                    );
                
                   

        
        $result = ResponseFactory::make($response, array(
            'codice',
            'descrizione',
            'esitoChiamata',
            'csv'
        ));
        
        if ($result->esitoChiamata == 0) {
			if(file_exists($path . "esito_{$protocollo}.zip"))
				{
					unlink($path . "esito_{$protocollo}.zip");
				}
            file_put_contents($path . "esito_{$protocollo}.zip", base64_decode($result->csv));
            $result->csv = realpath($path . "esito_{$protocollo}.zip");
        }
                    
        return $result;
    }

    public function esitoInvio() {
        
    }

    /**
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    protected function encodeUsernamePassword() {
        return base64_encode("{$this->username}:{$this->password}");
    }

}

