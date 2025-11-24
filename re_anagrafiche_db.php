<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 2;
$tablename = 'utenti';
include_once('include/function_page.php');
$id=$_REQUEST['id'];

if($id==1) controlla_CF();
if($id==2) controlla_DN();
if($id==3) controlla_LN();
if($id==4) controlla_RE();

function controlla_LN(){
$conn = db_connect();
$query="SELECT * from re_ricerca_pazienti_db WHERE (luogo_nascita IS NULL) order by cognome asc";
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);

//pagina_new();
?>
<div class="padding10">
	<div class="titoloalternativo">
        <h1>Assenza di Luogo di Nascita - elenco pazienti</h1>		
	</div>
<?	if($conta==0){?>	
	<div class="info">Non esistono pazienti con luogo di nascita assente</div>
	<?
	exit();
	}
	?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>    
    <th>Codice</th> 
	<th>Cognome</th> 
    <th>Nome</th> 
	<th>Data di Nascita</th> 
	<th>Codice fiscale</th>
	<th>Luogo Nascita</th>
	<td>anagrafica</td>
	<td>area amministrativa</td>
	<td>area sanitaria</td>
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$id=$row['IdUtente'];		
		$CodiceUtente=$row['CodiceUtente'];
		if (trim($CodiceUtente)=='') $CodiceUtente=" - ";
		$cognome=$row['Cognome'];
		$nome=$row['Nome'];
		$datanascita=formatta_data($row['DataNascita']);		
		$regime_normativa=get_regime_paziente_descr($id);
		$stato_paziente=$row['stato_impegnativa'];
		$codice_fiscale=$row['CodiceFiscale'];
		$luogo_nascita=$row['luogo_nascita'];
		
		if (is_int($stato_paziente))
		{
			$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);			
		}
		else{
			//$stato_paziente_descr="lista d'attesa";
			$stato_paziente=9;
			$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);
		}	
		?>		
		<tr> 
		 <td><?=$CodiceUtente?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$datanascita?></td>
		 <td><?=$codice_fiscale?></td>
		 <td><?=$luogo_nascita?></td>
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$id?>');" href="#"><img src="images/anagrafica.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$id?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$id?>');" href="#"><img src="images/cartella.png" /></a></td> 		
		</tr>
		<?
}
mssql_free_result($rs);	
?>
</tbody> 
</table> 

<? 
$numero_pager=(int)($conta/20)

?>

<div class="titolo_pag">
		<div class="comandi">
				<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_pazienti.php?do=add');">aggiungi paziente</a></div>
         
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
						$x=50;
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
						$x=$x+50;
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
<?}


function controlla_DN(){
$conn = db_connect();
$query="SELECT * from re_ricerca_pazienti_db WHERE (DataNascita is null) order by cognome asc";
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);

//pagina_new();
?>
<div class="padding10">
	<div class="titoloalternativo">
        <h1>Assenza di Data di Nascita - elenco pazienti</h1>		
	</div>
<?	if($conta==0){?>	
	<div class="info">Non esistono pazienti con data di nascita assente</div>
	<?
	exit();
	}
	?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>    
    <th>Codice</th> 
	<th>Cognome</th> 
    <th>Nome</th> 
	<th>Data di Nascita</th> 
	<th>Codice fiscale</th>
	<th>Luogo Nascita</th>
	<td>anagrafica</td>
	<td>area amministrativa</td>
	<td>area sanitaria</td>
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$id=$row['IdUtente'];		
		$CodiceUtente=$row['CodiceUtente'];
		if (trim($CodiceUtente)=='') $CodiceUtente=" - ";
		$cognome=$row['Cognome'];
		$nome=$row['Nome'];
		$datanascita=formatta_data($row['DataNascita']);		
		$regime_normativa=get_regime_paziente_descr($id);
		$stato_paziente=$row['stato_impegnativa'];
		$codice_fiscale=$row['CodiceFiscale'];
		$luogo_nascita=$row['luogo_nascita'];
		
		if (is_int($stato_paziente))
		{
			$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);			
		}
		else{
			//$stato_paziente_descr="lista d'attesa";
			$stato_paziente=9;
			$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);
		}	
		?>		
		<tr> 
		 <td><?=$CodiceUtente?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$datanascita?></td>
		 <td><?=$codice_fiscale?></td>
		 <td><?=$luogo_nascita?></td>
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$id?>');" href="#"><img src="images/anagrafica.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$id?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$id?>');" href="#"><img src="images/cartella.png" /></a></td> 		
		</tr>
		<?
}
mssql_free_result($rs);	
?>
</tbody> 
</table> 

<? 
$numero_pager=(int)($conta/20)

?>

<div class="titolo_pag">
		<div class="comandi">
				<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_pazienti.php?do=add');">aggiungi paziente</a></div>
         
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
						$x=50;
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
						$x=$x+50;
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
<?}


function controlla_CF(){
$conn = db_connect();
$query="SELECT * from re_ricerca_pazienti_db WHERE (CodiceFiscale is NULL) order by cognome asc";
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);

//pagina_new();
?>
<div class="padding10">
	<div class="titoloalternativo">
        <h1>Assenza di Codice Fiscale - elenco pazienti</h1>		
	</div>
<?	if($conta==0){?>	
	<div class="info">Non esistono pazienti con codice fiscale assente</div>
	<?
	exit();
	}
	?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>    
    <th>Codice</th> 
	<th>Cognome</th> 
    <th>Nome</th> 
	<th>Data di Nascita</th> 
	<th>Codice fiscale</th>
	<th>Luogo Nascita</th>
	<td>anagrafica</td>
	<td>area amministrativa</td>
	<td>area sanitaria</td>
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$id=$row['IdUtente'];		
		$CodiceUtente=$row['CodiceUtente'];
		if (trim($CodiceUtente)=='') $CodiceUtente=" - ";
		$cognome=$row['Cognome'];
		$nome=$row['Nome'];
		$datanascita=formatta_data($row['DataNascita']);		
		$regime_normativa=get_regime_paziente_descr($id);
		$stato_paziente=$row['stato_impegnativa'];
		$codice_fiscale=$row['CodiceFiscale'];
		$luogo_nascita=$row['luogo_nascita'];
		
		if (is_int($stato_paziente))
		{
			$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);			
		}
		else{
			//$stato_paziente_descr="lista d'attesa";
			$stato_paziente=9;
			$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);
		}	
		?>		
		<tr> 
		 <td><?=$CodiceUtente?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$datanascita?></td>
		 <td><?=$codice_fiscale?></td>
		 <td><?=$luogo_nascita?></td>
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$id?>');" href="#"><img src="images/anagrafica.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$id?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$id?>');" href="#"><img src="images/cartella.png" /></a></td> 		
		</tr>
		<?
}
mssql_free_result($rs);	
?>
</tbody> 
</table> 

<? 
$numero_pager=(int)($conta/20)

?>

<div class="titolo_pag">
		<div class="comandi">
				<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_pazienti.php?do=add');">aggiungi paziente</a></div>
         
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
						$x=50;
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
						$x=$x+50;
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
<?}



function controlla_RE(){
$conn = db_connect();
$query="SELECT * from re_ricerca_pazienti_db WHERE (residenza IS NULL) order by cognome asc";
$rs = mssql_query($query, $conn);
if(!$rs) error_message(mssql_error());
$conta=mssql_num_rows($rs);

//pagina_new();
?>
<div class="padding10">
	<div class="titoloalternativo">
        <h1>Assenza di Residenza - elenco pazienti</h1>		
	</div>
<?	if($conta==0){?>	
	<div class="info">Non esistono pazienti con residenza assente</div>
	<?
	exit();
	}
	?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>    
    <th>Codice</th> 
	<th>Cognome</th> 
    <th>Nome</th> 
	<th>Data di Nascita</th> 
	<th>Codice fiscale</th>
	<th>Luogo Nascita</th>
	<td>anagrafica</td>
	<td>area amministrativa</td>
	<td>area sanitaria</td>
</tr> 
</thead> 
<tbody> 

<?
while($row = mssql_fetch_assoc($rs))   
{
		$id=$row['IdUtente'];		
		$CodiceUtente=$row['CodiceUtente'];
		if (trim($CodiceUtente)=='') $CodiceUtente=" - ";
		$cognome=$row['Cognome'];
		$nome=$row['Nome'];
		$datanascita=formatta_data($row['DataNascita']);		
		$regime_normativa=get_regime_paziente_descr($id);
		$stato_paziente=$row['stato_impegnativa'];
		$codice_fiscale=$row['CodiceFiscale'];
		$luogo_nascita=$row['luogo_nascita'];
		
		if (is_int($stato_paziente))
		{
			$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);			
		}
		else{
			//$stato_paziente_descr="lista d'attesa";
			$stato_paziente=9;
			$stato_paziente_descr=get_stato_paziente_descr($stato_paziente);
		}	
		?>		
		<tr> 
		 <td><?=$CodiceUtente?></td> 
		 <td><?=$cognome?></td> 
		 <td><?=$nome?></td> 
		 <td><?=$datanascita?></td>
		 <td><?=$codice_fiscale?></td>
		 <td><?=$luogo_nascita?></td>
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=1&do=review&id=<?=$id?>');" href="#"><img src="images/anagrafica.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=2&do=review&id=<?=$id?>');" href="#"><img src="images/impegnativa.png" /></a></td> 
		 <td><a id="<?=$id?>" onclick="javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=<?=$id?>');" href="#"><img src="images/cartella.png" /></a></td> 		
		</tr>
		<?
}
mssql_free_result($rs);	
?>
</tbody> 
</table> 

<? 
$numero_pager=(int)($conta/20)

?>

<div class="titolo_pag">
		<div class="comandi">
				<div class="aggiungi aggiungi_left"><a onclick="javascript:load_content('div','lista_pazienti.php?do=add');">aggiungi paziente</a></div>
         
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
						$x=50;
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
						$x=$x+50;
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
<?}?>
