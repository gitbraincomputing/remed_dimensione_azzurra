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
$tablename = 'immagini';
$id_permesso = $id_menu = 3;
$pagetitle = "gestione immagini";

// recupera la struttura corrente
//$SID = $_SESSION['STRUTTURA'];


/****************************
* funzione add()            *
*****************************/
function add() {

	global $PHP_SELF, $tablename, $pagetitle;
	$array_mod=array();
	$admin=$_SESSION['UTENTE']->is_root();
	//$sid = $_SESSION['STRUTTURA1']->get_id();

	open_center();
?>
		<form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" onSubmit="return controlla_campi_form('nome','nome','0');">
		<input type="hidden" name="action" value="create" />

		<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - aggiungi</small></h1></div>

		<div class="blocco_centralcat">			
			<span class="rigo_mask">
				<div class="testo_mask">network</div>
				<div class="campo_mask">
					<select name="annuario" class="scrittura">
					<?php
					$conn = db_connect();
					$query = "SELECT * from annuario WHERE status=1 AND cancella='n'";
					$rs = mssql_query($query, $conn);
					if(!$rs) error_message(mssql_error());
				
					while($row = mssql_fetch_assoc($rs)){
				
						$idann = $row['id'];
						$nome = $row['nome'];
						
						print('<option value="'.$idann.'">'.$nome.'</option>');
					}
					mssql_free_result($rs);
					?>
					</select>
				</div>
			</span>
            <span class="rigo_mask">
				<div class="testo_mask"><span class="need">nome immagine</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="nome" size="100" maxlength="255">
				</div>
			</span>
            <span class="rigo_mask">
            	<div class="testo_mask"><span class="need">immagine</span></div>
				<div class="campo_mask">                			
                    <input type="file" class="scrittura" name="img_1_src" size="25" />              
				</div>                
			</span>
            <span class="rigo_mask">
				<div class="testo_mask">alt immagine</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="alt_immagine" size="100" maxlength="255">
				</div>
			</span>
            <span class="rigo_mask">
				<div class="testo_mask">url immagine</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="url_immagine" size="100" maxlength="255">
				</div>
			</span>           
            
			<span class="rigo_mask">
				<div class="testo_mask">stato</div>
				<div class="campo_mask">
					<select name="status" class="scrittura">
						<option value="1" selected>online</option>
						<option value="0">offline</option>
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


/****************************
* funzione edit($id)        *
*****************************/
function edit($id){

	global $PHP_SELF, $tablename, $pagetitle, $id_permesso;	
	$conn = db_connect();
	$query = "SELECT * from $tablename WHERE id='$id'";
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$nome_img = $row['nome'];
		$immagine = $row['immagine'];
		$annuario = $row['annuario'];
		$alt_immagine = $row['alt'];		
		$url_immagine=$row['url'];								
	}
	
	mssql_free_result($rs);

	open_center();
?>
		<form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" onSubmit="return controlla_campi_form('nome','nome','0');">
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />

		<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - edita</small></h1></div>

		<div class="blocco_centralcat">
		   
			<span class="rigo_mask">
				<div class="testo_mask">network</div>
				<div class="campo_mask">
					<select name="annuario" class="scrittura">
					<?php
					$query = "SELECT * from annuario WHERE status=1 AND cancella='n'";
					$rs = mssql_query($query, $conn);
					if(!$rs) error_message(mssql_error());
				
					while($row = mssql_fetch_assoc($rs)){
				
						$idann = $row['id'];
						$nome = $row['nome'];
						
						print('<option value="'.$idann.'"');
						if($idann == $annuario) echo " selected";
						print('>'.$nome.'</option>');
					}
					mssql_free_result($rs);
					?>
					</select>
				</div>
			</span>
            <span class="rigo_mask">
				<div class="testo_mask"><span class="need">nome immagine</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="nome" size="100" maxlength="255" value="<?php print($nome_img); ?>">
				</div>
			</span>
            <span class="rigo_mask">
            	<div class="testo_mask"><span class="need">immagine</span></div>
				<div class="campo_mask">                			
                    <input type="file" class="scrittura" name="img_1_src" size="25" /><br />
                    <img src="images/<?php print($immagine); ?>" class="scrittura" name="img_1_dis" />                 
				</div>                
			</span>
            <span class="rigo_mask">
				<div class="testo_mask">alt immagine</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="alt_immagine" size="100" maxlength="255" value="<?php print($alt_immagine); ?>">
				</div>
			</span>
            <span class="rigo_mask">
				<div class="testo_mask">url immagine</div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="url_immagine" size="100" maxlength="255" value="<?php print($url_immagine); ?>">
				</div>
			</span>           
            
			<span class="rigo_mask">
				<div class="testo_mask">stato</div>
				<div class="campo_mask">
					<select name="status" class="scrittura">
					   <?php if ($status) {	?>
						<option value="1" selected>online</option>
						<option value="0">offline</option>
					    <?php } else { ?>
						<option value="1" >online</option>
						<option value="0" selected>offline</option>
					  <?php	} ?>
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





/****************************
* funzione create()         *
*****************************/
function create() {
	
	global $tablename;
	//$immagine = $_POST['img_1_src'];
	$nome=$_POST['nome'];
	$annuario = $_POST['annuario'];
	$status = $_POST['status'];
	$alt_immagine=$_POST['alt_immagine'];
	$url_immagine=$_POST['url_immagine'];
	
	
	$conn = db_connect();

	// verifica se la cat merc esiste già
	$query = "SELECT id FROM $tablename WHERE nome='".$nome."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	if(mssql_num_rows($result)) error_message("L'immagine ".$nome." è già esistente. Provare con un altro nome.");
	mssql_free_result($result);


	$query = "INSERT INTO $tablename(nome,immagine,annuario,opeins,status,alt,url) VALUES('$nome','".$_FILES['img_1_src']['name']."','$annuario','".$_SESSION['UTENTE']->get_userid()."','$status','$alt_immagine','$url_immagine')";

	//echo $query;

	$result = mssql_query($query, $conn);
	if(!$result) error_message("non posso");

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM $tablename WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		die("errore MAX select");
	
// immagine	
if ((trim($_FILES['img_1_src']['name']) != "")) {	
	$nomefile = $_FILES['img_1_src']['name'];
	
	$imgHNDL = fopen($_FILES['img_1_src']['tmp_name'], "r");
	$imgf = fread($imgHNDL, $_FILES['img_1_src']['size']);
	//$eimgf = mysql_escape_string($imgf);

	// apre la connessione ftp
	$conn_id = ftp_connect(IP_ADDR);	

	// login con user name e password
	$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 

	// controllo della connessione
	if ((!$conn_id) || (!$login_result)) { 
		echo "La connessione FTP è fallita!";
		echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 
		die; 	
	} else {
		
		// se il file non esiste, lo crea via ftp
		//if (!file_exists($curr_path.'/'.$filename)) {

			// passa in passive mode			
			ftp_pasv ( $conn_id, true );
			
			$temp = tmpfile();
			fwrite($temp, $imgf);
			fseek($temp, 0);

			// si posiziona nella directory giusta
			if (!ftp_chdir($conn_id, IMAGE_PATH)) {
				 error_message("non posso cambiare dir");
				 exit;
			} 
			
			// upload del file
			if(! ($upload = ftp_fput ($conn_id, $nomefile , $temp ,FTP_BINARY))) {
				 error_message("errore upload del file");
				 exit;
			}
			
			// chiude l'handler del file
			fclose($temp);

	}

	fclose($imgHNDL);

	// chiudere il flusso FTP 
	ftp_quit($conn_id); 
	}
		
	// scrive il log
	scrivi_log ($row[0],$tablename,'ins');

}


/****************************
* funzione update($sid)     *
*****************************/
function update($id) {

	global $PHP_SELF, $tablename;
	$nome = $_POST['nome'];
	$annuario = $_POST['annuario'];
	$status = $_POST['status'];
	$alt_immagine=$_POST['alt_immagine'];
	$url_immagine=$_POST['url_immagine'];	


	$conn = db_connect();

	// recupera il vecchio nome della struttura
	$query = "SELECT nome from $tablename WHERE id='$id'";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	if($row = mssql_fetch_assoc($rs))
		$old_dirname = pulisci(ucfirst($row['nome']));
	mssql_free_result($rs);

		
	if(strcmp($old_dirname,$nome)) {
		// verifica se la struttura non esiste
		$query = "SELECT id FROM $tablename WHERE nome='".$nome."'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());
		if(mssql_num_rows($result)) error_message("L'immagine ".$nome." è già esistente! Provare con una altro nome.");
		mssql_free_result($result);
	}

	// aggiunge i caratteri di escape
	//$nome = str_replace("'","''",$nome);
$nomefile1 = $_FILES['img_1_src']['name'];

if ($nomefile1 != ""){
	$query = "UPDATE $tablename SET nome='$nome',immagine='".$_FILES['img_1_src']['name']."',annuario='$annuario',opeins='".$_SESSION['UTENTE']->get_userid()."',status='$status', alt_immagine='$alt_immagine',url_immagine='$url_immagine' WHERE id='$id'";}
	else {$query = "UPDATE $tablename SET nome='$nome',annuario='$annuario',opeins='".$_SESSION['UTENTE']->get_userid()."',status='$status', alt_immagine='$alt_immagine',url_immagine='$url_immagine' WHERE id='$id'";}

	
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	
if ((trim($_FILES['img_1_src']['name']) != "")) {	
	$nomefile = $_FILES['img_1_src']['name'];
	
	$imgHNDL = fopen($_FILES['img_1_src']['tmp_name'], "r");
	$imgf = fread($imgHNDL, $_FILES['img_1_src']['size']);
	//$eimgf = mysql_escape_string($imgf);

	// apre la connessione ftp
	$conn_id = ftp_connect(IP_ADDR);	

	// login con user name e password
	$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 

	// controllo della connessione
	if ((!$conn_id) || (!$login_result)) { 
		echo "La connessione FTP è fallita!";
		echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 
		die; 	
	} else {
		
		// se il file non esiste, lo crea via ftp
		//if (!file_exists($curr_path.'/'.$filename)) {

			// passa in passive mode			
			ftp_pasv ( $conn_id, true );
			
			$temp = tmpfile();
			fwrite($temp, $imgf);
			fseek($temp, 0);

			// si posiziona nella directory giusta
			if (!ftp_chdir($conn_id, IMAGE_PATH)) {
				 error_message("non posso cambiare dir");
				 exit;
			} 
			
			// upload del file
			if(! ($upload = ftp_fput ($conn_id, $nomefile , $temp ,FTP_BINARY))) {
				 error_message("errore upload del file");
				 exit;
			}
			
			// chiude l'handler del file
			fclose($temp);

	}

	fclose($imgHNDL);

	// chiudere il flusso FTP 
	ftp_quit($conn_id); 
	}
	
// scrive il log
	scrivi_log ($id,$tablename,'agg');
}



/****************************
* funzione confirm_del($id) *
*****************************/
function confirm_del($id) {

	global $PHP_SELF, $tablename, $pagetitle;

	$conn = db_connect();

	$query = "SELECT nome FROM $tablename where id=$id";
	if(! ($rs = mssql_query($query, $conn))) error_message(mssql_error());
	if(! ($row = mssql_fetch_row($rs))) error_message(mssql_error());
	mssql_free_result($rs);
	open_center();
?>
	<form method="post" name="modulo" action="<?php echo $PHP_SELF ?>?id=<?php echo $id ?>">
	<input type="hidden" name="action" value="del" />
	
	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - conferma cancellazione</small></h1></div>

	<div class="blocco_centralcat">

		<div id="messaggio_cancella">Cancellare la categoria merceologica<br /><strong><?php echo $row[0]; ?></strong> ?</div>
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

	$query = "SELECT COUNT (idstruttura) FROM strutture_prodotti WHERE idprodotto='".$id."' GROUP BY idprodotto";
	$result = mssql_query($query, $conn);
	if(!$result) error_message("errore");
	$row = mssql_fetch_row($result);

	?>

	<script> confirm("Eliminando la categoria merceologica verranno aggiornate <?php print $row[0]; ?> strutture. Continuare?");</script>
	<?php


	// cancella l'elemento selezionato
	$query = "UPDATE $tablename SET cancella='y' WHERE id='".$id."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());


	// scrive il log
	scrivi_log ($id,$tablename,'del');

}


/****************************
 funzione show_list()      *
*****************************/
function show_list(){
	
	global $PHP_SELF, $tablename, $pagetitle, $id_menu, $id_permesso;

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
		
	//se non è settato il flag, fai una nuova query
	if($_SESSION['RICALCOLA']) {
	
		$_SESSION['TROVATE'] = array();

		// fa la query dei record non cancellati
		if(isset($_REQUEST['letter'])) {
			if($_REQUEST['letter']=='ALL') $query = "SELECT id from $tablename WHERE cancella='n'";
			else $query = "SELECT id FROM $tablename WHERE cancella='n' AND  nome LIKE \"".$_REQUEST['letter']."%\"";
	
		} else {
		
			$query = "SELECT id FROM $tablename WHERE cancella='n'";
		}
		
		
		if(isset($_SESSION['ANNUARIO']) AND $_SESSION['ANNUARIO']) $query .= " AND annuario=".$_SESSION['ANNUARIO'];
		
		// order by
		$query .=" ORDER BY ";
		if(isset($_SESSION['ORDINAPER'])) {
			
			// criterio 1
			if($_SESSION['ORDINAPER']==1) $query .="$tablename.id";
			else if($_SESSION['ORDINAPER']==2) $query .= "$tablename.nome";
			else $query .= "$tablename.nome";
		}
		else $query .= "$tablename.id DESC";		
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
	
		while($row = mssql_fetch_row($rs)){
			// riempie l'array
			array_push($_SESSION['TROVATE'], $row[0]);
		}
		
		$_SESSION['RICALCOLA'] = false;
	}
	
	// recupera il n.
	$num_rows = sizeof($_SESSION['TROVATE']);	

	if($num_rows > 0) {
	
		print('<div id="pulsantiera">');
		lettere_alfabetiche();
		GestionePagine($num_rows, $page, $tablename);
		$arr1= array('id','alfabetico');
		$arr2 = array();
		ordina_per ($arr1, $arr2);
		print('</div>');
	
		print('<p class="intest">Sono state trovate <strong>'.$num_rows.'</strong> categorie news');
		if(isset($_REQUEST['letter'])) print(' con la lettera <strong>'.$_REQUEST['letter'].'</strong>');
		print('.</p>'."\n");

		$rpp = $_SESSION['NXPAGINA'];
	
		// calcola la prima e l'ultima pagina
		$prima = ($rpp * ($page-1));
		$ultima = ($rpp * $page);

		print('<ul>'."\n");

		/*print('<li>');
		print('<span class="id_notizia_cat_link">id</span>'."\n");
		print('<span class="id_notizia_cat_link">nome</span>'."\n");
		print('</li>');*/

		$count = 0;
		// parte il ciclo
		for($i=$prima; $i<$ultima; $i++) {
	
			if($i<sizeof($_SESSION['TROVATE'])) {

				$query  ="SELECT nome,status FROM $tablename WHERE id='".$_SESSION['TROVATE'][$i]."'";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				
				$row = mssql_fetch_assoc($rs);
	
				$id = $_SESSION['TROVATE'][$i];
				$nome = stripslashes($row['nome']);
				$status = $row['status'];
	
				print('<li>');
				print('<span class="id_notizia_cat">'.$id.'</span>'."\n");
				print('<span class="notizia_cat">'."\n");
				print('<a href="principale.php?page='.$PHP_SELF.'&do=edit&id='.$id.'" target="_parent">');
				if(!$status) 
					print ('<span class="offline">'.$nome.' (offline)</span>'."\n");
				else 
					print($nome);
				print('</a>');
				print('</span>'."\n");
				
				// visualizza le icone dei permessi
				//icone_permessi($id, $id_menu);
	
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

	else print ('<p class="none">Nessuna categoria news presente.</a>');

	print("\n".'</div>'."\n");

	// chiude il div centrale
	close_center();
}




// controllo d'accesso
$_SESSION['RICALCOLA'] = true;
if(isset($_SESSION['UTENTE'])) {
	
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if(!isset($_SESSION['RICALCOLA'])) $_SESSION['RICALCOLA'] = true;
		
		// se non è stato mai settato ricalcola, settalo a true
		if(!isset($_SESSION['RICALCOLA'])) $_SESSION['RICALCOLA'] = true;

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
					else {						
						create();						
						$_SESSION['RICALCOLA'] = true;
					}
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
					else if($_POST['choice']=="si") {
						del($_REQUEST['id'],'n');
						$_SESSION['RICALCOLA'] = true;
					}
			break;

			case "ordina":
				$_SESSION['RICALCOLA'] = true;
				$_SESSION['ORDINAPER'] = $_POST['ordinaper'];
				//$_SESSION['ORDINAPER2'] = $_POST['ordinaper2'];
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