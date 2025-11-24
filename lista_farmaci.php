<script type="text/javascript" src="script/jquery.tablesorter/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
<?php

include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');

$id_permesso = $id_menu = 91;

define('SEDE_SERV_AMB', 'Servizi ambulatoriali');
define('SEDE_RESIDENZIALE', 'Residenziale');
define('SEDE_NON_GESTITA', 'Struttura non gestita');

if (isset($_SESSION['UTENTE'])) {

	if (in_array($id_permesso, $_SESSION['PERMESSI'])) {

		switch ($do) {

			case "aggiungi_farmaco":
				if ($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					aggiungi_farmaco();
				} else {
					error_message("Permessi insufficienti per questa operazione!");
				}
				break;


			case "view_farmaco":
				if ($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					view_farmaco($_REQUEST['id']);
				} else {
					error_message("Permessi insufficienti per questa operazione!");
				}
				break;

			case "edit_farmaco":
				if ($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
					edit_farmaco($_REQUEST['id']);
				} else {
					error_message("Permessi insufficienti per questa operazione!");
				}
				break;

			default:
				show_list();
				break;
		}
	} else {
		error_message("Non hai i permessi per visualizzare questa pagina!");
		go_home();
	}
} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}


function show_list()
{
	$reparto_sel = $_GET['reparto_sel'];
	$stato_sel = isset($_GET['stato_sel']) ? $_GET['stato_sel'] : 2;
	$conn = db_connect();
	$_rep_filter = !empty($reparto_sel) ? " AND id_reparto = $reparto_sel" : '';
	$_stat_filter = !empty($stato_sel) ? " AND stato_farmaco = $stato_sel" : '';
	$lista_farmaci_sql = "SELECT id, lotto, nome, dosaggio, data_scadenza, data_carico, quantita_caricata, quantita_attuale, giacenza_minima, stato_farmaco, id_reparto
							FROM farmaci
							WHERE status = 1 AND struttura = 1 AND deleted = 0 $_rep_filter $_stat_filter
							ORDER BY nome, data_scadenza, lotto ASC";
	$lista_farmaci_rs = mssql_query($lista_farmaci_sql, $conn);

	$tot_farmaci = mssql_num_rows($lista_farmaci_rs);
	$lista_farmaci = array();

	while ($row = mssql_fetch_assoc($lista_farmaci_rs)) {
		$lista_farmaci[] = $row;
	}

	mssql_free_result($lista_farmaci_rs);

	if ($_SESSION['UTENTE']->is_root() || $_SESSION['UTENTE']->is_ds()) {
		$op_filter = '';
	} else {
		$op_filter = ' AND id IN (
			SELECT id_reparto
			FROM reparti_operatori
			WHERE id_operatore = ' . $_SESSION['UTENTE']->get_userid() . '
		)';
	}

	$_sql = "SELECT id, nome, sede
				FROM reparti rp
				WHERE status = 2 AND deleted = 0 
				$op_filter";
	$_rs = mssql_query($_sql, $conn);
	$lista_reparti = array();
	while ($row = mssql_fetch_assoc($_rs)) {
		$lista_reparti[] = $row;
	}
	mssql_free_result($_rs);
?>
	<div id="wrap-content">
		<div class="padding10">
			<div class="logo"><img src="images/re-med-logo.png" /></div>
			<div class="titoloalternativo">
				<h1>Farmaci di Struttura</h1>
				<div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
			</div>

			<div class="titolo_pag">
				<div class="comandi">
					<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_farmaci.php?do=aggiungi_farmaco');">aggiungi farmaco</a></div>
				</div>
			</div>

			<span style="border-bottom: 0px none;" class="rigo_mask no_print">
				<div class="campo_big" style="float:left;padding-right:10px">
					<h3>Per abilitare la funzionalità contatta il nostro reparto commerciale</h3>
					<br>
				</div>
				<div class="campo_big" style="float:left;padding-right:10px">
					<div class="testo_mask">Reparto</div>
					<div class="campo_mask">
						<select id="sel_rep_paz" name="sel_rep_paz">
							<?php
							if (count($lista_reparti) > 1)
								echo '<option value="" selected="selected" >Tutti</option>';

							foreach ($lista_reparti as $row) {
								switch ($row['sede']) {
									case 1:
										$row['sede'] = SEDE_SERV_AMB;
										break;

									case 2:
										$row['sede'] = SEDE_RESIDENZIALE;
										break;

									default:
										$row['sede'] = SEDE_NON_GESTITA;
										break;
								}
								echo '<option value="' . $row['id'] . '" ' . (!empty($reparto_sel) && $reparto_sel == $row['id'] ? 'selected' : '') . ' >' . $row['nome'] . ' - ' . $row['sede'] . '</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="campo_big " style="float:left;padding-right:10px">
					<div class="testo_mask">Stato farmaco</div>
					<div class="campo_mask">
						<select id="sel_stato_farm" name="sel_stato_farm">
							<option value="" <?= (empty($stato_sel) ? 'selected' : '') ?>>Tutti</option>
							<option value="1" <?= (!empty($stato_sel) && $stato_sel == 1 ? 'selected' : '') ?>>Non presente</option>
							<option value="2" <?= (!empty($stato_sel) && $stato_sel == 2 ? 'selected' : '') ?>>Presente</option>
							<option value="3" <?= (!empty($stato_sel) && $stato_sel == 3 ? 'selected' : '') ?>>Esaurito</option>
							<option value="5" <?= (!empty($stato_sel) && $stato_sel == 5 ? 'selected' : '') ?>>In scorta</option>
							<option value="4" <?= (!empty($stato_sel) && $stato_sel == 4 ? 'selected' : '') ?>>Non presente in terapia</option>
						</select>
					</div>
				</div>
			</span>

			<table id="table_farmaci" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th width="10%">Lotto</th>
						<th>Nome</th>
						<th width="5%">Dosaggio</th>
						<th width="5%">Scadenza</th>
						<th width="15%">Stato</th>
						<th width="7%" colspan="2">Quantita attuale</th>
						<th width="5%">Giacenza minima</th>
						<th>Reparto</th>
						<th width="5%">Visualizza</th>
						<th width="5%">Modifica</th>
						<td width="5%">Elimina</td>
					</tr>
				</thead>
				<tbody>
					<?
					if ($tot_farmaci > 0) {
						foreach ($lista_farmaci as $farmaco) {
							$status = '';
							$title = '';

							if (!empty($farmaco['quantita_caricata']) && $farmaco['quantita_caricata'] > 0) {
								if ($farmaco['quantita_attuale'] > $farmaco['giacenza_minima']) {
									$status = 'stato_ok';
								} elseif ($farmaco['quantita_attuale'] == $farmaco['giacenza_minima']) {
									$status = 'stato_giallo';
									$title = 'Raggiunta quantita minima';
								} else {
									$status = 'stato_red';
									$title = 'Passata quantita minima';
								}
							} else {
								$status	= 'alert_scadenza';
								$title = 'Quantita non impostata';
							}
					?>
							<tr id="<?= $farmaco['id'] ?>">
								<td><?= $farmaco['lotto'] ?></td>
								<td><?= $farmaco['nome'] ?></td>
								<td><?= $farmaco['dosaggio'] ?></td>
								<td><?= empty($farmaco['data_scadenza']) ? '' : date('d/m/Y', strtotime($farmaco['data_scadenza'])) ?></td>
								<td>
									<? switch ($farmaco['stato_farmaco']) {
										case 1:
											echo 'Non presente';
											break;

										case 2:
											echo 'Presente';
											break;

										case 3:
											echo 'Esaurito';
											break;

										case 4:
											echo 'Non presente in terapia';
											break;

										case 5:
											echo 'In scorta';
											break;

										default:
											echo 'Stato non gestito';
											break;
									}
									?>
								</td>
								<td><?= $farmaco['quantita_attuale'] ?></td>
								<td style="text-align: center;"><img src="images/<?= $status ?>.png" style="max-width: 10px;max-height: 10px;" title="<?= $title ?>"></td>
								<td><?= $farmaco['giacenza_minima'] ?></td>
								<?
								$_sql = "SELECT nome, sede
									FROM reparti
									WHERE id = " . $farmaco['id_reparto'];
								$_rs = mssql_query($_sql, $conn);
								$reparto = mssql_fetch_assoc($_rs);

								mssql_free_result($_rs);

								switch ($reparto['sede']) {
									case 1:
										$reparto['sede'] = SEDE_SERV_AMB;
										break;

									case 2:
										$reparto['sede'] = SEDE_RESIDENZIALE;
										break;

									default:
										$reparto['sede'] = SEDE_NON_GESTITA;
										break;
								}
								?>
								<td class="campo_mask"><?= $reparto['nome'] . ' - ' . $reparto['sede'] ?></td>
								<td><a href="#" onclick="javascript:load_content('div','lista_farmaci.php?do=view_farmaco&id=<?= $farmaco['id'] ?>')"><img src="images/view.png" /></a></td>
								<td><a href="#" onclick="javascript:load_content('div','lista_farmaci.php?do=edit_farmaco&id=<?= $farmaco['id'] ?>')"><img src="images/gear.png" /></a></td>
								<td><a href="#" onclick="javascript:if(confirm('Sei sicuro di voler cancellare il farmaco ?')) del_farmaco('<?= $farmaco['id'] ?>')"><img src="images/remove.png" /></a></td>
							</tr>
							<?
							if (!empty($farmaco['data_scadenza'])) {
								if (strtotime(date('Y-m-d')) > strtotime($farmaco['data_scadenza'])) {
							?>
									<script type="text/javascript">
										var farm_id = <? echo $farmaco['id']; ?>;
										$("#" + farm_id).find('td').css("background-color", "#ff00003d");
									</script>
								<?
								} elseif (strtotime($row['data_scadenza']) <= strtotime(date('Y-m-d') . ' +7 days')) {
								?>
									<script type="text/javascript">
										var farm_id = <? echo $farmaco['id']; ?>;
										$("#" + farm_id).find('td').css("background-color", "##ffff0069");
									</script>
								<?
								}
							}

							if (!empty($farmaco['quantita_attuale']) && $farmaco['quantita_attuale'] <= 0) {
								?>
								<script type="text/javascript">
									var farm_id = <? echo $farmaco['id']; ?>;
									$("#" + farm_id).find('td').css("background-color", "#ff00003d");
								</script>
							<?
							}

							if ($farmaco['stato_farmaco'] == 5) {
							?>
								<script type="text/javascript">
									var farm_id = <? echo $farmaco['id']; ?>;
									$("#" + farm_id).find('td').css("background-color", "rgba(149, 149, 149, 0.16)");
									$("#" + farm_id).find('td').css("color", "#808080de");
								</script>
						<?
							}
						}
					} else { ?>
						<tr>
							<td colspan="12">
								<div class="info" style="margin-left: 0px;">Nessun farmaco registrato.</div>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<? footer_paginazione($tot_farmaci); ?>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$("#table_farmaci").tablesorter({
				widthFixed: true,
				widgets: ['zebra']
			}).tablesorterPager({
				container: $("#pager")
			});

			$("#sel_rep_paz").change(function() {
				$("#layer_nero2").toggle();
				$('#wrap-content').innerHTML = "";

				pagina_da_caricare = "lista_farmaci.php?reparto_sel=" + $("#sel_rep_paz").val() + "&stato_sel=" + $("#sel_stato_farm").val();
				$("#wrap-content").load(pagina_da_caricare, '', function() {
					loading_page('loading');
				});
			});

			$("#sel_stato_farm").change(function() {
				$("#layer_nero2").toggle();
				$('#wrap-content').innerHTML = "";

				pagina_da_caricare = "lista_farmaci.php?reparto_sel=" + $("#sel_rep_paz").val() + "&stato_sel=" + $("#sel_stato_farm").val();
				$("#wrap-content").load(pagina_da_caricare, '', function() {
					loading_page('loading');
				});
			});
		});
	</script>
<?
}


function aggiungi_farmaco()
{
	$conn = db_connect();

	$lista_reparti = array();

	if ($_SESSION['UTENTE']->is_root() || $_SESSION['UTENTE']->is_ds()) {
		$op_filter = '';
	} else {
		$op_filter = ' AND id IN (
			SELECT id_reparto
			FROM reparti_operatori
			WHERE id_operatore = ' . $_SESSION['UTENTE']->get_userid() . '
		)';
	}

	$_sql = "SELECT rp.id, rp.nome, rp.sede
				FROM reparti rp
				WHERE rp.status = 2 AND deleted = 0
				$op_filter";
	$_rs = mssql_query($_sql, $conn);

	while ($_row = mssql_fetch_assoc($_rs)) {
		$lista_reparti[] = $_row;
	}

	mssql_free_result($_rs);
?>
	<script>
		inizializza();
	</script>
	<div id="wrap-content">
		<div class="padding10">
			<div class="logo"><img src="images/re-med-logo.png" /></div>

			<div class="titolo_pag">
				<div class="comandi">
					<div class="indietro indietro_left"><a href="#" onclick="javascript:back_to_list()">indietro</a></div>
					<div>
						<h1>Censimento farmaco</h1>
					</div>
				</div>
			</div>
			<div class="blocco_centralcat">
				<form method="post" name="form0" action="lista_farmaci_POST.php" id="myForm" enctype="multipart/form-data">
					<input type="hidden" name="action" value="create" />
					<input type="hidden" name="from_struttura" value="1" />

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Nome farmaco</div>
							<div class="campo_mask mandatory">
								<input type="text" id="nome" name="nome" class="scrittura_campo" SIZE="50" maxlenght="50" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Lotto</div>
							<div class="campo_mask mandatory">
								<input type="text" id="lotto" name="lotto" class="scrittura_campo" SIZE="30" maxlenght="30" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Dosaggio</div>
							<div class="campo_mask">
								<input type="text" id="dosaggio" name="dosaggio" class="scrittura_campo" pattern="[0-9]+([\.][0-9]+)?" />
							</div>
						</div>
					</div>
					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Data scadenza</div>
							<div class="campo_mask mandatory data_futura">
								<input type="text" name="data_scadenza" id="data_scadenza" class="campo_data scrittura" value="" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Data di carico</div>
							<div class="campo_mask mandatory">
								<input type="text" id="data_carico" name="data_carico" class="campo_data scrittura" value="" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Tipologia somministrazione</div>
							<div class="campo_mask mandatory">
								<select id="tipologia_somministrazione" name="tipologia_somministrazione" class="scrittura">
									<?
									$combo_somm_sql = "SELECT *
							FROM campi_combo
							WHERE idcombo = 250 AND stato = 1 AND cancella = 'n'";
									$_rs = mssql_query($combo_somm_sql, $conn);

									while ($row = mssql_fetch_assoc($_rs)) {
										echo '<option value="' . $row['valore'] . '" >' . $row['etichetta'] . '</option>';
									}

									mssql_free_result($_rs); ?>
								</select>
							</div>
						</div>

					</div>
					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Quantità</div>
							<div class="campo_mask mandatory">
								<input type="text" id="quantita" name="quantita" class="scrittura_campo" pattern="[0-9]+([\.][0-9]+)?" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Giacenza minima</div>
							<div class="campo_mask mandatory">
								<input type="text" id="giacenza_minima" name="giacenza_minima" class="scrittura_campo" pattern="[0-9]+([\.][0-9]+)?" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Stato del farmaco</div>
							<div class="campo_mask mandatory">
								<select id="stato_farmaco" name="stato_farmaco" class="scrittura">
									<option value="1">Non presente</option>
									<option value="2">Presente</option>
									<option value="3">Esaurito</option>
									<option value="5">In scorta</option>
									<option value="4">Non presente in terapia</option>
								</select>
							</div>
						</div>
					</div>
					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Reparto</div>
							<div class="campo_mask mandatory">
								<select id="reparto" class="scrittura" name="reparto">
									<option value="" selected="selected">Seleziona un valore</option>
									<? foreach ($lista_reparti as $reparto) {
										switch ($reparto['sede']) {
											case 1:
												$reparto['sede'] = SEDE_SERV_AMB;
												break;

											case 2:
												$reparto['sede'] = SEDE_RESIDENZIALE;
												break;

											default:
												$reparto['sede'] = SEDE_NON_GESTITA;
												break;
										}
										echo '<option value="' . $reparto['id'] . '" >' . $reparto['nome'] . ' - ' . $reparto['sede'] . '</option>';
									} ?>
								</select>
							</div>
						</div>
					</div>
					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Assegnazione paziente*</div>
							<div class="campo_mask">
								<input type="checkbox" id="associa_pazienti" name="associa_pazienti" />
							</div>
						</div>
						<div class="campo_mask">
							<div class="testo_mask">*Somministrazione d’urgenza autorizzata telefonicamente da medico responsabile a cui seguirà aggiornamento terapia farmacologica entro le successive 24h</div>
						</div>
					</div>

					<div id="pazienti_container" class="riga" style="display: none;">
						<div class="rigo_mask">
							<div class="testo_mask">Elenco pazienti</div>
							<div class="campo_mask">
								<select id="pazienti" name="pazienti[]" class="scrittura" multiple style="width:350px; height: 250px;">
									<?
									$combo_user_sql = "SELECT DISTINCT paz.IdUtente, YEAR(paz.DataNascita) as AnnoNasciata, CONCAT(paz.Cognome, ' ',paz.Nome) as Utente
							FROM utenti as paz
							INNER JOIN (
								select idutente
								from utenti_cartelle
								where idutente not in (605) AND idregime IN (52,53,59,60,61,62,64,65) AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'
							) as paz_cart_inf ON paz_cart_inf.idutente = paz.IdUtente
							WHERE paz.cancella = 'n' AND paz.stato = 1 AND paz.cancella = 'n'
							ORDER BY Utente, AnnoNasciata ASC";
									$_rs = mssql_query($combo_user_sql, $conn);

									while ($row = mssql_fetch_assoc($_rs)) {
										echo '<option value="' . $row['IdUtente'] . '" >' . $row['Utente'] . ' (' . $row['IdUtente'] . ') | Anno ' . $row['AnnoNasciata'] . '</option>';
									}

									mssql_free_result($_rs); ?>
								</select>
							</div>
						</div>
					</div>

					<div class="titolo_pag">
						<div class="comandi">
							<input type="submit" title="salva" value="salva" class="button_salva" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

<?
}


function view_farmaco($id)
{
	$conn = db_connect();
	$_sql = "SELECT *
				FROM farmaci
				WHERE id = $id";
	$_rs = mssql_query($_sql, $conn);

	$farmaco = mssql_fetch_assoc($_rs);

	mssql_free_result($_rs);

	$combo_somm_sql = "SELECT etichetta
						FROM campi_combo
						WHERE idcombo = 250 AND valore = " . $farmaco['tipologia_somministrazione'];
	$_rs = mssql_query($combo_somm_sql, $conn);

	$row = mssql_fetch_assoc($_rs);
	$tipologia_somministrazione = $row['etichetta'];

	mssql_free_result($_rs);
?>
	<div id="wrap-content">
		<div class="padding10">
			<div class="logo"><img src="images/re-med-logo.png" /></div>

			<div class="titolo_pag">
				<div class="comandi">
					<div class="indietro indietro_left"><a href="#" onclick="javascript:back_to_list()">indietro</a></div>
					<div>
						<h1>Dettaglio farmaco</h1>
					</div>
				</div>
			</div>

			<div class="blocco_centralcat">
				<div class="riga">
					<div class="rigo_mask">
						<div class="testo_mask">Nome farmaco</div>
						<div class="campo_mask"><?= $farmaco['nome'] ?></div>
					</div>

					<div class="rigo_mask">
						<div class="testo_mask">Lotto</div>
						<div class="campo_mask"><?= $farmaco['lotto'] ?></div>
					</div>

					<div class="rigo_mask">
						<div class="testo_mask">Dosaggio</div>
						<div class="campo_mask"><?= $farmaco['dosaggio'] ?></div>
					</div>
				</div>

				<div class="riga">
					<div class="rigo_mask">
						<div class="testo_mask">Data scadenza</div>
						<div class="campo_mask"><?= empty($farmaco['data_scadenza']) ? '' : date('d/m/Y', strtotime($farmaco['data_scadenza'])) ?></div>
					</div>

					<div class="rigo_mask">
						<div class="testo_mask">Data di carico</div>
						<div class="campo_mask"><?= empty($farmaco['data_carico']) ? '' : date('d/m/Y', strtotime($farmaco['data_carico'])) ?></div>
					</div>

					<div class="rigo_mask">
						<div class="testo_mask">Tipologia somministrazione</div>
						<div class="campo_mask"><?= $tipologia_somministrazione ?></div>
					</div>
				</div>

				<div class="riga">
					<div class="rigo_mask">
						<div class="testo_mask">Quantità</div>
						<div class="campo_mask"><?= $farmaco['quantita_caricata'] ?></div>
					</div>

					<div class="rigo_mask">
						<div class="testo_mask">Quantità attuale</div>
						<div class="campo_mask"><?= $farmaco['quantita_attuale'] ?></div>
					</div>
					<div class="rigo_mask">
						<div class="testo_mask">Giacenza minima</div>
						<div class="campo_mask"><?= $farmaco['giacenza_minima'] ?></div>
					</div>
				</div>


				<div class="riga">
					<div class="rigo_mask">
						<div class="testo_mask">Stato del farmaco</div>
						<div class="campo_mask">
							<?
							switch ($farmaco['stato_farmaco']) {
								case 1:
									echo 'Non presente';
									break;

								case 2:
									echo 'Presente';
									break;

								case 3:
									echo 'Esaurito';
									break;

								case 4:
									echo 'Non presente in terapia';
									break;

								case 5:
									echo 'In scorta';
									break;

								default:
									echo 'Stato non gestito';
									break;
							}
							?>
						</div>
					</div>
					<div class="rigo_mask">
						<div class="testo_mask">Reparto</div>
						<?
						$_sql = "SELECT nome, sede
									FROM reparti
									WHERE id = " . $farmaco['id_reparto'];
						$_rs = mssql_query($_sql, $conn);
						$reparto = mssql_fetch_assoc($_rs);

						mssql_free_result($_rs);

						switch ($reparto['sede']) {
							case 1:
								$reparto['sede'] = SEDE_SERV_AMB;
								break;

							case 2:
								$reparto['sede'] = SEDE_RESIDENZIALE;
								break;

							default:
								$reparto['sede'] = SEDE_NON_GESTITA;
								break;
						}
						?>
						<div class="campo_mask"><?= $reparto['nome'] . ' - ' . $reparto['sede'] ?></div>
					</div>
					<div class="rigo_mask">
						<div class="testo_mask">Pazienti assegnati</div>
						<div class="campo_mask">
							<?
							$_sql = "SELECT id_paziente
					FROM farmaci_struttura_pazienti
					WHERE id_farmaco = $id";
							$_rs = mssql_query($_sql, $conn);
							$num_pazienti = mssql_num_rows($_rs);
							$counter = 0;

							while ($row = mssql_fetch_assoc($_rs)) {
								$counter++;

								$combo_user_sql = "SELECT Cognome, Nome
								FROM utenti
								WHERE IdUtente = " . $row['id_paziente'];
								$_rs_utente = mssql_query($combo_user_sql, $conn);

								$row_utente = mssql_fetch_assoc($_rs_utente);
								echo $row_utente['Cognome'] . ' ' . $row_utente['Nome'] . ($num_pazienti > $counter ? ' <br> ' : '');

								mssql_free_result($_rs_utente);
							}

							mssql_free_result($_rs); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?
}


function edit_farmaco($id)
{
	$conn = db_connect();
	$_sql = "SELECT *
				FROM farmaci
				WHERE id = $id";
	$_rs = mssql_query($_sql, $conn);

	$farmaco = mssql_fetch_assoc($_rs);

	mssql_free_result($_rs);

	$lista_reparti = array();

	if ($_SESSION['UTENTE']->is_root() || $_SESSION['UTENTE']->is_ds()) {
		$op_filter = '';
	} else {
		$op_filter = ' AND id IN (
			SELECT id_reparto
			FROM reparti_operatori
			WHERE id_operatore = ' . $_SESSION['UTENTE']->get_userid() . '
		)';
	}

	$_sql = "SELECT rp.id, rp.nome, rp.sede
				FROM reparti rp
				WHERE rp.status = 2 AND deleted = 0
				$op_filter";
	$_rs = mssql_query($_sql, $conn);

	while ($_row = mssql_fetch_assoc($_rs)) {
		$lista_reparti[] = $_row;
	}

	mssql_free_result($_rs);

	$_farmaci_paz_sql = "SELECT id_paziente
							FROM farmaci_struttura_pazienti
							WHERE id_farmaco = $id";
	$_farm_paz_rs = mssql_query($_farmaci_paz_sql, $conn);
	$n_pazienti = mssql_num_rows($_farm_paz_rs);

	$lista_paz_farmaco = array();

	while ($row = mssql_fetch_assoc($_farm_paz_rs)) {
		$lista_paz_farmaco[] = $row['id_paziente'];
	}
?>
	<script>
		inizializza();
	</script>
	<div id="wrap-content">
		<div class="padding10">
			<div class="logo"><img src="images/re-med-logo.png" /></div>

			<div class="titolo_pag">
				<div class="comandi">
					<div class="indietro indietro_left"><a href="#" onclick="javascript:back_to_list()">indietro</a></div>
					<div>
						<h1>Censimento farmaco</h1>
					</div>
				</div>
			</div>
			<div class="blocco_centralcat">
				<form method="post" name="form0" action="lista_farmaci_POST.php" id="myForm" enctype="multipart/form-data">
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="id" value="<?= $id ?>" />
					<input type="hidden" name="from_struttura" value="1" />

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Nome farmaco</div>
							<div class="campo_mask mandatory">
								<input type="text" id="nome" name="nome" class="scrittura_campo" SIZE="50" maxlenght="50" value="<?= $farmaco['nome'] ?>" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Lotto</div>
							<div class="campo_mask mandatory">
								<input type="text" id="lotto" name="lotto" class="scrittura_campo" SIZE="30" maxlenght="30" value="<?= $farmaco['lotto'] ?>" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Dosaggio</div>
							<div class="campo_mask">
								<input type="text" id="dosaggio" name="dosaggio" class="scrittura_campo" pattern="[0-9]+([\.][0-9]+)?" value="<?= $farmaco['dosaggio'] ?>" />
							</div>
						</div>
					</div>

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Data scadenza</div>
							<div class="campo_mask mandatory">
								<input type="text" name="data_scadenza" id="data_scadenza" class="campo_data scrittura" value="<?= empty($farmaco['data_scadenza']) ? '' : date('d/m/Y', strtotime($farmaco['data_scadenza'])) ?>" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Data di carico</div>
							<div class="campo_mask mandatory">
								<input type="text" id="data_carico" name="data_carico" class="campo_data scrittura" value="<?= empty($farmaco['data_carico']) ? '' : date('d/m/Y', strtotime($farmaco['data_carico'])) ?>" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Tipologia somministrazione</div>
							<div class="campo_mask mandatory">
								<select id="tipologia_somministrazione" name="tipologia_somministrazione" class="scrittura">
									<?
									$combo_somm_sql = "SELECT *
							FROM campi_combo
							WHERE idcombo = 250 AND stato = 1 AND cancella = 'n'";
									$_rs = mssql_query($combo_somm_sql, $conn);

									while ($row = mssql_fetch_assoc($_rs)) {
										echo '<option ' . ($farmaco['tipologia_somministrazione'] == $row['valore'] ? 'selected' : '') . ' value="' . $row['valore'] . '" >' . $row['etichetta'] . '</option>';
									}

									mssql_free_result($_rs); ?>
								</select>
							</div>
						</div>
					</div>

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Quantità</div>
							<div class="campo_mask mandatory">
								<input type="text" id="quantita" name="quantita" class="scrittura_campo" value="<?= $farmaco['quantita_caricata'] ?>" pattern="[0-9]+([\.][0-9]+)?" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Quantità attuale</div>
							<div class="campo_mask mandatory">
								<input type="text" id="quantita_attuale" name="quantita_attuale" class="scrittura_campo" value="<?= $farmaco['quantita_attuale'] ?>" pattern="[0-9]+([\.][0-9]+)?" />
							</div>
						</div>
						<div class="rigo_mask">
							<div class="testo_mask">Giacenza minima</div>
							<div class="campo_mask mandatory">
								<input type="text" id="giacenza_minima" name="giacenza_minima" class="scrittura_campo" value="<?= $farmaco['giacenza_minima'] ?>" pattern="[0-9]+([\.][0-9]+)?" />
							</div>
						</div>
					</div>

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Stato del farmaco</div>
							<div class="campo_mask mandatory">
								<select id="stato_farmaco" name="stato_farmaco" class="scrittura">
									<option value="1" <?= $farmaco['stato_farmaco'] == 1 ? 'selected' : '' ?>>Non presente</option>
									<option value="2" <?= $farmaco['stato_farmaco'] == 2 ? 'selected' : '' ?>>Presente</option>
									<option value="3" <?= $farmaco['stato_farmaco'] == 3 ? 'selected' : '' ?>>Esaurito</option>
									<option value="5" <?= $farmaco['stato_farmaco'] == 5 ? 'selected' : '' ?>>In scorta</option>
									<option value="4" <?= $farmaco['stato_farmaco'] == 4 ? 'selected' : '' ?>>Non presente in terapia</option>
								</select>
							</div>
						</div>
						<div class="rigo_mask">
							<div class="testo_mask">Reparto</div>
							<div class="campo_mask mandatory">
								<select id="reparto" class="scrittura" name="reparto">
									<?
									$id_reparto_sel = '';
									$id_sede_reparto_sel = '';
									foreach ($lista_reparti as $reparto) {
										if (!empty($farmaco['id_reparto']) && $farmaco['id_reparto'] == $reparto['id']) {
											$id_reparto_sel = $reparto['id'];
											$id_sede_reparto_sel = $reparto['sede'];
										}

										switch ($reparto['sede']) {
											case 1:
												$reparto['sede'] = SEDE_SERV_AMB;
												break;

											case 2:
												$reparto['sede'] = SEDE_RESIDENZIALE;
												break;

											default:
												$reparto['sede'] = SEDE_NON_GESTITA;
												break;
										}

										echo '<option value="' . $reparto['id'] . '" ' . (!empty($farmaco['id_reparto']) && $farmaco['id_reparto'] == $reparto['id'] ? 'selected' : '') . ' >' . $reparto['nome'] . ' - ' . $reparto['sede'] . '</option>';
									} ?>
								</select>
							</div>
						</div>
					</div>
					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Assegnazione paziente*</div>
							<div class="campo_mask">
								<input type="checkbox" id="associa_pazienti" name="associa_pazienti" <?= $n_pazienti > 0 ? 'checked' : '' ?> />
							</div>
						</div>

						<div class="campo_mask">
							<div class="testo_mask">*Somministrazione d’urgenza autorizzata telefonicamente da medico responsabile a cui seguirà aggiornamento terapia farmacologica entro le successive 24h</div>
						</div>
					</div>

					<div id="pazienti_container" <?= $n_pazienti > 0 ? "" : "style='display: none;'" ?>>
						<div class="rigo_mask">
							<div class="testo_mask">Elenco pazienti</div>
							<div class="campo_mask">
								<select id="pazienti" name="pazienti[]" class="scrittura" multiple style="width:350px; height: 250px;">
									<?
									$_user_sql = "SELECT DISTINCT paz.IdUtente, YEAR(paz.DataNascita) as AnnoNasciata, CONCAT(paz.Cognome, ' ',paz.Nome) as Utente, rep_paz.id_reparto
						FROM utenti as paz
						INNER JOIN (
							select idutente
							from utenti_cartelle
							where idutente not in (605) AND idregime IN (52,53,59,60,61,62,64,65) AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'
						) as paz_cart_inf ON paz_cart_inf.idutente = paz.IdUtente
						INNER JOIN (
							SELECT DISTINCT rep_paz.id_paziente, rep_paz.id_reparto
							FROM reparti_pazienti rep_paz
							INNER JOIN reparti rep ON rep.id = rep_paz.id_reparto
							WHERE rep.deleted = 0 AND rep.status = 2 AND sede = $id_sede_reparto_sel
						) as rep_paz ON rep_paz.id_paziente = paz.IdUtente
						WHERE paz.cancella = 'n' AND paz.stato = 1 AND paz.cancella = 'n'
						ORDER BY Utente, AnnoNasciata ASC";
									$_rs_users = mssql_query($_user_sql, $conn);

									while ($row = mssql_fetch_assoc($_rs_users)) {
										if ($id_reparto_sel == $row['id_reparto']) {
											$symbol = '&#8226;';
										}

										echo '<option value="' . $row['IdUtente'] . '" ' . (in_array($row['IdUtente'], $lista_paz_farmaco) ? 'selected' : '') . ' >' .
											$row['Utente'] . ' (' . $row['IdUtente'] . ') | Anno ' . $row['AnnoNasciata'] . ' ' . $symbol .
											'</option>';
									}

									mssql_free_result($_rs); ?>
								</select>
							</div>
						</div>
					</div>

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Scarico farmaco*</div>
							<div class="campo_mask">
								<input type="checkbox" id="scarico_farmaco" name="scarico_farmaco" />
							</div>
						</div>

						<div class="campo_mask">
							<div class="testo_mask">*Lo scarico permette di registrare la rimozione di farmaci dall'anagrafica, l'azione non è reversibile.</div>
						</div>
					</div>

					<div id="scarico_farmaco_container" style="display: none;">
						<div class="rigo_mask">
							<div class="testo_mask">Quantita da scartare</div>
							<div class="campo_mask">
								<input type="text" id="qnt_scarico" name="qnt_scarico" class="scrittura_campo solo_numeri" value="" />
							</div>
						</div>

						<div class="rigo_mask">
							<div class="testo_mask">Note di scarico</div>
							<div class="campo_mask">
								<textarea id="note_scarico" name="note_scarico" maxlength="200" style="width: 100%;" class="scrittura_campo"></textarea>
							</div>
						</div>
					</div>

					<div class="titolo_pag">
						<div class="comandi">
							<input type="submit" title="salva" value="salva" class="button_salva" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

<?
}

?>

<script>
	// ####################################################################################################
	// ######################################## UTILITY FUNCTIONS #########################################
	// ####################################################################################################

	$(document).ready(function() {
		$(".solo_numeri").validation({
			type: "int"
		});

		$(".solo_numeri_dec").validation({
			type: "float"
		});

		$(".campo_data").mask("99/99/9999");

		$("#associa_pazienti").change(function(e) {
			if (e.currentTarget.checked) {
				$("#pazienti_container").css("display", "");
			} else {
				$("#pazienti_container").css("display", "none");
				$('[name="pazienti[]"]').find('option').attr('selected', '');
			}
		});

		$("#scarico_farmaco").change(function(e) {
			if (e.currentTarget.checked) {
				$("#scarico_farmaco_container").css("display", "");
				$('#qnt_scarico').parent().addClass('mandatory');
				$('#note_scarico').parent().addClass('mandatory');
			} else {
				$("#scarico_farmaco_container").css("display", "none");
				$('#qnt_scarico').parent().removeClass('mandatory');
				$('#note_scarico').parent().removeClass('mandatory');
				$('#qnt_scarico').val('');
				$('#note_scarico').val('');
			}
		});

		$("#reparto").change(function(e) {
			$.ajax({
				type: "POST",
				url: "ajax_refresh_lista_pazienti_reparti_farmaci.php",
				data: {
					"id_reparto": $("#reparto").val(),
					"id_farmaco": '<?php echo $id ?>',
				},
				success: function(response) {
					if (response != null && response != 'null') {
						response = JSON.parse(response);
						var options = '';
						$("#pazienti").empty();

						Object.keys(response).forEach(idx => {
							var symbol = '';

							if ($("#reparto").val() == response[idx]['id_reparto']) {
								symbol = '&#8226;';
							}

							$("#pazienti").append("<option value='" + response[idx]['IdUtente'] + "' " + (response[idx]['associato'] ? 'selected' : '') + " >" +
								response[idx]['Utente'] + " (" + response[idx]['IdUtente'] + ") | Anno " + response[idx]['AnnoNasciata'] + " " + symbol +
								"</option>");
						});
					}
				}
			});
		});
	});

	function back_to_list() {
		$("#wrap-content").load("lista_farmaci.php", '', function() {
			loading_page('loading');
		});
	}

	function del_farmaco(id) {
		$.ajax({
			type: "POST",
			url: "lista_farmaci_POST.php",
			data: "action=delete&id=" + id,
			success: function(res) {
				if (res == 'true') {
					$("#wrap-content").load("lista_farmaci.php", '', function() {
						loading_page('loading');
					});
				} else
					alert('Errore nella cancellazione del farmaco');
			},
			error: function(request, error) {
				alert('Errore nella cancellazione del farmaco');
			}
		});
	}
</script>