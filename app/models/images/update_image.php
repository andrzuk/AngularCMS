<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_FILES;
	$id = $_GET['id'];

	$file_name = $form_data['file']['name'];
	$file_type = $form_data['file']['type'];
	$file_size = $form_data['file']['size'];
	$local_file = $form_data['file']['tmp_name'];
	$error = $form_data['file']['error'];

	$file_name = str_replace(array('-', '_'), ' ', $file_name);

	if ($file_size && !$error && !empty($file_name) && !empty($file_type) && !empty($local_file)) 
	{
		if (substr($file_type, 0, 5) == 'image') // plik graficzny
		{
			list($picture_width, $picture_height) = getimagesize($local_file);

			$user = get_user_by_token($dbc);

			$owner_id = $user['id'];

			$query = ' UPDATE images SET owner_id = :owner_id, file_format = :file_format, file_name = :file_name, file_size = :file_size, picture_width = :picture_width, picture_height = :picture_height, modified = NOW()' .
			'          WHERE id = :id';

			$statement = $dbc->prepare($query);

			$statement->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
			$statement->bindParam(':file_format', $file_type, PDO::PARAM_STR);
			$statement->bindParam(':file_name', $file_name, PDO::PARAM_STR);
			$statement->bindParam(':file_size', $file_size, PDO::PARAM_INT);
			$statement->bindParam(':picture_width', $picture_width, PDO::PARAM_INT);
			$statement->bindParam(':picture_height', $picture_height, PDO::PARAM_INT);
			$statement->bindParam(':id', $id, PDO::PARAM_INT);

			$statement->execute();

			// zapisuje oryginalny obrazek na serwer:
			move_uploaded_file($local_file, GALLERY_DIR . $id);

			// generuje thumb obrazka:
			$thumbnail_width = intval(THUMB_WIDTH);
			$thumbnail_height = intval(THUMB_HEIGHT);

			$image_details = getimagesize(GALLERY_DIR . $id); 
			$original_width = $image_details[0];
			$original_height = $image_details[1];

			if ($image_details[2] == 1) 
			{
				$imgcreateto = "ImageGIF";
				$imgcreatefrom = "ImageCreateFromGIF";
			}
			if ($image_details[2] == 2) 
			{
				$imgcreateto = "ImageJPEG";
				$imgcreatefrom = "ImageCreateFromJPEG";
			}
			if ($image_details[2] == 3) 
			{
				$imgcreateto = "ImagePNG";
				$imgcreatefrom = "ImageCreateFromPNG";
			}
			// zapisuje thumb obrazka na serwerze:
			if ($imgcreateto) 
			{
				$old_image = $imgcreatefrom(GALLERY_DIR . $id);
				$new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
				$background = imagecolorallocate($new_image, 255, 255, 255);
				imagefill($new_image, 0, 0, $background);
				imagecopyresized($new_image, $old_image, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $original_width, $original_height);
				$imgcreateto($new_image, THUMB_DIR . $id);
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
			$message = 'Wybrany plik nie jest grafiką.';
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

