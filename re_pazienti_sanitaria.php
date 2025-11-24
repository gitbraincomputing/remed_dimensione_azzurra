<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
include_once('include/function_page.php');

//$idutente=$id;

function show_allegati($id,$idcart)
{

$idutente=$id;
$conn = db_connect();
if($idcart=="") $idcart=0;
if(($idcart!=0)and($idcart!="")) $que="and (idcartella=$idcart)";
$query = "SELECT * from re_allegati_cartelle where (idutente=$idutente) and(stato=1) and (cancella='n') $que order by data_produzione DESC";
//echo($query);	
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);
?>
<script>
$(document).ready(function() {
	
	  $("#table_allegati_san").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#sorter_allegati_sanitari")}); 
});
function conf_cancella(id_al,paz){
if (confirm('sei sicuro di voler cancellare l\'allegato corrente?')){
	document.getElementById('id_allegato').value=id_al;
	  $.ajax({
		   type: "POST",
		   url: "re_pazienti_sanitaria_POST.php?action=del_files&id_all="+id_al+"&paz="+paz,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 reload_allegati(<?=$idutente?>,<?=$idcart?>);
			}
		 });	  
	  }
	else return false;
}
function add_allegati(idpaziente){
	$("#layer_nero2").toggle();
	$('#cartella_clinica1').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria.php?do=add_allegati&id="+idpaziente;
	$("#cartella_clinica1").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-3 .tabs-selected").removeClass('tabs-selected');
	 });
}
</script>

<div id="cartella_clinica1">
<script>
inizializza();
</script>
<div class="titoloalternativo">
            <h1>Allegati Sanitari</h1>
	        <!--<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
</div>

<?	
	if($conta==0){
	?>		
	<div class="info">Nessun allegato trovato.</div>
	<span class="rigo_mask no_print" style="margin-bottom:15px; border-bottom:0px">
		<div class="testo_mask">Raggruppa per cartelle</div>
			<div class="campo_big ">
				<select name="idimp<?=$i?>" class="scrittura" onchange="reload_allegati('<?=$idutente?>',this.value);">
				<option value="0" <?if($idcart=="0") echo("selected");?>>tutte</option>
				<?
					$query = "SELECT * from utenti_cartelle where idutente=$idutente order by id desc";	
					$rs1 = mssql_query($query, $conn);			
					while($row1 = mssql_fetch_assoc($rs1))   
						{
							$idcartella=$row1['id'];					
							$cartella=$row1['codice_cartella']."/".convert_versione($row1['versione']);
						?>
		<option value="<?=$idcartella?>" <?if($idcartella==$idcart) echo("selected");?>><?=$cartella?></option>
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
	<form name="form0" action="re_pazienti_sanitaria_POST.php" method="post" id="myForm" >
    <input type="hidden" name="action" value="del_files" />
	<div class="nomandatory"><input type="hidden" name="id_allegato" id="id_allegato" value="" /></div>
    <div class="nomandatory"><input type="hidden" name="id_paziente" id="id_paziente" value="<?=$idutente?>" /></div>					
	<span class="rigo_mask no_print" style="margin-bottom:15px; border-bottom:0px">
		<div class="testo_mask">Raggruppa per cartelle</div>
			<div class="campo_big ">
				<select name="idimp<?=$i?>" class="scrittura" onchange="reload_allegati('<?=$idutente?>',this.value);">
				<option value="0" <?if($idcart=="0") echo("selected");?>>tutte</option>
				<?
					$query = "SELECT * from utenti_cartelle where idutente=$idutente order by id desc";	
					$rs1 = mssql_query($query, $conn);			
					while($row1 = mssql_fetch_assoc($rs1))   
						{
							$idcartella=$row1['id'];					
							$cartella=$row1['codice_cartella']."/".convert_versione($row1['versione']);
						?>
		<option value="<?=$idcartella?>" <?if($idcartella==$idcart) echo("selected");?>><?=$cartella?></option>
						<?
						}
					?>
				</select>
			</div>
	</span>	
<?
$id_cart="";
$open=0;
while($row = mssql_fetch_assoc($rs)) {
	
	if($row['idcartella']!=$id_cart){		
		if($open) echo("</tbody></table>");		
		$id_cart=$row['idcartella'];		
		echo("<div class='titolo_pag'><div class='comandi space_top_print'>");
		echo("Cartella n. <strong>".$row['codice_cartella']."/".convert_versione($row['versione'])."</strong> aperta il <strong>".formatta_data($row['data_creazione'])."</strong>");
		if ($row['data_chiusura']!="") echo(" chiusa il <strong>".formatta_data($row['data_chiusura'])."</strong>");
		echo("</div></div>");
		$open=1;
		?>
		
		<table id="table_allegati_san" class="tablesorter" cellspacing="1"> 
		<thead> 
		<tr> 
			
			<th width="8%" align="center" >Tipo file</th>
			<th width="15%" align="center">Data acquisizione</th>	
			<th width="10%" align="center">Operatore</th>	
			<th width="40%" align="left">Descrizione</th>
			<th width="20%" align="left">Data di produzione</th>		
			<td width="10%" align="center" class="no_print">Visualizza</td>     
			<td width="10%" align="center" class="no_print">Cancella</td>
			
		</tr> 
		</thead> 
		<tbody> 

		<?	
		}
		
		$id_all=$row['id'];
		$descrizione=$row['descrizione'];
		$file=$row['file_name'];
		$type=$row['type'];		
		$data_ora=formatta_data($row['datains']);
		$operatore=$row['opeins'];
		$data_prod=$row['data_produzione'];
		if(formatta_data($data_prod)=="01/01/1900") $data_prod="";
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
				$alt_file="jpeg";
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
		 
		 <td align="center"><img src="images/icons/<?=$tipo_file?>" alt="<?=$alt_file?>"/><span class="hide"><?=$alt_file?></span></td> 
		 <td><?=$data_ora?></td>
		 <td><?=$nome_op?></td>		 
		 <td><?=stripslashes($descrizione)?></td>	
		 <td><?=formatta_data($data_prod)?></td>
		 <td align="center" class="no_print"><a href="view_file.php?idfile=<?=$id_all?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>&sec=san" ><img src="images/view.png" /></a></td>         
         <td align="center" class="no_print"><!--<input type="image" class="no_print" src="images/remove.png" title="elimina" onclick="javascript:document.getElementById('id_allegato').value='<?=$id_all?>';" />-->
		  <img type="submit" src="images/remove.png" alt="elimina"  onclick="conf_cancella('<?=$id_all?>','<?=$idutente?>');"></td> 		  
		</tr>
		<?
}
?>
</tbody> 
</table>

	<div id="sorter_allegati_sanitari"></div>

 
</form>
</div>

	
</div>
<script type="text/javascript" language="javascript">

	function reload_allegati(idpaziente,idcartella){
		$("#layer_nero2").toggle();
		$('#cartella_clinica1').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria.php?do=show_allegati&idcart="+idcartella+"&id="+idpaziente;
		$("#cartella_clinica1").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	}
	
</script>	
<?
}

function add_allegati($id){

$idutente=$id;
$conn = db_connect();

?>
<div id="cartella_clinica">
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
	<form name="form0" action="re_pazienti_sanitaria_POST.php" method="post" id="myForm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add_files" />
	<input type="hidden" name="id" value="<?php echo($id);?>" />			
	
    <div class="riga">
    <input id="debug" type="hidden" value="1" name="debug"/>
	<table id="elenco" style="clear:both; width: 100%" cellspacing="2" cellpadding="2" border="0">
 	<tbody id="tbody">
    <tr class="riga_tab nodrag nodrop">
		<td width="0%" align="left">&nbsp;</td>
		<td width="20%" align="left">cartella</td>
		<td width="10%" align="left">data di produzione</td>
        <td width="20%" align="left">descrizione</td>	
		<td  width="5%">Stampa elenco istanze</td>
		<td  width="20%" align="center">file</td>			
		<td  width="5%">&nbsp;</td>
		<td >&nbsp;</td>
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
		<select name="idcart<?=$i?>" class="scrittura">
			<?
			$query = "SELECT * from utenti_cartelle where idutente=$idutente order by id desc";	
			$rs1 = mssql_query($query, $conn);			
			while($row1 = mssql_fetch_assoc($rs1))   
				{
					$idcartella=$row1['id'];					
					$cartella=$row1['codice_cartella']."/".convert_versione($row1['versione']);
					
				?>
<option value="<?=$idcartella?>"><?=$cartella?></option>
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
	<td align="left" class="nodrag nodrop"><input type="checkbox" name="stampa_elenco<?=$i?>" id="stampa_elenco<?=$i?>" checked value="1" /></td>	
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

function show_anagrafica_farmaci($id_paziente) {	
	$stato_sel = isset($_GET['stato_sel']) ? $_GET['stato_sel'] : 2;
?>
<script type="text/javascript" >
	$(document).ready(function() {
		$("#table_farmaci").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
	});
	
	function aggiungi_farmaco(id_paziente){
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=aggiungi_farmaco&id_paziente="+id_paziente;
		
		$('#sanitaria').load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}
	
	function view_farmaco(id_farmaco, id_paziente) {
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=view_farmaco&id_farmaco="+id_farmaco+"&id_paziente="+id_paziente;
		
		$('#sanitaria').load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}

	function edit_farmaco(id_farmaco, id_paziente) {
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=edit_farmaco&id_farmaco="+id_farmaco+"&id_paziente="+id_paziente;
		
		$('#sanitaria').load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}
	
	function associa_farmaco(id_farmaco, id_paziente) {
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=associa_farmaco&id_farmaco="+id_farmaco+"&id_paziente="+id_paziente;
		
		$('#sanitaria').load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}

	function del_farmaco(id_farmaco, id_paziente) {
		$.ajax({
			type: "POST",
			url: "lista_farmaci_POST.php",
			data: "action=delete&id="+id_farmaco,
			success: function(res){
				if(res == 'true') {
					$("#layer_nero2").toggle();
					$('#sanitaria').innerHTML="";

					pagina_da_caricare = "re_pazienti_sanitaria.php?do=show_anagrafica_farmaci&id="+id_paziente;

					$('#sanitaria').load(pagina_da_caricare, '', function(){
						loading_page('loading');
					});
				} else
					alert('Errore nella cancellazione del farmaco');
			},
			error: function(request, error){
				alert('Errore nella cancellazione del farmaco');
			}
		});
	}
	
	function filter_stato_farmaco(id_farmaco) {
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		
		pagina_da_caricare="re_pazienti_sanitaria.php?do=show_anagrafica_farmaci&id="+id_paziente+"&stato_sel="+$("#sel_stato_farm").val();
		$('#sanitaria').load(pagina_da_caricare, '', function(){
			loading_page('loading');
		});
	}
	
</script>
<?php
	$conn = db_connect();
	$_stat_filter = !empty($stato_sel) ? " AND stato_farmaco = $stato_sel" : '';
	$lista_farmaci_sql = "SELECT id, lotto, nome, dosaggio, data_scadenza, data_carico, quantita_caricata, quantita_attuale, giacenza_minima, stato_farmaco
							FROM farmaci
							WHERE status = 1 AND deleted = 0 AND id_paziente = '$id_paziente' $_stat_filter
							ORDER BY nome, data_scadenza, lotto ASC";
	$lista_farmaci_rs = mssql_query($lista_farmaci_sql, $conn);
	
	$tot_farmaci = mssql_num_rows($lista_farmaci_rs);
	$lista_farmaci = array();
	
	while($row = mssql_fetch_assoc($lista_farmaci_rs)) {
		$lista_farmaci[] = $row;
	}
	
	mssql_free_result($lista_farmaci_rs);
?>
<div id="sanitaria">
	<div class="titoloalternativo">
		<h1>Farmacia Paziente</h1>
	</div>
	
	<div class="titolo_pag">
		<div class="comandi">		
			<div class="aggiungi aggiungi_right">
			<a href="#" onclick="javascript:aggiungi_farmaco('<?=$id_paziente?>')" >aggiungi farmaco</a>
			</div>
		</div>
		<h1>Elenco farmaci</h1>
	</div>
	<span style="border-bottom: 0px none;" class="rigo_mask no_print">
		<div class="campo_big">
			<div class="testo_mask">Stato farmaco</div>
			<div class="campo_mask">
				<select id="sel_stato_farm" name="sel_stato_farm" onchange="filter_stato_farmaco(<?=$id_paziente?>)" >
					<option value="" <?= (empty($stato_sel) ? 'selected' : '') ?> >Tutti</option>
					<option value="1" <?= (!empty($stato_sel) && $stato_sel == 1 ? 'selected' : '') ?> >Non presente</option>
					<option value="2" <?= (!empty($stato_sel) && $stato_sel == 2 ? 'selected' : '') ?> >Presente</option>
					<option value="3" <?= (!empty($stato_sel) && $stato_sel == 3 ? 'selected' : '') ?> >Esaurito</option>
					<option value="5" <?= (!empty($stato_sel) && $stato_sel == 5 ? 'selected' : '') ?> >In scorta</option>
					<option value="4" <?= (!empty($stato_sel) && $stato_sel == 4 ? 'selected' : '') ?> >Non presente in terapia</option>
				</select>
			</div>
		</div>
	</span>
	<table id="table_farmaci" class="tablesorter" cellspacing="1"> 
		<thead> 
			<tr>
				<th width="10%">Lotto</th> 
				<th>Nome</th> 
				<th width="5%">Dosaggio</th>
				<th width="5%">Scadenza</th>
				<th width="15%">Stato</th>				
				<th width="7%" colspan="2" >Quantita attuale</th>
				<th width="5%">Giacenza minima</th>
				<th width="5%">Visualizza</th>
				<th width="5%">Modifica</th>
				<th width="5%">Associa</th>
				<td width="5%">Elimina</td>
			</tr> 
		</thead> 
		<tbody>
<?	
	if($tot_farmaci > 0) {
		foreach($lista_farmaci as $farmaco) {
			$status = '';
			$title = '';
			
			if(!empty($farmaco['quantita_caricata']) && $farmaco['quantita_caricata'] > 0) {
				if($farmaco['quantita_attuale'] > $farmaco['giacenza_minima']) {
					$status = 'stato_ok';
				} elseif($farmaco['quantita_attuale'] == $farmaco['giacenza_minima']) {
					$status = 'stato_giallo';
					$title = 'Raggiunta quantita minima';
				} else {
					$status = 'stato_red';
					$title = 'Passata quantita minima';
				}
			} else {
				$status	= 'alert_scadenza';
				$title = 'Quantita non impostata';
			}
?>
			<tr id="<?=$farmaco['id']?>">
				<td><?=$farmaco['lotto']?></td> 
				<td><?=$farmaco['nome']?></td>
				<td><?=$farmaco['dosaggio']?></td>
				<td><?=empty($farmaco['data_scadenza']) ? '' : date('d/m/Y',strtotime($farmaco['data_scadenza']))?></td> 
				<td>
<?			switch($farmaco['stato_farmaco']) {
				case 1:
					echo 'Non presente';
				break;
				
				case 2:
					echo 'Presente';
				break;
				
				case 3:
					echo 'Esaurito';
				break;
				
				case 4:
					echo 'Non presente in terapia';
				break;
				
				case 5:
					echo 'In scorta';
				break;
				
				default:
					echo 'Stato non gestito';
				break;
			}
?>
				</td>
				<td><?=$farmaco['quantita_attuale']?></td>
				<td style="text-align: center;" ><img src="images/<?=$status?>.png" style="max-width: 10px;max-height: 10px;" title="<?=$title?>" ></td>
				<td><?=$farmaco['giacenza_minima']?></td>
				<td><a href="#" onclick="javascript:view_farmaco(<?=$farmaco['id']?>,<?=$id_paziente?>);" ><img src="images/view.png" /></a></td> 
				<td><a href="#" onclick="javascript:edit_farmaco(<?=$farmaco['id']?>,<?=$id_paziente?>);" ><img src="images/gear.png" /></a></td>
				<td>
			<?php if($farmaco['quantita_attuale'] > 0 && $farmaco['stato_farmaco'] == 2) { ?>
				<a href="#" onclick="javascript:associa_farmaco(<?=$farmaco['id']?>,<?=$id_paziente?>);" ><img src="images/add.png" /></a>
			<?php } else { ?>
				<img title='Non è possibile associare un farmaco terminato o con stato diverso da Presente' src="images/add_disabled.png" />
			<?php } ?>
				</td>
				<td><a href="#" onclick="javascript:if(confirm('Sei sicuro di voler cancellare il farmaco ?')) del_farmaco('<?=$farmaco['id']?>', <?=$id_paziente?>);" ><img src="images/remove.png" /></a></td>
			</tr>
<?
			if(!empty($farmaco['data_scadenza'])) {
				if(strtotime(date('Y-m-d')) > strtotime($farmaco['data_scadenza'])) {
					?><script type="text/javascript" >
						var farm_id = <? echo $farmaco['id']; ?>;
						$("#" + farm_id).find('td').css("background-color", "#ff00003d");
					</script><?
				} elseif(strtotime($farmaco['data_scadenza']) <= strtotime(date('Y-m-d') . ' +7 days')) {
					?><script type="text/javascript" >
						var farm_id = <? echo $farmaco['id']; ?>;
						$("#" + farm_id).find('td').css("background-color", "#ffff004d");
					</script><?
				}
			}
			
			if(!empty($farmaco['quantita_attuale']) && $farmaco['quantita_attuale'] <= 0) {
				?><script type="text/javascript" >
					var farm_id = <? echo $farmaco['id']; ?>;
					$("#" + farm_id).find('td').css("background-color", "#ff00003d");
				</script><?
			}
			
			if($farmaco['stato_farmaco'] == 5) {
				?><script type="text/javascript" >
					var farm_id = <? echo $farmaco['id']; ?>;
					$("#" + farm_id).find('td').css("background-color", "rgba(149, 149, 149, 0.16)");
					$("#" + farm_id).find('td').css("color", "#808080de");
				</script><?
			}
		}
	} else { ?>
			<tr>
				<td colspan="12"><div class="info" style="margin-left: 0px;">Nessun farmaco registrato.</div></td>
			</tr>
<?php
	}
?>
		</tbody> 
	</table> 
<?	footer_paginazione($tot_farmaci); ?>

</div>

<?
}

function aggiungi_farmaco($id_paziente) {
	$conn = db_connect();
?>
<script>inizializza();</script>	
<div class="titoloalternativo">
	<h1>Anagrafica farmaci paziente</h1>
</div>

<div class="titolo_pag">
	<div class="comandi">
		<div class="indietro indietro_left"><a href="#" onclick="javascript:back_to_list(<?=$id_paziente?>)" >indietro</a></div>
		<div><h1>Censimento farmaco</h1></div>
	</div>
</div>
	
<div class="blocco_centralcat">
	<div class="form">		
		<form method="post" name="form0" action="lista_farmaci_POST.php" id="myForm" enctype="multipart/form-data" >
			<input type="hidden" name="action" value="create" />
			<input type="hidden" name="id_paziente" value="<?=$id_paziente?>" />
			
			<div class="riga">
				<div class="rigo_mask">
					<div class="testo_mask">Nome farmaco</div>
					<div class="campo_mask mandatory">
						<input type="text" id="nome"  name="nome" class="scrittura_campo" SIZE="50" maxlenght="50" />
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Lotto</div>
					<div class="campo_mask mandatory">
						<input type="text" id="lotto" name="lotto" class="scrittura_campo"  SIZE="30" maxlenght="30" />
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Dosaggio</div>
					<div class="campo_mask">
						<input type="text" id="dosaggio" name="dosaggio" class="scrittura_campo" pattern="[0-9]+([\.][0-9]+)?" />
					</div>
				</div>
			</div>
			
			<div class="riga">
				<div class="rigo_mask">
					<div class="testo_mask">Data scadenza</div>
					<div class="campo_mask mandatory data_futura">
						<input type="text" name="data_scadenza" id="data_scadenza" class="campo_data scrittura" value=""/>
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Data di carico</div>
					<div class="campo_mask mandatory">
						<input type="text" id="data_carico" name="data_carico" class="campo_data scrittura" value=""/>
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Tipologia somministrazione</div>
					<div class="campo_mask mandatory">
						<select id="tipologia_somministrazione" name="tipologia_somministrazione" class="scrittura">
<?
	$combo_somm_sql = "SELECT *
						FROM campi_combo
						WHERE idcombo = 250 AND stato = 1 AND cancella = 'n'";
	$_rs = mssql_query($combo_somm_sql, $conn);
	
	while($row = mssql_fetch_assoc($_rs)) {
							echo '<option value="' . $row['valore'] . '" >' . $row['etichetta'] . '</option>';
	}

	mssql_free_result($_rs); ?>
						</select>
					</div>
				</div>			
			</div>
			
			<div class="riga">
				<div class="rigo_mask">
					<div class="testo_mask">Quantità</div>
					<div class="campo_mask mandatory">
						<input type="text" id="quantita" name="quantita" class="scrittura_campo" pattern="[0-9]+([\.][0-9]+)?" />
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Giacenza minima</div>
					<div class="campo_mask mandatory">
						<input type="text" id="giacenza_minima" name="giacenza_minima" class="scrittura_campo" pattern="[0-9]+([\.][0-9]+)?" />
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Stato del farmaco</div>
					<div class="campo_mask mandatory">
						<select id="stato_farmaco" name="stato_farmaco" class="scrittura">
							<option value="1" >Non presente</option>
							<option value="2" >Presente</option>
							<option value="3" >Esaurito</option>
							<option value="5" >In scorta</option>
							<option value="4" >Non presente in terapia</option>
						</select>
					</div>
				</div>
			</div>
			
			<div class="titolo_pag">
				<div class="comandi">
					<input type="submit"  title="salva" value="salva" class="button_salva"/>			
				</div>
			</div>
		</form>	
	</div>
</div>

<script type="text/javascript" >
	$(document).ready(function() {
		$(".solo_numeri").validation({
			type: "int"
		});
		
		$(".campo_data").mask("99/99/9999");
	});
	
	function back_to_list(idpaziente){
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=show_anagrafica_farmaci&id="+idpaziente;
		
		$('#sanitaria').load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}
</script>

<?
}


function view_farmaco($id_farmaco, $id_paziente)
{
	$conn = db_connect();
	$_sql = "SELECT *
				FROM farmaci
				WHERE id = $id_farmaco";
	$_rs = mssql_query($_sql, $conn);
	
	$farmaco = mssql_fetch_assoc($_rs);
	
	mssql_free_result($_rs);
	
	$combo_somm_sql = "SELECT etichetta
						FROM campi_combo
						WHERE idcombo = 250 AND valore = " . $farmaco['tipologia_somministrazione'];
	$_rs = mssql_query($combo_somm_sql, $conn);
	
	$row = mssql_fetch_assoc($_rs);
	$tipologia_somministrazione = $row['etichetta'];
	
	mssql_free_result($_rs);
?>
<div class="titoloalternativo">
	<h1>Anagrafica farmaci paziente</h1>
</div>

<div class="titolo_pag">
	<div class="comandi">
		<div class="indietro indietro_left"><a href="#" onclick="javascript:back_to_list(<?=$id_paziente?>)" >indietro</a></div>
		<div><h1>Dettaglio farmaco</h1></div>
	</div>
</div>
	
<div class="blocco_centralcat">
	<div class="riga">
		<div class="rigo_mask">
			<div class="testo_mask">Nome farmaco</div>
			<div class="campo_mask"><?=$farmaco['nome']?></div>
		</div>
		
		<div class="rigo_mask">
			<div class="testo_mask">Lotto</div>
			<div class="campo_mask"><?=$farmaco['lotto']?></div>
		</div>
		
		<div class="rigo_mask">
			<div class="testo_mask">Dosaggio</div>
			<div class="campo_mask"><?=$farmaco['dosaggio']?></div>
		</div>
	</div>
	
	<div class="riga">
		<div class="rigo_mask">
			<div class="testo_mask">Data scadenza</div>
			<div class="campo_mask"><?=empty($farmaco['data_scadenza']) ? '' : date('d/m/Y',strtotime($farmaco['data_scadenza']))?></div>
		</div>
		
		<div class="rigo_mask">
			<div class="testo_mask">Data di carico</div>
			<div class="campo_mask"><?=empty($farmaco['data_carico']) ? '' : date('d/m/Y',strtotime($farmaco['data_carico']))?></div>
		</div>
		
		<div class="rigo_mask">
			<div class="testo_mask">Tipologia somministrazione</div>
			<div class="campo_mask"><?=$tipologia_somministrazione?></div>
		</div>
	</div>
	
	<div class="riga">	
		<div class="rigo_mask">
			<div class="testo_mask">Quantità</div>
			<div class="campo_mask"><?=$farmaco['quantita_caricata']?></div>
		</div>	
	
		<div class="rigo_mask">
			<div class="testo_mask">Quantità attuale</div>
			<div class="campo_mask"><?=$farmaco['quantita_attuale']?></div>
		</div>
		<div class="rigo_mask">
			<div class="testo_mask">Giacenza minima</div>
			<div class="campo_mask"><?=$farmaco['giacenza_minima']?></div>
		</div>
	</div>
		
	<div class="riga">
		<div class="rigo_mask">
			<div class="testo_mask">Stato del farmaco</div>
			<div class="campo_mask">
<?
	switch($farmaco['stato_farmaco']) {
		case 1:
			echo 'Non presente';
		break;
		
		case 2:
			echo 'Presente';
		break;
		
		case 3:
			echo 'Esaurito';
		break;
		
		case 4:
			echo 'Non presente in terapia';
		break;
		
		case 5:
			echo 'In scorta';
		break;
		
		default:
			echo 'Stato non gestito';
		break;
	}
?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" >
	function back_to_list(id_paziente){
		$("#layer_nero2").toggle();
		$('#cartella_clinica1').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=show_anagrafica_farmaci&id="+id_paziente;
		
		$('#sanitaria').load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}
</script>

<?
}


function edit_farmaco($id_farmaco, $id_paziente)
{
	$conn = db_connect();
	$_sql = "SELECT *
				FROM farmaci
				WHERE id = $id_farmaco";
	$_rs = mssql_query($_sql, $conn);
	
	$farmaco = mssql_fetch_assoc($_rs);
	
	mssql_free_result($_rs);	
?>
<script>inizializza();</script>
<div class="titoloalternativo">
	<h1>Anagrafica farmaci paziente</h1>
</div>

<div class="titolo_pag">
	<div class="comandi">
		<div class="indietro indietro_left"><a href="#" onclick="javascript:back_to_list(<?=$id_paziente?>)" >indietro</a></div>
		<div><h1>Modifica farmaco</h1></div>
	</div>
</div>

<div class="blocco_centralcat">
	<div class="form">
		<form method="post" name="form0" action="lista_farmaci_POST.php" id="myForm" enctype="multipart/form-data" >
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="id" value="<?=$id_farmaco?>" />
			<input type="hidden" name="id_paziente" value="<?=$id_paziente?>" />
			
			<div class="riga">
				<div class="rigo_mask">
					<div class="testo_mask">Nome farmaco</div>
					<div class="campo_mask mandatory">
						<input type="text" id="nome"  name="nome" class="scrittura_campo" SIZE="50" maxlenght="50" value="<?=$farmaco['nome']?>" />
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Lotto</div>
					<div class="campo_mask mandatory">
						<input type="text" id="lotto" name="lotto" class="scrittura_campo"  SIZE="30" maxlenght="30" value="<?=$farmaco['lotto']?>" />
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Dosaggio</div>
					<div class="campo_mask">
						<input type="text" id="dosaggio" name="dosaggio" class="scrittura_campo" pattern="[0-9]+([\.][0-9]+)?" value="<?=$farmaco['dosaggio']?>" />
					</div>
				</div>
			</div>
			
			<div class="riga">
				<div class="rigo_mask">
					<div class="testo_mask">Data scadenza</div>
					<div class="campo_mask mandatory">
						<input type="text" name="data_scadenza" id="data_scadenza" class="campo_data scrittura" value="<?=empty($farmaco['data_scadenza']) ? '' : date('d/m/Y',strtotime($farmaco['data_scadenza']))?>"/>
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Data di carico</div>
					<div class="campo_mask mandatory">
						<input type="text" id="data_carico" name="data_carico" class="campo_data scrittura" value="<?=empty($farmaco['data_carico']) ? '' : date('d/m/Y',strtotime($farmaco['data_carico']))?>"/>
					</div>
				</div>
			
				<div class="rigo_mask">
					<div class="testo_mask">Tipologia somministrazione</div>
					<div class="campo_mask mandatory">
						<select id="tipologia_somministrazione" name="tipologia_somministrazione" class="scrittura">
<?
	$combo_somm_sql = "SELECT *
						FROM campi_combo
						WHERE idcombo = 250 AND stato = 1 AND cancella = 'n'";
	$_rs = mssql_query($combo_somm_sql, $conn);
	
	while($row = mssql_fetch_assoc($_rs)) {
							echo '<option ' . ($farmaco['tipologia_somministrazione'] == $row['valore'] ? 'selected' : '') . ' value="' . $row['valore'] . '" >' . $row['etichetta'] . '</option>';
	}

	mssql_free_result($_rs); ?>
						</select>
					</div>
				</div>
			</div>
			
			<div class="riga">			
				<div class="rigo_mask">
					<div class="testo_mask">Quantità</div>
					<div class="campo_mask mandatory">
						<input type="text" id="quantita" name="quantita" class="scrittura_campo" value="<?=$farmaco['quantita_caricata']?>" pattern="[0-9]+([\.][0-9]+)?" />
					</div>
				</div>	

				<div class="rigo_mask">
					<div class="testo_mask">Quantità attuale</div>
					<div class="campo_mask mandatory">
						<input type="text" id="quantita_attuale" name="quantita_attuale" class="scrittura_campo" value="<?=$farmaco['quantita_attuale']?>" pattern="[0-9]+([\.][0-9]+)?" />
					</div>
				</div>
				<div class="rigo_mask">
					<div class="testo_mask">Giacenza minima</div>
					<div class="campo_mask mandatory">
						<input type="text" id="giacenza_minima" name="giacenza_minima" class="scrittura_campo" value="<?=$farmaco['giacenza_minima']?>" pattern="[0-9]+([\.][0-9]+)?" />
					</div>
				</div>
			</div>
			
			<div class="riga">	
				<div class="rigo_mask">
					<div class="testo_mask">Stato del farmaco</div>
					<div class="campo_mask mandatory">
						<select id="stato_farmaco" name="stato_farmaco" class="scrittura">
							<option value="1" <?= $farmaco['stato_farmaco'] == 1 ? 'selected' : '' ?> >Non presente</option>
							<option value="2" <?= $farmaco['stato_farmaco'] == 2 ? 'selected' : '' ?> >Presente</option>
							<option value="3" <?= $farmaco['stato_farmaco'] == 3 ? 'selected' : '' ?> >Esaurito</option>
							<option value="5" <?= $farmaco['stato_farmaco'] == 5 ? 'selected' : '' ?> >In scorta</option>
							<option value="4" <?= $farmaco['stato_farmaco'] == 4 ? 'selected' : '' ?> >Non presente in terapia</option>
						</select>
					</div>
				</div>
			</div>
			
			<div class="riga">
				<div class="rigo_mask">
					<div class="testo_mask">Scarico farmaco*</div>
					<div class="campo_mask">
						<input type="checkbox" id="scarico_farmaco" name="scarico_farmaco" />
					</div>
				</div>
				
				<div class="campo_mask">
					<div class="testo_mask">*Lo scarico permette di registrare la rimozione di farmaci dall'anagrafica, l'azione non è reversibile.</div>
				</div>
			</div>
				
			<div id="scarico_farmaco_container" style="display: none;" >
				<div class="rigo_mask">
					<div class="testo_mask">Quantita da scartare</div>
					<div class="campo_mask">
						<input type="text" id="qnt_scarico" name="qnt_scarico" class="scrittura_campo" value="" pattern="[0-9]+([\.][0-9]+)?" />
					</div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Note di scarico</div>
					<div class="campo_mask">
						<textarea id="note_scarico" name="note_scarico" maxlength="200" style="width: 100%;" class="scrittura_campo" ></textarea>
					</div>
				</div>
			</div>
			
			<div class="titolo_pag">
				<div class="comandi">
					<input type="submit"  title="salva" value="salva" class="button_salva"/>			
				</div>
			</div>
		</form>	
	</div>
</div>

<script type="text/javascript" >
	$(document).ready(function() {
		$(".solo_numeri").validation({
			type: "int"
		});
		
		$(".campo_data").mask("99/99/9999");
		
		$("#scarico_farmaco").change(function(e) {
			if(e.currentTarget.checked) {
				$("#scarico_farmaco_container").css("display","");
				$('#qnt_scarico').parent().addClass('mandatory');
				$('#note_scarico').parent().addClass('mandatory');
			} else {
				$("#scarico_farmaco_container").css("display","none");
				$('#qnt_scarico').parent().removeClass('mandatory');
				$('#note_scarico').parent().removeClass('mandatory');
				$('#qnt_scarico').val('');
				$('#note_scarico').val('');
			}
		});
	});
	
	function back_to_list(id_paziente){
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=show_anagrafica_farmaci&id="+id_paziente;
		
		$('#sanitaria').load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}
</script>

<?
}

function associa_farmaco($id_farmaco, $id_paziente)
{
	$conn = db_connect();
	$_sql = "SELECT *
				FROM farmaci
				WHERE id = $id_farmaco";
	$_rs = mssql_query($_sql, $conn);
	
	$farmaco = mssql_fetch_assoc($_rs);
	
	mssql_free_result($_rs);	
	
	$combo_somm_sql = "SELECT etichetta
						FROM campi_combo
						WHERE idcombo = 250 AND valore = " . $farmaco['tipologia_somministrazione'];
	$_rs = mssql_query($combo_somm_sql, $conn);
	
	$row = mssql_fetch_assoc($_rs);
	$tipologia_somministrazione = $row['etichetta'];
	
	mssql_free_result($_rs);
	
	$_farmaci_paz_sql = "SELECT id_paziente_destinatario
							FROM farmaci_paziente_pazienti
							WHERE id_farmaco = $id_farmaco AND id_paziente_proprietario = $id_paziente";
	$_farm_paz_rs = mssql_query($_farmaci_paz_sql, $conn);
	
	$lista_paz_farmaco = array();
	
	while ($row = mssql_fetch_assoc($_farm_paz_rs)) {
		$lista_paz_farmaco[] = $row['id_paziente_destinatario'];
	}

	mssql_free_result($_farm_paz_rs);
	
	$_sql_reparto = "SELECT id_reparto
						FROM reparti_pazienti
						WHERE id_paziente = $id_paziente";
	$_rs = mssql_query($_sql_reparto, $conn);
	
	$row = mssql_fetch_assoc($_rs);
	$id_reparto = $row['id_reparto'];
	
	mssql_free_result($_rs);
?>
<script>inizializza();</script>
<div class="titoloalternativo">
	<h1>Farmacia Paziente</h1>
</div>

<div class="titolo_pag">
	<div class="comandi">
		<div class="indietro indietro_left"><a href="#" onclick="javascript:back_to_list(<?=$id_paziente?>)" >indietro</a></div>
		<div><h1>Associa farmaco ad altro paziente</h1></div>
	</div>
</div>

<div class="blocco_centralcat">
	<div class="form">
		<form method="post" name="form0" action="lista_farmaci_POST.php" id="myForm" enctype="multipart/form-data" >
			<input type="hidden" name="action" value="connect_to_patients" />
			<input type="hidden" name="id" value="<?=$id_farmaco?>" />
			<input type="hidden" name="id_paziente" value="<?=$id_paziente?>" />
			
			<div class="riga">
				<div class="rigo_mask">
					<div class="testo_mask">Farmaco</div>
					<div class="campo_mask"><?=$farmaco['nome'] . ' ' . $farmaco['dosaggio'] . ' (' . $tipologia_somministrazione . ')'?></div>
				</div>
				
				<div class="rigo_mask">
					<div class="testo_mask">Lotto</div>
					<div class="campo_mask"><?=$farmaco['lotto']?></div>
				</div>
			</div>
			
			<div class="riga">			
				<div class="rigo_mask" style="min-width: 400px;" >
					<div class="testo_mask">Lista pazienti*</div>
					<div class="campo_mask mandatory">
						<select id="pazienti" name="pazienti[]" class="scrittura" multiple style="width:350px; height: 250px;" >
<?
	$_user_sql = "SELECT DISTINCT paz.IdUtente, YEAR(paz.DataNascita) as AnnoNasciata, CONCAT(paz.Cognome, ' ',paz.Nome) as Utente, rep_paz.id_reparto
					FROM utenti as paz
					INNER JOIN (
						select idutente
						from utenti_cartelle
						where idutente not in ($id_paziente, 605) AND idregime IN (52,53,59,60,61,62,64,65) AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'
					) as paz_cart_inf ON paz_cart_inf.idutente = paz.IdUtente
					INNER JOIN (
						SELECT DISTINCT rep_paz.id_paziente, rep_paz.id_reparto
						FROM reparti_pazienti rep_paz
						INNER JOIN reparti rep ON rep.id = rep_paz.id_reparto
						WHERE rep.deleted = 0 AND rep.status = 2 AND sede = (SELECT sede FROM reparti rep INNER JOIN reparti_pazienti rep_paz ON rep_paz.id_reparto = rep.id WHERE id_paziente = $id_paziente)
					) as rep_paz ON rep_paz.id_paziente = paz.IdUtente
					WHERE paz.Cognome IS NOT NULL AND paz.Nome IS NOT NULL  AND paz.cancella = 'n' AND paz.stato = 1 AND paz.cancella = 'n'
					ORDER BY Utente, AnnoNasciata ASC";
	$_rs_users = mssql_query($_user_sql, $conn);
	
	while($row = mssql_fetch_assoc($_rs_users)) {
		if($id_reparto == $row['id_reparto']) {
			$symbol = '&#8226;';
		}
			
		echo '<option value="' . $row['IdUtente'] . '" ' . (in_array($row['IdUtente'], $lista_paz_farmaco) ? 'selected' : '') . ' >' . 
					$row['Utente'] . ' (' . $row['IdUtente'] . ') | Anno ' . $row['AnnoNasciata'] . " $symbol" .
				'</option>';
	}
	
	mssql_free_result($_rs); ?>
						</select>
					</div>
				</div>
				<div class="rigo_mask">
					<div class="campo_mask">*Il farmaco verrà aggiunto alla lista farmaci del paziente selezionato. Le quantità sottratte dalle somministrazioni agiranno su questo paziente e non su quello associato.</div>
				</div>
			</div>
			
			<div class="titolo_pag">
				<div class="comandi">
					<input type="submit"  title="salva" value="salva" class="button_salva"/>			
				</div>
			</div>
		</form>	
	</div>
</div>

<script type="text/javascript" >	
	function back_to_list(id_paziente){
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=show_anagrafica_farmaci&id="+id_paziente;
		
		$('#sanitaria').load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}
</script>

<?
}


function show_list()
{

$idutente=$_REQUEST['id'];
$conn = db_connect();

$stato_paziente=get_stato_paziente($idutente);
if ($stato_paziente>0)
{
	$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);
	
}

 $idregime=get_regime_paziente($idutente);
$responsabile=get_responsabile_regime($idregime);

$query = "SELECT * From dbo.re_pazienti_cartelle where idutente=$idutente and cancella='n' ORDER BY id DESC";
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());
	
	
$conta=mssql_num_rows($rs)+1;	

if (mssql_num_rows($rs)>0)
{	
?>

<!--
<div class="menualternativo">
                            	<ul>
                                	<li><a>fsdfdsfds</a></li>
                                    <li><a>fsdfdsfds</a></li>
                                    <li><a>fsdfdsfds</a></li>
                                    <li><a>fsdfdsfds</a></li>
                                </ul>
                            </div>-->
                            
<!-- script per suggermenti -->
<script type="text/javascript" src="script/jtip.js"></script> 

<!-- fine pop-up -->

<script>
	function show_list(idpaziente)
	{
		$("#layer_nero2").toggle();
		$('#cartella_clinica').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria.php?do=&id="+idpaziente;
		$("#cartella_clinica").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	}
</script>

<div id="cartella_clinica">
<div class="titoloalternativo">
	<h1>elenco cartelle cliniche</h1>
	<!--<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
</div>

	


<table id="table_cartelle" class="tablesorter" cellspacing="1"> 
	<thead> 
	<tr> 
	    <th>stato</th> 
	    <!--<th>id</th> -->
		<th>cartella</th> 
	    <th>apertura</th>
		<th width="13%">aperta da</th>
		<th>chiusura</th>
		<th width="13%">chiusa da</th>
		<!--<td>pianificata</td>-->
		<td width="10%">regime</td> 	
		<td width="5%">esplora</td>
		<td width="5%">anteprima</td>
		<td width="5%">istanze</td>
		<td width="5%">test clinici</td>
		<td width="8%">modelli file</td>
		<td width="8%">modifica pianif.</td>
		<?php if($_SESSION['UTENTE']->is_root()) { ?>
			<td width="8%">duplica cartella</td>
		<? } ?>
		<!--<td width="8%">associa test clinici</td> -->
		<td width="8%">chiudi la cartella</td> 
		<!--<td>stampa</td> -->
	    
	</tr> 
	</thead> 
	<tbody> 

	<?
	while($row = mssql_fetch_assoc($rs))   
	{
			$id=$row['id'];
			$codice=$row['idutente'];
			//$codice_cartella=$codice;
			$codice_cartella=$row['codice_cartella'];
			$versione=$row['versione'];
			$medico_ins=$row['nome_ins'];
			$medico_chius=$row['nome_chius'];
			$data_apertura=formatta_data($row['data_creazione']);
			$data_chiusura=$row['data_chiusura'];
			//$data_chiusura=valid_date(formatta_data($row['data_chiusura']));
			$stato=$row['stato'];
			$idregime=$row['idregime'];
			$idnormativa=$row['idnormativa'];
			$motivazione=$row['motivazione'];
			$cod_archiviazione=$row['codice_archiviazione'];
			if(trim($cod_archiviazione)!="")$cod_archiviazione="CA: ".$cod_archiviazione;
			
			$query = "SELECT * from cartelle_pianificazione_testata where id_cartella=$id";	
			
			$rs2 = mssql_query($query, $conn);

			if(!$rs2) error_message(mssql_error());
			if (mssql_num_rows($rs2)>0)
			$img="spunto.png";
			else
			$img="";
		
			if ($data_chiusura!='')
			{
				$img_stato="stato_blu.png";
				$img_close="";
				$data_chiusura=formatta_data($data_chiusura);
			}
			else
			{
				$img_stato="stato_ok.png";			
				$img_close="close.png";
			}
			?>
			
			<tr> 
			<td><img src="images/<?=$img_stato?>" /></td> 
			<!-- <td><?=$id?></td> -->			
			 <td><?=$codice_cartella?>/<?=convert_versione($versione); $cart=$codice_cartella."/".convert_versione($versione);?></td> 
			 <td><?=$data_apertura?></td> 
			 <td><?=$medico_ins?></td> 
			 <td><?=$data_chiusura?></td>
			 <td><?=$medico_chius?></td> 			 
			<?if ($img!=''){?>
			 <!--<td><img src="images/<?=$img?>"/></td>-->
			 <?}else{?>
			 <!--<td>&nbsp;</td> -->
			<? 
			 }

			 $query="SELECT dbo.regime.idregime, dbo.regime.regime as reg, dbo.normativa.normativa as nor FROM dbo.regime INNER JOIN dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa WHERE (dbo.regime.idregime = $idregime)";
			 $rs2 = mssql_query($query, $conn);
			 if ($row2=mssql_fetch_assoc($rs2)){
				$reg=$row2['reg'];
				$nor=$row2['nor'];
			 } 
			 ?>
			 <td><?=$reg."/".$nor?></td> 
			 <? 
			 
			 
			 
			if (($img=='') and ($img_close!='') and (((($responsabile==0) and (controlla_ds())) or (($responsabile==1) and (controlla_dt())))))
			{ 	
				?>
			 <td>&nbsp;</td> 
			  <td><a href="#" onclick="javascript:crea_pianificazione('<?=$id?>','<?=$idutente?>');" ><img src="images/gear.png" /></a></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>	
 			
			 <?}else{?>
			 <td><a href="#" onclick="javascript:visulizza_cartella('<?=$id?>','<?=$idutente?>');" ><img src="images/view.png" /></a></td>
			 <td><a href="#" onclick="javascript:ante_istanze_cartella('<?=$id?>','<?=$idutente?>','<?=$cart?>');"><img src="images/book_open.png" /></a></td>
			  <td><a href="#" onclick="javascript:ante_istanze_cartella_ins('<?=$id?>','<?=$idutente?>','<?=$cart?>');"><img src="images/istanze.png" /></a></td>	
			
			 <td><a href="#" onclick="javascript:visualizza_test_clinici('<?=$id?>','<?=$idutente?>','<?=$cart?>');"><img src="images/test.gif" /></a></td>
			<? 
			 }
			
			?>
			<td><a href="principale.php?mod=ok" target="_blank"><img src="images/modelli_file.png" /></a></td>
			<?

			if( (($responsabile==0) and (controlla_ds())) or (($responsabile==1) and (controlla_dt())))
			  {
			  if ($img_close!='') {?>
			 
				<td>
					<a href="#" onclick="javascript:modifica_pianificazione('<?=$id?>','<?=$idutente?>','<?=$cart?>');"><img src="images/cart_edit.png" /></a>
				</td>
<?php		if($_SESSION['UTENTE']->is_ds() || $_SESSION['UTENTE']->is_root()) { ?>
				<td>
					<a href="#" onclick="javascript:duplica_cartella('<?=$id?>','<?=$idutente?>', '<?=$idregime?>');"><img src="images/icon_mostramenu.png" /></a>					
				</td>
<?php		} ?>
				<!--<td><a href="#" onclick="javascript:modifica_pianificazione_test_clinici('<?=$id?>','<?=$idutente?>','<?=$cart?>');"><img src="images/cart_edit.png" /></a></td>-->
				<td><a href="#" onclick="javascript:chiudi_cartella('<?=$id?>','<?=$idutente?>');" ><img src="images/<?=$img_close?>" /></a></td>
				<?}else{?>
				<td>&nbsp;</td>
				<!--<td>&nbsp;</td>-->				
				<td><?=$cod_archiviazione?></td>
				<?}
			}
			 else
			 {?>
			   <td>&nbsp;</td> 
			   <!--<td>&nbsp;</td>-->
			   <td><?=$cod_archiviazione?></td>
			<?}
			  //if ( (controlla_ds()) or ( ($img_close!='') and (controlla_permesso_stampa($idregime)) )) {?>
			  <!-- <td><a href="#" onclick="javascript:stampa_cartella('<?=$id?>','<?=$idutente?>');" ><img src="images/gear.png" /></a></td> -->
			<?
			//}
			// else
			// {?>
			 <!--  <td>&nbsp;</td> -->
			<?//}?>

			
			</tr> 		 
			
			<?
	}	

	?>


	</tbody> 
	</table> 


<? 
footer_paginazione($conta, 'pager_cartelle');
?>
	</div>

	<script type="text/javascript" id="js">

	function visulizza_cartella(iddiv,idpaziente)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_cartella&idcartella="+iddiv+"&idpaziente="+idpaziente;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	}
	
	function visualizza_test_clinici(iddiv,idpaziente,cartella)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_cartelle.php?do=visualizza_test_clinici&idcartella="+iddiv+"&idpaziente="+idpaziente+"&cartella="+cartella;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	}
	
	function view_istanze_modulo(idcartella,idpaziente,idmodulo,cart,impegnativa)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+impegnativa;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function edita_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,impegnativa)
	{
	div='#cartella_clinica';
	
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=edita_modulo&cartella="+cartella+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&impegnativa="+impegnativa;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function ante_istanze_cartella(idcartella,idpaziente,cart)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=anteprima_istanze_cartella&idcartella="+idcartella+"&idpaziente="+idpaziente+"&cartella="+cart.replace(" ", "%20");		// sostituisce lo spazio, altrimenti si spezza la var
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function ante_istanze_cartella_ins(idcartella,idpaziente,cart)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=anteprima_istanze_cartella_ins&idcartella="+idcartella+"&idpaziente="+idpaziente+"&cartella="+cart.replace(" ", "%20");
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function modifica_pianificazione(idcartella,idpaziente,cart)
	{
		$("#layer_nero2").toggle();
		$('#cartella_clinica').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_pianificazione&idcartella="+idcartella+"&idpaziente="+idpaziente+"&cartella="+cart.replace(" ", "%20");
		$("#cartella_clinica").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	}
	
	function duplica_cartella(idcartella,idpaziente,idregime)
	{
		small_window("popup_duplica_cartella.php?idcartella=" + idcartella + "&idpaziente=" + idpaziente + "&idregime=" + idregime, 600, 350);
	}
	
	function modifica_pianificazione_test_clinici(idcartella,idpaziente,cart)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_pianificazione_test_clinici&idcartella="+idcartella+"&idpaziente="+idpaziente+"&cartella="+cart;;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function crea_pianificazione(iddiv,idpaziente)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_pianificazione&idcartella="+iddiv+"&idpaziente="+idpaziente;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function chiudi_cartella(iddiv,idpaziente)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=chiudi_cartella&idcartella="+iddiv+"&idpaziente="+idpaziente;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function crea_visita_specialistica(iddiv,idpaziente)
	{
	$("#layer_nero2").toggle();
	$('#sanitaria').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=crea_visita_specialistica&idcartella="+iddiv+"&idpaziente="+idpaziente;
	$("#sanitaria").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}

	
	$(document).ready(function() {
		
		  $("#table_cartelle").tablesorter({ widgets: ['zebra']}).tablesorterPager({container: $("#pager_cartelle")}); 
		  cartella_da_aprire = self.document.location.hash.substring(1);
		  Arr_do=cartella_da_aprire.split("&");
		  Arr=Arr_do[0].split("#");		 
		  if(cartella_da_aprire!="" && cartella_da_aprire!="goodday"){		  
		  parent.location.hash = "goodday";		  
		  if(Arr_do[1]=="load")  view_istanze_modulo(Arr[0],<?=$idutente?>,Arr[1],'<?=$cart?>',Arr[2]);
		  if(Arr_do[1]=="mod")  visulizza_cartella(Arr[0],'<?=$idutente?>');								
		  if(Arr_do[1]=="edit")  {			 
			edita_modulo('<?=$cart?>',Arr[0],<?=$idutente?>,Arr[1],Arr[2],'sanitaria',Arr[4],view_istanze_modulo(Arr[0],<?=$idutente?>,Arr[1],'<?=$cart?>',Arr[4]));
			}
		  }

	});
	</script>


	


	<?
}
else
{


?>
<div id="cartella_clinica">
<div class="titoloalternativo">
        <h1>elenco cartelle cliniche</h1>
</div>
<div class="info">Nessuna cartella clinica trovata.</div>	
</div>

<script type="text/javascript" id="js">
function view_istanze_modulo(idcartella,idpaziente,idmodulo,cart,impegnativa)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+impegnativa;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function edita_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg)
	{
	div='#cartella_clinica';
	
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=edita_modulo&cartella="+cartella+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}

$(document).ready(function() {
		
		  $("table").tablesorter({ widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
		  cartella_da_aprire = self.document.location.hash.substring(1);
		  Arr_do=cartella_da_aprire.split("&");
		  Arr=Arr_do[0].split("#");		 
		  if(cartella_da_aprire!="" && cartella_da_aprire!="goodday"){		  
		  parent.location.hash = "goodday";
		  if(Arr_do[1]=="load")  view_istanze_modulo(Arr[0],<?=$idutente?>,Arr[1],'<?=$cart?>',Arr[2]);
		  if(Arr_do[1]=="edit")  {			 
			edita_modulo('<?=$cart?>',Arr[0],<?=$idutente?>,Arr[1],Arr[2],'sanitaria',view_istanze_modulo(Arr[0],<?=$idutente?>,Arr[1],'<?=$cart?>'));
			}
		  }

	});
	</script>
<?

}

}



 

if((isset($_SESSION['UTENTE'])) and ($_SESSION['UTENTE']->get_tipoaccesso()!=1)){
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) 
	{

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
					else show_allegati($_REQUEST['id'],$_REQUEST['idcart']);
			break;
			
			case "show_anagrafica_farmaci":
				if($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					show_anagrafica_farmaci($_REQUEST['id']);
				} else {					
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;
			
			case "aggiungi_farmaco":
				if($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					aggiungi_farmaco($_REQUEST['id_paziente']);
				} else {					
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;
			
			case "view_farmaco":
				if($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					view_farmaco($_REQUEST['id_farmaco'], $_REQUEST['id_paziente']);
				} else {					
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;

			case "edit_farmaco":
				if($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					edit_farmaco($_REQUEST['id_farmaco'], $_REQUEST['id_paziente']);
				} else {					
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;
			
			case "associa_farmaco":
				if($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					associa_farmaco($_REQUEST['id_farmaco'], $_REQUEST['id_paziente']);
				} else {					
					error_message("Permessi insufficienti per questa operazione!");
				}
			break;
			
			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
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
	print("non hai i permessi per visualizzare questa pagina!");
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>