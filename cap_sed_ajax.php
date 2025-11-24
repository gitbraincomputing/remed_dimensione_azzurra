<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$nome_com=$_REQUEST['nome_com'];
$id_req=$_REQUEST['id_req'];

$conn = db_connect();
$query = 'SELECT denominazione, cap FROM dbo.comuni WHERE (denominazione="'.$nome_com.'")';
$rs = mssql_query($query, $conn);
$row = mssql_fetch_assoc($rs);
    ?>
	<input type="text" class="scrittura" name="cap_sede<?=$id_req?>" SIZE="30" maxlenght="30" value="<?=$row['cap']?>"/>






