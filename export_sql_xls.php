<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');

$conn = db_connect();

if(isset($_GET['rep']))
{
	// Load the database configuration file 
	//include_once 'dbConfig.php'; 

	// Filter the excel data 
	function filterData(&$str){ 
		$str = preg_replace("/\t/", "\\t", $str); 
		$str = preg_replace("/\r?\n/", "\\n", $str); 
		if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
	} 
	 
	// Excel file name for download 
	$fileName = $_GET['rep'] . "_" . date('Y-m-d') . ".xls"; 
	$where = str_replace('\\', '', $_GET['where']);
	 
	switch($_GET['rep']) {
		case 'rep_moduli_scaduti':
			$intestazione = array('normativa', 'regime', 'nome_operatore', 'codice_cartella', 'cognome_paziente', 'nome_paziente', 'data_scadenza'); 
			$excelData = implode("\t", array_values($intestazione)) . "\n"; 	// Display column names as first row 

			$query="SELECT * FROM re_report_moduli_scaduti_pianificati " .$where . " ORDER BY data_scadenza ASC";		// Fetch records from database 
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs) > 0){
 				while($row = mssql_fetch_assoc($rs)) 
				{
					$lineData = array($row['normativa'], $row['regime'], $row['nome_operatore'], $row['codice_cartella']."/".$row['versione'], $row['cognome_paziente'], $row['nome_paziente'], $row['data_scadenza']); 
					//array_walk($lineData, 'filterData'); 
					$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				}
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
		
		case 'rep_allegati_non_caric':
			$intestazione = array('normativa', 'regime', 'nome_operatore', 'codice_cartella', 'cognome_paziente', 'nome_paziente', 'data_scadenza'); 
			$excelData = implode("\t", array_values($intestazione)) . "\n"; 	// Display column names as first row 

			$query="SELECT * FROM re_report_moduli_scaduti_pianificati " .$where . " ORDER BY data_scadenza ASC";		// Fetch records from database 
			$rs = mssql_query($query, $conn);
			if(mssql_num_rows($rs) > 0){
 				while($row = mssql_fetch_assoc($rs)) 
				{
					$lineData = array($row['normativa'], $row['regime'], $row['nome_operatore'], $row['codice_cartella']."/".$row['versione'], $row['cognome_paziente'], $row['nome_paziente'], $row['data_scadenza']); 
					//array_walk($lineData, 'filterData'); 
					$excelData .= implode("\t", array_values($lineData)) . "\n"; 
				}
			} else $excelData .= 'Nessun record trovato.'. "\n"; 
		break;
	}

	// Headers for download 
	header("Content-Type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=\"$fileName\""); 
	 
	// Render excel data 
	echo $excelData; 
} 
else echo "Nessun report specificato per l'esportazione.";