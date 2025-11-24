<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require(DOC_ROOT.'/blocchi/include_general/init_session_organizza.php');?>
<?php require(DOC_ROOT.'/blocchi/v2_meta_tmpl.php'); ?>

</head>
<body onload="MM_preloadImages('images/news_homepage_vai.png')">

<div  class="wrapper">
    <? require(DOC_ROOT.'/blocchi/include_general/3_testata.php'); ?>
	<?php if ($id_news!="") dettaglio_news($id_news,$ABS_PATH);	?>
    <div id="briciola">
    	<div class="sinistra_home"><p>sei qui: <?=stampa_briciola($id_page)?> </p></div>
    </div>
    <?php require(DOC_ROOT.'/blocchi/include_general/init_session_organizza.php');?>
    
	<div id="corpo-pagina">
    	<div class="sinistra_altre">
            <div class="box">
            	<div class="tag-affiliati-9"><img src="<?=BACK_ROOT?>/template/3_images/advertising-interne.gif" /></div>
            	<? banner(9); ?>
               
               	<?php	
					//$id_fiera=$_REQUEST['idfiera'];
					$conn = db_connect();	
					dettaglio_fiera($id_fiera,$ABS_PATH);	
				?>	

	<div class="altre_centrale">
		<div class="strutture">
            <h1><?=$titolo_f?></h1>
			<p>
			<img src="<?=$img_f?>" align="left" class="foto_news_interna" alt="<?=$titolo_f?>" />
			<?=$contenuto_f?>
			</p>
		
            <h1>informazioni:</h1>
            
            <div class="altre_box_informazioni">
                
                <div class="altre_box_informazioni_riga">
                    <div class="altre_informazioni_scheda">
                    <h4>città</h4>
                    <?=$citta_f?>
                    </div>
                    
                    <div class="altre_informazioni_scheda">
                    <h4>sito web</h4>
                    <?=$web_f?>
                    </div>
                </div>
                
                
                <div class="altre_box_informazioni_riga">
                    <div class="altre_informazioni_scheda">
                    <h4>data inizio</h4><?=$data_inizio_f?>
                    </div>
                    
                    <div class="altre_informazioni_scheda">
                    <h4>data fine</h4><?=$data_fine_f?>
                    </div>
                </div>
                
            </div>
            <h1>Altre fiere</h1>
        </div>
	</div>
   <div class="altre_centrale_news">	

		<?php
		 
		 if (is_numeric($_REQUEST['pagina'])) $n_pag=$_REQUEST['pagina'];
		 if($n_pag>0)
		 	$ricalcola=0;
			else
			$ricalcola=1;
			
		 griglia_fiera_ricerca(2,4,$n_pag,$ricalcola,$pos,$ABS_PATH,WEB_ROOT);	
		//paginazione
		if ($n_pag>0){
			//stampa precedente
			print("<div class=\"a-red_p\"><a name=\"news\" href=\"fiere.php?pagina=".($n_pag-1)."#news\" title=\"precedente\">&laquo; precedente</a></div>");
		}		
		if (count($_SESSION['TROVATE_fiere'])>((6*$n_pag)+6)){
			//stampa successivo
			print("<div class=\"a-red_s\"><a name=\"news\" href=\"fiere.php?pagina=".($n_pag+1)."#news\" title=\"successivo\">successivo &raquo;</a></div>");  
        }
		
		?>
	

       </div><!-- <- fine altre centrale -->
               
               
             <? require(DOC_ROOT.'/blocchi/include_general/3_destra.php') ?>
            </div>
                        
        
        
        </div><!-- fine sinistra home -->
        
        
        
        
        
        
        
    </div>
   
</div>

<? require(DOC_ROOT.'/blocchi/include_general/3_footer.php') ?>

<? mssql_close($conn); ?>

</body>
</html>