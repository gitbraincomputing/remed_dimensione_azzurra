<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 61;
$tablename = 'utenti';
$mod_type=3;
include_once('include/function_page.php');

function get_range($idrange)
{
$test_ramificato=$_REQUEST['test_ramificato'];
$idriga=$_REQUEST['idriga'];
$conn = db_connect();
$nome_select="test_clinico_scelto_sel".$idriga;

$query="select * from test_clinici where stato=1 and cancella='n' and test_ramificato=$test_ramificato order by nome asc";
$rs = mssql_query($query, $conn);
?>
<select name="<?=$nome_select?>" id="<?=$nome_select?>">
<?
while($row = mssql_fetch_assoc($rs))
{   
$idtest=$row['idtest'];
$nome=strtolower($row['nome']);



		?>
		<option  value="<?=$idtest?>"><?=$nome?></option>
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