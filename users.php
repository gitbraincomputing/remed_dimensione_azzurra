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
$tablename = 'operatori';
$pagetitle = "Gestione operatori";
$id_menu = $id_permesso = 12;

/****************************************************************
* funzione add() 					           					*
*****************************************************************/
function add() {

	global $PHP_SELF, $pagetitle;

//	echo "permessi ".$_SESSION['UTENTE']->controlla_permessi(5, 1);

	$conn = db_connect();
	/*$query = "SELECT gid,nome FROM gruppi WHERE sid=".$_SESSION['STRUTTURA1']->get_id()." ORDER BY gid";
	$rs = mssql_query($query, $conn);

	if(!mssql_num_rows($rs))
		error_message('Attenzione! In questa struttura non è presente nessun UTENTE. Per creare un UTENTE, è necessario creare prima un gruppo.');
	*/
	// apre il div centrale
	open_center();
?>
	<form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" onSubmit="return controlla_campi_form('nome','nome','0');">
	<input type="hidden" name="action" value="create" />
	<input type="hidden" name="op" value="1" />

	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?></small></h1></div>

	<div class="blocco_centralcat">

	<span class="rigo_mask">
		<div class="testo_mask">nome e cognome</div>
		<div class="campo_mask">
			<input type="text" class="scrittura_campo" name="nome" SIZE="30" maxlenght="30" />
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">email</div>
		<div class="campo_mask">
			<input type="text" class="scrittura_campo" name="email" SIZE="30" maxlenght="30" />
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">breve descrizione</div>
		<div class="campo_mask">
			<textarea name="descr" class="scrittura" rows="4" cols="40"></textarea>
		</div>
	</span>

		<span class="rigo_mask">
			<div class="testo_mask">username</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="username" SIZE="30" maxlenght="30" />
			</div>
		</span>
	
		<span class="rigo_mask">
			<div class="testo_mask">password</div>
			<div class="campo_mask">
				<input type="password" class="scrittura" name="password" SIZE="30" maxlenght="30" />
			</div>
		</span>
	
		<span class="rigo_mask">
			<div class="testo_mask">conferma password</div>
			<div class="campo_mask">
				<input type="password" class="scrittura" name="rpassword" SIZE="30" maxlenght="30" />
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">gruppo</div>
			<div class="campo_mask">
					<?php
					$conn = db_connect();
	
					$query = "SELECT gid,nome FROM gruppi WHERE cancella='n' ORDER BY nome";
					$rs = mssql_query($query, $conn);
	
					if(mssql_num_rows($rs)) {
					
						print('<select name="gid" class="scrittura">'."\n");
						while( $row = mssql_fetch_assoc($rs)) {
		
							$gid = $row['gid'];
							$nome_gruppo = $row['nome'];
		
							print ('<option value="'.$gid.'">'.$nome_gruppo.'</option>');
						}
						print('</select>'."\n");
					
					} else print('nessun gruppo presente');
					?>
					<!-- <input type="submit" name="action" value="cambia gruppo" disabled /> -->
			</div>
		</span>
	
		<span class="rigo_mask">
			<div class="testo_mask">validazione</div>
			<div class="campo_mask">
				<input type="radio" name="validazione" value="y" /> si 
				<input type="radio" name="validazione" value="n" checked /> no
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
	</div>

	<div class="barra_inf">
		<div class="comandi">
			<input type="image" src="images/salva.gif" title="salva" />
		</div>
	</div>
	</form>

<?php
	// chiude il div centrale
	close_center();
}


/****************************************************************
* funzione edit()						*
*****************************************************************/
function edit($id){

	global $PHP_SELF, $tablename, $pagetitle;

	$conn = db_connect();

	$query = "SELECT * from $tablename WHERE uid='$id'";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$nome = $row['nome'];
		$descr = $row['descr'];
		$email = $row['email'];
		$usr = $row['username'];
		$gid = $row['gid'];
		$validazione = $row['validazione'];
		$status = $row['status'];
	}

	// rimuove i caratteri di escape
	$nome = stripslashes($nome);
	$usr = stripslashes($usr);
	$descr = stripslashes($descr);

	mssql_free_result($rs);

	// apre il div centrale
	open_center();
?>
	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?></small></h1></div>

	<form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" onSubmit="return controlla_campi_form('nome','nome','0');">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?php echo $id ?>" />

	<div class="blocco_centralcat">

		<span class="rigo_mask">
			<div class="testo_mask">nome e cognome</div>
			<div class="campo_mask">
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
			<div class="testo_mask">breve descrizione</div>
			<div class="campo_mask">
				<textarea name="descr" class="scrittura" rows="4" cols="40"><?php echo $descr ?></textarea>
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">username</div>
			<div class="campo_mask">
				<input class="lettura" type="text" name="usr" value="<?php echo $usr; ?>" readonly />
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">password</div>
			<div class="campo_mask">
				<a href="password.php?do=edit_pwd&amp;id=<?php echo $id ?>">cambia la password</a>
			</div>
		</span>
	
	<?php
		// se non stai editando te stesso...
		//if($_SESSION['UTENTE']->get_userid() != $id) {
	?>

		<span class="rigo_mask">
			<div class="testo_mask">gruppo</div>
			<div class="campo_mask">
					<?php
					$conn = db_connect();
	
					$query = "SELECT gid,nome FROM gruppi WHERE cancella='n' ORDER BY nome";
					$rs = mssql_query($query, $conn);
	
					if(mssql_num_rows($rs)) {
					
						print('<select name="gid" class="scrittura">'."\n");
						while( $row = mssql_fetch_assoc($rs)) {
		
							$id_gruppo = $row['gid'];
							$nome_gruppo = $row['nome'];
		
							print ('<option value="'.$id_gruppo.'"');
							if($gid == $id_gruppo) echo " selected";
							print ('>'.$nome_gruppo.'</option>');
						}
						print('</select>'."\n");
					
					} else print('nessun gruppo presente');
					?>
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">validazione</div>
			<div class="campo_mask">
				<input type="radio" name="validazione" value="y" <?php if($validazione=='y') echo "checked"; ?> /> si 
				<input type="radio" name="validazione" value="n" <?php if($validazione=='n') echo "checked"; ?> /> no
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">stato</div>
			<div class="campo_mask">
		<?php
			// se gruppo è disattivo ferma tutto..
			$conn = db_connect();
			$query = "SELECT status FROM gruppi WHERE gid='".$gid."'";
			$rs = mssql_query($query, $conn);
			//if(!$row = mssql_fetch_assoc($rs)) error_message("errore gruppo");
			//if($row['status']) {
		?>
					<select name="status" class="scrittura">
					<option value="1" <?php if($status) echo "selected"; ?>>attivo</option>
					<option value="0" <?php if(!$status) echo "selected"; ?>>disattivo</option>
					</select>
		<?php
			//} else print ("<span class=\"offline\">Il gruppo di appartenenza è disattivo. Non è possibile cambiare questa opzione.</span>");
			mssql_free_result($rs);
		?>
			</div>
		</span>
	<?php
		//} else print('<input type="hidden" name="status" value="1" />');
	?>
	</div>

	<div class="barra_inf">
		<div class="comandi">
			<input type="image" src="images/salva.gif" title="salva" />
		</div>
	</div>
	</form>

<?php
	// chiude il div centrale
	close_center();
}


/****************************************************************
* funzione create()												*
*****************************************************************/
function create() {

	global $tablename;

	$isroot = $_SESSION['UTENTE']->is_root();

	$nome = stripslashes($_POST['nome']);
	$descr = stripslashes($_POST['descr']);
	$usr = $_POST['username'];
	$pwd = $_POST['password'];
	$rpwd = $_POST['rpassword'];
	$gid = $_POST['gid'];
	$email = $_POST['email'];
	$validazione = $_POST['validazione'];
	$status = $_POST['status'];

	// controllo dei campi
	if(empty($nome)) error_message("Inserire il nome e il cognome!");
	if(empty($usr)) error_message("Inserire la username!");
	if($pwd!=$rpwd) error_message("Le password non coincidono!");
	//if(!$gid) error_message("L'UTENTE deve essere associato necessariamente ad un gruppo!");

	// aggiunge i caratteri di escape
	$nome = str_replace("'","''",$nome);
	$usr = str_replace("'","''",$usr);
	//$sign = addslashes($sign);

	if(!verifica_username($usr)) error_message("Username già presente nel database. Provare con un altro.");

	$conn = db_connect();

	// cripta la pwd con la chiave corrente
	$pwd_crypt = crypt($pwd,SALE);

	$query = "INSERT INTO $tablename (nome,gid,descr,username,password,email,opeins,status,validazione) VALUES('$nome','$gid','$descr','$usr','$pwd_crypt','$email','".$_SESSION['UTENTE']->get_userid()."','$status','$validazione')";

	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(uid) FROM $tablename WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		die("errore MAX select");
	
	// compila i campi di log
	scrivi_log ($row[0],$tablename,'ins_op');

	// pic
	/*if ((trim($_FILES['pic']['name']) != "") && ($_FILES['pic'] != 'none')) {

		$imgHNDL = fopen($_FILES['pic']['tmp_name'], "rb");
		$imgf = fread($imgHNDL, $_FILES['pic']['size']);
		$eimgf = mssql_escape_string($imgf);

		$query_img ="update $tablename set img='" . $eimgf . "',alt_img='".$nome."' where uid='".$id."'";
		$result = mssql_query($query_img, $conn);
		if(!$result) error_message(mssql_error());

		fclose($imgHNDL);
	}*/

	// scrive il log
	//scrivi_log("users.php","A","users",$id);
	
	return $row[0];

}


/****************************************************************
* funzione update()						*
*****************************************************************/
function update($id) {

	global $tablename;

	$isroot = $_SESSION['UTENTE']->is_root();

	$nome = stripslashes($_POST['nome']);
	$descr = stripslashes($_POST['descr']);
	$gid = $_POST['gid'];
	$email = $_POST['email'];
	$validazione = $_POST['validazione'];
	$status = $_POST['status'];

	if(empty($nome)) error_message("Inserire il nome e il cognome!");
	if(empty($email)) error_message("Inserire l'email!");

		// aggiunge i caratteri di escape
		$nome = str_replace("'","''",$nome);
		$descr = str_replace("'","''",$descr);

		$conn = db_connect();

		// recupera dal db il vecchio username
		/*$query = "SELECT username FROM $tablename where uid='$id'";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		if(! $row = mssql_fetch_row($rs)) error_message(mssql_error());
		$old_usr = $row[0];
		mssql_free_result($rs);

		// se si sta tentando di modificare lo username, lo confronta con il vecchio
		if(strcmp($old_usr,$usr) !=0)
			if(!verifica_username($usr)) error_message("Username già presente. Provare con un altro.");

		//if($_SESSION['UTENTE']->is_root())
			$query = "UPDATE $tablename SET nome='$nome',descr='$descr',email='$email',username='$usr',status='$status' WHERE uid='$id'";
		
		//else  {
			//$query = "UPDATE $tablename SET nome='$nome',descr='$descr',username='$usr' WHERE uid='$id'";
		//}*/
		
		// aggiorna il campo username solo se è il proprio
		//if($id==$_SESSION['UTENTE']->get_userid()) $_SESSION['UTENTE']->set_username($usr);

		$query = "UPDATE $tablename SET nome='$nome',gid=$gid, email='$email',descr='$descr',validazione='$validazione' WHERE uid='$id'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		// compila i campi di log
		scrivi_log ($id,$tablename,'agg_op');

		// pic
		/*if ((trim($_FILES['pic']['name']) != "") && ($_FILES['pic'] != 'none')) {

			$imgHNDL = fopen($_FILES['pic']['tmp_name'], "rb");
			$imgf = fread($imgHNDL, $_FILES['pic']['size']);
			$eimgf = mssql_escape_string($imgf);

			$query_img ="update $tablename set img='" . $eimgf . "',alt_img='$nome' where uid=$id";
			$result = mssql_query($query_img, $conn);
			if(!$result) error_message(mssql_error());

			fclose($imgHNDL);
		}*/

		// rilascia l'id
		unset($id);

		// scrive il log
        //scrivi_log("users.php","M","users",$id);
		return true;
}


/****************************************************************************************************************
* funzione verifica_username()																					*
*****************************************************************************************************************/
function verifica_username($usr) {

	global $tablename;

	$conn = db_connect();
	$query = "SELECT uid FROM $tablename where username='".addslashes($usr)."'";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if(mssql_num_rows($rs)) return false;
	else return true;
}


/****************************************************************
* funzione confirm_del()					*
*****************************************************************/
function confirm_del($id) {

	global $tablename;
	
	$conn = db_connect();
	$query = "SELECT nome FROM $tablename where uid=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	$row= mssql_fetch_row($rs);

	global $PHP_SELF;
	open_center();
?>
	<form method="post" name="modulo" action="<?php echo $PHP_SELF ?>?id=<?php echo $id ?>">
	<input type="hidden" name="action" value="del" />
	
	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - conferma cancellazione</small></h1></div>

	<div class="blocco_centralcat">

		<div id="messaggio_cancella">Cancellare l' utente<br /><strong><?php echo $row[0]; ?></strong> ?</div>
		<div id="tasti_cancella">
			
			<span class="tasto_canc">
			<input class="larghezza_bottone" type="submit" value="si" name="choice" />
			</span>
			<input class="larghezza_bottone" type="button" value="no" onClick="javascript:window.location='<?php echo $PHP_SELF; ?>';" />
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

	global $tablename;

	$conn = db_connect();

	// verifica se sei root
	//$isroot = $_SESSION['UTENTE']->is_root();
	
	//if($id != $_SESSION['UTENTE']->get_userid()) {

		// cancella l'elemento selezionato
		$query = "UPDATE $tablename SET cancella='y' WHERE uid='$id'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		// scrive il log
		//scrivi_log ($id,$tablename,'del');

	//} else {
	
		//error_message("Operazione non permessa!");
	
	//}

  //scrivi_log("users.php","C","users",$id);
}


/****************************************************************
* funzione show_list()						*
*****************************************************************/
function show_list(){

	global $PHP_SELF, $tablename, $pagetitle, $id_permesso;
	
	$myid = $_SESSION['UTENTE']->get_userid();

	// apre il div centrale
	open_center();

	print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>'."\n");
	print('<div class="blocco_centralcat">'."\n");

	$conn = db_connect();

	//if($_SESSION['UTENTE']->is_root())
		$total_query = "SELECT uid,nome,email,is_root,status FROM $tablename WHERE cancella='n' ORDER BY nome";
	
	//else 
	//	$total_query = "SELECT uid,nome,email,is_root,status from $tablename WHERE is_root='0' AND cancella='n' ORDER BY nome";

	//echo $total_query;
	$rt = mssql_query($total_query, $conn);
	if(!$rt) error_message(mssql_error());

	print('<div class="titolo_par"><em>Elenco operatori</em></div>'."\n");

	if(mssql_num_rows($rt) > 0) {

		print('<ul>'."\n");

		while($total_row = mssql_fetch_assoc($rt)){

			$id = $total_row['uid'];
			$nome = $total_row['nome'];
			$email = $total_row['email'];
			$status = $total_row['status'];
			$is_root = $total_row['is_root'];
			
			// rimuove i caratteri di escape
			$nome = stripslashes($nome);
	
			print ('<li>');
			print('<span class="id_notizia_cat">'.$id.'</span>'."\n");
			print('<span class="notizia_cat">'."\n");
			print('<a href="principale.php?page='.$PHP_SELF.'&do=edit&id='.$id.'" target="_parent">');
			if(!$status) print ('<span class="offline">'.$nome.'</span>'."\n");
			else {
				if($id==$myid) print ('<span class="evidenziato">'.$nome.'</span>');
				else print ($nome);
			}
			print('</a>');

			if($email!="") print(' (<a href="mailto:'.$email.'" title="invia una email">'.$email.'</a>)'."\n");
			print ('</span>'."\n");

			//icone_permessi($id, $id_permesso);
				
			print ('</li>');
		
		}

		print('</ul>'."\n");
		
		mssql_free_result($rt);
		
	}
	
	else print ('<p class="none">Nessun utente presente in questa struttura. Per crearne uno nuovo clicca <a href="'.$PHP_SELF.'?do=add">qui.</a>');

	print("\n".'</div>'."\n");

	// chiude il div centrale
	close_center();
}


// controllo d'accesso
if( isset($_SESSION['UTENTE'])) {

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		// recupera i dati di sessione dell'UTENTE
		$objUser = $_SESSION['UTENTE'];
	
		$back = "main.php";
		// azzera la variabile do
		if(!isset($do)) $do ='';
	
		//if(authorize_me($objUser->get_userid(), 'none')) {
	
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
					//if(!$objUser->is_root()) go_home();
	
				case "del":
					// verifica i permessi..
					if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
						error_message("Permessi insufficienti per questa operazione!");
						else if($_POST['choice']=="si") del($_REQUEST['id']);
					break;
			}
	
			if($do!='logout'){
				html_header();
			}
	
			switch($do) {
	
				case "add":
				add();
				break;
	
				case "edit":
				edit($_REQUEST['id']);
				break;
	
				case "confirm_del":
				confirm_del($_REQUEST['id']);
				break;
	
				case "logout":
				logout();
				break;
	
				default:
				// array comandi disponibili
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