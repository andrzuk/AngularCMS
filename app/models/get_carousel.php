<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';
include dirname(__FILE__) . '/../db/setting.php';

$result = array();

$dbc = connect();

$images_list = array();

$index_enabled = 'carousel_index_enabled';
$page_enabled = 'carousel_page_enabled';
$category_enabled = 'carousel_category_enabled';
$contact_enabled = 'carousel_contact_enabled';
$search_enabled = 'carousel_search_enabled';

try
{
	$query = 'SELECT * FROM settings WHERE key_name IN (:index_enabled, :page_enabled, :category_enabled, :contact_enabled, :search_enabled)';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':index_enabled', $index_enabled, PDO::PARAM_STR);
	$statement->bindParam(':page_enabled', $page_enabled, PDO::PARAM_STR);
	$statement->bindParam(':category_enabled', $category_enabled, PDO::PARAM_STR);
	$statement->bindParam(':contact_enabled', $contact_enabled, PDO::PARAM_STR);
	$statement->bindParam(':search_enabled', $search_enabled, PDO::PARAM_STR);

	$statement->execute();

	$context_list = $statement->fetchAll(PDO::FETCH_ASSOC);

	$result['context_list'] = $context_list;

	$carousel_images = get_setting_value($dbc, 'carousel_images');

	$objects_list = json_decode($carousel_images);

	foreach ($objects_list as $item)
	{
		foreach ($item as $key => $value) 
		{
			if ($key == 'image') $image = $value;
			if ($key == 'text') $text = $value;
		}
		array_push(
			$images_list, array(
				'image' => 'public/img/gallery/' . $image,
				'text' => $text,
			)
		);
	} 

	$result['interval'] = get_setting_value($dbc, 'carousel_interval');

	$result['slides'] = $images_list;
}
catch (PDOException $e)
{
	$result = array('context_list' => NULL, 'interval' => NULL, 'slides' => NULL);
}

echo json_encode($result);

?>
