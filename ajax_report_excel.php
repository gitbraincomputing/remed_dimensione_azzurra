<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');

$destination_path =MODELLI_WORD_DEST_PATH;
$gruppo=$_SESSION['UTENTE']->get_gid();
if (isset($_REQUEST['ngid'])) 
	$gid=$_REQUEST['ngid'];
	else
	$gid=0;

srand((double)microtime()*1000000);  
$random=rand(0,999999999999999);

$query=$_SESSION['query_ex'];
$fun=$_REQUEST['fun'];
$tbl="";
switch ($fun) {
	case "1":
		$nome_file=lista_attesa($query);			
		break;
	case "2":
		$nome_file=operazioni_stampe($query);			
		break;
	case "3":
		$nome_file=lista_primo_tratt($query);			
		break;
	case "4":
		$nome_file=lista_dt($query);			
		break;
	case "6":
		$nome_file=report_impegnative($query);			
		break;
	case "7":
		$nome_file=report_impegnative_aperte($query);			
		break;
	case "8":
		$nome_file=operazioni_sgq($query);			
		break;
	case "9":
		$nome_file=obiettivi_fkt($query);			
		break;
	
}
 
header("Content-type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=".$nome_file); 
header("Content-length: ". strlen($tbl)); 
header("Content-Transfer-Encoding: BinARY");
echo($tbl);	

function obiettivi_fkt($query){
	global $tbl;
	$tbl="<h3>Elenco Impegnative aperte</h3>";
	$tbl.="<table id='table' cellspacing='1'>";
	$tbl.="<thead><tr>   
		<th style='width:8%'>Data Rilev.</th>
		<th style='width:10%'>Cognome</th> 
		<th style='width:10%'>Nome</th> 
		<th style='width:5%'>Sesso</th>
		<th style='width:8%'>Data Nascita</th>
		<th style='width:10'>Obiettivo 1 </th>
		<th style='width:10%'>Obiettivo 2 </th>
		<th style='width:10%'>Obiettivo 3 </th>
		<th style='width:10%'>Obiettivo 4 </th>
		<th style='width:10%'>Obiettivo 5 </th>
		<th style='width:10%'>Valore Finale</th>	 	
	</tr> 
	</thead> 
	<tbody>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);			
	while($row = mssql_fetch_assoc($rs)){
		$media=$media;
		$totale_fkt++;
		if(($idutente!=$row['IdUtente'])and($idutente!="")){
		//if(($id_istanza_testata!=$row['id_istanza_testata'])and ($id_istanza_testata!="")){
		$totale_fkt++;
		switch($ob1){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
		}
		switch($ob2){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
			
		}
		switch($ob3){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
			
		}
		switch($ob4){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
			
		}
		switch($ob5){
			case "No":
				$vObb+=0;
			break;
			case "Parzial.":
				$vObb+=1.5;
			break;
			case "Si":
				$vObb+=2;
			break;
			
		}
		if($vObb>0)
			$media=round(($vObb/($nObb*2))*100);
			else
			$media=0;
		
		if($media==0)
			$no++;
			else if($media<50)
				$parz++;
				else 
				$si++;
				
		$media=$media." %";

		$tbl.="<tr> 
		<td>$data_rilevazione</td>
		<td>$cognome</td> 
		<td>$nome</td> 
		<td>$sesso</td>
		<td>$data_nascita</td>
		<td>$ob1</td> 
		<td>$ob2</td>
		<td>$ob3</td>
		<td>$ob4</td>	
		<td>$ob5</td>	
		<td>$media</td>	 	 
			</tr>"; 		
		$nObb=0;
		$vObb=0;	
		if(($idutente!=$row['IdUtente'])and($idutente!="")){
			$tbl.="<tr><td></td></tr>";
			$totale++;
			$media=0;	
		}
		}
		$id_istanza_testata=$row['id_istanza_testata'];	
		$idutente=$row['IdUtente'];		
		$cognome=pulisci_lettura($row['Cognome']);
		$nome=pulisci_lettura($row['Nome']);		
		$data_nascita=formatta_data($row['DataNascita']);
		$data_rilevazione=formatta_data($row['datains']);

		if($row['Sesso']==1) 
			$sesso="uomo";
			else
			$sesso="donna";

		if(($row['idcampo']==17103)or($row['idcampo']==17205)or($row['idcampo']==17443)) {$ob1=$row['valore_combo']; if($ob1!="")$nObb++;}
		if(($row['idcampo']==17106)or($row['idcampo']==17208)or($row['idcampo']==17446)) {$ob2=$row['valore_combo']; if($ob2!="")$nObb++;}
		if(($row['idcampo']==17109)or($row['idcampo']==17211)or($row['idcampo']==17449)) {$ob3=$row['valore_combo']; if($ob3!="")$nObb++;}
		if(($row['idcampo']==17112)or($row['idcampo']==17214)or($row['idcampo']==17452)) {$ob4=$row['valore_combo']; if($ob4!="")$nObb++;}
		if(($row['idcampo']==17115)or($row['idcampo']==17217)or($row['idcampo']==17455)) {$ob5=$row['valore_combo']; if($ob5!="")$nObb++;}
	}
	$tbl.="</tbody></table>";
	return "report_fkt.xls";
}

function report_impegnative_aperte($query){
	global $tbl;
	$tbl="<h3>Elenco Impegnative aperte</h3>";
	$tbl.="<table id='table' cellspacing='1'>";
	$tbl.="<thead><tr>   
		<th>Regime/Normativa</th>
		<th>Codice</th> 
		<th>Cognome</th> 
		<th>Nome</th> 
		<th>Data Autorizz. ASL</th>	
		<th>Prot Autorizz. ASL</th>
		<th>Med. Responsabile</th>
		<th>Case Manager</th>	
		<th>Protocollo DT</th> 	
	</tr> 
	</thead> 
	<tbody>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);			
	while($row = mssql_fetch_assoc($rs)){
		$idutente=$row['IdUtente'];
		$cognome=pulisci_lettura($row['Cognome']);
		$nome=pulisci_lettura($row['Nome']);
		$regime=$row['normativa']." - ".$row['regime'];	
		$data_aut=formatta_data($row['DataAutorizAsl']);
		$prot_aut=$row['ProtAutorizAsl'];
		$med_resp=$row['medico_responsabile'];
		$case_man=$row['case_manager'];
		$protocollo=pulisci_lettura($row['protocollo']);
		$tbl.="<tr>
		<td>$regime</td>
		 <td>$idutente</td> 
		 <td>$cognome</td> 
		 <td>$nome</td> 
		 <td>$data_aut</td> 
		 <td>$prot_aut</td> 
		 <td>$med_resp</td>
		 <td>$case_man</td>
		 <td>$protocollo</td>	 	 
			</tr>"; 		
	}
	$tbl.="</tbody></table>";
	return "impegnative_aperte.xls";
}


function report_impegnative($query){
	global $tbl;
	$tbl="<h3>Elenco Impegnative attive</h3>";
	$tbl.="<table id='table' cellspacing='1'>";
	$tbl.="<thead><tr>   
		<th>Regime/Normativa</th>
		<th>Codice</th> 
		<th>Cognome</th> 
		<th>Nome</th> 
		<th>Data Autorizz. ASL</th>	
		<th>Prot Autorizz. ASL</th>
		<th>Data Fine Tratt.</th>
		<th>Frequenza</th> 	
	</tr> 
	</thead> 
	<tbody>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);			
	while($row = mssql_fetch_assoc($rs)){
		$idutente=$row['IdUtente'];
		$cognome=pulisci_lettura($row['Cognome']);
		$nome=pulisci_lettura($row['Nome']);
		$regime=$row['normativa']." - ".$row['regime'];	
		$data_aut=formatta_data($row['DataAutorizAsl']);
		$prot_aut=$row['ProtAutorizAsl'];
		$data_fine=formatta_data($row['DataFineTrattamento']);
		$freq=$row['ggfrequenza'];
		$tbl.="<tr>
		<td>$regime</td>
		 <td>$idutente</td> 
		 <td>$cognome</td> 
		 <td>$nome</td> 
		 <td>$data_aut</td> 
		 <td>$prot_aut</td> 
		 <td>$data_fine</td>
		 <td>$freq</td>		 	 
			</tr>"; 		
	}
	$tbl.="</tbody></table>";
	return "impegnative_attive.xls";
}

function lista_attesa($query){
	global $tbl;
	$tbl="<h3>Elenco Pazienti in lista d'attesa</h3>";
	$tbl.="<table id='table' cellspacing='1'>";
	$tbl.="<thead><tr>   
		<th>Codice</th> 
		<th>Cognome</th> 
		<th>Nome</th> 
		<th>Data Aut.</th>
		<th>Protocollo Asl</th>
		<th>Tipologia</th> 
		<th>Regime/Normativa</th>
		<th>Gravita</th> 	
	</tr> 
	</thead> 
	<tbody>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);			
	while($row = mssql_fetch_assoc($rs)){
	$idutente=$row['idutente'];
			$cognome=pulisci_lettura($row['cognome']);
			$nome=pulisci_lettura($row['nome']);		
			$data_nascita=formatta_data($row['data_nascita']);		
			$tipologia=pulisci_lettura($row['tipologia']);
			$prot_asl=$row['prot_asl'];
			$regime=$row['normativa']." - ".$row['regime'];	
			$data_asl=formatta_data($row['data']);
			$gravita=pulisci_lettura($row['gravita']);
			$tbl.="<tr>
			 <td>$idutente</td> 
			 <td>$cognome</td> 
			 <td>$nome</td> 
			 <td>$data_asl</td> 
			 <td>$prot_asl</td> 
			 <td>$tipologia</td>
			 <td>$regime</td>
			 <td>$gravita</td>		 	 
			</tr>"; 		
	}
	$tbl.="</tbody></table>";
	return "lista_attesa.xls";
}

function operazioni_stampe($query){
	global $tbl;
	$tbl="<h3>Report Operazioni di Stampa Moduli</h3>";
	$tbl.="<table id='table' cellspacing='1'>";
	$tbl.="<thead><tr>   
		<th>data / ora</th> 
		<th>operatore</th> 
		<th>modulo</th> 
		<th>paziente</th>
		<th>cartella clinica</th> 	
		</tr></thead> 
	<tbody>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);			
	while($row = mssql_fetch_assoc($rs)){
	$id=$row['id'];
		$cognome_paziente=pulisci_lettura($row['Cognome']);
		$nome_paziente=pulisci_lettura($row['Nome']);
		$operatore=pulisci_lettura($row['operatore']);		
		$data_ora_stampa=formatta_data($row['data_stampa'])." ".$row['ora_stampa'];		
		$modulo=pulisci_lettura($row['modulo']);		
		$nome_file=pulisci_lettura($row['nome_file']);		
		$operatore=pulisci_lettura($row['operatore']);		
		$codice_cartella=pulisci_lettura($row['codice_cartella']);
		$versione=$row['versione'];
		if ($codice_cartella){
			$ver=convert_versione($versione);
			$cartella=$codice_cartella."/".$ver;
		}else
			$cartella="";
		
		$paziente=$cognome_paziente." ".$nome_paziente;		
		$tbl.="<tr> 
		 <td>$data_ora_stampa</td> 
		 <td>$operatore</td> 
		 <td>$modulo</td>		
		 <td>$paziente</td> 
		 <td>$cartella</td>
		 </tr>"; 		
}
	$tbl.="</tbody></table>";
	return "report_stampe.xls";
}

function lista_primo_tratt($query){
	global $tbl;
	$tbl="<h3>Elenco Pazienti con statistiche di primo trattamento</h3>";
	$tbl.="<table id='table' cellspacing='1'>";
	$tbl.="<thead><tr>   
		<th>Codice</th> 
		<th>Cognome</th> 
		<th>Nome</th> 
		<th>Data Ric. Autorizz.</th>	
		<th>Data Primo trattamento</th>
		<th>Diff. primo tratt.</th> 
		<th>Regime/Normativa</th> 	
		</tr></thead> 
	<tbody>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);			
	while($row = mssql_fetch_assoc($rs)){
	$idutente=$row['idutente'];
		$cognome=pulisci_lettura($row['cognome']);
		$nome=pulisci_lettura($row['nome']);		
		$data_nascita=formatta_data($row['data_nascita']);		
		$data_aut=formatta_data($row['data']);
		$data_tra=formatta_data($row['data_trattamento']);
		$regime=$row['normativa']." - ".$row['regime'];	
		if($data_tra!="") 
			$diff=diff_in_giorni($data_tra,$data_aut);
			else
			$diff=" - ";	
		$tbl.="<tr> 
		 <td>$idutente</td> 
		 <td>$cognome</td> 
		 <td>$nome</td> 
		 <td>$data_aut</td> 
		 <td>$data_tra</td> 
		 <td>$diff</td>
		 <td>$regime</td>				 	 
		</tr>"; 		
}
	$tbl.="</tbody></table>";
	return "report_primo_trattamento.xls";
}

function lista_dt($query){
	global $tbl;
	$tbl="<h3>Elenco Pazienti con protocollo DT e Successo del trattamento</h3>";
	$tbl.="<table id='table' cellspacing='1'>";
	$tbl.="<thead><tr>   
		 <th>Codice</th> 
		<th>Cognome</th> 
		<th>Nome</th> 
		<th>Data Ric. Autorizz.</th>
		<th>Tipologia</th>
		<th>Esiste DT</th>
		<th>Successo</th>		
		<th>Data Primo trattamento</th>
		<th>Data Ultimo trattamento</th> 
		<th>Regime/Normativa</th>
		<th>Diagnosi</th> 	
		</tr></thead> 
	<tbody>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);			
	while($row = mssql_fetch_assoc($rs)){
		$idutente=$row['idutente'];
		$cognome=pulisci_lettura($row['cognome']);
		$nome=pulisci_lettura($row['nome']);		
		$data_aut=formatta_data($row['data']);		
		$primo_trat=formatta_data($row['primo_trat']);
		$ultimo_trat=formatta_data($row['ultimo_trat']);
		$regime=pulisci_lettura($row['normativa']." - ".$row['regime']);
		$dt=$row['dt'];
		$su=$row['su'];
		$tipologia=$row['tipologia'];
	    $diagnosi=pulisci_lettura($row['diagnosi']);		
		if($dt=="on") 
			$dt="SI"; 
			else
			$dt="-";
		if($su=="on") 
			$su="SI"; 
			else
			$su="-";
		$tbl.="<tr> 
		 <td>$idutente</td> 
		 <td>$cognome</td> 
		 <td>$nome</td> 
		 <td>$data_aut</td>
		 <td>$tipologia</td>
		 <td>$dt</td>
		 <td>$su</td> 		
		 <td>$primo_trat</td> 
		 <td>$ultimo_trat</td>
		 <td>$regime</td>
		 <td>$diagnosi</td>				 		 
		</tr>";			
}
	$tbl.="</tbody></table>";
	return "report_lista_dt_successo.xls";
}

function operazioni_sgq($query){
	global $tbl;
	$tbl="<h3>Report SGQ</h3>";
	$tbl.="<table id='table' cellspacing='1'>";
	$tbl.="<thead><tr>   
		   <th>stato</th> 
	       <th>operatore</th> 
	       <th>area</th> 
	       <th>modulo sgq</th>
	       <th>scadenza associata</th>
	       <th>data / ora inserimento</th>
		</tr></thead> 
	<tbody>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);
	//	echo($query);
	while($row = mssql_fetch_assoc($rs))   
{
		$stato="";
		$data_scadenza_generata=formatta_data($row['data_scadenza_generata']);
		$diff_in_giorni=diff_in_giorni($data_scadenza_generata,$datadioggi);
		if($diff_in_giorni < 0){
			    $stato='scadenza non rispettata';
		}
		
		$operatore=pulisci_lettura($row['nome']);		
		$area=pulisci_lettura($row['nome_area']);
		$modulo=pulisci_lettura($row['nome_modulo']);
		$data_scadenza_associata=formatta_data($row['data_scadenza_generata']);	
		$id_istanza_testata_sgq=$row['id_istanza_testata_sgq'];
		$data_ora_inserimento="";
		if($id_istanza_testata_sgq!=""){
		    $query_scadenza="SELECT * FROM istanze_testata_sgq WHERE (id_istanza_testata_sgq=$id_istanza_testata_sgq) ";
			$rs_scadenza = mssql_query($query_scadenza, $conn);
			$record_trovati=mssql_num_rows($rs_scadenza);
			if($record_trovati>0){
                $row_scadenza = mssql_fetch_assoc($rs_scadenza);
				$data_ora_inserimento=formatta_data($row_scadenza['datains'])." ".$row_scadenza['orains'];		
			}
		}
		$tbl.="<tr> 
		 <td>$stato</td> 
		 <td>$operatore</td> 
		 <td>$area</td> 
		 <td>$modulo</td>
		 <td>$data_scadenza_associata</td> 
		 <td>$data_ora_inserimento</td>
		 </tr>"; 		
}
	$tbl.="</tbody></table>";
	//echo($tbl);
	return "report_sgq.xls";
}

?>