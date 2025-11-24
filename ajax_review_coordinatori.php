<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');

$conn = db_connect();
$user=$_REQUEST['uid'];

?>
<div id="wrap-content"><div class="padding10">
	<div id="ope<?=$row_coor['uid']?>" >
		<input type="hidden" value="0" id="ope_status<?=$row_coor['uid']?>" name="ope_status<?=$row_coor['uid']?>">
		<?
	
		$query="SELECT conteggio, idmodulo, nome, regime, normativa, idregime FROM dbo.re_conteggio_contributo WHERE (id_operatore = $user) order by normativa,regime";
		$rs = mssql_query($query, $conn);
			$query_pagina="";?>
			
			<div class="titoloalternativo">
				<h1 style="font-size: 12px;font-weight:bold">Moduli da visionare</h1>
			</div>
		<?php	
		print("<dl style=\"clear:both;width:50%\">");
		print("<dt >Moduli in cui contribuire</dt>");
		if(mssql_num_rows($rs)>0){
			while ($row = mssql_fetch_assoc($rs)) {
				$query_pagina="";
				$conta=$row['conteggio'];	
				$query_pagina="do=utenti_in_carico&idregime=".$row['idregime']."&idmodulo=".$row['idmodulo']."&idoperatore=$user";
			?>	
			<dd class="float_left" ><span><?=$row['normativa']." - ".$row['regime']." -> ".$row['nome']?></span></dd>

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

		/*moduli in scadenza ciclica*/
		$stringa="<div class=\"info\">Nessun modulo in scadenza ciclica.</div>";
		print("<dl style=\"clear:both;width:50%\">");
		print("<dt>Moduli in scadenza ciclica</dt>");
		$query="SELECT * FROM dbo.re_controllo_scadenza WHERE (id_operatore = $user) and (scadenza <> N'') order by normativa,regime";
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
			<a href="#<?=$idcartella?>#<?=$idmoduloversione?>&mod" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['idpaziente']?>');"><?=$row['nome']." - cart.".$codice_cartella."/".convert_versione($versione)." </a><dd class=\"float_right\"> ".$mancano?></dd></span></dd>
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
	
/*moduli in scadenza puntuale*/
$stringa="<div class=\"info\">Nessun modulo in scadenza puntuale.</div>";
print("<dl style=\"clear:both;width:50%\">");
print("<dt>Moduli in scadenza puntuale</dt>");
$query="SELECT * FROM dbo.re_controllo_scadenza_puntuale WHERE id_operatore=$user ORDER BY codice_cartella, id_modulo_padre DESC";
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
				if($diff>0)
					$mancano="mancano ".$diff." gg";
					else
					$mancano="scaduto da ".$diff*(-1)." gg";
				$i++;		
		?>	
		<dd class="float_left" style="width:70%;"><span><?=$row['normativa']." - ".$row['regime']." -> "?>	
		<a href="#<?=$idcartella?>#<?=$idmoduloversione?>&mod" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['idpaziente']?>');"><?=$row['nome']." - cart.".$codice_cartella."/".convert_versione($versione)." </a><dd class=\"float_right\">".$mancano?></dd></span></dd>
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
print("</dl>");


		$stringa="<div class=\"info\">Nessun modulo in scadenza di trattamento.</div>";
		print("<dl style=\"clear:both;width:50%\">");
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
						<a href="#<?=$idcartella?>#<?=$idmoduloversione?>&mod" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$row['idpaziente']?>');"><?=$row['nome']." - cart.".$codice_cartella."/".convert_versione($versione)." </a><dd class=\"float_right\">".$mancano?></dd></span></dd>
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

		$stringa="<div class=\"info\">Nessun modulo in cui si ha contribuito.</div>";
		print("<dl style=\"clear:both;width:50%\">");
		print("<dt>Moduli in cui si ha contribuito</dt>");
		$query="SELECT * FROM dbo.re_moduli_contribuiti WHERE (id_operatore = $user) order by normativa,regime";
		$rs = mssql_query($query, $conn);

		if(mssql_num_rows($rs)>0){
		while ($row = mssql_fetch_assoc($rs)) {
			$query_pagina="";
			$conta=$row['conteggio'];
			
			$query_pagina="do=utenti_in_carico&idregime=".$row['idregime']."&idmodulo=".$row['id_modulo']."&idoperatore=$user&contributo=si";
		?>	
		<dd class="float_left"><span><?=$row['normativa']." - ".$row['regime']." -> ".$row['nome']?></span></dd>

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

		?>
		<div class="titoloalternativo">
				<h1 style="font-size: 12px;font-weight:bold">Cartella cliniche da chiudere</h1>
		</div>
		<?
		$stringa="<div class=\"info\">Nessuna cartella fuori data massima.</div>";
		print("<dl style=\"clear:both;width:50%\">");
		print("<dt>Cartelle aperte con data ultimo trattamento maggiore di 365 gg</dt>");
		$query="SELECT * FROM dbo.re_ultimo_trattamento WHERE (datadiff >365)";		
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
		print("</dl>");
		?>		

<script type="text/javascript">
function slideOpe(id){
	if($('#ope_status'+id).val()==0){
		$('#ope'+id).slideDown();
		$('#ope_status'+id).val(1);
		//alert("qui");
	}else{
		$('#ope'+id).slideUp();
		$('#ope_status'+id).val(0);
		//alert("o qui");
	}
	
}

function change_tab_edit(iddiv)
{
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="re_pazienti_amministrativa_edita_impegnativa.php?do=edit&id="+iddiv;
$("#lista_impegnative").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });
}


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

	function add_modulo(idcartella,idpaziente,idmodulo)
	{
	$("#layer_nero2").toggle();
	$('#sanitaria').innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=aggiungi_modulo&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo;
	$("#sanitaria").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function edita_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg)
	{
	div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=edita_modulo&cartella="+cartella+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function visualizza_modulo(cartella,idcartella,idpaziente,idmodulo,idinserimento,divagg)
	{
	div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_modulo&cartella="+cartella+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
		
	function ritorna(idcartella,idpaziente,idmodulo,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_cartella&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function ritorna_istanze(cartella,idcartella,idpaziente,idmodulo,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_POST.php?do=visualizza_istanze_modulo&cartella="+cartella+"&idcartella="+idcartella+"&idpaziente="+idpaziente+"&idmodulo="+idmodulo;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });

	}
	
	
	function ritorna_visite(idcartella,idpaziente,idmodulo,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria_cartelle.php?do=addvisita&idcartella="+idcartella+"&id="+idpaziente+"&idmodulo="+idmodulo;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}


	 </script>


<?
exit(0);

?>

