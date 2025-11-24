<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
include_once('include/function_page.php');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$conn = db_connect();

$where = '';
$id_paziente = $data_dal = $data_al = '';

if(isset($_REQUEST['data_dal']) && $_REQUEST['data_dal'] !== "") {
	$where .= " AND rpi.DataDimissione  >= CONVERT(datetime, '".str_replace('-', '', $_REQUEST['data_dal'])." 00:00:00:000')";
	$data_dal= $_REQUEST['data_dal'];
}

if(isset($_REQUEST['data_al']) && $_REQUEST['data_al'] !== "") {
	$where .= $where .= " AND rpi.DataDimissione  <= CONVERT(datetime, '".str_replace('-', '', $_REQUEST['data_al'])." 00:00:00:000')";
	$data_al = $_REQUEST['data_al'];
}

?>

<script type="text/javascript">
	// AZIONE DEL TASTO DI RICERCA
	function search(data_inizio, data_fine) {
		$("#layer_nero2").toggle();
		$('#wrap-content').innerHTML="";
		pagina_da_caricare="report_cartelle_aperte_pazienti_dimessi.php?data_dal="+data_inizio+"&data_al="+data_fine;
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
			<h1>Elenco pazienti dimessi con cartelle aperte</h1>
			<div class="stampa">
				<a onclick="javascript:(alert('Premere OK e attendere l\'elaborazione del documento..'));" 
					href="include/export_sql_xls.php?rep=rep_cart_usr_dimessi&where=<?=rawurlencode($where)?>"><img src="images/excel-button.png"></a>
			</div>
		</div>

		<span style="border-bottom: 0px none; margin-bottom: 15px; width:100% !important" class="rigo_mask no_print">		
			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">Cerca dal</label><br>
				<input type="date" name="data_dal" id="data_dal" <? if($data_dal) echo 'value="'.$data_dal.'"'; ?> >
			</div>

			<div class="campo_big " style="float:left;padding-right:10px">
				<label class="testo_mask">al</label><br>
				<input type="date" name="data_al" id="data_al" <? if($data_al) echo 'value="'.$data_al.'"'; ?> >
			</div>
	
			<div class="campo_big "><br>
				<input type="button" style="padding: 0px 10px" name="esegui filtro" value="esegui filtro" onclick="search($('#data_dal').val(),$('#data_al').val());" />
			</div>
			<br>
			<p>Se i campi data sono vuoti, il conteggio non ha limiti temporali.</p>
		</span>

		<table id="ter_farm_list" class="tablesorter" cellspacing="1"> 
			<thead> 
				<tr>
					<th>ID CARTELLA</th>
					<th>CODICE CARTELLA</th>
					<th>NORMATIVA | REGIME</th>
					<th>ID UTENTE</th>
					<th>UTENTE</th>
				</tr>
			</thead>
			<tbody>
<?
		$_query = "SELECT DISTINCT uc.id ,uc.codice_cartella, CONCAT(norm.normativa, ' | ', reg.regime) as nome, uc.idutente, CONCAT(usr.Nome, ' ', usr.Cognome) AS utente
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
		$_rs = mssql_query($_query, $conn);			
		$conta = mssql_num_rows($_rs);
		
		if($conta == 0) {
			echo "<tr>
					<td colspan='5'>Nessun record trovato</td>
				</tr>";
		} else {
			while($row = mssql_fetch_assoc($_rs)) { ?>
				<tr>
					<td><?=$row['id']?></td>
					<td><?=$row['codice_cartella']?></td>
					<td><?=$row['nome']?></td>
					<td><?=$row['idutente']?></td>
					<td><?=$row['utente']?></td>
				</tr>
<?php		}
		}
		
		mssql_free_result($_rs); 
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
