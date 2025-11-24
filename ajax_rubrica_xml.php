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
	case "3":
		$nome_file=lista_primo_tratt($query);			
		break;
	case "4":
		$nome_file=lista_dt($query);			
		break;
	
}
 
header("Content-type: text/xml"); 
header("Content-Disposition: attachment; filename=".$nome_file); 
header("Content-length: ". strlen($tbl)); 
header("Content-Transfer-Encoding: BinARY");
echo($tbl);	

function lista_attesa($query){
	global $tbl;
	$tbl="<?xml version=\"1.0\"?>";
	$tbl.="<AddressBook>";	
	$conn = db_connect();
	$rs = mssql_query($query, $conn);
	$i=1;	
	while($row = mssql_fetch_assoc($rs)){
		$idutente=$row['idutente'];
		$query_tel="SELECT IdUtente, telefono FROM dbo.utenti_residenze GROUP BY IdUtente, telefono HAVING (IdUtente = $idutente)";
		$rs_tel = mssql_query($query_tel, $conn);
		$row_tel=mssql_fetch_assoc($rs_tel);		
		$telefono=str_replace("/","",$row_tel['telefono']);		
		if($telefono!=""){
			$cognome=pulisci_lettura($row['cognome']);
			$nome=pulisci_lettura($row['nome']);		
			$data_nascita=formatta_data($row['data_nascita']);		
			$tipologia=pulisci_lettura($row['tipologia']);
			$prot_asl=$row['prot_asl'];		
			$regime=$row['normativa']." - ".$row['regime'];	
			$data_asl=formatta_data($row['data']);
			$gravita=pulisci_lettura($row['gravita']);
			$tbl.="<Contact>";
			$tbl.="<LastName>$cognome</LastName>
				<FirstName>$nome</FirstName>";
			$tbl.="<Phone>";
			$tbl.="<phonenumber>$telefono</phonenumber>
				<accountindex>$i</accountindex>";
			$tbl.="</Phone>";
			$tbl.="</Contact>";
			$i++;
		}			
	}
	$tbl.="</AddressBook>";
	return "phonebook.xml";
}

function lista_primo_tratt($query){
	global $tbl;
	$tbl="<?xml version=\"1.0\"?>";
	$tbl.="<AddressBook>";
	$conn = db_connect();
	$rs = mssql_query($query, $conn);
	$i=1;		
	while($row = mssql_fetch_assoc($rs)){
	$idutente=$row['idutente'];
	$query_tel="SELECT IdUtente, telefono FROM dbo.utenti_residenze GROUP BY IdUtente, telefono HAVING (IdUtente = $idutente)";
		$rs_tel = mssql_query($query_tel, $conn);
		$row_tel=mssql_fetch_assoc($rs_tel);		
		$telefono=str_replace("/","",$row_tel['telefono']);		
		if($telefono!=""){
			$cognome=pulisci_lettura($row['cognome']);
			$nome=pulisci_lettura($row['nome']);		
			$data_nascita=formatta_data($row['data_nascita']);		
			$data_aut=formatta_data($row['data']);
			$data_tra=formatta_data($row['data_trattamento']);
			$regime=$row['normativa']." - ".$row['regime'];	
			$tbl.="<Contact>";
			$tbl.="<LastName>$cognome</LastName>
				<FirstName>$nome</FirstName>";
			$tbl.="<Phone>";
			$tbl.="<phonenumber>$telefono</phonenumber>
				<accountindex>$i</accountindex>";
			$tbl.="</Phone>";
			$tbl.="</Contact>";
			$i++; 		
		}
	}
	$tbl.="</AddressBook>";
	return "phonebook.xml";
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
?>