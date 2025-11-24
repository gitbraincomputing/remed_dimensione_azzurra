<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 58;
$tablename = 'regime';
include_once('include/function_page.php');

function show_list(){
?>
<script>
function conf_cancella(id){
if (confirm('sei sicuro di voler cancellare il range corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "ajax_range_test.php?id_range="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 if(msg==""){
				 $.ajax({
					   type: "POST",
					   url: "lista_range_POST.php?action=del_range&id_range="+id,
					   data: "",
					   success: function(msg){
						 //alert( "Data Saved: " + msg );
						 reload_tipologie();
						}
					 });
			 }else{
				alert('Impossibile cancellare il range in quanto presente all\'interno di un test');
				return false;	
			 }
			 
			}
		 });
	  }
	else return false;
}
function reload_tipologie(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_range.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	 });
}
	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione Test Clinici</a></div>
            <div class="elem_pari"><a href="#">elenco range</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_range.php?do=add');">aggiungi Range</a></div>
		</div>
	</div>

<?
$conn = db_connect();

$query = "select * from test_clinici_range order by Nome asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);

if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Range</h1>
	</div>
	<div class="info">Non esistiono Range.</div>
	
	
	<?
	exit();}?>
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th>id range</th> 
	<th>nome</th> 
   <td width="5%">modifica</td>
   <td width="5%">cancella</td> 
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{

while($row = mssql_fetch_assoc($rs))
{   

		$id=$row['idrange'];
		$nome=pulisci_lettura($row['Nome']);
		
		?>
		
		<tr> 
		 <td><?=$id?></td> 
		 <td><?=$nome?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_range.php?do=edit&id=<?=$id?>');" href="#"><img src="images/gear.png" /></a></td> 
		 <td><a onclick="conf_cancella('<?=$id?>');" href="#"><img src="images/remove.png" /></a></td>
		</tr> 
		
		<?
//}
}	

?>

</tbody> 
</table> 
<? 
footer_paginazione($conta);
?>
</div></div>
<script>
$(document).ready(function() {
	
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
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

<?
//footer_new();

}

function add()
{

//$nome_pagina="gestione normative";
//header_new($nome_pagina);
//barra_top_new();
//top_message();
//menu_new();
//pagina_new();
?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_range.php');" href="#">elenco Range</a></div>
			<div class="elem_dispari"><a href="#">aggiungi Range</a></div>
          
        </div>

<form  method="post" name="form0" action="lista_range_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Range</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">nome range</div>
			<div class="campo_mask mandatory">
				<input type="text" name="nome_range" class="scrittura" size="50"/>
			</div>
		</div>
    </div>    
	
	
	  
    

	<div class="titolo_pag"><h1>Dettaglio Range</h1></div>	
			
		<div class="blocco_centralcat">	

			
<script type="text/javascript">
/*$(document).ready(function() {
    // Initialise the table
    $("#elenco").tableDnD();
});*/


$(document).ready(function() {


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





</script>
			

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

<table id="elenco" style="clear:both; width: 100%;" cellspacing="2" cellpadding="2" border="0">

 <tbody id="tbody">
<!--
 <tr class="riga_tab nodrag nodrop">
<td width="5%" align="center">ordine</td>
<td width="20%" align="center">item</td>
<td width="15%" align="center">tipo campo</td>
<td width="15%" align="center">segnalibro</td>

<td width="20%" align="center">editabile</td>
<td width="15%" align="center">obbligatorio</td>
<td width="5%">validazione</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
</tr>
-->
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
	<!--<tr id="<?=$i?>" <?=$style?> class="<?=$class?>"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('<?=$i?>','su');">su</a> - <a OnClick="javascript:DinamicDiv('<?=$i?>','giu');">giu</a></span></td>-->
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
		<td colspan="8"><table class="table_padre_<?=$i?>" style="width: 100%">
		<tr class="riga_tab nodrag nodrop">
			<td width="5%" align="center">ordine</td>
			<td width="5%" align="center">descrizione</td>
			<td width="5%" align="center">tipo dato per valutazione limite</td>
			<td width="5%" align="center">et&agrave; da</td>
			<td width="5%" align="center">et&agrave; a</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">elimina</td>
		</tr>
		<tr>
		<td align="center"><span class="id_notizia_cat"></span></td>
		<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" /></td>
		<td align="center">
		
			<select id="tipo_dato<?=$i?>" name="tipo_dato<?=$i?>">
				<option value="1">Numerico</option>
				<option value="4">Decimale</option>
				<option value="2">Data</option>
				<option value="3">Orario</option>
			</select>
		
		</td>
		<td align="center"><input id="vi<?=$i?>" name="vi<?=$i?>" value="" /></td>
<td align="center"><input id="vf<?=$i?>" name="vf<?=$i?>" value="" /></td>
		<td>
		&nbsp;
		</td>
		<td>
		<a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a>
		</td>
		</tr>

		</table>
		
		
		</td>
	</tr>
<?
$i++;
}

?>

<tr id="1000" class="riga_sottolineata_diversa nodrag nodrop"><td align="center"><span><a OnClick="javascript:DinamicDiv('0','add','<?=$numerofield?>');"><img src="images/add.png" /></a></td></tr>
</tbody>
</table>
	
	</div>
	
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	
	</form>
    
		
		
</div></div>
<?
//footer_new();

}

function edit($id)
{

	$conn = db_connect();

	$query = "Select * from test_clinici_range WHERE idrange=$id";
	$nome_range="";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$nome_range = pulisci_lettura($row['Nome']);
		
	}

	// rimuove i caratteri di escape
	$nome_range = stripslashes($nome_range);
	

	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_range.php');" href="#">elenco Range</a></div>
			 <div class="elem_dispari"><a href="#">modifica Range</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_range_POST.php" id="myForm">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?php echo $id ?>" />

	<div class="titolo_pag"><h1>Modifica Range <?=$nome_range?></h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">nome range</div>
			<div class="campo_mask mandatory">
				<input type="text" name="nome_range" class="scrittura" value="<?=$nome_range?>">
			</div>
		</div>
    </div> 
	
	
	
	
<input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">			

<table id="elenco" style="clear:both; width: 100%;" cellspacing="2" cellpadding="2" border="0">

 <tbody id="tbody">
 <tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
		<td colspan="8"><table class="table_padre_<?=$i?>" style="width: 100%">
		<tr class="riga_tab nodrag nodrop">
			<td width="5%" align="center">ordine</td>
			<td width="5%" align="center">descrizione</td>
			<td width="5%" align="center">tipo dato per valutazione limite</td>
			<td width="5%" align="center">et&agrave; da</td>
			<td width="5%" align="center">et&agrave; a</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">elimina</td>
		</tr>

<?
$query="select * from test_clinici_range_details where idrange=$id and stato=1 and cancella='n'";

$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
$i=1;
	while($row = mssql_fetch_assoc($rs)){
	$tipo_dato=$row['tipo_dato'];
	$descrizione=$row['descrizione'];
	$valore_da=$row['valore_da'];
	$valore_a=$row['valore_a'];
	$idcampo=$row['idrange_detail'];

		?>
		<tr id="<?=$i?>" >
		<td align="center"><span class="id_notizia_cat"></span></td>
		<td align="center" class="nodrag nodrop"><input id="et<?=$i?>"  name="etichetta<?=$i?>" value="<?=$descrizione?>" /></td>
		<td align="center">
	
		
			<select id="tipo_dato<?=$i?>" name="tipo_dato<?=$i?>">
				<option <? if ($tipo_dato==1) echo("selected"); ?> value="1">Numerico</option>
				<option <? if ($tipo_dato==4) echo("selected"); ?> value="4">Decimale</option>
				<option <? if ($tipo_dato==2) echo("selected"); ?> value="2">Data</option>
				<option <? if ($tipo_dato==3) echo("selected"); ?> value="3">Orario</option>
			</select>
		
		</td>
		<td align="center"><input id="vi<?=$i?>" name="vi<?=$i?>" value="<?=$valore_da?>" /></td>
		<td align="center"><input id="vf<?=$i?>" name="vf<?=$i?>" value="<?=$valore_a?>" /></td>
		<td>
		&nbsp;
		</td>
		<td>
			<input type="hidden" name="idcampo<?=$i?>" value="<?=$idcampo?>" >
		<input type="hidden" id="elim<?=$i?>" name="elimina<?=$i?>" value="">
		<a OnClick="javascript:document.getElementById('elim<?=$i?>').value='si';DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a></td>
		</td>
		</tr>
		
		<?
		$i++;
	}



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
		<td align="center"><span class="id_notizia_cat"></span></td>
		<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" /></td>
		<td align="center">
		
			<select id="tipo_dato<?=$i?>" name="tipo_dato<?=$i?>">
				<option value="1">Numerico</option>
				<option value="4">Decimale</option>
				<option value="2">Data</option>
				<option value="3">Orario</option>
			</select>
		
		</td>
		<td align="center"><input id="vi<?=$i?>" name="vi<?=$i?>" value="" /></td>
<td align="center"><input id="vf<?=$i?>" name="vf<?=$i?>" value="" /></td>
		<td>
		&nbsp;
		</td>
		<td>
		<a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a>
		</td>
		</tr>

		
<?
$i++;
}

?>

<tr id="1000" class="riga_sottolineata_diversa nodrag nodrop"><td align="center"><span><a OnClick="javascript:DinamicDiv('0','add','<?=$numerofield?>');"><img src="images/add.png" /></a></td></tr>
</tbody>
</table>	
	
	
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	
	</form>
    
		
		
</div></div>
	
	
	
	<?
}




if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

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
					else update($_POST['id']);
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

			case "edit":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit($_REQUEST['id']);
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
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>

