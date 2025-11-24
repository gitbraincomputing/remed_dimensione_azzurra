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
<?php	
	//$id_fiera=$_REQUEST['idfiera'];
	$conn = db_connect();	
	dettaglio_fiera($id_fiera,$ABS_PATH);	
?>	

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
	
		<?=banner(9);?>
		
		<h1><?=$titolo_f?></h1>
			<p>
			<img src="<?=$img_f?>" align="left" class="foto_news_interna" alt="<?=trim($titolo_f)?>" title="<?=trim($titolo_f)?>" />
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
    <div class="altre_centrale_news">	

		<?php
		 
		 if (is_numeric($_REQUEST['pagina'])) $n_pag=$_REQUEST['pagina'];
		 if($n_pag>0)
		 	$ricalcola=0;
			else
			$ricalcola=1;
			
		 griglia_fiera_ricerca(2,3,$n_pag,$ricalcola,$pos,$ABS_PATH,WEB_ROOT);	
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
