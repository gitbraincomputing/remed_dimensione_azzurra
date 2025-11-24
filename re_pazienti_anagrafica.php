<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
include_once('include/function_page.php');

define('SEDE_SERV_AMB', 'Servizi ambulatoriali');
define('SEDE_RESIDENZIALE', 'Residenziale');
define('SEDE_NON_GESTITA', 'Struttura non gestita');

//$idutente=$id;

function show_allegati($id)
{

$idutente=$id;
$conn = db_connect();

$query = "SELECT * from utenti_allegati where (idutente=$idutente) and(stato=1) and (cancella='n')";
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);
?>
<script>
$(document).ready(function() {
	
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
function conf_cancella(id_al,paz){
if (confirm('sei sicuro di voler cancellare l\'allegato corrente?')){
	document.getElementById('id_allegato').value=id_al;
	  $.ajax({
		   type: "POST",
		   url: "re_pazienti_anagrafica_POST.php?action=del_files&id_all="+id_al+"&paz="+paz,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 reload_allegati(<?=$idutente?>,'0');
			}
		 });	  
	  }
	else return false;
}
function reload_allegati(idpaziente,idimpegnativa){
	$("#layer_nero2").toggle();
	$('#lista_impegnative').innerHTML="";
	pagina_da_caricare="re_pazienti_anagrafica.php?do=show_allegati&id="+idpaziente;
	$("#lista_impegnative").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}

function add_allegati(idpaziente){
	$("#layer_nero2").toggle();
	$('#lista_impegnative').innerHTML="";
	pagina_da_caricare="re_pazienti_anagrafica.php?do=add_allegati&id="+idpaziente;
	$("#lista_impegnative").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
	
</script>

<div id="lista_impegnative">

<div class="titoloalternativo">
	<h1>Allegati Anagrafici</h1>
	<div class="stampa"><a href="javascript:stampa('fragment-2');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>

</div>

<?
	if($conta==0){
	?>
		
	<div class="info">Nessun allegato trovato.</div>
		
	<?
	exit();}?>
<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_right">
		<a href="#"  onclick="javascript:add_allegati('<?=$idutente?>')" >aggiungi allegato</a>
		</div>
	</div>
	<h1>Elenco allegati</h1>
</div>
<div class="form">
	<form name="form0" action="re_pazienti_anagrafica_POST.php" method="post" id="myForm" >
    <input type="hidden" name="action" value="del_files" />
	<div class="nomandatory"><input type="hidden" name="id_allegato" id="id_allegato" value="" /></div>
    <div class="nomandatory"><input type="hidden" name="id_paziente" id="id_paziente" value="<?=$id?>" /></div>					
<table id="table" class="tablesorter" cellspacing="1">&nbsp; 
<thead> 
<tr> 
    
	<th width="10%" align="center">Tipo file</th>
	<th width="10%" align="center">Data acquisizione</th>	
	<th width="15%" align="center">Operatore</th>	
    <th width="45%" align="left">Descrizione</th> 
    <th width="10%" align="center" class="no_print">Visualizza</th>    
    <th width="10%" align="center" class="no_print">Cancella</th> 
	
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$id_all=$row['id'];
		$descrizione=pulisci_lettura($row['descrizione']);
		$file=$row['file_name'];
		$type=$row['type'];
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
		 
		 <td align="center"><img src="images/icons/<?=$tipo_file?>" alt="<?=$alt_file?>" /><span class="hide"><?=$alt_file?></span></td> 
		 <td><?=$data_ora?></td>
		 <td><?=$nome_op?></td>
		 <td><?=$descrizione?></td> 
		 <td align="center" class="no_print"><a href="view_file.php?idfile=<?=$id_all?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>" ><img src="images/view.png" /></a></td>         
         <td align="center" class="no_print"><!--<input type="image" src="images/remove.png" title="salva" onclick="javascript:document.getElementById('id_allegato').value='<?=$id_all?>';" />-->
		  <img type="submit" src="images/remove.png" alt="elimina"  onclick="conf_cancella('<?=$id_all?>','<?=$idutente?>');"></td> 		  
		</tr> 
		
		<?
}	

?>


</tbody> 
</table> 
</form>
</div>

	
</div>

	


<?

}


function add_allegati($id)
{

$idutente=$id;
$conn = db_connect();

?>
<div id="lista_impegnative">


<div id="cartella_clinica">
<div class="titoloalternativo">
            <h1>Nuovo Allegato</h1>
	         
			<!--<h2>creata il 17/12/2008 da brain</h2>
			<h2>creata il 17/12/2008 da brain</h2>-->
</div>


<div class="titolo_pag"><h1>Aggiungi allegati</h1></div>

<div class="blocco_centralcat">
<div class="form">	
	<form name="form0" action="re_pazienti_anagrafica_POST.php" method="post" id="myForm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add_files" />
	<input type="hidden" name="id" value="<?php echo($id);?>" />			
	
    <div class="riga">
    <input id="debug" type="hidden" value="1" name="debug"/>
	<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">
 	<tbody id="tbody">
    <tr class="riga_tab nodrag nodrop">
		<td width="20%" align="left">&nbsp;</td>
        <td width="30%" align="left">descrizione</td>
		<td width="40%" align="left">file</td>		
		<td width="5%">&nbsp;</td>
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
	<td align="left" class="nodrag nodrop"><div id="div_et<?=$i?>" class="mandatory"><textarea name="et<?=$i?>" id="et<?=$i?>" class="scrittura_campo" <?=$dis?>/></textarea></div></td>
	<td align="left" class="nodrag nodrop"><div id="div_file<?=$i?>" class="mandatory controllo_file generica"><input type="file" name="ulfile<?=$i?>" <?=$dis?> id="ulfile<?=$i?>" /></div></td>	
	<td>&nbsp;</td>
	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a>
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
</div>	</div>	
</div>
<?

}


function edit($id){

	$idutente=$id;
	$_SESSION['id_paziente']=$id;
	$conn = db_connect();

	$query = "SELECT * from re_utenti_anagrafica where idutente=$idutente";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	if($row = mssql_fetch_assoc($rs)){
	
		$codiceutente = $row['CodiceUtente'];
		$cognome =pulisci_lettura($row['Cognome']);
		$nome =pulisci_lettura($row['Nome']);
		$data_nascita = formatta_data($row['DataNascita']);
		$sesso=$row['Sesso'];
		$note_ana=$row['note_ana'];
		$codice_fiscale = $row['CodiceFiscale'];	
		$stato=$row['stato'];
		$stato_nascita=$row['stato_nascita'];
		$comune_nascita = pulisci_lettura($row['luogonascita']);
		$comune_nascita_id = $row['LuogoNascita_id'];
		$cap_nascita_id = $row['cap'];
		$prov_nascita=pulisci_lettura($row['prov']);	
		$reg_nascita=$row['reg'];	
		$reparto=$row['id_reparto'];
	}
	if($prov_nascita==""){
		$query = "SELECT  id, nome FROM  province WHERE (nome ='$comune_nascita')";			
		$rs1 = mssql_query($query, $conn);
		if($row1 = mssql_fetch_assoc($rs1)){
			$prov_nascita=pulisci_lettura($row1['nome']);
		}
	}
		
	$query = "SELECT TOP 1 dbo.utenti_residenze.id as id, dbo.utenti_residenze.decorrenza, dbo.utenti_residenze.indirizzo, dbo.utenti_residenze.telefono, dbo.utenti_residenze.fax,dbo.utenti_residenze.cellulare, dbo.utenti_residenze.email, dbo.utenti_residenze.stato, dbo.utenti_residenze.cancella, dbo.utenti_residenze.IdUtente, dbo.comuni.denominazione, dbo.comuni.cap, dbo.comuni.provincia, dbo.comuni.sigla, dbo.utenti_residenze.comune_id, dbo.comuni.id_comune as id_comune, dbo.province.nome AS nome_prov FROM dbo.utenti_residenze INNER JOIN dbo.comuni ON dbo.utenti_residenze.comune_id = dbo.comuni.id_comune INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.utenti_residenze.stato = 1) AND (dbo.utenti_residenze.cancella = 'n') AND (dbo.utenti_residenze.IdUtente = $idutente) ORDER BY dbo.utenti_residenze.id DESC";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
		$id_residenza=trim($row['id']);
		$res_indirizzo=pulisci_lettura(trim($row['indirizzo']));
		$res_telefono=trim($row['telefono']);
		$res_fax=trim($row['fax']);
		$res_cellulare=trim($row['cellulare']);
		$res_email=trim($row['email']);
		$res_comune=pulisci_lettura(trim($row['denominazione']));
		$res_comune_id=trim($row['id_comune']);
		$res_cap=trim($row['cap']);
		$res_provincia=pulisci_lettura(trim($row['nome_prov']));
		$res_sigla=trim($row['sigla']);
		$decorrenza = formatta_data($row['decorrenza']);
		if($decorrenza=='01/01/1900') $decorrenza="";		
	}
	
	//$query = "SELECT TOP 1 dbo.utenti_medici.id as id, dbo.utenti_medici.decorrenza, dbo.utenti_medici.nome,dbo.utenti_medici.cognome,dbo.utenti_medici.indirizzo, dbo.utenti_medici.telefono, dbo.utenti_medici.fax,dbo.utenti_medici.cellulare, dbo.utenti_medici.email, dbo.utenti_medici.stato, dbo.utenti_medici.cancella, dbo.utenti_medici.IdUtente, dbo.comuni.denominazione, dbo.comuni.cap, dbo.comuni.provincia, dbo.comuni.sigla, dbo.utenti_medici.comune_id, dbo.comuni.id_comune as id_comune, dbo.province.nome AS nome_prov FROM dbo.utenti_medici INNER JOIN dbo.comuni ON dbo.utenti_medici.comune_id = dbo.comuni.id_comune INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.utenti_medici.stato = 1) AND (dbo.utenti_medici.cancella = 'n') AND (dbo.utenti_medici.IdUtente = $idutente) ORDER BY dbo.utenti_medici.id DESC";
	$query = "SELECT TOP 1 * FROM re_pazienti_medico_curante WHERE (IdUtente = $idutente) ORDER BY id DESC";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
		$id_medico=trim($row['id']);
		$med_nome=pulisci_lettura(trim($row['nome']));
		$med_cognome=pulisci_lettura(trim($row['cognome']));
		$med_indirizzo=pulisci_lettura(trim($row['indirizzo']));
		$med_telefono=trim($row['telefono']);
		$med_fax=trim($row['fax']);
		$med_cellulare=trim($row['cellulare']);
		$med_email=trim($row['email']);
		$med_comune=pulisci_lettura(trim($row['denominazione']));
		$med_comune_id=trim($row['id_comune']);
		$med_cap=trim($row['cap']);
		$med_provincia=pulisci_lettura(trim($row['nome_prov']));
		$med_sigla=trim($row['sigla']);
		$med_decorrenza = formatta_data($row['decorrenza']);
		if($med_decorrenza=='01/01/1900') $med_decorrenza="";	
	}
	if($comune_nascita_id==""){?>
		<script type="text/javascript">
		alert("IMPOSSIBILE PROSEGUIRE.\nANAGRAFICA NON COMPILATA INTEGRALMENTE - CONTROLLARE LA PRESENZA DEL LUOGO DI NASCITA.");
		</script><?
	}
?>

<!-- pop up more info -->
<div id="paziente" >
<a name="paziente"></a>
<script type="text/javascript">
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		
		$("#popupdati").html("<div style=\"width:100%; text-align: center; padding: 150px 0 0 0; color: #666; float: left;\">caricamento in corso <br /><br /><img src=\"images/loading-bianco.gif\" /> </div>");
		
		$("#popupdati").load('carica_popup.php?type=res&do=&id=<?=$idutente?>');
		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati").fadeIn("slow");
		
		popupStatus = 1;
	}
}

function loadPopup_t(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		
		$("#popupdati").html("<div style=\"width:100%; text-align: center; padding: 150px 0 0 0; color: #666; float: left;\">caricamento in corso <br /><br /><img src=\"images/loading-bianco.gif\" /> </div>");
		
		$("#popupdati").load('carica_popup.php?type=tut&do=&id=<?=$idutente?>');
		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati").fadeIn("slow");
		
		popupStatus = 1;
	}
}

function loadPopup_m(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		
		$("#popupdati").html("<div style=\"width:100%; text-align: center; padding: 150px 0 0 0; color: #666; float: left;\">caricamento in corso <br /><br /><img src=\"images/loading-bianco.gif\" /> </div>");
		
		$("#popupdati").load('carica_popup.php?type=med&do=&id=<?=$idutente?>');
		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati").fadeIn("slow");
		
		popupStatus = 1;
	}
}

function loadPopup_img(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		
		$("#popupdati3").html("<div style=\"width:100%; text-align: center; padding: 150px 0 0 0; color: #666; float: left;\">caricamento in corso <br /><br /><img src=\"images/loading-bianco.gif\" /> </div>");
		
		$("#popupdati3").load('carica_popup_img.php?type=add&do=&id=<?=$idutente?>');
		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati3").fadeIn("slow");
		
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){		
		$("#backgroundPopup").fadeOut("slow");		
		$("#popupdati").fadeOut("slow");
		$("#popupdati3").fadeOut("slow");
		$('#thumbnail_form').hide();
		$("#desc_foto").hide();
		$(".imgareaselect-border2").hide();
		$(".imgareaselect-border1").hide();
		$(".imgareaselect-outer").hide();	
		$(".imgareaselect-selection").hide();	
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popupdati").height();
	var popupWidth = $("#popupdati").width();
	//centering
	$("#popupdati").css({
		"position": "fixed",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}

//centering popup
function centerPopup3(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popupdati3").height();
	var popupWidth = $("#popupdati3").width();
	//centering
	$("#popupdati3").css({
		"position": "fixed",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}


//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!
	$(".storico").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
	});
	
	$(".storico_t").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup_t();
	});
	
	$(".storico_m").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup_m();
	});
	
	$(".miniatura").click(function(){
		//centering with css
		centerPopup3();
		//load popup
		loadPopup_img();
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

/*function omocodia(value){
	if(value.length==16) {
		//alert(value);
		$.ajax({
	   type: "POST",
	   url: "omocodia.php",
	   data: "codfisc="+value,
	   success: function(msg){
		//alert("g"+trim(msg)+"g");
			
		 if (parseInt(msg)) 
			alert("Attenzione, il Codice Fiscale inserito e' stato gia' attribuito ad altro paziente.");
		//else
			//alert("no");
	   }
	 });
	}
	return value.toUpperCase();
}*/

function nome_comune(value){
		$.ajax({
	   type: "POST",
	   url: "get_comune.php",
	   data: "idcomune="+value,
	   success: function(msg){
		alert(msg);
		return msg;
		//alert("g"+trim(msg)+"g");
			
		 //if (parseInt(msg)) 
		//	alert("Attenzione, il Codice Fiscale inserito e' stato gia' attribuito ad altro paziente.");
		//else
			//alert("no");
	   }
	 });
}
</script>

<!-- script per suggermenti -->
<script type="text/javascript" src="script/jtip.js"></script> 
<script type="text/javascript" src="script/cf.js"></script> 

<div id="popupdati"></div>
<div id="popupdati3"></div>
<div id="backgroundPopup"></div>

<!-- fine pop up more info -->
<div class="titoloalternativo">
            <h1>Anagrafica</h1>
	        <div class="stampa"><a href="javascript:stampa('fragment-2');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
</div>

<form id="myForm" method="post" name="form0" action="re_pazienti_anagrafica_POST.php" >
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="1" />
	<input type="hidden" name="id" value="<?php echo($id);?>" />
    
 

	<div class="titolo_pag"><h1>Dati del Paziente</h1></div>
	
	<div class="blocco_centralcat">
	<div class="riga"> 
	<span class="rigo_mask">
		<div class="testo_mask">codiceutente</div>
		<div class="campo_mask mandatory">
			<input id="c_p" type="text" class="scrittura_campo " name="codiceutente" SIZE="30" maxlenght="30" value="<?php echo($codiceutente);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
	<span class="rigo_mask">
		<div class="testo_mask">cognome</div>
		<div class="campo_mask mandatory">
			<input id="c_p" type="text" class="scrittura_campo solo_lettere" name="cognome" SIZE="30" maxlenght="30" value="<?php echo($cognome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input id="n_p" type="text" class="scrittura_campo solo_lettere" name="nome" SIZE="30" maxlenght="30" value="<?php echo($nome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
        <div class="testo_mask">sesso</div>
        <div class="campo_mask mandatory">
            <input id="1s_p" type="radio" name="sesso" value="1" <?php if($sesso=="1") echo("checked='checked'");?>/> M 
            <input id="2s_p" type="radio" name="sesso" value="2" <?php if($sesso=="2") echo("checked='checked'");?>/> F
        </div>
    </span>
    </div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">data di nascita</div>
		<div class="campo_mask mandatory data_passata">
			<input type="text" class="campo_data scrittura"  name="data_nascita" id="data_nascita" value="<?php echo($data_nascita);?>" />
			<!--	<input type="button" style=" background-image:url(images/calendar.jpg); background-repeat: no-repeat; border: none; background-color: #abc6f3; width:18px; height:18px; margin: 4px 0 0 5px; float: rigth;" value="" onclick="displayCalendar(document.getElementById('data_nascita'),'dd/mm/yyyy',this)">-->
		</div>
	</span>
	
	<div class="rigo_mask">
		<div class="testo_mask">stato di nascita</div>
		<div class="campo_mask mandatory">
			 <select id="stato_nascita" class="scrittura" name="stato_nascita" onChange="javascript:carica_stato(this.value,1,0);">
			<option value="297" selected="selected">ITALIA</option>
			 <?php 
			$conn = db_connect();
			$query = "SELECT id, nome FROM dbo.province WHERE sigla='(EE)' order by nome";
			$rs = mssql_query($query, $conn);
			 while ($row = mssql_fetch_assoc($rs)) {
	
				// setta la variabile di sessione
				$idprov = $row['id'];
				$nomeprov = htmlentities($row['nome']);
									
				print('<option value="'.$idprov.'"');
				if($stato_nascita==$idprov) echo("selected='selected'");
				print ('>'.$nomeprov.'</option>'."\n");					
			}
			mssql_free_result($rs);
			?>
			</select>
		</div>
	</div>
    
        
    <span class="rigo_mask">
		<div class="testo_mask">provincia di nascita <a onclick="carica_province(document.getElementById('prov_nascita').value,1)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask mandatory" id="province">
			<input type="text" class="scrittura readonly" name="prov_nascita" id="prov_nascita" SIZE="30" maxlenght="30" value="<?php echo($prov_nascita);?>" readonly/>
		</div>
	</span>
    </div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">Comune di nascita <a onclick="carica_comuni(document.getElementById('prov_nascita').value,1,document.getElementById('comune_nascita').value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask mandatory" id="comuni">
        <input type="text" class="scrittura readonly" id="cn_p" name="comune_nascita_nome" SIZE="30" maxlenght="30" value="<?php echo($comune_nascita);?>" readonly/>	
        <input type="hidden" name="comune_nascita" id="comune_nascita" value="<?=$comune_nascita_id?>"/> 
		</div>
		
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">Codice Fiscale <a class="cambia_valore calcola_cf">calcola Codice Fiscale</a></div>
		<div class="campo_mask mandatory codice_fiscale_omocodia">
			<input id="cod_fisc" type="text" class="scrittura" name="codice_fiscale" SIZE="30" maxlength="16" value="<?php echo($codice_fiscale);?>" onKeyUp="this.value=this.value.toUpperCase()" />
		</div>
	</span>
	
	<div class="rigo_mask">
		<div class="testo_mask">Reparto</div>
		<div class="campo_mask">
			<select id="reparto_paziente" class="scrittura" name="reparto_paziente" >
				<option value="" selected="selected" >Seleziona un valore</option>
<?php				
			$_sql = "SELECT id, nome, sede
						FROM reparti rp
						WHERE status = 2 AND deleted = 0";
			$_rs = mssql_query($_sql, $conn);
							
			while ($row = mssql_fetch_assoc($_rs)) {
				switch ($row['sede']) {
					case 1:
						$row['sede'] = SEDE_SERV_AMB;
						break;

					case 2:
						$row['sede'] = SEDE_RESIDENZIALE;
						break;

					default:
						$row['sede'] = SEDE_NON_GESTITA;
						break;
				}
				echo '<option value="' . $row['id'] . '" ' . (!empty($reparto) && $reparto == $row['id'] ? 'selected' : '') . ' >' . $row['nome'] . ' - ' . $row['sede'] . '</option>';
			}
			mssql_free_result($_rs);
?>
			</select>
		</div>
	</div>
	
     <input type="hidden" name="stato" value="1" />
     <!--<span class="rigo_mask">
        <div class="testo_mask">stato</div>
        <div class="campo_mask">
                <select name="stato" class="scrittura">
                <option value="1" <?php if($stato=="1") echo("selected");?>>attivo</option>
                <option value="0" <?php if($stato=="0") echo("selected");?>>disattivo</option>
                </select>
        </div>
    </span>-->
	</div>
	<div class="riga"> 
    <div class="rigo_mask">
		<div class="testo_mask">Foto paziente</div>
		<div class="comandi">
			<div id="miniatura" class="aggiungi miniatura"><a href="#">aggiungi foto paziente</a><div id="miniatura_ok"></div></div>
		</div>				
	</div>
	</div>	
	<div class="riga"> 
    <div class="rigo_mask rigo_big">
		<div class="testo_mask">Note anagrafiche</div>
		<textarea name="note_ana" id="note_ana" class="scrittura_campo"><?php echo($note_ana);?></textarea>		
	</div>
    </div>
	<div class="riga"> 
	<?php					
		$elaion="";	
		$tutor="";				
		$query = "SELECT * FROM modelli_word WHERE (stato = 1)";
		$query_t="SELECT dbo.re_utenti_tutor.* FROM dbo.re_utenti_tutor WHERE (IdUtente = $idutente) ORDER BY id DESC";
		$rs_t = mssql_query($query_t, $conn);
		if(!$rs_t) error_message(mssql_error());
		$num_rows=mssql_num_rows($rs_t);				
		if($num_rows==0){
			$query.=" AND (tipo='adulto')";
			}
			else{
			$query.=" AND (tipo='minore')";
			$id_tutor="";
			while($row_t = mssql_fetch_assoc($rs_t)){
				$id_tutor_old=$id_tutor;
				$id_tutor=trim($row_t['id']);
				$tutor.="&idtutor=$id_tutor;";
			}			
			}
		$rs = mssql_query($query, $conn);
		//echo($query);	
		if(mssql_num_rows($rs)==0) {
			$query = "SELECT * FROM modelli_word WHERE (stato = 1) AND (tipo='elaion')";
			$elaion="&consenso=informato";
			$tutor="";
			$rs = mssql_query($query, $conn);
			}	
		if((mssql_num_rows($rs)>0)and(1==2)){
			$arr_tut=split(";",$tutor);
			while($row=mssql_fetch_assoc($rs)){
				if($tutor!=""){
				foreach ($arr_tut as $value) {
					if($value!=""){
		?>
		<span class="rigo_mask" >
		<div class="testo_mask">stampa modello</div>		
	   <div class="box"><a class="rounded {transparent, anti-alias} button2 " href="stampa_modulo.php?modello=<?=$row['codice']?><?=$value?><?=$elaion?>&idpaziente=<?=$idutente?>"><?=$row['etichetta']?></a></div>
	   </span>
	   <?}
		}
		}else{
		?>
		<span class="rigo_mask" >
		<div class="testo_mask">stampa modello</div>		
	   <div class="box"><a class="rounded {transparent, anti-alias} button2 " href="stampa_modulo.php?modello=<?=$row['codice']?><?=$elaion?>&idpaziente=<?=$idutente?>"><?=$row['etichetta']?></a></div>
	   </span>
	   <?		
		}
		}
	   }
	   if(($num_rows==2)and($elaion=="")){		
		$query = "SELECT * FROM modelli_word WHERE (stato = 1) AND (tipo='tutor_c')";		
		$rs1 = mssql_query($query, $conn);
		if(mssql_num_rows($rs1)>0){
		?>
	
	<?php
		while($row1=mssql_fetch_assoc($rs1)){
	?>
		<span class="rigo_mask" >
		<div class="testo_mask">stampa modello</div>
		<div class="box"><a class="rounded {transparent, anti-alias} button2 " href="stampa_modulo.php?modello=<?=$row1['codice']?>&idpaziente=<?=$idutente?>&idtutor=<?=$id_tutor_old?>&idtutor2=<?=$id_tutor?>"><?=$row1['etichetta']?></a></div>
		</span>
	<?}?>
		
	<?}
	}?>		 
	 </div>
	 </div>
	
    <div class="titolo_pag">
	    <div class="comandi">
        	<h1>Residenza</h1>
        </div>
		<div class="comandi">
			<div id="aggiungi_residenza" class="aggiungi" onclick="document.getElementById('res_dec').style.display='block';$('#man_div').addClass('mandatory');$('#man_div').removeClass('nomandatory');aggiungi_nuovo('residenza')"><a>aggiungi residenza</a></div>
		</div>
        <?php
		$query = "SELECT * FROM utenti_residenze WHERE (utenti_residenze.stato = 1) AND (utenti_residenze.cancella = 'n') AND (utenti_residenze.IdUtente = $idutente)";
		$rs = mssql_query($query, $conn);
		if(mssql_num_rows($rs)>1){
		?>
        <div class="comandi">
			<div id="storico_residenza" class="aggiungi storico"><a>visualizza storico residenza</a></div>
		</div>
        <?php }
		mssql_free_result($rs);?>
	</div>
	<input type="hidden" name="flag_residenza" id="flag_residenza" SIZE="30" maxlenght="30" value="false"/>
    <input type="hidden" name="id_residenza" value="<?=$id_residenza?>"/>
    <div class="blocco_centralcat" id="residenza">
	<div class="comunicazione"></div>
	<div class="riga"> 
     <span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="indirizzo" SIZE="30" maxlenght="30" value="<?php echo($res_indirizzo);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">provincia di residenza <a onclick="carica_province(document.getElementById('prov_residenza').value,2)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask mandatory" id="province_res">
			<input type="text" class="scrittura readonly" name="prov_residenza" id="prov_residenza" SIZE="30" maxlenght="30" value="<?php echo($res_provincia);?>" readonly/>
            
		</div>
	</span>
	<span class="rigo_mask">
		<div class="testo_mask">Comune di residenza <a onclick="carica_comuni(document.getElementById('prov_residenza').value,2,document.getElementById('comune_residenza').value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask mandatory" id="comuni_res">
         <input type="text" class="scrittura readonly" name="comune_residenza_nome" SIZE="30" maxlenght="30" value="<?php echo($res_comune);?>" readonly/>
		  <input type="hidden" name="comune_residenza" id="comune_residenza" value="<?=$res_comune_id?>"/>
              
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask" id="cap_r">
			<input type="text" class="scrittura " name="cap_residenza" SIZE="30" maxlenght="30" value="<?php echo($res_cap);?>"readonly/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="telefono" SIZE="30" maxlenght="30" value="<?php echo($res_telefono);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" name="fax" SIZE="30" maxlenght="30" value="<?php echo($res_fax);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask ">
			<input id="cell" type="text" class="scrittura" name="cellulare" SIZE="30" maxlenght="30" value="<?php echo($res_cellulare);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask email">
			<input type="text" class="scrittura" name="email" SIZE="30" maxlenght="30" value="<?php echo($res_email);?>"/>
		</div>
	</span>
	<?php
	if($decorrenza==""){
		$display="none";
		$man="nomandatory";}
		else{
		$display="block";
		$man="nomandatory";}
	?>
	<div id="res_dec" style="display:<?=$display?>;">
	<span class="rigo_mask" >
		<div class="testo_mask">a decorrere dal</div>
		<div id="man_div" class="campo_mask nomandatory data_all">
			<input type="text" class="scrittura campo_data" name="decorrenza" SIZE="30" maxlenght="30" value="<?php echo($decorrenza);?>"/>
		</div>
	</span>
	</div>
	</div>
	</div>
 
    <div class="titolo_pag">
	    <div class="comandi">
        	<h1>Medico Curante</h1>
        </div>
		<div class="comandi">
			<div id="aggiungi_medico" class="aggiungi" onclick="document.getElementById('med_dec').style.display='block';$('#man_div_med').addClass('mandatory');$('#man_div_med').removeClass('nomandatory');aggiungi_nuovo('medico')"><a>aggiungi medico</a></div>
		</div>
        <?php
		$query = "SELECT * FROM utenti_medici WHERE (utenti_medici.stato = 1) AND (utenti_medici.cancella = 'n') AND (utenti_medici.IdUtente = $idutente)";
		$rs = mssql_query($query, $conn);
		if(mssql_num_rows($rs)>1){
		?>
        <div class="comandi">
			<div id="storico_medico" class="aggiungi storico_m"><a>visualizza storico medici</a></div>
		</div>
        <?php }
		mssql_free_result($rs);?>
	</div>
	<input type="hidden" name="flag_medico" id="flag_medico" SIZE="30" maxlenght="30" value="false"/>
    <input type="hidden" name="id_medico" value="<?=$id_medico?>"/>
    <div class="blocco_centralcat" id="medico">
	
	<div class="riga">
		<span class="rigo_mask">    
		<div class="testo_mask">medici di medicina generali censiti</div>
		<div class="campo_mask">
			<?
				$query = "SELECT * FROM re_medici_medicina_generale WHERE ((re_medici_medicina_generale.cancella = 'n'))ORDER BY re_medici_medicina_generale.cognome";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="case_manager" class="scrittura" onchange="javascript:carica_medico(this.value);">
					<option value="">seleziona un medico</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					$id_med=$row['id'];
					$nome=pulisci_lettura($row['cognome']." ".$row['nome']);
					$comune=pulisci_lettura($row['nome_comune']." ".$row['sigla']);				
					?>
		                <option <?=$sel?> value="<?=$id_med?>"><?=$nome." - ".$comune?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>				 
					</select>
		</div>
	</span>
	</div>	
	<div class="comunicazione"></div>
	<div class="riga"> 
	<span class="rigo_mask">    
		<div class="testo_mask">cognome</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura_campo solo_lettere" id="cognome_m" name="cognome_m" SIZE="30" maxlenght="30" value="<?php echo($med_cognome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura_campo solo_lettere" id="nome_m" name="nome_m" SIZE="30" maxlenght="30" value="<?php echo($med_nome);?>"/>
		</div>
	</span>
    
	<span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura" id="indirizzo_m" name="indirizzo_m" SIZE="30" maxlenght="30" value="<?php echo($med_indirizzo);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">provincia di residenza <a onclick="carica_province(document.getElementById('med_residenza').value,5)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask nomandatory" id="province_med">
			<input type="text" class="scrittura readonly" name="med_residenza" id="med_residenza" SIZE="30" maxlenght="30" value="<?php echo($med_provincia);?>" readonly/>            
		</div>
		<?
		if($med_provincia==""){?>
		<script type="text/javascript">
		carica_province(document.getElementById('med_residenza').value,5);
		</script><?
		}?>
	</span>
	<span class="rigo_mask">
		<div class="testo_mask">Comune di residenza <a onclick="carica_comuni(document.getElementById('med_residenza').value,5,document.getElementById('med_comune_residenza').value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask nomandatory" id="comuni_med">
         <input type="text" class="scrittura readonly" id="med_comune_residenza_nome" name="med_comune_residenza_nome" SIZE="30" maxlenght="30" value="<?php echo($med_comune);?>" readonly/>
		 <input type="hidden" name="med_comune_residenza" id="med_comune_residenza" value="<?=$med_comune_id?>"/>
              
		</div>
	</span>
	
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask" id="cap_med">
			<input type="text" class="scrittura" id="med_cap_residenza" name="med_cap_residenza" SIZE="30" maxlenght="30" value="<?php echo($med_cap);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" id="med_telefono" name="med_telefono" SIZE="30" maxlenght="30" value="<?php echo($med_telefono);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" id="med_fax" name="med_fax" SIZE="30" maxlenght="30" value="<?php echo($med_fax);?>"/>
		</div>
	</span>	
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" id="med_cellulare" name="med_cellulare" SIZE="30" maxlenght="30" value="<?php echo($med_cellulare);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask email">
			<input type="text" class="scrittura" id="med_email" name="med_email" SIZE="30" maxlenght="30" value="<?php echo($med_email);?>"/>
		</div>
	</span>
	<?php
	if($med_decorrenza==""){
		$display="none";
		$man="nomandatory";}
		else{
		$display="block";
		$man="nomandatory";}
	?>
	<div id="med_dec" style="display:<?=$display?>;">
		<span class="rigo_mask" >
			<div class="testo_mask">a decorrere dal</div>
			<div id="man_div_med" class="campo_mask <?=$man?> data_all">
				<input type="text" class="scrittura campo_data" name="med_decorrenza" SIZE="30" maxlenght="30" value="<?php echo($med_decorrenza);?>"/>
			</div>
		</span>
	</div>	
	</div>
	</div>
	
	<div class="titolo_pag">
	    <div class="comandi">
        	<h1>Genitori\Tutor</h1>
        </div>
		 <?php
		$query = "SELECT * FROM dbo.utenti_tutor WHERE (utenti_tutor.stato = 1) AND (utenti_tutor.cancella = 'n') AND (utenti_tutor.IdUtente = $idutente)  AND (utenti_tutor.disattivato IS NULL OR dbo.utenti_tutor.disattivato > { fn NOW() })";
		$rs = mssql_query($query, $conn);
		$num_tutor=mssql_num_rows($rs);
		if($num_tutor<2){
		?>
		<div class="comandi">
			<div id="aggiungi_tutor" class="aggiungi" onclick="aggiungi_nuovo('tutor')"><a>aggiungi tutor</a></div>
		</div>
         <?php
		 }
		$query = "SELECT * FROM dbo.utenti_tutor WHERE (utenti_tutor.stato = 1) AND (utenti_tutor.cancella = 'n') AND (utenti_tutor.IdUtente = $idutente)";
		$rs = mssql_query($query, $conn);
		$num_tutor_s=mssql_num_rows($rs);
		if($num_tutor_s>0){
		?>
        <div class="comandi">
			<div class="aggiungi storico_t" id="storico_tutor"><a>visualizza storico tutor</a></div>
		</div>
        <?php }
		mssql_free_result($rs);
		?>
	</div>
	<?php
	$id_tr="";	
	$query="SELECT dbo.re_utenti_tutor.* FROM dbo.re_utenti_tutor WHERE (IdUtente = $idutente) ORDER BY id DESC";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	$num_rows=mssql_num_rows($rs);
	$i=1;	
	while($row = mssql_fetch_assoc($rs)){
		$id_tutor_old=$id_tutor;
		$id_tutor=trim($row['id']);
		$tut_nome=trim(pulisci_lettura($row['nome']));
		$tut_sesso=$row['sesso'];
		$tut_data_nascita=trim(formatta_data($row['data_nascita']));
		if($tut_data_nascita=="01/01/1900") $tut_data_nascita=""; 
		$tut_codice_fiscale=trim($row['codice_fiscale']);
		$tutore_principale=$row['tutoreprincipale'];
		$tutore1=$row['tutore1'];
		$tutore2=$row['tutore2'];
		$delegato1=$row['delegato1'];
		$delegato2=$row['delegato2'];
		$tut_cognome=trim(pulisci_lettura($row['cognome']));
		$tut_indirizzo=pulisci_lettura(trim($row['indirizzo']));
		$tut_tel=trim($row['telefono']);
		$tut_fax=trim($row['fax']);
		$tut_cell=trim($row['cellulare']);
		$tut_mail=trim($row['email']);
		$tut_relazione=pulisci_lettura(trim($row['relazione_paziente']));
		$tut_comune=trim($row['comune_r']);
		$tut_comune_id=trim($row['id_comune']);
		$tut_n_comune=pulisci_lettura(trim($row['nome_comune_n']));
		$tut_n_comune_id=trim($row['id_comune_n']);
		$tut_cap=trim($row['cap']);
		$tut_provincia=trim($row['nome_prov']);
		$tut_n_provincia=pulisci_lettura(trim($row['nome_prov_n']));
		$tut_stato=trim($row['stato_nascita']);
		$tut_sigla=trim($row['sigla']);
		$disattivato=formatta_data($row['disattivato']);
		$id_tr.=$id_tutor.";";
		
		if($tut_n_provincia==""){
		$query = "SELECT  id, nome FROM  province WHERE (nome ='$tut_n_comune')";	
		$rs1 = mssql_query($query, $conn);
		if($row1 = mssql_fetch_assoc($rs1)){
			$tut_n_provincia=pulisci_lettura($row1['nome']);
		}
	}
	?>
		<input type="hidden" name="flag_tutor" id="flag_tutor" SIZE="30" maxlenght="30" value="false"/>   
    <div class="blocco_centralcat" id="tutor">
    <div class="comunicazione"></div>
    <div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">cognome</div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti#">
			<input id="c_t_<?=$id_tutor?>" type="text" class="scrittura_campo solo_lettere" name="cognome_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_cognome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti#">
			<input id="n_t_<?=$id_tutor?>" type="text" class="scrittura_campo solo_lettere" name="nome_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_nome);?>"/>
		</div>
	</span>
		
	<span class="rigo_mask">
        <div class="testo_mask">sesso</div>
        <div class="campo_mask">
            <input id="1s_t_<?=$id_tutor?>" type="radio" name="sesso_t_<?=$id_tutor?>" value="1" <?php if($tut_sesso=="1") echo("checked");?>/> M 
            <input id="2s_t_<?=$id_tutor?>" type="radio" name="sesso_t_<?=$id_tutor?>" value="2" <?php if($tut_sesso=="2") echo("checked");?>/> F
        </div>
    </span>
    </div>
	
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">data di nascita</div>
		<div class="campo_mask data_passata nomandatory">
			<input type="text" class="campo_data scrittura"  name="data_nascita_t_<?=$id_tutor?>" id="data_nascita_t_<?=$id_tutor?>" value="<?php echo($tut_data_nascita);?>" />			
		</div>
	</span>	
	
	<div class="rigo_mask">
		<div class="testo_mask">stato di nascita</div>
		<div class="campo_mask mandatory">
			 <select id="stato_nascita_t_<?=$id_tutor?>"  class="scrittura" name="stato_nascita_t_<?=$id_tutor?>" onChange="javascript:carica_stato(this.value,2,<?=$id_tutor?>);">
			<option value="297" selected="selected">ITALIA</option>
			 <?php 
			$conn = db_connect();
			$query = "SELECT id, nome FROM dbo.province WHERE sigla='(EE)' order by nome";
			$rs_s = mssql_query($query, $conn);
			 while ($row_s = mssql_fetch_assoc($rs_s)) {
	
				// setta la variabile di sessione
				$idprov = $row_s['id'];
				$nomeprov = htmlentities($row_s['nome']);
									
				print('<option value="'.$idprov.'"');
				if($tut_stato==$idprov) echo("selected='selected'");
				print ('>'.$nomeprov.'</option>'."\n");					
			}
			mssql_free_result($rs_s);
			?>
			</select>
		</div>
	</div>
    
    <span class="rigo_mask">
		<div class="testo_mask">provincia di nascita <a onclick="carica_province(document.getElementById('prov_residenza_tn_<?=$id_tutor?>').value,4,<?=$id_tutor?>)" class="cambia_valore">cambia provincia</a></div>
		<div class="province_tut_n<?=$id_tutor?> campo_mask #condition,data_nascita,18,anni_precedenti#" >
			<input type="text" class="scrittura readonly" name="prov_residenza_tn_<?=$id_tutor?>" id="prov_residenza_tn_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_n_provincia);?>"readonly/>		
        </div>
	</span> 
	</div>
	<div class="riga"> 	
     <span class="rigo_mask">
		<div class="testo_mask">Comune di nascita <a onclick="carica_comuni(document.getElementById('prov_residenza_tn_<?=$id_tutor?>').value,4,document.getElementById('comune_residenza_tn_<?=$id_tutor?>').value,<?=$id_tutor?>)" class="cambia_valore">cambia comune</a></div>
        <div class="comuni_tut_n<?=$id_tutor?> campo_mask">
		<input type="text" class="scrittura readonly" name="comune_residenza_tn_nome_<?=$id_tutor?>" id="comune_residenza_tn_nome_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_n_comune);?>" readonly/>		
		<input type="hidden"  name="comune_residenza_tn_<?=$id_tutor?>" id="comune_residenza_tn_<?=$id_tutor?>"  value="<?=$tut_n_comune_id?>"/>                   
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">Codice Fiscale <a rel="<?=$id_tutor?>" class="cambia_valore calcola_cf_t">calcola Codice Fiscale</a></div>
		<div class="campo_mask nomandatory codice_fiscale">
			<input id="cod_fisc_t_<?=$id_tutor?>" type="text" class="scrittura" name="codice_fiscale_t_<?=$id_tutor?>" SIZE="30" maxlength="16" value="<?php echo($tut_codice_fiscale);?>" onKeyUp="this.value=this.value.toUpperCase()" />
		</div>
	</span>
	
        <div class="rigo_mask">
            <div class="testo_mask">Tipologia Genitore/Tutore</div>
            <div class="campo_mask">
				<input type="checkbox" id="delegato_1_<?=$id_tutor?>" name="delegato_1_<?=$id_tutor?>" value="1" <?php if($delegato1=="-1") echo("checked");?>><label for="delegato_1_<?=$id_tutor?>"> Delegato 1 &nbsp;</label>
				<input type="checkbox" id="delegato_2_<?=$id_tutor?>" name="delegato_2_<?=$id_tutor?>" value="1" <?php if($delegato2=="-1") echo("checked");?>><label for="delegato_2_<?=$id_tutor?>"> Delegato 2</label><br>
				<input type="checkbox" id="tutor_p_<?=$id_tutor?>" name="tutor_p_<?=$id_tutor?>" value="1" <?php if($tutore_principale=="-1") echo("checked");?>><label for="tutor_p_<?=$id_tutor?>"> Tutore principale &nbsp;</label>
				<input type="checkbox" id="tutor_1_<?=$id_tutor?>" name="tutor_1_<?=$id_tutor?>" value="1" <?php if($tutore1=="-1") echo("checked");?>><label for="tutor_1_<?=$id_tutor?>"> Tutore 1 &nbsp;</label>
				<input type="checkbox" id="tutor_2_<?=$id_tutor?>" name="tutor_2_<?=$id_tutor?>" value="1" <?php if($tutore2=="-1") echo("checked");?>><label for="tutor_2_<?=$id_tutor?>"> Tutore 2 &nbsp;</label>
            </div>
        </div>
		
	</div>
	<div class="riga">
	<div class="etichetta_campi" style="font-size:12px;">Informazioni sulla residenza del genitore\tutor</div>
	</div>
	<div class="riga">
    <span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti#">
			<input type="text" class="scrittura" name="indirizzo_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_indirizzo);?>"/>
		</div>
	</span>    
	<span class="rigo_mask">
		<div class="testo_mask">provincia di residenza <a onclick="carica_province(document.getElementById('prov_residenza_t_<?=$id_tutor?>').value,3,<?=$id_tutor?>)" class="cambia_valore">cambia provincia</a></div>
		<div class="province_tut<?=$id_tutor?> campo_mask #condition,data_nascita,18,anni_precedenti#" >
			<input type="text" class="scrittura readonly" name="prov_residenza_t_<?=$id_tutor?>" id="prov_residenza_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_provincia);?>"readonly/>		
        </div>
	</span>	
    <span class="rigo_mask">
		<div class="testo_mask">Comune di residenza <a onclick="carica_comuni(document.getElementById('prov_residenza_t_<?=$id_tutor?>').value,3,document.getElementById('comune_residenza_t_<?=$id_tutor?>').value,<?=$id_tutor?>)" class="cambia_valore">cambia comune</a></div>
        <div class="campo_mask comuni_tut<?=$id_tutor?>" >
		<input type="text" class="scrittura readonly" name="comune_residenza_t_nome_<?=$id_tutor?>" id="comune_residenza_t_nome_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_comune);?>" readonly/>		
		<input type="hidden"  name="comune_residenza_t_<?=$id_tutor?>" id="comune_residenza_t_<?=$id_tutor?>"  value="<?=$tut_comune_id?>"/>                   
		</div>
	</span>
	</div>
	<div class="riga">
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask" id="cap_t">
			<input type="text" class="scrittura " name="cap_residenza_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_cap);?>"readonly/>
		</div>
	</span>	
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="telefono_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_tel);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" name="fax_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_fax);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" name="cellulare_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_cell);?>"/>
		</div>
	</span>	
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask email ">
			<input type="text" class="scrittura" name="email_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_mail);?>"/>
		</div>
	</span>	
     <span class="rigo_mask">
		<div class="testo_mask">relazione con il paziente</div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti#">
			<input type="text" class="scrittura" name="relazione_t_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($tut_relazione);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
	<?if ($disattivato=="01/01/1900") $disattivato="";?>
	<span class="rigo_mask" >
		<div class="testo_mask">disattivato dal</div>
		<div class="campo_mask nomandatory data_all">
			<input type="text" class="scrittura campo_data" name="disattivato_<?=$id_tutor?>" SIZE="30" maxlenght="30" value="<?php echo($disattivato);?>"/>
		</div>
	</span>
	</div>
	 <?php
		$query = "SELECT * FROM modelli_word WHERE (stato = 5) AND ((tipo='genitore') OR (tipo='tutor'))";		
		$rs1 = mssql_query($query, $conn);
		if(mssql_num_rows($rs1)>0){
		?>
	<div class="riga">
	<?php
		while($row1=mssql_fetch_assoc($rs1)){
	?>
		<span class="rigo_mask" >
		<div class="testo_mask">stampa modello</div>
		<div class="box"><a class="rounded {transparent, anti-alias} button2 " href="stampa_modulo.php?modello=<?=$row1['codice']?>&idpaziente=<?=$idutente?>&idtutor=<?=$id_tutor?>"><?=$row1['etichetta']?></a></div>
		</span>
	<?}?>
	</div>	
	<?}?>
	</div>
	<? 
		$i++;
	}
	
	if ($num_rows==0){?>
	<input type="hidden" name="flag_tutor" id="flag_tutor" SIZE="30" maxlenght="30" value="false"/>
    <input type="hidden" name="id_tutor" value="<?=$id_tutor?>"/>
    <div class="blocco_centralcat" id="tutor">
    <div class="comunicazione"></div>
    <div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">cognome</div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti#">
			<input id="c_t_" type="text" class="scrittura_campo solo_lettere" name="cognome_t" SIZE="30" maxlenght="30" value="<?php echo($tut_cognome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti#">
			<input id="n_t_" type="text" class="scrittura_campo solo_lettere" name="nome_t" SIZE="30" maxlenght="30" value="<?php echo($tut_nome);?>"/>
		</div>
	</span>
	
	<span class="rigo_mask">
        <div class="testo_mask">sesso</div>
        <div class="campo_mask">
            <input id="1s_t_" type="radio" name="sesso_t_" value="1" checked /> M 
            <input id="2s_t_" type="radio" name="sesso_t_" value="2" /> F
        </div>
    </span>
    </div>
	
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">data di nascita</div>
		<div class="campo_mask data_passata nomandatory">
			<input type="text" class="campo_data scrittura"  name="data_nascita_t_" id="data_nascita_t_" value="<?php echo($tut_data_nascita);?>" />			
		</div>
	</span>

	<div class="rigo_mask">
		<div class="testo_mask">stato di nascita</div>
		<div class="campo_mask mandatory">
			 <select id="stato_nascita_t_"  class="scrittura" name="stato_nascita_t_" onChange="javascript:carica_stato(this.value,2);">
			<option value="297" selected="selected">ITALIA</option>
			 <?php 
			$conn = db_connect();
			$query = "SELECT id, nome FROM dbo.province WHERE sigla='(EE)' order by nome";
			$rs_s = mssql_query($query, $conn);
			 while ($row_s = mssql_fetch_assoc($rs_s)) {
	
				// setta la variabile di sessione
				$idprov = $row_s['id'];
				$nomeprov = htmlentities($row_s['nome']);
									
				print('<option value="'.$idprov.'"');
				//if($stato_nascita==$idprov) echo("selected='selected'");
				print ('>'.$nomeprov.'</option>'."\n");					
			}
			mssql_free_result($rs_s);
			?>
			</select>
		</div>
	</div>
	
    <span class="rigo_mask">
		<div class="testo_mask">provincia di nascita <a onclick="carica_province(document.getElementById('prov_residenza_tn_').value,4)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti# province_tut_n">
			<input type="text" class="scrittura readonly" name="prov_residenza_tn_" id="prov_residenza_tn_" SIZE="30" maxlenght="30" value="<?php echo($tut_n_provincia);?>"readonly/>		
        </div>
		<?
		if($tut_n_provincia==""){?>
		<script type="text/javascript">
		carica_province(document.getElementById('prov_residenza_tn_').value,4);
		</script><?
		}?>
	</span>
	</div>
	<div class="riga">     
     <span class="rigo_mask">
		<div class="testo_mask">Comune di nascita <a onclick="carica_comuni(document.getElementById('prov_residenza_tn_').value,4,document.getElementById('comune_residenza_tn_').value)" class="cambia_valore">cambia comune</a></div>
        <div class="campo_mask comuni_tut_n">
		<input type="text" class="scrittura readonly" name="comune_residenza_tn_nome_" id="comune_residenza_tn_nome_" SIZE="30" maxlenght="30" value="<?php echo($tut_n_comune);?>" readonly/>		
		<input type="hidden"  name="comune_residenza_tn_" id="comune_residenza_tn_"  value="<?=$tut_n_comune_id?>"/>                   
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">Codice Fiscale <a rel="" class="cambia_valore calcola_cf_t">calcola Codice Fiscale</a></div>
		<div class="campo_mask nomandatory codice_fiscale">
			<input id="cod_fisc_t_" type="text" class="scrittura" name="codice_fiscale_t_" SIZE="30" maxlength="16" value="<?php echo($tut_codice_fiscale);?>" onKeyUp="this.value=this.value.toUpperCase()" />
		</div>
	</span>
        <div class="rigo_mask">
            <div class="testo_mask">Tipologia Genitore/Tutore</div>
            <div class="campo_mask">
				<input type="checkbox" id="delegato_1_" name="delegato_1_" value="1" <?php if($delegato1=="-1") echo("checked");?>><label for="delegato_1_<?=$id_tutor?>"> Delegato 1 &nbsp;</label>
				<input type="checkbox" id="delegato_2_" name="delegato_2_" value="1" <?php if($delegato2=="-1") echo("checked");?>><label for="delegato_2_<?=$id_tutor?>"> Delegato 2</label><br>
				<input type="checkbox" id="tutor_p_" name="tutor_p_" value="1" <?php if($tutore_principale=="-1") echo("checked");?>><label for="tutor_p_<?=$id_tutor?>"> Tutore principale &nbsp;</label>
				<input type="checkbox" id="tutor_1_" name="tutor_1_" value="1" <?php if($tutore1=="-1") echo("checked");?>><label for="tutor_1_<?=$id_tutor?>"> Tutore 1 &nbsp;</label>
				<input type="checkbox" id="tutor_2_" name="tutor_2_" value="1" <?php if($tutore2=="-1") echo("checked");?>><label for="tutor_2_<?=$id_tutor?>"> Tutore 2 &nbsp;</label>
            </div>
        </div>
	</div>
	<div class="riga">
	<div class="etichetta_campi" style="font-size:12px;">Informazioni sulla residenza del genitore\tutor</div>
	</div>
	<div class="riga">
    <span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti#">
			<input type="text" class="scrittura" name="indirizzo_t" SIZE="30" maxlenght="30" value="<?php echo($tut_indirizzo);?>"/>
		</div>
	</span>
  
	<span class="rigo_mask">
		<div class="testo_mask">provincia di residenza <a onclick="carica_province(document.getElementById('prov_residenza_t_').value,3)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti# province_tut">
			<input type="text" class="scrittura readonly" name="prov_residenza_t_" id="prov_residenza_t_" SIZE="30" maxlenght="30" value="<?php echo($tut_provincia);?>"readonly/>		
        </div>
		<?
		if($tut_n_provincia==""){?>
		<script type="text/javascript">
		carica_province(document.getElementById('prov_residenza_t_').value,3);
		</script><?
		}?>
	</span>   
    <span class="rigo_mask">
		<div class="testo_mask">Comune di residenza <a onclick="carica_comuni(document.getElementById('prov_residenza_t_').value,3,document.getElementById('comune_residenza_t_').value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti# comuni_tut">
        <input type="text" class="scrittura readonly" name="comune_residenza_t_nome" SIZE="30" maxlenght="30" value="<?php echo($tut_comune);?>" readonly/>		
		<input type="hidden"  name="comune_residenza_t_" id="comune_residenza_t_"  value="<?=$tut_comune_id?>"/>    
                   
		</div>
	</span>
     </div>
	<div class="riga">       
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask" id="cap_t">
			<input type="text" class="scrittura " name="cap_residenza_t" SIZE="30" maxlenght="30" value="<?php echo($tut_cap);?>"readonly/>
		</div>
	</span>
	
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="telefono_t" SIZE="30" maxlenght="30" value="<?php echo($tut_tel);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" name="fax_t" SIZE="30" maxlenght="30" value="<?php echo($tut_fax);?>"/>
		</div>
	</span>
    </div>
	<div class="riga"> 
	<span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" name="cellulare_t" SIZE="30" maxlenght="30" value="<?php echo($tut_cell);?>"/>
		</div>
	</span>
	
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask email ">
			<input type="text" class="scrittura" name="email_t" SIZE="30" maxlenght="30" value="<?php echo($tut_mail);?>"/>
		</div>
	</span>	
     <span class="rigo_mask">
		<div class="testo_mask">relazione con il paziente</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="relazione_t" SIZE="30" maxlenght="30" value="<?php echo($tut_relazione);?>"/>
		</div>
	</span>
	</div>
	</div>
	<div class="riga"> 	
	</div>
	<? }?>
	<input type="hidden" name="id_tutor" value="<?=$id_tr?>"/>
   
     <? 
	
	if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and (($_SESSION['UTENTE']->get_tipoaccesso()!=5)))
	{
	?>
    <div class="titolo_pag">
		<div class="comandi">
			<input type="submit"  title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	<?
	}
	else
	print("<p>Non si dispone dei permessi necessari per modificare l'impegnativa</p>");
	?>
	</form>
	
	
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				$(".solo_lettere").validation({ 
					type: "alpha",
					add:"' "	
				}); 	
		
	});
		
	
function carica_medico(id_med){
	med=$("#cognome_m").val();	
	if(med!=""){
		if (confirm('si vuole sostiutire il medico esistente?'))
			go="1";
			else
			go="0";
	}else{
	go="1";
	}
	if(go=="1"){
		$.ajax({
		   type: "POST",
		   url: "get_dati_medico.php",
		   data: "idmedico="+id_med,
		   success: function(msg){
			arr_med=msg.split(";");
			$("#cognome_m").val(arr_med[0]);
			$("#nome_m").val(arr_med[1]);
			$("#indirizzo_m").val(arr_med[2]);
			$("#med_residenza").val(arr_med[3]);			
			$("#med_comune_residenza_nome").val(arr_med[5]);
			$("#med_comune_residenza").val(arr_med[6]);
			$("#med_cap_residenza").val(arr_med[7]);
			$("#med_telefono").val(arr_med[8]);
			$("#med_fax").val(arr_med[9]);
			$("#med_cellulare").val(arr_med[10]);
			$("#med_email").val(arr_med[11]);		
		   }
		   });
	 }
}

</script>
</div>	
	
<?php
 }
 

if(isset($_SESSION['UTENTE'])) {
	?>
    <script type="text/javascript"> 
		x=inizializza();
	</script> 
    <?php
	
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
					else show_allegati($_REQUEST['id']);
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
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>