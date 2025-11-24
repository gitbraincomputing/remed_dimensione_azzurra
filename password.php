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
$pagetitle = 'Cambio password';

/****************************
* funzione change_pwd()  *
*****************************/
function change_pwd($uid) {

	global $tablename;

	//$uid = $_SESSION['UTENTE']->get_userid();
	$isroot = $_SESSION['UTENTE']->is_root();

	// se non sei l'amminsitratore e non è il tuo userid..
	/*if( (!$isroot) and ($id!=$_SESSION['UTENTE']->get_userid())){
		error_message("Permessi insufficienti per questa operazione!");
		return false;
	}*/

	// esegue l'operazione
	//else {

		// recupera i campi del form
		if(isset($_POST['old_pwd'])) 
			$old_pwd = $_POST['old_pwd'];		// vecchia password
		$pwd = $_POST['pwd'];				// nuova passowrd
		$repwd = $_POST['repwd'];			// conferma nuova password

		$conn = db_connect();

		// recupera la chiave per criptare
		$query = "SELECT password FROM $tablename WHERE uid='$uid'";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());

		if( $row = mssql_fetch_assoc($rs)){
			$curr_pwd = $row['password'];
		}

		mssql_free_result($rs);

		// controlli vari..
		if(strcmp($pwd,$repwd)!=0) error_message("Le due password non coincidono");

		// cripta la nuova password
		$pwd_crypt = crypt($pwd,SALE);

		// se non sei un amministratore..
		if(!$isroot){

			//if(empty($old_pwd)) error_message("Inserire la vecchia password");

			// recupera la vecchia password e la confronta
			//echo $curr_pwd;
			if(strcmp($curr_pwd, crypt($old_pwd,SALE))!=0) error_message("Password vecchia sbagliata!");
		}

		$query = "UPDATE $tablename SET password='$pwd_crypt' WHERE uid='$uid'";

		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		// rilascia l'uid
		unset($uid);
		
		// conferma il cambio password
		confirm('password cambiata con successo!',"users.php?do=edit&id=".$_REQUEST['id']);

}

/****************************
* funzione view()        *
*****************************/
function view_pwd($uid){

	global $PHP_SELF, $pagetitle;
	
	//$uid = $_SESSION['UTENTE']->get_userid();
	$isroot = $_SESSION['UTENTE']->is_root();

	// apre il div centrale
	open_center();
?>
	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?></small></h1></div>

	<form method="post" action="<?php echo $PHP_SELF ?>?id=<? echo $uid; ?>">
	<input type="hidden" name="action" value="change_pwd" />

	<div class="blocco_centralcat">

		<?php
		// se non sei l'amministratore
		if(!$isroot) {
		?>
		<span class="rigo_mask">
			<div class="testo_mask">vecchia password</div>
			<div class="campo_mask">
				<input type="password" name="old_pwd" />
			</div>
		</span>
		<?php } ?>

		<span class="rigo_mask">
			<div class="testo_mask">nuova password</div>
			<div class="campo_mask">
				<input type="password" name="pwd" />
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">conferma password</div>
			<div class="campo_mask">
				<input type="password" name="repwd" />
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


// controllo d'accesso
// controllo d'accesso
if(isset($_SESSION['UTENTE'])) {

	// recupera i dati di sessione dell'utente
	$objUser = $_SESSION['UTENTE'];

	$back = "main.php";
	if(!isset($do)) $do ='';
	
	//if(authorize_me($objUser->get_userid(), $rule)) {

		if (empty($_POST)) $_POST['action'] = "";

		switch($_POST['action']) {

			case "change_pwd":
			change_pwd($_REQUEST['id']);
			break;

		}

		switch($do) {

			case "edit_pwd":
			html_header();
			view_pwd($_REQUEST['id']);
			html_footer();
			break;

			case "logout":
			logout();
			break;

		}
	//}
	//else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>