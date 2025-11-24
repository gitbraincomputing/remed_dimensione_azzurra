<?php
header("Content-type: text/html; charset=ISO-8859-1");
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
include_once('include/function_page.php');
session_start();
//$idutente=$id;

function show_list()
{
echo ("funzione disattivata");
}


function edit($id){

	$idimpegnativa=$id;
	$conn = db_connect();
	$query ="SELECT * from impegnative where idimpegnativa=$idimpegnativa";	
	
	$rs = mssql_query($query, $conn);
	
	if($row = mssql_fetch_assoc($rs)){
		$idutente=$row['idutente'];
		if ($row['DataPrescrizione']!="") 
			$DataPrescrizione=formatta_data(valid_date($row['DataPrescrizione']));
			else
			$DataPrescrizione="";
		$ProtPrescrizione=pulisci_lettura($row['ProtPrescrizione']);
		if ($row['data_ricezione_pr']!="") 
			$data_ricezione_pr=formatta_data(valid_date($row['data_ricezione_pr']));
			else
			$data_ricezione_pr="";
		$protocollo_int_pr=pulisci_lettura($row['protocollo_int_pr']);		
		if ($row['DataPianoTrattamento']!="") 
			$DataPianoTrattamento=formatta_data(valid_date($row['DataPianoTrattamento']));
			else
			$DataPianoTrattamento="";		
		$ProtPianoTrattamento=pulisci_lettura($row['ProtPianoTrattamento']);		
		if ($row['data_ricezione_pro']!="") 
			$data_ricezione_pro=formatta_data(valid_date($row['data_ricezione_pro']));
			else
			$data_ricezione_pro="";		
		$protocollo_asl_pro=pulisci_lettura($row['protocollo_asl_pro']);		
		if ($row['DataAutorizAsl']!="") 
			$DataAutorizAsl=formatta_data(valid_date($row['DataAutorizAsl']));
			else
			$DataAutorizAsl="";			
		$ProtAutorizAsl=pulisci_lettura($row['ProtAutorizAsl']);		
		if ($row['data_ricezione_au']!="") 
			$data_ricezione_au=formatta_data(valid_date($row['data_ricezione_au']));
			else
			$data_ricezione_au="";			
		$protocollo_int_au=pulisci_lettura($row['protocollo_int_au']);		
		if ($row['DataNullaOsta']!="") 
			$DataNullaOsta=formatta_data(valid_date($row['DataNullaOsta']));
			else
			$DataNullaOsta="";		
		$ProtNullaOsta=pulisci_lettura($row['ProtNullaOsta']);		
		if ($row['data_ricezione_nu']!="") 
			$data_ricezione_nu=formatta_data(valid_date($row['data_ricezione_nu']));
			else
			$data_ricezione_nu="";		
		$protocollo_int_nu=pulisci_lettura($row['protocollo_int_nu']);
				
		$cdc=pulisci_lettura($row['cdc']);
		$MedicoPrescrittore=pulisci_lettura($row['MedicoPrescrittore']);
		$MedicoInduttore=pulisci_lettura($row['MedicoInduttore']);
		$Normativa=pulisci_lettura($row['Normativa']);
		$RegimeAssistenza=pulisci_lettura($row['RegimeAssistenza']);
		$Frequenza=pulisci_lettura($row['Frequenza']);
		$ggfrequenza=pulisci_lettura($row['ggfrequenza']);	
		$diagnosi=pulisci_lettura($row['Diagnosi']);		
		$classemenomazione=pulisci_lettura($row['classemenomazione']);
		$disabilita=pulisci_lettura($row['disabilita']);
		$classemenomazione=pulisci_lettura($row['classemenomazione']);
		$classemenomazione2=pulisci_lettura($row['classemenomazione2']);
		$classemenomazione3=pulisci_lettura($row['classemenomazione3']);
		$grav_dis=pulisci_lettura($row['grav_dis']);
		$liv_prognostico=pulisci_lettura($row['liv_prognostico']);
		if ($row['DataDimissione']!="") 
			$DataDimissione=formatta_data(valid_date($row['DataDimissione']));
			else
			$DataDimissione="";		
		$MotivoDimissione=pulisci_lettura($row['MotivoDimissione']);
		$case_manager=$row['case_manager'];
		$note_dimissione=pulisci_lettura($row['note_dimissione']);		
		$asl=$row['asl'];	
		$distretto=$row['distretto'];
		$esenzione=$row['esenzione'];	
		$esenzionecodice=$row['esenzionecodice'];
		$esenzionecodice_alt=$row['esenzionecodice_alt'];		
		$ticket=$row['ticket'];		
		$note=$row['Note'];	
		$numtrattamenti=$row['NumeroTrattamenti'];			
		$datafinetrattamento=formatta_data(valid_date($row['DataFineTrattamento']));
		$protocollo_dt=$row['protocollo_dt'];
		$successo_trattamento=$row['successo_trattamento'];
		$tipologiamedico=$row['tipologiamedico'];
		$datavisitaasl=formatta_data($row['DataVisitaASL']);
		$oravisitaasl=$row['OraVisitaASL'];		
		$idreparto=$row['idreparto'];
		$stampainformatizzata=$row['stampainformatizzata'];
		$datainiziotrattamento=formatta_data($row['DataInizioTrattamento']);
		$livello_gravita=$row['livello_gravita'];
		$med_resp=$row['med_resp'];
		$idprotocollo=$row['idprotocollo'];	
		$MotivoTrattamento=$row['motivazione_trat'];
		$compartecipazione=$row['compartecipazione'];
		
		$icdm9=$row['icdm9'];
		$icdm92=$row['icd92'];
		$idpacchetto=$row['idpacchetto'];
		$idprofilo=$row['idprofilo'];
		$idpacchetto1=$row['idpacchetto1'];
		$idprofilo1=$row['idprofilo1'];		
		$icd9=$row['ICD9RIA'];		
	}
?>
<script>
inizializza();
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('carica_presenze.php?&do=&id='+id, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}

function loadPopup_plan(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('gest_planning.php?&do=edit&id='+id, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}


//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
		
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popupdati2").height();
	var popupWidth = $("#popupdati2").width();
	//centering
	$("#popupdati2").css({
		"position": "fixed",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}

function prev(id){	
	//centering with css
	loadPopup(id);
	centerPopup();
	//load popup


}

function prev_plan(id){	
	//centering with css
	loadPopup_plan(id);
	centerPopup();
	//load popup


}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){		
		$("#backgroundPopup").fadeOut("slow");				
		$("#popupdati2").fadeOut("slow");
		$('#thumbnail_form').hide();
		$("#desc_foto").hide();
		$(".imgareaselect-border2").hide();
		$(".imgareaselect-border1").hide();
		$(".imgareaselect-outer").hide();	
		$(".imgareaselect-selection").hide();	
		popupStatus = 0;
	}
}

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!
	$(".preview").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
	});
					
	//CLOSING POPUP
	//Click the x event!
	$(".popupdatiClose").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

});

</script>
<div id="popupdati2"></div>
<div id="backgroundPopup"></div>
<div id="lista_impegnative" onclick="$('#selectICD9').css('display','none');" >
<form id="myForm" method="post" name="form0" action="re_pazienti_impegnativa_POST.php">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?php echo($idimpegnativa);?>" />
    
	<div class="titolo_pag"><h1>Dati relativi alla Prescrizione</h1></div>
	
	<div class="blocco_centralcat">	
		<div class="riga">
		
		<? 
	
	if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and (($_SESSION['UTENTE']->get_tipoaccesso()!=5)))
	print("");
	else
	print("<br /><br /><p><strong>Non si dispone dei permessi necessari per modificare l'impegnativa</strong></p><br /><br />");
	
				$query = "SELECT * from cdc where cancella='n' order by centrodicosto asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				if(mssql_num_rows($rs)>1){
				?>
					<div class="rigo_mask">
					<div class="testo_mask">Centro di Costo</div>
					<div class="campo_mask mandatory">
				
					<select name="cdc" class="scrittura">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($cdc==$row['idcdc'])
					$sel="selected";
					?>
		                <option <?=$sel?> value="<?=$row['idcdc']?>"><?=$row['centrodicosto']?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>					 
					</select>
					</div>
					</div>			
					<?}else{
					$row = mssql_fetch_assoc($rs);
					$cdc=$row['idcdc'];
					mssql_free_result($rs);
					?>
					<input type="hidden" name="cdc" value="<?=$cdc?>" />
					<?}?>			
		<div class="rigo_mask">
				<div class="testo_mask">Normativa</div>
				<div class="campo_mask mandatory">
							<?
				$query = "SELECT * from normativa order by normativa asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="Normativa" class="scrittura textsmall">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{					
					$sel="";
					if ($Normativa==$row['idnormativa'])
					$sel="selected";					
					?>
		                <option <?=$sel?> value="<?=$row['idnormativa']?>"><?=pulisci_lettura($row['normativa']." - ".$row['descrizione'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 </select>
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Regime</div>
				<div class="campo_big mandatory">
							<?			
				$query = "SELECT dbo.regime.idregime, dbo.regime.idnormativa, dbo.regime.regime, dbo.regime.stato, dbo.regime.cancella, dbo.normativa.normativa as normativa
FROM dbo.regime INNER JOIN dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa order by dbo.normativa.normativa asc";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<!--<select name="RegimeAssistenza" class="scrittura textsmall" onclick="javascript:manImpegnativa(this.options[this.value].text);">-->
					<select name="RegimeAssistenza" class="scrittura textsmall" id="RegimeAssistenza">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$idregime=$row['idregime'];
						$regime=$row['regime'];
						$normativa=$row['normativa'];
					$sel="";
					if ($RegimeAssistenza==$row['idregime'])
					$sel="selected";
					
					?>
		                <option <?=$sel?> value="<?=$row['idregime']?>"><?=pulisci_lettura($normativa." - ".$row['regime'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 </select>
				</div>
			</div>
						
	 	</div>
		<div class="riga">
		<div class="rigo_mask">
				<div class="testo_mask">Protocollo</div>
				<div class="campo_big">
				<?
					$conn=db_connect();
					$query = "SELECT idprotocollo,codice_protocollo,descrizione from re_protocolli_compilatore_attivi order by codice_protocollo ASC";
					$rs2 = mssql_query($query, $conn);
					if(!$rs2) error_message(mssql_error());
						//echo(mssql_num_rows($rs2));
				?>
					<select id="idprotocollo" name="idprotocollo">
							<option value="">seleziona il protocollo</option>
				<?
					while($row2= mssql_fetch_assoc($rs2)){
						$idprot=$row2['idprotocollo'];
						$codice_p=pulisci_lettura($row2['codice_protocollo']);
						$descrizione_p=($row2['descrizione']);
						?>
						<option value="<?=$idprot?>" <?if ($idprotocollo==$idprot)echo("selected"); ?>><?=$codice_p."  - ".$descrizione_p?></option>
						<?
					}
					?>
					</select>
				</div>
			</div>
		</div>
		
		<div class="riga">
		<div id="first_step">
			<div class="rigo_mask rigo_doppio">
				<div class="testo_mask">Prescrizione</div>
				<div class="campo_mask">
					<div class="etichetta">data prod</div>
					<div id="prescrizione_data" class="data_all nomandatory">
					<input type="text" class="campo_data" name="DataPrescrizione" id="DataPrescrizione" onblur="setNow('data_ricezione_pr1');" value="<?php echo($DataPrescrizione);?>"/>
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot asl</div>
					<div id="prescrizione_prot" class="nomandatory">
					<input type="text" class="scrittura_campo" name="ProtPrescrizione" value="<?php echo($ProtPrescrizione);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">data cons</div>
					<div id="data_ricezione_pr" class="data_all nomandatory">
					<input type="text" class="campo_data" id="data_ricezione_pr1" name="data_ricezione_pr" onblur="checkDate(this.value,'DataPrescrizione');" value="<?php echo($data_ricezione_pr);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot int</div>
					<div id="protocollo_int_pr" class="nomandatory">
					<input type="text" class="scrittura_campo" name="protocollo_int_pr" value="<?php echo($protocollo_int_pr);?>" />
					</div>
				</div>
			</div>
			
			<div class="rigo_mask rigo_doppio">
				<div class="testo_mask">Progetto</div>
				<div class="campo_mask">
					<div class="etichetta">data prod</div>
					<div id="trattamento_data" class="data_all nomandatory">
					<input type="text" class="campo_data" name="DataPianoTrattamento" id="DataPianoTrattamento" onblur="setNow('data_ricezione_pro1');" value="<?php echo($DataPianoTrattamento);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot int</div>
					<div id="trattamento_prot" class="nomandatory">
					<input type="text" class="scrittura_campo" name="ProtPianoTrattamento"  value="<?php echo($ProtPianoTrattamento);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">data cons</div>
					<div id="data_ricezione_pro" class="data_all nomandatory">
					<input type="text" class="campo_data" name="data_ricezione_pro" id="data_ricezione_pro1" onblur="checkDate(this.value,'DataPianoTrattamento');" value="<?php echo($data_ricezione_pro);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot asl</div>
					<div id="protocollo_asl_pro" class="nomandatory">
					<input type="text" class="scrittura_campo" name="protocollo_asl_pro"  value="<?php echo($protocollo_asl_pro);?>" />
					</div>
				</div>
			</div>
			</div>
			<div class="rigo_mask rigo_doppio">
				<div class="testo_mask">Autorizzazione</div>
				<div class="campo_mask">
					<div class="etichetta">data prod</div>
					<div id="autorizzazione_data" class="data_all nomandatory">
					<input type="text" class="campo_data" name="DataAutorizAsl" id="DataAutorizAsl"  onblur="setNow('data_ricezione_au1');" value="<?php echo($DataAutorizAsl);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot asl</div>
					<div id="autorizzazione_prot" class="nomandatory">
					<input type="text" class="scrittura_campo" name="ProtAutorizAsl"  value="<?php echo($ProtAutorizAsl);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">data cons</div>
					<div id="data_ricezione_au" class="data_all nomandatory">
					<input type="text" class="campo_data" id="data_ricezione_au1" name="data_ricezione_au" onblur="checkDate(this.value,'DataAutorizAsl');" value="<?php echo($data_ricezione_au);?>" />
					</div>
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot int</div>
					<div id="protocollo_int_au" class="nomandatory">
					<input type="text" class="scrittura_campo" name="protocollo_int_au" value="<?php echo($protocollo_int_au);?>" />
					</div>
				</div>
			</div>
			<div id="second_step">
			<div class="rigo_mask rigo_doppio">
				<div class="testo_mask">Nulla Osta</div>
				<div class="campo_mask">
					<div class="etichetta">data prod</div>
					<div id="data_produzione_no" class="data_all nomandatory">
					<input type="text" class="campo_data" name="DataNullaOsta" id="DataNullaOsta" onblur="setNow('data_ricezione_nu1');" value="<?php echo($DataNullaOsta);?>" />
					</div>					
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot asl</div>
					<input type="text" class="scrittura_campo" name="ProtNullaOsta" value="<?php echo($ProtNullaOsta);?>" />
				</div>
				<div class="campo_mask">
					<div class="etichetta">data cons</div>
					<div id="data_ricezione_no" class="data_all nomandatory">	
					<input type="text" class="campo_data" name="data_ricezione_nu" id="data_ricezione_nu1" onblur="checkDate(this.value,'DataNullaOsta');" value="<?php echo($data_ricezione_nu);?>" />
					</div>	
				</div>
				<div class="campo_mask">
					<div class="etichetta">prot int</div>					
					<input type="text" class="scrittura_campo" name="protocollo_int_nu" value="<?php echo($protocollo_int_nu);?>" />					
				</div>
			</div>
			</div>
			
	</div>
		
	<div class="riga">
		<?if(TIPOLOGIA_MEDICO){?>
		<div class="rigo_mask">
			<div class="testo_mask">Tipologia di Medico</div>
			<div class="campo_mask">
				<?
			$query = "SELECT * FROM tipomedico order by IdTipoMedico ASC";	
			$rs = mssql_query($query, $conn);
			if(!$rs) error_message(mssql_error());
			?>
				<select name="tipologiamedico" class="scrittura">
				<option value="0">selezionare</option>
				<?  while($row = mssql_fetch_assoc($rs))   
				{
				$sel="";
				if ($tipologiamedico==$row['IdTipoMedico'])
				$sel="selected";
				
				?>
					<option <?=$sel?> value="<?=$row['IdTipoMedico']?>"><?=pulisci_lettura($row['CodiceTipoMedico']."  ".$row['Descrizione'])?></option>
				 <?
				 }
				 mssql_free_result($rs);
				 ?>				 
				</select>
			</div>
		</div>
		<?}else{?>
		<input name="tipologiamedico" value="0" type="hidden">
		<?}?>
		<?if(DATA_VIS_ASL){?>
		<div class="rigo_mask">
				<div class="testo_mask">Data visita ASL</div>
				<div class="campo_mask">
				<input type="text" class="campo_data" name="DataVisitaASL"  value="<?php echo($datavisitaasl);?>"/>
				</div>
		</div>
		<?}else{?>
		<input name="DataVisitaASL" value="" type="hidden">
		<?}?>
		<?if(ORA_VIS_ASL){?>
		<div class="rigo_mask">
			<div class="testo_mask">Ora visita ASL</div>
			<div class="campo_mask">
			<input type="text" class="scrittura_campo" name="OraVisitaASL"  value="<?php echo($oravisitaasl);?>"/>
			</div>
		</div>
		<?}else{?>
		<input name="OraVisitaASL" value="" type="hidden">
		<?}?>
	</div>
	
	<div class="riga">
	
				<div class="rigo_mask">
				<div class="testo_mask">Reparto</div>
				<div class="campo_mask">
					<?
				$query = "SELECT * FROM tiporeparto order by CodiceReparto ASC";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="idreparto" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($idreparto==$row['CodiceReparto'])
					$sel="selected";					
					?>
						<option <?=$sel?> value="<?=$row['CodiceReparto']?>"><?=pulisci_lettura($row['Descrizione'])?></option>
					 <?
					 }
					 mssql_free_result($rs);
					 ?>				 
					</select>
				</div>
				</div>
					
			<div class="rigo_mask">
				<div class="testo_mask">Medico Prescrittore</div>
				<div class="campo_mask">
					<?
				$query = "SELECT IdPrescrittore,NominativoPrescrittore from prescrittori where cancella='n' and status=1 order by NominativoPrescrittore asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="MedicoPrescrittore" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($MedicoPrescrittore==$row['IdPrescrittore'])
					$sel="selected";
					
					?>
		                <option <?=$sel?> value="<?=$row['IdPrescrittore']?>"><?=pulisci_lettura(strtoupper($row['NominativoPrescrittore']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Medico Induttore</div>
				<div class="campo_mask">
					<?
				$query = "SELECT IdPrescrittore,NominativoPrescrittore from prescrittori where cancella='n' and status=1 order by NominativoPrescrittore asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="MedicoInduttore" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($MedicoInduttore==$row['IdPrescrittore'])
					$sel="selected";
					
					?>
		                <option <?=$sel?> value="<?=$row['IdPrescrittore']?>"><?=pulisci_lettura(strtoupper($row['NominativoPrescrittore']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>	
		</div>
		<div class="riga">
			<div class="rigo_mask rigo_doppio_big">
				<div class="testo_mask">Frequenza</div>
				<div class="campo_mask">
					<div class="etichetta">tratt.</div>
					<div class="nomandatory controllo_trattamenti">	
					<input type="text" class="campo_numerico" name="ggfrequenza"  value="<?php echo($ggfrequenza);?>" />
					</div>	
				</div>
				<div class="campo_mask">
					<div class="etichetta">settim.</div><?
					$query = "SELECT IdFrequenza, TipoFrequenza FROM ElencoFrequenze WHERE (NOT (TipoFrequenza IS NULL)) ORDER BY TipoFrequenza";	
					$rs = mssql_query($query, $conn);
					if(!$rs) error_message(mssql_error());
					?>
						<select name="Frequenza" class="scrittura textsmall">
						<option value="0">selezionare</option>
						<?  while($row = mssql_fetch_assoc($rs))   
						{
						$sel="";
						if ($Frequenza==$row['IdFrequenza'])
						$sel="selected";
						
						?>
							<option <?=$sel?> value="<?=$row['IdFrequenza']?>"><?=pulisci_lettura(strtoupper($row['TipoFrequenza']))?></option>
						 <?
						 }
						 mssql_free_result($rs);
						 ?>
						 </select>					
				</div>				
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Numero Trattamenti</div>
				<div class="campo_mask">
				<input type="text" class="scrittura_campo" name="NumeroTrattamenti" value="<?php echo($numtrattamenti);?>" />
				</div>
			</div>
			<?
			if($Normativa==2){
			?>
			<div class="rigo_mask">
				<div class="testo_mask">Compartecipazione</div>
				<div class="campo_mask">
				<input type="text" class="scrittura_campo" name="compartecipazione" value="<?php echo($compartecipazione);?>" />
				</div>
			</div>
			<? }?>
		
		</div>
		<div class="riga">
		
			<div class="rigo_mask">
				<div class="testo_mask">Diagnosi</div>
				<div class="campo_mask mandatory">
					<input style="width:705px" id="diagnosi" type="text" class="scrittura_campo" name="Diagnosi" value="<?=$diagnosi?>" />
				</div>
			</div>
		
		</div>
		<div class="riga">
		     <div class="rigo_mask" >
				<div class="testo_mask">Menomazione 1</div>
				<div class="campo_mask">
						<?
				$query = "SELECT * from menomazioni order by codice asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="classemenomazione" class="scrittura textsmall" style="width:705px;">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($classemenomazione==$row['idmen'])
					$sel="selected";
					
					
					?>
		                <option <?=$sel?> value="<?=$row['idmen']?>"><?=strtoupper($row['codice']." ".$row['descrizione'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
		</div>
		<div class="riga">
		     <div class="rigo_mask" >
				<div class="testo_mask">Menomazione 2</div>
				<div class="campo_mask">
						<?
				$query = "SELECT * from menomazioni order by codice asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="classemenomazione2" class="scrittura textsmall" style="width:705px;">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($classemenomazione2==$row['idmen'])
					$sel="selected";
					
					
					?>
		                <option <?=$sel?> value="<?=$row['idmen']?>"><?=strtoupper($row['codice']." ".$row['descrizione'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
		</div>
		<div class="riga">
		     <div class="rigo_mask" >
				<div class="testo_mask">Menomazione 3</div>
				<div class="campo_mask">
						<?
				$query = "SELECT * from menomazioni order by codice asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="classemenomazione3" class="scrittura textsmall" style="width:705px;">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($classemenomazione3==$row['idmen'])
					$sel="selected";
					
					
					?>
		                <option <?=$sel?> value="<?=$row['idmen']?>"><?=strtoupper($row['codice']." ".$row['descrizione'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
		</div>
		
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">Asl</div>
				<div class="campo_mask">
				<?
				$query = "SELECT CodiceASL as codice, DescrizioneAsl as descr FROM dbo.distretti GROUP BY CodiceASL,DescrizioneAsl ORDER BY CodiceASL ASC";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
				<select name="asl" class="scrittura" onchange="javascript:carica_distretto(this.value);">
					<option value="0" selected>selezionare</option>
					<?php
					while($row=mssql_fetch_assoc($rs)){
						$sel="";
						if ($asl==$row['codice'])
						$sel="selected";
						$desc=$row['descr'];	
						?>
							<option <?=$sel?> value="<?=$row['codice']?>"><?=$row['codice']." - ".$desc?></option>
						 <?
					}?>
				</select>		
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Distretto</div>
				<div id="div_distretto" class="campo_mask" >
				<?
				$query = $query = "SELECT IdDistretto as id, CodiceASL, CodiceDistretto as codice, DescrizioneDistretto as nome FROM dbo.distretti GROUP BY IdDistretto,CodiceASL, CodiceDistretto, DescrizioneDistretto HAVING (CodiceASL = '$asl')";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
				<select name="distretto" class="scrittura" style="width:540px;">
					<option value="0" selected>selezionare</option>
					<?php
					while($row=mssql_fetch_assoc($rs)){
						$sel="";
						if ($distretto==$row['id'])
						$sel="selected";						
						?>
							<option <?=$sel?> value="<?=$row['id']?>"><?=pulisci_lettura($row['codice']." - ".$row['nome'])?></option>
						 <?
					}?>
				</select>
				</div>
			</div>
		</div>	
		
		<div class="riga">	
			<div class="rigo_mask">
				<div class="testo_mask">Ticket</div>
				<div class="campo_mask prezzo_euro">
				<input type="text" class="scrittura_campo" name="ticket" value="<?php echo($ticket);?>" />
				</div>
			</div>
		
			<div class="rigo_mask rigo_doppio_big">
				<div class="testo_mask">Esenzione</div>
				<div class="campo_mask">
				<div class="etichetta">Tipo</div>
				<select name="esenzione" class="scrittura">
					<option value="0">selezionare</option>
					<option value="1"<? if ($esenzione==1) echo("selected")?>>Esente</option>
					<option value="2"<? if ($esenzione==2) echo("selected")?>>Non Esente</option>
				</select>
				</div>
			
				<div class="campo_mask">
				<div class="etichetta">Codice</div>			
				<?			
				$query = "SELECT * from codici_esenzione order by codice asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
				<select name="esenzionecodice" class="scrittura">
					<option value="0" selected='selected'>selezionare</option>
					<?php
					while($row=mssql_fetch_assoc($rs)){						
						if (trim($esenzionecodice).""==$row['idesenzione']) 
							$sel="selected='true'";
							else
							$sel="";							
						?>
							<option <?=$sel?> value="<?=$row['idesenzione']?>"><?=$row['codice']." - ".$row['nome']?></option>
						 <?
					}?>
				</select>
				</div>
			</div>
		
			<div class="rigo_mask">
				<div class="testo_mask">Codice esenzione alternativo</div>
				<div class="campo_mask">
				<input type="text" class="scrittura_campo" name="esenzionecodice_alt"  value="<?php echo($esenzionecodice_alt);?>"/>
				</div>
			</div>
			</div>
		
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">Tipologia di stampa</div>
				<div class="campo_mask">
				<input type="checkbox" name="stampainformatizzata" <? if ($stampainformatizzata=='on') echo("checked")?>/>Stampa informatizzata				
				</div>
			</div>
		
			<div class="rigo_mask">
				<div class="testo_mask">Data inizio Trattamento</div>
				<div class="campo_mask">
				<input type="text" class="campo_data" name="DataInizioTrattamento"  value="<?php echo($datainiziotrattamento);?>"/>
				</div>
			</div>
			<div class="rigo_mask">
				<div class="testo_mask">Data fine Trattamento</div>
				<div class="campo_mask">
				<input type="text" class="campo_data" name="DataFineTrattamento"  value="<?php echo($datafinetrattamento);?>"/>
				</div>
			</div>
			</div>
		
		<div class="riga">
			
		
			
			<div class="rigo_mask">
				<div class="testo_mask">Case Manager</div>
				<div class="campo_mask">
					<?
				$query = "SELECT uid, nome, status, cancella, case_manager FROM dbo.operatori WHERE (status = 1) AND (cancella = 'n') order by nome asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="case_manager" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($case_manager==$row['uid'])
					$sel="selected";
					
					?>
		                <option <?=$sel?> value="<?=$row['uid']?>"><?=pulisci_lettura(strtoupper($row['nome']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>			
			
			
			<div class="rigo_mask">
				<div class="testo_mask">Medico Responsabile</div>
				<div class="campo_mask">
					<?
				$query = "SELECT uid, nome, status, cancella, med_resp FROM dbo.operatori WHERE (status = 1) AND (cancella = 'n') AND (med_resp='y') order by nome asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="med_resp" class="scrittura">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($med_resp==$row['uid'])
					$sel="selected";
					
					?>
		                <option <?=$sel?> value="<?=$row['uid']?>"><?=pulisci_lettura(strtoupper($row['nome']))?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					</select>
				</div>
			</div>
			<div class="rigo_mask">
			<div class="testo_mask">Livello di gravità</div>
				<div class="campo_mask">
				<!--<input type="text" class="scrittura_campo" name="livello_gravita" value="<?php echo($livello_gravita);?>" />-->				
				<select name="livello_gravita" class="scrittura">
					<option value="nessuno">selezionare</option>
					<option value="A-ALTO"<? if ($livello_gravita=="A-ALTO") echo("selected")?>>A-ALTO</option>
					<option value="B-MEDIO"<? if ($livello_gravita=="B-MEDIO") echo("selected")?>>B-MEDIO</option>
					<option value="C-BASSO"<? if ($livello_gravita=="C-BASSO") echo("selected")?>>C-BASSO</option>
				</select>
				</div>
			</div>
			</div>
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">Protocollo DT</div>
				<div class="campo_mask">
				<input type="checkbox" id="succ" name="protocollo_dt" <? if ($protocollo_dt=='on') echo("checked")?>/> Esiste un protocollo DT per la patologia
				<!--<input type="checkbox" id="succ" onclick="if($('#succ:checked').val()=='on'){ $('#s_t').slideDown();} else{ $('#s_t').slideUp();}" name="protocollo_dt" <? if ($protocollo_dt=='on') echo("checked")?>/> Esiste un protocollo DT per la patologia-->
				<!--<select name="protocollo_dt" class="scrittura">
					<option value="0">selezionare</option>
					<option value="1"<? if ($protocollo_dt==1) echo("selected")?>>Esiste</option>
					<option value="2"<? if ($protocollo_dt==2) echo("selected")?>>Non Esiste</option>
				</select>-->
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">Successo del trattamento</div>
				<div class="campo_mask">				
				<span id="s_t"><input type="checkbox" name="successo_trattamento" <? if ($successo_trattamento=='on') echo("checked")?>/> Il trattamento ha avuto successo</span>
				<!--<select name="successo_trattamento" class="scrittura">
					<option value="0">selezionare</option>
					<option value="1"<? if ($successo_trattamento==1) echo("selected")?>>Ha avuto successo</option>
					<option value="2"<? if ($successo_trattamento==2) echo("selected")?>>Non ha avuto successo</option>
				</select>-->
				</div>
			</div>
		</div>
		<div class="riga">
		
			<div class="rigo_mask">
				<div class="testo_mask">Pacchetto 1</div>
				<div class="campo_mask">				
				<?
				if($idpacchetto!="")
					$where_p="WHERE Id_pacchetto=$idpacchetto";
					else
					$where_p="WHERE Id_pacchetto=0";
				$query = "SELECT Id_pacchetto,Pacchetto,Descrizione FROM dbo.Fkt_Pacchetti $where_p Order by Pacchetto asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				$row = mssql_fetch_assoc($rs);
				$codice1=$row['Pacchetto'];
				$valore=pulisci_lettura($row['Pacchetto']." - ".$row['Descrizione']);
				?>
				<input type="hidden" name="idpacchetto"  value="<?php echo($idpacchetto);?>"/>
				<input type="text" name="idpacchetto_desc"  style="width: 300px;" value="<?php echo($valore);?>"/>				
				</div>
			</div>
		<div class="rigo_mask">
				<div class="testo_mask">Profilo 1</div>
				<div class="campo_mask" id="profili_pacchetti">								
				<?
				if($idpacchetto!="")
					$where_p="WHERE idpacchetto=$idpacchetto";
					else
					$where_p="WHERE idpacchetto=0";
				$query = "SELECT IDprofilo,codice_profilo,idpacchetto,Denominazione_profilo FROM dbo.Fkt_Pacchetti_profili $where_p order by codice_profilo asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="idprofilo" class="scrittura" style="width: 300px;">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($idprofilo==$row['codice_profilo'])
					$sel="selected";
					
					?>
		                <option <?=$sel?> value="<?=$row['codice_profilo']?>"><?=pulisci_lettura($row['codice_profilo']." - ".$row['Denominazione_profilo'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					</select>
				</div>
			</div>		
		</div>
		<div class="riga">
		
			<div class="rigo_mask">
				<div class="testo_mask">Pacchetto 2</div>
				<div class="campo_mask">				
				<?
				if($idpacchetto1!="")
					$where_p="WHERE Id_pacchetto=$idpacchetto1";
					else
					$where_p="WHERE Id_pacchetto=0";
				$query = "SELECT Id_pacchetto,Pacchetto,Descrizione FROM dbo.Fkt_Pacchetti $where_p Order by Pacchetto asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				$row = mssql_fetch_assoc($rs);
				$codice2=$row['Pacchetto'];
				$valore=pulisci_lettura($row['Pacchetto']." - ".$row['Descrizione']);
				?>
				<input type="hidden" name="idpacchetto1"  value="<?php echo($idpacchetto1);?>" />
				<input type="text" name="idpacchetto_desc"  style="width: 300px;" value="<?php echo($valore);?>" />				
				</div>
			</div>
		<div class="rigo_mask">
				<div class="testo_mask">Profilo 2</div>
				<div class="campo_mask" id="profili_pacchetti">								
				<?
				if($idpacchetto1!="")
					$where_p="WHERE idpacchetto=$idpacchetto1";
					else
					$where_p="WHERE idpacchetto=0";
				$query = "SELECT IDprofilo,codice_profilo,idpacchetto,Denominazione_profilo FROM dbo.Fkt_Pacchetti_profili $where_p order by codice_profilo asc";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="idprofilo1" class="scrittura" style="width: 300px;">
					<option value="">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
					$sel="";
					if ($idprofilo1==$row['codice_profilo'])
					$sel="selected";
					
					?>
		                <option <?=$sel?> value="<?=$row['codice_profilo']?>"><?=pulisci_lettura($row['codice_profilo']." - ".$row['Denominazione_profilo'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					</select>
				</div>
			</div>		
		</div>

		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">ICD9</div>	
				<div class="campo_mask">
					<input type="text" name="ICD9RIA" id="idIcd9" autocomplete="off" value="<?php echo($icd9);?>" onkeyup="showListaIcd9(this.value)" />
					<div id="selectICD9"></div>
				</div>
			</div>
		
			<div class="rigo_mask">
				<div class="testo_mask">ICDM9</div>
				<div class="campo_mask">
				<input type="text" name="icdm9"  value="<?php echo($icdm9);?>" />
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">ICDM9 -2</div>
				<div class="campo_mask">
				<input type="text" name="icd92"  value="<?php echo($icdm92);?>" />
				</div>
			</div>
		</div>
		<div class="riga">		
			<div class="rigo_mask rigo_big">
				<div class="testo_mask">Note</div>
				<div class="campo_mask">
					<input id="note" type="text" class="scrittura_campo" name="Note" value="<?=$note?>" />
				</div>
			</div>		
		</div>
		
	<div class="titolo_pag"><h1>Tipologie di trattamento</h1></div>	
	<div id="elenco_trattamenti">	
	<?	
	$d=1;
	while ($d<2)
	{
		if ($d==1)
			$st=$d;
		else
			$st.=",".$d;
		
		$ordinamento=$st;

		$d++;
	}
	?>			
	<input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">			

	<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">

	 <tbody id="tbody" >
	<tr class="riga_tab nodrag nodrop">
	<td width="15%" align="left">&nbsp;</td>
	<td width="10%" align="center">pacchetto</td>
	<td width="30%" align="center">tipolgia assistenza</td>
	<td width="10%" align="center">trattamenti richiesti</td>
	<td width="10%" align="center">frequenza</td>
	<td width="15%" align="center">fatti/da fare - ultima data</td>
	<td width="5%">view presenze</td>
	<td width="5%">view planning</td>
	<td width="5%">elimina</td>
	<td width="10%">&nbsp;</td>
	</tr>
	<?

	$query="SELECT RicetteCodici.pacchetto, RicetteCodici.Frequenza as frequenza, RicetteCodici.IdRicettaCodice as idcampo, RicetteCodici.CodicePrestazione, tipologia.descrizione, tipologia.idregime, RicetteCodici.QuantitaRichieste
			FROM tipologia INNER JOIN RicetteCodici ON tipologia.idtipologia = RicetteCodici.CodicePrestazione
			WHERE (RicetteCodici.IdRicetta = '$idimpegnativa')";
	$rs = mssql_query($query, $conn);
		
	$i=1;
	$numerofield=5;
	while($row1 = mssql_fetch_assoc($rs)){
						
			$idcampo= $row1['idcampo'];
			$pacchetto= $row1['pacchetto'];
			$codice= $row1['CodicePrestazione'];	
			$descrizione= $row1['descrizione'];	
			$richieste= $row1['QuantitaRichieste'];				
			$idregime= $row1['idregime'];
			$cod_freq= $row1['frequenza'];			
			$class="riga_sottolineata_diversa";
	?>	

		
		<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
		<td align="center">&nbsp;</td>
		<td align="center">
		<select name="pac_terapia<?=$i?>">			
			<?
			if($pacchetto==""){?>
			<option value=""></option>
			<?}else{?>
				<? if($idpacchetto!=""){?><option value="<?=$codice1?>" <? if($codice1==$pacchetto) echo("selected");?>><?=$codice1?></option><?}
				else{?>
					<option value=""></option>
				<?}?>
				<? if($idpacchetto1!=""){?><option value="<?=$codice2?>" <? if($codice2==$pacchetto) echo("selected");?>><?=$codice2?></option><?}?>	
		<?}?>
		</select>
		</td>
		<td align="center">
			<select id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" OnClick="javascript:addSelect(getElementById('RegimeAssistenza').value,'<?=$i?>');">
			<?
			$query_1 = "SELECT idtipologia, idregime, descrizione, codice FROM dbo.tipologia WHERE (idregime =$idregime) and ((cancella='n') or (cancella IS NULL))";
			$rs_1 = mssql_query($query_1, $conn);
			?>
			<option value="0">seleziona una tipologia</option>
			<option value="0">aggiorna elenco...</option>
			<?
			while($row_1 = mssql_fetch_assoc($rs_1)){?>
				<option value="<?=$row_1['idtipologia']?>" <? if($codice==$row_1['idtipologia']) echo("selected");?>><?=$row_1['codice']?> - <?=$row_1['descrizione']?></option>
			<?
			} 
			mssql_free_result($rs_1);?>			
			</select>
		</td>
		<td align="center" class="nodrag nodrop nomandatory integer"><input style="width:30px;" id="ric<?=$i?>" name="richiesti<?=$i?>" value="<?=$richieste?>" /></td>
		<td align="center" class="nodrag nodrop nomandatory integer">
			<?
			$query="SELECT * FROM re_ricettacodice_frequenza WHERE idricettacodice=$idcampo";
			$rs_f = mssql_query($query, $conn);
			$freq="";
			while($row_f=mssql_fetch_assoc($rs_f)){
				$freq.=$row_f['frequenza']." ";
			}			
			echo($freq);
			?>	
		</td>

		<td align="center" class="nodrag nodrop">
		<?
		$max="";
		$pres="";
		$query_1="SELECT     SUM(dbo.RicettePresenze.QuantitaErogate) AS presenze, dbo.RicettePresenze.IdRicettaCodice, MAX(RicettePresenze_1.DataPrestazione) 
                      AS data_ultima, RicettePresenze_1.QuantitaErogate
					FROM         dbo.RicettePresenze INNER JOIN
                      dbo.RicettePresenze AS RicettePresenze_1 ON dbo.RicettePresenze.IdRicettaPresenza = RicettePresenze_1.IdRicettaPresenza
					GROUP BY dbo.RicettePresenze.IdRicettaCodice, RicettePresenze_1.QuantitaErogate
					HAVING      (dbo.RicettePresenze.IdRicettaCodice = $idcampo) AND (RicettePresenze_1.QuantitaErogate > 0)";
		
					//echo($query_1);
		$rs_1 = mssql_query($query_1, $conn);
		if($row_1=mssql_fetch_assoc($rs_1)){
			$max=$row_1['data_ultima'];			
			$pres=$row_1['presenze'];			
		}
		if($max!="")
			$max=formatta_data($max);
			else
			$max="nessuna";
		if($pres=="")$pres="0";	
		echo($pres."/".$richieste." - ".$max);
		mssql_free_result($rs_1);
		?>		
		</td>	  	
		<td id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev('<?=$idcampo?>');"><img src="images/view.png" /></a></td>
		<td id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev_plan('<?=$idcampo?>');"><img src="images/calendar.png" /></a></td>
		<input type="hidden" id="el<?=$i?>" name="elimina<?=$i?>" value="" />
		<input type="hidden" id="idcampo<?=$i?>" name="idcampo<?=$i?>" value="<?=$idcampo?>" />
		<?
		if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and (($_SESSION['UTENTE']->get_tipoaccesso()!=5)))
		{?>
		<td><a OnClick="javascript:if (confirm('sei sicuro di voler cancellare la tipologia corrente?')){document.getElementById('el<?=$i?>').value='si';DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');}"><img src="images/remove.png" /></a></td>
		<?}?>
		</tr>
		
	<?
	$i++;
	}
	while($i<$numerofield)
	{
		
		if ($i>1)
		{
		$style="style=\"display:none\"";
		$class="riga_sottolineata_diversa hidden";
		$dis="disabled";
		
		}
		else
		{
		$nodrag="nodrag=false";
		$style='';
		$class="riga_sottolineata_diversa";
		$dis="";
		}
		?>		
		<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
		<td align="center">&nbsp;</td>
		<td align="center">
		<select name="pac_terapia<?=$i?>">						
				<? if($idpacchetto!=""){?><option value="<?=$codice1?>" <? if($codice1==$pacchetto) echo("selected");?>><?=$codice1?></option><?}
				else{?>
					<option value=""></option>
				<?}?>
				<? if($idpacchetto1!=""){?><option value="<?=$codice2?>" <? if($codice2==$pacchetto) echo("selected");?>><?=$codice2?></option><?}?>			
		</select>
		</td>
		<td align="center">
			<select id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" OnClick="javascript:addSelect(getElementById('RegimeAssistenza').value,'<?=$i?>');">
			<option value="0">seleziona una tipologia</option>
			</select>
		</td>
		<td align="center" class="nodrag nodrop nomandatory integer"><input style="width:30px;" id="ric<?=$i?>" name="richiesti<?=$i?>" value="" /></td>
		<td align="center" class="nodrag nodrop nomandatory integer">
			<select id="freq<?=$i?>" name="frequenza<?=$i?>">
			<option value="0">non definita</option>
			<?
			$query = "SELECT * FROM ElencoFrequenze order by TipoFrequenza";	
			$rs = mssql_query($query, $conn);
			if(!$rs) error_message(mssql_error());
			while($row = mssql_fetch_assoc($rs)) {
				?>
				<option value="<?=$row['IdFrequenza']?>"><?=$row['TipoFrequenza']?></option>
				 <?
			 }
			 mssql_free_result($rs);
			 ?>
			</select>
		</td>
		<td align="center" class="nodrag nodrop">nessuna</td>	  	
		<td>&nbsp;</td>
		<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a></td>
		</tr>
	<?
	$i++;
	}

	?>

	<tr id="1000" class="riga_sottolineata_diversa nodrag nodrop"><td align="center"><span><a OnClick="javascript:DinamicDiv('0','add','<?=$numerofield?>');"><img src="images/add.png" /></a></td></tr>
	</tbody>
	</table>
	</div>
	<div class="titolo_pag"><h1>Dati relativi alla chiusura dell'impegnativa</h1></div>
	<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">data chiusura</div>
				<div class="campo_mask data_passata">
					<input type="text" class="campo_data" name="DataDimissione" value="<?=$DataDimissione?>" readonly />
				</div>
				
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">motivazione</div>
				<div class="campo_mask">
						<?
				$query = "SELECT id, motivazione FROM tbl_motivo_chiusura_impegnativa order by motivazione";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="MotivoDimissione" class="scrittura textsmall">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
						$sel="";
						if ($MotivoDimissione==$row['id'])
						$sel="selected";
						
					?>
		                <option <?=$sel?> value="<?=$row['id']?>"><?=pulisci_lettura($row['motivazione'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
			
			<div class="rigo_mask">
				<div class="testo_mask">motivazione fine trattamento</div>
				<div class="campo_mask">
						<?
				$query = "SELECT id, motivazione FROM tbl_motivo_trattamento order by motivazione";	
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				?>
					<select name="MotivoDimissione" class="scrittura textsmall">
					<option value="0">selezionare</option>
					<?  while($row = mssql_fetch_assoc($rs))   
					{
						$sel="";
						if ($MotivoTrattamento==$row['id'])
						$sel="selected";
						
					?>
		                <option <?=$sel?> value="<?=$row['id']?>"><?=pulisci_lettura($row['motivazione'])?></option>
		             <?
					 }
					 mssql_free_result($rs);
					 ?>
					 
					</select>
				</div>
			</div>
		</div>		
		<div class="riga">
			<div class="rigo_mask rigo_big">
				<div class="testo_mask">note di chiusura</div>
				<div class="campo_mask">
					<input type="text" class="scrittura h_small" name="note_dimissione" value="<?=$note_dimissione?>"/>
				</div>
			</div>
	</div>
	<div class="titolo_pag"><h1>Report trattamenti</h1></div>
	<div class="riga">
	<span class="rigo_mask" id="trat_equipe">
	caricamento in corso...
	</span>
	</div>	
	 <?php
		$query = "SELECT  dbo.modelli_word.id, dbo.modelli_word.codice, dbo.modelli_word.modello, dbo.modelli_word.stato, dbo.modelli_word.etichetta, dbo.modelli_word.tipo,dbo.modelli_word_regime.id_regime
				FROM dbo.modelli_word LEFT OUTER JOIN
                dbo.modelli_word_regime ON dbo.modelli_word.id = dbo.modelli_word_regime.id_modello
				WHERE (tipo = '1') AND (stato = 1) AND (id_regime=$idregime)";
		//echo($query);
		$rs = mssql_query($query, $conn);
		if(mssql_num_rows($rs)>0){
		?>
	
	<div class="titolo_pag"><h1>Comunicazioni al MMG</h1></div>
	<div class="riga">
	<?php
		while($row=mssql_fetch_assoc($rs)){
	?>
		<span class="rigo_mask" >
		<div class="testo_mask">stampa modello</div>
		<div class="box"><a class="rounded {transparent, anti-alias} button2 " href="stampa_modulo.php?modello=<?=$row['codice']?>&idpaziente=<?=$idutente?>&idimpegnativa=<?=$idimpegnativa?>"><?=$row['etichetta']?></a></div>
		</span>
	<?}?>
	</div>	
	<?}?>

	
	</div>
	
    <? 
	
	if (($_SESSION['UTENTE']->get_tipoaccesso()!=4) and (($_SESSION['UTENTE']->get_tipoaccesso()!=5)))
	{
	?>
    <div class="titolo_pag">
		<div class="comandi">
			<input type="submit"  title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	<?
	}
	else
	print("<p>Non si dispone dei permessi necessari per modificare l'impegnativa</p>");
	?>
	</form>


	
	<script type="text/javascript">
	
function hideListaIcd9(CodiceDiagnosi){
	document.getElementById('idIcd9').value = CodiceDiagnosi;
	$('#selectICD9').css('display','none');
}
	
function showListaIcd9(value){
	$('#selectICD9').css('display','block');
	$.ajax({
			type: "POST",
		   url: "get_icd9.php?value="+value,
		   data: "",		   
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 $("#selectICD9").html(trim(msg));
			}
		 });
}	
	
function findValue(li) {
	if( li == null ) return alert("No match!");

	// if coming from an AJAX call, let's use the CityId as the value
	if( !!li.extra ) var sValue = li.extra[0];

	// otherwise, let's just display the value in the text box
	else var sValue = li.selectValue;

	//alert("The value you selected was: " + sValue);
}

function selectItem(li) {
	findValue(li);
}

function formatItem(row) {
	return row[0] + " (id: " + row[1] + ")";
}

/*function lookupAjax(){
	var oSuggest = $("#CityAjax")[0].autocompleter;

	oSuggest.findValue();

	return false;
}*/

function lookupLocal(){
	var oSuggest = $("#CityLocal")[0].autocompleter;

	oSuggest.findValue();

	return false;
}

function checkDate(value,id){
	var idVal = $("#"+id).val();	
	//if(idVal>value) alert("Attenzione, la data inserita deve essere maggiore della data di produzione.");
}
function getDate() {
  var date = new Date();
  var day = date.getDate();
  if(day<9) day="0"+day;
  var month = date.getMonth() + 1;
  if(month<10) month="0"+month;
  var year = date.getFullYear();  
  data= day + "/" + month + "/" + year;  
  return data;
 }
 
 function setNow(id){
	var data=getDate();	
	$("#"+id).val(data);
 }

</script>

	
	
	<script type="text/javascript" language="javascript">
	function trattamenti_equipe(id,idimpegnativa){
		$.ajax({
			type: "POST",
		   url: "ajax_trattamenti.php?id_paziente="+id+"&idimpegnativa="+idimpegnativa,
		   data: "",		   
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 $("#trat_equipe").html(trim(msg));
			}
		 });
	}
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				$(".campo_numerico").validation({ type: "int" });
				trattamenti_equipe(<?=$idutente?>,<?=$idimpegnativa?>);	
				//<?if($protocollo_dt=='on'){?> 
				//	$('#s_t').slideDown();
				//	<?}else{?>
				//	$('#s_t').slideUp();
				//	<?}?>
	
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
	
	function addSelect(id,sel){	
	//var html="<option value='1'>prova "+id+"</option>";	
	//alert($("#et"+sel).val()+" "+id);
	 if(id!=""){
	// if((jQuery.trim($("#et"+sel).text())=="seleziona una tipologia")||(jQuery.($("#et"+sel).text())=="aggiorna elenco...")){
	if($("#et"+sel).val()==0){	
		$.ajax({
		   type: "POST",
		   url: "tipologie_ajax.php",
		   data: "idregime="+id,
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 $("#et"+sel).html(trim(msg));
			}
		 });
	}
	}		
	}
	
</script>
	
	
</div>	
	
	
	
<?php
 }
 

 if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
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
	print("ciao");
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>