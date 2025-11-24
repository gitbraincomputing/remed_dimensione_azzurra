<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 39;
$tablename = 'regime';
include_once('include/function_page.php');

/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_range($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM test_clinici_range_details WHERE idrange_detail='".$id."'";	
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM test_clinici_range WHERE idrange='".$id."'";
	$result = mssql_query($query, $conn);	
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}

function create()
{
		$conn = db_connect();
		$nome = stripslashes($_POST['nome_range']);
		$nome=pulisci($nome);
		$opeins=$_SESSION['UTENTE']->get_userid();
		$query = "INSERT INTO test_clinici_range (nome,opeins) VALUES('$nome',$opeins)";
		$result = mssql_query($query, $conn);
		if(!$result) 
		{
		echo ("no");
		exit();
		die();
		}
		
		$query = "SELECT MAX(idrange) FROM test_clinici_range WHERE (opeins=$opeins)";
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
			
		$id=$row[0];	
		scrivi_log ($id,'test_clinici_range','ins','idrange');


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
	$numerofields_items=20;
	$numerofields_limiti=20;
	
	
	
	$k=0;
$i=1;
	// scrivo le prove del test
	while($k<$numerofield)
	{
	
	  if (((isset($_POST['vi'.$i])) and ($_POST['vi'.$i]!='')) and ((isset($_POST['vf'.$i])) and ($_POST['vf'.$i]!='')))
	  {
	  $i=$arr_ordine[$k];
	  $etichetta=pulisci($_POST['etichetta'.$i]);
	  $tipo_dato=($_POST['tipo_dato'.$i]);
	  $vi=pulisci($_POST['vi'.$i]);
	  $vf=pulisci($_POST['vf'.$i]);
	  
      $query = "INSERT INTO test_clinici_range_details (idrange,descrizione,tipo_dato,valore_da,valore_a) VALUES($id,'$etichetta',$tipo_dato,'$vi','$vf')";
	 //echo($query);
			if(trim($vi)!="")	{
			   $result1 = mssql_query($query, $conn);
			   $query = "SELECT MAX(idprova) FROM test_clinici_prove";
				$result1 = mssql_query($query, $conn);
				
					
		
		} //end if etichetta valorizzata
	  }//end if valore prove postate
	$k++;
	} // end while prove

		
	echo("ok;;1;lista_range.php");	
	exit();
}


function update($id){

	$conn = db_connect();
	
	$nome_range=pulisci($_POST['nome_range']);
	$id=$_POST['id'];
	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "UPDATE test_clinici_range SET Nome='$nome_range' WHERE idrange='$id'";		
	$result = mssql_query($query, $conn);
	if(!$result)
	{
	echo("no");
	die();
	exit();
	}
	// scrive il log
	scrivi_log ($id,'test_clinici_range','agg','idrange');	


	$i=1;
	$numerofield=100;
	$classe="";
	 
	$arr_ordine=Array();
	$j=0;
	while($j<100)
		{
		$arr_ordine[$j]=$j+1;
		$j++;
		}
	$numerofield=sizeof($arr_ordine);

	$k=0;
	$i=1;
	// scrivo le prove del test
	//$numerofield=100;
	

	
	while($k<$numerofield)
	{
	
	 $i=$arr_ordine[$k];
	 
	if ((isset($_POST['elimina'.$i])) )
	{
	
	 $elimina=$_POST['elimina'.$i];
	// echo($elimina);
		
	   if(trim($elimina)=="si"){
	   $idcampo_el=$_POST['idcampo'.$i];
	   //echo($idcampo);
	   
			  $query = "UPDATE test_clinici_range_details SET stato=0 WHERE (idrange_detail=$idcampo_el)";			 
			 echo($query);
			  $result1 = mssql_query($query, $conn);
		  }		
	}	
	 	
	if (((isset($_POST['vi'.$i])) and ($_POST['vi'.$i]!='')) and ((isset($_POST['etichetta'.$i])) and ($_POST['etichetta'.$i]!='')) and ((isset($_POST['vf'.$i])) and ($_POST['vf'.$i]!='')))	
	 {
	  $id_campo=$_POST['idcampo'.$i];
	  $etichetta=pulisci($_POST['etichetta'.$i]);
	  //echo( $etichetta);
	  $tipo_dato=($_POST['tipo_dato'.$i]);
	  $vi=pulisci($_POST['vi'.$i]);
	  $vf=pulisci($_POST['vf'.$i]);
			
	  if($id_campo==""){
			$peso++;
			$query = "INSERT INTO test_clinici_range_details (idrange,descrizione,tipo_dato,valore_da,valore_a) VALUES($id,'$etichetta',$tipo_dato,'$vi','$vf')";
			//echo($query);
			mssql_query($query, $conn);
		  }else{
			  $query="SELECT * FROM test_clinici_range_details WHERE (idrange_detail=$id_campo)";
			   //echo($query);
			  $result1 = mssql_query($query, $conn);
			  if(mssql_num_rows($result1)==0){
				mssql_free_result($result1);			
				$query = "INSERT INTO test_clinici_range_details (idrange,descrizione,tipo_dato,valore_da,valore_a) VALUES($id,'$etichetta',$tipo_dato,'$vi','$vf')";
				//echo($query);
				$result1 = mssql_query($query, $conn);
			  }
			  else{
				$row_1=mssql_fetch_assoc($result1);
				$etichetta_old=$row_1['descrizione'];
				$tipo_dato_old=$row_1['tipo_dato'];
				$vi_old=$row_1['valore_da'];
				$vf_old=$row_1['valore_a'];
				mssql_free_result($result1);
				//echo($etichetta_old."-".$tipo_dato_old."-".$vi_old."-".$vf_old);	
				if(($etichetta_old!=$etichetta)or($tipo_dato_old!=$tipo_dato)or($vi_old!=$vi)or($vf_old!=$vf)){					
					$query = "UPDATE test_clinici_range_details SET stato=0 WHERE idrange_detail=$id_campo";
					//echo($query);
					$result1 = mssql_query($query, $conn);
					$query = "INSERT INTO test_clinici_range_details (idrange,descrizione,tipo_dato,valore_da,valore_a) VALUES($id,'$etichetta',$tipo_dato,'$vi','$vf')";
					//echo($query);
					$result1 = mssql_query($query, $conn);
					}
			  }
		  /*echo($query);*/
		}
	
     
	  }//end if valore prove postate
	$k++;
	} // end while prove

	
	
	
	
	echo("ok;;1;lista_range.php");
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
		
		if($_REQUEST['action']=="del_range"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_range($_REQUEST['id_range']);
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

