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
$where=" WHERE 1=1";
if(isset($_REQUEST['idop'])and($_REQUEST['idop']!=0)) {
	$where.=" and opeins=".$_REQUEST['idop'];
	$idop=$_REQUEST['idop'];
}	
if(isset($_REQUEST['in'])and($_REQUEST['in']!=0)) {
	$data_in=substr($_REQUEST['in'],3,2)."/".substr($_REQUEST['in'],0,2)."/".substr($_REQUEST['in'],6,4);
	//$data=$_REQUEST['in'];
	$where.=" and datains>=CONVERT(DATETIME, '".$data_in."', 102)";
	$in=$_REQUEST['in'];
}
if(isset($_REQUEST['out'])and($_REQUEST['out']!=0)) {
	$data_out=substr($_REQUEST['out'],3,2)."/".substr($_REQUEST['out'],0,2)."/".substr($_REQUEST['out'],6,4);
	//$data=$_REQUEST['out']; 
	$where.=" and datains<=CONVERT(DATETIME, '".$data_out."', 102)";
	$out=$_REQUEST['out'];
}

$conn = db_connect();

$conta=0;
if($where==" WHERE 1=1")
	$query="SELECT top(500) * from re_istanze_operatori order by datains Desc, orains Desc ";
	else
	$query="SELECT top(500) * from re_istanze_operatori $where order by datains Desc, orains Desc ";
//echo($query);
$rs = mssql_query($query, $conn);


if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

?>
<script type="text/javascript">
function reload_review(idpaz,datain,dataout){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="review_istanze.php?idop="+idpaz+"&in="+datain+"&out="+dataout;;
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
</script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				$(".solo_lettere").validation({ 
					type: "alpha",
					add:"' "
					});	
		
	});	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 			<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">home</a></div>
            <div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>          
        </div>

<div class="titoloalternativo">
            <h1>Elenco Istanze inserite</h1>
	         <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>			
			<!--<h2>creata il 17/12/2008 da brain</h2>
			<h2>creata il 17/12/2008 da brain</h2>-->
</div>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
	<div class="testo_mask">Raggruppa per operatore</div>
		<div class="campo_big ">
			<select  class="scrittura" name="idoperatore" id="idoperatore">
				<option selected="" value="0">tutti</option>
			<?
			$query="SELECT * FROM operatori WHERE ((status=1) and (cancella='n')) order by nome asc";
			$rs_op = mssql_query($query, $conn);
			while($row_op=mssql_fetch_assoc($rs_op)){
			?>
				<option <?if($idop==$row_op['uid']) echo("selected")?> value="<?=$row_op['uid']?>"><?=$row_op['nome']?></option>
			<?}	?>
			</select>
		</div>	
</span>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
	<div class="testo_mask">Data inizio</div>
		<div class="campo_big ">
			<input class="campo_data" type="text" name="data_in" id="data_in" value="<?=substr($_REQUEST['in'],0,10)?>"/>
		</div>
</span>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
		<div class="testo_mask">Data fine</div>
		<div class="campo_big " style="float:left;padding-right:20px">
			<input class="campo_data" type="text" name="data_out" id="data_out" value="<?=substr($_REQUEST['out'],0,10)?>"/>
		</div>

		<div class="campo_big ">
			<input type="button" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#idoperatore').val(),$('#data_in').val(),$('#data_out').val());" />
		</div>
</span>
<div class="info">Numero istanze trovate: <?=$conta?></div>		
<?
	if($conta==0){
	?>
	
	
	<div class="info">Nessun paziente trovato.</div>
	
	
	<?
	exit();}?>
	
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    <?
    if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
	?>
		<th>data creaz.</th> 
		<th>ora creaz.</th>	
	<?
	}
	?>	 
	<th>data sist.</th> 
    <th>Operatore</th>
	<th>cartella</th>
	<th>modulo (ver)</th> 
	<td class="no_print">esplora</td>
	<td class="no_print">modifica</td>
	<td class="no_print">stampa</td>
</tr> 
</thead> 
<tbody> 

<?
$div="sanitaria";
while($row = mssql_fetch_assoc($rs))   
{
		//$id=$row['idutente'];
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
			$data_creazione=formatta_data($row['datains']);
			$ora_creazione=$row['orains'];		
		}else{
			$data_creazione="";
			$ora_creazione="";
		}		
		$operatore=$row['operatore'];
		$idmodulo=$row['id_modulo_padre'];
		$id_impegnativa=$row['id_impegnativa'];
		$opeins=$row['opeins'];
		$idinserimento=$row['id_inserimento'];
		$idcartella=$row['id_cartella'];
		if($row['codice_cartella']!="")
			$cartella=$row['codice_cartella']."/".convert_versione($row['versione_cartella']);
			else
			$cartella="vis. spec.";
		$idpaziente=$row['idpaziente'];
		$idmoduloversione=$row['id_modulo_versione'];	
		$data_sistema=formatta_data($row['data_osservazione']);
		$io=strpos($row['modello_word'],".");
		
		$vers=1;
		$stop=0;
		/*
		$query_v="SELECT id, idmodulo, nome FROM dbo.moduli WHERE (idmodulo = $idmodulo) ORDER BY id asc";		
		$rs_v = mssql_query($query_v, $conn);
		while($row_v = mssql_fetch_assoc($rs_v)) {				
			if(!$stop){
			if($row_v['id']!=$idmoduloversione) 
				$vers++;
				else
				$stop=1;
			}
		}
		mssql_free_result($rs_v);
		*/
		if(($idmodulo!=0) and ($idmoduloversione!=0)){
			$query_v="SELECT id, idmodulo, versione FROM dbo.moduli WHERE (idmodulo = $idmodulo) and (id=$idmoduloversione) ORDER BY id asc";		
			$rs_v = mssql_query($query_v, $conn);
			if($row_v = mssql_fetch_assoc($rs_v)) $vers=$row_v['versione'];
			mssql_free_result($rs_v);
		}
		
		//$mudolo=$row['nome_modulo']." (ver ".$row['versione']." )";
		$mudolo=$row['nome_modulo']." (ver ".$vers." )";
		$chiusa=1;
		$query="select id, codice_cartella, data_chiusura from utenti_cartelle where id=$idcartella";
			
		$rs1 = mssql_query($query, $conn);
		if($row=mssql_fetch_assoc($rs1)){ 	
			if($row['data_chiusura']==NULL) $chiusa=0;
		}
		mssql_free_result($rs1);
		
		?>		
		<tr>
		<?
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
		?>	
		 <td><?=$data_creazione?></td> 
		 <td><?=$ora_creazione?></td>
		<?
		}
		?>	
		 <td><?=$data_sistema?></td> 
		 <td><?=$operatore?></td>
		 <td><?=$cartella?></td>
		 <td><?=$mudolo?></td> 	
		 <td class="no_print">
				<?							
			if ( ($opeins==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($idmodulo,$_SESSION['UTENTE']->get_gid())))
			{?> 
			
			
			
			<a href="#<?=$idcartella?>#<?=$idmoduloversione?>#<?=$id_impegnativa?>&load"  onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$idpaziente?>');" ><img src="images/view.png" /></a>
			<? }else {
			 echo("&nbsp;");
			}
			?>
			</td>						
			<td class="no_print">
			<?
			
			if ( ($opeins==$_SESSION['UTENTE']->get_userid()) and  (get_permesso_modulo($idmodulo,$_SESSION['UTENTE']->get_gid())) and (!$chiusa))
			{?> 
			<a href="#<?=$idcartella?>#<?=$idmoduloversione?>#<?=$idinserimento?>#<?=$div?>#<?=$id_impegnativa?>&edit"  onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$idpaziente?>');" ><img src="images/gear.png" /></a>
			<? }else {
			echo("&nbsp;");
			}?>
			
		  </td> 
		  <td class="no_print">
			<?
						
			if ((($opeins==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($idmodulo,$_SESSION['UTENTE']->get_gid())))and($io!=""))
			{?> 				      	
			<a href="stampa_modulo.php?idcartella=<?=$idcartella?>&idinserimento=<?=$idinserimento?>&idimpegnativa=<?=$id_impegnativa?>&idmodulopadre=<?=$idmodulo?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>"><img src="images/printer.png" /></a>
			<? }else {
			echo("&nbsp;");
			}?>
			
		  </td> 
		 
		</tr> 
		<?		
}

mssql_free_result($rs);	
mssql_close($conn);
?>
</tbody> 
</table> 

<? footer_paginazione($conta);?>

</div></div>


<script>
$(document).ready(function() {
	
		
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>

<script type="text/javascript" id="js">

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
 </script>
 
 
<script type="text/javascript" id="js">



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

