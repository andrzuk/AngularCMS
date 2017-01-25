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

	if ($author == 1 && $id != 1) // superadmin and selected other account
	{
		$user_resources = 0;

		// sprawdza, czy istnieją zasoby będące własnością usera:

		$query = 'SELECT COUNT(*) AS licznik FROM pages WHERE author_id = :author_id';

		$statement = $dbc->prepare($query);
		
		$statement->bindValue(':author_id', $id, PDO::PARAM_INT); 

		$statement->execute();
		
		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$user_resources += $result['licznik'];

		$query = 'SELECT COUNT(*) AS licznik FROM images WHERE owner_id = :owner_id';

		$statement = $dbc->prepare($query);
		
		$statement->bindValue(':owner_id', $id, PDO::PARAM_INT); 

		$statement->execute();
		
		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$user_resources += $result['licznik'];

		if ($user_resources == 0) // nie ma swoich zasobów
		{
			$query = 'DELETE FROM access_rights WHERE user_id = :user_id';

			$statement = $dbc->prepare($query);

			$statement->bindValue(':user_id', $id, PDO::PARAM_INT); 

			$statement->execute();

			$query = 'DELETE FROM users WHERE id = :id';

			$statement = $dbc->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 

			$statement->execute();

			if ($statement->rowCount())
			{
				$message = 'Rekord został poprawnie usunięty.';
				$success = true;
			}
			else
			{
				$message = 'Rekord nie został usunięty.';
				$success = false;
			}
		}
		else // ma swoje zasoby
		{
			$message = 'Użytkownik posiada w systemie swoje zasoby.';
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
