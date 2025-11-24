<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 57;
$tablename = 'regime';
include_once('include/function_page.php');

function create()
{
		$conn = db_connect();
		$idprotocollo=$_POST['idprotocollo'];
		$etichetta=stripslashes($_POST['etichetta']);
		$contenuto = stripslashes($_POST['contenuto']);
		 $etichetta=str_replace("'","''",$etichetta);
		  $contenuto=str_replace("'","''",$contenuto);
		$opeins=$_SESSION['UTENTE']->get_userid();
		$query = "INSERT INTO compilatore_contenuti (idprotocollo,etichetta,contenuto,opeins) VALUES('$idprotocollo','$etichetta','$contenuto',$opeins)";
		$result = mssql_query($query, $conn);
		if(!$result) 
		{
		echo ("no");
		exit();
		die();
		}
		
		$query = "SELECT MAX(idcontenuto) FROM compilatore_contenuti WHERE (opeins=$opeins)";
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
			
		$idcontenuto=$row[0];	
		scrivi_log ($idcontenuto,'compilatore_contenuti','ins','idcontenuto');	
	echo("ok;;1;lista_contenuti_compilatore.php");	
	exit();
}


function update($id)
{



$conn = db_connect();

	$idprotocollo=$_POST['idprotocollo'];
	$etichetta=stripslashes($_POST['etichetta']);
	$contenuto = stripslashes($_POST['contenuto']);
	$id=$_POST['id'];
	$etichetta=str_replace("'","''",$etichetta);
		  $contenuto=str_replace("'","''",$contenuto);
	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "UPDATE compilatore_contenuti SET idprotocollo=$idprotocollo,etichetta='$etichetta',contenuto='$contenuto' WHERE idcontenuto='$id'";		
	$result = mssql_query($query, $conn);
	if(!$result)
	{
	echo("no");
	die();
	exit();
	}
	// scrive il log
	scrivi_log ($id,'compilatore_contenuti','agg','idcontenuto');	

	// rilascia l'id
	unset($id);

	// scrive il log
	//scrivi_log("users.php","M","users",$id);
	echo("ok;;1;lista_contenuti_compilatore.php");
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

