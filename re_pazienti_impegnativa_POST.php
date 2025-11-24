<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
session_start();
 

 
function create_nota()
{
$idpaziente=$_POST['id'];
$conn = db_connect();
$opeins = $_SESSION['UTENTE']->get_userid();
$nota=$_POST['nota'];
$nota=pulisci($nota);
$query ="insert into note_amministrative (idutente,opeins,nota) values ($idpaziente,$opeins,'$nota')";	
$result = mssql_query($query, $conn);
		if(!$result)
		{
				echo("no");
				exit();
				die();
		}
		$query = "SELECT MAX(idnota) FROM note_amministrative WHERE (opeins=$opeins)";
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
			
		$idnota=$row[0];	
		scrivi_log ($idnota,'note_amministrative','ins','idnota');
		
		echo("ok;".$idpaziente.";3;re_pazienti_amministrativa.php?do=show_note&id=".$idpaziente);
	exit();

}
 
 
 /************************************************************************
* funzione update()         						*
*************************************************************************/


/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_files($id,$id_paz) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "UPDATE utenti_allegati_imp SET cancella='y' WHERE id='".$id."'";	
	$result = mssql_query($query, $conn);
	echo("ok;".$id_paz.";3;re_pazienti_amministrativa.php?do=show_allegati&id=".$id_paz);	
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}



/************************************************************************
* funzione add_files()         						*
*************************************************************************/
function add_files($id) {

	$ope = $_SESSION['UTENTE']->get_userid();
	$id_utente=$id;	
	$tablename="utenti_allegati_imp";
	$conn = db_connect();
	ini_set('memory_limit', '100M');   
	for( $i = 1; $i < 10; $i++){
		$descrizione = str_replace("'","''",$_POST['et'.$i.'']);
		$impegnativa = $_POST['idimp'.$i.''];
		$data_prod = $_POST['data'.$i.''];
		if(isset($_POST['stampa'.$i.'']))
			$stampa="on";
			else
			$stampa="";
		$data=date(dmyHis);
		$nome_file=str_replace(" ","_",$_FILES['ulfile'.$i.'']['name']);
		$userfile_size = $_FILES['ulfile'.$i.'']['size'];
		$max_file=1;
		if ($userfile_size > ($max_file*50048576)) {
			$error.= "Images must be under ".$max_file."MB in size";
		}
		
		$file = $id."_".$data."_".$nome_file;
		$type = $_FILES['ulfile'.$i.'']['type'];	
		if ($descrizione!=""){
			$query = "INSERT INTO $tablename (IdUtente,descrizione,file_name,tipo,type,opeins,idimpegnativa,stampa,data_produzione) VALUES('$id','$descrizione','$file ',1,'$type',$ope,$impegnativa,'$stampa','$data_prod')";			
			$result = mssql_query($query, $conn);
								
			if ((trim($_FILES['ulfile'.$i.'']['name']) != "")) {	
			//$nomefile = $_FILES['img_src']['name'];
			
			$upload_dir = ALLEGATI_UTENTI_IMP;		// The directory for the images to be saved in
			$upload_path = $upload_dir."/";				// The path to where the image will be saved			
			$large_image_location = $upload_path.$file;
			$userfile_tmp = $_FILES['ulfile'.$i.'']['tmp_name'];
			//echo($large_image_location);			
			move_uploaded_file($userfile_tmp, $large_image_location);
			//chmod($large_image_location, 0777);
			
			}
			$query = "SELECT MAX(id) FROM $tablename WHERE (opeins='$ope')";
			$result = mssql_query($query, $conn);
			if(!$result) error_message(mssql_error());

			if(!$row = mssql_fetch_row($result))
				die("errore MAX select");
				
			$id_all=$row[0];
			scrivi_log ($id_all,$tablename,'ins','id');
		}
	}	
	echo("ok;".$id_utente.";3;re_pazienti_amministrativa.php?do=show_allegati&id=".$id_utente);	
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}


function create($id)
{
$tablename="impegnative";
$conn = db_connect();
$idutente=$_POST['idutente'];

   
   foreach ($_POST as $k => $v) {
   if (($k!="idpacchetto_desc") and ($k!="action") and ($k!="nomandatory") and ($k!="op") and ($k!="debug") and (substr($k,0,11)!="pac_terapia") and (substr($k,0,5)!="frequ") and (substr($k,0,8)!="richiest") and (substr($k,0,8)!="etichett") and ($k!="menu") and ($k!="PHPSESSID") and ($k!="usrPUBLIACI") and ($k!="headerbar"))
      if ($v!='')
	 {
	 $campi.=$k.',';
	 
	 if ($k=="ticket"){ 
		$v=str_replace(",", ".",$v);
	}
	 $valori.="'".pulisci($v)."',";
	 } else{
	if($k=="ticket"){
		$campi.=$k.',';
		$valori.="'0.00',";
	} 
   }
   }
   
    $data = date('d/m/Y');
	$ora = date('H:i');
	$ip = getenv('REMOTE_ADDR');
	$ope = $_SESSION['UTENTE']->get_userid();
   
   $campi.="datains".',';
   $campi.="orains".',';
   $campi.="opeins".',';
   $campi.="ipins".',';
   
   $valori.="'".$data."',";
   $valori.="'".$ora."',";
   $valori.="'".$ope."',";
   $valori.="'".$ip."',";
   
   
	$lc=strlen($campi)-1;
	$lv=strlen($valori)-1;
	$campi=substr($campi, 0, $lc); // returns "d"
	$valori=substr($valori, 0, $lv); // returns "d"



$query="insert into impegnative (".$campi.") values (".$valori.")";
$result = mssql_query($query, $conn);

$stato_paziente=get_stato_paziente($idutente);
if (!($stato_paziente>0)) $stato_paziente=0;
$query="UPDATE utenti SET stato_impegnativa=$stato_paziente WHERE idutente=$idutente";
$rs1 = mssql_query($query, $conn);

$ope = $_SESSION['UTENTE']->get_userid();
$query = "SELECT MAX(idimpegnativa) as id FROM impegnative WHERE (opeins=$ope)";
//echo($query);
$result = mssql_query($query, $conn);
$row=mssql_fetch_assoc($result);
$idimpegnativa=$row['id'];
		
	
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
	  if ((isset($_POST['etichetta'.$i])) and ($_POST['etichetta'.$i]!='0'))
	  {
	  $i=$arr_ordine[$k];
	  
		$data = date('d/m/Y');
		$ora = date('H:i');	
	  $etichetta=$_POST['etichetta'.$i];
	  $richiesti=trim($_POST['richiesti'.$i]);
	  $frequenza=trim($_POST['frequenza'.$i]);
	  $pacchetto=trim($_POST['pac_terapia'.$i]);
	if($richiesti=="") $richiesti=0;	
	  $peso++;	
	  //$versione=1;     
	 $query = "INSERT INTO RicetteCodici (IdRicetta,Frequenza,CodicePrestazione,QuantitaRichieste,DataInserimento,OraInserimento,OpeIns,pacchetto) VALUES($idimpegnativa,$frequenza,$etichetta,$richiesti,'$data','$ora','$ope','$pacchetto')";		
	
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
		
if(!$result) echo ("no");
else echo("ok;".$idutente.";3;re_pazienti_amministrativa.php?do=");	

exit();


}

function cancella_imp($imp,$user){

$conn = db_connect();
$query="DELETE FROM impegnative WHERE (idimpegnativa=".$imp.")";
//echo($query);
$result= mssql_query($query, $conn);
?>
<script type="text/javascript" id="js">
$("#layer_nero2").toggle();
	$('#lista_impegnative').innerHTML="";
	pagina_da_caricare="re_pazienti_amministrativa.php?do=&id="+<?=$user?>;
	$("#lista_impegnative").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });
 </script>
<?


//echo("ok;".$user.";3;re_pazienti_amministrativa.php?do=");	
exit();
}


function update($id)
{
$tablename="impegnative";
$conn = db_connect();
$protocollo_dt=$_POST['protocollo_dt'];
$successo_trattamento=$_POST['successo_trattamento'];
$query="update impegnative set protocollo_dt='', successo_trattamento='' where idimpegnativa=$id";
//echo($query);
$result = mssql_query($query, $conn);
  
foreach ($_POST as $k => $v) {
   if (($k!="idpacchetto_desc") and ($k!="action") and ($k!="op") and ($k!="id") and ($k!="nomandatory") and ($k!="debug") and (substr($k,0,11)!="pac_terapia") and (substr($k,0,5)!="frequ") and (substr($k,0,7)!="idcampo") and (substr($k,0,7)!="elimina") and (substr($k,0,8)!="richiest") and (substr($k,0,8)!="etichett") )
   {
     $campi=$k.'=';	
	 if (trim($v)!='')
	 {
	 if ($k=="ticket") $v=str_replace(",", ".",$v);		
	 $v=str_replace("'","''",$v);	 
	 $valori="'".stripslashes($v)."'";
	 //$valori=pulisci($v);
	 }
	 else
	 if ($k=="ticket"){ 
		$valori="0.00";
	}else{
		$valori="NULL";
	}
	 $upd.=$campi.$valori.",";
   }
} 
  
    $data = date('d/m/Y');
	$ora = date('H:i');
	$ip = getenv('REMOTE_ADDR');
	$ope = $_SESSION['UTENTE']->get_userid();
   
   $campi="dataagg".'=';
   $valori="'".addslashes($data)."'";
   $upd.=$campi.$valori.",";
	
   $campi="oraagg".'=';
   $valori="'".addslashes($ora)."'";
   $upd.=$campi.$valori.",";
  
   $campi="opeagg".'=';
   $valori="'".addslashes($ope)."'";
   $upd.=$campi.$valori.",";
   
   $campi="ipagg".'=';
   $valori="'".addslashes($ip)."'";
   $upd.=$campi.$valori.",";
  
  
	$lc=strlen($upd)-1;
	$upd=substr($upd, 0, $lc); // returns "d"


$query="update impegnative set ".$upd." where idimpegnativa=$id";
$result = mssql_query($query, $conn);

$query="select idutente from impegnative where idimpegnativa=$id";
$result = mssql_query($query, $conn);

$rs = mssql_query($query, $conn);
if($row = mssql_fetch_assoc($rs))
	$idutente=$row['idutente'];


	$stato_paziente=get_stato_paziente($idutente);	
	if (!($stato_paziente>0)) $stato_paziente=0;
	$query="UPDATE utenti SET stato_impegnativa=$stato_paziente WHERE idutente=$idutente";
	$rs1 = mssql_query($query, $conn);

	
	$idimpegnativa=$id;	
		
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
	while($k<$numerofield){
		$idcampo_el=$_POST['idcampo'.$k];
		if ((isset($_POST['elimina'.$k])) and (trim($_POST['elimina'.$k])=='si')) {
			$elimina=$_POST['elimina'.$k];
			if(trim($elimina)=="si"){				
				$query = "DELETE FROM RicetteCodici WHERE (IdRicettaCodice=$idcampo_el)";
				//echo($query);				
				$result1 = mssql_query($query, $conn);
			}		
		}
		//echo($_POST['elimina'.$k]);
		if ((isset($_POST['etichetta'.$k])) and (trim($_POST['etichetta'.$k])!='0')and (($_POST['elimina'.$k])!='si')){
			$etichetta=$_POST['etichetta'.$k];
			if (trim($etichetta)!=""){		 	  
				$data = date('d/m/Y');
				$ora = date('H:i');
				$richiesti=trim($_POST['richiesti'.$k]);
				$frequenza=trim($_POST['frequenza'.$k]);
				$pacchetto=trim($_POST['pac_terapia'.$k]);
				if($richiesti=="") $richiesti=0;
				//echo("id".$idcampo_el);
				if($idcampo_el!=""){
					$query="SELECT * FROM RicetteCodici WHERE (IdRicettaCodice=$idcampo_el)";
					//echo($query);
					$result1 = mssql_query($query, $conn);
					if(mssql_num_rows($result1)==0){
						mssql_free_result($result1);						
						$query = "INSERT INTO RicetteCodici (IdRicetta,Frequenza,CodicePrestazione,QuantitaRichieste,DataInserimento,OraInserimento,OpeIns,pacchetto) VALUES($idimpegnativa,$frequenza,$etichetta,$richiesti,'$data','$ora','$ope','$pacchetto')";		
						//echo("--".$query);
						$result1 = mssql_query($query, $conn);
					}
					else{
						$row1=mssql_fetch_assoc($result1);
						if(($row1['Frequenza']!=$frequenza)or($row1['CodicePrestazione']!=$etichetta)or($row1['QuantitaRichieste']!=$richiesti)){
							$query="UPDATE RicetteCodici SET Frequenza=$frequenza, CodicePrestazione=$etichetta, QuantitaRichieste=$richiesti, DataInserimento='$data', OraInserimento='$ora', OpeIns='$ope', pacchetto=$pacchetto  WHERE IdRicettaCodice=$idcampo_el";
							//echo($query);
							//$result1 = mssql_query($query, $conn);
						}				
					}
				}else{
					$query = "INSERT INTO RicetteCodici (IdRicetta,Frequenza,CodicePrestazione,QuantitaRichieste,DataInserimento,OraInserimento,OpeIns,pacchetto) VALUES($idimpegnativa,$frequenza,$etichetta,$richiesti,'$data','$ora','$ope','$pacchetto')";		
					//echo("**".$query);
					$result1 = mssql_query($query, $conn);
				}		
			}
		}
		$k++;
	}	

echo("ok;".$idutente.";3;re_pazienti_amministrativa.php?do=");	
//echo("ok");
exit();




}



if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			case "create":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create($_POST['idutente']);					
			break;
			
			case "create_nota":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_nota();					
			break;

			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_POST['id']);
			break;

			case "del":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else cancella_imp($_REQUEST['id'],$_REQUEST['idutente']);
			break;
			
			case "add_files":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else add_files($_POST['id']);
			break;
			
			case "del_files":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_files($_POST['id_allegato'],$_POST['id_paziente']);
			break;
		}
		if($_REQUEST['action']=="del_files"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_files($_REQUEST['id_all'],$_REQUEST['paz']);
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
			
			case "review":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else review($_REQUEST['id']);
			break;
			
			case "del":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else cancella_imp($_REQUEST['id'],$_REQUEST['idutente']);
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
	print("ciao");
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>