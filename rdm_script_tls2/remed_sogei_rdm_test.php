<?php
		

		//die("qui");
		$nre = $_GET['nre'];
		$cfAssistito = $_GET['cf'];
		$idImp = $_GET['idImp'];
		$op_type = $_GET['type'];
				
		$giaincarica = 0;
		
		// echo "<pre>";
        error_reporting(E_ALL);
        ini_set('display_errors',0 );
		
        require 'sogeiClient.php';
		require('config.rdm_test.php');
       
        $sogei = new SogeiClient();
		
		//TEST BEGINS
		//$nre = '1500A4000040512';
        //$cfAssistito = 'PNIMRA70A01H501P';
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
		{
			$dal = $_GET['dal'];
			$al = $_GET['al'];
			ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
		$data = json_decode(file_get_contents('php://input'), true);
			
		//	print_r($data);
			
			$sogei ->invioErogato($nre,$cfAssistito,$idImp,$dal,$al,$data);
		}
			
		
		
		//print_r($visualizzato);
        
		?>

