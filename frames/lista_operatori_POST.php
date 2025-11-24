<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 11;
$tablename = 'normative';
include_once('include/function_page.php');

function create()
{
	$isroot = $_SESSION['UTENTE']->is_root();

	$nome = stripslashes($_POST['nome']);
	$descr = stripslashes($_POST['descr']);
	$usr = $_POST['username'];
	$pwd = $_POST['password'];
	$rpwd = $_POST['rpassword'];
	$gid = $_POST['gid'];
	$email = $_POST['email'];	
	$status = $_POST['status'];
	$dir_san = $_POST['dir_sanitario'];
	$dir_tec = $_POST['dir_tecnico'];

	// controllo dei campi
	if(empty($nome)) error_message("Inserire il nome e il cognome!");
	if(empty($usr)) error_message("Inserire la username!");
	if($pwd!=$rpwd) error_message("Le password non coincidono!");
	//if(!$gid) error_message("L'UTENTE deve essere associato necessariamente ad un gruppo!");

	// aggiunge i caratteri di escape
	$nome = str_replace("'","''",$nome);
	$usr = str_replace("'","''",$usr);
	//$sign = addslashes($sign);

	if(!verifica_username($usr)) error_message("Username già presente nel database. Provare con un altro.");

	$conn = db_connect();

	// cripta la pwd con la chiave corrente
	$pwd_crypt = crypt($pwd,SALE);

	$query = "INSERT INTO operatori (nome,gid,descr,username,password,email,opeins,status,dir_sanitario,dir_tecnico) VALUES('$nome','$gid','$descr','$usr','$pwd_crypt','$email','".$_SESSION['UTENTE']->get_userid()."','$status','$dir_san','$dir_tec')";

	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(uid) FROM operatori WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		die("errore MAX select");
	
	// compila i campi di log
	$id_ope=$row[0];
	scrivi_log ($id_ope,'operatori','ins','uid');
	if($dir_san=='y'){
		$san_inizio=$_POST['san_inizio'];
		$san_fine=$_POST['san_fine'];		
		$query = "INSERT INTO operatori_direttore (uid,data_inizio,data_fine,tipo) VALUES($id_ope,'$san_inizio','$san_fine','1')";
		$result = mssql_query($query, $conn);
	}
	if($dir_tec=='y'){
		$tec_inizio=$_POST['tec_inizio'];
		$tec_fine=$_POST['tec_fine'];		
		$query = "INSERT INTO operatori_direttore (uid,data_inizio,data_fine,tipo) VALUES($id_ope,'$tec_inizio','$tec_fine','2')";
		$result = mssql_query($query, $conn);
	}	

	
	echo("ok;;1;lista_operatori.php");	
	exit();

}


function update($id)
{
	$id_ope=$id;
	$nome = stripslashes($_POST['nome']);
	$descr = stripslashes($_POST['descr']);
	$gid = $_POST['gid'];
	$email = $_POST['email'];	
	$status = $_POST['status'];
	$dir_san = $_POST['dir_sanitario'];
	$dir_tec = $_POST['dir_tecnico'];

	if(empty($nome)) error_message("Inserire il nome e il cognome!");
	if(empty($email)) error_message("Inserire l'email!");

		// aggiunge i caratteri di escape
		$nome = str_replace("'","''",$nome);
		$descr = str_replace("'","''",$descr);

		$conn = db_connect();

		$query = "UPDATE operatori SET nome='$nome',gid=$gid, email='$email',descr='$descr',dir_sanitario='$dir_san',dir_tecnico='$dir_tec' WHERE uid='$id'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		// compila i campi di log
		scrivi_log ($id,'operatori','agg','uid');
		
		if($dir_san=='y'){
			$san_inizio=$_POST['san_inizio'];
			$san_fine=$_POST['san_fine'];		
			$query = "UPDATE operatori_direttore SET data_inizio='$san_inizio',data_fine='$san_fine' WHERE ((uid='$id') and (tipo='1'))";
			$result = mssql_query($query, $conn);
		}
		if($dir_tec=='y'){
			$tec_inizio=$_POST['tec_inizio'];
			$tec_fine=$_POST['tec_fine'];		
			$query = "UPDATE operatori_direttore SET data_inizio='$tec_inizio',data_fine='$tec_fine' WHERE ((uid='$id') and (tipo='2'))";
			$result = mssql_query($query, $conn);
		}	
		// scrive il log
        //scrivi_log("users.php","M","users",$id);
		echo("ok;;1;lista_operatori.php");	
		exit();

}

function confirm_del($id) {

	
}


/****************************************************************************************************************
* funzione verifica_username()																					*
*****************************************************************************************************************/
function verifica_username($usr) {

	global $tablename;

	$conn = db_connect();
	$query = "SELECT uid FROM operatori where username='".addslashes($usr)."'";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if(mssql_num_rows($rs)) return false;
	else return true;
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

