<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 62;
$tablename = 'normative';
include_once('include/function_page.php');

/************************************************************************
* funzione add_files()         						*
*************************************************************************/
function add_files() {

	$ope = $_SESSION['UTENTE']->get_userid();
	$id_utente=$id;	
	$tablename="sgq_allegati";
	$conn = db_connect();
	
	for( $i = 1; $i < 10; $i++){
		$descrizione = str_replace("'","''",$_POST['et'.$i.'']);
		$data=date(dmyHis);
		$nome_file=str_replace(" ","_",$_FILES['ulfile'.$i.'']['name']);
		$userfile_size = $_FILES['ulfile'.$i.'']['size'];
		$max_file=1;
		if ($userfile_size > ($max_file*10048576)) {
			$error.= "Images must be under ".$max_file."MB in size";
		}
		
		$file = $id."_".$data."_".$nome_file;
		$type = $_FILES['ulfile'.$i.'']['type'];	
		if ($descrizione!=""){
			$query = "INSERT INTO $tablename (descrizione,file_name,type,opeins) VALUES('$descrizione','$file ','$type',$ope)";			
			$result = mssql_query($query, $conn);
								
			if ((trim($_FILES['ulfile'.$i.'']['name']) != "")) {	
			//$nomefile = $_FILES['img_src']['name'];
			
			$upload_dir = ALLEGATI_MODELLI_SGQ;		// The directory for the images to be saved in
			$upload_path = $upload_dir."/";				// The path to where the image will be saved			
			$large_image_location = $upload_path.$file;
			$userfile_tmp = $_FILES['ulfile'.$i.'']['tmp_name'];
			//echo($large_image_location);			
			move_uploaded_file($userfile_tmp, $large_image_location);
			//chmod($large_image_location, 0777);
			
			}
			$query = "SELECT MAX(idallegato) FROM $tablename WHERE (opeins='$ope')";
			$result = mssql_query($query, $conn);
			if(!$result) error_message(mssql_error());

			if(!$row = mssql_fetch_row($result))
				die("errore MAX select");
				
			$id_all=$row[0];
			scrivi_log ($id_all,$tablename,'ins','id');
		}
	}	
	//echo("ok;".$id_utente.";3;re_pazienti_amministrativa.php?do=show_allegati&id=".$id_utente);	
	exit();
	/**/
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');
}


function update($id)
{



	$conn = db_connect();

	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);
	$opeins=$_SESSION['UTENTE']->get_userid();	
	$query = "UPDATE area SET area='$nome',descrizione='$descr' WHERE idarea='$id'";
	$result = mssql_query($query, $conn);
	if(!$result)
	{
				echo("no");
				exit();
				die();
				}
	// scrive il log
	scrivi_log ($id,'area','agg','idarea');	
	// rilascia l'id
	unset($id);

	// scrive il log
	//scrivi_log("users.php","M","users",$id);
	echo("ok;;1;lista_aree.php");	
	exit();

}

function confirm_del($id) {

	
}


function del($id) {

	

}



if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			case "add_files":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add_files();
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

