<?php
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');


//importazione delle impegnativa
//importa_091imp();

//importazione dei dati aggiuntivi delle impegnative
//importa_addinfo_imp();

//importazione dei trattamenti alias ricettecodice
//importa_091tra();

//importazione delle presenze relative a ricettecodice
//importa_presenze_imp();

function importa_091imp(){
$myFile = "import/fkt/101imp.txt";
$fh = fopen($myFile, 'r');
while($record = trim(fgets($fh))){
	$idutente="";
	$codimp="";
	$cod_med="";
	$data_pren="";
	$data_imp="";
	$distretto="";
	$riferimento="";
	$prot_imp="";
	$prot_imp_rosso="";
	echo $record."<br/>";
	$codimp=substr($record, 0, 5);
	$prot_imp=trim(substr($record, 56, 5));
	$prot_imp_rosso=trim(substr($record,91, 16));
	$idutente=substr($record, 49, 5);
	$cod_med=substr($record, 74, 6);
	$data_pren=substr($record, 88, 2)."/".substr($record, 86, 2)."/20".substr($record, 84, 2);
	$data_imp=substr($record, 9, 2)."/".substr($record, 7, 2)."/20".substr($record, 5, 2);
	$distretto=substr($record, 11, 2);
	$riferimento=substr($record, 90, 19);
	echo($idutente."<br/>");
	echo($codimp."<br/>");	
	echo($data_pren."<br/>");
	echo($data_imp."<br/>");	
	echo($riferimento."<br/>");
	$conn = db_connect();
	$distretto=$distretto*1;
	$query="SELECT codice,asl FROM _asl_fkt where codice='$distretto'";
	echo($query);
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	if(($distretto==80)or($distretto==81)or($distretto==82))
		$distretto="DS12";
		else
		$distretto=$row['asl'];		
	$query="SELECT * FROM prescrittori where codice_asl='$cod_med'";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$cod_med=$row['IdPrescrittore'];
	$distretto=substr($distretto, 0, 2)." ".substr($distretto, 2, 2);
	$query="SELECT * FROM distretti where DescrizioneDistretto='$distretto'";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$distretto=$row['IdDistretto'];
	$asl=$row['CodiceASL'];	
	//echo($cod_med."<br/>");
	$idutente=$idutente*1;
	if($cod_med=="") $cod_med=125;
	if($prot_imp_rosso=="") $prot_imp_rosso=$prot_imp;
	if($prot_imp!=""){
		$query="INSERT INTO _impegnative_fkt (codice_paziente,id_imp_fkt,cdc,Normativa,RegimeAssistenza,DataPrescrizione,ProtPrescrizione,MedicoPrescrittore,DataAutorizAsl,ProtAutorizAsl,asl,distretto,riferimento,esenzione,DataInizioTrattamento,ticket,tipologiamedico)
	VALUES('$idutente','$codimp','1','2','0','$data_pren','$prot_imp','$cod_med','$data_imp','$prot_imp_rosso','$asl','$distretto','$riferimento','1','$data_pren','0','10')";
		echo($query."<br/>");
		mssql_query($query, $conn);
	}
}
fclose($fh);
echo("<br/>fine");
}

function importa_091tra(){
$myFile = "import/fkt/101tra.txt";
$fh = fopen($myFile, 'r');
while($record = trim(fgets($fh))){	
	$codimp="";
	$codtrat="";
	$quantita="";
	$importo="";	
	echo $record."<br/>";	
	$codimp=(substr($record, 0, 5))*1;
	$codtrat=substr($record, 7, 4);
	$quantita=substr($record, 27, 2);
	$importo=substr($record, 20, 7);
	echo($codimp."<br/>");
	echo($codtrat."<br/>");	
	echo($quantita."<br/>");
	echo($importo."<br/>");	
	$importo=($importo*1)/100;
	$conn = db_connect();
	
	$query="SELECT * FROM _impegnative_fkt where id_imp_fkt='$codimp'";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$codimp=$row['idimpegnativa'];
		
	$query="SELECT dbo.tipologia.codice, dbo.tipologia.idregime, dbo.normativa.idnormativa,dbo.tipologia.idtipologia
			FROM  dbo.tipologia INNER JOIN
                      dbo.regime ON dbo.tipologia.idregime = dbo.regime.idregime INNER JOIN
                      dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa
			WHERE     (dbo.normativa.idnormativa = 2) AND (dbo.tipologia.codice = '$codtrat')";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$codtrat=$row['idtipologia'];
	$regime=$row['idregime'];
	
	echo($codimp."<br/>");
	echo($codtrat."<br/>");
	echo($regime."<br/>");
		
	if(($codimp!="")and($regime!="")){
		$query="UPDATE impegnative SET RegimeAssistenza='$regime' WHERE idimpegnativa=$codimp";
		echo($query."<br/>");
		mssql_query($query, $conn);
	}
	
	
	$query="INSERT INTO RicetteCodici (IdRicetta,CodicePrestazione,CodiceImporto,QuantitaRichieste)
	VALUES('$codimp','$codtrat','$importo','$quantita')";
	echo($query."<br/>");
	//mssql_query($query, $conn);
}
fclose($fh);
echo("<br/>fine");
}
                       
function importa_addinfo_imp(){

//diagnosi
$myFile = "import/fkt/101dia.txt";
$fh = fopen($myFile, 'r');
while($record = trim(fgets($fh))){	
	$codimp="";
	$diagnosi="";	
	echo $record."<br/>";	
	$codimp=substr($record, 0, 5);
	$diagnosi=substr($record, 5, 60);	
	//echo($codimp."<br/>");
	//echo($diagnosi."<br/>");		
	$diagnosi=pulisci($diagnosi);
	$codimp=$codimp*1;
	$conn = db_connect();	
	$query="SELECT * FROM _impegnative_fkt where id_imp_fkt='$codimp'";
	echo($query);
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$codimp=$row['idimpegnativa'];
	if(($codimp!="")and($diagnosi!="")){
		$query="UPDATE _impegnative_fkt SET Diagnosi='$diagnosi' WHERE idimpegnativa=$codimp";
		echo($query."<br/>");
		mssql_query($query, $conn);
	}
}
fclose($fh);

//esenzione
$myFile = "import/fkt/101ini.txt";
$fh = fopen($myFile, 'r');
while($record = trim(fgets($fh))){	
	$codimp="";
	$diagnosi="";	
	echo $record."<br/>";	
	$codimp=trim(substr($record, 0, 5));
	$esenzione=pulisci(trim(substr($record, 12, 22)));	
	//echo($codimp."<br/>");
	//echo($esenzione."<br/>");		

	$conn = db_connect();	
	$query="SELECT * FROM _impegnative_fkt where id_imp_fkt='$codimp'";
	//echo($query);
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$codimp=$row['idimpegnativa'];
	
	if(($codimp!="")and($esenzione!="")){
		$query="SELECT * FROM esenzione_codici where CodiceEsenzione='$esenzione'";
		//echo($query);
		$rs = mssql_query($query, $conn);
		$row=mssql_fetch_assoc($rs);
		$id_esenzione=$row['IdEsenzione'];
			
		if($id_esenzione!=""){
			$query="UPDATE _impegnative_fkt SET esenzione='2', esenzionecodice='$id_esenzione' WHERE idimpegnativa=$codimp";
			echo($query."<br/>");
			mssql_query($query, $conn);
		}
	}
}
fclose($fh);
echo("<br/>fine");
}

function importa_presenze_imp(){
//presenze
$myFile = "import/fkt/101sta.txt";
$fh = fopen($myFile, 'r');
while($record = trim(fgets($fh))){	
	$codimp="";
	$diagnosi="";	
	echo $record."<br/>";	
	$codimp=trim(substr($record, 0, 5));
	$dataprest=substr($record, 9, 2)."/".substr($record, 7, 2)."/20".substr($record, 5, 2);
	$codtrat=trim(substr($record, 13, 4));
	//echo($codimp."<br/>");
	
	$conn = db_connect();
	//$codimp=$codimp*1;	
	$query="SELECT * FROM _impegnative_fkt where id_imp_fkt='$codimp'";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$codimp=$row['idimpegnativa'];
	
	$query="SELECT dbo.tipologia.codice, dbo.tipologia.idregime, dbo.normativa.idnormativa,dbo.tipologia.idtipologia
			FROM  dbo.tipologia INNER JOIN
                      dbo.regime ON dbo.tipologia.idregime = dbo.regime.idregime INNER JOIN
                      dbo.normativa ON dbo.regime.idnormativa = dbo.normativa.idnormativa
			WHERE     (dbo.normativa.idnormativa = 2) AND (dbo.tipologia.codice = '$codtrat')";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$codtrat=$row['idtipologia'];
	
	$query="SELECT * FROM RicetteCodici where IdRicetta='$codimp' and CodicePrestazione='$codtrat'";
	echo($query."<br/>");
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	$codPre=$row['IdRicettaCodice'];
	echo("codPRE: ".$codPre."<br/>");
	
	$query="INSERT INTO RicettePresenze (IdRicettaCodice,DataPrestazione,QuantitaErogate,DataInserimento)
	VALUES('$codPre','$dataprest','1','$dataprest')";
	echo($query."<br/>");
	mssql_query($query, $conn);
}
fclose($fh);
echo("<br/>fine");
}

?>

