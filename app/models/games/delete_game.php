<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'DELETE FROM _game_stats WHERE id = :id';

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
else
{
	$message = 'Brak uprawnień do wykonania uruchomionej akcji.';
	$success = false;
}

$result = array('success' => $success, 'message' => $message);

echo json_encode($result);

?>
