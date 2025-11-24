<html>
<head>
	<link rel="stylesheet" type="text/css" href="css.css">
</head>
<body>
	<div class="text-center">
		<img src="logo_infocert.jpg">
<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
 	if (
		(	isset($_GET['pdf'])				&& !empty($_GET['pdf']) &&
			isset($_GET['firma_cartella'])	&& $_GET['firma_cartella'] == 1
		) ||
		(
			isset($_GET['idcartella']) 	 	 && $_GET['idcartella'] > 0 	  &&
			isset($_GET['idpaziente']) 		 && $_GET['idpaziente'] > 0 	  &&
			isset($_GET['idmodulopadre']) 	 && $_GET['idmodulopadre'] > 0 	  &&
			isset($_GET['idmoduloversione']) && $_GET['idmoduloversione'] > 0 &&
			isset($_GET['idinserimento']) 	 && $_GET['idinserimento'] > 0 	  &&
			isset($_GET['uid']) 			 && $_GET['uid'] > 0 			  &&
			isset($_GET['usephp']) 			 && $_GET['usephp']=='7' 		  &&
			isset($_GET['idregime']) 		 && $_GET['idregime'] > 0 		  &&
			isset($_GET['idimpegnativa']) 	 &&
			isset($_GET['tipoallegato'])	 && !empty($_GET['tipoallegato']) &&
			isset($_GET['allegato_successivo'])  && ($_GET['allegato_successivo'] == 0 || $_GET['allegato_successivo'] == 1) &&
			isset($_GET['p'])				 && !empty($_GET['p']) 			  &&
			isset($_GET['rtf_landscape'])  	 && ($_GET['rtf_landscape'] == 0 || $_GET['rtf_landscape'] == 1) &&
			isset($_GET['rtf'])  			 && !empty($_GET['rtf']) 
		)
	)
	{	
		$rtf = isset($_GET['rtf']) ? $_GET['rtf'] : '';
		$pdf = isset($_GET['pdf']) ? $_GET['pdf'] : '';
		
		if(empty($rtf) && empty($pdf))
			die("<h1>Errore, documento da firmare non trovato!</h1></body></html>");
		else {
			// carico le configurazioni dal file config.rdm.php generale
			$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));
			require_once($productionConfig);
			require_once 'digital_doc_sign_test.php';
			
							
			$idcartella 	  = null;
			$idpaziente 	  = null;
			$idmoduloversione = null;
			$idmodulopadre	  = null;
			$idinserimento 	  = null;
			$idregime		  = null;
			$idimpegnativa	  = null;
			$tipoallegato 	  = null;
			$allegato_successivo  = null;
			$invio_fse 		  = null;

			$firma_cartella	  = isset($_GET['firma_cartella']) ? $_GET['firma_cartella'] : 0;
			
			if($firma_cartella == 0) {
		   
				$idcartella 	  = $_GET['idcartella'];
				$idpaziente 	  = $_GET['idpaziente'];
				$idmoduloversione = $_GET['idmoduloversione'];
				$idmodulopadre	  = $_GET['idmodulopadre'];
				$idinserimento 	  = $_GET['idinserimento'];
				$idregime		  = $_GET['idregime'];
				$idimpegnativa	  = $_GET['idimpegnativa'];
				$tipoallegato 	  = $_GET['tipoallegato'];
				$allegato_successivo  = $_GET['allegato_successivo'];
			}
			
			$idoperatore 	  = $_GET['uid'];
			$pin 	  		  = $_GET['p'];
			$rtf_landscape	  = $_GET['rtf_landscape'];
				/*
			 	if($idoperatore !== '11') {
					die("<h1>MANUTENZIONE STRAORDINARIA.<br><br>Vi preghiamo di riprovare tra poco, ci scusiamo per il disturbo.</h1></body></html>");
				} 
			*/
   
			// verifico se il modulo è da inviare al Fascicolo Sanitario Elettronico
			if($firma_cartella == 0) {
				$query_check_invio_fse = "SELECT invio_fse FROM regimi_moduli WHERE idmodulo = $idmodulopadre AND idregime = $idregime";
				$result_check_invio_fse = $conn->query($query_check_invio_fse)->fetchAll();
				if (count($result_check_invio_fse) > 0)
					 $invio_fse = $result_check_invio_fse[0]['invio_fse'];
				else $invio_fse = 'n';
			}
			
			// recupero nome e alias dell'operatore collegato a ReMed
			$query_get_alias_firma = "SELECT nome, alias_firma_digitale FROM operatori WHERE uid = $idoperatore";
			$result_get_alias_firma = $conn->query($query_get_alias_firma)->fetchAll();

			$nome_operatore 			= $result_get_alias_firma[0]['nome'];
			$dominio_alias_certificato 	= $result_get_alias_firma[0]['alias_firma_digitale'];

			// se non ha un certificato, gli dico che non può firmare
			if(empty($dominio_alias_certificato)) {
				echo '<div class="div-nome-operatore">
						UTENTE: ' . $nome_operatore . '
					  </div>
					  <h1 class="text-center" style="margin-bottom:5px">Spiacente, non si dispone di una firma digitale remota Infocert.</h1>
					  <p class="text-center" style="margin-bottom:15px; font-weight: 600">Pertanto, non è possibile continuare.</p>
					  </body>
					  </html>';
				die();
			}
			// se ha un certificato, eseguo le chiamate a Infocert per iniziare lo sviluppo
			else 
			{

				// recupera token
				$auth = getOauthToken();
				//print_r($auth);
				
				if(property_exists($auth, 'access_token')) 		// errore di autenticazione
					$access_token = $auth->access_token;
				else die("<br><br>- ERRORE DI AUTENTICAZIONE - <br>".getOauthToken()->error);
				//echo "<br>TOKEN: $access_token<br>";
			
				// tutti i certificati di un utente ICSS
				//$certificatiICSS = getCertificates($access_token, "MA290786");
				//print_r($certificatiICSS);

				// tutti i certificati di un utente PKBOX
				//$certificatiPKBOX = getCertificates($access_token, $dominio_alias_certificato);
				//echo "<br><br>";
				//print_r($certificatiPKBOX);
				
				// recupera un certificato di un utente ICSS
				//$certificatiICSS = getCertificate($access_token, "MA290786", "INFOCERT+CSBMRC84E24C361Z");
				//print_r($certificatiICSS);

				// recupera un certificato di un utente PKBOX
				//$certificatiPKBOX = getCertificate($access_token, $dominio_alias_certificato, $dominio_alias_certificato);
				//echo "<br><br>";
				//print_r($certificatiPKBOX);
				
				// avvia challenge per l'utente ICSS
				//$certificatiICSS = challenge($access_token, "ICSS", "MA290786");
				//print_r($certificatiICSS);

				// avvia challenge per l'utente PKBOX
				$challenge = challenge($access_token, $dominio_alias_certificato);
				//echo "<br><br>";
				//print_r($challenge);
				
				if(!property_exists($challenge, 'transactionId'))		// errore
				{
					if(property_exists($challenge, 'error'))		$challenge_error  = $challenge->error;	else	$challenge_error = "";
					if(property_exists($challenge, 'title'))		$challenge_title  = $challenge->title;	else	$challenge_title = "";
					if(property_exists($challenge, 'detail'))		$challenge_detail = $challenge->detail; else	$challenge_detail = "";
					die("<br><br>ERRORE: ".$challenge_error."<br>".$challenge_title."<br>DETTAGLI: ".$challenge_detail);
				} else $transactionId = $challenge->transactionId;

				//echo "<br><br>TRAS: $transactionId";
?>

			<div class="div-nome-operatore">
				UTENTE: <?php echo $nome_operatore?>
			</div>
			<h1 class="text-center" id="lbl-titolo" style="margin-bottom:5px">Inserisci il codice inviato via SMS</h1>
			<p class="text-center" id="lbl-sottotitolo" style="margin-bottom:15px; font-weight: 600">Cliccando su Verifica, verrà scaricato un PDF automaticamente firmato.<br>Non saranno necessarie ulteriori azioni.</p>
			<img class="text-center" style="display: none; width: 50px" id="img-loading" src="http://192.168.210.203/remed/images/loading_big.gif">

			<div class="text-center" id="div-otp-input">
				<input class="otp text-center" type="text" value="●">
				<input class="otp text-center" type="text" value="●">
				<input class="otp text-center" type="text" value="●">
				<input class="otp text-center" type="text" value="●">
				<input class="otp text-center" type="text" value="●">
				<input class="otp text-center" type="text" value="●">
				<input class="otp text-center" type="text" value="●">
				<input class="otp text-center" type="text" value="●">
			</div>
			<p style="color:red" class="text-center" id="lbl-otp-warning"></p>
			<br>
			<a href="#" class="verify-btn" data-invio="0">VERIFICA</a>
			</p>
		</div>
		
		
		<script type="text/javascript" src="http://192.168.210.203/remed/script/jquery-1.2.6.min.js"></script>
		<script>
			$(document).ready(function()
			{
				var otp_inputs = document.querySelectorAll('.otp');
				
				// inizializza le caselle, utile per i refresh
				for (var i = 0; i < otp_inputs.length; i++) {
					otp_inputs[i].value = "●";
				}
				// gestisce l'autofocus all'input sulle caselle
				if(otp_inputs[0]){
					otp_inputs.forEach(el => {
						el.addEventListener('focus', (e) => {el.value = "";})
						el.addEventListener('input', (e) => {el.nextElementSibling ? el.nextElementSibling.focus() : el.blur();});
					});
				}
				
				$('.verify-btn').click( function() 
				{
					if( $(this).attr('data-invio') == '0')	// se non è gia' stato cliccato il tasto conferma
					{
						let otp = "";
						for (var i = 0; i < otp_inputs.length; i++) {
							otp += otp_inputs[i].value;
						}
						
						// se non è numero
						if(isNaN(otp)) {
							$('#lbl-otp-warning').text("Il codice inserito deve essere interamente numerico!");
						} else {
							// procede alla validazione
							$('#lbl-titolo').text("VALIDAZIONE IN CORSO..");
							$('#lbl-sottotitolo').text("Attendere, ci vorrà solo qualche secondo.");
							$('#img-loading').show();
							$('#lbl-otp-warning').text("");
							$('.verify-btn').attr('data-invio', '1').css('background-color', '#008ac9');
							$('.verify-btn, #div-otp-input, #lbl-richiesta-reinvio, #lbl-otp-warning').hide();
							
							var print_data;
							var firma_cartella = <?php echo $firma_cartella ?>;							
							
							if(firma_cartella == 1) {
								print_data = {
									access_token:'<?php echo $access_token?>', 
									pin:'<?php echo $pin ?>', 
									otp:otp, 
									transaction_id:'<?php echo $transactionId?>', 
									alias:'<?php echo $dominio_alias_certificato?>', 
									rtf_landscape: <?php echo $rtf_landscape ?>, 
									firma_cartella: true,
									pdf: '<?php echo $pdf ?>',
									usephp:'7'
								};
							} else {
								print_data = {
									access_token:'<?php echo $access_token?>', 
									pin:'<?php echo $pin ?>', 
									otp:otp, 
									transaction_id:'<?php echo $transactionId?>', 
									alias:'<?php echo $dominio_alias_certificato?>', 
									idcartella: '<?php echo $idcartella ?>', 
									idpaziente: '<?php echo $idpaziente ?>', 
									idmoduloversione: '<?php echo $idmoduloversione ?>', 
									idmodulopadre: '<?php echo $idmodulopadre ?>', 
									idinserimento: '<?php echo $idinserimento ?>', 
									idoperatore: '<?php echo $idoperatore ?>', 
									idregime: '<?php echo $idregime ?>', 
									idimpegnativa: '<?php echo $idimpegnativa ?>', 
									rtf: '<?php echo htmlspecialchars($rtf, ENT_QUOTES, 'UTF-8') ?>', 
									tipoallegato: '<?php echo $tipoallegato?>', 
									allegato_successivo: '<?php echo $allegato_successivo ?>', 
									invio_fse: '<?php echo $invio_fse ?>', 
									rtf_landscape: <?php echo $rtf_landscape ?>, 
									firma_cartella: false,
									usephp:'7'
								};
							}
							
							$.ajax({
								type: 'POST',
								url: 'validate_test.php',
								data: print_data,
								success: function(response)
								{
									response = JSON.parse(response);
									
									$('#lbl-titolo').html( response.titolo );
									$('#lbl-sottotitolo').html( response.sottotitolo );
									$('#img-loading').hide();
									
									if(response.esito == '0')				// FIRMA DIGITALE RIUSCITA
									{
										// parte un timer che chiudera' il popup
										let seconds = 4;
										let close = setInterval(function() {
											seconds--;
											$('#lbl-sottotitolo').text("Chiusura automatica tra " + seconds  + "..");
											
											if(seconds == 0) {
												clearInterval(close);
												window.close('','_parent','');

												var firma_cartella = <?php echo $firma_cartella ?>;
												
												if(firma_cartella == 1) {
													var filename = '<?php echo $pdf ?>';
													var path = "../modelli_word/allegato";
													
													window.open("http://192.168.210.203/firmadigitale/download_file.php?path="+path+"&filename="+filename);	
												}
											}
										}, 1000);
									}
									
								}
							});
						}
					}
				});

			});
		</script>
		
	</body>
	</html>
<?php 		}
		}
	} else die("<h1>Errore nel recupero delle informazioni.</h1></body></html>");
?>