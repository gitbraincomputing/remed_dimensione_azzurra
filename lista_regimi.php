<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 39;
$tablename = 'regime';
include_once('include/function_page.php');

function show_list()
{

	//$nome_pagina="gestione normative";
	//header_new($nome_pagina);
	//barra_top_new();
	//top_message();
	//menu_new();
	//pagina_new();

	//$conn = db_connect();

	//$query = "SELECT * from regime where (stato=1) and (cancella='n')order by idregime desc";	
	//$rs1 = mssql_query($query, $conn);

	//if(!$rs1) error_message(mssql_error());

	//$conta=mssql_num_rows($rs1);
?>
	<script>
		function conf_cancella(id) {
			if (confirm('sei sicuro di voler cancellare il regime corrente?')) {
				//document.getElementById('id_allegato').value=id;
				$.ajax({
					type: "POST",
					url: "lista_regimi_POST.php?action=del_regime&id_regime=" + id,
					data: "",
					success: function(msg) {
						//alert( "Data Saved: " + msg );
						reload_tipologie();
					}
				});
			} else return false;
		}

		function reload_tipologie() {
			$("#layer_nero2").toggle();
			$('#wrap-content').innerHTML = "";
			pagina_da_caricare = "lista_regimi.php";
			$("#wrap-content").load(pagina_da_caricare, '', function() {
				loading_page('loading');
				//$("#container-4 .tabs-selected").removeClass('tabs-selected');
			});
		}
	</script>
	<div id="wrap-content">
		<div class="padding10">
			<div class="logo"><img src="images/re-med-logo.png" /></div>

			<div id="briciola" style="margin-bottom:20px;">
				<div class="elem0"><a href="#">gestione ASL</a></div>
				<div class="elem_pari"><a href="#">elenco regimi</a></div>

			</div>


			<div id="ricerca"></div>

			<div class="titolo_pag">
				<div class="comandi">
					<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_regimi.php?do=add');">aggiungi regime</a></div>
				</div>
			</div>

			<?
			$conn = db_connect();

			$query = "SELECT r.idregime, r.idnormativa, r.regime, r.stato, r.cancella, n.normativa
						FROM regime r
						INNER JOIN normativa n ON n.idnormativa = r.idnormativa 
						where r.cancella='n' and r.stato = 1 and n.cancella='n' and n.stato = 1
						order by r.idregime asc";
			$rs = mssql_query($query, $conn);
			$conta = mssql_num_rows($rs);

			if (!$rs) error_message(mssql_error());
			if (mssql_num_rows($rs) == 0) {
			?>

				<div class="titoloalternativo">
					<h1>Elenco Regimi</h1>
				</div>
				<div class="info">Non esistiono regimi.</div>


			<?
				exit();
			} ?>

			<table id="table" class="tablesorter" cellspacing="1">
				<thead>
					<tr>
						<th>id</th>
						<th>regime</th>
						<th>normativa</th>
						<td width="5%">modifica</td>
						<td width="5%">cancella</td>
					</tr>
				</thead>
				<tbody>
					<?
					//while($row1 = mssql_fetch_assoc($rs1))   
					//{

					while ($row = mssql_fetch_assoc($rs)) {

						$idregime = $row['idregime'];
						$regime = pulisci_lettura($row['regime']);
						$normativa = $row['normativa'];
					?>

						<tr>
							<td><?= $idregime ?></td>
							<td><?= $regime ?></td>
							<td><?= $normativa ?></td>
							<td><a id="<?= $id ?>" onclick="javascript:load_content('div','lista_regimi.php?do=edit&id=<?= $idregime ?>');" href="#"><img src="images/gear.png" /></a></td>
							<td><a id="<?= $id ?>" onclick="conf_cancella('<?= $idregime ?>');" href="#"><img src="images/remove.png" /></a></td>
						</tr>

					<?
						//}
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

function add()
{

	//$nome_pagina="gestione normative";
	//header_new($nome_pagina);
	//barra_top_new();
	//top_message();
	//menu_new();
	//pagina_new();
?>
	<script>
		inizializza();
	</script>
	<div id="wrap-content">
		<div class="padding10">
			<div class="logo"><img src="images/re-med-logo.png" /></div>

			<div id="briciola" style="margin-bottom:20px;">
				<div class="elem0"><a href="principale.php">home</a></div>
				<div class="elem_pari"><a onclick="javascript:load_content('div','lista_regimi.php');" href="#">elenco regimi</a></div>
				<div class="elem_dispari"><a href="#">aggiungi regime</a></div>

			</div>

			<form method="post" name="form0" action="lista_regimi_POST.php" id="myForm">
				<input type="hidden" value="create" name="action" />
				<input type="hidden" value="1" name="op" />

				<div class="titolo_pag">
					<h1>Aggiungi Regime</h1>
				</div>

				<div class="blocco_centralcat">

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Regime</div>
							<div class="campo_mask mandatory">
								<input type="text" name="regime" class="scrittura" size="50" />
							</div>
						</div>
					</div>
					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">normativa</div>
							<div class="campo_mask mandatory">
								<select id="normativa" class="scrittura" name="normativa">
									<?php
									$conn = db_connect();
									print('<option value="">Selezionare una normativa</option>' . "\n");
									$query = "SELECT normativa, idnormativa, stato, cancella FROM dbo.normativa WHERE (stato = 1) AND (cancella = 'n')";
									$rs1 = mssql_query($query, $conn);
									while ($row = mssql_fetch_assoc($rs1)) {
										$idnormativa = $row['idnormativa'];
										$normativa = $row['normativa'];

										print('<option value="' . $idnormativa . '"');
										print('>' . $normativa . '</option>' . "\n");
									}
									?>
								</select>
							</div>
						</div>

					</div>

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">responsabile</div>
							<div class="campo_mask mandatory">
								<select name="responsabile">
									<option value="">seleziona</option>
									<option value="0">direttore sanitario</option>
									<option value="1">direttore tecnico</option>
								</select>
							</div>
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
<?
	//footer_new();

}

function edit($id)
{

	$conn = db_connect();

	$query = "SELECT dbo.regime.idregime, dbo.regime.idnormativa, dbo.regime.regime, dbo.regime.stato, dbo.regime.cancella,dbo.regime.responsabile, dbo.normativa.normativa as normativa
FROM dbo.regime INNER JOIN dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa WHERE idregime=$id";
	$rs = mssql_query($query, $conn);
	if (!$rs) error_message(mssql_error());

	if ($row = mssql_fetch_assoc($rs)) {

		$idregime = $row['idregime'];
		$regime = pulisci_lettura($row['regime']);
		$normativa = pulisci_lettura($row['normativa']);
		$responsabile = pulisci_lettura($row['responsabile']);
	}

	mssql_free_result($rs);
?>
	<script>
		inizializza();
	</script>
	<div id="wrap-content">
		<div class="padding10">
			<div class="logo"><img src="images/re-med-logo.png" /></div>

			<div id="briciola" style="margin-bottom:20px;">
				<div class="elem0"><a href="principale.php">home</a></div>
				<div class="elem_pari"><a onclick="javascript:load_content('div','lista_regimi.php');" href="#">elenco regimi</a></div>
				<div class="elem_dispari"><a href="#">modifica regime</a></div>

			</div>


			<form method="post" name="form0" action="lista_regimi_POST.php" id="myForm">
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="op" value="2" />
				<input type="hidden" name="id" value="<?php echo $idregime ?>" />

				<div class="titolo_pag">
					<h1>Modifica Regime</h1>
				</div>

				<div class="blocco_centralcat">

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Regime</div>
							<div class="campo_mask mandatory">
								<input type="text" name="regime" class="scrittura" value="<?= $regime ?>" />
							</div>
						</div>
					</div>
					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">normativa</div>
							<div class="campo_mask mandatory">
								<select id="normativa" class="scrittura" name="normativa">
									<?php
									$conn = db_connect();
									print('<option value="">Selezionare una normativa</option>' . "\n");
									$query = "SELECT normativa, idnormativa, stato, cancella FROM dbo.normativa WHERE (stato = 1) AND (cancella = 'n')";
									$rs1 = mssql_query($query, $conn);
									while ($row = mssql_fetch_assoc($rs1)) {
										$idnormativa = $row['idnormativa'];
										$normativa1 = $row['normativa'];

										print('<option value="' . $idnormativa . '"');
										if ($normativa == $normativa1) echo (" selected=\"selected\"");
										print('>' . $normativa1 . '</option>' . "\n");
									}
									?>
								</select>
							</div>
						</div>

					</div>

					<div class="riga">
						<div class="rigo_mask">
							<div class="testo_mask">Responsabile</div>
							<div class="campo_mask mandatory">
								<select name="responsabile">
									<option value="0" <? if ($responsabile == 0) print("selected") ?>>direttore sanitario</option>
									<option value="1" <? if ($responsabile == 1) print("selected") ?>>direttore tecnico</option>
								</select>
							</div>
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



<?
}




if (isset($_SESSION['UTENTE'])) {


	if (!isset($do)) $do = '';
	$back = "main.php";

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
				else update($_POST['id']);
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