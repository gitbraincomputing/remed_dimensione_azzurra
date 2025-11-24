<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 64;
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
if(isset($_REQUEST['idop'])and($_REQUEST['idop']!=0)) {
	$where.=" and ope_stampa=".$_REQUEST['idop'];
	$idop=$_REQUEST['idop'];
}	
if(isset($_REQUEST['in'])and($_REQUEST['in']!=0)) {
	$data_in=substr($_REQUEST['in'],3,2)."/".substr($_REQUEST['in'],0,2)."/".substr($_REQUEST['in'],6,4);
	//$data=$_REQUEST['in'];
	$where.=" and data_stampa>=CONVERT(DATETIME, '".$data_in."', 102)";
	$in=$_REQUEST['in'];
}
if(isset($_REQUEST['out'])and($_REQUEST['out']!=0)) {
	$data_out=substr($_REQUEST['out'],3,2)."/".substr($_REQUEST['out'],0,2)."/".substr($_REQUEST['out'],6,4);
	//$data=$_REQUEST['out']; 
	$where.=" and data_stampa<=CONVERT(DATETIME, '".$data_out."', 102)";
	$out=$_REQUEST['out'];
}

$conta=0;
$query="SELECT * from re_log_stampa_moduli $where order by id desc";
$_SESSION['query_ex']=$query;
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

?>
<script type="text/javascript">
function reload_review(idpaz,datain,dataout){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="report_lista_stampe.php?idop="+idpaz+"&in="+datain+"&out="+dataout;;
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
            <h1>Report Operazioni di Stampa Moduli</h1>	      
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="ajax_report_excel.php?fun=2";
			$xml="";$doc="";$pdf="";
			include_once('include_stampa.php');
			?>
</div>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
	<div class="testo_mask">Raggruppa per operatore</div>
		<div class="campo_big ">
			<select  class="scrittura" name="idoperatore" id="idoperatore">
				<option selected="" value="0">tutti</option>
			<?
			$query="SELECT * FROM operatori WHERE ((status=1) and (cancella='n')) order by nome asc";
			$rs_op = mssql_query($query, $conn);
			while($row_op=mssql_fetch_assoc($rs_op)){
			?>
				<option <?if($idop==$row_op['uid']) echo("selected")?> value="<?=$row_op['uid']?>"><?=$row_op['nome']?></option>
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
			<input type="button" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#idoperatore').val(),$('#data_in').val(),$('#data_out').val());" />
		</div>
</span>		
<?
	if($conta==0){
	?>
	<div class="info">Nessuna stampa effettuata.</div>
	<?
	exit();}?>
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>
    <th>data / ora</th> 
	<th>operatore</th> 
	<th>modulo</th> 
	<th>paziente</th>
	<th>cartella clinica</th>
	<th class="no_print">tipo</th>
	<th class="no_print">visualizza</th>
	
</tr> 
</thead> 
<tbody> 

<?
$div="sanitaria";
while($row = mssql_fetch_assoc($rs))   
{
		$id=$row['id'];
		$cognome_paziente=utf8_encode($row['Cognome']);
		$nome_paziente=utf8_encode($row['Nome']);
		$operatore=utf8_encode($row['operatore']);		
		$data_ora_stampa=formatta_data($row['data_stampa'])." ".$row['ora_stampa'];		
		$modulo=utf8_encode($row['modulo']);
		if($row['id_modulo']=='99999') $modulo='anterpima_cartella';	
		$nome_file=utf8_encode($row['nome_file']);		
		$operatore=utf8_encode($row['operatore']);		
		$codice_cartella=utf8_encode($row['codice_cartella']);
		$versione=$row['versione'];
		$tipo=$row['tipo'];
		
		if ($codice_cartella)
		{
		$ver=convert_versione($versione);
		$cartella=$codice_cartella."/".$ver;
		}
		else
		$cartella="";		
		$paziente=$cognome_paziente." ".$nome_paziente;		
		
		switch (trim($tipo)) {
			case "pdf":
				$tipo_file="page_white_acrobat.png";
				$alt_file="pdf";
				break;
			case "word":
				$tipo_file="page_word.png";
				$alt_file="word";
				break;			
   		}
		if($modulo=='anterpima_cartella') 
			$id_pdf=1;
		elseif ($alt_file=='pdf')
				$id_pdf=2;
			else
				$id_pdf=0;
		
		?>		
		<tr> 
		 <td><?=$data_ora_stampa?></td> 
		 <td><?=$operatore?></td> 
		 <td><?=$modulo?></td>
		 <td><?=$paziente?></td> 
		 <td><?=$cartella?></td>
		 <td align="center"><img src="images/icons/<?=$tipo_file?>" alt="<?=$alt_file?>" /><span class="hide"><?=$alt_file?></span></td> 
		 <? if (is_file(MODELLI_WORD_DEST_PATH.$nome_file))
		 {
		 ?>
		  <td><a href="view_stampe.php?idreport=<?=$id?>&id_pdf=<?=$id_pdf?>" <?if($id_pdf!=0)echo("target='_blank'")?>><img src="images/view.png"></a></td> 
		  <?
		  }
		  else
		  print("<td>&nbsp;</td>");
		  ?>		 
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

