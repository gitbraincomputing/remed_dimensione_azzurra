<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
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


function edit_presenza()
{
$idprestazione=$_REQUEST['id'];
//echo("prest:".$idprestazione);

$conn=db_connect();
$query="select * from ricettacodice_planning where idricettacodice=$idprestazione";
$rs = mssql_query($query, $conn);

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
        <h1>planning terapie</h1>
</div>


<div class="titolo_pag">	
	<div class="comandi" style="float:right;">
		<div class="elimina"><a href="#" onclick="javascript:disablePopup_force();">chiudi scheda presenze</a></div>
	</div>	
</div>

<div id="wrap-content"><div class="padding10">


		

<form  method="post" name="form0" action="gest_planning.php" id="myForm">
	<input type="hidden" value="update" name="azione"/>	
	<input type="hidden" value="<?=$idprestazione?>" name="idprestazione"/>
	
	<div class="blocco_centralcat">

	<table id="table" class="tablesorter" cellspacing="1"> 
	<thead> 
	<tr>    
		<th>Giorno della settimana</th>
		<th>Operatore</th>
		<th>Ora Prestazione</th>
		<th>Elimina</th>		
	</tr> 
	</thead> 
	<tbody>
	<?
	$i=1;
	while($row=mssql_fetch_assoc($rs)){
		$ora = date_create($row['ora']);
	?>
	<tr>
	<td>
	<input type="hidden" id="el<?=$i?>" name="elimina<?=$i?>" value="" />
	<input type="hidden" id="idcampo<?=$i?>" name="idcampo<?=$i?>" value="<?=$row['Idplanning']?>" />
	<?
	$query1 = "SELECT * from ElencoFrequenze WHERE peso>0 order by peso asc";	
	$rs1 = mssql_query($query1, $conn);
	if(!$rs1) error_message(mssql_error());
	?>
		<select id="frequenza<?=$i?>" name="frequenza<?=$i?>" class="scrittura" style="width:50px">
		<option value="">selezionare</option>
		<?  while($row1 = mssql_fetch_assoc($rs1))   
		{					
		$sel="";
		if ($row['giorno']==$row1['IdFrequenza'])
		$sel="selected";					
		?>
			<option <?=$sel?> value="<?=$row1['IdFrequenza']?>"><?=pulisci_lettura($row1['TipoFrequenza'])?></option>
		 <?
		 }
		 mssql_free_result($rs1);
		 ?>
		 </select>
	</td>
	<td><?
	$query1 = "SELECT * from Operatori order by nome asc";	
	$rs1 = mssql_query($query1, $conn);
	if(!$rs1) error_message(mssql_error());
	?>
		<select id="operatore<?=$i?>" name="operatore<?=$i?>" class="scrittura" style="width:150px">
		<option value="">selezionare</option>
		<?  while($row1 = mssql_fetch_assoc($rs1))   
		{					
		$sel="";
		if ($row['idoperatore']==$row1['uid'])
		$sel="selected";					
		?>
			<option <?=$sel?> value="<?=$row1['uid']?>"><?=pulisci_lettura($row1['nome'])?></option>
		 <?
		 }
		 mssql_free_result($rs1);
		 ?>
		 </select>
	</td>
	<td><input type="text" class="scrittura_campo" id="ora<?=$i?>" name="ora<?=$i?>" value="<?php echo(date_format($ora, 'H:i'));?>" /></td>
	<td><a OnClick="javascript:if (confirm('sei sicuro di voler cancellare la tipologia corrente?')){document.getElementById('el<?=$i?>').value='si';document.getElementById('frequenza<?=$i?>').disabled='disabled';document.getElementById('operatore<?=$i?>').disabled='disabled';document.getElementById('ora<?=$i?>').disabled='disabled';}"><img src="images/remove.png" /></a></td>
	</tr>
	<?
	$i++;
	}
	?>
	<?
	while ($i<8){
	?>
	<tr>
	<td>
	<input type="hidden" id="el<?=$i?>" name="elimina<?=$i?>" value="" />
	<input type="hidden" id="idcampo<?=$i?>" name="idcampo<?=$i?>" value="" />
	<?
	$query1 = "SELECT * from ElencoFrequenze WHERE peso>0 order by peso asc";	
	$rs1 = mssql_query($query1, $conn);
	if(!$rs1) error_message(mssql_error());
	?>
		<select id="frequenza<?=$i?>" name="frequenza<?=$i?>" class="scrittura" style="width:50px">
		<option value="">selezionare</option>
		<?  while($row1 = mssql_fetch_assoc($rs1))   
		{					
		$sel="";
		if ($row['giorno']==$row1['IdFrequenza'])
		$sel="selected";					
		?>
			<option <?=$sel?> value="<?=$row1['IdFrequenza']?>"><?=pulisci_lettura($row1['TipoFrequenza'])?></option>
		 <?
		 }
		 mssql_free_result($rs1);
		 ?>
		 </select>
	</td>
	<td><?
	$query1 = "SELECT * from Operatori order by nome asc";	
	$rs1 = mssql_query($query1, $conn);
	if(!$rs1) error_message(mssql_error());
	?>
		<select id="operatore<?=$i?>" name="operatore<?=$i?>" class="scrittura" style="width:150px">
		<option value="">selezionare</option>
		<?  while($row1 = mssql_fetch_assoc($rs1))   
		{					
		$sel="";
		if ($row['idoperatore']==$row1['uid'])
		$sel="selected";					
		?>
			<option <?=$sel?> value="<?=$row1['uid']?>"><?=pulisci_lettura($row1['nome'])?></option>
		 <?
		 }
		 mssql_free_result($rs1);
		 ?>
		 </select>
	</td>
	<td><input type="text" class="scrittura_campo" id="ora<?=$i?>" name="ora<?=$i?>" value="00:00" /></td>
		<?
	if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and (($_SESSION['UTENTE']->get_tipoaccesso()!=5)))
	{
	?>
	<td><a OnClick="javascript:if (confirm('sei sicuro di voler cancellare la tipologia corrente?')){document.getElementById('el<?=$i?>').value='si';document.getElementById('frequenza<?=$i?>').disabled='disabled';document.getElementById('operatore<?=$i?>').disabled='disabled';document.getElementById('ora<?=$i?>').disabled='disabled';}"><img src="images/remove.png" /></a></td>
	<?
	}else{
	?>
	<td>_</td>
	<?
	}
	?>
	</tr>
	<?
	$i++;
	}
	?>
	</tbody> 
	</table> 
   
	</div>
	<?
	if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and (($_SESSION['UTENTE']->get_tipoaccesso()!=5)))
	{
	?>
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva" onclick="javascript:disablePopup_force();"/>
		</div>
	</div>
	<? }?>
	
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
$i=1;
while($i<8){
	$idprestazione=$_POST['idprestazione'];
	$elimina=$_POST['elimina'.$i];
	$idcampo=$_POST['idcampo'.$i];
	$frequenza=$_POST['frequenza'.$i];
	$operatore=$_POST['operatore'.$i];
	$ora=$_POST['ora'.$i];
	$conn = db_connect();

	
	if(($idcampo!="")and($elimina=='si')){
		$query="DELETE FROM ricettacodice_planning WHERE Idplanning=$idcampo";
		//echo($query);
		$result = mssql_query($query, $conn);
	}
	if(($idcampo!="")and($elimina=='')){
		$query="UPDATE ricettacodice_planning SET idoperatore='$operatore', ora='$ora', giorno='$frequenza' WHERE Idplanning=$idcampo";
		//echo($query);
		$result = mssql_query($query, $conn);
	}
	if(($idcampo=="")and($operatore!='')and($frequenza!='')and($ora!='')){
		$query="INSERT INTO ricettacodice_planning (idricettacodice,idoperatore,ora,giorno) VALUES('$idprestazione','$operatore','$ora','$frequenza')";
		//echo($query);
		$result = mssql_query($query, $conn);
	}
$i++;
}

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