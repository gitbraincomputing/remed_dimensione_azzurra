<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
$id_permesso = $id_menu = 45;
session_start();
 
/************************************************************************
* funzione update()         						*
*************************************************************************/

function show_list()
{

$conn = db_connect();

$query = "SELECT * FROM area order by area asc";	
$rs = mssql_query($query, $conn);

if(!$rs)
{		
	echo("no");
	exit();
	die();
	}

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
            <h1>Elenco aree</h1>       
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
	<td>esplora</td>
    
	
    
</tr> 
</thead> 
<tbody> 

<?

while($row = mssql_fetch_assoc($rs))
{   

		$id=$row['idarea'];
		$area=pulisci_lettura($row['area']);
		$descrizione=pulisci_lettura($row['descrizione']);
	    $query_area="select * from re_moduli_aree_nome_sgq where idarea=$id order by nome asc";
        $rs_area = mssql_query($query_area, $conn);
        if(!$rs_area)
        {		
				echo("no");
				exit();
				die();
	    }
 
	
	
		?>
		
		<tr> 
		
		 <td><?=$area?></td> 
		 <td><?=$descrizione?></td> 
		 <td><a id="<?=$id?>" <?//if(mssql_num_rows($rs_area)>0) {?>onclick="javascript:load_content('div','re_sgq.php?do=visualizza_moduli&id=<?=$id?>');" href="#"><img src="images/view.png" /><?//}?></a></td> 
		</tr> 
		
		
		<?

}	

?>


</tbody> 
</table> 
<? 
$numero_pager=(int)($conta/20)

?>
<div class="titolo_pag">
		<div class="comandi">
			
         
            <div id="pager" class="pager">
                <form>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/first.png" class="first"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/prev.png" class="prev"/>
                    <input type="text" class="pagedisplay"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/next.png" class="next"/>
                    <img src="script/jquery.tablesorter/tablesorter/addons/pager/icons/last.png" class="last"/>
                    <select class="pagesize">
						
						<? 
						$y=0;
						$x=20;
					if ($conta>$x)
					{
						while ($y<$numero_pager)
						{
						if ($y==0)
						$sel="selected";
						else
						$sel="";
						
						?>
					    <option <?=$sel?> value="<?=$x?>"><?=$x?></option>
                     
						<?
						$y++;
						$x=$x+20;
						}
					}	
						?>
					 <option  value="<?=$conta?>">tutti</option>
                    </select>
                </form>
            </div>
		</div>
</div>
	




</div>


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

function visualizza_moduli($idarea)
{
$conn = db_connect();
$query="select * from area where idarea=$idarea";
$rs = mssql_query($query, $conn);
$row = mssql_fetch_assoc($rs);
$nome_area=pulisci_lettura($row['area']);
mssql_free_result($rs);


$query="select * from re_moduli_aree_nome_sgq where idarea=$idarea order by nome asc";
$rs = mssql_query($query, $conn);
if(!$rs)
{		
    echo("no");
    exit();
    die();
}
$rs_area_istanza=0;
$row_area_istanza =0;
$conta1=0;
$conta=mssql_num_rows($rs);
if($conta==0){
    $query_area_istanza="select * from istanze_testata_sgq where id_area=$idarea";
    $rs_area_istanza = mssql_query($query_area_istanza, $conn);
    $conta1=mssql_num_rows($rs_area_istanza);
    
}

//$query="select * from re_moduli_aree_nome where idarea=$idarea order by nome asc";



?>
<div id="wrap-content"><div class="padding10">
<div id="cartella_clinica">
<div class="logo"><img src="images/re-med-logo.png" /></div>
<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
<div class="titoloalternativo">
            <h1>Moduli compilabili per l'Area: <?=$nome_area?></h1>
	         <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
             <?
     	if(($conta==0) and ($conta1==0)){
    	?>
    	<div class="info">Non esistono moduli compilabili per l'Area: <?=$nome_area?> </div>
	    <?
	    exit();}
        ?>
</div>
			 
<div class="titolo_pag">
		<div class="comandi">
		</div>
</div>
<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_re('wrap-content')" >ritorna alla lista aree</a></div>			
	</div>
</div>

			 
<table id="table" class="tablesorter" cellspacing="1"> 
	<thead> 
		<tr> 
			<th>codice sgq</th> 
			<th>modulo</th> 						 
			<th>ultima compilazione</th> 
			<td>esplora</td>
			<td>anteprima</td> 
			<?if($conta>0){?><td>aggiungi</td><?}?>
		</tr> 
</thead> 
<tbody> 
<?
if($conta>0){
	while($row = mssql_fetch_assoc($rs))   
	{
		$idmodulo_id=$row['idmodulo_id'];
		$idmodulo=$row['idmodulo'];
		$codice=pulisci_lettura($row['codice']);
		$nome=pulisci_lettura($row['nome']);		
		$ultima_compilazione=$row['data'];	
		
		$img="";
		if ($obbligatorio=='s')
			$img="spunto.png";
		$u_c="";
		 if ($ultima_compilazione!='')
			$u_c=formatta_data($ultima_compilazione);
		
	?>
					
					<tr> 
					 <td><?=$codice?></td>
					 <td><?=$nome?></td>					 
					 <td><?=$u_c?></td> 
					 <? if ($ultima_compilazione!='')
					 {
					 	$ultima_compilazione=formatta_data($ultima_compilazione);
					 	?>
					  <td><a href="#"  onclick="javascript:view_istanze_modulo('<?=$idarea?>','<?=$idmodulo_id?>',<?=$conta?>);"><img src="images/view.png" /></a></td> 
					  <td>
					   <?
		            	$query="select idmodulo,stampa_continua,replica,codice from moduli where id=$idmodulo_id";					
             			$rs1 = mssql_query($query, $conn);
            			if(!$rs1)
	     				{		
	            			echo("no");
	            			exit();
	             			die();
	                    }
	               		if($row1 = mssql_fetch_assoc($rs1))
	             		{
	              			$idmodulo=$row1['idmodulo'];
	               			$replica=$row1['replica'];
               				$codice=$row1['codice'];
             			}
            			if  ((get_permesso_modulo_vis($idmodulo,$_SESSION['UTENTE']->get_gid())) and  (($replica==1) or (($replica==0) and (trim($ultima_compilazione)==''))))
	               		{
                            $query_area="select * from re_moduli_aree_nome_sgq where idarea=$idarea order by nome asc";
                            $rs_area = mssql_query($query_area, $conn);
                        if(!$rs_area)
                        {		
	          			    echo("no");
		        		    exit();
		         		    die();
	                    }
                        ?>
                        <a <?if(mssql_num_rows($rs_area)>0) { ?>  href="#"  onclick="javascript:ante_istanze_modulo('<?=$idarea?>','<?=$idmodulo_id?>');"><img src="images/book_open.png" /><?}?></a>
                     <? }else {
                        echo("&nbsp;");
					    }?>
					  
					  <!--<a href="#"  onclick="javascript:ante_istanze_modulo('<?=$idarea?>','<?=$idmodulo_id?>');"><img src="images/book_open.png" /></a>-->
					  </td> 
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
					  $query="select idmodulo,stampa_continua,replica,codice from moduli where id=$idmodulo_id";					
					  $rs1 = mssql_query($query, $conn);
						
				  	if(!$rs1)
					{		
				        echo("no");
			        	exit();
				        die();
	                }
                    if($row1 = mssql_fetch_assoc($rs1))
					{
						$idmodulo=$row1['idmodulo'];
						$replica=$row1['replica'];
						$codice=$row1['codice'];
					}
					  
					if  ((get_permesso_modulo($idmodulo,$_SESSION['UTENTE']->get_gid())) and  (($replica==1) or (($replica==0) and (trim($ultima_compilazione)==''))))
					 // if (get_permesso_modulo($idmodulo,$_SESSION['UTENTE']->get_gid()))
					{
                    $query_area="select * from re_moduli_aree_nome_sgq where idarea=$idarea order by nome asc";
                    $rs_area = mssql_query($query_area, $conn);
                    if(!$rs_area)
                    {		
				        echo("no");
				        exit();
				        die();
	                }
                     
                     
                     
                     
                     ?>
                      	<a <?if(mssql_num_rows($rs_area)>0) { ?> href="#"  onclick="javascript:add_modulo('<?=$idarea?>','<?=$idmodulo_id?>');" ><img src="images/add.png" /><?}?></a>
                        <? }else {
                        echo("&nbsp;");
						}?>
                        </td> 
					</tr> 
					
					
			
			<?
		
	}
	mssql_free_result($rs);

}else if(($conta==0)and($conta1>0)){
    
    while($row_area_istanze = mssql_fetch_assoc($rs_area_istanza))   
	{   
        $idmodulo=$row_area_istanze['id_modulo'];
      
        $query="SELECT  TOP 1 id AS idmodulo_id, idmodulo, nome, codice FROM dbo.moduli GROUP BY id, idmodulo, nome, codice HAVING (idmodulo = $idmodulo)
ORDER BY idmodulo_id DESC";					
        $rs_versione = mssql_query($query, $conn);
        $row_versione_m = mssql_fetch_assoc($rs_versione);
		
        $idmodulo_id=$row_versione_m['idmodulo_id'];
		$codice=pulisci_lettura($row_versione_m['codice']);
		$nome=pulisci_lettura($row_versione_m['nome']);		
		$ultima_compilazione=$row_area_istanze['datains'];	
		
        $u_c=formatta_data($ultima_compilazione);
        
	?>
    <tr> 
        <td><?=$codice?></td>
        <td><?=$nome?></td>					 
        <td><?=$u_c?></td> 
        <td><a href="#"  onclick="javascript:view_istanze_modulo('<?=$idarea?>','<?=$idmodulo_id?>','<?=$conta?>');"><img src="images/view.png" /></a></td> 
        <td><a href="#"  onclick="javascript:ante_istanze_modulo('<?=$idarea?>','<?=$idmodulo_id?>');"><img src="images/book_open.png" /></a></td>
        <!--<td></td>-->
    </tr> 
    <?
	}
	mssql_free_result($rs_versione);
	mssql_free_result($rs_area_istanza);
}
	
	
	
?>			
        

</tbody>
</table>

<? 
footer_paginazione($conta);
?>
</div>

<script type="text/javascript" id="js">
function ritorna_re(divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_sgq.php?";
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
function add_modulo(idarea,idmodulo)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_sgq.php?do=aggiungi_modulo&idarea="+idarea+"&idmodulo="+idmodulo;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function view_istanze_modulo(idarea,idmodulo,conta)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_sgq.php?do=visualizza_lista_istanze_modulo&idarea="+idarea+"&idmodulo="+idmodulo+"&conta="+conta;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function ante_istanze_modulo(idarea,idmodulo)
	{
	$("#layer_nero2").toggle();
	$('#cartella_clinica').innerHTML="";
	pagina_da_caricare="re_sgq.php?do=anteprima_istanze_modulo&idarea="+idarea+"&idmodulo="+idmodulo;
	$("#cartella_clinica").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
	
	function ritorna_cartelle(idpaziente,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_pazienti_sanitaria.php?do=&id="+idpaziente;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	 
	}

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

exit();

}


function aggiungi_modulo()
{

$idarea=$_REQUEST['idarea'];
$idmoduloversione=$_REQUEST['idmodulo'];
$opeins=$_SESSION['UTENTE']->get_userid();

$conn = db_connect();
$query="select * from max_vers_moduli_sgq where idmoduloversione_new=$idmoduloversione";
$rs = mssql_query($query, $conn);
	
if(!$rs)
{		
				echo("no");
				exit();
				die();
	}

if($row = mssql_fetch_assoc($rs))   
	$idmoduloversione_new=$row['idmoduloversione_new'];
	$nome_modulo=pulisci_lettura(trim($row['nome']));	
	
$query="select * from campi where idmoduloversione=$idmoduloversione_new order by peso asc";	

$rs1 = mssql_query($query, $conn);
if(!$rs1)
{		
				echo("no");
				exit();
				die();
	}

$query_per_modulo="select * from moduli where id=$idmoduloversione";
$rs_per_modulo = mssql_query($query_per_modulo, $conn);
if(!$rs_per_modulo)
{		
    echo("no");
    exit();
    die();
}
$row_per_modulo = mssql_fetch_assoc($rs_per_modulo); 
$idmodulo=$row_per_modulo['idmodulo'];


$query_seleziona_area_mod="select * from aree_moduli where idarea=$idarea AND idmodulo=$idmodulo";
//echo($query_seleziona_scad);
$rs_seleziona_area_mod = mssql_query($query_seleziona_area_mod, $conn);
if(!$rs_seleziona_area_mod)
{		
    echo("no");
    exit();
    die();
}
$row_seleziona_area_mod = mssql_fetch_assoc($rs_seleziona_area_mod); 
$pk_aree_moduli=$row_seleziona_area_mod['id'];
//echo($pk_aree_moduli);
$query_seleziona_resp="select * from area_moduli_scadenza_sgq where id_area_moduli=$pk_aree_moduli AND responsabile=$opeins AND memorizzazione='salvata'";
//echo($query_seleziona_resp);
$rs_seleziona_scad = mssql_query($query_seleziona_resp, $conn);
$numScadenze = mssql_num_rows($rs_seleziona_scad);
$is=1;
if($numScadenze==0){		
    $scadenzeOpeins='0';
}else{

    while($row_seleziona_scad = mssql_fetch_assoc($rs_seleziona_scad)){
        $pk_aree_moduli_scadenza[$is]=$row_seleziona_scad['id'];
        //echo($pk_aree_moduli_scadenza[$is]);
        $is++;
    }
}
//echo($pk_aree_moduli);
?>
<script>
    inizializza();
    
    function addListaScadenze(){
     $('#lista_scadenze_mandatory').addClass('mandatory');
     $('#listaScadenze').slideDown();
    }
    
    function remListaScadenze(){
     $('#lista_scadenze_mandatory').removeClass('mandatory');
     $('#listaScadenze').slideUp();
     document.getElementById("seleziona_data_scadenza").value="";
    }
    
   

</script>
<div id="cartella_clinica">
<div class="logo"><img src="images/re-med-logo.png" /></div>
<!-- qui va il codice dinamico dei campi -->
<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna('<?=$idarea?>','cartella_clinica')" >ritorna alla lista dei moduli</a></div>			
	</div>
</div>

	<div class="titolo_pag"><h1>creazione modulo: <?=$nome_modulo?></h1></div>	
	
<form id="myForm" method="post" name="form0" action="re_sgq.php" enctype="multipart/form-data">
    
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>

	<input type="hidden" name="action" value="create_modulo" />
	<input type="hidden" name="idarea" value="<?=$idarea?>" />	
	<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
	<input type="hidden" name="numScadenze" value="<?=$numScadenze?>" />
	
    
	<div class="blocco_centralcat">
    <?if($numScadenze>0){?>	
        <div class="riga">
        
        <div class="rigo_mask">
            <div class="testo_mask">seleziona scadenza</div>
            <div class="campo_mask ">
                <select name="seleziona scadenza">
                    <option value="0"  <?if($numScadenze>0){?>onclick="javascript:addListaScadenze()"<?}?>>scadenze assegnate</option>   
                    <option value="1" selected onclick="javascript:remListaScadenze();">nessuna scadenza</option>   
                </select>
            </div>
        </div>
        <div class="rigo_mask">
                <div id="listaScadenze" <?if($numScadenze!=0){?>style="display:none;"<? }?> >
                <?
                $arr_seleziona_scadenze[]=array();
                $query_seleziona_scadenze="SELECT TOP 6 * from re_scadenze_responsabile where (responsabile=$opeins) and (id_istanza_testata_sgq=NULL)";
                $rs_seleziona_scadenze = mssql_query($query_seleziona_scadenze, $conn);
                    $scadenza= $row1['data_scadenza_generata'];
                $i_scad=0;
                while($row_seleziona_scadenze=mssql_fetch_assoc($rs_seleziona_scadenze)){
                    $arr_seleziona_scadenze[$i_scad]=$row_seleziona_scadenze;
	                $i_scad++;	
                }?>
                <div id="lista_scadenze_mandatory"  <?if($numScadenze==0){?>class="mandatory"<? }?>>
                    <div class="testo_mask">data</div>
                    <select id="seleziona_data_scadenza" name="seleziona_data_scadenza" >
                    <option value="" selected>Seleziona una data</option>
                    <?php								
                    for($y_scad=0;$y_scad<sizeof($arr_seleziona_scadenze);$y_scad++){
                        $dataFormattata= formatta_data($arr_seleziona_scadenze[$y_scad]['data_scadenza_generata']);
                        echo("<option value='".$arr_seleziona_scadenze[$y_scad]['id_scadenza_generata']."'");
                        if ($seleziona_data_scadenza==$arr_seleziona_scadenze[$y]['id_scadenza_generata']) /*echo("selected")*/;
                             echo(">".$dataFormattata."</option>");						
                    }
                    ?>								
                     </select>
                </div>
                </div>
        </div>
    </div>        
    <?
    }
        
		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		$i=1;
		
		while($row1 = mssql_fetch_assoc($rs1)){

			$idcampo= $row1['idcampo'];
			$etichetta= pulisci_lettura($row1['etichetta']);
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
		
			if ($obbligatorio=='y')
			$classe="mandatory ".$classe;

			$multi=0;
			if($tipo==4){
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3)
			{		
				echo("no");
				exit();
				die();
	}
			$row3 = mssql_fetch_assoc($rs3);
			$multi=$row3['multi'];
			mssql_free_result($rs3);
			}
            elseif($tipo==9){
			$query3="select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3) error_message(mssql_error());
			$row3 = mssql_fetch_assoc($rs3);
			$multi_ram=$row3['multi'];
			mssql_free_result($rs3);
			}
	
			if(($i%3==1)and ($tipo!=5)){ 
				echo("<div class=\"riga\">");
				$div=1;
			}
			if((($tipo==2)or($multi==1)) and(($i%3==2)or($i%3==0))){
				echo("</div><div class=\"riga\">");
			}
				if(($tipo==5)and(($i%3==2)or($i%3==0))){
				echo("</div><div>");
			}
			if($multi==1) $stile="style=\"width:600px;\"";
		?>
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<!-- commentare per etichetta-->
			<div class="etichetta_campi"><?=$etichetta?></div>
			<input type="hidden" class="scrittura" name="<?=$idcampo?>" >	
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5))  echo("rigo_big");?>"<?php if($multi==1) echo($stile);?>>
            <div class="testo_mask"><?=$etichetta?></div>            
			<div class="campo_mask <?=$classe?>">
                <?if($tipo==1){?>
					<input type="text" class="scrittura" name="<?=$idcampo?>" >
				  <?}
				     elseif($tipo==2)
					 {?>
					<textarea class="scrittura_campo" name="<?=$idcampo?>" ></textarea>
				  <?}
				    elseif(($tipo==3) or($tipo==6))
					 {?>
					<input type="text" class="scrittura campo_data" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==7)
					 {
					 ?>
					<input type="file" class="scrittura_campo" name="<?=$idcampo?>" >
				  <?}
				  elseif($tipo==8)
					 {?>
					<input type="text" class="scrittura_campo" name="<?=$idcampo?>" value="<?=$titolo_medico.$nome_medico?>">
				  <?}
				   elseif($tipo==4)
					 {
						$query2="select * from re_moduli_combo where idcampo=$idcampo and stato=1";	
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2)
						{		
				echo("no");
				exit();
				die();
	}
					 if($multi==1){
						$xy=1;	
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);
						?>					
					<div style="float:left; padding:0 10px 0 0;"><input type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=$etichetta?></div>
					<?
						$xy++;
							}						
					 }else{?>
						<select class="scrittura" name="<?=$idcampo?>">
						<option value="">Selezionare un Valore</option><?
					 }										
					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$valore=pulisci_lettura($row2['valore']);
                        $etichetta=pulisci_lettura($row2['etichetta']);
					?>
					<option value="<?=$valore?>"><?=$etichetta?></option>
					<?}?>
					</select>
				  <?}
                  elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo and stato=1";	
						
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$xy=1;	
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);
						?>					
					<div style="float:left; padding:0 10px 0 0;clear:both;"><input type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=$etichetta?></div>
					<?
						$xy++;
							}						
					 }else{?>
						<select class="scrittura" name="<?=$idcampo?>" style="width:auto;">
						<option value="">Selezionare un Valore</option><?
					 }
					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore=pulisci_lettura($row2['valore']);
                        $etichetta=pulisci_lettura($row2['etichetta']);
						?>
						<option value="<?=$idcampocombo?>"><?=$etichetta?> </option>     
						<!--<optgroup label="<?=$etichetta?>">-->
						<?
						$rinc=rincorri_figlio($idc,$idcampocombo,"",""); 
						?><!--</optgroup>--><?	
                        
					/*
					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$valore=$row2['valore'];
						$etichetta=$row2['etichetta'];
					?>
					<option value="<?=$valore?>"><?=$etichetta?></option>
					<?}*/	
					}?>
					</select>
				  <?}?>
            </div>
			<?}?>
		</div>	
   
	<?
	if(($tipo==2)or($multi==1)) $i=0;
	if($i%3==0){ 
		echo("</div>");
		$div=0;}
	if($tipo!=5) $i++;	
	}	
	if($div==1) echo("</div>");				
	?>
		</div>
<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	</form>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>

</div>
<?

}


function create_modulo()
{

	$conn = db_connect();
	$idarea=$_REQUEST['idarea'];
    $id_data_scadenza=$_POST['seleziona_data_scadenza'];
	//$idpaziente=$_REQUEST['idpaziente'];
	$idmoduloversione=$_REQUEST['idmoduloversione'];
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
    echo ("ok;".$idarea.";5;re_sgq.php?do=visualizza_moduli");
    exit();
}



function anteprima_istanze_modulo(){

$uid=$_SESSION['UTENTE']->get_userid();
$idarea=$_REQUEST['idarea'];
$idmoduloversione=$_REQUEST['idmodulo'];
$conn = db_connect();
$query="select idarea,area from area where idarea=$idarea";
$rsa = mssql_query($query, $conn);
$rowa=mssql_fetch_assoc($rsa);
$nome_area=pulisci_lettura($rowa['area']);
//echo($nome_area);
//exit();
mssql_free_result($rsa);

$query="select id,idmodulo,nome,replica from moduli where id=$idmoduloversione";
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$nome_modulo=pulisci_lettura($row1['nome']);
$id_modulo_padre=$row1['idmodulo'];
mssql_free_result($rs1);

//$query="select * from re_moduli_valori_sgq WHERE (idarea = $idarea) AND (idmodulo=$id_modulo_padre) ORDER BY idinserimento, peso";

$query="select * from re_moduli_valori_sgq WHERE (id_area = $idarea) AND (id_modulo=$id_modulo_padre) AND (uid=$uid) ORDER BY id_inserimento, peso";
$rs1 = mssql_query($query, $conn);
//echo($query);
//exit();
$txt_dati_modulo="Area: <b>$nome_area</b> - Modulo SGQ: <b>$nome_modulo</b>\n\n";
srand((double)microtime()*1000000);  
$random=rand(0,999999999999999);
$filename_tmp = "tmp_data_".$random."_".$nome_modulo.".txt";
$filename_tmp=str_replace("/","_",$filename_tmp);
//echo($filename_tmp);
$destination_path =MODELLI_WORD_DEST_PATH;	
//echo($destination_path);
//exit();

?>
<script>inizializza();</script>
<div id="cartella_clinica">

<div class="logo"><img src="images/re-med-logo.png" /></div>
    <div class="titoloalternativo">
            <h1>Area: <?=$nome_area?></h1>
	        <!--<div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
            <?
			$web="javascript:stampa2('wrap-content');";
			$xls="";$xml="";
			$pdf="pdfclass/stampa_istanze_sgq.php?file=$filename_tmp&user=".$_SESSION['UTENTE']->get_userid()."&idarea=$idarea&idmodulo=$id_modulo_padre&presenza_indice=1";
			//echo($pdf="pdfclass/stampa_istanze_sgq.php?file=$filename_tmp&user=".$_SESSION['UTENTE']->get_userid()."&idarea=$idarea&idmodulo=$id_modulo_padre");
			//exit();
			include_once('include_stampa.php');
			?>
		</div>
<div class="titoloalternativo">
            <h1>modulo: <?=$nome_modulo?></h1>
</div>
<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna('<?=$idarea?>','cartella_clinica')" >ritorna alla lista dei moduli</a></div>		
	</div>
</div>

<?
	$numerofield=100;
	$style='';
	$class='';
	$dis="";
	

	$datains="";
	$orains="";
	$operatore="";
	$IST="";
	$idinserimento="";
	$MOD="0";
	$txt_dati_testata="1<".$nome_modulo.">\n\n";
	while($row1 = mssql_fetch_assoc($rs1)){
        
        //if (($row1['opeins']==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($row1['id_modulo'],$_SESSION['UTENTE']->get_gid()))){
			
		if (($row1['opeins']==$_SESSION['UTENTE']->get_userid()) or  (get_permesso_modulo_vis($row1['id_modulo'],$_SESSION['UTENTE']->get_gid()))){
			
			if ($idinserimento!=$row1['id_inserimento']) {
			
				if($div==1) echo("</div>");
				if($IST==1){
					$IST="";
					echo("</div><div class=\"titolo_pag\"><div class=\"comandi\"></div></div></form>");
					if($txt_dati_valori!="") $content.=$txt_dati_testata.$txt_dati_creazione.$txt_dati_impegnativa.$txt_dati_valori."#NP\n";
			        $txt_dati_valori="";
				}
				$datains=formatta_data($row1['datains']);
				$idinserimento=$row1['id_inserimento'];
				$orains=$row1['orains'];
				$operatore=$row1['nome'];
				$scadenza_associata=formatta_data($row1['scadenza_associata']);
				//echo("scadenza_associata");
				//echo($scadenza_associata);
				if(($scadenza_associata=="01/01/1900") || ($scadenza_associata==null))
                    $scadenza_associata="nessuna";
				$IST=1;
				$i=1;
				$div=0;				
				//$txt_dati_intestazione="\n2< Nome Modulo: <b>".strtoupper($nome_modulo)."</b>>";?>
				<?
				
				
				
               // if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){
                if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->get_gid()))){
				
				?>
                	<div class="titolo_pag"><h1>modulo creato da: <?=$operatore?> il: <?=$datains?> ore: <?=$orains?> data associata: <?=$scadenza_associata?></h1></div>	
                	
					<?
					$txt_dati_creazione="\n2<creato da:$operatore il:$datains alle ore:$orains  >\n\n";
					
					$txt_dati_valori.="\n\n<b>data associata</b>";
					$txt_dati_valori.="\n".pulisci_lettura($scadenza_associata)."\n\n";
					?>
				
				
				<?}else{
             	$txt_dati_creazione="";
	            }?>
				<form id="myForm" method="post" name="form0" action="re_sgq.php">
					
					<div class="nomandatory">
						<input type="hidden" name="nomandatory" value="1" />
					</div>
					<input type="hidden" name="action" value="update_modulo" />
					<input type="hidden" name="idarea" value="<?=$idarea?>" />			
					<input type="hidden" name="idinserimento" value="<?=$idinserimento?>" />
					<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
					
					
					<div class="blocco_centralcat">
				<?}

					$idvalore= $row1['idvalore'];
					$idcampo= pulisci_lettura($row1['idcampo']);
					$etichetta= pulisci_lettura($row1['etichetta']);
					$tipo = $row1['tipo'];
					$editabile = $row1['editabile'];
					$obbligatorio = $row1['obbligatorio'];
					$classe=$row1['classe'];
					$valore=pulisci_lettura($row1['valore']);
				
					if ($obbligatorio=='y')
					$classe="mandatory ".$classe;
				$multi_ram=0;
				$multi=0;
				if($tipo==4){
					$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo";	
					$rs3 = mssql_query($query3, $conn);
					if(!$rs3)
					{		
				echo("no");
				exit();
				die();
	            }
					$row3 = mssql_fetch_assoc($rs3);
					$multi=$row3['multi'];
					mssql_free_result($rs3);
					}
                if($tipo==9){
					$query3="select * from re_moduli_combo_ramificate_propriety WHERE re_moduli_combo_ramificate_propriety.idcampo=$idcampo";	
					//echo($query3);
					//exit();
					/*$query3="select * 
                            FROM re_moduli_combo_ramificate_propriety,re_moduli_combo_ramificate
                            WHERE (re_moduli_combo_ramificate_propriety.idcampo=$idcampo)  AND (re_moduli_combo_ramificate.idcampo=$idcampo)";*/	
				    $rs3 = mssql_query($query3, $conn);
				    if(!$rs3) error_message(mssql_error());
					$row3 = mssql_fetch_assoc($rs3);
					$multi_ram=$row3['multi'];
					//echo("multi_ram");
					//echo($multi_ram);
					//exit();
					mssql_free_result($rs3);
					}
						
					if(($i%3==1) and ($tipo!=5)){ 
						echo("<div class=\"riga\">");
						$div=1;
					}
					if((($tipo==2)or($multi==1)) and(($i%3==2)or($i%3==0))){
						echo("</div><div class=\"riga\">");
					}
					if(($tipo==5)and(($i%3==2)or($i%3==0))){
						echo("</div><div>");
					}
				if($multi==1) $stile="style=\"width:600px;\"";			
				?>
				
				<? if ($tipo==5) {?>			
					<div class="rigo_big" style="float:left;">
					<div class="etichetta_campi"><?=$etichetta?></div>
					<?	
					$txt_dati_valori.="<".pulisci_lettura($etichetta).">\n\n";
					} else{?>
				<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if($multi==1) echo($stile);?>> 
					<div class="testo_mask"><?=$etichetta?></div>            
					<div class="campo_mask ">
						<?
						$txt_dati_valori.="<b>".pulisci_lettura($etichetta)."</b>\n";
						if(($tipo==1)or($tipo==3)or($tipo==6)or($tipo==8)){
					echo($valore);
					$txt_dati_valori.=pulisci_lettura($valore)."\n\n";

				  }
				elseif($tipo==7){
?>
					<div id="file_name" style="display:block;"><a target="_new" href="load_file.php?filename=<?=$valore?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>" style="text-decoration:none;" ><img src="images/view.png" /> <?=$valore?></a>
					<input type="hidden" class="scrittura" name="<?=$idcampo?>_file" value="<?=$valore?>" >		
					</div>
                    <div id="file_box" class="campo_mask nomandatory" style="display:none">
                        <input type="file" class="scrittura_campo" name="<?=$idcampo?>" >
                    </div>	
<?
                    if(trim($valore)=="") $valore="non presente";
	                $txt_dati_valori.=pulisci_lettura($valore)."\n\n";
                }				
						elseif($tipo==2){
							// recupera il dato
                            $query="select id_istanza_testata_sgq from istanze_testata_sgq WHERE (id_area = $idarea) AND (id_modulo=$id_modulo_padre) AND (id_inserimento=$idinserimento) ORDER BY id_inserimento, peso";
                            $rs2 = mssql_query($query, $conn);
                            while($row2 = mssql_fetch_assoc($rs2)){
                                $id_istanza_testata_sgq=$row2['id_istanza_testata_sgq'];
                                
                                $db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
							    $res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
							    $res = odbc_exec ($db, "SELECT valore FROM istanze_dettaglio_sgq WHERE (id_campo=$idcampo) AND (id_istanza_testata_sgq=$id_istanza_testata_sgq)");
							    odbc_longreadlen ($res, MAX);
							    $text = odbc_result ($res, "valore");
                                $text = str_replace("\n","<br>",$text);
							    $valore = pulisci_lettura($text);
							    odbc_close($db);
							    echo($valore);	
								$txt_dati_valori.=($valore)."\n\n";
                            }
						}
						  elseif($tipo==4)
							 {
								$query2="select * from re_moduli_combo where idcampo=$idcampo";									
								$rs2 = mssql_query($query2, $conn);
								
								if(!$rs2)
								{		
				                    echo("no");
				                    exit();
				                    die();
	                            }
							 if($multi==1){
								$et="";								
								$valore=split(";",$valore);						
								while ($row2 = mssql_fetch_assoc($rs2))
									{
										$valore1=pulisci_lettura($row2['valore']);
								        $etichetta=pulisci_lettura($row2['etichetta']);
										if (in_array($valore1,$valore)) $et.=$etichetta.", ";									
									}
								$et=substr($et,0,strlen($et)-2);
								echo(pulisci_lettura($et));
								$txt_dati_valori.=pulisci_lettura($et)."\n\n";

							 }else{				
							while ($row2 = mssql_fetch_assoc($rs2))
									{								
										if ($row2['valore']==$valore) {
										    echo($row2['etichetta']);	
                                            $txt_dati_valori.=pulisci_lettura($row2['etichetta'])."\n";											
										}
									}
							$txt_dati_valori.="\n";	
							//echo($valore1);
							
						   }
						  }
                           elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";	
                       // echo($query2);
                         //exit();						
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2) error_message(mssql_error());

					 if($multi_ram==1){
						$et="";
						$valore=split(";",$valore);									
						while ($row2 = mssql_fetch_assoc($rs2))
							{									
								$valore1=$row2['valore'];
								$etichetta=$row2['etichetta'];
								if (in_array($valore1,$valore)) $et.=$etichetta."<br/>";									
							}
						$et=substr($et,0,strlen($et)-2);						
						echo(pulisci_lettura($et));
						$txt_dati_valori.=pulisci_lettura($et);
					 }else{	
									 
					while ($row2 = mssql_fetch_assoc($rs2))	{						
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore1=$row2['valore'];
						$etichetta1=$row2['etichetta'];
						$stampa=$row2['stampa'];
						$v=(int)($valore);						
						if(($v==$valore1) and($stampa)){ 
							echo($etichetta1);
							$txt_dati_valori.=pulisci_lettura($etichetta1);
							}
							else{
							$rinc=rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
							$txt_dati_valori.=$rinc."\n";
							}
					}
				   }
				  }			  
						 ?>
					</div>
					<?}?>
				</div>	
		   
			<?
			if(($tipo==2)or($multi==1)) $i=0;			
			if($i%3==0){ 
				echo("</div>");				
				$div=0;}
			if($tipo!=5) $i++;	
		}		
	}
	if($div==1) echo("</div>");
	$content.=$txt_dati_testata.$txt_dati_creazione.$txt_dati_modulo.$txt_dati_valori;
	$content=str_replace("<br/>","\n",$content);
	$content=str_replace("<br","",$content);
	$handle = fopen($destination_path.$filename_tmp, "w");
	fwrite($handle,$content);	
	if($IST==1){
		$IST="";?>	
		</div>
<div class="titolo_pag">
		<div class="comandi">			
		</div>
	</div>
	</form>
	<?}?>
</div>
<script type="text/javascript" id="js">
function ritorna(idarea,divagg)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_sgq.php?do=visualizza_moduli&id="+idarea;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });


	}
</script>
<?
}

function visualizza_lista_istanze_modulo()
{

$idarea=$_REQUEST['idarea'];
$idmoduloversione=$_REQUEST['idmodulo'];
$conta_area_mod=$_REQUEST['conta'];

$conn = db_connect();
$query="select idarea,area from area where idarea=$idarea";
$rsa = mssql_query($query, $conn);
$rowa=mssql_fetch_assoc($rsa);
$nome_area=pulisci_lettura($rowa['area']);
mssql_free_result($rsa);

$nome_modulo="nome del modulo";

	$conn = db_connect();
	$query="select idmodulo,nome,replica from moduli where id=$idmoduloversione";
	
	$rs = mssql_query($query, $conn);
		
	if(!$rs)
	{		
				echo("no");
				exit();
				die();
	}

	if($row = mssql_fetch_assoc($rs))
	{
		$nome_modulo=pulisci_lettura($row['nome']);
		$idmodulo=$row['idmodulo'];
		$replica=$row['replica'];
	}
$conn = db_connect();
/*
query="SELECT nome, idinserimento, idarea, idmodulo, datains, orains, opeins FROM dbo.re_istanze_moduli_sgq 
	GROUP BY nome, idinserimento, idarea, idmodulo, datains, orains, opeins HAVING idarea=$idarea and idmodulo=$idmodulo";
*/





$query="SELECT nome, id_inserimento, id_area, id_modulo, datains, orains, opeins,id_istanza_testata_sgq FROM dbo.re_istanze_moduli_sgq 
	GROUP BY nome, id_inserimento, id_area, id_modulo, datains, orains, opeins,id_istanza_testata_sgq HAVING id_area=$idarea and id_modulo=$idmodulo";
$rs = mssql_query($query, $conn);
	
if(!$rs)
{		
    echo("no");
    exit();
    die();
	}

$div="sanitaria";

?>

<div id="sanitaria">
<div class="logo"><img src="images/re-med-logo.png" /></div>
<!--<input type="hidden" name="conta_area_mod" value="<?=$conta_area_mod?>" />-->
<div class="titoloalternativo">
            <h1>Area: <?=$nome_area?></h1>
	        <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>

</div>
<div class="titoloalternativo">
            <h1>modulo: <?=$nome_modulo?></h1>
</div>


<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna('<?=$idarea?>','sanitaria','<?=$conta_area_mod?>')" >ritorna alla lista dei moduli</a></div>			
	</div>
</div>

<?
$conta=mssql_num_rows($rs);
    if($conta==0){
?>
	<div class="titoloalternativo">
    </div>
        <div class="info">Non esistono istanze per questo modulo SGQ </div>
    <?
    exit();}
   ?>
<table id="table" class="tablesorter" cellspacing="1"> 
	<thead> 
		<tr> 			
			<th>data creazione</th> 
			<th>ora creazione</th> 
			<th>operatore</th>  
			<th>scadenza associata</th> 
			<td width="5%">anteprima</td> 			
			<?if($conta_area_mod > 0) {?><td width="5%">modifica</td>
	        <td width="5%">cancella</td><?}?>
		</tr> 
        
   
</thead> 
<tbody> 
	
	
		
		
<?

	while($row = mssql_fetch_assoc($rs))   
	{
		$idmodulo=$row['id_modulo'];
		$datains=formatta_data($row['datains']);
		$orains=$row['orains'];
		$operatore=$row['nome'];
		$idoperatore=$row['opeins'];
		$idinserimento=$row['id_inserimento'];
		$id_istanza_testata_sgq=$row['id_istanza_testata_sgq'];
        
        $query_associati="select id_istanza_testata_sgq,data_scadenza_generata from scadenze_generate_sgq where id_istanza_testata_sgq=$id_istanza_testata_sgq";
        //echo($query_associati);
	    $rs_associati = mssql_query($query_associati, $conn);
        $row = mssql_fetch_assoc($rs_associati);
        $id_istanza_test_verifica=$row['id_istanza_testata_sgq'];
        
        //exit();
        echo('id_istanza_test_verifica');
        echo($id_istanza_test_verifica);
        if($id_istanza_test_verifica!=""){
            $data_scadenza_generata=formatta_data($row['data_scadenza_generata']);
            echo($data_scadenza_generata);}
        else
            $data_scadenza_generata="";
	?>					
					<tr> 					
					 <td><?=$datains?></td> 
					 <td><?=$orains?></td> 
					 <td><?=$operatore?></td>			
					 <td><?=$data_scadenza_generata?></td>					  
                      <?
					  
					$query="select idmodulo,opeins from moduli where id=$idmoduloversione";
					$rs1 = mssql_query($query, $conn);
						
					if(!$rs1)
					{		
				echo("no");
				exit();
				die();
	}

					if($row1 = mssql_fetch_assoc($rs1))
					{
						$idmodulo1=$row1['idmodulo'];
						$replica=$row1['replica'];
						//$opeins=$row1['opeins'];
					}
					//$query="select * from sgq_valori where idmodulo=$idmodulo1 and idarea=$idarea and idinserimento=$idinserimento";
                    $query="select * from istanze_testata_sgq where id_modulo=$idmodulo1 and id_area=$idarea and id_inserimento=$idinserimento";
					$rs2 = mssql_query($query, $conn);
						
					if(!$rs2)
					{		
				echo("no");
				exit();
				die();
	}

					if($row2 = mssql_fetch_assoc($rs2))
					{
						$opeins=$row2['opeins'];					
					}					
					mssql_free_result($rs2);
					
						?><td>

							<?				
                        if ( ($idoperatore==$_SESSION['UTENTE']->get_userid()) and  (get_permesso_modulo_vis($idmodulo1,$_SESSION['UTENTE']->get_gid())))							
						{?> 
						
				      	<a href="#"  onclick="javascript:visualizza_istanza_modulo('<?=$idarea?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$conta_area_mod?>')" ><img src="images/book_open.png" /></a>
                        <? }else {
                         echo("&nbsp;");
						}
						?>
						</td>			
                        <?if ($conta_area_mod > 0){?>                        
						<td>
						<?
						
						if (( ($idoperatore==$_SESSION['UTENTE']->get_userid()) and  (get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid()))))
						{?> 
				      	<a href="#"  onclick="javascript:edita_modulo('<?=$idarea?>','<?=$idmoduloversione?>','<?=$idinserimento?>','<?=$div?>','<?=$conta_area_mod?>')" ><img src="images/gear.png" /></a>
                        <? }else {
                        echo("&nbsp;");
						}?>                    	
                      </td> 
                      
                      <td>
						<?
						
						if ( ($idoperatore==$_SESSION['UTENTE']->get_userid()) and  (get_permesso_modulo($idmodulo1,$_SESSION['UTENTE']->get_gid())))
						{?> 
				      	<a href="#"  onclick="javascript:del_istanza('<?=$idarea?>','<?=$idmodulo?>','<?=$idinserimento?>','<?=$div?>','<?=$conta_area_mod?>')" ><img src="images/remove.png" /></a>
                        <? }else {
                        echo("&nbsp;");
						}?>                    	
                      </td> 
                      <?}?>
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

<script type="text/javascript" id="js">

	function ritorna(idarea,divagg,conta)
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_sgq.php?do=visualizza_moduli&id="+idarea;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });

	}
	
	function visualizza_istanza_modulo(idarea,idmodulo,idinserimento,divagg,conta_area_mod)
	{
	div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_sgq.php?do=visualizza_istanza_modulo&idarea="+idarea+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&conta_area_mod="+conta_area_mod;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	}
		
	function edita_modulo(idarea,idmodulo,idinserimento,divagg,conta_area_mod)
	{
	div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_sgq.php?do=edita_modulo&idarea="+idarea+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento+"&conta_area_mod="+conta_area_mod;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });
	}
    
    function del_istanza(idarea,idmodulo,idinserimento,conta_area_mod){
    if (confirm("Sei sicuro di voler eliminare l'istanza ")){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "re_sgq.php?do=del_istanza&idarea="+idarea+"&idmodulo="+idmodulo+"&idinserimento="+idinserimento,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
           view_istanze_modulo('<?=$idarea?>','<?=$idmoduloversione?>',<?=$conta_area_mod?>);//,'<?=$idmoduloversione?>','sanitaria'
           }
		 });
	  }
     else return false;
     }
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

}

function del_istanza($idarea,$idmodulo,$idinserimento){

    $idarea=$_REQUEST['idarea'];
    $idmodulo=$_REQUEST['idmodulo'];
    $idinserimento=$_REQUEST['idinserimento'];

    $conn = db_connect();
    
    $query="Select * FROM istanze_testata_sgq where (id_area=$idarea) and (id_modulo=$idmodulo) and (id_inserimento=$idinserimento)";
    $result = mssql_query($query,$conn);
    echo($query);
    
    while($row2 = mssql_fetch_assoc($result)){
        $id_istanza_testata_sgq=$row2['id_istanza_testata_sgq'];
        
        $query3=" UPDATE scadenze_generate_sgq SET id_istanza_testata_sgq = NULL WHERE (id_istanza_testata_sgq = $id_istanza_testata_sgq)";
        $result3 = mssql_query($query3,$conn);
        echo($query3);
        
        $query1="DELETE FROM istanze_dettaglio_sgq where (id_istanza_testata_sgq=$id_istanza_testata_sgq)";
        $result1 = mssql_query($query1,$conn);
        
    
        $query2="DELETE FROM istanze_testata_sgq where (id_istanza_testata_sgq=$id_istanza_testata_sgq)";
        $result2 = mssql_query($query2,$conn);
    echo($query1);
    echo($query2);
    }
    
}


function edita_modulo()
{
$opeins=$_SESSION['UTENTE']->get_userid();
$idarea=$_REQUEST['idarea'];
$idmoduloversione=$_REQUEST['idmodulo'];
$idinserimento=$_REQUEST['idinserimento'];
$conta_area_mod=$_REQUEST['conta_area_mod'];
$conn = db_connect();
$query="select idarea,area from area where idarea=$idarea";
$rsa = mssql_query($query, $conn);
$rowa=mssql_fetch_assoc($rsa);
$nome_area=pulisci_lettura($rowa['area']);
mssql_free_result($rsa);

$query="select id,idmodulo,nome,replica from moduli where id=$idmoduloversione";
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$nome_modulo=pulisci_lettura($row1['nome']);
$id_modulo_padre=$row1['idmodulo'];
mssql_free_result($rs1);
/*
$query="SELECT nome, idinserimento, idarea, idmodulo, datains, orains, opeins FROM dbo.re_istanze_moduli_sgq
		GROUP BY nome, idinserimento, idarea, idmodulo, datains, orains, opeins
		HAVING      (idarea = $idarea) AND (idmodulo = $id_modulo_padre)";
*/
$query="SELECT nome, id_inserimento, id_area, id_modulo, datains, orains, opeins FROM dbo.re_istanze_moduli_sgq
		GROUP BY nome, id_inserimento, id_area, id_modulo, datains, orains, opeins
		HAVING      (id_area = $idarea) AND (id_modulo = $id_modulo_padre)";
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$datains=formatta_data($row1['datains']);
$orains=$row1['orains'];
$operatore=$row1['nome'];
$id_modulo_padre=$row1['id_modulo'];
mssql_free_result($rs1);

//$query="select * from re_moduli_valori_sgq where idinserimento=$idinserimento and idarea=$idarea and idmodulo=$id_modulo_padre";

$query="select * from re_moduli_valori_sgq where id_inserimento=$idinserimento and id_area=$idarea and id_modulo=$id_modulo_padre";
$rs1 = mssql_query($query, $conn);
if(!$rs1)
{		
    echo("no");
    exit();
    die();
}

$query_per_modulo="select * from moduli where id=$idmoduloversione";
$rs_per_modulo = mssql_query($query_per_modulo, $conn);
if(!$rs_per_modulo)
{		
    echo("no");
    exit();
    die();
}
$row_per_modulo = mssql_fetch_assoc($rs_per_modulo); 
$idmodulo=$row_per_modulo['idmodulo'];


$query_seleziona_area_mod="select * from aree_moduli where idarea=$idarea AND idmodulo=$idmodulo";
//echo($query_seleziona_scad);
$rs_seleziona_area_mod = mssql_query($query_seleziona_area_mod, $conn);
if(!$rs_seleziona_area_mod)
{		
    echo("no");
    exit();
    die();
}
$row_seleziona_area_mod = mssql_fetch_assoc($rs_seleziona_area_mod); 
$pk_aree_moduli=$row_seleziona_area_mod['id'];

//echo('pk_aree_moduli');
//echo($pk_aree_moduli);
$query_seleziona_resp="select * from area_moduli_scadenza_sgq where (id_area_moduli=$pk_aree_moduli) AND (responsabile=$opeins) AND (cancella='n')";
//echo($query_seleziona_resp);
$rs_seleziona_scad = mssql_query($query_seleziona_resp, $conn);
$numScadenze = mssql_num_rows($rs_seleziona_scad);
$is=1;
if($numScadenze==0){		
    $scadenzeOpeins='0';
}else{
    while($row_seleziona_scad = mssql_fetch_assoc($rs_seleziona_scad)){
        $pk_aree_moduli_scadenza[$is]=$row_seleziona_scad['id'];
        //echo($pk_aree_moduli_scadenza[$is]);
        $is++;
    }
}

$query_seleziona_id_ist_testata_sgq="select * from istanze_testata_sgq where id_area=$idarea AND id_modulo=$idmodulo and id_inserimento=$idinserimento";
$rs_seleziona_id_ist_testata_sgq = mssql_query($query_seleziona_id_ist_testata_sgq, $conn);
if(!$rs_seleziona_area_mod)
{		
    echo("no");
    exit();
    die();
}
$row_seleziona_id_ist_testata_sgq = mssql_fetch_assoc($rs_seleziona_id_ist_testata_sgq); 
$pk_istanza_testata_sgq=$row_seleziona_id_ist_testata_sgq['id_istanza_testata_sgq'];


if($numScadenze>0){
    $query_seleziona_scadenza_modifica="SELECT * from re_scadenze_responsabile where (responsabile=$opeins) and (id_istanza_testata_sgq=$pk_istanza_testata_sgq)";
    $rs_seleziona_scadenza_modifica= mssql_query($query_seleziona_scadenza_modifica, $conn);
    $row_seleziona_scadenza_modifica = mssql_fetch_assoc($rs_seleziona_scadenza_modifica); 
    $arr_seleziona_scadenze[]=array();
                
    $arr_seleziona_scadenze[0]=$row_seleziona_scadenza_modifica;
    $id_scadenza_da_eliminare=$arr_seleziona_scadenze[0]['id_scadenza_generata'];
    
}
?>
<script>
inizializza();

function ritorna_istanze(idarea,idmoduloversione,divagg,conta_area_mod) //divagg
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_sgq.php?do=visualizza_lista_istanze_modulo&idarea="+idarea+"&idmodulo="+idmoduloversione+"&conta="+conta_area_mod;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });

	}


function addListaScadenzeMod(){
     $('#lista_scadenze_mandatory').addClass('mandatory');
     document.getElementById("seleziona_data_scadenza").value="<?echo ($id_scadenza_da_eliminare)?>";
     $('#listaScadenze').slideDown();
    }
    
    function remListaScadenzeMod(){
     $('#lista_scadenze_mandatory').removeClass('mandatory');
     $('#listaScadenze').slideUp();
     document.getElementById("seleziona_data_scadenza").value="";
    }
</script>
<div id="sanitaria">
<div class="logo"><img src="images/re-med-logo.png" /></div>
<div class="titoloalternativo">
            <h1>Area: <?=$nome_area?></h1>
	        <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
<div class="titoloalternativo">
            <h1>modifica modulo: <?=$nome_modulo?></h1>
</div>
<!-- qui va il codice dinamico dei campi -->
	
<form id="myForm" method="post" name="form0" action="re_sgq.php">

	<div class="titolo_pag">
	    <div class="comandi">		
		    <div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_istanze('<?=$idarea?>','<?=$idmoduloversione?>','sanitaria','<?=$conta_area_mod?>')" >ritorna elenco istanze</a></div>	
	    </div>
    </div>
    
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>
    
    <input type="hidden" name="action" value="update_modulo" />
	<input type="hidden" name="idarea" value="<?=$idarea?>" />
	<input type="hidden" name="idinserimento" value="<?=$idinserimento?>" />
	<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
	<input type="hidden" name="id_scadenza_da_eliminare" value="<?=$id_scadenza_da_eliminare?>" />
	
	<div class="blocco_centralcat">
    
    <?if(($numScadenze>0)and ($id_scadenza_da_eliminare!="")){?>
        <div class="riga">
        
        <div class="rigo_mask">
            <div class="testo_mask">seleziona scadenza</div>
            <div class="campo_mask ">
                <select name="seleziona scadenza">
                    <option value="0"  <?if($numScadenze>0){?> selected onclick="javascript:addListaScadenzeMod()"<?}?>>scadenze assegnate</option>   
                    <option value="1"onclick="javascript:remListaScadenzeMod();">nessuna scadenza</option>   
                </select>
            </div>
        </div>
        <div class="rigo_mask">
                <div id="listaScadenze" <?if($numScadenze!=0){?>style="display:block;"<?}?> >
                <?
                $query_seleziona_scadenze="SELECT TOP 5 * from re_scadenze_responsabile where (responsabile=$opeins) and (id_istanza_testata_sgq=NULL)";
                $rs_seleziona_scadenze = mssql_query($query_seleziona_scadenze, $conn);
                $i_scad=1;
                while($row_seleziona_scadenze=mssql_fetch_assoc($rs_seleziona_scadenze)){
                    $arr_seleziona_scadenze[$i_scad]=$row_seleziona_scadenze;
	                $i_scad++;	
                }?>
                <div id="lista_scadenze_mandatory"  <?if($numScadenze==0){?>class="mandatory"<? }?>>
                    <div class="testo_mask">data</div>
                    <select id="seleziona_data_scadenza" name="seleziona_data_scadenza" >
                    <option value="" >Seleziona una data</option>
                    <option value="<?=$arr_seleziona_scadenze[0]['id_scadenza_generata']?>" selected> <?echo(formatta_data($arr_seleziona_scadenze[0]['data_scadenza_generata']))?></option>
                    <?							
                    for($y_scad=1;$y_scad<sizeof($arr_seleziona_scadenze);$y_scad++){
                        $dataFormattata= formatta_data($arr_seleziona_scadenze[$y_scad]['data_scadenza_generata']);
                        echo("<option value='".$arr_seleziona_scadenze[$y_scad]['id_scadenza_generata']."'");
                        if ($seleziona_data_scadenza==$arr_seleziona_scadenze[$y]['id_scadenza_generata']) /*echo("selected")*///;
                             echo(">".$dataFormattata."</option>");						
                    }
                    ?>								
                     </select>
                </div>
                </div>
        </div>
        </div>
		<?
        }
		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		$i=1;
	
		
		while($row1 = mssql_fetch_assoc($rs1)){
			$idcampo= $row1['idcampo'];
			$etichetta= pulisci_lettura($row1['etichetta']);
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
			$valore=pulisci_lettura($row1['valore']);
		
			if ($obbligatorio=='y')
			$classe="mandatory ".$classe;
		
		$multi=0;
		if($tipo==4){
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3)
			{		
				echo("no");
				exit();
				die();
	        }
			$row3 = mssql_fetch_assoc($rs3);
			$multi=$row3['multi'];
			mssql_free_result($rs3);
			}
        
        if($tipo==9){
			$query4="select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo";	
            //echo($idcampo);
			$rs4 = mssql_query($query4, $conn);
			if(!$rs4){ 
                error_message(mssql_error());
			}
            $row4 = mssql_fetch_assoc($rs4);
			$multi_ram=$row4['multi'];
			mssql_free_result($rs4);
			}
	
        if(($i%3==1) and ($tipo!=5)){ 
				echo("<div class=\"riga\">");
				$div=1;
			}
        if((($tipo==2)or($multi==1)) and(($i%3==2)or($i%3==0))){
				echo("</div><div class=\"riga\">");
			}
        if(($tipo==5)and(($i%3==2)or($i%3==0))){
				echo("</div><div>");
			}
		if($multi==1) $stile="style=\"width:600px;\"";
		?>
		
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<!--<div class="etichetta_campi"><?=$etichetta?></div>-->
			<input type="hidden" class="scrittura" name="<?=$idcampo?>" >				
			<?} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if($multi==1) echo($stile);?>>
		    <?if($tipo==7){?>
            <div class="testo_mask"><a onclick="document.getElementById('file_box<?=$idcampo?>').style.display='block';document.getElementById('file_name').style.display='none';" class="cambia_valore">cambia <?=pulisci_lettura($etichetta)?></a></div>
			<?} else{?>
				<div class="testo_mask"><?=pulisci_lettura($etichetta)?></div>    
			  <?}?>  
			<div class="campo_mask <?=$classe?>">
                <?if(($tipo==1)or($tipo==8)){
				if ($editabile=='y'){?>
					<input type="text" class="scrittura" name="<?=$idcampo?>" value="<?=$valore?>"  >
					<?}else{
						echo($valore);
					}
				  }
				     elseif($tipo==2)
					 {
					 if ($editabile=='y'){?>
					<textarea class="scrittura_campo" name="<?=$idcampo?>" ><?=$valore?></textarea>
					<?}else{
						echo($valore);
					}
				  }
				   elseif(($tipo==3)or($tipo==6))
					 {
					if ($editabile=='y'){?>
					<input type="text" class="scrittura campo_data" name="<?=$idcampo?>" value="<?=$valore?>"  >
					<?}else{
						echo pulisci_lettura($valore);
					}
				  }
				  elseif($tipo==7)
					{
					
					?>
						<div id="file_name" style="display:<?if(trim($valore)!=""){?>block;<?}else{?>none;<?}?>"><a target="_new" href="load_file.php?filename=<?=$valore?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>" style="text-decoration:none;" ><img src="images/view.png" /> <?=$valore?></a>
						<input type="hidden" class="scrittura" name="<?=$idcampo?>_file" value="<?=$valore?>" >		
						</div>					
					  <div id="file_box<?=$idcampo?>" class="campo_mask nomandatory" style="display:none">
                            <input type="file" class="scrittura_campo" name="<?=$idcampo?>" value="<?=$valore?>">
                        </div>
					<?						
					}
				  elseif($tipo==4)
					 {
						$query2="select * from re_moduli_combo where idcampo=$idcampo";	
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2)
						{		
				echo("no");
				exit();
				die();
	}
					 if(($multi==1)or($multi==2)){
						$xy=1;
						$valore=split(";",$valore);					
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);
								
									if (in_array($valore1,$valore))
									$sel="checked";
									else
									$sel='';
						if ((($sel=='') and($row2['stato']=='1')) or ($sel!='')){
						?>					
					<div style="float:left; padding:0 10px 0 0;"><input <?=$sel?> type="checkbox" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore1?>" /> <?=$etichetta?></div>
					<?
					}
						$xy++;
							}						
					 }else{?>
						<select class="scrittura" name="<?=$idcampo?>">
						<option value="">Selezionare un Valore</option><?
					 }								
					while ($row2 = mssql_fetch_assoc($rs2))
					{
                        $valore1=pulisci_lettura($row2['valore']);
                        $etichetta=pulisci_lettura($row2['etichetta']);
						
						if (trim($valore)==trim($valore1))
						$sel="selected";
						else
						$sel='';
						if ((($sel=='') and($row2['stato']=='1')) or ($sel!='')){
					?>
					<option <?=$sel?> value="<?=$valore1?>"><?=$etichetta?></option>
					<?}
					}
					?>
					</select>
				  <?}
                  elseif($tipo==9)
					 {
                     
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo and stato=1";	
						
						$rs2 = mssql_query($query2, $conn);
						if(!$rs2) error_message(mssql_error());
					 if($multi_ram==1){
						$xy=1;	
						 while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);
						?>					
					<div style="float:left; padding:0 10px 0 0;clear:both;"><input type="list" name="<?=$idcampo?>[<?=$xy?>]" value="<?=$valore?>" /> <?=$etichetta?></div>
					<?
						$xy++;
							}						
					 }else{?>
						<select class="scrittura" name="<?=$idcampo?>" style="width:auto;">
						<option value="">Selezionare un Valore</option><?
					 }

					while ($row2 = mssql_fetch_assoc($rs2))
					{
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						$valore1=pulisci_lettura($row2['valore']);
						$etichetta1=pulisci_lettura($row2['etichetta']);
                        $v=(int)($valore);
						?>
						<option value="<?=$idcampocombo?>"><?=$etichetta1?> </option>
						<!--<optgroup label="<?=$etichetta?>">-->
						<?
						//$rinc=rincorri_figlio($idc,$idcampocombo,"","");
                        $rinc=rincorri_figlio($idc,$idcampocombo,"",$v);
						?><!--</optgroup>--><?	
					
					/*while ($row2 = mssql_fetch_assoc($rs2))
					{
						$valore=$row2['valore'];
						$etichetta=$row2['etichetta'];
					?>
					<option value="<?=$valore?>"><?=$etichetta?></option>
					<?}	*/
						
					}?>
					</select>
				  <?}?>
            </div>
			<?}?>
		</div>	
   
	<?
	if(($tipo==2)or($multi==1)) $i=0;
	if($i%3==0){ 
		echo("</div>");
		$div=0;}
	if($tipo!=5) $i++;	
	}	
	if($div==1) echo("</div>");				
	?>
	
		</div>
<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	</form>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
	</script>
</div>
</div>
<?

}



function visualizza_istanza_modulo()
{

$idarea=$_REQUEST['idarea'];
$idmoduloversione=$_REQUEST['idmodulo'];
$conta_area_mod=$_REQUEST['conta_area_mod'];
$idinserimento=$_REQUEST['idinserimento'];
$conn = db_connect();
$query="select idarea,area from area where idarea=$idarea";
$rsa = mssql_query($query, $conn);
$rowa=mssql_fetch_assoc($rsa);
$nome_area=pulisci_lettura($rowa['area']);
mssql_free_result($rsa);

$query="select id,idmodulo,nome,replica,modello_word from moduli where id=$idmoduloversione";
//echo($query);
$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$nome_modulo=pulisci_lettura($row1['nome']);
//$modello_word=pulisci_lettura($row1['modello_word']);
$modello_word=strpos($row1['modello_word'],".");
//echo($modello_word);

$id_modulo_padre=$row1['idmodulo'];
mssql_free_result($rs1);
/*$query="SELECT nome, idinserimento, idarea, idmodulo, datains, orains, opeins FROM dbo.re_istanze_moduli_sgq
		GROUP BY nome, idinserimento, idarea, idmodulo, datains, orains, opeins
		HAVING      (idarea = $idarea) AND (idmodulo = $id_modulo_padre)";*/
 


 
$query="SELECT nome, id_inserimento, id_area, id_modulo, datains, orains, scadenza_associata, opeins FROM dbo.re_istanze_moduli_sgq
		GROUP BY nome, id_inserimento, id_area, id_modulo, datains, orains, opeins, scadenza_associata
		HAVING      (id_area = $idarea) AND (id_modulo = $id_modulo_padre) AND (id_inserimento=$idinserimento)";
//echo($query);

$rs1 = mssql_query($query, $conn);
$row1=mssql_fetch_assoc($rs1);
$datains=formatta_data($row1['datains']);
$orains=$row1['orains'];
$operatore=$row1['nome'];
$scadenza_associata=formatta_data($row1['scadenza_associata']);
mssql_free_result($rs1);
if(($scadenza_associata=="01/01/1900") || ($scadenza_associata==null))
    $scadenza_associata="nessuna";

//$query="select * from re_moduli_valori_sgq where idinserimento=$idinserimento and idarea=$idarea and idmodulo=$id_modulo_padre";

$query="select * from re_moduli_valori_sgq where id_inserimento=$idinserimento and id_area=$idarea and id_modulo=$id_modulo_padre";


$rs1 = mssql_query($query, $conn);
if(!$rs1)
{		
				echo("no");
				exit();
				die();
	}                                                                                
$txt_dati_modulo=" <b>Area:</b> $nome_area\n <b>Modulo SGQ:</b> $nome_modulo\n\n";
srand((double)microtime()*1000000);  
$random=rand(0,999999999999999);
$filename_tmp = "tmp_data_".$random."_".$nome_modulo.".txt";
$filename_tmp=str_replace("/","_",$filename_tmp);
//echo($filename_tmp);
$destination_path =MODELLI_WORD_DEST_PATH;	
//echo($destination_path);
//exit();
//$txt_dati_testata="1<".$nome_modulo.">\n";
?>

<script>inizializza();</script>
<div id="sanitaria">
<div class="logo"><img src="images/re-med-logo.png" /></div>
<div class="titoloalternativo">
            <h1>Area: <?=$nome_area?></h1>
	        <!--<div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>-->
            <?
			$web="javascript:stampa2('wrap-content');";
			$xls="";$xml="";
			if ($modello_word!=""){
				if ($idarea>0){
					$doc="stampa_modulo.php?idarea=$idarea&idinserimento=$idinserimento&idmodulopadre=$id_modulo_padre&ngid=".$_SESSION['UTENTE']->get_gid();
					}else
					$doc="stampa_modulo.php?idarea=0&idinserimento=$idinserimento&idpaziente=$idpaziente&idmodulopadre=$id_modulo_padre&ngid=".$_SESSION['UTENTE']->get_gid();
			} 
			//$pdf="pdfclass/stampa_istanza.php?file=$filename_tmp&user=".$_SESSION['UTENTE']->get_userid()."&idpaz=$idpaziente&idcart=$idcartella&idmod=$id_modulo_padre";
			$pdf="pdfclass/stampa_istanza_sgq.php?file=$filename_tmp&user=".$_SESSION['UTENTE']->get_userid()."&idarea=$idarea&idmodulo=$id_modulo_padre&presenza_indice=0";
			//echo($pdf="pdfclass/stampa_istanza_sgq.php?file=$filename_tmp&user=".$_SESSION['UTENTE']->get_userid()."&idarea=$idarea&idmodulo=$id_modulo_padre");
			//exit();
			include_once('include_stampa.php');
			?>
</div>
<div class="titoloalternativo">
            <h1>modulo: <?=$nome_modulo?></h1>
</div>


<div class="titolo_pag">
	<div class="comandi">		
		<div class="aggiungi aggiungi_left"><a href="#"  onclick="javascript:ritorna_istanze('<?=$idarea?>','<?=$idmoduloversione?>','sanitaria','<?=$conta_area_mod?>')" >ritorna elenco istanze</a></div>	
		<!--<div class="aggiungi aggiungi_left"><a href="stampa_modulo.php?idarea=<?//=$idarea?>&idinserimento=<?//=$idinserimento?>&idmodulopadre=<?//=$id_modulo_padre?>&ngid=<?//=$_SESSION['UTENTE']->get_gid()?>">stampa modulo</a></div>	-->
	    <!--
		<?
	    if ($modello_word!=""){
		    if ($idarea>0) {?>
		    <div class="aggiungi aggiungi_left"><a href="stampa_modulo.php?idarea=<?=$idarea?>&idinserimento=<?=$idinserimento?>&idmodulopadre=<?=$id_modulo_padre?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>">stampa modulo</a></div>
	    	<? } else {?>
		         <div class="aggiungi aggiungi_left"><a href="stampa_modulo.php?idarea=0&idinserimento=<?=$idinserimento?>&idmodulopadre=<?=$id_modulo_padre?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>">stampa modulo</a></div>
		    <? }
		}
		?>
		-->
	</div>
</div>

<!-- qui va il codice dinamico dei campi -->
<?
/*
if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){?>
	<!--<div class="titolo_pag"><h1>modulo creato da: <?=$operatore?> il: <?=$datains?> ore: <?=$orains?></h1></div>	
	    <?$txt_dati_creazione="Nome Modulo: <b>".strtoupper($nome_modulo)."</b>\ncreato da: $operatore il $datains alle ore $orains \n\n";?>
	<?}else{
	$txt_dati_creazione="";
	}*/
	
	
	?>
	<div class="titolo_pag"><h1>modulo creato da: <?=$operatore?> il: <?=$datains?> ore: <?=$orains?> data associata: <?=$scadenza_associata?>  </h1></div>	
	<?$txt_dati_creazione="<b>creato da:</b> $operatore <b>il:</b> $datains <b>alle ore:</b> $orains <b>data associata:</b> $scadenza_associata  \n\n";?>
<form id="myForm" method="post" name="form0" action="re_pazienti_sanitaria_POST.php">
	
	<div class="nomandatory">
		<input type="hidden" name="nomandatory" value="1" />
	</div>

	<input type="hidden" name="action" value="update_modulo" />
	<input type="hidden" name="idarea" value="<?=$idarea?>" />	
	<input type="hidden" name="idinserimento" value="<?=$idinserimento?>" />
	<input type="hidden" name="idmoduloversione" value="<?=$idmoduloversione?>" />
	
	
	<div class="blocco_centralcat">
		<?

		$i=1;
		$numerofield=100;
		$style='';
		$class='';
		$dis="";
		$i=1;
	    //$txt_dati_testata="1<".$nome_modulo.">\n";
		$txt_dati_valori="";
		while($row1 = mssql_fetch_assoc($rs1)){

			$idvalore=$row1['idvalore'];
			$idcampo= $row1['idcampo'];
			$etichetta= pulisci_lettura($row1['etichetta']);
			$tipo = $row1['tipo'];
			$editabile = $row1['editabile'];
			$obbligatorio = $row1['obbligatorio'];
			$classe=$row1['classe'];
			$valore=pulisci_lettura($row1['valore']);
		
			if ($obbligatorio=='y')
			$classe="mandatory ".$classe;
		
		$multi=0;
		if($tipo==4){
			$query3="select * from re_moduli_combo_propriety where idcampo=$idcampo";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3)
			{		
				echo("no");
				exit();
				die();
	}
			$row3 = mssql_fetch_assoc($rs3);
			$multi=$row3['multi'];
			mssql_free_result($rs3);
			}
        if($tipo==9){
			$query3="select * from re_moduli_combo_ramificate_propriety where idcampo=$idcampo";	
			$rs3 = mssql_query($query3, $conn);
			if(!$rs3)
			{		
				echo("no");
				exit();
				die();
	}
			$row3 = mssql_fetch_assoc($rs3);
			$multi_ram=$row3['multi'];
			mssql_free_result($rs3);
			}
	
			if(($i%3==1) and ($tipo!=5)){ 
				echo("<div class=\"riga\">");
				$div=1;
			}
			if((($tipo==2)or($multi==1)) and(($i%3==2)or($i%3==0))){
				echo("</div><div class=\"riga\">");
			}
			if(($tipo==5)and(($i%3==2)or($i%3==0))){
				echo("</div><div>");
			}
		if($multi==1) $stile="style=\"width:600px;\"";		
		?>
		
		<? if ($tipo==5) {?>
			<div class="rigo_big" style="float:left;">
			<div class="etichetta_campi"><?=pulisci_lettura($etichetta)?></div>
			<?
			$txt_dati_valori.="<".pulisci_lettura($etichetta).">\n\n";
			} else{?>
		<div class="rigo_mask <?php if(($tipo==2) or ($tipo==5)) echo("rigo_big");?>" <?php if($multi==1) echo($stile);?>>
            <div class="testo_mask"><?=pulisci_lettura($etichetta)?></div>            
			<div class="campo_mask ">
                <?
				$txt_dati_valori.="<b>".pulisci_lettura($etichetta)."</b>\n";
				if(($tipo==1)or($tipo==3)or($tipo==6)or($tipo==8)){
					echo(pulisci_lettura($valore));
					$txt_dati_valori.=pulisci_lettura($valore)."\n\n";
				  }
				elseif($tipo==2){
					// recupera il dato
                    /*
					$db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
					$res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
					$res = odbc_exec ($db, "SELECT valore FROM sgq_valori WHERE idvalore=$idvalore");
					odbc_longreadlen ($res, MAX);
					$text = odbc_result ($res, "valore");
					$text = str_replace("\n","<br>",$text);
					$valore = pulisci_lettura($text);
					odbc_close($db);
					echo($valore);		*/
					

                $query="select id_istanza_testata_sgq from istanze_testata_sgq WHERE (id_area = $idarea) AND (id_modulo=$id_modulo_padre) AND (id_inserimento=$idinserimento) ORDER BY id_inserimento, peso";
                    $rs2 = mssql_query($query, $conn);
                    while($row2 = mssql_fetch_assoc($rs2)){
                        $id_istanza_testata_sgq=$row2['id_istanza_testata_sgq'];
                        $db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
                        $res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
                        $res = odbc_exec ($db, "SELECT valore FROM istanze_dettaglio_sgq WHERE (id_campo=$idcampo) AND (id_istanza_testata_sgq=$id_istanza_testata_sgq)");
                        odbc_longreadlen ($res, MAX);
                        $text = odbc_result ($res, "valore");
                        $text = str_replace("\n","<br>",$text);
                        $valore = pulisci_lettura($text);
                        odbc_close($db);
                        echo($valore);	
						$txt_dati_valori.=($valore)."\n\n";
                    }				
				}
				  elseif($tipo==4)
					 {
						$query2="select * from re_moduli_combo where idcampo=$idcampo";					
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2)
						{		
				echo("no");
				exit();
				die();
	}
					 if(($multi==1)or($multi==2)){
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);
								$separatore=$row2['separatore'];
								switch ($separatore){
									case 0:
										$sep="<br/>";
										break;
									case 1:
										$sep=" - ";
										break;
									case 2:
										$sep=" , ";
										break;
								}
								if($multi==2){
									if (in_array($valore1,$valore)) 
										$et.="[X] ".$etichetta.$sep;
										else
										$et.="[&nbsp;&nbsp;] ".$etichetta.$sep;	
								}
								if($multi==1) if(in_array($valore1,$valore)) $et.=$etichetta.", ";									
							}
						$et=substr($et,0,strlen($et)-2);
						echo(pulisci_lettura($et));
						$txt_dati_valori.=pulisci_lettura($et)."\n\n";
					 }else{				
					while ($row2 = mssql_fetch_assoc($rs2))
							{								
								if ($row2['valore']==$valore){
								    echo($row2['etichetta']);
                                    $txt_dati_valori.=pulisci_lettura($row2['etichetta'])."\n";								
								}
							}
					$txt_dati_valori.="\n";	
					//$valore1=$row2['etichetta'];
					//echo($valore1);
				   }
                }
				elseif($tipo==7){
?>
		<div id="file_name" style="display:block;"><?if((trim($valore)!="")and(strpos($valore,"."))){?><a target="_new" href="load_file.php?filename=<?=$valore?>&ngid=<?=$_SESSION['UTENTE']->get_gid()?>" style="text-decoration:none;" ><img src="images/view.png" /> <?=$valore?></a><?}?>
		<input type="hidden" class="scrittura" name="<?=$idcampo?>_file" value="<?=$valore?>" >		
		</div>		
<?
		if(trim($valore)=="") $valore="non presente";
		$txt_dati_valori.=pulisci_lettura($valore)."\n\n";
}
                  elseif($tipo==9)
					 {
						$query2="select * from re_moduli_combo_ramificate where idcampo=$idcampo";				
                     //echo($query2);
					 //exit();						
						$rs2 = mssql_query($query2, $conn);
						
						if(!$rs2)
						{		
				echo("no");
				exit();
				die();
				
	}//echo("prima di multi");
	
					 if($multi_ram==1){
					 
				//	 echo("in multi");
	                 
						$et="";
						$valore=split(";",$valore);							
						while ($row2 = mssql_fetch_assoc($rs2))
							{
								$valore1=pulisci_lettura($row2['valore']);
								$etichetta=pulisci_lettura($row2['etichetta']);
								if (in_array($valore1,$valore)) $et.=$etichetta.", ";									
							}
						$et=substr($et,0,strlen($et)-2);
						echo($et);
						$txt_dati_valori.=pulisci_lettura($et)."\n\n";
					 }else{
					 
					//echo( "prima del while");
					
					
					while ($row2 = mssql_fetch_assoc($rs2))	{
				        
						$idc=$row2['idcombo'];
						$idcampocombo=$row2['idcampocombo'];
						
						//echo("idc");echo($idc);
						
						
						$valore1=pulisci_lettura($row2['valore']);
						$etichetta1=pulisci($row2['etichetta']);
						
						
						$stampa=$row2['stampa'];
						$v=(int)($valore);
						
						if(($v==$valore1) and($stampa)){ 
							//echo($etichetta1);
							$txt_dati_valori.=pulisci_lettura($etichetta1)."\n\n";
							}else{
							    $rinc=rincorri_figlio_per_stampa($idc,$idcampocombo,"",$v);
								$txt_dati_valori.=$rinc."\n";
							}
					}
					//$valore1=$row2['etichetta'];
					//echo($valore1);
				   }
				   
				  }?>
            </div>
			<?}?>
		</div>	
   
	<?
	if(($tipo==2)or($multi==1)) $i=0;
	if($i%3==0){ 
		echo("</div>");
		$div=0;}
	if($tipo!=5) $i++;	
	}	
	if($div==1) echo("</div>");				
	$content.=$txt_dati_creazione.$txt_dati_modulo.$txt_dati_valori;
	$content=str_replace("<br/>","\n",$content);
	$content=str_replace("<br","",$content);
	$handle = fopen($destination_path.$filename_tmp, "w");
	fwrite($handle,$content);
	?>
	
</div>
<?/*
if((DATA_INSERT==1) or((DATA_INSERT==0)and($_SESSION['UTENTE']->is_root()))){

	$query="SELECT  * FROM re_log_modifiche_istanze WHERE (idcartella = $idarea) AND (id_modulo_versione = $idmoduloversione) AND (istanza = $idinserimento) order by id desc";
	//echo($query);
	$rs = mssql_query($query, $conn);
	if(mssql_num_rows($rs)>0){
	?>
	<div class="blocco_centralcat">
		<div class="titolo_pag"><h1>cronologia modifiche:</h1></div>
		<?
		$i=1;
		while($row=mssql_fetch_assoc($rs)){
			?><div class="campo_mask "><?
			echo("$i - <strong>data</strong>: ".formatta_data($row['data_modifica'])." ".$row['ora_modifica']." - <strong>operatore</strong>: ".$row['operatore']);	
			$i++;
			?></div><?
		}
	?></div>
	<?}
}*/?>



<div class="titolo_pag">
		<div class="comandi">			
		</div>
	</div>
	</form>

</div>
<script type="text/javascript" id="js">

	function ritorna_istanze(idarea,idmoduloversione,divagg,conta_area_mod) //divagg
	{
		div='#'+divagg;
	$("#layer_nero2").toggle();
	$(div).innerHTML="";
	pagina_da_caricare="re_sgq.php?do=visualizza_lista_istanze_modulo&idarea="+idarea+"&idmodulo="+idmoduloversione+"&conta="+conta_area_mod;
	$(div).load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   $("#container-5 .tabs-selected").removeClass('tabs-selected');
	 });

	}
</script>

<?
}

function a($id)
{
$tablename="impegnative";
$conn = db_connect();



foreach ($_POST as $k => $v) {
   if (($k!="action") and ($k!="op") and ($k!="id") and ($k!="nomandatory") )
   {
     $campi=$k.'=';
	 $v=str_replace("'","''",$v);
	 $valori="'".addslashes($v)."'";
	 $upd.=$campi.$valori.",";
   }
  } 
	$lc=strlen($upd)-1;
	$upd=substr($upd, 0, $lc); // returns "d"




$query="update impegnative set ".$upd." where idimpegnativa=$id";
$result = mssql_query($query, $conn);

if(!$result) 
 echo ("no");
else 
 echo ("ok;".$idpaziente.";4;re_pazienti_sanitaria_cartelle.php?do=addvisita"); 
exit();

}

function update_modulo()
{
	$idarea=$_POST['idarea'];	
	$idmoduloversione=$_POST['idmoduloversione'];
	$idinserimento=$_POST['idinserimento'];
    $id_scadenza_da_eliminare=$_POST['id_scadenza_da_eliminare'];
    $id_data_scadenza_nuova=$_POST['seleziona_data_scadenza'];
    
   
    $conn = db_connect();
    
    $query="select idmodulo,nome from moduli where id=$idmoduloversione";
	$rs = mssql_query($query, $conn);
		
	if(!$rs)
	{		
        echo("no");
        exit();
        die();
	}

	if($row = mssql_fetch_assoc($rs))
	{
		$nome_modulo=pulisci_lettura($row['nome']);
		$idmodulo=$row['idmodulo'];
	}
    
    $query_id_ist="select id_istanza_testata_sgq from istanze_testata_sgq where id_inserimento=$idinserimento and id_area=$idarea and id_modulo=$idmodulo ";
	$rs_id_ist = mssql_query($query_id_ist, $conn);
	if(!$rs_id_ist)
	{		
        echo("no");
        exit();
        die();
	}
    if($row = mssql_fetch_assoc($rs_id_ist))
	{
		$id_istanza_testata_sgq=$row['id_istanza_testata_sgq'];
	}
	if($id_scadenza_da_eliminare!=""){
        "update scadenze_generate_sgq set id_istanza_testata_sgq=NULL where id=$id_scadenza_da_eliminare";
        $rs2 = mssql_query($query, $conn);
        if(!$rs2)
        {
            echo("no");
            exit();
            die();
        }
	}
    $query_scad_elim="update istanze_testata_sgq set scadenza_associata=NULL  where (id_istanza_testata_sgq=$id_istanza_testata_sgq)";
    $rs_scad_elim = mssql_query($query_scad_elim,$conn);
    
    if($id_data_scadenza_nuova!=""){
        $query="update scadenze_generate_sgq set id_istanza_testata_sgq='$id_istanza_testata_sgq'  where id='$id_data_scadenza_nuova'";
        $rs2 = mssql_query($query, $conn);
        if(!$rs2)
        {
            echo("no");
            exit();
            die();
        }
        $query_data="select * from scadenze_generate_sgq where id='$id_data_scadenza_nuova' ";
	    $rs_data = mssql_query($query_data, $conn);
        $row = mssql_fetch_assoc($rs_data );
        $scadenza_associata=formatta_data($row['data_scadenza_generata']);
    
        $query="update istanze_testata_sgq set scadenza_associata='$scadenza_associata'  where id_istanza_testata_sgq='$id_istanza_testata_sgq'";
        $rs2 = mssql_query($query, $conn);
    }
	
	//$query="select idcampo from sgq_valori where  idinserimento=$idinserimento and idarea=$idarea and idmodulo=$idmodulo order by peso asc";
	
	$dataagg = date('d/m/Y');
	$oraagg = date('H:i');
	$ipagg = getenv('REMOTE_ADDR');
	$opeagg = $_SESSION['UTENTE']->get_userid();
    
    $query="update istanze_testata_sgq set dataagg='$dataagg',oraagg='$oraagg',opeagg=$opeagg,ipagg='$ipagg' where id_istanza_testata_sgq=$id_istanza_testata_sgq";
    $rs2 = mssql_query($query, $conn);
    if(!$rs2)
    {
        echo("no");
        exit();
        die();
    }
    
    $query="select id_campo from istanze_dettaglio_sgq where  id_istanza_testata_sgq=$id_istanza_testata_sgq";
	$rs1 = mssql_query($query, $conn);
    
	while($row1 = mssql_fetch_assoc($rs1))
	{
        $idcampo= $row1['id_campo'];
		$query="select tipo,idcampo from campi where idcampo=$idcampo";
			$rs_c=mssql_query($query, $conn);
			$row_c=mssql_fetch_assoc($rs_c);
			$tipo=$row_c['tipo'];
        if (isset($_POST[$idcampo]))
        {
            $valore=$_POST[$idcampo];
            if (is_array($valore)){
                $val="";
                foreach ($valore as $key => $value) {
                    //echo "Hai selezionato la checkbox: $key con valore: $value<br />";
                    $val.=$value.";";
                }					
                $valore=substr($val,0,strlen($val)-1);
            }
            $valore=pulisci($valore);
			if($tipo!=7){	
				$query="update istanze_dettaglio_sgq set valore='$valore' where id_istanza_testata_sgq=$id_istanza_testata_sgq and id_campo=$idcampo";
				$rs2 = mssql_query($query, $conn);
				if(!$rs2)
				{
					echo("no");
					exit();
					die();
				}
			}
        }
		elseif(isset($_FILES[$idcampo])){
				$data=date(dmyHis);
				$nome_file=$_FILES[$idcampo]['name'];
				$nome_file=str_replace(" ","_",$nome_file);
				$type = $_FILES[$idcampo]['type'];
				$file = $idpaziente."_".$data."_".$nome_file;
				$valore=$file;		
				//******************				
				if ($_FILES[$idcampo]['name']!="") {	
				    //echo("cambiato allegato");
					//exit();
					$upload_dir = ALLEGATI_UTENTI;		// The directory for the images to be saved in
					$upload_path = $upload_dir."/";				// The path to where the image will be saved			
					$large_image_location = $upload_path.$file;
					$userfile_tmp = $_FILES[$idcampo]['tmp_name'];
					move_uploaded_file($userfile_tmp, $large_image_location);
										 
					/*update del dettaglio campo modificato*/
					$query="update istanze_dettaglio_sgq set valore='$valore' where id_campo=$idcampo and id_istanza_testata_sgq=$id_istanza_testata_sgq";
					$rs2 = mssql_query($query, $conn);
					/*fine update*/ 
				}
			}
	}
	mssql_free_result($rs1);
	echo ("ok;".$idarea.";5;re_sgq.php?do=visualizza_moduli");
    exit();
}



if(isset($_SESSION['UTENTE'])){
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {

			case "create_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_cartella($_POST['id']);					
			break;
			
			case "create_chiusura_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_chiusura_cartella();					
			break;
			
			case "create_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_modulo();					
			break;
			
			case "create_visita":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_visita();					
			break;
			
			case "create_pianificazione":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_pianificazione();					
			break;
			
			
			case "visualizza_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create_cartella();	
							
			break;
			

			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_POST['id']);
			break;
			
			case "update_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update_modulo();
			break;
            
            

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
		}

		
		switch($do) {
         
            case "del_istanza":
                // verifica i permessi..
                if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else del_istanza($_REQUEST['idarea'],$_REQUEST['idmodulo'],$_REQUEST['idinserimento']);//	visualizza_lista_istanze_modulo();
            break;

			
			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
			break;
			
			case "aggiungi_pianificazione":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_pianificazione();
			break;
			
			case "visualizza_moduli":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_moduli($_REQUEST['id']);
			break;
			
			case "chiudi_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else chiudi_cartella();
			break;
			
			case "visualizza_lista_istanze_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_lista_istanze_modulo();
			break;
			
			case "anteprima_istanze_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else anteprima_istanze_modulo();
			break;
			
			case "anteprima_istanze_cartella":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else anteprima_istanze_cartella();
			break;
			
			case "aggiungi_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_modulo();
			break;
			
			case "aggiungi_visita":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else aggiungi_visita();
			break;
			
			case "edita_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else edita_modulo();
			break;
			
			case "visualizza_istanza_modulo":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else visualizza_istanza_modulo();
			break;
			
			
			case "edit":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit($_REQUEST['id']);
			break;
			
			case "review":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else review($_REQUEST['id']);
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
	print("non hai i permessi per visualizzare questa pagina!");
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}

?>