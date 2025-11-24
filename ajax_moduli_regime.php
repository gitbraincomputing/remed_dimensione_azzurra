<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$idnorm=$_REQUEST['idnorm'];
$conn = db_connect();
?>
<select  class="scrittura" name="moduli_regime" id="moduli_regime">
	<option selected="" value="0">tutti</option>
	<?
	$query="SELECT * FROM re_moduli_regimi WHERE (idregime = $idnorm) order by nome ASC";
	$rs_op = mssql_query($query, $conn);
	while($row_op=mssql_fetch_assoc($rs_op)){
	?>
		<option <?if($idmodulo==$row_op['idmodulo']) echo("selected")?> value="<?=$row_op['idmodulo']?>"><?=$row_op['nome']?></option>
	<?}	?>
</select>

