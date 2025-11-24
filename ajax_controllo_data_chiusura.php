<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$data=$_REQUEST['data'];
$idcartella=$_REQUEST['cartella'];

$res="0";
$conn = db_connect();
$query = "SELECT  dbo.utenti_cartelle_impegnative.idcartella, MAX(dbo.re_max_trattamenti_eseguiti_impegnative.data_ultima) AS ultimo_trattamento, 
         dbo.re_max_trattamenti_eseguiti_impegnative.presenze FROM dbo.utenti_cartelle_impegnative INNER JOIN dbo.re_max_trattamenti_eseguiti_impegnative ON 
         dbo.utenti_cartelle_impegnative.idimpegnativa = dbo.re_max_trattamenti_eseguiti_impegnative.idimpegnativa
		GROUP BY dbo.utenti_cartelle_impegnative.idcartella, dbo.re_max_trattamenti_eseguiti_impegnative.presenze
		HAVING (dbo.utenti_cartelle_impegnative.idcartella = $idcartella)";
$rs = mssql_query($query, $conn);
$num_rows=mssql_num_rows($rs);
$row=mssql_fetch_assoc($rs);
$ultimo_trat=formatta_data($row['ultimo_trattamento']);
if($data<$ultimo_trat) $res="1";

$query="SELECT dbo.utenti_cartelle_impegnative.idcartella, MAX(dbo.cartelle_moduli.ultima_compilazione) AS ultima_compilazione
		FROM dbo.utenti_cartelle_impegnative INNER JOIN dbo.cartelle_moduli ON dbo.utenti_cartelle_impegnative.idcartella = dbo.cartelle_moduli.idcartella
GROUP BY dbo.utenti_cartelle_impegnative.idcartella HAVING (dbo.utenti_cartelle_impegnative.idcartella = $idcartella)";
$rs = mssql_query($query, $conn);
$num_rows=mssql_num_rows($rs);
$row=mssql_fetch_assoc($rs);
$ultima_comp=formatta_data($row['ultima_compilazione']);
if($data<$ultima_comp) $res="1";
//echo("data:".$data." u_t:".$ultimo_trat." u_c:".$ultima_comp);

echo($res);
	
?>






