<?php

include dirname(__FILE__) . '/../db/connection.php';

$credentials = $_POST;

$user_id = 0;
$login = NULL;
$token = NULL;
$message = NULL;
$success = false;

if (!empty($credentials['login']) && !empty($credentials['email']) && !empty($credentials['password'])) 
{
	$login = $credentials['login'];
	$email = $credentials['email'];
	$password = sha1($credentials['password']);
	
	$db_connection = connect();

	$query = ' SELECT COUNT(*) AS counter FROM users' .
	'          WHERE login = :login OR email = :email';

	$statement = $db_connection->prepare($query);

	$statement->bindParam(':login', $login, PDO::PARAM_STR);
	$statement->bindParam(':email', $email, PDO::PARAM_STR);
	
	$statement->execute();
	
	$row_item = $statement->fetch(PDO::FETCH_ASSOC);

	if (is_array($row_item))
	{
		if ($row_item['counter'] == 0)
		{
			$role = 3;
			$active = 1;
			$token = hash('sha256', uniqid());

			$query = ' INSERT INTO users (login, password, email, role, active, registered, logged_in, token)' .
			'          VALUES (:login, :password, :email, :role, :active, NOW(), NOW(), :token)';

			$statement = $db_connection->prepare($query);

			$statement->bindParam(':login', $login, PDO::PARAM_STR);
			$statement->bindParam(':password', $password, PDO::PARAM_STR);
			$statement->bindParam(':email', $email, PDO::PARAM_STR);
			$statement->bindParam(':role', $role, PDO::PARAM_INT);
			$statement->bindParam(':active', $active, PDO::PARAM_INT);
			$statement->bindParam(':token', $token, PDO::PARAM_STR);

			$statement->execute();

			$user_id = $db_connection->lastInsertId();

			// dopisuje prawa dostępu zgodnie z nadaną rolą:

			$role_mask = array(1 => 'mask_a', 2 => 'mask_o', 3 => 'mask_u');

			$query = ' INSERT INTO access_rights (user_id, resource_id, access)' .
			'          SELECT :user_id AS user_id, id as resource_id, ' . $role_mask[$role] . ' as access FROM access_levels ORDER BY id';

			$statement = $db_connection->prepare($query);

			$statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);

			$statement->execute();

			$message = 'Zostałeś poprawnie zarejestrowany do serwisu.';
			$success = true;
		}
		else 
		{
			$message = 'Podany login lub e-mail już istnieje.';
		}
	}
}

$result = array('user_id' => $user_id, 'login' => $login, 'token' => $token, 'success' => $success, 'message' => $message);

echo json_encode($result);

?>

