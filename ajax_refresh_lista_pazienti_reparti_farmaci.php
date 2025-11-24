<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');

$conn = db_connect();

$_farmaci_paz_sql = "SELECT id_paziente
						FROM farmaci_struttura_pazienti
						WHERE id_farmaco = " . $_POST['id_farmaco'];
$_farm_paz_rs = mssql_query($_farmaci_paz_sql, $conn);

$lista_paz_farmaco = array();
while ($row = mssql_fetch_assoc($_farm_paz_rs)) {
	$lista_paz_farmaco[] = $row['id_paziente'];
}

$_sql_select = '';
$_sql_inner = '';

if(isset($_POST['id_reparto']) && !empty($_POST['id_reparto'])) {
	$_sql_select = ", rep_paz.id_reparto";
	$_sql_inner = "INNER JOIN (
						SELECT DISTINCT rep_paz.id_paziente, rep_paz.id_reparto
						FROM reparti_pazienti rep_paz
						INNER JOIN reparti rep ON rep.id = rep_paz.id_reparto
						WHERE rep.deleted = 0 AND rep.status = 2 AND sede = (SELECT sede FROM reparti WHERE id = " . $_POST['id_reparto'] . ")
					) as rep_paz ON rep_paz.id_paziente = paz.IdUtente";
}

$_user_sql = "SELECT DISTINCT paz.IdUtente, YEAR(paz.DataNascita) as AnnoNasciata, CONCAT(paz.Cognome, ' ',paz.Nome) as Utente $_sql_select
				FROM utenti as paz
				INNER JOIN (
					select idutente
					from utenti_cartelle
					where idutente not in (605) AND idregime IN (52,53,59,60,61,62,64,65) AND data_chiusura IS NULL AND stato = 1 AND cancella = 'n'
				) as paz_cart_inf ON paz_cart_inf.idutente = paz.IdUtente
				$_sql_inner
				WHERE paz.cancella = 'n' AND paz.stato = 1 AND paz.cancella = 'n'
				ORDER BY Utente, AnnoNasciata ASC";
$_rs_users = mssql_query($_user_sql, $conn);

$utenti = array();
while ($row = mssql_fetch_assoc($_rs_users)) {
	$row['associato'] = in_array($row['IdUtente'], $lista_paz_farmaco) ? true : false;
	$utenti[] = $row;
}

echo json_encode($utenti);