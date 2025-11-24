<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_combo=$_REQUEST['id_combo'];
$id=$_REQUEST['id'];
$conn = db_connect();
if($id==1)
		$query="SELECT     dbo.moduli_combo.idcombo, dbo.istanze_dettaglio.id_istanza_dettaglio
				FROM         dbo.moduli_combo INNER JOIN dbo.istanze_dettaglio ON dbo.moduli_combo.idcampo = dbo.istanze_dettaglio.idcampo
				WHERE     (dbo.moduli_combo.idcombo = $id_combo)";
		else
		$query="SELECT dbo.istanze_dettaglio.id_istanza_dettaglio, dbo.moduli_combo_ramificate.idcombo
				FROM dbo.istanze_dettaglio INNER JOIN dbo.moduli_combo_ramificate ON dbo.istanze_dettaglio.idcampo = dbo.moduli_combo_ramificate.idcampo
				WHERE (dbo.moduli_combo_ramificate.idcombo =$id_combo)";		
//echo($query);
$rs = mssql_query($query, $conn);
if(mssql_num_rows($rs)>0) {
	echo(mssql_num_rows($rs));
	}
	else{
		if($id==1)
			$query="SELECT TOP (1) dbo.combo.id, dbo.moduli_combo.idmoduloversione, dbo.moduli.id AS id_modulo, dbo.moduli.idmodulo AS modulo
					FROM dbo.moduli_combo INNER JOIN dbo.moduli ON dbo.moduli_combo.idmoduloversione = dbo.moduli.id INNER JOIN
                      dbo.combo ON dbo.moduli_combo.idcombo = dbo.combo.id  GROUP BY dbo.combo.id, dbo.moduli_combo.idmoduloversione, dbo.moduli.id, dbo.moduli.idmodulo
					HAVING (dbo.combo.id = $id_combo) ORDER BY dbo.moduli_combo.idmoduloversione DESC";
			else
			$query="SELECT TOP (1) dbo.moduli.id AS id_modulo, dbo.moduli.idmodulo AS modulo, dbo.combo_ramificate.id, dbo.moduli_combo_ramificate.idmoduloversione
					FROM dbo.moduli INNER JOIN  dbo.moduli_combo_ramificate ON dbo.moduli.id = dbo.moduli_combo_ramificate.idmoduloversione INNER JOIN
                      dbo.combo_ramificate ON dbo.moduli_combo_ramificate.idcombo = dbo.combo_ramificate.id
					GROUP BY dbo.moduli.id, dbo.moduli.idmodulo, dbo.combo_ramificate.id, dbo.moduli_combo_ramificate.idmoduloversione
					HAVING (dbo.combo_ramificate.id = $id_combo) ORDER BY dbo.moduli_combo_ramificate.idmoduloversione DESC";
		//echo($query);
		$rs = mssql_query($query, $conn);
		$row=mssql_fetch_assoc($rs);
		$id_modulo=$row['id_modulo'];
		$modulo=$row['modulo'];
		if($modulo!=""){
			$query="SELECT MAX(id) AS id_modulo, idmodulo AS modulo FROM dbo.moduli GROUP BY idmodulo HAVING (idmodulo = $modulo)";
			//echo($query);
			$rs = mssql_query($query, $conn);
			$row=mssql_fetch_assoc($rs);
			$id_modulo_padre=$row['id_modulo'];
			if($id_modulo_padre==$id_modulo) echo($id_modulo_padre);
		}		
	}
?>