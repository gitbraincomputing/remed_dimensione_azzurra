<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
include_once('include/functions_test_clinici.php');

/*
QUESTO FILE SERVE PER I MODULI CON SCADENZA "CICLICA A PARTIRE DAL".

NELLO SPECIFICO, QUANDO VENGONO GENERATE LE DATE, SI DEVE SPECIFICARE UNA DATA DI PARTENZA E L'INTERVALLO DI GIORNI.
QUESTA OPERAZIONE VIENE ESEGUITA NELLA PAGINA DI PIANIFICAZIONE CCD.

CLICCANDO SU "genera date" VIENE APERTO UN POPUP IN CUI VIENE PASSATA LA DATA INZIALE E GENERATE POI TUTTE LE SUCCESSIVE.

IN CASO DI CANCELLAZIONE DI UNA PRIMA DATA, LA DATA INIZIALE VIENE SOSTITUITA DA QUELLA IMMEDIATAMENTE SUCCESSIVA E PASSATA ALLA PAGINA DI PIANIFICAZIONE.
SI SONO VERIFICATO CASI IN CUI (forse per colpa di un browser diverso da Firefox) LA NUOVA DATA INZIALE NON VENIVA PASSATA, DANDO COSì VITA AD UNA DISCREPANZA DI DATE.

QUESTO FILE PESCA TUTTE LE PIANIFICAZIONI LA CUI DATA DI INIZIO E' DIVERSA DALLA PRIMA PIANIFICATA E LA AGGIORNA.
*/
$conn = db_connect();


// ciclo tutte le pianificazioni che hanno una scadenza "ciclica a partire dal"
$query_ricerca = "SELECT id_cartella_pianificazione_testata as id_pt, id_cartella, id_modulo_padre, id_paziente
					FROM cartelle_pianificazione_date_scad_cicl
					GROUP BY id_cartella_pianificazione_testata, id_cartella, id_modulo_padre, id_paziente
					ORDER BY id_cartella, id_modulo_padre asc";
$result_ricerca = mssql_query($query_ricerca, $conn);
if(!$result_ricerca) error_message(mssql_error());
$i = $u = $d = $qko = $qok = 0;
while($row_ricerca = mssql_fetch_assoc($result_ricerca))   
{
	++$i;
	echo "ID_PIANIF. TEST.: " . $row_ricerca['id_pt'] ." - id_modulo_padre = " . $row_ricerca['id_modulo_padre'] . " - id_paziente = " . $row_ricerca['id_paziente'];
	
	// per ognuna di esse, prendo la data di inizio memorizzata nella pianificazione e la prima delle date generate
	$query1 = "SELECT TOP(1) cpd.data, cp.id_pianificazione, cp.data_iniz_scad_cicl
					from cartelle_pianificazione AS cp
					JOIN cartelle_pianificazione_date_scad_cicl AS cpd 
						ON cp.id_cartella_pianificazione_testata = cpd.id_cartella_pianificazione_testata 
							and cp.id_modulo_padre = cpd.id_modulo_padre
					where 
							cp.id_modulo_padre = " . $row_ricerca['id_modulo_padre'] . "
						and cpd.id_paziente = " . $row_ricerca['id_paziente'] . "
						and cpd.id_cartella_pianificazione_testata = " .$row_ricerca['id_pt'] . "
					order by cp.id_modulo_padre asc, cpd.data asc";	
	$result1 = mssql_query($query1, $conn);

	if(!$result1) error_message(mssql_error());
	$row1 = mssql_fetch_assoc($result1);
	
	$id_pianificazione = $row1['id_pianificazione'];
	$data 			   = $row1['data'];
	$data_iniz_scad_cicl = $row1['data_iniz_scad_cicl'];
	
	
	if($data == $data_iniz_scad_cicl) 
	{
		++$u;
		echo " - DATA UGUALE - ";
		
	} else {
		
		++$d;
		echo " - DATA DIVERSA - ";
		
		$query2 = "update cartelle_pianificazione
						SET data_iniz_scad_cicl = 	CONVERT(DATETIME, '".$data." 00:00:00', 102)
						WHERE id_pianificazione = $id_pianificazione";	

		$result2 = mssql_query($query2, $conn);
		if(!$result2)  {
			++$qko;
			error_message(mssql_error());
			echo " ----- ERRORE: ";
		} else {
			++$qok;
			echo " ----- FATTO: ";
		}
	}
	
		
	echo " data_iniz_scad_cicl = $data_iniz_scad_cicl & data = $data<br>";
}

echo "<br>Trovati $i elementi, di cui $u uguali e $d diversi<br>$qko query fallite, $qok query eseguite.";

?>