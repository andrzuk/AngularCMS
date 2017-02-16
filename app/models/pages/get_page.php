<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'SELECT pages.*, categories.caption AS category_name, users.login AS author_name FROM pages' .
	'         LEFT JOIN categories ON categories.id = pages.category_id' .
	'         INNER JOIN users ON users.id = pages.author_id' .
	'         WHERE pages.id = :id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);

	$result['main_page'] = $result['main_page'] ? true : false;
	$result['contact_page'] = $result['contact_page'] ? true : false;
	$result['visible'] = $result['visible'] ? true : false;
}

echo json_encode($result);

?>
