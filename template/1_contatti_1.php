<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php require('blocchi/v2_meta_tmpl.php');?>


</head>
<body onload="MM_preloadImages('images/news_homepage_vai.png')">


<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td><div align="center"><table border="0" class="tabella_centrale" cellspacing="0" cellpadding="0"><tr><td>

<div class="centrale">
	
	<? require('blocchi/include_general/1_testata.php') ?>
	
	<div class="barra_briciola">
		<div class="home_sinistra">
			<strong>sei qui:</strong>
            <?=stampa_briciola($id_page)?> 			
		</div>	
		<div class="home_destra">
			<!--<script type="text/javascript">new rsspausescroller("spettacolo", "pscroller1", "rssclass", 3000, "_new")</script>-->
		</div>
	</div>
    
	<? require('blocchi/include_general/1_interna_sinistra.php') ?>
    <? require('blocchi/include_general/1_interna_destra.php') ?>
	
	
	<div class="altre_centrale">
    	
        
			<div class="bordino altre_banner_top_pagina">
            	<? banner(9); ?>
            </div>
			<? require('blocchi/include_general/all_contatti.php') ?>            
          
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



<? mssql_close($conn); ?>
</td></tr></table></div></td></tr>
</table>
</body>
</html>