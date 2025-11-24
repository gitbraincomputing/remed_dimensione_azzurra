<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);


if(isset($_GET['usephp']) && $_GET['usephp']=='7')
{
	// in produzione carichiamo le configurazioni dal file config.rdm.php generale
	$productionConfig = join(DIRECTORY_SEPARATOR, array('C:','web','Republic','include','reserved','config.rdm.php'));
	require_once($productionConfig);
	
	
	// cerco le prenotazioni
	$stmt= "SELECT id, compilationDate, startTime, endTime, deleted_at FROM cup_regionale_prenotazioni";
	$result = $conn->query($stmt)->fetchAll(PDO::FETCH_ASSOC);
	
	$i = 0;
	if (count($result) > 0) 
	{
		foreach($result as $record)
		{
			$id = $record['id'];
			
			$compilationDate = new DateTime($record['compilationDate'], new DateTimeZone('UTC'));
			$compilationDate->setTimezone(new DateTimeZone('Europe/Rome'));
			$compilationDate = $compilationDate->format('Y-m-d H:i:s');
			
			$startTime = new DateTime($record['startTime'], new DateTimeZone('UTC'));
			$startTime->setTimezone(new DateTimeZone('Europe/Rome'));
			$startTime = $startTime->format('Y-m-d H:i:s');
			
			$endTime = new DateTime($record['endTime'], new DateTimeZone('UTC'));
			$endTime->setTimezone(new DateTimeZone('Europe/Rome'));
			$endTime = $endTime->format('Y-m-d H:i:s');	

			if(!empty($record['deleted_at'])) {
				$deleted_at = new DateTime($record['deleted_at'], new DateTimeZone('UTC'));
				$deleted_at->setTimezone(new DateTimeZone('Europe/Rome'));
				$deleted_at = $deleted_at->format('Y-m-d H:i:s');
			} else {
				$deleted_at = NULL;
			}
			

		
			$stmt_aggiorna = "UPDATE cup_regionale_prenotazioni 
								SET
									compilationDate	= CONVERT(DATETIME, :compilationDate, 102),
									startTime 		= CONVERT(DATETIME, :startTime, 102),
									endTime 		= CONVERT(DATETIME, :endTime, 102),
									deleted_at 		= CONVERT(DATETIME, :deleted_at, 102)									
								WHERE
									id = :id";

			$stmt_aggiorna = $conn->prepare($stmt_aggiorna);
			
			$stmt_aggiorna->bindParam(':compilationDate', $compilationDate);
			$stmt_aggiorna->bindParam(':startTime', $startTime);
			$stmt_aggiorna->bindParam(':endTime', $endTime);
			$stmt_aggiorna->bindParam(':deleted_at', $deleted_at);
			$stmt_aggiorna->bindParam(':id', $id);
			$stmt_aggiorna->execute();
			$i++;
		}
	}
	echo "Aggiornate $i prenotazioni<br>";
	
	
	
	
	
	// cerco la prestazioni
	$stmt= "SELECT id, compilationDate, scheduledDatetime, deleted_at FROM cup_regionale_prestazioni";
	$result = $conn->query($stmt)->fetchAll(PDO::FETCH_ASSOC);
	$i = 0;
	
	if (count($result) > 0) 
	{
		foreach($result as $record)
		{
			$id = $record['id'];
			
			$compilationDate = new DateTime($record['compilationDate'], new DateTimeZone('UTC'));
			$compilationDate->setTimezone(new DateTimeZone('Europe/Rome'));
			$compilationDate = $compilationDate->format('Y-m-d H:i:s');
			
			$scheduledDatetime = new DateTime($record['scheduledDatetime'], new DateTimeZone('UTC'));
			$scheduledDatetime->setTimezone(new DateTimeZone('Europe/Rome'));
			$scheduledDatetime = $scheduledDatetime->format('Y-m-d H:i:s');
			
			if(!empty($record['deleted_at'])) {
				$deleted_at = new DateTime($record['deleted_at'], new DateTimeZone('UTC'));
				$deleted_at->setTimezone(new DateTimeZone('Europe/Rome'));
				$deleted_at = $deleted_at->format('Y-m-d H:i:s');
			} else {
				$deleted_at = NULL;
			}

		
			$stmt_aggiorna = "UPDATE cup_regionale_prestazioni 
								SET
									compilationDate		= CONVERT(DATETIME, :compilationDate, 102),
									scheduledDatetime 	= CONVERT(DATETIME, :scheduledDatetime, 102),
									deleted_at 			= CONVERT(DATETIME, :deleted_at, 102)
								WHERE
									id = :id";

			$stmt_aggiorna = $conn->prepare($stmt_aggiorna);
			
			$stmt_aggiorna->bindParam(':compilationDate', $compilationDate);
			$stmt_aggiorna->bindParam(':scheduledDatetime', $scheduledDatetime);
			$stmt_aggiorna->bindParam(':deleted_at', $deleted_at);
			$stmt_aggiorna->bindParam(':id', $id);
			$stmt_aggiorna->execute();
			$i++;
		}
	}
	echo "Aggiornate $i prestazioni<br>";
}