<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$idregime=$_REQUEST['idregime'];

$conn = db_connect();
$query = "SELECT * FROM tiporeparto order by CodiceReparto ASC";
$rs = mssql_query($query, $conn);
?>
<option value="0">seleziona un reparto</option>
<?
while($row = mssql_fetch_assoc($rs)){?>
	<option value="<?=$row['CodiceReparto']?>"><?=$row['Descrizione']?></option>
<?
} 
mssql_free_result($rs);?>






