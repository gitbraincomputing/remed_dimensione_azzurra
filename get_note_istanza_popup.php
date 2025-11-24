<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
include_once('include/function_page.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=VERSIONE?> / gestionale aziende sanitarie</title>
<link rel="Shortcut Icon" href="favicon.ico">
<?
srand(time());
$random = rand();
?>
<!--<script language="javascript" type="text/javascript" src="script/niceforms.js"></script>-->
<script type="text/javascript" src="include/code2.js?random=<?=$random?>"></script>


<script type="text/javascript" src="script/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="script/ddaccordion.js"></script>
<script type="text/javascript" src="script/jquery.tablednd_0_5.js"></script>
<script type="text/javascript" src="script/jquery.validation.js"></script>
<!--<script type="text/javascript" src="script/jquery.dropshadow.js"></script>-->

<script type="text/javascript" src="script/jquery.corners.min.js"></script>
<script type="text/javascript" src="script/jquery.flexbox.min.js"></script>
<script type="text/javascript" src="script/jquery.tabs.min.js"></script>

<script type="text/javascript" src="script/flexigrid.js"></script>
<script type="text/javascript" src="script/jquery.maskedinput-1.1.4.js"></script>
<script type="text/javascript" src="script/jquery.form.js"></script> 
<script type="text/javascript" src="script/jquery_autocomplete.js"></script> 

<script type="text/javascript" src="script/jquery.tablesorter/tablesorter/jquery.tablesorter.js"></script>
<script type="text/javascript" src="script/jquery.tablesorter/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>

<script type="text/javascript" src="script/flexigrid.js"></script>
<script type="text/javascript" src="script/jquery.imgareaselect.min.js"></script>
<script type="text/javascript" src="script/jquery.ocupload-packed.js"></script>



<script type="text/javascript" src="script/general.js?business=<?=$buiseness_require?>&random=<?=$random?>"></script>

 
<?
$validation=true;
 if ($validation==true)
{
?>
<script src="M_Validation/M_global.js?version=4" type="text/javascript">/**/</script>
<?
}
?>
<link href="style/flexigrid.css" rel="stylesheet" type="text/css" />
<link href="style/new_ui.css?random=<?=$random?>" rel="stylesheet" type="text/css" />
<link href="style/new_ui_print.css?random=<?=$random?>" rel="stylesheet" type="text/css" media="print" />


</head>

<body onResize="javascript:ridimensiona();" onload="javascript:visualizzamenu();$('#valore_id').focus();">
<!-- loading div tab -->
<div id="layer_nero2" class="layer_nero_background">
	<div id="loading_big_loading"><img src="images/ajax-loader.gif" alt="attendere prego, salvataggio informazioni in corso" /></div>
</div>
<!-- loading div -->

<div id="layer_nero" class="layer_nero_background">
	<div id="loading_big"><img src="images/ajax-loader.gif" alt="attendere prego, salvataggio informazioni in corso" /></div>
    <div id="loading_big_ok">&nbsp;</div>
    <div id="loading_big_no">&nbsp;</div>
</div>
<!-- loading div -->

<div id="wrap-page">
<div >
<?
if(isset($_SESSION['UTENTE'])) {

	// verifica i permessi	
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) 
	{
		$conn = db_connect();
		$query_note_all="SELECT note FROM istanze_testata WHERE id_cartella = " . $_REQUEST['idcartella'] . " AND id_modulo_versione = " . $_REQUEST['idmoduloversione'] . " AND id_inserimento = " . $_REQUEST['idinserimento'];
		$rs_note_all = mssql_query($query_note_all, $conn);
		$row_note_all = mssql_fetch_assoc($rs_note_all);
	?>

	<div id="menu" style="display:none;"></div>

		
	<div class="titolo_pag">
		<h1>
		<? if(trim($row_note_all['note']) == NULL)
			 echo "Inserisci note istanza modulo";
		else echo "Sostituisci note istanza modulo"; ?>
		</h1>
	</div>


	<div class="blocco_centralcat">
		<div class="form">	
			<form name="form0" action="re_pazienti_sanitaria_POST.php" method="post" id="myForm" enctype="multipart/form-data">
			
				<input type="hidden" value="<?=$_REQUEST['idcartella']?>" name="idcartella" id="idcartella">
				<input type="hidden" value="<?=$_REQUEST['idpaziente']?>" name="idpaziente" id="idpaziente">
				<input type="hidden" value="<?=$_REQUEST['idmoduloversione']?>" name="idmoduloversione" id="idmoduloversione">
				<input type="hidden" value="<?=$_REQUEST['idinserimento']?>" name="idinserimento" id="idinserimento">
				<input type="hidden" value="<?=$_REQUEST['imp']?>" name="imp" id="imp">
				<input type="hidden" value="add_note_istanza" name="action"/>
		
				<div id="div_file" class="generica" style="margin-top:2px; margin-left:5px">
			<? if(isset($_REQUEST['w'])) { ?>
					<textarea name="note" id="note" rows="3" cols="26"><? if($row_note_all['note'] !== NULL && trim($row_note_all['note']) !== "") echo trim($row_note_all['note']); ?></textarea>
				</div>
				<div class="titolo_pag" style="margin-top:2px">
					<div class="comandi">		
							<div class="aggiungi aggiungi_left" style="background: transparent url(images/saveicon.png) 5px 3px no-repeat !important"><a href="#" onclick="$('#myForm').submit();">Salva note</a></div>
					</div>
				</div>
			<? } elseif(isset($_REQUEST['r'])) {
				if($row_note_all['note'] !== NULL && trim($row_note_all['note']) !== "") 
					echo trim($row_note_all['note']); 
				else echo 'Non ci sono note inserite.'; 
				} ?>
			</form>
		</div>
	</div>
<?}
mssql_free_result($rs_note_all);
}
?>
