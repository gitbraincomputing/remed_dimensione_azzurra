<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 48;
$tablename = 'utenti';
include_once('include/function_page.php');

function show_list()
{

$conn = db_connect();

$query = "SELECT * FROM area order by area asc";	
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

//pagina_new();
?>
	<script>inizializza();</script>	


<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione moduli</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_modulisgq_aree.php');" href="#">elenco moduli</a></div>
          
        </div>

	<div class="titoloalternativo">
            <h1>Associazione Moduli Aree</h1>       
	</div>	
	<div class="titolo_pag">
		<div class="comandi">
			
		</div>
	</div>
<?
	if($conta==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Aree</h1>
	</div>
	<div class="info">Non esistono aree</div>
	
	
	<?
	exit();}?>
	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
  
	<th>area</th> 
    <th>descrizione</th> 
	<td>modifica</td>
    
	
    
</tr> 
</thead> 
<tbody> 

<?

while($row = mssql_fetch_assoc($rs))
{   

		$id=$row['idarea'];
		$area=pulisci_lettura($row['area']);
		$descrizione=pulisci_lettura($row['descrizione']);
	
	
	
		?>
		
		<tr> 
		
		 <td><?=$area?></td> 
		 <td><?=$descrizione?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_modulisgq_aree.php?do=edit&id=<?=$id?>');" href="#"><img src="images/gear.png" /></a></td> 
		</tr> 
		
		
		<?

}	

?>


</tbody> 
</table> 
<? 
footer_paginazione($conta);
?>
</div></div>


<script>
$(document).ready(function() {
	
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>


<script type="text/javascript" id="js">

function change_tab_edit(iddiv)
{
//$('#container-4').triggerTab(3); 
//$('#container-4').tabs('add', 'index.php', 'New Tab', 1);
//$('#container-4').tabs('add', '#new-tab', 'New Tab');
//$('#container-4 > #tab_block').append('<li><a href="re_pazienti_amministrativa.php?do=add&id=10"><span>antonio esposito</span></a></li>');
//$('#container-4').find('#tab_block > li >a')..end().tabs();
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="re_pazienti_amministrativa_edita_impegnativa.php?do=edit&id="+iddiv;
$("#lista_impegnative").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });


}

 </script>

<?
//footer_new();

}
function edit($id){

$idarea=$id;
$conn = db_connect();
$query = "SELECT * from area WHERE idarea=$idarea";	
$rs = mssql_query($query, $conn);

$row = mssql_fetch_assoc($rs);
$id=$row['idarea'];
$area=$row['area'];

$descrizione=$row['descrizione'];

$opeinsAccedeAlleModifiche=$_SESSION['UTENTE']->get_userid();

$query = "DELETE FROM area_moduli_scadenza_sgq WHERE memorizzazione is null AND opeins=$opeinsAccedeAlleModifiche";	
$result = mssql_query($query, $conn);
  
$query = "DELETE FROM area_moduli_scadenza_sgq WHERE cancella='y' AND opeins=$opeinsAccedeAlleModifiche";	
$result = mssql_query($query, $conn);
        
$query = "DELETE FROM aree_moduli WHERE cancella='y' AND opeins=$opeinsAccedeAlleModifiche";	
$result = mssql_query($query, $conn);

?>


<script type="text/javascript">
/*$(document).ready(function() {
    // Initialise the table
    $("#elenco").tableDnD();
});*/

inizializza();
var popupStatus = 0;




//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
		
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popupdati2").height();
	var popupWidth = $("#popupdati2").width();
	//centering
	$("#popupdati2").css({
		"position": "fixed",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}

function prev(id){	
	//centering with css
	loadPopup(id);
	centerPopup();
	//load popup


}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){		
		$("#backgroundPopup").fadeOut("slow");				
		$("#popupdati2").fadeOut("slow");
		$('#thumbnail_form').hide();
		$("#desc_foto").hide();
		$(".imgareaselect-border2").hide();
		$(".imgareaselect-border1").hide();
		$(".imgareaselect-outer").hide();	
		$(".imgareaselect-selection").hide();	
		popupStatus = 0;
	}
}
$(document).ready(function() {

//LOADING POPUP
	//Click the button event!
	$(".preview").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
	});
					
	//CLOSING POPUP
	//Click the x event!
	$(".popupdatiClose").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});


	// Initialise the first table (as before)
	$("#elenco").tableDnD();

		// Initialise the second table specifying a dragClass and an onDrop function that will display an alert
	$("#elenco").tableDnD({
	    onDragClass: "myDragClass",
	    onDrop: function(table, row) {
            var rows = table.tBodies[0].rows;
            var debugStr = "";
            for (var i=0; i<(rows.length-1); i++) 
			{
				if (rows[i].style.display != 'none')
			    debugStr += rows[i].id+" ";
				
            }
			document.getElementById("debug").value=debugStr;
	       
	    },
		onDragStart: function(table, row) {
		//alert(row.id);
			//$(#debugArea).html("Started dragging row "+row.id);
		}
	});
});
function gestione_scadenza_add(idarea,idmodulo,opeins){	
	//centering with css
	loadPopup_add(idarea,idmodulo,opeins);
	centerPopup();
	//load popup


}
//loading popup with jQuery magic!
function loadPopup_add(idarea,idmodulo,opeins){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?&do=add&idarea='+idarea+'&idmodulo='+idmodulo+'&opeins='+opeins, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}
function visualizza_scadenze(idarea,idmodulo,opeins){	
	//centering with css
	
	loadPopupVisualizza(idarea,idmodulo,opeins);
	centerPopup();
	//load popup


}
function loadPopupVisualizza(idarea,idmodulo,opeins){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?do=visualizza_scadenze&idarea='+idarea+'&idmodulo='+idmodulo+'&opeins='+opeins, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}

</script>

<?
$conn = db_connect();
$query = "SELECT idmodulo from re_moduli_aree where idarea=$idarea order by id";
$ordinamento="";
$arr_moduli_id=array();
$rs1 = mssql_query($query, $conn);
if(!$rs1) error_message(mssql_error());
while ($row1 = mssql_fetch_assoc($rs1)) {
    array_push($arr_moduli_id,  $row1['idmodulo']);
}

$query = "SELECT idmodulo from re_moduli_aree_ass order by idmodulo asc";
$rs1 = mssql_query($query, $conn);
if(!$rs1) error_message(mssql_error());
$conta=mssql_num_rows($rs1);
$i=1;
$opeins=$_SESSION['UTENTE']->get_userid();
while ($row1 = mssql_fetch_assoc($rs1)) {
    //echo($row1['idmodulo']);
	if (!(in_array($row1['idmodulo'], $arr_moduli_id))) array_push($arr_moduli_id,  $row1['idmodulo']);
}
//print_r ($arr_moduli_id);
?>

<div id="popupdati2"></div>
<div id="backgroundPopup"></div>
<div class="padding10">
    <div class="logo"><img src="images/re-med-logo.png" /></div>
		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_modulisgq_aree.php');" href="#">elenco aree</a></div>
			<div class="elem_dispari"><a href="#">elenco moduli SGQ aree</a></div>          
        </div>
        <? 
		if($conta==0){
    	?>
	
     	<div class="titoloalternativo">
            <h1>Elenco moduli associati all'area: <?=$area?></h1>
    	</div>
        
    	<div class="info">Non esistono moduli da poter associare all'area: <?=$area?> </div>
	    <?
	    exit();}
        ?>

	<div class="titoloalternativo">
            <h1>Elenco moduli associati all'area: <?=$area?> </h1>       
</div>	
		
<form id="myForm"  method="post" name="form0" action="lista_modulisgq_aree_POST.php">
		<input type="hidden" name="action" value="create" />
		<input type="hidden" name="id" value="<?=$id?>" />
		<div class="nomandatory">
		<input type="hidden" name="nomandatory" />
		</div>

<table style="clear:both;" id="elencoScadenze" class="tablesorter" cellspacing="1" >
	<thead>
	<tr>
		<th>codice SGQ</th>
		<th>modulo SGQ</th>
		<th>previsto</th>
        <td width="12%" align="left">genera scadenza</td>
        <td width="12%" align="left">visualizza scadenze</td>
	</tr>	
    </thead> 
	<tbody id="tbody"> 
<?
foreach ($arr_moduli_id as $value)   
    {
    //$idmodulo=$row1['idmodulo'];
    $idmodulo=$value;
    $query = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc, nome asc";
    //echo($query);	
    $rs = mssql_query($query, $conn);
    if(!$rs) error_message(mssql_error());
        while($row = mssql_fetch_assoc($rs))
            {   
            $nome=pulisci_lettura($row['nome']);
            $codice=pulisci_lettura($row['codice']);
            $disuso=$row['disuso'];	
            if ($i>2)
                {
                $style="style=\"display:none\"";
                $class="riga_sottolineata_diversa hidden";
                $dis="disabled";
                }
            else
                {
                $nodrag="nodrag=false";
                $style='';
                $class="riga_sottolineata_diversa hidden";
                $dis="";
                }
            $previsto="n";
            $query3 = "SELECT * FROM aree_moduli where idmodulo=$idmodulo and idarea=$idarea";
            $rs3 = mssql_query($query3, $conn);
            if(!$rs3) error_message(mssql_error());
                if (mssql_num_rows($rs3)>0)
                    {
                    if($row3 = mssql_fetch_assoc($rs3))
                        {   
                        $pk_aree_moduli=trim($row3['id']);
                        $previsto="s";
                        $opeins=$row3['opeins']=$_SESSION['UTENTE']->get_userid();
                        if($disuso) $nome="<-- IN DISUSO --> ".$nome;
                        }
                    }
                else
                    {
                    $previsto="n";
                    $pk_aree_moduli="";                            
                    }
                //if($trattamenti=="01/01/1900") $trattamenti="";		
                if(($previsto=='s')or(($previsto=='n')and(!$disuso))){
                    ?>
                    <tr id="<?=$i?>" class="<?=$class?>">
						<td align="left"><span class="id_notizia_cat"><input type="hidden" name="idmodulo<?=$i?>" value="<?=$idmodulo?>" /><?=$codice?></span></td>
						<td align="left"><span class="id_notizia_cat"><?=$nome?></span></td>
                    <?
                        
                        /*$query5 ="Select * FROM dbo.sgq_valori
                        WHERE (dbo.sgq_valori.idarea = $idarea) AND (dbo.sgq_valori.id_modulo = $idmodulo)";*/
                        
                        $query5 ="Select * FROM dbo.istanze_testata_sgq
                        WHERE (dbo.istanze_testata_sgq.id_area = $idarea) AND (dbo.istanze_testata_sgq.id_modulo = $idmodulo)";
                        $rs5 = mssql_query($query5, $conn);		
                        if(mssql_num_rows($rs5)>0) {
                            $associato = '1';
                        }else{
                            $associato = '0';
                        }
                        $query6 ="Select * FROM dbo.aree_moduli where idarea=$idarea AND idmodulo=$idmodulo AND previsto='s'";
                        $rs6 = mssql_query($query6, $conn);		
                        if(mssql_num_rows($rs6)>0) {
                            $previsto = 's';
                        }else{
                            $previsto = 'n';
                        }
                        ?>
                        <td align="left">							
                            <select name="previsto<?=$i?>" onclick="<?if($associato) echo("alert('Questa associazione moduloSGQ/area non puo essere eliminata, esistono delle istanze nel modulo SGQ selezionato che fanno riferimento all\'area presa in considerazione');");?>">
                                <option value="s" <?if ($previsto=='s') echo("selected");?> <?if(!$associato){?>onclick="javascript:$('#genera_vis<?=$i?>').slideDown();$('#visualizza_vis<?=$i?>').slideDown();<?}?>">Si</option>
                                <?if(!$associato){?>
                                <option value="n" <?if ($previsto=='n') echo("selected");?> onclick="javascript:$('#genera_vis<?=$i?>').slideUp();$('#visualizza_vis<?=$i?>').slideUp();">No</option>
							    <?}?>  
                            </select>
                        </td>
                        <?
                    
                    }
                        ?>
                        <td>
                           <?php if(($previsto=='s')or($previsto=='')) {?>
						     		<div id="genera_vis<?=$i?>" style="display:block;">
						    		<? }else {?>	
						    		<div id="genera_vis<?=$i?>" style="display:none;"><? } ?>    
                           <a id="<?=$id?>" onclick="javascript:gestione_scadenza_add('<?=$idarea?>','<?=$idmodulo?>','<?=$opeins?>');" href="#"><img src="images/add.png" /></a>
                        </div>
                        </td> 
                        <td width="100">
                        <?php if(($previsto=='s')or($previsto=='')) {?>
						     		<div id="visualizza_vis<?=$i?>" style="display:block;">
						    		<? }else {?>	
						    		<div id="visualizza_vis<?=$i?>" style="display:none;"><? } ?>    
                                    <?/*$queryAreeMod = "SELECT * from aree_moduli where idmodulo=$idmodulo and idarea=$idarea";
                                    $rsAreeMod = mssql_query($queryAreeMod, $conn);//ritorna
                                    if (mssql_num_rows($rsAreeMod)>0){
                                        $row = mssql_fetch_assoc($rs);
                                        $id_area_moduli=$row['id'];
                                        
                                        $query = "SELECT * from area_moduli_scadenza_sgq where id_area_moduli=$id_area_moduli";
                                        $rsScadGen = mssql_query($query, $conn);
                                        $numScadGeg=mssql_num_rows($rsScadGen);
                                    }*/?>
                                        
                          <a id="<?=$id?>" onclick="javascript:visualizza_scadenze('<?=$idarea?>','<?=$idmodulo?>','<?=$opeins?>');" href="#"><?echo($numScadGeg)?><img src="images/view.png" /></a>
                       </div>
                        </td>
					</tr>
						<?
						if ($i==0)
							$ordinamento.=$i;
							else
							$ordinamento.=$i." ";
						$i++;
					}		
				
		}
        
		?>		
		</tbody>
         
    </table>		

        <input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">	
        <div class="titolo_pag">
            <div class="comandi">
           
            <input   type="submit" title="salva" value="salva" class="button_salva"/ >
            <? 
            footer_paginazione($conta);
            ?>
            </div>
        </div>

    </form>
     
            
            
    </div></div>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {			
                    $.mask.addPlaceholder('~',"[+-]");	
                    $(".campo_data").mask("99/99/9999");				
            
        });	
    </script>
    
    
    
    
    
    

<script>
$(document).ready(function() {
	
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>


<script type="text/javascript" id="js">

function change_tab_edit(iddiv)
{
//$('#container-4').triggerTab(3); 
//$('#container-4').tabs('add', 'index.php', 'New Tab', 1);
//$('#container-4').tabs('add', '#new-tab', 'New Tab');
//$('#container-4 > #tab_block').append('<li><a href="re_pazienti_amministrativa.php?do=add&id=10"><span>antonio esposito</span></a></li>');
//$('#container-4').find('#tab_block > li >a')..end().tabs();
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="re_pazienti_amministrativa_edita_impegnativa.php?do=edit&id="+iddiv;
$("#lista_impegnative").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });


}

 </script>

<?
//footer_new();

}




if(isset($_SESSION['UTENTE'])) {

	
	if(!isset($do)) $do='';
	$back = "main.php";

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
		}

		
		switch($do) {

			case "edit":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit($_REQUEST['id']);
			break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

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

