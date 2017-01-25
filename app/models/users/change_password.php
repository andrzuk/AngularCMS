<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if ($form_data['id'] == $form_data['author'] || $form_data['author'] == 1) // author is accessing to his own accout or superadmin - all right
	{
		if (!empty($form_data['password_set']) && !empty($form_data['password_repeat'])) 
		{
			$id = $form_data['id'];
			$password_set = sha1($form_data['password_set']);
			$password_repeat = sha1($form_data['password_repeat']);

			if ($password_set == $password_repeat)
			{
				$query = ' UPDATE users SET password = :password' .
				'          WHERE id = :id';

				$statement = $dbc->prepare($query);

				$statement->bindParam(':password', $password_set, PDO::PARAM_STR);
				$statement->bindParam(':id', $id, PDO::PARAM_INT);

				$statement->execute();

				if ($statement->rowCount())
				{
					$message = 'Hasło zostało poprawnie zapisane.';
					$success = true;
				}
				else
				{
					$message = 'Hasło nie zostało zmienione.';
					$success = false;
				}
			}
			else
			{
				$message = 'Wprowadzone wartości różnią się.';
				$success = false;
			}
		}
		else
		{
			$message = 'Brak wymaganych danych.';
			$success = false;
		}
	}
	else // author is accessing to other's accout - forbidden
	{
		$message = 'Brak uprawnień do wykonania uruchomionej akcji.';
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
