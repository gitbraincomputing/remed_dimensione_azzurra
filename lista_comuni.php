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
if (confirm('sei sicuro di voler cancellare il comune corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_comuni_POST.php?action=del_comune&id_comune="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 reload_comuni();
			}
		 });	  
	  }
	else return false;
}
function reload_comuni(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_comuni.php";
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
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_comuni.php?do=add');">aggiungi un comune</a></div>
		</div>
	</div>

<?
$query = "SELECT dbo.regioni.nome as reg_nome, dbo.comuni.* FROM dbo.comuni INNER JOIN dbo.regioni ON dbo.comuni.regione = dbo.regioni.id WHERE (dbo.comuni.nazione='ITA') ORDER BY dbo.comuni.denominazione ASC";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco dei Comuni</h1>
	</div>
	<div class="info">Non esistono comuni.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th width="10%">id</th> 
	<th >comune</th>		
	<th width="10%">sigla prov</th>
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
	$id_comune=$row['id_comune'];
	$comune=pulisci_lettura($row['denominazione']);
	$sigla=trim(pulisci_lettura($row['sigla']));
	$regione = pulisci_lettura($row['reg_nome']);	
	?>	
	<tr> 
	 <td><?=$id_comune?></td>  
	 <td><?=$comune?></td> 
	 <td><?=$sigla?></td>
	 <td><?=$regione?></td>	
	 <td><a onclick="javascript:load_content('div','lista_comuni.php?do=edit&id=<?=$id_comune?>');" href="#"><img src="images/gear.png" /></a></td>
	<td><a onclick="conf_cancella('<?=$id_comune?>');" href="#"><img src="images/remove.png" /></a></td>	
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
<script>inizializza();
function get_province(id){
  $.ajax({
	   type: "POST",
	   url: "ajax_province.php?id_regione="+id,
	   data: "",
	   success: function(msg){
		$("#province").html($.trim(msg));
		}
	 });
}
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
			<div class="elem_dispari"><a href="#">aggiungi operatore</a></div>
          
        </div>

<form  method="post" name="operatori" action="lista_comuni_POST.php" id="myForm"  >
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>
	<input type="hidden" name="op" value="2" />


	<div class="titolo_pag"><h1>Aggiungi Comune</h1></div>

		<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="comune" />
		</div>
	</span>
	

	<span class="rigo_mask">
		<div class="testo_mask">cap</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="cap" SIZE="30" maxlength="5" />
		</div>
	</span>
	</div>
    
    <div class="riga"> 
	<span class="rigo_mask">
		<div class="testo_mask">regione</div>
		<div class="campo_mask mandatory">
			<select name="regione" class="scrittura" onchange="get_province(this.value);">
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
	<span class="rigo_mask">
		<div class="testo_mask">provincia</div>
		<div id="province" class="campo_mask mandatory">
			<select name="provincia" class="scrittura">
				<option value="0">selezionare</option>
				<?
				$query = "SELECT * FROM province Where (sigla<>'(EE)') order by nome ASC";	
				$rs = mssql_query($query, $conn);				
				while($row = mssql_fetch_assoc($rs)){
					$sel="";
					if ($provincia==$row['id']) $sel="selected";				
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
	
   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">codice istat</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="istat" />
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">codice comune per CF</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="com_cod_fisc" SIZE="30" />
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">codice ASL</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="codiceasl" SIZE="30" />
		</div>
	</span>
	</div>

    <div class="riga">
    <span class="rigo_mask">
		<div class="testo_mask">codice regione</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="codiceregione" />
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
		
	});	
</script>
<?
//footer_new();

}


function edit($id)
{
	$idoperatore=$id;
	$conn = db_connect();
	$query = "SELECT * FROM re_comuni_province WHERE (id_comune=$id)";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
		
		$comune=pulisci_lettura($row['denominazione']);
		$sigla=pulisci_lettura($row['sigla']);
		$regione=$row['regione'];
		$reg_nome = pulisci_lettura($row['reg_nome']);
		$provincia=$row['provincia'];
		$prov_nome = pulisci_lettura($row['prov_nome']);
		$istat=$row['istat'];
		$cap=$row['cap'];
		$com_cod_fisc=$row['com_cod_fisc'];
		$codiceasl=$row['codiceasl'];
		$codiceregione=$row['codiceregione'];
		
	}
	mssql_free_result($rs);
	// rimuove i caratteri di escape		
	?>
	<script>inizializza();
	function get_province(id){
  $.ajax({
	   type: "POST",
	   url: "ajax_province.php?id_regione="+id,
	   data: "",
	   success: function(msg){
		$("#province").html($.trim(msg));
		}
	 });
}
</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
			 <div class="elem_dispari"><a href="#">modifica operatore</a></div>
          
        </div>


<form  method="post" name="operatori" action="lista_comuni_POST.php" id="myForm"  >
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" id="id" value="<?=$id?>" />

	<div class="titolo_pag"><h1>Modifica Comune</h1></div>

		<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="comune" value="<?php echo $comune ?>" />
		</div>
	</span>
	

	<span class="rigo_mask">
		<div class="testo_mask">cap</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="cap" SIZE="30" maxlength="5" value="<?php echo $cap ?>"/>
		</div>
	</span>
	</div>
    
    <div class="riga"> 
	<span class="rigo_mask">
		<div class="testo_mask">regione</div>
		<div class="campo_mask mandatory">
			<select name="regione" class="scrittura" onchange="get_province(this.value);">
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
	<span class="rigo_mask">
		<div class="testo_mask">provincia</div>
		<div class="campo_mask mandatory">
			<select id="province" name="provincia" class="scrittura">
				<option value="0">selezionare</option>
				<?
				$query = "SELECT * FROM province Where (sigla<>'(EE)') order by nome ASC";	
				$rs = mssql_query($query, $conn);				
				while($row = mssql_fetch_assoc($rs)){
					$sel="";
					if ($provincia==$row['id']) $sel="selected";				
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
	
   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">codice istat</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="istat" value="<?php echo $istat ?>" />
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">codice comune per CF</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="com_cod_fisc" SIZE="30" value="<?php echo $com_cod_fisc ?>"/>
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">codice ASL</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="codiceasl" SIZE="30" value="<?php echo $codiceasl ?>"/>
		</div>
	</span>
	</div>

    <div class="riga">
    <span class="rigo_mask">
		<div class="testo_mask">codice regione</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="codiceregione" value="<?php echo $codiceregione ?>" />
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

