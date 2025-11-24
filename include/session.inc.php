<?php
// inizializza la sessione
if ( ! session_id() ) {
   session_start();
}

// registra le variabili di sessione

if(!session_is_registered('UTENTE')) session_register('UTENTE');
if(!session_is_registered('PERMESSI')){ session_register('PERMESSI'); $_SESSION['PERMESSI'] = array(); }

if(!session_is_registered('STRUTTURA')) session_register('STRUTTURA');

if(!session_is_registered('STATO')) session_register('STATO');
if(!session_is_registered('ANNUARIO')) session_register('ANNUARIO');
if(!session_is_registered('NOME_ANNUARIO')) session_register('NOME_ANNUARIO');
if(!session_is_registered('SETTORE')) session_register('SETTORE');
if(!session_is_registered('NOME_SETTORE')) session_register('NOME_SETTORE');
if(!session_is_registered('REGIONE')) session_register('REGIONE');
if(!session_is_registered('NOME_REGIONE')) session_register('NOME_REGIONE');
if(!session_is_registered('PROVINCIA')) session_register('PROVINCIA');
if(!session_is_registered('NOME_PROVINCIA')) session_register('NOME_PROVINCIA');

if(!session_is_registered('BISNONNO')) session_register('BISNONNO');
if(!session_is_registered('NONNO')) session_register('NONNO');
if(!session_is_registered('PADRE')) session_register('PADRE');

if(!session_is_registered('I_MANAGER')) session_register('I_MANAGER');
if(!session_is_registered('MANAGERS')) session_register('MANAGERS');
if(!session_is_registered('DATA_MANAGERS')) session_register('DATA_MANAGERS');
if(!session_is_registered('STRUTTURE_FAX_MANAGERS')) session_register('STRUTTURE_FAX_MANAGERS');

if(!session_is_registered('I_IMMAGINI')) session_register('I_IMMAGINI');
if(!session_is_registered('IMMAGINI')) session_register('IMMAGINI');
if(!session_is_registered('DATA_IMMAGINI')) session_register('DATA_IMMAGINI');

if(!session_is_registered('I_PRODOTTO')) session_register('I_PRODOTTO');
if(!session_is_registered('PRODOTTI')) session_register('PRODOTTI');
if(!session_is_registered('DATA_PRODOTTI')) session_register('DATA_PRODOTTI');
if(!session_is_registered('STRUTTURE_FAX_PRODOTTI')) session_register('STRUTTURE_FAX_PRODOTTI');

if(!session_is_registered('I_ALTRODATO')) session_register('I_ALTRODATO');
if(!session_is_registered('ALTRIDATI')) session_register('ALTRIDATI');
if(!session_is_registered('DATA_ALTRIDATI')) session_register('DATA_ALTRIDATI');
if(!session_is_registered('STRUTTURE_FAX_ALTRIDATI')) session_register('STRUTTURE_FAX_ALTRIDATI');

if(!session_is_registered('NXPAGINA')) session_register('NXPAGINA');
if(!session_is_registered('TROVATE')) session_register('TROVATE');
if(!session_is_registered('TROVATE_M')) session_register('TROVATE_M');
if(!session_is_registered('TROVATE_TUTTE')) session_register('TROVATE_TUTTE');
if(!session_is_registered('ORDINAPER')) session_register('ORDINAPER');
if(!session_is_registered('ORDINAPER2')) session_register('ORDINAPER2');
if(!session_is_registered('QUALI')) session_register('QUALI');
if(!session_is_registered('BACK')) session_register('BACK');

if(!session_is_registered('QUERY')) session_register('QUERY');
if(!session_is_registered('RS')) session_register('RS');
if(!session_is_registered('AGG_MULT')) session_register('AGG_MULT');
if(!session_is_registered('AZIONE')) session_register('AZIONE');
if(!session_is_registered('FLAG')) session_register('FLAG');
if(!session_is_registered('RICALCOLA')) session_register('RICALCOLA');
if(!session_is_registered('SCELTI')) session_register('SCELTI');
if(!session_is_registered('TIPOINVIO')) session_register('TIPOINVIO');

if(!session_is_registered('IDCAMPAGNA')) session_register('IDCAMPAGNA');

if(!session_is_registered('USERID')) session_register('USERID');
if(!session_is_registered('USERNAME')) session_register('USERNAME');
if(!session_is_registered('IS_ADMIN')) session_register('IS_ADMIN');

if(!session_is_registered('PAROLA_RICERCA')) session_register('PAROLA_RICERCA');
if(!session_is_registered('RICERCA_ALTRIDATI')) session_register('RICERCA_ALTRIDATI');

if(!session_is_registered('pagina_precedente')) session_register('pagina_precedente');
if(!session_is_registered('pagina_chiamata')) session_register('pagina_chiamata');

if(!session_is_registered('MACRO_ord')) session_register('MACRO_ord');
if(!session_is_registered('ORDINI_ord')) session_register('ORDINI_ord');
if(!session_is_registered('STRUTTURA_ord')) session_register('STRUTTURA_ord');
if(!session_is_registered('CONNESSIONE')) session_register('CONNESSIONE');
if(!session_is_registered('businesskey_usafirma')) session_register('businesskey_usafirma');
if(!session_is_registered('id_paziente')) session_register('id_paziente');
if(!session_is_registered('query_ex')) session_register('query_ex');
if(!session_is_registered('error_handler')) { session_register('error_handler');$_SESSION['error_handler'] = array();}


?>
