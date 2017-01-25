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
		if (!empty($form_data['login']) && !empty($form_data['email']) && !empty($form_data['role'])) 
		{
			$id = $form_data['id'];
			$login = $form_data['login'];
			$email = $form_data['email'];
			$role = $form_data['role'];
			$active = $form_data['active'] == 'true' ? 1 : 0;

			$query = ' SELECT COUNT(*) AS counter FROM users' .
			'          WHERE (login = :login OR email = :email) AND id <> :id';

			$statement = $dbc->prepare($query);

			$statement->bindParam(':login', $login, PDO::PARAM_STR);
			$statement->bindParam(':email', $email, PDO::PARAM_STR);
			$statement->bindParam(':id', $id, PDO::PARAM_INT);
			
			$statement->execute();
			
			$row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($row_item['counter'] == 0)
			{
				if ($form_data['author'] == 1) // superadmin
				{
					$query = ' UPDATE users SET login = :login, email = :email, role = :role, active = :active' .
					'          WHERE id = :id';

					$statement = $dbc->prepare($query);

					$statement->bindParam(':login', $login, PDO::PARAM_STR);
					$statement->bindParam(':email', $email, PDO::PARAM_STR);
					$statement->bindParam(':role', $role, PDO::PARAM_INT);
					$statement->bindParam(':active', $active, PDO::PARAM_INT);
					$statement->bindParam(':id', $id, PDO::PARAM_INT);

					$statement->execute();
				}
				else // not superadmin
				{
					$query = ' UPDATE users SET login = :login, email = :email, active = :active' .
					'          WHERE id = :id';

					$statement = $dbc->prepare($query);

					$statement->bindParam(':login', $login, PDO::PARAM_STR);
					$statement->bindParam(':email', $email, PDO::PARAM_STR);
					$statement->bindParam(':active', $active, PDO::PARAM_INT);
					$statement->bindParam(':id', $id, PDO::PARAM_INT);

					$statement->execute();
				}

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
