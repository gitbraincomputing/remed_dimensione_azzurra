<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_paziente=$_REQUEST['id_paziente'];
$idimpegnativa=$_REQUEST['idimpegnativa'];

$conn = db_connect();
$query1="SELECT     IdUtente, Cognome, Nome, Normativa, RegimeAssistenza, DataPrestazione, DataAutorizAsl, conteggio
FROM         (SELECT     ROW_NUMBER() OVER (ORDER BY DataPrestazione) AS conteggio, IdUtente, Cognome, Nome, Normativa, RegimeAssistenza, 
                      DataPrestazione, DataAutorizAsl, desc_regime, desc_norm, idimpegnativa, indice
FROM         dbo.re_trattamenti_equipe
WHERE     (IdUtente = $id_paziente) AND (idimpegnativa = $idimpegnativa)) AS test
WHERE     (conteggio = 40 OR conteggio = 80 OR conteggio = 120 OR conteggio = 160 OR conteggio = 200 OR conteggio = 240)
ORDER BY idimpegnativa, DataPrestazione";
$rs1 = mssql_query($query1, $conn);
//echo($query1);
while($row1 = mssql_fetch_assoc($rs1)){
	if($row1['conteggio']==40) $tr40=formatta_data($row1['DataPrestazione']);
	if($row1['conteggio']==80) $tr80=formatta_data($row1['DataPrestazione']);
	if($row1['conteggio']==120) $tr120=formatta_data($row1['DataPrestazione']);
	if($row1['conteggio']==160) $tr160=formatta_data($row1['DataPrestazione']);
	if($row1['conteggio']==200) $tr200=formatta_data($row1['DataPrestazione']);
	if($row1['conteggio']==240) $tr240=formatta_data($row1['DataPrestazione']);
}?>
<table id="table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr>    
	<th>Trattamento</th>
	<th>Data Prestazione</th>	
</tr> 
</thead> 
<tbody>
<tr><td>40 esimo</td><td><?=$tr40?></td></tr>
<tr><td>80 esimo</td><td><?=$tr80?></td></tr>
<tr><td>120 esimo</td><td><?=$tr120?></td></tr>
<tr><td>160 esimo</td><td><?=$tr160?></td></tr>
<tr><td>200 esimo</td><td><?=$tr200?></td></tr>
<tr><td>240 esimo</td><td><?=$tr240?></td></tr>
</tbody> 
</table>    
