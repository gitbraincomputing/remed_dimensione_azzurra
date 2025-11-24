<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 11;
$tablename = 'gruppi';
include_once('include/function_page.php');

function show_list()
{

?>
<script>
function conf_cancella(id){
if (confirm('sei sicuro di voler cancellare la combo corrente?')){
	//document.getElementById('id_allegato').value=id;
	  $.ajax({
		   type: "POST",
		   url: "ajax_gruppi.php?id_gruppo="+id,
		   data: "",
		   success: function(msg){
			 //alert( "Data Saved: " + msg );
			 if($.trim(msg)==""){
				 $.ajax({
					   type: "POST",
					   url: "lista_gruppi_POST.php?action=del_gruppo&id_gruppo="+id,
					   data: "",
					   success: function(msg){
						 //alert( "Data Saved: " + msg );
						 reload_gruppi();
						}
					 });
			 }else{
				alert('Impossibile cancellare il gruppo in quanto risulta assegnato ad un operatore');
				return false;	
			 }
			 
			}
		 });
	  }
	else return false;
}

function reload_gruppi(){
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	pagina_da_caricare="lista_gruppi.php";
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione permessi</a></div>
            <div class="elem_pari"><a href="#">elenco gruppi</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_gruppi.php?do=add');">aggiungi gruppo</a></div>
		</div>
	</div>
<?
$conn = db_connect();

$query = "SELECT gid,nome,status,tipo,coordinatore FROM gruppi WHERE cancella='n' ORDER BY nome";

$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if($conta==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Gruppi</h1>
	</div>
	<div class="info">Non esistono gruppi.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th align="center" width="10%">stato</th>
    <th align="center" width="10%">id</th> 
	<th align="left" width="40%">gruppo</th> 
    <th align="left" width="20%">tipologia</th> 
	<th align="left" width="20%">coordinatore</th> 
    <td align="center" width="15%">modifica</td> 
	<td align="center" width="15%">elimina</td> 
</tr> 
</thead> 
<tbody> 
<?

if(!$rs) error_message(mssql_error());

while($row = mssql_fetch_assoc($rs))
{   

			$coord = $row['coordinatore'];
			$coord_nome="";
			if($coord!=""){
				$query="SELECT nome, uid FROM operatori WHERE uid=$coord";
				$rs_n=mssql_query($query,$conn);
				if($row_n=mssql_fetch_assoc($rs_n)) $coord_nome=$row_n['nome'];
			}
			$idgruppo = $row['gid'];
			$nome = $row['nome'];
			$status = $row['status'];
			if($status)
				$stato="stato_ok.png";
				else
				$stato="stato_blu.png";
			$tipo = trim($row['tipo']);			
			
			if($tipo==1) $tipo="amministrativo";
			if($tipo==2) $tipo="sanitario";
			if($tipo==3) $tipo="entrambi";	
			if($tipo==4) $tipo="amministrativo solo lettura";	
			if($tipo==5) $tipo="sanitario completo e amministrativo solo lettura";	
			
			// rimuove i caratteri di escape
			$nome = stripslashes($nome);
		?>
		
		<tr> 
		 <td align="center" width="10%"><img src="images/<?=$stato?>" title="stato"/></td> 
         <td align="center" width="10%"><?=$idgruppo?></td> 
		 <td align="left" width="40%"><?=$nome?></td> 
		 <td align="left" width="20%"><?=$tipo?></td> 
		 <td align="left" width="20%"><?=$coord_nome?></td> 
		 <td align="center" width="15%"><a id="<?=$idgruppo?>" onclick="javascript:load_content('div','lista_gruppi.php?do=edit&id=<?=$idgruppo?>');" href="#"><img src="images/gear.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="conf_cancella('<?=$idgruppo?>');" href="#"><img src="images/remove.png" /></a></td>
		</tr> 
		
		<?
//}
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
	
	  $("table").tablesorter({widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>

<?
//footer_new();

}

function add()
{

//$nome_pagina="gestione normative";
//header_new($nome_pagina);
//barra_top_new();
//top_message();
//menu_new();
//pagina_new();
?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_gruppi.php');" href="#">elenco gruppi</a></div>
			<div class="elem_dispari"><a href="#">aggiungi gruppo</a></div>
          
        </div>
<div id="tutto">
<form  method="post" name="form0" action="lista_gruppi_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Gruppo</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">Gruppo</div>
			<div class="campo_mask mandatory">
				<input type="text" name="gruppo" class="scrittura" size="50"/>
			</div>
		</div>
    </div>    
    <div class="riga">
        <div class="rigo_mask">
			<div class="testo_mask">descrizione</div>
			<div class="campo_mask mandatory">
				<textarea name="descrizione" class="scrittura_campo" size="50"></textarea>
			</div>
		</div>

	</div>    	
	
	
    <div class="riga">
        <div class="rigo_mask">
            <div class="testo_mask">tipologia</div>
            <div class="campo_mask">
                    <select name="tipo" class="scrittura">
                    <option value="1">amministrativo</option>
					<option value="4">amministrativo solo lettura</option>
                    <option value="2">sanitario</option>
					<option value="5">sanitario completo e amministrativo solo lettura</option>
				    <option value="3">entrambi</option>
                    </select>
            </div>
        </div>        
     </div>
	 
	 
    <div class="riga">
        <div class="rigo_mask">
            <div class="testo_mask">coordinatore</div>
            <div class="campo_mask">
                    <select name="coordinatore" class="scrittura">
					<option value="0">seleziona un coordinatore</option>
                    <?
						$conn = db_connect();
						$query="SELECT nome, uid FROM operatori WHERE cancella='n' and status=1";
						$rs_n=mssql_query($query,$conn);
						while($row_n=mssql_fetch_assoc($rs_n)){							
						?>
						<option value="<?=$row_n['uid']?>"><?=$row_n['nome']?></option>
						<?}?>
                    </select>
            </div>
        </div>        
     </div>
    
    
    <div class="riga">
        <div class="rigo_mask">
            <div class="testo_mask">stato</div>
            <div class="campo_mask">
                    <select name="stato" class="scrittura">
                    <option value="1">attivo</option>
                    <option value="0">disattivo</option>
                    </select>
            </div>
        </div>
     </div>
</div>
<?
	$conn = db_connect();
	$query = "SELECT * from menu where status='1' order by livello, ordine ";	
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());
	
	while($row = mssql_fetch_assoc($rs))
	{   

			$id=$row['id'];
			$nome=$row['nome'];			
			$arr_pagine[$nome] = $id;
			//array_push( $arr_moduli, Array($nome => $id) );
			
	}
	mssql_free_result($rs);

$arr_permessi=split(",",$permessi);

?>
<script type="text/javascript">
	var seleziona_tutti = 0;

	function seleziona(gruppo_campi){
		
		if(gruppo_campi=="tutto"){
			$("#"+gruppo_campi).find("input").each(function() {
				if(seleziona_tutti==0){
					$(this).attr("checked", "checked");
				}
				else{
					$(this).removeAttr("checked");
				}
			});	
			if(seleziona_tutti==0) seleziona_tutti=1
			else seleziona_tutti=0;
		}
		else{
			$("#tutto").find("."+gruppo_campi).each(function() {
					$(this).attr("checked", "checked");
			});
		}
	}
	
	function deseleziona(gruppo_campi){
		$("#tutto").find("."+gruppo_campi).each(function() {
			$(this).removeAttr("checked");
		});
	}

</script>
<table style="clear:both;" class="sortable tablesorter"  cellspacing="1">
	<thead>
	<tr class="fissa">
    	<th class=" dim1">
        	<strong><a onclick="seleziona('tutto')" class="puntatore"><img src="images/lock-unlock.png" align="left" /> sel/desel tutto</a></strong>
        </th>
       <!-- <th class=" dim2">
        accesso<br />
        <a onclick="seleziona('settore<?=$idgruppo?>')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a>
        <a onclick="deseleziona('settore<?=$idgruppo?>')" class="puntatore"><img src="images/lock_bottom.png" align="left" /></a>
        </th>-->		
		<th class=" dim2">
        lettura<br />
        <a onclick="seleziona('settore<?=$idgruppo?>')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a>
        <a onclick="deseleziona('settore<?=$idgruppo?>')" class="puntatore"><img src="images/lock_bottom.png" align="left" /></a>
        </th>
		<th class=" dim2">
        modifica<br />
        <a onclick="seleziona('settore_m<?=$idgruppo?>')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a>
        <a onclick="deseleziona('settore_m<?=$idgruppo?>')" class="puntatore"><img src="images/lock_bottom.png" align="left" /></a>
        </th>	
		
    </tr>
    </thead>

	<?
        $i=0;
        foreach( $arr_pagine as $key=>$value){
    ?>
    	<tr class="riga<?=$i%2?>">
        <td class="dim1">
        <?=$key?><br />
        <a onclick="seleziona('dominio<?=$value?>')" class="puntatore"><img src="images/unlock_small.png" align="left" /> </a>
        <a onclick="deseleziona('dominio<?=$value?>')" class="puntatore"><img src="images/lock_small.png" align="left" /> </a>
        <img src="images/greenarrow.png" align="left" style="padding-left:5px;" /> 
        </td>      
        	<!--<td class="dim2">
        	<input type="checkbox" class="dominio<?=$value?> settore<?=$idgruppo?>" name="sel_<?=$value?>" <? if($valore==1) echo("checked");?>/>
        	</td>-->
			<td class="dim2">
        	<input type="checkbox" class="dominio<?=$value?> settore<?=$idgruppo?>" name="sel_<?=$value?>" <? if($valore_l==1) echo("checked");?>/>
        	</td>
			<td class="dim2">
        	<input type="checkbox" class="dominio<?=$value?> settore_m<?=$idgruppo?>" name="sem_<?=$value?>" <? if($valore_m==1) echo("checked");?>/>
        	</td>					
        </tr>
    <?
        $i++;
        }
    ?>
    
    	
</table>	
    
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	
	</form></div>
    
		
		
</div>
<?
//footer_new();

}

function edit($id)
{

	$conn = db_connect();

	$query = "SELECT * FROM gruppi WHERE gid=$id";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$idgruppo = $row['gid'];
		$nome = $row['nome'];
		$descr = $row['descr'];
		$status = $row['status'];
		$coordinatore=$row['coordinatore'];
		//echo(trim($row['permessi']));
		//$permessi = $row['permessi'];
		$tipo = $row['tipo'];		
		
		// rimuove i caratteri di escape
		$nome = pulisci_lettura($nome);
	}

	// rimuove i caratteri di escape
	$normativa = pulisci_lettura($normativa);
	$regime = pulisci_lettura($regime);
	mssql_free_result($rs);
	
	$db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
	$res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
	$res = odbc_exec ($db, "SELECT permessi FROM gruppi WHERE gid=$id");
	odbc_longreadlen ($res, MAX);
	$permessi = odbc_result ($res, "permessi");		
	odbc_close($db);

	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
	<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_gruppi.php');" href="#">elenco gruppi</a></div>
			 <div class="elem_dispari"><a href="#">modifica gruppo</a></div>
          
        </div>

<div id="tutto">
<form  method="post" name="form0" action="lista_gruppi_POST.php" id="myForm">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?=$idgruppo?>" />

	<div class="titolo_pag"><h1>Modifica Gruppo</h1></div>

	<div class="blocco_centralcat">

	<div class="riga">
	    <div class="rigo_mask">
			<div class="testo_mask">Gruppo</div>
			<div class="campo_mask mandatory">
				<input type="text" name="gruppo" class="scrittura" size="50" value="<?=$nome?>"/>
			</div>
		</div>
    </div>
        
    <div class="riga">
        <div class="rigo_mask rigo_big">
			<div class="testo_mask">descrizione</div>
			<div class="campo_mask mandatory">
				<textarea name="descrizione" class="scrittura_campo" size="50"><?=$descr?></textarea>
			</div>
		</div>
	</div>
    
    <div class="riga">
        <div class="rigo_mask">
            <div class="testo_mask">tipologia</div>
            <div class="campo_mask">
                    <select name="tipo" class="scrittura">
                    <option value="1" <?php if($tipo=='1') echo('selected');?>>amministrativo</option>
					<option value="4" <?php if($tipo=='4') echo('selected');?>>amministrativo solo lettura</option>
                    <option value="2" <?php if($tipo=='2') echo('selected');?>>sanitario</option>
					<option value="5" <?php if($tipo=='5') echo('selected');?>>sanitario completo e amministrativo solo lettura</option>
					<option value="3" <?php if($tipo=='3') echo('selected');?>>entrambi</option>
                    </select>
            </div>
        </div>        
     </div>
     <div class="riga">
        <div class="rigo_mask">
            <div class="testo_mask">coordinatore</div>
            <div class="campo_mask">
                    <select name="coordinatore" class="scrittura">
					<option value="0">seleziona un coordinatore</option>
                    <?
						$query="SELECT nome, uid FROM operatori WHERE cancella='n' and status=1";
						$rs_n=mssql_query($query,$conn);
						while($row_n=mssql_fetch_assoc($rs_n)){							
						?>
						<option value="<?=$row_n['uid']?>" <?if($coordinatore==$row_n['uid']) echo("selected");?>><?=$row_n['nome']?></option>
						<?}?>
                    </select>
            </div>
        </div>        
     </div>
    
    <div class="riga">
        <div class="rigo_mask">
            <div class="testo_mask">stato</div>
            <div class="campo_mask">
                    <select name="stato" class="scrittura">
                    <option value="1" <?php if($status=='1') echo('selected');?>>attivo</option>
                    <option value="0" <?php if($status=='0') echo('selected');?>>disattivo</option>
                    </select>
            </div>
        </div>
     </div>
              
</div>
<?
$query = "SELECT * from menu where status='1' order by livello, ordine ";	
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());
	
	while($row = mssql_fetch_assoc($rs))
	{   

			$id=$row['id'];
			$nome=$row['nome'];			
			$arr_pagine[$nome] = $id;
			//array_push( $arr_moduli, Array($nome => $id) );
			
	}
	mssql_free_result($rs);

$arr_permessi=split(",",$permessi);

?>
<script type="text/javascript">
	var seleziona_tutti = 0;

	function seleziona(gruppo_campi){
		
		if(gruppo_campi=="tutto"){
			$("#"+gruppo_campi).find("input").each(function() {
				if(seleziona_tutti==0){
					$(this).attr("checked", "checked");
				}
				else{
					$(this).removeAttr("checked");
				}
			});	
			if(seleziona_tutti==0) seleziona_tutti=1
			else seleziona_tutti=0;
		}
		else{
			$("#tutto").find("."+gruppo_campi).each(function() {
					$(this).attr("checked", "checked");
			});
		}
	}
	
	function deseleziona(gruppo_campi){
		$("#tutto").find("."+gruppo_campi).each(function() {
			$(this).removeAttr("checked");
		});
	}

</script>
<table style="clear:both;" class="sortable tablesorter"  cellspacing="1">
	<thead>
	<tr class="fissa">
    	<th class=" dim1">
        	<strong><a onclick="seleziona('tutto')" class="puntatore"><img src="images/lock-unlock.png" align="left" /> sel/desel tutto</a></strong>
        </th>
        <th class=" dim2">
        lettura<br />
        <a onclick="seleziona('settore<?=$idgruppo?>')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a>
        <a onclick="deseleziona('settore<?=$idgruppo?>')" class="puntatore"><img src="images/lock_bottom.png" align="left" /></a>
        </th>
		<th class=" dim2">
        modifica<br />
        <a onclick="seleziona('settore<?=$idgruppo?>')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a>
        <a onclick="deseleziona('settore<?=$idgruppo?>')" class="puntatore"><img src="images/lock_bottom.png" align="left" /></a>
        </th> 		 		
    </tr>
    </thead>

	<?
        $i=0;
        foreach( $arr_pagine as $key=>$value){
    ?>
    	<tr class="riga<?=$i%2?>">
        <td class="dim1">
        <?=$key?><br />
        <a onclick="seleziona('dominio<?=$value?>')" class="puntatore"><img src="images/unlock_small.png" align="left" /> </a>
        <a onclick="deseleziona('dominio<?=$value?>')" class="puntatore"><img src="images/lock_small.png" align="left" /> </a>
        <img src="images/greenarrow.png" align="left" style="padding-left:5px;" /> 
        </td>
        <?			
			$valore_l=0;
			$valore_m=0;			
			foreach( $arr_permessi as $key_per=>$value_per){
				$controllo=split("-",$value_per);						
				if (($controllo[0]==trim($value))and(substr($controllo[1],0,1)=="y")) $valore_l=1;
				if (($controllo[0]==trim($value))and(substr($controllo[1],1,1)=="y")) $valore_m=1;	
			}
		?>
        	<td class="dim2">
        	<input type="checkbox" class="dominio<?=$value?> settore<?=$idgruppo?>" name="sel_<?=$value?>" <? if($valore_l==1) echo("checked");?>/>
        	</td>
			<td class="dim2">
        	<input type="checkbox" class="dominio<?=$value?> settore<?=$idgruppo?>" name="sem_<?=$value?>" <? if($valore_m==1) echo("checked");?>/>
        	</td>			
        </tr>
    <?
        $i++;
        }
    ?>
    
    	
</table>	
	<div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	
	</form></div>
		
</div>
	
	
	
	<?
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
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else create();
			break;

			case "update":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else update($_POST['id']);
			break;

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

