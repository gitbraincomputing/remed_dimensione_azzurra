<?php
include_once('include/class.User.php');
include_once('include/session.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/functions.inc.php');

$email = $_POST['email'];
$pwd = $_POST['password'];
$rpwd = $_POST['rpassword'];
$id=$_SESSION['UTENTE']->get_userid();

if(empty($email)){ 
	$err=1;
	}
	else {
		if($pwd!=$rpwd) {
			$err=2;
			}
			else {
				if ($pwd!="") $pwd_crypt = crypt($pwd,SALE);
				$conn = db_connect();

				$query = "UPDATE operatori SET email='$email'";
				if ($pwd!="") $query .=",password='$pwd_crypt' ";		
				$query .="WHERE uid='$id'";	
				$result = mssql_query($query, $conn);				
				if(!$result) error_message(mssql_error());
	
				// compila i campi di log
				scrivi_log ($id,'operatori','agg','uid');
				//echo($query);
				$err=3;
			}
	}	
echo("ok;;1;user_modificato.php?doing=".$err);	
	exit();
?>
	
	
	
	