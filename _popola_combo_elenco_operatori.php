<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');


/*
questo file è servito (e può riservire) per popolare la combo multipla "elenco operatori" con ID 195.
Cicla tutti gli operatori attivi e li inserisce in "campi_combo".
NON LANCIARE MAI - E' QUI SOLO PER I POSTERI
*/




// LEGGE I RECORD IN TBL "ISTANZE DETTAGLIO" E AGGIORNA I VALORI LEGATI ALLA COMBO "ELENCO OPERATORI" (ID 195) INSERITI
// QUESTO PERCHE IN ORIGINE LA COMBO AVEVA COME VALORI 1,2,3, ECC.. MENTRE SUCCESSIVAMENTE QUESTI VALORI SONO STATI SOSTITUITI CON GLI ID DEGLI OPERATORI

$conn = db_connect();

/*
$query1 = "SELECT id_istanza_dettaglio, id_istanza_testata, idcampo, valore FROM istanze_dettaglio
			WHERE idcampo IN (SELECT idcampo FROM campi where segnalibro = 'combo_elenco_operatori')";	
$result1 = mssql_query($query1, $conn);
if(!$result1) error_message(mssql_error());

while($row1 = mssql_fetch_assoc($result1))  
{
	$id_istanza_dettaglio 	= $row1['id_istanza_dettaglio'];
	$id_istanza_testata 	= $row1['id_istanza_testata'];
	$idcampo 	 			= $row1['idcampo'];
	$valore 	 			= $row1['valore'];
	

	$nuovo_valore = "";
	
	$valoreArr = explode(";", $valore);
	
	
	for ($i = 0; $i < count($valoreArr); $i++)
	{
		$query2 = "SELECT etichetta FROM campi_combo
					WHERE idcombo = 195 AND valore = '$valoreArr[$i]'";	
		$result2 = mssql_query($query2, $conn);
		if(!$result2) error_message(mssql_error());
		$row2 = mssql_fetch_assoc($result2);
		$etichetta = $row2['etichetta'];
		mssql_free_result($result2);
		
		if ( strpos($valore, ';') === false)
			 $nome_operatore = str_replace("'", "''", strtoupper($etichetta));
		else {
			$nome_operatoreArr = explode(" -", $etichetta);
			$nome_operatore = str_replace("'", "''", strtoupper($nome_operatoreArr[0]));
		}

		//echo "$nome_operatore - ";
		
		$query3 = "SELECT uid FROM operatori 
					WHERE nome = '$nome_operatore'";	
		$result3 = mssql_query($query3, $conn);
		if(!$result3) error_message(mssql_error());
		$row3 = mssql_fetch_assoc($result3);
		$uid = $row3['uid'];
		mssql_free_result($result3);
		//echo $uid."<br>";
		
		$nuovo_valore .= "$uid;";
	}
	
	
	$nuovo_valore = substr($nuovo_valore, 0, -1);
	
	$query4 = "UPDATE istanze_dettaglio SET valore = '$nuovo_valore' 
				WHERE id_istanza_dettaglio = $id_istanza_dettaglio AND id_istanza_testata = $id_istanza_testata AND idcampo = $idcampo";	
	echo "$query4<br><br>";
	$result4 = mssql_query($query4, $conn);
	if(!$result4) error_message(mssql_error());	
}

	
	
exit();





// POPOLA LA COMBO "ELENCO OPERATORI" (ID 195) CON I NOMI, L'ORDINE PROFESSIONALE E IL NUMERO DI ISCRIZIONE DEGLI OPERATORI ATTIVI 


$query0 = "DELETE FROM campi_combo WHERE idcombo = 195"
$result0 = mssql_query($query0, $conn);
if(!$result0) error_message(mssql_error());

*/
$query1 = "SELECT uid, nome, ordine_professionale, num_iscrizione, cancella FROM operatori
			WHERE cancella = 'n' and status=1 AND nome NOT LIKE '%Software Administrator%' AND nome != 'admin'
			ORDER BY nome ASC";	
$result1 = mssql_query($query1, $conn);
if(!$result1) error_message(mssql_error());

mssql_query('DELETE FROM campi_combo WHERE idcombo IN (195,266)', $conn);

$i = 1;
while($row1 = mssql_fetch_assoc($result1))   
{
	$id 		 			= $row1['uid'];
	$nome 		 			= $row1['nome'];
	$ordine_professionale 	= $row1['ordine_professionale'];
	$num_iscrizione 	 	= $row1['num_iscrizione'];
	$cancella = $row1['cancella'];
	
	if($ordine_professionale == "")
		 $etichetta = $nome;
	else $etichetta = $nome." - #".$num_iscrizione." | ". $ordine_professionale;
	
	$query2 = "INSERT INTO campi_combo (idcombo, etichetta, valore, peso, stato ,cancella) VALUES (195, '". str_replace("'", "''", strtoupper($etichetta))."', $id, $i, '1', '$cancella')";	
	//echo "$query2<br>"; exit();
	$result2 = mssql_query($query2, $conn);
	if(!$result2) error_message(mssql_error());	
	
	$query2 = "INSERT INTO campi_combo (idcombo, etichetta, valore, peso, stato ,cancella) VALUES (266, '". str_replace("'", "''", strtoupper($etichetta))."', $id, $i, '1', '$cancella')";	
	//echo "$query2<br>"; exit();
	$result2 = mssql_query($query2, $conn);
	if(!$result2) error_message(mssql_error());
	
	$i++;
}

echo "Inseriti $i record per combo";

