<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 72;
$tablename = 'menomazioni';
include_once('include/function_page.php');

function show_list()
{

//$nome_pagina="gestione normative";
//header_new($nome_pagina);
//barra_top_new();
//top_message();
//menu_new();
//pagina_new();

$conn = db_connect();

//$query = "SELECT * from normativa where (stato=1) and (cancella='n')";	
//$rs1 = mssql_query($query, $conn);

//if(!$rs1) error_message(mssql_error());


?>

<script>

function conf_cancella(idmen){
if (confirm('sei sicuro di voler cancellare la menomazione corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_menomazioni_POST.php?action=del_menomazione&idmen="+idmen,
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
	pagina_da_caricare="lista_menomazioni.php";
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
            <div class="elem_pari"><a href="#">elenco menomazioni</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_menomazioni.php?do=add');">aggiungi menomazioni</a></div>
		</div>
	</div>
<?
$query = "SELECT * from menomazioni order by codice asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);

if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Menomazioni</h1>
	</div>
	<div class="info">Non esistono menomazioni.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th>idMen</th> 
	<th>Codice</th> 
    <th>Descrizione</th>
    <td width="5%">Modifica</td>
	<td width="5%">Cancella</td>
	</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idnormativa=$row1['idnormativa'];
while($row = mssql_fetch_assoc($rs))
{   

		$idmen=$row['idmen'];
		$codice=$row['codice'];
		$descrizione=pulisci_lettura($row['descrizione']);
		$codice=pulisci_lettura($codice);
		//$codice=pulisci($codice);
			
		?>
		
		<tr> 
		 <td><?=$idmen?></td> 
		 <td><?=$codice?></td> 
		 <td><?=$descrizione?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_menomazioni.php?do=edit&id=<?=$idmen?>');" href="#"><img src="images/gear.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="conf_cancella('<?=$idmen?>');" href="#"><img src="images/remove.png" /></a></td>
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
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_menomazioni.php');" href="#">elenco menomazioni</a></div>
			<div class="elem_dispari"><a href="#">aggiungi menomazioni</a></div>
          
        </div>

<form  method="post" name="form0" action="lista_menomazioni_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi menomazioni</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">codice</div>
			<div class="campo_mask mandatory">
				<input type="text" name="codice" class="scrittura" size="50"/>
			</div>
		</div>
    </div>    
    <div class="riga">
        <div class="rigo_mask rigo_big">
			<div class="testo_mask">descrizione</div>
			<div class="campo_mask mandatory">
				<textarea name="descrizione" class="scrittura_campo" size="50"></textarea>
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
//footer_new();

}


function edit($id)
{

	$conn = db_connect();

	$query = "SELECT * from menomazioni WHERE idmen=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$codice = $row['codice'];
		$descrizione = $row['descrizione'];
		
	}

	// rimuove i caratteri di escape
	$codice = stripslashes($codice);
	$descrizione = stripslashes($descrizione);
	$codice=pulisci_lettura($codice);
	$descrizione=pulisci_lettura($descrizione);

	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_menomazioni.php');" href="#">elenco menomazioni</a></div>
			 <div class="elem_dispari"><a href="#">modifica menomazioni</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_menomazioni_POST.php" id="myForm">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?php echo $id ?>" />

	<div class="titolo_pag"><h1>Modifica Menomazioni</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">menomazioni</div>
			<div class="campo_mask mandatory">
				<input type="text" name="codice" class="scrittura" value="<?=$codice?>"/>
			</div>
		</div>
    </div>    
    <div class="riga">
        <div class="rigo_mask rigo_big">
			<div class="testo_mask">descrizione</div>
			<div class="campo_mask mandatory">
				<textarea name="descr" class="scrittura_campo"><?=$descrizione?></textarea>
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




if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		

		
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

