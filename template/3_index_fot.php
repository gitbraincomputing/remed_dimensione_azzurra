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
<!--script src="http://www.networkmatrimonio.it/--frank/pageear/AC_OETags.js" language="javascript"></script>    
<script src="http://www.networkmatrimonio.it/--frank/pageear/pageear.js" type="text/javascript"></script-->
<?php require('blocchi/v2_meta_tmpl.php');?>
</head>
<body onload="MM_preloadImages('images/news_homepage_vai.png')">

<div  class="wrapper">
    
	<? require('blocchi/include_general/3_testata.php') ?>

    <div id="briciola">
    	<div class="sinistra_home"><p>sei qui: <?=stampa_briciola($id_page)?> </p></div>
    </div>
	<div id="corpo-pagina">
		<div class="sinistra_home">
        	<h1>fashion news</h1>
            <div class="news-big">
			 <?=spara_news(0,1,0,0,$template['news_home']);?>
         	</div>
            <div class="box">
            	 <div class="news-small">
                    <?=spara_news(1,1,0,0,$template['news_home']);?>
                </div>
                <div class="news-small">
                    <?=spara_news(2,1,0,0,$template['news_home']);?>
                </div>
            </div>
		
            <h1>esercenti in vetrina / <a href="<?=WEB_ROOT?>/organizzare-il-matrimonio.php?idsettore=default" title="visualizza tutti gli esercenti">visualizza tutti</a></h1>
            <div class="box">
                <div class="tag-affiliati-3">
                    <a href="#"><img src="<?=BACK_ROOT?>/template/3_images/advertising-home.gif" alt="advertising" /></a>
                </div>
                <?=banner(3)?>
                <div class="altre_colonna_esercente">
                <?php spara_azienda_vetrina(0,1,$template['strutture_home'],$ABS_PATH,WEB_ROOT); ?>
                </div>
                <div class="altre_colonna_esercente">
                <?php spara_azienda_vetrina(1,1,$template['strutture_home'],$ABS_PATH,WEB_ROOT); ?>
                </div>
             
             </div>
            
            <h1>le fiere dedicate al matrimonio</h1>
            <div class="box">
            	<?=banner(2)?>
                <div class="fiere-small">
                    <?=spara_fiera(0,1,$template['fiere_home'])?>	
                </div>
                <div class="fiere-small">
                    <?=spara_fiera(1,1,$template['fiere_home'])?>
                </div>
            </div>   	
        
        </div><!-- fine sinistra home -->
           
  		<div class="destra_home">
        	<div class="box">
            	<h1><?=$parola?></h1>
        		<p><?=$cappelletto?></p>
            </div>
           	<?=banner(1)?>
            <div class="box">
            	<h1>affiliati</h1>
        		<p></p>
            </div>
            
        </div> <!-- fine destra_home -->
        
    </div>
   
</div>

<? require('blocchi/include_general/3_footer.php') ?>

<? mssql_close($conn); ?>
<!-- PageEar function call  
<script type="text/javascript">    
    writeObjects();
</script>
 -->

</body>
</html>
