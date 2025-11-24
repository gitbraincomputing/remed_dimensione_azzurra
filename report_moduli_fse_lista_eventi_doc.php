<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');
?>
<html>
	<head>
		<link href="style/flexigrid.css" rel="stylesheet" type="text/css" />
		<link href="style/new_ui.css" rel="stylesheet" type="text/css" />
		<link href="style/new_ui_print.css" rel="stylesheet" type="text/css" media="print" />
	</head>
	<body style="background: #ffffff;">
		<div style="padding: 10px;">
			<div class="titoloalternativo">
				<h1>Moduli FSE</h1>
			</div>
<?php
$conn = db_connect();
$cdf = $_GET['cdf'];
$doc_temp_id = $_GET['doc_temp_id'];

$_query = "SELECT CONCAT(paz.Nome, ' ', paz.Cognome) as nome_paziente, 
				ope.nome AS nome_operatore, 
				rep_fse.nome_modulo, rep_fse.data_esecuzione, rep_fse.stato_esecuzione, rep_fse.offuscamento, rep_fse.id_temp_fse, rep_fse.id_final_fse
			FROM repository_fse AS rep_fse
			LEFT JOIN operatori AS ope ON ope.uid = rep_fse.opeins_modulo
			LEFT JOIN utenti AS paz ON paz.IdUtente = rep_fse.id_paziente
			WHERE codice_fiscale_paziente = '$cdf' AND id_temp_fse = '$doc_temp_id'";		

$_rs = mssql_query($_query, $conn);
$conta = mssql_num_rows($_rs);
?>
			<div>
				<table class="tablesorter" cellspacing="1">
					<thead>
						<tr>
							<th>PAZIENTE</th>
							<th>OPERATORE</th>
							<th>MODULO</th>
							<th>OFFUSCATO</th>
							<th>AZIONE</th>
							<th>DATA ESECUZIONE</th>					
							<th>STATO AZIONE</th>			
							<th>ID DOCUMENTO TEMPORANEO</th>
							<th>ID DOCUMENTO ELABORATO</th>
						</tr>
					</thead>
					<tbody>
<?php
if($conta == 0) {
	echo "<tr>
			<td colspan='9'>Nessun record trovato</td>
		</tr>";
} else {
	while($doc = mssql_fetch_assoc($_rs)) 
	{
		echo "<tr>
				<td>" . $doc['nome_paziente']. "</td>
				<td>" . $doc['nome_operatore']. "</td>
				<td>" . $doc['nome_modulo']. "</td>
				<td>" . ($doc['offuscamento'] == 'n' ? 'NO' : 'SI') . "</td>
				<td>" . ($doc['azione'] === 'CREATE' ? 'Creazione' : 'Cancellazione') . "</td>
				<td>" . date_format(date_create($doc['data_esecuzione']),"d/m/Y H:i") . "</td>							
				<td>" . ($doc['stato_esecuzione'] === 'OK' ? 'Eseguita' : 'Non eseguita') . "</td>					
				<td>" . $doc['id_temp_fse'] . "</td>
				<td>" . $doc['id_final_fse'] . "</td>
			</tr>";
	}
}
mssql_free_result($_rs);

?>
					</tbody>
				</table>
			</div>
<?php
footer_paginazione($conta);
?>
		</div>
	</body>
</html>