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
		$descrizione = pulisci($_POST['et'.$i.'']);
		$data=date(dmyHis);
		$nome_file=str_replace(" ","_",$_FILES['ulfile'.$i.'']['name']);
		$userfile_size = $_FILES['ulfile'.$i.'']['size'];
		$max_file=1;
		if ($userfile_size > ($max_file*10048576)) {
			$error.= "Images must be under ".$max_file."MB in size";
		}
		
		$file = $data."_".$nome_file;
		$type = $_FILES['ulfile'.$i.'']['type'];
		$revisione = pulisci($_POST['revisione'.$i.'']);
		$edizione =  pulisci($_POST['edizione'.$i.'']);	
		if ($descrizione!=""){
			$query = "INSERT INTO $tablename (descrizione,file_name,type,opeins,revisione,edizione) VALUES('$descrizione','$file ','$type',$ope,'$revisione','$edizione')";			
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
			scrivi_log ($id_all,$tablename,'ins','idallegato');
		}
	}	
	echo("ok;;1;lista_modelli_file_sgq.php");		
	exit();	
}


function update($id)
{
	$ope = $_SESSION['UTENTE']->get_userid();
	$id_utente=$id;	
	$tablename="sgq_allegati";
	$conn = db_connect();
	
	for( $i = 1; $i < 2; $i++){
		$descrizione = pulisci($_POST['et'.$i.'']);
		$data=date(dmyHis);
		$nome_file=str_replace(" ","_",$_FILES['ulfile'.$i.'']['name']);
		$userfile_size = $_FILES['ulfile'.$i.'']['size'];
		$max_file=1;
		if ($userfile_size > ($max_file*10048576)) {
			$error.= "Images must be under ".$max_file."MB in size";
		}
		
		$file = $data."_".$nome_file;
		$type = $_FILES['ulfile'.$i.'']['type'];
		$revisione = pulisci($_POST['revisione'.$i.'']);
		$edizione =  pulisci($_POST['edizione'.$i.'']);	
		if ($descrizione!=""){
			if($nome_file!="")
				$query = "Update $tablename SET descrizione='$descrizione',file_name='$file',type='$type',revisione='$revisione',edizione='$edizione' WHERE idallegato=$id";
				else
				$query = "Update $tablename SET descrizione='$descrizione',revisione='$revisione',edizione='$edizione' WHERE idallegato=$id";
			//echo($query);
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
			scrivi_log ($id_all,$tablename,'ins','idallegato');
		}
	}	
	echo("ok;;1;lista_modelli_file_sgq.php");		
	exit();	


}



function del_file($idallegato) {

	$ope = $_SESSION['UTENTE']->get_userid();
	$id_utente=$id;	
	$tablename="sgq_allegati";
	$conn = db_connect();
	$query = "UPDATE sgq_allegati SET cancella='y' WHERE idallegato='$idallegato'";
	mssql_query($query, $conn);
	echo("ok");
	exit();

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
			
			case "del_file":
				del_file($_POST['idallegato']);
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

		

			case "del_file":
				del_file($_REQUEST['idallegato']);
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

