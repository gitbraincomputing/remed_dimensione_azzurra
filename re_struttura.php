<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 44;
$tablename = 'normative';
include_once('include/function_page.php');

function show_list()
{

$conn = db_connect();

?>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione permessi</a></div>
            <div class="elem_pari"><a href="#">elenco operatori</a></div>
          
        </div>


<div id="ricerca"></div>

<div class="titolo_pag">
		<div class="comandi">
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','re_struttura.php?do=add');">aggiungi struttura</a></div>
		</div>
	</div>

<?
$query = "SELECT * FROM dbo.struttura WHERE (dbo.struttura.cancella = 'n') ORDER BY dbo.struttura.nome";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());
if(mssql_num_rows($rs)==0){
	?>
	
	<div class="titoloalternativo">
        <h1>Elenco Strutture</h1>
	</div>
	<div class="info">Non esistono strutture.</div>
	
	
	<?
	exit();}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th width="10%">id</th> 
	<th width="10%">stato</th>	
	<th width="70%">nome</th>	
    <td width="10%">modifica</td>	
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idnormativa=$row1['idnormativa'];

while($row = mssql_fetch_assoc($rs))
{   	
	$id_str=$row['idstruttura'];
	$nome=$row['nome'];	
	$status = $row['status'];
	if($status){
	$stato="stato_ok.png";
	$alt_s="attivo";}
	else{
	$stato="stato_blu.png";
	$alt_s="disattivo";}
	// rimuove i caratteri di escape
	$nome = stripslashes($nome);	
	?>
	
	<tr> 
	 <td><?=$id_str?></td>
	 <td align="center" width="10%"><img src="images/<?=$stato?>" title="<?=$alt_s?>"/></td> 	  
	 <td><?=$nome?></td>	
	 <td><a id="<?=$id?>" onclick="javascript:load_content('div','re_struttura.php?do=edit&id=<?=$id_str?>');" href="#"><img src="images/gear.png" /></a></td> 	 
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
					<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','re_struttura.php?do=add');">aggiungi struttura</a></div>
         
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
	


</div></div>
<script>
$(document).ready(function() {
	
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>

<?
}

function add()
{

?>
<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
			<div class="elem_dispari"><a href="#">aggiungi operatore</a></div>
          
        </div>

<form  method="post" name="form0" action="re_struttura_POST.php" id="myForm" >
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Struttura</h1></div>

	<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="nome" SIZE="30" maxlenght="30" />
		</div>
	</span>
  </div>
    
    <div class="riga">
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">breve descrizione</div>
		<div class="campo_mask">
			<textarea name="descr" class="scrittura_campo" rows="4" cols="40"></textarea>
		</div>
	</span>
    </div>
	
	<div class="riga">
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">intestazione struttura <em>(per moduli)</em></div>
		<div class="campo_mask">
			<textarea name="intestazione" class="scrittura_campo" rows="4" cols="40"></textarea>
		</div>
	</span>
    </div>
	
	<div class="riga">
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">footer pagina struttura <em>(per moduli)</em></div>
		<div class="campo_mask">
			<textarea name="piede" class="scrittura_campo" rows="4" cols="40"></textarea>
		</div>
	</span>
    </div>

	<div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">email</div>
		<div class="campo_mask mandatory email">
			<input type="text" class="scrittura_campo" name="email" SIZE="100" maxlenght="100" />
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">sito web</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="sito_web" SIZE="100" maxlenght="100" />
		</div>
	</span>
  </div>
  
  <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">partita iva</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="piva" SIZE="30" maxlenght="30" />
		</div>
	</span>
  </div>
  <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">codice struttura</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="cod_struttura" SIZE="30" maxlenght="30" />
		</div>
	</span>
	 <span class="rigo_mask">
		<div class="testo_mask">regione struttura</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="reg_struttura" SIZE="30" maxlenght="30" />
		</div>
	</span>
	 <span class="rigo_mask">
		<div class="testo_mask">codice asl di riferimento</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="cod_asl_rif" SIZE="30" maxlenght="30" />
		</div>
	</span>
  </div>
  <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">codice distretto</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="distretto" SIZE="30" maxlenght="30" />
		</div>
	</span>
  </div>  
   
    <div class="riga">        
    <span class="rigo_mask">
        <div class="testo_mask">stato</div>
        <div class="campo_mask">
                <select name="status" class="scrittura">
                <option value="1">attivo</option>
                <option value="0">disattivo</option>
                </select>
        </div>
    </span>     
   </div>  
   
	</div>

	 <div class="titolo_pag">
	    <div class="comandi">
        	<h1>Sedi</h1>
        </div>	  
	</div>
	<input type="hidden" name="flag_sede" id="flag_sede" SIZE="30" maxlenght="30" value="false"/>
	
		<div class="blocco_centralcat" id="1">
		<div class="comunicazione"></div>
		<?php $index=1;?>
		<div class="riga"> 
		 <span class="rigo_mask">
			<div class="testo_mask">indirizzo</div>
			<div class="campo_mask mandatory">
				<input type="text" class="scrittura" name="indirizzo_sede<?=$index?>" SIZE="30" maxlenght="30" />
			</div>
		</span>
		<span class="rigo_mask">
			<div class="testo_mask">provincia <a onclick="carica_province_sede(document.getElementById('prov_sede<?=$index?>').value,<?=$index?>)" class="cambia_valore">cambia provincia</a></div>
			<div class="campo_mask mandatory" id="province_res<?=$index?>">
				<input type="text" class="scrittura readonly" name="prov_sede<?=$index?>" id="prov_sede<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($nome_prov);?>" readonly/>
				
			</div>
		</span>
		<span class="rigo_mask">
			<div class="testo_mask">Comune <a onclick="carica_comuni_sede(document.getElementById('prov_sede<?=$index?>').value,<?=$index?>,document.getElementById('comune_sede<?=$index?>').value)" class="cambia_valore">cambia comune</a></div>
			<div class="campo_mask mandatory" id="comuni_res<?=$index?>">
			 <input type="text" class="scrittura readonly" name="comune_sede<?=$index?>" id="comune_sede<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['comune']);?>" readonly/>				  
			</div>
		</span>
		</div>
		<div class="riga"> 
		<span class="rigo_mask">
			<div class="testo_mask">CAP</div>
			<div class="campo_mask">
				<div id="cap_res<?=$index?>">
				<input type="text" class="scrittura" name="cap_sede<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['cap']);?>"/>
				</div>
			</div>
		</span>
		<span class="rigo_mask">
			<div class="testo_mask">telefono</div>
			<div class="campo_mask mandatory">
				<input type="text" class="scrittura" name="telefono<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['tel']);?>"/>
			</div>
		</span>
		<span class="rigo_mask">
			<div class="testo_mask">fax</div>
			<div class="campo_mask ">
				<input type="text" class="scrittura" name="fax<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['fax']);?>"/>
			</div>
		</span>
	</div>
	</div>
	
	
	<div class="titolo_pag">
		<div class="comandi">
			<input onclick="javascript: return controlla_campi_operatori('edit');" id="salva_operatore" type="submit" title="salva" value="salva"  class="button_salva"/>
		</div>
	</div>
	
	</form>
    
		
		
</div>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>
<?
//footer_new();

}


function edit($id)
{
	$idstruttura=$id;
	$conn = db_connect();
	$query = "SELECT * FROM dbo.struttura WHERE (dbo.struttura.idstruttura=$id)";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$nome = utf8_encode($row['nome']);
		$descr = utf8_encode($row['descrizione']);
		$int_str = utf8_encode($row['intestazione']);
		$piede_str = utf8_encode($row['piede']);
		$email = $row['email'];		
		$sitoweb = $row['sitoweb'];		
		$pi = $row['pi'];		
		$status = $row['status'];
		$cod_struttura = $row['codice_struttura'];
		$reg_struttura = $row['regione_struttura'];
		$cod_asl_rif = $row['cod_asl_rif'];
		$distretto = $row['distretto'];
		$logo = $row['logo'];
	}
	mssql_free_result($rs);
	// rimuove i caratteri di escape		
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
			 <div class="elem_dispari"><a href="#">modifica operatore</a></div>
          
        </div>


<form  method="post" name="operatori" action="re_struttura_POST.php" id="myForm"  >
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="idstruttura" id="id" value="<?=$id?>" />
	<input type="hidden" name="cambia_mod" value="no" id="cambia_mod"/>

	<div class="titolo_pag"><h1>Modifica Struttura</h1></div>

		<div class="blocco_centralcat">

    <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="nome" SIZE="30" maxlenght="30" value="<?=$nome?>" />
		</div>
	</span>
  </div>
    
    <div class="riga">
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">breve descrizione</div>
		<div class="campo_mask">
			<textarea name="descr" class="scrittura_campo" rows="4" cols="40"><?=$descr?></textarea>
		</div>
	</span>
    </div>
	
	<div class="riga">
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">intestazione struttura per <em>(per moduli)</em></div>
		<div class="campo_mask">
			<textarea name="intestazione" class="scrittura_campo" rows="4" cols="40"><?=$int_str?></textarea>
		</div>
	</span>
    </div>
	
	<div class="riga">
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">footer di pagina struttura <em>(per moduli)</em></div>
		<div class="campo_mask">
			<textarea name="piede" class="scrittura_campo" rows="4" cols="40"><?=$piede_str?></textarea>
		</div>
	</span>
    </div>

	<div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">email</div>
		<div class="campo_mask mandatory email">
			<input type="text" class="scrittura_campo" name="email" SIZE="100" maxlenght="100" value="<?=$email?>"/>
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">sito web</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="sito_web" SIZE="100" maxlenght="100" value="<?=$sitoweb?>"/>
		</div>
	</span>
  </div>
  
  <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">partita iva</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="piva" SIZE="30" maxlenght="30" value="<?=$pi?>"/>
		</div>
	</span>
  </div>
    <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">codice struttura</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="cod_struttura" SIZE="30" maxlenght="30" value="<?=$cod_struttura?>"/>
		</div>
	</span>
	 <span class="rigo_mask">
		<div class="testo_mask">regione struttura</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="reg_struttura" SIZE="30" maxlenght="30" value="<?=$reg_struttura?>"/>
		</div>
	</span>
	 <span class="rigo_mask">
		<div class="testo_mask">codice asl di riferimento</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="cod_asl_rif" SIZE="30" maxlenght="30" value="<?=$cod_asl_rif?>"/>
		</div>
	</span>
  </div>
  <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">codice distretto</div>
		<div class="campo_mask ">
			<input type="text" class="scrittura_campo" name="distretto" SIZE="30" maxlenght="30" value="<?=$distretto?>"/>
		</div>
	</span>
  </div>
  <div class="riga">        
    <span class="rigo_mask">
       <div class="rigo_mask">
			<div class="testo_mask">logo struttura <a onclick="document.getElementById('cambia_mod').value='si';document.getElementById('file_box').style.display='block';document.getElementById('file_name').style.display='none';" class="cambia_valore">cambia logo</a></div>
			<div id="file_name" style="display:block;">
			 <? if($logo!=""){?>
			<a href="images/<?=$logo?>" target="_blank"><?=$logo?></a>
			<?}?>
			</div>	
			<div id="file_box" class="campo_mask nomandatory" style="display:none">
				<input type="file" name="logo" id="logo" />
			</div>
		</div>
    </span>     
   </div>   
   
    <div class="riga">        
    <span class="rigo_mask">
        <div class="testo_mask">stato</div>
        <div class="campo_mask">
                <select name="status" class="scrittura">
                 <option value="1" <?php if($status) echo "selected"; ?>>attivo</option>
				<option value="0" <?php if(!$status) echo "selected"; ?>>disattivo</option>
                </select>
        </div>
    </span>     
   </div> 
   </div>
   
   <div class="titolo_pag">
	    <div class="comandi">
        	<h1>Sedi</h1>
        </div>
		<div class="comandi">
			<div id="aggiungi_residenza" class="aggiungi" onclick="aggiungi_nuovo('sede')"><a>aggiungi Sede</a></div>
		</div>       
	</div>
	<input type="hidden" name="flag_sede" id="flag_sede" SIZE="30" maxlenght="30" value="false"/>
	<?php
		$query = "SELECT * FROM strutturasedi WHERE (idstruttura = $id) order by strutturasedi.idsede asc";
		$rs = mssql_query($query, $conn);
		$numrow=mssql_num_rows($rs);
		$i=1;		
		while($row=mssql_fetch_assoc($rs)){			
			$index=$row['idsede'];
		?>		
		<input type="hidden" name="flag_sede<?=$index?>" value="<?=$index?>"/>
		<?php $sede=""; 
			  if ($i=1) $sede="sede";?>
		<div class="blocco_centralcat" id="<?=$sede?>">
		<div class="comunicazione"></div>
		<div class="riga"> 
		 <span class="rigo_mask">
			<div class="testo_mask">indirizzo</div>
			<div class="campo_mask mandatory">
				<input type="text" class="scrittura" name="indirizzo_sede<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['indirizzo']);?>"/>
			</div>
		</span>
		<?php
		if (!(is_numeric($row['provincia']))) {
			$nome_prov=$row['provincia'];}
			else{
			$query = "SELECT id, nome FROM dbo.province WHERE id=".$row['provincia'];	
			$rs1 = mssql_query($query, $conn);
			$row_1=mssql_fetch_assoc($rs1);
			$nome_prov=$row_1['nome'];
			mssql_free_result($rs1);
			}
		
		?>
		<span class="rigo_mask">
			<div class="testo_mask">provincia <a onclick="carica_province_sede(document.getElementById('prov_sede<?=$index?>').value,<?=$index?>)" class="cambia_valore">cambia provincia</a></div>
			<div class="campo_mask mandatory" id="province_res<?=$index?>">
				<input type="text" class="scrittura readonly" name="prov_sede<?=$index?>" id="prov_sede<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($nome_prov);?>" readonly/>
				
			</div>
		</span>
		<span class="rigo_mask">
			<div class="testo_mask">Comune <a onclick="carica_comuni_sede(document.getElementById('prov_sede<?=$index?>').value,<?=$index?>,document.getElementById('comune_sede<?=$index?>').value)" class="cambia_valore">cambia comune</a></div>
			<div class="campo_mask mandatory" id="comuni_res<?=$index?>">
			 <input type="text" class="scrittura readonly" name="comune_sede<?=$index?>" id="comune_sede<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['comune']);?>" readonly/>				  
			</div>
		</span>
		</div>
		<div class="riga"> 
		<span class="rigo_mask">
			<div class="testo_mask">CAP</div>
			<div class="campo_mask">
				<div id="cap_res<?=$index?>">
				<input type="text" class="scrittura" name="cap_sede<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['cap']);?>"/>
				</div>
			</div>
		</span>
		<span class="rigo_mask">
			<div class="testo_mask">telefono</div>
			<div class="campo_mask mandatory">
				<input type="text" class="scrittura" name="telefono<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['tel']);?>"/>
			</div>
		</span>
		<span class="rigo_mask">
			<div class="testo_mask">fax</div>
			<div class="campo_mask ">
				<input type="text" class="scrittura" name="fax<?=$index?>" SIZE="30" maxlenght="30" value="<?php echo($row['fax']);?>"/>
			</div>
		</span>
	</div>
	</div>
	
	<?php }?>
	
	
	<div class="titolo_pag">
		<div class="comandi">
			<input onclick="javascript: return controlla_campi_operatori('edit');" id="salva_operatore" type="submit" title="salva" value="salva"  class="button_salva"/>
		</div>
	</div>
	
	</form>
    
		
		
</div>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>	
	
	
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

