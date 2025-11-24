<?php
//header("Content-type: text/html; charset=ISO-8859-1");
/****************************************************
file di inclusione
*****************************************************/
include_once('dbengine.inc.php');
include_once('class.User.php');
include_once('class.Struttura.php');
//include_once('session.inc.php');



function firstupper($value){
$len=strlen($value);
if(strpos($value, "'")!=0) {	
	$val_1=substr($value,0,strpos($value, "'")-1)."\'";	
	$val_2=strtoupper(substr($value,strpos($value, "'")+1,1));	
	$val_3=substr($value,strpos($value, "'")+2,$len);
	$value=$val_1.$val_2.$val_3;	
	}
$pos=0;
$i++;
While((strpos($value," ",$pos)!=0) and ($i<10)){
	$pos=strpos($value," ",$pos);
	//echo($pos." ");
	$val_1=substr($value,0,$pos)." ";
	$val_2=strtoupper(substr($value,$pos+1,1));
	$val_3=substr($value,$pos+2,$len);
	$value=$val_1.$val_2.$val_3;
	$pos++;
	$i++;
	}
return $value;
}




	function unhtmlentities($string)
{
    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
    // replace literal entities
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}


function rincorri_figlio($idc,$idv,$spazio,$v)
{

$val=$v;
$conn = db_connect();
//$spazio.="&nbsp;&nbsp;&nbsp;&nbsp;";
$spazio.="&nbsp;&nbsp;&nbsp;&nbsp;";
$query2="select * from re_combo_ramificate_campi_valori where idvalorepadre=$idv and idcombopadre=$idc and stato=1 order by peso asc";


$rs2 = mssql_query($query2, $conn);
if(!$rs2) error_message(mssql_error());

	while ($row2 = mssql_fetch_assoc($rs2))
	{
		$idc=$row2['id'];
		$idcampo=$row2['idcampo'];
		$valore=$row2['valore'];		
		$etichetta=$spazio.$row2['etichetta'];
		
		$query2="select * from combo_ramificate where idcombopadre=$idc and stato=1";
		$rs_padre = mssql_query($query2, $conn);
		$padre=mssql_num_rows($rs_padre);			
		
		if ($idcampo==$val)
		{
		$sel="selected";
		//$etichetta=costruisci_percorso_combo($idcampo,"",0,0)."->".$row2['etichetta'];
		}
		else
		$sel="";
		if($padre>0){
		?>
		<option value="<?=$idcampo?>"<?=$sel?>><?=pulisci_lettura($etichetta." ".$idcombopadre)?></option>	
		<!--<optgroup label="<?=pulisci_lettura($etichetta." ".$idcombopadre)?>">			-->
		<?}else{?>
		<option value="<?=$idcampo?>" <?=$sel?>><?=pulisci_lettura($etichetta)?></option>
		<?}		
		rincorri_figlio($idc,$idcampo,$spazio,$val);
		
		//if($padre>0) echo("</optgroup>");
	}
	
return true;

}

function rincorri_figlio_per_stampa($idc,$idv,$spazio,$v)
{

$val=$v;
$conn = db_connect();
//$spazio.="&nbsp;&nbsp;&nbsp;&nbsp;";
$spazio.="->&nbsp;";
$query2="select * from re_combo_ramificate_campi_valori where idvalorepadre=$idv and idcombopadre=$idc";	
//echo($query2);
$result="";

$rs2 = mssql_query($query2, $conn);
if(!$rs2) error_message(mssql_error());

	while ($row2 = mssql_fetch_assoc($rs2))
	{
		$idc=$row2['id'];
		$idcampo=$row2['idcampo'];
		$valore=$row2['valore'];		
		$etichetta=$spazio.$row2['etichetta'];
		if ($idcampo==$val)
		{
		$sel="selected";	
		$etichetta=costruisci_percorso_combo($idcampo,"",0,0);
		print(pulisci_lettura($etichetta));	
		$result.=pulisci_lettura($etichetta);
		}		
		rincorri_figlio_per_stampa($idc,$idcampo,$spazio,$val);
	}

return $result;

}

function extractComboCode($str){
	
	if(strpos($str,'|') !== false){
		$arr = explode('|',$str);
		//echo '-> '.trim($arr[0]);
		$strret = $arr[0];
		return "$strret ";
	}
	return $str;
}


function get_rincorri_figlio_per_stampa($idc,$idv,$spazio,$v)
{

$val=$v;
$conn = db_connect();
//$spazio.="&nbsp;&nbsp;&nbsp;&nbsp;";
$spazio.="->&nbsp;";
$query2="select * from re_combo_ramificate_campi_valori where idvalorepadre=$idv and idcombopadre=$idc";	


$rs2 = mssql_query($query2, $conn);
if(!$rs2) error_message(mssql_error());

	while ($row2 = mssql_fetch_assoc($rs2))
	{
		$idc=$row2['id'];
		$idcampo=$row2['idcampo'];
		$valore=$row2['valore'];		
		if ($idcampo==$val)
		{
		
		$sel="selected";
		$etichetta=costruisci_percorso_combo($idcampo,"",0,0);
		//$etichetta=costruisci_percorso_combo($idcampo,"",0,0)." / ".$row2['etichetta'];
		
		return $etichetta;
		
		
		}
		
		$etichetta=get_rincorri_figlio_per_stampa($idc,$idcampo,$spazio,$val);
		
		
		if ($etichetta!='')
		{
		break;
		
		}
		
	
	}

return $etichetta;
}



function costruisci_percorso_combo($idcampofiglio,$briciola1,$idv1,$idc1)
{
	$conn = db_connect();

	$idv=$idv1;
	$idc=$idc1;
	
	
		$query2="select * from re_combo_ramificate_campi_valori where idcampo=$idcampofiglio";	
		//echo($query2);
		$rs2 = mssql_query($query2, $conn);
		if(!$rs2) error_message(mssql_error());
		if ($row2 = mssql_fetch_assoc($rs2))
		{
			if($row2['stampa']=='1') 
				$etichetta=extractComboCode($row2['etichetta']);
				else
				$etichetta="";
				
			if (($briciola1!='')and ($etichetta!=""))
				$briciola=$etichetta." - ".$briciola1;		
			elseif(($briciola1!='')and ($etichetta==""))
					$briciola=$briciola1;	
					else
					$briciola=$etichetta;			
			
			$idc=$row2['idcombopadre'];
			$idv=$row2['idvalorepadre'];
			$idcampo=$idcampofiglio;
			if ($idv>0)
				$briciola=costruisci_percorso_combo($idv,$briciola,$idv,$idc);
		}	
	return $briciola;

}


function convert_versione($numero)
{
	/*	
	$nuovo_numero=$numero-1;
	$ranges=range('A', 'Z');
	if(($nuovo_numero)>24) {
		$new_num=$nuovo_numero%25;
		$new_num1=((int)($nuovo_numero/25))-1;
		$versione=$ranges[$new_num1].$ranges[$new_num];
	}else{
		$versione=$ranges[$nuovo_numero];
	}
	
	return $versione;*/
	return $numero;
			

}


function controlla_permesso_stampa($idcartella)
{
return false;	
	
}

function get_privacy_cartella($idutente,$operatore){
	$conn = db_connect();
	$query = "SELECT * from re_operatore_in_cartella where (idutente=$idutente)and(id_operatore=$operatore)";	
	$rs = mssql_query($query, $conn);
	if(mssql_num_rows($rs)>0)
		return true;
		else
		return false;
}

function get_permesso_modulo($modulo,$gruppo){
	$conn = db_connect();
	$query = "SELECT * from moduli_permessi where (cancella='n')and(stato=1)and(idgruppo=$gruppo)and(idmodulo=$modulo) and tipo=0";	
	$rs = mssql_query($query, $conn);
	if($row=mssql_fetch_assoc($rs))
		return true;
		else
		return false;
}

function get_flag_edita_modulo($modulo, $vers){
	$conn = db_connect();
	$query = "SELECT * from moduli where (edit_all = 1) AND (idmodulo = $modulo) AND (versione = $vers)";	
	$rs = mssql_query($query, $conn);
	if($row=mssql_fetch_assoc($rs))
		return true;
		else
		return false;
}

function get_permesso_modulo_vis($modulo,$gruppo){
	$conn = db_connect();
	$query = "SELECT * from moduli_permessi where (cancella='n')and(stato=1)and(idgruppo=$gruppo)and(idmodulo=$modulo) and tipo=1";	
	$rs = mssql_query($query, $conn);
	if($row=mssql_fetch_assoc($rs))
		return true;
		else
		return false;
}

function get_gestore_cartella($user){
	$conn = db_connect();
	$query = "SELECT * from operatori where (uid=$user)";	
	$rs = mssql_query($query, $conn);
	$row=mssql_fetch_assoc($rs);
	if ($row['gestore_cartella']=="y") return true;
		else
		return false;
}



function controlla_ds()
{
//date_default_timezone_set('Europe/Rome');


$data_corrente=date('d/m/Y');
	if($_SESSION['UTENTE']->is_ds())
	{
		$data_inizio=$_SESSION['UTENTE']->get_data_inizio_ds();
		$data_fine=$_SESSION['UTENTE']->get_data_fine_ds();		
		if( ((diff_in_giorni($data_corrente,$data_inizio))>0)and((diff_in_giorni($data_corrente,$data_fine))<0) ){		
		//if( ($data_inizio<=$data_corrente) and ($data_fine>=$data_corrente) ){				
		return true;}	
		else{	
		return false;}
	}
	return get_gestore_cartella($_SESSION['UTENTE']->get_userid());		
}

//definiamo la funzione e le variabili
function diff_in_giorni($first, $second)
{

//isoliamo i valori contenuti nei due array
  $array_f = @explode ("/", $first);
  $array_s = @explode ("/", $second);

  $dd1 = $array_f[0];
  $mm1 = $array_f[1];
  $yyyy1 = $array_f[2];

  $dd2 = $array_s[0];
  $mm2 = $array_s[1];
  $yyyy2 = $array_s[2];

//utilizziamo i valori degli array come termini di confronto 
  $confronto1 = @gregoriantojd($mm1, $dd1, $yyyy1);
  $confronto2 = @gregoriantojd($mm2, $dd2, $yyyy2);
  
//calcoliamo la differenza in giorni 
  return $confronto1 - $confronto2; 
  }



function controlla_dt()
{

date_default_timezone_set('Europe/Rome');

$data_corrente=formatta_data(date('m/d/Y'));
	if($_SESSION['UTENTE']->is_dt())
	{
		//$data_inizio = date_create($_SESSION['UTENTE']->get_data_inizio_dt());
		//$data_inizio=date_format($data_inizio, 'd/m/Y');		
		$data_inizio=$_SESSION['UTENTE']->get_data_inizio_dt();
		//$data_fine = date_create($_SESSION['UTENTE']->get_data_fine_dt());
		//if ($data_fine!="") $data_fine=date_format($data_fine, 'd/m/Y');		
		$data_fine=$_SESSION['UTENTE']->get_data_fine_dt();
				
		if( ($data_inizio<=$data_corrente) and ($data_fine>=$data_corrente) )
		return true;
		else
		return get_gestore_cartella($_SESSION['UTENTE']->get_userid());	
	}
	return get_gestore_cartella($_SESSION['UTENTE']->get_userid());	
	
	
}

function get_stato_paziente_descr($idstato)
{

	switch($idstato) {

			case "0":
				return "dati incompleti - stato non definibile";				
			break;
			case "1":
				return "censito";				
			break;
			case "2":
				return "in attesa di piano di trattamento";
			break;
			
			case "3":
				return "in attesa di autorizzazione ASL";
			break;

			case "4":
				return "in attesa di inizio trattamento";
			break;
			
			case "5":
				return "in trattamento";
			break;
			
			case "6":
				return "dimesso";
			break;			
			case "8":
				return "in attesa di proroga ASL";
			break;			
		}
}

function get_stato_paziente($idpaziente)
{

		$conn = db_connect();
		
		$query="SELECT top 2 data_inizio_trat,DataDimissione,DataPrescrizione,DataPianoTrattamento,DataAutorizAsl,ProtAutorizAsl,idimpegnativa from re_pazienti_impegnative where idutente=$idpaziente ORDER BY DataPrescrizione DESC, DataAutorizAsl DESC";		
		//echo($query);
		$result = mssql_query($query, $conn);
		if(!$result) error_message($query);
		$contaimp=0;
		$et=0;
		if(mssql_num_rows($result)==0) return 1;
		while($row=mssql_fetch_assoc($result)){
	
			$data_pres=valid_date(formatta_data($row['DataPrescrizione']));
			$data_piano=valid_date(formatta_data($row['DataPianoTrattamento']));
			$data_aut=valid_date(formatta_data($row['DataAutorizAsl']));
			$prot_aut=valid_date(formatta_data($row['ProtAutorizAsl']));
			$prot_aut_len=strlen($prot_aut_len);
			$data_dim=valid_date(formatta_data($row['DataDimissione']));
			$data_ini=valid_date(formatta_data($row['data_inizio_trat']));
			if ($contaimp==0){
				if($data_dim!="") return 6;
				if(($data_dim=="")AND($data_aut!="")AND($data_ini!="")) return 5;
				if(($data_dim=="")AND($data_aut!="")AND($data_ini=="")AND($data_piano!="") AND($prot_aut_len==15)) return 4;
				if(($data_dim=="")AND($data_aut!="")AND($data_ini=="")AND($data_piano=="") AND($prot_aut_len<15)) return 4;
				if(($data_dim=="")AND($data_aut=="")AND($data_ini=="")AND($data_piano!="")AND($data_pres!="")) $et=3;	
				if(($data_dim=="")AND($data_aut=="")AND($data_ini=="")AND($data_piano=="")AND($data_pres!="")) return 2;				
			}else{
				if(($data_dim=="")AND($data_aut!="")AND($data_ini!="")) $et=8;
			}
			$contaimp++;
		}
		if($et!=0) 
			return $et;
		else 
			return 0;	
			
			
			
			
			/*
			if ($contaimp==0){
			
				// paziente in trattamento
				if ( ($DataPrescrizione!="") and ($DataPianoTrattamento!="") and ($DataAutorizAsl!=""))
					return 1;
				// attesa di autorizzazione asl
				else if	( ($DataPrescrizione!="") and ($DataPianoTrattamento!=""))
					return 2;
			}
			else {
				if ( ($DataPrescrizione!="") and ($DataPianoTrattamento!="") and ($DataAutorizAsl!=""))
				//stato di proroga
				return 3;
				else
				// necessario piano di trattamento
			return 4;
								
			}
			
		
		
		$contaimp++;
		}
		if ($contaimp>0)
		return 4;
		else
		return 0;
		*/
}


function get_regime_paziente($idpaziente)
{

		$conn = db_connect();
		$query="SELECT top 1 idregime from re_pazienti_impegnative where idutente=$idpaziente ORDER BY idimpegnativa DESC";		
		$result = mssql_query($query, $conn);
		if(!$result) error_message($query);
		$contaimp=0;
		if($row=mssql_fetch_assoc($result))
		{
		return $row['idregime'];
		break;
		}
		return 0;
}


function get_responsabile_regime($idregime)
{

		$conn = db_connect();
		$query="SELECT responsabile from regime where idregime=$idregime";		
		$result = mssql_query($query, $conn);
		if(!$result) error_message($query);
		$contaimp=0;
		if($row=mssql_fetch_assoc($result))
		{
		return $row['responsabile'];
		break;
		}
		return "";
}


function get_regime_paziente_descr($idpaziente)
{

		$conn = db_connect();
		$query="SELECT top 1 normativa,regime from re_pazienti_impegnative where idutente=$idpaziente 
		AND (DataDimissione = CONVERT(DATETIME, '1900-01-01 00:00:00', 102) OR DataDimissione IS NULL) ORDER BY idimpegnativa DESC";	
		$result = mssql_query($query, $conn);
		if(!$result) error_message($query);
		$contaimp=0;
		if($row=mssql_fetch_assoc($result))
		{
		return $row['regime']." / ".$row['normativa'];
		break;
		}
		return "";
}

function get_cartella_attiva_paziente($idpaziente)
{
	$id_medico=0;
	$conta=0;
	$conn = db_connect();
	
	/*
	$pazienti_test = array(1390,2488,2595);
	if(!in_array($idpaziente, $pazienti_test)) {
		$query = "SELECT top 2 idmedico_chiusura from utenti_cartelle where idutente=$idpaziente and cancella='n' order by id desc";	
		$rs = mssql_query($query, $conn);

		if(!$rs) error_message(mssql_error());
	
	
		$conta=mssql_num_rows($rs);		
		if ($conta>1)
		{
			/*if($row=mssql_fetch_assoc($rs))
			{
			$id_medico=$row['idmedico_chiusura'];
			
			if ((($id_medico))>0)
				return true;
			else
				return false;	
			}*/
			/*
			while($row=mssql_fetch_assoc($rs)){
				if ($row['idmedico_chiusura']=="") $id_medico++;
			}
			if ($id_medico<2)
				return true;
			else
				return false;	
		}
		else
		return true;
	} else {
		*/
	$query = "SELECT top 4 idmedico_chiusura from utenti_cartelle where idutente=$idpaziente and cancella='n' order by id desc";	
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());


	$conta=mssql_num_rows($rs);		
	if ($conta>1)
	{
		/*if($row=mssql_fetch_assoc($rs))
		{
		$id_medico=$row['idmedico_chiusura'];
		
		if ((($id_medico))>0)
			return true;
		else
			return false;	
		}*/
		while($row=mssql_fetch_assoc($rs)){
			if ($row['idmedico_chiusura']=="") $id_medico++;
		}
		if ($id_medico<=4)
			return true;
		else
			return false;	
	}
	else
	return true;

}



function get_normativa_paziente($idpaziente)
{

		$conn = db_connect();
		$query="SELECT top 1 idnormativa from re_pazienti_impegnative where idutente=$idpaziente";		
		$result = mssql_query($query, $conn);
		if(!$result) error_message($query);
		$contaimp=0;
		if($row=mssql_fetch_assoc($result))
		{
		return $row['idnormativa'];
		break;
		}
		return 0;
}




/****************************************************************
* funzione invia_mail()	
* $tipo_mail: 1->demo; 2->proroga; 3->benvenuto;										*
*****************************************************************/
function invia_mail($tipo_mail, $id_str, $dominio, $dominio_nome, $testata) {
	
	$conn = db_connect();
	$query="SELECT dbo.settori.nome AS set_nome, dbo.prodotti.nome AS cat_nome, dbo.strutture.nome, dbo.strutture.email, dbo.strutture.pw, dbo.strutture.id
FROM dbo.prodotti INNER JOIN dbo.strutture_prodotti ON dbo.prodotti.id = dbo.strutture_prodotti.idprodotto RIGHT OUTER JOIN dbo.strutture LEFT OUTER JOIN
 dbo.settori ON dbo.strutture.idsettore = dbo.settori.id ON dbo.strutture_prodotti.idstruttura = dbo.strutture.id WHERE (dbo.strutture.id =$id_str)";		
	$result = mssql_query($query, $conn);
	if (mssql_num_rows($result)>1)
		$categorie="le categorie";
		else
		$categorie="la categoria";
	while($row=mssql_fetch_assoc($result)){
		$settore=$row['set_nome'];
		$categorie.=" ".$row['cat_nome'].",";
		$mail_s=$row['email'];
		$pwd=$row['pw'];
		$user=$row['id'];
		$nome=$row['nome'];
	}
	$categorie=substr($categorie,0,(strlen($categorie)-1));
	mssql_free_result($result);
	
	switch($tipo_mail){
		case 1:			
			$query="SELECT dbo.strutture.id, COUNT(dbo.strutture.id) AS num_msg FROM dbo.strutture INNER JOIN dbo.messaggi ON dbo.strutture.id = dbo.messaggi.idstruttura
		GROUP BY dbo.strutture.id HAVING (dbo.strutture.id =$id_str)";
			$result = mssql_query($query, $conn);
			$row=mssql_fetch_assoc($result);
			$num_msg=$row['num_msg'];
			mssql_free_result($result);
			
			$query="SELECT dbo.strutture.id, dbo.impressioni.tipo, COUNT(dbo.impressioni.id_imp) AS num_vis FROM dbo.strutture INNER JOIN dbo.impressioni ON dbo.strutture.id = dbo.impressioni.idstruttura GROUP BY dbo.strutture.id, dbo.impressioni.tipo HAVING (dbo.strutture.id = 48580) AND (dbo.impressioni.tipo = 1)";
			$result = mssql_query($query, $conn);
			$row=mssql_fetch_assoc($result);
			$num_vis=$row['num_vis'];
			mssql_free_result($result);
			 
			$tmpl_mail="mail_demo.html";
			$tmpl_percorso = "web/template/mail/".$tmpl_mail;			
			$fh = fopen($tmpl_percorso,"r");
			$content = fread($fh,filesize($tmpl_percorso));
			fclose($fh);			
			$content=str_replace("#dominio#",$dominio,$content);
			$content=str_replace("#testata#",$testata,$content);
			$content=str_replace("#dominio_titolo#",$dominio_nome,$content);
			$content=str_replace("#nome_struttura#",$nome,$content);
			$content=str_replace("#settore#",$settore,$content);
			$content=str_replace("#categorie#",$categorie,$content);
			$content=str_replace("#user#",$user,$content);
			$content=str_replace("#password#",$pwd,$content);
			$content=str_replace("#num_richieste_prev#",$num_msg,$content);
			$content=str_replace("#num_visual_scheda#",$num_vis,$content);						
			break;
	}

	//percorso
	if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
		$eol="\r\n";
		} elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
		$eol="\r";
		} else {
		$eol="\n";
		}

$headers .= 'From: Network Matrimonio  <info@networkmatrimonio.it>'.$eol;
$headers .= "Content-Type: text/html; charset=iso-8859-1".$eol;
# To Email Address
//$emailaddress=$mail_s;
$emailaddress="a.trezza@braincomputing.com"; //mail di prova - abilitare la riga superiore in funzionamento effettivo
$emailsubject="benvenuto in ".$dominio_nome;

ini_set(sendmail_from,'contact@area04.it');  // the INI lines are to force the From Address to be used !
mail($emailaddress, $emailsubject, $content, $headers);	
echo("mail inviata");
	

}



/****************************************************************
* funzione scrivi_log_rebuild()											*
*****************************************************************/
function scrivi_log_rebuild($id, $tipo) {

	$data = date('m/d/Y');
	$ora = date('H:i');
	//$ip = getenv('REMOTE_ADDR');
	//$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
	$query="INSERT INTO tb_next_rebuild SET (data_mod, ora_mod, id_oggetto, tipo) VALUES('$data','$ora','$id','$tipo')";
	
	//	echo $query;	
	$rt = mssql_query($query, $conn);
	if(!$rt) error_message(mssql_error());
}





/****************************
* funzione calcolo pagerank	    *
*****************************/
//unsigned shift right
function zeroFill($a, $b)
{
$z = hexdec(80000000);
if ($z & $a)
{
$a = ($a>>1);
$a &= (~$z);
$a |= 0x40000000;
$a = ($a>>($b-1));
}
else
{
$a = ($a>>$b);
}
return $a;
}

function mix($a,$b,$c) {
$a -= $b; $a -= $c; $a ^= (zeroFill($c,13));
$b -= $c; $b -= $a; $b ^= ($a<<8);
$c -= $a; $c -= $b; $c ^= (zeroFill($b,13));
$a -= $b; $a -= $c; $a ^= (zeroFill($c,12));
$b -= $c; $b -= $a; $b ^= ($a<<16);
$c -= $a; $c -= $b; $c ^= (zeroFill($b,5));
$a -= $b; $a -= $c; $a ^= (zeroFill($c,3));
$b -= $c; $b -= $a; $b ^= ($a<<10);
$c -= $a; $c -= $b; $c ^= (zeroFill($b,15));

return array($a,$b,$c);
}

function GCH($url, $length=null, $init=GMAG) {
	if(is_null($length)) {
	$length = sizeof($url);
	}
$a = $b = 0x9E3779B9;
$c = $init;
$k = 0;
$len = $length;
while($len >= 12) {
	$a += ($url[$k+0] +($url[$k+1]<<8) +($url[$k+2]<<16) +($url[$k+3]<<24));
	$b += ($url[$k+4] +($url[$k+5]<<8) +($url[$k+6]<<16) +($url[$k+7]<<24));
	$c += ($url[$k+8] +($url[$k+9]<<8) +($url[$k+10]<<16)+($url[$k+11]<<24));
	$mix = mix($a,$b,$c);
	$a = $mix[0]; $b = $mix[1]; $c = $mix[2];
	$k += 12;
	$len -= 12;
	}
	$c += $length;
switch($len) /* all the case statements fall through */
	{
	case 11: $c+=($url[$k+10]<<24);
	case 10: $c+=($url[$k+9]<<16);
	case 9 : $c+=($url[$k+8]<<8);
	/* the first byte of c is reserved for the length */
	case 8 : $b+=($url[$k+7]<<24);
	case 7 : $b+=($url[$k+6]<<16);
	case 6 : $b+=($url[$k+5]<<8);
	case 5 : $b+=($url[$k+4]);
	case 4 : $a+=($url[$k+3]<<24);
	case 3 : $a+=($url[$k+2]<<16);
	case 2 : $a+=($url[$k+1]<<8);
	case 1 : $a+=($url[$k+0]);
	/* case 0: nothing left to add */
	}
	$mix = mix($a,$b,$c);
	/*-------------------------------------------- report the result */
	return $mix[2];
}

//converts a string into an array of integers containing the numeric value of the char
function strord($string) {
for($i=0;$i<strlen($string);$i++) {
$result[$i] = ord($string{$i});
}
return $result;
}

function getPR($_url) {
$url = 'info:'.$_url;
$ch = GCH(strord($url));
$url='info:'.urlencode($_url);
$pr = file("http://www.google.com/search?client=navclient-auto&ch=6$ch&ie=UTF-8&oe=UTF-8&features=Rank&q=$url");
$pr_str = implode("", $pr);
return substr($pr_str,strrpos($pr_str, ":")+1);
}

function pageRank($website){
// Edit this to your website url:
$myWebSite = $website;

define('GMAG', 0xE6359A60);

//echo "<br>The PR of $myWebsite is: ".getPR($myWebSite);

return getPR($myWebSite);

}


/****************************
* funzione invia mail ordini	    *
*****************************/


function invia_mail_ordini($id_struttura,$tipo_pacchetto,$tipo_mail,$attivazione,$scadenza,$diff){
	
	$conn = db_connect();
	if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
		$eol="\r\n";
		} elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
		$eol="\r";
		} else {
		$eol="\n";
		}

	// recupera tutti i dati dell'azienda
	$query = "SELECT strutture.nome, strutture.indirizzo, strutture.cap, strutture.email, strutture.citta, strutture.idprovincia, strutture.status, strutture.partita_iva as partita_iva, province.id, province.nome AS nome_pro, province.sigla,strutture.id AS id_str FROM strutture INNER JOIN province ON strutture.idprovincia = province.id WHERE strutture.id=$id_struttura";
	
	$result = mssql_query($query, $conn);
	$row = mssql_fetch_row($result);	
		$nome = $row[0];		
		$indirizzo = $row[1];
		$cap = $row[2];
		$citta = $row[4];
		$prov = $row[9];
		$email = $row[3];
		$piva = $row[7];		

# To Email Address
//$emailaddress=$email;
$emailaddress="antonio.trezza@poste.it"; //mail di prova - abilitare la riga superiore in funzionamento effettivo

# Message Subject
//if($export)
//	$emailsubject="Invio link esportazione banca dati";
//else
//	$emailsubject="Attivazione utente registrato";

$query = "SELECT * FROM utenti_web_pacchetti WHERE id=$tipo_pacchetto";
$result = mssql_query($query, $conn);
if($row=mssql_fetch_assoc($result)) {	
		$pacc = $row['accesso'];		
	}
# Message Subject
if($pacc=='1')
	$abbonamento="abbonamento di Affiliazione";
else
	$abbonamento="abbonamento per uso spazio Banner";
	

# Boundry for marking the split & Multitype Headers
$mime_boundary=md5(time()); 


# Common Headers
$headers .= 'From: Network Matrimonio  <info@networkmatrimonio.it>'.$eol;
$headers .= "Content-Type: text/html; charset=iso-8859-1".$eol;
//$headers .= "--".$mime_boundary.$eol;
//$headers .= "Content-Type: text/plain; charset=iso-8859-1".$eol;
//$headers .= "Content-Transfer-Encoding: 8bit".$eol;
//$headers .= 'Reply-To: Jonny <jon@genius.com>'.$eol;
//$headers .= 'Return-Path: Jonny <jon@genius.com>'.$eol;    // these two to set reply address
//$headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
//$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid

$msg = "";
$br = "<br>";

# Text Version
$msg .= "Spett.le <strong>".trim($nome)."</strong>,".$br;

switch ($tipo_mail){
	case "1":
		$emailsubject="Avviso scadenza ".$abbonamento;
		$msg .= "La informiamo che il suo ".$abbonamento." �<strong> scaduto </strong> il giorno ";
		$msg .= date("d")."/".date("m")."/".date("Y").".".$br;
		$msg .= "Per il rinnovo del suo abbonamento La invitiamo a contattare il nostro servizio affiliazioni:".$br;		 
		$msg .="telefono: 081.99999999  -  e_mail: <a href=\"mailto:info@braincomputing.com\">info@braincomputing.com</a>".$br.$br;	
	break;
	
	case "2":
		$emailsubject="Avviso attivazione ".$abbonamento;
		$msg .= "La informiamo che il suo ".$abbonamento." � stato <strong> attivato </strong> ed avr� decorrenza ";
		$msg .= "dal: ".$attivazione." - al: ".$scadenza.$br.$br;				
	break;

}
$msg .= $br."Network Matrimonio La ringrazia e Le porge cordiali saluti.".$br.$br;
# SEND THE EMAIL

# SEND THE EMAIL


ini_set(sendmail_from,'contact@area04.it');  // the INI lines are to force the From Address to be used !
  //mail($emailaddress, $emailsubject, $msg, $headers);
  
  //mail di conoscenza
  $msg1="AVVISO - ".$abbonamento." inviato a: ".$nome." ".$indirizzo.$br.$br;
  $msg1.="<------  INIZIO MESSAGGIO  ------>".$br.$br;
  $msg1.=$msg.$br;
  $msg1.="<------  FINE MESSAGGIO  ------>";
  $emailaddress="a.trezza@braincomputing.com";
  $emailsubject.=" - azienda: ".$nome;
  //mail($emailaddress, $emailsubject, $msg1, $headers);
  
//ini_restore(smtp); 
//ini_restore(smtp_port); 
//ini_restore(sendmail_from); 


//print("<script>alert ('Email inviata a ".$email."');

	// fine else attiva
}



/****************************
* funzione differenza tra date	    *
*****************************/

function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {  
/*    $interval can be:    
	yyyy - Number of full years    
	q - Number of full quarters
	m - Number of full months    
	y - Difference between day numbers      
	(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)    
	d - Number of full days    
	w - Number of full weekdays    
	ww - Number of full weeks    
	h - Number of full hours    
	n - Number of full minutes    
	s - Number of full seconds (default)  */    
	
	if (!$using_timestamps) {    
	$datefrom = strtotime($datefrom, 0);    
	$dateto = strtotime($dateto, 0);  }  
	$difference = $dateto - $datefrom; 
	// Difference in seconds     
	switch($interval) {       
	case 'yyyy': 
	// Number of full years      
	$years_difference = floor($difference / 31536000);      
	if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {        $years_difference--;      }      if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {        $years_difference++;      }      $datediff = $years_difference;      break;    
	case "q": 
	// Number of full quarters      
	$quarters_difference = floor($difference / 8035200);      while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {        $months_difference++;      }      $quarters_difference--;      $datediff = $quarters_difference;      break;    
	case "m": 
	// Number of full months      
	$months_difference = floor($difference / 2678400);      while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {        $months_difference++;      }      $months_difference--;      $datediff = $months_difference;      break;    
	case 'y': 
	// Difference between day numbers      
	$datediff = date("z", $dateto) - date("z", $datefrom);      break;    
	case "d": 
	// Number of full days      
	$datediff = floor($difference / 86400);      break;    
	case "w": 
	// Number of full weekdays      
	$days_difference = floor($difference / 86400);      $weeks_difference = floor($days_difference / 7); 
	// Complete weeks      
	$first_day = date("w", $datefrom);      $days_remainder = floor($days_difference % 7);      $odd_days = $first_day + $days_remainder; 
	// Do we have a Saturday or Sunday in the remainder?      
	if ($odd_days > 7) { 
	// Sunday        
	$days_remainder--;      }      if ($odd_days > 6) { 
	// Saturday        
	$days_remainder--;      }      
	$datediff = ($weeks_difference * 5) + $days_remainder;      break;    
	case "ww": // Number of full weeks      
	$datediff = floor($difference / 604800);      break;    
	case "h": // Number of full hours      
	$datediff = floor($difference / 3600);      break;    
	case "n": // Number of full minutes      
	$datediff = floor($difference / 60);      break;    
	default: // Number of full seconds (default)      
	$datediff = $difference;      break;  }      
	return $datediff;}



/****************************
* funzione is_superadmin()	    *
*****************************/
function is_superadmin(){

	if ( ($_SESSION['UTENTE']->get_type()== 0)) return true;
	else return false;

}

function is_admin(){

	if ( ($_SESSION['UTENTE']->get_type()== 1)) return true;
	else return false;

}


/****************************
* funzione controlla_permessi()*
*****************************/
function controlla_permessi2($curr_struct) {  

	$start_structure = $_SESSION['UTENTE']->get_start_struct();
	$flag = false;
	//$padre = get_padre($curr_struct);
	
	// se sei il super-admin setta il flag a true
	if($_SESSION['UTENTE']->is_root()) $flag = true;
	
	while($padre!=0)
	{   
	    if ($start_structure==$curr_struct)
		{
			$flag=true;
			break;
		}
		$curr_struct = $padre;
		//$padre = get_padre($padre);
    }
	
	return $flag;

}


/****************************************************************
* funzione scrivi_log()											*
*****************************************************************/
function scrivi_log($id, $tablename, $operazione, $campo_id) {

	$data = date('d/m/Y');
	$ora = date('H:i');
	$ip = getenv('REMOTE_ADDR');
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
		
	switch($operazione) {

		case 'ins_op': 
			$query = "UPDATE $tablename SET updcount=0, datains='$data',orains='$ora',opeins='$ope',ipins='$ip' WHERE $campo_id='$id'";
			break;

		case 'agg_op': 
			$select = "SELECT updcount FROM $tablename WHERE uid='$id'";
			$rs = mssql_query($select, $conn);
			if(!$rs) error_message(mssql_error());
			if(!$row=mssql_fetch_row($rs)) error_message(mssql_error());
			if($row[0]==NULL) $count = 0;
			else $count = $row[0];
			
			$query = "UPDATE $tablename SET updcount = ".($count+1).",dataagg='$data',oraagg='$ora',opeagg='$ope',ipagg='$ip' WHERE $campo_id='$id'";
			break;

		case 'ins': 
			$query = "UPDATE $tablename SET updcount=0,datains='$data',orains='$ora',opeins='$ope',ipins='$ip' WHERE $campo_id='$id'";
			break;

		case 'agg': 
			$select = "SELECT updcount FROM $tablename WHERE $campo_id='$id'";	
		
			$rs = mssql_query($select, $conn);
			if(!$rs) error_message(mssql_error());
			if(!$row=mssql_fetch_row($rs)) error_message(mssql_error());
			if($row[0]==NULL) $count = 0;
			else $count = $row[0];
			
			$query = "UPDATE $tablename SET updcount = ".($count+1).",dataagg='$data',oraagg='$ora',opeagg='$ope',ipagg='$ip' WHERE $campo_id='$id'";
			break;

		case 'del': 
			$select = "SELECT updcount FROM $tablename WHERE $campo_id='$id'";
			$rs = mssql_query($select, $conn);
			if(!$rs) error_message(mssql_error());
			if(!$row=mssql_fetch_row($rs)) error_message(mssql_error());
			if($row[0]==NULL) $count = 0;
			else $count = $row[0];
			
			$query = "UPDATE $tablename SET updcount = ".($count+1).",dataagg='$data',oraagg='$ora',opeagg='$ope',ipagg='$ip' WHERE $campo_id='$id'";
			break;

	}	
//	echo $query;
	
	$rt = mssql_query($query, $conn);
	if(!$rt) error_message(mssql_error());

}

/*****************************************
funciton aggiornamento
******************************************/
function aggiornamento($id) {

	$data = date('d/m/Y');
	$ora = date('H:i');

	$conn = db_connect();
	//alla fine scrivo nella tabella_aggiornamento_web
	$query = "INSERT INTO tabella_aggiornamento_web
		(data,idstruttura,referente) VALUES
		('".$data." ".$ora."',$id,'".$_SESSION['UTENTE']->get_username()."')";
//echo $query;
	$result = mssql_query($query, $conn);
	if(!$result) error_message($query);
		
}


/****************************
* funzione briciole di pane	    *
*****************************/

function briciole_pane()
{
	global $PHP_SELF;

	$is_link=true;
	// crea l'oggetto
	/*$objStruttura = new Struttura();*/
	$objStruttura = $_SESSION['STRUTTURA1'];
	//echo "struttura =".$_SESSION['STRUTTURA'];
	$curr_struct = $_SESSION['STRUTTURA1']->get_id();
	$start_struct= $_SESSION['UTENTE']->get_start_struct();
	
	// recupera il padre
	//$padre = get_padre($curr_struct);

	$famiglia = array();

	//array_push($famiglia, "<a href=\"".$PHP_SELF."?do=goto&amp;id=".$curr_struct."\" title=\"vai a ".$row[0]."\">".$objStruttura->get_label()."</a> / ");
	array_push($famiglia, "<a title=\"sei in ".$objStruttura->get_label()."\">".$objStruttura->get_label()."</a>"); 
 	
	$conn = db_connect();
	
	while($padre!=0)
	{

		if($curr_struct==$start_struct) $is_link = false;
		
		// recupera il nome della curr_struct
		$query = "SELECT nome FROM strutture WHERE id='".$padre."'";
		$rt = mssql_query($query, $conn);
		if(!$rt) error_message(mssql_error());
		if(!$row = mssql_fetch_row($rt)) error_message(mssql_error());

		if($padre!=$start_struct)
		{
				$curr_struct = $padre;
				if($is_link) array_push($famiglia, "<a href=\"".$PHP_SELF."?do=goto&amp;id=".$curr_struct."\" title=\"vai a ".$row[0]."\">".$row[0]."</a> / ");
				else array_push($famiglia, "<span class=\"offline\"><a title=\"non hai accesso a questa struttura\">".$row[0]."</a></span> / ");
		}	
		else
		{
				$is_link=false;
				$curr_struct = $padre;
				array_push($famiglia, "<a href=\"".$PHP_SELF."?do=goto&amp;id=".$curr_struct."\" title=\"vai a ".$row[0]."\">".$row[0]."</a> / ");
		} 
		mssql_free_result($rt);
		
		//$padre=get_padre($curr_struct);	

	}//END WHILE

	//asort($famiglia);
	//reset($famiglia);
	// inverte l'array
	$briciole = array_reverse($famiglia);
	
	return $briciole;
	//foreach($briciole as $elem) print($elem);
	
}//END FUNCTION BRICIOLE DI PANE



/****************************
* funzione authorize_me	    *
*****************************/
function authorize_me($uid, $rule){

	$tablename = "users";


	// se hai settato il cookie..
	if(isset($_SESSION['UTENTE'])) {

		// se non sei l'amministratore..
		if(! ($_SESSION['UTENTE']->is_root())){

			if($rule=='none') return true;
			else{

				$conn = db_connect();

				$query = "SELECT cando".$rule." FROM $tablename where uid='$uid'";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());

				if(! ($row=mssql_fetch_row($rs))) error_message(mssql_error());

				if($row[0] == 1) return true;	// accesso confermato
				else return false;		// accesso fallito

			}
		}

		// sei l'amministratore
		else return true;

	} else {
		ob_start();
		Header("Location: index.php");
		ob_end_flush();
	}
	return false;
}

/****************************
* funzione go_home()	    *
*****************************/
function go_home(){
	ob_start();
	Header("Location: main.php");
	ob_end_flush();
}


/****************************************************************************
* funzione wordlimit 														*
*****************************************************************************/
function wordlimit($string, $length = 20, $ellipsis = "..."){

   $paragraph = explode(" ", $string);

   if($length < count($paragraph))
   {
       for($i = 0; $i < $length; $i++)
       {
           if($i < $length - 1)
               $output .= $paragraph[$i] . " ";
           else
               $output .= $paragraph[$i] . $ellipsis;
       }

       return $output;
}

   return $string;
}


/********************************************************************************
* funzione html_header	    													*
*********************************************************************************/
function html_header(){

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="it">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title><?php print CMS.' - '.VERSIONE; ?></title>
<link rel="stylesheet" media="screen" href="css/base.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="jquery/flexigrid/css/flexigrid/flexigrid.css">

<?
srand(time());
$random = rand();
?>
<script src="M_Validation/M_global.js?version=5" type="text/javascript">/**/</script>
<script type="text/javascript" src="http://netmat.braincomputing.com/netmat/republic/include/code2.js?random=<?=$random?>"></script>
<link rel="stylesheet" href="http://netmat.braincomputing.com/netmat/republic/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen"></link>
<script type="text/javascript" src="http://netmat.braincomputing.com/netmat/republic/dhtmlgoodies_calendar/dhtmlgoodies_calendar-it.js?random=20060118"></script>


<script type="text/javascript" src="jquery/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="jquery/menu/ddaccordion.js"></script>

<script type="text/javascript" src="jquery/flexigrid/lib/jquery/jquery.js"></script>
<script type="text/javascript" src="jquery/flexigrid/flexigrid.js"></script>


<script type="text/javascript" src="jquery/dragtable/jquery.tablednd_0_5.js"></script>


 <script src="jquery/tabs/jquery-1.1.3.1.pack.js" type="text/javascript"></script>
 <script src="jquery/tabs/jquery.history_remote.pack.js" type="text/javascript"></script>
 <script src="jquery/tabs/jquery.tabs.pack.js" type="text/javascript"></script>
 
  <link rel="stylesheet" href="jquery/tabs/jquery.tabs.css" type="text/css" media="print, projection, screen">


<script>
$(document).ready(function(){

	//mostro nascondo il menu e vario la larghezza del content principale
	$("#contactLink").click(function(){
		if ($("#menu").is(":hidden")){
			
			width_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
			width_finestra = width_finestra - 195;
			$("#wrap-content").animate({ 
			width: width_finestra,
			}, 1500, function()	{
				 			$("#menu").slideDown("slow");
				 			}
			);
			
		}
		else{
			$("#menu").slideUp("slow", function() {
      									 $("#wrap-content").animate({ 
											width: "100%",
											}, 1000 );
    									 }
								);
			
		}
	});
                
});

//per effetto accordion
ddaccordion.init({
	headerclass: "headerbar", //Shared CSS class name of headers group
	contentclass: "submenu", //Shared CSS class name of contents group
	revealtype: "mouseover", //Reveal content when user clicks or onmouseover the header? Valid value: "click" or "mouseover
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false
	defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: true, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", "selected"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "normal", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
});

</script>

<?php 
$js=$_SESSION['UTENTE']->get_gid().'.js';
Malicius();
?>
<script language=JavaScript src="<?=$js;?>" type=text/javascript></script>
<script language=JavaScript src="mmenu.js" type=text/javascript></script>  

<style type="text/css">

*{
	margin: 0px;
	padding: 0px;
}
body{
	margin: 0px;
	text-align:left;
	background-color:#FFF;
	font-family:Arial, Sans-Serif;
	font-size:0.75em;
}
#menu
{
	height: auto;
	width:195px;
	display:none;
	float:left;
	clear: both;
	border-bottom: 1px solid #CCC;
}
#contactLink
{
	height:21px;
	width:185px;
	background: url('images-new/menu-nascondimostra-up.gif') top right no-repeat;
	display:block;
	cursor:pointer;
	float:left;
	clear: both;
	font-size:11px;
	padding: 6px 0 0 10px;
}

#top{
	float: left;
	width: 100%;
	}
	#top img{
		padding: 20px;
	}

#wrap-bar{
	float: left;
	width: 100%;
	background: url('images-new/menu-repeat.gif') top right repeat-x;
}

#wrap-content{
	float: right;
	width: 100%;
	/*background-color: #999999;*/
}

/* per accordion */
.urbangreymenu{
	width: 190px; /*width of menu*/
}

.urbangreymenu .headerbar{
	font-size:11px;
	padding: 7px 0 0 10px;
	color: #000;
	text-transform: uppercase;
	height:20px;
	width:185px;
	background: #CCC url('images-new/menu-tab.gif') top right no-repeat;
	}
	.urbangreymenu .headerbar a{
		text-decoration: none;
		color: #000;
		display: block;
		}
	
.urbangreymenu ul{
	list-style-type: none;
	}
	.urbangreymenu ul li{
		padding-top: 1px; /*bottom spacing between menu items*/
		background: #CCC;
		}
		.urbangreymenu ul li a{
			font: 11px;
			color: black;
			background: #F6F6F6;
			display: block;
			line-height: 17px;
			padding-left: 8px; /*link text is indented 8px*/
			text-decoration: none;
		}
		.urbangreymenu ul li a:visited{
			color: #000;
			}
			.urbangreymenu ul li a:hover{ /*hover state CSS*/
				color: #000;
				font-weight:bold;
				background: #ccc;
				}


.flexigrid div.fbutton .add
		{
			background: url(css/images/add.png) no-repeat center left;
		}	

	.flexigrid div.fbutton .delete
		{
			background: url(css/images/close.png) no-repeat center left;
		}	
		
		div.mandatory {background: url('M_Validation/images/mandatory.png') bottom right no-repeat;}
	div.focused {background: url('M_Validation/images/focused.png') bottom right no-repeat;}
	div.correct {background: url('M_Validation/images/correct.gif') bottom right no-repeat;}
	
div.warning { 
width: auto;
	height: 26px;
	float: left;
	padding: 0;
	
	color: #f0af00;

				

</style>


</head>
<body>

<?php
}






/********************************************************************
* funzione lettere_alfabetiche	    								*
*********************************************************************/
function lettere_alfabetiche() {
	
global $PHP_SELF;

print('<div id="lettere">');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=a"><img src="images/letter_a.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=b"><img src="images/letter_b.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=c"><img src="images/letter_c.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=d"><img src="images/letter_d.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=e"><img src="images/letter_e.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=f"><img src="images/letter_f.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=g"><img src="images/letter_g.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=h"><img src="images/letter_h.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=i"><img src="images/letter_i.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=j"><img src="images/letter_j.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=k"><img src="images/letter_k.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=l"><img src="images/letter_l.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=m"><img src="images/letter_m.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=n"><img src="images/letter_n.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=o"><img src="images/letter_o.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=p"><img src="images/letter_p.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=q"><img src="images/letter_q.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=r"><img src="images/letter_r.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=s"><img src="images/letter_s.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=t"><img src="images/letter_t.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=u"><img src="images/letter_u.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=v"><img src="images/letter_v.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=w"><img src="images/letter_w.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=x"><img src="images/letter_x.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=y"><img src="images/letter_y.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=z"><img src="images/letter_z.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=ALL"><img src="images/all.gif"></a></div>');
print('</div>'."\n");
	
}

/************************************************************************
* funzione GestionePagine   											*
*************************************************************************/
function GestionePagine($num_rows, $page, $tablename) {

	global $PHP_SELF; 
	$maxpages = MAXPAGES;

	$conn = db_connect();

	// risultati per pagina
	if(!$_SESSION['NXPAGINA']) $_SESSION['NXPAGINA'] = DEFAULT_NXPAGINA;
	$rpp = $_SESSION['NXPAGINA'];

	if ($page == 0) $page = 1;

	// recupera le pagine totali
	$pages = ceil($num_rows / $rpp);
	$subpages = ceil($pages / $maxpages);
	$subpage = (ceil($page / $maxpages)-1);
	// calcola la prima e l'ultima pagina da visualizzare
	$prima = (($subpage *$maxpages)+1);
	$ultima = ($prima+$maxpages-1);

	/*echo "pagina corrente ".$page."<br>";
	echo "subpagina corrente ".$subpage."<br>";
	echo "pagine totali ".$pages."<br>";
	echo "subpagine totali ".$subpages."<br>";
	echo "prima ".$prima."<br>";
	echo "ultima ".$ultima."<br>";*/

	print('<div class="numerazione">'."\n");

	if($subpage)
		print('<a href="'.$PHP_SELF.'?do=show_list&page='.($prima-1).'&tablename='.$tablename.'"><img src="images/indietro_indietro.gif" /></a> '."\n");

	if ($page > 1)
		print('<a href="'.$PHP_SELF.'?do=show_list&page='.($page-1).'&tablename='.$tablename.'" title="pagina precedente"><img src="images/indietro.gif" /></a> '."\n");

	for($i=$prima; $i<=$ultima; $i++){

		if($i <= $pages) {
			print('<span class="numero">');
			if($i==$page) print('<strong>'.$i.'</strong>'."\n");
			else print('<a href="'.$PHP_SELF.'?do=show_list&page='.$i.'&tablename='.$tablename.'" title="pagina successiva">'.$i.'</a>'."\n");
			print('</span>');
		}
	}

	if ($page < $pages)
		print(' <a href="'.$PHP_SELF.'?do=show_list&page='.($page+1).'&tablename='.$tablename.'" title="pagina successiva"><img src="images/avanti.gif" /></a>'."\n");

	if($ultima < $pages)
		print('<a href="'.$PHP_SELF.'?do=show_list&page='.($ultima+1).'&tablename='.$tablename.'"><img src="images/avanti_avanti.gif" /></a> '."\n");

	print('</div>'."\n");

	print('<div class="cambia_nxpagina">');
	print('<form method="post" action="'.$PHP_SELF.'">'."\n");
	print('visualizza ');
	print('<input type="text" name="nxpagina" size="3" maxlength="3" value="'.$_SESSION['NXPAGINA'].'" />');
	print(' pagine per volta ');
	print('<input type="submit" name="action" value="aggiorna" />');
	print('</form>'."\n");
	print('</div>'."\n");

}


/****************************************************************
* funzione cambia_nxpagina	  							  		*
*****************************************************************/
function cambia_nxpagina($value) {

	$_SESSION['NXPAGINA'] = $value;
}


/****************************************************************
* funzione visualizza_ordina_per()		  				  		*
*****************************************************************/
function visualizza_ordina_per() {

	global $PHP_SELF;
	
	print('<div class="ordinaper">');
	print('<form method="post" action="'.$PHP_SELF.'">'."\n");

	// criterio 1
	print('ordina per ');

	print('<select class="select_form" name="ordinaper">');
	print('<option value="0"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==0) echo " selected";
	print('>alfabetico</option>'."\n");
	print('<option value="1"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==1) echo " selected";
	print('>comune</option>'."\n");
	print('<option value="2"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==2) echo " selected";
	print('>c.a.p.</option>'."\n");
	print('<option value="3"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==3) echo " selected";
	print('>provincia</option>'."\n");
	print('<option value="4"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==4) echo " selected";
	print('>telefono</option>'."\n");
	print('<option value="5"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==5) echo " selected";
	print('>fax</option>'."\n");
	print('<option value="6"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==6) echo " selected";
	print('>email</option>'."\n");
	print('<option value="7"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==7) echo " selected";
	print('>http</option>'."\n");
	print('<option value="8"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==8) echo " selected";
	print('>data modifica</option>'."\n");
	print('<option value="9"');
	if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==9) echo " selected";
	print('>settore</option>'."\n");
	print('</select>');

	print(' e per ');
	print('<select class="select_form" name="ordinaper2">');
	print('<option value="0"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==0) echo " selected";
	print('>alfabetico</option>'."\n");
	print('<option value="1"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==1) echo " selected";
	print('>comune</option>'."\n");
	print('<option value="2"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==2) echo " selected";
	print('>c.a.p.</option>'."\n");
	print('<option value="3"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==3) echo " selected";
	print('>provincia</option>'."\n");
	print('<option value="4"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==4) echo " selected";
	print('>telefono</option>'."\n");
	print('<option value="5"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==5) echo " selected";
	print('>fax</option>'."\n");
	print('<option value="6"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==6) echo " selected";
	print('>email</option>'."\n");
	print('<option value="7"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==7) echo " selected";
	print('>http</option>'."\n");
	print('<option value="8"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==8) echo " selected";
	print('>data modifica</option>'."\n");
	print('<option value="9"');
	if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==9) echo " selected";
	print('>settore</option>'."\n");
	print('</select>');
	print(' <input type="submit" name="action" value="ordina" />');

	print('</form>'."\n");
	print('</div>');

	//echo $_SESSION['ORDINAPER'];
	//echo "-".$_SESSION['ORDINAPER2'];

}


/****************************************************************
* funzione ordina_per()					  				  		*
*****************************************************************/
function ordina_per($arr1, $arr2) {

	global $PHP_SELF;
	
	print('<div class="ordinaper">');
	print('<form method="post" action="'.$PHP_SELF.'">'."\n");

	// criterio 1
	if(sizeof($arr1)) {

		print('ordina per ');
		print('<select class="select_form" name="ordinaper">');
		
		for($i=0; $i<sizeof($arr1); $i++) {
		
			$index = $i+1;
			print('<option value="'.$index.'"');
			if(isset($_SESSION['ORDINAPER']) AND $_SESSION['ORDINAPER']==$index) echo " selected";
			print('>'.$arr1[$i].'</option>'."\n");
		}
		print('</select>');
	}

	// criterio 2
	if(sizeof($arr2)) {

		print('e per ');
		print('<select class="select_form" name="ordinaper2">');
		
		for($i=0; $i<sizeof($arr2); $i++) {
		
			$index = $i+1;
			print('<option value="'.$index.'"');
			if(isset($_SESSION['ORDINAPER2']) AND $_SESSION['ORDINAPER2']==$index) echo " selected";
			print('>'.$arr2[$i].'</option>'."\n");
		}
		print('</select>');
	}
	print(' <input type="submit" name="action" value="ordina" />');

	print('</form>'."\n");
	print('</div>');

	//echo $_SESSION['ORDINAPER'];
	//echo "-".$_SESSION['ORDINAPER2'];

}

/************************************************************************
* funzione quali	    												*
*************************************************************************/
function quali() {

	global $PHP_SELF;

	print('<div class="cambia_nxpagina">');
	print('<form method="post" action="'.$PHP_SELF.'">'."\n");
	print('visualizza strutture ');

	print('<input type="radio" name="quali" value="1" title="non corrette" ');
	if(isset($_SESSION['QUALI']) AND $_SESSION['QUALI']==1) echo "checked";
	print('	/>');
	print('<img src="images/sem_rosso.gif" title="non corrette" /> non corrette&nbsp;&nbsp;&nbsp;');

	if($_SESSION['UTENTE']->get_validazione()=='y') {

		print('<input type="radio" name="quali" value="3" title="da convalidare" ');
		if(isset($_SESSION['QUALI']) AND $_SESSION['QUALI']==3) echo "checked";
		print('	/>');
		print(' <img src="images/sem_arancio.gif" title="da convalidare" /> da convalidare&nbsp;&nbsp;&nbsp;');

		print('<input type="radio" name="quali" value="2" title="corrette" ');
		if(isset($_SESSION['QUALI']) AND $_SESSION['QUALI']==2) echo "checked";
		print('	/>');
		print(' <img src="images/sem_verde.gif" title="corrette" /> corrette&nbsp;&nbsp;&nbsp;');

	} else {
	
		print('<input type="radio" name="quali" value="2" title="corrette" ');
		if(isset($_SESSION['QUALI']) AND $_SESSION['QUALI']==2) echo "checked";
		print('	/>');
		print(' <img src="images/sem_verde.gif" title="corrette" /> corrette&nbsp;&nbsp;&nbsp;');

	}

	print('<input type="radio" name="quali" value="0" ');
	if(!isset($_SESSION['QUALI']) OR $_SESSION['QUALI']==0) echo "checked";
	print('	/> ');
	print(' <img src="images/sem_tutte.gif" title="tutte" /> tutte ');

	print('<input type="submit" name="action" value="visualizza" title="tutte" />');
	print('</form>'."\n");
	print('</div>'."\n");
}

/************************************************************************
* funzione pulisci	    						*
*************************************************************************/
function pulisci($testo) {

	$testo = str_replace("'","''",$testo);
	$testo=utf8_decode($testo);
	$testo=stripslashes($testo);
	return $testo;

	/*$testo = str_replace("'",'&#39;',$testo);
	$testo = str_replace('"','&quot;',$testo);
	return stripslashes($testo);*/
}

/************************************************************************
* funzione pulisci	    						*
*************************************************************************/
function pulisci_lettura($testo) {

	$testo=utf8_encode($testo);
	$testo=stripslashes($testo);
	return $testo;

	/*$testo = str_replace("'",'&#39;',$testo);
	$testo = str_replace('"','&quot;',$testo);
	return stripslashes($testo);*/
}

/************************************************************************
* funzione vaiacapo()	    						*
*************************************************************************/
function vaiacapo($testo) {

	$testo = str_replace(chr(13).chr(10),'<br>',$testo);
	return $testo;
}

/************************************************************************
* funzione open_center	    											*
*************************************************************************/
function open_center() {

	global $PHP_SELF;
	//print_r($_SESSION['TROVATE']);
	//print_r($_SESSION['CERCATE']);
?>

		<!-- inizio contenuto centrale -->
		
<div id="top">
	<img src="images-new/remedlogo.gif" />
</div>
<form id="sform" style="float:right">
	<p>
	Value 1 : <input type="text" name="val1" value="" autocomplete="off" />
    Value 2 : <input type="text" name="val2" value="3" /><br />
    Value 3 : 
    <select name="val3">
    	<option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
        <option value="4">Four</option>
        <option value="5">Five</option>
    </select>
    Value 4 : <input type="checkbox" name="val4" id="val4" value="4" />
    </p>
    <p>
    <input type="reset" value="Reset" />
    <input type="submit" value="Submit" />
    </p>
    
</form>
<div id="wrap-bar">
	<div id="contactLink"> mostra / nascondi men�</div>
	
</div>

<div id="menu" class="urbangreymenu">
<? 

$conn = db_connect();
for($i=1; $i<=5; $i++) {
?>
<h3 class="headerbar"><a href="#"><?="livello".$i?></a></h3>

<ul class="submenu">
<?
	$key = 0;
	$query = "SELECT id,nome,link FROM menu WHERE (livello = ".$i.") AND (status = 1) ORDER BY ordine";
	$result = mssql_query($query, $conn);
	
	
	if(!$result) error_message(mssql_error());
		while($row = mssql_fetch_assoc($result)) {
			$id = $row['id'];
			$nome_menu = $row['nome'];
			$link_menu = $row['link'];
		
			if(in_array($id, $_SESSION['PERMESSI'])) 
			{
?>
	<li><a href="<?=$link_menu?>" title="<?$nome_menu?>"><?=$nome_menu?></a></li>
<?
	}
	$key++;
	}
	print("</ul>");
} 
?>

</div>

		<div id="wrap-content">
		<!--<div id="contenuto_principale">-->
<?php
}

/************************************************************************
* funzione close_center	    											*
*************************************************************************/
function close_center() {

	global $PHP_SELF;
?>
		<div id="credits" align="center"><?php print CMS.' - '.VERSIONE; ?> <a href="http://www.braincomputing.com"><img src="images/brain.gif" title="vai al sito di Brain Computing"></a> &#169; 2006 - <?php print date(Y); ?></div>
		</div>
		<!--</div>-->
		<!-- fine contenuto centrale -->
<?php

if (isset($conn)) mssql_close($conn);
}

/*************************************************************************
* funzione open_center_home												*
*****************************/
/*function open_center_home() {

	global $PHP_SELF;
?>
		<!-- fine colonna sinistra -->
		</div>

		<!-- inizio contenuto centrale -->
		<div class="center_home">
<?php
}*/


/************************************************************************
* funzione semaforo														*
*************************************************************************/
function semaforo($corretto) {

	print('<span class="semaforo">');
	if($_SESSION['UTENTE']->get_validazione()=='y') {
		if($corretto=='y') print('<img src="images/sem_verde.gif" alt="corretta" />');
		else if($corretto=='v') print('<img src="images/sem_arancio.gif" alt="da convalidare" />');
		else if($corretto=='n') print('<img src="images/sem_rosso.gif" alt="non corretta" />');
	}
	else {
		if($corretto=='y' || $corretto=='v') print('<img src="images/sem_verde.gif" alt="corretta" />');
		else if($corretto=='n') print('<img src="images/sem_rosso.gif" alt="non corretta" />');
	}
	print('</span>');

}


/************************************************************************
* funzione html_footer	    											*
*************************************************************************/
function html_footer() {

	global $PHP_SELF;

	/*echo "annuario ".$_SESSION['ANNUARIO']."<br>";
	echo "settore ".$_SESSION['SETTORE']."<br>";
	echo "regione ".$_SESSION['REGIONE']."<br>";*/

	/*if(isset($_SESSION['NEWS'])) echo "news:".$_SESSION['NEWS']->get_id();
	if(isset($_SESSION['ARCHIVIO']))echo "<br>archivio:".$_SESSION['ARCHIVIO']->get_id();
	if(isset($_SESSION['DOSSIER'])) echo "<br>dossier:".$_SESSION['DOSSIER']->get_id();
	if($_SESSION['HOME']) echo "home =".$_SESSION['HOME'];
	if(isset($_SESSION['TABLE_HOME'])) echo "tabella home =".$_SESSION['TABLE_HOME'];
	if(isset($_SESSION['HOME_FLASH'])) echo "notizia flash:".$_SESSION['HOME_FLASH'];
	if(isset($_SESSION['HOME_PPIANO'])) echo "primo piano:".$_SESSION['HOME_PPIANO'];*/
	//echo "<br>struttura = ".$_SESSION['STRUTTURA1']->get_label();
	//print_r($_SESSION['PERMESSI']);
?>
</body>
</html>
<?php
}

/************************************************************************
* funzione error_message    											*
*************************************************************************/
function error_message($msg) {
echo "<SCRIPT language=\"JavaScript\">alert(\"$msg\");history.go(-1)</SCRIPT>";
exit;
}


/************************************************************************
* funzione confirm   													*
*************************************************************************/
function confirm($msg, $url) {
echo "<SCRIPT language=\"JavaScript\">alert(\"$msg\");window.location='".$url."';</SCRIPT>";
exit;
}


/************************************************************************
* funzione genera_password()   													*
*************************************************************************/
function genera_password() {

	// Imposto la lunghezza della password a 10 caratteri
	$lung_pass = 8;
	
	// Creo un ciclo for che si ripete per il valore di $lung_pass
	for ($x=1; $x<=$lung_pass; $x++)
	{
	  // Se $x � multiplo di 2...
	  if ($x < 6){
	
		// Aggiungo una lettera casuale usando chr() in combinazione
		// con rand() che genera un valore numerico compreso tra 97
		// e 122, numeri che corrispondono alle lettere dell'alfabeto
		// nella tabella dei caratteri ASCII
		$mypass = $mypass . chr(rand(97,122));
	
	  // Se $x non � multiplo di 2...
	  }else{
	
		// Aggiungo alla password un numero compreso tra 0 e 9
		$mypass = $mypass . rand(0,9);
	
	  }
	}
	
	return $mypass;
}

/************************************************************************
* funzione malicius()   													*
*************************************************************************/

function Malicius(){

   $injection="False";
   foreach ($_REQUEST as $k => $v) {
   
    if (strpos($v,"<script")>0) $injection="True";
	 if (strpos($v,"exec(")>0) $injection="True";
	 if (strpos($v,"xtype")>0) $injection="True";
	 if (strpos($v,"convert(")>0) $injection="True";
	 if (strpos($v,"function")>0) $injection="True";
	 if (strpos($v,"varchar")>0) $injection="True";
	 if (strpos($v,"exec (")>0) $injection="True";
	 if (strpos($v,"set @")>0) $injection="True";
	 if (strpos($v,"0x")>0) $injection="True";
	 if (strpos($v,"ipt src")>0) $injection="True";
	 if (strpos($v,"ipt+src")>0) $injection="True";
	 if (substr($v,0,7)=="<script") $injection="True";
	 if (substr($v,0,5)=="exec(") $injection="True";
	 if (substr($v,0,5)=="xtype") $injection="True";
	 if (substr($v,0,8)=="convert(") $injection="True";
	 if (substr($v,0,8)=="function") $injection="True";
	 if (substr($v,0,7)=="varchar") $injection="True";
	 if (substr($v,0,6)=="exec (") $injection="True";
	 if (substr($v,0,5)=="set @") $injection="True";
	 if (substr($v,0,8)=="nvarchar") $injection="True";
	 if (substr($v,0,5)=="cast(") $injection="True";
	 if (substr($v,0,2)=="0x") $injection="True";
	 if (substr($v,0,7)=="ipt src") $injection="True";
	 if (substr($v,0,7)=="ipt+src") $injection="True";	  
   }
   if ($injection=="True") {
  
   	print("<script type='text/javascript'>window.location.href('feder_mal.php?id=strunz');</script>");
   	exit();
	}
}

function valid_date($strdate)
{	
	date_default_timezone_set('Europe/Rome');	
	if ($strdate!=""){
		//$strdate = date_create($strdate);
		//$strdate=date_format($strdate, 'd/m/Y');		
		if((substr_count($strdate,"1900"))<>0)
		return "";
		else
		return $strdate;
		}else{
		return "";
		}
}

/************************************************************************
* funzione formatta_data()   													*
*************************************************************************/

function formatta_data($data){

if ((strlen($data)==10)or($data==""))
{
return $data;
exit();
}

date_default_timezone_set('Europe/Rome');

$datetime = date_create($data);
if ($datetime!=""){
$data=date_format($datetime, 'd/m/Y');	

return $data;
}
//exit();

/*
					if (strlen($data)>10) {
					   $gg = substr($data,2,1);
					   
						if($gg==" ") {
							$gg =substr($data,0,2);
							$mm = substr($data,3,3);
							$aa = substr($data,7,4);
							}else{
							$gg = "0".substr($data,0,1);
							$mm = substr($data,2,3);
							$aa = substr($data,6,4);
							}
						
						switch($mm) {	
							case 'gen': $mm="01";
							break;
							case 'feb': $mm="02";
							break;
							case 'mar': $mm="03";
							break;
							case 'apr': $mm="04";
							break;
							case 'mag': $mm="05";
							break;
							case 'giu': $mm="06";
							break;
							case 'lug': $mm="07";
							break;
							case 'ago': $mm="08";
							break;
							case 'set': $mm="09";
							break;
							case 'ott': $mm="10";
							break;
							case 'nov': $mm="11";
							break;
							case 'dic': $mm="12";
							break;
						}
						return($gg."/".$mm."/".$aa);
					}
					else
					{
					return $data;
					}*/
}

function riduci_foto($path,$dest,$sourcefile,$h,$w){

		list($width, $height, $type, $attr) = getimagesize($path.$sourcefile);			
		$altezza=$h;
		$larghezza=round((($width*$altezza)/$height),0);		

		if($larghezza<$w){
			$larghezza=$w;
			$altezza=round((($height*$larghezza)/$width),0);			
		}
		
		// Creo la versione 120*90 dell'immagine (thumbnail)
		$thumb = imagecreatetruecolor($larghezza, $altezza);
			
		switch($type) {
				case '1': 
					$source = imagecreatefromgif($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);		
					// Salvo l'immagine ridimensionata
					imagegif($thumb,$dest."tb_".$sourcefile);
				break;
				case '2': 
					$source = imagecreatefromjpeg($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);
					// Salvo l'immagine ridimensionata
					imagejpeg($thumb,$dest."tb_".$sourcefile, 75);					
				break;
				case '3': 
					$source = imagecreatefrompng($path.$sourcefile);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $larghezza, $altezza, $width, $height);
					// Salvo l'immagine ridimensionata
					imagepng($thumb,$dest."tb_".$sourcefile);
				break;
		}		
		
}

function ftp_del_file($nomefile)
{
	$ftp_server = IP_ADDR;
	$ftp_user = FTP_USR;
	$ftp_password = FTP_PWD;

	$conn_id = ftp_connect($ftp_server);
	$login_result = ftp_login($conn_id, $ftp_user, $ftp_password);

	if((!$conn_id) || (!$login_result))
	{
            echo "FTP connection has failed!";
            echo "Attempted to connect to $ftp_server for user $ftp_user";
   	}

	ftp_delete($conn_id, $nomefile);
	ftp_close($conn_id); 
	
}

?>
