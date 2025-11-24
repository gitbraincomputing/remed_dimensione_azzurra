<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_area=$_REQUEST['id_area'];
//echo($id_area);
$conn = db_connect();


/*verificare che all'interno dell'area non siano compilate ISTANZE se
 questo è verificato allora si puo eliminare l'area*/

$query="SELECT dbo.sgq_valori.idarea, dbo.sgq_valori.idinserimento FROM dbo.sgq_valori 
		WHERE  dbo.sgq_valori.idarea = $id_area";
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0) {
	echo(mssql_num_rows($rs));
	}

?>

