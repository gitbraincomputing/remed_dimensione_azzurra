<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
include_once('include/functions_test_clinici.php');
$id_permesso = $id_menu = 2;
session_start();

/*
questo file è servito per eliminare record doppioni durante l'iniziale fase di test della memorizzazione delle date generate se, in fase di pianificazione della cartlla, ad 1 o più moduli
venisse assegnata la tipologia di scadenza "ciclica a partire dal".
NON LANCIARE MAI - E' QUI SOLO PER I POSTERI
*/


$conn = db_connect();
$query1 = "SELECT MAX(p.id) as max_id, p.id_modulo_padre, p.id_paziente, p.id_cartella, COUNT(p.scadenza) as ripetizioni FROM cartelle_pianificazione_date_scad_cicl as p
			INNER JOIN (SELECT * FROM cartelle_pianificazione_date_scad_cicl
			where scadenza = 0) as c ON p.id = c.id
			GROUP BY p.id_modulo_padre, p.id_paziente, p.id_cartella
			HAVING (COUNT(p.scadenza) > 1)";	
$result1 = mssql_query($query1, $conn);
if(!$result1) error_message(mssql_error());

while($row1 = mssql_fetch_assoc($result1))   
{
	$max_id 		 = $row1['max_id'];
	$id_modulo_padre = $row1['id_modulo_padre'];
	$id_paziente 	 = $row1['id_paziente'];
	$id_cartella 	 = $row1['id_cartella'];
	
	$query2 = "DELETE FROM cartelle_pianificazione_date_scad_cicl WHERE id < $max_id AND id_modulo_padre = $id_modulo_padre AND id_paziente = $id_paziente AND id_cartella = $id_cartella";	
	//echo "$query2<br>"; exit();
	$result2 = mssql_query($query2, $conn);
	if(!$result2) error_message(mssql_error());
	else echo "id_modulo_padre = $id_modulo_padre AND id_paziente = $id_paziente AND id_cartella = $id_cartella<br>";
	
}

