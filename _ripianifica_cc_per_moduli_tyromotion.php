<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');

/*
QUESTO FILE E' SERVITO PER EFFETTUARE UNA PIANIFICAZIONE DI TUTTE LE CC DEI PAZIENTI IN TRATTAMENTO IN MODO DA AGGIUNGERE I MODULI DEI TYROMOTION 
NON LANCIARE MAI - E' QUI SOLO PER I POSTERI
*/

$conn = db_connect();
/*
$query1 = "SELECT MAX(cpt.id_pianificazione_testata) AS id_pianificazione_testata 
			FROM utenti_cartelle AS uc
			JOIN cartelle_pianificazione_testata AS cpt ON cpt.id_cartella = uc.id
			WHERE uc.cancella = 'n' AND uc.data_chiusura IS NULL AND uc.idregime NOT IN (52,53,59,60,61,62)
			GROUP BY uc.id";	
*/
$query1 = "SELECT MAX(cpt.id_pianificazione_testata) AS id_pianificazione_testata 
			FROM utenti_cartelle AS uc
			--JOIN re_pazienti_impegnative re_pi ON re_pi.IdUtente = uc.idutente AND re_pi.idregime = uc.idregime
			JOIN cartelle_pianificazione_testata AS cpt ON cpt.id_cartella = uc.id
			WHERE uc.cancella = 'n' AND uc.data_chiusura IS NULL 
			--AND re_pi.DataDimissione is not null
					AND uc.idregime NOT IN (52,53,59,60,61,62,64,65)
			GROUP BY uc.id";

$result1 = mssql_query($query1, $conn);
if(!$result1) error_message(mssql_error());

$i = 0;
while($row1 = mssql_fetch_assoc($result1))   
{
	$contato = 0;
	$id_pianif_test = $row1['id_pianificazione_testata'];
	
	
 	$query2controllo = "SELECT * FROM cartelle_pianificazione WHERE id_cartella_pianificazione_testata = $id_pianif_test AND id_modulo_padre = 214";	
	$result2controllo = mssql_query($query2controllo, $conn);
	if(!$result2controllo) error_message(mssql_error());
	if (mssql_num_rows($result2controllo) == 0 )
	{
 		$query2 = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,invio_fse,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)
					VALUES($id_pianif_test,214,5843,'p', 	'n','','',1156,1,NULL,NULL,NULL)";	
		$result2 = mssql_query($query2, $conn);
		if(!$result2) error_message(mssql_error());
		else {
			echo "Modulo Reportistica TyroMotion Diego per id_cartella_pianificazione_testata: $id_pianif_test<br>";
			$contato = 1;
			$i++;
		}
	}
	
	
/* 	$query3controllo = "SELECT * FROM cartelle_pianificazione WHERE id_cartella_pianificazione_testata = $id_pianif_test AND id_modulo_padre = 215";	
	$result3controllo = mssql_query($query3controllo, $conn);
	if(!$result3controllo) error_message(mssql_error());
	if (mssql_num_rows($result3controllo) == 0 )
	{
 		$query2 = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,invio_fse,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)
					VALUES($id_pianif_test,215,5844,'p', 	'n','','',1156,1,NULL,NULL,NULL)";	
		$result2 = mssql_query($query2, $conn);
		if(!$result2) error_message(mssql_error());
		else {
			echo "Modulo Reportistica TyroMotion Pablo per id_cartella_pianificazione_testata: $id_pianif_test<br>";
			$contato = 1;
			$i++;
		}
	}	 */
	
	
	$query3controllo = "SELECT * FROM cartelle_pianificazione WHERE id_cartella_pianificazione_testata = $id_pianif_test AND id_modulo_padre = 216";	
	$result3controllo = mssql_query($query3controllo, $conn);
	if(!$result3controllo) error_message(mssql_error());
	if (mssql_num_rows($result3controllo) == 0 )
	{
 		$query2 = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,invio_fse,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)
					VALUES($id_pianif_test,216,5845,'p', 	'n','','',1156,1,NULL,NULL,NULL)";	
		$result2 = mssql_query($query2, $conn);
		if(!$result2) error_message(mssql_error());
		else {
			echo "Modulo Reportistica TyroMotion Tymo|Pablo per id_cartella_pianificazione_testata: $id_pianif_test<br>";
			$contato = 1;
			$i++;
		}
	}	
	
	
	$query4controllo = "SELECT * FROM cartelle_pianificazione WHERE id_cartella_pianificazione_testata = $id_pianif_test AND id_modulo_padre = 217";	
	$result4controllo = mssql_query($query4controllo, $conn);
	if(!$result4controllo) error_message(mssql_error());
	if (mssql_num_rows($result4controllo) == 0 )
	{
 		$query2 = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,invio_fse,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)
					VALUES($id_pianif_test,217,5846,'p', 	'n','','',1156,1,NULL,NULL,NULL)";	
		$result2 = mssql_query($query2, $conn);
		if(!$result2) error_message(mssql_error());
		else {
			echo "Modulo Reportistica TyroMotion Lexo per id_cartella_pianificazione_testata: $id_pianif_test<br>";
			$contato = 1;
			$i++;
		}
	}
	
	
	$query5controllo = "SELECT * FROM cartelle_pianificazione WHERE id_cartella_pianificazione_testata = $id_pianif_test AND id_modulo_padre = 255";	
	$result5controllo = mssql_query($query5controllo, $conn);
	if(!$result5controllo) error_message(mssql_error());
	if (mssql_num_rows($result5controllo) == 0 )
	{
 		$query2 = "INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_modulo_padre,id_modulo_versione,obbligatorio,invio_fse,trattamenti,scadenza,id_operatore,replica,id_impegnativa,data_fissa,data_iniz_scad_cicl)
					VALUES($id_pianif_test,255,6147,'p', 	'n','','',1156,1,NULL,NULL,NULL)";	
		$result2 = mssql_query($query2, $conn);
		if(!$result2) error_message(mssql_error());
		else {
			echo "Modulo Reportistica TyroMotion Amadeo per id_cartella_pianificazione_testata: $id_pianif_test<br>";
			$contato = 1;
			$i++;
		}
	}
	
	
	echo "------------------------<br>";
	
}
echo "operazione completata - $i righe lette";
	
	