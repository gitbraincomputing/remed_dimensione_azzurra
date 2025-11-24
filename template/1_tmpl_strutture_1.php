<?php 
function show_struttura($idstruttura1){ 
require(DOC_ROOT.'/blocchi/include_general/include_pag_4.php');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require(DOC_ROOT.'/blocchi/v2_meta_tmpl.php'); ?>
<link rel="stylesheet" href="<?=WEB_ROOT?>/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen"></link>
<script type="text/javascript" src="<?=WEB_ROOT?>/dhtmlgoodies_calendar/dhtmlgoodies_calendar-it.js?random=20060118"></script>
<?php	require(DOC_ROOT.'/blocchi/include_general/include_preventivo.php');?>
<style>
.IW { width: 300px;}
.IWContent {height: 60px; overflow:auto;}
.IWCaption {font-weight: bold; font-size: 10pt; color: #369; border-bottom: 2px solid #369;}
.IWFooter {margin-top: 2px; font-size: 8pt; }
.IWFooterZoom {}
.IWDirections{background-color:#FFF;}


</style>
</head>
<?php if ((trim($keygoogle)!="")and(trim($lat_s)!="")and(trim($lon_s)!=""))
	$view_map=true;
	else
	$view_map=false;
?>
<body>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td><div align="center"><table border="0" class="tabella_centrale" cellspacing="0" cellpadding="0"><tr><td>

<div class="centrale">
	
<?php require(DOC_ROOT.'/blocchi/include_general/1_testata.php');?>
<div class="barra_briciola">
		<div class="home_sinistra">
			<strong>sei qui:</strong>
            <?=stampa_briciola($id_page)?> 			
		</div>	
		<div class="home_destra">
			<!--<script type="text/javascript">new rsspausescroller("spettacolo", "pscroller1", "rssclass", 3000, "_new")</script>-->
		</div>
	</div>
<?php require(DOC_ROOT.'/blocchi/include_general/1_interna_sinistra.php');?>
<?php require(DOC_ROOT.'/blocchi/include_general/1_interna_destra.php');?>		
	<div class="contenitore_centrale">
    
	<div class="altre_centrale">
    <?php	
    if ($cancella_s=="y")	{
		print("<h1>Attenzione</h1>");
		print("<p>La struttura richiesta non è più presente sul nostro sito.</p>");	}
		else{
		if (($cancella_s=="n")and($stato_s==0)){
			print("<h1>Attenzione</h1>");
			print("<p>Le informazioni relative alla struttura selezionata non sono più disponibili.<br/>Per ulteriori informazioni invii una email a <a href=\"mailto:contact@area04.it\" title=\"Contatta Area04\">contact@area04.it</a></p>");	
			}else{
			if (($cancella_s=="n")and($stato_s==1)){
			?>	<!--apertura struttura-->			
            
		<div class="altre_messaggio_centrale">contatto diretto con l'esercente tramite il nostro modulo!</div>
		<?php 
			print("<h1>".$nome_s."</h1>");
			print("<p>".$descrizione_s."</p>");	
		?>		
				
		<div class="altre_box_foto">
       <?=stampa_immagini_struttura($idstruttura,$ABS_PATH)?>
		</div>
		
		<h1>informazioni:</h1>
		
		<div class="altre_box_informazioni">
			
			<div class="altre_box_informazioni_riga">
            				
                <div class="altre_informazioni_scheda">
				<h4>nome</h4><?=$nome_s?>
				</div>
                
				<div class="altre_informazioni_scheda">
				<h4>indirizzo</h4><?=$indirizzo_s?>				
				</div>
			</div>
			
			<div class="altre_box_informazioni_riga">
				<div class="altre_informazioni_scheda">
				<h4>città</h4><?=$citta_s." (".$cap_s.")"?>
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>località</h4><?=$localita_s?>
				</div>
			</div>	
            
            <div class="altre_box_informazioni_riga">
				<div class="altre_informazioni_scheda">
				<h4>provincia</h4><?=$provincia_s?>
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>regione</h4><?=$regione_s?>
				</div>
			</div>	
            
            <div class="altre_box_informazioni_riga">
				<div class="altre_informazioni_scheda">
				<h4>telefono</h4><?=$tel_s?>
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>fax</h4><?=$fax_s?>
				</div>
			</div>
			
			<div class="altre_box_informazioni_riga">
				<div class="altre_informazioni_scheda">
				<h4>sito internet</h4><?php echo("<a href=\"".WEB_ROOT."/redirect.php?indirizzo=".$web_s."&tipo=1&pag=3&dom=".$id_dominio."&id_str=".$idstruttura."\" target=\"_blank\">".$web_s."</a>");?>
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>e-mail</h4><?php echo("<a href=\"".WEB_ROOT."/redirect.php?indirizzo=".trim($mail_s)."&tipo=2&pag=3&dom=".$id_dominio."&id_str=".$idstruttura."\" target=\"_self\">".trim($mail_s)."</a>");?>
				</div>
			</div>
			
		</div>
        <?php if ($view_map==true){?>
        <a name="mappa"></a>	
        <h1><a href="#mappa" onclick="javascript:showMap();" id="mostra"><img src="<?=WEB_ROOT?>/images/mappa_come_raggiungerci_off.gif" alt="visualizza mappa" /></a></h1> 
        <h1><a href="#mappa" onclick="javascript:noShowMap();" id="nascondi" style="display:none;"><img src="<?=WEB_ROOT?>/images/mappa_come_raggiungerci_on.gif" alt="nascondi mappa" /></a></h1>
        <div class="altre_box_informazioni"><div class="altre_box_informazioni_riga">
  		<div id="map" style="margin: 10px 0; width: 100%; height: 290px; display:none; clear:both; float:left;"></div>
        </div></div>
        
		<?php }?>
		<h1>richiesta preventivo:</h1>

        <p>Le Richieste di preventivo e di informazioni, sono gratuite e non impegnano in nessun caso l'utente che le richiede.</p>		
        <div class="altre_box_informazioni">
			<form name="form1" style="padding:0; margin:0;" method="post" action="<?php echo($PHP_SELF."?idstruttura=".$idstruttura);?>" onsubmit="return controlloModulo();">
            <input type="hidden" name="action" value="null" id="action" />
            <input type="hidden" name="agg_s" value="<?=$agg_s?>" id="agg_s" />
            <input type="hidden" name="tel_s" value="<?=$tel_s?>" id="tel_s" />
			<div class="altre_box_informazioni_riga">
				<div class="altre_informazioni_scheda">
				<h4>nome</h4>
				<input type="text" class="input_scheda" name="nome" id="nome" value="<?php echo($nome_ric);?>"/>
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>cognome</h4>
				<input type="text" class="input_scheda" name="cognome" id="cognome" value="<?php echo($cognome);?>"/>
				</div>
			</div>
			
			<div class="altre_box_informazioni_riga" >
				<div class="altre_informazioni_scheda">
				<h4>e-mail</h4>
				<input type="text" class="input_scheda" name="mail" id="mail" value="<?php echo($mail);?>"/>
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>telefono</h4>
				<input type="text" class="input_scheda" name="telefono" id="telefono" value="<?php echo($telefono);?>" />
				</div>
			</div>
			
			<div class="altre_box_informazioni_riga">
				<div class="altre_informazioni_scheda">
				<h4>periodo indicativo cerimonia: dal</h4>
               <input type="text" onclick="displayCalendar(document.forms[1].data_in,'dd/mm/yyyy',this)" class="input_scheda2"  value="<?php if ($data_in=="") echo(date("d/m/Y"));else echo($data_in); ?>" readonly name="data_in" id="data_in" /><input type="button" style=" background-image:url(<?=WEB_ROOT?>/images/calendar.jpg); background-repeat: no-repeat; border: none; background-color: #abc6f3; width:18px; height:18px; margin: 4px 0 0 5px; float: left;" value=" " onclick="displayCalendar(document.forms[1].data_in,'dd/mm/yyyy',this)">
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>al</h4>
              <input type="text" onclick="displayCalendar(document.form1.data_out,'dd/mm/yyyy',this)" class="input_scheda2" readonly name="data_out" id="data_out" value="<?php echo($data_out);?>"/><input type="button" style=" background-image:url(<?=WEB_ROOT?>/images/calendar.jpg); background-repeat: no-repeat; border: none; background-color: #abc6f3; width:18px; height:18px; margin: 4px 0 0 5px; float: left;" value=" " onclick="displayCalendar(document.form1.data_out,'dd/mm/yyyy',this)">
				</div>
			</div>
			
			<div class="altre_box_informazioni_riga">
				<div class="altre_informazioni_scheda">
				<h4>altre informazioni</h4>
				<textarea class="input_scheda" style="height:100px;" name="note" id="note" ><?php echo($note);?></textarea>
				
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>privacy</h4>
				D. Lgs 196/03. Artt. 13 e 24
I dati da lei forniti saranno:
· conservati per il tempo necessario a ricontattarla;
· trattati con modalità informatica dal titolare del trattamento dei dati e/o da appositi incaricati nominati nel DPSS ed in ogni caso non verranno divulgati se non per i casi espressamente previsti dal D.Lgs 196/03.
Per permettere il trattamento deve acconsentire alla registrazione temporanea dei suoi dati. 
				
				</div>
                <input type="checkbox" name="privacy" id="privacy" style="margin-left:280px;"  /> ho letto ed accettato l'informativa sulla privacy
			</div>
            
             <div class="altre_box_informazioni_riga">
				<div class="altre_informazioni_scheda">
				<h4>codice di sicurezza</h4>
				<img src='<?=WEB_ROOT?>/captcha/immagine.php' class="captcha">
				</div>
				
				<div class="altre_informazioni_scheda">
				<h4>inserire il codice mostrato in figura</h4>
				<input class="input_scheda" type="text" name="key" id="key" size=17 maxlength=6 >
				</div>
			</div>
			
			<input type="submit" class="red_pulsante" style="margin: 10px 0 5px 0; padding: 3px 5px 3px 35px; border: 0px; color: #FFF;" value="invia richiesta &raquo;" />		
		</form>			
		</div>
		
<?php if ($view_map==true){?>
		<p>
		<div id="map" style="margin-top:10px;width: 550px; height: 400px;"></div>
		</p> 
<? }?>				

		

	
    <?php }
	else{
	print("<h1>Attenzione</h1>");
	print("<p>La struttura azienda non è più presente sul nostro sito.</p>");}
   ?> <!-- fine apertura struttura--><?php
			
			}
		
		}
    
    ?>
    
	</div><!-- <- fine altre centrale -->
	</div>
	
</div><!-- <- fine centrale -->

<div class="footer">
	<div class="home_sinistra">
			 <? footer_copyright(); ?> 
	</div>	
	<div class="footer_destra">
		<? footer_link(); ?> 
	</div>

</div>

</td></tr></table></div></td></tr>
</table>

</body>
</html>

<?php if ($view_map==true){?>
<script src="http://maps.google.com/maps?file=api&v=2&key=<?=trim($keygoogle)?>" type="text/javascript"></script>
<script type="text/javascript">
    //<![CDATA[
function noShowMap(){
	 document.getElementById("map").style.display="none";
	  document.getElementById("mostra").style.display="block";
	   document.getElementById("nascondi").style.display="none";
}


function showMap()
{
        document.getElementById("map").style.display="block";
		 document.getElementById("mostra").style.display="none";
		  document.getElementById("nascondi").style.display="block";
		if (GBrowserIsCompatible()) {
            
	// Custom Icon
	//var iconcustom = new GIcon(iconbig);
	//iconcustom.shadow = '';

	var lat ='<?=$lat_s?>';
	var longi ='<?=$lon_s?>';
	
	var nome='<?=$nome_s?>';
	var indirizzo='<?=$indirizzo_completo_s?>';
	
	

	
	

	var icona_hotel = new GIcon();
	
	icona_hotel.image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
	icona_hotel.shadow = "http://www.google.com/mapfiles/shadow50.png";
	icona_hotel.iconSize = new GSize(32, 32);
	icona_hotel.shadowSize = new GSize(37, 34);
	icona_hotel.iconAnchor = new GPoint(6, 20);
	icona_hotel.infoWindowAnchor = new GPoint(10, 10);

	// Ottiene l'elemento della pagina chiamato "map" (il DIV) 
	//   e crea la mappa utilizzandolo come contenitore.
	var map = new GMap2(document.getElementById("map"));                

	// Aggiunge dei controlli per lo zoom e lo spostamento 
	map.addControl(new GLargeMapControl());
	map.addControl(new GMapTypeControl());
	// centra la mappa su Roma
	map.setCenter(new GLatLng(lat,longi), 15);
	
	var immagine='<?=$logo_azienda_s?>';
	
	//var img_htl="<img src=loghi_aziende/"+immagine+" align=\"left\"  />";	
	var img_htl="";	
			
		footerHtml = "<div class=\"IWFooter\"><div class=\"IWFooterZoom\"></div>";

	
		// Define Marker
		
	//HTML = "<div class=\"IW\"><div class=\"IWCaption\">"+nome+" </div><div class=\"IWContent\">"+img_htl +"<br />&nbsp;"+ indirizzo+"</div>" + footerHtml + "</div>";
		
InfoHTML1 = "<div class=\"IW\"><div class=\"IWCaption\">"+nome+"<br />"+indirizzo+" </div><div class=\"IWContent\">"+img_htl+"&nbsp;<br />Per maggiori informazioni visita il nostro sito</div>" + footerHtml + "</div>";

		InfoHTML = "<div class=\"IW\"><div class=\"IWCaption\">"+nome+"</div><div class=\"IWContent\">"+img_htl +"<br />&nbsp;"+ indirizzo + "</div>" + footerHtml + "</div>";
	
		
	//var HTML = "<div class=\"IW\"><div class=\"IWCaption\">"+nome+"</div><div class=\"IWContent\">"+img_htl +"<br />&nbsp;"+ indirizzo+"</div></div>";
   
   function createMarker(point, icona_hotel,InfoHTML) {

		var marker = new GMarker(point,icona_hotel);
		GEvent.addListener(marker, "click", function() {
		marker.openInfoWindowHtml(InfoHTML);
		});
		return marker;
	}
	
		

		// recupera il punto da marcare
		point = new GLatLng(lat,longi);
		// crea il marker
		marker = createMarker(point, icona_hotel,InfoHTML);
		
		// scrive il marker nella mappa
		map.addOverlay(marker); 

		//marker.openInfoWindowTabsHtml(InfoHTML);
		marker.openInfoWindowHtml(InfoHTML);


	

	}   

 }
    //]]>
</script>

<? } ?>

<? }?>
