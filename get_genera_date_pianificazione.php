<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
include_once('include/function_page.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=VERSIONE?> / gestionale aziende sanitarie</title>
<link rel="Shortcut Icon" href="favicon.ico">
<?
srand(time());
$random = rand();
?>
<!--<script language="javascript" type="text/javascript" src="script/niceforms.js"></script>-->
<script type="text/javascript" src="include/code2.js?random=<?=$random?>"></script>


<script type="text/javascript" src="script/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="script/ddaccordion.js"></script>
<script type="text/javascript" src="script/jquery.tablednd_0_5.js"></script>
<script type="text/javascript" src="script/jquery.validation.js"></script>
<!--<script type="text/javascript" src="script/jquery.dropshadow.js"></script>-->

<script type="text/javascript" src="script/jquery.corners.min.js"></script>
<script type="text/javascript" src="script/jquery.flexbox.min.js"></script>
<script type="text/javascript" src="script/jquery.tabs.min.js"></script>

<script type="text/javascript" src="script/flexigrid.js"></script>
<script type="text/javascript" src="script/jquery.maskedinput-1.1.4.js"></script>
<script type="text/javascript" src="script/jquery.form.js"></script> 
<script type="text/javascript" src="script/jquery_autocomplete.js"></script> 

<script type="text/javascript" src="script/jquery.tablesorter/tablesorter/jquery.tablesorter.js"></script>
<script type="text/javascript" src="script/jquery.tablesorter/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>

<script type="text/javascript" src="script/flexigrid.js"></script>
<script type="text/javascript" src="script/jquery.imgareaselect.min.js"></script>
<script type="text/javascript" src="script/jquery.ocupload-packed.js"></script>



<script type="text/javascript" src="script/general.js?business=<?=$buiseness_require?>&random=<?=$random?>"></script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
		$.mask.addPlaceholder('~',"[+-]");	
		$(".campo_data").mask("99/99/9999");				
			
		if ( $('#elenco-date').val() !== "")	// sto aprendo il popup con date inserite in precedenza, quindi le mostro a video
		{
		
			var arr_elenco_date = $('#elenco-date').val().split("|");
			var arr_elenco_date_length = arr_elenco_date.length;
			
			if ( arr_elenco_date[0].split("_")[0] == $('#data_inizio').val() ) 	// controllo se si cambia la data di inizio nella finestra pianificazione dopo aver generato già delle date
			{
				$('#thead_elenco_date').show();
				$('#tbody_elenco_date').html("");
				var options = {'weekday': 'long'};
				
				for(i=0; i<arr_elenco_date_length; i++)
				{
					var data = new Date(arr_elenco_date[i].split("_")[0]);
					var scadenza = arr_elenco_date[i].split("_")[1];
					var compilato = arr_elenco_date[i].split("_")[2];
					var data_compilazione = arr_elenco_date[i].split("_")[3];
					if(data_compilazione !== "") {
						dataagg = new Date(data_compilazione);
						if (dataagg.getDate()  	   < 10)		var dataagg_giorno = '0' +  dataagg.getDate();			else	var dataagg_giorno = dataagg.getDate();
						if ((dataagg.getMonth() + 1) < 10)		var dataagg_mese   = '0' + (dataagg.getMonth() + 1);	else	var dataagg_mese   = (dataagg.getMonth() + 1);
						var dataagg_anno = data.getFullYear();
						data_compilazione = dataagg_giorno + "-" + dataagg_mese + "-" + dataagg_anno;
					}
					
					if (data.getDate()  	   < 10)	var giorno = '0' +  data.getDate();			else	var giorno = data.getDate();
					if ((data.getMonth() + 1) < 10)		var mese   = '0' + (data.getMonth() + 1);	else	var mese   = (data.getMonth() + 1);
					var anno = data.getFullYear();

					var nome_giorno = data.toLocaleString('it-IT', options);
					if( nome_giorno == "sabato" || nome_giorno == "domenica")
						 var giorno_festivo = ' style="color:red; font-weight:800"';
					else var giorno_festivo = ' style="font-weight:400"';

					var readonly = "";
					var alert_compilato = '<span name="span_compilato" data-indice="' + i + '" style="padding: 1px 4px; background-color: #b5b5b5; cursor: pointer; border-radius: 20px; color: white; font-weight: 800">NO</span>';

						
					if(compilato == 1) {
						var readonly = " readonly";
						var alert_compilato = '<span name="span_compilato" data-indice="' + i + '" style="padding: 1px 7px; background-color: #00d900; cursor: pointer; border-radius: 20px; color: white; font-weight: 800">SI</span>';
					}
					
					
					var campo_scadenza = '<input name="campo_scadenza" id="campo_scadenza_' + i + '" data-indice="' + i + '" data-scadenza-originale="' + scadenza + '" type="number" min="1" value="' + scadenza + '" style="width:50px" ' + readonly +'>';
					
					
					
					$('#tbody_elenco_date').append( '<tr id="riga_' + i + '">\
														<td style="text-align:center" name="td_elimina_data"><a style="cursor: pointer"><img name="btn_elimina_data" data-indice="' + i + '" src="images/remove.png"></a></td>\
														<td style="text-align:right" id="td_nome_giorno_' + i + '"><span '+ giorno_festivo + '>' + nome_giorno.toUpperCase() + ' </span></td>\
														<td style="text-align:center"><input name="campo_data" id="campo_data_' + i + '" data-indice="' + i + '" type="date" value="' + anno + '-' + mese + '-' + giorno + '" ' + readonly + '></td>\
														<td style="text-align:center">' + campo_scadenza + '</td>\
														<td style="text-align:center" name="td_compilato" id="td_compilato_' + i + '" data-compilato="' + compilato + '">' + alert_compilato + '</td>\
														<td style="text-align:center">' + data_compilazione + '</td>\
													  </tr>');
					
					if(i == 240) {
						alert('Raggiunto il limite di date generabili, data massima disponibile: ' + giorno + '/' + mese + '/' + anno);
						$("#data_fine_impegnativa").val(anno + '-' + mese + '-' + giorno);
						break;
					}
				}
				$('.aggiungi_left').attr('data-max-i', i);
				
				
			} else {
				alert('La data di inizio (' + $('#data_inizio').val() + ') risulta differente da quella memorizzata in precedenza (' + arr_elenco_date[0].split("_")[0] + ').\n\rDi conseguenza i calcoli non sono più validi. Per riprendere le date memorizzate, reimpostare la data di inizio come precedentemente definito oppure generare nuove date.');
			}
		}
						
			
		$("#btn_genera_date").click( function() 
		{
			if ( $('#tbody_elenco_date').html().trim() !== "") {
				if (confirm('Sei sicuro di voler sovrascrivere le date già create?')) {
					var continua = 1;
				} else {
				  var continua = 0;
				}
			} else var continua = 1;
			
			if(continua == 1)
			{
				var data_inizio = $("#data_inizio").val();
				var scadenza = $("#scadenza").val();
				var data_fine_impegnativa = $("#data_fine_impegnativa").val();
				
				if(data_fine_impegnativa == "")	{
					alert("Devi prima inserire una data.");
				} else if( new Date(data_inizio) >= new Date(data_fine_impegnativa) ) {
					alert("La data di fine deve essere maggiore della data di inizio.");
				} else {
					
					$('#thead_elenco_date').show();
					$('#tbody_elenco_date').html("");
					var options = {'weekday': 'long'};
					var result = data_inizio;
					var i = 0;
					var count_dates = 0;	// contatore per gestire il numero massimo di date ma poter inserire nel popup (240 date)

					while ( new Date(result) < new Date(data_fine_impegnativa) )
					{
						var result = genera_date(data_inizio, result, parseInt(scadenza));
						if ( new Date(result) < new Date(data_fine_impegnativa) ) 
						{
							if (result.getDate()  	   < 10)		var giorno = '0' +  result.getDate();			else	var giorno = result.getDate();
							if ((result.getMonth() + 1) < 10)		var mese   = '0' + (result.getMonth() + 1);		else	var mese   = (result.getMonth() + 1);
							var anno = result.getFullYear();
							
							if(count_dates == 240) {
								alert('Raggiunto il limite di date generabili, data massima disponibile: ' + giorno + '/' + mese + '/' + anno);
								$("#data_fine_impegnativa").val(anno + '-' + mese + '-' + giorno);
								break;
							}
							
							var nome_giorno = result.toLocaleString('it-IT', options);
							if( nome_giorno == "sabato" || nome_giorno == "domenica")
								 var giorno_festivo = ' style="color:red; font-weight:800"';
							else var giorno_festivo = ' style="font-weight:400"';
							
							if (i == 0)	{
								var campo_scadenza = '<input name="campo_scadenza" id="campo_scadenza_' + i + '" data-indice="' + i + '" type="number" min="1" value="0" style="width:50px" disabled>';	
								var readonly = " readonly";
							} else {
								var campo_scadenza = '<input name="campo_scadenza" id="campo_scadenza_' + i + '" data-indice="' + i + '" data-scadenza-originale="' + scadenza + '" type="number" min="1" value="' + scadenza + '" style="width:50px">';
								var readonly = "";
							}
							
							$('#tbody_elenco_date').append( '<tr id="riga_' + i + '">\
																<td style="text-align:center" name="td_elimina_data"><a style="cursor: pointer"><img name="btn_elimina_data" data-indice="' + i + '" src="images/remove.png"></a></td>\
																<td style="text-align:right" id="td_nome_giorno_' + i + '"><span '+ giorno_festivo + '>' + nome_giorno.toUpperCase() + ' </span></td>\
																<td style="text-align:center"><input name="campo_data" id="campo_data_' + i + '" data-indice="' + i + '" type="date" value="' + anno + '-' + mese + '-' + giorno + '" ' + readonly + '></td>\
																<td style="text-align:center">' + campo_scadenza + '</td>\
																<td style="text-align:center" name="td_compilato" id="td_compilato_' + i + '" data-compilato="0"><span name="span_compilato" data-indice="' + i + '" style="padding: 1px 4px; background-color: #b5b5b5; cursor: pointer; border-radius: 20px; color: white; font-weight: 800">NO</span></td>\
																<td style="text-align:center"></td>\
															  </tr>');
							
							i++;
							count_dates++;							
						}
					}
					$('.aggiungi_left').attr('data-max-i', i);
				}
			}
		});
		
		
		// $(document).on('change','[name="campo_data"]',function(){			NON SUPPORTATO IN QST VERSIONE DI JQUERY
		document.addEventListener('change', function(e) {
			if(e.target && e.target.name == 'campo_data')
			{
				var i = e.target.getAttribute('data-indice'); //$(this).attr('data-indice');
				var data 			= $('#campo_data_' + i).val();
				var scadenza 		= $('#campo_scadenza_' + i).val();
				var result 			= new Date(data);
				
				if (data !== "")	// gestisco la X del browser che cancella il campo data
				{
					if (result.getDate()  	    < 10)		var giorno = '0' +  result.getDate();			else	var giorno = result.getDate();
					if ((result.getMonth() + 1) < 10)		var mese   = '0' + (result.getMonth() + 1);		else	var mese   = (result.getMonth() + 1);
					var anno = result.getFullYear();
					
					var options = {'weekday': 'long'};
					var nome_giorno = result.toLocaleString('it-IT', options);
					if( nome_giorno == "sabato" || nome_giorno == "domenica")
						 var giorno_festivo = ' style="color:red; font-weight:800"';
					else var giorno_festivo = ' style="font-weight:400"';
					
					$('#td_nome_giorno_' + i).html('<span '+ giorno_festivo + '>' + nome_giorno.toUpperCase() + ' </span>');
					$('#campo_data_' + i).val(anno + '-' + mese + '-' + giorno);
					
					if (i > 0) 	// se non sto cambiando la prima data
					{						
						var data			 = $('#campo_data_' + i).val();
						
						//var data_precedente  = $('#campo_data_' + (parseInt(i)-1) ).val();
						var prev_i = parseInt(i)-1;
						var data_precedente  = $('#campo_data_' + prev_i ).val();
						while(data_precedente == undefined) {
							prev_i--;
							data_precedente  = $('#campo_data_' + prev_i ).val();
						}
							
						//var data_successiva  = $('#campo_data_' + (parseInt(i)+1) ).val();
						var last_indice = $('#tbody_elenco_date > tr:last > td:nth-child(4) > [name="campo_scadenza"]').attr('data-indice');
						
						//alert(i + " < " + last_indice);
						if(parseInt(i) < parseInt(last_indice) )
						{
							var next_i = parseInt(i)+1;
							var data_successiva  = $('#campo_data_' + next_i ).val();
							while(data_successiva == undefined) {
								next_i++;
								data_successiva  = $('#campo_data_' + next_i ).val();
							}
								
								
							const diffInDaysPrec = (new Date(data) - new Date(data_precedente)) / (1000 * 60 * 60 * 24);
							var diffInDaysSucc   = (new Date(data_successiva) - new Date(data)) / (1000 * 60 * 60 * 24);

							$('#campo_scadenza_' + i).val( diffInDaysPrec );
							
							// ricalcolo i giorni della scadenza della data successiva
							var scadenza_campo_succ =  parseInt($('#campo_scadenza_' + next_i ).attr('data-scadenza-originale') );
							if( $('#campo_scadenza_' + next_i ).length) {
								$('#campo_scadenza_' + next_i ).val( diffInDaysSucc );
							}
						}
					}	
				} else {
					$('#td_nome_giorno_' + i).html('<span style="color:black; font-weight:800">DATA NON VALIDA</span>');
					$('#campo_scadenza_' + i).val( "" );
				}
			}
		});

		var eventList = ["keyup", "paste", "input"];
		for(event of eventList) {
			document.addEventListener(event, function(e) {
				if(e.target && e.target.name == 'campo_scadenza')
				{
					var i = e.target.getAttribute('data-indice');
					
					var data 			= $('#campo_data_' + i).val();
					var scadenza 		= $('#campo_scadenza_' + i).val();
					
					var prev_i = parseInt(i)-1;
					var data_precedente  = $('#campo_data_' + prev_i ).val();
					while(data_precedente == undefined) {
						prev_i--;
						
						data_precedente  = $('#campo_data_' + prev_i ).val();
					}
					//var data_precedente = $('#campo_data_' + (i-1)).val();
						
					if (data_precedente !== "")	// gestisco la X del browser che cancella il campo data
					{
						var result = new Date(data_precedente);
						result.setDate(result.getDate() + parseInt(scadenza));
						
						if (result.getDate()  	    < 10)		var giorno = '0' +  result.getDate();			else	var giorno = result.getDate();
						if ((result.getMonth() + 1) < 10)		var mese   = '0' + (result.getMonth() + 1);		else	var mese   = (result.getMonth() + 1);
						var anno = result.getFullYear();
						
						var options = {'weekday': 'long'};
						var nome_giorno = result.toLocaleString('it-IT', options);
						if( nome_giorno == "sabato" || nome_giorno == "domenica")
							 var giorno_festivo = ' style="color:red; font-weight:800"';
						else var giorno_festivo = ' style="font-weight:400"';
						
						$('#td_nome_giorno_' + i).html('<span '+ giorno_festivo + '>' + nome_giorno.toUpperCase() + ' </span>');
						$('#campo_data_' + i).val(anno + '-' + mese + '-' + giorno);
						
					
						
						if(i > 0)
						{
							
							var data			 = $('#campo_data_' + i).val();
							
							var prev_i = parseInt(i)-1;
							var data_precedente  = $('#campo_data_' + prev_i ).val();
							while(data_precedente == undefined) {
								//alert($('#campo_data_' + prev_i ).attr('id') + " | " + data_precedente + " | " + prev_i);
								prev_i--;
								data_precedente  = $('#campo_data_' + prev_i ).val();
							}
							//alert($('#campo_data_' + prev_i ).attr('id') + " | P: " + data_precedente + " | " + prev_i);
							
							// prendo l'indice della ultima riga presente
							var last_indice = $('#tbody_elenco_date > tr:last > td:nth-child(4) > [name="campo_scadenza"]').attr('data-indice');
							
							//alert(i + " < " + last_indice);
							if(parseInt(i) < parseInt(last_indice) )
							{
								var next_i = parseInt(i)+1;
								var data_successiva  = $('#campo_data_' + next_i ).val();
								while(data_successiva == undefined) {
									//alert($('#campo_data_' + next_i ).attr('id') + " | " + data_successiva + " | " + next_i);
									next_i++;
									data_successiva = $('#campo_data_' + next_i ).val();
								}
								//alert($('#campo_data_' + next_i ).attr('id') + " | S: " + data_successiva + " | " + next_i);
								
								
								const diffInDaysPrec = (new Date(data) - new Date(data_precedente)) / (1000 * 60 * 60 * 24);
								var diffInDaysSucc   = (new Date(data_successiva) - new Date(data)) / (1000 * 60 * 60 * 24);							
								$('#campo_scadenza_' + i).val( diffInDaysPrec );
								
								// ricalcolo i giorni della scadenza della data successiva
								var scadenza_campo_succ =  parseInt($('#campo_scadenza_' + next_i ).val() );
								if( $('#campo_scadenza_' + next_i ).length) {
									$('#campo_scadenza_' + next_i ).val( diffInDaysSucc );
								}
							}
						}
						
					} else {
						$('#td_nome_giorno_' + i).html('<span style="color:black; font-weight:800">DATA NON VALIDA</span>');
						$('#campo_data_' + i).val( "" );
						$('#campo_scadenza_' + i).val( "" );
					}

				}
				
			});
		}
		
		//$("[name='td_compilato']").click( function() {
		document.addEventListener('click', function(e) 	// toogle per compilato SI/NO
		{
			if(e.target && e.target.getAttribute('name') == 'span_compilato') {
				var stato_compilato = e.target.innerText;
				var indice = e.target.getAttribute('data-indice');
				if(stato_compilato == 'NO')
				{
					document.getElementById("td_compilato_" + indice).setAttribute("data-compilato", 1); 
					e.target.outerHTML = '<span name="span_compilato" data-indice="' + indice + '" style="padding: 1px 7px; background-color: #00d900; cursor: pointer; border-radius: 20px; color: white; font-weight: 800">SI</span>';
				} else if(stato_compilato == 'SI') {
					document.getElementById("td_compilato_" + indice).setAttribute("data-compilato", 0); 
					e.target.outerHTML = '<span name="span_compilato" data-indice="' + indice + '" style="padding: 1px 4px; background-color: #b5b5b5; cursor: pointer; border-radius: 20px; color: white; font-weight: 800">NO</span>';
				}
			}
		});
		
		document.addEventListener('click', function(e) 	// clic su btn elimina data
		{
			if(e.target && e.target.getAttribute('name') == 'btn_elimina_data') 
			{
				var indice = e.target.getAttribute('data-indice');
				var indice_next = parseInt(indice) + 1;
				
				if (confirm('Sei sicuro di voler cancellare questa data?')) 
				{
					if (indice_next < ($('#tbody_elenco_date tr').length - 1)) {
						var giorni_da_somm = $('#campo_scadenza_' + indice ).val();
						var giorni_campo_succ = $('#campo_scadenza_' + indice_next ).val();
						$('#campo_scadenza_' + indice_next ).val( parseInt(giorni_da_somm) + parseInt(giorni_campo_succ) );
					}
					
				
					var first_indice = $('#tbody_elenco_date > tr:first > td:nth-child(4) > [name="campo_scadenza"]').attr('data-indice');
					if(indice == first_indice ) {
						$('#data_inizio').val(	$('#campo_data_' + indice_next).val() );
						$('#campo_scadenza_' + indice_next).val(0);
					}
					
					$('#riga_' + indice ).remove();
				} 
			}
		});
		

	});	
	
	function genera_date(data_inizio, date, days) 
	{
		var result = new Date(date);
		if (date !== data_inizio) result.setDate(result.getDate() + days);		//IF necessario per visualizzare la data di inizio senza i giorni sommati
		return result;
	}

	
	function addSelectedItemsToParent(act) 
	{
		if(act == "mem")
		{
			var elenco_date_scadenze = "";
			var data_inizio = new Date($('#data_inizio').val() ) ;
			if (data_inizio.getDate()  	     < 10)		var giorno = '0' +  data_inizio.getDate();			else	var giorno = data_inizio.getDate();
			if ((data_inizio.getMonth() + 1) < 10)		var mese   = '0' + (data_inizio.getMonth() + 1);	else	var mese   = (data_inizio.getMonth() + 1);
			var anno = data_inizio.getFullYear();
			data_inizio = giorno + "/" + mese + "/" + anno;
							
			var last_indice = $('#tbody_elenco_date > tr:last > td:nth-child(4) > [name="campo_scadenza"]').attr('data-indice');
			
			if(last_indice > 0)	// è presente almeno 1 data
			{
				for(i=0; i < (last_indice + 1); i++) {
					if( $('#riga_' + i).length )
						elenco_date_scadenze += $('#campo_data_'+i).val() + "_" + $('#campo_scadenza_'+i).val() + "_" + $('#td_compilato_'+i).attr('data-compilato') + "_|";
				}
				elenco_date_scadenze = elenco_date_scadenze.slice(0, -1);
				self.opener.addToParentList(document.getElementById('sorgente').value, elenco_date_scadenze, data_inizio, document.getElementById('tipo').value );
				window.close();
			} else alert("Date non generate");
		} else {
			self.opener.addToParentList(document.getElementById('sorgente').value, '', data_inizio, document.getElementById('tipo').value );
			window.close();
		}

	}
</script>
 
 
<? $validation=true;
 if($validation==true){ ?>
  <script src="M_Validation/M_global.js?version=4" type="text/javascript">/**/</script>
<? } ?>
<link href="style/flexigrid.css" rel="stylesheet" type="text/css" />
<link href="style/new_ui.css?random=<?=$random?>" rel="stylesheet" type="text/css" />
<link href="style/new_ui_print.css?random=<?=$random?>" rel="stylesheet" type="text/css" media="print" />


</head>

<body onResize="javascript:ridimensiona();" onload="javascript:visualizzamenu();$('#valore_id').focus();">
<!-- loading div tab -->
<div id="layer_nero2" class="layer_nero_background">
	<div id="loading_big_loading"><img src="images/ajax-loader.gif" alt="attendere prego, salvataggio informazioni in corso" /></div>
</div>
<!-- loading div -->

<div id="layer_nero" class="layer_nero_background">
	<div id="loading_big"><img src="images/ajax-loader.gif" alt="attendere prego, salvataggio informazioni in corso" /></div>
    <div id="loading_big_ok">&nbsp;</div>
    <div id="loading_big_no">&nbsp;</div>
</div>
<!-- loading div -->

<div style="width:480px !important; display:block">
<div>
<?
if(isset($_SESSION['UTENTE'])) {

	?>

	<div id="menu" style="display:none;"></div>

		
	<div class="titolo_pag">
		<h1>Genera date automaticamente</h1>
	</div>


	<div class="blocco_centralcat">
		<div class="form">	
			<form name="form0" action="re_pazienti_sanitaria_POST.php" method="post" id="myForm" enctype="multipart/form-data"  style="margin: 10px">
			
				<input type="hidden" value="<?=$_REQUEST['sorgente']?>" name="sorgente" id="sorgente">
				<input type="hidden" value="<?=$_REQUEST['tipo']?>" name="tipo" id="tipo">
				<input type="hidden" value="<?=$_REQUEST['idmodulo']?>" name="idmodulo" id="idmodulo">
				<input type="hidden" value="<?=$_REQUEST['idimpegnativa']?>" name="idimpegnativa" id="idimpegnativa">
				<input type="hidden" value="<?=$_REQUEST['scadenza']?>" name="scadenza" id="scadenza">
				<input type="hidden" value="<?=$_REQUEST['elenco-date']?>" name="elenco-date" id="elenco-date">
				<input type="hidden" value="" name="action"/>
				<? 
					$data_inizio = $_REQUEST['data'];
					$data_inizio = str_replace('/', '-', $data_inizio);
				?>		
				Data di inizio: <input type="date" value="<?=date('Y-m-d', strtotime($data_inizio))?>" name="data_inizio" id="data_inizio" readonly/><br><br>
				Data di fine:   <input type="date" value="<?=date('Y-m-d',strtotime("+5 year", strtotime($data_inizio)))?>" name="data_fine_impegnativa" id="data_fine_impegnativa" style="margin-left:8px"/><br>
				<input type="button" value="GENERA DATE" id="btn_genera_date" style="padding: 3px 10px 3px 10px; margin-top:10px">
			
			
				<div id="div_file" class="generica" style="margin-top:2px; margin-left:5px">
					<table>
						<thead id="thead_elenco_date" style="text-align:center; height: 25px; display:none">
							<th style="border-bottom: 1px black solid">CANC.</th>
							<th style="border-bottom: 1px black solid">GIORNO</th>
							<th style="border-bottom: 1px black solid; width:130px">DATA</th>
							<th style="border-bottom: 1px black solid; width:100px">SCADENZA</th>
							<th style="border-bottom: 1px black solid; width:100px">COMPILATO</th>
							<th style="border-bottom: 1px black solid; width:100px">DATA COMPILAZIONE</th>
						</thead>
						<tbody id="tbody_elenco_date">
						</tbody>	
					</table>
				</div>
				
				<div class="titolo_pag" style="margin-top:10px">
					<div class="comandi">		
						<div class="aggiungi aggiungi_left" style="background: transparent url(images/saveicon.png) 5px 3px no-repeat !important"><a href="#" onclick="javascript:addSelectedItemsToParent('mem')">Memorizza</a></div>
						<div class="aggiungi aggiungi_right" style="background: transparent url(images/remove.png) 5px 6px no-repeat !important"><a href="#" onclick="javascript:addSelectedItemsToParent('canc')">Elimina memorizzazione</a></div>
					</div>
				</div>
			</form>
		</div>
	</div>
<?}


?>
