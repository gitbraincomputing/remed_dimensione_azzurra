<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 61;
$tablename = 'utenti';
$mod_type=1;
include_once('include/function_page.php');

/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_test($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM test_clinici WHERE idtest='".$id."'";	
	echo($query);
	$result = mssql_query($query, $conn);
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}

/****************************
* funzione create()         *
*****************************/
function create() {
    
	global $mod_type;
	
	$nome = pulisci($_POST['nome']);
	$codice = pulisci($_POST['codice']);
	$descrizione = pulisci($_POST['descr']);
	$idrange = $_POST['range'];
	$word = $_FILES['word']['name'];
	$status = $_POST['status'];
	$tipo_test=$_POST['tipo_test'];
	$limite_normativo_default=$_POST['limite_normativo_default'];
	$dispo_lista=$_POST['dispo_lista'];
	
	//$nome = str_replace("'","''",$nome);

	$conn = db_connect();
	
	$opeins=$_SESSION['UTENTE']->get_userid();	
	$query = "INSERT INTO test_clinici (nome,codice,descrizione,opeins,cancella,stato,idrange,tipo,limite_default,test_ramificato,disponibile_in_lista_moduli) VALUES('$nome','$codice','$descrizione',$opeins,'n',1,$idrange,$tipo_test,$limite_normativo_default,1,$dispo_lista)";
	
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(idtest) as idtest FROM test_clinici WHERE (opeins=$opeins)";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	
	if($row = mssql_fetch_row($result))
	$idtest=$row[0];	
	else
	{
	echo ("no");
	exit();
	die();
	}
	
	
	$i=1;
	$numerofield=100;
	$classe="";
	
	$arr_ordine=Array();
	if (isset($_POST['debug']))
	{
	$ordine=$_POST['debug'];
	if(strrpos($ordine, ",")>0)
		$arr_ordine=split(",",$ordine);
		else
		$arr_ordine=split(" ",trim($ordine));
	}
	else
	{
		while($j<100)
		$arr_ordine[$j]=$j+1;	
	}
	
	$numerofield=sizeof($arr_ordine);
	
	$k=0;
	// scrivo le prove del test
	while($k<$numerofield)
	{
	
	  if ((isset($_POST['test_clinico_scelto_sel'.$i])) and ($_POST['test_clinico_scelto_sel'.$i]!=''))
	  {
	  
	  $i=$arr_ordine[$k];
	  $test_ramificato=$_POST['test_ramificato'.$i];
	  $test_clinico_scelto=$_POST['test_clinico_scelto_sel'.$i];
	  
      $query = "INSERT INTO test_clinici_ramificati (idtest_padre,test_ramificato,idtest_figlio) VALUES($idtest,$test_ramificato,$test_clinico_scelto)";	
	  mssql_query($query, $conn);
			
	  }//end if valore prove postate
	$k++;
	} // end while prove

	echo("ok;;1;lista_test_clinici_ramificati.php");	
	exit();
	// scrive il log
	//scrivi_log ($idmodulo,$tablename,'ins');

}


function update($id) {
   
	global $mod_type;
	
	$conn = db_connect();
	
	$idtest = $_POST['id'];
	$query="UPDATE test_clinici SET stato=0 WHERE idtest=$id";
	//echo($query);
	$result = mssql_query($query, $conn);
	
	$nome = pulisci($_POST['nome']);
	$codice = pulisci($_POST['codice']);
	$descrizione = pulisci($_POST['descr']);
	$idrange = $_POST['range'];
	$word = $_FILES['word']['name'];
	$status = $_POST['status'];
	$tipo_test=$_POST['tipo_test'];
	$limite_normativo_default=$_POST['limite_normativo_default'];
	$dispo_lista=$_POST['dispo_lista'];
	
	//$nome = str_replace("'","''",$nome);

	$opeins=$_SESSION['UTENTE']->get_userid();	
	$query = "INSERT INTO test_clinici (nome,codice,descrizione,opeins,cancella,stato,idrange,tipo,limite_default,test_ramificato,disponibile_in_lista_moduli) VALUES('$nome','$codice','$descrizione',$opeins,'n',1,$idrange,$tipo_test,$limite_normativo_default,1,$dispo_lista)";
	
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(idtest) as idtest FROM test_clinici WHERE (opeins=$opeins)";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	
	if($row = mssql_fetch_row($result))
	$idtest=$row[0];	
	else
	{
	echo ("no");
	exit();
	die();
	}
	
	
	$i=1;
	$numerofield=100;
	$classe="";
	
	$arr_ordine=Array();
	if (isset($_POST['debug']))
	{
	$ordine=$_POST['debug'];
	if(strrpos($ordine, ",")>0)
		$arr_ordine=split(",",$ordine);
		else
		$arr_ordine=split(" ",trim($ordine));
	}
	else
	{
		while($j<100)
		$arr_ordine[$j]=$j+1;	
	}
	
	$numerofield=100;
	
	$k=0;
	// scrivo le prove del test
	while($k<$numerofield)
	{
	  if ((isset($_POST['test_clinico_scelto_sel'.$k])) and ($_POST['test_clinico_scelto_sel'.$k]!=''))
	  {
	  
	  $test_ramificato=$_POST['test_ramificato'.$k];
	  $test_clinico_scelto=$_POST['test_clinico_scelto_sel'.$k];
	  
      $query = "INSERT INTO test_clinici_ramificati (idtest_padre,test_ramificato,idtest_figlio) VALUES($idtest,$test_ramificato,$test_clinico_scelto)";
	  mssql_query($query, $conn);
			
	  }//end if valore prove postate
	$k++;
	} // end while prove

	echo("ok;;1;lista_test_clinici_ramificati.php");	
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
			
		}
		
		if($_REQUEST['action']=="del_test"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_test($_REQUEST['id_test']);
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

