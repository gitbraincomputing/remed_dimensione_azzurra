<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 40;
$tablename = 'tipologia';
include_once('include/function_page.php');


/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_tipologia($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM tipologia WHERE idtipologia='".$id."'";	
	$result = mssql_query($query, $conn);
	//echo("ok;;2;re_pazienti_anagrafica.php?do=show_allegati&id=".$id_paz);
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}


function create()
{
	$conn = db_connect();
		$descrizione = pulisci($_POST['descrizione']);
		$codice = $_POST['codice'];
		$descrizionelunga = pulisci($_POST['descrizionelunga']);
		$regime = $_POST['regime'];
		$opeins=$_SESSION['UTENTE']->get_userid();
		$query = "INSERT INTO tipologia (idregime,descrizione,descrizionelunga,codice,opeins) VALUES('$regime','$descrizione','$descrizionelunga','$codice',$opeins)";
		$result = mssql_query($query, $conn);
		if(!$result) 
		{
		echo ("no");
		exit();
		die();
		}
		// scrive il log
	// recupera l'id del record appena inserito
	$query = "SELECT MAX(idtipologia) FROM tipologia WHERE (opeins=$opeins)";
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
		
	$idtipologia=$row[0];	
	scrivi_log ($idtipologia,'tipologia','ins','idtipologia');	
	$peso=0;	
	$i=1;
	$numerofield=100;
	$classe="";	
	
	$arr_ordine=Array();
	if (isset($_POST['debug']))
	{
	$ordine=$_POST['debug'];
	$arr_ordine=split(",",$ordine);
	}
	else
	{
		while($j<100)
		$arr_ordine[$j]=$j+1;
	
	}
		
	$numerofield=sizeof($arr_ordine);
	
		
	$k=0;
	
	while($k<$numerofield)
	{
		 
	  if ((isset($_POST['etichetta'.$i])) and (trim($_POST['etichetta'.$i]!='')))
	  {
	  $i=$arr_ordine[$k];
	  $etichetta=trim($_POST['etichetta'.$i]);
  	  $codice=$_POST['cod'.$i];	 
	  $dal=$_POST['dal'.$i];
	  $al=$_POST['al'.$i];
	  $peso++;	  
	   
	  if ($etichetta!=''){	 
	  if($al!='')
			$query = "INSERT INTO tariffe (idtipologia,codice,tariffa,dal,al,opeins) VALUES($idtipologia,'$codice','$etichetta','$dal','$al',$opeins)";
			else
			$query = "INSERT INTO tariffe (idtipologia,codice,tariffa,dal,opeins) VALUES($idtipologia,'$codice','$etichetta','$dal',$opeins)";			 
		 $result1 = mssql_query($query, $conn);
		 if(!$result1)
		 {
		echo ("no");
		exit();
		die();
		}
		   // recupera l'id del record appena inserito
			$query = "SELECT MAX(idtariffa) FROM tariffe WHERE opeins=$opeins";
			$result = mssql_query($query, $conn);
			if(!$result)
			{
		echo ("no");
		exit();
		die();
		}
			if($row = mssql_fetch_row($result))	$idtariffa=$row[0]; 
			scrivi_log ($idtariffa,'tariffe','ins','idtariffa');
		  }		
		}	
	$k++;
	}
		
	echo("ok;;1;lista_tipologie.php");
	exit();
	
}


function update($id)
{



$conn = db_connect();

	$descrizione = pulisci($_POST['descrizione']);
	$codice = $_POST['codice'];
	$descrizionelunga = pulisci($_POST['descrizionelunga']);
	$regime = $_POST['regime'];
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "UPDATE tipologia SET idregime='$regime',descrizione='$descrizione',codice='$codice',descrizionelunga='$descrizionelunga' WHERE idtipologia='$id'";	
	$result = mssql_query($query, $conn);
	if(!$result)
	{
		echo ("no");
		exit();
		die();
		}
	// scrive il log
	scrivi_log ($id,'tipologia','agg','idtipologia');	
	$idtipologia=$id;	
	$peso=0;	
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
		while($j<100){
			$arr_ordine[$j]=$j+1;
			$j++;
		}
		
	}
	$numerofield=sizeof($arr_ordine);
	
	$k=0;
	$i=1;
	$numerofield=100;
	while($k<$numerofield)
	{
		 
		$i=$k;
	  $idcampo=$_POST['idcampo'.$i];		
	if ((isset($_POST['elimina'.$i])) and (trim($_POST['elimina'.$i])=='si')) {
			$elimina=$_POST['elimina'.$i];
			if(trim($elimina)=="si"){				
				$query = "DELETE FROM tariffe WHERE (idtipologia=$idtipologia)and(tariffa=$idcampo)";							
				$result1 = mssql_query($query, $conn);
			}		
		}
		
	 if ((isset($_POST['etichetta'.$i])) and (trim($_POST['etichetta'.$i]!='')) and($_POST['elimina'.$i]!='si')) {	 
	 // $i=$arr_ordine[$k];	
	  $etichetta=$_POST['etichetta'.$i];
	  $codice=$_POST['cod'.$i];	 
	  $dal=$_POST['dal'.$i];
	  $al=$_POST['al'.$i];
	  $peso++;	  
	  
	  $query="SELECT TOP(1) * FROM tariffe WHERE (idtipologia=$idtipologia)and(tariffa='$etichetta') order by idtariffa DESC";	
	  $result1 = mssql_query($query, $conn);
	  if ($row=mssql_fetch_assoc($result1))	{
		$id_old=$row['idtariffa'];
		$al_old=$row['al']; 	  
		mssql_free_result($result1);
		$query = "UPDATE tariffe SET al='$al',dal='$dal',tariffa='$etichetta',codice='$codice',opeins=$opeins WHERE idtariffa='$id_old'";		
		$result1 = mssql_query($query, $conn);	
	  }
	  else {		 
			if($al!='')
				$query = "INSERT INTO tariffe (idtipologia,codice,tariffa,dal,al,opeins) VALUES($idtipologia,'$codice','$etichetta','$dal','$al',$opeins)";
				else
				$query = "INSERT INTO tariffe (idtipologia,codice,tariffa,dal,opeins) VALUES($idtipologia,'$codice','$etichetta','$dal',$opeins)";				
				$result1 = mssql_query($query, $conn);
			   // recupera l'id del record appena inserito
				$query = "SELECT MAX(idtariffa) FROM tariffe WHERE opeins=$opeins";
				$result = mssql_query($query, $conn);
				if($row = mssql_fetch_row($result))	$idtariffa=$row[0]; 
				scrivi_log ($idtariffa,'tariffe','ins','idtariffa');
			}		  
		}			
	$k++;
	}
		
	echo("ok;;1;lista_tipologie.php");
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

		if($_REQUEST['action']=="del_tipologia"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_tipologia($_REQUEST['id_tipologia']);
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

