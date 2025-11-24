<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 67;
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


$where=" WHERE 1=1";
if(isset($_REQUEST['regime'])and($_REQUEST['regime']!=0)) {
	$where.=" and idregime=".$_REQUEST['regime'];
	$idnorm=$_REQUEST['regime'];
}	
if(isset($_REQUEST['in'])and($_REQUEST['in']!=0)) {
	$data_in=substr($_REQUEST['in'],3,2)."/".substr($_REQUEST['in'],0,2)."/".substr($_REQUEST['in'],6,4);	
	$where.=" and ultimo_trat>=CONVERT(DATETIME, '".$data_in."', 102)";
	$in=$_REQUEST['in'];
}
if(isset($_REQUEST['out'])and($_REQUEST['out']!=0)) {
	$data_out=substr($_REQUEST['out'],3,2)."/".substr($_REQUEST['out'],0,2)."/".substr($_REQUEST['out'],6,4);
	//$data=$_REQUEST['out']; 
	$where.=" and ultimo_trat<=CONVERT(DATETIME, '".$data_out."', 102)";
	$out=$_REQUEST['out'];
}


$conta=0;
$fgl=1;
if($where!=" WHERE 1=1"){

	$query="SELECT * FROM re_statistica_dt $where order by cognome ASC";
	$_SESSION['query_ex']=$query;
	$rs = mssql_query($query, $conn);
	//echo($query);
	if(!$rs) error_message(mssql_error());

	$conta=mssql_num_rows($rs);
	$fgl=0;
}
?>
<script type="text/javascript">
function reload_review(regime,datain,dataout){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="report_lista_dt.php?regime="+regime+"&in="+datain+"&out="+dataout;;
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 			<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">home</a></div>
            <div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>          
        </div>


<div class="titoloalternativo">
            <h1>Elenco Pazienti con protocollo DT e Successo del trattamento</h1>	        
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="ajax_report_excel.php?fun=4";
			$xml="";$doc="";$pdf="";
			include_once('include_stampa.php');
			?>
</div>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
	<div class="testo_mask">Filtra per regime</div>
		<div class="campo_big " >
			<select  class="scrittura" name="idnormativa" id="idnormativa">
				<option selected="" value="0">tutti</option>
				<?
				$query="SELECT idregime,regime,normativa FROM regime INNER JOIN normativa ON regime.idnormativa = normativa.idnormativa WHERE (regime.stato = 1) AND (regime.cancella = 'n')";
				$rs_op = mssql_query($query, $conn);
				while($row_op=mssql_fetch_assoc($rs_op)){
				?>
					<option <?if($idnorm==$row_op['idregime']) echo("selected")?> value="<?=$row_op['idregime']?>"><?=$row_op['normativa']." ".$row_op['regime']?></option>
				<?}	?>
			</select>
		</div>
</span>

<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
	<div class="testo_mask">Data inizio</div>
		<div class="campo_big ">
			<input class="campo_data" type="text" name="data_in" id="data_in" value="<?=substr($_REQUEST['in'],0,10)?>"/>
		</div>
</span>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
		<div class="testo_mask">Data fine</div>
		<div class="campo_big " style="float:left;padding-right:20px">
			<input class="campo_data" type="text" name="data_out" id="data_out" value="<?=substr($_REQUEST['out'],0,10)?>"/>
		</div>

		<div class="campo_big ">
			<input type="button" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#idnormativa').val(),$('#data_in').val(),$('#data_out').val());" />
		</div>
</span>		
		
<?
	if(($conta==0)and($fgl==0)){
	?>
	
	
	<div class="info">Nessun paziente trovato.</div>
	
	
	<?
	exit();
	}else if($fgl==1){
	?>
	
	<div class="info">Selezionare un valore dai filtri posti in alto e premere il tasto esegui filtro per visualizzare il report.</div>
	<?
	exit();
	}?>
	
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>Codice</th> 
	<th>Cognome</th> 
	<th>Nome</th> 
    <th>Data Ric. Autorizz.</th>
	<th>Tipologia</th>
	<th>Esiste DT</th>
	<th>Successo</th>
	<th>Motivazione</th>	
	<th>Data Primo trattamento</th>
	<th>Data Ultimo trattamento</th> 
	<th>Regime/Normativa</th>
	<th>Diagnosi</th>		
	<!--<td class="no_print">Anag</td>-->
	<td class="no_print">Amm</td>
	<!--<td class="no_print">San</td>-->
</tr> 
</thead> 
<tbody> 

<?
$div="sanitaria";
$idutente="";
$data_aut="";
while($row = mssql_fetch_assoc($rs)){
		if(($idutente!="")and($data_aut!="")and(($data_aut!=formatta_data($row['data']))or($idutente!=$row['IdUtente']))){
		?>		
		<tr> 
		 <td><?=$idutente?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$data_aut?></td>
		 <td><?=substr($tipologia,0,-3)?></td>
		 <td><?=$dt?></td>
		 <td><?=$su?></td> 
		 <td><?=$motivazione?></td> 	
		 <td><?=$primo_trat?></td> 
		 <td><?=$ultimo_trat?></td>
		 <td><?=$regime?></td>
		 <td><?=$diagnosi?></td>		
		 <!--<td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$idutente?>');" href="#"><img src="images/anagrafica.png" /></a></td> -->
		 <td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$idutente?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <!--<td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$idutente?>');" href="#"><img src="images/cartella.png" /></a></td> -->
		</tr> 
		<?	
		$tipologia="";
		$primo_trat="30/12/2999";
		$ultimo_trat="";
		}
	
		$idutente=$row['IdUtente'];
		$cognome=pulisci_lettura($row['Cognome']);
		$nome=pulisci_lettura($row['Nome']);		
		$data_aut=formatta_data($row['data']);		
		if($primo_trat>formatta_data($row['primo_trat'])) $primo_trat=formatta_data($row['primo_trat']);
		if($ultimo_trat<formatta_data($row['ultimo_trat'])) $ultimo_trat=formatta_data($row['ultimo_trat']);
		$regime=pulisci_lettura($row['normativa']." - ".$row['regime']);
		$dt=$row['dt'];
		$su=$row['su'];
		$tipologia.=pulisci_lettura($row['tipologia'])." - ";
	    $diagnosi=pulisci_lettura($row['Diagnosi']);
		$motivazione=pulisci_lettura($row['motivazione']);	
		if($dt=="on") 
			$dt="SI"; 
			else
			$dt="-";
		if($su=="on") 
			$su="SI"; 
			else
			$su="-";			
}
?>		
		<tr> 
		 <td><?=$idutente?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$data_aut?></td>
		  <td><?=substr($tipologia,0,-3)?></td>
		 <td><?=$dt?></td>
		 <td><?=$su?></td> 
		 <td><?=$motivazione?></td> 	
		 <td><?=$primo_trat?></td> 
		 <td><?=$ultimo_trat?></td>
		 <td><?=$regime?></td>
		 <td><?=$diagnosi?></td>		
		 <!--<td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$idutente?>');" href="#"><img src="images/anagrafica.png" /></a></td> -->
		 <td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$idutente?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <!--<td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$idutente?>');" href="#"><img src="images/cartella.png" /></a></td> -->
		</tr> 
		<?	

mssql_free_result($rs);	
mssql_close($conn);
?>
</tbody> 
</table> 

<? footer_paginazione($conta);?>
</div></div>


<script>
$(document).ready(function() {
	$(".campo_data").mask("99/99/9999");	
	$("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>

<script type="text/javascript">

function change_tab_edit(iddiv)
{
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="re_pazienti_amministrativa_edita_impegnativa.php?do=edit&id="+iddiv;
$("#lista_impegnative").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });


}

 </script>
 
 
<?
exit(0);

?>

