<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 68;
$tablename = 'normative';
include_once('include/function_page.php');

function create()
{
	$isroot = $_SESSION['UTENTE']->is_root();	
	$cognome = pulisci($_POST['cognome_m']);
	$nome = pulisci($_POST['nome_m']);
	$indirizzo = pulisci($_POST['indirizzo_m']);
	$comune = $_POST['med_comune_residenza'];
	$cap = $_POST['med_cap_residenza'];
	$telefono = $_POST['med_telefono'];
	$fax = $_POST['med_fax'];
	$cellulare = $_POST['med_cellulare'];
	$email = $_POST['med_email'];
	$status = $_POST['status'];
	
	$conn = db_connect();
	$query = "INSERT INTO medici_generali (nome,cognome,indirizzo,comune_id,cap,telefono,fax,cellulare,email,stato,opeins) VALUES('$nome','$cognome','$indirizzo','$comune','$cap','$telefono','$fax','$cellulare','$email','$status','".$_SESSION['UTENTE']->get_userid()."')";	
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM medici_generali WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
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
	scrivi_log ($id_ope,'medici_generali','ins','id');
	
	echo("ok;;1;lista_medici_generali.php");	
	exit();

}


function update($id)
{
	$id_ope=$id;
	$cognome = pulisci($_POST['cognome_m']);
	$nome = pulisci($_POST['nome_m']);
	$indirizzo = pulisci($_POST['indirizzo_m']);
	$comune = $_POST['med_comune_residenza'];
	$cap = $_POST['med_cap_residenza'];
	$telefono = $_POST['med_telefono'];
	$fax = $_POST['med_fax'];
	$cellulare = $_POST['med_cellulare'];
	$email = $_POST['med_email'];
	$status = $_POST['status'];
	
	
	$conn = db_connect();
	$query = "UPDATE medici_generali SET nome='$nome',cognome='$cognome',indirizzo='$indirizzo',comune_id='$comune',cap='$cap',telefono='$telefono',fax='$fax',cellulare='$cellulare',email='$email',stato='$status' WHERE id='$id'";	
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	// compila i campi di log
	scrivi_log ($id,'medici_generali','agg','id');
	
	echo("ok;;1;lista_medici_generali.php");	
	exit();

}

function del_medico($id) {
	$conn = db_connect();
	$query = "UPDATE medici_generali SET cancella='s' WHERE id='$id'";	
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	// compila i campi di log
	scrivi_log ($id,'medici_generali','agg','id');
	//echo("ok;;1;lista_medici_generali.php");
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

			case "del_medico":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else del_medico($_REQUEST['id']);
			break;
		}

		if($_REQUEST['action']=="del_medico"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_medico($_REQUEST['id']);
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

			case "del_medico":
				del_medico($_REQUEST['id']);
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

