<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 55;
$tablename = 'utenti';
$mod_type=3;
include_once('include/function_page.php');

function carica_cosa_fare()
{
$idprotocollo=$_REQUEST['idprotocollo'];
$conn = db_connect();


$query="select * from compilatore_contenuti where idprotocollo=$idprotocollo";
$rs = mssql_query($query, $conn);
?>
<select id="comp_cosafare" name="comp_cosafare" onchange="javascript:carica_testo_autocompilato();"  >
<option value="">seleziona</option>
<?
while($row = mssql_fetch_assoc($rs))
{   
$idcontenuto=$row['idcontenuto'];
$descrizione=$row['etichetta'];


		?>
		<option  value="<?=$idcontenuto?>"><?=$descrizione?></option>
		<?
}
?>
</select>
<?
exit();
}

function crea_testo_autocompilato($descrizione,$riferimento)
{
$testo_compilato="";
$pos_corrente_ini=0;
$pos_corrente_fin=0;
$pos_corrente=1;
$g=0;

$stack_variabili = array();

$arr_char=(count_chars($descrizione,1));
$limit=$arr_char[91];

while (($pos_corrente>0) and ($g<$limit))
{
if ((strpos($descrizione, '[', $pos_corrente_ini)) and (strpos($descrizione, ']', $pos_corrente_fin)))
{
$pos_corrente_ini = strpos($descrizione, '[', $pos_corrente_ini); 
$pos_corrente_fin = strpos($descrizione, ']', $pos_corrente_fin); 
$pos_corrente=$pos_corrente_ini+1;
$conta_caratteri=$pos_corrente_fin-$pos_corrente_ini+1;
$variabile=substr($descrizione, $pos_corrente_ini, $conta_caratteri);
$var=str_replace("[","",$variabile);
$var=str_replace("]","",$var);

if (!in_array($var, $stack_variabili)) 
array_push($stack_variabili,$var);

$pos_corrente_ini++;
$pos_corrente_fin++;
}
else
$pos_corrente=0;
$g++;
}
if (sizeof($stack_variabili)>0)
$testo_compilato=replace_variabili($stack_variabili,$descrizione,$riferimento);
else
$testo_compilato=$descrizione;

return $testo_compilato;
}


function replace_variabili($array_variabili,$descrizione,$riferimento)
{
$conn = db_connect();
$arr_rif=split(";",$riferimento);
foreach ($array_variabili as $variabile) {

$query="select * from re_compilatore_variabili_tabelle where nome_variabile='$variabile'";

$rs = mssql_query($query, $conn);
if($row = mssql_fetch_assoc($rs))
{
	$nome_tabella=$row['nome_tabella_vista'];
	$nome_campo=$row['campo_tabella'];
	$campo_riferimento=$row['campo_riferimento'];
	
		$valore="";
		$variabile_da_sost="[".$variabile."]";
		if($variabile_da_sost=="[cartella_edizione]"){
			$query="SELECT * FROM utenti_cartelle WHERE id=".$arr_rif[2];		
			$rs1 = mssql_query($query, $conn);
			if($row1 = mssql_fetch_assoc($rs1)) {
				$valore=$row1['codice_cartella'].convert_versione($row1['versione']);				
				$descrizione=str_replace($variabile_da_sost,$valore,$descrizione);
			}
			mssql_free_result($rs1);
		
		}else{
			if($campo_riferimento=="IdUtente") $riferimento=$arr_rif[0];
			if($campo_riferimento=="idimpegnativa") $riferimento=$arr_rif[1];			
			$query="select ".$nome_campo." from ".$nome_tabella. " where ". $campo_riferimento."='$riferimento'";				
			if($riferimento!=""){				
				$rs1 = mssql_query($query, $conn);
				if(mssql_num_rows($rs1)>1){					
					while($row1 = mssql_fetch_row($rs1)){
						$valore.=" - ".pulisci_lettura($row1[0]);
					}
					$valore=substr($valore, 3, strlen($valore));					
					$descrizione=str_replace($variabile_da_sost,$valore,$descrizione);
				}else{					
					if($row1 = mssql_fetch_row($rs1)) {
						$valore=$row1[0];
						if((strpos($valore, ':'))and((strpos($valore, 'AM'))or (strpos($valore, 'PM')))) $valore=formatta_data($valore);
						//echo("dsfsdfsdfsdfsdf".$variabile_da_sost." - ".$valore);	
						$descrizione=str_replace($variabile_da_sost,$valore,$descrizione);
					}
				}
			}
		}
}


} 
return $descrizione;

}


function carica_testo_autocompilato()
{
$idprotocollo=$_REQUEST['idprotocollo'];
$idcosa=$_REQUEST['idcosafare'];
$idpaziente=$_REQUEST['idpaziente'];
$idimpegnativa=$_REQUEST['idimpegnativa'];
$idcartella=$_REQUEST['idcartella'];
$id_riferimento=$idpaziente.";".$idimpegnativa.";".$idcartella;
$conn = db_connect();


$query="select * from compilatore_contenuti where idcontenuto=$idcosa";
$rs = mssql_query($query, $conn);

if($row = mssql_fetch_assoc($rs))
{   
$descrizione=$row['contenuto'];
$testo_autocompilato=crea_testo_autocompilato($descrizione,$id_riferimento);
}
else
$testo_autocompilato=$descrizione;
?>
<textarea style="width: 400px;bordeR: 1px solid #666;height:200px;"	 name="testo_compilato_result" id="testo_compilato_result"><?=$testo_autocompilato?></textarea>
<?
exit();
}

if(isset($_SESSION['UTENTE'])) {

	switch($_REQUEST['action']) {

			case "1":
				carica_cosa_fare();	
			break;
			case "2":
				carica_testo_autocompilato();	
			break;
			default:
				
				exit();
			break;
	}		
			
	

} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>