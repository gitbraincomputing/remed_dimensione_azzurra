<?php



// inizializza le variabili di sessione

include_once('include/class.User.php');

//include_once('include/class.Struttura.php');

include_once('include/class.Ricerca.php');

include_once('include/session.inc.php');

include_once('include/functions.inc.php');

include_once('include/dbengine.inc.php');



$tablename = 'strutture';





/****************************************************************************

* funzione form_cerca_struttura						*

******************************************************************************/

function form_cerca_struttura(){



	global $PHP_SELF; 



	$pagetitle = "effettua una ricerca";

	$url = 'ricerca.php';


	if(isset($_SESSION['AGG_MULT']) && $_SESSION['AGG_MULT'] && !isset($_POST['agg_mult']))
		$_SESSION['AGG_MULT']=0;

	// apre il div centrale

	open_center();



	print('<form method="post" name="form0" action="'.$PHP_SELF.'">'."\n");

	?>

	<div class="barra_inf">

		<div class="comandi">

			<!--<input type="image" src="images/aggiungi.gif" name="action" value="aggiungi" />
			&nbsp;&nbsp;&nbsp; -->

			<input type="submit" name="action" value="cerca struttura" />

			<!-- <input type="hidden" name="action" value="cerca struttura" /> -->

		</div>

	</div>

	<?php

	print('<div class="blocco_centralcat">'."\n");

	print('<div class="titolo_par">Compilare i campi con i criteri di ricerca desiderati.</div>'."\n");

	print('<input type="hidden" name="n_campi" value="8" />'."\n");

	//print('<input type="hidden" name="url_ricerca" value="risultati_strutture.php" />'."\n");

	?>



	<span class="rigo_mask">

		<div class="testo_mask">annuario</div>

		<div class="campo_mask">

		<?php

		$conn = db_connect();

	

		// annuario

		$query = "select id,nome FROM annuario WHERE cancella='n' ORDER BY id";

		$result = mssql_query($query, $conn);

		if(!$result) error_message(mssql_error());

		print('<select class="scrittura" name="annuario" onChange="cambia_finestra(\''.$url.'\');">');

		

		print('<option value="0"');

		if(!$_SESSION['ANNUARIO'] OR $_REQUEST['annuario']==0) echo " selected";

		print('>TUTTI</option>');

	

		while($row = mssql_fetch_assoc($result)) {

	

			$id = $row['id'];

			$nome = $row['nome'];

			print('<option value="'.$id.'"');

			if(isset($_SESSION['ANNUARIO']))

				if($_SESSION['ANNUARIO']==$id AND !isset($_REQUEST['annuario'])) echo " selected";

			if(isset($_REQUEST['annuario']))

				if($_REQUEST['annuario']==$id) echo " selected";

			print('>'.$nome.'</option>'."\n");

	

		}

		print('</select>');

		?>

		</div>

	</span>



	<span class="rigo_mask">

		<div class="testo_mask">settore</div>

		<div class="campo_mask">

	<?php

	// settore

	if(isset($_REQUEST['annuario']))

			$query = "select id,nome FROM settori WHERE annuario='".$_REQUEST['annuario']."' AND cancella='n' ORDER BY nome";

	else $query = "select id,nome FROM settori WHERE annuario='".$_SESSION['ANNUARIO']."' AND cancella='n' ORDER BY nome";



	$result = mssql_query($query, $conn);

	if(!$result) error_message(mssql_error());



	print('<select class="scrittura" name="settore">');

	print('<option value="0"');

	if(!$_SESSION['SETTORE'] OR $_REQUEST['settore']==0) echo " selected";

	print('>TUTTI</option>');



	

	while($row = mssql_fetch_assoc($result)) {



		$id = $row['id'];

		$nome = $row['nome'];

		print('<option value="'.$id.'"');

		if(isset($_SESSION['SETTORE']))

				if($_SESSION['SETTORE']==$id AND !isset($_REQUEST['settore'])) echo " selected";

		if(isset($_REQUEST['settore']))

			if($_REQUEST['settore']==$id) echo " selected";

		print('>'.$nome.'</option>'."\n");

	}

	print('</select>');

	?>

		</div>

	</span>



	<span class="rigo_mask">

		<div class="testo_mask">regione</div>

		<div class="campo_mask">

	<?php



	// regioni

	$query = "select id,nome FROM regioni WHERE cancella='n' ORDER BY nome";



	$result = mssql_query($query, $conn);

	if(!$result) error_message(mssql_error());

	print('<select class="scrittura" name="regione" onChange="cambia_finestra(\''.$url.'\');">');

	

	print('<option value="0"');

	if(!$_SESSION['REGIONE'] OR $_REQUEST['regione']==0) echo " selected";

	print('>TUTTE</option>');



	while($row = mssql_fetch_assoc($result)) {



		$id = $row['id'];

		$nome = $row['nome'];

		print('<option value="'.$id.'"');

		if(isset($_SESSION['REGIONE']))

				if($_SESSION['REGIONE']==$id AND !isset($_REQUEST['regione'])) echo " selected";

		if(isset($_REQUEST['regione']))

			if($_REQUEST['regione']==$id) echo " selected";

		print('>'.$nome.'</option>'."\n");

	}

	print('</select>');

	?>

		</div>

	</span>



	<span class="rigo_mask">

		<div class="testo_mask">provincia</div>

		<div class="campo_mask">

	<?php



	// province

	if(isset($_REQUEST['regione']))

		$query = "select id,nome FROM province WHERE idregione='".$_REQUEST['regione']."' AND cancella='n' ORDER BY nome";

	else $query = "select id,nome FROM province WHERE cancella='n' ORDER BY nome";



	$result = mssql_query($query, $conn);

	if(!$result) error_message(mssql_error());



	print('<select class="scrittura" name="provincia">');

	

	print('<option value="0"');

	if(!$_SESSION['PROVINCIA'] OR $_REQUEST['provincia']==0) echo " selected";

	print('>TUTTE</option>');



	while($row = mssql_fetch_assoc($result)) {



		$id = $row['id'];

		$nome = $row['nome'];

		print('<option value="'.$id.'"');

		if(isset($_SESSION['PROVINCIA']))

			if($_SESSION['PROVINCIA']==$id) echo " selected";

		print('>'.$nome.'</option>'."\n");

	}

	print('</select>');

	?>

			</div>

		</span>

		<span class="rigo_mask">

			<div class="testo_mask">id struttura</div>

			<div class="campo_mask">

				<input type="text" class="lettura_piccolo" name="q_id" size="50" maxlength="255" />&nbsp;

				<!-- <input type="image" src="images/search_pic.gif" title="cerca struttura per id" /><br /> -->
				<br />
				<em>(separare gli id con la virgola per effettuare una ricerca multipla)</em>

			</div>

		</span>


		<span class="rigo_mask">
	
			<div class="testo_mask"><strong>agg. multiplo</strong></div>
	
			<div class="campo_mask">
	
			<?php
			if(isset($_SESSION['AGG_MULT']) && $_SESSION['AGG_MULT'])
				print('<input type="checkbox" name="agg_mult" checked /> <strong>selezionato</strong>');
			else print('<input type="checkbox" name="agg_mult" /> <em>(selezionare nel caso di un aggiornamento multiplo)</em>');?>
			
			</div>
	
		</span>

		<div class="linea"></div>

		<span class="rigo_mask">
			<div class="testo_mask">stato</div>
			<div class="campo_mask">
				<input type="radio" name="quali_ricerca" value="0" checked /> 
				<img src="images/sem_tutte.gif" title="tutte" /> tutte
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="quali_ricerca" value="1" />
				<img src="images/sem_rosso.gif" title="struttura non corretta" /> non corrette
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="quali_ricerca" value="3" />
				<img src="images/sem_arancio.gif" title="struttura da convalidare" /> da convalidare
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="quali_ricerca" value="2" /> 
				<img src="images/sem_verde.gif" title="struttura corretta" /> corrette
			</div>
		</span>
		
		<div class="linea"></div>

		<span class="rigo_mask">
			<div class="testo_mask">visualizza</div>
			<div class="campo_mask">
				<input type="radio" name="quali_stato" value="1" checked /> solo on-line
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="quali_stato" value="0" /> solo off-line
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="quali_stato" value="t" /> tutte le strutture
				
			</div>
		</span>

<div class="linea"></div>

		<span class="rigo_mask">

			<div class="testo_mask">nome struttura</div>

			<div class="campo_mask">

				<input type="text" class="scrittura" name="q_0" size="50" maxlength="255" />

				 <input type="radio" name="c_0" value="3" checked /> contiene

				 <input type="radio" name="c_0" value="1" /> uguale a

				 <input type="radio" name="c_0" value="2" /> comincia per

			</div>

		</span>



		<span class="rigo_mask">

			<div class="testo_mask">indirizzo</div>

			<div class="campo_mask">

				<input type="text" class="scrittura" name="q_1" size="50" maxlength="255" />

				 <input type="radio" name="c_1" value="3" checked /> contiene

				 <input type="radio" name="c_1" value="1" /> uguale a

				 <input type="radio" name="c_1" value="2" /> comincia per

			</div>

		</span>

		

		<span class="rigo_mask">

			<div class="testo_mask">c.a.p.</div>

			<div class="campo_mask">

				<input type="text" class="scrittura" name="q_2" size="50" maxlength="5" />

				 <input type="radio" name="c_2" value="3" checked /> contiene

				 <input type="radio" name="c_2" value="1" /> uguale a

				 <input type="radio" name="c_2" value="2" /> comincia per

			</div>

		</span>



		<span class="rigo_mask">

			<div class="testo_mask">citt</div>

			<div class="campo_mask">

				<input type="text" class="scrittura" name="q_3" size="50" maxlength="255" />

				 <input type="radio" name="c_3" value="3" checked /> contiene

				 <input type="radio" name="c_3" value="1" /> uguale a

				 <input type="radio" name="c_3" value="2" /> comincia per

			</div>

		</span>



		

		<span class="rigo_mask">

			<div class="testo_mask">telefono</div>

			<div class="campo_mask">

				<input type="text" class="scrittura" name="q_4" size="50" maxlength="20" />

				 <input type="radio" name="c_4" value="3" checked /> contiene

				 <input type="radio" name="c_4" value="1" /> uguale a

				 <input type="radio" name="c_4" value="2" /> comincia per

			</div>

		</span>

		

		<span class="rigo_mask">

			<div class="testo_mask">fax</div>

			<div class="campo_mask">

				<input type="text" class="scrittura" name="q_5" size="50" maxlength="20" />

				 <input type="radio" name="c_5" value="3" checked /> contiene

				 <input type="radio" name="c_5" value="1" /> uguale a

				 <input type="radio" name="c_5" value="2" /> comincia per

			</div>

		</span>

		

		<span class="rigo_mask">

			<div class="testo_mask">email</div>

			<div class="campo_mask">

				<input type="text" class="scrittura" name="q_6" size="50" maxlength="20" />

				 <input type="radio" name="c_6" value="3" checked /> contiene

				 <input type="radio" name="c_6" value="1" /> uguale a

				 <input type="radio" name="c_6" value="2" /> comincia per

			</div>

		</span>

		

		<span class="rigo_mask">

			<div class="testo_mask">http</div>

			<div class="campo_mask">

				<input type="text" class="scrittura" name="q_7" size="50" maxlength="20" />

				 <input type="radio" name="c_7" value="3" checked /> contiene

				 <input type="radio" name="c_7" value="1" /> uguale a

				 <input type="radio" name="c_7" value="2" /> comincia per

			</div>

		</span>

		

		</div>



	<div id="titolo_pag"><h1><small>managers</small></h1></div>

	<div class="blocco_centralcat">



		<span class="rigo_mask">

			<div class="testo_mask">funzione</div>

			<div class="campo_mask">

			<select name="funzione" class="scrittura">

			<option value="0">QUALSIASI</option>

			<option value="-1">ALMENO UNA FUNZIONE</option>

			<?php 

			$conn = db_connect();

			$query = "SELECT id,nome FROM funzioni_manager WHERE cancella='n' ORDER BY nome";

			$rr = mssql_query($query, $conn);

			while($row = mssql_fetch_assoc($rr)){

		

				$idfunzione = $row['id'];

				$nomefunzione = $row['nome'];

								

				print ('<option value="'.$idfunzione.'"');

				print('>'.$nomefunzione.'</option>'."\n");

			}

			mssql_free_result($rr);

			?>

			</select>

			</div>

		</span>



		<span class="rigo_mask">

			<div class="testo_mask">nome e cognome</div>

			<div class="campo_mask">

			<input type="text" class="scrittura" name="man_0" size="50" maxlength="255" />

			<input type="radio" name="manc_0" value="3" checked /> contiene

			<input type="radio" name="manc_0" value="1" /> uguale a

			<input type="radio" name="manc_0" value="2" /> comincia per

			</div>

		</span>

	</div>





	<div id="titolo_pag"><h1><small>categoria merceologica</small></h1></div>

	<div class="blocco_centralcat">

		<span class="rigo_mask">

			<div class="testo_mask">nome</div>

			<div class="campo_mask">

			<input type="text" class="scrittura" name="cat_0" size="50" maxlength="255" />

				<input type="radio" name="catc_0" value="3" checked /> contiene

				<input type="radio" name="catc_0" value="1" /> uguale a

				<input type="radio" name="catc_0" value="2" /> comincia per

			</div>

		</span>

	</div>





	<div id="titolo_pag"><h1><small>altri dati</small></h1></div>

	<div class="blocco_centralcat">

		<span class="rigo_mask">

			<div class="testo_mask">tipo dato</div>

			<div class="campo_mask">

			<select name="tipodato" class="scrittura">

			<option value="0">QUALSIASI</option>

			<?php 

			$conn = db_connect();

			$query = "SELECT id,nome FROM tipidati WHERE cancella='n' ORDER BY nome";

			$rr = mssql_query($query, $conn);

			while($row = mssql_fetch_assoc($rr)){

		

				$idtipodato = $row['id'];

				$nometipodato = $row['nome'];

								

				print ('<option value="'.$idtipodato.'"');

				print('>'.$nometipodato.'</option>'."\n");

			}

			mssql_free_result($rr);

			?>

			</select>

			</div>

		</span>



		<span class="rigo_mask">

			<div class="testo_mask">dato</div>

			<div class="campo_mask">

			<input type="text" class="scrittura" name="dato_0" size="50" maxlength="255" />

				<input type="radio" name="datoc_0" value="3" checked /> contiene

				<input type="radio" name="datoc_0" value="1" /> uguale a

				<input type="radio" name="datoc_0" value="2" /> comincia per

			</div>

		</span>

	</div>

	

		<div class="barra_inf">

			<div class="comandi">

				<!-- <input type="image" src="images/aggiungi.gif" name="action" value="aggiungi" /> -->
				<input type="submit" name="action" value="aggiungi" />
				&nbsp;&nbsp;&nbsp;
				<!-- <input type="image" src="images/search.gif" title="cerca struttura" /> -->
				<input type="submit" name="action" value="cerca struttura" />

			</div>

		</div>

	</form>

	<?php

	close_center();

}









/****************************************************************************

* funzione cerca_struttura							*

******************************************************************************/

function cerca_struttura() {



	global $PHP_SELF;

	

	$tablename = 'strutture';

	

	// recupera il n. dei campi postati

	$n_campi = 8;

	$CAMPI = array ('nome','indirizzo','cap','citta','tel','fax','email','http');

	

	// recupera l'aggiornamento multiplo
	if($_POST['agg_mult']=='on')
		$_SESSION['AGG_MULT'] = 1;

	$conn = db_connect();


	// se si sta cercando per id...

	if(isset($_POST['q_id']) && (trim($_POST['q_id'])!='')) {

	

		$id_strut = $_POST['q_id'];
		
		
	
		$query = "SELECT  id FROM $tablename WHERE (id IN (".$id_strut.") ) AND (cancella='n')";
		
		
				
					$stato_sel=$_POST['quali_stato'];
					
					if ($stato_sel=='1')
					$query .= " AND (status=1)";
					else if ($stato_sel=='0')
					$query .= " AND (status=0)";
					
					
		

	// fa una ricerca classica
	} else {
		

		$query = "SELECT id FROM vis_megaquery  WHERE (cancella='n') ";
		
		$stato_sel=$_POST['quali_stato'];
		
		if ($stato_sel=='1')
			$query .= " AND (status=1)";
		else if ($stato_sel=='0')
			$query .= " AND (status=0)";
			

		if(isset($_POST['annuario']) && $_POST['annuario'] > 0)

			$query .= " AND (idannuario=".$_POST['annuario'].")";



		if(isset($_POST['settore']) && $_POST['settore'] > 0)

			$query .= " AND (idsettore=".$_POST['settore'].")";



		if(isset($_POST['regione']) && $_POST['regione'] > 0)

			$query .= " AND (idregione=".$_POST['regione'].")";



		if(isset($_POST['provincia']) && $_POST['provincia'] > 0)

			$query .= " AND (idprovincia=".$_POST['provincia'].")";



		for($i=0; $i<$n_campi; $i++) {
	
	
			if($_POST['q_'.$i]!='') {
	
	
				$var=str_replace("'","''",$_POST['q_'.$i]);
	
				$var=str_replace('"','chr(34)',$var);
	
	
	
				if($_POST['c_'.$i]==1) 
	
					$query .= " AND (".$CAMPI[$i]."='".$_POST['q_'.$i]."')";
	
				else if($_POST['c_'.$i]==2) 
	
					$query .= " AND (".$CAMPI[$i]." LIKE \"".$_POST['q_'.$i]."%\")";
	
				else if($_POST['c_'.$i]==3) 
	
					$query .= " AND (".$CAMPI[$i]." LIKE \"%".$_POST['q_'.$i]."%\")";
	
				else $query .= " AND (".$CAMPI[$i]."='".$_POST['q_'.$i]."')";
	
			}
	
		}


		if(isset($_POST['funzione'])) {

			if($_POST['funzione'] > 0)
				$query .= " AND (idfunzione=".$_POST['funzione'].")";

			else if($_POST['funzione'] == -1)
				$query .= " AND (idfunzione=NULL)";
	
		}
	
		if(isset($_POST['man_0']) && trim($_POST['man_0'])!='') {
	
			//$query .= " AND (manager='".$_POST['man_0']."')";
	
			if($_POST['manc_0']==3)
				$query .= " AND (manager LIKE N'%".$_POST['man_0']."%')";
	
			else if($_POST['manc_0']==2)
				$query .= " AND (manager LIKE N'".$_POST['man_0']."%')";
	
			else if($_POST['manc_0']==1)
				$query .= " AND (manager LIKE N'".$_POST['man_0']."')";
	
		}


		if(isset($_POST['cat_0']) && trim($_POST['cat_0'])!='') {
	
			//$query .= " AND (catmerc='".$_POST['cat_0']."')";
	
			if($_POST['catc_0']==3)
				$query .= " AND (catmerc LIKE N'%".$_POST['cat_0']."%')";
	
			else if($_POST['catc_0']==2)
				$query .= " AND (catmerc LIKE N'".$_POST['cat_0']."%')";
	
			else if($_POST['catc_0']==1)
				$query .= " AND (catmerc LIKE N'".$_POST['cat_0']."')";
	
		}

		if(isset($_POST['tipodato']) && $_POST['tipodato'] > 0)
	
			$query .= " AND (idtipodato=".$_POST['tipodato'].")";
	
	
	
		if(isset($_POST['dato_0']) && trim($_POST['dato_0'])!='') {
	
			if($_POST['datoc_0']==3)
				$query .= " AND (altridati LIKE '%".$_POST['dato_0']."%')";
	
			else if($_POST['datoc_0']==2)
				$query .= " AND (altridati LIKE '".$_POST['dato_0']."%')";
	
			else if($_POST['datoc_0']==1)
				$query .= " AND (altridati LIKE '".$_POST['dato_0']."')";
	
		}
		
		
	
		// setta la variabile di sessione
		$_SESSION['QUERY'] = $query;

	}

	//echo $query;
	
	
	

	//esegue la query
	$result = mssql_query($query, $conn);


	// se no n  stato creato, inizializza l'array di sessione	
	if(!isset($_SESSION['TROVATE_M']))
		$_SESSION['TROVATE_M'] = array();

	// inserisce gli id nell'array temp
	while( ($row = mssql_fetch_row($result))) {

		array_push($_SESSION['TROVATE_M'], $row[0]);

	} // fine while


	// azzera i filtri e le variabili di sessione
	if(isset($_SESSION['QUALI'])) unset($_SESSION['QUALI']);
	if(isset($_SESSION['LETTERA'])) unset($_SESSION['LETTERA']);
	if(isset($_SESSION['ORDINAPER'])) unset($_SESSION['ORDINAPER']);
	if(isset($_SESSION['ORDINAPER2'])) unset($_SESSION['ORDINAPER2']);
	$_SESSION['VENGODARICERCA'] = true;
	$_SESSION['RICALCOLA'] = false;

}



// controllo d'accesso

//if(authorize_me($_SESSION['USERID'], $rule)) {

//if(isset($_SESSION['UTENTE'])) {



	if(isset($_POST['action'])) {



		switch($_POST['action']) {

	

			case "cerca struttura":

			 cerca_struttura();

			// azzera l'array di sessione che conterr tutte le strutture trovate
			$ARR = $_SESSION['TROVATE_TUTTE'] = array();

			// elimina i doppioni
			$_SESSION['TROVATE_TUTTE'] = array_unique($_SESSION['TROVATE_M']);

			// azzera TROVATE_M
			$_SESSION['TROVATE_M'] = array();
			unset($_SESSION['TROVATE_M']);
			
			// prende i post di QUALi
			if(isset($_POST['quali_ricerca']) && $_POST['quali_ricerca']) {
				$_SESSION['QUALI'] = $_POST['quali_ricerca'];
				
			}

			if(isset($_SESSION['AGG_MULT']) && ($_SESSION['AGG_MULT'])) {
				ob_start();
				Header("Location: aggiornamento_multiplo2.php");
				ob_end_flush();
			}
			else {
				ob_start();
				Header("Location: risultati_ricerca.php");
				ob_end_flush();
			}
			break;



			case "aggiungi":

			cerca_struttura();

			html_header();

			form_cerca_struttura();

			html_footer();

			//print $risposta."<br>";

			//echo $_SESSION['TROVATE_M'];

			break;



		}



	} else {

	

		html_header();

		form_cerca_struttura();

		html_footer();

	}

	

/*} else {

	ob_start();

	Header("Location: index.php");

	ob_end_flush();

}*/



?>