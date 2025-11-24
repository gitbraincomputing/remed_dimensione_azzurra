<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_paziente=$_REQUEST['id_paziente'];
$id=$_REQUEST['id'];
$id_cartella=$_REQUEST['id_cartella'];

if($id=="1"){
$conn = db_connect();
$query = "SELECT * FROM utenti WHERE (IdUtente = $id_paziente)";	
$rs = mssql_query($query, $conn);
$row = mssql_fetch_assoc($rs);				
$paziente=pulisci_lettura("<strong>".$row['Cognome']." ".$row['Nome'])."</strong><br/>";

?>
<script type="text/javascript">
function get_impegnative(cartella,idutente){
	$.ajax({
	   type: "POST",
		 url: "ajax_dati_paziente_compilatore.php",
		 data: "id_paziente="+idutente+"&id=2&id_cartella="+cartella,
	   success: function(data){			   
		 $("#list_imp").html($.trim(data));				
	   }
	 });
}
</script>
<div class="testo_mask"><?=$paziente?></div>
	<div class="campo_mask" style="float:left;margin-right:10px;" >
	<select id="idcartella" name="cartella" class="scrittura" style="width:140px;" onchange="get_impegnative(this.value,<?=$id_paziente?>);" >
	<option value="0">cartella</option>
		<?
		$x=1;
		$query = "SELECT idutente, codice_cartella, versione, id FROM utenti_cartelle WHERE (idutente = $id_paziente)";	
		$rs = mssql_query($query, $conn);				
		while($row = mssql_fetch_assoc($rs)){			
			?>
			<option value="<?=$row['id']?>" <?if($x==1)echo("selected")?>><?=$row['codice_cartella']."/".convert_versione($row['versione'])?></option>
			<?
		 if($x==1) $id_cartella=$row['id'];
		 $x++;
		 }
		 mssql_free_result($rs);
		 ?>				 
	</select>
</div>
<?
if($id_cartella==""){
?>
<div class="testo_mask">Nessuna Cartella sanitaria presente</div>
<?
exit();
}
$arr_imp=array();
$conn = db_connect();
$query = "SELECT * FROM re_impegnative_cartella WHERE (id_cartella = $id_cartella) order by ording DESC";
$rs = mssql_query($query, $conn);
while($row = mssql_fetch_assoc($rs)){
	array_push($arr_imp, $row['idimpegnativa'].";".$row['data_asl'].";".$row['prot_asl']);
}
if(count($arr_imp)==0){
	$query = "SELECT TOP(1) * FROM re_pazienti_impegnative WHERE (idutente = $id_paziente) ORDER BY DataAutorizAsl DESC";	
	$rs = mssql_query($query, $conn);
	$row = mssql_fetch_assoc($rs);
	array_push($arr_imp, $row['idimpegnativa'].";".$row['DataAutorizAsl'].";".$row['ProtAutorizAsl']);
}

?>
<div class="campo_mask" style="float:left;" id="list_imp">
	<select id="idimpegnativa" name="impegantiva" class="scrittura" style="width:140px;" >
	<option value="0">impegnativa </option>	
	<?
	$x=1;
	foreach ($arr_imp as $value) {			
		$imp=split(";",$value);
		?>
		<option value="<?=$imp[0]?>" <?if($x==1)echo("selected")?>><?=formatta_data($imp[1])." - ".$imp[2]?></option>
		<?
	 $x++;
	 }
	 ?>	 	
	</select>
</div>
<?}
if($id=="2"){
$arr_imp=array();
$conn = db_connect();
$query = "SELECT * FROM re_impegnative_cartella WHERE (id_cartella = $id_cartella) order by ording DESC";
$rs = mssql_query($query, $conn);
while($row = mssql_fetch_assoc($rs)){
	array_push($arr_imp, $row['idimpegnativa'].";".$row['data_asl'].";".$row['prot_asl']);
}
if(count($arr_imp)==0){
	$query = "SELECT TOP(1) * FROM re_pazienti_impegnative WHERE (idutente = $id_paziente) ORDER BY DataAutorizAsl DESC";	
	$rs = mssql_query($query, $conn);
	$row = mssql_fetch_assoc($rs);
	array_push($arr_imp, $row['idimpegnativa'].";".$row['DataAutorizAsl'].";".$row['ProtAutorizAsl']);
}

?>	
<select id="idimpegnativa" name="impegantiva" class="scrittura" style="width:140px;" >
	<option value="0">impegnativa</option>
	<?	
	foreach ($arr_imp as $value) {			
		$imp=split(";",$value);
		?>
		<option value="<?=$imp[0]?>"><?=formatta_data($imp[1])." - ".$imp[2]?></option>
		<?
	 }	 
	 ?>				 
</select>
<?}?>

