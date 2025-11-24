<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 4;
$tablename = 'utenti';
include_once('include/function_page.php');

function show_list()
{


	$conn = db_connect();

	$query = "SELECT * from re_normative_regimi order by normativa ASC";
	$rs = mssql_query($query, $conn);

	if (!$rs) error_message(mssql_error());

	$conta = mssql_num_rows($rs);

	//pagina_new();
?>
	<script>
		inizializza();
	</script>
	<div id="wrap-content">
		<div class="padding10">
			<div class="logo"><img src="images/re-med-logo.png" /></div>

			<div id="briciola" style="margin-bottom:20px;">
				<div class="elem0"><a href="#">gestione moduli</a></div>
				<div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli_regimi.php');" href="#">elenco moduli</a></div>

			</div>

			<div class="titolo_pag">
				<div class="comandi">

				</div>
			</div>
			<?
			if ($conta == 0) {
			?>

				<div class="titoloalternativo">
					<h1>Elenco Moduli</h1>
				</div>
				<div class="info">Non esistono moduli associati a regimi.</div>


			<?
				exit();
			} ?>

			<table id="table" class="tablesorter" cellspacing="1">
				<thead>
					<tr>


						<th>normativa</th>
						<th>regime</th>
						<td>modifica</td>



					</tr>
				</thead>
				<tbody>

					<?

					while ($row = mssql_fetch_assoc($rs)) {
						$id = $row['idregime'];
						$normativa = $row['normativa'];
						$regime = $row['regime'];
					?>
						<tr>
							<td><?= $normativa ?></td>
							<td><?= $regime ?></td>
							<td><a id="<?= $id ?>" onclick="javascript:load_content('div','lista_moduli_regimi.php?do=edit&id=<?= $id ?>');" href="#"><img src="images/gear.png" /></a></td>
						</tr>
					<?
					}

					?>


				</tbody>
			</table>
			<?
			footer_paginazione($conta);
			?>
		</div>
	</div>


	<script>
		$(document).ready(function() {

			$("table").tablesorter({
				widthFixed: true,
				widgets: ['zebra']
			}).tablesorterPager({
				container: $("#pager")
			});
		});
	</script>


	<script type="text/javascript" id="js">
		function change_tab_edit(iddiv) {
			//$('#container-4').triggerTab(3); 
			//$('#container-4').tabs('add', 'index.php', 'New Tab', 1);
			//$('#container-4').tabs('add', '#new-tab', 'New Tab');
			//$('#container-4 > #tab_block').append('<li><a href="re_pazienti_amministrativa.php?do=add&id=10"><span>antonio esposito</span></a></li>');
			//$('#container-4').find('#tab_block > li >a')..end().tabs();
			$("#layer_nero2").toggle();
			$('#lista_impegnative').innerHTML = "";
			pagina_da_caricare = "re_pazienti_amministrativa_edita_impegnativa.php?do=edit&id=" + iddiv;
			$("#lista_impegnative").load(pagina_da_caricare, '', function() {
				loading_page('loading');
				$("#container-4 .tabs-selected").removeClass('tabs-selected');
			});


		}
	</script>

<?
	//footer_new();

}

function edit($id)
{

	$idregime = $id;
	$conn = db_connect();
	$query = "SELECT * from re_normative_regimi WHERE idregime=$idregime";
	$rs = mssql_query($query, $conn);

	$row = mssql_fetch_assoc($rs);
	$id = $row['idregime'];
	$normativa = $row['normativa'];
	$regime = $row['regime'];
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
	<script type="text/javascript">
		var popupStatus = 0;

		function conf_cancella(id) {
			if (confirm('sei sicuro di voler cancellare il modulo corrente?')) {
				//document.getElementById('id_allegato').value=id;
				$.ajax({
					type: "POST",
					url: "ajax_moduli.php?id_modulo=" + id,
					data: "",
					success: function(msg) {
						//alert( "Data Saved:"+$.trim(msg)+"1" );
						if ($.trim(msg) == "") {
							$.ajax({
								type: "POST",
								url: "lista_moduli_POST.php?action=del_modulo&id_modulo=" + id,
								data: "",
								success: function(msg) {
									//alert( "Data Saved: " + msg );
									reload_moduli();
								}
							});
						} else {
							alert('Impossibile cancellare il modulo in quanto risulta presente in cartelle pianificate e/o associato a regimi');
							return false;
						}

					}
				});
			} else return false;
		}

		function reload_moduli() {
			$("#layer_nero2").toggle();
			$('#wrap-content').innerHTML = "";
			pagina_da_caricare = "lista_moduli.php";
			$("#wrap-content").load(pagina_da_caricare, '', function() {
				loading_page('loading');
				//$("#container-4 .tabs-selected").removeClass('tabs-selected');
			});
		}

		//loading popup with jQuery magic!
		function loadPopup(id) {
			//loads popup only if it is disabled
			if (popupStatus == 0) {
				$("#backgroundPopup").css({
					"opacity": "0.7"
				});
				$("#popupdati2").load('carica_preview.php?&do=&id=' + id, function() {
					centerPopup();
					$("#backgroundPopup").fadeIn("slow");
					$("#popupdati2").fadeIn("slow");
				});
				popupStatus = 1;
			}
		}


		//disabling popup with jQuery magic!
		function disablePopup() {
			//disables popup only if it is enabled
			if (popupStatus == 1) {
				$("#backgroundPopup").fadeOut("slow");
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

			$("#backgroundPopup").css({
				"height": windowHeight
			});

		}

		function prev(id) {
			//centering with css
			loadPopup(id);

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
				//loadPopup();
			});

			//CLOSING POPUP
			//Click the x event!
			$(".popupdatiClose").click(function() {
				disablePopup();
			});
			//Click out event!
			$("#backgroundPopup").click(function() {
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
	<div id="popupdati2"></div>
	<div id="backgroundPopup"></div>
	<div class="padding10">
		<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
			<div class="elem0"><a href="principale.php">home</a></div>
			<div class="elem_pari"><a onclick="javascript:load_content('div','lista_moduli_regimi.php');" href="#">elenco regimi</a></div>
			<div class="elem_dispari"><a href="#">elenco moduli regimi</a></div>
		</div>
		<div class="titoloalternativo">
			<h1>Elenco moduli associati al regime: <?= $normativa ?> <?= $regime ?></h1>
		</div>

		<form id="myForm" method="post" name="form0" action="lista_moduli_regimi_POST.php">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="id" value="<?= $id ?>" />
			<div class="nomandatory">
				<input type="hidden" name="nomandatory" />
			</div>

			<table style="clear:both;" id="elenco" class="tablesorter" cellspacing="1">
				<thead>

					<tr class="riga_tab nodrag nodrop">
						<th width="10%" align="left">codice sgq</th>
						<th align="left">ant.</th>
						<th width="30%" align="left">nome</th>
						<th width="5%" align="left">previsto</th>
						<th width="2%">tipo</th>
						<th width="5%" align="left">obbligatorio</th>
						<th width="15%" align="left">figura responsabile</th>
						<th width="5%" align="left">scadenza</th>
						<th width="10%" align="left">scad. ciclica</th>
						<th width="10%" align="left">scad. trattamenti</th>
						<th width="20%" align="left">raggruppamento</th>
					</tr>
				</thead>
				<tbody id="tbody">
					<?
					$arr_medici[] = array();
					$conn = db_connect();
					$query = "SELECT nome, uid from medici_operatori ORDER BY nome asc";
					$rs1 = mssql_query($query, $conn);
					$i = 0;
					while ($row = mssql_fetch_assoc($rs1)) {
						$arr_medici[$i] = $row;
						$i++;
					}

					mssql_free_result($rs1);

					$query = "SELECT idmodulo from re_moduli_regimi where idregime=$idregime order by id, raggruppamento asc";
					$ordinamento = "";
					$arr_moduli_id = array();
					$rs1 = mssql_query($query, $conn);
					if (!$rs1) error_message(mssql_error());
					while ($row1 = mssql_fetch_assoc($rs1)) {
						array_push($arr_moduli_id,  $row1['idmodulo']);
					}
					$query = "SELECT idmodulo,raggruppamento from re_moduli_regimi_ass order by raggruppamento desc,idmodulo asc";
					$rs1 = mssql_query($query, $conn);
					if (!$rs1) error_message(mssql_error());
					$conta = mssql_num_rows($rs1);
					$i = 1;
					while ($row1 = mssql_fetch_assoc($rs1)) {
						//echo($row1['idmodulo']);
						if (!(in_array($row1['idmodulo'], $arr_moduli_id))) array_push($arr_moduli_id,  $row1['idmodulo']);
					}
					//print_r ($arr_moduli_id);

					foreach ($arr_moduli_id as $value) {
						//$idmodulo=$row1['idmodulo'];
						$idmodulo = $value;
						$query = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc, nome asc";
						//echo($query);	
						$rs = mssql_query($query, $conn);

						if (!$rs) error_message(mssql_error());

						while ($row = mssql_fetch_assoc($rs)) {

							$nome = $row['nome'];
							$codice = $row['codice'];
							$disuso = $row['disuso'];
							$replica = $row['replica'];
							switch ($replica) {
								case "0":
									$replica = "Un/Car";
									break;
								case "1":
									$replica = "Mu/Car";
									break;
								case "2":
									$replica = "Un/Imp";
									break;
								case "3":
									$replica = "Mu/Imp";
									break;
							}


							if ($i > 2) {
								$style = "style=\"display:none\"";
								$class = "riga_sottolineata_diversa hidden";
								$dis = "disabled";
							} else {
								$nodrag = "nodrag=false";
								$style = '';
								$class = "riga_sottolineata_diversa hidden";
								$dis = "";
							}

							$previsto = "n";
							$scad_flg = "n";
							$obbligatorio = "n";
							$scadenza = "";
							$trattamenti = "";
							$raggruppamento = "";

							$query3 = "SELECT * FROM regimi_moduli where idmodulo=$idmodulo and idregime=$idregime";

							$rs3 = mssql_query($query3, $conn);

							if (!$rs3) error_message(mssql_error());

							if (mssql_num_rows($rs3) > 0) {
								if ($row3 = mssql_fetch_assoc($rs3)) {
									$previsto = "s";
									$obbligatorio = trim($row3['obbligatorio']);
									$scad_flg = trim($row3['scad_flg']);
									$scadenza = trim($row3['scadenza']);
									//$trattamenti=formatta_data(trim($row3['trattamenti']));
									$trattamenti = $row3['trattamenti'];
									$raggruppamento = $row3['raggruppamento'];
									if ($disuso) $nome = "<-- IN DISUSO --> " . $nome;
								}
							} else {
								$previsto = "n";
								$obbligatorio = "n";
								$scadenza = "";
								$trattamenti = "";
								$scad_flg = "n";
								$raggruppamento = "";
							}
							//if($trattamenti=="01/01/1900") $trattamenti="";		
							$query4 = "SELECT TOP (1) id_modulo, id_regime, status, cancella, id, id_operatore, id_cartella FROM dbo.moduli_medici
						WHERE (status = 1) AND (cancella = 'n')AND (id_cartella = 0) AND (id_modulo = $idmodulo) AND (id_regime = $idregime) ORDER BY id DESC";
							//echo($query4);
							$rs4 = mssql_query($query4, $conn);
							$medico = "";
							if ($row4 = mssql_fetch_assoc($rs4)) $medico = $row4['id_operatore'];
							mssql_free_result($rs4);

							if ($trattamenti > 0)
								$trattamenti = $trattamenti;
							else
								$trattamenti = "";

							if (($previsto == 's') or (($previsto == 'n') and (!$disuso))) {
					?>

								<tr id="<?= $i ?>" class="<?= $class ?>">

									<td align="left"><span class="id_notizia_cat"><input type="hidden" name="idmodulo<?= $i ?>" value="<?= $idmodulo ?>" /><?= $codice ?></span></td>
									<?
									$query = "SELECT id, idmodulo from moduli where idmodulo=$idmodulo order by id desc";
									//echo($query);	
									$rs_id = mssql_query($query, $conn);
									$row = mssql_fetch_assoc($rs_id);
									$id_mod_vers = $row['id'];
									?>
									<td>
										<div id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev('<?= $id_mod_vers ?>');" title="anteprima modulo"><img src="images/view.png" /></a></div>
									</td>
									<td align="left"><span class="id_notizia_cat"><?= $nome ?></span></td>
									<td align="left">
										<select name="previsto<?= $i ?>" onclick="javascript:manMedici(this.value,<?= $i ?>);">
											<option value="s" <? if ($previsto == 's') echo ("selected"); ?> onclick="javascript:$('#medico_vis<?= $i ?>').slideDown();$('#default_vis<?= $i ?>').slideDown();$('#scad_flg_vis<?= $i ?>').slideDown();document.getElementById('medico<?= $i ?>').disabled=false;">Si</option>
											<option value="n" <? if ($previsto == 'n') echo ("selected"); ?> onclick="javascript:$('#medico_vis<?= $i ?>').slideUp();$('#default_vis<?= $i ?>').slideUp();$('#scad_flg_vis<?= $i ?>').slideUp();">No</option>
										</select>
									</td>
									<td align="left"><?= $replica ?></td>
									<td align="left">
										<?php if (($previsto == 's') or ($previsto == '')) { ?>
											<div id="default_vis<?= $i ?>" style="display:block;">
											<? } else { ?>
												<div id="default_vis<?= $i ?>" style="display:none;"><? } ?>
												<select name="default<?= $i ?>">
													<option value="s" <? if ($obbligatorio == 's') echo ("selected"); ?>>Si</option>
													<option value="n" <? if ($obbligatorio == 'n') echo ("selected"); ?>>No</option>
													<!-- Modifica MIO e LTO
									OLD:
										<option value="f" <!?if ($obbligatorio=='f') echo("selected");?>>Pianificato</option>
								-->
													<option value="p" <? if ($obbligatorio == 'p') echo ("selected"); ?>>Pianificato</option>
												</select>
												</div>
									</td>
									<td align="left">
										<?php if (($previsto == 's') or ($previsto == '')) {
										?>
											<div id="medico_vis<?= $i ?>" style="display:block;" class="nomandatory">
											<?
											//echo($medico);
										} else {
											$medico = "";
											?>
												<div id="medico_vis<?= $i ?>" style="display:none;" class="nomandatory"><? } ?>
												<select id="medico<?= $i ?>" name="medico<?= $i ?>" style="width:150px">
													<option value="" selected>Seleziona una figura</option>
													<?php
													for ($y = 0; $y < sizeof($arr_medici); $y++) {
														echo ("<option value='" . $arr_medici[$y]['uid'] . "'");
														if ($medico == $arr_medici[$y]['uid']) echo ("selected");
														echo (">" . $arr_medici[$y]['nome'] . "</option>");
													}
													?>
												</select>
												</div>
									</td>
									<td align="left">
										<?php if (($previsto == 's') or ($previsto == '')) { ?>
											<div id="scad_flg_vis<?= $i ?>" style="display:block;">
											<? } else { ?>
												<div id="scad_flg_vis<?= $i ?>" style="display:none;"><? } ?>
												<select name="scad_flg<?= $i ?>">
													<option value="n" <? if ($scad_flg == 'n') echo ("selected"); ?> onclick="javascript:$('#scadenza_vis<?= $i ?>').slideUp();$('#trattamenti_vis<?= $i ?>').slideUp();">No</option>
													<option value="c" <? if ($scad_flg == 'c') echo ("selected"); ?> onclick="javascript:$('#scadenza_vis<?= $i ?>').slideDown();$('#trattamenti_vis<?= $i ?>').slideUp();">Ciclica</option>
													<option value="t" <? if ($scad_flg == 't') echo ("selected"); ?> onclick="javascript:$('#scadenza_vis<?= $i ?>').slideUp();$('#trattamenti_vis<?= $i ?>').slideDown();">Trattamenti</option>
												</select>
												</div>
									</td>

									<td align="left">
										<?php if ((($previsto == 's') or ($previsto == '')) and (($scad_flg == 'c') or ($scad_flg == ''))) { ?>
											<div id="scadenza_vis<?= $i ?>" style="display:block;" class="nomandatory integer">
												<input id="scad<?= $i ?>" type="text" name="scadenza<?= $i ?>" value="<?= $scadenza ?>" />
											<? } else { ?>
												<div id="scadenza_vis<?= $i ?>" style="display:none;" class="nomandatory integer">
													<input id="scad<?= $i ?>" type="text" name="scadenza<?= $i ?>" value="<?= $scadenza ?>" />
												<? } ?>
												</div>
									</td>
									<td align="left">
										<?php if ((($previsto == 's') or ($previsto == '')) and (($scad_flg == 't') or ($scad_flg == ''))) { ?>
											<div id="trattamenti_vis<?= $i ?>" style="display:block;" class="nomandatory integer">
												<input id="trat<?= $i ?>" type="text" name="trattamenti<?= $i ?>" value="<?= $trattamenti ?>" />
											<? } else { ?>
												<div id="trattamenti_vis<?= $i ?>" style="display:none;" class="nomandatory integer">
													<input id="trat<?= $i ?>" type="text" name="trattamenti<?= $i ?>" value="<?= $trattamenti ?>" />
												<? } ?>
												</div>
									</td>
									<td align="left">
										<div id="raggruppamento<?= $i ?>" class="nomandatory">
											<select name="raggruppamento<?= $i ?>" style="width:100px;">
												<option value="modulo" <? if ($raggruppamento == 'modulo') echo ("selected"); ?>>per modulo</option>
												<option value="cronologia" <? if ($raggruppamento == 'cronologia') echo ("selected"); ?>>per cronologia</option>
											</select>
										</div>
									</td>
								</tr>
					<?
								if ($i == 0)
									$ordinamento .= $i;
								else
									$ordinamento .= $i . " ";
								$i++;
							}
						}
					}
					?>
				</tbody>
			</table>


			<input id="debug" type="hidden" name="debug" value="<?= $ordinamento ?>">
			<div class="titolo_pag">
				<div class="comandi">

					<input type="submit" title="salva" value="salva" class="button_salva" />
				</div>
			</div>

		</form>



	</div>
	</div>
	<script type="text/javascript" language="javascript">
		$(document).ready(function() {
			$.mask.addPlaceholder('~', "[+-]");
			$(".campo_data").mask("99/99/9999");

		});
	</script>
<?

	//footer_new();

}






if (isset($_SESSION['UTENTE'])) {


	if (!isset($do)) $do = '';
	$back = "main.php";

	// verifica i permessi
	if (in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";


		switch ($_POST['action']) {



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

			case "edit":
				// verifica i permessi..
				if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
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
	} else {
		error_message("non hai i permessi per visualizzare questa pagina!");
		go_home();
	}
} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>