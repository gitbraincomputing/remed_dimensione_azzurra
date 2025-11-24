<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 1;
include_once('include/function_page.php');
include_once('include/functions_test_clinici.php');
session_start();
//$idutente=$id;


/************************************************************************
* funzione aggiungi test clinico     						*
*************************************************************************/

function preview_test_clinico_ramificato(){

$idtest=$_REQUEST['id'];

$conn = db_connect();
$query = "SELECT idtest,nome,codice,tipo_test,descrizione from re_test_clinici where idtest=$idtest";	
			$ordinamento="";
			$rs1 = mssql_query($query, $conn);
			if(!$rs1) error_message(mssql_error());
			$conta=mssql_num_rows($rs1);
				$i=1;
					if($row1 = mssql_fetch_assoc($rs1))   
					{
						$idtest=$row1['idtest'];
						$codice=$row1['codice'];
						$nome=$row1['nome'];
						$tipo_test=$row1['tipo_test'];
						$descrizione=$row1['descrizione'];
					}
?>
<script type="text/javascript">
	/*$(document).ready(function() {
	    // Initialise the table
	    $("#elenco").tableDnD();
	});*/

	inizializza();
	$(document).ready(function() {


		// Initialise the first table (as before)
		$("#elenco").tableDnD();

			// Initialise the second table specifying a dragClass and an onDrop function that will display an alert
		$("#elenco").tableDnD({
		    onDragClass: "myDragClass",
		    onDrop: function(table, row) {
	            var rows = table.tBodies[0].rows;
	            var debugStr = "";
	            for (var i=0; i<(rows.length-1); i++) 
				{
					if (rows[i].style.display != 'none')
				    debugStr += rows[i].id+" ";
					
	            }
				document.getElementById("debug").value=debugStr;
		       
		    },
			onDragStart: function(table, row) {
			//alert(row.id);
				//$(#debugArea).html("Started dragging row "+row.id);
			}
		});
	});

	</script>

<div id="sanitaria">
<!-- qui va il codice dinamico dei campi -->

<div class="titoloalternativo">
	            <h1>cartella <?=$cartella?></h1>
				
	</div>

	<div class="titolo_pag"><h1>creazione test clinico ramificato:<?=$codice." - ".$nome?> </h1></div>	
	
<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
	
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>

	<input type="hidden" name="action" value="create_test_clinico_ramificato" />
	<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
	<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
	<input type="hidden" name="idtest" value="<?=$idtest?>" />
	<div class="blocco_centralcat">
	<div class="riga">
        <span class="rigo_mask">
            <div class="testo_mask">data di osservazione</div>
            <div class="campo_mask mandatory data_passata">
                <input type="text" class="campo_data scrittura" name="data_osservazione"/>
            </div>
        </span>
    </div>	
	
	<div class="riga">
        <span class="rigo_mask">
            <div class="testo_mask">associa alla pratica</div>
            <div class="campo_mask">
            
				<select name="idimpegnativa" class="scrittura" style="width:auto;">
				<option value="0">seleziona la pratica</option>
				<option value="1">pratiche associabili alla cartella</option>
				</select>
            </div>
        </span>
    </div>
		</div>
					
<div id="pianificazione">		
	<div class="titolo_pag"><h1>inserisci i risultati del test</h1></div>
	<table id="table" class="tablesorter" cellspacing="1"> 
	<!--<table style="clear:both;" id="table" class="tablesorter" cellspacing="2" cellpadding="2" border="0">-->
		
		
		<thead> 
<tr> 
    
    <th colspan="7">Prova</th> 
	<th>Risposte</th> 
      
</tr> 
</thead> 

<?
			/*$query = "SELECT * from re_test_clinici_ramificati where idtest_padre=$idtest";	
			$rs_ram = mssql_query($query, $conn);
			
			while($row_ram = mssql_fetch_assoc($rs_ram))   
			{
			$idtest_figlio=$row_ram['idtest_figlio'];
			$nome_test_figlio=strtolower($row_ram['nome']);

			$query = "SELECT * from test_clinici_prove where idtest=$idtest_figlio";	
			$rs1 = mssql_query($query, $conn);
			?>
			<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr>
			<tr>
			<td><span class="id_notizia_cat"><strong><?=$nome_test_figlio?></strong></span></td>
			<td>&nbsp;</td>
			</tr>
			
			
			<?
	
			if(!$rs1) error_message(mssql_error());
			$conta=mssql_num_rows($rs1);
				$i=1;
					while($row1 = mssql_fetch_assoc($rs1))   
					{
					?>
					<tr>
					<?
						$idprova=$row1['idprova'];
						$descrizione=$row1['descrizione'];
?>
								<td><span class="id_notizia_cat"><?=$descrizione?></span></td>
								
								<?
								$query = "SELECT * from test_clinici_items where idprova=$idprova";	
								$rs2 = mssql_query($query, $conn);
								?>
								
								<td><select class="scrittura" style="width:150px" name="risposta<?=$idprova?>">
								<option value="-10001" selected>non somministrato</option>
								<?
								while($row2 = mssql_fetch_assoc($rs2))   
								{
									$iditem=$row2['iditem'];
									$etichetta=$row2['etichetta'];
									?>
									<option value="<?=$iditem?>"><?=$etichetta?></option>
									<?
								}
								?>
								
								</select>
								</td>
								</tr>
						<?
					}
			}		*/
			stampa_test_figli($idtest,0);
					?>	
		</tbody> 
</table> 
	</div>	
	

	</div>
	<div class="titolo_pag">
	<div class="comandi">
		<div id="aggiungi_tutor" class="elimina popupdatiClose"><a>chiudi anteprima</a></div>
	</div>		
</div>
	</form>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
$(".popupdatiClose").click(function(){
		disablePopup();
	});				
		
	});	
</script>

</div>
<?
}


if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	

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
					else update($_REQUEST['id']);
			break;

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
		}

		
		switch($do) {

	

			case "preview_test_clinico_ramificato":
				preview_test_clinico_ramificato($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				//if($_REQUEST['type']=='res')
					preview_test_clinico_ramificato($_REQUEST['id']);
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