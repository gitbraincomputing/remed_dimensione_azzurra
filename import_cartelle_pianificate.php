<?php
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
include_once('include/functions_test_clinici.php');
$id_permesso = $id_menu = 2;
session_start();

$conn = db_connect();
$query="SELECT id, idutente FROM dbo.utenti_cartelle ORDER BY id";
$rs1 = mssql_query($query, $conn);
while($row=mssql_fetch_assoc($rs1)){
	$id_c=$row['id'];
	$id_u=$row['idutente'];
	importa_cartella($id_c,$id_u);
}

function importa_cartella($idcar,$idpaz){

$idcartella=$idcar;
$idpaziente=$idpaz;
$idversione=1;
$chiusa=false;
$conn = db_connect();

$query="select * from utenti_cartelle WHERE id=$idcartella";
$rs1 = mssql_query($query, $conn);
$row=mssql_fetch_assoc($rs1);
$updcount=$row['updcount'];
$datains=formatta_data($row['datains']);
$orains=$row['orains'];
$opeins=$row['opeins'];
$ipins=$row['ipins'];
$dataagg=formatta_data($row['dataagg']);
$oraagg=$row['oraagg'];
$ipagg=$row['ipagg'];
$query1="INSERT INTO cartelle_pianificazione_testata (id_cartella,updcount,datains,orains,opeins,ipins,dataagg,oraagg,ipagg) VALUES($idcartella,'$updcount','$datains','$orains','$opeins','$ipins','$dataagg','$oraagg','$ipagg')";
$rs1 = mssql_query($query1, $conn);
$query1="SELECT MAX(id_pianificazione_testata) as id_pian_test FROM cartelle_pianificazione_testata";
$rs1 = mssql_query($query1, $conn);
$row=mssql_fetch_assoc($rs1);
$id_pian_test=$row['id_pian_test'];

$query="select id, idregime, codice_cartella, versione, data_chiusura from utenti_cartelle where id=$idcartella";
$rs1 = mssql_query($query, $conn);
if($row=mssql_fetch_assoc($rs1)){ 
	$id_regime=$row['idregime'];
	$cod_cartella=$row['codice_cartella'];
	$versione=$row['versione'];
	if($row['data_chiusura']!="") $chiusa=true;
}
mssql_free_result($rs1);
$arr_idimpegnativa=array();
$query="select * from utenti_cartelle_impegnative where idcartella=$idcartella";
$rs1 = mssql_query($query, $conn);
while($row=mssql_fetch_assoc($rs1)){ 
	array_push($arr_idimpegnativa,$row['idimpegnativa']);	
}
mssql_free_result($rs1);

?>
<div id="cartella_clinica">
<div class="titoloalternativo">
            <h1>cartella clinica n. <?=$cod_cartella?>/<?=convert_versione($versione); $cart=$cod_cartella."/".convert_versione($versione);?></h1>	         			
			<!--<h2>creata il 17/12/2008 da brain</h2>
			<h2>creata il 17/12/2008 da brain</h2>-->
</div>
<?
//print_r($arr_idimpegnativa);
for($i=0;$i<=sizeof($arr_idimpegnativa);$i++){	
	if($i==0){
		$query="select * from re_cartelle_moduli_imp where (idcartella=$idcartella) and  (replica = 0 OR replica = 1) order by id_alias asc";	
		}		
	?>
	<div class="titolo_pag">
			<div class="comandi">
				<?				
				if($i>0){
					$query_imp="SELECT  idutente, idregime, DataAutorizAsl, ProtAutorizAsl, regime FROM dbo.re_pazienti_impegnative WHERE (idimpegnativa=".$arr_idimpegnativa[$i-1].")";					
					$rs_imp = mssql_query($query_imp, $conn);
					if($row_imp=mssql_fetch_assoc($rs_imp)){
						$data_auth_asl=formatta_data($row_imp['DataAutorizAsl']);
						$prot_auth_asl=$row_imp['ProtAutorizAsl'];
						$regime=$row_imp['regime'];						
					}
				echo("Impegnativa prot. <strong>".$prot_auth_asl."</strong> del <strong>".$data_auth_asl."</strong> - regime: <strong>".$regime."</strong>");	
				$idimpegnativa=$arr_idimpegnativa[$i-1];
				} ?>				
				<!--<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_cartelle('<?=$idpaziente?>','cartella_clinica');">elenco cartelle</a></div>-->
				<div class="aggiungi aggiungi_left"></div>
			</div>
	</div>
				 
	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
			<tr> 
				<th>codice sgq</th> 
				<th>modulo</th> 
				<th>figura responsabile</th> 
				<th>scadenza</th> 
				<th>ultima compilazione</th> 
				<th>esplora</th>
				<th>anteprima</th> 
				<th>aggiungi</th>			
			</tr> 
	</thead> 
	<tbody> 
			
	<?	
	$items = array(); //contiene tutti gli elementi 
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	
	if($i>0){
		$query="select * from max_vers_moduli_add where (idcartella=$idcartella) order by idmodulo asc";	
		$rs2 = mssql_query($query, $conn);		
		while($row = mssql_fetch_assoc($rs2)){	
			$query_1="SELECT dbo.cartelle_moduli.ultima_compilazione, dbo.cartelle_moduli.idcartella,dbo.cartelle_moduli.data_fissa,dbo.cartelle_moduli.trattamenti, dbo.moduli.*
						FROM dbo.moduli LEFT OUTER JOIN dbo.cartelle_moduli ON dbo.moduli.idmodulo = dbo.cartelle_moduli.idmodulo 
						WHERE (dbo.moduli.id = ".$row['idmodulo_id'].") AND (dbo.cartelle_moduli.idcartella = $idcartella)";						
			$rs1 = mssql_query($query_1, $conn);
			$row1 = mssql_fetch_assoc($rs1);
			if(($row1['replica']==2) or($row1['replica']==3)) $items[] = $row1;
		}		
	}else{
		while($row = mssql_fetch_assoc($rs)){
			$items[] = $row;
		}
	}	
		foreach($items as $row){
			$idmodulo_id=$row['id'];
			$idmodulo=$row['idmodulo'];
			$codice=$row['codice'];
			$nome=$row['nome'];
			$replica_old=$row['replica'];
			$scadenza=$row['scadenza'];
			$ultima_compilazione=$row['ultima_compilazione'];		
			$obbligatorio=$row['obbligatorio'];
			$data_fissa=formatta_data($row['data_fissa']);
			$trattamenti=$row['trattamenti'];
			$img="";
												
			if ($obbligatorio=='s')
				$img="spunto.png";
			$u_c="";			
			 if ($ultima_compilazione!='')
				$u_c=formatta_data($ultima_compilazione);
			//echo($ultima_compilazione." uc:".$u_c);
			$query4="SELECT TOP (1) dbo.moduli_medici.id_modulo, dbo.moduli_medici.id_regime, dbo.moduli_medici.status, dbo.moduli_medici.cancella, dbo.moduli_medici.id, 
					dbo.moduli_medici.id_operatore, dbo.moduli_medici.id_cartella, dbo.operatori.nome as nome, dbo.operatori.uid as uid FROM dbo.moduli_medici INNER JOIN
					dbo.operatori ON dbo.moduli_medici.id_operatore = dbo.operatori.uid WHERE (dbo.moduli_medici.status = 1) AND (dbo.moduli_medici.cancella = 'n')AND (id_modulo = $idmodulo) AND (id_regime =$id_regime) AND (dbo.moduli_medici.id_cartella = $idcartella) ORDER BY id_cartella DESC, id DESC";				
			$rs4 = mssql_query($query4, $conn);	
			
			if (mssql_num_rows($rs4)==0){
			$query4="SELECT TOP (1) dbo.moduli_medici.id_modulo, dbo.moduli_medici.id_regime, dbo.moduli_medici.status, dbo.moduli_medici.cancella, dbo.moduli_medici.id, 
					dbo.moduli_medici.id_operatore, dbo.moduli_medici.id_cartella, dbo.operatori.nome as nome, dbo.operatori.uid as uid FROM dbo.moduli_medici INNER JOIN
					dbo.operatori ON dbo.moduli_medici.id_operatore = dbo.operatori.uid WHERE (dbo.moduli_medici.status = 1) AND (dbo.moduli_medici.cancella = 'n')AND (id_modulo = $idmodulo) AND (id_regime =$id_regime) AND (dbo.moduli_medici.id_cartella = 0) ORDER BY id_cartella DESC, id DESC";				
			$rs4 = mssql_query($query4, $conn);	}
			
			$medico="";
			if($row4=mssql_fetch_assoc($rs4)) {
				$medico=$row4['nome'];						
				$id_medico=$row4['uid'];						
			}
			mssql_free_result($rs4);
			$idimp="";
			if($idimpegnativa!=""){
				$query="SELECT idimpegnativa AS idimpegnativa_val FROM dbo.utenti_cartelle_valori_impegnative WHERE (idmodulo = $idmodulo) AND (idcartella = $idcartella) AND (idimpegnativa = $idimpegnativa)";
				$rs1 = mssql_query($query, $conn);
				//echo($query);	
				if($row1 = mssql_fetch_assoc($rs1)){					
					if (($row1['idimpegnativa_val'])!="") 
						$idimp=$row1['idimpegnativa_val'];
						else
						$idimp="";
				}
				mssql_free_result($rs1);
			}
			$query="select * from max_vers_moduli where idcartella=$idcartella and idmoduloversione=$idmodulo_id ORDER BY id_modVers desc";
		   $rs1 = mssql_query($query, $conn);
			if(!$rs1) error_message(mssql_error());
			$idmoduloversione_new=0;
		
			if($row1 = mssql_fetch_assoc($rs1))   
			$idmoduloversione_new=$row1['idmoduloversione_new'];
			  
			$query="select idmodulo,stampa_continua,replica,codice from moduli where id=$idmoduloversione_new";				
			$rs1 = mssql_query($query, $conn);						
			if(!$rs1) error_message(mssql_error());
			if($row1 = mssql_fetch_assoc($rs1))	{
				$idmodulo=$row1['idmodulo'];
				$replica=$row1['replica'];
				$codice=$row1['codice'];
			}
			mssql_free_result($rs1);			
			//echo($ultima_compilazione." r:".$replica);
			if($i>0) $replica=$replica_old;
			if(((($replica==2) or ($replica==3)) and ($ultima_compilazione!='') and($i==0)) or ($replica==0) or($replica==1) or((($replica==2) or($replica==3)) and ($i>0))){	
			?>
						<tr> 
						 <td><?=$codice?></td>
						 <td><?=$nome?></td>
						 <td><?=$medico?></td>
						  
						 <td><?=$scadenza?></td> 
						 <td><?=$u_c?></td> 
						 <? 						 
						 if (($ultima_compilazione!='') and ((($idimpegnativa!="") and ($idimp!="")) or($idimpegnativa=="")))
						 {
							$utlima_compilazione=formatta_data($ultima_compilazione);
							?>
						  <td><a href="#"  ><img src="images/view.png" /></a></td> 
						  <td><a href="#"  ><img src="images/book_open.png" /></a></td> 
						  <?
						  }
						  else
							{?>
						  <td></a></td>
						  <td></a></td>
						  <?
						  }						 
						  ?>
						  <td>
						  <?
						  
							if(((($replica==2) or ($replica==3)) and(($replica_old==2) or($replica_old==3))) or ((($replica==0) or ($replica==1)) and(($replica_old==0) or($replica_old==1))))
								$congruente=1;
								else
								$congruente=0;
								
								
						if(($replica==2) and($congruente)){						
						$query="SELECT idimpegnativa AS idimpegnativa_val FROM dbo.utenti_cartelle_valori_impegnative WHERE (idmodulo = $idmodulo) AND (idcartella = $idcartella) AND (idimpegnativa = $idimpegnativa)";
						$rs1 = mssql_query($query, $conn);
						//echo($query);	
						if($row1 = mssql_fetch_assoc($rs1)){					
							if (($row1['idimpegnativa_val'])!="") 
								$idimp=$row1['idimpegnativa_val'];
								else
								$idimp="";
						}
						mssql_free_result($rs1);
						  }
						  //echo ("ro:".$replica_old." r:".$replica." c:".$congruente);
						  if  ((get_permesso_modulo($idmodulo,$_SESSION['UTENTE']->get_gid())) and  (($replica==1) or (($replica==0) and (trim($ultima_compilazione)=='')) or (($replica==2)and ($idimp=="")) or($replica==3))and (!$chiusa) and (($congruente) or ($congruente==0) and ($i>0)	)) 
						 { ?>
							<a href="#"   ><img src="images/add.png" /></a>
							<? }else {
							echo("&nbsp;");
							}?>
							</td> 
						</tr>
				<?
				//insert riga
			if($i==0) $idimpegnativa='NULL';
			//if($replica==2) $replica=0;
			//if($replica==3) $replica=1;
			if($trattamenti=="") $trattamenti=0;
			$query1="INSERT INTO cartelle_pianificazione (id_cartella_pianificazione_testata,id_impegnativa,id_modulo_padre,id_modulo_versione,obbligatorio,replica,data_fissa,trattamenti,scadenza,id_operatore)";
			$query1.="VALUES($id_pian_test,$idimpegnativa,$idmodulo,$idmodulo_id,'$obbligatorio',$replica,'$data_fissa',$trattamenti,'$scadenza',$id_medico)";
			$rs1 = mssql_query($query1, $conn);
			echo($query1);	
			}			
			
		}
		mssql_free_result($rs);
	?>
	</tbody>
	</table>
<?}?>

</div>


<?

}

?>

