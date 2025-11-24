<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_paz=$_REQUEST['id_paziente'];
$conn = db_connect();

$query="SELECT idutente FROM dbo.utenti_cartelle WHERE (idutente =  $id_paz)";
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0){ 
	echo(mssql_num_rows($rs));
	}else{
		$query="SELECT id_paziente FROM dbo.istanze_testata WHERE (id_paziente = $id_paz)";
		$rs = mssql_query($query, $conn);
		if(mssql_num_rows($rs)>0) {
			echo(mssql_num_rows($rs));
		}else{
			$query="SELECT idutente FROM dbo.impegnative WHERE (idutente = $id_paz)";
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs)>0) echo(mssql_num_rows($rs));
		}
	}
?>

