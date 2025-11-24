<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 74;
$tablename = 'combo';
$mod_type=2;
include_once('include/function_page.php');

function show_list()
{
global $mod_type;


$conn = db_connect();
?>
<script>
function conf_cancella(id){
if (confirm('sei sicuro di voler cancellare la combo ramificata corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "ajax_combo_moduli_sgq.php?id=2&id_combo="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 if(msg==""){
				 $.ajax({
					   type: "POST",
					   url: "lista_combo_ramificatesgq_POST.php?action=del_combo&id_combo="+id,
					   data: "",
					   success: function(msg){
						 //alert( "Data Saved: " + msg );
						 reload_tipologie();
						}
					 });
			 }else{
				alert('Impossibile cancellare la combo ramificata in quanto presente all\'interno di un modulo');
				return false;	
			 }
			 
			}
		 });
	  }
	else return false;
}
function reload_tipologie(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_combo_ramificatesgq.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">combo ramificate SGQ</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_combo_ramificatesgq.php');" href="#">elenco combo ramificate SGQ</a></div>
          
        </div>

	<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_combo_ramificatesgq.php?do=add');">aggiungi combo ramificata</a></div>
		</div>
	</div>
<?
$query = "SELECT * from combo_ramificate WHERE (tipo=$mod_type) order by nome asc";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>elenco combo ramificate SGQ</h1>
	</div>
	<div class="info">Non esistono combo ramificate SGQ.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>id</th> 
	<th>nome</th> 
    <th>descrizione</th>
	<td width="5%">modifica</td>
	<td width="5%">cancella</td>
	
</tr> 
</thead> 
<tbody> 

<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{

while($row = mssql_fetch_assoc($rs))
{   
		$id=$row['id'];
		$nome=pulisci_lettura($row['nome']);
		$descrizione=pulisci_lettura($row['descrizione']);
		?>
		
		<tr> 
		 <td><?=$id?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$descrizione?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_combo_ramificatesgq.php?do=edit&id=<?=$id?>');" href="#"><img src="images/gear.png" /></a></td>
		 <td><a onclick="conf_cancella('<?=$id?>');" href="#"><img src="images/remove.png" /></a></td>
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

function add(){
global $mod_type;

?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
           <div class="elem_pari"><a onclick="javascript:load_content('div','lista_combosgq.php');" href="#">elenco combo ramificate SGQ</a></div>
			<div class="elem_dispari"><a href="#">aggiungi combo ramificata</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_combo_ramificatesgq_POST.php" id="myForm">
		<input type="hidden" name="action" value="create" />

	
	
	<div class="blocco_centralcat">
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">
				<span class="need">nome</span></div>
				<div class="mandatory campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="255" value="" style="width:690px;">
				</div>
			</div>
            
            <div class="rigo_mask">
              <!--  <div class="testo_mask">multivalore</div>-->
                <div class="campo_mask mandatory">
<input type="hidden" name="multi_valore" value="No" />                   				
                </div>
            </div>
        </div>
		
		<div class="riga">
				
			 <div class="rigo_mask">
                <div class="testo_mask">combo padre</div>
                <div id="combovalore" class="campo_mask">
                    
					<select id="idpadre" name="idpadre" onchange="javascript:carica_combo_valori(this.value);" >
				<? $conn = db_connect();
					print('<option value="">Selezionare una combo</option>'."\n");
					$query="SELECT id, nome, stato cancella FROM dbo.combo_ramificate WHERE (stato = 1) AND (cancella = 'n') and (tipo=$mod_type) order by nome ASC";
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$id_combo= $row['id'];
						echo($id_combo);
                        $id_nome = pulisci_lettura($row['nome']);		
						print('<option value="'.$id_combo.'"');     							
						print ('>'.$id_nome.'</option>'."\n");
					}	
				?>               
					</select>				
                </div>
            </div>
			<?//if()?>
			<div class="rigo_mask">
                <div class="testo_mask">carica la combo quando &egrave; selezionato il valore:</div>
				<div id="combovalori" class="nomandatory">
                    <input type="hidden"  name="idvalorepadre" value="" />
					<!--<select  name="idvalorepadre">
					<option value="">selezionare il valore </option>
                    <?
					
					/*$idcombo=35;
					$query="select * from campi_combo where (idcombo='$idcombo') and(cancella='n') and (stato=1)";
					$rs = mssql_query($query, $conn);
					
					$i=1;
					
					while($row1 = mssql_fetch_assoc($rs)){
					
						$idcampo= $row1['idcampo'];
						$etichetta= $row1['etichetta'];												
						print('<option value="'.$idcampo.'"');     							
						print ('>'.$etichetta.'</option>'."\n");
					
					}*/					
					?>				
                    </select>-->
                </div>
            </div>			
	    </div>
		<div class="rigo_mask">
            <div class="testo_mask">stampa valore</div>
            <div class="campo_mask">
				<select name="stampa">
                <option value="1" selected>si</option>
				<option value="0">no</option>
				</select>				
            </div>
        </div>
		
        <div class="riga">
            <div class="rigo_mask rigo_big">
                <div class="testo_mask">descrizione</div>
                <div class="campo_mask">
                    <textarea name="descr" class="scrittura_campo"></textarea>
                </div>
            </div>
		</div>        
	</div>

<div class="titolo_pag"><h1>valori della combo ramificata</h1></div>	
			
		<div class="blocco_centralcat">	

			
<script type="text/javascript">
/*$(document).ready(function() {
    // Initialise the table
    $("#elenco").tableDnD();
});*/




$(document).ready(function() {

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





</script>
			

<?
$d=1;
while ($d<2)
{
	if ($d==1)
		$st=$d;
	else
		$st.=",".$d;	
	$ordinamento=$st;
	$d++;
}
?>			
<input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">			

<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">

 <tbody id="tbody">
<tr class="riga_tab nodrag nodrop">
<td width="20%" align="left">&nbsp;</td>
<td width="5%" align="center">ordine</td>
<td width="20%" align="center">valore</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
<td width="45%">&nbsp;</td>
</tr>
<?
$i=1;
$numerofield=100;
$style='';
$class='';
$dis="";
while($i<$numerofield)
{
	
	if ($i>1)
	{
	$style="style=\"display:none\"";
	$class="riga_sottolineata_diversa hidden";
	$dis="disabled";
	
	}
	else
	{
	$nodrag="nodrag=false";
	$style='';
	$class="riga_sottolineata_diversa";
	$dis="";
	}
	?>
	<!--<tr id="<?=$i?>" <?=$style?> class="<?=$class?>"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('<?=$i?>','su');">su</a> - <a OnClick="javascript:DinamicDiv('<?=$i?>','giu');">giu</a></span></td>-->
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<td align="center">&nbsp;</td>
    <td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" style="width:500px;"/></td>	  
	<td>&nbsp;</td>
	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a></td>
	</tr>
<?
$i++;
}

?>

<tr id="1000" class="riga_sottolineata_diversa nodrag nodrop"><td align="center"><span><a OnClick="javascript:DinamicDiv('0','add','<?=$numerofield?>');"><img src="images/add.png" /></a></td></tr>
</tbody>
</table>			

	</div>
	
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>

	</form>
		
</div></div>
<?
//footer_new();

}





function edit($id)
{

$mod_type=2;
	$conn = db_connect();

	$query = "SELECT * from combo_ramificate WHERE id=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$id=$row['id'];
		$nome = pulisci_lettura($row['nome']);
		$descrizione = pulisci_lettura($row['descrizione']);
		$multi=$row['multi'];
		$idcombopadre=$row['idcombopadre'];
		$idvalorepadre=$row['idvalorepadre'];
		$stampa=$row['stampa'];
	}

	// rimuove i caratteri di escape
	//$nome = stripslashes($nome);
	//$descrizione = stripslashes($descrizione);

	mssql_free_result($rs);
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
         <div class="elem_pari"><a onclick="javascript:load_content('div','lista_combo_ramificatesgq.php');" href="#">elenco combo ramificate SGQ</a></div>
			 <div class="elem_dispari"><a href="#">modifica combo ramificata</a></div>
          
        </div>


<form method="post" name="form0" action="lista_combo_ramificatesgq_POST.php" id="myForm">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?=$id?>" />
	
	
	<div class="titolo_pag"><h1>Combo ramificata</h1></div>
	
		<div class="blocco_centralcat">
		<div class="riga">
			<div class="rigo_mask">
				<div class="testo_mask">
				<span class="need">nome</span></div>
				<div class="mandatory campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="255" value="<?=$nome?>" style="width:690px;">
				</div>
			</div>
            
            <div class="rigo_mask">
                <!--<div class="testo_mask">multivalore</div>-->
				<input type="hidden" value="No" name="multi_valore" />
               <!-- <div class="campo_mask mandatory">
                    <select name="multi_valore">
                    <option value="1" <?php if ($multi=='1') echo("selected");?>>Si</option>
                    <option value="0" <?php if ($multi=='0') echo("selected");?>>No</option>
                    </select>
                </div>-->
            </div>
        </div>
		
		
		<div class="riga">
				
			 <div class="rigo_mask">
                <div class="testo_mask">combo padre</div>
                <div class="campo_mask">
                    
					<select name="idpadre" onchange="javascript:carica_combo_valori(this.value);" >
				<? $conn = db_connect();
					print('<option value="">Selezionare una combo</option>'."\n");
					$query="SELECT id, nome, stato, cancella FROM dbo.combo_ramificate WHERE (stato = 1) AND (cancella = 'n') and (tipo=$mod_type)  order by nome ASC";					
					$rs1 = mssql_query($query, $conn);
					while($row = mssql_fetch_assoc($rs1))
					{ 
						$id_combo= $row['id'];
						$id_nome = pulisci_lettura($row['nome']);	

						$sel="";
						if ($id_combo==$idcombopadre)
						$sel="selected";
						
						?>
						<option value="<?=$id_combo?>" <?=$sel?>><?=$id_nome?></option>
						<?
						
					}	
				?>               
		</select>
	
                </div>
            </div>
			
			<div class="rigo_mask">
                <div class="testo_mask">carica la combo quando &egrave; selezionato il valore:</div>
                
				<div id="combovalori">
                    
					<select  name="idvalorepadre">
					<option value="">selezionare il valore </option>
                    <?
					
					
					$query="select * from campi_combo_ramificate where (idcombo='$idcombopadre') and(cancella='n') and (stato=1) ";
					$rs = mssql_query($query, $conn);
					
					$i=1;
					
					while($row1 = mssql_fetch_assoc($rs)){
					
						$idcampo= $row1['idcampo'];
						$etichetta= pulisci_lettura($row1['etichetta']);												
						
						$sel="";
						if ($idcampo==$idvalorepadre)
						$sel="selected";
						
						?>
						<option value="<?=$idcampo?>" <?=$sel?>><?=$etichetta?></option>
						<?
					}
					?>
                    </select>
                </div>
            </div>
		
		<div class="rigo_mask">
            <div class="testo_mask">stampa valore</div>
            <div class="campo_mask">
				<select name="stampa">
                <option value="1"<?if($stampa) echo("selected");?>>si</option>
				<option value="0"<?if(!$stampa) echo("selected");?>>no</option>
				</select>				
            </div>
        </div>
			
	    </div>
		
        <div class="riga">
            <div class="rigo_mask rigo_big">
                <div class="testo_mask">descrizione</div>
                <div class="campo_mask">
                    <textarea name="descr" class="scrittura_campo"><?=$descrizione?></textarea>
                </div>
            </div>
		</div>        
	</div>


<div class="titolo_pag"><h1>valori della combo ramificata</h1></div>	
			
		<div class="blocco_centralcat">	

			
<script type="text/javascript">
/*$(document).ready(function() {
    // Initialise the table
    $("#elenco").tableDnD();
});*/


$(document).ready(function() {


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





</script>
			

<?
$d=1;
while ($d<100)
{
	if ($d==1)
		$st=$d;
	else
		$st.=",".$d;
	
	$ordinamento=$st;
	
	$d++;

}
?>
			
<input id="debug" type="hidden" name="debug" value="<?=$ordinamento?>">	
			
<table id="elenco" style="clear:both" cellspacing="2" cellpadding="2" border="0">

 <tbody id="tbody">
<tr class="riga_tab nodrag nodrop">
<td width="20%" align="left">&nbsp;</td>
<td width="5%" align="center">ordine</td>
<td width="20%" align="center">valore</td>
<td width="5%">&nbsp;</td>
<td width="5%">elimina</td>
<td width="45%">&nbsp;</td>
<?

$query="select * from campi_combo_ramificate where (idcombo='$id') and(cancella='n') and (stato=1)";
$rs = mssql_query($query, $conn);

	
$i=1;
$numerofield=100;
while($row1 = mssql_fetch_assoc($rs)){

		$idcampo= $row1['idcampo'];
		$etichetta= pulisci_lettura($row1['etichetta']);			
		$class="riga_sottolineata_diversa";
?>	

	<!--<tr id="<?=$i?>" <?=$style?> class="<?=$class?>"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('<?=$i?>','su');">su</a> - <a OnClick="javascript:DinamicDiv('<?=$i?>','giu');">giu</a></span></td>-->
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<td align="center">&nbsp;</td>
    <td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" name="etichetta<?=$i?>" value="<?=$etichetta?>" style="width:500px;"/></td>	
	<td>&nbsp;</td>
    <input type="hidden" id="el<?=$i?>" name="elimina<?=$i?>" value="" />
	<input type="hidden" id="idcampo<?=$i?>" name="idcampo<?=$i?>" value="<?=$idcampo?>" />
	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');document.getElementById('el'+<?=$i?>).value='si';"><img src="images/remove.png" /></a></td>
	</tr>

<?
$i++;
}





while($i<$numerofield)
{
	
	if ($i>1)
	{
	$style="style=\"display:none\"";
	$class="riga_sottolineata_diversa hidden";
	$dis="disabled";
	
	}
	else
	{
	$nodrag="nodrag=false";
	$style='';
	$class="riga_sottolineata_diversa";
	$dis="";
	}
	?>

	

	<!--<tr id="<?=$i?>" <?=$style?> class="<?=$class?>"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('<?=$i?>','su');">su</a> - <a OnClick="javascript:DinamicDiv('<?=$i?>','giu');">giu</a></span></td>-->
	<tr id="<?=$i?>" <?=$style?> class="<?=$class?>" >
	<td align="center">&nbsp;</td>
    <td align="center"><span class="id_notizia_cat"></span></td>
	<td align="center" class="nodrag nodrop"><input id="et<?=$i?>" <?=$dis?> name="etichetta<?=$i?>" value="" style="width:500px;"/></td>	   
	<td>&nbsp;</td>
	<td><a OnClick="javascript:DinamicDiv('<?=$i?>','elimina','<?=$numerofield?>');"><img src="images/remove.png" /></a></td>
	</tr>


<?
$i++;
}



?>

<tr id="1000" class="riga_sottolineata_diversa nodrag nodrop"><td align="center"><span class="id_notizia_cat"><a OnClick="javascript:DinamicDiv('0','add','<?=$numerofield?>');" class="puntatore"><img src="images/add.png" /></a></td></tr>
</tbody>
</table>		

	</div>
	
    <div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>

	</form>
    
		
		
</div></div>
	
	
	
	<?


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

			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
			break;

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

