<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if ($form_data['author'] == 1) // superadmin
	{
		if (!empty($form_data['login']) && !empty($form_data['email']) && !empty($form_data['role']) && !empty($form_data['password'])) 
		{
			$login = $form_data['login'];
			$email = $form_data['email'];
			$role = $form_data['role'];
			$password = sha1($form_data['password']);
			$token = 'New User';
			$active = $form_data['active'] == 'true' ? 1 : 0;

			$query = ' SELECT COUNT(*) AS counter FROM users' .
			'          WHERE login = :login OR email = :email';

			$statement = $dbc->prepare($query);

			$statement->bindParam(':login', $login, PDO::PARAM_STR);
			$statement->bindParam(':email', $email, PDO::PARAM_STR);
			
			$statement->execute();
			
			$row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($row_item['counter'] == 0)
			{
				$query = ' INSERT INTO users (login, password, email, role, active, logged_in, token)' .
				'          VALUES (:login, :password, :email, :role, :active, NOW(), :token)';

				$statement = $dbc->prepare($query);

				$statement->bindParam(':login', $login, PDO::PARAM_STR);
				$statement->bindParam(':password', $password, PDO::PARAM_STR);
				$statement->bindParam(':email', $email, PDO::PARAM_STR);
				$statement->bindParam(':role', $role, PDO::PARAM_INT);
				$statement->bindParam(':active', $active, PDO::PARAM_INT);
				$statement->bindParam(':token', $token, PDO::PARAM_STR);

				$statement->execute();

				$inserted_id = $dbc->lastInsertId();

				// dopisuje prawa dostępu zgodnie z nadaną rolą:

				$role_mask = array(1 => 'mask_a', 2 => 'mask_o', 3 => 'mask_u');

				$query = ' INSERT INTO access_rights (user_id, resource_id, access)' .
				'          SELECT :user_id AS user_id, id as resource_id, ' . $role_mask[$role] . ' as access FROM access_levels ORDER BY id';

				$statement = $dbc->prepare($query);

				$statement->bindParam(':user_id', $inserted_id, PDO::PARAM_INT);

				$statement->execute();

				if ($statement->rowCount())
				{
					$message = 'Rekord został poprawnie dopisany.';
					$success = true;
				}
				else
				{
					$message = 'Rekord nie został dopisany.';
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
	else // not superadmin
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

