<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

$id_permesso = 12;
session_start();

if (isset($_SESSION['UTENTE'])) {

    if (true || in_array($id_permesso, $_SESSION['PERMESSI'])) {

        switch ($_POST['action']) {

            case "create":
                if (true || $_SESSION['UTENTE']->controlla_permessi($id_permesso, 1)) {
                    create();
                } else {
                    error_message("Permessi insufficienti per questa operazione!");
                }
                break;

            case "update":
                if (true || $_SESSION['UTENTE']->controlla_permessi($id_permesso, 2)) {
                    update($_POST['id']);
                } else {
                    error_message("Permessi insufficienti per questa operazione!");
                }
                break;

            case "connect_to_patients":
                if (true || $_SESSION['UTENTE']->controlla_permessi($id_permesso, 2)) {
                    connect_to_patients();
                } else {
                    error_message("Permessi insufficienti per questa operazione!");
                }
                break;

            case "delete":
                if (true || $_SESSION['UTENTE']->controlla_permessi($id_permesso, 3)) {
                    del($_POST['id']);
                } else {
                    error_message("Permessi insufficienti per questa operazione!");
                }
                break;
        }
    } else {
        error_message("Non hai i permessi per visualizzare questa pagina!");
        go_home();
    }
} else {
    ob_start();
    Header("Location: index.php");
    ob_end_flush();
}


function create()
{
    $conn = db_connect();

    $nome = trim($_POST['nome']);
    $sede_reparto = $_POST['sede_reparto'];
    $stato_reparto = $_POST['stato_reparto'];
    $ope_ins = $_SESSION['UTENTE']->_properties['uid'];

    $_sql = "SELECT 1 as found
			FROM reparti
			WHERE nome = '$nome' AND sede = '$sede_reparto'";
    $_rs = mssql_query($_sql, $conn);
    $row = mssql_fetch_assoc($_rs);

    if (empty($row['found'])) {
        $_sql = "INSERT INTO reparti (nome, sede, ope_ins, status)
					VALUES ('$nome', '$sede_reparto', $ope_ins, $stato_reparto)";
        $_rs = mssql_query($_sql, $conn);

        if (!empty($_POST['operatori'])) {
            foreach ($_POST['operatori'] as $op) {
                $_sql = "INSERT INTO reparti_operatori (id_reparto, id_operatore, ope_ins)
							VALUES ((SELECT id FROM reparti WHERE nome = '$nome' AND sede = '$sede_reparto' AND ope_ins = $ope_ins), $op, $ope_ins)";
                $_rs = mssql_query($_sql, $conn);
            }
        }

        if (!empty($_POST['pazienti'])) {
            foreach ($_POST['pazienti'] as $paz) {
                $_sql = "INSERT INTO reparti_pazienti (id_reparto, id_paziente, ope_ins)
							VALUES ((SELECT id FROM reparti WHERE nome = '$nome' AND sede = '$sede_reparto' AND ope_ins = $ope_ins), $paz, $ope_ins)";
                $_rs = mssql_query($_sql, $conn);
            }
        }
    }

    echo ("ok;;1;lista_reparti.php");
    exit();
}


function update()
{
    $conn = db_connect();

    $nome = trim($_POST['nome']);
	$nome_old = $_POST['nome_old'];
    $sede_reparto = $_POST['sede_reparto'];
	$sede_reparto_old = $_POST['sede_reparto_old'];
    $stato_reparto = $_POST['stato_reparto'];	
    $curr_ope = $_SESSION['UTENTE']->_properties['uid'];
	
	$found = null;

	if ($nome_old != $nome || $sede_reparto_old != $sede_reparto) {
		$_sql = "SELECT 1 as found
				FROM reparti
				WHERE nome = '$nome' AND sede = '$sede_reparto'";
		$_rs = mssql_query($_sql, $conn);
		$row = mssql_fetch_assoc($_rs);
		$found = $row['found'];
	}

    if (empty($found)) {
        $_sql = "UPDATE reparti
				SET nome = '$nome', sede = '$sede_reparto', status = $stato_reparto, ope_agg = $curr_ope, data_agg = GETDATE()
				WHERE id = " . $_POST['id'];
        $_rs = mssql_query($_sql, $conn);
        mssql_free_result($_rs);

        mssql_query("DELETE FROM reparti_pazienti WHERE id_reparto = " . $_POST['id'], $conn);
        mssql_query("DELETE FROM reparti_operatori WHERE id_reparto = " . $_POST['id'], $conn);

        if (isset($_POST['operatori'])) {
            foreach ($_POST['operatori'] as $op) {
                $_sql = "INSERT INTO reparti_operatori (id_reparto, id_operatore, ope_ins)
							VALUES (" . $_POST['id'] . ", $op, $curr_ope)";
                $_rs = mssql_query($_sql, $conn);				
            }
        }

        if (isset($_POST['pazienti'])) {
			
            foreach ($_POST['pazienti'] as $paz) {
                $_sql = "INSERT INTO reparti_pazienti (id_reparto, id_paziente, ope_ins)
							VALUES (" . $_POST['id'] . ", $paz, $curr_ope)";
                $_rs = mssql_query($_sql, $conn);
            }
        }
    }

    echo ("ok;;1;lista_reparti.php");
    exit();
}


function del()
{
    $conn = db_connect();
    $ope_agg = $_SESSION['UTENTE']->_properties['uid'];

    $_sql = "UPDATE reparti				
				SET status = 1, deleted = 1, ope_agg = $ope_agg, data_agg = GETDATE()
				WHERE id = " . $_POST['id'];
    $_rs = mssql_query($_sql, $conn);

    if ($_rs) {
        $_sql = "DELETE FROM reparti_pazienti				
					WHERE id_reparto = " . $_POST['id'];
        mssql_query($_sql, $conn);

        $_sql = "DELETE FROM reparti_operatori				
					WHERE id_reparto = " . $_POST['id'];
        mssql_query($_sql, $conn);
    }

    if (!$_rs) {
        echo 'false';
    } else {
        echo 'true';
    }
}
