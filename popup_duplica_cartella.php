<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
*/
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');

if(isset($_SESSION['UTENTE']))
{
	$idcartella = !empty($_GET['idcartella']) ? $_GET['idcartella'] : $_POST['idcartella'];
	$idpaziente = !empty($_GET['idpaziente']) ? $_GET['idpaziente'] : $_POST['idpaziente'];
	$idregime = !empty($_GET['idregime']) ? $_GET['idregime'] : $_POST['idregime_cart_der'];
	$conn = db_connect();
	$error = '';
	
	if(isset($_POST['do'])) {
		$idregime_cart_der = $_POST['idregime_cart_der'];
		$idregime_cart_new = $_POST['idregime_cart_new'];

		// RECUPERO I MODULI COMUNI TRA LA CARTELLA DA CLONARE E IL REGIME SELEZIONATO
		$q_mod_com_cart = "SELECT idmodulo
							FROM re_moduli_regimi_replica as mod_new
							INNER JOIN (
								SELECT id_modulo_padre
								FROM re_cartelle_pianificazione
								WHERE id_cartella = $idcartella
								GROUP BY id_modulo_padre
							) AS mod_pianif ON mod_pianif.id_modulo_padre = mod_new.idmodulo
							WHERE idregime = $idregime_cart_new AND ((replica=0) OR (replica=1)) 
							ORDER BY id ASC";
		$rs_mod_com_cart = mssql_query($q_mod_com_cart, $conn);		

		$moduli_comuni = array();
		while ($row = mssql_fetch_assoc($rs_mod_com_cart)){
			$moduli_comuni[] = $row['idmodulo'];
		}
		mssql_free_result($rs_mod_com_cart);
		
		if(empty($moduli_comuni)) {
			$error = 'ATTENZIONE: duplicazione bloccata, non sono presenti moduli in comune tra i due regimi';
		} else {
			// RECUPERO L'ULTIMA VERSIONE DELLA CARTELLA PER IL PAZIENTE
			$q_max_cart = "SELECT max(versione) as numero_versione, codice_cartella
							FROM utenti_cartelle 
							WHERE idutente = $idpaziente AND versione IS NOT NULL
							GROUP BY codice_cartella";
			$rs_max_cart = mssql_query($q_max_cart, $conn);
			$row = mssql_fetch_assoc($rs_max_cart);
			
			$versione =	$row['numero_versione'] + 1;
			$codice_cartella = $row['codice_cartella'];
			
			mssql_free_result($rs_max_cart);

			$datains = date('d/m/Y');
			$orains = date('H:i');
			$ipins = $_SERVER['REMOTE_ADDR'];
			$opeins = $_SESSION['UTENTE']->get_userid();
			
			// RECUPERO LA NORMATIVA DEL REGIME SELEZIOANTO DALLA COMBO
			$_query = "SELECT * 
						FROM regime 
						WHERE idregime = $idregime_cart_new";
			$rs_regime = mssql_query($_query, $conn);		
			$row_regime = mssql_fetch_assoc($rs_regime);

			$idnormativa = $row_regime['idnormativa'];
			
			mssql_free_result($rs_regime);
							
			// CREO LA NUOVA CARTELLA
			$q_new_cart = "INSERT INTO utenti_cartelle (idutente, codice_cartella, versione, data_creazione, idmedico_creazione, 
														idregime, idnormativa, datains, orains, opeins, 
														ipins) 
							VALUES ($idpaziente, '$codice_cartella', $versione, '$datains', $opeins,
									$idregime_cart_new, $idnormativa, '$datains', '$orains', $opeins, 
									'$ipins')";
			
			$rs_new_cart = mssql_query($q_new_cart, $conn);
			
			// SE LA CARTELLA E' STATA GENERATA PASSO ALLE PIANIFICAZIONI
			if($rs_new_cart) {
				$_query = "SELECT MAX(id) as last_cartella 
							FROM utenti_cartelle 
							WHERE idutente = $idpaziente";
				$_rs = mssql_query($_query, $conn);
				$_row = mssql_fetch_assoc($_rs);
				
				$id_new_cartella = $_row['last_cartella'];
				
				mssql_free_result($_rs);
				
				// GENERO UNA NUOVA TESTATA PIANIFICAZIONE LEGATA ALLA CARTELLA APPENA CREATA
				$_query = "INSERT INTO cartelle_pianificazione_testata (id_cartella, datains, orains, opeins, ipins) 
							VALUES ($id_new_cartella, '$datains', '$orains', $opeins, '$ipins')";
				$rs_new_pian = mssql_query($_query, $conn);
				
				// SE LA TESTASTA PIANIFICAZIONE E' STATA GENERATA INIZIO A LEGARE I MODULI ALLA PIANIFICAZIONE
				if($rs_new_cart) {
					// RECUPERO L'ULTIMA TESTATA INSERITA E POI CERCO TUTTI I MODULI IN COMUNE E PIANIFICATI TRA I REGIMI DELLE DUE CARTELLE
					$_query = "SELECT MAX(id_pianificazione_testata) as id_pian_testata 
								FROM cartelle_pianificazione_testata 
								WHERE id_cartella = $id_new_cartella AND opeins = $opeins";
					$_rs = mssql_query($_query, $conn);
					$_row = mssql_fetch_assoc($_rs);
					
					$id_pian_testata_new = $_row['id_pian_testata'];
					
					mssql_free_result($_rs);

					// RECUPERO L'id_pianificazione_testata DA CUI DOVER COPIARE LE PIANIFICAZIONI
					$_query = "SELECT MAX(id_pianificazione_testata) as id_pian_testata 
								FROM cartelle_pianificazione_testata 
								WHERE id_cartella = $idcartella";
					$_rs = mssql_query($_query, $conn);
					$_row = mssql_fetch_assoc($_rs);
					
					$id_pianificazione_testata = $_row['id_pian_testata'];

					mssql_free_result($_rs);
					
					// RECUPERO LE PIANIFICAZIONE E ALLO STESSO MOMENTO LE INSERISCO CON IL NUOVO ID TESTATA
					$q_cart_pian = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata, id_impegnativa, id_modulo_padre, id_modulo_versione, obbligatorio, 
																				invio_fse, data_fissa, trattamenti, scadenza, id_operatore, 
																				replica, data_iniz_scad_cicl)
									SELECT $id_pian_testata_new, id_impegnativa, id_modulo_padre, id_modulo_versione, obbligatorio, 
											invio_fse, data_fissa, trattamenti, scadenza, $opeins, 
											replica, data_iniz_scad_cicl
									FROM cartelle_pianificazione
									WHERE id_cartella_pianificazione_testata = $id_pianificazione_testata
										AND id_impegnativa IS NULL 
										AND id_modulo_padre IN (" . implode(',', $moduli_comuni) . ")";
					$rs_cart_pian = mssql_query($q_cart_pian, $conn);
					
					// RECUPERO LE SCADENZE E ALLO STESSO MOMENTO LE INSERISCO CON IL NUOVO ID TESTATA
					if($rs_cart_pian) {			
						$q_new_scad_cicl = "INSERT INTO cartelle_pianificazione_date_scad_cicl (id_cartella_pianificazione_testata, id_cartella, id_paziente, id_regime, id_normativa, 
																								id_modulo_padre, id_modulo_versione, scadenza, compilato, id_inserimento_modulo, 
																								opeins, datains, orains, ipins)
											SELECT $id_pian_testata_new, $id_new_cartella, id_paziente, $idregime_cart_new, $idnormativa, 
													id_modulo_padre, id_modulo_versione, scadenza, compilato, id_inserimento_modulo, 
													$opeins, '$datains', '$orains', '$ipins'
											FROM cartelle_pianificazione_date_scad_cicl
											WHERE id_cartella_pianificazione_testata = $id_pianificazione_testata";
						$rs_new_scad_cicl = mssql_query($q_new_scad_cicl, $conn);
						
						// RECUPERO LE ISTANZE DEI MODULI DA CLONARE 
						$q_ist_mod_comuni = "SELECT DISTINCT ist_test.*, ist_test_firm.tipo_firma
												FROM istanze_testata ist_test
												LEFT JOIN istanze_testata_firme ist_test_firm ON ist_test_firm.id_istanza_testata = ist_test.id_istanza_testata
												INNER JOIN (
													SELECT id_cartella, id_modulo_padre, MAX(id_modulo_versione) AS id_modulo_versione, MAX(id_inserimento) as id_inserimento
													FROM istanze_testata
													WHERE id_cartella = $idcartella
														AND id_modulo_padre IN (" . implode(',', $moduli_comuni) . ")
													GROUP BY id_cartella, id_modulo_padre
												) as ist_mod_com ON ist_mod_com.id_cartella = ist_test.id_cartella 
																	AND ist_mod_com.id_modulo_padre = ist_test.id_modulo_padre 
																	AND ist_mod_com.id_modulo_versione = ist_test.id_modulo_versione
																	AND ist_mod_com.id_inserimento = ist_test.id_inserimento
												WHERE ist_test.id_modulo_padre NOT IN (202,203,204,205,206,208,230,254)";
						$rs_ist_mod_comuni = mssql_query($q_ist_mod_comuni, $conn);

						// CLONO TUTTE LE ISTANZE
						while($row_ist_mod_comuni = mssql_fetch_assoc($rs_ist_mod_comuni)) {
							$id_istanza_testata = $row_ist_mod_comuni['id_istanza_testata'];
							$id_modulo_padre = $row_ist_mod_comuni['id_modulo_padre'];
							$id_modulo_versione = $row_ist_mod_comuni['id_modulo_versione'];
							$id_impegnativa = !empty($row_ist_mod_comuni['id_impegnativa']) ? $row_ist_mod_comuni['id_impegnativa'] : 'NULL';
							$id_paziente = $row_ist_mod_comuni['id_paziente'];
							$updcount = !empty($row_ist_mod_comuni['updcount']) ? $row_ist_mod_comuni['updcount'] : 'NULL';
							
							$note = !empty($row_ist_mod_comuni['note']) ? $row_ist_mod_comuni['note'] : NULL;
							if(!empty($note) && '(Modulo duplicato)' == $note) {
								$note = NULL;
							}
							
							$offuscamento_fse = $row_ist_mod_comuni['offuscamento_fse'];
							$firmato_digitalmente = $row_ist_mod_comuni['firmato_digitalmente'];
							$firmato_fea = $row_ist_mod_comuni['firmato_fea'];							
							$tipo_firma = $row_ist_mod_comuni['tipo_firma'];
							
							$q_ins_ist_mod = "INSERT INTO istanze_testata (id_cartella, id_modulo_padre, id_modulo_versione, id_inserimento, id_impegnativa,
															id_paziente, data_osservazione, updcount, datains, orains,
															opeins, ipins, note, offuscamento_fse, validazione, 
															firmato_digitalmente, firmato_fea, duplicata)
											 VALUES ($id_new_cartella, $id_modulo_padre, $id_modulo_versione, 1, $id_impegnativa,
													$id_paziente, '$datains', $updcount, '$datains', '$orains', 
													$opeins, '$ipins', '$note', '$offuscamento_fse', 'a',
													'$firmato_digitalmente', '$firmato_fea', 1)";
							$rs_ins_ist_mod = mssql_query($q_ins_ist_mod, $conn);
							
							if($rs_ins_ist_mod) {
								// RECUPERO L'ULTIMA ISTANZA INSERITA
								$_query = "SELECT id_istanza_testata
											FROM istanze_testata 
											WHERE id_cartella = $id_new_cartella AND id_modulo_padre = $id_modulo_padre AND id_modulo_versione = $id_modulo_versione AND id_paziente = $id_paziente";
								$_rs = mssql_query($_query, $conn);
								$_row = mssql_fetch_assoc($_rs);
								
								$last_id_istanza_testata = $_row['id_istanza_testata'];
								
								mssql_free_result($_rs);
								
								// E SE HO UN TIPO FIRMA VALORIZZATO INSERISCO NELLA TABELLA istanze_testata_firme UNA NUOVA RIGA CON LO STATO 'IN ATTESA DI FIRMA'
								if(!empty($tipo_firma)) {
									$q_ins_ist_test_fir = "INSERT INTO istanze_testata_firme (id_istanza_testata, id_operatore, stato_firma_allegato, id_paziente, tipo_firma)
															VALUES ($last_id_istanza_testata, $opeins, 'a', $id_paziente, '$tipo_firma')";
									$_rs = mssql_query($q_ins_ist_test_fir, $conn);
								}
								
								// VADO A RECUPERARE TUTTI I VALORI DELL'ISTANZA PRECEDENTE E LI INSERISCO NEL DETTAGLIO DELLA NUOVA ISTANZA
								$q_ins_ist_det_mod = "INSERT INTO istanze_dettaglio (id_istanza_testata, idcampo, valore)
														SELECT $last_id_istanza_testata, idcampo, valore
														FROM istanze_dettaglio
														WHERE id_istanza_testata = $id_istanza_testata";
								$_rs = mssql_query($q_ins_ist_det_mod, $conn);

								if($_rs) {
									// RESETTO TUTTI I CAMPI DI TIPO DATA, DATA_OSSERVAZIONE E FIRMA_OPERATORE IN QUANTO POTREBBERO DIFFERIRE RISPETTO IL MODULO DA CUI LI RECUPERO
									$q_agg_ist_det_mod = "UPDATE istanze_dettaglio
															SET valore = ''
															WHERE id_istanza_testata = $last_id_istanza_testata AND idcampo IN (
																SELECT idcampo 
																FROM campi 
																WHERE idmoduloversione = $id_modulo_versione AND tipo IN (3,6,8)
															)";
									$_rs = mssql_query($q_agg_ist_det_mod, $conn);
								}
							} else {
								$error = "ERRORE: errore durante la clonazione dell'istanze con ID $id_istanza_testata";
							}
						}
					} else {
						$error = "ERRORE: errore durante la clonazione delle pianificazioni";
					}
				}
			} else {
				$error = 'ERRORE: errore durante la creazione della cartella';
			}
		}

		if(empty($error)) { ?>
			<script>
				self.opener.show_list('<?=$idpaziente?>');
				window.close();
			</script>
<?		}
	}
	
	$query_imp = "SELECT idregime, idnormativa, regime, normativa, idutente 
					FROM re_pazienti_impegnative 
					GROUP BY idregime, idnormativa, regime, normativa, idutente 
					HAVING idutente = $idpaziente
					ORDER BY idregime ASC";
	$rs_imp = mssql_query($query_imp, $conn);

	$tot_imp = mssql_num_rows($rs_imp);
	$imp_list = array();
	$regime_der_cart = '';
	
	if($tot_imp > 0) {
		while ($row_imp = mssql_fetch_assoc($rs_imp)){
			if($idregime == $row_imp['idregime']) {
				$regime_der_cart = $row_imp['normativa'] . ' - ' . $row_imp['regime'];
			}
			$imp_list[$row_imp['idregime']] = $row_imp['normativa'] . ' - ' . $row_imp['regime'];
		}
	} else {
		$error = "ERRORE: Non ci sono impegnative attive associate a questo paziente";
	}

} else {
	die("La tua sessione su ReMed &egrave; scaduta.<br>Chiudi questa finestra ed effettua nuovamente il login");
}
?>

<html>
	<head>
		<link href="style/flexigrid.css" rel="stylesheet" type="text/css" />
		<link href="style/new_ui.css" rel="stylesheet" type="text/css" />
		<link href="style/new_ui_print.css" rel="stylesheet" type="text/css" media="print" />
		<script type="text/javascript" src="script/jquery-1.2.6.min.js"></script>
	</head>
	<body style="background: #ffffff;">
		<div style="padding: 10px;">
			<div class="titoloalternativo">
				<h1>Duplica cartella</h1>
			</div>

			<div class="titolo_pag">
				<div class="comandi" style="float:right;">
					<div class="elimina"><a href="#" onclick="window.close();">chiudi</a></div>
				</div>	
			</div>
			<div id="wrap-content">
				<div class="padding10">   
					<form  method="post" name="form0" action="popup_duplica_cartella.php" >
						<input type="hidden" name="idcartella" value="<?= $idcartella ?>" >
						<input type="hidden" name="idpaziente" value="<?= $idpaziente ?>" >
						<input type="hidden" name="idregime_cart_der" value="<?= $idregime ?>" >
						<input type="hidden" value="duplica" name="do"/>
						
						<div class="blocco_centralcat">
							<div class="riga">
								<br>
<?							if(!empty($error)) {	?>
								<div class="rigo_mask" style="width: auto;">
									<div class="testo_mask"><?=$error?></div>
								</div>
<?							}	?>								
								<br>
							</div>
							<input type="hidden" value="match" name="do"/>
							<div class="riga">
								<div class="rigo_mask" style="width: auto;">
									<div><b>Regime di derivazione:</b> <?=$regime_der_cart?></div><br>
									<div class="testo_mask">Seleziona il regime da applicare alla nuova cartella: </div>
									<div class="campo_mask mandatory">
										<select id="idregime_cart_new" name="idregime_cart_new" class="scrittura" style="width:auto;">
<?php									foreach($imp_list as $key=>$imp) {
											echo "<option value='$key'>$imp</option>";
											$regime_inf = 0;
											
											switch($key) {
												// Regime ex Art. 26 L. 833/78
												case 26:
												case 27:
												case 28:
												case 48:
												case 50:
													$regime_inf = 52;
												break;
												
												// Regime L.R. 08/2003
												case 36:
													$regime_inf = 59;
												break;
												
												// Regime RD1 Estensiva
												case 51:
													$regime_inf = 59;
												break;
												// Regime T.P. RD1 estensiva
												case 56:
													$regime_inf = 60;
												break;
												
												// Regime T.P. RD1 intensntiva
												case 55:
													$regime_inf = 62;
												break;
												// Regime T.P. RD1 intensivo
												case 63:
													$regime_inf = 64;
												break;		
												// Regime T.P. Suap
												case 57:
													$regime_inf = 65;
												break;
														
												// Regime SUAP
												case 54:
													$regime_inf = 61;
												break;

												default:
												break;
											}
											
											if($regime_inf > 0) {
												echo "<option value='$regime_inf'>$imp (INFERMIERISTICA)</option>";
											}
										} ?>										
										</select>
									</div>
								</div>
							</div>
							<div class="riga">
								<div class="rigo_mask" style="width: auto;">
									<div class="testo_mask"><b>N.B.:</b> Verrano duplicati solo i moduli in comune tra il regime della cartella che si vuole duplicare e quello selezionato.</div>
								</div>
							</div>
							<div class="riga">
								<br><br>
							</div>
							<div class="titolo_pag">
								<div class="comandi">
									<input type="submit" title="Conferma" value="conferma" class="button_salva"/>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>	
		</div>
	</body>
</html>