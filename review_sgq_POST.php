<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
$id_permesso = $id_menu = 47;


function create_modulo()
{

	$conn = db_connect();
	$idarea=$_REQUEST['idarea'];
	$idmoduloversione=$_REQUEST['idmoduloversione'];
	$id_data_scadenza=$_REQUEST['idscadenza'];
	$id_impegnativa=0;	

	$conn = db_connect();
	$query="select * from max_vers_moduli_sgq where idmoduloversione_new=$idmoduloversione";
    //echo($query);
	//exit();
	$rs = mssql_query($query, $conn);
		
	if(!$rs)
	{		
        echo("no");
        exit();
        die();
	}

	if($row = mssql_fetch_assoc($rs))
	{	
		$idmoduloversione_new=$row['idmoduloversione_new'];
		$idmodulo=$row['idmodulo'];
	}
	else
	{
	    echo("no");
    	exit();
	    die();
	}
	
	mssql_free_result($rs);	
	
	$conn = db_connect();
    
	//$query="select max (idinserimento) as maxins from sgq_valori where idarea=$idarea and idmodulo=$idmodulo";
	
    $query="select max (id_inserimento) as maxins from istanze_testata_sgq where id_area=$idarea and id_modulo=$idmodulo";
	

	$rs = mssql_query($query, $conn);
		
	if(!$rs)
	{		
        echo("no");
        exit();
        die();
	}

	if($row = mssql_fetch_assoc($rs))
		$maxins=$row['maxins']+1;
	else
		$maxins=1;
	
	mssql_free_result($rs);	
			
	$query="select * from campi where idmoduloversione=$idmoduloversione_new order by peso asc";	

	$rs1 = mssql_query($query, $conn);
	if(!$rs1) {
		echo("no");
		exit();}
	
	$datains = date('d/m/Y');
	$orains = date('H:i');
	$ipins = getenv('REMOTE_ADDR');
	$opeins = $_SESSION['UTENTE']->get_userid();
    
    if($id_data_scadenza==""){
        $query1="insert into istanze_testata_sgq (id_inserimento,id_area,id_modulo,datains,orains,opeins,ipins,scadenza_associata) values ($maxins,$idarea,$idmodulo,'$datains','$orains',$opeins,'$ipins','')";
        $rs2 = mssql_query($query1, $conn);
    }else{
        $query = "SELECT * FROM scadenze_generate_sgq WHERE (id=$id_data_scadenza)";
        $result1 = mssql_query($query, $conn);
        $row=mssql_fetch_assoc($result1);
        $dataScadenza=formatta_data($row['data_scadenza_generata']);
        
        $query1="insert into istanze_testata_sgq (id_inserimento,id_area,id_modulo,datains,orains,opeins,ipins,scadenza_associata) values ($maxins,$idarea,$idmodulo,'$datains','$orains',$opeins,'$ipins','$dataScadenza')";
        $rs2 = mssql_query($query1, $conn);
        
        $query2="select max (id_istanza_testata_sgq) as max_id_ist from istanze_testata_sgq where id_inserimento=$maxins and opeins=$opeins";
        $rs3 = mssql_query($query2, $conn);
        if($row3 = mssql_fetch_assoc($rs3)){
            $max_id_ist=$row3['max_id_ist'];
        }else{
            echo("no");
            exit();
            die();
        }
        
        $query2="update scadenze_generate_sgq set id_istanza_testata_sgq=$max_id_ist where id='$id_data_scadenza'";
        $rs3 = mssql_query($query2, $conn);
    
    }
    
    
	while($row1 = mssql_fetch_assoc($rs1))
	{
        $idcampo= $row1['idcampo'];
        if (isset($_POST[$idcampo]))
        {
            $valore=$_POST[$idcampo];				
            if (is_array($valore))
            {
                $val="";
                foreach ($valore as $key => $value) {
                    //echo "Hai selezionato la checkbox: $key con valore: $value<br />";
                    $val.=$value.";";
                }					
                $valore=substr($val,0,strlen($val)-1);
            }
            $valore=pulisci($valore);
            $query2="select max (id_istanza_testata_sgq) as max_id_ist from istanze_testata_sgq where id_inserimento=$maxins and opeins=$opeins";
            $rs3 = mssql_query($query2, $conn);
            if($row3 = mssql_fetch_assoc($rs3)){
                $max_id_ist=$row3['max_id_ist'];
            }else{
                echo("no");
                exit();
                die();
            }
            $query3="insert into istanze_dettaglio_sgq (id_istanza_testata_sgq,id_campo,valore) values ($max_id_ist,$idcampo,'$valore')";
            $rs4 = mssql_query($query3, $conn);
            if(!$rs2)
            {
                echo("no");
                exit();
                die();
            }
        }
		elseif(isset($_FILES[$idcampo])){
				$data=date(dmyHis);
				$nome_file=$_FILES[$idcampo]['name'];
				$nome_file=str_replace(" ","_",$nome_file);
				$type = $_FILES[$idcampo]['type'];
				$file = $id."_".$data."_".$nome_file;
				$valore=$file;
							
				//******************				
				if ($nome_file!="") {
					$upload_dir = ALLEGATI_UTENTI;		// The directory for the images to be saved in
					$upload_path = $upload_dir."/";				// The path to where the image will be saved			
					$large_image_location = $upload_path.$file;
					$userfile_tmp = $_FILES[$idcampo]['tmp_name'];
					//echo($large_image_location);			
					move_uploaded_file($userfile_tmp, $large_image_location);
				}
				//******************				
				
				/*inserimento dell'istanza in istanza_dettaglio*/
			$query="insert into istanze_dettaglio_sgq (id_istanza_testata_sgq,id_campo,valore) values ($max_id_ist,$idcampo,'$valore')";
				$rs2 = mssql_query($query, $conn);
				/*fine inserimento*/
				
				if(!$rs2){
					echo("no3");
					exit();
					die();
				}
			}
    }
    mssql_free_result($rs1);
    echo("ok;;1;review_sgq.php");	
    exit();
}

function configura_review_sgq(){

$utente=$_SESSION['UTENTE']->get_userid();

//echo("qui");
$conn = db_connect();
$giorni_successivi = pulisci($_POST['giorni_successivi']);
$giorni_scadenze = pulisci($_POST['giorni_scadenze']);
$giorni_alert = pulisci($_POST['giorni_alert']);
$giorni=pulisci($_POST['giorni']);
$giorni_s=pulisci($_POST['giorni_s']);
$giorni_a=pulisci($_POST['giorni_a']);
$uid=$_SESSION['UTENTE']->get_userid();

if($giorni=="")$giorni=0;
if($giorni_s=="")$giorni_s=0;
if($giorni_a=="")$giorni_a=0;

if(($giorni<0)or($giorni__s<0)or($giorni_alert<0)){
    echo("no");
	exit();
	die();
}
if(($giorni_successivi=="")or($giorni_scadenze=="")or($giorni_alert=="")){
    echo("no");
	exit();
	die();
}

else{
    $query = "SELECT * FROM review_sgq WHERE id_utente=$uid";
    $result = mssql_query($query, $conn);
	$record_trovati=mssql_num_rows($result);
	//echo("query1");
	//echo($query);
	//exit();	
    if(!($record_trovati>0))
    {
		$query = "INSERT INTO review_sgq (id_utente,giorni_successivi, giorni_scadenze, giorni_alert, valore_giorni_successivi, valore_giorni_scadenze, valore_giorni_alert) VALUES($uid,'$giorni_successivi', '$giorni_scadenze', '$giorni_alert', $giorni, $giorni_s, $giorni_a)";
        $result = mssql_query($query, $conn);
		//echo("queryInsert");
		//echo($query);
		//exit();
        if(!$result)
       {
	    	echo("no");
		    exit();
		    die();
    	}
	}else{
	    $query="UPDATE review_sgq SET giorni_successivi='$giorni_successivi', giorni_scadenze='$giorni_scadenze', giorni_alert='$giorni_alert', valore_giorni_successivi=$giorni, valore_giorni_scadenze=$giorni_s, valore_giorni_alert=$giorni_a WHERE (id_utente=$uid)";
        $result = mssql_query($query, $conn);
		//echo("queryUpdate");
		//echo($query);
		//exit();
		if(!$result)
       {
	    	echo("no");
		    exit();
		    die();
    	}
		
	}//echo("usito");exit();
}
echo("ok;;1;review_sgq.php");	
exit();

}


if(isset($_SESSION['UTENTE'])) {
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		switch($_POST['action']) {

			case "configura_review_sgq":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else configura_review_sgq();
			break;
			
			case "create_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_modulo();
			break;
		}

			html_footer();
	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>

