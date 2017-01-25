<?php

include dirname(__FILE__) . '/../db/connection.php';

$dbc = connect();

$result = array();

$title = 'app_title';
$suffix = 'app_suffix';
$description = 'app_description';
$keywords = 'app_keywords';
$author = 'app_author';
$logoicon = 'app_logoicon';
$brand = 'app_brand';
$footer = 'app_footer';

try
{
	$query = 'SELECT * FROM settings WHERE key_name IN (:title, :suffix, :description, :keywords, :author, :logoicon, :brand, :footer)';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':title', $title, PDO::PARAM_STR);
	$statement->bindParam(':suffix', $suffix, PDO::PARAM_STR);
	$statement->bindParam(':description', $description, PDO::PARAM_STR);
	$statement->bindParam(':keywords', $keywords, PDO::PARAM_STR);
	$statement->bindParam(':author', $author, PDO::PARAM_STR);
	$statement->bindParam(':logoicon', $logoicon, PDO::PARAM_STR);
	$statement->bindParam(':brand', $brand, PDO::PARAM_STR);
	$statement->bindParam(':footer', $footer, PDO::PARAM_STR);

	$statement->execute();

	$row_items = $statement->fetchAll(PDO::FETCH_ASSOC);
	
	if (is_array($row_items))
	{
		$result = $row_items;
	}
}
catch (PDOException $e)
{
	$result = array(
		array('key_name' => $title, 'key_value' => 'Angular CMS'),
		array('key_name' => $suffix, 'key_value' => 'Single Page Application'),
		array('key_name' => $logoicon, 'key_value' => 'public/img/angularjs-logo.png'),
	);
}

echo json_encode($result);

?>

