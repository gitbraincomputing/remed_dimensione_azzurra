<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 11;
$tablename = 'utenti';
include_once('include/function_page.php');

function show_list()
{

//$nome_pagina="gestione moduli";
//header_new($nome_pagina,false);
//barra_top_new();
//top_message();
//menu_new();


$conn = db_connect();

$query = "SELECT * from re_distinct_moduli";	
$rs1 = mssql_query($query, $conn);

if(!$rs1) error_message(mssql_error());

$conta=mssql_num_rows($rs1);

//pagina_new();
?>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione moduli</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli.php');" href="#">elenco moduli</a></div>
          
        </div>

	<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_moduli.php?do=add');">aggiungi modulo</a></div>
		</div>
	</div>

<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>id</th> 
	<th>nome</th> 
    <th>descrizione</th> 
    
	
    
</tr> 
</thead> 
<tbody> 

<?
while($row1 = mssql_fetch_assoc($rs1))   
{
$idmodulo=$row1['idmodulo'];

$query = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc";	
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

while($row = mssql_fetch_assoc($rs))
{   

		$id=$row['id'];
		$nome=$row['nome'];
		$descrizione=$row['descrizione'];
	
	
	
		?>
		
		<tr> 
		 <td><?=$id?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$descrizione?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_moduli.php?do=edit&id=<?=$id?>');" href="#"><img src="images/gear.png" /></a></td> 
		</tr> 
		
		
		<?
}
}	

?>


</tbody> 
</table> 
<? 
$numero_pager=(int)($conta/20)

?>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a>aggiungi modulo</a></div>
         
            <div id="pager" class="pager">
                <form>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/first.png" class="first"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/prev.png" class="prev"/>
                    <input type="text" class="pagedisplay"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/next.png" class="next"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/last.png" class="last"/>
                    <select class="pagesize">
						
						<? 
						$y=0;
						$x=20;
					if ($conta>$x)
					{
						while ($y<$numero_pager)
						{
						if ($y==0)
						$sel="selected";
						else
						$sel="";
						
						?>
					    <option <?=$sel?> value="<?=$x?>"><?=$x?></option>
                     
						<?
						$y++;
						$x=$x+20;
						}
					}	
						?>
					 <option  value="<?=$conta?>">tutti</option>
                    </select>
                </form>
            </div>
		</div>
</div>
	




</div></div>


<script>
$(document).ready(function() {
	
	  $("table").tablesorter({sortList: [[1,1]] ,widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
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


?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
           <div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli.php');" href="#">elenco moduli</a></div>
			<div class="elem_dispari"><a href="#">aggiungi modulo</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_moduli_POST.php">
		<input type="hidden" name="action" value="create" />

	
	
	<div class="blocco_centralcat">
		
			<div class="rigo_mask">
				<div class="testo_mask">
				<span class="need">nome</span></div>
				<div class="mandatory campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="255" value="">
				</div>
			</div>
            
            <div class="rigo_mask">
                <div class="testo_mask">stampa continua</div>
                <div class="campo_mask mandatory">
                    <select name="stampa_continua">
                    <option value="1">Si</option>
                    <option value="0">No</option>
                    </select>
                </div>
            </div>
            
            <div class="rigo_mask">
                <div class="testo_mask">disponibile per lista d'attesa</div>
                <div class="campo_mask mandatory">
                    <select name="lista_attesa">
                    <option value="1">Si</option>
                    <option value="0">No</option>
                    </select>
                </div>
            </div>

            <div class="rigo_mask rigo_big">
                <div class="testo_mask">descrizione</div>
                <div class="campo_mask">
                    <textarea name="descr" class="scrittura_campo"></textarea>
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
<td width="5%" align="center">ordine</td>
<td width="20%" align="center">etichetta</td>
<td width="15%" align="center">tipo campo</td>

<td width="20%" align="center">editabile</td>
<td width="15%" align="center">obbligatorio</td>
<td width="5%">validazione</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
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
	$class="riga_sottolineata_diversa";
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
	<td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" /></td>
	<td align="center">
	
		<select name="tipo<?=$i?>">
			<option value="1">testo</option>
			<option value="2">memo</option>
			<option value="3">data</option>
			<option value="4">combo</option>
			<option value="5">selezione multipla</option>
		</select>
	
	</td>
	
	<td align="center">
	<select name="editabile<?=$i?>">
	<option value="y">Si</option>
	<option value="n">No</option>
	</select></td>
	<td align="center">
	<select name="obbligatorio<?=$i?>">
	<option value="y">Si</option>
	<option value="n">No</option>
	</select></td>
	
	<td align="center">
	<select name="tipoclasse<?=$i?>">
	<option value="">nessuna</option>
	<option value="integer">intero</option>
	<option value="email">email</option>
	</select></td>
	<td>&nbsp;
	
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

	$query = "SELECT * from moduli WHERE id=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$id=$row['id'];
		$nome = $row['nome'];
		$descrizione = $row['descrizione'];
		$idmodulo=$row['idmodulo'];
		$versione=$row['versione'];
		$stampa_continua=$row['stampa_continua'];
		$lista_attesa=$row['lista_attesa'];
		
	}

	// rimuove i caratteri di escape
	$nome = stripslashes($nome);
	$descrizione = stripslashes($descrizione);

	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
         <div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli.php');" href="#">elenco moduli</a></div>
			 <div class="elem_dispari"><a href="#">modifica modulo</a></div>
          
        </div>


<form method="post" name="form0" action="lista_moduli_POST.php">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?=$id?>" />
	
	
	<div class="titolo_pag"><h1>Modulo</h1></div>
	
		<div class="blocco_centralcat">
		
			<div class="rigo_mask">
				<div class="testo_mask">
				<span class="need">nome</span></div>
				<div class="mandatory campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="255" value="<?=$nome?>">
				</div>
			</div>
            
            <div class="rigo_mask">
                <div class="testo_mask">stampa continua</div>
                <div class="campo_mask mandatory">
                    <select name="stampa_continua">
                    <option value="1"<?if ($stampa_continua==1) echo ("selected")?>>Si</option>
                    <option value="0"<?if ($stampa_continua==0) echo ("selected")?>>No</option>
                    </select>
                </div>
            </div>
            
            <div class="rigo_mask">
                <div class="testo_mask">disponibile per lista d'attesa</div>
                <div class="campo_mask mandatory">
                    <select name="lista_attesa">
                    <option value="1"<?if ($lista_attesa==1) echo ("selected")?>>Si</option>
                    <option value="0"<?if ($lista_lista==1) echo ("selected")?>>No</option>
                    </select>
                </div>
            </div>

            <div class="rigo_mask rigo_big">
                <div class="testo_mask">descrizione</div>
                <div class="campo_mask">
                    <textarea name="descr" class="scrittura_campo"><?=$descrizione?></textarea>
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
			
<table style="clear:both;" id="elenco" cellspacing="2" cellpadding="2" border="0">
<tbody id="tbody">
<tr class="riga_tab nodrag nodrop">
<td width="5%" align="center">&nbsp;</td>
<td width="20%" align="center">nome</td>
<td width="15%" align="center">tipo campo</td>

<td width="20%" align="center">editabile</td>
<td width="15%" align="center">obbligatorio</td>
<td width="5%">validazione</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
</tr>
<?

$query="select * from campi where idmoduloversione=$id";

$rs = mssql_query($query, $conn);

	
$i=1;
$numerofield=100;
while($row1 = mssql_fetch_assoc($rs)){

		$idcampo= $row1['idcampo'];
		$etichetta= $row1['etichetta'];
		$tipo = $row1['tipo'];
		$editabile = $row1['editabile'];
		$obbligatorio = $row1['obbligatorio'];
		$classe=$row1['classe'];
		$class="riga_sottolineata_diversa";
?>	

	<!--<tr id="<?=$i?>" class="<?=$class?>"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('<?=$i?>','su');">su</a> - <a OnClick="javascript:DinamicDiv('<?=$i?>','giu');">giu</a></span></td>-->
	<tr id="<?=$i?>" class="<?=$class?>"><td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center">
	<input type="hidden" name="idcampo<?=$i?>" value="<?=$idcampo?>" />
		<input id="et<?=$i?>" name="etichetta<?=$i?>" value="<?=$etichetta?>" />
	</td>
	
	<td align="center">
	
		<select name="tipo<?=$i?>">
			<option value="1" <?if ($tipo==1) echo("selected");?>>testo</option>
			<option value="2" <?if ($tipo==2) echo("selected");?>>memo</option>
			<option value="3" <?if ($tipo==3) echo("selected");?>>data</option>
			<option value="4" <?if ($tipo==4) echo("selected");?>>combo</option>
			<option value="5" <?if ($tipo==5) echo("selected");?>>selezione multipla</option>
		</select>
	
	</td>
	
	<td align="center">
	<select name="editabile<?=$i?>">
	<option value="y" <?if ($editabile=='y') echo("selected");?>>Si</option>
	<option value="n" <?if ($editabile=='n') echo("selected");?>>No</option>
	</select></td>
	<td align="center">
	<select name="obbligatorio<?=$i?>">
	<option value="y" <?if ($obbligatorio=='y') echo("selected");?>>Si</option>
	<option value="n" <?if ($obbligatorio=='n') echo("selected");?>>No</option>
	</select></td>
	
	<td align="center">
	<select name="tipoclasse<?=$i?>">
	<option value="" selected >nessuna</option>
	<option value="integer" <?if ($classe=='integer') echo("selected");?>>intero</option>
	<option value="email" <?if ($classe=='email') echo("selected");?>>email</option>
	
	</select></td>
	<td>&nbsp;
	
	</td>
	<td>
	<a OnClick="javascript:DinamicDiv('<?=$i?>','elimina',,'<?=$numerofield?>');"><img src="images/remove.png" /></a>
	</td>
	
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

