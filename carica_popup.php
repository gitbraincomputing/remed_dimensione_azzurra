<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 11;
include_once('include/function_page.php');
session_start();
//$idutente=$id;


function show_list_med($idpaziente){

	$conn = db_connect();
	$query ="SELECT * from re_pazienti_medici where IdUtente=$idpaziente order by id desc";	
	$rs = mssql_query($query, $conn);
	
	while($row = mssql_fetch_assoc($rs)){
	
		$nome=stripslashes($row['nome']);
		$cognome=stripslashes($row['cognome']);
		$indirizzo=stripslashes($row['indirizzo']);
		$cap=stripslashes($row['cap']);
		$comune=stripslashes($row['denominazione']);
		$nome=stripslashes($row['nome']);
		$provincia=stripslashes($row['nome_prov']);
		$fax=stripslashes($row['fax']);
		$telefono=stripslashes($row['telefono']);
		$cellulare=stripslashes($row['cellulare']);
		$email=stripslashes($row['email']);
		$datains=formatta_data($row['datains']);
		$decorrenza=formatta_data($row['decorrenza']);
		
		?>
		
		
		<div class="titolo_pag">
		    <div class="comandi">
	        	<h1>Medico alla data del <?=formatta_data($datains)?></h1>
	        </div>
			<div class="comandi">
				<div id="aggiungi_tutor" class="aggiungi popupdatiClose"><a>chiudi scheda</a></div>
			</div>
		</div>
		<div class="blocco_centralcat">
	    	<div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">nome</div>
	            <div class="campo_mask"><?=$nome?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">cognome</div>
	            <div class="campo_mask"><?=$cognome?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">indirizzo</div>
	            <div class="campo_mask"><?=$indirizzo?></div>
	            </div>
	         </div>
			<div class="riga">	           
	            <div class="rigo_mask">
	            <div class="testo_mask">provincia</div>
	            <div class="campo_mask"><?=$provincia?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">comune</div>
	            <div class="campo_mask"><?=$comune?></div>
	            </div>
				<div class="rigo_mask">
	            <div class="testo_mask">cap</div>
	            <div class="campo_mask"><?=$cap?></div>
	            </div>
	         </div>
			 <div class="riga">
	            
	            <div class="rigo_mask">
	            <div class="testo_mask">telefono</div>
	            <div class="campo_mask"><?=$telefono?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">fax</div>
	            <div class="campo_mask"><?=$fax?></div>
	            </div>
				 <div class="rigo_mask">
	            <div class="testo_mask">cellulare</div>
	            <div class="campo_mask"><?=$cellulare?></div>
	            </div>
	         </div>
			 <div class="riga">	           
	            <div class="rigo_mask">
	            <div class="testo_mask">mail</div>
	            <div class="campo_mask"><?=$email?></div>
	            </div>
				 <div class="rigo_mask">
	            <div class="testo_mask">decorrenza</div>
	            <div class="campo_mask"><?=$decorrenza?></div>
	            </div>	
	         </div>
	    </div>
	<script>	
		
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
}	


function show_list_tut($idpaziente){

	$conn = db_connect();
	$query ="SELECT * from re_pazienti_tutor where IdUtente=$idpaziente order by id desc";	
	$rs = mssql_query($query, $conn);
	
	while($row = mssql_fetch_assoc($rs)){
	
		$nome=stripslashes($row['nome']);
		$cognome=stripslashes($row['cognome']);
		$indirizzo=stripslashes($row['indirizzo']);
		$cap=stripslashes($row['cap']);
		$comune=stripslashes($row['denominazione']);
		$nome=stripslashes($row['nome']);
		$provincia=stripslashes($row['nome_prov']);
		$fax=stripslashes($row['fax']);
		$telefono=stripslashes($row['telefono']);
		$cellulare=stripslashes($row['cellulare']);
		$email=stripslashes($row['email']);
		$relazione=stripslashes($row['relazione_paziente']);
		$datains=$row['datains'];
		$disattivato=formatta_data($row['disattivato']);
		?>		
		
		<div class="titolo_pag">
		    <div class="comandi">
	        	<h1>Tutor alla data del <?=formatta_data($datains)?></h1>
	        </div>
			<div class="comandi">
				<div id="aggiungi_tutor" class="aggiungi popupdatiClose"><a>chiudi scheda</a></div>
			</div>
		</div>
		<div class="blocco_centralcat">
	    	<div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">cognome</div>
	            <div class="campo_mask"><?=$cognome?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">nome</div>
	            <div class="campo_mask"><?=$nome?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">indirizzo</div>
	            <div class="campo_mask"><?=$indirizzo?></div>
	            </div>
	         </div>
			<div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">provincia</div>
	            <div class="campo_mask"><?=$provincia?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">comune</div>
	            <div class="campo_mask"><?=$comune?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">cap</div>
	            <div class="campo_mask"><?=$cap?></div>
	            </div>
	         </div>
			 <div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">cap</div>
	            <div class="campo_mask"><?=$cap?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">telefono</div>
	            <div class="campo_mask"><?=$telefono?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">fax</div>
	            <div class="campo_mask"><?=$fax?></div>
	            </div>
	         </div>
			 <div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">telefono</div>
	            <div class="campo_mask"><?=$telefono?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">fax</div>
	            <div class="campo_mask"><?=$fax?></div>
	            </div>
				  <div class="rigo_mask">
	            <div class="testo_mask">cellulare</div>
	            <div class="campo_mask"><?=$cellulare?></div>
	            </div>
	         </div>
			 <div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">e-mail</div>
	            <div class="campo_mask"><?=$email?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">relazione con il paziente</div>
	            <div class="campo_mask"><?=$relazione?></div>
	            </div>
				<div class="rigo_mask">
	            <div class="testo_mask">disattivato dal</div>
	            <div class="campo_mask"><?=$disattivato?></div>
	            </div>		
	         </div>
	    </div>
	<script>	
		
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
}	


function show_list_res($idpaziente){

	$conn = db_connect();
	$query ="SELECT * from re_pazienti_residenze where IdUtente=$idpaziente order by id desc";	
	$rs = mssql_query($query, $conn);
	
	while($row = mssql_fetch_assoc($rs)){
	
		$indirizzo=stripslashes($row['indirizzo']);
		$cap=stripslashes($row['cap']);
		$comune=stripslashes($row['denominazione']);
		$nome=stripslashes($row['nome']);
		$provincia=stripslashes($row['nome_prov']);
		$fax=stripslashes($row['fax']);
		$telefono=stripslashes($row['telefono']);
		$cellulare=stripslashes($row['cellulare']);
		$email=stripslashes($row['email']);
		$datains=formatta_data($row['datains']);
		$decorrenza=formatta_data($row['decorrenza']);
		if ($decorrenzza=="") $decorrenza=$datains;
		
		?>
		
		
		<div class="titolo_pag">
		    <div class="comandi">
	        	<h1>Residenza alla data del <?=$decorrenza?></h1>
	        </div>
			<div class="comandi">
				<div id="aggiungi_tutor" class="aggiungi popupdatiClose"><a>chiudi scheda</a></div>
			</div>
		</div>
		<div class="blocco_centralcat">
	    	<div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">indirizzo</div>
	            <div class="campo_mask"><?=$indirizzo?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">provincia</div>
	            <div class="campo_mask"><?=$provincia?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">comune</div>
	            <div class="campo_mask"><?=$comune?></div>
	            </div>
	         </div>
			 <div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">cap</div>
	            <div class="campo_mask"><?=$cap?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">telefono</div>
	            <div class="campo_mask"><?=$telefono?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">fax</div>
	            <div class="campo_mask"><?=$fax?></div>
	            </div>
	         </div>
			 <div class="riga">
	            <div class="rigo_mask">
	            <div class="testo_mask">cellulare</div>
	            <div class="campo_mask"><?=$cellulare?></div>
	            </div>
	            <div class="rigo_mask">
	            <div class="testo_mask">mail</div>
	            <div class="campo_mask"><?=$email?></div>
	            </div>	           	            
	         </div>
	    </div>
	<script>	
		
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
				if($_REQUEST['type']=='res')
					show_list_res($_REQUEST['id']);
				if($_REQUEST['type']=='tut')
					show_list_tut($_REQUEST['id']);
				if($_REQUEST['type']=='med')
					show_list_med($_REQUEST['id']);
			break;
		}
			html_footer();
	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>