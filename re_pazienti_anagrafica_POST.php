<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;	
session_start();
 
/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_files($id,$id_paz) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "UPDATE utenti_allegati SET cancella='y' WHERE id='".$id."'";	
	$result = mssql_query($query, $conn);
	echo("ok;;2;re_pazienti_anagrafica.php?do=show_allegati&id=".$id_paz);
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
	$tablename="utenti_allegati";
	$conn = db_connect();
	//$isroot = $_SESSION['UTENTE']->is_root();
	for( $i = 1; $i < 10; $i++){
		$descrizione = pulisci($_POST['et'.$i.'']);
		$data=date(dmyHis);
		$nome_file=str_replace(" ","_",$_FILES['ulfile'.$i.'']['name']);
		$userfile_size = $_FILES['ulfile'.$i.'']['size'];
		$max_file=1;
		if ($userfile_size > ($max_file*10048576)) {
			$error.= "Images must be under ".$max_file."MB in size";
		}
		
		$file = $id."_".$data."_".$nome_file;
		$type = $_FILES['ulfile'.$i.'']['type'];	
		if ($descrizione!=""){
			$query = "INSERT INTO $tablename (IdUtente,descrizione,file_name,tipo,type,opeins) VALUES('$id','$descrizione','$file ',1,'$type',$ope)";						
			$result = mssql_query($query, $conn);
								
			if ((trim($_FILES['ulfile'.$i.'']['name']) != "")) {	
			//$nomefile = $_FILES['img_src']['name'];
			
			$upload_dir = ALLEGATI_UTENTI;		// The directory for the images to be saved in
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
	
	echo("ok;".$id.";3;re_pazienti_anagrafica.php?do=show_allegati");	
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}


/************************************************************************
* funzione update()         						*
*************************************************************************/
function update($id) {
	$ope = $_SESSION['UTENTE']->get_userid();
	$id_paz=$id;
	$tablename="utenti";
	$conn = db_connect();
	//$isroot = $_SESSION['UTENTE']->is_root();
	$codiceutente = $_POST['codiceutente'];
	$nome = ucfirst(strtolower($_POST['nome']));
	$cognome = ucfirst(strtolower($_POST['cognome']));
	$cognome=pulisci(firstupper($cognome));
	$nome=pulisci(firstupper($nome));	
	$note_ana = pulisci($_POST['note_ana']);
	$sesso = $_POST['sesso'];
	$data_nascita = $_POST['data_nascita'];
	$comune_nascita = $_POST['comune_nascita'];	
	$stato_nascita=$_POST['stato_nascita'];	
	$codice_fiscale = $_POST['codice_fiscale'];	
	$indirizzo = pulisci($_POST['indirizzo']);
	$comune_residenza  = $_POST['comune_residenza'];	
	$telefono = $_POST['telefono'];
	$cap_r = $_POST['cap_residenza'];
	$fax = $_POST['fax'];
	$cellulare = $_POST['cellulare'];
	$email = $_POST['email'];	
	$decorrenza=$_POST['decorrenza'];
	$status = $_POST['stato'];
	$reparto = $_POST['reparto_paziente'];
	$foto_paziente = $_FILES['foto_paziente']['name'];
	
	$indirizzo = pulisci($indirizzo);	
		
		// cripta la pwd con la chiave corrente
	$data=date(dmyHis);
	if ($foto_paziente!=""){
		$foto_paziente=$data."_".$foto_paziente;
		$foto_paziente1="tb_".$foto_paziente;
	}
	$query = "UPDATE utenti 
				SET codiceutente='$codiceutente', note_ana='$note_ana', Cognome='$cognome',Nome='$nome',
					Sesso='$sesso',DataNascita='$data_nascita',CodiceFiscale='$codice_fiscale',
					LuogoNascita_id='$comune_nascita',stato='$status',opeins='$ope',stato_nascita='$stato_nascita'";
	if ($foto_paziente!="") $query .=",foto_paziente='$foto_paziente1'";	
	
	$query .=" WHERE idUtente='".$id."'";
				
	$result = mssql_query($query, $conn);
	if(!$result)
	{
		echo("no");
		exit();
		die();
	}
	// scrive il log
	scrivi_log ($id,'utenti','agg','idutente');
	
	if (!empty($reparto)) {
		$_sql = "DELETE FROM reparti_pazienti
					WHERE id_paziente = $id";		
		$_rs = mssql_query($_sql, $conn);
		
		$_sql = "INSERT INTO reparti_pazienti (id_reparto, id_paziente, ope_ins)
						VALUES ($reparto, $id, $ope)";
		$_rs = mssql_query($_sql, $conn);
    }
	
	if ((trim($_FILES['foto_paziente']['name']) != "")) {	
			//$nomefile = $_FILES['img_src']['name'];
			
			$imgHNDL = fopen($_FILES['foto_paziente']['tmp_name'], "r");
			$imgf = fread($imgHNDL, $_FILES['foto_paziente']['size']);
			//$eimgf = mysql_escape_string($imgf);
		
			// apre la connessione ftp
			$conn_id = ftp_connect(IP_ADDR);	
		
			// login con user name e password
			$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 
		
			// controllo della connessione
			if ((!$conn_id) || (!$login_result)) { 
				echo "La connessione FTP è fallita!";
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
					if (!ftp_chdir($conn_id, FOTO_PAZIENTI)) {
						 {
				echo("no");
				exit();
				die();
				}
						 exit;
					} 
					
					// upload del file
					if(! ($upload = ftp_fput ($conn_id, $foto_paziente , $temp ,FTP_BINARY))) {
						 {
				echo("no");
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
			
			riduci_foto("foto_pazienti/","foto_pazienti/",$foto_paziente,112,112);
			ftp_del_file(FOTO_PAZIENTI."/".$foto_paziente);
			}
	
	
				
	if ($_POST['flag_residenza']=='false'){
		$query="SELECT idUtente, id FROM utenti_residenze WHERE idUtente='".$id."'";
		$result = mssql_query($query, $conn);
		if(mssql_num_rows($result)>0){
			$row=mssql_fetch_assoc($result);
			$id_res=$row['id'];		
			$query = "UPDATE utenti_residenze SET indirizzo='$indirizzo',comune_id='$comune_residenza',telefono='$telefono',fax='$fax',cellulare='$cellulare',email='$email',opeins='$ope',cap='$cap_r' WHERE id='".$_POST['id_residenza']."'";			
			$result = mssql_query($query, $conn);
			scrivi_log ($_POST['id_residenza'],'utenti_residenze','agg','id');			
			}
			else{
			$query = "INSERT INTO utenti_residenze (IdUtente,indirizzo,comune_id,telefono,fax,cellulare,email,opeins,cap,decorrenza) VALUES('$id','$indirizzo','$comune_residenza ','$telefono','$fax','$cellulare','$email','$ope','$cap_r','$decorrenza')";
			$result = mssql_query($query, $conn);			
			$query = "SELECT TOP 1 dbo.utenti_residenze.IdUtente, dbo.utenti_residenze.id as id, dbo.utenti_residenze.opeins FROM dbo.utenti_residenze WHERE (dbo.utenti_residenze.IdUtente = $id) and (dbo.utenti_residenze.opeins=$ope) ORDER BY dbo.utenti_residenze.id DESC";
			$rs = mssql_query($query, $conn);	
			$row=mssql_fetch_assoc($rs);
			$id_res=$row['id'];	
			scrivi_log ($id_res,'utenti_residenze','ins','id');
			}	
	}else{
		$query = "INSERT INTO utenti_residenze (IdUtente,indirizzo,comune_id,telefono,fax,cellulare,email,opeins,cap,decorrenza) VALUES('$id','$indirizzo','$comune_residenza ','$telefono','$fax','$cellulare','$email','$ope','$cap_r','$decorrenza')";
		$result = mssql_query($query, $conn);
		$query = "SELECT TOP 1 dbo.utenti_residenze.IdUtente, dbo.utenti_residenze.id as id, dbo.utenti_residenze.opeins FROM dbo.utenti_residenze WHERE (dbo.utenti_residenze.IdUtente = $id) and (dbo.utenti_residenze.opeins=$ope) ORDER BY dbo.utenti_residenze.id DESC";
		$rs = mssql_query($query, $conn);	
		$row=mssql_fetch_assoc($rs);
		$id_res=$row['id'];	
		scrivi_log ($id_res,'utenti_residenze','ins','id');
	}
	
	
	$med_cognome = ucfirst(strtolower($_POST['cognome_m']));
	$med_nome = ucfirst(strtolower($_POST['nome_m']));
	$med_nome=pulisci(firstupper($med_nome));
	$med_cognome=pulisci(firstupper($med_cognome));
	$med_indirizzo = pulisci($_POST['indirizzo_m']);
	$med_comune_residenza = $_POST['med_comune_residenza'];
	$med_cap_r = $_POST['med_cap_residenza'];	
	$med_telefono = $_POST['med_telefono'];	
	$med_fax = $_POST['med_fax'];
	$med_cellulare = $_POST['med_cellulare'];
	$med_email = $_POST['med_email'];	
	$med_decorrenza=$_POST['med_decorrenza'];
		
	if ($_POST['flag_medico']=='false'){
		$query="SELECT idUtente, id FROM utenti_medici WHERE idUtente='".$id."'";
		$result = mssql_query($query, $conn);
		if(mssql_num_rows($result)>0){
			$row=mssql_fetch_assoc($result);
			$id_res=$row['id'];		
			$query = "UPDATE utenti_medici SET nome='$med_nome',cognome='$med_cognome',indirizzo='$med_indirizzo',comune_id='$med_comune_residenza',telefono='$med_telefono',fax='$med_fax',cellulare='$med_cellulare',email='$med_email',opeins='$ope',cap='$med_cap_r',decorrenza='$med_decorrenza' WHERE id=$id_res";			
			$result = mssql_query($query, $conn);
			scrivi_log ($id_res,'utenti_medici','agg','id');			
			}
			else{
			$query = "INSERT INTO utenti_medici (IdUtente,nome,cognome,indirizzo,comune_id,telefono,fax,cellulare,email,opeins,cap,decorrenza) VALUES('$id','$med_nome','$med_cognome','$med_indirizzo','$med_comune_residenza ','$med_telefono','$med_fax','$med_cellulare','$med_email','$ope','$med_cap_r','$med_decorrenza')";
			$result = mssql_query($query, $conn);			
			$query = "SELECT TOP 1 dbo.utenti_medici.IdUtente, dbo.utenti_medici.id as id, dbo.utenti_medici.opeins FROM dbo.utenti_medici WHERE (dbo.utenti_medici.IdUtente = $id) and (dbo.utenti_medici.opeins=$ope) ORDER BY dbo.utenti_medici.id DESC";
			$rs = mssql_query($query, $conn);	
			$row=mssql_fetch_assoc($rs);
			$id_med=$row['id'];	
			scrivi_log ($id_med,'utenti_medici','ins','id');
			}	
	}else{		
		$query = "INSERT INTO utenti_medici (IdUtente,nome,cognome,indirizzo,comune_id,telefono,fax,cellulare,email,opeins,cap,decorrenza) VALUES('$id','$med_nome','$med_cognome','$med_indirizzo','$med_comune_residenza ','$med_telefono','$med_fax','$med_cellulare','$med_email','$ope','$med_cap_r','$med_decorrenza')";
		$result = mssql_query($query, $conn);			
		$query = "SELECT TOP 1 dbo.utenti_medici.IdUtente, dbo.utenti_medici.id as id, dbo.utenti_medici.opeins FROM dbo.utenti_medici WHERE (dbo.utenti_medici.IdUtente = $id) and (dbo.utenti_medici.opeins=$ope) ORDER BY dbo.utenti_medici.id DESC";
		$rs = mssql_query($query, $conn);	
		$row=mssql_fetch_assoc($rs);
		$id_med=$row['id'];	
		scrivi_log ($id_med,'utenti_medici','ins','id');
	}
	
	
	$cognome_t = ucfirst(strtolower($_POST['cognome_t']));
	$nome_t = ucfirst(strtolower($_POST['nome_t']));
	$nome_t=pulisci(firstupper($nome_t));
	$cognome_t=pulisci(firstupper($cognome_t));
	$indirizzo_t = pulisci($_POST['indirizzo_t']);	
	$telefono_t = $_POST['telefono_t'];
	$data_nascita_t = $_POST['data_nascita_t_'];
	$sesso_t = $_POST['sesso_t_'];
	$codice_fiscale_t = $_POST['codice_fiscale_t_'];
	$cap_t = $_POST['cap_residenza_t'];
	$fax_t = $_POST['fax_t'];
	$cellulare_t = $_POST['cellulare_t'];
	$email_t = $_POST['email_t'];
	$relazione_t = pulisci($_POST['relazione_t']);	
	$disattivato = $_POST['disattivato'];
	$comune_residenza_t = $_POST['comune_residenza_t_'];
	$comune_residenza_tn = $_POST['comune_residenza_tn_'];
	$stato_nascita_t=$_POST['stato_nascita_t_'];	
	
	if(isset($_POST['tutor_p_']))	$tutore_principale = -1;	else	$tutore_principale = 0;
	if(isset($_POST['tutor_1_']))			$tutore1 = -1;				else	$tutore1 = 0;
	if(isset($_POST['tutor_2_']))			$tutore2 = -1;				else	$tutore2 = 0;
	if(isset($_POST['delegato_1_']))		$delegato1 = -1;			else	$delegato1 = 0;
	if(isset($_POST['delegato_2_']))		$delegato2 = -1;			else	$delegato2 = 0;
	//$cognome_t = pulisci($cognome_t);
	//$nome_t = pulisci($nome_t);
	//$indirizzo_t = pulisci($indirizzo_t);
	//$relazione_t = pulisci($relazione_t);		
	
	
	if ($_POST['flag_tutor']=='false'){
		$query="SELECT idUtente, id FROM utenti_tutor WHERE idUtente='$id' AND (disattivato IS NULL OR disattivato > { fn NOW() })";
		//echo($query);
		$result = mssql_query($query, $conn);
		if(mssql_num_rows($result)>0){
			//$row=mssql_fetch_assoc($result);
			//$id_res=$row['id'];
			$arr_tutor=split(";",$_POST['id_tutor']);			
			foreach($arr_tutor as &$value){
				if($value!=""){
					$idt=$value;
					$cognome_t = pulisci(ucfirst(strtolower($_POST['cognome_t_'.$idt])));
					$nome_t = pulisci(ucfirst(strtolower($_POST['nome_t_'.$idt])));
					$cognome_t=pulisci(firstupper($cognome_t));
					$nome_t=pulisci(firstupper($nome_t));					
					$indirizzo_t = pulisci($_POST['indirizzo_t_'.$idt]);
					$data_nascita_t = $_POST['data_nascita_t_'.$idt];
					$sesso_t = $_POST['sesso_t_'.$idt];	
					$codice_fiscale_t = $_POST['codice_fiscale_t_'.$idt];	
					if(isset($_POST['tutor_p_'.$idt])) 			$tutore_principale = -1;	else	$tutore_principale = 0;
					if(isset($_POST['tutor_1_'.$idt]))			$tutore1 = -1;				else	$tutore1 = 0;
					if(isset($_POST['tutor_2_'.$idt]))			$tutore2 = -1;				else	$tutore2 = 0;
					if(isset($_POST['delegato_1_'.$idt]))		$delegato1 = -1;			else	$delegato1 = 0;
					if(isset($_POST['delegato_2_'.$idt]))		$delegato2 = -1;			else	$delegato2 = 0;
					$telefono_t = $_POST['telefono_t_'.$idt];
					$cap_t = $_POST['cap_residenza_t_'.$idt];
					$fax_t = $_POST['fax_t_'.$idt];
					$cellulare_t = $_POST['cellulare_t_'.$idt];
					$email_t = $_POST['email_t_'.$idt];
					$relazione_t = pulisci($_POST['relazione_t_'.$idt]);	
					$disattivato = $_POST['disattivato_'.$idt];
					$stato_nascita_t=$_POST['stato_nascita_t_'.$idt];	
					if(!(is_numeric($_POST['comune_residenza_t_'.$idt]))){
						$comune_residenza_t = pulisci($_POST['comune_residenza_t_'.$idt]);
						$query="SELECT denominazione, id_comune FROM dbo.comuni WHERE (denominazione = '$comune_residenza_t')";
						$result = mssql_query($query, $conn);
						$row=mssql_fetch_assoc($result);
						$comune_residenza_t=$row['id_comune'];
					}else
						$comune_residenza_t=$_POST['comune_residenza_t_'.$idt];
					
					if(!(is_numeric($_POST['comune_residenza_tn_'.$idt]))){
						$comune_residenza_tn = pulisci($_POST['comune_residenza_tn_'.$idt]);
						$query="SELECT denominazione, id_comune FROM dbo.comuni WHERE (denominazione = '$comune_residenza_tn')";
						$result = mssql_query($query, $conn);
						$row=mssql_fetch_assoc($result);
						$comune_residenza_tn=$row['id_comune'];
					}else
						$comune_residenza_tn=$_POST['comune_residenza_tn_'.$idt];
					//$cognome_t = pulisci($cognome_t);
					//$nome_t = pulisci($nome_t);
					//$indirizzo_t = pulisci($indirizzo_t);
					//$relazione_t = pulisci($relazione_t);					
					if($disattivato!="") 
						$disactive=",disattivato='$disattivato'";
						else
						$disactive="";
					$query = "UPDATE utenti_tutor SET stato_nascita=$stato_nascita_t,nome='$nome_t',cognome='$cognome_t',comune_id_n='$comune_residenza_tn',data_nascita='$data_nascita_t',codice_fiscale='$codice_fiscale_t',sesso='$sesso_t',indirizzo='$indirizzo_t',comune_id='$comune_residenza_t',telefono='$telefono_t',fax='$fax_t',cellulare='$cellulare_t',email='$email_t',relazione_paziente='$relazione_t',tutoreprincipale=$tutore_principale,tutore1=$tutore1,tutore2=$tutore2,delegato1=$delegato1,delegato2=$delegato2,opeins='$ope',cap='$cap_t'$disactive WHERE id=$idt";								
					//echo($query);
					$result = mssql_query($query, $conn);
					scrivi_log ($idt,'utenti_tutor','agg','id');
					}
				}	
			}
			else{		
			if($cognome_t!=""){
				$query = "INSERT INTO utenti_tutor (IdUtente,nome,cognome,indirizzo,comune_id,telefono,fax,cellulare,email,relazione_paziente,opeins,cap,comune_id_n,data_nascita,codice_fiscale,sesso,stato_nascita,tutoreprincipale,tutore1,tutore2,delegato1,delegato2) VALUES('$id','$nome_t','$cognome_t ','$indirizzo_t','$comune_residenza_t','$telefono_t','$fax_t','$cellulare_t','$email_t','$relazione_t','$ope','$cap_t','$comune_residenza_tn','$data_nascita_t','$codice_fiscale_t','$sesso_t',$stato_nascita_t,$tutore_principale,$tutore1,$tutore2,$delegato1,$delegato2)";			
				$result = mssql_query($query, $conn);
				$query = "SELECT TOP 1 dbo.utenti_tutor.IdUtente, dbo.utenti_tutor.id as id, dbo.utenti_tutor.opeins FROM dbo.utenti_tutor WHERE (dbo.utenti_tutor.IdUtente = $id) and (dbo.utenti_tutor.opeins=$ope) ORDER BY dbo.utenti_tutor.id DESC";
				$rs = mssql_query($query, $conn);	
				$row=mssql_fetch_assoc($rs);
				$id_res=$row['id'];	
				scrivi_log ($id_res,'utenti_tutor','ins','id');						
				}
			}
	}else{
		if($cognome_t!=""){
			$query = "INSERT INTO utenti_tutor (IdUtente,nome,cognome,indirizzo,comune_id,telefono,fax,cellulare,email,relazione_paziente,opeins,cap,comune_id_n,data_nascita,codice_fiscale,sesso,stato_nascita,tutoreprincipale,tutore1,tutore2,delegato1,delegato2) VALUES('$id','$nome_t','$cognome_t ','$indirizzo_t','$comune_residenza_t','$telefono_t','$fax_t','$cellulare_t','$email_t','$relazione_t','$ope','$cap_t','$comune_residenza_tn','$data_nascita_t','$codice_fiscale_t','$sesso_t',$stato_nascita_t,$tutore_principale,$tutore1,$tutore2,$delegato1,$delegato2)";			
			$result = mssql_query($query, $conn);
			$query = "SELECT TOP 1 dbo.utenti_tutor.IdUtente, dbo.utenti_tutor.id as id, dbo.utenti_tutor.opeins FROM dbo.utenti_tutor WHERE (dbo.utenti_tutor.IdUtente = $id) and (dbo.utenti_tutor.opeins=$ope) ORDER BY dbo.utenti_tutor.id DESC";
			$rs = mssql_query($query, $conn);	
			$row=mssql_fetch_assoc($rs);
			$id_res=$row['id'];	
			//echo("qui");
			scrivi_log ($id_res,'utenti_tutor','ins','id');	
			}else{
			$arr_tutor=split(";",$_POST['id_tutor']);
			$idt=$arr_tutor[0];
			//echo($idt);
			$cognome_t = pulisci(ucfirst(strtolower($_POST['cognome_t_'.$idt])));
			$nome_t = pulisci(ucfirst(strtolower($_POST['nome_t_'.$idt])));
			$cognome_t=pulisci(firstupper($cognome_t));
			$nome_t=pulisci(firstupper($nome_t));
			$data_nascita_t = $_POST['data_nascita_t_'.$idt];
			$sesso_t = $_POST['sesso_t_'.$idt];	
			$codice_fiscale_t = $_POST['codice_fiscale_t_'.$idt];
			if(isset($_POST['tutor_p_'.$idt])) 			$tutore_principale = -1;	else	$tutore_principale = 0;
			if(isset($_POST['tutor_1_'.$idt]))			$tutore1 = -1;				else	$tutore1 = 0;
			if(isset($_POST['tutor_2_'.$idt]))			$tutore2 = -1;				else	$tutore2 = 0;
			if(isset($_POST['delegato_1_'.$idt]))		$delegato1 = -1;			else	$delegato1 = 0;
			if(isset($_POST['delegato_2_'.$idt]))		$delegato2 = -1;			else	$delegato2 = 0;
			$indirizzo_t = pulisci($_POST['indirizzo_t_'.$idt]);	
			$telefono_t = $_POST['telefono_t_'.$idt];
			$cap_t = $_POST['cap_residenza_t_'.$idt];
			$fax_t = $_POST['fax_t_'.$idt];
			$cellulare_t = $_POST['cellulare_t_'.$idt];
			$email_t = $_POST['email_t_'.$idt];
			$relazione_t = pulisci($_POST['relazione_t_'.$idt]);	
			$disattivato = $_POST['disattivato_'.$idt];
			$stato_nascita_t=$_POST['stato_nascita_t_'.$idt];	
			if(!(is_numeric($_POST['comune_residenza_t_'.$idt]))){
				$comune_residenza_t = pulisci($_POST['comune_residenza_t_'.$idt]);
				$query="SELECT denominazione, id_comune FROM dbo.comuni WHERE (denominazione = '$comune_residenza_t')";
				$result = mssql_query($query, $conn);
				$row=mssql_fetch_assoc($result);
				$comune_residenza_t=$row['id_comune'];
			}else
				$comune_residenza_t=$_POST['comune_residenza_t_'.$idt];
			//echo("comune_tn ".$_POST['comune_residenza_tn_'.$idt]);
			if(!(is_numeric($_POST['comune_residenza_tn_'.$idt]))){
				$comune_residenza_tn = pulisci($_POST['comune_residenza_tn_'.$idt]);
				$query="SELECT denominazione, id_comune FROM dbo.comuni WHERE (denominazione = '$comune_residenza_tn')";
				$result = mssql_query($query, $conn);
				$row=mssql_fetch_assoc($result);
				$comune_residenza_tn=$row['id_comune'];
			}else
				$comune_residenza_tn=$_POST['comune_residenza_tn_'.$idt];
			
			$cognome_t = ucfirst(strtolower($cognome_t));
			$cognome_t = pulisci(firstupper($cognome_t));
			$nome_t = pulisci($nome_t);
			//$indirizzo_t = pulisci($indirizzo_t);
			//$relazione_t = pulisci($relazione_t);
			$query = "INSERT INTO utenti_tutor (IdUtente,nome,cognome,indirizzo,comune_id,telefono,fax,cellulare,email,relazione_paziente,opeins,cap,comune_id_n,data_nascita,codice_fiscale,sesso,stato_nascita,tutoreprincipale,tutore1,tutore2,delegato1,delegato2) VALUES('$id','$nome_t','$cognome_t ','$indirizzo_t','$comune_residenza_t','$telefono_t','$fax_t','$cellulare_t','$email_t','$relazione_t','$ope','$cap_t','$comune_residenza_tn','$data_nascita_t','$codice_fiscale_t','$sesso_t',$stato_nascita_t,$tutore_principale,$tutore1,$tutore2,$delegato1,$delegato2)";			
			$result = mssql_query($query, $conn);
			$query = "SELECT TOP 1 dbo.utenti_tutor.IdUtente, dbo.utenti_tutor.id as id, dbo.utenti_tutor.opeins FROM dbo.utenti_tutor WHERE (dbo.utenti_tutor.IdUtente = $id) and (dbo.utenti_tutor.opeins=$ope) ORDER BY dbo.utenti_tutor.id DESC";
			$rs = mssql_query($query, $conn);	
			$row=mssql_fetch_assoc($rs);
			$id_res=$row['id'];	
			//echo("qui");
			scrivi_log ($id_res,'utenti_tutor','ins','id');			
			}			
	}
	echo("ok;$id_paz;7;");
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
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
					else create();					
			break;

			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_POST['id']);
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

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
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