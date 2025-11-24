<?php
header("Content-type: text/html; charset=ISO-8859-1");
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
include_once('include/function_page.php');

//$idutente=$id;
function show_allegati($id,$idimp)
{

$idutente=$id;
$conn = db_connect();
if($idimp=="") $idimp=0;
if(($idimp!=0)and($idimp!="")) $que="and (idimpegnativa=$idimp)";
$query = "SELECT * from re_allegati_impegnative where (idutente=$idutente) and(stato=1) and (cancella='n') $que order by idimpegnativa DESC";
//echo($query);	
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);
?>
<script>
$(document).ready(function() {
	
	  $("#table_allegati_amm").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager_allegati_amm")}); 
});
</script>

<div id="lista_impegnative1">
<script>
function conf_cancella(id_al,paz){
if (confirm('sei sicuro di voler cancellare l\'allegato corrente?')){
	document.getElementById('id_allegato').value=id_al;
	  $.ajax({
		   type: "POST",
		   url: "re_pazienti_impegnativa_POST.php?action=del_files&id_all="+id_al+"&paz="+paz,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 reload_allegati(<?=$idutente?>,<?=$idimp?>);
			}
		 });	  
	  }
	else return false;
}
function add_allegati(idpaziente){
	$("#layer_nero2").toggle();
	$('#lista_impegnative1').innerHTML="";
	pagina_da_caricare="re_pazienti_amministrativa.php?do=add_allegati&id="+idpaziente;
	$("#lista_impegnative1").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-3 .tabs-selected").removeClass('tabs-selected');
	 });
}
</script>
<div class="titoloalternativo">
            <h1>Allegati Amministrativi</h1>			
	        <div class="stampa"><a href="javascript:stampa('fragment-3');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
</div>

<?	
	if($conta==0){
	?>		
	<div class="info">Nessun allegato trovato.</div>
		<span class="rigo_mask no_print" style="margin-bottom:15px; border-bottom:0px">
            <div class="testo_mask">Raggruppa per impegnative</div>
				<div class="campo_big ">
					<select name="idimp<?=$i?>" class="scrittura"  onchange="reload_allegati('<?=$idutente?>',this.value);">
					<option value="0" <?if($idimp=="0") echo("selected");?>>tutte</option>
					<?
					$query = "SELECT * from re_pazienti_impegnative where idutente=$idutente order by DataAutorizAsl desc";	
					$rs1 = mssql_query($query, $conn);			
					while($row1 = mssql_fetch_assoc($rs1))   
						{
							$idimpegnativa=$row1['idimpegnativa'];
							$data_auth_asl=formatta_data($row1['DataAutorizAsl']);
							$prot_auth_asl=$row1['ProtAutorizAsl'];
							$regime=$row1['regime'];
						?>
		<option value="<?=$idimpegnativa?>" <?if($idimpegnativa==$idimp) echo("selected");?>><?=$prot_auth_asl." ".$data_auth_asl." - ".$regime?></option>
						<?
						}
					?>
					</select>
				</div>
        </span>	
	<?
	exit();}
	?>
<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_right">
		<a href="#"  onclick="javascript:add_allegati('<?=$idutente?>')" >aggiungi allegato</a>
		</div>
	</div>
	<h1>Elenco allegati</h1>
</div>
<div class="form">
	<form name="form0" action="re_pazienti_impegnativa_POST.php" method="post" id="myForm">
    <input type="hidden" name="action" value="del_files" />
	<div class="nomandatory"><input type="hidden" name="id_allegato" id="id_allegato" value="" /></div>
    <div class="nomandatory"><input type="hidden" name="id_paziente" id="id_paziente" value="<?=$id?>" /></div>
	<div class="rigo_mask no_print" style="margin-bottom:15px; border-bottom:0px">
            <div class="testo_mask">Raggruppa per impegnative</div>
				<div class="campo_big ">
					<select name="idimp<?=$i?>" class="scrittura"  onchange="reload_allegati('<?=$idutente?>',this.value);">
					<option value="0" <?if($idimp==0) echo("selected");?>>tutte</option>
					<?
					$query = "SELECT * from re_pazienti_impegnative where idutente=$idutente order by DataAutorizAsl DESC";	
					$rs1 = mssql_query($query, $conn);			
					while($row1 = mssql_fetch_assoc($rs1))   
						{
							$idimpegnativa=$row1['idimpegnativa'];
							$data_auth_asl=formatta_data($row1['DataAutorizAsl']);
							$prot_auth_asl=$row1['ProtAutorizAsl'];
							$regime=$row1['regime'];
						?>
		<option value="<?=$idimpegnativa?>" <?if($idimpegnativa==$idimp) echo("selected");?>><?=$data_auth_asl." ".$prot_auth_asl." - ".$regime?></option>
						<?
						}
					?>
					</select>
				</div>
        </div>	
<?
$id_imp="";
$open=0;
while($row = mssql_fetch_assoc($rs)) {
	if($row['idimpegnativa']!=$id_imp){
		if($open) echo("</tbody></table>");		
		$id_imp=$row['idimpegnativa'];		
		echo("<div class='titolo_pag'><div class='comandi space_top_print'>");
		echo("Impegnativa prot. <strong>".$row['prot_asl']."</strong> del <strong>".formatta_data($row['data_asl'])."</strong> - regime: <strong>".$row['regime']."</strong>");
		echo("</div></div>");
		$open=1;
		?>
		<table id="table_allegati_amm" class="tablesorter" cellspacing="1"> 
		<thead> 
		<tr> 
			
			<th width="8%" align="center">Tipo file</th>
			<th width="10%" align="center">Data acquisizione</th>	
			<th width="15%" align="center">Operatore</th>	
			<th width="28%" align="left">Descrizione</th>
			<td width="20%" align="left">data di produzione</td>	
			<th width="7%" align="center" class="no_print">Stampa per fatturazione</th>
			<td width="7%" align="center" class="no_print">Visualizza</td>     
			<td width="12%" align="center" class="no_print">Cancella</td> 
			
		</tr> 
		</thead> 
		<tbody> 

		<?	
		}
		
		$id_all=$row['id'];
		$descrizione=$row['descrizione'];
		$file=$row['file_name'];
		$type=$row['type'];		
		$stampa=$row['stampa'];
		$data_prod=$row['data_produzione'];
		if(formatta_data($data_prod)=="01/01/1900") $data_prod="";
		if($stampa=='on') 
			$stampa="SI";
			else
			$stampa="NO";		
		$data_ora=formatta_data($row['datains']);
		$operatore=$row['opeins'];
		$query_op="SELECT uid, nome FROM dbo.operatori WHERE (uid = $operatore)";
		$rs_op = mssql_query($query_op, $conn);
		$row_op = mssql_fetch_assoc($rs_op);
		$nome_op=$row_op['nome'];		
		switch (trim($type)) {
			case "application/pdf":
				$tipo_file="page_white_acrobat.png";
				$alt_file="pdf";
				break;
			case ("image/gif"):
				$tipo_file="page_image.gif";
				$alt_file="gif";
				break;
			case ("image/jpeg"):
				$tipo_file="page_image.gif";
				$alt_file="peg";
				break;
			case ("image/tiff"):
				$tipo_file="page_image.gif";
				$alt_file="tiff";
				break;
			case ("image/png"):
				$tipo_file="page_image.gif";
				$alt_file="png";
				break;
			case ("image/bmp"):
				$tipo_file="page_image.gif";
				$alt_file="bmp";
				break;		
			case "application/msword":
				$tipo_file="page_word.png";
				$alt_file="msword";
				break;
			case "text/plain":
				$tipo_file="page_word.png";
				$alt_file="txt";
				break;
			default:
    			$tipo_file="application.png";
				$alt_file="sconosciuto";
   		}	
		?>
		<tr> 
		 
		 <td align="center"><img src="images/icons/<?=$tipo_file?>" alt="<?=$alt_file?>" /><span class="hide"><?=$alt_file?></span></td> 
		 <td><?=$data_ora?></td>
		 <td><?=$nome_op?></td>		 
		 <td><?=$descrizione?></td>
		 <td><?=formatta_data($data_prod)?></td>
			<td class="no_print"><?=$stampa?></td>		 
		 <td align="center" class="no_print"><a href="view_file.php?idfile=<?=$id_all?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>&sec=imp" ><img src="images/view.png" /></a></td>         
         
		 <?
		 if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and ($_SESSION['UTENTE']->get_tipoaccesso()!=5)){?>
		 <td align="center" class="no_print"><!--<input class="no_print" type="image" src="images/remove.png" title="elimina" onclick="javascript:document.getElementById('id_allegato').value='<?=$id_all?>';"/>-->
		 <img type="submit" src="images/remove.png" alt="elimina"  onclick="conf_cancella('<?=$id_all?>','<?=$id_utente?>');"></td> 		  
		 <?}else echo("<td align='center'>x</td>");?>

		</tr>
		<?
}
?>
</tbody> 
</table>

 
</form>
</div>

<div id="pager_allegati_amm"></div>
	
</div>
<script type="text/javascript" language="javascript">

	function reload_allegati(idpaziente,idimpegnativa){
		$("#layer_nero2").toggle();
		$('#lista_impegnative1').innerHTML="";
		pagina_da_caricare="re_pazienti_amministrativa.php?do=show_allegati&idimp="+idimpegnativa+"&id="+idpaziente;
		$("#lista_impegnative1").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-4 .tabs-selected").removeClass('tabs-selected');
		 });
	}
	
</script>	
	
<?
}


function add_nota(){

$idutente=$_REQUEST['id'];
$conn = db_connect();

?>
<div id="lista_note">
<script>
inizializza();
</script>

<div class="titoloalternativo">
            <h1>Nuova Nota</h1>	        
</div>

<form  method="post" name="form0" action="re_pazienti_impegnativa_POST.php" id="myForm">
	<input type="hidden" value="create_nota" name="action"/>
	<input type="hidden" value="<?=$idutente?>" name="id"/>

	<div class="titolo_pag"><h1>Aggiungi nota</h1></div>

	<div class="blocco_centralcat">

	 
    <div class="riga">
        <div class="rigo_mask rigo_big">
			<div class="testo_mask">nota</div>
			<div class="campo_mask mandatory">
				<textarea name="nota" class="scrittura_campo"></textarea>
			</div>
		</div>

	</div>	
	</div>
	
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	
	</form>
	
    
</div>
</div>
<?

}
function show_presenze()
{
$idutente=$_REQUEST['id'];
$conn = db_connect();
$idricettecodici=$_REQUEST['idricettecodici'];

?>
<div id="sanitaria">
	
	<div class="titoloalternativo">
	            <h1>Gestione Presenze</h1>
				<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
	</div>
	
	<div class="titoloalternativo">
	seleziona la ricetta:           
	<select name="idragr" onchange="view_istanze_test_clinico('<?=$idtest?>','<?=$idpaziente?>',this.value,'<?=$idcartella?>','<?=$cartella?>');">
	<option value="1" <?if ($idragr==1) echo ("selected")?>>raggruppa per storico della cartella corrente</option>
	<option value="2" <?if ($idragr==2) echo ("selected")?>>raggruppa per impegnativa</option>
	<!--<option value="3" <?if ($idragr==3) echo ("selected")?>>raggruppa per cartella clinica</option>-->
	</select>
	
	
	
<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_presenze.php?do=add');">aggiungi presenza</a></div>
		</div>
</div>

<?
$query = "SELECT * from vis_ricettepresenze where idutente=$idutente";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());

if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Presenze</h1>
	</div>
	<div class="info">Non esistiono presenze.</div>
	
	
	<?
	exit();}?>	
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th>regime</th>
	<th>data prestazione</th> 
	<th>quantit&agrave; erogate</th> 
    <td>modifica</td>
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idtipologia=$row1['idtipologia'];

while($row = mssql_fetch_assoc($rs))
{   
		$idpresenza=$row['IdRicettaPresenza'];
		$regime=$row['regime'];
		$DataPrestazione=$row['DataPrestazione'];
		$QuantitaErogate=$row['QuantitaErogate'];
		
		
		?>
		
		<tr> 
		 <td><?=$idtipologia?></td> 
		 <td><?=htmlentities($regime)?></td> 
		 <td><?=formatta_data($DataPrestazione)?></td>
         <td><?=$QuantitaErogate?></td>          
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_tipologie.php?do=edit&id=<?=$idpresenza?>');" href="#"><img src="images/gear.png" /></a></td> 
		 
		</tr> 
		
		<?
}
//}	

?>

</tbody> 
</table> 
<? 
footer_paginazione($conta, 'pager_elenco_presenze');
?>
</div></div>
<script>
$(document).ready(function() {
	
	  $("#table_elenco_presenze").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager_elenco_presenze")}); 
});
</script>


<script type="text/javascript" id="js">




function change_tab_edit(iddiv)
{
//$('#container-4').triggerTab(3); 
//$('#container-4').tabs('add', 'index.php', 'New Tab', 1);
//$('#container-4').tabs('add', '#new-tab', 'New Tab');
//$('#container-4 > #tab_block').append('<li><a href="re_pazienti_amministrativa.php?do=add&id=10"><span>antonio esposito</span></a></li>');
//$('#container-4').find('#tab_block > li >a')..end().tabs();
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="re_pazienti_amministrativa_edita_impegnativa.php?do=edit&id="+iddiv;
$("#lista_impegnative").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });


}

 </script>
	
	
</div>	


<?



}
function show_note(){

$idutente=$_REQUEST['id'];

$conn = db_connect();

$query = "SELECT * from re_note_amministrative where idutente=$idutente order by idnota DESC";	
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());
	
$conta=mssql_num_rows($rs);	

?>

<script type="text/javascript" id="js">


function add_nota(idpaziente){

	$("#layer_nero2").toggle();
	$('#lista_note').innerHTML="";
	pagina_da_caricare="re_pazienti_amministrativa.php?do=add_nota&id="+idpaziente;
	$("#lista_note").load(pagina_da_caricare,'', function(){
    loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 //  $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });
}


 </script>
<div id="lista_note">



<div class="titoloalternativo">
            <h1>Note amministrative</h1>
	        <div class="stampa"><a href="javascript:stampa('fragment-3');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>

</div>

<div class="titoloalternativo">
            <h1><a href="#" onclick="javascript:add_nota('<?=$idutente?>');">aggiungi nota</a></h1>
	     

</div>

<? if ($conta>0){?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>idnota</th> 
	<th>data / ora</th>
	<th>operatore</th> 
	<th>Nota</th> 	
    <th>impegnativa</th> 
	<th>Modifica</th> 
	<th>Cancella</th>
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$idnota=$row['idnota'];
		$nota=$row['nota'];
		$operatore=$row['operatore'];
		$dataora=formatta_data(valid_date($row['datains']))." ".$row['orains'];
	
	
	
		?>
		
		<tr> 
		 <td><?=$idnota?></td>
		 <td><?=$dataora?></td> 
		 <td><?=$operatore?></td> 
		 <td><?=$nota?></td> 
		 <td><?=$impegnativa?></td> 
		 
		 <td align="center"><a onclick="javascript:change_tab_edit('<?=$idimp?>');" ><img src="images/gear.png" /></a></td> 
		 <?if ($_SESSION['UTENTE']->get_tipoaccesso()!=4)
			{
			?>
			 <td align="center"><a onclick="javascript:cancella_impegnativa('<?=$idimp?>','<?=$idutente?>');" ><img src="images/remove.png" /></a></td> 
			<?
			}
			else
			{
			?>
			<td align="center">X</td> 	
			<?
			}
		?>
		</tr> 
		
		
		<?
}	

?>


</tbody> 
</table> 

	<? 
footer_paginazione($conta);
?>

<script>
$(document).ready(function() {
	
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>



<?
}
else
{
?>
<div class="info">nessuna nota amministrativa presente.</div>

<?
}
?>
</div>
<?

}


function add_allegati($id){

$idutente=$id;
$conn = db_connect();

?>
<div id="lista_impegnative">
<script>
inizializza();
</script>
	
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>
	
<div class="titoloalternativo">
            <h1>Nuovo Allegato</h1>	        
</div>


<div class="titolo_pag"><h1>Aggiungi allegati</h1></div>


<div class="blocco_centralcat">
<div class="form">	
	<form name="form0" action="re_pazienti_impegnativa_POST.php" method="post" id="myForm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add_files" />
	<input type="hidden" name="id" value="<?php echo($id);?>" />			
	
    <div class="riga">
    <input id="debug" type="hidden" value="1" name="debug"/>
	<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">
 	<tbody id="tbody">
    <tr class="riga_tab nodrag nodrop">
		<td width="5%" align="left">&nbsp;</td>
		<td width="20%" align="left">impegnativa</td>
		<td width="20%" align="left">data di produzione</td>
        <td width="35%" align="left">descrizione</td>
		<td width="5%" align="left">stampa per fatturazione</td>	
		<td width="20%" align="center">file</td>			
		<td width="10%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
	<?
    $i=1;
    $numerofield=10;
    $style='';
    $class='';
    $dis="";
    while($i<$numerofield)
    {        
        if ($i>1)
        {
        $style="style=\"display:none\"";
        $class="riga_sottolineata_diversa hidden";
        $dis="disabled";
        
        }
        else
        {
        //$nodrag="nodrag=false";
        $style='';
        $class="riga_sottolineata_diversa";
        $dis="";
        }
    ?>
	
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<td align="center"><span class="id_notizia_cat"></span></td>
	<td align="left" class="nodrag nodrop">
		<select name="idimp<?=$i?>" class="scrittura">
			<?
			$query = "SELECT * from re_pazienti_impegnative where idutente=$idutente order by DataAutorizAsl desc";	
			$rs1 = mssql_query($query, $conn);			
			while($row1 = mssql_fetch_assoc($rs1))   
				{
					$idimpegnativa=$row1['idimpegnativa'];
					$data_auth_asl=formatta_data($row1['DataAutorizAsl']);
					$prot_auth_asl=$row1['ProtAutorizAsl'];
					$regime=$row1['regime'];
				?>
<option value="<?=$idimpegnativa?>"><?=$prot_auth_asl." ".$data_auth_asl." - ".$regime?></option>
				<?
				}
			?>
			</select>
	</td>
	<td align="left" class="nodrag nodrop">
		<div id="div_prova<?=$i?>">
			<input type="text" name="data<?=$i?>" id="data<?=$i?>" class="campo_data"/>
		</div>
	</td>
	<td align="left" class="nodrag nodrop"><div id="div_et<?=$i?>" class="mandatory"><textarea name="et<?=$i?>" id="et<?=$i?>" class="scrittura_campo" cols="30" <?=$dis?>/></textarea></div></td>
	<td align="left" class="nodrag nodrop"><input type="checkbox" name="stampa<?=$i?>"/></div>
	<td align="left" class="nodrag nodrop"><div id="div_file<?=$i?>" class="mandatory controllo_file generica"><input type="file" name="ulfile<?=$i?>" <?=$dis?> id="ulfile<?=$i?>" /></div></td>
	<td>&nbsp;</td>	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a>
	</td>
	</tr>
<?
$i++;
}
?>
<tr id="10" class="riga_sottolineata_diversa nodrag nodrop"><td width="20%" align="left">&nbsp;</td><td align="left"><span><a OnClick="javascript:DinamicDiv('0','add','<?=$numerofield?>');"><img src="images/add.png" /></a></td></tr>
</tbody>
</table>      

</div>



<div class="titolo_pag">
	<div class="comandi">
		<input type="submit" title="salva" value="salva" class="button_salva"/>
	</div>
</div>	
</form>
</div>	
</div>
</div>

<?

}


function show_list(){

$idutente=$_REQUEST['id'];

$conn = db_connect();

$query = "SELECT * from re_pazienti_impegnative where idutente=$idutente ORDER BY ording_dim DESC, ording DESC, idimpegnativa DESC";	
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());
	
$conta=mssql_num_rows($rs);	

?>

<script type="text/javascript" id="js">

function change_tab_edit(iddiv){

	$("#layer_nero2").toggle();
	$('#lista_impegnative').innerHTML="";
	pagina_da_caricare="re_pazienti_amministrativa_edita_impegnativa.php?do=edit&id="+iddiv;
	$("#lista_impegnative").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });
}

function cancella_impegnativa(idimp,user){
	
	$("#layer_nero2").toggle();
	$('#lista_impegnative').innerHTML="";
	pagina_da_caricare="re_pazienti_impegnativa_POST.php?do=del&id="+idimp+"&idutente="+user;
	$("#lista_impegnative").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });
}

 </script>
<div id="lista_impegnative">

<div class="titoloalternativo">
            <h1>Elenco impegnative</h1>
	        <div class="stampa"><a href="javascript:stampa('fragment-3');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>

</div>

<? if ($conta>0){?>
<table id="table_elenco_impegnative" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>id</th> 
	<th width="10%">stato</th>
	<!--<th>Centro di Costo</th>-->
	<th>Data Auto.</th> 
	<th>Prot Auto.</th> 	
    <th>Normativa</th> 
    <th>Regime</th> 
	<th>Modifica</th> 
	<th>Cancella</th>
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$idimp=$row['idimpegnativa'];
		$cdc=$row['centrodicosto'];
		$normativa=$row['normativa'];
		$regime=$row['regime'];
		if ($row['DataAutorizAsl']!="") 
		$DataAutorizAsl=formatta_data(valid_date($row['DataAutorizAsl']));
		else
		$DataAutorizAsl="";			
		$ProtAutorizAsl=stripslashes($row['ProtAutorizAsl']);	
		$data_dimissione=valid_date($row['DataDimissione']);		
		if(valid_date($row['DataDimissione'])==""){
			$stato="stato_ok.png";
			$alt_s="in carico";}
			else{
			$stato="stato_blu.png";
			$alt_s="terminata";}
		if($ProtAutorizAsl==$row['CodiceUtente']){
			$stato="stato_arancio.png";
			$alt_s="RNC";
			$ProtAutorizAsl="RNC";
		}
	
		?>
		
		<tr> 
		 <td><?=$idimp?></td>
		  <td align="center" width="10%"><img src="images/<?=$stato?>" title="<?=$alt_s?>"/></td> 
		<!-- <td><?=$cdc?></td> -->
		 <td><?=$DataAutorizAsl?></td> 
		 <td><?=$ProtAutorizAsl?></td> 
		 <td><?=$normativa?></td> 
		 <td><?=$regime?></td> 
		 <td align="center"><a href="#" onclick="javascript:change_tab_edit('<?=$idimp?>');" ><img src="images/gear.png" /></a></td> 
		 <?if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and(1==0))
	{
	?>
     <td align="center"><a href="#" onclick="javascript:if (confirm('sei sicuro di voler cancellare l\'impegnativa corrente?'))cancella_impegnativa('<?=$idimp?>','<?=$idutente?>');" ><img src="images/remove.png" /></a></td> 
	<?
	}
	else
	{
	?>
	<td align="center">X</td> 	
	<?
	}
		?>
		</tr> 
		
		
		<?
}	

?>


</tbody> 
</table> 
<? 
footer_paginazione($conta, 'pager_elenco_presenze');
?>
	
</div>

<script>
$(document).ready(function() {
	
	  $("#table_elenco_impegnative").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager_elenco_presenze")}); 
});
</script>



<?
}
else
{
?>
<div class="info">nessuna impegnativa caricata per il paziente.</div>

<?
}

}





function add()
{

$idutente=$_REQUEST['id'];
$conn = db_connect();

?>


<div id="lista_impegnative">
<script>
inizializza();

</script>

<div class="titoloalternativo">
            <h1>Nuova impegnativa</h1>
</div>

<form id="myForm" method="post" name="form0" action="re_pazienti_impegnativa_POST.php">
	
	<div class="nomandatory">
	<input type="hidden" name="nomandatory" value="1" />
	</div>
	
	
	<input type="hidden" name="action" value="create" />
	
	<input type="hidden" name="idutente" value="<?php echo($idutente);?>" />
    
 

	<div class="titolo_pag"><h1>Dati relativi alla Prescrizione</h1></div>
	
	<div class="blocco_centralcat">
	
		<div class="riga">
			<?
				$query = "SELECT * from cdc where cancella='n' order by centrodicosto asc";			
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				if(mssql_num_rows($rs)>1){
				?>
					<div class="rigo_mask">
					<div class="testo_mask">Centro di Costo</div>
					<div class="campo_mask mandatory">
				
					<select name="cdc" class="scrittura">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($cdc==$row['idcdc'])
					$sel="selected";
					?>
		                <option <?=$sel?> value="<?=$row['idcdc']?>"><?=$row['centrodicosto']?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>					 
					</select>
					</div>
					</div>			
					<?}else{
					$row = mssql_fetch_assoc($rs);
					$cdc=$row['idcdc'];
					mssql_free_result($rs);
					?>
					<input type="hidden" name="cdc" value="<?=$cdc?>" />
					<?}?>
			
			<!--<div class="rigo_mask">
				<div class="testo_mask">Normativa</div>
				<div class="campo_mask mandatory">
							<?
				$query = "SELECT * from normativa order by normativa asc";	
				
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="Normativa" class="scrittura textsmall">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					?>
		                <option value="<?=$row['idnormativa']?>"><?=htmlentities(strtoupper($row['normativa']." - ".$row['descrizione']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 </select>
				</div>
			</div>-->
			
			<div class="rigo_mask">
				<div class="testo_mask">Normativa / Regime</div>
				<div class="campo_big mandatory">
							<?
				$query = "SELECT r.idregime, r.idnormativa, r.regime, r.stato, r.cancella, n.normativa
							FROM regime r
							INNER JOIN normativa n ON n.idnormativa = r.idnormativa 
							where r.cancella='n' and r.stato = 1 and n.cancella='n' and n.stato = 1
							order by r.idregime asc";

				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<!--<select name="RegimeAssistenza" class="scrittura textsmall" onclick="javascript:manImpegnativa(this.options[this.value].text);">-->
					<select name="RegimeAssistenza" class="scrittura textsmall" id="RegimeAssistenza" onchange="get_normativa(this.value);">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
						$idregime=$row['idregime'];
						$regime=$row['regime'];
						$normativa=$row['normativa'];
					?>
		                <option value="<?=$row['idregime']?>"><?=htmlentities(strtoupper($normativa." - ".$row['regime']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 </select>
				</div>
			</div>
			<input id="idNormativa" name="Normativa" type="hidden" value="">
		</div>
		
		<div class="riga">
		<div class="rigo_mask">
				<div class="testo_mask">Protocollo</div>
				<div class="campo_big">
				<?
					$conn=db_connect();
					$query = "SELECT idprotocollo,codice_protocollo,descrizione from re_protocolli_compilatore_attivi order by codice_protocollo ASC";
					$rs2 = mssql_query($query, $conn);
					if(!$rs2) error_message(mssql_error());
						//echo(mssql_num_rows($rs2));
				?>
					<select id="idprotocollo" name="idprotocollo">
							<option value="">seleziona il protocollo</option>
				<?
					while($row2= mssql_fetch_assoc($rs2)){
						$idprot=$row2['idprotocollo'];
						$codice_p=pulisci_lettura($row2['codice_protocollo']);
						$descrizione_p=pulisci_lettura($row2['descrizione']);
						?>
						<option value="<?=$idprot?>" <?if ($idprotocollo==$idprot)echo("selected"); ?>><?=$codice_p."  - ".$descrizione_p?></option>
						<?
					}
					?>
					</select>
				</div>
			</div>
		</div>		
		
		<div class="riga">
		<div id="first_step">
			<div class="rigo_mask rigo_doppio">
				<div class="testo_mask">Prescrizione</div>
				<div class="campo_mask">
					<div class="etichetta">data prod</div>
					<div id="prescrizione_data" class="data_all nomandatory">
					<input type="text" class="campo_data" name="DataPrescrizione" id="DataPrescrizione" onblur="setNow('data_ricezione_pr1');" value="<?php echo($DataPrescrizione);?>"/>
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot asl</div>
					<div id="prescrizione_prot" class="nomandatory">
					<input type="text" class="scrittura_campo" name="ProtPrescrizione" value="<?php echo($ProtPrescrizione);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">data cons</div>
					<div id="data_ricezione_pr" class="data_all nomandatory">
					<input type="text" class="campo_data" id="data_ricezione_pr1" name="data_ricezione_pr" onblur="checkDate(this.value,'DataPrescrizione');" value="<?php echo($data_ricezione_pr);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot int</div>
					<div id="protocollo_int_pr" class="nomandatory">
					<input type="text" class="scrittura_campo" name="protocollo_int_pr" value="<?php echo($protocollo_int_pr);?>" />
					</div>
				</div>
			</div>
			
			<div class="rigo_mask rigo_doppio">
				<div class="testo_mask">Progetto</div>
				<div class="campo_mask">
					<div class="etichetta">data prod</div>
					<div id="trattamento_data" class="data_all nomandatory">
					<input type="text" class="campo_data" name="DataPianoTrattamento" id="DataPianoTrattamento" onblur="setNow('data_ricezione_pro1');" value="<?php echo($DataPianoTrattamento);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot int</div>
					<div id="trattamento_prot" class="nomandatory">
					<input type="text" class="scrittura_campo" name="ProtPianoTrattamento"  value="<?php echo($ProtPianoTrattamento);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">data cons</div>
					<div id="data_ricezione_pro" class="data_all nomandatory">
					<input type="text" class="campo_data" name="data_ricezione_pro" id="data_ricezione_pro1" onblur="checkDate(this.value,'DataPianoTrattamento');" value="<?php echo($data_ricezione_pro);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot asl</div>
					<div id="protocollo_asl_pro" class="nomandatory">
					<input type="text" class="scrittura_campo" name="protocollo_asl_pro"  value="<?php echo($protocollo_asl_pro);?>" />
					</div>
				</div>
			</div>
			</div>
			<div class="rigo_mask rigo_doppio">
				<div class="testo_mask">Autorizzazione</div>
				<div class="campo_mask">
					<div class="etichetta">data prod</div>
					<div id="autorizzazione_data" class="data_all nomandatory">
					<input type="text" class="campo_data" name="DataAutorizAsl" id="DataAutorizAsl"  onblur="setNow('data_ricezione_au1');" value="<?php echo($DataAutorizAsl);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot asl</div>
					<div id="autorizzazione_prot" class="nomandatory">
					<input type="text" class="scrittura_campo" name="ProtAutorizAsl"  value="<?php echo($ProtAutorizAsl);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">data cons</div>
					<div id="data_ricezione_au" class="data_all nomandatory">
					<input type="text" class="campo_data" id="data_ricezione_au1" name="data_ricezione_au" onblur="checkDate(this.value,'DataAutorizAsl');" value="<?php echo($data_ricezione_au);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot int</div>
					<div id="protocollo_int_au" class="nomandatory">
					<input type="text" class="scrittura_campo" name="protocollo_int_au" value="<?php echo($protocollo_int_au);?>" />
					</div>
				</div>
			</div>
			<div id="second_step">
			<div class="rigo_mask rigo_doppio">
				<div class="testo_mask">Nulla Osta</div>
				<div class="campo_mask">
					<div class="etichetta">data prod</div>
					<div id="data_produzione_no" class="data_all nomandatory">
					<input type="text" class="campo_data" name="DataNullaOsta" id="DataNullaOsta" onblur="setNow('data_ricezione_nu1');" value="<?php echo($DataNullaOsta);?>" />
					</div>					
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot asl</div>
					<input type="text" class="scrittura_campo" name="ProtNullaOsta" value="<?php echo($ProtNullaOsta);?>" />
				</div>
				<div class="campo_mask">
					<div class="etichetta">data cons</div>
					<div id="data_ricezione_no" class="data_all nomandatory">	
					<input type="text" class="campo_data" name="data_ricezione_nu" id="data_ricezione_nu1" onblur="checkDate(this.value,'DataNullaOsta');" value="<?php echo($data_ricezione_nu);?>" />
					</div>	
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot int</div>					
					<input type="text" class="scrittura_campo" name="protocollo_int_nu" value="<?php echo($protocollo_int_nu);?>" />					
				</div>
			</div>
			</div>
			
	</div>
	
	<div class="riga">
		<?if(TIPOLOGIA_MEDICO){?>
		<div class="rigo_mask">
			<div class="testo_mask">Tipologia di Medico</div>
			<div class="campo_mask">
				<?
			$query = "SELECT * FROM tipomedico order by IdTipoMedico ASC";	
			$rs = mssql_query($query, $conn);
			if(!$rs) error_message(mssql_error());
			?>
				<select name="tipologiamedico" class="scrittura">
				<option value="0">selezionare</option>
				<?  while($row = mssql_fetch_assoc($rs))   
				{
				$sel="";
				if ($tipologiamedico==$row['IdTipoMedico'])
				$sel="selected";
				
				?>
					<option <?=$sel?> value="<?=$row['IdTipoMedico']?>"><?=$row['CodiceTipoMedico']."  ".$row['Descrizione']?></option>
				 <?
				 }
				 mssql_free_result($rs);
				 ?>				 
				</select>
			</div>
		</div>
		<?}else{?>
		<input name="tipologiamedico" value="0" type="hidden">
		<?}?>
		<?if(DATA_VIS_ASL){?>
		<div class="rigo_mask">
				<div class="testo_mask">Data visita ASL</div>
				<div class="campo_mask">
				<input type="text" class="campo_data" name="DataVisitaASL"  value="<?php echo($datavisitaasl);?>"/>
				</div>
		</div>
		<?}else{?>
		<input name="DataVisitaASL" value="" type="hidden">
		<?}?>
		<?if(ORA_VIS_ASL){?>
		<div class="rigo_mask">
			<div class="testo_mask">Ora visita ASL</div>
			<div class="campo_mask">
			<input type="text" class="scrittura_campo" name="OraVisitaASL"  value="<?php echo($oravisitaasl);?>"/>
			</div>
		</div>
		<?}else{?>
		<input name="OraVisitaASL" value="" type="hidden">
		<?}?>
	</div>
		
	<div class="riga">
		<div class="rigo_mask">
				<div class="testo_mask">ID Reparto</div>
				<div class="campo_mask">
					<?
				$query = "SELECT * FROM tiporeparto order by CodiceReparto ASC";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="idreparto" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($idreparto==$row['CodiceReparto'])
					$sel="selected";					
					?>
						<option <?=$sel?> value="<?=$row['CodiceReparto']?>"><?=$row['Descrizione']?></option>
					 <?
					 }
					 mssql_free_result($rs);
					 ?>				 
					</select>
				</div>
			</div>
			
		<div class="rigo_mask">
				<div class="testo_mask">Medico Prescrittore</div>
				<div class="campo_mask">
					<?
				$query = "SELECT IdPrescrittore,NominativoPrescrittore from prescrittori where cancella='n' and status=1 order by NominativoPrescrittore asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="MedicoPrescrittore" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					?>
		                <option value="<?=$row['IdPrescrittore']?>"><?=strtoupper($row['NominativoPrescrittore'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Medico Induttore</div>
				<div class="campo_mask">
					<?
				$query = "SELECT IdPrescrittore,NominativoPrescrittore from prescrittori where cancella='n' and status=1 order by NominativoPrescrittore asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="MedicoInduttore" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					?>
		                <option value="<?=$row['IdPrescrittore']?>"><?=strtoupper($row['NominativoPrescrittore'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
	
	</div>
		
	<div class="riga">
			<div class="rigo_mask rigo_doppio_big">
				<div class="testo_mask">Frequenza</div>
				<div class="campo_mask">
					<div class="etichetta">tratt.</div>
					<div class="nomandatory controllo_trattamenti">		
					<input type="text" class="campo_numerico" name="ggfrequenza"  value="<?php echo($ggfrequenza);?>" />
					</div>	
				</div>
				<div class="campo_mask">
					<div class="etichetta">settim.</div>
					<?
					$query = "SELECT IdFrequenza, TipoFrequenza FROM ElencoFrequenze WHERE (NOT (TipoFrequenza IS NULL)) ORDER BY TipoFrequenza";	
					$rs = mssql_query($query, $conn);
					if(!$rs) error_message(mssql_error());
					?>
						<select name="Frequenza" class="scrittura textsmall">
						<option value="0">selezionare</option>
						<?  while($row = mssql_fetch_assoc($rs))   
						{
						$sel="";
						if ($Frequenza==$row['IdFrequenza'])
						$sel="selected";
						
						?>
							<option <?=$sel?> value="<?=$row['IdFrequenza']?>"><?=htmlentities(strtoupper($row['TipoFrequenza']))?></option>
						 <?
						 }
						 mssql_free_result($rs);
						 ?>
						 </select>					
				</div>				
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Numero Trattamenti</div>
				<div class="campo_mask">
				<input type="text" class="scrittura_campo" name="NumeroTrattamenti" value="<?php echo($NumTrattamenti);?>" />
				</div>
			</div>
			<div class="rigo_mask" id="compartecipazione">
				<div class="testo_mask">Compartecipazione</div>
				<div class="campo_mask">
				<input type="text" class="scrittura_campo" name="compartecipazione" value="" />
				</div>
			</div>
		
		</div>
		
		<div class="riga">
		
			<div class="rigo_mask">
				<div class="testo_mask">Diagnosi</div>
				<div class="campo_mask mandatory">
					<input style="width:705px" id="diagnosi" type="text" class="scrittura_campo" name="Diagnosi" />
				</div>
			</div>
		
		</div>
		
		<div class="riga">
		     <div class="rigo_mask rigo">
				<div class="testo_mask">Menomazione 1</div>
				<div class="campo_mask">
						<?
				$query = "SELECT * from menomazioni order by codice asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="classemenomazione" class="scrittura textsmall" style="width:705px;">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					?>
		                <option value="<?=$row['idmen']?>"><?=htmlentities(strtoupper($row['codice']." ".$row['descrizione']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
		</div>
		<div class="riga">
		     <div class="rigo_mask rigo">
				<div class="testo_mask">Menomazione 2</div>
				<div class="campo_mask">
						<?
				$query = "SELECT * from menomazioni order by codice asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="classemenomazione2" class="scrittura textsmall" style="width:705px;">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					?>
		                <option value="<?=$row['idmen']?>"><?=htmlentities(strtoupper($row['codice']." ".$row['descrizione']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
		</div>
		<div class="riga">
		     <div class="rigo_mask rigo">
				<div class="testo_mask">Menomazione 3</div>
				<div class="campo_mask">
						<?
				$query = "SELECT * from menomazioni order by codice asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="classemenomazione3" class="scrittura textsmall" style="width:705px;">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					?>
		                <option value="<?=$row['idmen']?>"><?=htmlentities(strtoupper($row['codice']." ".$row['descrizione']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
		</div>
		
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">Asl</div>
				<div class="campo_mask">
				<?
				$query = "SELECT CodiceASL as codice, DescrizioneAsl as descr FROM dbo.distretti WHERE (obsoleto=0 or obsoleto is null) GROUP BY CodiceASL,DescrizioneAsl ORDER BY CodiceASL ASC";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
				<select name="asl" class="scrittura" onchange="javascript:carica_distretto(this.value);">
					<option value="0" selected>selezionare</option>
					<?php
					while($row=mssql_fetch_assoc($rs)){
						$sel="";
						if ($asl==$row['codice'])
						$sel="selected";
						$desc=$row['descr'];	
						?>
							<option <?=$sel?> value="<?=$row['codice']?>"><?=$row['codice']." - ".$desc?></option>
						 <?
					}?>
				</select>		
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Distretto</div>
				<div id="div_distretto" class="campo_mask">
				<?
				$query = $query = "SELECT IdDistretto as id, CodiceASL, CodiceDistretto as codice, DescrizioneDistretto as nome FROM dbo.distretti GROUP BY CodiceASL, CodiceDistretto, DescrizioneDistretto,IdDistretto HAVING (CodiceASL = '$asl')";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
				<select name="distretto" class="scrittura" style="width:540px;">
					<option value="0" selected>selezionare</option>
					<?php
					while($row=mssql_fetch_assoc($rs)){
						$sel="";
						if ($distretto==$row['id'])
						$sel="selected";						
						?>
							<option <?=$sel?> value="<?=$row['id']?>"><?=$row['codice']." - ".$row['nome']?></option>
						 <?
					}?>
				</select>
				</div>
			</div>
			</div>	
		
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">Ticket</div>
				<div class="campo_mask prezzo_euro">
				<input type="text" class="scrittura_campo" name="ticket" value="" />
				</div>
			</div>
		
			<div class="rigo_mask rigo_doppio_big">
				<div class="testo_mask">Esenzione</div>
				<div class="campo_mask">
				<div class="etichetta">Tipo</div>
				<select name="esenzione" class="scrittura">
					<option value="0">selezionare</option>
					<option value="1"<?php if($esenzione==1) echo("selected");?>>Non Esente</option>
					<option value="2"<?php if($esenzione==2) echo("selected");?>>Esente</option>
				</select>
				</div>
			
				<div class="campo_mask">
				<div class="etichetta">Codice</div>			
				<?
				$query = "SELECT * from codici_esenzione order by codice asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
				<select name="esenzionecodice" class="scrittura">
					<option value="0" selected>selezionare</option>
					<?php
					while($row=mssql_fetch_assoc($rs)){
						$sel="";
						if ($codEsezione==$row['idesenzione'])
						$sel="selected";						
						?>
							<option <?=$sel?> value="<?=$row['idesenzione']?>"><?=$row['codice']." - ".$row['nome']?></option>
						 <?
					}?>
				</select>
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Codice esenzione alternativo</div>
				<div class="campo_mask">
				<input type="text" class="scrittura_campo" name="esenzionecodice_alt"  value="<?php echo($esenzionecodice_alt);?>"/>
				</div>
			</div>
			</div>
		
			<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">Tipologia di stampa</div>
				<div class="campo_mask">
				<input type="checkbox" name="stampainformatizzata" <? if ($stampainformatizzata=='on') echo("checked")?>/>Stampa informatizzata				
				</div>
			</div>
		
			<div class="rigo_mask">
				<div class="testo_mask">Data inizio Trattamento</div>
				<div class="campo_mask">
				<input type="text" class="" readonly name="DataInizioTrattamento"  value="<?php echo($datainiziotrattamento);?>"/>
				</div>
			</div>
			<div class="rigo_mask">
				<div class="testo_mask">Data fine Trattamento</div>
				<div class="campo_mask">
				<input type="text" class="" readonly name="DataFineTrattamento"  value="<?php echo($datafinetrattamento);?>"/>
				</div>
			</div>
			</div>
		
			<div class="riga">		
		
			<div class="rigo_mask">
				<div class="testo_mask">Case Manager</div>
				<div class="campo_mask nomandatory">
					<?
				$query = "SELECT uid, nome, status, cancella, case_manager FROM dbo.operatori WHERE (status = 1) AND (cancella = 'n') AND (case_manager='y') order by nome asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="case_manager" class="scrittura">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
									
					?>
		                <option <?=$sel?> value="<?=$row['uid']?>"><?=strtoupper($row['nome'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>		
			
			<div class="rigo_mask">
				<div class="testo_mask">Medico Responsabile</div>
				<div class="campo_mask">
					<?
				$med_resp="";
				$query = "SELECT uid, nome, status, cancella, med_resp FROM dbo.operatori WHERE (status = 1) AND (cancella = 'n') AND (med_resp='y') order by nome asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="med_resp" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($med_resp==$row['uid'])
					$sel="selected";
					
					?>
		                <option <?=$sel?> value="<?=$row['uid']?>"><?=pulisci_lettura(strtoupper($row['nome']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					</select>
				</div>
			</div>
			
			<div class="rigo_mask">
			<div class="testo_mask">Livello di gravit�</div>
				<div class="campo_mask">
				<!--<input type="text" class="scrittura_campo" name="livello_gravita" value="<?php echo($livello_gravita);?>" />-->
				<select name="livello_gravita" class="scrittura">
					<option value="nessuno">selezionare</option>
					<option value="A-ALTO">A-ALTO</option>
					<option value="B-MEDIO">B-MEDIO</option>
					<option value="C-BASSO">C-BASSO</option>
				</select>
				</div>
			</div>
			</div>
			<div class="riga">				
			<div class="rigo_mask">
				<div class="testo_mask">Protocollo DT</div>
				<div class="campo_mask">
				<input type="checkbox" id="succ" name="protocollo_dt" /> Esiste un protocollo DT per la patologia
				<!--<input type="checkbox" id="succ" onclick="if($('#succ:checked').val()=='on'){ $('#s_t').slideDown();} else{ $('#s_t').slideUp();}" name="protocollo_dt" /> Esiste un protocollo DT per la patologia-->
				<!--<select name="protocollo_dt" class="scrittura">
					<option value="0">selezionare</option>
					<option value="1"<? if ($protocollo_dt==1) echo("selected")?>>Esiste</option>
					<option value="2"<? if ($protocollo_dt==2) echo("selected")?>>Non Esiste</option>
				</select>-->
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Successo del trattamento</div>
				<div class="campo_mask">				
				<span id="s_t"><input type="checkbox" name="successo_trattamento" /> Il trattamento ha avuto successo</span>
				<!--<select name="successo_trattamento" class="scrittura">
					<option value="0">selezionare</option>
					<option value="1"<? if ($successo_trattamento==1) echo("selected")?>>Ha avuto successo</option>
					<option value="2"<? if ($successo_trattamento==2) echo("selected")?>>Non ha avuto successo</option>
				</select>-->
				</div>
			</div>	
		
		</div>
		
		<div class="riga">		
			<div class="rigo_mask rigo_big">
				<div class="testo_mask">Note</div>
				<div class="campo_mask">
					<input id="note" type="text" class="scrittura_campo" name="Note" value="<?=$note?>" />
				</div>
			</div>		
		</div>
	
	<div class="titolo_pag"><h1>Tipologie di trattamento</h1></div>	
	<div id="elenco_trattamenti">	
	<?	
	$d=1;
	while ($d<2)
	{
		if ($d==1)
			$st=$d;
		else
			$st.=",".$d;
		
		$ordinamento=$st;

		$d++;
	}
	?>			
	<input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">			

	<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">

	 <tbody id="tbody" >
	<tr class="riga_tab nodrag nodrop">
	<td width="10%" align="left">&nbsp;</td>
	<td width="40%" align="center">tipolgia assistenza</td>
	<td width="10%" align="center">trattamenti richiesti</td>
	<td width="10%" align="center">frequenza</td>
	<td width="20%" align="center">presenze</td>
	<td width="5%">&nbsp;</td>
	<td width="5%">elimina</td>
	<td width="10%">&nbsp;</td>
	</tr>
	<?
	$i=1;
	$numerofield=100;
	$style='';
	$class='';
	$dis="";
	while($i<$numerofield)
	{
		
		if ($i>1)
		{
		$style="style=\"display:none\"";
		$class="riga_sottolineata_diversa hidden";
		$dis="disabled";
		
		}
		else
		{
		$nodrag="nodrag=false";
		$style='';
		$class="riga_sottolineata_diversa";
		$dis="";
		}
		?>		
		<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
		<td align="center">&nbsp;</td>
		<td align="center">
			<select id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" OnClick="javascript:addSelect(getElementById('RegimeAssistenza').value,'<?=$i?>');">
			<option value="0">seleziona una tipologia</option>
			</select>
		</td>
		<td align="center" class="nodrag nodrop nomandatory integer"><input id="ric<?=$i?>" name="richiesti<?=$i?>" value="" /></td>
		<td align="center" class="nodrag nodrop nomandatory integer">
			<select id="freq<?=$i?>" name="frequenza<?=$i?>">
			<option value="0">non definita</option>
			<?
			$query = "SELECT * FROM ElencoFrequenze order by TipoFrequenza";	
			$rs = mssql_query($query, $conn);
			if(!$rs) error_message(mssql_error());
			while($row = mssql_fetch_assoc($rs)) {
				?>
				<option value="<?=$row['IdFrequenza']?>"><?=$row['TipoFrequenza']?></option>
				 <?
			 }
			 mssql_free_result($rs);
			 ?>
			</select>
		</td>
		<td align="center" class="nodrag nodrop">nessuna</td>	  	
		<td>&nbsp;</td>
		<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a></td>
		</tr>
	<?
	$i++;
	}
	?>

	<tr id="1000" class="riga_sottolineata_diversa nodrag nodrop"><td align="center"><span><a OnClick="javascript:DinamicDiv('0','add','<?=$numerofield?>');"><img src="images/add.png" /></a></td></tr>
	</tbody>
	</table>
	</div>
	
	
	</div>
	
    
    <div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	</form>
	
	
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				$("#compartecipazione").slideUp();
				//$('#s_t').slideUp();

// Initialise the first table (as before)
	$("#elenco").tableDnD();

		// Initialise the second table specifying a dragClass and an onDrop function that will display an alert
	$("#elenco").tableDnD({
	    onDragClass: "myDragClass",
	    onDrop: function(table, row) {
            var rows = table.tBodies[0].rows;
            var debugStr = "";
            for (var i=0; i<(rows.length-1); i++) 
			{
				if (rows[i].style.display != 'none')
			    debugStr += rows[i].id+" ";
				
            }
			document.getElementById("debug").value=debugStr;
	       
	    },
		onDragStart: function(table, row) {
		//alert(row.id);
			//$(#debugArea).html("Started dragging row "+row.id);
		}
	});		
		
	});	
	function get_normativa(value){
		$.ajax({
	   type: "POST",
	   url: "get_normativa.php",
	   data: "idregime="+value,
	   success: function(msg){
			nor=$.trim(msg);			
			$("#idNormativa").val(nor);
			if(nor=="2") {
				$("#compartecipazione").slideDown();
				}else{
				$("#compartecipazione").slideUp();
				}
	   }
	 });
}
	
	
	function addSelect(id,sel){	
	//var html="<option value='1'>prova "+id+"</option>";
	//alert("x"+jQuery.trim($("#et"+sel).text())+"x");
	 if(id!=""){
	// if((jQuery.trim($("#et"+sel).text())=="seleziona una tipologia")||(jQuery.($("#et"+sel).text())=="aggiorna elenco...")){
	if($("#et"+sel).val()==0){	
		$.ajax({
		   type: "POST",
		   url: "tipologie_ajax.php",
		   data: "idregime="+id,
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 $("#et"+sel).html(trim(msg));
			}
		 });
	}
	}		
	}
	
function checkDate(value,id){
	var idVal = $("#"+id).val();	
	//if(idVal>value) alert("Attenzione, la data inserita deve essere maggiore della data di produzione.");
}
function getDate() {
  var date = new Date();
  var day = date.getDate();
  if(day<9) day="0"+day;
  var month = date.getMonth() + 1;
  if(month<10) month="0"+month;
  var year = date.getFullYear();  
  data= day + "/" + month + "/" + year;  
  return data;
 }
 
 function setNow(id){
	var data=getDate();	
	if($("#"+id).val()=="")	$("#"+id).val(data);
 }
	
	
	//function test ()
	//{		
	//	$('#container-4').triggerTab(0);		
	//	return false;
	//}
</script>
</div>	
		
	
	
	
	
<?php
 }


 

if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			case "create":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create();					
			break;

			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_REQUEST['id']);
			break;

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
		}

		
		switch($do) {

			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
			break;
			
			case "add_nota":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add_nota();
			break;
			
			case "add_allegati":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add_allegati($_REQUEST['id']);
			break;
			
			case "show_allegati":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else show_allegati($_REQUEST['id'],$_REQUEST['idimp']);
			break;
			
			case "show_note":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else show_note($_REQUEST['id']);
			break;
			
			case "show_presenze":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else show_presenze();
			break;

			case "edit":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit($_REQUEST['id']);
			break;
			
						
			case "review":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else review($_REQUEST['id']);
			break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				show_list();
			break;
		}
			html_footer();
	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>