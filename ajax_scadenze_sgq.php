<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$pk_area_moduli_scadenza=$_REQUEST['pk_area_moduli_scadenza'];
//echo($id_area);
$conn = db_connect();

/*questa query verifica se esiste almeno un valore non nullo dell'id_istanza_testata_sgq se esiste non fa effettuare la cancellazione*/
$query="SELECT * FROM dbo.scadenze_generate_sgq
		WHERE  (id_area_moduli_s=$pk_area_moduli_scadenza) AND (id_istanza_testata_sgq is not null)";
$rs = mssql_query($query,$conn);

if(mssql_num_rows($rs)>0){
    //se esiste almeno un'elemento non nullo allora non fa effettuare la cancellazione e restituisce l'echo
    echo(mssql_num_rows($rs));	
}

?>
