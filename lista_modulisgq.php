<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 46;
$tablename = 'utenti';
$mod_type=2;
include_once('include/function_page.php');

function show_list()
{
global $mod_type;
//echo($mod_type);
//exit();
$conn = db_connect();


$query = "SELECT * from re_distinct_moduli where tipo=$mod_type order by idmodulo desc";	
$rs1 = mssql_query($query, $conn);

if(!$rs1) error_message(mssql_error());

$conta=mssql_num_rows($rs1);

//pagina_new();
?>
<script type="text/javascript">
var popupStatus = 0;

function conf_cancella(idmodulo,id){
if (confirm('sei sicuro di voler cancellare il modulo selezionato?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "ajax_moduli_sgq.php?id_modulo="+idmodulo,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved:"+$.trim(msg)+"1" );
			 if($.trim(msg)==""){
				 $.ajax({
					   type: "POST",
					   url: "lista_modulisgq_POST.php?action=del_modulo&id="+id,
					   data: "",
					   success: function(msg){
						 //alert( "Data Saved: " + msg );
						 reload_moduli();
						}
					 });
			 }else{
				alert("Impossibile eliminare il modulo selezionato essendo associato ad un'area");
				return false;	
			 }
			 
			}
		 });
	  }
	else return false;
}

function reload_moduli(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_modulisgq.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}

//loading popup with jQuery magic!
function loadPopup(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('carica_preview.php?&do=&id='+id, function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});					
		popupStatus = 1;
	}
}


//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});		
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popupdati2").height();
	var popupWidth = $("#popupdati2").width();
	//centering
	$("#popupdati2").css({
		"position": "fixed",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}

function prev(id){	
	//centering with css
	loadPopup(id);
	
	//load popup


}


//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!
	$(".preview").click(function(){
		//centering with css
		//centerPopup();
		//load popup
		//loadPopup();
	});
					
	//CLOSING POPUP
	//Click the x event!
	$(".popupdatiClose").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});
});

</script>		
<html>
<div id="popupdati2"></div>
<div id="backgroundPopup"></div>
	
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione moduli</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_modulisgq.php');" href="#">elenco moduli SQG </a></div>
          
        </div>

	<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_modulisgq.php?do=add');">aggiungi modulo SGQ</a></div>
		</div>
	</div>
<?
	if($conta==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Moduli SGQ</h1>
	</div>
	<div class="info">Non esistono moduli SGQ.</div>	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th width="5%">stato</th>
	<th>codice sgq</th> 
	<th>vers.</th> 
	<th>nome</th> 
    <th>allegato</th> 
	<td width="5%">ant.</td>
	<td width="5%">segn.</td>
	<td width="5%">mod.</td>
	<td width="5%">elim.</td>	
</tr> 
</thead> 
<tbody> 

<?
while($row1 = mssql_fetch_assoc($rs1))   
{
	
$idmodulo=$row1['idmodulo'];

$query = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc";	
//echo($query);
//exit();
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());

while($row = mssql_fetch_assoc($rs))
{       
		$id=$row['id'];		
        $idmodulo['idmodulo'];
		$nome=pulisci_lettura(trim($row['nome']));
		$descrizione=pulisci_lettura(trim($row['descrizione']));
		$codice=pulisci_lettura(trim($row['codice']));
		$versione=trim($row['versione']);
		$doc=trim($row['modello_word']);
		$io=strpos($doc,".");
		if($row['disuso']){
			$stato="stato_blu.png";
			$alt_s="in disuso";}
			else{
			$stato="stato_ok.png";
			$alt_s="attivo";}
		?>
		<tr>
		<td align="center" width="10%"><img src="images/<?=$stato?>" title="<?=$alt_s?>"/></td> 	
		 <td><?=$codice?></td> 
		 <td><?=$versione?></td>
		 <td><?=$nome?></td>
		<? if($io!=""){?>	
		 <td><a href="view_file.php?idfile=<?=$id?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>&call=1" ><?=$doc?></a></td>
		 <?}else{?>
		 <td>&nbsp;</td>
		 <?}?>
		 <td><div id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev('<?=$id?>');" title="anteprima modulo"><img src="images/view.png" /></a></div></td>
		<td><a id="<?=$id?>" href="stampa_modulo.php?idmoduloversione=<?=$id?>" title="elenco segnalibri"><img src="images/book_open.png" /></a></td>
		<td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_modulisgq.php?do=edit&id=<?=$id?>');" href="#" title="modufica modulo"><img src="images/gear.png" /></a></td>
		<td><a id="<?=$id?>" onclick="conf_cancella('<?=$idmodulo?>','<?=$id?>');" href="#"><img src="images/remove.png" /></a></td>
		</tr>
		<?
}
mssql_free_result($rs);

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
  <script>
	function mostra_combo(val,ind){		
	if (val==4){
		document.getElementById('div_combo'+ind).style.display='block';
		}else{		
		document.getElementById('div_combo'+ind).style.display='none';
		}
	
	if (val==9){
		document.getElementById('div_combo_ram'+ind).style.display='block';
		}else{		
		document.getElementById('div_combo_ram'+ind).style.display='none';
		}
	}
	</script>

<?
//footer_new();

}

function add()
{
	global $mod_type;

?>
<script>inizializza();</script>	
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
           <div class="elem_pari"><a onclick="javascript:load_content('div','lista_modulisgq.php');" href="#">elenco moduli</a></div>
			<div class="elem_dispari"><a href="#">aggiungi modulo</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_modulisgq_POST.php" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="action" value="create" />

	
	<div class="stampa" style="float:right"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
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
                <div class="testo_mask">stampa continua</div>
                <div class="campo_mask mandatory">
                    <select name="stampa_continua">
                    <option value="1">Si</option>
                    <option value="0" selected>No</option>
                    </select>
                </div>
            </div>
            
            <div class="rigo_mask">
                <div class="testo_mask">disponibile in elenco moduli</div>
                <div class="campo_mask mandatory">
                    <select name="lista_attesa">
                    <option value="1" >Si</option>
                    <option value="0" selected >No</option>
                    </select>
                </div>
            </div>
        </div>
		<div class="riga">
			<div class="rigo_mask">
                    <div class="testo_mask">
                    <span class="need">codice sgq</span></div>
                    <div class=" campo_mask">
                        <input type="text" class="scrittura" name="codice" size="50" maxlength="255" >
                    </div>
                </div>
                
                <!--<div class="rigo_mask">
                    <div class="testo_mask">replica</div>
                    <div class="campo_mask ">
                        <select name="replica">
                        <option value="0">unico per cartella</option>
						<option value="1" selected>multiplo per cartella</option>   
<option value="2" selected>unico per impegnativa</option> 
<option value="3" selected>multiplo per impegnativa</option> 						
                        </select>
                    </div>
                </div>-->
			<div class="rigo_mask">
                <div class="testo_mask">modulo in disuso</div>
                <div class="campo_mask mandatory">
                    <select name="disuso">
                    <option value="1">Si</option>
                    <option value="0" selected>No</option>
                    </select>
                </div>
            </div>
			</div>
        <div class="riga">
            <div class="rigo_mask rigo_big">
                <div class="testo_mask">intestazione modulo</div>
                <div class="campo_mask">
                    <input type="text" class="scrittura h_small" name="intestazione" size="50" maxlength="255" >
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
        <div class="riga">
        <div class="rigo_mask">
                <div class="testo_mask">modello word</div>
                <div class="campo_mask nomandatory controllo_file personalizzato #.doc.#">
                    <input type="file" name="word" id="word" />
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
	$(".solo_lettere").validation({ 
					type: "alpha",
					add:"_"	
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
<td width="15%" align="center">segnalibro</td>

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
	<td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" /></td>
	<td align="center">
	
		<select id="tipo<?=$i?>" name="tipo<?=$i?>" onchange="mostra_combo(document.getElementById('tipo<?=$i?>').value,<?=$i?>);">
			<option value="1" <?if ($tipo==1) echo("selected");?>>testo</option>
			<option value="2" <?if ($tipo==2) echo("selected");?>>memo</option>
			<option value="3" <?if ($tipo==3) echo("selected");?>>data</option>
			<option value="4" <?if ($tipo==4) echo("selected");?>>combo</option>	
			<option value="9" <?if ($tipo==9) echo("selected");?>>combo ramificata</option>	
			<option value="5" <?if ($tipo==5) echo("selected");?>>etichetta</option>
			<option value="6" <?if ($tipo==6) echo("selected");?>>data osservazione</option>	
			<option value="7" <?if ($tipo==7) echo("selected");?>>allegato</option>
			<option value="8" <?if ($tipo==8) echo("selected");?>>firma_operatore</option>		
		</select>
    <div id="div_combo<?=$i?>" style="display:none;">
        
        <select id="tipo_combo<?=$i?>" name="tipo_combo<?=$i?>" >
				<? $conn = db_connect();
					print('<option value="">Selezionare una combo</option>'."\n");
					$query="SELECT id, nome, stato, cancella FROM dbo.combo WHERE (stato = 1) AND (cancella = 'n')  and (tipo=$mod_type)";
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$id_combo= $row['id'];
						$id_nome = $row['nome'];											
						print('<option value="'.$id_combo.'"');     							
						print ('>'.$id_nome.'</option>'."\n");
					}	
				?>               
		</select>
		
	</div>
	
	<div id="div_combo_ram<?=$i?>" style="display:none;">
        
        <select id="tipo_combo_ram<?=$i?>" name="tipo_combo_ram<?=$i?>" >
				<? $conn = db_connect();
					print('<option value="">Selezionare una combo ramificata padre</option>'."\n");
					$query="SELECT id, nome, stato, cancella FROM dbo.combo_ramificate WHERE (stato = 1) AND (cancella = 'n')  and (tipo=$mod_type) and (idcombopadre is null or idcombopadre=0)";
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$id_combo= $row['id'];
						$id_nome = $row['nome'];											
						print('<option value="'.$id_combo.'"');     							
						print ('>'.$id_nome.'</option>'."\n");
					}	
				?>               
		</select>
		
	</div>
	
	</td>
	<td align="center"><input class="solo_lettere" id="segnalibro<?=$i?>" name="segnalibro<?=$i?>" value="" /></td>
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
	<option value="" selected >nessuna</option>
	<option value="integer" <?if ($classe=='integer') echo("selected");?>>numero</option>
	<option value="float" <?if ($classe=='float') echo("selected");?>>numero decimale</option>
	<option value="email" <?if ($classe=='email') echo("selected");?>>email</option>
	<option value="data_all" <?if ($classe=='data_all') echo("selected");?>>data</option>
	<option value="data_passata" <?if ($classe=='data_passata') echo("selected");?>>data passata (<=oggi)</option>
	<option value="data_futura" <?if ($classe=='data_futura') echo("selected");?>>data futura (>oggi)</option>
		<option value="controllo_file generica" <?if ($classe=='controllo_file generica') echo("selected");?>>controllo file</option>
	
	</select>
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





function edit($id){
	global $mod_type;

	$conn = db_connect();

	$query = "SELECT * from moduli WHERE id=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$id=$row['id'];
		$nome =pulisci_lettura(trim($row['nome']));
		$descrizione =pulisci_lettura(trim($row['descrizione']));
		$idmodulo=$row['idmodulo'];
		$versione=trim($row['versione']);
		$stampa_continua=trim($row['stampa_continua']);
		$lista_attesa=trim($row['lista_attesa']);
		$word=trim($row['modello_word']);
		$codice=pulisci_lettura(trim($row['codice']));
		//$replica=trim($row['replica']);
		$disuso=trim($row['disuso']);
		$intestazione=pulisci_lettura(trim($row['intestazione']));		
	}

	// rimuove i caratteri di escape
	//$nome = stripslashes($nome);
	//$descrizione = stripslashes($descrizione);

	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
   <script>
	function mostra_combo(val,ind){		
	if (val==4){
		document.getElementById('div_combo'+ind).style.display='block';
		}else{		
		document.getElementById('div_combo'+ind).style.display='none';
		}
	
	if (val==9){
		document.getElementById('div_combo_ram'+ind).style.display='block';
		}else{		
		document.getElementById('div_combo_ram'+ind).style.display='none';
		}
	}
	</script>
	<!-- pop up more info -->



	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
         <div class="elem_pari"><a onclick="javascript:load_content('div','lista_modulisgq.php');" href="#">elenco moduli</a></div>
			 <div class="elem_dispari"><a href="#">modifica modulo</a></div>
          
        </div>


<form method="post" name="form0" action="lista_modulisgq_POST.php">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?=$id?>" />
<input type="hidden" name="cambia_mod" value="no" id="cambia_mod"/>
<input type="hidden" name="nome_mod" value="<?=$word?>" />
	
	 <div class="stampa" style="float:right"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
	<div class="titolo_pag"><h1>Modulo</h1></div>
	
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
                    <div class="testo_mask">stampa continua</div>
                    <div class="campo_mask mandatory">
                        <select name="stampa_continua">
                        <option value="1"<?if ($stampa_continua==1) echo ("selected")?>>Si</option>
                        <option value="0"<?if ($stampa_continua==0) echo ("selected")?>>No</option>
                        </select>
                    </div>
                </div>
                
                <div class="rigo_mask">
                    <div class="testo_mask">disponibile in elenco moduli</div>
                    <div class="campo_mask mandatory">
                        <select name="lista_attesa">
                        <option value="1"<?if ($lista_attesa==1) echo ("selected")?>>Si</option>
                        <option value="0"<?if ($lista_lista==1) echo ("selected")?>>No</option>
                        </select>
                    </div>
                </div>
			</div>           
			<div class="riga">
			<div class="rigo_mask">
                    <div class="testo_mask">
                    <span class="need">codice sgq</span></div>
                    <div class="campo_mask">
                        <input type="text" class="scrittura" name="codice" size="50" maxlength="255" value="<?=$codice?>">
                    </div>
                </div>
                
                <!--<div class="rigo_mask">
                    <div class="testo_mask">replica</div>
                    <div class="campo_mask ">
                        <select name="replica">
                        <option value="0"<?if ($replica==0) echo ("selected")?>>unico per cartella</option>
						<option value="1"<?if ($replica==1) echo ("selected")?>>multiplo per cartella</option>  
                        <option value="2"<?if ($replica==2) echo ("selected")?>>unico per impegnativa</option>
						<option value="3"<?if ($replica==3) echo ("selected")?>>multiplo per impegnativa</option>  
						
                        </select>
                    </div>
                </div>-->
				<div class="rigo_mask">
                <div class="testo_mask">modulo in disuso</div>
                <div class="campo_mask mandatory">
                    <select name="disuso">
                    <option value="1" <?if ($disuso==1) echo ("selected")?>>Si</option>
                    <option value="0" <?if ($disuso==0) echo ("selected")?>>No</option>
                    </select>
                </div>
            </div>
			</div>
			<div class="riga">
				<div class="rigo_mask rigo_big">
					<div class="testo_mask">intestazione modulo</div>
					<div class="campo_mask">
						<input type="text" class="scrittura h_small" name="intestazione" size="50" maxlength="255" value="<?=$intestazione?>" >
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
             <div class="riga">
			
                <div class="rigo_mask">
                        <div class="testo_mask">modello word <a onclick="document.getElementById('cambia_mod').value='si';document.getElementById('file_box').style.display='block';document.getElementById('file_name').style.display='none';" class="cambia_valore">cambia modello</a></div>
                        <div id="file_name" style="display:block;">
						 <? if(strpos($row['modello_word'],".")!=""){?>
						<a href="view_file.php?idfile=<?=$id?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>&call=1" ><?=$word?></a>
						<?}?>
						</div>	
                        <div id="file_box" class="campo_mask nomandatory controllo_file personalizzato#.doc.#condition,cambia_mod,si#" style="display:none">
                            <input type="file" name="word" id="word" />
                        </div>
                    </div>
					
       </div> 		
	</div>


<div class="titolo_pag"><div class="comandi"><h1>Campi del modulo</h1></div>
</div>	
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
		$(".solo_lettere").validation({ 
					type: "alpha",
					add:"_"	
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
<td width="15%" align="center">segnalibro</td>

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

		$idcampo= trim($row1['idcampo']);
		$etichetta= pulisci_lettura(trim($row1['etichetta']));
		$segnalibro= pulisci_lettura(trim($row1['segnalibro']));
		$tipo = trim($row1['tipo']);
		$editabile = trim($row1['editabile']);
		$obbligatorio = trim($row1['obbligatorio']);
		$classe=trim($row1['classe']);
		$class="riga_sottolineata_diversa";
?>	

	<!--<tr id="<?=$i?>" class="<?=$class?>"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('<?=$i?>','su');">su</a> - <a OnClick="javascript:DinamicDiv('<?=$i?>','giu');">giu</a></span></td>-->
	<tr id="<?=$i?>" class="<?=$class?>"><td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center">
	<input type="hidden" name="idcampo<?=$i?>" value="<?=$idcampo?>" />
		<input id="et<?=$i?>" name="etichetta<?=$i?>" value="<?=$etichetta?>" />
	</td>
	
	<td align="center">
	
		<select id="tipo<?=$i?>" name="tipo<?=$i?>" onchange="mostra_combo(document.getElementById('tipo<?=$i?>').value,<?=$i?>);">
			<option value="1" <? if ($tipo==1) echo("selected");?>>testo</option>
			<option value="2" <? if ($tipo==2) echo("selected");?>>memo</option>
			<option value="3" <? if ($tipo==3) echo("selected");?>>data</option>
			<option value="4" <? if ($tipo==4) echo("selected");?>>combo</option>
			<option value="9" <? if ($tipo==9) echo("selected");?>>combo ramificata</option>				
			<option value="5" <? if ($tipo==5) echo("selected");?>>etichetta</option>
			<option value="6" <? if ($tipo==6) echo("selected");?>>data osservazione</option>	
			<option value="7" <?if ($tipo==7) echo("selected");?>>allegato</option>
			<option value="8" <?if ($tipo==8) echo("selected");?>>firma_operatore</option>	
		</select>
        <?
		$query="select * from moduli_combo where ((idmoduloversione=$id) and (idcampo=$idcampo))";		
		$rs_1 = mssql_query($query, $conn);
		if($row_1 = mssql_fetch_assoc($rs_1)) {?>        
        	<div id="div_combo<?=$i?>" style="display:block;">
            <? $idcombo=$row_1['idcombo']; 
			} else {?>
			<div id="div_combo<?=$i?>" style="display:none;">
         <? } ?>
        
        <select id="tipo_combo<?=$i?>" name="tipo_combo<?=$i?>" >
				<? $conn = db_connect();
					print('<option value="">Selezionare una combo</option>'."\n");
					$query="SELECT id, nome, stato, cancella FROM dbo.combo WHERE (stato = 1) AND (cancella = 'n')  and (tipo=$mod_type)";
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$id_combo= $row['id'];
						$id_nome = pulisci_lettura($row['nome']);											
						print('<option value="'.$id_combo.'"');
						     if($idcombo==$id_combo) echo(" selected=\"selected\""); 							
						print ('>'.$id_nome.'</option>'."\n");
					}	
				?>               
		</select>
	</div>    

  <?
		$query="select * from moduli_combo_ramificate where ((idmoduloversione=$id) and (idcampo=$idcampo))";		
		$rs_1 = mssql_query($query, $conn);
		if($row_1 = mssql_fetch_assoc($rs_1)) {?>        
        	<div id="div_combo_ram<?=$i?>" style="display:block;">
            <? $idcombo=$row_1['idcombo']; 
			} else {?>
			<div id="div_combo_ram<?=$i?>" style="display:none;">
         <? } ?>
        
        <select id="tipo_combo_ram<?=$i?>" name="tipo_combo_ram<?=$i?>" >
				<? $conn = db_connect();
					print('<option value="">Selezionare una combo ramificata padre</option>'."\n");
					$query="SELECT id, nome, stato, cancella FROM dbo.combo_ramificate WHERE (stato = 1) AND (cancella = 'n')  and (tipo=$mod_type) and (idcombopadre is null or idcombopadre=0)";
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$id_combo= $row['id'];
						$id_nome = pulisci_lettura($row['nome']);											
						print('<option value="'.$id_combo.'"');
						     if($idcombo==$id_combo) echo(" selected=\"selected\""); 							
						print ('>'.$id_nome.'</option>'."\n");
					}	
				?>               
		</select>		
	</div>
	</td>
	<td align="center"><input class="solo_lettere" id="segnalibro<?=$i?>" name="segnalibro<?=$i?>" value="<?=$segnalibro?>" /></td>
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
	<option value="integer" <?if ($classe=='integer') echo("selected");?>>numero</option>
	<option value="float" <?if ($classe=='float') echo("selected");?>>numero decimale</option>
	<option value="email" <?if ($classe=='email') echo("selected");?>>email</option>
	<option value="data_all" <?if ($classe=='data_all') echo("selected");?>>data</option>
	<option value="data_passata" <?if ($classe=='data_passata') echo("selected");?>>data passata (<=oggi)</option>
	<option value="data_futura" <?if ($classe=='data_futura') echo("selected");?>>data futura (>oggi)</option>
		<option value="controllo_file generica" <?if ($classe=='controllo_file generica') echo("selected");?>>controllo file</option>
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
	<td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" /></td>
	<td align="center">
	
		<select id="tipo<?=$i?>" name="tipo<?=$i?>" onchange="mostra_combo(document.getElementById('tipo<?=$i?>').value,<?=$i?>);">
			<option value="1" <?if ($tipo==1) echo("selected");?>>testo</option>
			<option value="2" <?if ($tipo==2) echo("selected");?>>memo</option>
			<option value="3" <?if ($tipo==3) echo("selected");?>>data</option>
			<option value="4" <?if ($tipo==4) echo("selected");?>>combo</option>
			<option value="9" <?if ($tipo==9) echo("selected");?>>combo ramificata</option>
			<option value="5" <?if ($tipo==5) echo("selected");?>>etichetta</option>
			<option value="6" <?if ($tipo==6) echo("selected");?>>data osservazione</option>	
			<option value="7" <?if ($tipo==7) echo("selected");?>>allegato</option>
			<option value="8" <?if ($tipo==8) echo("selected");?>>firma_operatore</option>	
		</select>        
	<div id="div_combo<?=$i?>" style="display:none;">
        
        <select id="tipo_combo<?=$i?>" name="tipo_combo<?=$i?>" >
				<? $conn = db_connect();
					print('<option value="">Selezionare una combo</option>'."\n");
					$query="SELECT id, nome, stato, cancella FROM dbo.combo WHERE (stato = 1) AND (cancella = 'n')  and (tipo=$mod_type)";
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$id_combo= $row['id'];
						$id_nome = pulisci_lettura($row['nome']);											
						print('<option value="'.$id_combo.'"');     							
						print ('>'.$id_nome.'</option>'."\n");
					}	
				?>               
		</select>
		
	</div>
	
	<div id="div_combo_ram<?=$i?>" style="display:none;">
        
        <select id="tipo_combo_ram<?=$i?>" name="tipo_combo_ram<?=$i?>" >
				<? $conn = db_connect();
					print('<option value="">Selezionare una combo ramificata padre</option>'."\n");
					//$query="SELECT id, nome, stato, cancella FROM dbo.combo_ramificata WHERE (stato = 1) AND (cancella = 'n')  and (tipo=$mod_type) and (idcombopadre is null or idcombopadre=0)";
					$query="SELECT id, nome, stato, cancella FROM dbo.combo_ramificate WHERE (stato = 1) AND (cancella = 'n')  and (tipo=$mod_type) and (idcombopadre is null or idcombopadre=0)";
					
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$id_combo= $row['id'];
						$id_nome = pulisci_lettura($row['nome']);											
						print('<option value="'.$id_combo.'"');     							
						print ('>'.$id_nome.'</option>'."\n");
					}	
				?>               
		</select>
	</div>
    </td>
	<td align="center"><input class="solo_lettere" id="segnalibro<?=$i?>" name="segnalibro<?=$i?>" value="" /></td>
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
	<option value="" selected >nessuna</option>
	<option value="integer" <?if ($classe=='integer') echo("selected");?>>numero</option>
	<option value="float" <?if ($classe=='float') echo("selected");?>>numero decimale</option>
	<option value="email" <?if ($classe=='email') echo("selected");?>>email</option>
	<option value="data_all" <?if ($classe=='data_all') echo("selected");?>>data</option>
	<option value="data_passata" <?if ($classe=='data_passata') echo("selected");?>>data passata (<=oggi)</option>
	<option value="data_futura" <?if ($classe=='data_futura') echo("selected");?>>data futura (>oggi)</option>
	<option value="controllo_file generica" <?if ($classe=='controllo_file generica') echo("selected");?>>controllo file</option>
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

