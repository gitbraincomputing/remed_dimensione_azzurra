<?


include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');

$conn = db_connect();

function runSQL($rsql) {

global $conn;

	$db['default']['hostname'] = "localhost";
	$db['default']['username'] = '';
	$db['default']['password'] = "";
	$db['default']['database'] = "";
	
	$db['live']['hostname'] = 'localhost';
	$db['live']['username'] = '';
	$db['live']['password'] = '';
	$db['live']['database'] = '';
	
	$active_group = 'default';
	
	$base_url = "http://".$_SERVER['HTTP_HOST'];
	$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
	if (strpos($base_url,'webplicity.net')) $active_group = "live";

	$connect = mysql_connect($db[$active_group]['hostname'],$db[$active_group]['username'],$db[$active_group]['password']) or die ("Error: could not connect to database");
	$db = mysql_select_db($db[$active_group]['database']);
	
	$result = mysql_query($rsql) or die ($rsql);
	return $result;
	mysql_close($connect);
}

function countRec($tname,$conn) {
	
	$query = "SELECT count(idmodulo) as num FROM $tname ";
	$result = mssql_query($query, $conn);
	
	while ($row = mssql_fetch_assoc($result)) {
		return $row['num'];
	}	
}
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];

if (!$sortname) $sortname = 'nome';
if (!$sortorder) $sortorder = 'desc';

if ($sortorder=='desc')
$sortorder1='asc';
else
$sortorder1='desc';


$sort1 = "ORDER BY $sortname $sortorder1";
$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 10;

$start = (($page-1) * $rp);

$z=$start+$rp;

$limit = "LIMIT $start, $rp";
$limit="";

$qtype = $_POST['qtype'];
/*
$query = $_POST['query'];
$where = "";
if ($query) $where = " WHERE $qtype LIKE '%$query%' ";
*/
/*where per lettere */

if($_POST['query']!=''){
	$where = "WHERE ".$qtype." LIKE '".$_POST['query']."%' ";
} else {
	$where ='';
}
if($_POST['letter_pressed']!=''){
	$where = "WHERE ".$qtype." LIKE '".$_POST['letter_pressed']."%' ";	
}
if($_POST['letter_pressed']=='#'){
	$where = "WHERE ".$qtype." REGEXP '[[:digit:]]' ";
}

//$sql = "SELECT iso,name,printable_name,iso3,numcode FROM country $where $sort $limit";



//$query = "SELECT idutente,cognome, nome, datanascita,luogonascita,provnascita,codicefiscale FROM utenti $where $sort $limit";
$query_base="SELECT TOP $z id,nome, descrizione from moduli $where $sort";
$query="SELECT TOP 100 PERCENT * FROM (SELECT TOP $rp * FROM ($query_base) AS FOO  $where $sort1) BAR $where $sort";


//$result = runSQL($sql);
$result = mssql_query($query, $conn);


$total = countRec("moduli $where",$conn);


/*header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-type: text/x-json");
*/
$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;
while ($row = mssql_fetch_assoc($result)) {
	if ($rc) $json .= ",";
	$json .= "\n{";
	$json .= "id:'".$row['id']."',";
	$json .= "cell:['".$row['id']."'";
	$json .= ",'".addslashes($row['nome'])."'";
	$json .= ",'".addslashes($row['descrizione'])."']";

	$json .= "}";
	$rc = true;		
}
$json .= "]\n";
$json .= "}";
echo $json;
?>