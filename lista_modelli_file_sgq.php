<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 62;
$tablename = 'area';
include_once('include/function_page.php');
if ($principale==true)
{
$nome_pagina="Benvenuto";
header_new($nome_pagina,false);
barra_top_new();
top_message();
menu_new();
$principale=false;
}
function show_list(){

$conn = db_connect();
?>
<script type="text/javascript">
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('carica_preview_sgq.php?&do=&id='+id, function(){ 
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
<div id="popupdati2"></div>
<div id="backgroundPopup"></div>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione modelli SGQ</a></div>
            <div class="elem_pari"><a href="#">elenco modelli precompilati</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_modelli_file_sgq.php?do=add');">aggiungi modello precompilato</a></div>
		</div>
	</div>
<?
$query = "SELECT * from re_sgq_allegati order by descrizione asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>elenco modelli precompilati</h1>
	</div>
	<div class="info">Non esistono modelli precompilati.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
			<th width="10%" align="center">Tipo file</th>
			<!--<th width="10%" align="center">Data e Ora</th>	
			<th width="15%" align="center">Operatore</th>	
			<th width="28%" align="left">nome file</th> -->
			<th width="15%" align="left">Edizione</th> 
			<th width="15%" align="left">Revisione</th>
			<th align="left">Descrizione</th> 			
			<td width="5%" align="center" class="no_print">Visualizza</td>
			<td width="5%" align="center" class="no_print">Modifica</td>
			<td width="5%" align="center" class="no_print">Cancella</td> 
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idarea=$row1['idarea'];
while($row = mssql_fetch_assoc($rs))
{   

		$idallegato=$row['idallegato'];
		$type=$row['type'];
		$descrizione=$row['descrizione'];
		$edizione=$row['edizione'];
		$revisione=$row['revisione'];
		$data=formatta_data($row['datains'])." ".$row['orains'];
		$operatore=$row['operatore'];
		$file_name=$row['file_name'];
		$opeins=$row['opeins'];
		
		
		
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
				$alt_file="peg";
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
			default:
    			$tipo_file="application.png";
   		}	
		
	
		?>
		
		<tr> 
		 
		 <td align="center"><img src="images/icons/<?=$tipo_file?>" alt="<?=$alt_file?>" /><span class="hide"><?=$alt_file?></span></td> 
		 <!--<td><?=$data?></td>
		 <td><?=$operatore?></td>	
		<td><?=$file_name?></td>-->
		 <td><?=$edizione?></td>
		 <td><?=$revisione?></td>
		 <td><?=pulisci_lettura($descrizione)?></td>
		 <td align="center" class="no_print"><a href="#"><!--href="view_file.php?idfile=<?=$idallegato?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>&sec=allsgq"--><img src="images/view.png" onclick="javascript:prev('<?=$idallegato?>');" /></a></td>         
		 <? if ($opeins==$_SESSION['UTENTE']->get_userid())
		 {
		 ?>
		 <td align="center" class="no_print"><a onclick="javascript:load_content('div','lista_modelli_file_sgq.php?do=edit&id=<?=$idallegato?>');" href="#"><img src="images/gear.png" /></a></td>
		 <td align="center" class="no_print"><a href="#" title="elimina file" onclick="javascript:del_file('<?=$idallegato?>','<?=$file_name?>');"><image src="images/remove.png" title="elimina file"></a></td> 		  
		 <?
		 }
		 else
		 {
		 ?>
		 <td align="center" class="no_print">X</td> 
		 <td align="center" class="no_print">X</td> 		  
		 <?
		 }
		 ?>
		 
		</tr>
		
		
		<?
//}
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

function del_file(idfile,nomefile)
{
var answer = confirm ("Sei sicuro di voler eliminare il file "+nomefile+"?")
if (!answer)
return false;
else
{
	$.ajax({
		   type: "POST",
		   url: "lista_modelli_file_sgq_POST.php",
		   data: "action=del_file&idallegato="+idfile,
		   success: function(msg){ 
			load_content_menu('div','lista_modelli_file_sgq.php',this);
		   }
			});


}

}


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

function add(){

?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_modelli_file_sgq.php');" href="#">elenco modelli precompilati</a></div>
			<div class="elem_dispari"><a href="#">aggiungi modello precompilato</a></div>
          
        </div>

<div class="titolo_pag"><h1>Aggiungi modello</h1></div>

<form name="form0" action="lista_modelli_file_sgq_POST.php" method="post" id="myForm" enctype="multipart/form-data">
<div class="blocco_centralcat">
<div class="form">	
	
    <input type="hidden" name="action" value="add_files">
	
    <div class="riga">
    <input id="debug" type="hidden" value="1" name="debug" >
	<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">
 	<tbody id="tbody">
    <tr class="riga_tab nodrag nodrop">
		<td width="5%" align="left">&nbsp;</td>
		<td width="10%" align="left">edizione</td>
		<td width="10%" align="left">revisione</td>
		<td width="35%" align="left">descrizione</td>
		<td width="20%" align="center">file</td>			
		<td width="10%">&nbsp;</td>
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
	<td align="left" class="nodrag nodrop"><div id="div_ed<?=$i?>" class="nomandatory"><textarea name="edizione<?=$i?>" id="edizione<?=$i?>" class="scrittura_campo" cols="15" <?=$dis?>/></textarea></div></td>
	<td align="left" class="nodrag nodrop"><div id="div_re<?=$i?>" class="nomandatory"><textarea name="revisione<?=$i?>" id="revisione<?=$i?>" class="scrittura_campo" cols="15" <?=$dis?>/></textarea></div></td>
	<td align="left" class="nodrag nodrop"><div id="div_et<?=$i?>" class="mandatory"><textarea name="et<?=$i?>" id="et<?=$i?>" class="scrittura_campo" cols="30" <?=$dis?>/></textarea></div></td>
	<td align="left" class="nodrag nodrop"><div id="div_file<?=$i?>" class="mandatory controllo_file generica"><input type="file" name="ulfile<?=$i?>" <?=$dis?> id="ulfile<?=$i?>" /></div></td>
	<td>&nbsp;</td>	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a>
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
//footer_new();

}

function create()
{
	$conn = db_connect();
		$nome = stripslashes($_POST['nome']);
		$descr = stripslashes($_POST['descr']);
	
		$query = "INSERT INTO area (area,descrizione) VALUES('$nome','$descr')";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

	
	
	return true;


}

function edit($id)
{

	$conn = db_connect();

	$query = "SELECT * from sgq_allegati WHERE idallegato=$id";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$descrizione = pulisci_lettura($row['descrizione']);
		$edizione = pulisci_lettura($row['edizione']);
		$revisione = pulisci_lettura($row['revisione']);
		$file = $row['file_name'];		
	}

	// rimuove i caratteri di escape

	mssql_free_result($rs);
	?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_modelli_file_sgq.php');" href="#">elenco modelli precompilati</a></div>
			<div class="elem_dispari"><a href="#">aggiungi modello precompilato</a></div>
          
        </div>

<div class="titolo_pag"><h1>Edita modello</h1></div>

<form name="form0" action="lista_modelli_file_sgq_POST.php" method="post" id="myForm" enctype="multipart/form-data">
<div class="blocco_centralcat">
<div class="form">	
	
    <input type="hidden" name="action" value="update">
	<input type="hidden" name="id" value="<?=$id?>">
	
    <div class="riga">
    <input id="debug" type="hidden" value="1" name="debug" >
	<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">
 	<tbody id="tbody">
    <tr class="riga_tab nodrag nodrop">
		<td width="5%" align="left">&nbsp;</td>
		<td width="10%" align="left">edizione</td>
		<td width="10%" align="left">revisione</td>
		<td width="35%" align="left">descrizione</td>
		<td width="20%" align="center">file</td>			
		<td width="10%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
	<?
    $i=1;
    $numerofield=1;
    $style='';
    $class='riga_sottolineata_diversa';
    $dis="";
    while($i<=$numerofield)
    {   
    ?>
	
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<td align="center"><span class="id_notizia_cat"></span></td>
	<td align="left" class="nodrag nodrop"><div id="div_ed<?=$i?>" class="nomandatory"><textarea name="edizione<?=$i?>" id="edizione<?=$i?>" class="scrittura_campo" cols="15" <?=$dis?>><?=$edizione?></textarea></div></td>
	<td align="left" class="nodrag nodrop"><div id="div_re<?=$i?>" class="nomandatory"><textarea name="revisione<?=$i?>" id="revisione<?=$i?>" class="scrittura_campo" cols="15" <?=$dis?>><?=$revisione?></textarea></div></td>
	<td align="left" class="nodrag nodrop"><div id="div_et<?=$i?>" class="mandatory"><textarea name="et<?=$i?>" id="et<?=$i?>" class="scrittura_campo" cols="30" <?=$dis?>><?=$descrizione?></textarea></div></td>
	
	<td align="left" class="nodrag nodrop"><input type="hidden" name="cambia_mod" value="no">
	<a onclick="$('#cambia_mod').val('si');$('#div_file<?=$i?>').slideDown();$('#file_name').slideUp();" class="cambia_valore">cambia modello</a></div>                        
						<div id="file_name" style="display:block;">
						 <? if(strpos($file,".")!=""){?>
						<a href="view_file.php?idfile=<?=$id?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>&sec=allsgq" ><?=$file?></a>
						<?}?>
						</div>	
                       <div id="div_file<?=$i?>" class="nomandatory controllo_file generica" style="display:none;"><input type="file" name="ulfile<?=$i?>" <?=$dis?> id="ulfile<?=$i?>" /></div>
	
	</td>	
	<td>&nbsp;</td><td>&nbsp;</td>
	</tr>
<?
$i++;
}
?>
</tbody>
</table>      

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

