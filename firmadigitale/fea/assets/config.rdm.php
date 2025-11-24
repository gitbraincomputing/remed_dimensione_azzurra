<?php


if (isset($_REQUEST['usephp']))
{
	if ($_REQUEST['usephp']=='7')
	{
		$serverName = 'SRV-REMED\SQLEXPRESS';
		$database = 'remed';
		$dbuser = 'remed_user';
		$dbpw = 'remeduserpw';

		/* Connect using Windows Authentication. */  
		try  
		{  
		  $conn = new PDO( "sqlsrv:server=$serverName ; Database=$database", $dbuser, $dbpw);  
		  $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		  $val = 1;
		}  
		catch(Exception $e)  
		{   
		  die( print_r( "ERR:" . $e->getMessage() ) );   
		} 
	}
}