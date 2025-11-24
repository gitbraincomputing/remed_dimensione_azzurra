<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 43;
$tablename = 'normative';
include_once('include/function_page.php');

function show_list()
{

$conn = db_connect();

?>
<script>
function conf_cancella(id){
if (confirm('sei sicuro di voler cancellare il medico prescrittore corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_medici_prescrittori_POST.php?action=del_medico&id_medico="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 reload_medici();
			}
		 });	  
	  }
	else return false;
}
function reload_medici(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_medici_prescrittori.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione permessi</a></div>
            <div class="elem_pari"><a href="#">elenco operatori</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_medici_prescrittori.php?do=add');">aggiungi medico prescrittore</a></div>
		</div>
	</div>

<?
$query = "SELECT * FROM dbo.prescrittori WHERE (dbo.prescrittori.cancella = 'n') ORDER BY dbo.prescrittori.NominativoPrescrittore";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Medici Prescrittori</h1>
	</div>
	<div class="info">Non esistono medici prescrittori.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th width="10%">id</th> 
	<th width="10%">stato</th>	
	<th width="10%">codice prescrittore</th>
	<th width="10%">codice induttore</th>
	<th width="30%">cognome e nome</th>
    <td width="10%">modifica</td>
	<td width="10%">cancella</td>	
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idnormativa=$row1['idnormativa'];

while($row = mssql_fetch_assoc($rs))
{   	
	$id_ope=$row['IdPrescrittore'];
	$nome=$row['NominativoPrescrittore'];
	$codice=$row['Codice_asl'];
	$codice_ind=$row['codice_induttore'];
	$status = $row['status'];
	if($status){
	$stato="stato_ok.png";
	$alt_s="attivo";}
	else{
	$stato="stato_blu.png";
	$alt_s="disattivo";}
	// rimuove i caratteri di escape
	$nome = stripslashes($nome);
	$codice = stripslashes($codice);	
	?>
	
	<tr> 
	 <td><?=$id_ope?></td>
	 <td align="center" width="10%"><img src="images/<?=$stato?>" title="<?=$alt_s?>"/></td> 	  
	 <td><?=$codice?></td> 
	 <td><?=$codice_ind?></td> 
	 <td><?=$nome?></td>	
	 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_medici_prescrittori.php?do=edit&id=<?=$id_ope?>');" href="#"><img src="images/gear.png" /></a></td> 	 
	<td><a id="<?=$id?>" onclick="conf_cancella('<?=$id_ope?>');" href="#"><img src="images/remove.png" /></a></td> 	
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

<?
}

function add()
{

?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
			<div class="elem_dispari"><a href="#">aggiungi operatore</a></div>
          
        </div>

<form  method="post" name="form0" action="lista_medici_prescrittori_POST.php" id="myForm" >
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Medico Prescrittore</h1></div>

	<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">cognome e nome</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="nome" SIZE="30" maxlenght="30" />
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">codice prescrittore</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="codice_asl" SIZE="30" maxlenght="30" />
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">codice induttore</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="codice_induttore" SIZE="30" maxlenght="30" />
		</div>
	</span>
	</div>
    
    <div class="riga">
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">breve descrizione</div>
		<div class="campo_mask">
			<textarea name="descr" class="scrittura" rows="4" cols="40"></textarea>
		</div>
	</span>
    </div>

   
    <div class="riga">        
    <span class="rigo_mask">
        <div class="testo_mask">stato</div>
        <div class="campo_mask">
                <select name="status" class="scrittura">
                <option value="1">attivo</option>
                <option value="0">disattivo</option>
                </select>
        </div>
    </span>     
   </div>  
   
	</div>

	<div class="titolo_pag">
		<div class="comandi">
			<input id="salva_operatore" type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	
	</form>
    
		
		
</div>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>
<?
//footer_new();

}


function edit($id)
{
	$idoperatore=$id;
	$conn = db_connect();
	$query = "SELECT * FROM dbo.prescrittori WHERE (dbo.prescrittori.IdPrescrittore=$id)";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$nome = $row['NominativoPrescrittore'];
		$descr = $row['descr'];
		$codice_asl = $row['Codice_asl'];	
		$codice_ind = $row['codice_induttore'];			
		$status = $row['status'];
	}
	mssql_free_result($rs);
	// rimuove i caratteri di escape		
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
			 <div class="elem_dispari"><a href="#">modifica operatore</a></div>
          
        </div>


<form  method="post" name="operatori" action="lista_medici_prescrittori_POST.php" id="myForm"  >
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" id="id" value="<?=$id?>" />

	<div class="titolo_pag"><h1>Modifica Medico Prescrittore</h1></div>

		<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">cognome e nome</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="nome" value="<?php echo $nome ?>" />
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">codice </div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="codice_asl" SIZE="30" maxlenght="30" value="<?php echo $codice_asl ?>"/>
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">codice </div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="codice_induttore" SIZE="30" maxlenght="30" value="<?php echo $codice_ind ?>"/>
		</div>
	</span>
	</div>
    
    <div class="riga"> 
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">breve descrizione</div>
		<div class="campo_mask">
			<textarea name="descr" class="scrittura" rows="4" cols="40"><?php echo $descr ?></textarea>
		</div>
	</span>
    </div>

    <div class="riga">
    <span class="rigo_mask">
        <div class="testo_mask">stato</div>
        <div class="campo_mask">
                <select name="status" class="scrittura">
                <option value="1" <?php if($status) echo "selected"; ?>>attivo</option>
				<option value="0" <?php if(!$status) echo "selected"; ?>>disattivo</option>
                </select>
        </div>
    </span>     
   </div>  
   </div>
   
	<div class="titolo_pag">
		<div class="comandi">
			<input onclick="javascript: return controlla_campi_operatori('edit');" id="salva_operatore" type="submit" title="salva" value="salva"  class="button_salva"/>
		</div>
	</div>
	
	</form>
    
		
		
</div>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
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

