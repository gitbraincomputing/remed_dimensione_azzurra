<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
$id_permesso = $id_menu = 2;

define('SEDE_SERV_AMB', 'Servizi ambulatoriali');
define('SEDE_RESIDENZIALE', 'Residenziale');
define('SEDE_NON_GESTITA', 'Struttura non gestita');

$conn = db_connect();

$where = '';
$req_start_time = '';
$req_end_time = '';
$start_time = '00:00';
$end_time = '23:30';
$id_paziente = '';
$reparto_paziente = '';
$data_somministrazione = date('Y-m-d', strtotime('now'));

if(isset($_REQUEST['reparto_paziente']) && !empty($_REQUEST['reparto_paziente'])) {
	$reparto_paziente = $_REQUEST['reparto_paziente'];
	$where = " AND ist_test.id_paziente IN (SELECT id_paziente FROM reparti_pazienti WHERE id_reparto = $reparto_paziente) ";
}

if(isset($_REQUEST['paziente']) && !empty($_REQUEST['paziente'])) {
	$id_paziente = $_REQUEST['paziente'];
	$where = " AND ist_test.id_paziente = $id_paziente ";
}

if(isset($_REQUEST['data_somministrazione']) && $_REQUEST['data_somministrazione'] !== "") {
	$data_somministrazione = $_REQUEST['data_somministrazione'];
}

// AZIONE SCATENATA SE EFFETTUO UNA RICECA SPECIFICANDO IL PAZIENTE
if(isset($_REQUEST['ora_inizio']) && !empty($_REQUEST['ora_inizio'])) {	
	$start_time = $req_start_time = $_REQUEST['ora_inizio'];
}

// AZIONE SCATENATA SE EFFETTUO UNA RICECA SPECIFICANDO IL PAZIENTE
if(isset($_REQUEST['ora_fine']) && !empty($_REQUEST['ora_fine'])) {	
	$end_time = $req_end_time = $_REQUEST['ora_fine'];
}

$start_time = strtotime($start_time);
$end_time = strtotime($end_time);

?>

<script type="text/javascript">
	// AZIONE DEL TASTO DI RICERCA
	function search(reparto_paziente, paziente, data_somministrazione, ora_inizio, ora_fine) {
		$("#layer_nero2").toggle();
		$('#wrap-content').innerHTML="";
		pagina_da_caricare="report_farmaci.php?reparto_paziente="+reparto_paziente+"&paziente="+paziente.replace(/\s/g,'')+"&data_somministrazione="+data_somministrazione+"&ora_inizio="+ora_inizio+"&ora_fine="+ora_fine;
		$("#wrap-content").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		 });
	}
	
	$(document).ready(function() {
		$.mask.addPlaceholder('~',"[+-]");	
		$(".campo_data").mask("99/99/9999");
		$(".solo_lettere").validation({ type: "alpha", add:"' "});	
		$("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
	});
</script>

<div id="wrap-content">
	<div class="padding10">
		<div class="logo">
			<img src="images/re-med-logo.png" />
		</div>
		
		<div class="titoloalternativo">
			<h1>Elenco farmaci da somministrare</h1>
			<div class="stampa">
				<a onclick="javascript:(alert('Premere OK e attendere l\'elaborazione del documento..'));" 
					href="include/export_sql_xls.php?rep=rep_terapia_farmacologica&where=<?=rawurlencode($where)?>&start_time=<?=$start_time?>&end_time=<?=$end_time?>"><img src="images/excel-button.png"></a>
			</div>
		</div>
		
<?

	if($_SESSION['UTENTE']->is_root() || $_SESSION['UTENTE']->is_ds()) {
		$op_filter = '';
	} else {
		$op_filter = ' AND id IN (
			SELECT id_reparto
			FROM reparti_operatori
			WHERE id_operatore = ' . $_SESSION['UTENTE']->get_userid() . '
		)';
	}
	
	$_sql = "SELECT id, nome, sede
				FROM reparti rp
				WHERE status = 2 AND deleted = 0 
				$op_filter";
	$_rs = mssql_query($_sql, $conn);
	$lista_reparti = array();
	while ($row = mssql_fetch_assoc($_rs)) {
		$lista_reparti[] = $row;
	}
	mssql_free_result($_rs);

?>
		<span style="border-bottom: 0px none; margin-bottom: 15px; width:100% !important" class="rigo_mask no_print">
			<div class="campo_big " style="float:left;padding-right:10px">
				<div class="testo_mask">Reparto</div>
				<div class="campo_mask">
					<select id="reparto_paziente" class="scrittura" name="reparto_paziente" >
<?php
			if(count($lista_reparti) > 1)
				echo '<option value="" selected="selected" >Tutti</option>';
				
			foreach($lista_reparti as $row) {
				switch ($row['sede']) {
					case 1:
						$row['sede'] = SEDE_SERV_AMB;
						break;

					case 2:
						$row['sede'] = SEDE_RESIDENZIALE;
						break;

					default:
						$row['sede'] = SEDE_NON_GESTITA;
						break;
				}
				echo '<option value="' . $row['id'] . '" ' . (!empty($reparto_paziente) && $reparto_paziente == $row['id'] ? 'selected' : '') . ' >' . $row['nome'] . ' - ' . $row['sede'] . '</option>';
			}			
?>
					</select>
				</div>
			</div>

			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Paziente</label><br>
				<input type='hidden' name='id_paziente' id='id_paziente'>

				<select class="scrittura" name="lista_pazienti" id="lista_pazienti" type="text" style="width: 350px;" >
					<option selected="" value="">Tutti</option>
<?php
	// RECUPERO I PAZIENTI CHE HANNO DEI MODULI PER REGIMI INFERMIERISTICI ATTIVI
	$_query = "SELECT paz.IdUtente, YEAR(DataNascita) as DataNascita, CONCAT(paz.Cognome, ' ',paz.Nome) as Utente
				FROM utenti as paz
				INNER JOIN (
					select idutente
					from utenti_cartelle
					where idutente not in (605) AND idregime IN (52,53,59,60,61,62,64,65) AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'
				) as paz_cart_inf ON paz_cart_inf.idutente = paz.IdUtente
				WHERE paz.cancella = 'n' AND paz.stato = 1 AND paz.cancella = 'n'
				ORDER BY Utente, DataNascita asc";			
	$_rs = mssql_query($_query, $conn);

	while ($paz = mssql_fetch_assoc($_rs)) {
?>			
					<option <?= $paz['IdUtente'] == $id_paziente ? 'selected' : '' ?> value="<?=$paz["IdUtente"]?>"><?= $paz["Utente"] . ' | Anno ' . $paz["DataNascita"] ?></option>
<?php
	}
?>
				</select>
			</div>
		</span>
		<span style="border-bottom: 0px none; margin-bottom: 15px; width:100% !important" class="rigo_mask no_print">	
			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Data</label><br>
				<input type="date" name="data_somministrazione" id="data_somministrazione" <? if($data_somministrazione) echo 'value="'.$data_somministrazione.'"'; ?> >
			</div>
		
			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Dalle</label><br>
				<select class="scrittura" name="ora_inizio" id="ora_inizio" style="min-width: 80px;">
					<option selected="" value="">Tutti</option>
<?php
	$query = "SELECT etichetta
				FROM campi_combo
				WHERE idcombo = 257 AND stato = 1 AND cancella = 'n'";
	$rs_periodo = mssql_query($query, $conn);

	while($_row = mssql_fetch_assoc($rs_periodo)) {	?>
					<option <?= $_row['etichetta'] == $req_start_time ? 'selected' : '' ?> value="<?=$_row['etichetta']?>"><?=$_row['etichetta']?></option>
<?php
	}
?>
				</select>
			</div>
			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Alle</label><br>			
				<select  class="scrittura" name="ora_fine" id="ora_fine" style="min-width: 80px;">
					<option selected="" value="">Tutti</option>
<?php
	$query = "SELECT etichetta
				FROM campi_combo
				WHERE idcombo = 257 AND stato = 1 AND cancella = 'n'";
	$rs_periodo = mssql_query($query, $conn);

	while($_row = mssql_fetch_assoc($rs_periodo)){
?>
						<option <?= $_row['etichetta'] == $req_end_time ? 'selected' : '' ?> value="<?=$_row['etichetta']?>"><?=$_row['etichetta']?></option>
<?php
	}
?>
				</select>
			</div>	
	
			<div class="campo_big "><br>
				<input type="button" style="padding: 0px 10px" name="esegui filtro" value="esegui filtro" onclick="search($('#reparto_paziente').val(),$('#lista_pazienti').val(),$('#data_somministrazione').val(),$('#ora_inizio').val(),$('#ora_fine').val());" />
			</div>
		</span>

		<table id="table" class="tablesorter" cellspacing="1"> 
			<thead> 
				<tr>
					<th>ORARIO</th>
					<th>PAZIENTE</th>
					<th>ID PAZIENTE</th>
					<th>REPARTO</th>
					<th>FARMACO</th>
					<th>DOSAGGIO</th>
					<th>TIPOLOGIA SOMMINISTRAZIONE</th>
					<th>QUANTITA</th>
				</tr>
			</thead>
			<tbody>
<?
		//RECUPERO I DATI DELL'ULTIMA TERAPIA FARMACOLOGICA DEL PAZIENTE
		$_query = "SELECT ist_test.id_paziente, cmp.segnalibro, 
						CASE cmp.etichetta 
							WHEN 'Tipologia somministrazione' THEN (SELECT etichetta FROM  campi_combo WHERE idcombo = 250 AND valore = ist_det.valore)
							ELSE CAST(TRIM(ist_det.valore) AS text)
						END AS valore
						FROM dbo.istanze_testata AS ist_test
						INNER JOIN ( 
							SELECT it.id_paziente, it.id_modulo_versione, MAX(it.id_inserimento) AS id_inserimento 
							FROM dbo.istanze_testata it
							INNER JOIN (
								select id_paziente, MAX(id_modulo_versione) AS id_modulo_versione
								from istanze_testata
								where id_modulo_padre = 205 AND id_paziente not in (605) AND id_modulo_versione >= 6143
								GROUP BY id_paziente
							) max_vers_mod ON max_vers_mod.id_paziente = it.id_paziente AND max_vers_mod.id_modulo_versione = it.id_modulo_versione
							WHERE it.id_modulo_padre = 205 AND it.id_paziente not in (605) AND it.id_modulo_versione >= 6143
							GROUP BY it.id_paziente, it.id_modulo_versione
						) AS max_id_ins ON max_id_ins.id_inserimento = ist_test.id_inserimento 
											AND max_id_ins.id_paziente = ist_test.id_paziente 
											AND max_id_ins.id_modulo_versione = ist_test.id_modulo_versione 
						INNER JOIN dbo.istanze_dettaglio AS ist_det ON ist_det.id_istanza_testata = ist_test.id_istanza_testata
						INNER JOIN dbo.campi AS cmp ON cmp.idcampo = ist_det.idcampo
						INNER JOIN (
							SELECT paz_cart_inf.id as IdCartella, paz.reparto
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
		$conta = mssql_num_rows($_rs);

		if($conta == 0) {
			echo "<tr>
					<td colspan='8'>Nessun record trovato</td>
				</tr>";
		} else {
			
			$terap_farm_list = array();
			$date_filter = strtotime($data_somministrazione);
			echo '<pre>';

			while($terap_farm_row = mssql_fetch_assoc($rs_terap)) 
			{
				$segnalibro = explode('_', $terap_farm_row['segnalibro']);
				$idx = array_pop($segnalibro);
				$segnalibro = implode('_', $segnalibro);
				
				if(!isset($terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'])) {
					$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = false;
				}
				
				if(strpos($terap_farm_row['segnalibro'], 'dt_inizio_') !== false) {					
					if(!empty($terap_farm_row['valore']) && trim($terap_farm_row['valore']) != '') {
						$date = explode('/',$terap_farm_row['valore']);
						$valore = date('Y-m-d', strtotime($date[2].'-'.$date[1].'-'.$date[0]));						
						$terap_farm_list[$terap_farm_row['id_paziente']][$idx][$segnalibro] = $valore;
						$dato_to_time = strtotime($valore);

						if($date_filter < $dato_to_time) {
							$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = true;
						}
					}
				} elseif(strpos($terap_farm_row['segnalibro'], 'dt_fine_') !== false) {
					if(!empty($terap_farm_row['valore']) && trim($terap_farm_row['valore']) != '') {
						$date = explode('/',$terap_farm_row['valore']);
						$dato_to_time = strtotime(date('Y-m-d', strtotime($date[2].'-'.$date[1].'-'.$date[0])));

						if($dato_to_time < $date_filter) {
							$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = true;
						}
					}
				} elseif(strpos($terap_farm_row['segnalibro'], 'orario_') !== false) {				
					if(!empty($terap_farm_row['valore']) && trim($terap_farm_row['valore']) != '') {
						$time = str_replace(';',',',$terap_farm_row['valore']);
						
						$_query = "SELECT etichetta
									FROM campi_combo
									WHERE idcombo = 257 AND valore in ($time)";
						$_rs = mssql_query($_query, $conn);
						$n_orari = mssql_num_rows($_rs);
						
						while($_row = mssql_fetch_assoc($_rs)) {
							$terap_farm_list[$terap_farm_row['id_paziente']][$idx][$segnalibro][] = $_row['etichetta'];
						}
						
						mssql_free_result($_rs);
					} else {
						$terap_farm_list[$terap_farm_row['id_paziente']][$idx]['skip'] = true;
					}
				} else {
					$terap_farm_list[$terap_farm_row['id_paziente']][$idx][$segnalibro] = $terap_farm_row['valore'];
				}
			}

			$_TMP_terap_farm_list = array();
			foreach($terap_farm_list as $key_paz=>$terapia) {
				
				foreach($terapia as $key_ter=>$item) {
					if(isset($item['skip']) && $item['skip']) {
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
			
			$conta = 0;
			foreach($terap_farm_list as $key_paz=>$paziente) {				
				foreach($paziente['terapia'] as $item) {					
					$ciclicita = $item["frequenza_terapia"];
					
					if(!empty($ciclicita)) {
						$dataInizio = strtotime($item['dt_inizio']);
						$differenzaGiorni = floor(($date_filter - $dataInizio) / (60 * 60 * 24));
						
						if ($differenzaGiorni % $ciclicita != 0) {
							continue;
						}
					}
					
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
			
			ksort($time_farm_list);
			
			foreach($time_farm_list as $orario=>$utenti_list) {
				$_time = strtotime($orario);
				if($_time < $start_time || $_time > $end_time) {
					continue;
				}
				
				foreach($utenti_list as $item) { 
					$conta++;	?>
				<tr>
					<td><?=$orario?></td>
					<td><?=$item['utente']?></td>
					<td><?=$item['id_utente']?></td>
					<td><?=$item['reparto']?></td>
					<td><?=$item['farmaco']?></td>
					<td><?=$item['dosaggio']?></td>
					<td><?=$item['tip_somm']?></td>
					<td><?=$item['quantita']?></td>
				</tr>
<?php			}
			}
		}
		mssql_free_result($rs_terap); 
?>
			</tbody> 
		</table>
<?
	footer_paginazione($conta);
	
	if($conta > 0) {
?>
		<p style="float:right; margin: 6px 10px 0 0"><?=$conta?> risultati trovati.</p>
<?	} ?>
	</div>
</div>