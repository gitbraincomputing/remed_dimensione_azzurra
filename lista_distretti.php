<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 71;
$tablename = 'distretti';
include_once('include/function_page.php');

function show_list()
{

$conn = db_connect();

?>

<script>

function conf_cancella(IdDistretto){
//alert(IdDistretto);
if (confirm('sei sicuro di voler cancellare il distretto corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_distretti_POST.php?action=del_distretto&IdDistretto="+IdDistretto,
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
	pagina_da_caricare="lista_distretti.php";
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
            <div class="elem_pari"><a href="#">elenco distretti</a></div>
       
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_distretti.php?do=add');">aggiungi distretto</a></div>
		</div>
	</div>
<?
$query = "SELECT * from distretti order by CodiceDistretto asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco DIstretti</h1>
	</div>
	<div class="info">Non esistono distretti.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th>id</th> 
	<th>codice ASL</th> 
    <th>descrizione ASL</th>
	<td>codice distretto</td>
	<td>desrizione distretto</td>  
	<td width="5%">Modifica</td>
	<td width="5%">Cancella</td>
</tr> 
</thead> 
<tbody> 
<?
while($row = mssql_fetch_assoc($rs)){   

		$IdDistretto=$row['IdDistretto'];
		$CodiceASL=pulisci_lettura($row['CodiceASL']);
		$DescrizioneAsl=pulisci_lettura($row['DescrizioneAsl']);
		$CodiceDistretto=pulisci_lettura($row['CodiceDistretto']);
		$DescrizioneDistretto=pulisci_lettura($row['DescrizioneDistretto']);
	
		?>		
		<tr> 
		 <td><?=$IdDistretto?></td> 
		 <td><?=$CodiceASL?></td> 
		 <td><?=$DescrizioneAsl?></td> 
		 <td><?=$CodiceDistretto?></td> 
		 <td><?=$DescrizioneDistretto?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_distretti.php?do=edit&id=<?=$IdDistretto?>');" href="#"><img src="images/gear.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="conf_cancella('<?=$IdDistretto?>');" href="#"><img src="images/remove.png" /></a></td>
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

?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_distretti.php');" href="#">elenco distretti</a></div>
			<div class="elem_dispari"><a href="#">aggiungi distretto</a></div>
          
        </div>

<form  method="post" name="form0" action="lista_distretti_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Distretto</h1></div>

	<div class="blocco_centralcat">

        <div class="riga">
            <div class="rigo_mask">
                <div class="testo_mask">Codice ASL</div>
                <div class="campo_mask mandatory">
                    <input type="text" name="CodiceASL" class="scrittura" size="50"/>
                </div>
            </div>
        </div>   
        <div class="riga">
            <div class="rigo_mask">
                <div class="testo_mask">Descrizione ASL</div>
                <div class="campo_mask mandatory">
                    <input type="text" name="DescrizioneAsl" class="scrittura" size="50"/>
                </div>
            </div>
        </div>   
        <div class="riga">
            <div class="rigo_mask">
                <div class="testo_mask">Codice distretto</div>
                <div class="campo_mask mandatory">
                    <input type="text" name="CodiceDistretto" class="scrittura" size="50"/>
                </div>
            </div>
        </div>        
        <div class="riga">
            <div class="rigo_mask">
                <div class="testo_mask">Descrizione distretto</div>
                <div class="campo_mask mandatory">
                    <input type="text" name="DescrizioneDistretto" class="scrittura" size="50"/>
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

	$query = "SELECT * from distretti WHERE IdDistretto=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
		$CodiceASL = pulisci_lettura($row['CodiceASL']);
		$DescrizioneAsl = pulisci_lettura($row['DescrizioneAsl']);
		$CodiceDistretto = pulisci_lettura($row['CodiceDistretto']);
		$DescrizioneDistretto = pulisci_lettura($row['DescrizioneDistretto']);
		$obsoleto=$row['obsoleto'];
	}
	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>
		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_distretti.php');" href="#">elenco distretti</a></div>
			 <div class="elem_dispari"><a href="#">modifica distretto</a></div>
          
        </div>
<form  method="post" name="form0" action="lista_distretti_POST.php" id="myForm">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?php echo $id ?>" />

	<div class="titolo_pag"><h1>Modifica Distretto</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">Codice ASL</div>
			<div class="campo_mask mandatory">
				<input type="text" name="CodiceASL" class="scrittura" value="<?=$CodiceASL?>"/>
			</div>
		</div>
    </div>    
    <div class="riga">
        <div class="rigo_mask">
			<div class="testo_mask">Descrizione ASL</div>
			<div class="campo_mask mandatory">
				<input type="text" name="DescrizioneAsl" class="scrittura" value="<?=$DescrizioneAsl?>"/>
			</div>
		</div>

	</div>
    <div class="riga">
        <div class="rigo_mask">
			<div class="testo_mask">Codice Distretto</div>
			<div class="campo_mask mandatory">
				<input type="text" name="CodiceDistretto" class="scrittura" value="<?=$CodiceDistretto?>"/>
			</div>
		</div>
    </div>	
    <div class="riga">
        <div class="rigo_mask">
			<div class="testo_mask">Descrizione Distretto</div>
			<div class="campo_mask mandatory">
				<input type="text" name="DescrizioneDistretto" class="scrittura" value="<?=$DescrizioneDistretto?>"/>
			</div>
		</div>
	</div>
	<div class="riga">
        <div class="rigo_mask">
			<div class="testo_mask">Obsoleto <em>(valido solo per nuovi inserimenti)</em></div>
			<div class="campo_mask ">
				<input type="checkbox" name="obsoleto" class="scrittura" <?if($obsoleto==-1) echo("checked");?>/>
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

