<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_mod=$_REQUEST['id_modulo'];
$conn = db_connect();
/*
$query="SELECT dbo.sgq_valori.idmodulo, dbo.sgq_valori.idinserimento FROM dbo.sgq_valori 
		WHERE  dbo.sgq_valori.idmodulo = $id_mod";
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0) {
    //se si verifica allora in msg va in automatico il contenuto di echo e in questo modo non mi fa effettuare 
    //automaticamente la cancellazione
	echo(mssql_num_rows($rs));
	}
*/
$query="SELECT *
        From dbo.aree_moduli
		WHERE dbo.aree_moduli.idmodulo = $id_mod";
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0) {
    //se si verifica allora in msg va in automatico il contenuto di echo e in questo modo non mi fa effettuare 
    //automaticamente la cancellazione
	echo(mssql_num_rows($rs));
	}
?>

