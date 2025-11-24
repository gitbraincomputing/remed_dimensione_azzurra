<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 55;
$tablename = 'utenti';
$mod_type=1;
include_once('include/function_page.php');

/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_test($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM test_clinici_prove WHERE idtest='".$id."'";	
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM test_clinici_item WHERE idprova='".$id."'";
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM test_clinici WHERE idtest='".$id."'";
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
	$status = $_POST['status'];
	$tipo_test=$_POST['tipo_test'];
	$limite_normativo_default=$_POST['limite_normativo_default'];
	$dispo_lista=$_POST['dispo_lista'];
	$nome = str_replace("'","''",$nome);


	$conn = db_connect();
	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "INSERT INTO test_clinici (nome,codice,descrizione,opeins,cancella,stato,idrange,tipo,limite_default,test_ramificato,disponibile_in_lista_moduli) VALUES('$nome','$codice','$descrizione',$opeins,'n',1,$idrange,$tipo_test,$limite_normativo_default,0,$dispo_lista)";
	//echo($query);
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
	$numerofield=200;
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
		while($j<200)
		$arr_ordine[$j]=$j+1;
	
	}
	
	
	$numerofield=sizeof($arr_ordine);
	$numerofields_items=200;
	$numerofields_limiti=200;
	
	
	$k=0;
	// scrivo le prove del test
	while($k<$numerofield)
	{
	
	  if ((isset($_POST['etichetta'.$i])) and ($_POST['etichetta'.$i]!=''))
	  {
	  $i=$arr_ordine[$k];
	  
	  $etichetta= pulisci($_POST['etichetta'.$i]);	 
	  $istruzioni_operative= pulisci($_POST['etichetta'.$i]);
	 	  
	  $valuta=$_POST['valuta'.$i];
	  $somma=$_POST['somma'.$i];
      $query = "INSERT INTO test_clinici_prove (idtest,descrizione,istruzioni_operative,tipo_valutazione,somma) VALUES($idtest,'$etichetta','$istruzioni_operative',$valuta,$somma)";
	 
			if(trim($etichetta)!="")	{
			   $result1 = mssql_query($query, $conn);
			   $query = "SELECT MAX(idprova) FROM test_clinici_prove";
				$result1 = mssql_query($query, $conn);
				if($row = mssql_fetch_row($result1))
				$idprova=$row[0];	
				else
				{
				echo ("no");
				exit();
				die();
				}

				$j=1;
				//ciclo le risposte
				while($j<$numerofields_items)
				{
				
					$item_n=($i*100)+$j;
					if (isset($_POST['descrizione'.$item_n]))
					{
						$item=pulisci($_POST['descrizione'.$item_n]);
						$valore_risposta=$_POST['valore'.$item_n];
						$query = "INSERT INTO test_clinici_items (idprova,etichetta,valore) VALUES($idprova,'$item','$valore_risposta')";
						 if(trim($item)!="")
						 {
						 $result1 = mssql_query($query, $conn);
							$query = "SELECT MAX(iditem) FROM test_clinici_items";
							$result1 = mssql_query($query, $conn);
						if($row = mssql_fetch_row($result1))
							$iditem=$row[0];	
						else
						{
						echo ("no");
						exit();
						die();
						}
							
						 }//end if items valorizzati
						
					}//end if items postati
				
				$j++;
				}//end while risposte
				
				$z=1;
							while($z<$numerofields_limiti)
							{
								$limit_n=($i*100)+$z;
								if (isset($_POST['limite_normativo'.$limit_n]))
								{
								$limit=$_POST['limite_normativo'.$limit_n];
								$punteggio=$_POST['punteggio'.$limit_n];
								$query = "INSERT INTO test_clinici_limiti (iditem,ideta,valore) VALUES($iditem,$limit,$punteggio)";
								 if(trim($punteggio)!="")
								 {
								 $result1 = mssql_query($query, $conn);
								 }//end if limiti valorizzati in termini di punteggio
								}//end if limiti postati
								$z++;
							}//end while limiti
		
		
		} //end if etichetta valorizzata
	  }//end if valore prove postate
	$k++;
	} // end while prove

	echo("ok;;1;lista_test_clinici.php");	
	exit();
	
}


function update($id) {
	global $mod_type;
	
	$conn = db_connect();
	$id=$_POST['id'];
	$query="UPDATE test_clinici SET stato=0 WHERE idtest=$id";
	//echo($query);
	$result = mssql_query($query, $conn);
	
	$nome = pulisci($_POST['nome']);
	$codice = pulisci($_POST['codice']);
	$descrizione = pulisci($_POST['descr']);
		
	$idrange = $_POST['range'];	
	$status = $_POST['status'];
	$tipo_test=$_POST['tipo_test'];
	$limite_normativo_default=$_POST['limite_normativo_default'];
	$dispo_lista=$_POST['dispo_lista'];
	$nome = str_replace("'","''",$nome);


	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "INSERT INTO test_clinici (nome,codice,descrizione,opeins,cancella,stato,idrange,tipo,limite_default,test_ramificato,disponibile_in_lista_moduli) VALUES('$nome','$codice','$descrizione',$opeins,'n',1,$idrange,$tipo_test,$limite_normativo_default,0,$dispo_lista)";
	//echo($query);
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
	$numerofield=200;
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
		while($j<200)
		$arr_ordine[$j]=$j+1;
	}
	
	
	//$numerofield=sizeof($arr_ordine);
	$numerofields_items=200;
	$numerofields_limiti=200;
	
	
	//$k=0;
	$i=0;
	// scrivo le prove del test
	
	while($i<$numerofield)
	{
	
	  if ((isset($_POST['etichetta'.$i])) and ($_POST['etichetta'.$i]!='') and ($_POST['elimina'.$i]!='1'))
	  {
	  //$i=$arr_ordine[$k];
	  
	  $etichetta= pulisci($_POST['etichetta'.$i]);	 
	  $istruzioni_operative= pulisci($_POST['istr_oper'.$i]);
	 	  
	  $valuta=$_POST['valuta'.$i];
	  $somma=$_POST['somma'.$i];
      $query = "INSERT INTO test_clinici_prove (idtest,descrizione,istruzioni_operative,tipo_valutazione,somma) VALUES($idtest,'$etichetta','$istruzioni_operative',$valuta,$somma)";	
			if(trim($etichetta)!="")	{
			   $result1 = mssql_query($query, $conn);
			   $query = "SELECT MAX(idprova) FROM test_clinici_prove";
				$result1 = mssql_query($query, $conn);
				if($row = mssql_fetch_row($result1))
				$idprova=$row[0];	
				else
				{
				echo ("no");
				exit();
				die();
				}

				$j=1;
				//ciclo le risposte
				while($j<$numerofields_items)
				{
				
					$item_n=($i*1000)+$j;
					if (isset($_POST['descrizione'.$item_n]))
					{
						$item=pulisci($_POST['descrizione'.$item_n]);
						$valore_risposta=$_POST['valore'.$item_n];
						$query = "INSERT INTO test_clinici_items (idprova,etichetta,valore) VALUES($idprova,'$item','$valore_risposta')";
						 if(trim($item)!="")
						 {
						 $result1 = mssql_query($query, $conn);
							$query = "SELECT MAX(iditem) FROM test_clinici_items";
							$result1 = mssql_query($query, $conn);
						if($row = mssql_fetch_row($result1))
							$iditem=$row[0];	
						else
						{
						echo ("no");
						exit();
						die();
						}
							
						 }//end if items valorizzati
						
					}//end if items postati
				
				$j++;
				}//end while risposte
				
				$z=1;
				while($z<$numerofields_limiti)
				{
					$limit_n=($i*1000)+$z;
					//echo($_POST['limite_normativo'.$limit_n]);
					if (isset($_POST['limite_normativo'.$limit_n]))
					{
					$limit=$_POST['limite_normativo'.$limit_n];
					$punteggio=$_POST['punteggio'.$limit_n];
					$query = "INSERT INTO test_clinici_limiti (iditem,ideta,valore) VALUES($iditem,$limit,$punteggio)";
					//echo($query);
					 if(trim($punteggio)!="")
					 {
					 $result1 = mssql_query($query, $conn);
					 }//end if limiti valorizzati in termini di punteggio
					}//end if limiti postati
					$z++;
				}//end while limiti
		
		
		} //end if etichetta valorizzata
	  }//end if valore prove postate
	//$k++;
	$i++;
	} // end while prove

	echo("ok;;1;lista_test_clinici.php");	
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

