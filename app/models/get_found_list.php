<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$search_text = $_GET['search'];
	$search_text = str_replace(' ', '%', $search_text);
	$search_text = '%' . $search_text . '%';
	
	$query = 'SELECT pages.*, categories.caption AS category_name, users.login AS author_name FROM pages' .
	'         INNER JOIN categories ON categories.id = pages.category_id' .
	'         INNER JOIN users ON users.id = pages.author_id' .
	'         WHERE (' .
	'         pages.title LIKE :search_text OR' .
	'         pages.contents LIKE :search_text OR' .
	'         pages.description LIKE :search_text OR' .
	'         categories.caption LIKE :search_text)' .
	'         AND pages.visible = 1' .
	'         ORDER BY pages.title';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':search_text', $search_text, PDO::PARAM_STR);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>

