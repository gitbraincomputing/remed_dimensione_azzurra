<?php

// inclusione delle classi
include_once('include/class.User.php');
include_once('include/class.Struttura.php');

// inizializza le variabili di sessione
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

// nome della tabella
$tablename = 'utenti_web';
$tablename2 = 'utenti_web_credenziali';
$tablename3 = 'ordini_web';
$id_permesso = $id_menu = 23;
$pagetitle = "gestione utenti web";

// recupera la struttura corrente
//$SID = $_SESSION['STRUTTURA'];


/********************************************************************
* funzione add()            					*
*********************************************************************/
function add() {

	global $PHP_SELF, $tablename, $pagetitle;
	$array_mod=array();
	$admin=$_SESSION['UTENTE']->is_root();

	open_center();
?>
		<form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" onSubmit="return controlla_campi_form('cognome,nome,email,login,valido_dal,valido_al','cognome,nome,email,login,valido dal,fino al','1');">
		<input type="hidden" name="action" value="create" />

		<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - aggiungi utente web</small></h1></div>

		<div class="blocco_centralcat">
			<div class="titolo_par"><em>I campi in <strong>grassetto</strong> sono obbligatori.</em></div>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">annuario</span></div>
				<div class="campo_mask">
			<?php
			$conn = db_connect();
			$query = "select id,nome FROM annuario WHERE cancella='n' ORDER BY id";
			$result = mssql_query($query, $conn);
			if(!$result) error_message(mssql_error());
			?>
			<select class="scrittura" name="annuario">
			<option value="">SELEZIONARE</otpion>
			<?php
			while($row = mssql_fetch_assoc($result)) {
		
				$id = $row['id'];
				$nome = $row['nome'];
				print('<option value="'.$id.'"');
				if(isset($_REQUEST['annuario']) && $_REQUEST['annuario']==$id)
					echo " selected";
				print('>'.$nome.'</option>'."\n");
			}
			?>
				</select>
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">cognome</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="cognome" size="50" maxlength="255">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">nome</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="255">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">indirizzo</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="indirizzo" size="50" maxlength="255">
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask">c.a.p.</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="cap" size="8" maxlength="5">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">comune</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="citta" size="50" maxlength="255">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">provincia</div>
				<div class="campo_mask">
	<select name="prov" class="scrittura">
	<option value="0">SELEZIONARE</option>
	<?php
	// recupera le province
	$conn =db_connect();

	$query = "select id, nome FROM province WHERE cancella='n' ORDER BY nome";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	while ($row = mssql_fetch_assoc($result)) {
		
		print ('<option value="'.$row['id'].'">'.$row['nome'].'</option>'."\n");
	}
	mssql_free_result($result);
	?>
	</select>
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">tel</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="tel" size="50" maxlength="255">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">email</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="email" size="50" maxlength="255">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">p.iva</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="piva" size="50" maxlength="255">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">c.f.</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="cf" size="50" maxlength="255">
				</div>
			</span>
			
			<div class="linea"></div>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">login</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="login" size="50" maxlength="255" />
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">password</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="psw" size="50" maxlength="255" value="<?php echo genera_password(); ?>" />
				</div>
			</span>

			<!-- <span class="rigo_mask">
				<div class="testo_mask"><span class="need">valido dal</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="valido_dal" size="50" maxlength="255" /> <em>(formato gg/mm/aaaa)</em>
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">fino al</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="valido_al" size="50" maxlength="255" /> <em>(formato gg/mm/aaaa)</em>
				</div>
			</span> -->

			<!-- <span class="rigo_mask">
				<div class="testo_mask">pacchetto</div>
				<div class="campo_mask">
	<select name="pacchetto" class="scrittura">
	<option value="0">SELEZIONARE</option> -->
	<?php
	// recupera le province
	/*$conn =db_connect();

	$query = "select id, nomepacchetto FROM utenti_web_pacchetti WHERE cancella='n' ORDER BY nomepacchetto";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	while ($row = mssql_fetch_assoc($result)) {
		
		print('<option value="'.$row['id'].'"');
		//if($row['id']==$pacchetto) echo " selected";
		print('>'.$row['nomepacchetto'].'</option>'."\n");
	}
	mssql_free_result($result);*/
	?>
	<!-- </select>
				</div>
			</span> -->

	<div class="linea"></div>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">stato</span></div>
				<div class="campo_mask">
					<select name="status" class="scrittura">
						<option value="s" selected>attivo</option>
						<option value="n">disattivo</option>
						<!-- <option value="v" selected>da convalidare</option> -->
					</select>
				</div>
			</span>

	</div>
	<div class="barra_inf">
		<div class="comandi">
			<input type="image" src="images/salva.gif" title="salva" />
		</div>
	</div>
	</form>
<?php
	close_center();
}


/****************************************************************
* funzione edit($id)        					*
*****************************************************************/
function edit($id){

	global $PHP_SELF, $tablename, $tablename2, $tablename3, $pagetitle, $id_permesso;
	$conn = db_connect();
	
	$query = "SELECT * FROM vis_utenti_web WHERE id='$id'";
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$nome = stripslashes($row['Nome']);
		$cognome = stripslashes($row['Cognome']);
		$annuario = $row['annuario'];
		$indirizzo = $row['Indirizzo'];
		$cap = $row['Cap'];
		$citta = $row['citta'];
		$prov = $row['prov'];
		$tel = $row['tel'];
		$email = $row['email'];
		$piva = $row['piva'];
		$cf = $row['cf'];

		$login = rtrim($row['login']);
		$psw = rtrim($row['psw']);
		$tot_accessi = rtrim($row['tot_accessi']);

		$scade = $row['scade'];
		if(trim($row['valido_dal'])!='')
			$valido_dal = $row['valido_dal'];
		else $valido_dal = "non definito";
		if(trim($row['valido_al'])!='') {

			if(trim($scade)=='s')
				$valido_al = $row['valido_al'];
			else $valido_al = "non scade";
		}
		else $valido_al = "non definito";
		//$pacchetto = $row['idpacchetto'];

		$status = $row['stato'];

	}
	mssql_free_result($rs);

	// recupera gli ordini associati all'utente
	$n_ordini = $ordini_attivi = $ordini_disattivi = 0;
	$query = "SELECT id,stato FROM $tablename3 WHERE idutenteweb=$id AND cancella='n'";
	$rs = mssql_query($query, $conn);
	while($row = mssql_fetch_assoc($rs)){

		$n_ordini++;
		if(trim($row['stato'])=='s') $ordini_attivi++;
		else $ordini_disattivi++;
	}
	mssql_free_result($rs);


	open_center();
?>
		<form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" onSubmit="return controlla_campi_form('cognome,nome,email,login','cognome,nome,email,login','1');">
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="stato_old" value="<?=$status;?>" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />

		<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - edita utente web</small></h1></div>

		<div class="blocco_centralcat">

	   		<div class="titolo_par"><em>I campi in <strong>grassetto</strong> sono obbligatori.</em></div>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">annuario</span></div>
				<div class="campo_mask">
				<?php
				$conn = db_connect();
				$query = "select id,nome FROM annuario WHERE cancella='n' ORDER BY id";
				$result = mssql_query($query, $conn);
				if(!$result) error_message(mssql_error());
				?>
				<select class="scrittura" name="annuario">
				<?php
				while($row = mssql_fetch_assoc($result)) {
			
					$id_ann = $row['id'];
					$nome_ann = $row['nome'];
					print('<option value="'.$id_ann.'"');
					if($id_ann==$annuario)
							echo " selected";
					print('>'.$nome_ann.'</option>'."\n");
				}
				?>
				</select>
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">cognome</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="cognome" size="50" maxlength="255" value="<?php print($cognome); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">nome</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="255" value="<?php print($nome); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">indirizzo</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="indirizzo" size="50" maxlength="255" value="<?php print($indirizzo); ?>">
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask">c.a.p.</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="cap" size="8" maxlength="5" value="<?php print($cap); ?>">
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">comune</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="citta" size="50" maxlength="255" value="<?php print($citta); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">provincia</div>
				<div class="campo_mask">
	<select name="prov" class="scrittura">
	<option value="0">SELEZIONARE</option>
	<?php
	// recupera le province
	$conn =db_connect();

	$query = "select id, nome FROM province WHERE cancella='n' ORDER BY nome";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	while ($row = mssql_fetch_assoc($result)) {
		
		print('<option value="'.$row['id'].'"');
		if($row['id']==$prov) echo " selected";
		print('>'.$row['nome'].'</option>'."\n");
	}
	mssql_free_result($result);
	?>
	</select>
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">tel</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="tel" size="50" maxlength="255" value="<?php print($tel); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">email</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="email" size="50" maxlength="255" value="<?php print($email); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">p.iva</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="piva" size="50" maxlength="255" value="<?php print($piva); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">c.f.</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="cf" size="50" maxlength="255" value="<?php print($cf); ?>">
				</div>
			</span>

			<div class="linea"></div>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">login</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="login" size="50" maxlength="255" value="<?php print($login); ?>">
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">password</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="psw" size="50" maxlength="255" value="<?php print($psw); ?>">
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">valido dal</span></div>
				<div class="campo_mask">
					<input type="text" class="lettura_piccolo" name="valido_dal" size="20" maxlength="255" value="<?php print($valido_dal); ?>" readonly />
					 <em>(formato gg/mm/aaaa)</em>
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">fino al</span></div>
				<div class="campo_mask">
					<input type="text" class="lettura_piccolo" name="valido_al" size="20" maxlength="255" value="<?php print($valido_al); ?>" readonly /> <em>(formato gg/mm/aaaa)</em>
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask">tot. accessi</div>
				<div class="campo_mask">
					<input type="text" class="lettura_piccolo" size="20" maxlength="20" value="<?php print($tot_accessi); ?>" readonly />
				</div>
			</span>

			<div class="linea"></div>

			<span class="rigo_mask">
				<div class="testo_mask">ordini web</div>
				<div class="campo_mask">
				<?php
				if($n_ordini) {
				print('<strong>n. '.$n_ordini.' associati</strong><br />');
				print('&nbsp;'.$ordini_attivi.' attivi<br />');
				print('&nbsp;'.$ordini_disattivi.' disattivi o da convalidare<br />');
				print('<br /><a href="principale.php?page=ordini_web.php&idutenteweb='.$id.'" target="_parent">visualizza gli ordini web di questo utente</a>');
				}
				else print('<strong>nessun ordine associato</strong>');
				?>
				</div>
			</span>

	<?php
	/*
	if(trim($esportazione)=='s') {


		print('<input type="hidden" name="esportazione" size="20" value="s" readonly />');

		// recupera l'ordine
		$conn =db_connect();
	
		// recupera l'ordine
		$query = "select id, settori, prezzo FROM ordini_web WHERE idutenteweb=$id";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());
	
		if($row = mssql_fetch_assoc($result)) {
	
			$idordine = $row['id'];
			$settori = $row['settori'];
			$prezzo = $row['prezzo'];
		}
		mssql_free_result($result);
		print('<input type="hidden" name="stringa" size="20" value="'.$settori.'" readonly />');
		?>
				<span class="rigo_mask">
					<div class="testo_mask">settori acquistati</div>
					<div class="campo_mask">
		<ul>
		<?php
		$query = "select id, nome FROM settori WHERE id IN ($settori)";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());
	
		while ($row = mssql_fetch_assoc($result)) {
			print ('<li>- '.$row['nome']);
	
			$querystr = "select COUNT(id) FROM strutture WHERE idsettore=".$row['id']." AND cancella='n' AND status=1";
			$rstr = mssql_query($querystr, $conn);
			if(!$rstr) error_message(mssql_error());
		
			if($row=mssql_fetch_row($rstr))
				print(' (<strong>'.$row[0].'</strong>)');
	
			$tot_strut = $tot_strut + $row[0];
	
			mssql_free_result($rstr);
		
			print('</li>'."\n");
	
		}
		mssql_free_result($result);
		?>
		</ul>
					</div>
				</span>
	
				<span class="rigo_mask">
					<div class="testo_mask">totale strutture</div>
					<div class="campo_mask">
					<input type="text" class="lettura" size="20" value="<?=$tot_strut;?>" readonly /> strutture
					</div>
				</span>
	
				<span class="rigo_mask">
					<div class="testo_mask">prezzo</div>
					<div class="campo_mask">
					<input type="text" class="lettura" size="20" value="<?=$prezzo;?>" readonly /> euro
					</div>
				</span>
	<?php
	}*/
	?>

	<div class="linea"></div>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">stato</span></div>
				<div class="campo_mask">
					<select name="status" class="scrittura">
			<option value="s" <?php if ($status=='s') echo " selected"; ?>>online</option>
			<option value="n" <?php if ($status=='n') echo " selected"; ?>>offline</option>
			<!-- <option value="v" <?php if ($status=='v') echo " selected"; ?>>da convalidare</option> -->
					</select>
				</div>
			</span>

	</div>
	<div class="barra_inf">
		<div class="comandi">
			<input type="image" src="images/salva.gif" title="salva" />
		</div>
	</div>
	</form>
<?php
	close_center();
}


/********************************************************************
* funzione create()         					*
*********************************************************************/
function create() {
    
	global $tablename, $tablename2;
	
	$cognome = stripslashes($_POST['cognome']);
	$nome = stripslashes($_POST['nome']);
	$annuario = $_POST['annuario'];
	$indirizzo = $_POST['indirizzo'];
	$cap = $_POST['cap'];
	$citta = $_POST['citta'];
	$prov = $_POST['prov'];
	$piva = $_POST['piva'];
	$cf = $_POST['cf'];
	$tel = $_POST['tel'];
	$email = $_POST['email'];

	$login = $_POST['login'];
	$psw = $_POST['psw'];
	//$valido_dal = $_POST['valido_dal'];
	//$valido_al = $_POST['valido_al'];
	//$pacchetto = $_POST['pacchetto'];

	$stato = $_POST['status'];


	$cognome = str_replace("'","''",$cognome);
	$nome = str_replace("'","''",$nome);
	$indirizzo = str_replace("'","''",$indirizzo);
	$citta = str_replace("'","''",$citta);
	$email = str_replace("'","''",$email);

	$conn = db_connect();

	// verifica se l'utente esiste già
	$query = "SELECT id FROM $tablename WHERE email='".$email."' AND cancella='n'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	if(mssql_num_rows($result)) {
		error_message("L'utente web ".$nome." è già esistente. Provare con un altra email.");
	}

	else {

	// recupera il nome della provincia
	if($prov) {
		$query = "SELECT sigla FROM province WHERE id='".$prov."'";
		$result = mssql_query($query, $conn);
		if($row=mssql_fetch_row($result))
			$nomeprovincia = $row[0];
	}

	/*$query = "SELECT nomepacchetto FROM utenti_web_pacchetti WHERE id='".$pacchetto."'";
	$result = mssql_query($query, $conn);
	if($row=mssql_fetch_row($result))
		$nomepacchetto = $row[0];*/


	$query = "INSERT INTO $tablename (Cognome,Nome,annuario,Indirizzo,Cap,citta,prov,tel,email,piva,cf,opeins) VALUES('$cognome','$nome',$annuario,'$indirizzo','$cap','$citta','$prov','$tel','$email','$piva','$cf',".$_SESSION['UTENTE']->get_userid().")";

	$result = mssql_query($query, $conn);
	if(!$result) error_message("errore nell'insert");

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM $tablename WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		die("errore MAX select");
	else $idutenteweb = $row[0];
		
	// scrive il log
	scrivi_log ($idutenteweb,$tablename,'ins');


	// inserisce nella tabella delle credenziali
	$query = "INSERT INTO $tablename2 (idutenteweb,valido_dal,valido_al,login,psw,stato,annuario,opeins) VALUES('$idutenteweb',NULL,NULL,'$login','$psw','$stato','$annuario',".$_SESSION['UTENTE']->get_userid().")";

	$result = mssql_query($query, $conn);
	if(!$result) error_message("errore nell'insert");

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM $tablename2 WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		die("errore MAX select");
	else $idcred = $row[0];
		
	// scrive il log
	scrivi_log ($idcred,$tablename2,'ins');

	// se lo stato old = v e stato = s, invia l'email
	/*if(trim($stato=='s')) {

		if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
			$eol="\r\n";
			} elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
			$eol="\r";
			} else {
			$eol="\n";
			}


# To Email Address
$emailaddress=$email;
# Message Subject
$emailsubject="Attivazione utente registrato";


# Boundry for marking the split & Multitype Headers
$mime_boundary=md5(time()); 


# Common Headers
$headers .= 'From: Editoriale Publiaci <info@editorialepubliaci.it>'.$eol;
//$headers .= "--".$mime_boundary.$eol;
//$headers .= "Content-Type: text/plain; charset=iso-8859-1".$eol;
//$headers .= "Content-Transfer-Encoding: 8bit".$eol;
//$headers .= 'Reply-To: Jonny <jon@genius.com>'.$eol;
//$headers .= 'Return-Path: Jonny <jon@genius.com>'.$eol;    // these two to set reply address
//$headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
//$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid

$msg = "";

# Text Version
$msg .= "Gentile ".trim($cognome)." ".trim($nome).",".$eol;
$msg .= "La informiamo che Le è stato abilitato l'accesso al Catalogo dell'Annuario ";
if($annuario==1) $msg .= "Sanità Italia".$eol;
else if($annuario==2) $msg .= "dell'Agricoltore".$eol;
$msg .= "Di seguito, Le riportiamo i suoi dati con le credenziali di accesso:".$eol.$eol;
$msg .= "Cognome: $cognome".$eol;
$msg .= "Nome: $nome".$eol;
if(trim($indirizzo)!='') $msg .= "Indirizzo: $indirizzo".$eol;
if(trim($cap)!='') $msg .= "C.A.P.: $cap".$eol;
if(trim($citta)!='') $msg .= "Comune: $citta".$eol;
if($prov) $msg .= "Provincia: $nomeprovincia".$eol;
if(trim($tel)!='') $msg .= "Telefono: $tel".$eol;
if(trim($email)!='') $msg .= "Email: $email".$eol;
if(trim($piva)!='') $msg .= "P.iva: $piva".$eol;
if(trim($cf)!='') $msg .= "C.F.: $cf".$eol.$eol;
$msg .= "Username: $login".$eol;
$msg .= "Password: $psw".$eol;
$msg .= "Valido dal: $valido_dal".$eol;
$msg .= "Fino al: $valido_al".$eol;
$msg .= "Pacchetto: $nomepacchetto".$eol;

# Finished
$msg .= $eol.$eol;  // finish with two eol's for better security. see Injection. 

# SEND THE EMAIL
ini_set(sendmail_from,'contact@area04.it');  // the INI lines are to force the From Address to be used !
  mail($emailaddress, $emailsubject, $msg, $headers);
ini_restore(sendmail_from); 


print("<script>alert ('Email inviata a ".$email."');</script>");

	}*/

	// fine else
	}
	mssql_free_result($result);

}


/************************************************************************
* funzione update($sid)     						*
*************************************************************************/
function update($id) {

	global $tablename, $tablename2;
	
	$cognome = stripslashes($_POST['cognome']);
	$nome = stripslashes($_POST['nome']);
	$annuario = $_POST['annuario'];
	$indirizzo = $_POST['indirizzo'];
	$cap = $_POST['cap'];
	$citta = $_POST['citta'];
	$prov = $_POST['prov'];
	$piva = $_POST['piva'];
	$cf = $_POST['cf'];
	$tel = $_POST['tel'];
	$email = $_POST['email'];

	$esportazione = $_POST['esportazione'];
	$stringa = $_POST['stringa'];

	$login = $_POST['login'];
	$psw = $_POST['psw'];

	/*if($esportazione=='s') {
		$valido_dal = '';
		$valido_al = '';
	} else {
		$valido_dal = $_POST['valido_dal'];
		$valido_al = $_POST['valido_al'];
	}
	$pacchetto = $_POST['pacchetto'];*/

	$stato_old = $_POST['stato_old'];
	$stato = $_POST['status'];

	$cognome = str_replace("'","''",$cognome);
	$nome = str_replace("'","''",$nome);
	$indirizzo = str_replace("'","''",$indirizzo);
	$citta = str_replace("'","''",$citta);
	$email = str_replace("'","''",$email);

	if(empty($nome)) error_message("Inserire il nome!");

	$conn = db_connect();

	// recupera il nome della provincia
	if($prov) {
		$query = "SELECT sigla FROM province WHERE id='".$prov."'";
		$result = mssql_query($query, $conn);
		if($row=mssql_fetch_row($result))
			$nomeprovincia = $row[0];
	}

	/*$query = "SELECT nomepacchetto FROM utenti_web_pacchetti WHERE id=".$pacchetto."";
	$result = mssql_query($query, $conn);
	if($row=mssql_fetch_row($result))
		$nomepacchetto = $row[0];*/

	$query = "UPDATE $tablename SET 
	Cognome ='".$cognome."',
	Nome = '$nome',
	annuario = $annuario,
	Indirizzo = '$indirizzo',
	Cap = '$cap',
	citta = '$citta',
	prov = $prov,
	tel = '$tel',
	email = '$email',
	piva = '$piva',
	cf = '$cf',
	opeagg=".$_SESSION['UTENTE']->get_userid()." WHERE id=$id";
	//  echo $query;
	$result = mssql_query($query, $conn);
	if(!$result) error_message("errore nell'update");

	// scrive il log
	scrivi_log ($id,$tablename,'agg');

	// inserisce nella tabella delle credenziali
	$query = "UPDATE $tablename2 SET 
	login = '$login',
	psw = '$psw',
	stato ='$stato',
	annuario ='$annuario',
	opeagg=".$_SESSION['UTENTE']->get_userid()." 
	WHERE idutenteweb=$id";
	
// 	echo $query;
	$result = mssql_query($query, $conn);
	if(!$result) error_message("errore nell'update");

	// recupera l'id del record appena inserito
	$query = "SELECT id FROM $tablename2 WHERE idutenteweb=$id";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		die("errore MAX select");
	else $idcred = $row[0];
		
	// scrive il log
	scrivi_log ($idcred,$tablename2,'agg');

	// fine else
	//}


}


/************************************************************************
* funzione confirm_del($id) 						*
*************************************************************************/
function confirm_del($id) {

	global $PHP_SELF, $tablename, $pagetitle;

	$conn = db_connect();

	$query = "SELECT Cognome,Nome FROM $tablename where id=$id";
	if(! ($rs = mssql_query($query, $conn))) error_message(mssql_error());
	if(! ($row = mssql_fetch_row($rs))) error_message(mssql_error());
	mssql_free_result($rs);

	open_center();
?>
	<form method="post" name="modulo" action="<?php echo $PHP_SELF ?>?id=<?php echo $id ?>">
	<input type="hidden" name="action" value="del" />
	
	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - conferma cancellazione</small></h1></div>

	<div class="blocco_centralcat">

		<div id="messaggio_cancella">Cancellare l'utente web<br /><strong><?php echo $row[0]." ".$row[1]; ?></strong> ?</div>
		<div id="tasti_cancella">
			
			<span class="tasto_canc">
			<input class="larghezza_bottone" type="submit" value="si" name="choice" />
			</span>
			<input class="larghezza_bottone" type="button" value="no" onClick="javascript:history.go(-1);" />
		</div>

	</div>
	</form>
<?php
	close_center();
}


/****************************************************************
* funzione del()						*
*****************************************************************/
function del($id) {

	global $tablename, $tablename2, $tablename3;

	$conn = db_connect();

	// verifica che non ci siano ordini attivi associati all'utente
	$query = "SELECT id FROM $tablename3 WHERE idutenteweb='".$id."' AND cancella='n'";
	$result = mssql_query($query, $conn);
	// se trova qualcosa da errore
	if(mssql_num_rows($result)) 
		error_message("Non è possibile cancellare questo utente web poichè alcuni ordini sono associati ad esso!");

	else {

		// cancella l'elemento selezionato
		$query = "UPDATE $tablename SET cancella='y' WHERE id='".$id."'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());
	
		// scrive il log
		scrivi_log ($id,$tablename,'del');
	
		// cancella dalle credenziali
		$query = "UPDATE $tablename2 SET cancella='y' WHERE idutenteweb='".$id."'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());
	
	}

}


/************************************************************************
 funzione show_list() 							*
*************************************************************************/
function show_list(){

	global $PHP_SELF, $tablename, $tablename2, $pagetitle, $id_menu, $id_permesso;

	if(isset($_REQUEST['page'])) $page = $_REQUEST['page'];
	else $page = 1;
	if(isset($_REQUEST['subpage'])) $subpage = $_REQUEST['subpage'];
	else $subpage = 0;

	// crea la connessione
	$conn = db_connect();

	// apre il div centrale
	open_center();

	print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>'."\n");
	print('<div class="blocco_centralcat">'."\n");
	
	print('<div class="titolo_par"><em>elenco utenti web</em></div>'."\n");

	// fa la query dei record non cancellati
	$query = "SELECT * FROM vis_utenti_web ";

	if(isset($_SESSION['ANNUARIO']) && $_SESSION['ANNUARIO'])
		$query .= " WHERE annuario = '".$_SESSION['ANNUARIO']."'";

	/*$query = "SELECT id FROM $tablename WHERE (($tablename.cancella='n')";
	// where clauses
	if(isset($_SESSION['ANNUARIO']) AND $_SESSION['ANNUARIO']) $query .= " AND ($tablename.annuario='".$_SESSION['ANNUARIO']."')";
	if(isset($_REQUEST['letter'])) $query .= " AND($tablename.nome LIKE \"".$_REQUEST['letter']."%\")";
	// order by
	$query .=") ORDER BY ";
	if(isset($_SESSION['ORDINAPER'])) {
		
		// criterio 1
		if($_SESSION['ORDINAPER']==1) $query .="$tablename.annuario";
		else if($_SESSION['ORDINAPER']==2) $query .="$tablename.id";
		else if($_SESSION['ORDINAPER']==3) $query .= "$tablename.nome";
		else $query .= "$tablename.nome";
	}
	else $query .= "$tablename.id DESC";*/

	$query .= " ORDER BY id DESC";

	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

// 	echo $query;

	$lista = array();
	while($row = mssql_fetch_assoc($rs)){
		// riempie l'array
		array_push($lista, $row['id']);
	}

	$num_rows = mssql_num_rows($rs);

	print('<div id="pulsantiera">');
	/*lettere_alfabetiche();
	GestionePagine($num_rows, $page, $tablename);
	$arr1= array('annuario','id','alfabetico');
	$arr2 = array();
	ordina_per ($arr1, $arr2);*/
	print('<form name="form0" method="post" action="'.$PHP_SELF.'">');
	$conn = db_connect();
	$query = "select id,nome FROM annuario WHERE cancella='n' ORDER BY id";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	?>
	visualizza gli utenti web dell'annuario &nbsp;&nbsp;&nbsp;
	<select class="scrittura" name="annuario">
	<option value="">TUTTI</otpion>
	<?php
	while($row = mssql_fetch_assoc($result)) {

		$id = $row['id'];
		$nome = $row['nome'];
		print('<option value="'.$id.'"');
		if(isset($_REQUEST['annuario']) && $_REQUEST['annuario']==$id)
			echo " selected";
		print('>'.$nome.'</option>'."\n");
	}
	?>
		</select>
	&nbsp;&nbsp;<input type="submit" name="action" value="visualizza" />
	<?php
	print('</form>');
	print('</div>');

	if($num_rows > 0) {
	
		print('<p class="intest">Sono stati trovati <strong>'.$num_rows.'</strong> utenti web.');
		if(isset($_REQUEST['letter'])) print(' con la lettera <strong>'.$_REQUEST['letter'].'</strong>');
		print('.</p>'."\n");
	
		$rpp = $_SESSION['NXPAGINA'];
	
		// calcola la prima e l'ultima pagina
		$prima = ($rpp * ($page-1));
		$ultima = ($rpp * $page);

		print('<ul>'."\n");

		$count = 0;
		// parte il ciclo
		for($i=$prima; $i<$ultima; $i++) {
	
			if($i<sizeof($lista)) {

				$query  ="SELECT Cognome,Nome,email FROM $tablename WHERE id=".$lista[$i]."";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				
				$row = mssql_fetch_assoc($rs);
	
				$id = $lista[$i];
				$cognome = stripslashes($row['Cognome']);
				$nome = stripslashes($row['Nome']);
				$email = stripslashes($row['email']);

				$query  ="SELECT stato FROM $tablename2 WHERE idutenteweb=".$lista[$i]."";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				
				$row = mssql_fetch_assoc($rs);

				$stato = $row['stato'];
	
				print('<li>');
				print('<span class="id_notizia_cat">'.$id.'</span>'."\n");
				print('<span class="notizia_cat">'."\n");
				print('<a href="principale.php?page='.$PHP_SELF.'&do=edit&id='.$id.'" target="_parent">');
				if(trim($stato)=='n')
					print('<span class="offline">'.$cognome." ".$nome."</span>");
				else print($cognome." ".$nome);
				print('</a>');
				print('</span>'."\n");
				
				print('<span class="icone">');
				print('<a href="'.$PHP_SELF.'?do=confirm_del&id='.$id.'" title="cancella l\'utente web" target="main"><img class="icona_singola" src="images/cancella.gif" alt="cancella" /></a>');
				print('</span>');
	
				print('</li>'."\n");
			
				mssql_free_result($rs);
				$count++;
			}
			
		}

		print('</ul>'."\n");

		if($count > 30) {
			print('<div id="pulsantiera">');
			lettere_alfabetiche();
			GestionePagine($num_rows, $page, $tablename);
			ordina_per ($arr1, $arr2);
			print('</div>');
		}
	}

	else print ('<p class="intest"><span class="evidenziato">Nessun utente web presente.</span></p>');

	print("\n".'</div>'."\n");

	// chiude il div centrale
	close_center();
}




// controllo d'accesso
if(isset($_SESSION['UTENTE'])) {

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {


		// recupera i dati di sessione dell'utente
		$objUser = $_SESSION['UTENTE'];

		if(!isset($do)) $do='';

		$back = "main.php";
	
		if (empty($_POST)) $_POST['action'] = "";

		include_once('include/aggiorna.php');
	
		switch($_POST['action']) {
	
			case "create":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create();
			break;
	
			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_POST['id']);
			break;
	
			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id'],'n');
			break;
	
			case "visualizza":
				$_SESSION['ANNUARIO'] = $_POST['annuario'];
			break;

			case "ordina":
				$_SESSION['ORDINAPER'] = $_POST['ordinaper'];
			break;
		}
	
		if($do!='logout'){
			html_header();
		}
	
		switch($do) {
	
			case "add":
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else add();
			break;
	
			case "edit":
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
				else edit($_REQUEST['id']);
			break;
	
			case "confirm_del":
				confirm_del($id);
			break;
	
			case "logout":
				logout();
			break;
	
			default:
				show_list();
			break;
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