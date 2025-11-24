<?PHP

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$test = false;
$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));

if($test){
    //credenziali di test
    $username_sogei = 'UYBP8F4G';
    $password_sogei = 'PXXCLMBE';
    $pin_sogei = '4143408326';
    $env = 'sandbox';
    
    $codiceSSA = 888888;
    $codiceAsl = 101;
    $codiceRegione = 120;
    $codiceFiscale = 'CCSRMO77A09H501E';
    
    $action = 'invia';
    $protocollo = '17041811075441097';
    $nome_file = 'TestNeap.zip';
    $debug = true;
}
else {
	// in produzione carichiamo le configurazioni dal file config.rdm.php generale
	require_once($productionConfig);
    //credenziali di produzione
    $username_sogei = LOGIN;
    $password_sogei = PASSWORD;
    $pin_sogei = PINCODE;
    $env = 'production';
    $codiceSSA = CODICE_SSA_EROGATORE;
    $codiceAsl = CODICE_ASL_EROGATORE;
    $codiceRegione = CODICEREGIONE;
    $codiceFiscale = CODICEFISCALE;
    
    $action = '';
    $protocollo = '';
    $nome_file = '';
    $debug = false;
}

define('USERNAME_SOGEI',$username_sogei);
define('PASSWORD_SOGEI',$password_sogei);
define('PIN_SOGEI',$pin_sogei);
define('ENVIRONMENT_SOGEI',$env); //sandbox / production
define('CODICE_SSA_SOGEI',$codiceSSA);
define('CODICE_ASL_SOGEI',$codiceAsl);
define('CODICE_REGIONE_SOGEI',$codiceRegione);
define('CODICE_FISCALE_SOGEI',$codiceFiscale); //sandbox / production
//die($codiceFiscale);
//define('BASE_PATH', dirname(__FILE__));
//$percorso_win='C:'.DIRECTORY_SEPARATOR.'ThinclientRemed'.DIRECTORY_SEPARATOR.'XML'.DIRECTORY_SEPARATOR.'xml730';
$percorso_win='\\\\192.168.210.203\\ThinclientRemed\\xml\\xml730';
define('BASE_PATH',$percorso_win);
define('PATH_SORGENTE', BASE_PATH.DIRECTORY_SEPARATOR.'DaInviare'.DIRECTORY_SEPARATOR);
define('PATH_RICEVUTE', BASE_PATH.DIRECTORY_SEPARATOR.'Ricevute'.DIRECTORY_SEPARATOR);
define('PATH_RICEVUTE_ERRORI', BASE_PATH.DIRECTORY_SEPARATOR.'Errori'.DIRECTORY_SEPARATOR);


if(isset($_GET['action']) && $_GET['action'] != ''){
    $action = $_GET['action'];
}
if(isset($_GET['protocollo']) && $_GET['protocollo'] != ''){
    $protocollo = $_GET['protocollo'];
}
if(isset($_GET['nomefile']) && $_GET['nomefile'] != ''){
    $nome_file = $_GET['nomefile'];
}
if(isset($_GET['debug']) && $_GET['debug'] != ''){
    $debug = true;
}