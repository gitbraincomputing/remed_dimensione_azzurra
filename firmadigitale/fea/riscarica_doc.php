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
		
		<h1 class="text-center" id="lbl-titolo" style="margin-bottom:5px">Clicca sul pulsante per procedere</h1>
		<h3 class="text-center" id="lbl-sottotitolo" style="margin-bottom:15px; font-weight: 600"></h3>

		<div class="text-center" id="div-elenco-tablet">

<?php
	 
	/* ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); */
	
	/*https://firmabc.wbb.it/fea/riscarica_doc.php?
	idcartella=857
	&idpaziente=471
	&idmodulopadre=196
	&idmoduloversione=5769
	&idinserimento=1					// esempio URL richiamante questa pagina
	&idimpegnativa=
	&idregime=56
	&tipoallegato=allegato
	&secondoallegato=0
	&id_istanza_testata=31451
	&id_doc_confirmo_fea=476
	&uid=11
	&usephp=7
	&source=remed*/
	
 	if ( isset($_GET['source']) 	&& 	($_GET['source']=='remed' || $_GET['source']=='tc') &&
		 isset($_GET['usephp']) 	&& 	$_GET['usephp']=='7'		&&
 	     isset($_GET['uid']) 		&&
		 isset($_GET['idpaziente']) && 	$_GET['idpaziente'] > 0 )
    {

		$source 	  = $_GET['source'];
		$id_paziente  = $_GET['idpaziente'];
		$id_operatore = $_GET['uid'];
		
		if($id_operatore == "0")
			die("</div><h1>ID utente connesso non valido. Forse è scaduta la sessione?</h1></body></html>");
		
		
		
		if($source == 'remed') 
		{
			if( isset($_GET['idcartella']) 			&& 	$_GET['idcartella'] > 0				&&
				isset($_GET['idmoduloversione'])	&& 	$_GET['idmoduloversione'] > 0		&&
				isset($_GET['idmodulopadre']) 		&& 	$_GET['idmodulopadre'] > 0			&&
				isset($_GET['idinserimento']) 		&& 	$_GET['idinserimento'] > 0			&&
				isset($_GET['idregime']) 			&& 	$_GET['idregime'] > 0				&&
				isset($_GET['tipoallegato'])		&& !empty($_GET['tipoallegato'])		&&
				isset($_GET['id_doc_confirmo_fea'])	&& $_GET['id_doc_confirmo_fea'] > 0  	&&
				isset($_GET['id_istanza_testata'])	&& $_GET['id_istanza_testata'] > 0  	&&
				isset($_GET['secondoallegato']) 	&& 
					 ($_GET['secondoallegato'] == 0 || $_GET['secondoallegato'] == 1)
			   )
			{

				$id_cartella 		= $_GET['idcartella'];
				$id_modulo_versione = $_GET['idmoduloversione'];
				$id_modulo_padre 	= $_GET['idmodulopadre'];
				$id_inserimento 	= $_GET['idinserimento'];
				$id_doc_confirmo_fea = $_GET['id_doc_confirmo_fea'];
				$id_istanza_testata = $_GET['id_istanza_testata'];
				
				$id_regime 			= $_GET['idregime'];
				$id_impegnativa_tc  = "";
				
				if(isset($_GET['idimpegnativa']) && (!empty($_GET['idimpegnativa']) || $_GET['idimpegnativa'] == NULL) )
					 $id_impegnativa = $_GET['idimpegnativa'];
				else $id_impegnativa = "";
				
				$tipo_allegato 		= $_GET['tipoallegato'];
				$secondo_allegato 	= $_GET['secondoallegato'];
				$tbl = "";			// tbl per documenti TC
				
				// carico le configurazioni dal file config.rdm.php 
				require_once 'assets/config.rdm.php';
				require_once 'confirmo_digital_doc_sign.php';
				
				$query_get_nome_doc = "SELECT nome_$tipo_allegato FROM istanze_testata_firme
										WHERE 
											id_istanza_testata = $id_istanza_testata
											AND tipo_firma = 'fea'";
				//echo $query_get_nome_doc;							
				$result_get_nome_doc = $conn->query($query_get_nome_doc)->fetchAll();
				$nome_documento = $result_get_nome_doc[0]["nome_$tipo_allegato"];
				
			} 
			else die("</div><h1>Errore nel recupero delle informazioni. [1]</h1></body></html>");
		}
/*		elseif($source == 'tc') 
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
*/
		
		
		
	
		if( empty($id_doc_confirmo_fea) )
			die("</div><h1>Errore, documento da firmare non specificato!</h1></body></html>");
		

		//#######################
		// recupera token dalle API di Confirmo
		$auth = getOauthToken();
		//print_r($auth);

		if(!property_exists($auth, 'Token')) 		// errore di autenticazione
			 die("<br><br>- ERRORE DI AUTENTICAZIONE - <br>".getOauthToken()->error);
		else $access_token = $auth->Token;
		//echo "<br>TOKEN: $access_token<br>";
		
	} else die("</div><h1>Errore nel recupero delle informazioni.</h1></body></html>");
?>

			<img class="text-center" style="display: none; width: 50px" id="img-loading" src="assets/img/loading_big.gif">
			<a href="#" class="btn_recupera_file" id="btn_recupera_file">RECUPERA DOCUMENTO</a>
			<a href="#" class="btn_recupera_file" id="btn_chiudi" onclick="self.close()" style="display:none">CHIUDI</a>

		</div>

	</div>
	
	
	<script>
		$(document).ready(function()
		{
			var clicked = false;
			
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
							id_documento:<?php echo $id_doc_confirmo_fea ?>,
							nome_documento:'<?php echo $nome_documento ?>',
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
					
					clicked = false;
				}
			});
		});
	</script>
</body>
</html>