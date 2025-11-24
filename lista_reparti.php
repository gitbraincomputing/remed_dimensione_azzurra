<script type="text/javascript" src="script/jquery.tablesorter/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
<?php

include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');

$id_permesso = $id_menu = 94;

if (isset($_SESSION['UTENTE'])) {
    if (in_array($id_permesso, $_SESSION['PERMESSI'])) {
        switch ($do) {
            case "add_reparto":
                if ($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
                    add_reparto();
                } else {
                    error_message("Permessi insufficienti per questa operazione!");
                }
                break;

            case "view_reparto":
                if ($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
                    view_reparto($_REQUEST['id']);
                } else {
                    error_message("Permessi insufficienti per questa operazione!");
                }
                break;

            case "edit_reparto":
                if ($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
                    edit_reparto($_REQUEST['id']);
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
    $conn = db_connect();
    $lista_reparti_sql = "SELECT id, nome, sede, status
							FROM reparti
							WHERE deleted = 0
							ORDER BY nome, sede, status ASC";
    $lista_reparti_rs = mssql_query($lista_reparti_sql, $conn);

    $tot_reparti = mssql_num_rows($lista_reparti_rs);
    $lista_reparti = array();

    while ($row = mssql_fetch_assoc($lista_reparti_rs)) {
        $lista_reparti[] = $row;
    }

    mssql_free_result($lista_reparti_rs);
?>
    <div id="wrap-content">
        <div class="padding10">
            <div class="logo"><img src="images/re-med-logo.png" /></div>
            <div class="titoloalternativo">
                <h1>Reparti</h1>
                <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
            </div>

            <div class="titolo_pag">
                <div class="comandi">
                    <div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_reparti.php?do=add_reparto');">aggiungi reparto</a></div>
                </div>
            </div>
            <? if ($tot_reparti == 0) { ?>
                <div class="info" style="margin-top:10px;">Nessun reparto registrato.</div>
            <? exit();
            } ?>
            <table id="table_reparti" class="tablesorter" cellspacing="1">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Sedi</th>
                        <th width="15%">Stato</th>
                        <th width="5%">Visualizza</th>
                        <th width="5%">Modifica</th>
                        <td width="5%">Elimina</td>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($lista_reparti as $reparto) { ?>
                        <tr id="<?= $reparto['id'] ?>">
                            <td><?= $reparto['nome'] ?></td>
                            <td>
                                <? switch ($reparto['sede']) {
                                    case 1:
                                        echo 'Servizi ambulatoriali';
                                        break;

                                    case 2:
                                        echo 'Residenziale';
                                        break;

                                    default:
                                        echo 'Struttura non gestita';
                                        break;
                                }
                                ?>
                            </td>
                            <td>
                                <? switch ($reparto['status']) {
                                    case 1:
                                        echo 'Non attivo';
                                        break;

                                    case 2:
                                        echo 'Attivo';
                                        break;

                                    default:
                                        echo 'Stato non gestito';
                                        break;
                                }
                                ?>
                            </td>
                            <td><a href="#" onclick="javascript:load_content('div','lista_reparti.php?do=view_reparto&id=<?= $reparto['id'] ?>')"><img src="images/view.png" /></a></td>
                            <td><a href="#" onclick="javascript:load_content('div','lista_reparti.php?do=edit_reparto&id=<?= $reparto['id'] ?>')"><img src="images/gear.png" /></a></td>
                            <td><a href="#" onclick="javascript:if(confirm('Sei sicuro di voler cancellare il reparto ?')) del_reparto('<?= $reparto['id'] ?>')"><img src="images/remove.png" /></a></td>
                        </tr>
                    <?    } ?>
                </tbody>
            </table>
            <? footer_paginazione($tot_farmaci); ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#table_reparti").tablesorter({
                widthFixed: true,
                widgets: ['zebra']
            }).tablesorterPager({
                container: $("#pager")
            });
        });
    </script>
<?
}

function add_reparto()
{
    $conn = db_connect();
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
                        <h1>Censimento reparto</h1>
                    </div>
                </div>
            </div>
            <div class="blocco_centralcat">
                <form method="post" name="form0" action="lista_reparti_POST.php" id="myForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create" />

                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">Nome reparto</div>
                            <div class="campo_mask mandatory">
                                <input type="text" id="nome" name="nome" class="scrittura_campo" SIZE="50" maxlenght="50" />
                            </div>
                        </div>

                        <div class="rigo_mask">
                            <div class="testo_mask">Sede del reparto</div>
                            <div class="campo_mask mandatory">
                                <select id="sede_reparto" name="sede_reparto" class="scrittura">
                                    <option value="1">Servizi ambulatoriali</option>
                                    <option value="2">Residenziale</option>
                                </select>
                            </div>
                        </div>

                        <div class="rigo_mask">
                            <div class="testo_mask">Stato del reparto</div>
                            <div class="campo_mask mandatory">
                                <select id="stato_reparto" name="stato_reparto" class="scrittura">
                                    <option value="1">Non attivo</option>
                                    <option value="2">Attivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">Assegnazione operatori</div>
                            <div class="campo_mask">
                                <input type="checkbox" id="associa_operatori" name="associa_operatori" />
                            </div>
                        </div>
                    </div>

                    <div id="operatori_container" class="riga" style="display: none;">
                        <div class="rigo_mask">
                            <div class="testo_mask">Elenco operatori</div>
                            <div class="campo_mask">
                                <select id="operatori" name="operatori[]" class="scrittura" multiple style="width:350px; height: 250px;">
                                    <?
                                    $combo_op_sql = "SELECT uid, nome
														FROM operatori
														WHERE uid NOT IN (11, 258)
														ORDER BY nome ASC";
                                    $_rs = mssql_query($combo_op_sql, $conn);

                                    while ($row = mssql_fetch_assoc($_rs)) {
                                        echo '<option value="' . $row['uid'] . '" >' . $row['nome'] . '</option>';
                                    }

                                    mssql_free_result($_rs); ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">Assegnazione pazienti</div>
                            <div class="campo_mask">
                                <input type="checkbox" id="associa_pazienti" name="associa_pazienti" />
                            </div>
                        </div>
                    </div>

                    <div id="pazienti_container" class="riga" style="display: none;">
                        <div class="rigo_mask">
                            <div class="testo_mask">Elenco pazienti</div>
                            <div class="campo_mask">
                                <select id="pazienti" name="pazienti[]" class="scrittura" multiple style="width:350px; height: 250px;">
                                    <?
                                    $combo_user_sql = "SELECT DISTINCT paz.IdUtente, YEAR(DataNascita) as AnnoNasciata, CONCAT(Cognome, ' ',Nome) as Utente
														FROM utenti paz
														INNER JOIN (
															select idutente
															from utenti_cartelle
															where idutente not in (605) AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'
														) as paz_cart_inf ON paz_cart_inf.idutente = paz.IdUtente
														WHERE cancella = 'n' AND stato = 1 AND cancella = 'n' 
															AND DataNascita IS NOT NULL AND Cognome <> '' AND Nome <> ''
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

function view_reparto($id)
{
    $conn = db_connect();
    $_sql = "SELECT *
				FROM reparti
				WHERE id = $id";
    $_rs = mssql_query($_sql, $conn);

    $reparto = mssql_fetch_assoc($_rs);
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
                        <div class="testo_mask">Nome reparto</div>
                        <div class="campo_mask"><?= $reparto['nome'] ?></div>
                    </div>

                    <div class="rigo_mask">
                        <div class="testo_mask">Sede del reparto</div>
                        <div class="campo_mask">
                            <? switch ($reparto['sede']) {
                                case 1:
                                    echo 'Servizi ambulatoriali';
                                    break;

                                case 2:
                                    echo 'Residenziale';
                                    break;

                                default:
                                    echo 'Struttura non gestita';
                                    break;
                            }
                            ?>
                        </div>
                    </div>

                    <div class="rigo_mask">
                        <div class="testo_mask">Stato del reparto</div>
                        <div class="campo_mask">
                            <? switch ($reparto['status']) {
                                case 1:
                                    echo 'Non attivo';
                                    break;

                                case 2:
                                    echo 'Attivo';
                                    break;

                                default:
                                    echo 'Stato non gestito';
                                    break;
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="riga">
                    <div class="rigo_mask">
                        <div class="testo_mask">Operatori assegnati</div>
                        <div class="campo_mask">
                            <?
                            $_sql = "SELECT id_operatore
										FROM reparti_operatori
										WHERE id_reparto = $id";
                            $_rs = mssql_query($_sql, $conn);
                            $num_op = mssql_num_rows($_rs);
                            $counter = 0;

                            while ($row = mssql_fetch_assoc($_rs)) {
                                $counter++;

                                $combo_op_sql = "SELECT nome
								FROM operatori
								WHERE uid = " . $row['id_operatore'];
                                $_rs_op = mssql_query($combo_op_sql, $conn);

                                $row_op = mssql_fetch_assoc($_rs_op);
                                echo $row_op['nome'] . ($num_op > $counter ? ' <br> ' : '');

                                mssql_free_result($_rs_op);
                            }

                            mssql_free_result($_rs); ?>
                        </div>
                    </div>

                    <div class="rigo_mask">
                        <div class="testo_mask">Pazienti assegnati</div>
                        <div class="campo_mask">
                            <?
                            $_sql = "SELECT id_paziente
										FROM reparti_pazienti
										WHERE id_reparto = $id";
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


function edit_reparto($id)
{
    $conn = db_connect();
    $_sql = "SELECT *
				FROM reparti
				WHERE id = $id";
    $_rs = mssql_query($_sql, $conn);

    $reparto = mssql_fetch_assoc($_rs);

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
                        <h1>Censimento reparto</h1>
                    </div>
                </div>
            </div>
            <div class="blocco_centralcat">
                <form method="post" name="form0" action="lista_reparti_POST.php" id="myForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="id" value="<?= $id ?>" />

                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">Nome reparto</div>
                            <div class="campo_mask mandatory">
                                <input type="text" id="nome" name="nome" class="scrittura_campo" SIZE="50" maxlenght="50" value="<?= $reparto['nome'] ?>" />
								<input type="hidden" id="nome_old" name="nome_old" value="<?= $reparto['nome'] ?>" />
                            </div>
                        </div>

                        <div class="rigo_mask">
                            <div class="testo_mask">Sede del reparto</div>
                            <div class="campo_mask mandatory">
                                <select id="sede_reparto" name="sede_reparto" class="scrittura">
                                    <option <?= $reparto['sede'] == 1 ? 'selected' : '' ?> value="1">Servizi ambulatoriali</option>
                                    <option <?= $reparto['sede'] == 2 ? 'selected' : '' ?> value="2">Residenziale</option>
                                </select>
								<input type="hidden" id="sede_reparto_old" name="sede_reparto_old" value="<?= $reparto['sede'] ?>" />
                            </div>
                        </div>

                        <div class="rigo_mask">
                            <div class="testo_mask">Stato del reparto</div>
                            <div class="campo_mask mandatory">
                                <select id="stato_reparto" name="stato_reparto" class="scrittura">
                                    <option <?= $reparto['status'] == 1 ? 'selected' : '' ?> value="1">Non attivo</option>
                                    <option <?= $reparto['status'] == 2 ? 'selected' : '' ?> value="2">Attivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?
                    $_reparti_op_sql = "SELECT id_operatore
										FROM reparti_operatori
										WHERE id_reparto = $id";
                    $_rep_op_rs = mssql_query($_reparti_op_sql, $conn);
                    $n_operatori = mssql_num_rows($_rep_op_rs);
                    ?>
                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">Assegnazione operatori</div>
                            <div class="campo_mask">
                                <input type="checkbox" id="associa_operatori" name="associa_operatori" <?= $n_operatori > 0 ? 'checked' : '' ?> />
                            </div>
                        </div>
                    </div>

                    <div id="operatori_container" <?= $n_operatori > 0 ? "" : "style='display: none;'" ?>>
                        <div class="rigo_mask">
                            <div class="testo_mask">Elenco operatori</div>
                            <div class="campo_mask">
                                <select id="operatori" name="operatori[]" class="scrittura" multiple style="width:350px; height: 250px;">
                                    <?
                                    $lista_op_rep = array();

                                    while ($row = mssql_fetch_assoc($_rep_op_rs)) {
                                        $lista_op_rep[] = $row['id_operatore'];
                                    }

                                    $_op_sql = "SELECT uid, nome
														FROM operatori
														WHERE uid NOT IN (11, 258)
														ORDER BY nome ASC";
                                    $_rs_users = mssql_query($_op_sql, $conn);

                                    while ($row = mssql_fetch_assoc($_rs_users)) {
                                        echo '<option value="' . $row['uid'] . '" ' . (in_array($row['uid'], $lista_op_rep) ? 'selected' : '') . ' >' . $row['nome'] . '</option>';
                                    }

                                    mssql_free_result($_rs_users); ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?
                    $_reparti_paz_sql = "SELECT id_paziente
										FROM reparti_pazienti
										WHERE id_reparto = $id";
                    $_rep_paz_rs = mssql_query($_reparti_paz_sql, $conn);
                    $n_reparti = mssql_num_rows($_rep_paz_rs);
                    ?>
                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">Assegnazione pazienti</div>
                            <div class="campo_mask">
                                <input type="checkbox" id="associa_pazienti" name="associa_pazienti" <?= $n_reparti > 0 ? 'checked' : '' ?> />
                            </div>
                        </div>
                    </div>

                    <div id="pazienti_container" <?= $n_reparti > 0 ? "" : "style='display: none;'" ?>>
                        <div class="rigo_mask">
                            <div class="testo_mask">Elenco pazienti</div>
                            <div class="campo_mask">
                                <select id="pazienti" name="pazienti[]" class="scrittura" multiple style="width:350px; height: 250px;">
                                    <?
                                    $lista_paz_rep = array();

                                    while ($row = mssql_fetch_assoc($_rep_paz_rs)) {
                                        $lista_paz_rep[] = $row['id_paziente'];
                                    }

                                    $_user_sql = "SELECT DISTINCT paz.IdUtente, YEAR(DataNascita) as AnnoNasciata, CONCAT(Cognome, ' ',Nome) as Utente
														FROM utenti paz
														INNER JOIN (
															select idutente
															from utenti_cartelle
															where idutente not in (605) AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'
														) as paz_cart_inf ON paz_cart_inf.idutente = paz.IdUtente
														WHERE cancella = 'n' AND stato = 1 AND cancella = 'n' 
															AND DataNascita IS NOT NULL AND Cognome <> '' AND Nome <> ''
														ORDER BY Utente, AnnoNasciata ASC";
                                    $_rs_users = mssql_query($_user_sql, $conn);

                                    while ($row = mssql_fetch_assoc($_rs_users)) {
                                        echo '<option value="' . $row['IdUtente'] . '" ' . (in_array($row['IdUtente'], $lista_paz_rep) ? 'selected' : '') . ' >' .
                                            $row['Utente'] . ' (' . $row['IdUtente'] . ') | Anno ' . $row['AnnoNasciata'] .
                                            '</option>';
                                    }

                                    mssql_free_result($_rs_users); ?>
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
?>

<script>
    // ####################################################################################################
    // ######################################## UTILITY FUNCTIONS #########################################
    // ####################################################################################################

    $(document).ready(function() {
        $("#associa_operatori").change(function(e) {
            if (e.currentTarget.checked) {
                $("#operatori_container").css("display", "");
            } else {
                $("#operatori_container").css("display", "none");
                $('[name="operatori[]"]').find('option').attr('selected', '');
            }
        });

        $("#associa_pazienti").change(function(e) {
            if (e.currentTarget.checked) {
                $("#pazienti_container").css("display", "");
            } else {
                $("#pazienti_container").css("display", "none");
                $('[name="pazienti[]"]').find('option').attr('selected', '');
            }
        });
    });

    function back_to_list() {
        $("#wrap-content").load("lista_reparti.php", '', function() {
            loading_page('loading');
        });
    }

    function del_reparto(id) {
        $.ajax({
            type: "POST",
            url: "lista_reparti_POST.php",
            data: "action=delete&id=" + id,
            success: function(res) {
                if (res == 'true') {
                    $("#wrap-content").load("lista_reparti.php", '', function() {
                        loading_page('loading');
                    });
                } else
                    alert('Errore nella cancellazione del reparto');
            },
            error: function(request, error) {
                alert('Errore nella cancellazione del reparto');
            }
        });
    }
</script>