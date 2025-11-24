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
if (confirm('sei sicuro di voler cancellare la provincia corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_province_POST.php?action=del_provincia&id_provincia="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 reload_province();
			}
		 });	  
	  }
	else return false;
}
function reload_province(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_province.php";
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
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_province.php?do=add');">aggiungi una provincia</a></div>
		</div>
	</div>

<?
$query = "SELECT province.*, regioni.nome AS reg_nome, regioni.nome AS reg_id FROM dbo.province INNER JOIN
                      dbo.regioni ON dbo.province.idregione = dbo.regioni.id WHERE (sigla <>'EE') ORDER BY nome ASC";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco delle Province</h1>
	</div>
	<div class="info">Non esistono province.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th width="10%">id</th> 
	<th >nome</th>		
	<th width="10%">sigla</th>
	<th width="20%">regione</th>	
    <td width="5%">modifica</td>
	<td width="5%">cancella</td>		
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idnormativa=$row1['idnormativa'];

while($row = mssql_fetch_assoc($rs))
{   	
	$id_provincia=$row['id'];
	$nome=pulisci_lettura($row['nome']);
	$sigla=pulisci_lettura($row['sigla']);
	$regione = pulisci_lettura($row['reg_nome']);	
	?>	
	<tr> 
	 <td><?=$id_provincia?></td>  
	 <td><?=$nome?></td> 
	 <td><?=$sigla?></td>
	 <td><?=$regione?></td>	
	 <td><a onclick="javascript:load_content('div','lista_province.php?do=edit&id=<?=$id_provincia?>');" href="#"><img src="images/gear.png" /></a></td>
	<td><a onclick="conf_cancella('<?=$id_provincia?>');" href="#"><img src="images/remove.png" /></a></td>	
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
$conn = db_connect();
?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
			<div class="elem_dispari"><a href="#">aggiungi operatore</a></div>
          
        </div>

<form  method="post" name="operatori" action="lista_province_POST.php" id="myForm"  >
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>
	<input type="hidden" name="op" value="2" />


	<div class="titolo_pag"><h1>Aggiungi Provincia</h1></div>

		<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="nome" />
		</div>
	</span>
	

	<span class="rigo_mask">
		<div class="testo_mask">sigla</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo prov" name="sigla" SIZE="30" maxlength="2" />
		</div>
	</span>
	</div>
    
    <div class="riga"> 
	<span class="rigo_mask">
		<div class="testo_mask">regione</div>
		<div class="campo_mask mandatory">
			<select name="regione" class="scrittura" >
				<option value="0">selezionare</option>
				<?
				$query = "SELECT * FROM regioni order by nome ASC";	
				$rs = mssql_query($query, $conn);				
				while($row = mssql_fetch_assoc($rs)){
					$sel="";
					if ($regione==$row['id']) $sel="selected";				
					?>
					<option <?=$sel?> value="<?=$row['id']?>"><?=pulisci_lettura($row['nome'])?></option>
					<?
				 }
				 mssql_free_result($rs);
				 ?>				 
			</select>
		</div>
	</span>
	</div>
   </div>
   
	<div class="titolo_pag">
		<div class="comandi">
			<input id="salva_operatore" type="submit" title="salva" value="salva"  class="button_salva"/>
		</div>
	</div>
	
	</form>

</div>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				$(".prov").mask("(aa)");
	});	
</script>
<?
//footer_new();

}


function edit($id)
{
	$idoperatore=$id;
	$conn = db_connect();
	$query = "SELECT province.*, regioni.nome AS reg_nome, regioni.id AS reg_id FROM dbo.province INNER JOIN
                      dbo.regioni ON dbo.province.idregione = dbo.regioni.id WHERE (province.id=$id)";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
		
		$nome=pulisci_lettura($row['nome']);
		$sigla=pulisci_lettura($row['sigla']);
		$regione=$row['reg_id'];
		$reg_nome = pulisci_lettura($row['reg_nome']);		
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


<form  method="post" name="operatori" action="lista_province_POST.php" id="myForm"  >
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" id="id" value="<?=$id?>" />

	<div class="titolo_pag"><h1>Modifica Provincia</h1></div>

		<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="nome" value="<?php echo $nome ?>" />
		</div>
	</span>
	

	<span class="rigo_mask">
		<div class="testo_mask">sigla</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo prov" name="sigla" SIZE="30" maxlength="5" value="<?php echo $sigla ?>"/>
		</div>
	</span>
	</div>
    
    <div class="riga"> 
	<span class="rigo_mask">
		<div class="testo_mask">regione</div>
		<div class="campo_mask mandatory">
			<select name="regione" class="scrittura">
				<option value="0">selezionare</option>
				<?
				$query = "SELECT * FROM regioni order by nome ASC";	
				$rs = mssql_query($query, $conn);				
				while($row = mssql_fetch_assoc($rs)){
					$sel="";
					if ($regione==$row['id']) $sel="selected";				
					?>
					<option <?=$sel?> value="<?=$row['id']?>"><?=pulisci_lettura($row['nome'])?></option>
					<?
				 }
				 mssql_free_result($rs);
				 ?>				 
			</select>
		</div>
	</span>	
    </div> 
   </div>
   
	<div class="titolo_pag">
		<div class="comandi">
			<input id="salva_operatore" type="submit" title="salva" value="salva"  class="button_salva"/>
		</div>
	</div>
	
	</form>
		
</div>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				$(".prov").mask("(aa)");
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

