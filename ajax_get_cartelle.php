<?


include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_paziente=$_REQUEST['idpaziente'];
$conn = db_connect();
$query="SELECT id,codice_cartella, idutente, versione FROM dbo.utenti_cartelle where idutente=$id_paziente order by versione DESC";
$rs = mssql_query($query, $conn);
$xx=0;
?>
<select name="num_cartella" class="scrittura" onchange="javascript: $('#num_cart').val(this.value);" id="num_cartella">
<option value="0">selezionare una cartella</option>
<?  while($row = mssql_fetch_assoc($rs)){
		$cartella=$row['codice_cartella'];
		if(convert_versione($row['versione'])){
			$cartella.="/".convert_versione($row['versione']);
		}
		$id=$row['id'];	
		$xx++;
	?>
	<option value="<?=$id?>" <?if($xx==1)echo("selected");?>><?=$cartella?></option>
 <?
 }
 mssql_free_result($rs);
 ?>
 </select>
 <script type="text/javascript">
 $(document).ready(function(){
		//CLOSING POPUP
		//Click the x event!
		$('#num_cart').val($('#num_cartella').val());
	});
	
 
 </script>