<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_regione=$_REQUEST['id_regione'];

$conn = db_connect();?>
<select name="provincia" class="scrittura">
	<option value="0">selezionare</option>
	<?
	$query = "SELECT dbo.province.nome, dbo.province.id FROM dbo.province INNER JOIN dbo.regioni ON dbo.province.idregione = dbo.regioni.id
WHERE (dbo.regioni.id = $id_regione) order by nome ASC";	
	$rs = mssql_query($query, $conn);				
	while($row = mssql_fetch_assoc($rs)){
		$sel="";
		if ($provincia==$row['id']) $sel="selected";				
		?>
		<option <?=$sel?> value="<?=$row['id']?>"><?=pulisci_lettura($row['nome'])?></option>
		<?
	 }
	 mssql_free_result($rs);
	 ?>				 
</select>
