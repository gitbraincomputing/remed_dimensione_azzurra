<?php
include_once('reserved/config.inc.php');

function db_connect(){
?>
<?php

		
		if(!($sqlconnect = mssql_connect(SERVER_NAME, DB_USER, DB_PASSWORD)))
			die("Il portale  temporaneamente in manutenzione, ci scusiamo per il disagio!");
		if(!($sqldb = mssql_select_db (DB_NAME,$sqlconnect)))
			die("errore selezione db!");
		//$sqlconnect = odbc_connect(DSN,DB_USER,DB_PASSWORD);
		//echo ($sql_connect);
		return $sqlconnect;
	
}



function db_connect_2005(){


$connectionInfo = array("Database"=>DB_NAME,"UID" => DB_USER, "PWD" => DB_PASSWORD);
$conn_2005 = sqlsrv_connect(SERVER_NAME, $connectionInfo);

if( $conn_2005 )
{
    return ($conn_2005);
}
else
{
     echo "Connection could not be established.\n";
     die(print_r( sqlsrv_errors(), true));
}



}


function db_connect_nodelay(){


		//if(!($sqlconnect = mssql_pconnect(SERVER_NAME, DB_USER, DB_PASSWORD)))
			//die("Il portale  temporaneamente in manutenzione, ci scusiamo per il disagio!");
		//if(!($sqldb = mssql_select_db (DB_NAME,$sqlconnect)))
			//die("errore selezione db!");
			//connect to a DSN "myDSN"
			//$sqlconnect = odbc_connect('remed','',''); 
		$sqlconnect = odbc_connect(DSN,DB_USER,DB_PASSWORD);
	//echo ($sql_connect);
		return $sqlconnect;
	
}



	

?>
