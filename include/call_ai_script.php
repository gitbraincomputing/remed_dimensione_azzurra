<?php

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,"https://firmabc.wbb.it/send_prompt_ai.php");
curl_setopt($curl, CURLOPT_POST, true);				
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_POSTFIELDS, array(
	'usephp' => 7,
	'user_input' => $_POST['user_input'],
	'id_paziente' => $_POST['id_paziente'],
	'id_modulo_versione' => $_POST['id_modulo_versione'],
	'id_modulo_padre' => $_POST['id_modulo_padre'],
	'id_cartella' => $_POST['id_cartella']
));

$response = curl_exec($curl);
$error = curl_error($curl);

curl_close($curl);

echo json_decode($response, true);
