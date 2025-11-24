<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 39;
$tablename = 'regime';
include_once('include/function_page.php');

/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_regime($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM regime WHERE idregime='".$id."'";	
	$result = mssql_query($query, $conn);
	//echo("ok;;2;re_pazienti_anagrafica.php?do=show_allegati&id=".$id_paz);
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}

function create()
{
		$conn = db_connect();
		$regime = $_POST['regime'];
		$regime=pulisci($regime);
		$idnorm = $_POST['normativa'];
		
		$responsabile = $_POST['responsabile'];
		$opeins=$_SESSION['UTENTE']->get_userid();
		$query = "INSERT INTO regime (regime,idnormativa,responsabile,opeins) VALUES('$regime','$idnorm',$responsabile,$opeins)";
		$result = mssql_query($query, $conn);
		if(!$result) 
		{
		echo ("no");
		exit();
		die();
		}
		
		$query = "SELECT MAX(idregime) FROM regime WHERE (opeins=$opeins)";
		$result = mssql_query($query, $conn);
		if(!$result)
		{
		echo ("no");
		exit();
		die();
		}

		if(!$row = mssql_fetch_row($result))
			{
		echo ("no");
		exit();
		die();
		}
			
		$idregime=$row[0];	
		scrivi_log ($idregime,'regime','ins','idregime');	
	echo("ok;;1;lista_regimi.php");	
	exit();
}


function update($id)
{



$conn = db_connect();

	$regime = $_POST['regime'];
	$regime=pulisci($regime);
	$idnorm = $_POST['normativa'];
	$responsabile = $_POST['responsabile'];	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "UPDATE regime SET responsabile=$responsabile,regime='$regime',idnormativa='$idnorm' WHERE idregime='$id'";		
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	// scrive il log
	scrivi_log ($id,'regime','agg','idregime');	

	// rilascia l'id
	unset($id);

	// scrive il log
	//scrivi_log("users.php","M","users",$id);
	echo("ok;;1;lista_regimi.php");
	exit();


}

function confirm_del($id) {

	
}


function del($id) {

	

}



if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			case "create":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create();
			break;

			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_POST['id']);
			break;

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
		}

			if($_REQUEST['action']=="del_regime"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_regime($_REQUEST['id_regime']);
		}
		
		switch($do) {

			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
			break;

			case "edit":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit($_REQUEST['id']);
			break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				show_list();
			break;
		}
			html_footer();
	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>

