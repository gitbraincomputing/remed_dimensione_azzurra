<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');
//ini_set('display_errors', 1);


$conn = db_connect();

$where2 = " WHERE (nome_operatore <> 'admin') AND (nome_operatore <> 'Software Administrator')";
$op = $data_dal = $data_al= "";
if(isset($_REQUEST['op']) && $_REQUEST['op'] !== "") {
	$where2 .= " AND id_operatore = ".$_REQUEST['op'];
	$op = $_REQUEST['op'];
}
if(isset($_REQUEST['data_dal']) && $_REQUEST['data_dal'] !== "") {
	$where2 .= " AND (data_firma_allegato  >= CONVERT(datetime, '".str_replace('-', '', $_REQUEST['data_dal'])." 00:00:00:000') OR
				      data_firma_allegato2 >= CONVERT(datetime, '".str_replace('-', '', $_REQUEST['data_dal'])." 00:00:00:000'))";
	$data_dal= $_REQUEST['data_dal'];
}
if(isset($_REQUEST['data_al']) && $_REQUEST['data_al'] !== "") {
	$where2 .= " AND (data_firma_allegato  <= CONVERT(datetime, '".str_replace('-', '', $_REQUEST['data_al'])." 00:00:00:000') OR 
				      data_firma_allegato2 <= CONVERT(datetime, '".str_replace('-', '', $_REQUEST['data_al'])." 00:00:00:000'))";
	$data_al = $_REQUEST['data_al'];
}
?>
<script type="text/javascript">
function reload_review(id_operatore,data_dal,data_al){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="report_elenco_firme_digitali_emesse.php?op="+id_operatore+"&data_dal="+data_dal+"&data_al="+data_al;
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
    <h1>Conteggio firme digitali emessi</h1>	         
	<div class="stampa">
		<a onclick="javascript:(alert('Premere OK e attendere l\'elaborazione del documento..'));" href="include/export_sql_xls.php?rep=rep_firme_digitali_emesse&where=<?=rawurlencode($where2)?>"><img src="images/excel-button.png"></a>
	</div>
</div>

<span style="border-bottom: 0px none; margin-bottom: 15px; width:100% !important" class="rigo_mask no_print">


		<div class="campo_big " style="float:left;padding-right:10px">
			<label class="testo_mask">Filtra per operatore</label><br>
			<select  class="scrittura" name="nome_operatore" id="nome_operatore">
				<option selected="" value="">tutti</option>
				<?
				$query="SELECT uid, nome FROM operatori
						WHERE (nome <> 'admin') AND (nome <> 'Software Administrator') 
						GROUP BY uid, nome
						ORDER BY nome";
				$rs_op = mssql_query($query, $conn);
				while($row_op=mssql_fetch_assoc($rs_op)){
				?>
					<option <?if($op==trim($row_op['uid'])) echo("selected")?> value="<?=$row_op['uid']?>"><?=strtoupper(trim($row_op['nome']))?></option>
				<?}	?>
			</select>
		</div>
		
		<div class="campo_big " style="float:left;padding-right:10px">
			<label class="testo_mask">Conta dal</label><br>
			<input type="date" name="data_dal" id="data_dal" <? if($data_dal) echo 'value="'.$data_dal.'"'; ?> >
		</div>

		<div class="campo_big " style="float:left;padding-right:10px">
			<label class="testo_mask">al</label><br>
			<input type="date" name="data_al" id="data_al" <? if($data_al) echo 'value="'.$data_al.'"'; ?> >
		</div>
		
		<br><br><p>&nbsp;</p>	
		
		<div class="campo_big "><br>
			<input type="button" style="padding: 0px 10px" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#nome_operatore').val(),$('#data_dal').val(),$('#data_al').val());" />
		</div>
		<br>
		<p>Se i campi data sono vuoti, il conteggio non ha limiti temporali.</p>
</span>



<?		
	$conta = 0;
	
	if(isset($_REQUEST['data_dal']) ) 	// solo dopo aver remuto sul btn di avvio ricerca
	{ 
?>
	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
			<tr>
				<th>OPERATORE</th>
				<th>TOT. ALLEGATI1 FIRMATI</th>
				<th>TOT. ALLEGATI2 FIRMATI</th>
				<th>CONTEGGIO TOTALE</th>
			</tr>
		</thead>
		<tbody>
	<?
		$query="SELECT nome_operatore, SUM(count_firme_alleg1) AS tot_firme_alleg1, SUM(count_firme_alleg2) AS tot_firme_alleg2 FROM re_conta_firme_digitali_emesse" . 
				$where2 . " 
				group by nome_operatore
				order by nome_operatore ASC";		
		//echo $query;
		$rs = mssql_query($query, $conn);
		$conta=mssql_num_rows($rs);
		$i = 1;
		$tot_firme = 0;

		if($conta > 0) {
			while($row = mssql_fetch_assoc($rs)) 
			{
				echo "<tr>
						<td>" . $row['nome_operatore'] . "</td>
						<td style=\"text-align:center\">" . $row['tot_firme_alleg1'] . "</td>
						<td style=\"text-align:center\">" . $row['tot_firme_alleg2']. "</td>
						<td style=\"text-align:center\">" . ($row['tot_firme_alleg1'] + $row['tot_firme_alleg2']) . "</td>
					</tr>";
					
					$tot_firme += $row['tot_firme_alleg1'] + $row['tot_firme_alleg2'];
			}
		} else {
			echo "<tr><td colspan='4' >Nessuna firma trovata</td></tr>";
		}
	?>
		</tbody> 
	</table>
<? 
	footer_paginazione($conta);
	mssql_free_result($rs);
}

if($conta > 0) { ?>
<p>Totale firme: <?=$tot_firme?></p>
<?php } ?>

</div></div>


<?
exit(0);

?>

