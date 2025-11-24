<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 68;
$tablename = 'normative';
include_once('include/function_page.php');

function show_list()
{

$conn = db_connect();

?>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione permessi</a></div>
            <div class="elem_pari"><a href="#">elenco operatori</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_medici_generali.php?do=add');">aggiungi medico di medicina generale</a></div>
		</div>
	</div>

<?
$query = "SELECT * FROM re_medici_medicina_generale WHERE ((re_medici_medicina_generale.cancella = 'n'))ORDER BY re_medici_medicina_generale.cognome";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Medici di Medicina Generale</h1>
	</div>
	<div class="info">Non esistono medici di medicina generale.</div>	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th width="10%">id</th>
	<th width="10%">stato</th>
	<th width="40%">nome e cognome</th>	
	<th width="20%">comune</th>	
	<td width="10%">modifica</td>
	<td width="10%">elimina</td>	
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idnormativa=$row1['idnormativa'];

while($row = mssql_fetch_assoc($rs))
{   	
	$id_med=$row['id'];
	$nome=pulisci_lettura($row['cognome']." ".$row['nome']);
	$comune=pulisci_lettura($row['nome_comune']." ".$row['sigla']);
	$status = $row['stato'];
	if($status){
	$stato="stato_ok.png";
	$alt_s="attivo";}
	else{
	$stato="stato_blu.png";
	$alt_s="disattivo";}
	?>	
	<tr> 
	 <td><?=$id_med?></td>
	 <td align="center" width="10%"><img src="images/<?=$stato?>" title="<?=$alt_s?>"/></td> 	  
	 <td><?=$nome?></td>	
	 <td><?=$comune?></td> 	 
	 <td><a id="<?=$id_med?>" onclick="javascript:load_content('div','lista_medici_generali.php?do=edit&id=<?=$id_med?>');" href="#"><img src="images/gear.png" /></a></td>
	 <td><a id="<?=$id_med?>" onclick="javascript:conf_cancella('<?=$id_med?>');" href="#"><img src="images/remove.png" /></a></td>	
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

function conf_cancella(id_med){
if (confirm('sei sicuro di voler cancellare il medico corrente?')){
	//document.getElementById('id_medico').value=id_al;
	  $.ajax({
		   type: "POST",
		   url: "lista_medici_generali_POST.php?action=del_medico&id="+id_med,
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
	//$('#lista_impegnative').innerHTML="";
	pagina_da_caricare="lista_medici_generali.php?do=";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
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

<form  method="post" name="form0" action="lista_medici_generali_POST.php" id="myForm" >
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Medico di Medicina Generale</h1></div>

	<div class="blocco_centralcat">

	<div class="riga"> 
	<span class="rigo_mask">    
		<div class="testo_mask">cognome</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura_campo solo_lettere" name="cognome_m" SIZE="30" maxlenght="30" value="<?php echo($med_cognome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura_campo solo_lettere" name="nome_m" SIZE="30" maxlenght="30" value="<?php echo($med_nome);?>"/>
		</div>
	</span>
    
	<span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura" name="indirizzo_m" SIZE="30" maxlenght="30" value="<?php echo($med_indirizzo);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">provincia di residenza <a onclick="carica_province(document.getElementById('med_residenza').value,5)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask nomandatory" id="province_med">
			<input type="text" class="scrittura readonly" name="med_residenza" id="med_residenza" SIZE="30" maxlenght="30" value="<?php echo($med_provincia);?>" readonly/>            
		</div>	
		<script type="text/javascript">
		carica_province(document.getElementById('med_residenza').value,5);
		</script>
	</span>
	<span class="rigo_mask">
		<div class="testo_mask">Comune di residenza <a onclick="carica_comuni(document.getElementById('med_residenza').value,5,document.getElementById('med_comune_residenza').value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask nomandatory" id="comuni_med">
         <input type="text" class="scrittura readonly" name="med_comune_residenza_nome" SIZE="30" maxlenght="30" value="<?php echo($med_comune);?>" readonly/>
		  <input type="hidden" name="med_comune_residenza" id="med_comune_residenza" value="<?=$med_comune_id?>"/>
              
		</div>
	</span>
	
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask" id="cap_med">
			<input type="text" class="scrittura" name="med_cap_residenza" SIZE="30" maxlenght="30" value="<?php echo($med_cap);?>"readonly/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="med_telefono" SIZE="30" maxlenght="30" value="<?php echo($med_telefono);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" name="med_fax" SIZE="30" maxlenght="30" value="<?php echo($med_fax);?>"/>
		</div>
	</span>	
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask ">
			<input id="cell" type="text" class="scrittura" name="med_cellulare" SIZE="30" maxlenght="30" value="<?php echo($med_cellulare);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask email">
			<input type="text" class="scrittura" name="med_email" SIZE="30" maxlenght="30" value="<?php echo($med_email);?>"/>
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
				$(".solo_lettere").validation({ 
					type: "alpha",
					add:"' "	
				}); 	
		
	});
</script>
<?
//footer_new();

}


function edit($id)
{
	$idoperatore=$id;
	$conn = db_connect();
	$query = "SELECT * FROM re_medici_medicina_generale WHERE (re_medici_medicina_generale.id = $id)";
	//echo($query);
	$rs = mssql_query($query, $conn);	
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$cognome = pulisci_lettura($row['cognome']);
		$nome = pulisci_lettura($row['nome']);
		$indirizzo= pulisci_lettura($row['indirizzo']);
		$nome_prov=pulisci_lettura($row['nome_provincia']);
		$id_prov=$row['id_provincia'];
		$nome_com=pulisci_lettura($row['nome_comune']);
		$id_com=$row['id_comune'];
		$cap=trim($row['cap']);
		$telefono=trim($row['telefono']);
		$fax=trim($row['fax']);
		$cellulare=trim($row['cellulare']);
		$email=trim($row['email']);
		$status = $row['stato'];
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


<form  method="post" name="operatori" action="lista_medici_generali_POST.php" id="myForm"  >
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" id="id" value="<?=$id?>" />

	<div class="titolo_pag"><h1>Modifica Medico di Medicina Generale</h1></div>

		<div class="blocco_centralcat">

   <div class="riga"> 
	<span class="rigo_mask">    
		<div class="testo_mask">cognome</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura_campo solo_lettere" name="cognome_m" SIZE="30" maxlenght="30" value="<?php echo($cognome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura_campo solo_lettere" name="nome_m" SIZE="30" maxlenght="30" value="<?php echo($nome);?>"/>
		</div>
	</span>
    
	<span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura" name="indirizzo_m" SIZE="30" maxlenght="30" value="<?php echo($indirizzo);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">provincia di residenza <a onclick="carica_province(document.getElementById('med_residenza').value,5)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask nomandatory" id="province_med">
			<input type="text" class="scrittura readonly" name="med_residenza" id="med_residenza" SIZE="30" maxlength="30" value="<?php echo($nome_prov);?>" readonly/>
            
		</div>
	</span>
	<span class="rigo_mask">
		<div class="testo_mask">Comune di residenza <a onclick="carica_comuni(document.getElementById('med_residenza').value,5,document.getElementById('med_comune_residenza').value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask nomandatory" id="comuni_med">
         <input type="text" class="scrittura readonly" name="med_comune_residenza_nome" SIZE="30" maxlenght="30" value="<?php echo($nome_com);?>" readonly/>
		  <input type="hidden" name="med_comune_residenza" id="med_comune_residenza" value="<?=$id_com?>"/>              
		</div>
	</span>
	
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask" id="cap_med">
			<input type="text" class="scrittura" name="med_cap_residenza" SIZE="30" maxlenght="30" value="<?php echo($cap);?>" />
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="med_telefono" SIZE="30" maxlenght="30" value="<?php echo($telefono);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura" name="med_fax" SIZE="30" maxlenght="30" value="<?php echo($fax);?>"/>
		</div>
	</span>	
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask ">
			<input id="cell" type="text" class="scrittura" name="med_cellulare" SIZE="30" maxlenght="30" value="<?php echo($cellulare);?>"/>
		</div>
	</span>
	</div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask email">
			<input type="text" class="scrittura" name="med_email" SIZE="30" maxlenght="30" value="<?php echo($email);?>"/>
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
				$(".solo_lettere").validation({ 
					type: "alpha",
					add:"' "	
				}); 	
		
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

