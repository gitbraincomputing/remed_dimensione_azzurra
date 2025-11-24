<html>
<head>
	<link rel="stylesheet" type="text/css" href="assets/css.css">
	<script type="text/javascript" src="assets/jquery-1.2.6.min.js"></script>
</head>
<body>
	<div class="text-center">
		<img class="img-header" src="assets/img/header_fea.png">
		<br><br>
		Firma Elettronica Avanzata Grafometrica
		
		<h1 class="text-center" id="lbl-titolo" style="margin-bottom:5px">Seleziona il Tablet da utilizzare</h1>
		<h3 class="text-center" id="lbl-sottotitolo" style="margin-bottom:15px; font-weight: 600">Alla selezione, il dispositivo sarà risvegliato automaticamente ed il documento inviato per la firma.</h3>

		<div class="text-center" id="div-elenco-tablet">

<?php
	 
	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/
	
 	if ( isset($_GET['source']) 	&& 	($_GET['source']=='remed' || $_GET['source']=='tc') &&
		 isset($_GET['usephp']) 	&& 	$_GET['usephp']=='7'		&&
 	    (isset($_GET['rtf']) 		|| 	isset($_GET['pdf']))		&&
 	     isset($_GET['uid']) 		&&
		isset($_GET['idpaziente']) 	&& 	$_GET['idpaziente'] > 0
	   )
   {

		$source 	  = $_GET['source'];
		$id_paziente  = $_GET['idpaziente'];
		$id_operatore = $_GET['uid'];
		
		if($id_operatore == "0")
			die("</div><h1>ID utente connesso non valido. Forse è scaduta la sessione?</h1></body></html>");
		
		
		
		if($source == 'remed') 
		{
			if( isset($_GET['idcartella']) 		&& 	$_GET['idcartella'] > 0			&&
				isset($_GET['idmoduloversione'])&& 	$_GET['idmoduloversione'] > 0	&&
				isset($_GET['idmodulopadre']) 	&& 	$_GET['idmodulopadre'] > 0		&&
				isset($_GET['idinserimento']) 	&& 	$_GET['idinserimento'] > 0		&&
				isset($_GET['idregime']) 		&& 	$_GET['idregime'] > 0			&&
				isset($_GET['tipoallegato'])	&& !empty($_GET['tipoallegato']) 	&&
				isset($_GET['secondoallegato']) && 
					 ($_GET['secondoallegato'] == 0 || $_GET['secondoallegato'] == 1)
			   )
			{
								
				$id_cartella 		= $_GET['idcartella'];
				$id_modulo_versione = $_GET['idmoduloversione'];
				$id_modulo_padre 	= $_GET['idmodulopadre'];
				$id_inserimento 	= $_GET['idinserimento'];
				
				$id_regime 			= $_GET['idregime'];
				$id_impegnativa_tc  = "";
				
				if(isset($_GET['idimpegnativa']) && (!empty($_GET['idimpegnativa']) || $_GET['idimpegnativa'] == NULL) )
					 $id_impegnativa = $_GET['idimpegnativa'];
				else $id_impegnativa = "";
				
				$tipo_allegato 		= $_GET['tipoallegato'];
				$secondo_allegato 	= $_GET['secondoallegato'];
				$tbl = "";			// tbl per documenti TC
			} 
			else die("</div><h1>Errore nel recupero delle informazioni. [1]</h1></body></html>");
		}
		elseif($source == 'tc') 
		{
			$id_cartella = "";
			$id_modulo_versione = $id_modulo_padre = $id_inserimento = "";
			$id_regime = $id_impegnativa = "";
			$tipo_allegato = $secondo_allegato = "";
			
			if(isset($_GET['idimpegnativa_tc']) && (!empty($_GET['idimpegnativa_tc']) || $_GET['idimpegnativa_tc'] == NULL) )
				 $id_impegnativa_tc = $_GET['idimpegnativa_tc'];
			else $id_impegnativa_tc = "";
			
			$tbl = $_GET['tbl'];
		}
		
		
		
		if( isset($_GET['rtf']) && !empty($_GET['rtf']) )
			 $rtf = $_GET['rtf'];
		else $rtf = "";
		
		if( isset($_GET['pdf']) && !empty($_GET['pdf']) )
			 $pdf = $_GET['pdf'];
		else $pdf = "";
		
		
			if(!empty($rtf) &&  empty($pdf))			$file_type = 'rtf';
		elseif( empty($rtf) && !empty($pdf))			$file_type = 'pdf';
		elseif( empty($rtf) &&  empty($pdf))			die("</div><h1>Errore, documento da firmare non specificato!</h1></body></html>");
		
		// carico le configurazioni dal file config.rdm.php 
		require_once 'assets/config.rdm.php';
		require_once 'confirmo_digital_doc_sign.php';

		//#######################
		// recupera token dalle API di Confirmo
		$auth = getOauthToken();
		//print_r($auth);

		if(!property_exists($auth, 'Token')) 		// errore di autenticazione
			 die("<br><br>- ERRORE DI AUTENTICAZIONE - <br>".getOauthToken()->error);
		else $access_token = $auth->Token;
		//echo "<br>TOKEN: $access_token<br>";
	
	
		//#######################
		// recupero l'elenco dei tablet disponibili
		$tabletList = getTabletList($access_token);

		if(!property_exists($tabletList[0], 'id')) 		// errore - nessun tablet
			die("<h3>Non risultano tablet associati a questa utenza oppure si &egrave; verificato un errore.</h3>".getOauthToken()->error);
		else {
			foreach($tabletList as $tablet) {
				echo '<div class="div-tablet" data-device-name="'.$tablet->label.'" data-device-id="'.$tablet->id.'"
						data-id-paziente="'.$id_paziente.'" data-id-cartella="'.$id_cartella.'">
						<img class="img-tablet" src="assets/img/tablet.png">
						<h3>'.$tablet->label.'</h3>
					  </div>';
			}
			
			echo '<img class="text-center" style="display: none; width: 50px" id="img-loading" src="assets/img/loading_big.gif">
				  <a href="#" class="btn_recupera_file" id="btn_recupera_file" style="display:none">RECUPERA E ASSOCIA AL PAZIENTE</a>
				  <a href="#" class="btn_recupera_file" id="btn_chiudi" onclick="self.close()" style="display:none">CHIUDI</a>';
				
		}

		
	} else die("</div><h1>Errore nel recupero delle informazioni.</h1></body></html>");
?>

		</div>
		<p style="color:red" class="text-center" id="lbl_warning"></p>

	</div>
	
	
	<script>
		$(document).ready(function()
		{
			var clicked = false;
			
			// scelta tablet su cui inviare il doc.
			$('.div-tablet').click( function() 
			{
				if(!clicked)
				{
					clicked = true;
					var device_id 	= $(this).attr('data-device-id');
					var device_name = $(this).attr('data-device-name');
					var id_paziente = $(this).attr('data-id-paziente');
					var id_cartella = $(this).attr('data-id-cartella');
					
	
					if(isNaN(device_id)) {
						$('#lbl_warning').text("ID tablet non valido!").show();
					} else {
						// procede alla validazione
						$('#lbl-titolo').text("ELABORAZIONE DOCUMENTO ED INVIO A \"" + device_name + "\"..");
						$('#lbl-sottotitolo').text("Attendere, ci vorrà solo qualche secondo.");
						$('#lbl_warning').text("").hide();
						$('.div-tablet').hide();
						$('#img-loading').fadeIn();
						
						$.ajax({
							type: 'POST',
							url: 'invia_doc.php',
							 data: {
								access_token:'<?php echo $access_token?>', 
								idpaziente: <?php echo $id_paziente ?>, 
								idcartella: '<?php echo $id_cartella ?>'	, 
								idmoduloversione: '<?php echo $id_modulo_versione ?>', 
								idmodulopadre: '<?php echo $id_modulo_padre ?>', 
								idinserimento: '<?php echo $id_inserimento ?>',
								uid: <?php echo $id_operatore ?>, 
								idregime: '<?php echo $id_regime ?>', 
								idimpegnativa: '<?php echo $id_impegnativa ?>', 
								rtf:'<?php echo str_replace("\\", "\\\\", $rtf) ?>', 
								pdf:'<?php echo str_replace("\\", "\\\\", $pdf) ?>', 
								tipoallegato:'<?php echo $tipo_allegato?>', 
								secondoallegato: '<?php echo $secondo_allegato ?>', 
								device_id:device_id,
								source:'<?php echo $source ?>',
								usephp:'7'},
							success: function(response)
							{
								response = JSON.parse(response);
								
								$('#lbl-titolo').html( response.titolo );
								$('#lbl-sottotitolo').html( response.sottotitolo );
								$('#img-loading').hide();
								
								if(response.esito == "0") {				// invio OK
									$('#btn_recupera_file').attr('data-id-doc', response.id_doc );
									$('#btn_recupera_file').attr('data-nome-doc', response.nome_file ).fadeIn();
								} else {								// invio KO
									$('.div-tablet').fadeIn();
								}
							}
						});
						clicked = false;
					}
				}
			});
			
			
			
			// btn per recuperare il PDF dai server di Confirmo e associarlo al paziente
			$('#btn_recupera_file').click( function() 
			{
				if(!clicked)
				{
					clicked = true;

					$('#lbl-titolo').text("RECUPERO DOCUMENTO IN CORSO..");
					$('#lbl-sottotitolo').text("Attendere, ci vorrà solo qualche secondo.");
					$('#img-loading').show();
					$('#lbl_warning').text("").hide();
					$('#btn_recupera_file').fadeOut();
					
					let id_documento   = $('#btn_recupera_file').attr('data-id-doc');
					let nome_documento = $('#btn_recupera_file').attr('data-nome-doc');
					if(id_documento !== "")
					{
						$.ajax({
							type: 'POST',
							url: 'scarica_doc.php',
							data: {
								access_token:'<?php echo $access_token?>', 
								idpaziente: <?php echo $id_paziente ?>, 
								idcartella: '<?php echo $id_cartella ?>', 
								idmoduloversione: '<?php echo $id_modulo_versione ?>', 
								idmodulopadre: '<?php echo $id_modulo_padre ?>', 
								idinserimento: '<?php echo $id_inserimento ?>',
								uid: <?php echo $id_operatore ?>, 
								idregime: '<?php echo $id_regime ?>', 
								idimpegnativa: '<?php echo $id_impegnativa ?>', 
								idimpegnativa_tc: '<?php echo $id_impegnativa_tc ?>', 
								tipoallegato:'<?php echo $tipo_allegato?>', 
								secondoallegato:'<?php echo $secondo_allegato ?>', 
								id_documento:id_documento,
								nome_documento:nome_documento,
								usephp:'7',
								source:'<?php echo $source ?>',
								tbl:'<?php echo $tbl ?>'
							},
							success: function(response)
							{
								response = JSON.parse(response);
								
								$('#lbl-titolo').html( response.titolo );
								$('#lbl-sottotitolo').html( response.sottotitolo );
								$('#img-loading').hide();
								
								if(response.esito == "0") {				// invio OK
									$('#lbl-titolo').html( response.titolo );
									$('#lbl-sottotitolo').html( response.sottotitolo );
									$('#img-loading').hide();
									$('#btn_chiudi').fadeIn();
								} else {								// invio KO
									$('#btn_recupera_file').fadeIn();
								}
							}
						});
					}
					clicked = false;
				}
			});
		});
	</script>
</body>
</html>