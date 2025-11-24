<?php 
function crea_pagina_risultati($id_regione1,$id_provincia1,$id_settore1,$id_categoria1,$id_jolly1){ 
require(DOC_ROOT.'/blocchi/include_general/include_pag_2.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php require(DOC_ROOT.'/blocchi/v2_meta_tmpl.php');?>


</head>
<body onload="MM_preloadImages('images/news_homepage_vai.png')">
<?php require(DOC_ROOT.'/blocchi/include_general/init_session_ricerca.php');?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td><div align="center"><table border="0" class="tabella_centrale" cellspacing="0" cellpadding="0"><tr><td>

<div class="centrale">
	
	<? require(DOC_ROOT.'/blocchi/include_general/1_testata.php') ?>
	
	<div class="barra_briciola">
		<div class="home_sinistra">
			<strong>sei qui:</strong>
            <?=stampa_briciola($id_page)?> 			
		</div>	
		<div class="home_destra">
			<!--<script type="text/javascript">new rsspausescroller("spettacolo", "pscroller1", "rssclass", 3000, "_new")</script>-->
		</div>
	</div>
    
	<? require(DOC_ROOT.'/blocchi/include_general/1_interna_sinistra.php') ?>
    <? require(DOC_ROOT.'/blocchi/include_general/1_interna_destra.php') ?>
	
	
	<div class="altre_centrale">
    	<?php require(DOC_ROOT.'/blocchi/include_general/init_descrizioni_ricerca.php');?>			
        
			<div class="bordino altre_banner_top_pagina">
            	<? banner(9); ?>
            </div>
			<? stampa_descrizione_categoria(); ?>
            
            <?php require(DOC_ROOT.'/blocchi/v2_strutture_tmpl.php');?>   

	</div><!-- <- fine altre centrale -->

	
	

</div><!-- <- fine centrale -->

<div class="footer">
	<div class="home_sinistra">
			 <? footer_copyright(); ?> 
	</div>	
	<div class="footer_destra">
		<? footer_link(); ?> 
	</div>

</div>
<? stampa_domini_ricerca() ?> 


<? mssql_close($conn); ?>
</td></tr></table></div></td></tr>
</table>
</body>
</html>
<? } ?>