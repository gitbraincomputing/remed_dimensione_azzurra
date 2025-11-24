<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 44;
$tablename = 'normative';
include_once('include/function_page.php');

function create()
{
	$isroot = $_SESSION['UTENTE']->is_root();	
	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);
	$int_str = pulisci($_POST['intestazione']);
	$piede_str = pulisci($_POST['piede']);
	$email = $_POST['email'];	
	$sito_web = $_POST['sito_web'];
	$piva = $_POST['piva'];	
	$status = $_POST['status'];
	$cod_struttura = $_POST['cod_struttura'];
	$reg_struttura = $_POST['reg_struttura'];
	$cod_asl_rif = $_POST['cod_asl_rif'];
	$distretto = $_POST['distretto'];
	
	// aggiunge i caratteri di escape
	$nome = str_replace("'","''",$nome);
	$descr = str_replace("'","''",$descr);	
	$conn = db_connect();

	
	$query = "INSERT INTO struttura (nome,descrizione,intestazione,piede,email,sitoweb,pi,opeins,status,cod_struttura,regione_struttura,cod_asl_rif,distretto) 
	VALUES('$nome','$descr','$int_str','$piede_str','$email','$sito_web','$piva','".$_SESSION['UTENTE']->get_userid()."','$status','$cod_struttura','$reg_struttura','$cod_asl_rif','$distretto')";
	$result = mssql_query($query, $conn);
	if(!$result)
	{		
				echo("no");
				exit();
				die();
	}

	// recupera l'id del record appena inserito
	$ope=$_SESSION['UTENTE']->get_userid();
	$query = "SELECT MAX(idstruttura) FROM struttura WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
	$result = mssql_query($query, $conn);
	if(!$result)
	{		
				echo("no");
				exit();
				die();
	}

	if(!$row = mssql_fetch_row($result))
		{		
				echo("no");
				exit();
				die();
	}
	
	// compila i campi di log
	$id_ope=$row[0];
	scrivi_log ($id_ope,'struttura','ins','idstruttura');
	
	$y=1;		
	$indirizzo = pulisci($_POST['indirizzo_sede'.$y]);
	$comune = pulisci($_POST['comune_sede'.$y]);
	$cap = $_POST['cap_sede'.$y];	
	$prov = $_POST['prov_sede'.$y];
	$tel = $_POST['telefono'.$y];
	$fax = $_POST['fax'.$y];
	$query = "INSERT INTO strutturasedi (Idstruttura,cdc,indirizzo,cap,comune,provincia,tel,fax,opeins) VALUES('$id_ope','$id_ope','$indirizzo','$cap','$comune','$prov','$tel','$fax','$ope')";			
	$rs = mssql_query($query, $conn);			
	$query = "SELECT TOP 1 strutturasedi.Idsede as id, strutturasedi.opeins, strutturasedi.Idstruttura FROM strutturasedi WHERE (strutturasedi.Idstruttura = $id_ope) and (strutturasedi.opeins=$ope) ORDER BY dbo.utenti_residenze.id DESC";		
	$rs = mssql_query($query, $conn);	
	$row=mssql_fetch_assoc($rs);
	$id_sed=$row['id'];	
	scrivi_log ($id_sed,'strutturasedi','ins','Idsede');				
	echo("ok;;1;re_struttura.php");	
	exit();

}


function update($id)
{
	$id_ope=$_SESSION['UTENTE']->get_userid();
	$id_str=$id;
	$nome = pulisci($_POST['nome']);
	$descr = pulisci($_POST['descr']);
	$int_str = pulisci($_POST['intestazione']);
	$piede_str = pulisci($_POST['piede']);
	$email = $_POST['email'];	
	$sito_web = $_POST['sito_web'];
	$piva = $_POST['piva'];	
	$status = $_POST['status'];
	$cod_struttura = $_POST['cod_struttura'];
	$reg_struttura = $_POST['reg_struttura'];
	$cod_asl_rif = $_POST['cod_asl_rif'];
	$distretto = $_POST['distretto'];
	
	// aggiunge i caratteri di escape
	$nome = str_replace("'","''",$nome);
	$descr = str_replace("'","''",$descr);	
	
	$conn = db_connect();

	$query = "UPDATE struttura SET nome='$nome',intestazione='$int_str',piede='$piede_str',descrizione='$descr',email='$email',sitoweb='$sito_web',
	pi='$piva',status='$status',codice_struttura='$cod_struttura' ,regione_struttura='$reg_struttura' ,cod_asl_rif='$cod_asl_rif' ,distretto='$distretto'  WHERE idstruttura='$id_str'";
	$result = mssql_query($query, $conn);
	if(!$result)
	{		
				echo("no");
				exit();
				die();
	}

	if ($_FILES['logo']['name']!=""){
		$logo=str_replace(" ","_",$_FILES['logo']['name']);		
		}
	
	$query = "UPDATE struttura SET logo='$logo' WHERE idstruttura='$id_str'";
	$result = mssql_query($query, $conn);
	if(!$result)
	 {
		echo ("no");
		exit();
		die();
		}
	
	if ((trim($_FILES['logo']['name']) != "")) {
		$upload_dir ="images";		// The directory for the images to be saved in
		$upload_path = $upload_dir."/";				// The path to where the image will be saved			
		$large_image_location = $upload_path.$logo;
		$userfile_tmp = $_FILES['logo']['tmp_name'];			
		move_uploaded_file($userfile_tmp, $large_image_location);
	}
	
	// compila i campi di log
	scrivi_log ($id_str,'struttura','agg','idstruttura');
	
	$query="SELECT * FROM strutturasedi WHERE Idstruttura='".$id_str."' order by strutturasedi.idsede asc";
	$result = mssql_query($query, $conn);				
	$z=1;
	while($row=mssql_fetch_assoc($result)){
		$y=$row['idsede'];		
		$indirizzo = pulisci($_POST['indirizzo_sede'.$y]);
		$comune = pulisci($_POST['comune_sede'.$y]);
		$cap = $_POST['cap_sede'.$y];	
		$prov = $_POST['prov_sede'.$y];
		$tel = $_POST['telefono'.$y];
		$fax = $_POST['fax'.$y];
		if (($_POST['flag_sede']=='true')and($z==1)){			
			$query = "INSERT INTO strutturasedi (Idstruttura,cdc,indirizzo,cap,comune,provincia,tel,fax,opeins) VALUES('$id_str','$id_str','$indirizzo','$cap','$comune','$prov','$tel','$fax','$id_ope')";			
			$rs = mssql_query($query, $conn);			
			$query = "SELECT TOP 1 strutturasedi.Idsede as id, strutturasedi.opeins, strutturasedi.Idstruttura FROM strutturasedi WHERE (strutturasedi.Idstruttura = $id_str) and (strutturasedi.opeins=$id_ope) ORDER BY dbo.utenti_residenze.id DESC";		
			$rs = mssql_query($query, $conn);	
			$row=mssql_fetch_assoc($rs);
			$id_sed=$row['id'];	
			scrivi_log ($id_sed,'strutturasedi','ins','Idsede');			
			} else{
			$query = "UPDATE strutturasedi SET Idstruttura='$id_str', cdc='$id_str', indirizzo='$indirizzo',comune='$comune',provincia='$prov',cap='$cap',tel='$tel',fax='$fax',opeins='$id_ope' WHERE idsede='".$y."'";			
			$rs = mssql_query($query, $conn);						
			scrivi_log ($y,'strutturasedi','agg','idsede');	
			}
		if ($z>1){
			$query = "UPDATE strutturasedi SET Idstruttura='$id_str', cdc='$id_str', indirizzo='$indirizzo',comune='$comune',provincia='$prov',cap='$cap',tel='$tel',fax='$fax',opeins='$id_ope' WHERE idsede='".$y."'";			
			$rs = mssql_query($query, $conn);						
			scrivi_log ($y,'strutturasedi','agg','Idsede');
		}		
		$z++;
	}			
	echo("ok;;1;re_struttura.php");	
	exit();

}

function confirm_del($id) {

	
}


if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

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
					else update($_POST['idstruttura']);
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

