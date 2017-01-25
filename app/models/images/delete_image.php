<?php

include dirname(__FILE__) . '/../../db/config.php';
include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'DELETE FROM images WHERE id = :id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT); 

	$statement->execute();

	// usuwa pliki oryginalu z dysku serwera:
	$delete_result_orig = unlink(GALLERY_DIR . $id);

	// usuwa plik thumbnail z dysku serwera:
	$delete_result_thumb = unlink(THUMB_DIR . $id);

	if ($statement->rowCount() && $delete_result_orig && $delete_result_thumb)
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
