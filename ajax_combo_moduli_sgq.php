<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_combo=$_REQUEST['id_combo'];
$id=$_REQUEST['id'];
$conn = db_connect();
if($id==1)//combo semplice
		$query="SELECT     dbo.moduli_combo.idcombo, dbo.istanze_dettaglio_sgq.id_istanza_dettaglio_sgq
				FROM         dbo.moduli_combo INNER JOIN dbo.istanze_dettaglio_sgq ON dbo.moduli_combo.idcampo = dbo.istanze_dettaglio_sgq.id_campo
				WHERE     (dbo.moduli_combo.idcombo = $id_combo)";
		else//combo ramificata
		$query="SELECT dbo.istanze_dettaglio_sgq.id_istanza_dettaglio_sgq, dbo.moduli_combo_ramificate.idcombo
				FROM dbo.istanze_dettaglio_sgq INNER JOIN dbo.moduli_combo_ramificate ON dbo.istanze_dettaglio_sgq.id_campo = dbo.moduli_combo_ramificate.idcampo
				WHERE (dbo.moduli_combo_ramificate.idcombo =$id_combo)";		
$rs = mssql_query($query, $conn);
//echo('rsss');
//echo(mssql_num_rows($rs));
//fin qui ok
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
   if($id!=1)
       $query="SELECT * FROM dbo.combo_ramificate WHERE (dbo.combo_ramificate.id=$id_combo) AND (dbo.combo_ramificate.idcombopadre=1)";
    $rs = mssql_query($query, $conn);
    if(mssql_num_rows($rs)>0)
        echo($query);
    
?>