<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 63;
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

$conta=0;
$query="SELECT * FROM re_statistica_primo_trattamento order by cognome ASC";
$_SESSION['query_ex']=$query;
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

?>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 			<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">home</a></div>
            <div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>          
        </div>


<div class="titoloalternativo">
            <h1>Elenco Pazienti: statistiche primo trattamento</h1>	        
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="ajax_report_excel.php?fun=3";
			$xml="";$doc="";$pdf="";
			include_once('include_stampa.php');
			?>
</div>
	
		
<?
	if($conta==0){
	?>
	<div class="info">Nessun paziente trovato.</div>
	<?
	exit();}?>
	
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>Codice</th> 
	<th>Cognome</th> 
	<th>Nome</th> 
    <th>Data Ric. Autorizz.</th>	
	<th>Data Primo trattamento</th>
	<th>Diff. primo tratt.</th> 
	<th>Regime/Normativa</th>	
	<td class="no_print">Anag</td>
	<td class="no_print">Amm</td>
	<td class="no_print">San</td>
</tr> 
</thead> 
<tbody> 

<?
$div="sanitaria";
while($row = mssql_fetch_assoc($rs))   
{
		$idutente=$row['idutente'];
		$cognome=utf8_encode($row['cognome']);
		$nome=utf8_encode($row['nome']);		
		$data_nascita=formatta_data($row['data_nascita']);		
		$data_aut=formatta_data($row['data']);
		$data_tra=formatta_data($row['data_trattamento']);
		$regime=$row['normativa']." - ".$row['regime'];	
		if($data_tra!="") 
			$diff=diff_in_giorni($data_tra,$data_aut);
			else
			$diff=" - ";	
		?>		
		<tr> 
		 <td><?=$idutente?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$data_aut?></td> 
		 <td><?=$data_tra?></td> 
		 <td><?=$diff?></td>
		 <td><?=$regime?></td>		
		 <td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$idutente?>');" href="#"><img src="images/anagrafica.png" /></a></td> 
		 <td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$idutente?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <td class="no_print"><a onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$idutente?>');" href="#"><img src="images/cartella.png" /></a></td> 		 
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

<script type="text/javascript">

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
 
 
<?
exit(0);

?>

