<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

if (
    isset($_POST['orderId']) && !empty($_POST['orderId']) &&
    isset($_POST['orderStatus']) && !empty($_POST['orderStatus']) &&
    isset($_GET['usephp']) && $_GET['usephp'] == '7'
) {

    $productionConfig = join(DIRECTORY_SEPARATOR, array('C:', 'web', 'Republic', 'include', 'reserved', 'config.rdm.php'));
    require_once($productionConfig);

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

    //$url = 'https://api-collaudo.cdp-sanita-coll.soresa.it:443/api/v4/process-apis/order-management/orders';
    $url = 'https://api-sanita.cdp-sanita.soresa.it/nghcupapi/api/v4/process-apis/order-management/orders';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");

    // Costruzione dei dati JSON corretta
    $postData = array(
        array(
            "orderId" => $_POST['orderId'],
            "orderStatus" => strtoupper($_POST['orderStatus']),
            "reasonId" => 0
        )
    );
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));

    // Aggiunta degli header
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'X-apigw-route-app: nghcupapi',
        'X-apigw-route-env: cert',
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json' // Aggiornato il tipo di contenuto
    )); // Aggiunta parentesi di chiusura

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Errore cURL: ' . curl_error($curl);
    }

    curl_close($curl);
    $response = json_decode($response, false);
    echo $response->_messages[0]->text;
} else {
    echo "Dati mancanti o non validi";
}
?>
