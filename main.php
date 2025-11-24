<?php

// inizializza le variabili di sessione
include_once('include/class.User.php');
include_once('include/class.Struttura.php');
include_once('include/session.inc.php');
include_once('include/functions.inc.php');

//$rule ='none';
// recupera i dati di sessione dell'utente
$objUser = $_SESSION['UTENTE'];


/************************************************************
* funzione main()      					*
*************************************************************/
function main($uid){

	global $PHP_SELF;

	// riazzera ricalcola
	if(isset($_SESSION['RICALCOLA'])) $_SESSION['RICALCOLA'] = true;
	
	$pagetitle = "menu principale";
	
	// apre il div centrale
	//open_center_home();
	open_center();

	print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>'."\n");
	print('<div class="blocco_centralcat">'."\n");
	
	// unsetta la variabile di sessione
	if(isset($_SESSION['VENGODARICERCA']))
		$_SESSION['VENGODARICERCA'] = false;

	if(isset($_SESSION['ORDINAPER'])) unset($_SESSION['ORDINAPER']);
	if(isset($_SESSION['ORDINAPER2'])) unset($_SESSION['ORDINAPER2']);
	if(isset($_SESSION['ID_GERARCHIA'])) unset($_SESSION['ID_GERARCHIA']);
	if(isset($_SESSION['LIVELLO_GERARCHIA'])) unset($_SESSION['LIVELLO_GERARCHIA']);
	?>
	
	<?php

	$key = 0;

	$conn = db_connect();

	//print_r ($_SESSION['PERMESSI']);
	
	for($i=1; $i<=5; $i++) {
	
		print('<div class="livello">'."\n");
		echo "<h2>livello ".$i."</h2>";
		$key = 0;
		
		//foreach($_SESSION['PERMESSI'] as $element) {
	
				$query = "SELECT id,nome,link FROM menu WHERE (livello = ".$i.") AND (status = 1) ORDER BY ordine";

				$result = mssql_query($query, $conn);
				if(!$result) error_message(mssql_error());

				while($row = mssql_fetch_assoc($result)) {

					
					$id = $row['id'];
					$nome_menu = $row['nome'];
					$link_menu = $row['link'];
					
					// se si ha il permesso, stampa il bollino
					if(in_array($id, $_SESSION['PERMESSI'])) {
			
						print ('<div id="box_gestione">'."\n");
						print ('<div class="logo_gestione">'."\n");
						print ('<a id="ant'.$id.'" class="img_gestione" href="principale.php?page='.$link_menu.'" title="'.$nome_menu.'"');
						print(' target="_parent">');
						print ('</a>'."\n");
						print ('</div>'."\n");

						print ('<div class="testo_gestione">');
						print ('<a href="principale.php?page='.$link_menu.'" title="'.$nome_menu.'"');
						print(' target="_parent">');
						print($nome_menu.'</a>'."\n");
						print ('</div>'."\n");
										
						print('</div>'."\n");
						$key++;

					}
					
		}
		if(!$key) print('<br /><span class="evidenziato">Nessuna funzione disponibile in questo livello.</span>');


		print('</div>'."\n");
	}
	
	print('</div>'."\n");

	/*
	// recupera gli ordini ancora inevasi
	$query = "SELECT * FROM ordini_web WHERE cancella='n' AND stato='v'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	print('<div id="titolo_pag"><h1><small>Ordini web</small></h1></div>'."\n");
	print('<div class="blocco_centralcat">'."\n");

	print('<div class="titolo_par">Ordini non ancora evasi: <strong>'.mssql_num_rows($result).'</strong>');
	if(mssql_num_rows($result))
		print(' <a href="ordini_web.php?visualizza=v" target="_self">[visualizza]</a>');

	print('</div>'."\n");
	print('</div>'."\n");
	
	
	//controllo disattivazione ordini
	$data_tmp=date("Y")."-".date("m")."-".date("d");
	$query="SELECT * FROM ordini_web WHERE (cancella='n') AND (stato = 's') ";
	$query.="AND (data_scadenza <= CONVERT(DATETIME,'".$data_tmp."', 102))";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	$num=mssql_num_rows($result);
	if ($num>0){
		while($row = mssql_fetch_assoc($result)){
			$query = "UPDATE ordini_web SET 				
				stato = 'n',
				opeagg=".$_SESSION['UTENTE']->get_userid()." WHERE (id=".$row['id'].")";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error("Errore in update scadenza ordine"));
				invia_mail_ordini($row['idstruttura'],$row['idpacchetto'],'1',$row['data_attivazione'],$row['data_scadenza'],"0");
		}
		print('<div class="titolo_par">Ordini scaduti: <strong>'.$num.'</strong>');	
		print(' <a href="ordini_web.php?visualizza=n" target="_self">[visualizza]</a>');
		print('</div>'."\n");	
	}
	
*/
	//print_r($_SESSION['PERMESSI']);
	// chiude il div centrale
	close_center();

}


// controllo d'accesso
//if(authorize_me($_SESSION['USERID'], $rule)) {
if(isset($_SESSION['UTENTE'])) {

	include_once('include/aggiorna.php');

	if(!isset($do))	{

		html_header();
		main($_SESSION['UTENTE']);
		html_footer();
	}
	else if($do=='logout')
		logout();

} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}



?>
