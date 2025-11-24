<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$idregime=$_REQUEST['idregime'];


$conn = db_connect();
$query = "SELECT idregime, idnormativa FROM regime WHERE (idregime=".$idregime.")";
$rs = mssql_query($query, $conn);
$row=mssql_fetch_assoc($rs);
echo($row['idnormativa']);	
?>






