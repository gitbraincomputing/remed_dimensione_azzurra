<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
include_once('include/function_page.php');

//$idutente=$id;
function add()
{
	$idutente = $_REQUEST['id'];
	$codiceutente = $_REQUEST['codiceutente'];
	$idregime = 0;
	if (isset($_REQUEST['idregime'])) {
		$idregime = $_REQUEST['idregime'];
	} else {
		$idregime = get_regime_paziente($idutente);
	}
	$conn = db_connect();
	$responsabile = get_responsabile_regime($idregime);
	if ($responsabile == 0)
		$d_responsabile = "Direttore Sanitario";
	else
		$d_responsabile = "Direttore Tecnico";



	if ((($responsabile == 0) and (!controlla_ds())) or (($responsabile == 1) and (!controlla_dt()))) {
?>

		<div class="titoloalternativo">
			<h1>Nuova Cartella Clinica</h1>
		</div>
		<!--<div class="info">Impossibile creare una nuova cartella clinica.<br />L'apertura della cartella clinica è riservata esclusivamente al <?= $d_responsabile ?> in carico.</div>-->
		<div class="info">Impossibile creare una nuova cartella clinica.<br />
			L'apertura della cartella clinica è riservata esclusivamente ad un Gestore delle cartelle.<br />
			Verificare di avere inserito almeno un'impegnativa attiva.</div>


	<?
		exit();
	}

	$arr_medici[] = array();
	$query = "SELECT nome, uid from medici_operatori ORDER BY nome ASC";
	$rs1 = mssql_query($query, $conn);
	$i = 0;
	while ($row = mssql_fetch_assoc($rs1)) {
		$arr_medici[$i] = $row;
		$i++;
	}

	$stato_paziente = get_stato_paziente($idutente);

	if ($stato_paziente > 0) {
		$stato_paziente_descr = get_stato_paziente_descr($stato_paziente);
	}

	$cartella_attiva = get_cartella_attiva_paziente($idutente);

	if (!$cartella_attiva && $idutente <> 605) {
	?>
		<div class="titoloalternativo">
			<h1>Nuova Cartella Clinica</h1>
		</div>
		<div class="info">Impossibile creare una nuova cartella clinica per il paziente in quanto &egrave; gi&agrave; presente una cartella clinica attiva.<br />Per poter creare una nuova cartella clinica &egrave; necessario chiudere prima l'ultima cartella clinica.</div>
	<?
		exit();
	}

	?>
	<script type="text/javascript">
		/*$(document).ready(function() {
    // Initialise the table
    $("#elenco").tableDnD();
});*/

		inizializza();
	</script>
	<script type="text/javascript">
		var popupStatus = 0;

		//loading popup with jQuery magic!
		function loadPopup1(id) {
			//loads popup only if it is disabled
			if (popupStatus == 0) {
				$("#backgroundPopup1").css({
					"opacity": "0.7"
				});
				$("#popupdati2").load('carica_preview.php?&do=&id=' + id, function() {
					centerPopup();
					$("#backgroundPopup1").fadeIn("slow");
					$("#popupdati2").fadeIn("slow");
				});


				popupStatus = 1;
			}
		}

		//disabling popup with jQuery magic!
		function disablePopup() {
			//disables popup only if it is enabled
			if (popupStatus == 1) {
				$("#backgroundPopup1").fadeOut("slow");
				$("#popupdati2").fadeOut("slow", function() {
					$("#popupdati2").html("");
				});

				popupStatus = 0;
			}
		}

		//centering popup
		function centerPopup() {
			//request data for centering
			var windowWidth = document.documentElement.clientWidth;
			var windowHeight = document.documentElement.clientHeight;
			var popupHeight = $("#popupdati2").height();
			var popupWidth = $("#popupdati2").width();
			//centering
			$("#popupdati2").css({
				"position": "fixed",
				"top": windowHeight / 2 - popupHeight / 2,
				"left": windowWidth / 2 - popupWidth / 2
			});
			//only need force for IE6

			$("#backgroundPopup1").css({
				"height": windowHeight
			});

		}

		function prev(id) {
			//centering with css
			loadPopup1(id);

			//load popup
		}


		//CONTROLLING EVENTS IN jQuery
		$(document).ready(function() {

			//LOADING POPUP
			//Click the button event!
			$(".preview").click(function() {
				//centering with css
				//centerPopup();
				//load popup
				//loadPopup1();
			});

			//CLOSING POPUP
			//Click the x event!
			$(".popupdatiClose").click(function() {
				disablePopup();
			});
			//Click out event!
			$("#backgroundPopup1").click(function() {
				disablePopup();
			});
			//Press Escape event!
			$(document).keypress(function(e) {
				if (e.keyCode == 27 && popupStatus == 1) {
					disablePopup();
				}
			});

		});
	</script>

	<!-- propone la pianificazione se esiste un regime per l'utente -->
	<?


	if ($idregime > 0) {
		$data_odierna = date('d/m/Y');
	?>

		<div id="popupdati2"></div>
		<div id="backgroundPopup1"></div>
		<div id="cartella_clinica" class="pianifica_cartella">

			<div class="titoloalternativo">
				<h1>nuova cartella clinica</h1>
			</div>


			<div class="titolo_pag">
				<h1>dati cartella clinica</h1>
			</div>
			<div class="blocco_centralcat">

				<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
					<input type="hidden" name="action" value="create_cartella" />
					<input type="hidden" name="id" value="<?= $idutente ?>" />
					<input type="hidden" name="idcartella" value="" />
					<input type="hidden" name="codiceutente" value="<?= $codiceutente ?>" />
					<div class="nomandatory">
						<input type="hidden" name="nomandatory" />
					</div>

					<div class="riga">
						<span class="rigo_mask">
							<div class="testo_mask">data di apertura</div>
							<div class="campo_mask mandatory data_passata">
								<input type="text" class="campo_data scrittura" name="data_apertura" value="<?php echo ($data_odierna); ?>" />
							</div>
						</span>
						<span class="rigo_mask">
							<div class="testo_mask">Normativa / Regime</div>
							<div class="campo_big ">
								<?
								$query_r = "SELECT idimpegnativa, idregime, idnormativa, regime, normativa, idutente, data_inizio_trat, data_fine_trat, (DATEDIFF(DAY, data_inizio_trat, data_fine_trat) / 2) AS meta_progetto
											FROM re_pazienti_impegnative 
											--WHERE DataDimissione is null
											GROUP BY idimpegnativa, idregime, idnormativa, regime, normativa, idutente, data_inizio_trat, data_fine_trat, (DATEDIFF(DAY, data_inizio_trat, data_fine_trat) / 2) 
											HAVING idutente = $idutente order by idregime ASC";
								// if($_SESSION['UTENTE']->_properties['uid'] == 11) {
								// 	echo "<br>$query_r<br>";
								// }
								$rs_r = mssql_query($query_r, $conn);
								if (!$rs_r) error_message(mssql_error());

								$cc_infermieristica_ex26 = 0;
								$cc_infermieristica_leg8 = 0;
								$cc_infermieristica_rd1est = 0;
								$cc_infermieristica_rd1int = 0;
								$cc_infermieristica_TPrd1int = 0;
								$cc_infermieristica_TPrd1est = 0;
								$cc_infermieristica_suap = 0;
								$cc_infermieristica_TPsuap = 0;

								$cc_infermieristica_ex26_normativa = "";
								$cc_infermieristica_leg8_normativa = "";
								$cc_infermieristica_rd1est_normativa = "";
								$cc_infermieristica_rd1int_normativa = "";
								$cc_infermieristica_TPrd1int_normativa = "";
								$cc_infermieristica_TPrd1est_normativa = "";
								$cc_infermieristica_suap_normativa = "";
								$cc_infermieristica_TPsuap_normativa = "";

								$cc_infermieristica_ex26_idregime = "";
								$cc_infermieristica_leg8_idregime = "";
								$cc_infermieristica_rd1est_idregime = "";
								$cc_infermieristica_rd1int_idregime = "";
								$cc_infermieristica_TPrd1int_idregime = "";
								$cc_infermieristica_TPrd1est_idregime = "";
								$cc_infermieristica_suap_idregime = "";
								$cc_infermieristica_TPsuap_idregime = "";

								$regime_selezionato = false;								
								?>

								<select name="regime_assistenza" class="scrittura textsmall" onchange="reload_pianificazione('<?= $idutente ?>',this.value,'<?php echo $codiceutente; ?>', this.querySelector('option:checked').getAttribute('data-id-imp') );">
									<? 
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
											// if($_SESSION['UTENTE']->_properties['uid'] == 11) {
											// 	echo "<script>alert('check ok - data inizio: $data_inizio_trat_sel');</script>";
											// }
										}							
									?>

									<optgroup <?=$optgroup_style?> label="↓ Dal <?=$data_inizio_trat?> al <?=$data_fine_trat?> (totale giorni: <?=($fine_progetto)?>)"?>">
									<optgroup <?=$optgroup_style?> label="Metà progetto (<?=($meta_progetto)?> giorni) ricorre il giorno <?=date('d/m/Y', strtotime($row_r['data_inizio_trat'] . " +$meta_progetto days"))?>">
										<option <?=$sel?> value="<?=$row_r['idregime']?>" data-id-imp="<?=$idimpegnativa_r?>">
											[<?=$idimpegnativa_r?>] <?=htmlentities(strtoupper($normativa_r." - ".$regime_r))?>
										</option>
									</optgroup>
									</optgroup>

									<?	//echo "<option> ".htmlentities(strtoupper($idregime."==".$row_r['idregime'])) ."</option>";
										//if($cc_infermieristica_ex26 == 0 && strpos(strtolower($regime_r), "residenziale") !== false ) {
										if (
											$cc_infermieristica_ex26 == 0 &&
											$row_r['idregime'] == 26 || $row_r['idregime'] == 28  || $row_r['idregime'] == 20
										) {
											$cc_infermieristica_ex26_normativa = $normativa_r . " - " . $regime_r;
											$cc_infermieristica_ex26 = 1;
											$cc_infermieristica_ex26_idregime = 52;
										}
									}

									if(!$regime_selezionato) {
										$data_inizio_trat_ymd_sel	= 	$data_inizio_trat_ymd;
										$data_fine_trat_ymd_sel		=	$data_fine_trat_ymd;										
										$data_inizio_trat_sel	=	$data_inizio_trat;
										$data_fine_trat_sel		=	$data_fine_trat;
										$meta_progetto_sel		=	$meta_progetto;
										$fine_progetto_sel		=	$fine_progetto;
									}

									/*
									if ($cc_infermieristica_ex26 == 1) {
										if ($idregime == $cc_infermieristica_ex26_idregime) $sel_inferm = "selected";
										else	$sel_inferm = "";
										echo '<option ' . $sel_inferm . ' value="' . $cc_infermieristica_ex26_idregime . '">' . strtoupper($cc_infermieristica_ex26_normativa) . ' (INFERMIERISTICO)</option>';
									}					
									*/
									mssql_free_result($rs_r);
									// if($_SESSION['UTENTE']->_properties['uid'] == 11) {
									// 	echo "<script>alert('fine ciclo - data inizio: $data_inizio_trat_sel');</script>";
									// }
									?>
								</select>
							</div>
							<!-- visualizzo le date dell'impegnativa selezionata -->
							<div>
								Inizio trattamento: <?=$data_inizio_trat_sel?><br>
								Metà trattamento: &nbsp;<?= date('d/m/Y', strtotime("$data_inizio_trat_ymd_sel +$meta_progetto_sel days"))?><br>
								Fine trattamento: &nbsp;&nbsp;<?=$data_fine_trat_sel?>
							</div>
							<!-- forzo la seleziona sull'ultima impegnativa (se non selezionata) o su quella selezionata -->
							<!-- <script>
								$('[name="regime_assistenza"] option[data-id-imp=<?=!$regime_selezionato ? $idimpegnativa_r : $_GET['idimpegnativa']?>]').attr('selected','selected');
							</script> -->
						</span>
					</div>

					<div class="riga" style="display:none;">
						<span class="rigo_mask">
							<div class="testo_mask">effettua ora la pianificazione <input type="checkbox" checked onclick="javascript:$('#pianificazione').toggle();" class="scrittura_campo" name="effettuapianificazione" /></div>
						</span>
					</div>

					<div id="pianificazione">
						<?
						$arr_imp = array();
						array_push($arr_imp, "");
						$query = "SELECT  idimpegnativa, DataAutorizAsl,ording FROM re_impegnative_per_pianificazione WHERE (idutente = $idutente) AND (idregime=$idregime) order by ording DESC, idimpegnativa DESC";
						$rs1 = mssql_query($query, $conn);
						while ($row1 = mssql_fetch_assoc($rs1)) {
							array_push($arr_imp, $row1['idimpegnativa']);
						}

						$i = 1;
						$z = 1;
						$ord_cont = 0;
						$ord_cont_t = 0;
						$ordinamento = "";
						$ordinamento_t = "";
						for ($j = 0; $j < count($arr_imp); $j++) {
							$conn = db_connect();
							$query = "SELECT idtest,nome,codice,tipo_test,descrizione,test_ramificato from re_test_clinici where disponibile_in_lista_moduli=1 and stato=1 order by nome asc";
							$rs1 = mssql_query($query, $conn);
							if (!$rs1) error_message(mssql_error());
							$conta_t = mssql_num_rows($rs1);
							for ($yy = 1; $yy <= $conta_t; $yy++) {
								$ord_cont_t++;
								$ordinamento_t .= $ord_cont_t . " ";
							}
							if ($j == 0) { ?>
								<div class="titolo_pag">
									<h1>seleziona i moduli da inserire nella cartella clinica</h1>
								</div>
							<?
								$query = "SELECT * from re_moduli_regimi_replica where idregime=$idregime and ((replica=0) or (replica=1)) order by id asc";
								$idimpegnativa = 0;
								$rs1 = mssql_query($query, $conn);
								if (!$rs1) error_message(mssql_error());
								$conta = mssql_num_rows($rs1);
							} else {
								$query = "SELECT * from re_moduli_regimi_replica where idregime=$idregime and ((replica=2) or (replica=3)) order by id asc";
								$rs1 = mssql_query($query, $conn);
								if (!$rs1) error_message(mssql_error());
								$conta = mssql_num_rows($rs1);
								$idimpegnativa = $arr_imp[$j]; ?>
								<div class="titolo_pag">
									<h1>seleziona le impegnative da associare alla cartella</h1>
								</div>
								<table id="table" class="tablesorter" cellspacing="1" style="margin:0px 0px 10px">
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
										if ($row1 = mssql_fetch_assoc($rs1));
										$data_auth_asl = formatta_data($row1['DataAutorizAsl']);
										$prot_auth_asl = $row1['ProtAutorizAsl'];
										$regime = $row1['regime'];
										$normativa = $row1['normativa'];
										$inizio = $row1['data_inizio_trat'];
										$fine = $row1['data_fine_trat'];
										$num_trat = $row1['NumeroTrattamenti'];
										?>
										<tr>
											<td><span class="id_notizia_cat"><?= $prot_auth_asl ?></span></td>
											<td><span class="id_notizia_cat"><?= $data_auth_asl ?></span></td>
											<td><span class="id_notizia_cat"><?= $regime ?></span></td>
											<td><span class="id_notizia_cat"><?= $normativa ?></span></td>
											<td><?= formatta_data($inizio) ?></td>
											<td><?= formatta_data($fine) ?></td>
											<td><?= $num_trat ?></td>
											<td><span class="id_notizia_cat"><input type="checkbox" id="imp<?= $j ?>" name="impegnativa<?= $j ?>" value="<?= $idimpegnativa ?>" onclick="gestImp(<?= $j ?>,<?= $idregime ?>,<?= $idimpegnativa ?>,'<?= $imp_associated ?>',<?= $ord_cont ?>,<?= $ord_cont_t ?>,'<?= $idcartella ?>');" />
											</td>
										</tr>
									</tbody>
								</table>
							<?
								for ($yy = 1; $yy <= $conta; $yy++) {
									$ord_cont++;
									$ordinamento .= $ord_cont . " ";
								}
							}
							?>
							<div id="pianficazione<?= $j ?>">
								<div class="titolo_pag" id="label<?= $j ?>" style="display:none;margin:0px 0px 0px 15px;width:98%;">
									<h1 style="font-size:10px;">Moduli da ssociare all'impegnativa</h1>
								</div>


								<? if ($j == 0) { ?>
									<table id="table<?= $j ?>" class="tablesorter" cellspacing="1" <? if ($j > 0) { ?>style="display:;margin:0px 0px 15px 15px;width:98%;" <? } ?>>
										<thead>
											<tr>
												<th width="10%">codice sgq</th>
												<th>ant.</th>
												<th width="30%">nome</th>
												<th>pianifica</th>
												<th>obbligatorio</th>
												<th>figura responsabile</th>
												<th>scadenza</th>
												<th>scad. ciclica</th>
												<th>scad. trattamenti</th>
												<th>scad. puntuale</th>
											</tr>
										</thead> <?

													while ($row1 = mssql_fetch_assoc($rs1)) {
														$idmodulo = $row1['idmodulo'];
														$query = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc";
														$rs = mssql_query($query, $conn);

														if (!$rs) error_message(mssql_error());

														while ($row = mssql_fetch_assoc($rs)) {
															$idmodulo_id = $row['id'];
															$nome = $row['nome'];
															$codice = $row['codice'];

															if ($i > 1) {
																$style = "style=\"display:none\"";
																$class = "riga_sottolineata_diversa hidden";
																$dis = "disabled";
															} else {
																$nodrag = "nodrag=false";
																$style = '';
																$class = "odd";
																$dis = "";
															}

															$obbligatorio = "n";
															$scadenza = "";
															$trattamenti = "";
															$data_fissa = "";

															$query4 = "SELECT TOP (1) id_modulo, id_regime, status, cancella, id, id_operatore, id_cartella,nome FROM dbo.re_moduli_medici_regime
								WHERE (status = 1) AND (cancella = 'n')AND (id_cartella = 0) AND (id_modulo = $idmodulo) AND (id_regime = $idregime) ORDER BY id DESC";

															$rs4 = mssql_query($query4, $conn);
															$medico = "";
															$nome_medico = "";
															if ($row4 = mssql_fetch_assoc($rs4)) {
																$medico = $row4['id_operatore'];
																$nome_medico = $row4['nome'];
															}

															mssql_free_result($rs4);

															$query3 = "SELECT * FROM regimi_moduli where idmodulo=$idmodulo and idregime=$idregime";
															$rs3 = mssql_query($query3, $conn);

															if (!$rs3) error_message(mssql_error());

															if (mssql_num_rows($rs3) > 0) {
																if ($row3 = mssql_fetch_assoc($rs3)) {
																	$obbligatorio = trim($row3['obbligatorio']);
																	$scadenza = $row3['scadenza'];
																	$trattamenti = $row3['trattamenti'];
																	if ($trattamenti == 0) $trattamenti = "";
																	$data_fissa = formatta_data($row3['data_fissa']);
																	if ($data_fissa == "01/01/1900") $data_fissa = "";
																}
																if (($obbligatorio == "s") or ($obbligatorio == "o")) {
																	$checked = "checked";
																	$dis = "disabled";
																	//$dis="READONLY";
																} else {
																	$checked = "";
																	$dis = "";
																}
													?>
													<tr id="<?= $i ?>">

														<td><span class="id_notizia_cat">
																<input type="hidden" name="selected<?= $i ?>" value="<? if ($j == 0) echo ("1");
																													else echo ("0"); ?>" class="imp<?= $j ?>" />
																<input type="hidden" name="mod_impegnativa<?= $i ?>" value="<?= $idimpegnativa ?>" />
																<input type="hidden" name="modulo<?= $i ?>" value="<?= $idmodulo ?>" />
																<input type="hidden" name="idmodulo<?= $i ?>" value="<?= $idmodulo_id ?>" />
																<?= $codice ?>
															</span>
														</td>
														<td>
															<div id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev('<?= $idmodulo_id ?>');" title="anteprima modulo"><img src="images/view.png" /></a></div>
														</td>
														<td><span class="id_notizia_cat"><?= pulisci_lettura($nome); ?></span></td>
														<td>
															<div class="groups_1_one nomandatory">
																<? if (($obbligatorio == "s") or ($obbligatorio == "o") or ($obbligatorio == "p")) { ?>
																	<input type="hidden" name="obbligatorio<?= $i ?>" value="s" />
																	<input checked type="checkbox" onclick="javascript:return false;" name="default<?= $i ?>" />
																<? } else { ?>
																	<input type="hidden" name="obbligatorio<?= $i ?>" value="n" />
																	<input type="checkbox" onchange="javascript:a_d_medico(this,'<?= $i ?>');" name="default<?= $i ?>" />
																<? } ?>
															</div>
														</td>

														<td align="center">
															<? if (($obbligatorio == 's') or ($obbligatorio == 'o') or ($obbligatorio == 'p')) { ?>
																<div id="scelta_vis<?= $i ?>" style="display:block;">
																<? } else { ?>
																	<div id="scelta_vis<?= $i ?>" style="display:none;">
																	<? } ?>
																	<select name="scelta<?= $i ?>" style="width:150px">
																		<? if (($obbligatorio == 's') or ($obbligatorio == 'o')) { ?>
																			<option value="o">obbligatorio</option>
																		<? } elseif (($obbligatorio == 'p')) { ?>
																			<option value="o">obbligatorio</option>
																			<option value="p" selected>opzionale</option>
																		<? } else { ?>
																			<option value="o">obbligatorio</option>
																			<option value="p" selected>opzionale</option>
																		<? }	?>
																	</select>
																	</div>
														</td>
														<td>
															<?php
																$display = "";
																if (($obbligatorio != 'n')) {
																	$display = "block"
															?>
																<div id="medico_vis<?= $i ?>" style="display:block;">
																<? } else {
																	$display = "none";
																}
																?>
																<div id="medico_vis<?= $i ?>" style="display:<?= $display ?>;">
																	<input type="hidden" name="medico_old<?= $i ?>" value="<?= $medico ?>" />
																	<input type="hidden" name="medico_id_<?= $i ?>" id="medico_id_<?= $i ?>" value="<?= $medico ?>" />
																	<input type="text" name="medico<?= $i ?>" id="medico<?= $i ?>" readonly value="<?= $nome_medico ?>" /><a href="javascript:void(0)" onclick="javascript:small_window('get_medici_popup.php?dest=<?= $i ?>&parent=<? $medico ?>&tipo=mod&idmodulo=<?= $idmodulo ?>');">seleziona</a>
																</div>
														</td>
														<td><?
																$scadenza = trim($scadenza);
																$trattamenti = trim($trattamenti);

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
																elseif(in_array($idmodulo, array(283, 284,312,313,314,315,316)) && !empty($meta_progetto_sel) && ($fine_progetto_sel > 120)) {
																	
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
															?>
															<?php if ($obbligatorio != 'n') $dis_scad = "display:block;";
																else $dis_scad = "display:none;"; ?>
															<div id="scad_f<?= $i ?>" style="<?= $dis_scad ?>">
																<select id="scad_flg<?= $i ?>" name="scad_flg<?= $i ?>" style="width:100px;">
																	<option value="n" <? if (($scadenza == '') and ($trattamenti == '') and ($data_fissa == '')) echo ("selected"); ?> onclick="javascript:$('#scad_vis<?= $i ?>').slideUp();$('#trat_vis<?= $i ?>').slideUp();$('#data_vis<?= $i ?>').slideUp();">No</option>
																	<option value="c" <? if ($scadenza != '') echo ("selected"); ?> onclick="javascript:$('#scad_vis<?= $i ?>').slideDown();$('#trat_vis<?= $i ?>').slideUp();$('#data_vis<?= $i ?>').slideUp();">Ciclica</option>
																	<option value="t" <? if ($trattamenti != '') echo ("selected"); ?> onclick="javascript:$('#scad_vis<?= $i ?>').slideUp();$('#trat_vis<?= $i ?>').slideDown();$('#data_vis<?= $i ?>').slideUp();">Trattamenti</option>
																	<option value="d" <? if ($data_fissa != '') echo ("selected"); ?> onclick="javascript:$('#scad_vis<?= $i ?>').slideUp();$('#trat_vis<?= $i ?>').slideUp();$('#data_vis<?= $i ?>').slideDown();">Data fissa</option>
																</select>
															</div>
														</td>
														<td><?php if (($scadenza != '') and ($obbligatorio != 'n')) $dis_scad = "display:block;";
																else $dis_scad = "display:none;"; ?>
															<div id="scad_vis<?= $i ?>" style="<?= $dis_scad ?>" class="nomandatory integer">
																<input type="text" name="scadenza<?= $i ?>" value="<?= $scadenza ?>" style="width:50px" />
															</div>
														</td>
														<td><?php if (($trattamenti != '') and ($obbligatorio != 'n')) $dis_scad = "display:block;";
																else $dis_scad = "display:none;"; ?>
															<div id="trat_vis<?= $i ?>" style="<?= $dis_scad ?>" class="nomandatory integer">
																<input type="text" name="trattamenti<?= $i ?>" value="<?= $trattamenti ?>" style="width:50px" />
															</div>
														</td>
														<td><?php if (($data_fissa != '') and ($obbligatorio != 'n')) $dis_scad = "display:block;";
																else $dis_scad = "display:none;"; ?>
															<div id="data_vis<?= $i ?>" style="<?= $dis_scad ?>" class="nomandatory">
																<input type="text" class="campo_data" name="data_fissa<?= $i ?>" value="<?= $data_fissa ?>" style="width:100px" />
															</div>
														</td>
													</tr>
										<?
															}
															$i++;
														}
													}
													for ($yy = 1; $yy <= $conta; $yy++) {
														$ord_cont++;
														$ordinamento .= $ord_cont . " ";
													}
													$ord_cont++;
													$ordinamento .= $ord_cont . " ";
										?>
										</tbody>
									</table>
									<? if ($j == -1) { ?>
										<div class="titolo_pag" id="label_t<?= $j ?>" style="display:;">
											<h1>Test Clinici associati alla cartella</h1>
										</div>
									<? } else { ?>
										<div class="titolo_pag" id="label_t<?= $j ?>" style="display:;margin:0px 0px 0px 15px;width:98%;">
											<h1 style="font-size:10px;">Test Clinici associati alla cartella</h1>
										</div>
									<? } ?>
									<table id="table_t<?= $j ?>" class="tablesorter" cellspacing="1" <? if ($j > 0) { ?>style="<? if (!$imp_associated) { ?>display:;<? } ?>margin:0px 0px 15px 15px;width:98%;" <? } ?>>
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
										<tbody> <?
												$query = "SELECT idtest,nome,codice,tipo_test,descrizione,test_ramificato from re_test_clinici where disponibile_in_lista_moduli=1 and stato=1 order by nome asc";
												$rs1 = mssql_query($query, $conn);
												if (!$rs1) error_message(mssql_error());
												$conta = mssql_num_rows($rs1);
												while ($row1 = mssql_fetch_assoc($rs1)) {
													$test_associated = 0;
													$idtest = $row1['idtest'];
													$codice = $row1['codice'];
													$nome = pulisci_lettura($row1['nome']);
													$tipo_test = $row1['tipo_test'];
													$descrizione = pulisci_lettura($row1['descrizione']);
													$test_ramificato = $row1['test_ramificato'];
													$operatore = "";
												?>
												<tr>
													<td><input type="hidden" name="selected_t<?= $z ?>" value="0" class="imp<?= $j ?>" />
														<input type="hidden" name="mod_impegnativa_t<?= $z ?>" value="<?= $idimpegnativa ?>" />
														<input type="hidden" name="idtest<?= $z ?>" value="<?= $idtest ?>" /><?= $codice ?>
													</td>
													<td><?= $tipo_test ?></td>
													<td><?= $nome ?></td>
													<td><?= $descrizione ?></td>
													<td>
														<input type="hidden" name="medico_test_id_<?= $z ?>" id="medico_test_id_<?= $z ?>" value="" />
														<input type="text" name="medico_test<?= $z ?>" id="medico_test<?= $z ?>" value="" /><a href="javascript:void(0)" onclick="javascript:small_window('get_medici_popup.php?dest=<?= $z ?>&parent=<? $operatore ?>&tipo=test');">seleziona</a>
													</td>
													<td>
														<div class="groups_1_one nomandatory">
															<input type="checkbox" name="default_test<?= $z ?>" />
														</div>
													</td>
												</tr>
											<?
													$z++;
												} ?>
										</tbody>
									</table>
								<? } ?>

							</div>
						<?

						} ?>
						<input id="debug_t" type="hidden" name="debug_t" value="<?= $ordinamento_t ?>">
						<input id="debug" type="hidden" name="debug" value="<?= $ordinamento ?>">
						<div class="titolo_pag">
							<div class="comandi">
								<input type="submit" title="salva" value="salva" class="button_salva" />
							</div>
						</div>

				</form>

			</div>
			<script type="text/javascript" language="javascript">
				$(document).ready(function() {
					$.mask.addPlaceholder('~', "[+-]");
					$(".campo_data").mask("99/99/9999");

				});

				function gestImp(val, idregime, idimpegnativa, imp_associated, val1, val2, idcartella) {
					if ($('#imp' + val + ':checked').val() != undefined) {


						$.ajax({
							type: "POST",
							url: "ajax_moduli_pianificazione.php",
							data: "idregime=" + idregime + "&idimpegnativa=" + idimpegnativa + "&j=" + val + "&imp_associated=" + imp_associated + "&i=" + val1 + "&z=" + val2 + "&idcartella=" + idcartella,
							success: function(msg) {
								//alert( "Data Saved: " + msg );			 
								$("#pianficazione" + val).html(trim(msg));
								$.mask.addPlaceholder('~', "[+-]");
								//alert("pianificazione"+val);
								$("#pianficazione" + val + " .campo_data").mask("99/99/9999");
							}
						});
						$('#table' + val).slideDown();
						$('.imp' + val).val('1');
						$('#label' + val).slideDown();
						$('#label_t' + val).slideDown();
						$('#table_t' + val).slideDown();

					} else {
						$('#table' + val).slideUp();
						$('.imp' + val).val('0');
						$('#label' + val).slideUp();
						$('#label_t' + val).slideUp();
						$('#table_t' + val).slideUp();
					}
				}

				function a_d_medico(ob, i) {

					if (ob.checked == true) {
						$('#medico_vis' + i).slideDown();
						$('#scelta_vis' + i).slideDown();
						$('#scad_f' + i).slideDown();
						if ($('#scad_flg' + i).val() == 'c')
							$('#scad_vis' + i).slideDown();
						if ($('#scad_flg' + i).val() == 't')
							$('#trat_vis' + i).slideDown();
						if ($('#scad_flg' + i).val() == 'd')
							$('#data_vis' + i).slideDown();
					} else {
						$('#medico_vis' + i).slideUp();
						$('#scelta_vis' + i).slideUp();
						$('#scad_f' + i).slideUp();
						$('#scad_vis' + i).slideUp();
						$('#trat_vis' + i).slideUp();
						$('#data_vis' + i).slideUp();
					}
				}

				function reload_pianificazione(idpaziente, idregime, codiceutente, idimpegnativa) {
					$("#layer_nero2").toggle();
					$('.pianifica_cartella').innerHTML = "";
					pagina_da_caricare = "re_pazienti_sanitaria_cartelle.php?do=add&idregime=" + idregime + "&id=" + idpaziente + "&codiceutente=" + codiceutente + "&idimpegnativa=" + idimpegnativa;
					$(".pianifica_cartella").load(pagina_da_caricare, '', function() {
						loading_page('loading');
						$("#container-5 .tabs-selected").removeClass('tabs-selected');
					});
				}
			</script>

		<?
	}

	// lista d'attesa
	else {
		?>

			<div class="titoloalternativo">
				<h1>Nuova Cartella Clinica</h1>
			</div>
			<div class="info">Non è possibile creare una cartella clinica per questo paziente in quanto è nello stato "lista d'attesa".<br />E' possibile creare una visita specialistica cliccando su "visite specialistiche"</div>

		<?

	} //end else


}


function addvisita()
{

	$idutente = $_REQUEST['id'];
	$idpaziente = $idutente;
	$conn = db_connect();
	$idregime = 0;
	$stato_paziente = get_stato_paziente($idutente);

	if ($stato_paziente > 0) {
		$stato_paziente_descr = get_stato_paziente_descr($stato_paziente);
		$idregime = get_regime_paziente($idutente);
	}
		?>

		<div id="sanitaria">

			<div class="titoloalternativo">
				<h1>Moduli Disponibili</h1>
				<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>

			</div>
			<table id="table" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th>codice sgq</th>
						<th>modulo</th>
						<th>figura responsabile</th>
						<th>ultima compilazione</th>
						<td>visualizza</td>
						<td>aggiungi</td>
					</tr>
				</thead>
				<tbody>

					<?
					$conn = db_connect();
					$query = "SELECT idmodulo from re_distinct_moduli";

					$ordinamento = "";

					$rs1 = mssql_query($query, $conn);

					if (!$rs1) error_message(mssql_error());

					$conta = mssql_num_rows($rs1);
					$i = 1;
					while ($row1 = mssql_fetch_assoc($rs1)) {

						$idmodulo = $row1['idmodulo'];
						$query = "SELECT top 1 * from moduli where idmodulo=$idmodulo and tipo=1 order by id desc";

						$rs = mssql_query($query, $conn);

						if (!$rs) error_message(mssql_error());

						if ($row = mssql_fetch_assoc($rs)) {
							$idmodulo_id = $row['id'];
							$nome = $row['nome'];
							$codice = $row['codice'];
							$stampa_continua = $row['stampa_continua'];
							$ultima_compilazione = "";
							$medico = "";
							$id_impegnativa = "";
							$query4 = "SELECT data_osservazione,datains,opeins,id_impegnativa  from istanze_testata where id_paziente=$idpaziente and id_cartella=0 and id_modulo_padre=$idmodulo";
							$rs4 = mssql_query($query4, $conn);
							//echo($query4);
							if (!$rs4) error_message(mssql_error());
							if ($row4 = mssql_fetch_assoc($rs4)) {
								if ($row4['data_osservazione'] != "")
									$ultima_compilazione = formatta_data($row4['data_osservazione']);
								elseif ($row4['datains'] != "") $ultima_compilazione = formatta_data($row4['datains']);
								else
									$ultima_compilazione = "";

								if ($row4['id_impegnativa'] != NULL) $id_impegnativa = $row4['id_impegnativa'];
								$medico = $row4['opeins'];
								$query4 = "SELECT uid,nome FROM operatori WHERE (uid = $medico)";
								$rs4 = mssql_query($query4, $conn);
								if ($row4 = mssql_fetch_assoc($rs4)) $medico = $row4['nome'];
								mssql_free_result($rs4);
							}
					?>
							<tr>
								<td><?= $codice ?></td>
								<td><?= pulisci_lettura($nome) ?></td>
								<td><?= $medico ?></td>
								<td><?= $ultima_compilazione ?></td>
								<? if ($ultima_compilazione != '') { ?>
									<td><a href="#" onclick="javascript:view_istanze_modulo('0','<?= $idpaziente ?>','<?= $idmodulo_id ?>','<?= $id_impegnativa ?>');"><img src="images/view.png" /></a></td>
								<?
								} else {
								?>
									<td>&nbsp;</td>
								<?
								}
								if ((get_permesso_modulo($idmodulo, $_SESSION['UTENTE']->get_gid())) and  (($stampa_continua == 1) or (($stampa_continua == 0) and (trim($ultima_compilazione) == '')))) {
								?>
									<td><a href="#" onclick="javascript:add_visita('0','<?= $idpaziente ?>','<?= $idmodulo_id ?>');"><img src="images/add.png" /></a></td>
								<? } else { ?>
									<td>&nbsp;</td>
								<? } ?>
							</tr>
					<? }
					} ?>
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
							for (var i = 0; i < (rows.length - 1); i++) {
								if (rows[i].style.display != 'none')
									debugStr += rows[i].id + " ";

							}
							document.getElementById("debug").value = debugStr;

						},
						onDragStart: function(table, row) {
							//alert(row.id);
							//$(#debugArea).html("Started dragging row "+row.id);
						}
					});
				});


				function add_visita(idcartella, idpaziente, idmodulo) {

					$("#layer_nero2").toggle();
					$('#sanitaria').innerHTML = "";
					pagina_da_caricare = "re_pazienti_sanitaria_POST.php?do=aggiungi_visita&idcartella=" + idcartella + "&idpaziente=" + idpaziente + "&idmodulo=" + idmodulo;
					$("#sanitaria").load(pagina_da_caricare, '', function() {
						loading_page('loading');
						$("#container-5 .tabs-selected").removeClass('tabs-selected');
					});


				}

				function view_istanze_modulo(idcartella, idpaziente, idmodulo, impegnativa) {
					$("#layer_nero2").toggle();
					$('#sanitaria').innerHTML = "";
					pagina_da_caricare = "re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella=" + idcartella + "&idpaziente=" + idpaziente + "&idmodulo=" + idmodulo + "&impegnativa=" + impegnativa;
					$("#sanitaria").load(pagina_da_caricare, '', function() {
						loading_page('loading');
						$("#container-5 .tabs-selected").removeClass('tabs-selected');
					});
				}

				$("table").tablesorter({
					sortList: [
						[0, 0]
					],
					widthFixed: true,
					widgets: ['zebra']
				});
			</script>

		</div>

	<?
}



function visualizza_test_clinici()
{

	$idutente = $_REQUEST['idpaziente'];
	$idcartella = $_REQUEST['idcartella'];
	$cartella = $_REQUEST['cartella'];
	$idpaziente = $idutente;

	$cartella_chiusa = false;
	$conn = db_connect();

	if ($idcartella > 0) {
		$query = "select id, idregime, codice_cartella, versione, data_chiusura from utenti_cartelle where id=$idcartella";
		$rs1 = mssql_query($query, $conn);
		if ($row = mssql_fetch_assoc($rs1)) {
			$id_regime = $row['idregime'];
			$cod_cartella = $row['codice_cartella'];
			$versione = $row['versione'];
			if ($row['data_chiusura'] != "") $cartella_chiusa = true;
		}
		mssql_free_result($rs1);
	}

	$idregime = 0;
	$stato_paziente = get_stato_paziente($idutente);
	if ($stato_paziente > 0) {
		$stato_paziente_descr = get_stato_paziente_descr($stato_paziente);
		$idregime = get_regime_paziente($idutente);
	}
	?>

		<div id="sanitaria">

			<div class="titoloalternativo">
				<h1>Cartella selezionata: <?= $cartella ?></h1>
				<h1>Test Clinici Disponibili</h1>
				<div class="stampa"><a href="javascript:stampa('fragment-4');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
			</div>
			<?
			$conn = db_connect();
			$query = "SELECT * from re_test_associati_cartella where id_cartella=$idcartella order by prot_asl,nome asc";
			$ordinamento = "";

			$rs1 = mssql_query($query, $conn);

			if (!$rs1) error_message(mssql_error());

			$conta = mssql_num_rows($rs1);
			$i = 1;
			$old_imp = "x";
			$open = 0;
			while ($row1 = mssql_fetch_assoc($rs1)) {
				$idtest = $row1['idtest'];
				$codice = $row1['codice'];
				$nome = $row1['nome'];
				$responsabile = $row1['responsabile'];
				$tipo_test = $row1['tipo_test'];
				$descrizione = $row1['descrizione'];
				$test_ramificato = $row1['test_ramificato'];
				$prot_asl = $row1['prot_asl'];
				$id_imp = $row1['id_impegnativa'];
				$data_asl = formatta_data($row1['data_asl']);
				$conta_istanze = 0;
				$query = "SELECT idtestclinico from re_test_clinici_result where idpaziente=$idpaziente and idtest=$idtest ";
				//echo($query);
				if ($idcartella > 0)
					$query .= " and idcartella=$idcartella";
				else {
					$idcartella = 0;
					$cartella = 0;
				}

				$rs2 = mssql_query($query, $conn);
				if (!$rs2) error_message(mssql_error());
				$conta_istanze = mssql_num_rows($rs2);

				if ($test_ramificato == 1) {
					$query = "SELECT idtestclinico from test_clinici_compilati where idpaziente=$idpaziente and idtest=$idtest";
					$rs22 = mssql_query($query, $conn);
					if (!$rs22) error_message(mssql_error());
					$conta_istanze = mssql_num_rows($rs22);
				}
				if ($old_imp != $id_imp) {
					if ($open) print("</tbody></table>");
			?>
					<div class="titolo_pag">
						<div class="comandi">
							<?
							if ($id_imp != NULL) {
								echo ("Impegnativa prot. <strong>" . $prot_asl . "</strong> del <strong>" . $data_asl . "</strong>");
							} ?>
							<div class="aggiungi aggiungi_left"></div>
						</div>
					</div>

					<table id="table" class="tablesorter" cellspacing="1">
						<thead>
							<tr>
								<th>codice sgq</th>
								<th>Tipo</th>
								<th>Test</th>
								<th>Descrizione</th>
								<th>Responsabile</th>
								<td>istanze</td>
								<td>aggiungi</td>
							</tr>
						</thead>
						<tbody>
						<?
						$old_imp = $id_imp;
						$open = 1;
					}
						?>
						<tr>
							<td><?= $codice ?></td>
							<td><?= $tipo_test ?></td>
							<td><?= pulisci_lettura($nome) ?></td>
							<td><?= pulisci_lettura($descrizione) ?></td>
							<td><?= $responsabile ?></td>
							<?
							if (!$idimp)
								$idimp = 0;

							if ($conta_istanze > 0) {
								if ($test_ramificato == 0) {

							?>
									<td><a href="#" onclick="javascript:view_istanze_test_clinico('<?= $idcartella ?>','<?= $idtest ?>','<?= $idpaziente ?>','<?= $cartella ?>','<?= $id_imp ?>');"><img src="images/book_open.png" /></a></td>
								<?
								} else {
								?>
									<td>&nbsp;</td>
									<!--<td><a href="#"  onclick="javascript:view_istanze_test_clinico_ramificato('<?= $idcartella ?>','<?= $idtest ?>','<?= $idpaziente ?>','<?= $cartella ?>','<?= $id_imp ?>');" ><img src="images/book_open.png" /></a></td>-->
								<?
								}
							} else {
								?>
								<td>&nbsp;</td>
								<?
							}
							if ($cartella_chiusa == false) {
								if ($test_ramificato == 0) {
								?>
									<td><a href="#" onclick="javascript:add_test_clinico('<?= $idcartella ?>','<?= $idpaziente ?>','<?= $idtest ?>','<?= $cartella ?>','<?= $id_imp ?>');"><img src="images/add.png" /></a></td>
								<?
								} else {
								?>
									<td><a href="#" onclick="javascript:add_test_clinico_ramificato('<?= $idcartella ?>','<?= $idpaziente ?>','<?= $idtest ?>','<?= $cartella ?>','<?= $id_imp ?>');"><img src="images/add.png" /></a></td>
								<?
								}
							} else {
								?>
								<td>&nbsp;</td>
							<?
							}
							?>
						</tr>
					<? } ?>
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
									for (var i = 0; i < (rows.length - 1); i++) {
										if (rows[i].style.display != 'none')
											debugStr += rows[i].id + " ";

									}
									document.getElementById("debug").value = debugStr;

								},
								onDragStart: function(table, row) {
									//alert(row.id);
									//$(#debugArea).html("Started dragging row "+row.id);
								}
							});
						});
					</script>

					<script>
						function add_test_clinico(idcartella, idpaziente, idtest, cartella, imp) {

							$("#layer_nero2").toggle();
							$('#sanitaria').innerHTML = "";
							pagina_da_caricare = "re_pazienti_sanitaria_POST.php?do=aggiungi_test_clinico&idcartella=" + idcartella + "&idpaziente=" + idpaziente + "&idtest=" + idtest + "&cartella=" + cartella + "&impegnativa=" + imp;
							$("#sanitaria").load(pagina_da_caricare, '', function() {
								loading_page('loading');
								$("#container-5 .tabs-selected").removeClass('tabs-selected');
							});


						}

						function add_test_clinico_ramificato(idcartella, idpaziente, idtest, cartella, imp) {

							$("#layer_nero2").toggle();
							$('#sanitaria').innerHTML = "";
							pagina_da_caricare = "re_pazienti_sanitaria_POST.php?do=aggiungi_test_clinico_ramificato&idcartella=" + idcartella + "&idpaziente=" + idpaziente + "&idtest=" + idtest + "&cartella=" + cartella + "&impegnativa=" + imp;
							$("#sanitaria").load(pagina_da_caricare, '', function() {
								loading_page('loading');
								$("#container-5 .tabs-selected").removeClass('tabs-selected');
							});


						}

						function view_istanze_modulo(idcartella, idpaziente, idmodulo) {
							$("#layer_nero2").toggle();
							$('#sanitaria').innerHTML = "";
							pagina_da_caricare = "re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&idcartella=" + idcartella + "&idpaziente=" + idpaziente + "&idmodulo=" + idmodulo;
							$("#sanitaria").load(pagina_da_caricare, '', function() {
								loading_page('loading');
								$("#container-5 .tabs-selected").removeClass('tabs-selected');
							});


						}

						function view_istanze_test_clinico(idcartella, idtest, idpaziente, cartella, idimp) {
							$("#layer_nero2").toggle();
							$('#sanitaria').innerHTML = "";
							pagina_da_caricare = "re_pazienti_sanitaria_POST.php?do=visualizza_istanze_test_clinico&idtest=" + idtest + "&idpaziente=" + idpaziente + "&idcartella=" + idcartella + "&cartella=" + cartella + "&idimp=" + idimp;
							$("#sanitaria").load(pagina_da_caricare, '', function() {
								loading_page('loading');
								$("#container-5 .tabs-selected").removeClass('tabs-selected');
							});


						}

						function view_istanze_test_clinico_ramificato(idcartella, idtest, idpaziente, cartella, idimp) {
							$("#layer_nero2").toggle();
							$('#sanitaria').innerHTML = "";
							pagina_da_caricare = "re_pazienti_sanitaria_POST.php?do=visualizza_istanze_test_clinico_ramificato&idtest=" + idtest + "&idpaziente=" + idpaziente + "&idcartella=" + idcartella + "&cartella=" + cartella + "&idimp=" + idimp;
							$("#sanitaria").load(pagina_da_caricare, '', function() {
								loading_page('loading');
								$("#container-5 .tabs-selected").removeClass('tabs-selected');
							});


						}
						$("table").tablesorter({
							widthFixed: true,
							widgets: ['zebra']
						});
					</script>

		</div>

	<?
}



if (isset($_SESSION['UTENTE'])) {

	if (!isset($do)) $do = '';
	$back = "lista_pazienti.php";
	// verifica i permessi
	if (in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";


		switch ($_POST['action']) {

			case "create":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else create();
				break;

			case "update":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
				else update($_REQUEST['id']);
				break;

			case "del":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
				else if ($_POST['choice'] == "si") del($_REQUEST['id']);
				break;
		}


		switch ($do) {

			case "add":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else add();
				break;
			case "addvisita":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else addvisita();
				break;

			case "visualizza_test_clinici":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else visualizza_test_clinici();
				break;



			case "edit":
				// verifica i permessi..				
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
				else edit($_REQUEST['id']);
				break;

			case "review":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
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
	} else {
		error_message("non hai i permessi per visualizzare questa pagina!");
		go_home();
	}
} else {
	print("non hai i permessi per visualizzare questa pagina1!");
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
	?>