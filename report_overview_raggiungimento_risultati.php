<?

include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

$tablename = 'utenti';
include_once('include/function_page.php');

if ($principale==true) {
	$nome_pagina="Benvenuto";
	header_new($nome_pagina,false);
	barra_top_new();
	top_message();
	menu_new();
	$principale=false;
}

$conn = db_connect();
$where2 = "";
$regime = $op = $modulo = $valore_risultati = $data_creazione = "";

if(isset($_REQUEST['op']) && $_REQUEST['op'] !== "") {
	$where2 .= " AND nome_operatore LIKE '%".$_REQUEST['op']."%'";
	$op = $_REQUEST['op'];
}
if(isset($_REQUEST['regime']) && $_REQUEST['regime'] !== "") {
	$where2 .= " AND idregime = ".$_REQUEST['regime'];
	$regime = $_REQUEST['regime'];
}
if(isset($_REQUEST['modulo']) && $_REQUEST['modulo'] !== "") {
	$where2 .= " AND nome_modulo = '".$_REQUEST['modulo'] . "'";
	$modulo = $_REQUEST['modulo'];
}
if(isset($_REQUEST['valore_risultati']) && $_REQUEST['valore_risultati'] !== "") {
	$where2 .= " AND valore_risultati = '".$_REQUEST['valore_risultati'] . "'";
	$valore_risultati = $_REQUEST['valore_risultati'];
}

if(isset($_REQUEST['data_dal']) && $_REQUEST['data_dal'] !== "") {
	$where2 .= " AND data_creazione  >= CONVERT(datetime, '". $_REQUEST['data_dal'] ." 00:00:00:000', 102)";
	$data_dal = $_REQUEST['data_dal'];
}
if(isset($_REQUEST['data_al']) && $_REQUEST['data_al'] !== "") {
	$where2 .= " AND data_creazione <= CONVERT(datetime, '". $_REQUEST['data_al']." 00:00:00:000', 102)";
	$data_al = $_REQUEST['data_al'];
}

?>
<script type="text/javascript">
function reload_review(nome_operatore,idregime,nome_modulo,valore_risultati,data_dal,data_al){
	//alert(idpaz);
	$("#layer_nero2").toggle();
	$('#wrap-content').innerHTML="";
	nome_operatore = nome_operatore.replace(/ /gi, "%20");
	nome_operatore = nome_operatore.replace(/'/gi, "");
	nome_modulo = nome_modulo.replace(/ /gi, "%20");
	valore_risultati = valore_risultati.replace(/ /gi, "%20");
	pagina_da_caricare="report_overview_raggiungimento_risultati.php?op="+nome_operatore+"&regime="+idregime+"&modulo="+nome_modulo+"&valore_risultati="+valore_risultati+"&data_dal="+data_dal+"&data_al="+data_al;
	$("#wrap-content").load(pagina_da_caricare,'', function(){
	   loading_page('loading');
	   //$("#container-4 .tabs-selected").removeClass('tabs-selected');
	 });
}
</script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() 
	{			
		$.mask.addPlaceholder('~',"[+-]");	
		$(".campo_data").mask("99/99/9999");
		$(".solo_lettere").validation({ type: "alpha", add:"' "});	
		$("table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
		
	});	
</script>
<div id="wrap-content"><div class="padding10">
<div class="logo"><img src="images/re-med-logo.png" /></div>

 			<div id="briciola" style="margin-bottom:20px;">
        	<div class="elem0"><a href="#">home</a></div>
            <div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>          
        </div>

<div class="titoloalternativo">
    <h1>Overview raggiungimento risultati</h1>	         
	<div class="stampa">
		<a onclick="javascript:(alert('Premere OK e attendere l\'elaborazione del documento..'));" href="include/export_sql_xls.php?rep=rep_raggiung_risult&where=<?=rawurlencode($where2)?>"><img src="images/excel-button.png"></a>
	</div>
</div>

<span style="border-bottom: 0px none; margin-bottom: 15px; width:100% !important" class="rigo_mask no_print">
		
		<div class="campo_big " style="float:left;padding-right:10px">
		<label class="testo_mask">Filtra per regime</label><br>
			<select  class="scrittura" name="idnormativa" id="idnormativa">
				<option selected="" value="">tutti</option>
				<?
				$query="SELECT n.normativa, r.idregime, r.regime FROM regime AS r
						INNER JOIN normativa AS n ON r.idnormativa = n.idnormativa
						WHERE r.stato = 1
						ORDER BY normativa ASC";
				$rs_op = mssql_query($query, $conn);
				while($row_op=mssql_fetch_assoc($rs_op)){
				?>
					<option <?if($regime==$row_op['idregime']) echo("selected")?> value="<?=$row_op['idregime']?>"><?=$row_op['normativa']." - ".$row_op['regime']?></option>
				<?}	?>
			</select>
		</div>
		
		<div class="campo_big " style="float:left;padding-right:10px">
		<label class="testo_mask">Filtra per operatore</label><br>
			<select  class="scrittura" name="nome_operatore" id="nome_operatore">
				<option selected="" value="">tutti</option>
				<?
				$query="SELECT nome FROM operatori
						WHERE (nome <> 'admin') AND (nome <> 'Software Administrator') AND cancella = 'n'
						GROUP BY nome
						ORDER BY nome";
				$rs_op = mssql_query($query, $conn);
				while($row_op=mssql_fetch_assoc($rs_op)){
				?>
					<option <?if($op==trim($row_op['nome'])) echo("selected")?> value="<?=trim($row_op['nome'])?>"><?=strtoupper(trim($row_op['nome']))?></option>
				<?}	?>
			</select>
		</div>

		<div class="campo_big " style="float:left;padding-right:10px">
			<label class="testo_mask">Visualizza dal</label><br>
			<input type="date" name="data_dal" id="data_dal" <? if($data_dal) echo 'value="'.$data_dal.'"'; ?> >
		</div>

		<div class="campo_big " style="float:left;padding-right:10px">
			<label class="testo_mask">al</label><br>
			<input type="date" name="data_al" id="data_al" <? if($data_al) echo 'value="'.$data_al.'"'; ?> >
		</div>

		<br><br><p>&nbsp;</p>
		
		<div class="campo_big " style="float:left;padding-right:10px">
		<label class="testo_mask">Filtra per modulo</label><br>
			<select class="scrittura" name="idmoduloversione" id="idmoduloversione">
				<option selected="" value="">tutti</option>
				<?
				$query="SELECT m.nome FROM campi AS c
						JOIN moduli AS m ON c.idmoduloversione = m.id
						WHERE (c.segnalibro LIKE 'raggiungimento_risultati_%')
						GROUP BY nome
						ORDER BY nome ASC";
				$rs_mod = mssql_query($query, $conn);
				while($row_mod=mssql_fetch_assoc($rs_mod)){
				?>
					<option <?if($modulo==$row_mod['nome']) echo("selected")?> value="<?=$row_mod['nome']?>"><?=$row_mod['nome']?></option>
				<?}	?>
			</select>
		</div>	

		<div class="campo_big " style="float:left;padding-right:10px">
		<label class="testo_mask">Filtra per valore risultati</label><br>
			<select class="scrittura" name="raggiungimento_risultati" id="raggiungimento_risultati">
				<option <?if($valore_risultati == '') echo("selected")?> value="">Tutti</option>
				<option <?if($valore_risultati == 'N.A.') echo("selected")?> value="N.A.">N.D.</option>
				<option <?if($valore_risultati == '1') echo("selected")?> value="1">Raggiunti</option>
				<option <?if($valore_risultati == '2') echo("selected")?> value="2">Parzialmente raggiunti</option>
				<option <?if($valore_risultati == '3') echo("selected")?> value="3">Non raggiunti</option>
			</select>
		</div>	
		
		<div class="campo_big "><br>
			<input type="button" style="padding: 0px 10px" name="esegui filtro" value="esegui filtro" onclick="reload_review($('#nome_operatore').val(),$('#idnormativa').val(),$('#idmoduloversione').val(),$('#raggiungimento_risultati').val(),$('#data_dal').val(),$('#data_al').val());" />
		</div>
		<br>
		<p>Se il campo data è vuoto, vengono visualizzati i moduli le cui scadenze hanno data inferiore o uguale ai prossimi 365 giorni.<br>Tutti i risultati sono limitati a 365 giorni.</p>
</span>



<?	
	if(isset($_REQUEST['data_dal']) || isset($_REQUEST['data_al'])) 	// solo dopo aver remuto sul btn di avvio ricerca
	{ 
?>
	<table id="table" class="tablesorter" cellspacing="1"> 
		<thead> 
			<tr>
				<th>NORMATIVA</th>
				<th>REGIME</th>
				<th>OPERATORE</th>
				<th>CARTELLA</th>
				<th>COGNOME PAZIENTE</th>
				<th>NOME PAZIENTE</th>
				<th>MODULO</th>
				<th>RAGGIUNGIMENTO RISULTATI</th>
				<th>DATA COMPILAZIONE</th>
			</tr>
		</thead>
		<tbody>
	<?	// SBO 12/12/23: A seguito della richiesta di un doppio filtro temporale abbiamo sostituito l'utilizzo della 
		//	vista a favore della query diretta in quanto con il filtro temporale i tempi di estrazione erano troppo 
		//	elevati
		$query="SELECT TOP (100) PERCENT dbo.utenti_cartelle.idregime, dbo.normativa.normativa, dbo.regime.regime, dbo.utenti_cartelle.codice_cartella, dbo.utenti_cartelle.versione AS versione_cartella, dbo.operatori.nome AS nome_operatore, 
                         dbo.utenti.Cognome AS cognome_paziente, dbo.utenti.Nome AS nome_paziente, dbo.moduli.nome AS nome_modulo, dbo.istanze_testata.datains AS data_creazione, dbo.utenti_cartelle.id AS id_cartella, 
                         dbo.moduli.id AS id_modulo_versione, dbo.utenti_cartelle.idutente AS id_paziente, dbo.moduli.cancella, dbo.cartelle_pianificazione.id_cartella_pianificazione_testata, dbo.cartelle_pianificazione.id_modulo_padre, 
                         dbo.istanze_dettaglio.valore AS valore_risultati
				FROM	dbo.istanze_testata 
					INNER JOIN dbo.utenti_cartelle ON dbo.istanze_testata.id_cartella = dbo.utenti_cartelle.id 
					INNER JOIN dbo.operatori ON dbo.istanze_testata.opeins = dbo.operatori.uid 
					INNER JOIN dbo.utenti ON dbo.utenti_cartelle.idutente = dbo.utenti.IdUtente 
					INNER JOIN dbo.normativa ON dbo.utenti_cartelle.idnormativa = dbo.normativa.idnormativa 
					INNER JOIN dbo.regime ON dbo.utenti_cartelle.idregime = dbo.regime.idregime 
					LEFT OUTER JOIN dbo.campi 
					INNER JOIN dbo.istanze_dettaglio ON dbo.campi.idcampo = dbo.istanze_dettaglio.idcampo 
					INNER JOIN dbo.moduli ON dbo.campi.idmoduloversione = dbo.moduli.id ON dbo.istanze_testata.id_istanza_testata = dbo.istanze_dettaglio.id_istanza_testata AND dbo.istanze_testata.id_modulo_versione = dbo.moduli.id 
					RIGHT OUTER JOIN dbo.cartelle_pianificazione ON dbo.istanze_testata.id_modulo_versione = dbo.cartelle_pianificazione.id_modulo_versione
				WHERE (dbo.utenti_cartelle.cancella = 'n') AND (dbo.moduli.cancella = 'n')
					AND (dbo.campi.segnalibro LIKE N'raggiungimento_obiettivi_ai%' OR dbo.campi.segnalibro LIKE N'raggiungimento_risultati_%') AND (dbo.utenti_cartelle.codice_cartella <> N'000605') 
					AND (dbo.cartelle_pianificazione.id_cartella_pianificazione_testata =
                             (SELECT        MAX(id_pianificazione_testata) AS max_id_pian_test
                               FROM            dbo.cartelle_pianificazione_testata AS cartelle_pianificazione_testata_1
                               WHERE        (id_cartella = dbo.istanze_testata.id_cartella))
						) 
					AND (dbo.istanze_dettaglio.valore <> '')
					AND (dbo.operatori.nome <> 'admin') AND (dbo.operatori.nome <> 'Software Administrator') AND (dbo.utenti_cartelle.idutente <> 605)
					$where2 
				GROUP BY dbo.utenti_cartelle.idregime, dbo.normativa.normativa, dbo.regime.regime, dbo.utenti_cartelle.codice_cartella, dbo.utenti_cartelle.versione, dbo.operatori.nome, dbo.utenti.Cognome, dbo.utenti.Nome, dbo.moduli.nome, 
										 dbo.istanze_testata.datains, dbo.utenti_cartelle.id, dbo.moduli.id, dbo.utenti_cartelle.idutente, dbo.moduli.cancella, dbo.cartelle_pianificazione.id_cartella_pianificazione_testata, dbo.cartelle_pianificazione.id_modulo_padre, 
										 dbo.istanze_dettaglio.valore
				ORDER BY data_creazione ASC";
		$rs = mssql_query($query, $conn);
		$conta=mssql_num_rows($rs);

		while($row = mssql_fetch_assoc($rs)) {
			echo "<tr>
					<td>".$row['normativa']."</td>
					<td>".$row['regime']."</td>
					<td>".$row['nome_operatore']."</td>
					<td>".$row['codice_cartella']."/".$row['versione_cartella']."</td>
					<td>".$row['cognome_paziente']."</td>
					<td>".$row['nome_paziente']."</td>
					<td><a href=\"#".$row['id_cartella']."#".$row['id_modulo_versione']."&mod\" onclick=\"javascript:load_content('div','lista_pazienti.php?main_container=3&do=review&id=".$row['id_paziente']."');\">".$row['nome_modulo']."</a></td>";
					
					switch($row['valore_risultati']) {
						case "N.A.":	echo "<td style=\"text-align:center\">N.D.</td>";				break;
						case "1":	echo "<td style=\"text-align:center\">Raggiunti</td>";				break;
						case "2":	echo "<td style=\"text-align:center\">Parzialmente raggiunti</td>";	break;
						case "3":	echo "<td style=\"text-align:center\">Non raggiunti</td>";			break;
					}
					
					echo "<td>".formatta_data($row['data_creazione'])."</td>
				</tr>";
		}
		
	?>
		</tbody> 
	</table>
<? 
	footer_paginazione($conta);
}
mssql_free_result($rs);
?>

</div></div>


<?
exit(0);

?>

