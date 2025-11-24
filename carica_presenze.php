<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 1;
include_once('include/function_page.php');
session_start();
//$idutente=$id;


function elenco_presenze($idricetta_codice){
?>

<script>
inizializza();
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('carica_presenze.php?&do=&id='+id, function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		
		
		popupStatus = 1;
	}
}

function loadPopup_add(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('gest_presenze.php?do=add&id='+id, function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		
		
		popupStatus = 1;
	}
}
function loadPopup_edit(idpresenza,idricetta){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('gest_presenze.php?do=edit&idpresenza='+idpresenza+'&idricetta='+idricetta, function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		
		
		popupStatus = 1;
	}
}


//disabling popup with jQuery magic!
function disablePopup_force(){
	//disables popup only if it is enabled
	
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
		
	
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
	disablePopup();
	loadPopup(id);
	centerPopup();
	//load popup


}

function prev_add(id){	
	//centering with css
	disablePopup();
	loadPopup_add(id);
	centerPopup();
	//load popup


}

function prev_edit(idpresenza,idricetta){	
	//centering with css
	disablePopup();
	loadPopup_edit(idpresenza,idricetta);
	centerPopup();
	//load popup


}


//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!
	$(".preview").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
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

function gest_presenze(azione,idpresenza,idricetta)
{

$.ajax({
	   async: false,
	   type: "POST",
	   url: "gest_presenze.php",
	   data: "azione="+azione+"&idpresenza="+idpresenza+"&idricetta="+idricetta,
	   success: function(msg){				
		  prev(idricetta);
	 }
	 });


}

</script>

<div id="carica_presenze">
<div class="titoloalternativo">
        <h1>Elenco Presenze</h1>
</div>

<div class="titolo_pag">
	<div class="comandi">
		<div class="aggiungi aggiungi_left"><a onclick="javascript:prev_add('<?=$idricetta_codice?>');">aggiungi presenza</a></div>
	</div>
	<div class="comandi" style="float:right;">
		<div class="elimina"><a href="#" onclick="javascript:disablePopup_force();">chiudi scheda presenze</a></div>
	</div>	
</div>
<?
$conn = db_connect();
$query="SELECT     IdRicettaPresenza, IdRicettaCodice, DataPrestazione, QuantitaErogate, Reparto, Note, Fatturata, Operatore, OraPrestazione, LEFT(CONVERT(varchar,OraPrestazione, 108), 5) AS Ora
		FROM  RicettePresenze where IdRicettaCodice=$idricetta_codice order by DataPrestazione DESC";
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(mssql_num_rows($rs)==0){?>	
	<div class="info">Non esistono Presenze.</div>	
	<?
	exit();
	}
	?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>    
    <th>data prestazione</th> 
	<th>quantita erogate</th> 
	<th>reparto</th>
	<th>operatore</th>
	<th>ora</th>
    <th>note</th> 	
	<td>P</td>
	<td>A</td>
	<td>modifica</td>
	<td>cancella</td>	
</tr> 
</thead> 
<tbody> 
<?

if(!$rs) error_message(mssql_error());
while($row1 = mssql_fetch_assoc($rs)){
			$id_presenza=$row1['IdRicettaPresenza'];
			$data_prestazione= formatta_data($row1['DataPrestazione']);
			$qta_erogata= $row1['QuantitaErogate'];
			$ora_prestazione= $row1['Ora'];
			$operatore=$row1['Operatore'];
			$reparto = $row1['Reparto'];
			$note = $row1['Note'];
			
			if($operatore!=""){
				$query="select nome,uid FROM Operatori WHERE uid=$operatore";
				$rs_op = mssql_query($query, $conn);
				$row_op = mssql_fetch_assoc($rs_op);
				$nome_operatore=$row_op['nome'];
				mssql_free_result($rs_op);
			}
			?>
		<tr> 
		 <td><?=$data_prestazione?></td> 
		 <td><?=$qta_erogata?></td>
		 <td><?=$reparto?></td>
		 <td><?=$nome_operatore?></td>
		 <td><?=$ora_prestazione?></td>		 
		 <td><?=$note?></td>
		 <?
		 if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and (($_SESSION['UTENTE']->get_tipoaccesso()!=5)))
	{?>
		 <td><a id="<?=$id_presenza?>" onclick="javascript:gest_presenze('add_one','<?=$id_presenza?>','<?=$idricetta_codice?>');" href="#"><img src="images/add.png" /></a></td>
		 <td><a id="<?=$id_presenza?>" onclick="javascript:gest_presenze('rem_one','<?=$id_presenza?>','<?=$idricetta_codice?>');" href="#"><img src="images/close.png" /></a></td>
		<td><a id="<?=$id_presenza?>" onclick="javascript:prev_edit('<?=$id_presenza?>','<?=$idricetta_codice?>');" href="#"><img src="images/gear.png" /></a></td>
		<td><a id="<?=$id_presenza?>" onclick="javascript:if (confirm('sei sicuro di voler cancellare l\'allegato corrente?')){gest_presenze('del','<?=$id_presenza?>','<?=$idricetta_codice?>');}" href="#"><img src="images/remove.png" /></a></td>
		<?}else{?>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<?}?>		
		</tr>
		<?
}?>
</tbody> 
</table>
 
<?
footer_paginazione($conta);
//$numero_pager=(int)($conta/20)
?>

</div>


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
mssql_free_result($rs);

}

function edit($idpresenza){
echo("qui");

}

if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	

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

			case "preview_test_clinico":
				preview_test_clinico($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				//if($_REQUEST['type']=='res')
					elenco_presenze($_REQUEST['id']);
				//else
				//	show_list_tut($_REQUEST['id']);
			break;
		}
			html_footer();


} else {
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>