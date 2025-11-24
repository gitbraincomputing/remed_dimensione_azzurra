<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$idmedico=$_REQUEST['idmedico'];


$conn = db_connect();
$query = "SELECT * FROM re_medici_medicina_generale WHERE (re_medici_medicina_generale.id = $idmedico)";
$rs = mssql_query($query, $conn);
if($row = mssql_fetch_assoc($rs)){
	$cognome = pulisci_lettura($row['cognome']);
	$nome = pulisci_lettura($row['nome']);
	$indirizzo= pulisci_lettura($row['indirizzo']);
	$nome_prov=pulisci_lettura($row['nome_provincia']);
	$id_prov=$row['id_provincia'];
	$nome_com=pulisci_lettura($row['nome_comune']);
	$id_com=$row['id_comune'];
	$cap=trim($row['cap']);
	$telefono=trim($row['telefono']);
	$fax=trim($row['fax']);
	$cellulare=trim($row['cellulare']);
	$email=trim($row['email']);
}
$stringa=$cognome.";".$nome.";".$indirizzo.";".$nome_prov.";".$id_prov.";".$nome_com.";".$id_com.";".$cap.";".$telefono.";".$fax.";".$cellulare.";".$email;
echo($stringa);	
?>






