<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);
	$author = intval($_GET['author']);
	$token = 'Logged out';

	if ($author == 1) // superadmin
	{
		$query = 'UPDATE users SET token = :token WHERE id = :id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':token', $token, PDO::PARAM_STR);
		$statement->bindValue(':id', $id, PDO::PARAM_INT); 

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
