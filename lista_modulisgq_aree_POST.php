<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 48;
$tablename = 'utenti';
include_once('include/function_page.php');

function update($id) {
$opeinsEffettuaSalvataggio=$_SESSION['UTENTE']->get_userid();
$conn = db_connect();
$idarea=$id;
/*
$query = "delete from aree_moduli where idarea=$idarea";
$result = mssql_query($query, $conn);
if(!$result) echo("no;;;");
	$j=0;
*///exit();
$arr_ordine=Array();
if (isset($_POST['debug'])){
	$ordine=$_POST['debug'];
	$arr_ordine=split(" ",$ordine);
}
else{
    while($j<100)
		$arr_ordine[$j]=$j+1;
}
$numerofield=sizeof($arr_ordine)-1;
//echo('numerofield:');
//echo($numerofield);
$k=0;
//echo('prima del while');
while($k<$numerofield){
    $idmodulo=$arr_ordine[$k];
    $pre='previsto'.($idmodulo);	 
    $mod='idmodulo'.($idmodulo);
    //echo($pre);
    //echo($mod);
    if ($_POST[$pre]=="s"){   
        //echo($_POST[$pre]);
        //echo("pkarea".$_POST[$mod]);
        //echo("pkmodulo".$idarea);
        //echo($_POST[$mod]);
        $opeinsEffettuaSalvataggio=$_SESSION['UTENTE']->get_userid();
    
        $query="Select * FROM aree_moduli join area_moduli_scadenza_sgq
                        on (aree_moduli.id = area_moduli_scadenza_sgq.id_area_moduli) AND (aree_moduli.opeins=$opeinsEffettuaSalvataggio)";
        $rs = mssql_query($query, $conn);
        $controlloAreaModuliScadenze=mssql_num_rows($rs);
        
        $query="Select * FROM aree_moduli where idarea=$idarea AND (idmodulo=$_POST[$mod])";
        $rs = mssql_query($query, $conn);
        $controlloAreaModuli=mssql_num_rows($rs);
        //echo('controlloAreaModuli');
        //echo($controlloAreaModuli);
        if($controlloAreaModuli==0){
            //echo('aggiornato solo aree moduli');
            //echo($_POST[$mod]);
            //echo('inserimento');
            $query="insert into aree_moduli (idarea,idmodulo,cancella,previsto,opeins) values ($idarea,$_POST[$mod],'n','s',$opeinsEffettuaSalvataggio)";
            //echo($query);
            $rs = mssql_query($query, $conn);
             //echo($query);
        }
        else if ($controlloAreaModuliScadenze>0){
            //echo('aggiornamento tutto');
            $query="update aree_moduli set cancella='n' where opeins=$opeinsEffettuaSalvataggio AND (idarea=$idarea) AND (idmodulo=$_POST[$mod])";
            $rs = mssql_query($query, $conn);
             //echo($query);
            $query="update scadenze_generate_sgq set cancella='n' where (opeins=$opeinsEffettuaSalvataggio)";
            $rs = mssql_query($query, $conn);
            //echo($query);
            $query="update area_moduli_scadenza_sgq set cancella='n', memorizzazione='salvata' where (opeins=$opeinsEffettuaSalvataggio)";
            $rs = mssql_query($query, $conn); 
            //echo($query);
        }
        $query="update aree_moduli set cancella='n' where opeins=$opeinsEffettuaSalvataggio AND (idarea=$idarea) AND (idmodulo=$_POST[$mod])";
        $rs = mssql_query($query, $conn);
    }elseif($_POST[$pre]=="n"){
     //  $query = "DELETE from aree_moduli where (idarea=$idarea) AND (idmodulo=$_POST[$mod]) AND (opeins=$opeinsEffettuaSalvataggio)";	
	   $query = "DELETE from aree_moduli where (idarea=$idarea) AND (idmodulo=$_POST[$mod])";
        $rs = mssql_query($query, $conn);
        //echo($query);
    }
    $k++;
}//echo('fine while');

echo("ok;;1;lista_modulisgq_aree.php");	
exit();

}
function show_list() {

	exit();

}

if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			
			case "create":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_POST['id']);
			break;

			
		}

		
		switch($do) {
			default:
				show_list();
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

