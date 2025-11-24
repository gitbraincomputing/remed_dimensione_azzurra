<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 74;
$tablename = 'combo';
$mod_type=2;
include_once('include/function_page.php');

/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_combo_ramificate($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM combo_ramificate WHERE id='".$id."'";	
	$result = mssql_query($query, $conn);
	$query = "UPDATE combo_ramificate SET idcombopadre=0, idvalorepadre=0 WHERE (idcombopadre=$id)";
	$result = mssql_query($query, $conn);
	//echo("ok;;2;re_pazienti_anagrafica.php?do=show_allegati&id=".$id_paz);
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
	$nome = pulisci(trim($_POST['nome']));
	$descrizione = pulisci(trim($_POST['descr']));
	$multi=trim($_POST['multi_valore']);
	$stampa=trim($_POST['stampa']);
	
	
	$combopadre=$_POST['idpadre'];
	$valorepadre=$_POST['idvalorepadre'];
	if ($valorepadre=='')
	{
	$combopadre=0;
	$valorepadre=0;
	}
	$conn = db_connect();
	
	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "INSERT INTO combo_ramificate (nome,opeins,descrizione,multi,tipo,idcombopadre,idvalorepadre,stampa) VALUES('$nome','$opeins','$descrizione','$multi',$mod_type,$combopadre,$valorepadre,$stampa)";
	$result = mssql_query($query, $conn);
	if(!$result) error_message("non posso");

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM combo_ramificate WHERE (opeins=$opeins) and (tipo=$mod_type)";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		die("errore MAX select");
		
	$idcombo=$row[0];
	scrivi_log ($idcombo,'combo_ramificate','ins','id');	
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
	 
	  
	  //$etichetta=stripslashes(trim($_POST['etichetta'.$i]));
      
      $etichetta=pulisci(trim($_POST['etichetta'.$i]));
	  $etichetta = str_replace("'","''",$etichetta);
	  //$valore=$_POST['valore'.$i];
	  $peso++;	
	  //$versione=1;
     $query = "INSERT INTO campi_combo_ramificate (idcombo,etichetta,valore) VALUES($idcombo,'$etichetta','$peso')";		
	$result1 = mssql_query($query, $conn);
	if(!$result1) error_message("errore inserimento");
	  
	  }
	
	$k++;
	}

	echo("ok;;1;lista_combo_ramificatesgq.php");	
	exit();
	// scrive il log
	//scrivi_log ($idmodulo,$tablename,'ins');

}


function update($id) {
    
	global $mod_type;
	$nome = pulisci(trim($_POST['nome']));
	$descrizione = pulisci(trim($_POST['descr']));
	$multi=trim($_POST['multi_valore']);
	$combopadre=pulisci(trim($_POST['idpadre']));
	$valorepadre=pulisci(trim($_POST['idvalorepadre']));
	$stampa=$_POST['stampa'];
	
	if  ($valorepadre==0)
	{
	$combopadre=0;
	$valorepadre=0;
	}

	$conn = db_connect();
	
	$query = "UPDATE combo_ramificate SET stampa=$stampa, nome='$nome',multi='$multi',descrizione='$descrizione',idcombopadre=$combopadre,idvalorepadre=$valorepadre WHERE (id=$id)";
	
	
	$result = mssql_query($query, $conn);
	if(!$result) error_message("non posso");
	scrivi_log ($id,'combo_ramificate','agg','id');	
	//$query = "DELETE FROM campi_combo WHERE(idcombo=$id)";
	//$result = mssql_query($query, $conn);
	
	$idcombo=$id;	
		
	$i=1;
	$numerofield=100;
	$classe="";	
	
	$query = "SELECT valore FROM campi_combo_ramificate WHERE (idcombo=$idcombo)";		
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
		
		
	 
	  if ((isset($_POST['elimina'.$i])) and (trim($_POST['elimina'.$i])=='si'))
	 {
	 $elimina=$_POST['elimina'.$i];
	   if(trim($elimina)=="si"){
	   $idcampo_el=$_POST['idcampo'.$i];
			  $query = "UPDATE campi_combo_ramificate SET stato=0 WHERE (idcampo=$idcampo_el)";			 
			  $result1 = mssql_query($query, $conn);
		  }		
	}	
	 
	 if ((isset($_POST['etichetta'.$i])) and (trim($_POST['etichetta'.$i])!=''))
	  {  	
	  $id_campo=$_POST['idcampo'.$i];
       $etichetta=pulisci(trim($_POST['etichetta'.$i]));
	 // $etichetta=stripslashes(trim($_POST['etichetta'.$i]));
	  //$etichetta = str_replace("'","''",$etichetta);
	  

	  if (trim($etichetta)!=""){		 		  		  
		  $peso++;
		  //$query="SELECT * FROM campi_combo_ramificate WHERE (idcombo=$idcombo) AND (etichetta='$etichetta') AND stato<>0";
		   if($id_campo==""){
			$query = "INSERT INTO campi_combo_ramificate (idcombo,etichetta,valore,peso) VALUES($idcombo,'$etichetta','$peso',$peso)";
			$result1 = mssql_query($query, $conn);
		  }else{
			  $query="SELECT * FROM campi_combo_ramificate WHERE (idcampo=$id_campo)";
			  $result1 = mssql_query($query, $conn);
			  if(mssql_num_rows($result1)==0){
				mssql_free_result($result1);			
				$query = "INSERT INTO campi_combo_ramificate (idcombo,etichetta,valore,peso) VALUES($idcombo,'$etichetta','$peso',$peso)";
				$result1 = mssql_query($query, $conn);
			  }
			  else{
				$row_1=mssql_fetch_assoc($result1);
				$etichetta_old=$row_1['etichetta'];
				mssql_free_result($result1);							
				if($etichetta_old==$etichetta){
					$query = "UPDATE campi_combo_ramificate SET peso='$peso' WHERE idcampo=$id_campo";
					$result1 = mssql_query($query, $conn);
					}
					else{
					$query = "UPDATE campi_combo_ramificate SET stato=0 WHERE idcampo=$id_campo";
					$result1 = mssql_query($query, $conn);
					$query = "INSERT INTO campi_combo_ramificate (idcombo,etichetta,valore,peso) VALUES($idcombo,'$etichetta','$peso',$peso)";
					$result1 = mssql_query($query, $conn);
					}
			  }
		  /*echo($query);*/
		}
	  }
	  }
	
	$k++;
	}

	echo("ok;;1;lista_combo_ramificatesgq.php");	
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
					else del_combo_ramificate($_REQUEST['id_combo']);
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

