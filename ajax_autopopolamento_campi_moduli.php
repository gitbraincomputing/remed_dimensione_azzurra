<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');

$conn = db_connect();


// cerco l'ID dei campi dell'ultima versione del modulo (RTF da sostituire)
// in questo modo, aggiro il problema dell'incremento dell'ID dei campi ad ogni aggiornamento modulo ed incremento versione

if(isset($_POST['segnalibri_rtf']))			
{
	$segnalibriArr = explode('|', $_POST['segnalibri_rtf']);
	$idmoduloversione = $_POST['idmoduloversione'];
	
	for($i = 0; $i < count($segnalibriArr); $i++)
	{
		$query = "SELECT TOP (1) idcampo, idmoduloversione, etichetta, segnalibro FROM campi AS c 
			WHERE segnalibro = '$segnalibriArr[$i]' AND idmoduloversione = $idmoduloversione
			ORDER BY idmoduloversione DESC";	
		$rs = mssql_query($query, $conn);

		if(mssql_num_rows($rs) > 0) 
		{
			if($row = mssql_fetch_assoc($rs)){
				if($row['idcampo'] !== "") 
					 $response[$segnalibriArr[$i]] = $row['idcampo'];
				else $response[$segnalibriArr[$i]] = "-----+";
			}
		} else {
			$response[$segnalibriArr[$i]] = "------";
		}
	}
	echo json_encode($response);
}



// AUTOPOPOLAMENTO PER CAMPI IN "VALUTAZIONE RELAZIONI TERAPISTI (ex26)"

elseif(isset($_POST['id_utente']) && isset($_POST['id_cartella']) && isset($_POST['segnalibri']) && isset($_POST['idmoduloversione']) ) 
{
	$segnal_mod_valutazioni = explode('|', utf8_decode($_POST['segnalibri']));
	$response['sit_attuale'] = "";
	$response['obiettivi'] = "";
	$response['metodologie'] = "";
	
	for($i = 0; $i < count($segnal_mod_valutazioni); $i++)
	{
		$query_id_istanzatestata_e_campo = "SELECT TOP (1) it.id_istanza_testata, c.idcampo FROM campi as c
			INNER JOIN istanze_testata as it ON it.id_modulo_versione = c.idmoduloversione
				WHERE c.segnalibro = '".$segnal_mod_valutazioni[$i]."' AND it.id_cartella = ".$_POST['id_cartella'] ." ORDER BY it.id_istanza_testata DESC";


		$rs_id_istanzatestata_e_campo = mssql_query($query_id_istanzatestata_e_campo, $conn);
		if(mssql_num_rows($rs_id_istanzatestata_e_campo) > 0) {
			if($row_id_istanzatestata_e_campo = mssql_fetch_assoc($rs_id_istanzatestata_e_campo)) 
			{
				$id_istanza_testata = $row_id_istanzatestata_e_campo['id_istanza_testata'];
				$id_campo = $row_id_istanzatestata_e_campo['idcampo'];

				$query_valore = "SELECT valore FROM istanze_dettaglio WHERE id_istanza_testata = $id_istanza_testata AND idcampo = $id_campo";
				$rs_valore = mssql_query($query_valore, $conn);

				if(mssql_num_rows($rs_valore) > 0) {
					if($row_valore = mssql_fetch_assoc($rs_valore)) 
					{
						
						if ($row_valore['valore'] !== "")
						{
							if (strpos($segnal_mod_valutazioni[$i], 'situazione_attuale_') !== false) 
							{
									if ($segnal_mod_valutazioni[$i] == "situazione_attuale_fs")		$response['sit_attuale'] .= "DAL PROGRAMMA RIABILITATIVO 'FUNZIONI SENSOMOTORIE':\n";
								elseif ($segnal_mod_valutazioni[$i] == "situazione_attuale_cr")		$response['sit_attuale'] .= "DAL PROGRAMMA RIABILITATIVO 'COMUNICATIVO/RELAZIONALI':\n";
								$response['sit_attuale'] .= mb_convert_encoding($row_valore['valore'], "UTF-8") . "\n\r";
							}
							elseif (strpos($segnal_mod_valutazioni[$i], 'obiettivi_') !== false) 
							{
									if ($segnal_mod_valutazioni[$i] == "elenco_obiettivi_ajax_fs")		$response['obiettivi'] .= "DAL PROGRAMMA RIABILITATIVO 'FUNZIONI SENSOMOTORIE':\n";
								elseif ($segnal_mod_valutazioni[$i] == "elenco_obiettivi_ajax_cr")		$response['obiettivi'] .= "DAL PROGRAMMA RIABILITATIVO 'COMUNICATIVO/RELAZIONALI':\n";
								$response['obiettivi'] .= mb_convert_encoding($row_valore['valore'], "UTF-8") . "\n\r";
							}
							elseif (strpos($segnal_mod_valutazioni[$i], 'metodologie_operative_') !== false) 			// si tratta di una combo, quindi leggo l'etichetta (testo della option)
							{										
									if ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_fs")	{	$idcombo = 183;		$response['metodologie'] .= "DAL PROGRAMMA RIABILITATIVO 'FUNZIONI SENSOMOTORIE':\n";	}
								elseif ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_cr")	{	$idcombo = 184;		$response['metodologie'] .= "DAL PROGRAMMA RIABILITATIVO 'COMUNICATIVO/RELAZIONALI':\n";	}
								elseif ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_cc")		$idcombo = 185;
								elseif ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_aea")	$idcombo = 186;
								elseif ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_acp")	$idcombo = 187;
								elseif ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_ms")		$idcombo = 182;
								elseif ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_rrs")	 $idcombo = 188;
								elseif ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_pei_acp") $idcombo = 187;
								elseif ($segnal_mod_valutazioni[$i] == "metodologie_operative_ajax_pei_rrs") $idcombo = 188;
								
								$query_val_combo = "SELECT etichetta FROM campi_combo WHERE idcombo = $idcombo AND valore = " . $row_valore['valore'];
								$rs_val_combo = mssql_query($query_val_combo, $conn);
								if(mssql_num_rows($rs_val_combo) > 0) {
									if($row_val_combo = mssql_fetch_assoc($rs_val_combo)) 
									{
										$response['metodologie'] .= mb_convert_encoding($row_val_combo['etichetta'], "UTF-8") . "\n\r";
									}
								}
							}
							
							
						}
					}
				}
			}
		}
	

	}
	// rimuovo i tag per l'ultima nuova linea "\n\r" - sebbene siano 4 caratteri in totale, vengono convertiti in 2 spazi per cui tolgo 2 caratteri
	if (strlen($response['sit_attuale']) > 2)		$response['sit_attuale'] = substr($response['sit_attuale'], 0, -2);
	if (strlen($response['obiettivi']) > 2)			$response['obiettivi']   = substr($response['obiettivi'], 	0, -2);
	if (strlen($response['metodologie']) > 2)		$response['metodologie'] = substr($response['metodologie'], 0, -2);
	
	echo json_encode($response);
}


// RECUPERO TUTTI I VALORI PER IL MODULO 'Cartella Clinica (RD1) - Documento di sintesi'
elseif(isset($_POST['recupera_tutti_valori_terapia_farmacologica'])) 
{
	$cartelle = array();
	$query = "SELECT r.idregime, r.regime
				FROM utenti_cartelle as uc
				JOIN regime as r ON r.idregime = uc.idregime
				JOIN normativa as n ON n.idnormativa = uc.idnormativa
				WHERE uc.idutente = " . $_POST['id_utente'] . " 
					AND uc.id = $id_cartella
					AND uc.data_chiusura is null";
	$result = mssql_query($query, $conn);
	$row_cart = mssql_fetch_assoc($result);

	$regime = $row_cart['idregime'];
	$regime_inf = null;
	
	switch($regime) {
		// Regime ex Art. 26 L. 833/78
		case 26:
		case 27:
		case 28:
		case 48:
		case 50:
			$regime_inf = 52;
		break;
		
		// Regime L.R. 08/2003
		case 36:
			$regime_inf = 59;
		break;
		
		// Regime RD1 Estensiva
		case 51:
			$regime_inf = 59;
		break;
		// Regime T.P. RD1 estensiva
		case 56:
			$regime_inf = 60;
		break;
		
		// Regime T.P. RD1 intensntiva
		case 55:
			$regime_inf = 62;
		break;
		// Regime T.P. RD1 intensivo
		case 63:
			$regime_inf = 64;
		break;		
		// Regime T.P. Suap
		case 57:
			$regime_inf = 65;
		break;
				
		// Regime SUAP
		case 54:
			$regime_inf = 61;
		break;

		default:
		break;
	}
	
	unset($row_cart);
	
	$query = "SELECT TOP 1 uc.id, r.regime, n.normativa
				FROM utenti_cartelle as uc
				JOIN regime as r ON r.idregime = uc.idregime
				JOIN normativa as n ON n.idnormativa = uc.idnormativa
				WHERE uc.idutente = " . $_POST['id_utente'] . " 
					AND uc.idregime = $regime_inf 
					AND uc.data_chiusura is null
				ORDER BY id DESC";
	$result = mssql_query($query, $conn);
	$row_cart = mssql_fetch_assoc($result);

	$id_cartella_inf = $row_cart['id'];
	
	$_query = "SELECT it.id_istanza_testata, CASE WHEN it.id_modulo_versione >= 6143 THEN 1 ELSE 0 END as nuova_versione, c.segnalibro, c.etichetta, id.idcampo, TRIM(id.valore) Collate SQL_Latin1_General_CP1253_CI_AI as valore
				FROM istanze_dettaglio AS id
				INNER JOIN istanze_testata AS it ON it.id_istanza_testata = id.id_istanza_testata
				INNER JOIN campi AS c ON c.idcampo = id.idcampo
				WHERE it.id_cartella = $id_cartella_inf 
					AND it.id_modulo_padre = 205
					AND annullamento = 'n'
					AND c.segnalibro IS NOT NULL AND TRIM(c.segnalibro) <> ''
					AND id_inserimento = (SELECT MAX(id_inserimento) 
											FROM istanze_testata 
											WHERE id_cartella = $id_cartella_inf AND id_modulo_padre = 205 AND annullamento = 'n')";
	$_result = mssql_query($_query, $conn);
	
	$module_values = array();
	while ($_row = mssql_fetch_assoc($_result)) {
		$segnalibro = explode('_', $_row['segnalibro']);			
		$idx = array_pop($segnalibro);
		$segnalibro = implode('_', $segnalibro);
		
		$module_values[$idx][$segnalibro] = array(
												'idcampo' => $_row['idcampo'],
												'etichetta' => $_row['etichetta'],
												'valore' => empty($_row['valore']) ? '' : trim($_row['valore']),
												'nuova_versione' => $_row['nuova_versione']
											);
	}

	$idx_to_remove = array();
	
	foreach($module_values as $key=>$mdl_vl) {
		if(empty($mdl_vl['terap_farm_farmaco']['valore']) && empty($mdl_vl['farmaco']['valore'])) {
			$idx_to_remove[] = $key;
		}
	}

	foreach($module_values as $key=>$mdl_vl) {
		if(in_array($key, $idx_to_remove)) {
			unset($module_values[$key]);
		}
	}

	foreach($module_values as $mdl_vl) {
		foreach($mdl_vl as $segnalibro=>$value) {
			if((($segnalibro == 'qnt' && !$value['nuova_versione']) || $segnalibro == 'orario') && !empty($value['valore'])) {

				if( $segnalibro == 'orario') {	
					$arr_value = explode(';', $value['valore']);
					
					$filter = " IN (";
					foreach ($arr_value as $key) {
						$filter .= "'$key',";
					}
					
					$filter = rtrim($filter,',');
					$filter .= ")";
				} else {
					$filter = " = '" . $value['valore'] . "'";
				}
				
				$query_combo = "SELECT DISTINCT mc.idcampo, cc.etichetta
								FROM moduli_combo AS mc
								INNER JOIN campi_combo AS cc ON mc.idcombo = cc.idcombo
								WHERE mc.idmoduloversione = (SELECT id_modulo_versione
																FROM istanze_testata 
																WHERE id_cartella = $id_cartella_inf AND id_modulo_padre = 205 AND annullamento = 'n' 
																	AND id_inserimento = (
																	SELECT MAX(id_inserimento)
																		FROM istanze_testata 
																		WHERE id_cartella = $id_cartella_inf AND id_modulo_padre = 205 AND annullamento = 'n')
															)
									AND mc.idcampo = " . $value['idcampo'] . " 
									AND cc.valore $filter";

				$result_combo = mssql_query($query_combo, $conn);
				
				if($count == 1) {
					$row_combo = mssql_fetch_assoc($result_combo);
					$value['valore'] = $row_combo['etichetta'];
				} else {
					$_tmp_val = array();
					while($row_combo = mssql_fetch_assoc($result_combo)) {
						$_tmp_val[] = $row_combo['etichetta'];
					}
					
					$value['valore'] = implode(', ', $_tmp_val);
				}
			}

			if(!empty($value['valore']) && trim($value['valore']) != '') {					
				$response[$row_cart['regime'] . '(' . $row_cart['normativa'] . ')'][] = array(
					'etichetta' => $value['etichetta'],
					'valore' => $value['valore']
				);
			}
		}		
	}

	echo json_encode($response);	
}


// AUTOPOPOLAMENTO PER CAMPI IN "ANAMNESI (ex26)" 
elseif(isset($_POST['id_utente']) && isset($_POST['id_cartella']))
{
	$id_utente = $_POST['id_utente'];
	$id_cartella = $_POST['id_cartella'];
	
	$query1 = "SELECT idregime FROM utenti_cartelle where id = $id_cartella ";
	$rs1 = mssql_query($query1, $conn);
	$row1 = mssql_fetch_assoc($rs1);
	$id_regime = $row1['idregime'];
	
	
	$query2= "SELECT MAX(idimpegnativa) as id_impegnativa from impegnative WHERE idutente = $id_utente and RegimeAssistenza = $id_regime";
	$rs2 = mssql_query($query2, $conn);
	$row2 = mssql_fetch_assoc($rs2);
	$id_impegnativa = $row2['id_impegnativa'];
	
	
	$query3 = "SELECT prescrittori.NominativoPrescrittore as NominativoPrescrittore FROM impegnative
		INNER JOIN prescrittori ON impegnative.MedicoPrescrittore = prescrittori.IdPrescrittore
		WHERE impegnative.idimpegnativa = " . $id_impegnativa;
	$rs3 = mssql_query($query3, $conn);
	$row3 = mssql_fetch_assoc($rs3);
	$medico_prescrittore = $row3['NominativoPrescrittore'];	
	
	//$query = "SELECT ICD9RIA, ICDXRIA, classemenomazione FROM impegnative WHERE idutente = $id_utente AND id_impegnativa = $id_impegnativa";
	
	//$query = "SELECT imp.ICD9RIA, imp.ICDXRIA, men.codice FROM impegnative AS imp
	//			JOIN menomazioni AS men ON men.idmen = imp.classemenomazione
	//			WHERE imp.idutente = $id_utente ";
	
	//$query = "SELECT ICD9RIA, ICDXRIA, icidh_menom, icf_fs, icidh_d, icf_ap, scala_disabilita, scala_prognosi FROM impegnative WHERE imp.idutente = $id_utente ";  i campi ICF diventano manuali e non + automatici
	$query4 = "SELECT ICD9RIA, ICDXRIA FROM impegnative WHERE idutente = $id_utente AND idimpegnativa = $id_impegnativa";
	$rs4 = mssql_query($query4, $conn);
	if(mssql_num_rows($rs4) > 0) 
	{
		if($row4 = mssql_fetch_assoc($rs4)){
			$response['icd9']  		= $row4['ICD9RIA'];
			$response['icd10'] 		= $row4['ICDXRIA'];
			$response['resp_progetto'] = $medico_prescrittore;
			//$response['icidh_menom']= $row['icidh_menom'];
			//$response['icidh_d'] 	= $row['icidh_d'];
			//$response['scala_disabilita'] = $row['scala_disabilita'];
			//$response['scala_prognosi']   = $row['scala_prognosi'];
		}
		echo json_encode($response);
	}
}



// AUTOPOPOLAMENTO PER CAMPI IN "PROGRAMMA RIABILITATIVO (ex26)"
elseif(isset($_POST['metodologia']) && isset($_POST['area_funzionale']))
{
	$metodologia 	 = str_replace("\'", "''", $_POST['metodologia']);		// escape per MS SQL
	$area_funzionale = $_POST['area_funzionale'];
	
	$query = "SELECT misure_desito_applicate, elenco_obiettivi FROM aree_funzionali WHERE metodologia = '$metodologia' AND area_funzionale = '$area_funzionale'";	
	$rs = mssql_query($query, $conn);
	if(mssql_num_rows($rs) > 0) 
	{
		if($row = mssql_fetch_assoc($rs)){
			$response['misure_desito'] 		= mb_convert_encoding($row['misure_desito_applicate'], "UTF-8");	// conversione per lettere accentate,
			$response['elenco_obiettivi'] 	= mb_convert_encoding($row['elenco_obiettivi'], "UTF-8");			// senza di essa il JSON ritorna NULL
		}
		
		echo json_encode($response);
	}

}


	// AUTOPOPOLAMENTO PER CAMPO "RISULTATO" DEL "PROGRAMMA RIABILITATIVO (ex26)" IN CASO DI MODIFICA, SE E SOLO SE:
	// - IL MODULO HA SUO IL CAMPO RISULTATO VUOTO
	// - IL MODULO HA L'ALLEGATO 1 CARICATO
	// - IL MODULO "VALUTAZIONI RELAZIONI" A LUI COLLEGATO E' STATO COMPILATO
elseif(isset($_POST['segnalibro_risult_mod_valutaz']) && isset($_POST['idmoduloversione']) && isset($_POST['idcartella']) && isset($_POST['idinserimento']))
{
	$idmoduloversione = $_POST['idmoduloversione'];								// del modulo "Programma Riabilitativo" che si sta modificando
	$segnalibriArr = explode('|', $_POST['segnalibro_risult_mod_valutaz']);		// del modulo "Valutazione Relazioni" collegato al modulo di cui sopra
	$response['risultati_mod_valutaz'] = "";

	$query_prog_riab = "SELECT id_istanza_testata, allegato, allegato2 FROM istanze_testata WHERE id_modulo_versione = $idmoduloversione AND id_cartella = ".$_POST['idcartella']." AND id_inserimento = ".$_POST['idinserimento'];	
	//echo $query_prog_riab . "<br><br>";
	$rs_prog_riab = mssql_query($query_prog_riab, $conn);
	if($row_prog_riab = mssql_fetch_assoc($rs_prog_riab))
	{
		if ($row_prog_riab['allegato'] !== NULL)			// prendo il valore del campo "Risultato" del modulo "Valutazione Relazioni" solo se allegato1 è caricato al "Programma Riabilitativo" e il modulo "Valutazione Relazioni" collegato è stato compilato
		{	
			$response['allegato_caricato'] = "si";
			
			for($i = 0; $i < count($segnalibriArr); $i++)
			{
				$query_id_istanzatestata_e_campo = "SELECT TOP (1) it.id_istanza_testata, c.idcampo FROM campi as c
														INNER JOIN istanze_testata as it ON it.id_modulo_versione = c.idmoduloversione
														WHERE c.segnalibro = '" . $segnalibriArr[$i] . "' AND it.id_cartella = " . $_POST['idcartella'] ." 
														ORDER BY it.id_istanza_testata DESC";
				//echo $query_id_istanzatestata_e_campo . "<br><br>";
				$rs_id_istanzatestata_e_campo = mssql_query($query_id_istanzatestata_e_campo, $conn);
				if(mssql_num_rows($rs_id_istanzatestata_e_campo) > 0) 
				{
					$row_id_istanzatestata_e_campo = mssql_fetch_assoc($rs_id_istanzatestata_e_campo);
				
					$query_valore = "SELECT valore FROM istanze_dettaglio WHERE idcampo = " .$row_id_istanzatestata_e_campo['idcampo']." AND id_istanza_testata = " .$row_id_istanzatestata_e_campo['id_istanza_testata'];
					//echo $query_valore . "<br><br>";
					$rs_valore = mssql_query($query_valore, $conn);
					if(mssql_num_rows($rs_valore) > 0) 
					{
						if($row_valore = mssql_fetch_assoc($rs_valore))
						{
								if ($segnalibriArr[$i] == "valutazione_risultati_ajax_psicot_individuale")	$response['risultati_mod_valutaz'] .= "DAL MODULO \"VALUTAZIONI/RELAZIONI PSICOTERAPEUTICHE INDIVIDUALI\":\n\r" . mb_convert_encoding($row_valore['valore'], "UTF-8") . "\n\r";
							elseif ($segnalibriArr[$i] == "valutazione_risultati_ajax_psicot_familiare")	$response['risultati_mod_valutaz'] .= "DAL MODULO \"VALUTAZIONI/RELAZIONI PSICOTERAPEUTICHE FAMILIARI\":\n\r" 	. mb_convert_encoding($row_valore['valore'], "UTF-8") . "\n\r";
							else $response['risultati_mod_valutaz'] .= mb_convert_encoding($row_valore['valore'], "UTF-8") . "\n\r";
						}
					}
				}
			}
		}
		else $response['allegato_caricato'] = "no";
		
		if (strlen($response['risultati_mod_valutaz']) > 2)			$response['risultati_mod_valutaz'] = substr($response['risultati_mod_valutaz'], 	0, -2);
		echo json_encode($response);
	}

}


// AUTOPOPOLAMENTO PER IL CAMPO "SITUAZIONE ATTUALE" NEI "PROGRAMMA RIABILITATIVO (ex26)"
// prendo il valore del campo del modulo "Visita Specialistica" ad esso collegato se, e solo se, l'ultima istanza di tale modulo 
// sia stata compilata nei 20 giorni precedenti alla data di compilazione del modulo "Programma Riabilitativo"
elseif(isset($_POST['id_cartella']) && isset($_POST['segnalibro_mod_visita_specialis']))
{
	$id_cartella = $_POST['id_cartella'];
	$segnalibro = $_POST['segnalibro_mod_visita_specialis'];
	
  //$query = "SELECT C.idcampo, C.idmoduloversione, M.idmodulo, IT.datains, IT.id_istanza_testata, ID.valore FROM campi as C
	$query = "SELECT ID.valore FROM campi as C
				JOIN moduli as M on C.idmoduloversione = M.id
				JOIN istanze_testata as IT on IT.id_modulo_padre = M.idmodulo
				JOIN istanze_dettaglio as ID on ID.id_istanza_testata = IT.id_istanza_testata
				where C.segnalibro = '$segnalibro' 
				AND IT.id_modulo_padre = M.idmodulo 
				AND IT.id_cartella = $id_cartella 
				AND IT.datains > DATEADD(dd, - 20, GETDATE()) 
				AND ID.idcampo = C.idcampo
				ORDER BY C.idcampo DESC";
	$rs = mssql_query($query, $conn);
	if($row = mssql_fetch_assoc($rs)) {
		$response_val_vis_spec['valore_visita_specialistica'] = mb_convert_encoding($row['valore'], "UTF-8");
		echo json_encode($response_val_vis_spec);
	}
	
}




//######################
// REGIME RESIDENZIALE #
//######################

// AUTOPOPOLAMENTO PER CAMPI IN "PROGRAMMA RIABILITATIVO (ex26)"
// AUTOPOPOLAMENTO PER IL CAMPO "SITUAZIONE ATTUALE" NEI "PROGRAMMA RIABILITATIVO (ex26)"
// prendo il valore del campo del modulo "Visita Specialistica" ad esso collegato se, e solo se, l'ultima istanza di tale modulo 
// sia stata compilata nei 20 giorni precedenti alla data di compilazione del modulo "Programma Riabilitativo"
elseif(isset($_POST['id_cartella']) && isset($_POST['segnalibro_campo_esiti']))
{
	$id_cartella = $_POST['id_cartella'];
	$segnalibro = $_POST['segnalibro_campo_esiti'];
	
// prendo il campo esito dal moduli valutazioni/osservazioni
	$query = "SELECT ID.valore FROM campi as C
				JOIN moduli as M on C.idmoduloversione = M.id
				JOIN istanze_testata as IT on IT.id_modulo_padre = M.idmodulo
				JOIN istanze_dettaglio as ID on ID.id_istanza_testata = IT.id_istanza_testata
				where C.segnalibro = '$segnalibro' 
				AND IT.id_modulo_padre = M.idmodulo 
				AND IT.id_cartella = $id_cartella 
				AND IT.datains > DATEADD(dd, - 30, GETDATE()) 
				AND ID.idcampo = C.idcampo
				ORDER BY IT.id_istanza_testata DESC";	// così prendo il valore dell'ultima istanza compilata
				//ORDER BY C.idcampo DESC";
	$rs = mssql_query($query, $conn);
	if($row = mssql_fetch_assoc($rs)) {
		$response['esiti_valutazione'] = mb_convert_encoding($row['valore'], "UTF-8");
		echo json_encode($response);
	}
}




//######################
//  REGIME EX ART. 44  #
//######################
elseif(isset($_POST['id_cartella']) && isset($_POST['segnalibri_da_cercare']))
{
	$id_cartella = $_POST['id_cartella'];
	$segnalibriArr = explode('|', $_POST['segnalibri_da_cercare']);

	for($i=0; $i < count($segnalibriArr); $i++)
	{
	
	// prendo il campo esito dal moduli valutazioni/osservazioni
		$query = "SELECT ID.valore FROM campi as C
					JOIN moduli as M on C.idmoduloversione = M.id
					JOIN istanze_testata as IT on IT.id_modulo_padre = M.idmodulo
					JOIN istanze_dettaglio as ID on ID.id_istanza_testata = IT.id_istanza_testata
					where C.segnalibro = '$segnalibriArr[$i]' 
					AND IT.id_modulo_padre = M.idmodulo
					AND IT.id_cartella = $id_cartella 
					AND ID.idcampo = C.idcampo
					ORDER BY C.idcampo DESC";

		$rs = mssql_query($query, $conn);
		if($row = mssql_fetch_assoc($rs)) 
		{
			if(mssql_num_rows($rs) > 0)	$valore  = mb_convert_encoding($row['valore'], "UTF-8");
			else 						$valore  = "";
			
			mssql_free_result($rs);

			$response[$segnalibriArr[$i]] = $valore;
		}
	}
	echo json_encode($response);
}



	
// prendo il campo diagnosi dall'impegnativa
elseif(isset($_POST['id_cartella']) && isset($_POST['prendi_diagnosi']))
{
	$id_cartella = $_POST['id_cartella'];
	$query = "SELECT diagnosi FROM impegnative AS i
				JOIN utenti_cartelle AS uc ON uc.id = $id_cartella 
				WHERE i.RegimeAssistenza = uc.idregime AND i.Normativa = uc.idnormativa AND i.idutente = uc.idutente";
	$rs = mssql_query($query, $conn);
	if($row = mssql_fetch_assoc($rs)) {
		$response['diagnosi_ingresso'] = mb_convert_encoding($row['diagnosi'], "UTF-8");
		echo json_encode($response);
	}
}	


// AUTOPOPOLAMENTO SELECT CON I MODULI PER CUI E' POSSIBILE ESERCITARE IL DIRITTO DI OSCURAMENTO AL FSE
elseif(isset($_POST['id_cartella_fse'])) 
{
	$response[] = "";
	$i=0;

	$queryModuliOscuramento = "SELECT * FROM re_istanze_moduli 
								WHERE id_cartella = " . $_POST['id_cartella_fse'] . " AND 
									  id_modulo_padre IN (148,147,146,132,138,197)";
	
	$result_MO= mssql_query($queryModuliOscuramento, $conn);
	$tot_righe_trovate = mssql_num_rows($result_MO);
	if($tot_righe_trovate > 0)
	{

		while($row_MO = mssql_fetch_assoc($result_MO)) 
		{

			$nome = trim($row_MO['nome_modulo']);
			// elimino le parte non necessarie dal nome del modulo
				if ((strpos($nome, "Cartella Clinica - ")) 				  	!== false)		$nome = substr($nome, strlen('Cartella Clinica - '));
			elseif ((strpos($nome, "Cart. Clinica ex26 - Sezione ")) 	  	!== false)		$nome = substr($nome, strlen('Cart. Clinica ex26 - Sezione ') + 4);
			elseif ((strpos($nome, "Cartella Clinica (ex26) - ")) 		  	!== false)		$nome = substr($nome, strlen('Cartella Clinica (ex26) - '));
			elseif ((strpos($nome, "Cartella Clinica (ex26 e Legge8) - ")) 	!== false)		$nome = substr($nome, strlen('Cartella Clinica (ex26 e Legge8) - '));
			elseif ((strpos($nome, "Cartella Clinica (Legge8) - ")) 		!== false)		$nome = substr($nome, strlen('Cartella Clinica (Legge8) - '));
			elseif ((strpos($nome, "Cart. Clinica legge8 - ")) 			  	!== false)		$nome = substr($nome, strlen('Cart. Clinica legge8 - '));
			elseif ((strpos($nome, "Cartella Clinica (ex art 44) - ")) 		!== false)		$nome = substr($nome, strlen('Cartella Clinica (ex art 44) - '));
			elseif ((strpos($nome, "Cartella Clinica (SUAP) - ")) 			!== false)		$nome = substr($nome, strlen('Cartella Clinica (SUAP) - '));
			elseif ((strpos($nome, "Cartella Infermieristica - ")) 			!== false)		$nome = substr($nome, strlen('Cartella Infermieristica - '));
		
			$response[$i]['nome'] = trim($nome);
			if($row_MO['data_osservazione'] !== NULL) {
				$response[$i]['data'] = date('d-m-Y', strtotime($row_MO['data_osservazione']));
				$response[$i]['data_usa'] = date('Y-m-d', strtotime($row_MO['data_osservazione'])) . "|dataoss";
			} else {
				$response[$i]['data'] = date('d-m-Y', strtotime($row_MO['datains']));
				$response[$i]['data_usa'] = date('Y-m-d', strtotime($row_MO['datains'])) . "|datains";
			}
			$response[$i]['id_modulo_padre'] = $row_MO['id_modulo_padre'];
			$response[$i]['id_modulo_versione'] = $row_MO['id_modulo_versione'];
			
			if(isset($_POST['idmoduloversione']) && isset($_POST['idinserimento']) ) {	 // li passo solo in modalità modifica modulo
				$response[$i]['checked'] 			= controlla_flag($conn, $_POST['id_cartella_fse'], $_POST['idmoduloversione'], $_POST['idinserimento'], $nome, $response[$i]['data'] );
				$response[($i-1)]['checked_tutti']  = controlla_flag($conn, $_POST['id_cartella_fse'], $_POST['idmoduloversione'], $_POST['idinserimento'], $nome, 'tutti' );
			}
			
		
			$i++;
			
			if($i == $tot_righe_trovate) {
 				if ($trovato_doppione == 1) {
					$response[($i-1)]['option_tutti'] = 's';
					$trovato_doppione = 0;
				}
			}

		}
		
		echo trim(json_encode($response));
	} else echo "0";
}


// RECUPERO VALORI PER IL MODULO 'Cartella Infermieristica - Monitoraggio temperature'
elseif(isset($_POST['name_campi_giorni_temp'])) 
{
	$arr_nome_campi = json_decode($_POST['name_campi_giorni_temp'], true);

	foreach($arr_nome_campi as $nome_campo) {
		$query = "SELECT c.etichetta, c.valore FROM istanze_dettaglio AS id
					JOIN istanze_testata AS it ON it.id_cartella = ".$_POST['id_cartella'] ." AND it.id_modulo_versione = ".$_POST['idmoduloversione'] ." AND it.id_inserimento = ".$_POST['idinserimento'] ."
					JOIN moduli_combo As m ON m.idcampo = id.idcampo
					JOIN campi_combo AS c ON c.idcombo = m.idcombo AND c.valore = id.valore
					WHERE id.id_istanza_testata = it.id_istanza_testata
					AND id.idcampo = '$nome_campo'";		
		//echo $query . "<br>";
		$result = mssql_query($query, $conn);
		$row = mssql_fetch_assoc($result); 
		
		if(empty($row['etichetta'])) {
			 $response[$nome_campo] = "";
		} else {
			$response[$nome_campo] = array(
					'etichetta' => $row['etichetta'],
					'valore' => $row['valore']
				);
		}
		
	}
	echo json_encode($response);	
}


// RECUPERO VALORI PER IL MODULO 'Cartella Infermieristica - Monitoraggio parametri'
elseif(isset($_POST['name_campi_mon_par'])) 
{
	$arr_nome_campi = json_decode($_POST['name_campi_mon_par'], true);

	foreach($arr_nome_campi as $nome_campo) {		
		$query = "SELECT c.etichetta, c.valore 
					FROM istanze_dettaglio AS id
						JOIN istanze_testata AS it ON it.id_cartella = ".$_POST['id_cartella'] ." AND it.id_modulo_versione = ".$_POST['idmoduloversione'] ." AND it.id_inserimento = ".$_POST['idinserimento'] ."
						JOIN moduli_combo As m ON m.idcampo = id.idcampo
						JOIN campi_combo AS c ON c.idcombo = m.idcombo AND c.valore = id.valore
					WHERE id.id_istanza_testata = it.id_istanza_testata
						AND id.idcampo = '$nome_campo'";		
		//echo $query . "<br>";
		$result = mssql_query($query, $conn);
		$row = mssql_fetch_assoc($result); 
		
		if(empty($row)) {
			$query = "SELECT id.valore 
						FROM istanze_dettaglio AS id
							JOIN istanze_testata AS it ON id.id_istanza_testata = it.id_istanza_testata
						WHERE it.id_cartella = ".$_POST['id_cartella'] ." 
							AND it.id_modulo_versione = ".$_POST['idmoduloversione'] ." 
							AND it.id_inserimento = ".$_POST['idinserimento'] ." AND id.idcampo = '$nome_campo'";		
			//echo $query . "<br>";
			$result = mssql_query($query, $conn);
			$row = mssql_fetch_assoc($result); 
			
			if(!empty($row)) {
				$response[$nome_campo] = array(
					'valore' => $row['valore']
				);	
			} else {
				$response[$nome_campo] = "";
			}
		} else {
			$response[$nome_campo] = array(
					'etichetta' => $row['etichetta'],
					'valore' => $row['valore']
				);
		}
		
	}
	echo json_encode($response);	
}


// RECUPERO VALORI PER IL MODULO 'Cartella Infermieristica - Bilancio Idrico'
elseif(isset($_POST['name_campi_bil_idr'])) 
{
	$arr_nome_campi = json_decode($_POST['name_campi_bil_idr'], true);

	foreach($arr_nome_campi as $nome_campo) {
		$query = "SELECT c.etichetta, c.valore 
					FROM istanze_dettaglio AS id
						JOIN istanze_testata AS it ON it.id_cartella = ".$_POST['id_cartella'] ." AND it.id_modulo_versione = ".$_POST['idmoduloversione'] ." AND it.id_inserimento = ".$_POST['idinserimento'] ."
						JOIN moduli_combo As m ON m.idcampo = id.idcampo
						JOIN campi_combo AS c ON c.idcombo = m.idcombo AND c.valore = id.valore
					WHERE id.id_istanza_testata = it.id_istanza_testata
						AND id.idcampo = '$nome_campo'";		
		//echo $query . "<br>";
		$result = mssql_query($query, $conn);
		$row = mssql_fetch_assoc($result); 
		
		if(empty($row)) {
			$query = "SELECT id.valore 
						FROM istanze_dettaglio AS id
							JOIN istanze_testata AS it ON id.id_istanza_testata = it.id_istanza_testata
						WHERE it.id_cartella = ".$_POST['id_cartella'] ." 
							AND it.id_modulo_versione = ".$_POST['idmoduloversione'] ." 
							AND it.id_inserimento = ".$_POST['idinserimento'] ." AND id.idcampo = '$nome_campo'";		
			$result = mssql_query($query, $conn);
			$row = mssql_fetch_assoc($result); 
			
			if(!empty($row)) {
				$response[$nome_campo] = array(
					'valore' => $row['valore']
				);	
			} else {
				$response[$nome_campo] = "";
			}
		} else {
			$response[$nome_campo] = array(
					'etichetta' => $row['etichetta'],
					'valore' => $row['valore']
				);
		}
		
	}
	echo json_encode($response);	
}

// RECUPERO VALORI DEL FARMACO PER IL MODULO 'Cartella Infermieristica - Somministrazione farmacologica'
elseif(isset($_POST['recupera_valore_terapia_farmacologica'])) 
{
	$query = "SELECT id.idcampo, id.valore
				FROM istanze_dettaglio AS id
				INNER JOIN istanze_testata AS it ON it.id_istanza_testata = id.id_istanza_testata
				INNER JOIN campi AS c ON c.idcampo = id.idcampo
				WHERE it.id_cartella = ". $_POST['id_cartella'] . "
					AND it.id_modulo_padre = 205
					AND annullamento = 'n'
					AND (c.segnalibro like 'farmaco_%' or c.segnalibro = 'terap_farm_farmaco_1')
					AND id_inserimento = (SELECT MAX(id_inserimento) 
											FROM istanze_testata 
											WHERE id_cartella = ". $_POST['id_cartella'] . " AND id_modulo_padre = 205 AND annullamento = 'n' )";

	$result = mssql_query($query, $conn);
	$str_values = '';
	$terap_values = array();
	
	while ($row = mssql_fetch_assoc($result)) {	
		if(!empty($row['valore']) && trim($row['valore']) != '') {
			$str_values .= "'" . trim($row['valore']) . "',";
			$terap_values[] = trim($row['valore']);
		}
	}
	
	$str_values = substr($str_values, 0, -1);
	
	$query_val = "SELECT valore, etichetta
					FROM campi_combo
					WHERE idcombo = 249 AND stato = 1 AND cancella = 'n' AND etichetta in ($str_values)
					ORDER BY idcampo ASC";

	$result_val = mssql_query($query_val, $conn);

	while ($row_val = mssql_fetch_assoc($result_val)) {
		$response_tmp[$row_val['valore']] = trim($row_val['etichetta']);
	}

	// SBO 31/07/23: ho dovuto annidare in un ulteriore array il valore e l'etichetta in quanto il browser modificava in base
	//		al valore della chiave dell'oggetto (il campo 'valore') l'ordinamento degli elementi. Questo comportava che
	//		l'elemento con chiave 17 in posizione 2 finiva (se avente valore più grande) in fondo alla lista o comunque
	//		veniva ordinato in modo ascendente
	// SBO 06/09/23: inserito strtolower per evitare che non escano farmaci inseriti con stesso nome ma differenti per minuscole
	//		e/o maiuscole
	foreach($terap_values as $ter_val) {
		foreach($response_tmp as $val=>$etic){
			if(strtolower($ter_val) == strtolower($etic)) {
				$response[] = array(
					'valore' => $val,
					'etichetta' => $etic
				);
				
				break;
			}
		}
	}

	echo json_encode($response);
}

// RECUPERO FARMACI DALL'ANAGRAFICA DELLA STRUTTURA E DEL PAZIENTE ANDANDO A GESTIRE GRAFICAMENTE ANCHE SCADENZE, 
//		GIACENZE MINIME ED ESAURIMENTI
elseif(isset($_POST['recupera_farmaci_anagrafica'])) 
{
	$response = array();
	$removed_farm = array();
	$farmaci_terapia = array();
	$farmaci_trovati = array();
	
	// se sono in modifica recupero tutti i farmaci inseriti nell'ultima istanza
	if(!empty($_POST['edit'])) {
		// RECUPERO LE INFORMAZIONI SU TUTTI I FARMACI NON PIU DISPONIBILI MA PRESENTI IN SOMMINISTRAZIONE
		$_query_log = "SELECT DISTINCT id_farmaco
						FROM farmaci_log
						WHERE id_modulo_versione = " . $_POST['id_modulo_versione'] . "
							AND id_paziente = " . $_POST['id_paziente'] ."
							AND progressivo_modulo = (
									SELECT MAX(progressivo_modulo)
									FROM farmaci_log
									WHERE id_modulo_versione = " . $_POST['id_modulo_versione'] . "
										AND id_paziente = " . $_POST['id_paziente'] . "
									GROUP BY id_modulo_versione, id_paziente
								)
							AND id_farmaco NOT IN (
									SELECT DISTINCT far.id
									FROM farmaci far
									WHERE far.status = 1 AND far.deleted = 0
										AND far.quantita_attuale IS NOT NULL AND far.quantita_attuale > 0 
										AND far.id_paziente = ".$_POST['id_paziente']." 
								)
							AND ( id_farmaco IN ( SELECT id FROM farmaci  WHERE struttura = 1 )
								OR id_farmaco IN ( SELECT id FROM farmaci WHERE  id_paziente != " . $_POST['id_paziente'] . " )
							)";
		$_far_log_rs = mssql_query($_query_log, $conn);
		
		while ($row = mssql_fetch_assoc($_far_log_rs)) {
			$removed_farm[] = $row['id_farmaco'];
		}
		
		mssql_free_result($_far_log_rs);
		
		// RECUPERO I VALORI INSERITI IN GENERAZIONE DELLA SOMMINISTRAZIONE		
		$_query = "SELECT id.idcampo, cmp.segnalibro, TRIM(id.valore) as valore
					FROM istanze_testata it
					INNER JOIN istanze_dettaglio id ON it.id_istanza_testata = id.id_istanza_testata
					LEFT JOIN campi cmp ON cmp.idcampo = id.idcampo
					WHERE it.id_modulo_versione = " . $_POST['id_modulo_versione'] . " 
						AND it.id_paziente = '" . $_POST['id_paziente'] . "'
						AND it.id_cartella = " . $_POST['id_cartella'] ."
						AND it.id_inserimento = " . $_POST['id_inserimento'] . " 
						AND id.valore IS NOT NULL AND id.valore != '' ";
		$_rs = mssql_query($_query, $conn);
		
		while ($row = mssql_fetch_assoc($_rs)) {
			$response['values'][] = array(
				'segnalibro' => $row['segnalibro'],
				'idcampo' => $row['idcampo'],
				'valore' => $row['valore']
			);
		}
		
		mssql_free_result($_rs);
		
		// SBO 22/12/23: GESTIONE CON CONNESSIONE ALLA TERAPIA
		/*
		$_query = "SELECT DISTINCT cmp.segnalibro, TRIM(id.valore) as valore
					FROM istanze_dettaglio id
					LEFT JOIN campi cmp ON cmp.idcampo = id.idcampo
					WHERE id.id_istanza_testata = (
							SELECT TOP 1 id_istanza_testata
							FROM istanze_testata
							WHERE id_modulo_padre = 205 
								AND id_paziente = '" . $_POST['id_paziente'] . "'
								AND id_cartella = " . $_POST['id_cartella'] ."
							ORDER BY id_istanza_testata DESC
						)
						AND id.valore IS NOT NULL AND id.valore <> ''
						AND (cmp.segnalibro like 'terap_farm_farmaco_%' or cmp.segnalibro like 'dos_%')";	
		$_rs = mssql_query($_query, $conn);

		while ($row = mssql_fetch_assoc($_rs)) {
			$idx = explode('_', $row['segnalibro']);
			$idx = $idx[count($idx)-1];
			
			if (strpos($row['segnalibro'],'terap_farm_farmaco_') !== false) {
				$farmaci_terapia[$idx]['nome'] = $row['valore'];
			} elseif (strpos($row['segnalibro'],'dos_') !== false) {
				$farmaci_terapia[$idx]['dosaggio'] = $row['valore'];
			}
		}

		mssql_free_result($_rs);
		*/

		// SBO 22/12/23: GESTIONE CON CONNESSIONE ALLA TERAPIA
		/* recupero il farmaco per nome e dosaggio in quanto nel modulo Foglio terapia farmacologica non abbiamo gli id dei
		*	farmaci e potrei averne con lo stesso nome ma con un dosaggio differente
		*/
		/*
		$farm_id_count = array();
		
		foreach($farmaci_terapia as $key=>$farm) {			
			$query = "SELECT DISTINCT far.id, far.nome, far.dosaggio, far.lotto, far.struttura, far.quantita_attuale, 
							far.giacenza_minima, far.data_scadenza, far.id_paziente
						FROM farmaci far
						WHERE far.status = 1 AND far.deleted = 0
							AND far.quantita_attuale IS NOT NULL
							AND far.nome = '" . $farm['nome'] . "' 
							AND (far.dosaggio IS NULL OR far.dosaggio = " . str_replace(',','.',$farm['dosaggio']) . ")
							AND far.id_paziente = ".$_POST['id_paziente'];
			
			$result = mssql_query($query, $conn);

			if(mssql_num_rows($result) > 0) {
				while($row = mssql_fetch_assoc($result)) {
					$farm_id_count[$row['id']] += 1;
					$row['terap_idx'] = $key;
					$farmaci_trovati[] = $row;
				}
			}
		}
		*/

		// SBO 22/12/23: LA CLIENTE HA CHIESTO DI SLEGARE LA TERAPIA FARMACOLOGICA DALLLA SOMMINISTRAZIONE
		$query = "SELECT far.id, far.nome, far.dosaggio, far.lotto, far.struttura, far.quantita_attuale, 
						far.giacenza_minima, far.data_scadenza, far.id_paziente
					FROM farmaci far
					WHERE far.status = 1 AND far.deleted = 0 AND far.stato_farmaco IN (2, 3)
						AND far.quantita_attuale IS NOT NULL
						AND far.id_paziente = ".$_POST['id_paziente'];
		$result = mssql_query($query, $conn);

		if(mssql_num_rows($result) > 0) {
			while($row = mssql_fetch_assoc($result)) {
				//$farm_id_count[$row['id']] += 1;
				//$row['terap_idx'] = $key;
				$farmaci_trovati[] = $row;
			}
		}

		mssql_free_result($result);
		
		$_farm_ids_current_somm = array();
		
		foreach($response['values'] as $resp_elem) {
			if (strpos($resp_elem['segnalibro'],'farmaco_') !== false) {
				$_farm_ids_current_somm[] = $resp_elem['valore'];
			}
		}

		foreach($farmaci_trovati as &$farm) {
			if($farm['quantita_attuale'] == 0 && !in_array($farm['id'], $_farm_ids_current_somm)){
				$farm['skip'] = true;
			} elseif(date('Y-m-d', strtotime($farm['data_scadenza'])) < date('Y-m-d', strtotime('now')) && !in_array($farm['id'], $_farm_ids_current_somm)){
				$farm['skip'] = true;
			} else {
				$farm['skip'] = false;
			}
		}
		unset($farm);
		
		foreach($farmaci_trovati as $key=>$farm) {
			if($farm['skip']) {
				unset($farmaci_trovati[$key]);
			}
		}

		foreach($removed_farm as $farm) {
			$query = "SELECT DISTINCT far.id, far.nome, far.dosaggio, far.lotto, far.struttura, far.quantita_attuale, 
							far.giacenza_minima, far.data_scadenza, far.id_paziente
						FROM farmaci far
						WHERE far.id = $farm";
			$result = mssql_query($query, $conn);

			if(mssql_num_rows($result) > 0) {
				$row = mssql_fetch_assoc($result);
				$farmaci_trovati[] = $row;
			}
		}
	} else {
		// SBO 22/12/23: GESTIONE CON CONNESSIONE ALLA TERAPIA
		/* se è una nuova istanza vado a recuperare, nomi dei farmaci e rispettivi dosaggi, dall'ultima istanza del modulo 
		*	Foglio terapia farmacologica
		*/
		/*
		$_query = "SELECT DISTINCT cmp.segnalibro, TRIM(id.valore) as valore
					FROM istanze_dettaglio id
					LEFT JOIN campi cmp ON cmp.idcampo = id.idcampo
					WHERE id.id_istanza_testata = (
							SELECT TOP 1 id_istanza_testata
							FROM istanze_testata
							WHERE id_modulo_padre = 205 
								AND id_paziente = '" . $_POST['id_paziente'] . "'
								AND id_cartella = " . $_POST['id_cartella'] ."
							ORDER BY id_istanza_testata DESC
						)
						AND (cmp.segnalibro like 'terap_farm_farmaco_%' or cmp.segnalibro like 'dos_%'
								or cmp.segnalibro like 'dt_inizio_%')";
		$_rs = mssql_query($_query, $conn);

		$today_time = strtotime(date('Y-m-d', strtotime('now')));

		while ($row = mssql_fetch_assoc($_rs)) {
			$idx = explode('_', $row['segnalibro']);
			$idx = $idx[count($idx)-1];

			if (strpos($row['segnalibro'],'terap_farm_farmaco_') !== false) {
				$farmaci_terapia[$idx]['nome'] = $row['valore'];
			} elseif (strpos($row['segnalibro'],'dos_') !== false) {
				$farmaci_terapia[$idx]['dosaggio'] = $row['valore'];
			} elseif (strpos($row['segnalibro'],'dt_inizio_') !== false) {
				//inserisco anche qua trim perché anche se già inserito nella query ricevo la stringa ' '
				if(!empty($row['valore']) && trim($row['valore']) != '') {
					$date = explode('/',$row['valore']);
					$time = strtotime(date('Y-m-d', strtotime($date[2].'-'.$date[1].'-'.$date[0])));
					
					if($today_time < $time) {
						$farmaci_terapia[$idx]['skip'] = true;
					} else {
						$farmaci_terapia[$idx]['skip'] = false;
					}
				} else {
					$farmaci_terapia[$idx]['skip'] = true;
				}
			}
		}

		mssql_free_result($_rs);		
		
		foreach($farmaci_terapia as $key=>$farm) {
			if($farm['skip']) {
				unset($farmaci_terapia[$key]);
			}
		}
		*/
		
		// SBO 22/12/23: GESTIONE CON CONNESSIONE ALLA TERAPIA
		/* recupero il farmaco per nome e dosaggio in quanto nel modulo Foglio terapia farmacologica non abbiamo gli id dei
		*	farmaci e potrei averne con lo stesso nome ma con un dosaggio differente
		*/
		/*
		$farm_id_count = array();
		
		foreach($farmaci_terapia as $key=>$farm) {
			$query = "SELECT DISTINCT far.id, far.nome, far.dosaggio, far.lotto, far.struttura, far.quantita_attuale, 
							far.giacenza_minima, far.data_scadenza, far.id_paziente
						FROM farmaci far
						WHERE far.status = 1 AND far.deleted = 0
							AND far.quantita_attuale IS NOT NULL AND far.quantita_attuale > 0 
							AND far.nome = '" . $farm['nome'] . "' 
							AND (far.dosaggio IS NULL OR far.dosaggio = " . str_replace(',','.',$farm['dosaggio']) . ")
							AND far.id_paziente = ".$_POST['id_paziente'];
			$result = mssql_query($query, $conn);

			if(mssql_num_rows($result) > 0) {
				while($row = mssql_fetch_assoc($result)) {
					$farm_id_count[$row['id']] += 1;
					$row['terap_idx'] = $key;
					$farmaci_trovati[] = $row;
				}
			}

			mssql_free_result($result);
		}
		*/
		
		// SBO 22/12/23: LA CLIENTE HA CHIESTO DI SLEGARE LA TERAPIA FARMACOLOGICA DALLLA SOMMINISTRAZIONE
		$query = "SELECT far.id, far.nome, far.dosaggio, far.lotto, far.struttura, far.quantita_attuale, 
						far.giacenza_minima, far.data_scadenza, far.id_paziente
					FROM farmaci far
					WHERE far.status = 1 AND far.deleted = 0 AND far.stato_farmaco = 2
						AND far.quantita_attuale IS NOT NULL AND far.quantita_attuale > 0 
						AND far.data_scadenza >= CONVERT(date, GETDATE(), 101)
						AND far.id_paziente = ".$_POST['id_paziente'];
		$result = mssql_query($query, $conn);

		if(mssql_num_rows($result) > 0) {
			while($row = mssql_fetch_assoc($result)) {
				//$farm_id_count[$row['id']] += 1;
				//$row['terap_idx'] = $key;
				$farmaci_trovati[] = $row;
			}
		}

		mssql_free_result($result);
	}
	
	// SBO 22/12/23: GESTIONE CON CONNESSIONE ALLA TERAPIA
	/*
	foreach($farmaci_trovati as &$farm) {
		if($farm_id_count[$farm['id']] > 1) {
			$farm['terap_idx'] = "Farm. (" . $farm['terap_idx'] . ") | ";
		} else {
			$farm['terap_idx'] = NULL;
		}
	}
	unset($farm);
	*/

	$query = "SELECT DISTINCT far.id, far.nome, far.dosaggio, far.lotto, far.struttura, far.quantita_attuale, 
							far.giacenza_minima, far.data_scadenza, far.id_paziente
				FROM farmaci far
				INNER JOIN farmaci_struttura_pazienti far_str_paz ON far_str_paz.id_farmaco = far.id
				WHERE far.status = 1 AND far.deleted = 0
					AND far.quantita_attuale IS NOT NULL AND far.quantita_attuale > 0 
					AND far_str_paz.id_paziente = ".$_POST['id_paziente']."
				UNION
				SELECT DISTINCT far.id, far.nome, far.dosaggio, far.lotto, far.struttura, far.quantita_attuale, 
								far.giacenza_minima, far.data_scadenza, far.id_paziente
				FROM farmaci far
				INNER JOIN farmaci_paziente_pazienti far_paz_paz ON far_paz_paz.id_farmaco = far.id
				WHERE far.status = 1 AND far.deleted = 0
					AND far.quantita_attuale IS NOT NULL AND far.quantita_attuale > 0 
					AND far_paz_paz.id_paziente_destinatario = ".$_POST['id_paziente']."
				ORDER BY far.data_scadenza ASC";
	$result = mssql_query($query, $conn);

	while($row = mssql_fetch_assoc($result)) {
		$farmaci_trovati[] = $row;
	}
	
	mssql_free_result($result);

	foreach($farmaci_trovati as $farm) {
		// SBO 22/12/23: GESTIONE CON CONNESSIONE ALLA TERAPIA		
		//$nome = (empty($farm['terap_idx']) ? '' : $farm['terap_idx']) . $farm['nome'] . ' ' . $farm['dosaggio'] . ' - Lot. ' . $farm['lotto'] . ' - Qnt. ' . $farm['quantita_attuale'];
		$nome = $farm['nome'] . ' ' . $farm['dosaggio'] . ' - Lot. ' . $farm['lotto'] . ' - Qnt. ' . $farm['quantita_attuale'];
		
		if($farm['struttura']) {
			$nome.= ' (Struttura)';
		} elseif($_POST['id_paziente'] != $farm['id_paziente']) {
			$_query = "SELECT CONCAT (nome, ' ', cognome) AS paziente
						FROM utenti
						WHERE IdUtente = ". $farm['id_paziente'];
			$_rs = mssql_query($_query, $conn);
			$nome_paziente = mssql_fetch_assoc($_rs);
			$nome_paziente = $nome_paziente['paziente'];
			$nome.= " ($nome_paziente)";
			
			mssql_free_result($_rs);
		}
		
		if(!empty($farm['quantita_attuale']) && !empty($farm['giacenza_minima'])) {
			if($farm['quantita_attuale'] <= 0) {
				$nome.= ' | ESAURITO';
			} elseif($farm['giacenza_minima'] >= $farm['quantita_attuale']) {
				$nome.= ' | IN ESAURIMENTO';
			}
		}

		if(!empty($farm['data_scadenza'])) {
			if(strtotime(date('Y-m-d')) > strtotime($farm['data_scadenza'])) {
				$nome.= ' | SCADUTO';
			} elseif(strtotime($farm['data_scadenza']) <= strtotime(date('Y-m-d') . ' +7 days')) {
				$nome.= ' | IN SCADENZA';
			}
		}
		
		$selected = false;
		
		if(!empty($_POST['edit']) && !empty($_POST['id_modulo_versione'])) {
			$response['farmaci'][] = array(
				'valore' => $farm['id'],
				'etichetta' => $nome,
				'removed_farm' => in_array($farm['id'], $removed_farm)
			);
		} else {
			$response[] = array(
				'valore' => $farm['id'],
				'etichetta' => $nome
			);
		}
		
	}
	
	echo json_encode($response);
}

// RECUPERO LE INFORMAZIONI DEL FARMACO
elseif(isset($_POST['recupera_dati_farmaco_da_somministrare'])) 
{
	$response = array();
	
	$idx_to_seatch = empty($_POST['terap_farm_idx']) ? '%' : trim($_POST['terap_farm_idx']);

	// Recupero le informazioni dall'ultimo modulo di Terapia Farmacologica
	$_query = "SELECT cmp.segnalibro, valore
				FROM istanze_dettaglio id
				LEFT JOIN campi cmp ON cmp.idcampo = id.idcampo
				WHERE id.id_istanza_testata = (
					SELECT TOP 1 id_istanza_testata
					FROM istanze_testata
					WHERE id_modulo_padre = 205 AND id_cartella = " . $_POST['id_cartella'] ."
					ORDER BY id_istanza_testata DESC
				) AND id.valore is not null AND id.valore != ''
				AND (cmp.segnalibro like 'terap_farm_farmaco_%' OR cmp.segnalibro like 'qnt_$idx_to_seatch' 
					OR cmp.segnalibro like 'orario_$idx_to_seatch' OR cmp.segnalibro like 'tip_somm_$idx_to_seatch'
					OR cmp.segnalibro like 'dos_$idx_to_seatch' 
				)";
	$_rs = mssql_query($_query, $conn);

	$lista_farmaci =  array();

	while ($row = mssql_fetch_assoc($_rs)) {			
		if (strpos($row['segnalibro'],'terap_farm_farmaco_') !== false) {
			$idx = explode('_', $row['segnalibro']);
			$idx = $idx[count($idx)-1];
			
			$lista_farmaci[$idx]['nome'] = trim($row['valore']);
		} elseif (strpos($row['segnalibro'],'qnt_') !== false) {
			$idx = explode('_', $row['segnalibro']);
			$idx = $idx[count($idx)-1];
			
			$lista_farmaci[$idx]['qnt'] = trim($row['valore']);
		} elseif (strpos($row['segnalibro'],'orario_') !== false) {
			$idx = explode('_', $row['segnalibro']);
			$idx = $idx[count($idx)-1];
			
			$lista_farmaci[$idx]['orario'] = trim($row['valore']);
		} elseif (strpos($row['segnalibro'],'dos_') !== false) {
			$idx = explode('_', $row['segnalibro']);
			$idx = $idx[count($idx)-1];
			
			$lista_farmaci[$idx]['dosaggio'] = trim($row['valore']);
		} elseif (strpos($row['segnalibro'],'tip_somm_') !== false) {
			$idx = explode('_', $row['segnalibro']);
			$idx = $idx[count($idx)-1];
			
			$lista_farmaci[$idx]['tip_somm'] = trim($row['valore']);
		}
	}
	

		
	mssql_free_result($_rs);

	// Recupero le informazioni del farmaco selezionato, confronto per nome e dosaggio perché nel modulo
	// di Terapia Farmacologica non ho un ID del farmaco
	foreach($lista_farmaci as $farm) {
		$_query = "SELECT nome, dosaggio, id_paziente, struttura
					FROM farmaci
					WHERE id = " . $_POST['id_farmaco'];
		$_rs = mssql_query($_query, $conn);
		$_row = mssql_fetch_assoc($_rs);
		
		if((strtolower($_row['nome']) == strtolower($farm['nome'])) && ($_row['dosaggio'] == $farm['dosaggio'])) {
			$farm['id_paziente'] = $_row['id_paziente'];
			$farm['struttura'] = $_row['struttura'];
			$response = $farm;
			break;
		}
	}

	// Se non ho trovato il farmaco nella terapia vuol dire che è presente in maschera perchè da somministrare 
	// d'urgenza
	if(empty($response)) {
		$_query = "SELECT nome, dosaggio, tipologia_somministrazione, id_paziente, struttura
					FROM farmaci
					WHERE id = " . $_POST['id_farmaco'];
		$_rs = mssql_query($_query, $conn);
		$_row = mssql_fetch_assoc($_rs);

		$response['nome'] = $_row['nome'];
		$response['dosaggio'] = $_row['dosaggio'];
		$response['tip_somm'] = $_row['tipologia_somministrazione'];		
		$response['id_paziente'] = $_row['id_paziente'];
		$response['struttura'] = $_row['struttura'];
		$response['orario'] = '';
		$response['qnt'] = '';
	}
	
	echo json_encode($response);
}


// controlla i moduli compilati nell'istanza e restituisce il flag checkato per la checkbox
	function controlla_flag($conn, $idcartella, $idmoduloversione, $idinserimento, $nome, $data) 	// in fase di modifica del modulo di oscuramento, controlla nel DB quali voci della combo sono state flaggate
	{
		$queryGetModuliOscurati = "SELECT id.valore FROM istanze_dettaglio AS id JOIN istanze_testata AS it ON it.id_istanza_testata = id.id_istanza_testata WHERE it.id_modulo_versione = $idmoduloversione AND it.id_cartella = $idcartella AND it.id_inserimento = $idinserimento";
		
		$result_GMO = mssql_query($queryGetModuliOscurati, $conn);
		if(mssql_num_rows($result_GMO) > 0)
		{
			$row_GMO = mssql_fetch_assoc($result_GMO);
			$valore = $row_GMO['valore'];
			$arr_valore= split(";", $valore);
			
			if($data == 'tutti')	$suffisso_nome = "(tutte le compilazioni)";
			else					$suffisso_nome = "(compilazione del $data)";
			
			foreach($arr_valore as $valore_singolo) {
				if( strpos($valore_singolo, "$nome $suffisso_nome") !== false) {
					$checked = "checked";
					break;
				} else {
					$checked = "";
				}
			}
		} else $checked = "";
		
		return $checked;
	}

	
?>