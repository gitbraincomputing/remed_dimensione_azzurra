<?php
/*
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); */

require_once 'digital_doc_sign_test.php';

if (
    (isset($_POST['pdf'])                && !empty($_POST['pdf']) &&
        isset($_POST['firma_cartella'])        && $_POST['firma_cartella']
    ) ||
    (
        isset($_POST['access_token'])         && !empty($_POST['access_token'])         &&
        isset($_POST['otp'])                  && !empty($_POST['otp'])                    &&
        isset($_POST['alias'])                  && !empty($_POST['alias'])                     &&
        isset($_POST['idcartella'])         && $_POST['idcartella'] > 0               &&
        isset($_POST['idpaziente'])         && $_POST['idpaziente'] > 0               &&
        isset($_POST['idmodulopadre'])          && $_POST['idmodulopadre'] > 0               &&
        isset($_POST['idmoduloversione'])     && $_POST['idmoduloversione'] > 0         &&
        isset($_POST['idinserimento'])          && $_POST['idinserimento'] > 0               &&
        isset($_POST['idoperatore'])         && $_POST['idoperatore'] > 0               &&
        isset($_POST['idregime'])             && $_POST['idregime'] > 0                   &&
        isset($_POST['idimpegnativa'])          &&
        isset($_POST['usephp'])             && $_POST['usephp'] == '7'                   &&
        isset($_POST['tipoallegato'])        && !empty($_POST['tipoallegato'])        &&
        isset($_POST['allegato_successivo'])     && ($_POST['allegato_successivo'] == 0 || $_POST['allegato_successivo'] == 1) &&
        isset($_POST['pin'])                && !empty($_POST['pin'])                 &&
        isset($_POST['rtf_landscape'])      && ($_POST['rtf_landscape'] == 0 || $_POST['rtf_landscape'] == 1) &&
        isset($_POST['rtf'])
    )
) {
    // carico le configurazioni dal file config.rdm.php generale
    $productionConfig = join(DIRECTORY_SEPARATOR, array('C:', 'web', 'Republic', 'include', 'reserved', 'config.rdm.php'));
    require_once($productionConfig);

    $access_token = $_POST['access_token'];
    $transaction_id = $_POST['transaction_id'];
    $otp = $_POST['otp'];
    $dominio_alias_certificato = $_POST['alias'];            // "INFOCERT+CSBMRC84E24C361Z";
    $firma_cartella = $_POST['firma_cartella'] == 'false' ? false : true;

    $rtf_landscape       = $_POST['rtf_landscape'];
    $pdf               = null;    // comprensivo di estensione
    $tipoallegato       = null;
    $allegato_successivo  = null;
    $idcartella       = null;
    $idpaziente       = null;
    $idmodulopadre      = null;
    $idmoduloversione = null;
    $idinserimento       = null;
    $idregime          = null;
    $idimpegnativa      = null;
    $idoperatore       = null;
    $rtf               = null;    // comprensivo di estensione

    if ($firma_cartella) {
        $pdf               = $_POST['pdf'];
    } else {
        $tipoallegato       = $_POST['tipoallegato'];
        $allegato_successivo  = $_POST['allegato_successivo'];
        $idcartella       = $_POST['idcartella'];
        $idpaziente       = $_POST['idpaziente'];
        $idmodulopadre      = $_POST['idmodulopadre'];
        $idmoduloversione = $_POST['idmoduloversione'];
        $idinserimento       = $_POST['idinserimento'];
        $idregime          = $_POST['idregime'];
        $idimpegnativa      = $_POST['idimpegnativa'];
        $idoperatore       = $_POST['idoperatore'];
        $rtf               = str_replace('/', '\\', $_POST['rtf']);        // comprensivo di estensione
    }

    //$pin = $_POST['pin'];
    $pin = "14725836";    // SVILUPPO

    if (!$firma_cartella) {
        if (empty($idimpegnativa) || $idimpegnativa == 'NULL')
            $imp_query = "";
        else $imp_query = "AND it.id_impegnativa = $idimpegnativa ";

        $query_check_prec_firme = "SELECT itf.* from istanze_testata AS it
									JOIN istanze_testata_firme AS itf ON itf.id_istanza_testata = it.id_istanza_testata
									WHERE it.id_cartella = $idcartella AND it.id_inserimento = $idinserimento AND it.id_modulo_padre = $idmodulopadre AND it.id_modulo_versione = $idmoduloversione $imp_query
									ORDER BY itf.id ASC";
        // ORDER BY itf.stato_firma_$tipoallegato DESC"; 
        // ho cambiato l'ordinamento cosi da avere la corretta sequenza di allegati

        $result_check_prec_firme = $conn->query($query_check_prec_firme)->fetchAll();

        $prima_firma_apposta = $doc_multifirma = $num_firma_da_apporre = $id_istanza_testata = "";

        if (count($result_check_prec_firme) > 0) {
            foreach ($result_check_prec_firme as $key => $row_check_prec_firme) {
                $id_istanza_testata = $row_check_prec_firme['id_istanza_testata'];

                // Controllo se e' già stata apposta una firma per questa istanza, il che vuol dire che e' stato gia' creato un PDF.
                // In questo modo so di non dover ricreare il PDF dal RTF (operazione che sovrascriverebbe il precedente file gia' creato e firmato)
                // ma di dover inviare ad Infocert il PDF gia' creato e firmato da un altro medico, cosi' da avere quest'altra firma e, dunque, la multifirma sul documento
                if (($prima_firma_apposta == "" && $row_check_prec_firme['stato_firma_' . $tipoallegato] == 's') ||
                    ($idmodulopadre == 269 && $tipoallegato !== 'allegato')
                ) {
                    $prima_firma_apposta = 's';
                }

                // Mi prendo il numero di firma in attesa (ad es., sta firmando il terzo operatore).
                // Con questo numero, in validate.php, mi calcolo la posizione del sigillo per evitare che venga sovrapposto ad altri presenti in caso di multifirma
                if (($num_firma_da_apporre === "" && $row_check_prec_firme['stato_firma_' . $tipoallegato] == 'a')                    
                ) {
                    $num_firma_da_apporre = $key + 1;
                }
            }
        }
		
		if($idmodulopadre == 269) {
			if($tipoallegato == 'allegato2')
				$num_firma_da_apporre = 3;
			elseif($tipoallegato == 'allegato3')
				$num_firma_da_apporre = 4;
			elseif($tipoallegato == 'allegato4')
				$num_firma_da_apporre = 5;
		}

        //if (count($result_check_prec_firme) > 1) 		// documento con piu firme da apporre
        if ($idmodulopadre == 134 || $idmodulopadre == 269) // modulo "Riunioni d'equipe" o "Emotrasfusioni"
            $doc_multifirma = 's';
        else $doc_multifirma = 'n';

        if ($prima_firma_apposta == "")         $prima_firma_apposta = "n";
        if ($num_firma_da_apporre == "")        $num_firma_da_apporre = 0;

        /* 
			echo $query_check_prec_firme;
			echo "<br><br>doc_multifirma = $doc_multifirma<br>num_firma_da_apporre; $num_firma_da_apporre";
			exit();
			*/

        //if ($prima_firma_apposta == 'n') {
		// Per il modulo Emotrasfusioni deve essere sempre fatto il pdf partendo dall'rtf in quanto per ogni firma c'è un allegato specifico
		if ($prima_firma_apposta == 'n' || ($idmodulopadre == 269 && $num_firma_da_apporre > 2)) {
            // conv RTF to PDF
            $tmp_relative_path = "pdf";
            if (!is_dir($tmp_relative_path))
                mkdir($tmp_relative_path);

            if (!file_exists($rtf)) {
                $response['titolo'] = "Errore, documento da firmare non trovato!";
                $response['sottotitolo'] = "Chiudi questa finestra e ripeti la procedura di firma del documento.<br><br>Se l'errore dovesse ripetersi, aprire un ticket di assistenza.";
                $response['esito'] = 1;
                echo json_encode($response);
                die();
            }

            ob_start();
            system('"C:\Program Files\LibreOffice\program\soffice" "-env:UserInstallation=file:///C:/web/Republic/firmadigitale/temp" --headless --convert-to pdf ' . $rtf . ' --outdir ' . $tmp_relative_path, $esito_convers);
            ob_end_clean();


            if ($esito_convers == '0')          // comando inoltrato correttamente
            {
                $arr_tmp_filename_dest = explode('\\', $rtf);
                $tmp_filename_dest = end($arr_tmp_filename_dest);                    // END prende l'ultimo indice dell'array creato dall'explode
                $tmp_filename_dest = substr($tmp_filename_dest, 0, -4) . ".pdf";        // tolgo l'estensione ".rtf" dal nome e metto ".pdf"

                $s = 0;
                while (!file_exists($tmp_relative_path . "/" . $tmp_filename_dest)) {
                    sleep(1);   // ad ogni check attendo 1 secondo e lo ripeto, finchè il file non esiste e vado avanti con addPDF
                    $s++;
                    if ($s > 60) {
                        $response['titolo'] = "Errore durante la creazione del file PDF (timeout).";
                        $response['sottotitolo'] = "Riprova aggiornando questa pagina (premi F5 sulla tastiera) e richiedendo un nuovo codice OTP da inserire.<br><br>Se l'errore dovesse ripetersi, aprire un ticket di assistenza.";
                        $response['esito'] = 1;
                        //echo json_encode($response);
                        die();
                    }
                }
            } else {
                $response['titolo'] = "Errore durante la creazione del file PDF (system).";
                $response['sottotitolo'] = "Riprova aggiornando questa pagina (premi F5 sulla tastiera) e richiedendo un nuovo codice OTP da inserire.<br><br>Se l'errore dovesse ripetersi, aprire un ticket di assistenza.";
                $response['esito'] = 1;
                //echo json_encode($response);
                die();
            }
            $file = array(
                'allegato' => array(
                    'name' => $tmp_filename_dest,
                    'tmp_name' => $tmp_relative_path . "/" . $tmp_filename_dest,
                    'error' => "0",
                )
            );
            $pdf_content = base64_encode(file_get_contents($file['allegato']['tmp_name']));

            $nome_file_firmato = substr($tmp_filename_dest, 5, -4) . "_SIGNED.pdf";
        } else    // documento gia' firmato
        {
            $query_get_allegato = "SELECT $tipoallegato as allegato from istanze_testata where id_istanza_testata = $id_istanza_testata";
            $result_get_allegato = $conn->query($query_get_allegato)->fetchAll();

            $allegato = $result_get_allegato[0]['allegato'];

            $file = array(
                'allegato' => array(
                    'name' => $allegato,
                    'tmp_name' => "../modelli_word/allegato/" . $allegato,
                    'error' => "0",
                )
            );

            $pdf_content = base64_encode(file_get_contents($file['allegato']['tmp_name']));

            $nome_file_firmato = $allegato;
        }
    } else {
        $doc_multifirma = 'n';
        $num_firma_da_apporre = 1;
        $pdf_content = base64_encode(file_get_contents("../modelli_word/allegato/" . $pdf));
        $nome_file_firmato = $pdf;
    }

    // firma del documento con PADES
    $path_file_firmato = "../modelli_word/allegato";
    $lato_firme = 0; // sinistra

    if ($idmodulopadre == 269) {
        $lato_firme = 1; // destra
    }

    $result = sign_pades_doc($access_token, $dominio_alias_certificato, $pin, $otp, $transaction_id, $pdf_content, $doc_multifirma, $lato_firme, $num_firma_da_apporre, $rtf_landscape, $idmodulopadre);
    // echo "<br><br>RESULT<br>";
    // print_r($result);

    if (property_exists($result, 'message')) {        // errore di autenticazione
        $err_message = $result->message;
        if (!strpos($err_message, 'Unauthorized') !== false) {
            $response['titolo'] = "Non autorizzato.";
            $response['sottotitolo'] = "Chiudi questa finestra e ripeti la procedura di firma del documento.<br><br>Se l'errore dovesse ripetersi, aprire un ticket di assistenza.";
            $response['esito'] = 1;
        } else {
            $response['titolo'] = $err_message;
            $response['sottotitolo'] = "Chiudi questa finestra e ripeti la procedura di firma del documento.<br><br>Se l'errore dovesse ripetersi, aprire un ticket di assistenza.";
            $response['esito'] = 1;
        }
    } else     // utente autenticato
    {
        if (property_exists($result, 'SoapFaultClientException')) {        // pin errato o altro errore
            $response['titolo'] = $result->SoapFaultClientException;
            $response['sottotitolo'] = "";
            $response['esito'] = 1;
            echo json_encode($response);
            die();
        }

        if (property_exists($result, 'signatureResult')) {
            $signatureResult = $result->signatureResult;
            $esito = $signatureResult[0]->isOk;

            if ($esito == 1)    // validazione e firma eseguita
            {
                $signedDocument = $signatureResult[0]->signedDocument;
                $content = $signedDocument->content;
                $pdf_decoded = base64_decode($content);
                $pdf = fopen($path_file_firmato . "/" . $nome_file_firmato, 'w');
                fwrite($pdf, $pdf_decoded);
                fclose($pdf);

                if (!$firma_cartella) {
                    $query_get_iit = "SELECT it.id_istanza_testata 
									FROM istanze_testata AS it 
									WHERE it.id_cartella = $idcartella AND it.id_modulo_versione = $idmoduloversione AND it.id_inserimento = $idinserimento $imp_query";
                    $result_get_iit = $conn->query($query_get_iit);
                    $result_get_iit = $conn->query($query_get_iit)->fetchAll();
                    $id_istanza_testata_da_firmare = $result_get_iit[0]['id_istanza_testata'];

                    $query = "SELECT DISTINCT uc.idregime, m.idmodulo as id_modulo_padre, m.validazione_automatica
									FROM moduli AS m
									INNER JOIN istanze_testata AS it ON it.id_modulo_versione = m.id
									INNER JOIN utenti_cartelle AS uc ON uc.id = it.id_cartella
									WHERE m.id = $idmoduloversione";
                    $info_modulo = $conn->query($query)->fetchAll();
                    $info_modulo = $info_modulo[0];

                    $query = "SELECT dir_sanitario as is_ds
									FROM operatori
									WHERE uid = $idoperatore";
                    $info_ope = $conn->query($query)->fetchAll()[0];
                    $info_ope = $info_ope[0];

                    $cstm_set = "";
                    //eseguo la validazione automatica per tutti i moduli che la prevedono o nel caso in cui sia il ds a fare 
                    //	il caricamento dell'allegato. Disabilito questa operazione per i moduli delle visite specialistiche
                    if (
                        $info_modulo['validazione_automatica'] && $info_ope['is_ds'] == 'y' &&
                        ($allegato_successivo == 0 || ($allegato_successivo == 1 && $tipoallegato == 'allegato2'))
                    ) {
                        $cstm_set = ", ope_validazione = $idoperatore, data_validazione = getdate(), validazione = 's' ";
                    }

                    $query_set_allegato = "UPDATE istanze_testata 
												SET " . $tipoallegato . " = '$nome_file_firmato', data_" . $tipoallegato . "=getdate() $cstm_set
												WHERE id_istanza_testata = $id_istanza_testata_da_firmare";
                    $result_set_allegato = $conn->query($query_set_allegato);

                    $emotrasf_filter = '';
                    if ($idmodulopadre == 269) {
                        $update_firma_allegati = "stato_firma_" . $tipoallegato . "='s', data_firma_" . $tipoallegato . "=getdate()";
                        $emotrasf_filter = " AND stato_firma_" . $tipoallegato . "='a' ";
                    } else {
                        if ($tipoallegato == 'allegato' && $allegato_successivo == 1)    // sto firmando il primo allegato e so che il modulo ne prevede un secondo
                            $update_firma_allegati = "stato_firma_" . $tipoallegato . "='s', data_firma_" . $tipoallegato . "=getdate(), stato_firma_allegato2='a'";
                        elseif ($tipoallegato == 'allegato2' && $allegato_successivo == 1)
                            $update_firma_allegati = "stato_firma_allegato2='s', data_firma_allegato2=getdate(), stato_firma_allegato3='a'";
                        elseif ($tipoallegato == 'allegato3' && $allegato_successivo == 1)
                            $update_firma_allegati = "stato_firma_allegato3='s', data_firma_allegato3=getdate(), stato_firma_allegato4='a'";
                        else
                            $update_firma_allegati = "stato_firma_" . $tipoallegato . "='s', data_firma_" . $tipoallegato . "=getdate()";
                    }

                    $query_set_firma = "UPDATE istanze_testata_firme 
											SET " . $update_firma_allegati . " 
											WHERE id_istanza_testata = $id_istanza_testata_da_firmare AND id_operatore = $idoperatore
													$emotrasf_filter";
                    $result_set_firma = $conn->query($query_set_firma);

                    if ($result_set_firma->execute()) {
                        $response['titolo'] = "Documento firmato!";
                        $response['sottotitolo'] = "";
                        $response['esito'] = '0';
                    } else {
                        $response['titolo'] = "Documento firmato ma non associato.";
                        $response['sottotitolo'] = "Il database non ha risposto in un tempo ragionevole e non è stato possibile associare il documento all'istanza.<br><br>Siete pregati di aprire un ticket di assistenza.";
                        $response['esito'] = '-1';
                    }
                } else {
                    $response['titolo'] = "Documento firmato!";
                    $response['sottotitolo'] = "";
                    $response['esito'] = '0';
                }
            } else     // altro errore
            {
                $response['titolo'] = "Si e' verificato un errore sconosciuto.";
                $response['sottotitolo'] = "";
                $response['esito'] = 1;

                if (property_exists($result, 'signatureResult')) {
                    $response['titolo'] = $result->signatureResult[0]->signatureError->title;
                    if (property_exists($result, 'detail'))
                        $response['sottotitolo'] = $result->signatureResult[0]->signatureError->detail;
                    else $response['sottotitolo'] = $result->signatureResult[0]->signatureError->title;
                }
                echo json_encode($response);
                die();
            }
        } else {
            if (property_exists($result, 'title'))
                $response['titolo'] = $result->title;
            else $response['titolo'] = "Si e' verificato un errore sconosciuto, di rete o esterno a ReMed.";

            if (property_exists($result, 'detail'))
                $response['sottotitolo'] = $result->detail;
            else $response['sottotitolo'] = $result;

            $response['esito'] = 1;
            echo json_encode($response);
            die();
        }
    }

    /*
		// -----------------------------------------

		// firma del documento con CADES

		$nome_file_firmato = substr($tmp_filename_dest, 5, -1)."_SIGNED.p7m";
		$result = sign_cades_doc($access_token, $dominio_alias_certificato, $pin, $otp, $transaction_id, $pdf_content);

		echo "<pre>";
		print_r($result);
		echo "</pre>";

		$signatureResult = $result->signatureResult;
		$signedDocument = $signatureResult[0]->signedDocument;
		$content = $signedDocument->content;
		$pdf_decoded = base64_decode($content);
		$pdf = fopen($nome_file_firmato,'w');
		fwrite ($pdf, $pdf_decoded);
		fclose ($pdf);
*/

    unlink($tmp_relative_path . "/" . $tmp_filename_dest);
    unlink($rtf);
    echo json_encode($response);
} else {
    $response = array();
    $response['titolo'] = "Parametri non inviati correttamente.";
    $response['sottotitolo'] = "";
    $response['esito'] = 1;
    echo json_encode($response);
}
