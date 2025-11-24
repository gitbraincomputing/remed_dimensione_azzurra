<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
//include_once('include/ftp.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
session_start();

function del_paziente($id) {
	global $tablename;
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query = "DELETE FROM utenti_allegati WHERE IdUtente='".$id."'";	
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM utenti_allegati_imp WHERE IdUtente='".$id."'";	
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM utenti_allegati_san WHERE IdUtente='".$id."'";	
	$result = mssql_query($query, $conn);
	$query = "DELETE FROM utenti WHERE IdUtente=".$id;		
	$result = mssql_query($query, $conn);	
	exit();
}

/************************************************************************
* funzione create()         						*
*************************************************************************/
function create() {

	global $tablename;

	$isroot = $_SESSION['UTENTE']->is_root();
	$ope = $_SESSION['UTENTE']->get_userid();
	$conn = db_connect();	
	
	$nome = ucfirst(strtolower($_POST['nome']));
	$cognome = ucfirst(strtolower($_POST['cognome']));
	$cognome=pulisci(firstupper($cognome));
	$nome=pulisci(firstupper($nome));	

	$sesso = $_POST['sesso'];
	$note_ana = pulisci($_POST['note_ana']);
	$data_nascita = $_POST['data_nascita'];
	$foto_paziente = $_FILES['foto_paziente']['name'];
	$comune_nascita = $_POST['comune_nascita'];	
	$codice_fiscale = $_POST['codice_fiscale'];
	$stato_nascita=$_POST['stato_nascita'];
	
	$indirizzo = pulisci($_POST['indirizzo']);
	$comune_residenza  = $_POST['comune_residenza'];
		
	$telefono = $_POST['telefono'];
	$cap_r = $_POST['cap_residenza'];
	$fax = $_POST['fax'];
	$cellulare = $_POST['cellulare'];
	$email = $_POST['email'];
	$cognome_t = ucfirst(strtolower($_POST['cognome_t']));
	$nome_t = ucfirst(strtolower($_POST['nome_t']));
	$cognome_t=pulisci(firstupper($cognome_t));
	$nome_t=pulisci(firstupper($nome_t));	
	
	$data_nascita_t = $_POST['data_nascita_t_'];
	$sesso_t = $_POST['sesso_t'];
	$codice_fiscale_t = $_POST['codice_fiscale_t'];
	if(isset($_POST['tutore_principale']))	$tutore_principale = -1;	else	$tutore_principale = 0;
	if(isset($_POST['tutore_1']))			$tutore1 = -1;				else	$tutore1 = 0;
	if(isset($_POST['tutore_2']))			$tutore2 = -1;				else	$tutore2 = 0;
	if(isset($_POST['delegato_1']))			$delegato1 = -1;			else	$delegato1 = 0;
	if(isset($_POST['delegato_2']))			$delegato2 = -1;			else	$delegato2 = 0;
	$indirizzo_t = pulisci($_POST['indirizzo_t']);
	$comune_residenza_t = $_POST['comune_residenza_t_'];
	$comune_residenza_tn = $_POST['comune_residenza_tn_'];
	
	$telefono_t = $_POST['telefono_t'];
	$cap_t = $_POST['cap_residenza_t'];
	$fax_t = $_POST['fax_t'];
	$cellulare_t = $_POST['cellulare_t'];
	$email_t = $_POST['email_t'];
	$relazione_t = pulisci($_POST['relazione_t']);	
	$status = $_POST['stato'];
	$reparto = $_POST['reparto_paziente'];
	
	$med_cognome = ucfirst(strtolower($_POST['cognome_m']));
	$med_nome = ucfirst(strtolower($_POST['nome_m']));
	$med_cognome=pulisci(firstupper($med_cognome));
	$med_nome=pulisci(firstupper($med_nome));	
	$med_indirizzo = pulisci($_POST['indirizzo_m']);
	$med_comune_residenza = $_POST['med_comune_residenza'];
	$med_cap_r = $_POST['med_cap_residenza'];	
	$med_telefono = $_POST['med_telefono'];	
	$med_fax = $_POST['med_fax'];
	$med_cellulare = $_POST['med_cellulare'];
	$med_email = $_POST['med_email'];
	
				
	// cripta la pwd con la chiave corrente
	$data=date(dmyHis);
	$foto_paziente=$data."_".$foto_paziente;
	$foto_paziente1="tb_".$foto_paziente;
	if ($_SESSION['foto_paziente']!="")$foto_paziente1=$_SESSION['foto_paziente'];
	$_SESSION['foto_paziente']="";
	$query = "INSERT INTO $tablename (note_ana,Cognome,Nome,Sesso,DataNascita,CodiceFiscale,LuogoNascita_id,stato,foto_paziente,opeins,stato_nascita) VALUES('$note_ana','$cognome','$nome','$sesso','$data_nascita','$codice_fiscale','$comune_nascita','$status','$foto_paziente1','$ope','$stato_nascita')";	
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
			
	$query = "SELECT MAX(idutente) as id FROM $tablename WHERE (opeins=$ope)";
	$rs = mssql_query($query, $conn);	
	$row=mssql_fetch_assoc($rs);
	$id_paz=$row['id'];	
	scrivi_log ($id_paz,'utenti','ins','idutente');
	
	$query1 = "update $tablename set CodiceUtente=$id_paz where idutente=$id_paz";
	$result1 = mssql_query($query1, $conn);
	
	
	// recupera l'id del record appena inserito
	$query = "SELECT MAX(idutente) FROM $tablename ";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if($row = mssql_fetch_row($result)) $idutente=$row[0];
		
	$query = "INSERT INTO utenti_residenze (IdUtente,indirizzo,comune_id,telefono,fax,cellulare,email,opeins,cap) VALUES('$idutente','$indirizzo','$comune_residenza ','$telefono','$fax','$cellulare','$email','$ope','$cap_r')";	
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	$query = "SELECT TOP 1 dbo.utenti_residenze.IdUtente, dbo.utenti_residenze.id as id, dbo.utenti_residenze.opeins FROM dbo.utenti_residenze WHERE (dbo.utenti_residenze.IdUtente = $idutente) and (dbo.utenti_residenze.opeins=$ope) ORDER BY dbo.utenti_residenze.id DESC";
	$rs = mssql_query($query, $conn);	
	$row=mssql_fetch_assoc($rs);
	$id_res=$row['id'];	
	scrivi_log ($id_res,'utenti_residenze','ins','id');
	
	$query = "INSERT INTO utenti_medici (IdUtente,nome,cognome,indirizzo,comune_id,telefono,fax,cellulare,email,opeins,cap) VALUES('$idutente','$med_nome','$med_cognome','$med_indirizzo','$med_comune_residenza ','$med_telefono','$med_fax','$med_cellulare','$med_email','$ope','$med_cap_r')";	
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	$query = "SELECT TOP 1 dbo.utenti_medici.IdUtente, dbo.utenti_medici.id as id, dbo.utenti_medici.opeins FROM dbo.utenti_medici WHERE (dbo.utenti_medici.IdUtente = $idutente) and (dbo.utenti_medici.opeins=$ope) ORDER BY dbo.utenti_medici.id DESC";
	$rs = mssql_query($query, $conn);	
	$row=mssql_fetch_assoc($rs);
	$id_med=$row['id'];	
	scrivi_log ($id_med,'utenti_medici','ins','id');
	
	if($cognome_t!=""){
		$query = "INSERT INTO utenti_tutor (IdUtente,nome,cognome,indirizzo,comune_id,telefono,fax,cellulare,email,relazione_paziente,opeins,cap,comune_id_n,data_nascita,codice_fiscale,sesso,tutoreprincipale,tutore1,tutore2,delegato1,delegato2) VALUES('$idutente','$nome_t','$cognome_t ','$indirizzo_t','$comune_residenza_t','$telefono_t','$fax_t','$cellulare_t','$email_t','$relazione_t','$ope','$cap_t','$comune_residenza_tn','$data_nascita_t','$codice_fiscale_t','$sesso_t',$tutore_principale,$tutore1,$tutore2,$delegato1,$delegato2)";
	
	
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	$query = "SELECT TOP 1 dbo.utenti_tutor.IdUtente, dbo.utenti_tutor.id as id, dbo.utenti_tutor.opeins FROM dbo.utenti_tutor WHERE (dbo.utenti_tutor.IdUtente = $idutente) and (dbo.utenti_tutor.opeins=$ope) ORDER BY dbo.utenti_tutor.id DESC";
	$rs = mssql_query($query, $conn);	
	$row=mssql_fetch_assoc($rs);
	$id_res=$row['id'];	
	scrivi_log ($id_res,'utenti_tutor','ins','id');	
	
	}
	
	if (!empty($reparto)) {
		$_sql = "INSERT INTO reparti_pazienti (id_reparto, id_paziente, ope_ins)
						VALUES ($reparto, $id_paz, $ope)";
		$_rs = mssql_query($_sql, $conn);
    }
	
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');

	//return $idutente;
		//echo("ok".$idutente);
	echo("ok;;2;lista_pazienti.php?do=review&id=".$id_paz);
	exit();

}


if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "principale.php";

	// verifica i permessi	
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
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
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
		}

		if($_REQUEST['action']=="del_paziente"){
			if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else del_paziente($_REQUEST['id_paziente']);
		}
		
		switch($do) {

			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
			break;

			case "edit":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit($_REQUEST['id']);
			break;
			
			case "review":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else review($_REQUEST['id']);
			break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
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