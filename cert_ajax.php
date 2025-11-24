<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$certificato=$_REQUEST['certificato'];
$id=$_REQUEST['id'];

if($id==1){
	$conn = db_connect();
	$certificato=str_replace("-----BEGIN CERTIFICATE-----","",trim($certificato));
	$certificato=str_replace("-----END CERTIFICATE-----","",$certificato);
	$certificato=ereg_replace('[^A-Za-z]',"",$certificato);	
	$query="SELECT nome, username, key_firma FROM operatori WHERE (key_firma LIKE N'%".trim($certificato)."%')";
	//echo($query);
	$result = mssql_query($query, $conn);	
	if($row=mssql_fetch_assoc($result)){
		echo($row['username']);		
		$_SESSION['businesskey']=$certificato;
		$_SESSION['businesskey_inserita']="si";
	}
	mssql_free_result($result);
}

if($id==2){
	if(isset($_SESSION['businesskey'])){
	$conn = db_connect();
	$certificato=str_replace("-----BEGIN CERTIFICATE-----","",trim($certificato));
	$certificato=str_replace("-----END CERTIFICATE-----","",$certificato);
	$certificato=ereg_replace('[^A-Za-z]',"",$certificato);	
	if($certificato==$_SESSION['businesskey']){
		$_SESSION['businesskey_inserita']="si";
		echo(1);}		
	else{
		$_SESSION['businesskey_inserita']="";
		echo(0);
		}
	}else
	echo(0);
}

if($id==3){
	$conn = db_connect();
	$certificato=str_replace("-----BEGIN CERTIFICATE-----","",trim($certificato));
	$certificato=str_replace("-----END CERTIFICATE-----","",$certificato);
	$certificato=ereg_replace('[^A-Za-z]',"",$certificato);	
	$query="SELECT nome, username, key_firma FROM operatori WHERE (key_firma LIKE N'%".trim($certificato)."%')";
	//echo($query);
	$result = mssql_query($query, $conn);	
	if(mssql_num_rows($result)>0)
		echo("0;Attenzione. Businesskey attribuita ad altro utente.");
		else
		echo("1;".$certificato);	
	mssql_free_result($result);
}
	

?>




