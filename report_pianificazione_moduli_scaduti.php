<?
ini_set('memory_limit', '1024M');
ini_set('mssql.timeout', 60 * 10); // 10 min

include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');


$conn = db_connect();
$totale_righe = 0;
$where2 = "WHERE (nome_operatore <> 'admin') AND (nome_operatore <> 'Software Administrator') AND (id_paziente <> 605)";
$regime = $op = $modulo = $data_scadenza = "";
if(isset($_REQUEST['op']) && $_REQUEST['op'] !== "") {
	$where2 .= " AND nome_operatore LIKE '%".$_REQUEST['op']."%'";
	$op = $_REQUEST['op'];
}
if(isset($_REQUEST['regime']) && $_REQUEST['regime'] !== "") {
	$where2 .= " AND idregime = ".$_REQUEST['regime'];
	$regime = $_REQUEST['regime'];
}
if(isset($_REQUEST['modulo']) && $_REQUEST['modulo'] !== "") {
	$where2 .= " AND nome_modulo = '".$_REQUEST['modulo'] . "'";
	$modulo = $_REQUEST['modulo'];
}
if(isset($_REQUEST['data_scadenza']) && $_REQUEST['data_scadenza'] !== "") {
	$where2 .= " AND data_scadenza <= CONVERT(datetime, '".str_replace('-', '', $_REQUEST['data_scadenza'])." 00:00:00:000')";
	$data_scadenza = $_REQUEST['data_scadenza'];
}
?>
<script type="text/javascript">
function reload_review(nome_operatore,idregime,nome_modulo,data_scadenza){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	nome_operatore = nome_operatore.replace(/ /gi, "%20");
	nome_operatore = nome_operatore.replace(/'/gi, "");
	nome_modulo = nome_modulo.replace(/ /gi, "%20");
	pagina_da_caricare="report_pianificazione_moduli_scaduti.php?op="+nome_operatore+"&regime="+idregime+"&modulo="+nome_modulo+"&data_scadenza="+data_scadenza;
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
</script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() 
	{			
		$.mask.addPlaceholder('~',"[+-]");	
		$(".campo_data").mask("99/99/9999");
		$(".solo_lettere").validation({ type: "alpha", add:"' "});	
		$("table").tablesorter({
			sortList : [[8,0], [7,0]],	// ordina prima per giorni e poi per data, per sicurezza
			widthFixed: true, 
			widgets: ['zebra']}).tablesorterPager({container: $("#pager")
		}); 
	});	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 			<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">home</a></div>
            <div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>          
        </div>

<div class="titoloalternativo">
	<h1>Overview scadenze moduli</h1>	         
	<div class="stampa">
		<a onclick="javascript:(alert('Premere OK e attendere l\'elaborazione del documento..'));" href="include/export_sql_xls.php?rep=rep_moduli_scaduti&where=<?=rawurlencode($where2)?>"><img src="images/excel-button.png"></a>
	</div>
</div>

<span style="border-bottom: 0px none; margin-bottom: 15px; width:100% !important" class="rigo_mask no_print">
		
		<div class="campo_big " style="float:left;padding-right:10px">
		<label class="testo_mask">Filtra per regime</label><br>
			<select  class="scrittura" name="idnormativa" id="idnormativa">
				<option selected="" value="">tutti</option>
				<?
				$query="SELECT n.normativa, r.idregime, r.regime FROM regime AS r
						INNER JOIN normativa AS n ON r.idnormativa = n.idnormativa
						WHERE r.stato = 1
						ORDER BY normativa ASC";
				$rs_op = mssql_query($query, $conn);
				while($row_op=mssql_fetch_assoc($rs_op)){
				?>
					<option <?if($regime==$row_op['idregime']) echo("selected")?> value="<?=$row_op['idregime']?>"><?=$row_op['normativa']." - ".$row_op['regime']?></option>
				<?}	?>
			</select>
		</div>
		
		<div class="campo_big " style="float:left;padding-right:10px">
		<label class="testo_mask">Filtra per operatore</label><br>
			<select  class="scrittura" name="nome_operatore" id="nome_operatore">
				<option selected="" value="">tutti</option>
				<?
				$query="SELECT nome FROM operatori
						WHERE (nome <> 'admin') AND (nome <> 'Software Administrator') AND cancella = 'n'
						GROUP BY nome
						ORDER BY nome";
				$rs_op = mssql_query($query, $conn);
				while($row_op=mssql_fetch_assoc($rs_op)){
				?>
					<option <?if($op==trim($row_op['nome'])) echo("selected")?> value="<?=trim($row_op['nome'])?>"><?=strtoupper(trim($row_op['nome']))?></option>
				<?}	?>
			</select>
		</div>
		
		<div class="campo_big " style="float:left;padding-right:10px">
		<label class="testo_mask">Visualizza fino al</label><br>
			<input type="date" name="data_scadenza" id="data_scadenza" 
				<? if($data_scadenza)
						//echo 'value="'.$data_scadenza.'" max="'.date('Y-m-d', strtotime('+1 year', strtotime($data_scadenza))).'"';
						echo 'value="'.$data_scadenza.'" max="'.date('Y-m-d', strtotime('+1 year')).'"';
				   else 
						//echo 'value="'.date('Y-m-d').'"  max="'.date('Y-m-d', strtotime('+1 year')).'"';
						echo 'max="'.date('Y-m-d', strtotime('+1 year')).'"';
			?> >
			<!--<input type="date" name="data_scadenza" id="data_scadenza" >-->
		</div>

		<br><br><p>&nbsp;</p>
		
		<div class="campo_big " style="float:left;padding-right:10px">
		<label class="testo_mask">Filtra per modulo</label><br>
			<select class="scrittura" name="idmoduloversione" id="idmoduloversione">
				<option selected="" value="">tutti</option>
				<?
				$query="SELECT nome FROM moduli
						WHERE (nome LIKE 'Cartella Clinica (%' OR nome LIKE '% FSE%')
						GROUP BY nome
						ORDER BY nome ASC";
				$rs_mod = mssql_query($query, $conn);
				while($row_mod=mssql_fetch_assoc($rs_mod)){
				?>
					<option <?if($modulo==$row_mod['nome']) echo("selected")?> value="<?=$row_mod['nome']?>"><?=$row_mod['nome']?></option>
				<?}	?>
			</select>
		</div>		
		
		<div class="campo_big "><br>
			<input type="button" style="padding: 0px 10px" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#nome_operatore').val(),$('#idnormativa').val(),$('#idmoduloversione').val(),$('#data_scadenza').val());" />
		</div>
		<br>
		<p>Se il campo data è vuoto, vengono visualizzati i moduli le cui scadenze hanno data inferiore o uguale ai prossimi 365 giorni.<br>Tutti i risultati sono limitati a 365 giorni.</p>
		
</span>



<?		
	if(isset($_REQUEST['data_scadenza']) ) 	// solo dopo aver remuto sul btn di avvio ricerca
	{ 
?>
	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
			<tr>
				<th>NORMATIVA</th>
				<th>REGIME</th>
				<th>OPERATORE</th>
				<th>CARTELLA</th>
				<th>COGNOME PAZIENTE</th>
				<th>NOME PAZIENTE</th>
				<th>MODULO</th>
				<th>DATA SCADENZA</th>
				<th>GIORNI</th>
			</tr>
		</thead>
		<tbody>
	<?
	
		$oggi = new DateTime();
		
	// ESEGUO LA VISTA PER RESTITUIRMI I MODULI CON SCADENZA PUNTUALE
		$query_scad_punt="SELECT normativa, regime, nome_operatore, codice_cartella, versione, cognome_paziente, nome_paziente, nome_modulo, data_scadenza, id_cartella, id_modulo_versione, id_paziente 
							FROM re_moduli_scaduti_non_compilati_con_scadenza_puntuale " . $where2;
		//echo $query_scad_punt; echo '<br><br>';
		$rs_scad_punt = mssql_query($query_scad_punt, $conn);
		
		$totale_righe = $totale_righe + mssql_num_rows($rs_scad_punt);

		while($row_scad_punt = mssql_fetch_assoc($rs_scad_punt)) {
			echo "<tr>
					<td>".$row_scad_punt['normativa']."</td>
					<td>".$row_scad_punt['regime']."</td>
					<td>".$row_scad_punt['nome_operatore']."</td>
					<td>".$row_scad_punt['codice_cartella']."/".$row_scad_punt['versione']."</td>
					<td>".$row_scad_punt['cognome_paziente']."</td>
					<td>".$row_scad_punt['nome_paziente']."</td>
					<td><a href=\"#".$row_scad_punt['id_cartella']."#".$row_scad_punt['id_modulo_versione']."&mod\" onclick=\"javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=".$row_scad_punt['id_paziente']."');\">".$row_scad_punt['nome_modulo']."</a></td>
					<td>".formatta_data($row_scad_punt['data_scadenza'])."</td>";
					
					$data_scadenza = new DateTime(date('Y-m-d', strtotime($row_scad_punt['data_scadenza'])));
					$diff_giorni = ceil(($data_scadenza->format('U') - $oggi->format('U')) / (60*60*24));
			  echo "<td>". $diff_giorni ."</td>
				</tr>";
		}
		mssql_free_result($rs_scad_punt);
		
	// ESEGUO LA VISTA PER RESTITUIRMI I MODULI CON SCADENZA CICLICA E CON DATA PREFISSATA
 		$query_scad_cicl="SELECT normativa, regime, nome_operatore, codice_cartella, versione, cognome_paziente, nome_paziente, nome_modulo, data_scadenza, id_cartella, id_modulo_versione, id_paziente 
							FROM re_moduli_scaduti_non_compilati_con_scadenza_ciclica_data_prefiss " . $where2;
		//echo $query_scad_cicl;
		$rs_scad_cicl = mssql_query($query_scad_cicl, $conn);
		$totale_righe = $totale_righe + mssql_num_rows($rs_scad_cicl);

		while($row_scad_cicl = mssql_fetch_assoc($rs_scad_cicl)) {
			echo "<tr>
					<td>".$row_scad_cicl['normativa']."</td>
					<td>".$row_scad_cicl['regime']."</td>
					<td>".$row_scad_cicl['nome_operatore']."</td>
					<td>".$row_scad_cicl['codice_cartella']."/".$row_scad_cicl['versione']."</td>
					<td>".$row_scad_cicl['cognome_paziente']."</td>
					<td>".$row_scad_cicl['nome_paziente']."</td>
					<td><a href=\"#".$row_scad_cicl['id_cartella']."#".$row_scad_cicl['id_modulo_versione']."&mod\" onclick=\"javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=".$row_scad_cicl['id_paziente']."');\">".$row_scad_cicl['nome_modulo']."</a></td>
					<td>".date('d/m/Y', strtotime($row_scad_cicl['data_scadenza']))."</td>";
					
					$data_scadenza = new DateTime(date('Y-m-d', strtotime($row_scad_cicl['data_scadenza'])));
					$diff_giorni = ceil(($data_scadenza->format('U') - $oggi->format('U')) / (60*60*24));
			  echo "<td>". $diff_giorni ."</td>
				</tr>";
		}
		mssql_free_result($rs_scad_cicl);
		
	?>
		</tbody> 
	</table>
<? 
	footer_paginazione($totale_righe);
}

?>

<p style="float:right; margin: 6px 10px 0 0"><?=$totale_righe?> risultati trovati.</p>
</div>
</div>


<?
exit(0);

?>

