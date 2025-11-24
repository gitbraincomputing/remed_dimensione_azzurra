<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 1;
include_once('include/function_page.php');
session_start();
//$idutente=$id;


function preview_test_clinico($idtest)
{



}

function preview_modulo($idmoduloversione){


//$idcartella=$_REQUEST['idcartella'];
//$idpaziente=$_REQUEST['idpaziente'];
//$idmoduloversione=$_REQUEST['idmodulo'];


$conn = db_connect();
$query="select * from max_vers_moduli_1 where idmoduloversione=$idmoduloversione";

$rs = mssql_query($query, $conn);
	
if(!$rs) error_message(mssql_error());

if($row = mssql_fetch_assoc($rs))   
	$idmoduloversione_new=$row['idmoduloversione'];
	$nome=$row['nome'];
	
if ($idmoduloversione_new=="") $idmoduloversione_new=0;
if ($nome=="") $nome="  - ERRORE in ANTEPRIMA MODULO";

	
$query="select * from campi where idmoduloversione=$idmoduloversione_new order by peso asc";	

$rs1 = mssql_query($query, $conn);
if(!$rs1) error_message(mssql_error());


?>
<script>inizializza();</script>
<div id="sanitaria">
<!-- qui va il codice dinamico dei campi -->
	<div class="titolo_pag"><h1>anteprima modulo: <?=$nome?></h1><div class="comandi" style="float:right;"><h1><a href="javascript:stampa2('sanitaria');" style="text-decoration:none; color:#990000;">stampa modulo</a></h1></div></div>	
	
<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
	
	<div class="blocco_centralcat">
		<div class="riga">i campi contrassegnati con <img src="M_Validation/images/mandatory.png"> sono obbligatori</div>
		<?

		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		$i=1;
		
			while($row1 = mssql_fetch_assoc($rs1)){			
			$idcampo= $row1['idcampo'];
			$etichetta= $row1['etichetta'];
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
		
			if ($obbligatorio=='y')
			$classe="mandatory ".$classe;

			$multi=0;
			$multi_ram=0;
			if($tipo==4){
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi=$row3['multi'];
			mssql_free_result($rs3);
			}
			elseif($tipo==9){
			$query3="select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi_ram=$row3['multi'];
			mssql_free_result($rs3);
			}
	
			if(($i%3==1) and ($tipo!=5)){ 
				echo("<div class=\"riga\">");
				$div=1;
			}
			if((($tipo==2)or($multi==1)or($multi==2)) and(($i%3==2)or($i%3==0))){
				echo("</div><div class=\"riga\">");
			}
			if(($tipo==5)and(($i%3==2)or($i%3==0))){
				echo("</div><div>");
			}
		if(($multi==1)or($multi==2)) $stile="style=\"width:600px;\"";
		
		if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<div class="etichetta_campi"><?=pulisci_lettura($etichetta)?></div>
			<input type="hidden" class="scrittura" name="<?=$idcampo?>" >	
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5))  echo("rigo_big");?>"<?php if(($multi==1)or($multi==2)) echo($stile);?>>
            <div class="testo_mask"><?=pulisci_lettura($etichetta)?></div>            
			<div class="campo_mask <?=$classe?>">
                <?if($tipo==1){?>
					<input type="text" class="scrittura" name="<?=$idcampo?>" >
				  <?}
				     elseif($tipo==2)
					 {?>
					<textarea class="scrittura_campo" name="<?=$idcampo?>" ></textarea>
				  <?
}				
				   elseif(($tipo==3) or($tipo==6))
					 {?>
					<input type="text" class="scrittura campo_data" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==7)
					 {?>
					<input type="file" class="scrittura_campo" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==8)
					 {?>
					<input type="text" class="scrittura_campo" name="<?=$idcampo?>" value="<?=$titolo_medico.$nome_medico?>">
				  <?}
				   elseif($tipo==4)
					 {
						$query2="select * from re_moduli_combo where idcampo=$idcampo and stato=1 order by peso";	

						$rs2 = mssql_query($query2, $conn);
						if(!$rs2) error_message(mssql_error());
					 if(($multi==1)or($multi==2)){
						$xy=1;	
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore=$row2['valore'];
								$etichetta=$row2['etichetta'];
						?>					
					<div style="float:left; padding:0 10px 0 0;clear:both;"><input type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=pulisci_lettura($etichetta)?></div>
					<?
						$xy++;
							}						
					 }else{?>
						<select class="scrittura" name="<?=$idcampo?>" style="width:auto;">
						<option value="">Selezionare un Valore</option><?
					 }										
					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$valore=$row2['valore'];
						$etichetta=$row2['etichetta'];
					?>
					<option value="<?=$valore?>"><?=pulisci_lettura($etichetta)?></option>
					<?}?>
					</select>
				  <?}
				  elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo and stato=1";
					//echo $query2;
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$xy=1;	
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore=$row2['valore'];
								$etichetta=$row2['etichetta'];
						?>					
					<div style="float:left; padding:0 10px 0 0;clear:both;"><input type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=pulisci_lettura($etichetta)?></div>
					<?
						$xy++;
							}						
					 }else{?>
						<select class="scrittura" name="<?=$idcampo?>" style="width:auto;">
						<option value="">Selezionare un Valore</option><?
					 }

					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore=$row2['valore'];
						$etichetta=$row2['etichetta'];
						?>
						<option value="<?=$idcampocombo?>"><?=$etichetta?> </option>
						<!--<optgroup label="<?=$etichetta?>">-->
						<?
						$rinc=rincorri_figlio($idc,$idcampocombo,"","");
						?><!--</optgroup>--><?	
					
					/*while ($row2 = mssql_fetch_assoc($rs2))
					{
						$valore=$row2['valore'];
						$etichetta=$row2['etichetta'];
					?>
					<option value="<?=$valore?>"><?=$etichetta?></option>
					<?}	*/
						
					}?>
					 
					
					</select>
				  <?}
				  
				  
				  
				  ?>
            </div>
			 <?}?>
		</div>	
   
	<?
	if(($tipo==2)or($multi==1)or($multi==2)) $i=0;
	if(($i%3==0)and($tipo!=5)){ 
		echo("</div>");
		$div=0;}
	if($tipo!=5) $i++;		
	}	
	if($div==1) echo("</div>");				
	?>
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
		
	});	
</script>

</div>

<script type="text/javascript" language="javascript">		
$(document).ready(function(){
	
	
				
	//CLOSING POPUP
	//Click the x event!
	$(".popupdatiClose").click(function(){
		disablePopup();
	});
});
	
</script>
		
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
			
			case "review":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else review($_REQUEST['id']);
			break;

			case "preview_test_clinico":
				preview_test_clinico($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				//if($_REQUEST['type']=='res')
					preview_modulo($_REQUEST['id']);
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