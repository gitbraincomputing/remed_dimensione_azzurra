<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
//include_once('include/function_page.php');

/*
QUESTO FILE VIENE LANCIATO IN MANIERA SCHEDULATA TRAMITE WINDOWS.

EFFETTUA LA VERIFICA DEI FILE RILASCIATI DAI MACCHINARI TYROMOTION E NE ALLEGA I FILE
AI MODULO PIANIFICATI IN OGNI CC DEI REGIMI "Residenziale" E "Trattamento privato".

I MACCHINARI SONO 4 E HANNO I SEGUENTI NOMI: Diego, Tymo, Lexo e Pablo
PER OGUNO DI ESSI, E' PRESENTE UN MODULO CON IL SUO NOME NELLE CC DI CUI SOPRA E UNA CARTELLA DI REPOSITORY FILE.
SOLO I MACCHINARI Tymo E Pablo SCRIVONO NELLA STESSA CARTELLA Tymo (per info apri file in "C:\web\tyromotion\Pablo - leggimi.txt")

## ULTIMO AGGIORNAMENTO - AME 20/12/2022 ##
*/


echo "start file...<br>";
$conn = db_connect();

// cerco tutti i file nella DIR
$array_dir[0] = "../tyromotion/Diego";
$array_dir[1] = "../tyromotion/Tymo";		// modulo "Tymo|Pablo"
$array_dir[2] = "../tyromotion/Lexo";	
$array_dir[3] = "../tyromotion/Amadeo";

$dir_salvataggio = "C:/web/republic/modelli_word/allegato";

$conta = 0;

foreach ($array_dir as $index_dir=>$dir)	// cicla tutte le directory dei macchinari
{
	$dir_out = "$dir/out";
	echo "cerco file txt nella dir $dir_out - file trovato finora = $conta<br>";
		
	$codice_fiscale = "";
	
	// ciclo tutti i file .txt all'interno delle singole dir
	foreach (glob("$dir_out/*.txt") as $index_file=>$filename)	
	{
		++$conta;
		echo "leggo il file $filename<br>";
		
		// estraggo le informazioni dal nome del file
		$nome_file_txt = 	 substr($filename, strrpos($filename, '/') + 1);				// Ad es.: "922_20221006_164048.txt"
		$id_file	 = 		 substr($nome_file_txt, 0, strpos($nome_file_txt, '_'));		// 		    922
		$data_e_ora_file =   substr($nome_file_txt, strpos($nome_file_txt, '_') + 1, 13);	// 			    20221006_1640 (senza i secondi)
		$data_file = 		 substr($data_e_ora_file, 0, 8);								// 			    20221006
		$ora_file = 		 substr($data_e_ora_file, 9, 4);								// 			   			 1640
		$estensione_file = substr(strrchr($nome_file_txt, "."), 1);     					// 								txt (senza punto)
		$data_file_formattata = date('Y-m-d', strtotime($data_file));						//				2022-10-06
		$ora_file_formattata = date('H:i', strtotime($ora_file));							//						 16:40
		$data_ora_file_formattata = $data_file_formattata . " " . $ora_file_formattata . ":00";
		


		// cerco il relativo file pdf con lo stesso id_file e data_file 
		// (non considero l'ora_file poichè ci sarà circa 1-2 minuti di scarto tra txt e pdf)
		
		$file_pdf_trovato = glob("$dir_out/*".$id_file."_".$data_file."*.pdf");
		if(count($file_pdf_trovato) == 0) 
		{
			write_log($conn, "Individuazione file PDF", $dir_out, $nome_file_txt, "NULL", "NULL", "NULL", $data_ora_file_formattata, "KO", "Rispettivo file PDF non trovato");	
			continue;
		}
		elseif(count($file_pdf_trovato) > 0) 
		{
			$nome_file_pdf = substr($file_pdf_trovato[0], strrpos($file_pdf_trovato[0], '/') + 1);
		
	
			// apro il file .txt trovato
			$file = fopen($filename,'r');       
			if ($file)
			{
				while (($line = fgets($file)) !== false) {						// leggo riga per riga
					if(strpos($line, 'Insurance number:') !== false)			// finche' non trovo il codice fiscale
					{
						$codice_fiscale = substr($line, 18);					// estraggo il codice fiscale dalla riga
						$codice_fiscale = trim($codice_fiscale);
						if($codice_fiscale == "") {
							write_log($conn, "Lettura file txt", $dir_out, $nome_file_txt, "NULL", "NULL", "NULL", $data_ora_file_formattata, "KO", "Il file riporta un Codice Fiscale vuoto al suo interno.");	
						}
					}
				}
			}
			fclose($file);


			// estraggo il nome del macchinario in base alla DIR che sto ciclando
			$nome_macchinario = substr($dir, strrpos($dir, '/') + 1);

			
			// STEP 1 - prendo le info del modulo a cui associare il file PDF
			$query1 = "SELECT id, idmodulo FROM moduli WHERE nome LIKE 'Reportistica TyroMotion $nome_macchinario%'";
			$result1 = mssql_query($query1, $conn);
			echo "<br>ESEGUO QUERY 1:<br>$codice_fiscale $filename - $query1";
			if(!$result1) { 
				error_message(mssql_error()); 
				write_log($conn, "ERRORE SQL query1: Ricerca ID modulo", $dir_out, $nome_file_txt, "NULL", $codice_fiscale, "NULL", $data_ora_file_formattata, "KO", mssql_error());
			}
			else 
			{
				if (mssql_num_rows($result1) == 0 ) {
					write_log($conn, "Ricerca ID modulo", $dir_out, $nome_file_txt, "NULL", $codice_fiscale, "NULL", $data_ora_file_formattata, "KO", "ID Modulo con nome Reportistica TyroMotion $nome_macchinario non trovato.");
					continue;
				}
				$row1 = mssql_fetch_assoc($result1);
				$id_modulo_padre 	= $row1['idmodulo'];
				$id_modulo_versione = $row1['id'];
				
				
				// STEP 2 - prendo l'ID della Cartella Clinica che contiene il modulo interessato del paziente che sto leggendo
				/*
				$query2 = "SELECT MAX(cpt.id_pianificazione_testata) AS id_pianificazione_testata, uc.id AS id_cartella, uc.idutente AS id_paziente 
								FROM utenti_cartelle AS uc
								JOIN cartelle_pianificazione_testata AS cpt ON cpt.id_cartella = uc.id
								JOIN utenti AS u ON u.IdUtente = uc.idutente
								WHERE uc.cancella = 'n' 
									AND uc.data_chiusura IS NULL 
									AND u.CodiceFiscale = '$codice_fiscale'
									AND uc.idregime NOT IN (52,53,59,60,61,62)
								GROUP BY uc.id, uc.idutente";
				*/
				$query2 = "SELECT MAX(cpt.id_pianificazione_testata) AS id_pianificazione_testata, uc.id AS id_cartella, uc.idutente AS id_paziente 
							FROM utenti_cartelle AS uc
							JOIN cartelle_pianificazione_testata AS cpt ON cpt.id_cartella = uc.id
							--JOIN re_pazienti_impegnative re_pi ON re_pi.IdUtente = uc.idutente AND re_pi.idregime = uc.idregime
							JOIN utenti AS u ON u.IdUtente = uc.idutente
							WHERE uc.cancella = 'n' 
								AND uc.data_chiusura IS NULL 
								AND u.CodiceFiscale = '$codice_fiscale'
								AND uc.idregime NOT IN (52,53,59,60,61,62,64,65)
							GROUP BY uc.id, uc.idutente";
				
				$result2 = mssql_query($query2, $conn);
				echo "<br>ESEGUO QUERY 2:<br>$query2<br>";
				if(!$result2) { 
					error_message(mssql_error()); 
					write_log($conn, "ERRORE SQL query2: Ricerca ID Cartella Clinica", $dir_out, $nome_file_txt, "NULL", $codice_fiscale, "NULL", $data_ora_file_formattata, "KO", mssql_error());
				}
				else 
				{		
					if (mssql_num_rows($result2) == 0 ) {
						echo " CC non trovata<br>";
						write_log($conn, "Ricerca ID Cartella Clinica", $dir_out, $nome_file_txt, "NULL", $codice_fiscale, "NULL", $data_ora_file_formattata, "KO", "ID Cartella Clinica non trovata (forse il paziente non ha una CC nei regimi previsti?)");
						continue;
					}
					$row2 = mssql_fetch_assoc($result2);
					//$id_pianif_test = $row2['id_pianificazione_testata'];
					$id_cartella = $row2['id_cartella'];
					$id_paziente = $row2['id_paziente'];
					
					$ipins = $_SERVER['REMOTE_ADDR'];
					
					// STEP 3 - cancello l'eventuale istanza creata precedentemente in favore della nuova
					$query3 = "DELETE FROM istanze_testata WHERE id_cartella = $id_cartella AND id_modulo_padre = $id_modulo_padre AND id_modulo_versione = $id_modulo_versione";
					$result3 = mssql_query($query3, $conn);
					if(!$result3) { 
						error_message(mssql_error()); 
						write_log($conn, "ERRORE SQL query3: Cancellazione istanza prededente", $dir_out, $nome_file_txt, $id_paziente, $id_cartella, $data_ora_file_formattata, "KO", mssql_error());
					}
					else {
						echo "<br>ESEGUO QUERY 3:<br>$query3";
					}
					
					
					// STEP 4 - creo una nuova istanza del modulo con il PDF allegato e le note per info
					$query4 = "INSERT INTO istanze_testata
									(id_cartella,id_modulo_padre,id_modulo_versione,id_inserimento,id_impegnativa,id_paziente,
									data_osservazione,updcount,datains,orains,opeins,ipins,
									note,
									allegato,allegato2,data_allegato)
								VALUES
									($id_cartella, $id_modulo_padre, $id_modulo_versione, 1, NULL, $id_paziente,
									getdate(),0,'$data_file_formattata','$ora_file_formattata',258,'$ipins',
									'Documento generato dal macchinario TyroMotion $nome_macchinario ed associato automaticamente da ReMed.',
									'".$id_paziente."_Reportistica_macchinario_".$nome_macchinario.".pdf','".$id_paziente."_Reportistica_macchinario_".$nome_macchinario.".txt','$data_file_formattata')";
					$result4 = mssql_query($query4, $conn);
					if(!$result4) { 
						error_message(mssql_error()); 
						write_log($conn, "ERRORE SQL query3: creazione istanza con allegato", $dir_out, $nome_file_txt, $id_paziente, $codice_fiscale, $id_cartella, $data_ora_file_formattata, "KO", mssql_error());
					}
					else {
						echo "<br>ESEGUO QUERY 4:<br>$query4<br>";
						
						
						//STEP 5 - cancello i vecchi allegati della vecchia istanza e sposto i nuovi
						if(file_exists("$dir_salvataggio/".$id_paziente."_Reportistica_macchinario_".$nome_macchinario.".txt"))		unlink("$dir_salvataggio/".$id_paziente."_Reportistica_macchinario_".$nome_macchinario.".txt");
						if(file_exists("$dir_salvataggio/".$id_paziente."_Reportistica_macchinario_".$nome_macchinario.".pdf"))		unlink("$dir_salvataggio/".$id_paziente."_Reportistica_macchinario_".$nome_macchinario.".pdf");
						
						if(!rename("$dir_out/$nome_file_txt", "$dir_salvataggio/".$id_paziente."_Reportistica_macchinario_".$nome_macchinario.".txt"))		write_log($conn, "Spostamento file txt in cartella ReMed", $dir_out, $nome_file_txt, $id_paziente, $codice_fiscale, $id_cartella, $data_ora_file_formattata, "KO", "File txt non trovato.");
						if(!rename("$dir_out/$nome_file_pdf", "$dir_salvataggio/".$id_paziente."_Reportistica_macchinario_".$nome_macchinario.".pdf"))		write_log($conn, "Spostamento file pdf in cartella ReMed", $dir_out, $nome_file_pdf, $id_paziente, $codice_fiscale, $id_cartella, $data_ora_file_formattata, "KO", "File pdf non trovato.");
						
						write_log($conn, "Creazione istanza con allegato", $dir_out, $nome_file_txt, $id_paziente, $codice_fiscale, $id_cartella, $data_ora_file_formattata, "OK", "Creata istanza per modulo Reportistica TyroMotion $nome_macchinario.");
					}
					
				}
			}
			
		} // fine ricerca file PDF

	} // fine foreach

} // fine funzione
		
if($conta == 0) echo "<br><br>Nessun file .txt trovato";



// funzione di scrittura log in tbl
function write_log($conn, $azione, $dir, $nome_file_txt, $id_paziente, $codice_fiscale, $id_cartella, $data_ora_file, $esito, $dettagli) 
{

	$query_log = "INSERT INTO tyromotion_file_log (azione, dir, nome_file, id_paziente, cod_fisc, id_cartella, data_ora_file, esito, dettagli, timestamp_exec)
										VALUES ('$azione', '$dir', '$nome_file_txt', $id_paziente, ";
	if($codice_fiscale == "NULL")	
		 $query_log .= "NULL, ";
	else $query_log .= "'$codice_fiscale', ";
										
	$query_log .= "$id_cartella, '$data_ora_file', '$esito', '$dettagli', getdate() )";
	
	//die($query_log);
	$result_query_log = mssql_query($query_log, $conn);
	if(!$result_query_log) { 
		error_message(mssql_error());
	}
}

?>