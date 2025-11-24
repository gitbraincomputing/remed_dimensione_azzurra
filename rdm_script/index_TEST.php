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
				
		$giaincarica = 0;
        require 'sogeiClient_TEST.php';
		require('../include/reserved/config.rdm.test.php');
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $sogei = new SogeiClient();
		//TEST BEGINS
		$nre = '1500A4000056314';
        $cfAssistito = 'PNIMRA70A01H501P';
		
        //TEST ENDS
		
		
		//echo 'Contatto Sogei S.p.A, attendere...';
		if($op_type == OP_PRENDI_IN_CARICO)
			$sogei ->visualizzaErogato($nre, $cfAssistito, $idImp, $giaincarica);		
		else if ($op_type == OP_RILASCIA)
			$sogei ->rilasciaErogato($nre,$cfAssistito,$idImp);
		else if ($op_type == OP_VISUALIZZA)
		{
			$giaincarica = 1;
			$sogei ->visualizzaErogato($nre, $cfAssistito, $idImp, $giaincarica);
		}
		else if($op_type == OP_EROG_TOTALE_PARAM)
			$sogei ->invioErogato($nre,$cfAssistito,$idImp, false);		
		else if($op_type == OP_EROG_PARZIALE_PARAM)		
			$sogei ->invioErogato($nre,$cfAssistito,$idImp, true);
		
			
		
		
		//print_r($visualizzato);
        
		?>
</html>
