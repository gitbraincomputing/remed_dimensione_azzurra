<?php
// inclusione delle classi
include_once('include/class.User.php');
//include_once('include/class.Struttura.php');
//include_once('include/class.Pagina.php');
//include_once('include/dbengine.inc.php');

// inizializza le variabili di sessione
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

if (($_SESSION['UTENTE']->get_tipoaccesso()==2) or ($_SESSION['UTENTE']->get_tipoaccesso()==3)or ($_SESSION['UTENTE']->get_tipoaccesso()==5))
{
// verificare se ha i permessi in lettura del modulo - ##da implmentare##
$curr_path =ALLEGATI_UTENTI_W;
$filename=$_REQUEST['filename'];

$handle = fopen($curr_path.$filename, "r");
		$content=fread($handle,filesize($curr_path.$filename));
		fclose($handle);	
		
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$filename");
			header("Expires: 0");
			header("Pragma: no-cache");
			echo($content);
}
else
error_message("non hai i permessi per visualizzare il file");

	


?>
