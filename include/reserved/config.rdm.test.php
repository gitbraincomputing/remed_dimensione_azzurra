<?php
//DEFINE DI TEST 
define("CODICE_ASL_EROGATORE", "201");
define("CODICE_SSA_EROGATORE", "888888");
define("LOGIN", "U86H32YM");
define("PASSWORD", "PQDR5R2U");
define("PINCODE", "9871318126");
define("PWD", "");
define("INVIO_EROGATO_ENDPOINT", "https://demservicetest.sanita.finanze.it/DemRicettaErogatoServicesWeb/services/demInvioErogato");
define("VISUALIZZA_EROGATO_ENDPOINT", "https://demservicetest.sanita.finanze.it/DemRicettaErogatoServicesWeb/services/demVisualizzaErogato");
define("INVIO_EROGATO_WSDL", dirname(__FILE__).DIRECTORY_SEPARATOR. "..". DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR. 'rdm_script'. DIRECTORY_SEPARATOR. 'wsdl' .DIRECTORY_SEPARATOR . 'demInvioErogato.wsdl');
define("VISUALIZZA_EROGATO_WSDL", dirname(__FILE__).DIRECTORY_SEPARATOR. "..". DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR. 'rdm_script'. DIRECTORY_SEPARATOR. 'wsdl' . DIRECTORY_SEPARATOR . 'demVisualizzaErogato.wsdl');
define("CODICE_REGIONE_EROGATORE", "150");
define("SANITEL_CERT", "cert/Test/SanitelCF.cer");
define("CA_CERT_FILE", "cert/Test/demservicetest.pem");

/*
//DEFINE DI PRODUZIONE
define("CODICE_ASL_EROGATORE", "206");
define("CODICE_SSA_EROGATORE", "740800");
define("LOGIN", "UX1K3CJK");                 
define("PASSWORD", "PH695BL5");
define("PINCODE", "1428537305");
define("PWD", "");
define("INVIO_EROGATO_ENDPOINT", "https://demservice.sanita.finanze.it/DemRicettaErogatoServicesWeb/services/demInvioErogato");
define("VISUALIZZA_EROGATO_ENDPOINT", "https://demservice.sanita.finanze.it/DemRicettaErogatoServicesWeb/services/demVisualizzaErogato ");
define("INVIO_EROGATO_WSDL", dirname(__FILE__).DIRECTORY_SEPARATOR. "..". DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR. 'rdm_script'. DIRECTORY_SEPARATOR. 'wsdl' . DIRECTORY_SEPARATOR . 'demInvioErogato.wsdl');
define("VISUALIZZA_EROGATO_WSDL", dirname(__FILE__).DIRECTORY_SEPARATOR. "..". DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR. 'rdm_script'. DIRECTORY_SEPARATOR. 'wsdl' . DIRECTORY_SEPARATOR . 'demVisualizzaErogato.wsdl');
define("CODICE_REGIONE_EROGATORE", "150");
define("SANITEL_CERT", "cert/Produzione/SanitelCF.cer");
define("CA_CERT_FILE", "cert/Produzione/demservice_sanita_finanze_it.pem");
*/

//DEFINE CODICI OPERAZIONI PRESA IN CARICO
define("OP_PRENDI_IN_CARICO", 1);
define("OP_RILASCIA", 3);
define("OP_VISUALIZZA", 4);

//DEFINE CODICI OPERAZIONI INVIO EROGATO
define("OP_EROG_TOTALE", 1);
define("OP_EROG_TOTALE_PARAM", 10); //SERVE PER CHIAMARE L'EROGAZIONE TOTALE COME PARAMETRO, LO 0 SERVE PER DISTINGUERLO DAL PARAMETRO DI OP 1 PER LA PRESA IN CARICA
define("OP_EROG_PARZIALE_PARAM", 30); //SERVE PER CHIAMARE L'EROGAZIONE PARZIALE COME PARAMETRO
define("OP_EROG_SINGOLA_PRESCR", 2);
define("OP_CHIUS_EROG_SINGOLA_RICETTA", 6);


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

//DEFINE CODICI INVIO
define("INSERIMENTO_OK", 0000);
define("INSERIMENTO_ERROR", 9999);
define("INSERIMENTO_WARNINGS", 0001);

//DEFINE CODICI ESITO
define("ESITO_OK", 0000);
define("NRE_REG_CODE_ERR", 1009);
define("NRE_RAGGRUPPAMENTO_ERR", 1011);
define("NRE_NON_PRESENTE", 1017);
define("NRE_PROGRESSIVO_ERR",1197);
define("RICETTA_NON_ESISTENTE", 5005);
define("RICETTA_SCADUTA", 5009);
define("CF_NON_CORRISPONDENTE", 5010);
define("IN_EROGAZIONE_DA_ALTRO_CENTRO", 5011);
define("OPERAZIONE_NON_CONSENTITA", 5014);

//DEFINE INVIO EROGATO
define("COD_BRANCA", 12);