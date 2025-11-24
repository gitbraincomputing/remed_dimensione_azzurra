<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 42;
$tablename = 'combo';
$mod_type=1;
include_once('include/function_page.php');

function show_list()
{
global $mod_type;


$conn = db_connect();
?>
<script>
function conf_cancella(id){
if (confirm('sei sicuro di voler cancellare la combo corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "ajax_combo_moduli.php?id=1&id_combo="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 if(msg==""){
				 $.ajax({
					   type: "POST",
					   url: "lista_combo_POST.php?action=del_combo&id_combo="+id,
					   data: "",
					   success: function(msg){
						 //alert( "Data Saved: " + msg );
						 reload_tipologie();
						}
					 });
			 }else{
				alert('Impossibile cancellare la combo in quanto presente all\'interno di un modulo');
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
	pagina_da_caricare="lista_combo.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione combo</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_combo.php');" href="#">elenco combo</a></div>
          
        </div>

	<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_combo.php?do=add');">aggiungi combo</a></div>
		</div>
	</div>
<?
$query = "SELECT * from combo WHERE (tipo=$mod_type) order by nome asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Combo</h1>
	</div>
	<div class="info">Non esistono combo.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>id</th> 
	<th>nome</th> 
    <th>descrizione</th>
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
		$id=$row['id'];
		$nome=pulisci_lettura($row['nome']);
		$descrizione=pulisci_lettura($row['descrizione']);
		?>
		<tr> 
		 <td><?=$id?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$descrizione?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_combo.php?do=edit&id=<?=$id?>');" href="#"><img src="images/gear.png" /></a></td>
		<td><a onclick="conf_cancella('<?=$id?>');" href="#"><img src="images/remove.png" /></a></td>
		</tr>
		<?
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

function add(){
global $mod_type;

?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
           <div class="elem_pari"><a onclick="javascript:load_content('div','lista_combo.php');" href="#">elenco combo</a></div>
			<div class="elem_dispari"><a href="#">aggiungi combo</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_combo_POST.php" id="myForm">
		<input type="hidden" name="action" value="create" />

	
	
	<div class="blocco_centralcat">
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">
				<span class="need">nome</span></div>
				<div class="mandatory campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="255" value="">
				</div>
			</div>
            
            <div class="rigo_mask">
                <div class="testo_mask">multivalore</div>
                <div class="campo_mask mandatory">
                    <select name="multi_valore">
                    <option value="1">Si</option>
					<option value="2">Si - elenco</option>
					<option value="3">Si - lista</option>
                    <option value="0">No</option>
                    </select>
                </div>
            </div>
			
			<div class="rigo_mask">
                <div class="testo_mask">separatore</div>
                <div class="campo_mask nomandatory">
                    <select name="separatore">
                    <option value="0">ritorno a capo</option>
                    <option value="1">separato da " - " </option>
					<option value="2">separato da " , "</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="riga">
            <div class="rigo_mask rigo_big">
                <div class="testo_mask">descrizione</div>
                <div class="campo_mask">
                    <textarea name="descr" class="scrittura_campo"></textarea>
                </div>
            </div>
		</div>        
	</div>

<div class="titolo_pag"><h1>Campi del modulo</h1></div>	
			
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

<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">

 <tbody id="tbody">
<tr class="riga_tab nodrag nodrop">
<td width="20%" align="left">&nbsp;</td>
<td width="5%" align="center">ordine</td>
<td width="20%" align="center">etichetta</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
<td width="45%">&nbsp;</td>
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

	

	<!--<tr id="<?=$i?>" <?=$style?> class="<?=$class?>"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('<?=$i?>','su');">su</a> - <a OnClick="javascript:DinamicDiv('<?=$i?>','giu');">giu</a></span></td>-->
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<td align="center">&nbsp;</td>
    <td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" style="width:500px;"/></td>	  
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





function edit($id){


	$conn = db_connect();

	$query = "SELECT * from combo WHERE id=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$id=$row['id'];
		$nome = pulisci_lettura($row['nome']);
		$descrizione = pulisci_lettura($row['descrizione']);
		$multi=$row['multi'];
		$separatore=$row['separatore'];
	}

	// rimuove i caratteri di escape
	
	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
         <div class="elem_pari"><a onclick="javascript:load_content('div','lista_combo.php');" href="#">elenco combo</a></div>
			 <div class="elem_dispari"><a href="#">modifica combo</a></div>
          
        </div>


<form method="post" name="form0" action="lista_combo_POST.php" id="myForm">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?=$id?>" />
	
	
	<div class="titolo_pag"><h1>Combo</h1></div>
	
		<div class="blocco_centralcat">
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">
				<span class="need">nome</span></div>
				<div class="mandatory campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="255" value="<?=$nome?>">
				</div>
			</div>
            
            <div class="rigo_mask">
                <div class="testo_mask">multivalore</div>
                <div class="campo_mask mandatory">
                    <select name="multi_valore">
                    <option value="1" <?php if ($multi=='1') echo("selected");?>>Si</option>
                    <option value="2" <?php if ($multi=='2') echo("selected");?>>Si - elenco</option>
					<option value="3" <?php if ($multi=='3') echo("selected");?>>Si - lista</option>
					<option value="0" <?php if ($multi=='0') echo("selected");?>>No</option>
                    </select>
                </div>
            </div>
			
			<div class="rigo_mask">
                <div class="testo_mask">separatore</div>
                <div class="campo_mask nomandatory">
                    <select name="separatore">
                    <option value="0" <?php if ($separatore=='0') echo("selected");?>>ritorno a capo</option>
                    <option value="1" <?php if ($separatore=='1') echo("selected");?>>separato da " - " </option>
					<option value="2" <?php if ($separatore=='2') echo("selected");?>>separato da " , "</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="riga">
            <div class="rigo_mask rigo_big">
                <div class="testo_mask">descrizione</div>
                <div class="campo_mask">
                    <textarea name="descr" class="scrittura_campo"><?=$descrizione?></textarea>
                </div>
            </div>
		</div>        
	</div>


<div class="titolo_pag"><h1>Campi del modulo</h1></div>	
			
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
while ($d<100)
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

 <tbody id="tbody">
<tr class="riga_tab nodrag nodrop">
<td width="20%" align="left">&nbsp;</td>
<td width="5%" align="center">ordine</td>
<td width="20%" align="center">etichetta</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
<td width="45%">&nbsp;</td>
<?

$query="select * from campi_combo where (idcombo='$id') and(cancella='n') and (stato=1) order by peso";
$rs = mssql_query($query, $conn);

	
$i=1;
$numerofield=100;
while($row1 = mssql_fetch_assoc($rs)){

		$idcampo= $row1['idcampo'];
		$etichetta= pulisci_lettura($row1['etichetta']);			
		$class="riga_sottolineata_diversa";
?>	

	<!--<tr id="<?=$i?>" <?=$style?> class="<?=$class?>"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('<?=$i?>','su');">su</a> - <a OnClick="javascript:DinamicDiv('<?=$i?>','giu');">giu</a></span></td>-->
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<td align="center">&nbsp;</td>
    <td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" name="etichetta<?=$i?>" value="<?=$etichetta?>" style="width:500px;"/></td>	
	<td>&nbsp;</td>
    <input type="hidden" id="el<?=$i?>" name="elimina<?=$i?>" value="" />
	<input type="hidden" id="idcampo<?=$i?>" name="idcampo<?=$i?>" value="<?=$idcampo?>" />
	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina_c','<?=$numerofield?>');document.getElementById('el'+<?=$i?>).value='si';"><img src="images/remove.png" /></a></td>
	</tr>

<?
$i++;
}

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
	<td align="center">&nbsp;</td>
    <td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" style="width:500px;"/></td>	   
	<td>&nbsp;</td>
	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a></td>
	</tr>


<?
$i++;
}



?>

<tr id="1000" class="riga_sottolineata_diversa nodrag nodrop"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('0','add','<?=$numerofield?>');" class="puntatore"><img src="images/add.png" /></a></td></tr>
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


}






if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			

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

