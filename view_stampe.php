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

$id_menu=$id_permess=0;
$relative_path =MODELLI_WORD_DEST_PATH;

$conn=db_connect();
$idreport=$_REQUEST['idreport'];
$id_pdf=$_REQUEST['id_pdf'];

$query="SELECT * from re_log_stampa_moduli WHERE id=$idreport";

$rt = mssql_query($query, $conn);
if(!$rt) error_message(mysql_error());
if(mssql_num_rows($rt) > 0)	{
	$total_row = mssql_fetch_assoc($rt);
	$filename = $total_row['nome_file'];
	$idcartella = $total_row['idcartella'];
	$idpaziente = $total_row['id_utente'];
	$id_modulo_padre = $total_row['id_modulo'];
	$curr_path = $relative_path;
	if($id_pdf==0){
		$handle = fopen($curr_path.$filename, "r");
		$content=fread($handle,filesize($curr_path.$filename));
		fclose($handle);	
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$filename");
		header("Expires: 0");
		header("Pragma: no-cache");
		echo($content);	
	}
	if(($id_pdf==1)or($id_pdf==2)){
		if($id_pdf==1){			
			header("location:pdfclass/stampa_cartella.php?file=$filename&idcart=$idcartella&user=".$_SESSION['UTENTE']->get_userid());
		}
		if($id_pdf==2){
			header("location:pdfclass/stampa_istanza.php?file=$filename&user=".$_SESSION['UTENTE']->get_userid()."&idpaz=$idpaziente&idcart=$idcartella&idmod=$id_modulo_padre");
		}
	}
}
	
		

?>
