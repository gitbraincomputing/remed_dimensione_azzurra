<?php

define('SERVER_NAME','VMI1992347');
define('DB_NAME','remed');
define('DSN','remed');
define('DB_USER','remed_user');
define('DB_PASSWORD','remeduserpw');
define('SALE','4aalidoS');
define('CHIAVE','token');
define('IP_ADDR','192.168.210.203');
define('FTP_USR','ftp_remed');
define('FTP_PWD','antonio1esposito!');
define('LUOGO_CENTRO','Casalnuovo');
define('DELETE_DIFF',10);
define('AVVISO_SCADENZA',15);
//visualizza data insert
define('DATA_INSERT',1);

define('CMS','ReMed');
define('VERSIONE','Re.Med. - version 2.0.1');
define('MAXPAGES',13);
define('DEFAULT_NXPAGINA',100);
define('FAXSERVER','info@braincomputing.com');
define('FAXSERVER_MIT','info@braincomputing.com');
define('MAX',100000);

define('IP_ADDR_REC_PASS','http://217.72.102.141/remed/'); //INDIRIZZO INTERNO ALLA LAN
define('IP_ADDR_REC_PASS_EST','http://remed.braincomputing.com/remed/'); // INDIRIZZO ESTERNO ALLA LAN

define('INDIRIZZO_HEADERS_FROM','From:remed@braincomputing.com');// Identifica il mittente della mail 
define('INDIRIZZO_HEADERS_CC','Cc:remed@braincomputing.com'); // Indirizzo amministratore sistema
define('G_REST_PASS',60);// numero di giorni passati per resettare password

define('ATTIVA_CRON_PIANIFICAZIONE_MODULI',true);// se settato su true il cron invia le email

define('ATTIVA_CRON_SGQ',true);// se settato su true il cron invia le email
define('VALORE_GIORNI_ALERT_SGQ',10);// giorni alert per moduli sgq in scadenza non compilati, utilizzati per inoltro mail


//nuovi define per REMED
//define('DELETE_DIFF',10);
//define('ALLEGATI_UTENTI','/ftp_remed/allegati_utenti');
define('ALLEGATI_UTENTI','allegati_utenti');
define('ALLEGATI_UTENTI_W','c:\\web\\Republic\\allegati_utenti\\');
define('ALLEGATI_UTENTI_IMP','allegati_utenti\amministrativi');
define('ALLEGATI_UTENTI_IMP_W','c:\\web\\Republic\\allegati_utenti\\amministrativi\\');
define('ALLEGATI_UTENTI_SAN','allegati_utenti\sanitari');
define('ALLEGATI_UTENTI_SAN_W','c:\\web\\Republic\\allegati_utenti\\sanitari\\');
define('ALLEGATI_MODULI_SANITARI','/remedftp/Republic/allegati_moduli_sanitari');
define('ALLEGATI_MODELLI_SGQ','allegati_sgq\modelli');
define('ALLEGATI_MODELLI_SGQ_W','c:\\web\\Republic\\allegati_sgq\\modelli\\');
define('FOTO_PAZIENTI','/remedftp/Republic/foto_pazienti');
define('IMG_PAZIENTI','foto_pazienti');
define('WEB_PATH','http://remed.braincomputing.com');
define('MODELLI_WORD','modelli_word');
//define('MODELLI_WORD','modelli_word');
define('MODELLI_WORD_PATH','c:\\web\\Republic\\modelli_word\\');
define('MODELLI_WORD_DEST_PATH','c:\\web\\Republic\\modelli_word\\stampe\\');
define('ALLEGATI_PDF_DEST_PATH','c:\\web\\Republic\\modelli_word\\allegato\\');
//fine

//parametrizzazione
define('TIPOLOGIA_MEDICO','1');
define('DATA_VIS_ASL','0');
define('ORA_VIS_ASL','0');
//fine

define('ITALIAN_DATE_REGEX','(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$');

?>