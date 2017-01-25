<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if (!empty($form_data['title']) && !empty($form_data['description']) && !empty($form_data['contents'])) 
	{
		$id = $form_data['id'];
		$title = $form_data['title'];
		$description = $form_data['description'];
		$contents = $form_data['contents'];
		$category_id = intval($form_data['category_id']);
		$main_page = $form_data['main_page'] == 'true' ? 1 : 0;
		$contact_page = $form_data['contact_page'] == 'true' ? 1 : 0;
		$visible = $form_data['visible'] == 'true' ? 1 : 0;

		$user = get_user_by_token($dbc);

		$author_id = $user['id'];

		$query = ' UPDATE pages SET main_page = :main_page, contact_page = :contact_page, category_id = :category_id, title = :title, description = :description, contents = :contents, author_id = :author_id, visible = :visible, modified = NOW()' .
		'          WHERE id = :id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':main_page', $main_page, PDO::PARAM_INT);
		$statement->bindParam(':contact_page', $contact_page, PDO::PARAM_INT);
		$statement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
		$statement->bindParam(':title', $title, PDO::PARAM_STR);
		$statement->bindParam(':description', $description, PDO::PARAM_STR);
		$statement->bindParam(':contents', $contents, PDO::PARAM_STR);
		$statement->bindParam(':author_id', $author_id, PDO::PARAM_INT);
		$statement->bindParam(':visible', $visible, PDO::PARAM_INT);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);

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
