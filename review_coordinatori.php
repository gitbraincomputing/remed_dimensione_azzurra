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
$conn = db_connect();
$coord=$_REQUEST['coord'];
//echo($coord);
$query="SELECT dbo.operatori.nome, dbo.operatori.uid, dbo.gruppi.coordinatore 
		FROM dbo.operatori INNER JOIN dbo.gruppi ON dbo.operatori.gid = dbo.gruppi.gid 
		WHERE (dbo.operatori.cancella = 'n') AND (dbo.operatori.status = 1) AND (dbo.gruppi.coordinatore = $coord)
		ORDER BY dbo.operatori.nome";

$rs_coor = mssql_query($query, $conn);

if(!$rs_coor) error_message(mssql_error());
$conta=mssql_num_rows($rs_coor);

?>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

<div class="titoloalternativo">
            <h1>Elenco Operatori da coordinare</h1>
	         <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
</div>
	
<?
	if($conta==0){
	?>	
	<div class="info">Nessun medico da coordinare.</div>
	<?
	exit();}
	
	while($row_coor=mssql_fetch_assoc($rs_coor)){
	?>	
	<div class="titoloalternativo" onClick="gestUser('<?=$row_coor['uid']?>');">
            <h1>Operatore: <?=$row_coor['nome']?></h1>	         
	</div>
	<div id="ope<?=$row_coor['uid']?>">
				
	</div><br/>
	<?}?>
</div></div>


<script type="text/javascript">
function gestUser(iduser){
	
	$("#ope"+iduser).html('caricamento in corso...');
	$.ajax({
	   type: "POST",
	   url: "ajax_review_coordinatori.php",
	   data: "uid="+iduser,
	   success: function(msg){
		 //alert( "Data Saved: " + msg );			 
		 $("#ope"+iduser).html(trim(msg));
		 $.mask.addPlaceholder('~',"[+-]");			 		
		}
	 });
}
		

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

