<?php

//DATI TEST FORNITI DALLA REGIONE CAMPANIA

return array(
    'username' => 'PROVAX00X00X000Y',			// Cod. Fiscale Direttore Sanitario registrato al FSE (PROVAX00X00X000Y medico test per regione Campania)
    'password' => 'Salve123',					// Password Direttore Sanitario registrato al FSE
	// Password Direttore Sanitario registrato al FSE codificata in BASE64 (RFC 1521)
    'pinCode' => 'LsQiYtf7FcpMYVKvf+51V6t1BSUk+E/dGOB2vmwNl0DhirZ8QzvTI2Ay04p6+t+eH+DjzkJpXrlEEZvKRz6wKVNOt7uYSQUYKBIFcbcEQJnqT7zTgtz7jV3BK+QaEphfKRsOP1Iejv+vKvJ/3te2xNMHPkNYZIAjxEQHftw9Swk=',
	'identificativoOrganizzazione' => '150'		// identificavo ASL Campania
);
/*

include_once('../../include/dbengine.inc.php');

$conn = db_connect();
$query = "SELECT codice_fiscale FROM operatori WHERE cancella = 'n' AND dir_sanitario = 'y'";	
$result = mssql_query($query, $conn);
if(!$result) error_message(mssql_error());

$row = mssql_fetch_assoc($result);
$cod_fisc_ds = $row['codice_fiscale'];
*/
	
/* return array(
    'username' => 'BRTSRG73T27F839I',			// Cod. Fiscale Direttore Sanitario registrato al FSE
    'password' => 'Daviduccio2',				// Password Direttore Sanitario registrato al FSE
	'pinCode' =>  'FO76oBfsGSJJkM9l1EZS+IzXW7fjS8T1JoH+2qRK1yml8EAGJGckSvP5fgd0R1cXXBC+pVnSfw0ZESPhxw8X6kCd1QSgzz4dsLGziUEJLkAKifcfWWra3s80HQVgkm56Cvc3x+lNj2RAM2B15HLiNE9cMFjlWA4Qx1QguPiILcY=',
    'identificativoOrganizzazione' => '150'		// identificavo ASL Campania
);
 */

// --------------------------------------
// password DS centro Linea Medica
// DE SENA PASQUA
// Texwiller2023%
// --------------------------------------

// openssl rsautl -encrypt -in pin.txt -out pin.enc -inkey Sanitel.cer -certin -pkcs
// openssl enc -base64 -A -in pin.enc -out pin64.enc