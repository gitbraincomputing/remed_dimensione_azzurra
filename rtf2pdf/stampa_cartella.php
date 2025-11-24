<?php

/*ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);*/

$test = false;
$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));
// in produzione carichiamo le configurazioni dal file config.rdm.php generale
require_once($productionConfig);



/* // inclusione delle classi
include_once('include/class.User.php');
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/function_page.php');
include_once('include/dbengine.inc.php'); */

// classi pdf necessarie
require_once('pdf_merger/PDFMerger.php');
//require_once('../include/function_page.php');


if (isset($_REQUEST['idcartella']) && isset($_REQUEST['idpaziente']) && isset($_REQUEST['usephp']) && $_REQUEST['usephp']=='7' ) 
{
	$id_cartella = $_REQUEST['idcartella'];
	$id_paziente = $_REQUEST['idpaziente'];
	$pdf = new PDFMerger;
	
	$serverName = SERVER_NAME;//'192.168.10.220'; 
	$database=DB_NAME;
	$dbuser=DB_USER;
	$dbpw=DB_PASSWORD;

	/* Connect using Windows Authentication. */  
	try  
	{  
	  $conn = new PDO( "sqlsrv:server=$serverName ; Database=$database", $dbuser, $dbpw);  
	  $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		
	}  
	catch(Exception $e)  
	{   
	  die( print_r( "ERR: " . $e->getMessage() ) );   
	}
	
	$sql_problem_allegato1 = "SELECT nome_modulo, [progressivo istanza] as progressivo_istanza
							FROM re_visualizza_info_duplicati_allegato1
							WHERE id_cartella = $id_cartella";
	$r_problem_allegato1 = $conn->query($sql_problem_allegato1)->fetch();
	
	if(!empty($r_problem_allegato1))
		die("La cartella riscontra anomalie nell'allegato 1 del modulo: <b>" . $r_problem_allegato1['nome_modulo'] . ' | Progressivo: ' . $r_problem_allegato1['progressivo_istanza']) .'</b>';
	
	$sql_problem_allegato2 = "SELECT nome_modulo, [progressivo istanza] as progressivo_istanza
							FROM re_visualizza_info_duplicati_allegato2
							WHERE id_cartella = $id_cartella";
	$r_problem_allegato2 = $conn->query($sql_problem_allegato2)->fetch();
	
	if(!empty($r_problem_allegato2))
		die("La cartella riscontra anomalie nell'allegato 2 del modulo: <b>" . $r_problem_allegato2['nome_modulo'] . ' | Progressivo: ' . $r_problem_allegato2['progressivo_istanza']) .'</b>';
	
	$arr_moduli_letti = [];		// contiene l'elenco dei moduli letti per la compilazione delle tabelle dinamiche.. in caso di più istanze di uno stesso modo, questo array mi servirà come controllo per entrare nel ciclo solo la prima volta 
	
	$tmp_relative_path = "../modelli_word/stampe_ccd_temp";
	if (!is_dir($tmp_relative_path)) 
		mkdir($tmp_relative_path);

	$query_sfoglia_ccd = "SELECT u.cognome, u.nome, u.DataNascita, uc.codice_cartella, uc.versione, it.id_modulo_padre, it.allegato, it.allegato2, it.allegato3, it.allegato4, it.id_inserimento, r.regime, uc.idregime, n.normativa FROM istanze_testata AS it 
				INNER JOIN utenti_cartelle AS uc ON uc.id = it.id_cartella 
				INNER JOIN re_moduli_regimi AS mr ON mr.idmodulo = it.id_modulo_padre AND mr.idregime = uc.idregime
				INNER JOIN utenti AS u ON u.IdUtente = $id_paziente
				INNER JOIN regime AS r ON  r.idregime = uc.idregime
				INNER JOIN normativa AS n ON n.idnormativa = uc.idnormativa
				WHERE (it.id_cartella = $id_cartella AND annullamento='n'
						AND (allegato IS NOT NULL OR allegato2 IS NOT NULL OR allegato3 IS NOT NULL OR allegato4 IS NOT NULL))
				   OR (it.id_cartella = $id_cartella AND it.id_modulo_padre = 166)
				   OR (it.id_cartella = $id_cartella AND it.id_modulo_padre = 210)
				ORDER BY mr.id, it.id_inserimento";
																		// id_modulo_padre = 166 -> PROGETTO SOCIO SANITARIO (legge 8)
																		// id_modulo_padre = 210 -> DOCUMENTAZIONE ALLEGATA (Res. Est.)
																		// Questi 2 moduli hanno gli allegati caricati internamente, non sull'istanza.
																		// Li includo nel WHERE perchè con la sola condizione degli allegati non uscirebbero
//echo "$query_sfoglia_ccd<br><br>";
	$frontespizio = 0;

	$result_sfoglia_ccd = $conn->query($query_sfoglia_ccd)->fetchAll();

	if (count($result_sfoglia_ccd) == 0) 	die("<html><head><title>Cartella Clinica vuota!</title></head><body><p>La Cartella Clinica selezionata non dispone di alcun allegato.<br><br>Non c'è nulla da stampare.</p></body></html>");
	else {	
		foreach ($result_sfoglia_ccd as $row_sfoglia_ccd) 
		{
		 	// creo il frontespizio al primo giro di each
			if($frontespizio == 0) {
				$relative_path = "../allegati_sgq/modelli";
				$AnnoInizioTrattamento = $DataInizioTrattamento = $DataFineTrattamento = "";
				
				// FRONTESPIZIO IN CASO DI CARTELLA INFERMIERISTICA
				if(strtolower($row_sfoglia_ccd['regime']) == "infermieristico") {
					$filename = "Cartella_clinica_-_Frontespizio_infermieristica";
					$query_count_cc_inferm = "SELECT versione FROM utenti_cartelle WHERE idutente = $id_paziente AND id = $id_cartella AND idregime = ".$row_sfoglia_ccd['idregime'];
					$result_query_count_cc_inferm = $conn->query($query_count_cc_inferm)->fetchAll();

					$query_count_cc_inferm = $result_query_count_cc_inferm[0];
					if($query_count_cc_inferm['versione'] < 10)	
						 $versione = "0".$query_count_cc_inferm['versione']; 
					else $versione =     $query_count_cc_inferm['versione']; 
					$codice_cartella = $row_sfoglia_ccd['codice_cartella'] . "/".$versione."INF";
					
					$query_get_date_imp = "SELECT DataInizioTrattamento, DataFineTrattamento FROM impegnative WHERE idutente = $id_paziente AND RegimeAssistenza = ".$row_sfoglia_ccd['idregime'];
					$result_query_get_date_imp = $conn->query($query_get_date_imp)->fetchAll();
					if (count($result_query_get_date_imp) > 0) {
						$query_get_date_imp = $result_query_get_date_imp[0];
						
						if($query_get_date_imp['DataInizioTrattamento'] !== NULL) {
							$AnnoInizioTrattamento = date('Y', 		strtotime($query_get_date_imp['DataInizioTrattamento']));
							$DataInizioTrattamento = date('d-m-Y',  strtotime($query_get_date_imp['DataInizioTrattamento']));
						}
						if($query_get_date_imp['DataFineTrattamento'] !== NULL) {
							$DataFineTrattamento = 	 date('d-m-Y',  strtotime($query_get_date_imp['DataFineTrattamento']));
						}
					}
					
								
				// FRONTESPIZIO IN CASO DI CARTELLA RESIDENZIALE (intensiva o estensiva)
				} else if(strpos(strtolower($row_sfoglia_ccd['regime']), "residenziale") !== false) {
					$filename = "Cartella_clinica_-_Frontespizio";
					$query_count_cc_residenz = "SELECT versione FROM utenti_cartelle WHERE idutente = $id_paziente AND id = $id_cartella AND idregime = ".$row_sfoglia_ccd['idregime'];
					$result_query_count_cc_residenz = $conn->query($query_count_cc_residenz)->fetchAll();

					$query_count_cc_residenz = $result_query_count_cc_residenz[0];
					if($query_count_cc_residenz['versione'] < 10)	
						 $versione = "0".$query_count_cc_residenz['versione']; 
					else $versione =     $query_count_cc_residenz['versione']; 
					
						if(strtolower($row_sfoglia_ccd['regime']) == "residenziale intensiva")	$suffisso_cc = "INT";
					elseif(strtolower($row_sfoglia_ccd['regime']) == "residenziale estensiva")	$suffisso_cc = "EST";
					else																		$suffisso_cc = "RES";
					
					$codice_cartella = $row_sfoglia_ccd['codice_cartella'] . "/".$versione."".$suffisso_cc;					
				} else {
					
					// FRONTESPIZIO IN CASO DI QUALUNQUE ALTRO TIPO DI CARTELLA CLINICA
					$filename = "Cartella_clinica_-_Frontespizio";
					$query_count_cc = "SELECT versione 
										FROM utenti_cartelle 
										WHERE idutente = $id_paziente AND id = $id_cartella";
					$result_query_count_cc = $conn->query($query_count_cc)->fetchAll();
					$query_count_cc = $result_query_count_cc[0];
					if($query_count_cc['versione'] < 10)	
						 $versione = "0".$query_count_cc['versione']; 
					else $versione =     $query_count_cc['versione']; 
					
					$codice_cartella = $row_sfoglia_ccd['codice_cartella'] . "/".$versione;
				}
				
				$fp = fopen($relative_path."/".$filename.".rtf", "rt");
				$contenuto = fread($fp,filesize($relative_path."/".$filename.".rtf")); 
				$contenuto = rtfReplaceWord('paziente_cartella', $codice_cartella, $contenuto);
				$contenuto = rtfReplaceWord('paziente_normativa', $row_sfoglia_ccd['normativa'], $contenuto);
				$contenuto = rtfReplaceWord('paziente_regime', $row_sfoglia_ccd['regime'], $contenuto);
				$contenuto = rtfReplaceWord('direttore_sanitario_solo_nome', "Dott./Dott.ssa Bertogliatti Sergio", $contenuto);
				
				$contenuto = rtfReplaceWord('paziente_annoiniziotratt',$AnnoInizioTrattamento, $contenuto);
				$contenuto = rtfReplaceWord('paziente_datainiziotratt',$DataInizioTrattamento, $contenuto);
				$contenuto = rtfReplaceWord('paziente_datafinetratt',$DataFineTrattamento, $contenuto);
			
				$tmp_filename = "tmp_".date('H_i_s')."__".$filename;
				if(file_put_contents($tmp_relative_path."/".$tmp_filename.".rtf", $contenuto)) {
					$frontespizio = 1;
					fclose($fp);
					//system('start /B soffice "-env:UserInstallation=file:///C:/web/Republic/rtf2pdf/temp" --convert-to pdf:writer_pdf_Export --accept=socket,host=0,port=8100;urp; '.$tmp_relative_path.'/'.$tmp_filename.'.rtf --outdir '.$tmp_relative_path, $esito_convers_frontespizio);
					ob_start();
					system('"C:\Program Files\LibreOffice\program\soffice" "-env:UserInstallation=file:///C:/web/Republic/rtf2pdf/temp" --headless --convert-to pdf '.$tmp_relative_path.'/'.$tmp_filename.'.rtf --outdir '.$tmp_relative_path, $esito_convers_frontespizio);
					ob_end_clean();
					

//start /B soffice "-env:UserInstallation=file:///C:/web/Republic/rtf2pdf/temp" --headless --convert-to pdf:writer_pdf_Export C:/web/Republic/allegati_sgq/modelli/Cartella_clinica_-_Frontespizio.doc --outdir C:/web/Republic/modelli_word/stampe_ccd_temp
					$s = 0;
					
					do {						
						if (file_exists($tmp_relative_path."/".$tmp_filename.".pdf")) {
							$pdf->addPDF($tmp_relative_path."/".$tmp_filename.".pdf", "all");
							break;
						}
						
						sleep(1);   // ad ogni check attendo 1 secondo e lo ripeto, finchè il file non esiste e vado avanti con addPDF
						$s++;
						if($s > 10) {
							die("Errore di invio del comando system.");
						}
					} while(!file_exists($tmp_relative_path.'/'.$tmp_filename.'.pdf'));

					/*
					if($esito_convers_frontespizio == '0') {		// comando inoltrato correttamente
						
						while(!file_exists($tmp_relative_path.'/'.$tmp_filename.'.pdf')) sleep(1);   // ad ogni check attendo 1 secondo e lo ripeto, finchè il file non esiste e vado avanti con addPDF
						
						if (file_exists($tmp_relative_path."/".$tmp_filename.".pdf")) {
							$pdf->addPDF($tmp_relative_path."/".$tmp_filename.".pdf", "all");
						}
						
					} else die("Errore di invio del comando system. 1");*/
				}
			}
 
 
 //echo '<pre>'.var_dump($row_sfoglia_ccd).'</pre>';
			
			// GENERO LE TABELLE DINAMICHE RIASSUNTIVE SOLO PER I MODULI CHE LA RICHIEDONO
			$titolo = $sottotitolo = "";
			$cerca_allegati_istanza = "";	// se 1, crea le tbl dinamiche con gli allegati associati alle istanze dei moduli
											// se 0, crea le tbl dinamiche con gli allegati caricati all'interno dei moduli
			switch($row_sfoglia_ccd['id_modulo_padre']) 
			{				
			// EX 26 e LEGGE8
				case 130: $cerca_allegati_istanza = 1;	$cerca_allegati_istanza = 1;	 $titolo = "PROGETTO RIABILITATIVO";	 $sottotitolo = "(n.b. i progetti riabilitativi sono redatti dalla competente ASL, eventuale presenza di progetti redatti dal centro sono esclusivamente di natura propositiva in una ottica di presa in carico globale del paziente. La struttura non ha responsabilità sui progetti che sono di competenza della Pubblica Amministrazione)";	break;
				case 149: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - FUNZIONI SENSOMOTORIE";					break;
				case 131: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - MOBILITÀ E SPOSTAMENTI";					break;
				case 150: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - COMUNICATIVO / RELAZIONALI";				break;
				case 151: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - COGNITIVO / COMPORTAMENTALI";				break;
				case 153: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - AREA EMOTIVA AFFETTIVA";					break;
				case 152: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - AUTONOMIA CURA PERSONA";					break;
				case 154: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - RIADATTAMENTO / REINSERIMENTO SOCIALE";	break;
				case 132: $cerca_allegati_istanza = 1;	 $titolo = "VISITE SPECIALISTICHE PSICOLOGICA";									break;
				case 143: $cerca_allegati_istanza = 1;	 $titolo = "PIANO ESECUTIVO INDIVIDUALE";										break;
				case 145: $cerca_allegati_istanza = 1;	 $titolo = "VISITE SPECIALISTICHE ASSISTENTE SOCIALE";							break;
				case 146: $cerca_allegati_istanza = 1;	 $titolo = "VISITE SPECIALISTICHE NEUROPSICHIATRICHE INFANTILE";				break;
				case 147: $cerca_allegati_istanza = 1;	 $titolo = "VISITE SPECIALISTICHE FONIATRICA";									break;
				case 148: $cerca_allegati_istanza = 1;	 $titolo = "VISITE SPECIALISTICHE FISIATRICA";									break;
				case 134: $cerca_allegati_istanza = 1;	 $titolo = "RIUNIONI DI EQUIPE";												break;
				case 135: $cerca_allegati_istanza = 1;	 $titolo = "VALUTAZIONI/RELAZIONI FISIOTERAPICHE";								break;
				case 156: $cerca_allegati_istanza = 1;	 $titolo = "VALUTAZIONI/RELAZIONI LOGOPEDICHE";									break;
				case 157: $cerca_allegati_istanza = 1;	 $titolo = "VALUTAZIONI/RELAZIONI PSICOMOTORIE";								break;
				case 158: $cerca_allegati_istanza = 1;	 $titolo = "VALUTAZIONI/RELAZIONI PSICOTERAPEUTICHE INDIVIDUALI";				break;
				case 159: $cerca_allegati_istanza = 1;	 $titolo = "VALUTAZIONI/RELAZIONI PSICOTERAPEUTICHE FAMILIARI";					break;
				case 160: $cerca_allegati_istanza = 1;	 $titolo = "VALUTAZIONI/RELAZIONI OPERATORI SEMICONVITTO A";					break;
				case 161: $cerca_allegati_istanza = 1;	 $titolo = "VALUTAZIONI/RELAZIONI OPERATORI SEMICONVITTO B";					break;
				case 165: $cerca_allegati_istanza = 1;	 $titolo = "CONSULENZE / VISITE ESTERNE";										break;
				case 166: $cerca_allegati_istanza = 0;	 $titolo = "PROGETTO SOCIO SANITARIO";											break;
			    case 174: $cerca_allegati_istanza = 1;	 $titolo = "ALLERGIE ALIMENTARI";												break;
				case 137: $cerca_allegati_istanza = 1;	 $titolo = "CONSULENZA PROTESICA";												break;
			// RESIDENZIALE ESTENSIVO
				case 179: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - FUNZIONI MOTORIE E SENSORIALI";							break;
				case 180: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - FUNZIONI CARDIO-RESPIRATORIE";							break;
				case 182: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - FUNZIONI VESCICO-SFINTERICHE";							break;
				case 183: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - FUNZIONI DIGESTIVE";										break;
				case 184: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - FUNZIONI COGNITIVE/COMPORTAMENTALI E DEL LINGUAGGIO";		break;
				case 185: $cerca_allegati_istanza = 1;	 $titolo = "PROGRAMMA RIABILITATIVO - INFORMAZIONE/FORMAZIONE AL CARE GIVER";					break;
				case 196: $cerca_allegati_istanza = 1;	 $titolo = "MONITORAGGIO DELLO STATO DI SALUTE DA PARTE DEL MMG CURANTE";						break;
				case 210: $cerca_allegati_istanza = 0;	 $titolo = "DOCUMENTAZIONE ALLEGATA";															break;
				case 240: $cerca_allegati_istanza = 1;	 $titolo = "MONITORAGGIO STATO DI SALUTE MEDICO CURANTE";										break;
			}
			
			if($titolo !== "") 
			{
				$salta_modulo = 0;
				for($m = 0; $m < sizeof($arr_moduli_letti); $m++) {
					if($row_sfoglia_ccd['id_modulo_padre'] == $arr_moduli_letti[$m]) {
						$salta_modulo = 1;			
						$break;
					}
				}
					
				if($salta_modulo == 0)	// non ho ancora elaborato questo modulo
				{
					$relative_path = "../modelli_word";
					$filename = "Tabella_riepilogativa_per_stampa_ccd";
					$fp = fopen($relative_path."/".$filename.".rtf","rt");
					$contenuto = fread($fp,filesize($relative_path."/".$filename.".rtf")); 
					
					$contenuto = rtfReplaceWord('paziente_cognome', $row_sfoglia_ccd['cognome'], $contenuto);
					$contenuto = rtfReplaceWord('paziente_nome', $row_sfoglia_ccd['nome'], $contenuto);
					$contenuto = rtfReplaceWord('paziente_annonascita', date('Y', strtotime($row_sfoglia_ccd['DataNascita'])), $contenuto);
					$contenuto = rtfReplaceWord('paziente_cartella', $row_sfoglia_ccd['codice_cartella']."/".$row_sfoglia_ccd['versione'], $contenuto);
					$contenuto = rtfReplaceWord('paziente_normativa', $row_sfoglia_ccd['normativa'], $contenuto);
					
					$contenuto = rtfReplaceWord('titolo', utf8_decode($titolo), $contenuto);
					$contenuto = rtfReplaceWord('sottotitolo', utf8_decode($sottotitolo), $contenuto);
				
					// PRENDO LE INFORMAZIONI DA INSERIRE NELLA TABELLA DI QUESTO MODULO
					if($cerca_allegati_istanza == 1)
					{
						$query_sfoglia_istanze = "SELECT it.datains, it.allegato, it.allegato2, it.allegato3, it.allegato4, it.id_inserimento, o.nome, it.note, it.id_cartella FROM istanze_testata AS it 
													INNER JOIN operatori AS o ON it.opeins = o.uid
													WHERE it.id_cartella = $id_cartella AND it.id_modulo_padre = ".$row_sfoglia_ccd['id_modulo_padre'];
													
//echo "$query_sfoglia_istanze<br><br>";
						
						$i = 1;
						foreach ($conn->query($query_sfoglia_istanze) as $row_sfoglia_istanze) 
						{
							$text_documento = "";
							
							// verifico che il modulo che sto leggendo abbia o meno un allegato2 con progressivo B.
							// in tal caso, dato che il B rappresenta un aggiornamento del progressivo A (allegato1), scrivo solo il B in tabella
							//if (preg_match('/_.B_/', $row_sfoglia_istanze['allegato2'], $output_array)) 
							if (!empty($row_sfoglia_istanze['allegato4']))
							{
								$text_documento .= utf8_decode($row_sfoglia_istanze['allegato4']);
							} 
							elseif (!empty($row_sfoglia_istanze['allegato3']))
							{
								$text_documento .= utf8_decode($row_sfoglia_istanze['allegato3']);
							} 
							elseif (!empty($row_sfoglia_istanze['allegato2']))
							{
								$text_documento .= utf8_decode($row_sfoglia_istanze['allegato2']);
							}
							elseif(!empty($row_sfoglia_istanze['allegato']))
							{
								$text_documento .= utf8_decode($row_sfoglia_istanze['allegato']);
							}						
							
							if ($text_documento !== "") {
								$contenuto = rtfReplaceWord('data_' 		. $i, date('d-m-Y', strtotime($row_sfoglia_istanze['datains'])), $contenuto);
								$contenuto = rtfReplaceWord('documento_' 	. $i, $text_documento , $contenuto);
								$contenuto = rtfReplaceWord('progressivo_' 	. $i, $row_sfoglia_istanze['id_inserimento'] , $contenuto);
								$contenuto = rtfReplaceWord('responsabile_' . $i, $row_sfoglia_istanze['nome'] , $contenuto);
								$contenuto = rtfReplaceWord('note_' 		. $i, $row_sfoglia_istanze['note'] , $contenuto);
								$i++;
							}
						}
					}
					elseif($cerca_allegati_istanza == 0) 
					{
						$query_sfoglia_istanze = "SELECT it.id_istanza_testata, it.datains, it.id_inserimento, o.nome, it.note, it.id_cartella, id.valore as file_caricato FROM istanze_testata AS it 
													INNER JOIN operatori AS o ON it.opeins = o.uid
													JOIN istanze_dettaglio AS id ON id.id_istanza_testata = it.id_istanza_testata
													JOIN campi as c on c.idcampo = id.idcampo
													WHERE it.id_cartella = $id_cartella AND it.id_modulo_padre = ".$row_sfoglia_ccd['id_modulo_padre'] . " and c.etichetta = 'Allegato'";
//echo "$query_sfoglia_istanze<br><br>";
						$i = 1;
						foreach ($conn->query($query_sfoglia_istanze) as $row_sfoglia_istanze) 
						{
							if($row_sfoglia_istanze['file_caricato'] !== NULL)		$text_documento = utf8_decode($row_sfoglia_istanze['file_caricato']);
							
							if ($text_documento !== "") {
								$contenuto = rtfReplaceWord('data_' 		. $i, date('d-m-Y', strtotime($row_sfoglia_istanze['datains'])), $contenuto);
								$contenuto = rtfReplaceWord('documento_' 	. $i, $text_documento , $contenuto);
								$contenuto = rtfReplaceWord('progressivo_' 	. $i, $row_sfoglia_istanze['id_inserimento'] , $contenuto);
								$contenuto = rtfReplaceWord('responsabile_' . $i, $row_sfoglia_istanze['nome'] , $contenuto);
								$contenuto = rtfReplaceWord('note_' 		. $i, $row_sfoglia_istanze['note'] , $contenuto);
								$i++;
							}
						}
					}
					

					
					// imposto a vuoto tutti gli altri segnalibri rimanenti
					for ($i = $i; $i < 16; $i++) {
						$contenuto = rtfReplaceWord('data_' 		. $i, '', $contenuto);
						$contenuto = rtfReplaceWord('documento_' 	. $i, '', $contenuto);
						$contenuto = rtfReplaceWord('progressivo_' 	. $i, '', $contenuto);
						$contenuto = rtfReplaceWord('responsabile_' . $i, '', $contenuto);
						$contenuto = rtfReplaceWord('note_' 		. $i, '', $contenuto);
					}
					
					
					
					$tmp_filename = "tmp_".date('H_i_s')."__".$row_sfoglia_ccd['id_modulo_padre']."_".$filename;
					file_put_contents($tmp_relative_path."/".$tmp_filename.".rtf", $contenuto);
					fclose($fp);
					
					// converto RTF in PDF
					//system('start /B soffice "-env:UserInstallation=file:///C:/web/Republic/rtf2pdf/temp" --convert-to pdf:writer_pdf_Export --accept=socket,host=0,port=8100;urp; '.$tmp_relative_path.'/'.$tmp_filename.'.rtf --outdir '.$tmp_relative_path, $esito_convers_frontespizio);
					ob_start();
					system('"C:\Program Files\LibreOffice\program\soffice" "-env:UserInstallation=file:///C:/web/Republic/rtf2pdf/temp" --headless --convert-to pdf '.$tmp_relative_path.'/'.$tmp_filename.'.rtf --outdir '.$tmp_relative_path, $esito_convers_frontespizio);
					ob_end_clean();

					if($esito_convers_frontespizio == '0') {		// comando inoltrato correttamente
					
						while(!file_exists($tmp_relative_path."/".$tmp_filename.".pdf")) sleep(1);   // ad ogni check attendo 1 secondo e lo ripeto, finchè il file non esiste e vado avanti con addPDF
						
						if (file_exists($tmp_relative_path."/".$tmp_filename.".pdf"))
							$pdf->addPDF($tmp_relative_path."/".$tmp_filename.".pdf", "all");
						
					} else die("Errore di invio del comando system.");
					
					
					array_push($arr_moduli_letti, $row_sfoglia_ccd['id_modulo_padre']);		// memorizzo in array il modulo appena elaborato
				} // fine salta_modulo
			}
			
			
			
			// dopo aver controllato ed eventualmente creato le tabelle riepilogative, aggiungo al PDF finale gli allegati delle istanze
			
			
			// verifico che il modulo che sto leggendo abbia o meno un allegato2 con progressivo B.
			// in tal caso, dato che il B rappresenta un aggiornamento del progressivo A (allegato1), carico solo il B
			//if (preg_match('/_.B_/', $row_sfoglia_ccd['allegato2'], $output_array)) 
			/*
				FIX SBO 27/05/24
				Per i moduli tyro l'allegato 2 è un file txt che non deve essere caricato, quindi escludo a prescindere il check su allegato 2 per questi moduli
				e carico sempre allegato 1
			*/
			
			$id_modulo_padre_moduli_tyro = array(214, 215, 216, 217, 255);
			$is_tyro = in_array($row_sfoglia_ccd['id_modulo_padre'], $id_modulo_padre_moduli_tyro);
			if (!empty($row_sfoglia_ccd['allegato4']) && !$is_tyro) 
			{
				$allegato4 = "../modelli_word/allegato/".$row_sfoglia_ccd['allegato4'];
				$estensione = substr($allegato4, strrpos($allegato4, '.') + 1);
				if($estensione == 'pdf') {
					if (file_exists($allegato4))
						$pdf->addPDF($allegato4, "all");
				}
			}
			elseif (!empty($row_sfoglia_ccd['allegato3']) && !$is_tyro) 
			{
				$allegato3 = "../modelli_word/allegato/".$row_sfoglia_ccd['allegato3'];
				$estensione = substr($allegato3, strrpos($allegato3, '.') + 1);
				if($estensione == 'pdf') {
					if (file_exists($allegato3))
						$pdf->addPDF($allegato3, "all");
				}
			}
			elseif (!empty($row_sfoglia_ccd['allegato2']) && !$is_tyro) 
			{
				$allegato2 = "../modelli_word/allegato/".$row_sfoglia_ccd['allegato2'];
				$estensione = substr($allegato2, strrpos($allegato2, '.') + 1);
				if($estensione == 'pdf') {
					if (file_exists($allegato2))
						$pdf->addPDF($allegato2, "all");
				}
			} else {
		
				// se non è presente un allegato2 con progressivo B, procedo al caricamento dell'allegato1 prima e dell'allegato2 poi
					
				if($row_sfoglia_ccd['allegato'] !== NULL) {
					$allegato  = "../modelli_word/allegato/".$row_sfoglia_ccd['allegato'];
					$estensione = substr($allegato, strrpos($allegato, '.') + 1);
					if($estensione == 'pdf') {
						if (file_exists($allegato)) {
							$pdf->addPDF($allegato, "all");
						}
					}
				}
				if($row_sfoglia_ccd['allegato2'] !== NULL) {
					$allegato2 = "../modelli_word/allegato/".$row_sfoglia_ccd['allegato2'];
					$estensione = substr($allegato2, strrpos($allegato2, '.') + 1);
					if($estensione == 'pdf') {
						if (file_exists($allegato2)) {
							$pdf->addPDF($allegato2, "all");
						}
					}
				}
			}
			
			
		}
	}

	if((isset($_REQUEST['no_firma']) && !$_REQUEST['no_firma'])) {
		$filename = $id_cartella."_".$id_paziente."_Cartella_Clinica_SIGNED.pdf";	
		$pin = $_REQUEST['pin_infocert'];
		$uid = $_REQUEST['uid'];
		$string_pdf = $pdf->merge("string", $filename);
		$base64_pdf = base64_encode($string_pdf);
		
		$pdf = fopen("../modelli_word/allegato/".$filename, 'wb');
		fwrite ($pdf, $string_pdf);
		fclose ($pdf);
		
?>
<script type="text/javascript" src="http://192.168.210.203/remed/script/jquery-1.2.6.min.js"></script>
<script>
		var ScreenWidth=window.screen.width;  
		var ScreenHeight=window.screen.height;  
		var newWindow;
		var w=800;
		var h=600;
		var placementx=(ScreenWidth/2)-((w)/2);  
		var placementy=(ScreenHeight/2)-((h+50)/2); 
		var props = 'scrollBars=no,resizable=yes,toolbar=no,menubar=no,location=no,directories=no,width='+w+',height='+h+',left='+placementx+',top='+placementy+',screenX='+placementx+',screenY='+placementy;
		
		window.open("http://192.168.210.203/firmadigitale/index.php?rtf_landscape=0&pdf=<?=$filename?>&uid=<?=$uid?>&firma_cartella=1&usephp=7&p=<?=$pin?>", "Add_from_Src_to_Dest", props);
</script>
<?php
	} else {
		$pdf->merge("browser", $id_cartella."_".$id_paziente."_Cartella_Clinica.pdf");
	}

	array_map('unlink', glob("$tmp_relative_path/*.*"));		// cancello file temp
	rmdir($tmp_relative_path); 									// cancello DIR temp
		
} else echo "Errore nell'esecuzione";


	
function rtfReplaceWord($bookmark, $word, $content){
	$bookmark = trim($bookmark);
    $ret = str_replace("##$bookmark##", $word, $content);
    return $ret;
}



?>