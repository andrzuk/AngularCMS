<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$rows = intval($_GET['rows']);
	$page = intval($_GET['page']) - 1;
	$start = $page * $rows;

	$query = 'SELECT pages.*, categories.caption AS category_name, users.login AS author_name FROM pages' .
	'         LEFT JOIN categories ON categories.id = pages.category_id' .
	'         INNER JOIN users ON users.id = pages.author_id' .
	'         ORDER BY pages.id LIMIT '. $start .', '. $rows;

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
