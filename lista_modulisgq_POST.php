<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 46;
$tablename = 'utenti';
$mod_type=2;
include_once('include/function_page.php');


function del_modulo($id) {
	global $tablename;
	$ope = $_SESSION['UTENTE']->get_userid();
	$conn = db_connect();
    $query = "DELETE FROM campi WHERE idmoduloversione=$id";	
	$result = mssql_query($query, $conn);
    $query = "DELETE FROM moduli_combo WHERE idmoduloversione=$id";	
	$result = mssql_query($query, $conn);
    $query = "DELETE FROM moduli_combo_ramificate WHERE idmoduloversione=$id";	
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM moduli WHERE id=$id";	
	$result = mssql_query($query, $conn);
    
    exit();
}


/****************************
* funzione create()         *
*****************************/
function create() {
    
	global $mod_type;
	
	$nome = pulisci($_POST['nome']);
	$codice = pulisci($_POST['codice']);
	//$replica = $_POST['replica'];
	$disuso = $_POST['disuso'];
	$intestazione = pulisci($_POST['intestazione']);
	$descrizione = pulisci($_POST['descr']);
	$stampa_continua = $_POST['stampa_continua'];
	$lista_attesa = $_POST['lista_attesa'];
	$word = $_FILES['word']['name'];
	
	$status = $_POST['status'];
	
	
	$nome = str_replace("'","''",$nome);

	$conn = db_connect();
	
	
	$query = "SELECT MAX(idmodulo) as maxmodulo FROM moduli";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	if($row = mssql_fetch_assoc($result)){

		$maxmodulo = $row['maxmodulo']+1;
		
	}
	else
	{
		$maxmodulo = 1;
	}
	$versione=1;
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "INSERT INTO moduli (idmodulo,intestazione,nome,opeins,cancella,status,descrizione,stampa_continua,lista_attesa,versione,codice,replica,tipo,disuso) VALUES($maxmodulo,'$intestazione','$nome',$opeins,'n',1,'$descrizione','$stampa_continua','$lista_attesa','$versione','$codice',1,$mod_type,$disuso)";

	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM moduli WHERE (opeins=$opeins) and (tipo=$mod_type)";
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
		
	$idmodulo=$row[0];	
	$peso=0;	
	$i=1;
	$numerofield=100;
	$classe="";
	$word=str_replace(" ","_",$word);
	$word=$idmodulo."_".$word;
	$query = "UPDATE moduli SET modello_word='$word' WHERE (id=$idmodulo)";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}
	scrivi_log ($idmodulo,'moduli','ins','id');
	 
	if ((trim($_FILES['word']['name']) != "")) {	
		//$nomefile = $_FILES['img_src']['name'];
		
		$imgHNDL = fopen($_FILES['word']['tmp_name'], "r");
		$imgf = fread($imgHNDL, $_FILES['word']['size']);
		//$eimgf = mysql_escape_string($imgf);
	
		// apre la connessione ftp
		$conn_id = ftp_connect(IP_ADDR);	
	
		// login con user name e password
		$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 
	
		// controllo della connessione
		if ((!$conn_id) || (!$login_result)) { 
			echo "La connessione FTP  fallita!";
			echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 
			die; 	
		} else {
			
			// se il file non esiste, lo crea via ftp
			//if (!file_exists($curr_path.'/'.$filename)) {
	
				// passa in passive mode			
				ftp_pasv ( $conn_id, true );
				
				$temp = tmpfile();
				fwrite($temp, $imgf);
				fseek($temp, 0);
	
				// si posiziona nella directory giusta
				if (!ftp_chdir($conn_id, MODELLI_WORD)) {
					  {
		echo ("no");
		exit();
		die();
		}
					 exit;
				} 
				
				// upload del file
				if(! ($upload = ftp_fput ($conn_id, $word, $temp ,FTP_BINARY))) {
					  {
		echo ("no");
		exit();
		die();
		}
					 exit;
				}
				
				// chiude l'handler del file
				fclose($temp);
	
		}
	
		fclose($imgHNDL);
	
		// chiudere il flusso FTP 
		ftp_quit($conn_id);
		
		//riduci_foto("foto_pazienti/","foto_pazienti/",$foto_paziente,112,112);
		//ftp_del_file(FOTO_PAZIENTI."/".$foto_paziente);
		}
	
	
	
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
	 
	  
	  $etichetta=pulisci($_POST['etichetta'.$i]);
	  $segnalibro=pulisci($_POST['segnalibro'.$i]);
	  //$etichetta = str_replace("'","''",$etichetta);
	  //$segnalibro = str_replace("'","''",$segnalibro);
	  $peso++;
	  $tipo=$_POST['tipo'.$i];
	  $obbligatorio=$_POST['obbligatorio'.$i];
	  $editabile=$_POST['editabile'.$i];
	  $classe=$_POST['tipoclasse'.$i];
	  $versione=1;
     $query = "INSERT INTO campi (idmoduloversione,etichetta,peso,tipo,editabile,obbligatorio,classe,segnalibro,opeins) VALUES($idmodulo,'$etichetta',$peso,$tipo,'$editabile','$obbligatorio','$classe','$segnalibro','$opeins')";
		 if(trim($etichetta)!="")	{
		   $result1 = mssql_query($query, $conn);	  	
		if(!$result1)
		 {
		echo ("no");
		exit();
		die();
		}
		if($tipo==4){
		  // recupera l'id del record appena inserito
			$query = "SELECT MAX(idcampo) FROM campi WHERE opeins=$opeins";
			$result = mssql_query($query, $conn);
			if(!$result) {
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
				
			$idcampo=$row[0];
			$idcombo=$_POST['tipo_combo'.$i];			
		  $query = "INSERT INTO moduli_combo (idcampo,idmoduloversione,idcombo) VALUES($idcampo,$idmodulo,$idcombo)";		  
	      $result1 = mssql_query($query, $conn);
		}
		
		if($tipo==9){
		  // recupera l'id del record appena inserito
			$query = "SELECT MAX(idcampo) FROM campi WHERE opeins=$opeins";
			$result = mssql_query($query, $conn);
			if(!$result) {
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
				
			$idcampo=$row[0];
			$idcombo=$_POST['tipo_combo_ram'.$i];			
		  $query = "INSERT INTO moduli_combo_ramificate (idcampo,idmoduloversione,idcombo) VALUES($idcampo,$idmodulo,$idcombo)";		  
	      $result1 = mssql_query($query, $conn);
		}
		
		
		
		}
	  }
	
	
	
	
	$k++;
	}

	echo("ok;;1;lista_modulisgq.php");	
	exit();
	// scrive il log
	//scrivi_log ($idmodulo,$tablename,'ins');

}


function update($id) {
    global $mod_type;
	
	$nome = pulisci($_POST['nome']);
	$descrizione = pulisci($_POST['descr']);
	$intestazione = pulisci($_POST['intestazione']);
	$stampa_continua = $_POST['stampa_continua'];
	$lista_attesa = $_POST['lista_attesa'];
	$codice = pulisci($_POST['codice']);
	//$replica = $_POST['replica'];
	$disuso = $_POST['disuso'];	
	$status = $_POST['status'];
	$conn = db_connect();
	
	$query = "SELECT  idmodulo,versione FROM moduli where id=$id";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}

	if($row = mssql_fetch_assoc($result)){

		$versione = $row['versione']+1;
		$idmodulo=$row['idmodulo'];
		
	}
	else
	{
		$maxmodulo = 1;
	}
	
	$opeins=$_SESSION['UTENTE']->get_userid();


	$query = "INSERT INTO moduli (idmodulo,intestazione,nome,opeins,cancella,status,descrizione,stampa_continua,lista_attesa,versione,codice,replica,tipo,disuso) VALUES($idmodulo,'$intestazione','$nome',$opeins,'n',1,'$descrizione','$stampa_continua','$lista_attesa','$versione','$codice',1,$mod_type,$disuso)";	
	$result = mssql_query($query, $conn);
	if(!$result) 
	{
	echo("no;;;");
	exit();
	}

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM moduli WHERE (opeins=$opeins) and (tipo=$mod_type)";
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
		
	$idmodulo=$row[0];	
	$peso=0;	
	$i=1;
	$numerofield=100;
	$classe="";
	if ($_FILES['word']['name']==""){
		$word = $_POST['nome_mod'];	
		}
		else{
		$word=str_replace(" ","_",$_FILES['word']['name']);
		$word=$idmodulo."_".$word;
		}
	
	$query = "UPDATE moduli SET modello_word='$word' WHERE (id=$idmodulo)";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}
	scrivi_log ($idmodulo,'moduli','agg','id');
	
	if ((trim($_FILES['word']['name']) != "")) {	
		
		
		$upload_dir = MODELLI_WORD;		// The directory for the images to be saved in
		$upload_path = $upload_dir."/";				// The path to where the image will be saved			
		$large_image_location = $upload_path.$word;
		$userfile_tmp = $_FILES['word']['tmp_name'];
		//echo($large_image_location);			
		move_uploaded_file($userfile_tmp, $large_image_location);
		//chmod($large_image_location, 0777);
		
	}

	
	/*if ((trim($_FILES['word']['name']) != "")) {
		$imgHNDL = fopen($_FILES['word']['tmp_name'], "r");
		$imgf = fread($imgHNDL, $_FILES['word']['size']);	
	
		// apre la connessione ftp
		$conn_id = ftp_connect(IP_ADDR);	
	
		// login con user name e password
		$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 
	
		// controllo della connessione
		if ((!$conn_id) || (!$login_result)) { 
			echo "La connessione FTP è fallita!";
			echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 
			die();
		} 
		else {			
			// passa in passive mode			
			ftp_pasv ( $conn_id, true );			
			$temp = tmpfile();
			fwrite($temp, $imgf);
			fseek($temp, 0);

			// si posiziona nella directory giusta
			if (!ftp_chdir($conn_id, MODELLI_WORD)) {
					echo ("no");
					exit();
					die();						
			}
			// upload del file
			if(! ($upload = ftp_fput ($conn_id, $word, $temp ,FTP_BINARY))) {
					echo ("no");
					exit();
					die();
			}			
			// chiude l'handler del file
			fclose($temp);	
		}
	
		fclose($imgHNDL);
	
		// chiudere il flusso FTP 
		ftp_quit($conn_id);
	}
	*/
	
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
	  
	  if ((isset($_POST['etichetta'.$i])) and (trim($_POST['etichetta'.$i])!=''))
	  {
		 
		  $etichetta=pulisci($_POST['etichetta'.$i]);
		  $segnalibro=pulisci($_POST['segnalibro'.$i]);
		  //$etichetta = str_replace("'","''",$etichetta);
		  //$segnalibro = str_replace("'","''",$segnalibro);
		  //$etichetta=$_POST['etichetta'.$i];
		  //$segnalibro=$_POST['segnalibro'.$i];		  
		  $peso++;
		  $tipo=$_POST['tipo'.$i];
		  $obbligatorio=$_POST['obbligatorio'.$i];
		  $editabile=$_POST['editabile'.$i];
		  $classe=$_POST['tipoclasse'.$i];
	 
		  $query = "INSERT INTO campi (idmoduloversione,etichetta,peso,tipo,editabile,obbligatorio,classe,segnalibro,opeins) VALUES($idmodulo,'$etichetta',$peso,$tipo,'$editabile','$obbligatorio','$classe','$segnalibro','$opeins')";
	       if(trim($etichetta)!="")	{
		   $result1 = mssql_query($query, $conn);
		if(!$result1) {
		
			echo("no");
			exit();
			die();
		}		
		if($tipo==4){
		  // recupera l'id del record appena inserito
			$query = "SELECT MAX(idcampo) FROM campi WHERE opeins=$opeins";
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
				
			$idcampo=$row[0];
			$idcombo=$_POST['tipo_combo'.$i];			
		  $query = "INSERT INTO moduli_combo (idcampo,idmoduloversione,idcombo) VALUES($idcampo,$idmodulo,$idcombo)";		  
	      $result1 = mssql_query($query, $conn);
		}
		
		if($tipo==9){
		  // recupera l'id del record appena inserito
			$query = "SELECT MAX(idcampo) FROM campi WHERE opeins=$opeins";
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
				
			$idcampo=$row[0];
			$idcombo=$_POST['tipo_combo_ram'.$i];			
		  $query = "INSERT INTO moduli_combo_ramificate (idcampo,idmoduloversione,idcombo) VALUES($idcampo,$idmodulo,$idcombo)";		  
	      $result1 = mssql_query($query, $conn);
		}
		
		
		}
		}
	$k++;
	}

	echo("ok;;1;lista_modulisgq.php");		
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
        if($_REQUEST['action']=="del_modulo"){
			   if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_modulo($_REQUEST['id']);
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

