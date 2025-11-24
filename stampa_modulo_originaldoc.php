<?php
// inclusione delle classi
include_once('include/class.User.php');
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/function_page.php');
include_once('include/dbengine.inc.php');

$relative_path =MODELLI_WORD_PATH;
$destination_path =MODELLI_WORD_DEST_PATH;
$gruppo=$_SESSION['UTENTE']->get_gid();
if (isset($_REQUEST['ngid'])) 
	$gid=$_REQUEST['ngid'];
	else
	$gid=0;

$conn = db_connect();
$query="SELECT * FROM struttura WHERE (status=1) and (cancella='n')";
$result = mssql_query($query, $conn);
$row_st=mssql_fetch_assoc($result);
$intestazione_str=$row_st['intestazione'];
$piede_str=$row_st['piede'];
mssql_free_result($result);
	
	
if (isset($_REQUEST['modello'])) $modello=$_REQUEST['modello'];		
if (isset($_REQUEST['consenso'])) $consenso=$_REQUEST['consenso'];		
if (isset($_REQUEST['idcartella'])) $idcartella=$_REQUEST['idcartella'];
if (isset($_REQUEST['idarea'])) $idarea=$_REQUEST['idarea'];
if (isset($_REQUEST['idpaziente'])) $idpaziente=$_REQUEST['idpaziente'];
if (isset($_REQUEST['idinserimento'])) $idinserimento=$_REQUEST['idinserimento'];
if (isset($_REQUEST['idmodulopadre'])) $idmodulopadre=$_REQUEST['idmodulopadre'];
if (isset($_REQUEST['idmoduloversione'])) $idmoduloversione=$_REQUEST['idmoduloversione'];
if (isset($_REQUEST['all'])) $all=$_REQUEST['all'];

if ($idmoduloversione!="") {
	$result_file=Stampa_Modulo_Segnalibro($idmoduloversione);
	Visualizza_File($destination_path,$result_file);
	exit();
}

if ($idarea!="") {
	$result_file=Stampa_Modulo_sgq($idarea,$idinserimento,$idmodulopadre);
	Visualizza_File($destination_path,$result_file);
	exit();
}

if ($consenso=="informato") {
	$result_file=Stampa_Consenso($idpaziente);
	Visualizza_File($destination_path,$result_file);
	exit();
}elseif($modello!=""){
	$result_file=Stampa_Modello($idpaziente,$modello);
	Visualizza_File($destination_path,$result_file);
	exit();
	}

// se non si appartiene al gruppo associato
if ($gruppo!=$gid) {
	error_message("Impossibile visualizzare il file!Non si dispone delle autorizzazioni necessarie");
	}
	else {
		//echo("qui");
	if($all=="si")		
		$result_file=Stampa_Cartella($idcartella,$idpaziente);	
	else
	{
		$result_file=Stampa_Modulo($idcartella,$idpaziente,$idinserimento,$idmodulopadre);
	}
	
	
	Visualizza_File($destination_path,$result_file);
	}
	
function add_log($file,$idpaziente,$idmodulo,$idcartella){

$conn = db_connect();
$datains = date('d/m/Y');
$orains = date('H:i');
$ipins = $_SERVER['REMOTE_ADDR'];
$opeins = $_SESSION['UTENTE']->get_userid();
$query_log="insert into log_stampa_moduli (id_utente,id_modulo,nome_file,data_stampa,ora_stampa,ope_stampa,ip_stampa,idcartella,tipo) values ($idpaziente,$idmodulo,'$file','$datains','$orains',$opeins,'$ipins',$idcartella,'word')";
$result_log = mssql_query($query_log, $conn);
}


function add_log_sgq($file,$idmodulo,$idarea){

$conn = db_connect();
$datains = date('d/m/Y');
$orains = date('H:i');
$ipins = $_SERVER['REMOTE_ADDR'];
$opeins = $_SESSION['UTENTE']->get_userid();
$query_log="insert into log_stampa_moduli (id_utente,id_modulo,nome_file,data_stampa,ora_stampa,ope_stampa,ip_stampa,idcartella,tipo) values (0,$idmodulo,'$file','$datains','$orains',$opeins,'$ipins',$idarea,'word')";
$result_log = mssql_query($query_log, $conn);
}

function Stampa_Modulo_Segnalibro($idmoduloversione){	
	global $relative_path,$destination_path,$intestazione_str,$piede_str;
	
	$conn = db_connect();
	$query = "SELECT * FROM moduli WHERE (id =$idmoduloversione)";
	
	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mysql_error());
	$row_rs=mssql_fetch_assoc($rs);
	$nome=$row_rs['nome'];
	mssql_free_result($rs);		
	$content="nome modulo: ".$nome."\r\n\r\n";
	$query="SELECT idmoduloversione, etichetta, segnalibro,tipo FROM dbo.campi WHERE (idmoduloversione =$idmoduloversione)";
	$rs = mssql_query($query, $conn);
	while($row=mssql_fetch_assoc($rs)){
		if($row['tipo']!="5")
			$content.=$row['etichetta'].": ".$row['segnalibro']."\r\n";
			else
			$content.="\r\n".$row['etichetta']."\r\n";
		}
	$content.="\r\nFINE elenco campi";
	srand((double)microtime()*1000000);  
	$random=rand(0,999999999999999);
	$filename_tmp = "tmp_sl_".$random.".txt";
	$handle = fopen($destination_path.$filename_tmp, "w");
	fwrite($handle,$content);	
	return($filename_tmp);
}	
	
	


function Stampa_Modello($idpaziente,$modello){	
	global $relative_path,$destination_path,$intestazione_str,$piede_str;
	
	$conn = db_connect();
	
	$query = "SELECT * FROM modelli_word WHERE (codice ='$modello')";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mysql_error());
	$row_rs=mssql_fetch_assoc($rs);
	$file=$row_rs['modello'];
	mssql_free_result($rs);
	srand((double)microtime()*1000000);  
	$random=rand(0,999999999999999);
	$i=1;
	$query="select utenti.Cognome as cognome, utenti.Nome as nome, utenti.IdUtente, utenti.sesso, comuni.denominazione as comune_n, comuni.sigla as prov_n, utenti.DataNascita as data_n FROM utenti INNER JOIN comuni ON utenti.LuogoNascita_id = comuni.id_comune where IdUtente=$idpaziente";	
	$rt = mssql_query($query, $conn);
	if(!$rt) error_message(mysql_error());
	$row=mssql_fetch_assoc($rt);
	
	
	
	$filename_tmp = "tmp_".$random.$file;		
	$filename = $file;
	$word = new clsMSWord;			
	$word->Open($relative_path.$filename);
	$i++;
	
	
	$terapista=$_REQUEST['terapista'];
		if(($terapista!="0")and($terapista!="")){
		$query6="SELECT nome, uid FROM operatori WHERE uid=$terapista";
		//echo($query6);
		$rs6 = mssql_query($query6, $conn);
		if(!$rs6) error_message(mssql_error());
		$row6 = mssql_fetch_assoc($rs6);		
		$terapista=trim($row6['nome']);
		$word->WriteBookmarkText('terapista',$terapista);
		}

	//$word->WriteBookmarkText('nome',$trd);
	$word->WriteBookmarkText('nome',strtoupper(trim(unhtmlentities($row['nome']))));
	$word->WriteBookmarkText('cognome',strtoupper(trim(unhtmlentities($row['cognome']))));
	$word->WriteBookmarkText('nome_1',strtoupper(trim(unhtmlentities($row['nome']))));
	$word->WriteBookmarkText('cognome_1',strtoupper(trim(unhtmlentities($row['cognome']))));
	$word->WriteBookmarkText('cartella',$idpaziente);
	if($row['sesso']=="2") 
		$nascita="Nata il ";
		else
		$nascita="Nato il ";
		
	if($row['data_n']=="") 
		$data_n="";
		else
		$data_n=trim(formatta_data($row['data_n']));
	$word->WriteBookmarkText('data_nascita',$data_n);
	$nascita.=$data_n;			
	if($row['comune_n']=="") 
		$comune_n="";
		else
		$comune_n=trim($row['comune_n'])." ".$row['prov_n'];
	$nascita.=" a ".$comune_n;
	$word->WriteBookmarkText('luogo_nascita',$comune_n);	
	$word->WriteBookmarkText('nascita',$nascita);	
	

	$query="SELECT comuni.denominazione as comune_p, comuni.sigla as prov_p, utenti_residenze.indirizzo as indirizzo, utenti_residenze.telefono as telefono, 
            utenti_residenze.IdUtente, utenti_residenze.id FROM comuni INNER JOIN utenti_residenze ON comuni.id_comune = utenti_residenze.comune_id
			where IdUtente=$idpaziente ORDER BY utenti_residenze.id DESC";
	$rt = mssql_query($query, $conn);
	if(!$rt) error_message(mysql_error());
	$row=mssql_fetch_assoc($rt);
	
	if($row['indirizzo']=="") 
		$indirizzo="";
		else
		$indirizzo=trim($row['indirizzo']);
	$word->WriteBookmarkText('indirizzo',unhtmlentities($indirizzo));
	if($row['comune_p']=="") 
		$comune_p="";
		else
		$comune_p=trim($row['comune_p'])." ".$row['prov_p'];
	$word->WriteBookmarkText('citta',$comune_p);	
	if($row['telefono']=="") 
		$telefono="";
		else
		$telefono=trim($row['telefono']);
	$word->WriteBookmarkText('telefono',$telefono);
	$word->WriteBookmarkText('data',date("d/m/Y"));	
	$word->WriteBookmarkText('luogo',LUOGO_CENTRO);

	
	//if (isset($_REQUEST['idtutor'])){	
		$idtutor=$_REQUEST['idtutor'];
		$idtutor_2=$_REQUEST['idtutor2'];		
		$query="SELECT dbo.re_utenti_tutor.* FROM dbo.re_utenti_tutor WHERE (IdUtente = $idpaziente) ORDER BY id DESC";		
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		$num_rows=mssql_num_rows($rs);
		$row = mssql_fetch_assoc($rs);		
		$tut_nome=strtoupper(trim(unhtmlentities($row['nome'])));
		if($tut_nome=="") $tut_nome="_";
		$tut_cognome=strtoupper(trim(unhtmlentities($row['cognome'])));						
		$tut_relazione=strtoupper(trim(unhtmlentities($row['relazione_paziente'])));
		$tut_data_nascita=formatta_data($row['data_nascita']);
		if($tut_data_nascita=="") $tut_data_nascita="_";
		$tut_luogo_nascita=strtoupper(trim(unhtmlentities($row['nome_comune_n'])));
		$tut_luogo_nascita.=" ".trim($row['sigla_n'])."";
		$tut_comune=strtoupper(trim(unhtmlentities($row['comune_r'])));
		$tut_comune.=" ".trim($row['sigla_r'])."";
		$tut_indirizzo=trim($row['indirizzo']);
		$tut_codice_fiscale=trim($row['codice_fiscale']);
		$word->WriteBookmarkText('cod_fiscale_t',$tut_codice_fiscale);
		$word->WriteBookmarkText('indirizzo_t',$tut_indirizzo);
		$word->WriteBookmarkText('comune_t',$tut_comune);
		$word->WriteBookmarkText('luogo_nascita_t',$tut_luogo_nascita);
		$word->WriteBookmarkText('luogo_nascita_t_1',$tut_luogo_nascita);
		$word->WriteBookmarkText('data_nascita_t',$tut_data_nascita);
		$word->WriteBookmarkText('data_nascita_t_1',$tut_data_nascita);		
		$word->WriteBookmarkText('genitore',$tut_nome." ".$tut_cognome);
		$word->WriteBookmarkText('genitore_1',$tut_nome." ".$tut_cognome);
		$word->WriteBookmarkText('genitore_11',$tut_nome." ".$tut_cognome);
		$word->WriteBookmarkText('relazione',$tut_relazione);
		
		if($idtutor_2!=""){
		$query="SELECT dbo.re_utenti_tutor.* FROM dbo.re_utenti_tutor WHERE (id = $idtutor_2) ORDER BY id DESC";		
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		$num_rows=mssql_num_rows($rs);
		$row = mssql_fetch_assoc($rs);		
		$tut_nome=strtoupper(trim(unhtmlentities($row['nome'])));
		$tut_cognome=strtoupper(trim(unhtmlentities($row['cognome'])));
		$tut_data_nascita=formatta_data($row['data_nascita']);
		$tut_luogo_nascita=strtoupper(trim(unhtmlentities($row['nome_comune_n'])));
		$tut_luogo_nascita.=" (".trim($row['sigla_n']).")";	
		$word->WriteBookmarkText('luogo_nascita_t_2',$tut_luogo_nascita);
		$word->WriteBookmarkText('data_nascita_t_2',$tut_data_nascita);			
		$word->WriteBookmarkText('genitore_2',$tut_nome." ".$tut_cognome);
		$word->WriteBookmarkText('genitore_21',$tut_nome." ".$tut_cognome);				
		}
	//}
		
	if (isset($_REQUEST['idimpegnativa'])){	
		
		$query="SELECT dbo.comuni.sigla as sigla, dbo.comuni.denominazione as citta, dbo.utenti_medici.* FROM dbo.utenti_medici INNER JOIN
                      dbo.comuni ON dbo.utenti_medici.comune_id = dbo.comuni.id_comune WHERE (dbo.utenti_medici.IdUtente = $idpaziente) ORDER BY dbo.utenti_medici.decorrenza DESC";		
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		$num_rows=mssql_num_rows($rs);		
		$row = mssql_fetch_assoc($rs);		
		$med_nome=trim(unhtmlentities($row['nome']));
		$med_cognome=trim(unhtmlentities($row['cognome']));	
		$med_indirizzo=trim(unhtmlentities($row['indirizzo']));	
		$med_citta=trim(unhtmlentities($row['citta']));	
		$med_citta.="(".trim($row['sigla']).")";
		mssql_free_result($rs);
		$word->WriteBookmarkText('nome_cognome_medico',$med_nome." ".$med_cognome);
		$word->WriteBookmarkText('indirizzo_medico',$med_indirizzo);	
		$word->WriteBookmarkText('citta_medico',$med_citta);
		
		$idimpegnativa=$_REQUEST['idimpegnativa'];		
		$query="SELECT * FROM re_impegnative_modelli_sgq WHERE (idimpegnativa =  $idimpegnativa)";	
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		$num_rows=mssql_num_rows($rs);		
		$row = mssql_fetch_assoc($rs);		
		$data_inizio=formatta_data($row['DataInizioTrattamento']);
		$data_fine=formatta_data($row['DataFineTrattamento']);	
		$ultimo_trattamento=formatta_data($row['ultimo_trattamento']);	
		$frequenza=trim($row['ggfrequenza']);	
		$num_trattamenti=trim($row['NumeroTrattamenti']);	
		$case_manager=trim(unhtmlentities($row['case_manager_nome']));
		if($case_manager=="") $case_manager="_";
		$diagnosi=trim(unhtmlentities($row['Diagnosi']));
		$menomazione=trim(unhtmlentities($row['menomazione']));
		$obiettivi=trim(pulisci_lettura($row['obiettivi']));
		$norma_regime=trim(unhtmlentities($row['normativa_nome']))." ".trim(unhtmlentities($row['regime_nome']));
		mssql_free_result($rs);
		$word->WriteBookmarkText('data_inizio',$data_inizio);
		$word->WriteBookmarkText('data_inizio_1',$data_inizio);
		$word->WriteBookmarkText('data_fine',$data_fine);	
		$word->WriteBookmarkText('frequenza',$frequenza);
		$word->WriteBookmarkText('paziente_diagnosi',$diagnosi);
		$word->WriteBookmarkText('paziente_menomazione',$menomazione);
		$word->WriteBookmarkText('ultimo_trattamento',$ultimo_trattamento);
		$word->WriteBookmarkText('normativa_regime',$norma_regime);
		$word->WriteBookmarkText('normativa_regime1',$norma_regime);
		$word->WriteBookmarkText('obiettivi',$obiettivi);
		$word->WriteBookmarkText('obiettivi1',$obiettivi);
		$word->WriteBookmarkText('frequenza_trattamenti',$frequenza." trattamenti a settimana per ".$num_trattamenti." trattamenti totali");
		$word->WriteBookmarkText('frequenza_trattamenti_ridotta'," - per ".$num_trattamenti." gg a datare dal ".$data_inizio);
		$word->WriteBookmarkText('case_manager',$case_manager);

		$query="SELECT operatori.nome as nome, operatori_direttore.data_inizio, operatori_direttore.data_fine, operatori_direttore.tipo, operatori.dir_sanitario, operatori.dir_tecnico 
		FROM operatori INNER JOIN operatori_direttore ON operatori.uid = operatori_direttore.uid WHERE (operatori_direttore.tipo = N'1') 
		AND (operatori.dir_sanitario = 'y') AND (operatori_direttore.data_inizio <= { fn NOW() }) AND (operatori_direttore.data_fine >= { fn NOW() })";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		$row = mssql_fetch_assoc($rs);		
		$dir_sanitario=trim($row['nome']);
		$word->WriteBookmarkText('direttore_sanitario',"dott./dott.ssa ".$dir_sanitario);
	}
	
	$query="select * from re_impegnative_trattamenti where (idutente=$idpaziente) and (idimpegnativa=$idimpegnativa) ORDER BY trattamento ASC";
	//echo($query);
	$trattamenti="";
	$result2 = mssql_query($query, $conn);
	if(!$result2) error_message(mysql_error());
	$trat="";
	$count_trat=0;	
	while($row2=mssql_fetch_assoc($result2))	{
		if($trat==$row2['trattamento']){
			$count_trat++;
		}else{
			if($trat!="") $trattamenti.=$trat." ".$count_trat."/7 - ";
			$trat=$row2['trattamento'];
			$count_trat=1;
		}		
	}
	if($trat!="") $trattamenti.=$trat." ".$count_trat."/7 - ";
	
	if($trattamenti!=""){		
		$word->WriteBookmarkText('paziente_trattamenti',substr($trattamenti,0,strlen($trattamenti)-3));
		}		
		else{
		$word->WriteBookmarkText('paziente_trattamenti',"_");
	}
	
	
		$word->WriteBookmarkText('intestazione_str',$intestazione_str);
		$word->WriteBookmarkText('piede_str',$piede_str);
		$filename=trim($filename);
		$word->SaveAs($destination_path.$filename_tmp);
		//$word->Close();
		$word->Quit();
		//add_log($filename_tmp,$idpaziente,"0");
		
		return($filename_tmp);
}	
	

function Stampa_Consenso($idpaziente){	
	global $relative_path,$destination_path,$intestazione_str,$piede_str;
	
	$conn = db_connect();
	$query = "SELECT * FROM modelli_word WHERE (codice ='".$_REQUEST['modello']."')";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mysql_error());
	$row_rs=mssql_fetch_assoc($rs);
	$file=$row_rs['modello'];
	mssql_free_result($rs);	
	srand((double)microtime()*1000000);  
	$random=rand(0,999999999999999);
	$i=1;
	$query="select utenti.Cognome as cognome, utenti.Nome as nome, utenti.IdUtente, utenti.sesso, comuni.denominazione as comune_n, comuni.sigla as prov_n, utenti.DataNascita as data_n FROM utenti INNER JOIN comuni ON utenti.LuogoNascita_id = comuni.id_comune where IdUtente=$idpaziente";	
	$rt = mssql_query($query, $conn);
	if(!$rt) error_message(mysql_error());
	$row=mssql_fetch_assoc($rt);
	//while($row=mssql_fetch_assoc($rt))	{
		//if($i==1){			
	$filename_tmp = "tmp_".$random.$file;		
	$filename = $file;
	$word = new clsMSWord;
	$word->Open($relative_path.$filename);
	$i++;
		//}
	$word->WriteBookmarkText('nome',trim($row['nome']));
	$word->WriteBookmarkText('cognome',trim($row['cognome']));
	if($row['sesso']=="2") 
		$nascita="Nata il ";
		else
		$nascita="Nato il ";
		
	if($row['data_n']=="") 
		$data_n="";
		else
		$data_n=trim(formatta_data($row['data_n']));
	$nascita.=$data_n;			
	if($row['comune_n']=="") 
		$comune_n="";
		else
		$comune_n=trim($row['comune_n'])." (".$row['prov_n'].")";
	$nascita.=" a ".$comune_n;			
	$word->WriteBookmarkText('nascita',$nascita);	
	
	$query="SELECT comuni.denominazione as comune_p, comuni.sigla as prov_p, utenti_residenze.indirizzo as indirizzo, utenti_residenze.telefono as telefono, 
            utenti_residenze.IdUtente, utenti_residenze.id FROM comuni INNER JOIN utenti_residenze ON comuni.id_comune = utenti_residenze.comune_id
			where IdUtente=$idpaziente ORDER BY utenti_residenze.id DESC";
	$rt = mssql_query($query, $conn);
	if(!$rt) error_message(mysql_error());
	$row=mssql_fetch_assoc($rt);
	
	if($row['indirizzo']=="") 
		$indirizzo="";
		else
		$indirizzo=trim($row['indirizzo']);
	$word->WriteBookmarkText('indirizzo',$indirizzo);
	if($row['comune_p']=="") 
		$comune_p="";
		else
		$comune_p=trim($row['comune_p'])." (".$row['prov_p'].")";
	$word->WriteBookmarkText('citta',$comune_p);	
	if($row['telefono']=="") 
		$telefono="";
		else
		$telefono=trim($row['telefono']);
	$word->WriteBookmarkText('telefono',$telefono);
	$word->WriteBookmarkText('data',date("d/m/Y"));			
	//}			
		$word->WriteBookmarkText('intestazione_str',$intestazione_str);
		$word->WriteBookmarkText('piede_str',$piede_str);
		$filename=trim($filename);
		$word->SaveAs($destination_path.$filename_tmp);
		//$word->Close();
		$word->Quit();
		//add_log($filename_tmp,$idpaziente,"0");
		return($filename_tmp);
}	
	
	
function Stampa_cartella($idcartella,$idpaziente){
	global $relative_path,$destination_path,$intestazione_str,$piede_str;

	$conn = db_connect();
	srand((double)microtime()*1000000);  
	$random=rand(0,999999999999999);
	$query="SELECT idcartella AS cartella, peso_modulo, idcartella, modello_word, nome, idinserimento, idmodulo FROM dbo.re_moduli_valori_stampa 
			GROUP BY idcartella, peso_modulo, modello_word, nome, idinserimento, idmodulo HAVING (idcartella = $idcartella)
			ORDER BY peso_modulo, idinserimento";
	
		// Extract content.
		$content = (string) $word->ActiveDocument->Content;

	return($filename_tmp);
}	
	
	
function Stampa_Modulo($idcartella,$idpaziente,$idinserimento,$idmodulopadre){	
	global $relative_path,$destination_path,$intestazione_str,$piede_str;
	
	$query_imp="";
	if (isset($_REQUEST['idimpegnativa'])) {
		$idimpegnativa=$_REQUEST['idimpegnativa'];
		if($idimpegnativa!="") $query_imp="and id_impegnativa=$idimpegnativa ";		
	}
	
	
	
	$conn = db_connect();
	srand((double)microtime()*1000000);  
	$random=rand(0,999999999999999);
	$i=1;		
	if($idcartella!=0)		
		$query="select * from re_moduli_valori_stampa where (id_inserimento=$idinserimento and id_cartella=$idcartella and id_modulo_padre=$idmodulopadre $query_imp) ORDER BY peso ASC";	
	else		
		$query="select * from re_moduli_valori_stampa where (id_modulo_padre=$idmodulopadre and id_inserimento=$idinserimento and id_cartella=0 and id_paziente=$idpaziente $query_imp) ORDER BY peso ASC";
	
	
	
	
	$rt = mssql_query($query, $conn);
	
	if(!$rt) error_message(mysql_error());
	$num_istanza=$idinserimento;
	$arr_segnalibri=array();	
	while($row=mssql_fetch_assoc($rt))	{
	
		if($i==1){			
			$idmoduloversione=$row['id_modulo_versione'];
			$filename_tmp = "tmp_".$random.$row['modello_word'];		
			$filename = $row['modello_word'];			
			$codicesgq = $row['codice'];
		//	$filename="368_nuovo_progetto_riabilitativo.doc";
		
	/*	echo($idmoduloversione." ".$filename);
		exit();*/
			$word = new clsMSWord;
			$word->Open($relative_path.$filename);
			$word->WriteBookmarkText('intestazione',$row['intestazione']);
			$i++;
			
			
			//echo("prima_");
		}
		//exit();
		$valore=$row['valore'];
		if($row['tipo']==4){
			$idcampo=$row['idcampo'];
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo";		
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi=$row3['multi'];
			mssql_free_result($rs3);
			
			$query2="select * from re_moduli_combo where idcampo=$idcampo ";		
			$rs2 = mssql_query($query2, $conn);
				
			if(!$rs2) error_message(mssql_error());
			if($multi==1){
				$et="";
				$valore=split(";",$valore);							
				while ($row2 = mssql_fetch_assoc($rs2))	{
					$valore1=$row2['valore'];
					$etichetta=$row2['etichetta'];
					$separatore=$row2['separatore'];
								switch ($separatore){
									case 0:
										$sep=" - ";
										break;
									case 1:
										$sep=" - ";
										break;
									case 2:
										$sep=" , ";
										break;
								}
					if (in_array($valore1,$valore)) $et.=$etichetta.$sep;									
				}
				$et=substr($et,0,strlen($et)-2);						
			$valore=$et;
			 }else{				
				while ($row2 = mssql_fetch_assoc($rs2))	{								
					if ($row2['valore']==$valore) $valore=$row2['etichetta'];									
				}	
			}
		}
		 elseif($row['tipo']==9)
					 {
					 $multi_ram=0;
					 $idcampo=$row['idcampo'];
			$query3="select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo";		
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi_ram=$row3['multi'];
			mssql_free_result($rs3);					 
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";						
						$rs2 = mssql_query($query2, $conn);						
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								if (in_array($valore1,$valore)) $et.=$etichetta." - ";									
							}
						//$et=substr($et,0,strlen($et)-2);						
						echo($et);
					 }else{	
					$valore2=$valore;				 
					while ($row2 = mssql_fetch_assoc($rs2))
							{							
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore1=$row2['valore'];
						$etichetta1=$row2['etichetta'];
						$v=(int)($valore2);
						$valore=get_rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
						if($valore!='')
						break;
							}
					//$valore1=$row2['etichetta'];
					//echo($valore1);
				   }
				  }
				 elseif($row['tipo']==7){
					if (trim($valore)=="") $valore="Nessun allegato inserito";
				 }	
		if ($valore){
			$valore=unhtmlentities($valore);
			$word->WriteBookmarkText($row['segnalibro'],$valore);
		}
		
		if($idpaziente=="") $idpaziente=$row['idpaziente'];
		
		array_push($arr_segnalibri, $row['segnalibro']);	
	}			
	mssql_free_result($rt);
	//segnalibri null
	$query="SELECT etichetta, segnalibro FROM campi WHERE (idmoduloversione = $idmoduloversione) AND (status = 1) AND (cancella = 'n')";
	
	
	
	$result = mssql_query($query, $conn);
	while ($row2 = mssql_fetch_assoc($result))
		{
			$segnalibro=$row2['segnalibro'];			
			if (!(in_array($segnalibro,$arr_segnalibri))){
				$word->WriteBookmarkText($row2['segnalibro']," ");
			}
		}
	mssql_free_result($result);
	$query="select * from re_dati_utenti_stampa where (idutente=$idpaziente) ORDER BY versione DESC, max_data DESC";
	//echo($query);
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mysql_error());	
	if($row1=mssql_fetch_assoc($result))	{
		//echo($row1['Nome']." ".$row1['Cognome']." ".$row1['datanascita']);
		$word->WriteBookmarkText('paziente_nome',unhtmlentities($row1['Nome']));
		$word->WriteBookmarkText('paziente_cognome',unhtmlentities($row1['Cognome']));
		$word->WriteBookmarkText('paziente_datanascita',formatta_data($row1['data_nascita']));
		$word->WriteBookmarkText('paziente_codice_fiscale',$row1['codice_fiscale']);
		$word->WriteBookmarkText('paziente_luogonascita',unhtmlentities($row1['comune_nascita']));	
		$word->WriteBookmarkText('paziente_comune_residenza',unhtmlentities($row1['comune_residenza']));	
		if($row1['telefono']!="")
			$word->WriteBookmarkText('paziente_telefono',$row1['telefono']);
			else
			$word->WriteBookmarkText('paziente_telefono',"_");
		if(trim($row1['Sesso'])=="1") 
			$sesso="M";
			else
			$sesso="F";
		$word->WriteBookmarkText('paziente_sesso',$sesso);
		$word->WriteBookmarkText('paziente_cartella',$row1['codice_cartella']."/".convert_versione($row1['versione']));
		$word->WriteBookmarkText('paziente_regime',unhtmlentities($row1['normativa']." ".$row1['regime']));
		if($row1['Diagnosi']!="")
			$diagnosi=$row1['Diagnosi'];
			else
			$diagnosi="_";			
		$word->WriteBookmarkText('paziente_menomazione',unhtmlentities($row1['menomazione']));
		$word->WriteBookmarkText('paziente_asl',unhtmlentities($row1['DescrizioneAsl']));
		$word->WriteBookmarkText('paziente_distretto',unhtmlentities($row1['DescrizioneDistretto']));
		$word->WriteBookmarkText('medico_responsabile',unhtmlentities($row1['medico_responsabile']));
		$word->WriteBookmarkText('case_manager',unhtmlentities($row1['case_manager']));
		
		if($idimpegnativa=="") $word->WriteBookmarkText('paziente_diagnosi',$diagnosi);
	}
	
	
	if($idimpegnativa!=""){
	$query="SELECT * FROM impegnative WHERE (idimpegnativa=$idimpegnativa)";
	$result2 = mssql_query($query, $conn);
	$row1=mssql_fetch_assoc($result2);
	$data_aut_asl=formatta_data($row1['DataAutorizAsl']);
	$prot_aut_asl=$row1['ProtAutorizAsl'];
	$prescrittore=$row1['MedicoPrescrittore'];
	$induttore=$row1['MedicoInduttore'];
	$pac1=$row1['idpacchetto'];
	$pac2=$row1['idpacchetto1'];
	$prof1=$row1['idprofilo'];
	$prof2=$row1['idprofilo1'];
	if($row1['Diagnosi']!="")
			$diagnosi=$row1['Diagnosi'];
			else
			$diagnosi="_";
	$medico_prescrittore="";
	if($prescrittore!=""){
		$query="SELECT * FROM prescrittori WHERE IdPrescrittore=$prescrittore";
		$result2 = mssql_query($query, $conn);
		$row1=mssql_fetch_assoc($result2);
		$medico_prescrittore=unhtmlentities($row1['Codice_asl'])." - ".unhtmlentities($row1['NominativoPrescrittore']);
	}else{
		$medico_prescrittore="_";
	}
	if($induttore!=""){
		$query="SELECT * FROM prescrittori WHERE IdPrescrittore=$induttore";
		$result2 = mssql_query($query, $conn);
		$row1=mssql_fetch_assoc($result2);
		$medico_induttore=unhtmlentities($row1['codice_induttore'])." - ".unhtmlentities($row1['NominativoPrescrittore']);
	}else{
		$medico_induttore="_";
	}
	if($pac1!=""){
		$query="SELECT * FROM Fkt_Pacchetti WHERE Id_pacchetto=$pac1";
		$result2 = mssql_query($query, $conn);
		$row1=mssql_fetch_assoc($result2);
		$pac1=$row1['Pacchetto'];
	}
	if($pac2!=""){
		$query="SELECT * FROM Fkt_Pacchetti WHERE Id_pacchetto=$pac2";
		$result2 = mssql_query($query, $conn);
		$row1=mssql_fetch_assoc($result2);
		$pac2=$row1['Pacchetto'];
	}
	
	$word->WriteBookmarkText('id_ricetta',$idimpegnativa);
	$word->WriteBookmarkText('dottore',$medico_prescrittore);
	$word->WriteBookmarkText('induttore',$medico_induttore);
	$word->WriteBookmarkText('num_ricetta',$prot_aut_asl);
	$word->WriteBookmarkText('data_prescrizione',$data_aut_asl);
	$word->WriteBookmarkText('paziente_diagnosi',$diagnosi);
	if($pac1!="")
		$word->WriteBookmarkText('pacc1',$pac1);
		else
		$word->WriteBookmarkText('pacc1',"_");
	if($pac2!="")
		$word->WriteBookmarkText('pacc2',$pac2);
		else
		$word->WriteBookmarkText('pacc2',"_");
	if($prof1!="")
		$word->WriteBookmarkText('prof1',$prof1);
		else
		$word->WriteBookmarkText('prof1',"_");
	if($prof2!="")
		$word->WriteBookmarkText('prof2',$prof2);
		else
		$word->WriteBookmarkText('prof2',"_");

	
	$query="select * from re_impegnative_trattamenti where (idutente=$idpaziente) and (idimpegnativa=$idimpegnativa) ORDER BY trattamento ASC";
	//echo($query);
	$trattamenti="";
	$result2 = mssql_query($query, $conn);
	if(!$result2) error_message(mysql_error());
	$trat="";
	$count_trat=0;	
	while($row2=mssql_fetch_assoc($result2))	{
		if($trat==$row2['trattamento']){
			$count_trat++;
		}else{
			if($trat!="") $trattamenti.=$trat." ".$count_trat."/7 - ";
			$trat=$row2['trattamento'];
			$count_trat=1;
		}		
	}
	if($trat!="") $trattamenti.=$trat." ".$count_trat."/7 - ";
	
	if($trattamenti!=""){		
		$word->WriteBookmarkText('paziente_trattamenti',substr($trattamenti,0,strlen($trattamenti)-3));
		}		
		else{
		$word->WriteBookmarkText('paziente_trattamenti',"_");
	}
	}
	
		$word->WriteBookmarkText('intestazione_str',unhtmlentities($intestazione_str));
		$word->WriteBookmarkText('piede_str',unhtmlentities(trim($codicesgq)."\n".$piede_str));
		$modulo_sgq=$row1['codice_cartella']."/".convert_versione($row1['versione'])."/".$codicesgq."/".$num_istanza;
		$word->WriteBookmarkText('cartella_sgq_versione',$modulo_sgq);
		$word->WriteBookmarkText('modulo_sgq',$codicesgq);
		$word->WriteBookmarkText('modulo_istanze',$num_istanza);
		$word->SaveAs($destination_path.$filename_tmp);		
		$word->Quit();
		
		add_log($filename_tmp,$idpaziente,$idmodulopadre,$idcartella);
		return($filename_tmp);
	}

	
function Stampa_Modulo_sgq($idarea,$idinserimento,$idmodulopadre){	
	global $relative_path,$destination_path,$intestazione_str,$piede_str;
	//echo($idarea."--".$idinserimento."-- ".$idmodulopadre);
	//exit();
	$conn = db_connect();
	srand((double)microtime()*1000000);  
	$random=rand(0,999999999999999);
	$i=1;		
	if($idarea!=0)
		if($idmodulopadre!=""){
		    $query="select * from re_moduli_valori_stampa_sgq where (id_inserimento=$idinserimento and id_area=$idarea and id_modulo=$idmodulopadre) ORDER BY peso ASC";
		    //echo($query);
			//exit();
		}else
			$query="select * from re_moduli_valori_stampa_sgq where (id_inserimento=$idinserimento and id_area=$idarea and id_modulo=$idmodulopadre) ORDER BY peso ASC";
	else
	    $query="select * from re_moduli_valori_stampa_sgq where (id_modulo=$idmodulopadre and id_inserimento=$idinserimento and id_area=0) ORDER BY peso ASC";
	
	
	
	$rt = mssql_query($query, $conn);
	if(!$rt) error_message(mysql_error());
	$num_istanza=$idinserimento;
	$arr_segnalibri=array();	
	while($row=mssql_fetch_assoc($rt))	{
		if($i==1){
			$idmoduloversione=$row['idmoduloversione'];
			$filename_tmp = "tmp_".$random.$row['modello_word'];		
			$filename = $row['modello_word'];			
			$codicesgq = $row['codice'];
			//si blocca
			$word = new clsMSWord;
			$word->Open($relative_path.$filename);
			$word->WriteBookmarkText('intestazione',$row['intestazione']);
			$i++;
			//echo($word);
			//echo("prima_");
			//exit();
		}
		$valore=$row['valore'];
		if($row['tipo']==4){
			$idcampo=$row['idcampo'];
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo";		
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi=$row3['multi'];
			mssql_free_result($rs3);
			
			$query2="select * from re_moduli_combo where idcampo=$idcampo ";		
			$rs2 = mssql_query($query2, $conn);
				
			if(!$rs2) error_message(mssql_error());
			if($multi==1){
				$et="";
				$valore=split(";",$valore);							
				while ($row2 = mssql_fetch_assoc($rs2))	{
					$valore1=$row2['valore'];
					$etichetta=$row2['etichetta'];
					$separatore=$row2['separatore'];
								switch ($separatore){
									case 0:
										$sep=" - ";
										break;
									case 1:
										$sep=" - ";
										break;
									case 2:
										$sep=" , ";
										break;
								}
					if (in_array($valore1,$valore)) $et.=$etichetta.$sep;									
				}
				$et=substr($et,0,strlen($et)-2);						
			$valore=$et;
			 }else{				
				while ($row2 = mssql_fetch_assoc($rs2))	{								
					if ($row2['valore']==$valore) $valore=$row2['etichetta'];									
				}	
			}
		}
		 elseif($row['tipo']==9)
					 {
					 $multi_ram=0;
					 $idcampo=$row['idcampo'];
			$query3="select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo";		
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi_ram=$row3['multi'];
			mssql_free_result($rs3);					 
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";						
						$rs2 = mssql_query($query2, $conn);						
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								if (in_array($valore1,$valore)) $et.=$etichetta." - ";									
							}
						//$et=substr($et,0,strlen($et)-2);						
						echo($et);
					 }else{	
					$valore2=$valore;				 
					while ($row2 = mssql_fetch_assoc($rs2))
							{							
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore1=$row2['valore'];
						$etichetta1=$row2['etichetta'];
						$v=(int)($valore2);
						$valore=get_rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
						if($valore!='')
						break;
							}
					//$valore1=$row2['etichetta'];
					//echo($valore1);
				   }
				  }
				 elseif($row['tipo']==7){
					if (trim($valore)=="") $valore="Nessun allegato inserito";
				 }
		if ($valore){
			$valore=unhtmlentities($valore);
			$valore=str_replace("?","'",$valore);
			$word->WriteBookmarkText($row['segnalibro'],$valore);
		}
		//echo("__qui");
		if($idpaziente=="")	$idpaziente=$row['idpaziente'];
		array_push($arr_segnalibri, $row['segnalibro']);	
	}			
	mssql_free_result($rt);
	//segnalibri null
	$query="SELECT etichetta, segnalibro FROM campi WHERE (idmoduloversione = $idmoduloversione) AND (status = 1) AND (cancella = 'n')";
	$result = mssql_query($query, $conn);
	while ($row2 = mssql_fetch_assoc($result))
		{
			$segnalibro=$row2['segnalibro'];			
			if (!(in_array($segnalibro,$arr_segnalibri))){
				$word->WriteBookmarkText($row2['segnalibro']," ");
			}
		}
	mssql_free_result($result);

	
		$word->WriteBookmarkText('intestazione_str',unhtmlentities($intestazione_str));
		$word->WriteBookmarkText('piede_str',unhtmlentities($piede_str));
		$modulo_sgq=$row1['codice_cartella']."/".convert_versione($row1['versione'])."/".$codicesgq."/".$num_istanza;
		$word->WriteBookmarkText('cartella_sgq_versione',$modulo_sgq);
		$word->WriteBookmarkText('modulo_sgq',$codicesgq);
		$word->WriteBookmarkText('modulo_istanze',$num_istanza);
		$word->SaveAs($destination_path.$filename_tmp);		
		$word->Quit();
		
		add_log_sgq($filename_tmp,$idmodulopadre,$idarea);
		return($filename_tmp);
	}
	
	

function Visualizza_File($path,$file){
	$handle = fopen($path.$file, "r");
	
	
	$content=fread($handle,filesize($path.$file));
	fclose($handle);		
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$file");
	header("Expires: 0");
	header("Pragma: no-cache");
	echo($content);
}		
		
	 

?>
