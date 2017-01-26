<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);
	$type = $_GET['type'];

	$query = 'SELECT * FROM images WHERE id = :id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);

	$file_name = $result['file_name'];
	$file_format = $result['file_format'];

	$file_name = str_replace(" ", "_", $file_name);

	if ($type == 'original') $picture_name = GALLERY_DIR . $id;
	if ($type == 'thumbnail') $picture_name = THUMB_DIR . $id;
	if (!file_exists($picture_name)) $picture_name = NOT_FOUND;

	// wczytuje plik z serwera:
	$image_data = file_get_contents($picture_name);

	header('content-type: ' . $file_format);

	// koduje dane do Base64:
	$data_encoded = 'data:' . $file_format . ';base64,' . base64_encode($image_data);

	// wysyła zakodowane dane do przeglądarki:
	if (IsSet($image_data))
	{
		$result = array('image_data' => $data_encoded, 'file_name' => $file_name, 'file_format' => $file_format);
		echo json_encode($result);
	}
}

?>
