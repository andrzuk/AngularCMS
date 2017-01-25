<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if (!empty($form_data['id']) && !empty($form_data['archive_id'])) 
	{
		$id = $form_data['id'];
		$archive_id = $form_data['archive_id'];

		$query = 'SELECT * FROM archives WHERE id = :id';

		$statement = $dbc->prepare($query);
		
		$statement->bindValue(':id', $archive_id, PDO::PARAM_INT); 

		$statement->execute();
		
		$archive_row_item = $statement->fetch(PDO::FETCH_ASSOC);

		if ($archive_row_item) // przywraca rekord z archiwum
		{
			$query = 'UPDATE pages' .
			'         SET main_page = :main_page, contact_page = :contact_page, category_id = :category_id,' .
			'         title = :title, description = :description, contents = :contents,' .
			'         author_id = :author_id, visible = :visible, modified = :modified' .
			'         WHERE id = :id';

			$statement = $dbc->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':main_page', $archive_row_item['main_page'], PDO::PARAM_INT); 
			$statement->bindValue(':contact_page', $archive_row_item['contact_page'], PDO::PARAM_INT); 
			$statement->bindValue(':category_id', $archive_row_item['category_id'], PDO::PARAM_INT); 
			$statement->bindValue(':title', $archive_row_item['title'], PDO::PARAM_STR); 
			$statement->bindValue(':description', $archive_row_item['description'], PDO::PARAM_STR); 
			$statement->bindValue(':contents', $archive_row_item['contents'], PDO::PARAM_STR); 
			$statement->bindValue(':author_id', $archive_row_item['author_id'], PDO::PARAM_INT); 
			$statement->bindValue(':visible', $archive_row_item['visible'], PDO::PARAM_INT); 
			$statement->bindValue(':modified', $archive_row_item['modified'], PDO::PARAM_STR); 
			
			$statement->execute();
			
			$affected_rows = $statement->rowCount();
		}

		if ($affected_rows)
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
