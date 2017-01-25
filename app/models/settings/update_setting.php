<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if (!empty($form_data['key_name']) && !empty($form_data['key_value']) && !empty($form_data['meaning'])) 
	{
		$id = $form_data['id'];
		$key_name = $form_data['key_name'];
		$key_value = $form_data['key_value'];
		$meaning = $form_data['meaning'];

		$query = ' SELECT COUNT(*) AS counter FROM settings' .
		'          WHERE key_name = :key_name AND id <> :id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':key_name', $key_name, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		
		$statement->execute();
		
		$row_item = $statement->fetch(PDO::FETCH_ASSOC);

		if ($row_item['counter'] == 0)
		{
			$query = ' UPDATE settings SET key_name = :key_name, key_value = :key_value, meaning = :meaning, modified = NOW()' .
			'          WHERE id = :id';

			$statement = $dbc->prepare($query);

			$statement->bindParam(':key_name', $key_name, PDO::PARAM_STR);
			$statement->bindParam(':key_value', $key_value, PDO::PARAM_STR);
			$statement->bindParam(':meaning', $meaning, PDO::PARAM_STR);
			$statement->bindParam(':id', $id, PDO::PARAM_INT);

			$statement->execute();

			if ($statement->rowCount())
			{
				$message = 'Rekord został poprawnie zapisany.';
				$success = true;
			}
			else
			{
				$message = 'Rekord nie został zapisany.';
				$success = false;
			}
		}
		else
		{
			$message = 'Wartość unikalnego klucza się powtarza.';
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

