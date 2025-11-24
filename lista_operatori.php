<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 12;
$tablename = 'normative';
include_once('include/function_page.php');

function show_list()
{

    $conn = db_connect();
?>
    <script>
        function conf_cancella(id) {
            if (confirm('sei sicuro di voler cancellare l\'operatore corrente?')) {
                //document.getElementById('id_allegato').value=id;
                $.ajax({
                    type: "POST",
                    url: "ajax_operatori.php?id_operatore=" + id,
                    data: "",
                    success: function(msg) {
                        //alert( "Data Saved:"+$.trim(msg)+"1" );
                        if ($.trim(msg) == "") {
                            $.ajax({
                                type: "POST",
                                url: "lista_operatori_POST.php?action=del_operatore&id_operatore=" + id,
                                data: "",
                                success: function(msg) {
                                    //alert( "Data Saved: " + msg );
                                    reload_operatori();
                                }
                            });
                        } else {
                            alert('Impossibile cancellare l\'operatore risulta presente in cartelle pianificate e/o compilate');
                            return false;
                        }

                    }
                });
            } else return false;
        }

        function reload_operatori() {
            $("#layer_nero2").toggle();
            $('#wrap-content').innerHTML = "";
            pagina_da_caricare = "lista_operatori.php";
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
                <div class="elem0"><a href="#">gestione permessi</a></div>
                <div class="elem_pari"><a href="#">elenco operatori</a></div>

            </div>

            <div id="ricerca"></div>

            <div class="titolo_pag">
                <div class="comandi">
                    <div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_operatori.php?do=add');">aggiungi operatore</a></div>
                </div>
            </div>

            <?
            $query = "SELECT dbo.operatori.privacy_cartella,dbo.operatori.case_manager, dbo.operatori.med_resp, dbo.operatori.dir_tecnico, dbo.operatori.dir_sanitario, dbo.operatori.uid, dbo.operatori.gid, dbo.operatori.nome, dbo.operatori.gestore_cartella as gc, dbo.operatori.status, dbo.operatori.cancella, dbo.gruppi.nome AS gr_nome, dbo.gruppi.tipo as gr_tipo FROM dbo.operatori INNER JOIN dbo.gruppi ON dbo.operatori.gid = dbo.gruppi.gid WHERE (dbo.operatori.cancella = 'n') ORDER BY dbo.operatori.nome";
            $rs = mssql_query($query, $conn);
            $conta = mssql_num_rows($rs);
            if (!$rs) error_message(mssql_error());
            if (mssql_num_rows($rs) == 0) {
            ?>

                <div class="titoloalternativo">
                    <h1>Elenco Operatori</h1>
                </div>
                <div class="info">Non esistono operatori.</div>


            <?
                exit();
            } ?>
            <table id="table" class="tablesorter" cellspacing="1">
                <thead>
                    <tr>
                        <th width="5%">id</th>
                        <th width="5%">stato</th>
                        <th width="3%">ds</th>
                        <th width="3%">dt</th>
                        <th width="3%">cm</th>
                        <th width="3%">gc</th>
                        <th width="3%">mr</th>
                        <th width="3%">pc</th>
                        <th width="30%">cognome e nome</th>
                        <th width="15%">gruppo</th>
                        <th width="15%">tipologia</th>
                        <td width="10%">modifica</td>
                        <td width="10%">elimina</td>

                    </tr>
                </thead>
                <tbody>
                    <?
                    //while($row1 = mssql_fetch_assoc($rs1))   
                    //{
                    //$idnormativa=$row1['idnormativa'];

                    while ($row = mssql_fetch_assoc($rs)) {
                        $id_ope = $row['uid'];
                        $nome = $row['nome'];
                        $gr_nome = $row['gr_nome'];
                        $gr_tipo = $row['gr_tipo'];
                        $gr_tipo_1 = $row['gr_tipo'];
                        $dir_san = $row['dir_sanitario'];
                        $dir_tec = $row['dir_tecnico'];
                        $ges_car = $row['gc'];
                        $med_resp = $row['med_resp'];
                        $privacy_cartella = $row['privacy_cartella'];
                        $case_manager = $row['case_manager'];
                        if ($gr_tipo == 1) $gr_tipo = "amministrativo";
                        if ($gr_tipo == 2) $gr_tipo = "sanitario";
                        if ($gr_tipo == 3) $gr_tipo = "entrambi";
                        if ($gr_tipo == 4) $gr_tipo = "amministrativo solo lettura";
                        if ($gr_tipo == 5) $gr_tipo = "san. completo e amm. solo lettura";
                        $status = $row['status'];
                        if ($status) {
                            $stato = "stato_ok.png";
                            $alt_s = "attivo";
                        } else {
                            $stato = "stato_blu.png";
                            $alt_s = "disattivo";
                        }
                        // rimuove i caratteri di escape
                        $nome = pulisci_lettura($nome);
                        $gr_nome = pulisci_lettura($gr_nome);
                        $num_ds = 0;
                        $num_dt = 0;
                    ?>

                        <tr>
                            <td><?= $id_ope ?></td>
                            <td align="center" width="10%"><img src="images/<?= $stato ?>" title="stato <?= $alt_s ?>" /></td>
                            <?
                            if ($dir_san == 'y') {
                                $query = "SELECT uid, data_inizio, data_fine, tipo FROM dbo.operatori_direttore GROUP BY uid, data_inizio, data_fine, tipo 
		HAVING  (data_inizio < CONVERT(DATETIME, '" . date("Y-m-d") . " 00:00:00', 102)) AND (data_fine > CONVERT(DATETIME, '" . date("Y-m-d") . " 00:00:00', 102)) AND (tipo = 1) AND (uid = $id_ope)";
                                $rs1 = mssql_query($query, $conn);
                                $num_ds = mssql_num_rows($rs1);
                            }
                            ?>
                            <td><? if ($num_ds > 0) { ?><img src="images/spunto.png" alt="direttore sanitario" /><? } ?></td>
                            <?
                            if ($dir_tec == 'y') {
                                $query = "SELECT uid, data_inizio, data_fine, tipo FROM dbo.operatori_direttore GROUP BY uid, data_inizio, data_fine, tipo 
		HAVING  (data_inizio < CONVERT(DATETIME, '" . date("Y-m-d") . " 00:00:00', 102)) AND (data_fine > CONVERT(DATETIME, '" . date("Y-m-d") . " 00:00:00', 102)) AND (tipo = '2') AND (uid = $id_ope)";
                                $rs1 = mssql_query($query, $conn);
                                $num_dt = mssql_num_rows($rs1);
                            } ?>
                            <td><? if ($num_dt > 0) { ?><img src="images/spunto.png" alt="direttore tecnico" /><? } ?></td>
                            <td><? if ($case_manager == 'y') { ?><img src="images/spunto.png" alt="case manager" /><? } ?></td>
                            <td><? if ($ges_car == 'y') { ?><img src="images/spunto.png" alt="gestore cartella" /><? } ?></td>
                            <td><? if ($med_resp == 'y') { ?><img src="images/spunto.png" alt="medico responsabile" /><? } ?></td>
                            <td><? if ($privacy_cartella == '1') { ?><img src="images/spunto.png" alt="privacy cartella" /><? } ?></td>
                            <td><?= $nome ?></td>
                            <td><?= $gr_nome ?></td>
                            <td><?= $gr_tipo ?></td>
                            <td><a id="<?= $id ?>" onclick="javascript:load_content('div','lista_operatori.php?do=edit&id=<?= $id_ope ?>');" href="#"><img src="images/gear.png" /></a></td>
                            <td><a id="<?= $id ?>" onclick="conf_cancella('<?= $id_ope ?>');" href="#"><img src="images/remove.png" /></a></td>
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

function add()
{
	$conn = db_connect();
	$lista_reparti = array();
	
	$_sql = "SELECT rp.id, rp.nome, rp.sede
				FROM reparti rp
				WHERE rp.status = 2 AND deleted = 0";
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

            <div id="briciola" style="margin-bottom:20px;">
                <div class="elem0"><a href="principale.php">home</a></div>
                <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
                <div class="elem_dispari"><a href="#">aggiungi operatore</a></div>

            </div>

            <form method="post" name="form0" action="lista_operatori_POST.php" id="myForm">
                <input type="hidden" value="create" name="action" />
                <input type="hidden" value="1" name="op" />

                <div class="titolo_pag">
                    <h1>Aggiungi Operatore</h1>
                </div>

                <div class="blocco_centralcat">

                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">cognome e nome</div>
                            <div class="campo_mask mandatory">
                                <input type="text" class="scrittura_campo" name="nome" SIZE="30" maxlenght="30" />
                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">email</div>
                            <div class="campo_mask email">
                                <input type="text" class="scrittura_campo" name="email" SIZE="30" maxlenght="30" />
                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">titolo</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura_campo" name="titolo" SIZE="30" maxlenght="30" />
                            </div>
                        </span>
                    </div>

                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">codice fiscale</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura_campo" name="codice_fiscale" SIZE="16" maxlenght="16" />
                            </div>
                        </span>
                        <span class="rigo_mask">
                            <div class="testo_mask">alias certificato firma digitale</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura_campo" name="alias_firma_digitale" SIZE="30" />
                            </div>
                        </span>
                        <span class="rigo_mask">
                            <div class="testo_mask">ore lavorative settimanali</div>
                            <div class="campo_mask mandatory">
                                <input type="number" min="0" class="scrittura_campo" name="ore_sett" max="99" required />
                            </div>
                        </span>
                    </div>

                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">ordine professionale</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura_campo" name="ordine_professionale">
                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">numero di iscrizione</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura_campo" name="num_iscrizione" min="0">
                            </div>
                        </span>
						
						<span class="rigo_mask">
							<div class="testo_mask">Elenco reparti</div>
							<div class="campo_mask">
								<select id="reparti" name="reparti[]" class="scrittura" multiple style="width:250px; height: 100px;">
								<? foreach ($lista_reparti as $reparto) {
										switch ($reparto['sede']) {
											case 1:
												$reparto['sede'] = 'Servizi ambulatoriali';
												break;

											case 2:
												$reparto['sede'] = 'Residenziale';
												break;

											default:
												$reparto['sede'] = 'Struttura non gestita';
												break;
										}
										echo '<option value="' . $reparto['id'] . '" ' . (!empty($reparto['id_operatore']) ? 'selected' : '') . ' >' . $reparto['nome'] . ' - ' . $reparto['sede'] . '</option>';
									} ?>
								</select>
							</div>
						</span>
                    </div>

                    <div class="riga">
                        <span class="rigo_mask rigo_big">
                            <div class="testo_mask">breve descrizione</div>
                            <div class="campo_mask">
                                <textarea name="descr" class="scrittura_campo" rows="10" cols="40"></textarea>
                            </div>
                        </span>
                    </div>

                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">username</div>
                            <div class="campo_mask mandatory">
                                <input type="text" class="scrittura" name="username" SIZE="30" maxlenght="30" />
                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">password</div>
                            <div class="campo_mask mandatory">
                                <input type="password" class="scrittura" name="password" SIZE="30" maxlenght="30" id="password" />
                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">conferma password</div>
                            <div class="campo_mask mandatory">
                                <input type="password" class="scrittura" id="rpassword" name="rpassword" SIZE="30" maxlenght="30" />
                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">Reimposta password al log in</div>
                            <div class="campo_mask">
                                <input type="radio" name="check_pass" value="y" /> si
                                <input type="radio" name="check_pass" value="n" checked /> no
                            </div>
                        </span>

                    </div>

                    <span class="rigo_mask">
                        <div class="testo_mask">Reimposta password ogni <?= G_REST_PASS ?> giorni</div>
                        <div class="campo_mask">
                            <input type="radio" name="check_pass_60" value="y" /> si
                            <input type="radio" name="check_pass_60" value="n" checked /> no
                        </div>
                    </span>

                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">Direttore sanitario</div>
                            <div class="campo_mask">
                                <input id="dir_sanitario" type="radio" name="dir_sanitario" value="y" onclick="javascript:document.getElementById('san_inizio').disabled=false;document.getElementById('san_fine').disabled=false;$('#sanitario').slideDown();" /> si
                                <input id="dir_sanitario" type="radio" name="dir_sanitario" value="n" onclick="javascript:$('#sanitario').slideUp();" checked /> no
                            </div>
                        </span>
                        <?php if ($dir_san == 'y') {
                            $dis = "";
                        ?>
                            <div id="sanitario" style="display:block;">
                            <? } else {
                            $dis = "disabled"; ?>
                                <div id="sanitario" style="display:none;">
                                <? } ?>
                                <span class="rigo_mask">
                                    <div class="testo_mask">dal</div>
                                    <div class="campo_mask mandatory ">
                                        <input type="text" class="campo_data scrittura" <?= $dis ?> name="san_inizio" id="san_inizio" />
                                    </div>
                                </span>
                                <span class="rigo_mask">
                                    <div class="testo_mask">al</div>
                                    <div class="campo_mask mandatory ">
                                        <input type="text" class="campo_data scrittura" <?= $dis ?> name="san_fine" id="san_fine" />
                                    </div>
                                </span>
                                </div>
                            </div>
                            <div class="riga">
                                <span class="rigo_mask">
                                    <div class="testo_mask">Direttore Tecnico</div>
                                    <div class="campo_mask">
                                        <input type="radio" name="dir_tecnico" value="y" onclick="javascript:document.getElementById('tec_inizio').disabled=false;document.getElementById('tec_fine').disabled=false;$('#tecnico').slideDown();" /> si
                                        <input type="radio" name="dir_tecnico" value="n" onclick="javascript:$('#tecnico').slideUp();" checked /> no
                                    </div>
                                </span>
                                <?php if ($dir_tec == 'y') {
                                    $dis = "";
                                ?>
                                    <div id="tecnico" style="display:block;">
                                    <? } else {
                                    $dis = "disabled"; ?>
                                        <div id="tecnico" style="display:none;">
                                        <? } ?>
                                        <span class="rigo_mask">
                                            <div class="testo_mask">dal</div>
                                            <div class="campo_mask mandatory ">
                                                <input type="text" class="campo_data scrittura" <?= $dis ?> name="tec_inizio" id="tec_inizio" />
                                            </div>
                                        </span>
                                        <span class="rigo_mask">
                                            <div class="testo_mask">al</div>
                                            <div class="campo_mask mandatory ">
                                                <input type="text" class="campo_data scrittura" <?= $dis ?> name="tec_fine" id="tec_fine" />
                                            </div>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="riga">
                                        <span class="rigo_mask">
                                            <div class="testo_mask">Case Manager</div>
                                            <div class="campo_mask">
                                                <input id="case_manager" type="radio" name="case_manager" value="y" /> si
                                                <input id="case_manager" type="radio" name="case_manager" value="n" checked /> no
                                            </div>
                                        </span>
                                    </div>
                                    <div class="riga">
                                        <span class="rigo_mask">
                                            <div class="testo_mask">Gestore Cartella</div>
                                            <div class="campo_mask">
                                                <input id="gest_cart" type="radio" name="gest_cart" value="y" /> si
                                                <input id="gest_cart" type="radio" name="gest_cart" value="n" checked /> no
                                            </div>
                                        </span>
                                    </div>
                                    <div class="riga">
                                        <span class="rigo_mask">
                                            <div class="testo_mask">Medico Responsabile</div>
                                            <div class="campo_mask">
                                                <input id="med_resp" type="radio" name="med_resp" value="y" /> si
                                                <input id="med_resp" type="radio" name="med_resp" value="n" checked /> no
                                            </div>
                                        </span>
                                    </div>

                                    <div class="riga">
                                        <span class="rigo_mask">
                                            <div class="testo_mask">gruppo</div>
                                            <div class="campo_mask">
                                                <?php
												$query = "SELECT gid,nome FROM gruppi WHERE cancella='n' ORDER BY nome";
                                                $rs = mssql_query($query, $conn);

                                                if (mssql_num_rows($rs)) {

                                                    print('<select name="gid" class="scrittura">' . "\n");
                                                    while ($row = mssql_fetch_assoc($rs)) {

                                                        $gid = $row['gid'];
                                                        $nome_gruppo = $row['nome'];

                                                        print('<option value="' . $gid . '">' . $nome_gruppo . '</option>');
                                                    }
                                                    print('</select>' . "\n");
                                                } else print('nessun gruppo presente');
                                                ?>
                                                <!-- <input type="submit" name="action" value="cambia gruppo" disabled /> -->
                                            </div>
                                        </span>

                                        <span class="rigo_mask">
                                            <div class="testo_mask">stato</div>
                                            <div class="campo_mask">
                                                <select name="status" class="scrittura">
                                                    <option value="1">attivo</option>
                                                    <option value="0">disattivo</option>
                                                </select>
                                            </div>
                                        </span>

                                        <span class="rigo_mask">
                                            <div class="testo_mask">privacy cartella</div>
                                            <div class="campo_mask">
                                                <select name="privacy_cartella" class="scrittura">
                                                    <option value="0">disattivo</option>
                                                    <option value="1">attivo</option>

                                                </select>
                                            </div>
                                        </span>

                                    </div>
                                    <div class="riga">
                                        <? if (!$_SESSION['businesskey_usafirma']) { ?>
                                            <span class="rigo_mask">
                                                <div class="testo_mask">chiave per firma digitale</div>
                                                <div class="campo_mask chiavebk">
                                                    <span class="acqbk"><a href="#">acquisici business key da associare all'operatore</a></span>
                                                    <textarea style="display:none;" name="key_firma" id="key_firma" class="scrittura" rows="4" cols="40"></textarea>
                                                </div>
                                            </span>
                                        <? } ?>

                                        <span class="rigo_mask">
                                            <div class="testo_mask">usa businesskey</div>
                                            <div class="campo_mask">
                                                <?php
                                                $conn = db_connect();
                                                $query = "SELECT status FROM gruppi WHERE gid='" . $gid . "'";
                                                $rs = mssql_query($query, $conn);

                                                ?>
                                                <select name="usabk" class="scrittura">
                                                    <option value="1" <?php if ($usabk) echo "selected"; ?>>attivo</option>
                                                    <option value="0" <?php if (!$usabk) echo "selected"; ?>>disattivo</option>
                                                </select>
                                                <?php
                                                mssql_free_result($rs);
                                                ?>
                                            </div>
                                        </span>
                                        <span class="rigo_mask">
                                            <div class="testo_mask">Business Key Code</div>
                                            <div class="campo_mask">
                                                <input type="text" class="scrittura" name="bk_code" />
                                            </div>
                                        </span>
                                    </div>

                            </div>

                            <div class="titolo_pag">
                                <div class="comandi">
                                    <input id="salva_operatore" type="submit" title="salva" value="salva" class="button_salva" />
                                </div>
                            </div>

            </form>



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


function edit($id)
{
    $conn = db_connect();
    $query = "SELECT dbo.operatori.privacy_cartella, dbo.operatori.med_resp, dbo.operatori.titolo, dbo.operatori.ordine_professionale, dbo.operatori.num_iscrizione, 
						dbo.operatori.codice_fiscale, dbo.operatori.alias_firma_digitale,
						dbo.operatori.bk_code, dbo.operatori.usa_firma, dbo.operatori.key_firma, dbo.operatori.check_pass,dbo.operatori.check_pass_60, dbo.operatori.case_manager, dbo.operatori.uid,dbo.operatori.gestore_cartella as gest_cart, dbo.operatori.gid, dbo.operatori.nome, dbo.operatori.status, 
						dbo.operatori.cancella, dbo.gruppi.nome AS gr_nome, dbo.gruppi.tipo AS gr_tipo, dbo.operatori.descr, dbo.operatori.username, 
						dbo.operatori.password, dbo.operatori.email, dbo.operatori.is_root, dbo.operatori.dir_sanitario, dbo.operatori.dir_tecnico, dbo.operatori.tot_ore_sett
				FROM dbo.operatori 
				INNER JOIN dbo.gruppi ON dbo.operatori.gid = dbo.gruppi.gid 
				WHERE (dbo.operatori.uid=$id)";
    $rs = mssql_query($query, $conn);
    if (!$rs) error_message(mssql_error());

    $lista_reparti = array();

    if ($row = mssql_fetch_assoc($rs)) {
        $nome = pulisci_lettura($row['nome']);
        $descr = pulisci_lettura($row['descr']);
        $email = $row['email'];
        $usr = $row['username'];
        $gid = $row['gid'];
        $status = $row['status'];
        $dir_san = $row['dir_sanitario'];
        $dir_tec = $row['dir_tecnico'];
        $tot_ore_sett = $row['tot_ore_sett'];
        $alias_firma_digitale = $row['alias_firma_digitale'];
        $codice_fiscale = $row['codice_fiscale'];
        $ordine_professionale = $row['ordine_professionale'];
        $num_iscrizione = $row['num_iscrizione'];
        $check_pass = $row['check_pass'];
        $check_pass_60 = $row['check_pass_60'];
        $gest_cart = $row['gest_cart'];
        $case_manager = $row['case_manager'];
        $med_resp = $row['med_resp'];
        $key = $row['key_firma'];
        $usabk = $row['usa_firma'];
        $bk_code = $row['bk_code'];
        $privacy_cartella = $row['privacy_cartella'];
        $titolo = pulisci_lettura($row['titolo']);

        $_sql = "SELECT rp.id, rp.nome, rp.sede, rp_op.id_operatore
				FROM reparti rp
				LEFT JOIN reparti_operatori rp_op ON rp_op.id_reparto = rp.id AND rp_op.id_operatore = $id
				WHERE rp.status = 2 AND deleted = 0";
        $_rs = mssql_query($_sql, $conn);

        while ($_row = mssql_fetch_assoc($_rs)) {
            $lista_reparti[] = $_row;
        }

        mssql_free_result($_rs);
    }
    mssql_free_result($rs);

    if ($dir_san == 'y') {
        $query = "SELECT * from operatori_direttore WHERE ((uid='$id') and (tipo='1'))";
        $rs = mssql_query($query, $conn);

        if ($row = mssql_fetch_assoc($rs)) {
            $san_inizio = formatta_data($row['data_inizio']);
            $san_fine = formatta_data($row['data_fine']);
        }
        mssql_free_result($rs);
    }

    if ($dir_tec == 'y') {
        $query = "SELECT * from operatori_direttore WHERE ((uid='$id') and (tipo='2'))";
        $rs = mssql_query($query, $conn);

        if ($row = mssql_fetch_assoc($rs)) {
            $tec_inizio = formatta_data($row['data_inizio']);
            $tec_fine = formatta_data($row['data_fine']);
        }
        mssql_free_result($rs);
    }

    ?>
        <script>
            inizializza();
        </script>
        <script language="javascript">
            $(document).ready(function() {
                $(".acqbk").click(function() {
                    $(".acqbk").html("acquisizione businesskey in corso...");
                    businessKey(3);
                });
            });
        </script>
        <div id="wrap-content">
            <div class="padding10">
                <div class="logo"><img src="images/re-med-logo.png" /></div>

                <div id="briciola" style="margin-bottom:20px;">
                    <div class="elem0"><a href="principale.php">home</a></div>
                    <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
                    <div class="elem_dispari"><a href="#">modifica operatore</a></div>

                </div>


                <form method="post" name="operatori" action="lista_operatori_POST.php" id="myForm">
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="op" value="2" />
                    <input type="hidden" name="id" id="id" value="<?= $id ?>" />

                    <div class="titolo_pag">
                        <h1>Modifica Operatore</h1>
                    </div>

                    <div class="blocco_centralcat">

                        <div class="riga">
                            <span class="rigo_mask">
                                <div class="testo_mask">cognome e nome</div>
                                <div class="campo_mask mandatory">
                                    <input class="scrittura_campo" type="text" name="nome" value="<?php echo $nome ?>" />
                                </div>
                            </span>

                            <span class="rigo_mask">
                                <div class="testo_mask">email</div>
                                <div class="campo_mask">
                                    <input class="scrittura_campo" type="text" name="email" value="<?php echo $email ?>" />
                                </div>
                            </span>

                            <span class="rigo_mask">
                                <div class="testo_mask">titolo</div>
                                <div class="campo_mask">
                                    <input type="text" class="scrittura_campo" name="titolo" value="<?php echo $titolo ?>" />
                                </div>
                            </span>
                        </div>

                        <div class="riga">
                            <span class="rigo_mask">
                                <div class="testo_mask">codice fiscale</div>
                                <div class="campo_mask">
                                    <input type="text" class="scrittura_campo" name="codice_fiscale" value="<?php echo $codice_fiscale ?>" SIZE="16" maxlenght="16" />
                                </div>
                            </span>
                            <span class="rigo_mask">
                                <div class="testo_mask">alias certificato firma digitale</div>
                                <div class="campo_mask">
                                    <input type="text" class="scrittura_campo" name="alias_firma_digitale" value="<?php echo $alias_firma_digitale ?>" SIZE="30" />
                                </div>
                            </span>
                            <span class="rigo_mask">
                                <div class="testo_mask">ore lavorative settimanali</div>
                                <div class="campo_mask mandatory">
                                    <input type="number" min="0" class="scrittura_campo" name="ore_sett" max="99" value="<?php echo $tot_ore_sett ?>" />
                                </div>
                            </span>
                        </div>

                        <div class="riga">
                            <span class="rigo_mask">
                                <div class="testo_mask">ordine professionale</div>
                                <div class="campo_mask">
                                    <input type="text" class="scrittura_campo" name="ordine_professionale" value="<?php echo $ordine_professionale ?>" />
                                </div>
                            </span>
                            <span class="rigo_mask">
                                <div class="testo_mask">numero di iscrizione</div>
                                <div class="campo_mask">
                                    <input type="text" class="scrittura_campo" name="num_iscrizione" value="<?php echo $num_iscrizione ?>" />
                                </div>
                            </span>
                            <div class="rigo_mask">
                                <div class="testo_mask">Elenco reparti</div>
                                <div class="campo_mask">
                                    <select id="reparti" name="reparti[]" class="scrittura" multiple style="width:250px; height: 100px;">
                                    <? foreach ($lista_reparti as $reparto) {
											switch ($reparto['sede']) {
												case 1:
													$reparto['sede'] = 'Servizi ambulatoriali';
													break;

												case 2:
													$reparto['sede'] = 'Residenziale';
													break;

												default:
													$reparto['sede'] = 'Struttura non gestita';
													break;
											}
                                            echo '<option value="' . $reparto['id'] . '" ' . (!empty($reparto['id_operatore']) ? 'selected' : '') . ' >' . $reparto['nome'] . ' - ' . $reparto['sede'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="riga">
                            <span class="rigo_mask rigo_big">
                                <div class="testo_mask">breve descrizione</div>
                                <div class="campo_mask">
                                    <textarea name="descr" class="scrittura" rows="4" cols="40"><?php echo $descr ?></textarea>
                                </div>
                            </span>
                        </div>

                        <div class="riga">
                            <span class="rigo_mask">
                                <div class="testo_mask">username</div>
                                <div class="campo_mask">
                                    <input class="lettura" type="text" name="usr" value="<?php echo $usr; ?>" <? if (!($_SESSION['UTENTE']->is_root())) { ?> readonly <? } ?> /><br /> <a onclick="$('.cambia_pwd').slideDown();var a=document.getElementById('pass_id');a.className=' mandatory';var a=document.getElementById('pass_co');a.className=' mandatory';" class="cambia_valore">cambia password</a>
                                </div>
                            </span>

                            <div id="change_pwd" class="cambia_pwd" style="display:none;">
                                <span class="rigo_mask">
                                    <div class="testo_mask">password</div>
                                    <div class="campo_mask nomandatory" id="pass_id">
                                        <input type="password" class="scrittura" name="password" SIZE="30" maxlenght="30" id="password" />
                                    </div>
                                </span>

                                <span class="rigo_mask">
                                    <div class="testo_mask">conferma password</div>
                                    <div class="campo_mask nomandatory" id="pass_co">
                                        <input type="password" class="scrittura" id="rpassword" name="rpassword" SIZE="30" maxlenght="30" /> <a onclick="$('.cambia_pwd').slideUp();var a=document.getElementById('pass_id');a.className=' nomandatory';var a=document.getElementById('pass_co');a.className=' nomandatory';" class="cambia_valore">annulla operazione</a>
                                    </div>
                                </span>
                            </div>
                            <!-- <span class="rigo_mask">
                                <div class="testo_mask">password</div>
                                <div class="campo_mask mandatory">
                                <a href="password.php?do=edit_pwd&amp;id=<?php echo $id ?>">cambia la password</a>
                                </div>
                            </span>-->
                        </div>

                        <span class="rigo_mask">
                            <div class="testo_mask">Reimposta password al log in</div>
                            <div class="campo_mask">
                                <input type="radio" name="check_pass" value="y" <?php if ($check_pass == 'y') echo "checked"; ?> /> si
                                <input type="radio" name="check_pass" value="n" <?php if ($check_pass == 'n') echo "checked"; ?> /> no
                            </div>
                        </span>
                        <div class="riga">
                            <span class="rigo_mask">
                                <div class="testo_mask">Reimposta password ogni <?= G_REST_PASS ?> giorni</div>
                                <div class="campo_mask">
                                    <input type="radio" name="check_pass_60" value="y" <?php if ($check_pass_60 == 'y') echo "checked"; ?> /> si
                                    <input type="radio" name="check_pass_60" value="n" <?php if ($check_pass_60 == 'n') echo "checked"; ?> /> no
                                </div>
                            </span>
                        </div>

                        <div class="riga">
                            <span class="rigo_mask">
                                <div class="testo_mask">Direttore sanitario</div>
                                <div class="campo_mask">
                                    <input type="radio" name="dir_sanitario" value="y" onclick="javascript:document.getElementById('san_inizio').disabled=false;document.getElementById('san_fine').disabled=false;$('#sanitario').slideDown();" <?php if ($dir_san == 'y') echo "checked"; ?> /> si
                                    <input type="radio" name="dir_sanitario" value="n" onclick="javascript:$('#sanitario').slideUp();" <?php if ($dir_san == 'n') echo "checked"; ?> /> no
                                </div>
                            </span>
                            <?php if ($dir_san == 'y') {
                                $dis = "";
                            ?>
                                <div id="sanitario" style="display:block;">
                                <? } else {
                                $dis = "disabled"; ?>
                                    <div id="sanitario" style="display:none;">
                                    <? } ?>
                                    <span class="rigo_mask">
                                        <div class="testo_mask">dal</div>
                                        <div class="campo_mask mandatory ">
                                            <input type="text" class="campo_data scrittura" name="san_inizio" <?= $dis ?> id="san_inizio" value="<?= $san_inizio ?>" />
                                        </div>
                                    </span>
                                    <span class="rigo_mask">
                                        <div class="testo_mask">al</div>
                                        <div class="campo_mask mandatory ">
                                            <input type="text" class="campo_data scrittura" name="san_fine" <?= $dis ?> id="san_fine" value="<?= $san_fine ?>" />
                                        </div>
                                    </span>
                                    </div>
                                </div>
                                <div class="riga">
                                    <span class="rigo_mask">
                                        <div class="testo_mask">Direttore Tecnico</div>
                                        <div class="campo_mask">
                                            <input type="radio" name="dir_tecnico" value="y" onclick="javascript:document.getElementById('tec_inizio').disabled=false;document.getElementById('tec_fine').disabled=false;$('#tecnico').slideDown();" <?php if ($dir_tec == 'y') echo "checked"; ?> /> si
                                            <input type="radio" name="dir_tecnico" value="n" onclick="javascript:$('#tecnico').slideUp();" <?php if ($dir_tec == 'n') echo "checked"; ?> /> no
                                        </div>
                                    </span>
                                    <?php if ($dir_tec == 'y') {
                                        $dis = "";
                                    ?>
                                        <div id="tecnico" style="display:block;">
                                        <? } else {
                                        $dis = "disabled"; ?>
                                            <div id="tecnico" style="display:none;">
                                            <? } ?>
                                            <span class="rigo_mask">
                                                <div class="testo_mask">dal</div>
                                                <div class="campo_mask mandatory ">
                                                    <input type="text" class="campo_data scrittura" name="tec_inizio" <?= $dis ?> id="tec_inizio" value="<?= $tec_inizio ?>" />
                                                </div>
                                            </span>
                                            <span class="rigo_mask">
                                                <div class="testo_mask">al</div>
                                                <div class="campo_mask mandatory ">
                                                    <input type="text" class="campo_data scrittura" name="tec_fine" <?= $dis ?> id="tec_fine" value="<?= $tec_fine ?>" />
                                                </div>
                                            </span>
                                            </div>
                                        </div>
                                        <div class="riga">
                                            <span class="rigo_mask">
                                                <div class="testo_mask">Case Manager</div>
                                                <div class="campo_mask">
                                                    <input id="case_manager" type="radio" name="case_manager" value="y" <?php if ($case_manager == 'y') echo "checked"; ?> /> si
                                                    <input id="case_manager" type="radio" name="case_manager" value="n" <?php if ($case_manager == 'n') echo "checked"; ?> /> no
                                                </div>
                                            </span>
                                        </div>
                                        <div class="riga">
                                            <span class="rigo_mask">
                                                <div class="testo_mask">Gestore Cartella</div>
                                                <div class="campo_mask">
                                                    <input id="gest_cart" type="radio" name="gest_cart" value="y" <?php if ($gest_cart == 'y') echo "checked"; ?> /> si
                                                    <input id="gest_cart" type="radio" name="gest_cart" value="n" <?php if ($gest_cart == 'n') echo "checked"; ?> /> no
                                                </div>
                                            </span>
                                        </div>
                                        <div class="riga">
                                            <span class="rigo_mask">
                                                <div class="testo_mask">Medico Responsabile</div>
                                                <div class="campo_mask">
                                                    <input id="med_resp" type="radio" name="med_resp" value="y" <?php if ($med_resp == 'y') echo "checked"; ?> /> si
                                                    <input id="med_resp" type="radio" name="med_resp" value="n" <?php if ($med_resp == 'n') echo "checked"; ?> /> no
                                                </div>
                                            </span>
                                        </div>
                                        <div class="riga">
                                            <span class="rigo_mask">
                                                <div class="testo_mask">gruppo</div>
                                                <div class="campo_mask">
                                                    <?php
                                                    $conn = db_connect();
                                                    $query = "SELECT gid,nome FROM gruppi WHERE cancella='n' ORDER BY nome";
                                                    $rs = mssql_query($query, $conn);

                                                    if (mssql_num_rows($rs)) {

                                                        print('<select name="gid" class="scrittura">' . "\n");
                                                        while ($row = mssql_fetch_assoc($rs)) {

                                                            $gid_1 = $row['gid'];
                                                            $nome_gruppo = $row['nome'];

                                                            print('<option value="' . $gid_1 . '"');
                                                            if ($gid_1 == $gid) echo " selected";
                                                            print('>' . $nome_gruppo . '</option>');
                                                        }
                                                        print('</select>' . "\n");
                                                    } else print('nessun gruppo presente');
                                                    ?>
                                                    <!-- <input type="submit" name="action" value="cambia gruppo" disabled /> -->
                                                </div>
                                            </span>

                                            <span class="rigo_mask">
                                                <div class="testo_mask">stato</div>
                                                <div class="campo_mask">
                                                    <select name="status" class="scrittura">
                                                        <option value="1" <?php if ($status) echo "selected"; ?>>attivo</option>
                                                        <option value="0" <?php if (!$status) echo "selected"; ?>>disattivo</option>
                                                    </select>
                                                </div>
                                            </span>

                                            <span class="rigo_mask">
                                                <div class="testo_mask">privacy cartella</div>
                                                <div class="campo_mask">
                                                    <select name="privacy_cartella" class="scrittura">
                                                        <option value="1" <?php if ($privacy_cartella) echo "selected"; ?>>attivo</option>
                                                        <option value="0" <?php if (!$privacy_cartella) echo "selected"; ?>>disattivo</option>
                                                    </select>
                                                </div>
                                            </span>

                                        </div>
                                        <div class="riga">
                                            <? if (!$_SESSION['businesskey_usafirma']) { ?>
                                                <span class="rigo_mask">
                                                    <div class="testo_mask">chiave per firma digitale</div>
                                                    <div class="campo_mask chiavebk">
                                                        <span class="acqbk"><a href="#">acquisici business key da associare all'operatore</a></span>
                                                        <textarea style="display:none;" name="key_firma" id="key_firma" class="scrittura" rows="4" cols="40"><?php echo $key ?></textarea>
                                                    </div>
                                                </span>
                                            <? } ?>
                                            <span class="rigo_mask">
                                                <div class="testo_mask">usa businesskey</div>
                                                <div class="campo_mask">
                                                    <?php
                                                    $conn = db_connect();
                                                    $query = "SELECT status FROM gruppi WHERE gid='" . $gid . "'";
                                                    $rs = mssql_query($query, $conn);

                                                    ?>
                                                    <select name="usabk" class="scrittura">
                                                        <option value="1" <?php if ($usabk) echo "selected"; ?>>attivo</option>
                                                        <option value="0" <?php if (!$usabk) echo "selected"; ?>>disattivo</option>
                                                    </select>
                                                    <?php
                                                    mssql_free_result($rs);
                                                    ?>
                                                </div>
                                            </span>
                                            <span class="rigo_mask">
                                                <div class="testo_mask">Business Key Code</div>
                                                <div class="campo_mask">
                                                    <input type="text" class="scrittura" name="bk_code" value="<?= $bk_code ?>" />
                                                </div>
                                            </span>

                                        </div>

                                        <div class="titolo_pag">
                                            <div class="comandi">
                                                <input onclick="javascript: return controlla_campi_operatori('edit');" id="salva_operatore" type="submit" title="salva" value="salva" class="button_salva" />
                                            </div>
                                        </div>

                </form>



            </div>
            <script type="text/javascript" language="javascript">
                $(document).ready(function() {
                    $.mask.addPlaceholder('~', "[+-]");
                    $(".campo_data").mask("99/99/9999");

                });
            </script>


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