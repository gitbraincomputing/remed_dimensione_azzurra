<?php
//DEFINE DI TEST 
/*
define("CODICE_ASL_EROGATORE", "101");
define("CODICE_SSA_EROGATORE", "888888");
define("LOGIN", "UXN6VMDD");
define("PASSWORD", "P2SPDVF4");
define("PINCODE", "1426073406");
define("PWD", "");
define("INVIO_EROGATO_ENDPOINT", "https://demservicetest.sanita.finanze.it/DemRicettaErogatoServicesWeb/services/demInvioErogato");
define("VISUALIZZA_EROGATO_ENDPOINT", "https://demservicetest.sanita.finanze.it/DemRicettaErogatoServicesWeb/services/demVisualizzaErogato");
define("INVIO_EROGATO_WSDL", dirname(__FILE__).DIRECTORY_SEPARATOR. "..". DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR. 'rdm_script'. DIRECTORY_SEPARATOR. 'wsdl' .DIRECTORY_SEPARATOR . 'demInvioErogato.wsdl');
define("VISUALIZZA_EROGATO_WSDL", dirname(__FILE__).DIRECTORY_SEPARATOR. "..". DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR. 'rdm_script'. DIRECTORY_SEPARATOR. 'wsdl' . DIRECTORY_SEPARATOR . 'demVisualizzaErogato.wsdl');
define("CODICE_REGIONE_EROGATORE", "020");
define("SANITEL_CERT", "cert/Test/SanitelCF.cer");
define("CA_CERT_FILE", "cert/Test/demservicetest.pem");
*/

//DEFINE DI PRODUZIONE
/*define("CODICE_ASL_EROGATORE", "206");
define("CODICE_SSA_EROGATORE", "771200");
define("LOGIN", "UDPDPF7X");
define("PASSWORD", "Flumen11@7");
define("PINCODE", "9325869474");
define("PWD", "");
define("CODICE_REGIONE_EROGATORE", "150");
 *  */

if (isset($_POST['pinCode']))
{
    define("CODICE_ASL_EROGATORE", $_POST['codiceAslErogatore']);
    define("CODICE_SSA_EROGATORE", $_POST['codiceSsaErogatore']);
    define("LOGIN", $_POST['login']);
    define("PASSWORD", $_POST['password']);
    define("PINCODE", $_POST['pinCode']);
    define("PWD", "");
    define("CODICE_REGIONE_EROGATORE", $_POST['codiceRegioneErogatore']);
	
	
	

}
elseif (isset($_REQUEST['pinCode']))
{
    define("CODICE_ASL_EROGATORE", $_REQUEST['codiceAslErogatore']);
    define("CODICE_SSA_EROGATORE", $_REQUEST['codiceSsaErogatore']);
    define("LOGIN", $_REQUEST['login']);
    define("PASSWORD", $_REQUEST['password']);
    define("PINCODE", $_REQUEST['pinCode']);
    define("PWD", "");
    define("CODICE_REGIONE_EROGATORE", $_REQUEST['codiceRegioneErogatore']);
	
	/*
echo(CODICE_ASL_EROGATORE."<BR />");
echo(CODICE_SSA_EROGATORE."<BR />");
echo(LOGIN."<BR />");
echo(PASSWORD."<BR />");
echo(PINCODE."<BR />");
echo(PWD."<BR />");
echo(CODICE_REGIONE_EROGATORE."<BR />");*/

}
else
	die("errore");



define("INVIO_EROGATO_ENDPOINT", "https://demservicetest.sanita.finanze.it/DemRicettaErogatoServicesWeb/services/demInvioErogato");
define("VISUALIZZA_EROGATO_ENDPOINT", "https://demservicetest.sanita.finanze.it/DemRicettaErogatoServicesWeb/services/demVisualizzaErogato");

define("INVIO_EROGATO_WSDL", 'wsdl' . DIRECTORY_SEPARATOR . 'demInvioErogato.wsdl');
define("VISUALIZZA_EROGATO_WSDL",  'wsdl' . DIRECTORY_SEPARATOR . 'demVisualizzaErogato.wsdl');

define("SANITEL_CERT", "cert/Test/SanitelCF.cer");
define("CA_CERT_FILE", "cert/Test/demservicetest.pem");


//DEFINE CODICI OPERAZIONI PRESA IN CARICO
define("OP_PRENDI_IN_CARICO", 1);
define("OP_RILASCIA", 3);
define("OP_VISUALIZZA", 4);

//DEFINE CODICI OPERAZIONI INVIO EROGATO
define("OP_EROG_TOTALE", 1);
define("OP_EROG_TOTALE_PARAM", 10); //SERVE PER CHIAMARE L'EROGAZIONE TOTALE COME PARAMETRO, LO 0 SERVE PER DISTINGUERLO DAL PARAMETRO DI OP 1 PER LA PRESA IN CARICA
define("OP_EROG_SINGOLA_PRESCR", 2);
define("OP_CHIUS_EROG_SINGOLA_RICETTA", 6);
define("OP_EROG_PARZIALE", 3);


//DEFINE STATO RDM
define("STATO_DA_EROGARE", 3);
define("ANNULLATA_DA_PRESCRITTORE", 4);
define("STATO_IN_EROGAZIONE", 5);
define("STATO_EROGATA", 8);

//DEFINE CODICE RICETTA RILASCIATA
define("RILASCIATA", 1);

//DEFINE CODICI VISUALIZZAZIONE
define("VISUAL_OK", 0000);
define("VISUAL_ERROR", 9999);

//DEFINE CODICI ESITO
define("ESITO_OK", 0000);
define("PINCODE_ASSENTE", 1001);
define("PINOCODE_ERRATO", 1002);
define("UTENTE_NON_AUTORIZZATO", 1003);
define("CF_MEDICO1_NON_IN_ATTIVITA", 1004);
define("CF_MEDICO2_NON_IN_ATTIVITA", 1005);
define("REGIONE_NON_ABILITATA", 1006);
define("CF_MEDICO1_NON_COINCIDENTE", 1007);
define("NRE_NON_COICIDENTE_CON_REGIONE", 1008);
define("NRE_REG_CODE_ERR", 1009);
define("NRE_LUNGHEZZA_ERR", 1010);
define("NRE_RAGGRUPPAMENTO_ERR", 1011);
define("CF_ASSISTITO_NON_INSERITO", 1012);
define("NRE_NON_IN_LOTTO", 1013);
define("CODICE_ASL_NON_COINCIDENTE_CON_LOTTO", 1014);
define("CF_MEDICO_NON_COINCIDENTE_CON_LOTTO", 1015);
define("NRE_GIA_USATO", 1016);
define("NRE_NON_PRESENTE", 1017);
define("COD_REG_MEDICO_OBBLIGATORIO", 1018);
define("COD_ASL_MEDICO_OBBLIGATORIO", 1019);
define("COD_ASL_MEDICO_NON_VALIDO", 1020);
define("COD_STRUTTURA_MED_NON_VALIDO", 1021);
define("COD_SPECIALIZZAZIONE_NON_VALIDO", 1022);
define("CF_MEDICO_ERR", 1023);
define("CHECK_DIGIT_CF_MEDICO_ERR", 1024);
define("COD_SPEC_OBBLIGATORIO", 1025);
define("COD_REG_MED_NON_VALIDO", 1026);
define("CF_MEDICO2_ERR", 1027);
define("CHECK_DIGIT_CF_MEDICO2_ERR", 1028);
define("NRE_NON_PREVISTO", 1029);
define("COD_SPEC_NON_COINCIDENTE_CON_LOTTO", 1030);
define("VALORE_TIPO_RICETTA_NON_AMMESSO", 1032);
define("INSERIRE_CF_STP_ENI", 1033);
define("CF_ASSISTITO_NON_DECIFRABILE", 1034);
define("CF_ASSISTITO_NON_CIFRATO", 1035);
define("CF_STP_ENI_ERR", 1036);
define("CF_NON_ASSISTITO_SSN", 1037);
define("CF_ASSISTITO_NON_IMPOSTATO", 1038);
define("STP_NON_IMPOSTATO", 1039);
define("COD_STRUTTURA_NON_COINCIDENTE_CON_LOTTO", 1031);
define("NRE_PROGRESSIVO_ERR",1197);
define("RICETTA_NON_ESISTENTE", 5005);
define("RICETTA_SCADUTA", 5009);
define("CF_NON_CORRISPONDENTE", 5010);
define("IN_EROGAZIONE_DA_ALTRO_CENTRO", 5011);
define("OPERAZIONE_NON_CONSENTITA", 5014);
define("RICETTA_ANNULLATA_DAL_MEDICO",5162);

//DEFINE INVIO EROGATO
define("COD_BRANCA", 12);
/*
include_once('../include/dbengine.inc.php');
$conn = db_connect();
$selectTS = "SELECT top 1 login_ts,psw_ts,pincode_ts, codicestruttura_ts, codiceasl_ts from struttura where idstruttura=1";
						
			$rsTS = mssql_query($selectTS,$conn);
			$cod_asl_rif="";
			while($rowTS =  mssql_fetch_assoc($rsTS))
			{
				$cod_asl_rif_ts = $rowTS['codiceasl_ts'];
				$codice_struttura_ts = $rowTS['codicestruttura_ts'];
				$login_ts = $rowTS['login_ts'];
				$psw_ts = $rowTS['psw_ts'];
				$pincode_ts=$rowTS['pincode_ts'];
				
				
				//echo($cod_asl_rif);
				
			}
define("CODICE_ASL_EROGATORE", $cod_asl_rif_ts);
define("CODICE_SSA_EROGATORE", $codice_struttura_ts);
define("LOGIN", $login_ts);
define("PASSWORD",$psw_ts); 
define("PINCODE", $pincode_ts);*/
