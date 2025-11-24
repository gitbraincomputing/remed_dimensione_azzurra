<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 40;
$tablename = 'tipologia';
include_once('include/function_page.php');

function show_list()
{
$conn = db_connect();

?>
<script>
function conf_cancella(id){
if (confirm('sei sicuro di voler cancellare la tipologia corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_tipologie_POST.php?action=del_tipologia&id_tipologia="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 reload_tipologie();
			}
		 });	  
	  }
	else return false;
}
function reload_tipologie(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_tipologie.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione ASL</a></div>
            <div class="elem_pari"><a href="#">elenco tipolgie</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_tipologie.php?do=add');">aggiungi tipologia</a></div>
		</div>
	</div>

<?
	$query = "SELECT dbo.tipologia.descrizionelunga, dbo.tipologia.descrizione, dbo.tipologia.idregime, dbo.tipologia.codice, dbo.regime.regime, dbo.tipologia.stato, dbo.tipologia.cancella, dbo.normativa.normativa, dbo.tipologia.idtipologia FROM dbo.tipologia INNER JOIN dbo.regime ON dbo.tipologia.idregime = dbo.regime.idregime INNER JOIN dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa where ( dbo.tipologia.cancella='n') order by dbo.tipologia.descrizione asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());

if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Tipologie</h1>
	</div>
	<div class="info">Non esistiono tipolgie.</div>
	
	
	<?
	exit();}?>	
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th>id</th> 
	<th>nome</th> 
    <th>descrizione</th>
    <th>regime</th>
	<th>modifica</th>
	<th>cancella</th>
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idtipologia=$row1['idtipologia'];

while($row = mssql_fetch_assoc($rs))
{   

		$idtipologia=$row['idtipologia'];
		$descrizione=$row['descrizione'];
		$descrizionelunga=$row['descrizionelunga'];
		$codice=$row['codice'];
		$regime=$row['regime']." - ".$row['normativa'];		
		
		?>
		
		<tr> 
		 <td><?=$idtipologia?></td> 
		 <td><?=pulisci_lettura($descrizione)?></td> 
		 <td><?=pulisci_lettura($descrizionelunga)?></td>
         <td><?=pulisci_lettura($regime)?></td>          
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_tipologie.php?do=edit&id=<?=$idtipologia?>');" href="#"><img src="images/gear.png" /></a></td>
		<td><a id="<?=$id?>" onclick="conf_cancella('<?=$idtipologia?>');" href="#"><img src="images/remove.png" /></a></td> 
		</tr> 
		
		<?
}
//}	

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
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_tipologie.php');" href="#">elenco tipologie</a></div>
			<div class="elem_dispari"><a href="#">aggiungi tipolgia</a></div>
          
        </div>

<form  method="post" name="form0" action="lista_tipologie_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Tipologie</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">Descrizione</div>
			<div class="campo_mask mandatory">
				<input type="text" name="descrizione" class="scrittura" size="50"/>
			</div>
		</div>
        <div class="rigo_mask">
			<div class="testo_mask">Codice</div>
			<div class="campo_mask mandatory">
				<input type="text" name="codice" class="scrittura" size="50"/>
			</div>
		</div>
    </div> 
    <div class="riga">
    	<div class="rigo_mask">
			<div class="testo_mask">Descrizione lunga</div>
			<div class="campo_mask mandatory">
				<input type="text" name="descrizionelunga" class="scrittura" size="50"/>
			</div>
		</div>
    </div>
    <div class="riga">
        <div class="rigo_mask">
			<div class="testo_mask">regime</div>
			<div class="campo_mask mandatory">
				<select id="regime" class="scrittura" name="regime">                
				<?php
					$conn = db_connect();
					print('<option value="">Selezionare un regime</option>'."\n");
					$query="SELECT dbo.regime.regime, dbo.normativa.normativa, dbo.regime.idregime FROM dbo.regime INNER JOIN dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa WHERE (dbo.regime.stato = 1) AND (dbo.regime.cancella = 'n')";
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$idregime = $row['idregime'];
						$regime=$row['regime']." - ".$row['normativa'];											
						print('<option value="'.$idregime.'"');     							
						print ('>'.pulisci_lettura($regime).'</option>'."\n");
					}	
				?>               
                </select>
			</div>
		</div>

	</div>	
	</div>
	<div class="titolo_pag"><h1>Elenco Tariffe</h1></div>	
			
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
<td width="10%" align="left">&nbsp;</td>
<td width="20%" align="center">codice</td>
<td width="20%" align="center">tariffa</td>
<td width="20%" align="center">decorrenza</td>
<td width="20%" align="center">fino al</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
<?

$query="select * from tariffe where (idtipologia='$id')";
$rs = mssql_query($query, $conn);

	
$i=1;
$numerofield=100;
while($row1 = mssql_fetch_assoc($rs)){

		$idtariffa= $row1['idtariffa'];
		$codice= trim($row1['codice']);
		$tariffa= $row1['tariffa'];
		$dal= $row1['dal'];	
		$al= $row1['al'];	
		$class="riga_sottolineata_diversa";
?>	
	
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<td align="center">&nbsp;</td>
    <td align="center" class="nodrag nodrop"><input id="cod<?=$i?>" name="cod<?=$i?>" value="<?=$codice?>" readonly /></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" name="etichetta<?=$i?>" value="<?=$tariffa?>" readonly /></td>
	<td align="center" class="nodrag nodrop"><input class="campo_data" id="dal<?=$i?>" name="dal<?=$i?>" value="<?=formatta_data($dal)?>" readonly /></td>	
	<td align="center" class="nodrag nodrop"><input class="campo_data" id="al<?=$i?>" name="al<?=$i?>" value="<?=formatta_data($al)?>" readonly /></td>	
	<td>&nbsp;</td>
    <!--<input type="hidden" id="el<?=$i?>" name="elimina<?=$i?>" value="" />
	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');document.getElementById('el'+<?=$i?>).value='si';"><img src="images/remove.png" /></a></td>-->
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
    <td align="center"><span class="id_notizia_cat"></span><input id="cod<?=$i?>" name="cod<?=$i?>" /></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" name="etichetta<?=$i?>" /></td>
	<td align="center" class="nodrag nodrop"><input class="campo_data" id="dal<?=$i?>" name="dal<?=$i?>" /></td>	
	<td align="center" class="nodrag nodrop"><input class="campo_data" id="al<?=$i?>" name="al<?=$i?>" /></td>	
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
    <script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>
		
		
</div></div>
<?
//footer_new();

}

function edit($id)
{

	$conn = db_connect();

	$query = "SELECT top 1 dbo.tipologia.descrizionelunga, dbo.tipologia.descrizione, dbo.tipologia.idregime, dbo.tipologia.codice, dbo.regime.regime, dbo.tipologia.stato, dbo.tipologia.cancella, dbo.normativa.normativa, dbo.tipologia.idtipologia FROM dbo.tipologia INNER JOIN dbo.regime ON dbo.tipologia.idregime = dbo.regime.idregime INNER JOIN dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa WHERE (idtipologia=$id) order by idtipologia desc";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$idtipologia=$row['idtipologia'];
		$descrizione=pulisci_lettura($row['descrizione']);
		$descrizionelunga=pulisci_lettura($row['descrizionelunga']);
		$codice=pulisci_lettura($row['codice']);
		$idregime=$row['idregime'];	
	}

	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_tipologie.php');" href="#">elenco tipologie</a></div>
			 <div class="elem_dispari"><a href="#">modifica tipologia</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_tipologie_POST.php" id="myForm">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?php echo $idtipologia ?>" />

	<div class="titolo_pag"><h1>Modifica Tipologia</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">Descrizione</div>
			<div class="campo_mask mandatory">
				<input type="text" name="descrizione" class="scrittura" size="50" value="<?=$descrizione?>"/>
			</div>
		</div>
        <div class="rigo_mask">
			<div class="testo_mask">Codice</div>
			<div class="campo_mask mandatory">
				<input type="text" name="codice" class="scrittura" size="50" value="<?=$codice?>"/>
			</div>
		</div>
    </div> 
    <div class="riga">
    	<div class="rigo_mask">
			<div class="testo_mask">Descrizione lunga</div>
			<div class="campo_mask mandatory">
				<input type="text" name="descrizionelunga" class="scrittura" size="50" value="<?=$descrizionelunga?>"/>
			</div>
		</div>
    </div>
    <div class="riga">
        <div class="rigo_mask">
			<div class="testo_mask">regime</div>
			<div class="campo_mask mandatory">            
				<select id="regime" class="scrittura" name="regime">                
				<?php
					$conn = db_connect();
					print('<option value="">Selezionare un regime</option>'."\n");
					$query="SELECT dbo.regime.regime, dbo.normativa.normativa, dbo.regime.idregime FROM dbo.regime INNER JOIN dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa WHERE (dbo.regime.stato = 1) AND (dbo.regime.cancella = 'n')";
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$idregime_t = $row['idregime'];
						$regime_r=$row['regime']." - ".$row['normativa'];											
						print('<option value="'.$idregime_t.'"');
							if($idregime==$row['idregime']) echo(" selected=\"selected\"");     							
						print ('>'.pulisci_lettura($regime_r).'</option>'."\n");
					}	
				?>               
                </select>
			</div>
		</div>

	</div>	
	</div>
	<div class="titolo_pag"><h1>Elenco Tariffe</h1></div>	
			
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
<td width="10%" align="left">&nbsp;</td>
<td width="20%" align="center">codice</td>
<td width="20%" align="center">tariffa</td>
<td width="20%" align="center">decorrenza</td>
<td width="20%" align="center">fino al</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
<?

$query="select * from tariffe where (idtipologia='$id')";
$rs = mssql_query($query, $conn);
	
$i=1;
$numerofield=100;
while($row1 = mssql_fetch_assoc($rs)){

		$idtariffa= $row1['idtariffa'];
		$codice= trim($row1['codice']);
		$tariffa= $row1['tariffa'];
		$dal= $row1['dal'];	
		$al= $row1['al'];	
		$class="riga_sottolineata_diversa";
?>	
	
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<input type="hidden" id="idcampo<?=$i?>" name="idcampo<?=$i?>" value="<?=$tariffa?>" />
	<td align="center">&nbsp;</td>
    <td align="center" class="nodrag nodrop"><input id="cod<?=$i?>" name="cod<?=$i?>" value="<?=$codice?>" /></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" name="etichetta<?=$i?>" value="<?=$tariffa?>" /></td>
	<td align="center" class="nodrag nodrop"><input class="campo_data" id="dal<?=$i?>" name="dal<?=$i?>" value="<?=formatta_data($dal)?>" /></td>	
	<td align="center" class="nodrag nodrop"><input class="campo_data" id="al<?=$i?>" name="al<?=$i?>" value="<?=formatta_data($al)?>"/></td>	
	<td>&nbsp;</td>
    <input type="hidden" id="el<?=$i?>" name="elimina<?=$i?>" value="" />
	<td><a OnClick="javascript:document.getElementById('el<?=$i?>').value='si';DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a></td>
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
    <td align="center"><span class="id_notizia_cat"></span><input id="cod<?=$i?>" name="cod<?=$i?>" /></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" name="etichetta<?=$i?>" /></td>
	<td align="center" class="nodrag nodrop"><input class="campo_data" id="dal<?=$i?>" name="dal<?=$i?>" /></td>	
	<td align="center" class="nodrag nodrop"><input class="campo_data" id="al<?=$i?>" name="al<?=$i?>" /></td>	
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
    <script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>
		
		
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

