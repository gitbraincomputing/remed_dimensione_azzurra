<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php require('blocchi/v2_meta_tmpl.php');?>


</head>
<body onload="MM_preloadImages('images/news_homepage_vai.png')">

<div  class="wrapper">
    <? require(DOC_ROOT.'/blocchi/include_general/3_testata.php'); ?>
	
	<div id="briciola">
    	<div class="sinistra_home"><p>sei qui: <?=stampa_briciola($id_page)?> </p></div>
    </div>
     	
	<div id="corpo-pagina">
    	<div class="sinistra_altre">

            <div class="contatti">
	        	<? require('blocchi/include_general/all_community.php') ?> 
                <p>
<!--<img src="images/coming-soon-community.gif" alt="community - coming soon online" />-->
<a href="http://www.forumsposi.it/index.php" target="_blank"><img src="images/community_entra.gif" align="left" /></a>
<a href="http://www.forumsposi.it/ucp.php?mode=login" target="_blank" ><img src="images/community_effettua.gif" align="left" /></a>
<a href="http://www.forumsposi.it/ucp.php?mode=register" target="_blank"><img src="images/community_iscriviti.gif" align="left" /></a>
			</p>
            </div>
        </div><!-- fine sinistra home -->
        <? require('blocchi/include_general/3_destra.php') ?>     
    </div>
</div>

<? require('blocchi/include_general/3_footer.php') ?>


<? mssql_close($conn); ?>
</td></tr></table></div></td></tr>
</table>
</body>
</html>