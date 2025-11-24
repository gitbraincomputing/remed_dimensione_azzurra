<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 55;
$tablename = 'utenti';
$mod_type=3;
include_once('include/function_page.php');



function show_list()
{
global $mod_type;

$conn = db_connect();


$query="select idtest,nome,codice from test_clinici WHERE stato=1 order by nome asc";
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
					   url: "lista_test_clinici_POST.php?action=del_test&id_test="+id,
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
	pagina_da_caricare="lista_test_clinici.php";
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
		$("#popupdati2").load('carica_preview_test_clinico.php?&do=&id='+id, function(){ 
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
        	<div class="elem0"><a href="#">gestione test clinici</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_test_clinici.php');" href="#">elenco test clinici</a></div>
          
        </div>

	<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_test_clinici.php?do=add');">aggiungi test clinico</a></div>
		</div>
	</div>
<?
	if($conta==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Test Clinici</h1>
	</div>
	<div class="info">Non esistono test clinici creati.</div>	
	
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
		$nome=pulisci_lettura($row['nome']);
		$codice=pulisci_lettura($row['codice']);
		?>
		<tr> 
		 <td><?=$codice?></td> 
		 <td><?=$nome?></td>
		 <td><div id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev('<?=$idtest?>');"><img src="images/view.png" /></a></div></td>		
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_test_clinici.php?do=edit&id=<?=$idtest?>');" href="#"><img src="images/gear.png" /></a></td>
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
           <div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli_test_da_somministrare.php');" href="#">elenco test</a></div>
			<div class="elem_dispari"><a href="#">aggiungi test clinico</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_test_clinici_POST.php" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="action" value="create" />

	
	<div class="stampa" style="float:right"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
	<div class="blocco_centralcat">
		<div class="riga">
			<div class="riga">
            <div class="rigo_mask rigo_big">
                <div class="testo_mask">nome del test clinico</div>
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
                <div class="testo_mask">limite normativo di default </div>
                <div class="campo_mask mandatory">
                    <input type="text" name="limite_normativo_default" id="limite_normativo_default" onkeyup="normalizza1(this.value);"/> (*) verr&agrave; utilizzato per le fasce d'et&agrave; alla data del test per cui non viene specificato un limite normativo.
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
function normalizza1(elem){	
	if(elem.substr(elem.length - 1)==',') $("#limite_normativo_default").val(elem.substr(0,(elem.length - 1))+'.');
}

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
$numerofield=200;
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
		<td colspan="8" class="rigo_items"><table class="table_padre_<?=$i?>" style="width: 100%">
		<tr class="riga_tab nodrag nodrop">
			<td width="5%" align="center">ordine</td>
			<td width="40%" align="center">prova del test</td>
			<td width="40%" align="center">istruzioni operative</td>
			<td width="10%" align="center">tipo valutazione (positiva se)</td>
			<td width="10%">somma punteggio</td>
			<td width="2%">&nbsp;</td>
			<td width="5%">elimina</td>
		</tr>
		<tr>
		<td align="center"><span class="id_notizia_cat"><strong>gruppo <?=$i?></strong></span></td>
		<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" /></td>
		<td align="center" class="nodrag nodrop"><textarea id="istr_oper<?=$i?>"  name="istr_oper<?=$i?>"></textarea></td>
		<td align="center">
		
			<select id="valuta<?=$i?>" name="valuta<?=$i?>" onchange="mostra_combo(document.getElementById('valuta<?=$i?>').value,<?=$i?>);">
		
				<option value="1">maggiore del limite</option>
				<option value="2" selected>minore del limite</option>
				<option value="4" selected>minore uguale del limite</option>
				<option value="5" selected>maggiore uguale del limite</option>
					
			</select>
		</td>
		<td align="center">
			<select id="somma<?=$i?>" name="somma<?=$i?>">
		
				<option value="1">si</option>
				<option value="0" selected>no</option>
					
			</select>
		</td>
		<td width="2%">&nbsp;</td>
		
		<td>
		<a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a>
		</td>
		</tr>

		</table>
		<table class="table_figli_<?=$i?>" style="width:100%">
		
		</table>
		
		<table class="table_limiti_<?=$i?>" style="width:100%">
		
		</table>
		
		<table>
			<tr>
				<td colspan="1">
					<span class="aggiungi_figlio" rel="<?=$i?>" value="1"><img src="images/add.png"/> aggiungi risposta</span>
				</td>
				<td colspan="1">
					<span class="aggiungi_limite" rel="<?=$i?>" value="1"><img src="images/add.png"/> aggiungi limite normativo</span>
				</td>
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>
		
		</td>
	</tr>
<?
$i++;
}

?>
<script>
function normalizza(elem,id){	
	if(elem.substr(elem.length - 1)==',') $("#valore"+id).val(elem.substr(0,(elem.length - 1))+'.');
}
function cambia_range(elem,idriga)
	{
		var ch_range=$(elem).find(":selected").attr("rel");
		//alert(idriga);
		$("#punteggio_wrap"+idriga).attr("class", "mandatory "+ch_range);
	}
	
$(document).ready(function(){
	
	$(".aggiungi_figlio").click(function(){
		class_da_ass = (parseInt($(this).attr("rel")) * 1000) + parseInt($(this).attr("value"));
		$(this).attr("value", parseInt($(this).attr("value"))+1); 
		$(".table_figli_"+$(this).attr("rel")).append('<table class="rigo_child figlio'+class_da_ass+'" style="width:100%"><tr>'
			+'<td align="center" colspan="1" width="20%">punteggio</td>'
			+'<td align="center" colspan="1" width="30%">risposta</td>'
			+'<td align="center" colspan="6" width="50%">&nbsp;</td>'
			+'</tr>'+
			'<tr>'
			+'<td align="center" colspan="1" width="20%"><div class="mandatory"><input type="text" name="valore'+class_da_ass+'" id="valore'+class_da_ass+'" onkeyup="normalizza(this.value,'+class_da_ass+');"/></div></td>'
			+'<td align="center" colspan="1" width="30%"><div class="mandatory"><input type="text" name="descrizione'+class_da_ass+'" id="descrizione'+class_da_ass+'" /></div></td>'
			+'<td align="center" colspan="1" width="25%"><span class="rimuovi_figlio" rel="'+class_da_ass+'"><img src="images/close.png"/>rimuovi questa riga</span></td>'			
			+'<td align="center" colspan="5" width="0%">&nbsp;</td>'
			+'</tr></table>');
			
		$(".rimuovi_figlio").click(function(){
			$(".figlio"+$(this).attr("rel")).html("");
			$(".figlio"+$(this).attr("rel")).removeClass(".figlio"+$(this).attr("rel"));
		});
		
	});
	
	$(".aggiungi_limite").click(function(e){
		class_da_ass = (parseInt($(this).attr("rel"))*1000) + parseInt($(this).attr("value"));
		$(this).attr("value", parseInt($(this).attr("value"))+1); 
		$(".table_limiti_"+$(this).attr("rel")).append('<tr class="rigo_child2 limite'+class_da_ass+'">'
			+'<td align="center" colspan="1" width="20%">&nbsp;</td>'
			+'<td align="center" colspan="1" width="30%">range di et&agrave;</td>'
			+'<td align="center" colspan="1" width="30%">limite normativo</td>'
			+'<td align="center" colspan="5" width="20%">&nbsp;</td>'
			+'</tr>'+
			'<tr class="rigo_child2 limite'+class_da_ass+'">'
			+'<td align="center" colspan="1" width="20%">&nbsp;</td>'
			+'<td align="center" colspan="1" width="30%" class="limite_normativo'+class_da_ass+'"></td>'
			+'<td align="center" colspan="1" width="30%"><div id="punteggio_wrap'+class_da_ass+'"><input type="punteggio" name="punteggio'+class_da_ass+'" id="punteggio'+class_da_ass+'" onkeyup="normalizza(this.value,'+class_da_ass+');"/></div></td>'
			+'<td align="center" colspan="5" width="20%"> <span class="rimuovi_limite" rel="'+class_da_ass+'"><img src="images/close.png"/> rimuovi questa riga</span></td>'
			+'</tr>');
		popolaselect('limite_normativo'+class_da_ass,class_da_ass);
		$(".rimuovi_limite").click(function(){
			$(".limite"+$(this).attr("rel")).html("");
			$(".limite"+$(this).attr("rel")).removeClass(".limite"+$(this).attr("rel"));
		});
		//stopPropagation();
	});
	
	
	
});
function popolaselect(selectdapopolare,idriga){
/*alert(document.getElementById("range").value);
		$("."+selectdapopolare).html('<select name="'+selectdapopolare+'" id="'+selectdapopolare+'">'
		+'<option value="1">da 1 anno a 1 anno</option>'
		+'<option value="2">da 2 anno a 2 anno</option>'
		+'<option value="3">da 3 anno a 3 anno</option>'
		+'<option value="4">da 4 anno a 4 anno</option>'		
		+'</select>');*/
	
		$.ajax({
   type: "POST",
     url: "lista_test_clinici_get_range.php",
     data: "name="+selectdapopolare+"&idrange="+document.getElementById("range").value,
   success: function(data){
   
     $("."+selectdapopolare).html(data);
	 cambia_range(document.getElementById(selectdapopolare),idriga);
   }
 });

		
		
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
//footer_new();

}



function edit($id){
	global $mod_type;

	$conn = db_connect();

	$query = "SELECT * from test_clinici WHERE idtest=$id";
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

	// rimuove i caratteri di escape
	$nome = stripslashes($nome);
	$descrizione = stripslashes($descrizione);

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
	}
	</script>
	<!-- pop up more info -->


	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
         <div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli.php');" href="#">elenco moduli</a></div>
			 <div class="elem_dispari"><a href="#">modifica modulo</a></div>          
        </div>

<form method="post" name="form0" action="lista_test_clinici_POST.php">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?=$id?>" />
<input type="hidden" name="cambia_mod" value="no" id="cambia_mod"/>
<input type="hidden" name="nome_mod" value="<?=$word?>" />
	
	<div class="stampa" style="float:right"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
	<div class="titolo_pag"><h1>Modulo</h1></div>
	<div class="blocco_centralcat">	
	
		<div class="riga">
				<div class="riga">
	            <div class="rigo_mask rigo_big">
	                <div class="testo_mask">nome del test clinico</div>
	                <div class="mandatory campo_mask">
	                    <input type="text" class="scrittura h_small" name="nome" size="50" maxlength="255" value="<?=$nome?>" >
	                </div>
	            </div>
			</div>
	    </div>
		<div class="riga">
			<div class="rigo_mask">
                    <div class="testo_mask">
                    <span class="need">codice del test</span></div>
                    <div class=" campo_mask">
                        <input type="text" class="scrittura" name="codice" size="50" maxlength="255" value="<?=$codice?>" >
                    </div>
                </div>
				
				<div class="rigo_mask">
                    <div class="testo_mask">tipologia test</div>
                    <div class="campo_mask ">
                        <select id="tipo_test" name="tipo_test">
						<option value="1" <? if ($tipo==1) echo("selected")?>>oggettivo</option>
						<option value="2" <? if ($tipo==2) echo("selected")?>>soggettivo</option>
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
		$sel="";
		if ($idrange==$idrange_get)
		$sel="selected=selected";
		?>
<option <?=$sel?> value="<?=$idrange?>"><?=$nome_range?></option>
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
						<option value="1" <? if ($disponibile_in_lista_moduli==1) echo("selected=selected");?>>si</option>
						<option value="0" <? if ($disponibile_in_lista_moduli==0) echo("selected=selected");?>>no</option>
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
                <div class="testo_mask">limite normativo di default </div>
                <div class="campo_mask mandatory">
                    <input type="text" name="limite_normativo_default" id="limite_normativo_default" value="<?=$limite_default?>" onkeyup="normalizza1(this.value);"/> (*) verr&agrave; utilizzato per le fasce d'et&agrave; alla data del test per cui non viene specificato un limite normativo.
                </div>
				
            </div>
		</div>	

<div class="titolo_pag"><h1>Campi del modulo</h1></div>	
			
		<div class="blocco_centralcat">	
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

$style='';
$class='';
$dis="";


$query = "SELECT * from test_clinici_prove where idtest=$id";
$rs_prove = mssql_query($query, $conn);
$numero_prove=mssql_num_rows($rs_prove);
$numerofield=200;
$$numerofield=$numero_prove;
$i=1;
while($row_prove= mssql_fetch_assoc($rs_prove)){

$descrizione=pulisci_lettura($row_prove['descrizione']);
$istruzioni_operative=pulisci_lettura($row_prove['istruzioni_operative']);
$tipo_valutazione=$row_prove['tipo_valutazione'];
$somma=$row_prove['somma'];
$idprova=$row_prove['idprova'];

	if ($i>$numero_prove)
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
		<input type="hidden" name="elimina<?=$i?>" id="elimina<?=$i?>" value="retet">
		<td colspan="8" class="rigo_items">
		<table class="table_padre_<?=$i?>" style="width: 100%">
			
			<tr class="riga_tab nodrag nodrop">
				<td width="5%" align="center">ordine</td>
				<td width="40%" align="center">prova del test</td>
				<td width="40%" align="center">istruzioni operative</td>
				<td width="10%" align="center">tipo valutazione (positiva se)</td>
				<td width="10%">somma punteggio</td>
				<td width="2%">&nbsp;</td>
				<td width="5%">elimina</td>
			</tr>
			
			<tr>
				<td align="center"><span class="id_notizia_cat"><strong>gruppo <?=$i?></strong></span></td>
				<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="<?=$descrizione?>" style="width:400px" /></td>
				<td align="center" class="nodrag nodrop"><textarea id="istr_oper<?=$i?>"  name="istr_oper<?=$i?>"><?=$istruzioni_operative?></textarea></td>
				<td align="center">
				
					<select id="valuta<?=$i?>" name="valuta<?=$i?>" onchange="mostra_combo(document.getElementById('valuta<?=$i?>').value,<?=$i?>);">
				
						<option value="1" <? if ($tipo_valutazione==1) echo ("selected=selected")?>>maggiore del limite</option>
						<option value="2" <? if ($tipo_valutazione==2) echo ("selected=selected")?>>minore del limite</option>
						<option value="4" <? if ($tipo_valutazione==4) echo ("selected=selected")?>>minore uguale del limite</option>
						<option value="5" <? if ($tipo_valutazione==5) echo ("selected=selected")?>>maggiore uguale del limite</option>
							
					</select>
				
				</td>
				<td align="center">
					<select id="somma<?=$i?>" name="somma<?=$i?>">
						<option value="1" <? if ($somma==1) echo ("selected=selected")?>>si</option>
						<option value="0" <? if ($somma==0) echo ("selected=selected")?> >no</option>
					</select>
				</td>
				<td width="2%">&nbsp;</td>
				
				<td>
				<a OnClick="javascript:$('#elimina<?=$i?>').val('1');DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a>
				</td>
			</tr>

		</table> <!-- fine table padre  -->
		
		
		
		<?
		
		$query = "SELECT * from test_clinici_items where idprova=$idprova";
		$rsitem = mssql_query($query, $conn);
		$numero_items=mssql_num_rows($rsitem );
		
		$x=1;
		
		//while($row_items= mssql_fetch_assoc($rsitem))
		while($row_items= mssql_fetch_assoc($rsitem)){
		
			if ($x==1){
				?>
				<table class="table_figli_<?=$i?>" style="width:100%">
				</table>
				
				<!--<div style="display:none;"  class="dacopiare_<?=$i?>">-->
				<?
			}
			
			$iditem=$row_items['iditem'];
			$it_etichetta=pulisci_lettura($row_items['etichetta']);
			$it_valore=pulisci_lettura($row_items['valore']);
			$class_da_ass=($i*1000)+$x;
			
			?>
				<table class="rigo_child figlio<?=$class_da_ass?>" style="width:100%">
				
					<tr>
						<td align="center" colspan="1" width="20%">punteggio</td>
						<td align="center" colspan="1" width="30%">risposta</td>
						<td align="center" colspan="6" width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td align="center" colspan="1" width="20%"><div class="mandatory"><input type="text" name="valore<?=$class_da_ass?>" id="valore<?=$class_da_ass?>" value="<?=$it_valore?>" onkeyup="normalizza(this.value,'<?=$class_da_ass?>');"/></div></td>
						<td align="center" colspan="1" width="30%"><div class="mandatory"><input type="text" name="descrizione<?=$class_da_ass?>" id="descrizione<?=$class_da_ass?>" value="<?=$it_etichetta?>" style="width:600px"/></div></td>
						<td align="center" colspan="1" width="25%"><span class="rimuovi_figlio" rel="<?=$class_da_ass?>"><img src="images/close.png"/>rimuovi questa riga</span></td>
						<td align="center" colspan="5" width="0%">&nbsp;</td>
					</tr>
				</table>

			<?
			/*
			if ($numero_items==$x)	{			
				?>
				</div>
				<?
			}*/
			$x++;
		}
			$query = "SELECT * from test_clinici_limiti where iditem=$iditem";
			$rslim = mssql_query($query, $conn);
			$numero_limiti=mssql_num_rows($rslim );
			
			$y=1;
			if ($numero_limiti>0){
				?>
				<table class="table_limiti_<?=$i?>" style="width:100%">
				<?
			}
			while($row_lim= mssql_fetch_assoc($rslim)){
			
				$ideta=pulisci_lettura($row_lim['ideta']);
				$valore=pulisci_lettura($row_lim['valore']);
				$class_da_ass=($i*1000)+$y;
				?>
			
				<tr class="rigo_child2 limite<?=$class_da_ass?>">
					<td align="center" colspan="1" width="20%">&nbsp;</td>
					<td align="center" colspan="1" width="30%">range di et&agrave;</td>
					<td align="center" colspan="1" width="30%">limite normativo</td>
					<td align="center" colspan="5" width="20%">&nbsp;</td>'
					</tr>
					<tr class="rigo_child2 limite<?=$class_da_ass?>">
					<td align="center" colspan="1" width="20%">&nbsp;</td>
					<td align="center" colspan="1" width="30%" class="limite_normativo<?=$class_da_ass?>">
					
					<?
					
						$idriga=$class_da_ass;
					

						$query4="select * from test_clinici_range_details where idrange=$idrange_get";
						
						$rs4 = mssql_query($query4, $conn);
						?>
						<select onchange="javascript:cambia_range(this,'<?=$idriga?>');" name="limite_normativo<?=$class_da_ass?>" id="limite_normativo<?=$class_da_ass?>">
						<?
						$z=0;
						$classe_punteggio="";
						while($row4 = mssql_fetch_assoc($rs4)){   
							//$class_da_ass=($i*100)+$z;
							$idrange_detail=$row4['idrange_detail'];
							$descrizione=$row4['descrizione'];
							$valore_da=$row4['valore_da'];
							$valore_a=$row4['valore_a'];
							$tipo_dato=$row4['tipo_dato'];

							$class="";
							if ($tipo_dato==1)
							$class="integer";
							elseif($tipo_dato==2)
							$class="data_all";
							elseif($tipo_dato==3)
							$class="";
							elseif($tipo_dato==4)
							$class="float";
							
							$classe_punteggio="mandatory ".$class;
							
							$sel="";
							if ($ideta==$idrange_detail) $sel="selected=selected";
								
							?>
							<option rel="<?=$class?>" <?=$sel?> value="<?=$idrange_detail?>"><?=$descrizione." ".$valore_da." - ".$valore_a?></option>
							<?					
						}
						?>
						</select>

					</td>
					<td align="center" colspan="1" width="30%"><div class="<?=$classe_punteggio?>" id="punteggio_wrap<?=$class_da_ass?>"><input type="punteggio" name="punteggio<?=$class_da_ass?>" id="punteggio<?=$class_da_ass?>" value="<?=$valore?>" onkeyup="normalizza(this.value,'<?=$class_da_ass?>');"/></div></td>
					<td align="center" colspan="5" width="20%"> <span class="rimuovi_limite" rel="<?=$class_da_ass?>"><img src="images/close.png"/> rimuovi questa riga</span></td>
					</tr>
				
				<?
				$y++;
			}
			
			if($numero_limiti>0)
			{
			?>
			</table>
			<?
			}
		
		if($numero_limiti==0)
			{
			?>
			<table class="table_limiti_<?=$i?>" style="width:100%">
			
			</table>
			<?
		}
		?>
		
		<table>
			<tr>
				<td colspan="1">
					<span class="aggiungi_figlio" rel="<?=$i?>" value="<?=($numero_items+1)?>"><img src="images/add.png"/> aggiungi risposta</span>
				</td>
				<td colspan="1">
					<span class="aggiungi_limite" rel="<?=$i?>" value="<?=($numero_limiti+1)?>"><img src="images/add.png"/> aggiungi limite normativo</span>
				</td>
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>
		
		</td>
	</tr>
<?
$i++;
}
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
		<td colspan="8" class="rigo_items"><table class="table_padre_<?=$i?>" style="width: 100%">
		<tr class="riga_tab nodrag nodrop">
			<td width="5%" align="center">ordine</td>
			<td width="40%" align="center">prova del test</td>
			<td width="40%" align="center">istruzioni operative</td>
			<td width="10%" align="center">tipo valutazione (positiva se)</td>
			<td width="10%">somma punteggio</td>
			<td width="2%">&nbsp;</td>
			<td width="5%">elimina</td>
		</tr>
		<tr>
		<td align="center"><span class="id_notizia_cat"><strong>gruppo <?=$i?></strong></span></td>
		<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" /></td>
		<td align="center" class="nodrag nodrop"><textarea id="istr_oper<?=$i?>"  name="istr_oper<?=$i?>"></textarea></td>
		<td align="center">
		
			<select id="valuta<?=$i?>" name="valuta<?=$i?>" onchange="mostra_combo(document.getElementById('valuta<?=$i?>').value,<?=$i?>);">
		
				<option value="1">maggiore del limite</option>
				<option value="2" selected>minore del limite</option>
				<option value="4" >minore uguale del limite</option>
				<option value="5" >maggiore uguale del limite</option>
					
			</select>
		</td>
		<td align="center">
			<select id="somma<?=$i?>" name="somma<?=$i?>">
		
				<option value="1">si</option>
				<option value="0" selected>no</option>
					
			</select>
		</td>
		<td width="2%">&nbsp;</td>
		
		<td>
		<a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a>
		</td>
		</tr>

		</table>
		<table class="table_figli_<?=$i?>" style="width:100%">
		
		</table>
		
		<table class="table_limiti_<?=$i?>" style="width:100%">
		cvxvxv
		</table>
		
		<table>
			<tr>
				<td colspan="1">
					<span class="aggiungi_figlio" rel="<?=$i?>" value="1"><img src="images/add.png"/> aggiungi risposta</span>
				</td>
				<td colspan="1">
					<span class="aggiungi_limite" rel="<?=$i?>" value="1"><img src="images/add.png"/> aggiungi limite normativo</span>
				</td>
				<td colspan="6">&nbsp;</td>
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
	

	<script type="text/javascript">
	function normalizza1(elem){	
	if(elem.substr(elem.length - 1)==',') $("#limite_normativo_default").val(elem.substr(0,(elem.length - 1))+'.');
}
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
			
			for (i=1;i<200;i++)
			{
			x=$(".dacopiare_"+i).html();
			
			
			if (x)
			$(".table_figli_"+i).append(x);						
			else
			i=1000;
			
			
			if (x)
			$(".table_limiti_"+i).append(x);						
			else
			i=1000;
				
			}
			
			
			for (i=1;i<200;i++)
			{
			x=$(".dacopiare_"+i).html("");
			
			
			if (!x)
			i=1000;
		
			
			}
			
									
			});
			</script>
	
<script>
function normalizza(elem,id){	
	if(elem.substr(elem.length - 1)==',') $("#valore"+id).val(elem.substr(0,(elem.length - 1))+'.');
}

function cambia_range(elem,idriga)
	{
		var ch_range=$(elem).find(":selected").attr("rel");
		//alert(idriga);
		$("#punteggio_wrap"+idriga).attr("class", "mandatory "+ch_range);
	}
	
$(document).ready(function(){
	
	$(".rimuovi_figlio").click(function(){
			$(".figlio"+$(this).attr("rel")).html("");
			$(".figlio"+$(this).attr("rel")).removeClass(".figlio"+$(this).attr("rel"));
		});
		
	$(".aggiungi_figlio").click(function(){
		class_da_ass = (parseInt($(this).attr("rel")) * 1000) + parseInt($(this).attr("value"));
		$(this).attr("value", parseInt($(this).attr("value"))+1); 
		$(".table_figli_"+$(this).attr("rel")).append('<table class="rigo_child figlio'+class_da_ass+'" style="width:100%"><tr>'
			+'<td align="center" colspan="1" width="20%">punteggio</td>'
			+'<td align="center" colspan="1" width="30%">risposta</td>'
			+'<td align="center" colspan="6" width="50%">&nbsp;</td>'
			+'</tr>'+
			'<tr>'
			+'<td align="center" colspan="1" width="20%"><div class="mandatory"><input type="text" name="valore'+class_da_ass+'" id="valore'+class_da_ass+'" onkeyup="normalizza(this.value,'+class_da_ass+');"/></div></td>'
			+'<td align="center" colspan="1" width="30%"><div class="mandatory"><input type="text" name="descrizione'+class_da_ass+'" id="descrizione'+class_da_ass+'" style="width:600px" /></div></td>'
			+'<td align="center" colspan="1" width="25%"><span class="rimuovi_figlio" rel="'+class_da_ass+'"><img src="images/close.png"/>rimuovi questa riga</span></td>'
			+'<td align="center" colspan="5" width="0%">&nbsp;</td>'
			+'</tr></table>');
			
		$(".rimuovi_figlio").click(function(){
			$(".figlio"+$(this).attr("rel")).html("");
			$(".figlio"+$(this).attr("rel")).removeClass(".figlio"+$(this).attr("rel"));
		});
		
	});
	
	$(".aggiungi_limite").click(function(e){
		class_da_ass = (parseInt($(this).attr("rel"))*1000) + parseInt($(this).attr("value"));
		$(this).attr("value", parseInt($(this).attr("value"))+1); 
		$(".table_limiti_"+$(this).attr("rel")).append('<tr class="rigo_child2 limite'+class_da_ass+'">'
			+'<td align="center" colspan="1" width="20%">&nbsp;</td>'
			+'<td align="center" colspan="1" width="30%">range di et&agrave;</td>'
			+'<td align="center" colspan="1" width="30%">limite normativo</td>'
			+'<td align="center" colspan="5" width="20%">&nbsp;</td>'
			+'</tr>'+
			'<tr class="rigo_child2 limite'+class_da_ass+'">'
			+'<td align="center" colspan="1" width="20%">&nbsp;</td>'
			+'<td align="center" colspan="1" width="30%" class="limite_normativo'+class_da_ass+'"></td>'
			+'<td align="center" colspan="1" width="30%"><div id="punteggio_wrap'+class_da_ass+'"><input type="punteggio" name="punteggio'+class_da_ass+'" id="punteggio'+class_da_ass+'" onkeyup="normalizza(this.value,'+class_da_ass+');"/></div></td>'
			+'<td align="center" colspan="5" width="20%"> <span class="rimuovi_limite" rel="'+class_da_ass+'"><img src="images/close.png"/> rimuovi questa riga</span></td>'
			+'</tr>');
		popolaselect('limite_normativo'+class_da_ass,class_da_ass);
		$(".rimuovi_limite").click(function(){
			$(".limite"+$(this).attr("rel")).html("");
			$(".limite"+$(this).attr("rel")).removeClass(".limite"+$(this).attr("rel"));
		});
		//stopPropagation();
	});
	
	
	
});
function popolaselect(selectdapopolare,idriga){

		$.ajax({
   type: "POST",
     url: "lista_test_clinici_get_range.php",
     data: "name="+selectdapopolare+"&idrange="+document.getElementById("range").value,
   success: function(data){
   
     $("."+selectdapopolare).html(data);
	 cambia_range(document.getElementById(selectdapopolare),idriga);
   }
 });

	}
</script>	
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

			case "del_istanza":
				del_istanza_testclinico();
			break;
			case "del_istanza_ram":
				del_istanza_testclinico_ram();
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

