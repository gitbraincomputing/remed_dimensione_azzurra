<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_ope=$_REQUEST['id_operatore'];
$conn = db_connect();

$query="SELECT opeins FROM dbo.istanze_testata WHERE (opeins = $id_ope)";
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0){ 
	echo(mssql_num_rows($rs));
	}else{
		$query="SELECT id_operatore FROM dbo.cartelle_pianificazione WHERE (id_operatore = $id_ope)";
		$rs = mssql_query($query, $conn);
		if(mssql_num_rows($rs)>0) {
			echo(mssql_num_rows($rs));
			}else{
			$query="SELECT idmedico_creazione, idmedico_chiusura FROM dbo.utenti_cartelle WHERE (idmedico_chiusura = $id_ope) OR (idmedico_creazione = $id_ope)";
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs)>0) echo(mssql_num_rows($rs));
			}
	}

?>

