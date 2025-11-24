<?php

// inclusione delle classi
include_once('include/class.User.php');
//include_once('include/class.Struttura.php');

// inizializza le variabili di sessione
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

// nome della tabella
$tablename = 'settori';
$id_permesso = $id_menu = 16;
$pagetitle = "azzeramento dei settori";


/********************************************************************
* funzione resoconto 												*
*********************************************************************/
function resoconto() {

	global $PHP_SELF, $tablename, $pagetitle;

	$conn = db_connect();

	open_center();
?>
	<form method="post" name="scelti" action="<?php echo $PHP_SELF ?>">
	<div id="titolo_pag"><h1><small>conferma azzeramento settori</small></h1></div>

	<div class="blocco_centralcat">
	<?php
	
	$n = $_POST['n'];
	//echo "settori totali ".$n;

	$_SESSION['SCELTI'] = array();
		
	for($i=1; $i<=$n; $i++) {

		if(isset($_POST['scelta'.$i]))
			array_push($_SESSION['SCELTI'], $_POST['scelta'.$i]);	
	}
	
	//print_r($_SESSION['SCELTI']);
	
	$conn = db_connect();

	print('<div class="titolo_par"><em>Totale settori selezionati: <strong>'.sizeof($_SESSION['SCELTI']).'</strong></em></div>'."\n");

	print('<ul>'."\n");
	
	foreach ($_SESSION['SCELTI'] as $element) {

		// fa la query dei record postati
		$query = "SELECT nome,status FROM $tablename WHERE $tablename.id='".$element."'";
	
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());

		if($row = mssql_fetch_assoc($rs)){
	
			$nome = $row['nome'];
			$status = $row['status'];
	
			print('<li>');
			print('<span class="id_notizia_cat">'.$element.'</span>'."\n");
			print('<span class="notizia_cat">'."\n");
			if(!$status) 
				print ('<span class="offline">'.$nome.' (offline)</span>'."\n");
			else 
				print($nome);
			print('</span>'."\n");
			
			print('</li>'."\n");
		}

	// fine for
	}

	print('</ul>'."\n");
	print("\n".'<input type="hidden" name="n" value="'.$num_rows.'" />'."\n");
	?>
	</div>
	<div class="barra_inf">
		<div class="comandi">
			<input type="submit" name="action" value="azzera" />
		</div>
	</div>
	</form>
<?php
	close_center();
}



/********************************************************************
* funzione conferma 												*
*********************************************************************/
function conferma() {

	global $PHP_SELF, $tablename, $pagetitle;

	open_center();
?>
	<form method="post" name="scelti" action="<?php echo $PHP_SELF ?>">
	<input type="hidden" name="action" value="finito" />
	<div id="titolo_pag"><h1><small>conferma azzeramento settori</small></h1></div>

	<div class="blocco_centralcat">
		<div id="messaggio_cancella">
			<strong>ATTENZIONE: QUESTA OPERAZIONE AZZERERA' TUTTI I SETTORI SELEZIONATI RIPORTANDO TUTTE LE STRUTTURE A 'NON CORRETTE'.<br />SI VUOLE CONTINUARE?</strong>
		</div>
		<div id="tasti_cancella">
			<span class="tasto_canc">
			<input class="larghezza_bottone" type="submit" value="si" name="choice" />
			</span>
			<input class="larghezza_bottone" type="submit" value="no" name="choice" />
		</div>

	</div>
	</form>
<?php
	close_center();
}


/****************************************************************
* funzione azzera()												*
*****************************************************************/
function azzera() {

	global $tablename;

	$conn = db_connect();

	// azzera i settori selezionati
	foreach ($_SESSION['SCELTI'] as $element) {

		// PERICOLOSA !!!!!
		$query = "UPDATE $tablename SET azzerato='1', data='".date('d/m/Y')."' WHERE id='".$element."'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());
		
		$query_struct = "UPDATE strutture SET updcount=0,datains='".date('d/m/Y')."',orains='".date("H:m")."',opeins='".$_SESSION['UTENTE']->get_userid()."',corretto='n' WHERE (idsettore='".$element."' AND cancella='n')";
		$rs = mssql_query($query_struct, $conn);
		if(!$rs) error_message(mssql_error());
		
	}
	
	ob_start();
	Header("Location: principale.php");
	ob_end_flush();
}



/************************************************************************
 funzione show_list()      												*
*************************************************************************/
function show_list(){

	global $PHP_SELF, $tablename, $pagetitle, $id_menu, $id_permesso;

	// crea la connessione
	$conn = db_connect();

	// azzera la variabile di sessione
	$_SESSION['SCELTI'] = array();
	
	// apre il div centrale
	open_center();

	print('<form method="post" name="lista" action="'.$PHP_SELF.'">'."\n");
	print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>'."\n");
	print('<div class="blocco_centralcat">'."\n");
	
	// fa la query dei record non cancellati
	$query = "SELECT id,nome,status,azzerato,data FROM $tablename WHERE $tablename.cancella='n' ORDER BY $tablename.nome";
	//echo $query;

	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	
	$num_rows = mssql_num_rows($rs);

	print('<div class="titolo_par"><em>Totale settori trovati: <strong>'.$num_rows.'</strong></em></div>'."\n");
	print('<ul>'."\n");

	$count = 1;

	while($row = mssql_fetch_assoc($rs)){

		$id = $row['id'];
		$nome = $row['nome'];
		$status = $row['status'];
		$azzerato = $row['azzerato'];
		$data = $row['data'];

		// recupera le strutture corrette e non corrette
		$query_struct = "SELECT count(id) FROM strutture WHERE (idsettore='$id' AND corretto='y')";
		$ss = mssql_query($query_struct, $conn);
		if(!$ss) error_message(mssql_error());
		if($riga = mssql_fetch_row($ss)) {
			$corrette = $riga[0];
		}
		mssql_free_result($ss);
		$query_struct = "SELECT count(id) FROM strutture WHERE (idsettore='$id' AND corretto='n')";
		$ss = mssql_query($query_struct, $conn);
		if(!$ss) error_message(mssql_error());
		if($riga = mssql_fetch_row($ss)) {
			$noncorrette = $riga[0];
		}

		print('<li>');
		print('<span class="id_notizia_cat">'.$id.'</span>'."\n");
		print('<span class="notizia_cat">'."\n");

		print('<input type="checkbox" name="scelta'.$count.'" value="'.$id.'" />');

		if(!$status) 
			print ('<span class="offline">'.$nome.' (offline)</span>'."\n");
		else 
			print($nome);

		print(' ( <img src="images/sem_verde.gif" title="strutture corrette"> <strong>'.$corrette.'</strong> <img src="images/sem_rosso.gif" title="strutture non corrette"> <strong>'.$noncorrette.'</strong> )');
		
		if($azzerato) print(' <span class="evidenziato">(azzerato il '.$data.')</span>');

		print('</span>'."\n");
	
		print('</li>'."\n");
		
		$count++;			
	}
	print('</ul>'."\n");
	print("\n".'<input type="hidden" name="n" value="'.$num_rows.'" />'."\n");

	print("\n".'</div>'."\n");
	?>
	<div class="barra_inf">
		<div class="comandi">
			<input type="button" value="seleziona tutti" onClick="javascript:sel_tutti(<?php print $num_rows; ?>);" />
			<input type="button" value="deseleziona tutti" onClick="javascript:desel_tutti(<?php print $num_rows; ?>);" />
			<input type="submit" name="action" value="avanti" onClick="javascript: return controlla_scelte(<?php print $num_rows; ?>);" />
		</div>
	</div>
	</form>

	<?php
	// chiude il div centrale
	close_center();
}



// controllo d'accesso
if(isset($_SESSION['UTENTE'])) {

	// verifica i permessi
	if($_SESSION['UTENTE']->is_root()) {


		// recupera i dati di sessione dell'utente
		$objUser = $_SESSION['UTENTE'];

		if(isset($_POST['choice']) AND $_POST['choice']=='no') {
			ob_start();
			Header("Location: principale.php?page=".$PHP_SELF);
			ob_end_flush();
		}

		if($_POST['action'] != 'finito') html_header();

		if(isset($_POST['action'])) {
			switch($_POST['action']) {
		
				case "avanti":
					resoconto();
				break;
	
				case "azzera":
						conferma();
				break;
	
				case "finito":
						if($_POST['choice']=="si") azzera();
				break;
		
			}

		} else {
		
			show_list();
		}

		html_footer();
	

	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>