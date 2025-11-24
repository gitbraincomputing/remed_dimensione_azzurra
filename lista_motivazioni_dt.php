<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 66;
$tablename = 'tbl_motivo_trattamento';
include_once('include/function_page.php');

function show_list(){
global $tablename;

$conn = db_connect();


?>
<script>
function conf_cancella(id){
if (confirm('sei sicuro di voler cancellare la motivazione corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_motivazioni_dt_POST.php?action=del_motivazione&id_motivazione="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 reload_motivazioni();
			}
		 });	  
	  }
	else return false;
}
function reload_motivazioni(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_motivazioni_dt.php";
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
            <div class="elem_pari"><a href="#">elenco normative</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_motivazioni_dt.php?do=add');">aggiungi motivazione trattamento
			</a></div>
		</div>
	</div>
<?
$query = "SELECT * from $tablename order by motivazione asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Motivazioni trattamento</h1>
	</div>
	<div class="info">Non esistono motivazioni trattamento.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th>id</th> 
	<th>motivazione</th> 
    <th>descrizione</th>
	<td>modifica</td>
	<td>cancella</td>	
</tr> 
</thead> 
<tbody> 
<?
while($row = mssql_fetch_assoc($rs))
{  		$id=$row['id'];
		$motivazione=pulisci_lettura($row['motivazione']);
		$descrizione=pulisci_lettura($row['descrizione']);	
		?>
		<tr> 
		 <td><?=$id?></td> 
		 <td><?=$motivazione?></td> 
		 <td><?=$descrizione?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_motivazioni_dt.php?do=edit&id=<?=$id?>');" href="#"><img src="images/gear.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="conf_cancella('<?=$id?>');" href="#"><img src="images/remove.png" /></a></td> 
		</tr>
		<?
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
}

function add(){
global $tablename;
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
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_motivazioni_dt.php');" href="#">elenco motivazioni trattamento</a></div>
			<div class="elem_dispari"><a href="#">aggiungi motivazioni</a></div>
          
        </div>

<form  method="post" name="form0" action="lista_motivazioni_dt_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Motivazioni Trattamento</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">motivazione</div>
			<div class="campo_mask mandatory">
				<input type="text" name="nome" class="scrittura" size="50"/>
			</div>
		</div>
    </div>    
    <div class="riga">
        <div class="rigo_mask rigo_big">
			<div class="testo_mask">descrizione</div>
			<div class="campo_mask mandatory">
				<textarea name="descr" class="scrittura_campo" size="50"></textarea>
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

function create(){
global $tablename;
	$conn = db_connect();
	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);

	$query = "INSERT INTO $tablename (motivazione,descrizione) VALUES('$nome','$descr')";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	return true;
}

function edit($id){
	global $tablename;
	$conn = db_connect();

	$query = "SELECT * from $tablename WHERE id=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$motivazione = pulisci_lettura($row['motivazione']);
		$descrizione = pulisci_lettura($row['descrizione']);
		
	}
	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

	<div id="briciola" style="margin-bottom:20px;">
		<div class="elem0"><a href="principale.php">home</a></div>
		<div class="elem_pari"><a onclick="javascript:load_content('div','lista_motivazioni.php');" href="#">elenco motivazioni trattamento</a></div>
		 <div class="elem_dispari"><a href="#">modifica motivazioni trattamento</a></div>          
	</div>


<form  method="post" name="form0" action="lista_motivazioni_dt_POST.php" id="myForm">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?php echo $id ?>" />

	<div class="titolo_pag"><h1>Modifica Motivazione</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">Motivazione</div>
			<div class="campo_mask mandatory">
				<input type="text" name="nome" class="scrittura" value="<?=$motivazione?>"/>
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
<? }




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

