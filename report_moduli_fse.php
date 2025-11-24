<?
	include_once('include/functions.inc.php');
	include_once('include/dbengine.inc.php');
	include_once('include/function_page.php');

	$session_ope = $_SESSION['UTENTE']->get_userid();
	$conn = db_connect();
	
	$id_operatore = $_REQUEST['id_operatore'];
	$dt_inizio = $_REQUEST['data_inizio'];
	$dt_fine = $_REQUEST['data_fine'];
	$search = 'search' == $_REQUEST['action'];
	
	// AZIONE SCATENATA DAL TENTATIVO DI RIMOZIONE DELLA SINGOLA RIGA
	if('delete' == $_REQUEST['action'] && isset($_REQUEST['id_final_fse']) && !empty($_REQUEST['id_final_fse'])) {
		$cdf = 'XTSXST71A01H501T'; //$_REQUEST['paziente'];
		$id_final_fse = $_REQUEST['id_final_fse'];
		
		// EFFETTUO LA CHIAMATA AL SERVIZIO FSE DI RICERCA DEL DOCUMENTO CHE IN CASO DI 'ESITO' => OK VALORIZZA IL CAMPO 'LID', IN CASO DI KO
		// E' VALORIZZATO ANCHE IL CAMPO 'TEXT_ESITO'
		// return array
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://localhost/fse_campania/index.php?action=ricercaDocumenti&usephp=7&from_fe=1&codice_fiscale=$cdf&dti=$dt_inizio&dtf=$dt_fine&id_final_fse=$id_final_fse");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$ricerca_doc_output = json_decode(curl_exec($ch), true);
		curl_close($ch);

		if('0000' == $ricerca_doc_output['esito']) {
			$idDocumento = $ricerca_doc_output['LID'];
			
			// EFFETTUO LA CHIAMATA AL SERVIZIO FSE DI CANCELLAZIONE DEL DOCUMENTO CHE MI RITORNA ESCLUSIVAMENTE OK O KO NEL CAMPO 'ESITO', IN CASO DI KO
			// E' VALORIZZATO ANCHE IL CAMPO 'TEXT_ESITO'
			// return array
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"http://localhost/fse_campania/index.php?action=cancellazioneMetadati&usephp=7&from_fe=1&codice_fiscale=$cdf&id_documento=$idDocumento");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$canc_output = json_decode(curl_exec($ch), true);
			curl_close($ch);
			
			//RECUPERO I DATI DEL DOCUMENTO E INSERISCO UNA NUOVA RIGA DI LOG DELLE AZIONE EFETTUATE SUI SERVIZI FSE
			$_query = "SELECT *
						FROM repository_fse
						WHERE codice_fiscale_paziente = '$cdf' AND id_final_fse = '$id_final_fse'";		
			$_rs = mssql_query($_query, $conn);

			$doc_log = mssql_fetch_assoc($_rs);
			
			$_query = "INSERT repository_fse
						VALUES (".
							$doc_log['id_paziente'].",'$cdf','".$doc_log['nome_modulo']."',".$doc_log['id_modulo_padre'].",".$doc_log['id_modulo_versione'].",".
							$doc_log['id_istanza_testata'].",'".$doc_log['offuscamento']."','DELETE','".$doc_log['id_temp_fse']."','$id_final_fse',".
							"GETDATE(),$session_ope,".$doc_log['opeins_modulo'].",'OK','".$canc_output['esito']."','".
							$canc_output['text_esito']."','Success','s'
						)";
			$_rs = mssql_query($_query, $conn);
		}
	}

	// AZIONE SCATENATA SE EFFETTUO UNA RICECA SPECIFICANDO IL PAZIENTE
	if(isset($_REQUEST['paziente']) && !empty($_REQUEST['paziente'])) {	
		$cdf = explode('-', $_REQUEST['paziente']);
		$cdf = trim($cdf[1]);
		
		// EFFETTUO LA CHIAMATA AL SERVIZIO FSE CHE MI ESTRAPOLA TUTTI I DOCUMENTI CARCICATI SULL'FSE E IL RELATIVO STATO, IN CASO DI 'ESITO' => OK 
		// VALORIZZA IL CAMPO 'values', IN OGNI CASO E' VALORIZZATO IL CAMPO (array) 'result' CHE CONTIENE 'esito' E 'text_esito'
		// return array
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://localhost/fse_campania/index.php?action=esitoCaricamentoDocumento&usephp=7&from_fe=1&codice_fiscale=$cdf&dti=$dt_inizio&dtf=$dt_fine");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$esito_caricamento_output = json_decode(curl_exec($ch), true);	
		curl_close($ch);
	}
?>

<script type="text/javascript">
	// AZIONE DEL TASTO DI RICERCA
	function search(paziente, id_operatore, data_inizio, data_fine) {
		$("#layer_nero2").toggle();
		$('#wrap-content').innerHTML="";
		pagina_da_caricare="report_moduli_fse.php?action=search&paziente="+paziente.replace(/\s/g,'')+"&id_operatore="+id_operatore+"&data_inizio="+data_inizio+"&data_fine="+data_fine;
		$("#wrap-content").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		 });
	}
	
	// SE IL DOCUMENTO HA RICEVUTO UN FINAL_ID ABILITO QUESTA FUNZIONA CHE ESEGUE IL SERVIZIO DI CANCELLAZIONE
	function delete_doc(id_final_fse, paziente, data_inizio, data_fine) {
		$("#layer_nero2").toggle();
		$('#wrap-content').innerHTML="";
		pagina_da_caricare="report_moduli_fse.php?action=delete&paziente="+paziente.replace(/\s/g,'')+"&data_inizio="+data_inizio+"&data_fine="+data_fine+"&id_final_fse="+id_final_fse;
		$("#wrap-content").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		 });
	}
	
	// SE EFFETTUO UNA RICERCA SENZA PAZIENTE E CLICCO SULL'ICONA LENTE POSSO VEDERE LO STORICO DELLE AZIONI DEL DOCUMENTO
	function show_history(cdf_paziente, id_temp_fse) {
		cdf_paziente = 'XTSXST71A01H501T';
		small_window("report_moduli_fse_lista_eventi_doc.php?cdf=" + cdf_paziente +"&doc_temp_id=" + id_temp_fse, 1500, 400);
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
<?php
	if($_SESSION['UTENTE']->get_userid() != 11) {
		die("Pagina in sviluppo...");
	} 
?>
		<div class="titoloalternativo">
			<h1>Moduli FSE</h1>
		</div>

		<span style="border-bottom: 0px none; margin-bottom: 15px; width:100% !important" class="rigo_mask no_print">
			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Filtra per paziente</label><br>
				<input type='hidden' name='id_paziente' id='id_paziente'>

				<input id="lista_pazienti" type="text" id="txtAutoComplete" list="datalist_pazienti" style="width: 300px;" placeholder="Seleziona il paziente" />
				<datalist id="datalist_pazienti">
<?php
	// RECUPERO I PAZIENTI CHE HANNO DEI MODULI INVIATI
	$_query = "SELECT DISTINCT paz.CodiceFiscale, paz.Cognome, paz.Nome
				FROM utenti as paz
				RIGHT JOIN repository_fse AS fse ON fse.id_paziente = paz.IdUtente
				WHERE paz.cancella = 'n'";			
	$_rs = mssql_query($_query, $conn);

	$userArray = array();

	while ($paz = mssql_fetch_assoc($_rs)) {
?>			
					<option value="<?= $paz["Cognome"] . ' ' . $paz["Nome"] . ' - ' . $paz["CodiceFiscale"] ?>" />
<?php
	}
?>
				</datalist>
			</div>
		
			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Filtra per operatore</label><br>
				<select  class="scrittura" name="id_operatore" id="id_operatore">
					<option selected="" value="">tutti</option>
<?php
	$query="SELECT uid, nome
			FROM operatori
			WHERE (nome <> 'admin') AND (nome <> 'Software Administrator') 
			GROUP BY uid,nome
			ORDER BY nome";
	$rs_op = mssql_query($query, $conn);

	while($row_op = mssql_fetch_assoc($rs_op)){
?>
						<option <?if($id_operatore==trim($row_op['uid'])) echo("selected")?> value="<?=trim($row_op['uid'])?>"><?=strtoupper(trim($row_op['nome']))?></option>
<?php
	}
?>
				</select>
			</div>
		
			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Data Inizio</label><br>
				<input type="date" name="data_inizio" id="data_inizio" value="<?=$dt_inizio?>" max="<?=date("Y-m-d")?>" >
			</div>	
	
			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Data fine</label><br>
				<input type="date" name="data_fine" id="data_fine" value="<?=$dt_fine?>" max="<?=date("Y-m-d")?>">
			</div>	
	
			<div class="campo_big "><br>
				<input type="button" style="padding: 0px 10px" name="esegui filtro" value="esegui filtro" onclick="search($('#lista_pazienti').val(),$('#id_operatore').val(),$('#data_inizio').val(),$('#data_fine').val());" />
			</div>
			<br>
			<div>
				<p>Il modulo prevede 2 modalità di utilizzo:</p>
				<ul>
					<li>Se non si inserisce il paziente, vengono recuperati tutti i pazienti con almeno 1 documento inviato al FSE e ne viene riportato l'ultimo stato.<br>Per ogni paziente è possibile consultare l'intera cronologia del documento tramite l'apposita icona.</li>
					<li>Inserendo il paziente viene recuperato e aggiornato dal repository della regione Campania l'ultimo stato di ogni documento caricato.</li>
				</ul>
			</div>
		</span>
<? 	// SE EFFETTUO LA RICECA SELEZIONANDO UN PAZIENTE E LA CHIAMATA VA BUON FINE, CREO UNA TABELLA PARTENDO DAI DATI RECUPERATI DAL SERVIZIO FSE
	// E INTERPOLATI CON QUELLI PRESENTI SUL NOSTRO DB, IN MODO DA POTER AGGIORNARE QUELLI CHE SONO IN STATO OK E HANNO OTTENUTO UN ID FINALE
	if($search && isset($esito_caricamento_output) && $esito_caricamento_output['result']['code'] = '0000') { ?>
		<table id="fse_list" class="tablesorter" cellspacing="1"> 
			<thead> 
				<tr>
					<th>OPERATORE</th>
					<th>MODULO</th>
					<th>OFFUSCATO</th>
					<th>DATA ESECUZIONE</th>
					<th>STATO ELABORAZIONE</th>					
					<th>ID DOCUMENTO TEMPORANEO</th>
					<th>ID DOCUMENTO ELABORATO</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
		<?
			$_query = "SELECT ope.nome AS nome_operatore, rep_fse.nome_modulo, rep_fse.data_esecuzione, rep_fse.offuscamento, rep_fse.id_temp_fse
					FROM repository_fse AS rep_fse
					LEFT JOIN operatori AS ope ON ope.uid = rep_fse.opeins_modulo
					WHERE rep_fse.id_final_fse = NULL OR rep_fse.id_final_fse not in (
							SELECT id_final_fse
							FROM repository_fse
							WHERE cancellato = 's'
						) " .
						(!empty($id_operatore) ? (" AND rep.opeins_modulo = $id_operatore ") : "") . "
					ORDER BY data_esecuzione, nome_modulo ASC";		
			$_rs = mssql_query($_query, $conn);			

			$documenti_fse = array();
			$doc_to_update = array();
			while($row = mssql_fetch_assoc($_rs)) 
			{
				foreach($esito_caricamento_output['values'] as $key=>&$elem) {			
					if($row['id_temp_fse'] === $elem['id_temp_doc']) {
						$documenti_fse[] = array(
							'stato_elab' => $elem['stato_elab'],
							'id_temp_doc' => $elem['id_temp_doc'],
							'id_doc' => $elem['id_doc'],
							'nome_operatore' => $row['nome_operatore'],
							'nome_modulo' => $row['nome_modulo'],
							'data_esecuzione' => $row['data_esecuzione'],
							'offuscamento' => $row['offuscamento']
						);
						
						if(!empty($elem['id_doc'])) {
							$doc_to_update[] = array(
								'id_temp_doc' => $elem['id_temp_doc'],
								'id_doc' => $elem['id_doc']
							);
						}
						
						unset($esito_caricamento_output[$key]);
					}
				}
			}
			mssql_free_result($_rs);
			$conta = count($documenti_fse);

			if($conta == 0) {
				echo "<tr>
						<td colspan='8'>Nessun record trovato</td>
					</tr>";
			} else {			
				foreach($documenti_fse as $doc) {
					echo "<tr>
							<td>" . $doc['nome_operatore']. "</td>
							<td>" . $doc['nome_modulo']. "</td>
							<td>" . ($doc['offuscamento'] == 'n' ? 'NO' : 'SI') . "</td>
							<td>" . date_format(date_create($doc['data_esecuzione']),"d/m/Y H:i") . "</td>
							<td>" . ($doc['stato_elab'] === 'OK' ? 'Elaborato' : ($doc['stato_elab'] === 'ELAB' ? 'In elaborazione' : 'Rifiutato')) . "</td>							
							<td>" . $doc['id_temp_doc'] . "</td>
							<td>" . $doc['id_doc'] . "</td>" .
							(!empty($doc['id_doc']) ?
								"<td>
									<a href='#' title='Cancella'><img src='images/remove.png' 
										onclick=\"delete_doc('" . $doc['id_doc'] . "','$cdf','$dt_inizio','$dt_fine');\" /></a>
								</td>"
								: 
								"<td>
									<a href='#' title='Cancella'><img src='images/remove_grayscale.png' 
										onclick=\"alert('Non è possibile cancellare un documento che non ha ancora terminato il caricamento...')\" /></a>
								</td>"
							) .
						"</tr>";
				}
			}
		?>
			</tbody> 
		</table>
		
<?
		footer_paginazione($conta);

		if(!empty($doc_to_update) && count($doc_to_update) > 0) {
			foreach($doc_to_update as $doc) {
				$_query = "UPDATE repository_fse
						SET id_final_fse = '" . $doc['id_doc'] . "' 
						WHERE azione='CREATE' AND stato_esecuzione = 'OK' AND id_final_fse IS NULL AND id_temp_fse = '" . $doc['id_temp_doc'] . "'";		
				$_rs = mssql_query($_query, $conn);	
			}
		}
		// SE EFFETTUO LA RICERCA SENZA INSERIRE UN PAZIENTE RECUPERO DI TUTTI I DOCUMENTI, PRESENTI SULLA TABELLA 'repository_fse', LA RIGA PIU' RECENTE
	} elseif($search) {
		$dt_inizio = (empty($dt_inizio) ? '01/10/2022' : $dt_inizio) . ' 00:00:00';
		$dt_fine = (empty($dt_fine) ? date('d/m/Y') : date_format(date_create($dt_fine),"d/m/Y")) . ' 23:59:59';
?>
		<table id="fse_logs" class="tablesorter" cellspacing="1"> 
			<thead> 
				<tr>
					<th>PAZIENTE</th>
					<th>OPERATORE</th>
					<th>MODULO</th>
					<th>OFFUSCATO</th>
					<th>AZIONE</th>
					<th>DATA ESECUZIONE</th>					
					<th>STATO AZIONE</th>			
					<th>ID DOCUMENTO TEMPORANEO</th>
					<th>ID DOCUMENTO ELABORATO</th>
					<th colspan="2" ></th>
				</tr>
			</thead>
			<tbody>
<?
		$_query = "SELECT CONCAT(paz.Nome,' ',paz.Cognome) as nome_paziente, paz.CodiceFiscale, ope.nome as nome_operatore, rep.*
					FROM repository_fse AS rep
					INNER JOIN (
						SELECT id_paziente, max(data_esecuzione) AS data_esecuzione
						FROM repository_fse
						GROUP BY id_paziente
					) last_activity ON last_activity.data_esecuzione = rep.data_esecuzione and last_activity.id_paziente = rep.id_paziente
					LEFT JOIN utenti AS paz ON paz.IdUtente = rep.id_paziente
					LEFT JOIN operatori AS ope ON ope.uid = rep.opeins_modulo
					WHERE rep.data_esecuzione BETWEEN '$dt_inizio' AND '$dt_fine' " .
					(!empty($id_operatore) ? (" AND rep.opeins_modulo = $id_operatore ") : "") . "
					ORDER BY paz.Nome, paz.Cognome, rep.nome_modulo, rep.data_esecuzione DESC";

		$_rs = mssql_query($_query, $conn);			
		$conta = mssql_num_rows($_rs);
			
		if($conta == 0) {
			echo "<tr>
					<td colspan='11'>Nessun record trovato</td>
				</tr>";
		} else {
			while($doc = mssql_fetch_assoc($_rs)) 
			{
				echo "<tr>
						<td>" . $doc['nome_paziente']. "</td>
						<td>" . $doc['nome_operatore']. "</td>
						<td>" . $doc['nome_modulo']. "</td>
						<td>" . ($doc['offuscamento'] == 'n' ? 'NO' : 'SI') . "</td>
						<td>" . ($doc['azione'] === 'CREATE' ? 'Creazione' : 'Cancellazione') . "</td>
						<td>" . date_format(date_create($doc['data_esecuzione']),"d/m/Y H:i") . "</td>							
						<td>" . ($doc['stato_esecuzione'] === 'OK' ? 'Eseguita' : 'Non eseguita') . "</td>					
						<td>" . $doc['id_temp_fse'] . "</td>
						<td>" . $doc['id_final_fse'] . "</td>
						<td>
							<a href='#' onclick=\"javascript:show_history('" . $doc['CodiceFiscale'] . "','" . $doc['id_temp_fse'] . "');\" ><img src='images/view.png' /></a>
						</td>" .
						(!empty($doc['id_final_fse']) ?
							(
								($doc['azione'] === 'DELETE' && $doc['stato_esecuzione'] === 'OK') ?
									"<td>
										<a href='#' title='Cancella'><img src='images/remove_grayscale.png' 
											onclick=\"alert('Documento già cancellato...')\" /></a>
									</td>"
								:
								"<td>
									<a href='#' title='Cancella'><img src='images/remove.png' 
										onclick=\"delete_doc('" . $doc['id_final_fse'] . "','" . $doc['CodiceFiscale']. "','$dt_inizio','$dt_fine');\" /></a>
								</td>"
							)
							: 
							"<td>
								<a href='#' title='Cancella'><img src='images/remove_grayscale.png' 
									onclick=\"alert('Non è possibile cancellare un documento che non ha ancora terminato il caricamento...')\" /></a>
							</td>"
						) .
					"</tr>";
			}
		}
		mssql_free_result($_rs);
?>
			</tbody> 
		</table>
<?
		footer_paginazione($conta);
	}	

	if($conta > 0) {
?>
		<p style="float:right; margin: 6px 10px 0 0"><?=$conta?> risultati trovati.</p>
<?	} ?>
	</div>
</div>

<?	exit(0); ?>