<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if (!empty($form_data['id'])) 
	{
		$id = $form_data['id'];

		$query = 'SELECT * FROM pages WHERE id = :id';

		$statement = $dbc->prepare($query);

		$statement->bindValue(':id', $id, PDO::PARAM_INT); 

		$statement->execute();

		$original_row_item = $statement->fetch(PDO::FETCH_ASSOC);

		// sprawdza, czy istnieje już kopia rekordu:

		$modified = $original_row_item['modified'];

		$query = 'SELECT COUNT(*) AS licznik FROM archives WHERE page_id = :page_id AND modified = :modified ';

		$statement = $dbc->prepare($query);
		
		$statement->bindValue(':page_id', $id, PDO::PARAM_INT); 
		$statement->bindValue(':modified', $modified, PDO::PARAM_STR); 

		$statement->execute();
		
		$result = $statement->fetch(PDO::FETCH_ASSOC);

		if ($result['licznik'] == 0) // nie ma jeszcze kopii rekordu
		{
			$query = 'INSERT INTO archives' .
			'         (page_id, main_page, contact_page, category_id, title, description, contents, author_id, visible, modified) VALUES' .
			'         (:page_id, :main_page, :contact_page, :category_id, :title, :description, :contents, :author_id, :visible, :modified)';

			$statement = $dbc->prepare($query);

			$statement->bindValue(':page_id', $original_row_item['id'], PDO::PARAM_INT); 
			$statement->bindValue(':main_page', $original_row_item['main_page'], PDO::PARAM_INT); 
			$statement->bindValue(':contact_page', $original_row_item['contact_page'], PDO::PARAM_INT); 
			$statement->bindValue(':category_id', $original_row_item['category_id'], PDO::PARAM_INT); 
			$statement->bindValue(':title', $original_row_item['title'], PDO::PARAM_STR); 
			$statement->bindValue(':description', $original_row_item['description'], PDO::PARAM_STR); 
			$statement->bindValue(':contents', $original_row_item['contents'], PDO::PARAM_STR); 
			$statement->bindValue(':author_id', $original_row_item['author_id'], PDO::PARAM_INT); 
			$statement->bindValue(':visible', $original_row_item['visible'], PDO::PARAM_INT); 
			$statement->bindValue(':modified', $original_row_item['modified'], PDO::PARAM_STR); 
			
			$statement->execute();
			
			$inserted_id = $dbc->lastInsertId();
		}

		if ($inserted_id)
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
