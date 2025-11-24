<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');

$conn = db_connect();

$query = "DELETE FROM area_moduli_scadenza_sgq WHERE memorizzazione is null";	
$result = mssql_query($query, $conn);
echo("ok");
?>