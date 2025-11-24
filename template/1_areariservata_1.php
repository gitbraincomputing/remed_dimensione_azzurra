<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require('blocchi/v2_meta_tmpl.php');?>
<?php 
$id="";
$carica=false;
$modulo="";
$sinistra="";
$destra="";
$post="";

if (isset($_REQUEST['id'])){ 	
	if ((strlen($_REQUEST['id']))==8) 
	$id=$_REQUEST['id'];
	}
if (($id!="")and(($id=="00000001")or($id=="00000002"))){ 
	$carica=true;}
	else{
	
	if (isset($_SESSION['USER_w'])){
	
		if (($id!="")and($_SESSION['USER_w']->controlla_permessi($id))) $carica=true;
		}
	}

	
if ($carica){
	 $conn = db_connect();		
     $query="SELECT moduli.* FROM moduli WHERE ((moduli.cancella='n') and (moduli.status='1') and (moduli.codice='$id'))";		 	 	
     $result=mssql_query($query,$conn);
	 if ($row=mssql_fetch_assoc($result)){
	 	$modulo=$row['nome'];
		
		
		
		$post=$row['modulo_POST'];
		$sinistra=$row['blocco_sinistra'];
		$destra=$row['blocco_destra'];
	 }
	 mssql_free_result($result);
}

?>
<link rel="stylesheet" href="/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
<script src="M_Validation/M_global.js?version=4" type="text/javascript">/**/</script>
<script type="text/javascript" src="/dhtmlgoodies_calendar/dhtmlgoodies_calendar-it.js"></script>


<style>


	div.mandatory {background: url('M_Validation/images/mandatory.png') bottom right no-repeat;}
	div.focused {background: url('M_Validation/images/focused.png') bottom right no-repeat;}
	div.correct {background: url('M_Validation/images/correct.gif') bottom right no-repeat;}
	
div.warning { 
width: auto;
	height: 26px;
	float: left;
	padding: 0;
	
	color: #f0af00;
}
</style>
</head>
<? if ($post!="") require('blocchi/include_general/moduli/'.$post) ?>
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
    
	<? if ($sinistra!="")
		require('blocchi/include_general/'.$sinistra);
		else
		require('blocchi/include_general/1_interna_sinistra.php'); ?>
    <? if ($destra!="")
		require('blocchi/include_general/'.$destra);
		else		
		require('blocchi/include_general/1_interna_destra.php'); ?>
	
	
	<div class="altre_centrale">
    	
        
			<div class="bordino altre_banner_top_pagina">
            	<? banner(9); ?>
            </div>
			<?  if ($modulo!="")
				require('blocchi/include_general/moduli/'.$modulo);
				else
				require('blocchi/include_general/moduli/cortesia.php'); ?>            
          
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