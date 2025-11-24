<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 47;
$tablename = 'normative';
$id_area=$_REQUEST['id_area'];
include_once('include/function_page.php');

function del_area($id_area) {
	$ope = $_SESSION['UTENTE']->get_userid();
	$conn = db_connect();
	$query = "DELETE FROM area WHERE idarea=$id_area";//.$id_area;		
    echo(query);
	$result = mssql_query($query, $conn);	
	exit();
}
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');

function create()
{
	$conn = db_connect();
		$nome = pulisci($_POST['nome']);
		$descr = pulisci($_POST['descr']);
		$opeins=$_SESSION['UTENTE']->get_userid();
		$query = "INSERT INTO area (area,descrizione,opeins) VALUES('$nome','$descr',$opeins)";
		$result = mssql_query($query, $conn);
		if(!$result)
		{
				echo("no");
				exit();
				die();
				}
		$query = "SELECT MAX(idarea) FROM area WHERE (opeins=$opeins)";
		$result = mssql_query($query, $conn);
		if(!$result)
				{
				echo("no");
				exit();
				die();
				}

		if(!$row = mssql_fetch_row($result))
			{
				echo("no");
				exit();
				die();
				}
			
		$idarea=$row[0];	
		scrivi_log ($idarea,'area','ins','idarea');
		
	echo("ok;;1;lista_aree.php");	
	exit();
}


function update($id)
{



	$conn = db_connect();

	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);
	$opeins=$_SESSION['UTENTE']->get_userid();	
	$query = "UPDATE area SET area='$nome',descrizione='$descr' WHERE idarea='$id'";
	$result = mssql_query($query, $conn);
	if(!$result)
	{
				echo("no");
				exit();
				die();
				}
	// scrive il log
	scrivi_log ($id,'area','agg','idarea');	
	// rilascia l'id
	unset($id);

	// scrive il log
	//scrivi_log("users.php","M","users",$id);
	echo("ok;;1;lista_aree.php");	
	exit();

}

function confirm_del($id) {

	
}


function del($id) {

	

}
function show_list() {

	

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
            
            case "del_area":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else del_area($_REQUEST['id_area']);
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

