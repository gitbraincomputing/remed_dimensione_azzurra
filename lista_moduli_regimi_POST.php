<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 4;
$tablename = 'utenti';
include_once('include/function_page.php');


function update($id) {
    
	$idregime=$id;
	
	$conn = db_connect();
	

	$query = "delete from regimi_moduli where idregime=$idregime";
	$result = mssql_query($query, $conn);
	if(!$result) echo("no;;;");
	$j=0;

	$arr_ordine=Array();
	if (isset($_POST['debug']))
	{
	$ordine=$_POST['debug'];
	$arr_ordine=split(" ",$ordine);
	
	}
	else
	{
		while($j<100)
		$arr_ordine[$j]=$j+1;
	
	}
		
	$numerofield=sizeof($arr_ordine)-1;
	$k=0;	
	while($k<$numerofield)
	{
		 $idmodulo=$arr_ordine[$k];
		$pre='previsto'.($idmodulo);	 
				
		 if ($_POST[$pre]=="s")
		 {
			 $obb='default'.($idmodulo);	 
		 
		 
			
		 $default=$_POST[$obb];
		  $scadenza=$_POST['scadenza'.($idmodulo)];
		  $trattamenti=$_POST['trattamenti'.($idmodulo)];
		  $scad_flg=$_POST['scad_flg'.($idmodulo)];
		  $scadenza=trim($scadenza);
		  $scadenza=pulisci($scadenza);
		  $idmod=$_POST['idmodulo'.($idmodulo)];
		  $raggruppamento=$_POST['raggruppamento'.($idmodulo)];
		 
		 if($scad_flg=='n'){
			$scadenza="";
			$trattamenti="";
		 }elseif($scad_flg=='c'){
			$trattamenti="";
		 }elseif($scad_flg=='t'){
			$scadenza="";
		 }
		 
		  $query = "INSERT INTO regimi_moduli (idregime,idmodulo,obbligatorio,scadenza,trattamenti,scad_flg,raggruppamento) VALUES($idregime,$idmod,'$default','$scadenza','$trattamenti','$scad_flg','$raggruppamento')";		  
		  $result1 = mssql_query($query, $conn);		
			if(!$result1) {				
				echo("no");
				exit();
				die();
			}
		$med='medico'.($idmodulo);		
		$medico=$_POST[$med];		
		$opeins=$_SESSION['UTENTE']->get_userid();
		$query = "INSERT INTO moduli_medici (id_regime,id_operatore,id_modulo,opeins) VALUES($idregime,$medico,$idmod,$opeins)";		
		$result1 = mssql_query($query, $conn);
		
		$query = "SELECT MAX(id) FROM moduli_medici WHERE (opeins='$opeins')";
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
			
		$idmod_med=$row[0];	
		scrivi_log ($idmod_med,'moduli_medici','ins','id');
		}
		$k++;
	}
	
	
	

	echo("ok;;1;lista_moduli_regimi.php");	
	exit();
		
	// scrive il log
	//scrivi_log ($idmodulo,$tablename,'ins');

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

