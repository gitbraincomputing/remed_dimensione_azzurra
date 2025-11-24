<?php

function thumb($path,$sourcefile,$h,$w,$call,$format){

switch ($format){
		case 1: //96x96
			$tmb="images/thumb/tb1_";
			break;
		case 2: //174x93
			$tmb="images/thumb/tb2_";
			break;
		case 3: //467x254
			$tmb="images/thumb/tb3_";
			break;
	}

if ($call=='es') $tmb='../'.$tmb;

// Ottengo le informazioni sull'immagine originale
list($width, $height, $type, $attr) = getimagesize($path.$sourcefile);

//echo("iniziali: ".$width."-".$height."<br />");
// Creo la versione 120*90 dell'immagine (thumbnail)


$altezza=$h;
$larghezza=round((($width*$altezza)/$height),0);
//echo("primo: ".$larghezza."-".$altezza);

if($larghezza<$w){
	$larghezza=$w;
	$altezza=round((($height*$larghezza)/$width),0);
	//echo("primo: ".$larghezza."-".$altezza);
}

$thumb = imagecreatetruecolor($larghezza, $altezza);
switch($type) {
		case '1': 
			$source = imagecreatefromgif($path.$sourcefile);
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);			
			// Salvo l'immagine ridimensionata
			imagegif($thumb, $tmb.$sourcefile, 100);
		break;
		case '2': 
			$source = imagecreatefromjpeg($path.$sourcefile);			
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);			
			// Salvo l'immagine ridimensionata
			imagejpeg($thumb, $tmb.$sourcefile, 100);
		break;
		case '3':
			$source = imagecreatefrompng($path.$sourcefile);
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);	
			// Salvo l'immagine ridimensionata
			imagepng($thumb, $tmb.$sourcefile, 100);
		break;
}

}





function thumb_1($path,$sourcefile,$h,$w,$call){

// Ottengo le informazioni sull'immagine originale
		//$sourcefile=$_SESSION['IMMAGINI'][$index]['immagine'];
		list($width, $height, $type, $attr) = getimagesize($path.$sourcefile);	
		
		// Creo la versione 120*90 dell'immagine (thumbnail)
		$thumb = imagecreatetruecolor($h, $w);
		if ($call=='es') {
			$tmb='../images/thumb/tb_';}
			else {
			$tmb='images/thumb/tb_';}
		
		switch($type) {
				case '1': 
					$source = imagecreatefromgif($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $h, $w, $width, $height);		
					// Salvo l'immagine ridimensionata
					imagegif($thumb,$tmb.$sourcefile);
				break;
				case '2': 
					$source = imagecreatefromjpeg($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $h, $w, $width, $height);
					// Salvo l'immagine ridimensionata
					imagejpeg($thumb,$tmb.$sourcefile, 75);
				break;
				case '3': 
					$source = imagecreatefrompng($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $h, $w, $width, $height);
					// Salvo l'immagine ridimensionata
					imagepng($thumb,$tmb.$sourcefile);
				break;
		}
		
}

function riduci_foto($path,$dest,$sourcefile,$h,$w){
//$dest='images/thumb/';
// Ottengo le informazioni sull'immagine originale
		//$sourcefile=$_SESSION['IMMAGINI'][$index]['immagine'];
		list($width, $height, $type, $attr) = getimagesize($path.$sourcefile);			
		$altezza=$h;
		$larghezza=round((($width*$altezza)/$height),0);
		//echo("primo: ".$larghezza."-".$altezza);

		if($larghezza<$w){
			$larghezza=$w;
			$altezza=round((($height*$larghezza)/$width),0);
			//echo("primo: ".$larghezza."-".$altezza);
		}
		
		// Creo la versione 120*90 dell'immagine (thumbnail)
		$thumb = imagecreatetruecolor($larghezza, $altezza);
			
		switch($type) {
				case '1': 
					$source = imagecreatefromgif($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);		
					// Salvo l'immagine ridimensionata
					imagegif($thumb,$dest."tb_".$sourcefile);
				break;
				case '2': 
					$source = imagecreatefromjpeg($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);
					// Salvo l'immagine ridimensionata
					imagejpeg($thumb,$dest."tb_".$sourcefile, 75);					
				break;
				case '3': 
					$source = imagecreatefrompng($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);
					// Salvo l'immagine ridimensionata
					imagepng($thumb,$dest."tb_".$sourcefile);
				break;
		}		
		
}


function ftp_update($nomefile,$tmp_name,$size,$path){

	
	$imgHNDL = fopen($tmp_name, "r");
	$imgf = fread($imgHNDL, $size);	
	// apre la connessione ftp
	$conn_id = ftp_connect(IP_ADDR);
	// login con user name e password
	$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 
	// controllo della connessione
	if ((!$conn_id) || (!$login_result)) { 
		echo "La connessione FTP è fallita!";
		echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 
		die; 	
	} else {		
			// passa in passive mode			
			ftp_pasv ( $conn_id, true );
			
			$temp = tmpfile();
			fwrite($temp, $imgf);
			fseek($temp, 0);

			// si posiziona nella directory giusta
			if (!ftp_chdir($conn_id, $path)) {
				 error_message("non posso cambiare dir");
				 exit;
			} 
			
			// upload del file
			if(! ($upload = ftp_fput ($conn_id, $nomefile , $temp ,FTP_BINARY))) {
				 error_message("errore upload del file");
				 exit;
			}
			
			// chiude l'handler del file
			fclose($temp);

	}

	fclose($imgHNDL);

	// chiudere il flusso FTP 
	ftp_quit($conn_id); 

}


function ftp_copy($source_file, $destination_file)
{
	$ftp_server = IP_ADDR;
	$ftp_user = FTP_USR;
	$ftp_password = FTP_PWD;

	//echo($source_file." ".$destination_file);
	$conn_id = ftp_connect($ftp_server);
	$login_result = ftp_login($conn_id, $ftp_user, $ftp_password);

	if((!$conn_id) || (!$login_result))
	{
            echo "FTP connection has failed!";
            echo "Attempted to connect to $ftp_server for user $ftp_user";
   	}

	$upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);
	ftp_close($conn_id); 

	if(!$upload)
	{
           echo "FTP copy has failed!";
	   return false;
   	}
	else
	{
	    return true;
	}	
}

function ftp_del_file($nomefile)
{
	$ftp_server = IP_ADDR;
	$ftp_user = FTP_USR;
	$ftp_password = FTP_PWD;

	$conn_id = ftp_connect($ftp_server);
	$login_result = ftp_login($conn_id, $ftp_user, $ftp_password);

	if((!$conn_id) || (!$login_result))
	{
            echo "FTP connection has failed!";
            echo "Attempted to connect to $ftp_server for user $ftp_user";
   	}

	ftp_delete($conn_id, $nomefile);
	ftp_close($conn_id); 
	
}

/********************************************************

* funzione cartella_ftp            						*

*********************************************************/

function cartella_ftp ( $dirname, $old_dirname, $action ) {



		
		
		

		// apre la connessione ftp

		$conn_id = ftp_connect(IP_ADDR);	

	

		// login con user name e password

		$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 

	

		// controllo della connessione

		if ((!$conn_id) || (!$login_result)) { 

			echo "La connessione FTP è fallita!";

			echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 

			die; 

		

		} else {

			

			// recupera il percorso corrente

			$curr_path = WEB_PATH.$percorso;
			$curr_path_ftp = FTP_PATH;

		



			switch($action) {



				case 'create': 



					// passa in passive mode

					ftp_pasv ( $conn_id, true );

					

					
					
					
				

					// crea la cartella via ftp
					
					

								// se non trova la cartella la crea

		 						if (!(ftp_mkdir($conn_id, $dirname))) {

								//	error_message("errore creazione cartella!");

								//	exit;

								}

					
					  

					

				break;



				case 'rename': 



						// si posiziona nella directory giusta

						if (!ftp_chdir($conn_id, $curr_path)) {

							 error_message("non posso cambiare dir");

							 exit;

						} 

						

						// rinomina la cartella, se non la trova la crea

						if(! (ftp_rename($conn_id, $dirname, $dirname)) ) {

								// se non trova la cartella la crea

		 						if (!(ftp_mkdir($conn_id, $dirname))) {

									error_message("errore creazione cartella!");

									exit;

								}

						}

				break;



				case 'delete': 



						// si posiziona nella directory giusta

						if (!ftp_chdir($conn_id, $curr_path)) {

							 error_message("non posso cambiare dir");

							 exit;

						} 



						// rimuove la cartella

						if(! ftp_rmdir($conn_id, $dirname) ) {

							 error_message("errore rimozione cartella");

							 exit;

						}

				break;



			// fine switch

			}



			// chiudere il flusso FTP 

			ftp_quit($conn_id); 





	// fine else

	}





}





/********************************************************

* funzione azione_ftp            						*

*********************************************************/

function azione_ftp ($idstruttura,$curr_path,$path, $filename, $action ) {



		

		
		

		// apre la connessione ftp

		$conn_id = ftp_connect(IP_ADDR);	

	

		// login con user name e password

		$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 

	

		// controllo della connessione

		if ((!$conn_id) || (!$login_result)) { 

			echo "La connessione FTP è fallita!";

			echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 

			die; 

		

		} else {

			

		


			switch($action) {



				case 'upload': 



					// se il file non esiste, lo crea via ftp

					//if (!file_exists($curr_path.'/'.$filename)) {



						// passa in passive mode

						ftp_pasv ( $conn_id, true );
						$percorso2=$path.'template/tmpl_strutture.php';
						

						// scrive il contenuto del file

						$contenuto_file .= '<?php show_struttura('.$idstruttura.');'."\n";

						
						$contenuto_file .= " include_once('".$percorso2."');"."\n";


						
						$contenuto_file .= '?>'."\n";

echo($contenuto_file);

						$temp = tmpfile();

						fwrite($temp, $contenuto_file);

						fseek($temp, 0);

			

						// si posiziona nella directory giusta

						if (!ftp_chdir($conn_id, $curr_path)) {

							 error_message("non posso cambiare dir");

							// exit;

						} 

						

						// upload del file

						if(! ($upload = ftp_fput ($conn_id, $filename, $temp ,FTP_BINARY))) {

							 error_message("errore upload del file");

							// exit;

						}

						

						// chiude l'handler del file

						fclose($temp);

					//}

					

					//else { error_message ("Il file $filename è già presente sul server. Provare con una altro nome

				break;



				case 'rename': 



					// se il file esiste, dà errore

					if (file_exists($curr_path.'/'.$filename)) {

						error_message("Il file è già presente sul server. Provare con un altro nome");

						exit;

					}

					

					else {



						// si posiziona nella directory giusta

						if (!ftp_chdir($conn_id, $curr_path)) {

							 error_message("non posso cambiare dir");

							 exit;

						} 

						

						// rimuove il file

						// formatta il nome come nome file

						if(isset($_SESSION['PAGINA'])) $Obj = $_SESSION['PAGINA'];

						else if(isset($_SESSION['OFFERTA'])) $Obj = $_SESSION['OFFERTA'];

						$old_filename = htmlentities($Obj->get_nomefisico());

						$old_filename = ereg_replace(' ','_',$old_filename);

						$old_filename = preg_replace("/&([a-z])[a-z]+;/i","$1",$old_filename);

						$old_filename = $old_filename.'.php';



						if (file_exists($curr_path.'/'.$old_filename)) {

							if(! (ftp_rename($conn_id, $old_filename, $filename)) ) {

								 error_message("errore rinomina file");

								 exit;

							}

						

						} else {



							error_message("Non trovo il file sul server!");

							exit;

						}



					}

				break;



				case 'delete': 



					// si posiziona nella directory giusta

					if (!ftp_chdir($conn_id, $curr_path)) {

						 error_message("non posso cambiare dir");

						 exit;

					} 

					

					// rimuove il file

					ftp_delete($conn_id, $filename);

					/*if(! ftp_delete($conn_id, $filename) ) {

						 error_message("errore rimozione del file");

						 exit;

					}*/

				break;



			// fine switch

			}



			// chiudere il flusso FTP 

			ftp_quit($conn_id); 





	// fine else

	}



}





/********************************************************

* funzione azione_ftp_sposta       						*

*********************************************************/

function azione_ftp_sposta ( $filename, $old_path ) {



		

		

		// apre la connessione ftp

		$conn_id = ftp_connect(IP_ADDR);	

	

		// login con user name e password

		$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 

	

		// controllo della connessione

		if ((!$conn_id) || (!$login_result)) { 

			echo "La connessione FTP è fallita!";

			echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 

			die; 

		

		} else {

			

			// recupera il percorso corrente

			$curr_path = WEB_PATH.$percorso;

			//echo $curr_path.'___'.$filename;



			// passa in passive mode

			ftp_pasv ( $conn_id, true );

			

			// scrive il contenuto del file

			$contenuto_file = '<?php include_once(\''.WEB_PATH.'/include/functions.inc.php\');'."\n";

			if(isset($_SESSION['PAGINA'])) $Obj = $_SESSION['PAGINA'];

			else if(isset($_SESSION['OFFERTA'])) $Obj = $_SESSION['OFFERTA'];

			if(isset($_SESSION['PID'])) $pid = $_SESSION['PID'];

			$contenuto_file .= 'view_page('.$pid.');'."\n";

			//else if(isset($_SESSION['PID'])) $contenuto_file .= 'view_page('.$_SESSION['PID'].');'."\n";

			$contenuto_file .= '?>'."\n";



			$temp = tmpfile();

			fwrite($temp, $contenuto_file);

			fseek($temp, 0);



			// si posiziona nella directory giusta

			if (!ftp_chdir($conn_id, $curr_path)) {

				 error_message("non posso cambiare dir");

				 exit;

			} 

			

			// upload del file

			if(! ($upload = ftp_fput ($conn_id, $filename, $temp ,FTP_BINARY))) {

				 error_message("errore upload del file");

				 exit;

			}

			

			// chiude l'handler del file

			fclose($temp);



			// rimuove il vecchio file

			if (!ftp_chdir($conn_id, $_SESSION['OLD_PATH'])) {

				 error_message("non posso cambiare dir");

				 exit;

			} 

			ftp_delete($conn_id, $filename);



			// chiudere il flusso FTP 

			ftp_quit($conn_id); 





	// fine else

	}



}


?>

