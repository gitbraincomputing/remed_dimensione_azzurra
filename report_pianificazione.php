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
	$where.=" and id_operatore=".$_REQUEST['idop'];
	$idop=$_REQUEST['idop'];
}
	
if(isset($_REQUEST['idnorm'])and($_REQUEST['idnorm']!=0)) {	 
	$where.=" and idregime=".$_REQUEST['idnorm'];
	$idnorm=$_REQUEST['idnorm'];
}else{
	if($idop==0){
		$idnorm=1;
		$where.=" and idregime>".$idnorm;
	}
}

$conn = db_connect();

$conta=0;
if($where==" WHERE 1=1")
	$query="SELECT DISTINCT Cognome, Nome FROM dbo.re_status_moduli";
	else
	$query="SELECT DISTINCT Cognome, Nome FROM dbo.re_status_moduli $where ";
//echo($query);
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);


if($where==" WHERE 1=1")
	$query1="SELECT * from re_status_moduli order by cognome, nome, DataAutorizAsl, id";
	else
	$query1="SELECT * from re_status_moduli $where order by cognome, nome, DataAutorizAsl, id";
//echo($query);
//exit();


?>
<script type="text/javascript">
function reload_review(idpaz,idnormativa){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="report_pianificazione.php?idop="+idpaz+"&idnorm="+idnormativa;
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
			$pdf="pdfclass/report_pianificazione_pdf.php?where=$where";
			include_once('include_stampa.php');
			?>
</div>
</div>
<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
	<div class="testo_mask">Filtra per regime</div>
		<div class="campo_big " >
			<select  class="scrittura" name="idnormativa" id="idnormativa">
				<option selected="" value="0">tutti</option>
				<?
				$query="SELECT idregime,regime,normativa FROM regime INNER JOIN normativa ON regime.idnormativa = normativa.idnormativa WHERE (regime.stato = 1) AND (regime.cancella = 'n')";
				$rs_op = mssql_query($query, $conn);
				while($row_op=mssql_fetch_assoc($rs_op)){
				?>
					<option <?if($idnorm==$row_op['idregime']) echo("selected")?> value="<?=$row_op['idregime']?>"><?=$row_op['normativa']." ".$row_op['regime']?></option>
				<?}	?>
			</select>
		</div>
</span>

<span style="border-bottom: 0px none; margin-bottom: 15px;" class="rigo_mask no_print">
		<div class="testo_mask">Filtra per operatore</div>
		<div class="campo_big " style="float:left;padding-right:20px">
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
		<div class="campo_big ">
			<input type="button" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#idoperatore').val(),$('#idnormativa').val());" />
		</div>
</span>
<div class="info">Numero cartelle pianificate trovate: <?=$conta?></div>		
<?
	if($conta==0){
	?>
	<div class="info">Nessuna cartella pianificata trovata.</div>	
	<?
	exit();}?>
	
<?
	//$query="SELECT * FROM re_cartelle_pianificazione WHERE id_cartella=$idcartella order by id_impegnativa";
	//echo($query);
	$rs = mssql_query($query1, $conn);
	$old_impegnativa="";
	$open=0;
	$cognome="";
	$nome="";
	$old_cartella="";
	while($row=mssql_fetch_assoc($rs)){
		$id_impegnativa=$row['id_impegnativa'];		
		$idmodulo=$row['id_modulo_padre'];		
		$idmodulo_id=$row['idmodulo_id'];
		$nome_modulo=$row['modello'];
		$cod_cartella=$row['codice_cartella'];	
		$replica=$row['replica'];
		$scadenza=$row['scadenza'];		
		$obbligatorio=$row['obbligatorio'];				
		$ultima_compilazione=formatta_data($row['ultima_compilazione']);		
		$data_osservazione=$row['data_fissa'];
		if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
			$ultima_compilazione=formatta_data($row['ultima_compilazione']);
		}else{
			$ultima_compilazione=formatta_data($data_osservazione);
		}
		
		$data_ins=$row['datains'];
		$nome_medico=$row['operatore'];
		$prot_auth_asl=$row['ProtAutorizAsl'];
		$data_auth_asl=formatta_data($row['DataAutorizAsl']);
		$normativa=$row['normativa'];
		$regime=$row['regime'];
		$num_istanze=$row['num_istanze'];
		$versione=$row['versione'];

	
	if(($old_cartella!=$row['codice_cartella'])){
		if($open) print("</tbody></table>");
		$cognome=$row['Cognome'];
		$nome=$row['Nome'];
		$old_cartella=$row['codice_cartella']?>
		<div class="titoloalternativo">
			<h1>paziente: <?=$cognome." ".$nome?></h1>
		</div>
		<div class='titolo_pag'>
		 cartella clinica n. <strong><?=$cod_cartella?>/<?=convert_versione($versione); $cart=$cod_cartella."/".convert_versione($versione);?></strong> - normativa: <strong><?=$normativa?> - <?=$regime?></strong>
		 </div>
		<?
		/*if($id_impegnativa!=0){	
			echo("<div class='titolo_pag'>");
			echo("Impegnativa prot. <strong>".$prot_auth_asl."</strong> del <strong>".$data_auth_asl."</strong> - normativa: <strong>".$normativa."</strong>");
			echo("</div>");
		} */?>
		<table id="table" class="tablesorter" cellspacing="1"> 
			<thead> 
				<tr> 
					<th>modulo</th> 
					<th>Prot Autoriz</th>
					<th>Data Autoriz</th>									
					<th>figura responsabile</th>
					<th style="width:5%">tipo</th> 					 
					<th>istanze</th> 
					<th>ultima compilazione</th> 											
				</tr> 
		</thead> 
		<tbody> 			
		<?
		//$old_impegnativa=$id_impegnativa;
		$open=1;
	}	
		$img="";												
		if ($obbligatorio=='o')	{
			$img="spunto.png";
			$tipo="OBB/";
			}else{
			$tipo="FAC/";
			}
		if(($replica==1)or($replica==3)){
			$tipo.="M";
			}else{
			$tipo.="U";
		}							
			?>
			<tr> 
			 <td><?=$nome_modulo?></td>
			 <td><?=$prot_auth_asl?></td>
			 <td><?=$data_auth_asl?></td>			 
			 <td><?=$nome_medico?></td>
			 <td><?=$tipo?></td>			 
			 <td><?=$num_istanze?></td> 			 
			 <td><?=$ultima_compilazione?></td> 						
			</tr>		
	<?
	}
		mssql_free_result($rs);
	?>	

<?// footer_paginazione($conta);?>

</div></div>




<?
exit(0);

?>

