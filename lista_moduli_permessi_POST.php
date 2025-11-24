<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 6;
$tablename = 'regime';
$mod_type=1;
include_once('include/function_page.php');

function create()
{
	global $mod_type;
	$conn = db_connect();
	$query = "DELETE FROM moduli_permessi where (tipo=1) and modtype=$mod_type";
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM moduli_permessi where (tipo=0) and modtype=$mod_type";
	$result = mssql_query($query, $conn);
	
	$arr_moduli = array();
	$arr_gruppi = array();
	$query = "SELECT idmodulo from re_distinct_moduli where (tipo=$mod_type)";	
	$rs1 = mssql_query($query, $conn);
		
	$i=0;
	while($row=mssql_fetch_assoc($rs1)){
		$idmodulo=$row['idmodulo'];
		$query = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc";	
		$rs = mssql_query($query, $conn);
		$row = mssql_fetch_assoc($rs);
		$id=$row['idmodulo'];
		$arr_moduli[$i]=$id;		
		$i++;
	}

	$query = "SELECT  * from gruppi where (cancella='n') and (status='1') and tipo<>1  order by nome ";	
	$rs = mssql_query($query, $conn);	
	$i=0;
	while($row=mssql_fetch_assoc($rs)){		
		$arr_gruppi[$i]=$row['gid'];		
		$i++;
	}
		
	foreach( $arr_moduli as $value){
		foreach( $arr_gruppi as $value_g){		
		//permessi in creazione / modifica / cancellazione solo del modulo di cui si è proprietario	
			if ($_POST['sel_'.$value.'_'.$value_g.'_0']==true){
				$query = "INSERT INTO moduli_permessi (idmodulo,idgruppo,permesso,tipo,modtype) VALUES($value,$value_g,1,0,$mod_type)";					
				$result1 = mssql_query($query, $conn);
			}
			//permessi in visulualizzazione _1
			if ($_POST['sel_'.$value.'_'.$value_g.'_1']==true){
				$query = "INSERT INTO moduli_permessi (idmodulo,idgruppo,permesso,tipo,modtype) VALUES($value,$value_g,1,1,$mod_type)";		
				$result1 = mssql_query($query, $conn);
			}
		}
	}
	
	echo("ok;;1;lista_moduli_permessi.php");
	exit();	

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
					else edit();
			break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				edit();
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

