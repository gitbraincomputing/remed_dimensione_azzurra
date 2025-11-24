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
	if ($id_news==""){
		$query="SELECT TOP 1 news.* FROM news ORDER BY NEWID()";
		$result=mssql_query($query,$conn);
		$row=mssql_fetch_assoc($result);
		$id_news=$row['id'];
		$titolo_n=strtoupper($row['titolo']);
		$img_n=$ABS_PATH."/img/news/".$row['immagine'];
		$contenuto_n=substr($row['contenuto'],3,(strlen($row['contenuto'])-4));		
		mssql_free_result($result);
	}	
	if ($id_news!=""){		
?>	
	<div class="altre_centrale">
		<div class="strutture">
            <h1><?php echo(strtoupper($titolo_n));?></h1>
            <p>
            <img src="<?=$img_n?>" align="left" class="foto_news_interna" alt="<?=trim($titolo_n)?>" title="<?=trim($titolo_n)?>" />
            <?php echo($contenuto_n);?>
            </p>
            
        
            <h1>Altre news</h1>
        </div>
	</div>
    <div class="altre_centrale_news">	
    
 <?php }
 	else {?>
 	
 	 <div class="altre_centrale_news">	

         <div class="box_home_big_mod">
        	<div class="box_home_big_sx_mod">
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
		</div><!-- <- fine box home big -->
		<?php }
	mssql_free_result;
	
		if (is_numeric($_REQUEST['pagina'])) $n_pag=$_REQUEST['pagina'];	 
		 if($n_pag>0)
		 	$ricalcola=0;
			else
			$ricalcola=1;
			 
		 griglia_news_ricerca(2,4,$n_pag,$ricalcola,0,$ABS_PATH,WEB_ROOT);
		//paginazione
		if ($n_pag>0){
			//stampa precedente
			print("<div class=\"a-red_p\"><a name=\"news\" href=\"?pagina=".($n_pag-1)."#news\" title=\"precedente\">&laquo; precedente</a></div>");
		}		
		if (count($_SESSION['TROVATE_news'])>((6*$n_pag)+6)){
			//stampa successivo
			print("<div class=\"a-red_s\"><a name=\"news\" href=\"?pagina=".($n_pag+1)."#news\" title=\"successivo\">successivo &raquo;</a></div>");  
        }
		
		?>
	
	

       </div><!-- <- fine altre centrale -->
               
               
            </div>
                        
        
        </div><!-- fine sinistra home -->
        
        
        
        
        
         <? require(DOC_ROOT.'/blocchi/include_general/3_destra.php') ?>
        
    </div>
   
</div>

<? require(DOC_ROOT.'/blocchi/include_general/3_footer.php') ?>

<? mssql_close($conn); ?>

</body>
</html>