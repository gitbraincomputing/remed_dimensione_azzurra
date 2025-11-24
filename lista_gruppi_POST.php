<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 11;
$tablename = 'regime';
include_once('include/function_page.php');

function del_gruppo($id)
{
	global $tablename;
	$ope = $_SESSION['UTENTE']->get_userid();

	$conn = db_connect();
	$query = "DELETE FROM gruppi WHERE gid='" . $id . "'";
	$result = mssql_query($query, $conn);
	exit();
}

function create()
{

	$conn = db_connect();

	$arr_permessi = array();
	$query = "SELECT * from menu where status='1' order by nome ";
	$rs = mssql_query($query, $conn);

	if (!$rs) {
		echo ("no");
		exit();
		die();
	}
	while ($row = mssql_fetch_assoc($rs)) {
		$id = $row['id'];
		if ($_POST['sel_' . $id] == true) $permessi .= $id . "-yyy,";
	}
	mssql_free_result($rs);
	$nome = pulisci($_POST['gruppo']);
	$descr = pulisci($_POST['descrizione']);
	$tipo = $_POST['tipo'];
	$stato = $_POST['stato'];
	$coord = $_POST['coordinatore'];
	$opeins = $_SESSION['UTENTE']->get_userid();
	$permessi = substr($permessi, 0, strlen($permessi) - 1);
	$query = "INSERT INTO gruppi (nome,descr,sid,permessi,status,cancella,tipo,opeins,coordinatore) VALUES('$nome','$descr','1','$permessi','$stato','n',$tipo,'$opeins',$coord)";
	$rs1 = mssql_query($query, $conn);

	$query = "SELECT MAX(gid) FROM gruppi WHERE (opeins='$opeins')";
	$result = mssql_query($query, $conn);
	if (!$result) {
		echo ("no");
		exit();
		die();
	}

	if (!$row = mssql_fetch_row($result)) {
		echo ("no");
		exit();
		die();
	}

	$idgruppo = $row[0];
	scrivi_log($idgruppo, 'gruppi', 'ins', 'gid');
	echo ("ok;;1;lista_gruppi.php");
	exit();
}


function update($id)
{

	$conn = db_connect();
	$idgruppo = $id;
	$query = "SELECT * from menu where status='1' order by nome";
	$rs = mssql_query($query, $conn);

	if (!$rs) {
		echo ("no");
		exit();
		die();
	}
	while ($row = mssql_fetch_assoc($rs)) {
		$id = $row['id'];
		if ($_POST['sel_' . $id] == true)
			$p_l = "y";
		else
			$p_l = "n";
		if ($_POST['sem_' . $id] == true)
			$p_m = "y";
		else
			$p_m = "n";
		if (($p_l == "y") or ($p_m == "y")) $permessi .= $id . "-" . $p_l . $p_m . "y,";
	}
	mssql_free_result($rs);
	$nome = pulisci($_POST['gruppo']);
	$descr = pulisci($_POST['descrizione']);
	$tipo = $_POST['tipo'];
	$stato = $_POST['stato'];
	$coord = $_POST['coordinatore'];
	$opeins = $_SESSION['UTENTE']->get_userid();
	$permessi = substr($permessi, 0, strlen($permessi) - 1);
	$query = "UPDATE gruppi SET coordinatore=$coord, nome='$nome',descr='$descr',sid='1',permessi='$permessi',status='$stato',cancella='n',tipo='$tipo',opeins='$opeins' WHERE gid=$idgruppo";
	$rs1 = mssql_query($query, $conn);
	//echo($query);
	scrivi_log($idgruppo, 'gruppi', 'ins', 'gid');
	echo ("ok;;1;lista_gruppi.php");
	exit();
}




if (isset($_SESSION['UTENTE'])) {


	if (!isset($do)) $do = '';
	$back = "main.php";

	// verifica i permessi
	if (in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";


		switch ($_POST['action']) {

			case "create":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else create();
				break;

			case "update":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
				else update($_POST['id']);
				break;

			case "del":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
				else if ($_POST['choice'] == "si") del($_REQUEST['id']);
				break;
		}

		if ($_REQUEST['action'] == "del_gruppo") {
			if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
				error_message("Permessi insufficienti per questa operazione!");
			else del_gruppo($_REQUEST['id_gruppo']);
		}

		switch ($do) {

			case "add":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else add();
				break;

			case "edit":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
				else edit();
				break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
				break;

			case "logout":
				logout();
				break;
		}
		html_footer();
	} else {
		error_message("non hai i permessi per visualizzare questa pagina!");
		go_home();
	}
} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
