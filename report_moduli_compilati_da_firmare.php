<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');

if ($principale==true)
{
$nome_pagina="Benvenuto";
header_new($nome_pagina,false);
barra_top_new();
top_message();
menu_new();
$principale=false;
}

$conn = db_connect();

$where2 = " WHERE 1=1 "; //WHERE (nome_operatore <> 'admin') AND (nome_operatore <> 'Software Administrator') AND (id_paziente <> 922)";
$regime = $op = $modulo = $data_creazione = "";
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
if(isset($_REQUEST['data_creazione']) && $_REQUEST['data_creazione'] !== "") {
	$where2 .= " AND data_creazione <= CONVERT(datetime, '".str_replace('-', '', $_REQUEST['data_creazione'])." 00:00:00:000')";
	$data_creazione = $_REQUEST['data_creazione'];
}
?>
<script type="text/javascript">
function reload_review(nome_operatore,idregime,nome_modulo,data_creazione){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	nome_operatore = nome_operatore.replace(/ /gi, "%20");
	nome_operatore = nome_operatore.replace(/'/gi, "");
	nome_modulo = nome_modulo.replace(/ /gi, "%20");
	pagina_da_caricare="report_moduli_compilati_da_firmare.php?op="+nome_operatore+"&regime="+idregime+"&modulo="+nome_modulo+"&data_creazione="+data_creazione;
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
		$("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
		
	});	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 			<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">home</a></div>
            <div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>          
        </div>

<div class="titoloalternativo">
    <h1>Moduli compilati da firmare</h1>	         
	<div class="stampa">
		<a onclick="javascript:(alert('Premere OK e attendere l\'elaborazione del documento..'));" href="include/export_sql_xls.php?rep=rep_allegati_non_caric&where=<?=rawurlencode($where2)?>"><img src="images/excel-button.png"></a>
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
						WHERE (nome <> 'admin') AND (nome <> 'Software Administrator') 
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
			<input type="date" name="data_creazione" id="data_creazione" 
				<? if($data_creazione)
						//echo 'value="'.$data_creazione.'" max="'.date('Y-m-d', strtotime('+1 year', strtotime($data_creazione))).'"';
						echo 'value="'.$data_creazione.'" max="'.date('Y-m-d', strtotime('+1 year')).'"';
				   else 
						//echo 'value="'.date('Y-m-d').'"  max="'.date('Y-m-d', strtotime('+1 year')).'"';
						echo 'max="'.date('Y-m-d', strtotime('+1 year')).'"';
			?> >
			<!--<input type="date" name="data_creazione" id="data_creazione" >-->
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
			<input type="button" style="padding: 0px 10px" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#nome_operatore').val(),$('#idnormativa').val(),$('#idmoduloversione').val(),$('#data_creazione').val());" />
		</div>
		<br>
		<p>Se il campo data è vuoto, vengono visualizzati i moduli le cui scadenze hanno data inferiore o uguale ai prossimi 365 giorni.<br>Tutti i risultati sono limitati a 365 giorni.</p>
</span>



<?		
	if(isset($_REQUEST['data_creazione']) ) 	// solo dopo aver remuto sul btn di avvio ricerca
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
				<th>PROGRESSIVO</th>
				<th>NUMERO ALLEGATO</th>			
				<th>DATA COMPILAZIONE</th>
			</tr>
		</thead>
		<tbody>
	<?
		$query="SELECT * FROM re_report_istanze_compilate_da_firmare" . $where2 . " ORDER BY data_creazione ASC";		
		//echo $query;
		$rs = mssql_query($query, $conn);
		$conta=mssql_num_rows($rs);
		
		while($row = mssql_fetch_assoc($rs)) 
		{
			echo "<tr>
					<td>".$row['normativa']."</td>
					<td>".$row['regime']."</td>
					<td>".$row['nome_operatore']."</td>
					<td>".$row['codice_cartella']."/".$row['versione_cartella']."</td>
					<td>".$row['cognome_paziente']."</td>
					<td>".$row['nome_paziente']."</td>
					<td><a href=\"#".$row['id_cartella']."#".$row['id_modulo_versione']."&mod\" onclick=\"javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=".$row['id_paziente']."');\">".$row['nome_modulo']."</a></td>
					<td style='text-align: center;'>".$row['id_inserimento']."</td>
					<td style='text-align: center;'>". ($row['stato_firma_allegato'] == 's' && $row['stato_firma_allegato2'] == 'a' ? 'Allegato 2' : 'Allegato 1' ) ."</td>
					<td style=\"text-align:center\">".formatta_data($row['data_creazione'])."</td>
				</tr>";
		}
		
	?>
		</tbody> 
	</table>
<? 
	footer_paginazione($conta);
}
mssql_free_result($rs);
?>
<p style="float:right; margin: 6px 10px 0 0"><?=$conta?> risultati trovati.</p>
</div></div>


<?
exit(0);

?>

