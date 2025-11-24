<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');

$asl=$_REQUEST['asl'];

$conn = db_connect();
//$query = "SELECT IdDistretto as id, CodiceASL, CodiceDistretto as codice, DescrizioneDistretto as nome FROM dbo.distretti GROUP BY IdDistretto, CodiceASL, CodiceDistretto, DescrizioneDistretto HAVING (CodiceASL = '$asl')";
$query ="SELECT dbo.distretti.IdDistretto as id, dbo.distretti.CodiceASL, dbo.distretti.CodiceDistretto as codice,DescrizioneDistretto as nome, dbo.distretti.comune as idcomune,denominazione as comune
FROM dbo.distretti INNER JOIN dbo.comuni ON dbo.distretti.Comune = dbo.comuni.id_comune where ( dbo.distretti.CodiceASL = '$asl') order by dbo.comuni.denominazione";
$rs = mssql_query($query, $conn);
?>
<select name="distretto" class="scrittura" style="width:540px;">
	<option value="0" selected>selezionare</option>
	<?php
	while($row=mssql_fetch_assoc($rs)){
		//$sel="";
		//if ($distretto==$row['codice'])
		//$sel="selected";						
		?>
			<option value="<?=$row['id']?>"><?=$row['comune']." - ".$row['codice']." - ".$row['nome']?></option>
		 <?
	}?>
</select>






