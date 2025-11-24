<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_gruppo=$_REQUEST['id_gruppo'];
$conn = db_connect();

$query="SELECT gid FROM dbo.operatori WHERE (gid = $id_gruppo)";
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0) echo(mssql_num_rows($rs));

?>