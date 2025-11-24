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
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
	function small_window(myurl) {
		var ScreenWidth=window.screen.width;  
		var ScreenHeight=window.screen.height;  
		var newWindow;
		var w=280;
		var h=150;
		var placementx=(ScreenWidth/2)-((w)/2);  
		var placementy=(ScreenHeight/2)-((h+50)/2); 
		var props = 'scrollBars=no,resizable=yes,toolbar=no,menubar=no,location=no,directories=no,width='+w+',height='+h+',left='+placementx+',top='+placementy+',screenX='+placementx+',screenY='+placementy;
		newWindow = window.open(myurl, "Add_from_Src_to_Dest", props);
	}
	function addToParentList(cont,nome_medico,id_medico,tipo) {				
		if(tipo=='mod'){
			$("#medico"+cont).val(nome_medico);
			$("#medico_id_"+cont).val(id_medico);
		}else{
			$("#medico_test"+cont).val(nome_medico);
			$("#medico_test_id_"+cont).val(id_medico);		
		}
}
</script>
 
 
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



<!--[if IE]>
<link href="style/new_ui-ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lte IE 7]>
<link href="style/new_ui-ie-6.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--
PNGGG!
-->


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
<div id="wrap-all">
<?
if(isset($_SESSION['UTENTE'])) {

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {
	
	$idmodulo="";
	$idmodulo=$_REQUEST['idmodulo'];
	$medico=$_REQUEST['parent'];
	$conn = db_connect();
	$arr_medici[]=array();
	if ($idmodulo!="")
		$query="SELECT nome, uid from re_operatori_gruppi_moduli WHERE idmodulo=$idmodulo ORDER BY nome ASC";
		else
		$query="SELECT nome, uid from operatori ORDER BY nome ASC";
	//echo($query);	
	$rs1 = mssql_query($query, $conn);
	$i=0;
	while($row=mssql_fetch_assoc($rs1)){
		$arr_medici[$i]=$row;
		$i++;	
	}
	?>
	<script language="JavaScript">
	function addSelectedItemsToParent() {
	//self.opener.addToParentList(window.document.forms[0].destList,window.document.forms[0].destTxt);
	self.opener.addToParentList(document.getElementById('sorgente').value,document.getElementById('valore_id').options[document.getElementById('valore_id').options.selectedIndex].innerHTML,document.getElementById('valore_id').value,document.getElementById('tipo').value);
	window.close();
	}
	</script>
	<div id="menu" style="display:none;"></div>
	<div class="titolo_pag" style="width:300px">
		<div class="comandi">
			<h1>Seleziona la figura responsabile desiderata</h1>
		</div>		
	</div>
	<form method="POST">
	<div class="blocco_centralcat" style="width:300px">
	<input type="hidden" value="<?=$_REQUEST['dest']?>" name="sorgente" id="sorgente">
	<input type="hidden" value="<?=$_REQUEST['tipo']?>" name="tipo" id="tipo">
	
	<!--medico <input type="text" value="" name="valore" id="valore">-->
	<!--id medico <input type="text" value="" name="valore_id" id="valore_id">-->
	<div class="riga" style="width:300px">
		<div class="rigo_mask" style="width:300px">
		<div class="testo_mask" style="width:300px">Operatore responsabile</div>
		<div class="campo_mask" style="width:300px">
		<select name="valore_id" id="valore_id" style="width:200px" onChange="javascript:addSelectedItemsToParent()">
			<option value="" selected>Selezionare un operatore</option>									
			<?php								
			for($y=0;$y<sizeof($arr_medici);$y++){									
				echo("<option value='".$arr_medici[$y]['uid']."'");
				if ($medico==$arr_medici[$y]['uid']) echo("selected");
				echo(">".$arr_medici[$y]['nome']."</option>");																																	
			}
			?>								
		</select>
		</div>
		</div>
	</div>
		</div>
	</form>
<?}
}
?>
