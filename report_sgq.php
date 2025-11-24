<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 64;
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
$datadioggi = date('d/m/Y');
$conta=0;
$conn = db_connect();

$where=" WHERE 1=1";
if(isset($_REQUEST['idop'])and($_REQUEST['idop']!=0)) {
	$where.=" and responsabile=".$_REQUEST['idop'];
	$idop=$_REQUEST['idop'];
}
if(isset($_REQUEST['idarea'])and($_REQUEST['idarea']!=0)) {
	$where.=" and idarea=".$_REQUEST['idarea'];
	$idarea=$_REQUEST['idarea'];
}
if(isset($_REQUEST['idmodulo_id'])and($_REQUEST['idmodulo_id']!=0)) {
	$where.=" and idmodulo_id=".$_REQUEST['idmodulo_id'];
	$idmodulo_id=$_REQUEST['idmodulo_id'];
}		
if(isset($_REQUEST['in'])and($_REQUEST['in']!=0)) {
	$data_in=substr($_REQUEST['in'],3,2)."/".substr($_REQUEST['in'],0,2)."/".substr($_REQUEST['in'],6,4);
	//$data=$_REQUEST['in'];
	$where.=" and data_scadenza_generata>=CONVERT(DATETIME, '".$data_in."', 102)";
	$in=$_REQUEST['in'];
}
if(isset($_REQUEST['out'])and($_REQUEST['out']!=0)) {
	$data_out=substr($_REQUEST['out'],3,2)."/".substr($_REQUEST['out'],0,2)."/".substr($_REQUEST['out'],6,4);
	//$data=$_REQUEST['out']; 
	$where.=" and data_scadenza_generata<=CONVERT(DATETIME, '".$data_out."', 102)";
	$out=$_REQUEST['out'];
}
if(isset($_REQUEST['idscadenze'])and($_REQUEST['idscadenze']!=0)) {
    if($_REQUEST['idscadenze']==1){//rispettate
	    $query="SELECT * from re_report_scadenze_rispettate_sgq $where order by data_scadenza_generata asc";
	    $idscadenze=$_REQUEST['idscadenze'];
	}
	if($_REQUEST['idscadenze']==2){//non rispettate
	    $query="SELECT * from re_report_sgq $where and (data_scadenza_generata<'$datadioggi') and (id_istanza_testata_sgq IS NULL) order by responsabile asc";
	    $idscadenze=$_REQUEST['idscadenze'];
	}
	if($_REQUEST['idscadenze']==3){//da rispettare
        $query="SELECT * from re_report_sgq $where and (data_scadenza_generata>='$datadioggi') and (id_istanza_testata_sgq IS NULL) order by responsabile asc";
		$idscadenze=$_REQUEST['idscadenze'];
	}
	if($_REQUEST['idscadenze']==4){//inserite in ritardo
	   $query="SELECT * from re_report_scadenze_rispettate_in_ritardo_sgq $where order by data_scadenza_generata asc";
	    $idscadenze=$_REQUEST['idscadenze'];
	}
}else{
$query="SELECT * from re_report_sgq $where order by data_scadenza_generata asc, responsabile desc";
}
$_SESSION['query_ex']=$query;
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

?>
<script type="text/javascript">
function reload_review(idpaz,idarea,idmodulo_id,idscadenze,datain,dataout){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="report_sgq.php?idop="+idpaz+"&idarea="+idarea+"&idmodulo_id="+idmodulo_id+"&idscadenze="+idscadenze+"&in="+datain+"&out="+dataout;;
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 			<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">home</a></div>
            <div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>          
        </div>


<div class="titoloalternativo">
            <h1>Report SGQ</h1>	      
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="ajax_report_excel.php?fun=8";
			$xml="";$doc="";$pdf="";
			include_once('include_stampa.php');
			?>
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
	<div class="testo_mask">Raggruppa per area</div>
		<div class="campo_big ">
			<select  class="scrittura" name="idarea" id="idarea">
				<option selected="" value="0">tutte</option>
			<?
			
			$query = "SELECT * from area order by area asc";	
            $rs_area = mssql_query($query, $conn);
			while($row_area = mssql_fetch_assoc($rs_area)){   
			?>
				<option <?if($idarea==$row_area['idarea']) echo("selected")?> value="<?=$row_area['idarea']?>"><?=$row_area['area']?></option>
			<?}	?>
			</select>
		</div>	
</span>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
	<div class="testo_mask">Raggruppa per modulo SGQ</div>
		<div class="campo_big ">
			<select  class="scrittura" name="idmodulo_id" id="idmodulo_id">
				<option selected="" value="0">tutti</option>
			<?
			$query = "SELECT * from re_distinct_moduli where tipo=2 order by idmodulo desc";	
            $rs1 = mssql_query($query, $conn);
            if(!$rs1) error_message(mssql_error());
			while($row1 = mssql_fetch_assoc($rs1)){
                $idmodulo=$row1['idmodulo'];
                $query = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc";	
                $rs2 = mssql_query($query, $conn);
                if(!$rs2) error_message(mssql_error());
                while($row_modulo = mssql_fetch_assoc($rs2))
                {       
			?>
				<option <?if($idmodulo_id==$row_modulo['id']) echo("selected")?> value="<?=$row_modulo['id']?>"><?=$row_modulo['nome']?></option>
			<?  }
			}	     ?>
			</select>
		</div>	
</span>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
	<div class="testo_mask">Raggruppa per scadenze</div>
		<div class="campo_big ">
			<select  class="scrittura" name="idscadenze" id="idscadenze">
				<option selected="" value="0">tutte</option>
				<option <?if($idscadenze==1)echo("selected")?> value="1">rispettate</option>
				<option <?if($idscadenze==2)echo("selected")?> value="2">non rispettate</option>
				<option <?if($idscadenze==3)echo("selected")?> value="3">da rispettare</option>
				<option <?if($idscadenze==4)echo("selected")?> value="4">inserite in ritardo</option>
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
			<input type="button" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#idoperatore').val(),$('#idarea').val(),$('#idmodulo_id').val(),$('#idscadenze').val(),$('#data_in').val(),$('#data_out').val());" />
		</div>
</span>		
<?
	if($conta==0){
	?>
	<div class="info">Nessuna scadenza associata.</div>
	<?
	exit();}?>
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>
    <th>stato</th> 
	<th>operatore</th> 
	<th>area</th> 
	<th>modulo sgq</th>
	<th>scadenza associata</th>
	<th width="15%">data / ora inserimento</th>
	
	
</tr> 
</thead> 
<tbody> 

<?
$datadioggi = date('d/m/Y');
$div="sanitaria";
while($row = mssql_fetch_assoc($rs))   
{
		$stato="";
		$data_scadenza_generata=formatta_data($row['data_scadenza_generata']);
		$diff_in_giorni=diff_in_giorni($data_scadenza_generata,$datadioggi);
		if($diff_in_giorni < 0){
			    $stato=2;//"immagine relativa ad una scadenza non inserita";
		}
		
		$operatore=utf8_encode($row['nome']);		
		$area=utf8_encode($row['nome_area']);
		$modulo=utf8_encode($row['nome_modulo']);
		$data_scadenza_associata=formatta_data($row['data_scadenza_generata']);	
		$id_istanza_testata_sgq=$row['id_istanza_testata_sgq'];
		$data_ora_inserimento="";
		if($id_istanza_testata_sgq!=""){
		    $query_scadenza="SELECT * FROM istanze_testata_sgq WHERE (id_istanza_testata_sgq=$id_istanza_testata_sgq) ";
			$rs_scadenza = mssql_query($query_scadenza, $conn);
			$record_trovati=mssql_num_rows($rs_scadenza);
			if($record_trovati>0){
                $row_scadenza = mssql_fetch_assoc($rs_scadenza);
				$data_ora_inserimento=formatta_data($row_scadenza['datains'])." ".$row_scadenza['orains'];		
			}
		}
		?>		
		<tr> 
		 <td><?if (($stato==2) and ($data_ora_inserimento=="")) {?><img src="images/scadenza_non_rispettata.png"  title="scadenza non rispettata" /> <?}?></td> 
		 <td><?=$operatore?></td> 
		 <td><?=$area?></td> 
		 <td><?=$modulo?></td>
		 <td><?=$data_scadenza_associata?></td> 
		 <td><?=$data_ora_inserimento?></td>
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
	$(".campo_data").mask("99/99/9999");
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

