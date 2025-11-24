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
$where_sp="";

if(isset($_REQUEST['idnorm'])and($_REQUEST['idnorm']!=0)) {	 
	$where.=" and idregime=".$_REQUEST['idnorm'];
	$idnorm=$_REQUEST['idnorm'];
	$where_sp="@idregime=$idnorm";
}else{
	$idnorm=26;
	//$where.=" and idregime=".$idnorm;
	$where_sp="@idregime='26'";
}

$conn = db_connect();

$conta=0;
if($where==" WHERE 1=1")
	$query="SELECT DISTINCT Cognome, Nome, idregime FROM car_pianificazioni_all_ok";
	else
	$query="SELECT DISTINCT Cognome, Nome, idregime FROM car_pianificazioni_all_ok $where ";
	
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);

if($conta>0){	
	$query1 = "crosstable_moduli $where_sp";
//echo($query1);
//exit();		
	$rs1 = mssql_query($query1, $conn);
	mssql_next_result($rs1);
}
?>
<script type="text/javascript">
function reload_review(idpaz,idnormativa){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="report_pianificazione_moduli.php?idop="+idpaz+"&idnorm="+idnormativa;
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
            <h1>Elenco Pianificazione Cartelle</h1>	         
			<?
			$web="javascript:stampa2('wrap-content');";
			$xls="";$xml="";$doc="";
			$pdf="pdfclass/report_pianificazione_moduli_pdf.php?idnorm=$idnorm";
			include_once('include_stampa.php');
			?>
</div>

<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
		<div class="testo_mask">Filtra per regime</div>
		<div class="campo_big " style="float:left;padding-right:10px">
			<select  class="scrittura" name="idnormativa" id="idnormativa">
				<option selected="" value="26">tutti</option>
				<?
				$query="SELECT idregime,regime,normativa FROM regime INNER JOIN normativa ON regime.idnormativa = normativa.idnormativa WHERE (regime.stato = 1) AND (regime.cancella = 'n')";
				$rs_op = mssql_query($query, $conn);
				while($row_op=mssql_fetch_assoc($rs_op)){
				?>
					<option <?if($idnorm==$row_op['idregime']) echo("selected")?> value="<?=$row_op['idregime']?>"><?=$row_op['normativa']." ".$row_op['regime']?></option>
				<?}	?>
			</select>
		</div>
		<div class="campo_big ">
			<input type="button" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#idoperatore').val(),$('#idnormativa').val());" />
		</div>
</span>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
</span>
<div class="info">Numero pianificazioni moduli per pazienti trovati: <?=$conta?></div>		
<?
	if($conta==0){
	?>
	<div class="info">Nessuna pianificazione moduli per paziente trovata.</div>	
	<?
	exit();}
	
	if($idnorm!=""){
	?>	
	<div class="titoloalternativo">
		<?
		$query_r="SELECT idregime,regime,normativa FROM regime INNER JOIN normativa ON regime.idnormativa = normativa.idnormativa WHERE (regime.stato = 1) AND (regime.cancella = 'n') AND (idregime=$idnorm)";
		$rs_op = mssql_query($query_r, $conn);
		$row_r=mssql_fetch_assoc($rs_op);
		?>
		<h1>regime: <?=$row_r['normativa']." ".$row_r['regime']?></h1>
	</div>
	<?}?>
	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
			<tr> 
		<?
		$fields_arr=array();		
		for ($i = 0; $i < mssql_num_fields($rs1); ++$i) {
			$field = mssql_fetch_field($rs1, $i);
		
				echo ("<th>".$field->name."</th>");
				array_push($fields_arr,$field->name);
		
		}
		?>
			</tr> 
		</thead> 
	<tbody> 
	<?
		
	while($row=mssql_fetch_assoc($rs1)){
		echo("<tr>");
		foreach ($fields_arr as $value){
			echo("<td>".$row[$value]."</td>");
		
		}
		echo("</tr>");
	}
	?>			
	
<?	
	mssql_free_result($rs);
?>		
</div></div>

<?
exit(0);

?>

