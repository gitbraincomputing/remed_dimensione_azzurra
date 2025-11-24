<?
//Script Initial load per popolare i codici stato dei pazienti - Necessari per il filtro della ricerca tramite stato

include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_mod=$_REQUEST['id_modulo'];
$conn = db_connect();

$query="SELECT IdUtente,stato_impegnativa from utenti";
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0){ 
	while($row=mssql_fetch_row($rs)){
		$stato=get_stato_paziente($row[0]);
		$query1="UPDATE utenti SET stato_impegnativa=$stato WHERE IdUtente=$row[0]";
		echo($query1."<br/>");
		$rs1 = mssql_query($query1, $conn);
	}
}
?>

