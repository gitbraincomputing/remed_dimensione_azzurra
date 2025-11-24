<?
include_once('functions.inc.php');
include_once('dbengine.inc.php');
include_once('function_page.php');

ini_set('memory_limit', '1024M');
ini_set('mssql.timeout', 60 * 10); // 10 min

$conn = db_connect();

if(isset($_GET['rep']))
{
	// Load the database configuration file 
	//include_once 'dbConfig.php'; 

	// Filter the excel data 
	function filterData(&$str){ 
		$str = preg_replace("/\t/", "\\t", $str); 
		$str = preg_replace("/\r?\n/", "\\n", $str); 
		if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
	} 
	 
	// Excel file name for download 
	
	$where = str_replace('\\', '', $_GET['where']);
	

	switch($_GET['rep']) {
		case 'rep_moduli_scaduti':
			$fileName = "Report Overvew Moduli Scaduti (" . date('Y-m-d H-i-s') . ").xls"; 
			$intestazione = array('Normativa', 'Regime', 'Nome operatore', 'Codice cartella', 'Cognome paziente', 'Nome paziente', 'Nome modulo', 'Data scadenza'); 
			$excelData = implode("\t", array_values($intestazione)) . "\n"; 	// Display column names as first row 

			$query="SELECT * FROM re_report_moduli_scaduti_pianificati " .$where . " ORDER BY data_scadenza ASC";		// Fetch records from database 
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs) > 0){
 				while($row = mssql_fetch_assoc($rs)) 
				{
					$lineData = array($row['normativa'], $row['regime'], $row['nome_operatore'], $row['codice_cartella']."/".$row['versione'], $row['cognome_paziente'], $row['nome_paziente'], $row['nome_modulo'], formatta_data($row['data_scadenza'])); 
					//array_walk($lineData, 'filterData'); 
					$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				}
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
		
		case 'rep_moduli_non_validati':
			$fileName = "Report Overvew Moduli Scaduti (" . date('Y-m-d H-i-s') . ").xls"; 
			$intestazione = array('Normativa', 'Regime', 'Nome operatore', 'Codice cartella', 'Cognome paziente', 'Nome paziente', 'Nome modulo', 'Progressivo', 'Data compilazione'); 
			$excelData = implode("\t", array_values($intestazione)) . "\n"; 	// Display column names as first row 

			$query="SELECT * FROM re_report_istanze_compilate_da_validare" . $where . " ORDER BY data_creazione ASC";		
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs) > 0){
 				while($row = mssql_fetch_assoc($rs)) 
				{
					$lineData = array($row['normativa'], $row['regime'], $row['nome_operatore'], $row['codice_cartella']."/".$row['versione_cartella'], $row['cognome_paziente'], $row['nome_paziente'], $row['nome_modulo'], $row['id_inserimento'], formatta_data($row['data_creazione'])); 
					//array_walk($lineData, 'filterData'); 
					$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				}
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
		
		case 'rep_allegati_non_caric':
			$fileName = "Report Verifica Allegati (" . date('Y-m-d H-i-s') . ").xls"; 
			$intestazione = array('Normativa', 'Regime', 'Nome operatore', 'Codice cartella', 'Cognome paziente', 'Nome paziente', 'Nome modulo', 'Data compilazione'); 
			$excelData = implode("\t", array_values($intestazione)) . "\n"; 	// Display column names as first row 

			$query="SELECT * FROM re_report_istanze_compilate_senza_allegato " . $where . " ORDER BY data_creazione ASC";		// Fetch records from database 
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs) > 0){
 				while($row = mssql_fetch_assoc($rs)) 
				{
					$lineData = array($row['normativa'], $row['regime'], $row['nome_operatore'], $row['codice_cartella']."/".$row['versione_cartella'], $row['cognome_paziente'], $row['nome_paziente'], $row['nome_modulo'], formatta_data($row['data_creazione'])); 
					//array_walk($lineData, 'filterData'); 
					$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				}
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
		
		case 'rep_raggiung_risult':
			$fileName = "Report Raggiungimento Risultati (" . date('Y-m-d H-i-s') . ").xls"; 
			$intestazione = array('Normativa', 'Regime', 'Nome operatore', 'Codice cartella', 'Cognome paziente', 'Nome paziente', 'Nome modulo', 'Raggiungimento risultati', 'Data compilazione'); 
			$excelData = implode("\t", array_values($intestazione)) . "\n"; 	// Display column names as first row 

			// SBO 12/12/23: A seguito della richiesta di un doppio filtro temporale abbiamo sostituito l'utilizzo della 
			//	vista a favore della query diretta in quanto con il filtro temporale i tempi di estrazione erano troppo 
			//	elevati
			$query="SELECT TOP (100) PERCENT dbo.utenti_cartelle.idregime, dbo.normativa.normativa, dbo.regime.regime, dbo.utenti_cartelle.codice_cartella, dbo.utenti_cartelle.versione AS versione_cartella, dbo.operatori.nome AS nome_operatore, 
                         dbo.utenti.Cognome AS cognome_paziente, dbo.utenti.Nome AS nome_paziente, dbo.moduli.nome AS nome_modulo, dbo.istanze_testata.datains AS data_creazione, dbo.utenti_cartelle.id AS id_cartella, 
                         dbo.moduli.id AS id_modulo_versione, dbo.utenti_cartelle.idutente AS id_paziente, dbo.moduli.cancella, dbo.cartelle_pianificazione.id_cartella_pianificazione_testata, dbo.cartelle_pianificazione.id_modulo_padre, 
                         dbo.istanze_dettaglio.valore AS valore_risultati
					FROM	dbo.istanze_testata 
						INNER JOIN dbo.utenti_cartelle ON dbo.istanze_testata.id_cartella = dbo.utenti_cartelle.id 
						INNER JOIN dbo.operatori ON dbo.istanze_testata.opeins = dbo.operatori.uid 
						INNER JOIN dbo.utenti ON dbo.utenti_cartelle.idutente = dbo.utenti.IdUtente 
						INNER JOIN dbo.normativa ON dbo.utenti_cartelle.idnormativa = dbo.normativa.idnormativa 
						INNER JOIN dbo.regime ON dbo.utenti_cartelle.idregime = dbo.regime.idregime 
						LEFT OUTER JOIN dbo.campi 
						INNER JOIN dbo.istanze_dettaglio ON dbo.campi.idcampo = dbo.istanze_dettaglio.idcampo 
						INNER JOIN dbo.moduli ON dbo.campi.idmoduloversione = dbo.moduli.id ON dbo.istanze_testata.id_istanza_testata = dbo.istanze_dettaglio.id_istanza_testata AND dbo.istanze_testata.id_modulo_versione = dbo.moduli.id 
						RIGHT OUTER JOIN dbo.cartelle_pianificazione ON dbo.istanze_testata.id_modulo_versione = dbo.cartelle_pianificazione.id_modulo_versione
					WHERE (dbo.utenti_cartelle.cancella = 'n') AND (dbo.moduli.cancella = 'n')
						AND (dbo.campi.segnalibro LIKE N'raggiungimento_obiettivi_ai%' OR dbo.campi.segnalibro LIKE N'raggiungimento_risultati_%') AND (dbo.utenti_cartelle.codice_cartella <> N'000471') 
						AND (dbo.cartelle_pianificazione.id_cartella_pianificazione_testata =
								 (SELECT        MAX(id_pianificazione_testata) AS max_id_pian_test
								   FROM            dbo.cartelle_pianificazione_testata AS cartelle_pianificazione_testata_1
								   WHERE        (id_cartella = dbo.istanze_testata.id_cartella))
							) 
						AND (dbo.istanze_dettaglio.valore <> '')
						AND (dbo.operatori.nome <> 'admin') AND (dbo.operatori.nome <> 'Software Administrator') AND (dbo.utenti_cartelle.idutente <> 605)
						$where
					GROUP BY dbo.utenti_cartelle.idregime, dbo.normativa.normativa, dbo.regime.regime, dbo.utenti_cartelle.codice_cartella, dbo.utenti_cartelle.versione, dbo.operatori.nome, dbo.utenti.Cognome, dbo.utenti.Nome, dbo.moduli.nome, 
											 dbo.istanze_testata.datains, dbo.utenti_cartelle.id, dbo.moduli.id, dbo.utenti_cartelle.idutente, dbo.moduli.cancella, dbo.cartelle_pianificazione.id_cartella_pianificazione_testata, dbo.cartelle_pianificazione.id_modulo_padre, 
											 dbo.istanze_dettaglio.valore
					ORDER BY data_creazione ASC";
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs) > 0){
 				while($row = mssql_fetch_assoc($rs)) 
				{
					switch($row['valore_risultati']) {
						case "N.A.":	$ragg_risult = "N.D.";				break;
						case "1":		$ragg_risult = "Raggiunti";				break;
						case "2":		$ragg_risult = "Parzialmente raggiunti";	break;
						case "3":		$ragg_risult = "Non raggiunti";			break;
					}
					
					$lineData = array($row['normativa'], $row['regime'], $row['nome_operatore'], $row['codice_cartella']."/".$row['versione_cartella'], $row['cognome_paziente'], $row['nome_paziente'], $row['nome_modulo'], $ragg_risult, formatta_data($row['data_creazione'])); 
					//array_walk($lineData, 'filterData'); 
					$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				}
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
		
		case 'rep_firme_digitali_emesse':

			$fileName = "Report Firme Digitali Emesse (" . date('Y-m-d H-i-s') . ").xls"; 
			$intestazione = array('Operatore', 'Conteggio Firme Allegati1', 'Conteggio Firme Allegati2', 'Conteggio Totale'); 
			$excelData = implode("\t", array_values($intestazione)) . "\n"; 	// Display column names as first row 
					
			$query="SELECT nome_operatore, SUM(count_firme_alleg1) AS tot_firme_alleg1, SUM(count_firme_alleg2) AS tot_firme_alleg2 
					FROM re_conta_firme_digitali_emesse" . 
					$where . " 
					group by nome_operatore
					order by nome_operatore ASC";
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs) > 0){
				$i = 1;
				$tot_firme_globale = $tot_firme_alleg1 = $tot_firme_alleg2 = 0;
				
				while($row = mssql_fetch_assoc($rs)) 
				{
					$tot_firme_per_operatore = $row['tot_firme_alleg1'] + $row['tot_firme_alleg2'];
					$tot_firme_alleg1 		+= $row['tot_firme_alleg1'];
					$tot_firme_alleg2 		+= $row['tot_firme_alleg2'];
					$tot_firme_globale 		+= $row['tot_firme_alleg1'] + $row['tot_firme_alleg2'];
					
					$lineData = array($row['nome_operatore'], $row['tot_firme_alleg1'], $row['tot_firme_alleg2'], $tot_firme_per_operatore); 
					$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				}
				
				$lineData = array('TOTALE FIRME:', $tot_firme_alleg1, $tot_firme_alleg2, $tot_firme_globale); 
				$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
				
		case 'rep_terapia_farmacologica':
			$start_time = $_GET['start_time'];
			$end_time = $_GET['end_time'];
			
			$fileName = "Report Terapia Farm. (" . date('Y-m-d H-i-s') . ").xls"; 
			$intestazione = array("Orario", "Paziente", "Id Paziente", "Reparto paziente", "Farmaco", "Dosaggio", "Tipologia Somministrazione", "Quantita"); 
			$excelData = implode("\t", array_values($intestazione)) . "\n";
			
			$_query = "SELECT ist_test.id_paziente, cmp.segnalibro, 
							CASE cmp.etichetta 
								WHEN 'Tipologia somministrazione' THEN (SELECT etichetta FROM  campi_combo WHERE idcombo = 250 AND valore = ist_det.valore)
								ELSE CAST(ist_det.valore AS text)
							END AS valore
							FROM dbo.istanze_testata AS ist_test
							INNER JOIN ( SELECT id_paziente, MAX(id_inserimento) AS id_inserimento
											FROM dbo.istanze_testata
											WHERE id_modulo_padre = 205 AND id_paziente not in (605) 
											GROUP BY id_paziente) AS max_id_ins ON max_id_ins.id_inserimento = ist_test.id_inserimento AND max_id_ins.id_paziente = ist_test.id_paziente 
							INNER JOIN dbo.istanze_dettaglio AS ist_det ON ist_det.id_istanza_testata = ist_test.id_istanza_testata
							INNER JOIN dbo.campi AS cmp ON cmp.idcampo = ist_det.idcampo
							INNER JOIN (
								SELECT paz_cart_inf.id as IdCartella
								FROM utenti as paz
								INNER JOIN (
									select id, idutente
									from utenti_cartelle
									where idutente not in (605) AND idregime IN (52,53,59,60,61,62,64,65) AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'
								) as paz_cart_inf ON paz_cart_inf.idutente = paz.IdUtente
								WHERE paz.cancella = 'n' AND paz.stato = 1 AND paz.cancella = 'n'
							) AS paz_attivi ON paz_attivi.IdCartella = ist_test.id_cartella
						WHERE ist_test.id_modulo_padre = 205 AND cmp.segnalibro IS NOT NULL 
							AND cmp.segnalibro <> '' AND cmp.segnalibro <> ' ' $where";
			$rs_terap = mssql_query($_query, $conn);

			if(mssql_num_rows($rs_terap) > 0) {
				$terap_farm_list = array();			
				$today_time = strtotime(date('Y-m-d', strtotime('now')));

				while($terap_farm_row = mssql_fetch_assoc($rs_terap)) 
				{
					$lunghezza = strlen($terap_farm_row['segnalibro']);				
					$idx = substr($terap_farm_row['segnalibro'], $lunghezza - 1, 1);				
					$segnalibro = substr($terap_farm_row['segnalibro'], 0, $lunghezza - 2);

					if(strpos($terap_farm_row['segnalibro'], 'dt_inizio_') !== false) {
						if(!empty($terap_farm_row['valore']) && trim($terap_farm_row['valore']) != '') {
							$date = explode('/',$terap_farm_row['valore']);
							$time = strtotime(date('Y-m-d', strtotime($date[2].'-'.$date[1].'-'.$date[0])));
							
							if($today_time < $time) {
								$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = true;
							} else {
								$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = false;
							}
						} else {
							$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = true;
						}
					} elseif(strpos($terap_farm_row['segnalibro'], 'dt_fine_') !== false) {
						if(!empty($terap_farm_row['valore']) && trim($terap_farm_row['valore']) != '') {
							$date = explode('/',$terap_farm_row['valore']);
							$time = strtotime(date('Y-m-d', strtotime($date[2].'-'.$date[1].'-'.$date[0])));
							
							if($today_time > $time) {
								$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = true;
							} else {
								$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = false;
							}
						} else {
							$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = true;
						}
					} elseif(strpos($terap_farm_row['segnalibro'], 'orario_') !== false) {
						if(!empty($terap_farm_row['valore']) && trim($terap_farm_row['valore']) != '') {
							$time = str_replace(';',',',$terap_farm_row['valore']);
							
							$_query = "SELECT etichetta
										FROM campi_combo
										WHERE idcombo = 257 AND valore in ($time)";
							$_rs = mssql_query($_query, $conn);
							
							while($_row = mssql_fetch_assoc($_rs)) {
								$terap_farm_list[$terap_farm_row['id_paziente']][$idx][$segnalibro][] = $_row['etichetta'];
							}
							
							mssql_free_result($_rs); 
							$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = false;
						} else {
							$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = true;
						}
					} else {
						$terap_farm_list[$terap_farm_row['id_paziente']][$idx][$segnalibro] = $terap_farm_row['valore'];
					}
				}
				
				mssql_free_result($rs_terap);
				
				$_TMP_terap_farm_list = array();
				foreach($terap_farm_list as $key_paz=>$terapia) {
					foreach($terapia as $key_ter=>$item) {
						if(isset($item['skip']) && !$item['skip']) {
							unset($terapia[$key_ter]);
						}
					}

					if(!empty($terapia) && count($terapia) > 0) {				
						$_query = "SELECT CONCAT(u.Cognome, ' ', u.Nome) as utente, r.nome as reparto
									FROM utenti u
									LEFT JOIN reparti_pazienti rp ON rp.id_paziente = u.IdUtente
									LEFT JOIN reparti r ON r.id = rp.id_reparto
									WHERE idutente = $key_paz";
						$_rs = mssql_query($_query, $conn);
						
						$_row = mssql_fetch_assoc($_rs);					
						
						$_TMP_terap_farm_list[$key_paz] = array(
							"utente" => $_row['utente'],
							"reparto" => $_row['reparto'],
							"terapia" => $terapia
						);
						
						mssql_free_result($_rs);
					} else {
						unset($terap_farm_list[$key_paz]);
					}
				}
				
				$terap_farm_list = $_TMP_terap_farm_list;
				$time_farm_list = array();
				
				foreach($terap_farm_list as $key_paz=>$paziente) {
					foreach($paziente['terapia'] as $item) {
						foreach($item['orario'] as $orario) {
							$time_farm_list[$orario][] = array(
								"utente" => $paziente['utente'],
								"reparto" => $paziente['reparto'],
								"id_utente" => $key_paz,
								"farmaco" => $item['terap_farm_farmaco'],
								"dosaggio" => $item['dos'],
								"tip_somm" => $item['tip_somm'],
								"quantita" => $item['qnt']
							);
						}
					}
				}

				foreach($time_farm_list as $orario=>$utenti_list) {
					$_time = strtotime($orario);
					if($_time < $start_time || $_time > $end_time) {
						continue;
					}
					
					foreach($utenti_list as $item) {
						$lineData = array($orario, $item['utente'], $item['id_utente'], $item['reparto'], $item['farmaco'], $item['dosaggio'], $item['tip_somm'], $item['quantita']); 
						$excelData .= implode("\t", array_values($lineData)) . "\n"; 
					}
				}				
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
		
		case "rep_cart_usr_dimessi":
			$fileName = "Report Cartelle Aperte Pazienti Dimessi (" . date('Y-m-d H-i-s') . ").xls"; 
			$intestazione = array('ID CARTELLA', 'CODICE CARTELLA', 'NORMATIVA | REGIME', 'ID UTENTE', 'UTENTE'); 
			$excelData = implode("\t", array_values($intestazione)) . "\n"; 

			$query="SELECT DISTINCT uc.id ,uc.codice_cartella, CONCAT(norm.normativa, ' | ', reg.regime) as nome, uc.idutente, CONCAT(usr.Nome, ' ', usr.Cognome) AS utente
					FROM utenti_cartelle uc
					INNER JOIN regime reg on reg.idregime = uc.idregime
					INNER JOIN normativa norm on norm.idnormativa = reg.idnormativa
					INNER JOIN re_pazienti_impegnative rpi on rpi.idutente = uc.idutente
					INNER JOIN utenti usr on usr.IdUtente = uc.idutente
					WHERE usr.cancella = 'n'  AND uc.cancella = 'n'
						AND uc.data_chiusura IS NULL AND uc.idutente <> 605						
						AND rpi.idutente not in (
							select idutente
							from re_pazienti_impegnative rpi
							where DataDimissione IS NULL
						)
						$where";
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs) > 0){
 				while($row = mssql_fetch_assoc($rs)) 
				{
					$lineData = array($row['id'], $row['codice_cartella'], $row['nome'], $row['idutente'], $row['utente']);
					$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				}
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
	}

	// Headers for download 
	header("Content-Type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=\"$fileName\""); 
	 
	// Render excel data 
	echo $excelData;
	
} 
else echo "Nessun report specificato per l'esportazione.";