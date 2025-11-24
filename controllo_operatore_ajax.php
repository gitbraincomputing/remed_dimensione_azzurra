<?php
//echo("ok");
//exit();
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');

$conn = db_connect();
$result="";

if (isset($_REQUEST['id_ope'])) $id=$_REQUEST['id_ope'];
if (isset($_REQUEST['user'])) {
	$user=$_REQUEST['user'];
	//controllo user
	$query = "SELECT username FROM operatori WHERE (username='".$user."')";
	$rs = mssql_query($query, $conn);
	if (mssql_num_rows($rs)>0) $result.="Username attribuita ad altro operatore.";
	mssql_free_result($rs);
	
}
if (isset($_REQUEST['pwd'])) {
	$pwd=$_REQUEST['pwd'];
	//controllo password
	$pass=array();
	$pass=split(";",$pwd);	
	if($pass[0]!=$pass[1]) $result.="Le password inserite non coincidono.";
}
if (isset($_REQUEST['san'])) {
	$san=$_REQUEST['san'];
	//controllo direttore sanitario
	$sanitario=array();
	$sanitario=split(";",$san);
	if ($sanitario[0]=="si"){
		$mm =substr($sanitario[1],0,2);
		$gg = substr($sanitario[1],3,2);
		$aa = substr($sanitario[1],6,4);
		$sanitario[1]=$gg."/".$mm."/".$aa;			
		$query = "SELECT tipo, data_inizio, data_fine, uid FROM dbo.operatori_direttore WHERE (tipo = '1') AND (data_inizio <= CONVERT(DATETIME, '".$sanitario[1]."', 102)) AND (data_fine >= CONVERT(DATETIME, '".$sanitario[1]."',102))";
		if ($id!="") $query.="AND (uid <> $id)";
		$rs = mssql_query($query, $conn);
		if (mssql_num_rows($rs)>0) $result.="Direttore sanitario esistente per l'intervallo definito.";
		mssql_free_result($rs);
	}
}
if (isset($_REQUEST['tec'])) {
	$tec=$_REQUEST['tec'];
	//controllo direttore tecnico
	$tecnico=array();
	$tecnico=split(";",$tec);
	if ($tecnico[0]=="si"){
		$mm =substr($tecnico[1],0,2);
		$gg = substr($tecnico[1],3,2);
		$aa = substr($tecnico[1],6,4);
		$tecnico[1]=$gg."/".$mm."/".$aa;	
		$query = "SELECT data_inizio, data_fine, tipo, uid FROM dbo.operatori_direttore GROUP BY data_inizio, data_fine, tipo, uid HAVING (CONVERT(DATETIME, '".$tecnico[1]."', 102)>=data_inizio) AND (CONVERT(DATETIME, '".$tecnico[1]."', 102)<=data_fine) AND (tipo ='2')";
		if ($id!="") $query.="AND (uid <> $id)";
		$rs = mssql_query($query, $conn);
		if (mssql_num_rows($rs)>0) $result.="Direttore tecnico esistente per l'intervallo definito.";
		mssql_free_result($rs);
	}
}

if ($result!="")
	echo($result);
	else
	echo("ok");
