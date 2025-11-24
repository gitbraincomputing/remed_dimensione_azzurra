<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 81;
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

if(isset($_REQUEST['in'])and($_REQUEST['in']!=0)) {
	$data_in=substr($_REQUEST['in'],3,2)."/".substr($_REQUEST['in'],0,2)."/".substr($_REQUEST['in'],6,4);
	//$data=$_REQUEST['in'];
	$where.=" and datains>=CONVERT(DATETIME, '".$data_in."', 102)";
	$in=$_REQUEST['in'];
}
if(isset($_REQUEST['out'])and($_REQUEST['out']!=0)) {
	$data_out=substr($_REQUEST['out'],3,2)."/".substr($_REQUEST['out'],0,2)."/".substr($_REQUEST['out'],6,4);
	//$data=$_REQUEST['out']; 
	$where.=" and datains<=CONVERT(DATETIME, '".$data_out."', 102)";
	$out=$_REQUEST['out'];
}
if($where==" WHERE 1=1") {
	$where.=" AND (datains > CONVERT(DATETIME, { fn NOW() } - 90, 102))";
	$in=$_REQUEST['in'];
}

$conta=0;
$query="SELECT * FROM re_obiettivi_fkt $where ORDER BY Cognome,Nome,id_istanza_testata, id_istanza_dettaglio";
//echo($query);
$rs = mssql_query($query, $conn);
$_SESSION['query_ex']=$query;
if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

?>
<script type="text/javascript">
function reload_review(datain,dataout){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="report_obiettivi_fkt.php?in="+datain+"&out="+dataout;
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
            <h1>Elenco pazienti con Cartelli Riabilitativa compilata </h1>
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="ajax_report_excel.php?fun=9";
			$xml="";
			$doc="";$pdf="";
			include_once('include_stampa.php');
			?>
</div>
<div class="titoloalternativo">
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
			<input type="button" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#data_in').val(),$('#data_out').val());" />
		</div>
</span>
</div>	
<div class="titoloalternativo2"></div>
	
		
<?
	if($conta==0){
	?>
	
	
	<div class="info">Nessun paziente trovato.</div>
	
	
	<?
	exit();}?>
	
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>    
    <!--<th>Codice</th>--> 
	<th style="width:8%">Data Rilev.</th>
	<th style="width:10%">Cognome</th> 
	<th style="width:10%">Nome</th> 
    <th style="width:5%">Sesso</th>
	<th style="width:8%">Data Nascita</th>
	<th style="width:10%">Obiettivo 1 </th>
	<th style="width:10%">Obiettivo 2 </th>
	<th style="width:10%">Obiettivo 3 </th>
	<th style="width:10%">Obiettivo 4 </th>
	<th style="width:10%">Obiettivo 5 </th>
	<th style="width:10%">Valore Finale</th>	
	<td class="no_print">San</td>
</tr> 
</thead> 
<tbody> 

<?
$div="sanitaria";
$si=0;
$parz=0;
$no=0;
$totale=0;
$totale_fkt=0;
while($row = mssql_fetch_assoc($rs))   
{
		if(($idutente!=$row['IdUtente'])and($idutente!="")){
			//if(($id_istanza_testata!=$row['id_istanza_testata'])and ($id_istanza_testata!="")){
			$totale_fkt++;
			switch($ob1){
				case "No":
					$vObb+=0;
				break;
				case "Parzial.":
					$vObb+=1.5;
				break;
				case "Si":
					$vObb+=2;
				break;
			}
			switch($ob2){
				case "No":
					$vObb+=0;
				break;
				case "Parzial.":
					$vObb+=1.5;
				break;
				case "Si":
					$vObb+=2;
				break;
				
			}
			switch($ob3){
				case "No":
					$vObb+=0;
				break;
				case "Parzial.":
					$vObb+=1.5;
				break;
				case "Si":
					$vObb+=2;
				break;
				
			}
			switch($ob4){
				case "No":
					$vObb+=0;
				break;
				case "Parzial.":
					$vObb+=1.5;
				break;
				case "Si":
					$vObb+=2;
				break;
				
			}
			switch($ob5){
				case "No":
					$vObb+=0;
				break;
				case "Parzial.":
					$vObb+=1.5;
				break;
				case "Si":
					$vObb+=2;
				break;
				
			}
			if($vObb>0)
				$media=round(($vObb/($nObb*2))*100);
				else
				$media=0;
			
			if($media==0)
				$no++;
				else if($media<50)
					$parz++;
					else 
					$si++;
					
			$media=$media." %";
			?>		
			<tr> 
			<!--<td><?=$idutente?></td>--> 
			<td><?=$data_rilevazione?></td>
			<td><?=$cognome?></td> 
			<td><?=$nome?></td> 
			<td><?=$sesso?></td>
			<td><?=$data_nascita?></td>
			<td><?=$ob1?></td> 
			<td><?=$ob2?></td>
			<td><?=$ob3?></td>
			<td><?=$ob4?></td>	
			<td><?=$ob5?></td>	
			<td><?=$media?></td>
			 <td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$idutente?>');" href="#"><img src="images/cartella.png" /></a></td> 		 
			</tr> 		
				<?
				$nObb=0;
				$vObb=0;
			if(($idutente!=$row['IdUtente'])and($idutente!="")){
				?>
				<tr><td></td></tr>
				<?
				$totale++;
				$media=0;
				
					
			}
		}
		if($id_istanza_testata!=$row['id_istanza_testata']) $nObb=0;
		$id_istanza_testata=$row['id_istanza_testata'];	
		$idutente=$row['IdUtente'];		
		$cognome=pulisci_lettura($row['Cognome']);
		$nome=pulisci_lettura($row['Nome']);		
		$data_nascita=formatta_data($row['DataNascita']);
		$data_rilevazione=formatta_data($row['datains']);

		if($row['Sesso']==1) 
			$sesso="uomo";
			else
			$sesso="donna";

		if(($row['idcampo']==17103)or($row['idcampo']==17205)or($row['idcampo']==17443)) {$ob1=$row['valore_combo']; if($ob1!="")$nObb++;}
		if(($row['idcampo']==17106)or($row['idcampo']==17208)or($row['idcampo']==17446)) {$ob2=$row['valore_combo']; if($ob2!="")$nObb++;}
		if(($row['idcampo']==17109)or($row['idcampo']==17211)or($row['idcampo']==17449)) {$ob3=$row['valore_combo']; if($ob3!="")$nObb++;}
		if(($row['idcampo']==17112)or($row['idcampo']==17214)or($row['idcampo']==17452)) {$ob4=$row['valore_combo']; if($ob4!="")$nObb++;}
		if(($row['idcampo']==17115)or($row['idcampo']==17217)or($row['idcampo']==17455)) {$ob5=$row['valore_combo']; if($ob5!="")$nObb++;}
}

mssql_free_result($rs);	
mssql_close($conn);
$totale_fkt++;
		switch($ob1){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
		}
		switch($ob2){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
			
		}
		switch($ob3){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
			
		}
		switch($ob4){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
			
		}
		switch($ob5){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
			
		}
		if($vObb>0)
			$media=round(($vObb/($nObb*2))*100);
			else
			$media=0;
		
		if($media==0)
			$no++;
			else if($media<50)
				$parz++;
				else 
				$si++;
				
		$media=$media." %";
?>
<tr> 
		<!--<td><?=$idutente?></td>--> 
		<td><?=$data_rilevazione?></td>
		<td><?=$cognome?></td> 
		<td><?=$nome?></td> 
		<td><?=$sesso?></td>
		<td><?=$data_nascita?></td>
		<td><?=$ob1?></td> 
		<td><?=$ob2?></td>
		<td><?=$ob3?></td>
		<td><?=$ob4?></td>	
		<td><?=$ob5?></td>	
		<td><?=$media?></td>
		 <td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$idutente?>');" href="#"><img src="images/cartella.png" /></a></td> 		 
		</tr> 
			<?
		if(($idutente!=$row['IdUtente'])and($idutente!="")){
			?>
			<tr></tr>
			<?			
		}
		?>
</tbody> 
</table> 

<? footer_paginazione($conta);

$si_perc=round((100*$si)/$totale_fkt);
$parz_perc=round((100*$parz)/$totale_fkt);
$no_perc=round((100*$no)/$totale_fkt);
?>
</div></div>


<script>
$(document).ready(function() {
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
	  $(".titoloalternativo2").html("Totale pazienti censiti: <strong><?=$totale?></strong> Totale relazioni Fkt: <strong><?=$totale_fkt?></strong><br><br>Ob.Superato: <strong><?=$si?> - <?=$si_perc?>%</strong><br>Ob.Parziale: <strong><?=$parz?> - <?=$parz_perc?>%</strong><br>Ob.Mancato: <strong><?=$no?> - <?=$no_perc?>%</strong>");
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

