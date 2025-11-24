<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$nome_com=$_REQUEST['nome_com'];
$id_req=$_REQUEST['id_req'];

$conn = db_connect();
$query = "SELECT id_comune, denominazione, cap FROM dbo.comuni WHERE (id_comune='".$nome_com."')";
$rs = mssql_query($query, $conn);
$row = mssql_fetch_assoc($rs);
	switch ($id_req) {
		case 1:
		?>
 		<input type="text" class="scrittura" name="cap_residenza" SIZE="30" maxlenght="30" value="<?=$row['cap']?>"/>
	<?php  
		break;
		case 2:
		?>
 		<input type="text" class="scrittura" name="cap_residenza_t" SIZE="30" maxlenght="30" value="<?=$row['cap']?>"/>
	<?php 
		break;
		case 3:
		?>
 		<input type="text" class="scrittura" name="med_cap_residenza" SIZE="30" maxlenght="30" value="<?=$row['cap']?>"/>
	<?php 
		break;		
	}	
    ?>






