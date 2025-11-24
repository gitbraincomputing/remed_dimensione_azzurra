<?php 
// file di inclusione
include_once('include/class.User.php');
include_once('include/session.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/functions.inc.php');



function footer_paginazione($conta){

$numero_pager=(int)($conta/20)
?>

<div class="titolo_pag">
		<div class="comandi">
            <div id="pager" class="pager">
                <form>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/first.png" class="first"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/prev.png" class="prev"/>
                    <input type="text" class="pagedisplay"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/next.png" class="next"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/last.png" class="last"/>
                    <select class="pagesize">
						
						<? 
						$y=0;
						$x=20;
					if ($conta>$x)
					{
						while ($y<$numero_pager)
						{
						if ($y==0)
						$sel="selected";
						else
						$sel="";
						
						?>
					    <option <?=$sel?> value="<?=$x?>"><?=$x?></option>
                     
						<?
						$y++;
						$x=$x+20;
						}
					}	
						?>
					 <option  value="<?=$conta?>">tutti</option>
                    </select>
                </form>
            </div>
		</div>
</div>
<?
}



function header_new ($nome_pagina,$validation)
{
$buiseness_require="0";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=VERSIONE?> / gestionale aziende sanitarie</title>
<link rel="Shortcut Icon" href="favicon.ico">
<?
srand(time());
$random = rand();
?>
<!--<script language="javascript" type="text/javascript" src="script/niceforms.js"></script>-->
<script type="text/javascript" src="include/code2.js?random=<?=$random?>"></script>


<script type="text/javascript" src="script/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="script/ddaccordion.js"></script>
<script type="text/javascript" src="script/jquery.tablednd_0_5.js"></script>
<script type="text/javascript" src="script/jquery.validation.js"></script>
<!--<script type="text/javascript" src="script/jquery.dropshadow.js"></script>-->

<script type="text/javascript" src="script/jquery.corners.min.js"></script>
<script type="text/javascript" src="script/jquery.flexbox.min.js"></script>
<script type="text/javascript" src="script/jquery.tabs.min.js"></script>

<script type="text/javascript" src="script/flexigrid.js"></script>
<script type="text/javascript" src="script/jquery.maskedinput-1.1.4.js"></script>
<script type="text/javascript" src="script/jquery.form.js"></script> 
<script type="text/javascript" src="script/jquery_autocomplete.js"></script> 

<script type="text/javascript" src="script/jquery.tablesorter/tablesorter/jquery.tablesorter.js"></script>
<script type="text/javascript" src="script/jquery.tablesorter/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
<script type="text/javascript" src="script/flexigrid.js"></script>
<script type="text/javascript" src="script/jquery.imgareaselect.min.js"></script>
<script type="text/javascript" src="script/jquery.ocupload-packed.js"></script>



<script type="text/javascript" src="script/general.js?business=<?=$buiseness_require?>&random=<?=$random?>"></script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>
 
 
<?
$validation=true;
 if ($validation==true)
{
?>
<script src="M_Validation/M_global.js?version=4" type="text/javascript">/**/</script>
<?
}
?>
<link href="style/flexigrid.css" rel="stylesheet" type="text/css" />
<link href="style/new_ui.css?random=<?=$random?>" rel="stylesheet" type="text/css" />
<link href="style/new_ui_print.css?random=<?=$random?>" rel="stylesheet" type="text/css" media="print" />



<!--[if IE]>
<link href="style/new_ui-ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lte IE 7]>
<link href="style/new_ui-ie-6.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--
PNGGG!
-->


</head>

<body onResize="javascript:ridimensiona();" onload="javascript:visualizzamenu();">
<!-- loading div tab -->
<div id="layer_nero2" class="layer_nero_background">
	<div id="loading_big_loading"><img src="images/ajax-loader.gif" alt="attendere prego, salvataggio informazioni in corso" /></div>
</div>
<!-- loading div -->

<div id="layer_nero" class="layer_nero_background">
	<div id="loading_big"><img src="images/ajax-loader.gif" alt="attendere prego, salvataggio informazioni in corso" /></div>
    <div id="loading_big_ok">&nbsp;</div>
    <div id="loading_big_no">&nbsp;</div>
</div>
<!-- loading div -->

<div id="wrap-page">
<div id="wrap-all">
<?
}

function footer_new()
{
// chiusura wrap-page e wrap-all
print ("</div></div></div></body></html>");
}

function barra_top_new()
{
$k_img="images/key_off.png";
$k_alt="businesskey non rilevata";
$k_text="bk off";
if(isset($_SESSION['businesskey_inserita'])){
	if($_SESSION['businesskey_inserita']=="si"){
		$k_img="images/key_on.png";
		$k_alt="businesskey rilevata";
		$k_text="bk on";
		}
}
?>
<div id="barratop">
	<div id="contactLink" onclick="javascript:setta_visual();"> mostra / nascondi men&ugrave;</div> 
    <div id="logOut"><a href="index.php?do=logout">effettua il log out</a></div>    
	<div id="ricercaAvanzata" class="aprichiudiricercaAvanzata"> ricerca avanzata</div>
    <div id="review">
	<a onclick="javascript:load_content_menu('div','review.php?p=n',this);" href="#" title="Review">review</a>
	</div>
	<div id="refresh">
	<a href="principale.php" title="ricarica">ricarica</a>
	</div>
	<!--<div id="compilatore" class="aprichiudicompilatore">compilatore</div>-->
	
	<div id="operatore" class="apriChiudiOperatore">
	utente: <strong><?=$_SESSION['UTENTE']->get_cognome_nome();?></strong> gruppo: <strong><?=$_SESSION['UTENTE']->get_nome_gruppo();?></strong> <image class="imgbk" src="<?=$k_img?>" alt="<?=$k_alt?>">
	</div>
	
	
	<!--<form name="ricerca">
	<div id="ricerca_base">
	MyTasks
    	<div class="inputricerca" id="ffb1" /></div>
	
    </div>
	</form>-->	
</div>

<?



}


function top_message()
{?>

<script>

function ricerca_avanzata(obj)
{
	valore=obj.value;
	
	if (valore==1)
	{
	document.getElementById('ric1').style.display="";
	document.getElementById('ric2').style.display="none";
	document.getElementById('ric3').style.display="none";
	}
	
	else if (valore==2)
	{
	document.getElementById('ric1').style.display="none";
	document.getElementById('ric2').style.display="";
	document.getElementById('ric3').style.display="none";
	}
	
	else if (valore==2)
	{
	document.getElementById('ric1').style.display="none";
	document.getElementById('ric2').style.display="none";
	document.getElementById('ric3').style.display="";
	}
	else
	{
	document.getElementById('ric1').style.display="none";
	document.getElementById('ric2').style.display="none";
	document.getElementById('ric3').style.display="none";
		
		
	}
}	

$(function() {   
    $("#ricerca .invio").keypress(function (e) {   
        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {   
            return post_ajax('lista_pazienti.php','wrap-content');           
        } else {   
            return true;   
        }   
    });   
});  
</script>
	
	
<div id="barra_compilatore" class="pannelli_top" >
		<div class="moreinfo_icona">
	    	<img src="images/ico_binocolo.png" />
	    </div>	
	    <div class="destra">
	        <h1>ausilio compilatore</h1>
	        <h2>utilizza la procedura guidata per la compilazione dei moduli sanitari</h2>
			
			 <form  method="post" name="formica_comp" id="ricerca_compilazione">
			 
				<div class="riga">
				<div class="riga">
					<div class="rigo_mask campo_2col">
						<div class="testo_mask" >protocollo</div>
						<div class="campo_mask" id="div_protocollo">
						<?
						$conn=db_connect();
						$query = "SELECT idprotocollo,codice_protocollo,descrizione from re_protocolli_compilatore_attivi";
						$rs2 = mssql_query($query, $conn);
						if(!$rs2) error_message(mssql_error());
?>
						<select id="comp_idprotocollo" name="comp_idprotocollo" onchange="javascript:carica_cosa_fare(this.value);"  onClick="javascript:copy_clipboard();">
							<option value="">seleziona il protocollo</option>
<?
						while($row2= mssql_fetch_assoc($rs2)){
						$idprotocollo=$row2['idprotocollo'];
						$codice_p=$row2['codice_protocollo'];
						$descrizione_p=$row2['descrizione'];
						?>
						<option value="<?=$idprotocollo?>"><?=$codice_p."  - ".$descrizione_p?></option>
						<?
						}
						?>
							 
							 </select>
						</div>
					</div>
					</div>	
					<div class="riga">
					<div class="rigo_mask campo_2col">
						<div class="testo_mask">cosa vuoi compilare?</div>
						<div class="campo_mask" id="div_cosa_fare">
						 </select>
						</div>
					</div>
					</div>
					
					<div class="riga">
					<div class="rigo_mask campo_2col">
						<div class="testo_mask">testo autocompilato</div>
						<div class="campo_mask" id="div_testocompilato">
							
						</div>
					</div>
					</div>
				
				</div> 
				<div class="riga" style="border: none; padding-top: 10px;">
				<div class="rigo_mask campo_2col">
	            	<a class="rounded {transparent, anti-alias} button1 aprichiudicompilatore" >annulla</a>
	            	
				</div>
            </div>
			 
			 </form>
			
			
		</div>
</div>
<div id="barra_moreinfo" class="pannelli_top" >
		<div class="moreinfo_icona">
	    	<img src="images/ico_binocolo.png" />
	    </div>	
	    <div class="destra">
	        <h1>ricerca avanzata</h1>
	        <h2>compila i campi sottostanti per effettuare una ricerca avanzata</h2>
		</div>
       <form  method="post" name="formica" action="ricerca_avanzata.php" id="ricerca">
        	
		<input type="hidden" name="action" value="ricerca_avanzata" />	
		
		<div class="riga">
			    <div class="rigo_mask campo_2col">
					<div class="testo_mask">Ricerca</div>
					<div class="campo_mask">
						 <select name="ricerca_per" onchange="javascript:ricerca_avanzata(this);" onchange="javascript:ricerca_avanzata(this);" >
						 <option value="">seleziona</option>
						 <option value="1" selected="selected">paziente</option>
						<!-- <option value="2">cartella clinica</option>
						 <option value="3">impegnativa</option>-->
						 
						 </select>
					</div>
				</div>
				
		   </div> 
		
		<div id="ric1" style="display:block;">
			
			
			<div class="riga">
				
				<div class="rigo_mask campo_2col">
					<div class="testo_mask">Codice Paziente</div>
					<div class="campo_mask">
						 <input name="CodiceUtente" type="text" class="scrittura invio" /><br />
					</div>
				</div>
				
		    </div> 
			
        	<div class="riga">
				
				<div class="rigo_mask campo_2col">
					<div class="testo_mask">Cognome</div>
					<div class="campo_mask">
						 <input name="Cognome" type="text" class="scrittura invio" /><br />
					</div>
				</div>
				<div class="rigo_mask campo_2col">
					<div class="testo_mask">Nome</div>
					<div class="campo_mask">
						 <input name="Nome" type="text" class="scrittura invio" /><br />
					</div>
				</div>
		    </div> 
			
			
			<div class="riga">
			    <div class="rigo_mask campo_2col">
					<div class="testo_mask">Regime</div>
					<div class="campo_mask">
						 
						 <?
						$conn=db_connect();				
						$query = "SELECT dbo.regime.idregime, dbo.regime.idnormativa, dbo.regime.regime, dbo.regime.stato, dbo.regime.cancella, dbo.normativa.normativa as normativa
						FROM dbo.regime INNER JOIN dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa order by dbo.regime.idregime asc";	
						$rs = mssql_query($query, $conn);
						$conta=mssql_num_rows($rs);
						
						if(!$rs) error_message(mssql_error());
						?>
						<select name="regime" class="scrittura">
							<option value="">tutti</option>
						<?
						while($row = mssql_fetch_assoc($rs))
						{   
						
								$idregime=$row['idregime'];
								$regime=$row['regime'];
								$normativa=$row['normativa'];
								?>
								
								<option value="<?=$idregime?>"><?=$regime." / ".$normativa?></option>
								
								<?
						}
						?>
						</select>	
						 
						 
					</div>
				</div>
				<div class="rigo_mask campo_2col">
					<div class="testo_mask">Stato</div>
					<div class="campo_mask">
						 <select name="stato" class="scrittura">
						 <option value="">tutti</option>
						<option value="1">in trattamento</option>
						<option value="2">in attesa di autorizzazione ASL</option>
						<option value="3">in stato di proroga</option>
						<option value="4">necessario piano di trattamento</option>
						<option value="5">lista d'attesa</option>
						<option value="0">trattamento terminato</option>
						
						</select>
					</div>
				</div>
		    </div>   
			
			
		</div>	
		
			
			
			<div class="riga" style="border: none; padding-top: 10px;">
				<div class="rigo_mask campo_2col">
	            	<a class="rounded {transparent, anti-alias} button1 aprichiudiricercaAvanzata">annulla</a>
	            	<input class="rounded {transparent, anti-alias} button1 " onclick="javascript: return post_ajax('lista_pazienti.php','wrap-content');" type="submit" value="avvia la ricerca">
				</div>
            </div>
			
        </form>
	
</div>

<div id="barra_operatore" class="pannelli_top" style="display:block;">			    
		<div class="moreinfo_icona">
	    	<img src="images/ico_binocolo.png" />
	    </div>	
	    <div class="destra">
	        <h1>gestione operatore</h1>	       
		</div>
		<?php
		$conn=db_connect();				
		$query = "SELECT * From operatori where uid=".$_SESSION['UTENTE']->get_userid();
		$rs = mssql_query($query, $conn);
		$row = mssql_fetch_assoc($rs);
		$mail=$row['email'];
		
		$k_img="images/key_off.png";
		$k_alt="non rilevata";		
		if(isset($_SESSION['businesskey'])){
			if($_SESSION['businesskey_inserita']=="si"){
				$k_img="images/key_on.png";
				$k_alt="rilevata";				
				}
		}
		?>
       <form  method="post" name="act" action="modifica_operatore.php" id="modifica">
        	
		<input type="hidden" name="action" value="edit" />	
			
				
		<div class="riga">
			<div class="rigo_mask campo_2col">
				<div class="testo_mask">email</div>
				<div class="campo_mask email">
					 <input name="email" type="text" class="scrittura" value="<?=$mail?>"/><br />
				</div>
			</div>
			
			<div class="rigo_mask campo_2col">
				<div class="testo_mask">stato businesskey</div>
				<div class="campo_mask email">
					 <image class="imgbk" src="<?=$k_img?>" alt="<?=$k_alt?>"><span class="imgbk_txt"><?=$k_alt?></span>
				</div>
			</div>
			
		</div> 
			
        <div class="riga">
		
		<div id="change_pwd" style="display:block;">
			<div class="rigo_mask campo_2col">	
				<div class="testo_mask" >password</div>
				<div class="campo_mask nomandatory" id="pass_id">
					<input type="password" class="scrittura" name="password" SIZE="30" maxlenght="30" id="password"/>
				</div>
			</div>
			<div class="rigo_mask campo_2col">	
				<div class="testo_mask">conferma password</div>
				<div class="campo_mask nomandatory" id="pass_co">
					<input type="password" class="scrittura" id="rpassword" name="rpassword" SIZE="30" maxlenght="30" /> 
				</div>
			</div>
		</div>
		
		</div>  	
			
		
		<div class="riga" style="border: none; padding-top: 10px;">
			<div class="rigo_mask campo_2col">
				<a class="rounded {transparent, anti-alias} button1 apriChiudiOperatore" style="float:left;">annulla</a>
				<input  src="images/mod_button.gif" type="image" value="modifica" style="float:left; margin-left:5px;">
			</div>
		</div>
			
        </form>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#barra_operatore').hide();
	});
	</script>
</div>
<?}


function menu_new ()
{
?>

<div id="menu">
    	<div class="utente_loggato">
            <span class="small celeste">utente loggato</span><br />
            <strong><?=$_SESSION['UTENTE']->get_username();?></strong>
        </div>
		
		
		<div class="urbangreymenu">
		<?

		$conn = db_connect();
		for($i=1; $i<=11; $i++) {
		
			if ($i==1)
				$liv="Gestione Pazienti";
			else if ($i==2)
				$liv="Gestione ASL";
			else if ($i==3)
				$liv="Gestione Moduli";
			else if ($i==4)
				$liv="Gestione Struttura";
			else if ($i==5)
				$liv="Gestione Permessi";
			else if ($i==6)
				$liv="Gestione SGQ";
			else if ($i==7)
				$liv="Gestione Moduli SGQ";
			else if ($i==8)
				$liv="Controlli DB";
			else if ($i==9)
				$liv="Gestione Compilatore";	
			else if ($i==10)
				$liv="Gestione Test Clinici";
			else if ($i==11)
				$liv="Area Report";			
		
		$num_row=0;
		$query = "SELECT id,nome,link FROM menu WHERE (livello = ".$i.") AND (status = 1) ORDER BY ordine";
		//echo($query);
		$result = mssql_query($query, $conn);
		$intestazione="<h3 class=\"headerbar\"><a href=\"#\">$liv</a></h3><ul class=\"submenu\">";
		$content="";
		$key = 0;				
		if(!$result) error_message(mssql_error());
			while($row = mssql_fetch_assoc($result)) {
				$id = $row['id'];
				$nome_menu = $row['nome'];
				$link_menu = $row['link'];
			
				if(in_array($id, $_SESSION['PERMESSI'])) {
					$content.="<li><a onclick=\"javascript:load_content_menu('div','".$link_menu."',this);\" href=\"#\" title=\"$nome_menu\">$nome_menu</a></li>";		
				}
				$key++;
			}			
		if ($content!="") {
			$content.="</ul>";
			print ($intestazione.$content);
			}
		}
		?>
		<h3 class="headerbar"><a href="#">Help</a></h3>
        	<ul class="submenu">
            	<li><a onclick="javascript:load_content_menu('div','credits.php',this);" href="#" title="elenco moduli">credits</a></li>
                <li><a onclick="javascript:load_content_menu('div','assistenza.php',this);" href="#" title="elenco moduli">supporto</a></li>
            </ul>
        </div>
   </div>
   <div id="wrap-content">
   

<?
}

function pagina_new()
{
?>
<div id="wrap-content"><div class="padding10">
    	<div class="logo"><img src="images/re-med-logo.png" /></div>
        
        <div id="briciola">
        	<div class="elem0"><a href="#">torna alla schermata precedente</a></div>
            <div class="elem_pari"><a href="#">vai a gestione anagrafica</a></div>
            <div class="elem_dispari"><a href="#">vai a gestione anagrafica</a></div>
        </div>
        
        <div id="informazioni-utente">
        	<div class="foto"><img src="images/sample_utente.gif" /></div>
            <div class="contenitore-riga">
                <div class="riga">
                	<div class="box"><em>cartella #</em><br /><strong>1918</strong></div>
                    <div class="box"><em>nome</em><br /><strong>LAURA</strong></div>
                    <div class="box"><em>cognome</em><br /><strong>VILLANI</strong></div>
                </div>
                <div class="riga">
                    <div class="box">nata il<br /><strong>25/08/1980</strong></div>
                    <div class="box">a<br /><strong>napoli</strong></div>
                </div>

            </div>
        </div>
        
        <div id="contenuto-centrale">
        <!-- inizio parte tab -->
            <div id="container-1">
                <ul>
                    <li><a href="#fragment-1"><span>One</span></a></li>
                    <li><a href="#fragment-2"><span>Two</span></a></li>
                    <li><a href="#fragment-3"><span>Tabs are flexible again</span></a></li>
                </ul>
                <div id="fragment-1">
                    <p>First tab is active by default:</p>
                    <pre><code>$(&#039;#container&#039;).tabs();</code></pre>
                </div>
                <div id="fragment-2">
                    <!-- contenuto tab 2 -->
                   	<div id="container-2">
                        <ul>
                            <li><a href="#fragment-4"><span>One</span></a></li>
                            <li><a href="#fragment-5"><span>Two</span></a></li>
                            <li><a href="#fragment-6"><span>Tabs are flexible again</span></a></li>
                        </ul>
                        <div id="fragment-4">
                            <table id="flex1" style="display:none"></table>
                        </div>
                        <div id="fragment-5">
                            Lorem ipsum 
                        </div>
                        <div id="fragment-6">
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                        </div>
                    </div>                    
                    <!-- fine contenuto tab 2 -->
                </div>
                <div id="fragment-3">
                    Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                    Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                    Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                </div>
            </div>
        <!-- fine parte tab -->
        </div>

<?
}

class clsMSWord {
       // Vars:
       var $handle;
    
       // Create COM instance to word
       function clsMSWord($Visible = false)
       {
           $this->handle = new COM("word.application") or die("Unable to instanciate Word");
           $this->handle->Visible = $Visible;
       }
    
       // Open existing document
       function Open($File)
       {
           $this->handle->Documents->Open($File);
       }
    
       // Create new document
       function NewDocument()
       {
           $this->handle->Documents->Add();
       }
   
       // Write text to active document
       function WriteText( $Text )
       {
           $this->handle->Selection->Typetext( $Text );
       }

	   // Write text at specified bookmark
       function WriteBookmarkText( $Bookmark ,$Text )
       {
	   if($Text!=""){
		   if($this->handle->ActiveDocument->Bookmarks->Exists($Bookmark)){ 
				$bookmarkname = $Bookmark;
				$objBookmark = $this->handle->ActiveDocument->Bookmarks($bookmarkname);
				$range = $objBookmark->Range;
				$range->Text = $Text;
			}
		}
       }

       // Number of documents open
       function DocumentCount()
       {
           return $this->handle->Documents->Count;
       }
    
       // Save document as another file and/or format
       function SaveAs($File, $Format = 0 )
       {
           $this->handle->ActiveDocument->SaveAs($File, $Format);
       }
   
       // Save active document
       function Save()
       {
           $this->handle->ActiveDocument->Save();
       }
   
       // close active document.
       function Close()
       {
           $this->handle->ActiveDocument->Close();
       }
   
       // Get word version
       function GetVersion()
       {
           return $this->handle->Version;
       }
   
       // get handle to word
       function GetHandle()
       {
           return $this->handle;
       }

       // Clean up instance with word
       function Quit()
       {
           if( $this->handle )
           {
               // close word
	try
	{
               $this->handle->Quit();

               // free the object
               //$this->handle->Release();
               $this->handle = null;
	} catch ( Exception $e ) {}
           }
       }
};




