<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 43;
$tablename = 'normative';
include_once('include/function_page.php');

/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_medico($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM prescrittori WHERE IdPrescrittore='".$id."'";	
	$result = mssql_query($query, $conn);	
	exit();
}

function create()
{
	$isroot = $_SESSION['UTENTE']->is_root();	
	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);
	$codice = $_POST['codice_asl'];	
	$codice_ind = $_POST['codice_induttore'];	
	$status = $_POST['status'];
	
	// aggiunge i caratteri di escape
	$nome = str_replace("'","''",$nome);
	$conn = db_connect();

	
	$query = "INSERT INTO prescrittori (NominativoPrescrittore,descr,Codice_asl,opeins,status,codice_induttore) VALUES('$nome','$descr','$codice','".$_SESSION['UTENTE']->get_userid()."','$status','$codice_ind')";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(IdPrescrittore) FROM prescrittori WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		 {
		echo ("no");
		exit();
		die();
		}
	
	// compila i campi di log
	$id_ope=$row[0];
	scrivi_log ($id_ope,'prescrittori','ins','IdPrescrittore');
	
	echo("ok;;1;lista_medici_prescrittori.php");	
	exit();

}


function update($id)
{
	$id_ope=$id;
	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);
	$codice = $_POST['codice_asl'];	
	$status = $_POST['status'];
	$codice_ind = $_POST['codice_induttore'];	
	
	// aggiunge i caratteri di escape
	$nome = str_replace("'","''",$nome);
	
	$conn = db_connect();

	$query = "UPDATE prescrittori SET NominativoPrescrittore='$nome',codice_induttore='$codice_ind',Codice_asl='$codice',descr='$descr',status='$status' WHERE IdPrescrittore='$id'";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	// compila i campi di log
	scrivi_log ($id,'prescrittori','agg','IdPrescrittore');
	
	echo("ok;;1;lista_medici_prescrittori.php");	
	exit();

}

function confirm_del($id) {

	
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

		if($_REQUEST['action']=="del_medico"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_medico($_REQUEST['id_medico']);
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

