<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

$id_permesso = 12;
session_start();

if(isset($_SESSION['UTENTE'])) {

	if(true || in_array($id_permesso, $_SESSION['PERMESSI'])) {
		
		switch($_POST['action']) {

			case "create":
				if(true || $_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					create();
				} else {
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;

			case "update":
				if(true || $_SESSION['UTENTE']->controlla_permessi($id_permesso, 2)) {
					update($_POST['id']);
				} else {
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;
			
			case "connect_to_patients":
				if(true || $_SESSION['UTENTE']->controlla_permessi($id_permesso, 2)) {
					connect_to_patients();
				} else {
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;

			case "delete":
				if(true || $_SESSION['UTENTE']->controlla_permessi($id_permesso, 3)) {
					del($_POST['id']);
				} else {
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;
		}
	}
	else {
		error_message("Non hai i permessi per visualizzare questa pagina!");
		go_home();
	}
} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}


function create() {
	$conn = db_connect();
	
	$nome = empty($_POST['nome']) ? '' : trim($_POST['nome']);
	$lotto = empty($_POST['lotto']) ? '' : trim($_POST['lotto']);
	$dosaggio = empty($_POST['dosaggio']) ? 'NULL' : str_replace(',','.', trim($_POST['dosaggio']));
	$data_scadenza = empty($_POST['data_scadenza']) ? '' : trim($_POST['data_scadenza']);
	$data_carico = empty($_POST['data_carico']) ? '' : trim($_POST['data_carico']);
	$quantita_caricata = empty($_POST['quantita']) ? '0' : str_replace(',','.', trim($_POST['quantita']));
	$quantita_attuale = empty($_POST['quantita_attuale']) ? '0' : str_replace(',','.', trim($_POST['quantita_attuale']));
	$giacenza_minima = empty($_POST['giacenza_minima']) ? '0' : str_replace(',','.', trim($_POST['giacenza_minima']));
	$tipologia_somministrazione = empty($_POST['tipologia_somministrazione']) ? '' : trim($_POST['tipologia_somministrazione']);
	$stato_farmaco = empty($_POST['stato_farmaco']) ? '' : trim($_POST['stato_farmaco']);
	$from_struttura = empty($_POST['from_struttura']) ? '0' : $_POST['from_struttura'];
	
	if(isset($_POST['reparto'])) {
		$reparto = $_POST['reparto'];
	} else {
		$reparto = "NULL";
	}
	
	if(isset($_POST['id_paziente'])) {
		$id_paziente = $_POST['id_paziente'];
	} else {
		$id_paziente = "NULL";
	}
		
	$ope_ins = $_SESSION['UTENTE']->_properties['uid'];
	
	$_sql = "INSERT INTO farmaci (nome, lotto, dosaggio, data_scadenza, data_carico, 
								quantita_caricata, quantita_attuale, giacenza_minima, tipologia_somministrazione, stato_farmaco, 
								struttura, id_reparto, id_paziente, ope_ins, status)
			VALUES ('$nome', '$lotto', $dosaggio, '$data_scadenza', '$data_carico',
					$quantita_caricata, $quantita_caricata, $giacenza_minima, $tipologia_somministrazione, $stato_farmaco, 
					$from_struttura, $reparto, $id_paziente, $ope_ins, 1)";
	$_rs = mssql_query($_sql, $conn);
	
	if(isset($_POST['pazienti'])) {
		foreach($_POST['pazienti'] as $paz) {
			$_sql = "INSERT INTO farmaci_struttura_pazienti (id_farmaco, id_paziente)
						VALUES ((SELECT id FROM farmaci WHERE nome = '$nome' AND lotto = '$lotto' AND dosaggio = '$dosaggio' AND ope_ins = $ope_ins), $paz)";
			$_rs = mssql_query($_sql, $conn);
		}
	}

	// il censimento con l'id del paziente è possibile solo dall'Area Sanitaria quindi gestisco il ritorno nella pagina specifica
	if($from_struttura) {
		echo("ok;;1;lista_farmaci.php");
	} else {
		echo("ok;$id_paziente;4;re_pazienti_sanitaria.php?do=show_anagrafica_farmaci");
	}
	exit();
}


function update() {
	$conn = db_connect();	
	
	$nome = empty($_POST['nome']) ? '' : trim($_POST['nome']);
	$lotto = empty($_POST['lotto']) ? '' : trim($_POST['lotto']);
	$dosaggio = empty($_POST['dosaggio']) ? 'NULL' : str_replace(',','.', trim($_POST['dosaggio']));
	$data_scadenza = empty($_POST['data_scadenza']) ? '' : trim($_POST['data_scadenza']);
	$data_carico = empty($_POST['data_carico']) ? '' : trim($_POST['data_carico']);
	$quantita_caricata = empty($_POST['quantita']) ? '0' : str_replace(',','.', trim($_POST['quantita']));
	$quantita_attuale = empty($_POST['quantita_attuale']) ? '0' : str_replace(',','.', trim($_POST['quantita_attuale']));
	$giacenza_minima = empty($_POST['giacenza_minima']) ? '0' : str_replace(',','.', trim($_POST['giacenza_minima']));
	$tipologia_somministrazione = empty($_POST['tipologia_somministrazione']) ? '' : trim($_POST['tipologia_somministrazione']);
	$stato_farmaco = empty($_POST['stato_farmaco']) ? '' : trim($_POST['stato_farmaco']);	
	$from_struttura = empty($_POST['from_struttura']) ? '0' : $_POST['from_struttura'];
	$ope_agg = $_SESSION['UTENTE']->_properties['uid'];
	
	if(isset($_POST['reparto'])) {
		$reparto = $_POST['reparto'];
	} else {
		$reparto = "NULL";
	}
	
	if(isset($_POST['id_paziente'])) {
		$id_paziente = $_POST['id_paziente'];
	} else {
		$id_paziente = "NULL";
	}
	
	if(!empty($_POST['qnt_scarico']) && $_POST['qnt_scarico'] > 0) {
		if($_POST['qnt_scarico'] > $quantita_attuale) {
			//FAR APPARIRE BANNER ROSSO CON AVVISO
		} else {
			$id_farmaco = $_POST['id'];
			$qnt_scarico = $_POST['qnt_scarico'];
			$note_scarico = $_POST['note_scarico'];
			$ipins = $_SERVER['REMOTE_ADDR'];
			$opeins = $_SESSION['UTENTE']->get_userid();
			$quantita_post_scarico = $quantita_attuale - $_POST['qnt_scarico'];
			
			$query_log = "INSERT INTO farmaci_log (id_farmaco, id_paziente, quantita_precedente, quantita_scaricata, 
													quantita_attuale, ope_ins, ip_ins, note)
									VALUES ($id_farmaco, $id_paziente, $quantita_attuale, $qnt_scarico,
											$quantita_post_scarico, $opeins, '$ipins', '$note_scarico')";
			$_rs = mssql_query($query_log, $conn);
			mssql_free_result($_rs);
			
			$quantita_attuale = $quantita_post_scarico;
		}
	}
	
	$stato_farmaco = $quantita_attuale == 0 && $stato_farmaco == 2 ? 3 : $stato_farmaco;
	
	$_sql = "UPDATE farmaci
				SET nome = '$nome', lotto = '$lotto', dosaggio = $dosaggio, data_scadenza = '$data_scadenza', 
					data_carico = '$data_carico', quantita_caricata = $quantita_caricata, quantita_attuale = $quantita_attuale,
					giacenza_minima = $giacenza_minima, tipologia_somministrazione = $tipologia_somministrazione,
					stato_farmaco = $stato_farmaco, id_reparto = $reparto, id_paziente = $id_paziente, ope_agg = $ope_agg, data_agg = GETDATE()
				WHERE id = " . $_POST['id'];
	$_rs = mssql_query($_sql, $conn);
	mssql_free_result($_rs);
	
	if(isset($_POST['pazienti'])) {
		$_rs = mssql_query("DELETE FROM farmaci_struttura_pazienti WHERE id_farmaco = " . $_POST['id'], $conn);
		mssql_free_result($_rs);
		
		foreach($_POST['pazienti'] as $paz) {
			$_sql = "INSERT INTO farmaci_struttura_pazienti (id_farmaco, id_paziente)
						VALUES (" . $_POST['id'] . ", $paz)";
			$_rs = mssql_query($_sql, $conn);
			mssql_free_result($_rs);
		}
	} else {
		$_rs = mssql_query("DELETE FROM farmaci_struttura_pazienti WHERE id_farmaco = " . $_POST['id'], $conn);
		mssql_free_result($_rs);
	}
	
	if($from_struttura) {
		echo("ok;;1;lista_farmaci.php");
	} else {
		echo("ok;$id_paziente;4;re_pazienti_sanitaria.php?do=show_anagrafica_farmaci");
	}

	exit();
}

function connect_to_patients() {
	$conn = db_connect();
	
	$_rs = mssql_query("DELETE FROM farmaci_paziente_pazienti 
							WHERE id_farmaco = " . $_POST['id'] . "AND id_paziente_proprietario = " . $_POST['id_paziente'], $conn);
	
	if(isset($_POST['pazienti'])) {
		foreach($_POST['pazienti'] as $paz) {
			$_sql = "INSERT INTO farmaci_paziente_pazienti (id_farmaco, id_paziente_proprietario, id_paziente_destinatario)
						VALUES (" . $_POST['id'] . "," . $_POST['id_paziente'] . ", $paz)";
			$_rs = mssql_query($_sql, $conn);
		}
	}
	
	echo("ok;" . $_POST['id_paziente'] . ";4;re_pazienti_sanitaria.php?do=show_anagrafica_farmaci");	
	exit();
}


function del() {
	$conn = db_connect();
	$ope_agg = $_SESSION['UTENTE']->_properties['uid'];
	
	$_sql = "UPDATE farmaci				
				SET status = 0, deleted = 1, ope_agg = $ope_agg, data_agg = GETDATE()
				WHERE id = " . $_POST['id'];
	$_rs = mssql_query($_sql, $conn);
	
	if($_rs) {
		$_sql = "DELETE FROM farmaci_struttura_pazienti				
					WHERE id_farmaco = " . $_POST['id'];
		mssql_query($_sql, $conn);
		
		$_sql = "DELETE FROM farmaci_paziente_pazienti				
					WHERE id_farmaco = " . $_POST['id'];
		mssql_query($_sql, $conn);
	}
	
	if(!$_rs) {
		echo 'false';
	} else {
		echo 'true';
	}
}