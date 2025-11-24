<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');

/*
QUESTO FILE E' SERVITO PER EFFETTUARE UNA PIANIFICAZIONE DI TUTTE LE CC DEI PAZIENTI IN TRATTAMENTO IN MODO DA AGGIUNGERE I 2 MODULI DEL FSE 
NON LANCIARE MAI - E' QUI SOLO PER I POSTERI
*/

$conn = db_connect();
$query1 = "SELECT MAX(cpt.id_pianificazione_testata) AS id_pianificazione_testata FROM utenti_cartelle AS uc
			JOIN cartelle_pianificazione_testata AS cpt ON cpt.id_cartella = uc.id
			WHERE uc.cancella = 'n' AND uc.data_chiusura IS NULL
			GROUP BY uc.id";	
$result1 = mssql_query($query1, $conn);
if(!$result1) error_message(mssql_error());

$i = 0;
while($row1 = mssql_fetch_assoc($result1))   
{
	$contato = 0;
	$id_pianif_test = $row1['id_pianificazione_testata'];
	
	$query2controllo = "SELECT * FROM cartelle_pianificazione WHERE id_cartella_pianificazione_testata = $id_pianif_test AND id_modulo_padre = 212";	
	$result2controllo = mssql_query($query2controllo, $conn);
	if(!$result2controllo) error_message(mssql_error());
	if (mssql_num_rows($result2controllo) == 0 )
	{
/* 		$query2 = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,invio_fse,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)
					VALUES($id_pianif_test,212,5828,'o','n','','',1156,1,NULL,NULL,NULL)";	
		$result2 = mssql_query($query2, $conn);
		if(!$result2) error_message(mssql_error());
		else*/
			echo "Modulo informativa inserito per id_cartella_pianificazione_testata: $id_pianif_test<br>";
			$contato = 1;
			$i++;
	}
	
	
	$query3controllo = "SELECT * FROM cartelle_pianificazione WHERE id_cartella_pianificazione_testata = $id_pianif_test AND id_modulo_padre = 213";	
	$result3controllo = mssql_query($query3controllo, $conn);
	if(!$result3controllo) error_message(mssql_error());
	if (mssql_num_rows($result3controllo) == 0 )
	{	

/*		$query3 = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,invio_fse,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)
					VALUES($id_pianif_test,213,5831,'o','n','','',1156,1,NULL,NULL,NULL)";	
		$result3 = mssql_query($query3, $conn);
		if(!$result3) error_message(mssql_error());	
		else */
			echo "Modulo oscuramento inserito per id_cartella_pianificazione_testata: $id_pianif_test<br>";
			if($contato == 0)
				$i++;
	}
	//else echo "------------------------<br>";
	
}
echo "operazione completata - $i righe lette";
	
	