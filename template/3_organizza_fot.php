<?php
require(DOC_ROOT.'/blocchi/include_general/include_pag_2_org.php');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require(DOC_ROOT.'/blocchi/include_general/init_session_organizza.php');?>
<?php require(DOC_ROOT.'/blocchi/v2_meta_tmpl.php'); ?>

</head>
<body onload="MM_preloadImages('images/news_homepage_vai.png')">

<div  class="wrapper">
    <? require(DOC_ROOT.'/blocchi/include_general/3_testata.php'); ?>

    <div id="briciola">
    	<div class="sinistra_home"><p>sei qui: <?=stampa_briciola($id_page)?> </p></div>
    </div>
    <?php require(DOC_ROOT.'/blocchi/include_general/init_descrizioni_ricerca_organizza.php');?>
    
	<div id="corpo-pagina">
    	<div class="sinistra_altre">
        	<? stampa_descrizione_categoria(); ?>
            <div class="box">
            	<div class="tag-affiliati-9"><img src="<?=BACK_ROOT?>/template/3_images/advertising-interne.gif" /></div>
            	<? banner(9); ?>
                <div class="cambia-provincia">
                	<h1>cambia provincia</h1>
                    <p>usa il campo qui sotto per selezionare<br />
					un’altra provincia campana!<br />
					<br />

					</p>
                    
                    <form method="post"  name="form0" class="seleziona_provincia" action="organizzare-il-matrimonio.php?idsettore=default">
                    	<? stampa_cerca_provincia(); ?>
                        <input type="hidden" name="regione" value="CHG" />
                        <input type="submit" class="red_pulsante" value="cambia provincia" />                        
                    </form>
                    
                </div>
            </div>
                        
        	<?php require(DOC_ROOT.'/blocchi/v2_strutture_tmpl.php');?>
        
        </div><!-- fine sinistra home -->
        
        
        
        
        
         <? require(DOC_ROOT.'/blocchi/include_general/3_destra.php') ?>
        
    </div>
   
</div>

<? require(DOC_ROOT.'/blocchi/include_general/3_footer.php') ?>

<? mssql_close($conn); ?>

</body>
</html>