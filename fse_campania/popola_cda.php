<?php
// carico le configurazioni dal file config.rdm.php generale
	$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));
	require_once($productionConfig);

function crea_pdf_per_invio_fse($tmp_relative_path, $tmp_filename_dest) 
{
	$response = [];
	
	
	
	
// DEFINIZIONI FILE E LETTURA TEMPLATE CDA	
	$filename_template_cda = "cda_template.xml";
	$filename_output_cda   = "cda.xml";
	
	if(file_exists($filename_template_cda))
	{
		$file_cda_template = fopen($filename_template_cda,"rt");
		$contenuto = fread($file_cda_template, filesize($filename_template_cda));
		fclose($file_cda_template);
	} else {
		$response['cod_esito'] = 'KO';
		$response['errore'] = 'File template CDA non trovato.';
		return json_encode($response);
		die();
	}




// ESEGUO LA QUERY PER ESTRARMI I DATI
	$query = "SELECT * FROM re_estrai_dati_cda_xml_fse WHERE id_istanza_testata = 14355";
	$result = $conn->query($query)->fetchAll();
	
	$query_valori_modulo = "SELECT c.etichetta, id.valore FROM istanze_dettaglio AS id
							JOIN campi As c ON c.idcampo = id.idcampo
							WHERE id.id_istanza_testata = 14355";
	$result_valori_modulo = $conn->query($query_valori_modulo)->fetchAll();
	
	
##########################
### RACCOLTA VARIABILI ###
##########################
	
// INFO MODULO
	$nome_modulo  = trim($result[0]['nome_modulo']);
	
		// elimino le parte non necessarie dal nome del modulo
		if ((strpos($nome_modulo, "Cartella Clinica - ")) 				  	!== false)		$nome_modulo = substr($nome_modulo, strlen('Cartella Clinica - '));
	elseif ((strpos($nome_modulo, "Cart. Clinica ex26 - Sezione ")) 	  	!== false)		$nome_modulo = substr($nome_modulo, strlen('Cart. Clinica ex26 - Sezione ') + 4);
	elseif ((strpos($nome_modulo, "Cartella Clinica (ex26) - ")) 		  	!== false)		$nome_modulo = substr($nome_modulo, strlen('Cartella Clinica (ex26) - '));
	elseif ((strpos($nome_modulo, "Cartella Clinica (ex26 e Legge8) - ")) 	!== false)		$nome_modulo = substr($nome_modulo, strlen('Cartella Clinica (ex26 e Legge8) - '));
	elseif ((strpos($nome_modulo, "Cartella Clinica (Legge8) - ")) 			!== false)		$nome_modulo = substr($nome_modulo, strlen('Cartella Clinica (Legge8) - '));
	elseif ((strpos($nome_modulo, "Cart. Clinica legge8 - ")) 			  	!== false)		$nome_modulo = substr($nome_modulo, strlen('Cart. Clinica legge8 - '));
	elseif ((strpos($nome_modulo, "Cartella Infermieristica - ")) 			!== false)		$nome_modulo = substr($nome_modulo, strlen('Cartella Infermieristica - '));

	$progressivo    = $result[0]['progressivo'];
	$data_creazione = date('Ymd', strtotime($result[0]['datains'])) . date('Hi', strtotime($result[0]['orains'])) . "00+0100";
	
	$flag_offuscamento = strtoupper($result[0]['offuscamento_fse']);
		if($flag_offuscamento == 'N')	$display_name_offuscamento = "Normal";
	elseif($flag_offuscamento == 'V') 	$display_name_offuscamento = "Very Restricted";

	$valori_compilati_modulo = "";
	foreach ($result_valori_modulo as $etichetta_valori) {
		$valori_compilati_modulo .= $etichetta_valori['etichetta'] . "\n" . $etichetta_valori['valore'] . "\n\n";
	}
	
// INFO PAZIENTE
	$cognome_paziente 		 = trim($result[0]['cognome_paziente']);
	$nome_paziente 			 = trim($result[0]['nome_paziente']);
	
	if(trim($result[0]['sesso_paziente']) == "1") {
		$sesso_sigla_paziente 		= "M"; 
		$sesso_descrizione_paziente = "Maschio";
	} else {	
		$sesso_sigla_paziente 		= "F"; 
		$sesso_descrizione_paziente = "Femmina";
	}
	
	$data_nascita_paziente 	 = date('Ymd', strtotime($result[0]['data_nascita_paziente']));
	$codice_fiscale_paziente = trim($result[0]['codice_fiscale_paziente']);
	
	if(!empty(trim($result[0]['tel_paziente'])))
		 $tel_paziente = '<telecom use="HP" value="tel:'.trim($result[0]['tel_paziente']).'"></telecom>';
	else $tel_paziente = "";
	
	if(!empty(trim($result[0]['cell_paziente'])))
		 $cell_paziente = '<telecom use="HP" value="tel:'.trim($result[0]['cell_paziente']).'"></telecom>';
	else $cell_paziente = "";

	if(!empty(trim($result[0]['email_paziente'])))
		 $email_paziente = '<telecom use="HP" value="mailto://'.trim($result[0]['email_paziente']).'"></telecom>';
	else $email_paziente = "";
	
	$indirizzo_res_paziente  = trim($result[0]['indirizzo_res_paziente']);
	$comune_res_paziente 	 = trim($result[0]['comune_res_paziente']);
	$prov_res_paziente 		 = trim($result[0]['prov_res_paziente']);
	$cap_res_paziente 		 = trim($result[0]['cap_res_paziente']);
	
	
	
// INFO DIRETTORE SANITARIO
	// il campo completo del direttore sanitario è valorizzato con "De Sena Pasqua", 
	// per cui divido la stringa per l'ultimo spazio e mi prendo i valori
	$cognome_ds = substr($result[0]['nominativo_ds'], 0, strrpos($result[0]['nominativo_ds'], ' ') );			// "De Sena"
	$nome_ds 	= substr($result[0]['nominativo_ds'], strrpos($result[0]['nominativo_ds'], ' ') + 1);			// "Pasqua"

	if(!empty(trim($result[0]['email_ds'])))
		 $email_ds = '<telecom use="HP" value="mailto://'.trim($result[0]['email_ds']).'"></telecom>';
	else $email_ds = "";
	
	$codice_fiscale_ds = trim($result[0]['codice_fiscale_ds']);
	
	
	
// INFO STRUTTURA
	$nome_struttura    = trim($result[0]['nome_struttura']);
	$codice_struttura  = trim($result[0]['codice_struttura']);
	$email_struttura   = trim($result[0]['email_struttura']);
	$cod_asl_rif   	   = trim($result[0]['cod_asl_rif']);
	$regione_struttura = trim($result[0]['regione_struttura']);
	
	switch($regione_struttura) {
		case 10:  $nome_regione = "Regione Piemonte";
		case 20:  $nome_regione = "Regione Autonoma Val d'Aosta";
		case 30:  $nome_regione = "Regione Lombardia";
		case 41:  $nome_regione = "Provincia autonoma di Bolzano";
		case 42:  $nome_regione = "Provincia autonoma di Trento";
		case 50:  $nome_regione = "Regione Veneto";
		case 60:  $nome_regione = "Regione Friuli Venezia Giulia";
		case 70:  $nome_regione = "Regione Liguria";
		case 80:  $nome_regione = "Regione Emilia Romagna";
		case 90:  $nome_regione = "Regione Toscana";
		case 100: $nome_regione = "Regione Umbria";
		case 110: $nome_regione = "Regione Marche";
		case 120: $nome_regione = "Regione Lazio";
		case 130: $nome_regione = "Regione Abruzzo";
		case 140: $nome_regione = "Regione Molise";
		case 150: $nome_regione = "Regione Campania";
		case 160: $nome_regione = "Regione Puglia";
		case 170: $nome_regione = "Regione Basilicata";
		case 180: $nome_regione = "Regione Calabria";
		case 190: $nome_regione = "Regione Sicilia";
		case 200: $nome_regione = "Regione Sardegna";
	}


######################################
### INIZIO SOSTITUZIONI SEGNALIBRI ###
######################################

	$contenuto = str_replace("##nome_modulo##", $nome_modulo, $contenuto);
	$contenuto = str_replace("##progressivo##", $progressivo, $contenuto);
	$contenuto = str_replace("##data_creazione##", $data_creazione, $contenuto);
	$contenuto = str_replace("##flag_offuscamento##", $flag_offuscamento, $contenuto);
	$contenuto = str_replace("##display_name_offuscamento##", $display_name_offuscamento, $contenuto);
	
	$contenuto = str_replace("##cognome_paziente##", $cognome_paziente, $contenuto);
	$contenuto = str_replace("##nome_paziente##", $nome_paziente, $contenuto);
	$contenuto = str_replace("##sesso_sigla_paziente##", $sesso_sigla_paziente, $contenuto);
	$contenuto = str_replace("##sesso_descrizione_paziente##", $sesso_descrizione_paziente, $contenuto);
	$contenuto = str_replace("##data_nascita_paziente##", $data_nascita_paziente, $contenuto);
	$contenuto = str_replace("##codice_fiscale_paziente##", $codice_fiscale_paziente, $contenuto);
	$contenuto = str_replace("##tel_paziente##", $tel_paziente, $contenuto);
	$contenuto = str_replace("##cell_paziente##", $cell_paziente, $contenuto);
	$contenuto = str_replace("##email_paziente##", $email_paziente, $contenuto);
	$contenuto = str_replace("##indirizzo_res_paziente##", $indirizzo_res_paziente, $contenuto);
	$contenuto = str_replace("##comune_res_paziente##", $comune_res_paziente, $contenuto);
	$contenuto = str_replace("##prov_res_paziente##", $prov_res_paziente, $contenuto);
	$contenuto = str_replace("##cap_res_paziente##", $cap_res_paziente, $contenuto);
	
	$contenuto = str_replace("##cognome_ds##", $cognome_ds, $contenuto);
	$contenuto = str_replace("##nome_ds##", $nome_ds, $contenuto);	
	$contenuto = str_replace("##email_ds##", $email_ds, $contenuto);
	$contenuto = str_replace("##codice_fiscale_ds##", $codice_fiscale_ds, $contenuto);
	
	$contenuto = str_replace("##nome_struttura##", $nome_struttura, $contenuto);
	$contenuto = str_replace("##codice_struttura##", $codice_struttura, $contenuto);
	$contenuto = str_replace("##regione_struttura##", $regione_struttura, $contenuto);
	$contenuto = str_replace("##nome_regione##", $nome_regione, $contenuto);
	$contenuto = str_replace("##cod_asl_rif##", $cod_asl_rif, $contenuto);
	$contenuto = str_replace("##email_struttura##", $email_struttura, $contenuto);
	
	$contenuto = preg_replace("~<!--(.*?)-->~s", "", $contenuto);				// rimuove tutti i commenti dal contenuto del file template
	$contenuto = trim(preg_replace('/^\n+|^[\t\s]*\n+/m', '', $contenuto));		// rimuove tutte le righe vuote dal contenuto del file template

// scrive e salva il CDA di output col contenuto compilato
	file_put_contents($filename_output_cda, $contenuto);
	
	
####################################################
### CONVERTO IL PDF IN PDF/A TRAMITE GhostScript ###
####################################################	
	ob_start();
	system('"C:\Program Files\gs\gs10.01.2\bin\gswin64" -dPDFA -dBATCH -dNOPAUSE -sColorConversionStrategy=UseDeviceIndependentColor -sDEVICE=pdfwrite -dPDFACompatibilityPolicy=2 -sOutputFile=' . $tmp_relative_path . '\output_filename.pdf ' . $tmp_relative_path . '\\' . $tmp_filename_dest, $esito_convers);
	ob_end_clean();
	
	if($esito_convers == '0')  		// comando inoltrato correttamente
	{
		$s = 0;
		while(!file_exists($filename_output_cda)) 
		{
			sleep(1);   // ad ogni check attendo 1 secondo e lo ripeto, finchè il file non esiste
			$s++;
			if($s > 60) {
				$response['cod_esito'] = 'KO';
				$response['errore'] = 'Errore durante la creazione del file PDF/A (timeout).';
				return json_encode($response);
				die();
			}
		}
		
		
######################################################
### CREATO IL PDF/A INIETTO IL CDA.XML ALL'INTERNO ###
###   (crea un nuovo PDF col file xml allegato)    ###
######################################################
		ob_start();
		system('java -jar pdf-generator\target\pdf-generator.jar -p ' . $tmp_relative_path . '\output_filename.pdf -c cda.xml -x', $esito_convers);
		ob_end_clean();
		
		if($esito_convers == '0')  		// comando inoltrato correttamente
		{
			$s = 0;
			while(!file_exists($filename_output_cda)) 
			{
				sleep(1);   // ad ogni check attendo 1 secondo e lo ripeto, finchè il file non esiste
				$s++;
				if($s > 60) {
					$response['cod_esito'] = 'KO';
					$response['errore'] = 'Errore durante la creazione del file PDF/A con XML (timeout).';
					return json_encode($response);
					die();
				}
				
			}
			
			// cancello il pdf di partenza standard senza l'xml iniettato
			unlink($tmp_relative_path . '\\' . $tmp_filename_dest);
			
			// rinomino il PDF/A con xml iniettato nel nome del PDF di partenza
			rename($tmp_relative_path . '\output_filename.pdf', $tmp_relative_path . '\\' . $tmp_filename_dest);
			
			
			
			$response['cod_esito'] = 'OK';
			//$response['filename'] = 'output_filename.pdf';
			return json_encode($response);
			die();
			
		} else {
			$response['cod_esito'] = 'KO';
			$response['errore'] = 'Errore durante la creazione del file PDF/A con XML (exec).';
			return json_encode($response);
			die();
		}
		
		
		
		
		
	} else {
		$response['cod_esito'] = 'KO';
		$response['errore'] = 'Errore durante la creazione del file PDF/A (exec).';
		return json_encode($response);
		die();
	}

	
}
	

	
// fine

?>