<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$idcombo=$_REQUEST['idcombo'];


$conn = db_connect();
//$query = "SELECT id, nome FROM dbo.province WHERE (id=".$id_prov.")";
//$rs = mssql_query($query, $conn);
//$row=mssql_fetch_assoc($rs);
//$prov_nome=$row['nome'];
//mssql_free_result($rs);
//$prov_nome=str_replace("'","''",$prov_nome);
$query="select * from campi_combo_ramificate where (idcombo='$idcombo') and(cancella='n') and (stato=1)";

?>
<select name="idvalorepadre">
<option value="0">selezionare</option>
<?

    $rs = mssql_query($query, $conn);
    if(!$rs) error_message(mssql_error());
	while($row1 = mssql_fetch_assoc($rs)){
						$idcampo= $row1['idcampo'];
						$etichetta= $row1['etichetta'];												
						print('<option value="'.$idcampo.'"');     							
						print ('>'.$etichetta.'</option>'."\n");
					
	}
?>
</select>
<?
exit();	
					





