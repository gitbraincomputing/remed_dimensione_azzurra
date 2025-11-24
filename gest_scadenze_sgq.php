<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 1;
include_once('include/function_page.php');
session_start();
//$idutente=$id;

function add_scadenza(){
?>
<script>
inizializza();
var popupStatus = 0;

function controlloNonCancellare()
{
$.ajax({
    type: "POST",
    url: "ajaxControlloNonCancellare.php",
    data: "",
    success: function(msg){
    //alert( "Data Saved: " + msg );
    //loadPopupVisualizza(idarea,idmodulo,pk_aree_moduli,opeins);
    //   
    }
});
}

function ricarica_mod_da_add(query){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?'+query,function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		
		
		popupStatus = 1;
	}
}

function ricarica(query)
{
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="lista_modulisgq_aree.php?"+query;
$("#wrap-content").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });
}

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
//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
		popupStatus = 0;
	}
}

//disabling popup with jQuery magic!
function disablePopup_force(){
	//disables popup only if it is enabled
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
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
	disablePopup();
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
//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
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

});
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				});
 
function seleziona_gms(tipo,valore,parametro) {

var sel=false;
var vartipo="";
if(valore=='seleziona')
sel=true;

if(tipo=='1')
vartipo='checkm';
else if (tipo=='2')
vartipo='checkg';
else if (tipo=='3')
vartipo='checkgs';
xy =1;
while(xy<parametro){
    var idcheck=vartipo+'['+xy+']';
    document.getElementById(idcheck).checked=sel;
    xy++;
}
}


</script>

<?
$conn = db_connect();
$idarea=$_REQUEST['idarea'];
$idmodulo=$_REQUEST['idmodulo'];
$opeins=$_REQUEST['opeins'];
$query = "SELECT * FROM aree_moduli WHERE ((idarea=$idarea)And (idmodulo=$idmodulo))";
$result = mssql_query($query, $conn);
//echo(mssql_num_rows($result));
if ((mssql_num_rows($result)==0)){
    $query = "INSERT INTO aree_moduli (idarea,idmodulo,opeins,cancella,previsto) VALUES($idarea,$idmodulo,$opeins,'n','s')";		  
    //echo($query);
    $result1 = mssql_query($query, $conn);
    $query = "SELECT * FROM aree_moduli WHERE ((idarea=$idarea)And (idmodulo=$idmodulo))";
    $result1 = mssql_query($query, $conn);
    $row=mssql_fetch_assoc($result1);
    $pk_aree_moduli=$row['id'];
    //echo('pk_aree_moduli');
    //echo($pk_aree_moduli);
}else{
    $query = "SELECT * FROM aree_moduli WHERE ((idarea=$idarea)And (idmodulo=$idmodulo))";
    $result = mssql_query($query, $conn); 
    $row=mssql_fetch_assoc($result);
    $pk_aree_moduli=$row['id'];
}
$query = "INSERT INTO area_moduli_scadenza_sgq (id_area_moduli,cancella,opeins) VALUES($pk_aree_moduli,'n',$opeins)";		  
$result1 = mssql_query($query,$conn);
$query = "SELECT MAX(id) as id FROM area_moduli_scadenza_sgq WHERE (opeins='$opeins')";
//echo($query);
$result = mssql_query($query, $conn);
$row=mssql_fetch_assoc($result);
$pk_aree_moduli_scadenza=$row['id'];
//echo('pk_aree_moduli_scadenza');
//echo($pk_aree_moduli_scadenza);
//echo('pk_aree_moduli');
//echo($pk_aree_moduli);
$idmoduloPerNome=$_REQUEST['idmodulo'];
$queryNomeModulo = "SELECT top 1 * from moduli where idmodulo=$idmoduloPerNome order by id desc, nome asc";
$resultNomeModulo = mssql_query($queryNomeModulo, $conn);
while($row = mssql_fetch_assoc($resultNomeModulo)){  
    $nomeModulo=pulisci_lettura(trim($row['nome']));
}
//$query_ric_mod="do=visualizza_scadenze&idarea=".$idarea."&idmodulo=".$idmodulo."&opeins=".$opeins;  
?>
<div id="carica_presenze">
    <div class="titoloalternativo">
        <h1>Genera scadenza</h1>
    </div>
    <div class="titolo_pag">
	    <div class="comandi" style="float:right;">
            <div class="elimina"><a href="#" onclick="javascript:controlloNonCancellare();disablePopup_force();">chiudi scheda</a></div>
	    </div>	
    </div>
    <div id="wrap-content"><div class="padding10">   
    <form  method="post" name="form0" action="gest_scadenze_sgq.php" id="myForm">
    	<input type="hidden" value="create" name="azione"/>
        <input type="hidden" name="pk_aree_moduli" value="<?=$pk_aree_moduli?>" />
        <input type="hidden" name="opeins" value="<?=$opeins?>" />
        <input type="hidden" name="pk_aree_moduli_scadenza" value="<?=$pk_aree_moduli_scadenza?>" />
    	<input type="hidden" value="<?=$_REQUEST['idarea']?>" name="idarea"/>
        <input type="hidden" value="<?=$_REQUEST['idmodulo']?>" name="idmodulo"/>

    	<div class="titolo_pag"><h1>Modulo SGQ: <?echo($nomeModulo)?></h1></div>
    	<div class="blocco_centralcat">
        	<div class="riga">
	            <div class="rigo_mask">
		        	<div class="testo_mask">Figura responsabile</div>
		    	<div class="campo_mask mandatory data_all"></div>
                
            <?
           $arr_medici[]=array();
           $conn = db_connect();
           $query="SELECT nome, uid from re_operatori_attivi ORDER BY nome asc";
           $rs1 = mssql_query($query, $conn);
           $i=0;
           while($row=mssql_fetch_assoc($rs1)){
	           $arr_medici[$i]=$row;
	           $i++;	
            }?>
   
            <div class="riga">
                <div id="medico_vis<?=$i?>" >
                    <div style="width:150px" class="mandatory">
                    <select id="medico" name="medico" >
                    <option value="" selected>Seleziona una figura</option>
                    <?php								
                    for($y=0;$y<sizeof($arr_medici);$y++){									
                        echo("<option value='".$arr_medici[$y]['uid']."'");
                        if ($medico==$arr_medici[$y]['uid']) echo("selected");
                             echo(">".$arr_medici[$y]['nome']."</option>");						
                             $nomeResp=$arr_medici[$y]['nome'];                             
                        }
        ?>								
                     </select>
                     </div>
                </div>
		    </div>
        </div>   
		<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">data inizio</div>
			<div class="campo_mask mandatory data_futura">
				<input type="text" name="data_inizio" value="<?=$data_inizio?>" class="campo_data"/>
			</div>
		</div>
    <?
    ?>
        <div class="rigo_mask">
			<div class="testo_mask">data fine</div>
			<div class="campo_mask mandatory data_futura">
				<input type="text" name="data_fine" value="<?=$data_fine?>" class="campo_data"/>
			</div>
		</div>
		</div>
    <div class="riga">
    <br />
    <br />
        <div class="rigo_mask">
        <div style="width:150px" class="mandatory groups_1_one">
			<div class="testo_mask" >Seleziona i mesi</div>
             <a onclick="seleziona_gms('1','seleziona','13')" class="puntatore"> <img src="images/lock_bottom.png" align="left" /> </a>
             <a onclick="seleziona_gms('1','deseleziona','13')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a><br />
             <?
            $conn = db_connect();
            $query = "SELECT * FROM mesi_sgq";	
            //echo($query);
            $rs = mssql_query($query, $conn);
            //echo($rs);
            $xy=1;
            while($row = mssql_fetch_assoc($rs)){
                $valoreMese=$row['valore'];
                $idMese=$row['id'];
            ?>
            <input type="checkbox" id="checkm[<?=$xy?>]" name="checkm[<?=$xy?>]" value="<?=$idMese?>"><?echo($valoreMese)?></option><br />
            <?
            $xy++;
            }            
            ?>
		</div>
        </div>
        <div class="rigo_mask">
        <div style="width:150px" class="mandatory groups_2_one">
            <div class="testo_mask">Seleziona giorni mesi</div>
             <a onclick="seleziona_gms('2','seleziona','32')" class="puntatore"> <img src="images/lock_bottom.png" align="left" /> </a>
             <a onclick="seleziona_gms('2','deseleziona','32')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a><br />
          <!--  <table>  -->
            <?
            $arrayGiorniMese=Array();
            $x=1;
            $query = "SELECT * FROM giorni_sgq";	
            //echo($query);
            $rs = mssql_query($query, $conn);
            //echo($rs);
            while($row = mssql_fetch_assoc($rs)){
                $valoreGiorni=$row['valore'];
                $idGiorni=$row['id'];
                $arrayGiorniMese[$x][0]=$idGiorni;
                $arrayGiorniMese[$x][1]=$valoreGiorni;
                $x++;
            }
            //print_r(sizeof($arrayGiorniMese));exit();
            ?>
            
            <?
            for($cont=1;$cont<=15;$cont++){
            ?>
              <input type="checkbox" id="checkg[<?=$cont?>]" name="checkg[<?=$cont?>]" value="<?=$arrayGiorniMese[$cont][0]?>" ><?echo($arrayGiorniMese[$cont][1]);?>
              <input type="checkbox" id="checkg[<?=$cont+15?>]" name="checkg[<?=$cont+15?>]" value="<?=$arrayGiorniMese[$cont+15][0]?>"><?echo($arrayGiorniMese[$cont+15][1]);?><br>
            <?
            }   
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="checkg[<?=31?>]" id="checkg[<?=31?>]"value="<?=$arrayGiorniMese[31][0]?>"><?echo($arrayGiorniMese[31][1]);?>
             
        </div>
        </div>
        
        <div class="rigo_mask">
        <div style="width:150px" class="mandatory groups_3_one">
            <div class="testo_mask">Seleziona giorni settimana</div>
             <a onclick="seleziona_gms('3','seleziona','8')" class="puntatore"> <img src="images/lock_bottom.png" align="left" /> </a>
             <a onclick="seleziona_gms('3','deseleziona','8')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a><br />
             <?
            $conn = db_connect();
            $query = "SELECT * FROM giorni_settimana_sgq";	
            //echo($query);
            $rs = mssql_query($query, $conn);
            //echo($rs);
            $xy=1;
            while($row = mssql_fetch_assoc($rs)){
                $valoreGiorniSet=$row['valore'];
                $idGIorniSet=$row['id'];
                $valoreGiorniSetPul=pulisci_lettura($valoreGiorniSet);
            ?>
             <input type="checkbox" name="checkgs[<?=$xy?>]" id="checkgs[<?=$xy?>]"value="<?=$idGIorniSet?>"><?echo($valoreGiorniSetPul)?></option><br />
            <?
            $xy++;
            }         
            ?>
        </div>
        </div>
    </div>
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="genera" class="button_salva"/>
		</div>
    </div>
	
	</form>
</div></div></div></div>
<?
}



function edit_scadenza($pk_area_moduli_scadenza){


?>

<script>
inizializza();
var popupStatus = 0;

function ricarica_mod(query){
    //alert("primo"+popupStatus);
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?'+query,function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		
		
		popupStatus = 1;
	}
}

//loading popup with jQuery magic!
function loadPopup(idarea,idmodulo,pk_aree_moduli,opeins){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?do=add&idarea='+idarea+'&idmodulo='+idmodulo+'&pk_aree_moduli='+pk_aree_moduli+'&opeins='+opeins,function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		
		
		popupStatus = 1;
	}
}

function loadPopupVisualizza(idarea,idmodulo,pk_aree_moduli,opeins){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?do=visualizza_scadenze&idarea='+idarea+'&idmodulo='+idmodulo+'&pk_aree_moduli='+pk_aree_moduli+'&opeins='+opeins, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}


//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
		
		popupStatus = 0;
	}
}

//disabling popup with jQuery magic!
function disablePopup_force(){
    alert("popap disable1");
	//disables popup only if it is enabled
	
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
		alert("popap disable2");
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
	disablePopup();
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

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
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

});
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				});

function seleziona_gms(tipo,valore,parametro) {

var sel=false;
var vartipo="";
if(valore=='seleziona')
sel=true;

if(tipo=='1')
    vartipo='checkm';
else if (tipo=='2')
    vartipo='checkg';
else if (tipo=='3')
    vartipo='checkgs';
xy =1;
while(xy<parametro){
    var idcheck=vartipo+'['+xy+']';
    document.getElementById(idcheck).checked=sel;
    xy++;
}
}
</script>

<?
$conn = db_connect();

    $query_chiudi="SELECT aree_moduli.idarea,aree_moduli.idmodulo,area_moduli_scadenza_sgq.id_area_moduli,area_moduli_scadenza_sgq.opeins,aree_moduli.id AS chiave_area
                  FROM   aree_moduli INNER JOIN area_moduli_scadenza_sgq ON aree_moduli.id = dbo.area_moduli_scadenza_sgq.id_area_moduli
                  WHERE     (dbo.area_moduli_scadenza_sgq.id =$pk_area_moduli_scadenza)";
    $result_chiudi = mssql_query($query_chiudi, $conn);  
    $row=mssql_fetch_assoc($result_chiudi);
    $idarea=$row['idarea'];
    $idmodulo=$row['idmodulo'];
    $pk_aree_moduli=$row['chiave_area'];
    $opeins=$row['opeins'];

    $query_s_giorni = "SELECT area_moduli_scadenza_sgq.responsabile,area_moduli_scadenza_sgq.data_inizio,area_moduli_scadenza_sgq.data_fine,giorni_sgq.id,giorni_sgq.valore 
              FROM area_moduli_scadenza_sgq join area_moduli_s_giorni 
              on (area_moduli_scadenza_sgq.id='$pk_area_moduli_scadenza') AND (area_moduli_s_giorni.id_area_moduli_s='$pk_area_moduli_scadenza')
              join giorni_sgq 
              on  (area_moduli_s_giorni.id_giorni=giorni_sgq.id)";
    //echo($query_s_giorni);
    //exit();
    $result_s_giorni = mssql_query($query_s_giorni, $conn);
    
    $query_s_giorni_set = "SELECT area_moduli_scadenza_sgq.responsabile,area_moduli_scadenza_sgq.data_inizio,area_moduli_scadenza_sgq.data_fine,giorni_settimana_sgq.id,giorni_settimana_sgq.valore 
              FROM area_moduli_scadenza_sgq join area_moduli_s_giorni_set 
              on (area_moduli_scadenza_sgq.id='$pk_area_moduli_scadenza') AND (area_moduli_s_giorni_set.id_area_moduli_s='$pk_area_moduli_scadenza')
              join giorni_settimana_sgq 
              on  (area_moduli_s_giorni_set.id_giorni_set=giorni_settimana_sgq.id)";
    //echo($query_s_giorni);
    //exit();
    $result_s_giorni = mssql_query($query_s_giorni, $conn);
    
    $query_s_mesi = "SELECT area_moduli_scadenza_sgq.responsabile,area_moduli_scadenza_sgq.data_inizio,area_moduli_scadenza_sgq.data_fine,mesi_sgq.id,mesi_sgq.valore  
              FROM area_moduli_scadenza_sgq join area_moduli_s_mesi 
              on (area_moduli_scadenza_sgq.id='$pk_area_moduli_scadenza') AND (area_moduli_s_mesi.id_area_moduli_s='$pk_area_moduli_scadenza')
              join mesi_sgq 
              on  (area_moduli_s_mesi.id_mesi=mesi_sgq.id)";
    //echo($query_s_mesi);
    //exit();
    $result_s_mesi = mssql_query($query_s_mesi, $conn);
    //exit();
    while($row_dati_scadenza_mesi = mssql_fetch_assoc($result_s_mesi)){
                $data_inizio=formatta_data($row_dati_scadenza_mesi['data_inizio']);
                $data_fine=formatta_data($row_dati_scadenza_mesi['data_fine']);
                $medico=$row_dati_scadenza_mesi['responsabile'];
    }          
    
    $queryNomeModulo = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc, nome asc";
    $resultNomeModulo = mssql_query($queryNomeModulo, $conn);
    while($row = mssql_fetch_assoc($resultNomeModulo)){  
        $nomeModulo=pulisci_lettura(trim($row['nome']));
    }
    
    //$query_ric_mod="do=visualizza_scadenze&idarea=".$idarea."&idmodulo=".$idmodulo."&pk_aree_moduli=".$pk_aree_moduli."&opeins=".$opeins;  
    $query_ric_mod="do=visualizza_scadenze&idarea=".$idarea."&idmodulo=".$idmodulo."&opeins=".$opeins;  
    
?>
<div id="carica_presenze">
    <div class="titoloalternativo">
        <h1>Modifica scadenza</h1>
    </div>
    <div class="titolo_pag">
	
	    <div class="comandi" style="float:right;">
		    <div class="elimina"><a href="#" onclick="javascript:ricarica_mod('<?=$query_ric_mod?>');">chiudi scheda</a></div>
	    </div>	
    </div>

    <div id="wrap-content"><div class="padding10">

    <form  method="post" name="form0" action="gest_scadenze_sgq.php" id="myForm">
    	<input type="hidden" value="update" name="azione"/>
        <input type="hidden" name="pk_aree_moduli" value="<?=$pk_aree_moduli?>" />
        <input type="hidden" name="opeins" value="<?=$opeins?>" />
        <input type="hidden" name="pk_aree_moduli_scadenza" value="<?=$pk_area_moduli_scadenza?>" />
        <input type="hidden" name="idarea" value="<?=$idarea?>" />
    	<input type="hidden" name="idmodulo" value="<?=$idmodulo?>" />

    	<div class="titolo_pag"><h1>Modulo SGQ: <?echo($nomeModulo)?></h1></div>
    	<div class="blocco_centralcat">
        	<div class="riga">
	            <div class="rigo_mask">
		        	<div class="testo_mask">Figura responsabile</div>
		    	<div class="campo_mask mandatory data_all"></div>
                
            <?
           $arr_medici[]=array();
           $conn = db_connect();
           $query="SELECT nome, uid from re_operatori_attivi ORDER BY nome asc";
           $rs1 = mssql_query($query, $conn);
           $i=0;
           while($row=mssql_fetch_assoc($rs1)){
	           $arr_medici[$i]=$row;
	           $i++;	
            }?>
            <div class="riga">
                <div id="medico_vis<?=$i?>" >
                    <div style="width:150px" class="mandatory">
                    <select id="medico" name="medico" >
                    <option value="" selected>Seleziona una figura</option>
                    <?php								
                    for($y=0;$y<sizeof($arr_medici);$y++){									
                        echo("<option value='".$arr_medici[$y]['uid']."'");
                        if ($medico==$arr_medici[$y]['uid']) echo("selected");
                             echo(">".$arr_medici[$y]['nome']."</option>");						
                             $nomeResp=$arr_medici[$y]['nome'];                             
                        }
        ?>								
                     </select>
                     </div>
                </div>
		    </div>
        </div>   
		<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">data inizio</div>
			<div class="campo_mask mandatory data_futura"">
				<input type="text" name="data_inizio" value="<?=pulisci_lettura($data_inizio)?>" class="campo_data"/>
			</div>
		</div>
    <?
    //if ($_REQUEST['tipo']=='p'or $_REQUEST['tipo']=='c'){
    ?>
        <div class="rigo_mask">
			<div class="testo_mask">data fine</div>
			<div class="campo_mask mandatory data_futura"">
				<input type="text" name="data_fine" value="<?=pulisci_lettura($data_fine)?>" class="campo_data"/>
			</div>
		</div>
		</div>	
    <div class="riga">
    <br />
    <br />
        <div class="rigo_mask">
        <div style="width:150px" class="mandatory groups_1_one">
			<div class="testo_mask" >Seleziona i mesi</div>
             <a onclick="seleziona_gms('1','seleziona','13')" class="puntatore"> <img src="images/lock_bottom.png" align="left" /> </a>
             <a onclick="seleziona_gms('1','deseleziona','13')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a><br />
             <?
            $conn = db_connect();
            $query = "SELECT * FROM mesi_sgq";	
            //echo($query);
            $rs = mssql_query($query, $conn);
            //echo($rs);
            $xy=1;
            while($row = mssql_fetch_assoc($rs)){
                $valoreMese=$row['valore'];
                $idMese=$row['id'];
                $query_s_mesi1=$query_s_mesi." and  mesi_sgq.id=".$idMese;
                
                $result_s_mesi1 = mssql_query($query_s_mesi1, $conn);
              // echo($query_s_mesi1);
                
                if($row_mesi = mssql_fetch_assoc($result_s_mesi1)){
                
                    $ck="";
                    if($idMese==$row_mesi['id'])
                    $ck="checked";
                    ?>
                     <input type="checkbox"  id="checkm[<?=$xy?>]" name="checkm[<?=$xy?>]" value="<?=$idMese?>" <?=$ck?>  ><?echo($valoreMese)?><br />
                    <?
                }
                else
                {
                ?>
                <input type="checkbox" id="checkm[<?=$xy?>]" name="checkm[<?=$xy?>]" value="<?=$idMese?>"  ><?echo($valoreMese)?><br />
                <?
                }
                $xy++;
            }
            ?>
		</div>
        </div>
        <div class="rigo_mask">
        <div style="width:150px" class="mandatory groups_2_one">
            <div class="testo_mask">Seleziona giorni mesi</div>
             <a onclick="seleziona_gms('2','seleziona','32')" class="puntatore"> <img src="images/lock_bottom.png" align="left" /> </a>
             <a onclick="seleziona_gms('2','deseleziona','32')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a><br />
          <!--  <table>  -->
            <?
            $arrayGiorniMese=Array();
            $x=1;
            $query = "SELECT * FROM giorni_sgq";	
            //echo($query);
            $rs = mssql_query($query, $conn);
            //echo($rs);
            while($row = mssql_fetch_assoc($rs)){
                $valoreGiorni=$row['valore'];
                $idGiorni=$row['id'];
                $arrayGiorniMese[$x][0]=$idGiorni;
                $arrayGiorniMese[$x][1]=$valoreGiorni;
                $x++;
                
            }
            //print_r(sizeof($arrayGiorniMese));exit();
            ?>
            
            <?
            for($cont=1;$cont<=15;$cont++){
                $query_s_giorni1=$query_s_giorni." and  (giorni_sgq.id=".$arrayGiorniMese[$cont][0]." or giorni_sgq.id=".$arrayGiorniMese[$cont+15][0].")";

               
                $result_s_giorni1 = mssql_query($query_s_giorni1, $conn);
                
                if (mssql_num_rows($result_s_giorni1)>0)
                {
                
                $trovati=mssql_num_rows($result_s_giorni1);
                while($row_giorni = mssql_fetch_assoc($result_s_giorni1)){
                
                        if ($trovati==2)
                        {
                             $ck="";
                            if($arrayGiorniMese[$cont][0]==$row_giorni['id'])
                            {
                            
                                $ck="checked";
                            ?>
                            <input type="checkbox" id="checkg[<?=$cont?>]" name="checkg[<?=$cont?>]" value="<?=$arrayGiorniMese[$cont][0]?>" <?=$ck?> ><?echo($arrayGiorniMese[$cont][1]);?>
                            <?
                            }
                            elseif($arrayGiorniMese[$cont+15][0]==$row_giorni['id'])
                            {
                             
                                $ck="checked";
                            ?>
                            <input type="checkbox" id="checkg[<?=$cont+15?>]" name="checkg[<?=$cont+15?>]" value="<?=$arrayGiorniMese[$cont+15][0]?>" <?=$ck?> ><?echo($arrayGiorniMese[$cont+15][1]);?><br>
                            <?
                            }
                        
                        
                        }
                        else
                        {
                            
                             if($arrayGiorniMese[$cont][0]==$row_giorni['id'])
                            {
                                $quale=15;
                                $ck="checked";
                            ?>
                            <input type="checkbox" id="checkg[<?=$cont?>]" name="checkg[<?=$cont?>]" value="<?=$arrayGiorniMese[$cont][0]?>" <?=$ck?> ><?echo($arrayGiorniMese[$cont][1]);?>
                             <input type="checkbox" id="checkg[<?=$cont+15?>]" name="checkg[<?=$cont+15?>]" value="<?=$arrayGiorniMese[$cont+15][0]?>"  ><?echo($arrayGiorniMese[$cont+15][1]);?><br />
                            <?
                            }
                            elseif($arrayGiorniMese[$cont+15][0]==$row_giorni['id'])
                            {
                             $quale=0;
                                $ck="checked";
                            ?>
                            <input type="checkbox" id="checkg[<?=$cont?>]" name="checkg[<?=$cont?>]" value="<?=$arrayGiorniMese[$cont][0]?>"  ><?echo($arrayGiorniMese[$cont][1]);?>
                            <input type="checkbox" id="checkg[<?=$cont+15?>]" name="checkg[<?=$cont+15?>]" value="<?=$arrayGiorniMese[$cont+15][0]?>" <?=$ck?> ><?echo($arrayGiorniMese[$cont+15][1]);?><br />
                            
                            <?
                            }
                        
                        
                        }
              
                    
                }// end while
                }//end if conta trovati
                else
                {
                    ?>
                    <input type="checkbox" id="checkg[<?=$cont?>]" name="checkg[<?=$cont?>]" value="<?=$arrayGiorniMese[$cont][0]?>" ><?echo($arrayGiorniMese[$cont][1]);?>
                    <input type="checkbox" id="checkg[<?=$cont+15?>]" name="checkg[<?=$cont+15?>]" value="<?=$arrayGiorniMese[$cont+15][0]?>" ><?echo($arrayGiorniMese[$cont+15][1]);?><br>
                    <?
                }
            } 
           $query_s_giorni1=$query_s_giorni." and  (giorni_sgq.id=31)";
   
                $result_s_giorni1 = mssql_query($query_s_giorni1, $conn);
                
                if (mssql_num_rows($result_s_giorni1)>0)
                {
           ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="checkg[<?=31?>]" name="checkg[<?=31?>]" <?=$ck?> value="<?=$arrayGiorniMese[31][0]?>"><?echo($arrayGiorniMese[31][1]);?>
                <?
            }else{
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="checkg[<?=31?>]" name="checkg[<?=31?>]" value="<?=$arrayGiorniMese[31][0]?>"><?echo($arrayGiorniMese[31][1]);?>
                <?  
            }
            ?>
        </div>
        </div>
        
        <div class="rigo_mask">
        <div style="width:150px" class="mandatory groups_3_one">
            <div class="testo_mask">Seleziona giorni settimana</div>
             <a onclick="seleziona_gms('3','seleziona','8')" class="puntatore"> <img src="images/lock_bottom.png" align="left" /> </a>
             <a onclick="seleziona_gms('3','deseleziona','8')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a><br />
             <?
            $conn = db_connect();
            $query = "SELECT * FROM giorni_settimana_sgq";	
            //echo($query);
            $rs = mssql_query($query, $conn);
            //echo($rs);
            $xy=1;
            while($row = mssql_fetch_assoc($rs)){
                $valoreGiorniSet=$row['valore'];
                $idGIorniSet=$row['id'];
                $valoreGiorniSetPul=pulisci_lettura($valoreGiorniSet);
                $query_s_giorni_set1=$query_s_giorni_set." and  giorni_settimana_sgq.id=".$idGIorniSet;
                
                $result_s_giorni_set1 = mssql_query($query_s_giorni_set1, $conn);
              // echo($query_s_giorni_set1);
                if($row_giorni_set = mssql_fetch_assoc($result_s_giorni_set1)){
                $ck="";
                if($idGIorniSet==$row_giorni_set['id'])
                    $ck="checked";
                ?>
                <input type="checkbox" id="checkgs[<?=$xy?>]" name="checkgs[<?=$xy?>]" <?=$ck?> value="<?=$idGIorniSet?>"><?echo($valoreGiorniSetPul)?></option><br />
                <?
                }
                else
                {
                    ?>
                    <input type="checkbox" id="checkgs[<?=$xy?>]" name="checkgs[<?=$xy?>]" value="<?=$idGIorniSet?>"><?echo($valoreGiorniSetPul)?></option><br />
                    <?
                }  
            $xy++;
            }
            ?>
        </div>
        </div>
    </div>
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="genera" class="button_salva" />
		</div>
    </div>
	
	</form>
</div></div></div></div>
<?

}

function visualizza_lista_scadenze($pk_area_moduli_scadenza){

?>

<script>
inizializza();
var popupStatus = 0;

function ricarica(query){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?'+query,function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		
		
		popupStatus = 1;
	}
}

function visualizza_lista_scadenze(pk_area_moduli_scadenza){	
	//centering with css
	loadPopupVisualizzaLista(pk_area_moduli_scadenza);
	centerPopup();
	//load popup
}

function loadPopupVisualizzaLista(pk_area_moduli_scadenza){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?do=visualizza_lista_scadenze&pk_area_moduli_scadenza='+pk_area_moduli_scadenza, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}

//loading popup with jQuery magic!
function loadPopup(idarea,idmodulo,pk_aree_moduli,opeins){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?&do=add&idarea='+idarea+'&idmodulo='+idmodulo+'&pk_aree_moduli='+pk_aree_moduli+'&opeins='+opeins,function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		
		
		popupStatus = 1;
	}
}
function loadPopupVisualizza(idarea,idmodulo,pk_aree_moduli,opeins){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?do=visualizza_scadenze&idarea='+idarea+'&idmodulo='+idmodulo+'&pk_aree_moduli='+pk_aree_moduli+'&opeins='+opeins, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}


//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
		
		popupStatus = 0;
	}
}

//disabling popup with jQuery magic!
function disablePopup_force(){
	//disables popup only if it is enabled
	
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
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
	disablePopup();
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

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
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

});
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				});
                
                
                

function loadPopupVisualizza(idarea,idmodulo,pk_aree_moduli,opeins){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?do=visualizza_scadenze&idarea='+idarea+'&idmodulo='+idmodulo+'&pk_aree_moduli='+pk_aree_moduli+'&opeins='+opeins, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}
/*
function reload_tipologie(idarea,idmodulo,pk_aree_moduli){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="gest_scadenze_sgq.php?do=visualizza_scadenze&idarea="+idarea+"&idmodulo="+idmodulo+"&pk_aree_moduli="+pk_aree_moduli;
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
*/

</script>
<?
$conn = db_connect();

$query = "SELECT idarea,idmodulo,id_area_moduli,responsabile,area_moduli_scadenza_sgq.opeins AS opeins_alias FROM area_moduli_scadenza_sgq join aree_moduli on (area_moduli_scadenza_sgq.id_area_moduli=aree_moduli.id) where (area_moduli_scadenza_sgq.id=$pk_area_moduli_scadenza)";
//echo($query);
//order by id asc
$rsValoriRicarica = mssql_query($query, $conn);
while($row = mssql_fetch_assoc($rsValoriRicarica))
{       
		$idarea=$row['idarea'];
        $idmodulo=$row['idmodulo'];
        $pk_aree_moduli=$row['id_area_moduli'];
        $opeins=$row['opeins_alias'];
        $responsabile=$row['responsabile'];
        $queryNomeResponsabile="SELECT nome, uid from re_operatori_attivi where (uid=$responsabile)";
        $rs1 = mssql_query($queryNomeResponsabile, $conn);
        $row1=mssql_fetch_assoc($rs1);
        $nomeResponsabile=$row1['nome'];
}
$query_ric="do=visualizza_scadenze&idarea=".$idarea."&idmodulo=".$idmodulo."&pk_aree_moduli=".$pk_aree_moduli."&opeins=".$opeins;  

$query = "SELECT * FROM scadenze_generate_sgq where id_area_moduli_s=$pk_area_moduli_scadenza order by data_scadenza_generata";	//order by id asc
$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

//pagina_new();
?>
	<script>inizializza();</script>	


<div id="wrap-content">
    <div class="padding10">
        <div class="logo"><img src="images/re-med-logo.png" /></div> 

        <div id="briciola" style="margin-bottom:20px;">
            <div class="elem0"><a href="#">Elenco scadenze operatore</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','gest_scadenze_sgq.php');" href="#">Elenco scadenze</a></div>
          
        </div>

	    <div class="titoloalternativo">
            <h1>Lista scadenze generate </h1>       
	    </div>
        <div class="titolo_pag">
	        <div class="comandi" style="float:right;">
		        <div class="elimina"><a href="#" onclick="javascript:ricarica('<?=$query_ric?>');">chiudi scheda</a></div>
	        </div>	
        </div>
        
        <div id="wrap-content"><div class="padding10">	
	        <div class="titolo_pag">
		        <div class="comandi"></div>
	        </div>
        </div>
        <?
     	if($conta==0){
    	?>
         <div class="titolo_pag"><h1>operatore: <?echo($nomeResponsabile)?></h1></div>
    	 <div class="blocco_centralcat"></div>
        
    	<div class="info">Non esistono scadenze </div>
	    <?
	    exit();}
        ?>
        <div class="titolo_pag"><h1>Figura responsabile: <?echo($nomeResponsabile)?></h1></div>
    	<div class="blocco_centralcat">
	    <table id="table_vis_scadenze" class="tablesorter" cellspacing="1"> 
        <thead> 
        <tr> 
            <th>date scadenza</th> 
            <th>giorno settimana</th> 
        </tr> 
        </thead> 
        <tbody> 

<?

while($row = mssql_fetch_assoc($rs))
{   
		$dataScadenza=$row['data_scadenza_generata'];
        $dataScadenzaPulita=formatta_data($dataScadenza);
		?>
		<tr> 
         <td><?=$dataScadenzaPulita?></td>
         
		<?
        //echo($dataScadenzaPulita);
        $giorni = array('Domenica','Luned&igrave','Marted&igrave;','Mercoled&igrave','Gioved&igrave','Venerd&igrave','Sabato');
        $giorno_inizio = substr($dataScadenzaPulita,0, 2); 
        $mese_inizio = substr($dataScadenzaPulita,3,2);
        $anno_inizio = substr($dataScadenzaPulita,8, 2); 
        //$anno_inizio = substr($dataScadenzaPulita,6, 4); 
        $data_generata=$anno_inizio."-".$mese_inizio."-".$giorno_inizio;
        //echo($data_scad_generata);
        //echo $giorni[date('w',strtotime($dataScadenzaPulita))]; 
		?>
         <td><?echo($giorni[date('w',strtotime($data_generata))]); ?></td>
        </tr> 
		<?

}	

?>
        </tbody> 
        </table> 
<? 
footer_paginazione($conta);
?>
    </div>
</div></div>


<script>
$(document).ready(function() {
	
	  $("table_vis_scadenze").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
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

function visualizza_scadenze(){

?>

<script>
inizializza();
var popupStatus = 0;

function ricarica(query)
{
$("#layer_nero2").toggle();
$('#lista_impegnative').innerHTML="";
pagina_da_caricare="lista_modulisgq_aree.php?"+query;
$("#wrap-content").load(pagina_da_caricare,'', function(){
   loading_page('loading');
   $("#container-4 .tabs-selected").removeClass('tabs-selected');
 });


}
function controlloNonCancellare()
{
$.ajax({
    type: "POST",
    url: "ajaxControlloNonCancellare.php",
    data: "",
    success: function(msg){
    //alert( "Data Saved: " + msg );
    //loadPopupVisualizza(idarea,idmodulo,pk_aree_moduli,opeins);
    //   
    }
});
}

function visualizza_lista_scadenze(pk_area_moduli_scadenza){	
	//centering with css
	loadPopupVisualizzaLista(pk_area_moduli_scadenza);
	centerPopup();
	//load popup


}
function loadPopupVisualizzaLista(pk_area_moduli_scadenza){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"			
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?do=visualizza_lista_scadenze&pk_area_moduli_scadenza='+pk_area_moduli_scadenza, function(){ 
		centerPopup();		
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
				
		popupStatus = 1;
	}
}
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

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
		
		popupStatus = 0;
	}
}

//disabling popup with jQuery magic!
function disablePopup_force(){
	//disables popup only if it is enabled
	
		$("#backgroundPopup").fadeOut("slow");
		$("#popupdati2").fadeOut("slow", function(){$("#popupdati2").html("");});
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
	disablePopup();
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

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
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

});
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");
				});
function conf_cancella(pk_area_moduli_scadenza,idarea,idmodulo,pk_aree_moduli,opeins){
if (confirm('sei sicuro di voler cancellare questa scadenza?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "ajax_scadenze_sgq.php?&pk_area_moduli_scadenza="+pk_area_moduli_scadenza,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
           if($.trim(msg)==""){
				 $.ajax({
					   type: "POST",
					   url: "gest_scadenze_sgq.php?do=del_scadenza&pk_area_moduli_scadenza="+pk_area_moduli_scadenza,
					   data: "",
					   success: function(msg){
						 //alert( "Data Saved: " + msg );
						 loadPopupVisualizza(idarea,idmodulo,pk_aree_moduli,opeins);
                      //   
						}
					 });
			 }else{
				alert("Non si puo cancellare questa scadenza in quanto l'operatore ha inserito delle istanze");
				return false;	
			 }
			 
			}
		 });
	  }
else return false;
}

function gestione_scadenza_mod(pk_area_moduli_scadenza,idarea){	
	//centering with css
	loadPopup_modifica_mod(pk_area_moduli_scadenza,idarea);
	centerPopup();
	//load popup
}
function loadPopup_modifica_mod(pk_area_moduli_scadenza,idarea){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});		
		$("#popupdati2").load('gest_scadenze_sgq.php?&do=edit&pk_area_moduli_scadenza='+pk_area_moduli_scadenza+'&idarea='+idarea,function(){ 
		centerPopup();
		$("#backgroundPopup").fadeIn("slow");
		$("#popupdati2").fadeIn("slow");
		});		
		popupStatus = 1;
	}
}
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

</script>
<?
$conn = db_connect();
$idarea=$_REQUEST['idarea'];
$idmodulo=$_REQUEST['idmodulo'];
$opeins=$_REQUEST['opeins'];
//$query_ric="do=edit&id=".$idarea."&ritorna="."nonCancellare";

//elimina le scadenze non generate inserite nel database
$query = "DELETE FROM area_moduli_scadenza_sgq WHERE memorizzazione is null";	
$result = mssql_query($query, $conn);

$query = "SELECT * FROM aree_moduli WHERE ((idarea=$idarea)And (idmodulo=$idmodulo))";
$result1 = mssql_query($query, $conn);
$row=mssql_fetch_assoc($result1);
$pk_aree_moduli=$row['id'];
//echo('pk_aree_moduli');
//echo($pk_aree_moduli);
$query = "SELECT * FROM area_moduli_scadenza_sgq WHERE (id_area_moduli='$pk_aree_moduli')";
//echo($query);
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);

$idmoduloPerNome=$_REQUEST['idmodulo'];
$queryNomeModulo = "SELECT top 1 * from moduli where idmodulo=$idmoduloPerNome order by id desc, nome asc";
$resultNomeModulo = mssql_query($queryNomeModulo, $conn);
while($row = mssql_fetch_assoc($resultNomeModulo)){  
    $nomeModulo=pulisci_lettura(trim($row['nome']));
}
//pagina_new();
?>
	<script>inizializza();</script>	

<div id="wrap-content1">
    <div class="padding10">
        <div class="logo"><img src="images/re-med-logo.png" /></div> 

        <div id="briciola" style="margin-bottom:20px;">
            <div class="elem0"><a href="#">gestione moduli</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','gest_scadenze_sgq.php');" href="#">elenco moduli</a></div>
        </div>

	    <div class="titoloalternativo">
            <h1>Lista scadenze</h1>       
	    </div>
        
        <div class="titolo_pag">
	        <div class="comandi" style="float:right;">
		       <div class="elimina"><a href="#" onclick="javascript:controlloNonCancellare();disablePopup_force();">chiudi scheda</a></div>
	        </div>	
        </div>
        
        <div id="wrap-content"><div class="padding10">	
	        <div class="titolo_pag">
		        <div class="comandi"></div>
	        </div>
        </div>
     
        <?
     	if($conta==0){
    	?>
        <div class="titolo_pag"><h1>Modulo SGQ: <?echo($nomeModulo)?></h1></div>
    	<div class="blocco_centralcat"></div>
    	<div class="info">Non esistono scadenze</div>
	    <?
	    exit();}
        ?>
        <div class="titolo_pag"><h1>Modulo SGQ: <?echo($nomeModulo)?></h1></div>
    	<div class="blocco_centralcat">
	    <table id="table_scadenze" class="tablesorter" cellspacing="1"> 
       
        <thead> 
        
        <tr>
            <th>figura responsabile</th> 
            <th>data inizio</th>
            <th>data fine</th>
            <th>stato scadenza</th>
            <!--<th>tipo</th>-->
            <!--<td width="5%" align="left">visualizza</td>-->
	        <td width="5%" align="left">visualizza</td>    
            <td width="5%" align="left">modifica</td>
	        <td width="5%" align="left">cancella</td>    
        </tr> 
        </thead> 
        <tbody> 
<?
while($row = mssql_fetch_assoc($rs))
{   

		$idresponsabile=$row['responsabile'];
        $query1="SELECT nome, uid from re_operatori_attivi where uid=$idresponsabile";
        $rs1 = mssql_query($query1, $conn);
        $row1=mssql_fetch_assoc($rs1);
        $nomeResponsabile=$row1['nome'];
		$data_inizio=$row['data_inizio'];
		$data_fine=$row['data_datafine'];
        $data_inizio=formatta_data($row['data_inizio']);
        $data_fine=formatta_data($row['data_fine']);
        $memorizzazione=$row['memorizzazione'];
	    $pk_area_moduli_scadenza=$row['id'];
        $opeins=$row['opeins']
		?>
		<tr> 
         <td><?=$nomeResponsabile?></td>
		 <td><?=$data_inizio?></td> 
		 <td><?=$data_fine?></td> 
         <td><?=$memorizzazione?></td>
        <td> <a onclick="javascript:visualizza_lista_scadenze('<?=$pk_area_moduli_scadenza?>');" href="#"><img src="images/view.png"/></a></td>
        <td><a onclick="gestione_scadenza_mod('<?=$pk_area_moduli_scadenza?>');" href="#"><img src="images/gear.png" /></a></td> 
		 <td><a onclick="conf_cancella('<?=$pk_area_moduli_scadenza?>','<?=$idarea?>','<?=$idmodulo?>','<?=$pk_aree_moduli?>','<?=$opeins?>');" href="#"><img src="images/remove.png" /></a></td>
        </tr> 
<?
}	
?>
        </tbody> 
        </table> 
 </div>       
<? 
footer_paginazione($conta);
?>
    </div>
</div></div>


<script>
$(document).ready(function() {
	
	  $("table_scadenze").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>



<?
exit();
//footer_new();
}

function del_scadenza($pk_area_moduli_scadenza)
{
$conn=db_connect();
$query="DELETE FROM area_moduli_scadenza_sgq where id=$pk_area_moduli_scadenza";
//echo($query);
//exit();
mssql_query($query, $conn);
}

function bisestile($anno_inizio){
if (($anno_inizio%100==0) And ($anno_inizio%4 == 0)){
    if ($anno_inizio%400==0)
        return 1;//bisestile
    else
        return 0;//non bisestile
}elseIf ($anno_inizio%4 == 0)
    return 1;//bisestile
else
    return 0;//non bisestile
}

function creaDataScadenza($opeins,$pk_aree_moduli_scadenza,$data_fine,$mese_inizio,$giorno_inizio,$valoreBisestile,$anno_inizio,$anno_scadenza,$controllo_anno_inizio,$checkm,$checkg,$checkgs){
//L'intervallo di valori con mktime() va' tipicamente dal 13 dicembre 1901 20:45:54 al 19 gennaio 2038 03:14:07 che corrispondono al minimo e massimo valore 
//rappresentabili mediante un numero intero a 32 bit con segno.
$meseForm=intval($mese_inizio,10); 
$giornoForm=intval($giorno_inizio,10);
$conn = db_connect();
foreach ($checkm as $key => $IdMvalue) {
    //mesi 30 
    if((($IdMvalue>=$meseForm) or ($anno_inizio>$controllo_anno_inizio)) and (($IdMvalue==4)or($IdMvalue==6)or($IdMvalue==9)or($IdMvalue==11))){
        foreach ($checkg as $key => $IdGvalue) {
            if($IdGvalue==31){
                $IdGvalue="";
            }
            if($IdGvalue<>""){
                if((($IdGvalue<$giornoForm)and($IdMvalue<=$meseForm))and($anno_inizio==$controllo_anno_inizio)){
                }
                else{
                    foreach ($checkgs as $key => $IdGSvalue){
                        //echo('secondo for each ');
                        $data_scad_generata=$IdGvalue."-".$IdMvalue."-".$anno_inizio;
                        $giornoSetEsatto = date('w',strtotime($data_scad_generata));
                        $data_scadenza_generata = mktime (0,0,0,$IdMvalue,$IdGvalue,$anno_inizio);
                        //echo($IdMvalue."/".$IdGvalue."/".$anno_inizio);
                        //echo("<br/> minore di<br/>");
                        //echo('data_scadenza_generata');
                        //echo($data_scadenza_generata);echo("-".$data_scad_generata);
                        $giorno_fine = substr($data_fine,0, 2); 
                        $mese_fine = substr($data_fine,3,2);
                        $anno_fine = substr($data_fine,8, 4);
                        $giorno_fine_form=intval($giorno_fine,10); 
                        $mese_fine_form=intval($mese_fine,10); 
                        $anno_fine_form=intval($anno_fine,10);                 
                        $data_fine_gen= mktime (0,0,0,$mese_fine_form,$giorno_fine_form,$anno_fine_form);
                        //echo($mese_fine_form."/".$giorno_fine_form."/".$anno_fine_form."<br/>");
                        //echo('data_fine_gen');echo("-".$data_fine);
                        //echo("-------------------".$data_scadenza_generata." minore di ".$data_fine_gen."-------------------");
                        if ($data_scadenza_generata >$data_fine_gen){
                            //echo("data errata");
                           return 0;
                        }
                        if ((($giornoSetEsatto==0) and ($IdGSvalue==7))or($giornoSetEsatto==$IdGSvalue)){
                            $data_db=date("d/m/Y",mktime (0,0,0,$IdMvalue,$IdGvalue,$anno_inizio));
                            $query_data="insert into scadenze_generate_sgq (id_area_moduli_s,data_scadenza_generata,opeins,cancella) values ($pk_aree_moduli_scadenza,'$data_db',$opeins,'n')";
                            //echo($query_data);
                            $rs_data_generata= mssql_query($query_data, $conn);
                        }          
                    }
                }
            }
        } 
    }else if((($IdMvalue>=$meseForm) or ($anno_inizio>$controllo_anno_inizio)) and (($IdMvalue==1)or($IdMvalue==3)or($IdMvalue==5)or($IdMvalue==7)or($IdMvalue==8)or($IdMvalue==10)or($IdMvalue==12))){
        //mesi 31 
        //echo('if mesi di 31 ');
        foreach ($checkg as $key => $IdGvalue) {
            if((($IdGvalue<$giornoForm)and($IdMvalue<=$meseForm))and($anno_inizio==$controllo_anno_inizio)){
                //echo('prima');
            }
            else{
                foreach ($checkgs as $key => $IdGSvalue){
                    //echo('secondo for each ');
                    $data_scad_generata=$IdGvalue."-".$IdMvalue."-".$anno_inizio;
                    $giornoSetEsatto = date('w',strtotime($data_scad_generata));
                    $data_scadenza_generata = mktime (0,0,0,$IdMvalue,$IdGvalue,$anno_inizio);
                    //echo($IdMvalue."/".$IdGvalue."/".$anno_inizio);
                    //echo("<br/> minore di<br/>");
                    
                    //echo('data_scadenza_generata');
                    //echo($data_scadenza_generata);echo("-".$data_scad_generata);
                    $giorno_fine = substr($data_fine,0, 2); 
                    $mese_fine = substr($data_fine,3,2);
                    $anno_fine = substr($data_fine,8, 4);
                    $giorno_fine_form=intval($giorno_fine,10); 
                    $mese_fine_form=intval($mese_fine,10); 
                    $anno_fine_form=intval($anno_fine,10);                 
                    $data_fine_gen= mktime (0,0,0,$mese_fine_form,$giorno_fine_form,$anno_fine_form);
                    //echo($mese_fine_form."/".$giorno_fine_form."/".$anno_fine_form."<br/>");
                    //echo('data_fine_gen');echo("-".$data_fine);
                    //echo("-------------------".$data_scadenza_generata." minore di ".$data_fine_gen."-------------------");
                    if ($data_scadenza_generata >$data_fine_gen){
                         return 0;
                        
                    }
                    if ((($giornoSetEsatto==0) and ($IdGSvalue==7))or($giornoSetEsatto==$IdGSvalue)){
                        $data_db=date("d/m/Y",mktime (0,0,0,$IdMvalue,$IdGvalue,$anno_inizio));
                        $query_data="insert into scadenze_generate_sgq (id_area_moduli_s,data_scadenza_generata,opeins,cancella) values ($pk_aree_moduli_scadenza,'$data_db',$opeins,'n')";
                        //echo($query_data);
                        $rs_data_generata= mssql_query($query_data, $conn);
                    }          
                }
            }
        }
    }else if((($IdMvalue>=$meseForm) or ($anno_inizio>$controllo_anno_inizio)) and (($IdMvalue==2))){
        //mese febbraio
        //echo('if mese febbraio ');
        foreach ($checkg as $key => $IdGvalue) {
            if($valoreBisestile==0){
                //echo('anno non bisestile');
             // anno non bisestile
                if(($IdGvalue==31) or($IdGvalue==30)or($IdGvalue==29))
                    $IdGvalue="";
            }
            else{
                //echo('anno bisestile');
                // anno bisestile
                if(($IdGvalue==31) or($IdGvalue==30) )
                    $IdGvalue="";
            }
            if($IdGvalue<>""){
                if((($IdGvalue<$giornoForm)and($IdMvalue<=$meseForm))and($anno_inizio==$controllo_anno_inizio)){
                    //echo('prima');
                }
                else{
                    foreach ($checkgs as $key => $IdGSvalue){
                        //echo('secondo for each ');
                        $data_scad_generata=$IdGvalue."-".$IdMvalue."-".$anno_inizio;
                        $giornoSetEsatto = date('w',strtotime($data_scad_generata));
                        $data_scadenza_generata = mktime (0,0,0,$IdMvalue,$IdGvalue,$anno_inizio);
                        //echo('data_scadenza_generata');
                        //echo($data_scadenza_generata);echo("-".$data_scad_generata);
                        $giorno_fine = substr($data_fine,0, 2); 
                        $mese_fine = substr($data_fine,3,2);
                        //$anno_fine = substr($data_fine,6, 4);
                        $anno_fine = substr($data_fine,8, 2);
                        $giorno_fine_form=intval($giorno_fine,10); 
                        $mese_fine_form=intval($mese_fine,10); 
                        $anno_fine_form=intval($anno_fine,10);                 
                        $data_fine_gen= mktime (0,0,0,$mese_fine_form,$giorno_fine_form,$anno_fine_form);
                        //echo('data_fine_gen');echo("-".$data_fine);
                        //echo($data_fine_gen);
                        if ($data_scadenza_generata >$data_fine_gen){
                            //echo('prima data scartata');
                            //echo($data_scad_generata);
                            //echo 'date superiori a '.$data_fine.' sono state scartate';
                             return 0;
                        }
                        if ((($giornoSetEsatto==0) and ($IdGSvalue==7))or($giornoSetEsatto==$IdGSvalue)){
                            //$data_scadenza_generata=$IdGvalue."/".$IdMvalue."/".$anno_inizio;
                            //$data_scadenza_generata = mktime (0,0,0,$IdMvalue,$IdGvalue,$anno_inizio);
                            $data_db=date("d/m/Y",mktime (0,0,0,$IdMvalue,$IdGvalue,$anno_inizio));
                            //echo("datatttttttttttttttttttttttttttttttttt:" );
                            //echo($data_db);
                            //exit();
                            //echo($data_scadenza_generata);
                            //echo('sono nel if quindi esiste un giorno set data da inserire:' );
                            //echo($data_scadenza_generata);
                            $query_data="insert into scadenze_generate_sgq (id_area_moduli_s,data_scadenza_generata,opeins,cancella) values ($pk_aree_moduli_scadenza,'$data_db',$opeins,'n')";
                            //echo($query_data);
                            $rs_data_generata= mssql_query($query_data, $conn);
                        }          
                    }
                }
            }
        }
    }
} 
//echo("ok");
//exit();
 return 1;
} // fine funzione


function create_scadenza(){

$conn = db_connect();
$idarea=$_POST['idarea'];
$idmodulo=$_POST['idmodulo'];
$data_inizio=$_POST['data_inizio'];
$data_fine=$_POST['data_fine'];
$responsabile=$_POST['medico'];
$pk_aree_moduli=$_POST['pk_aree_moduli'];
$pk_aree_moduli_scadenza=$_POST['pk_aree_moduli_scadenza'];
$opeins=$_POST['opeins'];

$giorno_inizio = substr($data_inizio,0, 2); 
$mese_inizio = substr($data_inizio,3,2);
$anno_inizio = substr($data_inizio,6, 4); 
$anno_scadenza = substr($data_fine,6, 4); 
$controllo_anno_inizio=$anno_inizio;
//echo($data_fine);
//echo($anno_inizio);
//echo($anno_scadenza);

$checkm=$_POST['checkm'];
foreach ($checkm as $key => $IdMvalue) {
    
    $query_m="insert into area_moduli_s_mesi (id_area_moduli_s,id_mesi) values ($pk_aree_moduli_scadenza,$IdMvalue)";
    //echo($query_m);
    $rs_m = mssql_query($query_m, $conn);
    
  //  echo "Hai selezionato la checkboxMese:$key con valore:$IdMvalue";
} 
$checkg=$_POST['checkg'];
foreach ($checkg as $key => $IdGvalue) {
    $query_g="insert into area_moduli_s_giorni (id_area_moduli_s,id_giorni) values ($pk_aree_moduli_scadenza,$IdGvalue)";
    $rs_g = mssql_query($query_g, $conn);
    
   //echo "Hai selezionato la checkboxGiorni:$key con valore:$IdGvalue";
} 
$checkgs=$_POST['checkgs'];
foreach ($checkgs as $key => $IdGSvalue) {
   $query_gs="insert into area_moduli_s_giorni_set (id_area_moduli_s,id_giorni_set) values ($pk_aree_moduli_scadenza,$IdGSvalue)";
   $rsg_s = mssql_query($query_gs, $conn);
    
 //  echo "Hai selezionato la checkboxGiorniSettimana:$key con valore:$IdGSvalue";
} 
//$data_inizio = date('d/m/Y');
//$data_fine = date('d/m/Y');

 

$query="update area_moduli_scadenza_sgq set responsabile=$responsabile,data_inizio='$data_inizio',data_fine='$data_fine',memorizzazione='salvata' where id=$pk_aree_moduli_scadenza";
 mssql_query($query, $conn);


while ($anno_inizio<=$anno_scadenza){
//echo('nel while' );
    //echo($anno_inizio);
    $valoreBisestile=bisestile($anno_inizio);
    $ckdate=creaDataScadenza($opeins,$pk_aree_moduli_scadenza,$data_fine,$mese_inizio,$giorno_inizio,$valoreBisestile,$anno_inizio,$anno_scadenza,$controllo_anno_inizio,$checkm=$_POST['checkm'],$checkg=$_POST['checkg'],$checkgs=$_POST['checkgs']);
    $anno_inizio++;
    if ($ckdate==0)
    $anno_inizio=$anno_scadenza+100; // esco dal while
}
$parametri=$idarea."#".$idmodulo."#".$opeins;

echo("ok;;8;".$parametri.";");
//echo("ok");	
exit();
}




function update_scadenza(){

$conn = db_connect();
$idarea=$_POST['idarea'];
$idmodulo=$_POST['idmodulo'];
$data_inizio=$_POST['data_inizio'];
$data_fine=$_POST['data_fine'];
$responsabile=$_POST['medico'];
$pk_aree_moduli=$_POST['pk_aree_moduli'];
$pk_aree_moduli_scadenza=$_POST['pk_aree_moduli_scadenza'];
$opeins=$_POST['opeins'];

$giorno_inizio = substr($data_inizio,0, 2); 
$mese_inizio = substr($data_inizio,3,2);
$anno_inizio = substr($data_inizio,6, 4); 
$anno_scadenza = substr($data_fine,6, 4); 
$controllo_anno_inizio=$anno_inizio;
//echo($data_fine);
//echo($anno_inizio);
//echo($anno_scadenza);

$checkm=$_POST['checkm'];
$query_m="DELETE FROM area_moduli_s_mesi WHERE id_area_moduli_s= $pk_aree_moduli_scadenza";
//echo($query_m);
$rs_m = mssql_query($query_m, $conn);
foreach ($checkm as $key => $IdMvalue) {    
    $query_m="insert into area_moduli_s_mesi (id_area_moduli_s,id_mesi) values ($pk_aree_moduli_scadenza,$IdMvalue)";
    //echo($query_m);
    $rs_m = mssql_query($query_m, $conn);
    //echo "Hai selezionato la checkboxMese:$key con valore:$IdMvalue";
}

$checkg=$_POST['checkg'];
$query_g="DELETE FROM area_moduli_s_giorni WHERE id_area_moduli_s= $pk_aree_moduli_scadenza";
//echo($query_g);
$rs_g = mssql_query($query_g, $conn);
foreach ($checkg as $key => $IdGvalue) {
    $query_g="insert into area_moduli_s_giorni (id_area_moduli_s,id_giorni) values ($pk_aree_moduli_scadenza,$IdGvalue)";
    $rs_g = mssql_query($query_g, $conn);
   //echo "Hai selezionato la checkboxGiorni:$key con valore:$IdGvalue";
} 

$checkgs=$_POST['checkgs'];
$query_gs="DELETE FROM area_moduli_s_giorni_set WHERE id_area_moduli_s= $pk_aree_moduli_scadenza";
//echo($query_gs);
$rs_gs = mssql_query($query_gs, $conn);
foreach ($checkgs as $key => $IdGSvalue) {
   $query_gs="insert into area_moduli_s_giorni_set (id_area_moduli_s,id_giorni_set) values ($pk_aree_moduli_scadenza,$IdGSvalue)";
   $rsg_s = mssql_query($query_gs, $conn);
   //echo "Hai selezionato la checkboxGiorniSettimana:$key con valore:$IdGSvalue";
} 
//$data_inizio = date('d/m/Y');
//$data_fine = date('d/m/Y');

 

$query="update area_moduli_scadenza_sgq set responsabile=$responsabile,data_inizio='$data_inizio',data_fine='$data_fine',memorizzazione='salvata' where id=$pk_aree_moduli_scadenza";
 mssql_query($query, $conn);
 
 //elimina le scadenze che non hanno istanze 
$querydel="delete from scadenze_generate_sgq where id IN (SELECT scadenze_generate_sgq.id  
          FROM scadenze_generate_sgq join area_moduli_scadenza_sgq 
          ON id_area_moduli_s= area_moduli_scadenza_sgq.id 
          WHERE (id_area_moduli_s=$pk_aree_moduli_scadenza) and (data_scadenza_generata<='$data_fine') and(data_scadenza_generata>='$data_inizio') and (id_istanza_testata_sgq IS NULL))";
//echo($query);
$rdel = mssql_query($querydel, $conn);

while ($anno_inizio<=$anno_scadenza){
//echo('nel while' );
    $valoreBisestile=bisestile($anno_inizio);
     $ckdate=creaDataScadenza($opeins,$pk_aree_moduli_scadenza,$data_fine,$mese_inizio,$giorno_inizio,$valoreBisestile,$anno_inizio,$anno_scadenza,$controllo_anno_inizio,$checkm=$_POST['checkm'],$checkg=$_POST['checkg'],$checkgs=$_POST['checkgs']);
    $anno_inizio++;
    if ($ckdate==0)
    $anno_inizio=$anno_scadenza+100; // esco dal while
}

$parametri=$idarea."#".$idmodulo."#".$opeins;
echo("ok;;8;".$parametri.";");	
exit();
}


if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	

		if (empty($_POST)) $_POST['azione'] = "";

		
		switch($_POST['azione']) {

			
			case "create":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else  create_scadenza();
					
			break;
			
			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update_scadenza();
			break;
            case "del_scadenza":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else del_scadenza($_REQUEST['pk_area_moduli_scadenza']);
			break; 

			
		}

		switch($do) {
            case "del_scadenza":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else del_scadenza($_REQUEST['pk_area_moduli_scadenza']);
			break; 

			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add_scadenza();
			break;
            case "visualizza_lista_scadenze":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_lista_scadenze($_REQUEST['pk_area_moduli_scadenza']);
			break;
			case "edit":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit_scadenza($_REQUEST['pk_area_moduli_scadenza']);
			break;
            case "visualizza_scadenze":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_scadenze();
			break;
			
			
			case "logout":
			logout();
			break;

			default:
				//if($_REQUEST['type']=='res')
					//add_scadenza($_REQUEST['id']);
				//else
				//	show_list_tut($_REQUEST['id']);
			break;
		}
			//html_footer();

} else {
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>