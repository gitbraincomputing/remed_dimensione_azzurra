<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
include_once('include/functions_test_clinici.php');

/*
QUESTO FILE SERVE PER I MODULI CON SCADENZA "CICLICA A PARTIRE DAL".

PREMESSA: DURANTE LA CREAZIONE DI UNA NUOVA ISTANZA DI UN MODULO PIANIFICATO CON LA SCADENZA DI CUI SOPRA,
VIENE EFFETTUATO UN UPDATE CHE NE AGGIORNA LA PRIMA DATA NON COMPILATA CON LA DATA DI CREAZIONE DELLA ISTANZA.

PROBLEMA: IN PRECEDENZA, PER UN PICCOLO PERIODI DI TEMPO, SI VERIFICAVA UN BUG CHE EFFETTUAVA DOPPI UPDATE, AGGIORNANDO
DUNQUE SIA LA DATA EFFETTIVA CHE QUELLA DELLA PROSSIMA SCADENZA, DIMEZZANDO I TEMPI PREVISTI REAZLI DI UNA PIANIFICAZIONE.

QUESTO SCRIPT TROVA TUTTI I DOPPIONI E LI RIALLINEA. 
OVVERO, DOPO IL SECONDO DOPPIONE:
	- SE NON CI SONO ALTRE COMPILAZIONI, IL RECORD DEL SECONDO DOPPIONE VIENE RIAGGOIRNATO COME "DA COMPILARE"
	- SE CI SONO ALTRE COMPILAZIONI, VIENE EFFETTUATO UNO SHIFT DI RIGA VERSO L'ALTO DAL PRIMO ALL'ULTIMO.

*/
$conn = db_connect();


// ciclo tutte le pianificazioni che hanno una scadenza "ciclica a partire dal"
$query_ricerca = "SELECT id_cartella_pianificazione_testata as icpt, id_cartella, id_paziente, id_regime, id_normativa, 
						id_modulo_padre, id_modulo_versione, dataagg, 
						COUNT(dataagg) as ripetizioni 
				  FROM cartelle_pianificazione_date_scad_cicl
				  GROUP BY id_cartella_pianificazione_testata, id_cartella, id_paziente, id_regime, id_normativa, 
								id_modulo_padre, id_modulo_versione, dataagg
				  HAVING COUNT(dataagg) > 1
				  ORDER BY id_cartella, id_modulo_padre, dataagg";
				  
$result_ricerca = mssql_query($query_ricerca, $conn);
if(!$result_ricerca) error_message(mssql_error());
$i = $u = $d = $qko = $qok = 0;

echo "<table border=1 \">
		<thead>
			<tr>
				<td>id</td>
				<td>id_pianificazione</td>
				<td>id_cartella</td>
				<td>id_paziente</td>
				<td>id_modulo_padre</td>
				<td>id_modulo_versione</td>
				<td>scadenza</td>
				<td>compilato</td>
				<td>id_inserimento_modulo</td>
				<td>data</td>
				<td>dataagg</td>
			</tr>
		</thead>
		<tbody>";
		
while($row_ricerca = mssql_fetch_assoc($result_ricerca))   
{
	echo "<tr><td colspan=11>-----</td></tr>";
	
	// per ognuna di esse, prendo la data di inizio memorizzata nella pianificazione e la prima delle date generate
	$query1 = "select *, id_cartella_pianificazione_testata as icpt from cartelle_pianificazione_date_scad_cicl 
				where 
						id_cartella_pianificazione_testata = ".$row_ricerca['icpt']." AND
						id_cartella = ".$row_ricerca['id_cartella']." and 
						id_paziente = ".$row_ricerca['id_paziente']." and 
						id_regime = ".$row_ricerca['id_regime']." and 
						id_normativa = ".$row_ricerca['id_normativa']." and 
						id_modulo_padre = ".$row_ricerca['id_modulo_padre']." and 
						id_modulo_versione = ".$row_ricerca['id_modulo_versione']."
				ORDER BY id asc";	
				//echo $query1."<br>";
	$result1 = mssql_query($query1, $conn);
	while($row1 = mssql_fetch_assoc($result1))   
	{

		$id 			   = $row1['id'];
		$id_pianificazione = $row1['icpt'];
		$id_cartella	   = $row1['id_cartella'];
		$id_paziente	   = $row1['id_paziente'];
		$id_modulo_padre   = $row1['id_modulo_padre'];
		$id_modulo_versione = $row1['id_modulo_versione'];
		$scadenza       	= $row1['scadenza'];
		$compilato	   		= $row1['compilato'];
		$id_inserimento_modulo	= $row1['id_inserimento_modulo'];
		$data				= $row1['data'];
		$dataagg				= $row1['dataagg'];
		
		echo "<tr>
				<td>$id</td>
				<td>$id_pianificazione</td>
				<td>$id_cartella</td>
				<td>$id_paziente</td>
				<td>$id_modulo_padre</td>
				<td>$id_modulo_versione</td>
				<td>$scadenza</td>
				<td>$compilato</td>
				<td>$id_inserimento_modulo</td>
				<td>$data</td>
				<td>$dataagg</td>
			</tr>";
	
	}
	
}

echo "</tbody></table>";


?>