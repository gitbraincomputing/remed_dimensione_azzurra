<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$idcomune=$_REQUEST['idcomune'];


$conn = db_connect();
$query = "SELECT denominazione, id_comune, com_cod_fisc FROM dbo.comuni WHERE (id_comune=".$idcomune.")";
$rs = mssql_query($query, $conn);
$row=mssql_fetch_assoc($rs);
echo($row['com_cod_fisc']);	
?>






