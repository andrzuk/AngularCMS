<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if (!empty($form_data['client_name']) && !empty($form_data['client_email']) && !empty($form_data['message_content'])) 
	{
		$id = $form_data['id'];
		$client_name = $form_data['client_name'];
		$client_email = $form_data['client_email'];
		$message_content = $form_data['message_content'];
		$requested = $form_data['requested'] == 'true' ? 0 : 1;

		$query = ' UPDATE messages SET client_name = :client_name, client_email = :client_email, message_content = :message_content, requested = :requested, close_date = NOW()' .
		'          WHERE id = :id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':client_name', $client_name, PDO::PARAM_STR);
		$statement->bindParam(':client_email', $client_email, PDO::PARAM_STR);
		$statement->bindParam(':message_content', $message_content, PDO::PARAM_STR);
		$statement->bindParam(':requested', $requested, PDO::PARAM_INT);
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
