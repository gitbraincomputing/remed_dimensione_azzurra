<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');

if ($principale==true)
{
$nome_pagina="Benvenuto";
header_new($nome_pagina,false);
barra_top_new();
top_message();
menu_new();
$principale=false;
}
$conta=0;
?>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 			<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">home</a></div>
            <div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>          
        </div>

<div class="titoloalternativo">
        <h1>Review</h1>
	</div>
<?
$conn = db_connect();

$query="select * from re_conta_utenti_in_carico order by normativa,regime";
$rs = mssql_query($query, $conn);

print("<dl>");
print("<dt>Utenti in carico</dt>");
while ($row = mssql_fetch_assoc($rs)) {
	$query_pagina="";
	if ($row['idregime']>0)
	$conta=$row['conta'];
	else
	$conta=0;
	
	$query_pagina="do=utenti_in_carico&idregime=".$row['idregime'];
?>	
<dd class="float_left"><span><?=pulisci_lettura($row['normativa']." - ".$row['regime'])?></span></dd>

	<? if($conta>0) { ?>
	<dd class="float_right"><a href="#" onclick="javascript:ricerca('<?=$query_pagina?>')"><strong><?=$conta?></strong></a></dd>
	<?}
	else{
	?>
	<dd class="float_right"><?=$conta?></dd>
	<?
	}
	?>
	<dd class="clear"></dd>
	<?
}
print("</dl>");


$conta=0;

if (controlla_ds())
{

$query="select * from re_cartelle_da_creare_per_regime order by normativa,regime";
$rs = mssql_query($query, $conn);
	$query_pagina="";


print("<dl>");
print("<dt>Cartelle da Creare / Impegnative da Gestire</dt>");
while ($row = mssql_fetch_assoc($rs)) {
	
	if ($row['re_idregime']>0)
	$conta=$row['conta'];
	else
	$conta=0;	
	
	if ($row['is_imp']>0)
	$conta_imp=$row['conta_imp'];
	else
	$conta_imp=0;	
	
	$query_pagina="do=cartelle_da_creare&idregime=".$row['idregime'];
	$query_pagina_imp="do=impegnative_da_gestire&idregime=".$row['idregime'];

	?>	
	<dd class="float_left" style="width:85%;"><span><?=pulisci_lettura($row['normativa'])." ".pulisci_lettura($row['regime'])?></span></dd>
	<dd class="float_right">
	<? if($conta>0) {?><a href="#" onclick="javascript:ricerca('<?=$query_pagina?>')"><strong><?}?>
	<?=$conta?>
	<? if($conta>0) {?></strong></a><?}?>
	 / 
	<? if($conta_imp>0) {?><a href="#" onclick="javascript:ricerca('<?=$query_pagina_imp?>')"><strong><?}?>
	<?=$conta_imp?>
	<? if($conta_imp>0) {?></strong></a><?}?>
		
	<dd class="clear"></dd>
	<?
}
print("</dl>");
}


$user=$_SESSION['UTENTE']->get_userid();
$query="SELECT conteggio, idmodulo, nome, regime, normativa, idregime FROM dbo.re_conteggio_contributo WHERE (id_operatore = $user) order by normativa,regime,nome";
$rs = mssql_query($query, $conn);
	$query_pagina="";

if($_SESSION['UTENTE']->is_gc()){	
?>
	<div class="titoloalternativo">
        <h1><a href="#" style="text-decoration:none" onclick="javascript:istanze('')">Istanze inserite</a></h1>
	</div>
<? } 
$query="SELECT coordinatore FROM gruppi WHERE coordinatore=$user";
$rs_1 = mssql_query($query, $conn);
if(mssql_num_rows($rs_1)>0){	
?>
	<div class="titoloalternativo">
        <h1><a href="#" style="text-decoration:none" onclick="javascript:coordinatori('coord=<?=$user?>')">Status attivit&agrave; operatori</a></h1>
	</div>
<? }?>
	
	<div class="titoloalternativo">
        <h1>Moduli da visionare</h1>
		<div style="clear:both"></div>
		<p style="padding-left: 42px">Clicca sul titolo di ogni sezione per visualizzare/nascondere il contenuto</p>
	</div>
<?php	
print("<dl style=\"clear:both;width:80%\">");
print("<dt onclick='$(\"#div_moduli_contribuire\").toggle()' style='cursor:pointer; padding: 8px 0 8px 4px'>Moduli in cui contribuire</dt>
		<div id='div_moduli_contribuire' style='display:none'>");
if(mssql_num_rows($rs)>0){

	while ($row = mssql_fetch_assoc($rs)) {
		$query_pagina="";
		$conta=$row['conteggio'];	
		$query_pagina="do=utenti_in_carico&idregime=".$row['idregime']."&idmodulo=".$row['idmodulo']."&idoperatore=$user";
	?>	
	<dd class="float_left" ><span><?=$row['normativa']." - ".$row['regime']." -> ".pulisci_lettura($row['nome'])?></span></dd>

		<? if($conta>0) { ?>
		<dd class="float_right"><a href="#" onclick="javascript:ricerca('<?=$query_pagina?>')"><strong><?=$conta?></strong></a></dd>
		<?}
		else{
		?>
		<dd class="float_right"><?=$conta?></dd>
		<?
		}
		?>
		<dd class="clear"></dd>
		<?
	}
	
	}
else{?>
	<div class="info">Nessun modulo disponibile.</div>
<?}
print("</div></dl>");

/*moduli in scadenza ciclica
$stringa="<div class=\"info\">Nessun modulo in scadenza ciclica.</div>";
print("<dl style=\"clear:both;width:80%\">");
print("<dt>Moduli in scadenza ciclica</dt>");
$query="SELECT * FROM dbo.re_controllo_scadenza WHERE (id_operatore = $user) and (scadenza <> N'') order by normativa,regime,nome";
$rs = mssql_query($query, $conn);
$i=0;
if(mssql_num_rows($rs)>0){
	while ($row = mssql_fetch_assoc($rs)) {
		$query_pagina="";
		//$conta=$row['conteggio'];
		$scadenza=$row['scadenza'];
		$datains=$row['datains'];
		$idmoduloversione=$row['id_modulo_versione'];
		$codice_cartella=$row['codice_cartella'];
		$versione=$row['versione'];
		$idcartella=$row['id_cartella'];
		//echo("ins:".$datains." scad:".$scadenza);
	
		//calcola  diff_date
		list($giorno, $mese, $anno) = explode("/",formatta_data($datains)); 
		$giorni = ((time() - mktime (0,0,0,$mese,$giorno,$anno) )/86400);
		$plu_sing = ((ceil(abs($giorni)>1)) or ceil($giorni)==0)?"giorni":"giorno";
		$giorni=ceil($giorni);	
		//echo("ins:".formatta_data($datains)." scad: ".$scadenza." diff: ".$giorni." rimanente:".(($scadenza*1)-$giorni)."<br/>");
		//echo("scad: ".$scadenza." diff: ".$giorni." rimanente:".$scadenza-$giorni."<br/>");
		$diff=($scadenza*1)-$giorni;
		if($diff<=AVVISO_SCADENZA){
			if($diff>0)
					$mancano="mancano ".$diff." gg";
					else
					$mancano="scaduto da ".$diff*(-1)." gg";
			$i++;		
	?>	
	<dd class="float_left" style="width:70%;"><span><?=$row['normativa']." - ".$row['regime']." -> "?>	
	<a href="#<?=$idcartella?>#<?=$idmoduloversione?>&mod" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['idpaziente']?>');"><?=pulisci_lettura($row['nome'])." - cart.".$codice_cartella."/".convert_versione($versione)." </a><dd class=\"float_right\"> ".$mancano?></dd></span></dd>
	<dd class="clear"></dd>
		<?
		}
	}	
	if ($i==0) print($stringa);
}
else{
	print($stringa);
}
print("</dl>");
*/

/*moduli in scadenza ciclica a partire da */
$stringa="<div class=\"info\">Nessun modulo in scadenza ciclica con date preimpostate.</div>";
print("<dl style=\"clear:both;width:80%\">");
print("<dt onclick='$(\"#div_scadenza_cicicla_date_preimpostate\").toggle()' style='cursor:pointer; padding: 8px 0 8px 4px'>Moduli in scadenza ciclica con date preimpostate</dt>
		<div id='div_scadenza_cicicla_date_preimpostate'>");
$query="SELECT * FROM re_moduli_scaduti_non_compilati_con_scadenza_ciclica_data_prefiss WHERE (id_operatore = $user) ORDER BY data_scadenza";		
$rs = mssql_query($query, $conn);
$i=0;
		
if(mssql_num_rows($rs)>0){
	while ($row = mssql_fetch_assoc($rs)) 
	{
		$data_da_confrontare = $row['data_scadenza']; //date('Y-m-d');
		$diff = ceil((strtotime($data_da_confrontare) - time()) / (60 * 60 * 24)); 
		if($diff<=AVVISO_SCADENZA)
		{
			if($diff < 0 ) { 
				$mancano="scaduto da ". ($diff*(-1)) ." gg"; 		// ceil() arrotonda verso l'alto, in modo che se mancano -1,9 giorni uscirà sempre -1 e non -2.
				$tr_style = "background-color: red; color: white";
			}
			elseif($diff == 0) { 
				$mancano="scade oggi";
				$tr_style = "background-color: orange";
			}
			elseif($diff == 1) { 
				$mancano="manca " . 	 $diff ." gg";
				$tr_style = "background-color: yellow";
			}
			elseif($diff > 1 && $diff < 10) { 
				$mancano="mancano " . $diff ." gg";
				$tr_style = "background-color: yellow";
			}			
			elseif($diff > 0 ) { 
				$mancano="mancano " . $diff ." gg";
				$tr_style = "";
			}
			$i++;
		?>	
		<dd class="float_left" style="width:70%">
			<span><?="<b>" . $row['cognome_paziente'] . " " . $row['nome_paziente'] . "</b> (" . $row['normativa']." - ".$row['regime'].") -> "?>
			<a href="#<?=$row['id_cartella']?>#<?=$row['id_modulo_versione']?>&mod" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['id_paziente']?>');"><?=pulisci_lettura($row['nome_modulo'])." - cart.".$row['codice_cartella']."/".convert_versione($row['versione'])." </a>
			<dd class=\"float_right\" style='".$tr_style."'> ".$mancano?></dd>
			</span></dd>
		<dd class="clear"></dd>
		<?
		}
	}
	if ($i==0) print($stringa);
}else{
	print($stringa);
}
print("</div></dl>");


/*moduli in scadenza puntuale*/
$stringa="<div class=\"info\">Nessun modulo in scadenza puntuale.</div>";
print("<dl style=\"clear:both;width:80%\">");
print("<dt onclick='$(\"#div_moduli_scadenza_puntuale\").toggle()' style='cursor:pointer; padding: 8px 0 8px 4px'>Moduli in scadenza puntuale</dt>
		<div id='div_moduli_scadenza_puntuale'>");
$query="SELECT * FROM dbo.re_controllo_scadenza_puntuale WHERE id_operatore=$user ORDER BY data_fissa DESC";
//ORDER BY id_modulo_padre, id_cartella DESC";
$rs = mssql_query($query, $conn);
$i=0;
$cod="";
$mod="";
if(mssql_num_rows($rs)>0){
	while ($row = mssql_fetch_assoc($rs)) {		
		//echo($mod." - ".$row['codice_cartella']."<br/>");
		if(($cod!=$row['codice_cartella'])or(($cod==$row['codice_cartella'])and($mod!=$row['id_modulo_padre']))){
			$query_pagina="";		
			$data_fissa=$row['data_fissa'];		
			$idmoduloversione=$row['id_modulo_versione'];
			$codice_cartella=$row['codice_cartella'];
			$versione=$row['versione'];
			$idcartella=$row['id_cartella'];
			$idoperatore=$row['id_operatore'];			
			$idmodulo=$row['id_modulo_padre'];
			$cod=$codice_cartella;
			$mod=$idmodulo;	
			
			//echo(" data_f ".formatta_data($data_fissa)." now".date('d/m/Y'));
			$diff=diff_in_giorni(formatta_data($data_fissa),date('d/m/Y'));
			//echo("diff: ".$diff."<br/>");
			if($diff<=AVVISO_SCADENZA){
			//if((($scadenza*1)-$giorni)<=AVVISO_SCADENZA){
				if($diff < 0 ) { 
					$mancano="scaduto da ". ($diff*(-1)) ." gg"; 		// ceil() arrotonda verso l'alto, in modo che se mancano -1,9 giorni uscirà sempre -1 e non -2.
					$tr_style = "background-color: red; color: white";
				}
				elseif($diff == 0) { 
					$mancano="scade oggi";
					$tr_style = "background-color: orange";
				}
				elseif($diff == 1) { 
					$mancano="manca " . 	 $diff ." gg";
					$tr_style = "background-color: yellow";
				}
				elseif($diff > 1 && $diff < 10) { 
					$mancano="mancano " . $diff ." gg";
					$tr_style = "background-color: yellow";
				}
				elseif($diff > 0 ) { 
					$mancano="mancano " . $diff ." gg";
					$tr_style = "";
				}
				$i++;	
		?>	
		<dd class="float_left" style="width:70%;">
			<span>
				<?="<b>" . $row['cognome_paziente'] . " " . $row['nome_paziente'] . "</b> (" . $row['normativa']." - ".$row['regime'].") -> "?>	
		<a href="#<?=$idcartella?>#<?=$idmoduloversione?>&mod" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['idpaziente']?>');">
			<?=pulisci_lettura($row['nome'])." - cart.".$codice_cartella."/".convert_versione($versione)." </a>
			<dd class=\"float_right\" style='".$tr_style."'>".$mancano?></dd></span></dd>
		<dd class="clear"></dd>
			<?
			}
		}
	}		
	if ($i==0) print($stringa);
}
else{
	print($stringa);
}
print("</div></dl>");

/*moduli in scadenza di trattamento*/
/*
$stringa="<div class=\"info\">Nessun modulo in scadenza di trattamento.</div>";
print("<dl style=\"clear:both;width:80%\">");
print("<dt>Moduli in scadenza di trattamento</dt>");
$query="SELECT * FROM dbo.re_controllo_scadenza_trattamenti WHERE id_operatore=$user ORDER BY id_modulo_padre, id_cartella DESC";
$rs = mssql_query($query, $conn);
$i=0;
$mod="";
if(mssql_num_rows($rs)>0){
	while ($row = mssql_fetch_assoc($rs)) {		
		
		//if($mod!=$row['id_modulo_padre']){
			$query_pagina="";		
			$trattamenti=$row['trattamenti'];
			$scad_trattamenti=$row['scad_trattamenti'];			
			$idmoduloversione=$row['id_modulo_versione'];
			$codice_cartella=$row['codice_cartella'];
			$versione=$row['versione'];
			$idcartella=$row['id_cartella'];
			$idoperatore=$row['id_operatore'];			
			$idmodulo=$row['id_modulo_padre'];
			$num_istanze=$row['num_istanze'];
			if($num_istanze=="") $num_istanze="0";
			$mod=$idmodulo;				
			$istanze_da_compilare=((int)($trattamenti/$scad_trattamenti))+1;
			$mancano="compilati ".$num_istanze." su ".$istanze_da_compilare." da compilare";
			
		if($istanze_da_compilare>$num_istanze){
			$i++;		
			?>	
			<dd class="float_left" style="width:70%;"><span><?=$row['normativa']." - ".$row['regime']." -> "?>	
			<a href="#<?=$idcartella?>#<?=$idmoduloversione?>&mod" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['idpaziente']?>');"><?=$row['nome']." - cart.".$codice_cartella."/".convert_versione($versione)." </a><dd class=\"float_right\">".$mancano?></dd></span></dd>
			<dd class="clear"></dd>
			<?			
		}
	}		
	if ($i==0) print($stringa);
}
else{
	print($stringa);
}
print("</dl>");

$stringa="<div class=\"info\">Nessun modulo in scadenza di trattamento.</div>";
print("<dl style=\"clear:both;width:80%\">");
print("<dt>Moduli in scadenza di trattamento</dt>");
$query="SELECT * FROM dbo.re_controllo_scadenza_trattamenti WHERE id_operatore=$user ORDER BY codice_cartella DESC,idimpegnativa,id_modulo_padre ASC";
$rs = mssql_query($query, $conn);
$i=0;
$cod="";
$imp="";
$idmod="";
$j=0;
if(mssql_num_rows($rs)>0){
	while ($row = mssql_fetch_assoc($rs)) {		
		//echo($user." - ".$row['id_operatore']."<br/>");
		if(($cod!=$row['codice_cartella'])OR($imp!=$row['idimpegnativa'])OR($idmod!=$row['id_modulo_padre'])){
			$query_pagina="";		
			$trattamenti=$row['trattamenti'];
			$scad_trattamenti=$row['scad_trattamenti'];			
			$idmoduloversione=$row['id_modulo_versione'];
			$codice_cartella=$row['codice_cartella'];
			$versione=$row['versione'];
			$idcartella=$row['id_cartella'];
			$idoperatore=$row['id_operatore'];			
			$idmodulo=$row['id_modulo_padre'];
			$num_istanze=$row['num_istanze'];
			$id_impegnativa=$row['idimpegnativa'];
			if($num_istanze=="") $num_istanze="0";
			$cod=$codice_cartella;
			$imp=$id_impegnativa;
			$idmod=$idmodulo;	
			$istanze_da_compilare=((int)($trattamenti/$scad_trattamenti));
			$mancano="compilati ".$num_istanze." su ".$istanze_da_compilare." da compilare";
			$i++;
			if((((int)($trattamenti/$scad_trattamenti))>0) and($num_istanze!=$istanze_da_compilare)){	
				?>	
				<dd class="float_left" style="width:70%;"><span><?=$row['normativa']." - ".$row['regime']." -> "?>	
				<a href="#<?=$idcartella?>#<?=$idmoduloversione?>&mod" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['idpaziente']?>');"><?=pulisci_lettura($row['nome'])." - cart.".$codice_cartella."/".convert_versione($versione)." </a><dd class=\"float_right\">".$mancano?></dd></span></dd>
				<dd class="clear"></dd>
					<?
					//}
				$j=1;
			}
		}
	}		
	if (($i==0)or($j==0)) print($stringa);
}
else{
	print($stringa);
}
print("</dl>");
*/	
		
/*
$stringa="<div class=\"info\">Nessun modulo in cui si ha contribuito.</div>";
print("<dl style=\"clear:both;width:80%\">");
print("<dt>Moduli in cui si ha contribuito</dt>");
$query="SELECT * FROM dbo.re_moduli_contribuiti WHERE (id_operatore = $user) order by normativa,regime,nome";
$rs = mssql_query($query, $conn);

if(mssql_num_rows($rs)>0){
while ($row = mssql_fetch_assoc($rs)) {
	$query_pagina="";
	$conta=$row['conteggio'];
	
	$query_pagina="do=utenti_in_carico&idregime=".$row['idregime']."&idmodulo=".$row['id_modulo']."&idoperatore=$user&contributo=si";
?>	
<dd class="float_left"><span><?=$row['normativa']." - ".$row['regime']." -> ".pulisci_lettura($row['nome'])?></span></dd>

	<? if($conta>0) { ?>
	<dd class="float_right"><a href="#" onclick="javascript:ricerca('<?=$query_pagina?>')"><strong><?=$conta?></strong></a></dd>
	<?}
	else{
	?>
	<dd class="float_right"><?=$conta?></dd>
	<?
	}
	?>
	<dd class="clear"></dd>
	<?
}
}
else{?>
	<div class="info">Nessun modulo disponibile.</div>
<?}
print("</dl>");
*/

print("<dl style=\"clear:both;width:80%\">");
print("<dt onclick='$(\"#div_moduli_infocert\").toggle()' style='cursor:pointer; padding: 8px 0 8px 4px'>Moduli da firmare digitalmente</dt>
		<div id='div_moduli_infocert'>");
$query="SELECT * FROM re_report_istanze_compilate_da_firmare WHERE oper_firmatario = $user ORDER BY data_creazione ASC";		
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if($conta > 0)	
{
	$moduli_infermieristica = array(202, 203, 204, 206, 207, 208, 209);
	
	while($row = mssql_fetch_assoc($rs)) 
	{
		// SBO - ENTRO SOLO PER I MODULI INDICATI DAL CLIENTE PRESENTI IN $moduli_infermieristica
		if(in_array($row['id_modulo_padre'], $moduli_infermieristica)) {
			// RECUPERO L'ULTIMO GIORNO DEL MESE RISPETTO LA DATA DI CREAZIONE
			$last_month_day = date("Y-m-t", strtotime($row['data_creazione']));
			
			// SE NON SIAMO ANCORA ARRIVATI A FINE MESE SALTO IL MODULO, DALL'ULTIMO GIORNO DEL MESE IN POI SARA' VISIBILE IN REVIEW
			if(strtotime(date("Y-m-d")) < strtotime($last_month_day) ) {
				continue;
			}
		}
		
		echo '<dd class="float_left" style="width:100%;">
				<span>'.$row['normativa'].' - '.$row['regime'].' ->
					<a href="#'.$row['id_cartella'].'#'.$row['id_modulo_versione'].'&mod" onclick="javascript:load_content(\'div\',\'lista_pazienti.php?main_container=3&do=review&id='.$row['id_paziente'].'\');">
						'.$row['nome_modulo'].' - cart.'.$row['codice_cartella'].'/'.$row['versione_cartella']
				. '</a>' . ($row['stato_firma_allegato'] == 's' && $row['stato_firma_allegato2'] == 'a' ? ' (Allegato 2)' : '' ) . 
				'</span></dd>
			<dd class="clear"></dd>';
	}
} else echo '<div class="info">Nessun modulo disponibile.</div>';
print("</div></dl>");


print("<dl style=\"clear:both;width:80%\">");
print("<dt onclick='$(\"#div_moduli_da_validare\").toggle()' style='cursor:pointer; padding: 8px 0 8px 4px'>Moduli da validare</dt>
		<div id='div_moduli_da_validare'>");

$query="SELECT * FROM re_report_istanze_compilate_da_validare WHERE ope_validazione = $user ORDER BY data_creazione ASC";		
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if($conta > 0)
	
{
	while($row = mssql_fetch_assoc($rs)) 
	{
		echo '<dd class="float_left" style="width:100%;">
				<span>'.$row['normativa'].' - '.$row['regime'].' ->
					<a href="#'.$row['id_cartella'].'#'.$row['id_modulo_versione'].'&mod" onclick="javascript:load_content(\'div\',\'lista_pazienti.php?main_container=3&do=review&id='.$row['id_paziente'].'\');">
						'.$row['nome_modulo'].' - cart.'.$row['codice_cartella'].'/'.$row['versione_cartella'].'
					</a>
				</span></dd>
			<dd class="clear"></dd>';
	}
} else echo '<div class="info">Nessun modulo disponibile.</div>';
print("</div></dl>");
?>




<div class="titoloalternativo">
        <h1>Cartella cliniche da chiudere</h1>
</div>
<?
$stringa="<div class=\"info\">Nessuna cartella fuori data massima.</div>";
print("<dl style=\"clear:both;width:80%\">");
print("<dt onclick='$(\"#div_moduli_cc_da_chiudere\").toggle()' style='cursor:pointer; padding: 8px 0 8px 4px'>Cartelle aperte con data ultimo trattamento maggiore di 365 gg</dt>
		<div id='div_moduli_cc_da_chiudere'>");
$query="SELECT * FROM dbo.re_ultimo_trattamento WHERE (datadiff >365)";
//$query="select * from re_conta_contributo_generale where uid=$user order by normativa,regime";
$rs = mssql_query($query, $conn);

if(mssql_num_rows($rs)>0){
	while ($row = mssql_fetch_assoc($rs)) {
		$i++;
		$mancano="passati ".$row['datadiff']." gg";	
		?>	
		<dd class="float_left" style="width:70%;"><span><?=$row['normativa']." - ".$row['regime']." -> "?>	
		<a href="#" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['idutente']?>');"><?="cartella.".$row['codice_cartella']."/".convert_versione($row['versione'])." </a><dd class=\"float_right\">".$mancano?></dd></span></dd>
		<dd class="clear"></dd>
			<?
	}
}		
if ($i==0) print($stringa);
print("</div></dl>");
?>


</div></div>


<script type="text/javascript">

function ricerca(query)
{
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="ricerca_avanzata.php?"+query;
$("#wrap-content").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });


}

function istanze(query)
{
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="review_istanze.php?"+query;
$("#wrap-content").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });
}


function coordinatori(query)
{
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="review_coordinatori.php?"+query;
$("#wrap-content").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });
}
</script>

<?
exit();
?>

