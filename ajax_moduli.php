<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_mod = $_REQUEST['id_modulo'];
$conn = db_connect();

$query = "SELECT dbo.moduli.id FROM dbo.moduli INNER JOIN dbo.regimi_moduli ON dbo.moduli.idmodulo = dbo.regimi_moduli.idmodulo
			WHERE (dbo.moduli.id =  $id_mod)";
$rs = mssql_query($query, $conn);
if (mssql_num_rows($rs) > 0) {
	echo (mssql_num_rows($rs));
} else {
	$query = "SELECT dbo.moduli.id FROM dbo.cartelle_pianificazione INNER JOIN dbo.moduli ON dbo.cartelle_pianificazione.id_modulo_padre = dbo.moduli.idmodulo
			WHERE (dbo.moduli.id = $id_mod)";
	$rs = mssql_query($query, $conn);
	if (mssql_num_rows($rs) > 0) echo (mssql_num_rows($rs));
}
