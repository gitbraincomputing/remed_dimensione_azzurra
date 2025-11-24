<?
include_once('include/function_page.php');	
global $principale;
$principale=true;
if (is_object($_SESSION['UTENTE'])) 
	{
	if($_REQUEST['mod']=='ok')	
		include_once('lista_modelli_file_sgq.php');
	else
		include_once('review.php');
	}
	else{
	$principale=false;
	header("location:index.php");
	}
	
?>
