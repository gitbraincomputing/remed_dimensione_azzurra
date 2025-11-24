<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 55;
$tablename = 'utenti';
$mod_type=3;
include_once('include/function_page.php');

function get_range($idrange)
{
$nome_select=$_REQUEST['name'];
$idriga=str_replace("limite_normativo","",$nome_select);
$conn = db_connect();


$query="select * from test_clinici_range_details where idrange=$idrange and stato=1";
$rs = mssql_query($query, $conn);
?>
<select onchange="javascript:cambia_range(this,'<?=$idriga?>');" name="<?=$nome_select?>" id="<?=$nome_select?>">
<?
while($row = mssql_fetch_assoc($rs))
{   
$idrange_detail=$row['idrange_detail'];
$descrizione=$row['descrizione'];
$valore_da=$row['valore_da'];
$valore_a=$row['valore_a'];
$tipo_dato=$row['tipo_dato'];

$class="";
if ($tipo_dato==1)
$class="integer";
elseif($tipo_dato==2)
$class="data_all";
elseif($tipo_dato==3)
$class="";
elseif($tipo_dato==4)
$class="float";


		?>
		<option rel="<?=$class?>" value="<?=$idrange_detail?>"><?=$descrizione." ".$valore_da." - ".$valore_a?></option>
		<?
}
?>
</select>
<?
exit();
}

if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		
				get_range($_REQUEST['idrange']);
			
		
		}
	
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>