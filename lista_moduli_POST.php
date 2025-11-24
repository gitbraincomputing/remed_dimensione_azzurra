<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 1;
$tablename = 'utenti';
$mod_type=1;
$destination_path =MODELLI_WORD_DEST_PATH;
include_once('include/function_page.php');


function del_modulo($id) {
	global $tablename;
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM campi WHERE idmoduloversione='".$id."'";	
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM moduli WHERE id=".$id;		
	$result = mssql_query($query, $conn);	
	exit();
}


/****************************
* funzione create()         *
*****************************/
function create() {
    
	global $mod_type, $destination_path;
	$cont_add=0;
	$cont_mod=0;
	$cont_del=0;	
	$txt_campi="";	
	$nome = pulisci($_POST['nome']);
	$codice = pulisci($_POST['codice']);
	$replica = $_POST['replica'];
	$disuso = $_POST['disuso'];
	$intestazione = pulisci($_POST['intestazione']);
	$descrizione = pulisci($_POST['descr']);
	$stampa_continua = $_POST['stampa_continua'];
	$lista_attesa = $_POST['lista_attesa'];
	$word = $_FILES['word']['name'];	
	$status = $_POST['status'];	
	$edit_all_post = $_POST['edit_all'];
	if(isset($edit_all_post))	
		$edit_all = 1;	else  $edit_all = 0;
	
	if(isset($_POST['secondo_allegato']))	
		$secondo_allegato = 1;	else $secondo_allegato = 0;
	
	if(isset($_POST['terzo_allegato']))
		$terzo_allegato = 1;	else 	$terzo_allegato = 0;
	
	if(isset($_POST['quarto_allegato']))
		$quarto_allegato = 1;	else 	$quarto_allegato = 0;
		
	$firma_digitale_post = $_POST['firma_digitale'];
	if(isset($firma_digitale_post))
		$firma_digitale = 1;	else $firma_digitale = 0;
	
	$firma_fea_post = $_POST['firma_fea'];
	if(isset($firma_fea_post))
		$firma_fea = 1;	else $firma_fea = 0;
		

	$conn = db_connect();
	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$datains = date('d/m/Y');
	
	
	$nome_t=str_replace("\'","'",$_POST['nome']);	
	$txt_campi.="campo <b>Nome Modulo</b> -> valore attuale: ".$_POST['nome']."\n";
	$desc_t=str_replace("\'","'",$_POST['descr']);	
	$txt_campi.="campo <b>Descrizione Modulo</b> -> valore attuale: ".$_POST['descr']."\n";
	$int_t=str_replace("\'","'",$_POST['intestazione']);	
	$txt_campi.="campo <b>Intestazione Modulo</b> -> valore attuale: ".$int_t."\n";	
	$txt_campi.="campo <b>Stampa Continua</b> -> valore attuale: ".$stampa_continua."\n";
	$cod_t=str_replace("\'","'",$_POST['codice']);	
	$txt_campi.="campo <b>Codice SGQ/b> -> valore attuale: ".$_POST['codice']."\n";	
	$txt_campi.="campo <b>Replica</b> -> valore attuale: ".$replica."\n";	
	$txt_campi.="campo <b>Disuso</b> -> valore attuale: ".$disuso."\n";
		
	$query="SELECT uid,nome FROM operatori WHERE uid=$opeins";
	$result = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($result);
	$operatore=$row['nome'];	
	
	$txt_titolo="1<Creato il ".formatta_data($datains)." - operatore: ".$operatore.">";
	$txt_testata="data: ".formatta_data($datains)." - ora: ".date('H:i')."\n";
	$txt_testata.="operatore: ".$operatore."\n";
	$txt_testata.="nome modulo: ".$nome_t."\n";
	$txt_testata.="codice sgq: ".$cod_t."\n";
	
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
	$txt_testata.="versione: ".$versione."\n";
	$tmp_file="audit_mod_".$maxmodulo.".txt";
	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$query = "INSERT INTO moduli (idmodulo,intestazione,nome,opeins,cancella,
									status,descrizione,stampa_continua,lista_attesa,versione,
									codice,replica,tipo,disuso,edit_all,
									secondo_allegato,terzo_allegato,quarto_allegato,firma_digitale,firma_fea) 
				VALUES($maxmodulo,'$intestazione','$nome',$opeins,'n',
						1,'$descrizione','$stampa_continua','$lista_attesa','$versione',
						'$codice',$replica,$mod_type,$disuso, $edit_all, 
						$secondo_allegato, $terzo_allegato, $quarto_allegato, $firma_digitale, $firma_fea)";

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
	$numerofield=200;
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
	
	$txt_campi.="campo <b>Modello Word</b> -> valore attuale: ".$word."\n";
	
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
		while($j<$numerofield)
		$arr_ordine[$j]=$j+1;
	
	}
	
	
	$numerofield=sizeof($arr_ordine);
	
	$txt_campi.="\nCampi di Dettaglio\n\n";
	
	$k=0;
	while($k<$numerofield)
	{
	
	  if ((isset($_POST['etichetta'.$i])) and ($_POST['etichetta'.$i]!=''))
	  {
	  $i=$arr_ordine[$k];	 
	  
	  $etichetta=pulisci($_POST['etichetta'.$i]);
	  $etichetta_t=str_replace("\'","'",$_POST['etichetta'.$i]);
	  $segnalibro=pulisci($_POST['segnalibro'.$i]);	  
	  $peso++;
	  $tipo=$_POST['tipo'.$i];
	  $obbligatorio=$_POST['obbligatorio'.$i];
	  $editabile=$_POST['editabile'.$i];
	  $classe=$_POST['tipoclasse'.$i];
	  $versione=1;
		$query = "INSERT INTO campi (idmoduloversione,etichetta,peso,tipo,editabile,obbligatorio,classe,segnalibro,opeins) VALUES($idmodulo,'$etichetta',$peso,$tipo,'$editabile','$obbligatorio','$classe','$segnalibro','$opeins')";
	if(trim($etichetta)!="")	{
		   $result1 = mssql_query($query, $conn);	  	
		   $txt_campi.="campo: <b>$etichetta_t</b> - segnalibro: $segnalibro - tipo: $tipo - classe: $classe - peso: $peso\n";		
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
	$txt_cont=$txt_titolo."\n\n".$txt_testata."\n\n";
	$txt_cont.="\n<b>Versione attualmente in essere</b>:\n";
	$txt_cont.=$txt_campi;
	$txt_cont.="\n----- fine\n#NP\n";
	
	$txt_cont=str_replace("<br/>","\n",$txt_cont);
	$txt_cont=str_replace("<br","",$txt_cont);
	$handle = fopen($destination_path.$tmp_file, "w");
	fwrite($handle,$txt_cont);

	echo("ok;;1;lista_moduli.php");	
	exit();
	// scrive il log
	//scrivi_log ($idmodulo,$tablename,'ins');

}

function update($id) {
    global $mod_type,$destination_path;
	
	$conn = db_connect();	
	$cont_add=0;
	$cont_mod=0;
	$cont_del=0;
	$txt_add="";
	$txt_mod="";
	$txt_del="";
	$txt_campi="";	
	$nome = pulisci($_POST['nome']);
	$descrizione = pulisci($_POST['descr']);
	$intestazione = pulisci($_POST['intestazione']);
	$stampa_continua = $_POST['stampa_continua'];
	$lista_attesa = $_POST['lista_attesa'];
	$codice = pulisci($_POST['codice']);
	$replica = $_POST['replica'];
	$disuso = $_POST['disuso'];	
	$status = $_POST['status'];	
	$crea_versione = $_POST['crea_versione'];	
	$opeins=$_SESSION['UTENTE']->get_userid();
	$datains = date('d/m/Y');
	$edit_all_post = $_POST['edit_all'];
	if(isset($edit_all_post))	
		$edit_all = 1;	else  $edit_all = 0;
	
	if(isset($_POST['secondo_allegato']))
		$secondo_allegato = 1;	else 	$secondo_allegato = 0;
	
	if(isset($_POST['terzo_allegato']))
		$terzo_allegato = 1;	else 	$terzo_allegato = 0;
	
	if(isset($_POST['quarto_allegato']))
		$quarto_allegato = 1;	else 	$quarto_allegato = 0;
	
	$firma_digitale_post = $_POST['firma_digitale'];
	if(isset($firma_digitale_post)) $firma_digitale = 1;	else $firma_digitale = 0;
	
	$firma_fea_post = $_POST['firma_fea'];
	if(isset($firma_fea_post)) $firma_fea = 1;	else $firma_fea = 0;
	
	
	$query="SELECT * FROM moduli WHERE id=$id";
	$result = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($result);
	$word_old=$row['modello_word'];
	$nome_t=str_replace("\'","'",$_POST['nome']);
	if($row['nome']!=$nome_t) {$txt_mod.="campo <b>Nome Modulo</b> -> valore precedente: ".$row['nome']." -> valore attuale: ".$nome_t."\n"; $cont_mod++;}
	$txt_campi.="campo <b>Nome Modulo</b> -> valore attuale: ".$nome_t."\n";
	$desc_t=str_replace("\'","'",$_POST['descr']);
	if($row['descrizione']!=$desc_t) {$txt_mod.="campo <b>Descrizione Modulo</b> -> valore precedente: ".$row['descrizione']." -> valore attuale: ".$desc_t."\n";$cont_mod++;}
	$txt_campi.="campo <b>Descrizione Modulo</b> -> valore attuale: ".$desc_t."\n";
	$int_t=str_replace("\'","'",$_POST['intestazione']);
	if($row['intestazione']!=$int_t) {$txt_mod.="campo <b>Intestazione Modulo</b> -> valore precedente: ".$row['intestazione']." -> valore attuale: ".$int_t."\n";$cont_mod++;}
	$txt_campi.="campo <b>Intestazione Modulo</b> -> valore attuale: ".$int_t."\n";
	if($row['stampa_continua']!=$stampa_continua) {$txt_mod.="campo <b>Stampa Continua</b> -> valore precedente: ".$row['stampa_continua']." -> valore attuale: ".$stampa_continua."\n";$cont_mod++;}
	$txt_campi.="campo <b>Stampa Continua</b> -> valore attuale: ".$stampa_continua."\n";
	$cod_t=str_replace("\'","'",$_POST['codice']);
	if($row['codice']!=$cod_t) {$txt_mod.="campo <b>Codice SGQ/b> -> valore precedente: ".$row['codice']." -> valore attuale: ".$cod_t."\n";$cont_mod++;}
	$txt_campi.="campo <b>Codice SGQ</b> -> valore attuale: ".$cod_t."\n";
	if($row['replica']!=$replica) {$txt_mod.="campo <b>Replica</b> -> valore precedente: ".$row['replica']." -> valore attuale: ".$replica."\n";$cont_mod++;}
	$txt_campi.="campo <b>Replica</b> -> valore attuale: ".$replica."\n";
	if($row['disuso']!=$disuso) {$txt_mod.="campo <b>Disuso</b> -> valore precedente: ".$row['disuso']." -> valore attuale: ".$disuso."\n";$cont_mod++;}
	$txt_campi.="campo <b>Disuso</b> -> valore attuale: ".$disuso."\n";
		
	$query="SELECT uid,nome FROM operatori WHERE uid=$opeins";
	$result = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($result);
	$operatore=$row['nome'];	
	
	$txt_titolo="\n\n1<Rev. del ".formatta_data($datains)." - operatore: ".$operatore.">";
	$txt_testata="data: ".formatta_data($datains)." - ora: ".date('H:i')."\n";
	$txt_testata.="operatore: ".$operatore."\n";
	$txt_testata.="nome modulo: ".$nome_t."\n";
	$txt_testata.="codice sgq: ".$cod_t."\n";
	
	$query="SELECT * FROM campi WHERE (idmoduloversione=$id) order by peso ASC";	
	$rs1 = mssql_query($query, $conn);
	while($row1=mssql_fetch_assoc($rs1)){
		$txt_del.="campo: <b>".$row1['etichetta']."</b>\n";
		//echo($txt_del);
		$cont_del++;
	}
	
	
	$query = "SELECT  idmodulo,versione FROM moduli where id=$id";
	$result = mssql_query($query, $conn);
	
	if($row = mssql_fetch_assoc($result)) {
		if($crea_versione=="0") {
			$versione = $row['versione']+1;	
		} else {
			$versione = $row['versione'];
		}	
		$idmodulo=$row['idmodulo'];
	} else {
		$maxmodulo = 1;
	}
	
	$txt_testata.="versione: ".$versione."\n";
	$tmp_file="audit_mod_".$idmodulo.".txt";

	if($crea_versione=="0")
		$query = "INSERT INTO moduli (idmodulo,intestazione,nome,opeins,cancella,
										status,descrizione,stampa_continua,lista_attesa,versione,
										codice,replica,tipo,disuso,edit_all,
										secondo_allegato,terzo_allegato,quarto_allegato,firma_digitale,firma_fea) 
					VALUES($idmodulo,'$intestazione','$nome',$opeins,'n',
							1,'$descrizione','$stampa_continua','$lista_attesa','$versione',
							'$codice',$replica,$mod_type,$disuso,$edit_all,
							$secondo_allegato,$terzo_allegato,$quarto_allegato,$firma_digitale,$firma_fea)";	
	else
		$query = "UPDATE moduli 
					SET intestazione='$intestazione',nome='$nome',opeins=$opeins,descrizione='$descrizione',
						stampa_continua='$stampa_continua',lista_attesa='$lista_attesa',codice='$codice',
						replica=$replica,tipo=$mod_type,disuso=$disuso,edit_all=$edit_all,
						secondo_allegato=$secondo_allegato,terzo_allegato=$terzo_allegato,quarto_allegato=$quarto_allegato,
						firma_digitale=$firma_digitale,firma_fea=$firma_fea
					WHERE id=$id";	
	$result = mssql_query($query, $conn);
	
	if(!$result) 
	{
	echo("no;;; $query");
	exit();
	}

	if($crea_versione=="0"){
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
	}else{
		$idmodulo=$id;
	}
	$peso=0;	
	$i=1;
	$numerofield=200;
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
	
	if($word_old!=$word) {$txt_mod.="campo <b>Modello Word</b> -> valore precedente: ".$word_old." -> valore attuale: ".$word."\n";$cont_mod++;}
	$txt_campi.="campo <b>Modello Word</b> -> valore attuale: ".$word."\n";
	
	if($crea_versione=="0") scrivi_log ($idmodulo,'moduli','agg','id');
	
	if ((trim($_FILES['word']['name']) != "")) {
		$upload_dir = MODELLI_WORD;		// The directory for the images to be saved in
		$upload_path = $upload_dir."/";				// The path to where the image will be saved			
		$large_image_location = $upload_path.$word;
		$userfile_tmp = $_FILES['word']['tmp_name'];			
		move_uploaded_file($userfile_tmp, $large_image_location);
	}

	$arr_ordine=Array();
	if (isset($_POST['debug']))
	{
	$ordine=$_POST['debug'];	
	if(strrpos($ordine, ",")>0){
		$arr_ordine=split(",",$ordine);
	}
		else
		$arr_ordine=split(" ",trim($ordine));	
	}
	else
	{
		while($j<$numerofield)
		$arr_ordine[$j]=$j+1;	
	}
	
	$numerofield=sizeof($arr_ordine);	
	
	$k=0;
	$i=1;
	$txt_campi.="\nCampi di Dettaglio\n\n";
	
	while($k<$numerofield)
	{
	  $i=$arr_ordine[$k];	 
	  
	  if ((isset($_POST['etichetta'.$i])) and (trim($_POST['etichetta'.$i])!=''))
	  {
		 
		  $etichetta=pulisci($_POST['etichetta'.$i]);
		  $etichetta_t=str_replace("\'","'",$_POST['etichetta'.$i]);
		  $segnalibro=pulisci($_POST['segnalibro'.$i]);		  		  
		  $peso++;
		  $tipo=$_POST['tipo'.$i];
		  $obbligatorio=$_POST['obbligatorio'.$i];
		  $editabile=$_POST['editabile'.$i];
		  $classe=$_POST['tipoclasse'.$i];
	 
		  if($crea_versione=="0"){
				$query = "INSERT INTO campi (idmoduloversione,etichetta,peso,tipo,editabile,obbligatorio,classe,segnalibro,opeins) VALUES($idmodulo,'$etichetta',$peso,$tipo,'$editabile','$obbligatorio','$classe','$segnalibro','$opeins')";
				$txt_add.="campo: <b>$etichetta_t</b> - segnalibro: $segnalibro - tipo: $tipo - classe: $classe - peso: $peso\n";
				$cont_add++;
				$txt_campi.="campo: <b>$etichetta_t</b> - segnalibro: $segnalibro - tipo: $tipo - classe: $classe - peso: $peso\n";				
				}else{				
				$id_campo=$_POST['idcampo'.$i];
				$query = "UPDATE campi SET etichetta='$etichetta',peso=$peso,tipo=$tipo,editabile='$editabile',obbligatorio='$obbligatorio',classe='$classe',segnalibro='$segnalibro',opeins='$opeins' WHERE idcampo=$id_campo";	       
				$txt_add.="campo: <b>$etichetta_t</b> - segnalibro: $segnalibro - tipo: $tipo - classe: $classe - peso: $peso\n";
				$cont_add++;
				$txt_campi.="campo: <b>$etichetta_t</b> - segnalibro: $segnalibro - tipo: $tipo - classe: $classe - peso: $peso\n";
				}
		   if(trim($etichetta)!="")	{
				$result1 = mssql_query($query, $conn);
				if(!$result1) {				
					echo("no");
					exit();
					die();
				}		
		if($tipo==4){
		  // recupera l'id del record appena inserito
			if($crea_versione=="0"){	
				$query = "SELECT MAX(idcampo) FROM campi WHERE opeins=$opeins";
				$result = mssql_query($query, $conn);
				if(!$result) {
					echo ("no");
					exit();
					die();
				}
			
				if(!$row = mssql_fetch_row($result)) {
					echo ("no");
					exit();
					die();
				}				
				$idcampo=$row[0];			
			  $idcombo=$_POST['tipo_combo'.$i];			
			  $query = "INSERT INTO moduli_combo (idcampo,idmoduloversione,idcombo) VALUES($idcampo,$idmodulo,$idcombo)";		  
			  $result1 = mssql_query($query, $conn);
		  }
		}
		
		if($tipo==9){
			if($crea_versione=="0"){
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
		}
	$k++;
	}
	$txt_cont=$txt_titolo."\n\n".$txt_testata."\nElenco delle variazioni intervenute rispetto alla versione precedente:";
	$txt_cont.="\n<b>Elenco dei campi aggiunti</b>: ".$cont_add."\n";
	$txt_cont.=$txt_add;
	$txt_cont.="\n<b>Elenco dei campi eliminati</b>: ".$cont_del."\n";
	$txt_cont.=$txt_del;
	$txt_cont.="\n<b>Elenco dei campi modificati</b>: ".$cont_mod."\n";
	$txt_cont.=$txt_mod;
	$txt_cont.="\n<b>Versione attualmente in essere</b>:\n";
	$txt_cont.=$txt_campi;
	$txt_cont.="\n----- fine\n#NP\n";
	
	$txt_cont=str_replace("<br/>","\n",$txt_cont);
	$txt_cont=str_replace("<br","",$txt_cont);
	$handle = fopen($destination_path.$tmp_file, "a+");
	fwrite($handle,$txt_cont);

	echo("ok;;1;lista_moduli.php");		
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
					else del_modulo($_REQUEST['id_modulo']);
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

