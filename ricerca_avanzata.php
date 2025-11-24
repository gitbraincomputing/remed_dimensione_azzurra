<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');


function ricerca_avanzata()
{
	$tiporicerca = $_POST['ricerca_per'];
	
	if ($tiporicerca == 1)
		ricerca_avanzata_utente();
}


function impegnative_da_gestire()
{
	$conn = db_connect();
	$idregime=$_REQUEST['idregime'];	
	$query="SELECT * from impegnative_non_gestite where RegimeAssistenza=$idregime ";



$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

//pagina_new();
?>
<div class="padding10">

<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione pazienti</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_pazienti.php');" href="#">elenco pazienti</a></div>
        </div>

	<div class="titoloalternativo">
            <h1>Pazienti con impegnative non ancora gestite</h1>
	         <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
			<!--<h2>creata il 17/12/2008 da brain</h2>
			<h2>creata il 17/12/2008 da brain</h2>-->
</div>

<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>Codice</th> 
	<th>Cognome</th> 
    <th>Nome</th> 
	<th>Data di Nascita</th> 	
	<td>anagrafica</td>
	<td>area amministrativa</td>
	<td>area sanitaria</td>
	<th>Regime / Normativa</th>
	
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$id=$row['IdUtente'];
		$codice=$row['CodiceUtente'];
		$cognome=pulisci_lettura($row['Cognome']);
		$nome=pulisci_lettura($row['Nome']);
		$datanascita=formatta_data($row['DataNascita']);
		//$datanascita=$row['datanascita'];
		
		$regime_normativa=pulisci_lettura($row['norma']." - ".$row['regime']);
	
		?>
		
		<tr> 
		 <td><?=$id?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$datanascita?></td>
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$id?>');" href="#"><img src="images/anagrafica.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$id?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$id?>');" href="#"><img src="images/cartella.png" /></a></td> 
		 <td><?=$regime_normativa?></td> 
		</tr> 
		
		
		<?
}

mssql_free_result($rs);	
mssql_close($conn);
	

?>


</tbody> 
</table> 


<? footer_paginazione($conta);?>



<script>
$(document).ready(function() {
	
		
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>


<script type="text/javascript">

function change_tab_edit(iddiv)
{
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

exit();
	
}


function utenti_in_carico()
{
	$conn = db_connect();
	$idregime=$_REQUEST['idregime'];
	$idmodulo="";
	$contributo="";
	if (isset($_REQUEST['idmodulo'])) $idmodulo=$_REQUEST['idmodulo'];
	if (isset($_REQUEST['idoperatore'])) $idoperatore=$_REQUEST['idoperatore'];
	if (isset($_REQUEST['contributo'])) $contributo=$_REQUEST['contributo'];
	if (isset($_REQUEST['idpaziente'])) $idpaziente=$_REQUEST['idpaziente'];
	if($idpaziente!="")
		$query="SELECT * from re_utenti_in_carico where idUtente=$idpaziente";
		elseif ($idmodulo=="")
			$query="SELECT * from re_utenti_in_carico where idregime=$idregime";
			elseif ($contributo=="")
				$query="SELECT * FROM  re_moduli_in_carico where idregime=$idregime and id_modulo_padre=$idmodulo and id_operatore=$idoperatore";
				else
				$query="SELECT * FROM  re_moduli_contribuiti_paziente where idregime=$idregime and id_modulo=$idmodulo and id_operatore=$idoperatore";
	
$query.=" order by Cognome";
//echo($query);
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);
//pagina_new();
?>
<div class="padding10">

<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione pazienti</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_pazienti.php');" href="#">elenco pazienti</a></div>
          
        </div>


<div class="titoloalternativo">
            <h1>Utenti in carico per il regime selezionato</h1>
	         <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
			<!--<h2>creata il 17/12/2008 da brain</h2>
			<h2>creata il 17/12/2008 da brain</h2>-->
</div>

	
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>Codice</th> 
	<th>Cognome</th> 
    <th>Nome</th> 
	<th>Data di Nascita</th> 
	<td>anagrafica</td>
	<td>area amministrativa</td>
	<td>area sanitaria</td>
	<th>Regime / Normativa</th>

</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$id=$row['IdUtente'];		
		$codice=$row['CodiceUtente'];
		$cognome=$row['Cognome'];
		$nome=$row['Nome'];
		$datanascita=formatta_data($row['DataNascita']);
		//$datanascita=$row['datanascita'];
		
		$regime_normativa=$row['normativa']." - ".$row['regime'];
		$conta_cartelle=$row['conta_cartelle'];
		$conta_impegnative=$row['conta_impegnative'];
		if (!$conta_cartelle)
		$conta_cartelle=0;
		if (!$conta_impegnative)
		$conta_impegnative=0;
		?>
		
		<tr> 
		 <td><?=$id?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$datanascita?></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$id?>');" href="#"><img src="images/anagrafica.png" /></a></td> 
		 <? if (!isset($_REQUEST['idmodulo'])){
		 ?>
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$id?>');" href="#"><img src="images/impegnativa.png" /> (<?=$conta_impegnative?>)</a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$id?>');" href="#"><img src="images/cartella.png" />(<?=$conta_cartelle?>)</a> </td> 
		 <?
		 }
		 else
		 {?>
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$id?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$id?>');" href="#"><img src="images/cartella.png" /></a> </td> 
		 
		 <?
		 }
		 ?>
		 
		 <td><?=$regime_normativa?></td>
		</tr> 
		
		
		<?
}

mssql_free_result($rs);	
mssql_close($conn);
	

?>


</tbody> 
</table> 

<? footer_paginazione($conta);?>



<script>
$(document).ready(function() {
	
		
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>


<script type="text/javascript">

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

exit();
	
}



function cartelle_da_creare()
{
	$conn = db_connect();
	$idregime=$_REQUEST['idregime'];	
	$query="SELECT * from re_cartelle_da_creare_def where idregime=$idregime ";



$rs = mssql_query($query, $conn);

if(!$rs) error_message(mssql_error());

$conta=mssql_num_rows($rs);

//pagina_new();
?>
<div class="padding10">

<div class="logo"><img src="images/re-med-logo.png" /></div>

 <div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">gestione pazienti</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_pazienti.php');" href="#">elenco pazienti</a></div>
          
        </div>

	<div class="titoloalternativo">
            <h1>Pazienti a cui aprire una cartella clinica</h1>
	         <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
			<!--<h2>creata il 17/12/2008 da brain</h2>
			<h2>creata il 17/12/2008 da brain</h2>-->
</div>

<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    
    <th>Codice</th> 
	<th>Cognome</th> 
    <th>Nome</th> 
	<th>Data di Nascita</th> 	
	<td>anagrafica</td>
	<td>area amministrativa</td>
	<td>area sanitaria</td>
	<th>Regime / Normativa</th>
	
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$id=$row['IdUtente'];
		$codice=$row['CodiceUtente'];
		$cognome=$row['Cognome'];
		$nome=$row['Nome'];
		$datanascita=formatta_data($row['DataNascita']);
		//$datanascita=$row['datanascita'];
		
		$regime_normativa=$row['normativa']." - ".$row['regime'];
	
		?>
		
		<tr> 
		 <td><?=$id?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$datanascita?></td>
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$id?>');" href="#"><img src="images/anagrafica.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$id?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$id?>');" href="#"><img src="images/cartella.png" /></a></td> 
		 <td><?=$regime_normativa?></td> 
		</tr> 
		
		
		<?
}

mssql_free_result($rs);	
mssql_close($conn);
	

?>


</tbody> 
</table> 


<? footer_paginazione($conta);?>



<script>
$(document).ready(function() {
	
		
	  $("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
});
</script>


<script type="text/javascript">

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

exit();
	
}


function ricerca_avanzata_utente()
{
    $conn = db_connect();

    $where = $stato = "";

    if (isset($_POST['CodiceUtente']) and ($_POST['CodiceUtente'] != '')) {
		$CodiceUtente = trim($_POST['CodiceUtente']);
        $where .= " AND u.CodiceUtente LIKE '%$CodiceUtente%' ";
    }
    if (isset($_POST['IdUtente']) and ($_POST['IdUtente'] != '')) {
        $where .= " AND u.IdUtente = " . trim($_POST['IdUtente']);
    }
    if (isset($_POST['Cognome']) and (trim($_POST['Cognome']) != '')) {
        $cognome = strtolower(pulisci(trim($_POST['Cognome'])));
        $where .= " AND lower(u.Cognome) like N'%$cognome%' ";
    }
    if (isset($_POST['Nome']) and (trim($_POST['Nome']) != '')) {
        $nome = strtolower(pulisci(trim($_POST['Nome'])));
        $where .= " AND lower(u.Nome) like N'%$nome%' ";
    }
    if (isset($_POST['regime']) and (trim($_POST['regime']) != '')) {
        $regime = $_POST['regime'];
        $where .= " AND uc.idregime = $regime ";
    }
    if (isset($_POST['stato']) and (trim($_POST['stato']) != '')) {
        $stato = $_POST['stato'];
    }

    $query = "SELECT u.IdUtente, u.CodiceUtente, u.Nome, u.Cognome, u.DataNascita
				FROM utenti u
				LEFT JOIN utenti_cartelle uc ON uc.idutente = u.IdUtente
				WHERE u.cancella = 'n' AND u.stato = 1 $where
				GROUP BY u.IdUtente, u.CodiceUtente, u.Nome, u.Cognome, u.DataNascita
				ORDER BY cognome, nome asc";
    $rs = mssql_query($query, $conn);
    if (!$rs) error_message(mssql_error());
    $conta = mssql_num_rows($rs);

?>
    <div class="padding10">
        <div class="logo"><img src="images/re-med-logo.png" /></div>
        <div class="titoloalternativo">
            <div class="stampa"><a href="javascript:stampa2('wrap-content');"><img src="images/stampa-button.jpg" alt="stampa questa pagina" /></a></div>
        </div>

        <div id="briciola" style="margin-bottom:20px;">
            <div class="elem0"><a href="#">gestione pazienti</a></div>
            <div class="elem_pari"><a onclick="javascript:load_content('div','lista_pazienti.php');" href="#">elenco pazienti</a></div>

        </div>

        <div class="titolo_pag">
            <div class="comandi">
                <div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_pazienti.php?do=add');">aggiungi paziente</a></div>
            </div>
        </div>
        <?
        if ($conta == 0) {
        ?>
            <div class="titoloalternativo">
                <h1>Elenco Pazienti</h1>
                <div class="elem_pari"></div>
            </div>
            <div class="info">Non esistono pazienti corrispondenti ai criteri di ricerca inseriti.</div>
        <?
            exit();
        } ?>
        <table id="table" class="tablesorter" cellspacing="1">
            <thead>
                <tr>

                    <th>IdUtente</th>
                    <th>Codice</th>
                    <th>Cognome</th>
                    <th>Nome</th>
                    <th>Data di Nascita</th>
                    <td>anagrafica</td>
                    <td>area amministrativa</td>
                    <td>area sanitaria</td>
                    <th>Regime / Normativa</th>
                    <th>Stato</th>
                </tr>
            </thead>
            <tbody>
               <?
				$trovato_paziente = false;
                while ($row = mssql_fetch_assoc($rs)) {
                    $id = $row['IdUtente'];
                    $CodiceUtente = $row['CodiceUtente'];
                    if (trim($CodiceUtente) == '') $CodiceUtente = " - ";
                    $cognome = $row['Cognome'];
                    $nome = $row['Nome'];
                    $datanascita = formatta_data($row['DataNascita']);
                    //$datanascita=$row['datanascita'];
                    $stato_paziente = get_stato_paziente($id);
					
					if($stato != '' && $stato != $stato_paziente)
						continue;
					
					$trovato_paziente = true;
					
                    $regime_normativa = get_regime_paziente_descr($id);
                    //$stato_paziente=$row['stato_impegnativa'];

                    if (is_int($stato_paziente)) {
                        $stato_paziente_descr = get_stato_paziente_descr($stato_paziente);
                    } else {
                        //$stato_paziente_descr="lista d'attesa";
                        $stato_paziente = 9;
                        $stato_paziente_descr = get_stato_paziente_descr($stato_paziente);
                    }
                ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= $CodiceUtente ?></td>
                        <td><?= $cognome ?></td>
                        <td><?= $nome ?></td>
                        <td><?= $datanascita ?></td>
                        <td><a id="<?= $id ?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?= $id ?>');" href="#"><img src="images/anagrafica.png" /></a></td>
                        <td><a id="<?= $id ?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?= $id ?>');" href="#"><img src="images/impegnativa.png" /></a></td>
                        <td><a id="<?= $id ?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?= $id ?>');" href="#"><img src="images/cartella.png" /></a></td>
                        <td><?= $regime_normativa ?></td>
                        <td><?= $stato_paziente_descr ?></td>
                    </tr>
                <?
                }
				
				if(!$trovato_paziente) { ?>
					<tr><td colspan="10" ><div class="info">Non esistono pazienti corrispondenti ai criteri di ricerca inseriti.</div></td></tr>
<?				}

                mssql_free_result($rs);
                mssql_close($conn);
                ?>
            </tbody>
        </table>
        <? footer_paginazione($conta); ?>
    </div>

    <script>
        $(document).ready(function() {
            $("table").tablesorter({
                widthFixed: true,
                widgets: ['zebra']
            }).tablesorterPager({
                container: $("#pager")
            });
        });
    </script>

    <script type="text/javascript" id="js">
        function change_tab_edit(iddiv) {
            $("#layer_nero2").toggle();
            $('#lista_impegnative').innerHTML = "";
            pagina_da_caricare = "re_pazienti_amministrativa_edita_impegnativa.php?do=edit&id=" + iddiv;
            $("#lista_impegnative").load(pagina_da_caricare, '', function() {
                loading_page('loading');
                $("#container-4 .tabs-selected").removeClass('tabs-selected');
            });
        }
    </script>
<?
    exit();
}



if(isset($_SESSION['UTENTE'])) {

	if(!isset($do)) $do='';
	$back = "principale.php";

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
		switch($_POST['action']) {
			case "ricerca_avanzata":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else ricerca_avanzata();					
			break;
		}
		
		switch($do) {
			case "utenti_in_carico":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else utenti_in_carico();
			break;
			
			case "impegnative_da_gestire":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else impegnative_da_gestire();
			break;
			
			case "cartelle_da_creare":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else cartelle_da_creare();
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