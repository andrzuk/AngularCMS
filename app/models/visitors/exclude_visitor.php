<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$ip = $_GET['ip'];
	$key_name = 'visitors_excluded';

	if (!empty($ip)) 
	{
		$query = " UPDATE settings SET key_value = CONCAT(key_value, ', \'". $ip ."\''), modified = NOW()" .
		"          WHERE key_name = :key_name";

		$statement = $dbc->prepare($query);

		$statement->bindParam(':key_name', $key_name, PDO::PARAM_STR);

		$statement->execute();

		if ($statement->rowCount())
		{
			$message = 'Wykluczenie zostało poprawnie dodane.';
			$success = true;
		}
		else
		{
			$message = 'Wykluczenie nie zostało dodane.';
			$success = false;
		}
	}
	else
	{
		$message = 'Brak wymaganych danych.';
		$success = false;
	}
}
else
{
	$message = 'Brak uprawnień do wykonania uruchomionej akcji.';
	$success = false;
}

$result = array('success' => $success, 'message' => $message);

echo json_encode($result);

?>
