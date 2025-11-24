<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_range=$_REQUEST['id_range'];
$conn = db_connect();
$query="SELECT dbo.test_clinici_range.idrange, dbo.test_clinici.idtest
		FROM dbo.test_clinici_range INNER JOIN dbo.test_clinici ON dbo.test_clinici_range.idrange = dbo.test_clinici.idrange
		WHERE  (dbo.test_clinici_range.idrange =$id_range)";		
//echo($query);
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0) echo(mssql_num_rows($rs));
?>