<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$idregime=$_REQUEST['idregime'];

$conn = db_connect();
$query = "SELECT idtipologia, idregime, descrizione, codice FROM dbo.tipologia WHERE (idregime =$idregime) and (cancella='n')";
$rs = mssql_query($query, $conn);
?>
<option value="0">seleziona una tipologia</option>
<option value="0">aggiorna elenco...</option>
<?
while($row = mssql_fetch_assoc($rs)){?>
	<option value="<?=$row['idtipologia']?>"><?=$row['codice']?> - <?=pulisci_lettura($row['descrizione'])?></option>
<?
} 
mssql_free_result($rs);?>






