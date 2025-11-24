<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 57;
$tablename = 'regime';
include_once('include/function_page.php');

function show_list()
{

//$nome_pagina="gestione normative";
//header_new($nome_pagina);
//barra_top_new();
//top_message();
//menu_new();
//pagina_new();

//$conn = db_connect();

//$query = "SELECT * from regime where (stato=1) and (cancella='n')order by idregime desc";	
//$rs1 = mssql_query($query, $conn);

//if(!$rs1) error_message(mssql_error());

//$conta=mssql_num_rows($rs1);
?>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione Compilatore</a></div>
            <div class="elem_pari"><a href="#">elenco contenuti per compilatore</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_contenuti_compilatore.php?do=add');">aggiungi contenuto</a></div>
		</div>
	</div>

<?
$conn = db_connect();

$query = "select * from re_compilatore_contenuti order by codice_protocollo asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);

if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Contenuti</h1>
	</div>
	<div class="info">Non esistiono Contenuti.</div>
	
	
	<?
	exit();}?>
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th>codice protocollo</th> 
	<th>Protocollo</th> 
	<th>etichetta</th> 
	<td>modifica</td>
	<!--<td>contenuto</td> -->
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{

while($row = mssql_fetch_assoc($rs))
{   

		$id=$row['idcontenuto'];
		$codice_protocollo=$row['codice_protocollo'];
		$descrizione_p=$row['descrizione'];
		$etichetta=$row['etichetta'];
		$contenuto=$row['contenuto'];
		?>
		
		<tr> 
		 <td><?=$codice_protocollo?></td> 
		 <td><?=$descrizione_p?></td> 
		  <td><?=$etichetta?></td> 
		<!-- <td><?=$contenuto?></td> -->
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_contenuti_compilatore.php?do=edit&id=<?=$id?>');" href="#"><img src="images/gear.png" /></a></td> 
		 
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
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_contenuti_protocolli.php');" href="#">elenco contenuti</a></div>
			<div class="elem_dispari"><a href="#">aggiungi contenuto</a></div>
          
        </div>

<form  method="post" name="form0" action="lista_contenuti_compilatore_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Contenuto</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">Protocollo</div>
			<div class="campo_mask mandatory">
				<select id="idprotocollo" name="idprotocollo" >
				<option value="">seleziona</option>
				<?
				$conn=db_connect();
					$query="select * from protocolli order by codice_protocollo";
					$rs2 = mssql_query($query, $conn);
					if(!$rs2) error_message(mssql_error());
						while($row2= mssql_fetch_assoc($rs2)){
						$idprotocollo=$row2['idprotocollo'];
						$codice_p=$row2['codice_protocollo'];
						$descrizione_p=$row2['descrizione'];
						?>
						<option value="<?=$idprotocollo?>"><?=$codice_p." - ".$descrizione_p?></option>
						<?
						}
				
				?>
				</select>
			</div>
		</div>
    </div>    
	
	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">etichetta</div>
			<div class="campo_mask mandatory">
				<input type="text" name="etichetta" class="scrittura" size="50"/>
			</div>
		</div>
    </div>    
	
	 <div class="riga">
                <div class="rigo_mask rigo_big">
                    <div class="testo_mask">contenuto</div>
                    <div class="campo_mask mandatory">
                        <textarea name="contenuto" id="contenuto" class="scrittura_campo"></textarea>
                    </div>
                </div>
				
				 <div class="rigo_mask">
                    <div class="testo_mask">variabili utilizzabili</div>
                    <div class="campo_mask">
                        <SELECT NAME="toppings"  SIZE=5 ondblclick="javascript:add_variabile(this.value);">
						<?
						$query="select * from compilatore_variabili where stato=1 and cancella='n' order by nome_variabile asc";
						$rs2 = mssql_query($query, $conn);
						if(!$rs2) error_message(mssql_error());
						while($row2= mssql_fetch_assoc($rs2)){
						$nome_variabile=$row2['nome_variabile'];
						?>
						<OPTION VALUE="[<?=$nome_variabile?>]"><?=$nome_variabile?>
						<?
						}
						?>
						
						
						
						</SELECT>

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

<script>
function add_variabile(valore)
{
cont=document.getElementById("contenuto").value;
document.getElementById("contenuto").value=cont+" "+valore;

}
</script>

<?
//footer_new();

}

function edit($id)
{

	$conn = db_connect();

	$query = "Select * from re_compilatore_contenuti WHERE idcontenuto=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$idprotocollo = $row['idprotocollo'];
		$etichetta = $row['etichetta'];
		$contenuto = $row['contenuto'];
	}

	// rimuove i caratteri di escape
	$codice_protocollo = stripslashes($codice_protocollo);
	$descrizione_protocollo = stripslashes($descrizione_protocollo);

	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_contenuti_compilatore.php');" href="#">elenco contenuti</a></div>
			 <div class="elem_dispari"><a href="#">modifica contenuto</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_contenuti_compilatore_POST.php" id="myForm">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?php echo $id ?>" />

	<div class="titolo_pag"><h1>Modifica Contenuto</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">Protocollo</div>
			<div class="campo_mask mandatory">
				<select id="idprotocollo" name="idprotocollo" >
				<option value="">seleziona</option>
				<?
				$conn=db_connect();
					$query="select * from protocolli order by codice_protocollo";
					$rs2 = mssql_query($query, $conn);
					if(!$rs2) error_message(mssql_error());
						while($row2= mssql_fetch_assoc($rs2)){
						$idprotocollo_p=$row2['idprotocollo'];
						$codice_p=$row2['codice_protocollo'];
						$descrizione_p=$row2['descrizione'];
						$sel="";
						if ($idprotocollo_p==$idprotocollo)
						$sel="selected";
						?>
						<option <?=$sel?> value="<?=$idprotocollo_p?>"><?=$codice_p." - ".$descrizione_p?></option>
						<?
						}
				
				?>
				</select>
			</div>
		</div>
    </div>    
	
	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">etichetta</div>
			<div class="campo_mask mandatory">
				<input type="text" name="etichetta" class="scrittura"  value="<?=$etichetta?>">
			</div>
		</div>
    </div>    
	
	 <div class="riga">
                <div class="rigo_mask rigo_big">
                    <div class="testo_mask">contenuto</div>
                    <div class="campo_mask mandatory">
                        <textarea name="contenuto" id="contenuto" class="scrittura_campo"><?=$contenuto?></textarea>
                    </div>
                </div>
				
				<div class="rigo_mask">
                    <div class="testo_mask">variabili utilizzabili</div>
                    <div class="campo_mask">
                        <SELECT NAME="toppings"  SIZE=5 ondblclick="javascript:add_variabile(this.value);">
						<?
						$query="select * from compilatore_variabili where stato=1 and cancella='n' order by nome_variabile asc";
						$rs2 = mssql_query($query, $conn);
						if(!$rs2) error_message(mssql_error());
						while($row2= mssql_fetch_assoc($rs2)){
						$nome_variabile=$row2['nome_variabile'];
						?>
						<OPTION VALUE="[<?=$nome_variabile?>]"><?=$nome_variabile?>
						<?
						}
						?>
						
						
						
						</SELECT>

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
	
	
<script>
function add_variabile(valore)
{
cont=document.getElementById("contenuto").value;
document.getElementById("contenuto").value=cont+" "+valore;

}
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

