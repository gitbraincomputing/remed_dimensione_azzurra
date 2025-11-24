<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$codfisc=$_REQUEST['codfisc'];


$conn = db_connect();
$query = "SELECT IdUtente as id, CodiceFiscale FROM dbo.utenti WHERE (CodiceFiscale='".$codfisc."')";
$rs = mssql_query($query, $conn);
$num_rows=mssql_num_rows($rs);
$row=mssql_fetch_assoc($rs);
$id_p=$row['id'];
if($_SESSION['id_paziente']=="") {
	if($num_rows!=0) echo("1");
	}else{	
	if(($num_rows!=0) and($id_p!=$_SESSION['id_paziente'])) echo("1");
}	
?>






