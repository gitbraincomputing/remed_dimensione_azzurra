
<html>
<head>
	
	<style>
		<style>
    /* Stile delle tabelle di Bootstrap */
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }
    .table th,
    .table td {
        padding: 0.75rem;
        border-top: 1px solid #dee2e6;
    }
    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }
    .table tbody + tbody {
        border-top: 2px solid #dee2e6;
    }

    /* Stile dei pulsanti di Bootstrap */
    .btn {
        display: inline-block;
        font-weight: 400;
        color: #212529;
        text-align: center;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        background-color: transparent;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-success {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-danger {
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-warning {
        color: #212529;
        background-color: #ffc107;
        border-color: #ffc107;
    }
	.btn-full {
        width: 100%;
    }
</style>

	</style>
</head>
<body>

<?php


ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);


if(	isset($_GET['num_ricetta']) && !empty($_GET['num_ricetta']) 	&&
    isset($_GET['usephp']) 		&& $_GET['usephp']=='7'
)
{
	// EROGATO, ANNULLATO, ACCETTATO, NON_EROGATO
	
	// TABELLA CON
	// tbl con data pianificata + lo stato attuale dell'orderId + i bottoni
	
	
	
	//100220000000003
	
	$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','webdia','Republic','include','reserved','config.rdm.php'));
	require_once($productionConfig);

	// cerco tutte le prestazioni per poter procedere con una erogazione parziale
	$stmt_prenotazioni = "SELECT * FROM cup_regionale_prestazioni WHERE prescriptionNumber = '".$_GET['num_ricetta'] ."'";
	$result_stmt_prenotazioni = $conn->query($stmt_prenotazioni)->fetchAll();

	if (count($result_stmt_prenotazioni) == 0) {
		die("<p>Non ci sono prestazioni legate a questa prenotazione.</p>");
	} else {	
	
		echo "<table class='table' border='0' cellspacing='0' cellpadding='0'>
				<thead>
					<tr>
					  <td><b>DATA PIANIFICATA</b></td>
					  <td colspan='4' style='text-align:center'><b>AZIONI</b>	</td>
					</tr>
				</thead>
				<tbody>";
								
		foreach ($result_stmt_prenotazioni as $prestazione) 
		{
			echo "<tr>
					<td>".date('d-m-Y H:i', strtotime($prestazione['scheduledDatetime']))."</td>
					<td><a class='btn btn-success btn-xs' name='btn_action' style='margin: 2px 0; cursor:pointer' orderId='".$prestazione['orderId']."' action='ACCETTATO'>ACCETTATO</a></td>
					<td><a class='btn btn-primary btn-xs' name='btn_action' style='margin: 2px 0; cursor:pointer' orderId='".$prestazione['orderId']."' action='EROGATO'>EROGATO</a></td>
					<td><a class='btn btn-warning btn-xs' name='btn_action' style='margin: 2px 0; cursor:pointer' orderId='".$prestazione['orderId']."' action='NON EROGATO'>NON EROGATO</a></td>
					<td><a class='btn btn-danger btn-xs' name='btn_action' style='margin: 2px 0; cursor:pointer' orderId='".$prestazione['orderId']."' action='ANNULLATO'>ANNULLATO</a></td>
				  </tr>";
		}

		echo "</tbody></table>";
		
	}
}
?>

<script>

document.attachEvent('onreadystatechange', function() 
{
    if (document.readyState === "complete") 
	{	
		var bottoni = document.getElementsByName('btn_action');
		 
		for (var i = 0; i < bottoni.length; i++) 
		{
			bottoni[i].onclick = function(event) 
			{
				var orderStatus = this.action;
				var orderId = this.orderId;
				
				if(orderId > 1)
				{
					if(confirm("Sei sicuro di voler procedere con il comando " + orderStatus + "?"))
					{
						var xhr = new XMLHttpRequest();
						xhr.open('POST', 'sql_erogazione_parziale.php?usephp=7', false);
						xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						xhr.onreadystatechange = function() {
							if (xhr.readyState === 4) {
								if (xhr.status === 200) {
									var response = xhr.responseText;
									if (response === 'null') {
										// handle null response
										alert("I server della Regione Campania non hanno risposto.");
									} else {
										// handle response
										alert(response);
									}
								} else {
									// handle error
									alert("Errore di comunicazione con i server della Regione Campania ("+xhr.status+").");
								}
							}
						};
						xhr.send('orderStatus=' + encodeURIComponent(orderStatus) + '&orderId=' + encodeURIComponent(orderId)) + '&usephp=7' ;
					}
				} else {
					alert("OrderId non valido: " + orderId);
				}
            }
        }
    }
});


</script>
</body>
</html>