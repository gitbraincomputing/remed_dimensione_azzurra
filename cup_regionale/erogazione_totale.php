<?php


ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);


if(	isset($_GET['num_ricetta']) 	&& !empty($_GET['num_ricetta']) 	&&
	isset($_GET['admissionStatus']) && !empty($_GET['admissionStatus']) &&
	isset($_GET['reasonId'])		&&
    isset($_GET['usephp']) 			&& $_GET['usephp']=='7'
)
{
	
    //$url = 'https://auth-sanita.cdp-sanita-coll.soresa.it/auth/realms/collaudo/protocol/openid-connect/token';		// collaudo
    $url = 'https://auth-sanita.cdp-sanita.soresa.it/auth/realms/prod/protocol/openid-connect/token';
    $data = array(
        'client_id' => 'STS_720600',
        'client_secret' => '9f90f92c-6654-4424-a5d1-2ee1753a76f4',
        'grant_type' => 'client_credentials',
        'scope' => 'openid'
    );
	//'client_secret' => 'Mc3KBUXiczT5VBTwozLdOAVVFc010BS4',		// collaudo

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response);
    $access_token = $response->access_token;	
	
	
	
	// DA_EROGARE, ELIMINATA, EROGATA, NON_EROGATA, PRESCRITTA
		
	/* Categoria delle motivazioni
	0 - Cancellazione Prescrizione
	1 - Cancellazione prenotazione
	2 - Cancellazione di sistema
	3 - Spostamento prenotazione
	4 - Cancellazione automatica prescrizione
	5 - Riprenotazione SISS
	6 - Indisponibilita' struttura
	7 - Cancellazione della spedizione del referto
	8 - Accettazione diretta
	9 - Esclusione appuntamenti sanzionabili
	10 - Indisponibilità numero di telefono
	11 - Motivazione forzatura
	12 - Motivazione prelievo
	13 - Motivazione disattivazione sospensione */
		
	$data = array(
		array(
			"admissionIdentification" => array(
				"identifiedBy" => "NUMERO_RICETTA",
				"identifier" => $_GET['num_ricetta']
			),
			"admissionStatus" => strtoupper($_GET['admissionStatus']),
			"reasonId" => $_GET['reasonId']
		)
	);

	$postData = json_encode($data);

	//$url = 'https://api-collaudo.cdp-sanita-coll.soresa.it:443/api/v4/process-apis/admission-management/admissions';
	$url = 'https://api-sanita.cdp-sanita.soresa.it/nghcupapi/api/v4/process-apis/admission-management/admissions';

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'X-apigw-route-app: nghcupapi',
        'X-apigw-route-env: cert',
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json' // Aggiornato il tipo di contenuto
	));

	$response = curl_exec($curl);

	if(curl_errno($curl)){
		echo 'Errore cURL: ' . curl_error($curl);
	}

	curl_close($curl);
	
	//$response = json_decode($response, false);
    //echo $response->_messages[0]->text;
	
	
	$data = json_decode($response, true);

	// Verifica se il campo "_messages" è un array
	if (isset($data['_messages']) && is_array($data['_messages'])) {
		foreach ($data['_messages'] as &$message) {
			// Verifica se il campo "meta" è un array
			if (isset($message['meta']) && is_array($message['meta'])) {
				// Concatena i valori dell'indice "text" con tutti i valori presenti nell'indice "!meta"
				$text = $message['text'] . "<br>";
				foreach ($message['meta'] as $meta_value) {
					$text .= '<br>- ' . $meta_value;
				}
				// Aggiorna il campo "text" con il nuovo valore
				$message['text'] = $text;
			}
		}
	}

	// Converte di nuovo in JSON
	//$new_json_data = json_encode($data, JSON_PRETTY_PRINT);

	
	
	
	
	// ---------------------------
	// se la chiamata arriva da ThinClient vengono passati anche questi dati, per cui eseguo il redirect allo script per la comunicazione al sistemaTS
	// ---------------------------
	// 04/04/2024 -> commentata per successiva decisione di richiamare il file con un tasto apposito in TC
	// ---------------------------
	
/* 	if( !empty($_GET['cf']) && !empty($_GET['idImp']) && !empty($_GET['type']) ) 
	{		
		header("Location: http://192.168.2.253/remeddia/rdm_script/index.php?nre=" . $_GET['num_ricetta'] . "&cf=" . $_GET['cf'] . "&idImp=" . $_GET['idImp'] . "&debug=" . $_GET['debug'] . "&type=10&reponse_cup=" . $message['text']);
	}
	else 	// altrimenti sto chiamando il file singolo e visualizzo la response del cup
	{
		echo $message['text'];
	} */
	
	
	
	echo $message['text'];
	
} else { echo "Dati mancanti o non validi"; }