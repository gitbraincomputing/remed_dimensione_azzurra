<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 63;
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

$where="WHERE 1=1 ";
if(isset($_REQUEST['idnorm'])and($_REQUEST['idnorm']!=0)) {	 
	$where.="AND idregime=".$_REQUEST['idnorm'];
	$idnorm=$_REQUEST['idnorm'];	
}else{
	$idnorm=0;
}

if(isset($_REQUEST['data_asl'])and($_REQUEST['data_asl']!=0)) {	 
	if($_REQUEST['data_asl']==1){
		$where.="AND DataAutorizAsl is not null";			
	}else{
		$where.="AND DataAutorizAsl is null";	
	}
	$data_asl=$_REQUEST['data_asl'];
}else{
	$data_asl=0;
}


$conta=0;
$query="SELECT * FROM re_impegnative_attive $where order by idregime ASC, idnormativa ASC, Cognome ASC, Nome ASC, DataAutorizAsl ASC";
$_SESSION['query_ex']=$query;
$rs = mssql_query($query, $conn);
//echo($query);
if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

?>
<script type="text/javascript">
function reload_review(idnormativa,data_asl){	
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="report_impegnative_aperte.php?idnorm="+idnormativa+"&data_asl="+data_asl;
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
            <h1>Elenco Pazienti: impegnative aperte</h1>	        
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="ajax_report_excel.php?fun=7";
			$xml="";$doc="";
			$pdf="pdfclass/report_impegnative_aperte_pdf.php";
			include_once('include_stampa.php');
			?>
</div>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
		<div class="testo_mask">Filtra per regime</div>
		<div class="campo_big " style="float:left;padding-right:10px">
			<select  class="scrittura" name="idnormativa" id="idnormativa">
				<option selected="selected" value="0">tutti</option>
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
		<div class="testo_mask">Filtra per Data Autoriz ASL</div>
		<div class="campo_big " style="float:left;padding-right:10px">
			<select  class="scrittura" name="data_asl" id="data_asl">
				<option value="0"<?if($data_asl==0) echo("selected");?>>tutti</option>
				<option value="1"<?if($data_asl==1) echo("selected");?>>Solo Valorizzate</option>
				<option value="2"<?if($data_asl==2) echo("selected");?>>Non Valorizzate</option>
			</select>
		</div>
		<div class="campo_big ">
			<input type="button" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#idnormativa').val(),$('#data_asl').val());" />
		</div>
</span>
		
<?
	if($conta==0){
	?>
	<div class="info">Nessun paziente trovato.</div>
	<?
	exit();}
	else{
	?>
	<div class="info">Numero pianificazioni moduli per pazienti trovati: <?=$conta?></div>	
	<?}?>
	
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>Regime/Normativa</th>
	<th>Codice</th> 
	<th>Cognome</th> 
	<th>Nome</th> 
    <th>Data Autorizz. ASL</th>	
	<th>Prot Autorizz. ASL </th>	
	<th>Med. Responsabile</th>
	<th>Case Manager</th>	
	<th>Protocollo DT</th>
	<td class="no_print">Amm</td>
	
</tr> 
</thead> 
<tbody> 

<?
$div="sanitaria";
while($row = mssql_fetch_assoc($rs))   
{
		$idutente=$row['IdUtente'];
		$cognome=pulisci_lettura($row['Cognome']);
		$nome=pulisci_lettura($row['Nome']);
		$regime=$row['normativa']." - ".$row['regime'];	
		$data_aut=formatta_data($row['DataAutorizAsl']);
		$prot_aut=$row['ProtAutorizAsl'];		
		$med_resp=$row['medico_responsabile'];
		$case_man=$row['case_manager'];
		$protocollo=pulisci_lettura($row['codice_protocollo'])." - ".pulisci_lettura($row['protocollo']);
	
		?>		
		<tr> 
		  <td><?=$regime?></td>
		 <td><?=$idutente?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$data_aut?></td> 
		 <td><?=$prot_aut?></td> 
		 <td><?=$med_resp?></td>
		 <td><?=$case_man?></td>
		 <td><?=$protocollo?></td>
		 <td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$idutente?>');" href="#"><img src="images/impegnativa.png" /></a></td> 	 
		</tr> 
		<?		
}

mssql_free_result($rs);	
mssql_close($conn);
?>
</tbody> 
</table> 

<? footer_paginazione($conta);?>
</div></div>


<script>
$(document).ready(function() {
	
		
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

