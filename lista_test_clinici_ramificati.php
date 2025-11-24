<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 61;
$tablename = 'utenti';
$mod_type=3;
include_once('include/function_page.php');



function show_list()
{
global $mod_type;

$conn = db_connect();


$query="select idtest,nome,codice from test_clinici where test_ramificato=1 and stato=1 order by nome asc";
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

//pagina_new();
?>
<script type="text/javascript">
function conf_cancella(id){
if (confirm('sei sicuro di voler cancellare il test clinico corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "ajax_test_clinici.php?id_test="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 if(msg==""){
				 $.ajax({
					   type: "POST",
					   url: "lista_test_clinici_ramificati_POST.php?action=del_test&id_test="+id,
					   data: "",
					   success: function(msg){
						 //alert( "Data Saved: " + msg );
						 reload_test();
						}
					 });
			 }else{
				alert('Impossibile cancellare il test clinco in quanto presente all\'interno di una pianifcazione cartella');
				return false;	
			 }
			 
			}
		 });
	  }
	else return false;
}
function reload_test(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_test_clinici_ramificati.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	 });
}

var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('carica_preview_test_clinico_ramificato.php?&do=preview_test_clinico_ramificato&id='+id, function(){ 
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
        	<div class="elem0"><a href="#">gestione test clinici ramificati</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_test_clinici_ramificati.php');" href="#">elenco test clinici ramificati</a></div>
          
        </div>

	<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_test_clinici_ramificati.php?do=add');">aggiungi test clinico ramificato</a></div>
		</div>
	</div>
<?
	if($conta==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Test Clinici ramificati</h1>
	</div>
	<div class="info">Non esistono test clinici creati ramificati.</div>	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th width="15%">codice test</th> 
	<th>nome</th> 
	<td width="5%">anteprima</td>
	<td width="5%">modifica</td>
   <td width="5%">cancella</td>	    
</tr> 
</thead> 
<tbody> 

<?


while($row = mssql_fetch_assoc($rs))
{   
		$idtest=$row['idtest'];		
		$nome=trim($row['nome']);
		$codice=trim($row['codice']);
		
		?>
		<tr> 
		 <td><?=$codice?></td> 
		 <td><?=$nome?></td>
		 <td><div id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev('<?=$idtest?>');"><img src="images/view.png" /></a></div></td>		
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_test_clinici_ramificati.php?do=edit&id=<?=$idtest?>');" href="#"><img src="images/gear.png" /></a></td>
		<td><a onclick="conf_cancella('<?=$idtest?>');" href="#"><img src="images/remove.png" /></a></td>	
		</tr>
		<?
}
mssql_free_result($rs);
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
  <script>
	function mostra_combo(val,ind){		
	if (val==4){
		document.getElementById('div_combo'+ind).style.display='block';
		}else{		
		document.getElementById('div_combo'+ind).style.display='none';
		}
	}
	</script>

<?
//footer_new();

}

function add()
{
	global $mod_type;
	$conn=db_connect();

?>
<script>inizializza();</script>	
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
           <div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli_test_da_somministrare.php');" href="#">elenco test ramificati</a></div>
			<div class="elem_dispari"><a href="#">aggiungi test clinico ramificato</a></div>
        </div>


<form  method="post" name="form0" action="lista_test_clinici_ramificati_POST.php" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="action" value="create" />

	
	<div class="stampa" style="float:right"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
	<div class="blocco_centralcat">
		<div class="riga">
			<div class="riga">
            <div class="rigo_mask rigo_big">
                <div class="testo_mask">nome del test clinico ramificato</div>
                <div class="mandatory campo_mask">
                    <input type="text" class="scrittura h_small" name="nome" size="50" maxlength="255" >
                </div>
            </div>
		</div>
           
         
        </div>
		<div class="riga">
			<div class="rigo_mask">
                    <div class="testo_mask">
                    <span class="need">codice del test</span></div>
                    <div class=" campo_mask">
                        <input type="text" class="scrittura" name="codice" size="50" maxlength="255" >
                    </div>
                </div>
				
				<div class="rigo_mask">
                    <div class="testo_mask">tipologia test</div>
                    <div class="campo_mask ">
                        <select id="tipo_test" name="tipo_test">
						<option value="1">oggettivo</option>
						<option value="2">soggettivo</option>
						</select>
					</div>
				</div	
                
                <div class="rigo_mask">
                    <div class="testo_mask">utilizza il range</div>
                    <div class="campo_mask ">
                        <select id="range" name="range">
                        <?
						$query = "SELECT * from test_clinici_range order by Nome asc";
						$rs = mssql_query($query, $conn);
						if(!$rs) error_message(mssql_error());

	while($row = mssql_fetch_assoc($rs)){

		$idrange=$row['idrange'];
		$nome_range =pulisci_lettura($row['Nome']);
		?>
<option value="<?=$idrange?>"><?=$nome_range?></option>
<?		
	}

	// rimuove i caratteri di escape
						?>
						
												
                        </select>
                    </div>
                </div>
			</div>
			
			<div class="riga">
			
				
				<div class="rigo_mask">
                    <div class="testo_mask">disponibile in lista moduli</div>
                    <div class="campo_mask mandatory ">
                        <select id="dispo_lista" name="dispo_lista">
						<option value="">seleziona</option>
						<option value="1">si</option>
						<option value="0">no</option>
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
		<div class="riga">
		<div class="rigo_mask">
                <div class="testo_mask">limite normativo di default</div>
                <div class="campo_mask mandatory">
                    <input type="text" name="limite_normativo_default" id="limite_normativo_default" />
                </div>
            </div>
		</div>	   
	</div>

<div class="titolo_pag"><h1>Test clinici associati</h1></div>	
			
		<div class="blocco_centralcat">	
			
<script type="text/javascript">
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
		<td colspan="8" class="rigo_items"><table class="table_padre_<?=$i?>" style="width: 100%">
		<tr class="riga_tab nodrag nodrop">
			<td width="5%" align="center">ordine</td>
			<td width="1%" align="center">&nbsp;</td>
			<td width="40%" align="center">seleziona da</td>
			<td width="40%" align="center">seleziona il test</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">elimina</td>
		</tr>
		<tr>
		<td align="center"><span class="id_notizia_cat"><strong>test <?=$i?></strong></span></td>
		<td width="1px" align="center" class="nodrag nodrop">
		<input type="hidden" id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" />
		</td>
		
		<td align="center">
		
			<select width="auto;" id="test_ramificato<?=$i?>" name="test_ramificato<?=$i?>" onchange="mostra_test_clinici(this.value,<?=$i?>);">
				<option value="" selected="selected" >seleziona il tipo di test</option>
				<option value="0">test semplice</option>
				<option value="1">test ramificato</option>
					
			</select>
		
		</td>
		<td align="center">
		<div id="test_clinico_scelto_div<?=$i?>">
			<input type="hidden" name="test_clinico_scelto_sel<?=$i?>" value="" >
		</div>
		</td>
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
<script>

function mostra_test_clinici(val,idriga){

if (val!='')
{
$.ajax({
   type: "POST",
     url: "lista_test_clinici_get_test.php",
     data: "test_ramificato="+val+"&idriga="+idriga,
   success: function(data){
   //alert(data);
     $("#test_clinico_scelto_div"+idriga).html(data);
	 
   }
 });
 }
 else
 alert("selezionare il campo seleziona da ");
}
</script>
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
}


function edit($id)
{
	$conn = db_connect();

	$query = "SELECT * from test_clinici WHERE idtest=$id";
	echo($query);
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$id=$row['idtest'];
		$nome =pulisci_lettura($row['nome']);
		$codice=pulisci_lettura($row['codice']);
		$tipo=$row['tipo'];
		$idrange_get=$row['idrange'];
		$disponibile_in_lista_moduli=$row['disponibile_in_lista_moduli'];
		$descrizione=pulisci_lettura($row['descrizione']);
		$limite_default=$row['limite_default'];
		
	}

	mssql_free_result($rs);
	?>
<script>inizializza();</script>	
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
           <div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli_test_da_somministrare.php');" href="#">elenco test ramificati</a></div>
			<div class="elem_dispari"><a href="#">aggiungi test clinico ramificato</a></div>
        </div>


<form  method="post" name="form0" action="lista_test_clinici_ramificati_POST.php" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="id" value="<?=$id?>" />


	
	<div class="stampa" style="float:right"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
	<div class="blocco_centralcat">
		<div class="riga">
			<div class="riga">
            <div class="rigo_mask rigo_big">
                <div class="testo_mask">nome del test clinico ramificato</div>
                <div class="mandatory campo_mask">
                    <input type="text" class="scrittura h_small" name="nome" size="50" maxlength="255" value="<?=$nome?>">
                </div>
            </div>
		</div>
           
         
        </div>
		<div class="riga">
			<div class="rigo_mask">
                    <div class="testo_mask">
                    <span class="need">codice del test</span></div>
                    <div class=" campo_mask">
                        <input type="text" class="scrittura" name="codice" size="50" maxlength="255" value="<?=$codice?>">
                    </div>
                </div>
				
				<div class="rigo_mask">
                    <div class="testo_mask">tipologia test</div>
                    <div class="campo_mask ">
                        <select id="tipo_test" name="tipo_test">
						<option value="1" <?if($tipo==1) echo("selected");?> >oggettivo</option>
						<option value="2" <?if($tipo==2) echo("selected");?> >soggettivo</option>
						</select>
					</div>
				</div	
                
                <div class="rigo_mask">
                    <div class="testo_mask">utilizza il range</div>
                    <div class="campo_mask ">
                    <select id="range" name="range">
                        <?
						$query = "SELECT * from test_clinici_range order by Nome asc";
						$rs = mssql_query($query, $conn);
						if(!$rs) error_message(mssql_error());

						while($row = mssql_fetch_assoc($rs)){
							$selected="";
							$idrange=$row['idrange'];
							$nome_range=pulisci_lettura($row['Nome']);
							if($idrange==$idrange_get) $selected="selected";
							?>
						<option value="<?=$idrange?>" <?=$selected?>><?=$nome_range?></option>
						<?		
							}?>			
                    </select>
                    </div>
                </div>
			</div>
			
			<div class="riga">
			
				
				<div class="rigo_mask">
                    <div class="testo_mask">disponibile in lista moduli</div>
                    <div class="campo_mask mandatory ">
                        <select id="dispo_lista" name="dispo_lista">
						<option value="">seleziona</option>
						<option value="1" <?if($disponibile_in_lista_moduli==1) echo("selected");?> >si</option>
						<option value="0" <?if($disponibile_in_lista_moduli==0) echo("selected");?> >no</option>
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
		<div class="riga">
		<div class="rigo_mask">
                <div class="testo_mask">limite normativo di default</div>
                <div class="campo_mask mandatory">
                    <input type="text" name="limite_normativo_default" id="limite_normativo_default" value="<?=$limite_default?>"/>
                </div>
            </div>
		</div>	
	</div>

<div class="titolo_pag"><h1>Test clinci associati</h1></div>	
			
		<div class="blocco_centralcat">	

<script type="text/javascript">
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
<?
$i=1;
$numerofield=100;
$style='';
$class='';
$dis="";

$query="SELECT * FROM test_clinici_ramificati WHERE idtest_padre=$id";
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());

while($row = mssql_fetch_assoc($rs)){
	$test_figlio=$row['idtest_figlio'];
	$test_ramificato=$row['test_ramificato'];

	$nodrag="nodrag=false";
	$style='';
	$class="riga_sottolineata_diversa";
	$dis="";
	?>
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
		<td colspan="8" class="rigo_items"><table class="table_padre_<?=$i?>" style="width: 100%">
		<tr class="riga_tab nodrag nodrop">
			<td width="5%" align="center">ordine</td>
			<td width="1%" align="center">&nbsp;</td>
			<td width="40%" align="center">seleziona da</td>
			<td width="40%" align="center">seleziona il test</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">elimina</td>
		</tr>
		<tr>
		<td align="center"><span class="id_notizia_cat"><strong>test <?=$i?></strong></span></td>
		<td width="1px" align="center" class="nodrag nodrop">
		<input type="hidden" id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" />
		</td>
		
		<td align="center">
			<select width="auto;" id="test_ramificato<?=$i?>" name="test_ramificato<?=$i?>" onchange="mostra_test_clinici(this.value,<?=$i?>);">
				<option value="" selected="selected" >seleziona il tipo di test</option>
				<option value="0" <?if($test_ramificato==0) echo("selected");?> >test semplice</option>
				<option value="1" <?if($test_ramificato==1) echo("selected");?> >test ramificato</option>
			</select>
		</td>
		
		<td align="center">
		<div id="test_clinico_scelto_div<?=$i?>">
			<select id="test_clinico_scelto_sel<?=$i?>" name="test_clinico_scelto_sel<?=$i?>">
				<?
				$query1 = "SELECT * from test_clinici WHERE test_ramificato=$test_ramificato and stato=1";				
				$rs1 = mssql_query($query1, $conn);
				if(!$rs1) error_message(mssql_error());

				while($row1 = mssql_fetch_assoc($rs1)){
					$selected="";
					$idtest=$row1['idtest'];
					$nome_test=pulisci_lettura($row1['nome']);
					if($idtest==$test_figlio) $selected="selected";
					?>
				<option value="<?=$idtest?>" <?=$selected?>><?=$nome_test?></option>
				<?		
					}?>			
			</select>
		</div>
		</td>
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
<?
while($i<$numerofield)
{
	
	$style="style=\"display:none\"";
	$class="riga_sottolineata_diversa hidden";
	$dis="disabled";
	?>
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
		<td colspan="8" class="rigo_items"><table class="table_padre_<?=$i?>" style="width: 100%">
		<tr class="riga_tab nodrag nodrop">
			<td width="5%" align="center">ordine</td>
			<td width="1%" align="center">&nbsp;</td>
			<td width="40%" align="center">seleziona da</td>
			<td width="40%" align="center">seleziona il test</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">elimina</td>
		</tr>
		<tr>
		<td align="center"><span class="id_notizia_cat"><strong>test <?=$i?></strong></span></td>
		<td width="1px" align="center" class="nodrag nodrop">
		<input type="hidden" id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" />
		</td>
		
		<td align="center">
			<select width="auto;" id="test_ramificato<?=$i?>" name="test_ramificato<?=$i?>" onchange="mostra_test_clinici(this.value,<?=$i?>);">
				<option value="" selected="selected" >seleziona il tipo di test</option>
				<option value="0">test semplice</option>
				<option value="1">test ramificato</option>
			</select>
		</td>
		<td align="center">
		<div id="test_clinico_scelto_div<?=$i?>">
			<input type="hidden" name="test_clinico_scelto_sel<?=$i?>" value="" >
		</div>
		</td>
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
<script>

function mostra_test_clinici(val,idriga){

if (val!='')
{
$.ajax({
   type: "POST",
     url: "lista_test_clinici_get_test.php",
     data: "test_ramificato="+val+"&idriga="+idriga,
   success: function(data){
   //alert(data);
     $("#test_clinico_scelto_div"+idriga).html(data);
	 
   }
 });
 }
 else
 alert("selezionare il campo seleziona da ");
}
</script>
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

