<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 12;
$tablename = 'normative';
include_once('include/function_page.php');

function del_operatore($id) {
	global $tablename;
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM operatori WHERE uid='".$id."'";	
	$result = mssql_query($query, $conn);	
	exit();
}

function create()
{
	$isroot = $_SESSION['UTENTE']->is_root();

	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);
	$usr = pulisci($_POST['username']);
	$pwd = pulisci($_POST['password']);
	$rpwd = pulisci($_POST['rpassword']);
	$gid = $_POST['gid'];
	$email = $_POST['email'];	
	$check_pass = $_POST['check_pass'];
	$check_pass_60 = $_POST['check_pass_60'];
	$status = $_POST['status'];
	$dir_san = $_POST['dir_sanitario'];
	$dir_tec = $_POST['dir_tecnico'];
	$gest_cart = $_POST['gest_cart'];
	$case_manager = $_POST['case_manager'];
	$key_firma = pulisci($_POST['key_firma']);
	$usabk = $_POST['usabk'];
	$bk_code = $_POST['bk_code'];
	$med_resp = $_POST['med_resp'];
	$titolo = pulisci($_POST['titolo']);
	$privacy_cartella = $_POST['privacy_cartella'];

	// controllo dei campi
	//if(empty($nome)) error_message("Inserire il nome e il cognome!");
	//if(empty($usr)) error_message("Inserire la username!");
	//if($pwd!=$rpwd) error_message("Le password non coincidono!");
	//if(!$gid) error_message("L'UTENTE deve essere associato necessariamente ad un gruppo!");

	// aggiunge i caratteri di escape
	//$nome = str_replace("'","''",$nome);
	//$usr = str_replace("'","''",$usr);
	//$sign = addslashes($sign);

	//if(!verifica_username($usr)) error_message("Username gi� presente nel database. Provare con un altro.");

	$conn = db_connect();

	// cripta la pwd con la chiave corrente
	$pwd_crypt = crypt($pwd,SALE);
	if($check_pass_60=='y') $datadioggi = date('d/m/Y');
	else $datadioggi="";
	$query = "INSERT INTO operatori (med_resp,titolo,bk_code,usa_firma,nome,gid,descr,username,password,check_pass,check_pass_60,data_pass_60,email,opeins,status,dir_sanitario,dir_tecnico,case_manager,gestore_cartella,key_firma,privacy_cartella) VALUES('$med_resp','$titolo','$bk_code',$usabk,'$nome','$gid','$descr','$usr','$pwd_crypt','$check_pass','$check_pass_60','$datadioggi','$email','".$_SESSION['UTENTE']->get_userid()."','$status','$dir_san','$dir_tec','$case_manager','$gest_cart','$key_firma',$privacy_cartella)";

	$result = mssql_query($query, $conn);
	if(!$result)
	{		
				echo("no");
				exit();
				die();
	}

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(uid) FROM operatori WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
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
	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);
	$gid = $_POST['gid'];
	$email = $_POST['email'];	
	$status = $_POST['status'];
	$usabk = $_POST['usabk'];
	$dir_san = $_POST['dir_sanitario'];
	$dir_tec = $_POST['dir_tecnico'];
	$case_manager = $_POST['case_manager'];
	$gest_cart = $_POST['gest_cart'];
	$pwd = pulisci($_POST['password']);
	$rpwd = pulisci($_POST['rpassword']);
	$check_pass = $_POST['check_pass'];
	$check_pass_60 = $_POST['check_pass_60'];
	$key_firma = pulisci($_POST['key_firma']);
	$bk_code = $_POST['bk_code'];
	$titolo = pulisci($_POST['titolo']);
	$med_resp = $_POST['med_resp'];	
	$user= $_POST['usr'];
	$privacy_cartella = $_POST['privacy_cartella'];	
	
	if ($pwd!="") $pwd_crypt = crypt($pwd,SALE);

	if(empty($nome)) error_message("Inserire il nome e il cognome!");
	//if(empty($email)) error_message("Inserire l'email!");

		// aggiunge i caratteri di escape
		//$nome = str_replace("'","''",$nome);
		//$descr = str_replace("'","''",$descr);

		$conn = db_connect();
        if($check_pass_60=='y') $datadioggi = date('d/m/Y');
	    else $datadioggi='';
		$query = "UPDATE operatori SET med_resp='$med_resp',check_pass='$check_pass',check_pass_60='$check_pass_60',data_pass_60='$datadioggi',titolo='$titolo',bk_code='$bk_code',usa_firma='$usabk',nome='$nome',gid=$gid, email='$email',key_firma='$key_firma',descr='$descr',dir_sanitario='$dir_san',dir_tecnico='$dir_tec',case_manager='$case_manager',gestore_cartella='$gest_cart',status='$status',privacy_cartella=$privacy_cartella";
		if ($pwd!="") $query .=",password='$pwd_crypt' ";
		if (($_SESSION['UTENTE']->is_root())and($user!="")) $query .=",username='$user' ";		
		$query .="WHERE uid='$id'";	
		$result = mssql_query($query, $conn);
		if(!$result)	{		
				echo("no");
				exit();
				die();
		}

		// compila i campi di log
		scrivi_log ($id,'operatori','agg','uid');
		
		if($dir_san=='y'){
			$san_inizio=$_POST['san_inizio'];
			$san_fine=$_POST['san_fine'];
			$query="SELECT * FROM operatori_direttore WHERE (uid='$id')";
			$result = mssql_query($query, $conn);
			if (mssql_num_rows($result)>0)
				$query = "UPDATE operatori_direttore SET data_inizio='$san_inizio',data_fine='$san_fine' WHERE ((uid='$id') and (tipo='1'))";
			else
				$query = "INSERT INTO operatori_direttore (uid,data_inizio,data_fine,tipo) VALUES($id,'$san_inizio','$san_fine','1')";	
			mssql_free_result($result);
			$result = mssql_query($query, $conn);
		}
		if($dir_tec=='y'){
			$tec_inizio=$_POST['tec_inizio'];
			$tec_fine=$_POST['tec_fine'];
			$query="SELECT * FROM operatori_direttore WHERE (uid='$id')";
			$result = mssql_query($query, $conn);
			if (mssql_num_rows($result)>0)
				$query = "UPDATE operatori_direttore SET data_inizio='$tec_inizio',data_fine='$tec_fine' WHERE ((uid='$id') and (tipo='2'))";
			else
				$query = "INSERT INTO operatori_direttore (uid,data_inizio,data_fine,tipo) VALUES($id_ope,'$tec_inizio','$tec_fine','2')";
			mssql_free_result($result);
			$result = mssql_query($query, $conn);			
		}
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
	if(!$rs)
	{		
				echo("no");
				exit();
				die();
	}

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

		if($_REQUEST['action']=="del_operatore"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_operatore($_REQUEST['id_operatore']);
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

