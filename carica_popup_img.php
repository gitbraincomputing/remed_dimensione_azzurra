<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 1;
include_once('include/function_page.php');

session_start();
//$idutente=$id;

function add_miniatura($idpaziente){
	include("image_functions.php");	
	?>
<script type="text/javascript">
//<![CDATA[

//create a preview of the selection
function preview(img, selection) { 
	//get width and height of the uploaded image.
	var current_width = $('#uploaded_image').find('#thumbnail').width();
	var current_height = $('#uploaded_image').find('#thumbnail').height();

	var scaleX = <?php echo $thumb_width;?> / selection.width; 
	var scaleY = <?php echo $thumb_height;?> / selection.height; 

	$('#uploaded_image').find('#thumbnail_preview').css({ 
		width: Math.round(scaleX * current_width) + 'px', 
		height: Math.round(scaleY * current_height) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
	$('#miniatura').show()
	$('#upload_status').show().html('<h1>selezione effettuata</h1><p>ora puoi riselezionare l\'area per la miniatura o salvare l\'immagine</p>');
	$('#upload_link').hide();
	$("#desc_foto").show();
	$(".imgareaselect-outer").hide();
	
	
	//display the hidden form
	$('#thumbnail_form').show();
} 



//show and hide the loading message
function loadingmessage(msg, show_hide){
	if(show_hide=="show"){
		$('#loader').show();
		$('#progress').show().text(msg);
		$('#uploaded_image').html('');
	}else if(show_hide=="hide"){
		$('#loader').hide();
		$('#progress').text('').hide();
	}else{
		$('#loader').hide();
		$('#progress').text('').hide();
		$('#uploaded_image').html('');
	}
}

//delete the image when the delete link is clicked.
function deleteimage(large_image, thumbnail_image){
	loadingmessage('Attendi, stiamo cancellando l immagine...', 'show');
	$.ajax({
		type: 'POST',
		url: '<?=$image_handling_file?>',
		data: 'a=delete&large_image='+large_image+'&thumbnail_image='+thumbnail_image,
		cache: false,
		success: function(response){
			loadingmessage('', 'hide');
			response = unescape(response);
			var response = response.split("|");
			var responseType = response[0];
			var responseMsg = response[1];
			if(responseType=="success"){
				$('#upload_status').show().html('<h1>immagine eliminata</h1><p>'+responseMsg+'</p>');
				$('#uploaded_image').html('');
				
				$('#upload_link').show();
				window.location.href='registrazione4.php';
				
				/*$("#box-foto").load('box_foto.php','', function(){
							 //  loading_page('loading');
							 });*/
			}else{
				$('#upload_status').show().html('<h1>Errore inaspettato</h1><p>riprova a caricare le immagini</p>'+response);
				
				$('#upload_link').show();
			}
		}
	});
}


function preferita(idfoto){
	//loadingmessage('Attendi...', 'show');
	$("#contenitore_"+idfoto).find('img').css('visibility', 'hidden');
	$.ajax({
		type: 'POST',
		url: '<?=$image_handling_file?>',
		data: 'a=preferita&idfoto='+idfoto,
		cache: false,
		success: function(response){
			loadingmessage('', 'hide');
			response = unescape(response);
			var response = response.split("|");
			var responseType = response[0];
			var responseMsg = response[1];
			if(responseType=="success"){
				$(".preferita").removeClass("preferita");
				elem = "#"+idfoto;
				$(elem).addClass("preferita");
				$("#contenitore_"+idfoto).find('img').css('visibility', 'visible');
			}else{
				//nothing
			}
		}
	});
}



$(document).ready(function () {
		$('#loader').hide();
		$('#progress').hide();
		
		$("#uploaded_image").mouseout(function(){
			$(".imgareaselect-border2").hide();
			$(".imgareaselect-border1").hide();
			$(".imgareaselect-selection").hide();	
		});
		

		var myUpload = $('#upload_link').upload({
		   name: 'image',
		   action: '<?=$image_handling_file?>',
		   enctype: 'multipart/form-data',
		   params: {upload:'Upload'},
		   autoSubmit: true,
		   onSubmit: function() {
		   		$('#upload_link').hide();
		   		$('#upload_status').html('').hide();
				loadingmessage('Attendi, stiamo caricando la tua foto...', 'show');
		   },
		   onComplete: function(response) {
		   		loadingmessage('', 'hide');
				response = unescape(response);
				var response = response.split("|");
				var responseType = response[0];
				var responseMsg = response[1];
				if(responseType=="success"){
					var current_width = response[2];
					var current_height = response[3];
					//display message that the file has been uploaded
					$('#upload_link').hide();
					$('#upload_status').show().html('<p style="font-size:13px">clicca e trascina il mouse sull\immagne per selezionare l\'area per la miniatura </p>');
					$("#desc_foto").show();
					
					//put the image in the appropriate div
					$('#uploaded_image').html('<img src="'+responseMsg+'" style="float: left; margin-right: 10px; width: <?php echo $width;?>;" id="thumbnail" alt="Create Thumbnail" /><div id="miniatura" style="display:none;"> <p style="clear:both">miniatura:</p><div style="clear:both; border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;"><img src="'+responseMsg+'" style="position: relative;" id="thumbnail_preview" alt="Thumbnail Preview" /></div></div>')
					//find the image inserted above, and allow it to be cropped
					$('#uploaded_image').find('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview }); 
					
				}else if(responseType=="error"){
					$('#upload_status').show().html('<h1>Errore</h1><p>'+responseMsg+'</p>');
					$('#uploaded_image').html('');
					$('#thumbnail_form').hide();
				}else{
					$('#upload_status').show().html('<h1>Errore inaspettato</h1><p>riprova a caricare le immagini</p>'+response);
					$('#uploaded_image').html('');
					$('#thumbnail_form').hide();
					$('#upload_link').show();
					$("#desc_foto").hide();
				}
		   }
		});
	
	//create the thumbnail
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		var descrizione_foto = $('#descrizione_foto').val();
		var idpaziente = $('#idpaziente').val();
		var idalbum = $('#idalbum').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("per proseguire seleziona l area della miniatura");
			return false;
		}else{
			//hide the selection and disable the imgareaselect plugin
			$('#uploaded_image').find('#thumbnail').imgAreaSelect({ disable: true, hide: true }); 
			loadingmessage('Attendi, stiamo memorizzando la foto....', 'show');
			$.ajax({
				type: 'POST',
				url: '<?=$image_handling_file?>',
				data: 'save_thumb=Save Thumbnail&x1='+x1+'&y1='+y1+'&x2='+x2+'&y2='+y2+'&w='+w+'&h='+h+'&descrizione_foto='+descrizione_foto+'&idpaziente='+idpaziente,
				cache: false,
				success: function(response){
					loadingmessage('', 'hide');
					response = unescape(response);
					var response = response.split("|");
					var responseType = response[0];
					var responseLargeImage = response[1];
					var responseThumbImage = response[2];
					if(responseType=="success"){
						$('#upload_status').show().html('<h1>Operazione completata!</h1><p>la foto &egrave; stata salvata!</p>');
						//load the new images
						
						$('#uploaded_image').html('');
						//hide the thumbnail form
						$('#thumbnail_form').hide();
						$('#upload_link').show();
						
						$("#foto_precedenti").css('display', "");
						// window.location.href='registrazione4.php';
						 //self.location.reload();
						 /*$("#box-foto").load('box_foto.php','', function(){
							 //  loading_page('loading');
							 });*/
						$("#desc_foto").hide();
					    $("#imgareaselect-border2").hide();
						$("#imgareaselect-border1").hide();
						$("#backgroundPopup").fadeOut("slow");
						$("#popupdati3").fadeOut("slow");
						popupStatus = 0;
						$("#miniatura_ok").html("foto caricata con successo");
						
						
					}else{
						$('#upload_status').show().html('<h1>Errore inaspettato</h1><p>riprova a caricare le immagini</p>'+response);
						//reactivate the imgareaselect plugin to allow another attempt.
						$('#uploaded_image').find('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview }); 
						$('#thumbnail_form').show();
						$('#upload_link').show();
						$("#desc_foto").hide();
					}
				}
			});
			
			return false;
		}
	});
}); 


//preferita

//]]>
</script>

	<div id="wrap">			
			<div class="page-wrap">
											
				<div class="right-side">					
				

	<noscript>per caricare nuove foto devi avere javascript attivo</noscript>

<div class="white">


	
	
	
	<div id="uploaded_image"></div>
	
	<div id="upload_status" style="clear:both"></div>


	
	<span id="loader" style="display:none; clear:both">
		<img src="loader.gif" alt="Loading..."/>
	</span> 
	
	<span id="progress"></span>
	
	
	<div id="thumbnail_form" style="display:none;">
		<form name="form" action="" method="post" class="formatta">
		<input type="hidden" id="idpaziente" name="idpaziente" value="<?=$idpaziente?>" />
			
			<!--<div class="riga_form">
			<div class="etichetta">album</div>
			<div class="mandatory">
				<select id="idalbum" name="idalbum">
				<option value="1">amici</option>
				<option value="2" selected>generico</option>
				<option value="3">i preparativi</option>
				<option value="4">il matrimonio</option>
				<option value="5">lui e lei</option>
			</select> 
			</div>	
			</div>
			
			<div class="riga_form">
			<div class="etichetta">descrizione foto</div>
			<textarea id="descrizione_foto" class="small" name="descrizione_foto"></textarea>
			</div>-->
			
			<input type="hidden" name="x1" value="" id="x1" />
			<input type="hidden" name="y1" value="" id="y1" />
			<input type="hidden" name="x2" value="" id="x2" />
			<input type="hidden" name="y2" value="" id="y2" />
			<input type="hidden" name="w" value="" id="w" />
			<input type="hidden" name="h" value="" id="h" />
			<input type="submit" name="save_thumb" value="salva questa foto" id="save_thumb" style="text-decoration: none;background: #A4A4A4;color: #FFF;font-size: 20px;text-align: center;width: 100%;height: 37px; display: block; clear: both; border: 0px; border-top: 10px solid #FFF; cursor: pointer"/>
		</form>
	</div>
	
	
	
	<div style="clear:both; float: left; width: 100%;">
		<a id="upload_link" style="text-decoration: none;background: #A4A4A4;color: #FFF;font-size: 20px;text-align: center;width: 100%;height: 27px; display: block; clear: both;">
			clicca qui per caricare una foto nuova
		</a>
	</div>
	
	
	
	
	
	<!-- -->
	
</div>
<script>
function cambia_album(foto,nuovoalbum)
{
pagina="cambia_album.php?idfoto="+foto+"&idalbum="+nuovoalbum;
		$.get(pagina,{} );
	self.location.reload();


}

</script>
					
				</div><!-- chiusura right-side -->
				
			</div><!-- chiusura page-wrap -->
			
		</div><!-- chiusura wrap -->
	<?php
}	


 

if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			case "create":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create();					
			break;

			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_REQUEST['id']);
			break;

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
		}

		
		switch($do) {

			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
			break;

			case "edit":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit($_REQUEST['id']);
			break;
			
			case "review":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else review($_REQUEST['id']);
			break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				if($_REQUEST['type']=='add')
					add_miniatura($_REQUEST['id']);
				else
					edit_miniatura($_REQUEST['id']);
			break;
		}
			html_footer();
	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>