<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
		<style>
		body {
            background-color: black;
            color: #00CC00;
        }   
		
		</style>
    </head>
    <body>
        <?php
		$nre = $_GET['nre'];
		$cfAssistito = $_GET['cf'];
		$idImp = $_GET['idImp'];
		$op_type = $_GET['type'];
		$debug = $_GET['debug'];
		
		$giaincarica = 0;
        require 'sogeiClient.php';
		require('../include/reserved/config.rdm.php');
        error_reporting(E_ALL);
		if($debug == -1)
			ini_set('display_errors', 1);
		else 
			ini_set('display_errors', 0);
        $sogei = new SogeiClient();
		//TEST BEGINS
		//$nre = '1500A4000040512';
        //$cfAssistito = 'PNIMRA70A01H501P';
        //TEST ENDS
		
		
		//echo 'Contatto Sogei S.p.A, attendere...';
		if($op_type == OP_PRENDI_IN_CARICO)
			$sogei ->visualizzaErogato($nre, $cfAssistito, $idImp, $giaincarica, $debug);		
		elseif ($op_type == OP_RILASCIA)
			$sogei ->rilasciaErogato($nre,$cfAssistito,$idImp);
		elseif ($op_type == OP_VISUALIZZA)
		{
			$giaincarica = 1;
			$sogei ->visualizzaErogato($nre, $cfAssistito, $idImp, $giaincarica,$debug);
		}
		elseif($op_type == OP_EROG_TOTALE_PARAM)
		{
			
			$sogei ->invioErogato($nre,$cfAssistito,$idImp,0);
		}
		elseif($op_type == OP_EROG_PARZIALE)
		{
			
			$sogei ->invioErogato($nre,$cfAssistito,$idImp,1);
		}
		
		elseif($op_type == OP_EROG_ANNULLAMENTO)
		{
		
			$sogei ->annullaErogato($nre,$cfAssistito,$idImp,null,null);
		}
			
		
		
		//print_r($visualizzato);
        
		?>
</html>
