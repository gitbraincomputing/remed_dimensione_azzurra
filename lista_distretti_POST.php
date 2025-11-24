<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 71;
$tablename = 'distretti';
include_once('include/function_page.php');

function create()
{
	$conn = db_connect();
		$CodiceASL = pulisci($_POST['CodiceASL']);
		$DescrizioneAsl = pulisci($_POST['DescrizioneAsl']);
		$CodiceDistretto = pulisci($_POST['CodiceDistretto']);
		$DescrizioneDistretto = pulisci($_POST['DescrizioneDistretto']);
        
		$opeins=$_SESSION['UTENTE']->get_userid();
		$query = "INSERT INTO distretti (CodiceASL,DescrizioneAsl,CodiceDistretto,DescrizioneDistretto) VALUES('$CodiceASL','$DescrizioneAsl','$CodiceDistretto','$DescrizioneDistretto')";
		//echo($query);
        $result = mssql_query($query, $conn);
		if(!$result) 
			{
		echo ("no");
		exit();
		die();
		}
	echo("ok;;1;lista_distretti.php");	
	exit();
}


function update($id)
{
	$conn = db_connect();
    $CodiceASL = pulisci($_POST['CodiceASL']);
    $DescrizioneAsl = pulisci($_POST['DescrizioneAsl']);
    $CodiceDistretto = pulisci($_POST['CodiceDistretto']);
    $DescrizioneDistretto = pulisci($_POST['DescrizioneDistretto']);
	if(isset($_POST['obsoleto']))
		$obsoleto=-1;
		else
		$obsoleto=0;
	
	$opeins=$_SESSION['UTENTE']->get_userid();	
	$query = "UPDATE distretti SET obsoleto='$obsoleto',CodiceASL='$CodiceASL',DescrizioneAsl='$DescrizioneAsl',CodiceDistretto='$CodiceDistretto',DescrizioneDistretto='$DescrizioneDistretto' WHERE IdDistretto=$id";
    $result = mssql_query($query, $conn);
	if(!$result)
		{
		echo ("no");
		exit();
		die();
		}
	// scrive il log
	//scrivi_log ($id,'distretti','agg','IdDistretto');	
	// rilascia l'id
	unset($id);

	// scrive il log
	//scrivi_log("users.php","M","users",$id);
	echo("ok;;1;lista_distretti.php");	
	exit();

}

function confirm_del($id) {

	
}
function del($id) {

}


function del_distretto($IdDistretto) {
    //echo($IdDistretto);exit;
    $ope = $_SESSION['UTENTE']->get_userid();
	$conn = db_connect();
	$query = "DELETE FROM distretti WHERE IdDistretto='".$IdDistretto."'";	
    //echo($query);exit;
	$result = mssql_query($query, $conn);
	exit();
	// compila i campi di log
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
        
        if($_REQUEST['action']=="del_distretto"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_distretto($_REQUEST['IdDistretto']);
                    
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

