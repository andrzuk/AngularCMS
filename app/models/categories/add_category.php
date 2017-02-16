<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if (!empty($form_data['caption']) && !empty($form_data['item_link'])) 
	{
		$caption = $form_data['caption'];
		$item_link = $form_data['item_link'];
		$item_order = intval($form_data['item_order']);
		$target = $form_data['target'] == 'true' ? 1 : 0;
		$visible = $form_data['visible'] == 'true' ? 1 : 0;

		$query = ' INSERT INTO categories (parent_id, item_order, caption, item_link, visible, target, modified)' .
		'          VALUES (0, :item_order, :caption, :item_link, :visible, :target, NOW())';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':item_order', $item_order, PDO::PARAM_INT);
		$statement->bindParam(':caption', $caption, PDO::PARAM_STR);
		$statement->bindParam(':item_link', $item_link, PDO::PARAM_STR);
		$statement->bindParam(':target', $target, PDO::PARAM_INT);
		$statement->bindParam(':visible', $visible, PDO::PARAM_INT);

		$statement->execute();

		$inserted_id = $dbc->lastInsertId();

		if ($form_data['auto_link'] == 'true')
		{
			$item_link = '/category/' . $inserted_id;
			
			$query = ' UPDATE categories SET item_link = :item_link' .
			'          WHERE id = :id';

			$statement = $dbc->prepare($query);

			$statement->bindParam(':item_link', $item_link, PDO::PARAM_STR);
			$statement->bindParam(':id', $inserted_id, PDO::PARAM_INT);

			$statement->execute();
		}

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

