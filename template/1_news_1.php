<?php 
// inizializza le variabili di sessione
//include_once('include/session.inc.php');
//include_once('include/session.start.php');
//$_SESSION['SETTORE_w']="";
//$_SESSION['CATEGORIA_w']="";
//$_SESSION['PROVINCIA_w']="default";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require(DOC_ROOT.'/blocchi/v2_meta_tmpl.php');?>
</head>
<body onload="MM_preloadImages('images/news_homepage_vai.png')">
<?php require(DOC_ROOT.'/blocchi/include_general/init_session_organizza.php');?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td><div align="center"><table border="0" class="tabella_centrale" cellspacing="0" cellpadding="0"><tr><td>

<div class="centrale">
	<? require(DOC_ROOT.'/blocchi/include_general/1_testata.php') ?>

<?php if ($id_news!="") dettaglio_news($id_news,$ABS_PATH);	?>
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
   	<div class="contenitore_centrale">
       
<?php	
	//$id_fiera=$_REQUEST['idfiera'];
	$conn = db_connect();	
	if ($id_news!=""){		
?>	
	<div class="altre_centrale">
		 <div class="altre_banner_top_pagina" style="float:left;">
		 <?=banner(9);?>
         </div>
		
        <h1><?php echo(strtoupper($titolo_n));?></h1>
        <p>
        <img src="<?=$img_n?>" align="left" class="foto_news_interna" alt="<?=trim($titolo_n)?>" title="<?=trim($titolo_n)?>" />
        <?php echo($contenuto_n);?>
        </p>
    
    
        <h1>Altre news</h1>
	</div>
    <div class="altre_centrale_news">	
    
 <?php }
 	else {?>
 	
 	 <div class="altre_centrale_news">	

		 <div class="altre_banner_top_pagina" style="float:left;">
		 <?=banner(9);?>
         </div>
         

         <div class="box_home_big_mod" style="background-image:url(images/box_home/box_news_1.jpg);">
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
			 
		 griglia_news_ricerca(2,3,$n_pag,$ricalcola,0,$ABS_PATH,WEB_ROOT);
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
