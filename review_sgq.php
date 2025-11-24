<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');



function show_list()
{
$conn = db_connect();
$user=$_SESSION['UTENTE']->get_userid();
$query = "SELECT * FROM review_sgq WHERE id_utente=$user";
//echo($query);
$result = mssql_query($query, $conn);
if($result){
    $row = mssql_fetch_assoc($result);
    $giorni_successivi=$row['giorni_successivi'];
	//echo("giorni_successivi");
	//echo($giorni_successivi);
    if($giorni_successivi=='y'){
		$valore_giorni_successivi=$row['valore_giorni_successivi'];
     //   echo('valore_giorni_successivi');
	//	echo($valore_giorni_successivi);
		
	}
    $giorni_scadenze=$row['giorni_scadenze'];
	//echo('giorni_scadenze');
	//echo($giorni_scadenze);
    if($giorni_scadenze=='y'){
        $valore_giorni_scadenze=$row['valore_giorni_scadenze'];
      //  echo('valore_giorni_scadenze'); 
      // echo($valore_giorni_scadenze); 
   }
   $giorni_alert=$row['giorni_alert'];
   //echo('giorni_alert');
   //echo($giorni_alert);
   if($giorni_alert=='y'){
        $valore_giorni_alert=$row['valore_giorni_alert'];
	//	echo('valore_giorni_alert');
		//echo($valore_giorni_alert);
		
   }
}//exit();
$conta=0;
?>
<script type="text/javascript" id="js">
inizializza();
var popupStatus = 0;

function add_modulo(idarea,idmodulo,idscadenza,data)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="review_sgq.php?do=aggiungi_modulo&idarea="+idarea+"&idmodulo="+idmodulo+"&idscadenza="+idscadenza+"&data="+data;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
function prev(id){	
	//centering with css
	loadPopup(id);	
	//load popup
}

//loading popup with jQuery magic!
function loadPopup(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('carica_preview.php?&do=&id='+id, function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});					
		popupStatus = 1;
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

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});		
		popupStatus = 0;
	}
}
function gestione_review(utente){	
	//centering with css
	loadPopup_review(utente);
	centerPopup();
	//load popup


}
//loading popup with jQuery magic!
function loadPopup_review(utente){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('review_sgq.php?&do=gestione_review&utente='+utente, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}
</script>
<?


$datadioggi = date('d/m/Y');

//query per le date scadute non inserite
$query_scadenze = "SELECT * FROM re_operatori_rewiew_scadenze_sgq WHERE (cancella='n') AND (responsabile='$user') AND (data_scadenza_generata < '$datadioggi')";
//echo($query_scadenze);

$rs_scadenze = mssql_query($query_scadenze, $conn);
$n_scadenze=mssql_num_rows($rs_scadenze);
//echo($n_scadenze);
//exit();
//controllo sul configura review SGQ
if($giorni_successivi=='y'){
	$data_giorni_successivi= date ("d/m/Y", mktime(0,0,0,date("m"),date("d")+$valore_giorni_successivi,date("Y")));
	//echo($data_giorni_successivi);
	//exit();
    $query_conf = "SELECT * FROM re_operatori_rewiew_scadenze_sgq WHERE (cancella='n') AND (responsabile='$user') AND (data_scadenza_generata <= '$data_giorni_successivi')order by data_scadenza_generata ";
   // echo($query_conf);
	//exit();
	if($giorni_scadenze=='y'){
	    $valore_giorni_scadenze = $valore_giorni_scadenze + $n_scadenze;
	    $query_conf = "SELECT TOP $valore_giorni_scadenze * FROM re_operatori_rewiew_scadenze_sgq WHERE (cancella='n') AND (responsabile='$user') AND (data_scadenza_generata < '$data_giorni_successivi')order by data_scadenza_generata ";
	}
}elseif($giorni_scadenze=='y'){
    $valore_giorni_scadenze = $valore_giorni_scadenze + $n_scadenze;
    $query_conf = "SELECT TOP $valore_giorni_scadenze * FROM re_operatori_rewiew_scadenze_sgq WHERE (cancella='n') AND (responsabile='$user')order by data_scadenza_generata ";
}
if(!$query_conf) $query_conf = "SELECT * FROM re_operatori_rewiew_scadenze_sgq WHERE (cancella='n') AND (responsabile='$user') order by data_scadenza_generata ";
//echo($query_conf);
//exit();
$rs = mssql_query($query_conf, $conn);
//echo($query_conf);
//exit();
$conta=mssql_num_rows($rs);

if(!$rs) error_message(mssql_error());
?>

<div id="popupdati2"></div>
<div id="backgroundPopup"></div>

<div id="wrap-content"><div class="padding10">
<div id="cartella_clinica">
<div class="logo"><img src="images/re-med-logo.png" /></div>

<?
if(mssql_num_rows($rs)==0){
?>
	<div class="titoloalternativo">
        <h1>Review SGQ</h1>
    </div>
	<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:gestione_review('<?=$user?>');" href="#">Configura Review SGQ</a></div>
		</div>
	</div>
	<div>
	<div class="info">Non ci sono scadenze da compilare.</div>
	</div>
	<?
	exit();}?>
	<div class="titoloalternativo">
        <h1>Review SGQ</h1>
    </div>
	
	
	<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:gestione_review('<?=$user?>');" href="#">Configura Review SGQ</a></div>
		</div>
	</div>
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th width="15%" >Stato</th> 
    <th>area</th> 
	<th>modulo SGQ</th> 
    <th width="9%">scadenza associata</th>
	<td width="5%">anteprima</td>
	<td width="5%">compila</td>
</tr>   
</thead> 
<tbody> 
<?

while($row = mssql_fetch_assoc($rs))
{       
        $id_scadenze_generate_sgq=$row['id_scadenze_generate_sgq'];
        $idmodulo=$row['idmodulo'];
		$id_modulo_versione =$row['id_modulo_versione'];
		$replica=$row['replica'];
		$idarea=$row['idarea'];
		$nome_area=$row['nome_area'];
		$nome_modulo=$row['nome_modulo'];
		$data_scadenza_generata=formatta_data($row['data_scadenza_generata']);
		
		//controllo alert
		$stato="";
		$diff_in_giorni=diff_in_giorni($data_scadenza_generata,$datadioggi);
		if($giorni_alert=='y'){
			//$diff_in_giorni=diff_in_giorni($data_scadenza_generata,$datadioggi);
		    if($diff_in_giorni < $valore_giorni_alert){
			    $stato=1;//"immagine di alert";
			}
		}
		if($diff_in_giorni < 0){
			    $stato=2;//"immagine relativa ad una scadenza non inserita";
		}
		
		$query_area="select * from re_moduli_aree_nome_sgq where idarea=$idarea order by nome asc";
		$rs_area = mssql_query($query_area, $conn);
		if(!$rs_area)
		{		
			echo("no");
			exit();
			die();
		}
		?>
		<tr> 
		    <td style="vertical-align: middle;">
			<?	if($stato==2) { ?>
					<img src="images/scadenza_non_rispettata.png"  title="Scadenza non rispettata" /> Scadenza non rispettata
			<?	} elseif($stato==1) { 
					$msg = $diff_in_giorni == 0 ? 'Scade oggi' : ("Scade tra $diff_in_giorni " . ($diff_in_giorni > 1 ? 'giorni' : 'giorno')); 
			?>
					<img src="images/alert_scadenza.png" title="<?= $msg ?>"/> <?= $msg ?>
			<?
				}
			?>
			</td> 
		    <td style="vertical-align: middle;" ><?=$nome_area?></td> 
		    <td style="vertical-align: middle;" ><?=$nome_modulo?></td> 
			<td style="vertical-align: middle; text-align:center;" >
			<?
			if  ((get_permesso_modulo($idmodulo,$_SESSION['UTENTE']->get_gid())) and  (($replica==1) or (($replica==0) and (trim($ultima_compilazione)==''))))
			{ ?>
                <a 
					<? if(mssql_num_rows($rs_area)>0) { ?> href="#"  <? if($diff_in_giorni+1==1){?>title="scade tra <?=$diff_in_giorni+1;?> giorno "<?}elseif($diff_in_giorni>0){?> title="scade tra <?=$diff_in_giorni+1; ?> giorni"<?}else{?>title="scadenza non rispettata "<?}?>onclick="javascript:add_modulo('<?=$idarea?>','<?=$id_modulo_versione?>','<?=$id_scadenze_generate_sgq?>','<?=$data_scadenza_generata?>');" ><?=$data_scadenza_generata;}?></a>
        <? }else {
                echo($data_scadenza_generata);
			}?>
			</td>
			
		    <td style="vertical-align: middle;">
            <?
			if  ((get_permesso_modulo_vis($idmodulo,$_SESSION['UTENTE']->get_gid())) and  (($replica==1) or (($replica==0) and (trim($ultima_compilazione)==''))))
			{
                
              ?>
                <a <?if(mssql_num_rows($rs_area)>0) { ?>  href="#" onclick="javascript:prev('<?=$id_modulo_versione?>');" title="anteprima modulo"><img src="images/view.png" /><?}?></a>
                 <? }else {
                       echo("&nbsp;");
					}?>
			</td>
			
			<td style="vertical-align: middle;">			
			<?
			if  ((get_permesso_modulo($idmodulo,$_SESSION['UTENTE']->get_gid())) and  (($replica==1) or (($replica==0) and (trim($ultima_compilazione)==''))))
			{
             ?>
                <a <?if(mssql_num_rows($rs_area)>0) { ?>  href="#" title="compila modulo" onclick="javascript:add_modulo('<?=$idarea?>','<?=$id_modulo_versione?>','<?=$id_scadenze_generate_sgq?>','<?=$data_scadenza_generata?>');" ><img src="images/add.png" /><?}?></a>
                 <? }else {
                       echo("&nbsp;");
					}?>
			
			</td>
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
exit();
}
?>


<?

function aggiungi_modulo()
{
$idarea=$_REQUEST['idarea'];
//echo("idarea");
//echo($idarea);

$idmoduloversione=$_REQUEST['idmodulo'];
//echo("idmoduloversione");
//echo($idmoduloversione);

$idscadenza=$_REQUEST['idscadenza'];
//echo("idscadenza");
//echo($idscadenza);

$data_scadenza_generata=$_REQUEST['data'];
//echo("data_scadenza_generata");
//echo($data_scadenza_generata);

//exit();

$opeins=$_SESSION['UTENTE']->get_userid();

$conn = db_connect();
$query="select * from max_vers_moduli_sgq where idmoduloversione_new=$idmoduloversione";
$rs = mssql_query($query, $conn);
	
if(!$rs)
{		
				echo("no");
				exit();
				die();
	}

if($row = mssql_fetch_assoc($rs))   
	$idmoduloversione_new=$row['idmoduloversione_new'];
	$nome_modulo=pulisci_lettura(trim($row['nome']));	
	
$query="select * from campi where idmoduloversione=$idmoduloversione_new order by peso asc";	

$rs1 = mssql_query($query, $conn);
if(!$rs1)
{		
	echo("no");
	exit();
	die();
}
?>
<script>
    inizializza();
    
    function ritorna(idarea,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="review_sgq.php";
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	}
</script>
<div id="cartella_clinica">
<div class="logo"><img src="images/re-med-logo.png" /></div>
<!-- qui va il codice dinamico dei campi -->
<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna('<?=$idarea?>','cartella_clinica')" >ritorna alla lista dei moduli</a></div>			
	</div>
</div>

	<div class="titolo_pag"><h1>Creazione modulo: <?=$nome_modulo?></h1> <h1>Scadenza associata: <?=$data_scadenza_generata?></h1>	</div>	
	
	
<form id="myForm" method="post" name="form0" action="review_sgq_POST.php" enctype="multipart/form-data">
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>
	<input type="hidden" name="action" value="create_modulo" />
	<input type="hidden" name="idarea" value="<?=$idarea?>" />	
	<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
	<input type="hidden" name="idscadenza" value="<?=$idscadenza?>" />

	<div class="blocco_centralcat">
    <?if($numScadenze>0){?>	
        </div>
    </div>        
    <?
    }
		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		$i=1;
		
		while($row1 = mssql_fetch_assoc($rs1)){

			$idcampo= $row1['idcampo'];
			$etichetta= pulisci_lettura($row1['etichetta']);
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
		
			if ($obbligatorio=='y')
			$classe="mandatory ".$classe;

			$multi=0;
			if($tipo==4){
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3)
			{		
				echo("no");
				exit();
				die();
	}
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
	
			if(($i%3==1)and ($tipo!=5)){ 
				echo("<div class=\"riga\">");
				$div=1;
			}
			if((($tipo==2)or($multi==1)) and(($i%3==2)or($i%3==0))){
				echo("</div><div class=\"riga\">");
			}
				if(($tipo==5)and(($i%3==2)or($i%3==0))){
				echo("</div><div>");
			}
			if($multi==1) $stile="style=\"width:600px;\"";
		?>
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<!-- commentare per etichetta-->
			<div class="etichetta_campi"><?=$etichetta?></div>
			<input type="hidden" class="scrittura" name="<?=$idcampo?>" >	
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5))  echo("rigo_big");?>"<?php if($multi==1) echo($stile);?>>
            <div class="testo_mask"><?=$etichetta?></div>            
			<div class="campo_mask <?=$classe?>">
                <?if($tipo==1){?>
					<input type="text" class="scrittura" name="<?=$idcampo?>" >
				  <?}
				     elseif($tipo==2)
					 {?>
					<textarea class="scrittura_campo" name="<?=$idcampo?>" ></textarea>
				  <?}
				    elseif(($tipo==3) or($tipo==6))
					 {?>
					<input type="text" class="scrittura campo_data" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==7)
					 {
					 ?>
					<input type="file" class="scrittura_campo" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==8)
					 {?>
					<input type="text" class="scrittura_campo" name="<?=$idcampo?>" value="<?=$titolo_medico.$nome_medico?>">
				  <?}
				   elseif($tipo==4)
					 {
						$query2="select * from re_moduli_combo where idcampo=$idcampo and stato=1";	
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2)
						{		
				echo("no");
				exit();
				die();
	}
					 if($multi==1){
						$xy=1;	
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);
						?>					
					<div style="float:left; padding:0 10px 0 0;"><input type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=$etichetta?></div>
					<?
						$xy++;
							}						
					 }else{?>
						<select class="scrittura" name="<?=$idcampo?>">
						<option value="">Selezionare un Valore</option><?
					 }										
					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$valore=pulisci_lettura($row2['valore']);
                        $etichetta=pulisci_lettura($row2['etichetta']);
					?>
					<option value="<?=$valore?>"><?=$etichetta?></option>
					<?}?>
					</select>
				  <?}
                  elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo and stato=1";	
						
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$xy=1;	
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);
						?>					
					<div style="float:left; padding:0 10px 0 0;clear:both;"><input type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=$etichetta?></div>
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
						$valore=pulisci_lettura($row2['valore']);
                        $etichetta=pulisci_lettura($row2['etichetta']);
						?>
						<option value="<?=$idcampocombo?>"><?=$etichetta?> </option>     
						<!--<optgroup label="<?=$etichetta?>">-->
						<?
						$rinc=rincorri_figlio($idc,$idcampocombo,"",""); 
						?><!--</optgroup>--><?	
                        
					/*
					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$valore=$row2['valore'];
						$etichetta=$row2['etichetta'];
					?>
					<option value="<?=$valore?>"><?=$etichetta?></option>
					<?}*/	
					}?>
					</select>
				  <?}?>
            </div>
			<?}?>
		</div>	
   
	<?
	if(($tipo==2)or($multi==1)) $i=0;
	if($i%3==0){ 
		echo("</div>");
		$div=0;}
	if($tipo!=5) $i++;	
	}	
	if($div==1) echo("</div>");				
	?>
		</div>
<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
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
<?
exit();
}

function gestione_review(){
?>
<script>inizializza();</script>

<script type="text/javascript" id="js">

var popupStatus = 0;

function add_modulo(idarea,idmodulo,idscadenza,data)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="review_sgq.php?do=aggiungi_modulo&idarea="+idarea+"&idmodulo="+idmodulo+"&idscadenza="+idscadenza+"&data="+data;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
function prev(id){	
	//centering with css
	loadPopup(id);	
	//load popup
}

//loading popup with jQuery magic!
function loadPopup(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('carica_preview.php?&do=&id='+id, function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});					
		popupStatus = 1;
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

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});		
		popupStatus = 0;
	}
}
function gestione_review(utente){	
	//centering with css
	loadPopup_review(utente);
	centerPopup();
	//load popup


}
//loading popup with jQuery magic!
function loadPopup_review(utente){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('review_sgq.php?&do=gestione_review&utente='+utente, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}
</script>

<script type="text/javascript" id="js">

//disabling popup with jQuery magic!
function disablePopup_force(){
	//disables popup only if it is enabled
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
}
function addGiorniSuccessivi(){
     $('#giorni_successivi_mandatory').addClass('mandatory integer');
     $('#numero_giorni').slideDown();
    }
function azzeraGiorniSuccessivi(){
     $('#giorni_successivi_mandatory').removeClass('mandatory integer');
     document.getElementById("giorni").value="";
	 $('#numero_giorni').slideUp();
    }
function addGiorniScadenze(){
     $('#giorni_scadenze_mandatory').addClass('mandatory integer');
     $('#numero_scadeze').slideDown();
    }
function azzeraGiorniScadenze(){
     $('#giorni_scadenze_mandatory').removeClass('mandatory integer');
     document.getElementById("giorni_s").value="";
	 $('#numero_scadeze').slideUp();
    }
function addGiorniAlert(){
     $('#giorni_alert_mandatory').addClass('mandatory integer');
     $('#numero_giorni_alert').slideDown();
    }
function azzeraGiorniAlert(){
     $('#giorni_alert_mandatory').removeClass('mandatory integer');
     document.getElementById("giorni_a").value="";
	 $('#numero_giorni_alert').slideUp();
    }
</script>

<?
$utente=$_REQUEST['utente'];
//echo($utente);
//exit();
$conn = db_connect();
$query = "SELECT * FROM review_sgq WHERE (id_utente=$utente)";
$result = mssql_query($query, $conn);
$record_trovati=mssql_num_rows($result);
$giorni_successivi='n';
$giorni_scadenze='n';
$giorni_alert='n';
if(($record_trovati>0))
    {
	if($row = mssql_fetch_assoc($result)){
        $giorni_successivi=$row['giorni_successivi'];
        $valore_giorni_successivi=$row['valore_giorni_successivi'];
        $giorni_scadenze=$row['giorni_scadenze'];
        $valore_giorni_scadenze=$row['valore_giorni_scadenze'];
        $giorni_alert=$row['giorni_alert'];
        $valore_giorni_alert=$row['valore_giorni_alert'];
    }else{
        echo("no");
        exit();
        die();
    }
	//echo("qui");
	//exit();
	}
//echo($query);
//exit();
$result1 = mssql_query($query, $conn);
?> 

<div id="wrap-content">
    <div class="padding10">
        <div class="logo"><img src="images/re-med-logo.png" /></div>
	    <div class="titoloalternativo">
            <h1>Configura Review SGQ</h1>
        </div>
        <div class="titolo_pag">
	        <div class="comandi" style="float:right;">
                <div class="elimina"><a href="#" onclick="javascript:disablePopup_force();">chiudi scheda</a></div>
	        </div>
        </div>

<form  method="post" name="form0" action="review_sgq_POST.php" id="myForm" >
	<input type="hidden" value="configura_review_sgq" name="action"/>
	<div class="blocco_centralcat">
	<div class="riga">
        <div class="rigo_mask">
            <div class="testo_mask">giorni da controllare </div>
            <div class="campo_mask ">
                <input id="giorni_successivi" type="radio" name="giorni_successivi" value="y" onclick="javascript:addGiorniSuccessivi();" <?if($giorni_successivi=='y'){?>checked<?}?>/> si 
                <input id="giorni_successivi" type="radio" name="giorni_successivi" value="n" class="nomandatory" onclick="javascript:azzeraGiorniSuccessivi();" <?if($giorni_successivi=='n'){?>checked<?}?> /> no
			</div>
        </div>
        <div class="rigo_mask">
		
		<div class="nomandatory">
		    <input type="hidden" name="nomandatory" value="n" />
	    </div>
            <div id="numero_giorni"  <?if($giorni_successivi=='y'){?>style="display:block;"<?}else{?>style="display:none;"<?}?> >
                <div id="giorni_successivi_mandatory" <?if($giorni_successivi=='y'){?>class="mandatory integer"<?}?>  >
                    <div class="testo_mask">numero giorni</div>
					<input type="text" size="3" maxlength="3" name="giorni" id="giorni" <?if($giorni_successivi=='y'){?>value="<?=$valore_giorni_successivi?>"<?}?> />						
                </div>
            </div>
        </div>
    </div>  
	<div class="riga">
	<div class="rigo_mask">
	<div class="nomandatory">
		                <input type="hidden" name="nomandatory" value="n" />
	                </div>
            <div class="testo_mask">scadenze da visualizzare</div>
            <div class="campo_mask ">
                <input id="giorni_scadenze" type="radio" name="giorni_scadenze" value="y" onclick="javascript:addGiorniScadenze();"<?if($giorni_scadenze=='y'){?>checked<?}?> /> si 
                <input id="giorni_scadenze" type="radio" name="giorni_scadenze" value="n"class="nomandatory" onclick="javascript:azzeraGiorniScadenze();"<?if($giorni_scadenze=='n'){?>checked<?}?> /> no				   
            </div>
        </div>
        <div class="rigo_mask">
                <div id="numero_scadeze"  <?if($giorni_scadenze=='y'){?>style="display:block;"<?}else{?>style="display:none;"<?}?> >
                    <div id="giorni_scadenze_mandatory"   <?if($giorni_scadenze=='y'){?>class="mandatory integer"<?}?>  >
                        <div class="testo_mask">numero scadenze</div>
					    <input type="text" size="3" maxlength="3" name="giorni_s" id="giorni_s" <?if($giorni_scadenze=='y'){?>value="<?=$valore_giorni_scadenze?>"<?}?> />										
                    </div>
               
    			</div>
		</div>
	</div>
	<div class="riga">
        <div class="rigo_mask">
            <div class="testo_mask">preavviso scadenza</div>
            <div class="campo_mask ">
                <input id="giorni_alert" type="radio" name="giorni_alert" value="y" onclick="javascript:addGiorniAlert();" <?if($giorni_alert=='y'){?>checked<?}?>  /> si 
                <input id="giorni_alert" type="radio" name="giorni_alert" value="n" class="nomandatory" onclick="javascript:azzeraGiorniAlert();" <?if($giorni_alert=='n'){?>checked<?}?> /> no
            </div>
        </div>
        <div class="rigo_mask">
		<div class="nomandatory">
		    <input type="hidden" name="nomandatory" value="n" />
	    </div>
                <div id="numero_giorni_alert" <?if($giorni_alert=='y'){?>style="display:block;"<?}else{?>style="display:none;"<?}?> >
                    <div id="giorni_alert_mandatory"  <?if($giorni_alert=='y'){?>class="mandatory integer"<?}?>  >
                        <div class="testo_mask">numero giorni</div>
					    <input type="text" size="3" maxlength="3" name="giorni_a" id="giorni_a"<?if($giorni_alert=='y'){?>value="<?=$valore_giorni_alert?>"<?}?> />														
                    </div>
               
    			</div>
        </div>
    </div>  

	<div class="titolo_pag">
		<div class="comandi">
			<input  type="submit" title="conferma" value="conferma" class="button_salva" />
		</div>
	</div>
	</div>
	</form>
</div>
</div>

<?
exit();
}



if(isset($_SESSION['UTENTE'])){
	if(!isset($do)) $do='';
	$back = "review_sgq.php";	
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {
         //i post di questa pagina sono in review_sgq_POST.php
		}

		
		switch($do) {
			
			case "aggiungi_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_modulo();
						
			break;
			case "gestione_review":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else 
					gestione_review();
						
			break;

			default:
				show_list();
			break;
		}
			html_footer();
	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	print("non hai i permessi per visualizzare questa pagina!");
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>




