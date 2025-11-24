<?


include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
include_once('include/functions_test_clinici.php');
$id_permesso = $id_menu = 2;
session_start();


/************************************************************************
* funzione del_files()         						*
*************************************************************************/
function del_files($id,$id_paz) {
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "UPDATE utenti_allegati_san SET cancella='y' WHERE id='".$id."'";	
	$result = mssql_query($query, $conn);
	echo("ok;".$id_paz.";6;re_pazienti_sanitaria.php?do=show_allegati&id=".$id_paz);	
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}


/************************************************************************

* funzione add_files()         						*
*************************************************************************/
function add_files($id) {

	$ope = $_SESSION['UTENTE']->get_userid();
	$id_utente=$id;	
	$tablename="utenti_allegati_san";
	$conn = db_connect();
	
	for( $i = 1; $i < 10; $i++){
		$descrizione = str_replace("'","''",$_POST['et'.$i.'']);
		$cartella = $_POST['idcart'.$i.''];		
		$data=date(dmyHis);
		$data_prod = $_POST['data'.$i.''];
		$nome_file=str_replace(" ","_",$_FILES['ulfile'.$i.'']['name']);
		$userfile_size = $_FILES['ulfile'.$i.'']['size'];
		if(isset($_POST['stampa_elenco'.$i])){
			$stampa_file = 1;
		}
		else {
			$stampa_file = 0;
		}
		$max_file=1;
		if ($userfile_size > ($max_file*500048576)) {
			$error.= "Images must be under ".$max_file."MB in size";
		}
		
		$file = $id."_".$data."_".$nome_file;
		$type = $_FILES['ulfile'.$i.'']['type'];	
		if ($descrizione!=""){
			$query = "INSERT INTO $tablename (IdUtente,descrizione,file_name,tipo,type,opeins,idcartella,data_produzione,stampa_allegato) VALUES('$id','$descrizione','$file ',1,'$type',$ope,$cartella,'$data_prod','$stampa_file')";			
			$result = mssql_query($query, $conn);
								
			if ((trim($_FILES['ulfile'.$i.'']['name']) != "")) {	
			//$nomefile = $_FILES['img_src']['name'];
			
			$upload_dir = ALLEGATI_UTENTI_SAN;		// The directory for the images to be saved in
			$upload_path = $upload_dir."/";				// The path to where the image will be saved			
			$large_image_location = $upload_path.$file;
			$userfile_tmp = $_FILES['ulfile'.$i.'']['tmp_name'];
			//echo($large_image_location);			
			move_uploaded_file($userfile_tmp, $large_image_location);
			//chmod($large_image_location, 0777);
			
			}
			$query = "SELECT MAX(id) FROM $tablename WHERE (opeins='$ope')";
			$result = mssql_query($query, $conn);
			if(!$result) error_message(mssql_error());

			if(!$row = mssql_fetch_row($result))
				die("errore MAX select");
				
			$id_all=$row[0];
			scrivi_log ($id_all,$tablename,'ins','id');
		}
	}	
	echo("ok;".$id_utente.";6;re_pazienti_sanitaria.php?do=show_allegati&id=".$id_utente);	
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}


/* funzione add_allegato()  		// carica allegato alle istanze dei moduli
*************************************************************************/
function add_allegati_istanza($idcartella,$idpaziente,$idmoduloversione,$idinserimento)
{
	$idcartella = $_POST['idcartella'];
	$idpaziente = $_POST['idpaziente'];
	$idmoduloversione = $_POST['idmoduloversione'];
	$idinserimento = $_POST['idinserimento'];		// progressivo istanza	
	$idimpegnativa = $_POST['imp'];
	$campo_tbl = $_POST['campo_tbl'];

	$nome_file = $idpaziente."_".$idmoduloversione."_".$idinserimento."_".str_replace(" ","_",$_FILES['allegato']['name']);

	$type = $_FILES['allegato']['type'];	
	if($type !== 'application/pdf') {
		echo "Il file selezionato non e' in formato PDF.";
		exit();
	}
	
	$max_file=1;
	$userfile_size = $_FILES['allegato']['size'];	
	if ($userfile_size > ($max_file*500048576)) {
		echo "Il file non deve essere maggiore di ".$max_file."MB.";
		exit();
	}
		
			
	$upload_path = "modelli_word/allegato";

	$conn = db_connect();
	
	$query = "SELECT DISTINCT uc.idregime, m.idmodulo as id_modulo_padre, m.validazione_automatica
				FROM moduli AS m
				INNER JOIN istanze_testata AS it ON it.id_modulo_versione = m.id
				INNER JOIN utenti_cartelle AS uc ON uc.id = it.id_cartella
				WHERE m.id = $idmoduloversione AND uc.id = $idcartella";
	$_rs = mssql_query($query, $conn);
	$info_modulo = mssql_fetch_assoc($_rs);

/*
 // cancella il file allegato precedente dall'hard-disk ma la funziona unlink non funziona
	$query = "SELECT allegato FROM istanze_testata WHERE id_cartella = $idcartella AND id_modulo_versione = $idmoduloversione AND id_inserimento = $idinserimento";
	if(!$result = mssql_query($query, $conn)) {
		echo "Errore ricerca modulo (non esiste piu il modulo a cui associare l'allegato?).";
		exit();
	} else {
		
		if($row = mssql_fetch_assoc($result)) { 
			
			if ($row['allegato'] !== NULL)
			{
				if (!rmdir($upload_path . $row['allegato'])) 
					echo "non riesco a cancellare " . $upload_path."/".$row['allegato'];
			}
		}
	}
*/

	//		eseguo la validazione automatica per tutti i moduli che la prevedono o nel caso in cui sia il ds a fare 
	//		il caricamento dell'allegato. Disabilito questa operazione per i moduli delle visite specialistiche
	//$cstm_set = "";
	// ----------------------------------------------------------------------------------
	// 		AME 22/04/2025 -- richiesta autovalidazione per tutti i moduli
	// ----------------------------------------------------------------------------------
	// $idmodulo_visiste_specialistiche = array(132, 145, 146, 147, 148, 250);
	// if(($info_modulo['validazione_automatica'] || $_SESSION["UTENTE"]->is_ds() )
	// 	&& !in_array($info_modulo['id_modulo_padre'], $idmodulo_visiste_specialistiche)
	// ) {		
		$cstm_set = ", ope_validazione = " . $_SESSION["UTENTE"]->get_userid() . ", data_validazione = getdate(), validazione = 's' ";
	// }
	
	$query = "UPDATE istanze_testata 
				SET $campo_tbl = '$nome_file', data_$campo_tbl = getdate() $cstm_set
				WHERE id_cartella = $idcartella AND id_modulo_versione = $idmoduloversione 
					AND id_inserimento = $idinserimento";
	
	if(!empty($idimpegnativa))
		$query .= " AND id_impegnativa = $idimpegnativa";
	
	if(!$result = mssql_query($query, $conn)) {
		echo "Errore salvataggio.";
		exit();
	}
		
	$large_image_location = $upload_path."/".$nome_file;
	$userfile_tmp = $_FILES['allegato']['tmp_name'];
	move_uploaded_file($userfile_tmp, $large_image_location);
	//chmod($large_image_location, 0777);
	
	echo '<p style="font-family: Tahoma">Allegato salvato!<br><br>Aggiornare l\'elenco delle istanze per visualizzare l\'icona dell\'allegato.</p><button onclick="window.close()" style="width:100%">CHIUDI</button>';
	exit();
}


function add_note_istanza($idcartella,$idpaziente,$idmoduloversione,$idinserimento,$note){
	$idcartella = $_POST['idcartella'];
	$idpaziente = $_POST['idpaziente'];
	$idmoduloversione = $_POST['idmoduloversione'];
	$idinserimento = $_POST['idinserimento'];		// progressivo istanza	
	$idimpegnativa = $_POST['imp'];
	
	if(trim($_POST['note']) == "")
		 $query = "UPDATE istanze_testata SET note = NULL WHERE id_cartella = $idcartella AND id_modulo_versione = $idmoduloversione AND id_inserimento = $idinserimento";
	else $query = "UPDATE istanze_testata SET note = '$note' WHERE id_cartella = $idcartella AND id_modulo_versione = $idmoduloversione AND id_inserimento = $idinserimento";
	
	if(!empty($idimpegnativa))
		$query .= " AND id_impegnativa = $idimpegnativa";

	$conn = db_connect();
	
	if(!$result = mssql_query($query, $conn)) {
		echo "Errore salvataggio.";
		exit();
	}
	
	echo '<p style="font-family: Tahoma">Note salvate!</p><button onclick="window.close()" style="width:100%">CHIUDI</button>';
	exit();
}


function visualizza_istanze_test_clinico_report()
{
		$conn=db_connect();
	$idtest=$_REQUEST['idtest'];
	$idpaziente=$_REQUEST['idpaziente'];
	$idcartella=$_REQUEST['idcartella'];
	$cartella=$_REQUEST['cartella'];
	$idimpegnativa=$_REQUEST['idimp'];
	$stringa_prot="";
	$codice_test="";
	$nome_test="";
	
	$query = "SELECT nome,codice from test_clinici where idtest=$idtest";	
				
				$rs1 = mssql_query($query, $conn);
				
				if($row1 = mssql_fetch_assoc($rs1))   
					{
						$nome_test=$row1['nome'];
						$codice_test=$row1['codice'];
					}
					
			
	
	
	if (empty($idimpegnativa))
	$idimpegnativa=0;
	else
	{
	$query = "SELECT * from re_impegnative where idimpegnativa=$idimpegnativa";	
				//echo($query);
				
				$rs1 = mssql_query($query, $conn);
				
				if($row1 = mssql_fetch_assoc($rs1))   
					{
						$idimpegnativa=$row1['idimpegnativa'];
						$data_auth_asl=formatta_data($row1['DataAutorizAsl']);
						$prot_auth_asl=$row1['ProtAutorizAsl'];
						$regime=$row1['regime'];
						$normativa=$row1['normativa'];
				$stringa_prot=$prot_auth_asl."-".$data_auth_asl." - ".$regime."/".$normativa;
				
	
					}
	}				
	
	
	if (isset($_REQUEST['idragr']))
		$idragr=$_REQUEST['idragr'];
	else
		$idragr=1;
		
	
	
	?>
<script type="text/javascript" id="js">
function ritorna(idcartella,idpaziente,divagg,cartella)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_cartelle.php?do=visualizza_test_clinici&idcartella="+idcartella+"&idpaziente="+idpaziente+"&cartella="+cartella.replace(" ", "%20");
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
</script>	
	<div id="sanitaria">
	
	<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna('<?=$idcartella?>','<?=$idpaziente?>','sanitaria','<?=$cartella?>')" >ritorna alla lista dei test</a></div>		
	</div>
</div>

	
	<div class="titoloalternativo">
	            <h1>cartella <?=$cartella?></h1>
				<h2 style="font-size:13px;"><?=$stringa_prot?></h2>
			
				<!--<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
	</div>
	
	<div class="titoloalternativo">
	            <h1> Test Clinico: <?=pulisci_lettura($nome_test)?> /  <?=pulisci_lettura($codice_test)?></h1>
	</div>
	
	<div class="titoloalternativo">
	<strong>legenda</strong>
		<br /><br />
		<strong>% pr. superate</strong> = percentuale di prove superate nell'istanza del test
		<br />
		<strong>% pr. migliorate</strong> = percentuale di prove migliorate rispetto alla prima istanza
		<br />
		<strong>% pr. stazionarie</strong> = percentuale di prove stazionarie rispetto alla prima istanza
		<br />
		<strong>% pr. regredite</strong> = percentuale di prove regredite rispetto alla prima istanza
		<br />
		<br />
		<strong>% pr. migliorate rel</strong> = percentuale di prove migliorate rispetto all'istanza precendete
		<br />
		<strong>% pr. stazionarie rel</strong> = percentuale di prove stazionarie rispetto all'istanza precendete
		<br />
		<strong>% pr. regredite rel</strong> = percentuale di prove regredite rispetto all'istanza precendete
		<br />
		<br />
		<strong>PUNTEGGIO</strong> = somma delle valutazioni di tutti gli items fleggati come "concorrono alla somma" per singola istanza del test
	</div>
	<?
	if (empty($idimpegnativa))
	{
	?>
	<div class="titoloalternativo">
	 Opzioni di visualizzazione:           
	<select name="idragr" onchange="view_istanze_test_clinico('<?=$idtest?>','<?=$idpaziente?>',this.value,'<?=$idcartella?>','<?=$cartella?>');">
	<option value="1" <?if ($idragr==1) echo ("selected")?>>raggruppa per storico della cartella corrente</option>
	<option value="2" <?if ($idragr==2) echo ("selected")?>>raggruppa per impegnativa</option>
	<!--<option value="3" <?if ($idragr==3) echo ("selected")?>>raggruppa per cartella clinica</option>-->
	</select>
	
	</div>
	<?
	}
	
	if ($idragr!=1)
	{
	$idragr=$_REQUEST['idragr'];
	$conn = db_connect();
	$query="select distinct(idimpegnativa),regime,normativa,DataAutorizAsl,ProtAutorizAsl from re_test_clinici_result where idtest=$idtest and idpaziente=$idpaziente";
	if ($idcartella>0)
			$query.=" and idcartella=$idcartella";
		
				$i=1;
				$idtestclinico_old=0;
				$conta=0;
				
				$rs1 = mssql_query($query, $conn);
	
			
					if(!$rs1) error_message(mssql_error());
				
					while($row1 = mssql_fetch_assoc($rs1))   
					{
					$idimpegnativa=$row1['idimpegnativa'];
					$regime=$row1['regime'];
					$normativa=$row1['normativa'];
					$DataAutorizAsl=formatta_data($row1['DataAutorizAsl']);
					$ProtAutorizAsl=$row1['ProtAutorizAsl'];
					?>
					<div class="titoloalternativo">
					<h1><?="Prot ".$ProtAutorizAsl." del ".$DataAutorizAsl."  - ".$regime." / ".$normativa ?></h1>
					</div>
					<?
				
					visualizza_istanze_test_clinico($idtest,$idpaziente,$idcartella,$cartella,$idimpegnativa);
					}
	}
	else
	{
	$idragr=1;
	
	visualizza_istanze_test_clinico($idtest,$idpaziente,$idcartella,$cartella,$idimpegnativa);
	}
	print("</div>");
	
	
}


function visualizza_istanze_test_clinico($idtest,$idpaziente,$idcartella,$cartella,$idimpegnativa)
{
	$conn = db_connect();
	
	
	$query = "SELECT idtest,nome,codice,tipo_test,descrizione from re_test_clinici where idtest=$idtest";	
	
			$rs1 = mssql_query($query, $conn);
	
			$nome_test="";
			if(!$rs1) error_message(mssql_error());
				if($row1 = mssql_fetch_assoc($rs1))   
					$nome_test=$row1['nome'];
					
				
	if ($cartella==0)
	$cartella="storico per tutte le cartelle";
?>

	
	
		<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
		<tr> 
		    <th>numero test</th> 
			<th>data osservazione</th> 
			<th>operatore</th>
			<th>et&agrave; alla data del test</th> 			
			<td>% pr. superate</td> 
			<td>% pr. migliorate</td> 
			<td>% pr. stazionarie</td> 
		    <td>% pr. regredite</td>
			<td>% pr. migliorate rel</td> 
			<td>% pr. stazionarie rel</td> 
		    <td>% pr. regredite rel</td>
			<td>PUNTEGGIO</td>
			<td>modifica</td>
		<!--	<td>cancella</td>-->
			
			
			
			
		</tr> 
		</thead> 
		<tbody> 
			<?
			
			$query = "SELECT * from re_test_clinici_result where idtest=$idtest and idpaziente=$idpaziente";	
			if ($idcartella>0)
			$query.=" and idcartella=$idcartella";
			if ($idimpegnativa>0)
			$query.=" and idimpegnativa=$idimpegnativa";

			
	
	$query.=" ORDER BY data_osservazione asc";
			$ordinamento="";
	
			$rs1 = mssql_query($query, $conn);
	
			if(!$rs1) error_message(mssql_error());
	
			$conta=mssql_num_rows($rs1);
				$i=1;
				$idtestclinico_old=0;
				$conta=0;
				$somma_valori=0;
					while($row1 = mssql_fetch_assoc($rs1))   
					{
					
						$somma=$row1['somma'];
						$idtestclinico=$row1['idtestclinico'];
						
					
						
						if ($idtestclinico!=$idtestclinico_old)
						{
						
						$data_osservazione=formatta_data($row1['data_osservazione']);
						$operatore=$row1['operatore'];
						$eta_data_test=calcola_eta_alla_data_del_test($row1['data_osservazione'],$idpaziente);
						$idtest=$row1['idtest'];
						$somma_valori=0;
						if ($somma){							
							$query4 = "SELECT * from re_test_clinici_somma_valori_punteggio where idtestclinico=$idtestclinico";
										
							$rs4 = mssql_query($query4, $conn);
							if(!$rs4) error_message(mssql_error());
							while ($row4 = mssql_fetch_assoc($rs4)){
								$somma_valori+=($row4['punteggio']);
							}
						}					
						$p_superate_perc=test_tot_prove_superate($idtestclinico,$eta_data_test);
						if ($i==1)
						{
						$andamento_assoluto=get_andamento_prove($idtest,$idpaziente,$conta,$idtestclinico,0,$idcartella,$idimpegnativa);
						$andamento_relativo=get_andamento_prove_relativo($idtest,$idpaziente,$conta,$idtestclinico,$idcartella,$idimpegnativa);
						$p_migliorate_perc="";
						$p_stazionarie_perc="";
						$p_regredite_perc="";
						$p_migliorate_perc_rel="";
						$p_stazionarie_perc_rel="";
						$p_regredite_perc_rel="";
						}
						else
						{
						$andamento_assoluto=get_andamento_prove($idtest,$idpaziente,$conta,$idtestclinico,0,$idcartella,$idimpegnativa);
						$andamento_relativo=get_andamento_prove_relativo($idtest,$idpaziente,$conta,$idtestclinico,$idcartella,$idimpegnativa);
						$tot_prove=$andamento_assoluto[0]+$andamento_assoluto[1]+$andamento_assoluto[2];
						
						if($tot_prove>0)
						{
						
						$p_migliorate_perc=round(($andamento_assoluto[0]/$tot_prove)*100,2);
						$p_stazionarie_perc=round(($andamento_assoluto[1]/$tot_prove)*100,2);
						$p_regredite_perc=round(($andamento_assoluto[2]/$tot_prove)*100,2);
						$tot_prove=$andamento_relativo[0]+$andamento_relativo[1]+$andamento_relativo[2];
						$p_migliorate_perc_rel=round(($andamento_relativo[0]/$tot_prove)*100,2);
						$p_stazionarie_perc_rel=round(($andamento_relativo[1]/$tot_prove)*100,2);
						$p_regredite_perc_rel=round(($andamento_relativo[2]/$tot_prove)*100,2);
						}
						else
						{
						$p_superate_perc="";
						$p_migliorate_perc="";
						$p_stazionarie_perc="";
						$p_regredite_perc="";
						$p_migliorate_perc_rel="";
						$p_stazionarie_perc_rel="";
						$p_regredite_perc_rel="";
						
						}
						
						
						/*$p_migliorate_perc_rel=round(($andamento_assoluto[3]/2)*100,2);
						$p_stazionarie_perc_rel=round(($andamento_assoluto[4]/2)*100,2);
						$p_regredite_perc_rel=round(($andamento_assoluto[5]/2)*100,2);*/
						
						}
						
						
						
						
						
						
						//$idtestclinico_1=$idtestclinico;
						$idtestclinico_old=$idtestclinico;
						
							?>
							
							<tr> 
							<td><?=$i?></td> 
							 <td><?=$data_osservazione?></td> 
							 <td><?=$operatore?></td>	
							 <td><?=$eta_data_test?></td>
							 <td><?=$p_superate_perc?></td>
							 <td><?=$p_migliorate_perc?></td>
							 <td><?=$p_stazionarie_perc?></td>
							 <td><?=$p_regredite_perc?></td>
							<td><?=$p_migliorate_perc_rel?></td>
							 <td><?=$p_stazionarie_perc_rel?></td>
							 <td><?=$p_regredite_perc_rel?></td>
							  <td><?=$somma_valori?></td>
							
							 <td><a href="#"  onclick="javascript:modifica_test_clinico('<?=$idtestclinico?>')" ><img src="images/gear.png" /></a></td>
							 <!-- <td><a href="#"  onclick="javascript:cancella_test_clinico('<?=$idtestclinico?>','<?=$idtest?>','<?=$idpaziente?>','0','<?=$idcartella?>','<?=$cartella?>')" ><img src="images/remove.png" /></a></td>-->
							</tr> 
							
							
							
							<?
							$i++;
							$conta++;
						}	
							
							}?>
					
	
		</tbody> 
	</table> 

	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
		<tr> 
		    <th>Prova</th> 
			<th>Et&agrave;</th> 
			<th>Data osservazione</th>
			<th>Limite normativo</th> 			
			<td>Valutazione</td> 
			<td>Deviazione</td> 
			<td>Migliorato</td> 
		    <td>Stazionario</td>
			<td>Regredito</td> 
			
		</tr> 
		
		</thead> 
		<tbody> 
		
		<?
		$query = "SELECT distinct(idprova),descrizione from re_test_clinici_result where idtest=$idtest and idpaziente=$idpaziente";	
		
		if ($idcartella>0)
		$query.=" and idcartella=$idcartella";
		if ($idimpegnativa>0)
			$query.=" and idimpegnativa=$idimpegnativa";
			$ordinamento="";
			
	
			$rs1 = mssql_query($query, $conn);
	
			if(!$rs1) error_message(mssql_error());
	
			$conta=mssql_num_rows($rs1);
				$i=1;
				$idtestclinico_old=0;
				$conta=0;
					while($row1 = mssql_fetch_assoc($rs1))   
					{
					$idprova=$row1['idprova'];
					$prova=$row1['descrizione'];
		
		?>
		
		
							 
					<?
$query = "SELECT * from re_test_clinici_result where idtest=$idtest and idpaziente=$idpaziente and idprova=$idprova";	
		if ($idcartella>0)
		$query.=" and idcartella=$idcartella";
		if ($idimpegnativa>0)
			$query.=" and idimpegnativa=$idimpegnativa";
		
		$query.=" ORDER BY data_osservazione";

		
			$ordinamento="";
	
			$rs2 = mssql_query($query, $conn);
	
			if(!$rs2) error_message(mssql_error());
	
			$conta_osservazioni=mssql_num_rows($rs2);
			
					$stampa=1;
					$pun=0;
					while($row2 = mssql_fetch_assoc($rs2))   
					{
			
					
					$data_osservazione=formatta_data($row2['data_osservazione']);
					$eta_data_test=calcola_eta_alla_data_del_test($row2['data_osservazione'],$idpaziente);
					//$limite_default=(int)($row2['limite_default']);
					$limite_default=($row2['limite_default']);
					$iditem=$row2['iditem'];
					$idrange=$row2['idrange'];
					$tipo_valutazione=$row2['tipo_valutazione'];
					$idprova=$row2['idprova'];
					
					$new_limite=get_limite_normativo($idprova,$eta_data_test);
					$valutazione=$row2['punteggio'];
					
					
						
						if ($new_limite<>"")
							$limite_default=(int)($new_limite);
					$deviazione=$limite_default-$valutazione;
					
					
					
					   // $andamento_assoluto=get_andamento_prove($idtest,$idpaziente,$conta,$idtestclinico,$idprova);
						//$andamento_relativo=get_andamento_prove_relativo($idtest,$idpaziente,$conta,$idtestclinico,$idprova);
						/*$tot_prove=$andamento_assoluto[0]+$andamento_assoluto[1]+$andamento_assoluto[2];
						$migliorato=round(($andamento_assoluto[0]/$tot_prove)*100,2);
						$stazionario=round(($andamento_assoluto[1]/$tot_prove)*100,2);
						$regredito=round(($andamento_assoluto[2]/$tot_prove)*100,2);*/
						$arr_punteggio[$pun]=$valutazione;
						$pun++;
					
					
					?>
							 <tr> 
							 <td><strong><?=pulisci_lettura($prova)?></strong></td> 
							 <?
							 $prova="";
							 ?>
							 <td><?=$eta_data_test?></td> 
							 <td><?=$data_osservazione?></td>	
							 <td><?=$limite_default?></td>
							 <td><?=$valutazione?></td>
							 <td><?=$deviazione?></td>
							<? if ($stampa==$conta_osservazioni){
								$ck=confronta_prove($arr_punteggio[sizeof($arr_punteggio)-1],$arr_punteggio[0],$tipo_valutazione);
							//print_r($arr_punteggio);
							
							$migliorato="";
							$stazionario="";
							$regredito="";
							//print($arr_punteggio[sizeof($arr_punteggio)-1]." ".$arr_punteggio[0]." ".$tipo_valutazione);
							if ($conta_osservazioni>1)
							{
							if ($arr_punteggio[sizeof($arr_punteggio)-1]==$arr_punteggio[0])
								$stazionario="X";
							elseif($ck==true)
								$migliorato="X";
							else
								$regredito="X";
							}	
								
								
							
							?>
							<td align="center"> <strong><?=$migliorato?></strong></td>
							 <td align="center"><strong><?=$stazionario?></strong></td>
							 <td align="center"><strong><?=$regredito?></strong></td>
							  </tr> 
							 <tr>
								 <td  align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
							 </tr>
							 <?}
							 else
							 {
							 {?>
							<td></td>
							 <td></td>
							 <td></td>
							  </tr> 
							 <?}
							 }
							 
							 ?>
							
							
					<?
					$stampa++;
}
?>					
							
					<?
					}
					?>					
							
	</tbody> 
	</table> 						
	
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
	
		<script>
		
		function add_test_clinico(idcartella,idpaziente,idtest,cartella)
		{
	
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_test_clinico&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idtest="+idtest+"&cartella="+cartella.replace(" ", "%20");
		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	
	
		}
		
		function modifica_test_clinico(idtestclinico)
		{
	
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=modifica_test_clinico&idtestclinico="+idtestclinico;
		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	
	
		}
		
		
		

function cancella_test_clinico(idtestclinico,idtest,idpaziente,idragr,idcartella,cartella){
if (confirm('sei sicuro di voler cancellare l\'istanza del test clinico?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_test_clinici.php?do=del_istanza&idtestclinico="+idtestclinico+"&idtest="+idtest,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			view_istanze_test_clinico(idtest,idpaziente,idragr,idcartella,cartella);
			}
		 });	
	  }
	else return false;
}

function reload_pagina(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_moduli.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
		
		
		function view_istanze_test_clinico(idtest,idpaziente,idragr,idcartella,cartella)
		{
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_test_clinico&idtest="+idtest+"&idragr="+idragr+"&idpaziente="+idpaziente+"&idcartella="+idcartella+"&cartella="+cartella.replace(" ", "%20");
		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	
	
		}
		
			  //$("table").tablesorter({sortList: [[0,0]] ,widthFixed: true, widgets: ['zebra']});
		</script>



<?	
}



function visualizza_istanze_figli_test_clinico_ramificato($idtest,$idpaziente,$idcartella,$cartella)
{

	$conn = db_connect();
	/*$idtest=$_REQUEST['idtest'];
	$idpaziente=$_REQUEST['idpaziente'];
	$idcartella=$_REQUEST['idcartella'];
	$cartella=$_REQUEST['cartella'];*/
	
	$idimpegnativa=0;
	
	$query = "SELECT idtest,nome,codice,tipo_test,descrizione from re_test_clinici where idtest=$idtest";	
	
	
	if (isset($_REQUEST['idragr']))
	$idragr=$_REQUEST['idragr'];
	else
	$idragr=1;
	
	
	
	
		
			
			$rs1 = mssql_query($query, $conn);
	
			$nome_test="";
			if(!$rs1) error_message(mssql_error());
				if($row1 = mssql_fetch_assoc($rs1))   
					$nome_test=$row1['nome'];
					
				
	
?>

	<div id="sanitaria">
	
	
	
	<div class="titoloalternativo">
	            <h1>Istanze del test <?=$nome_test?></h1>
	</div>
	
	
	
		<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
		<tr> 
		    <th>numero test</th> 
			<th>data osservazione</th> 
			<th>operatore</th>
			<th>et&agrave; alla data del test</th> 			
			<td>% pr. superate</td> 
			<td>% pr. migliorate</td> 
			<td>% pr. stazionarie</td> 
		    <td>% pr. regredite</td>
			<td>% pr. migliorate rel</td> 
			<td>% pr. stazionarie rel</td> 
		    <td>% pr. regredite rel</td>
			<td>modifica</td>
			<!--<td>cancella</td>-->
			
			
			
			
		</tr> 
		</thead> 
		<tbody> 
			<?
			
			$query = "SELECT * from re_test_clinici_result where idtest=$idtest and idpaziente=$idpaziente";	
			if ($idcartella>0)
	$query.=" and idcartella=$idcartella";
	if ($idimpegnativa>0)
			$query.=" and idimpegnativa=$idimpegnativa";
	
	$query.=" ORDER BY data_osservazione";
			$ordinamento="";
	
			$rs1 = mssql_query($query, $conn);
	
			if(!$rs1) error_message(mssql_error());
	
			$conta=mssql_num_rows($rs1);
				$i=1;
				$idtestclinico_old=0;
				$conta=0;
					while($row1 = mssql_fetch_assoc($rs1))   
					{
						$idtestclinico=$row1['idtestclinico'];
						if ($idtestclinico!=$idtestclinico_old)
						{
						$data_osservazione=formatta_data($row1['data_osservazione']);
						$operatore=$row1['operatore'];
						$eta_data_test=calcola_eta_alla_data_del_test($row1['data_osservazione'],$idpaziente);
						$idtest=$row1['idtest'];
						$p_superate_perc=test_tot_prove_superate($idtestclinico,$eta_data_test);
						if ($i==1)
						{
						$andamento_assoluto=get_andamento_prove($idtest,$idpaziente,$conta,$idtestclinico,0,$idcartella,$idimpegnativa);
						$andamento_relativo=get_andamento_prove_relativo($idtest,$idpaziente,$conta,$idtestclinico,$idcartella,$idimpegnativa);
						$p_migliorate_perc="";
						$p_stazionarie_perc="";
						$p_regredite_perc="";
						$p_migliorate_perc_rel="";
						$p_stazionarie_perc_rel="";
						$p_regredite_perc_rel="";
						}
						else
						{
						$andamento_assoluto=get_andamento_prove($idtest,$idpaziente,$conta,$idtestclinico,0,$idcartella,$idimpegnativa);
						$andamento_relativo=get_andamento_prove_relativo($idtest,$idpaziente,$conta,$idtestclinico,$idcartella,$idimpegnativa);
						$tot_prove=$andamento_assoluto[0]+$andamento_assoluto[1]+$andamento_assoluto[2];
						
						if($tot_prove>0)
						{
						
						$p_migliorate_perc=round(($andamento_assoluto[0]/$tot_prove)*100,2);
						$p_stazionarie_perc=round(($andamento_assoluto[1]/$tot_prove)*100,2);
						$p_regredite_perc=round(($andamento_assoluto[2]/$tot_prove)*100,2);
						$tot_prove=$andamento_relativo[0]+$andamento_relativo[1]+$andamento_relativo[2];
						$p_migliorate_perc_rel=round(($andamento_relativo[0]/$tot_prove)*100,2);
						$p_stazionarie_perc_rel=round(($andamento_relativo[1]/$tot_prove)*100,2);
						$p_regredite_perc_rel=round(($andamento_relativo[2]/$tot_prove)*100,2);
						}
						else
						{
						$p_superate_perc="";
						$p_migliorate_perc="";
						$p_stazionarie_perc="";
						$p_regredite_perc="";
						$p_migliorate_perc_rel="";
						$p_stazionarie_perc_rel="";
						$p_regredite_perc_rel="";
						
						}
						
						
						/*$p_migliorate_perc_rel=round(($andamento_assoluto[3]/2)*100,2);
						$p_stazionarie_perc_rel=round(($andamento_assoluto[4]/2)*100,2);
						$p_regredite_perc_rel=round(($andamento_assoluto[5]/2)*100,2);*/
						
						}
						
						
						
						
						
						
						//$idtestclinico_1=$idtestclinico;
						$idtestclinico_old=$idtestclinico;
						
							?>
							
							<tr> 
							<td><?=$i?></td> 
							 <td><?=$data_osservazione?></td> 
							 <td><?=$operatore?></td>	
							 <td><?=$eta_data_test?></td>
							 <td><?=$p_superate_perc?></td>
							 <td><?=$p_migliorate_perc?></td>
							 <td><?=$p_stazionarie_perc?></td>
							 <td><?=$p_regredite_perc?></td>
							 <td><?=$p_migliorate_perc_rel?></td>
							 <td><?=$p_stazionarie_perc_rel?></td>
							 <td><?=$p_regredite_perc_rel?></td>
							 <td><a href="#"  onclick="javascript:modifica_test_clinico_ram('<?=$idtestclinico?>','<?=$idtest?>')" ><img src="images/gear.png" /></a></td>
							<!-- <td><a href="#"  onclick="javascript:cancella_test_clinico_ram('<?=$idtestclinico?>','<?=$idtest?>','<?=$idpaziente?>','0','<?=$idcartella?>','<?=$cartella?>')" ><img src="images/remove.png" /></a></td>							 -->
							 
							 <!-- <td><a href="#"  onclick="javascript:cancella_test_clinico_ram('<?=$idtestclinico?>','<?=$idtest?>')" ><img src="images/remove.png" /></a></td>-->
							
							</tr> 
							
							
							
							
							<?
							$i++;
							$conta++;
						}	
							
							}?>
					
	
		</tbody> 
	</table> 
	
	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
		<tr> 
		    <th>Prova</th> 
			<th>Et&agrave;</th> 
			<th>Data osservazione</th>
			<th>Limite normativo</th> 			
			<td>Valutazione</td> 
			<td>Deviazione</td> 
			<td>Migliorato</td> 
		    <td>Stazionario</td>
			<td>Regredito</td> 
			
		</tr> 
		
		</thead> 
		<tbody> 
		
		<?
		$query = "SELECT distinct(idprova),descrizione from re_test_clinici_result where idtest=$idtest and idpaziente=$idpaziente";	
		
		if ($idcartella>0)
		$query.=" and idcartella=$idcartella";
		if ($idimpegnativa>0)
			$query.=" and idimpegnativa=$idimpegnativa";
			$ordinamento="";
	
			$rs1 = mssql_query($query, $conn);
	
			if(!$rs1) error_message(mssql_error());
	
			$conta=mssql_num_rows($rs1);
				$i=1;
				$idtestclinico_old=0;
				$conta=0;
					while($row1 = mssql_fetch_assoc($rs1))   
					{
					$idprova=$row1['idprova'];
					$prova=$row1['descrizione'];
		
		?>
		
		
							 
					<?
$query = "SELECT * from re_test_clinici_result where idtest=$idtest and idpaziente=$idpaziente and idprova=$idprova";	
		if ($idcartella>0)
		$query.=" and idcartella=$idcartella";
		if ($idimpegnativa>0)
			$query.=" and idimpegnativa=$idimpegnativa";
		
		$query.=" ORDER BY data_osservazione";

		
			$ordinamento="";
	
			$rs2 = mssql_query($query, $conn);
	
			if(!$rs2) error_message(mssql_error());
	
			$conta_osservazioni=mssql_num_rows($rs2);
			
					$stampa=1;
					$pun=0;
					while($row2 = mssql_fetch_assoc($rs2))   
					{
			
					
					$data_osservazione=formatta_data($row2['data_osservazione']);
					$eta_data_test=calcola_eta_alla_data_del_test($row2['data_osservazione'],$idpaziente);
					$limite_default=(int)($row2['limite_default']);
					$iditem=$row2['iditem'];
					$idrange=$row2['idrange'];
					$tipo_valutazione=$row2['tipo_valutazione'];
					$idprova=$row2['idprova'];
					$new_limite=get_limite_normativo($idprova,$eta_data_test);
					$valutazione=$row2['punteggio'];
						
						if ($new_limite<>"")
							$limite_default=(int)($new_limite);
							$deviazione=$limite_default-$valutazione;
							
							
					
					   // $andamento_assoluto=get_andamento_prove($idtest,$idpaziente,$conta,$idtestclinico,$idprova);
						//$andamento_relativo=get_andamento_prove_relativo($idtest,$idpaziente,$conta,$idtestclinico,$idprova);
						/*$tot_prove=$andamento_assoluto[0]+$andamento_assoluto[1]+$andamento_assoluto[2];
						$migliorato=round(($andamento_assoluto[0]/$tot_prove)*100,2);
						$stazionario=round(($andamento_assoluto[1]/$tot_prove)*100,2);
						$regredito=round(($andamento_assoluto[2]/$tot_prove)*100,2);*/
						$arr_punteggio[$pun]=$valutazione;
						$pun++;
					
					
					?>
							 <tr> 
							 <td><strong><?=$prova?></strong></td> 
							 <?
							 $prova="";
							 ?>
							 <td><?=$eta_data_test?></td> 
							 <td><?=$data_osservazione?></td>	
							 <td><?=$limite_default?></td>
							 <td><?=$valutazione?></td>
							 <td><?=$deviazione?></td>
							<? if ($stampa==$conta_osservazioni){
								$ck=confronta_prove($arr_punteggio[sizeof($arr_punteggio)-1],$arr_punteggio[0],$tipo_valutazione);
							//print_r($arr_punteggio);
							
							$migliorato="";
							$stazionario="";
							$regredito="";
							//print($arr_punteggio[sizeof($arr_punteggio)-1]." ".$arr_punteggio[0]." ".$tipo_valutazione);
							if ($conta_osservazioni>1)
							{
							if($ck==true)
								$migliorato="X";
							elseif ($arr_punteggio[sizeof($arr_punteggio)-1]==$arr_punteggio[0])
								$stazionario="X";	
							else
								$regredito="X";
							}	
								
								
							
							?>
							<td align="center"> <strong><?=$migliorato?></strong></td>
							 <td align="center"><strong><?=$stazionario?></strong></td>
							 <td align="center"><strong><?=$regredito?></strong></td>
							  </tr> 
							 <tr>
								 <td  align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
								 <td align="center">&nbsp;</td>
							 </tr>
							 <?}
							 else
							 {
							 {?>
							<td></td>
							 <td></td>
							 <td></td>
							  </tr> 
							 <?}
							 }
							 
							 ?>
							
							
					<?
					$stampa++;
}
?>					
							
					<?
					}
					?>					
							
	</tbody> 
	</table> 						
	
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
	
		<script>
		
		function add_test_clinico(idcartella,idpaziente,idtest,cartella)
		{
	
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_test_clinico&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idtest="+idtest+"&cartella="+cartella.replace(" ", "%20");
		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	
	
		}
		
		function modifica_test_clinico_ram(idtestclinico,idtest)
		{
	
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=modifica_test_clinico&idtestclinico="+idtestclinico+"&idtest="+idtest;
		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	
	
		}
		
		
		
function cancella_test_clinico_ram(idtestclinico,idtest,idpaziente,idragr,idcartella,cartella){
if (confirm('sei sicuro di voler cancellare l\'istanza del test clinico?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "lista_test_clinici.php?do=del_istanza_ram&idtestclinico="+idtestclinico+"&idtest="+idtest,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			view_istanze_test_clinico(idtest,idpaziente,idragr,idcartella,cartella);
			}
		 });	
	  }
	else return false;
}
		
		function view_istanze_test_clinico(idtest,idpaziente,idragr,idcartella,cartella)
		{
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_test_clinico&idtest="+idtest+"&idragr="+idragr+"&idpaziente="+idpaziente+"&idcartella="+idcartella+"&cartella="+cartella.replace(" ", "%20");
		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	
	
		}
		
			  //$("table").tablesorter({sortList: [[0,0]] ,widthFixed: true, widgets: ['zebra']});
		</script>

</div>

<?	
}


function visualizza_istanze_test_clinico_ramificato()
{
$conn = db_connect();
	$idtest=$_REQUEST['idtest'];
	$idpaziente=$_REQUEST['idpaziente'];
	$idcartella=$_REQUEST['idcartella'];
	$cartella=$_REQUEST['cartella'];
	
	$query = "SELECT idtest,nome,codice,tipo_test,descrizione from re_test_clinici where idtest=$idtest";	
	
	
	if (isset($_REQUEST['idragr']))
	$idragr=$_REQUEST['idragr'];
	else
	$idragr=1;
	
	
	
	
		
			
			$rs1 = mssql_query($query, $conn);
	
			$nome_test="";
			if(!$rs1) error_message(mssql_error());
				if($row1 = mssql_fetch_assoc($rs1))   
					$nome_test=$row1['nome'];
					
				
	
?>

	<script type="text/javascript" id="js">
function ritorna(idcartella,idpaziente,divagg,cartella)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_cartelle.php?do=visualizza_test_clinici&idcartella="+idcartella+"&idpaziente="+idpaziente+"&cartella="+cartella.replace(" ", "%20");
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
</script>	
	<div id="sanitaria">
	
	<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna('<?=$idcartella?>','<?=$idpaziente?>','sanitaria','<?=$cartella?>')" >ritorna alla lista dei test</a></div>		
	</div>
</div>
	
	<div class="titoloalternativo">
	            <h1>cartella <?=$cartella?></h1>
				<!--<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
	</div>
	
	<div class="titoloalternativo">
	            <h1>Istanze del test <?=$nome_test?></h1>
	</div>
	
	<div class="titoloalternativo">
	<strong>legenda</strong>
		<br /><br />
		<strong>% pr. superate</strong> = percentuale di prove superate nell'istanza del test
		<br />
		<strong>% pr. migliorate</strong> = percentuale di prove migliorate rispetto alla prima istanza
		<br />
		<strong>% pr. stazionarie</strong> = percentuale di prove stazionarie rispetto alla prima istanza
		<br />
		<strong>% pr. regredite</strong> = percentuale di prove regredite rispetto alla prima istanza
		<br />
		<br />
		<strong>% pr. migliorate rel</strong> = percentuale di prove migliorate rispetto all'istanza precendete
		<br />
		<strong>% pr. stazionarie rel</strong> = percentuale di prove stazionarie rispetto all'istanza precendete
		<br />
		<strong>% pr. regredite rel</strong> = percentuale di prove regredite rispetto all'istanza precendete
		<br />
		<strong>PUNTEGGIO</strong> = somma delle valutazioni di tutti gli items fleggati come "concorrono alla somma" per singola istanza del test
	</div>
	
	<!--<div class="titoloalternativo">
	 Opzioni di visualizzazione:           
	<select name="" onchange="view_istanze_test_clinico('<?=$idtest?>','<?=$idpaziente?>',this.value,'<?=$idcartella?>','<?=$cartella?>');">
	<option value="1" <?if ($idragr==1) echo ("selected")?>>raggruppa per storico</option>
	<option value="2" <?if ($idragr==2) echo ("selected")?>>raggruppa per impegnativa</option>
	<option value="3" <?if ($idragr==3) echo ("selected")?>>raggruppa per cartella clinica</option>
	</select>
	
	</div>-->
	
		<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
		<tr> 
		    <th>numero test</th> 
			<th>data osservazione</th> 
			<th>operatore</th>
			<th>et&agrave; alla data del test</th> 			
			<td>% pr. superate</td> 
			<td>% pr. migliorate</td> 
			<td>% pr. stazionarie</td> 
		    <td>% pr. regredite</td>
			<td>% pr. migliorate rel</td> 
			<td>% pr. stazionarie rel</td> 
		    <td>% pr. regredite rel</td>
			<td>modifica</td>
			<td>cancella</td>
			
			
			
			
		</tr> 
		</thead> 
		<tbody> 
		
		</tbody> 
	</table> 
			<?
			
			
			$query_padre="select top 1 * from re_test_clinici_compilati_ram where is_padre=1 and idtest=$idtest and idpaziente=$idpaziente";
			if ($idcartella>0)
			$query_padre.=" and idcartella=$idcartella";
			$rs_padre = mssql_query($query_padre, $conn);
			if(!$rs_padre) error_message(mssql_error());
			$i=0;
			while($row_padre = mssql_fetch_assoc($rs_padre))   
			{
			$i++;
			$idtestclinico=$row_padre['idtestclinico'];
			$idtestclinicopadre=$idtestclinico;
			$data_osservazione=formatta_data($row_padre['data_osservazione']);
			$operatore=$row_padre['operatore'];
			$eta_data_test=calcola_eta_alla_data_del_test($row_padre['data_osservazione'],$idpaziente);
			$idtest=$row_padre['idtest'];
			//calcola_stat_figli_test_clinici($idtestclinico,$idtest,$idtestclinico,$idcartella,$idpaziente)
		
			$query = "SELECT * from re_test_clinici_ramificati where idtest_padre=$idtest";	
			$rs_ram = mssql_query($query, $conn);
			$conta=mssql_num_rows($rs_ram);
			$conta=0;
			while($row_ram = mssql_fetch_assoc($rs_ram))   
			{
			$idtest_figlio=$row_ram['idtest_figlio'];
			
			$query = "SELECT * from re_test_clinici_compilati_ram where idtest=$idtest_figlio and idtestclinicopadre=$idtestclinicopadre";	
			$rs = mssql_query($query, $conn);
			if($row = mssql_fetch_assoc($rs ))   
			{
				$idtestclinico=$row['idtestclinico'];
				visualizza_istanze_figli_test_clinico_ramificato($idtest_figlio,$idpaziente,$idcartella,$cartella);
				
			}
			}
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
	
		<script>
		
		function add_test_clinico(idcartella,idpaziente,idtest,cartella)
		{
	
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_test_clinico&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idtest="+idtest+"&cartella="+cartella.replace(" ", "%20");
		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	
	
		}
		
		function view_istanze_test_clinico(idtest,idpaziente,idragr,idcartella,cartella)
		{
		$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_test_clinico&idtest="+idtest+"&idragr="+idragr+"&idpaziente="+idpaziente+"&idcartella="+idcartella+"&cartella="+cartella.replace(" ", "%20");
		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	
	
		}
		
			  //$("table").tablesorter({sortList: [[0,0]] ,widthFixed: true, widgets: ['zebra']});
		</script>

</div>

<?	
}


 
/************************************************************************
* funzione aggiungi test clinico     						*
*************************************************************************/

function aggiungi_test_clinico(){

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idtest=$_REQUEST['idtest'];
$cartella=$_REQUEST['cartella'];
$impegnativa=$_REQUEST['impegnativa'];

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
	
	
	$("#data_osservazione").focus();

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

	<div class="titolo_pag"><h1>creazione test clinico:<?=$codice." - ".$nome?> </h1></div>	
	
<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
	
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>

	<input type="hidden" name="action" value="create_test_clinico" />
	<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
	<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
	<input type="hidden" name="idtest" value="<?=$idtest?>" />
	<div class="blocco_centralcat">
	<div class="riga">
        <span class="rigo_mask">
            <div class="testo_mask">data di osservazione</div>
            <div class="campo_mask mandatory data_passata">
                <input type="text" class="campo_data scrittura" id="data_osservazione" name="data_osservazione"/>
            </div>
        </span>
    </div>	
	
	<div class="riga">
        <span class="rigo_mask">
            <div class="testo_mask">associa alla pratica</div>
            <div class="campo_mask">
                <?
				$query = "SELECT * from re_impegnative where idimpegnativa=$impegnativa";	
				//echo($query);
				?>
				<select name="idimpegnativa" class="scrittura" style="width:auto;">				
				<?
				
				$rs1 = mssql_query($query, $conn);
				
				if($row1 = mssql_fetch_assoc($rs1))   
					{
						$idimpegnativa=$row1['idimpegnativa'];
						$data_auth_asl=formatta_data($row1['DataAutorizAsl']);
						$prot_auth_asl=$row1['ProtAutorizAsl'];
						$regime=$row1['regime'];
						$normativa=$row1['normativa'];
					?>
<option value="<?=$idimpegnativa?>"><?=$prot_auth_asl."-".$data_auth_asl." - ".$regime."/".$normativa?></option>
					<?
					}else{
					?>
					<option value="0">nessuna impegnativa associata</option>
					<?}
				?>
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
    
    <th>Prova</th> 
	<th>Risposte</th> 
	<th>Istruzioni Operative</th> 
      
</tr> 
</thead> 

<?
			$query = "SELECT * from test_clinici_prove where idtest=$idtest";	
			$rs1 = mssql_query($query, $conn);
	
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
						$istruzioni_operative=$row1['istruzioni_operative'];
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
								<td>
								<p><?=$istruzioni_operative?></p>
								</td>
								</tr>
						<?
					}
					?>	
		</tbody> 
</table> 
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
}



/************************************************************************
* funzione aggiungi test clinico     						*
*************************************************************************/

function modifica_test_clinico(){

$idtestclinico=$_REQUEST['idtestclinico'];
$idpaziente=0;
$idtest=0;
$idcartella=0;
$idimpegnativa_get=0;
$data_osservazione="";

$conn = db_connect();
$add_sql="";
if (isset($_REQUEST['idtest']))
{
$idtest=$_REQUEST['idtest'];
$add_sql=" and idtest=$idtest";
}
$query="select * from test_clinici_compilati where idtestclinico=$idtestclinico ".$add_sql;
			$rs1 = mssql_query($query, $conn);
			if(!$rs1) error_message(mssql_error());
			$conta=mssql_num_rows($rs1);
				$i=1;
					if($row1 = mssql_fetch_assoc($rs1))   
					{
						$idtest=$row1['idtest'];
						$idpaziente=$row1['idpaziente'];
						$idcartella=$row1['idcartella'];
						$idimpegnativa_get=$row1['idimpegnativa'];
						$data_osservazione=formatta_data($row1['data_osservazione']);
					}
					
					
					
$query="select * from re_test_clinici_result where idtestclinico=$idtestclinico ".$add_sql;
			$rs1 = mssql_query($query, $conn);
			if(!$rs1) error_message(mssql_error());
			$conta=mssql_num_rows($rs1);
				$i=1;
				//$arr_prove_compilate= new array();
					while($row1 = mssql_fetch_assoc($rs1))   
					{
					$idprova=$row1['idprova'];
					$iditem=$row1['iditem'];
					$arr_prove_compilate[$idprova]=$iditem;
						
					}					


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

	<div class="titolo_pag"><h1>modica test clinico: <?=$codice." - ".$nome?> </h1></div>	
	
<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
	
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>

	<input type="hidden" name="action" value="update_test_clinico" />
	<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
	<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
	<input type="hidden" name="idtestclinico" value="<?=$idtestclinico?>" />
	<input type="hidden" name="idtest" value="<?=$idtest?>" />
	<div class="blocco_centralcat">
	<div class="riga">
        <span class="rigo_mask">
            <div class="testo_mask">data di osservazione</div>
            <div class="campo_mask mandatory data_passata">
                <input type="text" class="campo_data scrittura" name="data_osservazione" value="<?=$data_osservazione?>"/>
            </div>
        </span>
    </div>	
	
	<div class="riga">
        <span class="rigo_mask">
            <div class="testo_mask">associa alla pratica</div>
            <div class="campo_mask">
                <?
				
				$query = "SELECT * from re_impegnative where idimpegnativa=$idimpegnativa_get";	
				//echo($query);
				?>
				<select name="idimpegnativa" class="scrittura" style="width:auto;">				
				<?
				
				$rs1 = mssql_query($query, $conn);
				
				if($row1 = mssql_fetch_assoc($rs1))   
					{
						$idimpegnativa=$row1['idimpegnativa'];
						$data_auth_asl=formatta_data($row1['DataAutorizAsl']);
						$prot_auth_asl=$row1['ProtAutorizAsl'];
						$regime=$row1['regime'];
						$normativa=$row1['normativa'];
					?>
<option value="<?=$idimpegnativa?>"><?=$prot_auth_asl."-".$data_auth_asl." - ".$regime."/".$normativa?></option>
					<?
					}else{
					?>
					<option value="0">nessuna impegnativa associata</option>
					<?}
				?>
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
    
    <th>Prova</th> 
	<th>Risposte</th> 
	<th>Istruzioni Operative</th> 
      
</tr> 
</thead> 

<?
			$query = "SELECT * from test_clinici_prove where idtest=$idtest";	
			$rs1 = mssql_query($query, $conn);
	
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
						$istruzioni_operative=$row1['istruzioni_operative'];
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
									$sel="";
									if ($arr_prove_compilate[$idprova]==$iditem)
									$sel="selected";
									?>
									<option <?=$sel?> value="<?=$iditem?>"><?=$etichetta?></option>
									<?
								}
								?>
								
								</select>
								</td>
								<td>
								<p><?=$istruzioni_operative?></p>
								</td>
								</tr>
						<?
					}
					?>	
		</tbody> 
</table> 
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
}


/************************************************************************
* funzione aggiungi test clinico     						*
*************************************************************************/

function aggiungi_test_clinico_ramificato(){

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idtest=$_REQUEST['idtest'];
$cartella=$_REQUEST['cartella'];
$impegnativa=$_REQUEST['impegnativa'];

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

$("#data_osservazione").focus();
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
                <input type="text" class="campo_data scrittura" id="data_osservazione" name="data_osservazione"/>
            </div>
        </span>
    </div>	
	
	<div class="riga">
        <span class="rigo_mask">
            <div class="testo_mask">associa alla pratica</div>
            <div class="campo_mask">
                <?
					$query = "SELECT * from re_impegnative where idimpegnativa=$impegnativa";
				//echo($query);
				?>
				<select name="idimpegnativa" class="scrittura" style="width:auto;">
				<option value="0">seleziona la pratica</option>
				<?
				
				$rs1 = mssql_query($query, $conn);
				
				while($row1 = mssql_fetch_assoc($rs1))   
					{
						$idimpegnativa=$row1['idimpegnativa'];
						$data_auth_asl=formatta_data($row1['DataAutorizAsl']);
						$prot_auth_asl=$row1['ProtAutorizAsl'];
						$regime=$row1['regime'];
						$normativa=$row1['normativa'];
					?>
<option value="<?=$idimpegnativa?>"><?=$prot_auth_asl."-".$data_auth_asl." - ".$regime."/".$normativa?></option>
					<?
					}
				
				
				?>
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
}



function create_test_clinico_ramificato()
{

	$conn=db_connect();
	$datains = date('d/m/Y');
	$orains = date('H:i');
	$ipins = $_SERVER['REMOTE_ADDR'];
	$opeins = $_SESSION['UTENTE']->get_userid();
	$idcartella=$_POST['idcartella'];
	$idpaziente=$_POST['idpaziente'];
	$idtest=$_POST['idtest'];
	$idimpegnativa=$_POST['idimpegnativa'];
	$data_osservazione=$_POST['data_osservazione'];
	
	$query="insert into test_clinici_compilati (idtest,idpaziente,idcartella,idimpegnativa,data_osservazione,datains,orains,ipins,opeins) values ($idtest,$idpaziente,$idcartella,$idimpegnativa,'$data_osservazione','$datains','$orains','$ipins',$opeins)";
	mssql_query($query, $conn);
	
	
	$query="select max (idtestclinico) as idtestclinico from test_clinici_compilati where opeins=$opeins and idtest=$idtest";
	
	$rs = mssql_query($query, $conn);
		
	if(!$rs){
	echo("no");
	exit();
	die();
	}
$idtestclinico=0;
	if($row = mssql_fetch_assoc($rs))
		$idtestclinico=$row['idtestclinico'];
	
	// calcola figli � presente nel file functions_test_clinici.php
	calcola_figli_test_clinici($idtestclinico,$idtest,$idtestclinico);
	
	
}



function update_test_clinico()
{

	$conn=db_connect();
	$datains = date('d/m/Y');
	$orains = date('H:i');
	$ipins = $_SERVER['REMOTE_ADDR'];
	$opeins = $_SESSION['UTENTE']->get_userid();
	$idcartella=$_POST['idcartella'];
	$idpaziente=$_POST['idpaziente'];
	$idtestclinico=$_POST['idtestclinico'];
	$idimpegnativa=$_POST['idimpegnativa'];
	$data_osservazione=$_POST['data_osservazione'];
	$idtest=$_POST['idtest'];
	
	$query="update test_clinici_compilati set idimpegnativa=$idimpegnativa,data_osservazione='$data_osservazione',opeagg=$opeins where idtestclinico=$idtestclinico";
	mssql_query($query, $conn);
	scrivi_log ($idtestclinico,'test_clinici_compilati','agg','idtestclinico');
	
	
	
	$query="select * from re_test_clinici_prove where idtest=$idtest";

	$rs = mssql_query($query, $conn);
		
	if(!$rs)
	{
	echo("no");
	exit();
	die();
	}

	while($row = mssql_fetch_assoc($rs))
	{
		$idprova=$row['idprova'];
		$risposta="risposta".$idprova;
		$risposta_postata=$_POST[$risposta];
		$query="update test_clinici_compilati_details set iditem=$risposta_postata where idprova=$idprova and  idtestclinico_compilato=$idtestclinico";
		//echo($query);
		mssql_query($query, $conn);
	}
	
	
echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria.php?do=");
exit();

	
}

function create_cartella($idpaziente){

	$conn 			= db_connect();
	$data_apertura	= $_POST['data_apertura'];
	$datains 		= date('d/m/Y');
	$orains 		= date('H:i');
	$ipins 			= $_SERVER['REMOTE_ADDR'];
	$opeins 		= $_SESSION['UTENTE']->get_userid();
	$idcartella		= $_POST['idcartella'];
	$codiceutente 	= $_POST['codiceutente'];
	if($idpaziente=="")$idpaziente=$_POST['idpaziente'];
	//echo("paz: ".$idpaziente." cart".$idcartella);
	//exit();

	if($idcartella==""){
		$query="select max(versione) as numero_versione from utenti_cartelle where idutente=$idpaziente";

		$rs = mssql_query($query, $conn);
		
		if(!$rs)
			error_message(mssql_error());

		if($row = mssql_fetch_assoc($rs))   
		{
			if($row['numero_versione'] == 'NULL')
				$versione = 1;
			else
				$versione=$row['numero_versione']+1;
		}
		else
			$versione=1;

		if ($versione==1) {
			
			$codice_cartella=$idpaziente;
			if($codiceutente != ''){
				$codice_cartella=$codiceutente;
			}
			
			$query="insert into utenti_cartelle (idutente,codice_cartella,versione,data_creazione,idmedico_creazione,datains,orains,opeins,ipins) values ($idpaziente,'$codice_cartella',$versione,'$data_apertura',$opeins,'$datains','$orains',$opeins,'$ipins')";
			$result = mssql_query($query, $conn);
			$query="update utenti set CodiceUtente='$codice_cartella' where idUtente=$idpaziente";
			$result = mssql_query($query, $conn);
			
		} else {
			/*
			$query="select top 1 codice_cartella from utenti_cartelle where idutente=$idpaziente";
			$rs = mssql_query($query, $conn);
			if(!$rs) error_message(mssql_error());
			
			if($row = mssql_fetch_assoc($rs))  
				//$codice_cartella=$row['codice_cartella'];
				$codice_cartella=$idpaziente;
				
			if($codiceutente != ''){
				$codice_cartella = $codiceutente;
			}
			else
				$codice_cartella = 1;
			*/
			// SBO 02/08/2023 : il codice commentato in precedenza non funzionava nel caso in cui la pagina venisse caricata
			// 		senza codiceutente passato in post in quanto impostava il codice cartella sempre a 1 non tenendo conto 
			//		del risultato della query
			
			if($codiceutente != ''){
				$codice_cartella = $codiceutente;
			} else {
				$query="select top 1 codice_cartella from utenti_cartelle where idutente=$idpaziente";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
			
				$row = mssql_fetch_assoc($rs);
				$codice_cartella=$row['codice_cartella'];
			}
			
			$query="insert into utenti_cartelle (idutente,codice_cartella,versione,data_creazione,idmedico_creazione,datains,orains,opeins,ipins) values ($idpaziente,'$codice_cartella',$versione,'$data_apertura',$opeins,'$datains','$orains',$opeins,'$ipins')";
			$result = mssql_query($query, $conn);
		}
	}
	if ($_POST['effettuapianificazione']=='on'){

		if($idcartella==""){	
			$query="select max(id) as last_cartella from utenti_cartelle where idutente=$idpaziente";
			$rs = mssql_query($query, $conn);		
			if(!$rs) {
				echo("no-2402");
				exit();
			}	
			
			if($row = mssql_fetch_assoc($rs)) $idcartella=$row['last_cartella'];
			
		}else{
			$query="update utenti_cartelle set data_creazione='$data_apertura' where id=$idcartella";
			$rs = mssql_query($query, $conn);
			scrivi_log ($idcartella,'utenti_cartelle','agg','id');	
		}
		
		$idregime=$_POST['regime_assistenza'];
		$query="SELECT * FROM regime WHERE (idregime = $idregime)";
		$rs2 = mssql_query($query, $conn);
		
		if ($row2=mssql_fetch_assoc($rs2))	
			$idnormativa=$row2['idnormativa'];

		$query="update utenti_cartelle set idregime=$idregime, idnormativa=$idnormativa where id=$idcartella";
		$rs = mssql_query($query, $conn);
			
		$query="insert into cartelle_pianificazione_testata (id_cartella,datains,orains,opeins,ipins) values ($idcartella,'$datains','$orains',$opeins,'$ipins')";
		$rs = mssql_query($query, $conn);
		
		$query="select max(id_pianificazione_testata) as id_pian_testata from cartelle_pianificazione_testata where opeins=$opeins";
		$rs = mssql_query($query, $conn);
		if($row = mssql_fetch_assoc($rs))  $id_pian_testata=$row['id_pian_testata'];
		mssql_free_result($rs);	
	
		$arr_ordine=Array();
		if (isset($_POST['debug']))
		{
		$ordine=$_POST['debug'];
		$arr_ordine=split(" ",$ordine);
		}
		else
		{
			while($j<100)
			$arr_ordine[$j]=$j+1;		
		}		
		$numerofield=sizeof($arr_ordine)-1;
		$k=0;
		while($k<$numerofield)
		{
				 $idmodulo=$arr_ordine[$k];
			     $selected=$_POST['selected'.($idmodulo)];
				 $mod_impegnativa=$_POST['mod_impegnativa'.($idmodulo)];
				 $default=$_POST['default'.($idmodulo)];		
				 $obbligatorio=$_POST['obbligatorio'.($idmodulo)];			 
				 $scelta=$_POST['scelta'.($idmodulo)];	
				 $scad_flg=$_POST['scad_flg'.($idmodulo)];
				 $scadenza=$_POST['scadenza'.($idmodulo)];
				 $scadenza_cicl_data=$_POST['scadenza_cicl_data'.($idmodulo)];
				 $scadenza_cicl_elenco_date=$_POST['scadenza_cicl_elenco_date'.($idmodulo)];
				 $trattamenti=$_POST['trattamenti'.($idmodulo)];				 
				 $data_fissa=$_POST['data_fissa'.($idmodulo)];		
				 $modulo=$_POST['modulo'.($idmodulo)];				//id_modulo_padre
				 $idmodulo_id=$_POST['idmodulo'.($idmodulo)];		//id_modulo_versione
				 $idmedico=$_POST['medico_id_'.($idmodulo)];			
				
			if ($default=='on')
				{					
				
				$query_r="SELECT * FROM moduli WHERE id=$idmodulo_id";
				$result_r = mssql_query($query_r, $conn);
				if($row_r=mssql_fetch_assoc($result_r))	$replica=$row_r['replica'];
				mssql_free_result($result_r);				
				
					if($scad_flg=="n"){
						$scadenza="";
						$scadenza_cicl_data="";
						$scadenza_cicl_elenco_date="";
						$trattamenti="";
						$data_fissa="";
					 }elseif($scad_flg=="c"){
						$scadenza_cicl_data="";
						$scadenza_cicl_elenco_date="";
						$scadenza_cicl_data="";
						$trattamenti="";
						$data_fissa="";
					 }elseif($scad_flg=="cd"){
						$trattamenti="";
						$data_fissa="";
					 }elseif($scad_flg=="t"){
						$scadenza_cicl_data="";
						$scadenza_cicl_elenco_date="";
						$scadenza="";
						$data_fissa="";
					 }elseif($scad_flg=="d"){
						$scadenza_cicl_data="";
						$scadenza_cicl_elenco_date="";
						$scadenza="";
						$trattamenti="";
					 }		
				 
						
											
					$query = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)";					
					$query.=" VALUES($id_pian_testata,$modulo,$idmodulo_id,'$scelta','$trattamenti','$scadenza',$idmedico,$replica";
					if(($mod_impegnativa!="")and($mod_impegnativa!="0")) 
						 $query.=",$mod_impegnativa";
					else $query.=",NULL";
					if($data_fissa!="") 
						 $query.=",'$data_fissa'";
					else $query.=",NULL";						
					if($scadenza_cicl_data!="") 
						 $query.=",'$scadenza_cicl_data'";
					else $query.=",NULL";						
					$query.=")";						
					echo($query);					
					$result1 = mssql_query($query, $conn);				
					if(!$result1) 
					{
						echo("no-2567");
						exit();						
					}

					// inserisco l'elenco delle date pianificate in caso di "scadenza ciclica a partire dal" (cd)
					if (trim($scadenza_cicl_data) !== "" && trim($scadenza_cicl_elenco_date) !== "")
					{
					
						$arr_elenco_date_scadenze = explode('|',$scadenza_cicl_elenco_date);
						$arr_elenco_date_length = sizeof($arr_elenco_date_scadenze);
						
						$query_delete_date_scad_cicl = "DELETE FROM cartelle_pianificazione_date_scad_cicl WHERE id_paziente = $idpaziente AND id_cartella = $idcartella AND id_modulo_padre = $modulo"; // AND id_regime = $idregime AND id_normativa = $idnormativa";
						$result_delete_date_scad_cicl = mssql_query($query_delete_date_scad_cicl, $conn);
				
						$query_date_scad_cicl = "INSERT INTO cartelle_pianificazione_date_scad_cicl (id_cartella_pianificazione_testata, id_cartella, id_paziente, id_regime, id_normativa, id_modulo_padre, id_modulo_versione, scadenza, data, compilato, opeins, datains, orains, ipins, dataagg) VALUES ";
						for($s = 0; $s < $arr_elenco_date_length; $s++)
						{
							$data_scadenza = explode('_',$arr_elenco_date_scadenze[$s]);
							$data = $data_scadenza[0];
							
							if($data_scadenza[1] == "") 	// intervallo gg scadenza
								 $scadenza = 0;
							else $scadenza = $data_scadenza[1];
							
							$compilato = $data_scadenza[2];	// 1 = compilato, 0 = non compilato
							
							$query_date_scad_cicl.="($id_pian_testata, $idcartella, $idpaziente, $idregime, $idnormativa, $modulo, $idmodulo_id, $scadenza, '$data', $compilato, $opeins,'$datains','$orains','$ipins'";
							
							$data_compilazione = $data_scadenza[3];		// se compilato = 1, contiene la data di compilazione del modulo
							if($data_compilazione == "")
								 $query_date_scad_cicl .= ",NULL),";
							else $query_date_scad_cicl .= ",'$data_compilazione'),";
							
/*echo ("<br>DATA COMP_ $data_compilazione<br>");
echo($query_date_scad_cicl);*/
						}
						$query_date_scad_cicl = substr($query_date_scad_cicl, 0, -1);
						
						//echo($query_date_scad_cicl);
						$result_date_scad_cicl = mssql_query($query_date_scad_cicl, $conn);
							
						if(!$result_date_scad_cicl) 
						{
							echo("no-2610");
							exit();						
						}
					} else {
						// se non trovo date, potrebbe esser cambiata la tipologia di scadenza (oppure non � mai stata impostata la "scadenza ciclica a partie dal")
						// cerco ed elimino eventuali vecchie date
						$query_delete_date_scad_cicl = "DELETE FROM cartelle_pianificazione_date_scad_cicl WHERE id_paziente = $idpaziente AND id_cartella = $idcartella AND id_modulo_padre = $modulo"; // AND id_regime = $idregime AND id_normativa = $idnormativa";
						$result_delete_date_scad_cicl = mssql_query($query_delete_date_scad_cicl, $conn);
					}
					
				}
			$k++;
		}
		
		$arr_ordine=Array();
		if (isset($_POST['debug_t']))
		{
		$ordine=$_POST['debug_t'];
		$arr_ordine=split(" ",$ordine);
		}
		else
		{
			while($j<100)
			$arr_ordine[$j]=$j+1;		
		}		
		$numerofield=sizeof($arr_ordine)-1;
		$k=0;
		while($k<$numerofield)
		{
			 $id_t=$arr_ordine[$k];
			 $selected=$_POST['selected_t'.($id_t)];
			 $mod_impegnativa=$_POST['mod_impegnativa_t'.($id_t)];
			 $default=$_POST['default_test'.($id_t)];				
			 $idmedico=$_POST['medico_test_id_'.($id_t)];
			 $idtest=$_POST['idtest'.($id_t)];
			 
			if (($default=='on'))
			{							
				$query = "INSERT INTO cartelle_pianificazione_test (id_cartella_pianificazione_testata,id_test,id_operatore,id_impegnativa)";					
				$query.=" VALUES($id_pian_testata,$idtest,$idmedico";						
				if(($mod_impegnativa!="")and($mod_impegnativa!="0")) 
					$query.=",$mod_impegnativa";
					else
					$query.=",NULL";				
				$query.=")";						
			//echo($query);							
			$result1 = mssql_query($query, $conn);				
			if(!$result1) 
			{
				echo("no");
				exit();						
			}
			}
		$k++;
		}
	}
	$idpaziente=trim($idpaziente);
	$idpaziente=trim($idpaziente);
	$query="UPDATE istanze_testata set id_paziente=0, id_cartella=$idcartella WHERE (id_paziente=$idpaziente and id_cartella=0)";
	$result1 = mssql_query($query, $conn);	
	echo ("ok;".$idpaziente.";6;re_pazienti_sanitaria.php?do=");
	exit();
}





function add_pianificazione(){

$conn = db_connect();

$datains = date('d/m/Y');
$orains = date('H:i');
$ipins = $_SERVER['REMOTE_ADDR'];
$opeins = $_SESSION['UTENTE']->get_userid();

$idcartella=$_POST['idcartella'];
$idpaziente=$_POST['idpaziente'];
$idregime=get_regime_paziente($idpaziente);
$idnormativa=get_normativa_paziente($idpaziente);	

$query="select id_pianificazione_testata as id_pian_testata from cartelle_pianificazione_testata where id_cartella=$idcartella";
$result_1 = mssql_query($query, $conn);
if ($row_1=mssql_fetch_assoc($result_1)) $id_pian_testata=$row_1['id_pian_testata'];
mssql_free_result($result_1);
scrivi_log ($id_pian_testata,'cartelle_pianificazione_testata','agg','id_pianificazione_testata');	
	
		$arr_ordine=Array();
		if (isset($_POST['debug']))
		{
		$ordine=$_POST['debug'];
		$arr_ordine=split(" ",$ordine);
		}
		else
		{
			while($j<100)
			$arr_ordine[$j]=$j+1;		
		}
		
		$numerofield=sizeof($arr_ordine)-1;
			
		$k=0;
		
		while($k<$numerofield)
		{			 
			 $idmodulo=$arr_ordine[$k];
			 $selected=$_POST['selected'.($idmodulo)];
			 $mod_impegnativa=$_POST['mod_impegnativa'.($idmodulo)];
			 $default=$_POST['default'.($idmodulo)];	
			 $obbligatorio=$_POST['obbligatorio'.($idmodulo)];			 
			 $scelta=$_POST['scelta'.($idmodulo)];	
			 $scad_flg=$_POST['scad_flg'.($idmodulo)];
			 $scadenza=$_POST['scadenza'.($idmodulo)];
			 $scadenza_cicl_data=$_POST['scadenza_cicl_data'.($idmodulo)];
			 $scadenza_cicl_elenco_date=$_POST['scadenza_cicl_elenco_date'.($idmodulo)];
			 $trattamenti=$_POST['trattamenti'.($idmodulo)];				 
			 $data_fissa=$_POST['data_fissa'.($idmodulo)];		
			 $modulo=$_POST['modulo'.($idmodulo)];				//id_modulo_padre
			 $idmodulo_id=$_POST['idmodulo'.($idmodulo)];		//id_modulo_versione
			 $idmedico=$_POST['medico_id_'.($idmodulo)];
		
			$query_r="SELECT * FROM moduli WHERE id=$idmodulo_id";
			$result_r = mssql_query($query_r, $conn);
			if($row_r=mssql_fetch_assoc($result_r))	$replica=$row_r['replica'];
			mssql_free_result($result_r);		
			
			if($scad_flg=="n"){
				$scadenza="";
				$scadenza_cicl_data="";
				$scadenza_cicl_elenco_date="";
				$trattamenti="";
				$data_fissa="";
			 }elseif($scad_flg=="c"){
				$scadenza_cicl_data="";
				$scadenza_cicl_elenco_date="";
				$scadenza_cicl_data="";
				$trattamenti="";
				$data_fissa="";
			 }elseif($scad_flg=="cd"){
				$trattamenti="";
				$data_fissa="";
			 }elseif($scad_flg=="t"){
				$scadenza_cicl_data="";
				$scadenza_cicl_elenco_date="";
				$scadenza="";
				$data_fissa="";
			 }elseif($scad_flg=="d"){
				$scadenza_cicl_data="";
				$scadenza_cicl_elenco_date="";
				$scadenza="";
				$trattamenti="";
			 }	
			 		
		
			if (($default=='on')and($selected)) {
			 
				$query="SELECT * FROM cartelle_moduli WHERE (idcartella=$idcartella) and (idmodulo=$modulo)";				
				$result1 = mssql_query($query, $conn);				
				if (mssql_num_rows($result1)==0){
					$query = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)";
					$query.=" VALUES($id_pian_testata,$modulo,$idmodulo_id,'$scelta','$trattamenti','$scadenza',$idmedico,$replica,";						
					if(($mod_impegnativa!="")and($mod_impegnativa!="0")) 
						 $query.=",$mod_impegnativa";
					else $query.=",NULL";
					if($data_fissa!="") 
						 $query.=",'$data_fissa'";
					else $query.=",NULL";						
					if($scadenza_cicl_data!="") 
						 $query.=",'$scadenza_cicl_data'";
					else $query.=",NULL";						
					$query.=")";	
					
					$result1 = mssql_query($query, $conn);
					if(!$result1) 
					{
						echo("no-2713");
						exit();
						die();
					}
				}else{
					if($data_fissa!="") 
						 $data_fissa_query ="data_fissa='$data_fissa'";
					else $data_fissa_query ="data_fissa=NULL";
					
					if($scadenza_cicl_data!="") 
						 $scadenza_cicl_data_query.="data_iniz_scad_cicl='$scadenza_cicl_data'";
					else $scadenza_cicl_data_query.="data_iniz_scad_cicl=NULL";	
					
					if($replica!="")
						$query = "UPDATE cartelle_pianificazione SET scadenza='$scadenza',trattamenti='$trattamenti',".$data_fissa_query.",".$scadenza_cicl_data_query.",replica='$replica' WHERE (id_cartella_pianificazione_testata=$id_pian_testata) and (id_modulo_padre=$modulo)and (id_impegnativa=$mod_impegnativa)";
						else
						$query = "UPDATE cartelle_pianificazione SET scadenza='$scadenza',trattamenti='$trattamenti',".$data_fissa_query.",".$scadenza_cicl_data_query.", WHERE (id_cartella_pianificazione_testata=$id_pian_testata) and (id_modulo_padre=$modulo)and (id_impegnativa=$mod_impegnativa)";	
					
					$result1 = mssql_query($query, $conn);
				}
			}
			
			// inserisco l'elenco delle date pianificate con caso di "scadenza ciclica a partire dal" (cd)
					if (trim($scadenza_cicl_data) !== "" && trim($scadenza_cicl_elenco_date) !== "")
					{
					
						$arr_elenco_date_scadenze = explode('|',$scadenza_cicl_elenco_date);
						$arr_elenco_date_length = sizeof($arr_elenco_date_scadenze);
						
						$query_delete_date_scad_cicl = "DELETE FROM cartelle_pianificazione_date_scad_cicl WHERE id_paziente = $idpaziente AND id_cartella = $idcartella AND id_modulo_padre = $modulo"; // AND id_regime = $idregime AND id_normativa = $idnormativa";
						$result_delete_date_scad_cicl = mssql_query($query_delete_date_scad_cicl, $conn);
				
						$query_date_scad_cicl = "INSERT INTO cartelle_pianificazione_date_scad_cicl (id_cartella_pianificazione_testata, id_cartella, id_paziente, id_regime, id_normativa, id_modulo_padre, id_modulo_versione, scadenza, data, compilato, opeins, datains, orains, ipins, dataagg) VALUES ";
						for($s = 0; $s < $arr_elenco_date_length; $s++)
						{
							$data_scadenza = explode('_',$arr_elenco_date_scadenze[$s]);
							$data = $data_scadenza[0];
							
							if($data_scadenza[1] == "") 	// intervallo gg scadenza
								 $scadenza = 0;
							else $scadenza = $data_scadenza[1];
							
							$compilato = $data_scadenza[2];	// 1 = compilato, 0 = non compilato
							
							$query_date_scad_cicl.="($id_pian_testata, $idcartella, $idpaziente, $idregime, $idnormativa, $modulo, $idmodulo_id, $scadenza, '$data', $compilato, $opeins,'$datains','$orains','$ipins'";
							
							$data_compilazione = $data_scadenza[3];		// se compilato = 1, contiene la data di compilazione del modulo
							if($data_compilazione == "")
								 $query_date_scad_cicl .= ",NULL),";
							else $query_date_scad_cicl .= ",'$data_compilazione'),";

						}
						$query_date_scad_cicl = substr($query_date_scad_cicl, 0, -1);
						
						//echo($query_date_scad_cicl);							
						$result_date_scad_cicl = mssql_query($query_date_scad_cicl, $conn);
							
						if(!$result_date_scad_cicl) 
						{
							echo("no-2559");
							exit();						
						}
					} else {
						// se non trovo date, potrebbe esser cambiata la tipologia di scadenza (oppure non � mai stata impostata la "scadenza ciclica a partie dal")
						// cerco ed elimino eventuali vecchie date
						$query_delete_date_scad_cicl = "DELETE FROM cartelle_pianificazione_date_scad_cicl WHERE id_paziente = $idpaziente AND id_cartella = $idcartella AND id_modulo_padre = $modulo"; // AND id_regime = $idregime AND id_normativa = $idnormativa";
						$result_delete_date_scad_cicl = mssql_query($query_delete_date_scad_cicl, $conn);
					}
					
			
	
			
			 if ($default==""){
				$query="DELETE FROM cartelle_moduli WHERE (idcartella=$idcartella) and (idmodulo=$modulo)";				
				$result1 = mssql_query($query, $conn);	
				$query="DELETE FROM moduli_medici WHERE (id_cartella=$idcartella) and (id_modulo=$modulo)";
				$result1 = mssql_query($query, $conn);
			}
				if ($default=="on"){
					$med='medico'.($k+1);	
					$med_old='medico_old'.($k+1);	
					$medico=$_POST[$med];
					$medico_old=$_POST[$med_old];
						if ($medico_old!=$medico){
							$opeins=$_SESSION['UTENTE']->get_userid();
							$query="SELECT * FROM moduli_medici WHERE (id_cartella=$idcartella) and (id_modulo=$modulo) and (id_operatore=$medico)";
							$result1 = mssql_query($query, $conn);
							if (mssql_num_rows($result1)==0){
								$query = "INSERT INTO moduli_medici (id_regime,id_operatore,id_modulo,opeins,id_cartella) VALUES($idregime,$medico,$modulo,$opeins,$idcartella)";							
								$result1 = mssql_query($query, $conn);
							
								$query = "SELECT MAX(id) FROM moduli_medici WHERE (opeins='$opeins')";
								$result = mssql_query($query, $conn);
								if(!$result) error_message(mssql_error());

								if(!$row = mssql_fetch_row($result))
									die("errore MAX select");
									
								$idmod_med=$row[0];	
								scrivi_log ($idmod_med,'moduli_medici','agg','id');
							}
						}
				}
			$k++;
		}


$idpaziente=trim($idpaziente);
$query="DELETE FROM utenti_cartelle_impegnative WHERE (idcartella=$idcartella) and (idpaziente=$idpaziente)";
$rs1 = mssql_query($query, $conn);
//echo($query);
$query = "SELECT  idimpegnativa, centrodicosto, normativa, regime, idutente, DataPrescrizione, DataPianoTrattamento, DataAutorizAsl, idregime, idnormativa, 
             DataDimissione, ProtAutorizAsl FROM dbo.re_pazienti_impegnative WHERE (idutente = $idpaziente)";	
$rs1 = mssql_query($query, $conn);
$num_rows=mssql_num_rows($rs1);
for($i=1;$i<=$num_rows;$i++){
	if(isset($_POST['impegnativa'.$i])){
		$query="INSERT INTO utenti_cartelle_impegnative (idcartella,idimpegnativa,idpaziente) VALUES($idcartella,".$_POST['impegnativa'.$i].",$idpaziente)";		
		$rs1 = mssql_query($query, $conn);
	}
}
echo ("ok;".$idpaziente.";5;re_pazienti_sanitaria.php?do=");
exit();
}

function converti_firma_istanza() { ?>
<script>
	function view_istanze_modulo(idcartella,idpaziente,idregime,idmodulo,cart,imp) {
		$("#layer_nero2").toggle();
		$('#cartella_clinica').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+imp;
		$("#cartella_clinica").load(pagina_da_caricare,'', function(){
		loading_page('loading');
			$("#container-5 .tabs-selected").removeClass('tabs-selected');
		});
	}
</script>
<?
	$conn = db_connect();
	$id_istanza_testata = $_REQUEST['id_istanza_testata'];
	$converti_in = $_REQUEST['converti_in'];
	$idcartella = $_REQUEST['idcartella'];
	$idpaziente = $_REQUEST['idpaziente'];
	$idmoduloversione = $_REQUEST['idmoduloversione'];
	$idimpegnativa = $_REQUEST['idimpegnativa'];
	$duplicata = $_REQUEST['duplicata'];

	$conn = db_connect();
	$esito = false;
	$duplicata_sql = !empty($duplicata) && $duplicata ? ", cambio_firma = 1 " : "";

	if($converti_in == 'manuale') {
		$query = "UPDATE istanze_testata
					SET firmato_digitalmente = 'n', firmato_fea = 'n', allegato = NULL, allegato2 = NULL, data_allegato = NULL, data_allegato2 = NULL" . $duplicata_sql . "
					WHERE id_istanza_testata = $id_istanza_testata";	
		echo
		$rs = mssql_query($query, $conn);
		
		if($rs != false) {
			$query = "DELETE FROM istanze_testata_firme
						WHERE id_istanza_testata = $id_istanza_testata";	
			$rs = mssql_query($query, $conn);
		}
	} elseif($converti_in == 'fdr') {
		$query = "UPDATE istanze_testata
					SET firmato_digitalmente = 's', firmato_fea = 'n', allegato = NULL, allegato2 = NULL, data_allegato = NULL, data_allegato2 = NULL" . $duplicata_sql . "
					WHERE id_istanza_testata = $id_istanza_testata";	
		$rs = mssql_query($query, $conn);

		if($rs != false) {
			$opeins = $_SESSION['UTENTE']->get_userid();
			
			$query = "INSERT INTO istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato, tipo_firma)
						VALUES ($id_istanza_testata, $opeins, 'a', 'fdr')";	
			$rs = mssql_query($query, $conn);
		}
	} elseif($converti_in == 'fea') {
		$query = "UPDATE istanze_testata
					SET firmato_digitalmente = 'n', firmato_fea = 's', allegato = NULL, allegato2 = NULL, data_allegato = NULL, data_allegato2 = NULL" . $duplicata_sql . "
					WHERE id_istanza_testata = $id_istanza_testata";	
		$rs = mssql_query($query, $conn);
		
		if($rs != false) {
			$opeins = $_SESSION['UTENTE']->get_userid();
			
			$query = "INSERT INTO istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato, tipo_firma)
						VALUES ($id_istanza_testata, $opeins, 'a', 'fea')";	
			$rs = mssql_query($query, $conn);
		}
	}
?>
<script>
	javascript:view_istanze_modulo('<?=$idcartella?>','<?=$idpaziente?>', '','<?=$idmoduloversione?>','','<?=$idimpegnativa?>')
</script>
<?
	exit();
}



function cancella_modulo(){

?>
<script>
function view_istanze_modulo(idcartella,idpaziente,idmodulo,cart,imp)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+imp;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
</script>
<?

$conn = db_connect();
$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idmodulo1=$_REQUEST['idmodulo1'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];
$idimpegnativa=$_REQUEST['idimpegnativa'];
/*gestione della cancellazione in testata e dettaglio istanza*/

$query="SELECT id_istanza_testata FROM istanze_testata WHERE (id_cartella=".$idcartella.") and (id_modulo_padre=".$idmodulo1.")and (id_inserimento=".$idinserimento.")";

if($idimpegnativa !== "" && $idimpegnativa > 0)
	$query.= " AND (id_impegnativa = $idimpegnativa)";
  
$result= mssql_query($query, $conn);
$row=mssql_fetch_assoc($result);
$id_testata=$row['id_istanza_testata'];
$query="DELETE FROM istanze_testata WHERE (id_istanza_testata=".$id_testata.")";
$result= mssql_query($query, $conn);
$query="DELETE FROM istanze_dettaglio WHERE (id_istanza_testata=".$id_testata.")";
$result= mssql_query($query, $conn);
$query="DELETE FROM istanze_testata_firme WHERE (id_istanza_testata=".$id_testata.")";
$result= mssql_query($query, $conn);

// aggiorno il flag "compilato", se il modulo � stato pianificato con scadenza ciclica e date prefissate 
$query_set_non_compilato = "UPDATE cartelle_pianificazione_date_scad_cicl 
							SET compilato = 0, id_inserimento_modulo = NULL, dataagg = NULL
							WHERE id = (
								SELECT TOP (1) id 
								FROM cartelle_pianificazione_date_scad_cicl 
								WHERE id_paziente = $idpaziente AND id_cartella = $idcartella AND id_modulo_padre = $idmodulo1 AND compilato = 1 AND id_inserimento_modulo = $idinserimento
							)";

$result_set_non_compilato = mssql_query($query_set_non_compilato, $conn);
//echo $result_set_non_compilato;

$datains = date('d/m/Y');
$orains = date('H:i');
$ipins = $_SERVER['REMOTE_ADDR'];
$opeins = $_SESSION['UTENTE']->get_userid();
$query_log="insert into log_cancella_istanza (id_utente,id_modulo_versione,istanza,idcartella,data_modifica,ora_modifica,ope_modifica,ip_modifica) 
				values ($idpaziente,$idmoduloversione,$idinserimento,$idcartella,'$datains','$orains',$opeins,'$ipins')";
$result_log = mssql_query($query_log, $conn);
/*fine gestione*/

//visualizza_istanze_modulo();
?>
<script>javascript:view_istanze_modulo('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','','<?=$idimpegnativa?>')</script>
<?
//echo ("ok;".$idpaziente.";5;re_pazienti_sanitaria.php?do=visualizza_istanze_modulo&idcartella="+$idcartella+"&idpaziente="+$idpaziente+"&idmodulo="+$idmodulo1+"&cartella="+$cart+"&impegnativa="+$imp);
exit();
}


function cancella_allegato(){

?>
<script>
function view_istanze_modulo(idcartella,idpaziente,idmodulo,cart,imp)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+imp;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
</script>
<?

$conn = db_connect();
$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idmodulo1=$_REQUEST['idmodulo1'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];
$idimpegnativa=$_REQUEST['idimpegnativa'];
$tipoallegato=$_REQUEST['tipoallegato'];

if($tipoallegato == "allegato" || $tipoallegato == "allegato2" || $tipoallegato == "allegato3"  || $tipoallegato == "allegato4")
{
	// prendo il nome dell'allegato per cancellarlo fisicamente
	$query_get_allegato = "SELECT id_istanza_testata, $tipoallegato FROM istanze_testata WHERE (id_cartella=".$idcartella.") and (id_modulo_padre=".$idmodulo1.")and (id_inserimento=".$idinserimento.")";
	$result_get_allegato = mssql_query($query_get_allegato, $conn);
	$row_get_allegato = mssql_fetch_assoc($result_get_allegato);
	
	// SBO & AME 08/09/2023: erroneamente era finito il carattere $ davanti a tipoallegato e id_istanza_testata, impedendo 
	//			il corretto funzionamento degli UPDATE successivi. $row_get_allegato["$tipoallegato"] -> $row_get_allegato["tipoallegato"]
	$nome_allegato 		= $row_get_allegato["tipoallegato"];
	$id_istanza_testata = $row_get_allegato["id_istanza_testata"];
	unlink("/modelli_word/allegato/$nome_allegato");
	
	// aggiorno a NULL il campo tipoallegato
	$query_canc_allegato = "UPDATE istanze_testata SET 
								$tipoallegato = NULL, 
								data_allegato = NULL 
							WHERE (id_cartella=".$idcartella.") and (id_modulo_padre=".$idmodulo1.")and (id_inserimento=".$idinserimento.")";
	$result_canc_allegato = mssql_query($query_canc_allegato, $conn);
	
	// aggiorno allo stato di attesa il record relativo alla firma del documento
	$query_canc_firma_allegato = "UPDATE istanze_testata_firme SET 
								stato_firma_".$tipoallegato." = 'a', 
								data_firma_".$tipoallegato." = NULL 
								WHERE id_istanza_testata = $id_istanza_testata AND stato_firma_".$tipoallegato." IS NOT NULL";
	$result_canc_firma_allegato = mssql_query($query_canc_firma_allegato, $conn);
?>
	<script>javascript:view_istanze_modulo('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','','<?=$idimpegnativa?>')</script>
<?
} else { ?>
	<script>alert("Errore nell'invio del comando.");</script>
 <?}
exit();
}



function valida_modulo(){

?>

<script>
	function view_istanze_modulo(idcartella,idpaziente,idmodulo,cart,imp)
	{
		$("#layer_nero2").toggle();
		$('#anagrafica_farmaci').innerHTML="";
		
		pagina_da_caricare = "re_pazienti_sanitaria.php?do=show_anagrafica_farmaci&id="+idpaziente;
		
		$("#anagrafica_farmaci").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		});
	}
</script>
<?

$conn = db_connect();

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idmodulo1=$_REQUEST['idmodulo1'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];
$idimpegnativa=$_REQUEST['idimpegnativa'];
$action=$_REQUEST['action'];

if($action == 's' || $action == 'n') {
	$query="UPDATE istanze_testata SET validazione = '$action', data_validazione = getdate() WHERE (id_cartella=$idcartella) and (id_modulo_padre=$idmodulo1)and (id_inserimento=$idinserimento)";
	$result= mssql_query($query, $conn);
} else { ?>
	<script>
		alert("Errore nell\'invio del comando.");
		return false;
	</script>
<?} 

?>
<script>javascript:view_istanze_modulo('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','','','<?=$idimpegnativa?>')</script>
<?
exit();
}



function annulla_modulo(){
	die('Contenuto bloccato');
?>
<script>
	function view_istanze_modulo(idcartella,idpaziente,idmodulo,cart,imp)
	{
		$("#layer_nero2").toggle();
		$('#cartella_clinica').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+imp;
		$("#cartella_clinica").load(pagina_da_caricare,'', function() {
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		});
	}
</script>
<?

$conn = db_connect();

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idmodulo1=$_REQUEST['idmodulo1'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];
$idimpegnativa=$_REQUEST['idimpegnativa'];
$action=$_REQUEST['action'];

?>
<script>
	small_window("http://192.168.30.100/firmadigitale/annulla_istanza.php?idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmodulopadre=<?=$idmodulo1?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento="+<?=$idinserimento?>+"&uid=<?=$_SESSION["UTENTE"]->get_userid()?>&action=<?=$action?>&usephp=7", 400, 400);
	setTimeout( function() {
		javascript:view_istanze_modulo('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','','<?=$idimpegnativa?>');
	}, 1500)	
</script>
<?
exit();
}



function duplica_modulo(){

?>
<script>
	function view_istanze_modulo(idcartella,idpaziente,idmodulo,cart,imp)
	{
		$("#layer_nero2").toggle();
		$('#cartella_clinica').innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+imp;
		$("#cartella_clinica").load(pagina_da_caricare,'', function(){
			loading_page('loading');
			$("#container-5 .tabs-selected").removeClass('tabs-selected');
		});
	}
</script>

<?
$conn = db_connect();

$datains = date('d/m/Y');
$orains = date('H:i');
$ipins = $_SERVER['REMOTE_ADDR'];
$opeins = $_SESSION['UTENTE']->get_userid();

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idmodulo1=$_REQUEST['idmodulo1'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];
$idimpegnativa=$_REQUEST['idimpegnativa'];
$firmato_digitalmente=$_REQUEST['firmato_digitalmente'];


/* duplico la testata */

$query_ist_test = "SELECT * FROM istanze_testata WHERE (id_cartella=".$idcartella.") and (id_modulo_padre=".$idmodulo1.")and (id_inserimento=".$idinserimento.")";
$result_ist_test = mssql_query($query_ist_test, $conn);
$row_ist_test = mssql_fetch_assoc($result_ist_test);
$id_istanza_testata = $row_ist_test['id_istanza_testata'];
//echo $query_ist_test . "<br><br>";

if($row_ist_test['id_impegnativa'] == "") 			$row_ist_test['id_impegnativa'] = 'NULL';
if($row_ist_test['id_impegnativa_associata'] == "") $row_ist_test['id_impegnativa_associata'] = 'NULL';
if($row_ist_test['id_paziente'] == "") 				$row_ist_test['id_paziente'] = 0;
if($row_ist_test['data_osservazione'] == "") 		$row_ist_test['data_osservazione'] = 'NULL';		else	$row_ist_test['data_osservazione'] = "'" . formatta_data($row_ist_test['data_osservazione']) . "'";

$row_ist_test['updcount'] = $row_ist_test['dataagg'] = $row_ist_test['oraagg'] = $row_ist_test['opeagg'] = $row_ist_test['ipagg'] = $row_ist_test['note'] = $row_ist_test['allegato'] = $row_ist_test['allegato2'] = 'NULL';
$row_ist_test['datains'] = $datains;
$row_ist_test['orains'] = $orains;
$row_ist_test['opeins'] = $opeins;
$row_ist_test['ipins'] = $ipins;

if($row_ist_test[0]['note'] == "")
	 $note = "(Modulo duplicato)";
else $note = $row_ist_test['note'] . " (Modulo duplicato)";


$query_max_prog="SELECT MAX(id_inserimento) as max_prog FROM istanze_testata WHERE id_modulo_versione = ".$row_ist_test['id_modulo_versione']." AND id_cartella = " . $row_ist_test['id_cartella'];
$result_max_prog= mssql_query($query_max_prog, $conn);
$row_max_prog=mssql_fetch_assoc($result_max_prog);
$max_id_inserimento = $row_max_prog['max_prog'] + 1;
//echo $query_max_prog . "<br><br>";


$query_insert="INSERT INTO istanze_testata (id_cartella,id_modulo_padre,id_modulo_versione,id_inserimento,id_impegnativa,id_paziente,data_osservazione ,updcount,datains,orains,opeins,ipins,dataagg,oraagg,opeagg,ipagg,note,allegato,allegato2,firmato_digitalmente,duplicata)
		VALUES (".$row_ist_test['id_cartella'].", ".$row_ist_test['id_modulo_padre'].", ".$row_ist_test['id_modulo_versione'].", ".$max_id_inserimento.", ".$row_ist_test['id_impegnativa'].", ".$row_ist_test['id_paziente'].", ".$row_ist_test['data_osservazione'].", ".$row_ist_test['updcount'].", '".$row_ist_test['datains']."', '".$row_ist_test['orains']."', ".$row_ist_test['opeins'].", '".$row_ist_test['ipins']."', ".$row_ist_test['dataagg'].", ".$row_ist_test['oraagg'].", ".$row_ist_test['opeagg'].", ".$row_ist_test['ipagg'].", '$note', ".$row_ist_test['allegato'].", ".$row_ist_test['allegato2'].", '$firmato_digitalmente', 1)";

//VALUES (".$row_ist_test['id_cartella'].", ".$row_ist_test['id_modulo_padre'].", ".$row_ist_test['id_modulo_versione'].", ".$max_id_inserimento.", ".$row_ist_test['id_impegnativa'].", ".$row_ist_test['id_paziente'].", ".$row_ist_test['data_osservazione'].", ".$row_ist_test['updcount'].", ".$row_ist_test['datains'].", '".$row_ist_test['orains']."', ".$row_ist_test['opeins'].", '".$row_ist_test['ipins']."', '".$row_ist_test['dataagg']."', '".$row_ist_test['oraagg']."', ".$row_ist_test['opeagg'].", '".$row_ist_test['ipagg']."', '$note', '".$row_ist_test['allegato']."', '".$row_ist_test['allegato2']."', '$firmato_digitalmente')";

$result_insert = mssql_query($query_insert, $conn);
//echo $query_insert . "<br><br>";

$query_max_id_ist_test="SELECT MAX(id_istanza_testata) as max_id FROM istanze_testata";
$result_max_id_ist_test = mssql_query($query_max_id_ist_test, $conn);
$row_max_id_ist_test = mssql_fetch_assoc($result_max_id_ist_test);
$max_id_istanza_testata = $row_max_id_ist_test['max_id'];
//echo $query_max_id_ist_test . "<br><br>";


// duplico i campi

$query_ist_dett ="SELECT * FROM istanze_dettaglio WHERE id_istanza_testata = " . $row_ist_test['id_istanza_testata'];
$result_ist_dett = mssql_query($query_ist_dett, $conn);
//echo $query_ist_dett . "<br><br>";
while ($row_ist_dett = mssql_fetch_assoc($result_ist_dett))
{
	$row_ist_dett['valore'] = str_replace("'", "''", $row_ist_dett['valore']);		// escape per MS SQL
	$query_insert="INSERT INTO istanze_dettaglio (id_istanza_testata, idcampo, valore) VALUES (".$max_id_istanza_testata.", ".$row_ist_dett['idcampo'].", '". utf8_encode($row_ist_dett['valore'])."')";
	$result_insert = mssql_query($query_insert, $conn);
	//echo $query_insert . "<br><br>";
}
/* 
mssql_free_result($result); */


//echo "javascript:view_istanze_modulo('".$idcartella."','".$idpaziente."','".$idmoduloversione."','','".$idimpegnativa."')";

//visualizza_istanze_modulo();
?>

<script>javascript:view_istanze_modulo('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','','<?=$idimpegnativa?>')</script>
<?
//echo ("ok;".$idpaziente.";5;re_pazienti_sanitaria.php?do=visualizza_istanze_modulo&idcartella="+$idcartella+"&idpaziente="+$idpaziente+"&idmodulo="+$idmodulo1+"&cartella="+$cart+"&impegnativa="+$imp);
exit();
}




function create_pianificazione()
{

$conn = db_connect();

$datains = date('d/m/Y');
$orains = date('H:i');
$ipins = $_SERVER['REMOTE_ADDR'];
$opeins = $_SESSION['UTENTE']->get_userid();

$idcartella=$_POST['idcartella'];
$idpaziente=$_POST['idpaziente'];

$query_1="SELECT idimpegnativa, idutente, datains FROM dbo.impegnative WHERE (idutente = $idpaziente) ORDER BY datains DESC";
$result_1 = mssql_query($query_1, $conn);
if ($row_1=mssql_fetch_assoc($result_1)){
	$id_impegnativa=$row_1['idimpegnativa'];
}else{
$id_impegnativa=0;
}
mssql_free_result($result_1);

$idregime=get_regime_paziente($idpaziente);
$idnormativa=get_normativa_paziente($idpaziente);		
$query="update utenti_cartelle set idregime=$idregime, idnormativa=$idnormativa where id=$idcartella";
$rs = mssql_query($query, $conn);

		$arr_ordine=Array();
		if (isset($_POST['debug']))
		{
		$ordine=$_POST['debug'];
		$arr_ordine=split(" ",$ordine);
		}
		else
		{
			while($j<100)
			$arr_ordine[$j]=$j+1;
		
		}
		$numerofield=sizeof($arr_ordine)-1;
		$k=0;		
		while($k<$numerofield)
		{
			 $idmodulo=$arr_ordine[$k];
		     $default=$_POST['default'.($k+1)];
			 $scadenza=$_POST['scadenza'.($k+1)];
			 $idmodulo_id=$_POST['idmodulo'.($k+1)];
			 $modulo=$_POST['modulo'.($k+1)];
			 
			 $query = "INSERT INTO cartelle_moduli (idcartella,idmodulo_id,obbligatorio,scadenza) VALUES($idcartella,$idmodulo_id,'$default','$scadenza')";
				
			$result1 = mssql_query($query, $conn);
				if(!$result1) 
				{
					echo("o-3002");
					exit();
					die();
				}			
				if (($default=="s")or($default=="f")){
					$med='medico'.($k+1);	
					$med_old='medico_old'.($k+1);	
					$medico=$_POST[$med];
					$medico_old=$_POST[$med_old];
						if ($medico_old!=$medico){
							$opeins=$_SESSION['UTENTE']->get_userid();
							$query = "INSERT INTO moduli_medici (id_regime,id_operatore,id_modulo,opeins,id_cartella) VALUES($idregime,$medico,$modulo,$opeins,$idcartella)";
							$result1 = mssql_query($query, $conn);
							
							$query = "SELECT MAX(id) FROM moduli_medici WHERE (opeins='$opeins')";
							$result = mssql_query($query, $conn);
							if(!$result) error_message(mssql_error());

							if(!$row = mssql_fetch_row($result))
								die("errore MAX select");
								
							$idmod_med=$row[0];	
							scrivi_log ($idmod_med,'moduli_medici','ins','id');
						}
				}
			$k++;
		}


$idpaziente=trim($idpaziente);

$query="DELETE FROM utenti_cartelle_impegnative WHERE (idcartella=$idcartella) and (idpaziente=$idpaziente)";
$rs1 = mssql_query($query, $conn);
$query = "SELECT  idimpegnativa, centrodicosto, normativa, regime, idutente, DataPrescrizione, DataPianoTrattamento, DataAutorizAsl, idregime, idnormativa, 
             DataDimissione, ProtAutorizAsl FROM dbo.re_pazienti_impegnative WHERE (idutente = $idpaziente)";	
$rs1 = mssql_query($query, $conn);
$num_rows=mssql_num_rows($rs1);
for($i=1;$i<=$num_rows;$i++){
	if(isset($_POST['impegnativa'.$i])){
		$query="INSERT INTO utenti_cartelle_impegnative (idcartella,idimpegnativa,idpaziente) VALUES($idcartella,".$_POST['impegnativa'.$i].",$idpaziente)";		
		$rs1 = mssql_query($query, $conn);
	}
}

echo ("ok;".$idpaziente.";5;re_pazienti_sanitaria.php?do=");
exit();
}



function aggiungi_pianificazione()
{

	$idutente=$_REQUEST['idpaziente'];
	$idcartella=$_REQUEST['idcartella'];
	$cartella= $_REQUEST['cartella'];

	$conn = db_connect();
	$arr_medici[]=array();
	$query="SELECT nome, uid from medici_operatori ORDER BY nome ASC";
	$rs1 = mssql_query($query, $conn);
	$i=0;
	while($row=mssql_fetch_assoc($rs1)){
		$arr_medici[$i]=$row;
		$i++;	
	}

	$idregime=0;

	$stato_paziente=get_stato_paziente($idutente);

	if ($stato_paziente>0)
	{
	   $stato_paziente_descr=get_stato_paziente_descr($stato_paziente);
	   $idregime=get_regime_paziente($idutente);
	}

	//determina lo stato dell'utente

	//determina la descrizione dello stato

	//determina il regime dell'utente

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
	<script type="text/javascript">
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup1(id){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup1").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('carica_preview.php?&do=&id='+id, function(){ 
		centerPopup();
		$("#backgroundPopup1").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});			
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup1").fadeOut("slow");
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
	
	$("#backgroundPopup1").css({
		"height": windowHeight
	});
	
}

function prev(id){	
	//centering with css
	loadPopup1(id);
	
	//load popup
}


//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!
	$(".preview").click(function(){
		//centering with css
		//centerPopup();
		//load popup
		//loadPopup1();
	});
					
	//CLOSING POPUP
	//Click the x event!
	$(".popupdatiClose").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#backgroundPopup1").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});
	// apre popup per generare date in caso di "scadenza ciclica a partire dal"
	$('[name="genera_date"]').click(function(){
		var i = 			$(this).attr('data-indice');
		var idmodulo = 		$(this).attr('data-idmodulo');
		var idimpegnativa = $(this).attr('data-idimpegnativa');
		
		var scadenza = 		$('[name="scadenza' + i +'"]').val();
		var data_scadenza = $('[name="scadenza_cicl_data' + i +'"]').val();
		var elenco_date = $('[name="scadenza_cicl_elenco_date' + i +'"]').val();	// contiene l'elenco delle date eventualmente generate in precedenza, pianificate e salvate nel DB (altrimenti � = "")

		if(scadenza !== "" && data_scadenza !== "") 
		{
			//var url = 'http://192.168.30.100/remed/get_genera_date_pianificazione.php?idmodulo='+idmodulo+'&sorgente='+i+'&tipo=data_scad_cicl&idimpegnativa='+idimpegnativa+'&scadenza='+scadenza+'&data='+data_scadenza+'&elenco-date='+elenco_date;
			var url = 'get_genera_date_pianificazione.php?idmodulo='+idmodulo+'&sorgente='+i+'&tipo=data_scad_cicl&idimpegnativa='+idimpegnativa+'&scadenza='+scadenza+'&data='+data_scadenza+'&elenco-date='+elenco_date;
			small_window(url, 500,600);
		} else alert("Inserire l'intervallo in giorni e la data di inizio scadenza ciclica.");
	});

});
</script>

	<div id="popupdati2"></div>
	<div id="backgroundPopup1"></div>
	<div id="cartella_clinica">
	<div class="titolo_pag"><h1>Modifica pianificazione della cartella clinica</h1></div>
	<div class="blocco_centralcat">
		
		<form id="myForm"  method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
				<input type="hidden" name="action" value="create_cartella" />
				<input type="hidden" name="idpaziente" value="<?=$idutente?>" />
				<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
				<div class="nomandatory">
				<input type="hidden" name="nomandatory" />
				</div>
				
				<div class="riga">
	        <span class="rigo_mask">
	            <div class="testo_mask">data di apertura</div>
	            <div class="campo_mask mandatory data_all">
	<?
	$query = "SELECT * from utenti_cartelle where id=$idcartella";	
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());

		if($row = mssql_fetch_assoc($rs))
		{
		$data_creazione=formatta_data($row['data_creazione']);
		$idmodulo_id=$row['id'];
		$idregime=$row['idregime'];
		$idnormativa=$row['idnormativa'];
		}		
	?>
	                <input type="text" class="campo_data scrittura" name="data_apertura" value="<?php echo($data_creazione);?>" readonly />
	            </div>
	        </span>
			<span class="rigo_mask">
            <div class="testo_mask">Normativa / Regime</div>
				<div class="campo_big ">
				<?			
				$query_r = "SELECT idimpegnativa, idregime, idnormativa, regime, normativa, idutente, data_inizio_trat, data_fine_trat, (DATEDIFF(DAY, data_inizio_trat, data_fine_trat) / 2) AS meta_progetto
							from re_pazienti_impegnative 
							--WHERE DataDimissione is null
							group by idimpegnativa, idregime, idnormativa, regime, normativa, idutente, data_inizio_trat, data_fine_trat, (DATEDIFF(DAY, data_inizio_trat, data_fine_trat) / 2)
							having idutente=$idutente 
							--and (idregime=$idregime) and (idnormativa=$idnormativa) 
								--AND data_inizio_trat >= getdate() AND data_fine_trat >= getdate()
							order by idregime ASC";
				//echo($query_r);
				$rs_r = mssql_query($query_r, $conn);
				if(!$rs_r) error_message(mssql_error());
				
				$cc_infermieristica_ex26 = 0;
				$cc_infermieristica_ex26_normativa = "";				
				$cc_infermieristica_leg8 = 0;
				$cc_infermieristica_leg8_normativa = "";
				$cc_infermieristica_ex26_idregime = "";				
				$cc_infermieristica_leg8_idregime = "";

				$regime_selezionato = false;
				
				?>
					<select name="regime_assistenza" class="scrittura textsmall" onchange="reload_pianificazione('<?=$idutente?>',this.value, '', this.querySelector('option:checked').getAttribute('data-id-imp') );">					
				<?  
					$conta_r=mssql_num_rows($rs_r);
					if($conta_r == 0) { ?>
						<option value="">ATTENZIONE: Impegnativa assente o non valida</option>
					<? }
					else 
					{
						$oggi = date("Y-m-d"); // data corrente in formato compatibile
						
						while ($row_r = mssql_fetch_assoc($rs_r)) {

							$idimpegnativa_r = $row_r['idimpegnativa'];
							$regime_r = $row_r['regime'];
							$normativa_r = $row_r['normativa'];
							$sel = "";
							$optgroup_style = '';

							$data_inizio_trat_ymd 	= date('Y-m-d', strtotime($row_r['data_inizio_trat']));
							$data_fine_trat_ymd 	= date('Y-m-d', strtotime($row_r['data_fine_trat']));
							// verifico se l'impegnativa che leggo rientra tra la data attuale e la evidenzio	
							if ($data_inizio_trat_ymd <= $oggi && $data_fine_trat_ymd >= $oggi) {
								$optgroup_style = 'style="background-color: #7ac46a"';
							}		
																										
							$data_inizio_trat 	= date('d/m/Y',  strtotime($row_r['data_inizio_trat']));
							$data_fine_trat 	= date('d/m/Y',  strtotime($row_r['data_fine_trat']));		
							$meta_progetto 		= $row_r['meta_progetto'] + 1;  			// sommo 1 perchè il DIFF in mssql non tiene conto dell'ultimo giorno che deve essere compreso nel calcolo
							$fine_progetto 		= !empty($meta_progetto) ? $meta_progetto * 2 : '';		
						
					
							//if ($idregime == $row_r['idregime']) {
							// se l'impegnativa che sto ciclando e' quella che ho selezionato dal menu a tendina (e non piu quella dello stesso regime)
							if(isset($_GET['idimpegnativa']) && $_GET['idimpegnativa'] == $idimpegnativa_r && !$regime_selezionato)
							{
								$sel = "selected";
								$data_inizio_trat_ymd_sel 	= date('Y/m/d',  strtotime($row_r['data_inizio_trat']));
								$data_fine_trat_ymd_sel 	= date('Y/m/d',  strtotime($row_r['data_fine_trat']));	

								$data_inizio_trat_sel		= date('d/m/Y',  strtotime($row_r['data_inizio_trat']));
								$data_fine_trat_sel			= date('d/m/Y',  strtotime($row_r['data_fine_trat']));			
								$meta_progetto_sel 			= $row_r['meta_progetto'] + 1;  			// sommo 1 perchè il DIFF in mssql non tiene conto dell'ultimo giorno che deve essere compreso nel calcolo
								$fine_progetto_sel 			= !empty($meta_progetto_sel) ? $meta_progetto_sel * 2 : '';
								$regime_selezionato = true;
							}							
						?>
							<optgroup <?=$optgroup_style?> label="↓ Dal <?=$data_inizio_trat?> al <?=$data_fine_trat?> (totale giorni: <?=($fine_progetto)?>)"?>">
							<optgroup <?=$optgroup_style?> label="Metà progetto (<?=($meta_progetto)?> giorni) ricorre il giorno <?=date('d/m/Y', strtotime($row_r['data_inizio_trat'] . " +$meta_progetto days"))?>">
								<option <?=$sel?> value="<?=$row_r['idregime']?>" data-id-imp="<?=$idimpegnativa_r?>">
									[<?=$idimpegnativa_r?>] <?=htmlentities(strtoupper($normativa_r." - ".$regime_r))?>
								</option>
							</optgroup>
							</optgroup>
							
						 <?

							if(!$regime_selezionato) {
								$data_inizio_trat_ymd_sel	= 	$data_inizio_trat_ymd;
								$data_fine_trat_ymd_sel		=	$data_fine_trat_ymd;										
								$data_inizio_trat_sel	=	$data_inizio_trat;
								$data_fine_trat_sel		=	$data_fine_trat;
								$meta_progetto_sel		=	$meta_progetto;
								$fine_progetto_sel		=	$fine_progetto;
							}						 
						/* 	if($cc_infermieristica_ex26 == 0 && (strpos(strtolower($regime_r), "residenziale") !== false || strpos(strtolower($regime_r), "semiresidenziale") !== false) ) {
								$cc_infermieristica_ex26_normativa = $normativa_r." - ".$regime_r;
								$cc_infermieristica_ex26 = 1;
								$cc_infermieristica_ex26_idregime = 52;//$row_r['idregime'];
							}

							if($cc_infermieristica_leg8 == 0 && strpos(strtolower($regime_r), "diurno") !== false) {
								$cc_infermieristica_leg8_normativa = $normativa_r." - ".$regime_r;
								$cc_infermieristica_leg8 = 1;
								$cc_infermieristica_leg8_idregime = 53; //$row_r['idregime'];
							} */
							
						}
						 
						 /* if($cc_infermieristica_ex26 == 1) {
							 if($regime_selezionato == 0) $sel_inferm="selected";	else	$sel_inferm="";
							 echo '<option '.$sel_inferm.' value="'.$cc_infermieristica_ex26_idregime.'">'.strtoupper($cc_infermieristica_ex26_normativa).' (INFERMIERISTICO)</option>';
						 }
						 
						 if($cc_infermieristica_leg8 == 1) {
							 if($regime_selezionato == 0) $sel_inferm="selected";	else	$sel_inferm="";
							 echo '<option '.$sel_inferm.' value="'.$cc_infermieristica_leg8_idregime.'">'.strtoupper($cc_infermieristica_leg8_normativa).' (INFERMIERISTICO)</option>';
						 } */
					}
					 mssql_free_result($rs_r);
					 ?>
					 </select>
				</div>
				<!-- visualizzo le date dell'impegnativa selezionata -->
				<div>
					<!-- ID impegnativa selezionata: <=!$regime_selezionato ? $idimpegnativa_r : $_GET['idimpegnativa']?><br> -->
					Inizio trattamento: <?=$data_inizio_trat_sel?><br>
					Metà trattamento: &nbsp;<?= date('d/m/Y', strtotime("$data_inizio_trat_ymd_sel +$meta_progetto_sel days"))?><br>
					Fine trattamento: &nbsp;&nbsp;<?=$data_fine_trat_sel?>
				</div>
				<!-- forzo la seleziona sull'ultima impegnativa (se non selezionata) o su quella selezionata -->
				<!-- <script>
					$('[name="regime_assistenza"] option[data-id-imp=<?=!$regime_selezionato ? $idimpegnativa_r : $_GET['idimpegnativa']?>]').attr('selected','selected');
				</script>				 -->
        </span>
	    </div>	
<div class="riga" style="display:none;">
        <span class="rigo_mask">
            <div class="testo_mask">effettua ora la pianificazione <input type="checkbox" checked onclick="javascript:$('#pianificazione').toggle();"  class="scrittura_campo" name="effettuapianificazione"/></div>
        </span>
    </div>	
<div id="pianificazione">
	<?
	$arr_imp_assoc=array();
	$query = "SELECT  id_impegnativa,id_cartella FROM re_cartelle_impegnative_associate WHERE (id_cartella = $idcartella)";		
	$rs1 = mssql_query($query, $conn);
		while($row1 = mssql_fetch_assoc($rs1))   
		{	
			array_push($arr_imp_assoc,trim($row1['id_impegnativa']));
		}	
	$arr_imp=array();
	array_push($arr_imp,"");	
	$query = "SELECT  idimpegnativa, DataAutorizAsl FROM re_impegnative_pianificazione WHERE (idutente = $idutente) AND (RegimeAssistenza=$idregime) order by ording DESC, idimpegnativa DESC";
	//echo($query);	
	$rs1 = mssql_query($query, $conn);
		while($row1 = mssql_fetch_assoc($rs1))   
		{	
			array_push($arr_imp,trim($row1['idimpegnativa']));
		}		

	$i=1;
	$z=1;
	$ord_cont=0;
	$ord_cont_t=0;
	$ordinamento="";
	$ordinamento_t="";	
	for($j=0;$j<count($arr_imp);$j++){
		$conn = db_connect();		
		$query = "SELECT idtest,nome,codice,tipo_test,descrizione,test_ramificato from re_test_clinici where disponibile_in_lista_moduli=1 and stato=1 order by nome asc";	
		$rs1 = mssql_query($query, $conn);
		if(!$rs1) error_message(mssql_error());
		$conta_t=mssql_num_rows($rs1);
		for($yy=1;$yy<=$conta_t;$yy++){
				$ord_cont_t++;
				$ordinamento_t.=$ord_cont_t." ";				
		}
	$imp_associated=0;
	if($j==0){?>		
		<div class="titolo_pag"><h1>seleziona i moduli da inserire nella cartella clinica</h1></div>
		<?
		$query = "SELECT * from re_moduli_regimi_replica where idregime=$idregime and ((replica=0) or (replica=1)) order by id asc";
		//echo $query;
		$idimpegnativa=0;
		$rs1 = mssql_query($query, $conn);
		//echo ('ciao mondo');	
		//echo 'Sto prima dell IF <br> rs1: ' . $rs1 . ' <br> Query: ' . $query . '<br>' ;	
		
		if(!$rs1) error_message(mssql_error());
		$conta=mssql_num_rows($rs1);	
			//echo ('ciao mondo- sono dentro all if: riga 3418');	
			//echo 'conta: ' . $conta . ' <br> rs1: ' . $rs1;	
			//echo $query2 = "SELECT * from re_moduli_regimi_replica where idregime=$idregime and ((replica=2) or (replica=3)) order by id asc";
			
		}
		else{
			//echo ('ciao mondo- sono dentro all else: riga 3424');	
			$query = "SELECT * from re_moduli_regimi_replica where idregime=$idregime and ((replica=2) or (replica=3)) order by id asc";
			$rs1 = mssql_query($query, $conn);
			if(!$rs1) error_message(mssql_error());
			$conta=mssql_num_rows($rs1);			
			$idimpegnativa=$arr_imp[$j];
			
			
			if(in_array(trim($idimpegnativa), $arr_imp_assoc)) $imp_associated=1;	
			?>			
			<div class="titolo_pag"><h1>seleziona le impegnative da associare alla cartella</h1></div>
			<table id="table" class="tablesorter" cellspacing="1" style="margin:0px 0px 10px;font-size:15px;font-weight:bold;">	
			<thead> 
				<tr>     
					<td>prot autorizzazione asl</td> 
					<td>data autorizzazione asl</td> 
					<td>regime</td> 
					<td>normativa</td>
					<td>inizio trattamento</td>
					<td>fine trattamento</td>
					<td>num trat</td>
					<td>associa</td> 	
				</tr> 
			</thead> 
			<tbody>
			<?
				$query = "SELECT  idimpegnativa, centrodicosto, normativa, regime, idutente, DataPrescrizione, DataPianoTrattamento, DataAutorizAsl, idregime, idnormativa, 
				 DataDimissione, ProtAutorizAsl,data_inizio_trat, data_fine_trat,NumeroTrattamenti FROM dbo.re_pazienti_impegnative WHERE idimpegnativa=$idimpegnativa";	
				$rs1 = mssql_query($query, $conn);
				if($row1=mssql_fetch_assoc($rs1));	
				$data_auth_asl=formatta_data($row1['DataAutorizAsl']);
				$prot_auth_asl=$row1['ProtAutorizAsl'];
				$regime=$row1['regime'];
				$normativa=$row1['normativa'];
				$inizio=$row1['data_inizio_trat'];
				$fine=$row1['data_fine_trat'];
				$num_trat=$row1['NumeroTrattamenti'];
				?>
			<tr>
			<td><span class="id_notizia_cat"><?=$prot_auth_asl?></span></td>
			<td><span class="id_notizia_cat"><?=$data_auth_asl?></span></td>
			<td><span class="id_notizia_cat"><?=$regime?></span></td>
			<td><span class="id_notizia_cat"><?=$normativa?></span></td>
			<td><?=formatta_data($inizio)?></td>
			<td><?=formatta_data($fine)?></td>
			<td><?=$num_trat?></td>
			<td><span class="id_notizia_cat"><input <?if($imp_associated)echo("checked");?>  type="checkbox" id="imp<?=$j?>" name="impegnativa<?=$j?>" value="<?=$idimpegnativa?>" onclick="gestImp(<?=$j?>,<?=$idregime?>,<?=$idimpegnativa?>,'<?=$imp_associated?>',<?=$ord_cont?>,<?=$ord_cont_t?>,'<?=$idcartella?>');"/>		
			</td>
			</tr>
			</tbody> 
			</table>
			<?		
			$i=$ord_cont;
			for($yy=1;$yy<=$conta;$yy++){
				$ord_cont++;
				$ordinamento.=$ord_cont." ";				
			}
			$colore="#CCCC33;";
			if($prot_auth_asl=="") $colore="rgb(102, 204, 51);";
		}		
		?>
		<div id="pianficazione<?=$j?>">				
		<div class="titolo_pag" id="label<?=$j?>" style="display:none;margin:0px 0px 0px 15px;width:98%;"><h1 style="font-size:10px;">Moduli da ssociare all'impegnativa</h1></div>
		<?
		if($imp_associated){
			include("ajax_moduli_pianificazione.php");
		}
		?>
		<?if($j==0){?>
			<table id="table<?=$j?>" class="tablesorter" cellspacing="1" <? if($j>0){?>style="display:;margin:0px 0px 15px 15px;width:98%;"<?}?>> 
			<thead> 
			<tr>     
				<th width="10%">codice sgq</th> 
				<th>ant.</th> 
				<th width="30%">nome</th> 
				<th>pianifica</th> 
				<th>obbligatorio</th> 
				<th>figura responsabile</th> 
				<th >scadenza</th>
				<th >scad. ciclica</th>
				<th >scad. trattamenti</th>
				<th>scad. puntuale</th>
			</tr> 
			</thead> <?		
				
			$scadenza="";
			$trattamenti="";
			$previsto="";
			$obbligatorio="";					
			$rs1 = mssql_query($query, $conn);
			if(!$rs1) error_message(mssql_error());
			$conta=mssql_num_rows($rs1);
			
			$moduli_in_pianificazione = array();
			$moduli_impegnative_in_pianificazione = array();
			
			$q_crt_pian = "SELECT *
							FROM cartelle_pianificazione
							WHERE id_cartella_pianificazione_testata = (
								SELECT MAX(id_pianificazione_testata) as id
								FROM cartelle_pianificazione_testata
								WHERE id_cartella = $idcartella
							)";
			$rs_crt_pian = mssql_query($q_crt_pian, $conn);
			if (mssql_num_rows($rs_crt_pian) > 0)
			{
				while($row_crt_pian = mssql_fetch_assoc($rs_crt_pian)) {
					if(empty($row_crt_pian['id_impegnativa'])) {
						$moduli_in_pianificazione[$row_crt_pian['id_modulo_padre']] = $row_crt_pian['id_modulo_versione'];
					} else {
						$moduli_impegnative_in_pianificazione[$row_crt_pian['id_impegnativa']][$row_crt_pian['id_modulo_padre']] = $row_crt_pian['id_modulo_versione'];
					}
				}
			}
			
			while($row1 = mssql_fetch_assoc($rs1))   
			{
				$cart=0;
				$idmodulo=$row1['idmodulo'];
				if($idimpegnativa==0) 
					$imp_query="id_impegnativa IS NULL";
					else
					$imp_query="id_impegnativa=$idimpegnativa";
				$query="SELECT * from istanze_testata WHERE id_modulo_padre=$idmodulo and id_cartella=$idcartella and $imp_query";
				$rs2 = mssql_query($query, $conn);				
				if (mssql_num_rows($rs2)>0){
					$compilato=1;}
					else{
					$compilato=0;
				}
				mssql_free_result($rs2);
				$query = "SELECT * from re_cartelle_pianificazione_1 where id_modulo_padre=$idmodulo and id_cartella=$idcartella and $imp_query";
				//echo($query);	
				$rs2 = mssql_query($query, $conn);				
				if (mssql_num_rows($rs2)>0){
					$cart=1;					
					$row2 = mssql_fetch_assoc($rs2);
					$idmodulo_id=$row2['id_modulo_versione'];
					$obbligatorio=trim($row2['obbligatorio']);		
					$scadenza=$row2['scadenza'];
					$trattamenti=$row2['trattamenti'];
					$data_fissa=formatta_data($row2['data_fissa']);
					$data_iniz_scad_cicl=formatta_data($row2['data_iniz_scad_cicl']);
					$id_operatore=$row2['id_operatore'];
					if($data_fissa=="01/01/1900") $data_fissa="";
						} else {
					$query = "SELECT * from max_vers_moduli where idmodulo=$idmodulo order by versione DESC, idmoduloversione DESC";						
					$rs2 = mssql_query($query, $conn);
					$row2 = mssql_fetch_assoc($rs2);
					$idmodulo_id=$row2['id_modVers'];
					}
					$query = "SELECT top 1 * from moduli where id=$idmodulo_id";								
					$rs = mssql_query($query, $conn);
					if($row = mssql_fetch_assoc($rs)){   					
						$idmodulo_id=$row['id'];
						$nome=pulisci_lettura($row['nome']);
						$codice=$row['codice'];					
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
							$class="odd";
							$dis="";
							}											
							$idcart=0;
							if ($cart==1) {					
								//$query4="SELECT uid as id_operatore, nome FROM operatori WHERE (status =1) AND (cancella = 'n') AND (uid = $id_operatore)";
								$query4="SELECT uid as id_operatore, nome FROM operatori WHERE (cancella = 'n') AND (uid = $id_operatore)";
							}else{
								$query4="SELECT TOP (1) id_modulo, id_regime, status, cancella, id, id_operatore, id_cartella,nome FROM dbo.re_moduli_medici_regime
							WHERE (status = 1) AND (cancella = 'n')AND (id_cartella = 0) AND (id_modulo = $idmodulo) AND (id_regime = $idregime) ORDER BY id DESC";								}
							
							//echo($query4);
							$rs4 = mssql_query($query4, $conn);							
							$medico="";
							$medico_nome="";
							if($row4=mssql_fetch_assoc($rs4)) {
								$medico=$row4['id_operatore'];						
								$medico_nome=$row4['nome'];	
								}
							mssql_free_result($rs4);
							
							$query3 = "SELECT * FROM regimi_moduli where idmodulo=$idmodulo and idregime=$idregime";							
							//echo $query3;
							$rs3 = mssql_query($query3, $conn);
							if(!$rs3) error_message(mssql_error());
							
							if (mssql_num_rows($rs3)>0)							
							{
								if($row3 = mssql_fetch_assoc($rs3)){   									
									$obbligatorio_regime=trim($row3['obbligatorio']);
									if($cart==0){
										$obbligatorio=trim($row3['obbligatorio']);		
										$scadenza=$row3['scadenza'];
										$trattamenti=$row3['trattamenti'];
										$data_fissa=formatta_data($row3['data_fissa']);
										if($data_fissa=="01/01/1900") $data_fissa="";
									}
								}
								
								$presente_in_pianificazione = in_array($idmodulo, array_keys($moduli_in_pianificazione));
								
								if (($obbligatorio=="s")or($obbligatorio=="o"))
								{
									$checked="checked";
									$dis="disabled";									
								}									
								else
								{
									$checked="";
									$dis="";
								}	
								?>
									<tr id="<?=$i?>">
									
									<td><span class="id_notizia_cat">
										<input type="hidden" name="selected<?=$i?>" value="<? if($j==0) echo("1"); else echo("0");?>" class="imp<?=$j?>" />
										<input type="hidden" name="mod_impegnativa<?=$i?>" value="<?=$idimpegnativa?>" />
										<input type="hidden" name="modulo<?=$i?>" value="<?=$idmodulo?>" />
										<input type="hidden" name="idmodulo<?=$i?>" value="<?=$idmodulo_id?>" />
										<?=$codice?>
										</span>
									</td>
									<td><div id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev('<?=$idmodulo_id?>');" title="anteprima modulo"><img src="images/view.png" /></a></div></td>
									<td><span class="id_notizia_cat"><?=$nome?></span></td>							
									<td>
										<div class="groups_1_one nomandatory">
										<? 
										if  ($presente_in_pianificazione && (($obbligatorio=='s') || ($obbligatorio=='o') || ($obbligatorio=='p'))){?>
												<input type="hidden" name="obbligatorio<?=$i?>" value="s" />
												<!--<input checked  type="checkbox" onclick="javascript:return false;" name="default<?=$i?>"/>-->
												<input  checked type="checkbox" onchange="javascript:a_d_medico(this,'<?=$i?>');" name="default<?=$i?>" />
										<?} else {?>	
												<input type="hidden" name="obbligatorio<?=$i?>" value="n" />	
												<input  type="checkbox" onchange="javascript:a_d_medico(this,'<?=$i?>');" name="default<?=$i?>" />
										<?}?>
										</div>
									</td>

									<td align="center">								
										<? if ($presente_in_pianificazione && (($obbligatorio=='s') || ($obbligatorio=='o') || ($obbligatorio=='p'))){?>
											<div id="scelta_vis<?=$i?>" style="display:block;">
										<?}	else {?>
											<div id="scelta_vis<?=$i?>" style="display:none;">
										<?}?>								
										<select name="scelta<?=$i?>" style="width:150px">
										<? if (($obbligatorio=='s')or($obbligatorio=='o')) { ?>
											<option value="o">obbligatorio</option>
										<?} elseif(($obbligatorio=='p')){?>
											<option value="o">obbligatorio</option>
											<option value="p" selected>opzionale</option>
										<?} else {?>									
											<option value="o">obbligatorio</option>
											<option value="p" selected>opzionale</option>
										<?}	?>
										</select>
										</div>								
									</td>								
									<td>
										<?php 
										$display="";
										if($presente_in_pianificazione && ($obbligatorio!='n')) {
											$display="block"
											?>
											<div id="medico_vis<?=$i?>" style="display:block;">
											<? }else {
											$display="none";
											
											}
											?>	
											<div id="medico_vis<?=$i?>" style="display:<?=$display?>;">
											<input type="hidden" name="medico_old<?=$i?>" value="<?=$medico?>"/>
											<input type="hidden" name="medico_id_<?=$i?>" id="medico_id_<?=$i?>" value="<?=$medico?>"/>
											<input type="text" name="medico<?=$i?>" id="medico<?=$i?>" readonly value="<?=$medico_nome?>"/><a href="javascript:void(0)" onclick="javascript:small_window('get_medici_popup.php?dest=<?=$i?>&parent=<?$medico?>&tipo=mod&idmodulo=<?=$idmodulo?>');" >seleziona</a>										
										</div>
									</td>
									<td><?
										$scadenza=trim($scadenza);
										$data_iniz_scad_cicl=trim($data_iniz_scad_cicl);
										$trattamenti=trim($trattamenti);

										// --------------------------------------------------------
										// GESTIONE PIANIFICAZIONE AUTOMATICA DATE FISSE
										// --------------------------------------------------------

										
										// 130	Cartella Clinica (ex26) - Progetto riabilitativo
										// 134	Cartella Clinica - Relazione equipe iniziale
										// 211	Cartella Clinica (ex26) - Programma riabilitativo																
										if(in_array($idmodulo, array(130,134,211))) 
										{
											$data_fissa = date('d/m/Y');
										}
										// 295	Cartella Clinica (L.8) - Diario Clinico
										// 296	Cartella Clinica (L.8) - Diario Clinico Infermiere
										// 297	Cartella Clinica (L.8) - Diario Clinico Medico Resp
										// 270	Cartella Clinica (ex26) - Valutazione iniziale
										// 307	Cartella Clinica (ex26) - Valutazione iniziale Logopedista
										// 308	Cartella Clinica (ex26) - Valutazione iniziale Neuropsicomotricista
										// 309	Cartella Clinica (ex26) - Valutazione iniziale Psicoterapeuta
										// 310	Cartella Clinica (ex26) - Valutazione iniziale Terapista Occupazionale
										// 311	Cartella Clinica (ex26) - Valutazione iniziale Fisioterapista
										elseif(in_array($idmodulo, array(295,296,297, 270,307,308,309,310,311))) 
										{
											$data_fissa = date('d/m/Y', strtotime("$data_inizio_trat_ymd_sel +15 days"));
										}

										// 283	Cartella Clinica - Relazione equipe intermedia
										// 284	Cartella Clinica (ex26) - Valutazione intermedia
										// 312	Cartella Clinica (ex26) - Valutazione intermedia Logopedista
										// 313	Cartella Clinica (ex26) - Valutazione intermedia Neuropsicomotricista
										// 314	Cartella Clinica (ex26) - Valutazione intermedia Psicoterapeuta
										// 315	Cartella Clinica (ex26) - Valutazione intermedia Terapista Occupazionale
										// 316	Cartella Clinica (ex26) - Valutazione intermedia Fisioterapista
										elseif(in_array($idmodulo, array(283, 284,312,313,314,315,316)) && !empty($meta_progetto_sel) && ($fine_progetto > 120)) {
											$data_fissa = date('d/m/Y', strtotime("$data_inizio_trat_ymd_sel +$meta_progetto_sel days"));
										}
										
										// 271	Cartella Clinica (ex26) - Valutazione finale
										// 317	Cartella Clinica (ex26) - Valutazione finale Logopedista
										// 318	Cartella Clinica (ex26) - Valutazione finale Neuropsicomotricista
										// 319	Cartella Clinica (ex26) - Valutazione finale Psicoterapeuta
										// 320	Cartella Clinica (ex26) - Valutazione finale Fisioterapista
										// 321	Cartella Clinica (ex26) - Valutazione finale Terapista Occupazionale																
										// 285  Cartella Clinica (ex26) - Consulenza Specialistica
										elseif(in_array($idmodulo, array(271,317,318,319,320,321, 285)) && !empty($fine_progetto_sel)) {
											$data_fissa = date('d/m/Y', strtotime("$data_fine_trat_ymd_sel -15 days"));			
										} 
										else
										{
											$data_fissa = trim($data_fissa);
										}										
										
										// if(in_array($idmodulo, array(133))) {
										// 	$data_fissa = date('d/m/Y', strtotime($inizio . ' + 15 days'));
										// } elseif(in_array($idmodulo, array(271, 283, 284)) && !empty($meta_progetto)) {
										// 	$data_fissa = date('d/m/Y', strtotime($inizio . " + $meta_progetto days"));
										// } else {
										// 	$data_fissa = trim($data_fissa);
										// }
										?>
										<?php if($presente_in_pianificazione && $obbligatorio!='n') $dis_scad="display:block;"; else $dis_scad="display:none;";?>
									<div id="scad_f<?=$i?>" style="<?=$dis_scad?>">
									<select id="scad_flg<?=$i?>" name="scad_flg<?=$i?>" style="width:110px; height: 19px;">
										<option value="n" <?if (($scadenza=='')and ($trattamenti=='') and ($data_fissa==''))echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>, #scad_cicl_data<?=$i?>, #trat_vis<?=$i?>, #data_vis<?=$i?>, #genera_date<?=$i?>').slideUp();">No</option>
										<option value="c" <?if ($scadenza!='') echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideDown();$('#scad_cicl_data<?=$i?>, #trat_vis<?=$i?>, #data_vis<?=$i?>, #genera_date<?=$i?>').slideUp();">Ciclica</option>
										<option value="cd" <?if (($scadenza!='') && ($data_iniz_scad_cicl!='')) echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>, #scad_cicl_data<?=$i?>, #genera_date<?=$i?>').slideDown();$('#trat_vis<?=$i?>, #data_vis<?=$i?>').slideUp();">Ciclica a partire dal</option>
										<option value="t" <?if (($trattamenti!='') and($trattamenti!=0)) echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>, #scad_cicl_data<?=$i?>, #data_vis<?=$i?>, #genera_date<?=$i?>').slideUp();$('#trat_vis<?=$i?>').slideDown();">Trattamenti</option>
										<option value="d" <?if ($data_fissa!='') echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>, #scad_cicl_data<?=$i?>, #trat_vis<?=$i?>, #genera_date<?=$i?>').slideUp();$('#data_vis<?=$i?>').slideDown();">Data fissa</option>									
									</select>
									<? if($scadenza!='' && $data_iniz_scad_cicl!='') $dis_scad="display:block;"; else $dis_scad="display:none;" ?>
										<a href="javascript:void(0)" name="genera_date" id="genera_date<?=$i?>"  style="<?=$dis_scad?>" data-indice="<?=$i?>" data-idmodulo="<?=$idmodulo?>" data-idimpegnativa="<?=$idimpegnativa?>">genera date</a>
									</div>
									</td>
									<td><?php if(($scadenza!='')and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
										<div id="scad_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory integer">
											<input type="text" name="scadenza<?=$i?>" value="<?=$scadenza?>" style="width:56px" />
										</div>
										<?php if(($scadenza!='')and($data_iniz_scad_cicl!='')and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
										<div id="scad_cicl_data<?=$i?>" style="<?=$dis_scad?>" class="nomandatory date">
											<input type="text" class="campo_data" name="scadenza_cicl_data<?=$i?>" value="<?=$data_iniz_scad_cicl?>" style="width:56px" />
										</div>
										<? if($data_iniz_scad_cicl !== "") 
											{
												//$query_date_scad_cicl="SELECT data, scadenza, compilato, dataagg FROM cartelle_pianificazione_date_scad_cicl WHERE id_paziente = $idutente AND id_cartella = $idcartella AND id_regime = $idregime AND id_normativa = $idnormativa AND id_modulo_padre = $idmodulo AND id_modulo_versione = $idmodulo_id ORDER BY data ASC";
												$query_date_scad_cicl="SELECT data, scadenza, compilato, dataagg FROM cartelle_pianificazione_date_scad_cicl 
																		WHERE 
																			id_paziente = $idutente AND 
																			id_cartella = $idcartella AND 
																			id_modulo_padre = $idmodulo AND 
																			id_modulo_versione = $idmodulo_id AND 
																			id_cartella_pianificazione_testata = (SELECT MAX(id_cartella_pianificazione_testata) FROM cartelle_pianificazione_date_scad_cicl 
																													WHERE id_paziente = $idutente AND id_cartella = $idcartella 
																													  AND id_modulo_padre = $idmodulo AND id_modulo_versione = $idmodulo_id)
																		ORDER BY data ASC";
												//echo $query_date_scad_cicl;

												$rs_date_scad_cicl = mssql_query($query_date_scad_cicl, $conn);
												$elenco_date_scad_cicl = "";
												while($row_date_scad_cicl = mssql_fetch_assoc($rs_date_scad_cicl)) {
													$elenco_date_scad_cicl .= $row_date_scad_cicl['data'] . "_" . $row_date_scad_cicl['scadenza'] . "_" . $row_date_scad_cicl['compilato']. "_" . $row_date_scad_cicl['dataagg'] . "|";
												}
												$elenco_date_scad_cicl = substr($elenco_date_scad_cicl, 0, -1);
											}
										?>
										<div id="scad_cicl_elenco_date<?=$i?>" style="display:none" class="nomandatory date">											
											<input type="text" name="scadenza_cicl_elenco_date<?=$i?>" id="scadenza_cicl_elenco_date<?=$i?>" value="<?=$elenco_date_scad_cicl?>"/>
										</div>
									</td>	
									<td><?php if((($trattamenti!='') and($trattamenti!=0))and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
										<div id="trat_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory integer">
										<input type="text" name="trattamenti<?=$i?>" value="<?=$trattamenti?>" style="width:50px"/>
										</div>
									</td>
									<td><?php if($presente_in_pianificazione && ($data_fissa!='') && ($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
										<div id="data_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory">
										<input type="text" class="campo_data" name="data_fissa<?=$i?>" value="<?=$data_fissa?>" style="width:100px" />
										</div>
									</td>
								</tr>
								<?
								}
								$i++;
						}						
				}
				for($yy=1;$yy<=$conta;$yy++){
					$ord_cont++;
					$ordinamento.=$ord_cont." ";				
				}
				$ord_cont++;
				$ordinamento.=$ord_cont." ";
				
				?>			
			</tbody>
		</table>	
		<?if($j==0){?>		
			<div class="titolo_pag" id="label_t<?=$j?>" style="display:;"><h1>Test Clinici associati alla cartella</h1></div>
			<?}else{?>
			<div class="titolo_pag" id="label_t<?=$j?>" style="display:;margin:0px 0px 0px 15px;width:98%;"><h1 style="font-size:10px;">Test Clinici associati alla cartella</h1></div>
			<?}?>
		<table id="table_t<?=$j?>" class="tablesorter" cellspacing="1" <? if($j>0){?>style="<?if(!$imp_associated){?>display:;<?}?>margin:0px 0px 15px 15px;width:98%;"<?}?>>	 
		<thead>
			<tr> 
			<th>codice sgq</th> 	 
			<th>nome test clinico</th>
			<th>tipo</th>	
			<th>descrizione</th> 			
			<td>responsabile</td>
			<td>pianifica</td>	
			</tr> 
		</thead> 
		<tbody>	<?
			$query = "SELECT idtest,nome,codice,tipo_test,descrizione,test_ramificato from re_test_clinici where disponibile_in_lista_moduli=1 and stato=1 order by nome asc";	
			$rs1 = mssql_query($query, $conn);
			if(!$rs1) error_message(mssql_error());
			$conta=mssql_num_rows($rs1);
					while($row1 = mssql_fetch_assoc($rs1))   
					{	
						$test_associated=0;
						$idtest=$row1['idtest'];
						$codice=$row1['codice'];
						$nome=pulisci_lettura($row1['nome']);
						$tipo_test=$row1['tipo_test'];
						$descrizione=pulisci_lettura($row1['descrizione']);
						$test_ramificato=$row1['test_ramificato'];
						$operatore="";
						$nome_operatore="";
						$query="SELECT * FROM re_max_cartelle_pianificazione_test where id_cartella=$idcartella and id_test=$idtest and id_impegnativa is null";
						//echo($query);
						$rs_test = mssql_query($query, $conn);
						if($row_t=mssql_fetch_assoc($rs_test)){
							$operatore=$row_t['id_operatore'];
							$selected=1;
							$query="SELECT nome, uid from operatori where uid=$operatore ";
							$rs_1 = mssql_query($query, $conn);
							if($row_o=mssql_fetch_assoc($rs_1)){
								$nome_operatore=$row_o['nome'];
							}	
						}	
							?>					
							<tr> 
							 <td><input type="hidden" name="selected_t<?=$z?>" value="0" class="imp<?=$j?>" />
								<input type="hidden" name="mod_impegnativa_t<?=$z?>" value="<?=$idimpegnativa?>" />
								<input type="hidden" name="idtest<?=$z?>" value="<?=$idtest?>" /><?=$codice?>
							 </td> 
							 <td><?=$tipo_test?></td>	
							 <td><?=$nome?></td>
							 <td><?=$descrizione?></td> 
							 <td>
								<input type="hidden" name="medico_test_id_<?=$z?>" id="medico_test_id_<?=$z?>" value="<?=$operatore?>"/>
								<input type="text" name="medico_test<?=$z?>" id="medico_test<?=$z?>" value="<?=$nome_operatore?>"/><a href="javascript:void(0)" onclick="javascript:small_window('get_medici_popup.php?dest=<?=$z?>&parent=<?$operatore?>&tipo=test');" >seleziona</a>										
							</td>
							<td>
								<div class="groups_1_one nomandatory">
									<input type="checkbox" name="default_test<?=$z?>" <?if($selected)echo("checked");?>/>
								</div>						
							</td>
						</tr> 
						<?				
					$z++;	
					}?>
				</tbody> 
			</table>
		<?}?>		
		</div>
		
	<?
	
	}?>

<!-- fine test clinici -->
<?
	$scadenza="";
	$trattamenti="";	
	$previsto="";
	$obbligatorio="";		
	$conn = db_connect();	
	$query="SELECT id_imp, id_cartella, id_modulo_padre, id_modulo_versione FROM dbo.re_cartelle_pianificazione_1 WHERE (id_cartella = $idcartella) 
		AND (NOT (id_modulo_padre IN (SELECT idmodulo FROM dbo.re_moduli_regimi WHERE (idregime = $idregime)))) ORDER BY id_modulo_padre";
	$rs1 = mssql_query($query, $conn);
	$conta=mssql_num_rows($rs1);
	if(mssql_num_rows($rs1)>0){?>
	<div class="titolo_pag"><h1>elenco moduli non pi&ugrave; presenti nel regime selezionato</h1></div>
	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
		<tr> 
			
			<th>codice sgq</th> 
			<th>prot ASL</th> 
			<th width="40%">nome</th> 
			<th>pianifica</th> 
			<th>obbligatorio</th> 
			<th>figura responsabile</th> 
			<th >scadenza</th>
			<th >scad. ciclica</th>
			<th >scad. trattamenti</th>
			<th>scad. puntuale</th>     
		</tr> 
		</thead> 	
	<?}
		//$i=1;
			while($row1 = mssql_fetch_assoc($rs1))   
			{
				$cart=0;
				$idmodulo=$row1['id_modulo_padre'];
				$idimpegnativa=$row1['id_imp'];
				if($idimpegnativa!=""){
				$query = "SELECT  idimpegnativa, centrodicosto, normativa, regime, idutente, DataPrescrizione, DataPianoTrattamento, DataAutorizAsl, idregime, idnormativa, 
				 DataDimissione, ProtAutorizAsl FROM dbo.re_pazienti_impegnative WHERE idimpegnativa=$idimpegnativa";	
				$rs_imp = mssql_query($query, $conn);
				if($row_imp=mssql_fetch_assoc($rs_imp)){	
					$data_auth_asl=formatta_data($row_imp['DataAutorizAsl']);
					$prot_auth_asl=$row_imp['ProtAutorizAsl'];
				}
				mssql_free_result($rs_imp);
				}else{
					$prot_auth_asl="";
				}
				
				$query = "SELECT * from re_cartelle_pianificazione_1 where id_modulo_padre=$idmodulo and id_cartella=$idcartella";						
				$rs2 = mssql_query($query, $conn);				
				if (mssql_num_rows($rs2)>0){
					$cart=1;
					$row2 = mssql_fetch_assoc($rs2);
					$idmodulo_id=$row2['id_modulo_versione'];
					$idimpegnativa=$row2['id_imp'];
					$obbligatorio=trim($row2['obbligatorio']);					
					$scadenza=$row2['scadenza'];
					$trattamenti=$row2['trattamenti'];
					$data_fissa=formatta_data($row2['data_fissa']);
					$id_operatore=$row2['id_operatore'];
					if($data_fissa=="01/01/1900") $data_fissa="";
					}
					else {
					$query = "SELECT * from max_vers_moduli where idmodulo=$idmodulo order by versione DESC, idmoduloversione DESC";						
					$rs2 = mssql_query($query, $conn);
					$row2 = mssql_fetch_assoc($rs2);
					$idmodulo_id=$row2['id_modVers'];
				}
					$query = "SELECT top 1 * from moduli where id=$idmodulo_id";				
					$rs = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs)){   
							$idmodulo_id=$row['id'];
							$nome=$row['nome'];
							$codice=$row['codice'];
					
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
							$class="odd";
							$dis="";
							}
							
							$previsto="n";
							$obbligatorio="n";
							$scadenza="";
							$idcart=0;
							if ($cart==1) {					
								$query4="SELECT uid as id_operatore FROM operatori WHERE (status =1) AND (cancella = 'n') AND (uid = $id_operatore)";
							}else{
								$query4="SELECT TOP (1) id_modulo, id_regime, status, cancella, id, id_operatore, id_cartella FROM dbo.moduli_medici
							WHERE (status = 1) AND (cancella = 'n')AND (id_cartella = 0) AND (id_modulo = $idmodulo) AND (id_regime = $idregime) ORDER BY id DES";								}
							$rs4 = mssql_query($query4, $conn);								
							$medico="";
							if($row4=mssql_fetch_assoc($rs4)) $medico=$row4['id_operatore'];						
							mssql_free_result($rs4);															
							?>							
								<tr id="<?=$i?>">								
								<td><span class="id_notizia_cat">
									<input type="hidden" name="selected<?=$i?>" value="1" class="imp<?=$j?>" />
									<input type="hidden" name="mod_impegnativa<?=$i?>" value="<?=$idimpegnativa?>" />
									<input type="hidden" name="modulo<?=$i?>" value="<?=$idmodulo?>" />
									<input type="hidden" name="idmodulo<?=$i?>" value="<?=$idmodulo_id?>" /><?=$codice?>
									
								</td>
								<td><span class="id_notizia_cat"><?=$prot_auth_asl?></span></td>
								<td><span class="id_notizia_cat"><?=$nome?></span></td>
							
								<td>
								<div class="groups_1_one nomandatory">
									<?									
									if  (($obbligatorio=="s")or($obbligatorio=="o")or($obbligatorio=="p")){ ?>
										<input type="hidden" name="obbligatorio<?=$i?>" value="s" />
										<input checked  type="checkbox" onclick="javascript:return false;" name="default<?=$i?>"/>
								   <?} else {?>	
										<input type="hidden" name="obbligatorio<?=$i?>" value="n" />	
										<input  type="checkbox" onchange="javascript:a_d_medico(this,'<?=$i?>');" name="default<?=$i?>" />
								  <?}?>
								  </div>
								</td>
								
								
								<td align="center">
								<? 
								//echo($obbligatorio);
								
								if (($obbligatorio=='s')or($obbligatorio=='o')or($obbligatorio=='p')){?>
								<div id="scelta_vis<?=$i?>" style="display:block;">
								<?}	else {?>
								<div id="scelta_vis<?=$i?>" style="display:none;">
								<?}?>
								
								<select name="scelta<?=$i?>" style="width:150px">
								<? if (($obbligatorio=='s')or($obbligatorio=='o')) { ?>
								<option value="o">obbligatorio</option>
								<?} elseif(($obbligatorio=='p')){?>
								<option value="o">obbligatorio</option>
								<option value="p" selected>opzionale</option>
								<?
								}
								else
								{?>
								<!--<option value="" selected>escluso</option>-->
								<option value="o">obbligatorio</option>
								<option value="p" selected>opzionale</option>
								<?
								}
								?>
								</select>
								</div>
								</td>								
								<td>
									<?php 
									$display="";
									if(($obbligatorio!='n')) {
										$display="block"
										?>
										<div id="medico_vis<?=$i?>" style="display:block;">
										<? }else {
										$display="none";
										
										}
										?>	
										<div id="medico_vis<?=$i?>" style="display:<?=$display?>;">
										<input type="hidden" name="medico_old<?=$i?>" value="<?=$medico?>"/>
										<select name="medico<?=$i?>" style="width:150px">
											
											<?php								
											for($y=0;$y<sizeof($arr_medici);$y++){									
												echo("<option value='".$arr_medici[$y]['uid']."'");
												if ($medico==$arr_medici[$y]['uid']) echo("selected");
												echo(">".$arr_medici[$y]['nome']."</option>");																																	
											}
											?>								
										</select>
									</div>
								</td>
								<td><?
									$scadenza=trim($scadenza);
									$trattamenti=trim($trattamenti);
									$data_fissa=trim($data_fissa);
									?>
									<?php if($obbligatorio!='n') $dis_scad="display:block;"; else $dis_scad="display:none;";?>
								<div id="scad_f<?=$i?>" style="<?=$dis_scad?>">
								<select id="scad_flg<?=$i?>" name="scad_flg<?=$i?>" style="width:100px;">
									<option value="n" <?if (($scadenza=='')and ($trattamenti=='') and ($data_fissa==''))echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideUp();$('#trat_vis<?=$i?>').slideUp();$('#data_vis<?=$i?>').slideUp();">No</option>
									<option value="c" <?if ($scadenza!='') echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideDown();$('#trat_vis<?=$i?>').slideUp();$('#data_vis<?=$i?>').slideUp();">Ciclica</option>
									<option value="t" <?if ($trattamenti!='') echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideUp();$('#trat_vis<?=$i?>').slideDown();$('#data_vis<?=$i?>').slideUp();">Trattamenti</option>
									<option value="d" <?if ($data_fissa!='') echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideUp();$('#trat_vis<?=$i?>').slideUp();$('#data_vis<?=$i?>').slideDown();">Data fissa</option>									
								</select>
								</div>
								</td>
								<td><?php if(($scadenza!='')and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
									<div id="scad_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory integer">
									<input type="text" name="scadenza<?=$i?>" value="<?=$scadenza?>" style="width:50px" />
									</div>
								</td>
								<td><?php if(($trattamenti!='')and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
									<div id="trat_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory integer">
									<input type="text" name="trattamenti<?=$i?>" value="<?=$trattamenti?>" style="width:50px"/>
									</div>
								</td>
								<td><?php if(($data_fissa!='')and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
									<div id="data_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory">
									<input type="text" class="campo_data" name="data_fissa<?=$i?>" value="<?=$data_fissa?>" style="width:100px" />
									</div>
								</td>
							</tr>
							<?
							if ($i==0)
								$ordinamento.=$i;
								else
								$ordinamento.=$i." ";
							$i++;
					}
			}
			?>			
		</tbody> 
</table>
	
</tbody> 
 </table>	
	</div>
	<input id="debug_t" type="hidden" name="debug_t" value="<?=$ordinamento_t?>">
	<input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">	
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>

		</form>
		    
		</div>		
				
		</div>
	<script type="text/javascript" language="javascript">
		$(document).ready(function() {			
					$.mask.addPlaceholder('~',"[+-]");	
					$(".campo_data").mask("99/99/9999");				
			
		});	
		
			function gestImp(val,idregime,idimpegnativa,imp_associated,val1,val2,idcartella){
	if($('#imp'+val+':checked').val()!=undefined){ 	
		
			
		$.ajax({
		   type: "POST",
		   url: "ajax_moduli_pianificazione.php",
		   data: "idregime="+idregime+"&idimpegnativa="+idimpegnativa+"&j="+val+"&imp_associated="+imp_associated+"&i="+val1+"&z="+val2+"&idcartella="+idcartella,
		   success: function(msg){
			 //alert( "Data Saved: " + msg );			 
			 $("#pianficazione"+val).html(trim(msg));
			 $.mask.addPlaceholder('~',"[+-]");	
			 //alert("pianificazione"+val);
			$("#pianficazione"+val+" .campo_data").mask("99/99/9999");
			}
		 });
		$('#table'+val).slideDown();
		$('.imp'+val).val('1');
		$('#label'+val).slideDown();
		$('#label_t'+val).slideDown();
		$('#table_t'+val).slideDown();
		
		} 
		else{ 
		$('#table'+val).slideUp();
		$('.imp'+val).val('0');
		$('#label'+val).slideUp();
		$('#label_t'+val).slideUp();
		$('#table_t'+val).slideUp();
		}
	}
		
		function a_d_medico(ob,i)
		{
			
		if (ob.checked==true)
		{
			$('#medico_vis'+i).slideDown(); 
			$('#scelta_vis'+i).slideDown(); 
			$('#scad_f'+i).slideDown(); 
			if($('#scad_flg'+i).val()=='c') $('#scad_vis'+i).slideDown(); 
			if($('#scad_flg'+i).val()=='t') $('#trat_vis'+i).slideDown(); 
			if($('#scad_flg'+i).val()=='d') $('#data_vis'+i).slideDown();
			$('#selected'+i).val('1');		
		}
		else
		{
			$('#medico_vis'+i).slideUp(); 
			$('#scelta_vis'+i).slideUp();
			$('#scad_f'+i).slideUp(); 
			$('#scad_vis'+i).slideUp(); 
			$('#trat_vis'+i).slideUp(); 
			$('#data_vis'+i).slideUp();
			$('#selected'+i).val('0'); 		
		}
	}
	
	</script>	
		
	<?
		
}



function aggiungi_pianificazione_test_clinici()
{

	$idutente=$_REQUEST['idpaziente'];
	$idcartella=$_REQUEST['idcartella'];
	$cartella=$_REQUEST['cartella'];

	$conn = db_connect();
	$arr_medici[]=array();
	$query="SELECT nome, uid from medici_operatori ORDER BY nome ASC";
	$rs1 = mssql_query($query, $conn);
	$i=0;
	while($row=mssql_fetch_assoc($rs1)){
		$arr_medici[$i]=$row;
		$i++;	
	}


	$idregime=0;

	$stato_paziente=get_stato_paziente($idutente);

	if ($stato_paziente>0)
	{
	   $stato_paziente_descr=get_stato_paziente_descr($stato_paziente);
	   $idregime=get_regime_paziente($idutente);
	   
	}  

	//determina lo stato dell'utente

	//determina la descrizione dello stato

	//determina il regime dell'utente

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

	<?

	$query = "SELECT * from utenti_cartelle where id=$idcartella";	
	
	
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());

		if($row = mssql_fetch_assoc($rs))
		{
		$data_creazione=formatta_data($row['data_creazione']);
		$idmodulo_id=$row['id'];
		}
		
	?>

	<div id="cartella_clinica">
	<div class="titolo_pag"><h1>Pianificazione per la cartella clinica</h1></div>
	<div class="blocco_centralcat">
		
		<form id="myForm"  method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
				<input type="hidden" name="action" value="add_pianificazione" />
				<input type="hidden" name="idpaziente" value="<?=$idutente?>" />
				<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
				<div class="nomandatory">
				<input type="hidden" name="nomandatory" />
				</div>
				
		<div class="riga">
	        <span class="rigo_mask">
	            <div class="testo_mask">data di apertura</div>
	            <div class="campo_mask mandatory data_all">
	                <input type="text" class="campo_data scrittura" name="data_apertura" value="<?php echo($data_creazione);?>"/>
	            </div>
	        </span>
	    </div>	
		
		

				
<div id="pianificazione">		
	<div class="titolo_pag"><h1>seleziona i moduli</h1></div>
	<table id="table" class="tablesorter" cellspacing="1"> 
	<!--<table style="clear:both;" id="table" class="tablesorter" cellspacing="2" cellpadding="2" border="0">-->
		
		
		<thead> 
<tr> 
    
    <th>codice sgq</th> 
	<th>nome</th> 
    <th>obbligatorio</th> 
	<th>figura responsabile</th> 
	<th>scad. ciclica</th>
	<th>scad. puntuale</th>     
</tr> 
</thead> 
		
	
	<?
	$ordinamento="";
	$conn = db_connect();
	$query = "SELECT * from re_moduli_regimi WHERE (idregime=$idregime) ORDER BY id ASC";	
	$rs1 = mssql_query($query, $conn);
	$conta=mssql_num_rows($rs1);
		$i=1;
			while($row1 = mssql_fetch_assoc($rs1))   
			{
				$cart=0;
				$idmodulo=$row1['idmodulo'];
				$query = "SELECT * from cartelle_moduli where idmodulo=$idmodulo and idcartella=$idcartella";						
				$rs2 = mssql_query($query, $conn);				
				if (mssql_num_rows($rs2)>0){
					$cart=1;
					$row2 = mssql_fetch_assoc($rs2);
					$idmodulo_id=$row2['idmodulo_id'];
						} else {
					$query = "SELECT * from max_vers_moduli where idmodulo=$idmodulo order by versione DESC, idmoduloversione DESC";						
					$rs2 = mssql_query($query, $conn);
					$row2 = mssql_fetch_assoc($rs2);
					$idmodulo_id=$row2['id_modVers'];
				}
					$query = "SELECT top 1 * from moduli where id=$idmodulo_id";
					$rs = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs)){   
					$idmodulo_id=$row['id'];
					$nome=$row['nome'];
					$codice=$row['codice'];
					
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
							$class="odd";
							$dis="";
							}
							
							$previsto="n";
							$obbligatorio="n";
							$scadenza="";
							$idcart=0;
							if ($cart==1) $idcart=$idcartella;							
							$query4="SELECT TOP (1) id_modulo, id_regime, status, cancella, id, id_operatore, id_cartella FROM dbo.moduli_medici
							WHERE (status = 1) AND (cancella = 'n')AND (id_cartella = $idcart) AND (id_modulo = $idmodulo) AND (id_regime = $idregime) ORDER BY id DESC";							
							$rs4 = mssql_query($query4, $conn);
							if (mssql_num_rows($rs4)==0){
								$query4="SELECT TOP (1) id_modulo, id_regime, status, cancella, id, id_operatore, id_cartella FROM dbo.moduli_medici
							WHERE (status = 1) AND (cancella = 'n')AND (id_cartella = 0) AND (id_modulo = $idmodulo) AND (id_regime = $idregime) ORDER BY id DESC";							
							$rs4 = mssql_query($query4, $conn);
							}		
							$medico="";
							if($row4=mssql_fetch_assoc($rs4)) $medico=$row4['id_operatore'];						
							mssql_free_result($rs4);
							
							$query3 = "SELECT * FROM regimi_moduli where idmodulo=$idmodulo and idregime=$idregime";							
							$rs3 = mssql_query($query3, $conn);

							if(!$rs3) error_message(mssql_error());
							
							if (mssql_num_rows($rs3)>0)							
							{
								if($row3 = mssql_fetch_assoc($rs3))
								{   
									$previsto=trim($row3['previsto']);
									$obbligatorio=trim($row3['obbligatorio']);
									$scadenza=$row3['scadenza'];
									$data_fissa=formatta_data($row3['data_fissa']);
									if($data_fissa=="01/01/1900") $data_fissa="";
								}
								if($cart==1){
								$obbligatorio="s";
								$previsto="s";
								$scadenza="";
								}
								if ($obbligatorio=="s")
								{
									$checked="checked";
									$dis="disabled";
									$dis="";
								}
									
								else
								{
									$checked="";
									$dis="";
								}
							
															
							
							?>
							
							
							
								<tr id="<?=$i?>">
								
								<td><span class="id_notizia_cat"><input type="hidden" name="modulo<?=$i?>" value="<?=$idmodulo?>" /><input type="hidden" name="idmodulo<?=$i?>" value="<?=$idmodulo_id?>" /><?=$codice?></span></td>
								<td><span class="id_notizia_cat"><?=$nome?></span></td>
							
								<td>
								<div class="groups_1_one nomandatory">
									<? if ($obbligatorio=="s"){
										?>
											<input type="hidden" name="obbligatorio<?=$i?>" value="s" />
											<input checked  type="checkbox" onclick="javascript:return false;" name="default<?=$i?>"/>
								   <?} else {?>	
								   <input type="hidden" name="obbligatorio<?=$i?>" value="n" />	
								<input  type="checkbox" onchange="javascript:a_d_medico(this,'<?=$i?>');" name="default<?=$i?>" />
								  <?}?>
								  </div>
								</td>
								<td>
									<?php 
									$display="";
									if(($obbligatorio=='s')or($obbligatorio=='')) {
										$display="block"
										?>
										<div id="medico_vis<?=$i?>" style="display:block;">
										<? }else {
										$display="none";
										
										}
										?>	
										<div id="medico_vis<?=$i?>" style="display:<?=$display?>;">
										<input type="hidden" name="medico_old<?=$i?>" value="<?=$medico?>"/>
										<select name="medico<?=$i?>" style="width:150px">
											
											<?php								
											for($y=0;$y<sizeof($arr_medici);$y++){									
												echo("<option value='".$arr_medici[$y]['uid']."'");
												if ($medico==$arr_medici[$y]['uid']) echo("selected");
												echo(">".$arr_medici[$y]['nome']."</option>");																																	
											}
											?>								
										</select>
									</div>
								</td>
								<td>
									<input type="text" name="scadenza<?=$i?>" value="<?=$scadenza?>" />
								</td>
								<td>
									<input type="text" class="campo_data" name="data_fissa<?=$i?>" value="<?=$data_fissa?>" />
								</td>
							</tr>
							<?
							if ($i==0)
								$ordinamento.=$i;
								else
								$ordinamento.=$i." ";
							$i++;
							
							}
					
					}
			}
			?>
			
		</tbody> 
</table> 
	</div>		

		

			<input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">	
		    <div class="titolo_pag">
				<div class="comandi">
					<input type="submit" title="salva" value="salva" class="button_salva"/>
				</div>
			</div>

		</form>
		    
		</div>		
				
		</div>
	<script type="text/javascript" language="javascript">
		$(document).ready(function() {			
					$.mask.addPlaceholder('~',"[+-]");	
					$(".campo_data").mask("99/99/9999");				
			
		});	
		
		function a_d_medico(ob,i)
	{
			
		if (ob.checked==true)
		$('#medico_vis'+i).slideDown(); 
		else
		$('#medico_vis'+i).slideUp(); 
	}
	
	</script>	
		
	<?
		
}


function create_chiusura_cartella()
{

$conn = db_connect();
$idpaziente=$_POST['idpaziente'];
$idimpegnativa=0;
$query = "select top 1 idimpegnativa from impegnative where idutente=$idpaziente order by DataAutorizAsl desc";
$result1 = mssql_query($query, $conn);
if($row1=mssql_fetch_assoc($result1)) 
$idimpegnativa=$row1['idimpegnativa'];


$dataagg = date('d/m/Y');
$oraagg = date('H:i');
$ipagg = $_SERVER['REMOTE_ADDR'];
$opeins = $_SESSION['UTENTE']->get_userid();

$idcartella=$_POST['idcartella'];

$data_chiusura=$_POST['data_chiusura'];
$motivazione=pulisci($_POST['motivazione']);
$codice_archiviazione=pulisci($_POST['codice_archiviazione']);

$query = "update utenti_cartelle set id_ultima_impegnativa=$idimpegnativa,data_chiusura='$data_chiusura', codice_archiviazione='$codice_archiviazione', motivazione='$motivazione', idmedico_chiusura=$opeins, dataagg='$dataagg', oraagg='$oraagg', opeagg='$opeins', ipagg='$ipagg'  where id=$idcartella";
$result1 = mssql_query($query, $conn);
if(!$result1){
	echo("no");
	exit();
	die();
}
			
$idpaziente=trim($idpaziente);
echo ("ok;".$idpaziente.";5;re_pazienti_sanitaria.php?do=");
exit();
}

function chiudi_cartella()
{
	$idutente=$_REQUEST['idpaziente'];
	$idcartella=$_REQUEST['idcartella'];

	$conn = db_connect();
	
	$sql_problem_allegato1 = "SELECT nome_modulo, [progressivo istanza] as progressivo_istanza
							FROM re_visualizza_info_duplicati_allegato1
							WHERE id_cartella = $idcartella";
	$r_problem_allegato1 = mssql_query($sql_problem_allegato1, $conn);

	if($r_problem_allegato1 !== false) {
		$problem_allegato1 = mssql_fetch_assoc($r_problem_allegato1);
		if(!empty($problem_allegato1['nome_modulo'])) {
			die("La cartella riscontra anomalie nell'allegato 1 del modulo: <b>" . $problem_allegato1['nome_modulo'] . ' | Progressivo: ' . $problem_allegato1['progressivo_istanza']) .'</b>';
		}
	}
	
	$sql_problem_allegato2 = "SELECT [nome modulo] as nome_modulo, [progressivo istanza] as progressivo_istanza
							FROM re_visualizza_info_duplicati_allegato2
							WHERE id_cartella = $idcartella";
	$r_problem_allegato2 = mssql_query($sql_problem_allegato2, $conn);

	if($r_problem_allegato2 !== false) {
		$problem_allegato2 = mssql_fetch_assoc($r_problem_allegato2);
		if(!empty($problem_allegato2['nome_modulo'])) {
			die("La cartella riscontra anomalie nell'allegato 2 del modulo: <b>" . $problem_allegato2['nome_modulo'] . ' | Progressivo: ' . $problem_allegato2['progressivo_istanza']) .'</b>';
		}
	}
	
$data_odierna = date('d/m/Y');
	?>
<script>inizializza();</script>
	<div id="cartella_clinica">
	<div class="titolo_pag"><h1>Chiusura cartella clinica</h1></div>
	<div class="blocco_centralcat">
		
		<form id="myForm"  method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
				<input type="hidden" name="action" value="create_chiusura_cartella" />
				<input type="hidden" name="idpaziente" value="<?=$idutente?>" />
				<input type="hidden" name="idcartella" id="idcartella" value="<?=$idcartella?>" />
				<div class="nomandatory">
				<input type="hidden" name="nomandatory" />
				</div>
				
		<div class="riga">
	        <span class="rigo_mask">
	            <div class="testo_mask">data di chiusura</div>
	            <div class="campo_mask mandatory data_chiusura">
	                <input type="text" class="campo_data scrittura" name="data_chiusura" value="<?php echo($data_odierna);?>"/>
	            </div>
	        </span>
	    </div>	
		
		<div class="riga">
	        <span class="rigo_mask rigo_big">
	            <div class="testo_mask">motivazione</div>
	            <div class="campo_mask">
	                <textarea class="scrittura_campo" name="motivazione"></textarea>
	            </div>
	        </span>
	    </div>

		<div class="riga">
	        <span class="rigo_mask rigo_big">
	            <div class="testo_mask">codice archiviazione</div>
	            <div class="campo_mask">	                
					<input type="text" class="scrittura h_small" name="codice_archiviazione" />
	            </div>
	        </span>
	    </div>	
		
	
			<input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">	
		    <div class="titolo_pag">
				<div class="comandi">
					<input type="submit" title="salva" value="salva" class="button_salva"/>
				</div>
			</div>

		</form>
		    
		</div>		
				
		</div>
	<script type="text/javascript" language="javascript">
		$(document).ready(function() {			
					$.mask.addPlaceholder('~',"[+-]");	
					$(".campo_data").mask("99/99/9999");				
			
		});	
	</script>	
		
	<?
		
}


function visualizza_cartella(){

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idversione=1;
$chiusa=false;
$conn = db_connect();
$query="select id, idregime, codice_cartella, versione, data_chiusura from utenti_cartelle where id=$idcartella";
$rs1 = mssql_query($query, $conn);
if($row=mssql_fetch_assoc($rs1)){ 
	$id_regime=$row['idregime'];
	$cod_cartella=$row['codice_cartella'];
	$versione=$row['versione'];
	if($row['data_chiusura']!="") $chiusa=true;
}
mssql_free_result($rs1);
$arr_idimpegnativa=array();

?>

<script>
function stampa_cartella(idcartella, idpaziente) {
	if(confirm('In base al numero di allegati, la completa elaborazione pu\u00F2 richiedere da pochi secondi fino a qualche minuto.. vi preghiamo di attendere.')) {
		var uid = '<?=$_SESSION["UTENTE"]->get_userid()?>';

		if(uid == '11') {
			window.open("http://192.168.30.100/rtf2pdf/stampa_cartella_test.php?idcartella="+idcartella+"&idpaziente="+idpaziente+"&uid="+uid+"&no_firma=1&usephp=7", "_blank");
		} else {			
			window.open("http://192.168.30.100/rtf2pdf/stampa_cartella.php?idcartella="+idcartella+"&idpaziente="+idpaziente+"&uid="+uid+"&no_firma=1&usephp=7", "_blank");
		}
	}
}
</script>

<div id="cartella_clinica">
<div class="titoloalternativo">
            <h1>cartella clinica n. <?=$cod_cartella?>/<?=convert_versione($versione); $cart=$cod_cartella."/".convert_versione($versione);?></h1>	         
			 <!--<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
			<!--<h2>creata il 17/12/2008 da brain</h2>
			<h2>creata il 17/12/2008 da brain</h2>-->
</div>
<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_cartelle('<?=$idpaziente?>','cartella_clinica')" >ritorna all'elenco cartelle</a></div>			
<?php 
	/* <div class="stampa"><a target="_blank" onclick="javascript:confirm('In base al numero di allegati, la completa elaborazione pu&ograve; richiedere da pochi secondi fino a qualche minuto.. vi preghiamo di attendere.');" href="http://192.168.30.100/rtf2pdf/stampa_cartella.php?idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&usephp=7">stampa allegati intera cartella clinica</a></div> */
 ?>
		<div class="stampa"><a onclick="stampa_cartella(<?=$idcartella?>,<?=$idpaziente?>)" href="#">Stampa allegati intera cartella clinica</a></div>
	</div>
</div>
<?
	$query="SELECT * FROM re_cartelle_pianificazione WHERE id_cartella=$idcartella and idregime=$id_regime order by ording DESC,order_replica ASC, id_impegnativa DESC,id ASC";
	//echo($query);
	$rs = mssql_query($query, $conn);
	$old_impegnativa="0";
	$open=0;
	while($row=mssql_fetch_assoc($rs)){
		$id_impegnativa=$row['id_impegnativa'];		
		$idmodulo=$row['id_modulo_padre'];		
		$idmodulo_id=$row['idmodulo_id'];
		$replica=$row['replica'];
		$scadenza=$row['scadenza'];
		$data_fissa=formatta_data($row['data_fissa']);
		$trattamenti=$row['trattamenti'];
		if($trattamenti==0)$trattamenti="";
		$data_osservazione=$row['data_osservazione'];		
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root())))
		{
			if(strval(formatta_data($row['ultima_compilazione'])) !== '01/01/1900') {
				 $ultima_compilazione = formatta_data($row['ultima_compilazione']);
			} else {				
				$query_data_creazione = "select TOP(1) datains FROM istanze_testata WHERE id_cartella = $idcartella and id_modulo_versione = $idmodulo_id ORDER BY id_istanza_testata DESC";
				$rs_data_creazione = mssql_query($query_data_creazione, $conn);
				$row_data_creazione = mssql_fetch_assoc($rs_data_creazione);
				$ultima_compilazione = date('d/m/Y' , strtotime($row_data_creazione['datains']));
			}
		}else{
			if($data_osservazione !== '01/01/1900') 
				 $ultima_compilazione=formatta_data($data_osservazione);
			else $ultima_compilazione=formatta_data($row['ultima_compilazione']);
		}
		
		$obbligatorio=$row['obbligatorio'];		
		$data_ins=$row['datains'];
		$nome_medico=$row['nome_medico'];
	
	$query_m="Select nome, id, codice from moduli where id=$idmodulo_id";
	$rs_m = mssql_query($query_m, $conn);
	if($row_m=mssql_fetch_assoc($rs_m)) {
		$nome=$row_m['nome'];
		$codice=$row_m['codice'];
	}
	mssql_free_result($rs_m);
	
	if($old_impegnativa!=$id_impegnativa){
		if($open) print("</tbody></table>");
	?>		
	<div class="titolo_pag">
			<div class="comandi">
				<?				
				if($id_impegnativa!=NULL){
					$query_imp="SELECT  idutente, idregime, DataAutorizAsl, ProtAutorizAsl, regime, data_inizio_trat, data_fine_trat FROM dbo.re_pazienti_impegnative WHERE (idimpegnativa=".$id_impegnativa.")";					
					$rs_imp = mssql_query($query_imp, $conn);
					if($row_imp=mssql_fetch_assoc($rs_imp)){
						$data_auth_asl=formatta_data($row_imp['DataAutorizAsl']);
						$prot_auth_asl=$row_imp['ProtAutorizAsl'];
						$regime=$row_imp['regime'];
						if($row_imp['data_inizio_trat']!="")
							$data_in=" - inizio trattamento: <strong>".formatta_data($row_imp['data_inizio_trat'])."</strong>";
							else
							$data_in="";
						if($row_imp['data_fine_trat']!="")
							$data_fine=" - fine trattamento: <strong>".formatta_data($row_imp['data_fine_trat'])."</strong>";
							else
							$data_fine="";
					}
				echo("Impegnativa prot. <strong>".$prot_auth_asl."</strong> del <strong>".$data_auth_asl."</strong> - regime: <strong>".$regime."</strong>".$data_in.$data_fine);	
			
				} /*else {
					$query = "SELECT MAX(idimpegnativa) WHERE id_utente = $idpaziente AND RegimeAssistenza = $id_regime";
					//echo($query);	
					$rs1 = mssql_query($query, $conn);
					$row1 = mssql_fetch_assoc($rs1))   
					$id_impegnativa = $row1['idimpegnativa'];
				}*/?>
				<div class="aggiungi aggiungi_left"></div>
			</div>
	</div>
				 
	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
			<tr> 
				<th style="width:15%">codice sgq</th> 
				<th>modulo</th> 
				<th style="width:5%">tipo</th> 
				<th>figura responsabile</th> 
				<th>scad ciclica</th> 
				<th>scad fissa</th> 
				<th>scad tratt</th> 
				<th>ultima compilazione</th> 
				<th>esplora</th>
				<th>anteprima</th> 
				<th>aggiungi</th>			
			</tr> 
	</thead> 
	<tbody> 			
	<?
	$old_impegnativa=$id_impegnativa;
	$open=1;
	}	
		$img="";												
		if ($obbligatorio=='o')	{
			$img="spunto.png";
			$tipo="O/";
			}else{
			$tipo="F/";
			}
		if(($replica==1)or($replica==3)){
			$tipo.="M";
			}else{
			$tipo.="U";
		}							
			?>
			<tr> 
			 <td><?=$codice?></td>
			 <td><?=pulisci_lettura($nome)?></td>
			 <td><?=$tipo?></td>
			 <td><?=$nome_medico?></td>
			  
			 <td><?=$scadenza?></td> 
			 <td><?=$data_fissa?></td> 
			 <td><?=$trattamenti?></td> 
			 <td><?=$ultima_compilazione?></td> 
			 <? 						 
			 if ($ultima_compilazione!='') {
				?>
			  <td><a href="#"  onclick="javascript:view_istanze_modulo('<?=$idcartella?>','<?=$idpaziente?>','<?=$id_regime?>','<?=$idmodulo_id?>','<?=$cart?>','<?=$id_impegnativa?>');"><img src="images/view.png" /></a></td> 
			  <td><a href="#"  onclick="javascript:ante_istanze_modulo('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmodulo_id?>','<?=$cart?>','<?=$id_impegnativa?>');"><img src="images/book_open.png" /></a></td> 
			  <?
			  }
			  else
				{?>
			  <td></a></td>
			  <td></a></td>
			  <?
			  }						 
			  ?>
			  <td>
			  <?
				$u_c=$ultima_compilazione;
				//echo($replica."-".$ultima_compilazione."-".$idimp);
			  if  ( get_permesso_modulo($idmodulo,$_SESSION['UTENTE']->get_gid())
					&&  (($replica==1) || (($replica==0) && (trim($u_c)=='')) || (($replica==2) && ($u_c=="")) || ($replica==3))
					&& (!$chiusa)) 
			 { ?>
				<a href="#"  onclick="javascript:add_modulo('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmodulo_id?>','<?=$id_impegnativa?>');" ><img src="images/add.png" /></a>
				<? }else {
				echo("&nbsp;");
				}?>
				</td> 
			</tr>
				<?
			}
		mssql_free_result($rs);
	?>	
</tbody></table >
	</div>

<?
$query="SELECT  * FROM re_log_pianificazione_cartelle WHERE (id_cartella = $idcartella)order by datains desc";
//echo($query);
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0){
?>
<div class="blocco_centralcat">
	<div class="titolo_pag"><h1>cronologia modifiche pianificazione:</h1></div>
	<?
	$i=mssql_num_rows($rs);
	while($row=mssql_fetch_assoc($rs)){
		?><div class="campo_mask "><?
		echo("$i - <strong>data</strong>: ".formatta_data($row['datains'])." ".substr($row['orains'],10,10)." - <strong>operatore</strong>: ".$row['nome']);	
		$i--;
		?></div><?
	}
?></div>
<?}?>

<script type="text/javascript" id="js">

	function add_modulo(idcartella,idpaziente,idmodulo,imp,cart)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&impegnativa="+imp+"&cartella="+cart;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function view_istanze_modulo(idcartella,idpaziente,idregime,idmodulo,cart,imp)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idregime="+idregime+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+imp;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function ante_istanze_modulo(idcartella,idpaziente,idmodulo,cart,imp)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=anteprima_istanze_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&cartella="+cart.replace(" ", "%20")+"&impegnativa="+imp;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function ritorna_cartelle(idpaziente,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria.php?do=&id="+idpaziente;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	 
	}

	 </script>

<?

exit();

}





function aggiungi_modulo(){

$idcartella=$_REQUEST['idcartella'];
$cartella=$_REQUEST['cartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idimpegnativa=$_REQUEST['impegnativa'];

$conn = db_connect();
$opeins=$_SESSION['UTENTE']->get_userid();
$query="Select * from operatori WHERE uid=$opeins";
$rs = mssql_query($query, $conn);	
if($row_m=mssql_fetch_assoc($rs)) {
	$nome_medico=$row_m['nome'];
	//if(trim($row_m['titolo'])!="") $titolo_medico=$row_m['titolo']." - ";	
}
mssql_free_result($rs);

$query="Select * from utenti_cartelle WHERE id=$idcartella";
$rs = mssql_query($query, $conn);	
if($row_m=mssql_fetch_assoc($rs)) {
	$idnormativa=$row_m['idnormativa'];
	$idregime=$row_m['idregime'];
}

$query="Select * from moduli WHERE id=$idmoduloversione";
$rs = mssql_query($query, $conn);	
if($row_m=mssql_fetch_assoc($rs)) {
	$idmodulo=$row_m['idmodulo'];
	$nome_modulo=$row_m['nome'];
}
mssql_free_result($rs);

if($_SESSION['UTENTE']->_properties['uid'] == 11) {
?>
<script>

	$(document).ready(function() {
		$(".aprichiudicompilatore").click(function() {
			
<?php
	$_query = "SELECT CONCAT(Nome, ' ', Cognome) as utente
				FROM UTENTI
				WHERE IdUtente = $idpaziente";
	$_rs = mssql_query($_query, $conn);
	$_row = mssql_fetch_assoc($_rs);
	mysqli_free_result($_rs);
?>
			if ($("#barra_ai").is(":hidden")) {
				$(".pannelli_top").hide();
				$("#barra_ai").slideDown(300);
				
				$("#ai_nome_modulo").text("<?php echo $nome_modulo;?>"); 
				$("#ai_paziente").text("<?php echo $_row['utente'];?>"); 				
				
				$("#ai_id_paziente").val("<?php echo $idpaziente;?>"); 
				$("#ai_id_cartella").val("<?php echo $idcartella;?>"); 
				$("#ai_id_modulo_versione").val("<?php echo $idmoduloversione;?>"); 
				$("#ai_id_modulo_padre").val("<?php echo $idmodulo?>"); 				
			} else {
				$("#barra_ai").slideUp(300);	
			}
		});
	});

</script>
<?php
}

$query="SELECT dbo.max_vers_moduli.idmoduloversione, dbo.max_vers_moduli.versione, dbo.max_vers_moduli.idmodulo, 
        dbo.max_vers_moduli.idmoduloversione_new, dbo.max_vers_moduli.id_modVers, dbo.moduli.replica
		FROM dbo.max_vers_moduli LEFT OUTER JOIN dbo.moduli ON dbo.max_vers_moduli.idmoduloversione_new = dbo.moduli.id 
		where id_cartella=$idcartella and idmoduloversione=$idmoduloversione ORDER BY id_modVers desc";
//echo($query);
$rs = mssql_query($query, $conn);	
if(!$rs) error_message(mssql_error());

if($row = mssql_fetch_assoc($rs)){   
	$idmoduloversione_new=$row['idmoduloversione_new'];
	$replica=$row['replica'];
}else{
	$query="SELECT replica FROM moduli WHERE id=$idmoduloversione";
	$rs = mssql_query($query, $conn);
	if($row = mssql_fetch_assoc($rs)) $replica=$row['replica'];
	$idmoduloversione_new=$idmoduloversione;	
}

$query="select * from campi where ((idmoduloversione=$idmoduloversione_new) and (status=1)) order by peso asc";	
$rs1 = mssql_query($query, $conn);
if(!$rs1) error_message(mssql_error());

?>
<script>inizializza();</script>
<div id="sanitaria">
<!-- qui va il codice dinamico dei campi -->
	<div class="titolo_pag"><h1>creazione modulo: <?=$nome_modulo?></h1></div>	
	
<form id="myForm" method="post" name="form0" enctype="multipart/form-data" action="re_pazienti_sanitaria_POST.php">
	
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>

	<input type="hidden" name="action" value="create_modulo" />
	<input type="hidden" name="idnormativa" value="<?=$idnormativa?>" />
	<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
	<input type="hidden" name="cartella" value="<?=$cartella?>" />
	<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />	
	<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione_new?>" />
	<input type="hidden" name="impegnativa" value="<?if(($replica==2)or($replica==3)) echo("si"); else echo("no");?>" />
	<input type="hidden" name="firmadigitaledocumento" id="firmadigitaledocumento" value="n" />
	<input type="hidden" name="salvaeallega" id="salvaeallega" value="n" />
	<input type="hidden" name="fea_documento" id="fea_documento" value="n" />

	
	<div class="blocco_centralcat">
		<?	
		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		
		if(($replica==2)or($replica==3)){?>		
		<div class="rigo_mask">
            <div class="testo_mask">associa alla pratica</div>
            <div class="campo_mask">
                <select name="idimpegnativa" class="scrittura" style="width:auto;">
				<?				
				if($idimpegnativa!=NULL){
					$query_imp="SELECT  idutente, idregime, DataAutorizAsl, ProtAutorizAsl, regime, data_inizio_trat, data_fine_trat FROM dbo.re_pazienti_impegnative WHERE (idimpegnativa=".$idimpegnativa.")";					
					$rs_imp = mssql_query($query_imp, $conn);
					if($row_imp=mssql_fetch_assoc($rs_imp)){
						$data_auth_asl=formatta_data($row_imp['DataAutorizAsl']);
						$prot_auth_asl=$row_imp['ProtAutorizAsl'];
						$regime=$row_imp['regime'];
						if($row_imp['data_inizio_trat']!="")
							$data_in=" - inizio trattamento: <strong>".formatta_data($row_imp['data_inizio_trat'])."</strong>";
							else
							$data_in="";
						if($row_imp['data_fine_trat']!="")
							$data_fine=" - fine trattamento: <strong>".formatta_data($row_imp['data_fine_trat'])."</strong>";
							else
							$data_fine="";
					}
				$echo_imp="Impegnativa prot. <strong>".$prot_auth_asl."</strong> del <strong>".$data_auth_asl."</strong> - regime: <strong>".$regime."</strong>".$data_in.$data_fine;	
				?>
				<option value="<?=$idimpegnativa?>"><?=$echo_imp?></option>
				<?
				}
				//mssql_free_result($rs11);
				?>
				</select>
            </div>
        </div>		
		<?}
		//echo($query_1);	
		while($row1 = mssql_fetch_assoc($rs1)){			
			$idcampo= $row1['idcampo'];
			$etichetta_campo = $row1['etichetta'];
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
			<div class="etichetta_campi"><?=pulisci_lettura($etichetta_campo)?></div>
			<input type="hidden" class="scrittura" name="<?=$idcampo?>" >	
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5))  echo("rigo_big");?>"<?php if(($multi==1)or($multi==2)) echo($stile);?>>
            <div class="testo_mask"><?=pulisci_lettura($etichetta_campo)?></div>            
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
					<input type="text" pattern="<?php echo ITALIAN_DATE_REGEX; ?>" title="gg/mm/aaaa" class="scrittura campo_data" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==7)
					 {?>
					<input type="file" class="scrittura_campo" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==8)
					 {?>
					<!--<input type="text" class="scrittura_campo" name="<=$idcampo?>" value="<=$titolo_medico.$nome_medico?>">-->
					<input type="text" class="scrittura_campo" name="<?=$idcampo?>" value="<?=$nome_medico?>">
				  <?}
				   elseif($tipo==4) 
				   {
						$query2="select * from re_moduli_combo where idcampo=$idcampo and stato=1 order by peso";	
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2) error_message(mssql_error());
						
						if(($multi==1)or($multi==2)) {
							$xy=1;	
							while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore=$row2['valore'];
								$etichetta=$row2['etichetta'];
								
								// le combo con queste etichette devono avere sempre flaggato l'operatore che compila
								if($etichetta_campo == "Elenco operatori" || $etichetta_campo == "Elenco firmatari") {
									if($valore == $opeins)
										 $checked = ' checked onclick="alert(\'Non puoi rimuovere te stesso.\'); return false;"';
									else $checked = '';
								}
							?>					
						<div style="float:left; padding:0 10px 0 0;clear:both;"><input type="checkbox" <?=$checked?> name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=pulisci_lettura($etichetta)?></div>
						<?
								$xy++;
							}						
						} elseif($multi == 3) {?>
						<select class="scrittura" name="<?=$idcampo?>[]" multiple style="width:auto;min-width: 60px;">
						<?
						}  else {?>
						<select class="scrittura" name="<?=$idcampo?>" style="width:auto;">
							<option value="">Selezionare un Valore</option><?
						}									
						
						while ($row2 = mssql_fetch_assoc($rs2))
						{
							$valore=$row2['valore'];
							$etichetta=$row2['etichetta'];
						?>
							<option value="<?=$valore?>"><?=pulisci_lettura($etichetta)?></option>
					<?	} ?>
						</select>
				  <?}
				  elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo and stato=1 order by peso asc";	
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
		<? 
		// se il modulo appartiene a quelli che devono avere il btn "salva e allega" (che crea il PDF dal RTF e lo associa alla CC)

			$moduli_con_salva_e_allega = array(218,219,220,222);
			if (in_array($idmodulo, $moduli_con_salva_e_allega)) { 
?>
			<input onclick="document.getElementById('firmadigitaledocumento').value='n'; document.getElementById('salvaeallega').value='s'; document.getElementById('fea_documento').value='n';" type="submit" title="salva e allega" value="salva e allega" class="button_salva"/>
		 <? }
			else 
			{
		// altrimenti procedo ai classici btn di salvataggio istanza e firma digitale
		?>
				<input onclick="document.getElementById('firmadigitaledocumento').value='n'; document.getElementById('salvaeallega').value='n'; document.getElementById('fea_documento').value='n';" type="submit" title="salva" value="salva" class="button_salva"/>
		<? // verifico che il modulo sia da firmare digitalmente			
				$_query = "SELECT TOP(1) firma_digitale, firma_fea
						FROM moduli
						WHERE idmodulo = $idmodulo ORDER BY versione DESC";
				//echo $_query;
				$_rs_modulo_firma_dig = mssql_query($_query, $conn);
				$_row_modulo_firma_dig = mssql_fetch_assoc($_rs_modulo_firma_dig);
				
				if(
					($idregime == 55 || 	// RD1 Intensiva
					 $idregime == 51 )     	// RD1 Estensiva
					&&
					$idmodulo == 174 		// Cartella Clinica (ex26 e Legge8) - Allergie Alimentari
				) {
					$_row_modulo_firma_dig['firma_digitale'] = 0;
				}

				if($_row_modulo_firma_dig['firma_digitale'] == '1') { ?>
					<input onclick="document.getElementById('firmadigitaledocumento').value='a'; document.getElementById('salvaeallega').value='n'; document.getElementById('fea_documento').value='n';" type="submit" title="salva e firma digitalmente" value="salva e firma digitalmente" class="button_salva"/>
			 <? } 
				if($_row_modulo_firma_dig['firma_fea']== '1') { ?>
					<input onclick="document.getElementById('firmadigitaledocumento').value='n'; document.getElementById('salvaeallega').value='n'; document.getElementById('fea_documento').value='a';" type="submit" title="salva e firma grafometricamente" value="salva e firma grafometricamente" class="button_salva"/>
				<? }
			} 
			?>	
		</div>
	</div>
	</form>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() 
	{	
	// SCRIPT ESEGUITI IN FASE DI COMPILAZIONE NUOVA ISTANZA DI UN MODULO
		//var segnalibri_rtf = "cod_icd_nove_ajax|cod_icd_dieci_ajax|cod_icidh_menom_ajax|cod_icidh_d_ajax|scala_disabilita_ajax|scala_prognosi_ajax|";		// Cartella Clinica (ex26) - Progetto riabilitativo
		var segnalibri_rtf = "cod_icd_nove_ajax|cod_icd_dieci_ajax|note_finali_ajax|";		// Cartella Clinica (ex26) - Progetto riabilitativo
			segnalibri_rtf+= "situazione_attuale_ms|metodologie_operative_ajax_ms|elenco_obiettivi_ajax_ms|misure_desito_ajax_ms|raggiungimento_risultati_ms|";			// Cartella Clinica (ex26) - Programma Riabilitativo - Mobilit� e Spostamenti
			segnalibri_rtf+= "situazione_attuale_fs|metodologie_operative_ajax_fs|elenco_obiettivi_ajax_fs|misure_desito_ajax_fs|raggiungimento_risultati_fs|";			// Cartella Clinica (ex26) - Programma Riabilitativo - Funzioni sensomotorie
			segnalibri_rtf+= "situazione_attuale_cr|metodologie_operative_ajax_cr|elenco_obiettivi_ajax_cr|misure_desito_ajax_cr|raggiungimento_risultati_cr|";			// Cartella Clinica (ex26) - Programma Riabilitativo - Comunicativo - Relazionali
			segnalibri_rtf+= "situazione_attuale_cc|metodologie_operative_ajax_cc|elenco_obiettivi_ajax_cc|misure_desito_ajax_cc|raggiungimento_risultati_cc|";			// Cartella Clinica (ex26) - Programma Riabilitativo - Cognitivo - Comportamentale
			segnalibri_rtf+= "situazione_attuale_acp|metodologie_operative_ajax_acp|elenco_obiettivi_ajax_acp|misure_desito_ajax_acp|raggiungimento_risultati_acp|";	// Cartella Clinica (ex26) - Programma Riabilitativo - Autonomia cura persona
			segnalibri_rtf+= "situazione_attuale_aea|metodologie_operative_ajax_aea|elenco_obiettivi_ajax_aea|misure_desito_ajax_aea|raggiungimento_risultati_aea|";	// Cartella Clinica (ex26) - Programma Riabilitativo - Area emotiva affettiva
			segnalibri_rtf+= "situazione_attuale_rrs|metodologie_operative_ajax_rrs|elenco_obiettivi_ajax_rrs|misure_desito_ajax_rrs|raggiungimento_risultati_rrs|";	// Cartella Clinica (ex26) - Programma Riabilitativo - Reinserimento sociale		
  			segnalibri_rtf+= "situazione_attuale_ajax_fisioterapica|obiettivi_ajax_fisioterapica|metodologie_trattamenti_ajax_fisioterapica|";							// Cartella Clinica (ex26) - Valutazioni/relazioni Fisioterapiche
			segnalibri_rtf+= "situazione_attuale_ajax_logopedica|obiettivi_ajax_logopedica|metodologie_trattamenti_ajax_logopedica|";									// Cartella Clinica (ex26) - Valutazioni/relazioni Logopediche
			segnalibri_rtf+= "situazione_attuale_ajax_psicomotoria|obiettivi_ajax_psicomotoria|metodologie_trattamenti_ajax_psicomotoria|";								// Cartella Clinica (ex26) - Valutazioni/relazioni Psicomotorie
			segnalibri_rtf+= "situazione_attuale_ajax_psicot_individuale|obiettivi_ajax_psicot_individuale|metodologie_trattamenti_ajax_psicot_individuale|";			// Cartella Clinica (ex26) - Valutazioni/relazioni Psicoterapeutiche individuali
			segnalibri_rtf+= "situazione_attuale_ajax_psicot_familiare|obiettivi_ajax_psicot_familiare|metodologie_trattamenti_ajax_psicot_familiare|";					// Cartella Clinica (ex26) - Valutazioni/relazioni Psicoterapeutiche familiari
			segnalibri_rtf+= "situazione_attuale_ajax_op_semiconvitto_a|obiettivi_ajax_op_semiconvitto_a|metodologie_trattamenti_ajax_op_semiconvitto_a|";				// Cartella Clinica (ex26) - Valutazioni/relazioni Operatori Semiconvitto A
			segnalibri_rtf+= "situazione_attuale_ajax_op_semiconvitto_b|obiettivi_ajax_op_semiconvitto_b|metodologie_trattamenti_ajax_op_semiconvitto_b|";				// Cartella Clinica (ex26) - Valutazioni/relazioni Operatori Semiconvitto B
			
			segnalibri_rtf+= "metodologie_operative_ajax_pei_acp|obiettivi_lungoterm_ajax_pei_acp|strumenti_monit_ajax_pei_acp|";										// Cartella Clinica (legge8) - Piano esecutivo individuale - Autonomia e cura della persona
			segnalibri_rtf+= "metodologie_operative_ajax_pei_rrs|obiettivi_lungoterm_ajax_pei_rrs|strumenti_monit_ajax_pei_rrs|";										// Cartella Clinica (legge8) - Piano esecutivo individuale - Riadattamento e reinserimento sociale
			segnalibri_rtf+= "obiettivi_ajax_op_centrodiurno_a|metodologie_trattamenti_ajax_op_centrodiurno_a|";														// Cartella Clinica (Legge8) - Valutazioni/relazioni Operatori Centro Diurno A
			segnalibri_rtf+= "obiettivi_ajax_op_centrodiurno_b|metodologie_trattamenti_ajax_op_centrodiurno_b|";														// Cartella Clinica (Legge8) - Valutazioni/relazioni Operatori Centro Diurno B
			
			segnalibri_rtf+= "visita_di_controllo_ajax|";																												// Cartella Clinica (ex26 e Legge8) - Visita di controllo
			segnalibri_rtf+= "tempi_verifica|resp_progetto|";																											// Cartella Clinica (ex26 e legge8) - Programma Riabilitativo
			
			segnalibri_rtf+= "raggiungimento_risultati_fms|data_raggiungimento_risultati_fms|esiti_raggiungimento_risultati_fms|";				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni motorie sensoriali
			segnalibri_rtf+= "raggiungimento_risultati_fvs|data_raggiungimento_risultati_fvs|esiti_raggiungimento_risultati_fvs|";				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni vescico sfinteriche
			segnalibri_rtf+= "raggiungimento_risultati_fcr|data_raggiungimento_risultati_fcr|esiti_raggiungimento_risultati_fcr|";				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni cardio respiratorie
			segnalibri_rtf+= "raggiungimento_risultati_fd|data_raggiungimento_risultati_fd|esiti_raggiungimento_risultati_fd|";					// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni digestive
			segnalibri_rtf+= "raggiungimento_risultati_fccl|data_raggiungimento_risultati_fccl|esiti_raggiungimento_risultati_fccl|";			// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni cognitive comportamentali e del linguaggio
			segnalibri_rtf+= "raggiungimento_risultati_ifcg|data_raggiungimento_risultati_ifcg|esiti_raggiungimento_risultati_ifcg|";			// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Informazione formazione care giver
			segnalibri_rtf+= "diagnosi_ingresso_ajax|";																							// 	Cartella Clinica (ex26 e legge8) - Programma Riabilitativo
			
			segnalibri_rtf+= "art44_valutiniz_strumento_utilizzato_a|art44_valutiniz_strumento_utilizzato_altro_a|art44_valutiniz_esito_val_funz_a|art44_valutiniz_esito_vas_a|art44_valutiniz_esito_altro_a|";
			segnalibri_rtf+= "art44_valutiniz_strumento_utilizzato_b|art44_valutiniz_strumento_utilizzato_altro_b|art44_valutiniz_esito_val_funz_b|art44_valutiniz_esito_vas_b|art44_valutiniz_esito_altro_b|";	// Cartella Clinica (ex art.44) - Valutazione iniziale
			segnalibri_rtf+= "art44_valutiniz_strumento_utilizzato_c|art44_valutiniz_strumento_utilizzato_altro_c|art44_valutiniz_esito_val_funz_c|art44_valutiniz_esito_vas_c|art44_valutiniz_esito_altro_c|";
			segnalibri_rtf+= "art44_valutiniz_strumento_utilizzato_d|art44_valutiniz_strumento_utilizzato_altro_d|art44_valutiniz_esito_val_funz_d|art44_valutiniz_esito_vas_d|art44_valutiniz_esito_altro_d|";
			
			segnalibri_rtf+= "art44_valutinter_ajax_strumento_utilizzato_a|art44_valutinter_ajax_esito_val_funz_a|art44_valutinter_ajax_esito_vas_a|";
			segnalibri_rtf+= "art44_valutinter_ajax_strumento_utilizzato_b|art44_valutinter_ajax_esito_val_funz_b|art44_valutinter_ajax_esito_vas_b|";
			segnalibri_rtf+= "art44_valutinter_ajax_strumento_utilizzato_c|art44_valutinter_ajax_esito_val_funz_c|art44_valutinter_ajax_esito_vas_c|";			// Cartella Clinica (ex art.44) - Valutazione intermedia / finale
			segnalibri_rtf+= "art44_valutinter_ajax_strumento_utilizzato_d|art44_valutinter_ajax_esito_val_funz_d|art44_valutinter_ajax_esito_vas_d|";
			segnalibri_rtf+= "art44_valutinter_risult_ajax_obiettivo_a|art44_valutinter_risult_ajax_obiettivo_b|art44_valutinter_risult_ajax_obiettivo_c|art44_valutinter_risult_ajax_obiettivo_d|";

			segnalibri_rtf+= "art44_valutfin_ajax_strumento_utilizzato_a|art44_valutfin_ajax_esito_val_funz_a|art44_valutfin_ajax_esito_vas_a|";
			segnalibri_rtf+= "art44_valutfin_ajax_strumento_utilizzato_b|art44_valutfin_ajax_esito_val_funz_b|art44_valutfin_ajax_esito_vas_b|";
			segnalibri_rtf+= "art44_valutfin_ajax_strumento_utilizzato_c|art44_valutfin_ajax_esito_val_funz_c|art44_valutfin_ajax_esito_vas_c|";				// Cartella Clinica (ex art.44) - Valutazione finale
			segnalibri_rtf+= "art44_valutfin_ajax_strumento_utilizzato_d|art44_valutfin_ajax_esito_val_funz_d|art44_valutfin_ajax_esito_vas_d|";			
			segnalibri_rtf+= "art44_valutfin_risult_ajax_obiettivo_a|art44_valutfin_risult_ajax_obiettivo_b|art44_valutfin_risult_ajax_obiettivo_c|art44_valutfin_risult_ajax_obiettivo_d|";
			
			segnalibri_rtf+= "provenienza_ricovero|provenienza_ricovero_altro|";		//Cartella Clinica (SUAP) - Generalita' e dati di ricovero 
			segnalibri_rtf+= "terapia_in_atto|";										//Cartella Clinica (SUAP) - Anagrafica - Anamnestica
			segnalibri_rtf+= "somministrazione_farmacologica_aifvb|etichetta_obiettivi_raggiunti_aifvb|data_obiettivi_raggiunti_aifvb|esiti_obiettivi_raggiunti_aifvb|raggiungimento_obiettivi_aifvb|";		//Cartella Clinica (SUAP) - Area intervento Funzioni Vitali di Base e Stabilita' Internistica
			segnalibri_rtf+= "somministrazione_farmacologica_aiacp|etichetta_obiettivi_raggiunti_aiacp|data_obiettivi_raggiunti_aiacp|esiti_obiettivi_raggiunti_aiacp|raggiungimento_obiettivi_aiacp|";		//Cartella Clinica (SUAP) - Area intervento autonomia cura persona
			segnalibri_rtf+= "somministrazione_farmacologica_aifmt|etichetta_obiettivi_raggiunti_aifmt|data_obiettivi_raggiunti_aifmt|esiti_obiettivi_raggiunti_aifmt|raggiungimento_obiettivi_aifmt|";		//Cartella Clinica (SUAP) - Area intervento funzioni mobilit� trasferimenti
			segnalibri_rtf+= "somministrazione_farmacologica_aifsm|etichetta_obiettivi_raggiunti_aifsm|data_obiettivi_raggiunti_aifsm|esiti_obiettivi_raggiunti_aifsm|raggiungimento_obiettivi_aifsm|";		//Cartella Clinica (SUAP) - Area intervento funzioni senso motorie
			segnalibri_rtf+= "somministrazione_farmacologica_aiprs|etichetta_obiettivi_raggiunti_aiprs|data_obiettivi_raggiunti_aiprs|esiti_obiettivi_raggiunti_aiprs|raggiungimento_obiettivi_aiprs|";		//Cartella Clinica (SUAP) - Area intervento partecipazione e reinserimento sociale
			segnalibri_rtf+= "somministrazione_farmacologica_aiifc|etichetta_obiettivi_raggiunti_aiifc|data_obiettivi_raggiunti_aiifc|esiti_obiettivi_raggiunti_aiifc|raggiungimento_obiettivi_aiifc|";		//Cartella Clinica (SUAP) - Area intervento informazione / formazione caregiver
			segnalibri_rtf+= "somministrazione_farmacologica_aiaa|etichetta_obiettivi_raggiunti_aiaa|data_obiettivi_raggiunti_aiaa|esiti_obiettivi_raggiunti_aiaa|raggiungimento_obiettivi_aiaa|";			//Cartella Clinica (SUAP) - Area intervento altre aree
			
			
			segnalibri_rtf+= "temp_data_giorno_I|";					//Cartella Infermieristica - Monitoraggio temperature
			segnalibri_rtf+= "mon_par_ora_1_1|";					//Cartella Infermieristica - Monitoraggio parametri
			segnalibri_rtf+= "bil_idr_ora_1_1|";					//Cartella Infermieristica - Bilancio idrico
			segnalibri_rtf+= "ris_cad_fat_correlati_1|";			//Cartella Infermieristica - Valutazione rischio cadute
			segnalibri_rtf+= "ris_inf_fat_correlati_1|";			//Cartella Infermieristica - Valutazione rischio infezioni
			segnalibri_rtf+= "somm_farm_dt_1|";						//Cartella Infermieristica - Somministrazione farmacologica
			segnalibri_rtf+= "terap_farm_farmaco_1|";				//Cartella Infermieristica - Foglio terapia farmacologica
						
			segnalibri_rtf+= "terapia|";							//Cartella Clinica (RD1) - Diario clinico - Cartella Clinica (SUAP) - Diario Clinico
			segnalibri_rtf+= "terapia_farmacologica|";				//Cartella SUAP - Documento di sintesi
	
			segnalibri_rtf+= "moduli_oscuramento_fse";				// Esercizio diritti di oscuramento FSE


		$.ajax({
			type: "POST",
			url: "ajax_autopopolamento_campi_moduli.php",
			data: {segnalibri_rtf:segnalibri_rtf, idmoduloversione: <? echo $idmoduloversione_new?>},
			success: function(response_rtf){
				if(response_rtf.trim() !== "") 
				{
					response_rtf = JSON.parse(response_rtf);
				// Cartella Clinica (ex26) - Progetto riabilitativo
					var id_campo_cod_icd_nove_ajax 				= response_rtf['cod_icd_nove_ajax'];
					var id_campo_cod_icd_dieci_ajax 			= response_rtf['cod_icd_dieci_ajax'];
					//var id_campo_cod_icidh_menom_ajax 		= response_rtf['cod_icidh_menom_ajax'];
					//var id_campo_cod_icf_fs_ajax 				= response_rtf['cod_icf_fs_ajax'];		// campo diventano manuale
					//var id_campo_cod_icidh_d_ajax 			= response_rtf['cod_icidh_d_ajax'];
					//var id_campo_cod_icf_ap_ajax 				= response_rtf['cod_icf_ap_ajax'];		// campo diventato manuale
					//var id_campo_scala_disabilita_ajax 		= response_rtf['scala_disabilita_ajax'];
					//var id_campo_scala_prognosi_ajax 			= response_rtf['scala_prognosi_ajax'];
					var note_finali_ajax 						= response_rtf['note_finali_ajax'];
					$('[name="'+note_finali_ajax+'"]').val("L'ASL competente non ha fornito PRI, in tal senso in questa sezione e' riportato il contenuto del verbale dell'UVBR.");
				
				
				// Cartella Clinica (ex26) - Programma Riabilitativo - Mobilit� e Spostamenti
					var id_campo_metodologie_operative_ajax_ms 	= response_rtf['metodologie_operative_ajax_ms'];
					var id_campo_elenco_obiettivi_ajax_ms 		= response_rtf['elenco_obiettivi_ajax_ms'];
					var id_campo_misure_desito_ajax_ms 			= response_rtf['misure_desito_ajax_ms'];
					var id_campo_situazione_attuale_ms			= response_rtf['situazione_attuale_ms'];
					var id_campo_raggiungimento_risultati_ms	= response_rtf['raggiungimento_risultati_ms'];
				
				// Cartella Clinica (ex26) - Programma Riabilitativo - Funzioni sensomotorie
					var id_campo_metodologie_operative_ajax_fs 	= response_rtf['metodologie_operative_ajax_fs'];
					var id_campo_elenco_obiettivi_ajax_fs 		= response_rtf['elenco_obiettivi_ajax_fs'];
					var id_campo_misure_desito_ajax_fs 			= response_rtf['misure_desito_ajax_fs'];
					var id_campo_situazione_attuale_fs			= response_rtf['situazione_attuale_fs'];
					var id_campo_raggiungimento_risultati_fs	= response_rtf['raggiungimento_risultati_fs'];

				
				// Cartella Clinica (ex26) - Programma Riabilitativo - Comunicativo - Relazionali
					var id_campo_metodologie_operative_ajax_cr 	= response_rtf['metodologie_operative_ajax_cr'];
					var id_campo_elenco_obiettivi_ajax_cr 		= response_rtf['elenco_obiettivi_ajax_cr'];
					var id_campo_misure_desito_ajax_cr 			= response_rtf['misure_desito_ajax_cr'];
					var id_campo_situazione_attuale_cr			= response_rtf['situazione_attuale_cr'];
					var id_campo_raggiungimento_risultati_cr	= response_rtf['raggiungimento_risultati_cr'];
					
				// Cartella Clinica (ex26) - Programma Riabilitativo - Cognitivo - Comportamentale
					var id_campo_metodologie_operative_ajax_cc 	= response_rtf['metodologie_operative_ajax_cc'];
					var id_campo_elenco_obiettivi_ajax_cc 		= response_rtf['elenco_obiettivi_ajax_cc'];
					var id_campo_misure_desito_ajax_cc 			= response_rtf['misure_desito_ajax_cc'];
					var id_campo_situazione_attuale_cc			= response_rtf['situazione_attuale_cc'];
					var id_campo_raggiungimento_risultati_cc	= response_rtf['raggiungimento_risultati_cc'];
				
				// Cartella Clinica (ex26) - Programma Riabilitativo - Autonomia cura persona
					var id_campo_metodologie_operative_ajax_acp = response_rtf['metodologie_operative_ajax_acp'];
					var id_campo_elenco_obiettivi_ajax_acp 		= response_rtf['elenco_obiettivi_ajax_acp'];
					var id_campo_misure_desito_ajax_acp 		= response_rtf['misure_desito_ajax_acp'];
					var id_campo_situazione_attuale_acp			= response_rtf['situazione_attuale_acp'];
					var id_campo_raggiungimento_risultati_acp	= response_rtf['raggiungimento_risultati_acp'];
				
				// Cartella Clinica (ex26) - Programma Riabilitativo - Area emotiva affettiva
					var id_campo_metodologie_operative_ajax_aea = response_rtf['metodologie_operative_ajax_aea'];
					var id_campo_elenco_obiettivi_ajax_aea 		= response_rtf['elenco_obiettivi_ajax_aea'];
					var id_campo_misure_desito_ajax_aea 		= response_rtf['misure_desito_ajax_aea'];
					var id_campo_situazione_attuale_aea			= response_rtf['situazione_attuale_aea'];
					var id_campo_raggiungimento_risultati_aea	= response_rtf['raggiungimento_risultati_aea'];
				
				// Cartella Clinica (ex26) - Programma Riabilitativo - Reinserimento sociale
					var id_campo_metodologie_operative_ajax_rrs = response_rtf['metodologie_operative_ajax_rrs'];
					var id_campo_elenco_obiettivi_ajax_rrs 		= response_rtf['elenco_obiettivi_ajax_rrs'];
					var id_campo_misure_desito_ajax_rrs 		= response_rtf['misure_desito_ajax_rrs'];
					var id_campo_situazione_attuale_rrs			= response_rtf['situazione_attuale_rrs'];
					var id_campo_raggiungimento_risultati_rrs	= response_rtf['raggiungimento_risultati_rrs'];
					
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni motorie sensoriali
					var id_campo_raggiungimento_risultati_fms		= response_rtf['raggiungimento_risultati_fms'];
					var id_campo_data_raggiungimento_risultati_fms	= response_rtf['data_raggiungimento_risultati_fms'];
					var id_campo_esiti_raggiungimento_risultati_fms	= response_rtf['esiti_raggiungimento_risultati_fms'];
					//var id_campo_esiti_raggiungimento_risultati_fms	= response_rtf['diagnosi'];
					
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni vescico sfinteriche
					var id_campo_raggiungimento_risultati_fvs		= response_rtf['raggiungimento_risultati_fvs'];
					var id_campo_data_raggiungimento_risultati_fvs	= response_rtf['data_raggiungimento_risultati_fvs'];
					var id_campo_esiti_raggiungimento_risultati_fvs	= response_rtf['esiti_raggiungimento_risultati_fvs'];
					
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni cardio respiratorie
					var id_campo_raggiungimento_risultati_fcr		= response_rtf['raggiungimento_risultati_fcr'];
					var id_campo_data_raggiungimento_risultati_fcr	= response_rtf['data_raggiungimento_risultati_fcr'];
					var id_campo_esiti_raggiungimento_risultati_fcr	= response_rtf['esiti_raggiungimento_risultati_fcr'];
					
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni digestive
					var id_campo_raggiungimento_risultati_fd		= response_rtf['raggiungimento_risultati_fd'];
					var id_campo_data_raggiungimento_risultati_fd	= response_rtf['data_raggiungimento_risultati_fd'];
					var id_campo_esiti_raggiungimento_risultati_fd	= response_rtf['esiti_raggiungimento_risultati_fd'];

				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni cognitive comportamentali e del linguaggio
					var id_campo_raggiungimento_risultati_fccl		 = response_rtf['raggiungimento_risultati_fccl'];
					var id_campo_data_raggiungimento_risultati_fccl	 = response_rtf['data_raggiungimento_risultati_fccl'];
					var id_campo_esiti_raggiungimento_risultati_fccl = response_rtf['esiti_raggiungimento_risultati_fccl'];
					
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Informazione formazione care giver
					var id_campo_raggiungimento_risultati_ifcg		 = response_rtf['raggiungimento_risultati_ifcg'];
					var id_campo_data_raggiungimento_risultati_ifcg	 = response_rtf['data_raggiungimento_risultati_ifcg'];
					var id_campo_esiti_raggiungimento_risultati_ifcg = response_rtf['esiti_raggiungimento_risultati_ifcg'];
					
					var id_campo_diagnosi_ingresso_ajax_res = response_rtf['diagnosi_ingresso_ajax'];
					
					
					
				// Cartella Clinica (SUAP) - Generalita' e dati di ricovero 
					var id_campo_provenienza_ricovero 		= response_rtf['provenienza_ricovero'];
					var id_campo_provenienza_ricovero_altro = response_rtf['provenienza_ricovero_altro'];
					
					$('[name="' + id_campo_provenienza_ricovero_altro+ '"]').attr('readonly', 'readonly').hide();
					$('[name="' + id_campo_provenienza_ricovero_altro + '"]').parent().parent().hide();
					// se seleziono Altro come proveninenza ricovero, appare il campo testo prima nascosto
					$('[name="' + id_campo_provenienza_ricovero + '"]').change( function(){ 
						if($('[name="' + id_campo_provenienza_ricovero + '"] option:selected').text() == 'Altro') {
							$('[name="' + id_campo_provenienza_ricovero_altro + '"]').removeAttr('readonly').show();
							$('[name="' + id_campo_provenienza_ricovero_altro + '"]').parent().parent().show();
						} else {
							$('[name="' + id_campo_provenienza_ricovero_altro+ '"]').val('').attr('readonly', 'readonly').hide();
							$('[name="' + id_campo_provenienza_ricovero_altro + '"]').parent().parent().hide();
						}
					});
					
					
				//Cartella Clinica (SUAP) - Anagrafica - Anamnestica
					var id_campo_terapia_in_atto = response_rtf['terapia_in_atto'];
					$('[name="' + id_campo_terapia_in_atto+ '"]').text('Come da cartella infermieristica');
					
					
				// Cartella Clinica (SUAP) - Area intervento Funzioni Vitali di Base e Stabilita' Internistica
					var id_campo_somministrazione_farmacologica_aifvb = response_rtf['somministrazione_farmacologica_aifvb'];
					var id_campo_etichetta_obiettivi_raggiunti_aifvb  = response_rtf['etichetta_obiettivi_raggiunti_aifvb'];
					var id_campo_data_obiettivi_raggiunti_aifvb  = response_rtf['data_obiettivi_raggiunti_aifvb'];
					var id_campo_esiti_obiettivi_raggiunti_aifvb = response_rtf['esiti_obiettivi_raggiunti_aifvb'];
					var id_campo_raggiungimento_obiettivi_aifvb  = response_rtf['raggiungimento_obiettivi_aifvb'];
					$('[name="' + id_campo_somministrazione_farmacologica_aifvb + '"]').text('Come da cartella infermieristica');
					// nascondo i campi relativi al Raggiungimento risultati/obiettivi in quanto sono da compilarsi alla modifica del modulo, non alla creazione
					if(id_campo_data_obiettivi_raggiunti_aifvb !== "") {
						$('[name="' + id_campo_data_obiettivi_raggiunti_aifvb + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_data_obiettivi_raggiunti_aifvb + '"]').parent().parent().hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aifvb + '"]').attr('readonly', 'readonly').html('<option value="N.A.">!! Non applicabile ora !!</option>').hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aifvb + '"]').parent().parent().hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aifvb + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aifvb + '"]').parent().parent().hide();
						$('[name="' + id_campo_etichetta_obiettivi_raggiunti_aifvb + '"]').parent().parent().hide()
					}
				// Cartella Clinica (SUAP) - Area intervento autonomia cura persona
				var id_campo_somministrazione_farmacologica_aiacp = response_rtf['somministrazione_farmacologica_aiacp'];
					var id_campo_etichetta_obiettivi_raggiunti_aiacp = response_rtf['etichetta_obiettivi_raggiunti_aiacp'];
					var id_campo_data_obiettivi_raggiunti_aiacp = response_rtf['data_obiettivi_raggiunti_aiacp'];
					var id_campo_esiti_obiettivi_raggiunti_aiacp = response_rtf['esiti_obiettivi_raggiunti_aiacp'];
					var id_campo_raggiungimento_obiettivi_aiacp  = response_rtf['raggiungimento_obiettivi_aiacp'];
					$('[name="' + id_campo_somministrazione_farmacologica_aiacp + '"]').text('Come da cartella infermieristica');
					// nascondo i campi relativi al Raggiungimento risultati/obiettivi in quanto sono da compilarsi alla modifica del modulo, non alla creazione
					if(id_campo_data_obiettivi_raggiunti_aiacp !== "") {
						$('[name="' + id_campo_data_obiettivi_raggiunti_aiacp + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_data_obiettivi_raggiunti_aiacp + '"]').parent().parent().hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aiacp + '"]').attr('readonly', 'readonly').html('<option value="N.A.">!! Non applicabile ora !!</option>').hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aiacp + '"]').parent().parent().hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aiacp + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aiacp + '"]').parent().parent().hide();
						$('[name="' + id_campo_etichetta_obiettivi_raggiunti_aiacp + '"]').parent().parent().hide();
					}
					
				// Cartella Clinica (SUAP) - Area intervento funzioni mobilita trasferimenti
				var id_campo_somministrazione_farmacologica_aifmt = response_rtf['somministrazione_farmacologica_aifmt'];
					var id_campo_etichetta_obiettivi_raggiunti_aifmt = response_rtf['etichetta_obiettivi_raggiunti_aifmt'];
					var id_campo_data_obiettivi_raggiunti_aifmt = response_rtf['data_obiettivi_raggiunti_aifmt'];
					var id_campo_esiti_obiettivi_raggiunti_aifmt = response_rtf['esiti_obiettivi_raggiunti_aifmt'];
					var id_campo_raggiungimento_obiettivi_aifmt  = response_rtf['raggiungimento_obiettivi_aifmt'];
					$('[name="' + id_campo_somministrazione_farmacologica_aifmt + '"]').text('Come da cartella infermieristica');
					// nascondo i campi relativi al Raggiungimento risultati/obiettivi in quanto sono da compilarsi alla modifica del modulo, non alla creazione
					if(id_campo_data_obiettivi_raggiunti_aifmt !== "") {
						$('[name="' + id_campo_data_obiettivi_raggiunti_aifmt + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_data_obiettivi_raggiunti_aifmt + '"]').parent().parent().hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aifmt + '"]').attr('readonly', 'readonly').html('<option value="N.A.">!! Non applicabile ora !!</option>').hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aifmt + '"]').parent().parent().hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aifmt + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aifmt + '"]').parent().parent().hide();
						$('[name="' + id_campo_etichetta_obiettivi_raggiunti_aifmt + '"]').parent().parent().hide();
					}
					
				// Cartella Clinica (SUAP) - Area intervento funzioni senso motorie
					var id_campo_somministrazione_farmacologica_aifsm = response_rtf['somministrazione_farmacologica_aifsm'];
					var id_campo_etichetta_obiettivi_raggiunti_aifsm = response_rtf['etichetta_obiettivi_raggiunti_aifsm'];
					var id_campo_data_obiettivi_raggiunti_aifsm = response_rtf['data_obiettivi_raggiunti_aifsm'];
					var id_campo_esiti_obiettivi_raggiunti_aifsm = response_rtf['esiti_obiettivi_raggiunti_aifsm'];
					var id_campo_raggiungimento_obiettivi_aifsm  = response_rtf['raggiungimento_obiettivi_aifsm'];
					$('[name="' + id_campo_somministrazione_farmacologica_aifsm + '"]').text('Come da cartella infermieristica');
					// nascondo i campi relativi al Raggiungimento risultati/obiettivi in quanto sono da compilarsi alla modifica del modulo, non alla creazione
					if(id_campo_data_obiettivi_raggiunti_aifsm !== "") {
						$('[name="' + id_campo_data_obiettivi_raggiunti_aifsm + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_data_obiettivi_raggiunti_aifsm + '"]').parent().parent().hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aifsm + '"]').attr('readonly', 'readonly').html('<option value="N.A.">!! Non applicabile ora !!</option>').hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aifsm + '"]').parent().parent().hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aifsm + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aifsm + '"]').parent().parent().hide();
						$('[name="' + id_campo_etichetta_obiettivi_raggiunti_aifsm + '"]').parent().parent().hide();
					}
					
				// Cartella Clinica (SUAP) - Area intervento altre aree
					var id_campo_somministrazione_farmacologica_aiaa = response_rtf['somministrazione_farmacologica_aiaa'];
					var id_campo_etichetta_obiettivi_raggiunti_aiaa = response_rtf['etichetta_obiettivi_raggiunti_aiaa'];
					var id_campo_data_obiettivi_raggiunti_aiaa = response_rtf['data_obiettivi_raggiunti_aiaa'];
					var id_campo_esiti_obiettivi_raggiunti_aiaa = response_rtf['esiti_obiettivi_raggiunti_aiaa'];
					var id_campo_raggiungimento_obiettivi_aiaa  = response_rtf['raggiungimento_obiettivi_aiaa'];
					$('[name="' + id_campo_somministrazione_farmacologica_aiaa + '"]').text('Come da cartella infermieristica');
					// nascondo i campi relativi al Raggiungimento risultati/obiettivi in quanto sono da compilarsi alla modifica del modulo, non alla creazione
					if(id_campo_data_obiettivi_raggiunti_aiaa !== "") {
						$('[name="' + id_campo_data_obiettivi_raggiunti_aiaa + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_data_obiettivi_raggiunti_aiaa + '"]').parent().parent().hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aiaa + '"]').attr('readonly', 'readonly').html('<option value="N.A.">!! Non applicabile ora !!</option>').hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aiaa + '"]').parent().parent().hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aiaa + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aiaa + '"]').parent().parent().hide();
						$('[name="' + id_campo_etichetta_obiettivi_raggiunti_aiaa + '"]').parent().parent().hide();
					}
					
					
				// Cartella Clinica (SUAP) - Area intervento partecipazione e reinserimento sociale
				var id_campo_somministrazione_farmacologica_aiprs = response_rtf['somministrazione_farmacologica_aiprs'];
					var id_campo_etichetta_obiettivi_raggiunti_aiprs = response_rtf['etichetta_obiettivi_raggiunti_aiprs'];
					var id_campo_data_obiettivi_raggiunti_aiprs = response_rtf['data_obiettivi_raggiunti_aiprs'];
					var id_campo_esiti_obiettivi_raggiunti_aiprs = response_rtf['esiti_obiettivi_raggiunti_aiprs'];
					var id_campo_raggiungimento_obiettivi_aiprs  = response_rtf['raggiungimento_obiettivi_aiprs'];
					$('[name="' + id_campo_somministrazione_farmacologica_aiprs + '"]').text('Come da cartella infermieristica');
					// nascondo i campi relativi al Raggiungimento risultati/obiettivi in quanto sono da compilarsi alla modifica del modulo, non alla creazione
					if(id_campo_data_obiettivi_raggiunti_aiprs !== "") {
						$('[name="' + id_campo_data_obiettivi_raggiunti_aiprs + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_data_obiettivi_raggiunti_aiprs + '"]').parent().parent().hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aiprs + '"]').attr('readonly', 'readonly').html('<option value="N.A.">!! Non applicabile ora !!</option>').hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aiprs + '"]').parent().parent().hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aiprs + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aiprs + '"]').parent().parent().hide();
						$('[name="' + id_campo_etichetta_obiettivi_raggiunti_aiprs + '"]').parent().parent().hide();
					}
					
				// Cartella Clinica (SUAP) - Area intervento informazione e formazione caregiver
				var id_campo_somministrazione_farmacologica_aiifc = response_rtf['somministrazione_farmacologica_aiifc'];
					var id_campo_etichetta_obiettivi_raggiunti_aiifc = response_rtf['etichetta_obiettivi_raggiunti_aiifc'];
					var id_campo_data_obiettivi_raggiunti_aiifc = response_rtf['data_obiettivi_raggiunti_aiifc'];
					var id_campo_esiti_obiettivi_raggiunti_aiifc = response_rtf['esiti_obiettivi_raggiunti_aiifc'];
					var id_campo_raggiungimento_obiettivi_aiifc  = response_rtf['raggiungimento_obiettivi_aiifc'];
					$('[name="' + id_campo_somministrazione_farmacologica_aiifc + '"]').text('Come da cartella infermieristica');
					// nascondo i campi relativi al Raggiungimento risultati/obiettivi in quanto sono da compilarsi alla modifica del modulo, non alla creazione
					if(id_campo_data_obiettivi_raggiunti_aiifc !== "") {
						$('[name="' + id_campo_data_obiettivi_raggiunti_aiifc + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_data_obiettivi_raggiunti_aiifc + '"]').parent().parent().hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aiifc + '"]').attr('readonly', 'readonly').html('<option value="N.A.">!! Non applicabile ora !!</option>').hide();
						$('[name="' + id_campo_raggiungimento_obiettivi_aiifc + '"]').parent().parent().hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aiifc + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_esiti_obiettivi_raggiunti_aiifc + '"]').parent().parent().hide();
						$('[name="' + id_campo_etichetta_obiettivi_raggiunti_aiifc + '"]').parent().parent().hide();
					}



				// Cartella Clinica (ex art.44) - Valutazione iniziale
 					var id_campo_art44_valutiniz_strumento_utilizzato_a 		= response_rtf['art44_valutiniz_strumento_utilizzato_a'];
 					var id_campo_art44_valutiniz_strumento_utilizzato_altro_a 	= response_rtf['art44_valutiniz_strumento_utilizzato_altro_a'];
					var id_campo_art44_valutiniz_esito_val_funz_a 				= response_rtf['art44_valutiniz_esito_val_funz_a'];
					var id_campo_art44_valutiniz_esito_vas_a					= response_rtf['art44_valutiniz_esito_vas_a'];
					var id_campo_art44_valutiniz_esito_altro_a					= response_rtf['art44_valutiniz_esito_altro_a'];
					var id_campo_art44_valutiniz_strumento_utilizzato_b			= response_rtf['art44_valutiniz_strumento_utilizzato_b'];
					var id_campo_art44_valutiniz_strumento_utilizzato_altro_b 	= response_rtf['art44_valutiniz_strumento_utilizzato_altro_b'];
					var id_campo_art44_valutiniz_esito_val_funz_b 				= response_rtf['art44_valutiniz_esito_val_funz_b'];
					var id_campo_art44_valutiniz_esito_vas_b					= response_rtf['art44_valutiniz_esito_vas_b'];
					var id_campo_art44_valutiniz_esito_altro_b					= response_rtf['art44_valutiniz_esito_altro_b'];
					var id_campo_art44_valutiniz_strumento_utilizzato_c 		= response_rtf['art44_valutiniz_strumento_utilizzato_c'];
					var id_campo_art44_valutiniz_strumento_utilizzato_altro_c 	= response_rtf['art44_valutiniz_strumento_utilizzato_altro_c'];
					var id_campo_art44_valutiniz_esito_val_funz_c 				= response_rtf['art44_valutiniz_esito_val_funz_c'];
					var id_campo_art44_valutiniz_esito_vas_c					= response_rtf['art44_valutiniz_esito_vas_c'];
					var id_campo_art44_valutiniz_esito_altro_c					= response_rtf['art44_valutiniz_esito_altro_c'];
					var id_campo_art44_valutiniz_strumento_utilizzato_d 		= response_rtf['art44_valutiniz_strumento_utilizzato_d'];
					var id_campo_art44_valutiniz_strumento_utilizzato_altro_d 	= response_rtf['art44_valutiniz_strumento_utilizzato_altro_d'];
					var id_campo_art44_valutiniz_esito_val_funz_d 				= response_rtf['art44_valutiniz_esito_val_funz_d'];
					var id_campo_art44_valutiniz_esito_vas_d					= response_rtf['art44_valutiniz_esito_vas_d'];
					var id_campo_art44_valutiniz_esito_altro_d					= response_rtf['art44_valutiniz_esito_altro_d'];
					// eseguo i controlli sui campi 
					if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_a + '"]').length ) {
						// nascondo i campi esito per le 2 scelte delle combo "strumento_utilizzato_x"
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').hide();	
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').hide();
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').hide();
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').hide();
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
						// in base al valore di ogni singola combo, visualizzo o nascondo i campi relativi al valore scelto
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_a + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
							} else if($(this).val() == '3')	{
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
							}
						});		
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_b + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
							} else if($(this).val() == '3')	{
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_c + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
							} else if($(this).val() == '3')	{
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_d + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
							} else if($(this).val() == '3')	{
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
							}
						});	
					} 
						
				// Cartella Clinica (ex art.44) - Valutazione intermedia / finale	
					var id_campo_art44_valutinter_ajax_strumento_utilizzato_a 	= response_rtf['art44_valutinter_ajax_strumento_utilizzato_a'];
					var id_campo_art44_valutinter_ajax_esito_val_funz_a 		= response_rtf['art44_valutinter_ajax_esito_val_funz_a'];
					var id_campo_art44_valutinter_ajax_esito_vas_a				= response_rtf['art44_valutinter_ajax_esito_vas_a'];
					var id_campo_art44_valutinter_ajax_strumento_utilizzato_b 	= response_rtf['art44_valutinter_ajax_strumento_utilizzato_b'];
					var id_campo_art44_valutinter_ajax_esito_val_funz_b 		= response_rtf['art44_valutinter_ajax_esito_val_funz_b'];
					var id_campo_art44_valutinter_ajax_esito_vas_b				= response_rtf['art44_valutinter_ajax_esito_vas_b'];
					var id_campo_art44_valutinter_ajax_strumento_utilizzato_c 	= response_rtf['art44_valutinter_ajax_strumento_utilizzato_c'];
					var id_campo_art44_valutinter_ajax_esito_val_funz_c 		= response_rtf['art44_valutinter_ajax_esito_val_funz_c'];
					var id_campo_art44_valutinter_ajax_esito_vas_c				= response_rtf['art44_valutinter_ajax_esito_vas_c'];
					var id_campo_art44_valutinter_ajax_strumento_utilizzato_d 	= response_rtf['art44_valutinter_ajax_strumento_utilizzato_d'];
					var id_campo_art44_valutinter_ajax_esito_val_funz_d 		= response_rtf['art44_valutinter_ajax_esito_val_funz_d'];
					var id_campo_art44_valutinter_ajax_esito_vas_d				= response_rtf['art44_valutinter_ajax_esito_vas_d'];
					var id_campo_art44_valutinter_risult_ajax_obiettivo_a = response_rtf['art44_valutinter_risult_ajax_obiettivo_a'];
					var id_campo_art44_valutinter_risult_ajax_obiettivo_b = response_rtf['art44_valutinter_risult_ajax_obiettivo_b'];
					var id_campo_art44_valutinter_risult_ajax_obiettivo_c = response_rtf['art44_valutinter_risult_ajax_obiettivo_c'];
					var id_campo_art44_valutinter_risult_ajax_obiettivo_d = response_rtf['art44_valutinter_risult_ajax_obiettivo_d'];
					
					// eseguo i controlli sui campi 
					if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').length ) {
						// nascondo i campi esito per le 2 scelte delle combo "strumento_utilizzato_x"
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').hide();	
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
						// in base al valore di ogni singola combo, visualizzo o nascondo i campi relativi al valore scelto
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
							}
						});		
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_b + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_c + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_d + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
							}
						});	
					} 
					
					
				// Cartella Clinica (ex art.44) - Valutazione finale	
					var id_campo_art44_valutfin_ajax_strumento_utilizzato_a = response_rtf['art44_valutfin_ajax_strumento_utilizzato_a'];
					var id_campo_art44_valutfin_ajax_esito_val_funz_a 		= response_rtf['art44_valutfin_ajax_esito_val_funz_a'];
					var id_campo_art44_valutfin_ajax_esito_vas_a			= response_rtf['art44_valutfin_ajax_esito_vas_a'];
					var id_campo_art44_valutfin_ajax_strumento_utilizzato_b = response_rtf['art44_valutfin_ajax_strumento_utilizzato_b'];
					var id_campo_art44_valutfin_ajax_esito_val_funz_b 		= response_rtf['art44_valutfin_ajax_esito_val_funz_b'];
					var id_campo_art44_valutfin_ajax_esito_vas_b			= response_rtf['art44_valutfin_ajax_esito_vas_b'];
					var id_campo_art44_valutfin_ajax_strumento_utilizzato_c = response_rtf['art44_valutfin_ajax_strumento_utilizzato_c'];
					var id_campo_art44_valutfin_ajax_esito_val_funz_c 		= response_rtf['art44_valutfin_ajax_esito_val_funz_c'];
					var id_campo_art44_valutfin_ajax_esito_vas_c			= response_rtf['art44_valutfin_ajax_esito_vas_c'];
					var id_campo_art44_valutfin_ajax_strumento_utilizzato_d = response_rtf['art44_valutfin_ajax_strumento_utilizzato_d'];
					var id_campo_art44_valutfin_ajax_esito_val_funz_d 		= response_rtf['art44_valutfin_ajax_esito_val_funz_d'];
					var id_campo_art44_valutfin_ajax_esito_vas_d			= response_rtf['art44_valutfin_ajax_esito_vas_d'];
					var id_campo_art44_valutfin_risult_ajax_obiettivo_a = response_rtf['art44_valutfin_risult_ajax_obiettivo_a'];
					var id_campo_art44_valutfin_risult_ajax_obiettivo_b = response_rtf['art44_valutfin_risult_ajax_obiettivo_b'];
					var id_campo_art44_valutfin_risult_ajax_obiettivo_c = response_rtf['art44_valutfin_risult_ajax_obiettivo_c'];
					var id_campo_art44_valutfin_risult_ajax_obiettivo_d = response_rtf['art44_valutfin_risult_ajax_obiettivo_d'];


////////////////////////////////////////////
				// se sono nel modulo "Cartella Clinica (ex art.44) - Valutazione intermedia / finale"
					var cerca_campi_ex_art44 = false;

					if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').length ) 
					{
						$('#layer_nero2').show();	// loading bar
						// nascondo i campi esito per le 2 scelte delle combo "strumento_utilizzato_x"
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').hide();	
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').hide();
						$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
						// in base al valore di ogni singola combo, visualizzo o nascondo i campi relativi al valore scelto
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
							}
						});		
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_b + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_c + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_d + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
							}
						});	
						
						segnalibri_da_cercare  = "art44_valutiniz_strumento_utilizzato_a|art44_valutiniz_esito_val_funz_a|art44_valutiniz_esito_vas_a|";
						segnalibri_da_cercare += "art44_valutiniz_strumento_utilizzato_b|art44_valutiniz_esito_val_funz_b|art44_valutiniz_esito_vas_b|";
						segnalibri_da_cercare += "art44_valutiniz_strumento_utilizzato_c|art44_valutiniz_esito_val_funz_c|art44_valutiniz_esito_vas_c|";
						segnalibri_da_cercare += "art44_valutiniz_strumento_utilizzato_d|art44_valutiniz_esito_val_funz_d|art44_valutiniz_esito_vas_d|";
						segnalibri_da_cercare += "art44_obprog_ajax_obiettivo_a|art44_obprog_ajax_obiettivo_b|art44_obprog_ajax_obiettivo_c|art44_obprog_ajax_obiettivo_d";

						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {id_cartella: <? echo $idcartella ?>, segnalibri_da_cercare:segnalibri_da_cercare},
							success: function(response)
							{
								if(response.trim() !== "") 
								{
									response = JSON.parse(response);
									
								// strumento a
									$("[name='" + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + "']").val( response.art44_valutiniz_strumento_utilizzato_a );
									if(response.art44_valutiniz_esito_val_funz_a.trim() !== "") {
										$("[name='" + id_campo_art44_valutinter_ajax_esito_val_funz_a + "']").val( response.art44_valutiniz_esito_val_funz_a ).show();
										$("[name='" + id_campo_art44_valutinter_ajax_esito_val_funz_a + "']").parent().parent().show(); }
									if(response.art44_valutiniz_esito_vas_a.trim() !== "") {
										$("[name='" + id_campo_art44_valutinter_ajax_esito_vas_a + "']").val( response.art44_valutiniz_esito_vas_a ).show();
										$("[name='" + id_campo_art44_valutinter_ajax_esito_vas_a + "']").parent().parent().show(); }
									
								// strumento b
									$("[name='" + id_campo_art44_valutinter_ajax_strumento_utilizzato_b + "']").val( response.art44_valutiniz_strumento_utilizzato_b );
									if(response.art44_valutiniz_esito_val_funz_b.trim() !== "") {
										$("[name='" + id_campo_art44_valutinter_ajax_esito_val_funz_b + "']").val( response.art44_valutiniz_esito_val_funz_b ).show();
										$("[name='" + id_campo_art44_valutinter_ajax_esito_val_funz_b + "']").parent().parent().show(); }
									if(response.art44_valutiniz_esito_vas_b.trim() !== "") {
										$("[name='" + id_campo_art44_valutinter_ajax_esito_vas_b + "']").val( response.art44_valutiniz_esito_vas_b ).show();
										$("[name='" + id_campo_art44_valutinter_ajax_esito_vas_b + "']").parent().parent().show(); }
									
								// strumento c
									$("[name='" + id_campo_art44_valutinter_ajax_strumento_utilizzato_c + "']").val( response.art44_valutiniz_strumento_utilizzato_c );
									if(response.art44_valutiniz_esito_val_funz_c.trim() !== "") {
										$("[name='" + id_campo_art44_valutinter_ajax_esito_val_funz_c + "']").val( response.art44_valutiniz_esito_val_funz_c ).show();
										$("[name='" + id_campo_art44_valutinter_ajax_esito_val_funz_c + "']").parent().parent().show(); }
									if(response.art44_valutiniz_esito_vas_c.trim() !== "") {
										$("[name='" + id_campo_art44_valutinter_ajax_esito_vas_c + "']").val( response.art44_valutiniz_esito_vas_c ).show();
										$("[name='" + id_campo_art44_valutinter_ajax_esito_vas_c + "']").parent().parent().show(); }
									
								// strumento d
									$("[name='" + id_campo_art44_valutinter_ajax_strumento_utilizzato_d + "']").val( response.art44_valutiniz_strumento_utilizzato_d );
									if(response.art44_valutiniz_esito_val_funz_d.trim() !== "") {
										$("[name='" + id_campo_art44_valutinter_ajax_esito_val_funz_d + "']").val( response.art44_valutiniz_esito_val_funz_d ).show();
										$("[name='" + id_campo_art44_valutinter_ajax_esito_val_funz_d + "']").parent().parent().show(); }
									if(response.art44_valutiniz_esito_vas_d.trim() !== "") {
										$("[name='" + id_campo_art44_valutinter_ajax_esito_vas_d + "']").val( response.art44_valutiniz_esito_vas_d ).show();
										$("[name='" + id_campo_art44_valutinter_ajax_esito_vas_d + "']").parent().parent().show(); }
										
								// obiettivi
									$("[name='" + id_campo_art44_valutinter_risult_ajax_obiettivo_a + "']").val( response.art44_obprog_ajax_obiettivo_a );
									$("[name='" + id_campo_art44_valutinter_risult_ajax_obiettivo_b + "']").val( response.art44_obprog_ajax_obiettivo_b );
									$("[name='" + id_campo_art44_valutinter_risult_ajax_obiettivo_c + "']").val( response.art44_obprog_ajax_obiettivo_c );
									$("[name='" + id_campo_art44_valutinter_risult_ajax_obiettivo_d + "']").val( response.art44_obprog_ajax_obiettivo_d );
									
									$('#layer_nero2').hide();	// loading bar
								}
							}
						});
					}
					
					
					
					
				// se sono nel modulo "Cartella Clinica (ex art.44) - Valutazione finale"
					else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_a + '"]').length )
					{
						$('#layer_nero2').show();	// loading bar
						// nascondo i campi esito per le 2 scelte delle combo "strumento_utilizzato_x"
						$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').hide();	
						$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').hide();
						$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').hide();
						$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().hide();
						$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').hide();
						$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().hide();
						// in base al valore di ogni singola combo, visualizzo o nascondo i campi relativi al valore scelto
						$('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_a + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().hide();
							}
						});		
						$('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_b + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_c + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_d + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().hide();
							}
						});	
						
						segnalibri_da_cercare  = "art44_valutinter_ajax_strumento_utilizzato_a|art44_valutinter_ajax_esito_val_funz_a|art44_valutinter_ajax_esito_vas_a|";
						segnalibri_da_cercare += "art44_valutinter_ajax_strumento_utilizzato_b|art44_valutinter_ajax_esito_val_funz_b|art44_valutinter_ajax_esito_vas_b|";
						segnalibri_da_cercare += "art44_valutinter_ajax_strumento_utilizzato_c|art44_valutinter_ajax_esito_val_funz_c|art44_valutinter_ajax_esito_vas_c|";
						segnalibri_da_cercare += "art44_valutinter_ajax_strumento_utilizzato_d|art44_valutinter_ajax_esito_val_funz_d|art44_valutinter_ajax_esito_vas_d|";
						segnalibri_da_cercare += "art44_valutinter_risult_ajax_obiettivo_a|art44_valutinter_risult_ajax_obiettivo_b|art44_valutinter_risult_ajax_obiettivo_c|art44_valutinter_risult_ajax_obiettivo_d";
						
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {id_cartella: <? echo $idcartella ?>, segnalibri_da_cercare:segnalibri_da_cercare},
							success: function(response)
							{
								if(response.trim() !== "") 
								{
									response = JSON.parse(response);
									
								// strumento a
									$("[name='" + id_campo_art44_valutfin_ajax_strumento_utilizzato_a + "']").val( response.art44_valutinter_ajax_strumento_utilizzato_a );
									if(response.art44_valutinter_ajax_esito_val_funz_a.trim() !== "") {
										$("[name='" + id_campo_art44_valutfin_ajax_esito_val_funz_a + "']").val( response.art44_valutinter_ajax_esito_val_funz_a ).show();
										$("[name='" + id_campo_art44_valutfin_ajax_esito_val_funz_a + "']").parent().parent().show(); }
									if(response.art44_valutinter_ajax_esito_vas_a.trim() !== "") {
										$("[name='" + id_campo_art44_valutfin_ajax_esito_vas_a + "']").val( response.art44_valutinter_ajax_esito_vas_a ).show();
										$("[name='" + id_campo_art44_valutfin_ajax_esito_vas_a + "']").parent().parent().show(); }
									
								// strumento b
									$("[name='" + id_campo_art44_valutfin_ajax_strumento_utilizzato_b + "']").val( response.art44_valutinter_ajax_strumento_utilizzato_b );
									if(response.art44_valutinter_ajax_esito_val_funz_b.trim() !== "") {
										$("[name='" + id_campo_art44_valutfin_ajax_esito_val_funz_b + "']").val( response.art44_valutinter_ajax_esito_val_funz_b ).show();
										$("[name='" + id_campo_art44_valutfin_ajax_esito_val_funz_b + "']").parent().parent().show(); }
									if(response.art44_valutinter_ajax_esito_vas_b.trim() !== "") {
										$("[name='" + id_campo_art44_valutfin_ajax_esito_vas_b + "']").val( response.art44_valutinter_ajax_esito_vas_b ).show();
										$("[name='" + id_campo_art44_valutfin_ajax_esito_vas_b + "']").parent().parent().show(); }
									
								// strumento c
									$("[name='" + id_campo_art44_valutfin_ajax_strumento_utilizzato_c + "']").val( response.art44_valutinter_ajax_strumento_utilizzato_c );
									if(response.art44_valutinter_ajax_esito_val_funz_c.trim() !== "") {
										$("[name='" + id_campo_art44_valutfin_ajax_esito_val_funz_c + "']").val( response.art44_valutinter_ajax_esito_val_funz_c ).show();
										$("[name='" + id_campo_art44_valutfin_ajax_esito_val_funz_c + "']").parent().parent().show(); }
									if(response.art44_valutinter_ajax_esito_vas_c.trim() !== "") {
										$("[name='" + id_campo_art44_valutfin_ajax_esito_vas_c + "']").val( response.art44_valutinter_ajax_esito_vas_c ).show();
										$("[name='" + id_campo_art44_valutfin_ajax_esito_vas_c + "']").parent().parent().show(); }
									
								// strumento d
									$("[name='" + id_campo_art44_valutfin_ajax_strumento_utilizzato_d + "']").val( response.art44_valutinter_ajax_strumento_utilizzato_d );
									if(response.art44_valutinter_ajax_esito_val_funz_d.trim() !== "") {
										$("[name='" + id_campo_art44_valutfin_ajax_esito_val_funz_d + "']").val( response.art44_valutinter_ajax_esito_val_funz_d ).show();
										$("[name='" + id_campo_art44_valutfin_ajax_esito_val_funz_d + "']").parent().parent().show(); }
									if(response.art44_valutinter_ajax_esito_vas_d.trim() !== "") {
										$("[name='" + id_campo_art44_valutfin_ajax_esito_vas_d + "']").val( response.art44_valutinter_ajax_esito_vas_d ).show();
										$("[name='" + id_campo_art44_valutfin_ajax_esito_vas_d + "']").parent().parent().show(); }
									
								// obiettivi
									$("[name='" + id_campo_art44_valutfin_risult_ajax_obiettivo_a + "']").val( response.art44_valutinter_risult_ajax_obiettivo_a );
									$("[name='" + id_campo_art44_valutfin_risult_ajax_obiettivo_b + "']").val( response.art44_valutinter_risult_ajax_obiettivo_b );
									$("[name='" + id_campo_art44_valutfin_risult_ajax_obiettivo_c + "']").val( response.art44_valutinter_risult_ajax_obiettivo_c );
									$("[name='" + id_campo_art44_valutfin_risult_ajax_obiettivo_d + "']").val( response.art44_valutinter_risult_ajax_obiettivo_d );
									
									$('#layer_nero2').hide();	// loading bar
								}
							}
						});
					}

					
					var unlock_controls = <? echo (!empty($_COOKIE['unlock_controls']) && $_COOKIE['unlock_controls'] ? 'true' : 'false') ?>;
				
				// per tutti i Programma Riabilitativo (ex26)
					var id_campo_resp_progetto					= response_rtf['resp_progetto'];
					
					// se sono in un Programma Riabilitativo EX-26, nascondo il campo "Raggiungimento risultati" in quando � da compilarsi alla modifica del modulo, non alla creazione
					var id_campo_raggiungimento_risultati = "";
					if( $('[name="' + id_campo_raggiungimento_risultati_ms + '"]').length  ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_ms;
					if( $('[name="' + id_campo_raggiungimento_risultati_fs + '"]').length  ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_fs;
					if( $('[name="' + id_campo_raggiungimento_risultati_cr + '"]').length  ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_cr;
					if( $('[name="' + id_campo_raggiungimento_risultati_cc + '"]').length  ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_cc;
					if( $('[name="' + id_campo_raggiungimento_risultati_acp + '"]').length ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_acp;
					if( $('[name="' + id_campo_raggiungimento_risultati_aea + '"]').length ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_aea;
					if( $('[name="' + id_campo_raggiungimento_risultati_rrs + '"]').length ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_rrs;
					
					if(id_campo_raggiungimento_risultati !== "") {
						$('[name="' + id_campo_raggiungimento_risultati + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_raggiungimento_risultati + '"]').html('<option value="N.A.">!! Non applicabile ora !!</option>');
						$('[name="' + id_campo_raggiungimento_risultati + '"]').parent().parent().hide()
					}
					
					// se sono in un Programma Riabilitativo RESIDENZIALE, nascondo il campo "Raggiungimento risultati" in quanto � da compilarsi alla modifica del modulo, non alla creazione
					var id_campo_raggiungimento_risultati_res = id_campo_data_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_res = "";
					if( $('[name="' + id_campo_raggiungimento_risultati_fms + '"]').length ) {
						id_campo_raggiungimento_risultati_res 		= id_campo_raggiungimento_risultati_fms;
						id_campo_data_raggiungimento_risultati_res  = id_campo_data_raggiungimento_risultati_fms;
						id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fms;
					}
					if( $('[name="' + id_campo_raggiungimento_risultati_fvs + '"]').length ) {
						id_campo_raggiungimento_risultati_res		= id_campo_raggiungimento_risultati_fvs;
						id_campo_data_raggiungimento_risultati_res  = id_campo_data_raggiungimento_risultati_fvs;
						id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fvs;		
					}
					if( $('[name="' + id_campo_raggiungimento_risultati_fcr + '"]').length ) {
						id_campo_raggiungimento_risultati_res 		= id_campo_raggiungimento_risultati_fcr;
						id_campo_data_raggiungimento_risultati_res  = id_campo_data_raggiungimento_risultati_fcr;
						id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fcr;		
					}
					if( $('[name="' + id_campo_raggiungimento_risultati_fd + '"]').length ) {
						id_campo_raggiungimento_risultati_res 		= id_campo_raggiungimento_risultati_fd;
						id_campo_data_raggiungimento_risultati_res  = id_campo_data_raggiungimento_risultati_fd;
						id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fd;		
					}
					if( $('[name="' + id_campo_raggiungimento_risultati_fccl + '"]').length ) {
						id_campo_raggiungimento_risultati_res 		= id_campo_raggiungimento_risultati_fccl;
						id_campo_data_raggiungimento_risultati_res  = id_campo_data_raggiungimento_risultati_fccl;
						id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fccl;		
					}
					if( $('[name="' + id_campo_raggiungimento_risultati_ifcg + '"]').length ) {
						id_campo_raggiungimento_risultati_res 		= id_campo_raggiungimento_risultati_ifcg;
						id_campo_data_raggiungimento_risultati_res  = id_campo_data_raggiungimento_risultati_ifcg;
						id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_ifcg;		
					}
					
					// SE SONO IN UN PROGRAMMA RIABILITATIVO (Res. Est.) E NE STO CREANDO UNA ISTANZA
					if(id_campo_raggiungimento_risultati_res !== "") {
						//NASCONDO I CAMPI "RAGGIUNGIMENTO RISULTATI"
						$('[name="' + id_campo_raggiungimento_risultati_res + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_raggiungimento_risultati_res + '"]').html('<option value="N.A.">!! Non applicabile ora !!</option>');
						$('[name="' + id_campo_raggiungimento_risultati_res + '"]').parent().parent().hide();
						
						$('[name="' + id_campo_data_raggiungimento_risultati_res + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_data_raggiungimento_risultati_res + '"]').parent().parent().hide();
						
						$('[name="' + id_campo_esiti_raggiungimento_risultati_res + '"]').attr('readonly', 'readonly').hide();
						$('[name="' + id_campo_esiti_raggiungimento_risultati_res + '"]').parent().parent().hide();
						
						var prendi_diagnosi = "prendi_diagnosi";
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {id_cartella: <? echo $idcartella ?>, prendi_diagnosi:prendi_diagnosi},
							success: function(response)
							{
								if(response.trim() !== "") {
									response = JSON.parse(response);
									$("[name='" + id_campo_diagnosi_ingresso_ajax_res + "']").val( response.diagnosi_ingresso );
								}
							}
						});
					}
					
				// Cartella Clinica (legge8) - Piano esecutivo individuale - Autonomia e cura della persona
					var id_campo_metodologie_operative_ajax_pei_acp 	 = response_rtf['metodologie_operative_ajax_pei_acp'];
					var id_campo_obiettivi_lungoterm_ajax_pei_acp 		 = response_rtf['obiettivi_lungoterm_ajax_pei_acp'];
					var id_campo_strumenti_monit_ajax_pei_acp 			 = response_rtf['strumenti_monit_ajax_pei_acp'];
					
				// Cartella Clinica (legge8) - Piano esecutivo individuale - Riadattamento e reinserimento sociale
					var id_campo_metodologie_operative_ajax_pei_rrs 	 = response_rtf['metodologie_operative_ajax_pei_rrs'];
					var id_campo_obiettivi_lungoterm_ajax_pei_rrs 		 = response_rtf['obiettivi_lungoterm_ajax_pei_rrs'];
					var id_campo_strumenti_monit_ajax_pei_rrs 			 = response_rtf['strumenti_monit_ajax_pei_rrs'];
					
				// Cartella Clinica (ex26 e Legge8) - Visita di controllo
					var id_visita_di_controllo_ajax 		= response_rtf['visita_di_controllo_ajax'];
					$('[name="'+id_visita_di_controllo_ajax+'"]').val("\n\nSi prosegue l'iter riabilitativo come definito all'interno del programma.");

				// Cartella Infermieristica - Monitoraggio temperature
					var campo_giorno_temp_I = response_rtf['temp_data_giorno_I'];
										
					if( $('[name="' + campo_giorno_temp_I + '"]').length && !unlock_controls)
					{
						var	segnalibri_da_cercare = "temp_data_giorno_II|temp_data_giorno_III|temp_data_giorno_IV|temp_data_giorno_V|temp_data_giorno_VI|temp_data_giorno_VII|temp_data_giorno_VIII|temp_data_giorno_IX|temp_data_giorno_X|temp_data_giorno_XI|temp_data_giorno_XII|temp_data_giorno_XIII|temp_data_giorno_XIV|temp_data_giorno_XV|temp_data_giorno_XVI|temp_data_giorno_XVII|temp_data_giorno_XVIII|temp_data_giorno_XIX|temp_data_giorno_XX|temp_data_giorno_XXI|temp_data_giorno_XXII|temp_data_giorno_XXIII|temp_data_giorno_XXIV|temp_data_giorno_XXV|temp_data_giorno_XXVI|temp_data_giorno_XXVII|temp_data_giorno_XXVIII|temp_data_giorno_XXIX|temp_data_giorno_XXX|temp_data_giorno_XXXI";
					
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response = JSON.parse(response);
								
								var campo_giorno_temp_II 	= response['temp_data_giorno_II'];
								var campo_giorno_temp_III 	= response['temp_data_giorno_III'];
								var campo_giorno_temp_IV 	= response['temp_data_giorno_IV'];
								var campo_giorno_temp_V 	= response['temp_data_giorno_V'];
								var campo_giorno_temp_VI 	= response['temp_data_giorno_VI'];
								var campo_giorno_temp_VII 	= response['temp_data_giorno_VII'];
								var campo_giorno_temp_VIII 	= response['temp_data_giorno_VIII'];
								var campo_giorno_temp_IX 	= response['temp_data_giorno_IX'];
								var campo_giorno_temp_X 	= response['temp_data_giorno_X'];
								var campo_giorno_temp_XI 	= response['temp_data_giorno_XI'];
								var campo_giorno_temp_XII 	= response['temp_data_giorno_XII'];
								var campo_giorno_temp_XIII 	= response['temp_data_giorno_XIII'];
								var campo_giorno_temp_XIV 	= response['temp_data_giorno_XIV'];
								var campo_giorno_temp_XV 	= response['temp_data_giorno_XV'];
								var campo_giorno_temp_XVI 	= response['temp_data_giorno_XVI'];
								var campo_giorno_temp_XVII 	= response['temp_data_giorno_XVII'];
								var campo_giorno_temp_XVIII = response['temp_data_giorno_XVIII'];
								var campo_giorno_temp_XIX 	= response['temp_data_giorno_XIX'];
								var campo_giorno_temp_XX 	= response['temp_data_giorno_XX'];
								var campo_giorno_temp_XXI 	= response['temp_data_giorno_XXI'];
								var campo_giorno_temp_XXII 	= response['temp_data_giorno_XXII'];
								var campo_giorno_temp_XXIII = response['temp_data_giorno_XXIII'];
								var campo_giorno_temp_XXIV 	= response['temp_data_giorno_XXIV'];
								var campo_giorno_temp_XXV 	= response['temp_data_giorno_XXV'];
								var campo_giorno_temp_XXVI 	= response['temp_data_giorno_XXVI'];
								var campo_giorno_temp_XXVII = response['temp_data_giorno_XXVII'];
								var campo_giorno_temp_XXVIII= response['temp_data_giorno_XXVIII'];
								var campo_giorno_temp_XXIX 	= response['temp_data_giorno_XXIX'];
								var campo_giorno_temp_XXX 	= response['temp_data_giorno_XXX'];
								var campo_giorno_temp_XXXI 	= response['temp_data_giorno_XXXI'];
								
								 							
								var oggi 		= <?php echo date('d'); ?>;
								var roman_days 	= [campo_giorno_temp_I,campo_giorno_temp_II,campo_giorno_temp_III,campo_giorno_temp_IV,campo_giorno_temp_V,campo_giorno_temp_VI,campo_giorno_temp_VII,campo_giorno_temp_VIII,campo_giorno_temp_IX,campo_giorno_temp_X,campo_giorno_temp_XI,campo_giorno_temp_XII,campo_giorno_temp_XIII,campo_giorno_temp_XIV,campo_giorno_temp_XV,campo_giorno_temp_XVI,campo_giorno_temp_XVII,campo_giorno_temp_XVIII,campo_giorno_temp_XIX,campo_giorno_temp_XX,campo_giorno_temp_XXI,campo_giorno_temp_XXII,campo_giorno_temp_XXIII,campo_giorno_temp_XXIV,campo_giorno_temp_XXV,campo_giorno_temp_XXVI,campo_giorno_temp_XXVII,campo_giorno_temp_XXVIII,campo_giorno_temp_XXIX,campo_giorno_temp_XXX,campo_giorno_temp_XXXI];
								var days 		= [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];
								
								days.forEach(disabilita_campi_non_odierni);

								function disabilita_campi_non_odierni(item, index, arr) {
									if(item !== oggi) {
										$('[name="' + roman_days[index] + '"]').html('<option value="_">Giorno non modificabile</option>').css('background-color', 'gray');
									}
								}
							}
						});
					}

				// Cartella Infermieristica - Monitoraggio parametri
					var mon_par_ora_1_1 = response_rtf['mon_par_ora_1_1'];

					if( $('[name="' + mon_par_ora_1_1 + '"]').length && !unlock_controls)
					{
						var	segnalibri_da_cercare_arr = ['ora','operatore','pa_max','pa_min','bm','spo','alvo','note'];
						var	segnalibri_da_cercare = '';
						
						for(var i=1; i<=31; i++) {
							for(var j=1; j<=2; j++) {	
								segnalibri_da_cercare_arr.forEach(function(item) {
									segnalibri_da_cercare += item + '_' + i + '_' + j + '|';
								});							
							}
						}
						
						segnalibri_da_cercare = 'mon_par_' + segnalibri_da_cercare.slice(0, -1);
						
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response = JSON.parse(response);
								
								segnalibri_da_cercare_arr = segnalibri_da_cercare.split('|');
								
								var oggi = <?php echo date('d'); ?>;

								segnalibri_da_cercare_arr.forEach(function(item) {									
									if(!item.includes('_'+oggi+'_')) {
										var obj = $('[name="' + response[item] + '"]');
										
										if(obj.is("select")) {
											obj.html('<option value="_">Giorno non modificabile</option>').css('background-color', 'gray');
										} else {
											if(obj.val() == "")
												 obj.val('.').attr('readonly',true).css('background-color', 'gray');			
											else obj.attr('readonly',true).css('background-color', 'gray');			
										}
									}
								});
							}
						});
					}
					
				// Cartella Infermieristica - Bilancio idrico
					var bil_idr_ora_1_1 = response_rtf['bil_idr_ora_1_1'];

					if( $('[name="' + bil_idr_ora_1_1 + '"]').length )
					{
						var	segnalibri_da_cercare_arr = ['ora','entrate','uscite','diuresi','glicemia','operatore','note'];
						var	segnalibri_da_cercare = '';
						
						for(var i=1; i<=31; i++) {
							for(var j=1; j<=2; j++) {	
								segnalibri_da_cercare_arr.forEach(function(item) {
									segnalibri_da_cercare += item + '_' + i + '_' + j + '|';
								});							
							}
						}
						
						segnalibri_da_cercare = 'bil_idr_' + segnalibri_da_cercare.slice(0, -1);
						
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response = JSON.parse(response);
								
								segnalibri_da_cercare_arr = segnalibri_da_cercare.split('|');
								
								var oggi = <?php echo date('d'); ?>;

								segnalibri_da_cercare_arr.forEach(function(item) {									
									if(!item.includes('_'+oggi+'_')) {
										var obj = $('[name="' + response[item] + '"]');
										
										if(obj.is("select")) {
											obj.html('<option value="_">Giorno non modificabile</option>').css('background-color', 'gray');
										} else {
											if(obj.val() == "")
												 obj.val('.').attr('readonly',true).css('background-color', 'gray');
											else obj.attr('readonly',true).css('background-color', 'gray');
										}
									}
								});
							}
						});
					}

					var ris_cad_fat_correlati_1 = response_rtf['ris_cad_fat_correlati_1'];
					
					if( $('[name="' + ris_cad_fat_correlati_1 + '"]').length )
					{						
						var	segnalibri_da_cercare = "fat_correlati_2|fat_correlati_3|fat_correlati_4|fat_correlati_5|fat_correlati_6|fat_correlati_7|fat_correlati_8|fat_correlati_9|fat_correlati_10|fat_correlati_11|fat_correlati_12|fat_correlati_13|fat_correlati_14|fat_correlati_15|";
						segnalibri_da_cercare += "obiettivi_1|obiettivi_2|obiettivi_3|obiettivi_4|obiettivi_5|obiettivi_6|obiettivi_7|obiettivi_8|obiettivi_9|obiettivi_10|obiettivi_11|obiettivi_12|obiettivi_13|obiettivi_14|obiettivi_15";

						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response = JSON.parse(response);
								
								$('[name="' + ris_cad_fat_correlati_1 + '"]').change(function() {
									if(this.value == 1) {
										$('[name="' + response['obiettivi_1'] + '"]').text('Il paziente non presentera episodi di caduta durante la degenza');
									} else if(this.value == 2) {
										$('[name="' + response['obiettivi_1'] + '"]').text('Il paziente adottera le misure di sicurezza per prevenire le cadute accidentali entro _________');
									} else {
										$('[name="' + response['obiettivi_1'] + '"]').text('');
									}
								});
								
								for(var i=2; i<=15;i++) {
									var obj = $('[name="' + response['fat_correlati_' + i] + '"]');
									obj.data('obiettivi_idx', i);
									obj.change(function() {
										var obiettivi_idx = $(this).data('obiettivi_idx');									

										if(this.value == 1) {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('Il paziente non presentera episodi di caduta durante la degenza');
										} else if(this.value == 2) {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('Il paziente adottera le misure di sicurezza per prevenire le cadute accidentali entro _________');
										} else {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('');
										}
									});
								}									
							}
						});						
					}
					
					var ris_inf_fat_correlati_1 = response_rtf['ris_inf_fat_correlati_1'];

					if( $('[name="' + ris_inf_fat_correlati_1 + '"]').length )
					{			
						var	segnalibri_da_cercare = "fat_correlati_2|fat_correlati_3|fat_correlati_4|fat_correlati_5|fat_correlati_6|fat_correlati_7|fat_correlati_8|fat_correlati_9|fat_correlati_10|fat_correlati_11|fat_correlati_12|fat_correlati_13|fat_correlati_14|fat_correlati_15|";
						segnalibri_da_cercare += "obiettivi_1|obiettivi_2|obiettivi_3|obiettivi_4|obiettivi_5|obiettivi_6|obiettivi_7|obiettivi_8|obiettivi_9|obiettivi_10|obiettivi_11|obiettivi_12|obiettivi_13|obiettivi_14|obiettivi_15";

						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response = JSON.parse(response);

								$('[name="' + ris_inf_fat_correlati_1 + '"]').change(function() {
									if(this.value == 1) {
										$('[name="' + response['obiettivi_1'] + '"]').text('Il paziente non sviluppera infezioni durante la degenza');
									} else {
										$('[name="' + response['obiettivi_1'] + '"]').text('');
									}
								});

								for(var i=2; i<=15;i++) {
									var obj = $('[name="' + response['fat_correlati_' + i] + '"]');
									obj.data('obiettivi_idx', i);
									obj.change(function() {
										var obiettivi_idx = $(this).data('obiettivi_idx');

										if(this.value == 1) {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('Il paziente non sviluppera infezioni durante la degenza');
										} else {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('');
										}
									});
								}									
							}
						});						
					}
					
					// Cartella Infermieristica - Foglio terapia farmacologica
					var id_campo_terap_farm_farmaco_1 = response_rtf['terap_farm_farmaco_1'];
					
					if( $('[name="' + id_campo_terap_farm_farmaco_1 + '"]').length )
					{
						var segnalibri_da_cercare = "qnt_1|qnt_2|qnt_3|qnt_4|qnt_5|qnt_6|qnt_7|qnt_8|qnt_9|qnt_10|qnt_11|qnt_12|qnt_13|qnt_14|qnt_15|qnt_16|qnt_17|qnt_18|qnt_19|qnt_20|qnt_21|qnt_22|qnt_23|qnt_24|qnt_25";
						segnalibri_da_cercare += "|frequenza_terapia_1|frequenza_terapia_2|frequenza_terapia_3|frequenza_terapia_4|frequenza_terapia_5|frequenza_terapia_6|frequenza_terapia_7|frequenza_terapia_8|frequenza_terapia_9|frequenza_terapia_10|frequenza_terapia_11|frequenza_terapia_12|frequenza_terapia_13|frequenza_terapia_14|frequenza_terapia_15|frequenza_terapia_16|frequenza_terapia_17|frequenza_terapia_18|frequenza_terapia_19|frequenza_terapia_20|frequenza_terapia_21|frequenza_terapia_22|frequenza_terapia_23|frequenza_terapia_24|frequenza_terapia_25";
						segnalibri_da_cercare += "|durata_terapia_1|durata_terapia_2|durata_terapia_3|durata_terapia_4|durata_terapia_5|durata_terapia_6|durata_terapia_7|durata_terapia_8|durata_terapia_9|durata_terapia_10|durata_terapia_11|durata_terapia_12|durata_terapia_13|durata_terapia_14|durata_terapia_15|durata_terapia_16|durata_terapia_17|durata_terapia_18|durata_terapia_19|durata_terapia_20|durata_terapia_21|durata_terapia_22|durata_terapia_23|durata_terapia_24|durata_terapia_25";
						segnalibri_da_cercare += "|altro_1|altro_2|altro_3|altro_4|altro_5|altro_6|altro_7|altro_8|altro_9|altro_10|altro_11|altro_12|altro_13|altro_14|altro_15|altro_16|altro_17|altro_18|altro_19|altro_20|altro_21|altro_22|altro_23|altro_24|altro_25";
						segnalibri_da_cercare += "|tipologia_prescrizione_1|tipologia_prescrizione_2|tipologia_prescrizione_3|tipologia_prescrizione_4|tipologia_prescrizione_5|tipologia_prescrizione_6|tipologia_prescrizione_7|tipologia_prescrizione_8|tipologia_prescrizione_9|tipologia_prescrizione_10|tipologia_prescrizione_11|tipologia_prescrizione_12|tipologia_prescrizione_13|tipologia_prescrizione_14|tipologia_prescrizione_15|tipologia_prescrizione_16|tipologia_prescrizione_17|tipologia_prescrizione_18|tipologia_prescrizione_19|tipologia_prescrizione_20|tipologia_prescrizione_21|tipologia_prescrizione_22|tipologia_prescrizione_23|tipologia_prescrizione_24|tipologia_prescrizione_25";
						segnalibri_da_cercare += "|allegato_1|allegato_2|allegato_3|allegato_4|allegato_5|allegato_6|allegato_7|allegato_8|allegato_9|allegato_10|allegato_11|allegato_12|allegato_13|allegato_14|allegato_15|allegato_16|allegato_17|allegato_18|allegato_19|allegato_20|allegato_21|allegato_22|allegato_23|allegato_24|allegato_25";
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response_terapia = JSON.parse(response);
								
								for(var i=1;i<=25;i++) {																		
									/*
									$('[name="' + response_terapia['altro_' + i] + '"]').parent().parent().css('display','none');
									var obj = $('[name="' + response_terapia['qnt_' + i] + '"]');

									obj.data('qnt_idx', i);
									obj.change(function() {	
										var qnt_idx = $(this).data('qnt_idx');
										$('[name="' + response_terapia['altro_' + qnt_idx] + '"]').val('');
										if(this.value == 3) {
											$('[name="' + response_terapia['altro_' + qnt_idx] + '"]').parent().parent().css('display','block');											
										} else {
											$('[name="' + response_terapia['altro_' + qnt_idx] + '"]').parent().parent().css('display','none');
										}											
									});
									*/
									$('[name="' + response_terapia['frequenza_terapia_' + i] + '"]').parent().parent().css('display','none');
									var obj_drtp_pres = $('[name="' + response_terapia['durata_terapia_' + i] + '"]');

									obj_drtp_pres.data('dr_tp_pres_idx', i);
									obj_drtp_pres.change(function() {	
										var drtp_pres_idx = $(this).data('dr_tp_pres_idx');

										if(this.value == 3 || this.value == 4) {
											$('[name="' + response_terapia['frequenza_terapia_' + drtp_pres_idx] + '"]').parent().parent().css('display','block');											
										} else {
											$('[name="' + response_terapia['frequenza_terapia_' + drtp_pres_idx] + '"]').parent().parent().css('display','none');
											$('[name="' + response_terapia['frequenza_terapia_' + drtp_pres_idx] + '"]').val('');
										}											
									});
									
									
									$('[name="' + response_terapia['allegato_' + i] + '"]').parent().parent().css('display','none');
									var obj_tp_pres = $('[name="' + response_terapia['tipologia_prescrizione_' + i] + '"]');

									obj_tp_pres.data('tp_pres_idx', i);
									obj_tp_pres.change(function() {	
										var tp_pres_idx = $(this).data('tp_pres_idx');
										$('[name="' + response_terapia['allegato_' + tp_pres_idx] + '"]').val('');
										if(this.value == 1) {
											$('[name="' + response_terapia['allegato_' + tp_pres_idx] + '"]').parent().parent().css('display','block');											
										} else {
											$('[name="' + response_terapia['allegato_' + tp_pres_idx] + '"]').parent().parent().css('display','none');
										}											
									});
								}								
							}
						});
					}
					
				// Cartella Infermieristica - Somministrazione farmacologica
					var somm_farm_dt_1 = response_rtf['somm_farm_dt_1'];

					if( $('[name="' + somm_farm_dt_1 + '"]').length )
					{
						var	segnalibri_da_cercare = "farmaco_1|farmaco_2|farmaco_3|farmaco_4|farmaco_5|farmaco_6|farmaco_7|farmaco_8|farmaco_9|farmaco_10|farmaco_11|farmaco_12|farmaco_13|farmaco_14|farmaco_15|";
						segnalibri_da_cercare += "qnt_1|qnt_2|qnt_3|qnt_4|qnt_5|qnt_6|qnt_7|qnt_8|qnt_9|qnt_10|qnt_11|qnt_12|qnt_13|qnt_14|qnt_15|";
						segnalibri_da_cercare += "altro_1|altro_2|altro_3|altro_4|altro_5|altro_6|altro_7|altro_8|altro_9|altro_10|altro_11|altro_12|altro_13|altro_14|altro_15|";
						segnalibri_da_cercare += "tip_somm_1|tip_somm_2|tip_somm_3|tip_somm_4|tip_somm_5|tip_somm_6|tip_somm_7|tip_somm_8|tip_somm_9|tip_somm_10|tip_somm_11|tip_somm_12|tip_somm_13|tip_somm_14|tip_somm_15|";
						segnalibri_da_cercare += "tm_1|tm_2|tm_3|tm_4|tm_5|tm_6|tm_7|tm_8|tm_9|tm_10|tm_11|tm_12|tm_13|tm_14|tm_15|";						
						segnalibri_da_cercare += "note_1|note_2|note_3|note_4|note_5|note_6|note_7|note_8|note_9|note_10|note_11|note_12|note_13|note_14|note_15";
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response_somministrazione = JSON.parse(response);
								
								for(var i=1;i<=15;i++) {
									$('[name="' + response_somministrazione['altro_' + i] + '"]').parent().parent().css('display','none')
									
									var obj = $('[name="' + response_somministrazione['qnt_' + i] + '"]');

									obj.data('qnt_idx', i);
									obj.change(function() {	
										var qnt_idx = $(this).data('qnt_idx');
										$('[name="' + response_somministrazione['altro_' + qnt_idx] + '"]').val('');
										if(this.value == 3) {
											$('[name="' + response_somministrazione['altro_' + qnt_idx] + '"]').parent().parent().css('display','block');											
										} else {
											$('[name="' + response_somministrazione['altro_' + qnt_idx] + '"]').parent().parent().css('display','none');
										}											
									});
								}
								
								var idmodulo = '<? echo $idmodulo?>';
								var idmoduloversione = '<? echo $idmoduloversione?>';
								var idpaziente = '<? echo $idpaziente?>';
								
								if(idmodulo == 206 && idmoduloversione >= 6146) {
									$.ajax({
										type: "POST",
										url: "ajax_autopopolamento_campi_moduli.php",
										data: {recupera_farmaci_anagrafica: 1, id_paziente: idpaziente, id_cartella: <? echo $idcartella ?>},
										success: function(response){
											if(response != null && response != 'null') {
												response = JSON.parse(response);
												
												var options = '';
												var options_val = [];
												Object.keys(response).forEach(idx => {
													options += "<option value='"+response[idx]['valore']+"' " + (response[idx]['etichetta'].indexOf("SCADUTO") > 0 ? "disabled" : "") + " >"+response[idx]['etichetta']+"</option>";
													options_val.push(response[idx]['valore']);
												});

												options = "<option value='' >Selezionare un Valore</option>" + options;
												for(var j=0;j<15;j++) {
													$('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').empty();												
													$('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').append(options);
													
													const html_items_names = {
														tip_somm: response_somministrazione['tip_somm_' + (j + 1)],
														tm: response_somministrazione['tm_' + (j + 1)],
														qnt: response_somministrazione['qnt_' + (j + 1)],
														note: response_somministrazione['note_' + (j + 1)]
													};
													
													$('select[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').change(function() {
														$('[name="' + html_items_names.tm + '[]"]').find('option').attr('selected','');
														if(this.value == '') {
															$('[name="' + html_items_names.tip_somm + '"]').val('');
															$('[name="' + html_items_names.qnt + '"]').val('');
														} else {
															let text_selected = $("option:selected", this).text();
															let match = text_selected.match(/\((\d+)\)/);
															let terap_farm_idx = '';
															
															if (match && match[1]) {
																terap_farm_idx = match[1];
															}
															
															$.ajax({
																type: "POST",
																url: "ajax_autopopolamento_campi_moduli.php",
																data: {recupera_dati_farmaco_da_somministrare: 1, id_farmaco: this.value, terap_farm_idx: terap_farm_idx, id_cartella: <? echo $idcartella ?>},
																success: function(response){
																	response = JSON.parse(response);
																	
																	if(response['struttura']) {
																		$('[name="' + html_items_names.note + '"]').val("Somministrazione d'urgenza autorizzata telefonicamente da medico responsabile a cui seguira aggiornamento terapia farmacologica entro le successive 24h");
																	} else if(idpaziente != response['id_paziente']) {
																		$('[name="' + html_items_names.note + '"]').val("Somministrazione d'urgenza autorizzata da medico responsabile a cui seguira aggiornamento terapia farmacologica entro le successive 24h");
																	} else {
																		$('[name="' + html_items_names.note + '"]').val("");
																	}
																	
																	var tip_somm = response['tip_somm'] == undefined || response['tip_somm'] == '' ? '' : response['tip_somm'];																
																	var qnt = response['qnt'] == undefined || response['qnt'] == '' ? '' : response['qnt'];
																	
																	$('[name="' + html_items_names.tip_somm + '"]').val(tip_somm);																	
																	$('[name="' + html_items_names.qnt + '"]').val(qnt);
																	
																	if(response['orario'] != undefined && response['orario'] != '') {
																		var orario = response['orario'].split(";");
																		
																		for(var i=0; i<orario.length; i++) {
																			$('[name="' + html_items_names.tm + '[]"]').find('option[value="'+orario[i]+'"]').attr('selected','true');
																		}
																	} else {
																		$('[name="' + html_items_names.tm + '[]"]').find('option').attr('selected','');																		
																	}
																}
															});
														}
													});
												}
											}
										}
									});
								} else {	
									$.ajax({
										type: "POST",
										url: "ajax_autopopolamento_campi_moduli.php",
										data: {recupera_valore_terapia_farmacologica: 1, id_cartella: <? echo $idcartella ?>},
										success: function(response){

											if(response != null && response != 'null') {
												response = JSON.parse(response);

												var options = '';
												var options_val = [];
												Object.keys(response).forEach(idx => {
													options += "<option value='"+response[idx]['valore']+"' >"+response[idx]['etichetta']+"</option>";
													options_val.push(response[idx]['valore']);
												});

												options = "<option value='' >Selezionare un Valore</option>" + options;
												for(var j=0;j<15;j++) {
													$('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').empty();												
													$('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').append(options);
													//$('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').val(options_val[j]);
												}
											}
										}
									});
								}
							}
						});
					}
				// Cartella Clinica (RD1) - Diario clinico
				// Cartella Clinica (SUAP) - Diario Clinico
					var terapia = response_rtf['terapia'];

					if($('[name="' + terapia + '"]').length)
					{
						$('[name="' + terapia + '"]').text('Come da cartella infermieristica');
					}
					
					
				// Cartella Clinica (RD1) - Documento di sintesi
					var terapia_farmacologica = response_rtf['terapia_farmacologica'];

					if( $('[name="' + terapia_farmacologica + '"]').length )
					{
						var	segnalibri_da_cercare = "terapia_farmacologica";
						
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response)
							{
								response_somministrazione = JSON.parse(response);
								
								$.ajax({
									type: "POST",
									url: "ajax_autopopolamento_campi_moduli.php",
									data: {recupera_tutti_valori_terapia_farmacologica: 1, id_cartella: <? echo $idcartella ?>, id_utente:<? echo $idpaziente?>},
									success: function(response)
									{
										response = JSON.parse(response);
										var testo = "";
										const show_labels = ["Farmaco", "Dosaggio", "Quantita", "Altro", "Orario", "Inizio periodo sospensione", "Fine periodo sospensione", "Data fine", "Note"];

										for(var regime in response) {											
											for(var item in response[regime]) {
												var elem = response[regime][item];
												
												if(!show_labels.includes(elem['etichetta']) ){
													continue;
												}
												
												if(elem['etichetta'] == 'Farmaco')
													testo += "\n";
												
												if(elem['valore'] != undefined) {
													testo += elem['etichetta'] + ": " + elem['valore'] + "\n";
												}
											}
											testo += "\n\n";
										}
										
										$('[name="' + response_somministrazione['terapia_farmacologica'] + '"]').val(testo);
									}
								});
							}
						});
					}					
					
				// Esercizio diritti di oscuramento FSE
					var id_moduli_oscuramento_fse = response_rtf['moduli_oscuramento_fse'];
					
					
// presi gli ID dei campi che mi interessano, eseguo gli autopopolamenti se esistono nel DOM (per cui se � stato aperto il modulo interessato)
					
					// Cartella Clinica (ex26) - Valutazioni/relazioni	
					// Prende i valori delle select dei soli moduli "VALUTAZIONI/RELAZIONI" e ne estrare i dati per autocompletamento
  					if ( $('[name="' + response_rtf['situazione_attuale_ajax_fisioterapica'] + '"]').length)
					{ 
						var idcampo_situaz_attuale 			= response_rtf['situazione_attuale_ajax_fisioterapica']; 		var	segnalibri = 'situazione_attuale_ms|elenco_obiettivi_ajax_ms|metodologie_operative_ajax_ms';
						var idcampo_obiettivi 				= response_rtf['obiettivi_ajax_fisioterapica'];					//var	segnalibri = 'elenco_obiettivi_ajax_ms|';
						var idcampo_metodologie_trattamenti = response_rtf['metodologie_trattamenti_ajax_fisioterapica'];	//var	segnalibri = 'metodologie_operative_ajax_ms|';
					}

  					if ( $('[name="' + response_rtf['situazione_attuale_ajax_logopedica'] + '"]').length)
					{ 
						var idcampo_situaz_attuale 			= response_rtf['situazione_attuale_ajax_logopedica']; 			var	segnalibri = 'situazione_attuale_fs|situazione_attuale_cr|elenco_obiettivi_ajax_fs|elenco_obiettivi_ajax_cr|metodologie_operative_ajax_fs|metodologie_operative_ajax_cr';
						var idcampo_obiettivi 				= response_rtf['obiettivi_ajax_logopedica'];					//var	segnalibri = 'elenco_obiettivi_ajax_fs|elenco_obiettivi_ajax_cr';
						var idcampo_metodologie_trattamenti = response_rtf['metodologie_trattamenti_ajax_logopedica'];		//var	segnalibri = 'metodologie_operative_ajax_fs|metodologie_operative_ajax_cr';
					}

  					if ( $('[name="' + response_rtf['situazione_attuale_ajax_psicomotoria'] + '"]').length)
					{ 
						var idcampo_situaz_attuale 			= response_rtf['situazione_attuale_ajax_psicomotoria']; 		var	segnalibri = 'situazione_attuale_cc|elenco_obiettivi_ajax_cc|metodologie_operative_ajax_cc';
						var idcampo_obiettivi 				= response_rtf['obiettivi_ajax_psicomotoria'];					//var	segnalibri = 'elenco_obiettivi_ajax_cc|';
						var idcampo_metodologie_trattamenti = response_rtf['metodologie_trattamenti_ajax_psicomotoria'];	//var	segnalibri = 'metodologie_operative_ajax_cc|';
					}					
					
  					if ( $('[name="' + response_rtf['situazione_attuale_ajax_psicot_individuale'] + '"]').length)
					{ 
						var idcampo_situaz_attuale 			= response_rtf['situazione_attuale_ajax_psicot_individuale']; 		var	segnalibri = 'situazione_attuale_aea|elenco_obiettivi_ajax_aea|metodologie_operative_ajax_aea';
						var idcampo_obiettivi 				= response_rtf['obiettivi_ajax_psicot_individuale'];				//var	segnalibri = 'elenco_obiettivi_ajax_aea|';
						var idcampo_metodologie_trattamenti = response_rtf['metodologie_trattamenti_ajax_psicot_individuale'];	//var	segnalibri = 'metodologie_operative_ajax_aea|';
					}		
					
  					if ( $('[name="' + response_rtf['situazione_attuale_ajax_psicot_familiare'] + '"]').length)
					{ 
						var idcampo_situaz_attuale 			= response_rtf['situazione_attuale_ajax_psicot_familiare']; 		var	segnalibri = 'situazione_attuale_aea|elenco_obiettivi_ajax_aea|metodologie_operative_ajax_aea';
						var idcampo_obiettivi 				= response_rtf['obiettivi_ajax_psicot_familiare'];					//var	segnalibri = 'elenco_obiettivi_ajax_aea|';
						var idcampo_metodologie_trattamenti = response_rtf['metodologie_trattamenti_ajax_psicot_familiare'];	//var	segnalibri = 'metodologie_operative_ajax_aea|';
					}		
					
  					if ( $('[name="' + response_rtf['situazione_attuale_ajax_op_semiconvitto_a'] + '"]').length)
					{ 
						var idcampo_situaz_attuale 			= response_rtf['situazione_attuale_ajax_op_semiconvitto_a']; 			var	segnalibri = 'situazione_attuale_acp|elenco_obiettivi_ajax_acp|metodologie_operative_ajax_acp';
						var idcampo_obiettivi 				= response_rtf['obiettivi_ajax_op_semiconvitto_a'];					//var	segnalibri = 'elenco_obiettivi_ajax_acp|';
						var idcampo_metodologie_trattamenti = response_rtf['metodologie_trattamenti_ajax_op_semiconvitto_a'];		//var	segnalibri = 'metodologie_operative_ajax_acp|';
					}		
					
  					if ( $('[name="' + response_rtf['situazione_attuale_ajax_op_semiconvitto_b'] + '"]').length)
					{ 
						var idcampo_situaz_attuale 			= response_rtf['situazione_attuale_ajax_op_semiconvitto_b']; 			var	segnalibri = 'situazione_attuale_rrs|elenco_obiettivi_ajax_rrs|metodologie_operative_ajax_rrs';
						var idcampo_obiettivi 				= response_rtf['obiettivi_ajax_op_semiconvitto_b'];					//var	segnalibri = 'elenco_obiettivi_ajax_ms|';
						var idcampo_metodologie_trattamenti = response_rtf['metodologie_trattamenti_ajax_op_semiconvitto_b'];		//var	segnalibri = 'metodologie_operative_ajax_ms|';
					}	
  					if ( $('[name="' + response_rtf['obiettivi_ajax_op_centrodiurno_a'] + '"]').length)
					{ 
						var idcampo_obiettivi 					= response_rtf['obiettivi_ajax_op_centrodiurno_a'];
						var idcampo_metodologie_trattamenti 	= response_rtf['metodologie_trattamenti_ajax_op_centrodiurno_a'];
						var	segnalibri = 'obiettivi_lungoterm_ajax_pei_acp|metodologie_operative_ajax_pei_acp';
					}	
					
  					if ( $('[name="' + response_rtf['obiettivi_ajax_op_centrodiurno_b'] + '"]').length)
					{ 
						var idcampo_obiettivi					= response_rtf['obiettivi_ajax_op_centrodiurno_b'];
						var idcampo_metodologie_trattamenti 	= response_rtf['metodologie_trattamenti_ajax_op_centrodiurno_b'];	
						var	segnalibri = 'obiettivi_lungoterm_ajax_pei_rrs|metodologie_operative_ajax_pei_rrs';
					}
					
					// se la CC non � in normativa "Trattamento privato" ed esiste il campo 'tempi_verifica', nascondo il campo (lo compiler� automaticamente in stampa_modulo.php)
					if( $('[name="idnormativa"]').val() !== 7 && $('[name="' + response_rtf['tempi_verifica'] + '"]').length )
					{
						$('[name="' + response_rtf['tempi_verifica'] + '"]').val("Come da PRI definito dall'ASL di competenza (o verbale UVBR)");//.attr('readonly', 'readonly').hide();
						//$('[name="' + response_rtf['tempi_verifica'] + '"]').parent().prev(".testo_mask").hide();
					}
						

					if (idcampo_obiettivi)
					{
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {id_cartella: <? echo $idcartella ?>, id_utente:<? echo $idpaziente?>, idmoduloversione:<? echo $idmoduloversione_new?>, segnalibri:segnalibri },
							success: function(response){
								response = JSON.parse(response);
								
								if(idcampo_situaz_attuale) {
									$("[name='" + idcampo_situaz_attuale + "']").val( response.sit_attuale);
								}
								
								$("[name='" + idcampo_obiettivi + "']").val( 	response.obiettivi );
								$("[name='" + idcampo_metodologie_trattamenti + "']").val( response.metodologie );
							}
						});	
					}
					

				
					
					
					// Modulo per l'sercizio diritti di oscuramento FSE
					// prendo l'elenco dei moduli della CC interessata atti ad essere oscurati e li inserisco nella sua combo apposita
					if (id_moduli_oscuramento_fse)
					{
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {id_cartella_fse: <? echo $idcartella ?> },
							success: function(response)
							{
								// nascondo il primo elemento vuoto della combo utilizzato come riferimento per l'append
								$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().hide();	
								
								if(trim(response) == 0) {
									$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().parent().append('\
										<div style="float:left; padding:20px 10px 0 0;clear:both;">\
											<input type="hidden" disabled value=""><strong>ATTENZIONE: in questa cartella clinica non sono presenti moduli da poter oscurare.</strong>\
										</div>');
								}
								else if(trim(response) !== "" )
								{
									var moduliArray = JSON.parse(response);
									var moduliArray_len = moduliArray.length;

									if(moduliArray_len > 0 ) {
										var z = 2;
										var idmodulopadre_prec = "";
										
										for (var i = 0; i < moduliArray_len; i++) { 
											
									// controllo se cambia modulo ed inserisco l'intestazione
											if(idmodulopadre_prec !== moduliArray[i].id_modulo_padre)
											{
												$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().parent().append('\
													<div style="float:left; padding:20px 10px 0 0;clear:both;">\
														<input type="hidden" disabled value=""><strong>' + moduliArray[i].nome + ':</strong>\
													</div>');
											}
											
									// inserisco le varie date delle varie istanze (una o piu')
											$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().parent().append('\
												<div style="float:left; padding:0 10px 0 20px;clear:both;">\
													<input type="checkbox" name="'+id_moduli_oscuramento_fse+'['+(z)+']" data-idmodulopadre="'+moduliArray[i].id_modulo_padre+'" \
														value="<?=$idcartella?>|' + moduliArray[i].id_modulo_versione + '|' + moduliArray[i].data_usa + '|' + moduliArray[i].nome + ' (compilazione del ' + moduliArray[i].data + ')">\
														Data: ' + moduliArray[i].data + '\
												</div>');
											z++;
											
									
									// quando cambia il modulo, inserisco la option "tutti" e aggiorna la var "idmodulopadre_prec" per i successivi controlli 
											if(idmodulopadre_prec !== moduliArray[i].id_modulo_padre) {
												idmodulopadre_prec = moduliArray[i].id_modulo_padre;
												
												$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().parent().append('\
													<div style="float:left; padding:0 10px 0 20px;clear:both;">\
														<input type="checkbox" name="'+id_moduli_oscuramento_fse+'['+(z)+']" data-idmodulopadre="'+moduliArray[i].id_modulo_padre+'" \
														value="<?=$idcartella?>|' + moduliArray[i].id_modulo_versione + '|tutti|nodata|' + moduliArray[i].nome + ' (tutte le compilazioni)"\
														onclick="$(\'input[type=checkbox][data-idmodulopadre='+moduliArray[i].id_modulo_padre+']\').attr(\'checked\', !$(\'input[type=checkbox][data-idmodulopadre='+moduliArray[i].id_modulo_padre+']\').attr(\'checked\') );">\
														Tutte le compilazioni\
													</div>');
														// nell'onclick c'� una funzione che fa il toggle tra checked e unchecked
												z++;
											}
											
										}

									}
								
								}
							}
						});	
					}

					
					// autopopola i primi 3 campi del modulo PROGETTO RIABILITATIVO, solo alla creazione di un nuovo modulo
					if ($('[name="' + id_campo_cod_icd_nove_ajax + '"]').length)
					{
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {id_cartella: <? echo $idcartella ?>, id_utente:<? echo $idpaziente?>, idmoduloversione:<? echo $idmoduloversione_new?>},
							success: function(response_icd){
								if(response_icd.trim() !== "") 
								{
									response_icd = JSON.parse(response_icd);
									$("[name='" + id_campo_cod_icd_nove_ajax + "']").val(response_icd.icd9);
									$("[name='" + id_campo_cod_icd_dieci_ajax + "']").val(response_icd.icd10);
									/*$("[name='" + id_campo_cod_icidh_menom_ajax + "']").val(response_icd.icidh_menom);
									$("[name='" + id_campo_cod_icf_fs_ajax + "']").val(response_icd.icf_fs);
									$("[name='" + id_campo_cod_icidh_d_ajax + "']").val(response_icd.icidh_d);
									$("[name='" + id_campo_cod_icf_ap_ajax + "']").val(response_icd.icf_ap);
									$("[name='" + id_campo_scala_disabilita_ajax + "']").val(response_icd.scala_disabilita);
									$("[name='" + id_campo_scala_prognosi_ajax + "']").val(response_icd.scala_prognosi);*/
								
								}
							}
						});
					}
					
					// Prende i valori delle select dei soli moduli "PROGRAMMA RIABILITATIVO" e ne estrare i dati per autocompletamento
					if ( $('[name="' + id_campo_metodologie_operative_ajax_ms + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_fs + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_cr + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_cc + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_acp + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_aea + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_rrs +'"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_pei_acp +'"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_pei_rrs +'"]').length )
					{

					// prendo il valore del campo del modulo "Visita Specialistica" ad esso collegato se, e solo se, l'ultima istanza di tale modulo 
					// sia stata compilata nei 20 giorni precedenti alla data di compilazione del modulo "Programma Riabilitativo"
							 if ( $('[name="' + id_campo_metodologie_operative_ajax_ms + '"]').length  )							{ var id_campo_mod_prog_riab = id_campo_situazione_attuale_ms;		var segnalibro_mod_visita_specialis = "consulenza_fisiatrica"; }
						else if ( $('[name="' + id_campo_metodologie_operative_ajax_fs + '"]').length  )							{ var id_campo_mod_prog_riab = id_campo_situazione_attuale_fs;		var segnalibro_mod_visita_specialis = "consulenza_foniatrica"; }
						else if ( $('[name="' + id_campo_metodologie_operative_ajax_cr + '"]').length  )							{ var id_campo_mod_prog_riab = id_campo_situazione_attuale_cr;		var segnalibro_mod_visita_specialis = "consulenza_foniatrica"; }
						else if ( $('[name="' + id_campo_metodologie_operative_ajax_cc + '"]').length  )							{ var id_campo_mod_prog_riab = id_campo_situazione_attuale_cc;		var segnalibro_mod_visita_specialis = "consulenza_neuropsichiatrica_infantile"; }
						else if ( $('[name="' + id_campo_metodologie_operative_ajax_acp + '"]').length )							{ var id_campo_mod_prog_riab = id_campo_situazione_attuale_acp;		var segnalibro_mod_visita_specialis = "consulenza_neuropsichiatrica_infantile"; }
						else if ( $('[name="' + id_campo_metodologie_operative_ajax_aea + '"]').length )							{ var id_campo_mod_prog_riab = id_campo_situazione_attuale_aea;		var segnalibro_mod_visita_specialis = "consulenza_psicologica"; }
					//	else if ( $('[name="' + id_campo_metodologie_operative_ajax_pei_rrs + '"]').length )	{ var id_campo_mod_prog_riab = "";		var segnalibro_mod_visita_specialis = "consulenza_fisiatrica"; }

						if(segnalibro_mod_visita_specialis) 
						{
							$.ajax({
								type: "POST",
								url: "ajax_autopopolamento_campi_moduli.php",
								data: {id_cartella: <? echo $idcartella ?>, segnalibro_mod_visita_specialis:segnalibro_mod_visita_specialis},
								success: function(response)
								{
									if(response.trim() !== "") 
									{
										response = JSON.parse(response);
										$("[name='" + id_campo_mod_prog_riab + "']").val( response.valore_visita_specialistica 	);
									}
								}
							});
						}
						
						// prende l'operatore responsabile di progetto e riempie il campo
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {id_cartella: <? echo $idcartella ?>, id_utente:<? echo $idpaziente?>, idmoduloversione:<? echo $idmoduloversione_new?>},
							success: function(response_resp_prog){
								if(response_resp_prog.trim() !== "") 
								{
									response_resp_prog = JSON.parse(response_resp_prog);
									//$("[name='" + id_campo_resp_progetto + "'],[name='" + id_campo_resp_progetto + "'],[name='" + id_campo_resp_progetto + "'],[name='" + id_campo_resp_progetto + "'],[name='" + id_campo_resp_progetto + "'],[name='" + id_campo_resp_progetto + "'],[name='" + id_campo_resp_progetto + "']").val( response_resp_prog['resp_progetto']  );
									$("[name='" + id_campo_resp_progetto + "']").val( response_resp_prog['resp_progetto']  );
								}
							}
						});	

						
						
						
						
						// al change della select delle AREE FUNZIONALI, carica i dati e riempie i campi
						$('[name="' + id_campo_metodologie_operative_ajax_ms + '"],[name="' + id_campo_metodologie_operative_ajax_fs + '"],[name="' + id_campo_metodologie_operative_ajax_cr + '"],[name="' + id_campo_metodologie_operative_ajax_cc + '"],[name="' + id_campo_metodologie_operative_ajax_acp + '"],[name="' + id_campo_metodologie_operative_ajax_aea + '"],[name="' + id_campo_metodologie_operative_ajax_rrs + '"],[name="' + id_campo_metodologie_operative_ajax_pei_acp + '"],[name="' + id_campo_metodologie_operative_ajax_pei_rrs + '"]').change(function () 
						{
							var select_name	= $(this).attr('name');
							var metodologia = $(this).find('option:selected').text();	//$("[name='"+select_name+"'] option:selected").text();							
							if (metodologia !== "Selezionare un Valore")
							{
								switch (parseInt(select_name)) {
									case id_campo_metodologie_operative_ajax_ms:		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_ms;				var name_misure_desito = id_campo_misure_desito_ajax_ms;			var area_funzionale = "Mobilita e Spostamenti";					break;
									case id_campo_metodologie_operative_ajax_fs:		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_fs;				var name_misure_desito = id_campo_misure_desito_ajax_fs;			var area_funzionale = "Area funzioni sensomotorie";				break;
									case id_campo_metodologie_operative_ajax_cr:		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_cr;				var name_misure_desito = id_campo_misure_desito_ajax_cr;			var area_funzionale = "Competenze comunicativo relazionali";	break;
									case id_campo_metodologie_operative_ajax_cc:		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_cc;				var name_misure_desito = id_campo_misure_desito_ajax_cc;			var area_funzionale = "Competenze cognitivo comportamentali";	break;
									case id_campo_metodologie_operative_ajax_acp:		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_acp;				var name_misure_desito = id_campo_misure_desito_ajax_acp;			var area_funzionale = "Autonomia della cura della persona";		break;
									case id_campo_metodologie_operative_ajax_aea:		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_aea;				var name_misure_desito = id_campo_misure_desito_ajax_aea;			var area_funzionale = "Area emotiva - affettiva";				break;
									case id_campo_metodologie_operative_ajax_rrs:		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_rrs;				var name_misure_desito = id_campo_misure_desito_ajax_rrs;			var area_funzionale = "Riadattamento e reinserimento sociale";	break;
									case id_campo_metodologie_operative_ajax_pei_acp:	var name_elenco_obiettivi = id_campo_obiettivi_lungoterm_ajax_pei_acp;		var name_misure_desito = id_campo_strumenti_monit_ajax_pei_acp;		var area_funzionale = "Autonomia della cura della persona";		break;
									case id_campo_metodologie_operative_ajax_pei_rrs:	var name_elenco_obiettivi = id_campo_obiettivi_lungoterm_ajax_pei_rrs;		var name_misure_desito = id_campo_strumenti_monit_ajax_pei_rrs;		var area_funzionale = "Riadattamento e reinserimento sociale";	break;
								}
								
								$.ajax({
									type: "POST",
									url: "ajax_autopopolamento_campi_moduli.php",
									data: {metodologia: metodologia, area_funzionale:area_funzionale},
									success: function(response_val_select){
										if(response_val_select.trim() !== "") 
										{
											response_val_select = JSON.parse(response_val_select);
											$("[name='" + name_elenco_obiettivi + "']").val( response_val_select.elenco_obiettivi 	);
											
											if (select_name == id_campo_metodologie_operative_ajax_pei_acp || select_name == id_campo_metodologie_operative_ajax_pei_rrs)
												 $("[name='" + name_misure_desito + "']"	  ).val( response_val_select.misure_desito + "\nSVAP" );
											else $("[name='" + name_misure_desito + "']"	  ).val( response_val_select.misure_desito  );
											
										} else {
											$("[name='" + name_elenco_obiettivi + "']").val("");
											$("[name='" + name_misure_desito + "']"	  ).val("");
										}
									}
								});
							} else {
								$("[name='" + name_metodologie + "']"	  ).val("");
								$("[name='" + name_elenco_obiettivi + "']").val("");
								$("[name='" + name_misure_desito + "']"	  ).val("");
							}
						});
					}
				}
			}
		});
		
	
		$.mask.addPlaceholder('~',"[+-]");	
		$(".campo_data").mask("99/99/9999");
		$(".campo_data").blur(function(){ 
			var valore = $(this).val(); 
			if(valore != ''){
				alert("Sei sicuro di voler inserire questa data: "+valore+" ?");
			}
		});
		
	});	
</script>

</div>
<?

}




function aggiungi_visita()
{

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idmoduloversione_new=$_REQUEST['idmodulo'];


$conn = db_connect();
$opeins=$_SESSION['UTENTE']->get_userid();
$query="Select * from operatori WHERE uid=$opeins";
$rs = mssql_query($query, $conn);	
if($row_m=mssql_fetch_assoc($rs)) {
	$nome_medico=$row_m['nome'];
	//if(trim($row_m['titolo'])!="") $titolo_medico=$row_m['titolo']." - ";	
}
mssql_free_result($rs);


$query="SELECT replica, id FROM dbo.moduli WHERE (id = $idmoduloversione_new)";
$rs = mssql_query($query, $conn);	
if(!$rs) error_message(mssql_error());
if($row = mssql_fetch_assoc($rs)) $replica=$row['replica'];
mssql_free_result($rs);

$query="select * from campi where idmoduloversione=$idmoduloversione_new order by peso asc";	

$rs1 = mssql_query($query, $conn);
if(!$rs1) error_message(mssql_error());


?>
<script>inizializza();</script>
<div id="sanitaria">
<!-- qui va il codice dinamico dei campi -->
	<div class="titolo_pag"><h1>creazione modulo: </h1></div>	
	
<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
	
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>

	<input type="hidden" name="action" value="create_visita" />
	<input type="hidden" name="idcartella" value="0" />
	<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
	<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
	<input type="hidden" name="impegnativa" value="<?if(($replica==2)or($replica==3)) echo("si"); else echo("no");?>" />
	
	<div class="blocco_centralcat">
		<?

		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		$i=1;
		
	if(($replica==2)or($replica==3)){?>		
		<div class="rigo_mask">
            <div class="testo_mask">associa alla pratica</div>
            <div class="campo_mask">
                <select name="idimpegnativa" class="scrittura" style="width:auto;">
				<?
				$query_1 = "SELECT * from re_pazienti_impegnative where idutente=$idpaziente order by DataAutorizAsl desc";	
				$rs11 = mssql_query($query_1, $conn);
				
				while($row11 = mssql_fetch_assoc($rs11))  {
						$idimpegnativa=$row11['idimpegnativa'];
						$data_auth_asl=formatta_data($row11['DataAutorizAsl']);
						$prot_auth_asl=$row11['ProtAutorizAsl'];
						$regime=$row11['regime'];
						if($row11['DataDimissione']!="")
							$chiusa=1;
							else
							$chiusa=0;
					?>
					<option value="<?=$idimpegnativa?>"><?if($chiusa)echo(" -CHIUSA- ");?><?=$prot_auth_asl." ".$data_auth_asl." - ".$regime?></option>
					<?
					}
					mssql_free_result($rs11);
				?>
				</select>
            </div>
        </div>		
		<?}		
		while($row1 = mssql_fetch_assoc($rs1)){
			$idcampo= $row1['idcampo'];
			$etichetta= pulisci_lettura($row1['etichetta']);
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
		
			if ($obbligatorio=='y')
			$classe="mandatory ".$classe;
		if (trim($etichetta)!='')
	{
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
		?>
		
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<div class="etichetta_campi"><?=$etichetta?></div>
			<input type="hidden" class="scrittura" name="<?=$idcampo?>" >		
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if(($multi==1)or($multi==2)) echo($stile);?>>
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
					 {?>
					<input type="file" class="scrittura_campo" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==8)
					 {?>
					<!--<input type="text" class="scrittura_campo" name="<=$idcampo?>" value="<=$titolo_medico.$nome_medico?>">-->
					<input type="text" class="scrittura_campo" name="<?=$idcampo?>" value="<?=$nome_medico?>">
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
								$valore=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);						
						?>					
					<div style="float:left; padding:0 10px 0 0;clear:both;"><input type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=$etichetta?></div>
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
				  <?}?>
            </div>
			<?}?>
		</div>	
   
	<?
	if(($tipo==2)or($multi==1)or($multi==2)) $i=0;
	if(($i%3==0)and($tipo!=5)){ 
		echo("</div>");
		$div=0;}
	}
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

}


function create_modulo(){
	$idcartella=$_REQUEST['idcartella'];
	$cartella=$_REQUEST['cartella'];
	$idpaziente=$_REQUEST['idpaziente'];
	$firmadigitaledocumento = $_REQUEST['firmadigitaledocumento'];	// n: solo salvataggio, a: salvare, salvataggio con prevista firma (a = attesa firma)
	$fea_documento = $_REQUEST['fea_documento'];					// n: solo salvataggio, a: salvare, salvataggio con prevista firma (a = attesa firma)
	$salvaeallega = $_REQUEST['salvaeallega'];						// s: salvare, convertire rtf in pdf e associare documento alla CC
	$idmoduloversione=$_REQUEST['idmoduloversione'];
	$idmoduloversione_new=$idmoduloversione;
	
	$datains = date('d/m/Y');
	$orains = date('H:i');
	$ipins = $_SERVER['REMOTE_ADDR'];
	$opeins = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	
	if($_POST["impegnativa"]=="no")	$id_impegnativa="NULL";
	if($_POST["impegnativa"]=="si")	$id_impegnativa=$_POST['idimpegnativa'];
	
	$data_osservazione="NULL";	
	$query="select * from moduli WHERE id=$idmoduloversione";
	$rs = mssql_query($query, $conn);
	if($row = mssql_fetch_assoc($rs)) $idmodulo=$row['idmodulo'];
	mssql_free_result($rs);	
	
	
	if($id_impegnativa=='NULL') 
		$imp_query="id_impegnativa IS NULL";
		else
		$imp_query="id_impegnativa=$id_impegnativa";
	
	$query="SELECT TOP(1) max (id_inserimento) as maxins, offuscamento_fse 
			FROM istanze_testata 
			WHERE id_cartella=$idcartella and id_modulo_padre=$idmodulo and $imp_query 
			GROUP BY id_inserimento, offuscamento_fse ORDER BY maxins DESC";
	$rs = mssql_query($query, $conn);		

	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)) {
		// sono gi� presenti altre istanze per questo modulo
		$maxins=$row['maxins']+1;
		$offuscamento_fse = $row['offuscamento_fse'];
	} else {
		// e' la prima istanza per questo modulo
		$maxins=1;
		$offuscamento_fse = 'n';
	}
	mssql_free_result($rs);	
	
	
	/* l'istanza deve essere validata prima di essere inviata al FSE. Se il modulo � la "Riunione d'equipe" (che prevede il multifirma) se ne occuper� il DS, altrimenti l'operatore che compila */	
	if($idmodulo == 134) {
		$query_ds="SELECT uid FROM operatori where dir_sanitario = 'y'";
		$rs_ds = mssql_query($query_ds, $conn);
		$row_ds = mssql_fetch_assoc($rs_ds);
		$ope_validazione = $row_ds['uid'];	
	}
	else $ope_validazione = $opeins;
		
	mssql_free_result($rs_ds);	
	
	
	/* inserimento in testata istanze */
		if($firmadigitaledocumento == 'a')		$firmato_digitalmente = 's';
	elseif($firmadigitaledocumento == 'n')		$firmato_digitalmente = 'n';
	
		if($fea_documento == 'a')		$firmato_fea = 's';
	elseif($fea_documento == 'n')		$firmato_fea = 'n';
	
	$query="insert into istanze_testata (id_modulo_padre,id_modulo_versione,id_cartella,id_paziente,id_impegnativa,data_osservazione,id_inserimento,datains,orains,opeins,ipins,offuscamento_fse,ope_validazione,firmato_digitalmente,firmato_fea) 
								values ($idmodulo,$idmoduloversione_new,$idcartella,$idpaziente,$id_impegnativa,NULL,$maxins,'$datains','$orains',$opeins,'$ipins','$offuscamento_fse',$ope_validazione,'$firmato_digitalmente','$firmato_fea')";
	//die($query);
	$rs2 = mssql_query($query, $conn);
	
	$query="SELECT MAX(id_istanza_testata) AS id_istanza, opeins FROM dbo.istanze_testata GROUP BY opeins HAVING (opeins = $opeins)";
	$rs2 = mssql_query($query, $conn);
	$row2 = mssql_fetch_assoc($rs2);
	$id_istanza=$row2['id_istanza'];

// se l'istanza � da firmare con la FEA, ne scrivo i dettagli in tbl
	if($fea_documento == 'a') {
		$query_fea = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato, id_paziente, tipo_firma) 
												values ($id_istanza, $opeins, 'a', $idpaziente, 'fea')";
		$rs_fea = mssql_query($query_fea, $conn);
	}
	/* fine inserimento */	
			
	$query="select * from campi where idmoduloversione=$idmoduloversione_new order by peso asc";	

	$rs1 = mssql_query($query, $conn);
	if(!$rs1) error_message(mssql_error());
		
	$ultima_compilazione=date('d/m/Y h:i:s A');
	$tbl_firma_inserito = 0;
	
	$lista_farmaci = array();
	$farmaci_struttura_da_rimuovere = array();
	$farmaci_paz_paz_da_rimuovere = array();
	
	$op_emotrasfusioni_1 = '';
	$op_emotrasfusioni_2 = '';

	while($row1 = mssql_fetch_assoc($rs1))
	{
		$idcampo= $row1['idcampo'];
		if (($idmodulo == 200) || isset($_POST[$idcampo]))
		{
			$valore=$_POST[$idcampo];
			
			// SE IL MODULO E' LA TERAPIA FARMACOLOGICA
			if($idmodulo == 205 && !empty($valore)) {
				if (strpos($row1['segnalibro'],'terap_farm_farmaco_') !== false) {
					$idx = explode('_', $row1['segnalibro']);
					$idx = $idx[count($idx)-1];
					
					$lista_farmaci[$idx]['nome'] = trim($valore);
				} elseif (strpos($row1['segnalibro'],'dos_') !== false) {
					$idx = explode('_', $row1['segnalibro']);
					$idx = $idx[count($idx)-1];
					
					$valore = trim($valore);
					$valore = str_replace(',', '.', $valore);
					
					$lista_farmaci[$idx]['dosaggio'] = $valore;
				} elseif (strpos($row1['segnalibro'],'tip_somm_') !== false) {
					$idx = explode('_', $row1['segnalibro']);
					$idx = $idx[count($idx)-1];
					
					$lista_farmaci[$idx]['tip_somm'] = trim($valore);
				} elseif (strpos($row1['segnalibro'],'operatore_') !== false) {
					$idx = explode('_', $row1['segnalibro']);
					$idx = $idx[count($idx)-1];
					
					if(!isset($lista_farmaci[$idx]['nome']) || empty($lista_farmaci[$idx]['nome'])) {
						$valore = '';
					}
				}
			}
			
			// SE IL MODULO E' LA SOMMINISTRAZIONE FARMACOLOGICA
			if($idmodulo == 206) {
				if(!empty($valore)) {
					if(strpos($row1['segnalibro'],'farmaco_') !== false) {
						$idx = explode('_', $row1['segnalibro']);
						$idx = $idx[count($idx)-1];
						
						$lista_farmaci[$idx]['id'] = trim($valore);
							
						$_query = "SELECT 1
									FROM farmaci 
									WHERE struttura = 1 AND deleted = 0 AND id = $valore";
						$_rs = mssql_query($_query, $conn);
						$_row = mssql_fetch_assoc($_rs);

						if(!empty($_row)) {
							$farmaci_struttura_da_rimuovere[] = $valore;
						} else {
							$_query = "SELECT id
										FROM farmaci_paziente_pazienti
										WHERE id_paziente_destinatario = $idpaziente AND id_farmaco = $valore";
							$_rs = mssql_query($_query, $conn);
							$_row = mssql_fetch_assoc($_rs);
							
							if(!empty($_row)) {
								$farmaci_paz_paz_da_rimuovere[] = $_row['id'];
							}
						}
					} elseif(strpos($row1['segnalibro'],'qnt_') !== false) {
						$idx = explode('_', $row1['segnalibro']);
						$idx = $idx[count($idx)-1];
						
						$valore = trim($valore);
						$valore = str_replace(',', '.', $valore);
						
						$lista_farmaci[$idx]['qnt'] = $valore;
					}
				}
				
				if(strpos($row1['segnalibro'],'somm_farm_dt_') !== false
						|| strpos($row1['segnalibro'],'dt_') !== false) {
					$idx = explode('_', $row1['segnalibro']);
					$idx = $idx[count($idx)-1];

					if((empty($valore) || trim($valore) == '') && (isset($lista_farmaci[$idx]['id']) || !empty($lista_farmaci[$idx]['id']))) {
						$valore = date('d/m/Y');
					}
				}
			}
			
			//INIZIO ---ICDM9
			if($row1['etichetta']=='ICD 9'){
				$multi_ram=1;										
				$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";						
				$rs2 = mssql_query($query2, $conn);						
				if(!$rs2) error_message(mssql_error());													 
				while ($row2 = mssql_fetch_assoc($rs2)){							
					$idc=$row2['idcombo'];
					$idcampocombo=$row2['idcampocombo'];
					$valore1=$row2['valore'];
					$etichetta1=$row2['etichetta'];
					$v=(int)($valore);
					$valore_m=get_rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
					if($valore_m!='') break;
				}
				$qry_imp="UPDATE impegnative SET icdm9='$valore_m' where idimpegnativa=$id_impegnativa";
				$rs_imp=mssql_query($qry_imp, $conn);
			}
			//FINE --- ICDM9

			//INIZIO ---ICDM9-2
			if($row1['etichetta']=='ICD 9 -2'){
				$multi_ram=1;										
				$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";						
				$rs2 = mssql_query($query2, $conn);						
				if(!$rs2) error_message(mssql_error());													 
				while ($row2 = mssql_fetch_assoc($rs2)){							
					$idc=$row2['idcombo'];
					$idcampocombo=$row2['idcampocombo'];
					$valore1=$row2['valore'];
					$etichetta1=$row2['etichetta'];
					$v=(int)($valore);
					$valore_m=get_rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
					if($valore_m!='') break;
				}
				$qry_imp="UPDATE impegnative SET icd92='$valore_m' where idimpegnativa=$id_impegnativa";
				$rs_imp=mssql_query($qry_imp, $conn);
			}
			//FINE --- ICDM9	


			if (is_array($valore)){
				$val="";
				foreach ($valore as $key => $value) {
					//echo "Hai selezionato la checkbox: $key con valore: $value<br />";
					$val.=$value.";";
				}					
				$valore=substr($val,0,strlen($val)-1);
			}
			$valore=pulisci($valore);				


			// INIZIO - MODULI PER OSCURAMENTO DA FSE (Fascicolo Sanitario Elettronico)
			if($row1['etichetta']=='Moduli FSE da oscurare')	//etichetta del campo
			{
				$nuovo_valore = "";
				$idcampo_moduli = $row1['idcampo'];
				$arr_elenco_moduli = explode(";", $valore);
				for ($i = 0; $i < count($arr_elenco_moduli); $i++)  {
					$arr_dettagli_modulo = explode('|', $arr_elenco_moduli[$i]);
					$id_c 			= $arr_dettagli_modulo[0];				// id cartella
					$id_mod_vers 	= $arr_dettagli_modulo[1];				// id modulo versione
					$data_istanza 	= $arr_dettagli_modulo[2];				// data osservazione o data inserimento/creazione modulo
					$tipo_data 		= $arr_dettagli_modulo[3];				// il tipo della data su menzionata (ovvervazione o inserimento)
					$nuovo_valore  .= $arr_dettagli_modulo[4].";";          // nome del modulo + la sua data + andatura a capo per moduli RTF(comporra' il valore da inserire in tbl istanza_dettaglio)
				
					if($data_istanza == "tutti")			$where_data_osser = "";
					else {
							if($tipo_data = "dataoss")		$where_data_osser = " AND data_osservazione = CAST('$data_istanza' AS DATE)";
						elseif($tipo_data = "datains")		$where_data_osser = " AND datains = CAST('$data_istanza' AS DATE)";
					}
				
					$query_oscura_modulo_fse = "UPDATE istanze_testata SET offuscamento_fse = 'o' WHERE id_cartella = $id_c AND id_modulo_versione = $id_mod_vers $where_data_osser";
					//echo $query_oscura_modulo_fse;
					$rs_query_oscura_modulo_fse = mssql_query($query_oscura_modulo_fse, $conn);
				}
				$valore = $nuovo_valore;
			}
			// FINE - MODULI PER OSCURAMENTO DA FSE (Fascioclo Sanitario Elettronico)
			
			
			// INIZIO - ASSEGNAZIONE DEL O DEGLI OPERATORI CHE DEVONO FIRMARE DIGITALMENTE QUESTO DOCUMENTO
			if($tbl_firma_inserito == 0 && $firmadigitaledocumento == 'a')
			{
				// definizione dei moduli interessati da dinamiche particolari
				$riunioni_d_equipe = 134;
				$emotrasfusioni = 269;				
				$moduli_resid_estens = array(177,178,179,180,182,183,184,185);
				$moduli_cartella_inf = array(202,203,204,205,206,207,208,209);
				
				// "Riunioni d'equipe"
				// qui chi deve firmare digitalm. sono gli operatori della combo "Elenco firmatari" (combo obbligatoria)
				if($idmodulo == $riunioni_d_equipe)
				{
					if($row1['etichetta'] == 'Elenco firmatari')		
					{
						$arr_elenco_firmatari = explode(";", $valore);
						foreach ($arr_elenco_firmatari as $operatore) {
							$query_firma = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato) values ($id_istanza, $operatore, 'a')";
							$rs_firma = mssql_query($query_firma, $conn);
							$tbl_firma_inserito = 1;
						} 
					}
				} 
				// moduli regime Residenziale Estentiva
				// nei moduli di cui e' composto l'array "$moduli_resid_estens", firma sempre sia l'operatore che compila che il DS
				// SBO 25/07/23 - sostituito $id_modulo con $idmodulo
				elseif (in_array($idmodulo, $moduli_resid_estens)) 
				{
					$query_firma_ope = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato) values ($id_istanza, $opeins, 'a')";
					$rs_firma_ope = mssql_query($query_firma_ope, $conn);
										
					$query = "select TOP 1 uid from operatori where dir_sanitario = 'y'";
					$rs = mssql_query($query, $conn);
					$row = mssql_fetch_assoc($rs);
					$id_direttore_sanitario = $row['uid'];

					$query_firma_ds = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato) values ($id_istanza, $id_direttore_sanitario, 'a')";
					$rs_firma_ds = mssql_query($query_firma_ds, $conn);
					$tbl_firma_inserito = 1;
				}
				// moduli Cartella Infermieristica
				// nei moduli di cui e' composto l'array "$moduli_cartella_inf", firma sempre sia l'operatore che compila che il DS
				// SBO 25/07/23 - sostituito $id_modulo con $idmodulo
				elseif (in_array($idmodulo, $moduli_cartella_inf)) 
				{
					$query_firma_ope = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato) values ($id_istanza, $opeins, 'a')";
					$rs_firma_ope = mssql_query($query_firma_ope, $conn);

					$query = "select TOP 1 uid from operatori where dir_sanitario = 'y'";
					$rs = mssql_query($query, $conn);
					$row = mssql_fetch_assoc($rs);
					$id_direttore_sanitario = $row['uid'];

					$query_firma_ds = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato) values ($id_istanza, $id_direttore_sanitario, 'a')";
					$rs_firma_ds = mssql_query($query_firma_ds, $conn);
					$tbl_firma_inserito = 1;
				}
				elseif($idmodulo == $emotrasfusioni)
				{
					if($row1['segnalibro'] == 'firma_op_1') {						
						$op_emotrasfusioni_1 = trim($valore);
					} elseif($row1['segnalibro'] == 'firma_op_2') {
						$op_emotrasfusioni_2 = $valore;
					}
				}
				// casistica normale, valida per tutti gli altri moduli per cui � prevista la firma digitale (firma solo l'operatore che compila)
				else
				{
					$query_firma = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato) values ($id_istanza, $opeins, 'a')";
					$rs_firma = mssql_query($query_firma, $conn);				
					$tbl_firma_inserito = 1;
				}
			}
			// FINE - ASSEGNAZIONE DEL O DEGLI OPERATORI CHE DEVONO FIRMARE DIGITALMENTE QUESTO DOCUMENTO
			
			// per il modulo terapia farmacologica vado ad inserire nella combo sfruttata poi nel modulo di somministrazione farmacologica, i farmaci 
			// VECCHIA GESTIONE MODULO Cartella Infermieristica - Foglio terapia farmacologica
			/*
			if(205 == $idmodulo && strpos($row1['segnalibro'],'farmaco_') !== false) {
				$query_chk = "SELECT idcampo FROM campi_combo WHERE idcombo = 249 and etichetta = '$valore' ";
				$rs_chk = mssql_query($query_chk, $conn);
				$row_chk = mssql_fetch_assoc($rs_chk);
				
				if(empty($row_chk) && !empty($valore)) 
				{
					$sql_peso = "SELECT MAX(peso) as peso FROM campi_combo WHERE idcombo = 249";
					$rs_peso = mssql_query($sql_peso, $conn);
					$row_peso = mssql_fetch_assoc($rs_seq);
					$peso = 1;

					if(!empty($row_peso['peso'])) {
						$peso = $row_peso['peso'] + 1;
					}
					
					// inserito il CAST as INT perch� il campo valore � un varchar e non un int, 
					// per cui veniva eseguito un MAX alfabetico e non numerico
					$sql_val = "SELECT MAX(CAST(valore AS Int)) as valore FROM campi_combo WHERE idcombo = 249";
					$rs_val = mssql_query($sql_val, $conn);
					$row_val = mssql_fetch_assoc($rs_val);
					$val = 1;

					if(!empty($row_val['valore'])) {
						$val = $row_val['valore'] + 1;
					}
					
					$query_firma_ds = "INSERT INTO campi_combo (idcombo, etichetta, stato, peso, valore, cancella) 
										VALUES (249, '$valore', '1', $peso, $val, 'n')";
					$rs_firma_ds = mssql_query($query_firma_ds, $conn);
				}
			}
			*/
			/*inserimento dell'istanza in istanza_dettaglio*/
			$query="insert into istanze_dettaglio (id_istanza_testata,idcampo,valore) values ($id_istanza,$idcampo,'$valore')";
			$rs2 = mssql_query($query, $conn);
			/*fine inserimento*/
			if(!$rs2){
				echo("no2");
				exit();
				die();
			}
			if($row1['tipo']==6){				
				/*aggiornamento istanze_testata con data osservazione*/
				$query="update istanze_testata set data_osservazione='$valore' where id_istanza_testata=$id_istanza";
				$rs2 = mssql_query($query, $conn);
				/*fine aggiornamento*/
			}
		}		
		
		elseif(isset($_FILES[$idcampo])){
				$data=date(dmyHis);
				$nome_file=$_FILES[$idcampo]['name'];
				$nome_file=str_replace(" ","_",$nome_file);
				$type = $_FILES[$idcampo]['type'];
				$file = $id."_".$data."_".$nome_file;
				$valore=$file;
							
				//******************				
				if ($nome_file!="") {
					$upload_dir = ALLEGATI_UTENTI;		// The directory for the images to be saved in
					$upload_path = $upload_dir."/";				// The path to where the image will be saved			
					$large_image_location = $upload_path.$file;
					$userfile_tmp = $_FILES[$idcampo]['tmp_name'];
					//echo($large_image_location);			
					move_uploaded_file($userfile_tmp, $large_image_location);
				}
				//******************				
				
				/*inserimento dell'istanza in istanza_dettaglio*/
				$query="insert into istanze_dettaglio (id_istanza_testata,idcampo,valore) values ($id_istanza,$idcampo,'$valore')";
				$rs2 = mssql_query($query, $conn);
				/*fine inserimento*/
				
				if(!$rs2){
					echo("no3");
					exit();
					die();
				} else {
					$allegato = 1;
				}
			}
		
	}
	mssql_free_result($rs1);
	// fine while 
	
	// SE E' IL MODULO TERAPIA FARMACOLOGICA INSERISCO TUTTI I FARMACI NON PRESENTI IN ANAGRAFICA
	// SE TROVO UN FARMACO CON LO STESSO NOME NON VIENE INSERITO
	// SE VENGONO INSERITI DOSAGGIO E TIPOLOGIA DI SOMMINISTRAZIONE UTILIZZO ANCHE QUESTE INFORMAZIONI PER EVITARE UN DOPPIO INSERIMENTO IN ANAGRAFICA
	if($idmodulo == 205) {
		foreach($lista_farmaci as $farmaco) {
			if(!isset($farmaco['nome']) || empty($farmaco['nome']) || trim($farmaco['nome']) == '') {
				continue;
			}
			
			$dosaggio_sql = '';
			$dosaggio = 'NULL';
			
			if(!empty($farmaco['dosaggio']) && trim($farmaco['dosaggio']) != '') {
				$dosaggio_sql = " AND dosaggio = ".$farmaco['dosaggio'];
				$dosaggio = $farmaco['dosaggio'];
			}
			
			$tip_somm_sql = '';
			$tip_somm = 'NULL';
			
			if(!empty($farmaco['tip_somm'])) {
				$tip_somm_sql = " AND tipologia_somministrazione = ".$farmaco['tip_somm'];
				$tip_somm = $farmaco['tip_somm'];
			}

			$_query = "SELECT id
						FROM farmaci 
						WHERE struttura = 0 AND deleted = 0 and id_paziente = '$idpaziente'
							AND LOWER(nome  Collate SQL_Latin1_General_CP1253_CI_AI) = LOWER(TRIM('".$farmaco['nome']."') Collate SQL_Latin1_General_CP1253_CI_AI)" .
							$dosaggio_sql .
							$tip_somm_sql;
			$_rs = mssql_query($_query, $conn);
			
			if(mssql_num_rows($_rs) == 0) {
				$_query = "INSERT INTO farmaci (nome, lotto, dosaggio, quantita_caricata, quantita_attuale, 
												giacenza_minima, tipologia_somministrazione, stato_farmaco, id_paziente, ope_ins, 
												status)
							VALUES ('".trim($farmaco['nome'])."', '', $dosaggio, 0, 0, 
									0, $tip_somm, 2, '$idpaziente', $opeins, 
									1)";
				$_rs = mssql_query($_query, $conn);
			}
		}
	}
	
	// SE E' IL MODULO SOMMINISTRAZIONE FARMACOLOGICA VADO A GESTIRE LO SCARICO DEI FARMACI SELEZIONATI
	// NEL CASO IN CUI IL FARMACO SIA DI STRUTTURA O DI UN PAZIENTE RIMUOVO L'ASSOCIAZIONE CON IL PAZIENTE CORRENTE IN QUANTO E' UNA SOMMINISTRAZIONE D'EMERGENZA
	if($idmodulo == 206) {
		foreach($lista_farmaci as $farmaco) {
			$id_farmaco = $farmaco['id'];
			$quantita = $farmaco['qnt'];
			
			$_query = "SELECT nome, quantita_attuale 
						FROM farmaci 
						WHERE id = $id_farmaco";
			$_rs = mssql_query($_query, $conn);
			$_row = mssql_fetch_assoc($_rs);
			
			if($_row['quantita_attuale'] < $quantita) {
				die();
			}
		}
		
		foreach($lista_farmaci as $farmaco) {
			$id_farmaco = $farmaco['id'];
			$quantita = $farmaco['qnt'];

			$_query = "SELECT quantita_attuale 
						FROM farmaci 
						WHERE id = $id_farmaco";
			$_rs = mssql_query($_query, $conn);
			$_row = mssql_fetch_assoc($_rs);
			$quantita_attuale = $_row['quantita_attuale'];
			mssql_free_result($_rs);

			$_query_log = "INSERT INTO farmaci_log (id_modulo_versione, progressivo_modulo, id_farmaco, id_paziente, quantita_precedente,
												quantita_scaricata, quantita_attuale, ope_ins, ip_ins)
							VALUES ($idmoduloversione_new, $maxins, $id_farmaco, $idpaziente, $quantita_attuale,
									$quantita, ($quantita_attuale - $quantita), $opeins, '$ipins')";
			mssql_query($_query_log, $conn);
			
			// se esaurisco il farmaco imposto lo stato ad esaurito
			$_sql_status = ($quantita_attuale - $quantita) > 0 ? '' : ', stato_farmaco = 3';
			
			$_query = "UPDATE farmaci 
						SET quantita_attuale = (quantita_attuale - $quantita) $_sql_status
						WHERE id = $id_farmaco";
			mssql_query($_query, $conn);
		}
		
		$_query = "DELETE FROM farmaci_struttura_pazienti 
					WHERE id_farmaco in (" . implode(',', $farmaci_struttura_da_rimuovere) . ") AND id_paziente = $idpaziente";
		$_rs = mssql_query($_query, $conn);
		
		$_query = "DELETE FROM farmaci_paziente_pazienti 
					WHERE id in (" . implode(',', $farmaci_paz_paz_da_rimuovere) . ")";
		$_rs = mssql_query($_query, $conn);		
	}
	
	// SE E' IL MODULO DI EMOTRASFUSIONI INSERISCO LE PRIME DUE FIRME DEL MODULO IN STATO ATTESO
	// IL PRIMO ALLEGATO DEL MODULO DEVE ESSERE MULTIFIRMA
	if($idmodulo == 269 && $firmadigitaledocumento == 'a') {
		$query_firma = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato) 
						values ($id_istanza, (SELECT uid FROM operatori WHERE nome = '$op_emotrasfusioni_1'), 'a')";
		$rs_firma = mssql_query($query_firma, $conn);
		
		$query_firma = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato) values ($id_istanza, $op_emotrasfusioni_2, 'a')";
		$rs_firma = mssql_query($query_firma, $conn);
	}
	
	// aggiorno il flag "compilato", se il modulo � stato pianificato con scadenza ciclica e date prefissate
	// prendendomi il primo record che trovo non compilato 
	$query_set_compilato = "UPDATE cartelle_pianificazione_date_scad_cicl 
								SET compilato = 1, 
									id_inserimento_modulo = $maxins, 
									dataagg = '$datains', 
									ipagg = '$ipins', 
									opeagg = $opeins 
								WHERE 
									id = (SELECT TOP (1) id FROM cartelle_pianificazione_date_scad_cicl 
											WHERE 
												id_paziente = $idpaziente AND 
												id_cartella = $idcartella AND 
												id_modulo_padre = $idmodulo AND 
												compilato = 0 
											ORDER BY id_cartella_pianificazione_testata DESC, data ASC
										  )";
	$result_set_compilato = mssql_query($query_set_compilato, $conn);
	

	if ($allegato == 1)	
		echo ("ok;".$idpaziente.";5;re_pazienti_sanitaria.php?do=");
	else echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella=$idcartella&idpaziente=$idpaziente&idmodulo=$idmoduloversione_new&cartella=$cartella&impegnativa=$id_impegnativa&idinserimento=$maxins&firmadigitaledocumento=$firmadigitaledocumento&fea_documento=$fea_documento&salvaeallega=$salvaeallega");
	//echo ("ok;".$idpaziente.";5;re_pazienti_sanitaria.php?do=");
	exit();
}


function create_test_clinico(){

	$datains = date('d/m/Y');
	$orains = date('H:i');
	$ipins = $_SERVER['REMOTE_ADDR'];
	$opeins = $_SESSION['UTENTE']->get_userid();
	
	$idcartella=$_POST['idcartella'];
	$idpaziente=$_POST['idpaziente'];
	$idtest=$_POST['idtest'];
	$idimpegnativa=$_POST['idimpegnativa'];
	$data_osservazione=$_POST['data_osservazione'];
	$conn = db_connect();
	$query="insert into test_clinici_compilati (idtest,idpaziente,idcartella,idimpegnativa,data_osservazione,datains,orains,ipins,opeins) values ($idtest,$idpaziente,$idcartella,$idimpegnativa,'$data_osservazione','$datains','$orains','$ipins',$opeins)";
	mssql_query($query, $conn);
	
	$query="select max (idtestclinico) as idtestclinico from test_clinici_compilati where opeins=$opeins and idtest=$idtest";
	
	$rs = mssql_query($query, $conn);
		
	if(!$rs)
	{
	echo("no");
	exit();
	die();
	}
$idtestclinico=0;
	if($row = mssql_fetch_assoc($rs))
		$idtestclinico=$row['idtestclinico'];
	//mssql_free_result($rs);	
	
	
	$query="select * from re_test_clinici_prove where idtest=$idtest";
	$rs = mssql_query($query, $conn);
		
	if(!$rs)
	{
	echo("no");
	exit();
	die();
	}

	while($row = mssql_fetch_assoc($rs))
	{
		$idprova=$row['idprova'];
		$risposta="risposta".$idprova;
		$risposta_postata=$_POST[$risposta];
		$query="insert into test_clinici_compilati_details (idtestclinico_compilato,idprova,iditem) values ($idtestclinico,$idprova,$risposta_postata)";
		mssql_query($query, $conn);
	}
	
	
echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria.php?do=");
exit();
}

function create_visita(){

	$idcartella=$_REQUEST['idcartella'];
	$idpaziente=$_REQUEST['idpaziente'];
	$idmoduloversione=$_REQUEST['idmoduloversione'];
	if($_POST["impegnativa"]=="no")	$id_impegnativa="NULL";
	if($_POST["impegnativa"]=="si")	$id_impegnativa=$_POST['idimpegnativa'];
	$conn = db_connect();
	
	$query="select idmodulo from moduli where id=$idmoduloversione";
	$rs = mssql_query($query, $conn);
		
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs))
		$idmodulo=$row['idmodulo'];
	mssql_free_result($rs);
	
	$datains = date('d/m/Y');
	$orains = date('H:i');
	$ipins = $_SERVER['REMOTE_ADDR'];
	$opeins = $_SESSION['UTENTE']->get_userid();	
	/* inserimento in testata istanze */		
	$query="insert into istanze_testata (id_modulo_padre,id_modulo_versione,id_paziente,id_cartella,id_impegnativa,data_osservazione,id_inserimento,datains,orains,opeins,ipins) values ($idmodulo,$idmoduloversione,$idpaziente,0,$id_impegnativa,NULL,1,'$datains','$orains',$opeins,'$ipins')";
	$rs2 = mssql_query($query, $conn);
	
	$query="SELECT MAX(id_istanza_testata) AS id_istanza, opeins FROM dbo.istanze_testata GROUP BY opeins HAVING (opeins = $opeins)";
	$rs2 = mssql_query($query, $conn);
	$row2 = mssql_fetch_assoc($rs2);
	$id_istanza=$row2['id_istanza'];	
	/* fine inserimento */	

	$query="select * from campi where idmoduloversione=$idmoduloversione order by peso asc";	

	$rs1 = mssql_query($query, $conn);
	if(!$rs1) error_message(mssql_error());

	while($row1 = mssql_fetch_assoc($rs1))
	{
		$idcampo= $row1['idcampo'];
		if (isset($_POST[$idcampo]))
		{
			$valore=$_POST[$idcampo];
			$valore=pulisci($valore);
			
			/*inserimento dell'istanza in istanza_dettaglio*/
			$query="insert into istanze_dettaglio (id_istanza_testata,idcampo,valore) values ($id_istanza,$idcampo,'$valore')";
			$rs2 = mssql_query($query, $conn);
			/*fine inserimento*/
			if(!$rs2){
				echo("no2");
				exit();
				die();
			}
			if($row1['tipo']==6){				
				/*aggiornamento istanze_testata con data osservazione*/
				$query="update istanze_testata set data_osservazione='$valore' where id_istanza_testata=$id_istanza";
				//echo($query);
				$rs2 = mssql_query($query, $conn);
				/*fine aggiornamento*/
				
			}	
		}
		elseif(isset($_FILES[$idcampo])){
						$data=date(dmyHis);
						$nome_file=$_FILES[$idcampo]['name'];
						$nome_file=str_replace(" ","_",$nome_file);
						$type = $_FILES[$idcampo]['type'];
						$file = $id."_".$data."_".$nome_file;
						$valore=$file;
									
						//******************				
						if ($nome_file!="") {
							$upload_dir = ALLEGATI_UTENTI;		// The directory for the images to be saved in
							$upload_path = $upload_dir."/";				// The path to where the image will be saved			
							$large_image_location = $upload_path.$file;
							$userfile_tmp = $_FILES[$idcampo]['tmp_name'];
							//echo($large_image_location);			
							move_uploaded_file($userfile_tmp, $large_image_location);
						}
						//******************				
						
						/*inserimento dell'istanza in istanza_dettaglio*/
						$query="insert into istanze_dettaglio (id_istanza_testata,idcampo,valore) values ($id_istanza,$idcampo,'$valore')";
						$rs2 = mssql_query($query, $conn);
						/*fine inserimento*/
						
						if(!$rs2){
							echo("no3");
							exit();
							die();
						}
					}		
	}
	mssql_free_result($rs1);

echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria.php?do=");
exit();
}


function anteprima_istanze_modulo(){

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];
$idimpegnativa=$_REQUEST['impegnativa'];

$conn = db_connect();
$query="select id,idmodulo,nome,replica from moduli where id=$idmoduloversione";
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$nome_modulo=$row1['nome'];
$id_modulo_padre=$row1['idmodulo'];
mssql_free_result($rs1);


if (($idcartella>0) and ($idimpegnativa!=""))
	$query="select * from re_anteprima_moduli_imp where id_cartella=$idcartella and id_modulo_padre=$id_modulo_padre and id_impegnativa=$idimpegnativa";	
	elseif(($idcartella>0) and ($impegnativa==""))
	$query="select * from re_anteprima_moduli WHERE (id_cartella = $idcartella) AND (id_modulo_padre=$id_modulo_padre) ORDER BY id_inserimento, peso";		
	else
	$query="select * from re_istanze_moduli where id_cartella=0 and id_modulo_padre=$id_modulo_padre and idpaziente=$idpaziente";

	//echo($query);

$rs1 = mssql_query($query, $conn);
//$row1=mssql_fetch_assoc($rs1);

?>
<script>inizializza();</script>
<div id="sanitaria">
<div class="titoloalternativo">
            <h1>cartella: <?=$cartella?></h1>
	        <!--<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->			
</div>
<div class="titoloalternativo">
            <h1>modulo: <?=$nome_modulo?></h1>
</div>


<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','sanitaria')" >ritorna alla lista dei moduli</a></div>		
	</div>
</div>

<?
		
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
			
		$datains="";
		$orains="";
		$idinserimento="";
		$operatore="";
		$IST="";
		while($row1 = mssql_fetch_assoc($rs1)){
			
		if (($row1['opeins']==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($row1['id_modulo_padre'],$_SESSION['UTENTE']->get_gid()))){
			
	if ($idinserimento!=$row1['id_inserimento']) {
		if($div==1) echo("</div>");
		if($IST==1){
			$IST="";
			echo("</div><div class=\"titolo_pag\"><div class=\"comandi\"></div></div></form>");
		}
		$datains=formatta_data($row1['datains']);
		$orains=$row1['orains'];
		$idinserimento=$row1['id_inserimento'];
		$operatore=$row1['nome'];
		//$nome_modulo=$row1['nome_modulo'];
		$IST=1;
		$i=1;
		$zz=1;
		$div=0;	
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
			$creazione="il: ".$datains." ore: ".$orains;		
		}else{
			$creazione="";
		}		
		?>
			<div class="titolo_pag"><h1>modulo: <?=$nome_modulo?> - creato da: <?=$operatore?> <?=$creazione?></h1></div>	
			
		<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
			
			<div class="nomandatory">
				<input type="hidden" name="nomandatory" value="1" />
			</div>

			<input type="hidden" name="action" value="update_modulo" />
			<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
			<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
			<input type="hidden" name="idinserimento" value="<?=$idinserimento?>" />
			<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
			
			
			<div class="blocco_centralcat">
		<?}
		if($zz==1){		
		if (($idimpegnativa) and($idimpegnativa!=""))
		$query="select * from re_anteprima_moduli where (id_impegnativa=$idimpegnativa) and id_inserimento=$idinserimento and id_cartella=$idcartella and id_modulo_padre=$id_modulo_padre";
		else
		$query="select * from re_anteprima_moduli where (id_impegnativa IS NOT NULL) and id_inserimento=$idinserimento and id_cartella=$idcartella and id_modulo_padre=$id_modulo_padre";
		//echo($query);
		$rs11 = mssql_query($query, $conn);
		if($row11 = mssql_fetch_assoc($rs11)){
		?>
		 <div class="rigo_mask">
		 <div class="testo_mask">impegnativa</div>            
			<div class="campo_mask "><? 
			
		;
			
				$query = "SELECT * from re_pazienti_impegnative where idimpegnativa=".$row11['id_impegnativa'];	
				$rs_q = mssql_query($query, $conn);				
				if($row_q = mssql_fetch_assoc($rs_q)){						
					$data_auth_asl=formatta_data($row_q['DataAutorizAsl']);
					$prot_auth_asl=$row_q['ProtAutorizAsl'];
					$regime=$row_q['regime'];
				}
				echo($prot_auth_asl." ".$data_auth_asl." - ".$regime);?>
			</div>
			</div><?		
		}
		mssql_free_result($rs11);
		}

			$idvalore=$row1['id_istanza_dettaglio'];
			$idcampo= $row1['idcampo'];
			$etichetta= pulisci_lettura($row1['etichetta']);
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
			
			$query_val="select valore from istanze_dettaglio WHERE (id_istanza_dettaglio = $idvalore)";
			$rs_val = mssql_query($query_val, $conn);
			if(!$rs_val) error_message(mssql_error());
			$row_val = mssql_fetch_assoc($rs_val);
			$valore=pulisci_lettura($row_val['valore']);	
		
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
		?>
		
		<? if ($tipo==5) {?>			
			<div class="rigo_big" style="float:left;">
			<div class="etichetta_campi"><?=$etichetta?></div>			
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if(($multi==1)or($multi==2)) echo($stile);?>> 
            <div class="testo_mask"><?=$etichetta?></div>            
			<div class="campo_mask ">
                <?if(($tipo==1)or($tipo==3)or($tipo==6)or($tipo==8)){
					echo($valore);
				  }
				elseif($tipo==7)
{
?>
						<div id="file_name" style="display:block;"><a target="_new" href="load_file.php?filename=<?=$valore?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>" style="text-decoration:none;" ><img src="images/view.png" /> <?=$valore?></a>
						<input type="hidden" class="scrittura" name="<?=$idcampo?>_file" value="<?=$valore?>" >		
						</div>
                        <div id="file_box" class="campo_mask nomandatory" style="display:none">
                            <input type="file" class="scrittura_campo" name="<?=$idcampo?>" >
                        </div>	

<?
}				
				elseif($tipo==2){
				if ($idvalore!=""){
					// recupera il dato
					$db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
					$res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
					$res = odbc_exec ($db, "SELECT valore FROM istanze_dettaglio WHERE id_istanza_dettaglio=$idvalore");
					//odbc_longreadlen ($res, MAX);
					$text = str_replace("?","'",pulisci_lettura(odbc_result ($res, "valore")));
					$text = str_replace("\n","<br>",$text);
					$valore = $text;
					odbc_close($db);
				}
					echo($valore);							
				}
				  elseif($tipo==4)
					 {
						$query2="select * from re_moduli_combo where idcampo=$idcampo order by peso";	
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2) error_message(mssql_error());
					 if(($multi==1)or($multi==2)){
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								$separatore=$row2['separatore'];
								switch ($separatore){
									case 0:
										$sep="<br/>";
										break;
									case 1:
										$sep=" - ";
										break;
									case 2:
										$sep=" , ";
										break;
								}
								if($multi==2){
									if (in_array($valore1,$valore)) 
										$et.="[X] ".$etichetta.$sep;
										else
										$et.="[&nbsp;&nbsp;] ".$etichetta.$sep;	
								}
								if($multi==1) if (in_array($valore1,$valore)) $et.=$etichetta.$sep;										
							}
						$et=substr($et,0,strlen($et)-2);
						echo(pulisci_lettura($et));
					 }else{				
					while ($row2 = mssql_fetch_assoc($rs2))
							{								
								if ($row2['valore']==$valore) echo($row2['etichetta']);									
							}					
				   }
				  }
				  elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";		
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								if (in_array($valore1,$valore)) $et.=$etichetta."<br/>";									
							}
						$et=substr($et,0,strlen($et)-2);						
						echo(pulisci_lettura($et));
					 }else{	
									 
					while ($row2 = mssql_fetch_assoc($rs2)){
							
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore1=$row2['valore'];
						$etichetta1=$row2['etichetta'];
						$v=(int)($valore);
						if($v==$valore1) 
							echo($etichetta1);
							else
						$rinc=rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
					}				
				   }
				  }				  
				  ?>
            </div>
			<?}?>
		</div>
	<?
	$zz++;
	if(($tipo==2)or($multi==1)or($multi==2)) $i=0;
	if(($i%3==0)and($tipo!=5)){ 
		echo("</div>");
		$div=0;}
	if($tipo!=5) $i++;		
	}
	}
	if($div==1) echo("</div>");			
	if($IST==1){
		$IST="";?>	
		</div>
<div class="titolo_pag">
		<div class="comandi">			
		</div>
	</div>
	</form>
	<?}?>
</div>
<script type="text/javascript" id="js">
function ritorna(idcartella,idpaziente,idmodulo,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_cartella&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
</script>
<?
}

function anteprima_istanze_cartella(){

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];

srand((double)microtime()*1000000);  
$random=rand(0,999999999999999);
$filename_tmp = "tmp_cartella_".$random."_".$idcartella.".txt";
$destination_path =MODELLI_WORD_DEST_PATH;	
?>
<script>inizializza();</script>
<!-- inizio anteprima -->
<div id="sanitaria">
<div class="titoloalternativo">
            <h1>cartella: <?=$cartella?></h1>	       
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="";$xml="";$doc="";
			$pdf="pdfclass/stampa_cartella.php?file=$filename_tmp&idcart=$idcartella&user=".$_SESSION['UTENTE']->get_userid();
			include_once('include_stampa.php');
			?>
</div>

<?
$conn = db_connect();
$query="select id, idregime, codice_cartella, versione from utenti_cartelle where id=$idcartella";
$rs1 = mssql_query($query, $conn);
if($row=mssql_fetch_assoc($rs1)){ 
	$id_regime=$row['idregime'];
	$cod_cartella=$row['codice_cartella'];
	$versione=$row['versione'];
}
mssql_free_result($rs1);

$query="SELECT re_cartelle_pianificazione_anteprima.* FROM re_cartelle_pianificazione_anteprima 
		WHERE re_cartelle_pianificazione_anteprima.id_cartella=$idcartella ORDER BY re_cartelle_pianificazione_anteprima.raggruppamento DESC, re_cartelle_pianificazione_anteprima.id_pianificazione ASC";

		$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
?>
<table id="table" class="tablesorter" cellspacing="1"> 
	<thead> 
		<tr> 
			<th style="width:10%">codice sgq</th>
			<th>prot.asl</th> 	
			<th>modulo</th> 
			<th>figura responsabile</th>			
			<th style="width:10%">ultima compilazione</th> 
			<th>vai a</th>			
		</tr> 
</thead> 
<tbody> 
<?
$content="";
	while($row = mssql_fetch_assoc($rs))   
	{
		$idmodulo_id=$row['idmodulo_id'];
		$idmodulo=$row['id_modulo_padre'];
		$codice=$row['codice'];
		$prot_asl=$row['prot_asl'];
		$data_asl=$row['data_asl'];
		$nome=$row['nome'];
		$scadenza=$row['scadenza'];
		$ultima_compilazione=formatta_data($row['ultima_compilazione']);
		$data_osservazione=$row['data_osservazione'];		
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
			$ultima_compilazione=formatta_data($row['ultima_compilazione']);		
		}else{
			$ultima_compilazione=formatta_data($data_osservazione);
		}
		$nome_medico=$row['nome_medico'];		
		$obbligatorio=$row['obbligatorio'];
		$img="";
		if ($obbligatorio=='s')
			$img="spunto.png";
		if($prot_asl!="") 
			$str_asl=$prot_asl." del ".formatta_data($data_asl);
			else
			$str_asl="";
	?>
	<tr> 
	 <td><?=$codice?></td>
	 <td><?=$str_asl?></td>
	 <td><?=$nome?></td>
	 <td><?=$nome_medico?></td>
	 <td><?=$ultima_compilazione?></td> 
	 <? if ($ultima_compilazione!=''){?>
	  <td><a href="#<?=$idmodulo?>"><img src="images/view.png" /></a></td>	  
	  <?}
	  else{
		?>
	  <td></a></td>					
	  <?}?>
	</tr> 	
	<?	
	}
	mssql_free_result($rs);
?>
</tbody>
</table>
<?
$content="";
if ($idcartella>0)
$query="select * from re_anteprima_moduli WHERE ((id_cartella = $idcartella) AND (raggruppamento = 'modulo')) ORDER BY raggruppamento DESC, id_impegnativa, id_modulo_padre, id_inserimento, id, peso";		
else
$query="select * from re_instanze_moduli where idcartella=0 and idmodulo=$id_modulo_padre and idpaziente=$idpaziente";
//echo($query);
$rs1 = mssql_query($query, $conn);

$MOD="0";
while($row1 = mssql_fetch_assoc($rs1)){
	
	if (($row1['opeins']==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($row1['id_modulo_padre'],$_SESSION['UTENTE']->get_gid()))){
	
	if ($MOD!=$row1['id_modulo_padre']) {
		if(($MOD!=$row1['id_modulo_padre'])and($MOD!="")){
			echo("$dati_creato</div></div>");
			if($txt_dati_valori!="") $content.=$txt_dati_testata.$txt_dati_creazione.$txt_dati_impegnativa.$txt_dati_valori."#NP\n";
			$txt_dati_valori="";
			$txt_dati_impegnativa="";
		}		
		$MOD=$row1['id_modulo_padre'];		
		$query="select id,idmodulo,nome,replica from moduli where id=".$row1['id'];
		$rs2 = mssql_query($query, $conn);
		$row2=mssql_fetch_assoc($rs2);
		$nome_modulo=$row2['nome'];
		$id_modulo_padre=$row2['idmodulo'];		
		mssql_free_result($rs2);		
	?>
<div class="basic_version">
<div class="titoloalternativo" id="<?=$MOD?>">
            <h1>modulo: <?=$nome_modulo?></h1>
			<?$txt_dati_testata="1<".strtoupper($nome_modulo).">\n";?>
</div>


<div class="titolo_pag">
	<div class="comandi">				
	</div>
</div>

<?		
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		
	
		$datains="";
		$orains="";
		$operatore="";
		$idinserimento="";
		$IST=0;		
	}			
	if ($idinserimento!=$row1['id_inserimento']) {		
		if($IST) echo($dati_creato);		
		if($div==1) echo("</div>");
		if($IST==1){
			$IST=0;
			echo("</div><div class=\"titolo_pag\"><div class=\"comandi\"></div></div></form>");
			$content.=$txt_dati_creazione.$txt_dati_impegnativa.$txt_dati_valori."#NP\n";
			$txt_dati_valori="";
			$txt_dati_impegnativa="";	
			}
		$datains=formatta_data($row1['datains']);
		$orains=$row1['orains'];
		$operatore=$row1['nome'];
		$nome_mod=$row1['nome_modulo'];
		$idinserimento=$row1['id_inserimento'];
		$idimpegnativa=$row1['id_impegnativa'];
		$data_oss=formatta_data($row1['data_osservazione']);
		if($data_oss=="")$data_oss=$datains;
		$IST=1;
		$i=1;
		$div=0;
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
			$dati_creato="<div class='etichetta_campi etichetta_campi_autore'>creato da $operatore il $datains alle ore $orains</div>";
			$txt_dati_creazione="\n2<del $data_oss effettuato da $operatore>\n\n";
		}else{
			$dati_creato="";
			$txt_dati_creazione="";
		}
		?>
			
		<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
			
			<div class="nomandatory">
				<input type="hidden" name="nomandatory" value="1" />
			</div>

			<input type="hidden" name="action" value="update_modulo" />
			<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
			<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
			<input type="hidden" name="idinserimento" value="<?=$idinserimento?>" />
			<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
			
			
			<div class="blocco_centralcat"><?			
			if($idimpegnativa!=Null){
			?>
			 <div class="rigo_mask">
			 <div class="testo_mask">impegnativa</div>            
				<div class="campo_mask "><? 
					$query = "SELECT * from impegnative where idimpegnativa=$idimpegnativa";	
					$rs_q = mssql_query($query, $conn);				
					if($row_q = mssql_fetch_assoc($rs_q)){						
						$data_auth_asl=formatta_data($row_q['DataAutorizAsl']);
						$prot_auth_asl=$row_q['ProtAutorizAsl'];
						$regime=$row_q['regime'];
					}
					echo(" n. ".$prot_auth_asl." del ".$data_auth_asl);
					$txt_dati_impegnativa="<b>impegnativa</b>\n".$prot_auth_asl." ".$data_auth_asl."\n\n";?>
				</div>
				</div><?		
			}			
		}
		
			$id_istanza_dettaglio=$row1['id_istanza_dettaglio'];
			$idcampo= $row1['idcampo'];
			$etichetta= $row1['etichetta'];
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
			
			$query_val="select valore from istanze_dettaglio WHERE (id_istanza_dettaglio = $id_istanza_dettaglio)";			
			$rs_val = mssql_query($query_val, $conn);
			if(!$rs_val) error_message(mssql_error());
			$row_val = mssql_fetch_assoc($rs_val);
			$valore=$row_val['valore'];
		
			if ($obbligatorio=='y')
			$classe="mandatory ".$classe;
		
		$multi=0;
		$multi_ram=0;
		if($tipo==4){
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo ";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi=$row3['multi'];
			mssql_free_result($rs3);
			}
			
		elseif($tipo==9){
			$query3="select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo ";	
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
		?>
		
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<div class="etichetta_campi"><?=pulisci_lettura($etichetta)?></div>			
			<?
			$txt_dati_valori.="<b><i>".pulisci_lettura($etichetta)."</i></b>\n\n";
			} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if(($multi==1)or($multi==2)) echo($stile);?>> 
            <div class="testo_mask"><?=pulisci_lettura($etichetta)?></div>            
			<div class="campo_mask ">
                <?
				$txt_dati_valori.="<b>".pulisci_lettura($etichetta)."</b>\n";
				if(($tipo==1)or($tipo==3)or($tipo==6)or($tipo==8)){
					echo(pulisci_lettura($valore));
					$txt_dati_valori.=pulisci_lettura($valore)."\n\n";
				  }
				elseif($tipo==7){
					echo("<a href='allegati_utenti/".pulisci_lettura($valore)."' target='_blank'>".pulisci_lettura($valore)."</a>");
					$txt_dati_valori.=pulisci_lettura($valore)."\n\n";
				}
				elseif($tipo==2){
				if ($idvalore!=""){
					// recupera il dato
					$db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
					$res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
					$res = odbc_exec ($db, "SELECT valore FROM istanze_dettaglio WHERE id_istanza_dettaglio=$idvalore");
					//odbc_longreadlen ($res, MAX);
					$text = str_replace("?","'",pulisci_lettura(odbc_result ($res, "valore")));
					$text = str_replace("\n","<br>",$text);
					$valore = $text;
					odbc_close($db);
				}
					echo(pulisci_lettura($valore));
					$txt_dati_valori.=$valore."\n\n";	
				}
				  elseif($tipo==4)
					 {
						$query2="select * from re_moduli_combo where idcampo=$idcampo order by peso";	
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2) error_message(mssql_error());
					 if(($multi==1)or($multi==2)){
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								$separatore=$row2['separatore'];
								switch ($separatore){
									case 0:
										$sep="<br/>";
										break;
									case 1:
										$sep=" - ";
										break;
									case 2:
										$sep=" , ";
										break;
								}
								if($multi==2){
									if (in_array($valore1,$valore)) 
										$et.="[X] ".$etichetta.$sep;
										else
										$et.="[&nbsp;&nbsp;] ".$etichetta.$sep;	
								}
								if($multi==1) if (in_array($valore1,$valore)) $et.=$etichetta.$sep;										
							}
						$et=substr($et,0,strlen($et)-2);
						echo(pulisci_lettura($et));
						$txt_dati_valori.=pulisci_lettura($et)."\n\n";
					 }else{
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								if ($row2['valore']==$valore) {
									echo(pulisci_lettura($row2['etichetta']));
									$txt_dati_valori.=$row2['etichetta']."\n";
								}
							}					
				   }
				  }
				   elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";		
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								if (in_array($valore1,$valore)) $et.=$etichetta."<br/>";									
							}
						$et=substr($et,0,strlen($et)-2);						
						echo(pulisci_lettura($et));
						$txt_dati_valori.=pulisci_lettura($et)."\n\n";
					 }else{	
									 
					while ($row2 = mssql_fetch_assoc($rs2))
							{
							
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore1=$row2['valore'];
						$etichetta1=$row2['etichetta'];
						$v=(int)($valore);
						if($v==$valore1){ 
							echo($etichetta1);
							$txt_dati_valori.=pulisci_lettura($etichetta1)."\n\n";
							}
							else
						$rinc=rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
					}					
				   }
				  }
				  ?>
            </div>		
			<?}?>				
		</div>	<?		
	if(($tipo==2)or($multi==1)or($multi==2)) $i=0;
	if(($i%3==0)and($tipo!=5)){  
		echo("</div>");
		$div=0;}
	if($tipo!=5) $i++;	
	}		
}
$content.=$txt_dati_testata.$txt_dati_creazione.$txt_dati_impegnativa.$txt_dati_valori;
$content=str_replace("<br/>","\n",$content);
$content=str_replace("<br","",$content);
$handle = fopen($destination_path.$filename_tmp, "w");
fwrite($handle,$content);

if ($idcartella>0)
$query="select * from re_anteprima_moduli_cronologia WHERE ((id_cartella = $idcartella)) ORDER BY data_osservazione ASC, id_modulo_padre, id_inserimento, id, peso";		
else
$query="select * from re_instanze_moduli_cronologia where id_cartella=0 and idmodulo=$id_modulo_padre ORDER BY data_osservazione ASC";

$rs1 = mssql_query($query, $conn);

$MOD="0";
while($row1 = mssql_fetch_assoc($rs1)){
	
	if (($row1['opeins']==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($row1['id_modulo_padre'],$_SESSION['UTENTE']->get_gid()))){
	
	if ($MOD!=$row1['idmodulo']) {
		if(($MOD!=$row1['idmodulo'])and($MOD!="")) echo("$dati_creato</div></div>");
		$MOD=$row1['idmodulo'];		
		$query="select id,idmodulo,nome,replica from moduli where id=".$row1['id'];
		$rs2 = mssql_query($query, $conn);
		$row2=mssql_fetch_assoc($rs2);
		$nome_modulo=$row2['nome'];
		$id_modulo_padre=$row2['idmodulo'];		
		mssql_free_result($rs2);
		
	?>
<div class="titoloalternativo" id="<?=$MOD?>">
            <h1>modulo: <?=$nome_modulo?></h1>
</div>


<div class="titolo_pag">
	<div class="comandi">		
		<!--<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_cartelle('<?=$idpaziente?>','cartella_clinica');">elenco cartelle</a></div>		-->
	</div>
</div>

<?
		
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		
	
		$datains="";
		$orains="";
		$operatore="";
		$idinserimento="";
		$IST="";
	}		

	if ($idinserimento!=$row1['id_inserimento']) {
		//if($div==1) echo("$dati_creato</div>");
		if($IST==1){
			$IST="";
			echo("</div><div class=\"titolo_pag\"><div class=\"comandi\"></div></div></form>");
		}
		$datains=formatta_data($row1['datains']);
		$orains=$row1['orains'];
		$operatore=$row1['nome'];
		$nome_mod=$row1['nome_modulo'];
		$idinserimento=$row1['id_inserimento'];
		$IST=1;
		$i=1;
		$div=0;
		?>
			<!--<div class="titolo_pag"><h1>modulo: <?=$nome_mod?></h1></div>	-->
			
		<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
			
			<div class="nomandatory">
				<input type="hidden" name="nomandatory" value="1" />
			</div>

			<input type="hidden" name="action" value="update_modulo" />
			<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
			<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
			<input type="hidden" name="idinserimento" value="<?=$idinserimento?>" />
			<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
			
			
			<div class="blocco_centralcat">
		<?}
		
			$idvalore=$row1['id_istanza_dettaglio'];
			$idcampo= $row1['idcampo'];
			$etichetta= $row1['etichetta'];
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
			
			$query_val="select valore from istanze_dettaglio WHERE (id_istanza_dettaglio= $idvalore)";
			$rs_val = mssql_query($query_val, $conn);
			if(!$rs_val) error_message(mssql_error());
			$row_val = mssql_fetch_assoc($rs_val);
			$valore=$row_val['valore'];	
		
			if ($obbligatorio=='y')
			$classe="mandatory ".$classe;
		
		$multi=0;
		$multi_ram=0;
		if($tipo==4){
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo ";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi=$row3['multi'];
			mssql_free_result($rs3);
			}
			
		elseif($tipo==9){
			$query3="select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo ";	
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
		?>
		
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<div class="etichetta_campi"><?=$etichetta?> creato da: <?=$operatore?> il: <?=$datains?> ore: <?=$orains?></div>			
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if(($multi==1)or($multi==2)) echo($stile);?>> 
            <div class="testo_mask"><?=pulisci_lettura($etichetta)?></div>            
			<div class="campo_mask ">
                <?if(($tipo==1)or($tipo==3)or($tipo==6)or($tipo==8)){
					echo(pulisci_lettura($valore));
				  }
				elseif($tipo==2){
				if ($idvalore!=""){
					// recupera il dato
					$db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
					$res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
					$res = odbc_exec ($db, "SELECT valore FROM istanze_dettaglio WHERE id_istanza_dettaglio=$idvalore");
					//odbc_longreadlen ($res, MAX);
					$text = str_replace("?","'",pulisci_lettura(odbc_result ($res, "valore")));
					$text = str_replace("\n","<br>",$text);
					$valore = $text;
					odbc_close($db);
				}
					echo(pulisci_lettura("<br/><br/>".$valore));							
				}
				  elseif($tipo==4)
					 {
						$query2="select * from re_moduli_combo where idcampo=$idcampo order by peso";	
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2) error_message(mssql_error());
					 if(($multi==1)or($multi==2)){
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								$separatore=$row2['separatore'];
								switch ($separatore){
									case 0:
										$sep="<br/>";
										break;
									case 1:
										$sep=" - ";
										break;
									case 2:
										$sep=" , ";
										break;
								}
								if (in_array($valore1,$valore)) $et.=$etichetta.$sep;									
							}
						$et=substr($et,0,strlen($et)-2);
						echo(pulisci_lettura($et));
					 }else{
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								if ($row2['valore']==$valore) echo(pulisci_lettura($row2['etichetta']));
							}					
				   }
				  }?>
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
}


	if($div==1) echo("</div>");				
	if($IST==1){
		$IST="";
		echo($dati_creato);?>	
		</div>
<div class="titolo_pag">
		<div class="comandi">			
		</div>
	</div>
	</form>
	<?}?>
</div>
</div><!-- chiusura basic version -->
<script type="text/javascript" id="js">

	function ritorna_cartelle(idpaziente,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria.php?do=&id="+idpaziente;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	 
	}
</script>
<?
}

function anteprima_istanze_cartella_ins(){

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];

?>
<script>inizializza();</script>
<div id="sanitaria">
<div class="titoloalternativo">
            <h1>cartella: <?=$cartella?></h1>
	        <!--div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div-->
			<div class="stampa"><a href="re_pazienti_sanitaria_POST.php?do=stampa_istanze_cartella_ins&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&cartella=<?=$cartella?>" target="new"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>

</div>

<?

$conn = db_connect();
$query="select id, idregime, codice_cartella, versione from utenti_cartelle where id=$idcartella";
$rs1 = mssql_query($query, $conn);
if($row=mssql_fetch_assoc($rs1)){ 
	$id_regime=$row['idregime'];
	$cod_cartella=$row['codice_cartella'];
	$versione=$row['versione'];
}
mssql_free_result($rs1);

$query="SELECT re_cartelle_pianificazione_anteprima.* FROM re_cartelle_pianificazione_anteprima 
		WHERE re_cartelle_pianificazione_anteprima.id_cartella=$idcartella ORDER BY raggruppamento, id_pianificazione";
//echo($query);
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
?>
<table id="table" class="tablesorter" cellspacing="1"> 
	<thead> 
		<tr> 
			<th style="width:10%">codice sgq</th> 
			<th>prot. asl</th> 
			<th>modulo</th> 
			<th>figura responsabile</th> 
			<!--<th>scadenza</th> -->
			<th style="width:10%">ultima compilazione</th> 
			<!--<th>vai a</th>			-->
		</tr> 
</thead> 
<tbody> 
<?

	while($row = mssql_fetch_assoc($rs))   
	{
		$idmodulo_id=$row['idmodulo_id'];
		$idmodulo=$row['id_modulo_padre'];
		$codice=$row['codice'];
		$prot_asl=$row['prot_asl'];
		$data_asl=$row['data_asl'];
		$nome=$row['nome'];
		$nome_medico=$row['nome_medico'];
		$scadenza=$row['scadenza'];
		$data_osservazione=$row['data_osservazione'];
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
			$ultima_compilazione=formatta_data($row['ultima_compilazione']);		
		}else{
			$ultima_compilazione=formatta_data($data_osservazione);
		}	
		$obbligatorio=$row['obbligatorio'];
		$img="";
		if ($obbligatorio=='s')
			$img="spunto.png";
		$u_c="";
		if($prot_asl!="") 
			$str_asl=$prot_asl." del ".formatta_data($data_asl);
			else
			$str_asl="";
	?>
	<tr> 
	 <td><?=$codice?></td>
	 <td><?=$str_asl?></td>
	 <td><?=$nome?></td>
	 <td><?=$nome_medico?></td>	  
	 <td><?=$ultima_compilazione?></td> 	
	</tr>
	<?
	}
	mssql_free_result($rs);
?>
</tbody>
</table>

<?
if ($idcartella>0)
$query="select * from re_anteprima_moduli WHERE (id_cartella = $idcartella) ORDER BY data_osservazione DESC, datains DESC, orains DESC, raggruppamento  DESC";		
else
$query="select * from re_instanze_moduli WHERE id_cartella=0 and id_modulo_padre=$id_modulo_padre and idpaziente=$idpaziente";
//echo($query);
$rs_12 = mssql_query($query, $conn);
?>
<table id="table" class="tablesorter" cellspacing="1"> 
	<thead> 
		<tr><? 			
			if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){?>
				<th>data creaz.</th>	
		<?}?>
			<!--<th>ora creaz.</th>-->
			<th>data osservazione</th> 			
			<th>operatore</th>
			<th>modulo (ver)</th>
			<td>esplora</td> 			
			<td>modifica</td>
			<td>stampa</td>
		</tr> 
</thead> 
<tbody> 
	
<?
//echo($query.$rs1);
	$vers=0;
	$idMod_vers="";
	$idMod="";
	$idIns="";
	$div="sanitaria";
	while($row = mssql_fetch_assoc($rs_12)) {		
		
		$idmodulo=$row['id_modulo_padre'];
		$nome_modulo=$row['nome_modulo'];
		$datains=formatta_data($row['datains'])." ".$row['orains'];
		$orains=$row['orains'];
		$operatore=$row['nome'];
		$idoperatore=$row['opeins'];
		$idinserimento=$row['id_inserimento'];
		$idmoduloversione=$row['idmoduloversione'];
		$datasistema=$row['data_osservazione'];
		$idimpegnativa=$row['id_impegnativa'];
		if(($idMod!=$idmodulo)or(($idMod==$idmodulo)and($idIns!=$idinserimento))){
			//if($idMod!=$idmodulo) $vers=0;
			$vers=1;
			$stop=0;
			$idMod=$idmodulo;
			$idIns=$idinserimento;			
			
			/*$query_v="SELECT id, idmodulo, nome FROM dbo.moduli WHERE (idmodulo = $idmodulo) ORDER BY id asc";
			//echo($query_v." ".$idmoduloversione." ".$vers);	
			$rs_v = mssql_query($query_v, $conn);
			while($row_v = mssql_fetch_assoc($rs_v)) {				
				if(!$stop){
				if($row_v['id']!=$idmoduloversione) 
					$vers++;
					else
					$stop=1;
				}
			}*/
			$query_v="SELECT id, idmodulo, versione FROM dbo.moduli WHERE (idmodulo = $idmodulo) and (id=$idmoduloversione) ORDER BY id asc";		
			$rs_v = mssql_query($query_v, $conn);
			$row_v = mssql_fetch_assoc($rs_v);
			$vers=$row_v['versione'];
			mssql_free_result($rs_v);
			//if($idMod_vers!=$idmoduloversione){			
			//	$idMod_vers=$idmoduloversione;			
			//	$vers++;			
			//	}
		
?>			<tr> 
			<?
			if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){?>
			<td><?=$datains?></td> 
			<?}?>
			<!--<td><?=$orains?></td>-->
			<td><?=formatta_data($datasistema)?></td>
			<td><?=$operatore?></td>
			<td id="<?=$idmodulo?>"><?php echo($nome_modulo." (vers. ".$vers.")");?></td>
			  
			  <?					  
			$query="select idmodulo,opeins from moduli where id=$idmoduloversione";
			$rs1 = mssql_query($query, $conn);
				
			if(!$rs1) error_message(mssql_error());

			if($row1 = mssql_fetch_assoc($rs1))
			{
				$idmodulo1=$row1['idmodulo'];
				$replica=$row1['replica'];
				//$opeins=$row1['opeins'];
			}
			$query="select * from re_istanze_moduli where id_modulo_padre=$idmodulo1 and id_cartella=$idcartella and id_inserimento=$idinserimento";
			$rs2 = mssql_query($query, $conn);
				
			if(!$rs2) error_message(mssql_error());

			if($row2 = mssql_fetch_assoc($rs2))
			{
				$opeins=$row2['opeins'];					
			}					
			mssql_free_result($rs2);
			
				?><td>
					<?							
				if ( ($opeins==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($idmodulo1,$_SESSION['UTENTE']->get_gid())))
				{?> 
				
				<a href="#"  onclick="javascript:visualizza_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','ins','<?=$idimpegnativa?>')" ><img src="images/view.png" /></a>
				<? }else {
				 echo("&nbsp;");
				}
				?>
				</td>						
				<td>
				<?
				
				if ( ($opeins==$_SESSION['UTENTE']->get_userid()) and  (get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid())))
				{?> 
				<a href="#"  onclick="javascript:edita_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idimpegnativa?>')" ><img src="images/gear.png" /></a>
				<? }else {
				echo("&nbsp;");
				}?>
				
			  </td> 
			  <td>
				<?				
				$io=strpos($row['modello_word'],".");				
				if ((($opeins==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($idmodulo1,$_SESSION['UTENTE']->get_gid())))and($io!=""))
				{?> 				      	
				<a href="stampa_modulo.php?idcartella=<?=$idcartella?>&idinserimento=<?=$idinserimento?>&idmodulopadre=<?=$idmodulo1?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>"><img src="images/printer.png" /></a>
				<? }else {
				echo("&nbsp;");
				}?>                    	
			  </td> 
			</tr>					
		<?
		}
	}
?>
</tbody>
</table>
<?


	if($div==1) echo("</div>");				
	if($IST==1){
		$IST="";?>	
		</div>
<div class="titolo_pag">
		<div class="comandi">			
		</div>
	</div>
	</form>
	<?}?>
</div>
<script type="text/javascript" id="js">
	 $("table").tablesorter({widthFixed: true, widgets: ['zebra']});

	function ritorna_cartelle(idpaziente,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria.php?do=&id="+idpaziente;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	 
	}
	function visualizza_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,call,impegnativa)
	{
	div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_modulo&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&call="+call+"&impegnativa="+impegnativa;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
</script>
<?
}


function visualizza_istanze_modulo()
{
$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idregime=$_REQUEST['idregime'];
$idmoduloversione=$_REQUEST['idmodulo'];
$cartella=$_REQUEST['cartella'];
$impegnativa=$_REQUEST['impegnativa'];
$idultimoinserimento = $_REQUEST['idinserimento'];
$firmadigitaledocumento = $_REQUEST['firmadigitaledocumento'];
$fea_documento = $_REQUEST['fea_documento'];
$salvaeallega = $_REQUEST['salvaeallega'];
$chiusa=true;
$conn = db_connect();

$query="select id, codice_cartella, data_chiusura, idregime from utenti_cartelle where id=$idcartella";

$rs1 = mssql_query($query, $conn);

if($row=mssql_fetch_assoc($rs1)){ 	
	if($row['data_chiusura']=="") $chiusa=false;
	$idregime = $row['idregime'];
}
if($idcartella==0) $chiusa=false;
mssql_free_result($rs1);

$nome_modulo="nome del modulo";
	
	$query="select idmodulo,nome,replica,secondo_allegato,terzo_allegato,quarto_allegato,firma_digitale,firma_fea from moduli where id=$idmoduloversione";	
	$rs = mssql_query($query, $conn);		
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs))
	{
		$nome_modulo=pulisci_lettura($row['nome']);
		$idmodulo=$row['idmodulo'];
		$replica=$row['replica'];
		$secondo_allegato= empty($row['secondo_allegato']) ? 0 : $row['secondo_allegato'];
		$terzo_allegato = empty($row['terzo_allegato']) ? 0 : $row['terzo_allegato'];
		$quarto_allegato= empty($row['quarto_allegato']) ? 0 : $row['quarto_allegato'];
		$firma_digitale=$row['firma_digitale'];
		$firma_fea=$row['firma_fea'];
	}
$conn = db_connect();

if ($idcartella=="")$idcartella=0;

if (!empty($impegnativa))
	 $imp_query="AND id_impegnativa=$impegnativa";
else $imp_query="";		//$imp_query="AND id_impegnativa IS NULL";

if($idcartella!=0)
	$paz_query="1=1";
	else
	$paz_query="id_paziente=$idpaziente";
$query="select * from re_istanze_moduli where id_cartella=$idcartella and id_modulo_padre=$idmodulo $imp_query and $paz_query order by id_inserimento ASC";
//echo($query);
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
$note = $allegato = "";

$div="sanitaria";
?>

<div id="sanitaria">
<div class="titoloalternativo">
            <h1>cartella: <?=$cartella?></h1>
	        <!--<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
</div>
<div class="titoloalternativo">
            <h1>modulo: <?=$nome_modulo?></h1>
</div>

<div class="titolo_pag">
	<div class="comandi">
		<?if ($idcartella>0){?>
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$div?>')" >ritorna alla lista dei moduli</a></div>
		<?
		}else
		{
		?>
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_visite('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$div?>')" >ritorna alle visite specialistiche</a></div>
		<?
		}
		?>
		<div class="sblocca sblocca_right"><a href="#"  onclick="javascript:sblocca_istanza('<?=$idcartella?>','<?=$idpaziente?>','<?=$idregime?>','<?=$idmoduloversione?>','<?=$cartella?>','<?=$impegnativa?>')" >
		<? if($_COOKIE['unlock_controls']) echo "blocca"; else echo "sblocca"; ?>
		</a></div>
		<!--<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_visite('<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$div?>')" >aggiungi nuovo</a></div>-->
	</div>
</div>

<? if($_COOKIE['unlock_controls'] && $_SESSION['UTENTE']->is_root()) { ?>
	<span class="balloon_float">
		Modalit&agrave; SBLOCCO ATTIVA
	</span>
<? } ?>

<table id="table" class="tablesorter" cellspacing="1"> 
	<thead> 
		<tr> <?
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){?>
			<th>data creaz.</th> 
			<th>ora creaz.</th>	
		<?}?>	
			<th>data sist.</th>
			<th>operatore</th>	
			<th>modulo (ver)</th>
			<th>progressivo</th>
			<th>esplora</th> 			
			<th>modifica</th>
			<th>Converti firma</th>
			<th>allegato</th>
			<?if($secondo_allegato == 1) echo '<th>allegato 2</th>'; ?>
			<?if($terzo_allegato == 1) echo '<th>allegato 3</th>'; ?>
			<?if($quarto_allegato == 1) echo '<th>allegato 4</th>'; ?>
			<th>note</th>
			<th>stampa</th>
			<th>validazione</th>
			<th>elimina</th>
			<?if( ($_COOKIE['unlock_controls'] && $_SESSION['UTENTE']->is_root()) || $_SESSION['UTENTE']->is_root() )  echo '<th>annulla</th>'; ?>
			<th>duplica</th>
		</tr> 
</thead> 
<tbody> 	
<?
	$vers=0;
	$idMod_vers="";
	while($row = mssql_fetch_assoc($rs))   
	{
		$id_istanza_testata=$row['id_istanza_testata'];
		$idmodulo=$row['id_modulo_padre'];
		$nome_modulo=pulisci_lettura($row['nome_modulo']);
		$datains=formatta_data($row['datains']);
		$orains=$row['orains'];
		$dataagg=$row['dataagg'];
		$operatore=$row['nome'];
		$idoperatore=$row['opeins'];
		$idinserimento=$row['id_inserimento'];
		$datasistema=formatta_data($row['data_osservazione']);
		$idmoduloversione=$row['id_modulo_versione'];		
		$note = $row['note'];
		$allegato = !empty($row['allegato']) ? utf8_encode($row['allegato']) : NULL;
		$allegato2 = !empty($row['allegato2']) ? utf8_encode($row['allegato2']) : NULL;
		$allegato3 = !empty($row['allegato3']) ? utf8_encode($row['allegato3']) : NULL;
		$allegato4 = !empty($row['allegato4']) ? utf8_encode($row['allegato4']) : NULL;
		$validazione = $row['validazione'];
		$ope_validazione = $row['ope_validazione'];
		$id_ope_validazione = $row['id_ope_validazione'];
		$data_validazione = date('d-m-Y \a\l\l\e \o\r\e H:i', strtotime($row['data_validazione']));
		$annullamento = $row['annullamento'];
		$ope_annullamento = $row['ope_annullamento'];
		$firmato_digitalmente = $row['firmato_digitalmente'];
		$firmato_fea = $row['firmato_fea'];
		$id_doc_confirmo_fea = $row['id_doc_confirmo_fea'];
		$rtf_landscape = $row['rtf_landscape'];
		$duplicata = $row['duplicata'];
		$cambio_firma = $row['cambio_firma'];

		$data_annullamento = date('d-m-Y \a\l\l\e \o\r\e H:i', strtotime($row['data_annullamento']));
		$vers=1;
		$stop=0;
		$query_v="SELECT id, idmodulo, versione FROM dbo.moduli WHERE (idmodulo = $idmodulo) and (id=$idmoduloversione) ORDER BY id asc";
		
		$rs_v = mssql_query($query_v, $conn);
		$row_v = mssql_fetch_assoc($rs_v);
		$vers=$row_v['versione'];		
		mssql_free_result($rs_v);	
		

		$allegato_1_da_firmare = 'n';
		$allegato_2_da_firmare = 'n';
		$allegato_3_da_firmare = 'n';
		$allegato_4_da_firmare = 'n';
		$firme_allegato1_apposte = 'n';
		$firme_allegato2_apposte = 'n';
		$firme_allegato3_apposte = 'n';
		$firme_allegato4_apposte = 'n';
		$documento_modificabile = $documento_cc_infermieristica_modificabile = 'n';

		// se la CC e' di tipo INFERMIERISTICO e l'utente collegato appartiene a MEDICI O INFERMIERI o OSS, il doc. puo' essere modificato
		// SBO 07/08/23 : aggiunto controllo sul DS
		if( 
			($idregime == 52 || 	// CC infermieristica
			 $idregime == 53 ||     // CC infermieristica L.8
			 $idregime == 59 ||     // CC Infermieristica RD1 estensiva
			 $idregime == 60 ||     // CC infermieristica Trattamento Privato RD1 estensiva
			 $idregime == 61 )     	// CC infermieristica SUAP
			 &&
			($idmodulo == 202 || 	// Cartella Infermieristica - Monitoraggio parametri
			 $idmodulo == 203 || 	// Cartella Infermieristica - Monitoraggio temperature
			 $idmodulo == 204 || 	// Cartella Infermieristica - Bilancio idrico
			 $idmodulo == 206)    	// Cartella Infermieristica - Somministrazione farmacologica
			 &&
			($_SESSION['UTENTE']->get_gid() == 1017 || 			// RTS - Infermieri
			 $_SESSION['UTENTE']->get_gid() == 1015 || 			// RTS - Medici Specialisti
			 $_SESSION['UTENTE']->get_gid() == 1020 ||			// RTS - Medici
			 $_SESSION['UTENTE']->get_gid() == 1003 ||			// OSS
			 $_SESSION['UTENTE']->is_ds())						// Direttore Sanitario
			)
		{
			$documento_cc_infermieristica_modificabile = 's';
		} 
		elseif
		( 
			($idregime == 51 ||
			 $idregime == 55 ||
			 $idregime == 56)
			&&
			($idmodulo == 131 || 	// Cartella Clinica (ex26) - Programma Riabilitativo - Mobilita' e Spostamenti
			 $idmodulo == 143 || 	// Cartella Clinica (legge8) - Piano esecutivo individuale - Autonomia e cura della persona
			 $idmodulo == 149 || 	// Cartella Clinica (ex26) - Programma Riabilitativo - Funzioni sensomotorie
			 $idmodulo == 150 || 	// Cartella Clinica (ex26) - Programma Riabilitativo - Comunicativo - Relazionali
			 $idmodulo == 151 || 	// Cartella Clinica (ex26) - Programma Riabilitativo - Cognitivo - Comportamentale
			 $idmodulo == 152 || 	// Cartella Clinica (ex26) - Programma Riabilitativo - Autonomia cura persona
			 $idmodulo == 153 || 	// Cartella Clinica (ex26) - Programma Riabilitativo - Area emotiva affettiva
			 $idmodulo == 154 || 	// Cartella Clinica (ex26) - Programma Riabilitativo - Riadattamento - Reinserimento sociale
			 $idmodulo == 165 || 	// Cartella Clinica (legge8) - Piano esecutivo individuale - Riadattamento e reinserimento sociale
			 $idmodulo == 179 || 	// Cartella Clinica (RD1) - Programma riabilitativo - Funzioni motorie e sensoriali
			 $idmodulo == 180 || 	// Cartella Clinica (RD1) - Programma riabilitativo - Funzioni cardio-respiratorie
			 $idmodulo == 182 || 	// Cartella Clinica (RD1) - Programma riabilitativo - Funzioni vescico-sfinteriche
			 $idmodulo == 183 || 	// Cartella Clinica (RD1) - Programma riabilitativo - Funzioni digestive
			 $idmodulo == 184 || 	// Cartella Clinica (RD1) - Programma riabilitativo - Funzioni cognitive/comportamentali e del linguaggio
			 $idmodulo == 185 || 	// Cartella Clinica (RD1) - Programma riabilitativo - Informazione/formazione al care giver
			 $idmodulo == 220 || 	// Cartella Clinica (ex art.44) - Obiettivi e Programma Riabilitativo
			 $idmodulo == 226)   	// Cartella Clinica (SUAP) - Piano esecutivo)
			&&
			 ($_SESSION['UTENTE']->get_gid() == 1015 || 			// RTS - Medici Specialisti
			 $_SESSION['UTENTE']->get_gid() == 1020 ||			// RTS - Medici
			 $_SESSION['UTENTE']->is_ds())						// Direttore Sanitario
		)
		{
			$documento_cc_infermieristica_modificabile = 's';
		}
		
		
		
		// I moduli Anagrafica - Anamnestica e Anamnesi non sono duplicabili, quindi nascondo la funzione
		$modulo_duplucabile = true;
		if($idmodulo == 129 || $idmodulo == 141 || $idmodulo == 178 || $idmodulo == 224 || $idmodulo == 228) {
			$modulo_duplucabile = false;			
		}
		
		
		
		if($idmodulo == 134 && $allegato !== NULL && $dataagg == NULL && $firmato_digitalmente == 's')
		{
			// idmodulo=134 -> riunione d'equipe -- se ha un allegato, verifico la possibilita' di modifica sulla data di aggiornamento 
			// (in quanto, avendolo modificato, vuol dire che stanno creando la versione B del progressivo)
			$documento_modificabile = 's';				// il doc. puo' ancora essere modificato, finche' non vengono caricati i rispettivi allegati		
		}
		else 
		{			
			// controllo sulla possibilita' di modifica in base alla data di compilazione e la presenza o meno di allegati
			//$data_ora_da_confrontare = $row['datains'];
			//$diff = ceil((strtotime($data_ora_da_confrontare) - time()) / (60 * 60 * 24));
			
			// AME / SBO 16-08-23 : rimosso doppio strtotime($data_ora_da_confrontare) e fix su format del numero 172.800 (ora 172800)
			$data_ora_da_confrontare = date('Y-m-d', strtotime($row['datains'])) . ' ' . $row['orains'];
			$out2days = (strtotime(date('Y-m-d H:i:s')) - strtotime($data_ora_da_confrontare)) > 172800;

			//if($diff <= -2) 									// se sono passati 2 giorni
			if($out2days) 										// se sono passati 2 giorni
			{
				$passati_due_gg = 'n';
				if( ($secondo_allegato == 0 && $allegato == NULL)||								// se l'allegato 1 e' stato caricato
					($terzo_allegato == 0 && $allegato == NULL && $allegato2 == NULL)||
					($quarto_allegato == 0 && $allegato == NULL && $allegato2 == NULL && $allegato3 == NULL)||	
					
					($secondo_allegato == 1 && ($allegato == NULL || $allegato2 == NULL)) ||	// se l'allegato 1 o entrambi sono stati caricati
					($terzo_allegato == 1 && ($allegato == NULL || $allegato2 == NULL || $allegato3 == NULL)) ||
					($quarto_allegato == 1 && ($allegato == NULL || $allegato2 == NULL || $allegato3 == NULL || $allegato == NULL))
				) 	
				{
					$documento_modificabile = 's';				// il doc. puo' ancora essere modificato, finche' non vengono caricati i rispettivi allegati							
				} else {
					$documento_modificabile = 'n';				// il doc. non puo' essere piu' modificato
				}
			}
			else 												// se non sono passati 2 giorni il doc. � sempre modificabile, indipendentemente dagli allegati
			{
				$passati_due_gg = 's';
				$documento_modificabile = 's';
			}

			$dt_exec_fse = '';

			$query_invio_fse = "SELECT invio_fse 
								FROM dbo.regimi_moduli
								WHERE idmodulo = $idmodulo and idregime=$idregime";
			$_rs = mssql_query($query_invio_fse, $conn);
			$_row = mssql_fetch_assoc($_rs);
			mssql_free_result($_rs);
		
			if($_row['invio_fse'] === 's') {
				$query_rep_fse = "SELECT TOP (1) data_esecuzione
									FROM dbo.repository_fse
									WHERE id_paziente = $idpaziente and id_modulo_padre = $idmodulo and id_istanza_testata = $id_istanza_testata and azione = 'CREATE' and stato_esecuzione = 'OK'";
				$_rs = mssql_query($query_rep_fse, $conn);
				$_row = mssql_fetch_assoc($_rs);
				mssql_free_result($_rs);
				$dt_exec_fse = !empty($_row['data_esecuzione']) ? $_row['data_esecuzione'] : '';
			}

			if($idmodulo == 269 && ($firmato_digitalmente == 's' || $firmato_fea == 's')) 
			{
				$_sql_firme = "SELECT TOP 1 id_operatore, stato_firma_allegato, stato_firma_allegato2, stato_firma_allegato3, stato_firma_allegato4
								FROM istanze_testata_firme
								WHERE id_istanza_testata = $id_istanza_testata
								ORDER BY id DESC";
				$_rs_firme = mssql_query($_sql_firme, $conn);
				$firme_emotrasf = mssql_fetch_assoc($_rs_firme);
				$firma_op_emotrasf = $firme_emotrasf['id_operatore'];
				
				if($allegato != NULL && $allegato2 != NULL && $allegato3 != NULL && $allegato4 != NULL)
				{
					$firme_allegato4_apposte = 's';
					$firme_allegato3_apposte = 's';
					$firme_allegato2_apposte = 's';
				}
				if($allegato != NULL && $allegato2 != NULL && $allegato3 != NULL && $quarto_allegato == 1) //se ho l'allegato 3 e i precedenti allora vado a sostituire il quarto
				{
					if($firme_emotrasf['stato_firma_allegato4'] == 'a') {
						$allegato_4_da_firmare = 's';
					}
					
					$firme_allegato3_apposte = 's';
					$firme_allegato2_apposte = 's';
				}
				elseif($allegato != NULL && $allegato2 != NULL && $terzo_allegato == 1) //se ho l'allegato 2 e i precedenti allora vado a sostituire il terzo
				{					
					if($firme_emotrasf['stato_firma_allegato3'] == 'a') {
						$allegato_3_da_firmare = 's';						
					}
					
					$firme_allegato2_apposte = 's';
				}
				elseif($allegato != NULL && $secondo_allegato == 1)	 //se ho sia il primo che il secondo allegato allora vado a sostituire il secondo
				{
					$_sql_firme = "SELECT stato_firma_allegato, stato_firma_allegato2 
									FROM istanze_testata_firme
									WHERE id_istanza_testata = $id_istanza_testata";
					$_rs_firme = mssql_query($_sql_firme, $conn);
					
					$stato_firma_allegato2 = '';
					$allegato_2_da_firmare = 's';
					$firme_allegato1_apposte = 's';
					
					while($_row_firme = mssql_fetch_assoc($_rs_firme))
					{
						if($_row_firme['stato_firma_allegato'] == 'a') {
							$allegato_2_da_firmare = 'n';
							$firme_allegato1_apposte = 'n';
						}
						
						if(!empty($_row_firme['stato_firma_allegato2'])) {
							$stato_firma_allegato2 = $_row_firme['stato_firma_allegato2'];
						}
					}
					
					if($allegato_2_da_firmare == 's' && ($stato_firma_allegato2 == '' || $stato_firma_allegato2 != 'a')) {
						$allegato_2_da_firmare = 'n';
					}

					
					if($firme_allegato1_apposte == 'n') {
						$allegato_1_da_firmare = 's';
					}
				} else {
					$_sql_firme = "SELECT TOP 1 id_operatore
									FROM istanze_testata_firme
									WHERE id_istanza_testata = $id_istanza_testata
									ORDER BY id ASC";
					$_rs_firme = mssql_query($_sql_firme, $conn);
					$firme_emotrasf = mssql_fetch_assoc($_rs_firme);
					$firma_op_emotrasf = $firme_emotrasf['id_operatore'];
				
					$allegato_1_da_firmare = 's';
				}				
			} 
			elseif ($firmato_digitalmente == 's' || $firmato_fea == 's') 
			{
				if($allegato != NULL && $allegato2 != NULL && $allegato3 != NULL && $quarto_allegato == 1) //se ho l'allegato 3 e i precedenti allora vado a sostituire il quarto
				{
					$allegato_4_da_firmare = 's';
				}
				elseif($allegato != NULL && $allegato2 != NULL && $terzo_allegato == 1) //se ho l'allegato 2 e i precedenti allora vado a sostituire il terzo
				{
					$allegato_3_da_firmare = 's';
				}
				elseif($allegato != NULL && $secondo_allegato == 1)	 //se ho sia il primo che il secondo allegato allora vado a sostituire il secondo
				{
					$allegato_2_da_firmare = 's';
				}
				else { //se la condizione precedente � false vuol dire che ho solo l'allegato 1 oppure non � previsto l'allegato 2, quindi sostiuisco il primo
					$allegato_1_da_firmare = 's';					
				}				
			}			
		}
		
		if( 
			($idregime == 55 || 	// RD1 Intensiva
			 $idregime == 57 ||     // Suap Privata
			 $idregime == 54 ||     // Suap
			 $idregime == 56 ||     // Trattamento privato RD1 estensiva
			 $idregime == 51 )     	// RD1 Estensiva
			&&
			$idmodulo == 174 		// Cartella Clinica (ex26 e Legge8) - Allergie Alimentari
		) {
			$firmato_digitalmente == 'n';
		}

		if($firmato_digitalmente == 's')
		{
			// verifico che il modulo sia da firmare digitalmente		
			$_query = "SELECT TOP(1) *
					FROM moduli
					WHERE firma_digitale = 1 AND idmodulo = $idmodulo ORDER BY versione DESC";
			$_rs_modulo_firma_dig = mssql_query($_query, $conn);
			$_row_modulo_firma_dig = mssql_fetch_assoc($_rs_modulo_firma_dig);
			
			if(!empty($_row_modulo_firma_dig))
			{
				$query_check_firma ="SELECT id_operatore, stato_firma_allegato, stato_firma_allegato2, stato_firma_allegato3, stato_firma_allegato4
										FROM dbo.istanze_testata_firme WHERE (id_istanza_testata = $id_istanza_testata)";
				$rs_check_firma = mssql_query($query_check_firma, $conn);
				while($row_check_firma = mssql_fetch_assoc($rs_check_firma))
				{
					$id_operatore_firma = $row_check_firma['id_operatore'];		
					$stato_firma_allegato = $row_check_firma['stato_firma_allegato'];		
					$stato_firma_allegato2 = $row_check_firma['stato_firma_allegato2'];	
					$stato_firma_allegato3 = $row_check_firma['stato_firma_allegato3'];
					$stato_firma_allegato4 = $row_check_firma['stato_firma_allegato4'];
					
					if($id_operatore_firma == $_SESSION['UTENTE']->get_userid() && $stato_firma_allegato == 'a') 		$allegato_1_da_firmare = 's';
					if($id_operatore_firma == $_SESSION['UTENTE']->get_userid() && $stato_firma_allegato2 == 'a') 		$allegato_2_da_firmare = 's';
					if($id_operatore_firma == $_SESSION['UTENTE']->get_userid() && $stato_firma_allegato3 == 'a') 		$allegato_3_da_firmare = 's';
					if($id_operatore_firma == $_SESSION['UTENTE']->get_userid() && $stato_firma_allegato4 == 'a') 		$allegato_4_da_firmare = 's';
					
					//echo "<BR>$idinserimento - $id_istanza_testata - $id_operatore_firma - $stato_firma_allegato";
				}
				mssql_free_result($rs_check_firma);	
			} 
		}
		
		
		if($annullamento == 's') 
			 $td_style = 'style="background-color:#eeeeee"';
		else $td_style= '';
	?>		
					<tr>
					<?
					if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){?>
					<td <?=$td_style?> ><?=$datains?></td> 
					<td <?=$td_style?> ><?=$orains?></td>
					<?}?>
					 
					<td <?=$td_style?> ><?if($datasistema <> '01/01/1900') echo $datasistema;?></td> 					
					<td <?=$td_style?> ><?=$operatore?></td>
					<td <?=$td_style?> ><?php echo($nome_modulo." (vers. ".$vers.")");?></td>
					<td <?=$td_style?> ><?php echo $idinserimento ?></td>
                      <?					  
					$query="select idmodulo,opeins from moduli where id=$idmoduloversione";
					$rs1 = mssql_query($query, $conn);
						
					if(!$rs1) error_message(mssql_error());

					if($row1 = mssql_fetch_assoc($rs1))
					{
						$idmodulo1=$row1['idmodulo'];
						$replica=$row1['replica'];		
						$opeins=$row1['opeins'];						
					}					
					
						?><td <?=$td_style?> >
						<?							
						if ($_COOKIE['unlock_controls'] || 
							$_SESSION['UTENTE']->is_root() || 
							$opeins == $_SESSION['UTENTE']->get_userid() 
							|| get_permesso_modulo_vis($idmodulo1,$_SESSION['UTENTE']->get_gid()) 
							|| $documento_cc_infermieristica_modificabile == 's' 
						)
						{?>
				      	<a href="#"  onclick="javascript:visualizza_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','','<?=$impegnativa?>')" ><img src="images/view.png" /></a>
                        <? }else {
                         echo("&nbsp;");
						}
						?>
						</td>						
						<td <?=$td_style?> >
						<?	
						if ($_COOKIE['unlock_controls']) { ?>
							<a href="#" onclick="javascript:edita_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$impegnativa?>','<?=$id_impegnativa_associata?>')" ><img src="images/gear.png" /></a>
					 <? }
						elseif(
								(($idoperatore==$_SESSION['UTENTE']->get_userid()) && (get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid())) && !$chiusa) ||
								(($_SESSION['UTENTE']->get_gid() == 1015 || $_SESSION['UTENTE']->get_gid() == 1020 || $_SESSION['UTENTE']->is_ds()) && !$chiusa) ||
								$_SESSION['UTENTE']->is_root() || 
								get_flag_edita_modulo($idmodulo1, $vers) ||
								$documento_cc_infermieristica_modificabile == 's')
						{
							if($secondo_allegato == 1) {
								//if (($documento_modificabile == 's' || $documento_modificabile == 'n') && $validazione == 'a') {
								//if ($validazione == 'a') {
								?>
									<a href="#"  onclick="javascript:edita_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$impegnativa?>','<?=$id_impegnativa_associata?>')" ><img src="images/gear.png" /></a>
								<?
								// }else {
								//	echo("&nbsp;");
								// }
							} elseif($secondo_allegato == 0) {
								//if (($documento_modificabile == 's' || $documento_modificabile == 'n') && $validazione == 'a') {
								// if ($validazione == 'a') {
								?>
									<a href="#"  onclick="javascript:edita_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$impegnativa?>','<?=$id_impegnativa_associata?>')" ><img src="images/gear.png" /></a>
								<?
								// }else {
								// 	echo("&nbsp;");									
								// }
							}
                        } else {
							echo("&nbsp;");
						}?>
                      </td> 
					  <td <?=$td_style?> >
					<?	if($_COOKIE['unlock_controls'] || ($duplicata && !$cambio_firma)) {					  
							if($firmato_digitalmente == 's' || $firmato_fea == 's') { ?>
								<a href="#" title="Converti in salvataggio standard" onclick="javascript:if(confirm('Sei sicuro di voler convertire l\'istanza in salvataggio standard? N.B. Gli allegati attualmente presenti veranno rimossi.')) converti_firma_istanza(<?=$id_istanza_testata?>, 'manuale', <?=$idcartella?>, <?=$idpaziente?>, <?=$idmoduloversione?>, '<?=$impegnativa?>', '<?=$duplicata?>');">Converti in manuale</a><br>
							<? }
							
							if(($firma_digitale == 1 && $firmato_digitalmente == 'n' && $firmato_fea == 'n') || ($firma_digitale == 1 && $firmato_digitalmente == 'n' && $firmato_fea == 's')) { ?>
								<a href="#" title="Converti in firma digitale" onclick="javascript:if(confirm('Sei sicuro di voler convertire l\'istanza in firma digitale ? N.B. Gli allegati attualmente presenti veranno rimossi.')) converti_firma_istanza(<?=$id_istanza_testata?>, 'fdr', <?=$idcartella?>, <?=$idpaziente?>, <?=$idmoduloversione?>, '<?=$impegnativa?>', '<?=$duplicata?>');">Converti in FDR</a><br>
							<? }
							
							if(($firma_fea == 1 && $firmato_fea == 'n' && $firmato_digitalmente == 'n') || ($firma_fea == 1 && $firmato_fea == 'n' && $firmato_digitalmente == 's')) { ?>
								<a href="#" title="Converti in firma grafometrica" onclick="javascript:if(confirm('Sei sicuro di voler convertire l\'istanza in firma grafometrica? N.B. Gli allegati attualmente presenti veranno rimossi.')) converti_firma_istanza(<?=$id_istanza_testata?>, 'fea', <?=$idcartella?>, <?=$idpaziente?>, <?=$idmoduloversione?>, '<?=$impegnativa?>', '<?=$duplicata?>');">Converti in FEA</a>
							<? }
						} 
					?>
					  </td>
					  <td <?=$td_style?> >					
					  
					  <?if ($_COOKIE['unlock_controls'] || (get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid()) ) || $_SESSION['UTENTE']->is_root() || get_flag_edita_modulo($idmodulo1, $vers))
						{							
							if(!$chiusa)
							{
								//if( ($allegato_1_da_firmare == 's' && $validazione == 'a'		&& 
								if( ($allegato_1_da_firmare == 's'      						&& 
										($firmato_digitalmente == 's' || $firmato_fea == 's') 	&&
										($allegato == NULL || $documento_modificabile == 's'))	|| 
										($allegato_1_da_firmare == 's' && $_COOKIE['unlock_controls'])
								  ) {										
										if($idmodulo == 269 && $firma_op_emotrasf != $_SESSION['UTENTE']->_properties['uid']) {?>
										<img src="images/add-file-disabled.png" title="Operatore non abilitato alla firma del modulo" />
									<?} elseif($firmato_digitalmente == 's') {
											if($_SESSION['UTENTE']->_properties['uid'] == 11) { ?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_digitalmente_TEST(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato',<?=$secondo_allegato?>,<?=$rtf_landscape?>);"><img src="images/add-file-signed.png" title="Genera allegato e firmalo digitalmente"/></a>
<?											} else { ?>									
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_digitalmente(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato',<?=$secondo_allegato?>,<?=$rtf_landscape?>);"><img src="images/add-file-signed.png" title="Genera allegato e firmalo digitalmente"/></a>
<?											}
										} elseif($firmato_fea == 's') {?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_con_fea(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato',<?=$secondo_allegato?>);"><img src="images/add-file-signed.png" title="Genera allegato e firmalo grafometricamente"/></a>
											<? if($allegato == NULL && $id_doc_confirmo_fea > 0) { ?>
												&nbsp;
												<a href="javascript:void(0)" onclick="javascript:riscarica_modulo_firmato_fea(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato',<?=$secondo_allegato?>,<?=$id_istanza_testata?>,<?=$id_doc_confirmo_fea?>);"><img src="images/download.png" title="Riscarica modulo firmato grafometricamente"/></a>
											<?	}
										// SBO & AME 06/11/23: Aggiunto controllo e bottone mancante
										} elseif (($idoperatore==$_SESSION['UTENTE']->get_userid() && $allegato == "") || $_COOKIE['unlock_controls']) { ?>
											<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" title="Carica allegato manualmente"/></a>
									<?	}
									}
								elseif( ($firmato_digitalmente == 'n' && $allegato_1_da_firmare == 'n' && 
										($allegato == NULL || $documento_modificabile == 's') ))
										//&& $validazione == 'a')) 
									{ ?>
										<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" title="Carica allegato manualmente"/></a>
								 <? }
								elseif(	($allegato_1_da_firmare == 'n' && ($firmato_digitalmente == 's' || $firmato_fea = 's')) ||
										($allegato_1_da_firmare == 's' && ($firmato_digitalmente == 'n' || $firmato_fea = 'n')) )
								{
									if (($idoperatore==$_SESSION['UTENTE']->get_userid() && $allegato == "") || $_COOKIE['unlock_controls']) { ?>
										<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" title="Carica allegato manualmente"/></a>
								 <? }
									elseif($_SESSION['UTENTE']->is_root() && $firmato_digitalmente == 'n') { ?>
										<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" title="Forza caricamento allegato manualmente"/></a>
								 <? }	
								}
								
								if ($allegato !== "" && $allegato !== NULL) {
									echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
									if(($passati_due_gg == 'n' && $allegato2 == "") || $_SESSION['UTENTE']->is_root() || $_COOKIE['unlock_controls'] ) { ?>
										<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato')" ><img src="images/remove.png" /></a>
								<? }
								}
							
							} else {
								if ($allegato !== "" && $allegato !== NULL) {
									echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
									if(($passati_due_gg == 'n' && $allegato2 == "") || 
										$_SESSION['UTENTE']->is_root() || 
										$_COOKIE['unlock_controls'] ) { ?>
										<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato')" ><img src="images/remove.png" /></a>
								 <? }
								}
							}							
							//else echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
						}
						elseif($documento_cc_infermieristica_modificabile == 's')
						{
							if ($allegato !== "" && $allegato !== NULL) {
								echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
								if(($passati_due_gg == 'n' && $allegato2 == "") || $_SESSION['UTENTE']->is_root() || $_COOKIE['unlock_controls'] ) { ?>
									<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato')" ><img src="images/remove.png" /></a>
							 <? }
							}
						
						} else {
							echo("&nbsp;");
						} ?>
					  </td>
					
<?					if($secondo_allegato == 1) {?>
					  <td <?=$td_style?> >
					  <? if ($_COOKIE['unlock_controls'] || (get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid()) ) || $_SESSION['UTENTE']->is_root() || get_flag_edita_modulo($idmodulo1, $vers))
						{
							if(!$chiusa)
							{	
								//if(($allegato_2_da_firmare == 's' && $firmato_digitalmente == 's' && ($allegato2 == NULL || $documento_modificabile == 's') && $validazione == 'a') || 
								if(($allegato_2_da_firmare == 's' && $firmato_digitalmente == 's' && ($allegato2 == NULL || $documento_modificabile == 's') ) || 
								   ($allegato_2_da_firmare == 's' && $firmato_digitalmente == 's' && $_COOKIE['unlock_controls']) ) {
									if($idmodulo == 269 && $firma_op_emotrasf != $_SESSION['UTENTE']->_properties['uid']) {?>
										<img src="images/add-file-disabled.png" title="Operatore non abilitato alla firma del modulo" />
									<?} elseif ($allegato !== "" && $allegato !== NULL) { 
										if($firmato_digitalmente == 's') { 
											if($_SESSION['UTENTE']->_properties['uid'] == 11) { ?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_digitalmente_TEST(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato2',<?=$terzo_allegato?>,<?=$rtf_landscape?>);"><img src="images/add-file-signed.png" title="Genera allegato 2 e firmalo digitalmente"/></a>
<? 											} else { ?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_digitalmente(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato2',<?=$terzo_allegato?>,<?=$rtf_landscape?>);"><img src="images/add-file-signed.png" title="Genera allegato 2 e firmalo digitalmente"/></a>
<?											}
										} elseif($firmato_fea == 's') {?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_con_fea(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato2',1);"><img src="images/add-file-signed.png" title="Genera allegato 2 e firmalo grafometricamente"/></a>
											<? if($allegato2 == NULL && $id_doc_confirmo_fea > 0) { ?>
												&nbsp;
												<a href="javascript:void(0)" onclick="javascript:riscarica_modulo_firmato_fea(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato2',1,<?=$id_istanza_testata?>,<?=$id_doc_confirmo_fea?>);"><img src="images/download.png" title="Riscarica modulo firmato grafometricamente"/></a>
											<?	}
										}
									} else {?>
										<img src="images/add-file-signed-disabled.png" title="&Egrave; necessario caricare prima l'allegato 1." />
									<?}
								}
								elseif($idmodulo == 269) {
									if($allegato_2_da_firmare == 'n' && $firmato_digitalmente == 's' && $firme_allegato1_apposte == 's' && $firme_allegato2_apposte == 'n') {?>
										<img src="images/add-file-disabled.png" title="&Egrave; necessario apporre prima la seconda firma nel modulo." />
									<?
									}
									elseif(($allegato_2_da_firmare == 'n' && $firmato_digitalmente == 's') || ($allegato_2_da_firmare == 's' && $firmato_digitalmente == 'n'))
									{
										if ($allegato !== "" && $allegato !== NULL  && $firmato_digitalmente == 'n') { ?>
											<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato2&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" /></a>
									  <?} elseif($allegato2 == "" && $allegato2 == NULL) {?>
											<img src="images/add-file-disabled.png" title="&Egrave; necessario caricare prima l'allegato 1." />
									  <?}
									}
								}
								elseif ($idoperatore==$_SESSION['UTENTE']->get_userid() || $_SESSION['UTENTE']->is_root() || $_COOKIE['unlock_controls'])
								{
									if(	($allegato_2_da_firmare == 'n' && $firmato_digitalmente == 's') ||
											($allegato_2_da_firmare == 's' && $firmato_digitalmente == 'n') )
									{
										if ($allegato !== "" && $allegato !== NULL) { ?>
											<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato2&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" /></a>
									  <?} else {?>
											<img src="images/add-file-disabled.png" title="&Egrave; necessario caricare prima l'allegato 1." />
									  <?}
									}
								} 
								else {
									if(	$allegato_2_da_firmare == 'n' && $firmato_digitalmente == 's' && $firme_allegato1_apposte == 'n')
									{?>
										<img src="images/add-file-disabled.png" title="&Egrave; necessario apporre prima tutte le firme all'allegato 1." />
									<?}
								}
										
								
								if ($allegato2 !== "" && $allegato2 !== NULL) {
									echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato2.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
									if(($passati_due_gg == 'n'  && !$chiusa) || $_SESSION['UTENTE']->is_root() ) { ?>
										<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato 2 attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato2')" ><img src="images/remove.png" /></a>
								 <? }
								}
							} else {
								if ($allegato2 !== "" && $allegato2 !== NULL) {
									echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato2.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
									if(($passati_due_gg == 'n'  && !$chiusa) || $_SESSION['UTENTE']->is_root() ) { ?>
										<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato 2 attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato2')" ><img src="images/remove.png" /></a>
								 <? }
								}
							}
						}
						elseif($documento_cc_infermieristica_modificabile == 's')
						{
							if ($allegato2 !== "" && $allegato2 !== NULL) {
								echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato2.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
								if(($passati_due_gg == 'n' && $allegato3 == "") || $_SESSION['UTENTE']->is_root() || $_COOKIE['unlock_controls'] ) { ?>
									<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato2')" ><img src="images/remove.png" /></a>
							 <? }
							}
						}
						?>			
					  </td>
<?					}

					if($terzo_allegato == 1) { ?>
					  <td <?=$td_style?> >
					  <? if ($_COOKIE['unlock_controls'] || get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid()) || $_SESSION['UTENTE']->is_root() || get_flag_edita_modulo($idmodulo1, $vers))
						{
							if(!$chiusa)
							{								
								//if(($allegato_3_da_firmare == 's' && $firmato_digitalmente == 's' && ($allegato3 == NULL || $documento_modificabile == 's') && $validazione == 'a') || 
								if(($allegato_3_da_firmare == 's' && $firmato_digitalmente == 's' && ($allegato3 == NULL || $documento_modificabile == 's') ) || 
								   ($allegato_3_da_firmare == 's' && $firmato_digitalmente == 's' && $_COOKIE['unlock_controls']) ) {

									if($idmodulo == 269 && $firma_op_emotrasf != $_SESSION['UTENTE']->_properties['uid']) {?>
										<img src="images/add-file-disabled.png" title="Operatore non abilitato alla firma del modulo" />
									<?} elseif ($allegato2 !== "" && $allegato2 !== NULL) { 
										if($firmato_digitalmente == 's') { 
											if($_SESSION['UTENTE']->_properties['uid'] == 11) { ?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_digitalmente_TEST(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato3',<?=$quarto_allegato?>,<?=$rtf_landscape?>);"><img src="images/add-file-signed.png" title="Genera allegato 3 e firmalo digitalmente"/></a>
<? 											} else { ?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_digitalmente(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato3',<?=$quarto_allegato?>,<?=$rtf_landscape?>);"><img src="images/add-file-signed.png" title="Genera allegato 3 e firmalo digitalmente"/></a>
<?											}
										} elseif($firmato_fea == 's') {?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_con_fea(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato3',<?=$terzo_allegato?>);"><img src="images/add-file-signed.png" title="Genera allegato 3 e firmalo grafometricamente"/></a>
											<? if($allegato3 == NULL && $id_doc_confirmo_fea > 0) { ?>
												&nbsp;
												<a href="javascript:void(0)" onclick="javascript:riscarica_modulo_firmato_fea(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato3',<?=$terzo_allegato?>,<?=$id_istanza_testata?>,<?=$id_doc_confirmo_fea?>);"><img src="images/download.png" title="Riscarica modulo firmato grafometricamente"/></a>
											<?	}
										}
									} else {?>
										<img src="images/add-file-signed-disabled.png" title="&Egrave; necessario caricare prima l'allegato 2." />
									<?}
								}
								elseif($idmodulo == 269) {									
									if($allegato_3_da_firmare == 'n' && $firmato_digitalmente == 's' && $firme_allegato2_apposte == 's' && $firme_allegato3_apposte == 'n') {?>
										<img src="images/add-file-disabled.png" title="&Egrave; necessario apporre prima la terza firma nel modulo." />
									<?
									}
									elseif(	
										$firme_allegato3_apposte == 'n' &&
										(($allegato_3_da_firmare == 'n' && $firmato_digitalmente == 's') || ($allegato_3_da_firmare == 's' && $firmato_digitalmente == 'n')) 
									)
									{
										if ($allegato2 !== "" && $allegato2 !== NULL) { ?>
											<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato3&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" /></a>
									  <?} elseif($allegato3 == "" && $allegato3 == NULL) {?>
											<img src="images/add-file-disabled.png" title="&Egrave; necessario caricare prima l'allegato 2." />
									  <?}
									}
								}
								elseif ($idoperatore==$_SESSION['UTENTE']->get_userid() || $_SESSION['UTENTE']->is_root() || $_COOKIE['unlock_controls'])
								{
									if(	($allegato_3_da_firmare == 'n' && $firmato_digitalmente == 's') ||
										($allegato_3_da_firmare == 's' && $firmato_digitalmente == 'n') )
									{
										if ($allegato2 !== "" && $allegato2 !== NULL) { ?>
											<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato3&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" /></a>
									  <?} else {?>
											<img src="images/add-file-disabled.png" title="&Egrave; necessario caricare prima l'allegato 2." />
									  <?}
									}
								}
								
								if ($allegato3 !== "" && $allegato3 !== NULL) {
									echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato3.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
									if(($passati_due_gg == 'n'  && !$chiusa) || $_SESSION['UTENTE']->is_root() ) { ?>
										<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato 3 attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato3')" ><img src="images/remove.png" /></a>
								 <? }
								}
								
							} else {								
								if ($allegato3 !== "" && $allegato3 !== NULL) {
									echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato3.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
									if(($passati_due_gg == 'n'  && !$chiusa) || $_SESSION['UTENTE']->is_root() ) { ?>
										<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato 3 attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato3')" ><img src="images/remove.png" /></a>
								 <? }
								}
							}
						}
						elseif($documento_cc_infermieristica_modificabile == 's')
						{
							if ($allegato3 !== "" && $allegato3 !== NULL) {
								echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato3.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
								if(($passati_due_gg == 'n' && $allegato4 == "") || $_SESSION['UTENTE']->is_root() || $_COOKIE['unlock_controls'] ) { ?>
									<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato3')" ><img src="images/remove.png" /></a>
							 <? }
							}
						}
						?>			
					  </td>
<?					}

					if($quarto_allegato == 1) { ?>					  
					  <td <?=$td_style?> >
					  <? if ($_COOKIE['unlock_controls'] || (get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid()) ) || $_SESSION['UTENTE']->is_root() || get_flag_edita_modulo($idmodulo1, $vers))
						{ 
							if(!$chiusa)
							{	
								//if(($allegato_4_da_firmare == 's' && $firmato_digitalmente == 's' && ($allegato4 == NULL || $documento_modificabile == 's') && $validazione == 'a') 
								if(($allegato_4_da_firmare == 's' && $firmato_digitalmente == 's' && ($allegato4 == NULL || $documento_modificabile == 's') ) 
									|| ($allegato_4_da_firmare == 's' && $firmato_digitalmente == 's' && $_COOKIE['unlock_controls']) ) {
									if($idmodulo == 269 && $firma_op_emotrasf != $_SESSION['UTENTE']->_properties['uid']) {?>
										<img src="images/add-file-disabled.png" title="Operatore non abilitato alla firma del modulo" />
									<?} elseif ($allegato3 !== "" && $allegato3 !== NULL) { 
										if($firmato_digitalmente == 's') { 
											if($_SESSION['UTENTE']->_properties['uid'] == 11) { ?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_digitalmente_TEST(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato4',0,<?=$rtf_landscape?>);"><img src="images/add-file-signed.png" title="Genera allegato 4 e firmalo digitalmente"/></a>
<? 											} else { ?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_digitalmente(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato4',0,<?=$rtf_landscape?>);"><img src="images/add-file-signed.png" title="Genera allegato 4 e firmalo digitalmente"/></a>
<?											}
										} elseif($firmato_fea == 's') {?>
											<a href="javascript:void(0)" onclick="javascript:firma_modulo_con_fea(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato4',<?=$quarto_allegato?>);"><img src="images/add-file-signed.png" title="Genera allegato 4 e firmalo grafometricamente"/></a>
											<? if($allegato4 == NULL && $id_doc_confirmo_fea > 0) { ?>
												&nbsp;
												<a href="javascript:void(0)" onclick="javascript:riscarica_modulo_firmato_fea(<?=$idcartella?>,<?=$idinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'allegato4',<?=$quarto_allegato?>,<?=$id_istanza_testata?>,<?=$id_doc_confirmo_fea?>);"><img src="images/download.png" title="Riscarica modulo firmato grafometricamente"/></a>
											<?	}
										}
									} else {?>
										<img src="images/add-file-signed-disabled.png" title="&Egrave; necessario caricare prima l'allegato 3." />
									<?}
								}
								elseif($idmodulo == 269) {
									if($allegato_4_da_firmare == 'n' && $firmato_digitalmente == 's' && $firme_allegato3_apposte == 's' && $firme_allegato4_apposte == 'n') {?>
										<img src="images/add-file-disabled.png" title="&Egrave; necessario apporre prima la quarta firma nel modulo." />
									<?
									}
									elseif(($allegato_4_da_firmare == 'n' && $firmato_digitalmente == 's') || ($allegato_4_da_firmare == 's' && $firmato_digitalmente == 'n')) 
									{
										if ($allegato3 !== "" && $allegato3 !== NULL && $firmato_digitalmente == 'n') { ?>
											<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato3&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" /></a>
									  <?} elseif($allegato4 == "" && $allegato4 == NULL) {?>
											<img src="images/add-file-disabled.png" title="&Egrave; necessario caricare prima l'allegato 3." />
									  <?}
									}
								}
								elseif ($idoperatore==$_SESSION['UTENTE']->get_userid() || $_SESSION['UTENTE']->is_root() || $_COOKIE['unlock_controls'])
								{
									if(	($allegato_4_da_firmare == 'n' && $firmato_digitalmente == 's') ||
										($allegato_4_da_firmare == 's' && $firmato_digitalmente == 'n') )
									{
										if ($allegato3 !== "" && $allegato3 !== NULL) { ?>
											<a href="javascript:void(0)" onclick="javascript:small_window('get_allegato_istanza_popup.php?campo=allegato4&idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>');"><img src="images/add-file.png" /></a>
									  <?} else {?>
											<img src="images/add-file-disabled.png" title="&Egrave; necessario caricare prima l'allegato 3." />
									  <?}
									}
								}
								
								if ($allegato4 !== "" && $allegato4 !== NULL) {
									echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato4.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
									if(($passati_due_gg == 'n'  && !$chiusa) || $_SESSION['UTENTE']->is_root() ) { ?>
										<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato 4 attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato4')" ><img src="images/remove.png" /></a>
								 <? }
								}
								
							} else {
								if ($allegato4 !== "" && $allegato4 !== NULL) {
									echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato4.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
									if(($passati_due_gg == 'n'  && !$chiusa) || $_SESSION['UTENTE']->is_root() ) { ?>
										<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato 4 attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato4')" ><img src="images/remove.png" /></a>
								 <? }
								}
							}
						}
						elseif($documento_cc_infermieristica_modificabile == 's')
						{
							if ($allegato4 !== "" && $allegato4 !== NULL) {
								echo '&nbsp;&nbsp;&nbsp;<a href="./modelli_word/allegato/'.$allegato4.'" target="_blank"><img src="images/icons/page_white_acrobat.png" /></a>';
								if(($passati_due_gg == 'n' && $allegato4 == "") || $_SESSION['UTENTE']->is_root() || $_COOKIE['unlock_controls'] ) { ?>
									<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'allegato attuale?'))cancella_allegato('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','allegato4')" ><img src="images/remove.png" /></a>
							 <? }
							}
						}
						?>			
					  </td>
<?					}
?>

					  <td <?=$td_style?> > 
					  <? if($_COOKIE['unlock_controls'] || 
							get_permesso_modulo_vis($idmodulo1,$_SESSION['UTENTE']->get_gid()) ||
							$documento_cc_infermieristica_modificabile == 's')
						{
							if ($idoperatore==$_SESSION['UTENTE']->get_userid()) { ?>
								<a href="javascript:void(0)" onclick="javascript:small_window('get_note_istanza_popup.php?idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>&w', 800, 600);">
								<? if ($note == NULL) echo '<img src="images/impegnativa.png" /></a>'; else echo '<img src="images/icons/page_html.png" /></a>';
							} else { ?>
								<a href="javascript:void(0)" onclick="javascript:small_window('get_note_istanza_popup.php?idcartella=<?=$idcartella?>&idpaziente=<?=$idpaziente?>&idmoduloversione=<?=$idmoduloversione?>&idinserimento=<?=$idinserimento?>&imp=<?=$impegnativa?>&r', 800, 600);">
								<? if ($note == NULL) echo '<img src="images/impegnativa.png" /></a>'; else echo '<img src="images/icons/page_html.png" /></a>';
							} 
						}?>
					  </td>
					  
					  
					  <td <?=$td_style?> >
						<?
						$io = strpos($row['modello_word'],".");						
						if (($_COOKIE['unlock_controls'] || 
							$opeins == $_SESSION['UTENTE']->get_userid() || 
							get_permesso_modulo_vis($idmodulo1,$_SESSION['UTENTE']->get_gid()) ||
							$documento_cc_infermieristica_modificabile == 's') && $io != "")
						{
							if($secondo_allegato == 1) {
								if ($_COOKIE['unlock_controls'] || $allegato == NULL || $allegato2 == NULL || $_SESSION['UTENTE']->is_root()) { ?>
									<a href="stampa_modulo.php?idcartella=<?=$idcartella?>&idinserimento=<?=$idinserimento?>&idpaziente=<?=$idpaziente?>&idimpegnativa=<?=$impegnativa?>&idimpegnativaassociata=<?=$id_impegnativa_associata?>&idmodulopadre=<?=$idmodulo1?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>"><img src="images/printer.png" /></a>
								<? }else {
									echo("&nbsp;");
								}
							} elseif($secondo_allegato == 0) {
								if ($_COOKIE['unlock_controls'] || $allegato == NULL || $_SESSION['UTENTE']->is_root()) { ?>
									<a href="stampa_modulo.php?idcartella=<?=$idcartella?>&idinserimento=<?=$idinserimento?>&idpaziente=<?=$idpaziente?>&idimpegnativa=<?=$impegnativa?>&idimpegnativaassociata=<?=$id_impegnativa_associata?>&idmodulopadre=<?=$idmodulo1?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>"><img src="images/printer.png" /></a>
								<? }else {
									echo("&nbsp;");									
								}
							}
						} else {
							echo("&nbsp;");
						}
						
						
						if(($_COOKIE['unlock_controls'] || $id_ope_validazione == $_SESSION['UTENTE']->get_userid() || $_SESSION['UTENTE']->is_ds() || $_SESSION['UTENTE']->is_root() ) && !$chiusa ) 
						{
							
							$can_validate = false;
							
							if($secondo_allegato == 1) {
								if($allegato != NULL && $allegato2 != NULL) {
									$can_validate = true;
								}
							} elseif($allegato != NULL) {
								$can_validate = true;
							}
							
							if($validazione == 'a') { 
								if($can_validate) { ?>								
								<td style="background-color: red">
									<a href="#" onclick="javascript:if(confirm('Sei sicuro di voler marcare l\'istanza corrente come NON VALIDA?')) valida_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','n')" ><img src="images/close.png" title="Marca istanza come NON VALIDA."/></a>
									<a href="#" style="margin-left:15px" onclick="javascript:if(confirm('Sei sicuro di voler marcare l\'istanza corrente come VALIDA?'))		valida_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','s')" ><img src="images/spunto.png"  title="Marca istanza come VALIDA."/></a>
							<?	if($_SESSION['UTENTE']->is_root() ) {
									echo '<img src="images/scadenza_non_rispettata.png" style="margin-left:15px" title="Istanza in attesa di validazione da parte del Dott./Dott.ssa '.$ope_validazione.'" />';
								} ?>
								</td>								
							<?	} else {
									echo '<td style="background-color: red"><img src="images/alert_scadenza.png" title="Istanza in attesa di allegati" /></td>';
								}
						    } elseif($validazione == 's') { ?> 
								<td <?=$td_style?> >
									<!-- <<img src="images/stato_ok.png" title="Istanza VALIDATA da <=trim($ope_validazione)?> il <=$data_validazione?>" /> -->
									<img src="images/stato_ok.png" title="Istanza VALIDATA da <?=trim($ope_validazione)?>" />
								<?	if(!$can_validate) { echo '<img src="images/alert_scadenza.png" title="Allegati non presenti" />'; }
									if($_SESSION['UTENTE']->is_root()) { ?>
									<a href="#" style="margin-left:15px" onclick="javascript:if(confirm('Sei sicuro di voler forzare l\'istanza corrente come NON VALIDA?')) valida_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','n')" ><img src="images/close.png" title="Forza istanza come NON VALIDA."/></a>
								<? } ?>
								</td>								
						<? } elseif($validazione == 'n') { ?> 
								<td <?=$td_style?> >
									<img src="images/stato_red.png" title="Istanza NON VALIDATA da <?=$ope_validazione?> il <?=$data_validazione?>" />
								<?	if(!$can_validate) { echo '<img src="images/alert_scadenza.png" title="Allegati non presenti" />'; }
									if($_SESSION['UTENTE']->is_root()) { ?>
									<a href="#" style="margin-left:15px" onclick="javascript:if(confirm('Sei sicuro di voler forzare l\'istanza corrente come VALIDA?'))		valida_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','s')" ><img src="images/spunto.png"  title="Forza istanza come VALIDA."/></a>
								<? } ?>
								</td>
						 <? }
						} 
						//elseif($id_ope_validazione !== $_SESSION['UTENTE']->get_userid() ) {
						else {
								if($validazione == 'a') echo '<td style="background-color: red"><img src="images/scadenza_non_rispettata.png" title="Istanza in attesa di validazione da parte del Dott./Dott.ssa '.$ope_validazione.'" /></td>';
							elseif($validazione == 's') echo '<td '.$td_style.'><img src="images/stato_ok.png" title="Istanza VALIDATA dal Dott./Dott.ssa '.$ope_validazione.' il '.$data_validazione.'" /></td>';
							elseif($validazione == 'n') echo '<td '.$td_style.'><img src="images/stato_red.png" title="Istanza NON VALIDATA dal Dott./Dott.ssa '.$ope_validazione.' il '.$data_validazione.'" /></td>';
						}
						//else echo "<td ".$td_style.">&nbsp;</td>" ?>
					  
						
						<td <?=$td_style?> >
						<?
						if ($_COOKIE['unlock_controls'] || 
							(
								$idoperatore==$_SESSION['UTENTE']->get_userid() 
								&& $documento_cc_infermieristica_modificabile == 'n' 
								&& get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid()) 
								&& !$chiusa 
								&& !$out2days 
								&& empty($dt_exec_fse)
							)
							|| $_SESSION['UTENTE']->is_root()
						) 
						{
							if($secondo_allegato == 1) {
								if ($_COOKIE['unlock_controls'] || $allegato == NULL || $allegato2 == NULL || $_SESSION['UTENTE']->is_root() ) { ?>
									<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'istanza corrente?'))cancella_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>')" ><img src="images/remove.png" /></a>
								<? }else {
									echo("&nbsp;");
								}
							} elseif($secondo_allegato == 0) {
								if ($_COOKIE['unlock_controls'] || $allegato == NULL || $_SESSION['UTENTE']->is_root()) { ?>
									<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler cancellare l\'istanza corrente?'))cancella_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>')" ><img src="images/remove.png" /></a>
								<? }else {
									echo("&nbsp;");
								}
							}
                        } else if(!empty($dt_exec_fse)) { ?>
							<a href="#" ><img src="images/remove_grayscale.png" title='Modulo inviato al FSE in data <?= formatta_data($dt_exec_fse) ?>' /></a>
						<? } else {
							echo("&nbsp;");
						}?>
						</td>	
					  


					<?if( ($_COOKIE['unlock_controls'] && $_SESSION['UTENTE']->is_root()) || $_SESSION['UTENTE']->is_root() ) { ?>
						<td <?=$td_style?> >
							<?if($annullamento == 'n') { ?>
								<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler ANNULLARE l\'istanza corrente?'))annulla_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','s')" ><img src="images/remove_grayscale.png" title="Annulla istanza" /></a>
						 <? } elseif($annullamento == 's') { ?>
								<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler RIMUOVERE L\'ANNULLAMENTO dell\'istanza corrente?'))annulla_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','n')" ><img src="images/spunto.png" title="Istanza annullata da <?=$ope_annullamento?> il <?=$data_annullamento?>."  /></a>
						  <?}?>					
						</td>
					<?}?>
						<td <?=$td_style?> >
							<? if ($_COOKIE['unlock_controls'] 
									or ($modulo_duplucabile &&
										((($idoperatore==$_SESSION['UTENTE']->get_userid()) and get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid()) and !$chiusa) 
										  or (($_SESSION['UTENTE']->get_gid() == 1015 || $_SESSION['UTENTE']->get_gid() == 1020 || $_SESSION['UTENTE']->is_ds()) and !$chiusa)
										  or get_flag_edita_modulo($idmodulo1, $vers))
										  or $_SESSION['UTENTE']->is_root()
									)									
								) 
								{ ?>
									<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler DUPLICARE l\'istanza corrente?'))duplica_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','n')" ><img src="images/icon_mostramenu.png" /></a>
									<a href="#"  onclick="javascript:if (confirm('Sei sicuro di voler DUPLICARE l\'istanza corrente in modo da applicare la firma digitale successivamente?'))duplica_modulo('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$idmodulo1?>','<?=$impegnativa?>','s')" ><img src="images/icon_mostramenu_lock.png" /></a>
							<?  } else {
									echo("&nbsp;");
								}
							?>
						</td>
					</tr> 					
			<?
	}
?>
</tbody>
</table>

</div>

<script type="text/javascript" id="js">

	function add_modulo(idcartella,idpaziente,idmodulo)
	{
	$("#layer_nero2").toggle();
	$('#sanitaria').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo;
	$("#sanitaria").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function edita_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,impegnativa){
		div='#'+divagg;
		$("#layer_nero2").toggle();
		$(div).innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=edita_modulo&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&impegnativa="+impegnativa;
		$(div).load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	}
	
	function converti_firma_istanza(id_istanza_testata, converti_in, idcartella, idpaziente, idmoduloversione, idimpegnativa, duplicata) {
		alert("Per abilitare la funzionalità contatta il nostro reparto commerciale.");
		/*
		$("#layer_nero2").toggle();
		$("#sanitaria").innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=converti_firma_istanza&id_istanza_testata="+id_istanza_testata+"&converti_in="+converti_in+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmoduloversione="+idmoduloversione+"&idimpegnativa="+idimpegnativa+"&duplicata="+duplicata;

		$("#sanitaria").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
		 */
	}
		
	function cancella_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,idmodulo1,idimpegnativa,action)
	{
		div='#'+divagg;
		$("#layer_nero2").toggle();
		$(div).innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=cancella_modulo&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&idmodulo1="+idmodulo1+"&idimpegnativa="+idimpegnativa;
		$(div).load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	}
	
	function cancella_allegato(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,idmodulo1,idimpegnativa,tipoallegato){
		div='#'+divagg;
		$("#layer_nero2").toggle();
		$(div).innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=cancella_allegato&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&idmodulo1="+idmodulo1+"&idimpegnativa="+idimpegnativa+"&tipoallegato="+tipoallegato;
		$(div).load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	}
	
	function valida_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,idmodulo1,idimpegnativa,action)
	{
		div='#'+divagg;
		$("#layer_nero2").toggle();
		$(div).innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=valida_modulo&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&idmodulo1="+idmodulo1+"&idimpegnativa="+idimpegnativa+"&action="+action;
		$(div).load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	}

	function annulla_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,idmodulo1,idimpegnativa,action)
	{
		alert("Per abilitare la funzionalità contatta il nostro reparto commerciale.");
		/*
		div='#'+divagg;
		$("#layer_nero2").toggle();
		$(div).innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=annulla_modulo&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&idmodulo1="+idmodulo1+"&idimpegnativa="+idimpegnativa+"&action="+action;
		$(div).load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
		 */
	}
	
	function duplica_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,idmodulo1,idimpegnativa,firmato_digitalmente)
	{
		div='#'+divagg;
		$("#layer_nero2").toggle();
		$(div).innerHTML="";
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=duplica_modulo&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&idmodulo1="+idmodulo1+"&idimpegnativa="+idimpegnativa+"&firmato_digitalmente="+firmato_digitalmente;
		$(div).load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-5 .tabs-selected").removeClass('tabs-selected');
		 });
	}
	
	// SE PREMO SUL LUCCHETTO APRO UN POPUP PER VERIFICARE LA PASSWORD, SE VIENE VERIFICATA SBLOCCO PER 10 MINUTI I CONTROLLI
	function sblocca_istanza(idcartella,idpaziente,idregime,idmodulo,cart,imp) {
		alert("Per abilitare la funzionalità contatta il nostro reparto commerciale.");
		//small_window("popup_confirm_password.php?idcartella=" + idcartella + "&idpaziente=" + idpaziente + "&idregime=" + idregime + "&idmodulo=" + idmodulo + "&cart=" + cart + "&imp=" + imp, 400, 275);
	}	
	
	function visualizza_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg,call,impegnativa)
	{
	div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_modulo&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&call="+call+"&impegnativa="+impegnativa;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
		
	function ritorna(idcartella,idpaziente,idmodulo,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_cartella&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function ritorna_istanze(cartella,idcartella,idpaziente,idmodulo,divagg,call,idimp)
	{
		if(call=="ins")	divagg="cartella_clinica";
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	if(call=="ins")
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=anteprima_istanze_cartella_ins&idcartella="+idcartella+"&idpaziente="+idpaziente+"&cartella="+cartella.replace(" ", "%20");		
		else
		pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&cartella="+cartella.replace(" ", "%20")+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&impegnativa="+idimp;		
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });

	}
	
	
	function ritorna_visite(idcartella,idpaziente,idmodulo,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_cartelle.php?do=addvisita&idcartella="+idcartella+"&id="+idpaziente+"&idmodulo="+idmodulo;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	}


	function firma_modulo_digitalmente(idcartella,idinserimento,idpaziente,idmodulo1,idmoduloversione,idimpegnativa,idregime,tipoallegato,allegato_successivo,rtf_landscape)
	{
		alert("Per abilitare la funzionalità contatta il nostro reparto commerciale.");
		/*
	 	if(document.cookie.match(/^(.*;)?\s*PIN_INFOCERT\s*=\s*[^;]+(.*)?$/))
		{
			var pin = '<?=''//$_COOKIE['PIN_INFOCERT']?>';
			
			$.ajax({
				type: "POST",
				url: "stampa_modulo.php?idcartella="+idcartella+"&idinserimento="+idinserimento+"&idpaziente="+idpaziente+"&idimpegnativa="+idimpegnativa+"&idimpegnativaassociata=&idmodulopadre="+idmodulo1+"&ngid=1&conv_pdf",
				success: function(response) {
					small_window("http://192.168.30.100/firmadigitale/index.php?idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulopadre="+idmodulo1+"&idmoduloversione="+idmoduloversione+"&idinserimento="+idinserimento+"&idimpegnativa="+idimpegnativa+"&idregime="+idregime+"&tipoallegato="+tipoallegato+"&allegato_successivo="+allegato_successivo+"&rtf_landscape="+rtf_landscape+"&rtf=" + response +"&uid=<?=$_SESSION["UTENTE"]->get_userid()?>&firma_cartella=0&usephp=7&p="+pin, 800, 600);
				}
			});
			
	 	} else {
			alert("Non hai inserito il PIN della tua firma digitale all'accesso in ReMed.");
		} 
		*/
	}
	
	function firma_modulo_digitalmente_TEST(idcartella,idinserimento,idpaziente,idmodulo1,idmoduloversione,idimpegnativa,idregime,tipoallegato,allegato_successivo,rtf_landscape)
	{
	 	if(document.cookie.match(/^(.*;)?\s*PIN_INFOCERT\s*=\s*[^;]+(.*)?$/))
		{
			var pin = '<?=$_COOKIE['PIN_INFOCERT']?>';
			
			$.ajax({
				type: "POST",
				url: "stampa_modulo.php?idcartella="+idcartella+"&idinserimento="+idinserimento+"&idpaziente="+idpaziente+"&idimpegnativa="+idimpegnativa+"&idimpegnativaassociata=&idmodulopadre="+idmodulo1+"&ngid=1&conv_pdf",
				success: function(response) {
					small_window("http://192.168.30.100/firmadigitale/index_test.php?idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulopadre="+idmodulo1+"&idmoduloversione="+idmoduloversione+"&idinserimento="+idinserimento+"&idimpegnativa="+idimpegnativa+"&idregime="+idregime+"&tipoallegato="+tipoallegato+"&allegato_successivo="+allegato_successivo+"&rtf_landscape="+rtf_landscape+"&rtf=" + response +"&uid=<?=$_SESSION["UTENTE"]->get_userid()?>&firma_cartella=0&usephp=7&p="+pin, 800, 600);
				}
			});
			
	 	} else {
			alert("Non hai inserito il PIN della tua firma digitale all'accesso in ReMed.");
		} 
	}
	
	function firma_modulo_con_fea(idcartella,idinserimento,idpaziente,idmodulo1,idmoduloversione,idimpegnativa,idregime,tipoallegato,secondoallegato)
	{
		alert("Per abilitare la funzionalità contatta il nostro reparto commerciale.");
		/*
		$.ajax({
			type: "POST",
			url: "stampa_modulo.php?idcartella="+idcartella+"&idinserimento="+idinserimento+"&idpaziente="+idpaziente+"&idimpegnativa="+idimpegnativa+"&idimpegnativaassociata=&idmodulopadre="+idmodulo1+"&ngid=1&conv_pdf",
			success: function(response) {
				small_window("https://firmabc.wbb.it/fea/index.php?idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulopadre="+idmodulo1+"&idmoduloversione="+idmoduloversione+"&idinserimento="+idinserimento+"&idimpegnativa="+idimpegnativa+"&idregime="+idregime+"&tipoallegato="+tipoallegato+"&secondoallegato="+secondoallegato+"&rtf=" + response +"&pdf=&uid=<?=$_SESSION["UTENTE"]->get_userid()?>&usephp=7&source=remed", 800, 600);
			}
		});
		*/
	}
	
	function riscarica_modulo_firmato_fea(idcartella,idinserimento,idpaziente,idmodulo1,idmoduloversione,idimpegnativa,idregime,tipoallegato,secondoallegato,id_istanza_testata,id_doc_confirmo_fea)
	{
		small_window("https://firmabc.wbb.it/fea/riscarica_doc.php?idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulopadre="+idmodulo1+"&idmoduloversione="+idmoduloversione+"&idinserimento="+idinserimento+"&idimpegnativa="+idimpegnativa+"&idregime="+idregime+"&tipoallegato="+tipoallegato+"&secondoallegato="+secondoallegato+"&id_istanza_testata="+id_istanza_testata+"&id_doc_confirmo_fea=" + id_doc_confirmo_fea + "&uid=<?=$_SESSION["UTENTE"]->get_userid()?>&usephp=7&source=remed", 500, 400);
	}
	

	function salva_e_allega_modulo(idcartella,idinserimento,idpaziente,idmodulo1,idmoduloversione,tipoallegato,secondoallegato)
	{
		$.ajax({
			type: "GET",
			url: "stampa_modulo.php?idcartella="+idcartella+"&idinserimento="+idinserimento+"&idpaziente="+idpaziente+"&idimpegnativa=&idimpegnativaassociata=&idmodulopadre="+idmodulo1+"&ngid=1&conv_e_allega_pdf",
			async: false,
			success: function(response) 
			{
				if(response !== "")
				{
					small_window("http://192.168.30.100/firmadigitale/converti_file_singolo.php?idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulopadre="+idmodulo1+"&idmoduloversione="+idmoduloversione+"&idinserimento="+idinserimento+"&uid=<?=$_SESSION["UTENTE"]->get_userid()?>&usephp=7&tipoallegato="+tipoallegato+"&secondoallegato="+secondoallegato+"&dirfilename=" + response, 800, 600);
				}
			}
		});
	}

<?
	if($allegato_1_da_firmare == 's')	
		$tipo_allegato = 'allegato';
	elseif($allegato_2_da_firmare == 's')
		$tipo_allegato = 'allegato2';
		
	if($firmadigitaledocumento == 'a') 
	{ ?>
		firma_modulo_digitalmente(<?=$idcartella?>,<?=$idultimoinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'<?=$tipo_allegato?>',<?=$secondo_allegato?>,<?=$rtf_landscape?>);
<?  } 
	elseif($fea_documento == 'a') 
	{ ?>
		firma_modulo_con_fea(<?=$idcartella?>,<?=$idultimoinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'<?=$impegnativa?>',<?=$idregime?>,'<?=$tipo_allegato?>',<?=$secondo_allegato?>);
<? 	}

	if($salvaeallega == 's') 
	{  ?>
		salva_e_allega_modulo(<?=$idcartella?>,<?=$idultimoinserimento?>,<?=$idpaziente?>,<?=$idmodulo1?>,<?=$idmoduloversione?>,'allegato',<?=$secondo_allegato?>);
<?  }  ?>
	

</script>

<?

}



function edita_modulo()
{

$idcartella=$_REQUEST['idcartella'];
$cartella=$_REQUEST['cartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idinserimento=$_REQUEST['idinserimento'];
$impegnativa=$_REQUEST['impegnativa'];

$conn = db_connect();
$query="select id,idmodulo,nome,replica from moduli where id=$idmoduloversione";
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$nome_modulo=$row1['nome'];
$id_modulo_padre=$row1['idmodulo'];
$replica=$row1['replica'];
mssql_free_result($rs1);


$query="Select * from utenti_cartelle WHERE id=$idcartella";
$rs = mssql_query($query, $conn);	
if($row_m=mssql_fetch_assoc($rs)) $idregime=$row_m['idregime'];
mssql_free_result($rs);

if ($idcartella=="")$idcartella=0;
if ($impegnativa=="")
	$imp_query="id_impegnativa=NULL";
	else
	$imp_query="id_impegnativa=$impegnativa";
$query="select * from re_istanze_moduli where id_cartella=$idcartella and id_modulo_padre=$id_modulo_padre and id_inserimento=$idinserimento and $imp_query";

$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$datains=formatta_data($row1['datains']);

$orains=$row1['orains'];
$operatore=$row1['nome'];
mssql_free_result($rs1);

$arr_valori	= array();
$query		= "select * from re_moduli_valori where id_inserimento=$idinserimento and id_cartella=$idcartella and id_modulo_padre=$id_modulo_padre and $imp_query";
$rs1 		= mssql_query($query, $conn);
if(!$rs1) error_message(mssql_error());
while($row1 = mssql_fetch_assoc($rs1)){	
	$campo				= $row1['idcampo'];
	$arr_valori[$campo]	= $row1['valore'];
}

$query="SELECT peso, tipo, editabile, obbligatorio, sqlcombo, status, cancella, idcampo, etichetta, classe, segnalibro, idmoduloversione
		FROM campi WHERE    (idmoduloversione = $idmoduloversione) ORDER BY peso";
//echo($query);

$rs1 = mssql_query($query, $conn);
if(!$rs1) error_message(mssql_error());

?>
<script>inizializza();</script>

<div class="titoloalternativo">
            <h1>cartella: <?=$cartella?></h1>
	        <!--<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
</div>
<div class="titoloalternativo">
            <h1>modifica modulo: <?=$nome_modulo?></h1>
</div>
<!-- qui va il codice dinamico dei campi -->
	
<form id="myForm" method="post" enctype="multipart/form-data" name="form0" action="re_pazienti_sanitaria_POST.php">
	
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>
	<input type="hidden" name="action" value="update_modulo" />
	<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
	<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
	<input type="hidden" name="idinserimento" value="<?=$idinserimento?>" />
	<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
	<input type="hidden" name="impegnativa" value="<?if(($replica==2)or($replica==3)) echo("si"); else echo("no");?>" />
	<input type="hidden" name="salvaeallega" id="salvaeallega" value="n" />
	
	<div class="blocco_centralcat">
		<?
		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		$i=1;
	
	if(($replica==2)or($replica==3)){?>		
		<div class="rigo_mask">
            <div class="testo_mask">associa alla pratica</div>
            <div class="campo_mask">
                <select name="idimpegnativa" class="scrittura" style="width:auto;">
				<?
				$query_1="SELECT dbo.re_pazienti_impegnative.* FROM dbo.re_pazienti_impegnative 
						WHERE (dbo.re_pazienti_impegnative.idimpegnativa=$impegnativa)";				
				$rs11 = mssql_query($query_1, $conn);
				while($row11 = mssql_fetch_assoc($rs11))  {
						$idimpegnativa=$row11['idimpegnativa'];
						$data_auth_asl=formatta_data($row11['DataAutorizAsl']);
						$prot_auth_asl=$row11['ProtAutorizAsl'];
						$regime=$row11['regime'];
					?>
					<option value="<?=$idimpegnativa?>"><?=$prot_auth_asl." ".$data_auth_asl." - ".$regime?></option>
					<?
					}
					mssql_free_result($rs11);
				?>
				</select>
            </div>
        </div>		
		<?}			
		while($row1 = mssql_fetch_assoc($rs1)){

			$idcampo= $row1['idcampo'];
			$etichetta= $row1['etichetta'];
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];				
			if (key_exists($idcampo, $arr_valori)) {
				$valore=$arr_valori[$idcampo];
			}
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
		?>
		
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<div class="etichetta_campi"><?=pulisci_lettura($etichetta)?></div>
			<input type="hidden" class="scrittura" name="<?=$idcampo?>" >				
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if(($multi==1)or($multi==2)) echo($stile);?>>  
           <?if($tipo==7){?>
				<div class="testo_mask"><?=pulisci_lettura($etichetta)?> <a onclick="document.getElementById('file_box<?=$idcampo?>').style.display='block';document.getElementById('file_name').style.display='none';" class="cambia_valore">cambia <?=pulisci_lettura($etichetta)?></a></div>
			  <?} else{?>
				<div class="testo_mask"><?=pulisci_lettura($etichetta)?></div>    
			  <?}?>            
			<div class="campo_mask <?=$classe?>">
                <?if(($tipo==1)or($tipo==8)){
				if ($editabile=='y'){?>
					<input type="text" class="scrittura" name="<?=$idcampo?>" value="<?=pulisci_lettura($valore)?>"  >
					<?}else{
						echo(pulisci_lettura($valore));
					}
				  }
				     elseif($tipo==2)
					 {
					 if ($editabile=='y'){
					 $valore=str_replace("?","'",$valore);
					 ?>
					<textarea class="scrittura_campo" name="<?=$idcampo?>" ><?=pulisci_lettura($valore)?></textarea>
					<?}else{
						echo(pulisci_lettura($valore));
					}
				  }
				   elseif(($tipo==3) or($tipo==6))
					 {
					if ($editabile=='y'){?>
					<input type="text" pattern="<?php echo ITALIAN_DATE_REGEX; ?>" class="scrittura campo_data" name="<?=$idcampo?>" value="<?=pulisci_lettura($valore)?>"  >
					<?}else{
						echo(pulisci_lettura($valore));?>
						<input type="hidden" class="scrittura campo_data" name="<?=$idcampo?>" value="<?=pulisci_lettura($valore)?>"  >
						<?
					}
				  }
				  elseif($tipo==7)
					{
					
					?>
						<div id="file_name" style="display:<?if(trim($valore)!=""){?>block;<?}else{?>none;<?}?>"><a target="_new" href="load_file.php?filename=<?=$valore?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>" style="text-decoration:none;" ><img src="images/view.png" /> <?=$valore?></a>
						<input type="hidden" class="scrittura" name="<?=$idcampo?>_file" value="<?=$valore?>" >		
						</div>					
					  <div id="file_box<?=$idcampo?>" class="campo_mask nomandatory" style="display:none">
                            <input type="file" class="scrittura_campo" name="<?=$idcampo?>" value="<?=$valore?>">
                        </div>
					<?						
					}		
				  elseif($tipo==4)
				  {
					$query2="select * from re_moduli_combo where idcampo=$idcampo order by peso";					
					$rs2 = mssql_query($query2, $conn);
					if(!$rs2) error_message(mssql_error());
						
					if(($multi==1)or($multi==2)) {
						$xy		= 1;
						$valore = split(";",$valore);
						
						while ($row2 = mssql_fetch_assoc($rs2))
						{
							$valore1	= $row2['valore'];
							$etichetta	= $row2['etichetta'];
							
							if (in_array($valore1, $valore))
								$sel="checked";
							else
								$sel='';
							
							if ((($sel=='') and($row2['stato']=='1')) or ($sel!='')) { ?>							
								<div style="float:left; padding:0 10px 0 0;clear:both;"><input <?=$sel?> type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=pulisci_lettura($valore1)?>" /> <?=pulisci_lettura($etichetta)?></div>
						<?	}
							$xy++;
						}						
					} elseif($multi == 3) { 
						$valore = split(";",$valore); ?>
						<select class="scrittura" name="<?=$idcampo?>[]" multiple style="width:auto; min-width: 60px;"> 
				<?	} else { ?>
						<select class="scrittura" name="<?=$idcampo?>" style="width:auto;">
							<option value="">Selezionare un Valore</option><?
					}								
					
					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$valore1=$row2['valore'];
						$etichetta1=$row2['etichetta'];
						
						if($multi == 3 && in_array($valore1, $valore)) {
							$sel="selected";
						} elseif (trim($valore)==trim($valore1)) {
							$sel="selected";				
						} else {
							$sel='';
						}
						
						if ((($sel=='') and($row2['stato']=='1')) or ($sel!='')){ ?>
							<option <?=$sel?> value="<?=$valore1?>"><?=$etichetta1?></option>
					<?	}
					}?>
					</select>
				  <?}				  
				    elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";					
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$xy=1;
						$valore=split(";",$valore);					
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								
									if (in_array($valore1,$valore))
									$sel="checked";
									else
									$sel='';
					if ((($sel=='') and($row2['stato']=='1')) or ($sel!='')){
					?>							
					<div style="float:left; padding:0 10px 0 0;clear:both;"><input <?=$sel?> type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore1?>" /> <?=pulisci_lettura($etichetta)?></div>
					<?
					}
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
						$valore1=$row2['valore'];
						$etichetta1=$row2['etichetta'];
						$v=(int)($valore);
						if ($idcampocombo==$v)
						{
						$sel="selected";					
						}
						else
						$sel='';						
					?>
					<option <?=$sel?> value="<?=$idcampocombo?>"><?=$etichetta1?></option>
					<?
					
					$rinc=rincorri_figlio($idc,$idcampocombo,"",$v);
					}
					?>
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
		<? 
		// se il modulo appartiene a quelli che devono avere il btn "salva e allega" (che crea il PDF dal RTF e lo associa alla CC)

			$moduli_con_salva_e_allega = array(218,219,220,222);
			if (in_array($id_modulo_padre, $moduli_con_salva_e_allega)) { 
?>
			<input onclick="document.getElementById('salvaeallega').value='s';" type="submit" title="salva e allega" value="salva e allega" class="button_salva"/>
		 <? }
			else 
			{ ?>
			<input type="submit" title="salva" value="salva" class="button_salva"/>
			<?
			}?>
		</div>
	</div>
	</form>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {	
		
		// SCRIPT ESEGUITI IN FASE DI MODIFICA DI ISTANZA GIA' COMPILATA DI UN MODULO
		
		var segnalibri_rtf = "metodologie_operative_ajax_ms|elenco_obiettivi_ajax_ms|misure_desito_ajax_ms|risultato_ms|raggiungimento_risultati_ms|";		// Cartella Clinica (ex26) - Programma Riabilitativo - Mobilit� e Spostamenti
			segnalibri_rtf+= "metodologie_operative_ajax_fs|elenco_obiettivi_ajax_fs|misure_desito_ajax_fs|risultato_fs|raggiungimento_risultati_fs|";		// Cartella Clinica (ex26) - Programma Riabilitativo - Funzioni sensomotorie
			segnalibri_rtf+= "metodologie_operative_ajax_cr|elenco_obiettivi_ajax_cr|misure_desito_ajax_cr|risultato_cr|raggiungimento_risultati_cr|";		// Cartella Clinica (ex26) - Programma Riabilitativo - Comunicativo - Relazionali
			segnalibri_rtf+= "metodologie_operative_ajax_cc|elenco_obiettivi_ajax_cc|misure_desito_ajax_cc|risultato_cc|raggiungimento_risultati_cc|";		// Cartella Clinica (ex26) - Programma Riabilitativo - Cognitivo - Comportamentale
			segnalibri_rtf+= "metodologie_operative_ajax_acp|elenco_obiettivi_ajax_acp|misure_desito_ajax_acp|risultato_acp|raggiungimento_risultati_acp|";	// Cartella Clinica (ex26) - Programma Riabilitativo - Autonomia cura persona
			segnalibri_rtf+= "metodologie_operative_ajax_aea|elenco_obiettivi_ajax_aea|misure_desito_ajax_aea|risultato_aea|raggiungimento_risultati_aea|";	// Cartella Clinica (ex26) - Programma Riabilitativo - Area emotiva affettiva
			segnalibri_rtf+= "metodologie_operative_ajax_rrs|elenco_obiettivi_ajax_rrs|misure_desito_ajax_rrs|risultato_rrs|raggiungimento_risultati_rrs|";	// Cartella Clinica (ex26) - Programma Riabilitativo - Reinserimento sociale
			
			segnalibri_rtf+= "esiti_raggiungimento_risultati_fms|";				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni motorie sensoriali
			segnalibri_rtf+= "esiti_raggiungimento_risultati_fvs|";				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni vescico sfinteriche
			segnalibri_rtf+= "esiti_raggiungimento_risultati_fcr|";				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni cardio respiratorie
			segnalibri_rtf+= "esiti_raggiungimento_risultati_fd|";				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni digestive
			segnalibri_rtf+= "esiti_raggiungimento_risultati_fccl|";			// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni cognitive comportamentali e del linguaggio
			segnalibri_rtf+= "esiti_raggiungimento_risultati_ifcg|";			// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Informazione formazione care giver
			segnalibri_rtf+= "diagnosi|";										// Cartella Clinica (ex26 e legge8) - Programma Riabilitativo
			
			segnalibri_rtf+= "testo_val_fms|";						// Cartella Clinica (ex26 - Res. Est.) - Valutazione/osservazione Programma Riabilitativo Funzioni motorie sensoriali
			segnalibri_rtf+= "testo_val_fvs|";						// Cartella Clinica (ex26 - Res. Est.) - Valutazione/osservazione Programma Riabilitativo Funzioni vescico sfinteriche
			segnalibri_rtf+= "testo_val_fcr|";						// Cartella Clinica (ex26 - Res. Est.) - Valutazione/osservazione Programma Riabilitativo Funzioni cardio respiratorie
			segnalibri_rtf+= "testo_val_fd|";						// Cartella Clinica (ex26 - Res. Est.) - Valutazione/osservazione Programma Riabilitativo Funzioni digestive
			segnalibri_rtf+= "testo_val_fccl|";						// Cartella Clinica (ex26 - Res. Est.) - Valutazione/osservazione Programma Riabilitativo Funzioni cognitive comportamentali e del linguaggio
			segnalibri_rtf+= "testo_val_ifcg|";						// Cartella Clinica (ex26 - Res. Est.) - Valutazione/osservazione Programma Riabilitativo Informazione formazione care giver
			
			
			segnalibri_rtf+= "suap_esiti_obiettivi_raggiunti_aifvb|";	// Cartella Clinica (SUAP) - Area intervento Funzioni Vitali di Base e Stabilita' Internistica
			segnalibri_rtf+= "suap_esiti_obiettivi_raggiunti_aiacp|";	// Cartella Clinica (SUAP) - Area intervento autonomia cura persona
			segnalibri_rtf+= "suap_esiti_obiettivi_raggiunti_aifmt|";	// Cartella Clinica (SUAP) - Area intervento funzioni mobilita trasferimenti
			segnalibri_rtf+= "suap_esiti_obiettivi_raggiunti_aifsm|";	// Cartella Clinica (SUAP) - Area intervento funzioni senso motorie
			segnalibri_rtf+= "suap_esiti_obiettivi_raggiunti_aiaa|";	// Cartella Clinica (SUAP) - Area intervento altre aree
			segnalibri_rtf+= "suap_esiti_obiettivi_raggiunti_aiifc|";	// Cartella Clinica (SUAP) - Area intervento informazione formazione caregiver
			segnalibri_rtf+= "suap_esiti_obiettivi_raggiunti_aiprs|";	// Cartella Clinica (SUAP) - Area intervento partecipazione e reinserimento sociale			
			
			segnalibri_rtf+= "suap_testo_val_fvb|"		// Cartella Clinica (SUAP) - Valutazione osservazioni funzioni di base e stabita' internistica
			segnalibri_rtf+= "suap_testo_val_acp|"		// Cartella Clinica (SUAP) - Valutazione osservazioni autonomia cura persona
			segnalibri_rtf+= "suap_testo_val_fmt|"		// Cartella Clinica (SUAP) - Valutazione osservazioni funzioni mobilita trasferimenti
			segnalibri_rtf+= "suap_testo_val_fms|"		// Cartella Clinica (SUAP) - Valutazione osservazioni funzioni motorie sensoriali
			segnalibri_rtf+= "suap_testo_val_aa|"		// Cartella Clinica (SUAP) - Valutazione osservazioni altre aree
		  //segnalibri_rtf+= "suap_testo_val_psic|"		// Cartella Clinica (SUAP) - Valutazione osservazioni psicologo
			segnalibri_rtf+= "suap_testo_val_ifc|"		// Cartella Clinica (SUAP) - Valutazione osservazioni su informazione formazione caregiver
			segnalibri_rtf+= "suap_testo_val_prs|"		// Cartella Clinica (SUAP) - Valutazione osservazioni su partecipazione e reinserimento sociale

			segnalibri_rtf+= "art44_valutiniz_strumento_utilizzato_a|art44_valutiniz_strumento_utilizzato_altro_a|art44_valutiniz_esito_val_funz_a|art44_valutiniz_esito_vas_a|art44_valutiniz_esito_altro_a|";
			segnalibri_rtf+= "art44_valutiniz_strumento_utilizzato_b|art44_valutiniz_strumento_utilizzato_altro_b|art44_valutiniz_esito_val_funz_b|art44_valutiniz_esito_vas_b|art44_valutiniz_esito_altro_b|";	// Cartella Clinica (ex art.44) - Valutazione iniziale
			segnalibri_rtf+= "art44_valutiniz_strumento_utilizzato_c|art44_valutiniz_strumento_utilizzato_altro_c|art44_valutiniz_esito_val_funz_c|art44_valutiniz_esito_vas_c|art44_valutiniz_esito_altro_c|";
			segnalibri_rtf+= "art44_valutiniz_strumento_utilizzato_d|art44_valutiniz_strumento_utilizzato_altro_d|art44_valutiniz_esito_val_funz_d|art44_valutiniz_esito_vas_d|art44_valutiniz_esito_altro_d|";
			
			segnalibri_rtf+= "art44_valutinter_ajax_strumento_utilizzato_a|art44_valutinter_ajax_esito_val_funz_a|art44_valutinter_ajax_esito_vas_a|";
			segnalibri_rtf+= "art44_valutinter_ajax_strumento_utilizzato_b|art44_valutinter_ajax_esito_val_funz_b|art44_valutinter_ajax_esito_vas_b|";
			segnalibri_rtf+= "art44_valutinter_ajax_strumento_utilizzato_c|art44_valutinter_ajax_esito_val_funz_c|art44_valutinter_ajax_esito_vas_c|";			// Cartella Clinica (ex art.44) - Valutazione intermedia / finale
			segnalibri_rtf+= "art44_valutinter_ajax_strumento_utilizzato_d|art44_valutinter_ajax_esito_val_funz_d|art44_valutinter_ajax_esito_vas_d|";

			segnalibri_rtf+= "art44_valutfin_ajax_strumento_utilizzato_a|art44_valutfin_ajax_esito_val_funz_a|art44_valutfin_ajax_esito_vas_a|";
			segnalibri_rtf+= "art44_valutfin_ajax_strumento_utilizzato_b|art44_valutfin_ajax_esito_val_funz_b|art44_valutfin_ajax_esito_vas_b|";
			segnalibri_rtf+= "art44_valutfin_ajax_strumento_utilizzato_c|art44_valutfin_ajax_esito_val_funz_c|art44_valutfin_ajax_esito_vas_c|";				// Cartella Clinica (ex art.44) - Valutazione finale
			segnalibri_rtf+= "art44_valutfin_ajax_strumento_utilizzato_d|art44_valutfin_ajax_esito_val_funz_d|art44_valutfin_ajax_esito_vas_d|";			

			segnalibri_rtf+= "temp_data_giorno_I|";					// Cartella Infermieristica - Monitoraggio temperature
			segnalibri_rtf+= "mon_par_ora_1_1|";					// Cartella Infermieristica - Monitoraggio parametri
			segnalibri_rtf+= "bil_idr_ora_1_1|";					// Cartella Infermieristica - Bilancio idrico
			segnalibri_rtf+= "ris_cad_fat_correlati_1|";			// Cartella Infermieristica - Valutazione rischio cadute
			segnalibri_rtf+= "ris_inf_fat_correlati_1|";			// Cartella Infermieristica - Valutazione rischio infezioni			
			segnalibri_rtf+= "terap_farm_farmaco_1|";				// Cartella Infermieristica - Foglio terapia farmacologica
			segnalibri_rtf+= "somm_farm_dt_1|";						// Cartella Infermieristica - Somministrazione farmacologica
			
			segnalibri_rtf+= "moduli_oscuramento_fse";																										// Esercizio diritti di oscuramento FSE

		
		$.ajax({
			type: "POST",
			url: "ajax_autopopolamento_campi_moduli.php",
			data: {segnalibri_rtf:segnalibri_rtf, idmoduloversione: <?=$idmoduloversione?>},
			success: function(response){
				if(response.trim() !== "") 
				{
					response = JSON.parse(response);
				// Cartella Clinica (ex26) - Programma Riabilitativo - Mobilit� e Spostamenti
					var id_campo_metodologie_operative_ajax_ms 	= response['metodologie_operative_ajax_ms'];
					var id_campo_elenco_obiettivi_ajax_ms 		= response['elenco_obiettivi_ajax_ms'];
					var id_campo_misure_desito_ajax_ms 			= response['misure_desito_ajax_ms'];
					var id_campo_raggiungimento_risultati_ms	= response['raggiungimento_risultati_ms'];
					var id_campo_risultato_ms 					= response['risultato_ms'];
				// Cartella Clinica (ex26) - Programma Riabilitativo - Funzioni sensomotorie
					var id_campo_metodologie_operative_ajax_fs 	= response['metodologie_operative_ajax_fs'];
					var id_campo_elenco_obiettivi_ajax_fs 		= response['elenco_obiettivi_ajax_fs'];
					var id_campo_misure_desito_ajax_fs 			= response['misure_desito_ajax_fs'];
					var id_campo_raggiungimento_risultati_fs	= response['raggiungimento_risultati_fs'];
					var id_campo_risultato_fs 					= response['risultato_fs'];
				// Cartella Clinica (ex26) - Programma Riabilitativo - Comunicativo - Relazionali
					var id_campo_metodologie_operative_ajax_cr 	= response['metodologie_operative_ajax_cr'];
					var id_campo_elenco_obiettivi_ajax_cr 		= response['elenco_obiettivi_ajax_cr'];
					var id_campo_misure_desito_ajax_cr 			= response['misure_desito_ajax_cr'];
					var id_campo_raggiungimento_risultati_cr	= response['raggiungimento_risultati_cr'];
					var id_campo_risultato_cr 					= response['risultato_cr'];
				// Cartella Clinica (ex26) - Programma Riabilitativo - Cognitivo - Comportamentale				
					var id_campo_metodologie_operative_ajax_cc 	= response['metodologie_operative_ajax_cc'];
					var id_campo_elenco_obiettivi_ajax_cc 		= response['elenco_obiettivi_ajax_cc'];
					var id_campo_misure_desito_ajax_cc 			= response['misure_desito_ajax_cc'];
					var id_campo_raggiungimento_risultati_cc	= response['raggiungimento_risultati_cc'];
					var id_campo_risultato_cc					= response['risultato_cc'];
				// Cartella Clinica (ex26) - Programma Riabilitativo - Autonomia cura persona
					var id_campo_metodologie_operative_ajax_acp = response['metodologie_operative_ajax_acp'];
					var id_campo_elenco_obiettivi_ajax_acp 		= response['elenco_obiettivi_ajax_acp'];
					var id_campo_misure_desito_ajax_acp 		= response['misure_desito_ajax_acp'];
					var id_campo_raggiungimento_risultati_acp	= response['raggiungimento_risultati_acp'];
					var id_campo_risultato_acp 					= response['risultato_acp'];
				// Cartella Clinica (ex26) - Programma Riabilitativo - Area emotiva affettiva
					var id_campo_metodologie_operative_ajax_aea = response['metodologie_operative_ajax_aea'];
					var id_campo_elenco_obiettivi_ajax_aea 		= response['elenco_obiettivi_ajax_aea'];
					var id_campo_misure_desito_ajax_aea 		= response['misure_desito_ajax_aea'];
					var id_campo_raggiungimento_risultati_aea	= response['raggiungimento_risultati_aea'];
					var id_campo_risultato_aea 					= response['risultato_aea'];
				// Cartella Clinica (ex26) - Programma Riabilitativo - Reinserimento sociale
					var id_campo_metodologie_operative_ajax_rrs = response['metodologie_operative_ajax_rrs'];
					var id_campo_elenco_obiettivi_ajax_rrs 		= response['elenco_obiettivi_ajax_rrs'];
					var id_campo_misure_desito_ajax_rrs 		= response['misure_desito_ajax_rrs'];
					var id_campo_raggiungimento_risultati_rrs	= response['raggiungimento_risultati_rrs'];
					var id_campo_risultato_rrs 					= response['risultato_rrs'];

				// Esercizio diritti di oscuramento FSE
					var id_moduli_oscuramento_fse = response['moduli_oscuramento_fse'];
					
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni motorie sensoriali
					var id_campo_esiti_raggiungimento_risultati_fms	= response['esiti_raggiungimento_risultati_fms'];
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni vescico sfinteriche
					var id_campo_esiti_raggiungimento_risultati_fvs	= response['esiti_raggiungimento_risultati_fvs'];
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni cardio respiratorie
					var id_campo_esiti_raggiungimento_risultati_fcr	= response['esiti_raggiungimento_risultati_fcr'];
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni digestive
					var id_campo_esiti_raggiungimento_risultati_fd	= response['esiti_raggiungimento_risultati_fd']
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Funzioni cognitive comportamentali e del linguaggio
					var id_campo_esiti_raggiungimento_risultati_fccl = response['esiti_raggiungimento_risultati_fccl'];
				// Cartella Clinica (ex26 - Res. Est.) - Programma Riabilitativo Informazione formazione care giver
					var id_campo_esiti_raggiungimento_risultati_ifcg = response['esiti_raggiungimento_risultati_ifcg'];
					
					
					
					// SUAP
   					var id_campo_suap_esiti_obiettivi_raggiunti_aifvb = response['suap_esiti_obiettivi_raggiunti_aifvb'];		// Cartella Clinica (SUAP) - Area intervento Funzioni Vitali di Base e Stabilita' Internistica
					var id_campo_suap_esiti_obiettivi_raggiunti_aiacp = response['suap_esiti_obiettivi_raggiunti_aiacp'];		// Cartella Clinica (SUAP) - Area intervento autonomia cura persona
					var id_campo_suap_esiti_obiettivi_raggiunti_aifmt = response['suap_esiti_obiettivi_raggiunti_aifmt'];		// Cartella Clinica (SUAP) - Area intervento funzioni mobilita trasferimenti
					var id_campo_suap_esiti_obiettivi_raggiunti_aifsm = response['suap_esiti_obiettivi_raggiunti_aifsm'];		// Cartella Clinica (SUAP) - Area intervento funzioni senso motorie
					var id_campo_suap_esiti_obiettivi_raggiunti_aiaa  = response['suap_esiti_obiettivi_raggiunti_aiaa'];		// Cartella Clinica (SUAP) - Area intervento altre aree
					var id_campo_suap_esiti_obiettivi_raggiunti_aiifc = response['suap_esiti_obiettivi_raggiunti_aiifc'];		// Cartella Clinica (SUAP) - Area intervento informazione formazione caregiver
					var id_campo_suap_esiti_obiettivi_raggiunti_aiprs = response['suap_esiti_obiettivi_raggiunti_aiprs'];		// Cartella Clinica (SUAP) - Area intervento partecipazione e reinserimento sociale			
					
					var id_campo_suap_testo_valutazione_fms  = response['suap_testo_val_fms'];	// Cartella Clinica (SUAP) - Valutazione osservazioni funzioni motorie sensoriali
					var id_campo_suap_testo_valutazione_mt   = response['suap_testo_val_fmt'];	// Cartella Clinica (SUAP) - Valutazione osservazioni funzioni mobilita trasferimenti
					var id_campo_suap_testo_valutazione_acp  = response['suap_testo_val_acp'];	// Cartella Clinica (SUAP) - Valutazione osservazioni autonomia cura persona
					var id_campo_suap_testo_valutazione_aa   = response['suap_testo_val_aa'];	// Cartella Clinica (SUAP) - Valutazione osservazioni altre aree
				  //var id_campo_suap_testo_valutazione_psic = response['suap_testo_val_psic'];	// Cartella Clinica (SUAP) - Valutazione osservazioni psicologo
					var id_campo_suap_testo_valutazione_fsi  = response['suap_testo_val_fvb'];	// Cartella Clinica (SUAP) - Valutazione osservazioni funzioni di base e stabita' internistica
					var id_campo_suap_testo_valutazione_ifc  = response['suap_testo_val_ifc'];	// Cartella Clinica (SUAP) - Valutazione osservazioni su informazione formazione caregiver
					var id_campo_suap_testo_valutazione_prs  = response['suap_testo_val_prs'];	// Cartella Clinica (SUAP) - Valutazione osservazioni su partecipazione e reinserimento sociale
					
					// se sono in un Area di intervento SUAP, mi prendo il campo "Esiti risultati" per l'autopopolamento nella successiva ajax
					var id_campo_suap_esiti_raggiungimento_risultati_res = "";
					if( $('[name="' + id_campo_suap_esiti_obiettivi_raggiunti_aifvb + '"]').length ) { id_campo_suap_esiti_raggiungimento_risultati_res = id_campo_suap_esiti_obiettivi_raggiunti_aifvb;	segnalibro_campo_esiti = "suap_testo_val_fvb"; }
					if( $('[name="' + id_campo_suap_esiti_obiettivi_raggiunti_aiacp + '"]').length ) { id_campo_suap_esiti_raggiungimento_risultati_res = id_campo_suap_esiti_obiettivi_raggiunti_aiacp;	segnalibro_campo_esiti = "suap_testo_val_acp"; }	
					if( $('[name="' + id_campo_suap_esiti_obiettivi_raggiunti_aifmt + '"]').length ) { id_campo_suap_esiti_raggiungimento_risultati_res = id_campo_suap_esiti_obiettivi_raggiunti_aifmt;	segnalibro_campo_esiti = "suap_testo_val_fmt"; }
					if( $('[name="' + id_campo_suap_esiti_obiettivi_raggiunti_aifsm + '"]').length ) { id_campo_suap_esiti_raggiungimento_risultati_res = id_campo_suap_esiti_obiettivi_raggiunti_aifsm;	segnalibro_campo_esiti = "suap_testo_val_fms"; }
					if( $('[name="' + id_campo_suap_esiti_obiettivi_raggiunti_aiaa + '"]').length  ) { id_campo_suap_esiti_raggiungimento_risultati_res = id_campo_suap_esiti_obiettivi_raggiunti_aiaa;		segnalibro_campo_esiti = "suap_testo_val_aa"; }
					if( $('[name="' + id_campo_suap_esiti_obiettivi_raggiunti_aiifc + '"]').length ) { id_campo_suap_esiti_raggiungimento_risultati_res = id_campo_suap_esiti_obiettivi_raggiunti_aiifc;	segnalibro_campo_esiti = "suap_testo_val_ifc"; }
					if( $('[name="' + id_campo_suap_esiti_obiettivi_raggiunti_aiprs + '"]').length ) { id_campo_suap_esiti_raggiungimento_risultati_res = id_campo_suap_esiti_obiettivi_raggiunti_aiprs;	segnalibro_campo_esiti = "suap_testo_val_prs"; }
					
					if(id_campo_suap_esiti_raggiungimento_risultati_res !== "") {					
					// prendo il valore del campo del modulo "Valutazioni osservazioni" ad esso collegato se, e solo se, l'ultima istanza di tale modulo 
					// sia stata compilata nei 30 giorni successivi alla data di compilazione del modulo "Programma Riabilitativo"
						if(segnalibro_campo_esiti) {
							$.ajax({
								type: "POST",
								url: "ajax_autopopolamento_campi_moduli.php",
								data: {id_cartella: <? echo $idcartella ?>, segnalibro_campo_esiti:segnalibro_campo_esiti},
								success: function(response)
								{
									if(response.trim() !== "") 
									{
										response = JSON.parse(response);
										$("[name='" + id_campo_suap_esiti_raggiungimento_risultati_res + "']").val( response.esiti_valutazione 	);
									}
								}
							});
						}
					}						
					
					
					
					
					
				// Cartella Clinica (ex art.44) - Valutazione iniziale
 					var id_campo_art44_valutiniz_strumento_utilizzato_a 		= response['art44_valutiniz_strumento_utilizzato_a'];
 					var id_campo_art44_valutiniz_strumento_utilizzato_altro_a 	= response['art44_valutiniz_strumento_utilizzato_altro_a'];
					var id_campo_art44_valutiniz_esito_val_funz_a 				= response['art44_valutiniz_esito_val_funz_a'];
					var id_campo_art44_valutiniz_esito_vas_a					= response['art44_valutiniz_esito_vas_a'];
					var id_campo_art44_valutiniz_esito_altro_a					= response['art44_valutiniz_esito_altro_a'];
					var id_campo_art44_valutiniz_strumento_utilizzato_b			= response['art44_valutiniz_strumento_utilizzato_b'];
					var id_campo_art44_valutiniz_strumento_utilizzato_altro_b 	= response['art44_valutiniz_strumento_utilizzato_altro_b'];
					var id_campo_art44_valutiniz_esito_val_funz_b 				= response['art44_valutiniz_esito_val_funz_b'];
					var id_campo_art44_valutiniz_esito_vas_b					= response['art44_valutiniz_esito_vas_b'];
					var id_campo_art44_valutiniz_esito_altro_b					= response['art44_valutiniz_esito_altro_b'];
					var id_campo_art44_valutiniz_strumento_utilizzato_c 		= response['art44_valutiniz_strumento_utilizzato_c'];
					var id_campo_art44_valutiniz_strumento_utilizzato_altro_c 	= response['art44_valutiniz_strumento_utilizzato_altro_c'];
					var id_campo_art44_valutiniz_esito_val_funz_c 				= response['art44_valutiniz_esito_val_funz_c'];
					var id_campo_art44_valutiniz_esito_vas_c					= response['art44_valutiniz_esito_vas_c'];
					var id_campo_art44_valutiniz_esito_altro_c					= response['art44_valutiniz_esito_altro_c'];
					var id_campo_art44_valutiniz_strumento_utilizzato_d 		= response['art44_valutiniz_strumento_utilizzato_d'];
					var id_campo_art44_valutiniz_strumento_utilizzato_altro_d 	= response['art44_valutiniz_strumento_utilizzato_altro_d'];
					var id_campo_art44_valutiniz_esito_val_funz_d 				= response['art44_valutiniz_esito_val_funz_d'];
					var id_campo_art44_valutiniz_esito_vas_d					= response['art44_valutiniz_esito_vas_d'];
					var id_campo_art44_valutiniz_esito_altro_d					= response['art44_valutiniz_esito_altro_d'];
					// eseguo i controlli sui campi 
					if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_a + '"]').length ) {
						// nascondo i campi esito per le 2 scelte delle combo "strumento_utilizzato_x" in base al valore di queste combo
						if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_a + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_a + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_a + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_a + '"]').val().trim() == "3" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').parent().parent().hide();
						}
						
						if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_b + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_b + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_b + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_b + '"]').val().trim() == "3" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').parent().parent().hide();
						}
							
						if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_c + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_c + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_c + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_c + '"]').val().trim() == "3" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').parent().parent().hide();
						}				
						
						if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_d + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_d + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_d + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_d + '"]').val().trim() == "3" ) {
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').parent().parent().hide();
						}
						
						// in base al valore di ogni singola combo, visualizzo o nascondo i campi relativi al valore scelto
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_a + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
							} else if($(this).val() == '3')	{
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_a + '"],[name="' + id_campo_art44_valutiniz_esito_vas_a + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_a + '"],[name="' + id_campo_art44_valutiniz_esito_altro_a + '"]').parent().parent().hide();
							}
						});		
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_b + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
							} else if($(this).val() == '3')	{
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_b + '"],[name="' + id_campo_art44_valutiniz_esito_vas_b + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_b + '"],[name="' + id_campo_art44_valutiniz_esito_altro_b + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_c + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
							} else if($(this).val() == '3')	{
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_c + '"],[name="' + id_campo_art44_valutiniz_esito_vas_c + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_c + '"],[name="' + id_campo_art44_valutiniz_esito_altro_c + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_d + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
							} else if($(this).val() == '3')	{
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').show();
								$('[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutiniz_esito_val_funz_d + '"],[name="' + id_campo_art44_valutiniz_esito_vas_d + '"],[name="' + id_campo_art44_valutiniz_strumento_utilizzato_altro_d + '"],[name="' + id_campo_art44_valutiniz_esito_altro_d + '"]').parent().parent().hide();
							}
						});		
					} 
						
				// Cartella Clinica (ex art.44) - Valutazione intermedia / finale	
					var id_campo_art44_valutinter_ajax_strumento_utilizzato_a 	= response['art44_valutinter_ajax_strumento_utilizzato_a'];
					var id_campo_art44_valutinter_ajax_esito_val_funz_a 		= response['art44_valutinter_ajax_esito_val_funz_a'];
					var id_campo_art44_valutinter_ajax_esito_vas_a				= response['art44_valutinter_ajax_esito_vas_a'];
					var id_campo_art44_valutinter_ajax_strumento_utilizzato_b 	= response['art44_valutinter_ajax_strumento_utilizzato_b'];
					var id_campo_art44_valutinter_ajax_esito_val_funz_b 		= response['art44_valutinter_ajax_esito_val_funz_b'];
					var id_campo_art44_valutinter_ajax_esito_vas_b				= response['art44_valutinter_ajax_esito_vas_b'];
					var id_campo_art44_valutinter_ajax_strumento_utilizzato_c 	= response['art44_valutinter_ajax_strumento_utilizzato_c'];
					var id_campo_art44_valutinter_ajax_esito_val_funz_c 		= response['art44_valutinter_ajax_esito_val_funz_c'];
					var id_campo_art44_valutinter_ajax_esito_vas_c				= response['art44_valutinter_ajax_esito_vas_c'];
					var id_campo_art44_valutinter_ajax_strumento_utilizzato_d 	= response['art44_valutinter_ajax_strumento_utilizzato_d'];
					var id_campo_art44_valutinter_ajax_esito_val_funz_d 		= response['art44_valutinter_ajax_esito_val_funz_d'];
					var id_campo_art44_valutinter_ajax_esito_vas_d				= response['art44_valutinter_ajax_esito_vas_d'];
					
					// eseguo i controlli sui campi 
					if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').length ) {
						// nascondo i campi esito per le 2 scelte delle combo "strumento_utilizzato_x" in base al valore di queste combo
						if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').parent().parent().hide();
						}
						
						if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_b + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_b + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_b + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').parent().parent().hide();
						}
							
						if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_c + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_c + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_c + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').parent().parent().hide();
						}				
						
						if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_d + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_d + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_d + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').parent().parent().hide();
						}
						
						// in base al valore di ogni singola combo, visualizzo o nascondo i campi relativi al valore scelto
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_a + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_a + '"]').parent().parent().hide();
							}
						});		
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_b + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_b + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_c + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_c + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutinter_ajax_strumento_utilizzato_d + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutinter_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutinter_ajax_esito_vas_d + '"]').parent().parent().hide();
							}
						});	
					} 
					
					var unlock_controls = <? echo (!empty($_COOKIE['unlock_controls']) && $_COOKIE['unlock_controls'] ? 'true' : 'false') ?>;
					
				// Cartella Clinica (ex art.44) - Valutazione finale	
					var id_campo_art44_valutfin_ajax_strumento_utilizzato_a = response['art44_valutfin_ajax_strumento_utilizzato_a'];
					var id_campo_art44_valutfin_ajax_esito_val_funz_a 		= response['art44_valutfin_ajax_esito_val_funz_a'];
					var id_campo_art44_valutfin_ajax_esito_vas_a			= response['art44_valutfin_ajax_esito_vas_a'];
					var id_campo_art44_valutfin_ajax_strumento_utilizzato_b = response['art44_valutfin_ajax_strumento_utilizzato_b'];
					var id_campo_art44_valutfin_ajax_esito_val_funz_b 		= response['art44_valutfin_ajax_esito_val_funz_b'];
					var id_campo_art44_valutfin_ajax_esito_vas_b			= response['art44_valutfin_ajax_esito_vas_b'];
					var id_campo_art44_valutfin_ajax_strumento_utilizzato_c = response['art44_valutfin_ajax_strumento_utilizzato_c'];
					var id_campo_art44_valutfin_ajax_esito_val_funz_c 		= response['art44_valutfin_ajax_esito_val_funz_c'];
					var id_campo_art44_valutfin_ajax_esito_vas_c			= response['art44_valutfin_ajax_esito_vas_c'];
					var id_campo_art44_valutfin_ajax_strumento_utilizzato_d = response['art44_valutfin_ajax_strumento_utilizzato_d'];
					var id_campo_art44_valutfin_ajax_esito_val_funz_d 		= response['art44_valutfin_ajax_esito_val_funz_d'];
					var id_campo_art44_valutfin_ajax_esito_vas_d			= response['art44_valutfin_ajax_esito_vas_d'];

					// eseguo i controlli sui campi 
					if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_a + '"]').length ) {
						// nascondo i campi esito per le 2 scelte delle combo "strumento_utilizzato_x" in base al valore di queste combo
						if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_a + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_a + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_a + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').parent().parent().hide();
						}
						
						if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_b + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_b + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_b + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').parent().parent().hide();
						}
							
						if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_c + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_c + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_c + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').parent().parent().hide();
						}				
						
						if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_d + '"]').val().trim() == "" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_d + '"]').val().trim() == "1" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().hide();
						} else if( $('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_d + '"]').val().trim() == "2" ) {
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').hide();	
							$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').parent().parent().hide();
						}
						
						// in base al valore di ogni singola combo, visualizzo o nascondo i campi relativi al valore scelto
						$('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_a + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_a + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_a + '"]').parent().parent().hide();
							}
						});		
						$('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_b + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_b + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_b + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_c + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_c + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_c + '"]').parent().parent().hide();
							}
						});	
						$('[name="' + id_campo_art44_valutfin_ajax_strumento_utilizzato_d + '"]').change( function(){ 
							if($(this).val() == '1') {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().hide();
							} else if($(this).val() == '2')	{
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().show();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"]').parent().parent().hide();
							} else {
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').val("").hide();
								$('[name="' + id_campo_art44_valutfin_ajax_esito_val_funz_d + '"],[name="' + id_campo_art44_valutfin_ajax_esito_vas_d + '"]').parent().parent().hide();
							}
						});	
					}
					
					
				// Cartella Infermieristica - Monitoraggio temperature
					var campo_giorno_temp_I = response['temp_data_giorno_I'];
					
					if( $('[name="' + campo_giorno_temp_I + '"]').length && !unlock_controls)
					{
						var	segnalibri_da_cercare = "temp_data_giorno_II|temp_data_giorno_III|temp_data_giorno_IV|temp_data_giorno_V|temp_data_giorno_VI|temp_data_giorno_VII|temp_data_giorno_VIII|temp_data_giorno_IX|temp_data_giorno_X|temp_data_giorno_XI|temp_data_giorno_XII|temp_data_giorno_XIII|temp_data_giorno_XIV|temp_data_giorno_XV|temp_data_giorno_XVI|temp_data_giorno_XVII|temp_data_giorno_XVIII|temp_data_giorno_XIX|temp_data_giorno_XX|temp_data_giorno_XXI|temp_data_giorno_XXII|temp_data_giorno_XXIII|temp_data_giorno_XXIV|temp_data_giorno_XXV|temp_data_giorno_XXVI|temp_data_giorno_XXVII|temp_data_giorno_XXVIII|temp_data_giorno_XXIX|temp_data_giorno_XXX|temp_data_giorno_XXXI";
					
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response = JSON.parse(response);
								
								var campo_giorno_temp_II 	= response['temp_data_giorno_II'];
								var campo_giorno_temp_III 	= response['temp_data_giorno_III'];
								var campo_giorno_temp_IV 	= response['temp_data_giorno_IV'];
								var campo_giorno_temp_V 	= response['temp_data_giorno_V'];
								var campo_giorno_temp_VI 	= response['temp_data_giorno_VI'];
								var campo_giorno_temp_VII 	= response['temp_data_giorno_VII'];
								var campo_giorno_temp_VIII 	= response['temp_data_giorno_VIII'];
								var campo_giorno_temp_IX 	= response['temp_data_giorno_IX'];
								var campo_giorno_temp_X 	= response['temp_data_giorno_X'];
								var campo_giorno_temp_XI 	= response['temp_data_giorno_XI'];
								var campo_giorno_temp_XII 	= response['temp_data_giorno_XII'];
								var campo_giorno_temp_XIII 	= response['temp_data_giorno_XIII'];
								var campo_giorno_temp_XIV 	= response['temp_data_giorno_XIV'];
								var campo_giorno_temp_XV 	= response['temp_data_giorno_XV'];
								var campo_giorno_temp_XVI 	= response['temp_data_giorno_XVI'];
								var campo_giorno_temp_XVII 	= response['temp_data_giorno_XVII'];
								var campo_giorno_temp_XVIII = response['temp_data_giorno_XVIII'];
								var campo_giorno_temp_XIX 	= response['temp_data_giorno_XIX'];
								var campo_giorno_temp_XX 	= response['temp_data_giorno_XX'];
								var campo_giorno_temp_XXI 	= response['temp_data_giorno_XXI'];
								var campo_giorno_temp_XXII 	= response['temp_data_giorno_XXII'];
								var campo_giorno_temp_XXIII = response['temp_data_giorno_XXIII'];
								var campo_giorno_temp_XXIV 	= response['temp_data_giorno_XXIV'];
								var campo_giorno_temp_XXV 	= response['temp_data_giorno_XXV'];
								var campo_giorno_temp_XXVI 	= response['temp_data_giorno_XXVI'];
								var campo_giorno_temp_XXVII = response['temp_data_giorno_XXVII'];
								var campo_giorno_temp_XXVIII= response['temp_data_giorno_XXVIII'];
								var campo_giorno_temp_XXIX 	= response['temp_data_giorno_XXIX'];
								var campo_giorno_temp_XXX 	= response['temp_data_giorno_XXX'];
								var campo_giorno_temp_XXXI 	= response['temp_data_giorno_XXXI'];
								
								var oggi 		= <?php echo date('d'); ?>;
								var ieri 		= <?php echo date('d', strtotime('-1 day')); ?>;
								var mese_corrente = <?php echo date('m', strtotime('now')); ?>;
								var datains_modulo = "<?php echo $datains; ?>";	
								var meseins_modulo = datains_modulo.split('/')[1];	
								var roman_days 	= [campo_giorno_temp_I,campo_giorno_temp_II,campo_giorno_temp_III,campo_giorno_temp_IV,campo_giorno_temp_V,campo_giorno_temp_VI,campo_giorno_temp_VII,campo_giorno_temp_VIII,campo_giorno_temp_IX,campo_giorno_temp_X,campo_giorno_temp_XI,campo_giorno_temp_XII,campo_giorno_temp_XIII,campo_giorno_temp_XIV,campo_giorno_temp_XV,campo_giorno_temp_XVI,campo_giorno_temp_XVII,campo_giorno_temp_XVIII,campo_giorno_temp_XIX,campo_giorno_temp_XX,campo_giorno_temp_XXI,campo_giorno_temp_XXII,campo_giorno_temp_XXIII,campo_giorno_temp_XXIV,campo_giorno_temp_XXV,campo_giorno_temp_XXVI,campo_giorno_temp_XXVII,campo_giorno_temp_XXVIII,campo_giorno_temp_XXIX,campo_giorno_temp_XXX,campo_giorno_temp_XXXI];
								var str_roman_days = JSON.stringify(roman_days);						
								
								$.ajax({
									type: "POST",
									url: "ajax_autopopolamento_campi_moduli.php",
									data: {id_cartella: <? echo $idcartella ?>, idmoduloversione: <?=$idmoduloversione?>, idinserimento: <?=$idinserimento?>, name_campi_giorni_temp : str_roman_days },
									success: function(response)
									{
										var responseArr = JSON.parse(response);	
										var count = true;
										roman_days.forEach( function (item, index) {
											// SBO 03/11/23 : fix per disabilitare se il modulo non � editato nel mese della creazione
											if((index+1 < ieri) || (mese_corrente != meseins_modulo)) {												
												if(responseArr[item]['valore'] == undefined || responseArr[item]['valore'] == null || responseArr[item]['valore'] == 'null') {
													$('[name="' + item + '"]').html('<option value="_">Giorno non inserito</option>').css('background-color', 'gray');
												} else {
													$('[name="' + item + '"]').html('<option value="' + responseArr[item]['valore'] + '">' + responseArr[item]['etichetta'] +'</option>').css('background-color', 'gray');
												}
											} else if(index+1 > oggi) {
												$('[name="' + item + '"]').html('<option value="_">Giorno non modificabile</option>').css('background-color', 'gray');
											}
										});
									}
								});
							}
						});
					}

					// Cartella Infermieristica - Monitoraggio parametri
					var mon_par_ora_1_1 = response['mon_par_ora_1_1'];

					if( $('[name="' + mon_par_ora_1_1 + '"]').length && !unlock_controls)
					{
						var	segnalibri_da_cercare_arr = ['ora','operatore','pa_max','pa_min','bm','spo','alvo','note'];
						var	segnalibri_da_cercare = '';
						
						for(var i=1; i<=31; i++) {
							for(var j=1; j<=2; j++) {	
								segnalibri_da_cercare_arr.forEach(function(item) {
									segnalibri_da_cercare += item + '_' + i + '_' + j + '|';
								});							
							}
						}
						
						segnalibri_da_cercare = 'mon_par_' + segnalibri_da_cercare.slice(0, -1);
						
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response_segnalibri_values = JSON.parse(response);
								
								segnalibri_da_cercare_arr = segnalibri_da_cercare.split('|');
								
								var oggi = <?php echo date('d'); ?>;
								var ieri = <?php echo date('d', strtotime('-1 day')); ?>;
								var mese_corrente = <?php echo date('m', strtotime('now')); ?>;
								var datains_modulo = "<?php echo $datains; ?>";	
								var meseins_modulo = datains_modulo.split('/')[1];
								var values = [];

								segnalibri_da_cercare_arr.forEach(function(item) {
									values.push(response_segnalibri_values[item]);
								});
								
								var str_values = JSON.stringify(values);	

								$.ajax({
									type: "POST",
									url: "ajax_autopopolamento_campi_moduli.php",
									data: {id_cartella: <? echo $idcartella ?>, idmoduloversione: <?=$idmoduloversione?>, idinserimento: <?=$idinserimento?>, name_campi_mon_par : str_values },
									success: function(response)
									{
										var responseArr = JSON.parse(response);	
										
										segnalibri_da_cercare_arr.forEach( function (item) {											
											var obj_name = response_segnalibri_values[item];
											var obj = $('[name="' + obj_name + '"]');
											var day = item.split('_');
											day = day[day.length-2];
											// SBO 03/11/23 : fix per disabilitare se il modulo non � editato nel mese della creazione
											if((day < ieri) || (mese_corrente != meseins_modulo)) {
												var _val = responseArr[obj_name]['valore'];												
												
												if(obj.is("select")) {													
													_val = _val  === null || _val === undefined ? '_' : _val;
													var _etichetta = responseArr[obj_name]['etichetta'];
													_etichetta = _etichetta === undefined ? 'Valore non selezionato' : _etichetta;

													obj.html('<option value="' + _val + '">' + _etichetta +'</option>').css('background-color', 'gray');
												} else {
													_val = _val  === null || _val  === undefined || _val == "" ? '.' : _val;
													
													obj.val(_val).attr('readonly',true).css('background-color', 'gray');
												}												
											} else if(day > oggi) {	
												if(obj.is("select")) {
													obj.html('<option value="_">Giorno non modificabile</option>').css('background-color', 'gray');
												} else {
													obj.attr('readonly',true).css('background-color', 'gray');
												}
											}
										});
										
									}
								});
							}
						});
					}
					
					
					// Cartella Infermieristica - Bilancio idrico
					var bil_idr_ora_1_1 = response['bil_idr_ora_1_1'];
					// SBO 01/08/23 : inserito sblocco come sui due moduli precedenti su richiesta di Claudia [TICKET: 55969]
					if( $('[name="' + bil_idr_ora_1_1 + '"]').length && !unlock_controls)
					{
						var	segnalibri_da_cercare_arr = ['ora','entrate','uscite','diuresi','glicemia','operatore','note'];
						var	segnalibri_da_cercare = '';
						
						for(var i=1; i<=31; i++) {
							for(var j=1; j<=2; j++) {	
								segnalibri_da_cercare_arr.forEach(function(item) {
									segnalibri_da_cercare += item + '_' + i + '_' + j + '|';
								});							
							}
						}
						
						segnalibri_da_cercare = 'bil_idr_' + segnalibri_da_cercare.slice(0, -1);
						
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response_segnalibri_values = JSON.parse(response);
								
								segnalibri_da_cercare_arr = segnalibri_da_cercare.split('|');
								
								var oggi = <?php echo date('d'); ?>;
								var ieri = <?php echo date('d', strtotime('-1 day')); ?>;
								var mese_corrente = <?php echo date('m', strtotime('now')); ?>;
								var datains_modulo = "<?php echo $datains; ?>";	
								var meseins_modulo = datains_modulo.split('/')[1];								
								
								// SBO 04/09/23 : fix per abilitare il giorno 1 in caso di modifica
								if(ieri > oggi) {
									ieri = 0;
								}
								
								var values = [];

								segnalibri_da_cercare_arr.forEach(function(item) {
									values.push(response_segnalibri_values[item]);
								});
								
								var str_values = JSON.stringify(values);	

								$.ajax({
									type: "POST",
									url: "ajax_autopopolamento_campi_moduli.php",
									data: {id_cartella: <? echo $idcartella ?>, idmoduloversione: <?=$idmoduloversione?>, idinserimento: <?=$idinserimento?>, name_campi_bil_idr : str_values },
									success: function(response)
									{
										var responseArr = JSON.parse(response);
										
										segnalibri_da_cercare_arr.forEach( function (item) {											
											var obj_name = response_segnalibri_values[item];
											var obj = $('[name="' + obj_name + '"]');
											var day = item.split('_');
											day = day[day.length-2];
												
											// SBO 03/11/23 : fix per disabilitare se il modulo non � editato nel mese della creazione
											if((day < ieri) || (mese_corrente != meseins_modulo)) {
												var _val = responseArr[obj_name]['valore'];												
												
												if(obj.is("select")) {												
													_val = _val  === null || _val === undefined ? '_' : _val;
													var _etichetta = responseArr[obj_name]['etichetta'];
													_etichetta = _etichetta === undefined ? 'Valore non selezionato' : _etichetta;

													obj.html('<option value="' + _val + '">' + _etichetta +'</option>').css('background-color', 'gray');
												} else {
													_val = _val === null || _val == undefined || _val == "" ? "." : _val;
													
													obj.val(_val).attr('readonly',true).css('background-color', 'gray');
												}												
											} else if(day > oggi) {	
												if(obj.is("select")) {
													obj.html('<option value="_">Giorno non modificabile</option>').css('background-color', 'gray');
												} else {
													obj.attr('readonly',true).css('background-color', 'gray');
												}
											}
										});
										
									}
								});
							}
						});
					}
					
					// Cartella Infermieristica - Valutazione rischio cadute
					var ris_cad_fat_correlati_1 = response['ris_cad_fat_correlati_1'];
					
					if( $('[name="' + ris_cad_fat_correlati_1 + '"]').length )
					{
						var	segnalibri_da_cercare = "fat_correlati_2|fat_correlati_3|fat_correlati_4|fat_correlati_5|fat_correlati_6|fat_correlati_7|fat_correlati_8|fat_correlati_9|fat_correlati_10|fat_correlati_11|fat_correlati_12|fat_correlati_13|fat_correlati_14|fat_correlati_15|";
						segnalibri_da_cercare += "obiettivi_1|obiettivi_2|obiettivi_3|obiettivi_4|obiettivi_5|obiettivi_6|obiettivi_7|obiettivi_8|obiettivi_9|obiettivi_10|obiettivi_11|obiettivi_12|obiettivi_13|obiettivi_14|obiettivi_15";

						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response = JSON.parse(response);
								
								$('[name="' + ris_cad_fat_correlati_1 + '"]').change(function() {
									if(this.value == 1) {
										$('[name="' + response['obiettivi_1'] + '"]').text('Il paziente non presentera episodi di caduta durante la degenza');
									} else if(this.value == 2) {
										$('[name="' + response['obiettivi_1'] + '"]').text('Il paziente adottera le misure di sicurezza per prevenire le cadute accidentali entro _________');
									} else {
										$('[name="' + response['obiettivi_1'] + '"]').text('');
									}
								});
								
								for(var i=2; i<=15;i++) {
									var obj = $('[name="' + response['fat_correlati_' + i] + '"]');
									obj.data('obiettivi_idx', i);
									obj.change(function() {
										var obiettivi_idx = $(this).data('obiettivi_idx');

										if(this.value == 1) {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('Il paziente non presentera episodi di caduta durante la degenza');
										} else if(this.value == 2) {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('Il paziente adottera le misure di sicurezza per prevenire le cadute accidentali entro _________');
										} else {
											$('[name="' + response['obiettivi_1'] + '"]').text('');
										}
									});
								}									
							}
						});						
					}
				
					// Cartella Infermieristica - Valutazione rischio infezioni
					var ris_inf_fat_correlati_1 = response['ris_inf_fat_correlati_1'];
					
					if( $('[name="' + ris_inf_fat_correlati_1 + '"]').length )
					{			
						var	segnalibri_da_cercare = "fat_correlati_2|fat_correlati_3|fat_correlati_4|fat_correlati_5|fat_correlati_6|fat_correlati_7|fat_correlati_8|fat_correlati_9|fat_correlati_10|fat_correlati_11|fat_correlati_12|fat_correlati_13|fat_correlati_14|fat_correlati_15|";
						segnalibri_da_cercare += "obiettivi_1|obiettivi_2|obiettivi_3|obiettivi_4|obiettivi_5|obiettivi_6|obiettivi_7|obiettivi_8|obiettivi_9|obiettivi_10|obiettivi_11|obiettivi_12|obiettivi_13|obiettivi_14|obiettivi_15";

						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response = JSON.parse(response);
								
								$('[name="' + ris_inf_fat_correlati_1 + '"]').change(function() {
									if(this.value == 1) {
										$('[name="' + response['obiettivi_1'] + '"]').text('Il paziente non sviluppera infezioni durante la degenza');
									} else {
										$('[name="' + response['obiettivi_1'] + '"]').text('');
									}
								});
								
								for(var i=2; i<=15;i++) {
									var obj = $('[name="' + response['fat_correlati_' + i] + '"]');
									obj.data('obiettivi_idx', i);
									obj.change(function() {
										var obiettivi_idx = $(this).data('obiettivi_idx');
										if(this.value == 1) {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('Il paziente non sviluppera infezioni durante la degenza');
										} else {
											$('[name="' + response['obiettivi_' + obiettivi_idx] + '"]').text('');
										}
									});
								}									
							}
						});						
					}
					
					// Cartella Infermieristica - Foglio terapia farmacologica
					var id_campo_terap_farm_farmaco_1 = response['terap_farm_farmaco_1'];
					
					if( $('[name="' + id_campo_terap_farm_farmaco_1 + '"]').length )
					{
						var segnalibri_da_cercare = "qnt_1|qnt_2|qnt_3|qnt_4|qnt_5|qnt_6|qnt_7|qnt_8|qnt_9|qnt_10|qnt_11|qnt_12|qnt_13|qnt_14|qnt_15|qnt_16|qnt_17|qnt_18|qnt_19|qnt_20|qnt_21|qnt_22|qnt_23|qnt_24|qnt_25";
						segnalibri_da_cercare += "|frequenza_terapia_1|frequenza_terapia_2|frequenza_terapia_3|frequenza_terapia_4|frequenza_terapia_5|frequenza_terapia_6|frequenza_terapia_7|frequenza_terapia_8|frequenza_terapia_9|frequenza_terapia_10|frequenza_terapia_11|frequenza_terapia_12|frequenza_terapia_13|frequenza_terapia_14|frequenza_terapia_15|frequenza_terapia_16|frequenza_terapia_17|frequenza_terapia_18|frequenza_terapia_19|frequenza_terapia_20|frequenza_terapia_21|frequenza_terapia_22|frequenza_terapia_23|frequenza_terapia_24|frequenza_terapia_25";
						segnalibri_da_cercare += "|durata_terapia_1|durata_terapia_2|durata_terapia_3|durata_terapia_4|durata_terapia_5|durata_terapia_6|durata_terapia_7|durata_terapia_8|durata_terapia_9|durata_terapia_10|durata_terapia_11|durata_terapia_12|durata_terapia_13|durata_terapia_14|durata_terapia_15|durata_terapia_16|durata_terapia_17|durata_terapia_18|durata_terapia_19|durata_terapia_20|durata_terapia_21|durata_terapia_22|durata_terapia_23|durata_terapia_24|durata_terapia_25";
						segnalibri_da_cercare += "|altro_1|altro_2|altro_3|altro_4|altro_5|altro_6|altro_7|altro_8|altro_9|altro_10|altro_11|altro_12|altro_13|altro_14|altro_15|altro_16|altro_17|altro_18|altro_19|altro_20|altro_21|altro_22|altro_23|altro_24|altro_25";
						segnalibri_da_cercare += "|tipologia_prescrizione_1|tipologia_prescrizione_2|tipologia_prescrizione_3|tipologia_prescrizione_4|tipologia_prescrizione_5|tipologia_prescrizione_6|tipologia_prescrizione_7|tipologia_prescrizione_8|tipologia_prescrizione_9|tipologia_prescrizione_10|tipologia_prescrizione_11|tipologia_prescrizione_12|tipologia_prescrizione_13|tipologia_prescrizione_14|tipologia_prescrizione_15|tipologia_prescrizione_16|tipologia_prescrizione_17|tipologia_prescrizione_18|tipologia_prescrizione_19|tipologia_prescrizione_20|tipologia_prescrizione_21|tipologia_prescrizione_22|tipologia_prescrizione_23|tipologia_prescrizione_24|tipologia_prescrizione_25";
						segnalibri_da_cercare += "|allegato_1|allegato_2|allegato_3|allegato_4|allegato_5|allegato_6|allegato_7|allegato_8|allegato_9|allegato_10|allegato_11|allegato_12|allegato_13|allegato_14|allegato_15|allegato_16|allegato_17|allegato_18|allegato_19|allegato_20|allegato_21|allegato_22|allegato_23|allegato_24|allegato_25";
						
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response_terapia = JSON.parse(response);
								
								for(var i=1;i<=25;i++) {
									// PER VECCHI MODULI
									/*
									var altro_obj = $('[name="' + response_terapia['altro_' + i] + '"]');

									if(altro_obj.val() == null || trim(altro_obj.val()) == '') {
										altro_obj.parent().parent().css('display','none')
									}
									
									var obj = $('[name="' + response_terapia['qnt_' + i] + '"]');

									obj.data('qnt_idx', i);
									obj.change(function() {										
										var qnt_idx = $(this).data('qnt_idx');

										$('[name="' + response_terapia['altro_' + qnt_idx] + '"]').val('');
										
										if(this.value == 3) {
											$('[name="' + response_terapia['altro_' + qnt_idx] + '"]').parent().parent().css('display','block');											
										} else {
											$('[name="' + response_terapia['altro_' + qnt_idx] + '"]').parent().parent().css('display','none');
										}											
									});
									*/
									// PER NUOVI MODULI DA ID VERSIONE 6143
									var obj_drtp_pres = $('[name="' + response_terapia['durata_terapia_' + i] + '"]');
									if(obj_drtp_pres.val() != 3 && obj_drtp_pres.val() != 4) {
										$('[name="' + response_terapia['frequenza_terapia_' + i] + '"]').parent().parent().css('display','none');
									}

									obj_drtp_pres.data('dr_tp_pres_idx', i);
									obj_drtp_pres.change(function() {	
										var drtp_pres_idx = $(this).data('dr_tp_pres_idx');

										if(this.value == 3 || this.value == 4) {
											$('[name="' + response_terapia['frequenza_terapia_' + drtp_pres_idx] + '"]').parent().parent().css('display','block');											
										} else {
											$('[name="' + response_terapia['frequenza_terapia_' + drtp_pres_idx] + '"]').parent().parent().css('display','none');
											$('[name="' + response_terapia['frequenza_terapia_' + drtp_pres_idx] + '"]').val('');
										}											
									});
									
									var obj_tp_pres = $('[name="' + response_terapia['tipologia_prescrizione_' + i] + '"]');
									
									if(obj_tp_pres.val() != 1) {
										$('[name="' + response_terapia['allegato_' + i] + '"]').parent().parent().parent().css('display','none');
									}
									
									obj_tp_pres.data('tp_pres_idx', i);
									obj_tp_pres.change(function() {	
										var tp_pres_idx = $(this).data('tp_pres_idx');
										$('[name="' + response_terapia['allegato_' + tp_pres_idx] + '"]').val('');
										if(this.value == 1) {
											$('[name="' + response_terapia['allegato_' + tp_pres_idx] + '"]').parent().parent().parent().css('display','block');											
										} else {
											$('[name="' + response_terapia['allegato_' + tp_pres_idx] + '"]').parent().parent().parent().css('display','none');
										}											
									});
								}							
							}
						});
					}

					// Cartella Infermieristica - Somministrazione farmacologica
					var id_campo_somm_farm_dt_1 = response['somm_farm_dt_1'];			

					if( $('[name="' + id_campo_somm_farm_dt_1 + '"]').length )
					{
						var	segnalibri_da_cercare = "farmaco_1|farmaco_2|farmaco_3|farmaco_4|farmaco_5|farmaco_6|farmaco_7|farmaco_8|farmaco_9|farmaco_10|farmaco_11|farmaco_12|farmaco_13|farmaco_14|farmaco_15|";
						segnalibri_da_cercare += "qnt_1|qnt_2|qnt_3|qnt_4|qnt_5|qnt_6|qnt_7|qnt_8|qnt_9|qnt_10|qnt_11|qnt_12|qnt_13|qnt_14|qnt_15|";
						segnalibri_da_cercare += "altro_1|altro_2|altro_3|altro_4|altro_5|altro_6|altro_7|altro_8|altro_9|altro_10|altro_11|altro_12|altro_13|altro_14|altro_15|";
						segnalibri_da_cercare += "tip_somm_1|tip_somm_2|tip_somm_3|tip_somm_4|tip_somm_5|tip_somm_6|tip_somm_7|tip_somm_8|tip_somm_9|tip_somm_10|tip_somm_11|tip_somm_12|tip_somm_13|tip_somm_14|tip_somm_15|";
						segnalibri_da_cercare += "tm_1|tm_2|tm_3|tm_4|tm_5|tm_6|tm_7|tm_8|tm_9|tm_10|tm_11|tm_12|tm_13|tm_14|tm_15|";
						segnalibri_da_cercare += "note_1|note_2|note_3|note_4|note_5|note_6|note_7|note_8|note_9|note_10|note_11|note_12|note_13|note_14|note_15";
						
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {segnalibri_rtf:segnalibri_da_cercare, idmoduloversione: <?=$idmoduloversione?>},
							success: function(response){
								response_somministrazione = JSON.parse(response);
								
								for(var i=1;i<=15;i++) {
									var altro_obj = $('[name="' + response_somministrazione['altro_' + i] + '"]');

									if(altro_obj.val() == null || trim(altro_obj.val()) == '') {
										altro_obj.parent().parent().css('display','none')
									}
									
									var obj = $('[name="' + response_somministrazione['qnt_' + i] + '"]');

									obj.data('qnt_idx', i);
									obj.change(function() {										
										var qnt_idx = $(this).data('qnt_idx');

										$('[name="' + response_somministrazione['altro_' + qnt_idx] + '"]').val('');
										
										if(this.value == 3) {
											$('[name="' + response_somministrazione['altro_' + qnt_idx] + '"]').parent().parent().css('display','block');											
										} else {
											$('[name="' + response_somministrazione['altro_' + qnt_idx] + '"]').parent().parent().css('display','none');
										}											
									});
								}

								var idmoduloversione = '<? echo $idmoduloversione?>';
								var idmodulo = '<? echo $id_modulo_padre?>';
								var idpaziente = '<? echo $idpaziente?>';
								
								if(idmodulo == 206 && idmoduloversione >= 6146) {
									$.ajax({
										type: "POST",
										url: "ajax_autopopolamento_campi_moduli.php",
										data: {recupera_farmaci_anagrafica: 1, edit: 1, id_paziente: idpaziente, id_modulo_versione: idmoduloversione, id_inserimento: <? echo $idinserimento ?>, id_cartella: <? echo $idcartella ?>},
										success: function(response){

											if(response != null && response != 'null') {
												response = JSON.parse(response);

												var farmaci = response['farmaci'];
												var values_remed = response['values'];

												var options = '';
												farmaci.forEach(function(item, index) {
													let disable_opt = false;
													if(item['etichetta'].indexOf("SCADUTO") > 0 || item['etichetta'].indexOf("ESAURITO") > 0) {
														disable_opt = true;
													}
													
													options += "<option value='"+item['valore']+"' " + (disable_opt ? "disabled" : "") + " >"+item['etichetta']+"</option>";
												});
												
												options = "<option value='' >Selezionare un Valore</option>" + options;
												for(var j=0;j<15;j++) {
													var id_campo = response_somministrazione['farmaco_' + (j + 1)];

													$('[name="' + id_campo + '"]').empty();
													$('[name="' + id_campo + '"]').append(options);
													var block_all = false;
													var value_found = false;
													
													if(values_remed != null) {
														values_remed.forEach(function(item, index) {
															if(item['idcampo'] == id_campo) {
																
																if(item['segnalibro'].startsWith('farmaco_')) {
																	let farm_esaurito = $('[name="' + id_campo + '"]').find('option[value="' + item['valore'] + '"]').text().includes("| ESAURITO");
																	if(farm_esaurito) {
																		$('[name="' + id_campo + '"]').find('option[value="' + item['valore'] + '"]').removeAttr('disabled')
																	}																
																}

																$('[name="' + id_campo + '"]').val(item['valore']);
																
																farmaci.forEach(function(item1, index1) {
																	if(item1['removed_farm']) {																
																		if(item1['valore'] == item['valore']) {
																			$('[name="' + id_campo + '"]').find('option[value!="' + item1['valore'] + '"]').remove();
																			block_all = true;
																			value_found = true;
																			return;
																		} else {
																			$('[name="' + id_campo + '"]').find('option[value="' + item1['valore'] + '"]').remove();
																		}
																	}
																});												
																return;
															}
														});
													}

													if(!value_found) {
														farmaci.forEach(function(item1, index1) {
															if(item1['removed_farm']) {																
																$('[name="' + id_campo + '"]').find('option[value="' + item1['valore'] + '"]').remove();
															}
														});	
													}

													if(block_all) {
														$('[name="' + response_somministrazione['qnt_' + (j + 1)] + '"]').attr('readonly',true).css('background-color', 'gray');
														$('[name="' + response_somministrazione['tip_somm_' + (j + 1)] + '"]').find('option:not(:selected)').remove();
														$('[name="' + response_somministrazione['tm_' + (j + 1)] + '[]"]').find('option:not(:selected)').remove();
														$('[name="' + response_somministrazione['tm_' + (j + 1)] + '[]"]').find('option:selected').attr('disabled', true);
													} else {
														const html_items_names = {
															tip_somm: response_somministrazione['tip_somm_' + (j + 1)],
															tm: response_somministrazione['tm_' + (j + 1)],
															qnt: response_somministrazione['qnt_' + (j + 1)],
															note: response_somministrazione['note_' + (j + 1)]
														};

														$('select[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').change(function() {
															$('[name="' + html_items_names.tm + '[]"]').find('option').attr('selected','');
															if(this.value == '') {
																$('[name="' + html_items_names.tip_somm + '"]').val('');
																$('[name="' + html_items_names.qnt + '"]').val('');
															} else {
																let text_selected = $("option:selected", this).text();
																let match = text_selected.match(/\((\d+)\)/);
																let terap_farm_idx = '';
																
																if (match && match[1]) {
																	terap_farm_idx = match[1];
																}
															
																$.ajax({
																	type: "POST",
																	url: "ajax_autopopolamento_campi_moduli.php",
																	data: {recupera_dati_farmaco_da_somministrare: 1, id_farmaco: this.value, terap_farm_idx: terap_farm_idx, id_cartella: <? echo $idcartella ?>},
																	success: function(response){
																		response = JSON.parse(response);
																		
																		if(response['struttura']) {
																			$('[name="' + html_items_names.note + '"]').val("Somministrazione d'urgenza autorizzata telefonicamente da medico responsabile a cui seguira aggiornamento terapia farmacologica entro le successive 24h");
																		} else if(idpaziente != response['id_paziente']) {
																			$('[name="' + html_items_names.note + '"]').val("Somministrazione d'urgenza autorizzata da medico responsabile a cui seguira aggiornamento terapia farmacologica entro le successive 24h");
																		} else {
																			$('[name="' + html_items_names.note + '"]').val("");
																		}
																		
																		var tip_somm = response['tip_somm'] == undefined || response['tip_somm'] == '' ? '' : response['tip_somm'];																
																		var qnt = response['qnt'] == undefined || response['qnt'] == '' ? '' : response['qnt'];
																		
																		$('[name="' + html_items_names.tip_somm + '"]').val(tip_somm);																	
																		$('[name="' + html_items_names.qnt + '"]').val(qnt);
																		
																		if(response['orario'] != undefined && response['orario'] != '') {
																			var orario = response['orario'].split(";");
																			
																			for(var i=0; i<orario.length; i++) {
																				$('[name="' + html_items_names.tm + '[]"]').find('option[value="'+orario[i]+'"]').attr('selected','true');
																			}
																		} else {
																			$('[name="' + html_items_names.tm + '[]"]').find('option').attr('selected','');																		
																		}
																	}
																});
															}
														});
													}
												}
											}
										}
									});
								} else {
									$.ajax({
										type: "POST",
										url: "ajax_autopopolamento_campi_moduli.php",
										data: {recupera_valore_terapia_farmacologica: 1, id_cartella: <? echo $idcartella ?>},
										success: function(response){

											if(response != null && response != 'null') {
												response = JSON.parse(response);

												var options = '';
												Object.keys(response).forEach(idx => {
													options += "<option value='"+response[idx]['valore']+"' >"+response[idx]['etichetta']+"</option>";
												});

												options = "<option value='' >Selezionare un Valore</option>" + options;
												for(var j=0;j<15;j++) {
													var val_item = $('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"] option:selected').val();

													$('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').empty();
													$('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').append(options);
													$('[name="' + response_somministrazione['farmaco_' + (j + 1)] + '"]').val(val_item);
												}
											}
										}
									});
								}
							}
						});
					}


					// Modulo per l'esercizio diritti di oscuramento FSE
					// prendo l'elenco dei moduli della CC interessata atti ad essere oscurati e li inserisco nella sua combo apposita
					if (id_moduli_oscuramento_fse)
					{
						$.ajax({
							type: "POST",
							url: "ajax_autopopolamento_campi_moduli.php",
							data: {id_cartella_fse: <? echo $idcartella ?>, idmoduloversione: <?=$idmoduloversione?>, idinserimento: <?=$idinserimento?> },
							success: function(response)
							{
								if(trim(response) == 0) {
									$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().parent().append('\
										<div style="float:left; padding:20px 10px 0 0;clear:both;">\
											<input type="hidden" disabled value=""><strong>ATTENZIONE: in questa cartella clinica non sono presenti moduli da poter oscurare.</strong>\
										</div>');
								}
								else if(trim(response) !== "" )
								{
									var moduliArray = JSON.parse(response);	
									var moduliArray_len = moduliArray.length;

									// nascondo il primo elemento vuoto della combo utilizzato come riferimento per l'append
									$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().hide();	
										
									if(moduliArray_len > 0 )
									{
										var z = 2;
										for (var i = 0; i < moduliArray_len; i++) 
										{ 
									
									
									// controllo se cambia modulo ed inserisco l'intestazione
											if(idmodulopadre_prec !== moduliArray[i].id_modulo_padre)
											{
												$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().parent().append('\
													<div style="float:left; padding:20px 10px 0 0;clear:both;">\
														<input type="hidden" disabled value=""><strong>' + moduliArray[i].nome + ':</strong>\
													</div>');
											}
											
									// inserisco le varie date delle varie istanze (una o piu')
											$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().parent().append('\
												<div style="float:left; padding:0 10px 0 20px;clear:both;">\
													<input type="checkbox" ' + moduliArray[i].checked + ' name="'+id_moduli_oscuramento_fse+'['+(z)+']" data-idmodulopadre="'+moduliArray[i].id_modulo_padre+'" \
														value="<?=$idcartella?>|' + moduliArray[i].id_modulo_versione + '|' + moduliArray[i].data_usa + '|' + moduliArray[i].nome + ' (compilazione del ' + moduliArray[i].data + ')">\
														Data: ' + moduliArray[i].data + '\
												</div>');
											z++;
											
									// inserisco la option "tutti" se prevista
											if(moduliArray[i].option_tutti == "s") 
											{
												$('[name="' + id_moduli_oscuramento_fse + '[1]"]').parent().parent().append('\
													<div style="float:left; padding:0 10px 0 20px;clear:both;">\
														<input type="checkbox" ' + moduliArray[i].checked_tutti + ' name="'+id_moduli_oscuramento_fse+'['+(z)+']" data-idmodulopadre="'+moduliArray[i].id_modulo_padre+'" \
														value="<?=$idcartella?>|' + moduliArray[i].id_modulo_versione + '|tutti|nodata|' + moduliArray[i].nome + ' (tutte le compilazioni)"\
														onclick="$(\'input[type=checkbox][data-idmodulopadre='+moduliArray[i].id_modulo_padre+']\').attr(\'checked\', !$(\'input[type=checkbox][data-idmodulopadre='+moduliArray[i].id_modulo_padre+']\').attr(\'checked\') );">\
														Tutte le compilazioni\
													</div>');
														// nell'onclick c'� una funzione che fa il toggle tra checked e unchecked
												z++;
											}
									
									// quando cambia il modulo, aggiorna la var "idmodulopadre_prec" per i successivi controlli 
											if(idmodulopadre_prec !== moduliArray[i].id_modulo_padre)
												idmodulopadre_prec = moduliArray[i].id_modulo_padre;
											
										}
									}								
								}
							}
						});	
					}
					// presi gli ID dei campi che mi interessano, eseguo gli autopopolamenti se esistono nel DOM (per cui se � stato aperto il modulo interessato)
					
					// se sono in un Programma Riabilitativo RESIDENZIALE, mi prendo il campo "Esiti risultati" per l'autopopolamento nella successiva ajax
					var id_campo_esiti_raggiungimento_risultati_res = "";
					if( $('[name="' + id_campo_esiti_raggiungimento_risultati_fms + '"]').length ) { id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fms;	segnalibro_campo_esiti = "testo_val_fms"; }
					if( $('[name="' + id_campo_esiti_raggiungimento_risultati_fvs + '"]').length ) { id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fvs;	segnalibro_campo_esiti = "testo_val_fvs"; }	
					if( $('[name="' + id_campo_esiti_raggiungimento_risultati_fcr + '"]').length ) { id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fcr;	segnalibro_campo_esiti = "testo_val_fcr"; }
					if( $('[name="' + id_campo_esiti_raggiungimento_risultati_fd + '"]').length )  { id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fd;	segnalibro_campo_esiti = "testo_val_fd"; }
					if( $('[name="' + id_campo_esiti_raggiungimento_risultati_fccl + '"]').length) { id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_fccl;	segnalibro_campo_esiti = "testo_val_fccl"; }
					if( $('[name="' + id_campo_esiti_raggiungimento_risultati_ifcg + '"]').length) { id_campo_esiti_raggiungimento_risultati_res = id_campo_esiti_raggiungimento_risultati_ifcg;	segnalibro_campo_esiti = "testo_val_ifcg"; }
					
					if(id_campo_esiti_raggiungimento_risultati_res !== "") {					
					// prendo il valore del campo del modulo "Valutazioni/osservazioni" ad esso collegato se, e solo se, l'ultima istanza di tale modulo 
					// sia stata compilata nei 30 giorni successivi alla data di compilazione del modulo "Programma Riabilitativo"
						if(segnalibro_campo_esiti) {
							$.ajax({
								type: "POST",
								url: "ajax_autopopolamento_campi_moduli.php",
								data: {id_cartella: <? echo $idcartella ?>, segnalibro_campo_esiti:segnalibro_campo_esiti},
								success: function(response)
								{
									if(response.trim() !== "") 
									{
										response = JSON.parse(response);
										$("[name='" + id_campo_esiti_raggiungimento_risultati_res + "']").val( response.esiti_valutazione 	);
									}
								}
							});
						}
					}
					
					
					// Prende i valori delle select dei soli moduli "PROGRAMMA RIABILITATIVO (ex26)" e ne estrare i dati per autocompletamento
					if ( $('[name="' + id_campo_metodologie_operative_ajax_ms + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_fs + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_cr + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_cc + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_acp + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_aea + '"]').length ||
						 $('[name="' + id_campo_metodologie_operative_ajax_rrs + '"]').length  )
					{
						
						// se sono in un "Programma Riabilitativo (ex26)", mi salvo il nome del campo "Raggiungimento risultati" per gestirlo dopo
						if( $('[name="' + id_campo_raggiungimento_risultati_ms + '"]').length  ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_ms;
						if( $('[name="' + id_campo_raggiungimento_risultati_fs + '"]').length  ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_fs;
						if( $('[name="' + id_campo_raggiungimento_risultati_cr + '"]').length  ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_cr;
						if( $('[name="' + id_campo_raggiungimento_risultati_cc + '"]').length  ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_cc;
						if( $('[name="' + id_campo_raggiungimento_risultati_acp + '"]').length ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_acp;
						if( $('[name="' + id_campo_raggiungimento_risultati_aea + '"]').length ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_aea;
						if( $('[name="' + id_campo_raggiungimento_risultati_rrs + '"]').length ) 		id_campo_raggiungimento_risultati = id_campo_raggiungimento_risultati_rrs;
						
						
						$('[name="' + id_campo_metodologie_operative_ajax_ms + '"],[name="' + id_campo_metodologie_operative_ajax_fs + '"],[name="' + id_campo_metodologie_operative_ajax_cr + '"],[name="' + id_campo_metodologie_operative_ajax_cc + '"],[name="' + id_campo_metodologie_operative_ajax_acp + '"],[name="' + id_campo_metodologie_operative_ajax_aea + '"],[name="' + id_campo_metodologie_operative_ajax_rrs + '"]').change(function () 
						{
							var select_name	= $(this).attr('name');
							var metodologia = $(this).find('option:selected').text();	//$("[name='"+select_name+"'] option:selected").text();							
							if (metodologia !== "Selezionare un Valore")
							{
								switch (parseInt(select_name)) {
									case id_campo_metodologie_operative_ajax_ms:		var name_metodologie = id_campo_metodologie_operative_ajax_ms;		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_ms;		var name_misure_desito = id_campo_misure_desito_ajax_ms;		var area_funzionale = "Mobilita e Spostamenti";					break;
									case id_campo_metodologie_operative_ajax_fs:		var name_metodologie = id_campo_metodologie_operative_ajax_fs;		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_fs;		var name_misure_desito = id_campo_misure_desito_ajax_fs;		var area_funzionale = "Area funzioni sensomotorie";				break;
									case id_campo_metodologie_operative_ajax_cr:		var name_metodologie = id_campo_metodologie_operative_ajax_cr;		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_cr;		var name_misure_desito = id_campo_misure_desito_ajax_cr;		var area_funzionale = "Competenze comunicativo relazionali";	break;
									case id_campo_metodologie_operative_ajax_cc:		var name_metodologie = id_campo_metodologie_operative_ajax_cc;		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_cc;		var name_misure_desito = id_campo_misure_desito_ajax_cc;		var area_funzionale = "Competenze cognitivo comportamentali";	break;
									case id_campo_metodologie_operative_ajax_acp:		var name_metodologie = id_campo_metodologie_operative_ajax_acp;		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_acp;		var name_misure_desito = id_campo_misure_desito_ajax_acp;		var area_funzionale = "Autonomia della cura della persona";		break;
									case id_campo_metodologie_operative_ajax_aea:		var name_metodologie = id_campo_metodologie_operative_ajax_aea;		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_aea;		var name_misure_desito = id_campo_misure_desito_ajax_aea;		var area_funzionale = "Area emotiva - affettiva";				break;
									case id_campo_metodologie_operative_ajax_rrs:		var name_metodologie = id_campo_metodologie_operative_ajax_rrs;		var name_elenco_obiettivi = id_campo_elenco_obiettivi_ajax_rrs;		var name_misure_desito = id_campo_misure_desito_ajax_rrs;		var area_funzionale = "Riadattamento e reinserimento sociale";	break;
								}
								
								$.ajax({
									type: "POST",
									url: "ajax_autopopolamento_campi_moduli.php",
									data: {metodologia: metodologia, area_funzionale:area_funzionale},
									success: function(response){
										if(response.trim() !== "") 
										{
											response = JSON.parse(response);
										   
											//$("[name='" + name_metodologie + "']"	  ).val( 	   metodologia 			);
											$("[name='" + name_elenco_obiettivi + "']").val( response.elenco_obiettivi 	);
											$("[name='" + name_misure_desito + "']"	  ).val( response.misure_desito  );
									   
										} else {
											//$("[name='" + name_metodologie + "']"	  ).val("");
											$("[name='" + name_elenco_obiettivi + "']").val("");
											$("[name='" + name_misure_desito + "']"	  ).val("");
										}
									}
								});
							} else {
								$("[name='" + name_metodologie + "']"	  ).val("");
								$("[name='" + name_elenco_obiettivi + "']").val("");
								$("[name='" + name_misure_desito + "']"	  ).val("");
							}
						});
						
						
					// Se modifico un "Programma Riabilitativo (ex26)", prendo il valore del campo "Risultato" del modulo "Valutazioni/relazioni" ad esso collegato
					// se, e solo se, il "Programma Riabilitativo" ha allegato1 caricato e il modulo "Valutazioni/relazioni" ad esso collegato risulta compilato
							 if ( $('[name="' + id_campo_risultato_ms + '"]').length  )	{ var campo_risultato = id_campo_risultato_ms;	var	segnalibro_risult_mod_valutaz = "valutazione_risultati_ajax_fisioterapica"; }
						else if ( $('[name="' + id_campo_risultato_fs + '"]').length  )	{ var campo_risultato = id_campo_risultato_fs;	var	segnalibro_risult_mod_valutaz = "valutazione_risultati_ajax_logopedica"; }
						else if ( $('[name="' + id_campo_risultato_cr + '"]').length  )	{ var campo_risultato = id_campo_risultato_cr;	var	segnalibro_risult_mod_valutaz = "valutazione_risultati_ajax_logopedica"; }
						else if ( $('[name="' + id_campo_risultato_cc + '"]').length  )	{ var campo_risultato = id_campo_risultato_cc;	var	segnalibro_risult_mod_valutaz = "valutazione_risultati_ajax_psicomotoria"; }
						else if ( $('[name="' + id_campo_risultato_acp + '"]').length )	{ var campo_risultato = id_campo_risultato_acp;	var	segnalibro_risult_mod_valutaz = "valutazione_risultati_ajax_op_semiconvitto_a"; }
						else if ( $('[name="' + id_campo_risultato_aea + '"]').length )	{ var campo_risultato = id_campo_risultato_aea;	var	segnalibro_risult_mod_valutaz = "valutazione_risultati_ajax_psicot_individuale|valutazione_risultati_ajax_psicot_familiare"; }
						else if ( $('[name="' + id_campo_risultato_rrs + '"]').length )	{ var campo_risultato = id_campo_risultato_rrs;	var	segnalibro_risult_mod_valutaz = "valutazione_risultati_ajax_op_semiconvitto_b"; }
					 
						if (segnalibro_risult_mod_valutaz)
						{
							$.ajax({
								type: "POST",
								url: "ajax_autopopolamento_campi_moduli.php",
								data: {segnalibro_risult_mod_valutaz:segnalibro_risult_mod_valutaz, idmoduloversione: <?=$idmoduloversione?>, idcartella: <?=$idcartella?>, idinserimento: <?=$idinserimento?>},
								success: function(response_ris){
										
									if(response_ris.trim() !== "") 
									{
										response_ris = JSON.parse(response_ris);
										
										if ( $("[name='" + campo_risultato + "']").val().trim() == "")		// se non hanno scritto nulla in precedenza, scrivo dal modulo valutazioni, altrimenti lascio quello scritto dagli operatori
											$("[name='" + campo_risultato + "']").val( response_ris.risultati_mod_valutaz );

									} else {
										$("[name='" + campo_risultato + "']").val("");
									}
									
									if(response_ris.allegato_caricato == "no" && $('[name="' + id_campo_raggiungimento_risultati + '"]').length) {
										$('[name="' + id_campo_raggiungimento_risultati + '"]').html('<option value="">!! Caricare prima allegato 1 !!</option>');
									}
								}
							});
						}

						
					}
				}
			}
		});
		
		$.mask.addPlaceholder('~',"[+-]");	
		$(".campo_data").mask("99/99/9999");				
		$(".campo_data").blur(function(){ 
			var valore = $(this).val(); 
			if(valore != ''){
				alert("Sei sicuro di voler inserire questa data: "+valore+" ?");
			}
		});
		
		
	});	
	</script>
</div>

<?

}

function visualizza_modulo()
{

$idcartella=$_REQUEST['idcartella'];
$cartella=$_REQUEST['cartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idinserimento=$_REQUEST['idinserimento'];
$call=$_REQUEST['call'];
$impegnativa=$_REQUEST['impegnativa'];


$conn = db_connect();
$query="select id,idmodulo,nome,replica,modello_word from moduli where id=$idmoduloversione";
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$nome_modulo=$row1['nome'];
$modello_word=strpos($row1['modello_word'],".");
$id_modulo_padre=$row1['idmodulo'];
mssql_free_result($rs1);

$query="SELECT Cognome, Nome FROM utenti WHERE IdUtente=$idpaziente";
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$cognome_pz=$row1['Cognome'];
$nome_pz=$row1['Nome'];
$txt_dati_modulo="Paziente: <b>$cognome_pz  $nome_pz</b> - Cartella Clinica: <b>$cartella</b>\n\n";

if ($idcartella=="")$idcartella=0;
if ($impegnativa=="")
	$imp_query="id_impegnativa=NULL";
	else
	$imp_query="id_impegnativa=$impegnativa";
if ($idcartella!=0)
	$paz_query="1=1";
	else
	$paz_query="id_paziente=$idpaziente";
$query="select * from re_istanze_moduli where id_cartella=$idcartella and id_modulo_padre=$id_modulo_padre and id_inserimento=$idinserimento AND $imp_query and $paz_query";
//echo($query);
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$datains=formatta_data($row1['datains']);
$orains=$row1['orains'];
$operatore=$row1['nome'];
mssql_free_result($rs1);

$query="select * from re_moduli_valori where id_inserimento=$idinserimento and id_cartella=$idcartella and id_modulo_padre=$id_modulo_padre and idmoduloversione = $idmoduloversione and $imp_query and $paz_query order by peso ASC";
//echo($query);
$rs1 = mssql_query($query, $conn);
if(!$rs1) error_message(mssql_error());

srand((double)microtime()*1000000);  
$random=rand(0,999999999999999);
$filename_tmp = "tmp_data_".$random."_".$nome_modulo.".txt";
$filename_tmp=str_replace("/","_",$filename_tmp);
$destination_path =MODELLI_WORD_DEST_PATH;	
?>
<script>inizializza();</script>
<div id="sanitaria">
<div class="titoloalternativo">
            <h1>cartella: <?=$cartella?></h1>	       
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="";$xml="";
			if ($modello_word!=""){
				if ($idcartella>0) 
					$doc="stampa_modulo.php?idcartella=$idcartella&idimpegnativa=$impegnativa&idinserimento=$idinserimento&idmodulopadre=$id_modulo_padre&ngid=".$_SESSION['UTENTE']->get_gid();
					else
					$doc="stampa_modulo.php?idcartella=0&idimpegnativa=$impegnativa&idinserimento=$idinserimento&idpaziente=$idpaziente&idmodulopadre=$id_modulo_padre&ngid=".$_SESSION['UTENTE']->get_gid();
			}
			$pdf="pdfclass/stampa_istanza.php?file=$filename_tmp&user=".$_SESSION['UTENTE']->get_userid()."&idpaz=$idpaziente&idcart=$idcartella&idmod=$id_modulo_padre";
			include_once('include_stampa.php');
			?>

</div>
<div class="titoloalternativo">
            <h1>modulo: <?=pulisci_lettura($nome_modulo)?></h1>
</div>

<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_istanze('<?=$cartella?>','<?=$idcartella?>','<?=$idpaziente?>','<?=$idmoduloversione?>','sanitaria','<?=$call?>','<?=$impegnativa?>')" >ritorna elenco istanze</a></div>
		<!--
		<?
		if ($modello_word!=""){
		if ($idcartella>0) {?>
		<div class="aggiungi aggiungi_left"><a href="stampa_modulo.php?idcartella=<?=$idcartella?>&idinserimento=<?=$idinserimento?>&idmodulopadre=<?=$id_modulo_padre?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>">stampa modulo</a></div>
		<? } else {?>
		<div class="aggiungi aggiungi_left"><a href="stampa_modulo.php?idcartella=0&idinserimento=<?=$idinserimento?>&idpaziente=<?=$idpaziente?>&idmodulopadre=<?=$id_modulo_padre?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>">stampa modulo</a></div>
		<? }
		}?>
		-->
	</div>
</div>

<!-- qui va il codice dinamico dei campi -->
<?
if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){?>
	<div class="titolo_pag"><h1>modulo creato da: <?=$operatore?> il: <?=$datains?> ore: <?=$orains?></h1></div>	
	<?$txt_dati_creazione="Nome Modulo: <b>".strtoupper($nome_modulo)."</b>\ncreato da: $operatore il $datains alle ore $orains \n\n";?>
	<?}else{
	$txt_dati_creazione="";
	}?>
<form id="myForm" method="post" name="form0" enctype="multipart/form-data" action="re_pazienti_sanitaria_POST.php">
	
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>

	<input type="hidden" name="action" value="update_modulo" />
	<input type="hidden" name="idcartella" value="<?=$idcartella?>" />
	<input type="hidden" name="idpaziente" value="<?=$idpaziente?>" />
	<input type="hidden" name="idinserimento" value="<?=$idinserimento?>" />
	<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
	
	
	<div class="blocco_centralcat">
		<?

		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		$i=1;	
		
		if($impegnativa!=""){?>
		 <div class="rigo_mask">
		 <div class="testo_mask">impegnativa</div>            
			<div class="campo_mask "><? 
				$query = "SELECT * from re_pazienti_impegnative where idimpegnativa=".$impegnativa;	
				$rs_q = mssql_query($query, $conn);				
				if($row_q = mssql_fetch_assoc($rs_q)){						
					$data_auth_asl=formatta_data($row_q['DataAutorizAsl']);
					$prot_auth_asl=$row_q['ProtAutorizAsl'];
					$regime=$row_q['regime'];
				}
				echo($prot_auth_asl." ".$data_auth_asl." - ".$regime);
				$txt_dati_impegnativa="<b>impegnativa</b>\n".$prot_auth_asl." ".$data_auth_asl." - ".$regime."\n\n"?>
			</div>
			</div>
			<?}		
		$txt_dati_valori="";

		while($row1 = mssql_fetch_assoc($rs1)){			
			$idvalore= $row1['id_istanza_dettaglio'];
			$idcampo= $row1['idcampo'];
			$etichetta= $row1['etichetta'];
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
			$valore=$row1['valore'];
		
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
		?>
		
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<div class="etichetta_campi"><?=pulisci_lettura($etichetta)?></div>			
			<?
			$txt_dati_valori.="2<".pulisci_lettura($etichetta).">\n\n";
			} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if(($multi==1)or($multi==2)) echo($stile);?>> 
            <div class="testo_mask"><?=pulisci_lettura($etichetta)?></div>            
			<div class="campo_mask ">
                <?
				$txt_dati_valori.="<b>".pulisci_lettura($etichetta)."</b>\n";
				if(($tipo==1)or($tipo==3)or($tipo==6)or($tipo==8)){
					echo(pulisci_lettura($valore));
					$txt_dati_valori.=pulisci_lettura($valore)."\n\n";
				  }
				elseif($tipo==2){
				  // recupera il dato
					/*
					$db 	= odbc_connect(DSN, DB_USER, DB_PASSWORD);
					$res  	= odbc_exec ($db, "SET TEXTSIZE ".(4 * MAX));
					$res 	= odbc_exec ($db, "SELECT valore FROM istanze_dettaglio WHERE id_istanza_dettaglio=$idvalore");
					$valGio = odbc_result ($res, "valore");
					*/
					//odbc_longreadlen ($res, MAX);
					$conn 		= db_connect();
					$queryGio   ="SELECT valore FROM istanze_dettaglio WHERE id_istanza_dettaglio=$idvalore";
					$rsGio 		= mssql_query($queryGio, $conn);
					$rowGio		= mssql_fetch_assoc($rsGio);
					$valGio     = $rowGio['valore'];
					$text = str_replace("?","'",pulisci_lettura($valGio));
					$text = str_replace("\n","<br>",$text);
					$valore = $text;
					odbc_close($db);					
					echo($valore);
					$txt_dati_valori.=($valore)."\n\n";
				}					  
				elseif($tipo==4)
				{	
					if($id_modulo_padre == 206 && $idmoduloversione >= 6146 && strpos($row1['segnalibro'], 'farmaco_') !== false) {
						$_query = "SELECT nome, dosaggio, lotto 
									FROM farmaci 
									WHERE id = $valore";		
						$_rs = mssql_query($_query, $conn);
						$farm = mssql_fetch_assoc($_rs);
						mssql_free_result($_rs);
						
						$nome = '';
						if(!empty($farm['nome'])) {
							$nome = $farm['nome'] . ' ' . $farm['dosaggio'] . ' - Lot. ' . $farm['lotto'];
						}
						
						echo $nome;
					} else {
						$query2="select * from re_moduli_combo where idcampo=$idcampo order by peso";		
						$rs2 = mssql_query($query2, $conn);

						if(!$rs2) error_message(mssql_error());

						if(($multi==1)or($multi==2)or($multi==3)){
							$et="";
							$valore = split(";",$valore);					
							while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								$separatore=$row2['separatore'];
								
								switch ($separatore){
									case 0:
										$sep="<br/>";
									break;
									case 1:
										$sep=" - ";
									break;
									case 2:
										$sep=" , ";
									break;
								}
								
								if($multi==2){
									if (in_array($valore1,$valore)) 
										$et.="[X] ".$etichetta.$sep;
									else
										$et.="[&nbsp;&nbsp;] ".$etichetta.$sep;	
								}
								elseif($multi==1 || $multi==3) {
									if($etichetta == 'Caricamento moduli FSE da oscurare..')
									{
										$queryGio   ="SELECT valore FROM istanze_dettaglio WHERE id_istanza_dettaglio=$idvalore";
										$rsGio 		= mssql_query($queryGio, $conn);
										$rowGio		= mssql_fetch_assoc($rsGio);
										$valGio     = $rowGio['valore'];
										$et.= str_replace(';', $sep, $valGio);
									} else {
										if (in_array($valore1,$valore)) 
											$et.=$etichetta.$sep;
									}
								}
							}
							$et=substr($et,0,strlen($et)-2);						
							echo(pulisci_lettura($et));
							$txt_dati_valori.=pulisci_lettura($et)."\n\n";
						} else {			
							while ($row2 = mssql_fetch_assoc($rs2))
							{								
								if ($row2['valore']==$valore) {
									echo(pulisci_lettura($row2['etichetta']));
									$txt_dati_valori.=pulisci_lettura($row2['etichetta'])."\n";
								}
							}
							
							$txt_dati_valori.="\n";	
							//$valore1=$row2['etichetta'];
							//echo($valore1);
						}
					}
				}
				elseif($tipo==7){
?>
		<div id="file_name" style="display:block;"><?if((trim($valore)!="")and(strpos($valore,"."))){?><a target="_new" href="load_file.php?filename=<?=$valore?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>" style="text-decoration:none;" ><img src="images/view.png" /> <?=$valore?></a><?}?>
		<input type="hidden" class="scrittura" name="<?=$idcampo?>_file" value="<?=$valore?>" >		
		</div>		
<?
		if(trim($valore)=="") $valore="non presente";
		$txt_dati_valori.=pulisci_lettura($valore);
}				  elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";		
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$et="";
						$valore=split(";",$valore);									
						while ($row2 = mssql_fetch_assoc($rs2))
							{									
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								if (in_array($valore1,$valore)) $et.=$etichetta."<br/>";									
							}
						$et=substr($et,0,strlen($et)-2);						
						echo(pulisci_lettura($et));
						$txt_dati_valori.=pulisci_lettura($et)."\n\n";
					 }else{	
									 
					while ($row2 = mssql_fetch_assoc($rs2))	{	
					

						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore1=$row2['valore'];
						$etichetta1=$row2['etichetta'];
						$stampa=$row2['stampa'];
						$v=(int)($valore);	
						
						//MODIFICA EFFETTUATA IL 30/05/2016 PER RISOLVERE STAMPA COMBO MENOMAZIONE COD20160530
						//if(($v==$valore1) and($stampa)){ 
						//echo $v.' -------------- '.$valore1.'<br>';
						if(($v==$valore1) and ($stampa)){ 
							echo(pulisci_lettura($etichetta1));
							$txt_dati_valori.=pulisci_lettura($etichetta1)."\n\n";
						}
						else if ($v == $idcampocombo){
							$et = array();
							if(strpos($etichetta1,'|')){
								$et = explode('|',$etichetta1);
								$etichetta1 = trim($et[0]);
							}
							echo $etichetta1;
						}
						else{						
							$rinc=rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
						}
					}
				   }
				  }
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
	$content=$txt_dati_creazione.$txt_dati_modulo.$txt_dati_impegnativa.$txt_dati_valori;
	$content=str_replace("<br/>","\n",$content);
	$content=str_replace("<br","",$content);
	$handle = fopen($destination_path.$filename_tmp, "w");
	fwrite($handle,$content);	
	?>
	
</div>
<?
if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){

	$query="SELECT  * FROM re_log_modifiche_istanze WHERE (idcartella = $idcartella) AND (id_modulo_versione = $idmoduloversione) AND (istanza = $idinserimento) order by id desc";
	//echo($query);
	$rs = mssql_query($query, $conn);
	if(mssql_num_rows($rs)>0){
	?>
	<div class="blocco_centralcat">
		<div class="titolo_pag"><h1>cronologia modifiche:</h1></div>
		<?
		$i=1;
		while($row=mssql_fetch_assoc($rs)){
			?><div class="campo_mask "><?
			echo("$i - <strong>data</strong>: ".formatta_data($row['data_modifica'])." ".$row['ora_modifica']." - <strong>operatore</strong>: ".$row['operatore']);	
			$i++;
			?></div><?
		}
	?></div>
	<?}
}?>

<div class="titolo_pag">
		<div class="comandi">			
		</div>
	</div>
	</form>

</div>
<?

}



function update_modulo()
{
	$idcartella			= $_POST['idcartella'];
	$idpaziente			= $_POST['idpaziente'];
	$idmoduloversione	= $_POST['idmoduloversione'];
	$idinserimento		= $_POST['idinserimento'];
	$impegnativa		= $_POST['impegnativa'];
	$salvaeallega 		= $_POST['salvaeallega'];							// s: salvare, convertire rtf in pdf e associare documento alla CC
		
	$conn = db_connect();
	$query="select idmodulo,nome from moduli where id=$idmoduloversione";
	$rs = mssql_query($query, $conn);
		
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs))
	{
		$nome_modulo=$row['nome'];
		$idmodulo=$row['idmodulo'];
	}
	
	$datains = date('d/m/Y');
	$orains = date('H:i');
	$ipins = $_SERVER['REMOTE_ADDR'];
	$opeins = $_SESSION['UTENTE']->get_userid();
	$query_log="insert into log_modifica_istanza (id_utente,id_modulo_versione,istanza,idcartella,data_modifica,ora_modifica,ope_modifica,ip_modifica) values ($idpaziente,$idmoduloversione,$idinserimento,$idcartella,'$datains','$orains',$opeins,'$ipins')";
	$result_log = mssql_query($query_log, $conn);
	
	
	if($impegnativa=='si'){
		$idimpegnativa=$_POST['idimpegnativa'];
		$query_imp=" and id_impegnativa=$idimpegnativa";
	}else{
		$query_imp="";
	}
	
	/*recupero l'id testata dell'istanza da modificare*/
	$query="select id_istanza_testata as id_istanza, allegato, allegato2, allegato3, allegato4, firmato_digitalmente
			from istanze_testata where  id_inserimento=$idinserimento and id_cartella=$idcartella and id_modulo_padre=$idmodulo".$query_imp;
	$rs1 = mssql_query($query, $conn);
	$row1 = mssql_fetch_assoc($rs1);
	$id_istanza = $row1['id_istanza'];
	$allegato = $row1['allegato'];
	$allegato2 = $row1['allegato2'];
	$allegato3 = $row1['allegato3'];
	$allegato4 = $row1['allegato4'];
	$firmato_digitalmente = $row1['firmato_digitalmente'];
	
	mssql_free_result($rs1);
	scrivi_log ($id_istanza,'istanze_testata','agg','id_istanza_testata');
	/*fine recupero*/	
	
	//$query="select idcampo, id_istanza_dettaglio from istanze_dettaglio where  id_istanza_testata=$id_istanza order by id_istanza_dettaglio asc";	
	$query="SELECT idcampo, peso FROM campi WHERE (idmoduloversione = $idmoduloversione) ORDER BY peso";
//echo($query);
	$rs1 = mssql_query($query, $conn);
	if(!$rs1) error_message(mssql_error());

	$farmaci = array();
	$val_farmaci = array();
	$lista_farmaci = array();	
	
	// Recupero prima di andarli a modificare tutti i farmaci presenti nell'istanza salvata, li andr� poi a
	// confrontare con quelli che ho nell'istanza corrente per vedere se ci sono differenze
	if($idmodulo == 206 && $idmoduloversione >= 6146) {
		$farmaci_struttura_da_rimuovere = array();
		$farmaci_paz_paz_da_rimuovere = array();
		$lista_farmaci_presenti_db = array(); 	// farmaci presente nell'istanza dettaglio prima del salvataggio
		$lista_farmaci_presenti_dif = array();	// lista di farmaci che sono stati sostituiti in un campo specifico
	
		$_sql_ist = "SELECT cmp.idcampo, cmp.segnalibro, ist_det.valore
						FROM istanze_dettaglio ist_det
						INNER JOIN campi cmp on cmp.idcampo = ist_det.idcampo 
							AND (cmp.segnalibro like 'farmaco_%' OR cmp.segnalibro like 'qnt_%')
						WHERE ist_det.id_istanza_testata = (
								SELECT MAX(id_istanza_testata) as id_istanza_testata
								FROM istanze_testata
								WHERE id_modulo_padre = 206  and id_paziente = $idpaziente
							) 
							AND ist_det.valore IS NOT NULL AND trim(ist_det.valore) <> ''";
		$rs_ist = mssql_query($_sql_ist, $conn);
		$_row_det_list = array();
		$_lista_farmaci_presenti_db = array();
		
		while($row_det = mssql_fetch_assoc($rs_ist)) {
			$segnalibro = explode('_', $row_det['segnalibro']);			
			$idx = array_pop($segnalibro);
			$segnalibro = implode('_', $segnalibro);

			$_lista_farmaci_presenti_db[$idx][$segnalibro] = $row_det['valore'];
			$_row_det_list[] = $row_det;
			
			if(strpos($segnalibro, 'farmaco') !== false) {
				$_lista_farmaci_presenti_db[$idx]['idcampo'] = $row_det['idcampo'];
			}			
		}

		foreach($_row_det_list as $row_det) {
			$segnalibro = explode('_', $row_det['segnalibro']);			
			$idx = array_pop($segnalibro);
			
			$lista_farmaci_presenti_db[$_lista_farmaci_presenti_db[$idx]['idcampo']] = $_lista_farmaci_presenti_db[$idx];
		}
	}

	$op_emotrasfusioni_2 = '';
	$op_emotrasfusioni_3 = '';
	$op_emotrasfusioni_4 = '';

	while($row1 = mssql_fetch_assoc($rs1))
	{
		$idcampo= $row1['idcampo'];
		$query="select tipo,idcampo,etichetta,segnalibro from campi where idcampo=$idcampo";
		$rs_c=mssql_query($query, $conn);
		$row_c=mssql_fetch_assoc($rs_c);
		$tipo=$row_c['tipo'];
		
		if (isset($_POST[$idcampo])){
			$valore = $_POST[$idcampo];
		} else {
			$valore = "";
		}
		
		//$valore=$_POST[$idcampo];
		//echo($valore);
		//INIZIO ---ICDM9
		if($row_c['etichetta']=='ICD 9'){
			
			$multi_ram=1;										
			$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";						
			$rs2 = mssql_query($query2, $conn);						
			if(!$rs2) error_message(mssql_error());													 
			while ($row2 = mssql_fetch_assoc($rs2)){							
				$idc=$row2['idcombo'];
				$idcampocombo=$row2['idcampocombo'];
				$valore1=$row2['valore'];
				$etichetta1=$row2['etichetta'];
				$v=(int)($valore);
				$valore_m=get_rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
				if($valore_m!='') break;
			}
			$qry_imp="UPDATE impegnative SET icdm9='$valore_m' where idimpegnativa=$idimpegnativa";
			$rs_imp=mssql_query($qry_imp, $conn);
		}
		//FINE --- ICDM9
				
		//INIZIO ---ICDM92
		if($row_c['etichetta']=='ICD 9 -2'){
			
			$multi_ram=1;										
			$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";						
			$rs2 = mssql_query($query2, $conn);						
			if(!$rs2) error_message(mssql_error());													 
			while ($row2 = mssql_fetch_assoc($rs2)){							
				$idc=$row2['idcombo'];
				$idcampocombo=$row2['idcampocombo'];
				$valore1=$row2['valore'];
				$etichetta1=$row2['etichetta'];
				$v=(int)($valore);
				$valore_m=get_rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
				if($valore_m!='') break;
			}
			$qry_imp="UPDATE impegnative SET icd92='$valore_m' where idimpegnativa=$idimpegnativa";
			$rs_imp=mssql_query($qry_imp, $conn);
		}
		//FINE --- ICDM92
				
		// INIZIO - MODULI PER OSCURAMENTO DA FSE (Fascicolo Sanitario Elettronico)
		if($row_c['etichetta']=='Moduli FSE da oscurare')	//etichetta del campo
		{
			$nuovo_valore = "";
			$idcampo_moduli = $idcampo;
			
			foreach($valore as $arr_elenco_moduli) {
				$arr_dettagli_modulo = explode('|', $arr_elenco_moduli);
				$id_c 			= $arr_dettagli_modulo[0];				// id cartella
				$id_mod_vers 	= $arr_dettagli_modulo[1];				// id modulo versione
				$data_istanza 	= $arr_dettagli_modulo[2];				// data osservazione o data inserimento/creazione modulo
				$tipo_data 		= $arr_dettagli_modulo[3];				// il tipo della data su menzionata (ovvervazione o inserimento)
				$nuovo_valore  .= $arr_dettagli_modulo[4].";";          // nome del modulo + la sua data + andatura a capo per moduli RTF(comporra' il valore da inserire in tbl istanza_dettaglio)
			
				if($data_istanza == "tutti")			$where_data_osser = "";
				else {
						if($tipo_data = "dataoss")		$where_data_osser = " AND data_osservazione = CAST('$data_istanza' AS DATE)";
					elseif($tipo_data = "datains")		$where_data_osser = " AND datains = CAST('$data_istanza' AS DATE)";
				}
			
				$query_oscura_modulo_fse = "UPDATE istanze_testata SET offuscamento_fse = 'o' WHERE id_cartella = $id_c AND id_modulo_versione = $id_mod_vers $where_data_osser";
				//echo $query_oscura_modulo_fse;
				$rs_query_oscura_modulo_fse = mssql_query($query_oscura_modulo_fse, $conn);
			}
			$valore = $nuovo_valore;
		}
		// FINE - MODULI PER OSCURAMENTO DA FSE (Fascioclo Sanitario Elettronico)
		
		if (is_array($valore)){
			$val="";
			foreach ($valore as $key => $value) {
				//echo "Hai selezionato la checkbox: $key con valore: $value<br />";
				$val.=$value.";";
			}					
			$valore=substr($val,0,strlen($val)-1);
		}
		$valore=pulisci($valore);
		
		if($idmodulo == 205 && $idmoduloversione >= 6143 && !empty($valore)) {
			if (strpos($row_c['segnalibro'],'terap_farm_farmaco_') !== false) {
				$idx = explode('_', $row_c['segnalibro']);
				$idx = $idx[count($idx)-1];
				
				$lista_farmaci[$idx]['nome'] = trim($valore);
			} elseif (strpos($row_c['segnalibro'],'dos_') !== false) {
				$idx = explode('_', $row_c['segnalibro']);
				$idx = $idx[count($idx)-1];
				
				$valore = trim($valore);
				$valore = str_replace(',','.', $valore);
				
				$lista_farmaci[$idx]['dosaggio'] = $valore;
			} elseif (strpos($row_c['segnalibro'],'tip_somm_') !== false) {
				$idx = explode('_', $row_c['segnalibro']);
				$idx = $idx[count($idx)-1];
				
				$lista_farmaci[$idx]['tip_somm'] = trim($valore);
			}
		}

		if($idmodulo == 206 && $idmoduloversione >= 6146) {
			if(!empty($valore) && trim($valore) != "") {
				//Costruisco un array che continue tutti i farmaci inseriti con le rispettive quantit� da scaricare.
				// Per non sbagliare ad accoppiarli mi baso sul numero dopo il segnalibro che identifica la riga
				if(strpos($row_c['segnalibro'],'farmaco_') !== false) {
					$idx = explode('_', $row_c['segnalibro']);
					$idx = $idx[count($idx)-1];
					$valore = trim($valore);
					 
					$lista_farmaci[$idx]['id'] = $valore;
					$lista_farmaci[$idx]['id_campo'] = $idcampo;

					//verifico se il farmaco inserito nella somministrazione � un farmaco della struttura.
					// Se lo � allora rimuovo l'associazione col paziente dalla tabella specifica, questo
					// perch� i farmaci della struttura non devono rimanere assegnati al paziente. Vengono 
					// somministrati solo in caso di emergenza o nel caso in cui il paziente non li abbia 
					// con se nell'immediato
					$_query = "SELECT 1
								FROM farmaci 
								WHERE struttura = 1 AND deleted = 0 AND id = " . $valore;
					$_rs = mssql_query($_query, $conn);
					$_row = mssql_fetch_assoc($_rs);

					if(!empty($_row)) {
							$farmaci_struttura_da_rimuovere[] = $valore;
					} else {
						$_query = "SELECT id
									FROM farmaci_paziente_pazienti
									WHERE id_paziente_destinatario = $idpaziente AND id_farmaco = $valore";
						$_rs = mssql_query($_query, $conn);
						$_row = mssql_fetch_assoc($_rs);
						
						if(!empty($_row)) {
							$farmaci_paz_paz_da_rimuovere[] = $_row['id'];
						}
					}
				} elseif(strpos($row_c['segnalibro'],'qnt_') !== false) {
					$idx = explode('_', $row_c['segnalibro']);
					$idx = $idx[count($idx)-1];
					
					$valore = trim($valore);
					$valore = str_replace(',','.', $valore);
					
					$lista_farmaci[$idx]['qnt'] = $valore;
				}
			}
			
			if(strpos($row_c['segnalibro'],'somm_farm_dt_') !== false
					|| strpos($row_c['segnalibro'],'dt_') !== false) {
				$idx = explode('_', $row_c['segnalibro']);
				$idx = $idx[count($idx)-1];

				if((empty($valore) || trim($valore) == '') && (isset($lista_farmaci[$idx]['id']) || !empty($lista_farmaci[$idx]['id']))) {
					$valore = date('d/m/Y');
				}
			}
		}
		
		if($idmodulo == 269 && $firmato_digitalmente == 's') {
			if(!empty($allegato) && empty($allegato2) && $row_c['segnalibro'] == 'sigla_op_seg_vit_15_min') {
				$op_emotrasfusioni_2 = trim($valore);	
			} elseif(!empty($allegato2) && empty($allegato3) && $row_c['segnalibro'] == 'sigla_op_seg_vit_term_trasf') {
				$op_emotrasfusioni_3 = trim($valore);	
			} elseif(!empty($allegato3) && empty($allegato4) && $row_c['segnalibro'] == 'sigla_op_seg_vit_60_min') {
				$op_emotrasfusioni_4 = trim($valore);
			}
		}	
		
		if($tipo!=7) {			
			/*update del dettaglio campo modificato*/
			$query="select idcampo, id_istanza_dettaglio from istanze_dettaglio where  id_istanza_testata=$id_istanza and idcampo=$idcampo";
			$rs2 = mssql_query($query, $conn);
			$num_row=mssql_num_rows($rs2);
			if($num_row==0)
				$query="insert into istanze_dettaglio (id_istanza_testata,idcampo,valore) values ($id_istanza,$idcampo,'$valore')";
			else
				$query="update istanze_dettaglio set valore='$valore' where idcampo=$idcampo and id_istanza_testata=$id_istanza";
			$rs2 = mssql_query($query, $conn);
			//echo($query);
			/*fine update*/
			
			if(!$rs2)
			{
				echo("no");
				exit();
				die();
			}
			
			if($tipo==6) {						
				/*aggiornamento istanze_testata con data osservazione*/
				$query="update istanze_testata set data_osservazione='$valore' where id_istanza_testata=$id_istanza";
				$rs2 = mssql_query($query, $conn);
				/*fine aggiornamento*/
		
				if(!$rs2) {
					echo("no-8667");
					exit();
					die();
				}
			}
		}
		
		if(isset($_FILES[$idcampo])){
			$data=date(dmyHis);
			$nome_file=$_FILES[$idcampo]['name'];
			$nome_file=str_replace(" ","_",$nome_file);
			$type = $_FILES[$idcampo]['type'];
			$file = $idpaziente."_".$data."_".$nome_file;
			$valore=$file;
						
			//******************				
			if ($_FILES[$idcampo]['name']!="") {	
				$upload_dir = ALLEGATI_UTENTI;		// The directory for the images to be saved in
				$upload_path = $upload_dir."/";				// The path to where the image will be saved			
				$large_image_location = $upload_path.$file;
				$userfile_tmp = $_FILES[$idcampo]['tmp_name'];
				move_uploaded_file($userfile_tmp, $large_image_location);
									 
				/*update del dettaglio campo modificato*/
				$query="update istanze_dettaglio set valore='$valore' where idcampo=$idcampo and id_istanza_testata=$id_istanza";
				$rs2 = mssql_query($query, $conn);
				/*fine update*/ 
			}
		}			
	}
	mssql_free_result($rs1);
	
	if($idmodulo == 205 && $idmoduloversione >= 6143) {
		foreach($lista_farmaci as $farmaco) {
			if(!isset($farmaco['nome']) || empty($farmaco['nome']) || trim($farmaco['nome']) == '') {
				continue;
			}
			
			$dosaggio_sql = '';
			$dosaggio = 'NULL';
			
			if(!empty($farmaco['dosaggio']) && trim($farmaco['dosaggio']) != '') {
				$dosaggio_sql = " AND dosaggio = ".$farmaco['dosaggio'];
				$dosaggio = $farmaco['dosaggio'];
			}
			
			$tip_somm_sql = '';
			$tip_somm = 'NULL';
			
			if(!empty($farmaco['tip_somm'])) {
				$tip_somm_sql = " AND tipologia_somministrazione = ".$farmaco['tip_somm'];
				$tip_somm = $farmaco['tip_somm'];
			}
			
			$_query = "SELECT id
						FROM farmaci 
						WHERE struttura = 0 AND deleted = 0 and id_paziente = '$idpaziente'
							AND LOWER(nome  Collate SQL_Latin1_General_CP1253_CI_AI) = LOWER(TRIM('".$farmaco['nome']."') Collate SQL_Latin1_General_CP1253_CI_AI)" .
							$dosaggio_sql .
							$tip_somm_sql;
			$_rs = mssql_query($_query, $conn);
			
			if(mssql_num_rows($_rs) == 0) {
				$_query = "INSERT INTO farmaci (nome, lotto, dosaggio, quantita_caricata, quantita_attuale, 
												giacenza_minima, tipologia_somministrazione, stato_farmaco, id_paziente, ope_ins, 
												status)
							VALUES ('".trim($farmaco['nome'])."', '', $dosaggio, 0, 0, 
									0, $tip_somm, 2, '$idpaziente', $opeins, 
									1)";
				$_rs = mssql_query($_query, $conn);
			}
		}
	}
	
	if($idmodulo == 206 && $idmoduloversione >= 6146) {
		$_lista_farmaci = array();

		foreach($lista_farmaci as $farmaco) {
			$_lista_farmaci[$farmaco['id_campo']] = $farmaco;
		}
		
		$lista_farmaci = $_lista_farmaci;
		
		foreach($lista_farmaci as $farmaco) {
			$id_farmaco = $farmaco['id'];
			$quantita = $farmaco['qnt'];
			$id_campo = $farmaco['id_campo'];
			$query_log = "";
			$query_farmaci = "";
			$campo_gia_presente = array_key_exists($id_campo, $lista_farmaci_presenti_db);
			
			$_query = "SELECT quantita_attuale 
						FROM farmaci 
						WHERE id = $id_farmaco";
			$_rs = mssql_query($_query, $conn);
			$_row = mssql_fetch_assoc($_rs);
			mssql_free_result($_rs);
			$quantita_attuale = $_row['quantita_attuale'];
			
			// se ho gi� valorizzato il campo nell'istanza
			if($campo_gia_presente) {
				// se ho mantenuto lo stesso farmaco
				if($lista_farmaci_presenti_db[$id_campo]['farmaco'] == $id_farmaco) {
					// se ho mantenuto lo stesso farmaco e non ho modificato la quantita 
					if($lista_farmaci_presenti_db[$id_campo]['qnt'] == $quantita) {
						$nuova_quantita_attuale = $quantita_attuale;
						$quantita = 0;
					} 
					// Se ho mantenuto lo stesso farmaco e ho modificato la quantita, aggiungo la quantita precedente
					// e sottrago quella nuova. Questo perch� do per scontato che se si vuole impostare uno scarico
					// aggiuntivo o si aggiunge un riga o si crea un nuovo modulo.
					else {
						$nuova_quantita_attuale = $quantita_attuale + $lista_farmaci_presenti_db[$id_campo]['qnt'] - $quantita;
					}
					
					// se esaurisco il farmaco imposto lo stato ad esaurito altrimenti a presente
					$_sql_status = $nuova_quantita_attuale > 0 ? ', stato_farmaco = 2' : ', stato_farmaco = 3';
										
					$query_farmaci = "UPDATE farmaci 
										SET quantita_attuale = $nuova_quantita_attuale $_sql_status
										WHERE id = $id_farmaco";
					mssql_query($query_farmaci, $conn);
					
					$query_log = "INSERT INTO farmaci_log (id_modulo_versione, progressivo_modulo, id_farmaco, id_paziente, quantita_precedente,
													quantita_scaricata, quantita_attuale, ope_ins, ip_ins)
									VALUES ($idmoduloversione, $idinserimento, $id_farmaco, $idpaziente, $quantita_attuale,
											$quantita, $nuova_quantita_attuale, $opeins, '$ipins')";
					mssql_query($query_log, $conn);
				}
				// Se ho sostituito un farmaco rispetto la volta precedente: ripristino la quantita sul farmaco
				// precedente e poi vado a scalare la quantita al farmaco appena inserito
				else {
					// Gestisco il farmaco precedente
					$vecchio_farmaco = $lista_farmaci_presenti_db[$id_campo]['farmaco'];
					$vecchio_farmaco_qnt_scaricata = $lista_farmaci_presenti_db[$id_campo]['qnt'];
					
					$_query = "SELECT quantita_attuale 
								FROM farmaci 
								WHERE id = $vecchio_farmaco";
					$_rs = mssql_query($_query, $conn);
					$_row = mssql_fetch_assoc($_rs);
					mssql_free_result($_rs);
			
					$vecchio_farmaco_qnt_prec = $_row['quantita_attuale'];			
					$vecchio_farmaco_qnt_attuale = $vecchio_farmaco_qnt_prec  + $vecchio_farmaco_qnt_scaricata;
					
					// se esaurisco il farmaco imposto lo stato ad esaurito altrimenti lo imposto come presente
					$_sql_status = $vecchio_farmaco_qnt_attuale > 0 ? ', stato_farmaco = 2' : ', stato_farmaco = 3';
					
					$_query = "UPDATE farmaci 
								SET quantita_attuale = $vecchio_farmaco_qnt_attuale $_sql_status
								WHERE id = $vecchio_farmaco";
					mssql_query($_query, $conn);
					
					$query_log = "INSERT INTO farmaci_log (id_modulo_versione, progressivo_modulo, id_farmaco, id_paziente, quantita_precedente,
														quantita_scaricata, quantita_attuale, ope_ins, ip_ins, note)
										VALUES ($idmoduloversione, $idinserimento, $vecchio_farmaco, $idpaziente, $vecchio_farmaco_qnt_prec,
												-$vecchio_farmaco_qnt_scaricata, $vecchio_farmaco_qnt_attuale, $opeins, '$ipins', 'Questo farmaco � stato rimosso dalla somministrazione, � stata quindi ripristinata la quantit� precedentemente scalata')";		
					mssql_query($query_log, $conn);
					
										
					// Gestisco il farmaco nuovo
					$nuova_quantita_attuale = $quantita_attuale - $quantita;
					
					// se esaurisco il farmaco imposto lo stato ad esaurito
					$_sql_status = $nuova_quantita_attuale > 0 ? '' : ', stato_farmaco = 3';
					
					$_query = "UPDATE farmaci 
								SET quantita_attuale = $nuova_quantita_attuale $_sql_status
								WHERE id = $id_farmaco";
					mssql_query($_query, $conn);
					
					$_query = "INSERT INTO farmaci_log (id_modulo_versione, progressivo_modulo, id_farmaco, id_paziente, quantita_precedente,
													quantita_scaricata, quantita_attuale, ope_ins, ip_ins)
								VALUES ($idmoduloversione, $idinserimento, $id_farmaco, $idpaziente, $quantita_attuale,
										$quantita, $nuova_quantita_attuale, $opeins, '$ipins')";	
					mssql_query($_query, $conn);
				}
			}
			// se non ancora valorizzato questo id_campo per l'istanza
			else {
				$nuova_quantita_attuale = $quantita_attuale - $quantita;
				
				// se esaurisco il farmaco imposto lo stato ad esaurito
				$_sql_status = $nuova_quantita_attuale > 0 ? '' : ', stato_farmaco = 3';
					
				$_query = "UPDATE farmaci 
							SET quantita_attuale = $nuova_quantita_attuale $_sql_status
							WHERE id = $id_farmaco";
				mssql_query($_query, $conn);
				
				$_query = "INSERT INTO farmaci_log (id_modulo_versione, progressivo_modulo, id_farmaco, id_paziente, quantita_precedente,
												quantita_scaricata, quantita_attuale, ope_ins, ip_ins)
							VALUES ($idmoduloversione, $idinserimento, $id_farmaco, $idpaziente, $quantita_attuale,
									$quantita, $nuova_quantita_attuale, $opeins, '$ipins')";				
				mssql_query($_query, $conn);
			}
		}
		
		$_query = "DELETE FROM farmaci_struttura_pazienti 
					WHERE id_farmaco in (" . implode(',', $farmaci_struttura_da_rimuovere) . ") AND id_paziente = $idpaziente";
		mssql_query($_query, $conn);
		
		$_query = "DELETE FROM farmaci_paziente_pazienti 
					WHERE id in (" . implode(',', $farmaci_paz_paz_da_rimuovere) . ")";
		mssql_query($_query, $conn);		
	}

	if($idmodulo == 269 && $firmato_digitalmente == 's') {		
		if(!empty($op_emotrasfusioni_2)) {
			$query_firma = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato2) 
							values ($id_istanza, $op_emotrasfusioni_2, 'a')";
			$rs_firma = mssql_query($query_firma, $conn);
		} elseif(!empty($op_emotrasfusioni_3)) {
			$query_firma = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato3) 
							values ($id_istanza, $op_emotrasfusioni_3, 'a')";
			$rs_firma = mssql_query($query_firma, $conn);
		} elseif(!empty($op_emotrasfusioni_4)) {
			$query_firma = "insert into istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato4) 
							values ($id_istanza, $op_emotrasfusioni_4, 'a')";
			$rs_firma = mssql_query($query_firma, $conn);
		}
	}

	if($idcartella==0)
		echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria_cartelle.php?do=addvisita");
	  //else echo ("ok;".$idpaziente.";6;re_pazienti_sanitaria_POST.php?do=visualizza_cartella&idcartella=".$idcartella."&idpaziente=".$idpaziente);
		
	  //else echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella=$idcartella&idpaziente=$idpaziente&idmodulo=$idmoduloversione&cartella=$cartella&impegnativa=$impegnativa&idinserimento=$idinserimento&firmadigitaledocumento=$firmadigitaledocumento&salvaeallega=$salvaeallega");
		else echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella=$idcartella&idpaziente=$idpaziente&idmodulo=$idmoduloversione&cartella=$cartella&impegnativa=$idimpegnativa&idinserimento=$idinserimento&salvaeallega=$salvaeallega");
		
	exit();
}


function update($id)
{
$tablename="impegnative";
$conn = db_connect();



foreach ($_POST as $k => $v) {
   if (($k!="action") and ($k!="op") and ($k!="id") and ($k!="nomandatory") )
   {
     $campi=$k.'=';
	 $v=str_replace("'","''",$v);
	 $valori="'".addslashes($v)."'";
	 $upd.=$campi.$valori.",";
   }
  } 
	$lc=strlen($upd)-1;
	$upd=substr($upd, 0, $lc); // returns "d"




$query="update impegnative set ".$upd." where idimpegnativa=$id";
$result = mssql_query($query, $conn);

if(!$result) 
 echo ("no");
else 
 echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria_cartelle.php?do=addvisita"); 
exit();






}





if((isset($_SESSION['UTENTE'])) and ($_SESSION['UTENTE']->get_tipoaccesso()!=1)){
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi

	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			case "create_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_cartella($_POST['id']);					
			break;
			
			case "create_chiusura_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_chiusura_cartella();					
			break;
			
			case "create_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_modulo();					
			break;
			
			case "create_visita":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_visita();					
			break;
			
			case "create_test_clinico":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_test_clinico();					
			break;
			
			case "update_test_clinico":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else update_test_clinico();					
			break;
			
			case "create_test_clinico_ramificato":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_test_clinico_ramificato();					
			break;
			
			case "create_pianificazione":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_pianificazione();					
			break;
			
			case "add_pianificazione":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add_pianificazione();					
			break;
			
			
			case "visualizza_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_cartella();	
							
			break;
			

			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_POST['id']);
			break;
			
			case "update_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update_modulo();
			break;

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
			
			case "add_files":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else add_files($_POST['id']);
			break;
			
			
			case "add_allegati_istanza":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else add_allegati_istanza($_POST['idcartella'],$_POST['idpaziente'],$_POST['idmoduloversione'],$_POST['idinserimento'],$_POST['imp']);
			break;	


			case "add_note_istanza":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else add_note_istanza($_POST['idcartella'],$_POST['idpaziente'],$_POST['idmoduloversione'],$_POST['idinserimento'],$_POST['note'],$_POST['imp']);
			break;				
			
			
			case "del_files":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_files($_POST['id_allegato'],$_POST['id_paziente']);
			break;
		}

		if($_REQUEST['action']=="del_files"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_files($_REQUEST['id_all'],$_REQUEST['paz']);
		}
		
		switch($do) {
			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
			break;
			
			case "visualizza_istanze_test_clinico":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_istanze_test_clinico_report();
			break;
			
			case "visualizza_istanze_test_clinico_ramificato":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_istanze_test_clinico_ramificato();
			break;
			
			case "aggiungi_pianificazione":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_pianificazione();
			break;
			
			
			
			case "aggiungi_pianificazione_test_clinici":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_pianificazione_test_clinici();
			break;
			
			case "visualizza_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_cartella();
			break;
			
			case "chiudi_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else chiudi_cartella();
			break;
			
			case "visualizza_istanze_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_istanze_modulo();
			break;
			
			case "anteprima_istanze_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else anteprima_istanze_modulo();
			break;
			
			case "anteprima_istanze_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else anteprima_istanze_cartella();
			break;
			
			case "anteprima_istanze_cartella_ins":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else anteprima_istanze_cartella_ins();
			break;
			
			case "stampa_istanze_cartella_ins":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else stampa_istanze_cartella_ins();
			break;
			
			case "aggiungi_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_modulo();
			break;
			
			case "aggiungi_visita":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_visita();
			break;
			
			case "aggiungi_test_clinico":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_test_clinico();
			break;
			
			case "aggiungi_test_clinico_ramificato":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_test_clinico_ramificato();
			break;
			
			case "edita_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else edita_modulo();
			break;
			
			case "converti_firma_istanza":				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else converti_firma_istanza();
			
			case "cancella_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else cancella_modulo();
			break;

			case "cancella_allegato":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else cancella_allegato();
			break;
			
			case "valida_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else valida_modulo();
			break;
			
			case "annulla_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else annulla_modulo();
			break;
			
			case "duplica_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else duplica_modulo();
			break;	
			
			case "visualizza_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_modulo();
			break;
			
			case "modifica_test_clinico":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else modifica_test_clinico();
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
				$idp=$_REQUEST['idpaziente'];
				echo ("ok;".$idp.";4;re_pazienti_sanitaria.php?do=");
				exit();
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




function stampa_istanze_cartella_ins(){

$idcartella=$_REQUEST['idcartella'];
$idpaziente=$_REQUEST['idpaziente'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idinserimento=$_REQUEST['idinserimento'];
$cartella=$_REQUEST['cartella'];

//prelevo solo i dati riferiti alla cartella eliminando la versione
$car = explode("/",$cartella);
$cartella = $car[0];

?>
	<html>
	<head>
	<link href="style/new_ui_print.css" rel="stylesheet" type="text/css" media="print" />
	<style>
	table {
		width: 100%;
	}
	</style>
	<title>remed - stampa</title>
	</head>
	<body topmargin=50>	
<?PHP

$conn = db_connect();
$sql = "SELECT Nome, Cognome  FROM utenti  where idUtente = $idpaziente";
$rs_sql = mssql_query($sql, $conn);
$row_utente = mssql_fetch_assoc($rs_sql);
?>
<div style="text-align: center">
	<img src="images/intestazione_stampa.jpg" />
</div>
<h1 style="text-align: center">Diario Clinico</h1><br/>
<table id="table1" class="tablesorter" cellspacing="1">
	<tr>
		<td><strong>Nome:</strong> <?PHP echo $row_utente['Nome']; ?></td>
		<td><strong>Cognome:</strong> <?PHP echo $row_utente['Cognome']; ?></td>
		<td><strong>C.C.:</strong> <?PHP echo $cartella; ?>
	</tr>
</table>
<?PHP


$query="select nome_modulo,data_osservazione,id_cartella from re_istanze_moduli WHERE id_cartella = $idcartella and descrizione <> 'amministrativo'
UNION
SELECT CAST(descrizione as varchar(255)), data_produzione, idcartella  from re_allegati_cartelle where idcartella = $idcartella and stampa_allegato = 1 and cancella = 'n'
order by data_osservazione DESC";
//echo($query);
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
?>
<br><br/><br/>
<table id="table" class="tablesorter" cellspacing="1"> 
	 
<tbody> 
<?

	while($row = mssql_fetch_assoc($rs))   
	{
		$nome_modulo=utf8_decode($row['nome_modulo']);
		$data_osservazione=date('d/m/Y', strtotime($row['data_osservazione']));
	?>
	<tr> 
	 <td><?=$nome_modulo?></td>
	 <td style="text-align: right;"><?=$data_osservazione?></td> 	
	</tr>
	<?
	}
	mssql_free_result($rs);
?>
</tbody>
</table>


<script>
	window.print();
</script>
</body>
</html>
<?
}







?>