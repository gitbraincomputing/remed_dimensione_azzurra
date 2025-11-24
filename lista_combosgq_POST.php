<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 49;
$tablename = 'combo';
$mod_type=2;
include_once('include/function_page.php');

/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_combo($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	echo(weewwwwwwwwwwwwwwwwww);
    echo($id);
	$conn = db_connect();
	$query = "DELETE FROM combo WHERE id='".$id."'";	
	$result = mssql_query($query, $conn);
	//echo("ok;;2;re_pazienti_anagrafica.php?do=show_allegati&id=".$id_paz);
	//exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}

/****************************
* funzione create()         *
*****************************/
function create() {
    
	global $mod_type;
	$nome = pulisci(trim($_POST['nome']));
	$descrizione = pulisci(trim($_POST['descr']));
	$multi=trim($_POST['multi_valore']);
	$separatore=trim($_POST['separatore']);
		
	$nome = str_replace("'","''",$nome);
	$descrizione = str_replace("'","''",$descrizione);

	$conn = db_connect();
	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "INSERT INTO combo (nome,opeins,descrizione,multi,tipo,separatore) VALUES('$nome','$opeins','$descrizione','$multi',$mod_type,$separatore)";
	
	$result = mssql_query($query, $conn);
	if(!$result)
	{				
		echo("no");
		exit();
		die();
	}

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM combo WHERE (opeins=$opeins) and (tipo=$mod_type)";
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
		
	$idcombo=$row[0];
	scrivi_log ($idcombo,'combo','ins','id');	
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
		while($j<100)
		$arr_ordine[$j]=$j+1;
	
	}	
	$numerofield=sizeof($arr_ordine);
	
	
	$k=0;
	while($k<$numerofield)
	{
	
	  if ((isset($_POST['etichetta'.$i])) and ($_POST['etichetta'.$i]!=''))
	  {
	  $i=$arr_ordine[$k];
	 
		$etichetta=pulisci(trim($_POST['etichetta'.$i]));
		$etichetta = str_replace("'","''",$etichetta);
		//$valore=$_POST['valore'.$i];
		$peso++;	
		//$versione=1;
		$query = "INSERT INTO campi_combo (idcombo,etichetta,valore,peso) VALUES($idcombo,'$etichetta','$peso',$peso)";		
		$result1 = mssql_query($query, $conn);
		if(!$result1)
		{		
			echo("no");
			exit();
			die();
		}	  
	  }	
	$k++;
	}
	echo("ok;;1;lista_combosgq.php");	
	exit();
}


function update($id) {
    
	global $mod_type;
	$nome = pulisci(trim($_POST['nome']));
	$descrizione = pulisci(trim($_POST['descr']));
	$multi=trim($_POST['multi_valore']);
	$separatore=trim($_POST['separatore']);
		
	

	$conn = db_connect();	
	$query = "UPDATE combo SET nome='$nome',multi='$multi',descrizione='$descrizione',separatore='$separatore' WHERE (id=$id)";
	$result = mssql_query($query, $conn);
	if(!$result)
	{		
				echo("no");
				exit();
				die();
		}
	scrivi_log ($id,'combo','agg','id');
	$idcombo=$id;	
		
	$i=1;
	$numerofield=100;
	$classe="";	
		
	$query = "SELECT valore FROM campi_combo WHERE (idcombo=$idcombo)";		
	$result = mssql_query($query, $conn);	
	$peso=mssql_num_rows($result);
	
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
	$i=1;
	
	while($k<$numerofield)
	{
		
		$i=$arr_ordine[$k];	 
	 
	  if ((isset($_POST['elimina'.$i])) and (trim($_POST['elimina'.$i])=='si')) {
	 $elimina=$_POST['elimina'.$i];
	   if(trim($elimina)=="si"){
	   $idcampo_el=$_POST['idcampo'.$i];
			  $query = "UPDATE campi_combo SET stato=0 WHERE (idcampo=$idcampo_el)";			 
			  $result1 = mssql_query($query, $conn);
		  }		
	 }	
	 
	 if ((isset($_POST['etichetta'.$i])) and (trim($_POST['etichetta'.$i])!='')) {	  
	 $id_campo=$_POST['idcampo'.$i];	
	 $etichetta=pulisci(trim($_POST['etichetta'.$i]));
	  //$etichetta = str_replace("'","''",$etichetta);
	 
	  if (trim($etichetta)!=""){
		  $peso++;		
		 // $query="SELECT * FROM campi_combo WHERE (idcombo=$idcombo) AND (etichetta='$etichetta') AND stato<>0";
		  if($id_campo==""){
			$query = "INSERT INTO campi_combo (idcombo,etichetta,valore,peso) VALUES($idcombo,'$etichetta','$peso',$peso)";
			$result1 = mssql_query($query, $conn);
		  }else{
			  $query="SELECT * FROM campi_combo WHERE (idcampo=$id_campo)";
			  $result1 = mssql_query($query, $conn);
			  if(mssql_num_rows($result1)==0){
				mssql_free_result($result1);			
				$query = "INSERT INTO campi_combo (idcombo,etichetta,valore,peso) VALUES($idcombo,'$etichetta','$peso',$peso)";
				$result1 = mssql_query($query, $conn);
			  }
			  else{
				$row_1=mssql_fetch_assoc($result1);
				$etichetta_old=$row_1['etichetta'];
				mssql_free_result($result1);							
				if($etichetta_old==$etichetta){
					$query = "UPDATE campi_combo SET peso='$peso' WHERE idcampo=$id_campo";
					$result1 = mssql_query($query, $conn);
					}
					else{
					$query = "UPDATE campi_combo SET stato=0 WHERE idcampo=$id_campo";
					$result1 = mssql_query($query, $conn);
					$query = "INSERT INTO campi_combo (idcombo,etichetta,valore,peso) VALUES($idcombo,'$etichetta','$peso',$peso)";
					$result1 = mssql_query($query, $conn);
					}
			  }
		  /*echo($query);*/
		}
	  }
	 }
	
	$k++;
	}

	echo("ok;;1;lista_combosgq.php");	
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
		
		if($_REQUEST['action']=="del_combo"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_combo($_REQUEST['id_combo']);
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

