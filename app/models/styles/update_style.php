<?php

include dirname(__FILE__) . '/../../db/config.php';
include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	$styles_file_name = CSS_DIR . 'app.css';

	if (!empty($form_data['file_contents'])) 
	{
		$file_contents = $form_data['file_contents'];

		$saved = file_put_contents($styles_file_name, $file_contents);

		if ($saved !== FALSE)
		{
			$message = 'Style zostały poprawnie zapisane.';
			$success = true;
		}
		else
		{
			$message = 'Style nie zostały zapisane.';
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
