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
	
	$query = "SELECT count(idUtente) as num FROM $tname ";
	$result = mssql_query($query, $conn);
	
	while ($row = mssql_fetch_assoc($result)) {
		return $row['num'];
	}	
}
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];

if (!$sortname) $sortname = 'cognome';
if (!$sortorder) $sortorder = 'desc';

if ($sortorder=='desc')
$sortorder1='asc';
else
$sortorder1='desc';


$sort1 = "ORDER BY $sortname $sortorder1";
$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 1000;

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




if($_POST['q']!=''){
	$where = "WHERE cognome LIKE '".$_POST['q']."%' ";
} else {
	$where ='';
}

//$sql = "SELECT iso,name,printable_name,iso3,numcode FROM country $where $sort $limit";



//$query = "SELECT idutente,cognome, nome, datanascita,luogonascita,provnascita,codicefiscale FROM utenti ";

$query="SELECT idutente,cognome, nome, datanascita, dbo.comuni.denominazione AS luogonascita, dbo.comuni.sigla AS provnascita, codicefiscale FROM dbo.comuni INNER JOIN dbo.utenti ON dbo.comuni.id_comune = dbo.utenti.LuogoNascita_id $where";

//echo $query;
//exit();

//$result = runSQL($sql);
$result = mssql_query($query, $conn);


//$total = countRec("utenti $where",$conn);

/*header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-type: text/x-json");
*/
$json1 = "";
$json1 .= "{\n";

$json1 .= "results: [";
$rc = false;
while ($row = mssql_fetch_assoc($result)) {
	if ($rc) $json1 .= ",";
	$json1 .= "\n{";
	$json1 .= "id:'".$row['idutente']."',";
	
	$n=addslashes($row['cognome'])." ".addslashes($row['nome'])." nato a ".addslashes($row['luogonascita'])." il ".addslashes(formatta_data($row['datanascita']));
	
	$json1 .= "name:'".$n."'";
	//$json1 .= "'".addslashes($row['cognome'])."'";
	$json1 .= "}";
	$rc = true;		
}
$json1 .= "]\n";
$json1 .= "}";
echo $json1;
?>