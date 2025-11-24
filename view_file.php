<?php
// inclusione delle classi
//ini_set('display_errors', 'on');
include_once('include/class.User.php');
// inizializza le variabili di sessione
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/function_page.php');
include_once('include/dbengine.inc.php');

$id_menu = $id_permess = 0;
srand((float)microtime() * 1000000);
$random = rand(0, 999999999999999);
$idcartella = "";

// nome della tabella
if (isset($_REQUEST['sec'])) {
	if ($_REQUEST['sec'] == "imp") {
		$tablename = 'utenti_allegati_imp';
		$relative_path = ALLEGATI_UTENTI_IMP_W;
		$id_menu = $id_permesso = $modulo_id = 11;
	} elseif ($_REQUEST['sec'] == "san") {
		$tablename = 'utenti_allegati_san';
		$relative_path = ALLEGATI_UTENTI_SAN_W;
		$id_menu = $id_permesso = $modulo_id = 11;
	} elseif ($_REQUEST['sec'] == "allsgq") {
		$idcartella = $_POST['num_cart'];
		//echo("cart:".$idcartella);
		$tablename = 'sgq_allegati';
		$relative_path = ALLEGATI_MODELLI_SGQ_W;
		$id_menu = $id_permesso = $modulo_id = 62;
	}
} else {
	$tablename = 'utenti_allegati';
	$relative_path = ALLEGATI_UTENTI_W;
	$id_menu = $id_permesso = $modulo_id = 11;
}


$nome_campo = 'file_name';

$path = '..//dispense//';

if ($idcartella != "") {

	$conn = db_connect();
	$query = "SELECT * FROM struttura WHERE (status=1) and (cancella='n')";
	$result = mssql_query($query, $conn);
	$row_st = mssql_fetch_assoc($result);
	$intestazione_str = $row_st['intestazione'];
	$piede_str = $row_st['piede'];
	mssql_free_result($result);

	$idfile = $_REQUEST['idfile'];
	$query = "SELECT * from $tablename WHERE idallegato=$idfile";
	$result = mssql_query($query, $conn);
	$total_row = mssql_fetch_assoc($result);
	$filename_tmp = "tmp_" . $random . $total_row['file_name'];
	$filename = $total_row['file_name'];
	$estensione = substr($filename, -3);

	if ($estensione == 'doc' || $estensione == 'dot') {
		$word = new clsMSWord;
		$word->Open($relative_path . $filename);
	} else {

		//$relative_path = $relative_path.'rtf'.DIRECTORY_SEPARATOR;
		$fp = fopen($relative_path . $filename, "rt");
		$contenuto = fread($fp, filesize($relative_path . $filename));
	}

	if ($estensione == 'doc' || $estensione == 'dot') {
		$word->WriteBookmarkText('intestazione_str', unhtmlentities($intestazione_str));
		$word->WriteBookmarkText('piede_str', unhtmlentities($piede_str));
	} else {
		$contenuto = rtfReplaceWord('intestazione_str', $intestazione_str, $contenuto);
		$contenuto = rtfReplaceWord('piede_str', $piede_str, $contenuto);
	}

	//$query="SELECT * FROM re_allegati_sgq_pazienti WHERE (id=$idcartella) ORDER BY max_data DESC";
	$query = "SELECT * FROM re_allegati_sgq_pazienti WHERE (id=$idcartella) ORDER BY idimpegnativa DESC";
	//echo($query);

	$result 	= mssql_query($query, $conn);
	$row		= mssql_fetch_assoc($result);
	$id_utente	= $row['idutente'];

	//RECUPERO ULTIMA DIAGNOSI
	$diagnosi	= "";
	$queryDiag  = "select * from dbo.impegnative where idutente=$id_utente and dataDimissione is null order by idimpegnativa  desc";
	$resultDiag = mssql_query($queryDiag, $conn);
	if ($rowDiag = mssql_fetch_assoc($resultDiag)) {
		$diagnosi = $rowDiag['Diagnosi'];
	} else {
		$queryDiag  = "select * from dbo.impegnative where idutente=$id_utente and dataDimissione is not null order by DataDimissione  desc";
		$resultDiag = mssql_query($queryDiag, $conn);
		if ($rowDiag = mssql_fetch_assoc($resultDiag)) {
			$diagnosi = $rowDiag['Diagnosi'];
		}
	}
	//RECUPERO ULTIMA DIAGNOSI END 

	/* //RECUPERO SESSO PAZIENTE
	// SE Sesso=1 --> Nato | ELSE --> Nata
	$nato	= "";
	$queryNat  = "select sesso from dbo.utenti where idUtente=$id_utente";
	$resultNat= mssql_query($queryDiag, $conn);
	if($rowNat=mssql_fetch_assoc($resultNat)){
		if ($rowNat['nato'] == 1)
			$nato = "nato";
	}else $nato = "nata";
	//RECUPERO SESSO PAZIENTE 
	

	//RECUPERO SIGLA PROVINCIA
	$queryProv  = "SELECT c.sigla AS sigla_prov FROM utenti_residenze as ur LEFT JOIN comuni as c on comune_id=$id_utente";
	$resultProv= mssql_query($queryProv, $conn);
	if($rowNat=mssql_fetch_assoc($resultProv)){
		if ($rowNat['sigla_prov'] !== NULL)
			$sigla_prov = $rowNat['sigla_prov'];
	}else $sigla_prov = "";
	//RECUPERO SIGLA PROVINCIA */
	//$row		= mssql_fetch_assoc($result);


	//OPERATORE CHE STA APRENDO IL FILE
	$user_id = $_SESSION['UTENTE']->get_userid();
	$query_op = "SELECT nome, ordine_professionale, num_iscrizione FROM operatori WHERE uid = $user_id";
	$rs_op = mssql_query($query_op, $conn);
	if (!$rs_op) error_message(mssql_error());
	$row_op = mssql_fetch_assoc($rs_op);
	$firma_operatore = "Dott./Dott.ssa " . trim($row_op['nome']);
	$op_ordine_professionale = trim($row_op['ordine_professionale']);
	$op_num_iscrizione = trim($row_op['num_iscrizione']);

	if ($op_ordine_professionale !== NULL && $op_num_iscrizione !== NULL) {
		if ($estensione == 'doc' || $estensione == 'dot')	$word->WriteBookmarkText('firma_operatore', unhtmlentities($firma_operatore . "\n #" . $op_num_iscrizione . " | " . $op_ordine_professionale));
		else												$contenuto = rtfReplaceWord('firma_operatore', unhtmlentities($firma_operatore . " \par #" . $op_num_iscrizione . " | " . $op_ordine_professionale), $contenuto);
	} else {
		if ($estensione == 'doc' || $estensione == 'dot')	$word->WriteBookmarkText('firma_operatore', unhtmlentities($firma_operatore));
		else												$contenuto = rtfReplaceWord('firma_operatore', unhtmlentities($firma_operatore), $contenuto);
	}
	mssql_free_result($rs_op);


	//DIRETTORE SANITARIO
	$user_id = $_SESSION['UTENTE']->get_userid();
	$query_ds = "SELECT nome, ordine_professionale, num_iscrizione FROM operatori WHERE dir_sanitario = 'y'";
	$rs_ds = mssql_query($query_ds, $conn);
	if (!$rs_ds) error_message(mssql_error());
	$row_ds = mssql_fetch_assoc($rs_ds);
	$direttore_sanitario = "Dott./Dott.ssa " . trim($row_ds['nome']);
	$ds_ordine_professionale = trim($row_ds['ordine_professionale']);
	$ds_num_iscrizione = trim($row_ds['num_iscrizione']);

	$contenuto = rtfReplaceWord('direttore_sanitario_solo_nome', $direttore_sanitario, $contenuto);

	if ($ds_ordine_professionale !== NULL && $ds_num_iscrizione !== NULL) {
		if ($estensione == 'doc' || $estensione == 'dot')	$word->WriteBookmarkText('direttore_sanitario', unhtmlentities($direttore_sanitario . "\n #" . $ds_num_iscrizione . " | " . $ds_ordine_professionale));
		else												$contenuto = rtfReplaceWord('direttore_sanitario', unhtmlentities($direttore_sanitario . " \par #" . $ds_num_iscrizione . " | " . $ds_ordine_professionale), $contenuto);
	} else {
		if ($estensione == 'doc' || $estensione == 'dot')	$word->WriteBookmarkText('direttore_sanitario', unhtmlentities($direttore_sanitario));
		else												$contenuto = rtfReplaceWord('direttore_sanitario', unhtmlentities($direttore_sanitario), $contenuto);
	}
	mssql_free_result($rs_ds);

	if ($estensione == 'doc' || $estensione == 'dot') {
		$word->WriteBookmarkText('paziente_nome', pulisci_lettura($row['Nome']));
		$word->WriteBookmarkText('paziente_cognome', pulisci_lettura($row['Cognome']));
		//$word->WriteBookmarkText('paziente_nato',pulisci_lettura($nato));
		//$word->WriteBookmarkText('paziente_cartella',$row['codice_cartella']."/".convert_versione($row['versione']));
		$word->WriteBookmarkText('paziente_cartella', $row['codice_cartella']);
		$word->WriteBookmarkText('paziente_regime', pulisci_lettura($row1['regime']));
		$word->WriteBookmarkText('paziente_normativa', pulisci_lettura($row1['normativa']));
		$word->WriteBookmarkText('medico_responsabile', pulisci_lettura($row['medico_responsabile']));
		$word->WriteBookmarkText('case_manager', pulisci_lettura($row['case_manager']));
		$word->WriteBookmarkText('paziente_ASL', pulisci_lettura($row['asl']));
		$word->WriteBookmarkText('paziente_Distretto', pulisci_lettura($row['distretto']));
		$word->WriteBookmarkText('paziente_diagnosi', pulisci_lettura($diagnosi));
		$word->WriteBookmarkText('paziente_menomazione', pulisci_lettura($row['menomazione']));
		$word->WriteBookmarkText('paziente_datanascita', formatta_data($row['DataNascita']));
		//$word->WriteBookmarkText('paziente_annonascita',date('Y', strtotime(($row['DataNascita'])));
		$word->WriteBookmarkText('paziente_comune_nascita', pulisci_lettura($row['comune_nascita']));
		$word->WriteBookmarkText('paziente_comune_residenza', pulisci_lettura($row['comune_residenza']));
		//$word->WriteBookmarkText('paziente_prov_residenza',pulisci_lettura($sigla_prov));
		$word->WriteBookmarkText('paziente_indirizzo', pulisci_lettura($row['indirizzo']));
		$word->WriteBookmarkText('codice_fiscale', pulisci_lettura($row['CodiceFiscale']));
		$word->WriteBookmarkText('data_odierna', date('d-m-Y'));
	} else {

		$contenuto = rtfReplaceWord('paziente_nome', pulisci_lettura($row['Nome']), $contenuto);
		$contenuto = rtfReplaceWord('paziente_cognome', pulisci_lettura($row['Cognome']), $contenuto);
		//$contenuto = rtfReplaceWord('paziente_nato',pulisci_lettura($nato),$contenuto);
		$contenuto = rtfReplaceWord('paziente_cartella', $row['codice_cartella'] . "/" . convert_versione($row['versione']), $contenuto);
		//$contenuto = rtfReplaceWord('paziente_cartella',$row['codice_cartella'],$contenuto);
		$contenuto = rtfReplaceWord('paziente_regime', $row['regime'], $contenuto);
		$contenuto = rtfReplaceWord('paziente_normativa', $row['normativa'], $contenuto);
		$contenuto = rtfReplaceWord('medico_responsabile', pulisci_lettura($row['medico_responsabile']), $contenuto);
		$contenuto = rtfReplaceWord('case_manager', pulisci_lettura($row['case_manager']), $contenuto);
		$contenuto = rtfReplaceWord('paziente_ASL', pulisci_lettura($row['asl']), $contenuto);
		$contenuto = rtfReplaceWord('paziente_Distretto', pulisci_lettura($row['distretto']), $contenuto);
		$contenuto = rtfReplaceWord('paziente_diagnosi', pulisci_lettura($diagnosi), $contenuto);
		$contenuto = rtfReplaceWord('paziente_menomazione', pulisci_lettura($row['menomazione']), $contenuto);
		$contenuto = rtfReplaceWord('paziente_datanascita', formatta_data($row['DataNascita']), $contenuto);
		//$contenuto = rtfReplaceWord('paziente_annonascita',date('Y', strtotime($row['DataNascita'])),$contenuto;
		$contenuto = rtfReplaceWord('paziente_comune_nascita', pulisci_lettura($row['comune_nascita']), $contenuto);
		$contenuto = rtfReplaceWord('paziente_comune_residenza', pulisci_lettura($row['comune_residenza']), $contenuto);
		//$contenuto = rtfReplaceWord('paziente_prov_residenza',pulisci_lettura($sigla_prov),$contenuto);
		$contenuto = rtfReplaceWord('paziente_indirizzo', pulisci_lettura($row['indirizzo']), $contenuto);
		$contenuto = rtfReplaceWord('codice_fiscale', pulisci_lettura($row['CodiceFiscale']), $contenuto);
		$contenuto = rtfReplaceWord('data_odierna', date('d-m-Y'), $contenuto);
	}

	/*DIRETTORE SANITARIO
	$query="SELECT operatori.nome as nome, operatori_direttore.data_inizio, operatori_direttore.data_fine, operatori_direttore.tipo, operatori.dir_sanitario, operatori.dir_tecnico 
		FROM operatori INNER JOIN operatori_direttore ON operatori.uid = operatori_direttore.uid WHERE (operatori_direttore.tipo = N'1') 
		AND (operatori.dir_sanitario = 'y') AND (operatori_direttore.data_inizio <= { fn NOW() }) AND (operatori_direttore.data_fine >= { fn NOW() })";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		$row = mssql_fetch_assoc($rs);		
		$dir_sanitario=trim($row['nome']);
		
		if($estensione == 'doc' || $estensione == 'dot'){
			$word->WriteBookmarkText('direttore_sanitario',"Dott./Dott.ssa ".$dir_sanitario);
		}
		else {
			$contenuto=rtfReplaceWord('direttore_sanitario',"Dott./Dott.ssa ".$dir_sanitario,$contenuto);
			
		}
	/*DIRETTORE SANITARIO*/


	$query = "SELECT * FROM re_utenti_tutor WHERE (IdUtente=$id_utente)";
	$result = mssql_query($query, $conn);

	$nome_cognome_tutore01 = $relazione_tutore01 = $comune_nascita_tutore01 = $datanascita_tutore01 = $comune_residenza_tutore01 = $indirizzo_tutore01 = "";
	$nome_cognome_tutore02 = $relazione_tutore02 = $comune_nascita_tutore02 = $datanascita_tutore02 = $comune_residenza_tutore02 = $indirizzo_tutore02 = "";
	$flag_delegato01 = $nome_cognome_delegato01 = $recapiti_delegato01 = "";

	while ($row = mssql_fetch_assoc($result)) {
		if ($row['tutore1'] == -1) {
			$nome_cognome_tutore01 = pulisci_lettura($row['nome']) . " " . pulisci_lettura($row['cognome']);
			$relazione_tutore01 = pulisci_lettura($row['relazione_paziente']);
			$comune_nascita_tutore01 = pulisci_lettura($row['nome_comune_n']) . " " . pulisci_lettura($row['sigla_n']);
			$datanascita_tutore01 = formatta_data($row['data_nascita']);
			$comune_residenza_tutore01 = pulisci_lettura($row['comune_r']) . " " . pulisci_lettura($row['sigla_r']);
			$indirizzo_tutore01 = pulisci_lettura($row['indirizzo']);
		}

		if ($row['tutore2'] == -1) {
			$nome_cognome_tutore02 = pulisci_lettura($row['nome']) . " " . pulisci_lettura($row['cognome']);
			$relazione_tutore02 = pulisci_lettura($row['relazione_paziente']);
			$comune_nascita_tutore02 = pulisci_lettura($row['nome_comune_n']) . " " . pulisci_lettura($row['sigla_n']);
			$datanascita_tutore02 = formatta_data($row['data_nascita']);
			$comune_residenza_tutore02 = pulisci_lettura($row['comune_r']) . " " . pulisci_lettura($row['sigla_r']);
			$indirizzo_tutore02 = pulisci_lettura($row['indirizzo']);
		}

		if ($row['delegato1'] == -1) {
			$flag_delegato01 = 'x';
			$nome_cognome_delegato01 = pulisci_lettura($row['nome']) . " " . pulisci_lettura($row['cognome']);
			$recapiti_delegato01 = $row['telefono'] . " " . $row['cellulare'];
		}
	}


	if ($estensione == 'doc' || $estensione == 'dot') {
		$word->WriteBookmarkText('nome_cognome_tutore01', $nome_cognome_tutore01);
		$word->WriteBookmarkText('relazione_tutore01', $relazione_tutore01);
		$word->WriteBookmarkText('comune_nascita_tutore01', $comune_nascita_tutore01);
		$word->WriteBookmarkText('datanascita_tutore01', $datanascita_tutore01);
		$word->WriteBookmarkText('comune_residenza_tutore01', $comune_residenza_tutore01);
		$word->WriteBookmarkText('indirizzo_tutore01', $indirizzo_tutore01);

		$word->WriteBookmarkText('nome_cognome_tutore02', $nome_cognome_tutore02);
		$word->WriteBookmarkText('relazione_tutore02', $relazione_tutore02);
		$word->WriteBookmarkText('comune_nascita_tutore02', $comune_nascita_tutore02);
		$word->WriteBookmarkText('datanascita_tutore02', $datanascita_tutore02);
		$word->WriteBookmarkText('comune_residenza_tutore02', $comune_residenza_tutore02);
		$word->WriteBookmarkText('indirizzo_tutore02', $indirizzo_tutore02);

		$word->WriteBookmarkText('flag_delegato01', $flag_delegato01);
		$word->WriteBookmarkText('nome_cognome_delegato01', $nome_cognome_delegato01);
		$word->WriteBookmarkText('recapiti_delegato01', $recapiti_delegato01);

		$word->SaveAs($relative_path . "istanze/" . $filename_tmp);
		$word->Quit();
	} else {
		$contenuto = rtfReplaceWord('nome_cognome_tutore01', $nome_cognome_tutore01, $contenuto);
		$contenuto = rtfReplaceWord('relazione_tutore01', $relazione_tutore01, $contenuto);
		$contenuto = rtfReplaceWord('comune_nascita_tutore01', $comune_nascita_tutore01, $contenuto);
		$contenuto = rtfReplaceWord('datanascita_tutore01', $datanascita_tutore01, $contenuto);
		$contenuto = rtfReplaceWord('comune_residenza_tutore01', $comune_residenza_tutore01, $contenuto);
		$contenuto = rtfReplaceWord('indirizzo_tutore01', $indirizzo_tutore01, $contenuto);

		$contenuto = rtfReplaceWord('nome_cognome_tutore02', $nome_cognome_tutore02, $contenuto);
		$contenuto = rtfReplaceWord('relazione_tutore02', $relazione_tutore02, $contenuto);
		$contenuto = rtfReplaceWord('comune_nascita_tutore02', $comune_nascita_tutore02, $contenuto);
		$contenuto = rtfReplaceWord('datanascita_tutore02', $datanascita_tutore02, $contenuto);
		$contenuto = rtfReplaceWord('comune_residenza_tutore02', $comune_residenza_tutore02, $contenuto);
		$contenuto = rtfReplaceWord('indirizzo_tutore02', $indirizzo_tutore02, $contenuto);

		$contenuto = rtfReplaceWord('flag_delegato01', $flag_delegato01, $contenuto);
		$contenuto = rtfReplaceWord('nome_cognome_delegato01', $nome_cognome_delegato01, $contenuto);
		$contenuto = rtfReplaceWord('recapiti_delegato01', $recapiti_delegato01, $contenuto);

		saveRtfFile($relative_path . "istanze/", $filename_tmp, $contenuto);
		//add_log($filename_tmp,$idpaziente,"0",0);			
		$handle = fopen($relative_path . "istanze/" . $filename_tmp, "r");
		$content2 = fread($handle, filesize($relative_path . "istanze/" . $filename_tmp));
		fclose($handle);
		header('Content-Type: application/rtf');
		header("Content-Disposition: attachment; filename=" . $filename_tmp);
		echo $content2;
		die();
	}


	Visualizza_File($relative_path . "istanze/", $filename_tmp);
	exit();
}

function Visualizza_File($path, $file)
{
	$handle = fopen($path . $file, "r");
	$content = fread($handle, filesize($path . $file));
	fclose($handle);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$file");
	header("Expires: 0");
	header("Pragma: no-cache");
	echo ($content);
}


if (isset($_REQUEST['call']))
	if ($_REQUEST['call'] == "1") {
		$relative_path = MODELLI_WORD_PATH;
		$tablename = 'moduli';
		$nome_campo = 'modello_word';
	}

//controllo se l'utente puo' accedere al file
$gruppo = $_SESSION['UTENTE']->get_tipoaccesso();
//$gid=$_REQUEST['ngid'];

// se non si appartiene al gruppo associato

// recupera l'id del file
$idfile = $_REQUEST['idfile'];

$conn = db_connect();

$query = "";

if (isset($_REQUEST['sec']) && $_REQUEST['sec'] == "allsgq")
	$query = "SELECT * from $tablename WHERE idallegato=$idfile";
else
	$query = "SELECT * from $tablename WHERE id=$idfile";


$rt = mssql_query($query, $conn);
if (!$rt) error_message(mysql_error());
if (mssql_num_rows($rt) > 0) {
	$total_row = mssql_fetch_assoc($rt);

	if ($gruppo == "") {
		error_message("Impossibile visualizzare il file!Non si dispone delle autorizzazioni necessarie");
	} else {
		//echo($_REQUEST['sec']." ".$gruppo);
		if (((isset($_REQUEST['sec']) && $_REQUEST['sec'] == "san") and (($gruppo == 3) or ($gruppo == 2) or ($gruppo == 5)))
			or (isset($_REQUEST['sec']) && $_REQUEST['sec'] != "san")
			or !isset($_REQUEST['sec'])
		) {

			if ($tablename != 'moduli') {
				$filename_tmp = "tmp_" . $random . $total_row['file_name'];
				$filename = $total_row['file_name'];
			} else {
				$filename_tmp = "tmp_" . $random . $total_row['modello_word'];
				$filename = $total_row['modello_word'];
			}
			//$type=$total_row['type'];

			$curr_path = $relative_path;

			$filename = trim($filename);



			$handle = fopen($curr_path . $filename, "r");
			$content = fread($handle, filesize($curr_path . $filename));
			fclose($handle);

			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$filename");
			header("Expires: 0");
			header("Pragma: no-cache");
			echo ($content);
		} elseif ($_REQUEST['sec'] == "allsgq") {
			$filename_tmp = "tmp_" . $random . $total_row['file_name'];
			$filename = $total_row['file_name'];
			$curr_path = $relative_path;

			$filename = trim($filename);



			$handle = fopen($curr_path . $filename, "r");
			$content = fread($handle, filesize($curr_path . $filename));
			fclose($handle);

			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$filename");
			header("Expires: 0");
			header("Pragma: no-cache");
			echo ($content);
		} else {
			error_message("Impossibile visualizzare il file! Permesso non disponibile");
		}
	}
} else {

	error_message("Il file richiesto non � presente sul server");
}

function rtfReplaceWord($bookmark, $word, $content)
{

	//echo $bookmark.chr(13).chr(10);

	$bookmark = trim($bookmark);
	$ret = str_replace("##$bookmark##", $word, $content);
	return $ret;
}
function saveRtfFile($path, $name, $rtf)
{
	// $rtf holds your complete file data

	file_put_contents($path . $name, $rtf);
}
