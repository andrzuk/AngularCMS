<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'SELECT archives.*, categories.caption AS category_name, users.login AS author_name FROM archives ' .
	'         LEFT JOIN categories ON categories.id = archives.category_id' .
	'         INNER JOIN users ON users.id = archives.author_id' .
	'         WHERE archives.id = :id AND archives.visible = 1';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);

	$result['main_page'] = $result['main_page'] ? true : false;
	$result['contact_page'] = $result['contact_page'] ? true : false;
	$result['visible'] = $result['visible'] ? true : false;
	$result['category_id'] = $result['category_id'];
}

echo json_encode($result);

?>
