<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_test=$_REQUEST['id_test'];
$conn = db_connect();
$query="SELECT id_pianificazione_test, id_test FROM  dbo.cartelle_pianificazione_test WHERE  (id_test =$id_test)";		
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0) echo(mssql_num_rows($rs));
$query="SELECT idtest_ramificato, idtest_padre, test_ramificato, idtest_figlio FROM dbo.test_clinici_ramificati
WHERE     (idtest_figlio =$id_test)";		
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0) echo(mssql_num_rows($rs));

?>