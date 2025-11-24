<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
include_once('include/functions_test_clinici.php');
$id_permesso = $id_menu = 2;
session_start();




$conn = db_connect();
$query1 = "SELECT        id, id_cartella, id_paziente, id_modulo_padre, scadenza, data, compilato, id_inserimento_modulo, dataagg, opeagg, ipagg
			FROM            cartelle_pianificazione_date_scad_cicl
			WHERE        (data > DATEADD(day, 30, { fn NOW() })) AND (scadenza > 0) AND compilato = 1
			ORDER BY id_cartella, id_modulo_padre, data";	
$result1 = mssql_query($query1, $conn);
echo "$query1<br><br>";
if(!$result1) error_message(mssql_error());

while($row1 = mssql_fetch_assoc($result1))   
{
	$id  			 = $row1['id'];
	$id_modulo_padre = $row1['id_modulo_padre'];
	$id_paziente 	 = $row1['id_paziente'];
	$id_cartella 	 = $row1['id_cartella'];
	$scadenza 	 	 = $row1['scadenza'];
	$data 	 		 = $row1['data'];
	$compilato 	 	 = $row1['compilato'];
	$id_inserimento_modulo = $row1['id_inserimento_modulo'];
	$dataagg 	 	 = $row1['dataagg'];
	$opeagg 	 	 = $row1['opeagg'];
	$ipagg 	 	 	 = $row1['ipagg'];

	
	
	$query2 = "SELECT * 
				FROM            cartelle_pianificazione_date_scad_cicl
				WHERE        id_modulo_padre = $id_modulo_padre AND id_paziente = $id_paziente AND id_cartella = $id_cartella
				ORDER BY data ASC";	
	echo "$query2<br>"; 
	//$result2 = mssql_query($query2, $conn);
	

	//if(!$result2) error_message(mssql_error());
	
	/* if (mssql_num_rows($result2) == 1)
	{
		$row2 = mssql_fetch_assoc($result2);
		
		$id2 			 = $row2['id'];
		$id_modulo_padre2 = $row2['id_modulo_padre'];
		$id_paziente2 	 = $row2['id_paziente'];
		$id_cartella2 	 = $row2['id_cartella'];
		$scadenza2 	 	 = $row2['scadenza'];
		$data2 	 		 = $row2['data'];
		$compilato2 	 = $row2['compilato'];
		
		// compilato DA 1 a  = 2 = 0
		$query3 = "UPDATE cartelle_pianificazione_date_scad_cicl SET compilato = 2, id_inserimento_modulo = NULL WHERE id = $id";	
		echo "3) $query3<br>";
		$result3 = mssql_query($query3, $conn);
		
		// compilato da 0 = 1 = 3
		$query4 = "UPDATE cartelle_pianificazione_date_scad_cicl SET compilato = 3, opeagg = '$opeagg', ipagg = '$ipagg' WHERE id = $id2";
		echo "4) $query4<br>-----------------------<br>";
  		$result4 = mssql_query($query4, $conn);

		

	} else echo('zero righe' . $id . "<br>"); */
	
}

