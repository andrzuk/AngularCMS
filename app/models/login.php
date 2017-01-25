<?php

include dirname(__FILE__) . '/../db/connection.php';

$credentials = $_POST;

$id = 0;
$login = NULL;
$token = NULL;
$message = NULL;
$success = false;

if (!empty($credentials['username']) && !empty($credentials['password'])) 
{
	$login = $credentials['username'];
	$password = sha1($credentials['password']);
	
	$db_connection = connect();

	$query = ' SELECT * FROM users' .
	'          WHERE (login = :login OR email = :login)' .
	'          AND password = :password '.
	'          AND active = 1';

	$statement = $db_connection->prepare($query);

	$statement->bindParam(':login', $login, PDO::PARAM_STR);
	$statement->bindParam(':password', $password, PDO::PARAM_STR);
	
	$statement->execute();
	
	$row_item = $statement->fetch(PDO::FETCH_ASSOC);

	if (is_array($row_item))
	{
		if (array_key_exists('id', $row_item))
		{
			$id = $row_item['id'];
			$token = hash('sha256', uniqid());
			$success = true;
			$message = 'Zostałeś poprawnie zalogowany do serwisu.';

			$query = ' UPDATE users' .
			'          SET logged_in = NOW(), token = :token' .
			'          WHERE id = :id';

			$statement = $db_connection->prepare($query);

			$statement->bindParam(':token', $token, PDO::PARAM_STR);
			$statement->bindParam(':id', $id, PDO::PARAM_INT);

			$statement->execute();
		}
	}
	else
	{
		$message = 'Login lub e-mail lub hasło są nieprawidłowe.';
	}
}

$result = array('user_id' => $id, 'username' => $login, 'token' => $token, 'success' => $success, 'message' => $message);

echo json_encode($result);

?>
