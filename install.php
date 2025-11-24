<?
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 43;
$tablename = 'normative';
include_once('include/function_page.php');


function edit($do)
{
	$arr_conf=array();
	
	$ricalcola=1;
	if($do=="update"){
		$ricalcola=0;
		$_SESSION['error_handler'] = array();
		$contents="";
		foreach ($_POST as $k => $v) {		
			if ($k!="action"){
				$v=str_replace("\\\\","\\",$v);
				$record="define('".$k."','".$v."');\n";
				array_push($arr_conf,$k.",".$v);
				$contents.=$record;
				$tmp=substr($v, 0, 2);
				if(substr($k, 0, 2)=="IP") $ip_add=$v;
				if(substr($k, 0, 7)=="FTP_USR") $ftp_u=$v;
				if(substr($k, 0, 7)=="FTP_PWD") $ftp_p=$v;
				if($tmp=="e:"){				
					if(!is_dir($v)) {
						array_push($_SESSION['error_handler'],$k);
						$msg= "Directory non valida!<br/>";
						$msg.= "Al parametro $k non è stata assegnata una directory valida";
						$_SESSION['error_handler'][99]=$msg;
					}else{
						//controllo ftp per ftp_remd user
						// apre la connessione ftp					
						$conn_id = ftp_connect($ip_add,21,2);
						if($conn_id){
							// login con user name e password
							$login_result = ftp_login($conn_id, $ftp_u, $ftp_p); 
						
							// controllo della connessione
							if (!$login_result) { 
								$msg= "Login FTP  fallita!<br/>";
								$msg.= "Tentativo di connessione fallito per l'utente $ftp_u su indirizzo $ip_add per la cartella $v"; 
								$_SESSION['error_handler'][99]=$msg;							
								}
						}else{
							$msg= "Connessione FTP fallita!<br/>";
							$msg.= "Tentativo di connessione fallito su indirizzo $ip_add"; 
							$_SESSION['error_handler'][99]=$msg;						
						}
					}
				}
			}
		}	
		if(sizeof($_SESSION['error_handler'])==0){
			$contents="<?php\n\n".$contents."\n\n?>";
			$filename = 'include/reserved/config.inc.php';	
			$handle=fopen($filename,"w"); //apre il file
			fwrite($handle, $contents);
			fclose($handle);		
			Header("Location: index.php");
		}
	}
			
	if($ricalcola){
	$myFile = "include/reserved/config.inc.php";
	$fh = fopen($myFile, 'r');
	$contents = fread($fh, filesize($myFile)); 	
	$contents=str_replace("<?php","",$contents);
	$contents=str_replace("?>","",$contents);
	
	$filename = 'include/reserved/tmp_conf.txt';	
	$handle=fopen($filename,"w"); //apre il file
	fwrite($handle, $contents);
	fclose($handle);	
	$fh = fopen($filename, 'r');
	while(!feof($fh)){		
		$buffer = fgets($fh, 4096);
		$commento=substr($buffer, 0, 2);
		if(($commento!="//")and($commento=="de")){			
			$buffer=str_replace("'","",$buffer);
			$buffer=str_replace("define(","",$buffer);
			$buffer=str_replace(");","",$buffer);		
			array_push($arr_conf,$buffer);
			}
		}
	}

	?>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

<form  method="post" name="operatori" action="<?=$PHP_SELF?>" id="myForm"  >
	<input type="hidden" name="action" value="update" />
	<?
	if(sizeof($_SESSION['error_handler'])>0){
		echo("<br/><strong>ERRORE nei parametri di configurazione. Salvataggio annullato.</strong>");
		echo("<br/>".$_SESSION['error_handler'][99]);
		}
	?>
	<div class="titolo_pag"><h1>Configurazione Iniziale Remed</h1></div>

	<div class="blocco_centralcat">
	<?
	
	foreach ($arr_conf as &$value) {
		$arr_tmp=split(",",$value);	
	?>		
   <div class="riga" style="padding-bottom:10px"> 
	   <span class="rigo_mask">
			<div class="testo_mask"><?=$arr_tmp[0]?></div>
			<?
			switch($arr_tmp[0]){
				case "DELETE_DIFF":
					echo("<em>num giorni entro cui è possibile eliminare una istanza modulo</em>");
				break;
				case "AVVISO_SCADENZA":
					echo("<em>avviso di scadenza in giorni per i moduli in scadenza ciclica e puntale</em>");
				break;
				case "TIPOLOGIA_MEDICO":
					echo("<em>valori ammessi 0 - 1. Se 0 il campo non viene visualizzato e gestito in area impegnative</em>");
				break;
				case "DATA_VIS_ASL":
					echo("<em>valori ammessi 0 - 1. Se 0 il campo non viene visualizzato e gestito in area impegnative</em>");
				break;
				case "ORA_VIS_ASL":
					echo("<em>valori ammessi 0 - 1. Se 0 il campo non viene visualizzato e gestito in area impegnative</em>");
				break;
			}
			if(in_array($arr_tmp[0], $_SESSION['error_handler'])){
				echo("<strong>Directory non valida</strong>");
			}
			?>
			<div class="campo_mask mandatory">
				<input class="scrittura_campo" type="text" id="<?=$arr_tmp[0]?>" name="<?=$arr_tmp[0]?>" value="<?if($arr_tmp[1]!="0")echo($arr_tmp[1]);?>" style="width:400px"/>
			</div>
		</span>
	</div>
	<?}?>
    
   </div>
   
	<div class="titolo_pag">
		<div class="comandi">
			<input id="salva_operatore" type="submit" title="salva" value="salva"  class="button_salva"/>
		</div>
	</div>	
	</form>		
</div>
	<?
}


function update()
{
	global $arr_conf,$arr_handler;
	$_SESSION['error_handler'] = array();
	$contents="";
	foreach ($_POST as $k => $v) {		
		if ($k!="action"){
			$v=str_replace("\\\\","\\",$v);
			$record="define('".$k."','".$v."');\n";
			$contents.=$record;
			$tmp=substr($v, 0, 2);
			if(substr($k, 0, 2)=="IP") $ip_add=$v;
			if(substr($k, 0, 7)=="FTP_USR") $ftp_u=$v;
			if(substr($k, 0, 7)=="FTP_PWD") $ftp_p=$v;
			if($tmp=="e:"){				
				if(!is_dir($v)) {
					array_push($_SESSION['error_handler'],$k);
				}else{
					//controllo ftp per ftp_remd user
					// apre la connessione ftp					
					$conn_id = ftp_connect($ip_add,21,10);
					if($conn_id){
						// login con user name e password
						$login_result = ftp_login($conn_id, $ftp_u, $ftp_p); 
					
						// controllo della connessione
						if (!$login_result) { 
							$msg= "Login FTP  fallita!<br/>";
							$msg.= "Tentativo di connessione fallito per l'utente $ftp_u su indirizzo $ip_add per la cartella $v"; 
							$_SESSION['error_handler'][99]=$msg;
							return 0;
							}
					}else{
						$msg= "Connessione FTP fallita!<br/>";
						$msg.= "Tentativo di connessione fallito su indirizzo $ip_add"; 
						$_SESSION['error_handler'][99]=$msg;
						return 0;
					}
				}
			}
		}
	}	
	if(sizeof($_SESSION['error_handler'])==0){
		$contents="<?php\n\n".$contents."\n\n?>";
		$filename = 'include/reserved/config.inc.php';	
		$handle=fopen($filename,"w"); //apre il file
		fwrite($handle, $contents);
		fclose($handle);		
		return 1;
	}
	return 0;
}


	if(!isset($do)) $do='';
	if (empty($_POST)) $_POST['action'] = "";

		
	switch($_POST['action']) {

		case "update":			
			//if(update()) Header("Location: index.php");
			edit($_POST['action']);
			exit();			
		break;		
	}

		
	switch($do) {

		case "edit":			
			edit("edit");
		break;
		
		default:
			edit("edit");
		break;
	}
		html_footer();
	



?>

