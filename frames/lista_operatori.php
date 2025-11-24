<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 11;
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
			<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_operatori.php?do=add');">aggiungi operatore</a></div>
		</div>
	</div>

<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>     
    <th>id</th> 
	<th>nome e cognome</th> 
    <th>gruppo</th>
    <th>tipologia</th>
    <th>modifica</th> 
</tr> 
</thead> 
<tbody> 
<?
//while($row1 = mssql_fetch_assoc($rs1))   
//{
//$idnormativa=$row1['idnormativa'];

$query = "SELECT dbo.operatori.uid, dbo.operatori.gid, dbo.operatori.nome, dbo.operatori.status, dbo.operatori.cancella, dbo.gruppi.nome AS gr_nome, dbo.gruppi.tipo as gr_tipo FROM dbo.operatori INNER JOIN dbo.gruppi ON dbo.operatori.gid = dbo.gruppi.gid WHERE (dbo.operatori.status = 1) AND (dbo.operatori.cancella = 'n') ORDER BY dbo.operatori.nome";	
$rs = mssql_query($query, $conn);
$conta=mssql_num_rows($rs);
if(!$rs) error_message(mssql_error());

while($row = mssql_fetch_assoc($rs))
{   
	$id_ope=$row['uid'];
	$nome=$row['nome'];
	$gr_nome=$row['gr_nome'];
	$gr_tipo=$row['gr_tipo'];
	if($gr_tipo==1) $gr_tipo="amministrativo";
	if($gr_tipo==2) $gr_tipo="sanitario";
	if($gr_tipo==3) $gr_tipo="entrambi";			
	// rimuove i caratteri di escape
	$nome = stripslashes($nome);
	$gr_nome = stripslashes($gr_nome);
	?>
	
	<tr> 
	 <td><?=$id_ope?></td> 
	 <td><?=$nome?></td>
	 <td><?=$gr_nome?></td> 
     <td><?=$gr_tipo?></td> 
	 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_operatori.php?do=edit&id=<?=$id_ope?>');" href="#"><img src="images/gear.png" /></a></td> 
	 
	</tr> 
	
	
	<?
//}
}	

?>


</tbody> 
</table> 
<? 
$numero_pager=(int)($conta/20)

?>

<div class="titolo_pag">
		<div class="comandi">
					<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_operatori.php?do=add');">aggiungi operatore</a></div>
         
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

<form  method="post" name="form0" action="lista_operatori_POST.php" id="myForm">
	<input type="hidden" value="create" name="action"/>
	<input type="hidden" value="1" name="op"/>

	<div class="titolo_pag"><h1>Aggiungi Operatore</h1></div>

	<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">nome e cognome</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="nome" SIZE="30" maxlenght="30" />
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">email</div>
		<div class="campo_mask mandatory email">
			<input type="text" class="scrittura_campo" name="email" SIZE="30" maxlenght="30" />
		</div>
	</span>
	</div>
    
    <div class="riga"> 
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">breve descrizione</div>
		<div class="campo_mask">
			<textarea name="descr" class="scrittura" rows="4" cols="40"></textarea>
		</div>
	</span>
    </div>

    <div class="riga"> 
    <span class="rigo_mask">
        <div class="testo_mask">username</div>
        <div class="campo_mask mandatory">
            <input type="text" class="scrittura" name="username" SIZE="30" maxlenght="30" />
        </div>
    </span>

    <span class="rigo_mask">
        <div class="testo_mask">password</div>
        <div class="campo_mask mandatory">
            <input type="password" class="scrittura" name="password" SIZE="30" maxlenght="30" />
        </div>
    </span>

    <span class="rigo_mask">
        <div class="testo_mask">conferma password</div>
        <div class="campo_mask mandatory">
            <input type="password" class="scrittura" name="rpassword" SIZE="30" maxlenght="30" />
        </div>
    </span>
	</div>
    
    <div class="riga">
    <span class="rigo_mask">
        <div class="testo_mask">Direttore sanitario</div>
        <div class="campo_mask">
            <input type="radio" name="dir_sanitario" value="y" onclick="javascript:$('#sanitario').slideDown();" /> si 
            <input type="radio" name="dir_sanitario" value="n" onclick="javascript:$('#sanitario').slideUp();" checked /> no
        </div>
    </span>
        <div id="sanitario" style="display:none;">
             <span class="rigo_mask">
                <div class="testo_mask">dal</div>
                <div class="campo_mask nomandatory ">
                    <input type="text" class="campo_data scrittura"  name="san_inizio" id="san_inizio" />			
                </div>
            </span>
             <span class="rigo_mask">
                <div class="testo_mask">al</div>
                <div class="campo_mask nomandatory ">
                    <input type="text" class="campo_data scrittura"  name="san_fine" id="san_fine" />			
                </div>
            </span>
        </div> 
    </div>
    <div class="riga">       	
    <span class="rigo_mask">
        <div class="testo_mask">Direttore Tecnico</div>
        <div class="campo_mask">
            <input type="radio" name="dir_tecnico" value="y" onclick="javascript:$('#tecnico').slideDown();"/> si 
            <input type="radio" name="dir_tecnico" value="n" onclick="javascript:$('#tecnico').slideUp();" checked /> no
        </div>
    </span>
        <div id="tecnico" style="display:none;">
             <span class="rigo_mask">
                <div class="testo_mask">dal</div>
                <div class="campo_mask nomandatory ">
                    <input type="text" class="campo_data scrittura"  name="tec_inizio" id="tec_inizio" />			
                </div>
            </span>
             <span class="rigo_mask">
                <div class="testo_mask">al</div>
                <div class="campo_mask nomandatory ">
                    <input type="text" class="campo_data scrittura"  name="tec_fine" id="tec_fine"/>			
                </div>
            </span>
        </div>  
	</div>
    
    <div class="riga">
    <span class="rigo_mask">
        <div class="testo_mask">gruppo</div>
        <div class="campo_mask">
                <?php
                $conn = db_connect();

                $query = "SELECT gid,nome FROM gruppi WHERE cancella='n' ORDER BY nome";
                $rs = mssql_query($query, $conn);

                if(mssql_num_rows($rs)) {
                
                    print('<select name="gid" class="scrittura">'."\n");
                    while( $row = mssql_fetch_assoc($rs)) {
    
                        $gid = $row['gid'];
                        $nome_gruppo = $row['nome'];
    
                        print ('<option value="'.$gid.'">'.$nome_gruppo.'</option>');
                    }
                    print('</select>'."\n");
                
                } else print('nessun gruppo presente');
                ?>
                <!-- <input type="submit" name="action" value="cambia gruppo" disabled /> -->
        </div>
    </span>
    
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
			<input type="submit" title="salva" value="salva" class="button_salva"/>
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

	$idoperatore=$id;
	$conn = db_connect();
	$query = "SELECT dbo.operatori.uid, dbo.operatori.gid, dbo.operatori.nome, dbo.operatori.status, dbo.operatori.cancella, dbo.gruppi.nome AS gr_nome, dbo.gruppi.tipo AS gr_tipo, dbo.operatori.descr, dbo.operatori.username, dbo.operatori.password, dbo.operatori.email, dbo.operatori.is_root, dbo.operatori.dir_sanitario, dbo.operatori.dir_tecnico FROM dbo.operatori INNER JOIN dbo.gruppi ON dbo.operatori.gid = dbo.gruppi.gid WHERE (dbo.operatori.status = 1) AND (dbo.operatori.cancella = 'n') AND (dbo.operatori.uid=$id)";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$nome = $row['nome'];
		$descr = $row['descr'];
		$email = $row['email'];
		$usr = $row['username'];
		$gid = $row['gid'];		
		$status = $row['status'];
		$dir_san=$row['dir_sanitario'];
		$dir_tec=$row['dir_tecnico'];
		
		
	}
	mssql_free_result($rs);
	if ($dir_san=='y'){
		$query="SELECT * from operatori_direttore WHERE WHERE ((uid='$id') and (tipo='1'))";
		$rs = mssql_query($query, $conn);
		if($row = mssql_fetch_assoc($rs)){
			$san_inizio=row['data_inizio'];
			$san_fine=row['data_fine'];
		}
		mssql_free_result($rs);
	}
	if ($dir_tec=='y'){
		$query="SELECT * from operatori_direttore WHERE WHERE ((uid='$id') and (tipo='2'))";
		$rs = mssql_query($query, $conn);
		if($row = mssql_fetch_assoc($rs)){
			$tec_inizio=row['data_inizio'];
			$tec_fine=row['data_fine'];
		}
		mssql_free_result($rs);
	}

	// rimuove i caratteri di escape
	$normativa = stripslashes($normativa);
	$descrizione = stripslashes($descrizione);

	
	?>
	<script>inizializza();</script>
	<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

		<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="principale.php">home</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_operatori.php');" href="#">elenco operatori</a></div>
			 <div class="elem_dispari"><a href="#">modifica operatore</a></div>
          
        </div>


<form  method="post" name="form0" action="lista_operatori_POST.php" id="myForm" onsubmit="return:controlla_campi();">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="2" />
	<input type="hidden" name="id" value="<?=$idoperatore ?>" />

	<div class="titolo_pag"><h1>Modifica Operatore</h1></div>

		<div class="blocco_centralcat">

   <div class="riga"> 
   <span class="rigo_mask">
		<div class="testo_mask">nome e cognome</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="nome" value="<?php echo $nome ?>" />
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">email</div>
		<div class="campo_mask mandatory">
			<input class="scrittura_campo" type="text" name="email" value="<?php echo $email ?>" />
		</div>
	</span>
	</div>
    
    <div class="riga"> 
	<span class="rigo_mask rigo_big">
		<div class="testo_mask">breve descrizione</div>
		<div class="campo_mask">
			<textarea name="descr" class="scrittura" rows="4" cols="40"><?php echo $descr ?></textarea>
		</div>
	</span>
    </div>

    <div class="riga"> 
    <span class="rigo_mask">
        <div class="testo_mask">username</div>
        <div class="campo_mask mandatory">
            <input class="lettura" type="text" name="usr" value="<?php echo $usr; ?>" readonly />
        </div>
    </span>

    <span class="rigo_mask">
        <div class="testo_mask">password</div>
        <div class="campo_mask mandatory">
            <a href="password.php?do=edit_pwd&amp;id=<?php echo $id ?>">cambia la password</a>
        </div>
    </span>
   	</div>
    
    <div class="riga">
        <span class="rigo_mask">
            <div class="testo_mask">Direttore sanitario</div>
            <div class="campo_mask">
                <input type="radio" name="dir_sanitario" value="y" onclick="javascript:$('#sanitario').slideDown();" <?php if($dir_san=='y') echo "checked"; ?>	/> si 
                <input type="radio" name="dir_sanitario" value="n" onclick="javascript:$('#sanitario').slideUp();" <?php if($dir_san=='n') echo "checked"; ?>	/> no
            </div>
        </span>
        <?php if($dir_san=='y')?>
        	<div id="sanitario" style="display:block;">
            <? else ?>	
        	<div id="sanitario" style="display:none;">
             <span class="rigo_mask">
                <div class="testo_mask">dal</div>
                <div class="campo_mask nomandatory ">
                    <input type="text" class="campo_data scrittura"  name="san_inizio" id="san_inizio" value="<?=$san_inizio?>" />			
                </div>
             </span>
             <span class="rigo_mask">
                <div class="testo_mask">al</div>
                <div class="campo_mask nomandatory ">
                    <input type="text" class="campo_data scrittura"  name="san_fine" id="san_fine" value="<?=$san_fine?>"/>			
                </div>
            </span>
        </div> 
    </div>
    <div class="riga">       	
    <span class="rigo_mask">
        <div class="testo_mask">Direttore Tecnico</div>
        <div class="campo_mask">
            <input type="radio" name="dir_tecnico" value="y" onclick="javascript:$('#tecnico').slideDown();" <?php if($dir_tec=='y') echo "checked"; ?>/> si 
            <input type="radio" name="dir_tecnico" value="n" onclick="javascript:$('#tecnico').slideUp();" <?php if($dir_tec=='n') echo "checked"; ?> /> no
        </div>
    </span>
    <?php if($dir_tec=='y')?>
        <div id="tecnico" style="display:block;">
        <? else ?>
        <div id="tecnico" style="display:none;">
             <span class="rigo_mask">
                <div class="testo_mask">dal</div>
                <div class="campo_mask nomandatory ">
                    <input type="text" class="campo_data scrittura"  name="tec_inizio" id="tec_inizio" value="<?=$tec_inizio?>"/>			
                </div>
            </span>
             <span class="rigo_mask">
                <div class="testo_mask">al</div>
                <div class="campo_mask nomandatory ">
                    <input type="text" class="campo_data scrittura"  name="tec_fine" id="tec_fine" value="<?=$tec_inizio?>"/>			
                </div>
            </span>
        </div>  
	</div>
    
    <div class="riga">
    <span class="rigo_mask">
        <div class="testo_mask">gruppo</div>
        <div class="campo_mask">
                <?php
                $conn = db_connect();

                $query = "SELECT gid,nome FROM gruppi WHERE cancella='n' ORDER BY nome";
                $rs = mssql_query($query, $conn);

                if(mssql_num_rows($rs)) {
                
                    print('<select name="gid" class="scrittura">'."\n");
                    while( $row = mssql_fetch_assoc($rs)) {
    
                        $gid = $row['gid'];
                        $nome_gruppo = $row['nome'];
    
                        print ('<option value="'.$gid.'"');
						if($gid == $id_gruppo) echo " selected";
							print ('>'.$nome_gruppo.'</option>');
                    }
                    print('</select>'."\n");
                
                } else print('nessun gruppo presente');
                ?>
                <!-- <input type="submit" name="action" value="cambia gruppo" disabled /> -->
        </div>
    </span>
        
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
			<input type="submit" title="salva" value="salva" class="button_salva"/>
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

