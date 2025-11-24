<?php
function test_tot_prove_superate($idtestclinico,$etapaziente_data_test)
{
$conn=db_connect();

$query = "SELECT * from re_test_clinici_result where idtestclinico=$idtestclinico";	

	
			$rsc = mssql_query($query, $conn);
	
			if(!$rsc) error_message(mssql_error());
	
			
				$i=0;
				$superati=0;
					while($rowc = mssql_fetch_assoc($rsc))   
					{
						//$punteggio=(int)($rowc['punteggio']);
						//$limite_default=(int)($rowc['limite_default']);
						$punteggio=$rowc['punteggio'];
						$limite_default=$rowc['limite_default'];
						$idrange=$rowc['idrange'];
						$iditem=$rowc['iditem'];
						$idprova=$rowc['idprova'];
						$tipo_valutazione=$rowc['tipo_valutazione'];
						
						$new_limite=get_limite_normativo($idprova,$etapaziente_data_test);
						$tipo_dato=get_tipo_dato_limite_normativo($iditem,$etapaziente_data_test);
						
						if ($new_limite<>"")
							$limite_default=(int)($new_limite);
						
						
						if (check_prova_superata($punteggio,$limite_default,$tipo_valutazione,$tipo_dato))
							$superati++;
				
					$i++;
					}
$perc_superati=round(($superati/$i)*100,2);

return $perc_superati;
}

function check_prova_superata($punteggio,$limite,$tipo_val,$tipo_dat)
{

//echo($tipo_val." ".$punteggio." ".$limite."<br />");

// tipo dato=1 numerico
// tipo dato=2 data
//tipo dato=3 orario
$tipo_dato=(int)($tipo_dat);
$tipo_valutazione=(int)($tipo_val);

if (($tipo_dato==1) or ($tipo_dato==0))
{
$p=(int)($punteggio);
$l=(int)($limite);

	
	switch ($tipo_valutazione)
	{
	
		// la prova è superata se il punteggio è maggiore o uguale del limite noramtivo
		case 1:
			if ($p>$l)
				return true;
			else
			    return false;
				
						
		break;
		// la prova è superata se il punteggio è minore o uguale del limite noramtivo
		case 2:
			if ($p<$l)
				return true;
			else
			    return false;
		break;
		// la prova è superata se il punteggio è uguale al limite normativo
		case 3:
				if ($p==$l)
				return true;
			else
			    return false;
		break;
		case 4:
				if ($p<=$l)
				return true;
			else
			    return false;
		break;
		case 5:
				if ($p>=$l)
				return true;
			else
			    return false;
		break;

	}	
	
}
elseif($tipo_dato==2)
{
$p=($punteggio);
$l=($limite);

$arr_orari_p= split(":", $p);
$arr_orari_l= split(":", $l);

$ph=(int)$arr_orari_p[0];
$lh=(int)$arr_orari_l[0];

$pm=(int)$arr_orari_p[1];
$lm=(int)$arr_orari_l[1];

$ps=(int)$arr_orari_p[2];
$ls=(int)$arr_orari_l[2];
switch ($tipo_valutazione)
	{
		case 1:
			if (($ph>=$lh) and ($pm>=$lm) and ($ps>=$ls))
				return true;
			else
			    return false;
				
						
		break;
		// la prova è superata se il punteggio è minore o uguale del limite noramtivo
		case 2:
			if (($ph<=$lh) and ($pm<=$lm) and ($ps<=$ls))
				return true;
			else
			    return false;
		break;
		// la prova è superata se il punteggio è uguale al limite normativo
		case 3:
				if (($ph==$lh) and ($pm==$lm) and ($ps==$ls))
				return true;
			else
			    return false;
		break;
	}
}

}


function confronta_prove($punteggio,$limite,$tipo_valutazione)
{

$p=(int)($punteggio);
$l=(int)($limite);
//echo($tipo_valutazione." ".$punteggio." ".$limite."<br />");
	switch ($tipo_valutazione)
	{
	
		// la prova è superata se il punteggio è maggiore o uguale del limite noramtivo
		case 1:
			if ($p>$l)
				return true;
			else
			    return false;
				
						
		break;
		// la prova è superata se il punteggio è minore o uguale del limite noramtivo
		case 2:
			if ($p<$l)
				return true;
			else
			    return false;
		break;
		// la prova è superata se il punteggio è uguale al limite normativo
		case 3:
				if ($p==$l)
				return true;
			else
			    return false;
		break;
		case 4:
				if ($p<=$l)
				return true;
			else
			    return false;
		break;
		case 5:
				if ($p>=$l)
				return true;
			else
			    return false;
		break;
		
	}	
	


}

function get_testclinico_precedente($idtestclinico,$idtest,$idpaziente,$idcartella,$idimpegnativa)
{

$idtestclinico_old=0;
$conn=db_connect();
//$query = "SELECT top 1 * from test_clinici_compilati where idtest=$idtest and idtestclinico<$idtestclinico and idpaziente=$idpaziente order by idtestclinico desc";
$query = "SELECT top 1 * from re_test_clinici_result where idtest=$idtest and idtestclinico<$idtestclinico and idpaziente=$idpaziente";


if ($idcartella>0)
$query.=" and idcartella=$idcartella";
if ($idimpegnativa>0)
$query.=" and idimpegnativa=$idimpegnativa";

//echo($query."<br />");
$rsc = mssql_query($query, $conn);
	
if(!$rsc) error_message(mssql_error());
if($rowc = mssql_fetch_assoc($rsc))   
$idtestclinico_old=$rowc['idtestclinico'];

return $idtestclinico_old;
}

function get_testclinico_precedente_desc($idtestclinico,$idtest,$idpaziente,$idcartella,$idimpegnativa)
{

$idtestclinico_old=0;
$conn=db_connect();
$query = "SELECT top 1 * from test_clinici_compilati where idtest=$idtest and idtestclinico<$idtestclinico and idpaziente=$idpaziente";

if ($idcartella>0)
$query.=" and idcartella=$idcartella";
if ($idimpegnativa>0)
$query.=" and idimpegnativa=$idimpegnativa";

$query.=" order by idtestclinico desc";

//echo($query."<br />");
$rsc = mssql_query($query, $conn);
	
if(!$rsc) error_message(mssql_error());
if($rowc = mssql_fetch_assoc($rsc))   
$idtestclinico_old=$rowc['idtestclinico'];

return $idtestclinico_old;
}


function confronta_array_caricati($arr1,$arr2)
{

return true;
}

function get_andamento_prove($idtest,$idpaziente,$prova_corrente,$idtestclinico,$idprova,$idcartella,$idimpegnativa)
{
$conn=db_connect();
$idtestclinico_old=get_testclinico_precedente($idtestclinico,$idtest,$idpaziente,$idcartella,$idimpegnativa);
$query="";
if ($idtestclinico_old>0)
$query = "SELECT * from re_test_clinici_result where idtest=$idtest and (idtestclinico=$idtestclinico_old or idtestclinico=$idtestclinico) and idpaziente=$idpaziente";
else
$query = "SELECT * from re_test_clinici_result where idtest=$idtest and (idtestclinico=$idtestclinico) and idpaziente=$idpaziente";

if ($idprova>0)
$query=$query." and idprova=$idprova";

if ($idcartella>0)
$query.=" and idcartella=$idcartella";
if ($idimpegnativa>0)
$query.=" and idimpegnativa=$idimpegnativa";

//echo($query."<br />");
$rsc = mssql_query($query, $conn);
	
if(!$rsc) error_message(mssql_error());
$array_prove= Array();
$array_testclinici= Array();
while($rowc = mssql_fetch_assoc($rsc))   
					{
						$punteggio=(int)($rowc['punteggio']);
						$limite_default=(int)($rowc['limite_default']);
						$idrange=$rowc['idrange'];
						$iditem=$rowc['iditem'];
						$idprova=$rowc['idprova'];
						$idtestc=$rowc['idtestclinico'];
						$tipo_valutazione=$rowc['tipo_valutazione'];
						if (!in_array($idprova, $array_prove)) {
							$array_prove[sizeof($array_prove)+1]=$idprova;
							$array_tipo_val[sizeof($array_tipo_val)+1]=$tipo_valutazione;
						}
						if (!in_array($idtestc, $array_testclinici)) {
							$array_testclinici[sizeof($array_testclinici)+1]=$idtestc;
							//print_r($array_testclinici);
							
						}
						//$new_limite=get_limite_normativo($iditem,$etapaziente_data_test);
						$z=sizeof($array_punteggi[$idprova]);
						$array_punteggi[$idprova][$z]=$punteggio;
						$n=sizeof($array_confronto[$idtestc]);
						$array_confronto[$idtestc][$n]=$iditem;
						
					
							
						
					$i++;
					}
$y=sizeof($array_prove);
$conta_elem=1;
$migliorati=0;
$stazionari=0;
$regrediti=0;
$migliorati_rel=0;
$stazionari_rel=0;
$regrediti_rel=0;
$prova_old=-1;
$calcola=false;

//print_r($array_confronto);

//print_r($array_confronto);
if (!is_array($array_confronto[$array_testclinici[2]]))
$calcola=true;
else
{
$s1=sizeof($array_confronto[$array_testclinici[1]]);
$s2=sizeof($array_confronto[$array_testclinici[2]]);

//echo($s1." ".$s2);
if ($s1==$s2)
$calcola=true;
else
$calcola=false;
}

while ($conta_elem<=$y)
{
$prova=$array_prove[$conta_elem];
$tipo_val=$array_tipo_val[$conta_elem];
$prova_element=sizeof($array_punteggi[$prova]);
//print_r($array_punteggi);
//echo("pr".$prova_element."<br />");

//echo(check_prova_superata($array_punteggi[$prova][$prova_element-1],$array_punteggi[$prova][0],$tipo_val));

	
	//print($array_punteggi[$prova][0]." - ".$array_punteggi[$prova][1]." - ".$tipo_val."<br />");
	
	$ck=confronta_prove($array_punteggi[$prova][1],$array_punteggi[$prova][0],$tipo_val);
	
	if ($array_punteggi[$prova][0]==$array_punteggi[$prova][1])
		$stazionari++;
	elseif($ck==true)
		$migliorati++;
	else
		$regrediti++;
		
	//echo("m".$migliorati." s".$stazionari." r".$regrediti."<br /><br />");	
		
		//echo($prova_old." ".$prova."<br />");
		/*if ($prova_corrente!=0)
		{
		
		$prova_old=$prova;
		if ($array_punteggi[$prova][$prova_corrente]==$array_punteggi[$prova][$prova_corrente+1])
			$stazionari_rel++;
		elseif(check_prova_superata($array_punteggi[$prova][$prova_corrente+1],$array_punteggi[$prova][$prova_corrente],$tipo_val))
			$migliorati_rel++;
		else
			$regrediti_rel++;	
		}*/
			
	
		
	
		
$conta_elem++;
}


if ($calcola==false)
{
$migliorati=0;
$stazionari=0;
$regrediti=0;
}


$arr_andamento[0]=$migliorati;
$arr_andamento[1]=$stazionari;
$arr_andamento[2]=$regrediti;
/*$arr_andamento[3]=$migliorati_rel;
$arr_andamento[4]=$stazionari_rel;
$arr_andamento[5]=$regrediti_rel;*/

//print_r($arr_andamento);

return $arr_andamento;					
		//echo("<br>".$migliorati." ".$stazionari." ".$regrediti);			
//print_r($array_prove);
//print_r($array_punteggi);

					
}


function get_andamento_prove_relativo($idtest,$idpaziente,$prova_corrente,$idtestclinico,$idcartella,$idimpegnativa)
{

$conn=db_connect();
$idtestclinico_old=get_testclinico_precedente_desc($idtestclinico,$idtest,$idpaziente,$idcartella,$idimpegnativa);
$query="";
if ($idtestclinico_old>0)
$query = "SELECT * from re_test_clinici_result where idtest=$idtest and (idtestclinico=$idtestclinico_old or idtestclinico=$idtestclinico) and idpaziente=$idpaziente";
else
$query = "SELECT * from re_test_clinici_result where idtest=$idtest and (idtestclinico=$idtestclinico) and idpaziente=$idpaziente";

if ($idcartella>0)
$query.=" and idcartella=$idcartella";
if ($idimpegnativa>0)
$query.=" and idimpegnativa=$idimpegnativa";

//echo($query."<br />");
$rsc = mssql_query($query, $conn);
	
if(!$rsc) error_message(mssql_error());
$array_prove= Array();
while($rowc = mssql_fetch_assoc($rsc))   
					{
						$punteggio=(int)($rowc['punteggio']);
						$limite_default=(int)($rowc['limite_default']);
						$idrange=$rowc['idrange'];
						$iditem=$rowc['iditem'];
						$idprova=$rowc['idprova'];
						$tipo_valutazione=$rowc['tipo_valutazione'];
						if (!in_array($idprova, $array_prove)) {
							$array_prove[sizeof($array_prove)+1]=$idprova;
							$array_tipo_val[sizeof($array_tipo_val)+1]=$tipo_valutazione;
						}
						//$new_limite=get_limite_normativo($iditem,$etapaziente_data_test);
						$z=sizeof($array_punteggi[$idprova]);
						$array_punteggi[$idprova][$z]=$punteggio;
							
						
					$i++;
					}
$y=sizeof($array_prove);
$conta_elem=1;
$migliorati=0;
$stazionari=0;
$regrediti=0;
$migliorati_rel=0;
$stazionari_rel=0;
$regrediti_rel=0;
$prova_old=-1;
while ($conta_elem<=$y)
{
$prova=$array_prove[$conta_elem];
$tipo_val=$array_tipo_val[$conta_elem];
$prova_element=sizeof($array_punteggi[$prova]);
//print_r($array_punteggi);
//echo("pr".$prova_element."<br />");

//echo(check_prova_superata($array_punteggi[$prova][$prova_element-1],$array_punteggi[$prova][0],$tipo_val));

	
	//print($array_punteggi[$prova][0]." - ".$array_punteggi[$prova][1]." - ".$tipo_val."<br />");
	
	$ck=confronta_prove($array_punteggi[$prova][1],$array_punteggi[$prova][0],$tipo_val);
	
	if ($array_punteggi[$prova][0]==$array_punteggi[$prova][1])
		$stazionari++;
	elseif($ck==true)
		$migliorati++;
	else
		$regrediti++;
		
	//echo("m".$migliorati." s".$stazionari." r".$regrediti."<br /><br />");	
		
		//echo($prova_old." ".$prova."<br />");
		/*if ($prova_corrente!=0)
		{
		
		$prova_old=$prova;
		if ($array_punteggi[$prova][$prova_corrente]==$array_punteggi[$prova][$prova_corrente+1])
			$stazionari_rel++;
		elseif(check_prova_superata($array_punteggi[$prova][$prova_corrente+1],$array_punteggi[$prova][$prova_corrente],$tipo_val))
			$migliorati_rel++;
		else
			$regrediti_rel++;	
		}*/
			
	
		
	
		
$conta_elem++;
}





$arr_andamento[0]=$migliorati;
$arr_andamento[1]=$stazionari;
$arr_andamento[2]=$regrediti;
/*$arr_andamento[3]=$migliorati_rel;
$arr_andamento[4]=$stazionari_rel;
$arr_andamento[5]=$regrediti_rel;*/

//print_r($arr_andamento);

return $arr_andamento;					
		//echo("<br>".$migliorati." ".$stazionari." ".$regrediti);			
//print_r($array_prove);
//print_r($array_punteggi);

					
}


function calcola_eta_alla_data_del_test($data_osservazione,$idpaziente)
{
$data_nascita=get_data_nascita_paziente($idpaziente);
$data_oss=formatta_data($data_osservazione);
$data_nas=formatta_data($data_nascita);
$eta_in_giorni=diff_in_giorni($data_oss,$data_nas);
$eta_in_anni=(int)($eta_in_giorni/365);
return $eta_in_anni;

}

function get_data_nascita_paziente($idpaziente)
{
$conn=db_connect();
$query = "SELECT DataNascita from utenti where idUtente=$idpaziente";	
	
			
			$rs1 = mssql_query($query, $conn);
	
			$data_nascita="";
			if(!$rs1) error_message(mssql_error());
				if($row1 = mssql_fetch_assoc($rs1))   
					$data_nascita=$row1['DataNascita'];	

return $data_nascita;
}

function get_limite_normativo($iditem,$etapaziente_data_test)
{

$conn=db_connect();
$query = "SELECT limite,CAST(valore_da as int),CAST(valore_a as int),iditem from re_test_clinici_item_limiti where idprova=$iditem and valore_da<=$etapaziente_data_test and valore_a>=$etapaziente_data_test";	
$rsc = mssql_query($query, $conn);

	if(!$rsc) error_message(mssql_error());
	$conta=mssql_num_rows($rsc);
	
	if ($conta==0)
	return "";
	else
	{
	if($row = mssql_fetch_assoc($rsc))
		$lim=$row['limite'];
		return $lim;
	
	
	}
	

}


function get_tipo_dato_limite_normativo($iditem,$etapaziente_data_test)
{

$conn=db_connect();
$query = "SELECT top 1 limite,CAST(valore_da as int),CAST(valore_a as int),iditem from re_test_clinici_item_limiti where iditem=$iditem and valore_da<=$etapaziente_data_test and valore_a>=$etapaziente_data_test";	
$rsc = mssql_query($query, $conn);

	if(!$rsc) error_message(mssql_error());
	$conta=mssql_num_rows($rsc);
	
	if ($conta==0)
	return "";
	else
	{
		$tipo_dato=$rsc['tipo_dato'];
		return $tipo_dato;
	
	
	}
	

}


function crea_test_clinico_ramificato($idtest,$idtestclinicopadre)
{

	$datains = date('d/m/Y');
	$orains = date('H:i');
	$ipins = $_SERVER['REMOTE_ADDR'];
	$opeins = $_SESSION['UTENTE']->get_userid();
	
	$idcartella=$_POST['idcartella'];
	$idpaziente=$_POST['idpaziente'];
	//$idtest=$_POST['idtest'];
	$idimpegnativa=$_POST['idimpegnativa'];
	$data_osservazione=$_POST['data_osservazione'];
	$conn = db_connect();
	$query="insert into test_clinici_compilati (idtest,idpaziente,idcartella,idimpegnativa,data_osservazione,datains,orains,ipins,opeins,is_padre,idtestclinicopadre) values ($idtest,$idpaziente,$idcartella,$idimpegnativa,'$data_osservazione','$datains','$orains','$ipins',$opeins,0,$idtestclinicopadre)";
	mssql_query($query, $conn);
	
	
	$query="select max (idtestclinico) as idtestclinico from test_clinici_compilati where opeins=$opeins and idtest=$idtest";
	$rs = mssql_query($query, $conn);
		
	if(!$rs) error_message(mssql_error());
$idtestclinico=0;
	if($row = mssql_fetch_assoc($rs))
		$idtestclinico=$row['idtestclinico'];
	mssql_free_result($rs);	
	
	
	$query="select * from re_test_clinici_prove where idtest=$idtest";
	$rs = mssql_query($query, $conn);
		
	if(!$rs) error_message(mssql_error());

	while($row = mssql_fetch_assoc($rs))
	{
		$idprova=$row['idprova'];
		$risposta="risposta".$idprova;
		$risposta_postata=$_POST[$risposta];
		$query="insert into test_clinici_compilati_details (idtestclinico_compilato,idprova,iditem) values ($idtestclinico,$idprova,$risposta_postata)";
		mssql_query($query, $conn);
	}
	mssql_free_result($rs);	
	

}


function crea_stat_test_clinico_ramificato($idtest,$idtestclinicopadre,$idcartella,$idpaziente,$idtestclinico)
{

	$conn=db_connect();
	
	$andamento_assoluto=get_andamento_prove($idtest,$idpaziente,$conta,$idtestclinico,0,$idcartella);
	//$andamento_relativo=get_andamento_prove_relativo($idtest,$idpaziente,$conta,$idtestclinico,$idcartella);
	return ($andamento_assoluto);
}


// consente di calcolare le statistiche per i figli del test clinico
function calcola_stat_figli_test_clinici($idtestclinico,$idtest_padre,$idtestclinicopadre,$idcartella,$idpaziente)
{
			$x=0;
			$conn=db_connect();
			$query = "SELECT * from re_test_clinici_ramificati where idtest_padre=$idtest_padre";	
			$rs_ram = mssql_query($query, $conn);
			$conta=mssql_num_rows($rs_ram);
			$conta=0;
			while($row_ram = mssql_fetch_assoc($rs_ram))   
			{
			$idtest_figlio=$row_ram['idtest_figlio'];
			
			$query = "SELECT * from re_test_clinici_compilati_ram where idtest=$idtest_figlio and idtestclinicopadre=$idtestclinicopadre";	
			$rs = mssql_query($query, $conn);
			if($row = mssql_fetch_assoc($rs ))   
			{
				$idtestclinico=$row['idtestclinico'];
				$arr_andamento[$conta]=crea_stat_test_clinico_ramificato($idtest_figlio,$idtestclinicopadre,$idcartella,$idpaziente,$idtestclinico);
				print("conta".$idtestclinico."<br />");
				print_r($arr_andamento[$conta]);
				
				
				//$tot_prove=$arr_andamento[0]+$arr_andamento[1]+$arr_andamento[2];
						
						if($tot_prove>0)
						{
						
						$p_migliorate_perc=round(($andamento_assoluto[0]/$tot_prove)*100,2);
						$p_stazionarie_perc=round(($andamento_assoluto[1]/$tot_prove)*100,2);
						$p_regredite_perc=round(($andamento_assoluto[2]/$tot_prove)*100,2);
						$tot_prove=$andamento_relativo[0]+$andamento_relativo[1]+$andamento_relativo[2];
						$p_migliorate_perc_rel=round(($andamento_relativo[0]/$tot_prove)*100,2);
						$p_stazionarie_perc_rel=round(($andamento_relativo[1]/$tot_prove)*100,2);
						$p_regredite_perc_rel=round(($andamento_relativo[2]/$tot_prove)*100,2);
						}
				
				
				calcola_stat_figli_test_clinici($idtestclinico,$idtest_figlio,$idtestclinicopadre,$idcartella,$idpaziente);
			}
			$conta++;
			}	


			
}




// consente di stampare l'html dei test figli in fase di compilazione
function calcola_figli_test_clinici($idtestclinico,$idtest_padre,$idtestclinicopadre)
{
			$x=0;
			$conn=db_connect();
			$query = "SELECT * from re_test_clinici_ramificati where idtest_padre=$idtest_padre";	
			$rs_ram = mssql_query($query, $conn);
			$conta=mssql_num_rows($rs_ram);
			
			while($row_ram = mssql_fetch_assoc($rs_ram))   
			{
			$idtest_figlio=$row_ram['idtest_figlio'];
			crea_test_clinico_ramificato($idtest_figlio,$idtestclinicopadre);
			calcola_figli_test_clinici($idtestclinico,$idtest_figlio,$idtestclinicopadre);
			}		
}

// consente di stampare l'html dei test figli in fase di compilazione
function stampa_test_figli($idtest_padre,$volte_idd)
{
			$x=0;
			$conn=db_connect();
			$query = "SELECT * from re_test_clinici_ramificati where idtest_padre=$idtest_padre";	
			$rs_ram = mssql_query($query, $conn);
			$conta=mssql_num_rows($rs_ram);
			if ($conta>0)
			$x=$volte_idd+1;
			$y=0;
			while($row_ram = mssql_fetch_assoc($rs_ram))   
			{
			$y++;
			
			$idtest_figlio=$row_ram['idtest_figlio'];
			$nome_test_figlio=strtolower($row_ram['nome']);

			$query = "SELECT * from test_clinici_prove where idtest=$idtest_figlio";	
			$rs1 = mssql_query($query, $conn);
			?>
			<tr>
			<td colspan="7">&nbsp;</td>
			<td>&nbsp;</td>
			</tr>
			<tr>
			<?
			$n=$x;
			while($n>1)
			{
			print("<td>&nbsp;</td>");
			$n--;
			}
			$colspan=8-$x;
			?>
			<td colspan="<?=$colspan?>"><span class="id_notizia_cat"><strong><?=$nome_test_figlio?></strong></span></td>
			<td>&nbsp;</td>
			</tr>
			
			
			<?
	
			if(!$rs1) error_message(mssql_error());
			$conta=mssql_num_rows($rs1);
				$i=1;
				
					while($row1 = mssql_fetch_assoc($rs1))   
					{
					?>
					<tr>
					<?
						$idprova=$row1['idprova'];
						$descrizione=$row1['descrizione'];
						$istruzioni_operative=$row1['istruzioni_operative'];

								$n=$x;
			while($n>1)
			{
			print("<td>&nbsp;</td>");
			$n--;
			}
			?>
								<td colspan="<?=$colspan?>"><span class="id_notizia_cat"><?=$descrizione?></span></td>
								
								<?
								$query = "SELECT * from test_clinici_items where idprova=$idprova";	
								$rs2 = mssql_query($query, $conn);
								?>
								
								<td><select class="scrittura" style="width:150px" name="risposta<?=$idprova?>">
								<option value="-10001" selected>non somministrato</option>
								<?
								while($row2 = mssql_fetch_assoc($rs2))   
								{
									$iditem=$row2['iditem'];
									$etichetta=$row2['etichetta'];
									?>
									<option value="<?=$iditem?>"><?=$etichetta?></option>
									<?
								}
								?>
								
								</select>
								</td>
								</tr>
						<?
						
					}
			
			stampa_test_figli($idtest_figlio,$x);
			}		
					

}

?>