<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 1;
include_once('include/function_page.php');
session_start();
//$idutente=$id;


function gest_presenza(){
$azione=$_POST['azione'];
$idpresenza=$_POST['idpresenza'];
$idricetta=$_POST['idricetta'];
$conn = db_connect();

$val=0;
if ($azione=='add_one')
$val=1;
elseif($azione=='rem_one')
$val=0;



$query="update RicettePresenze set QuantitaErogate=$val where IdRicettaPresenza=$idpresenza";
$rs = mssql_query($query, $conn);
//echo($query);
exit();
}

function add_presenza()
{
$idricetta=$_REQUEST['id'];

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
		$("#popupdati2").load('carica_presenze.php?do=&id='+id, function(){ 
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

//disabling popup with jQuery magic!
function disablePopup_force(){
	//disables popup only if it is enabled
	
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
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

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){		
		$("#backgroundPopup").fadeOut("slow");				
		$("#popupdati2").fadeOut("slow");
		$('#thumbnail_form').hide();
		$("#desc_foto").hide();
		$(".imgareaselect-border2").hide();
		$(".imgareaselect-border1").hide();
		$(".imgareaselect-outer").hide();	
		$(".imgareaselect-selection").hide();	
		popupStatus = 0;
	}
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
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				});
</script>

<div id="carica_presenze">
<div class="titoloalternativo">
        <h1>Nuova presenza</h1>
</div>


<div class="titolo_pag">
	<div class="comandi">
		<div class="aggiungi aggiungi_left"><a onclick="javascript:prev('<?=$idricetta?>');">elenco presenze</a></div>
	</div>
	<div class="comandi" style="float:right;">
		<div class="elimina"><a href="#" onclick="javascript:disablePopup_force();">chiudi scheda presenze</a></div>
	</div>	
</div>

<div id="wrap-content"><div class="padding10">


		

<form  method="post" name="form0" action="gest_presenze.php" id="myForm">
	<input type="hidden" value="create" name="azione"/>
	<input type="hidden" value="<?=$_REQUEST['id']?>" name="id"/>

	<div class="titolo_pag"><h1>Aggiungi Presenza</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">data prestazione</div>
			<div class="campo_mask mandatory data_all">
				<input type="text" name="data_prestazione" class="campo_data"/>
			</div>
		</div>
    </div>    
	
	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">quantit&agrave; erogate</div>
			<div class="campo_mask mandatory integer">
				<input type="text" name="qnt_erogate" class="scrittura" size="5"/>
			</div>
		</div>
    </div>    
   
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



function edit_presenza()
{
$idpresenza=$_REQUEST['idpresenza'];
$idricetta=$_REQUEST['idricetta'];

$conn=db_connect();
$query="select * from RicettePresenze where IdRicettaPresenza=$idpresenza";
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if($row1 = mssql_fetch_assoc($rs)){
$id_presenza=$row1['IdRicettaPresenza'];
$data_prestazione= formatta_data($row1['DataPrestazione']);
$qta_erogata= $row1['QuantitaErogate'];
$reparto = $row1['Reparto'];
$note = $row1['Note'];
}

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
		$("#popupdati2").load('carica_presenze.php?do=&id='+id, function(){ 
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


//disabling popup with jQuery magic!
function disablePopup_force(){
	//disables popup only if it is enabled
	
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
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

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){		
		$("#backgroundPopup").fadeOut("slow");				
		$("#popupdati2").fadeOut("slow");
		$('#thumbnail_form').hide();
		$("#desc_foto").hide();
		$(".imgareaselect-border2").hide();
		$(".imgareaselect-border1").hide();
		$(".imgareaselect-outer").hide();	
		$(".imgareaselect-selection").hide();	
		popupStatus = 0;
	}
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

</script>

<div id="carica_presenze">
<div class="titoloalternativo">
        <h1>modifica presenza</h1>
</div>


<div class="titolo_pag">
	<div class="comandi">
		<div class="aggiungi aggiungi_left"><a onclick="javascript:prev('<?=$idricetta?>');">elenco presenze</a></div>
	</div>
	<div class="comandi" style="float:right;">
		<div class="elimina"><a href="#" onclick="javascript:disablePopup_force();">chiudi scheda presenze</a></div>
	</div>	
</div>

<div id="wrap-content"><div class="padding10">


		

<form  method="post" name="form0" action="gest_presenze.php" id="myForm">
	<input type="hidden" value="update" name="azione"/>
	<input type="hidden" value="<?=$idricetta?>" name="id"/>
<input type="hidden" value="<?=$idpresenza?>" name="idpresenza"/>
	<div class="titolo_pag"><h1>modifica presenza</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">data prestazione</div>
			<div class="campo_mask mandatory data_all">
				<input type="text" name="data_prestazione" class="campo_data" value="<?=$data_prestazione?>"/>
			</div>
		</div>
    </div>    
	
	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">quantit&agrave; erogate</div>
			<div class="campo_mask mandatory integer">
				<input type="text" name="qnt_erogate" class="scrittura" size="5" value="<?=$qta_erogata?>" />
			</div>
		</div>
    </div>    
   
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


function create_presenza(){

$idricettacodice=$_POST['id'];
$data_prestazione=$_POST['data_prestazione'];
$qnt_erogate=$_POST['qnt_erogate'];
$conn = db_connect();
$opeins=$_SESSION['UTENTE']->get_userid();
$datains = date('d/m/Y');
$orains = date('H:i');
$query="insert into RicettePresenze (IdRicettaCodice,DataPrestazione,QuantitaErogate,OpeIns,DataInserimento,OraInserimento) values ($idricettacodice,'$data_prestazione',$qnt_erogate,$opeins,'$datains','$orains')";
$result = mssql_query($query, $conn);
		if(!$result) 
		{
		echo ("no");
		exit();
		die();
		}
	$query = "SELECT MAX(IdRicettaPresenza) FROM RicettePresenze WHERE (opeins=$opeins)";
	$result = mssql_query($query, $conn);
	if(!$result)
	{
		echo ("no");
		exit();
		die();
		}
	else
	{
	$idricettamax=$row[0];
	echo("ok");
	exit();
	}
echo("no");	
exit();
}


function update_presenza() {
$idricettacodice=$_POST['idricetta'];
$idpresenza=$_POST['idpresenza'];
$data_prestazione=$_POST['data_prestazione'];
$qnt_erogate=$_POST['qnt_erogate'];
$conn = db_connect();
$opeins=$_SESSION['UTENTE']->get_userid();
$datains = date('d/m/Y');
$orains = date('H:i');
$query="update RicettePresenze set DataPrestazione='$data_prestazione',QuantitaErogate=$qnt_erogate where IdRicettaPresenza=$idpresenza";
mssql_query($query, $conn);
echo("ok");
exit();
}


function del_presenza(){
$azione=$_POST['azione'];
$idpresenza=$_POST['idpresenza'];
$idricetta=$_POST['idricetta'];
$conn = db_connect();


$query="delete RicettePresenze where IdRicettaPresenza=$idpresenza";
mssql_query($query, $conn);
//echo($query);
exit();
}


if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	

		if (empty($_POST)) $_POST['azione'] = "";

		
		switch($_POST['azione']) {

			case "add_one":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else gest_presenza();					
			break;

			case "rem_one":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else gest_presenza();
			break;
			
			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_presenza();
			break;
			
			case "create":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else create_presenza();
			break;
			
			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update_presenza();
			break;

			
		}

		
		switch($do) {

			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add_presenza();
			break;

			case "edit":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit_presenza();
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