<?php 
$id_page=1;
include_once('include/functions.inc.php');
include_once('include/functions.new.php');
include_once('include/dbengine.inc.php');
// inizializza le variabili di sessione
//include_once('include/session.inc.php');
//include_once('include/session.start.php');
$_SESSION['SETTORE_w']="";
$_SESSION['CATEGORIA_w']="";
?>
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
    <!-- load delle news -->
   
	<div class="home_sinistra"> 
    	 <!-- banner home centro pagina tipo=2-->
        
		
        <div class="box_home_big" style="background-image:url(images/box_home/box_home_<?php echo(rand(1,3)); ?>.jpg);">
		 	
			<div class="box_home_big_sx">
            <?=spara_news(0,1,0,0,$template['news_home']);?>
            </div>
            <div class="box_home_big_dx">
            	<div class="box_home_blocchetto">
                	<?=spara_news(1,1,1,0,$template['news_home']);?>
                </div>
                <div class="box_home_blocchetto">
                	<?=spara_news(2,1,1,0,$template['news_home']);?>
                </div>
                <div class="box_home_blocchetto">
                	<?=spara_news(3,1,1,0,$template['news_home']);?>
                </div>
           </div>
           
           <div class="box_home_big_dw"><div class="box_home_big_dw_sfondo">
           <div class="box_home_big_dw_blocco" style="cursor:pointer;">
		   <?=banner(3)?>
           </div>
           <div class="box_home_big_dw_blocco"><h2>Altre news</h2>
           <?=spara_news(4,2,0,0,$template['news_home']);?>
           </div>
           
           <div class="box_home_big_dw_blocco"><h2>Altre news</h2>
           <?=spara_news(6,2,0,0,$template['news_home']);?>
           </div>
           </div></div>
                
                
			
		</div><!-- <- fine box home big -->
		
		<div class="blocco_bordo altezzaminima">
        <?php print($cappelletto);?>
        
		</div>
		
		
		<div class="blocco_home_stacco">
        	<div class="blocco_home_sx_small">
			<a href="fiere.php"><img src="images/area_fiere_new.gif" CLASS="fierehome" /></a>
			  <?=spara_fiera(0,3,$template['fiere_home'])?>	  		        
            </div>
            
		</div>
        <?=banner(2)?>
		
	</div><!-- <- fine home_sinistra -->
	
	<div class="home_destra">
		<?php require('blocchi/v2_box_ricerca.php'); ?>
	<div class="blocco_bordo_home_vetrina" id="blocco_altre_news_2">
	
    	</div>
	<?=banner(1)?>
        
        <h3 style="padding-top: 10px; padding-bottom: 10px; padding-left: 10px">Esercenti in vetrina</h3>
		<div class="blocco_bordo_home_vetrina">
        	<div class="blocco_home_vetrina">
				<?php spara_azienda_vetrina(0,1,$template['strutture_home'],$ABS_PATH,WEB_ROOT); ?>
            </div>
            <div class="blocco_home_vetrina">
				<?php spara_azienda_vetrina(1,1,$template['strutture_home'],$ABS_PATH,WEB_ROOT); ?>
            </div>
            <div class="blocco_home_vetrina">
				<?php spara_azienda_vetrina(2,1,$template['strutture_home'],$ABS_PATH,WEB_ROOT); ?>
            </div>
            <div class="blocco_home_vetrina">
				<?php spara_azienda_vetrina(3,1,$template['strutture_home'],$ABS_PATH,WEB_ROOT); ?>
            </div>
            <div class="blocco_home_vetrina">
				<?php spara_azienda_vetrina(4,1,$template['strutture_home'],$ABS_PATH,WEB_ROOT); ?>
            </div>
            
		</div>
	</div><!-- <- fine destra -->
	
	<?php
    if ((DOMINIO=="www.networkmatrimonio.it")or(DOMINIO=="www.networkmatrimonio.com")) { ?>
    <div class="blocco_bordo_big">
		<h3>I siti del network</h3>
        <? stampa_domini();?>
    </div>
	<?php } ?>

</div>
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
