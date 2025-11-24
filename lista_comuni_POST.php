<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 43;
$tablename = 'normative';
include_once('include/function_page.php');

function del_comune($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM comuni WHERE id_comune='".$id."'";	
	$result = mssql_query($query, $conn);	
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}


function create()
{
	$conn = db_connect();
	$id_ope=$id;
	$comune=pulisci($_POST['comune']);	
	$cap=$_POST['cap'];
	$regione=$_POST['regione'];	
	$provincia=$_POST['provincia'];	
	$istat=$_POST['istat'];	
	$com_cod_fisc=$_POST['com_cod_fisc'];
	$codiceasl=$_POST['codiceasl'];
	$codiceregione=$_POST['codiceregione'];
	
	$query = "SELECT * FROM province Where (id=$provincia)";	
	$rs = mssql_query($query, $conn);				
	$row = mssql_fetch_assoc($rs);
	$sigla=$row['sigla'];

	
	$query = "INSERT INTO comuni (id_importazione,nazione,denominazione,cap,regione,provincia,istat,com_cod_fisc,codiceasl,codiceregione) VALUES(0,'ITA','$comune','$cap',$regione,$provincia,'$istat','$com_cod_fisc','$codiceasl','$codiceregione')";
	//echo($query);
		
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

		
	echo("ok;;1;lista_comuni.php");	
	exit();

}


function update($id)
{
	$conn = db_connect();
	$id_ope=$id;
	$comune=pulisci($_POST['comune']);	
	$cap=$_POST['cap'];
	$idregione=$_POST['regione'];	
	$provincia=$_POST['provincia'];	
	$istat=$_POST['istat'];	
	$com_cod_fisc=$_POST['com_cod_fisc'];
	$codiceasl=$_POST['codiceasl'];
	$codiceregione=$_POST['codiceregione'];
	
	$query = "SELECT * FROM province Where (id=$provincia)";	
	$rs = mssql_query($query, $conn);				
	$row = mssql_fetch_assoc($rs);
	$sigla=$row['sigla'];
	
	$query = "UPDATE comuni SET denominazione='$comune',cap='$cap',regione=$idregione,provincia=$provincia,istat='$istat',com_cod_fisc='$com_cod_fisc',codiceasl='$codiceasl',codiceregione='$codiceregione' WHERE id_comune='$id'";
	//echo($query);
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	echo("ok;;1;lista_comuni.php");	
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

			if($_REQUEST['action']=="del_comune"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_comune($_REQUEST['id_comune']);
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

