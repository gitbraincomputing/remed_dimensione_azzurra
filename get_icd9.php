<?php
header("Content-type: text/html; charset=ISO-8859-1");
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

//include_once('include/function_page.php');

$valore = $_REQUEST['value'];
$conn = db_connect();
/* $lista_valori = split(' ', $valore);

for($i=0;$i<$count($lista_valori)-1;$i++){
	$descrizione = "(Descrizione LIKE '%".$lista_valori[$i]."%'";
	if($lista_valori[$i+1] != '')
		$descrizione.=" AND Descrizione LIKE '%".$lista_valori[$i+1]."%'";
	else
		$descrizione.=")";
}
 */
$query = "SELECT CodiceDiagnosi, Descrizione FROM ICD9Diagnosi WHERE CodiceDiagnosi LIKE '".$valore."%' OR Descrizione LIKE '%".$valore."%'";	
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());

while($row = mssql_fetch_assoc($rs))   
{
	echo '<p class="scegliIcd9" tabindex="0" onclick="hideListaIcd9(\''.$row['CodiceDiagnosi'].'\')">'.$row['CodiceDiagnosi'].' - '.$row['Descrizione'].'</p>';
}
mssql_free_result($rs);
?>