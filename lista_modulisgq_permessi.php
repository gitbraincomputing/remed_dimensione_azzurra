<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 50;
$tablename = 'regime';
$mod_type=2;
include_once('include/function_page.php');

function edit()
{
	global $mod_type;
	
$conn = db_connect();
$arr_moduli = array();
$arr_gruppi = array();
$query = "SELECT * from re_distinct_moduli WHERE (tipo=$mod_type)";	
$rs1 = mssql_query($query, $conn);

if(!$rs1) error_message(mssql_error());

$conta=mssql_num_rows($rs1);


while($row1 = mssql_fetch_assoc($rs1))   
{
	$idmodulo=$row1['idmodulo'];

	$query = "SELECT top 1 * from moduli where idmodulo=$idmodulo order by id desc";	
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());
	
	while($row = mssql_fetch_assoc($rs))
	{   

			$id=$row['idmodulo'];
			$nome=pulisci_lettura($row['nome']);
			$descrizione=$row['descrizione'];
			$arr_moduli[$nome] = $id;
	}
}	


$query = "SELECT  * from gruppi where (cancella='n') and (status='1') order by nome ";	
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());
	
	while($row = mssql_fetch_assoc($rs))
	{   

			$id=$row['gid'];
			$nome=$row['nome'];
			
			$arr_gruppi[$nome] = $id;
			//array_push( $arr_moduli, Array($nome => $id) );
			
	}
	
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




<script>inizializza();</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','groups.php');" href="#">gestione permessi</a></div>
			<div class="elem_dispari"><a href="#">aggiungi regime</a></div>
          
        </div>

<?
	$numero_moduli=sizeof($arr_moduli);
	$numero_domini = 10;
	$numero_settori = 13;
	$settori = array( "location per ricevimenti" => 2, "gioielli" => 3,  "trezza" => 13);
	$dominio = array( "networkmatrimonio.it" => 2, "networkmatrimonio.com" => 3, "networkmatrimonio.org" => 3);

?>
<form id="tutto" method="post" name="form0" action="lista_modulisgq_permessi_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>
	

<table style="clear:both;" class="sortable tablesorter"  cellspacing="1">
	<thead>
	<tr class="fissa">
    	<th class=" dim1">
        	<strong><a onclick="seleziona('tutto')" class="puntatore"><img src="images/lock-unlock.png" align="left" /> sel/desel tutto</a></strong>
        </th>
        <?
			$i=0;
			foreach( $arr_gruppi as $key=>$value){
		?>
        	<th class=" dim2">
            <?=$key?><br />
        	<a onclick="seleziona('settore<?=$value?>')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a>
            <a onclick="deseleziona('settore<?=$value?>')" class="puntatore"><img src="images/lock_bottom.png" align="left" /></a>
        	</th>
        <?
			$i++;
			}
		?>
        	
    </tr>
    </thead>
	<?
        $i=0;
        foreach( $arr_moduli as $key=>$value){
		
		if((($i%5)==0)and($i>0)){?>
			<thead>
			<tr class="fissa">
				<th class=" dim1">
					<strong><a onclick="seleziona('tutto')" class="puntatore"><img src="images/lock-unlock.png" align="left" /> sel/desel tutto</a></strong>
				</th>
				<?
					
					foreach( $arr_gruppi as $key1=>$value1){
				?>
					<th class=" dim2">
					<?=$key1?><br />
					<a onclick="seleziona('settore<?=$value1?>')" class="puntatore"><img src="images/unlock_bottom.png" align="left" /> </a>
					<a onclick="deseleziona('settore<?=$value1?>')" class="puntatore"><img src="images/lock_bottom.png" align="left" /></a>
					</th>
				<?
					
					}
				?>
					
			</tr>
			</thead>		
		<?}?>
    	<tr class="riga<?=$i%2?>">
        <td class="dim1">
        <?=$key?><br />
        <a onclick="seleziona('dominio<?=$value?>')" class="puntatore"><img src="images/unlock_small.png" align="left" /> </a>
        <a onclick="deseleziona('dominio<?=$value?>')" class="puntatore"><img src="images/lock_small.png" align="left" /> </a>
        <img src="images/greenarrow.png" align="left" style="padding-left:5px;" /> 
        </td>
        <?
			$j=0;
			foreach( $arr_gruppi as $key_set=>$value_set){
		?>
        	<td class="dim2">                       
            <?
				$query = "SELECT * from moduli_permessi where (cancella='n')and(stato=1)and(idgruppo=$value_set)and(idmodulo=$value) and tipo=0";	
				$rs = mssql_query($query, $conn);
				if($row=mssql_fetch_assoc($rs))
				{
					$valore=1;
				}
					else
					$valore=0;
							?>
        	 crea. <input type="checkbox" class="dominio<?=$value?> settore<?=$value_set?>" name="sel_<?=$value."_".$value_set?>_0" <? if($valore==1) echo("checked");?>/><br />
			
			<?
				$query = "SELECT * from moduli_permessi where (cancella='n')and(stato=1)and(idgruppo=$value_set)and(idmodulo=$value) and tipo=1";	
				$rs = mssql_query($query, $conn);
				if($row=mssql_fetch_assoc($rs))
				{
					$valore=1;
				}
					else
					$valore=0;
							?>
			
			
			visu. <input type="checkbox" class="dominio<?=$value?> settore<?=$value_set?>" name="sel_<?=$value."_".$value_set?>_1" <? if($valore==1) echo("checked");?>/>
        	</td>
        <?
			$j++;
			}
		?>
        	
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

</form>
</div>
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
					else edit();
			break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				edit();
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

