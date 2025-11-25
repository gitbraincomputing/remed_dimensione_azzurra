<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
*/

// inclusione delle classi
include_once('include/class.User.php');
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/function_page.php');
include_once('include/dbengine.inc.php');

$relative_path = MODELLI_WORD_PATH;
$rtf_destination_path = MODELLI_WORD_DEST_PATH;

$gruppo = $_SESSION['UTENTE']->get_gid();
if (isset($_REQUEST['ngid']))
    $gid = $_REQUEST['ngid'];
else
    $gid = 0;

$conn = db_connect();
$query = "SELECT * FROM struttura WHERE (status=1) and (cancella='n')";
$result = mssql_query($query, $conn);
$row_st = mssql_fetch_assoc($result);
$intestazione_str = $row_st['intestazione'];
$piede_str = $row_st['piede'];
mssql_free_result($result);

// Inizializzazione variabili per evitare Notice
$modello = isset($_REQUEST['modello']) ? $_REQUEST['modello'] : null;
$consenso = isset($_REQUEST['consenso']) ? $_REQUEST['consenso'] : null;
$idcartella = isset($_REQUEST['idcartella']) ? $_REQUEST['idcartella'] : null;
$idarea = isset($_REQUEST['idarea']) ? $_REQUEST['idarea'] : null;
$idpaziente = isset($_REQUEST['idpaziente']) ? $_REQUEST['idpaziente'] : null;
$idinserimento = isset($_REQUEST['idinserimento']) ? $_REQUEST['idinserimento'] : null;
$idmodulopadre = isset($_REQUEST['idmodulopadre']) ? $_REQUEST['idmodulopadre'] : null;
$idmoduloversione = isset($_REQUEST['idmoduloversione']) ? $_REQUEST['idmoduloversione'] : null;
$all = isset($_REQUEST['all']) ? $_REQUEST['all'] : '';

if ($idmoduloversione != "") {
    $result_file = Stampa_Modulo_Segnalibro($idmoduloversione);
    Visualizza_File($rtf_destination_path, $result_file);
    exit();
}

if ($idarea != "") {
    $result_file = Stampa_Modulo_sgq($idarea, $idinserimento, $idmodulopadre);
    Visualizza_File($rtf_destination_path, $result_file);
    exit();
}

if ($consenso == "informato") {
    $result_file = Stampa_Consenso($idpaziente);
    Visualizza_File($rtf_destination_path, $result_file);
    exit();
} elseif ($modello != "") {
    $result_file = Stampa_Modello($idpaziente, $modello);
    Visualizza_File($rtf_destination_path, $result_file);
    exit();
}

// se non si appartiene al gruppo associato
/*if ($gruppo!=$gid) {
	error_message("Impossibile visualizzare il file!Non si dispone delle autorizzazioni necessarie");
}else { */

if ($all == "si")
    $result_file = Stampa_Cartella($idcartella, $idpaziente);
else
    $result_file = Stampa_Modulo($idcartella, $idpaziente, $idinserimento, $idmodulopadre);

Visualizza_File($rtf_destination_path, $result_file);
//}

function add_log($file, $idpaziente, $idmodulo, $idcartella)
{

    $conn = db_connect();
    $datains = date('d/m/Y');
    $orains = date('H:i');
    $ipins = $_SERVER['REMOTE_ADDR'];
    $opeins = $_SESSION['UTENTE']->get_userid();
    $query_log = "insert into log_stampa_moduli (id_utente,id_modulo,nome_file,data_stampa,ora_stampa,ope_stampa,ip_stampa,idcartella,tipo) values ($idpaziente,$idmodulo,'$file','$datains','$orains',$opeins,'$ipins',$idcartella,'word')";
    $result_log = mssql_query($query_log, $conn);
}


function add_log_sgq($file, $idmodulo, $idarea)
{

    $conn = db_connect();
    $datains = date('d/m/Y');
    $orains = date('H:i');
    $ipins = $_SERVER['REMOTE_ADDR'];
    $opeins = $_SESSION['UTENTE']->get_userid();
    $query_log = "insert into log_stampa_moduli (id_utente,id_modulo,nome_file,data_stampa,ora_stampa,ope_stampa,ip_stampa,idcartella,tipo) values (0,$idmodulo,'$file','$datains','$orains',$opeins,'$ipins',$idarea,'word')";
    $result_log = mssql_query($query_log, $conn);
}

function Stampa_Modulo_Segnalibro($idmoduloversione)
{
    global $relative_path, $rtf_destination_path, $intestazione_str, $piede_str;

    $conn = db_connect();
    $query = "SELECT * FROM moduli WHERE (id =$idmoduloversione)";
    $rs = mssql_query($query, $conn);
    if (!$rs) error_message(mysql_error());
    $row_rs = mssql_fetch_assoc($rs);
    $nome = $row_rs['nome'];
    mssql_free_result($rs);
    $content = "nome modulo: " . $nome . "\r\n\r\n";
    $query = "SELECT idmoduloversione, etichetta, segnalibro,tipo FROM dbo.campi WHERE (idmoduloversione =$idmoduloversione)";
    $rs = mssql_query($query, $conn);
    while ($row = mssql_fetch_assoc($rs)) {
        if ($row['tipo'] != "5")
            $content .= $row['etichetta'] . ": " . $row['segnalibro'] . "\r\n";
        else
            $content .= "\r\n" . $row['etichetta'] . "\r\n";
    }
    $content .= "\r\nFINE elenco campi";
    srand((float)microtime() * 1000000);
    $random = rand(0, 999999999999999);
    $filename_tmp = "tmp_sl_" . $random . ".txt";
    $handle = fopen($rtf_destination_path . $filename_tmp, "w");
    fwrite($handle, $content);
    return ($filename_tmp);
}




function Stampa_Modello($idpaziente, $modello)
{
    global $relative_path, $rtf_destination_path, $intestazione_str, $piede_str;
    die();


    $conn = db_connect();

    $query = "SELECT * FROM modelli_word WHERE (codice ='$modello')";
    $rs = mssql_query($query, $conn);
    if (!$rs) error_message(mysql_error());
    $row_rs = mssql_fetch_assoc($rs);
    $file = $row_rs['modello'];
    mssql_free_result($rs);
    srand((float)microtime() * 1000000);
    $random = rand(0, 999999999999999);
    $i = 1;
    $query = "select utenti.Cognome as cognome, utenti.Nome as nome, utenti.IdUtente, utenti.sesso, comuni.denominazione as comune_n, comuni.sigla as prov_n, utenti.DataNascita as data_n FROM utenti INNER JOIN comuni ON utenti.LuogoNascita_id = comuni.id_comune where IdUtente=$idpaziente";
    $rt = mssql_query($query, $conn);
    if (!$rt) error_message(mysql_error());
    $row = mssql_fetch_assoc($rt);



    $filename_tmp     = "tmp_" . $random . $file;
    $filename         = $file;
    $estensione     = substr($filename, -3);

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word = new clsMSWord;
        $word->Open($relative_path . $filename);
    } else {
        //$relative_path = $relative_path.'rtf'.DIRECTORY_SEPARATOR;
        $fp = fopen($relative_path . $filename, "rt");
        $contenuto = fread($fp, filesize($relative_path . $filename));
    }
    $i++;

    $terapista = $_REQUEST['terapista'];
    if (($terapista != "0") and ($terapista != "")) {
        $query6 = "SELECT nome, uid FROM operatori WHERE uid=$terapista";
        //echo($query6);
        $rs6 = mssql_query($query6, $conn);
        if (!$rs6) error_message(mssql_error());
        $row6 = mssql_fetch_assoc($rs6);
        $terapista = trim($row6['nome']);
        if ($estensione == 'doc' || $estensione == 'dot')
            $word->WriteBookmarkText('terapista', $terapista);
        else
            $contenuto = rtfReplaceWord('terapista', $terapista, $contenuto);
    }

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('nome', strtoupper(trim(unhtmlentities($row['nome']))));
        $word->WriteBookmarkText('cognome', strtoupper(trim(unhtmlentities($row['cognome']))));
        $word->WriteBookmarkText('nome_1', strtoupper(trim(unhtmlentities($row['nome']))));
        $word->WriteBookmarkText('cognome_1', strtoupper(trim(unhtmlentities($row['cognome']))));
        $word->WriteBookmarkText('cartella', $idpaziente);
    } else {
        $contenuto = rtfReplaceWord('nome', strtoupper(trim(unhtmlentities($row['nome']))), $contenuto);
        $contenuto = rtfReplaceWord('cognome', strtoupper(trim(unhtmlentities($row['cognome']))), $contenuto);
        $contenuto = rtfReplaceWord('nome_1', strtoupper(trim(unhtmlentities($row['nome']))), $contenuto);
        $contenuto = rtfReplaceWord('cognome_1', strtoupper(trim(unhtmlentities($row['cognome']))), $contenuto);
        $contenuto = rtfReplaceWord('cartella', $idpaziente, $contenuto);
    }

    if ($row['sesso'] == "2")
        $nascita = "Nata il ";
    else
        $nascita = "Nato il ";

    if ($row['data_n'] == "")
        $data_n = "";
    else
        $data_n = trim(formatta_data($row['data_n']));

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('data_nascita', $data_n);
    } else {
        $contenuto = rtfReplaceWord('data_nascita', $data_n, $contenuto);
    }

    $nascita .= $data_n;
    if ($row['comune_n'] == "")
        $comune_n = "";
    else
        $comune_n = trim($row['comune_n']) . " (" . $row['prov_n'] . ")";
    $nascita .= " a " . $comune_n;

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('luogo_nascita', $comune_n);
        $word->WriteBookmarkText('nascita', $nascita);
    } else {
        $contenuto = rtfReplaceWord('luogo_nascita', $comune_n, $contenuto);
        $contenuto = rtfReplaceWord('nascita', $nascita, $contenuto);
    }

    $query = "SELECT comuni.denominazione as comune_p, comuni.sigla as prov_p, utenti_residenze.indirizzo as indirizzo, utenti_residenze.telefono as telefono, 
            utenti_residenze.IdUtente, utenti_residenze.id FROM comuni INNER JOIN utenti_residenze ON comuni.id_comune = utenti_residenze.comune_id
			where IdUtente=$idpaziente ORDER BY utenti_residenze.id DESC";
    $rt = mssql_query($query, $conn);
    if (!$rt) error_message(mysql_error());
    $row = mssql_fetch_assoc($rt);

    if ($row['indirizzo'] == "")
        $indirizzo = "";
    else
        $indirizzo = trim($row['indirizzo']);

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('indirizzo', unhtmlentities($indirizzo));
    } else {
        $contenuto = rtfReplaceWord('indirizzo', unhtmlentities($indirizzo), $contenuto);
    }

    if ($row['comune_p'] == "")
        $comune_p = "";
    else
        $comune_p = trim($row['comune_p']) . " (" . $row['prov_p'] . ")";

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('citta', $comune_p);
    } else {
        $contenuto = rtfReplaceWord('citta', $comune_p, $contenuto);
    }

    if ($row['telefono'] == "")
        $telefono = "";
    else
        $telefono = trim($row['telefono']);

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('telefono', $telefono);
        $word->WriteBookmarkText('data', date("d/m/Y"));
        $word->WriteBookmarkText('luogo', LUOGO_CENTRO);
    } else {
        $contenuto = rtfReplaceWord('telefono', $telefono, $contenuto);
        $contenuto = rtfReplaceWord('data', date("d/m/Y"), $contenuto);
        $contenuto = rtfReplaceWord('luogo', LUOGO_CENTRO, $contenuto);
    }



    if (isset($_REQUEST['idtutor'])) {
        $idtutor = $_REQUEST['idtutor'];
        $idtutor_2 = $_REQUEST['idtutor2'];
        $query = "SELECT dbo.re_utenti_tutor.* FROM dbo.re_utenti_tutor WHERE (id = $idtutor) ORDER BY id DESC";
        $rs = mssql_query($query, $conn);
        if (!$rs) error_message(mssql_error());
        $num_rows = mssql_num_rows($rs);
        $row = mssql_fetch_assoc($rs);
        $tut_nome = strtoupper(trim(unhtmlentities($row['nome'])));
        $tut_cognome = strtoupper(trim(unhtmlentities($row['cognome'])));
        $tut_relazione = strtoupper(trim(unhtmlentities($row['relazione_paziente'])));
        $tut_data_nascita = formatta_data($row['data_nascita']);
        $tut_luogo_nascita = strtoupper(trim(unhtmlentities($row['nome_comune_n'])));
        $tut_luogo_nascita .= " (" . trim($row['sigla_n']) . ")";
        $tut_comune = strtoupper(trim(unhtmlentities($row['comune_r'])));
        $tut_comune .= " (" . trim($row['sigla_r']) . ")";
        $tut_indirizzo = trim($row['indirizzo']);
        $tut_codice_fiscale = trim($row['codice_fiscale']);

        if ($estensione == 'doc' || $estensione == 'dot') {
            $word->WriteBookmarkText('cod_fiscale_t', $tut_codice_fiscale);
            $word->WriteBookmarkText('indirizzo_t', $tut_indirizzo);
            $word->WriteBookmarkText('comune_t', $tut_comune);
            $word->WriteBookmarkText('luogo_nascita_t', $tut_luogo_nascita);
            $word->WriteBookmarkText('luogo_nascita_t_1', $tut_luogo_nascita);
            $word->WriteBookmarkText('data_nascita_t', $tut_data_nascita);
            $word->WriteBookmarkText('data_nascita_t_1', $tut_data_nascita);
            $word->WriteBookmarkText('genitore', $tut_nome . " " . $tut_cognome);
            $word->WriteBookmarkText('genitore_1', $tut_nome . " " . $tut_cognome);
            $word->WriteBookmarkText('genitore_11', $tut_nome . " " . $tut_cognome);
            $word->WriteBookmarkText('relazione', $tut_relazione);
        } else {
            $contenuto = rtfReplaceWord('cod_fiscale_t', $tut_codice_fiscale, $contenuto);
            $contenuto = rtfReplaceWord('indirizzo_t', $tut_indirizzo, $contenuto);
            $contenuto = rtfReplaceWord('comune_t', $tut_comune, $contenuto);
            $contenuto = rtfReplaceWord('luogo_nascita_t', $tut_luogo_nascita, $contenuto);
            $contenuto = rtfReplaceWord('luogo_nascita_t_1', $tut_luogo_nascita, $contenuto);
            $contenuto = rtfReplaceWord('data_nascita_t', $tut_data_nascita, $contenuto);
            $contenuto = rtfReplaceWord('data_nascita_t_1', $tut_data_nascita, $contenuto);
            $contenuto = rtfReplaceWord('genitore', $tut_nome . " " . $tut_cognome, $contenuto);
            $contenuto = rtfReplaceWord('genitore_1', $tut_nome . " " . $tut_cognome, $contenuto);
            $contenuto = rtfReplaceWord('genitore_11', $tut_nome . " " . $tut_cognome, $contenuto);
            $contenuto = rtfReplaceWord('relazione', $tut_relazione, $contenuto);
        }


        if ($idtutor_2 != "") {
            $query = "SELECT dbo.re_utenti_tutor.* FROM dbo.re_utenti_tutor WHERE (id = $idtutor_2) ORDER BY id DESC";
            $rs = mssql_query($query, $conn);
            if (!$rs) error_message(mssql_error());
            $num_rows = mssql_num_rows($rs);
            $row = mssql_fetch_assoc($rs);
            $tut_nome = strtoupper(trim(unhtmlentities($row['nome'])));
            $tut_cognome = strtoupper(trim(unhtmlentities($row['cognome'])));
            $tut_data_nascita = formatta_data($row['data_nascita']);
            $tut_luogo_nascita = strtoupper(trim(unhtmlentities($row['nome_comune_n'])));
            $tut_luogo_nascita .= " (" . trim($row['sigla_n']) . ")";

            if ($estensione == 'doc' || $estensione == 'dot') {
                $word->WriteBookmarkText('luogo_nascita_t_2', $tut_luogo_nascita);
                $word->WriteBookmarkText('data_nascita_t_2', $tut_data_nascita);
                $word->WriteBookmarkText('genitore_2', $tut_nome . " " . $tut_cognome);
                $word->WriteBookmarkText('genitore_21', $tut_nome . " " . $tut_cognome);
            } else {
                $contenuto = rtfReplaceWord('luogo_nascita_t_2', $tut_luogo_nascita, $contenuto);
                $contenuto = rtfReplaceWord('data_nascita_t_2', $tut_data_nascita, $contenuto);
                $contenuto = rtfReplaceWord('genitore_2', $tut_nome . " " . $tut_cognome, $contenuto);
                $contenuto = rtfReplaceWord('genitore_21', $tut_nome . " " . $tut_cognome, $contenuto);
            }
        }
    }

    if (isset($_REQUEST['idimpegnativa'])) {

        $query = "SELECT dbo.comuni.sigla as sigla, dbo.comuni.denominazione as citta, dbo.utenti_medici.* FROM dbo.utenti_medici INNER JOIN
                      dbo.comuni ON dbo.utenti_medici.comune_id = dbo.comuni.id_comune WHERE (dbo.utenti_medici.IdUtente = $idpaziente) ORDER BY dbo.utenti_medici.decorrenza DESC";
        $rs = mssql_query($query, $conn);
        if (!$rs) error_message(mssql_error());
        $num_rows = mssql_num_rows($rs);
        $row = mssql_fetch_assoc($rs);
        $med_nome = trim(unhtmlentities($row['nome']));
        $med_cognome = trim(unhtmlentities($row['cognome']));
        $med_indirizzo = trim(unhtmlentities($row['indirizzo']));
        $med_citta = trim(unhtmlentities($row['citta']));
        $med_citta .= "(" . trim($row['sigla']) . ")";
        mssql_free_result($rs);

        if ($estensione == 'doc' || $estensione == 'dot') {
            $word->WriteBookmarkText('nome_cognome_medico', $med_nome . " " . $med_cognome);
            $word->WriteBookmarkText('indirizzo_medico', $med_indirizzo);
            $word->WriteBookmarkText('citta_medico', $med_citta);
        } else {
            $contenuto = rtfReplaceWord('nome_cognome_medico', $med_nome . " " . $med_cognome, $contenuto);
            $contenuto = rtfReplaceWord('indirizzo_medico', $med_indirizzo, $contenuto);
            $contenuto = rtfReplaceWord('citta_medico', $med_citta, $contenuto);
        }


        $idimpegnativa = $_REQUEST['idimpegnativa'];
        $query = "SELECT * FROM re_impegnative_modelli_sgq WHERE (idimpegnativa =  $idimpegnativa)";
        $rs = mssql_query($query, $conn);
        if (!$rs) error_message(mssql_error());
        $num_rows = mssql_num_rows($rs);
        $row = mssql_fetch_assoc($rs);
        $data_inizio = formatta_data($row['DataInizioTrattamento']);
        $data_fine = formatta_data($row['DataFineTrattamento']);
        $ultimo_trattamento = formatta_data($row['ultimo_trattamento']);
        $frequenza = trim($row['ggfrequenza']);
        $num_trattamenti = trim($row['NumeroTrattamenti']);
        $case_manager = trim(unhtmlentities($row['case_manager_nome']));
        $diagnosi = trim(unhtmlentities($row['Diagnosi']));
        $menomazione = trim(unhtmlentities($row['menomazione']));
        $obiettivi = trim(pulisci_lettura($row['obiettivi']));
        $norma_regime = trim(unhtmlentities($row['normativa_nome'])) . " " . trim(unhtmlentities($row['regime_nome']));
        mssql_free_result($rs);

        if ($estensione == 'doc' || $estensione == 'dot') {
            $word->WriteBookmarkText('data_inizio', $data_inizio);
            $word->WriteBookmarkText('data_inizio_1', $data_inizio);
            $word->WriteBookmarkText('data_fine', $data_fine);
            $word->WriteBookmarkText('frequenza', $frequenza);
            $word->WriteBookmarkText('paziente_diagnosi', $diagnosi);
            $word->WriteBookmarkText('paziente_menomazione', $menomazione);
            $word->WriteBookmarkText('ultimo_trattamento', $ultimo_trattamento);
            $word->WriteBookmarkText('normativa_regime', $norma_regime);
            $word->WriteBookmarkText('normativa_regime1', $norma_regime);
            $word->WriteBookmarkText('obiettivi', $obiettivi);
            $word->WriteBookmarkText('obiettivi1', $obiettivi);
            $word->WriteBookmarkText('frequenza_trattamenti', $frequenza . " trattamenti a settimana per " . $num_trattamenti . " trattamenti totali");
            $word->WriteBookmarkText('case_manager', "dott./dott.ssa " . $case_manager);
        } else {
            $contenuto = rtfReplaceWord('data_inizio', $data_inizio, $contenuto);
            $contenuto = rtfReplaceWord('data_inizio_1', $data_inizio, $contenuto);
            $contenuto = rtfReplaceWord('data_fine', $data_fine, $contenuto);
            $contenuto = rtfReplaceWord('paziente_diagnosi', $diagnosi, $contenuto);
            $contenuto = rtfReplaceWord('frequenza', $frequenza, $contenuto);
            $contenuto = rtfReplaceWord('paziente_menomazione', $menomazione, $contenuto);
            $contenuto = rtfReplaceWord('ultimo_trattamento', $ultimo_trattamento, $contenuto);
            $contenuto = rtfReplaceWord('normativa_regime', $norma_regime, $contenuto);
            $contenuto = rtfReplaceWord('normativa_regime1', $norma_regime, $contenuto);
            $contenuto = rtfReplaceWord('obiettivi', $obiettivi, $contenuto);
            $contenuto = rtfReplaceWord('obiettivi1', $obiettivi, $contenuto);
            $contenuto = rtfReplaceWord('frequenza_trattamenti', $frequenza . " trattamenti a settimana per " . $num_trattamenti . " trattamenti totali", $contenuto);
            $contenuto = rtfReplaceWord('case_manager', $case_manager, $contenuto);
        }

        /*
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
			
		}*/
    }

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('intestazione_str', $intestazione_str);
        $word->WriteBookmarkText('piede_str', $piede_str);
        $filename = trim($filename);
        $word->SaveAs($rtf_destination_path . $filename_tmp);
        //$word->Close();
        $word->Quit();
        //add_log($filename_tmp,$idpaziente,"0");

        return ($filename_tmp);
    } else {
        $contenuto = rtfReplaceWord('intestazione_str', $intestazione_str, $contenuto);
        $contenuto = rtfReplaceWord('piede_str', $piede_str, $contenuto);
        saveRtfFile($rtf_destination_path, $filename_tmp, $contenuto);
        //add_log($filename_tmp,$idpaziente,"0",0);

        $handle = fopen($rtf_destination_path . $filename_tmp, "r");
        $content2 = fread($handle, filesize($rtf_destination_path . $filename_tmp));
        fclose($handle);
        header('Content-Type: application/rtf');
        header("Content-Disposition: attachment; filename=" . $filename_tmp);
        echo $content2;
        die();
    }
}


function Stampa_Consenso($idpaziente)
{
    global $relative_path, $rtf_destination_path, $intestazione_str, $piede_str;

    $conn = db_connect();
    $query = "SELECT * FROM modelli_word WHERE (codice ='" . $_REQUEST['modello'] . "')";
    $rs = mssql_query($query, $conn);
    if (!$rs) error_message(mysql_error());
    $row_rs = mssql_fetch_assoc($rs);
    $file = $row_rs['modello'];
    mssql_free_result($rs);
    srand((float)microtime() * 1000000);
    $random = rand(0, 999999999999999);
    $i = 1;
    $query = "select utenti.Cognome as cognome, utenti.Nome as nome, utenti.IdUtente, utenti.sesso, comuni.denominazione as comune_n, comuni.sigla as prov_n, utenti.DataNascita as data_n FROM utenti INNER JOIN comuni ON utenti.LuogoNascita_id = comuni.id_comune where IdUtente=$idpaziente";
    $rt = mssql_query($query, $conn);
    if (!$rt) error_message(mysql_error());
    $row = mssql_fetch_assoc($rt);
    //while($row=mssql_fetch_assoc($rt))	{
    //if($i==1){			
    $filename_tmp = "tmp_" . $random . $file;
    $filename = $file;
    $estensione = substr($filename, -3);

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word = new clsMSWord;
        $word->Open($relative_path . $filename);
    } else {
        //$relative_path = $relative_path.'rtf'.DIRECTORY_SEPARATOR;
        $fp = fopen($relative_path . $filename, "rt");
        $contenuto = fread($fp, filesize($relative_path . $filename));
    }
    $i++;
    //}
    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('nome', trim($row['nome']));
        $word->WriteBookmarkText('cognome', trim($row['cognome']));
    } else {
        $contenuto = rtfReplaceWord('nome', trim($row['nome']), $contenuto);
        $contenuto = rtfReplaceWord('cognome', trim($row['cognome']), $contenuto);
    }
    if ($row['sesso'] == "2")
        $nascita = "Nata il ";
    else
        $nascita = "Nato il ";

    if ($row['data_n'] == "")
        $data_n = "";
    else
        $data_n = trim(formatta_data($row['data_n']));
    $nascita .= $data_n;
    if ($row['comune_n'] == "")
        $comune_n = "";
    else
        $comune_n = trim($row['comune_n']) . " (" . $row['prov_n'] . ")";
    $nascita .= " a " . $comune_n;
    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('nascita', $nascita);
    } else {
        $contenuto = rtfReplaceWord('nascita', $nascita, $contenuto);
    }

    $query = "SELECT comuni.denominazione as comune_p, comuni.sigla as prov_p, utenti_residenze.indirizzo as indirizzo, utenti_residenze.telefono as telefono, 
            utenti_residenze.IdUtente, utenti_residenze.id FROM comuni INNER JOIN utenti_residenze ON comuni.id_comune = utenti_residenze.comune_id
			where IdUtente=$idpaziente ORDER BY utenti_residenze.id DESC";
    $rt = mssql_query($query, $conn);
    if (!$rt) error_message(mysql_error());
    $row = mssql_fetch_assoc($rt);

    if ($row['indirizzo'] == "")
        $indirizzo = "";
    else
        $indirizzo = trim($row['indirizzo']);

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('indirizzo', $indirizzo);
    } else {
        $contenuto = rtfReplaceWord('indirizzo', $indirizzo, $contenuto);
    }

    if ($row['comune_p'] == "")
        $comune_p = "";
    else
        $comune_p = trim($row['comune_p']) . " (" . $row['prov_p'] . ")";

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('citta', $comune_p);
    } else {
        $contenuto = rtfReplaceWord('citta', $comune_p, $contenuto);
    }

    if ($row['telefono'] == "")
        $telefono = "";
    else
        $telefono = trim($row['telefono']);

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('telefono', $telefono);
        $word->WriteBookmarkText('data', date("d/m/Y"));
    } else {
        $contenuto = rtfReplaceWord('telefono', $telefono, $contenuto);
        $contenuto = rtfReplaceWord('data', date("d/m/Y"), $contenuto);
    }

    //}		

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('intestazione_str', $intestazione_str);
        $word->WriteBookmarkText('piede_str', $piede_str);
        $filename = trim($filename);
        $word->SaveAs($rtf_destination_path . $filename_tmp);
        //$word->Close();
        $word->Quit();
        //add_log($filename_tmp,$idpaziente,"0");
        return ($filename_tmp);
    } else {
        $contenuto = rtfReplaceWord('intestazione_str', $intestazione_str, $contenuto);
        $contenuto = rtfReplaceWord('piede_str', $piede_str, $contenuto);
        saveRtfFile($rtf_destination_path, $filename_tmp, $contenuto);
        //add_log($filename_tmp,$idpaziente,"0",0);
        $handle = fopen($rtf_destination_path . $filename_tmp, "r");
        $content2 = fread($handle, filesize($rtf_destination_path . $filename_tmp));
        fclose($handle);
        header('Content-Type: application/rtf');
        header("Content-Disposition: attachment; filename=" . $filename_tmp);
        echo $content2;
        die();
    }
}


function Stampa_cartella($idcartella, $idpaziente)
{
    global $relative_path, $rtf_destination_path, $intestazione_str, $piede_str;

    $conn = db_connect();
    srand((float)microtime() * 1000000);
    $random = rand(0, 999999999999999);
    $query = "SELECT idcartella AS cartella, peso_modulo, idcartella, modello_word, nome, idinserimento, idmodulo FROM dbo.re_moduli_valori_stampa 
			GROUP BY idcartella, peso_modulo, modello_word, nome, idinserimento, idmodulo HAVING (idcartella = $idcartella)
			ORDER BY peso_modulo, idinserimento";

    // Extract content.
    $content = (string) $word->ActiveDocument->Content;

    return ($filename_tmp);
}


function Stampa_Modulo($idcartella, $idpaziente, $idinserimento, $idmodulopadre)
{
    global $relative_path, $rtf_destination_path, $intestazione_str, $piede_str;

    //die($piede_str);
    $query_imp = "";
    if (isset($_REQUEST['idimpegnativa'])) {
        $idimpegnativa = $_REQUEST['idimpegnativa'];
        if ($idimpegnativa != "") $query_imp = "and id_impegnativa=$idimpegnativa ";
    }

    $conn = db_connect();
    srand((float)microtime() * 1000000);
    //$random = rand(0, 999999999999999);
    $random = md5( microtime() );		// sostituito a rand() poiche si e' verificata una doppia generazione dello stesso numero casuale
    $i = 1;
    if ($idcartella != 0) {
        $query = "select * from re_moduli_valori_stampa where (id_inserimento=$idinserimento and id_cartella=$idcartella and id_modulo_padre=$idmodulopadre $query_imp) ORDER BY peso ASC";
        $query_regime = "select idregime from utenti_cartelle where id = $idcartella";
    } else {
        $query = "select * from re_moduli_valori_stampa where (id_modulo_padre=$idmodulopadre and id_inserimento=$idinserimento and id_cartella=0 and idpaziente=$idpaziente $query_imp) ORDER BY peso ASC";
        $query_regime = "SELECT uc.idregime
							FROM utenti_cartelle uc
							INNER JOIN istanze_testata it ON it.id_cartella = uc.id
							WHERE it.id_modulo_padre = $idmodulopadre AND it.id_inserimento = $idinserimento  AND it.id_paziente = $idpaziente";
    }

    $rt = mssql_query($query, $conn);
    if (!$rt) error_message(mysql_error());
    $num_istanza = $idinserimento;
    $arr_segnalibri = array();
    $vecchio_automatismo_tempi_verifica = 1;        // mi serve per gestire il segnalibro "tempi_verifica" popolato con un query per le vecchie versoine dei programmi riabilitativi. se trovo il segnalibro, è una vers. del modulo nuovo con il campo editabile e non mi serve la vecchia automazione

    $rt_regime = mssql_query($query_regime, $conn);
    if (!$rt_regime) error_message(mysql_error());
    $regime = mssql_fetch_assoc($rt_regime);
    $idregime = $regime['idregime'];

    $arr_word = array();

    $arr_word['grado_lesione_I'] = '[   ]';
    $arr_word['grado_lesione_II'] = '[   ]';
    $arr_word['grado_lesione_III'] = '[   ]';
    $arr_word['grado_lesione_IV'] = '[   ]';
    $arr_word['grado_lesione_escara'] = '[   ]';

    $arr_word['condizioni_lesione_detersa'] = '[   ]';
    $arr_word['condizioni_lesione_fibrina'] = '[   ]';
    $arr_word['condizioni_lesione_essudato'] = '[   ]';
    $arr_word['condizioni_lesione_necrosi'] = '[   ]';
    $arr_word['condizioni_lesione_infetta'] = '[   ]';

    $arr_word['bordi_lesione_lineari'] = '[   ]';
    $arr_word['bordi_lesione_macerati'] = '[   ]';
    $arr_word['bordi_lesione_necrotici'] = '[   ]';
    $arr_word['bordi_lesione_infetti'] = '[   ]';
    $arr_word['bordi_lesione_frastagliati'] = '[   ]';

    $arr_word['cute_perilesionale_integra'] = '[   ]';
    $arr_word['cute_perilesionale_arrossata'] = '[   ]';
    $arr_word['cute_perilesionale_macerata'] = '[   ]';

    $arr_word['pellicola_semiperm_trasp_poliuretano'] = '[   ]';
    $arr_word['idrocolloidi_extra_sottili'] = '[   ]';
    $arr_word['schiuma_poliuretano'] = '[   ]';

    $arr_word['flittene_forare_senza_rimuovere_tetto'] = '[   ]';
    $arr_word['flittene_schiuma_poliuretano'] = '[   ]';

    $arr_word['escara_pomate_garze'] = '[   ]';
    $arr_word['escara_idrogeli_schiuma'] = '[   ]';
    $arr_word['escara_rimozione_graduale'] = '[   ]';
    $arr_word['escara_rimozione_totale'] = '[   ]';

    $arr_word['emorragica_alginati_garze'] = '[   ]';

    $arr_word['essud_necro_fibri_idrogeli'] = '[   ]';
    $arr_word['essud_necro_fibri_schiuma'] = '[   ]';
    $arr_word['essud_necro_fibri_placca_idro'] = '[   ]';
    $arr_word['essud_necro_fibri_fibra_idro_placca_idro'] = '[   ]';

    $arr_word['cavit_tampone_schiuma'] = '[   ]';
    $arr_word['cavit_fibra_idro_garza'] = '[   ]';

    $arr_word['les_gran_schiuma'] = '[   ]';
    $arr_word['les_gran_placca_idro'] = '[   ]';

    $arr_word['les_inf_fibra_idro'] = '[   ]';

    $arr_word['tipo_lesione_principale'] = '[   ]';
    $arr_word['tipo_lesione_secondaria'] = '[   ]';

    $durata_terapia_clicica = false;
    $freq_terapia_found = false;
	$id_istanza_testata = NULL;

    while ($row = mssql_fetch_assoc($rt)) {
		$id_istanza_testata = $row['id_istanza_testata'];
		
        if ($i == 1) {
            $idmoduloversione = $row['id_modulo_versione'];
            $filename_tmp = "tmp_" . $idpaziente . "_" . $random . $row['modello_word'];
            //$filename_pdf = $idmodulopadre."_".$idmoduloversione."_".$idinserimento."_".$row['modello_word'];		
            //$filename = "308_013_07_16_Foglio_Ge_-_SALERNO.rtf";//$row['modello_word'];
            $filename = $row['modello_word'];
            $codicesgq = $row['codice'];
            //echo($relative_path.$filename);
            $allegato1 = $row['allegato'];

            $estensione = substr($filename, -3);

            if ($estensione == 'doc' || $estensione == 'dot') {
                $word = new clsMSWord;
                $word->Open($relative_path . $filename);
                $word->WriteBookmarkText('intestazione', $row['intestazione']);
            } else {
                $fp = fopen($relative_path . $filename, "rt");
                $contenuto = fread($fp, filesize($relative_path . $filename));
                //echo($contenuto);
                $contenuto = rtfReplaceWord('intestazione', $row['intestazione'], $contenuto);
            }

            $i++;
            //echo("prima_");
        }
        $valore = $row['valore'];

        $is_terap_farm = $idmodulopadre == 205 && $idmoduloversione >= 6143;
        $is_somm_farm = $idmodulopadre == 206 && $idmoduloversione >= 6146;

        $idvalore = $row['id_istanza_dettaglio'];

        if (
            $row['tipo'] == 4 &&
            (!$is_somm_farm || ($is_somm_farm && strpos($row['segnalibro'], 'farmaco_') === false))
        ) {
            $idcampo = $row['idcampo'];
            $query3 = "select * from re_moduli_combo_propriety where idcampo=$idcampo";
            $rs3 = mssql_query($query3, $conn);
            if (!$rs3) error_message(mssql_error());
            $row3 = mssql_fetch_assoc($rs3);
            $multi = $row3['multi'];
            mssql_free_result($rs3);

            $query2 = "select * from re_moduli_combo where idcampo=$idcampo ";
            $rs2 = mssql_query($query2, $conn);

            if (!$rs2) error_message(mssql_error());
            if ($multi == 1) {        // combo multipla
                $et = "";
                $valore = split(";", $valore);
                while ($row2 = mssql_fetch_assoc($rs2)) {
                    $valore1 = $row2['valore'];

                    if ($row['segnalibro'] == 'combo_elenco_operatori') {
                        $etichetta = $row2['etichetta'];
                        if (strpos($etichetta, " - #") !== false)
                            $etichetta = str_replace(" - #", " \par #", $etichetta);
                        else $etichetta .= " \par ";
                    } elseif ($row['segnalibro'] == 'moduli_oscuramento_fse') {
                        $etichetta = $row['valore'];
                        $etichetta_word = "";
                        $arr_etichetta = split(";", $etichetta);
                        foreach ($arr_etichetta as $arr_etichetta_singolo) {
                            if (!strpos($arr_etichetta_singolo, "(tutte le compilazioni)") !== false)
                                $etichetta_word .= $arr_etichetta_singolo . " \par ";
                        }

                        if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText('moduli_oscuramento_fse', $etichetta_word);
                        else                                                $contenuto = rtfReplaceWord('moduli_oscuramento_fse', $etichetta_word, $contenuto);

                        //if($_SESSION['UTENTE']->get_userid() == 11) echo "etichetta: " . $etichetta . "<br><br>"; exit();										
                    } else $etichetta = extractComboCode($row2['etichetta']);


                    $separatore = $row2['separatore'];
                    switch ($separatore) {
                        case 0:
                            $sep = " \par ";
                            break;
                        case 1:
                            $sep = " - ";
                            break;
                        case 2:
                            $sep = ", ";
                            break;
                    }
                    if (in_array($valore1, $valore)) $et .= $etichetta . $sep;
                }
                $et = substr($et, 0, strlen($et) - 2);
                $valore = $et;
            } else {
                while ($row2 = mssql_fetch_assoc($rs2)) {
                    if ($row2['valore'] == $valore) $valore = extractComboCode($row2['etichetta']);
                }
            }
        } elseif ($row['tipo'] == 9) {
            $multi_ram = 0;
            $idcampo = $row['idcampo'];
            $query3 = "select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo";
            $rs3 = mssql_query($query3, $conn);
            if (!$rs3) error_message(mssql_error());
            $row3 = mssql_fetch_assoc($rs3);
            $multi_ram = $row3['multi'];
            mssql_free_result($rs3);
            $query2 = "select * from re_moduli_combo_ramificate where idcampo=$idcampo";
            $rs2 = mssql_query($query2, $conn);
            if (!$rs2) error_message(mssql_error());
            if ($multi_ram == 1) {
                $et = "";
                $valore = split(";", $valore);
                while ($row2 = mssql_fetch_assoc($rs2)) {
                    $valore1 = $row2['valore'];
                    $etichetta = extractComboCode($row2['etichetta']);
                    if (in_array($valore1, $valore)) $et .= $etichetta . " - ";
                }
                //$et=substr($et,0,strlen($et)-2);						
                echo ($et);
            } else {
                $valore2 = $valore;
                while ($row2 = mssql_fetch_assoc($rs2)) {

                    $idc = $row2['idcombo'];
                    $idcampocombo = $row2['idcampocombo'];
                    $valore1 = $row2['valore'];
                    $etichetta1 = extractComboCode($row2['etichetta']);
                    $v = (int)($valore2);
                    //modifica COD20160530
                    if ($idcampocombo == $v) {
                        $valore = $etichetta1;
                    } else {
                        $valore = get_rincorri_figlio_per_stampa($idc, $idcampocombo, "", $v);
                    }
                    if ($valore != '')
                        break;
                }
                //$valore1=$row2['etichetta'];
                //echo($valore1);
            }
        } elseif ($row['tipo'] == 7) {
            if (trim($valore) == "") $valore = "Nessun allegato inserito";
        } elseif ($row['tipo'] == 2) {

            $db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
            //$res  = odbc_exec ($db, "SET TEXTSIZE ".(10 * MAX));
            //$res  = odbc_exec ($db, "SET TEXTSIZE ".(10 * MAX));
            //$res = odbc_exec ($db, "SELECT valore FROM istanze_dettaglio WHERE id_istanza_dettaglio=$idvalore");
            //odbc_longreadlen ($res, MAX);
            //$text = odbc_result ($res, "valore");

            $conn         = db_connect();
            $queryGio   = "SELECT valore FROM istanze_dettaglio WHERE id_istanza_dettaglio=$idvalore";
            $rsGio         = mssql_query($queryGio, $conn);
            $rowGio        = mssql_fetch_assoc($rsGio);
            $text         = $rowGio['valore'];

            //$text = str_replace("?","'",pulisci_lettura(odbc_result ($res, "valore")));
            //$text = str_replace("\n","\line",$text);
            $text = str_replace("\n", " \par ", $text);        //lto replace per "a capo" del campo memo in rtf

            //LTO - Se viene inserito un apostrofo particolare font strano, nel db viene riportato come "?"
            $text = str_replace("l?", "l'", $text);
            $text = str_replace("un?", "un'", $text);
            $text = str_replace("d?", "d'", $text);
            $text = str_replace("n?", "n'", $text);
            $text = str_replace("c?", "c'", $text);

            $valore = $text;
            odbc_close($db);
        }
        //echo ($valore."<br>");

        // SBO 01/08/23: estrapolata verifica del segnalibro dall'area sottostante, dedicata alla gestione dei dati, in modo dal
        // 		poter escludere questo modulo dal trim($valore) iniziale. Qesto perché le date non selezionate non passano e 
        // 		vengono di conseguenza stampati i segnalibri invece che i valori vuoti
        $is_temp_print = substr($row['segnalibro'], 0, strlen('temp_data_giorno_')) == 'temp_data_giorno_';
        $is_piaghe_print = $idmodulopadre == 200 || $idmodulopadre == 201;
        $is_piaghe_print = $idmodulopadre == 200 || $idmodulopadre == 201;

        // #### INIZIO #### GESTIONE STAMPA MODULO MONITORAGGIO PIAGHE DA DECUPITO

        if ($is_piaghe_print) {
            $valore = unhtmlentities($valore);

            $arr_word_found = array();

            if ($row['segnalibro'] == 'tipo_lesione') {
                if (trim($valore) == 'Principale') {
                    $arr_word['tipo_lesione_principale'] = '[x]';
                } elseif (trim($valore) == 'Secondaria') {
                    $arr_word['tipo_lesione_secondaria'] = '[x]';
                }
            } elseif ($row['segnalibro'] == 'grado_lesione_principale') {
                $valore = explode('-', $valore);

                $arr_word_found['grado_lesione_I'] = '[   ]';
                $arr_word_found['grado_lesione_II'] = '[   ]';
                $arr_word_found['grado_lesione_III'] = '[   ]';
                $arr_word_found['grado_lesione_IV'] = '[   ]';
                $arr_word_found['grado_lesione_escara'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Primo') {
                        $arr_word_found['grado_lesione_I'] = '[x]';
                        unset($arr_word['grado_lesione_I']);
                    } elseif ($v == 'Secondo') {
                        $arr_word_found['grado_lesione_II'] = '[x]';
                        unset($arr_word['grado_lesione_II']);
                    } elseif ($v == 'Terzo') {
                        $arr_word_found['grado_lesione_III'] = '[x]';
                        unset($arr_word['grado_lesione_III']);
                    } elseif ($v == 'Quarto') {
                        $arr_word_found['grado_lesione_IV'] = '[x]';
                        unset($arr_word['grado_lesione_IV']);
                    } elseif ($v == 'Escara') {
                        $arr_word_found['grado_lesione_escara'] = '[x]';
                        unset($arr_word['grado_lesione_escara']);
                    }
                }
            } else if ($row['segnalibro'] == 'condizioni_lesione_principale') {
                $valore = explode('-', $valore);

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Detersa') {
                        $arr_word_found['condizioni_lesione_detersa'] = '[x]';
                        unset($arr_word['condizioni_lesione_detersa']);
                    } elseif ($v == 'Fibrina') {
                        $arr_word_found['condizioni_lesione_fibrina'] = '[x]';
                        unset($arr_word['condizioni_lesione_fibrina']);
                    } elseif ($v == 'Essudato') {
                        $arr_word_found['condizioni_lesione_essudato'] = '[x]';
                        unset($arr_word['condizioni_lesione_essudato']);
                    } elseif ($v == 'Necrosi') {
                        $arr_word_found['condizioni_lesione_necrosi'] = '[x]';
                        unset($arr_word['condizioni_lesione_necrosi']);
                    } elseif ($v == 'Infetta') {
                        $arr_word_found['condizioni_lesione_infetta'] = '[x]';
                        unset($arr_word['condizioni_lesione_infetta']);
                    }
                }
            } else if ($row['segnalibro'] == 'bordi_lesione_principale') {
                $valore = explode('-', $valore);

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Lineari') {
                        $arr_word_found['bordi_lesione_lineari'] = '[x]';
                        unset($arr_word['bordi_lesione_lineari']);
                    } elseif ($v == 'Macerati') {
                        $arr_word_found['bordi_lesione_macerati'] = '[x]';
                        unset($arr_word['bordi_lesione_macerati']);
                    } elseif ($v == 'Necrotici') {
                        $arr_word_found['bordi_lesione_necrotici'] = '[x]';
                        unset($arr_word['bordi_lesione_necrotici']);
                    } elseif ($v == 'Infetti') {
                        $arr_word_found['bordi_lesione_infetti'] = '[x]';
                        unset($arr_word['bordi_lesione_infetti']);
                    } elseif ($v == 'Frastagliati') {
                        $arr_word_found['bordi_lesione_frastagliati'] = '[x]';
                        unset($arr_word['bordi_lesione_frastagliati']);
                    }
                }
            } else if ($row['segnalibro'] == 'cute_perilesionale') {
                $valore = explode('-', $valore);

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Integra') {
                        $arr_word_found['cute_perilesionale_integra'] = '[x]';
                        unset($arr_word['cute_perilesionale_integra']);
                    } elseif ($v == 'Arrossata') {
                        $arr_word_found['cute_perilesionale_arrossata'] = '[x]';
                        unset($arr_word['cute_perilesionale_arrossata']);
                    } elseif ($v == 'Macerata') {
                        $arr_word_found['cute_perilesionale_macerata'] = '[x]';
                        unset($arr_word['cute_perilesionale_macerata']);
                    }
                }
            } else if ($row['segnalibro'] == 'sintesi_trat_I_II') {
                $valore = explode('-', $valore);

                $arr_word_found['pellicola_semiperm_trasp_poliuretano'] = '[   ]';
                $arr_word_found['idrocolloidi_extra_sottili'] = '[   ]';
                $arr_word_found['schiuma_poliuretano'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if (strpos($v, 'Pellicola semipermeabile trasparente di poliuretano') !== false) {
                        $arr_word_found['pellicola_semiperm_trasp_poliuretano'] = '[x]';
                        unset($arr_word['pellicola_semiperm_trasp_poliuretano']);
                    } elseif (strpos($v, 'Idrocolloidi extra sottili') !== false) {
                        $arr_word_found['idrocolloidi_extra_sottili'] = '[x]';
                        unset($arr_word['idrocolloidi_extra_sottili']);
                    } elseif ($v == 'Schiuma di poliuretano') {
                        $arr_word_found['schiuma_poliuretano'] = '[x]';
                        unset($arr_word['schiuma_poliuretano']);
                    }
                }
            } else if ($row['segnalibro'] == 'sintesi_trat_flittene') {
                $valore = explode('-', $valore);

                $arr_word_found['flittene_forare_senza_rimuovere_tetto'] = '[   ]';
                $arr_word_found['flittene_schiuma_poliuretano'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Forare senza rimuovere il tetto') {
                        $arr_word_found['flittene_forare_senza_rimuovere_tetto'] = '[x]';
                        unset($arr_word['flittene_forare_senza_rimuovere_tetto']);
                    } elseif ($v == 'Schiuma di poliuretano') {
                        $arr_word_found['flittene_schiuma_poliuretano'] = '[x]';
                        unset($arr_word['flittene_schiuma_poliuretano']);
                    }
                }
            } else if ($row['segnalibro'] == 'var_les_escara') {
                $valore = explode('-', $valore);

                $arr_word_found['escara_pomate_garze'] = '[   ]';
                $arr_word_found['escara_idrogeli_schiuma'] = '[   ]';
                $arr_word_found['escara_rimozione_graduale'] = '[   ]';
                $arr_word_found['escara_rimozione_totale'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Pomate enzimatiche + garze') {
                        $arr_word_found['escara_pomate_garze'] = '[x]';
                        unset($arr_word['escara_pomate_garze']);
                    } elseif ($v == 'Idrogeli + schiuma di poliuretano') {
                        $arr_word_found['escara_idrogeli_schiuma'] = '[x]';
                        unset($arr_word['escara_idrogeli_schiuma']);
                    } elseif ($v == 'Graduale') {
                        $arr_word_found['escara_rimozione_graduale'] = '[x]';
                        unset($arr_word['escara_rimozione_graduale']);
                    } elseif ($v == 'Totale') {
                        $arr_word_found['escara_rimozione_totale'] = '[x]';
                        unset($arr_word['escara_rimozione_totale']);
                    }
                }
            } else if ($row['segnalibro'] == 'var_les_emorragica') {
                $valore = explode('-', $valore);

                $arr_word_found['emorragica_alginati_garze'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'alginati + garze sterili') {
                        $arr_word_found['emorragica_alginati_garze'] = '[x]';
                        unset($arr_word['emorragica_alginati_garze']);
                    }
                }
            } else if ($row['segnalibro'] == 'var_les_essudativa_necrotica_fibrinosa') {
                $valore = explode('-', $valore);

                $arr_word_found['essud_necro_fibri_idrogeli'] = '[   ]';
                $arr_word_found['essud_necro_fibri_schiuma'] = '[   ]';
                $arr_word_found['essud_necro_fibri_placca_idro'] = '[   ]';
                $arr_word_found['essud_necro_fibri_fibra_idro_placca_idro'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Idrogeli') {
                        $arr_word_found['essud_necro_fibri_idrogeli'] = '[x]';
                        unset($arr_word['essud_necro_fibri_idrogeli']);
                    } elseif ($v == 'Schiuma di poliuretano') {
                        $arr_word_found['essud_necro_fibri_schiuma'] = '[x]';
                        unset($arr_word['essud_necro_fibri_schiuma']);
                    } elseif ($v == 'Placca idroccoloidale') {
                        $arr_word_found['essud_necro_fibri_placca_idro'] = '[x]';
                        unset($arr_word['essud_necro_fibri_placca_idro']);
                    } elseif ($v == 'Fibra idrocolloidale + placca idrocoll.') {
                        $arr_word_found['essud_necro_fibri_fibra_idro_placca_idro'] = 'x';
                        unset($arr_word['essud_necro_fibri_fibra_idro_placca_idro']);
                    }
                }
            } else if ($row['segnalibro'] == 'var_les_cavitaria_essudato') {
                $valore = explode('-', $valore);

                $arr_word_found['cavit_tampone_schiuma'] = '[   ]';
                $arr_word_found['cavit_fibra_idro_garza'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Tampone in schiuma di poliuretano + schiuma di poliuretano') {
                        $arr_word_found['cavit_tampone_schiuma'] = '[x]';
                        unset($arr_word['cavit_tampone_schiuma']);
                    } elseif ($v == 'Fibra idrocolloidale + garza') {
                        $arr_word_found['cavit_fibra_idro_garza'] = '[x]';
                        unset($arr_word['cavit_fibra_idro_garza']);
                    }
                }
            } else if ($row['segnalibro'] == 'var_les_granuleggiata') {
                $valore = explode('-', $valore);

                $arr_word_found['les_gran_schiuma'] = '[   ]';
                $arr_word_found['les_gran_placca_idro'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Schiuma di poliuretano') {
                        $arr_word_found['les_gran_schiuma'] = '[x]';
                        unset($arr_word['les_gran_schiuma']);
                    } elseif ($v == 'Placca idrocolloidale') {
                        $arr_word_found['les_gran_placca_idro'] = '[x]';
                        unset($arr_word['les_gran_placca_idro']);
                    }
                }
            } else if ($row['segnalibro'] == 'var_les_infetta') {
                $valore = explode('-', $valore);

                $arr_word_found['les_inf_fibra_idro'] = '[   ]';

                foreach ($valore as $v) {
                    $v = trim($v);

                    if ($v == 'Fibra idrocolloidale + garza') {
                        $arr_word_found['les_inf_fibra_idro'] = '[x]';
                        unset($arr_word['les_inf_fibra_idro']);
                    }
                }
            }

            foreach ($arr_word_found as $segnalibro => $value) {
                if ($estensione == 'doc' || $estensione == 'dot')     $word->WriteBookmarkText($segnalibro, $value);
                else                                                $contenuto = rtfReplaceWord($segnalibro, $value, $contenuto);
            }
        }

        // #### FINE #### GESTIONE STAMPA MODULO MONITORAGGIO PIAGHE DA DECUPITO

        if ($is_temp_print || $is_piaghe_print || $is_terap_farm || trim($valore)) {
            $valore = unhtmlentities($valore);

            if (
                $row['segnalibro'] == 'firma_operatore' || $row['segnalibro'] == 'resp_progetto' || $row['segnalibro'] == 'resp_programma'
                || $row['segnalibro'] == 'firma_dirett_sanit_anamn_riab' || $row['segnalibro'] == 'firma_dirett_sanit_aggior_anamn'
                || $row['segnalibro'] == 'firma_dirett_sanitario_terap' || $row['segnalibro'] == 'firma'
                || $row['segnalibro'] == 'firma_specialista' || $row['segnalibro'] == ''
            ) {
                if ($row['segnalibro'] == 'firma_operatore')                    $segnalibro = 'firma_operatore';
                elseif ($row['segnalibro'] == 'resp_progetto')                        $segnalibro = 'resp_progetto';
                elseif ($row['segnalibro'] == 'resp_programma')                    $segnalibro = 'resp_programma';
                elseif ($row['segnalibro'] == 'firma_dirett_sanit_anamn_riab')        $segnalibro = 'firma_dirett_sanit_anamn_riab';
                elseif ($row['segnalibro'] == 'firma_dirett_sanit_aggior_anamn')    $segnalibro = 'firma_dirett_sanit_aggior_anamn';
                elseif ($row['segnalibro'] == 'firma_dirett_sanitario_terap')        $segnalibro = 'firma_dirett_sanitario_terap';
                elseif ($row['segnalibro'] == 'firma_dirett_sanitario_piano_esec')    $segnalibro = 'firma_dirett_sanitario_piano_esec';
                elseif ($row['segnalibro'] == 'firma_specialista')                    $segnalibro = 'firma_specialista';
                elseif ($row['segnalibro'] == 'firma')                                $segnalibro = 'firma';


                if (trim($valore) !== "" && $valore !== NULL) {
                    $query_op = "SELECT nome, ordine_professionale, num_iscrizione FROM operatori WHERE nome = '" . str_replace("'", "''", $valore) . "' AND cancella ='n'";
                    $rs_op = mssql_query($query_op, $conn);
                    if (!$rs_op) error_message(mssql_error());
                    $row_op = mssql_fetch_assoc($rs_op);

                    if ($row_op['nome'] !== NULL) {
                        $nome_operatore = trim($row_op['nome']);
                        $firma_operatore = "Dott./Dott.ssa " . $nome_operatore;
                        $op_ordine_professionale = trim($row_op['ordine_professionale']);
                        $op_num_iscrizione = trim($row_op['num_iscrizione']);

                        if ($op_ordine_professionale !== "" && $op_num_iscrizione !== "") {
                            if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText($segnalibro, unhtmlentities($firma_operatore . "\n #" . $op_num_iscrizione . " | " . $op_ordine_professionale));
                            else                                                $contenuto = rtfReplaceWord($segnalibro, unhtmlentities($firma_operatore . " \par #" . $op_num_iscrizione . " | " . $op_ordine_professionale), $contenuto);
                        } else {
                            if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText($segnalibro, unhtmlentities($firma_operatore));
                            else                                                $contenuto = rtfReplaceWord($segnalibro, unhtmlentities($firma_operatore), $contenuto);
                        }
                        mssql_free_result($rs_op);
                    } else {
                        if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText($segnalibro, unhtmlentities($valore));
                        else                                                $contenuto = rtfReplaceWord($segnalibro, unhtmlentities($valore), $contenuto);
                    }
                } else {
                    if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText($segnalibro, "");
                    else                                                $contenuto = rtfReplaceWord($segnalibro, "", $contenuto);
                }
            } else if ($row['segnalibro'] == 'combo_elenco_operatori') {
                if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText('combo_elenco_operatori', $valore);
                else                                                $contenuto = rtfReplaceWord('combo_elenco_operatori', $valore, $contenuto);
            } else if (
                $row['segnalibro'] == 'art44_valutiniz_esito_val_funz_a' ||
                $row['segnalibro'] == 'art44_valutiniz_esito_val_funz_b' ||
                $row['segnalibro'] == 'art44_valutiniz_esito_val_funz_c' ||
                $row['segnalibro'] == 'art44_valutiniz_esito_val_funz_d' ||
                $row['segnalibro'] == 'art44_valutinter_ajax_esito_val_funz_a' ||
                $row['segnalibro'] == 'art44_valutinter_ajax_esito_val_funz_b' ||
                $row['segnalibro'] == 'art44_valutinter_ajax_esito_val_funz_c' ||
                $row['segnalibro'] == 'art44_valutinter_ajax_esito_val_funz_d' ||
                $row['segnalibro'] == 'art44_valutfin_ajax_esito_val_funz_a' ||
                $row['segnalibro'] == 'art44_valutfin_ajax_esito_val_funz_b' ||
                $row['segnalibro'] == 'art44_valutfin_ajax_esito_val_funz_c' ||
                $row['segnalibro'] == 'art44_valutfin_ajax_esito_val_funz_d'
            ) {
                if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText($row['segnalibro'], $valore . utf8_decode("°"));
                else                                                $contenuto = rtfReplaceWord($row['segnalibro'], $valore . utf8_decode("°"), $contenuto);
            } else if ($row['segnalibro'] == 'tempi_verifica') {
                $vecchio_automatismo_tempi_verifica = 0;    // non mi serve il vecchio automatismo in quanto ho trovato il campo, quindi sono in una versione nuova del modulo con campo editabile
                if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText('tempi_verifica', $valore);
                else                                                $contenuto = rtfReplaceWord('tempi_verifica', $valore, $contenuto);
            } else if ($row['segnalibro'] == 'numero_sede_lesione') {
                if ($estensione == 'doc' || $estensione == 'dot')     $word->WriteBookmarkText('n_sede', trim($valore));
                else                                                $contenuto = rtfReplaceWord('n_sede', trim($valore), $contenuto);
            } else if ($is_temp_print) {
                $days = array(
                    "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX",
                    "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX",
                    "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX",
                    "XXX", "XXXI"
                );
                $_segnalibro = substr($row['segnalibro'], strlen('temp_data_giorno_'));
                $day = 0;

                for ($d = 31; $d >= 1; $d--) {
                    if ($days[$d - 1] === $_segnalibro) {
                        $day = $d;
                    }
                }

                $temps = array(
                    "35.9",
                    "36.0", "36.1", "36.2", "36.3", "36.4", "36.5", "36.6", "36.7", "36.8", "36.9",
                    "37.0", "37.1", "37.2", "37.3", "37.4", "37.5", "37.6", "37.7", "37.8", "37.9",
                    "38.0", "38.1", "38.2", "38.3", "38.4", "38.5", "38.6", "38.7", "38.8", "38.9",
                    "39.0", "39.1", "39.2", "39.3", "39.4", "39.5", "39.6"
                );

                foreach ($temps as $temp) {
                    $value = '';

                    if (trim($valore) == $temp) {
                        $value = 'X';
                    }

                    if ($estensione == 'doc' || $estensione == 'dot') {
                        $word->WriteBookmarkText(str_replace('.', '', $temp) . "_$day", $value);
                    } else {
                        $contenuto = rtfReplaceWord(str_replace('.', '', $temp) . "_$day", $value, $contenuto);
                    }
                }
            } elseif (($is_terap_farm && !empty($valore) && strpos($row['segnalibro'], 'orario_') !== false)
                || ($is_somm_farm && !empty($valore) && strpos($row['segnalibro'], 'tm_') !== false)
            ) {
                if (strpos($valore, ';') !== false) {
                    $_query = "SELECT valore, etichetta
								FROM campi_combo
								WHERE idcombo = 257 AND stato = 1 AND cancella = 'n'
									AND valore in (" . str_replace(';', ',', $valore) . ")";
                    $_rs = mssql_query($_query, $conn);

                    $orario = '';
                    while ($_row = mssql_fetch_assoc($_rs)) {
                        $orario .= $_row['etichetta'] . '        ';
                    }
                } else {
                    $orario = $valore;
                }

                if ($estensione == 'doc' || $estensione == 'dot') {
                    $word->WriteBookmarkText($row['segnalibro'], $orario);
                } else {
                    $contenuto = rtfReplaceWord($row['segnalibro'], $orario, $contenuto);
                }
            } elseif ($is_somm_farm && !empty($valore) && strpos($row['segnalibro'], 'farmaco_') !== false) {
                $_query = "SELECT nome, lotto
							FROM farmaci
							WHERE id = $valore";
                $_rs = mssql_query($_query, $conn);
                $_row = mssql_fetch_assoc($_rs);

                $nome_farmaco = $_row['nome'] . ' - Lot. ' . $_row['lotto'];

                if ($estensione == 'doc' || $estensione == 'dot') {
                    $word->WriteBookmarkText($row['segnalibro'], $nome_farmaco);
                } else {
                    $contenuto = rtfReplaceWord($row['segnalibro'], $nome_farmaco, $contenuto);
                }
            } elseif (strpos($row['segnalibro'], 'frequenza_terapia_') !== false) {
                $freq_terapia_found    = true;

                if ($durata_terapia_clicica) {
                    if ($estensione == 'doc' || $estensione == 'dot') {
                        $word->WriteBookmarkText($row['segnalibro'], "($valore gg)");
                    } else {
                        $contenuto = rtfReplaceWord($row['segnalibro'], "($valore gg)", $contenuto);
                    }
                } else {

                    if ($estensione == 'doc' || $estensione == 'dot') {
                        $word->WriteBookmarkText($row['segnalibro'], "");
                    } else {
                        $contenuto = rtfReplaceWord($row['segnalibro'], "", $contenuto);
                    }
                }
            } else if ($row['segnalibro'] == 'operatore_in_qualita_di') {
                $_val_ds = '';
                $_val_med_del = '';

                if ($valore == 1 || $valore == 'Direttore Sanitario') {
                    $_val_ds = 'X';
                    $_val_med_del = '';
                } elseif ($valore == 2 || $valore == 'Medico di turno delegato alla funzione') {
                    $_val_ds = '';
                    $_val_med_del = 'X';
                }

                if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText('operatore_in_qualita_di_ds', $_val_ds);
                else                                                $contenuto = rtfReplaceWord('operatore_in_qualita_di_ds', $_val_ds, $contenuto);

                if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText('operatore_in_qualita_di_med_del', $_val_med_del);
                else                                                $contenuto = rtfReplaceWord('operatore_in_qualita_di_med_del', $_val_med_del, $contenuto);
            } else if ($row['segnalibro'] == 'paziente_statocivile') {
                if (strpos($valore, ';') !== false) {
                    $vals = explode(';', $valore);
                } else {
                    $vals = array();

                    switch ($valore) {
                        case 'Coniugato/a':
                            $vals[] = 1;
                            break;

                        case 'Ex-Coniuge':
                            $vals[] = 2;
                            break;

                        case 'Vedovo/a':
                            $vals[] = 3;
                            break;

                        case 'Divorziato/a':
                            $vals[] = 4;
                            break;

                        case 'Nubile/Celibe':
                            $vals[] = 5;
                            break;
                    }
                }

                for ($i = 1; $i <= 5; $i++) {
                    $val = '';
                    if (in_array($i, $vals)) {
                        $val = 'X';
                    }

                    if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText("paziente_statocivile_$i", $val);
                    else                                                $contenuto = rtfReplaceWord("paziente_statocivile_$i", $val, $contenuto);
                }
            } else if (strpos($row['segnalibro'], 'chk_op_') !== false && $row['tipo'] == 4) {
                $_tmp_ar = explode('_', $row['segnalibro']);
                $idx = array_pop($_tmp_ar);

                if (strpos($valore, ';') !== false) {
                    if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText("chk_op_" . $idx . "_1", '[X]');
                    else                                                $contenuto = rtfReplaceWord("chk_op_" . $idx . "_1", '[X]', $contenuto);

                    if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText("chk_op_" . $idx . "_2", '[X]');
                    else                                                $contenuto = rtfReplaceWord("chk_op_" . $idx . "_2", '[X]', $contenuto);
                } else {
                    $check_1 = '[   ]';
                    $check_2 = '[   ]';

                    switch ($valore) {
                        case 'Check Operatore 1':
                            $check_1 = '[X]';
                            break;

                        case 'Check Operatore 2':
                            $check_2 = '[X]';
                            break;
                    }

                    if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText("chk_op_" . $idx . "_1", $check_1);
                    else                                                $contenuto = rtfReplaceWord("chk_op_" . $idx . "_1", $check_1, $contenuto);

                    if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText("chk_op_" . $idx . "_2", $check_2);
                    else                                                $contenuto = rtfReplaceWord("chk_op_" . $idx . "_2", $check_2, $contenuto);
                }
            } else {
                if (strpos($row['segnalibro'], 'durata_terapia_') !== false) {
                    if ($valore == 'Ciclica' || $valore == 'Ciclica con sospensione') {
                        $durata_terapia_clicica = true;
                    } else {
                        $durata_terapia_clicica = false;
                    }
                }

                if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText($row['segnalibro'], $valore);
                else                                                $contenuto = rtfReplaceWord($row['segnalibro'], $valore, $contenuto);
            }

            array_push($arr_segnalibri, $row['segnalibro']);
        }

        if (empty($idpaziente) or (!$idpaziente)) $idpaziente = $row['idpaziente'];

        $idnormativa = $row['idnormativa'];
        if ($idnormativa == 2)
            $regime_ex_art_44 = true;
        else $regime_ex_art_44 = false;
    }
    mssql_free_result($rt);

    if (!$freq_terapia_found) {
        for ($i = 1; $i < 26; $i++) {
            if ($estensione == 'doc' || $estensione == 'dot') {
                $word->WriteBookmarkText("frequenza_terapia_" . $i, "");
            } else {
                $contenuto = rtfReplaceWord("frequenza_terapia_" . $i, "", $contenuto);
            }
        }
    }

    if ($is_piaghe_print) {
        foreach ($arr_word as $segnalibro => $value) {
            if ($estensione == 'doc' || $estensione == 'dot')     $word->WriteBookmarkText($segnalibro, $value);
            else                                                $contenuto = rtfReplaceWord($segnalibro, $value, $contenuto);
        }
    }
	
	// Modulo emotrasfusioni
	if ($idmodulopadre == 269) {
		$_query  = "SELECT stato_firma_allegato, data_firma_allegato, 
							stato_firma_allegato2, data_firma_allegato2, 
							stato_firma_allegato3, data_firma_allegato3, 
							stato_firma_allegato4, data_firma_allegato4
						FROM istanze_testata_firme
						WHERE id_istanza_testata = $id_istanza_testata
						ORDER BY id ASC";
		$_result = mssql_query($_query, $conn);
		$count_firma_a1 = 0;
		$found_1_1 = $found_1_2 = $found_2 = $found_3 = false;
		
		while ($row = mssql_fetch_assoc($_result)) {
			if (!empty($row['stato_firma_allegato'])) {
				if ($count_firma_a1 == 0) {
					if ($row['stato_firma_allegato'] == 's') {
						$dt_sig = date('d/m/Y', strtotime($row['data_firma_allegato']));
						$tm_sig = date('H:i', strtotime($row['data_firma_allegato']));
						$contenuto = rtfReplaceWord('firma_ctrl_par_vit_1', "| Firmato digitalmente il $dt_sig alle $tm_sig", $contenuto);
						$contenuto = rtfReplaceWord('firma_ctrl_par_vit_1_1', "| Firmato digitalmente il $dt_sig alle $tm_sig", $contenuto);						
						$count_firma_a1++;
						$found_1_1 = true;
					}
					continue;
				} else {
					if ($row['stato_firma_allegato'] == 's') {
						$dt_sig = date('d/m/Y', strtotime($row['data_firma_allegato']));
						$tm_sig = date('H:i', strtotime($row['data_firma_allegato']));
						$contenuto = rtfReplaceWord('firma_ctrl_par_vit_2', "| Firmato digitalmente il $dt_sig alle $tm_sig", $contenuto);
						$count_firma_a1++;
						$found_1_2 = true;
					}
					continue;
				}
			} else {
				$contenuto = rtfReplaceWord('firma_ctrl_par_vit_1', "", $contenuto);
				$contenuto = rtfReplaceWord('firma_ctrl_par_vit_2', "", $contenuto);
			}

			if (!empty($row['stato_firma_allegato2'])) {
				if ($row['stato_firma_allegato2'] == 's') {
					$dt_sig = date('d/m/Y', strtotime($row['data_firma_allegato2']));
					$tm_sig = date('H:i', strtotime($row['data_firma_allegato2']));
					$contenuto = rtfReplaceWord('firma_seg_vit_15', "| Firmato digitalmente il $dt_sig alle $tm_sig", $contenuto);
					$found_2 = true;
				}
				continue;
			}

			if (!empty($row['stato_firma_allegato3'])) {
				if ($row['stato_firma_allegato3'] == 's') {
					$dt_sig = date('d/m/Y', strtotime($row['data_firma_allegato3']));
					$tm_sig = date('H:i', strtotime($row['data_firma_allegato3']));
					$contenuto = rtfReplaceWord('firma_seg_vit_term', "| Firmato digitalmente il $dt_sig alle $tm_sig", $contenuto);
					$found_3 = true;
				}
				break;
			}
		}
		
		if(!$found_1_1) {
			$contenuto = rtfReplaceWord('firma_ctrl_par_vit_1', "", $contenuto);
			$contenuto = rtfReplaceWord('firma_ctrl_par_vit_1_1', "", $contenuto);
		}
		if(!$found_1_2)
			$contenuto = rtfReplaceWord('firma_ctrl_par_vit_2', "", $contenuto);
		if(!$found_2)
			$contenuto = rtfReplaceWord('firma_seg_vit_15', "", $contenuto);
		if(!$found_3)
			$contenuto = rtfReplaceWord('firma_seg_vit_term', "", $contenuto);
	}

    //RECUPERO SIGLA PROVINCIA
    $queryProv  = "SELECT c.sigla  FROM utenti_residenze as ur 
					LEFT JOIN comuni as c on c.id_comune=ur.comune_id
					where ur.idUtente=$idpaziente";
    $resultProv = mssql_query($queryProv, $conn);
    if ($rowProv = mssql_fetch_assoc($resultProv)) {
        if ($rowProv['sigla'] !== NULL)
            $sigla_prov = $rowProv['sigla'];
    } else $sigla_prov = "";


    //segnalibri null
    $query = "SELECT etichetta, segnalibro FROM campi WHERE (idmoduloversione = $idmoduloversione) AND (status = 1) AND (cancella = 'n')";
    $result = mssql_query($query, $conn);
    while ($row2 = mssql_fetch_assoc($result)) {
        $segnalibro = $row2['segnalibro'];

        if (!(in_array($segnalibro, $arr_segnalibri))) {
            if ($estensione == 'doc' || $estensione == 'dot') {
                $word->WriteBookmarkText($row2['segnalibro'], " ");
            } else {
                $contenuto = rtfReplaceWord($segnalibro, " ", $contenuto);
            }
        }
    }
    mssql_free_result($result);
    /*LTO - FMA - Check data odierna per recuperare impegnative in corso checkdataodierna = '1' and*/
    /* SBO 15/03/24: aggiunto idregime nel where in quanto, senza, venivano recuperate informazioni di impegnative con regime differente rispetto il modulo in stampa */
    $querydataodierna = "select checkdataodierna from re_dati_utenti_stampa where (idutente=$idpaziente) AND (idregime = $idregime) ORDER BY checkdataodierna DESC "; //versione DESC, max_data DESC, id_residenza DESC";
    $resultdataodierna = mssql_query($querydataodierna, $conn);

    if (!$resultdataodierna) error_message(mysql_error());
    if ($row3 = mssql_fetch_assoc($resultdataodierna)) {
        if (trim($row3['checkdataodierna']) == "1") {                // per paziente
            $checkdataodierna = " checkdataodierna = '1' and ";
        } else {
            $checkdataodierna = " 1=1 and ";
        }
    }

    // se stampo un moduli del regime ex art. 44, elenco tutti i pacchetti di tutte le ricette attive
    //die('regime_ex_art_44 = ' .$regime_ex_art_44);
    if ($regime_ex_art_44) {
        $query = "select * from re_dati_utenti_stampa_fkt where " . $checkdataodierna . " (idutente=$idpaziente) AND id_cartella = $idcartella AND DataFineTrattamento > getdate() ORDER BY versione DESC, max_data DESC, id_residenza DESC";
        //die($query);
        $result = mssql_query($query, $conn);
        if (!$result) error_message(mysql_error());

        $elenco_pacchetti_paziente = "";
		$profilo_1 = '';
		$profilo_2 = '';
        while ($row11 = mssql_fetch_assoc($result)) {
            /*var_dump($row11);
			die();*/
            $pacchetto_1 = trim($row11['codice_catalogo_1']);
            $pacchetto_2 = trim($row11['codice_catalogo_2']);
            $max_data      = $row11['max_data'];

            if ($pacchetto_1 !== "" && $pacchetto_2 !== "")    $elenco_pacchetti_paziente .= $pacchetto_1 . " - " . $pacchetto_2 . " \par ";
            elseif ($pacchetto_1 !== "" && $pacchetto_2  == "")    $elenco_pacchetti_paziente .= $pacchetto_1 . " \par ";
            elseif ($pacchetto_1  == "" && $pacchetto_2 !== "")    $elenco_pacchetti_paziente .= $pacchetto_2 . " \par ";

            $profilo_1 = $row11['profilo_1'];
			$profilo_2 = $row11['profilo_2'];
        }
		
		$contenuto = rtfReplaceWord('paziente_profilo_1', $row11['profilo_1'], $contenuto);
        $contenuto = rtfReplaceWord('paziente_profilo_2', $row11['profilo_2'], $contenuto);
		
        $contenuto = rtfReplaceWord('paziente_pacchetti', $elenco_pacchetti_paziente, $contenuto);
        $contenuto = rtfReplaceWord('data_prescrizione', formatta_data($max_data), $contenuto);
    }

    $regimi_infiermieritici = array(52, 53, 59, 60, 61, 62, 64, 65);

    if (!empty($idregime) && !in_array($idregime, $regimi_infiermieritici)) {
        $query    = "select TOP(1) * from re_dati_utenti_stampa where " . $checkdataodierna . " (idutente=$idpaziente) AND id_cartella = $idcartella  ORDER BY versione DESC, max_data DESC, id_residenza DESC";
        //die($query);
        $result = mssql_query($query, $conn);
        if (!$result) error_message(mysql_error());
        if (!$row1 = mssql_fetch_assoc($result))    die("Si e' verificato un inaspettato errore. Vi preghiamo di aprire un ticket di assistenza, grazie.");
    } else {

        $query    = "select TOP(1) * from re_dati_utenti_stampa where " . $checkdataodierna . " (idutente=$idpaziente) ORDER BY versione DESC, max_data DESC, id_residenza DESC";
        //die($query);
        $result = mssql_query($query, $conn);
        if (!$result) error_message(mysql_error());
        if (!$row1 = mssql_fetch_assoc($result))    die("Si è verificato un errore inaspettato. Vi preghiamo di aprire un ticket di assistenza, grazie.");

        $query_cc_vers = "select codice_cartella, versione FROM utenti_cartelle WHERE id = $idcartella";
        $result_cc_vers = mssql_query($query_cc_vers, $conn);
        if (!$result_cc_vers) error_message(mysql_error());
        if (!$row_cc_vers = mssql_fetch_assoc($result_cc_vers)) {
            die("Si e' verificato un errore inaspettato. Vi preghiamo di aprire un ticket di assistenza, grazie.");
        } else {
            $row1['paziente_cartella'] = $row_cc_vers['paziente_cartella'];
            $row1['versione']            = $row_cc_vers['versione'];
        }
    }



    //die($row1['Nome']." ".$row1['Cognome']." ".$row1['data_nascita'].' -----  '.formatta_data($row1['data_nascita']));

    if (trim($row1['Sesso']) == "1") {                // per paziente e var per tutore (cerca la var piu in basso)
        $sesso = "M";
        $nato = "nato";
        $sottoscritto = "Il sottoscritto";
        $paziente_nato_lui_o_parente = "nato";
    } else {
        $sesso = "F";
        $nato = "nata";
        $sottoscritto = "La sottoscritta";
        $paziente_nato_lui_o_parente = "nata";
    }

    // RECUPERO IL TUTORE PRINCIPALE DEL PAZIENTE
    $firma_tutore1 = $nome_cognome_tutore01 = $relazione_tutore01 = $comune_nascita_tutore01 = $datanascita_tutore01 = $comune_residenza_tutore01 = $indirizzo_tutore01 = "";
    $firma_tutore2 = $nome_cognome_tutore02 = $relazione_tutore02 = $comune_nascita_tutore02 = $datanascita_tutore02 = $comune_residenza_tutore02 = $indirizzo_tutore02 = "";
    $flag_delegato01 = "[  ] ";
    $nome_cognome_delegato01 = $recapiti_delegato01 = "";
    $firma_tutore_princ = "";
    $nome_parente = $cognome_parente = $data_nascita_parente = $comune_nascita_parente = $provincia_nascita_parente = $comune_residenza_parente = $provincia_residenza_parente = $indirizzo_parente = $codfisc_parente = $tel_parente = $relazione_parente = $sesso_parente = $nato_parente = $num_doc_id = "";

    $queryTutori = "SELECT * FROM re_utenti_tutor WHERE idutente = $idpaziente order by tutoreprincipale DESC, tutore1 DESC, tutore2 DESC";
    $resultTutori = mssql_query($queryTutori, $conn);

    //non ci sono tutori, per cui il paziente deve essere maggiorenne
    $paziente_firma_sua_o_tutore = $row1['Cognome'] . " " . $row1['Nome'];
    $paziente_comune_nascita_sua_o_tutore = $row1['comune_nascita'];
    $paziente_data_nascita_sua_o_tutore = formatta_data($row1['data_nascita']);
    $paziente_cod_fisc_suo_o_tutore = $row1['codice_fiscale'];
    $flag_in_qualita_di_tutore = '';
    $flag_in_qualita_di_paziente = 'x';

    $paziente_indirizzo_suo_o_tutore = $row1['indirizzo'];
    $paziente_telefono_suo_o_tutore = trim($row1['telefono']);
    $paziente_comune_residenza_suo_o_tutore = $row1['comune_residenza'];

    if (mssql_num_rows($resultTutori) > 0)    // se c'è anche solo un tutore, il paziente deve essere minorenne
    {
        while ($rowTutori = mssql_fetch_assoc($resultTutori)) {
            $tutore_attivo = false;

            if ($rowTutori['delegato1'] == -1) {
                $tutore_attivo = true;
                $flag_delegato01 = "[x] ";
                $nome_cognome_delegato01 = pulisci_lettura($row['nome']) . " " . pulisci_lettura($row['cognome']);
                $recapiti_delegato01 = $row['telefono'] . " " . $row['cellulare'];
                $flag_in_qualita_di_tutore = 'x';
                $flag_in_qualita_di_paziente = '';
            }

            if ($rowTutori['tutore1'] == -1) {
                $tutore_attivo = true;
                $paziente_firma_sua_o_tutore = $rowTutori['cognome'] . " " . $rowTutori['nome'];
                $nome_cognome_tutore01 = pulisci_lettura($rowTutori['nome']) . " " . pulisci_lettura($rowTutori['cognome']);
                $relazione_tutore01 = pulisci_lettura($rowTutori['relazione_paziente']);
                $comune_nascita_tutore01 = pulisci_lettura($rowTutori['nome_comune_n']) . " " . pulisci_lettura($rowTutori['sigla_n']);
                $datanascita_tutore01 = formatta_data($rowTutori['data_nascita']);
                $comune_residenza_tutore01 = pulisci_lettura($rowTutori['comune_r']) . " " . pulisci_lettura($rowTutori['sigla_r']);
                $indirizzo_tutore01 = pulisci_lettura($rowTutori['indirizzo']);
                $flag_in_qualita_di_tutore = 'x';
                $flag_in_qualita_di_paziente = '';
            }

            if ($rowTutori['tutore2'] == -1) {
                $tutore_attivo = true;
                $firma_tutore2 = $rowTutori['nome'] . " " . $rowTutori['cognome'];
                $nome_cognome_tutore02 = pulisci_lettura($rowTutori['nome']) . " " . pulisci_lettura($rowTutori['cognome']);
                $relazione_tutore02 = pulisci_lettura($rowTutori['relazione_paziente']);
                $comune_nascita_tutore02 = pulisci_lettura($rowTutori['nome_comune_n']) . " " . pulisci_lettura($rowTutori['sigla_n']);
                $datanascita_tutore02 = formatta_data($rowTutori['data_nascita']);
                $comune_residenza_tutore02 = pulisci_lettura($rowTutori['comune_r']) . " " . pulisci_lettura($rowTutori['sigla_r']);
                $indirizzo_tutore02 = pulisci_lettura($rowTutori['indirizzo']);
                $flag_in_qualita_di_tutore = 'x';
                $flag_in_qualita_di_paziente = '';
            }

            if ($rowTutori['tutoreprincipale'] == -1) {
                $tutore_attivo = true;
                $firma_tutore_princ = $rowTutori['nome'] . " " . $rowTutori['cognome'];
                $flag_in_qualita_di_tutore = 'x';
                $flag_in_qualita_di_paziente = '';
            }

            // invertire priorita?

            $nome_parente = $rowTutori['nome'];
            $cognome_parente = $rowTutori['cognome'];
            $data_nascita_parente = formatta_data($rowTutori['data_nascita']);
            $comune_nascita_parente = $rowTutori['nome_comune_n'];
            $provincia_nascita_parente = $rowTutori['nome_prov_n'];
            $comune_residenza_parente = $rowTutori['comune_r'];
            $provincia_residenza_parente = $rowTutori['sigla_r'];
            $indirizzo_parente = $rowTutori['indirizzo'];
            $codfisc_parente = $rowTutori['codice_fiscale'];
            $tel_parente = trim($rowTutori['telefono']) . " " . trim($rowTutori['cellulare']);
            $relazione_parente = $rowTutori['relazione_paziente'];
            $num_doc_id = $rowTutori['num_doc_id'];

            if ($tutore_attivo) {
                if (trim($rowTutori['sesso']) == "1") {
                    $sesso_parente = "M";
                    $nato_parente = "nato";
                    $paziente_nato_lui_o_parente = "nato";
                } else {
                    $sesso_parente = "F";
                    $nato_parente = "nata";
                    $paziente_nato_lui_o_parente = "nata";
                }

                $paziente_firma_sua_o_tutore = $rowTutori['cognome'] . " " . $rowTutori['nome'];
                $paziente_comune_nascita_sua_o_tutore = $rowTutori['nome_comune_n'];
                $paziente_data_nascita_sua_o_tutore = formatta_data($rowTutori['data_nascita']);
                $paziente_cod_fisc_suo_o_tutore = $rowTutori['codice_fiscale'];
                $paziente_indirizzo_suo_o_tutore = $rowTutori['indirizzo'];
                $paziente_telefono_suo_o_tutore = trim($rowTutori['telefono']);
                $telefono_tutore = trim($rowTutori['telefono']);
                $paziente_comune_residenza_suo_o_tutore = $rowTutori['comune_r'];
            }
        }
    }
    mssql_free_result($resultTutori);




    // RECUPERO GLI OPERATORI ASSOCIATI AL PAZIENTE (NEL PLANNING)
    $op_planning = "";
    if ($row1['num_ricetta'] !== NULL) {
        $queryOpPlanning = "SELECT op.nome AS nominativo FROM ricettacodice_planning AS ric_p
							JOIN ricettecodici As ric ON ric_p.idricettacodice = ric.idricettacodice
							JOIN operatori AS op ON op.uid = ric_p.idoperatore
							WHERE ric.idricetta =  " . $row1['num_ricetta'] . "
							GROUP BY op.nome";
        $resultOpPlanning = mssql_query($queryOpPlanning, $conn);
        while ($rowOpPlanning = mssql_fetch_assoc($resultOpPlanning)) {
            if ($rowOpPlanning['nominativo'] !== NULL)
                $op_planning .= $rowOpPlanning['nominativo'] . " - ";
        }
        $op_planning = substr($op_planning, 0, -3);
        mssql_free_result($resultOpPlanning);
    }

    // RECUPERO I TRATTAMENTI DEL PAZIENTE
    $trattamenti_per_ricetta = $trattamenti_frequenza = $periodo_frequenza = "";
    $frequenza_totale = 0;
    $flag_fisioterapista = $flag_psicomotricista = $flag_logopedista = $flag_terapista_occ = $flag_psicoterapeuta = $flag_neuropsicomotricista = "";

    if ($row1['num_ricetta'] !== NULL) {
        $queryTrattRic = "SELECT trattamento, frequenza, periodo FROM remed.dbo.re_tipologia_freq_per_impegnativa WHERE id_ricetta = " . $row1['num_ricetta'] . "order by periodo desc";
        //$queryTrattRic = "SELECT TOP(1) trattamento, frequenza, periodo FROM remed.dbo.re_tipologia_freq_per_impegnativa";
        $resultTrattRic = mssql_query($queryTrattRic, $conn);
        $num_rows = mssql_num_rows($resultTrattRic);
        $indice = 0;
        while ($rowTrattRic = mssql_fetch_assoc($resultTrattRic)) {
            $indice++;

            if($periodo_frequenza == "") {     // primissima assegnazione
                $periodo_frequenza = $rowTrattRic['periodo'];
            }

            // finche' il periodo e' uguale, ad esempio "/7", sommo le frequenze totali
            if($periodo_frequenza == $rowTrattRic['periodo']) {
                $trattamenti_frequenza .= $rowTrattRic['trattamento'] . " (freq. gg: " . $rowTrattRic['frequenza'] . $rowTrattRic['periodo'] . ")";
                $frequenza_totale += $rowTrattRic['frequenza'];
            }
            else        // se cambia il periodo, ad esempio "/15", scrivo quanto calcolato, azzero la somma delle frequenze totali e assegno il nuovo periodo
            {
                $trattamenti_frequenza .= " \par - Frequenze totali: " . $frequenza_totale . $periodo_frequenza;        // contiene i valori precedentemente calcolati

                $trattamenti_frequenza .= " \par \par " . $rowTrattRic['trattamento'] . " (freq. gg: " . $rowTrattRic['frequenza'] . $rowTrattRic['periodo'] . ")";
                $frequenza_totale = $rowTrattRic['frequenza'];
                $periodo_frequenza = $rowTrattRic['periodo'];
                
                if($indice == $num_rows) {      // se sto leggendo l'ultimo record e questo non ha un periodo diverso
                    $trattamenti_frequenza .= " \par - Frequenze totali: " . $frequenza_totale . $periodo_frequenza;
                }
            }

            switch (utf8_encode($rowTrattRic['trattamento'])) {
                case 'Neuromotoria':
                case 'Fisioterapia':
                case 'Riab.Respiratoria':
                    $flag_fisioterapista = 'x';
                    break;
                case 'Psicomotricità':
                    $flag_psicomotricista = 'x';
                    break;
                case 'Logopedia':
                    $flag_logopedista = 'x';
                    break;
                case 'Psicoterapia':
                case 'Psicoterapia familiare':
                    $flag_psicoterapeuta = 'x';
                    break;
                case 'CDI':    // Centro Diurno - Legge8
                case 'T.Occupazionale':
                case 'Terap. Occupazionale':
                case 'Terapia occupazionale':
                    $flag_terapista_occ = 'x';
                    break;
                case "Neuropsicomotricita'":
                    $flag_neuropsicomotricista = 'x';
                    break;
            }
            //echo $rowTrattRic['trattamento']."<br>";
        }
        
        //$trattamenti_frequenza = substr($trattamenti_frequenza, 0, -6);
        mssql_free_result($resultTrattRic);
    }


    // mi serve per gestire il segnalibro "tempi_verifica" popolato con un query per le vecchie versoine dei programmi riabilitativi. se trovo il segnalibro, è una vers. del modulo nuovo con il campo editabile e non mi serve la vecchia automazione
    if ($vecchio_automatismo_tempi_verifica == 1) {
        // CALCOLO I TEMPI DI VERIFICA RICHIESTI DAI MODULI "PROGRAMMA RIABILITATIVO ...."		
        $tempi_verifica = "";
        if ($row1['durata'] !== NULL && $row1['DataInizioTrattamento'] !== NULL && $row1['DataFineTrattamento'] !== NULL) {
            if ($row1['durata'] >= 180)
                $tempi_verifica = date('d-m-Y', strtotime($row1['DataInizioTrattamento'] . ' + 90 days'));
            else $tempi_verifica = date('d-m-Y', strtotime($row1['DataFineTrattamento']   . ' - 30 days'));

            if ($estensione == 'doc' || $estensione == 'dot')    $word->WriteBookmarkText('tempi_verifica', $tempi_verifica);
            else                                                $contenuto = rtfReplaceWord('tempi_verifica', $tempi_verifica, $contenuto);
        }
        $tempi_verifica = $row1['durata'];
    }



    if ($row1['profilo_1'] == 'PR32') {
        $flag_tipo_operatore_logopedista = 'x';
        $flag_tipo_operatore_terapista_riabilitazione = '';
    } else {
        $flag_tipo_operatore_logopedista = '';
        $flag_tipo_operatore_terapista_riabilitazione = 'x';
    }


    // se l'allegato1 è stato caricato, in fase di stampa del modulo modifico il progressivo 
    // (se posso stampare vuol dire che è previsto il secondo allegato, per cui si suppone che stia stampando l'istanza per firmare e caricare il secondo allegato, motivo della modifica del progressivo)
    if ($allegato1 !== NULL)
        $idinserimento = $idinserimento . "b";

    if ($estensione == 'doc' || $estensione == 'dot') {

        $word->WriteBookmarkText('data_ricovero', unhtmlentities($row1['ricovero_data']));
        $word->WriteBookmarkText('stanza_ricovero', unhtmlentities($row1['ricovero_stanza']));
        $word->WriteBookmarkText('letto_ricovero', unhtmlentities($row1['ricovero_letto']));
        $word->WriteBookmarkText('provenienza_ricovero', unhtmlentities($row1['ricovero_provenienza']));

        $word->WriteBookmarkText('paziente_nome', unhtmlentities($row1['Nome']));
        $word->WriteBookmarkText('paziente_cognome', unhtmlentities($row1['Cognome']));
        $word->WriteBookmarkText('paziente_datanascita', formatta_data($row1['data_nascita']));

        $word->WriteBookmarkText('paziente_comune_nascita', unhtmlentities($row1['comune_nascita']));
        $word->WriteBookmarkText('paziente_comune_residenza', unhtmlentities($row1['comune_residenza']));
        $word->WriteBookmarkText('paziente_cap', unhtmlentities($row1['cap']));
        $word->WriteBookmarkText('paziente_indirizzo', unhtmlentities($row1['indirizzo']));
        $word->WriteBookmarkText('paziente_telefono', unhtmlentities($row1['telefono']));
        $word->WriteBookmarkText('paziente_cellulare', unhtmlentities($row1['cellulare']));
        //Woord->WriteBookmarkText('medico_prescrittore',unhtmlentities($row1['prescrittore']));			// non funziona, sostituito dalla riga seguente
        $word->WriteBookmarkText('paziente_medico_prescrittore', unhtmlentities($row1['NominativoPrescrittore']));
        $word->WriteBookmarkText('paziente_icd9', unhtmlentities($row1['icd9']));
        $word->WriteBookmarkText('paziente_icd10', unhtmlentities($row1['icd10']));

        $word->WriteBookmarkText('paziente_sesso', $sesso);
        $word->WriteBookmarkText('paziente_cartella', $row1['codice_cartella'] . "/" . convert_versione($row1['versione']));
        //$word->WriteBookmarkText('paziente_cartella',$row1['codice_cartella']);
        $word->WriteBookmarkText('paziente_regime', unhtmlentities($row1['regime']));
        $word->WriteBookmarkText('paziente_normativa', unhtmlentities($row1['normativa']));
        $word->WriteBookmarkText('paziente_diagnosi', unhtmlentities($row1['Diagnosi']));
        $word->WriteBookmarkText('paziente_menomazione', unhtmlentities($row1['menomazione']));
        $word->WriteBookmarkText('paziente_cod_menomazione', unhtmlentities($row1['cod_menomazione']));
        $word->WriteBookmarkText('paziente_trattamento', $trattamenti_per_ricetta);
        $word->WriteBookmarkText('paziente_numricetta', $row1['num_ricetta']);
        $word->WriteBookmarkText('paziente_durata', unhtmlentities($row1['durata']));
        $word->WriteBookmarkText('paziente_trattamenti_frequenza', $trattamenti_frequenza);
        $word->WriteBookmarkText('paziente_datainiziotratt', unhtmlentities($row1['DataInizioTrattamento']));
        $word->WriteBookmarkText('paziente_datafinetratt', unhtmlentities($row1['DataFineTrattamento']));




        $word->WriteBookmarkText('paziente_ASL', unhtmlentities($row1['DescrizioneAsl']));
        $word->WriteBookmarkText('paziente_Distretto', unhtmlentities($row1['DescrizioneDistretto']));
        $word->WriteBookmarkText('medico_responsabile', unhtmlentities($row1['medico_responsabile']));
        $word->WriteBookmarkText('case_manager', unhtmlentities($row1['case_manager']));
        $word->WriteBookmarkText('op_planning', $op_planning);
        $word->WriteBookmarkText('num_progressivo', $idinserimento);

        $word->WriteBookmarkText('flag_fisioterapista', $flag_fisioterapista);
        $word->WriteBookmarkText('flag_psicomotricista', $flag_psicomotricista);
        $word->WriteBookmarkText('flag_logopedista', $flag_logopedista);
        $word->WriteBookmarkText('flag_terapista_occ', $flag_terapista_occ);
        $word->WriteBookmarkText('flag_psicoterapeuta', $flag_psicoterapeuta);
        $word->WriteBookmarkText('flag_neuropsicomotricista', $flag_neuropsicomotricista);

        $word->WriteBookmarkText('paziente_firma_sua_o_tutore', $paziente_firma_sua_o_tutore);
        $word->WriteBookmarkText('firma_tutore_princ', $firma_tutore_princ);
        $word->WriteBookmarkText('firma_tutore1', $firma_tutore1);
        $word->WriteBookmarkText('firma_tutore2', $firma_tutore2);

        $word->WriteBookmarkText('paziente_indirizzo_suo_o_tutore', $paziente_indirizzo_suo_o_tutore);
        $word->WriteBookmarkText('paziente_telefono_suo_o_tutore', $paziente_telefono_suo_o_tutore);
        $word->WriteBookmarkText('telefono_tutore', $telefono_tutore);
        $word->WriteBookmarkText('paziente_comune_residenza_suo_o_tutore', $paziente_comune_residenza_suo_o_tutore);

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

        $word->WriteBookmarkText('parente_nome', $nome_parente);
        $word->WriteBookmarkText('parente_cognome', $cognome_parente);
        $word->WriteBookmarkText('parente_datanascita',  $data_nascita_parente);
        $word->WriteBookmarkText('parente_comune_nascita', $comune_nascita_parente);
        $word->WriteBookmarkText('parente_provincia_nascita', $provincia_nascita_parente);
        $word->WriteBookmarkText('parente_comune_residenza', $comune_residenza_parente);
        $word->WriteBookmarkText('parente_provincia_residenza', $provincia_residenza_parente);
        $word->WriteBookmarkText('parente_indirizzo', $indirizzo_parente);
        $word->WriteBookmarkText('parente_cod_fisc', $codfisc_parente);
        $word->WriteBookmarkText('parente_telefono', $tel_parente);
        $word->WriteBookmarkText('parente_relazione', $relazione_parente);
        $word->WriteBookmarkText('parente_sesso', $sesso_parente);
        $word->WriteBookmarkText('parente_nato', $nato_parente);
        $word->WriteBookmarkText('documento_riconoscimento_parente', $num_doc_id);


        $word->WriteBookmarkText('data_odierna', date('d/m/Y'));
        $word->WriteBookmarkText('mese_odierno', date('m'));
        $word->WriteBookmarkText('anno_odierno', date('Y'));
        $word->WriteBookmarkText('ora_attuale', date('H:i'));
    } else {
        $contenuto = rtfReplaceWord('data_odierna', date('d/m/Y'), $contenuto);
        $contenuto = rtfReplaceWord('mese_odierno', date('m'), $contenuto);
        $contenuto = rtfReplaceWord('anno_odierno', date('Y'), $contenuto);
        $contenuto = rtfReplaceWord('ora_attuale', date('H:i'), $contenuto);

        if (!empty($row1['ricovero_data'])) {
            $contenuto = rtfReplaceWord('data_ricovero', unhtmlentities(date('d/m/Y', strtotime($row1['ricovero_data']))), $contenuto);
        } else {
            $contenuto = rtfReplaceWord('data_ricovero', 'N.D.', $contenuto);
        }
        $contenuto = rtfReplaceWord('stanza_ricovero', unhtmlentities($row1['ricovero_stanza']), $contenuto);
        $contenuto = rtfReplaceWord('letto_ricovero', unhtmlentities($row1['ricovero_letto']), $contenuto);
        $contenuto = rtfReplaceWord('provenienza_ricovero', unhtmlentities($row1['ricovero_provenienza']), $contenuto);

        $contenuto = rtfReplaceWord('paziente_nome', unhtmlentities($row1['Nome']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_cognome', unhtmlentities($row1['Cognome']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_datanascita', formatta_data($row1['data_nascita']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_annonascita', date('Y', strtotime($row1['data_nascita'])), $contenuto);
        $contenuto = rtfReplaceWord('paziente_nato', pulisci_lettura($nato), $contenuto);
        $contenuto = rtfReplaceWord('paziente_prov_residenza', pulisci_lettura($sigla_prov), $contenuto);
        $contenuto = rtfReplaceWord('codice_fiscale', unhtmlentities($row1['codice_fiscale']), $contenuto);

        $contenuto = rtfReplaceWord('paziente_comune_nascita', unhtmlentities($row1['comune_nascita']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_comune_residenza', unhtmlentities($row1['comune_residenza']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_indirizzo', unhtmlentities($row1['indirizzo']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_cap', unhtmlentities($row1['cap']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_provincia_nascita', unhtmlentities($row1['provincia_nascita']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_telefono', unhtmlentities($row1['telefono']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_cellulare', unhtmlentities($row1['cellulare']), $contenuto);

        //$contenuto = rtfReplaceWord('medico_prescrittore',unhtmlentities($row1['prescrittore']), $contenuto);			// non funziona, sostituito dalla riga seguente
        $contenuto = rtfReplaceWord('paziente_medico_prescrittore', unhtmlentities($row1['NominativoPrescrittore']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_icd9', unhtmlentities($row1['icd9']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_icd10', unhtmlentities($row1['icd10']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_pacchetto', unhtmlentities($row1['pacchetto']), $contenuto);
        $contenuto = rtfReplaceWord('flag_tipo_operatore_logopedista', $flag_tipo_operatore_logopedista, $contenuto);
        $contenuto = rtfReplaceWord('flag_tipo_operatore_terapista_riabilitazione', $flag_tipo_operatore_terapista_riabilitazione, $contenuto);

        $contenuto = rtfReplaceWord('codice_fiscale', unhtmlentities($row1['codice_fiscale']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_sesso', $sesso, $contenuto);
        $contenuto = rtfReplaceWord('paziente_cartella', $row1['codice_cartella'] . "/" . convert_versione($row1['versione']), $contenuto);
        //$contenuto = rtfReplaceWord('paziente_cartella',$row1['codice_cartella'], $contenuto);
        $contenuto = rtfReplaceWord('paziente_regime', unhtmlentities($row1['regime']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_normativa', unhtmlentities($row1['normativa']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_diagnosi', unhtmlentities($row1['Diagnosi']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_menomazione', unhtmlentities($row1['menomazione']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_cod_menomazione', unhtmlentities($row1['cod_menomazione']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_trattamento', $trattamenti_per_ricetta, $contenuto);
        $contenuto = rtfReplaceWord('paziente_numricetta', $row1['num_ricetta'], $contenuto);
        $contenuto = rtfReplaceWord('paziente_trattamenti_frequenza', $trattamenti_frequenza, $contenuto);
        $contenuto = rtfReplaceWord('paziente_durata', unhtmlentities($row1['durata']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_datainiziotratt', formatta_data($row1['DataInizioTrattamento']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_annoiniziotratt', date('Y', strtotime($row1['DataInizioTrattamento'])), $contenuto);
        $contenuto = rtfReplaceWord('paziente_datafinetratt', formatta_data($row1['DataFineTrattamento']), $contenuto);

        $contenuto = rtfReplaceWord('paziente_ASL', unhtmlentities($row1['DescrizioneAsl']), $contenuto);
        $contenuto = rtfReplaceWord('paziente_Distretto', unhtmlentities($row1['DescrizioneDistretto']), $contenuto);
        $contenuto = rtfReplaceWord('medico_responsabile', unhtmlentities($row1['medico_responsabile']), $contenuto);
        $contenuto = rtfReplaceWord('case_manager', unhtmlentities($row1['case_manager']), $contenuto);
        $contenuto = rtfReplaceWord('op_planning', $op_planning, $contenuto);
        $contenuto = rtfReplaceWord('num_progressivo', $idinserimento, $contenuto);
        $contenuto = rtfReplaceWord('protocollo_prescrizione', '', $contenuto);
        $contenuto = rtfReplaceWord('data_prescrizione', '', $contenuto);

        $contenuto = rtfReplaceWord('flag_fisioterapista', $flag_fisioterapista, $contenuto);
        $contenuto = rtfReplaceWord('flag_psicomotricista', $flag_psicomotricista, $contenuto);
        $contenuto = rtfReplaceWord('flag_logopedista', $flag_logopedista, $contenuto);
        $contenuto = rtfReplaceWord('flag_terapista_occ', $flag_terapista_occ, $contenuto);
        $contenuto = rtfReplaceWord('flag_psicoterapeuta', $flag_psicoterapeuta, $contenuto);
        $contenuto = rtfReplaceWord('flag_neuropsicomotricista', $flag_neuropsicomotricista, $contenuto);


        $contenuto = rtfReplaceWord('paziente_firma_sua_o_tutore', $paziente_firma_sua_o_tutore, $contenuto);
        $contenuto = rtfReplaceWord('paziente_nato_lui_o_parente', $paziente_nato_lui_o_parente, $contenuto);
        $contenuto = rtfReplaceWord('paziente_comune_nascita_sua_o_tutore', $paziente_comune_nascita_sua_o_tutore, $contenuto);
        $contenuto = rtfReplaceWord('paziente_data_nascita_sua_o_tutore', $paziente_data_nascita_sua_o_tutore, $contenuto);
        $contenuto = rtfReplaceWord('paziente_cod_fisc_suo_o_tutore', $paziente_cod_fisc_suo_o_tutore, $contenuto);
        $contenuto = rtfReplaceWord('flag_in_qualita_di_tutore', $flag_in_qualita_di_tutore, $contenuto);
        $contenuto = rtfReplaceWord('flag_in_qualita_di_paziente', $flag_in_qualita_di_paziente, $contenuto);
        $contenuto = rtfReplaceWord('sottoscritto', $sottoscritto, $contenuto);
        $contenuto = rtfReplaceWord('firma_tutore_princ', $firma_tutore_princ, $contenuto);
        $contenuto = rtfReplaceWord('firma_tutore1', $firma_tutore1, $contenuto);
        $contenuto = rtfReplaceWord('firma_tutore2', $firma_tutore2, $contenuto);

        $contenuto = rtfReplaceWord('paziente_indirizzo_suo_o_tutore', $paziente_indirizzo_suo_o_tutore, $contenuto);
        $contenuto = rtfReplaceWord('paziente_telefono_suo_o_tutore', $paziente_telefono_suo_o_tutore, $contenuto);
        $contenuto = rtfReplaceWord('telefono_tutore', $telefono_tutore, $contenuto);
        $contenuto = rtfReplaceWord('paziente_comune_residenza_suo_o_tutore', $paziente_comune_residenza_suo_o_tutore, $contenuto);

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

        //$contenuto = rtfReplaceWord('direttore_sanitario',"Dott. Sergio Bertogliatti",$contenuto);

        $contenuto = rtfReplaceWord('nome_parente', $nome_parente, $contenuto);
        $contenuto = rtfReplaceWord('cognome_parente', $cognome_parente, $contenuto);
        $contenuto = rtfReplaceWord('data_nascita_parente', $data_nascita_parente, $contenuto);
        $contenuto = rtfReplaceWord('luogo_nascita_parente', $comune_nascita_parente, $contenuto);
        $contenuto = rtfReplaceWord('provincia_nascita_parente', $provincia_nascita_parente, $contenuto);
        $contenuto = rtfReplaceWord('comune_residenza_parente', $comune_residenza_parente, $contenuto);
        $contenuto = rtfReplaceWord('prov_residenza_parente', $provincia_residenza_parente, $contenuto);
        $contenuto = rtfReplaceWord('indirizzo_residenza_parente', $indirizzo_parente, $contenuto);
        $contenuto = rtfReplaceWord('codice_fiscale_parente', $codfisc_parente, $contenuto);
        $contenuto = rtfReplaceWord('telefono_parente', $tel_parente, $contenuto);
        $contenuto = rtfReplaceWord('grado_parentela', $relazione_parente, $contenuto);
        $contenuto = rtfReplaceWord('sesso_parente', $sesso_parente, $contenuto);
        $contenuto = rtfReplaceWord('testo_nato_parente', $nato_parente, $contenuto);
        $contenuto = rtfReplaceWord('documento_riconoscimento_parente', $num_doc_id, $contenuto);

        // AME 28/12/2023 
        //SOLO PER TERAPIA FARMACOLOGICA TEST/NUOVO
        /* 			if($idmoduloversione < 6137)	// 6137
		{
			for($i = 5; $i < 16; $i++)
			{
				$contenuto = rtfReplaceWord('terap_farm_farmaco_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('dos_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('qnt_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('orario_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('durata_terapia_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('dt_inizio_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('dt_fine_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('per_sos_ini_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('operatore_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('tipologia_prescrizione_'.$i, '', $contenuto);
				$contenuto = rtfReplaceWord('note_'.$i, '', $contenuto);
			}
		} */
    }


    //DIRETTORE SANITARIO

    $user_id = $_SESSION['UTENTE']->get_userid();
    $query_ds = "SELECT nome, ordine_professionale, num_iscrizione FROM operatori WHERE dir_sanitario = 'y'";
    $rs_ds = mssql_query($query_ds, $conn);
    if (!$rs_ds) error_message(mssql_error());
    $row_ds = mssql_fetch_assoc($rs_ds);

    // AME - per i moduli "Programma Riabilitativo" delle cartelle con regime Residenziale,
    // se l'operatore che sta compilando l'istanza è il Direttore Sanitario, compare solo la firma nel campo "Firma Medico" e non la scrivo nel campo "Firma DS" (popolata dalla var "direttore_sanitario_facoltativo"
    if (trim($nome_operatore) == trim($row_ds['nome']))
        $direttore_sanitario_facoltativo = "s";    // firma DS facoltativa sul modulo
    else $direttore_sanitario_facoltativo = "n";    // firma DS obbligatoria sul modulo

    $direttore_sanitario = "Dott./Dott.ssa " . trim($row_ds['nome']);
    $ds_ordine_professionale = trim($row_ds['ordine_professionale']);
    $ds_num_iscrizione = trim($row_ds['num_iscrizione']);

    if ($ds_ordine_professionale !== NULL && $ds_num_iscrizione !== NULL) {
        if ($estensione == 'doc' || $estensione == 'dot') {
            $word->WriteBookmarkText('direttore_sanitario', unhtmlentities($direttore_sanitario . "\n #" . $ds_num_iscrizione . " | " . $ds_ordine_professionale));
            if ($direttore_sanitario_facoltativo == "n")
                $word->WriteBookmarkText('direttore_sanitario_facoltativo', unhtmlentities($direttore_sanitario . "\n #" . $ds_num_iscrizione . " | " . $ds_ordine_professionale));
            else $word->WriteBookmarkText('direttore_sanitario_facoltativo', "");
        } else {
            $contenuto = rtfReplaceWord('direttore_sanitario', unhtmlentities($direttore_sanitario . " \par #" . $ds_num_iscrizione . " | " . $ds_ordine_professionale), $contenuto);
            if ($direttore_sanitario_facoltativo == "n")
                $contenuto = rtfReplaceWord('direttore_sanitario_facoltativo', unhtmlentities($direttore_sanitario . " \par #" . $ds_num_iscrizione . " | " . $ds_ordine_professionale), $contenuto);
            else $contenuto = rtfReplaceWord('direttore_sanitario_facoltativo', "", $contenuto);
        }
    } else {

        if ($estensione == 'doc' || $estensione == 'dot') {
            $word->WriteBookmarkText('direttore_sanitario', unhtmlentities($direttore_sanitario));
            if ($direttore_sanitario_facoltativo == "n")
                $word->WriteBookmarkText('direttore_sanitario_facoltativo', unhtmlentities($direttore_sanitario));
            else $word->WriteBookmarkText('direttore_sanitario_facoltativo', "");
        } else {
            $contenuto = rtfReplaceWord('direttore_sanitario', unhtmlentities($direttore_sanitario), $contenuto);
            if ($direttore_sanitario_facoltativo == "n")
                $contenuto = rtfReplaceWord('direttore_sanitario_facoltativo', unhtmlentities($direttore_sanitario), $contenuto);
            else $contenuto = rtfReplaceWord('direttore_sanitario_facoltativo', "", $contenuto);
        }
    }
    mssql_free_result($rs_ds);


    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('intestazione_str', unhtmlentities($intestazione_str));
        $word->WriteBookmarkText('piede_str', unhtmlentities($piede_str));
        //$modulo_sgq=$row1['codice_cartella']."/".convert_versione($row1['versione'])."/".$codicesgq."/".$num_istanza;
        $modulo_sgq = $row1['codice_cartella'] . "/" . $codicesgq . "/" . $num_istanza;
        $word->WriteBookmarkText('cartella_sgq_versione', $modulo_sgq);
        $word->WriteBookmarkText('modulo_sgq', $codicesgq);
        $word->WriteBookmarkText('modulo_istanze', $num_istanza);
        $word->SaveAs($rtf_destination_path . $filename_tmp);
        $word->Quit();

        add_log($filename_tmp, $idpaziente, $idmodulopadre, $idcartella);
        return ($filename_tmp);
    } else {
        $contenuto = rtfReplaceWord('intestazione_str', unhtmlentities($intestazione_str), $contenuto);
        $contenuto = rtfReplaceWord('piede_str', unhtmlentities(trim($piede_str) . "\n" . $piede_str), $contenuto);
        //$modulo_sgq=$row1['codice_cartella']."/".convert_versione($row1['versione'])."/".$codicesgq."/".$num_istanza;
        $modulo_sgq = $row1['codice_cartella'] . "/" . $codicesgq . "/" . $num_istanza;
        $contenuto = rtfReplaceWord('cartella_sgq_versione', $modulo_sgq, $contenuto);
        $contenuto = rtfReplaceWord('modulo_sgq', $codicesgq, $contenuto);
        $contenuto = rtfReplaceWord('modulo_istanze', $num_istanza, $contenuto);
        
        saveRtfFile($rtf_destination_path, $filename_tmp, $contenuto);
        add_log($filename_tmp, $idpaziente, $idmodulopadre, $idcartella);
        $handle = fopen($rtf_destination_path . $filename_tmp, "r");
        $content2 = fread($handle, filesize($rtf_destination_path . $filename_tmp));
        fclose($handle);

        // salva il pdf e restituisce la dir per gli RTF
        if (isset($_REQUEST['conv_pdf'])) {
            $rtf_destination_path = str_replace('\\', '/', $rtf_destination_path);
            echo  $rtf_destination_path . $filename_tmp;
        }
        // salva il pdf e restituisce la dir per i PDF allegati alla CC
        elseif (isset($_REQUEST['conv_e_allega_pdf'])) {
            echo  $rtf_destination_path . $filename_tmp;
            die();
        }
        // salva l'rtf e ne propone il download
        else {
            header('Content-Type: application/rtf');
            header("Content-Disposition: attachment; filename=" . $filename_tmp);
            echo $content2;
            die();
        }
    }
}


function Stampa_Modulo_sgq($idarea, $idinserimento, $idmodulopadre)
{
    global $relative_path, $rtf_destination_path, $intestazione_str, $piede_str;
    //echo($idarea."--".$idinserimento."-- ".$idmodulopadre);
    //exit();
    $conn = db_connect();
    srand((float)microtime() * 1000000);
    $random = rand(0, 999999999999999);
    $i = 1;
    if ($idarea != 0)
        if ($idmodulopadre != "") {
            $query = "select * from re_moduli_valori_stampa_sgq where (id_inserimento=$idinserimento and id_area=$idarea and id_modulo=$idmodulopadre) ORDER BY peso ASC";
            //echo($query);
            //exit();
        } else
            $query = "select * from re_moduli_valori_stampa_sgq where (id_inserimento=$idinserimento and id_area=$idarea and id_modulo=$idmodulopadre) ORDER BY peso ASC";
    else
        $query = "select * from re_moduli_valori_stampa_sgq where (id_modulo=$idmodulopadre and id_inserimento=$idinserimento and id_area=0) ORDER BY peso ASC";


    $rt = mssql_query($query, $conn);
    if (!$rt) error_message(mysql_error());
    $num_istanza = $idinserimento;
    $arr_segnalibri = array();
    while ($row = mssql_fetch_assoc($rt)) {
        if ($i == 1) {
            $idmoduloversione = $row['idmoduloversione'];
            $filename_tmp = "tmp_" . $random . $row['modello_word'];
            $filename = $row['modello_word'];
            $codicesgq = $row['codice'];
            //si blocca
            $estensione = substr($filename, -3);

            if ($estensione == 'doc' || $estensione == 'dot') {
                $word = new clsMSWord;
                $word->Open($relative_path . $filename);
                $word->WriteBookmarkText('intestazione', $row['intestazione']);
            } else {
                $relative_path = $relative_path . 'rtf' . DIRECTORY_SEPARATOR;
                $fp = fopen($relative_path . $filename, "rt");
                $contenuto = fread($fp, filesize($relative_path . $filename));
                $contenuto = rtfReplaceWord('intestazione', $row['intestazione'], $contenuto);
            }
            $i++;
            //echo($word);
            //echo("prima_");
            //exit();
        }
        $valore = $row['valore'];
        if ($row['tipo'] == 4) {
            $idcampo = $row['idcampo'];
            $query3 = "select * from re_moduli_combo_propriety where idcampo=$idcampo";
            $rs3 = mssql_query($query3, $conn);
            if (!$rs3) error_message(mssql_error());
            $row3 = mssql_fetch_assoc($rs3);
            $multi = $row3['multi'];
            mssql_free_result($rs3);

            $query2 = "select * from re_moduli_combo where idcampo=$idcampo ";
            $rs2 = mssql_query($query2, $conn);

            if (!$rs2) error_message(mssql_error());
            if ($multi == 1) {
                $et = "";
                $et = "";
                $valore = split(";", $valore);
                while ($row2 = mssql_fetch_assoc($rs2)) {
                    $valore1 = $row2['valore'];
                    $etichetta = extractComboCode($row2['etichetta']);
                    $separatore = $row2['separatore'];
                    switch ($separatore) {
                        case 0:
                            $sep = " - ";
                            break;
                        case 1:
                            $sep = " - ";
                            break;
                        case 2:
                            $sep = " , ";
                            break;
                    }
                    if (in_array($valore1, $valore)) $et .= $etichetta . $sep;
                }
                $et = substr($et, 0, strlen($et) - 2);
                $valore = $et;
            } else {
                while ($row2 = mssql_fetch_assoc($rs2)) {
                    if ($row2['valore'] == $valore) $valore = extractComboCode($row2['etichetta']);
                }
            }
        } elseif ($row['tipo'] == 9) {
            $multi_ram = 0;
            $idcampo = $row['idcampo'];
            $query3 = "select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo";
            $rs3 = mssql_query($query3, $conn);
            if (!$rs3) error_message(mssql_error());
            $row3 = mssql_fetch_assoc($rs3);
            $multi_ram = $row3['multi'];
            mssql_free_result($rs3);
            $query2 = "select * from re_moduli_combo_ramificate where idcampo=$idcampo";
            $rs2 = mssql_query($query2, $conn);
            if (!$rs2) error_message(mssql_error());
            if ($multi_ram == 1) {
                $et = "";
                $valore = split(";", $valore);
                while ($row2 = mssql_fetch_assoc($rs2)) {
                    $valore1 = $row2['valore'];
                    $etichetta = extractComboCode($row2['etichetta']);
                    if (in_array($valore1, $valore)) $et .= $etichetta . " - ";
                }
                //$et=substr($et,0,strlen($et)-2);						
                echo ($et);
            } else {
                $valore2 = $valore;
                while ($row2 = mssql_fetch_assoc($rs2)) {
                    $idc = $row2['idcombo'];
                    $idcampocombo = $row2['idcampocombo'];
                    $valore1 = $row2['valore'];
                    $etichetta1 = extractComboCode($row2['etichetta']);
                    $v = (int)($valore2);
                    $valore = get_rincorri_figlio_per_stampa($idc, $idcampocombo, "", $v);
                    if ($valore != '')
                        break;
                }
                //$valore1=$row2['etichetta'];
                //echo($valore1);
            }
        } elseif ($row['tipo'] == 7) {
            if (trim($valore) == "") $valore = "Nessun allegato inserito";
        }
        if ($valore) {
            $valore = unhtmlentities($valore);
            $valore = str_replace("?", "'", $valore);

            if ($estensione == 'doc' || $estensione == 'dot') {
                $word->WriteBookmarkText($row['segnalibro'], $valore);
            } else {
                $contenuto = rtfReplaceWord($row['segnalibro'], $valore, $contenuto);
            }
        }
        //echo("__qui");
        if ($idpaziente == "")    $idpaziente = $row['idpaziente'];
        array_push($arr_segnalibri, $row['segnalibro']);
    }
    mssql_free_result($rt);
    //segnalibri null
    $query = "SELECT etichetta, segnalibro FROM campi WHERE (idmoduloversione = $idmoduloversione) AND (status = 1) AND (cancella = 'n')";
    $result = mssql_query($query, $conn);
    while ($row2 = mssql_fetch_assoc($result)) {
        $segnalibro = $row2['segnalibro'];
        if (!(in_array($segnalibro, $arr_segnalibri))) {
            if ($estensione == 'doc' || $estensione == 'dot') {
                $word->WriteBookmarkText($row2['segnalibro'], " ");
            } else {
                $contenuto = rtfReplaceWord($row2['segnalibro'], " ", $contenuto);
            }
        }
    }
    mssql_free_result($result);

    if ($estensione == 'doc' || $estensione == 'dot') {
        $word->WriteBookmarkText('intestazione_str', unhtmlentities($intestazione_str));
        $word->WriteBookmarkText('piede_str', unhtmlentities($piede_str));
        //$modulo_sgq=$row1['codice_cartella']."/".convert_versione($row1['versione'])."/".$codicesgq."/".$num_istanza;
        $modulo_sgq = $row1['codice_cartella'] . "/" . $codicesgq . "/" . $num_istanza;
        $word->WriteBookmarkText('cartella_sgq_versione', $modulo_sgq);
        $word->WriteBookmarkText('modulo_sgq', $codicesgq);
        $word->WriteBookmarkText('modulo_istanze', $num_istanza);
        $word->SaveAs($rtf_destination_path . $filename_tmp);
        $word->Quit();

        add_log_sgq($filename_tmp, $idmodulopadre, $idarea);
        return ($filename_tmp);
    } else {
        $contenuto = rtfReplaceWord('intestazione_str', unhtmlentities($intestazione_str), $contenuto);
        $contenuto = rtfReplaceWord('piede_str', unhtmlentities($piede_str), $contenuto);
        //$modulo_sgq=$row1['codice_cartella']."/".convert_versione($row1['versione'])."/".$codicesgq."/".$num_istanza;
        $modulo_sgq = $row1['codice_cartella'] . "/" . $codicesgq . "/" . $num_istanza;
        $contenuto = rtfReplaceWord('cartella_sgq_versione', $modulo_sgq, $contenuto);
        $contenuto = rtfReplaceWord('modulo_sgq', $codicesgq, $contenuto);
        $contenuto = rtfReplaceWord('modulo_istanze', $num_istanza, $contenuto);
        saveRtfFile($rtf_destination_path, $filename_tmp, $contenuto);
        add_log_sgq($filename_tmp, $idmodulopadre, $idarea);
        $handle = fopen($rtf_destination_path . $filename_tmp, "r");
        $content2 = fread($handle, filesize($rtf_destination_path . $filename_tmp));
        fclose($handle);
        header('Content-Type: application/rtf');
        header("Content-Disposition: attachment; filename=" . $filename_tmp);
        echo $content2;
        die();
    }
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


function getSegnalibroDocTemp($etichetta, $temperatura)
{
    $days = array(
        'XXXI', 'XXX',
        'XXIX', 'XXVIII', 'XXVII', 'XXVI', 'XXV', 'XXIV', 'XXIII', 'XXII', 'XXI', 'XX',
        'XIX', 'XVIII', 'XVII', 'XVI', 'XV', 'XXV', 'XIII', 'XII', 'XI', 'X',
        'IX', 'VIII', 'VII', 'VI', 'V', 'IV', 'III', 'II', 'I'
    );
    for ($d = 31; $d >= 1; $d--) {
        if ($days[$d] == 'XXX') {
            return $d;
        }
    }

    if (strpos($etichetta, 'XXXI') !== false) {
        $day_to_exclude = 31;
    } elseif (strpos($etichetta, 'XXX') !== false) {
        $day_to_exclude = 30;
    } elseif (strpos($etichetta, 'XXIX') !== false) {
        $day_to_exclude = 29;
    } elseif (strpos($etichetta, 'XXVIII') !== false) {
        $day_to_exclude = 28;
    } elseif (strpos($etichetta, 'XXVII') !== false) {
        $day_to_exclude = 27;
    } elseif (strpos($etichetta, 'XXVI') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_26';
    } elseif (strpos($etichetta, 'XXV') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_25';
    } elseif (strpos($etichetta, 'XXIV') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_24';
    } elseif (strpos($etichetta, 'XXIII') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_23';
    } elseif (strpos($etichetta, 'XXII') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_22';
    } elseif (strpos($etichetta, 'XXI') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_21';
    } elseif (strpos($etichetta, 'XX') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_20';
    } elseif (strpos($etichetta, 'XIX') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_19';
    } elseif (strpos($etichetta, 'XVIII') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_18';
    } elseif (strpos($etichetta, 'XVII') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_17';
    } elseif (strpos($etichetta, 'XVI') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_16';
    } elseif (strpos($etichetta, 'XV') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_15';
    } elseif (strpos($etichetta, 'XIV') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_14';
    } elseif (strpos($etichetta, 'XIII') !== false) {
        return str_replace('.', '', $valore) . '_13';
    } elseif (strpos($etichetta, 'XII') !== false) {
        return str_replace('.', '', $valore) . '_12';
    } elseif (strpos($etichetta, 'XI') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_11';
    } elseif (strpos($etichetta, 'X') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_10';
    } elseif (strpos($etichetta, 'IX') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_9';
    } elseif (strpos($etichetta, 'VIII') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_8';
    } elseif (strpos($etichetta, 'VII') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_7';
    } elseif (strpos($etichetta, 'VI') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_6';
    } elseif (strpos($etichetta, 'V') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_5';
    } elseif (strpos($etichetta, 'IV') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_4';
    } elseif (strpos($etichetta, 'III') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_3';
    } elseif (strpos($etichetta, 'II') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_2';
    } elseif (strpos($etichetta, 'I') !== false) {
        $day_to_exclude = str_replace('.', '', $valore) . '_1';
    }

    for ($c = 1; $c <= 31; $c++) {
        if ($c != $day_to_exclude) {
            if ($estensione == 'doc' || $estensione == 'dot') {
                $word->WriteBookmarkText(str_replace('.', '', $temp) . "_$c", '');
            } else {
                $contenuto = rtfReplaceWord(str_replace('.', '', $temp) . "_$c", '', $contenuto);
            }
        } else {
            if ($estensione == 'doc' || $estensione == 'dot') {
                $word->WriteBookmarkText(str_replace('.', '', $temp) . "_$c", 'x');
            } else {
                $contenuto = rtfReplaceWord(str_replace('.', '', $temp) . "_$c", 'x', $contenuto);
            }
        }
    }
}
