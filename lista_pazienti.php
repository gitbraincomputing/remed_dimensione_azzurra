<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');

define('SEDE_SERV_AMB', 'Servizi ambulatoriali');
define('SEDE_RESIDENZIALE', 'Residenziale');
define('SEDE_NON_GESTITA', 'Struttura non gestita');

function show_list($pg, $numpag)
{
    $conn = db_connect();

    $conta = 0;
    $page = $pg;
    $query = "SELECT * from re_lista_pazienti order by cognome,nome,datanascita";
    $rs = mssql_query($query, $conn);
    if (!$rs) error_message(mssql_error());
    $conta = mssql_num_rows($rs);

    $query = "paginazione_utenti @Pagenumber=$page, @pagesize=$numpag";
    $rs = mssql_query($query, $conn);
?>
    <script type="text/javascript">
        function conf_cancella(id) {
            if (confirm('sei sicuro di voler cancellare il paziente corrente?')) {
                //document.getElementById('id_allegato').value=id;
                $.ajax({
                    type: "POST",
                    url: "ajax_pazienti.php?id_paziente=" + id,
                    data: "",
                    success: function(msg) {
                        //alert( "Data Saved:"+$.trim(msg)+"1" );
                        if ($.trim(msg) == "") {
                            $.ajax({
                                type: "POST",
                                url: "lista_pazienti_POST.php?action=del_paziente&id_paziente=" + id,
                                data: "",
                                success: function(msg) {
                                    //alert( "Data Saved: " + msg );
                                    reload_pazienti(1, 1);
                                }
                            });
                        } else {
                            alert('Impossibile cancellare il paziente in quanto risulta associato a cartelle sanitarie e/o impegnative');
                            return false;
                        }

                    }
                });
            } else return false;
        }

        function reload_pazienti(pagina, numpag) {
            $("#layer_nero2").toggle();
            $('#wrap-content').innerHTML = "";
            pagina_da_caricare = "lista_pazienti.php?do=pager&pagina=" + pagina + "&numpag=" + numpag;
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
                <div class="elem0"><a href="#">gestione pazienti</a></div>
                <div class="elem_pari"><a onclick="javascript:load_content('div','lista_pazienti.php');" href="#">elenco pazienti</a></div>

            </div>

            <div class="titoloalternativo">
                <h1>Elenco Pazienti</h1>
                <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
                <!--<h2>creata il 17/12/2008 da brain</h2>
			<h2>creata il 17/12/2008 da brain</h2>-->
            </div>



            <div class="titolo_pag">
                <div class="comandi">
                    <div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_pazienti.php?do=add');">aggiungi paziente</a></div>
                </div>
            </div>
            <?
            if ($conta == 0) {
            ?>
                <div class="info">Nessun paziente trovato.</div>
            <?
                exit();
            } ?>


            <table id="table" class="tablesorter" cellspacing="1">
                <thead>
                    <tr>

                        <th width="5%">Codice</th>
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th width="10%">Data di Nascita</th>
                        <th width="6%">Anag</th>
                        <th width="6%">Amm</th>
                        <th width="6%">aperte</th>
                        <th width="6%">San</th>
                        <th width="6%">aperte</th>
                        <td width="5%">elimina</td>

                    </tr>
                </thead>
                <tbody>

                    <?

                    while ($row = mssql_fetch_assoc($rs)) {
                        $id = $row['idutente'];
                        $cognome = pulisci_lettura($row['cognome']);
                        $nome = pulisci_lettura($row['nome']);
                        $datanascita = formatta_data($row['datanascita']);
                        $stato_paziente = $row['stato_impegnativa'];
                        if (is_numeric($stato_paziente)) {
                            $stato_paziente_descr = get_stato_paziente_descr($stato_paziente);
                        } else {
                            $stato_paziente = 9;
                            $stato_paziente_descr = get_stato_paziente_descr($stato_paziente);
                        }
                        $CodiceUtente = $id;
                        $imp_tot = $row['imp_tot'];
                        $imp_ape = $row['imp_ape'];
                        $cart_tot = $row['cart_tot'];
                        $cart_ape = $row['cart_ape'];
                        $img_imp = "stato_red.png";
                        $img_cart = "stato_red.png";
                        if ($imp_tot == "") $imp_tot = 0;
                        if ($imp_ape == "") {
                            $imp_ape = 0;
                            $img_imp = "stato_green.png";
                        }
                        if ($cart_tot == "") $cart_tot = 0;
                        if ($cart_ape == "") {
                            $cart_ape = 0;
                            $img_cart = "stato_green.png";
                        }
                    ?>
                        <tr>

                            <td><?= $id ?></td>
                            <td><?= $cognome ?></td>
                            <td><?= $nome ?></td>
                            <td><?= $datanascita ?></td>
                            <td><a id="<?= $id ?>" style="text-decoration:none;" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?= $id ?>');" href="#"><img src="images/anagrafica.png" /></a></td>
                            <td><a id="<?= $id ?>" style="text-decoration:none;" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?= $id ?>');" href="#"><img src="images/impegnativa.png" /> - (<?= $imp_tot ?>)</a></td>
                            <td><a id="<?= $id ?>" style="text-decoration:none;" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?= $id ?>');" href="#"><img src="images/<?= $img_imp ?>" style="padding-top:4px" /> - (<?= $imp_ape ?>)</a></td>
                            <td><a id="<?= $id ?>" style="text-decoration:none;" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?= $id ?>');" href="#"><img src="images/cartella.png" /> - (<?= $cart_tot ?>)</a></td>
                            <td><a id="<?= $id ?>" style="text-decoration:none;" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?= $id ?>');" href="#"><img src="images/<?= $img_cart ?>" style="padding-top:4px" /> - (<?= $cart_ape ?>)</a></td>
                            <td>x</td>
                            <!--<td><a id="<?= $id ?>" style="text-decoration:none;" onclick="conf_cancella('<?= $id ?>');" href="#"><img src="images/remove.png" /></a></td>-->
                            <!-- <td><?= $regime_normativa ?></td>-->
                            <!-- <td><?= $stato_paziente_descr ?></td>-->

                        </tr>
                    <?
                    }

                    mssql_free_result($rs);
                    mssql_close($conn);


                    ?>


                </tbody>
            </table>
            <?
            footer_paginazione_1($conta, $page, $numpag);
            ?>
        </div>
    </div>


    <script>
        $(document).ready(function() {

            $("table").tablesorter({
                sortList: [
                    [1, 0]
                ],
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
    global $PHP_SELF;
	$conn = db_connect();
    $_SESSION['id_paziente'] = "";
    //$nome_pagina="aggiungi paziente";
    //header_new($nome_pagina,true);
    //barra_top_new();
    //top_message();
    //menu_new();

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
    <div id="lista_impegnative">
        <script>
            inizializza();
        </script>
        <script type="text/javascript" src="script/ArrayComuni.js"></script>
        <script type="text/javascript" src="script/cf.js"></script>
        <script type="text/javascript" id="js">
            /*function omocodia(value){
	if(value.length==16) {
		//alert(value);
		$.ajax({
	   type: "POST",
	   url: "omocodia.php",
	   data: "codfisc="+value,
	   success: function(msg){				
		 if (parseInt(msg)) alert("Attenzione, il Codice Fiscale inserito e' stato gia' attribuito ad altro paziente.");		
	   }
	 });
	}
	return value.toUpperCase();
}*/


            function nome_comune(value) {
                $.ajax({
                    type: "POST",
                    url: "get_comune.php",
                    data: "idcomune=" + value,
                    success: function(msg) {
                        alert(msg);
                        return msg;
                    }
                });
            }
        </script>

        <div class="padding10">
            <div class="logo"><img src="images/re-med-logo.png" /></div>

            <div id="briciola" style="margin-bottom:20px;">
                <div class="elem0"><a href="principale.php">gestione pazienti</a></div>
                <div class="elem_pari"><a onclick="javascript:load_content('div','lista_pazienti.php');" href="#">elenco pazienti</a></div>
                <div class="elem_dispari"><a href="#">aggiungi pazente</a></div>

            </div>

            <form method="post" name="form0" action="lista_pazienti_POST.php" id="myForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create" />
                <input type="hidden" name="op" value="1" />

                <div class="titolo_pag">
                    <h1>Anagrafica</h1>
                </div>

                <div class="blocco_centralcat">
                    <?
                    if (($_SESSION['UTENTE']->get_tipoaccesso() != 4) and ($_SESSION['UTENTE']->get_tipoaccesso() != 5)) {
                    ?>
                        <div class="riga">
                            <div class="rigo_mask">
                                <div class="testo_mask">cognome</div>
                                <div class="campo_mask mandatory">
                                    <input id="c_p" type="text" class="scrittura_campo solo_lettere" name="cognome" SIZE="30" maxlenght="30" />
                                </div>
                            </div>

                            <div class="rigo_mask">
                                <div class="testo_mask">nome</div>
                                <div class="campo_mask mandatory">
                                    <input id="n_p" type="text" class="scrittura_campo solo_lettere" name="nome" SIZE="30" maxlenght="30" />
                                </div>
                            </div>

                            <div class="rigo_mask">
                                <div class="testo_mask">sesso</div>
                                <div class="campo_mask mandatory">
                                    <input id="1s_p" type="radio" name="sesso" value="1" checked /> M
                                    <input id="2s_p" type="radio" name="sesso" value="2" /> F
                                </div>
                            </div>
                        </div>
                        <div class="riga">
                            <div class="rigo_mask">
                                <div class="testo_mask">data di nascita</div>
                                <div class="campo_mask mandatory data_passata">
                                    <input type="text" class="campo_data scrittura" name="data_nascita" id="data_nascita" value="" />
                                </div>
                            </div>

                            <div class="rigo_mask">
                                <div class="testo_mask">stato di nascita</div>
                                <div class="campo_mask mandatory">
                                    <select id="stato_nascita" class="scrittura" name="stato_nascita" onChange="javascript:carica_stato(form0.stato_nascita.value,1,form0.prov_nascita.value);">
                                        <option value="297" selected="selected">ITALIA</option>
                                        <?php
                                        $conn = db_connect();
                                        $query = "SELECT id, nome FROM dbo.province WHERE sigla='(EE)' order by nome";
                                        $rs = mssql_query($query, $conn);
                                        while ($row = mssql_fetch_assoc($rs)) {

                                            // setta la variabile di sessione
                                            $idprov = $row['id'];
                                            $nomeprov = htmlentities($row['nome']);

                                            print('<option value="' . $idprov . '"');
                                            print('>' . $nomeprov . '</option>' . "\n");
                                        }
                                        mssql_free_result($rs);
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <span class="rigo_mask">
                                <div class="testo_mask">provincia di nascita </div>
                                <div class="campo_mask mandatory" id="province">
                                    <select id="prov_nascita" class="scrittura " name="prov_nascita" onChange="javascript:carica_comuni(form0.prov_nascita.value,1,form0.comune_nascita.value);">
                                        <option value="" selected="selected">Seleziona una provincia</option>
                                        <?php                                        
                                        $query = "SELECT id, nome FROM dbo.province WHERE (sigla<>'(EE)') order by nome";
                                        $rs = mssql_query($query, $conn);
                                        while ($row = mssql_fetch_assoc($rs)) {

                                            // setta la variabile di sessione
                                            $idprov = $row['id'];
                                            $nomeprov = htmlentities($row['nome']);

                                            print('<option value="' . $idprov . '"');
                                            print('>' . $nomeprov . '</option>' . "\n");
                                        }
                                        mssql_free_result($rs);
                                        ?>
                                    </select>
                                </div>
                            </span>
                        </div>

                        <div class="riga">
                            <span class="rigo_mask">
                                <div class="testo_mask">Comune di nascita</div>
                                <div class="campo_mask mandatory" id="comuni">
                                    <input type="text" class="scrittura readonly" name="comune_nascita" id="comune_nascita" SIZE="30" maxlength="30" value="" readonly />

                                </div>
                            </span>


                            <div class="rigo_mask">
                                <div class="testo_mask">Codice Fiscale <a class="cambia_valore calcola_cf">calcola Codice Fiscale</a></div>
                                <div class="campo_mask mandatory codice_fiscale_omocodia">
                                    <input id="cod_fisc" type="text" class="scrittura" name="codice_fiscale" SIZE="30" maxlength="16" onKeyUp="this.value=this.value.toUpperCase()" />
                                </div>
                            </div>

                            <input type="hidden" name="stato" value="1" />
                            <!--<div class="rigo_mask">
            <div class="testo_mask">stato</div>
            <div class="campo_mask">
                    <select name="stato" class="scrittura">
                    <option value="1">attivo</option>
                    <option value="0">disattivo</option>
                    </select>
            </div>
        </div>-->
                            <div class="rigo_mask">
                                <div class="testo_mask">Reparto</div>
                                <div class="campo_mask">
                                    <select id="reparto_paziente" class="scrittura" name="reparto_paziente">
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
											echo '<option value="' . $reparto['id'] . '" ' . (!empty($reparto['id_operatore']) ? 'selected' : '') . ' >' . $reparto['nome'] . ' - ' . $reparto['sede'] . '</option>';
										} ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="riga">
       <div class="rigo_mask">
            <div class="testo_mask">Foto paziente</div>
            <div class="campo_mask nomandatory controllo_file immagini">
                <input type="file" name="foto_paziente" id="foto_paziente" class="scrittura" />
            </div>
        </div>
	</div>   -->

                </div>
                <div class="titolo_pag" style="margin-top:20px;">
                    <h1>Residenza</h1>
                </div>
                <div class="blocco_centralcat">
                    <div class="riga">

                        <div class="rigo_mask">
                            <div class="testo_mask">indirizzo</div>
                            <div class="campo_mask mandatory">
                                <input type="text" class="scrittura" name="indirizzo" SIZE="30" maxlenght="30" />
                            </div>
                        </div>

                        <span class="rigo_mask">
                            <div class="testo_mask">provincia</div>
                            <div class="campo_mask mandatory" id="province_res">
                                <select id="prov_residenza" class="scrittura" name="prov_residenza" onChange="carica_comuni(form0.prov_residenza.value,2,form0.comune_residenza.value);">
                                    <option value="">Selezionare una provincia</option>
                                    <?php
                                    $conn = db_connect();
                                    $query = "SELECT id, nome FROM dbo.province WHERE sigla <>'(EE)' order by nome";
                                    $rs = mssql_query($query, $conn);

                                    while ($row = mssql_fetch_assoc($rs)) {

                                        // setta la variabile di sessione
                                        $idprov = $row['id'];
                                        $nomeprov = htmlentities($row['nome']);

                                        print('<option value="' . $idprov . '"');
                                        print('>' . $nomeprov . '</option>' . "\n");
                                    }
                                    ?>
                                </select>
                            </div>
                        </span>
                        <span class="rigo_mask">
                            <div class="testo_mask">Comune </div>
                            <div class="campo_mask mandatory" id="comuni_res">
                                <input type="text" class="scrittura readonly" name="comune_residenza" id="comune_residenza" SIZE="30" maxlenght="30" value="" readonly />

                            </div>
                        </span>
                    </div>

                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">CAP</div>
                            <div class="campo_mask" id="cap_r">
                                <input type="text" class="scrittura" name="cap_residenza" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                        <div class="rigo_mask">
                            <div class="testo_mask">telefono</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura" name="telefono" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                        <div class="rigo_mask">
                            <div class="testo_mask">fax</div>
                            <div class="campo_mask ">
                                <input type="text" class="scrittura" name="fax" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                    </div>
                    <div class="riga">

                        <div class="rigo_mask">
                            <div class="testo_mask">cellulare</div>
                            <div class="campo_mask ">
                                <input id="cell" type="text" class="scrittura" name="cellulare" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                        <div class="rigo_mask">
                            <div class="testo_mask">e-mail</div>
                            <div class="campo_mask  email">
                                <input type="text" class="scrittura" name="email" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="titolo_pag" style="margin-top:20px;">
                    <h1>Medico Curante</h1>
                </div>
                <div class="blocco_centralcat">
                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">cognome</div>
                            <div class="campo_mask ">
                                <input type="text" class="scrittura solo_lettere" name="cognome_m" SIZE="30" maxlenght="30" />
                            </div>
                        </span><span class="rigo_mask">
                            <div class="testo_mask">nome</div>
                            <div class="campo_mask ">
                                <input type="text" class="scrittura solo_lettere" name="nome_m" SIZE="30" maxlenght="30" />
                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">indirizzo</div>
                            <div class="campo_mask ">
                                <input type="text" class="scrittura" name="indirizzo_m" SIZE="30" maxlenght="30" />
                            </div>
                        </span>
                    </div>

                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">provincia</div>
                            <div class="campo_mask " id="province_med">
                                <select id="med_residenza" class="scrittura" name="med_residenza" onChange="carica_comuni(form0.med_residenza.value,5,form0.med_comune_residenza.value);">
                                    <option value="">Selezionare una provincia</option>
                                    <?php
                                    $conn = db_connect();
                                    $query = "SELECT id, nome FROM dbo.province WHERE sigla <>'(EE)' order by nome";
                                    $rs = mssql_query($query, $conn);

                                    while ($row = mssql_fetch_assoc($rs)) {

                                        // setta la variabile di sessione
                                        $idprov = $row['id'];
                                        $nomeprov = htmlentities($row['nome']);

                                        print('<option value="' . $idprov . '"');
                                        print('>' . $nomeprov . '</option>' . "\n");
                                    }
                                    ?>
                                </select>
                            </div>
                        </span>
                        <span class="rigo_mask">
                            <div class="testo_mask">Comune </div>
                            <div class="campo_mask " id="comuni_med">
                                <input type="text" class="scrittura readonly" name="med_comune_residenza" id="med_comune_residenza" SIZE="30" maxlenght="30" value="" readonly />

                            </div>
                        </span>

                        <div class="rigo_mask">
                            <div class="testo_mask">CAP</div>
                            <div class="campo_mask" id="cap_med">
                                <input type="text" class="scrittura" name="med_cap_residenza" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                    </div>

                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">telefono</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura" name="med_telefono" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                        <div class="rigo_mask">
                            <div class="testo_mask">fax</div>
                            <div class="campo_mask ">
                                <input type="text" class="scrittura" name="med_fax" SIZE="30" maxlenght="30" />
                            </div>
                        </div>


                        <div class="rigo_mask">
                            <div class="testo_mask">cellulare</div>
                            <div class="campo_mask ">
                                <input id="cell" type="text" class="scrittura" name="med_cellulare" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                    </div>
                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">e-mail</div>
                            <div class="campo_mask  email">
                                <input type="text" class="scrittura" name="med_email" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                    </div>
                </div>



                <div class="titolo_pag" style="margin-top:20px;">
                    <h1>Genitori\Tutor</h1>
                </div>
                <div class="blocco_centralcat">
                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">cognome</div>
                            <div class="campo_mask  #condition,data_nascita,18,anni_precedenti#">
                                <input id="c_t_" type="text" class="scrittura_campo solo_lettere" name="cognome_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                        <div class="rigo_mask">
                            <div class="testo_mask">nome</div>
                            <div class="campo_mask  #condition,data_nascita,18,anni_precedenti#">
                                <input id="n_t_" type="text" class="scrittura_campo solo_lettere" name="nome_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                        <span class="rigo_mask">
                            <div class="testo_mask">sesso</div>
                            <div class="campo_mask ">
                                <input id="1s_t_" type="radio" name="sesso_t" value="1" checked /> M
                                <input id="2s_t_" type="radio" name="sesso_t" value="2" /> F
                            </div>
                        </span>
                    </div>

                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">data di nascita</div>
                            <div class="campo_mask  data_passata">
                                <input type="text" class="campo_data scrittura" name="data_nascita_t_" id="data_nascita_t_" value="<?php echo ($tut_data_nascita); ?>" />
                            </div>
                        </span>

                        <div class="rigo_mask">
                            <div class="testo_mask">stato di nascita</div>
                            <div class="campo_mask mandatory">
                                <select id="stato_nascita_tn_" class="scrittura" name="stato_nascita_tn_" onChange="javascript:carica_stato(this.value,2);">
                                    <option value="297" selected="selected">ITALIA</option>
                                    <?php
                                    $conn = db_connect();
                                    $query = "SELECT id, nome FROM dbo.province WHERE sigla='(EE)' order by nome";
                                    $rs = mssql_query($query, $conn);
                                    while ($row = mssql_fetch_assoc($rs)) {

                                        // setta la variabile di sessione
                                        $idprov = $row['id'];
                                        $nomeprov = htmlentities($row['nome']);

                                        print('<option value="' . $idprov . '"');
                                        print('>' . $nomeprov . '</option>' . "\n");
                                    }
                                    mssql_free_result($rs);
                                    ?>
                                </select>
                            </div>
                        </div>

                        <span class="rigo_mask">
                            <div class="testo_mask">provincia di nascita</div>
                            <div class="campo_mask province_tut_n">
                                <select id="prov_residenza_tn_" class="scrittura" name="prov_residenza_tn_" onChange="carica_comuni(document.getElementById('prov_residenza_tn_').value,4,document.getElementById('comune_residenza_tn_').value);">
                                    <?php
                                    $conn = db_connect();
                                    $query = "SELECT id, nome FROM dbo.province WHERE sigla <>'(EE)' order by nome";
                                    $rs = mssql_query($query, $conn);
                                    print('<option value="">Selezionare una provincia</option>' . "\n");
                                    while ($row = mssql_fetch_assoc($rs)) {

                                        // setta la variabile di sessione
                                        $idprov = $row['id'];
                                        $nomeprov = $row['nome'];

                                        print('<option value="' . $idprov . '"');
                                        print('>' . $nomeprov . '</option>' . "\n");
                                    }
                                    ?>
                                </select>
                            </div>
                        </span>
                    </div>
                    <div class="riga">
                        <span class="rigo_mask">
                            <div class="testo_mask">Comune di nascita</div>
                            <div class="campo_mask comuni_tut_n">
                                <input type="text" class="scrittura readonly" name="comune_residenza_tn" id="comune_residenza_tn_" SIZE="30" maxlenght="30" value="<?php echo ($tut_comune); ?>" readonly />

                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">Codice Fiscale <a rel="" class="cambia_valore calcola_cf_t">calcola Codice Fiscale</a></div>
                            <div class="campo_mask nomandatory codice_fiscale">
                                <input id="cod_fisc_t_" type="text" class="scrittura" name="codice_fiscale_t" SIZE="30" maxlength="16" value="<?php echo ($tut_codice_fiscale); ?>" onKeyUp="this.value=this.value.toUpperCase()" />
                            </div>
                        </span>

                        <div class="rigo_mask">
                            <div class="testo_mask">Tipologia Genitore/Tutore</div>
                            <div class="campo_mask">
                                <input type="checkbox" id="delegato_1" name="delegato_1" value="1"><label for="delegato_1"> Delegato 1 &nbsp;</label>
                                <input type="checkbox" id="delegato_2" name="delegato_2" value="1"><label for="delegato_2"> Delegato 2</label><br>
                                <input type="checkbox" id="tutor_p" name="tutor_p" value="1"><label for="tutor_p"> Tutore principale &nbsp;</label>
                                <input type="checkbox" id="tutor_1" name="tutor_1" value="1"><label for="tutor_1"> Tutore 1 &nbsp;</label>
                                <input type="checkbox" id="tutor_2" name="tutor_2" value="1"><label for="tutor_2"> Tutore 2 &nbsp;</label>
                            </div>
                        </div>

                    </div>
                    <div class="riga">
                        <div class="etichetta_campi" style="font-size:12px;">Informazioni sulla residenza del genitore\tutor</div>
                    </div>
                    <div class="riga">

                        <div class="rigo_mask">
                            <div class="testo_mask">indirizzo</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura" name="indirizzo_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>

                        <span class="rigo_mask">
                            <div class="testo_mask">provincia </div>
                            <div class="campo_mask province_tut">
                                <select id="prov_residenza_t" class="scrittura" name="prov_residenza_t" onChange="carica_comuni(form0.prov_residenza_t.value,3,form0.comune_residenza_t.value);">
                                    <?php
                                    $conn = db_connect();
                                    $query = "SELECT id, nome FROM dbo.province WHERE sigla <>'(EE)' order by nome";
                                    $rs = mssql_query($query, $conn);
                                    print('<option value="">Selezionare una provincia</option>' . "\n");
                                    while ($row = mssql_fetch_assoc($rs)) {

                                        // setta la variabile di sessione
                                        $idprov = $row['id'];
                                        $nomeprov = $row['nome'];

                                        print('<option value="' . $idprov . '"');
                                        print('>' . $nomeprov . '</option>' . "\n");
                                    }
                                    ?>
                                </select>
                            </div>
                        </span>

                        <span class="rigo_mask">
                            <div class="testo_mask">Comune </div>
                            <div class="campo_mask comuni_tut">
                                <input type="text" class="scrittura readonly" name="comune_residenza_t" id="comune_residenza_t" SIZE="30" maxlenght="30" value="<?php echo ($tut_comune); ?>" readonly />

                            </div>
                        </span>
                    </div>
                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">CAP</div>
                            <div class="campo_mask" id="cap_t">
                                <input type="text" class="scrittura " name="cap_residenza_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>

                        <div class="rigo_mask">
                            <div class="testo_mask">telefono</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura" name="telefono_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>

                        <div class="rigo_mask">
                            <div class="testo_mask">fax</div>
                            <div class="campo_mask ">
                                <input type="text" class="scrittura" name="fax_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                    </div>
                    <div class="riga">
                        <div class="rigo_mask">
                            <div class="testo_mask">cellulare</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura" name="cellulare_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>

                        <div class="rigo_mask">
                            <div class="testo_mask">e-mail</div>
                            <div class="campo_mask email ">
                                <input type="text" class="scrittura" name="email_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>

                        <div class="rigo_mask">
                            <div class="testo_mask">relazione con il paziente</div>
                            <div class="campo_mask">
                                <input type="text" class="scrittura" name="relazione_t" SIZE="30" maxlenght="30" />
                            </div>
                        </div>
                    </div>
                    <div class="riga">
                    </div>
                <?
                    }
                ?>
                </div>


                <?

                if (($_SESSION['UTENTE']->get_tipoaccesso() != 4) and ($_SESSION['UTENTE']->get_tipoaccesso() != 5)) {
                ?>
                    <div class="titolo_pag">
                        <div class="comandi">
                            <input type="submit" title="salva" value="salva" class="button_salva" />
                        </div>
                    </div>
                <?
                } else
                    print("<p>Non si dispone dei permessi necessari per aggiungere un paziente</p>");
                ?>
            </form>
            <script type="text/javascript" language="javascript">
                $(document).ready(function() {
                    $.mask.addPlaceholder('~', "[+-]");
                    $(".campo_data").mask("99/99/9999");
                    $(".solo_lettere").validation({
                        type: "alpha",
                        add: "' "
                    });
                });
            </script>
        </div>

    </div>
    <?php
    // chiude il div centrale
    //footer_new();
}

function review($id)
{

    $nome_pagina = "scheda paziente";
    $idutente = $id;
    $conn = db_connect();

    $query = "SELECT intestazione, idstruttura FROM dbo.struttura WHERE (status = 1)";
    $rs = mssql_query($query, $conn);
    if ($row = mssql_fetch_assoc($rs)) $int_str = $row['intestazione'];
    mssql_free_result($rs);

    $query = "SELECT * from re_utenti_anagrafica where idutente=$idutente ORDER BY max_data DESC";
    $rs = mssql_query($query, $conn);
    if (!$rs) error_message(mssql_error());

    if ($row = mssql_fetch_assoc($rs)) {

        $codiceutente = $row['CodiceUtente'];
        $cognome = $row['Cognome'];
        $nome = $row['Nome'];
        $comune_nascita = $row['luogonascita'];
        $data_nascita = formatta_data($row['DataNascita']);
        $foto_paziente = trim($row['foto_paziente']);
        $diagnosi = pulisci_lettura(trim($row['Diagnosi']));
        $idimpegnativa = $row['idimpegnativa'];
        //$stato_paziente=trim($row['stato_impegnativa']);
        $stato_paziente = get_stato_paziente($idutente);

        $io = strpos($foto_paziente, ".");
        if (($io == "") and ($row['Sesso'] == 1)) $foto_paziente = "male_user.jpg";
        if (($io == "") and ($row['Sesso'] == 2)) $foto_paziente = "female_user.jpg";
        if ($row['Sesso'] == 1)
            $nato_stringa = "nato il";
        else
            $nato_stringa = "nata il";
    }
    //$stato_paziente=get_stato_paziente_descr($stato_paziente);
    if (is_numeric($stato_paziente) > 0) {
        $stato_paziente_descr = get_stato_paziente_descr($stato_paziente);
    } else {
        $stato_paziente_descr = get_stato_paziente_descr(9);
        //$stato_paziente_descr="lista d'attesa";
    }
    $tipoaccesso = $_SESSION['UTENTE']->get_tipoaccesso();
    $protocollo = "";
    //echo("sdfsd".$idimpegnativa);
    if ($idimpegnativa != "") {

        $query = "SELECT * from impegnative where idimpegnativa=$idimpegnativa";
        $rs = mssql_query($query, $conn);

        if ($row = mssql_fetch_assoc($rs)) {
            $idprot = $row['idprotocollo'];
        }
        if ($idprot != "") {

            $query = "SELECT idprotocollo,codice_protocollo,descrizione from re_protocolli_compilatore_attivi WHERE idprotocollo=$idprot";
            $rs2 = mssql_query($query, $conn);
            if (!$rs2) error_message(mssql_error());

            if ($row2 = mssql_fetch_assoc($rs2)) {
                $protocollo = pulisci_lettura($row2['codice_protocollo']) . " - " . pulisci_lettura($row2['descrizione']);
            }
        }
    }
    if ($_SESSION['UTENTE']->get_privacy_cartella()) {

        $privacy_cartella = get_privacy_cartella($idutente, $_SESSION['UTENTE']->get_userid());
        if (!$privacy_cartella) {
    ?>
            <script type="text/javascript">
                alert("Attenzione!\nNon hai i permessi per visualizzare questa cartella.");
            </script>
    <?
            exit();
        }
    }

    $sql_cart_inf = "SELECT COUNT(*) as num_cart_aperte
						FROM utenti_cartelle
						WHERE idutente = $idutente AND idregime IN (52,53,59,60,61,62,64,65)
							AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'";
    $rs_cart_inf = mssql_query($sql_cart_inf, $conn);
    $row_cart_inf = mssql_fetch_assoc($rs_cart_inf);

    $show_anagrafica = $row_cart_inf['num_cart_aperte'] > 0;
    ?>

    <script type="text/javascript">
        var id_paziente = '<?= $idutente ?>';


        $(document).ready(function() {
            //mostro nascondo compilatore
            /*
            $(".aprichiudicompilatore").click(function(){
            //$("#div_testocompilato").html("");

             $("#div_cosa_fare").html("");
             //carico i dati del paziente
            		$.ajax({
            		   type: "POST",
            			 url: "ajax_dati_paziente_compilatore.php",
            			 data: "id_paziente="+<?= $idutente ?>+"&id=1",
            		   success: function(data){			   
            			 $("#div_paziente").html($.trim(data));				
            		   }
            		 });
             
             var idprot=document.getElementById("comp_idprotocollo");
             idprot.value="";
            	if ($("#barra_compilatore").is(":hidden")){
            		$(".pannelli_top").hide();

            		$("#barra_compilatore").slideDown(300 //, function(){ $("#barra_moreinfo").dropShadow({left: 4, top: 3, opacity: 0.4, blur: 4})}
            									  );
            	}		
            	else{
            		$("#barra_compilatore").slideUp(300);	
            	}
            });
            */
        });


        function carica_cosa_fare(idprotocollo) {
            $("#div_testocompilato").html("");
            if (idprotocollo != "") {
                $.ajax({
                    type: "POST",
                    url: "compilatore.php",
                    data: "idprotocollo=" + idprotocollo + "&action=1",
                    success: function(data) {

                        $("#div_cosa_fare").html(data);

                    }
                });
            } else
                alert("selezionare il protocollo");
        }

        function carica_testo_autocompilato() {
            var idprot = document.getElementById("comp_idprotocollo");
            var idcosa = document.getElementById("comp_cosafare");
            var idcartella = $("#idcartella").val();
            var idimpegnativa = $("#idimpegnativa").val();

            $.ajax({
                type: "POST",
                url: "compilatore.php",
                data: "idprotocollo=" + idprot.value + "&idcosafare=" + idcosa.value + "&idpaziente=" + id_paziente + "&action=2&idcartella=" + idcartella + "&idimpegnativa=" + idimpegnativa,
                success: function(data) {

                    $("#div_testocompilato").html(data);

                }
            });
        }
    </script>

    <div class="padding10">
        <div class="logo"><img src="images/re-med-logo.png" /></div>


        <div id="briciola" style="margin-bottom:20px;">
            <div class="elem0"><a href="principale.php">gestione pazienti</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_pazienti.php');" href="#">elenco pazienti</a></div>
            <div class="elem_dispari">scheda paziente</div>

        </div>
        <div id="informazioni-utente">
            <div class="foto" style="background: #CCC url('foto_pazienti/<?= $foto_paziente ?>') top left no-repeat"><img src="images/frame-foto.png" alt="foto utente" /></div>
            <div class="contenitore-riga">
                <div class="hide" style="padding-bottom:20px; width:100%; text-align:center;"><strong><?= pulisci_lettura($int_str) ?></strong></div>
                <div class="hide" style="padding-bottom:30px; width:100%; text-align:left;">Foglio O__/a-Ed. 02</div>
                <div class="riga">

                    <div class="box"><em>codice #</em><br /><strong><?= $codiceutente ?> </strong></div>
                    <div class="box"><em>nome</em><br /><strong><?= $nome ?></strong></div>
                    <div class="box"><em>cognome</em><br /><strong><?= $cognome ?></strong></div>
                    <div class="box"><em>regime</em><br /><strong><?= get_regime_paziente_descr($idutente) ?></strong></div>
                    <div class="box"><em>stato</em><br /><strong><?= $stato_paziente_descr ?></strong></div>
                </div>
                <div class="riga">
                    <div class="box"><?= $nato_stringa ?><br /><strong><?= $data_nascita ?></strong></div>
                    <div class="box">a<br /><strong><?= pulisci_lettura($comune_nascita) ?></strong></div>
                </div>

                <div class="riga">
                    <div class="box"><em>diagnosi</em><br /><strong><?= $diagnosi ?></strong></div>
                </div>

                <div class="riga">
                    <div class="box"><em>protocollo D\T</em><br /><strong><?= $protocollo ?></strong></div>
                </div>
            </div>
            <? if (($tipoaccesso != 1) and ($tipoaccesso != 4)) { ?>
                <div id="compilatore" class="aprichiudicompilatore"><img class="no_print" src="images/magic_wand.png" title="compilatore" /></div>
            <?
            }
            ?>
        </div>


        <div id="contenuto-centrale">
            <!-- inizio parte tab -->


            <div id="container-1">
                <ul>

                    <li><a href="#fragment-2"><span>
                                <div onclick="javascript:reload_pagina();">Anagrafica</div>
                            </span></a></li>
                    <li><a href="#fragment-3"><span>Area Amministrativa</span></a></li>
                    <? if (($tipoaccesso != 1) and ($tipoaccesso != 4)) { ?>
                        <li><a href="#fragment-4"><span>
                                    <div>Area Sanitaria</div>
                                </span></a></li>
                    <? } ?>
                </ul>

                <!-- ANAGRAFICA -->
                <div id="fragment-2">

                    <div id="container-3">
                        <ul>
                            <li><a href="re_pazienti_anagrafica.php?do=edit&id=<?= $id ?>"><span>modifica anagrafica</span></a></li>
                            <? if (($tipoaccesso != 1) and ($tipoaccesso != 4) and ($tipoaccesso != 5)) { ?>
                                <li><a href="re_pazienti_anagrafica.php?do=add_allegati&id=<?= $id ?>"><span>aggiungi allegati</span></a></li>
                            <?
                            }
                            $idutente = $id;
                            $conn = db_connect();

                            $query_al = "SELECT * from utenti_allegati where (idutente=$idutente) and(stato=1) and (cancella='n')";
                            //echo($query);	
                            $rs_al = mssql_query($query_al, $conn);

                            if (!$rs_al) error_message(mssql_error());
                            $conta = mssql_num_rows($rs_al);
                            ?>
                            <li><a href="re_pazienti_anagrafica.php?do=show_allegati&id=<?= $id ?>"><span>visualizza allegati (<?= $conta ?>)</span></a></li>
                        </ul>
                    </div>
                </div>
                <!-- FINE ANAGRAFICA -->
                <div id="fragment-3">
                    <div id="container-4">
                        <ul id="tab_block" class="test">
                            <li><a href="re_pazienti_amministrativa.php?do=&id=<?= $id ?>"><span>elenco impegnative</span></a></li>
                            <?
                            if ($_SESSION['UTENTE']->get_tipoaccesso() != 4 and ($tipoaccesso != 5)) {
                            ?>
                                <li><a href="re_pazienti_amministrativa.php?do=add&id=<?= $id ?>"><span>aggiungi impegnativa</span></a></li>
                                <li><a href="re_pazienti_amministrativa.php?do=add_allegati&id=<?= $id ?>"><span>aggiungi allegati</span></a></li>
                            <?
                                $idutente = $id;
                                $conn = db_connect();

                                $query_al = "SELECT * from utenti_allegati_imp where (idutente=$idutente) and(stato=1) and (cancella='n')";
                                //echo($query);	
                                $rs_al = mssql_query($query_al, $conn);

                                if (!$rs_al) error_message(mssql_error());
                                $conta = mssql_num_rows($rs_al);
                            }
                            ?>
                            <li><a href="re_pazienti_amministrativa.php?do=show_allegati&id=<?= $id ?>"><span>visualizza allegati (<?= $conta ?>)</span></a></li>
                            <!-- <li><a href="re_pazienti_amministrativa.php?do=show_note&id=<?= $id ?>"><span>note amministrative</span></a></li> -->
                            <!--<li><a href="re_pazienti_amministrativa.php?do=show_presenze&id=<?= $id ?>"><span>gestione presenze</span></a></li>-->
                        </ul>
                    </div>

                </div>
                <div id="fragment-4">
                    <div id="container-5">
                        <ul>
                            <li><a href="re_pazienti_sanitaria.php?do=&id=<?= $id ?>"><span>elenco cartelle</span></a></li>
                            <li><a href="re_pazienti_sanitaria_cartelle.php?do=add&id=<?= $id ?>&codiceutente=<?= $codiceutente ?>"><span>nuova cartella</span></a></li>
                            <li><a href="re_pazienti_sanitaria_cartelle.php?do=addvisita&id=<?= $id ?>"><span>elenco moduli</span></a></li>
                            <!--<li><a href="re_pazienti_sanitaria_cartelle.php?do=visualizza_test_clinici&idpaziente=<?= $id ?>"><span>storico test clinici</span></a></li>-->
                            <li><a href="re_pazienti_sanitaria.php?do=add_allegati&id=<?= $id ?>"><span>aggiungi allegati</span></a></li>
                            <?
                            $idutente = $id;
                            $conn = db_connect();
                            $query_al = "SELECT * from utenti_allegati_san where (idutente=$idutente) and(stato=1) and (cancella='n')";
                            $rs_al = mssql_query($query_al, $conn);
                            if (!$rs_al) error_message(mssql_error());
                            $conta = mssql_num_rows($rs_al);
                            ?>
                            <li><a href="re_pazienti_sanitaria.php?do=show_allegati&id=<?= $id ?>"><span>visualizza allegati (<?= $conta ?>)</span></a></li>
                            <? if ($show_anagrafica) { ?>
                                <li><a href="re_pazienti_sanitaria.php?do=show_anagrafica_farmaci&id=<?= $id ?>"><span>farmacia paziente</span></a></li>
                            <? } ?>
                        </ul>
                    </div>

                </div>
            </div>

            <!-- fine parte tab -->
        </div>
        <?
        // parte relativa alla selezione automatica dei tab
        $tipoaccesso = $_SESSION['UTENTE']->get_tipoaccesso();

        if (($tipoaccesso == 2) or ($tipoaccesso == 3) or ($tipoaccesso == 5))
            $main_container = 3;
        else
            $main_container = 2;

        $sub_container = 1;
        $sub_container_tab = 1;

        if (isset($_REQUEST['main_container']))
            $main_container = $_REQUEST['main_container'];

        if (isset($_REQUEST['sub_container']))
            $sub_container = $_REQUEST['sub_container'];

        if (isset($_REQUEST['sub_container_tab']))
            $sub_container_tab = $_REQUEST['sub_container_tab'];

        ?>
        <script>
            function reload_pagina() {
                //$("#layer_nero2").toggle();
                $('#paziente').innerHTML = "";
                pagina_da_caricare = "re_pazienti_anagrafica.php?do=edit&id=<?= $id ?>";
                $("#paziente").load(pagina_da_caricare, '', function() {
                    loading_page('loading');
                });
            }


            $('#container-1').tabs(<?= $main_container ?>);
            $('#container-<?= $sub_container ?>').tabs(<?= $sub_container_tab ?>);
            $('#container-2').tabs({
                remote: true
            }, loading_page("loading"));
            $('#container-3').tabs({
                remote: true
            }, loading_page("loading"));
            $('#container-4').tabs({
                remote: true
            }, loading_page("loading"));
            $('#container-5').tabs({
                remote: true
            }, loading_page("loading"));

            $('.rounded').corners();
        </script>
        <?
        //footer_new();
    }


    if (isset($_SESSION['UTENTE'])) {

        if (!isset($do)) $do = '';
        $back = "principale.php";

        // verifica i permessi
        if (in_array($id_permesso, $_SESSION['PERMESSI'])) {
        ?>
            <script type="text/javascript" src="script/jquery.tablesorter/tablesorter/addons/pager/jquery.tablesorter.pager_utenti.js"></script>
            <?

            if (empty($_POST)) $_POST['action'] = "";


            switch ($_POST['action']) {

                case "create":
                    // verifica i permessi..
                    if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
                        error_message("Permessi insufficienti per questa operazione!");
                    else create();
                    break;

                case "ricerca_avanzata":
                    // verifica i permessi..
                    if (!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
                        error_message("Permessi insufficienti per questa operazione!");
                    else ricerca_avanzata();
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

                case "pager":
                    show_list($_REQUEST['pagina'], $_REQUEST['numpag']);
                    break;

                default:
                    show_list(1, 20);
                    break;
            }
            ?>
            <script type="text/javascript" src="script/jquery.tablesorter/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
    <?
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