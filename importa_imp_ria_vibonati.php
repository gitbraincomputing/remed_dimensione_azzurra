<?php
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');


//importazione delle impegnativa
//importa_091aut();

//importazione dei dati aggiuntivi delle impegnative
//importa_addinfo_imp();

//importazione dei trattamenti alias ricettecodice
importa_091presenze();

//99016250022571 20030424122090001 SA2003040716 ESASETTIMANALE D
//9901514|004|444 20040129|197070105  A 20031217|26|01|00 TRISETTIMANALE TRISETTIMANALE D

function importa_091aut(){
$myFile = "import/ria/101aut.txt";
$fh = fopen($myFile, 'r');
while($record = trim(fgets($fh))){
	$idutente="";
	$codimp="";
	$cod_med="";
	$data_pren="";
	$data_imp="";
	$distretto="";
	$riferimento="";
	$menomazione="";
	echo $record."<br/>";
	$idutente=substr($record, 0, 7);	
	$codimp=substr($record, 10, 4);
	$data_imp=substr($record, 26, 2)."/".substr($record, 24, 2)."/".substr($record, 20, 4);
	$data_inizio=substr($record, 45, 2)."/".substr($record, 43, 2)."/".substr($record, 39, 4);
	$regime=substr($record, 37, 2);
	$menomazione=substr($record, 159, 5);
	$num_tratt=substr($record, 28, 3);
	
	switch ($regime){
		case "SM":
			$normativa="1";
			$regime="19";
			break;
		case "S ":
			$normativa="1";
			$regime="19";
			break;
		case "SG":
			$normativa="1";
			$regime="24";
			break;
		case "SA":
			$normativa="1";
			$regime="18";
			break;
		case "D ":
			$normativa="1";
			$regime="4";
			break;
		case "A ":
			$normativa="1";
			$regime="3";
			break;
		case "AG":
			$normativa="1";
			$regime="22";
			break;
		case "E ":
			$normativa="1";
			$regime="25";
			break;
		case "IG":
			$normativa="1";
			$regime="16";
			break;
		case "IM":
			$normativa="1";
			$regime="17";
			break;
		case "IB":
			$normativa="1";
			$regime="15";
			break;
		case "CD":
			$normativa="3";
			$regime="8";
			break;
		default:
			$normativa="0";
			$regime="0";
			break;		
	}	
	
	$conn = db_connect();
	$query="SELECT * FROM menomazioni where codice='$menomazione'";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$idmen=$row['idmen'];
	if($idmen=="")$idmen="65";	
	
	$query="INSERT INTO _impegnative_ria (codice_paziente,id_imp_ria,cdc,Normativa,RegimeAssistenza,DataAutorizAsl,ProtAutorizAsl,DataInizioTrattamento,classemenomazione,tipologiamedico,MedicoPrescrittore,esenzione,NumeroTrattamenti,ticket)
	VALUES('$idutente','$codimp','1',$normativa,$regime,'$data_imp','$codimp','$data_inizio',$idmen,'10','125','1',$num_tratt,'0')";
	echo($query."<br/>");
	mssql_query($query, $conn);
}
fclose($fh);
echo("<br/>fine");
}

function importa_091presenze(){
//$myFile = "import/ria/101pre.txt";
//$myFile = "import/ria/091pre.txt";
//$myFile = "import/ria/081pre.txt";
//$myFile = "import/ria/071pre.txt";
//$myFile = "import/ria/061pre.txt";
//$myFile = "import/ria/051pre.txt";
//$myFile = "import/ria/041pre.txt";
//$myFile = "import/ria/031pre.txt";
$myFile = "import/ria/021pre.txt";
//$myFile = "import/ria/011pre.txt";
//$myFile = "import/ria/001pre.txt";
//$myFile = "import/ria/991pre.txt";
//*controllo ASSENTE
//091259A SG20010109000080320010109SG162001010906820010109ASSENTE
  
$fh = fopen($myFile, 'r');
while($record = trim(fgets($fh))){	
	$codpaz="";
	$codtrat="";
	$quantita="";
	$importo="";
	$idtrat="";
	$id_imp="";
	$assente="";	
	echo "<br/>".$record."<br/>";	
	$codpaz=substr($record, 18, 7);
	$codtrat=substr($record, 35, 2);
	$data_pre=substr($record, 16, 2)."/".substr($record, 14, 2)."/".substr($record, 10, 4);
	$cod_ope=(substr($record, 45, 3))*1;
	$assente=trim(substr($record, 56, 7));
	//echo($assente."<br/>");	
	//echo($codimp."<br/>");
	//echo($codtrat."<br/>");	
	//echo($quantita."<br/>");
	//echo($importo."<br/>");
	$codpaz=$codpaz*1;	
	$conn = db_connect();
	
	$query="SELECT * FROM _ope_ria where id_ope=$cod_ope";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$operatore=pulisci($row['cognome']." ".$row['nome']." ($cod_ope)");
		
	$query="SELECT * FROM _impegnative_ria where codice_paziente='$codpaz'";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$idutente=$row['idutente'];
	$prot_asl=$row['ProtAutorizAsl'];
	$data_asl=formatta_data($row['DataAutorizAsl']);
	
	$query="SELECT idimpegnativa, idutente, DataAutorizAsl, ProtAutorizAsl, Normativa, RegimeAssistenza
			FROM dbo.impegnative WHERE (idutente = $idutente) AND (DataAutorizAsl = '$data_asl') AND (ProtAutorizAsl = '$prot_asl')";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$id_imp=$row['idimpegnativa'];
	$normativa=$row['Normativa'];
	$regime=$row['RegimeAssistenza'];
			
	$query="SELECT * FROM _terapie_ria where codice_ria='$codtrat' and normativa='$normativa' and regime='$regime'";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$codtrat=$row['codice_remed'];
	
	if($id_imp!=""){
		$query="SELECT * FROM RicetteCodici where IdRicetta='$id_imp' and CodicePrestazione='$codtrat'";
		echo($query."<br/>");
		$rs = mssql_query($query, $conn);
		if($row=mssql_fetch_assoc($rs)){
			$idtrat=$row['IdRicettaCodice'];
			}else{
			$query="INSERT INTO RicetteCodici (IdRicetta,CodicePrestazione)
			VALUES('$id_imp','$codtrat')";
			echo($query."<br/>");
			mssql_query($query, $conn);
			$query="SELECT * FROM RicetteCodici where IdRicetta='$id_imp' and CodicePrestazione='$codtrat'";
			echo($query."<br/>");
			$rs = mssql_query($query, $conn);
			$row=mssql_fetch_assoc($rs);
			$idtrat=$row['IdRicettaCodice'];
			}
		if($assente=="ASSENTE")
			$pres="0";
			else
			$pres="1";
		$query="INSERT INTO RicettePresenze (IdRicettaCodice,DataPrestazione,QuantitaErogate,DataInserimento,Note)
		VALUES('$idtrat','$data_pre','$pres','$data_pre','$operatore')";
		echo($query."<br/>");
		mssql_query($query, $conn);
	}
}
fclose($fh);
echo("<br/>fine");
}
?>